<?php
/*
 * Handle If bundle product is active
 */

if( ! function_exists('ph_fedex_handle_bundle_products') ) {

	function ph_fedex_handle_bundle_products( $package, $order) {
		if( is_a( $order, 'wf_order' ) ) $order = wc_get_order( $order->get_id() );
		if( is_a( $order, 'WC_Order' ) ) {
			$line_items = $order->get_items();
			$package = array();
			foreach( $line_items as $line_item ) {
				if( is_a($line_item, 'WC_Order_Item_Product') ) {
					$require_shipping = $line_item->get_meta('_bundled_item_needs_shipping');
					if( empty($require_shipping) || $require_shipping == 'yes' ) {
						$product = $line_item->get_product();
						if( is_a( $product, 'WC_Product') ) {
							$product_id = $product->get_id();
							if( ! isset($package[$product_id])) {
								$package[$product_id] = array(
									'data'		=>	$product,
									'quantity'	=>	$line_item->get_quantity(),
								);
							}
							else{
								$package[$product_id]['quantity'] += $line_item->get_quantity();
							}
						}
						else{
								$deleted_products[] = $line_item->get_name();
						}
					}
				}
			}
		}

		if( ! empty($deleted_products) && is_admin() && ! is_ajax() && class_exists('WC_Admin_Meta_Boxes') ) {
			WC_Admin_Meta_Boxes::add_error( __( "FedEx Warning! One or more Ordered Products have been deleted from the Order. Please check these Products- ", 'wf-shipping-fedex' ).implode( ',', $deleted_products ).'.' );
		}
		return $package;
	}
	add_filter( 'xa_fedex_get_customized_package_items_from_order', 'ph_fedex_handle_bundle_products', 10, 2 );
}