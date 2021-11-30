<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wf_fedex_woocommerce_shipping_admin_helper  {
	private $service_code;

	public $address_validation_contries=array('VI','VG','BR','MX','BS','KY','AR','AW','BB','BM','CL','CR','GT','JM','NL','DE','ES','GB','CH','AT','SE','EE','FI','GR','NO','PT','ZA','PA','TT','UY','VE','CO','FR','PE','SG','IT','BE','CZ','DK','CA','AU','NZ','HK','MY','US');
	
	public $fedexBoxCountries 	= array( 'CA', 'CO', 'BR' );
	public $fedexBox 			= array( 'FEDEX_SMALL_BOX','FEDEX_SMALL_BOX:2','FEDEX_MEDIUM_BOX','FEDEX_MEDIUM_BOX:2','FEDEX_LARGE_BOX','FEDEX_LARGE_BOX:2','FEDEX_EXTRA_LARGE_BOX','FEDEX_EXTRA_LARGE_BOX:2' );

	public $standard_boxes = array( 'FEDEX_SMALL_BOX','FEDEX_SMALL_BOX:2','FEDEX_MEDIUM_BOX','FEDEX_MEDIUM_BOX:2','FEDEX_LARGE_BOX','FEDEX_LARGE_BOX:2','FEDEX_EXTRA_LARGE_BOX','FEDEX_EXTRA_LARGE_BOX:2','FEDEX_PAK','FEDEX_ENVELOPE','FEDEX_10KG_BOX','FEDEX_25KG_BOX','FEDEX_BOX','FEDEX_TUBE');

	public $country_without_cities = array('SG');

	public $splCharToFind 		= array('é','á','ä','Ä','Ã','ö','Ö','ü','Ü','ß','б','в','г','д','ё','ж','з','и','й','к','л','м','н','п','т','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я','А','Б','Г','Д','Ё','Ж','З','И','Й','Л','П','У','Ф','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','№');
	public $splCharToReplace 	= array('e','a','a','A','A','o','O','u','U','B','b','v','g','d','io','zh','z','i','y','k','l','m','n','p','t','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya','A','B','G','D','Io','Zh','Z','I','Y','L','P','U','F','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya','No.');

	//PDS-179	
	public $prioritizedSignatureOption 	= array( 5=>'ADULT',4=>'DIRECT',3=>'INDIRECT',2=>'SERVICE_DEFAULT',1=>'NO_SIGNATURE_REQUIRED',0=>'');

	public function __construct() {
		$this->id                                   = WF_Fedex_ID;
		$this->rateservice_version                  = 31;
		$this->ship_service_version                 = 28;
		$this->pickup_service_version               = 23;
		$this->addressvalidationservice_version     = 4;
		$this->tracking_ids                         = '';
		$this->init();
	}

	private function init() {		
		$this->settings = apply_filters( 'xa_fedex_settings',get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null ) );
		$this->is_international = false;
		$this->fed_req	=	new	wfFedexRequest($this->settings);

		$this->soap_method = $this->is_soap_available() ? 'soap' : 'nusoap';
		if( $this->soap_method == 'nusoap' && !class_exists('nusoap_client') ){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nusoap/lib/nusoap.php';
		}
		
		//TODO:
		$this->weight_dimensions_manual = isset($this->settings['manual_wgt_dimensions']) ? $this->settings['manual_wgt_dimensions'] : 'no';
		
		$this->add_trackingpin_shipmentid = $this->settings['tracking_shipmentid'];
		
		
		$this->origin          = str_replace( ' ', '', strtoupper( $this->settings[ 'origin' ] ) ); //Post code

		$this->account_number  = $this->settings[ 'account_number' ];
		$this->meter_number    = $this->settings[ 'meter_number' ];
		$this->smartpost_hub   = $this->settings[ 'smartpost_hub' ];
		
		$this->indicia = isset($this->settings[ 'indicia']) ? $this->settings[ 'indicia'] : 'PARCEL_SELECT';

		$this->api_key         			= $this->settings[ 'api_key' ];
		$this->api_pass        			= $this->settings[ 'api_pass' ];
		$this->ship_from_address 		= isset($this->settings['ship_from_address'])? $this->settings['ship_from_address'] : 'origin_address';
		$this->production      			= ( $bool = $this->settings[ 'production' ] ) && $bool == 'yes' ? true : false;
		$this->debug           			= ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ? true : false;
		$this->insure_contents 			= ( $bool = $this->settings[ 'insure_contents' ] ) && $bool == 'yes' ? true : false;
		$this->insure_contents 			= ($this->ship_from_address == 'origin_address') ? $this->insure_contents : false;
		$this->request_type    			= $this->settings[ 'request_type'];
		
		$this->packing_method 			= $this->settings[ 'packing_method'];
		$this->boxes           			= $this->settings[ 'boxes'];
		$this->custom_services			= $this->settings[ 'services'];
		$this->offer_rates     			= $this->settings[ 'offer_rates'];
		$this->residential     			= ( $bool = $this->settings[ 'residential'] ) && $bool == 'yes' ? true : false;
		$this->freight_enabled 			= ( $bool = isset( $this->settings[ 'freight_enabled'] ) ? $this->settings[ 'freight_enabled'] : 'no' ) && $bool == 'yes' ? true : false;
		$this->saturday_pickup			= ( $bool = isset($this->settings[ 'saturday_pickup' ] ) ? $this->settings[ 'saturday_pickup' ] : 'no') && $bool == 'yes' ? true : false;
		$this->fedex_one_rate  			= ( $bool = isset($this->settings[ 'fedex_one_rate'] ) ? $this->settings[ 'fedex_one_rate'] : 'no') && $bool == 'yes' ? true : false;
		$this->fedex_one_rate_package_ids = array(
			'FEDEX_SMALL_BOX',
			'FEDEX_MEDIUM_BOX',
			'FEDEX_LARGE_BOX',
			'FEDEX_EXTRA_LARGE_BOX',
			'FEDEX_PAK',
			'FEDEX_ENVELOPE',
		);
		
		$this->box_max_weight				=	$this->settings[ 'box_max_weight'];
		$this->weight_pack_process			=	$this->settings[ 'weight_pack_process'];
		
		if(isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN'){
			$this->dimension_unit 			= 	'in';
			$this->weight_unit 				= 	'lbs';
			$this->labelapi_dimension_unit	=	'IN';
			$this->labelapi_weight_unit 	=	'LB';
			$this->default_boxes                    = include( 'data-wf-box-sizes.php' );
		}else{
			$this->dimension_unit 			= 	'cm';
			$this->weight_unit 				= 	'kg';
			$this->labelapi_dimension_unit	=	'CM';
			$this->labelapi_weight_unit 	=	'KG';
			$this->default_boxes                    = include( 'data-wf-box-sizes-cm.php' );
		}
			
		$this->freight_class               = $this->settings[ 'freight_class' ];
		$this->freight_number              = $this->settings[ 'freight_number'];
		$this->freight_bill_street         = $this->settings[ 'freight_bill_street' ];
		$this->freight_billing_street_2    = $this->settings[ 'billing_street_2' ];
		$this->freight_billing_city        = $this->settings[ 'freight_billing_city' ];
		$this->freight_billing_state       = $this->settings[ 'freight_billing_state' ];
		$this->freight_billing_postcode    = $this->settings[ 'billing_postcode' ];
		$this->freight_billing_country     = $this->settings[ 'billing_country' ];
		
		$this->freight_shipper_person_name = $this->settings[ 'shipper_person_name' ];
		$this->freight_shipper_company_name= $this->settings[ 'shipper_company_name' ];
		$this->freight_shipper_phone_number= $this->settings[ 'shipper_phone_number' ];
		
		$this->frt_shipper_street      	   = $this->settings[ 'frt_shipper_street' ];
		$this->freight_shipper_street_2    = $this->settings[ 'shipper_street_2'];
		$this->freight_shipper_city        = $this->settings[ 'freight_shipper_city' ];
		$this->freight_shipper_residential = ( $bool = $this->settings[ 'shipper_residential'] ) && $bool == 'yes' ? true : false;
		$this->freight_class               = str_replace( array( 'CLASS_', '.' ), array( '', '_' ), $this->freight_class );
		
		$this->freight_document_type 		= ( isset($this->settings['freight_document_type']) && !empty($this->settings['freight_document_type']) ) ? $this->settings['freight_document_type'] : 'VICS_BILL_OF_LADING';

		$this->output_format				= $this->settings['output_format'];
		$this->image_type					= $this->settings['image_type'];
		$this->commercial_invoice 			= (isset($this->settings['commercial_invoice']) && ($this->settings['commercial_invoice'] == 'yes')) ? true : false;
        //USMCA Certificate
		$this->usmca_certificate 			= (isset($this->settings['usmca_certificate']) && ($this->settings['usmca_certificate'] == 'yes')) ? true : false;
		$this->usmca_ci_certificate_of_origin = (isset($this->settings['usmca_ci_certificate_of_origin']) && ($this->settings['usmca_ci_certificate_of_origin'] == 'yes')) ? true : false;
		$this->blanket_begin_period		= ( isset( $this->settings['blanket_begin_period'] ) && !empty($this->settings['blanket_begin_period']) ) ? $this->settings['blanket_begin_period'] : '';
		$this->blanket_end_period		= ( isset( $this->settings['blanket_end_period'] ) && !empty($this->settings['blanket_end_period']) ) ? $this->settings['blanket_end_period'] : '';
		$this->certifier_specification 	= ( isset($this->settings['certifier_specification']) && !empty($this->settings['certifier_specification'])) ? $this->settings['certifier_specification'] : 'IMPORTER' ;
		$this->importer_specification 	= ( isset($this->settings['importer_specification']) && !empty($this->settings['importer_specification'])) ? $this->settings['importer_specification'] : 'UNKNOWN' ;
		$this->producer_specification 	= ( isset($this->settings['producer_specification']) && !empty($this->settings['producer_specification'])) ? $this->settings['producer_specification'] : 'SAME_AS_EXPORTER' ;
		$this->etd_label 					= (isset($this->settings['etd_label']) && ($this->settings['etd_label'] == 'yes')) ? true : false;
		$this->cod_collection_type 			= isset($this->settings[ 'cod_collection_type']) ? $this->settings[ 'cod_collection_type'] : 'ANY';

		
		$this->charges_payment_type 		= isset ( $this->settings['charges_payment_type'] ) ? $this->settings['charges_payment_type'] : '';
		$this->shipping_payor_acc_no		= isset ( $this->settings['shipping_payor_acc_no'] ) ? $this->settings['shipping_payor_acc_no'] : '';
		$this->shipping_payor_cname			= isset ( $this->settings['shipping_payor_cname'] ) ? $this->settings['shipping_payor_cname'] : '';
		$this->shipp_payor_company		 	= isset ( $this->settings['shipp_payor_company'] ) ? $this->settings['shipp_payor_company'] : '';
		$this->shipping_payor_phone			= isset ( $this->settings['shipping_payor_phone'] ) ? $this->settings['shipping_payor_phone'] : '';
		$this->shipping_payor_email			= isset ( $this->settings['shipping_payor_email'] ) ? $this->settings['shipping_payor_email'] : '';
		$this->shipp_payor_address1		 	= isset ( $this->settings['shipp_payor_address1'] ) ? $this->settings['shipp_payor_address1'] : '';
		$this->shipp_payor_address2		 	= isset ( $this->settings['shipp_payor_address2'] ) ? $this->settings['shipp_payor_address2'] : '';
		$this->shipping_payor_city			= isset ( $this->settings['shipping_payor_city'] ) ? $this->settings['shipping_payor_city'] : '';
		$this->shipping_payor_state			= isset ( $this->settings['shipping_payor_state'] ) ? $this->settings['shipping_payor_state'] : '';
		$this->shipping_payor_zip	 		= isset ( $this->settings['shipping_payor_zip'] ) ? $this->settings['shipping_payor_zip'] : '';
		$this->shipp_payor_country		 	= isset ( $this->settings['shipp_payor_country'] ) ? $this->settings['shipp_payor_country'] : '';
		
		$this->customs_duties_payer 		= isset ( $this->settings['customs_duties_payer'] ) ? $this->settings['customs_duties_payer'] : '';
		$this->customs_ship_purpose 		= isset ( $this->settings['customs_ship_purpose'] ) ? $this->settings['customs_ship_purpose'] : '';
		
		$this->email_notification 	= isset ( $this->settings['email_notification'] ) ? $this->settings['email_notification'] : false;
		$this->shipper_email 		= isset ( $this->settings['shipper_email'] ) ? $this->settings['shipper_email'] : '';
		$this->signature_option 	= isset ( $this->settings['signature_option'] ) ? $this->settings['signature_option'] : '';
		$this->signature_option 	= array_search($this->signature_option, $this->prioritizedSignatureOption);
		$this->exclude_tax		 	= isset ( $this->settings['exclude_tax'] ) && $this->settings['exclude_tax'] == 'yes' ? true : false;
		$this->timezone_offset 		= !empty($this->settings['timezone_offset']) ? intval($this->settings['timezone_offset']) * 60 : 0;
		$this->is_dry_ice_enabled 	= isset( $this->settings['dry_ice_enabled'] ) && $this->settings['dry_ice_enabled'] == 'yes' ? true : false;
		$this->dropoff_type 		= isset($this->settings['dropoff_type']) && !empty($this->settings['dropoff_type']) ? $this->settings['dropoff_type'] : 'REGULAR_PICKUP';
		
		$this->dry_ice_shipment 	= false;
		$this->customtotal 			= 0;
		$this->dry_ice_total_weight = 0;

		$this->broker_acc_no 	= isset( $this->settings['broker_acc_no'] ) ? $this->settings['broker_acc_no'] : '';
		$this->broker_name 		= isset( $this->settings['broker_name'] ) ? $this->settings['broker_name'] : '';
		$this->broker_company	= isset( $this->settings['broker_company'] ) ? $this->settings['broker_company'] : '';
		$this->broker_phone 	= isset( $this->settings['broker_phone'] ) ? $this->settings['broker_phone'] : '';
		$this->broker_email 	= isset( $this->settings['broker_email'] ) ? $this->settings['broker_email'] : '';
		$this->broker_address 	= isset( $this->settings['broker_address'] ) ? $this->settings['broker_address'] : '';
		$this->broker_city 		= isset( $this->settings['broker_city'] ) ? $this->settings['broker_city'] : '';
		$this->broker_state 	= isset( $this->settings['broker_state'] ) ? $this->settings['broker_state'] : '';
		$this->broker_zipcode 	= isset( $this->settings['broker_zipcode'] ) ? $this->settings['broker_zipcode'] : '';
		$this->broker_country 	= isset( $this->settings['broker_country'] ) ? $this->settings['broker_country'] : '';
		
		$this->tin_number 	= isset( $this->settings['tin_number'] ) ? $this->settings['tin_number'] : '';

		// Alternative Return Address
		$this->alternate_return_address 	= ( isset( $this->settings['alternate_return_address'] ) && !empty( $this->settings['alternate_return_address'] ) && $this->settings['alternate_return_address'] == 'yes' ) ? true : false;
		$this->billing_as_alternate_return_address 	= ( isset( $this->settings['billing_as_alternate_return_address'] ) && !empty( $this->settings['billing_as_alternate_return_address'] ) && $this->settings['billing_as_alternate_return_address'] == 'yes' ) ? true : false;

		$this->alt_return_streetline 	= ( isset($this->settings['alt_return_streetline']) && !empty($this->settings['alt_return_streetline']) ) ?  $this->settings['alt_return_streetline'] : '';
		$this->alt_return_city 			= ( isset($this->settings['alt_return_city']) && !empty($this->settings['alt_return_city']) ) ?  $this->settings['alt_return_city'] : '';
		$this->alt_return_postcode 		= ( isset($this->settings['alt_return_postcode']) && !empty($this->settings['alt_return_postcode']) ) ?  $this->settings['alt_return_postcode'] : '';

		//Hazmat Packaging : 4G - Fiberboard Box
		$this->hazmat_package_enabled 	= ( isset($this->settings['hazmat_enabled']) && !empty($this->settings['hazmat_enabled']) && $this->settings['hazmat_enabled'] == 'yes' ) ? true : false;
		$this->hazmat_package_type		= ( isset($this->settings['hp_packaging_type']) && !empty($this->settings['hp_packaging_type']) ) ?  $this->settings['hp_packaging_type'] : '4';
		$this->hazmat_package_material 	= ( isset($this->settings['hp_packaging_material']) && !empty($this->settings['hp_packaging_material']) ) ?  $this->settings['hp_packaging_material'] : 'G';

		//Reason for Return
		$this->int_return_label_reason 	= ( isset($this->settings['int_return_label_reason']) && !empty($this->settings['int_return_label_reason']) ) ? $this->settings['int_return_label_reason'] : 'TRIAL';
		$this->int_return_label_desc 	= ( isset($this->settings['int_return_label_desc']) && !empty($this->settings['int_return_label_desc']) ) ? $this->settings['int_return_label_desc'] : '';

		$this->wc_store_currency		= get_woocommerce_currency();
		$this->fedex_currency			= ! empty($this->settings['fedex_currency']) ? $this->settings['fedex_currency'] : $this->wc_store_currency;
		$this->fedex_conversion_rate	= ! empty($this->settings['fedex_conversion_rate']) ? (float) $this->settings['fedex_conversion_rate'] : 1;

		$this->remove_special_char =  ( isset( $this->settings['remove_special_char_product'] ) && !empty($this->settings['remove_special_char_product']) && $this->settings['remove_special_char_product'] == 'yes' ) ? true : false;

		$this->duties_and_taxes_rate 	= ( isset($this->settings['fedex_duties_and_taxes_rate']) && !empty($this->settings['fedex_duties_and_taxes_rate']) && $this->settings['fedex_duties_and_taxes_rate'] == 'yes' ) ? true : false;
   
		$this->discounted_price 		= ( isset($this->settings['discounted_price']) && !empty($this->settings['discounted_price']) && $this->settings['discounted_price'] == 'yes' ) ? true : false;
		$this->invoice_commodity_value 	= ( isset($this->settings['invoice_commodity_value']) && !empty($this->settings['invoice_commodity_value'])) ? $this->settings['invoice_commodity_value'] : '' ;

		$this->commercial_invoice_shipping = ( isset($this->settings['commercial_invoice_shipping']) && !empty($this->settings['commercial_invoice_shipping']) && $this->settings['commercial_invoice_shipping'] == 'yes' ) ? true : false;
		$this->commercial_invoice_order_currency = ( isset($this->settings['commercial_invoice_order_currency']) && !empty($this->settings['commercial_invoice_order_currency']) && $this->settings['commercial_invoice_order_currency'] == 'yes' ) ? true : false;

		$this->doc_tab_content 		= ( isset($this->settings['doc_tab_content']) && !empty($this->settings['doc_tab_content']) && $this->settings['doc_tab_content'] == 'yes' ) ? true : false;
		$this->doc_tab_orientation 	= ( isset($this->settings['doc_tab_orientation']) && !empty($this->settings['doc_tab_orientation']) ) ? $this->settings['doc_tab_orientation'] : 'TOP_EDGE_OF_TEXT_FIRST';
		$this->global_hs_code 		= ( isset($this->settings['global_hs_code']) && !empty($this->settings['global_hs_code']) ) ? $this->settings['global_hs_code'] : '';
		$this->special_instructions = ( isset($this->settings['special_instructions']) && !empty($this->settings['special_instructions']) ) ? $this->settings['special_instructions'] : '';
		$this->payment_terms 		= ( isset($this->settings['payment_terms']) && !empty($this->settings['payment_terms']) ) ? $this->settings['payment_terms'] : '';

		$this->csb5_shipments 	= ( isset($this->settings['csb5_shipments']) && !empty($this->settings['csb5_shipments']) && $this->settings['csb5_shipments'] == 'yes' ) ? true : false;
		$this->ad_code 			= ( isset($this->settings['ad_code']) && !empty($this->settings['ad_code']) ) ? $this->settings['ad_code'] : '';
		$this->gst_shipment 	= ( isset($this->settings['gst_shipment']) && !empty($this->settings['gst_shipment']) ) ? $this->settings['gst_shipment'] : 'N';
		$this->csb_termsofsale 	= ( isset($this->settings['csb_termsofsale']) && !empty($this->settings['csb_termsofsale']) ) ? $this->settings['csb_termsofsale'] : 'FOB';
		$this->under_bond 		= ( isset($this->settings['under_bond']) && !empty($this->settings['under_bond']) ) ? $this->settings['under_bond'] : 'U';
		$this->meis_shipment 	= ( isset($this->settings['meis_shipment']) && !empty($this->settings['meis_shipment']) ) ? $this->settings['meis_shipment'] : 'M';
		$this->saturday_delivery_label 	= ( isset($this->settings['saturday_delivery_label']) && !empty($this->settings['saturday_delivery_label']) && $this->settings['saturday_delivery_label'] == 'yes' ) ? true : false;
		$this->pro_forma_invoice 	= ( isset($this->settings['ph_pro_forma_invoice']) && !empty($this->settings['ph_pro_forma_invoice']) && $this->settings['ph_pro_forma_invoice'] == 'yes' ) ? true : false;
		$this->document_content 	= ( isset($this->settings['document_content']) && !empty($this->settings['document_content']) ? $this->settings['document_content'] : '');
		$this->third_party_acc_no 	= ( isset($this->settings['third_party_acc_no']) && !empty($this->settings['third_party_acc_no']) ? $this->settings['third_party_acc_no'] : '');
		$this->shipment_comments 	= ( isset($this->settings['shipment_comments']) && !empty($this->settings['shipment_comments']) ? $this->settings['shipment_comments'] : '');
		$this->label_maskable_type 	= ( isset($this->settings['label_maskable_type'	]) && !empty($this->settings['label_maskable_type'	]) ) ? $this->settings['label_maskable_type'	] : array();
		$this->home_delivery_premium    	= (isset($this->settings['home_delivery_premium']) && ($this->settings['home_delivery_premium'] == 'yes')) ? true : false;
		$this->home_delivery_premium_type 	=	( isset($this->settings['home_delivery_premium_type']) && !empty($this->settings['home_delivery_premium_type']) ) ? $this->settings['home_delivery_premium_type'] : '';
		$this->default_dom_service 			=	( isset($this->settings['default_dom_service']) && !empty($this->settings['default_dom_service']) ) ? $this->settings['default_dom_service'] : '';

		if( $this->saturday_pickup ) {

			$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
		} else {
			$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' );
		}

		$this->working_days 	= ( isset($this->settings['fedex_working_days']) && !empty($this->settings['fedex_working_days']) ) ? $this->settings['fedex_working_days'] : $working_days;

		$this->set_origin_country_state();
		// Insure contents requires matching currency to country
		switch ( $this->origin_country ) {
			case 'US' :
				if ( 'USD' !== $this->fedex_currency ) {
					$this->insure_contents = false;
				}
			break;
		case 'CA' :
				if ( 'CAD' !== $this->fedex_currency ) {
					$this->insure_contents = false;
				}
			break;
		case 'IN' :
				if ( 'INR' !== $this->fedex_currency ) {
					$this->insure_contents = false;
				}
				break;
		case 'CO' :
				if ( 'COP' !== $this->fedex_currency ) {
					$this->insure_contents = false;
				}
				break;
		}
		
		$this->shipmentErrorMessage 		= '';
		// Fedex LTL freight services, remaining freight services are express freight
		$this->fedex_ltl_freight_services 	= array(
			'FEDEX_FREIGHT_ECONOMY',
			'FEDEX_FREIGHT_PRIORITY',
			'INTERNATIONAL_ECONOMY_FREIGHT',
			'INTERNATIONAL_PRIORITY_FREIGHT'
		);
	}
	
	/**
	 * Convert the Cost to FedEx Currency.
	 */
	public function convert_to_fedex_currency( $cost ) {
		if( $this->fedex_currency != $this->wc_store_currency && ! empty($this->fedex_conversion_rate) ) {
			$cost = (float) $cost * $this->fedex_conversion_rate;
		}
		return $cost;
	}

	public function convert_to_inr_currency( $cost ) {
		$inr = "INR";
		$store_currency = $this->wc_store_currency;
		$woocommerce_currency_conversion_rate = get_option('woocommerce_multicurrency_rates');
		if($inr != $store_currency && !empty($woocommerce_currency_conversion_rate)){

			$inr_currency_rate = $woocommerce_currency_conversion_rate[$inr];
			$store_currency_rate = $woocommerce_currency_conversion_rate[$store_currency];

			$conversion_rate = $inr_currency_rate / $store_currency_rate;
			$cost *= $conversion_rate;
		}		
		return $cost;
	}

	private function set_origin_country_state(){
		$origin_country_state 		= isset( $this->settings['origin_country'] ) ? $this->settings['origin_country'] : '';
		if ( strstr( $origin_country_state, ':' ) ) :
			// WF: Following strict php standards.
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_country 				= current($origin_country_state_array);
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_state   				= end($origin_country_state_array);
		else :
			$origin_country = $origin_country_state;
			$origin_state   = '';
		endif;

		$this->origin_country  	= apply_filters( 'woocommerce_fedex_origin_country_code', $origin_country );
		$this->origin_state 	= !empty($origin_state) ? $origin_state : ( isset($this->settings[ 'freight_shipper_state' ]) ? $this->settings[ 'freight_shipper_state' ] : '');

		// Alternate Return Address
		$alt_return_country_state 		= isset( $this->settings['alt_return_country_state'] ) ? $this->settings['alt_return_country_state'] : '';
		
		if ( strstr( $alt_return_country_state, ':' ) ) :
			$alt_return_country_state_array		= explode(':',$alt_return_country_state);
			$alt_return_country					= current($alt_return_country_state_array);
			$alt_return_country_state_array		= explode(':',$alt_return_country_state);
			$alt_return_state					= end($alt_return_country_state_array);
		else :
			$alt_return_country			= $alt_return_country_state;
			$alt_return_state			= '';
		endif;

		$this->alt_return_country  	= apply_filters( 'woocommerce_fedex_alt_return_country_code', $alt_return_country );
		$this->alt_return_state 	= !empty($alt_return_state) ? $alt_return_state : ( isset($this->settings[ 'alt_return_custom_state' ]) && !empty( $this->settings[ 'alt_return_custom_state' ] ) ? $this->settings[ 'alt_return_custom_state' ] : '' );
	}

	private function is_soap_available(){
		if( extension_loaded( 'soap' ) ){
			return true;
		}
		return false;
	}

	public function debug( $message, $type = 'notice' ) {

		if ( $this->debug && is_admin() ) {
			echo( $message);
		}
	}

	public function admin_diagnostic_report( $data ) {
	
		if( function_exists("wc_get_logger") ) {

			$log = wc_get_logger();
			$log->debug( ($data).PHP_EOL.PHP_EOL, array('source' => 'PluginHive-FedEx-Error-Debug-Log'));
		}
	}

	public function get_fedex_packages( $package ) {
		switch ( $this->packing_method ) {
			case 'box_packing' :
				$fedex_packages = $this->box_shipping( $package );
			break;
			case 'weight_based' :
				$fedex_packages = $this->weight_based_shipping( $package );
			break;
			case 'per_item' :
			default :
				$fedex_packages = $this->per_item_shipping( $package );
			break;
		}
		return apply_filters( 'wf_fedex_packages', $fedex_packages, $package );
	}

	public function get_freight_class( $item_data ) {

		global $wp_version;

		if($item_data->variation_id){
			$class	=	get_post_meta( $item_data->variation_id, '_wf_freight_class', true );
		}
		
		if(!$class)
			$class	=	get_post_meta( $item_data->id, '_wf_freight_class', true );
		
		// To be deprecated after WC 2.6
		if(!$class){
			$shipping_class_id	=	$item_data->get_shipping_class_id();
			if($shipping_class_id){
				
				if( version_compare( $wp_version, '4.4', '<=' ) ){
					$class = get_woocommerce_term_meta( $shipping_class_id, 'fedex_freight_class', true );
				}else{
					$class = get_term_meta( $shipping_class_id, 'fedex_freight_class', true );
				}
				if ( !empty($class) && !is_numeric($class) ) {
					$fClass = explode('_', $class);
					$fArray = array(
						'50'  ,
						'55'  ,
						'60'  ,
						'65'  ,
						'70'  ,
						'77.5',
						'85'  ,
						'92.5',
						'100' ,
						'110' ,
						'125' ,
						'150' ,
						'175' ,
						'200' ,
						'250' ,
						'300' ,
						'400' ,
						'500' ,
					);
					if( isset($fClass[1]) && in_array( $fClass[1], $fArray )) {
						$class = $fClass[1];
					} else {
						$class = '';
					}
				}
				
			}
		}
		return $class ? $class : '';
	}

	private function per_item_shipping( $package ) {
		
		$to_ship 		= array();
		$group_id 		= 1;
		$freight_id 	= 1;

		// Get weight of order
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $this->xa_get_product( $values ) );
			
			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support Product addon, WooCommerce Measurement Price Calculator plugin
			
			$signature 	= '';
			foreach( $additional_products as $values) {
				if ( ! $values['data']->needs_shipping() ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );
					
					$this->admin_diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				
				if($skip_product) {

					$this->admin_diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}

				if ( ! $values['data']->get_weight() ) {
					$this->debug( sprintf( __( 'Product # is missing weight. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Label Generation.', $values['data']->get_id() ) );

					return;
				}

				//PDS-179
				$parentId 		= wp_get_post_parent_id($values['data']->get_id());
				$productId 		= $values['data']->get_id();
				$signature_temp = get_post_meta( $productId, '_ph_fedex_signature_option', true );

				if ( empty($signature_temp) && !empty($parentId) ) {
					$signature_temp = get_post_meta( $parentId, '_ph_fedex_signature_option', true );
				}

				$signature 	= array_search($signature_temp, $this->prioritizedSignatureOption);
				$group 		= array();

				$group = array(
					'GroupNumber'			=> $group_id,
					'GroupPackageCount'		=> 1,
					'Weight'				=> array(

						'Value'		=> $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $values['data']->get_weight(), $this->weight_unit ), 2 ) ,
						'Units'		=> $this->labelapi_weight_unit
					),
					'packed_products'		=> array( $values['data'] )
				);

				//PDS-179
				if( isset($signature) && !empty($signature)){
					$group['signature_option']	= $signature ;
				}
				else{
					$group['signature_option']	=  $this->signature_option ;
				}

				if ( $values['data']->length && $values['data']->height && $values['data']->width ) {

					$dimensions = array( $values['data']->length, $values['data']->width, $values['data']->height );

					sort( $dimensions );

					$group['Dimensions'] = array(
						'Length' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
						'Width'  => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
						'Height' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
						'Units'  => $this->labelapi_dimension_unit
					);
				}

				$group['InsuredValue']	= array(

					'Amount'	=> round( $this->convert_to_fedex_currency( $this->wf_get_insurance_amount($values['data']) ), 2),
					'Currency'	=> $this->wf_get_fedex_currency()
				);

				if ( $this->freight_enabled ) {

					$group['PhysicalPackaging'] 			= 'SKID';
					$group['AssociatedFreightLineItems'] 	= array(
						'Id'	=> $freight_id,
					);
				}

				for($loop = 0; $loop < $values['quantity'];$loop++){

					$to_ship[] = $group;

					if ( $this->freight_enabled ) {

						$freight_id++;
						$group['AssociatedFreightLineItems']['Id'] 	= $freight_id;
					}
				}
				
				$group_id++;
			}
		}

		return $to_ship;
	}

	private function box_shipping( $package ) {
		if ( ! class_exists( 'PH_FedEx_Boxpack' ) ) {
			include_once 'class-wf-packing.php';
		}

		$boxpack = new PH_FedEx_Boxpack();

		// Merge default boxes for Backward Compatibility
		foreach ( $this->default_boxes as $key => $box ) {

			if( isset($this->boxes) && isset($this->boxes[$box['id']]) && isset($this->boxes[$box['id']]['id']) && in_array($this->boxes[$box['id']]['id'], $this->standard_boxes) ) {
				continue;
			}

			$box['enabled'] = isset( $this->boxes[ $box['id'] ]['enabled'] ) ? $this->boxes[ $box['id'] ]['enabled'] : true;
			$this->boxes[$box['id']] = $box;
		} 
		
		// Define boxes
		foreach ( $this->boxes as $key => $box ) {

			if( $package['destination']['country']==$this->origin_country && ( isset($box['id']) && ($box['id']=='FEDEX_25KG_BOX' || $box['id']=='FEDEX_10KG_BOX') ) ) {
				continue;
			}
			
			if ( !is_numeric( $key ) && !in_array($key, $this->standard_boxes) ) {
				continue;
			}

			if ( ! $box['enabled'] ) {
				continue;
			}

			$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'], $box['box_weight'] );
			$newbox->set_inner_dimensions($box['inner_length'], $box['inner_width'], $box['inner_height']);

			if ( isset( $box['id'] ) ) {
				$newbox->set_id( current( explode( ':', $box['id'] ) ) );
			}

			if ( $box['max_weight'] ) {
				$newbox->set_max_weight( $box['max_weight'] );
			}
		}

		// Add items
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $this->xa_get_product( $values ) );
			
			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support Products addon, WooCommerce Measurement Price Calculator plugin
			
			foreach( $additional_products as $values ) {
				if ( ! $values['data']->needs_shipping() ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				
				if($skip_product) {

					$this->admin_diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}

				$pre_packed = $this->ph_get_post_meta_key( $values['data'] , '_wf_fedex_pre_packed_var', 1);

				if( empty( $pre_packed ) || $pre_packed == 'no' ){
					$pre_packed = $this->ph_get_post_meta_key( $values['data'] , '_wf_fedex_pre_packed', 1);
				}

				$pre_packed = apply_filters('wf_fedex_is_pre_packed',$pre_packed,$values);

				if( !empty($pre_packed) && $pre_packed == 'yes' ){
					$pre_packed_contents[] = $values;
					$this->debug( sprintf( __( 'Pre Packed product. Skipping the product '.$values['data']->id, 'wf-shipping-fedex' ), $item_id ) );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is pre packed. Skipping from Box Packaging Algorithm.', $values['data']->get_id() ) );

					continue;
				}			

				$dimensions = array( $values['data']->get_length(), $values['data']->get_width(), $values['data']->get_height() );
				$weight = $values['data']->get_weight();

				if ( $dimensions[0] && $dimensions[1] && $dimensions[2] && $weight ) {	
					for ( $i = 0; $i < $values['quantity']; $i ++ ) {
						$boxpack->add_item(
							Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ),
							Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ),
							Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ),
							round(Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $weight, $this->weight_unit ),2),
							$this->get_product_price($values['data']),
							array(
								'data' => $values['data']
							)
						);
					}

				} else {
					$this->debug( sprintf( __( 'Product #%s is missing weight or dimensions. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is missing weight or dimensions. Aborting Label Generation.', $values['data']->get_id() ) );

					return;
				}
			}

		}

		// Pack it
		$boxpack->pack();
		$packages = $boxpack->get_packages();
		$group_id = 1;
		
		$to_ship = array();
		if( !empty($packages) ){
			$to_ship  = array_merge( $to_ship, $this->xa_get_box_packages( $packages, $group_id ) );
			$group_id += count( $to_ship );
		}

		//add pre packed wf_fedex_add_pre_packed_productitem with the packages
		if( !empty($pre_packed_contents) ){
			$prepacked_requests = $this->wf_fedex_add_pre_packed_product( $pre_packed_contents, $group_id );
			$to_ship = array_merge($to_ship, $prepacked_requests);
		}

		return $to_ship;
	}

	private function xa_get_box_packages( $packages, $group_id=1 ) {

		$to_ship  				= array();
		$freight_id 			= 1;

		foreach ( $packages as $package ) {
			
			// Insurance amount of box $boxinsuredprice
			$boxinsuredprice 		= 0;
			$associatedLineItemId 	= array();

			if(isset($package->packed))
			{
				$signature = '';
				foreach( $package->packed as $box_item)
				{
					//PDS-179
					$item 			= $box_item->meta['data'];
					$parentId 		= wp_get_post_parent_id($item->get_id());
					$productId 		= $item->get_id();
					$signature_temp = get_post_meta( $productId, '_ph_fedex_signature_option', true );

					if ( empty($signature_temp) && !empty($parentId) ) {
						$signature_temp = get_post_meta( $parentId, '_ph_fedex_signature_option', true );
					}

                    $signature_temp = array_search($signature_temp, $this->prioritizedSignatureOption);
                    $signature_temp = empty($signature_temp) ? $signature : $signature_temp;
                    $signature 		= !empty($signature) && $signature > $signature_temp ? $signature : $signature_temp;
					$boxinsuredprice += $this->wf_get_insurance_amount($box_item->meta['data']);

					if ( $this->freight_enabled ) {

						$item = $box_item->meta['data'];

						if( isset($associatedLineItemId[$item->get_id()]) ) {

							continue;
						}

						$associatedLineItemId[$item->get_id()] 	= array(

							'Id'	=> $freight_id,
						);

						$freight_id++;
					}
				}
			}
			
			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			$group = array(
				'GroupNumber'			=> $group_id,
				'GroupPackageCount'		=> 1,
				'Weight'				=> array(

					'Value'		=> $this->round_up( $package->weight, 2 ) ,
					'Units'		=> $this->labelapi_weight_unit
				),

				'Dimensions'			=> array(

					'Length'	=> max( 1, round( $dimensions[2], 0 ) ),
					'Width'		=> max( 1, round( $dimensions[1], 0 ) ),
					'Height'	=> max( 1, round( $dimensions[0], 0 ) ),
					'Units'		=> $this->labelapi_dimension_unit
				),

				'InsuredValue'			=> array(
					'Amount'	=> round( $this->convert_to_fedex_currency( $boxinsuredprice ), 2),
					'Currency'	=> $this->wf_get_fedex_currency()
				),

				'packed_products'		=> array(),
				'package_id'			=> $package->id,
				'boxName'               => $package->id,
			);

			//PDS-179
			if( isset($signature) && !empty($signature)){
				$group['signature_option']	= $signature ;
			}
			else{
				$group['signature_option']	=  $this->signature_option ;
			}

			if ( ! empty( $package->packed ) && is_array( $package->packed ) ) {

				foreach ( $package->packed as $packed ) {

					$group['packed_products'][] = $packed->get_meta( 'data' );
				}
			}

			if ( $this->freight_enabled ) {

				$group['PhysicalPackaging'] 			= 'SKID';
				$group['AssociatedFreightLineItems'] 	= array_values($associatedLineItemId);
			}

			$to_ship[] = $group;

			$group_id++;
		}

		return $to_ship;
	}

	private function xa_get_product($package){
		//Case of Multiple shipping address 
		if( !empty($package['product_id']) ){
			
			if( isset($package['variation_id']) && !empty($package['variation_id']) )
			{
				return $package['variation_id'];
			}

			return $package['product_id'];
		}
		return $package['data'];
	}
	
	private function weight_based_shipping( $package ){
		if ( ! class_exists( 'Ph_Fedex_WeightPack' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		
		$weight_pack=new Ph_Fedex_WeightPack($this->weight_pack_process);
		$weight_pack->set_max_weight($this->box_max_weight);
		
		foreach ( $package['contents'] as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $this->xa_get_product( $values ) );
			
			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support Product addon, WooCommerce Measurement Price Calculator plugin
			
			foreach($additional_products as $values) {
				if ( ! $values['data']->needs_shipping() ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				
				if($skip_product) {

					$this->admin_diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Label Generation.', $values['data']->get_id() ) );

					continue;
				}
				
				$pre_packed = $this->ph_get_post_meta_key($values['data'] , '_wf_fedex_pre_packed_var', 1);

				if( empty( $pre_packed ) || $pre_packed == 'no' ){
					$pre_packed = $this->ph_get_post_meta_key( ($values['data']) , '_wf_fedex_pre_packed', 1);
				}

				$pre_packed = apply_filters('wf_fedex_is_pre_packed',$pre_packed,$values);
				
				if( !empty($pre_packed) && $pre_packed == 'yes' ){
					$pre_packed_contents[] = $values;
					$this->debug( sprintf( __( 'Pre Packed product. Skipping the product '.$values['data']->id, 'wf-shipping-fedex' ), $item_id ) );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is pre packed. Skipping from Weight Packing Algorithm.', $values['data']->get_id() ) );

					continue;
				}

				$product_weight = $this->xa_get_volumatric_products_weight( $values['data'] );
				if( !empty($product_weight) ){
					$weight_pack->add_item( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product_weight, $this->weight_unit ), $values['data'], $values['quantity'] );
				}else{	
					$this->debug( sprintf( __( 'Product #%s is missing weight. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->admin_diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Label Generation.', $values['data']->get_id() ) );

					return;
				}
			}

		}		
		
		$pack	=	$weight_pack->pack_items();
		
		$errors	=	$pack->get_errors();
		if( !empty($errors) ){
			//do nothing
			return;
		} else {
			$boxes		=	$pack->get_packed_boxes();
			$unpacked_items	=	$pack->get_unpacked_items();
			
			$to_ship  				= 	array();
			$group_id 				= 	1;		
			$group					= 	array();
			$packed_products		=	array();
			$insured_value			=	0;
			$freight_id 			=	1;
			
			if(isset($this->order)){
				$order_sub_total	=	$this->order->get_subtotal();
			}
			
			$packages		=	array_merge( $boxes,	$unpacked_items ); // merge items if unpacked are allowed
			$package_count	=	sizeof($packages);
			
			// get all items to pass if item info in box is not distinguished
			$packable_items	=	$weight_pack->get_packable_items();
			$all_items		=	array();
			if(is_array($packable_items)){
				foreach($packable_items as $packable_item){
					$all_items[]	=	$packable_item['data'];
				}
			}
            
            $signature = '';
			foreach($packages as $package) {

				$insured_value			= 0;
				$associatedLineItemId 	= array();

				if( !empty($package['items']) ) {

					foreach($package['items'] as $item) {

						//PDS-179
						$parentId 		= wp_get_post_parent_id($item->get_id());
						$productId 		= $item->get_id();
						$signature_temp = get_post_meta( $productId, '_ph_fedex_signature_option', true );

						if ( empty($signature_temp) && !empty($parentId) ) {
							$signature_temp = get_post_meta( $parentId, '_ph_fedex_signature_option', true );
						}

						$signature_temp = array_search($signature_temp, $this->prioritizedSignatureOption);
						$signature_temp = empty($signature_temp) ? $signature : $signature_temp;
						$signature 		= !empty($signature) && $signature > $signature_temp ? $signature : $signature_temp;
						$insured_value	= $insured_value + $this->wf_get_insurance_amount($item);
						
						if ( $this->freight_enabled ) {

							if( isset($associatedLineItemId[$item->get_id()]) ) {

								continue;
							}

							$associatedLineItemId[$item->get_id()] 	= array(

								'Id'	=> $freight_id,
							);

							 $freight_id++;
						}
					}

				}else{
					// If package doesn't have item information then devide order sub total with #no of packages
					if($order_sub_total && $package_count) {

						$insured_value	= $order_sub_total/$package_count;
					}
				}

				$group = array(
					'GroupNumber'			=> $group_id,
					'GroupPackageCount'		=> 1,
					'Weight'				=> array(

						'Value'			=> $this->round_up($package['weight'],2),
						'Units'			=> $this->labelapi_weight_unit
					),
					'packed_products'		=> $package['items']?$package['items']:$all_items
				);

				$group['InsuredValue']		= array(

					'Amount'		=> round( $this->convert_to_fedex_currency($insured_value), 2),
					'Currency'		=> $this->wf_get_fedex_currency()
				);

				//PDS-179
				if( isset($signature) && !empty($signature)){
					$group['signature_option']	= $signature ;
				}
				else{
					$group['signature_option']	=  $this->signature_option ;
				}

				if ( $this->freight_enabled ) {

					$group['PhysicalPackaging'] 			= 'SKID';
					$group['AssociatedFreightLineItems'] 	= array_values($associatedLineItemId);
				}
				
				$to_ship[] = $group;
			}
		}

		//add pre packed item with the package
		if( !empty($pre_packed_contents) ){
			$prepacked_requests = $this->wf_fedex_add_pre_packed_product( $pre_packed_contents, $group_id );
			$to_ship = array_merge($to_ship, $prepacked_requests);
		}
		return $to_ship;
	}

	/**
	 * Get Meta key.
	 * @param $post mixed object | int Post object or key of which meta key has to be fetched.
	 * @param $key string Meta key to fetch.
	 * @param $single boolean True if single value to be fetched false if array of data to be fetched. Default value true.
	 * @return string meta value.
	 */
	public function ph_get_post_meta_key( $post, $key, $single= 'true') {
		$meta_val = null;
		if( is_object($post) ) {
			if( WC()->version >= '3.0' && $post instanceof wf_product && ! empty($post->obj) ) {
				$meta_val = $post->obj->get_meta( $key, $single);
			}
			else{
				$meta_val = get_post_meta( $post->get_id(), $key, $single );
			}
		}
		else {
			$meta_val = get_post_meta( $post, $key, $single );
		}
		return $meta_val;
	}

	private function wf_fedex_add_pre_packed_product( $pre_packed_items, $group_id=1 ){

		$to_ship  		= array();
		$freight_id 	= 1;
		$signature 		= '';

		 // Get weight of order
		foreach ( $pre_packed_items as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $this->xa_get_product( $values ) );
			
			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

				$this->admin_diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Label Generation.', $values['data']->get_id() ) );

				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				$this->debug( sprintf( __( 'Product # is missing weight. Aborting.', 'wf-shipping-fedex' ), $item_id ), 'error' );

				$this->admin_diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Label Generation.', $values['data']->get_id() ) );

				return;
			}

			//PDS-179
			$parentId 		= wp_get_post_parent_id($values['data']->get_id());
			$productId 		= $values['data']->get_id();
			$signature_temp = get_post_meta( $productId, '_ph_fedex_signature_option', true );

			if ( empty($signature_temp) && !empty($parentId) ) {
				$signature_temp = get_post_meta( $parentId, '_ph_fedex_signature_option', true );
			}

			$signature 	= array_search($signature_temp, $this->prioritizedSignatureOption);
			$group 		= array();

			$group = array(
				'GroupNumber'			=> $group_id,
				'GroupPackageCount'		=> 1,
				'Weight'				=> array(

					'Value'			=> $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $values['data']->get_weight(), $this->weight_unit ), 2 ) ,
					'Units'			=> $this->labelapi_weight_unit
				),
				'packed_products'		=> array( $values['data'] )
			);
			
			//PDS-179
			if( isset($signature) && !empty($signature)){
				$group['signature_option']	= $signature ;
			}
			else{
				$group['signature_option']	=  $this->signature_option ;
			}

			if( $this->packing_method == 'box_packing' ) {
				$group['boxName']= "Pre-packed Product";
			}

			$dimensions = array( $values['data']->get_length(), $values['data']->get_width(), $values['data']->get_height() );

			if ( $dimensions[0] && $dimensions[1] && $dimensions[2] ) {

				sort( $dimensions );

				$group['Dimensions']	= array(

					'Length'		=> max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
					'Width'			=> max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
					'Height'		=> max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
					'Units'			=> $this->labelapi_dimension_unit
				);
			}

			$group['InsuredValue']	= array(

				'Amount'		=> round( $this->convert_to_fedex_currency( $this->wf_get_insurance_amount($values['data']) ), 2),
				'Currency'		=> $this->wf_get_fedex_currency()
			);

			if ( $this->freight_enabled ) {

				$group['PhysicalPackaging'] 			= 'SKID';
				$group['AssociatedFreightLineItems'] 	= array(

					'Id'	=> $group_id,
				);
			}

			for($loop = 0; $loop < $values['quantity'];$loop++) {

				$to_ship[] = $group;

				if ( $this->freight_enabled ) {

					$freight_id++;
					$group['AssociatedFreightLineItems']['Id'] 	= $freight_id;
				}
			}

			$group_id++;
			
			
		}

		return $to_ship;
	}

	private function round_up( $value, $precision=2 ) { 
		$pow = pow ( 10, $precision ); 
		return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
	}

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

	public function residential_address_validation( $package ) {
		$residential = $this->residential;

		// Address Validation API only available for production
		if ( $this->production && in_array($package['destination']['country'],$this->address_validation_contries) ) {

			// Check if address is residential or commerical
			try {
				$request = array();

				$request['WebAuthenticationDetail'] = array(
					'UserCredential' => array(
						'Key'      => $this->api_key,
						'Password' => $this->api_pass
					)
				);
				$request['ClientDetail'] = array(
					'AccountNumber' => $this->account_number,
					'MeterNumber'   => $this->meter_number
				);
				$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Address Validation Request v4 from WooCommerce ***' );
				$request['Version'] = array( 'ServiceId' => 'aval', 'Major' => $this->addressvalidationservice_version, 'Intermediate' => '0', 'Minor' => '0' );
				$request['RequestTimestamp'] = date( 'c' );
				$request['Options'] = array(
					'CheckResidentialStatus' => 1,
					'MaximumNumberOfMatches' => 1,
					'StreetAccuracy' => 'LOOSE',
					'DirectionalAccuracy' => 'LOOSE',
					'CompanyNameAccuracy' => 'LOOSE',
					'ConvertToUpperCase' => 1,
					'RecognizeAlternateCityNames' => 1,
					'ReturnParsedElements' => 1
				);
				$request['AddressesToValidate'] = array(
					0 => array(
						'AddressId' => 'WTC',
						'Address' => array(
							'StreetLines' => array( $package['destination']['address_1'], $package['destination']['address_2'] ),
							'PostalCode'  => $package['destination']['postcode'],
						)
					)
				);

				$wsdl_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/production/AddressValidationService_v' . $this->addressvalidationservice_version. '.wsdl';
				$client = $this->wf_create_soap_client( $wsdl_dir );

				if( $this->soap_method == 'nusoap' ){
					$response = $client->call( 'addressValidation', array( 'AddressValidationRequest' => $request ) );
					$response = json_decode( json_encode( $result ), false );
				}else{
					$response = $client->addressValidation( $request );
				}

				if ( $response->HighestSeverity == 'SUCCESS' ) {
					if ( is_array( $response->AddressResults ) )
						$addressResult = $response->AddressResults[0];
					else
						$addressResult = $response->AddressResults;

					if ( $addressResult->Classification == 'BUSINESS' )
						$residential = false;
					elseif ( $addressResult->Classification == 'RESIDENTIAL' )
						$residential = true;
				}

			} catch (Exception $e) {
				$this->debug( __( 'SoapFault while residential_address_validation.', 'wf-shipping-fedex' ) );
			}

		}

		$this->residential = apply_filters( 'woocommerce_fedex_address_type', $residential, $package );

		if ( $this->residential == false ) {
			$this->debug( __( 'Business Address', 'wf-shipping-fedex' ) );
		}
	}

	private function get_fedex_common_api_request( $request ) {
		// Prepare Shipping Request for FedEx
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'      => $this->api_key,
				'Password' => $this->api_pass
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number
		);
		$request['TransactionDetail'] = array(
			'CustomerTransactionId'     => '*** Express Domestic Shipping Request using PHP ***'
		);
		$request['Version'] = array(
			'ServiceId'              => 'ship',
			'Major'                  => $this->ship_service_version,
			'Intermediate'           => '0',
			'Minor'                  => '0'
		);		
		return $request;
	}
	private function wf_add_tin_number($request, $package){	
		$tintype 	= isset( $this->settings['tin_type'] ) ? $this->settings['tin_type'] : 'BUSINESS_STATE';
		$tin_number = isset($package['origin']['tin_number']) ? $package['origin']['tin_number'] : $this->tin_number;

		if(!empty($tin_number)){
			$request['RequestedShipment']['Shipper']['Tins'] =  array(
				'TinType'	=> $tintype,
				'Number'	=> $tin_number
			);
		}
		return $request;
	}

	public function get_next_working_day( $shippingDay, $shipTimeStamp ) {

		$day_order = array(
			0 => 'Sun',
			1 => 'Mon',
			2 => 'Tue',
			3 => 'Wed',
			4 => 'Thu',
			5 => 'Fri',
			6 => 'Sat',
		);

		$nextWorkingDay 	= $shippingDay;
		$date 				= new DateTime( $shipTimeStamp );

		$shippingDayKey = array_search($shippingDay, $day_order);

		for ($i=0; $i<8; $i++) {

			$found_index 	= array_search($day_order[$shippingDayKey], $this->working_days);

			if ( !empty($found_index) || $found_index === 0 ) {

				$nextWorkingDay = $this->working_days[$found_index];
				break;
			}

			if($shippingDayKey <= 5 )  $shippingDayKey++;  else  $shippingDayKey=0; 
		}

		$date->modify("next $nextWorkingDay");

		return $date->format('c');
	}

	private function get_fedex_api_request( $fedex_packages, $package ,$request_type) {
		$request = array();

		$request = $this->get_fedex_common_api_request($request);
		$this->packaging_type = empty($fedex_packages['package_id']) ? 'YOUR_PACKAGING' : $fedex_packages['package_id'];

		//$request['ReturnTransitAndCommit'] = false;
		$request['RequestedShipment']['PreferredCurrency']	= $this->wf_get_fedex_currency();
		$request['RequestedShipment']['DropoffType']		= $this->dropoff_type;
		$request['RequestedShipment']['ServiceType']		= $this->service_code;
		$request['RequestedShipment']['ShipTimestamp']		= date( 'c', ( current_time('timestamp') + $this->timezone_offset ) );

		$date 			= new DateTime( $request['RequestedShipment']['ShipTimestamp'] );
		$shippingDay 	= $date->format('D');
		
		if( ! in_array($shippingDay, $this->working_days) ) {

			$shipTimeStamp 	= $this->get_next_working_day( $shippingDay, $request['RequestedShipment']['ShipTimestamp'] );
			
			$request['RequestedShipment']['ShipTimestamp'] = $shipTimeStamp;
		}

		$request['RequestedShipment']['PackagingType']		= $this->packaging_type;

		if( $this->duties_and_taxes_rate && $this->origin_country != $package['destination']['country'] )
		{
			$request['RequestedShipment']['EdtRequestType']		= 'ALL';
		}

		//Temp Fix
		if('US' === $package['destination']['country'] && 'US' === $this->origin_country)
		{
			if( $request['RequestedShipment']['PackagingType'] == 'FEDEX_25KG_BOX' ||  $request['RequestedShipment']['PackagingType'] == 'FEDEX_10KG_BOX' )
			{
				$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING';
			}
		}

		if( in_array($this->origin_country, $this->fedexBoxCountries) && in_array($request['RequestedShipment']['PackagingType'], $this->fedexBox) ) {
			$request['RequestedShipment']['PackagingType'] = 'FEDEX_BOX';
		}

		if($this->ship_from_address === 'shipping_address'){
			$from_address =  $this->order_address($package);
			$to_address   =  $this->shop_address( $package );
		}else {
			$from_address =  $this->shop_address( $package );
			$to_address   =  $this->order_address($package);
		}
				
		$request['RequestedShipment']['Shipper'] = $from_address;
		$request = $this->wf_add_tin_number($request, $package);
		
		$this->fed_req->set_package( $package );
		$shipping_charges_payment	=	$this->fed_req->get_shipping_charges_payment( $request_type );		
		$request['RequestedShipment']['ShippingChargesPayment'] = $shipping_charges_payment;
		  
		$request['RequestedShipment']['RateRequestTypes'] = $this->request_type === 'LIST' ? 'LIST' : 'NONE';
		$request['RequestedShipment']['Recipient'] = $to_address;

		// In v25.WSDl only COMMON2D, LABEL_DATA_ONLY supported. Freight Labels will be added seperately

		// if ( 'freight' === $request_type ){
		// 	$request['RequestedShipment']['LabelSpecification'] = array(
		// 		'LabelFormatType' => 'VICS_BILL_OF_LADING',
		// 		'ImageType' => strtoupper($this->image_type),  // valid values DPL, EPL2, PDF, ZPLII and PNG
		// 		'LabelStockType' => $this->output_format
		// 	);
		// }
		
		$request['RequestedShipment']['LabelSpecification'] = array(
			'LabelFormatType' 		=> 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
			'ImageType'				=> strtoupper($this->image_type),  // valid values DPL, EPL2, PDF, ZPLII and PNG
			'LabelStockType'		=> $this->output_format
		);

		// Data elements / areas which may be masked from printing on the shipping labels.
		if ( !empty( $this->label_maskable_type )) {

			$request['RequestedShipment']['LabelSpecification']['CustomerSpecifiedDetail'] = array(

				'MaskedData' => $this->label_maskable_type,
			);
		}

		if( $this->doc_tab_content && strtoupper($this->image_type) == 'ZPLII' ) {

			$request['RequestedShipment']['LabelSpecification']['LabelPrintingOrientation'] = $this->doc_tab_orientation;
			$request['RequestedShipment']['LabelSpecification']['CustomerSpecifiedDetail'] 	= array();

			$request['RequestedShipment']['LabelSpecification']['CustomerSpecifiedDetail'] = array(

				'DocTabContent' => array(

					'DocTabContentType' => 'STANDARD',

				),
			);
		}

		if( $this->alternate_return_address )
		{

			if( $this->billing_as_alternate_return_address ) {

				$billing_address 	= $this->order->get_address('billing');

				$alt_address 	=  array(
					'name'		=> htmlspecialchars($billing_address['first_name']).' '.htmlspecialchars($billing_address['last_name']),
					'company' 	=> !empty($billing_address['company']) ? htmlspecialchars($billing_address['company']) : '-',
					'phone' 	=> ( strlen($billing_address['phone']) > 15 ) ? str_replace(' ', '', $billing_address['phone']) : $billing_address['phone'],
					'email' 	=> htmlspecialchars($billing_address['email']),
					'address_1'	=> htmlspecialchars($billing_address['address_1']),
					'address_2'	=> htmlspecialchars($billing_address['address_2']),
					'city' 		=> htmlspecialchars($billing_address['city']),
					'state' 	=> htmlspecialchars($billing_address['state']),
					'country' 	=> $billing_address['country'],
					'postcode' 	=> $billing_address['postcode'],
				);

				$request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin'] = array(
					'Contact'	=> array(
						'PersonName' 	=> $this->ph_replace_special_characters($alt_address['name']),
						'CompanyName' 	=> $alt_address['company'],
						'PhoneNumber'	=> $alt_address['phone'],
					),

					'Address'	=> array(
						'StreetLines' 	=> $this->ph_replace_special_characters($alt_address['address_1']).' '.$this->ph_replace_special_characters($alt_address['address_2']),
						'City' 			=> $this->ph_replace_special_characters($alt_address['city']),
						'PostalCode'	=> $alt_address['postcode'],
						'CountryCode'	=> $alt_address['country'],
						'StateOrProvinceCode' => $alt_address['state'],
					),
				);

			}else{

				$request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin'] = array(
					'Contact'	=> array(
						'PersonName' 	=> $this->ph_replace_special_characters($this->freight_shipper_person_name),
						'CompanyName' 	=> $this->freight_shipper_company_name,
						'PhoneNumber'	=> $this->freight_shipper_phone_number,
					),

					'Address'	=> array(
						'StreetLines' 	=> $this->ph_replace_special_characters($this->alt_return_streetline),
						'City' 			=> $this->ph_replace_special_characters($this->alt_return_city),
						'PostalCode'	=> $this->alt_return_postcode,
						'CountryCode'	=> $this->alt_return_country,
						'StateOrProvinceCode' => $this->alt_return_state,
					),
				);
			}
		}

		return $request;
	}

	public function get_fedex_requests( $fedex_packages, $package, $request_type = '' ) {

		$requests = array();
		global $woocommerce;
		
		$this->is_international = ($package['destination']['country'] != $this->origin_country ) ? true : false;
		
		// All reguests for this package get this data
		if ( $fedex_packages ) {

			$total_packages = 0;
			$total_weight   = 0;
			$package_count 	= 0;

			$freight_line_items 	= array();
			$line_item_id 			= 1;

			foreach ( $fedex_packages as $key => $parcel ) {

				$num_of_packages = isset( $parcel['num_of_packages'] ) && !empty( $parcel['num_of_packages'] ) ? $parcel['num_of_packages'] : 1;
				if( $num_of_packages>1 ){
					$total_packages += $num_of_packages;
					$total_weight   += $parcel['Weight']['Value'] * $num_of_packages;
				}

				else{
					$total_packages += $parcel['GroupPackageCount'];
					$total_weight   += $parcel['Weight']['Value'] * $parcel['GroupPackageCount'];
				}

				if ( isset($parcel['packed_products']) && $request_type === 'freight' ) {

					$executed_prouducts 	= array();

					foreach ( $parcel['packed_products'] as $product ) {

						$line_items 	= array();
						$freight_class 	= $this->get_freight_class( $product );
						$product_id 	= $product->get_id();

						$freight_id 	= $line_item_id;
						$flag 			= true;

						// When a Package contains Multiple Quantity of Same Product, then Line Item Id will be same for all the Product Quantity
						if( isset($executed_prouducts[$product_id]) && !empty($executed_prouducts[$product_id]) ) {

							$freight_id 	= $executed_prouducts[$product_id];
							$flag 			= false;
						}

						$desription 		= !empty($this->settings['item_description']) ? $this->settings['item_description'] : 'Heavy Stuff';
						$commodity_desc 	= html_entity_decode( get_post_meta( $product_id , '_ph_commodity_description', 1) );
						$commodity_desc 	= !empty($commodity_desc) ? $commodity_desc : $desription;
						
						// Remove Special Characters Commodity Description
						if( $this->remove_special_char == true ) {

							$commodity_desc 	= preg_replace('/[^A-Za-z0-9-() ]/', '', $commodity_desc);
						}

						$commodity_desc = ( strlen( $commodity_desc ) >= 450 ) ? substr( $commodity_desc, 0, 445 ).'...' : $commodity_desc;

						$freight_class = $freight_class ? $freight_class : $this->freight_class;
						$freight_class = $freight_class < 100 ?  '0' . $freight_class : $freight_class;
						$freight_class = 'CLASS_' . str_replace( '.', '_', $freight_class );

						/***
							When same product with more than one Quantity, increase Pieces value
							Getting error: The total of Requested Package Line Item Weights cannot exceed the Requested Shipment Total Weight.
						***/
						// if( isset($freight_line_items[$product_id.$line_item_id]) ) {

						// 	$freight_line_items[$product_id.$line_item_id]['Pieces']++;
						// 	$freight_line_items[$product_id.$line_item_id]['HandlingUnits']++;

						// 	continue;
						// }

						$line_items = array(
							'Id'				=> $freight_id,
							'Pieces'			=> $parcel['GroupPackageCount'],
							'HandlingUnits'		=> $parcel['GroupPackageCount'],
							'FreightClass' 		=> $freight_class,
							'Packaging'			=> 'SKID',
							'Description' 		=> $commodity_desc,
							'Weight'	   		=> array(

								'Units'		=> $this->labelapi_weight_unit,
								'Value'		=> $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product->get_weight(), $this->weight_unit ), 2 ),
							),

						);

						$dimensions = array( $product->get_length(), $product->get_width(), $product->get_height() );

						if ( $dimensions[0] && $dimensions[1] && $dimensions[2] ) {

							sort( $dimensions );

							$line_items['Dimensions'] = array(

								'Length' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
								'Width'  => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
								'Height' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
								'Units'  => $this->labelapi_dimension_unit
							);
						}

						// Increment Line Item Id only when new Product is added
						if ( $flag || !isset($executed_prouducts[$product_id]) ) {

							$executed_prouducts[$product_id] 	= $line_item_id;
							$line_item_id++;
						}

						$freight_line_items[] = $line_items;
					}
				}
			}

			foreach ( $fedex_packages as $key => $parcel ) {

			  $num_of_packages = isset( $parcel['num_of_packages'] ) && !empty( $parcel['num_of_packages'] ) ? $parcel['num_of_packages'] : 1;

              for ( $i=0; $i < $num_of_packages ; $i++) {  //looping packages based on no: of packages selected in order page

				$package_request = $this->get_fedex_api_request( $parcel, $package, $request_type );
				$package_count++;
				$parcel_request;
				
				$single_package_weight 	= $parcel['Weight']['Value'];
				$request        		= $package_request;

				if( !empty($parcel['service']) ) {

					$request['RequestedShipment']['ServiceType'] = $parcel['service'];
				}

				$parcel_value = (double) $parcel['InsuredValue']['Amount'] * (int)$parcel['GroupPackageCount'];

				$request['RequestedShipment']['TotalWeight'] = array(

					'Value'	=> $total_weight, 
					'Units'	=> $this->labelapi_weight_unit // valid values LB and KG
				);
				
				$commodoties    = array();
				$freight_class  = '';
				$this->customtotal = 0;
				$this->dry_ice_shipment=false;
				$this->dry_ice_total_weight = 0; 


				// Store parcels as line items
				$request['RequestedShipment']['RequestedPackageLineItems'] = array();
				
				if($package_count == 1 ) {
					//This function will add all the items in the order to the first package of the order request in order to create commercial invoice as per the api requirement while creating shipment.
					$commodoties = $this->get_package_one_commodoties( $fedex_packages );
					$parcel_request = $parcel;
				} else {
					$parcel_request = $parcel;
					if ( $parcel_request['packed_products'] ) {
						foreach ( $parcel_request['packed_products'] as $product ) {
							
							$unit_price 		= 0;
							$custom_value 		= 0;
							$unit_currency 		= $this->wf_get_fedex_currency();
							$custom_currency 	= $this->wf_get_fedex_currency();
							$product_id 		= wp_get_post_parent_id($product->get_id());	// Get Parent Id
							$main_product_id 	= $product->get_id();	// Get Product Id ( Variation Id if present )

							$multi_part_product = get_post_meta($main_product_id , '_ph_multi_part_product', 1);
							$multi_part_count 	= get_post_meta($main_product_id , '_ph_multi_part_addon_count', 1);

							if(empty($product_id)) {

								$product_id = $main_product_id;
							}

							$is_dry_ice_product = get_post_meta($product_id , '_wf_dry_ice', 1);
                            
                            //PDS-149
							if ( (empty($this->invoice_commodity_value) && $this->discounted_price) || $this->invoice_commodity_value == 'discounted_price' )
							{
								$order_object = wc_get_order($this->order->id);

								if( $order_object instanceof WC_Order )
								{	
									$order_items = $order_object->get_items();

									if( !empty($order_items) )
									{
										foreach ( $order_items as  $item_key => $item_values ) {

											$order_item_id = $item_values->get_variation_id();

											if( empty($order_item_id) )
											{
												$order_item_id = $item_values->get_product_id();
											}

											// Compare with Parent Id and Variation Id
											if( $order_item_id == $product_id || $order_item_id == $main_product_id  )
											{
												$product_unit_price 	= $item_values->get_total() / $item_values->get_quantity();
												$this->order_currency	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

												if( $this->commercial_invoice_order_currency ){

													$unit_price 	= $product_unit_price;
													$unit_currency 	= $this->order_currency;

													$custom_value 	 	= $unit_price;
													$custom_currency 	= $unit_currency;

												}else{

													$new_unit_price 	= apply_filters( 'ph_fedex_change_currency_to_fedex_currency', $product_unit_price, $this->order_currency, $this->wc_store_currency, $order_object );

													$unit_price 		= round( $this->convert_to_fedex_currency( $new_unit_price ), 2);
													$unit_currency 		= $this->wf_get_fedex_currency();

													$custom_value 	 	= $unit_price;
													$custom_currency 	= $unit_currency;
												}

												if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

													$unit_price 		= round( ($unit_price/$multi_part_count), 2);
													$custom_value 		= round( ($custom_value/$multi_part_count), 2);
												}

												break;
											}

										}
									}
								}

							}else{

								if( $this->commercial_invoice_order_currency ) {

									$order_object 			= wc_get_order($this->order->id);
									$unit_price 			= $this->get_product_price($product);
									$this->order_currency	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

									$unit_price 	= apply_filters( 'ph_fedex_change_currency_to_order_currency', $unit_price, $this->order_currency, $this->wc_store_currency, $order_object );
									$unit_currency 	= $this->order_currency;

									$custom_value 	 	= $unit_price;
									$custom_currency 	= $unit_currency;

								}else{

									$unit_price 	= round( $this->convert_to_fedex_currency($this->get_product_price($product)), 2);
									$unit_currency 	= $this->wf_get_fedex_currency();

									$custom_value 	 	= $unit_price;
									$custom_currency 	= $unit_currency;
								}

								if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

									$unit_price 		= round( ($unit_price/$multi_part_count), 2);
									$custom_value 		= round( ($custom_value/$multi_part_count), 2);
								}

							}

							$custom_declared_value = get_post_meta( $product_id, '_wf_fedex_custom_declared_value', true );

                            //PDS-149
							if ( ( !empty($custom_declared_value) && empty($this->invoice_commodity_value) ) || ( !empty($custom_declared_value) && $this->invoice_commodity_value == 'declared_price' ) ) {

								if( $this->commercial_invoice_order_currency ) {

									$order_object 			= wc_get_order($this->order->id);
									$this->order_currency 	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

									$declared_value 		= apply_filters( 'ph_fedex_change_currency_to_order_currency', $custom_declared_value, $this->order_currency, $this->wc_store_currency, $order_object );

									$unit_price 		= $declared_value;
									$unit_currency 		= $this->order_currency;
									$custom_value 		= $declared_value;
									$custom_currency 	= $this->order_currency;							

								}else{

									$declared_value 	= round($this->convert_to_fedex_currency($custom_declared_value), 2);
									$unit_price 		= $declared_value;
									$unit_currency 		= $this->wf_get_fedex_currency();
									$custom_value 		= $declared_value;
									$custom_currency 	= $this->wf_get_fedex_currency();

								}

								if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

									$unit_price 		= round( ($unit_price/$multi_part_count), 2);
									$custom_value 		= round( ($custom_value/$multi_part_count), 2);
								}
							}

							if( $is_dry_ice_product=='yes' ){
								$this->dry_ice_shipment = true;		
								$meta_exists=metadata_exists('post', $product_id, '_wf_dry_ice_weight');
								$dry_ice_weight=($meta_exists)?get_post_meta($product_id , '_wf_dry_ice_weight', 1):$product->get_weight();           // for backward compactibility ( added on 22/11/2018) 
								$this->dry_ice_total_weight += Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $dry_ice_weight, 'kg' )*$parcel['GroupPackageCount']; //Fedex support dry ice weight in KG only
							}

							if ( isset( $commodoties[ $main_product_id ] ) ) {
								$commodoties[ $main_product_id ]['Quantity'] ++;
								$commodoties[ $main_product_id ]['CustomsValue']['Amount'] += $custom_value;
								$this->customtotal += $custom_value;
								continue;
							}

							$product_name 		= html_entity_decode( $product->get_name() );
							$commodity_desc 	= html_entity_decode( get_post_meta( $product_id , '_ph_commodity_description', 1) );

							$commodity_desc 	= !empty($commodity_desc) ? $commodity_desc : $product_name;

							//Remove special-characters from Product name and description
							if( $this->remove_special_char == true ){
								$product_name 		= preg_replace('/[^A-Za-z0-9-() ]/', '', $product_name);
								$commodity_desc 	= preg_replace('/[^A-Za-z0-9-() ]/', '', $commodity_desc);
							}

							$commodity_desc = ( strlen( $commodity_desc ) >= 450 ) ? substr( $commodity_desc, 0, 445 ).'...' : $commodity_desc;
							
							$commodoties[ $main_product_id ] = array(
								'Name'                 => $product_name,
								'NumberOfPieces'       => 1,
								'Description'          => $commodity_desc,
								'CountryOfManufacture' => ( $country = get_post_meta( $product_id, '_wf_manufacture_country', true ) ) ? $country : $this->origin_country,
								'Weight'               => array(
									'Units'            => $this->labelapi_weight_unit,
									'Value'            => round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product->get_weight(), $this->weight_unit ), 2 ) ,
								),
								'Quantity'             => $parcel['GroupPackageCount'],
								'UnitPrice'            => array(
									'Amount'           => $unit_price,
									'Currency'         => $unit_currency,
								),
								'CustomsValue'         => array(
									'Amount'           => $custom_value,
									'Currency'         => $custom_currency,
								),
								'QuantityUnits' => 'EA'
							);

							$this->customtotal += $custom_value;
							$product_id = $product->get_type() == 'simple' ? $product->get_id() : $product->get_parent_id();

							if(empty($product_id))
							{
								$product_id = $main_product_id;
							}

							$wf_hs_code = get_post_meta( $product_id , '_wf_hs_code', 1 );
							
							// for backword compatiblity
							if(!$wf_hs_code){

								$product_data = wc_get_product( $product_id  );

								if( !empty($product_data) && is_object($product_data) && $product_data->has_attributes() )
								{
									$wf_hs_code = $product_data->get_attribute( 'wf_hs_code' );
								}
							}

							if( !empty($wf_hs_code) ){
								$commodoties[ $main_product_id ]['HarmonizedCode'] = $wf_hs_code;
							} else if ( !empty($this->global_hs_code) ) {
								$commodoties[ $main_product_id ]['HarmonizedCode'] = $this->global_hs_code;
							}
						}
					}
				}
				$commodoties=apply_filters('ph_fedex_commodities',$commodoties,$request,$fedex_packages);  
				
				if ( 'freight' === $request_type ) {
					
					// Line Items added seperately at the top 

				} else {
					// Work out the commodoties for CA shipments
					$special_servicetypes = array();

					$line_items_special_services	=	array();
					
					// Is this valid for a ONE rate? Smart post does not support it
					if ( $this->fedex_one_rate && '' === $request_type && isset($parcel_request['package_id']) && in_array( $parcel_request['package_id'], $this->fedex_one_rate_package_ids ) && count($fedex_packages) == 1 )
					{
						$this->packaging_type = $this->xa_get_countrywise_packagin_type( $parcel_request['package_id'], $this->origin_country );
						$request['RequestedShipment']['PackagingType'] = $this->packaging_type;

						if( in_array($this->origin_country, $this->fedexBoxCountries) && in_array($request['RequestedShipment']['PackagingType'], $this->fedexBox) ) {
							$request['RequestedShipment']['PackagingType'] = 'FEDEX_BOX';
						}
						
						if('US' === $package['destination']['country'] && 'US' === $this->origin_country ){
							$special_servicetypes[] = 'FEDEX_ONE_RATE';
							
						}
					}

					// Send COD option automatically for automatic label generation if order has been placed using COD.
					$order_payment_method = get_post_meta( $this->order_id, '_payment_method', true );
					if( (isset($_GET['cod']) && $_GET['cod'] === 'true' ) || ( ! isset($_GET['cod'])  && $order_payment_method == 'cod' ) ){
						if( ! ( $parcel['service'] == "FEDEX_GROUND" || $parcel['service'] == "GROUND_HOME_DELIVERY" ) ) {
							
							if(isset($this->order)) {
								$order_total 		= round( $this->order->get_total(), 2);
							}

							$special_servicetypes[] = 'COD';
							$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CodCollectionAmount']['Currency'] = $this->wf_get_fedex_currency();
							$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CodCollectionAmount']['Amount'] = isset( $order_total ) ? $this->convert_to_fedex_currency($order_total) : $parcel_value;
	
							$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CollectionType'] =$this->cod_collection_type;
							
							if( $package['destination']['country'] == 'IN' && $this->convert_to_inr_currency( $order_total ) >= 20000 ){
								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CollectionType'] = 'PERSONAL_CHECK';
							}
							
							if( $this->cod_collection_type == 'PERSONAL_CHECK' || $this->cod_collection_type == 'COMPANY_CHECK' || ( $package['destination']['country'] == 'IN' && $this->convert_to_inr_currency( $order_total ) >= 20000 )){		
								
								//check address
								if( strlen($package['destination']['address_1']) > 30 ){
									$address_1 = substr( $package['destination']['address_1'], 0, strpos( wordwrap($package['destination']['address_1'], 30), "\n") ); //Get first 30 char from $address_1
									$address_2 = str_replace($address_1, '', $package['destination']['address_1']) . ' ' . $package['destination']['address_2']; //Take remains of $address_1 + $address_2
								}else{
									$address_1 = $package['destination']['address_1'];
									$address_2 = $package['destination']['address_2'];
								}
								$country_code = $package['destination']['country'];
								$country_name = strtoupper( WC()->countries->countries[ $country_code ] );

								$phonenummeta 	= get_post_meta( $this->order->id , '_shipping_phone', 1);
								$phonenum 		= !empty($phonenummeta) ? $phonenummeta : $this->order->billing_phone;

								$addr = array(
									'Contact' => array(
										'PersonName' => $this->ph_replace_special_characters($package['destination']['first_name']) . ' ' . $this->ph_replace_special_characters($package['destination']['last_name']),
										'CompanyName' => $package['destination']['company'],
										'PhoneNumber' => $phonenum
									),
									'Address' => array(
										'StreetLines'         => array( $this->ph_replace_special_characters($address_1), $this->ph_replace_special_characters($address_2) ),
										'PostalCode'          => str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
										'City'                => strtoupper( $this->ph_replace_special_characters($package['destination']['city']) ),
										'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
										'CountryCode'         => $package['destination']['country'],
										'CountryName'		  => $country_name,
									)
								);
								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['FinancialInstitutionContactAndAddress'] = $addr;
								
								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['RemitToName'] = $this->ph_replace_special_characters($package['destination']['first_name']) . ' ' . $this->ph_replace_special_characters($package['destination']['last_name']);
							}
						}else{
							$package_cost = 0;
							foreach ( $parcel_request['packed_products'] as $product ) {
								$package_cost += $this->convert_to_fedex_currency($this->get_product_price($product));
							}

							if(isset($this->order)){
								$order_total 		= round( $this->order->get_total(), 2);
							}

							$line_items_special_services['SpecialServiceTypes'][] = 'COD';
							$line_items_special_services['CodDetail']['CollectionType'] = $this->cod_collection_type;
							$line_items_special_services['CodDetail']['CodCollectionAmount'] = array(
								'Amount'           => $package_cost,
								'Currency'         => $this->wf_get_fedex_currency(),
							);
	
							if(  $package['destination']['country'] == 'IN' && $this->convert_to_inr_currency( $order_total ) >= 20000 ){		
								if( ! ( $this->cod_collection_type == 'PERSONAL_CHECK' || $this->cod_collection_type == 'COMPANY_CHECK' ) ){
									$line_items_special_services['CodDetail']['CollectionType'] = 'PERSONAL_CHECK';
								}
								//check address
								if( strlen($package['destination']['address_1']) > 30 ){
									$address_1 = substr( $package['destination']['address_1'], 0, strpos( wordwrap($package['destination']['address_1'], 30), "\n") ); //Get first 30 char from $address_1
									$address_2 = str_replace($address_1, '', $package['destination']['address_1']) . ' ' . $package['destination']['address_2']; //Take remains of $address_1 + $address_2
								}else{
									$address_1 = $package['destination']['address_1'];
									$address_2 = $package['destination']['address_2'];
								}
								$country_code = $package['destination']['country'];
								$country_name = strtoupper( WC()->countries->countries[ $country_code ] );

								$phonenummeta 	= get_post_meta( $this->order->id , '_shipping_phone', 1);
								$phonenum 		= !empty($phonenummeta) ? $phonenummeta : $this->order->billing_phone;
								
								$addr = array(
									'Contact' => array(
										'PersonName' => $this->ph_replace_special_characters($package['destination']['first_name']) . ' ' . $this->ph_replace_special_characters($package['destination']['last_name']),
										'CompanyName' => $package['destination']['company'],
										'PhoneNumber' => $phonenum
									),
									'Address' => array(
										'StreetLines'         => array( $this->ph_replace_special_characters($address_1), $this->ph_replace_special_characters($address_2) ),
										'PostalCode'          => str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
										'City'                => strtoupper( $this->ph_replace_special_characters($package['destination']['city']) ),
										'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
										'CountryCode'         => $package['destination']['country'],
										'CountryName'		  => $country_name,
									)
								);
								$line_items_special_services['CodDetail']['FinancialInstitutionContactAndAddress'] = $addr;
								$line_items_special_services['CodDetail']['RemitToName'] = $this->ph_replace_special_characters($package['destination']['first_name']) . ' ' . $this->ph_replace_special_characters($package['destination']['last_name']);
							}
						}	
					}
					
					//$request['RequestedShipment']['SpecialServicesRequested']['SignatureOptionDetail']['OptionType']='SERVICE_DEFAULT';
					if( ( isset($_GET['sat_delivery']) && $_GET['sat_delivery'] === 'true' ) || ( $this->saturday_delivery_label && !isset($_GET['sat_delivery']) ) ) {
						
						$special_servicetypes[] = 'SATURDAY_DELIVERY';
					}
					
					$billing_email = $this->order->billing_email;					
					$email_recipients	=	array();					
					switch($this->email_notification){
						case 'CUSTOMER':
							$receipient_customer	= $this->notification_receiver($billing_email,	'RECIPIENT');
							if($receipient_customer){
								$email_recipients	=	$receipient_customer;
							}
							break;
							
						case 'SHIPPER':
							$receipient_shipper		= 	$this->notification_receiver($this->shipper_email,	'SHIPPER');
							if($receipient_shipper){
								$email_recipients	=	$receipient_shipper;
							}
							break;
							
						case 'BOTH':
							$receipient_customer	= $this->notification_receiver($billing_email,	'RECIPIENT');
							if($receipient_customer){
								$email_recipients	=	$receipient_customer;
							}
							// $receipient_shipper		= 	$this->notification_receiver($this->shipper_email,	'SHIPPER');
							// if($receipient_shipper){
							// 	$email_recipients[]	=	$receipient_shipper;
							// }
							break;
							
						default:							
							break;
					}

					if(is_array($email_recipients) && sizeof($email_recipients)>0){
						$special_servicetypes[] = 'EVENT_NOTIFICATION';
						$events_requested	= array(
							'ON_DELIVERY',
							'ON_EXCEPTION',
							'ON_SHIPMENT',
							'ON_TENDER',
							'ON_PICKUP_DRIVER_ARRIVED',
							'ON_PICKUP_DRIVER_ASSIGNED',
							'ON_PICKUP_DRIVER_DEPARTED',
							'ON_PICKUP_DRIVER_EN_ROUTE',
						);
						$request['RequestedShipment']['SpecialServicesRequested']['EventNotificationDetail']['EventNotifications']['Role']	= 'SHIPPER';
						$request['RequestedShipment']['SpecialServicesRequested']['EventNotificationDetail']['EventNotifications']['Events']	=  $events_requested;
						$request['RequestedShipment']['SpecialServicesRequested']['EventNotificationDetail']['EventNotifications']['NotificationDetail'] = $email_recipients;
						$request['RequestedShipment']['SpecialServicesRequested']['EventNotificationDetail']['EventNotifications']['FormatSpecification']['Type']	= 'HTML';
						
					}

					//FedEx Premium delivery service
					if ( $this->home_delivery_premium && $parcel['service'] == "GROUND_HOME_DELIVERY" && isset($_GET['wf_fedex_createshipment']) ) {

						$special_servicetypes[] = 'HOME_DELIVERY_PREMIUM';
						$request['RequestedShipment']['SpecialServicesRequested']['HomeDeliveryPremiumDetail'] = array(
							'HomeDeliveryPremiumType' => $this->home_delivery_premium_type,
							'PhoneNumber' 			  => $this->freight_shipper_phone_number,
						);

						//If Date - Certain is selectaed and date passed from Order Edit page it will take here, Date is Required only for Date - Certain.
						if ( isset($_GET['home_delivery_date']) && !empty($_GET['home_delivery_date']) && $this->home_delivery_premium_type == 'DATE_CERTAIN' ) {
							$request['RequestedShipment']['SpecialServicesRequested']['HomeDeliveryPremiumDetail']['Date'] = $_GET['home_delivery_date'];
						}
					}

					if(!empty($special_servicetypes)){
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = $special_servicetypes;
					}
				}
				
				

				// Remove temp elements
				unset( $parcel_request['freight_class'] );
				unset( $parcel_request['packed_products'] );
				unset( $parcel_request['package_id'] );
				unset( $parcel_request['service'] );

				if( $this->commercial_invoice_order_currency && isset($parcel_request['InsuredValue']) ) {

					$insurance 				= $parcel_request['InsuredValue']['Amount'];
					$order_object 			= wc_get_order($this->order->id);
					$insurance_currency 	= isset( $this->order_currency ) ? $this->order_currency : $this->ph_get_fedex_currency_for_order( $this->order->get_currency() );

					$parcel_request['InsuredValue']['Amount'] 	= apply_filters( 'ph_fedex_change_currency_to_order_currency', $insurance, $insurance_currency, $this->wc_store_currency, $order_object );
					$parcel_request['InsuredValue']['Currency'] = $insurance_currency; 
				}

				if ( ! $this->insure_contents || 'smartpost' === $request_type || empty($parcel['InsuredValue']['Amount'])) {
					unset( $parcel_request['InsuredValue'] );
				}				
				if ( 'smartpost' === $request_type ) {
					$request['RequestedShipment']['PackageCount'] = 1;
					$parcel_request = array_merge( array( 'SequenceNumber' => 1 ), $parcel_request );
				
				}else{
					$request['RequestedShipment']['PackageCount'] = $total_packages;
					$parcel_request = array_merge( array( 'SequenceNumber' => $package_count ), $parcel_request );
				}

				if( $this->dry_ice_shipment ){
					$line_items_special_services['SpecialServiceTypes'][]	=	'DRY_ICE';
					$line_items_special_services['DryIceWeight']			=	array(
						'Units' => 'KG',
						'Value' => round($this->dry_ice_total_weight, 2)
					);
				}

				//PDS-179
				if ( 'smartpost' !== $request_type ){
					
					if(isset($_GET['signature_option']) && !empty($_GET['signature_option']) ){

						$line_items_special_services['SpecialServiceTypes'][]	= 'SIGNATURE_OPTION';
						$line_items_special_services['SignatureOptionDetail']	= array( 'OptionType'=>$_GET['signature_option'] );
						update_post_meta( $this->order->id, 'ph_fedex_signature_option_meta', array_search($_GET['signature_option'], $this->prioritizedSignatureOption) );

					} else if( !isset( $_GET['signature_option'] )) {

						$bulk_label_signature  	= isset($parcel['signature_option']) && !empty($parcel['signature_option']) ? $parcel['signature_option'] : $this->signature_option;
						$bulk_label_signature 	= $this->prioritizedSignatureOption[$bulk_label_signature];
						
						if( !empty($bulk_label_signature)){

							$line_items_special_services['SpecialServiceTypes'][]	= 'SIGNATURE_OPTION';
							$line_items_special_services['SignatureOptionDetail']	= array( 'OptionType'=> $bulk_label_signature );
						}
					}
				}
				if( isset($parcel_request['signature_option']) ){

					unset( $parcel_request['signature_option'] );
				}
				if( isset($parcel_request['boxName']) ){

					unset( $parcel_request['boxName'] );
				}
				if( isset($parcel_request['num_of_packages']) ){

					unset( $parcel_request['num_of_packages'] );
				}

				// Dangerous Goods
				$dangerous_goods = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_dangerous_goods' );
				$hazmat_products = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_hazmat_products' );

				if( !empty($dangerous_goods)  && empty($hazmat_products) ){
					$dangerous_goods_regulation	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_dg_regulations');
					$dangerous_goods_accessibility	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_dg_accessibility');
					$line_items_special_services['SpecialServiceTypes'][]	= 'DANGEROUS_GOODS';
					$line_items_special_services['DangerousGoodsDetail']	= array(
						'Regulation'	=> ( ! empty($dangerous_goods_regulation) && is_array($dangerous_goods_regulation) ) ? array_pop($dangerous_goods_regulation) :'DOT',
						'Accessibility'	=> ( empty($dangerous_goods_accessibility) || (is_array($dangerous_goods_accessibility) && in_array('INACCESSIBLE', $dangerous_goods_accessibility) ) ) ? 'INACCESSIBLE' :  array_pop($dangerous_goods_accessibility)
					);
				}

				if( !empty($hazmat_products) ){

					$hazmat_id_num	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_id_num');
					$hazmat_packaging_group	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_packaging_group');
					$hazmat_proper_shipping_name	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_proper_shipping_name');
					$hazmat_hazard_class	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_hazard_class');
					$hazmat_subsidairy_class	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_subsidiary_classes');
					$hazmat_label_text	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_ph_fedex_hp_label_text');

					$line_items_special_services['SpecialServiceTypes'][]	= 'DANGEROUS_GOODS';
					$line_items_special_services['DangerousGoodsDetail']['Options']	= 'HAZARDOUS_MATERIALS';

					$i = 0;

					foreach ( $hazmat_products as $product_key => $value) {

						$product_weight = 0;

						foreach ($parcel['packed_products'] as $key => $product) {
							if( $product_key == $product->id || $product_key == $product->variation_id )
							{
								$product_weight += $product->get_weight();
							}
						}

						$line_items_special_services['DangerousGoodsDetail']['Containers'][$i] = array(

							'HazardousCommodities' => array(
								'Description' => array(
									'Id' 			=> ( ! empty($hazmat_id_num[$product_key]) ) ? $hazmat_id_num[$product_key] : '',
									'PackingGroup'	=> ( ! empty($hazmat_packaging_group[$product_key]) ) ? $hazmat_packaging_group[$product_key] : 'DEFAULT',
									'ProperShippingName' => ( ! empty($hazmat_proper_shipping_name[$product_key]) ) ? $hazmat_proper_shipping_name[$product_key] : '',
									'HazardClass' 	=> ( ! empty($hazmat_hazard_class[$product_key]) ) ? $hazmat_hazard_class[$product_key] : '',
									'LabelText' => ( ! empty($hazmat_label_text[$product_key]) ) ? $hazmat_label_text[$product_key] : '',

								), 
								'Quantity' 	=> array(
									'Amount' 	=> round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product_weight , $this->weight_unit ), 2 ),
									'Units' 	=> $this->labelapi_weight_unit,
								),
							),
						);
						
						if( !empty($hazmat_subsidairy_class[$product_key]) )
						{
							$subsidairy_class 	= $hazmat_subsidairy_class[$product_key];
							$sub_classes 		= explode(',', $subsidairy_class);
						
							if( is_array($sub_classes) && !empty($sub_classes) )
							{
								foreach ($sub_classes as $key => $sub_class) {
									$line_items_special_services['DangerousGoodsDetail']['Containers'][$i]['HazardousCommodities']['Description']['SubsidiaryClasses'][] = trim($sub_class," ");
								}
							}
						}

						$i++;
					}

					if( $this->hazmat_package_enabled )
					{
						$packaging_unit = $this->hazmat_package_type.$this->hazmat_package_material;
						$line_items_special_services['DangerousGoodsDetail']['Packaging'] = array(
							'Count' => '1',
							'Units' => (string) $packaging_unit,
						);
					}
					
					$line_items_special_services['DangerousGoodsDetail']['EmergencyContactNumber']	= $this->freight_shipper_phone_number;
					$line_items_special_services['DangerousGoodsDetail']['Offeror']	= 'SHIPPER';		
				}
				
				$special_servicetype = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_special_service_types' );
				if( ! empty($special_servicetype) ) {
					foreach( $special_servicetype as $special_servicetype_key => $special_servicetype_value ) {
						
						if($special_servicetype_value == 'ALCOHOL') {
							$receipient_type = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_sst_alcohal_recipient' );
							$line_items_special_services['SpecialServiceTypes'][]	= 'ALCOHOL';
							$alcohal_recipient_type	= is_array($receipient_type) ? current($receipient_type) : '';
							$line_items_special_services['AlcoholDetail']		= array(
								'RecipientType'		=> ! empty($alcohal_recipient_type) ? $alcohal_recipient_type : 'CONSUMER',
							);
						}
					}
				}
				
				// Non Standard Products
				$non_standard_product = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_non_standard_product' );

				if ( !empty($non_standard_product) ) {
					$line_items_special_services['SpecialServiceTypes'][] = 'NON_STANDARD_CONTAINER';
				}

				//PDS-121
				if( !empty($line_items_special_services) && !isset($parcel_request['manual_package']) ) {

					$parcel_request['SpecialServicesRequested']	=	$line_items_special_services;
				}

				if(isset($parcel_request['manual_package'])){

					unset($parcel_request['manual_package']);
				}

				$reff = array();				
				
				$reff['CustomerReferences'][] =  array( 'CustomerReferenceType' => 'CUSTOMER_REFERENCE', 'Value' => $this->order->get_order_number() );
				$reff['CustomerReferences'][] =  array( 'CustomerReferenceType' => 'INVOICE_NUMBER', 'Value' => $this->order->get_order_number() );
				
				if( $this->is_international && $this->csb5_shipments ) {

					if( !empty($this->ad_code) ) {

						$reff['CustomerReferences'][] =  array( 'CustomerReferenceType' => 'P_O_NUMBER', 'Value' => $this->ad_code );
					}

					$invoice_date 	= date("dmy");
					$gst 			= 0;
					$cart_tax 		= $this->order->get_cart_tax();
					$shipping_tax 	= $this->order->get_shipping_tax();

					if( $this->gst_shipment == 'G' ) {

						$total_tax 	= $this->order->get_total_tax();
						$gst 		= $total_tax;
					}

					// CSB Dept Num
					$dept_num = 'CSB5/'.$this->gst_shipment.'/'.$this->csb_termsofsale.'/'.$this->under_bond.'/E/'.$this->meis_shipment.'/'.$gst.'/'.$invoice_date;

					$reff['CustomerReferences'][] =  array( 'CustomerReferenceType' => 'DEPARTMENT_NUMBER', 'Value' => $dept_num );
				}

				$parcel_request += $reff;				
				
				//Priority boxed no need dimensions
				if( $this->packaging_type != 'YOUR_PACKAGING' ){
					unset( $parcel_request['Dimensions'] );
				}
				$parcel_request['ItemDescription'] = isset($this->settings['item_description']) ? $this->settings['item_description'] : '';
				$parcel_request['ItemDescriptionForClearance'] = isset($this->settings['item_description']) ? $this->settings['item_description'] : '';

				$request['RequestedShipment']['RequestedPackageLineItems'][] = $parcel_request;				

				$indicia = $this->indicia;
				
				if($indicia == 'AUTOMATIC' && $single_package_weight >= 1)
					$indicia = 'PARCEL_SELECT';
				elseif($indicia == 'AUTOMATIC' && $single_package_weight < 1)
					$indicia = 'PRESORTED_STANDARD';				
				
				// Smart post
				if ( 'smartpost' === $request_type ) {

					$request['RequestedShipment']['SmartPostDetail'] = array(
						'Indicia'              => $indicia,
						'HubId'                => $this->smartpost_hub,
						'AncillaryEndorsement' => 'ADDRESS_CORRECTION',
						'SpecialServices'      => ''
					);
					
					// Smart post does not support insurance, but is insured up to $100
					if ( $this->insure_contents && round( $parcel_value ) > 100 ) {
						return false;
					}
				} elseif ( $this->insure_contents ) {
					
					if( $this->commercial_invoice_order_currency ) {
						
						$order_object 			= wc_get_order($this->order->id);
						$insurance_currency 	= isset( $this->order_currency ) ? $this->order_currency : $this->ph_get_fedex_currency_for_order( $this->order->get_currency() );
						$total_insurance 		= apply_filters( 'ph_fedex_change_currency_to_order_currency', $parcel_value, $insurance_currency, $this->wc_store_currency, $order_object );
						
					}else{

						$total_insurance 		= round( $parcel_value, 2 );
						$insurance_currency 	= $this->wf_get_fedex_currency();
					}
                    
					if (!empty($total_insurance)) {
						$request['RequestedShipment']['TotalInsuredValue'] = array(
							'Amount'   => $total_insurance,
							'Currency' => $insurance_currency,
						);
					}
				}

				if( $hazmat_products ) {

					$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'OP_900';
					$request['RequestedShipment']['ShippingDocumentSpecification']['Op900Detail'] = array(
						'Format'		=>	array(
							'ImageType'	=>	'PDF',
							'StockType'	=>	'OP_900_LL_B',
						),
						'SignatureName'		=>	$this->freight_shipper_person_name,
					);
					
				}
				
				if ( 'freight' === $request_type ) {

					$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'] 	= [];
					
					$request['RequestedShipment']['FreightShipmentDetail'] = array(

						'Role'					=> 'SHIPPER',
						'TotalHandlingUnits'	=> $total_packages,
					);

					//if any of dimension value exceed limit 180, Then special service EXTREME_LENGTH need to fill.
					foreach ($request['RequestedShipment']['RequestedPackageLineItems'] as $key => $item) {
						
						if( !empty($item['Dimensions']) &&  max(array_map( array($this,'dimensions_in_inches'), array($item['Dimensions']['Length'], $item['Dimensions']['Width'], $item['Dimensions']['Height']) ) )  >= 96 ) {

							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'OVER_LENGTH';
							
							$request['RequestedShipment']['FreightShipmentDetail']['SpecialServicePayments'] = array(

								'SpecialService' => 'OVER_LENGTH',
								'PaymentType'	 => 'SHIPPER',
							);
							break;
						}
					}

					$request['RequestedShipment']['FreightShipmentDetail']['LineItems'] = array();
					$request['RequestedShipment']['FreightShipmentDetail']['LineItems'] = array_values($freight_line_items);

					if( $this->fed_req->get_charges_payment_type() == 'SENDER' ){
						$request['RequestedShipment']['FreightShipmentDetail']['FedExFreightAccountNumber'] = strtoupper( $this->freight_number );
						$request['RequestedShipment']['FreightShipmentDetail']['FedExFreightBillingContactAndAddress'] =  array(
							'Address'                             => array(
								'StreetLines'                        => array( strtoupper( $this->freight_bill_street ), strtoupper( $this->freight_billing_street_2 ) ),
								'City'                               => strtoupper( $this->freight_billing_city ),
								'StateOrProvinceCode'                => strtoupper( $this->freight_billing_state ),
								'PostalCode'                         => strtoupper( $this->freight_billing_postcode ),
								'CountryCode'                        => strtoupper( $this->freight_billing_country )
							)
						);
					}else{
						$request['RequestedShipment']['FreightShipmentDetail']['AlternateBilling'] = $this->fed_req->get_alternate_address();
						
						$request['RequestedShipment']['ShippingChargesPayment']['PaymentType'] = 'SENDER';
						$request['RequestedShipment']['ShippingChargesPayment']['Payor']['ResponsibleParty']['AccountNumber'] = $this->shipping_payor_acc_no;

						$request['RequestedShipment']['FreightShipmentDetail']['AlternateBilling']['AccountNumber'] = $this->shipping_payor_acc_no;
						
					}
					
					$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = $this->freight_document_type;

					$request['RequestedShipment']['ShippingDocumentSpecification']['FreightBillOfLadingDetail']['Format'] = array(
						'ImageType' 		=> 'PDF',
						'StockType' 		=> 'PAPER_LETTER',
					);

					// Lift Gate delivery and pickup service only allowed for LTL freight Services
					if( in_array( $request['RequestedShipment']['ServiceType'], $this->fedex_ltl_freight_services) ) {
						if( $this->order->get_meta( 'xa_fedex_lift_gate_for_delivery', true ) ) {
							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_DELIVERY';
						}

						if( ! empty($this->settings['lift_gate_for_pickup']) && $this->settings['lift_gate_for_pickup'] == 'yes' ) {
							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_PICKUP';
						}
					}

					// Inside pickup and delivery available for all freight services i.e. for both LTL Freight and Express Freight
					if( $this->order->get_meta( 'xa_fedex_inside_delivery', true ) ) {
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'INSIDE_DELIVERY';
					}

					if( ! empty($this->settings['inside_pickup']) && $this->settings['inside_pickup'] == 'yes' ) {
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'INSIDE_PICKUP';
					}
					
				}
				
				
					
				$core_countries =['US'];
				if ($this->origin_country !== $package['destination']['country'] || !in_array($this->origin_country,array('US'))) {
					
					$this->customs_duties_payer			= apply_filters('xa_shipping_duties_payer',$this->customs_duties_payer, $package );
					
					$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment'] = array(
						'PaymentType' => $this->customs_duties_payer == 'THIRD_PARTY_ACCOUNT'  ? 'THIRD_PARTY' : $this->customs_duties_payer
					);
					

					// If payor is not a recipient then account details is not needed
					if( $this->customs_duties_payer == 'SENDER' ){
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
							'AccountNumber'           => strtoupper( $this->account_number ),
							'CountryCode'             => $this->origin_country
						);
					}elseif ( $this->customs_duties_payer == 'THIRD_PARTY' ){
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['PaymentType'] = 'RECIPIENT';

						$require ['CustomsClearanceDetailBrokers']['AccountNumber'] = $this->broker_acc_no;
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'BROKER_SELECT_OPTION';
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Type'] = 'IMPORT';

						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['AccountNumber'] = $this->broker_acc_no;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Contact']['PersonName'] = $this->broker_name;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Contact']['CompanyName'] = $this->broker_company;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Contact']['PhoneNumber'] = $this->broker_phone;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Contact']['EMailAddress'] = $this->broker_email;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Address']['StreetLines'] = $this->broker_address;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Address']['City'] = $this->broker_city;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Address']['StateOrProvinceCode'] = $this->broker_state;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Address']['PostalCode'] = $this->broker_zipcode;
						$request['RequestedShipment']['CustomsClearanceDetail']['Brokers']['Broker']['Address']['CountryCode'] = $this->broker_country;
					}elseif ( $this->customs_duties_payer == 'THIRD_PARTY_ACCOUNT' ) {

						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
							'AccountNumber' 	=> strtoupper( $this->third_party_acc_no ),
						);
					}

					if( isset($this->document_content) && !empty($this->document_content) ) {

						$request['RequestedShipment']['CustomsClearanceDetail']['DocumentContent'] = $this->document_content;
					}

					if( $this->commercial_invoice_order_currency ) {
						
						$custom_currency 	= isset( $this->order_currency ) ? $this->order_currency : $this->ph_get_fedex_currency_for_order( $this->order->get_currency() );
					}else{
						$custom_currency 	= $this->wf_get_fedex_currency();
					}

					$request['RequestedShipment']['CustomsClearanceDetail']['CustomsValue'] = array('Amount' => round($this->customtotal, 2), 'Currency' => $custom_currency );
					
					if( !empty($commodoties) && is_array($commodoties) ){
						$request['RequestedShipment']['CustomsClearanceDetail']['Commodities'] = array_values( $commodoties );
					}

					//B13 export
					if($this->origin_country=='CA'){ //International shipment from CA other than US
						if(isset($this->order->id) && ($this->order->shipping_country != 'US' && $this->order->shipping_country != 'CA' && $this->order->shipping_country != 'PR' && $this->order->shipping_country != 'VI') ){
							//$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail']['ExportComplianceStatement']='NOT_REQUIRED';//'AESX20160102123456';
							$export_compliance = array(
								'B13AFilingOption' => 'NOT_REQUIRED',
								'ExportComplianceStatement' => '02',
							);
							$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail'] =$export_compliance;
						}
					}
					if(isset($_GET['export_compliance'])){		
						$export_comp_code = $_GET['export_compliance'];
						$export_comp_code = preg_replace("/[^a-zA-Z0-9\s]/", "", $export_comp_code);

						if(!empty($export_comp_code)){

							$fed_currency = $this->wf_get_fedex_currency();
							$b13_currency = "CAD";
							$woocommerce_currency_conversion_rate = get_option('woocommerce_multicurrency_rates');
							$b13_order_cost = $this->customtotal;
							if($fed_currency != $b13_currency && !empty($woocommerce_currency_conversion_rate)){

								$b13_currency_rate = $woocommerce_currency_conversion_rate[$b13_currency];
								$fed_currency_rate = $woocommerce_currency_conversion_rate[$fed_currency];

								$conversion_rate = $b13_currency_rate / $fed_currency_rate;
								$b13_order_cost *= $conversion_rate;
							}

							if( $this->origin_country === 'CA' && ( $b13_order_cost >= 2000 && ($this->order->shipping_country != 'US' && $this->order->shipping_country != 'CA' && $this->order->shipping_country != 'PR' && $this->order->shipping_country != 'VI'))) {
								
								update_post_meta( $this->order_id, '_wf_fedex_export_compliance', $export_comp_code );
								$export_compliance = array(
									'B13AFilingOption' => 'FILED_ELECTRONICALLY',
									'ExportComplianceStatement' => $export_comp_code,
								);
								$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail'] = $export_compliance;
								
							}
						}
					}
					
					if( !in_array($this->origin_country,$core_countries)){
						$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice'] = array(
							'Purpose' => $this->customs_ship_purpose
						);
					}

					if( $this->commercial_invoice && $this->is_international ) {

						if( !empty($this->special_instructions) ) {

							$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['SpecialInstructions'] = $this->special_instructions;
						}

						if( isset($this->shipment_comments) && !empty($this->shipment_comments) ) {

							$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['Comments'] = $this->shipment_comments;
						}

						if( !empty($this->payment_terms) ) {

							$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['PaymentTerms'] = $this->payment_terms;
						}

						$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['CustomerReferences'] = array();

						$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['CustomerReferences'][] = array(

							'CustomerReferenceType' 	=> 'INVOICE_NUMBER',
							'Value'						=> $this->order->get_order_number(),
						);

					}

					if( $this->commercial_invoice_shipping && $this->is_international )
					{
						
						$shipping_total 	= $this->order->get_shipping_total();
						
						if( $this->commercial_invoice_order_currency ) {

							$order_object 		= wc_get_order($this->order->id);
							$shipping_cost 		= round( $shipping_total, 2);
							$shipping_currency 	= isset( $this->order_currency ) ? $this->order_currency : $this->ph_get_fedex_currency_for_order( $this->order->get_currency() );
							
						}else{

							$new_shipping_total = apply_filters( 'ph_fedex_change_currency_to_fedex_currency', $shipping_total, $this->order->get_currency(), $this->wc_store_currency, $this->order );

							$shipping_cost 		= round( $this->convert_to_fedex_currency( $new_shipping_total ), 2);
							$shipping_currency 	= $this->wf_get_fedex_currency();
						}
						
						$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice']['FreightCharge'] = array(

							'Amount'   => $shipping_cost,
							'Currency' => $shipping_currency,
						);
					}

					//USMCA Certificate
					if( $this->usmca_certificate && $this->is_international )
					{

						$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'USMCA_CERTIFICATION_OF_ORIGIN';
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['Format'] = array(
							'ImageType' 		=> 'PDF',
							'StockType' 		=> 'PAPER_LETTER',
						);
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['BlanketPeriod'] = array(
							'Begins' 	=> $this->blanket_begin_period,
							'Ends' 		=> $this->blanket_end_period,
						);
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['CertifierSpecification'] = $this->certifier_specification;
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['ImporterSpecification']  = $this->importer_specification;
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['ProducerSpecification']  = $this->producer_specification;

						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['Producer'] = array(
							'AccountNumber' 	=> strtoupper( $this->account_number ),
							'Tins' 				=> array(
								'TinType' => isset( $this->settings['tin_type'] ) ? $this->settings['tin_type'] : 'BUSINESS_STATE',
								'Number'  => isset($package['origin']['tin_number']) ? $package['origin']['tin_number'] : $this->tin_number,
							),
							'Contact' 				=> array(
								'PersonName' 		  => isset($this->settings['shipper_person_name']) ? $this->settings['shipper_person_name'] : '',
								'CompanyName' 		  => isset($this->settings['shipper_company_name']) ? $this->settings['shipper_company_name'] : '',
								'PhoneNumber' 		  => isset($this->settings['shipper_phone_number']) ? $this->settings['shipper_phone_number'] : '',
								'EMailAddress' 		  => isset($this->settings['shipper_email']) ? $this->settings['shipper_email'] : '', 
							),
							'Address' 				=> array(
								'StreetLines' 		  	=> isset($this->settings['frt_shipper_street']) ? $this->settings['frt_shipper_street'] : '',
								'City' 		  			=> isset($this->settings['freight_shipper_city']) ? $this->settings['freight_shipper_city'] : '',
								'StateOrProvinceCode' 	=> $this->origin_state,
								'PostalCode' 		 	=> isset($this->settings['origin']) ? $this->settings['origin'] : '',
								'CountryCode'	  		=> $this->origin_country,
								'CountryName' 			=> strtoupper( WC()->countries->countries[ $this->origin_country ] ),
								'Residential' 		  	=> isset($this->settings['shipper_residential']) && $this->settings['shipper_residential'] == 'yes' ? true : false,
							),
						);
						
						$digital_signature = isset($this->settings['digital_signature']) && !empty($this->settings['digital_signature']) ? true : false;

						if($digital_signature){
							$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCertificationOfOriginDetail']['CustomerImageUsages'] = array(
								'Type' 	=> 'SIGNATURE',
								'Id' 	=> 'IMAGE_2',
							);	
						}	
					}

					//USMCA Commercial Invoice Certificate Of Origin
					if( $this->usmca_ci_certificate_of_origin && $this->is_international )
					{
						
						$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'USMCA_COMMERCIAL_INVOICE_CERTIFICATION_OF_ORIGIN';
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCommercialInvoiceCertificationOfOriginDetail']['Format'] = array(
							'ImageType' 		=> 'PDF',
							'StockType' 		=> 'PAPER_LETTER',
						);
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCommercialInvoiceCertificationOfOriginDetail']['CertifierSpecification'] = $this->certifier_specification;
						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCommercialInvoiceCertificationOfOriginDetail']['ProducerSpecification']  = $this->producer_specification;

						$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCommercialInvoiceCertificationOfOriginDetail']['Producer'] = array(
							'AccountNumber' 	=> strtoupper( $this->account_number ),
							'Tins' 				=> array(
								'TinType' => isset( $this->settings['tin_type'] ) ? $this->settings['tin_type'] : 'BUSINESS_STATE',
								'Number'  => isset($package['origin']['tin_number']) ? $package['origin']['tin_number'] : $this->tin_number,
							),
							'Contact' 				=> array(
								'PersonName' 		  => isset($this->settings['shipper_person_name']) ? $this->settings['shipper_person_name'] : '',
								'CompanyName' 		  => isset($this->settings['shipper_company_name']) ? $this->settings['shipper_company_name'] : '',
								'PhoneNumber' 		  => isset($this->settings['shipper_phone_number']) ? $this->settings['shipper_phone_number'] : '',
								'EMailAddress' 		  => isset($this->settings['shipper_email']) ? $this->settings['shipper_email'] : '', 
							),
							'Address' 				=> array(
								'StreetLines' 		  	=> isset($this->settings['frt_shipper_street']) ? $this->settings['frt_shipper_street'] : '',
								'City' 		  			=> isset($this->settings['freight_shipper_city']) ? $this->settings['freight_shipper_city'] : '',
								'StateOrProvinceCode' 	=> $this->origin_state,
								'PostalCode' 		 	=> isset($this->settings['origin']) ? $this->settings['origin'] : '',
								'CountryCode'	  		=> $this->origin_country,
								'CountryName' 			=> strtoupper( WC()->countries->countries[ $this->origin_country ] ),
								'Residential' 		  	=> isset($this->settings['shipper_residential']) && $this->settings['shipper_residential'] == 'yes' ? true : false,
							),
						);
						
						$digital_signature = isset($this->settings['digital_signature']) && !empty($this->settings['digital_signature']) ? true : false;

						if($digital_signature){
							$request['RequestedShipment']['ShippingDocumentSpecification']['UsmcaCommercialInvoiceCertificationOfOriginDetail']['CustomerImageUsages'] = array(
								'Type' 	=> 'SIGNATURE',
								'Id' 	=> 'IMAGE_2',
							);	
						}	
					}
				}

				// Add request				
				$request=apply_filters('wf_fedex_request',$request,$this->order, $parcel,$fedex_packages); // to support Snippet - Adjust importer price for international Shipment FedEx
				$requests[] = $request;
			}
		  }				
		}
		return $requests;
	}
	
	private function xa_get_countrywise_packagin_type( $packaging_type, $origin_country ){
		$eu_boxes = array(
			'FEDEX_SMALL_BOX' 		=> 'FEDEX_BOX',
			'FEDEX_MEDIUM_BOX' 		=> 'FEDEX_BOX',
			'FEDEX_LARGE_BOX' 		=> 'FEDEX_BOX',
			'FEDEX_EXTRA_LARGE_BOX' => 'FEDEX_BOX',
		);
		
		global $woocommerce;
		$country_obect			= new WC_Countries();
		
		if( method_exists($country_obect, 'get_continent_code_for_country') //method 'get_continent_code_for_country' is not avail in older WC versions
			&& $country_obect->get_continent_code_for_country($origin_country) === 'EU'
			&& !empty($eu_boxes[ $packaging_type ]) ) {
			return $eu_boxes[ $packaging_type ];
		}else{
			return $packaging_type;
		}
	}
	
	/**
	* @param $product wf_product_object
	* @return int Custom Declared Value (Fedex) | Product Selling Price. <br />The Insurance amount of the product to get reimbursed from Fedex.
	*/
	public function wf_get_insurance_amount( $product ) {
		global $woocommerce;
		if( $woocommerce->version > 2.7 ) {
			$parent_id = $product->get_parent_id();
			$product_id = ! empty( $parent_id ) ? $parent_id : $product->get_id();
		}
		else {
			$product_id = ($product instanceof WC_Product_Variable) ? $product->parent->id : $product->id ;
		}
		$insured_price = get_post_meta( $product_id, '_wf_fedex_custom_declared_value', true );
		$product_price = ! empty( $this->get_product_price($product) ) ? $this->get_product_price($product) : 0;
		return ( ! empty( $insured_price ) ? $insured_price : $product_price );	
	}


	/**
	* return details of FedEx custome field in product page (Eg: Dangerous Goods).
	* Return array of product ids and product option value
	*/
	private function xa_get_custom_product_option_details( $packed_products, $option_mame ){
		
		global $woocommerce;
		$products_with_value = array();
		foreach ( $packed_products as $product ) {
			$product = $this->wf_load_product($product);
			$option = get_post_meta( $product->get_id() , $option_mame, 1 );
			
			$parent_id 	= ( WC()->version > '2.7' ) ? $product->get_parent_id() : ( isset($product->parent->id) ? $product->parent->id : 0 );

			if( $option_mame == '_dangerous_goods' && ! metadata_exists('post', $product->get_id(), '_dangerous_goods') ) {
				$product_id = ! empty( $parent_id ) ? $parent_id : $product->get_id();
				$option = get_post_meta( $product_id , $option_mame, 1 );
			}

			if( $option_mame == '_hazmat_products' && ! metadata_exists('post', $product->get_id(), '_hazmat_products') ) {
				$product_id = ! empty( $parent_id ) ? $parent_id : $product->get_id();
				$option = get_post_meta( $product_id , $option_mame, 1 );
			}
			
			if( !empty($option) && $option != 'no' ){
				$products_with_value[ $product->get_id() ] = $option;
			}
			elseif( ! empty($parent_id) ) {
				$option = get_post_meta( $parent_id , $option_mame, 1 );
				if( !empty($option) && $option != 'no' ){
					$products_with_value[ $product->get_id() ] = $option;
				}
			}
		}
		return $products_with_value;
	}

	private function dimensions_in_inches($value){
		if( !is_numeric($value) )
			return $value;
		if( $this->dimension_unit == 'in' )
			return $value;
		return $value * 0.393701;
	}

	private function is_refunded_item( $order, $item_id ){
		$qty = 0;
		foreach ( $order->get_refunds() as $refund ) {
			foreach ( $refund->get_items('line_item') as $refunded_item ) {
				if ( isset( $refunded_item['product_id'] ) && $refunded_item['product_id'] == $item_id ) {
					$qty += $refunded_item['qty'];
				}
			}
		}
		return $qty * -1;
	}

	public function wf_get_package_from_order($order){
		$this->order_object = $order;
		$orderItems = $order->get_items();
		foreach($orderItems as $orderItem){
			if( $refd_qty = $this->is_refunded_item($order, $orderItem['product_id']) ){
				if( $orderItem['qty'] - $refd_qty <= 0 ){
					continue;
				}
				else{
					$orderItem['qty'] = $orderItem['qty'] - $refd_qty;
				}
			}
			$product_data   = wc_get_product( $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'] );
			if( $product_data == false )
			{
				return array('error' => 'Fedex Package Generation Failed - Unable to get Product Data. Aborting');
			}
			$items[] = array('data' => $product_data , 'quantity' => $orderItem['qty']);
		}
		$package['contents'] = apply_filters( 'xa_fedex_get_customized_package_items_from_order',$items, $order );
		$package['destination']['country'] = $order->shipping_country;
		$package['destination']['first_name'] = $order->shipping_first_name;
		$package['destination']['last_name'] = $order->shipping_last_name;
		$package['destination']['company'] = $order->shipping_company;
		$package['destination']['address_1'] = $order->shipping_address_1;
		$package['destination']['address_2'] = $order->shipping_address_2;
		$package['destination']['city'] = $order->shipping_city;
		$package['destination']['state'] = $order->shipping_state;
		$package['destination']['postcode'] = $order->shipping_postcode;

		$packages = apply_filters( 'wf_filter_label_packages', array($package) , $this->ship_from_address, $order->id); //for multivendor
		return $packages;
	}
	
	public function print_label( $order,$service_code,$order_id ){
		$this->order_object = $order;
		$this->pre_service = '';
		$this->order = $this->wf_load_order($order);
		$this->order_id = $order_id;
		$this->service_code = $service_code;

		$packages = array_values($this->wf_get_package_from_order($order));
		
		$stored_packages    = get_post_meta( $order_id, '_wf_fedex_stored_packages', true );
		$stored_packages 	= $this->manual_packages( $stored_packages );

		foreach($stored_packages as $key => $fedex_package){
			$grouped_packages 	= $this->split_shipment_by_services( $fedex_package, $order );
			foreach ($grouped_packages as $group_name => $package) {
				$this->print_label_processor($package, $packages[$key] );
			}
			if( !empty( $this->shipmentErrorMessage) ){
				$this->shipmentErrorMessage .= "</br>Some error occured for packages $key: ".$this->shipmentErrorMessage;
			}
		}	
	}

	private function split_shipment_by_services($ship_packages, $order){
		foreach ($ship_packages as $key => &$entry) {
			$splited_array[$entry['service']][$key] = $entry;
		}
		return $splited_array;
	}
	
	private function get_return_request( $shipment_id, $order_id, $serviceCode ){
		$request	= get_post_meta($order_id, 'wf_woo_fedex_request_'.$shipment_id, true);
		
		$request['Version']['Major'] =$this->ship_service_version;
		
		$request['RequestedShipment']['ServiceType'] 				= $serviceCode;

		$shipper_address = $request['RequestedShipment']['Shipper'];
		$request['RequestedShipment']['Shipper'] 					= $request['RequestedShipment']['Recipient'];
		$request['RequestedShipment']['Recipient']					= $shipper_address;

		$total_weight = 0;
		foreach ($request['RequestedShipment']['RequestedPackageLineItems'] as $key => $item) {
			$request['RequestedShipment']['RequestedPackageLineItems'][$key]['SequenceNumber'] = 1;
			$request['RequestedShipment']['RequestedPackageLineItems'][$key]['GroupNumber'] = 1;
			$total_weight += $item['Weight']['Value'];
		}
		$request['RequestedShipment']['TotalWeight']['Value']		= $total_weight;

		$request['RequestedShipment']['SpecialServicesRequested']['ReturnShipmentDetail']['ReturnType']	= 'PRINT_RETURN_LABEL';
		// $request['RequestedShipment']['SpecialServicesRequested']['ReturnShipmentDetail']['ReturnEMailDetail']['MerchantPhoneNumber']	= '';
		$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'RETURN_SHIPMENT';
		
		$request['RequestedShipment']['PackageCount'] 				= 1;
		unset($request['RequestedShipment']['RequestedPackageLineItems']['SequenceNumber'], $request['RequestedShipment']['SpecialServicesRequested']['CodDetail'] );
		foreach( $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] as $key => $special_service ) {
			if( $special_service == 'COD' ) {
				unset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][$key]);			// Unset COD in return request
			}

			if( $special_service == 'ELECTRONIC_TRADE_DOCUMENTS' ) {
				unset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][$key]);	// Unset ELECTRONIC_TRADE_DOCUMENTS in return request
			}
		}
		$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']=array_values($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']);

		//unset COD for when COD node added in package level
		if( isset($request['RequestedShipment']['RequestedPackageLineItems']) ){
			foreach( $request['RequestedShipment']['RequestedPackageLineItems'] as $key => $attribute ) {
				foreach( $attribute as $type => $value ){
					if( $type == 'SpecialServicesRequested'){
						foreach( $value['SpecialServiceTypes'] as $index => $special_service ){
							if( $special_service == 'COD' ){
								unset($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes'][$index]);
								unset($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['CodDetail']);
								
								$request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes'] = array_values($request['RequestedShipment']['RequestedPackageLineItems'][$key]['SpecialServicesRequested']['SpecialServiceTypes']);
							}
						}
					}
				}
			}
		}

		if( isset($request['RequestedShipment']['CustomsClearanceDetail']) ) {

			if( $request['RequestedShipment']['Shipper']['Address']['CountryCode'] != $request['RequestedShipment']['Recipient']['Address']['CountryCode'] ) {

				$request['RequestedShipment']['CustomsClearanceDetail']['CustomsOptions']['Type'] = $this->int_return_label_reason;

				if( $this->int_return_label_reason == "OTHER" ) {
					$request['RequestedShipment']['CustomsClearanceDetail']['CustomsOptions']['Description'] = $this->int_return_label_desc;
				}
			}

			$custom_clearance_details	= $request['RequestedShipment']['CustomsClearanceDetail'];
			$dest_country 				= $request['RequestedShipment']['Recipient']['Address']['CountryCode'];

			if ( $request['RequestedShipment']['Shipper']['Address']['CountryCode'] == 'CA' && ( $dest_country != 'US' && $dest_country != 'CA' && $dest_country != 'PR' && $dest_country != 'VI' ) ) {
				$export_compliance = array(
					'B13AFilingOption' 			=> 'NOT_REQUIRED',
					'ExportComplianceStatement' => '02',
				);
				$request['RequestedShipment']['CustomsClearanceDetail']['ExportDetail'] = $export_compliance;
			}

			// Duties Payment Type as Recepient is not allowed for Return Shipments
			if( isset($custom_clearance_details['DutiesPayment']) && isset($custom_clearance_details['DutiesPayment']['PaymentType']) && $custom_clearance_details['DutiesPayment']['PaymentType'] == 'RECIPIENT' && $this->customs_duties_payer != 'THIRD_PARTY' && $this->customs_duties_payer != 'THIRD_PARTY_ACCOUNT' ) {

				$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment'] = array(
					'PaymentType' => 'SENDER',
				);

				$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty']=array(
					'AccountNumber'           => strtoupper( $this->account_number ),
					'CountryCode'             => $this->origin_country
				);
			}
			
		}

		// Alternative Return is not supported for Return Shipments
		if( isset($request['RequestedShipment']['LabelSpecification']) && isset($request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin']) )
		{
			unset( $request['RequestedShipment']['LabelSpecification']['PrintedLabelOrigin'] );
		}

		return apply_filters('ph_fedex_return_label_request',$request,$order_id);
	}

	public function print_return_label( $shipment_id, $order_id, $serviceCode ){
		$this->order_id 	= $order_id;
		$this->shipmentId 	= $shipment_id;

		$request = $this->get_return_request($shipment_id, $order_id, $serviceCode);
		$this->process_result( $request, $this->get_result($request) );
	}
	
	public function void_shipment( $order_id , $shipment_id, $tracking_completedata){
		$request = array();
		$this->order_id = $order_id;
		$request = $this->get_fedex_common_api_request($request);
		$request['ShipTimestamp'] = date('c');
		$request['TrackingId'] = $tracking_completedata;
		$request['DeletionControl'] = 'DELETE_ONE_PACKAGE'; // Package/Shipment

		$request 	= apply_filters( 'ph_fedex_void_shipment_request', $request, $order_id, $shipment_id, $tracking_completedata );

		try {
			
			$wsdl_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/ShipService_v' . $this->ship_service_version. '.wsdl';
			$client = $this->wf_create_soap_client($wsdl_dir );

			if( $this->soap_method == 'nusoap' ){
				$result = $client->call( 'deleteShipment', array( 'DeleteShipmentRequest' => $request ) );
				$result = json_decode( json_encode( $result ), false );
			}else{
				$result = $client->deleteShipment( $request );
			}
			
		} catch (Exception $e) {
			$this->debug( __( 'SoapFault while void_shipment.', 'wf-shipping-fedex' ) );
		}
		
		$this->debug( 'FedEx REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $request, true ) . '</pre>' );
		$this->debug( 'FedEx RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $result, true ) . '</pre>' );

		if( $this->debug ) {

			$this->admin_diagnostic_report( "------------------------------- Fedex Void Shipment Request -------------------------------" );
			$this->admin_diagnostic_report( print_r( $request, true ) );
			$this->admin_diagnostic_report( "------------------------------- Fedex Void Shipment Response -------------------------------" );
			$this->admin_diagnostic_report( print_r( $result, true ) );
		}

		if ( WF_FEDEX_ADV_DEBUG_MODE == "on" ) { // Test mode is only for development purpose.
			$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
			$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
			
			$this->debug( 'FedEx REQUEST in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;overflow: auto;">' . print_r( htmlspecialchars( $xml_request ), true ) . "</pre>\n" );
			$this->debug( 'FedEx RESPONSE in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;overflow: auto;">' . print_r( htmlspecialchars( $xml_response ), true ) . "</pre>\n" );

			if( $this->debug ) {

				$this->admin_diagnostic_report( "------------------------------- Fedex Void Shipment Request -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_request ) );
				$this->admin_diagnostic_report( "------------------------------- Fedex Void Shipment Response -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_response ) );
			}
		}

		if ( is_object($result) && $result->HighestSeverity != 'FAILURE' && $result->HighestSeverity != 'ERROR') {
			add_post_meta($order_id, 'wf_woo_fedex_shipment_void', $shipment_id, false);					 
		}elseif( is_object($result) ){
			$shipment_void_errormessage =  $this->result_notifications($result->Notifications, $error_message='');

			$this->admin_diagnostic_report( "------------------------------- Fedex Void Shipment Error - ".$order_id." -------------------------------" );
			$this->admin_diagnostic_report( $shipment_void_errormessage );

			update_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage', $shipment_void_errormessage);
			add_post_meta($order_id, 'ph_woo_fedex_shipment_client_reset', $shipment_id, false);
		}		
	}
	
	public function print_label_processor( $fedex_packages, $package ) {
		
		$this->master_tracking_id = '';
		
		// Debugging
		$this->debug( __( 'FEDEX debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-shipping-fedex' ) );

		// See if address is residential
		$this->residential_address_validation( $package );

		$request_type= '';
		if(! empty( $this->smartpost_hub ) && $package['destination']['country'] == 'US' && $this->service_code == 'SMART_POST'){
			$request_type = 'smartpost';
		}elseif(strpos($this->service_code, 'FREIGHT') !== false){
			$request_type = 'freight';
		}
			
		if($this->validate_package($fedex_packages)){
			$fedex_requests   = $this->get_fedex_requests( $fedex_packages, $package, $request_type);
			if ( $fedex_requests ) {
				$this->run_package_request( $fedex_requests );
			}
			$packages_to_quote_count = sizeof( $fedex_requests );
		}
		update_post_meta($this->order_id, 'wf_woo_fedex_shipmentErrorMessage', $this->shipmentErrorMessage);      
	}

	public function manual_packages($packages) {

		if (!isset($_GET['weight'])) {
			return $packages;
		}

		$length_arr		= json_decode(stripslashes(html_entity_decode($_GET["length"])));
		$width_arr		= json_decode(stripslashes(html_entity_decode($_GET["width"])));
		$height_arr		= json_decode(stripslashes(html_entity_decode($_GET["height"])));
		$weight_arr		= json_decode(stripslashes(html_entity_decode($_GET["weight"])));  
		$service_arr	= json_decode(stripslashes(html_entity_decode($_GET["service"])));
		$insurance_arr	= json_decode(stripslashes(html_entity_decode($_GET["insurance"])));
        $num_of_packages  = json_decode(stripslashes(html_entity_decode($_GET["num_of_packages"])));


		// To recreate packages for package removed from order page, not required in case of automatic label generation
		if ( isset($_GET["package_key"]) ) {

			$group_index_package_index	= json_decode(stripslashes(html_entity_decode($_GET["package_key"])));
			$temp_insurance_arr			= $insurance_arr;
			$new_packages 				= [];

			foreach( $group_index_package_index as $key => $packages_indexes ) {

				// Empty for extra added packages manually
				if( ! empty($packages_indexes) ) {

					list( $main_arr_index, $inner_arr_index ) = explode( '_', $packages_indexes );

					if( ! empty($packages[$main_arr_index][$inner_arr_index]) ) {

						$new_packages[$main_arr_index][$inner_arr_index] 							= $packages[$main_arr_index][$inner_arr_index];
						if (!empty($insurance_arr[$key])) {
							$new_packages[$main_arr_index][$inner_arr_index]['InsuredValue']['Amount'] 	= round( array_shift($temp_insurance_arr), 2);
						}else{
							$new_packages[$main_arr_index][$inner_arr_index]['InsuredValue']['Amount'] 	= 0;
							array_shift($temp_insurance_arr);
						}
					}
				}
			}

			if( isset($new_packages) ) {
				$packages = $new_packages;
			}
		}
		// End of creation of the package depending on removed package

		$no_of_package_entered  = count($weight_arr);
		$no_of_packages 		= 0;

		foreach ($packages as $key => $package) {
			$no_of_packages += count($package);
		}

		// Populate extra packages, if entered manual values
		if ($no_of_package_entered > $no_of_packages) {
			
			if( $this->debug ) {

				$this->admin_diagnostic_report( "Default Number of Packages:".$no_of_packages );
				$this->admin_diagnostic_report( "Number of Packages Entered:".$no_of_package_entered );
				$this->admin_diagnostic_report( "------------------------------- Package Details -------------------------------" );
				$this->admin_diagnostic_report( "Weight: ".print_r( $weight_arr, true ) );
				$this->admin_diagnostic_report( "Length: ".print_r( $length_arr, true ) );
				$this->admin_diagnostic_report( "Width: ".print_r( $width_arr, true ) );
				$this->admin_diagnostic_report( "Height: ".print_r( $height_arr, true ) );
				$this->admin_diagnostic_report( "Insurance: ".print_r( $insurance_arr, true ) );
				$this->admin_diagnostic_report( "Service: ".print_r( $service_arr, true ) );
			}

			// Get first package to clone default data
			$package_clone 			=   isset($packages[0]) && is_array($packages[0]) ? current($packages[0]) : '';
			$new_manual_package 	=	array();
			$new_manual_package[0] 	=	[];

			for($i=$no_of_packages; $i<$no_of_package_entered; $i++) {

				if( empty($package_clone) ) {

					$manual_package = array(
						'GroupNumber'			=> $i+1,
						'GroupPackageCount'		=> 1,
						'Weight'				=> array(

							'Value'		=> '',
							'Units'		=> $this->labelapi_weight_unit
						),

						'Dimensions'			=> array(),

						'InsuredValue'			=> array(
							'Amount'	=> 0,
							'Currency'	=> $this->wf_get_fedex_currency()
						),

						'packed_products'		=> array(),
					);

					$new_manual_package[0][$i] = $manual_package;

				} else {

					$package_clone['GroupNumber'] 		= $i+1;
					$package_clone['packed_products'] 	= array();

					if( isset($package_clone['package_id']) ) {

						unset($package_clone['package_id']);
					}

					$package_clone['manual_package']='true';

					$new_manual_package[0][$i] = $package_clone;
				}
			}
			
			if( isset($packages[0]) && is_array($packages[0]) ) {

				$packages[0] = array_merge($packages[0], $new_manual_package[0]);
			} else {
				$packages[0] = $new_manual_package[0];
			}
		}

		// Overridding package values
		$i = 0;

		foreach($packages as $package_key => $stored_package) {

			foreach($stored_package as $key => $package) {

				if( !empty($length_arr[$i]) || !empty($width_arr[$i]) || !empty($height_arr[$i]) ) {
                    
                    //PDS-85
					// If not available in GET then don't overwrite.
					if (isset($length_arr[$i])) {

						$packages[$package_key][$key]['Dimensions']['Length'] =  max( 1, round( $length_arr[$i] ,0) );
					}

					// If not available in GET then don't overwrite.
					if (isset($width_arr[$i])) {

						$packages[$package_key][$key]['Dimensions']['Width']  =  max( 1, round( $width_arr[$i] ,0) );
					}

					// If not available in GET then don't overwrite.
					if (isset($height_arr[$i])) {

						$packages[$package_key][$key]['Dimensions']['Height'] = max( 1, round( $height_arr[$i] ,0) );
					}

					$packages[$package_key][$key]['Dimensions']['Units']	= $this->labelapi_dimension_unit;

				} elseif ( isset($packages[$package_key][$key]['Dimensions']) ) {

					unset($packages[$package_key][$key]['Dimensions']);
				}

				if ( !empty($service_arr[$i]) ) {

					$packages[$package_key][$key]['service']  			= $service_arr[$i];
				}

				if ( !empty($insurance_arr[$i]) ) {

					$packages[$package_key][$key]['InsuredValue']['Amount']	= $insurance_arr[$i];
				}

				// If not available in GET then don't overwrite.
				if (isset($weight_arr[$i])) {

					$weight 											=   $weight_arr[$i];
					$packages[$package_key][$key]['Weight']['Value'] 	=   $weight;
					$packages[$package_key][$key]['Weight']['Units'] 	=   $this->labelapi_weight_unit;
				}

				if (isset($num_of_packages[$i])) {

					$packages[$package_key][$key]['num_of_packages'] = $num_of_packages[$i];
				}

				$i++;
			}
		}

		update_post_meta( $this->order_id, '_wf_fedex_stored_packages', $packages );

		return $packages;
	}

	public function run_package_request( $requests ) {
		/* try {		 	
		 */	
			//$this->tracking_ids = '';
			
			foreach ( $requests as $key => $request ) {
				
				if( $this->commercial_invoice && $this->is_international ) {
					$company_logo = !empty($this->settings['company_logo']) ? true : false;
					$digital_signature = !empty($this->settings['digital_signature']) ? true : false;

					$special_servicetypes = !empty($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']) ? $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] : array();

					$etd_label 	= false;

					if ( isset($_GET['etd']) ) {

						$etd_label = ($_GET['etd'] === 'true') ? true : false;

					}else if( $this->etd_label ) {

						$etd_label = true;
					}

					if( $etd_label ) {

						array_unshift( $special_servicetypes, 'ELECTRONIC_TRADE_DOCUMENTS' );

						$request['RequestedShipment']['SpecialServicesRequested']['EtdDetail']['RequestedDocumentCopies'] = [];
						$request['RequestedShipment']['SpecialServicesRequested']['EtdDetail']['RequestedDocumentCopies'][] = 'COMMERCIAL_INVOICE';

						if( $this->pro_forma_invoice ){

							$request['RequestedShipment']['SpecialServicesRequested']['EtdDetail']['RequestedDocumentCopies'][] = 'PRO_FORMA_INVOICE';
						}
					}

					$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = $special_servicetypes;

					$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'COMMERCIAL_INVOICE';

					if( $this->pro_forma_invoice ){

						$request['RequestedShipment']['ShippingDocumentSpecification']['ShippingDocumentTypes'][] = 'PRO_FORMA_INVOICE';
					}

					$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['Format']['ImageType'] = 'PDF';
					$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['Format']['StockType'] = 'PAPER_LETTER';

					if($company_logo){
						$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['CustomerImageUsages'][] = array(
							'Type' 	=> 'LETTER_HEAD', 
							'Id' 	=> 'IMAGE_1', 
						);
					}

					if($digital_signature){
						$request['RequestedShipment']['ShippingDocumentSpecification']['CommercialInvoiceDetail']['CustomerImageUsages'][] = array(
							'Type' 	=> 'SIGNATURE',
							'Id' 	=> 'IMAGE_2',
						);
					}
				}
				$this->process_result( $request, $this->get_result( $request ));
			}
			if(!empty($this->tracking_ids)){
				// Auto fill tracking info.
				$shipment_id_cs = $this->tracking_ids;
				Ph_FedEx_Tracking_Util::update_tracking_data( $this->order_id, $shipment_id_cs, 'fedex', WF_Tracking_Admin_FedEx::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_FedEx::SHIPMENT_RESULT_KEY );
			}
			
		/*  } catch ( Exception $e ) {
			$this->debug( print_r( $e, true ), 'error' );
			return false;
		} */ 
	}
	
	private function wf_get_fedex_currency(){
		//$wc_currency = $this->fedex_currency;
		$this->order_object = ( isset($this->order_object) && !empty($this->order_object) ) ? $this->order_object : '';
		$wc_currency = apply_filters('ph_change_fedex_currency', $this->fedex_currency, $this->order_object);
		
		$fedex_currency = '';
		switch ( $wc_currency ) {
			case 'ARS':
				$fedex_currency = 'ARN';
				break;
			case 'GBP':
				$fedex_currency = 'UKL';
				break;
			case 'CHF':
				$fedex_currency = 'SFR';
				break;	
			case 'MXN':
				$fedex_currency = 'NMP';
				break;	
			case 'SGD':
				$fedex_currency = 'SID';
				break;		
			case 'AED':
				$fedex_currency = 'DHS';
				break;
			case 'KWD':
				$fedex_currency = 'KUD';
				break;
			case 'JMD':
				$fedex_currency = 'JAD';
				break;
			case 'JPY':
				$fedex_currency = 'JYE';
				break;
			default:
				$fedex_currency = $wc_currency;
				break;
		}
		return $fedex_currency;
	}

	private function ph_get_fedex_currency_for_order( $currency ) {
		
		$order_currency = $currency;

		switch ( $currency ) {
			case 'ARS':
				$order_currency = 'ARN';
				break;
			case 'GBP':
				$order_currency = 'UKL';
				break;
			case 'CHF':
				$order_currency = 'SFR';
				break;	
			case 'MXN':
				$order_currency = 'NMP';
				break;	
			case 'SGD':
				$order_currency = 'SID';
				break;		
			case 'AED':
				$order_currency = 'DHS';
				break;
			case 'KWD':
				$order_currency = 'KUD';
				break;
			case 'JMD':
				$order_currency = 'JAD';
				break;
			case 'JPY':
				$order_currency = 'JYE';
				break;
			default:
				$order_currency = $currency;
				break;
		}

		return $order_currency;
	}

	public function get_result( $request ) {
		if( !empty($this->pre_service) && $this->pre_service !== $request['RequestedShipment']['ServiceType'] ){
			$this->master_tracking_id = ''; 
			//$request['RequestedShipment']['PackageCount'] = 1;
		}
		$this->pre_service = $request['RequestedShipment']['ServiceType'];

		if(!empty($this->master_tracking_id) )
			$request['RequestedShipment']['MasterTrackingId'] = $this->master_tracking_id;		

		$result = '';
		try {
			$wsdl_dir =plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/ShipService_v' . $this->ship_service_version. '.wsdl';
			$client = $this->wf_create_soap_client( $wsdl_dir );

			if( $this->soap_method == 'nusoap' ){
				$result = $client->call( 'processShipment', array( 'ProcessShipmentRequest' => $request ) );
				$result = json_decode( json_encode( $result ), false );
			}
			else{
				$result = $client->processShipment( $request );
			}

		} catch (Exception $e) {
			$this->debug( __( 'SoapFault while run_package_request.', 'wf-shipping-fedex' ) );
			$this->debug( 'Error Message: '.$e->getMessage() ); 
		}

		$this->debug( 'FedEx REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $request, true ) . '</pre>' );
		$this->debug( 'FedEx RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $result, true ) . '</pre>' );

		if( $this->debug ) {

			$this->admin_diagnostic_report( "------------------------------- Fedex Shipment Request -------------------------------" );
			$this->admin_diagnostic_report( print_r( $request, true ) );
		}
		
		if ( WF_FEDEX_ADV_DEBUG_MODE == "on" ) { // Test mode is only for development purpose.
			try{
				$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
				$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
			}
			catch ( Exception $e){
				echo "Error: ".$e->getMessage() ;
			}
			$this->debug( 'FedEx REQUEST in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;overflow: auto;">' . print_r( htmlspecialchars( $xml_request ), true ) . "</pre>\n" );
			$this->debug( 'FedEx RESPONSE in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;overflow: auto;">' . print_r( htmlspecialchars( $xml_response ), true ) . "</pre>\n" );

			if( $this->debug ) {

				$this->admin_diagnostic_report( "------------------------------- Fedex Create Shipment Request -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_request ) );
				$this->admin_diagnostic_report( "------------------------------- Fedex Create Shipment Response -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_response ) );
			}
		}
		return $result;
	}

	private function process_result( $request, $result = '' ) {
		if(!$result)
			return false;
		
		if ( $result->HighestSeverity != 'FAILURE' && $result->HighestSeverity != 'ERROR' && ! empty ($result->CompletedShipmentDetail) ) {
			
			if( property_exists($result->CompletedShipmentDetail,'CompletedPackageDetails') ){
				if(is_array($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds)){
					foreach($result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds as $track_ids){
						if($track_ids->TrackingIdType != 'USPS'){
							$shipmentId = $track_ids->TrackingNumber;	
							$tracking_completedata = $track_ids; 		
						}else{
							$usps_shipmentId = $track_ids->TrackingNumber;
						}
					}
				}
				else{
					$shipmentId = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;		
					$tracking_completedata = $result->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds;
				}	
			}
			elseif(property_exists($result->CompletedShipmentDetail,'MasterTrackingId')){
				$shipmentId = $result->CompletedShipmentDetail->MasterTrackingId->TrackingNumber;		
				$tracking_completedata = $result->CompletedShipmentDetail->MasterTrackingId;				
			}			
			
			//if return label
			if( !empty($this->shipmentId) && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails->Label,'ShippingDocumentDisposition') && $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ShippingDocumentDisposition == 'RETURNED'){
				
				$package_shipping_label = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;
				if(base64_encode(base64_decode($package_shipping_label, true)) === $package_shipping_label){  //For nusoap encoded label response
					$return_label = $package_shipping_label;
				}
				else{
					$return_label = base64_encode($package_shipping_label);
				}
				$returnlabel_type = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType; //Shipment ImageType

				add_post_meta($this->order_id, 'wf_woo_fedex_returnShipmetId', $shipmentId, true);
				add_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_'.$this->shipmentId, $return_label, true);
				if( !empty($returnlabel_type) ){
					 add_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_image_type_'.$this->shipmentId, $returnlabel_type, true);
				}
				$shipping_label = get_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_'.$this->shipmentId, true);
				return;				
			}
			
			if( !empty($result->CompletedShipmentDetail->MasterTrackingId) && empty($this->master_tracking_id) )
				$this->master_tracking_id = $result->CompletedShipmentDetail->MasterTrackingId;
			
			$shippingLabel 				= array();
			$addittional_label 			= array();
			$addittional_label_type 	= array();

			if(property_exists($result->CompletedShipmentDetail,'CompletedPackageDetails')  && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'Label') ){

				$package_shipping_label = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image;

				if(base64_encode(base64_decode($package_shipping_label, true)) === $package_shipping_label){  //For nusoap encoded label response
					$shippingLabel = $package_shipping_label;
				}
				else{
					$shippingLabel = base64_encode($package_shipping_label);
				}
				$shippinglabel_type = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType; //Shipment ImageType
				
				if(property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'CodReturnDetail') && property_exists($result->CompletedShipmentDetail->CompletedPackageDetails->CodReturnDetail,'Label') ){

					$cod_return_label = $result->CompletedShipmentDetail->CompletedPackageDetails->CodReturnDetail->Label->Parts->Image;

					//For nusoap encoded label response
					if(base64_encode(base64_decode($cod_return_label, true)) === $cod_return_label){  
						$addittional_label['COD Return'] = $cod_return_label;
					}
					else{
						$addittional_label['COD Return'] = base64_encode($cod_return_label);
					}

					// Cod Return Label don't have Imagetype Use Main Label Type only
					$addittional_label_type['COD Return'] = $result->CompletedShipmentDetail->CompletedPackageDetails->Label->ImageType;
				}

				if(property_exists($result->CompletedShipmentDetail->CompletedPackageDetails,'PackageDocuments')) {

					$package_documents = $result->CompletedShipmentDetail->CompletedPackageDetails->PackageDocuments;

					if(is_array($package_documents)) {

						foreach($package_documents as $document_key=>$package_document) {

							$package_additional_label = $package_document->Parts->Image;

							if(base64_encode(base64_decode($package_additional_label, true)) === $package_additional_label) {

								$addittional_label[$document_key] = $package_additional_label;
							}else{

								$addittional_label[$document_key] = base64_encode($package_additional_label);
							}

							$addittional_label_type[$document_key] = $package_document->ImageType;
						}

					}elseif( is_object($package_documents) && $package_documents->Type == 'AUXILIARY_LABEL' ) {

						$addittional_label_type[$package_documents->Type] = $package_documents->ImageType;

						if(base64_encode(base64_decode($package_documents->Parts->Image, true)) === $package_documents->Parts->Image){
							$addittional_label[$package_documents->Type] = $package_documents->Parts->Image;
						}else{
							$addittional_label[$package_documents->Type] = base64_encode($package_documents->Parts->Image);
						}

					}elseif( is_object($package_documents) && $package_documents->Type == 'OP_900' ) {

						$addittional_label_type[$package_documents->Type] = $package_documents->ImageType;

						if(base64_encode(base64_decode($package_documents->Parts->Image, true)) === $package_documents->Parts->Image) {

							$addittional_label[$package_documents->Type] = $package_documents->Parts->Image;
						}else{

							$addittional_label[$package_documents->Type] = base64_encode($package_documents->Parts->Image);
						}

					}
				}
				
				if(property_exists($result->CompletedShipmentDetail,'ShipmentDocuments')) {

					$shipmentDocuments 	= $result->CompletedShipmentDetail->ShipmentDocuments;

					if( is_array($shipmentDocuments) ) {

						foreach($shipmentDocuments as $document_key => $shipment_document) {

							$shipment_additional_label 	= $shipment_document->Parts->Image;
							$shipment_type 				= ( $shipment_document->Type == 'COMMERCIAL_INVOICE' ) ? 'Commercial Invoice' : $shipment_document->Type;

							if(base64_encode(base64_decode($shipment_additional_label, true)) === $shipment_additional_label) {

								$addittional_label[$shipment_type] = $shipment_additional_label;
							}else{

								$addittional_label[$shipment_type] = base64_encode($shipment_additional_label);
							}

							$addittional_label_type[$shipment_type] = $shipment_document->ImageType;
						}

					} else {

						$commercial_invoice_label = $shipmentDocuments->Parts->Image;

						if(base64_encode(base64_decode($commercial_invoice_label, true)) === $commercial_invoice_label) {

							$addittional_label['Commercial Invoice'] = $commercial_invoice_label;

						}else{

							$addittional_label['Commercial Invoice'] = base64_encode($commercial_invoice_label);

						}

						$addittional_label_type['Commercial Invoice'] = $result->CompletedShipmentDetail->ShipmentDocuments->ImageType;
					}
				}
			} 
			elseif(property_exists($result->CompletedShipmentDetail,'ShipmentDocuments')){ 
				//As per the documentation. This case will never occure. 
				$shipment_document_label = $result->CompletedShipmentDetail->ShipmentDocuments->Parts->Image;
				if(base64_encode(base64_decode($shipment_document_label, true)) === $shipment_document_label){
					$shippingLabel = $shipment_document_label;
				}
				else{
					$shippingLabel = base64_encode($shipment_document_label);
				}
				$shippinglabel_type = $result->CompletedShipmentDetail->ShipmentDocuments->ImageType;
			}
			
			if( !empty($shippingLabel) && property_exists($result->CompletedShipmentDetail,'AssociatedShipments') && property_exists($result->CompletedShipmentDetail->AssociatedShipments,'Label') ) {
				
				$associated_documents = $result->CompletedShipmentDetail->AssociatedShipments->Label;
				if( ! empty($result->CompletedShipmentDetail->AssociatedShipments->TrackingId) ) {
					$associated_documents_tracking_id = $result->CompletedShipmentDetail->AssociatedShipments->TrackingId->TrackingNumber;
					$this->tracking_ids .= $associated_documents_tracking_id.',';
					add_post_meta( $this->order_id, '_ph_woo_fedex_additional_tracking_number_'.$shipmentId, $associated_documents_tracking_id );
				}
				if(!empty($associated_documents)){
					
						$associated_shipment_label = $associated_documents->Parts->Image;
						if(base64_encode(base64_decode($associated_shipment_label, true)) === $associated_shipment_label){
							$addittional_label['AssociatedLabel'] = $associated_shipment_label;
						}
						else{
							$addittional_label['AssociatedLabel'] = base64_encode($associated_shipment_label);
						}
						$addittional_label_type['AssociatedLabel'] = $associated_documents->ImageType;
				}
			}
			
			 if(!empty($shipmentId) && !empty($shippingLabel)){

			 	$shipmentIds = get_post_meta($this->order_id, 'ph_woo_fedex_shipmentIds', true);

			 	if( empty($shipmentIds) && !is_array($shipmentIds) ) {

			 		$shipmentIds   = array($shipmentId);

			 	}else{

			 		$shipmentIds[] =$shipmentId;
			 		
			 	}

                delete_post_meta($this->order_id, 'ph_woo_fedex_shipmentIds');
                add_post_meta($this->order_id, 'wf_woo_fedex_shipmentId', $shipmentId, false);
                // Some Customers Site wont allow adding duplicate Meta Keys in DB, Adding new meta key with custom build Shipment Id Array
			 	add_post_meta($this->order_id, 'ph_woo_fedex_shipmentIds', $shipmentIds, false);
				add_post_meta($this->order_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, $shippingLabel, true);
				add_post_meta($this->order_id, 'wf_woo_fedex_packageDetails_'.$shipmentId, $this->wf_get_parcel_details($request) , true);
				add_post_meta($this->order_id, 'wf_woo_fedex_request_'.$shipmentId, $request , true);

				if( !empty($shippinglabel_type) ){
					 add_post_meta($this->order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipmentId, $shippinglabel_type, true);
				}
				
				if(isset($tracking_completedata)){
					add_post_meta($this->order_id, 'wf_woo_fedex_tracking_full_details_'.$shipmentId, $tracking_completedata, true);
				}			
					
				if( !empty($request['RequestedShipment']['ServiceType']) ){
					add_post_meta($this->order_id, 'wf_woo_fedex_service_code'.$shipmentId, $request['RequestedShipment']['ServiceType'], true);
				}
				
				if(!empty($usps_shipmentId)){
					add_post_meta($this->order_id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, $usps_shipmentId, true);
				}

				if($this->add_trackingpin_shipmentid == 'yes' && !empty($shipmentId)){
					//$this->order->add_order_note( sprintf( __( 'Fedex Tracking-pin #: %s.', 'wf-shipping-fedex' ), $shipmentId) , true);
					$this->tracking_ids = $this->tracking_ids . $shipmentId . ',';			
				}
				
				if($this->add_trackingpin_shipmentid == 'yes' && !empty($usps_shipmentId)){
					//$this->order->add_order_note( sprintf( __( 'Fedex Smart Post USPS Tracking-pin #: %s.', 'wf-shipping-fedex' ), $usps_shipmentId) , true);
				}
				
				if(!empty($addittional_label)){
					add_post_meta($this->order_id, 'wf_fedex_additional_label_'.$shipmentId, $addittional_label, true);	
					if(!empty($addittional_label_type)){
						add_post_meta($this->order_id, 'wf_fedex_additional_label_image_type_'.$shipmentId, $addittional_label_type, true);		
					}	
				}							
			} 
			do_action('xa_fedex_label_generated_successfully',$shipmentId,$shippingLabel,$this->order_id,$shippinglabel_type);
		}else{
			$this->shipmentErrorMessage .=  $this->result_notifications($result->Notifications, $error_message='', true);

			$this->admin_diagnostic_report( "------------------------------- Fedex Create Shipment Error - ".$this->order_id." -------------------------------" );
			$this->admin_diagnostic_report( $this->shipmentErrorMessage );
		}
	}
	
	public function wf_get_parcel_details($request){
		 $weight = '';
		 $height = '';
		 $width = '';
		 $length = '';
		 if(isset($request['RequestedShipment']['RequestedPackageLineItems'][0])){
			$line = $request['RequestedShipment']['RequestedPackageLineItems'][0];
			if(isset($line['Weight'])){
				$weight = $line['Weight']['Value'] . ' ' . $line['Weight']['Units'];			
			}
			if(isset($line['Dimensions'])){
				$height = $line['Dimensions']['Height'] . ' ' . $line['Dimensions']['Units'];	
				$width = $line['Dimensions']['Width'] . ' ' . $line['Dimensions']['Units'];	
				$length = $line['Dimensions']['Length'] . ' ' . $line['Dimensions']['Units'];					
			}			
		 }		 
		 return array('Weight' => $weight, 'Height' => $height, 'Width' => $width, 'Length' => $length);
	}
	
	public function result_notifications( $notes, $error_message = '', $label = false ) {

		$error_message 			= '';
		$authenitication_failed = "<br/> The \"Authentication Failed\" error comes when the FedEx label evaluation process is not completed.<br/><br/>
									PluginHive has tied up with FedEx in order to speed up this process, please contact support@pluginhive.com with the
									following FedEx production key details to complete the label evaluation process.<br/><br/>
									Company Name:<br/>
									Account Number:<br/>
									Meter Number:<br/>
									Authentication Key:<br/>
									Web Services Password: <br/><br/>
									FedEx takes 1-2 working days to complete this.";

		if( is_object( $notes ) ) {

			$authentication_code = false;

			// TODO: Not fair to use foreach across an object. We need to re-write this code.
			foreach( $notes as $noteKey => $note ) {

				if( is_string( $note ) ) {

					if( $note == 1000 ) {
						$authentication_code = true;
					}

					if( $this->production && $label && $authentication_code && $note == "Authentication Failed" ) {

						$note .= "<br/>". $authenitication_failed;
						$authentication_code =false;
					}

					$error_message .=  $noteKey . ': ' . $note . "<br />";

				} else {

					$error_message .=  $this->result_notifications( $note, $error_message, $label );
				}
			}
		}
		
		return $error_message;
	}
	
	public function validate_package($packages) {

		if( !$packages ) {
			return false;
		}

		$package_valid 		= true;
		$unpacked_items 	= array();
		$msg 				= '';

		// Removed Package Validation for Box Packing to support Manual Packages
		return $package_valid;

		// Only box packing is needed to check products now 
		// if ( $this->packing_method != 'box_packing') {
		// 	return true;
		// }

		// foreach($packages as $package) {

		// 	if (!isset($package['packed_products'])||empty($package['packed_products'])) {
				
		// 		$package_valid 		= false;
		// 		$unpacked_items[]	= $package;
		// 	}			
		// }

		// if ( !$package_valid && !empty($unpacked_items) ) {

		// 	$msg = 'Following product dimensions cannot be packed. Please configure correct box dimensions, Or set Individually/Weight based  as parcel packing method.</br>';
			
		// 	foreach($unpacked_items as $unpacked_item) {

		// 		$dim 		= $unpacked_item['Dimensions']['Length'].'X'.$unpacked_item['Dimensions']['Width'].'X'.$unpacked_item['Dimensions']['Height'].' '.$unpacked_item['Dimensions']['Units'];
		// 		$weight 	= $unpacked_item['Weight']['Value'].' '.$unpacked_item['Weight']['Units'];
		// 		$msg 	   .= sprintf('Dimensions: %1$s Weight: %2$s</br>',$dim,$weight);				
		// 	}
		// }

		// if($msg) {
		// 	$this->debug('<br>'.$msg);
		// 	$this->shipmentErrorMessage=__($msg);
		// }

		// return $package_valid;
	}
	
	//function to get shipper address for api request
	private function shop_address( $package = '' ){	
		$from_address = array(
			'name' 		=> $this->freight_shipper_person_name,
			'company' 	=> $this->freight_shipper_company_name,
			'phone' 	=> $this->freight_shipper_phone_number,
			'address_1' => $this->frt_shipper_street,
			'address_2' => $this->freight_shipper_street_2,
			'city' 		=> $this->freight_shipper_city,
			'state' 	=> $this->origin_state,
			'country' 	=> $this->origin_country,
			'postcode' 	=> $this->origin,
			'email'		=> isset($this->settings['shipper_email']) ? $this->settings['shipper_email'] : '',
		);

		//Filter for origin address switcher plugin.
		$from_address =  apply_filters( 'wf_filter_label_from_address', $from_address , $package );

		// Only first 30 characters get printed on Label.
		if( strlen($from_address['address_1']) > 30 ){
			$address_1 = substr( $from_address['address_1'], 0, strpos( wordwrap($from_address['address_1'], 30), "\n") ); //Get first 30 char from $address_1
			$address_2 = str_replace($address_1, '', $from_address['address_1']) . ' ' . $from_address['address_2']; //Take remains of $address_1 + $address_2
		}else{
			$address_1 = $from_address['address_1'];
			$address_2 = $from_address['address_2'];
		}

		$request = array(
			'Contact'=>array(
				'PersonName' 	=> $this->ph_replace_special_characters($from_address['name']),
				'CompanyName' 	=> $from_address['company'],
				'PhoneNumber' 	=> $from_address['phone'],
				'EMailAddress'	=> isset($from_address['email']) ? $from_address['email'] : '',
			),
			'Address'               => array(
				'StreetLines'         => array( strtoupper( $this->ph_replace_special_characters($address_1) ), strtoupper( $this->ph_replace_special_characters($address_2) ) ),
				'City'                => strtoupper( $this->ph_replace_special_characters($from_address['city']) ),
				'StateOrProvinceCode' => strtoupper( $from_address['state'] ),
				'PostalCode'          => strtoupper( $from_address['postcode'] ),
				'CountryCode'         => strtoupper( $from_address['country'] ),
				'Residential'         => $this->freight_shipper_residential
			)
		);
		return $request;
	}
	
	//function to get recipient address for api request
	private function order_address($package) {

		// Only first 30 characters get printed on Label.
		if( strlen($package['destination']['address_1']) > 30 ){
			$address_1 = substr( $package['destination']['address_1'], 0, strpos( wordwrap($package['destination']['address_1'], 30), "\n") ); //Get first 30 char from $address_1
			$address_2 = str_replace($address_1, '', $package['destination']['address_1']) . ' ' . $package['destination']['address_2']; //Take remains of $address_1 + $address_2
		}else{
			$address_1 = $package['destination']['address_1'];
			$address_2 = $package['destination']['address_2'];
		}

		$recipient_city = $package['destination']['city'];

		if( empty($recipient_city) && in_array($package['destination']['country'], $this->country_without_cities) ) {
			$recipient_city = 'Singapore';
		}

		$phonenummeta 	= get_post_meta( $this->order->id , '_shipping_phone', 1);
		$phonenum 		= !empty($phonenummeta) ? $phonenummeta : $this->order->billing_phone;

		$addr = array(
			'Contact' => array(
				'PersonName' 	=> $this->ph_replace_special_characters($package['destination']['first_name']) . ' ' . $this->ph_replace_special_characters($package['destination']['last_name']),
				'CompanyName' 	=> $package['destination']['company'],
				'PhoneNumber' 	=> $phonenum,
				'EMailAddress' 	=> isset($this->order->billing_email) ? $this->order->billing_email : '',
			),
			'Address' => array(
				'StreetLines'         =>  array( $this->ph_replace_special_characters($address_1), $this->ph_replace_special_characters($address_2) ),
				'Residential'         => ( $this->service_code == 'GROUND_HOME_DELIVERY' ) ? true : $this->residential,
				'PostalCode'          => str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
				'City'                => strtoupper( $this->ph_replace_special_characters($recipient_city) ),
				'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
				'CountryCode'         => $package['destination']['country']
			)
		);
		return $addr;
	}

	public function ph_replace_special_characters( $stringToCheck ) {

		$updatedString = str_replace( $this->splCharToFind, $this->splCharToReplace, $stringToCheck );

		return $updatedString;
	}
	
	private function manual_dimensions( $package ) {
		$group  = array();
		if ( empty($_GET['weight'] ) ) {
			$this->debug( sprintf( __( '<br> Package weight is missing. Aborting.', 'wf-shipping-fedex' ) ), 'error' );
			return;
		}

		$total_price = 0;
		$packed_products = array();
		foreach ( $package['contents'] as $item_no => $item) {
			$total_price += ( $this->wf_get_insurance_amount( $item['data'] ) * $item['quantity'] );
			$packed_products[] = $item['data'];
		}
		
		$group =array(
			array(
				'GroupNumber'       => 1,
				'GroupPackageCount' => 1,
				'Weight' => array(
					'Value' => $_GET['weight'],
					'Units' => $this->labelapi_weight_unit
				),
				'packed_products' => $packed_products,
				'Dimensions' => array(
					'Length' => $_GET['length'],
					'Width'  => $_GET['width'],
					'Height' => $_GET['height'],
					'Units'  => $this->labelapi_dimension_unit
				),
				'InsuredValue' => array(
					'Amount'   => round( $this->convert_to_fedex_currency( $total_price ), 2),
					'Currency' => $this->wf_get_fedex_currency()
				)
			)
		);
		return $group;
	}
	
	function get_package_one_commodoties($fedex_packages)
	{	
		$commodoties 	= array();
		$commodityQty 	= array();
		$package_id 	= 0;

		foreach ( $fedex_packages as $key => $parcel ) {
			if ( $parcel['packed_products'] ) {
				foreach ( $parcel['packed_products'] as $pid => $product ) {

					$unit_price 		= 0;
					$custom_value 		= 0;
					$unit_currency 		= $this->wf_get_fedex_currency();
					$custom_currency 	= $this->wf_get_fedex_currency();
					$product 			= $this->wf_load_product($product);
					$product_id 		= wp_get_post_parent_id($product->get_id());	// Get Parent Id
					$main_product_id 	= $product->get_id();	// Get Product Id ( Variation Id if present )

					$multi_part_product = get_post_meta($main_product_id , '_ph_multi_part_product', 1);
					$multi_part_count 	= get_post_meta($main_product_id , '_ph_multi_part_addon_count', 1);

					if(empty($product_id)) {

						$product_id = $main_product_id;
					}

					$is_dry_ice_product = get_post_meta($product_id , '_wf_dry_ice', 1);
                    
                    //PDS-149
					if ( (empty($this->invoice_commodity_value) && $this->discounted_price) || $this->invoice_commodity_value == 'discount_price' ) {

						$order_object = wc_get_order($this->order->id);

						if( $order_object instanceof WC_Order )
						{	
							$order_items = $order_object->get_items();

							if( !empty($order_items) )
							{
								foreach ( $order_items as  $item_key => $item_values ) {

									$order_item_id = $item_values->get_variation_id();

									if( empty($order_item_id) )
									{
										$order_item_id = $item_values->get_product_id();
									}
									
									// Compare with Parent Id and Variation Id
									if( $order_item_id == $product_id || $order_item_id == $main_product_id )
									{
										$product_unit_price 	= $item_values->get_total() / $item_values->get_quantity();
										$this->order_currency	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

										if( $this->commercial_invoice_order_currency ){

											$unit_price 	= $product_unit_price;
											$unit_currency 	= $this->order_currency;

											$custom_value 	 	= $unit_price;
											$custom_currency 	= $unit_currency;

										}else{

											$new_unit_price 	= apply_filters( 'ph_fedex_change_currency_to_fedex_currency', $product_unit_price, $this->order_currency, $this->wc_store_currency, $order_object );

											$unit_price 		= round( $this->convert_to_fedex_currency( $new_unit_price ), 2);
											$unit_currency 		= $this->wf_get_fedex_currency();

											$custom_value 	 	= $unit_price;
											$custom_currency 	= $unit_currency;
										}

										if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

											$unit_price 		= round( ($unit_price/$multi_part_count), 2);
											$custom_value 		= round( ($custom_value/$multi_part_count), 2);
										}
										
										break;
									}
									
								}
							}
						}
						
					}else{
						
						if( $this->commercial_invoice_order_currency ) {

							$order_object 			= wc_get_order($this->order->id);
							$unit_price 			= $this->get_product_price($product);
							$this->order_currency	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

							$unit_price 	= apply_filters( 'ph_fedex_change_currency_to_order_currency', $unit_price, $this->order_currency, $this->wc_store_currency, $order_object );
							$unit_currency 	= $this->order_currency;

							$custom_value 	 	= $unit_price;
							$custom_currency 	= $unit_currency;

						}else{
							$unit_price 	= round( $this->convert_to_fedex_currency($this->get_product_price($product)), 2);
							$unit_currency 	= $this->wf_get_fedex_currency();

							$custom_value 	 	= $unit_price;
							$custom_currency 	= $unit_currency;

						}

						if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

							$unit_price 		= round( ($unit_price/$multi_part_count), 2);
							$custom_value 		= round( ($custom_value/$multi_part_count), 2);
						}
					}

					$custom_declared_value = get_post_meta( $product_id, '_wf_fedex_custom_declared_value', true );

					//PDS-149
					if ( ( !empty($custom_declared_value) && empty($this->invoice_commodity_value) ) || ( !empty($custom_declared_value) && $this->invoice_commodity_value == 'declared_price' ) ) {


						if( $this->commercial_invoice_order_currency ) {

							$order_object 			= wc_get_order($this->order->id);
							$this->order_currency 	= $this->ph_get_fedex_currency_for_order( $order_object->get_currency() );

							$declared_value 		= apply_filters( 'ph_fedex_change_currency_to_order_currency', $custom_declared_value, $this->order_currency, $this->wc_store_currency, $order_object );

							$unit_price 		= $declared_value;
							$unit_currency 		= $this->order_currency;
							$custom_value 		= $declared_value;
							$custom_currency 	= $this->order_currency;							

						}else{

							$declared_value 	= round($this->convert_to_fedex_currency($custom_declared_value), 2);
							$unit_price 		= $declared_value;
							$unit_currency 		= $this->wf_get_fedex_currency();
							$custom_value 		= $declared_value;
							$custom_currency 	= $this->wf_get_fedex_currency();

						}

						if ( $multi_part_product && !empty($multi_part_count) && is_numeric($multi_part_count) ) {

							$unit_price 		= round( ($unit_price/$multi_part_count), 2);
							$custom_value 		= round( ($custom_value/$multi_part_count), 2);
						}
					}

					if( $is_dry_ice_product=='yes' && ( $key=='0' || count($fedex_packages) == 1 ) ){
						$this->dry_ice_shipment = true;
						$meta_exists=metadata_exists('post', $product_id, '_wf_dry_ice_weight');
						$dry_ice_weight=($meta_exists)?get_post_meta($product_id , '_wf_dry_ice_weight', 1):$product->get_weight();           // for backward compactibility ( added on 22/11/2018) 
						$this->dry_ice_total_weight += Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $dry_ice_weight, 'kg' )*$parcel['GroupPackageCount']; //Fedex support dry ice weight in KG only
					}
					
					$commodity_id 	= $main_product_id;

					if ( $multi_part_product ) {

						$commodity_id 	= $commodity_id.'_'.$key.$pid;
					}

					if ( isset( $commodoties[ $commodity_id ] ) ) {

						$commodoties[ $commodity_id ]['Quantity'] ++;

						if ( $package_id != $key ) {

							$package_id 	= $key;
							$commodoties[ $commodity_id ]['NumberOfPieces'] ++;
						}

						// If Commodity Item Quantity exceeds Order Item Quantity then restrict it to Order Item Quantity
						if( isset($commodityQty[$commodity_id]) && !empty($commodityQty[$commodity_id]) && $commodoties[ $commodity_id ]['Quantity'] > $commodityQty[$commodity_id] ) {

							$commodoties[ $commodity_id ]['Quantity'] = $commodityQty[$commodity_id];

							if ( $package_id != $key ) {
								$commodoties[ $commodity_id ]['NumberOfPieces'] = $commodityQty[$commodity_id];
							}

							continue;
						}
						
						$commodoties[ $commodity_id ]['CustomsValue']['Amount'] += $custom_value;
						$this->customtotal += $custom_value;
						continue;
					}

					$product_name 		= html_entity_decode( $product->get_name() );
					$commodity_desc 	= html_entity_decode( get_post_meta( $product_id , '_ph_commodity_description', 1) );
								
					$commodity_desc 	= !empty($commodity_desc) ? $commodity_desc : $product_name;

					//Remove special-characters from Product name
					if( $this->remove_special_char == true ){
						$product_name 		= preg_replace('/[^A-Za-z0-9-() ]/', '', $product_name);
						$commodity_desc 	= preg_replace('/[^A-Za-z0-9-() ]/', '', $commodity_desc);
					}

					$commodity_desc = ( strlen( $commodity_desc ) >= 450 ) ? substr( $commodity_desc, 0, 445 ).'...' : $commodity_desc;
					
					$commodoties[ $commodity_id ] = array(
						'Name'                 => $product_name,
						'NumberOfPieces'       => 1,
						'Description'          => $commodity_desc,
						'CountryOfManufacture' => ( $country = get_post_meta( $product_id, '_wf_manufacture_country', true ) ) ? $country : $this->origin_country,
						'Weight'               => array(
							'Units'            => $this->labelapi_weight_unit,
							'Value'            => round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product->get_weight(), $this->weight_unit ), 2 ) ,
						),
						'Quantity'             => $parcel['GroupPackageCount'],
						'UnitPrice'            => array(
							'Amount'           => $unit_price,
							'Currency'         => $unit_currency,
						),
						'CustomsValue'         => array(
							'Amount'           => $custom_value,
							'Currency'         => $custom_currency,
						),
						'QuantityUnits' => 'EA'
					);

					$package_id 	= $key;

					$this->customtotal += $custom_value;
					$product_id = $product->get_type() == 'simple' ? $product->get_id() : $product->get_parent_id();

					if(empty($product_id)) {

						$product_id = $main_product_id;
					}

					$wf_hs_code = get_post_meta( $product_id , '_wf_hs_code', 1 );
					
					// for backword compatiblity
					if(!$wf_hs_code){

						$product_data   = wc_get_product( $product_id  );

						if( !empty($product_data) && is_object($product_data) && $product_data->has_attributes() )
						{
							$wf_hs_code = $product_data->get_attribute( 'wf_hs_code' );
						}
					}
					
					if( !empty($wf_hs_code) ){
						$commodoties[ $commodity_id ]['HarmonizedCode'] = $wf_hs_code;
					} else if ( !empty($this->global_hs_code) ) {
						$commodoties[ $commodity_id ]['HarmonizedCode'] = $this->global_hs_code;
					}

					/*** To Handle Commodity Quantity in Case of Manual Packages - Start ***/
					$order_object = wc_get_order($this->order->id);

					if( $order_object instanceof WC_Order ) {

						$order_items = $order_object->get_items();

						if( !empty($order_items) ) {

							foreach ( $order_items as  $item_key => $item_values ) {

								$order_item_id = $item_values->get_variation_id();

								if( empty($order_item_id) ) {
									$order_item_id = $item_values->get_product_id();
								}

								if( $order_item_id == $main_product_id ) {

									$quantity = $item_values->get_quantity();

									// Add Order Item Quantity
									$commodityQty[$commodity_id] 	= $quantity;
								}

							}
						}
					}
					/*** To Handle Commodity Quantity in Case of Manual Packages - End ***/
				}
			}
		}
		return $commodoties;
	}
	
	function notification_receiver($email, $recipient_type = 'RECIPIENT'){
		if( !isset($email) || empty($email)){
			return false;
		}
		
		$recipient_email = array(
			'NotificationType'	=> 'EMAIL',
			'EmailDetail'=>array( 
				'EmailAddress'	=> $email
			),
			'Localization'=> array(
				'LanguageCode'	=> 'EN'
			),
		);
		return $recipient_email;
	}
	
	public function request_pickup($order_ids = array()){		
		if(!is_array($order_ids))
			return false;

		// pickup settings		
		$pickup_enabled				= ( $bool = $this->settings[ 'pickup_enabled'] ) && $bool == 'yes' ? true : false;
		$use_pickup_address			= ( $bool = $this->settings[ 'use_pickup_address'] ) && $bool == 'yes' ? true : false;
		$pickup_start_time    		= $this->settings[ 'pickup_start_time' ] ? $this->settings[ 'pickup_start_time' ] : 8; // Pickup min start time 8 am
		$pickup_close_time    		= $this->settings[ 'pickup_close_time' ] ? $this->settings[ 'pickup_close_time' ] : 18;

		// $pickup_service	    		= $this->settings[ 'pickup_service' ] ? $this->settings[ 'pickup_service' ] : 'FEDEX_NEXT_DAY_EARLY_MORNING';
		
		$master_order_id		=	current($order_ids);
		$pickup_packages		=	array();
		$domestic_package		=	array();
		$international_package	=	array();
		$response				=	array();
		$domestic_order_id		=	0;
		$int_order_id			=	0;

		$domestic_ground_order_id 	= 0;
		$domestic_freight_order_id 	= 0;
		$domestic_express_order_id 	= 0;
		
		$domestic_express_weight 			= 0;
		$domestic_express_package_count 	= 0;
		$domestic_ground_weight 			= 0;
		$domestic_ground_package_count 		= 0;
		$domestic_freight_weight 			= 0;
		$domestic_freight_package_count 	= 0;

		$int_express_weight 			= 0;
		$int_express_package_count		= 0;
		$int_freight_weight 			= 0;
		$int_freight_package_count 		= 0;
		$int_freight_order_id 			= 0;
		$int_express_order_id 			= 0;

		$domestic_freight_service 	= '';
		$int_freight_service 		= '';

		$request = array();
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'      => $this->api_key,
				'Password' => $this->api_pass
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number
		);
		$request['TransactionDetail'] = array( 'CustomerTransactionId' => ' *** Pickup Request V23 from WooCommerce ***' );
		$request['Version'] = array( 'ServiceId' => 'disp', 'Major' => 23, 'Intermediate' => '0', 'Minor' => '0' );

		$ready_date					= date('Y-m-d');
		$pickup_ready_timestamp		= strtotime($ready_date);
		$pickup_close_timestamp		= strtotime(date('Y-m-d')) + $pickup_close_time*3600; 	// For closing we need only time, so date is irrelavant
		
		$pickup_ready_timestamp_string = date('c', $pickup_ready_timestamp);

		// Check Current Time exceeds Store Cut-off Time, if yes request on next day
		if( !empty($pickup_close_time) && $pickup_close_time != '24:00') {

			$this->current_wp_time_hour_minute = current_time('H:i');

			if( $this->current_wp_time_hour_minute > $pickup_close_time ) {

				$pickup_ready_timestamp_string = date( 'c', strtotime( '+1 days', strtotime( $pickup_ready_timestamp_string ) ) );
			}
		}

		$date 			= new DateTime( $pickup_ready_timestamp_string );
		$shippingDay 	= $date->format('D');
		
		if( ! in_array($shippingDay, $this->working_days) ) {

			$pickup_ready_timestamp_string = $this->get_next_working_day( $shippingDay, $pickup_ready_timestamp_string );
		}

		$pickup_ready_timestamp = strtotime($pickup_ready_timestamp_string) + ( $pickup_start_time * 3600 );
		$ground_ready_timestamp = $pickup_ready_timestamp;

		if( date('Y-m-d') == date("Y-m-d", $ground_ready_timestamp) ) {

			$date 					= new DateTime( date( 'c', strtotime( '+1 days', strtotime( $pickup_ready_timestamp_string ) ) ) );
			$shippingDay 			= $date->format('D');
			$ground_ready_timestamp = strtotime( $this->get_next_working_day( $shippingDay, $pickup_ready_timestamp_string ) );
			$ground_ready_timestamp = $ground_ready_timestamp + ( $pickup_start_time * 3600 );
		}

		if( $use_pickup_address ){
			$pickup_address = array(
				'Contact'			=>	array(
					'PersonName'		=>	!empty($this->settings[ 'pickup_contact_name' ]) ? $this->settings[ 'pickup_contact_name' ] : '',
					'CompanyName'		=>	!empty($this->settings[ 'pickup_company_name' ]) ? $this->settings[ 'pickup_company_name' ] : '',
					'PhoneNumber'		=>	!empty($this->settings[ 'pickup_phone_number' ]) ? $this->settings[ 'pickup_phone_number' ] : '',
				),
				'Address'		=>	array(
					'StreetLines'			=>	!empty($this->settings[ 'pickup_address_line' ]) ? $this->settings[ 'pickup_address_line' ] : '',
					'City'					=>	!empty($this->settings[ 'pickup_address_city' ]) ? $this->settings[ 'pickup_address_city' ] : '',
					'StateOrProvinceCode'	=>	!empty($this->settings[ 'pickup_address_state_code' ]) ? $this->settings[ 'pickup_address_state_code' ] : '',
					'PostalCode'			=>	!empty($this->settings[ 'pickup_address_postal_code' ]) ? $this->settings[ 'pickup_address_postal_code' ] : '',
					'CountryCode'			=>	!empty($this->settings[ 'pickup_address_country_code' ]) ? $this->settings[ 'pickup_address_country_code' ] : '',
				)
			);
		}

		asort($order_ids);

		foreach($order_ids as $order_id){
			$order = 	$this->wf_load_order($order_id);
			if (!$order) 
				continue;
			$this->order 		= 	$order;
			$this->order_id 	= 	$order_id;
			$packages			=	$this->wf_get_package_from_order($order);

			foreach($packages as $package){

				$package = apply_filters( 'wf_customize_package_on_request_pickup', $package, $order_id );
				
				$shipmentIds 		= get_post_meta($order_id, 'wf_woo_fedex_shipmentId', false);
				// Some Customers Site wont allow adding duplicate Meta Keys in DB, Adding new meta key with custom build Shipment Id Array
				$shipment_ids 		= get_post_meta($order_id, 'ph_woo_fedex_shipmentIds', true);

				if( is_array($shipmentIds) && is_array($shipment_ids) ){
					$shipmentIds  		= array_unique(array_merge($shipmentIds,$shipment_ids));
				}

				$package_service 	= array();

				if( is_array($shipmentIds) && !empty($shipmentIds) ){
					foreach($shipmentIds as $shipmentId) {

						$package_service[$shipmentId]  	 = [];
						$package_service[$shipmentId][0] = $order_id;
						$package_service[$shipmentId][1] = get_post_meta($order_id, 'wf_woo_fedex_service_code'.$shipmentId, true);
						$package_service[$shipmentId][2] = get_post_meta($order_id, 'wf_woo_fedex_packageDetails_'.$shipmentId, true);


					}
				}else{
					wf_admin_notice::add_notice('Order #'.$order_id.' - No shipping labels found','error');
					continue;
				}
			
				if( $use_pickup_address )
				{
					$origin_country_code = !empty($this->settings[ 'pickup_address_country_code' ]) ? $this->settings[ 'pickup_address_country_code' ] : '';
				}else{
					$origin_country_code = $this->origin_country;
				}

				if ( $origin_country_code == $package['destination']['country'] )
				{
					$freight_array 	= array(
						'FEDEX_1_DAY_FREIGHT',
						'FEDEX_2_DAY_FREIGHT',
						'FEDEX_3_DAY_FREIGHT',
						'FEDEX_FIRST_FREIGHT',
						'FEDEX_FREIGHT_ECONOMY',
						'FEDEX_FREIGHT_PRIORITY',
					);
					
					$domestic_package 	= $package;
					$domestic_order_id 	= $order_id;
	
					if( !empty($package_service) ) {
						foreach ($package_service as $track_id => $service) {

							if( $service[1] == 'FEDEX_GROUND' || $service[1] == 'GROUND_HOME_DELIVERY' ) {

								$domestic_ground_order_id 		 = $order_id;
								$domestic_ground_package_count  += 1;
								$domestic_ground_weight			+= trim( preg_replace('/[A-Za-z ]/', '', $service[2]['Weight']) ) ;

							}else if( in_array( $service[1], $freight_array) ) {

								$domestic_freight_order_id 		 	 = $order_id;
								$domestic_freight_service 			 =  $service[1];
								$domestic_freight_package_count 	+= 1;
								$domestic_freight_weight 			+= trim( preg_replace('/[A-Za-z ]/', '', $service[2]['Weight']) ) ;

							}else {

								$domestic_express_order_id 			 = $order_id;
								$domestic_express_package_count 	+= 1;
								$domestic_express_weight 			+= trim( preg_replace('/[A-Za-z ]/', '', $service[2]['Weight']) ) ;
							}
						}
					}

				}else{
					
					$international_package 	= $package;
					$int_order_id 			= $order_id;

					if( !empty($package_service) ) {
						foreach ($package_service as $track_id => $service) {

							if( $service[1] == 'INTERNATIONAL_ECONOMY_FREIGHT' || $service[1] == 'INTERNATIONAL_PRIORITY_FREIGHT' )
							{

								$int_freight_order_id 		 = $order_id;
								$int_freight_service 	 	 =  $service[1];
								$int_freight_package_count  += 1;
								$domestic_ground_weight		+= trim( preg_replace('/[A-Za-z ]/', '', $service[2]['Weight']) ) ;

							}else{

								$int_express_order_id 		 = $order_id;
								$int_express_package_count 	+= 1;
								$int_express_weight 		+= trim( preg_replace('/[A-Za-z ]/', '', $service[2]['Weight']) ) ;
							}
						}
					}
				}
				
			}
		}

		if( !empty($domestic_package) && is_array($domestic_package) && !empty($domestic_order_id) )
		{
			$fedex_packages 		= $this->get_fedex_packages( $domestic_package );
			$commodoties 			= $this->get_package_one_commodoties( $fedex_packages );
			$description 			= '';

			if( !empty($commodoties) )
			{
				foreach ($commodoties as $key => $commodity_data) {
					$description .= $commodity_data['Description'] . ',';
				}
			}

			$description 	= rtrim( $description, ',' );
			$description 	= ( strlen( $description ) >= 20 ) ? substr( $description 	, 0, 17 ).'..' : $description 	;

			if($this->ship_from_address === 'shipping_address'){
				$origin_address =  $this->order_address( $domestic_package );
			}else {
				$origin_address =  $this->shop_address( $domestic_package );
			}

			if( !$use_pickup_address )
			{
				$pickup_address = array(
					'Contact'			=>	array(
						'PersonName'		=>	$origin_address['Contact']['PersonName'],
						'CompanyName'		=>	$origin_address['Contact']['CompanyName'],
						'PhoneNumber'		=>	$origin_address['Contact']['PhoneNumber'],
					),
					'Address'		=>	array(
						'StreetLines'			=>	$origin_address['Address']['StreetLines'],
						'City'					=>	$origin_address['Address']['City'],
						'StateOrProvinceCode'	=>	$origin_address['Address']['StateOrProvinceCode'],
						'PostalCode'			=>	$origin_address['Address']['PostalCode'],
						'CountryCode'			=>	$origin_address['Address']['CountryCode']
					)
				);
			}
			
			$request['CommodityDescription']			= $description;
			$request['CountryRelationship']				= 'DOMESTIC';

			$response[$domestic_order_id] 				= [];
			$response[$domestic_order_id]['Express'] 	= [];
			$response[$domestic_order_id]['Ground'] 	= [];
			$response[$domestic_order_id]['Freight'] 	= [];

			if( !empty($domestic_express_weight) && !empty($domestic_express_package_count)  ) {

				$request_express['OriginDetail']	=	array(
					'UseAccountAddress'	=>	0,
					'PickupLocation'	=>	$pickup_address,
					'ReadyTimestamp'	=>	date("Y-m-d\TH:i:s",$pickup_ready_timestamp),
					'CompanyCloseTime'	=>	date("H:i:s",$pickup_close_timestamp),
					'PackageLocation' 	=>	'FRONT',
				);

				// Service not valid in provided country
				// $request_express['PickupServiceCategory']	=	$pickup_service;
				$request_express['CarrierCode']				=	'FDXE';
				$request_express['PackageCount']			=	$domestic_express_package_count;
				$request_express['TotalWeight']				=	array(
					'Units'	=>	$this->labelapi_weight_unit,
					'Value'	=>	$domestic_express_weight
				);

				$express_request = array_merge($request, $request_express);

				$response[$domestic_express_order_id]['Express'][]	= $this->run_pickup_request($express_request);

			}

			if( !empty($domestic_ground_weight) && !empty($domestic_ground_package_count)  ) {

				$request_ground['OriginDetail']	=	array(
					'UseAccountAddress'	=>	0,
					'PickupLocation'	=>	$pickup_address,
					'ReadyTimestamp'	=>	date("Y-m-d\TH:i:s",$ground_ready_timestamp),
					'CompanyCloseTime'	=>	date("H:i:s",$pickup_close_timestamp),
					'PackageLocation' 	=>	'FRONT',
				);
				
				// ERROR: Service is only available for Express pickups
				// $request['PickupServiceCategory']	=	$pickup_service;

				$request_ground['CarrierCode']				=	'FDXG';
				$request_ground['PackageCount']			=	$domestic_ground_package_count;
				$request_ground['TotalWeight']				=	array(
					'Units'	=>	$this->labelapi_weight_unit,
					'Value'	=>	$domestic_ground_weight
				);

				$ground_request = array_merge($request, $request_ground);

				$response[$domestic_ground_order_id]['Ground'][]	= $this->run_pickup_request($ground_request);

			}

			if( !empty($domestic_freight_weight) && !empty($domestic_freight_package_count)  ) {

				$destination_address 	= $this->order_address( $domestic_package );

				$request_freight['OriginDetail']	=	array(
					'UseAccountAddress'	=>	0,
					'PickupLocation'	=>	$pickup_address,
					'ReadyTimestamp'	=>	date("Y-m-d\TH:i:s",$pickup_ready_timestamp),
					'CompanyCloseTime'	=>	date("H:i:s",$pickup_close_timestamp),
					'PackageLocation' 	=>	'FRONT',
				);

				// Invalid Service Type
				//$request_freight['PickupServiceCategory']	=	$pickup_service;

				$request_freight['CarrierCode']				=	'FXFR';
				$request_freight['PackageCount']			=	$domestic_freight_package_count;
				$request_freight['TotalWeight']				=	array(
					'Units'	=>	$this->labelapi_weight_unit,
					'Value'	=>	$domestic_freight_weight
				);

				$request_freight['FreightPickupDetail'] = array(
					'Payment' 		=> 'SENDER',
					'Role' 			=> 'SHIPPER',
					'LineItems' 	=> array(
						'Service' 		=> $domestic_freight_service,
						'Destination' 	=> array(
							'Streetlines' 			=> $destination_address['Address']['StreetLines'][0].' '.$destination_address['Address']['StreetLines'][0],
							'City' 					=> $destination_address['Address']['City'],
							'StateOrProvinceCode' 	=> $destination_address['Address']['StateOrProvinceCode'],
							'PostalCode' 			=> $destination_address['Address']['PostalCode'],
							'CountryCode' 			=> $destination_address['Address']['CountryCode'],
							'Residential' 			=> $destination_address['Address']['Residential'],
						),
						'Packaging' 			=> 'SKID',
						'Pieces'				=> '1',
						'Weight' 				=> array( 'Units' => $this->labelapi_weight_unit, 'Value' => $domestic_freight_weight ),
						'TotalHandlingUnits' 	=> '1',
						'Description'			=> $description,
					),
				);

				$freight_request = array_merge($request, $request_freight);

				$response[$domestic_freight_order_id]['Freight'][]	= $this->run_pickup_request($freight_request);

			}
			
		}

		if( !empty($international_package) && is_array($international_package) && !empty($int_order_id) )
		{
			$fedex_packages 	= $this->get_fedex_packages( $international_package);
			$commodoties 		= $this->get_package_one_commodoties( $fedex_packages );
			$description 		= '';

			if($this->ship_from_address === 'shipping_address'){
				$origin_address =  $this->order_address( $international_package );
			}else {
				$origin_address =  $this->shop_address( $international_package );
			}

			if( !$use_pickup_address )
			{
				$pickup_address = array(
					'Contact'			=>	array(
						'PersonName'		=>	$origin_address['Contact']['PersonName'],
						'CompanyName'		=>	$origin_address['Contact']['CompanyName'],
						'PhoneNumber'		=>	$origin_address['Contact']['PhoneNumber'],
					),
					'Address'		=>	array(
						'StreetLines'			=>	$origin_address['Address']['StreetLines'],
						'City'					=>	$origin_address['Address']['City'],
						'StateOrProvinceCode'	=>	$origin_address['Address']['StateOrProvinceCode'],
						'PostalCode'			=>	$origin_address['Address']['PostalCode'],
						'CountryCode'			=>	$origin_address['Address']['CountryCode']
					)
				);
			}
			
			$request['CountryRelationship']		= 'INTERNATIONAL';

			if( !empty($commodoties) )
			{
				foreach ($commodoties as $key => $commodoty_data) {
					$description .= $commodoty_data['Description'] . ',';
				}
			}

			$description 	= rtrim( $description, ',' );
			$description 	= ( strlen( $description ) >= 20 ) ? substr( $description 	, 0, 17 ).'..' : $description 	;

			$request['CommodityDescription']		= $description;

			$response[$int_order_id] 				= [];
			$response[$int_order_id]['IntExpress'] 	= [];
			$response[$int_order_id]['IntFreight'] 	= [];

			if( !empty($int_express_weight) && !empty($int_express_package_count)  ) {

				$request_int_express['OriginDetail']	=	array(
					'UseAccountAddress'	=>	0,
					'PickupLocation'	=>	$pickup_address,
					'ReadyTimestamp'	=>	date("Y-m-d\TH:i:s",$pickup_ready_timestamp),
					'CompanyCloseTime'	=>	date("H:i:s",$pickup_close_timestamp),
					'PackageLocation' 	=>	'FRONT',
				);

				// Service is not applicable for an international pickup.
				// $request_int_express['PickupServiceCategory']	=	$pickup_service;

				$request_int_express['CarrierCode']				=	'FDXE';
				$request_int_express['PackageCount']			=	$int_express_package_count;
				$request_int_express['TotalWeight']				=	array(
					'Units'	=>	$this->labelapi_weight_unit,
					'Value'	=>	$int_express_weight
				);

				$int_express_request = array_merge($request, $request_int_express);

				$response[$int_express_order_id]['IntExpress'][]	= $this->run_pickup_request($int_express_request);

			}

			if( !empty($int_freight_weight) && !empty($int_freight_package_count)  ) {

				$destination_address 	= $this->order_address( $domestic_package );

				$request_int_freight['OriginDetail']	=	array(
					'UseAccountAddress'	=>	0,
					'PickupLocation'	=>	$pickup_address,
					'ReadyTimestamp'	=>	date("Y-m-d\TH:i:s",$pickup_ready_timestamp),
					'CompanyCloseTime'	=>	date("H:i:s",$pickup_close_timestamp),
					'PackageLocation' 	=>	'FRONT',
				);
				
				// Invalid Service Type
				// $request_int_freight['PickupServiceCategory']	=	$pickup_service;

				$request_int_freight['CarrierCode']				=	'FXFR';
				$request_int_freight['PackageCount']			=	$domestic_freight_package_count;
				$request_int_freight['TotalWeight']				=	array(
					'Units'	=>	$this->labelapi_weight_unit,
					'Value'	=>	$int_freight_weight
				);

				$request_int_freight['FreightPickupDetail'] = array(
					'Payment' 		=> 'SENDER',
					'Role' 			=> 'SHIPPER',
					'LineItems' 	=> array(
						'Service' 		=> $int_freight_service,
						'Destination' 	=> array(
							'Streetlines' 			=> $destination_address['Address']['StreetLines'][0].' '.$destination_address['Address']['StreetLines'][0],
							'City' 					=> $destination_address['Address']['City'],
							'StateOrProvinceCode' 	=> $destination_address['Address']['StateOrProvinceCode'],
							'PostalCode' 			=> $destination_address['Address']['PostalCode'],
							'CountryCode' 			=> $destination_address['Address']['CountryCode'],
							'Residential' 			=> $destination_address['Address']['Residential'],
						),
						'Packaging' 			=> 'SKID',
						'Pieces'				=> '1',
						'Weight' 				=> array( 'Units' => $this->labelapi_weight_unit, 'Value' => $int_freight_weight ),
						'TotalHandlingUnits' 	=> '1',
						'Description'			=> $description,
					),
				);

				$int_freight_request = array_merge($request, $request_int_freight);

				$response[$int_freight_order_id]['IntFreight'][]	= $this->run_pickup_request($int_freight_request);

			}
			
		}
		
		return $this->process_pickup_response( $response, array( 'OrderId' => $master_order_id, 'ScheduledDate' => date("Y-m-d",$pickup_ready_timestamp) ) );
	}
	
	public function run_pickup_request($request){
		try {
			$wsdl_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/PickupService_v' . $this->pickup_service_version. '.wsdl';
			$client = $this->wf_create_soap_client( $wsdl_dir );

			if( $this->soap_method == 'nusoap' ){
				$result = $client->call( 'createPickup', array( 'CreatePickupRequest' => $request ) );
				$result = json_decode( json_encode( $result ), false );
			}
			else{
				$result = $client->createPickup($request);			
			}

		}catch(Exception $e){
			$result	=	array(
				'error'		=>	1,
				'message'	=>	$e->getMessage(),
			);
		}
		if ( $this->debug ) {
			$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
			$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
			wf_admin_notice::add_notice( 'FedEx PICKUP REQUEST: <pre>'.print_r( htmlspecialchars( $xml_request ) ,true).'</pre>', 'notice' );
			wf_admin_notice::add_notice( 'FedEx PICKUP RESPONSE: <pre>'.print_r( htmlspecialchars( $xml_response ) ,true).'</pre>', 'notice' );

			if( $this->debug ) {

				$this->admin_diagnostic_report( "------------------------------- Fedex Pickup Request -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_request ) );
				$this->admin_diagnostic_report( "------------------------------- Fedex Pickup Response -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_response ) );
			}

		}
		return $result;
	}
	
	public function process_pickup_response($response, $info = array()){

		$return	=	array();

		foreach ($response as $order_id => $result) {

			$return[$order_id]	= 	[];
			
			if( !empty( $result ) )
			{
				foreach ($result as $service => $service_data) {
			
					if( !empty($service_data) )
					{

						$return[$order_id][$service] =	array(
							'error'		=>	0,
							'message'	=>	'',
						);

						foreach ($service_data as $result_data) {

							if( is_array($result_data->Notifications) )
							{
								$return[$order_id][$service]['error']	=	1;
								$return[$order_id][$service]['message']	=	$result_data->Notifications[0]->Message;
							}else{

								if(!isset($result_data->Notifications->Code)) {
									$return[$order_id][$service]['error']	=	1;
									$return[$order_id][$service]['message']	=	'Unexpected error';
								}else if($result_data->Notifications->Code	!=	'0000') {
									$return[$order_id][$service]['error']	=	1;
									$return[$order_id][$service]['message']	=	$result_data->Notifications->Message;
								}
								else{

									if( isset($result_data->PickupConfirmationNumber) )
									{
										$return[$order_id][$service]['data']['PickupConfirmationNumber']	= $result_data->PickupConfirmationNumber;
									}

									$return[$order_id][$service]['data']['Location'] = isset($result_data->Location) ? $result_data->Location : '';	

									if( $service == 'Freight' && empty($return[$order_id][$service]['data']['Location']) && isset($result_data->CompletedFreightPickupDetail) && isset($result_data->CompletedFreightPickupDetail->Origin) && isset($result_data->CompletedFreightPickupDetail->Origin->Location) )
									{
										$return[$order_id][$service]['data']['Location'] = $result_data->CompletedFreightPickupDetail->Origin->Location;
									}

									if(is_array($info)){
										foreach($info as $param	=>	$value){
											$return[$order_id][$service]['data'][$param]	=	$value;
										}
									}
								}
							}
						}
					}
				}
			}
		}
			
		return $return;
	}
	
	public function pickup_cancel($order, $order_id, $pickup_details = array()){

		$this->order 		= 	$order;
		$this->order_id 	= 	$order_id;
		$request 			=	array();
		$response 			=	array();

		$use_pickup_address		= ( $bool = $this->settings[ 'use_pickup_address'] ) && $bool == 'yes' ? true : false;
		$order_data 			= 	$this->wf_load_order($this->order_id);

		if ( !$order_data ){
			return array(
				'error'		=>	1,
				'message'	=>	'Order Data Not Found',
			);
		}

		$packages			=	$this->wf_get_package_from_order($order_data);

		if( is_array($packages) && !empty($packages) )
		{
			$package 	= current($packages);

			if( $use_pickup_address ){

				$pickup_address = array(
					'PersonName'		=>	!empty($this->settings[ 'pickup_contact_name' ]) ? $this->settings[ 'pickup_contact_name' ] : '',
					'PhoneNumber'		=>	!empty($this->settings[ 'pickup_phone_number' ]) ? $this->settings[ 'pickup_phone_number' ] : '',
				);
			}else{

				if($this->ship_from_address === 'shipping_address'){
					$origin_address =  $this->order_address( $package );
				}else {
					$origin_address =  $this->shop_address( $package );
				}

				$pickup_address = array(
					'PersonName'		=>	$origin_address['Contact']['PersonName'],
					'PhoneNumber'		=>	$origin_address['Contact']['PhoneNumber'],
				);
			}

			$request['ContactName'] =	$pickup_address['PersonName'];

		}
		
		if( empty($pickup_details['pickup_confirmation_number']) ){
			return array(
				'error'		=>	1,
				'message'	=>	'Pickup Number Not Found',
			);
		}
		
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'      => $this->api_key,
				'Password' => $this->api_pass
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number
		);
		$request['TransactionDetail'] = array( 'CustomerTransactionId' => 'CancelPickupRequest_V23' );
		$request['Version'] = array( 'ServiceId' => 'disp', 'Major' => 23, 'Intermediate' => '0', 'Minor' => '0' );
		$request['Remarks'] =	'Pickup Cancelation Request';
		$request['Reason'] =	'Cancel Pickup';

		// $request['AssociatedAccountNumber']	=	array(
		// 	'Type'			=>	'FEDEX_EXPRESS',
		// 	'AccountNumber'	=>	$this->account_number,
		// );
		// $request['PickupServiceCategory']	=	$pickup_service;

		if( is_array( $pickup_details['pickup_confirmation_number'] ) )
		{
			foreach ($pickup_details['pickup_confirmation_number'] as $service => $pickup_number ) {

				if( $service == 'Express' )
				{
					$request['CarrierCode']				=	'FDXE';
					$request['PickupConfirmationNumber']=	trim($pickup_number);
					$request['Location']				=	$pickup_details['pickup_location'][$service];
					$request['ScheduledDate']			=	$pickup_details['pickup_scheduled_date'][$service];

					$response[]	=	$this->run_pickup_cancel($request);

				}else if( $service == 'Ground' ) {

					$request['CarrierCode']				=	'FDXG';
					$request['PickupConfirmationNumber']=	trim($pickup_number);
					$request['Location']				=	$pickup_details['pickup_location'][$service];
					$request['ScheduledDate']			=	$pickup_details['pickup_scheduled_date'][$service];

					$response[]	=	$this->run_pickup_cancel($request);

				}else if( $service == 'Freight' ) {

					$request['CarrierCode']				=	'FXFR';
					$request['PickupConfirmationNumber']=	trim($pickup_number);
					$request['Location']				=	$pickup_details['pickup_location'][$service];
					$request['ScheduledDate']			=	$pickup_details['pickup_scheduled_date'][$service];

					$response[]	=	$this->run_pickup_cancel($request);

				}else if( $service == 'IntExpress' ) {

					$request['CarrierCode']				=	'FDXE';
					$request['PickupConfirmationNumber']=	trim($pickup_number);
					$request['Location']				=	$pickup_details['pickup_location'][$service];
					$request['ScheduledDate']			=	$pickup_details['pickup_scheduled_date'][$service];

					$response[]	=	$this->run_pickup_cancel($request);

				}else if( $service == 'IntFreight' ) {

					$request['CarrierCode']				=	'FXFR';
					$request['PickupConfirmationNumber']=	trim($pickup_number);
					$request['Location']				=	$pickup_details['pickup_location'][$service];
					$request['ScheduledDate']			=	$pickup_details['pickup_scheduled_date'][$service];

					$response[]	=	$this->run_pickup_cancel($request);

				}
			}
		}

		return $this->process_pickup_cancel($response);
	}

	public function run_pickup_cancel($request){
		try {
			$wsdl_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/PickupService_v' . $this->pickup_service_version. '.wsdl';
			$client = $this->wf_create_soap_client( $wsdl_dir );
			
			if( $this->soap_method == 'nusoap' ){
				$result = $client->call( 'cancelPickup', array( 'CancelPickupRequest' => $request ) );
				$result = json_decode( json_encode( $result ), false );
			}
			else{
				$result = $client->cancelPickup($request);		
			}
		}catch(Exception $e){
			$result	=	array(
				'error'		=>	1,
				'message'	=>	$e->getMessage(),
			);
		}

		if ( $this->debug ) {
			$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
			$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
			wf_admin_notice::add_notice( 'FedEx CANCEL PICKUP REQUEST: <pre>'.print_r( htmlspecialchars( $xml_request ) ,true).'</pre>', 'notice' );
			wf_admin_notice::add_notice( 'FedEx CANCEL PICKUP RESPONSE: <pre>'.print_r( htmlspecialchars( $xml_response ) ,true).'</pre>', 'notice' );

			if( $this->debug ) {

				$this->admin_diagnostic_report( "------------------------------- Fedex Cancel Pickup Request -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_request ) );
				$this->admin_diagnostic_report( "------------------------------- Fedex Cancel Pickup Response -------------------------------" );
				$this->admin_diagnostic_report( htmlspecialchars( $xml_response ) );
			}

		}

		return $result;
	}
	
	public function process_pickup_cancel($response, $info = array()){

		$return	=	array(
			'error'		=>	0,
			'message'	=>	'',
		);

		foreach ($response as $response_data) {
			
			if( is_array($response_data->Notifications) )
			{
				$return['error']	=	1;
				$return['message']	=	$response_data->Notifications[0]->Message;
			}else{
				if( !isset($response_data->Notifications->Code) ){
					$return['error']	=	1;
					$return['message']	=	$response_data['message'];
				}else if($response_data->Notifications->Code != '0000'){
					$return['error']	=	1;
					$return['message']	=	$response_data->Notifications->Message;
				}else{
					$return['message']	=	$response_data->Notifications->Message;
				}	
			}	
		}
		
		return $return;
	}
	
	private function wf_load_order($orderId){
		if( !$orderId ){
			return false;
		}
		if(!class_exists('wf_order')){
			include_once('class-wf-legacy.php');
		}
		return ( WC()->version < '2.7.0' ) ? new WC_Order( $orderId ) : new wf_order( $orderId );   
	}
	public function get_product_price($product){
		$product = $this->wf_load_product($product);
		if($this->exclude_tax){
			return apply_filters( 'xa_order_product_price', ( ( WC()->version < '2.7.0' ) ? $product->get_price_excluding_tax() : wc_get_price_excluding_tax( $product ) ), $product, $this->order_object ) ;
		}else{
			return apply_filters( 'xa_order_product_price', $product->get_price(), $product, $this->order_object ) ;
		}
	}

	private function wf_load_product( $product ){
		if( !$product ){
			return false;
		}
		if( !class_exists('wf_product') ){
			include_once('class-wf-legacy.php');
		}
		if($product instanceof wf_product){
			return $product;
		}
		return new wf_product( $product );
	}

	/**
	 * Get Volumetric weight .
	 * @param object wf_product | wc_product object .
	 * @return float Volumetric weight if it is higher than product weight else actual product weight.
	 */
	private function xa_get_volumatric_products_weight( $values ){

		if( ! empty($this->settings['volumetric_weight']) && $this->settings['volumetric_weight'] == 'yes' ) {

			$length = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $values->get_length(), 'cm' );
			$width 	= Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $values->get_width(), 'cm' );
			$height = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $values->get_height(), 'cm' );
			if( $length != 0 && $width != 0 && $width !=0 ) {
				$volumetric_weight = $length * $width * $height /  5000; // Divide by 5000 as per fedex standard
			}
		}
		
		$weight = $values->get_weight();

		if( ! empty($volumetric_weight) ) {
			$volumetric_weight = Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $volumetric_weight, get_option( 'woocommerce_weight_unit' ), 'kg' );
			if( $volumetric_weight > $weight ) {
				$weight = $volumetric_weight;
			}
		}
		return $weight;
	}

}