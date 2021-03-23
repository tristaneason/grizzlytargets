<?php

/**
 * This file is to support YITH multiple shipping address. It will be included only when YITH multiple shipping address.
 */

if( ! function_exists('ph_fedex_label_packages_yith') ) {

	/**
	 * Support for shipping multiple address. Get customized package.
	 */
	function ph_fedex_label_packages_yith( $packages, $address,$order_id ) {
		$order = wc_get_order($order_id);
		$destination_packages=array();
		foreach( $order->get_items('shipping') as $item ){
		    $item_data = $item->get_data();
		    $content=array();
		    if(!empty($item->get_meta('ywcmas_shipping_destination')))
		    {
		    	$content['contents']=$item->get_meta('ywcmas_shipping_contents');
		    	$destination=$item->get_meta('ywcmas_shipping_destination');
		    	$destination['address_1']=isset($destination['address'])?$destination['address']:'';
		    	$content['destination']=$destination;
		    }
		    
		    $destination_packages[]=$content;
		}
		if(!empty($destination_packages) && !empty($destination_packages[0])){
			return $destination_packages;
		}
		return $packages;
	}
	add_filter( 'wf_filter_label_packages', 'ph_fedex_label_packages_yith', 10, 3 );
}