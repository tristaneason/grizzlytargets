<?php
/**
 * Functions for orders / checkout
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add custom meta to order
 */
function pewc_add_custom_data_to_order( $item, $cart_item_key, $values, $order ) {

	$display_product_meta = apply_filters( 'pewc_display_child_product_meta', false, $item );

	foreach( $item as $cart_item_key=>$values ) {

		if( isset( $values['product_extras'] ) ) {

			$product_id = $values['product_extras']['product_id'];

			// Unserialise the add-on data

			if( isset( $values['product_extras']['groups'] ) ) {

				foreach ( $values['product_extras']['groups'] as $group_id=>$group ) {

					if( $group ) {

						$hidden_group_types = apply_filters( 'pewc_hidden_group_types_in_order', array() );

						foreach( $group as $field_id=>$field ) {

							if( isset( $field['type'] ) ) {

								if( $field['type'] == 'products' && ! $display_product_meta ) {
									continue;
								}

								$field_label = pewc_get_field_label_order_meta( $field, $item );

								if( $field['type'] == 'upload' ) {

									$value = pewc_get_uploaded_files_per_field( $field, $item->get_order_id(), $product_id, $values );
									$value = join( ', ', $value );

								} else {

									$value = isset( $field['value'] ) ? $field['value'] : '';
									$value = str_replace( '__checked__', '<span class="dashicons dashicons-yes"></span>', $value );

								}

								// Add the price
								$price = pewc_get_field_price_order( $field );
								if( apply_filters( 'pewc_show_field_prices_in_order', true ) ) {
									$value .= ' ' . $price;
								}

								$value = wp_kses_post( apply_filters( 'pewc_filter_item_value_in_cart', $value, $field ) );

								$item->add_meta_data( $field_label, $value, true );

							}

						}

					}

				}

			}

			// This is all the add-on fields saved as an array
			// This is used in several places, including exports, instead of individual meta data items
			$item->add_meta_data( 'product_extras', $values['product_extras'], true );

		}

	}

}
add_action( 'woocommerce_checkout_create_order_line_item', 'pewc_add_custom_data_to_order', 10, 4 );

function pewc_get_uploaded_files_per_field( $field, $order_id, $product_id, $cart_values=false ) {

	$uploaded_files = array();

	if( ! empty( $field['files'] ) ) {

		foreach( $field['files'] as $index=>$file ) {

			// Only generate a thumb for image files
			// if( is_array( getimagesize( $file['file'] ) ) ) {

				$file_name = isset( $file['file'] ) ? $file['file'] : '';
				$url = isset( $file['url'] ) ? $file['url'] : '';
				$display_name = isset( $file['display'] ) ? $file['display'] : '';

				// Filter the file name if it's renamed
				// Change the file name
				if( pewc_get_rename_uploads() ) {

					$file_name = pewc_get_uploaded_file_name( $file['file'], $order_id, $field, $product_id, $cart_values );
					$url = pewc_get_uploaded_file_url( $file['url'], $order_id, $field, $product_id, $cart_values );
					$display_name = pewc_get_uploaded_file_display( $file['display'], $file_name, $cart_values );

				}

				if( ! empty( $file['quantity'] ) ) {
					$display_name .= sprintf(
						' [%s: %s]',
						__( 'Quantity', 'pewc' ),
						$file['quantity']
					);
				}

				$uploaded_files[] = sprintf(
					'<a href="%s" target="_blank">%s</a>',
					esc_url( $url ),
					esc_html( $display_name )
				);

			}

		// }

	}

	return $uploaded_files;

}

/**
 * Add product_extra information to front-end view order page
 */
