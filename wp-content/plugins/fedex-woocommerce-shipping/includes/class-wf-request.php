<?php
/*
 * Purpose of Ttis file is for writing common API requests related
 * functions which will be used across the plugin code. 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wfFedexRequest{
	public function __construct( $settings = null ) {
		$this->settings = $settings;
		$this->id                               = WF_Fedex_ID;
		$this->rateservice_version              = 31;
		$this->ship_service_version             = 28;
		$this->addressvalidationservice_version = 4;
		$this->init();		
	}
	
	private function init() {
		if( empty($this->settings) ) {
			$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		}
		
		
		$this->account_number  				 = isset ( $this->settings[ 'account_number' ] ) ? $this->settings[ 'account_number' ] : '';
		$this->charges_payment_type 		= isset ( $this->settings['charges_payment_type'] ) ? $this->settings['charges_payment_type'] : '';
		$this->shipping_payor_acc_no		 = isset ( $this->settings['shipping_payor_acc_no'] ) ? $this->settings['shipping_payor_acc_no'] : '';
		$this->shipping_payor_cname			 = isset ( $this->settings['shipping_payor_cname'] ) ? $this->settings['shipping_payor_cname'] : '';
		$this->shipp_payor_company		 = isset ( $this->settings['shipp_payor_company'] ) ? $this->settings['shipp_payor_company'] : '';
		$this->shipping_payor_phone			 = isset ( $this->settings['shipping_payor_phone'] ) ? $this->settings['shipping_payor_phone'] : '';
		$this->shipping_payor_email			 = isset ( $this->settings['shipping_payor_email'] ) ? $this->settings['shipping_payor_email'] : '';
		$this->shipp_payor_address1		 = isset ( $this->settings['shipp_payor_address1'] ) ? $this->settings['shipp_payor_address1'] : '';
		$this->shipp_payor_address2		 = isset ( $this->settings['shipp_payor_address2'] ) ? $this->settings['shipp_payor_address2'] : '';
		$this->shipping_payor_city			 = isset ( $this->settings['shipping_payor_city'] ) ? $this->settings['shipping_payor_city'] : '';
		$this->shipping_payor_state			 = isset ( $this->settings['shipping_payor_state'] ) ? $this->settings['shipping_payor_state'] : '';
		$this->shipping_payor_zip	 = isset ( $this->settings['shipping_payor_zip'] ) ? $this->settings['shipping_payor_zip'] : '';
		$this->shipp_payor_country		 = isset ( $this->settings['shipp_payor_country'] ) ? $this->settings['shipp_payor_country'] : '';

		$this->shipper_person_name 	= isset($this->settings[ 'shipper_person_name' ]) ? $this->settings[ 'shipper_person_name' ] : '';
		$this->shipper_company_name	= isset($this->settings[ 'shipper_company_name' ])  ? $this->settings[ 'shipper_company_name' ] : '';
		$this->shipper_street      	= isset($this->settings[ 'frt_shipper_street' ] )  ? $this->settings[ 'frt_shipper_street' ] : '';
		$this->shipper_street_2    	= isset($this->settings[ 'shipper_street_2']) ? $this->settings[ 'shipper_street_2' ] : '';
		$this->shipper_city        	= isset($this->settings[ 'freight_shipper_city' ]) ? $this->settings[ 'freight_shipper_city' ] : '';
		$this->origin          		= isset($this->settings[ 'origin' ]) ? str_replace( ' ', '', strtoupper( $this->settings[ 'origin' ] ) ) : '';

		$this->shipper_phone_number	= $this->settings[ 'shipper_phone_number' ];
		$this->shipper_email 		= isset ( $this->settings['shipper_email'] ) ? $this->settings['shipper_email'] : '';

		$this->package = array();	

		$this->acc_no = $this->charges_payment_type=='SENDER' ? $this->account_number : $this->shipping_payor_acc_no;
		$this->freight_acc_number	= isset($this->settings[ 'freight_number']) ? $this->settings[ 'freight_number'] : null;
		$this->freight_enabled 		= ( $bool = $this->settings[ 'freight_enabled'] ) && $bool == 'yes' ? true : false;

		$this->set_origin_country_state();
	}

	private function set_origin_country_state(){
		$origin_country_state 		= isset( $this->settings['origin_country'] ) ? $this->settings['origin_country'] : '';
		if ( strstr( $origin_country_state, ':' ) ) :
			// WF: Following strict php standards.
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_country 			= current($origin_country_state_array);
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_state   				= end($origin_country_state_array);
		else :
			$origin_country = $origin_country_state;
			$origin_state   = '';
		endif;

		$this->origin_country  	= apply_filters( 'woocommerce_fedex_origin_country_code', $origin_country );
		$this->origin_state 	= !empty($origin_state) ? $origin_state : ( isset($this->settings[ 'freight_shipper_state' ]) ? $this->settings[ 'freight_shipper_state' ] : '' );
	}

	public function set_package($package){
		$this->package = $package;
	}
	public function get_shipping_charges_payment( $request_type ){
		$from_address = array(
			'name' 		=> $this->shipper_person_name,
			'company' 	=> $this->shipper_company_name,
			'phone' 	=> $this->shipper_phone_number,
			'address_1' => $this->shipper_street,
			'address_2' => $this->shipper_street_2,
			'city' 		=> $this->shipper_city,
			'state' 	=> $this->origin_state,
			'country' 	=> $this->origin_country,
			'postcode' 	=> $this->origin,
		);
		$from_address =  apply_filters( 'wf_filter_label_from_address', $from_address , $this->package );


		$return	=	array();
		
		$return['PaymentType']					=	$this->charges_payment_type;
		if( $this->charges_payment_type=='SENDER' || $request_type == 'freight' ){
			$return['Payor']['ResponsibleParty']=array(
				'AccountNumber'	=>	$this->account_number,
				'Contact'		=>	array(
					'PersonName'	=> $from_address['name'],
					'CompanyName'	=> $from_address['company'],
					'PhoneNumber'	=> $from_address['phone'],
				),
				'Address'		=>	array(
					'StreetLines'         => array( strtoupper( $from_address['address_1'] ), strtoupper( $from_address['address_2'] ) ),
					'City'                => strtoupper( $from_address['city'] ),
					'StateOrProvinceCode' => strtoupper( $from_address['state'] ),
					'PostalCode'          => strtoupper( $from_address['postcode'] ),
					'CountryCode'         => strtoupper( $from_address['country'] ),
				)
			);
			if( $request_type == 'freight' && $this->freight_enabled && ! empty($this->freight_acc_number) )
				$return['Payor']['ResponsibleParty']['AccountNumber'] = $this->freight_acc_number;
		}else{
			$return['Payor']['ResponsibleParty'] = $this->get_alternate_address();
		}
		
		return $return;
	}
	public function get_charges_payment_type(){
		return $this->charges_payment_type;
	}

	public function get_alternate_address(){
		if( $this->charges_payment_type=='SENDER' ){
			return false;
		}
		$return = array(
			'AccountNumber'	=>	$this->acc_no,
			'Contact'		=>	array(
				'PersonName'	=> $this->shipping_payor_cname,
				'CompanyName'	=> $this->shipp_payor_company,
				'PhoneNumber'	=> $this->shipping_payor_phone,
				'EMailAddress'	=> $this->shipping_payor_email,
			),
			'Address'		=>	array(
				'StreetLines'			=>	array($this->shipp_payor_address1,$this->shipp_payor_address2),
				//'StreetLines'			=>	$this->shipp_payor_address2,
				'City'					=>	$this->shipping_payor_city,
				'StateOrProvinceCode'	=>	$this->shipping_payor_state,
				'PostalCode'			=>	$this->shipping_payor_zip,
				'CountryCode'			=>	$this->shipp_payor_country,
			)
		);
		return $return;
	}
}