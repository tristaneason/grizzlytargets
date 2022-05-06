<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post Type Builder
 *
 * @since 6.3.0
 */

if ( ! class_exists( 'PortoBuildersType' ) ) :
	class PortoBuildersType {

		private $elements = array(
			'featured_image',
			'content',
		);

		/**
		 * Meta fields
		 *
		 * @since 6.3.0
		 */
		private $meta_fields;

		/**
		 * Constructor
		 *
		 * @since 6.3.0
		 */
		public function __construct() {
			if ( is_admin() && ( 'post.php' == $GLOBALS['pagenow'] || 'post-new.php' == $GLOBALS['pagenow'] ) ) {
				add_action( 'current_screen', array( $this, 'init' ) );
			}

			add_action( 'wp_enqueue_script', array( $this, 'enqueue' ) );

			add_filter( 'porto_builder_get_current_object', array( $this, 'get_dynamic_content_data' ), 10, 2 );

			$this->add_elements();
		}

		/**
		 * Init functions
		 *
		 * @since 6.3.0
		 */
		public function init() {
			$screen = get_current_screen();
			if ( $screen && 'post' == $screen->base && PortoBuilders::BUILDER_SLUG == $screen->id ) {
				$this->post_id = is_singular() ? get_the_ID() : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : ( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : false ) );
				if ( ! $this->post_id ) {
					return;
				}
				$builder_type = get_post_meta( $this->post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
				if ( ! $builder_type || 'type' != $builder_type ) {
					return;
				}

				if ( $screen->is_block_editor() ) {

					add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

					// add elements
					add_action(
						'enqueue_block_editor_assets',
						function () {
							wp_enqueue_style( 'porto-tb-css', PORTO_FUNC_URL . 'builders/assets/type-builder.css', array(), PORTO_SHORTCODES_VERSION );
							wp_enqueue_script( 'porto-tb-blocks', PORTO_FUNC_URL . 'builders/elements/type/elements/blocks.min.js', array( 'porto_blocks' ), PORTO_SHORTCODES_VERSION, true );
						},
						999
					);
					add_filter(
						'block_categories_all',
						function ( $categories ) {
							return array_merge(
								$categories,
								array(
									array(
										'slug'  => 'porto-tb',
										'title' => __( 'Porto Type Builder Blocks', 'porto-functionality' ),
										'icon'  => '',
									),
								)
							);
						},
						11,
						1
					);

					add_filter( 'porto_gutenberg_editor_vars', array( $this, 'add_dynamic_field_vars' ) );
				} else {
					add_action( 'save_post', array( $this, 'save_meta_values' ), 99, 2 );
				}
			}
		}

		/**
		 * Add meta box to set post type, dynamic content as, preview width
		 *
		 * @since 6.3.0
		 */
		public function add_meta_box() {
			add_meta_box(
				PortoBuilders::BUILDER_SLUG . '-type-meta-box',
				__( 'Post Type Builder Options', 'porto-functionality' ),
				array( $this, 'meta_box_content' ),
				PortoBuilders::BUILDER_SLUG,
				'normal',
				'high'
			);
		}

		/**
		 * Output the meta box content
		 *
		 * @since 6.3.0
		 */
		public function meta_box_content() {
			porto_show_meta_box( $this->get_meta_box_fields() );
		}

		/**
		 * Save meta fields
		 *
		 * @since 6.3.0
		 */
		public function save_meta_values( $post_id, $post ) {
			if ( ! $post || ! isset( $post->post_type ) || PortoBuilders::BUILDER_SLUG != $post->post_type || ! $post->post_content || 'type' != get_post_meta( $post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			porto_save_meta_value( $post_id, $this->get_meta_box_fields() );

			// save dynamic styles
			if ( false !== strpos( $post->post_content, '<!-- wp:porto-tb' ) ) { // Gutenberg editor

				$blocks = parse_blocks( $post->post_content );
				if ( ! empty( $blocks ) ) {
					ob_start();
					$css = '';
					$this->include_style( $blocks );
					$css = ob_get_clean();
					if ( $css ) {
						update_post_meta( $post_id, 'porto_builder_css', wp_strip_all_tags( $css ) );
					} else {
						delete_post_meta( $post_id, 'porto_builder_css' );
					}
				}
			}
		}

		/**
		 * Generate meta box fields
		 *
		 * @since 6.3.0
		 */
		private function get_meta_box_fields() {
			if ( $this->meta_fields ) {
				return $this->meta_fields;
			}
			$choices = array(
				''     => __( 'Default', 'porto-functionality' ),
				'term' => __( 'Term', 'porto-functionality' ),
			);

			$post_types          = get_post_types(
				array(
					'public'            => true,
					'show_in_nav_menus' => true,
				),
				'objects',
				'and'
			);
			$post_taxonomies     = array();
			$sub_fields_types    = array();
			$disabled_post_types = array( 'attachment', 'porto_builder', 'page' );

			foreach ( $disabled_post_types as $disabled ) {
				unset( $post_types[ $disabled ] );
			}
			foreach ( $post_types as $post_type ) {
				$taxonomies = get_object_taxonomies( $post_type->name, 'objects' );
				foreach ( $taxonomies as $new_taxonomy ) {
					$post_taxonomies[ $new_taxonomy->name ] = ucwords( esc_html( $new_taxonomy->label ) );
				}

				$sub_fields_types[ 'content_type_' . $post_type->name ] = array(
					'name'     => 'content_type_' . $post_type->name,
					/* translators: The post name. */
					'title'    => sprintf( __( 'Select %s', 'porto-functionality' ), $post_type->labels->singular_name ),
					/* translators: The post name. */
					'desc'     => sprintf( __( 'Choose to view dynamic content as %s. Leave Empty for random selection.', 'porto-functionality' ), $post_type->labels->singular_name ),
					'type'     => 'ajaxselect2',
					'option'   => $post_type->name,
					'required' => array(
						'name'  => 'content_type',
						'value' => $post_type->name,
					),
				);

				$choices[ $post_type->name ] = $post_type->labels->singular_name;

				if ( ! empty( $post_type->has_archive ) ) {
					$archive_choices[ $post_type->name ] = $post_type->labels->singular_name;
				}
			}

			unset( $post_taxonomies['post_format'] );
			unset( $post_taxonomies['product_visibility'] );

			$sub_fields_types['content_type_term'] = array(
				'name'     => 'content_type_term',
				'title'    => __( 'Select Taxonomy', 'porto-functionality' ),
				'desc'     => __( 'Select a taxonomy to pull a term from. The most recent term in the taxonomy will be used.', 'porto-functionality' ),
				'type'     => 'select',
				'default'  => '',
				'options'  => $post_taxonomies,
				'required' => array(
					'name'  => 'content_type',
					'value' => 'term',
				),
			);

			$this->meta_fields = array_merge(
				array(
					'content_type' => array(
						'name'    => 'content_type',
						'title'   => __( 'Content Type', 'porto-functionality' ),
						'type'    => 'select',
						'default' => '',
						'options' => $choices,
					),
				),
				$sub_fields_types
			);

			$this->meta_fields['preview_width'] = array(
				'name'    => 'preview_width',
				'title'   => __( 'Preview Width (%)', 'porto-functionality' ),
				'desc'    => __( 'Note: this is only used for previewing purposes.', 'porto-functionality' ),
				'type'    => 'text',
				'default' => '',
			);

			return $this->meta_fields;
		}

		/**
		 * Enqueue styles
		 *
		 * @since 6.3.0
		 */
		public function enqueue() {
			wp_enqueue_style( 'porto-type-builder-css', PORTO_FUNC_URL . 'builders/assets/css/type-builder.css', array(), PORTO_SHORTCODES_VERSION );
		}

		/**
		 * Add dynamic field vars
		 *
		 * @since 6.3.0
		 */
		public function add_dynamic_field_vars( $block_vars ) {
			$meta_fields = array(
				'global'      => array(
					'page_sub_title' => array( esc_html__( 'Page Sub Title', 'porto-functionality' ), 'text' ),
				),
				'post'        => array(),
				'event'       => array(),
				'portfolio'   => array(),
				'member'      => array(),
				'product'     => array(),
				'product_cat' => array(),
			);

			foreach ( $meta_fields as $post_type => $val ) {
				if ( 'global' == $post_type ) {
					continue;
				}
				if ( 'product_cat' == $post_type ) {
					global $porto_settings;
					if ( isset( $porto_settings['show-category-skin'] ) ) {
						$backup = $porto_settings['show-category-skin'];
						$porto_settings['show-category-skin'] = false;
					}
				}
				$fn_name     = 'porto_' . $post_type . '_meta_fields';
				$post_fields = $fn_name();
				if ( 'product_cat' == $post_type && isset( $backup ) ) {
					global $porto_settings;
					$porto_settings['show-category-skin'] = $backup;
				}
				foreach ( $post_fields as $key => $arr ) {
					$meta_fields[ $post_type ][ $key ] = array( esc_js( $arr['title'] ), $arr['type'] );
				}
			}

			$block_vars['meta_fields'] = $meta_fields;
			return $block_vars;
		}

		/**
		 * Load post type builder blocks
		 *
		 * @since 6.3.0
		 */
		private function add_elements() {

			register_block_type(
				'porto-tb/porto-featured-image',
				array(
					'attributes'      => array(
						'image_type'         => array(
							'type' => 'string',
						),
						'hover_effect'       => array(
							'type' => 'string',
						),
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'add_link'           => array(
							'type' => 'string',
						),
						'custom_url'         => array(
							'type' => 'string',
						),
						'link_target'        => array(
							'type' => 'string',
						),
						'image_size'         => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function( $atts ) {
						return $this->render_block( $atts, 'featured_image' );
					},
				)
			);

			register_block_type(
				'porto-tb/porto-content',
				array(
					'attributes'      => array(
						'content_display'    => array(
							'type'    => 'string',
							'default' => 'excerpt',
						),
						'excerpt_length'     => array(
							'type'    => 'integer',
							'default' => 50,
						),
						'content_type'       => array(
							'type' => 'string',
						),
						'content_type_value' => array(
							'type' => 'string',
						),
						'alignment'          => array(
							'type' => 'string',
						),
						'content_ff'         => array(
							'type' => 'string',
						),
						'content_fs'         => array(
							'type' => 'string',
						),
						'content_fw'         => array(
							'type' => 'integer',
						),
						'content_tt'         => array(
							'type' => 'string',
						),
						'content_lh'         => array(
							'type' => 'string',
						),
						'content_ls'         => array(
							'type' => 'string',
						),
						'content_color'      => array(
							'type' => 'string',
						),
						'className'          => array(
							'type' => 'string',
						),
					),
					'editor_script'   => 'porto-tb-blocks',
					'render_callback' => function( $atts ) {
						return $this->render_block( $atts, 'content' );
					},
				)
			);
		}

		/**
		 * Render block
		 *
		 * @since 6.3.0
		 */
		protected function render_block( $atts, $block_name, $content = null ) {
			ob_start();
			$should_save_global = false;
			if ( wp_is_json_request() ) { // in block editor
				$post = $this->get_dynamic_content_data( false, $atts );
				if ( ! $post ) {
					return;
				}
				$should_save_global      = isset( $atts['content_type'] ) ? $atts['content_type'] : 'post';
				$original_query          = $GLOBALS['wp_query'];
				$original_queried_object = $GLOBALS['wp_query']->queried_object;
				if ( 'term' == $should_save_global ) {
					$original_is_tax     = $GLOBALS['wp_query']->is_tax;
					$original_is_archive = $GLOBALS['wp_query']->is_archive;

					$GLOBALS['wp_query']->queried_object = $post;
					$GLOBALS['wp_query']->is_tax         = true;
					$GLOBALS['wp_query']->is_archive     = true;
				} else {
					$original_post = $GLOBALS['post'];

					$GLOBALS['post'] = $post;
					setup_postdata( $GLOBALS['post'] );
					$GLOBALS['wp_query']->queried_object = $GLOBALS['post'];

					if ( 'product' == $should_save_global ) {
						$GLOBALS['product'] = wc_get_product( $post->ID );
					}
				}
			}
			include PORTO_BUILDERS_PATH . 'elements/type/views/' . $block_name . '.php';

			if ( 'term' == $should_save_global ) {
				$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->is_tax         = $original_is_tax; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->is_archive     = $original_is_archive; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			} elseif ( $should_save_global ) {
				// Restore global data.
				$GLOBALS['post']                     = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

				if ( 'product' == $should_save_global ) {
					unset( $GLOBALS['product'] );
				}
			}

			return ob_get_clean();
		}

		/**
		 * Returns the dynamic content data
		 *
		 * @since 6.3.0
		 */
		public function get_dynamic_content_data( $builder_id = false, $atts = array() ) {
			$content_type       = false;
			$content_type_value = false;

			if ( isset( $atts['content_type'] ) ) {
				$content_type = $atts['content_type'];
			}
			if ( isset( $atts['content_type_value'] ) ) {
				$content_type_value = $atts['content_type_value'];
			}

			if ( $builder_id ) {
				if ( ! $content_type ) {
					$content_type = get_post_meta( $builder_id, 'content_type', true );
				}
				if ( ! $content_type_value ) {
					if ( $content_type ) {
						$content_type_value = get_post_meta( $builder_id, 'content_type_' . $content_type, true );
					}
				}
			}
			$result = false;

			if ( 'term' == $content_type ) {
				$args = array(
					'hide_empty' => true,
					'number'     => 1,
				);
				if ( $content_type_value ) {
					$args['taxonomy'] = $content_type_value;
				}
				$terms = get_terms( $args );

				if ( is_array( $terms ) && ! empty( $terms ) ) {
					$terms = array_values( $terms );
					return $terms[0];
				}
			} elseif ( $content_type && $content_type_value ) {
				$result = get_post( $content_type_value );
			} else {
				$args = array( 'numberposts' => 1 );
				if ( $content_type ) {
					$args['post_type'] = $content_type;
				}

				$result = get_posts( $args );

				if ( is_array( $result ) && isset( $result[0] ) ) {
					return $result[0];
				}
			}

			return $result;
		}

		/**
		 * Generate internal styles
		 *
		 * @since 6.3.0
		 */
		protected function include_style( $blocks ) {
			if ( empty( $blocks ) ) {
				return;
			}

			foreach ( $blocks as $block ) {
				if ( ! empty( $block['blockName'] ) && in_array( $block['blockName'], array( 'porto-tb/porto-content', 'porto/porto-heading' ) ) ) {
					$atts = empty( $block['attrs'] ) ? array() : $block['attrs'];

					// porto typography styles
					if ( ! empty( $atts ) && ! empty( $atts['font_settings'] ) ) {
						$settings             = $atts['font_settings'];
						$settings['selector'] = '.tb-' . str_replace( array( 'porto-tb/porto-', 'porto/porto-' ), '', $block['blockName'] );
						if ( 'porto/porto-heading' == $block['blockName'] ) {
							$settings['selector'] = '.' . esc_attr( $atts['tb_cls'] );
						}
						if ( ! empty( $atts['alignment'] ) ) {
							$settings['textAlign'] = $atts['alignment'];
						}
						include PORTO_BUILDERS_PATH . '/elements/type/style-font.php';
					}
				}
				if ( ! empty( $block['innerBlocks'] ) ) {
					$this->include_style( $block['innerBlocks'] );
				}
			}
		}
	}
endif;

new PortoBuildersType;
