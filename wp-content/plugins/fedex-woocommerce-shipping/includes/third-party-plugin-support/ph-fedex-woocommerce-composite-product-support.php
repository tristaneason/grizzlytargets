<?php

/**
 * This file is to support Woocommerce Composite Products. It will be included only when Woocommerce Composite product plugin is active.
 * We don't need to handle on cart and checkout page, it is handled by Woocommerce Composite product plugin itself.
 * Plugin link : https://woocommerce.com/products/composite-products/
 */

if( ! function_exists('ph_fedex_handle_composite_products') ) {

	/**
	 * Support for Composite product on order page (Admin side). Get customized package.
	 * @param $items array Array of items.
	 * @param $order object wf_order object.
	 * @return array Array of items.
	 */
	function ph_fedex_handle_composite_products( $items, $order ) {

		$order 								= wc_get_order($order->get_id());
		$order_items 						= $order->get_items();
		$composite_product_exist_in_order 	= false;

		foreach( $order_items as $key => $line_item ) {

			$order_items_composit_children[$key]  	= $line_item->get_meta('_composite_children');
			if( ! empty($order_items_composit_children[$key]) ) {
				$composite_product_exist_in_order = true;
			}
		}

		// If composite product exist in order then customize the package accordingly
		if( $composite_product_exist_in_order ) {
			foreach( $order_items as $line_item ) {

				// WC_Order_Item_Product meta key _composite_item_needs_shipping is only set in children items of composite products, possible value yes or no, for all other products it will be empty
				$shipped_individually = $line_item->get_meta('_composite_item_needs_shipping');
				if( $shipped_individually !== 'no' ) {
					$item_id 	= $line_item['variation_id'] ? $line_item['variation_id'] : $line_item['product_id'];
					if( empty($new_items[$item_id]) ) {
						$product_data 			= wc_get_product( $item_id );
						$new_items[$item_id] 	= array('data' => $product_data , 'quantity' => $line_item['qty']);
					}
					else {
						$new_items[$item_id]['quantity'] += $line_item['qty'];
					}
				}
			}
		}

		return ! empty($new_items) ? $new_items : $items;
	}

	// Customize package on order page
	add_filter( 'xa_fedex_get_customized_package_items_from_order', 'ph_fedex_handle_composite_products', 10, 2 );
}