function pewc_order_item_name( $product_name, $item ) {

	$display_product_meta = apply_filters( 'pewc_display_child_product_meta', false, $item );

	if( isset( $item['product_extras']['groups'] ) ) {

		foreach ( $item['product_extras']['groups'] as $groups ) {

			if( $groups ) {

				$hidden_group_types = apply_filters( 'pewc_hidden_group_types_in_order', array() );

				$product_name .= '<ul>';

				foreach( $groups as $group ) {

					if( isset( $group['type'] ) ) {

						if( in_array( $group['type'], $hidden_group_types ) ) {
							// Don't add this to the order if it's a hidden field type
							continue;
						}

						if( ! empty( $group['hidden'] ) ) {
							// Don't add hidden fields
							continue;
						}

						if( $group['type'] == 'products' && ! $display_product_meta ) {
							continue;
						}

					// if( isset( $group['type'] ) && empty( $group['flat_rate'] ) ) {
						$classes = array( strtolower( str_replace( ' ', '_', $group['type'] ) ) );
						$classes[] = strtolower( str_replace( ' ', '_', $group['label'] ) );

						$product_id = $item->get_product_id();
						$product = wc_get_product( $product_id );
						$price = pewc_get_field_price_order( $group, $product );

						if( ! apply_filters( 'pewc_show_field_prices_in_order', true ) ) {
							$price = '';
						}

						if( $group['type'] == 'upload' ) {

							if( ! empty( $group['files'] ) ) {

								$display = sprintf(
									'<li class="%s"><span class="pewc-order-item-label">%s:</span> <span class="pewc-order-item-price">%s</span>',
									join( ' ', $classes ),
									$group['label'],
									$price
								);

								foreach( $group['files'] as $index=>$file ) {

									// Only generate a thumb for image files
									if( is_array( getimagesize( $file['file'] ) ) || apply_filters( 'pewc_force_always_display_thumbs', false ) ) {
										$img = sprintf(
											'<br><img style="max-width: 50px; height: auto;" src="%s">',
											esc_url( $file['url'] )
										);
									} else {
										$img = '';
									}

									$display_name = $file['display'];

									if( ! empty( $file['quantity'] ) ) {
										$display_name .= sprintf(
											' [%s: %s]',
											__( 'Quantity', 'pewc' ),
											$file['quantity']
										);
									}

									$display .= sprintf(
										'<br><span class="pewc-order-item-item"><a target="_blank" href="%s">%s</a></span>%s',
										$file['url'],
										$display_name,
										$img
									);

								}

								$display .= '</li>';

								$product_name .= $display;

							}

						} else if( $group['type'] == 'checkbox' ) {

							$product_name .= '<li class="' . join( ' ', $classes ) . '"><span class="pewc-order-item-label">' . $group['label'] . '</span> <span class="pewc-order-item-price">' . $price . '</span></li>';

						} else {

							$value = wp_kses_post( apply_filters( 'pewc_filter_item_value_in_cart', $group['value'], $group ) );

							$product_name .= apply_filters(
								'pewc_order_item_product_name',
								'<li class="' . join( ' ', $classes ) . '"><span class="pewc-order-item-label">' . $group['label'] . ':</span> <span class="pewc-order-item-item">' . nl2br( $value ) . '</span> <span class="pewc-order-item-price">' . $price . '</span></li>',
								$group,
								$price
							);

						}
					}
				}

				// Optionally show the original product price in the order
				if( apply_filters( 'pewc_show_original_price_in_order', false ) && isset( $item['product_extras']['original_price'] ) ) {

					$product_name .= sprintf(
						'<li class="%s">%s: %s</li>',
						join( ' ', $classes ),
						apply_filters( 'pewc_original_price_text', __( 'Original price', 'pewc' ) ),
						wc_price( $item['product_extras']['original_price']  )
					);

				}

				$product_name .= '</ul>';
			}
		}
	}

	return $product_name;

}
add_filter( 'woocommerce_order_item_name', 'pewc_order_item_name', 10, 2 );

/**
 * Create product_extra post when the order is processed
 */
