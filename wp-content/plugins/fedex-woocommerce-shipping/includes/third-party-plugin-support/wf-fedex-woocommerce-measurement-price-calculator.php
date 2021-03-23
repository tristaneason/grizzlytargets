<?php

/* 
 * This file makes woocommerce measurement price calculator plugin compatible
 */

// To set weight and dimension
add_filter( 'wf_customize_package_on_request_pickup', 'wf_wpmc_modify_weight_generate_packages', 10, 2 );
add_filter( 'wf_customize_package_on_generate_label', 'wf_wpmc_modify_weight_generate_packages', 10, 2 );

function wf_wpmc_modify_weight_generate_packages( $package, $order_id ) {
    
	if( wc()->version > '2.7' ) {
		$wc_dimension_unit = get_option( 'woocommerce_dimension_unit' );
		$order = wc_get_order($order_id);
		$order_items = $order->get_items();
		foreach( $package['contents'] as $key => $values ) {
			foreach( $order_items as  $order_item_key => $order_item) {
				if( $order_item->get_product_id() == $values['data']->get_id() ) {
					$price_calculator_array = get_post_meta($values['data']->id, '_wc_price_calculator', true);
					if( ! empty($price_calculator_array) && is_array($price_calculator_array) ) {
						$calculator_type = $price_calculator_array['calculator_type'];

						$orderitem_measurement_data = wc_get_order_item_meta($order_item_key, '_measurement_data', true);

						if( $price_calculator_array[$calculator_type]['pricing']['weight']['enabled'] == 'yes' && isset($orderitem_measurement_data) ) {
							if( ! empty($orderitem_measurement_data['_measurement_needed']) ) {
								$weight = $values['data']->get_weight() * ($orderitem_measurement_data['_measurement_needed']);
								$package['contents'][$key]['weight'] = $weight;
							}
						}

						if( isset($orderitem_measurement_data) ) {
							if( ! empty ($orderitem_measurement_data['length']['value']) ) {
								$package['contents'][$key]['length'] = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $orderitem_measurement_data['length']['value'], $wc_dimension_unit, $orderitem_measurement_data['length']['unit'] ) ;
							}
							if( ! empty($orderitem_measurement_data['width']['value']) ) {
								$package['contents'][$key]['width'] = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $orderitem_measurement_data['width']['value'], $wc_dimension_unit, $orderitem_measurement_data['width']['unit'] ) ;
							}
							if( ! empty($orderitem_measurement_data['height']['value']) ) {
								$package['contents'][$key]['height'] = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension($orderitem_measurement_data['height']['value'], $wc_dimension_unit, $orderitem_measurement_data['height']['unit'] ) ;
							}
						}
					}
				}
			}
		}
	}
	return $package;
}


//Modify product price when generating the packages and WooCommerce Measurement Price Calculator is active, insurance will work properly only if customs value is not set
add_filter( 'xa_order_product_price', 'wf_wmpc_modify_product_price', 15, 3 );

if( !function_exists('wf_wmpc_modify_product_price') ) {
	function wf_wmpc_modify_product_price( $product_price, $product, $order ){
		if( ! empty($product->weight && wc()->version > '2.7' ) ) {
			$order_items = $order->get_items();
			$price_calculator_array = $product->obj->get_meta('_wc_price_calculator',true);
			if( ! empty($price_calculator_array) ) {
				foreach( $order_items as $order_item_key => $order_item ) {
					if($order_item->get_product_id() == $product->get_id() ) {
						$wf_wpmc_measurement_data = $order_item->get_meta('_measurement_data',true);
						$product_price = ( ! empty($wf_wpmc_measurement_data['_measurement_needed']) ) ? $product_price * $wf_wpmc_measurement_data['_measurement_needed'] : $product_price;
					}
				}
			}
		}
		return $product_price ;
	}
}


/**
* Update dimension in wf_object when WooCommerce Measurement Price Calculator is active for cart and checkout
* @param object $all_values array of wf_object
* @return object array of wf_object with updated length, width and height
*/
add_filter( 'xa_alter_products_list', 'wf_get_modified_length_wpmc', 9 );
if( !function_exists('wf_get_modified_length_wpmc') ) {
	function wf_get_modified_length_wpmc($all_values) {
		foreach( $all_values as $values ) {
			if( is_admin() && wc()->version > '2.7'){
				$values['data']->length = ! empty ($values['length']) ? $values['length'] : $values['data']->length;
				$values['data']->width  = ! empty ($values['width']) ? $values['width']   : $values['data']->width;
				$values['data']->height = ! empty ($values['height']) ? $values['height'] : $values['data']->height;
				$values['data']->weight = ! empty ($values['weight']) ? $values['weight'] : $values['data']->weight;

				unset( $values['length'], $values['width'], $values['height'], $values['weight'] );
			}
			elseif( wc()->version > '2.7' ) {
				$values['data']->length = isset ($values['pricing_item_meta_data']['length']) ? $values['pricing_item_meta_data']['length'] : $values['data']->length;
				$values['data']->width  = isset ($values['pricing_item_meta_data']['width']) ? $values['pricing_item_meta_data']['width']   : $values['data']->width;
				$values['data']->height = isset ($values['pricing_item_meta_data']['height']) ? $values['pricing_item_meta_data']['height'] : $values['data']->height;
			}
			$modified_values[] = $values;
		}
		return !empty($modified_values) ? $modified_values : $all_values;
	}
}