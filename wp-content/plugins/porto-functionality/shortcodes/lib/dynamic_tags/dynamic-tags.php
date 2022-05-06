<?php
/**
 * Porto Dynamic Tags Content class
 *
 * @author     P-Themes
 * @since      6.3.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Porto_Func_Dynamic_Tags_Content' ) ) :

	class Porto_Func_Dynamic_Tags_Content {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'current_screen', array( $this, 'init' ) );

			add_filter( 'porto_dynamic_tags_content', array( $this, 'get_dynamic_content' ), 10, 4 );

			add_action( 'wp_ajax_porto_dynamic_tags_get_value', array( $this, 'get_value' ) );
			add_action( 'wp_ajax_porto_dynamic_tags_acf_fields', array( $this, 'get_acf_fields' ) );
		}

		/**
		 * Init functions
		 *
		 * @since 6.3.0
		 */
		public function init() {
			if ( class_exists( 'ACF' ) ) {
				$screen = get_current_screen();
				if ( $screen && 'post' == $screen->base && PortoBuilders::BUILDER_SLUG == $screen->id ) {
					$this->post_id = is_singular() ? get_the_ID() : ( isset( $_GET['post'] ) ? (int) $_GET['post'] : ( isset( $_GET['post_id'] ) ? (int) $_GET['post_id'] : false ) );
					if ( ! $this->post_id ) {
						return;
					}
					$builder_type = get_post_meta( $this->post_id, PortoBuilders::BUILDER_TAXONOMY_SLUG, true );
					if ( 'type' == $builder_type ) { // in mini type builder
						// add ACF fields
						include_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
					}
				}
			}

		}

		/**
		 * Retrive dynamic tags content according to its type
		 *
		 * @since 6.3.0
		 */
		public function get_dynamic_content( $default = false, $object = null, $type = 'post', $field = '' ) {
			if ( ! $object ) {
				if ( 'post' == $type || 'acf' == $type ) {
					global $post;
					$object = $post;
				} else {
					if ( ( $current_object = get_queried_object() ) && $current_object->term_id ) {
						$object = $current_object;
					} else {
						global $post;
						$object = $post;
					}
				}
			}
			if ( ! $object ) {
				return $default;
			}
			if ( 'post' == $type ) {
				if ( 'content' == $field ) {
					return apply_filters( 'the_content', $object->post_content );
				} elseif ( 'like_count' == $field ) {
					return esc_html( get_post_meta( $object->ID, 'like_count', true ) );
				} elseif ( $field && isset( $object->{ 'post_' . $field } ) ) {
					return $object->{ 'post_' . $field };
				} elseif ( 'thumbnail' == $field ) {
					return esc_url( get_the_post_thumbnail_url( $object, 'full' ) );
				} elseif ( 'permalink' == $field ) {
					return esc_url( get_permalink( $object ) );
				} else {
					return (int) $object->ID;
				}
			} elseif ( 'metabox' == $type ) {
				if ( ! $field ) {
					$field = 'page_sub_title';
				}
				if ( $object->ID ) {
					return get_post_meta( $object->ID, $field, true );
				} else {
					$result = get_term_meta( $object->term_id, $field, true );
					if ( $result ) {
						return $result;
					}
					return get_metadata( $object->taxonomy, $object->term_id, $field, true );
				}
			} elseif ( 'acf' == $type && $field ) {
				$field_arr = explode( '-', $field );
				if ( 2 === count( $field_arr ) ) {
					return get_post_meta( $object->ID, $field_arr[1], true );
				}
			} elseif ( 'meta' == $type ) {
				if ( $object->ID ) {
					return get_post_meta( $object->ID, $field, true );
				} else {
					$result = get_term_meta( $object->term_id, $field, true );
					if ( $result ) {
						return $result;
					}
					return get_metadata( $object->taxonomy, $object->term_id, $field, true );
				}
			} elseif ( 'tax' == $type ) {
				if ( $object->term_id ) {
					if ( 'id' == $field ) {
						return (int) $object->term_id;
					} elseif ( 'title' == $field ) {
						return esc_html( $object->name );
					} elseif ( 'desc' == $field ) {
						return $object->description;
					} elseif ( 'count' == $field ) {
						return (int) $object->count;
					} elseif ( 'term_link' == $field ) {
						return esc_url( get_term_link( $object ) );
					}
				}
			}

			return $default;
		}

		/**
		 * Retrieve dynamic tags content from editor
		 *
		 * @since 6.3.0
		 */
		public function get_value() {
			check_ajax_referer( 'porto-nonce', 'nonce' );
			if ( isset( $_POST['content_type'] ) && isset( $_POST['content_type_value'] ) && ! empty( $_POST['source'] ) && ! empty( $_POST['field_name'] ) ) {
				$atts = array(
					'content_type'       => $_POST['content_type'],
					'content_type_value' => $_POST['content_type_value'],
				);
				$object = apply_filters( 'porto_builder_get_current_object', false, $atts );
				if ( $object ) {
					if ( 'term' == $atts['content_type'] && ( 'post' == $_POST['source'] || 'acf' == $_POST['source'] ) ) {

					} elseif ( 'term' != $atts['content_type'] && $atts['content_type'] && 'tax' == $_POST['source'] ) {

					} else {
						$result = $this->get_dynamic_content( false, $object, $_POST['source'], $_POST['field_name'] );
						if ( false === $result ) {
							wp_send_json_error();
						}
						wp_send_json_success( $result );
					}
				}
			}
			wp_send_json_error();
		}

		/**
		 * Retrive acf fields from selected content type
		 *
		 * @since 6.3.0
		 */
		public function get_acf_fields() {
			check_ajax_referer( 'porto-nonce', 'nonce' );
			if ( class_exists( 'ACF' ) && isset( $_POST['content_type'] ) && isset( $_POST['content_type_value'] ) && 'term' != $_POST['content_type'] ) {
				$atts = array(
					'content_type'       => $_POST['content_type'],
					'content_type_value' => $_POST['content_type_value'],
				);
				$object = apply_filters( 'porto_builder_get_current_object', false, $atts );
				if ( $object ) {
					include_once PORTO_SHORTCODES_LIB . 'dynamic_tags/class-porto-func-acf.php';
					global $post;
					$post = $object;
					$fields = apply_filters( 'porto_gutenberg_editor_vars', array() );
					if ( isset( $fields['acf'] ) ) {
						$fields = $fields['acf'];
					}
					wp_send_json_success( $fields );
				}
			}
			wp_send_json_error();
		}
	}
endif;

new Porto_Func_Dynamic_Tags_Content;
