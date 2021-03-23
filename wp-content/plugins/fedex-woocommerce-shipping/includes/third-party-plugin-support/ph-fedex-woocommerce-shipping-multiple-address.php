<?php

add_filter('wf_filter_label_packages', 'ph_fedex_shipping_packages_filter', 21, 3);

function ph_fedex_shipping_packages_filter($packages,$address, $order_id){	

	$wms_packages           = get_post_meta($order_id, '_wcms_packages', true);	

	if(is_array($wms_packages)){
		return $wms_packages;
	}

	return $packages;
}

// To display respective Shipping Method for Multiple Shipping Addresses in Order Shipping Addresses Meta Box
add_filter('woocommerce_order_get_items', 'ph_fedex_update_shipping_method_instance_id', 10, 3);

function ph_fedex_update_shipping_method_instance_id($items, $that, $type )
{

	if( $type[0] == 'shipping' && !empty($items) && is_array($items) )
	{

		$order_id 			= '';
		$method_index 		= 0;
		$methods 			= [];
		$shipping_methods 	= [];

		foreach ($items as $order_item_shipping)
		{

			if( is_object($order_item_shipping) )
			{
				$order_id 	= $order_item_shipping->get_order_id();
				$order_meta = $order_item_shipping->get_meta_data();
				
				foreach ($order_meta as $key => $meta_value)
				{
					$order_meta_data 		= $meta_value->get_data();

					if( is_array($order_meta_data) && !empty($order_meta_data) && $order_meta_data['key'] == '_xa_fedex_method' && is_array($order_meta_data['value']) )
					{
						$instance_array = explode(':', $order_meta_data['value']['id'] );

						if( isset($instance_array[1]) && !empty($instance_array[1]) )
						{
							
							$methods[$method_index]['id'] = $order_meta_data['value']['id']; 
							$methods[$method_index]['label'] = $order_meta_data['value']['id']; 

							$shipping_methods 	=  array_merge($shipping_methods, $methods);

							$order_item_shipping->set_instance_id( $instance_array[1] );

							$order_item_shipping->apply_changes();

							$order_item_shipping->save();
							
						}
					}
				}	
			}

		}
		
		if( !empty($order_id) && !empty($shipping_methods) )
		{
			$existing_method = get_post_meta( $order_id, '_shipping_methods' );
			
			update_post_meta( $order_id, '_shipping_methods', $shipping_methods, $existing_method );

			$new_order_total 			= 0;
			$order_total 				= 0;
			$order_subtotal 			= 0;
			$order_cart_tax				= 0;
			$order_discount_with_tax	= 0;
			$shipping_total 			= 0;
			$shipping_tax 				= 0;
			$order 						= wc_get_order($order_id);

			if( is_object($order) && !empty($order->get_user_id()) && !empty($order->get_order_key()) )
			{
				$order_shipping 			= get_post_meta( $order_id, '_order_shipping' );
				$order_total 				= !empty( $order->get_total() ) ? $order->get_total() : 0;
				$order_subtotal 			= !empty( $order->get_subtotal() ) ? $order->get_subtotal() : 0;
				$order_cart_tax				= !empty( $order->get_cart_tax() ) ? $order->get_cart_tax() : 0;
				$order_discount_with_tax	= !empty( $order->get_total_discount(false) ) ? $order->get_total_discount(false) : 0;
				$shipping_total 			= !empty( $order->get_shipping_total() ) ? $order->get_shipping_total() : 0;
				$shipping_tax 				= !empty( $order->get_shipping_tax() ) ? $order->get_shipping_tax() : 0;
				
				$new_order_total = ( $order_subtotal + $order_cart_tax + $shipping_total + $shipping_tax ) - $order_discount_with_tax;
				
				if( ($order_total != $new_order_total) && ($order_total < $new_order_total) )
				{
					update_post_meta( $order_id, '_order_total',  $new_order_total);
				}

				if( ($order_shipping != $shipping_total) && ($order_shipping < $shipping_total) )
				{
					update_post_meta( $order_id, '_order_shipping', $shipping_total );
				}
				
			}
		}
		
	}
	
	return $items;

}


//get correct shipping service for package
if( ! function_exists('ph_fedex_shipping_method_for_multiple_address') ) {
	function ph_fedex_shipping_method_for_multiple_address( $shipping_sevice, $order, $package_group_key=false ) {
		if( $package_group_key != false ) {
			$shipping_methods = $order->get_shipping_methods();
			while( $package_group_key >= 0 ) {
				$shipping_method 		= array_shift($shipping_methods);
				$package_group_key--;
			}
			$shipping_method_meta 	= $shipping_method->get_meta('_xa_fedex_method');
			$shipping_method_id 	= ! empty($shipping_method_meta) ? $shipping_method_meta['id'] : $shipping_method['method_id'];

			if( strstr( $shipping_method_id, WF_Fedex_ID ) ) {
				return str_replace( WF_Fedex_ID.':', '', $shipping_method_id );
			}
		}
		else {
			return $shipping_sevice;
		}
	}
}

add_filter( 'ph_modify_shipping_method_service', 'ph_fedex_shipping_method_for_multiple_address', 10, 3);

