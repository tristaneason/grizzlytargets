<?php

add_filter( 'ph_fedex_change_currency_to_fedex_currency', 'ph_change_it_to_fedex_currency_value' , 10, 4 );
add_filter( 'ph_fedex_change_currency_to_order_currency', 'ph_fedex_change_it_to_order_currency_value' , 10, 4 );


if( ! function_exists('ph_change_it_to_fedex_currency_value') ) {
	function ph_change_it_to_fedex_currency_value( $product_unit_price, $order_currency, $store_currency, $order ) {

		$order_meta = $order->get_meta_data();

		if( !empty($order_meta) && is_array($order_meta) )
		{
			foreach ($order_meta as $key => $meta_value)
			{
				$order_meta_data 		= $meta_value->get_data();

				if( is_array($order_meta_data) && !empty($order_meta_data) && $order_meta_data['key'] == '_woocs_order_rate' && isset($order_meta_data['value']) )
				{
					$product_unit_price = $product_unit_price / $order_meta_data['value'];
				}
			}
		}
		
		return $product_unit_price;
	}
}

if( ! function_exists('ph_fedex_change_it_to_order_currency_value') ) {
	function ph_fedex_change_it_to_order_currency_value( $product_unit_price, $order_currency, $store_currency, $order ) {

		$order_meta = $order->get_meta_data();

		if( !empty($order_meta) && is_array($order_meta) )
		{
			foreach ($order_meta as $key => $meta_value)
			{
				$order_meta_data 		= $meta_value->get_data();

				if( is_array($order_meta_data) && !empty($order_meta_data) && $order_meta_data['key'] == '_woocs_order_rate' && isset($order_meta_data['value']) )
				{
					$product_unit_price = $product_unit_price * $order_meta_data['value'];
				}
			}
		}
		
		return round( $product_unit_price, 2);
	}
}