function pewc_create_product_extra( $order_id ) {

	$order = wc_get_order( $order_id );
	$payment_method = is_callable( array( $order, 'get_payment_method' ) ) ? $order->get_payment_method() : $order->payment_method;

	// Don't publish product_extras for COD orders.
	if ( $order->has_status( 'processing' ) && 'cod' === $payment_method ) {
		// return;
	}

	// Get the product_extra meta data and create the product_extra
	$order_items = $order->get_items( 'line_item' );

	if( $order_items ) {

		foreach( $order_items as $order_item_id=>$order_item ) {

			$product_extras = $order_item->get_meta( 'product_extras' );

			if( ! empty( $product_extras['groups'] ) || ! empty( $product_extras['products'] ) ) {

				// Save the product_extra data
				$product_extra_id = wp_insert_post( array(
					'post_title'	=> $product_extras['title'],
					'post_type'   => 'pewc_product_extra',
					'post_status'	=> 'publish'
				) );
				if( ! is_wp_error( $product_extra_id ) ) {
					wp_update_post(
						array(
							'ID'					=> $product_extra_id,
							'post_title'	=> $product_extras['title'] . ' #' . $product_extra_id
						)
					);
					// User data
					$user_id = $order->get_user_id();
					$user = get_userdata( $user_id );
					if( $user && ! is_wp_error( $user ) ) {
						update_post_meta( $product_extra_id, 'pewc_user_id', absint( $user_id ) );
					}

					update_post_meta( $product_extra_id, 'pewc_order_id', absint( $order_id ) );
					update_post_meta( $product_extra_id, 'pewc_item_cost', $order->get_item_total( $order_item ) );
					update_post_meta( $product_extra_id, 'pewc_order_total', $order->get_total() );
					update_post_meta( $product_extra_id, 'pewc_product_id', absint( $product_extras['product_id'] ) );

					update_post_meta( $product_extra_id, 'pewc_user_name', sanitize_text_field( $order->get_formatted_billing_full_name() ) );
					update_post_meta( $product_extra_id, 'pewc_user_email', sanitize_email( $order->get_billing_email() ) );
					update_post_meta( $product_extra_id, 'pewc_user_phone', sanitize_text_field( $order->get_billing_phone() ) );

					// Save the product_extra ID to the order as well
					update_post_meta( $order_id, 'pewc_product_extra_id', absint( $product_extra_id ) );

				}

				$fields = array();

				if( ! empty( $product_extras['groups'] ) ) {

					// Rename any uploads if appropriate
					$product_extras['groups'] = pewc_rename_uploaded_files_item_meta( $order_item );

					foreach( $product_extras['groups'] as $groups ) {

						if( $groups ) {

							foreach( $groups as $group ) {

								if( isset( $group['type'] ) ) {

									$group_id = $group['group_id'];
									$field_id = $group['field_id'];
									$fields[$group_id][$field_id] = array(
										'id'	=> sanitize_text_field( $group['id'] ),
										'type'	=> sanitize_text_field( $group['type'] ),
										'label'	=> sanitize_text_field( $group['label'] )
									);

									if( isset( $group['price'] ) ) {
										$fields[$group_id][$field_id]['price'] = $group['price'];
									}

									if( $group['type'] == 'upload' ) {

										$fields[$group_id][$field_id]['files'] = $group['files'];
										// $fields[$group_id][$field_id]['url'] = esc_url( $group['url'] );
										// $fields[$group_id][$field_id]['display'] = sanitize_text_field( $group['display'] );
										// Delete uploaded image in product_extras folder (tidy up time)
										// unlink( $group['file'] );

									} else {

										$fields[$group_id][$field_id]['value'] = sanitize_text_field( $group['value'] );

									}

									// Use this for fancy stuff, like sending custom emails
									do_action( 'pewc_after_create_product_extra', $product_extra_id, $order, $group );

								}
							}
						}
					}
					if( ! empty( $fields ) ) {

						update_post_meta( $product_extra_id, 'pewc_product_extra_fields', $fields );

					}
				}
			}
		}

	}
}
add_action( 'woocommerce_checkout_order_processed', 'pewc_create_product_extra', 10, 1 );

/**
 * Rename uploaded files if necessary
 * @return Array 	Updated file array
 * @param $files	$files array from field
 * @since 3.7.0
 */
