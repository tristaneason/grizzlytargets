<?php

/**
 * Mix-and-Match (MNM) product plugin support in rate request .
 * Plugin link - https://woocommerce.com/products/woocommerce-mix-and-match-products/
 */
if( ! function_exists('ph_fedex_woocommerce_mixed_and_match_product_support_rate_request') ) {
	function ph_fedex_woocommerce_mixed_and_match_product_support_rate_request($package) {
		foreach( $package['contents'] as $key => $line_item ) {
			// If product type is MNM and per item shipping is checked in MNM product setting then unset this MNM product since we have to get every individual product weight and dimension.
			if( $line_item['data']->get_type() == 'mix-and-match' && $line_item['data']->is_shipped_per_product() ) {
				
				// In per item case shipping we need price of every product for custom so if per item pricing is not checked then set price in every item
				if( ! $line_item['data']->is_priced_per_product() && ! empty($line_item['mnm_contents']) ) {
					// Loop through the products available in mix-and-match and set their respective price
					foreach( $line_item['mnm_contents'] as $product_key_in_mix_match ) {
						$product_id = ! empty($package['contents'][$product_key_in_mix_match]['variation_id']) ? $package['contents'][$product_key_in_mix_match]['variation_id'] : $package['contents'][$product_key_in_mix_match]['product_id'];
						$product 	= wc_get_product($product_id);
						$package['contents'][$product_key_in_mix_match]['data']->set_price( $product->get_price() );
					}
				}

				unset( $package['contents'][$key]);		// Unset Mix-and-match product from package in case of per item shipping
			}
		}
		return $package;
	}

	add_filter( 'wf_customize_package_on_cart_and_checkout', 'ph_fedex_woocommerce_mixed_and_match_product_support_rate_request' );
}


/**
 * Get updated data of package contents on order admin page . To support mixed-and-matched (MNM) product on admin order page.
 * MNM product plugin require woocommerce 3.0.
 * @param $items array Array of Package Contents.
 * @param $order object wf_order or WC_Order object.
 * @return array Array of updated package Contents.
 */

if( ! function_exists('ph_fedex_woocommerce_mixed_and_match_product_support_on_order_page') ) {
	function ph_fedex_woocommerce_mixed_and_match_product_support_on_order_page( $items, $order ) {

		$order 							= wc_get_order( $order->get_id());
		$order_items 					= $order->get_items();
		$mix_and_match_product_found 	= false;
		$fedex_settings 				= $GLOBALS['xa_fedex_settings'];

		foreach( $order_items as $key => $line_item ) {

			$product 						= $line_item->get_product();
			$all_products_in_order[$key] 	= $product;

			// Product type for MNM product is mix-and-match
			if( $product->get_type() == 'mix-and-match' ) {
				$mix_and_match[$line_item->get_meta('_mnm_cart_key')] 	= $key;		// _mnm_cart_key set in case of mnm products
				$mix_and_match_product_found 							= true;
			}

			// _mnm_item_needs_shipping meta key set in case of MNM child product not the main product values - yes or no
			$per_item_shipping_status = $line_item->get_meta('_mnm_item_needs_shipping');

			// If this item is part of MNM product and per item shipping disabled in the MNM product at the time of order creation
			if( $per_item_shipping_status === 'no' ) {
				unset($order_items[$key]);
				$skipped_products[] = $key;
			}
			elseif( $per_item_shipping_status === 'yes' ) {
				// Unset main MNM product since per item shipping was enabled at the time of order creation
				$mixed_product_to_unset = $mix_and_match[$line_item->get_meta('_mnm_container')];
				$skipped_products[] 	= $mixed_product_to_unset;
				unset($order_items[$mixed_product_to_unset]);
			}
		}

		// If any MNM product found in order then reset the items
		if( $mix_and_match_product_found ) {
			$items = null;
			foreach ( $order_items as $key => $line_item ) {
				$items[] = array(
					'data'		=> $line_item->get_product(),
					'quantity'	=> $line_item['quantity'],
				);
			}

			// Print skipped items name of MNM either parent or child products
			if( $fedex_settings['debug'] == 'yes' ) {
				$skipped_products = array_unique($skipped_products);
				WC_Admin_Meta_Boxes::add_error( sprintf( __( 'FedEx - Mixed and Matched Products found in this order. Skipped Products :', 'wf-shipping-fedex' ) ) );
				foreach( $skipped_products as $skipped_product_key ) {
					WC_Admin_Meta_Boxes::add_error( sprintf( __( $all_products_in_order[$skipped_product_key]->get_name() ) ) );
				}
			}
		}

		return $items;
	}

	add_filter( 'xa_fedex_get_customized_package_items_from_order', 'ph_fedex_woocommerce_mixed_and_match_product_support_on_order_page', 8, 2 );
}