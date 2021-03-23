<?php
/**
 * Functions for orders in the backend
 * @since 3.7.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add product_extra information to back-end view order page
 */
function pewc_add_order_itemmeta_admin( $item_id, $item, $product ) {

	// Check to see if this product has got meta
	// If not, then use the pre 3.7.0 method to display add-ons
	if( isset( $item['product_extras']['groups'] ) ) {

		// See if we've got a meta field for the first field
		foreach ( $item['product_extras']['groups'] as $group_id=>$group ) {

			if( $group ) {

				$output = '<ul>';

				foreach( $group as $field_id=>$field ) {

					$field_label = pewc_get_field_label_order_meta( $field, $item );
					$check_meta = $item->get_meta( $field_label );

					if( empty( $check_meta ) ) {

						// If there's no meta data then display add-on data using old method
						add_filter( 'pewc_hide_itemised_add_on_data_order', '__return_false' );
						break;

					}

				}

			}

		}

	}

	// You can display the add-on data in its pre-3.7.0 format if you like
	if( apply_filters( 'pewc_hide_itemised_add_on_data_order', true ) ) {
		return;
	}

	if( isset( $item['product_extras']['groups'] ) ) {

		foreach ( $item['product_extras']['groups'] as $groups ) {

			if( $groups ) {

				$output = '<ul>';

				foreach( $groups as $group ) {

					if( isset( $group['type'] ) ) {
					// if( isset( $group['type'] ) && empty( $group['flat_rate'] ) ) {
						$classes = array( strtolower( str_replace( ' ', '_', $group['type'] ) ) );
						$classes[] = strtolower( str_replace( ' ', '_', $group['label'] ) );

						$price = pewc_get_field_price_order( $group, $product );
						if( ! apply_filters( 'pewc_show_field_prices_in_order', true ) ) {
							$price = '';
						}

						if( $group['type'] == 'upload' ) {

							if( ! empty( $group['files'] ) ) {

								$output .= '<li class="' . join( ' ', $classes ) . '">' . $group['label'] . ': ' . $price . '<ul>';

								foreach( $group['files'] as $index=>$file ) {

									$thumb = '';

									// Add a thumb for image files
									if( is_array( getimagesize( $file['file'] ) ) ) {
										$thumb = sprintf(
											'<img src="%s">',
											esc_url( $file['url'] )
										);
									}

									$output .= sprintf(
										'<li><a target="_blank" href="%s"><span>%s</span>%s</a></li>',
										esc_url( $file['url'] ),
										$thumb,
										$file['display']
									);

								}

								$output .= '</ul></li>';

							}

						} else if( $group['type'] == 'checkbox' ) {

							$output .= '<li class="' . join( ' ', $classes ) . '">' . $group['label'] . ' ' . $price . '</li>';

						} else {

							// $output .= '<li class="' . join( ' ', $classes ) . '">' . $group['label'] . ': ' . $group['value'] . ' ' . $price . '</li>';
							$list_item = apply_filters(
								'pewc_itemmeta_admin_item',
								sprintf(
									'%s: %s %s',
									$group['label'],
									$group['value'],
									$price
								),
								$group,
								$price
							);

							$output .= sprintf(
								'<li class="%s">%s</li>',
								join( ' ', $classes ),
								$list_item
							);

						}

					}

				}

				// Optionally show the original product price in the order
				if( apply_filters( 'pewc_show_original_price_in_order', false ) && isset( $item['product_extras']['original_price'] ) ) {

					$output .= sprintf(
						'<li class="%s">%s: %s</li>',
						join( ' ', $classes ),
						apply_filters( 'pewc_original_price_text', __( 'Original price', 'pewc' ) ),
						wc_price( $item['product_extras']['original_price']  )
					);

				}

				$output .= '</ul>';

				echo apply_filters( 'pewc_add_order_itemmeta_admin', $output, $item_id, $item, $product );

			}

		}

	}

}
add_action( 'woocommerce_after_order_itemmeta', 'pewc_add_order_itemmeta_admin', 10, 3 );

/**
 * Filter our meta labels to remove initial underscore
 * @since 3.7.0
 */
function pewc_attribute_label( $label, $meta_key ) {

	$label = ltrim( $label, '_' );
	return $label;

}
add_filter( 'woocommerce_attribute_label', 'pewc_attribute_label', 10, 2 );

/**
 * Get the price of the field in the order
 * @since 3.7.0
 */
function pewc_get_field_price_order( $field, $product=false ) {

	$hide_zero = get_option( 'pewc_hide_zero', 'no' );
	$price = '';

	// Calculate price
	if( isset( $field['price'] ) ) {

		if( $hide_zero == 'yes' && empty( $field['price'] ) ) {

			$price = '';

		} else {

			/**
			 * Removed in 3.7.1 to avoid doubling of tax
			 */
			// $price = pewc_maybe_include_tax( $product, $field['price'] );
			$price = ' ' . wc_price( $field['price'] );

		}

	}

	return $price;

}


/**
 * Optionally attach uploaded images to the order email
 */
