<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class xa_fedex_image_upload {
	public function __construct() {
		$this->upload_document_wsdl_version = 19;

		$this->xa_init();
		add_action( 'wp_ajax_xa_fedex_upload_image', array($this,'xa_upload_image'), 10, 1 );
	}

	public function xa_upload_image(){
		$image_url	= isset($_POST['image']) ? $_POST['image'] : '';
		$image_id	= isset($_POST['image_id']) ? $_POST['image_id'] : '';
		if( empty($image_url) ){
			echo "Not able to get image, Please select a proper image";
			wp_die();
		}

		$response = $this->xa_get_response( $this->xa_get_fedex_image_upload_request($image_url, $image_id) );
		$result = $this->xa_get_result_text($response);
		wp_die( json_encode($result) );
	}

	private function xa_get_result_text($response){
		if ( $response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
			$result = array(
				'success' => true,
				'message' => 'Image uploaded successfully',
			);
		}else{
			$result_text = isset($response->Notifications->Message) ? $response->Notifications->Message : 'An unexpected error occurred, Please try again later';
			$result = array(
				'success' => false,
				'message' => $result_text,
			);
		}
		return $result;
	}

	private function xa_get_response( $request ){
		$wsdl = plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/UploadDocumentService_v' . $this->upload_document_wsdl_version. '.wsdl';
		
		$client = $this->wf_create_soap_client($wsdl);
		
		try{
			if( $this->soap_method == 'nusoap' ){
				$response = $client->call( 'UploadImages', array( 'UploadImagesRequest' => $request ) );
				$response = json_decode( json_encode( $result ), false );
			}else{
				$response = $client->UploadImages( $request );
			}
		}catch( exception $e ){
			return 'Uexpected error on image upload';
		}
		return $response;
	}

	private function is_soap_available(){
		if( extension_loaded( 'soap' ) ){
			return true;
		}
		return false;
	}

	private function xa_init(){
		$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		
		$this->production				= ( isset($this->settings[ 'production' ]) && ( $bool = $this->settings[ 'production' ] ) && $bool == 'yes' ) ? true : false;
		$this->api_key					= isset($this->settings[ 'api_key' ]) ? $this->settings[ 'api_key' ] : '';
		$this->api_pass					= isset($this->settings[ 'api_pass' ]) ? $this->settings[ 'api_pass' ] : '';
		$this->account_number			= isset($this->settings[ 'account_number' ]) ? $this->settings[ 'account_number' ] : '';
		$this->meter_number				= isset($this->settings[ 'meter_number' ]) ? $this->settings[ 'meter_number' ] : '';

		$this->soap_method = $this->is_soap_available() ? 'soap' : 'nusoap';
		if( $this->soap_method == 'nusoap' && !class_exists('nusoap_client') ){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nusoap/lib/nusoap.php';
		}
	}
/*
	private function xa_soap_call( $method, $request ){
		if( $this->soap_method == 'nusoap' ){
			$response = $client->call( $method, array( $request.'Request' => $request ) );
			$response = json_decode( json_encode( $result ), false );
		}else{
			$response = $client->__call( $request );
		}
		return $response;
	}*/

	private function wf_create_soap_client( $wsdl ){
		if( $this->soap_method=='nusoap' ){
			$soapclient = new nusoap_client( $wsdl, 'wsdl' );
		}else{
			$soapclient = new SoapClient( $wsdl, 
				array(
					'trace' =>	true
				)
			);
		}
		return $soapclient;
	}

	private function xa_get_fedex_image_upload_request( $image_url, $image_id='IMAGE_1' ){
		
		//Get the absolute path of the image file from url 
		$image_url = $_SERVER['DOCUMENT_ROOT'].parse_url($image_url,PHP_URL_PATH);
		
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'	  => $this->api_key,
				'Password' => $this->api_pass
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number
		);


		$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Upload Documents Request using PHP ***');
		$request['Version'] = array(
			'ServiceId' => 'cdus', 
			'Major' => '19', 
			'Intermediate' => '0', 
			'Minor' => '0'
		);
		$request['Images']['Id'] = $image_id;
		$request['Images']['Image'] = stream_get_contents(fopen($image_url, "r"));

		return $request;
	}
}
new xa_fedex_image_upload();