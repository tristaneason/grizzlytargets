<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class wf_fedex_woocommerce_shipping_method extends WC_Shipping_Method {
	private $default_boxes;
	private $found_rates;
	private $services;

	/**
	 * Wordpress date format.
	 */
	public static $wp_date_format;

	/**
	 * FedEx Ground working days.
	 */
	public static $fedex_ground_working_days = array( "Mon", "Tue", "Wed","Thu", "Fri" );
	
	public $address_validation_contries=array('VI','VG','BR','MX','BS','KY','AR','AW','BB','BM','CL','CR','GT','JM','NL','DE','ES','GB','CH','AT','SE','EE','FI','GR','NO','PT','ZA','PA','TT','UY','VE','CO','FR','PE','SG','IT','BE','CZ','DK','CA','AU','NZ','HK','MY','US');
	
	public $fedexBoxCountries 	= array( 'CA', 'CO', 'BR' );
	public $fedexBox 			= array( 'FEDEX_SMALL_BOX','FEDEX_SMALL_BOX:2','FEDEX_MEDIUM_BOX','FEDEX_MEDIUM_BOX:2','FEDEX_LARGE_BOX','FEDEX_LARGE_BOX:2','FEDEX_EXTRA_LARGE_BOX','FEDEX_EXTRA_LARGE_BOX:2' );

	public $standard_boxes = array( 'FEDEX_SMALL_BOX','FEDEX_SMALL_BOX:2','FEDEX_MEDIUM_BOX','FEDEX_MEDIUM_BOX:2','FEDEX_LARGE_BOX','FEDEX_LARGE_BOX:2','FEDEX_EXTRA_LARGE_BOX','FEDEX_EXTRA_LARGE_BOX:2','FEDEX_PAK','FEDEX_ENVELOPE','FEDEX_10KG_BOX','FEDEX_25KG_BOX','FEDEX_BOX','FEDEX_TUBE');

	//PDS-179	
	public $prioritizedSignatureOption 	= array( 5=>'ADULT',4=>'DIRECT',3=>'INDIRECT',2=>'SERVICE_DEFAULT',1=>'NO_SIGNATURE_REQUIRED',0=>'');

	/**
	 * Current Wordpress time.
	 */
	public static $current_wp_time;

	private $transit_time = array(
		'ONE_DAY'		=> '+1day',
		'TWO_DAYS'		=> '+2days',
		'THREE_DAYS'		=> '+3days',
		'FOUR_DAYS'		=> '+4days',
		'FIVE_DAYS'		=> '+5days',
		'SIX_DAYS'		=> '+6days',
		'SEVEN_DAYS'		=> '+7days',
		'EIGHT_DAYS'		=> '+8days',
		'NINE_DAYS'		=> '+9days',
		'TEN_DAYS'		=> '+10days',
		'ELEVEN_DAYS'		=> '+11days',
		'TWELVE_DAYS'		=> '+12days',
		'THIRTEEN_DAYS'		=> '+13days',
		'FOURTEEN_DAYS'		=> '+14days',
		'FIFTEEN_DAYS'		=> '+15days',
		'SIXTEEN_DAYS'		=> '+16days',
		'SEVENTEEN_DAYS'	=> '+17days',
		'EIGHTEEN_DAYS'		=> '+18days',
		'NINETEEN_DAYS'		=> '+19days',
		'TWENTY_DAYS'		=> '+20days'
	);

	public function __construct() {
		$this->id							= WF_Fedex_ID;

		$this->method_title					= __( 'FedEx', 'wf-shipping-fedex' );
		$this->method_description 			= __( 'WooCommerce FedEx Shipping Plugin with Print Label by PluginHive', 'wf-shipping-fedex' );
		$this->rateservice_version			= 31;
		$this->addressvalidationservice_version = 4;
		$this->default_boxes				= include( 'data-wf-box-sizes.php' );
		$this->speciality_boxes				= include( 'data-wf-speciality-boxes.php' );
		$this->services						= include( 'data-wf-service-codes.php' );

		$this->init();
	}


	/**
	 * is_available function.
	 *
	 * @param array $package
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( "no" === $this->enabled ) {
			return false;
		}

		if ( 'specific' === $this->availability ) {
			if ( is_array( $this->countries ) && ! in_array( $package['destination']['country'], $this->countries ) ) {
				return false;
			}
		} elseif ( 'excluding' === $this->availability ) {
			if ( is_array( $this->countries ) && ( in_array( $package['destination']['country'], $this->countries ) || ! $package['destination']['country'] ) ) {
				return false;
			}
		}
		
		$has_met_min_amount = false;
		
		if(!method_exists(WC()->cart, 'get_displayed_subtotal')){// WC version below 2.6
			$total = WC()->cart->subtotal;
		}else{
			$total = WC()->cart->get_displayed_subtotal();

			if( version_compare( WC()->version, '4.4', '<' ) ) {
				$tax_display 	= WC()->cart->tax_display_cart;
			} else {
				$tax_display 	= WC()->cart->get_tax_price_display_mode();
			}
			
			if ( 'incl' === $tax_display ) {
				$total = $total - ( WC()->cart->get_cart_discount_total() + WC()->cart->get_cart_discount_tax_total() );
			} else {
				$total = $total - WC()->cart->get_cart_discount_total();
			}
		}
		
		if( $total < 0 )
		{		
			$total = 0;
		}
		
		if ( $total >= $this->min_amount ) {
			$has_met_min_amount = true;
		}
		$is_available	= $has_met_min_amount;
		
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}

	
	function custom_price_message( $price ) { 
		global $post;
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}
	
	private function is_soap_available(){
		if( extension_loaded( 'soap' ) ){
			return true;
		}
		return false;
	}
	
	private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		$this->soap_method = $this->is_soap_available() ? 'soap' : 'nusoap';
		if( $this->soap_method == 'nusoap' && !class_exists('nusoap_client') ){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nusoap/lib/nusoap.php';
		}

		// Define user set variables
		$this->enabled				= isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : $this->enabled;
		$this->title				= $this->get_option( 'title', $this->method_title );
		$this->availability			= isset( $this->settings['availability'] ) ? $this->settings['availability'] : 'all';
		$this->countries			= isset( $this->settings['countries'] ) ? $this->settings['countries'] : array();
		$this->origin				= apply_filters( 'woocommerce_fedex_origin_postal_code', str_replace( ' ', '', strtoupper( $this->get_option( 'origin' ) ) ) );
		$this->account_number			= $this->get_option( 'account_number' );
		$this->meter_number			= $this->get_option( 'meter_number' );
		$this->smartpost_hub			= $this->get_option( 'smartpost_hub' );
		$this->indicia				= $this->get_option( 'indicia' );
		$this->ship_from_address 		= isset($this->settings['ship_from_address'])? $this->settings['ship_from_address'] : 'origin_address';
		
		$this->api_key				= $this->get_option( 'api_key' );
		$this->api_pass				= $this->get_option( 'api_pass' );
		$this->production			= ( $bool = $this->get_option( 'production' ) ) && $bool == 'yes' ? true : false;
		$this->debug				= ( $bool = $this->get_option( 'debug' ) ) && $bool == 'yes' ? true : false;
		$this->silent_debug 		= ( isset($this->settings[ 'silent_debug' ]) && ( $bool = $this->settings[ 'silent_debug' ] ) && $bool == 'yes' ) ? true : false;
		$this->delivery_time			= ( $bool = $this->get_option( 'delivery_time' ) ) && $bool == 'yes' ? true : false;
		if( $this->delivery_time && empty(self::$wp_date_format) ) {
			self::$wp_date_format = get_option('date_format');
		}
		$this->insure_contents			= ( $bool = $this->get_option( 'insure_contents' ) ) && $bool == 'yes' ? true : false;
		$this->request_type			= $this->get_option( 'request_type', 'LIST' );

		$this->packing_method			= $this->get_option( 'packing_method', 'per_item' );
		$this->conversion_rate			= ! empty( $this->settings['conversion_rate'] ) ? $this->settings['conversion_rate'] : '';
		$this->min_shipping_cost	= ! empty($this->settings['min_shipping_cost']) ? $this->settings['min_shipping_cost'] : null;
		$this->max_shipping_cost	= ! empty($this->settings['max_shipping_cost']) ? $this->settings['max_shipping_cost'] : null;
		$this->boxes				= $this->get_option( 'boxes', array( ));
		$this->custom_services			= $this->get_option( 'services', array( ));
		$this->offer_rates			= $this->get_option( 'offer_rates', 'all' );
		$this->convert_currency_to_base		= $this->get_option( 'convert_currency');		
		$this->residential			= ( $bool = $this->get_option( 'residential' ) ) && $bool == 'yes' ? true : false;
		$this->freight_enabled			= ( $bool = $this->get_option( 'freight_enabled' ) ) && $bool == 'yes' ? true : false;
		$this->saturday_pickup			= ( $bool = $this->get_option( 'saturday_pickup' ) ) && $bool == 'yes' ? true : false;
		$this->fedex_one_rate			= ( $bool = $this->get_option( 'fedex_one_rate' ) ) && $bool == 'yes' ? true : false;
		$this->fedex_one_rate_package_ids = array(
			'FEDEX_SMALL_BOX',
			'FEDEX_MEDIUM_BOX',
			'FEDEX_LARGE_BOX',
			'FEDEX_EXTRA_LARGE_BOX',
			'FEDEX_PAK',
			'FEDEX_ENVELOPE',
		);

		$this->fedex_cod_rate 		= ( isset($this->settings['fedex_cod_rate']) && !empty($this->settings['fedex_cod_rate']) && $this->settings['fedex_cod_rate'] == 'yes' ) ? true : false;
		$shipping_type          	= ( isset($this->settings['fedex_duties_and_taxes_rate']) && !empty($this->settings['fedex_duties_and_taxes_rate']) && $this->settings['fedex_duties_and_taxes_rate'] == 'yes' ) ? 'DUTIES_AND_TAXES' : 'NET_CHARGE';
		$this->shipping_charge 	    = isset ( $this->settings['shipping_quote_type'] ) && !empty($this->settings['shipping_quote_type']) ? $this->settings['shipping_quote_type'] : $shipping_type;  
		$this->saturday_delivery 	= ( isset($this->settings['saturday_delivery']) && !empty($this->settings['saturday_delivery']) && $this->settings['saturday_delivery'] == 'yes' ) ? true : false;
		
		$this->delivery_time_details		= '';
		$this->box_max_weight			= $this->get_option( 'box_max_weight' );
		$this->weight_pack_process		= $this->get_option( 'weight_pack_process' );
		
		if($this->get_option( 'dimension_weight_unit' ) == 'LBS_IN'){
			$this->dimension_unit		= 'in';
			$this->weight_unit		= 'lbs';
			$this->labelapi_dimension_unit	= 'IN';
			$this->labelapi_weight_unit 	= 'LB';
		}else{
			$this->dimension_unit		= 'cm';
			$this->weight_unit		= 'kg';
			$this->labelapi_dimension_unit	= 'CM';
			$this->labelapi_weight_unit 	= 'KG';
			$this->default_boxes		= include( 'data-wf-box-sizes-cm.php' );
		}
		if ( $this->freight_enabled ) {
			$this->freight_class			= $this->get_option( 'freight_class' );
			$this->freight_number			= $this->get_option( 'freight_number', $this->account_number );
			$this->freight_bill_street		= $this->get_option( 'freight_bill_street' );
			$this->freight_billing_street_2		= $this->get_option( 'billing_street_2' );
			$this->freight_billing_city		= $this->get_option( 'freight_billing_city' );
			$this->freight_billing_state		= $this->get_option( 'freight_billing_state' );
			$this->freight_billing_postcode		= $this->get_option( 'billing_postcode' );
			$this->freight_billing_country		= $this->get_option( 'billing_country' );
			$this->frt_shipper_street		= $this->get_option( 'frt_shipper_street' );
			$this->freight_shipper_street_2		= $this->get_option( 'shipper_street_2' );
			$this->freight_shipper_city		= $this->get_option( 'freight_shipper_city' );
			$this->freight_shipper_residential	= ( $bool = $this->get_option( 'shipper_residential' ) ) && $bool == 'yes' ? true : false;
			$this->freight_class			= str_replace( array( 'CLASS_', '.' ), array( '', '_' ), $this->freight_class );
		}
		$this->is_dry_ice_enabled 	= isset( $this->settings['dry_ice_enabled'] ) && $this->settings['dry_ice_enabled'] =='yes' ? true : false;
		$this->dropoff_type 		= isset($this->settings['dropoff_type']) && !empty($this->settings['dropoff_type']) ? $this->settings['dropoff_type'] : 'REGULAR_PICKUP';
		
		$this->signature_option 	= isset ( $this->settings['signature_option'] ) ? $this->settings['signature_option'] : '';
		$this->signature_option 	= array_search($this->signature_option, $this->prioritizedSignatureOption);
		$this->min_amount	  		= isset( $this->settings['min_amount'] ) ? $this->settings['min_amount'] : 0;
		$this->customs_duties_payer	= isset ( $this->settings['customs_duties_payer'] ) ? $this->settings['customs_duties_payer'] : '';
		$this->enable_speciality_box	= ( $bool = $this->get_option( 'enable_speciality_box' ) ) && $bool == 'yes' ? true : false;
		$this->ship_time_adjustment = isset( $this->settings['ship_time_adjustment']) ? $this->settings['ship_time_adjustment'] : 1;
		$this->wc_store_currency		= get_woocommerce_currency();
		$this->fedex_currency			= ! empty($this->settings['fedex_currency']) ? $this->settings['fedex_currency'] : $this->wc_store_currency;
		$this->fedex_conversion_rate	= ! empty($this->settings['fedex_conversion_rate']) ? (float) $this->settings['fedex_conversion_rate'] : 1;

		$this->fallback_rate	= ( isset($this->settings['fedex_fallback']) && !empty($this->settings['fedex_fallback']) ) ? $this->settings['fedex_fallback'] : '';
		$this->cut_off_time 	= ( isset($this->settings['cut_off_time']) && !empty($this->settings['cut_off_time']) ) ? $this->settings['cut_off_time'] : '';
		$this->global_hs_code 	= ( isset($this->settings['global_hs_code']) && !empty($this->settings['global_hs_code']) ) ? $this->settings['global_hs_code'] : '';

		if( $this->saturday_pickup ) {

			$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
		} else {
			$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' );
		}

		$this->working_days 	= ( isset($this->settings['fedex_working_days']) && !empty($this->settings['fedex_working_days']) ) ? $this->settings['fedex_working_days'] : $working_days;

		$this->satday_rates 	= false;

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

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		// To add Fields to checkout like Lift gate delivery , Inside delivery etc.
		add_filter( 'woocommerce_checkout_fields' , array( $this, 'xa_add_fields_to_checkout') );
		
		// Add Liftgate, Inside Delivery to cart shipping packages.
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'ph_fedex_liftgate_inside_delivery_in_checkout_package'),10,1 );

		//add_action( 'woocommerce_checkout_update_order_review', array($this,'wf_fedex_update_checkout_fields'), 1, 1 );

		// To save the fedex option selected on checkout page like liftgate_delivery, inside_delivery
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'custom_checkout_field_update_order_meta' ) );
	}

	/**
	 * Convert the Cost to FedEx Currency.
	 */
	public function convert_to_fedex_currency( $cost ) {
		if( $this->fedex_currency != $this->wc_store_currency && ! empty($this->fedex_conversion_rate) ) {
			$fedex_conversion_rate	= apply_filters('ph_fedex_currency_conversion_rate',$this->fedex_conversion_rate,$this->fedex_currency);   //currency switcher
			$cost = (float) $cost * $fedex_conversion_rate;
		}
		return $cost;
	}

	/**
	 * Function to update the custom checkout option to the order meta . Like - Liftgate_delivery, Inside_delivery etc.
	 */
	public function custom_checkout_field_update_order_meta( $order_id ) {
		// Update Lift Gate Delivery to the order meta
		if( ! empty($_POST['xa_fedex_lift_gate_for_delivery']) ) {
			update_post_meta( $order_id, 'xa_fedex_lift_gate_for_delivery', esc_attr($_POST['xa_fedex_lift_gate_for_delivery']) );
		}
		// Update Inside Delivery to the order meta
		if( ! empty($_POST['xa_fedex_inside_delivery']) ) {
			update_post_meta( $order_id, 'xa_fedex_inside_delivery', esc_attr($_POST['xa_fedex_inside_delivery']) );
		}

		if( !is_admin( ) ) {

			$suggested_adress 	= WC()->session->get( 'ph_address_validation' );
			$meta_key 			= get_post_meta( $order_id, 'ph_fedex_suggested_address' );

			if( !empty($suggested_adress) && is_array($suggested_adress) && empty($meta_key) )
			{

				update_post_meta( $order_id, 'ph_fedex_suggested_address', $suggested_adress );

				$order 		= new WC_Order( $order_id );
				$address 	= $suggested_adress['suggested_street'].', '.$suggested_adress['suggested_city'].', '.$suggested_adress['suggested_state'].', '.$suggested_adress['suggested_country'].' - '.$suggested_adress['suggested_postcode'];
				
				$order->add_order_note( __( 'FedEx Address Suggestion: '.$address, 'wf-shipping-fedex' ) );

				WC()->session->set( 'ph_address_validation', '' );
			}
			
		}
	}

	/**
	 * Function to add custom fields in checkout Page like lift gat delivery, inside delivery etc.
	 * @param $fields array Array of checkout fields.
	 * @return array Array of updated checkout fields.
	 */
	public function xa_add_fields_to_checkout( $fields ){

		// Lift Gate Delivery
		if( ! empty($this->settings['lift_gate_for_delivery']) && $this->settings['lift_gate_for_delivery'] == 'yes' ) {
			$fields['billing']['xa_fedex_lift_gate_for_delivery'] = array(
				'label'			=> __('Lift gate required on delivery', 'wf-shipping-fedex'),
				'required'		=> false,
				'clear'			=> false,
				'type'			=> 'checkbox',
				'class'			=> array ('address-field', 'update_totals_on_change' ),
			);
		}
		// Inside delivery
		if( ! empty($this->settings['inside_delivery']) && $this->settings['inside_delivery'] == 'yes' ) {
			$fields['billing']['xa_fedex_inside_delivery'] = array(
				'label'			=> __('Inside delivery', 'wf-shipping-fedex'),
				'required'		=> false,
				'clear'			=> false,
				'type'			=> 'checkbox',
				'class'			=> array ('address-field', 'update_totals_on_change' ),
			);
		}
		
		return $fields;
	}

	/** 
	 * Function to trigger Calculate Shipping if Lift Gate checkbox status is changed on checkout page.
	 */
	// public function wf_fedex_update_checkout_fields($updated_data){
	// 	$updated_fields = explode("&",$updated_data);
	// 	if(is_array($updated_fields)){
	// 		foreach($updated_fields as $updated_field){
	// 			$updated_field_values = explode('=',$updated_field);
	// 			if(is_array($updated_field_values)){
	// 				// Lift Gate Delivery
	// 				if(in_array('xa_fedex_lift_gate_for_delivery',$updated_field_values)){
	// 					$this->wf_update_checkout_custom_field_data('xa_fedex_lift_gate_for_delivery', urldecode($updated_field_values[1]) );
	// 				}
	// 				// Inside Delivery
	// 				if(in_array('xa_fedex_inside_delivery',$updated_field_values)){
	// 					$this->wf_update_checkout_custom_field_data('xa_fedex_inside_delivery', urldecode($updated_field_values[1]));
	// 				}
	// 			}
	// 		}
	// 	}
	// 	WC()->cart->calculate_shipping();
	// }

	/**
	 * Function to set the Checkout custom field data.
	 */
	// private function wf_update_checkout_custom_field_data( $field, $value = null ){

	// 	switch($field) {
	// 		case 'xa_fedex_lift_gate_for_delivery':
	// 					if( WC()->version < '2.7.0' ) {
	// 						WC()->customer->__set( 'xa_fedex_lift_gate_for_delivery', $value );
	// 					}
	// 					else{
	// 						WC()->customer->update_meta_data( 'xa_fedex_lift_gate_for_delivery', $value );
	// 					}
	// 					break;
	// 		case 'xa_fedex_inside_delivery':
	// 					if( WC()->version < '2.7.0' ) {
	// 						WC()->customer->__set( 'xa_fedex_inside_delivery', $value );
	// 					}
	// 					else {
	// 						WC()->customer->update_meta_data( 'xa_fedex_inside_delivery', $value );
	// 					}
	// 					break;

	// 		default : 	break;
	// 	}
	// }

	/**
     * Update Liftgate, Inside Delivery in Woocommerce Packages.
     * @param array $packages Array of Woocommerce Packages.
     * @return array
    **/
	public function ph_fedex_liftgate_inside_delivery_in_checkout_package( $packages ) {

		if(isset($_POST['post_data']))
		{
			parse_str($_POST['post_data'], $data );

			foreach( $packages as &$package ) {
				if( ! empty($package['contents']) ) {

					$liftgate_delivery = isset($data['xa_fedex_lift_gate_for_delivery'])?$data['xa_fedex_lift_gate_for_delivery']:'';
					$inside_delivery = isset($data['xa_fedex_inside_delivery'])?$data['xa_fedex_inside_delivery']:'';

					if( !empty($liftgate_delivery) )
					{
						$package['xa_fedex_lift_gate_for_delivery'] = $liftgate_delivery;
					}

					if( !empty($inside_delivery) )
					{
						$package['xa_fedex_inside_delivery'] = $inside_delivery;
					}
				}
			}
		}
		return $packages;
	}
	
	private function set_origin_country_state(){
		$origin_country_state 		= isset( $this->settings['origin_country'] ) ? $this->settings['origin_country'] : '';
		if ( strstr( $origin_country_state, ':' ) ) :
			// WF: Following strict php standards.
			$origin_country_state_array			= explode(':',$origin_country_state);
			$origin_country					= current($origin_country_state_array);
			$origin_country_state_array			= explode(':',$origin_country_state);
			$origin_state					= end($origin_country_state_array);
		else :
			$origin_country					= $origin_country_state;
			$origin_state					= '';
			$this->settings[ 'freight_shipper_state' ]	= '';
		endif;

		$this->origin_country  	= apply_filters( 'woocommerce_fedex_origin_country_code', $origin_country );
		$this->origin_state 	= !empty($origin_state) ? $origin_state : ( isset($this->settings[ 'freight_shipper_state' ]) ? $this->settings[ 'freight_shipper_state' ] : '' );

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

	public function debug( $message, $type = 'notice' ) {
		if ( $this->debug && function_exists('wc_add_notice') && !$this->silent_debug ) {
			wc_add_notice( $message, $type );
		}
	}

	public function diagnostic_report( $data ) {
	
		if( function_exists("wc_get_logger") ) {

			$log = wc_get_logger();
			$log->debug( ($data).PHP_EOL.PHP_EOL, array('source' => 'PluginHive-FedEx-Error-Debug-Log'));
		}
	}	
	
	/**
	 * Get FedEx Currency.
	 */
	public function wf_get_fedex_currency(){
		$wc_currency = $this->fedex_currency;
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

	private function environment_check() {
		if ( ! in_array( get_woocommerce_currency(), array( 'USD' ) )) {
			echo '<div class="notice">
				<p>' . __( 'FedEx API returns the rates in USD. Please enable Rates in base currency option in the plugin. Conversion happens only if FedEx API provide the exchange rates.', 'wf-shipping-fedex' ) . '</p>
			</div>'; 
		} 
			
		if ( ! $this->origin && $this->enabled == 'yes' ) {
			echo '<div class="error">
				<p>' . __( 'FedEx is enabled, but the origin postcode has not been set.', 'wf-shipping-fedex' ) . '</p>
			</div>';
		}
	}

	public function admin_options() {
		// Check users environment supports this method
		$this->environment_check();

		// Show settings
		parent::admin_options();
	}

	public function init_form_fields() {
		if( is_admin() && ! did_action('wp_enqueue_media') && isset($_GET['section']) &&  $_GET['section'] == 'wf_fedex_woocommerce_shipping'){
			wp_enqueue_media();
		}
		$this->form_fields  = include( 'data-wf-settings.php' );
	}

	public function generate_single_select_country_html() {
		global $woocommerce;
		ob_start();
		?>
		<tr valign="top" class="fedex_general_tab">
			<th scope="row" class="titledesc">
				<label for="origin_country"><?php _e( 'Origin Country and State', 'wf-shipping-fedex' ); ?></label>
			</th>
			<td class="forminp">
				<select name="woocommerce_origin_country_state" id="woocommerce_origin_country_state" style="width: 250px;" data-placeholder="<?php _e('Choose a country&hellip;', 'woocommerce'); ?>" title="Country" class="chosen_select">
					<?php echo $woocommerce->countries->country_dropdown_options( $this->origin_country, $this->origin_state ? $this->origin_state : '*' ); ?>
				</select>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 *
	 * generate_alt_return_country_state_html function
	 *
	 * @access public
	 * @return void
	 */
	function generate_alt_return_country_state_html() {

		global $woocommerce;

		ob_start();
		?>
		<tr valign="top" class="fedex_general_tab ph_fedex_alt_return_address">

			<th scope="row" class="titledesc">
				<label for="woocommerce_wf_fedex_woocommerce_shipping_alt_return_country_state">
					<?php _e( 'Alternate Return Country', 'wf-shipping-fedex' ); ?>

				</label>
			</th>

			<td class="forminp">
				<select name="woocommerce_wf_fedex_woocommerce_shipping_alt_return_country_state" id="woocommerce_wf_fedex_woocommerce_shipping_alt_return_country_state" style="width: 250px;" data-placeholder="<?php _e('Choose a Country&hellip;', 'woocommerce'); ?>" title="Country" class="chosen_select">
					<?php echo $woocommerce->countries->country_dropdown_options( $this->alt_return_country, $this->alt_return_state ? $this->alt_return_state : '*' ); ?>
				</select>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	public function generate_settings_tabs_html()
	{
		$current_tab = (!empty($_GET['subtab'])) ? esc_attr($_GET['subtab']) : 'general';

		echo '
			<div class="wrap">
			<style>
				.wrap {
					min-height: 800px;
					}
				a.nav-tab{
					cursor: default;
				}
				.nav-tab-active{
					height: 24px;
				}
			</style>
			<hr class="wp-header-end">';

			$tabs = array(
				'general' 			=> __("General", 'wf-shipping-fedex'),
				'rates' 			=> __("Rates & Services", 'wf-shipping-fedex'),
				'labels' 			=> __("Label Generation", 'wf-shipping-fedex'),
				'commercial_invoice'=> __("International Forms", 'wf-shipping-fedex'),
				'special_services'	=> __("Special Services", 'wf-shipping-fedex'),
				'packaging' 		=> __("Packaging", 'wf-shipping-fedex'),
				'pickup' 			=> __("Pickup", 'wf-shipping-fedex'),
				'freight' 			=> __("Freight", 'wf-shipping-fedex'),
				'help_and_support ' => __("Help & Support", 'wf-shipping-fedex'),
			);

			$html = '<h2 class="nav-tab-wrapper">';
			foreach ($tabs as $stab => $name) {
				$class = ($stab == $current_tab) ? 'nav-tab-active' : '';
				$html .= '<a style="text-decoration:none !important;" class="nav-tab ph-fedex-tab ' . $class." tab_".$stab . '" >' . $name . '</a>';
			}
			$html .= '</h2>';
			echo $html;

	}

	public function generate_help_support_section_html() {

		ob_start();
		include( 'html-ph-help-and-support.php' );
		return ob_get_clean();
	}

	public function generate_services_html() {
		ob_start();
		include( 'html-wf-services.php' );
		return ob_get_clean();
	}

	public function generate_box_packing_html() {
		ob_start();
		include( 'html-wf-box-packing.php' );
		return ob_get_clean();
	}

	public function generate_validate_button_html(){
		ob_start();?>
			<tr style="padding-top: 0px;" class="fedex_general_tab">
				<td></td>
				<td style="vertical-align: top;padding-top: 0px;">
					<input type="button" value=" Validate Credentials" id="xa_fedex_validate_credentials" class="button button-secondary" name="xa_fedex_validate_credentials" size="5" >
					<p class="fedex-validation-result"></p>
				</td>
			</tr><?php
		return ob_get_clean();
	}

	private function merge_with_speciality_box($boxes=''){
		if( empty($boxes) )
			return;
		foreach ($this->speciality_boxes as $sp_key => $sp_box) {
			$found = 0;
			foreach ($boxes as $key => $box) {
				if( isset( $box['box_type'] ) && $box['box_type'] == $sp_box['box_type'] ){
					$found = 1;
				}
			}
			if( $found == 0 ){
				array_unshift($boxes, $sp_box);
			}
		}
		return $boxes;
	}
	public function validate_box_packing_field( $key ) {
		$box_type	 		= isset( $_POST['box_type'] ) ? $_POST['box_type'] : array();
		$boxes_name			 = isset( $_POST['boxes_name'] ) ? $_POST['boxes_name'] : array();
				$boxes_length	 	= isset( $_POST['boxes_length'] ) ? $_POST['boxes_length'] : array();
		$boxes_width	  	= isset( $_POST['boxes_width'] ) ? $_POST['boxes_width'] : array();
		$boxes_height	 	= isset( $_POST['boxes_height'] ) ? $_POST['boxes_height'] : array();

		$boxes_inner_length	= isset( $_POST['boxes_inner_length'] ) ? $_POST['boxes_inner_length'] : array();
		$boxes_inner_width	= isset( $_POST['boxes_inner_width'] ) ? $_POST['boxes_inner_width'] : array();
		$boxes_inner_height	= isset( $_POST['boxes_inner_height'] ) ? $_POST['boxes_inner_height'] : array();
		
		$boxes_box_weight 	= isset( $_POST['boxes_box_weight'] ) ? $_POST['boxes_box_weight'] : array();
		$boxes_max_weight 	= isset( $_POST['boxes_max_weight'] ) ? $_POST['boxes_max_weight'] :  array();
		$boxes_enabled		= isset( $_POST['boxes_enabled'] ) ? $_POST['boxes_enabled'] : array();

		$boxes = array();

		// For Standard Boxes,Custom Boxes and Speciality Boxes
		if ( !empty( $boxes_name ) && sizeof( $boxes_name ) > 0 ) {
			
			foreach ($boxes_name as $key => $value) {

				if ( !in_array($key, $this->standard_boxes) || is_numeric( $key ) ) {
					$box_pack_type  =  isset( $box_type[ $key] ) ? $box_type[ $key ] : '';
					$box_id         = '';
				}
				else{
					$box_pack_type  = 'standard_box';
					$box_id         = $key;
				}
				
				if ( $boxes_length[ $key ] && $boxes_width[ $key ] && $boxes_height[ $key ] ) {

					$boxes[$key] = array(
						'box_type'	=> $box_pack_type,
						'name'		=> strval($boxes_name[$key]),
						'length'	=> floatval( $boxes_length[ $key ] ),
						'width'		=> floatval( $boxes_width[ $key ] ),
						'height'	=> floatval( $boxes_height[ $key ] ),

						/* Old version compatibility: If inner dimensions are not provided, assume outer dimensions as inner.*/
						'inner_length'	=> isset( $boxes_inner_length[ $key ] ) ? floatval( $boxes_inner_length[ $key ] ) : floatval( $boxes_length[ $key ] ),
						'inner_width'	=> isset( $boxes_inner_width[ $key ] ) ? floatval( $boxes_inner_width[ $key ] ) : floatval( $boxes_width[ $key ] ), 
						'inner_height'	=> isset( $boxes_inner_height[ $key ] ) ? floatval( $boxes_inner_height[ $key ] ) : floatval( $boxes_height[ $key ] ),
						
						'box_weight'	=> floatval( $boxes_box_weight[ $key ] ),
						'max_weight'	=> floatval( $boxes_max_weight[ $key ] ),
						'enabled'		=> isset( $boxes_enabled[ $key ] ) ? true : false
					);
					if(!empty( $box_id )) {
						$boxes[$key]['id'] = $box_id ;
					}
				}
			}
		}

		// For Backward Compatibility
		foreach ( $this->default_boxes as $box ) {

			if( isset($boxes[$box['id']]) ){
				continue;
			}

			$boxes[ $box['id'] ] = array(
				'enabled' => isset( $boxes_enabled[ $box['id'] ] ) ? true : false
			);
		}
		return $boxes;
	}

	public function validate_single_select_country_field( $key ) {

		if ( isset( $_POST['woocommerce_origin_country_state'] ) )
			return $_POST['woocommerce_origin_country_state'];
		return '';
	}

	public function validate_alt_return_country_state_field( $key ) {

		if ( isset( $_POST['woocommerce_wf_fedex_woocommerce_shipping_alt_return_country_state'] ) )
			return $_POST['woocommerce_wf_fedex_woocommerce_shipping_alt_return_country_state'];
		return '';
	}
	
	public function validate_services_field( $key ) {
		$services		 = array();
		$posted_services  = $_POST['fedex_service'];

		foreach ( $posted_services as $code => $settings ) {
			$services[ $code ] = array(
				'name'			   => wc_clean( $settings['name'] ),
				'order'			  => wc_clean( $settings['order'] ),
				'enabled'			=> isset( $settings['enabled'] ) ? true : false,
				'adjustment'		 => wc_clean( $settings['adjustment'] ),
				'adjustment_percent' => str_replace( '%', '', wc_clean( $settings['adjustment_percent'] ) )
			);
		}

		return $services;
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

	public function get_freight_class( $item_data ){

		global $wp_version;

		$item_data = $this->wf_load_product($item_data);
		if($item_data->variation_id){
			$class	=	get_post_meta( $item_data->variation_id, '_wf_freight_class', true );
		}
		
		if(!$class)
			$class	=	get_post_meta($item_data->id, '_wf_freight_class', true );
		
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

		$to_ship  = array();
		$group_id = 1;

		// Get weight of order
		foreach ( $package['contents'] as $item_id => $values ) {

			$values['data'] = $this->wf_load_product( $values['data'] );

			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support product addon, WooCommerce Measurement Price Calculator plugin
			$signature = '';
			foreach( $additional_products as $values) {

				if ( ! $values['data']->needs_shipping() ) {

					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );
					
					$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				
				if($skip_product) {

					$this->diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Rate Calculation.', $values['data']->get_id() ) );

					continue;
				}

				if ( ! $values['data']->get_weight() ) {
					$this->debug( sprintf( __( 'FedEx - Product Weight Missing, Aborting! Product Id - %d, Product Name - %s.', 'wf-shipping-fedex' ), $values['data']->get_id(), $values['data']->get_name() ), 'error' );

					$this->diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Rate Calculation.', $values['data']->get_id() ) );

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
					'GroupNumber'		=> $group_id,
					'GroupPackageCount'	=> $values['quantity'],
					'Weight'			=> array(

						'Value'		=> $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $values['data']->get_weight(), $this->weight_unit ), 2 ),
						'Units'		=> $this->labelapi_weight_unit
					),
					'packed_products'	=> array( $values['data'] )
				);

				$group['InsuredValue']	= array(

					'Amount'		=> $this->convert_to_fedex_currency($this->wf_get_insurance_amount($values['data'])),
					'Currency'		=> $this->wf_get_fedex_currency()
				);

				//PDS-179
				if( isset($signature) && !empty($signature)){
					$group['signature_option']	= $signature ;
				}
				else{
					$group['signature_option']	=  $this->signature_option ;
				}

				$dimensions = array( $values['data']->get_length(), $values['data']->get_width(), $values['data']->get_height() );

				if ( $dimensions[0] && $dimensions[1] && $dimensions[2] ) {

					sort( $dimensions );

					$group['Dimensions'] = array(

						'Length' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
						'Width'  => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
						'Height' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
						'Units'  => $this->labelapi_dimension_unit
					);
				}

				if ( $this->freight_enabled ) {

					$group['PhysicalPackaging'] 			= 'SKID';
					$group['AssociatedFreightLineItems'] 	= array(
						'Id'	=> $group_id,
					);
				}

				$to_ship[] = $group;

				$group_id++;
			}
			
		}

		return $to_ship;
	}
	
	private function wf_fedex_add_pre_packed_product( $pre_packed_items,$group_id=1,$weightflag=false ) {
		$to_ship  	= array();
		$signature 	= '';

		foreach ( $pre_packed_items as $item_id => $values ) {
			$values['data'] = $this->wf_load_product( $values['data'] );

			if ( ! $values['data']->needs_shipping() ) {
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

				$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation.', $values['data']->get_id() ) );

				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				$this->debug( sprintf( __( 'FedEx - Product Weight Missing, Aborting! Product Id - %d, Product Name - %s.', 'wf-shipping-fedex' ), $values['data']->get_id(), $values['data']->get_name() ), 'error' );

				$this->diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Rate Calculation.', $values['data']->get_id() ) );

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
				'GroupPackageCount'		=> $values['quantity'],
				'Weight'				=> array(
					'Value' 	=> $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $values['data']->get_weight(), $this->weight_unit ), 2 ),
					'Units' 	=> $this->labelapi_weight_unit
				),
				'packed_products' 		=> array( $values['data'] )
			);

			$group['InsuredValue'] = array(

				'Amount'   => $this->convert_to_fedex_currency($this->wf_get_insurance_amount($values['data'])),
				'Currency' => $this->wf_get_fedex_currency()
			);

			//PDS-179
			if( isset($signature) && !empty($signature)){
				$group['signature_option']	= $signature ;
			}
			else{
				$group['signature_option']	=  $this->signature_option ;
			}

			if ( $values['data']->length && $values['data']->height && $values['data']->width && !$weightflag ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_width(), $values['data']->get_height() );

				sort( $dimensions );
				
				$group['Dimensions'] = array(

					'Length' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[2], $this->dimension_unit ), 0 ) ),
					'Width'  => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[1], $this->dimension_unit ), 0 ) ),
					'Height' => max( 1, round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_dimension( $dimensions[0], $this->dimension_unit ), 0 ) ),
					'Units'  => $this->labelapi_dimension_unit
				);
				
			}

			if ( $this->freight_enabled ) {

				$group['PhysicalPackaging'] 			= 'SKID';
				$group['AssociatedFreightLineItems'] 	= array(
					'Id'	=> $group_id,
				);
			}
			
			$to_ship[] = $group;
			$group_id++;
		}

		return $to_ship;
	}

	private function round_up( $value, $precision=2 ) { 
		$pow = pow ( 10, $precision ); 
		return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
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
			
			if( $package['destination']['country'] == $this->origin_country && ( isset($box['id']) && ($box['id']=='FEDEX_25KG_BOX' || $box['id']=='FEDEX_10KG_BOX') ) ) {
				continue;
			}
			
			if ( ! is_numeric( $key ) && !in_array($key, $this->standard_boxes) ) {
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
			$values['data'] = $this->wf_load_product( $values['data'] );

			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support product addon, WooCommerce Measurement Price Calculator plugin
			
			foreach( $additional_products as $values) {
				if ( ! $values['data']->needs_shipping() ) {
					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product',false, $values, $package['contents']);
				
				if($skip_product) {

					$this->diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Rate Calculation.', $values['data']->get_id() ) );

					continue;
				}

				$pre_packed = $this->ph_get_post_meta_key($values['data'] , '_wf_fedex_pre_packed_var', 1);

				if( empty( $pre_packed ) || $pre_packed == 'no' ){
					$pre_packed = $this->ph_get_post_meta_key( $values['data'] , '_wf_fedex_pre_packed', 1);
				}

				$pre_packed = apply_filters('wf_fedex_is_pre_packed',$pre_packed,$values);

				if( !empty($pre_packed) && $pre_packed == 'yes' ){
					$pre_packed_contents[] = $values;
					$this->debug( sprintf( __( 'Pre Packed product. Skipping the product '.$values['data']->id, 'wf-shipping-fedex' ), $item_id ) );

					$this->diagnostic_report( sprintf( 'Product #%d is pre packed. Skipping the Product from Box Packing Algorithm.', $values['data']->get_id() ) );

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
							$values['data']->get_price(),
							array(
								'data' => $values['data']
							)
						);
					}

				} else {
					$this->debug( sprintf( __( 'FedEx - Product Dimensions Missing, Aborting! Product Id - %d, Product Name - %s.', 'wf-shipping-fedex' ), $values['data']->get_id(), $values['data']->get_name() ), 'error' );

					$this->diagnostic_report( sprintf( 'Product #%d is missing Dimensions. Aborting Rate Calculation.', $values['data']->get_id() ) );

					return;
				}
			}
			
			
		}

		// Pack it
		$boxpack->pack();
		$packages 	= $boxpack->get_packages();
		$to_ship  	= array();
		$group_id 	= 1;
		$freight_id = 1;

		foreach ( $packages as $package ) {

			if ( $package->unpacked === true ) {
				$this->debug( 'Unpacked Item' );
			} else {
				$this->debug( 'Packed ' . $package->id );
			}

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );
			
			// Insurance amount of box $boxinsuredprice
			$boxinsuredprice 		= 0;
			$associatedLineItemId 	= array();

			if( ! empty($package->packed) ) {
                
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

			$group = array(
				'GroupNumber'			=> $group_id,
				'GroupPackageCount'		=> 1,
				'Weight'				=> array(
					'Value'			=> $this->round_up( $package->weight, 2 ),
					'Units'			=> $this->labelapi_weight_unit
				),
				'Dimensions'		=> array(
					'Length'	=> max( 1, round( $dimensions[2], 0 ) ),
					'Width'		=> max( 1, round( $dimensions[1], 0 ) ),
					'Height'	=> max( 1, round( $dimensions[0], 0 ) ),
					'Units'		=> $this->labelapi_dimension_unit
				),
				'InsuredValue'		=> array(
					'Amount'	=> $this->convert_to_fedex_currency($boxinsuredprice),
					'Currency'	=> $this->wf_get_fedex_currency()
				),
				'packed_products'	=> array(),
				'package_id'		=> $package->id
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

			if ( $this->freight_enabled && !empty( $package->packed ) && is_array( $package->packed ) ) {

				$group['PhysicalPackaging'] 			= 'SKID';
				$group['AssociatedFreightLineItems'] 	= array_values($associatedLineItemId);
			}

			$to_ship[] = $group;

			$group_id++;

		}

		//add pre packed item with the packagee
		if( !empty($pre_packed_contents) ){
			$prepacked_requests = $this->wf_fedex_add_pre_packed_product( $pre_packed_contents, $group_id);
			$to_ship = array_merge($to_ship, $prepacked_requests);
		}

		return $to_ship;
	}
	
	private function weight_based_shipping( $package ){

		if ( ! class_exists( 'Ph_Fedex_WeightPack' ) ) {
			include_once 'weight_pack/class-wf-weight-packing.php';
		}
		
		$weight_pack = new Ph_Fedex_WeightPack($this->weight_pack_process);
		$weight_pack->set_max_weight($this->box_max_weight);
		
		foreach ( $package['contents'] as $item_id => $values ) {

			$values['data'] = $this->wf_load_product( $values['data'] );

			$additional_products = apply_filters( 'xa_alter_products_list', array($values) );	// To support product addon, WooCommerce Measurement Price Calculator plugin
			
			foreach( $additional_products as $values) {

				if ( ! $values['data']->needs_shipping() ) {

					$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'wf-shipping-fedex' ), $item_id ), 'error' );

					$this->diagnostic_report( sprintf( 'Product #%d is virtual. Skipping from Rate Calculation.', $values['data']->get_id() ) );

					continue;
				}

				$skip_product = apply_filters('wf_shipping_skip_product', false, $values, $package['contents']);

				if($skip_product) {

					$this->diagnostic_report( sprintf( 'Product #%d is skipped. Skipping from Rate Calculation.', $values['data']->get_id() ) );

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

					$this->diagnostic_report( sprintf( 'Product #%d is pre packed. Skipping the Product from Weight Packing Algorithm', $values['data']->get_id() ) );

					continue;
				}

				$product_weight = $this->xa_get_volumatric_products_weight( $values['data'] );
				if( !empty($product_weight) ){
					$weight_pack->add_item( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product_weight, $this->weight_unit ), $values['data'], $values['quantity'] );
				}else{
					$this->debug( sprintf( __( 'FedEx - Product Weight Missing, Aborting! Product Id - %d, Product Name - %s.', 'wf-shipping-fedex' ), $values['data']->get_id(), $values['data']->get_name() ), 'error' );

					$this->diagnostic_report( sprintf( 'Product #%d is missing weight. Aborting Rate Calculation.', $values['data']->get_id() ) );

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
			$weightflag 			=	false;
			
			$packages	=	array_merge( $boxes,	$unpacked_items ); // merge items if unpacked are allowed
            $signature  = '';

			foreach($packages as $package){
				
				$insured_value 			= 0;
				$associatedLineItemId 	= array();

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

				$group = array(

					'GroupNumber'		=> $group_id,
					'GroupPackageCount'	=> 1,
					'Weight'			=> array(
						'Value'		=> $this->round_up($package['weight'],2),
						'Units'		=> $this->labelapi_weight_unit
					),
					'packed_products' 	=> $package['items']
				);

				$group['InsuredValue'] = array(

					'Amount'   => $this->convert_to_fedex_currency($insured_value),
					'Currency' => $this->wf_get_fedex_currency()
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

                $weightflag = true;
				$to_ship[] 	= $group;
				
			}
		}

		// Add pre packed item with the package
		if( !empty($pre_packed_contents) ) {

			$prepacked_requests = $this->wf_fedex_add_pre_packed_product( $pre_packed_contents,$group_id+1,$weightflag );
			$to_ship = array_merge($to_ship, $prepacked_requests);
		}

		return $to_ship;
	}

	public function residential_address_validation( $package ) {

		$residential 	= $this->residential;
		$request 		= array();
		$response 		= null;
		// Address Validation API only available for production
		if ( !empty($package['destination']['address']) && !empty($package['destination']['country']) && in_array($package['destination']['country'],$this->address_validation_contries) ) {

			// Check if address is residential or commerical
			try {

				$request['WebAuthenticationDetail'] = array(
					'UserCredential' => array(
						'Key'		=> $this->api_key,
						'Password'  => $this->api_pass,
					)
				);
				$request['ClientDetail'] = array(
					'AccountNumber' => $this->account_number,
					'MeterNumber'   => $this->meter_number,
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
							'StreetLines' 	=> array( $package['destination']['address'], $package['destination']['address_2'] ),
							'PostalCode'  	=> $package['destination']['postcode'],
							'City'			=> $package['destination']['city'], 
							'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '', 
							'CountryCode'	 => $package['destination']['country'] 
						)
					)
				);

				$client = $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/production/AddressValidationService_v' . $this->addressvalidationservice_version. '.wsdl' );
				$exception_message = null;
				if( $this->soap_method == 'nusoap' ){
					$response = $client->call( 'addressValidation', array( 'AddressValidationRequest' => $request ) );
					$response = json_decode( json_encode( $response ), false );
				}else{
					try {
						$response = $client->addressValidation( $request );
					}
					catch( Exception $e) {
						$exception_message = $e->getMessage();
					}
				}

				if ( WF_FEDEX_ADV_DEBUG_MODE == "on" ) { // Test mode is only for development purpose.
					$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
					$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
					
					$this->debug( 'FedEx ADDRESS VALIDATION REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_request ), true ) . "</pre>\n" );
					$this->debug( 'FedEx ADDRESS VALIDATION RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_response ), true ) . "</pre>\n" );

					if( $this->debug ) {

			 			$this->diagnostic_report( "----------------------------- FedEx Address Validation XML Request -----------------------------" );
			 			$this->diagnostic_report( htmlspecialchars( $xml_request ) );
			 			$this->diagnostic_report( "----------------------------- FedEx Address Validation XML Response -----------------------------" );
			 			$this->diagnostic_report( htmlspecialchars( $xml_response ) );
			 		}
					
				}
			 	$this->debug( 'FedEx ADDRESS VALIDATION REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $request, true ) . '</pre>' );
			 	if( $exception_message !== null ) {
			 		$this->debug( 'FedEx ADDRESS VALIDATION Exception: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $exception_message, true ) . '</pre>' );

			 		if( $this->debug ) {

			 			$this->diagnostic_report( "----------------------------- FedEx Address Validation Exception -----------------------------" );
			 			$this->diagnostic_report( print_r( $exception_message, 1 ) );
			 		}

			 		return;
			 	}
			 	$this->debug( 'FedEx ADDRESS VALIDATION RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $response, true ) . '</pre>' );

			 	if( $this->debug ) {

			 		$this->diagnostic_report( "----------------------------- FedEx Address Validation Request -----------------------------" );
			 		$this->diagnostic_report( print_r( $request, 1 ) );
			 		$this->diagnostic_report( "----------------------------- FedEx Address Validation Response -----------------------------" );
			 		$this->diagnostic_report( print_r( $response, 1 ) );
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

					if( isset( $addressResult->ProposedAddressDetails->Address) )
					{
						$address_data 		= $addressResult->ProposedAddressDetails->Address;
						$suggested_adress 	= array();

						if( !is_admin( ) ) {
							
							$suggested_adress['suggested_street'] 		= isset($address_data->StreetLines) ? $address_data->StreetLines : '';
							$suggested_adress['suggested_city'] 		= isset($address_data->City) ? $address_data->City : '';
							$suggested_adress['suggested_state'] 		= isset($address_data->StateOrProvinceCode) ? $address_data->StateOrProvinceCode : '';
							$suggested_adress['suggested_country'] 		= isset($address_data->CountryCode) ? $address_data->CountryCode : '';
							$suggested_adress['suggested_postcode'] 	= isset($address_data->PostalCode) ? $address_data->PostalCode : '';

							WC()->session->set( 'ph_address_validation', $suggested_adress );
						}
						
					}
				}

			} catch (Exception $e) {
				$this->debug( 'FedEx ADDRESS VALIDATION: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' .'An Unexpected error occured while calling API.' . '</pre>' );
				$this->debug( 'FedEx ADDRESS VALIDATION Error Code:'.$e->getMessage().'</pre>' );
			}
		}

		if(isset($_POST['post_data'])){
			parse_str($_POST['post_data'],$str);
			if(isset($str['eha_is_residential'])){
				$residential = true;
			}
		}

		$this->residential = apply_filters( 'woocommerce_fedex_address_type', $residential, $package, $response, $request );

		if ( $this->residential == false ) {
			$this->debug( __( 'Business Address', 'wf-shipping-fedex' ) );
		}
	}

	private function get_origin_address($package){
		$from_address = array(
			'postcode' 	=> $this->origin,
			'country' 	=> $this->origin_country,
		);
		//Filter for origin address switcher plugin.
		$from_address =  apply_filters( 'wf_filter_label_from_address', $from_address , $package );
		return array(
			'PostalCode'	=>	$from_address['postcode'],
			'CountryCode'	=>	$from_address['country']
		);
	}

	public function get_fedex_api_request( $package ) {

		$request 				= array();
		self::$current_wp_time 	= date_create(current_time('c',false));
		$package_origin			= $this->get_origin_address($package);
		
		//If vendor country set, then use vendor address
		if(isset($package['origin'])){
			if(isset($package['origin']['country'])){
				$package_origin['PostalCode']	=	$package['origin']['postcode'];
				$package_origin['CountryCode']	=	$package['origin']['country'];
			}
		}
		
		// Prepare Shipping Request for FedEx
		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'		=> $this->api_key,
				'Password'  => $this->api_pass,
			)
		);
		$request['ClientDetail'] = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number,
		);
		$request['TransactionDetail'] = array(
			'CustomerTransactionId'	 => ' *** WooCommerce Rate Request ***'
		);
		$request['Version'] = array(
			'ServiceId'			  => 'crs',
			'Major'				  => $this->rateservice_version,
			'Intermediate'		   => '0',
			'Minor'				  => '0'
		);
		$request['ReturnTransitAndCommit'] = true;
		$request['RequestedShipment']['EditRequestType'] = true;
		$request['RequestedShipment']['PreferredCurrency'] = $this->wf_get_fedex_currency();
		$request['RequestedShipment']['DropoffType']	   = $this->dropoff_type;
		$rate_request_time = clone self::$current_wp_time;

		if( ! empty($this->ship_time_adjustment) ) {
			$rate_request_time->modify("+$this->ship_time_adjustment days");
		}

		$request['RequestedShipment']['ShipTimestamp']	 = $rate_request_time->format('c');

		// Check Current Time exceeds Store Cut-off Time, if yes request on next day
		if( !empty($this->cut_off_time) && $this->cut_off_time != '24:00') {

			$this->current_wp_time_hour_minute = current_time('H:i');

			if( $this->current_wp_time_hour_minute > $this->cut_off_time ) {

				$date = date( 'c', strtotime( '+1 days', strtotime( $request['RequestedShipment']['ShipTimestamp'] ) ) );

				$request['RequestedShipment']['ShipTimestamp'] = $date;
			}
		}

		$date 			= new DateTime( $request['RequestedShipment']['ShipTimestamp'] );
		$shippingDay 	= $date->format('D');
		
		if( ! in_array($shippingDay, $this->working_days) ) {

			$shipTimeStamp 	= $this->get_next_working_day( $shippingDay, $request['RequestedShipment']['ShipTimestamp'] );
			
			$request['RequestedShipment']['ShipTimestamp'] = $shipTimeStamp;
		}

		// Make Saturday Delivery Rate Request only on Friday
		if( date('l',strtotime($request['RequestedShipment']['ShipTimestamp'])) != 'Friday' ) {

			$this->saturday_delivery = false;
		}
		
		$request['RequestedShipment']['PackagingType']	 = $this->packaging_type;
		$request['RequestedShipment']['Shipper']		   = array(
			'Address'			   => array(
				'PostalCode'			  => $package_origin['PostalCode'],
				'CountryCode'			 => $package_origin['CountryCode'],
			)
		);
		$request['RequestedShipment']['ShippingChargesPayment'] = array(
			'PaymentType' => 'SENDER',
			'Payor' => array(
				'ResponsibleParty' => array(
					'AccountNumber'		   => $this->account_number,
				)
			)
		);
		$request['RequestedShipment']['RateRequestTypes'] = $this->request_type === 'LIST' ? 'LIST' : 'NONE';

		if( $this->shipping_charge === 'DUTIES_AND_TAXES' && $this->origin_country !=  $package['destination']['country'] )
		{
			$request['RequestedShipment']['EdtRequestType'] = 'ALL';
		}

		$request['RequestedShipment']['Recipient'] = array(
			'Address' => array(
				'Residential'		 => $this->residential,
				'PostalCode'		  => str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
				'City'				=> strtoupper( $package['destination']['city'] ),
				'StateOrProvinceCode' => strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
				'CountryCode'		 => $package['destination']['country']
			)
		);

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

	public function get_fedex_requests( $fedex_packages, $package, $request_type = '' ) {
		
		$requests 				= array();
		$this->packaging_type 	= empty($fedex_packages['package_id']) ? 'YOUR_PACKAGING' : $fedex_packages['package_id'];
		$country_obect 			= new WC_Countries();

		// All reguests for this package get this data
		$package_request = $this->get_fedex_api_request( $package );

		if ( $fedex_packages ) {

			// Fedex Supports a Max of 99 per request
			$parcel_chunks 		= array_chunk( $fedex_packages, 99 );
			$num_of_packages  	= isset($_GET["num_of_packages"]) ? json_decode(stripslashes(html_entity_decode($_GET["num_of_packages"]))) : 0;

			foreach ( $parcel_chunks as $parcels ) {

				$request			= $package_request;
				$total_value		= 0;
				$total_packages 	= 0;
				$total_weight   	= 0;
				$commodoties		= array();
				$freight_class  	= '';
				$freight_line_items = array();
				$line_item_id 		= 1;

				// Store parcels as line items
				$request['RequestedShipment']['RequestedPackageLineItems'] = array();

				foreach ( $parcels as $key => $parcel ) {

					$no_of_packages = isset($num_of_packages) && !empty($num_of_packages) ? $num_of_packages[$key] : 1;
					$order_no 		= isset($_GET['oid'])? $_GET['oid']:'';

					if ( !empty( $order_no )) {
						
						update_post_meta( $order_no, 'ph_get_no_of_packages'.$parcel['GroupNumber'], $no_of_packages );
					}

					for( $i=0; $i<$no_of_packages; $i++ ) { 

						if ( isset( $parcel['AssociatedFreightLineItems'] ) && !empty( $parcel['AssociatedFreightLineItems'] ) && is_array( $parcel['AssociatedFreightLineItems'] ) ) {

							foreach ( $parcel['AssociatedFreightLineItems'] as $associatedId => $values ) {

								if( isset( $values ) && !empty( $values ) && is_array( $values ) ){

									foreach ( $values as $id => $value ) {

										$parcel['AssociatedFreightLineItems'][$associatedId][$id] = $value + $i;
									}
								}
							}
						}

						$request['RequestedShipment']['PackagingType']  = empty($parcel['package_id']) ? 'YOUR_PACKAGING' : $parcel['package_id'];

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

						$is_dry_ice_shipment = false;
						$single_package_weight = $parcel['Weight']['Value'];

						$parcel_request = $parcel;
						$total_value	+= $parcel['InsuredValue']['Amount'] * $parcel['GroupPackageCount'];
						$total_packages += $parcel['GroupPackageCount'];
						$total_weight   += $parcel['Weight']['Value'] * $parcel['GroupPackageCount'];

						if ( 'freight' === $request_type ) {

							if ( isset($parcel_request['packed_products']) ) {

								$executed_prouducts 	= array();

								foreach ( $parcel_request['packed_products'] as $product ) {

									for ($loop = 0; $loop < $parcel_request['GroupPackageCount']; $loop++) {

										$line_items 	= array();
										$freight_class 	= $this->get_freight_class( $product );
										$product_id 	= $product->get_id();

										$freight_id 	= $line_item_id;
										$flag 			= true;

										// When a Package contains Multiple Quantity of Same Product, add the Weight to the Same Line Items
										if( isset($executed_prouducts[$product_id]) && !empty($executed_prouducts[$product_id]) ) {

											$freight_id 	= $executed_prouducts[$product_id];
											$flag 			= false;

											$freight_line_items[$product_id.'_'.$freight_id]['Weight']['Value'] += $this->round_up( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product->get_weight(), $this->weight_unit ), 2 );
											continue;
										}

										$freight_class = $freight_class ? $freight_class : $this->freight_class;
										$freight_class = $freight_class < 100 ?  '0' . $freight_class : $freight_class;
										$freight_class = 'CLASS_' . str_replace( '.', '_', $freight_class );

										$line_items = array(

											'Id'				=> $freight_id,
											'FreightClass' 		=> $freight_class,
											'Packaging'			=> 'SKID',
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

										$freight_line_items[$product_id.'_'.$freight_id] = $line_items;

										// Increment Line Item Id only when new Product is added
										if ( $flag || !isset($executed_prouducts[$product_id]) ) {

											$executed_prouducts[$product_id] 	= $line_item_id;
											$line_item_id++;
										}
									}
								}

								// Use only Array Values
								$freight_line_items = array_values($freight_line_items);
							}

						} else {

							// Work out the commodoties for CA shipments
							if ( $parcel_request['packed_products'] ) {

								$dry_ice_total_weight 			= 0;
								$contain_non_standard_product 	= false;

								foreach ( $parcel_request['packed_products'] as $product ) {

									$product 		= $this->wf_load_product( $product );
									$product_id 	= wp_get_post_parent_id($product->get_id());

									if(empty($product_id)) {

										$product_id=$product->get_id();
									}

									$is_dry_ice_product = get_post_meta($product_id , '_wf_dry_ice', 1);

									if( $is_dry_ice_product=='yes' ){
										$is_dry_ice_shipment = true;
										$meta_exists=metadata_exists('post', $product_id, '_wf_dry_ice_weight');
										$dry_ice_weight=($meta_exists)?get_post_meta($product_id , '_wf_dry_ice_weight', 1):$product->get_weight();           // for backward compactibility ( added on 22/11/2018) 
										$dry_ice_total_weight += Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $dry_ice_weight, 'kg' )*$parcel['GroupPackageCount']; //Fedex support dry ice weight in KG only
									}

									if ( isset( $commodoties[ $product->id ] ) ) {
										$commodoties[ $product->get_id() ]['Quantity'] ++;
										$commodoties[ $product->get_id() ]['CustomsValue']['Amount'] += $this->convert_to_fedex_currency($this->wf_get_insurance_amount($product));
										continue;
									}
									$product_name 	= html_entity_decode( $product->get_title() );
									$commodity_desc = html_entity_decode( get_post_meta( $product_id , '_ph_commodity_description', 1) );

									$commodity_desc = !empty($commodity_desc) ? $commodity_desc : $product_name;
									//Remove special-characters from Product name and description
									$remove_special_char =  ( isset( $this->settings['remove_special_char_product'] ) && $this->settings['remove_special_char_product'] == 'yes' && !empty( $this->settings['remove_special_char_product'] ) ) ? true : false;

									if( $remove_special_char == true ){
										$product_name 	= preg_replace('/[^A-Za-z0-9-() ]/', '', $product_name);
										$commodity_desc = preg_replace('/[^A-Za-z0-9-() ]/', '', $commodity_desc);
									}

									$commodity_desc = ( strlen( $commodity_desc ) >= 450 ) ? substr( $commodity_desc, 0, 445 ).'...' : $commodity_desc;

									$commodoties[ $product->get_id() ] = array(
										'Name'				 => $product_name,
										'NumberOfPieces'	   => 1,
										'Description'		  => $commodity_desc,
										'CountryOfManufacture' => ( $country = get_post_meta( $product_id, '_wf_manufacture_country', true ) ) ? $country : $this->origin_country,
										'Weight'			   => array(
											'Units'			=> $this->labelapi_weight_unit,
											'Value'			=> round( Ph_Fedex_Woocommerce_Shipping_Common::ph_get_converted_weight( $product->get_weight(), $this->weight_unit ), 2 ),
										),
										'Quantity'			 => $parcel['GroupPackageCount'],
										'UnitPrice'			=> array(
											'Amount'		   => $this->convert_to_fedex_currency($product->get_price()),
											'Currency'		 => $this->wf_get_fedex_currency()
										),
										'CustomsValue'		 => array(
											'Amount'		   => $this->convert_to_fedex_currency($this->wf_get_insurance_amount($product)),
											'Currency'		 => $this->wf_get_fedex_currency()
										)
									);


									$product_id = $product->get_type() == 'simple' ? $product->get_id() : $product->get_parent_id();

									$hst = get_post_meta( $product_id, '_wf_hs_code', 1);

									if( ! empty($hst) ) {

										$commodoties[ $product->get_id() ]['HarmonizedCode'] = $hst;

									} else if ( !empty($this->global_hs_code) ) {

										$commodoties[ $product->get_id() ]['HarmonizedCode'] = $this->global_hs_code;
									}
								}

								$commodoties = apply_filters( 'ph_fedex_commodities', $commodoties, $request, $fedex_packages );
							}

							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = array();

							// Is this valid for a ONE rate? Smart post does not support it
							if ( $this->fedex_one_rate && '' === $request_type && isset($parcel_request['package_id']) && in_array( $parcel_request['package_id'], $this->fedex_one_rate_package_ids ) && count($parcels) == 1 ) {
								$this->packaging_type = $this->xa_get_countrywise_packagin_type( $parcel_request['package_id'], $this->origin_country );
								$request['RequestedShipment']['PackagingType'] = $this->packaging_type;

								if( in_array($this->origin_country, $this->fedexBoxCountries) && in_array($request['RequestedShipment']['PackagingType'], $this->fedexBox) ) {
									$request['RequestedShipment']['PackagingType'] = 'FEDEX_BOX';
								}

								if('US' === $package['destination']['country'] && 'US' === $this->origin_country){
									$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'FEDEX_ONE_RATE';
								}
							}

							if( $this->fedex_cod_rate && $this->origin_country == $package['destination']['country'] )
							{
								$cart_total = isset($package['cart_subtotal']) ? $package['cart_subtotal'] : 0;

								$address = array(
									'Address' => array(
										'StreetLines' 			=> isset( $package['destination']['address1'] ) ? $package['destination']['address1'] : (isset( $package['destination']['address'] ) ? $package['destination']['address'] : '' ),
										'PostalCode' 			=> str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
										'City' 					=> isset($package['destination']['city']) ? strtoupper( $package['destination']['city'] ) : ' ',
										'StateOrProvinceCode' 	=> strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
										'CountryCode' 			=> $package['destination']['country'],
									),
								);

								$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'COD';

								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CodCollectionAmount'] = array(
									'Currency' 	=> $this->wf_get_fedex_currency(),
									'Amount' 	=> $cart_total ? $this->convert_to_fedex_currency($cart_total) : $total_value,
								);

								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['CollectionType'] = 'ANY';

								$request['RequestedShipment']['SpecialServicesRequested']['CodDetail']['FinancialInstitutionContactAndAddress'] = $address;

							}
						}	

						if( $request_type == 'saturday_delivery' ) {

							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'SATURDAY_DELIVERY';

						}				

						// Remove temp elements
						unset( $parcel_request['freight_class'] );
						unset( $parcel_request['packed_products'] );
						unset( $parcel_request['package_id'] );

						if ( ! $this->insure_contents || 'smartpost' === $request_type || empty($parcel['InsuredValue']['Amount'])) {
							unset( $parcel_request['InsuredValue'] );
						}

						$parcel_request = array_merge( array( 'SequenceNumber' => $key + 1 ), $parcel_request );

						$SpecialServices = array();

						//PDS-179
						$order_ID = isset($_GET['oid'])? $_GET['oid']:'';

						if( ( isset($parcel_request['signature_option']) && !empty($parcel_request['signature_option'])) || ( isset( $_GET['signature_option'] ) ) ){
							$signature_option = isset( $_GET['signature_option'] ) ? array_search($_GET['signature_option'], $this->prioritizedSignatureOption) : $parcel_request['signature_option'];

							update_post_meta( $order_ID, 'ph_fedex_signature_option_meta', $signature_option );
						}
						else{
							$signature_option = isset($this->signature_option) && !empty($this->signature_option) ? $this->signature_option : 0;
						}

						if( isset($parcel_request['signature_option']) ){

							unset( $parcel_request['signature_option'] );
						}

						$signature_option = isset($this->prioritizedSignatureOption[$signature_option]) && !empty($this->prioritizedSignatureOption[$signature_option]) ? $this->prioritizedSignatureOption[$signature_option] : '';

						if(isset($signature_option) && !empty($signature_option)){
							$signature = array();
							$SpecialServices['SpecialServiceTypes'][] = 'SIGNATURE_OPTION';
							$SpecialServices['SignatureOptionDetail'] = array('OptionType'=>$signature_option);
						}

						if( $this->is_dry_ice_enabled && $is_dry_ice_shipment ){
							$SpecialServices['SpecialServiceTypes'][] = 'DRY_ICE';
							$SpecialServices['DryIceWeight'] = array('Units' => 'KG','Value' => round($dry_ice_total_weight,2) );
						}

						$non_standard_product = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_non_standard_product' );
						if( !empty($non_standard_product) ){
							$SpecialServices['SpecialServiceTypes'][] = 'NON_STANDARD_CONTAINER';
						}

						$dangerous_goods = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_dangerous_goods' );
						$hazmat_products = $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_hazmat_products' );

						if( !empty($dangerous_goods) && empty($hazmat_products) ){
							$dangerous_goods_regulation	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_dg_regulations');
							$dangerous_goods_accessibility	= $this->xa_get_custom_product_option_details( $parcel['packed_products'], '_wf_fedex_dg_accessibility');
							$SpecialServices['SpecialServiceTypes'][]	= 'DANGEROUS_GOODS';
							$SpecialServices['DangerousGoodsDetail']	= array(
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

							$SpecialServices['SpecialServiceTypes'][]	= 'DANGEROUS_GOODS';
							$SpecialServices['DangerousGoodsDetail']['Options']	= 'HAZARDOUS_MATERIALS';

							$i = 0;

							foreach ( $hazmat_products as $product_key => $value) {

								$product_weight = 0;

								foreach ($parcel['packed_products'] as $key => $product) {
									if( $product_key == $product->id || $product_key == $product->variation_id )
									{
										$product_weight += $product->get_weight();
									}
								}

								$SpecialServices['DangerousGoodsDetail']['Containers'][$i] = array(

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

								if( !empty($hazmat_subsidairy_class[$product_key]))
								{
									$subsidairy_class 	= $hazmat_subsidairy_class[$product_key];
									$sub_classes 		= explode(',', $subsidairy_class);

									if( is_array($sub_classes) && !empty($sub_classes) )
									{
										foreach ($sub_classes as $key => $sub_class) {
											$SpecialServices['DangerousGoodsDetail']['Containers'][$i]['HazardousCommodities']['Description']['SubsidiaryClasses'][] = trim($sub_class," ");
										}
									}
								}

								$i++;
							}	
						}

						if( $this->fedex_cod_rate && $this->origin_country == $package['destination']['country'] )
						{
							$package_total = 0;

							if( isset($parcel['packed_products']) && !empty($parcel['packed_products']) )
							{

								foreach ($parcel['packed_products'] as $product)
								{
									if( is_object($product) && isset($product->obj) && is_a($product->obj, 'WC_Product') )
									{
										$package_total += $this->convert_to_fedex_currency($product->get_price());
									}
								}
							}

							$address = array(
								'Address' => array(
									'StreetLines' 			=> isset( $package['destination']['address1'] ) ? $package['destination']['address1'] : (isset( $package['destination']['address'] ) ? $package['destination']['address'] : '' ),
									'PostalCode' 			=> str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) ),
									'City' 					=> isset($package['destination']['city']) ? strtoupper( $package['destination']['city'] ) : ' ',
									'StateOrProvinceCode' 	=> strlen( $package['destination']['state'] ) == 2 ? strtoupper( $package['destination']['state'] ) : '',
									'CountryCode' 			=> $package['destination']['country'],
								),
							);

							$SpecialServices['SpecialServiceTypes'][]	= 'COD';

							$SpecialServices['CodDetail']['CodCollectionAmount'] = array(
								'Currency' 	=> $this->wf_get_fedex_currency(),
								'Amount' 	=> !empty( $package_total ) ? $package_total : $total_value,
							);

							$SpecialServices['CodDetail']['CollectionType'] = 'ANY';

							$SpecialServices['CodDetail']['FinancialInstitutionContactAndAddress'] = $address;

						}

						if( !empty($SpecialServices) ){
							$parcel_request = array_merge( array( 'SpecialServicesRequested' => $SpecialServices), $parcel_request );
						}

						//Priority boxed no need dimensions
						if( $this->packaging_type != 'YOUR_PACKAGING' ){
							unset( $parcel_request['Dimensions'] );
						}
						$request['RequestedShipment']['RequestedPackageLineItems'][] = $parcel_request;
					}
				}

				// Size
				$request['RequestedShipment']['PackageCount'] = $total_packages;

				$indicia = $this->indicia;
				
				if($indicia == 'AUTOMATIC' && $single_package_weight >= 1)
					$indicia = 'PARCEL_SELECT';
				elseif($indicia == 'AUTOMATIC' && $single_package_weight < 1)
					$indicia = 'PRESORTED_STANDARD';				
				
				// Smart post
				if ( 'smartpost' === $request_type ) {
					$request['RequestedShipment']['SmartPostDetail'] = array(
						'Indicia'			  => $indicia,
						'HubId'				=> $this->smartpost_hub,
						'AncillaryEndorsement' => 'ADDRESS_CORRECTION',
						'SpecialServices'	  => ''
					);
					$request['RequestedShipment']['ServiceType'] = 'SMART_POST';
					
					$this->debug( __( 'Only $100 amount will be insured for smartpost.', 'wf-shipping-fedex' ) );

					$this->diagnostic_report( 'Only $100 amount will be insured for smartpost.' );

				} elseif ( $this->insure_contents && !empty($total_value) ) {
					$request['RequestedShipment']['TotalInsuredValue'] = array(
						'Amount'   => $total_value,
						'Currency' => $this->wf_get_fedex_currency()
					);
				}

				if ( 'freight' === $request_type ) {

					$request['RequestedShipment']['Shipper'] = array(

						'Address'			=> array(
							'StreetLines'			=> array( strtoupper( $this->frt_shipper_street ), strtoupper( $this->freight_shipper_street_2 ) ),
							'City'					=> strtoupper( $this->freight_shipper_city ),
							'StateOrProvinceCode'	=> strtoupper( $this->origin_state ),
							'PostalCode'			=> strtoupper( $this->origin ),
							'CountryCode'			=> strtoupper( $this->origin_country ),
							'Residential'			=> $this->freight_shipper_residential
						)
					);

					$request['CarrierCodes'] = 'FXFR';

					$request['RequestedShipment']['FreightShipmentDetail'] = array(

						'FedExFreightAccountNumber'				=> strtoupper( $this->freight_number ),
						'FedExFreightBillingContactAndAddress'	=> array(
							'Address'				=> array(
								'StreetLines'			=> array( strtoupper( $this->freight_bill_street ), strtoupper( $this->freight_billing_street_2 ) ),
								'City'					=> strtoupper( $this->freight_billing_city ),
								'StateOrProvinceCode'	=> strtoupper( $this->freight_billing_state ),
								'PostalCode'			=> strtoupper( $this->freight_billing_postcode ),
								'CountryCode'			=> strtoupper( $this->freight_billing_country )
							)
						),
						'Role'							=> 'SHIPPER',
					);

					foreach ($request['RequestedShipment']['RequestedPackageLineItems'] as $key => $item) {

						if( isset($item['Dimensions']) && max( array_map( array($this,'dimensions_in_inches'), array($item['Dimensions']['Length'], $item['Dimensions']['Width'], $item['Dimensions']['Height']) ) )  >= 96 ) {
							
							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'OVER_LENGTH';

							$request['RequestedShipment']['FreightShipmentDetail']['SpecialServicePayments'] = array(

								'SpecialService' => 'OVER_LENGTH',
								'PaymentType'	 => 'SHIPPER',
							);

							break;
						}
					}

					$request['RequestedShipment']['FreightShipmentDetail']['LineItems'] = array();
					$request['RequestedShipment']['FreightShipmentDetail']['LineItems'] = $freight_line_items;

					$request['RequestedShipment']['ShippingChargesPayment'] = array(

						'PaymentType'	=> 'SENDER',
						'Payor'			=> array(

							'ResponsibleParty'	=> array(

								'AccountNumber'		=> strtoupper( $this->freight_number ),
								'CountryCode'		=> $this->origin_country,
							)
						)
					);

					// Lift Gate Delivery in freight
					if( isset($_POST['post_data']) )
					{
						parse_str($_POST['post_data'], $data );

						if(isset($data['xa_fedex_lift_gate_for_delivery']) && $data['xa_fedex_lift_gate_for_delivery']==1)
						{
							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_DELIVERY';
						}
					}
					elseif( isset($_POST['xa_fedex_lift_gate_for_delivery']) && $_POST['xa_fedex_lift_gate_for_delivery']==1)
					{
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_DELIVERY';
					}
					// Lift Gate Pickup in freight request
					if( ! empty($this->settings['lift_gate_for_pickup']) && $this->settings['lift_gate_for_pickup'] == 'yes' ) {
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'LIFTGATE_PICKUP';
					}
					// Inside delivery in freight
					if( isset($_POST['post_data']) )
					{
						parse_str($_POST['post_data'], $data );

						if(isset($data['xa_fedex_inside_delivery']) && $data['xa_fedex_inside_delivery']==1)
						{
							$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'INSIDE_DELIVERY';
						}
					}
					elseif( isset($_POST['xa_fedex_inside_delivery']) && $_POST['xa_fedex_inside_delivery']==1 )
					{
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'INSIDE_DELIVERY';
					}
					// Inside Pickup freight request
					if( ! empty($this->settings['inside_pickup']) && $this->settings['inside_pickup'] == 'yes' ) {
						$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'INSIDE_PICKUP';
					}

				} else {
					$core_countries = ['US'];

					if ( $this->origin_country !== $package['destination']['country'] || !in_array( $this->origin_country,$core_countries ) ) {

						// Changes Made to avoid General Failure as per FedEx Response - 41963
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment'] = array(
							'PaymentType' => 'SENDER',
						);

						// Changes Made to avoid General Failure as per FedEx Response - 41963
						// if($this->customs_duties_payer!='RECIPIENT'){
						$request['RequestedShipment']['CustomsClearanceDetail']['DutiesPayment']['Payor']['ResponsibleParty'] = array(
							'AccountNumber'		=> strtoupper( $this->account_number ),
							'CountryCode'		=> $this->origin_country,
						);
						// }
						
						$request['RequestedShipment']['CustomsClearanceDetail']['Commodities'] 	= array_values( $commodoties );
						$request['RequestedShipment']['CustomsClearanceDetail']['CustomsValue'] = array(
							'Currency'	=>  $this->wf_get_fedex_currency(),
							'Amount'	=>  $total_value
						);

						if( !in_array( $this->origin_country, $core_countries ) ) {
							$request['RequestedShipment']['CustomsClearanceDetail']['CommercialInvoice'] = array(
								'Purpose' => 'SOLD'
							);
						}
					}
				}
				// Add request
				$requests[] = apply_filters( 'xa_fedex_rate_request', $request, $parcels );
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
	* return details of FedEx custome field in product page (Eg: Dangerous Goods).
	* Return array of product ids and product option value
	*/
	private function xa_get_custom_product_option_details( $packed_products, $option_mame ){
		$products_with_value = array();
		foreach ( $packed_products as $product ) {
			
			$parent_id 	= ( WC()->version > '2.7' ) ? $product->get_parent_id() : ( isset($product->parent->id) ? $product->parent->id : 0 );
			$option 	= get_post_meta( $product->get_id() , $option_mame, 1 );

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
	
	/**
	* @param $product wf_product object
	* @return int Custom Declared Value (Fedex) | Product Selling Price <br />The Insurance amount for the product , Custom Declared Value (Fedex) can be set in individual product page.
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
		return ( ! empty( $insured_price ) ? $insured_price : $product->get_price() );	
	}

	private function dimensions_in_inches($value){
		if( !is_numeric($value) )
			return $value;
		if( $this->dimension_unit == 'in' )
			return $value;
		return $value * 0.393701;
	}

	public function calculate_shipping( $package = array() ) {
		// Clear rates
		$this->found_rates = array();
		// Debugging
		$this->debug( __( 'FEDEX debug mode is on - to hide these messages, turn debug mode off in the settings.', 'wf-shipping-fedex' ) );

		// See if address is residential
		if( $this->production ) {
			$this->residential_address_validation( $package );
		}
		

		$packages = apply_filters('wf_filter_package_address', array($package) , $this->ship_from_address);
		
		// Get requests
		$fedex_packages	=	array();
		$fedex_requests	=	array();
		
		foreach($packages as $package){
			
			$package	= apply_filters( 'wf_customize_package_on_cart_and_checkout', $package );	// Customize the packages if cart contains bundled products

			// To pass the product info with rates meta data
			foreach( $package['contents'] as $product ) {
				$product_id = ! empty($product['variation_id']) ? $product['variation_id'] : $product['product_id'];
				$this->current_package_items_and_quantity[$product_id] = $product['quantity'];
			}
			$this->vendorId = ! empty($package['vendorID']) ? $package['vendorID'] : null ;

			$fedex_packs	= $this->get_fedex_packages( $package );
			$fedex_reqs	= $this->get_fedex_requests( $fedex_packs, $package );
			
			if(is_array($fedex_packs)){
				$fedex_packages	=	array_merge($fedex_packages,	$fedex_packs);
			}
			if(is_array($fedex_reqs)){
				$fedex_requests	=	array_merge($fedex_requests,	$fedex_reqs);
			}
		}

		$fedex_requests 		= apply_filters('wf_fedex_calculate_shipping_request',$fedex_requests,$fedex_packages);

		if ( $fedex_requests ) {
			$this->run_package_request( $fedex_requests );
		}

		if ( ! empty( $this->custom_services['SMART_POST']['enabled'] ) && ! empty( $this->smartpost_hub ) && $package['destination']['country'] == 'US' && ( $smartpost_requests = $this->get_fedex_requests( $fedex_packages, $package, 'smartpost' ) ) ) {
			$this->run_package_request( $smartpost_requests );
		}

		if ( $this->freight_enabled && ( $freight_requests = $this->get_fedex_requests( $fedex_packages, $package, 'freight' ) ) ) {
			$this->run_package_request( $freight_requests );
		}

		// Only on Fridays this rate requests will hit
		if( $this->saturday_delivery && ( $satdelivery_requests = $this->get_fedex_requests( $fedex_packages, $package, 'saturday_delivery' ) ) ) {

			$this->satday_rates 	= true;
			$this->run_package_request( $satdelivery_requests );
		}

		// Ensure rates were found for all packages
		$packages_to_quote_count = sizeof( $fedex_requests );

		if ( $this->found_rates ) {
			foreach ( $this->found_rates as $key => $value ) {
				if ( $value['packages'] < $packages_to_quote_count ) {
					unset( $this->found_rates[ $key ] );
				}
			}
		}

		$this->add_found_rates();		
	}

	public function wf_add_delivery_time( $label, $method ) {
		if(!$this->delivery_time) {
			return $label;
		}

		//Older versoin of WC is not supporting get_meta_data() on method.
		if( !is_object($method) || !method_exists($method,'get_meta_data') ){
			return $label;
		}

		$est_delivery = $method->get_meta_data();
		if( !empty($est_delivery['fedex_delivery_time']) && strpos( $label, 'Est delivery' ) == false ){
			$est_delivery_html = "<br /><small>".__('Est delivery: ', 'wf-shipping-fedex'). $est_delivery['fedex_delivery_time'].'</small>';
			$est_delivery_html = apply_filters( 'wf_fedex_estimated_delivery', $est_delivery_html, $est_delivery );
			$label .= $est_delivery_html;
		}
		return $label;
	}

	public function run_package_request( $requests ) {
		try {
			foreach ( $requests as $key => $request ) {
				$this->process_result( $this->get_result( $request ) );
			}
		} catch ( Exception $e ) {
			$this->debug( print_r( $e, true ), 'error' );
			return false;
		}
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

	private function get_result( $request ) {

		$result = null;
		$client = $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $this->production ? 'production' : 'test' ) . '/RateService_v' . $this->rateservice_version. '.wsdl' );
		
		$exception_message = null;
		if( $this->soap_method == 'nusoap' ){
			$result = $client->call( 'getRates', array( 'RateRequest' => $request ) );
			$result = json_decode( json_encode( $result ), false );
		}
		else{
			try {
				$result = $client->getRates( $request );
			}
			catch( Exception $e ) {
				$exception_message = $e->getMessage();
			}
		}

		if( $this->debug ) {
			$debug_request_to_display = $this->create_debug_request_or_response( $request, 'rate_request' );
			$this->debug( 'FedEx REQUEST: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $debug_request_to_display, true ) . '</pre>' );
			if( $exception_message !== null ) {
				$this->debug( 'FedEx RATE EXCEPTION: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $exception_message, true ) . '</pre>' );

				$this->diagnostic_report( '------------------------------------ Fedex Rate Exception ------------------------------------' );
				$this->diagnostic_report( print_r( $exception_message, true ) );

				return;
			}
			$debug_response_to_display = $this->create_debug_request_or_response( $result, 'rate_response' );
			$this->debug( 'FedEx RESPONSE: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $debug_response_to_display, true ) . '</pre>' );

			$this->diagnostic_report( '------------------------------------ Fedex Rate Request ------------------------------------' );
			$this->diagnostic_report( print_r( $debug_request_to_display, true ) );
			$this->diagnostic_report( '------------------------------------ Fedex Rate Response ------------------------------------' );
			$this->diagnostic_report( print_r( $debug_response_to_display, true ) );

			try{
				$xml_request 	= $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
				$xml_response 	= $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;			
				$this->debug( 'FedEx  REQUEST in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_request ), true ) . "</pre>\n" );
				$this->debug( 'FedEx RESPONSE in XML Format: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_response ), true ) . "</pre>\n" );

				$this->diagnostic_report( '------------------------------------ Fedex Rate Request in XML ------------------------------------' );
				$this->diagnostic_report( htmlspecialchars( $xml_request ) );
				$this->diagnostic_report( '------------------------------------ Fedex Rate Response in XML ------------------------------------' );
				$this->diagnostic_report( htmlspecialchars( $xml_response ) );

			}
			catch(Exception $e){
				$this->debug( 'Exception while getting FedEx Request or Response: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( $e->getMessage(), true ) . '</pre>' );
			}
		}

		wc_enqueue_js( "
			jQuery('a.debug_reveal').on('click', function(){
				jQuery(this).closest('div').find('.debug_info').slideDown();
				jQuery(this).remove();
				return false;
			});
			jQuery('pre.debug_info').hide();
		" );

		return $result;
	}

	/**
	 * Get the request or response to display in debug mode.
	 * @param $data mixed Fedex Request or Response.
	 * @param $type string Type of FedEx request like FedEx Rate request (rate_request).
	 * @return mixed Debug data to display.
	 */
	private function create_debug_request_or_response( $data, $type='' ){
		$debug_data = null;
		switch( $type ) {
			case 'rate_request' : 
									$debug_data = array(
										'From Address'		=>	$data['RequestedShipment']['Shipper']['Address'],
										'To Address'		=>	$data['RequestedShipment']['Recipient']['Address'],
									);
									foreach( $data['RequestedShipment']['RequestedPackageLineItems'] as $requested_package_line_item ) {
										if( ! empty($requested_package_line_item['Dimensions']) ) {
											$debug_data['Packages'][] = array(
												'Weight'		=>	$requested_package_line_item['Weight'],
												'Dimensions'	=>	$requested_package_line_item['Dimensions'],
											);
										}
										else{
											$debug_data['Packages'][] = array(
												'Weight'		=>	$requested_package_line_item['Weight'],
											);
										}
									}
									break;
			case 'rate_response' :
									if( ! empty($data->HighestSeverity) && $data->HighestSeverity == 'ERROR' || $data->HighestSeverity == 'FAILURE' ) {
										$debug_data = $data->Notifications;
									}
									elseif( ! empty($data->RateReplyDetails) ) {
										if( is_object($data->RateReplyDetails) )	$data->RateReplyDetails = array($data->RateReplyDetails);
										foreach( $data->RateReplyDetails as $quote ) {
											foreach( $quote->RatedShipmentDetails as $rate_details ) {
												if( $this->request_type == "LIST" ) {
													if( strstr( $rate_details->ShipmentRateDetail->RateType, 'PAYOR_LIST' ) && empty($debug_data[$quote->ServiceType]['ListRates']) ) {
														$debug_data[$quote->ServiceType]['ListRates'] = $rate_details->ShipmentRateDetail->TotalNetCharge;
													}
													elseif( empty($debug_data[$quote->ServiceType]['AccountRates']) ){
														$debug_data[$quote->ServiceType]['AccountRates'] = $rate_details->ShipmentRateDetail->TotalNetCharge;
													}
												}
												else{
													if( ! empty($rate_details->TotalNetCharge) ) {
														$debug_data[$quote->ServiceType]['AccountRates'] = $rate_details->TotalNetCharge;
													}
												}
											}
										}
									}
									break;
			default : break;
		}
		return $debug_data;
	}

	private function process_result( $result = '' ) {
		if ( $result && ! empty ( $result->RateReplyDetails ) ) {

			$rate_reply_details = $result->RateReplyDetails;

			// Workaround for when an object is returned instead of array
			if ( is_object( $rate_reply_details ) && isset( $rate_reply_details->ServiceType ) )
				$rate_reply_details = array( $rate_reply_details );

			if ( ! is_array( $rate_reply_details ) )
				return false;

			// Remove same Normal Services when Saturday Delivery Services Available
			if( isset($this->satday_rates) && $this->satday_rates && $this->saturday_delivery ) {

				foreach ( $rate_reply_details as $quote ) {

					$rate_code = strval( $quote->ServiceType );
					$rate_id   = $this->id . ':' . $rate_code;

					if( isset($this->found_rates[$rate_id]) ) {

						unset($this->found_rates[$rate_id]);
					}
				}
			}

			foreach ( $rate_reply_details as $quote ) {
				
				$is_skip = apply_filters( 'wf_skip_fedex_shipping_method', false, $quote );
				if( $is_skip ){
					continue;
				}
				
				if ( is_array( $quote->RatedShipmentDetails ) ) {

					if ( $this->request_type == "LIST" ) {
						// LIST quotes return both ACCOUNT rates (in RatedShipmentDetails[1])
						// and LIST rates (in RatedShipmentDetails[3])
						foreach ( $quote->RatedShipmentDetails as $i => $d ) {
							if ( strstr( $d->ShipmentRateDetail->RateType, 'PAYOR_LIST' ) ) {
								$details = $quote->RatedShipmentDetails[ $i ];
								break;
							}
						}
					} else {
						// ACCOUNT quotes may return either ACCOUNT rates only OR
						// ACCOUNT rates and LIST rates.
						foreach ( $quote->RatedShipmentDetails as $i => $d ) {
							if ( strstr( $d->ShipmentRateDetail->RateType, 'PAYOR_ACCOUNT' ) ) {
								$details = $quote->RatedShipmentDetails[ $i ];
								break;
							}
						}
					}

				} else {
					$details = $quote->RatedShipmentDetails;
				}

				if ( empty( $details ) )
					continue;

				if( !isset($this->services[ $quote->ServiceType ]) ) {
					
					continue;
				}

				$rate_name 			= isset( $this->services[ $quote->ServiceType ] ) ? strval( $this->services[ $quote->ServiceType ] ) : '';
				$rate_name_extra 	= '';
				
				if($this->delivery_time) {
					if( !empty($quote->DeliveryTimestamp) ){
						$delivery_time_details =  strtotime( $quote->DeliveryTimestamp );
					}elseif(isset($quote->DeliveryDayOfWeek)) {
						$delivery_time_details = strtotime( 'next'.$quote->DeliveryDayOfWeek );
					}elseif(isset($quote->TransitTime)) {
						$transit_day = strtotime( $this->transit_time[$quote->TransitTime], strtotime( date('Y-m-d H:i:s') ) );
						$delivery_time_details = $transit_day;
					}
					if( !empty($delivery_time_details) ){
						
						$date_format = apply_filters('wf_estimate_delivery_date_format', ! empty( self::$wp_date_format ) ? self::$wp_date_format : 'd-m-Y' );
						$this->delivery_time_details = date( $date_format, $delivery_time_details );
						
						if( $quote->ServiceType == 'SMART_POST' ) {

							$today_date 			= clone self::$current_wp_time;
							$fedex_service_date 	= date_create_from_format( $date_format, $this->delivery_time_details );
							$transit_time_as_num 	= abs( (int) ( (double) ( strtotime($fedex_service_date->format('Y-m-d')) - strtotime($today_date->format('Y-m-d')) ) / (60*60*24) ) );

							if( ! empty($this->ship_time_adjustment) ) {
								$today_date->modify("+$this->ship_time_adjustment days");
							}

							$updated_date = $today_date;

							while( $transit_time_as_num != 0 ) {

								$transit_time_as_num--;
								$updated_date->modify("+1 day");

								if( $updated_date->format('D') == 'Sun' ) {
									$updated_date->modify("+1 day");
								}
							}
							$this->delivery_time_details = $updated_date->format($date_format);	// FedEx Ground date, Smart post Date
						}
					}
				}

				$rate_code = strval( $quote->ServiceType );
				$rate_id   = $this->id . ':' . $rate_code;
				
				if( $this->shipping_charge ==='DUTIES_AND_TAXES'&&  isset($details->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes) && isset($details->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Amount) )
				{
					$rate_cost = floatval( $details->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Amount );

				}elseif( $this->shipping_charge ==='NET_FEDEX_CHARGE'&&  isset($details->ShipmentRateDetail->TotalNetFedExCharge) && isset($details->ShipmentRateDetail->TotalNetFedExCharge->Amount) )
				{
					$rate_cost = floatval( $details->ShipmentRateDetail->TotalNetFedExCharge->Amount );

				}elseif( $this->shipping_charge ==='BASE_CHARGE'&&  isset($details->ShipmentRateDetail->TotalBaseCharge) && isset($details->ShipmentRateDetail->TotalBaseCharge->Amount) )
				{
					$rate_cost = floatval( $details->ShipmentRateDetail->TotalBaseCharge->Amount );

				}else{
					$rate_cost = floatval( $details->ShipmentRateDetail->TotalNetCharge->Amount );
				}
				
				$rate_cost = $this->convert_to_store_currency($rate_cost);
				$rate_cost = $this->convert_to_base_currency($details,$rate_cost);
				$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $rate_name_extra );
			}
		}
		elseif( is_admin() && ! is_ajax() && ! empty($result->HighestSeverity) && ! empty( $result->Notifications ) ){
			$message = 'FedEx Calculate Rate Response.<br>';
			$notifications_arr = is_object($result->Notifications) ? array($result->Notifications) : $result->Notifications;
			foreach($notifications_arr as $notifications ) {
				$message .= 'Severity - '.(string) $notifications->Severity.'. Response Code - '.(string) $notifications->Code.'. Message - '.(string) $notifications->Message.'.<br>';
			}
			wf_admin_notice::add_notice( $message,'error');
		}
	}

	/**
	 * Convert the rate to the store Currency, if fedex currency and store currency are different.
	 */
	public function convert_to_store_currency($rate_cost) {
		if( $this->fedex_currency != $this->wc_store_currency && ! empty($this->fedex_conversion_rate) ) {
			$fedex_conversion_rate	= apply_filters('ph_fedex_currency_conversion_rate_from_fedex_currency',$this->fedex_conversion_rate,$this->fedex_currency); //currency switcher
			$rate_cost = $rate_cost / $fedex_conversion_rate;
		}
		return $rate_cost;
	}
	
	private function convert_to_base_currency($details,$rate_cost){
		$converted_rate = $rate_cost;
		if($this->convert_currency_to_base == 'yes'){
			if(property_exists($details->ShipmentRateDetail,'CurrencyExchangeRate')){
				$from_currency = $details->ShipmentRateDetail->CurrencyExchangeRate->FromCurrency;
				$convertion_rate = floatval( $details->ShipmentRateDetail->CurrencyExchangeRate->Rate);
				if( $from_currency == $this->wf_get_fedex_currency() && $convertion_rate > 0 ){
					$converted_rate = $converted_rate/$convertion_rate;
				}			
			}
		}
		return $converted_rate;		
	}

	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $rate_name_extra='' ) {

		// Name adjustment
		if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
			$rate_name = $this->custom_services[ $rate_code ]['name'] . $rate_name_extra;
		}

		// Cost adjustment %, Don't apply rate adjustment on back end rates
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment_percent'] ) && ! isset($_GET['wf_fedex_generate_packages_rates']) ) {
			$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $rate_code ]['adjustment_percent'] ) / 100 ) );
		}
		// Cost adjustment, Don't apply rate adjustment on back end rates
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment'] ) && ! isset($_GET['wf_fedex_generate_packages_rates']) ) {
			$rate_cost = $rate_cost + floatval( $this->convert_to_store_currency($this->custom_services[ $rate_code ]['adjustment'] ));
		}

		// Enabled check
		if ( isset( $this->custom_services[ $rate_code ] ) && empty( $this->custom_services[ $rate_code ]['enabled'] ) ) {
			return;
		}

		// Merging
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages  = 1;
		}

		// Sort
		if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
			$sort = $this->custom_services[ $rate_code ]['order'];
		} else {
			$sort = 999;
		}

		$this->found_rates[ $rate_id ] = array(
			'id'			=> $rate_id,
			'label'			=> $rate_name,
			'cost'			=> $rate_cost,
			'sort'			=> $sort,
			'packages' 		=> $packages,
			'meta_data' 	=> array(
				'fedex_delivery_time' 	=> $this->delivery_time_details,
				'VendorId'				=> ! empty($this->vendorId) ? $this->vendorId : null,
				'_xa_fedex_method'		=>	array(
							'id'			=>	$rate_id,	// Rate id will be in format WF_FEDEX_ID:service_id ex for ground wf_fedex_woocommerce_shipping:FEDEX_GROUND
							'method_title'	=>	$rate_name,
							'items'			=>	! empty($this->current_package_items_and_quantity) ? $this->current_package_items_and_quantity : array(),
						),
			),
		);
		
		// For fetching the rates on order page
		if( $this->found_rates && isset($_GET['wf_fedex_generate_packages_rates']) && !empty($_GET['oid']) ) {
			update_post_meta( $_GET['oid'], 'wf_fedex_generate_packages_rates_response', $this->found_rates );
		}
	}

	/**
	 * Add rates to the Woocommerce Cart or Checkout.
	 */
	public function add_found_rates() {

		if ( $this->found_rates ) {
			
			if( $this->conversion_rate ) {
				foreach ( $this->found_rates as $key => $rate ) {
					$this->found_rates[ $key ][ 'cost' ] = $rate[ 'cost' ] * $this->convert_to_store_currency($this->conversion_rate);
				}
			}

			// Check for Minimum Shipping Cost
			if( ! empty($this->min_shipping_cost) ) {
				foreach ( $this->found_rates as $key => $rate ) {
					if( (double) $this->found_rates[ $key ][ 'cost' ] < (double) $this->convert_to_store_currency($this->min_shipping_cost) ) {
						$this->found_rates[ $key ][ 'cost' ] = (double) $this->convert_to_store_currency($this->min_shipping_cost);
					}
				}
			}
			// Check for Maximum Shipping Cost
			if( ! empty($this->max_shipping_cost) ) {
				foreach ( $this->found_rates as $key => $rate ) {
					if( (double) $this->found_rates[ $key ][ 'cost' ] > $this->convert_to_store_currency($this->max_shipping_cost) ) {
						$this->found_rates[ $key ][ 'cost' ] = (double) $this->convert_to_store_currency($this->max_shipping_cost);
					}
				}
			}

			if ( $this->offer_rates == 'all' ) {

				uasort( $this->found_rates, array( $this, 'sort_rates' ) );

				foreach ( $this->found_rates as $key => $rate ) {
					$this->add_rate( $rate );
				}
			} else {
				$cheapest_rate = '';

				foreach ( $this->found_rates as $key => $rate ) {
					if ( ! $cheapest_rate || $cheapest_rate['cost'] > $rate['cost'] ) {
						$cheapest_rate = $rate;
					}
				}

				$cheapest_rate['label'] = $this->title;

				$this->add_rate( $cheapest_rate );
			}
		} elseif ( $this->fallback_rate ) {

			$this->add_rate( 
				array(
					'id' 	=> $this->id . '_fallback',
					'label' => $this->title,
					'cost' 	=> $this->fallback_rate,
					'sort'  => 0
				)
			);

			$this->debug( __('FedEx: Using Fallback Setting.', 'wf-shipping-fedex') );

			$this->diagnostic_report( 'Using Fallback Rate' );
		}
	}

	public function sort_rates( $a, $b ) {
		if ( $a['sort'] == $b['sort'] ) return 0;
		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
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

}
