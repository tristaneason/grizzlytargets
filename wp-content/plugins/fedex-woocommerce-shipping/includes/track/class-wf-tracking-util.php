<?php

class Ph_FedEx_Tracking_Util {
	const TRACKING_SETTINGS_TAB_KEY 		= "wf_tracking";
	const TRACKING_DATA_KEY 			= "_tracking_data";
	const TRACKING_MESSAGE_KEY 			= "_custom_message";
	const TRACKING_TURN_OFF_API_KEY			= "_turn_off_api";
	const TRACKING_TURN_OFF_CSV_IMPORT_KEY		= "_turn_off_csv_import";	//To remove cofliction from woocommerce shipping Tracking Pro
	const TRACKING_TURN_OFF_EMAIL_STATUS_KEY	= "_turn_off_email_status";	//To remove cofliction from woocommerce shipping Tracking Pro
	const TAG_SHIPMENT_SERVICE			= "[SERVICE]";
	const TAG_SHIPMENT_DATE				= "[DATE]";
	const TAG_SHIPMENT_ID				= "[ID]";
	const TAG_SHIPPING_POST_CODE			= "[PIN]";
	
	public static function convert_shipment_result_obj_to_array ( $shipment_result_obj ) {
		$shipment_result_array 			= array();
		$shipment_result_array['message']	= $shipment_result_obj->message;

		$tracking_info_array = array();
		if( !empty( $shipment_result_obj->tracking_info_obj_array ) ) {
			foreach ( $shipment_result_obj->tracking_info_obj_array as $tracking_info_obj ) {
				$tracking_info			= array();
				$tracking_info['tracking_link']	= $tracking_info_obj->tracking_link;
				$tracking_info['tracking_id']	= $tracking_info_obj->tracking_id;
				$tracking_info_array[] 		= $tracking_info;
			}
			
			$shipment_result_array['tracking_info'] = $tracking_info_array;
		}
		
		$tracking_info_api_array = array();
		if( !empty( $shipment_result_obj->tracking_info_api_obj_array ) ) {
			foreach ( $shipment_result_obj->tracking_info_api_obj_array as $tracking_info_api_obj ) {
				$tracking_info_api				= array();
				$tracking_info_api['tracking_link']		= $tracking_info_api_obj->tracking_link;
				$tracking_info_api['tracking_id']		= $tracking_info_api_obj->tracking_id;
				$tracking_info_api['api_tracking_status']	= $tracking_info_api_obj->api_tracking_status;
				$tracking_info_api['api_tracking_error']	= $tracking_info_api_obj->api_tracking_error;
				$tracking_info_api_array[]			= $tracking_info_api;
			}

			$shipment_result_array['tracking_info_api'] = $tracking_info_api_array;
		}

		return $shipment_result_array;
	}
	
	public static function load_tracking_data( $sort = false, $force_default =  false ) {
		
		$tracking_data		= include( 'data-wf-tracking.php' );
		$tracking_data		= self::transform_tracking_data( $tracking_data );
		
		if( $sort) {
			ksort( $tracking_data );
		}

		return $tracking_data;
	}
	
	public static function transform_tracking_data( $input_tracking_data ) {
		$tracking_data = array();
		foreach ( $input_tracking_data as $key => $tracking_ele ) {
			$name				= $tracking_ele[ 'name' ];
			$new_key			= sanitize_title( $name );
			$tracking_data[ $new_key ]	= $tracking_ele;
		}
		
		return $tracking_data;
	}

	public static function convert_tracking_data_to_piped_text( $tracking_data ) {
		$tracking_data_txt = '';
		foreach ( $tracking_data as $key => $tracking_ele ) {
			$tracking_data_txt .= $tracking_ele[ 'name' ];
			$tracking_data_txt .= ' | ';
			$tracking_data_txt .= $tracking_ele[ 'tracking_url' ];
			$tracking_data_txt .= "\n";
		}

		return $tracking_data_txt;
	}
	