function pewc_rename_uploaded_files_item_meta( $item ) {

	if( isset( $item['product_extras']['groups'] ) ) {

		$order_id = $item->get_order_id();
		$order = wc_get_order( $order_id );
		$order_id = apply_filters( 'woocommerce_order_number', $order_id, $order );

		$item_id = $item->get_id();
		$product_id = $item['product_extras']['product_id'];

		$new_item_meta = $item['product_extras'];

		if( ( pewc_get_rename_uploads() || pewc_get_pewc_organise_uploads() ) && isset( $new_item_meta['groups'] ) ) {

			// Save a list of all uploaded files in this order
			$uploaded_files = get_post_meta( $order_id, 'pewc_uploaded_files', true );
			if( ! $uploaded_files ) {
				$uploaded_files = array();
			}

			foreach( $new_item_meta['groups'] as $group_id=>$group ) {

				if( $group ) {

					foreach( $group as $field_id=>$field ) {

						if( isset( $field['type'] ) && $field['type'] == 'upload' ) {

							if( ! empty( $field['files'] ) ) {

								$uploaded_files_meta = array();

								foreach( $field['files'] as $index=>$file ) {

									$new_file_name = $file['file'];
									$new_url = $file['url'];
									$new_display_name = $file['display'];

									if( pewc_get_rename_uploads() ) {

										// Change the file name
										$new_file_name = pewc_get_uploaded_file_name( $file['file'], $order_id, $field, $product_id, $item );
										$new_url = pewc_get_uploaded_file_url( $file['url'], $order_id, $field, $product_id, $item );
										$new_display_name = pewc_get_uploaded_file_display( $file['display'], $new_file_name, $item );

									}

									// Move files into order specific folders
									// Check if we are moving into order specific folders
									if( pewc_get_pewc_organise_uploads() == 'yes' ) {

										$upload_dir = trailingslashit( pewc_get_upload_dir() );
										$upload_url = trailingslashit( pewc_get_upload_url() );

										$order_upload_dir = rtrim( trailingslashit( $upload_dir ) . $order_id, '/' );
										$order_upload_url = rtrim( trailingslashit( $upload_url ) . $order_id, '/' );

										$order_upload_dir = apply_filters( 'pewc_order_upload_dir', $order_upload_dir, $field );
										$order_upload_url = apply_filters( 'pewc_order_upload_url', $order_upload_url, $field );

										$info = pathinfo( $new_file_name );
										$ext = isset( $info['extension'] ) && ! empty( $info['extension'] ) ? '.'. $info['extension'] : '';
										$basename = basename( $new_file_name, $ext );

										// Create the directory
										if ( ! file_exists( $order_upload_dir ) ) {
											mkdir( $order_upload_dir, 0755, true );
											// Top level blank index.php
											@file_put_contents( $order_upload_dir . '/index.php', '<?php' . PHP_EOL . '// That whereof we cannot speak, thereof we must remain silent.' );

										}

										$new_file_name = trailingslashit( $order_upload_dir ) . $basename . $ext;
										$new_url = trailingslashit( $order_upload_url ) . $basename . $ext;

									}

									// Move / rename the file
									if( file_exists( $file['file'] ) ) {

										// Don't rename twice, it's not New York
										rename( $file['file'], $new_file_name );
										$uploaded_files[] = $new_file_name;

									}

									$new_item_meta['groups'][$group_id][$field_id]['files'][$index]['file'] = $new_file_name;
									$new_item_meta['groups'][$group_id][$field_id]['files'][$index]['url'] = $new_url;
									$new_item_meta['groups'][$group_id][$field_id]['files'][$index]['display'] = $new_display_name;

									if( ! empty( $file['quantity'] ) ) {
										$new_display_name .= sprintf(
											' [%s: %s]',
											__( 'Quantity', 'pewc' ),
											$file['quantity']
										);
									}

									$uploaded_files_meta[] = sprintf(
										'<a href="%s" target="_blank">%s</a>',
										esc_url( $new_url ),
										esc_html( $new_display_name )
									);

								}

								// Update the meta
								$field_label = pewc_get_field_label_order_meta( $field, $item );
								wc_update_order_item_meta( $item_id, $field_label, join( ', ', $uploaded_files_meta ) );

							}

						}

					}

				}

			}

			// Save the list of uploaded files
			update_post_meta( $order_id, 'pewc_uploaded_files', array_unique( $uploaded_files ) );

			// Update the meta
			wc_update_order_item_meta( $item_id, 'product_extras', $new_item_meta );
			return $new_item_meta['groups'];

		}

	}

	return $item['product_extras']['groups'];

}

/**
 * Filter the uploaded file name to include tags
 * @since 3.7.0
 */
function pewc_get_uploaded_file_name( $name, $order_id, $field, $product_id, $order_item=false ) {

	$quantity = isset( $order_item['quantity'] ) ? $order_item['quantity'] : false;

	// Change the file name
	$new_file_name = str_replace( 'xxorder_numberxx', $order_id, $name );
	$new_file_name = str_replace( 'xxgroup_idxx', $field['group_id'], $new_file_name );
	$new_file_name = str_replace( 'xxfield_idxx', $field['field_id'], $new_file_name );
	$new_file_name = str_replace( 'xxquantityxx', $quantity, $new_file_name );

	return $new_file_name;

}

/**
 * Filter the uploaded file name to include tags
 * @since 3.7.0
 */
function pewc_get_uploaded_file_url( $url, $order_id, $field, $product_id, $order_item=false ) {

	$quantity = isset( $order_item['quantity'] ) ? $order_item['quantity'] : false;

	// Change the file name
	$new_url = str_replace( 'xxorder_numberxx', $order_id, $url );
	$new_url = str_replace( 'xxgroup_idxx', $field['group_id'], $new_url );
	$new_url = str_replace( 'xxfield_idxx', $field['field_id'], $new_url );
	$new_url = str_replace( 'xxquantityxx', $quantity, $new_url );

	return $new_url;

}

/**
 * Filter the uploaded file name to include tags
 * @since 3.7.0
 */
function pewc_get_uploaded_file_display( $display, $filename ) {

	// Change the file display name
	$info = pathinfo( $filename );
	$ext  = isset( $info['extension'] ) && ! empty( $info['extension'] ) ? '.'. $info['extension'] : '';
	$name = basename( $filename, $ext );

	return $name . $ext;

}

function pewc_get_field_label_order_meta( $field, $item ) {

	if( empty( $field['label'] ) || apply_filters( 'pewc_use_field_id_order_item_meta_label', false, $field, $item ) ) {
		$field_label = $field['id'];
	} else {
		$field_label = $field['label'];
	}

	// Hide the meta key from the front end
	if( apply_filters( 'pewc_apply_underscore_metakey', true, $field, $item ) ) {
		$field_label = '_' . $field_label;
	}
	return apply_filters( 'pewc_field_label_item_meta_data', $field_label, $field, $item );

}
