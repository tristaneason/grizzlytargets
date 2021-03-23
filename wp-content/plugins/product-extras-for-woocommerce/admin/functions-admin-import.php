<?php
/**
 * Functions for importing Product Add-Ons groups from other products
 * @since 3.9.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add import/export links
 * @since 3.9.0
 */
function pewc_add_export_group_link( $groups, $post_id ) {
	if( ! apply_filters( 'pewc_allow_import', false ) ) {
		return;
	}
	wp_nonce_field( 'pewc_export_addons', 'pewc_export_addons' );
	echo '<div class="options_group">';
	printf(
		'<p class="form-field"><label><a href="#" class="pewc-import-groups">%s</a> / <a href="#" class="pewc-export-groups">%s</a></label></p>',
		__( 'Import', 'pewc' ),
		__( 'Export', 'pewc' )
	);
	echo '</div>';

}
add_action( 'pewc_end_tab_options', 'pewc_add_export_group_link', 10, 2 );

/**
 * Add import/export modal form
 * @since 3.9.0
 */
function pewc_add_import_group_form() {

	if( ! apply_filters( 'pewc_allow_import', false ) ) {
		return;
	}

	wp_nonce_field( 'pewc_import_addons', 'pewc_import_addons' ); ?>

	<div id="pewc_import_groups_wrapper">
		<div id="pewc_import_groups">
			<?php $url = admin_url( 'post.php' ); ?>
			<form action="<?php echo $url; ?>"
      class="dropzone"
      id="pewc_import_dropzone"></form>
			<script type="text/javascript">
				Dropzone.autoDiscover = false;
			</script>
		</div>
	</div>

	<?php
}
add_action( 'admin_footer', 'pewc_add_import_group_form' );

/**
 * Export the groups and fields from a product to a JSON file
 * @since 3.9.0
 */
function pewc_export_addons_to_json() {

	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_export_addons' ) ) {
		wp_send_json( array( 'result' => $_POST['pewc_export_addons'] ) );
		exit;
	}

	$post_id = $_POST['post_id'];
	$group_order = pewc_get_group_order( $post_id );
	$groups = pewc_get_extra_fields( $post_id );

	wp_send_json( array(
		'data'	=> json_encode( $groups )
	) );

	exit;

}
add_action( 'wp_ajax_pewc_export_addons_to_json', 'pewc_export_addons_to_json' );

/**
 * Import the groups and fields from a JSON file
 * @since 3.9.0
 */
