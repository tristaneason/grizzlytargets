<?php

/**
 * Canada Post
 */
class WfTrackingCanadaPost extends WfTrackingAbstract {
	
	const CP_DEFAULT_USER_ID 		= "6e93d53968881714";
	const CP_DEFAULT_PASSWD 		= "0bfa9fcb9853d1f51ee57a";

	protected function get_api_tracking_status( $shipment_id, $api_uri ) { 
		$api_tracking = $this->wf_cp_tracking_response( $shipment_id, $api_uri );
		return $api_tracking;
	}

	private function wf_cp_tracking_response( $shipment_id, $api_uri ) {

		$api_tracking 			= new ApiTracking( );
		$api_tracking->status 	= '';
		$api_tracking->error 	= '';
	
		$endpoint = str_replace( "rs/" , "", $api_uri ) . 'vis/track/pin/'.$shipment_id.'/summary';
		
		$response	= wp_remote_post( $endpoint,
			array(
				'method'	=> 'GET',
				'timeout'	=> 70, 
				'sslverify'	=> 0,
				'headers'	=> $this->wf_get_request_header('application/vnd.cpc.track+xml','application/vnd.cpc.track+xml')					
			)
		);
		
		if ( ! empty( $response['body'] ) ) {
			$response = $response['body'];
		} else {
			return 	$api_tracking;
		}

		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($response);

		if ( !$xml ) {
			$api_tracking->error .= 'Failed loading XML' . "</br>";

			foreach( libxml_get_errors() as $error ) {
				$api_tracking->error .=  $error->message . "</br>";
			}
		} else {
			$trackingSummary = $xml->children( 'http://www.canadapost.ca/ws/track' );

			if ( $trackingSummary->{'pin-summary'} ) {

				foreach ( $trackingSummary as $pinSummary ) {
					$api_tracking->status .= 'PIN Number: ' . $pinSummary->{'pin'} . "</br>";
					$api_tracking->status .=  'Mailed On Date: ' . $pinSummary->{'event-date-time'} . "</br>";
					$api_tracking->status .=  'Event Description: ' . $pinSummary->{'event-description'} . "</br></br>";
				}
			} else {
				$messages = $xml->children( 'http://www.canadapost.ca/ws/messages' );
				
				if( !empty($messages) ){
					foreach ( $messages as $message ) {
						$api_tracking->error .=  'Error Code: ' . $message->code . "</br>";
						$api_tracking->error .=  'Error Msg: ' . $message->description . "</br></br>";
					}
				}
				else{
					$messages = $xml->children('http://www.canadapost.ca/ws/track');
					
					if(!empty($messages)){
						foreach ( $messages as $message ) {
							$api_tracking->error .=  'Error Code: ' . $message->code . "</br>";
							$api_tracking->error .=  'Error Msg: ' . $message->description . "</br></br>";
						}
					}
				}
			}
		}

		return 	$api_tracking;
	}
	
	private function wf_get_request_header( $accept, $content_type ) {
	   return array(
			'Accept'          => $accept,
			'Content-Type'    => $content_type,
			'Authorization'   => 'Basic ' . base64_encode( self::CP_DEFAULT_USER_ID . ':' . self::CP_DEFAULT_PASSWD ),
			'Accept-language' => 'en-CA'
		);
    }
}
