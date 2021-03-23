<?php
/**
 * Functions for exporting Product Add-Ons
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( isset( $_GET['do_export'] ) ) {
	// Do the export
	add_action( 'admin_init', 'pewc_generate_csv' );
}

function pewc_register_export_page() {
	add_submenu_page(
		'edit.php?post_type=pewc_product_extra',
		__( 'Export', 'pewc' ),
		__( 'Export', 'pewc' ),
		'manage_options',
		'export-product-extras',
		'pewc_export_page_callback'
	);
}
add_action( 'admin_menu', 'pewc_register_export_page', 99 );

function pewc_export_page_callback() { ?>
	<div class="wrap">
		<?php printf( '<h1>%s</h2>', __( 'Export Product Add-Ons', 'pewc' ) ); ?>
		<p><?php _e( 'Click the Export button to export all your Product Add-Ons to a csv file which you can then open with Excel or other spreadsheet software.', 'pewc' ); ?></p>
		<p class="submit">
			<a href="<?php echo admin_url( 'admin.php?page=export-product-extras&do_export=true' ); ?>" id="export_product-extras" class="button button-primary"><?php _e( 'Export', 'pewc' ); ?></a>
		</p>

		<?php do_action( 'pewc_export_page_end' ); ?>

	</div>
<?php }

function pewc_generate_csv() {

	// Capability check
	if( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$filename = 'product-extras-export-' . time() . '.csv';
	$header_row = array(
		'0' => __( 'Product Add On ID', 'pewc' ),
		'1' => __( 'Product Add On Date', 'pewc' ),
		'2' => __( 'User ID', 'pewc' ),
		'3' => __( 'User Name', 'pewc' ),
		'4' => __( 'Email', 'pewc' ),
		'5' => __( 'Phone Number', 'pewc' ),
		'6' => __( 'Address', 'pewc' ),
		'7' => __( 'Order ID', 'pewc' ),
		'8' => __( 'Product', 'pewc' ),
		'9' => __( 'Product Add On Content', 'pewc' ),
		'10'	=> __( 'Notes', 'pewc' ),
		'11'	=> __( 'Status', 'pewc' )
	);
	$col_index = 12;
	// Get the highest value of $col_index in all rows
	$max_cols = 12;
	$data_rows = array();

	$args = array(
		'post_type'				=> 'pewc_product_extra',
		'posts_per_page'	=> -1,
		'post_status'			=> 'publish',
		'fields'					=> 'ids'
	);
	$subs = new WP_Query( $args );

	if( $subs->posts ) {

		// Get all fields first
		$all_fields = array();
		// Map fields to column IDs
		$fields_to_cols = array();

		foreach( $subs->posts as $sub ) {

			$groups = get_post_meta( $sub, 'pewc_product_extra_fields', true );

			if( $groups ) {
				foreach( $groups as $group_id=>$group ) {
					foreach( $group as $field_id=>$field ) {
						$field_label = $field['id'];
						$field_label .= isset( $field['label'] ) ? '-' . $field['label'] : '';

						// Check that this field has not already been added as a column
						if( ! isset( $fields_to_cols[$field_label] ) ) {
							$all_fields[$col_index] = $field_label;
							$header_row[$col_index] = $field_label;
							$fields_to_cols[$field_label] = $col_index;
							$col_index++;
						}

					}
				}
			}

			$max_cols = max( $max_cols, $col_index );

		}

		$date_format = get_option( 'date_format' );

		foreach( $subs->posts as $sub ) {

			$user_id = get_post_meta( $sub, 'pewc_user_id', true );
			$user = get_userdata( $user_id );
			$address = array();
			$address[] = get_user_meta( $user_id, 'billing_address_1', true );
			$address[] = get_user_meta( $user_id, 'billing_address_2', true );
			$address[] = get_user_meta( $user_id, 'billing_city', true );
			$address[] = get_user_meta( $user_id, 'billing_state', true );
			$address[] = get_user_meta( $user_id, 'billing_country', true );
			$address[] = get_user_meta( $user_id, 'billing_postcode', true );
			$sub_content = '';

			$order_id = get_post_meta( $sub, 'pewc_order_id', true );

			if( $user ) {
				$display_name = $user->display_name;
				$user_email = $user->user_email;
			} else if( $order_id ) {
				$order = wc_get_order( $order_id );
				if( $order ) {
					$display_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
					$user_email = $order->get_billing_email();
				}

			}

			$row = array(
				'0'	=> $sub,
				'1'	=> get_the_date( $date_format, $sub ),
				'2'	=> $user_id,
				'3'	=> $display_name,
				'4'	=> $user_email,
				'5'	=> get_post_meta( $sub, 'pewc_user_phone', true ),
				'6'	=> str_replace( ', , ', ', ', join( ', ', $address ) ),
				'7'	=> $order_id,
				'8'	=> get_post_meta( $sub, 'pewc_product_id', true ),
				'9'	=> $sub_content,
				'10'	=> get_post_meta( $sub, 'pewc_extra_notes', true ),
				'11'	=> get_post_meta( $sub, 'pewc_extra_status', true )
			);

			// Make sure the row contains all columns, even if they'll be empty
			$count_cols = 12;
			for( $i = 12; $i < $max_cols; $i++ ) {
				$row[$i] = '';
			}

			$groups = get_post_meta( $sub, 'pewc_product_extra_fields', true );

			if( $groups ) {

				foreach( $groups as $group_id=>$group ) {

					foreach( $group as $field_id=>$field ) {

						$field_label = $field['id'];
						$field_label .= isset( $field['label'] ) ? '-' . $field['label'] : '';
						$col_index = $fields_to_cols[$field_label];

						if( isset( $field['value'] ) ) {

							$row[$col_index] = $field['value'];

						} else if( isset( $field['files'] ) ) {

							$uploaded_files = array();

							foreach( $field['files'] as $index=>$file ) {

								if( is_array( getimagesize( $file['file'] ) ) ) {

									$uploaded_files[] = $file['url'];

								}

							}

							$row[$col_index] = join( "\n", $uploaded_files );

						} else {

							$row[$col_index] = '&nbsp;';

						}

					}

				}

			}

			$data_rows[] = $row;

		}

	}

	$fh = @fopen( 'php://output', 'w' );
	fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Content-Description: File Transfer' );
	header( 'Content-type: text/csv' );
	header( "Content-Disposition: attachment; filename={$filename}" );
	header( 'Expires: 0' );
	header( 'Pragma: public' );
	fputcsv( $fh, $header_row );
	foreach ( $data_rows as $data_row ) {
		fputcsv( $fh, $data_row );
	}
	fclose( $fh );
	die();

}