function pewc_import_addons_from_json() {

	if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'pewc_export_addons' ) ) {
		wp_send_json( array( 'result' => $_POST['pewc_export_addons'] ) );
		exit;
	}

	$post_id = $_POST['post_id'];

	if( isset( $_POST['groups'] ) ) {
		$groups_string = stripslashes( $_POST['groups'] );
		$groups = json_decode( $groups_string, true );

		$current_user_id = get_current_user_id();

		if( $groups ) {

			$map_groups = array();
			$map_fields	= array();
			$map_variations = array();
			$duplicate_fields = array();
			$field_params = pewc_get_field_params();

			// Append duplicated groups to existing groups in an existing product
			$duplicate_group_order = pewc_get_group_order( $post_id );
			$duplicate_group_order = explode( ',', $duplicate_group_order );

			// The $duplicate product is the one we're importing to
			$duplicate = wc_get_product( $post_id );

			// Check for any variation specific fields
			if( $duplicate->is_type( 'variable' ) ) {
				$product_children = $product->get_children();
				$duplicate_children = $duplicate->get_children();
				foreach( $product_children as $index=>$variation_id ) {
					$map_variations[$variation_id] = $duplicate_children[$index];
				}
			}

			foreach( $groups as $group_id=>$group ) {

				$args = array(
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_author'    => $current_user_id,
					'post_content'   => '',
					'post_excerpt'   => '',
					'post_name'      => 'Group Name',
					'post_parent'    => $post_id,
					'post_password'  => '',
					'post_status'    => 'publish',
					'post_title'     => 'Title',
					'post_type'      => 'pewc_group',
					'to_ping'        => ''
				);

				// This is our new group ID
				$duplicate_group_id = wp_insert_post( $args );

				// Map old group IDs to duplicate group IDs
				$map_groups[$group_id] = $duplicate_group_id;
				$duplicate_group_order[] = $duplicate_group_id;

				// Save the group meta
				// NEED TO ADD GROUP META WHEN WE EXPORT
				// $group_title = get_post_meta( $group_id, 'group_title', true );
				// $group_description = get_post_meta( $group_id, 'group_description', true );
				// $group_layout = get_post_meta( $group_id, 'group_layout', true );
				// update_post_meta( $duplicate_group_id, 'group_title', $group_title );
				// update_post_meta( $duplicate_group_id, 'group_description', $group_description );
				// update_post_meta( $duplicate_group_id, 'group_layout', $group_layout );

				// Find each field in each group, duplicate and assign new IDs
				$product_fields = $group['items'];
				$duplicate_fields[$duplicate_group_id] = array();

				if( $product_fields ) {

					foreach( $product_fields as $field_id=>$product_field ) {

						$args = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_author'    => $current_user_id,
							'post_content'   => '',
							'post_excerpt'   => '',
							'post_name'      => sanitize_title_with_dashes( $product_field['field_label'] ),
							'post_parent'    => $duplicate_group_id,
							'post_status'    => 'publish',
							'post_title'     => $product_field['field_label'],
							'post_type'      => 'pewc_field'
						);

						// This is our new field ID
						$duplicate_field_id = wp_insert_post( $args );

						// Map old field IDs to duplicate field IDs
						$map_fields[$field_id] = $duplicate_field_id;

						$duplicate_fields[$duplicate_group_id][$duplicate_field_id] = array();

						// This method catches products saved from 3.3.0
						$product_all_params = get_post_meta( $field_id, 'all_params', true );
						$duplicate_all_params = array();

						if( empty( $product_all_params['field_default'] ) && ! empty( $product_all_params['field_default_hidden'] ) ) {
							$product_all_params['field_default'] = $product_all_params['field_default_hidden'];
						}

						if( $product_all_params ) {
							foreach( $product_all_params as $param=>$param_value ) {
								$duplicate_fields[$duplicate_group_id][$duplicate_field_id][$param] = $param_value;
							}
						}

						$duplicate_fields[$duplicate_group_id][$duplicate_field_id]['field_id'] = $duplicate_field_id;
						$duplicate_fields[$duplicate_group_id][$duplicate_field_id]['id'] = 'pewc_group_' . $duplicate_group_id . '_' . $duplicate_field_id;

					}

				}

				// Now we have to update the group field IDs
				update_post_meta( $duplicate_group_id, 'field_ids', array_keys( $duplicate_fields[$duplicate_group_id] ) );

			}

		}

		// After iterating through all groups and fields, we can replace references to old group and field IDs
		if( $duplicate_fields ) {

			foreach( $duplicate_fields as $group_id=>$fields_by_group ) {

				foreach( $fields_by_group as $duplicate_field_id=>$duplicate_field_params ) {

					if( ! empty( $duplicate_field_params['condition_field'] ) ) {

						foreach( $duplicate_field_params['condition_field'] as $index=>$condition_field ) {

							// Replace old group and field IDs
							$condition_field = explode( '_', $condition_field );
							if( isset( $condition_field[2] ) ) {
								$condition_field[2] = $map_groups[$condition_field[2]];
								$condition_field[3] = $map_fields[$condition_field[3]];
							}

							$duplicate_field_params['condition_field'][$index] = join( '_', $condition_field );

						}

					}

					if( ! empty( $duplicate_field_params['variation_field'] ) ) {

						foreach( $duplicate_field_params['variation_field'] as $index=>$variation_id ) {

							// Replace old variation IDs
							$duplicate_variation_id = $map_variations[$variation_id];
							$duplicate_field_params['variation_field'][$index] = $duplicate_variation_id;

						}

					}

					if( ! empty( $duplicate_field_params['formula'] ) ) {

						// Iterate through each {field_xxx} tag
						foreach( $map_fields as $old_field_id=>$new_field_id ) {
							$duplicate_field_params['formula'] = str_replace( '{field_' . $old_field_id, '{field_' . $new_field_id, $duplicate_field_params['formula'] );
						}

					}

					foreach( $duplicate_field_params as $duplicate_field_param=>$value ) {
						update_post_meta( $duplicate_field_id, $duplicate_field_param, $value );
					}

				}

			}

		}

		// Now we have to update the group field IDs
		update_post_meta( $post_id, 'group_order', join( ',', array_filter( $duplicate_group_order ) ) );

	}

	wp_send_json( array(
		'groups'		=> join( ',', array_filter( $duplicate_group_order ) ),
		'post_id'		=> $post_id
	) );

	exit;

}
add_action( 'wp_ajax_pewc_import_addons_from_json', 'pewc_import_addons_from_json' );
