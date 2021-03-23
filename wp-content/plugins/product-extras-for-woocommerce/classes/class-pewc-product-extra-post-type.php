<?php
/**
 * Class to create our Product Add-Ons post type
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'PEWC_Product_Extra_Post_Type' ) ) {

	class PEWC_Product_Extra_Post_Type {

		public function __construct() {
		}

		public function init() {

			require_once PEWC_DIRNAME . '/admin/metaboxes.php';

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 99 );
			add_action( 'init', array( $this, 'register_post_type' ), 5 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_metabox_data' ) );
			add_action( 'save_post_pewc_group', array( $this, 'save_group_metabox_data' ) );

			add_filter( 'views_edit-pewc_product_extra', array( $this, 'post_type_description' ) );

			add_filter( 'manage_pewc_product_extra_posts_columns', array( $this, 'add_custom_columns' ), 10, 1 );
			add_action( 'manage_posts_custom_column', array( $this, 'manage_custom_columns' ), 10, 2 );

		}

		public function enqueue_scripts( $hook ) {
			global $post;
			$is_post_type = ( isset( $post->post_type ) && ( $post->post_type == 'product' || $post->post_type == 'pewc_product_extra' || $post->post_type == 'pewc_group'  || $post->post_type == 'pewc_field' || $post->post_type == 'shop_order' ) ) ? true : false;
			if( $hook == 'pewc_product_extra_page_global' || $is_post_type || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'pewc' ) ) {
				$has_migrated = pewc_has_migrated();
				if( ! $has_migrated ) {
					$admin_js_file = 'admin-pewc.js';
				} else {
					$admin_js_file = 'admin-fields.js';
				}
				$version = defined( 'PEWC_SCRIPT_DEBUG' ) && PEWC_SCRIPT_DEBUG ? time() : PEWC_PLUGIN_VERSION;
				wp_enqueue_style( 'pewc-admin-style', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/admin-style.css', array(), $version );
				wp_register_script( 'pewc-admin-script', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/' . $admin_js_file, array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'select2', 'jquery-tiptip', 'wc-enhanced-select' ), $version, true );
				// wp_register_script( 'pewc-admin-fields', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/admin-fields.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'select2', 'jquery-tiptip', 'wc-enhanced-select' ), $version, true );
				$params = array(
					'delete_group'				=> __( 'Delete this group?', 'pewc' ),
					'delete_field'				=> __( 'Delete this field? Deleting this field will also delete any conditions associated with it.', 'pewc' ),
					'delete_option' 			=> __( 'Delete this option?', 'pewc' ),
					'checked_label' 			=> __( 'Checked', 'pewc' ),
					'condition_continue' 	=> __( 'This field is used in a condition. Changing its field type may affect the condition. Continue?', 'pewc' ),
					'copy_label'					=> __( 'copy', 'pewc' ),
					'select_text'					=> __( ' -- Select a field -- ', 'pewc' ),
					'load_addons_ajax'		=> pewc_enable_ajax_load_addons()
				);
				if( class_exists( 'WC' ) ) {
					$params['placeholder_src'] = wc_placeholder_img_src();
				}

				wp_localize_script(
					'pewc-admin-script',
					'pewc_obj',
					$params
				);
				// wp_localize_script(
				// 	'pewc-admin-fields',
				// 	'pewc_obj',
				// 	$params
				// );

				wp_enqueue_style( 'pewc-dropzone-basic', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/basic.min.css', array(), $version );
				wp_enqueue_style( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/dropzone.min.css', array(), $version );
				wp_enqueue_script( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/dropzone.js', array( 'jquery' ), $version, false );

				wp_enqueue_script( 'pewc-admin-script' );
				// wp_enqueue_script( 'pewc-admin-fields' );
				wp_enqueue_media();
			}
		}

		public function register_post_type() {
			$label = pewc_get_post_type_labels();
			$labels = array(
				'name'               => $label['plural'],
				'singular_name'      => $label['single'],
				'add_new'            => __( 'Add ', 'pewc' ) . $label['single'],
				'add_new_item'       => __( 'Add New ', 'pewc' ) . $label['single'],
				'edit'               => __( 'Edit', 'pewc' ),
				'edit_item'          => __( 'Edit ', 'pewc' ) . $label['single'],
				'new_item'           => __( 'New ', 'pewc' ) . $label['single'],
				'view'               => __( 'View ', 'pewc' ) . $label['single'],
				'view_item'          => __( 'View ', 'pewc' ). $label['single'],
				'search_items'       => __( 'Search ', 'pewc' ) . $label['plural'],
				'not_found'          => __( 'None found', 'pewc' ),
				'not_found_in_trash' => __( 'None found in trash', 'pewc' ),
				'parent'             => __( 'Parent ', 'pewc' ) . $label['plural'],
				'menu_name'          => $label['plural'],
				'all_items'          => __( 'Add-Ons by Order', 'pewc' )
			);
			$args = array(
				'labels'											=> $labels,
				'label'												=> $label['single'],
				'public'											=> false,
				'show_ui'											=> true,
				'capability_type'							=> 'product',
				'capabilities'								=> array(
					'create_posts'							=> 'do_not_allow'
				),
				'menu_position'								=> apply_filters( 'pewc_menu_position', 56 ),
				'menu_icon'										=> 'dashicons-plus-alt',
				'map_meta_cap'								=> true,
				'publicly_queryable'					=> false,
				'exclude_from_search'					=> true,
				'hierarchical'								=> false,
				'show_in_nav_menus'						=> true,
				'rewrite'											=> false,
				'query_var'										=> false,
				'supports'										=> array( '' ),
				'has_archive'									=> false,
				'description'									=> sprintf(
					'<p class="description">%s</p><p class="description">%s</p>',
					__( 'This page shows a list of Product Add-Ons associated with orders, so don\'t be alarmed if you don\'t see anything here: it just means that you haven\'t had any orders placed yet that contain Product Add On fields.', 'pewc' ),
					__( 'If you are looking to add or edit a Product Add On, you can do so from the Product Data panel when you add or edit a product or from the Global Add-Ons page.', 'pewc' )
				)
			);
			register_post_type( 'pewc_product_extra', $args );

			$group_args = array(
				'label'           => __( 'Groups', 'woocommerce' ),
				'public'          => false,
				'hierarchical'    => false,
				'supports'        => false,
				'capability_type' => 'product',
				'map_meta_cap'		=> true,
				'rewrite'         => false,
			);

			$has_migrated = pewc_has_migrated();
			$enable_groups_as_post_types = pewc_enable_groups_as_post_types();

			if( pewc_is_group_public() == 'yes' && $has_migrated && $enable_groups_as_post_types ) {
				$group_labels = array(
					'name'               => __( 'Groups', 'pewc' ),
					'singular_name'      => __( 'Group', 'pewc' ),
					'add_new'            => __( 'Add Group', 'pewc' ),
					'add_new_item'       => __( 'Add New Group', 'pewc' ),
					'edit'               => __( 'Edit', 'pewc' ),
					'edit_item'          => __( 'Edit Group', 'pewc' ),
					'new_item'           => __( 'New Group', 'pewc' ),
					'view'               => __( 'View Group', 'pewc' ),
					'view_item'          => __( 'View Group', 'pewc' ),
					'search_items'       => __( 'Search Groups', 'pewc' ),
					'not_found'          => __( 'None found', 'pewc' ),
					'not_found_in_trash' => __( 'None found in trash', 'pewc' ),
					'parent'             => __( 'Parent Groups', 'pewc' ),
					'menu_name'          => __( 'Global Groups', 'pewc' ),
					'all_items'          => __( 'Global Groups', 'pewc' )
				);
				$group_args['labels'] = $group_labels;
				$group_args['show_ui'] = true;
				$group_args['publicly_queryable'] = false;
				$group_args['exclude_from_search'] = true;
				$group_args['show_in_menu'] = 'edit.php?post_type=pewc_product_extra';
				$group_args['supports'] = array( 'title', 'page-attributes' );
			}

			// Register a post type for groups
			register_post_type(
				'pewc_group',
				$group_args
			);

			// Register a post type for fields
			$field_args = array(
				'label'           => __( 'Fields', 'pewc' ),
				'public'          => false,
				'hierarchical'    => false,
				'supports'        => false,
				'capability_type' => 'product',
				'rewrite'         => false,
			);

			$enable_fields_as_post_types = pewc_enable_fields_as_post_types();

			if( $has_migrated && $enable_fields_as_post_types ) {
				$field_labels = array(
					'name'               => __( 'Fields', 'pewc' ),
					'singular_name'      => __( 'Field', 'pewc' ),
					'add_new'            => __( 'Add Field', 'pewc' ),
					'add_new_item'       => __( 'Add New Field', 'pewc' ),
					'edit'               => __( 'Edit', 'pewc' ),
					'edit_item'          => __( 'Edit Field', 'pewc' ),
					'new_item'           => __( 'New Field', 'pewc' ),
					'view'               => __( 'View Fields', 'pewc' ),
					'view_item'          => __( 'View Field', 'pewc' ),
					'search_items'       => __( 'Search Fields', 'pewc' ),
					'not_found'          => __( 'None found', 'pewc' ),
					'not_found_in_trash' => __( 'None found in trash', 'pewc' ),
					'parent'             => __( 'Parent Fields', 'pewc' ),
					'menu_name'          => __( 'All Fields', 'pewc' ),
					'all_items'          => __( 'All Fields', 'pewc' )
				);
				$field_args['labels'] = $field_labels;
				$field_args['show_ui'] = true;
				$field_args['publicly_queryable'] = false;
				$field_args['exclude_from_search'] = true;
				$field_args['show_in_menu'] = 'edit.php?post_type=pewc_product_extra';
				$field_args['supports'] = array( 'page-attributes' );
			}


			register_post_type(
				'pewc_field',
				$field_args
			);

		}

		/**
		 * Add some description
		 * @since 1.0.0
		 */
		public function post_type_description( $views ){

	    $screen = get_current_screen();
	    $post_type = get_post_type_object( $screen->post_type );

	    if( $post_type->description ) {
	      echo $post_type->description;
	    }

	    return $views; // return original input unchanged
		}

		/**
		 * Register the metabox
		 * @since 1.0.0
		 */
		public function add_meta_box() {

			$metaboxes = pewc_metaboxes();

			foreach( $metaboxes as $id=>$metabox ) {

				add_meta_box (
					$metabox['ID'],
					$metabox['title'],
					array( $this, $metabox['callback'] ),
					$metabox['screens'],
					$metabox['context'],
					$metabox['priority'],
					$metabox['fields']
				);

			}

		}

		/**
		 * Metabox callback for slide order
		 * @since 1.0.0
		*/
		public function meta_box_callback( $post, $fields ) {

			wp_nonce_field( 'save_metabox_data', 'pewc_metabox_nonce' );

			if( $fields['args'] ) {

				foreach( $fields['args'] as $field ) {

					switch( $field['type'] ) {

						case 'text':
							$this->metabox_text_output( $post, $field );
							break;
						case 'textarea':
							$this->metabox_textarea_output( $post, $field );
							break;
						case 'select':
							$this->metabox_select_output( $post, $field );
							break;
						case 'checkbox':
							$this->metabox_checkbox_output( $post, $field );
							break;
						case 'divider':
							$this->metabox_divider_output( $post, $field );
							break;
						case 'description':
							$this->metabox_description_output( $post, $field );
							break;
						case 'product_extra_fields':
							$this->metabox_product_extra_fields_output( $post, $field );
							break;
						case 'global_rules':
							$this->metabox_global_rules_output( $post, $field );
							break;
						case 'group_fields':
							$this->metabox_group_fields_output( $post, $field );
							break;
						case 'metabox_assign_group_to_product':
							$this->metabox_assign_group_to_product( $post, $field );
							break;
						case 'metabox_get_parent_product':
							$this->metabox_get_parent_product( $post, $field );
							break;
					}

				}

			}

		}

		/**
		 * Metabox callback for text type
		 * @since 1.0.0
		 */
		public function metabox_text_output( $post, $field ) {
			$value = get_post_meta( $post->ID, $field['ID'], true );
			$readonly = '';
			if( isset( $field['readonly'] ) ) {
				$readonly = 'readonly';
			}

			// If the field is pewc_order_id, run the output through the woocommerce_order_number filter
			if( $field['ID'] == 'pewc_order_id' ) {
				$value = apply_filters( 'woocommerce_order_number', $value, wc_get_order( $value ) );
			}

			$value = apply_filters( 'pewc_metabox_text_output_value', $value, $post, $field ); ?>

			<div class="pewc-metafield <?php echo $field['class']; ?>">
				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<input <?php echo $readonly; ?> class="widefat" type="text" id="<?php echo esc_attr( $field['name'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" >
			</div>
			<?php
		}

		/**
		 * Metabox callback for textarea type
		 * @since 1.0.0
		 */
		public function metabox_textarea_output( $post, $field ) {
			$value = get_post_meta( $post->ID, $field['ID'], true ); ?>
			<div class="pewc-metafield <?php echo $field['class']; ?>">
				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<textarea class="widefat" id="<?php echo esc_attr( $field['name'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $value ); ?></textarea>
			</div>
			<?php
		}

		/**
		 * Metabox callback for select
		 */
		public function metabox_select_output( $post, $field ) {
			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			// If there's no saved value and a default value exists, set the value to the default
			// This is to ensure certain settings are set automatically
			if( empty( $field_value ) && ! empty( $field['default'] ) ) {
				$field_value = $field['default'];
			} ?>
			<div class="pewc-metafield <?php echo $field['class']; ?>">
				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<select id="<?php echo esc_attr( $field['name'] ); ?>" name="<?php echo esc_attr( $field['name'] ); ?>">
					<?php if( $field['options'] ) {
						foreach( $field['options'] as $key => $value ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $field_value, $key ); ?>><?php echo esc_attr( $value ); ?></option>
						<?php }
					} ?>
				</select>
			</div>
			<?php
		}

		/**
		 * Metabox callback for product_extra fields
		 */
		public function metabox_product_extra_fields_output( $post, $field ) {
			$groups = get_post_meta( $post->ID, $field['ID'], true );
			if( ! empty( $groups ) ) { ?>
				<div class="pewc-metafield pewc-metafield-pewc <?php echo $field['class']; ?>">
					<?php foreach( $groups as $group ) { ?>
					<table class="widefat product-extra-items">
						<thead>
							<tr>
								<th><?php _e( 'Field', 'pewc' ); ?></th>
								<th><?php _e( 'Value', 'pewc' ); ?></th>
								<th><?php _e( 'Price', 'pewc' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach( $group as $item ) { ?>
								<tr>
									<td>
										<?php if( ! empty( $item['label'] ) ) {
											echo esc_html( $item['label'] );
										} ?>
									</td>
									<td class="product-extra-value">
										<?php if( isset( $item['type'] ) && $item['type'] == 'upload' ) {

											if( empty( $item['files'] ) ) {

												$image = apply_filters( 'pewc_product_extra_image', '<img src="' . esc_url( $item['url'] ) . '">', $item );
												printf(
													'<div class="pewc-image-modal-wrapper"><div class="pewc-image-wrapper"><div class="pewc-image-inner"></div>%s<span class="pewc-image-close"></span></div><a href="#" class="pewc-view-image">%s</a></div>',
													$image,
													$image
												);

											} else {

												foreach( $item['files'] as $index=>$file ) {

													$img = sprintf(
														'<img src="%s">',
														esc_url( $file['url'] )
													);

													printf(
														'<div class="pewc-image-modal-wrapper"><div class="pewc-image-wrapper"><div class="pewc-image-inner"></div>%s<span class="pewc-image-close"></span></div><a href="#" class="pewc-view-image">%s</a></div>',
														$img,
														$img
													);

												}

											}

											// echo apply_filters( 'pewc_product_extra_image', $image, $item );
										} else if( isset( $item['type'] ) && $item['type'] != 'upload' ) {
											echo esc_html( $item['value'] );
										} ?>
									</td>
									<td class="product-extra-price">
										<?php if( ! empty( $item['price'] ) ) {
											echo wc_price( $item['price'] );
										} ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php } ?>
				</div>
			<?php }
		}

		/**
		 * Metabox callback for global rules
		 * @since 1.0.0
		 */
		public function metabox_global_rules_output( $post, $field ) {

			$field_value = get_post_meta( $post->ID, $field['ID'], true );
			$operator = isset( $field_value['operator'] ) ? $field_value['operator'] : 'all'; ?>

			<div class="pewc-rule-instruction-wrapper pewc-metafield <?php echo $field['class']; ?>">

				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>

				<?php	// $operator = pewc_get_group_operator( $group_key, $group ); ?>

				<select id="<?php echo esc_attr( $field['name'] ); ?>[operator]" name="<?php echo esc_attr( $field['name'] ); ?>[operator]">
					<?php if( $field['options'] ) {
						foreach( $field['options'] as $key => $value ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $operator, $key ); ?>><?php echo esc_attr( $value ); ?></option>
						<?php }
					} ?>
				</select>

			</div>

			<div class="pewc-metafield <?php echo $field['class']; ?>">

				<table class="widefat wp-list-table product-extra-global-rule-row">
					<thead>
						<tr>
							<td id="cb"></td>
							<th scope="col" id="name"><?php _e( 'Rule', 'pewc' ); ?></th>
							<th scope="col" id="includes"><?php _e( 'With', 'pewc' ); ?></th>
						</tr>
					</thead>
					<tbody id="the-list">
						<?php
						$checked = ( isset( $field_value["all"]['is_selected'] ) && $field_value["all"]['is_selected'] == 'on' ) ? 'checked="checked"' : ''; ?>
						<tr>
							<td>
								<input class="pewc-rule-field" type="checkbox" <?php echo $checked; ?> name="<?php echo esc_attr( $field['name'] ); ?>[all][is_selected]" id="<?php echo esc_attr( $field['name'] ); ?>[all][is_selected]">
							</td>
							<td>
								<label for="<?php echo esc_attr( $field['name'] ); ?>[all][is_selected]"><?php _e( 'On all products', 'pewc' ); ?></label>
							</td>
							<td></td>
						</tr>

						<?php
						$checked = ( isset( $field_value["ids"]['is_selected'] ) && $field_value["ids"]['is_selected'] == 'on' ) ? 'checked="checked"' : ''; ?>
						<tr>
							<td>
								<input class="pewc-rule-field" type="checkbox" <?php echo $checked; ?> name="<?php echo esc_attr( $field['name'] ); ?>[ids][is_selected]" id="<?php echo esc_attr( $field['name'] ); ?>[ids][is_selected]">
							</td>
							<td>
								<label for="<?php echo esc_attr( $field['name'] ); ?>[ids][is_selected]"><?php _e( 'By product', 'pewc' ); ?></label>
							</td>
							<td>
								<?php $args = array(
									'numberposts' => 9999,
								 	'status'			=> 'publish',
									'return'			=> 'ids'
								);

								$products = wc_get_products( $args );

								$select = '';

								if( $products ) { ?>
									<select multiple="multiple" class="pewc-rule-field pewc-rule-select" name="<?php echo esc_attr( $field['name'] ); ?>[ids][products][]" data-name="" id="<?php echo esc_attr( $field['name'] ); ?>[ids][products]">';
									<?php foreach( $products as $id ) {
										$selected = ( isset( $field_value['ids']['products'] ) && is_array( $field_value['ids']['products'] ) && in_array( $id, $field_value['ids']['products'] ) ) ? 'selected="selected"' : ''; ?>
										<option <?php echo $selected; ?> value="<?php echo $id; ?>"><?php echo get_the_title( $id ); ?></option>
									<?php } ?>
									</select>
								<?php } ?>
							</td>
						</tr>

						<?php
						$checked = ( isset( $field_value["categories"]['is_selected'] ) && $field_value["categories"]['is_selected'] == 'on' ) ? 'checked="checked"' : ''; ?>
						<tr>
							<td>
								<input class="pewc-rule-field" type="checkbox" <?php echo $checked; ?> name="<?php echo esc_attr( $field['name'] ); ?>[categories][is_selected]" id="<?php echo esc_attr( $field['name'] ); ?>[categories][is_selected]">
							</td>
							<td>
								<label for="<?php echo esc_attr( $field['name'] ); ?>[categories][is_selected]"><?php _e( 'By category', 'pewc' ); ?></label>
							</td>
							<td>
								<?php $args = array(
									'taxonomy'	=> 'product_cat',
									'fields'		=> 'ids'
								);

								// $categories = get_terms( $args );
								$taxonomy = apply_filters(
									'pewc_filter_global_categories_taxonomy',
									'product_cat',
									$post->ID,
									false,
									false
								);

								$args = array(
									'taxonomy'	=> $taxonomy,
									'fields'		=> 'ids',
									'hide_empty'	=> false
								);
								$categories = apply_filters( 'pewc_filter_global_categories', get_terms( $args ), $post->ID, false );

								if( $categories ) { ?>
									<select multiple="multiple" class="pewc-rule-field pewc-rule-select" name="<?php echo esc_attr( $field['name'] ); ?>[categories][cats][]" data-name="" id="<?php echo esc_attr( $field['name'] ); ?>[categories][cats]">';
									<?php foreach( $categories as $id ) {
										$selected = ( isset( $field_value['categories']['cats'] ) && is_array( $field_value['categories']['cats'] ) && in_array( $id, $field_value['categories']['cats'] ) ) ? 'selected="selected"' : '';
										$term = get_term_by( 'id', $id, $taxonomy );
										printf(
											'<option %s value="%s">%s</option>',
											$selected,
											$id,
											$term->name
										);
									} ?>
									</select>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>

			</div>
			<?php
		}

		public function metabox_group_fields_output( $post, $field ) {

			$group_id = $post->ID;
			$fields = get_post_meta( $group_id, 'field_ids', true );
			$post_id = 0;

			$class = pewc_is_pro() ? 'pewc-is-pro' : '';
			$has_migrated = pewc_has_migrated();
			if( $has_migrated ) {
				$class .= ' pewc_has_migrated';
			}
			if( pewc_enable_ajax_upload() == 'yes' ) {
				$class .= ' pewc-is-ajax-upload';
			} ?>

			<div id='pewc_options' class='panel woocommerce_options_panel pewc_panel <?php echo esc_attr( $class ); ?>' style="display: block">

				<div class="options_group">
					<div class="options-group-inner">
						<ul class="new-field-list">
							<?php include( PEWC_DIRNAME . '/templates/admin/new-field-item.php' ); ?>
						</ul>
						<table class="new-option">
							<?php include( PEWC_DIRNAME . '/templates/admin/views/option-new.php' ); ?>
						</table>
						<div class="new-information-row">
							<?php include( PEWC_DIRNAME . '/templates/admin/views/information-row-new.php' ); ?>
						</div>

						<div class="product-extra-group-data" id="product_extra_groups">

							<!-- Start of the new-group-row element -->
							<?php include( PEWC_DIRNAME . '/templates/admin/new-group.php' ); ?>

							<?php include( PEWC_DIRNAME . '/templates/admin/new-conditional-row.php' ); ?>

							<?php $group_order = pewc_get_group_order( $group_id ); ?>
							<?php $groups = pewc_get_extra_fields( $group_id ); ?>
							<?php $group = pewc_get_group_fields( $group_id ); ?>

							<input type="hidden" id="pewc_group_order" value="<?php echo $group_order; ?>" name="pewc_group_order">

							<div data-group-count="0" data-group-id="<?php echo esc_attr( $group_id ); ?>" id="group-<?php echo esc_attr( $group_id ); ?>" class="group-row">

								<div id="pewc_group_wrapper">
									<?php echo '<ul class="field-list">';
									if( $fields ) {
										foreach( $fields as $item_key ) {
											$item = pewc_create_item_object( $item_key );
											include( PEWC_DIRNAME . '/templates/admin/field-item.php' );
										}
									}
									echo '</ul>'; ?>

									<p><a href="#" class="button add_new_field"><?php _e( 'Add Field', 'pewc' ); ?></a></p>

								</div>

							</div>

						</div><!-- #product_extra_groups -->

					</div>

				</div>

				<?php wp_nonce_field( 'add_new_pewc_group_nonce', 'add_new_pewc_group_nonce' ); ?>

				<div class="pewc-loading"><span class="spinner"></span></div>

			</div>

			<?php

		}

		/**
		 * Find which groups a field belongs to
		 * @since 1.0.0
		 */
		public function metabox_get_parent_product( $post, $field ) {

			// Query products to find which products have this $post->ID in their group_order meta
			$products = pewc_get_parent_product( $post->ID ); ?>

			<div class="pewc-metafield <?php echo $field['class']; ?>">

				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php if( $products ) {

					echo '<ul>';

					foreach( $products as $product_id ) {

						$url = add_query_arg(
							array(
								'post'		=> $product_id,
								'action'	=> 'edit'
							),
							admin_url( 'post.php' )
						);

						printf(
							'<li><a href="%s">%s</a> [ID: %s]</li>',
							$url,
							get_the_title( $product_id ),
							$product_id
						);

					}

					echo '</ul>';

					// We'll use this product ID if we duplicate this group
					printf(
						'<input type="hidden" name="pewc_parent_product_id" id="pewc_parent_product_id" value="%s">',
						$product_id
					);

				} ?>

			</div>

			<?php
		}

		/**
		 * Duplicate group and assign to new products
		 * @since 1.0.0
		 */
		public function metabox_assign_group_to_product( $post, $field ) {

			// Query products to find which products have this $post->ID in their group_order meta
			$products = pewc_get_parent_product( $post->ID ); ?>

			<div class="pewc-metafield <?php echo $field['class']; ?>">

				<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<select class="wc-product-search" data-options="" multiple="multiple" style="width: 100%;" name="<?php echo esc_attr( $field['name'] ); ?>[]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose products', 'pewc' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-include="" data-exclude="">
				</select>

				<?php printf(
					'<p><a href="#" class="pewc-duplicate-global">%s</a></p>',
					__( 'Duplicate Global Group', 'pewc' )
				); ?>

			</div>

			<?php
		}

		/**
		 * Save the data
		 * @since 1.0.0
		 */
		public function save_metabox_data( $post_id ) {

			// Check the nonce is set
			if( ! isset( $_POST['pewc_metabox_nonce'] ) ) {
				return;
			}

			// Verify the nonce
			if( ! wp_verify_nonce( $_POST['pewc_metabox_nonce'], 'save_metabox_data' ) ) {
				return;
			}

			if( get_post_type( $post_id ) == 'pewc_group' ) {
				// Do groups separately
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check the user's permissions.
			if( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Save all our metaboxes
			$metaboxes = pewc_metaboxes();
			foreach( $metaboxes as $metabox ) {
				if( $metabox['ID'] != 'pewc_metabox' ) { // Don't save the product extra groups data
					if( $metabox['fields'] ) {
						foreach( $metabox['fields'] as $field ) {
							if( isset( $_POST[$field['name']] ) ) {
								$data = sanitize_text_field( $_POST[$field['name']] );
								update_post_meta( $post_id, $field['ID'], $data );
							} else {
								delete_post_meta( $post_id, $field['ID'] );
							}
						}
					}
				}
			}
		}


		/**
		 * Save group data
		 * @todo sync this up with pewc_save_product_extra_options in functions-custom-panel.php
		 * @since 3.2.3
		 */
		public function save_group_metabox_data( $group_id ) {

			// Check the nonce is set
			if( ! isset( $_POST['pewc_metabox_nonce'] ) ) {
				return;
			}

			// Verify the nonce
			if( ! wp_verify_nonce( $_POST['pewc_metabox_nonce'], 'save_metabox_data' ) ) {
				return;
			}

			if( get_post_type( $group_id ) != 'pewc_group' ) {
				// Do groups separately
				return;
			}

			// Save our metaboxes
			if( isset( $_POST['group_title'] ) ) {
				$data = sanitize_text_field( $_POST['group_title'] );
				update_post_meta( $group_id, 'group_title', $data );
			} else {
				delete_post_meta( $group_id, 'group_title' );
			}

			if( isset( $_POST['group_description'] ) ) {
				$data = wp_kses_post( $_POST['group_description'] );
				update_post_meta( $group_id, 'group_description', $data );
			} else {
				delete_post_meta( $group_id, 'group_description' );
			}

			if( isset( $_POST['group_layout'] ) ) {
				$data = wp_kses_post( $_POST['group_layout'] );
				update_post_meta( $group_id, 'group_layout', $data );
			} else {
				delete_post_meta( $group_id, 'group_layout' );
			}

			if( isset( $_POST['global_rules'] ) ) {
				$data = ( $_POST['global_rules'] );
				update_post_meta( $group_id, 'global_rules', $data );
			} else {
				delete_post_meta( $group_id, 'global_rules' );
			}

			// Save the fields
			// $items = isset( $_POST['_product_extra_groups'][$group_id]['items'] ) ? $_POST['_product_extra_groups'][$group_id]['items'] : array();

			// Implemented this method in 3.3.0
			$params = pewc_get_field_params();
			$field_ids = array(); // Save the child fields to the group post

			foreach( $_POST as $key=>$value ) {

				if( strpos( $key, '_product_extra_groups_' ) !== false ) {

					// Get the group ID
					$ids = str_replace( '_product_extra_groups_', '', $key );
					$ids = explode( '_', $ids );
					$group_id = isset( $ids[0] ) ? $ids[0] : false;
					$field_id = isset( $ids[1] ) ? $ids[1] : false;

					if( ! is_numeric( $field_id ) ) {
						continue;
					}

					// Delete the conditional rules transient
					delete_transient( 'pewc_rules_transient_pewc_group_' . $group_id . '_' . $field_id );

					// Save each parameter as post meta
					foreach( $params as $param ) {

						if( isset( $value[$param] ) ) {
							// Ensure the options array doesn't get out of sync
							if( in_array( $param, array( 'field_options', 'condition_field', 'condition_rule', 'condition_value' ) ) ) {
								$value[$param] = array_values( $value[$param] );
							}

							// Need to sanitise this
							update_post_meta( $field_id, $param, $value[$param] );

						} else {

							delete_post_meta( $field_id, $param );

						}

					}

					delete_transient( 'pewc_item_object_' . $field_id );

					$field_ids[] = $field_id;

				}

			}

			// Check if we are duplicating this group and assigning it to another product
			if( isset( $_POST['pewc_assign_group'] ) ) {
				$duplicate_to = $_POST['pewc_assign_group'];
				$parent_id = $_POST['pewc_parent_product_id'];
				pewc_duplicate_and_assign( $group_id, $parent_id, $duplicate_to );
			}

			update_post_meta( $group_id, 'field_ids', $field_ids );

			// Reset transients to ensure product groups and fields are correct
			pewc_reset_all_transients();

			// Set the global group order
			do_action( 'pewc_after_save_group_metabox_data', $group_id );

		}

		/**
		 * Add custom columns
		 */
		function add_custom_columns( $columns ) {
			unset( $columns['date'] );
			$columns['order'] = __( 'Order', 'pewc' );
			$columns['product'] = __( 'Product', 'pewc' );
			$columns['user'] = __( 'User', 'pewc' );
			$columns['date'] = __( 'Date', 'pewc' );
			return $columns;
		}

		/**
		 * Populate custom columns with values
		 */
		function manage_custom_columns( $column, $post_id ) {
			if ( get_post_type( $post_id ) == 'pewc_product_extra' ) {
				if( $column == 'order' ) {
					$order_id = get_post_meta( $post_id, 'pewc_order_id', true );
					echo apply_filters( 'woocommerce_order_number', $order_id, wc_get_order( $order_id ) );
				} else if( $column == 'product' ) {
					$product_id = get_post_meta( $post_id, 'pewc_product_id', true );
					echo get_the_title( $product_id );
				} else if( $column == 'user' ) {
					$user_id = get_post_meta( $post_id, 'pewc_user_id', true );
					$user = get_userdata( $user_id );
					if( isset( $user->user_login ) ) {
						echo $user->user_login;
					}
				}
			}
		}

	}

	$PEWC_Product_Extra_Post_Type = new PEWC_Product_Extra_Post_Type;
	$PEWC_Product_Extra_Post_Type->init();

}