function pewc_attach_images_to_email( $attachments, $id, $order ) {

	if( ( $id == 'new_order' || $id == 'customer_on_hold_order' ) && get_option( 'pewc_email_images', 'no' ) == 'yes' ) {

		// Find any attachments
		$order_items = $order->get_items( 'line_item' );
		if( $order_items ) {
			foreach( $order_items as $order_item ) {
				$product_extras = $order_item->get_meta( 'product_extras' );
				if( ! empty( $product_extras['groups'] ) ) {
					foreach( $product_extras['groups'] as $group ) {
						foreach( $group as $item_id=>$item ) {
							if( ! empty( $item['files'] ) ) {
								foreach( $item['files'] as $index=>$file ) {
									$attachments[] = $file['file'];
								}
							}
						}
					}
				}
			}
		}

	}

	return $attachments;

}
add_filter( 'woocommerce_email_attachments', 'pewc_attach_images_to_email', 10, 3 );

/**
 * Add 'Download files' button to orders with uploaded files
 */
function pewc_order_item_add_action_buttons( $order ) {

	if( pewc_get_pewc_organise_uploads() == 'no' ) {
		return;
	}

	if( ! current_user_can( 'manage_woocommerce' ) ) {
		return;
	}

	$order_id = $order->get_id();

	// Check if the order has uploads
	$dir = trailingslashit( pewc_get_upload_dir() );
	$dir = trailingslashit( $dir ) . $order_id;
	$url = trailingslashit( pewc_get_upload_url() ) . $order_id;

	$filename = trailingslashit( $dir ) . 'uploads-' . $order_id;
	// Get the uploaded files
	$uploaded_files = get_post_meta( $order_id, 'pewc_uploaded_files', true );

	if( ! $uploaded_files ) {
		// No files to download
		return;
	}

	if( isset( $_GET['download_zip'] ) ) {

		$result = pewc_create_zip( $uploaded_files, $filename, false, $order_id );

		if( ! $result ) {
			return;
		}

	  $filename .= '.zip';

	  if( file_exists( $filename ) ) {

	    header( "Content-Type: application/zip" );
	    header( "Content-Disposition: attachment; filename=" . basename( $filename ) );
	    header( "Content-Length: " . filesize( $filename ) );
	    ob_clean();
	    flush();
	    readfile( $filename );

		}

	}

	if( $uploaded_files ) {
		$url = admin_url( 'post.php' );
		$url = add_query_arg(
			array(
				'post'	=> $order_id,
				'action'	=> 'edit',
				'download_zip'	=> 'true'
			),
			$url
		);
		printf(
			'<a href="%s" class="button pewc-download-files">%s</a>',
			esc_url( $url ),
			__( 'Download Files', 'pewc' )
		); ?>

	<?php }

}
add_action( 'woocommerce_order_item_add_action_buttons', 'pewc_order_item_add_action_buttons' );

/* creates a compressed zip file */
function pewc_create_zip( $files = array(), $folder_name='', $overwrite=false, $order_id=false ) {
	// if the zip file already exists and overwrite is false, return false
	if( file_exists( $folder_name ) && ! $overwrite) {
		// return false;
	}

	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists

			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}

	//if we have good files...
	if( count( $valid_files ) ) {
		//create the archive
		$zip = new ZipArchive();
		if( $zip->open( $folder_name . '.zip', $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true ) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {

			$info = pathinfo( $file );
			$ext = isset( $info['extension'] ) && ! empty( $info['extension'] ) ? '.'. $info['extension'] : '';
			$basename = basename( $file, $ext );
			$zip->addFile( $file, trailingslashit( 'uploads-' . $order_id ) . $basename . $ext );
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

		//close the zip -- done!
		$zip->close();

		//check to make sure the file exists
		return file_exists( $folder_name . '.zip' );
	}
	else {
		return false;
	}
}


/**
 * Add add-ons to order again
 * @since 3.7.0
 */
function pewc_order_again_cart_item_data( $cart_item_meta, $product, $order ) {
	$customfields = [
		'product_extras'
	];
	global $woocommerce;
	remove_all_filters( 'woocommerce_add_to_cart_validation' );
	if( ! array_key_exists('item_meta', $cart_item_meta ) || ! is_array( $cart_item_meta['item_meta'] ) ) {
		foreach( $customfields as $key ) {
			if( ! empty($product[$key] ) ) {
				$cart_item_meta[$key] = $product[$key];
			}
		}
	}
	return $cart_item_meta;
}
add_filter( 'woocommerce_order_again_cart_item_data', 'pewc_order_again_cart_item_data', 20, 3 );

/**
 * Integrate with Repeat Order for WooCommerce
 * @link https://wordpress.org/plugins/repeat-order-for-woocommerce/
 * @since 3.7.0
 */
function pewc_order_again( $cart_item_meta, $item, $order ) {

	$addon_fields = wc_get_order_item_meta( $item->get_id(), 'product_extras');

	if( ! $addon_fields ) {
		return $cart_item_meta;
	}
	$cart_item_meta['product_extras'] = $addon_fields;

	return $cart_item_meta;

}
add_filter( 'woocommerce_order_again_cart_item_data', 'pewc_order_again', 99, 3 );
