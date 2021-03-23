<?php

/**
 * USPS
 */
class WfTrackingUSPS extends WfTrackingAbstract {
	const USPS_DEFAULT_USER_ID 		= "570CYDTE1766";

	protected function get_api_tracking_status( $shipment_id, $api_uri ) {
		$api_tracking = new ApiTracking();
		$api_tracking->status 	= '';
		$api_tracking->error 	= '';

		$response 				= $this->wf_get_trackv2_response( $shipment_id, self::USPS_DEFAULT_USER_ID, $api_uri );
		
		if ( is_wp_error( $response ) ) {
		    $api_tracking->error = $response->get_error_message();
		}
		else if( isset( $response["body"] ) ) {
			$xml_response 		= simplexml_load_string( $response['body'] );
			$trackinfo_array 	= $xml_response->TrackInfo;
			
			foreach ( $trackinfo_array as $trackinfo ) {
				if( isset($trackinfo->Error ) ){
					$description 	= (string) $trackinfo->Error->Description;
					$error_number	= (string) $trackinfo->Error->Number;
					$api_tracking->error = $description.' ['.$error_number.']';
				}
				else {
					$api_tracking->status = (string) $trackinfo->TrackSummary;
				}
			}
		}

		return $api_tracking;
	}

	private function wf_get_trackv2_response( $shipment_id, $usps_user_id, $api_uri ) {
		$request = $this->wf_trackv2_request( $api_uri, $shipment_id, $usps_user_id );

		$response = wp_remote_post( $api_uri,
			array(
				'timeout'   => 70,
				'sslverify' => 0,
				'body'      => $request
			)
		);
 
		return $response;
	}
	
	private function wf_trackv2_request( $tracking_api_uri, $shipment_id, $usps_user_id ) {
		$xml_request 	= '<?xml version="1.0" encoding="UTF-8" ?>';
		$xml_request 	.= '<TrackRequest USERID="'.$usps_user_id.'">';
		
		//foreach ( $shipment_ids as $shipment_id ) {
			$xml_request .= '<TrackID ID="'.$shipment_id.'"></TrackID>';
		//}
		
		$xml_request 	.= '</TrackRequest>';
		$request 		= $tracking_api_uri.'&API=TrackV2&XML='.str_replace( array( "\n", "\r" ), '', $xml_request );
		
		return $request;
	}
}