	/**
	 * default_tracking_data can be obtained by calling load_tracking_data by setting force_default param true.
	 */
	public static function convert_piped_text_to_tracking_data( $tracking_data_txt, $default_tracking_data ) {
		$data_txt_array	= explode( "\n", $tracking_data_txt );
		$tracking_data 	= array();
		
		foreach ( $data_txt_array as  $data_txt ) {
			$name		= '';
			$tracking_url 	= '';
			$api_url	= '';
			
			$data_elem = explode( "|", $data_txt );
			if( isset( $data_elem[0] ) && '' != trim( $data_elem[0] ) ) {
				$name = trim( $data_elem[0] );
				if ( isset( $data_elem[1]) ) {
					$tracking_url = trim( $data_elem[1] );
				}
				
				$key		= sanitize_title( $name );
				$api_url	= '';
				if( isset( $default_tracking_data[$key]['api_url'] ) ) {
					$api_url = $default_tracking_data[$key]['api_url'];
				}
			}

			if ( '' != $name ) {
				$tracking_data_val			= array();
				$tracking_data_val['name']		= $name;
				$tracking_data_val['tracking_url']	= $tracking_url;
				$tracking_data_val['api_url']		= $api_url;
				$tracking_data[ $key ]			= $tracking_data_val;
			}
		}
		
		return $tracking_data;
	}
	
	public static function get_default_shipment_message_placeholder() {
		$message = 'Your order was shipped on [DATE] via [SERVICE]. To track shipment, please follow the link of shipment ID(s) [ID]';
		return $message;
	}

	public static function get_shipment_custom_message($shipment_source_data) {
		$shipment_custom_message = get_option( self::TRACKING_SETTINGS_TAB_KEY.self::TRACKING_MESSAGE_KEY, '' );
		return apply_filters('wf_custom_tracking_message', $shipment_custom_message, get_locale(), $shipment_source_data['order_id'] );
	}

	public static function get_shipment_display_custom_message( $shipment_result_array, $shipment_source_data ) {
		$shipment_display_message = '';
		if ( isset( $shipment_result_array['tracking_info'] ) ) {
			$shipment_custom_message = self::get_shipment_custom_message($shipment_source_data);
		
			$tracking_id_substr = '';
			foreach ( $shipment_result_array['tracking_info'] as $tracking_info ) {
				if( empty($tracking_info['tracking_id']) )
					continue;

				$tracking_id_substr .= ' ';
				if( '' == $tracking_info['tracking_link'] ) {
					$tracking_id_substr .= $tracking_info['tracking_id'].',';
				}
				else {
					$tracking_id_substr .= ' <a href="'.$tracking_info['tracking_link'].'" target="_blank">'.$tracking_info['tracking_id'].'</a>,';
				}
			}
			$tracking_id_substr		= rtrim( $tracking_id_substr, ',' );
			$tracking_id_substr		= trim( $tracking_id_substr );
			$tracking_data			= self::load_tracking_data();
			$shipping_service_key		= $shipment_source_data['shipping_service'];
			$shipping_service_substr	= $tracking_data[ $shipping_service_key ]['name'];
			$order_date_substr		= $shipment_source_data['order_date'];

			$shipment_display_message	= $shipment_custom_message;
			$shipment_display_message 	= str_replace(self::TAG_SHIPMENT_ID, $tracking_id_substr, $shipment_display_message);
			$shipment_display_message 	= str_replace(self::TAG_SHIPMENT_SERVICE, $shipping_service_substr, $shipment_display_message);
			$shipment_display_message 	= str_replace(self::TAG_SHIPMENT_DATE, $order_date_substr, $shipment_display_message);
		}

		return $shipment_display_message;
	}
	
	public static function get_shipment_display_default_message( $shipment_result_array ) {
		$message  = '';
		if ( isset( $shipment_result_array['tracking_info'] ) ) {
			$message .= $shipment_result_array['message'];
			$sub_message_1 = ' To track shipment, please follow the shipment ID(s)';
			$sub_message_2 = '';

			foreach ( $shipment_result_array['tracking_info'] as $tracking_info ) {
				if( '' != trim($tracking_info['tracking_id']) ) {
					$sub_message_2 .= ' ';
					if( '' == $tracking_info['tracking_link'] ) {
						$sub_message_2 .= $tracking_info['tracking_id'].',';
					}
					else {
						$sub_message_2 .= ' <a href="'.$tracking_info['tracking_link'].'" target="_blank">'.$tracking_info['tracking_id'].'</a>,';
					}
				}
			}

			$sub_message_2 = rtrim( $sub_message_2, ',' );
			$trimmed_sub_message_2 = trim( $sub_message_2 );
			if( '' != $trimmed_sub_message_2 ) {
				$message .= $sub_message_1;
				$message .= $sub_message_2;
				$message .= '.';
			}
		}

		return $message;
	}
	
