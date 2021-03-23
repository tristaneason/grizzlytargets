<?php

/**
 * All tracking classes should extend this abstract tracking class
 */
abstract class WfTrackingAbstract {
	protected 	$shipment_source_obj;
	protected	$tracking_data;
	
	public function init( $shipment_source_obj ) {
		$this->set_shipment_source_obj( $shipment_source_obj );
		$this->load_tracking_data();
	}
	
	public function get_shipment_info( ) {
		$shipment_result 							= new ShipmentResult();
		$shipping_service							= $this->shipment_source_obj->shipping_service;
		if( isset( $this->tracking_data[ $shipping_service ]['api_url'] ) ) {
			$api_url									= $this->tracking_data[ $shipping_service ]['api_url'];
			$shipment_result->message 					= $this->get_tracking_message();
			$shipment_result->tracking_info_obj_array	= $this->get_tracking_info_obj_array();

			if( '' != $api_url ) {
				$shipment_result->tracking_info_api_obj_array	= $this->get_tracking_info_api_obj_array();
			}
		}

		return $shipment_result;
	}
	
	private function set_shipment_source_obj( $shipment_source_obj ) {
		$this->shipment_source_obj = $shipment_source_obj;
	}
	
	private function load_tracking_data( ) {
		$this->tracking_data = Ph_FedEx_Tracking_Util::load_tracking_data();
	}

	private function get_tracking_message( ) {
		$message = '';
		$message .= 'Your order was shipped';
		if( $this->shipment_source_obj->order_date ) {
			$message .= ' on '.$this->shipment_source_obj->order_date;
		}

		if( $this->shipment_source_obj->shipping_service ) {
			$shipping_service_name 	= $this->tracking_data[ $this->shipment_source_obj->shipping_service ]['name'];
			$temp_array 			= explode( ' (', $shipping_service_name, 2 );
			$shipping_service_name 	= $temp_array[0];
			$message 				.= ' via '.$shipping_service_name;
		}
		
		$message .= '.';

		return $message;
	}
	
	private function get_tracking_info_obj_array( ) {
		$shipment_id_cs		= $this->shipment_source_obj->shipment_id_cs;
		$shipment_ids 		= explode( ",", $shipment_id_cs );
		$shipping_service	= $this->shipment_source_obj->shipping_service;
		$shipping_postcode	= $this->shipment_source_obj->shipping_postcode;
		$tracking_url		= $this->tracking_data[ $shipping_service ]['tracking_url'];
		
		$tracking_info_obj_array = array();
		
		foreach ( $shipment_ids as $shipment_id ) {
			$tracking_info_obj							= new TrackingInfo();
			$tracking_info_obj->tracking_id				= $shipment_id;
			$tracking_info_obj->tracking_link			= $this->get_tracking_link( $tracking_url, $shipment_id, $shipping_postcode );
			$tracking_info_obj_array[] 					= $tracking_info_obj;
		}

		return $tracking_info_obj_array;
	} 

	private function get_tracking_info_api_obj_array( ) {
		$shipment_id_cs		= $this->shipment_source_obj->shipment_id_cs;
		$shipment_ids 		= explode( ",", $shipment_id_cs );
		$shipping_service	= $this->shipment_source_obj->shipping_service;
		$shipping_postcode	= $this->shipment_source_obj->shipping_postcode;
		$api_uri			= $this->tracking_data[ $shipping_service ]['api_url'];
		$tracking_url		= $this->tracking_data[ $shipping_service ]['tracking_url'];
		
		$tracking_info_api_obj_array = array();

 		if( '' != trim($api_uri) ) {
			foreach ( $shipment_ids as $shipment_id ) {
				$tracking_info_api_obj						= new TrackingInfoApi();
				$tracking_info_api_obj->tracking_id			= $shipment_id;
				$tracking_info_api_obj->tracking_link		= $this->get_tracking_link( $tracking_url, $shipment_id, $shipping_postcode );
				$api_tracking								= $this->get_api_tracking_status( $shipment_id, $api_uri );
				$tracking_info_api_obj->api_tracking_status	= $api_tracking->status;
				$tracking_info_api_obj->api_tracking_error	= $api_tracking->error;
				$tracking_info_api_obj_array[] 				= $tracking_info_api_obj;
			}
		}

		return $tracking_info_api_obj_array;
	}

	private function get_tracking_link( $tracking_url, $shipment_id, $shipping_postcode ) {
		$tracking_url	= trim( $tracking_url );
		$tracking_link 	= '';
		if( '' != $tracking_url ){
			if( stripos($tracking_url, Ph_FedEx_Tracking_Util::TAG_SHIPMENT_ID ) ) {
				$tracking_link 	= $tracking_url;
				$tracking_link 	= str_replace( Ph_FedEx_Tracking_Util::TAG_SHIPMENT_ID, $shipment_id, $tracking_link );
			}
			else {
				$tracking_link 	= $tracking_url.$shipment_id;
			}
			
			if( stripos($tracking_link, Ph_FedEx_Tracking_Util::TAG_SHIPPING_POST_CODE ) ) {
				$shipping_postcode = preg_replace('/\s+/', '', $shipping_postcode);
				$tracking_link 	= str_replace( Ph_FedEx_Tracking_Util::TAG_SHIPPING_POST_CODE, $shipping_postcode, $tracking_link );
			}
		}

		return $tracking_link;
	}

	/**
	 * Return tracking api status as an Object of class ApiTracking.
	 * Abstract function, so, must inherit class and override.
	 */
	abstract protected function get_api_tracking_status( $shipment_id, $api_uri ) /* { return new ApiTracking() } */;
}
