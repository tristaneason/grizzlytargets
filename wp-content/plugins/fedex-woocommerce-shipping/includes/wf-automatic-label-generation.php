<?php
	$shipping_setting =get_option('woocommerce_wf_fedex_woocommerce_shipping_settings');

	if(isset($shipping_setting['automate_package_generation']) && $shipping_setting['automate_package_generation']=='yes' )
	{
		add_action( 'woocommerce_thankyou', 'wf_automatic_package_and_label_generation_fedex' );
	}
	
	function wf_automatic_package_and_label_generation_fedex( $order_id )
	{
		$order 					= new WC_Order($order_id);
		$order_status 			= $order->get_status();
		$shipping_setting_fedex = get_option('woocommerce_wf_fedex_woocommerce_shipping_settings');
		$allowed_order_status 	= apply_filters( 'xa_automatic_label_generation_allowed_order_status', array('processing'), $order_status, $order_id );	// Allowed order status for automatic label generation

		// Add transient to check for duplicate label generation
		$transient			 	= 'fedex_auto_generate' . md5( $order_id );
		$processed_order		= get_transient( $transient );

		// If requested order is already processed, return.
		if( $processed_order ) {
			return;
		}
		
		// Stop automatic package generation when order status is changed and order status not found in allowed order status
		if( ! in_array($order_status, $allowed_order_status) ) {
			if( $shipping_setting_fedex['debug'] == 'yes' ) {
				WC_Admin_Meta_Boxes::add_error( __( "Since Order Status is ", 'wf-shipping-fedex' ).$order_status.__( ". Automatic label generation has been suspended (Fedex).", 'wf-shipping-fedex' ) );
			}
			return;
		}
		
		$order_items = $order->get_items();
		if( empty($order_items) ) {
			WC_Admin_Meta_Boxes::add_error( __( 'Fedex - No product Found. Please check the products in order.', 'wf-shipping-fedex' ) );
			return;
		}
		
		//  Automatically Generate Packages
		$current_minute=(integer)date('i');

		// Set transient for 2 min to avoid duplicate label generation
		set_transient( $transient, $order_id, 120 );

		$fedex_admin_class 	= new wf_fedex_woocommerce_shipping_admin();

		$fedex_admin_class->ph_fedex_auto_generate_packages( base64_encode($order_id), md5($current_minute), $shipping_setting_fedex );
		
		// $package_url=admin_url( '/post.php?wf_fedex_generate_packages='.base64_encode($order_id).'&auto_generate='.md5($current_minute) );
		// $ch = curl_init();
		// curl_setopt($ch,CURLOPT_URL,$package_url);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// $output=curl_exec($ch);
		// if( ! $output && curl_errno($ch) ) {
		// 	WC_Admin_Meta_Boxes::add_error( __( 'Fedex - Curl error while automatic package generation. Error number - ', 'wf-shipping-fedex' ). curl_errno($ch) );
		// }
		// curl_close($ch);
	}
	
	function wf_get_shipping_service($order,$retrive_from_order = false, $shipment_id=false, $package_group_key=false)
	{
		if($retrive_from_order == true){
			$service_code = get_post_meta($order->id, 'wf_woo_fedex_service_code'.$shipment_id, true);
			if(!empty($service_code)) return $service_code;
		}
		
		if(!empty($_GET['service'])){			
		    $service_arr    =   json_decode(stripslashes(html_entity_decode($_GET["service"])));  
			return $service_arr[0];
		}

		//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
		$shipping_methods = $order->get_shipping_methods();
	
		if( ! empty($shipping_methods) ) {
			$shipping_method 		= array_shift($shipping_methods);
			$shipping_method_meta 	= $shipping_method->get_meta('_xa_fedex_method');
			$shipping_method_id 	= ! empty($shipping_method_meta) ? $shipping_method_meta['id'] : $shipping_method['method_id'];
			if( strstr( $shipping_method_id, WF_Fedex_ID ) ) {
				return apply_filters( 'ph_modify_shipping_method_service', str_replace( WF_Fedex_ID.':', '', $shipping_method_id ) , $order, $package_group_key );
			}
		}

		$settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		//Origin coutry without state
		$origin_country = current( explode( ':', $settings['origin_country'] ) ) ;
		
		if( $origin_country == $order->get_shipping_country() ){
			if( !empty($settings['default_dom_service']) ){
				return $settings['default_dom_service'];
			}
		}else{
			if( !empty($settings['default_int_service']) ){
				return $settings['default_int_service'];
			}
		}
	}
	
	if(isset($shipping_setting['automate_label_generation']) && $shipping_setting['automate_label_generation']=='yes' ){	
		add_action('wf_after_package_generation','wf_auto_genarate_label_fedex',2,2);
	}

	function wf_auto_genarate_label_fedex($order_id,$package_data){
		if( empty($package_data[key($package_data)]) ){
			WC_Admin_Meta_Boxes::add_error('Fedex Automatic label generation Failed. Please check product weight and Dimension.');
		}
		else {
			// Automatically Generate Labels

			$shipping_setting_fedex 	= get_option('woocommerce_wf_fedex_woocommerce_shipping_settings');
			$current_minute 			= (integer)date('i');

			// $package_url=admin_url( '/post.php?wf_fedex_createshipment='.$order_id.'&auto_generate='.md5($current_minute) );

			$weight=array();
			$length=array();
			$width=array();
			$height=array();
			$services=array();
			foreach($package_data as $key=>$val)
			{	
				foreach($val as $key2=>$package)
				{	
					if(isset($package['Weight'])) $weight[]=$package['Weight']['Value'];
					if(isset($package['Dimensions']))
					{
						$length[]=$package['Dimensions']['Length'];
						$width[]=$package['Dimensions']['Width'];
						$height[]=$package['Dimensions']['Height'];
					}
					
					$service 	= wf_get_shipping_service( new WC_Order($order_id),false, false, $key );
					$service 	= apply_filters( 'ph_fedex_label_shipping_method', $service, new WC_Order($order_id) );

					// $package['service'] is set by Multivendor addon
					$services[] = ! empty($package['service']) ? $package['service'] : $service;
				}
			}
			
			$fedex_admin_class 	= new wf_fedex_woocommerce_shipping_admin();

			$fedex_admin_class->ph_fedex_auto_create_shipment( $order_id, md5($current_minute), $shipping_setting_fedex, $weight, $length, $width, $height, $services );

			// $package_url.='&weight=["'.implode('","',$weight).'"]';
			// $package_url.='&length=["'.implode('","',$length).'"]';
			// $package_url.='&width=["'.implode('","',$width).'"]';
			// $package_url.='&height=["'.implode('","',$height).'"]';
			// $package_url.='&service=["'.implode('","',$services).'"]';
			
			// $ch = curl_init();
			// curl_setopt($ch,CURLOPT_URL,$package_url);
			// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			// @$output=curl_exec($ch);
			// if( ! $output && curl_errno($ch) ) {
			// 	WC_Admin_Meta_Boxes::add_error( __( 'Fedex - Curl error while automatic label generation. Error number - ', 'wf-shipping-fedex' ). curl_errno($ch) );
			// }
			// curl_close($ch);
		}
	}

	// To send the label
	// $shipping_setting['auto_email_label']=='yes' is for older version compatibility can be removed after some time, 4.1.0.6
	if( isset($shipping_setting['auto_email_label']) && ( $shipping_setting['auto_email_label']=='yes' || is_array($shipping_setting['auto_email_label']) ) )
	{
		add_action('xa_fedex_label_generated_successfully','wf_after_label_generation_fedex',3,3);
	}


	function wf_after_label_generation_fedex($shipment_id,$encoded_label_image,$order_id)
	{	
		$shipping_setting2 =get_option('woocommerce_wf_fedex_woocommerce_shipping_settings');

		$subject = ( isset($shipping_setting2['email_subject']) && !empty($shipping_setting2['email_subject']) ) ? $shipping_setting2['email_subject'] : __('Shipment Label For Your Order', 'wf-shipping-fedex').' [ORDER NO]';
		$subject = str_replace( '[ORDER NO]', $order_id, $subject );

		if( isset($shipping_setting2['email_content']) && !empty($shipping_setting2['email_content']) ){
			$emailcontent=$shipping_setting2['email_content'];
		}else{
			$emailcontent="<html><body>
				<div>Please Download the label</div>
				<a href='[DOWNLOAD LINK]' ><button>Download the label here</button> </a>
			</body></html>";
		}


		if(!empty($shipment_id)){
			$order = new WC_Order( $order_id );
			$to_emails = array();

			// Get all the email addresses to send the label
			if( is_array($shipping_setting2['auto_email_label']) ) {
				foreach( $shipping_setting2['auto_email_label'] as $label_email_receiver ) {
					switch( $label_email_receiver ) {
						case 'shipper'	: 	$to_emails[] = $shipping_setting2['shipper_email'];
											break;
						case 'customer' :	$to_emails[] = $order->get_billing_email();
											break;
					}
				}
			}		// else part is for older version copatibility can be removed after some time, while removing also remove if( is_array($shipping_setting2['auto_email_label']) ) condition check, 4.1.0.6
			else {
				$to_emails = array( $order->get_billing_email() );
			}
			$to_emails = apply_filters( 'xa_add_email_addresses_to_send_label', $to_emails, $shipment_id, $order);	// Remove this filter after few version 4.1.2
			$to_emails = apply_filters( 'xa_fedex_add_email_addresses_to_send_label', $to_emails, $shipment_id, $order, $shipping_setting2 );

			// For Product Related tag holders
			$stored_packages = get_post_meta( $order_id, '_wf_fedex_stored_packages', true);
			$all_products = array(
				'ids'	=>	null,
				'skus'	=>	null,
				'names'	=>	null,
			);
			$product_info_as_table = null;
			$table_style = "style='border: 1px solid #dddddd;text-align: left;padding: 8px;'";

			if( !empty($stored_packages) && is_array($stored_packages) )
			{
				foreach( $stored_packages as $stored_package ) {
					foreach( $stored_package as $package ) {
						foreach( $package['packed_products'] as $product ) {
							$all_products['ids'] = ! empty($all_products['ids']) ? $all_products['ids'].', '. $product->get_id() : $product->get_id();
							$all_products['skus'] = ! empty($all_products['skus']) ? $all_products['skus'].', '. $product->get_sku() : $product->get_sku();
							$all_products['names'] = ! empty($all_products['names']) ? $all_products['names'].', '. $product->get_name() : $product->get_name();
							$product_info_as_table .= "<tr>
							<td $table_style>".$product->get_id()."</td>
							<td $table_style>".$product->get_sku()."</td>
							<td $table_style>".$product->get_name()."</td>";
						}
					}
				}
			}

			if( ! empty($product_info_as_table) ) {
				$product_info_as_table = "<table style='border-collapse: collapse;'>
											<th $table_style>Product Name</th>
											<th $table_style>Product SKU</th>
											<th $table_style>Product Name</th>
											".$product_info_as_table;
			}

			$customer_email	= "";
			$first_name 	= "";
			$last_name 		= "";

			if( is_object($order) )
			{
				$customer_email = $order->get_billing_email();
				$first_name 	= $order->get_billing_first_name();
				$last_name		= $order->get_billing_last_name();
			}
			
			$customer_name 	= $first_name.' '.$last_name;

			$emailcontent = str_replace( array( "[PRODUCTS ID]", "[PRODUCTS NAME]", "[PRODUCTS SKU]", "[ORDER NO]", "[ORDER AMOUNT]","[PRODUCT_INFO]","[CUSTOMER EMAIL]", "[CUSTOMER NAME]"),
									array( $all_products['ids'], $all_products['names'], $all_products['skus'], $order->get_order_number(), $order->get_total(), $product_info_as_table, $customer_email, $customer_name ), $emailcontent );
			
			$additional_labels 	= get_post_meta($order_id, 'wf_fedex_additional_label_'.$shipment_id, true);
			$add_label 			= '';

			if(!empty($additional_labels) && is_array($additional_labels)) {
				foreach($additional_labels as $additional_key => $additional_label) {					

					$add_label_url = admin_url('/post.php?wf_fedex_additional_label='.base64_encode($shipment_id.'|'.$order_id.'|'.$additional_key));

					$add_label 	.= "<a href='".$add_label_url."' ><button>Additional Label</button></a>";
				}		
			}

			if( !empty($add_label) ) {

				$emailcontent 			= str_replace("[ADDITIONAL LABELS]",$add_label, $emailcontent);
			}
												
			$img_url		= admin_url('/post.php?wf_fedex_viewlabel='.base64_encode($shipment_id.'|'.$order_id));
			$body 			= str_replace("[DOWNLOAD LINK]",$img_url, $emailcontent);

			$headers = array('Content-Type: text/html; charset=UTF-8');
			foreach($to_emails as $to){
				wp_mail( $to, $subject, $body, $headers );
			}
		}
	}

	if(isset($shipping_setting['allow_label_btn_on_myaccount']) && $shipping_setting['allow_label_btn_on_myaccount']=='yes' )
	{	
		add_action('woocommerce_view_order','wf_add_view_shippinglabel_button_on_myaccount_order_page_fedex');
	}
	function wf_add_view_shippinglabel_button_on_myaccount_order_page_fedex($order_id)
	{
		$shipment_id= get_post_meta($order_id,'wf_woo_fedex_shipmentId',true);
		if(!empty($shipment_id))
		{
			$img_url=admin_url('/post.php?wf_fedex_viewlabel='.base64_encode($shipment_id.'|'.$order_id));
			echo ' </br><a href="'.$img_url.'" ><input type="button" value="Download Shipping Label here" class="button" /> </a> </br></br>';			
		}

	}
	unset($shipping_setting);