	public static function prepare_shipment_source_data( $order_id, $shipment_id_cs, $shipping_service, $order_date ){
		$shipment_source_data				= array();
		$shipment_source_data['shipment_id_cs']		= $shipment_id_cs;
		$shipment_source_data['shipping_service']	= $shipping_service;
		$shipment_source_data['order_date']		= $order_date;
		$shipment_source_data['order_id']		= $order_id;
		
		$order	=  ( WC()->version < '2.7.0' ) ? new WC_Order( $order_id ) : new wf_order( $order_id );
		if( !empty( $order->shipping_postcode ) ) {
			$shipment_source_data['shipping_postcode'] = $order->shipping_postcode;
		}
		else {
			$shipment_source_data['shipping_postcode'] = '';
		}
		
		return $shipment_source_data;
	}
	
	public static function get_shipping_service_key( $service_name ) {
		return sanitize_title( $service_name );
	}

	public static function update_tracking_data ( $order_id, 
													$shipment_id_cs, 
													$shipping_service, 
													$shipment_source_key, 
													$shipment_result_key, 
													$order_date='' ) {
		$shipment_source_data = get_post_meta( $order_id, $shipment_source_key, true);
		if( isset( $shipment_tracking_source['shipment_id_cs'] ) ) {
			$shipment_source_data['shipment_id_cs']		= $shipment_id_cs;
			$shipment_source_data['shipping_service']	= $shipping_service;
			$shipment_source_data['order_date']		= $order_date;
		}
		else {
			$shipment_source_data = self::prepare_shipment_source_data( $order_id, $shipment_id_cs, $shipping_service, $order_date );
		}

		update_post_meta( $order_id, $shipment_source_key, $shipment_source_data );

		$shipment_result	= self::get_shipment_result( $shipment_source_data );

		if ( null != $shipment_result && is_object( $shipment_result ) ) {
			$shipment_result_array = self::convert_shipment_result_obj_to_array ( $shipment_result );
			update_post_meta( $order_id, $shipment_result_key, $shipment_result_array );
			$message = self::get_shipment_display_message( $shipment_result_array, $shipment_source_data );
		}
		else {
			$message = __( 'Unable to update tracking info.', 'woocommerce-shipment-tracking' );
			update_post_meta( $order_id, $shipment_result_key, '' );
		}
		
		return $message;
	}

	public static function get_shipment_result( $shipment_source_data ) {
		WfTrackingFactory::init();
		$shipment_source_obj			= new ShipmentSource();
		$shipment_source_obj->shipment_id_cs	= isset ( $shipment_source_data['shipment_id_cs'] ) ? $shipment_source_data['shipment_id_cs'] : '';
		$shipment_source_obj->shipping_service	= isset ( $shipment_source_data['shipping_service'] ) ? $shipment_source_data['shipping_service'] : '';
		$shipment_source_obj->order_date	= isset ( $shipment_source_data['order_date'] ) ? $shipment_source_data['order_date'] : '';
		$shipment_source_obj->shipping_postcode	= isset ( $shipment_source_data['shipping_postcode'] ) ? $shipment_source_data['shipping_postcode'] : '';

		$wf_tracking 		= WfTrackingFactory::create( $shipment_source_obj );
		
		$shipment_result	= $wf_tracking->get_shipment_info();
		return $shipment_result;
	}
	
	public static function get_shipment_display_message( $shipment_result_array, $shipment_source_data ) {
		$shipment_custom_message	= self::get_shipment_custom_message($shipment_source_data);
		if( '' == trim($shipment_custom_message) ) {
			$message = self::get_shipment_display_default_message( $shipment_result_array );
		}
		else {
			$message = self::get_shipment_display_custom_message( $shipment_result_array, $shipment_source_data );
		}
		
		return $message;
	}
}
