<?php
class wf_fedex_woocommerce_shipping_admin{

	//PDS-179
	public $prioritizedSignatureOption 	= array( 5=>'ADULT',4=>'DIRECT',3=>'INDIRECT',2=>'SERVICE_DEFAULT',1=>'NO_SIGNATURE_REQUIRED',0=>'' );
	
	public function __construct(){
		add_action('init', array($this, 'wf_init'));
		
		if (is_admin()) {
			$this->init_bulk_printing();
			add_action('add_meta_boxes', array($this, 'wf_add_fedex_metabox'));
		}
		if ( isset( $_GET['wf_fedex_generate_packages'] ) ) {
			add_action('init', array($this, 'wf_fedex_generate_packages'));
		}

		if (isset($_GET['wf_fedex_createshipment'])) {
			add_action('init', array($this, 'wf_fedex_createshipment'));
		}
		
		if (isset($_GET['wf_fedex_generate_packages_rates'])) {
			add_action('init', array($this, 'wf_fedex_generate_packages_rates'));
		}
		
		if (isset($_GET['wf_fedex_additional_label'])) {
			add_action('init', array($this, 'wf_fedex_additional_label'));
		}
		
		if (isset($_GET['wf_fedex_viewlabel'])) {
			add_action('init', array($this, 'wf_fedex_viewlabel'));
		}

		if (isset($_GET['wf_fedex_void_shipment'])) {
			add_action('init', array($this, 'wf_fedex_void_shipment'));
		}
		
		if (isset($_GET['wf_clear_history'])) {
			add_action('init', array($this, 'wf_clear_history'));
		}

		if (isset($_GET['ph_client_reset_link'])) {  
			add_action('init', array($this, 'wf_clear_history'));
		}

		if (isset($_GET['wf_create_return_label'])) {
			add_action('init', array($this, 'wf_create_return_label'));
		}
		
		if (isset($_GET['wf_fedex_viewReturnlabel'])) {
			add_action('init', array($this, 'wf_fedex_viewReturnlabel'));
		}

		add_action( 'wp_ajax_xa_fedex_validate_credential', array($this,'xa_fedex_validate_credentials'), 10, 1 );
		
		add_action('admin_notices',array(new wf_admin_notice, 'throw_notices'));
		add_action('woocommerce_admin_order_actions_end', array(&$this, 'fedex_action_column'),2);
	}

	public function wf_init(){
		global $woocommerce;
		$this->rateservice_version			  = 31;
		$this->settings 			= get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		
		$this->weight_dimensions_manual 	= isset($this->settings['manual_wgt_dimensions']) ? $this->settings['manual_wgt_dimensions'] : 'no';
		$this->custom_services 				= isset($this->settings['services']) ? $this->settings['services'] : '';
		$this->image_type 					= isset($this->settings['image_type']) ? $this->settings['image_type'] : '';
		$this->packing_method 				= isset($this->settings[ 'packing_method']) ? $this->settings[ 'packing_method'] : '';
		$this->debug 						= ( isset($this->settings[ 'debug' ]) && ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ) ? true : false;
		$this->xa_show_all_shipping_methods		= isset( $this->settings['xa_show_all_shipping_methods'] ) && $this->settings['xa_show_all_shipping_methods'] == 'yes' ? true : false;
		$this->display_fedex_meta_box_on_order 	= isset($this->settings['display_fedex_meta_box_on_order']) ? $this->settings['display_fedex_meta_box_on_order'] : 'yes';

		$this->production					= ( isset($this->settings['production']) && ( $bool = $this->settings['production'] ) && $bool == 'yes' ) ? true : false;

		if(isset($this->settings['dimension_weight_unit']) && $this->settings['dimension_weight_unit'] == 'LBS_IN'){
			$this->dimension_unit 			= 	'in';
			$this->weight_unit 				= 	'lbs';
		}else{
			$this->dimension_unit 			= 	'cm';
			$this->weight_unit 				= 	'kg';
		}
		
		$this->set_origin_country_state();

		$this->account_number 		= isset($this->settings[ 'account_number' ]) && !empty($this->settings['account_number']) ? $this->settings['account_number'] : '';
		$this->meter_number 		= isset($this->settings[ 'meter_number' ]) && !empty($this->settings['meter_number']) ? $this->settings['meter_number'] : '';
		$this->api_key 				= isset($this->settings[ 'api_key' ]) && !empty($this->settings['api_key']) ? $this->settings['api_key'] : '';
		$this->api_pass 			= isset($this->settings[ 'api_pass' ]) && !empty($this->settings['api_pass']) ? $this->settings['api_pass'] : '';
		$this->hold_at_location 	= isset($this->settings['hold_at_location']) && $this->settings['hold_at_location'] == 'yes' ? true : false;
		$this->saturday_delivery 	= ( isset($this->settings['saturday_delivery']) && !empty($this->settings['saturday_delivery']) && $this->settings['saturday_delivery'] == 'yes' ) ? true : false;
		$this->freight_enabled 		= ( $bool = isset( $this->settings[ 'freight_enabled'] ) ? $this->settings[ 'freight_enabled'] : 'no' ) && $bool == 'yes' ? true : false;
		$this->custom_scaling 		= ( isset($this->settings['label_custom_scaling']) && !empty($this->settings['label_custom_scaling']) ) ? $this->settings['label_custom_scaling'] : '100';
		$this->client_side_reset 	= ( isset($this->settings['client_side_reset']) && !empty($this->settings['client_side_reset']) && $this->settings['client_side_reset'] == 'yes' ) ? true : false;
		$this->etd_label 			= (isset($this->settings['etd_label']) && ($this->settings['etd_label'] == 'yes')) ? true : false;
		$this->home_delivery_premium 		= (isset($this->settings['home_delivery_premium']) && ($this->settings['home_delivery_premium'] == 'yes')) ? true : false;
		$this->home_delivery_premium_type 	=	( isset($this->settings['home_delivery_premium_type']) && !empty($this->settings['home_delivery_premium_type']) ) ? $this->settings['home_delivery_premium_type'] : '';

		// Hold At Location
		if( $this->hold_at_location ) {
			add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'ph_editable_hold_at_location'), 15 );
			add_action( 'woocommerce_process_shop_order_meta', array($this, 'ph_save_hold_at_location'), 15 );
		}
	}

	private function set_origin_country_state(){
		$origin_country_state 		= isset( $this->settings['origin_country'] ) ? $this->settings['origin_country'] : '';
		if ( strstr( $origin_country_state, ':' ) ) :
			// WF: Following strict php standards.
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_country 		= current($origin_country_state_array);
			$origin_country_state_array 	= explode(':',$origin_country_state);
			$origin_state   		= end($origin_country_state_array);
		else :
			$origin_country = $origin_country_state;
			$origin_state   = '';
		endif;

		$this->origin_country  	= apply_filters( 'woocommerce_fedex_origin_country_code', $origin_country );
		$this->origin_state 	= !empty($origin_state) ? $origin_state : ( isset($this->settings[ 'freight_shipper_state' ]) ? $this->settings[ 'freight_shipper_state' ] : '') ;
	}

	public function ph_editable_hold_at_location( $order ){

		$hold_at_location 		= get_post_meta( $order->get_id(), 'ph_fedex_hold_at_location', true );
		$selected_location 		= '';
		$all_locations 			= [];
		$request 				= [];
		$response 				= null;
		$shipping_address 		= $order->get_shipping_address_1();
		$shipping_city 			= $order->get_shipping_city();
		$shipping_postalcode 	= $order->get_shipping_postcode();
		$shipping_state 		= $order->get_shipping_state();
		$shipping_country 		= $order->get_shipping_country();
		$hold_at_location_carrier_code 	    = isset ( $this->settings['hold_at_location_carrier_code'] )  && !empty ($this->settings['hold_at_location_carrier_code']) ? $this->settings['hold_at_location_carrier_code'] : ''; 

		$supported_hold_at_location_type = array(
			"FEDEX_EXPRESS_STATION",
			"FEDEX_FACILITY",
			"FEDEX_FREIGHT_SERVICE_CENTER",
			"FEDEX_GROUND_TERMINAL",
			"FEDEX_HOME_DELIVERY_STATION",
			"FEDEX_OFFICE",
			"FEDEX_SHIPSITE",
			"FEDEX_SMART_POST_HUB",
			"FEDEX_ONSITE"
		);

		if( ! empty($hold_at_location) ) {

			$selected_location 	= (isset($hold_at_location->LocationDetail) && isset($hold_at_location->LocationDetail->LocationId)) ? $hold_at_location->LocationDetail->LocationId : '';
		}

		$request['WebAuthenticationDetail'] = array(
			'UserCredential' => array(
				'Key'       => $this->api_key,
				'Password'  => $this->api_pass,
			)
		);
		$request['ClientDetail']            = array(
			'AccountNumber' => $this->account_number,
			'MeterNumber'   => $this->meter_number,
		);
		$request['Version']                 = array(
			'ServiceId'     => 'locs',
			'Major'         => '12',
			'Intermediate'  => '0',
			'Minor'         => '0'
		);
		$request['LocationsSearchCriterion'] = 'ADDRESS';
		$request['Address']                 = array(
			'PostalCode'            => $shipping_postalcode,
			'City'                  => $shipping_city,
			'StateOrProvinceCode'   => $shipping_state,
			'CountryCode'           => $shipping_country,
		);
		$request['MultipleMatchesAction']   = 'RETURN_ALL';
		$request['SortDetail']              = array(
			'Criterion'     => 'DISTANCE',
			'Order'         => 'LOWEST_TO_HIGHEST',
		);

		$request['Constraints'] =[];
		$request['Constraints'] = array(
            'RadiusDistance' => array(
                'Value'      => '10',
                'Units'      => 'MI',
            ),
            'RequiredLocationCapabilities' => array(
                'TransferOfPossessionType' => 'HOLD_AT_LOCATION',
            )
          );
		if(!empty($hold_at_location_carrier_code)){
			$request['Constraints']['RequiredLocationCapabilities']['CarrierCode'] = $hold_at_location_carrier_code;
		}

		$this->request_hash 	= md5(json_encode($request));
		$transient_data  		= get_transient($this->request_hash);

		if( !empty($transient_data) ) {
			
			$response 	= json_decode(get_transient( $this->request_hash ));

		} else {

			$this->hal_version 	= '12';
			$this->soap_method 	= $this->is_soap_available() ? 'soap' : 'nusoap';
			$client 			= $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/'. ( $this->production ? 'production' : 'test' ) .'/LocationsService_v'.$this->hal_version.'.wsdl' );

			if( $this->soap_method == 'nusoap' ) {

				$response = $client->call( 'searchLocations', array( 'SearchLocationsRequest' => $request ) );
				$response = json_decode( json_encode( $response ), false );
			}else{

				try {
					$response = $client->searchLocations( $request );
				}
				catch( Exception $e ) {
					$exception_message = $e->getMessage();
				}
			}
			
			set_transient($this->request_hash,json_encode($response),HOUR_IN_SECONDS);
		}
		
		if( isset($response->AddressToLocationRelationships) && !empty($response->AddressToLocationRelationships) ) {

			if( ! empty($response->AddressToLocationRelationships->DistanceAndLocationDetails) && is_array($response->AddressToLocationRelationships->DistanceAndLocationDetails) ) {

				foreach( $response->AddressToLocationRelationships->DistanceAndLocationDetails as $location ) {
					$all_locations[$location->LocationDetail->LocationId] = $location;
				}

				update_post_meta( $order->get_id(), 'ph_available_fedex_hold_at_location', $all_locations );
			}
		}

		$locator='<div class="edit_address"><p class="form-field form-field-wide"><label>FedEx Hold At Location</label><select id="shipping_hold_at_location" name="shipping_hold_at_location" class="select first" >';
		$locator .=	"<option value=''>". __('Select Hold At Location', 'ups-woocommerce-shipping') ."</option>";

		if(!empty($all_locations)) {

			$hold_at_location_types 	=   apply_filters('ph_fedex_supported_hold_at_location_types', $supported_hold_at_location_type);

			foreach ($all_locations as $location_id => $location) {

				if( !empty($location->LocationDetail->LocationType) && in_array($location->LocationDetail->LocationType, $hold_at_location_types) ) {
					
					$address = null;

					// Street Address
					if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->StreetLines) ) {
						$address = $location->LocationDetail->LocationContactAndAddress->Address->StreetLines;
					}
					
					// City
					if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->City) ) {
						$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->City : $location->LocationDetail->LocationContactAndAddress->Address->City;
					}
					
					// State
					if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode) ) {
						$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode : $location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode;
					}
					
					// Postal Code
					if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->PostalCode) ) {
						$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->PostalCode : $location->LocationDetail->LocationContactAndAddress->Address->PostalCode;
					}
					
					// Country
					$address = !empty($address) ? $address.', '.$location->LocationDetail->LocationContactAndAddress->Address->CountryCode : $location->LocationDetail->LocationContactAndAddress->Address->CountryCode;
					
					//Distance
					if( ! empty($location->Distance->Value) ) {
						$address = $address.'. ('. $location->Distance->Value.' '. $location->Distance->Units.')';
					}

					if( $selected_location != $location_id) {
						$locator .= "<option value= '".$location_id."'> ". $address ."</option>";
					}else{
						$locator .= "<option value= '".$location_id."' selected> ". $address ."</option>";
					}
				}

			}
		}

		$locator .=	'</select></p></div>';
		$array['#shipping_hold_at_location'] = $locator;
		
		echo $array['#shipping_hold_at_location'];

	}

	function ph_save_hold_at_location( $post_id ){

		$hold_at_location 		= get_post_meta( $post_id, 'ph_available_fedex_hold_at_location', true );
		$selected_location 		= isset($_POST['shipping_hold_at_location']) ? $_POST['shipping_hold_at_location'] : '';

		if( !empty($selected_location) && !empty($hold_at_location) && array_key_exists($selected_location, $hold_at_location) ) {

			$location 	= $hold_at_location[$selected_location];

			$address = null;

			// Street Address
			if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->StreetLines) ) {
				$address = $location->LocationDetail->LocationContactAndAddress->Address->StreetLines;
			}

			// City
			if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->City) ) {
				$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->City : $location->LocationDetail->LocationContactAndAddress->Address->City;
			}

			// State
			if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode) ) {
				$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode : $location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode;
			}

			// Postal Code
			if( ! empty($location->LocationDetail->LocationContactAndAddress->Address->PostalCode) ) {
				$address = ! empty($address) ? $address.', '. $location->LocationDetail->LocationContactAndAddress->Address->PostalCode : $location->LocationDetail->LocationContactAndAddress->Address->PostalCode;
			}

			// Country
			$address = !empty($address) ? $address.', '.$location->LocationDetail->LocationContactAndAddress->Address->CountryCode : $location->LocationDetail->LocationContactAndAddress->Address->CountryCode;

			$selectedAddress    = $address;
			
			//Distance
			if( ! empty($location->Distance->Value) ) {
				$address = $address.'. ('. $location->Distance->Value.' '. $location->Distance->Units.')';
			}

			if( isset($location->LocationDetail->LocationContactAndAddress->Contact) && isset($location->LocationDetail->LocationContactAndAddress->Contact->CompanyName) ) {

				$selectedAddress    = $location->LocationDetail->LocationContactAndAddress->Contact->CompanyName.', '.$selectedAddress;
			}
			
			update_post_meta( $post_id, 'ph_fedex_hold_at_location', $hold_at_location[$selected_location] );
			update_post_meta( $post_id, 'ph_fedex_selected_hold_at_location', $selectedAddress );
		} else {
			update_post_meta( $post_id, 'ph_fedex_hold_at_location', '' );
			update_post_meta( $post_id, 'ph_fedex_selected_hold_at_location', '' );
		}
			
	}

	/**
	 * Display the messages in debug mode.
	 * @param $message mixed Message to display.
	 */
	public function print_debug_message( $message ) {
		if ( $this->debug ) {
			echo "<pre>".print_r($message,true)."</pre>";
		}
	}

	function init_bulk_printing(){
		add_action('admin_footer', 	array($this, 'add_bulk_print_option'));
		add_action('load-edit.php',	array($this, 'perform_bulk_label_actions'));
	}

	function add_bulk_print_option(){
		global $post_type;

		if($post_type == 'shop_order') {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('<option>').val('fedex_print_label').text('<?php _e('Print FedEx label', 'wf-shipping-fedex');?>').appendTo("select[name='action']");
					jQuery('<option>').val('fedex_print_label').text('<?php _e('Print FedEx label', 'wf-shipping-fedex');?>').appendTo("select[name='action2']");

					jQuery('<option>').val('wf_create_shipment').text('<?php _e('Create FedEx label', 'wf-shipping-fedex');?>').appendTo("select[name='action']");
					jQuery('<option>').val('wf_create_shipment').text('<?php _e('Create FedEx label', 'wf-shipping-fedex');?>').appendTo("select[name='action2']");
				});
			</script>
			<?php
		}
	}

	function perform_bulk_label_actions() {
		
		$wp_list_table 	= _get_list_table('WP_Posts_List_Table');
		$action 		= $wp_list_table->current_action();

		if( $action == 'fedex_print_label' ) {

			if( isset($_REQUEST['post']) && is_array($_REQUEST['post']) ) {

				$shipping_labels	= array();
				
				foreach($_REQUEST['post'] as $order_id) {

					$shipmentIds = get_post_meta($order_id, 'wf_woo_fedex_shipmentId', false);
					// Some Customers Site wont allow adding duplicate Meta Keys in DB, Adding new meta key with custom build Shipment Id Array
					$shipment_ids 		= get_post_meta($order_id, 'ph_woo_fedex_shipmentIds', true);

					if( is_array($shipmentIds) && is_array($shipment_ids) ){
						$shipmentIds  		= array_unique(array_merge($shipmentIds,$shipment_ids));
					}
					
					foreach($shipmentIds as $shipmentId) {

						$shipping_labels[] 			= get_post_meta($order_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
						$shipping_label_image_type	= get_post_meta($order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipmentId, true);

						if( $shipping_label_image_type !='PNG' ) {
							wf_admin_notice::add_notice( __("Bulk label printing will work with only PNG format, You have selected label format '$shipping_label_image_type'", 'wf-shipping-fedex') );
							return;
						}
					}
				}

				if( empty($shipping_labels) ) {
					wf_admin_notice::add_notice( __('No Fedex label found on selected order', 'wf-shipping-fedex') );
					wp_redirect( admin_url( '/edit.php?post_type=shop_order') );
					exit();
				}

				echo "<html>
						<body style='margin: 0; display: flex; flex-direction: column; justify-content: center;'>
							<div style='text-align: center;'>";
				
							foreach ($shipping_labels as $key => $label) {

								echo "<div>";

									echo "<img style='max-width: ".$this->custom_scaling."%;' src='data:image/png;base64,". $label . "'/>";

								echo "</div>";
							}

				echo "		</div>
						</body>
					 </html>";

				exit();

			} else {
				wf_admin_notice::add_notice( __('Please select atleast one order', 'wf-shipping-fedex') );
			}

		} elseif( $action == 'wf_create_shipment' ) {
			
			if( isset($_REQUEST['post']) && is_array($_REQUEST['post']) ){
				foreach($_REQUEST['post'] as $order_id){
					$order = $this->wf_load_order($order_id);
					if($order) {
						$package = get_post_meta( $order->get_id(), '_wf_fedex_stored_packages', true );		
						if( empty($package) ){
							$this->xa_generate_package($order);
						}

						$shipmentIds = get_post_meta($order->get_id(), 'wf_woo_fedex_shipmentId', false);
						if( !empty($shipmentIds) ){
							wf_admin_notice::add_notice( __("Label already generated for order $order_id", 'wf-shipping-fedex'), 'notice' );
							continue;
						}

						$this->wf_create_shipment($order);
						
						$shipmentIds = get_post_meta($order->get_id(), 'wf_woo_fedex_shipmentId', false);
						if( !empty($shipmentIds) ){
							wf_admin_notice::add_notice( __("Label has been generated for order $order_id", 'wf-shipping-fedex'), 'notice' );
						}else{
							wf_admin_notice::add_notice( __("There is some error occured while creating the shipment for order $order_id", 'wf-shipping-fedex'), 'error' );
						}
						wp_redirect( admin_url( '/edit.php?post_type=shop_order') );
					}
				}
			}
		}
	}

	public function xa_fedex_validate_credentials() {

		$production			= ( isset($_POST['production']) ) ? $_POST['production'] =='true' : false;
		$account_number		= ( isset($_POST['account_number']) ) ? $_POST['account_number'] : '';
		$meter_number		= ( isset($_POST['meter_number']) ) ? $_POST['meter_number'] : '';
		$api_key			= ( isset($_POST['api_key']) ) ? $_POST['api_key'] : '';
		$api_pass			= ( isset($_POST['api_pass']) ) ? $_POST['api_pass'] : '';
		
		if ( empty($account_number) || empty($meter_number) || empty($api_key) || empty($api_pass) ) {

			$result = array(

				'message' 	=> "Please fill the FedEx account details above and try again.",
				'success'	=> 'no',
			);

			wp_die( json_encode($result) );
		}

		$origin_country_state		= ( isset($_POST['origin_country']) ) ? $_POST['origin_country'] : '';
		$origin						= ( isset($_POST['origin']) ) ? $_POST['origin'] : '';
		$origin_country_state_array = explode(':',$origin_country_state);
		$origin_country 			= current($origin_country_state_array);

		$request = array(
			'WebAuthenticationDetail' => array(
				'UserCredential' => array(
					'Key' => $api_key,
					'Password' => $api_pass,
				),
			),
			'ClientDetail' => array(
				'AccountNumber' => $account_number,
				'MeterNumber' => $meter_number,
			),
			'TransactionDetail' => array(
				'CustomerTransactionId' =>  '*** WooCommerce Rate Request ***',
			),
			'Version' => array(
				'ServiceId' => 'crs',
				'Major' => 31,
				'Intermediate' => 0,
				'Minor' => 0,
			),
			'ReturnTransitAndCommit' => 1,
			'RequestedShipment' => array(
				'EditRequestType' => 1,
				'PreferredCurrency' => 'USD',
				'DropoffType' => 'REGULAR_PICKUP',
				'Shipper' => array(
					'Address' => array(
						'PostalCode' => $origin,
						'CountryCode' => $origin_country,
					),
				),
				'Recipient' => array(
					'Address' => array(
						'PostalCode' => '90017',
						'City' => 'LOSE ANGELES',
						'StateOrProvinceCode' => 'CA',
						'CountryCode' => 'US',
					),
				),
				'RequestedPackageLineItems' => array(
					0 => array(
						'SequenceNumber' => 1,
						'GroupNumber' => 1,
						'GroupPackageCount' => 1,
						'Weight' => array(
							'Value' => '5.52',
							'Units' => 'LB',
						),
					),
				),
			),
		);

		$this->soap_method = $this->is_soap_available() ? 'soap' : 'nusoap';
		if( $this->soap_method == 'nusoap' && !class_exists('nusoap_client') ){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nusoap/lib/nusoap.php';
		}
		$client = $this->wf_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/' . ( $production ? 'production' : 'test' ) . '/RateService_v' . $this->rateservice_version. '.wsdl' );

		$log = new WC_Logger();
		try {
			if( $this->soap_method == 'nusoap' ){
				$response = $client->call( 'getRates', array( 'RateRequest' => $request ) );
				$response = json_decode( json_encode( $response ), false );
				
				if( $client->fault ) {
					$log->add('Fedex Soap Details', " Nusoap Fault : ".print_r($response,true));
				}
				elseif( $client->getError() ) {
					$log->add('Fedex Soap Details', " Nusoap Error : ".print_r($client->getError(),true));
				}
				if( empty($response) ) {
					$log->add( 'Fedex Soap Details', "NuSoap Debug Data : ".print_r(htmlspecialchars($client->debug_str, ENT_QUOTES),true));
				}
			}
			else{
				$response = $client->getRates( $request );
				if( is_soap_fault($response) ) {
					$log->add( 'Fedex Soap Details', " Soap Fault ".print_r($response,true) );
				}
			}
		}
		catch(Exception $e) {
			$log->add( 'Fedex Soap Details', 'Exception Occured - '.print_r($e->getMessage(),true));
		}
		
		$result = array();
		if ( $response  ) {
			if( isset($response->HighestSeverity) && $response->HighestSeverity === 'ERROR' ){
				$error_message = '';
				if( isset($response->Notifications->Message) ) {
					$result = array(
						'message' 	=> $response->Notifications->Message,
						'success'	=> 'no',
					);
				}elseif( isset($response->Notifications[0]->Message) ) {
					$result = array(
						'message' 	=> $response->Notifications[0]->Message,
						'success'	=> 'no',
					);
				}
			}else{
				$result = array(
					'message' 	=> "Successfully authenticated, The credentials are valid. Soapmethod : $this->soap_method",
					'success'	=> 'yes',
				);
			}
		}else{
			$result = array(
				'message' 	=> "An unexpected error occurred. No response from soap client. Unable to authenticate. Soapmethod : $this->soap_method",
				'success'	=> 'no',
			);
		}
		wp_die( json_encode($result) );
	}


	public function wf_fedex_viewReturnlabel(){

		$settings 				= get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		$show_label_in_browser  = isset( $settings['show_label_in_browser'] ) ? $settings['show_label_in_browser'] : 'no';
		$shipmentDetails 		= explode('|', base64_decode($_GET['wf_fedex_viewReturnlabel']));

		if (count($shipmentDetails) != 2) {
			exit;
		}
		
		$shipmentId					= $shipmentDetails[0]; 
		$post_id					= $shipmentDetails[1]; 
		$shipping_label				= get_post_meta($post_id, 'wf_woo_fedex_returnLabel_'.$shipmentId, true);
		$shipping_label_image_type	= get_post_meta($post_id, 'wf_woo_fedex_returnLabel_image_type_'.$shipmentId, true);

		if( empty($shipping_label_image_type) ){
			$shipping_label_image_type = $this->image_type;
		}

		if( $show_label_in_browser == "yes" && $shipping_label_image_type == "PNG" && $this->image_type == 'png' ) {

			$final_image 		= base64_decode(chunk_split($shipping_label));;
			$final_image 		= imagecreatefromstring($final_image);
			$html_before_image 	= "<html><body style='margin: 0; display: flex; flex-direction: column; justify-content: center;'><div style='text-align: center;'>";
			$html_after_image 	= "</div></body></html>";
			$image_style 		= "style='max-width: 100%;'";

			ob_start();
			imagepng($final_image);
			$contents =  ob_get_contents();
			ob_end_clean();
			echo $html_before_image."<img ".$image_style." src='data:image/gif;base64,".base64_encode($contents)."'/>".$html_after_image;

		}else{

			header('Content-Type: application/'.$shipping_label_image_type);
			$label_name = apply_filters( 'ph_fedex_label_name', 'ShipmentArtifact-'.$shipmentId, $shipmentId, $post_id, 'return_label' );
			header('Content-disposition: attachment; filename="' . $label_name . '.'.$shipping_label_image_type.'"');
			print(base64_decode($shipping_label)); 
		}
		exit;
	}

	private function is_soap_available(){
		if( extension_loaded( 'soap' ) ){
			return true;
		}
		return false;
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

	public function wf_create_return_label(){

		$user_ok = $this->wf_user_permission();
		if (!$user_ok) 			
			return;

		$return_params = explode('|', base64_decode($_GET['wf_create_return_label']));
		
		if(empty($return_params) || !is_array($return_params) || count($return_params) != 2)
			return;
		
		$shipment_id = $return_params[0]; 
		$order_id =  $return_params[1];


		$this->wf_create_return_shipment( $shipment_id, $order_id );

		if ( $this->debug ) {
			//dont redirect when debug is printed
			die();
		}
		else{
		  wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
		  exit;
		}

	}
	
	public function wf_clear_history(){

		$user_ok = $this->wf_user_permission();

		if (!$user_ok) {
			wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
			exit;
		}

		if( isset($_GET['ph_client_reset_link']) ) { 

			$order_id 			= base64_decode($_GET['ph_client_reset_link']);
			$void_shipments  	= get_post_meta($order_id, 'wf_woo_fedex_shipmentId', false);
			$shipment_ids    	= get_post_meta($order_id, 'ph_woo_fedex_shipmentIds', true);

			if( is_array($void_shipments) && is_array($shipment_ids) ){
				$void_shipments  		= array_unique(array_merge($void_shipments,$shipment_ids));
			}

		} else{

			$order_id 			= base64_decode($_GET['wf_clear_history']);
			$void_shipments 	= get_post_meta($order_id, 'wf_woo_fedex_shipment_void',false);
		}	
		
		
		if(empty($order_id))
			return;
		
		if(empty($void_shipments)){
			wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
			exit;
		}
		
		foreach($void_shipments as $void_shipment_id){
			delete_post_meta($order_id, 'wf_woo_fedex_packageDetails_'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shippingLabel_'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_service_code'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shippingLabel_image_type_'.$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shipmentId',$void_shipment_id);
			delete_post_meta($order_id, 'wf_woo_fedex_shipment_void',$void_shipment_id);
			delete_post_meta($order_id, 'wf_fedex_additional_label_',$void_shipment_id);
		}
		
		delete_post_meta($order_id, 'wf_woo_fedex_shipment_void_errormessage');		
		delete_post_meta($order_id, 'wf_woo_fedex_service_code');
		delete_post_meta($order_id, 'wf_woo_fedex_shipmentErrorMessage');	
		delete_post_meta($order_id, 'ph_woo_fedex_shipmentIds'); //New added meta key	
					
		wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
		exit;	
	}	
	
	public function wf_fedex_void_shipment(){
		$user_ok = $this->wf_user_permission();
		if (!$user_ok) 			
			return;
			
		$void_params = explode('||', base64_decode($_GET['wf_fedex_void_shipment']));
		
		if(empty($void_params) || !is_array($void_params) || count($void_params) != 2)
			return;
		
		$shipment_id = $void_params[0]; 
		$order_id =  $void_params[1];			
			
			
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		
		$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();
		$tracking_completedata = get_post_meta($order_id, 'wf_woo_fedex_tracking_full_details_'.$shipment_id, true);
		if(!empty($tracking_completedata)){
			$woofedexwrapper->void_shipment($order_id,$shipment_id,$tracking_completedata);
		}
		
		if ( $this->debug ) {
			//dont redirect when debug is printed
			die();
		}
		else{
		  wp_redirect(admin_url('/post.php?post='.$order_id.'&action=edit'));
		  exit;
		}
	}
	
	function wf_load_order( $orderId ){
		if( !$orderId ){
			return false;
		}
		if(!class_exists('wf_order')){
			include_once('class-wf-legacy.php');
		}
		return ( WC()->version < '2.7.0' ) ? new WC_Order( $orderId ) : new wf_order( $orderId );	
	}
	
	private function wf_user_permission($auto_generate=null){
		// Check if user has rights to generate invoices
		$current_minute=(integer)date('i');
		if(!empty($auto_generate) && ($auto_generate==md5($current_minute) || $auto_generate==md5($current_minute+1) ))
		{
			return true;
		}
		$current_user = wp_get_current_user();
		$user_ok = false;
		$wf_roles = apply_filters( 'wf_user_permission_roles', array('administrator', 'shop_manager') );

		if ($current_user instanceof WP_User) {
			$role_ok = array_intersect($wf_roles, $current_user->roles);
			if( !empty( $role_ok ) ){
				$user_ok = true;
			}
		}
		return $user_ok;
	}
	
	public function wf_fedex_createshipment(){
		$user_ok = $this->wf_user_permission(isset($_GET['auto_generate'])?$_GET['auto_generate']:null);
		if (!$user_ok) 			
			return;
		
		$order = $this->wf_load_order($_GET['wf_fedex_createshipment']);
		if (!$order) 
			return;
		
		$shipment_ids = get_post_meta( $_GET['wf_fedex_createshipment'], 'wf_woo_fedex_shipmentId', true );
		if( empty($shipment_ids) ) {
			$this->wf_create_shipment($order);
		}
		else{
			if( $this->debug ) {
				_e( 'Fedex label generation Suspended. Label has been already generated.', 'wf-shipping-fedex' );
			}
			if( class_exists('WC_Admin_Meta_Boxes') ) {
				WC_Admin_Meta_Boxes::add_error( 'Fedex label generation Suspended. Label has been already generated.', 'wf-shipping-fedex' );
			}
		}

		if ( $this->debug ) {
			//dont redirect when debug is printed
			die();
		}
		else{
		  wp_redirect(admin_url('/post.php?post='.$_GET['wf_fedex_createshipment'].'&action=edit'));
		  exit;
		}
	}
	
	public function wf_fedex_viewlabel(){
		$settings 					= get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		$show_label_in_browser      = isset( $settings['show_label_in_browser'] ) ? $settings['show_label_in_browser'] : 'no';
		$shipmentDetails 			= explode('|', base64_decode($_GET['wf_fedex_viewlabel']));

		if (count($shipmentDetails) != 2) {
			exit;
		}
		
		$shipmentId 				= $shipmentDetails[0]; 
		$post_id 					= $shipmentDetails[1]; 
		$shipping_label 			= get_post_meta($post_id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
		$shipping_label_image_type 	= get_post_meta($post_id, 'wf_woo_fedex_shippingLabel_image_type_'.$shipmentId, true);
		
		if( empty($shipping_label_image_type) ){
			$shipping_label_image_type = $this->image_type;
		}

		if( $show_label_in_browser == "yes" && $shipping_label_image_type == "PNG" && $this->image_type == 'png' ) {

			$final_image 		= base64_decode(chunk_split($shipping_label));;
			$final_image 		= imagecreatefromstring($final_image);
			$html_before_image 	= "<html><body style='margin: 0; display: flex; flex-direction: column; justify-content: center;'><div style='text-align: center;'>";
			$html_after_image 	= "</div></body></html>";
			$image_style 		= "style='max-width: 100%;'";

			ob_start();
			imagepng($final_image);
			$contents =  ob_get_contents();
			ob_end_clean();
			echo $html_before_image."<img ".$image_style." src='data:image/gif;base64,".base64_encode($contents)."'/>".$html_after_image;

		}else{

			header('Content-Type: application/'.$shipping_label_image_type);
			$label_name = apply_filters( 'ph_fedex_label_name', 'ShipmentArtifact-'.$shipmentId, $shipmentId, $post_id, 'normal_label' );
			header('Content-disposition: attachment; filename="' . $label_name . '.'.$shipping_label_image_type.'"');
			print(base64_decode($shipping_label)); 
		}
		exit;
	}
	
	public function wf_fedex_additional_label(){
		$shipmentDetails = explode('|', base64_decode($_GET['wf_fedex_additional_label']));

		if (count($shipmentDetails) != 3) {
			exit;
		}
		
		$shipmentId = $shipmentDetails[0]; 
		$post_id = $shipmentDetails[1];
		$add_key = $shipmentDetails[2];		
		$additional_labels = get_post_meta($post_id, 'wf_fedex_additional_label_'.$shipmentId, true);
		$additional_label_image_type = get_post_meta($post_id, 'wf_fedex_additional_label_image_type_'.$shipmentId, true);
		
		if( !empty($additional_label_image_type[$add_key])){
			$image_type = $additional_label_image_type[$add_key];
		}else{
			$image_type = $this->image_type;
		}
		if( !empty($additional_labels) && isset($additional_labels[$add_key]) ){
			header('Content-Type: application/'.$image_type);
			$label_name = apply_filters( 'ph_fedex_label_name', 'Addition-doc-'. $add_key .'-'.$shipmentId, $add_key.'-'.$shipmentId, $post_id, 'additional_label' );
			header('Content-disposition: attachment; filename="' . $label_name . '.'.$image_type.'"');
			print(base64_decode($additional_labels[$add_key])); 
		}
		exit;		
	}
	
	private function wf_is_service_valid_for_country($order, $service_code, $dest_country=''){
		$uk_domestic_services = array('FEDEX_DISTANCE_DEFERRED', 'FEDEX_NEXT_DAY_EARLY_MORNING', 'FEDEX_NEXT_DAY_MID_MORNING', 'FEDEX_NEXT_DAY_AFTERNOON', 'FEDEX_NEXT_DAY_END_OF_DAY', 'FEDEX_NEXT_DAY_FREIGHT' );
		
		$shipper_country = $this->origin_country;
		$shipping_country = !empty($dest_country) ? $dest_country : $order->shipping_country;
		
		if( 'GB'==$shipper_country && 'GB'==$shipping_country && in_array($service_code,$uk_domestic_services) ){
			return true;
		}
		$exception_list = array('FEDEX_GROUND','FEDEX_FREIGHT_ECONOMY','FEDEX_FREIGHT_PRIORITY');
		$exception_country = array('US','CA');
		if(in_array($shipping_country,$exception_country) && in_array($service_code,$exception_list)){
			return true;
		}
		
		if( $shipping_country == $this->origin_country ){
			return strpos($service_code, 'INTERNATIONAL_') === false;
		}
		else{
			return  strpos($service_code, 'INTERNATIONAL_') !== false;
		}
		return false; 
	}

	private function is_domestic($order){

		if ( !isset($this->origin_country) ) {

			$settings 					= get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
			$origin_country_state 		= isset( $settings['origin_country'] ) ? $settings['origin_country'] : '';

			if ( strstr( $origin_country_state, ':' ) ) :
				$origin_country_state_array 	= explode(':',$origin_country_state);
				$origin_country 		= current($origin_country_state_array);
			else :
				$origin_country = $origin_country_state;
			endif;

			$this->origin_country  	= apply_filters( 'woocommerce_fedex_origin_country_code', $origin_country );
		}
		
		return $this->origin_country == $order->shipping_country;
	}

	private function wf_get_shipping_service($order,$retrive_from_order = false, $shipment_id=false, $package_group_key=false )
	{
		if($retrive_from_order == true){
			$service_code = get_post_meta($order->id, 'wf_woo_fedex_service_code'.$shipment_id, true);
			if(!empty($service_code)) return $service_code;
		}
		
		if(!empty($_GET['service'])){			
			$service_arr	=   json_decode(stripslashes(html_entity_decode($_GET["service"])));
			// If all the generated packages has been removed from order then services will be empty
			if( ! empty($service_arr[0]) ) {
				return $service_arr[0];
			}
			elseif( ! $this->debug ){
				$order_id = ( WC()->version < '3.0' ) ? $order->ID : $order->get_id();
				if ( class_exists( 'WC_Admin_Meta_Boxes' ) ) {
					WC_Admin_Meta_Boxes::add_error( __( "FedEx services missing. Label generation has been terminated.", "wf-shipping-fedex" ) );
 				}
				wp_redirect( admin_url( '/post.php?post='.$order_id.'&action=edit') );
				exit();
			}
			else{
				$this->print_debug_message( __( "FedEx services missing. Label generation has been terminated.", "wf-shipping-fedex" ) );
				exit();
			}
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

		if( $this->is_domestic($order) ){
			if( !empty($this->settings['default_dom_service']) ){
				return $this->settings['default_dom_service'];
			}
		}else{
			if( !empty($this->settings['default_int_service']) ){
				return $this->settings['default_int_service'];
			}
		}
	}
	
	public function wf_create_shipment( $order, $service_arr = array() ){
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		
		$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();

		if( !empty($service_arr) && !empty($service_arr[0]) ){
			$serviceCode 	= $service_arr[0];
		}else{
			$serviceCode 	= $this->wf_get_shipping_service($order,false);
		}

		if( empty($serviceCode) ){
			wf_admin_notice::add_notice( __("Not found any Service Code for the Order #".$order->get_id(), 'wf-shipping-fedex') );
			return false;
		}

		$woofedexwrapper->print_label($order,$serviceCode,$order->id);
	}
	
	public function wf_create_return_shipment( $shipment_id, $order_id ){		
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		
		$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();
		$serviceCode = $this->wf_get_shipping_service( $this->wf_load_order($order_id),false);
		$woofedexwrapper->print_return_label( $shipment_id, $order_id, $serviceCode  );		
	}

	public function wf_add_fedex_metabox(){
		// Check whether to show meta box on order is enabled or not
		if( $this->display_fedex_meta_box_on_order == 'no' ) {
			return;
		}
		global $post;
		if (!$post) {
			return;
		}
		if ( in_array( $post->post_type, array('shop_order') )) {
			$order = $this->wf_load_order($post->ID);
			if (!$order) 
				return;
			
			add_meta_box('wf_fedex_metabox', __('FedEx', 'wf-shipping-fedex'), array($this, 'wf_fedex_metabox_content'), 'shop_order', 'advanced', 'default');
		}
	}

	public function wf_fedex_generate_packages(){
		if( !$this->wf_user_permission(isset($_GET['auto_generate'])?$_GET['auto_generate']:null)) {
			echo "You don't have admin privileges to view this page.";
			exit;
		}
		
		$post_id	=	base64_decode($_GET['wf_fedex_generate_packages']);
		$order = $this->wf_load_order( $post_id );
		if ( !$order ) return;

		if( !$this->debug && $this->settings['automate_label_generation'] == 'yes' && !isset( $_GET['wf_fedex_generate_packages'] ) ) {
			
			// Add transient to check for duplicate label generation
			$transient			 	= 'ph_fedex_auto_shipment' . md5( $order->get_id() );
			$processed_order		= get_transient( $transient );

			// If requested order is already processed, return.
			if( $processed_order ) {
				return;
			}

			// Set transient for 1 min to avoid duplicate label generation
			set_transient( $transient, $order->get_id(), 60 );
		}
		
		$this->xa_generate_package( $order );
		
		if( ( ! $this->debug ) ||  ( isset( $_GET['wf_fedex_generate_packages'] ) ) || ( $this->settings['automate_label_generation'] == 'no' ) ) {
			wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit#wf_fedex_metabox') );
		}
		exit;
	}

	private function xa_generate_package( $order ){
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';

		$woofedexwrapper	= new wf_fedex_woocommerce_shipping_admin_helper();
		$packages		= $woofedexwrapper->wf_get_package_from_order($order);
		
		if( isset( $packages['error'] ) ){
			wf_admin_notice::add_notice( sprintf( __( $packages['error'], 'wf-shipping-fedex' ) ), 'error' );
			return;
		}

		foreach($packages as $package){
			$package = apply_filters( 'wf_customize_package_on_generate_label', $package, $order->get_id() );		//Filter to customize the package
			$package_data[] = $woofedexwrapper->get_fedex_packages($package);
		}

		if( isset( $package_data ) && !empty( $package_data ) ){

			foreach( $package_data as $package_group_key =>	$package_group ){

				if( !empty( $package_group ) && is_array( $package_group ) ){

					foreach( $package_group as $stored_package_key => $stored_package ){

						delete_post_meta( $order->get_id(), 'ph_get_no_of_packages'.$stored_package['GroupNumber'] );
					}
				}
			}
		}	

		update_post_meta( $order->get_id(), '_wf_fedex_stored_packages', $package_data );

		if ( !isset( $_GET['wf_fedex_generate_packages'] ) ) {	
			//For automatic label generation 
			do_action( 'wf_after_package_generation', $order->get_id(), $package_data );
		}
	}

	public function wf_fedex_metabox_content(){
		global $post;
		
		if (!$post) {
			return;
		}

		$order = $this->wf_load_order($post->ID);
		if (!$order) 
			return;			

		$shipmentIds = get_post_meta($order->id, 'wf_woo_fedex_shipmentId', false);
		// Some Customers Site wont allow adding duplicate Meta Keys in DB, Adding new meta key with custom build Shipment Id Array
		$shipment_ids 		= get_post_meta($order->id, 'ph_woo_fedex_shipmentIds', true);

		if( is_array($shipmentIds) && is_array($shipment_ids) ){
			$shipmentIds  		= array_unique(array_merge($shipmentIds,$shipment_ids));
		}

		$shipment_void_ids = get_post_meta($order->id, 'wf_woo_fedex_shipment_void', false);
		
		$shipmentErrorMessage = get_post_meta($order->id, 'wf_woo_fedex_shipmentErrorMessage',true);
		$shipment_void_error_message = get_post_meta($order->id, 'wf_woo_fedex_shipment_void_errormessage',true);
		
		//Only Display error message if the process is not complete. If the Invoice link available then Error Message is unnecessary
		if(!empty($shipmentErrorMessage))
		{
			echo '<div class="error"><p>' . sprintf( __( 'FedEx Create Shipment Error:<br/>%s', 'wf-shipping-fedex' ), $shipmentErrorMessage) . '</p></div>';
		}

		if(!empty($shipment_void_error_message)){
			echo '<div class="error"><p>' . sprintf( __( 'Void Shipment Error:%s', 'wf-shipping-fedex' ), $shipment_void_error_message) . '</p></div>';
		}			
		echo '<ul>';
		if (!empty($shipmentIds)) {
			foreach($shipmentIds as $shipmentId) {
				$selected_sevice = $this->wf_get_shipping_service($order,true,$shipmentId);	
				if(!empty($selected_sevice))
					echo "<li>Shipping Service: <strong>$selected_sevice</strong></li>";		
				
				?><li><strong><?php _e( 'Shipment Tracking ID: ' ); ?></strong><a href="https://www.fedex.com/fedextrack/no-results-found?trknbr=<?php echo $shipmentId ?>" target="_blank"><?php echo $shipmentId ?></a><?php

				$usps_trackingid = get_post_meta($order->id, 'wf_woo_fedex_usps_trackingid_'.$shipmentId, true);
				if(!empty($usps_trackingid)){
					echo "<br><strong>USPS Tracking #:</strong> ".$usps_trackingid;
				}
				if((is_array($shipment_void_ids) && in_array($shipmentId,$shipment_void_ids))){
					echo "<br> This shipment $shipmentId is terminated.";
				}
				$additional_labels = get_post_meta($post->ID, 'wf_fedex_additional_label_'.$shipmentId, true);
				if( ! empty($additional_labels) ) {
					$additional_label_tracking_number = get_post_meta( $post->ID, '_ph_woo_fedex_additional_tracking_number_'.$shipmentId, true );
					if( ! empty($additional_label_tracking_number ) )	echo "<li> Additional Tracking Number #: $additional_label_tracking_number</li>";
				}
				echo '<hr>';
				$packageDetailForTheshipment = get_post_meta($order->id, 'wf_woo_fedex_packageDetails_'.$shipmentId, true);
				if(!empty($packageDetailForTheshipment)){
					foreach($packageDetailForTheshipment as $dimentionKey => $dimentionValue){
						if($dimentionValue){
							echo '<strong>' . $dimentionKey . ': ' . '</strong>' . $dimentionValue ;
							echo '<br />';
						}						
					}
					echo '<hr>';
				}
				$shipping_label = get_post_meta($post->ID, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
				if(!empty($shipping_label)){
					$download_url = admin_url('/post.php?wf_fedex_viewlabel='.base64_encode($shipmentId.'|'.$post->ID));?>
					<a class="button tips" href="<?php echo $download_url; ?>" target="_blank" data-tip="<?php _e('Print Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Label', 'wf-shipping-fedex'); ?></a>
					<?php 
				}
				
				if(!empty($additional_labels) && is_array($additional_labels)){
					foreach($additional_labels as $additional_key => $additional_label){
						$download_add_label_url = admin_url('/post.php?wf_fedex_additional_label='.base64_encode($shipmentId.'|'.$post->ID.'|'.$additional_key));?>
						<a class="button tips" href="<?php echo $download_add_label_url; ?>" data-tip="<?php _e('Additional Label', 'wf-shipping-fedex'); ?>"><?php _e('Additional Label', 'wf-shipping-fedex'); ?></a>
						<?php
					}		
				}
				if((!is_array($shipment_void_ids) || !in_array($shipmentId,$shipment_void_ids))){
					$void_shipment_link = admin_url('/post.php?wf_fedex_void_shipment=' . base64_encode($shipmentId.'||'.$post->ID));?>				
					<a class="button tips" href="<?php echo $void_shipment_link; ?>" data-tip="<?php _e('Void Shipment', 'wf-shipping-fedex'); ?>"><?php _e('Void Shipment', 'wf-shipping-fedex'); ?></a>
					<?php 
				}
				$shipping_return_label = get_post_meta($post->ID, 'wf_woo_fedex_returnLabel_'.$shipmentId, true);
				$return_shipment_id = get_post_meta($post->ID, 'wf_woo_fedex_returnShipmetId', true);
				echo '<hr>';
				if(!empty($shipping_return_label)){
					$download_url = admin_url('/post.php?wf_fedex_viewReturnlabel='.base64_encode($shipmentId.'|'.$post->ID) );
					
					?><li><strong><?php _e( 'Return Shipment Tracking ID: ' ); ?></strong><a href="https://www.fedex.com/fedextrack/no-results-found?trknbr=<?php echo $return_shipment_id ?>" target="_blank"><?php echo $return_shipment_id ?></a>

					<li><a class="button tips" href="<?php echo $download_url; ?>" target="_blank" data-tip="<?php _e('Print Return Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Return Label', 'wf-shipping-fedex'); ?></a></li>
					<?php 
				}else{
					$selected_sevice = $this->wf_get_shipping_service($order);	
					echo '<select class="fedex_return_service select">';
					foreach($this->custom_services as $service_code => $service){
						if($service['enabled'] == true ){
							echo '<option value="'.$service_code.'" ' . selected($selected_sevice,$service_code) . ' >'.$service_code.'</option>';
						}	
					}
					echo'</select>'?>
					<a class="button button-primary fedex_create_return_shipment tips" href="<?php echo admin_url( '/post.php?wf_create_return_label='.base64_encode($shipmentId.'|'.$post->ID) ); ?>" data-tip="<?php _e( 'Generate return label', 'wf-shipping-fedex' ); ?>"><?php _e( 'Generate return label', 'wf-shipping-fedex' ); ?></a><?php
				}
				echo '<hr style="border-color:#0074a2"></li>';
			} 
			if($shipment_void_error_message){
				$client_reset_link  = admin_url('/post.php?ph_client_reset_link=' . base64_encode($post->ID));
			    $void_shipments 	= get_post_meta($post->ID, 'ph_woo_fedex_shipment_client_reset',false);

				if($this->client_side_reset && $void_shipments ) { 

					echo '<p>If you have already cancelled this shipment by calling FedEx customer care, and you would like to create shipment again then click.</p>';?>				
					<a class="button button-primary tips" id="fedex_client_side_reset" href="<?php echo $client_reset_link; ?>" data-tip="<?php _e('Clear Data', 'wf-shipping-fedex'); ?>" OnClick="return confirm('The shipping labels and the tracking details for all the packages will be removed from the Order page.                 Are you sure you want to continue?')";  ><?php _e('Clear Data', 'wf-shipping-fedex'); ?></a><?php 
					echo '<p style="color:red"><strong>Note: </strong>Previous shipment details and label will be removed from Order page.</p>';	

				} 	  
			}else if( (count($shipmentIds) == count($shipment_void_ids ) )){

				$clear_history_link = admin_url('/post.php?wf_clear_history=' . base64_encode($post->ID));?>				
					<a class="button button-primary tips"; href="<?php echo $clear_history_link; ?>"  data-tip="<?php _e('Clear History', 'wf-shipping-fedex'); ?>"><?php _e('Clear History', 'wf-shipping-fedex'); ?></a><?php 
			}				
		}
		else {
			$stored_packages	=	get_post_meta( $post->ID, '_wf_fedex_stored_packages', true );
			if(empty($stored_packages)	&&	!is_array($stored_packages)){
				echo '<strong>'.__( 'Auto generate packages.', 'wf-shipping-fedex' ).'</strong></br>';
				?>
				<a class="button button-primary tips fedex_generate_packages" href="<?php echo admin_url( '/post.php?wf_fedex_generate_packages='.base64_encode($post->ID) ); ?>" data-tip="<?php _e( 'Regenerate all the packages', 'wf-shipping-fedex' ); ?>"><?php _e( 'Generate Packages', 'wf-shipping-fedex' ); ?></a><hr style="border-color:#0074a2">
				<?php
			}else{
				$generate_url = admin_url('/post.php?wf_fedex_createshipment='.$post->ID);
 
				echo '<li>';
					echo '<h4>'.__( 'Package(s)' , 'wf-shipping-fedex').': </h4>';
					echo '<table id="wf_fedex_package_list" class="wf-shipment-package-table">';					
						echo '<tr>';
						   echo '<th style="width: 10%;padding:8px" id="ph_fedex_packages_no" class="ph_fedex_packages_no">'.__('No. of Packages</br>(Max. 25)', 'wf-shipping-fedex').'</th>';
						if (isset($stored_packages[0]) && isset($stored_packages[0][0]) && isset($stored_packages[0][0]['boxName'])) {
							echo '<th style="width: 17%;padding:8px" id="ph_fedex_manual_box_name" class="ph_fedex_manual_box_name">'.__('Box Name', 'wf-shipping-fedex').'</th>';
						}
							echo '<th>'.__('Wt.', 'wf-shipping-fedex').'</br>('.$this->weight_unit.')</th>';
							echo '<th>'.__('L', 'wf-shipping-fedex').'</br>('.$this->dimension_unit.')</th>';
							echo '<th>'.__('W', 'wf-shipping-fedex').'</br>('.$this->dimension_unit.')</th>';
							echo '<th>'.__('H', 'wf-shipping-fedex').'</br>('.$this->dimension_unit.')</th>';
							echo '<th>'.__('Insur.', 'wf-shipping-fedex').'</th>';
							echo '<th>';
								echo __('Select Service', 'wf-shipping-fedex');
								echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'Select the FedEx service.', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" />';
							echo '</th>';
							echo '<th>';
								_e('Remove', 'wf-shipping-fedex');
								echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'Remove FedEx generated packages (Beta Version).', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" />';
							echo '</th>';
						echo '</tr>';
						
						//case of multiple shipping address
						$multiship = get_post_meta( $order->id, '_multiple_shipping', true);
						if( $multiship ){
							$multi_ship_packages  = get_post_meta($post->ID, '_wcms_packages', true);
						}
						
						foreach($stored_packages as $package_group_key	=>	$package_group){
							if( !is_array($package_group) ){
								$package_group = array();
							}
							foreach($package_group as $stored_package_key	=>	$stored_package){

								$order_no = $post->ID;
								$nos_of_packages  = 1;

								if ( !empty( $order_no )) {
									
									$nos_of_packages  = get_post_meta( $order_no, 'ph_get_no_of_packages'.$stored_package['GroupNumber'], true );
								}

								//PDS-179
								$temp_signature 		= isset($stored_package['signature_option']) && !empty($stored_package['signature_option']) ?
								$stored_package['signature_option'] : 0;
								$this->signature_temp 	= (isset($this->signature_temp) && !empty($this->signature_temp)) && $this->signature_temp > $temp_signature ? $this->signature_temp : $temp_signature;
								$dimensions	=	$this->get_dimension_from_package($stored_package);
								$insurance_amount = ! empty($stored_package['InsuredValue']['Amount']) ? $stored_package['InsuredValue']['Amount'] : null;
								
								$insurance_style = ( $this->settings['insure_contents'] == 'yes' ) ? null : 'style="visibility:hidden"';
								if(is_array($dimensions)){
									?>
									<tr>
										<td><select id="fedex_packages_no" name="fedex_packages_no[]" class="ph_fedex_packages_no" />
											<?php 
											$allowed_no_packages 	= array(
												'1'	   				=> __( '1', 'wf-shipping-fedex' ),
												'2'	   				=> __( '2', 'wf-shipping-fedex' ),
												'3'	   				=> __( '3', 'wf-shipping-fedex' ),
												'4'	   				=> __( '4', 'wf-shipping-fedex' ),
												'5'	   				=> __( '5', 'wf-shipping-fedex' ),
												'6'	   				=> __( '6', 'wf-shipping-fedex' ),
												'7'	   				=> __( '7', 'wf-shipping-fedex' ),
												'8'	   				=> __( '8', 'wf-shipping-fedex' ),
												'9'	   				=> __( '9', 'wf-shipping-fedex' ),
												'10'	   			=> __( '10', 'wf-shipping-fedex' ),
												'11'	   			=> __( '11', 'wf-shipping-fedex' ),
												'12'	   			=> __( '12', 'wf-shipping-fedex' ),
												'13'	   			=> __( '13', 'wf-shipping-fedex' ),
												'14'	   			=> __( '14', 'wf-shipping-fedex' ),
												'15'	   			=> __( '15', 'wf-shipping-fedex' ),
												'16'	   			=> __( '16', 'wf-shipping-fedex' ),
												'17'	   			=> __( '17', 'wf-shipping-fedex' ),
												'18'	   			=> __( '18', 'wf-shipping-fedex' ),
												'19'	   			=> __( '19', 'wf-shipping-fedex' ),
												'20'	   			=> __( '20', 'wf-shipping-fedex' ),
												'21'	   			=> __( '21', 'wf-shipping-fedex' ),
												'22'	   			=> __( '22', 'wf-shipping-fedex' ),
												'23'	   			=> __( '23', 'wf-shipping-fedex' ),
												'24'	   			=> __( '24', 'wf-shipping-fedex' ),
												'25'	   			=> __( '25', 'wf-shipping-fedex' ),
											);
											foreach ($allowed_no_packages as $key => $value) {
												if($key == $nos_of_packages){
													echo "<option value='".$key."' selected >".$value."</option>";
												} else {
													echo "<option value='".$key."'>".$value."</option>";
												}
											}
											?>
										</select></td>
									<?php
									   if (isset($stored_package['boxName'])) {

										$box_name 	= isset($stored_package['boxName']) && !empty($stored_package['boxName'])? $stored_package['boxName']: "Unpacked Product";																 
									?>
										<td><input type="text"  style="margin:7px;" id="phFedexManualBoxName" name="fedex_manual_box_name[]" class="ph_fedex_manual_box_name" size="10" value="<?php echo $box_name;?>" readonly /></td>

									<?php } ?>
										<td><input type="text" id="fedex_manual_weight" name="fedex_manual_weight[]" size="2" value="<?php echo $dimensions['Weight'];?>" /></td>	 
										<td><input type="text" id="fedex_manual_length" name="fedex_manual_length[]" size="2" value="<?php echo $dimensions['Length'];?>" /></td>
										<td><input type="text" id="fedex_manual_width" name="fedex_manual_width[]" size="2" value="<?php echo $dimensions['Width'];?>" /></td>
										<td><input type="text" id="fedex_manual_height" name="fedex_manual_height[]" size="2" value="<?php echo $dimensions['Height'];?>" /></td>
										<td><input <?php echo $insurance_style; ?> type="text" id="fedex_manual_insurance" name="fedex_manual_insurance[]" size="2" value="<?php echo $insurance_amount;?>" /></td>
										<td><?php
											$package_dest_country = ( isset( $multi_ship_packages[$package_group_key]['destination']['country'] ) ) ? $multi_ship_packages[$package_group_key]['destination']['country'] : false;
											// $stored_package['service'] is setted by Multivendor Plugin
											if( ! empty($stored_package['service']) ) {
												$selected_sevice = $stored_package['service'];
											}
											elseif( isset( $multi_ship_packages[$package_group_key] ) ) {
												$selected_sevice = $this->wf_get_shipping_service($order,false, false, $package_group_key);
											}
											else{
												$selected_sevice = $this->wf_get_shipping_service($order);
											}
											echo '<select class="fedex_manual_service select">';
											if($this->xa_show_all_shipping_methods==true)
											{
												$services = include('data-wf-service-codes.php');
												foreach($services as $service_code => $service)
												{
													echo '<option value="'.$service_code.'" ' . selected($selected_sevice,$service_code) . ' >'.$service.'</option>';
												
												}
											}
											else
											{   
												foreach($this->custom_services as $service_code => $service)
												{
												if($service['enabled'] == true && $this->wf_is_service_valid_for_country($order,$service_code, $package_dest_country) == true)
												{
													echo '<option value="'.$service_code.'" ' . selected($selected_sevice,$service_code) . ' >'.$service_code.'</option>';
												}
												}
											}?>
										</td>
										<td><a class="wf_fedex_package_line_remove" id="<?php echo $package_group_key.'_'.$stored_package_key; ?>">&#x26D4;</a></td>
										<td>&nbsp;</td>
									</tr>
									<?php
								}
							}
						}
					echo '</table>';
					echo '<a class="button wf-action-button wf-add-button" style="font-size: 12px; margin-left: 4px; margin-right: 5px; margin-top: 15px;" id="wf_fedex_add_package">Add Package</a>';
				?>
				<a style="margin: 4px; margin-right: 5px; margin-top: 15px;" class="button tips fedex_generate_packages" href="<?php echo admin_url( '/post.php?wf_fedex_generate_packages='.base64_encode($post->ID) ); ?>" data-tip="<?php _e( 'Regenerate all the packages', 'wf-shipping-fedex' ); ?>"><?php _e( 'Generate Packages', 'wf-shipping-fedex' ); ?></a><li/>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						
						
						jQuery('#wf_fedex_add_package').on("click", function(){
							var new_row = '<tr>';
							new_row		+= '<td>';
						    new_row		+= '	<select id="fedex_packages_no" name="fedex_packages_no[]" class="ph_fedex_packages_no">';
						    <?php 
						    $allowed_no_packages 	= array(
							'1'	   				=> __( '1', 'wf-shipping-fedex' ),
							'2'	   				=> __( '2', 'wf-shipping-fedex' ),
							'3'	   				=> __( '3', 'wf-shipping-fedex' ),
							'4'	   				=> __( '4', 'wf-shipping-fedex' ),
							'5'	   				=> __( '5', 'wf-shipping-fedex' ),
							'6'	   				=> __( '6', 'wf-shipping-fedex' ),
							'7'	   				=> __( '7', 'wf-shipping-fedex' ),
							'8'	   				=> __( '8', 'wf-shipping-fedex' ),
							'9'	   				=> __( '9', 'wf-shipping-fedex' ),
							'10'	   			=> __( '10', 'wf-shipping-fedex' ),
							'11'	   			=> __( '11', 'wf-shipping-fedex' ),
							'12'	   			=> __( '12', 'wf-shipping-fedex' ),
							'13'	   			=> __( '13', 'wf-shipping-fedex' ),
							'14'	   			=> __( '14', 'wf-shipping-fedex' ),
							'15'	   			=> __( '15', 'wf-shipping-fedex' ),
							'16'	   			=> __( '16', 'wf-shipping-fedex' ),
							'17'	   			=> __( '17', 'wf-shipping-fedex' ),
							'18'	   			=> __( '18', 'wf-shipping-fedex' ),
							'19'	   			=> __( '19', 'wf-shipping-fedex' ),
							'20'	   			=> __( '20', 'wf-shipping-fedex' ),
							'21'	   			=> __( '21', 'wf-shipping-fedex' ),
							'22'	   			=> __( '22', 'wf-shipping-fedex' ),
							'23'	   			=> __( '23', 'wf-shipping-fedex' ),
							'24'	   			=> __( '24', 'wf-shipping-fedex' ),
							'25'	   			=> __( '25', 'wf-shipping-fedex' ),
						    );
						    foreach($allowed_no_packages as $key => $value)
							{?>
								new_row	+=  '<option value="<?php echo $key ?>"><?php echo $value ?></option>';
								<?php
							}
							?>
						    new_row		+= '</td>';

                            if( jQuery('#wf_fedex_package_list .ph_fedex_manual_box_name').length > 0 ) {
	                            new_row 	+= '<td><input type="text"  style="margin:7px;"id="phFedexManualBoxName" class="ph_fedex_manual_box_name" size="10" value="Manual Box" readonly /></td>';
                            }
								new_row 	+= '<td><input type="text" id="fedex_manual_weight" name="fedex_manual_weight[]" size="2" value="0"></td>';
								new_row 	+= '<td><input type="text" id="fedex_manual_length" name="fedex_manual_length[]" size="2" value="0"></td>';								
								new_row 	+= '<td><input type="text" id="fedex_manual_width" name="fedex_manual_width[]" size="2" value="0"></td>';
								new_row 	+= '<td><input type="text" id="fedex_manual_height" name="fedex_manual_height[]" size="2" value="0"></td>';
								new_row 	+= '<td><input type="text" id="fedex_manual_insurance" name="fedex_manual_insurance[]" size="2" value=""></td>';
								new_row		+= '<td>';
								new_row		+= '	<select class="fedex_manual_service select">';
								<?php
								if($this->xa_show_all_shipping_methods==true)
											{
												$services = include('data-wf-service-codes.php');
												foreach($services as $service_code => $service)
												{?>
												new_row	+=  '<option value="<?php echo $service_code ?>"><?php echo $service ?></option>';
												<?php
												}
											}
											else
											{
												if( ! isset($package_dest_country) ) {
													$package_dest_country = '';
												}
											   foreach($this->custom_services as $service_code => $service)
												   {
													if($service['enabled'] == true && $this->wf_is_service_valid_for_country($order,$service_code, $package_dest_country) == true)
													{
												?>
													new_row		+= '<option value="<?php echo $service_code?>"><?php echo $service_code ?></option>';
												<?php
													}
												}
											} ?>
								new_row		+= '</td>';
								new_row 	+= '<td><a class="wf_fedex_package_line_remove">&#x26D4;</a></td>';
							new_row 	+= '</tr>';
							
							jQuery('#wf_fedex_package_list tr:last').after(new_row);
						});
						
						jQuery(document).on('click', '.wf_fedex_package_line_remove', function(){
							jQuery(this).closest('tr').remove();
						});
					});
				</script><?php
				
				// Rates on order page
				$generate_packages_rates = get_post_meta( $_GET['post'], 'wf_fedex_generate_packages_rates_response', true );
				echo '<li><table id="wf_fedex_service_select" class="wf-shipment-calculate-cost-table" style="margin-bottom: 10px;margin-top: 15px;box-shadow:.5px .5px 5px lightgrey;">';

					echo '<tr>';
						echo '<th>Select Service</th>';
						echo '<th style="text-align:left;padding:5px; font-size:13px;">'.__('Service Name', 'wf-shipping-fedex').'</th>';
						echo '<th style="text-align:left; font-size:13px;">'.__('Delivery Time', 'wf-shipping-fedex').' </th>';
						echo '<th style="text-align:left;font-size:13px;">'.__('Cost (', 'wf-shipping-fedex').get_woocommerce_currency_symbol().__(')', 'wf-shipping-fedex').' </th>';
					echo '</tr>';
					
					echo '<tr>';
						echo "<td style = 'padding-bottom: 10px; padding-left: 15px; '><input name='wf_fedex_service_choosing_radio' id='wf_fedex_service_choosing_radio' value='wf_fedex_individual_service' type='radio' checked='true'></td>";
						echo "<td colspan = '3' style= 'padding-bottom: 10px; text-align:left;'><b>Choose Shipping Methods</b> - Select this option to choose FedEx services for each package (Shipping rates will be applied accordingly).</td>";
					echo "</tr>";
					
					if( ! empty($generate_packages_rates) ) {
						$wp_date_format = get_option('date_format');
						foreach( $generate_packages_rates as $key => $rates ) {
							$fedex_service = explode( ':', $rates['id']);
							$est_date_style = empty($rates['meta_data']['fedex_delivery_time']) ? "style=visibility:hidden;" : null;
							echo '<tr style="padding:10px;">';
								echo "<td style = 'padding-left: 15px;'><input name='wf_fedex_service_choosing_radio' id='wf_fedex_service_choosing_radio' value='".end($fedex_service)."' type='radio' ></td>";
								echo "<td>".$rates['label']."</td>";
								echo "<td $est_date_style>".date( $wp_date_format, strtotime($rates['meta_data']['fedex_delivery_time']) )."</td>";
								echo "<td>".( ! empty($this->settings['conversion_rate']) ? $this->settings['conversion_rate'] * $rates['cost'] : $rates['cost'] )."</td>";
							echo "</tr>";
						}
					}

				echo '</table></li>';
				//End of Rates on order page
				?>
				<a style="margin-left: 4px; margin-top: 10px; margin-bottom: 10px" class="button tips wf_fedex_generate_packages_rates button-secondary" href="<?php echo admin_url( '/post.php?wf_fedex_generate_packages_rates='.base64_encode($post->ID) ); ?>" data-tip="<?php _e( 'Calculate the Shipping Cost.', 'wf-shipping-fedex' ); ?>"><?php _e( 'Calculate Cost', 'wf-shipping-fedex' ); ?></a>
				<?php

				// If payment method is COD, check COD by default.
				$order_payment_method 	= get_post_meta( $post->ID, '_payment_method', true );
				$cod_checked 			= $order_payment_method == 'cod' ? 'checked': '';
				$order_data 			= new WC_Order($post->ID);
				$items_cost 			= $order_data->get_subtotal();
				$order_currency 		= $order_data->get_currency();
				$b13_post_currency 		= "CAD";
				$woocommerce_currency_conversion_rate = get_option('woocommerce_multicurrency_rates');
				$shipping_country 		= $order_data->get_shipping_country();
				$sat_checked 			= isset($this->settings['saturday_delivery_label']) && !empty($this->settings['saturday_delivery_label']) && $this->settings['saturday_delivery_label'] == 'yes' ? 'checked': '';

				echo '<li><table id="ph_fedex_order_edit_page_options" class="ph-order-edit-options-table" style="margin-bottom: 10px;margin-top: 10px;box-shadow:.5px .5px 5px lightgrey;">';
				echo '<tr><th colspan="2"; style="text-align:center;padding:5px; font-size:13px; ">'.__('FedEx Special Services', 'wf-shipping-fedex').'</th>';

				echo '<tr><td>'. __('Collect On Delivery', 'wf-shipping-fedex') . '</td>';
				echo '<td><label for="wf_fedex_cod"><input type="checkbox" style="" id="wf_fedex_cod" '.$cod_checked.' name="wf_fedex_cod" class=""></label></td></tr>';

				echo '<tr><td>'. __('Saturday Delivery', 'wf-shipping-fedex');
				echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'This option will enable Saturday Delivery Shipping Services.', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" /></td>';
				echo '<td><label for="wf_fedex_sat_delivery"><input type="checkbox" style="" id="wf_fedex_sat_delivery" '.$sat_checked.' name="wf_fedex_sat_delivery" class=""></label></td></tr>';

				if ( $this->origin_country != $shipping_country ) {

					$etd_checked 	= $this->etd_label ? 'checked': '';
					echo '<tr><td>'. __('ETD - Electronic Trade Documents', 'wf-shipping-fedex');
					echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'On enabling this option the shipment details will be sent electronically and ETD will be printed in the Shipping Label', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" /></td>';
					echo '<td><label for="ph_fedex_etd"><input type="checkbox" '.$etd_checked.' id="ph_fedex_etd" name="ph_fedex_etd"></label></td></tr>';
				}

				//PDS-179
				$order_id               = $order->id;
				$signature_meta 		= get_post_meta( $order_id, 'ph_fedex_signature_option_meta' );
                $this->signature_temp 	= isset($this->signature_temp) && !empty($this->signature_temp) ? $this->signature_temp : 0;
				$this->signature_temp 	= isset($signature_meta[0]) ? $signature_meta[0] : $this->signature_temp;
				$this->signature 		= isset($this->prioritizedSignatureOption[$this->signature_temp]) && !empty($this->prioritizedSignatureOption[$this->signature_temp]) ? $this->prioritizedSignatureOption[$this->signature_temp] : '';
				$signature_options 		= array(
					''        				=> __( 'Select Anyone', 'wf-shipping-fedex' ),
					'ADULT'	   				=> __( 'Adult', 'wf-shipping-fedex' ),
					'DIRECT'	  			=> __( 'Direct', 'wf-shipping-fedex' ),
					'INDIRECT'	  			=> __( 'Indirect', 'wf-shipping-fedex' ),
					'SERVICE_DEFAULT'	  	=> __( 'Service Default', 'wf-shipping-fedex' ),
					'NO_SIGNATURE_REQUIRED' => __( 'No Signature Required', 'wf-shipping-fedex' ),
					
				);

				_e('<tr><td> Delivery Signature ', 'wf-shipping-fedex');
				echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'FedEx Web Services Selects the appropriate signature option for your shipping service.', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16" /></td>';
				echo '<td><select id="ph_fedex_signature_option" class="ph_fedex_signature_option" style= "width:50%">';

				foreach ($signature_options as $key => $value) {

					if($key == $this->signature){
						echo "<option value='".$key."' selected>".$value."</option>";
					} else {
						echo "<option value='".$key."'>".$value."</option>";
					}
				}
				echo '</select></td></tr>';
		
				if($order_currency != $b13_post_currency && !empty($woocommerce_currency_conversion_rate)){

					$b13_currency_rate = $woocommerce_currency_conversion_rate[$b13_post_currency];
					$order_currency_rate = $woocommerce_currency_conversion_rate[$order_currency];

					$conversion_rate = $b13_currency_rate / $order_currency_rate;
					$items_cost *= $conversion_rate;
				}
				
				if( $this->origin_country === 'CA' && ( $items_cost >= 2000 &&	 ($shipping_country != 'US' && $shipping_country != 'CA' &&$shipping_country != 'PR' && $shipping_country != 'VI'))) {
					$export_declaration_required = 1;
					$export_compliance = get_post_meta($post->ID, '_wf_fedex_export_compliance',true);
				?>
						<tr><td><?php echo __( 'B13A Authentication Code Number', 'wf-shipping-fedex' ); echo '<img class="help_tip" style="float:none;" data-tip="'.__( 'B13A Export compliance for shippment from Canada', 'wf-shipping-fedex' ).'" src="'.WC()->plugin_url().'/assets/images/help.png" height="16" width="16"/>';?> </td>
						<td><input type="text" name="wf_fedex_compliance" value="<?php echo $export_compliance;?>" id="wf_fedex_compliance" style="width:50%"></li></td></tr>
				<?php
				}
				?><?php

				////Home delivery premium
				if ( $this->home_delivery_premium && $this->home_delivery_premium_type === 'DATE_CERTAIN') {

					$date = date("Y-m-d", strtotime("+2 days"));

					echo '<tr><td>'. __('Home Delivery Premium - Date Certain', 'wf-shipping-fedex').'</td>';
					echo "<td><input type='date' min='".$date."' id='ph_fedex_home_delivery_premium_date' name='ph_fedex_home_delivery_premium_date' class='ph_fedex_home_delivery_premium_date' size='16' style='width:50%' value='' /></td></tr>";
				}
				echo '</table></li>';
				?>
				
				<li>
					<a style="margin: 4px; margin-top: 10px; margin-bottom: 10px" class="button button-primary tips onclickdisable fedex_create_shipment" href="<?php echo $generate_url; ?>" data-tip="<?php _e('Create shipment for the packages', 'wf-shipping-fedex'); ?>"><?php _e('Create Shipment', 'wf-shipping-fedex'); ?></a><hr style="border-color:#0074a2">
				</li>
				<?php
			}
			?>
			
			<script type="text/javascript">
				jQuery("a.fedex_generate_packages").on("click", function() {
					location.href = this.href;
				});
				
				// To get rates on order page
				jQuery("a.wf_fedex_generate_packages_rates").one("click", function() {

					jQuery(this).click(function () { return false; });

					var manual_weight_arr 		= jQuery("input[id='fedex_manual_weight']").map(function(){return jQuery(this).val();}).get();
					var manual_height_arr 		= jQuery("input[id='fedex_manual_height']").map(function(){return jQuery(this).val();}).get();
					var manual_width_arr 		= jQuery("input[id='fedex_manual_width']").map(function(){return jQuery(this).val();}).get();
					var manual_length_arr 		= jQuery("input[id='fedex_manual_length']").map(function(){return jQuery(this).val();}).get();
					var manual_insurance_arr 	= jQuery("input[id='fedex_manual_insurance']").map(function(){return jQuery(this).val();}).get();
					var manual_packages_no_arr 	= jQuery("[id='fedex_packages_no']").map(function(){return jQuery(this).val();}).get();
					var manual_packages_no 		= JSON.stringify(manual_packages_no_arr);
					var manual_signature_option	= jQuery('#ph_fedex_signature_option').map(function(){return jQuery(this).val();}).get();
					var order_id                = <?php $order_id = isset($order_id) && !empty($order_id) ? $order_id : $post->ID; echo $order_id  ?>;

					let package_key_arr = [];

					jQuery('.wf_fedex_package_line_remove').each(function () {
						package_key_arr.push(this.id);
					});

					let package_key = JSON.stringify(package_key_arr);

					location.href = this.href + '&weight=' + manual_weight_arr 
					+ '&length=' + manual_length_arr
					+ '&width=' + manual_width_arr
					+ '&height=' + manual_height_arr
					+ '&insurance=' + manual_insurance_arr
					+ '&package_key=' + package_key
					+ '&signature_option=' + manual_signature_option
					+ '&num_of_packages=' + manual_packages_no
					+ '&oid=' + order_id;

					return false;
				});

				jQuery(document).ready( function() {
					jQuery(document).on("change", "#wf_fedex_service_choosing_radio", function(){
						if (jQuery("#wf_fedex_service_choosing_radio:checked").val() == 'wf_fedex_individual_service') {
						jQuery(".fedex_manual_service").prop("disabled", false);
					} else {
						jQuery(".fedex_manual_service").val(jQuery("#wf_fedex_service_choosing_radio:checked").val()).change();
						jQuery(".fedex_manual_service").prop("disabled", true);  
					}
				});
				});
			</script>
			<?php
		}
		echo '</ul>';?>
		<script>
		jQuery("a.fedex_create_return_shipment").one("click", function(e) {
			e.preventDefault();
			service = jQuery(this).prev("select").val();
			var manual_service 		=	'[' + JSON.stringify( service ) + ']';
			location.href = this.href + '&service=' +  manual_service;
		});

		jQuery("a.fedex_create_shipment").on("click", function() {

			jQuery(".error_home_delivery_date").remove();

			if ( jQuery('#ph_fedex_home_delivery_premium_date').is(':visible')){

				var home_delivery_date = jQuery('#ph_fedex_home_delivery_premium_date').val();

				if ( home_delivery_date === "" ) {

					var error_message = '<p class="error_home_delivery_date" style="color:red"><strong>Note: </strong>Please select the date while using Home Delivery Premium - Date Certain option and try again.</p>';
					jQuery('.fedex_create_shipment').before(error_message);
					return false;
				}
			}
			
			// Preventing Multiple Clicks 
			jQuery('.fedex_create_shipment').attr('disabled', 'disabled');
			
			jQuery(this).click(function () { return false; });
			    var manual_packages_no_arr 	= 	jQuery("[id='fedex_packages_no']").map(function(){return jQuery(this).val();}).get();
				var manual_packages_no 		=	JSON.stringify(manual_packages_no_arr);

				var manual_weight_arr 	= 	jQuery("input[id='fedex_manual_weight']").map(function(){return jQuery(this).val();}).get();
				var manual_weight 		=	JSON.stringify(manual_weight_arr);
				
				var manual_height_arr 	= 	jQuery("input[id='fedex_manual_height']").map(function(){return jQuery(this).val();}).get();
				var manual_height 		=	JSON.stringify(manual_height_arr);
				
				var manual_width_arr 	= 	jQuery("input[id='fedex_manual_width']").map(function(){return jQuery(this).val();}).get();
				var manual_width 		=	JSON.stringify(manual_width_arr);
				
				var manual_length_arr 	= 	jQuery("input[id='fedex_manual_length']").map(function(){return jQuery(this).val();}).get();
				var manual_length 		=	JSON.stringify(manual_length_arr);
				
				var manual_insurance_arr 	= 	jQuery("input[id='fedex_manual_insurance']").map(function(){return jQuery(this).val();}).get();
				var manual_insurance 		=	JSON.stringify(manual_insurance_arr);

				var export_compliance_arr = jQuery("input[id='wf_fedex_compliance']").map(function(){return jQuery(this).val();}).get();
				var export_compliance  = JSON.stringify(export_compliance_arr);

				var manual_service_arr		= [];
				var manual_single_service_arr	= [];
				jQuery('.fedex_manual_service').each(function(){
					manual_service_arr.push( jQuery(this).val() );
					manual_single_service_arr.push(jQuery("input[id='wf_fedex_service_choosing_radio']:checked").val());
				});
				var manual_service 		=	JSON.stringify(manual_service_arr);

				if( jQuery("input[id='wf_fedex_service_choosing_radio']:checked").val() != 'wf_fedex_individual_service' ){
					manual_service	= JSON.stringify(manual_single_service_arr);
				}

				let package_key_arr = [];
				jQuery('.wf_fedex_package_line_remove').each(function () {
					package_key_arr.push(this.id);
				});
				let package_key = JSON.stringify(package_key_arr);


			   location.href = this.href + '&weight=' + manual_weight +
				'&length=' + manual_length
				+ '&width=' + manual_width
				+ '&height=' + manual_height
				+ '&num_of_packages=' + manual_packages_no
				+ '&cod=' + jQuery('#wf_fedex_cod').is(':checked')
				+ '&sat_delivery=' + jQuery('#wf_fedex_sat_delivery').is(':checked')
				+ '&signature_option=' + jQuery('#ph_fedex_signature_option').val()
				+ '&home_delivery_date=' + jQuery('#ph_fedex_home_delivery_premium_date').val()
				+ '&etd=' + jQuery('#ph_fedex_etd').is(':checked')
				+ '&insurance=' + manual_insurance
				+ '&service=' + manual_service
				+ '&package_key=' + package_key
				+ '&export_compliance=' + export_compliance;
			return false;			
		});
		</script>
		<?php
	}	
	public function get_dimension_from_package($package){
		$dimensions	=	array(
			'Length'	=>	'',
			'Width'		=>	'',
			'Height'	=>	'',
			'Weight'	=>	'',
		);
		
		if(!is_array($package)){ // Package is not valid
			return $dimensions;
		}
		if(isset($package['Dimensions'])){
			$dimensions['Length']	=	$package['Dimensions']['Length'];
			$dimensions['Width']	=	$package['Dimensions']['Width'];
			$dimensions['Height']	=	$package['Dimensions']['Height'];
			$dimensions['dim_unit']	=	$package['Dimensions']['Units'];
		}
		
		$dimensions['Weight']	=	$package['Weight']['Value'];
		$dimensions['weight_unit']	=	$package['Weight']['Units'];
		return $dimensions;
	}

	/**
	 * To calculate the shipping cost on order page.
	 */
	public function wf_fedex_generate_packages_rates() {

		if( ! $this->wf_user_permission() ) {
			echo "You don't have admin privileges to view this page.";
			exit;
		}
		
		$post_id				= base64_decode($_GET['wf_fedex_generate_packages_rates']);
		$length_arr				= explode(',',$_GET['length']);
		$width_arr				= explode(',',$_GET['width']);
		$height_arr				= explode(',',$_GET['height']);
		$weight_arr				= explode(',',$_GET['weight']);
		$insurance_arr			= explode(',',$_GET['insurance']);
		$get_stored_packages	= get_post_meta( $post_id, '_wf_fedex_stored_packages', true );
		
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_method' ) ) {
			include_once 'class-wf-fedex-woocommerce-shipping.php';
		}

		$shipping_obj		= new wf_fedex_woocommerce_shipping_method();
		$order				= wc_get_order($post_id);
		$shipping_address	= $order->get_address('shipping');

		$address_package	= array(
			'destination'	=> array(
				'address'	=>	$shipping_address['address_1'],
				'address_2'	=>	$shipping_address['address_2'],
				'country'	=>	$shipping_address['country'],
				'state'		=>	$shipping_address['state'],
				'postcode'	=>	$shipping_address['postcode'],
				'city'		=>	$shipping_address['city'],

			),
		);

		// See if address is residential
		if( $this->production )
		{
			$shipping_obj->residential_address_validation( $address_package );

			if ( $shipping_obj->residential == true ) {
				wf_admin_notice::add_notice( sprintf( __( 'Residential Address', 'wf-shipping-fedex' ) ), 'notice' );
			}
		}

		$fedex_requests 	= array();
		$satdelivery_rates 	= array();
		$packages 			= $get_stored_packages;

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
							'Units'		=> $shipping_obj->labelapi_weight_unit,
						),

						'Dimensions'			=> array(),

						'InsuredValue'			=> array(
							'Amount'	=> 0,
							'Currency'	=> $shipping_obj->wf_get_fedex_currency()
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

					$new_manual_package[0][$i] = $package_clone;
				}
			}
			
			if( isset($packages[0]) && is_array($packages[0]) ) {

				$packages[0] = array_merge($packages[0], $new_manual_package[0]);
			} else {
				$packages[0] = $new_manual_package[0];
			}
		}

		foreach ($packages as $package) {

			if (!empty($package) && is_array($package)) {

				$package 	= array_values($package);

				foreach ($package as $key => $value) {

					if ( ! empty($weight_arr[$key] ) ) {

						$package[$key]['Weight']['Value']	= $weight_arr[$key];
						$package[$key]['Weight']['Units']	= $shipping_obj->labelapi_weight_unit;

					} else {

						wf_admin_notice::add_notice( sprintf( __( 'Fedex Rate Request Failed - Weight is missing in the pacakge. Aborting.', 'wf-shipping-fedex' ) ), 'error' );

						// Redirect to same order page
						wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit') );
						//To stay on same order page
						exit;
					}

					if ( ! empty($length_arr[$key]) && ! empty($width_arr[$key]) && ! empty($height_arr[$key]) ) {

						$package[$key]['Dimensions']['Length']	= $length_arr[$key] ;
						$package[$key]['Dimensions']['Width']	= $width_arr[$key] ;
						$package[$key]['Dimensions']['Height']	= $height_arr[$key] ;
						$package[$key]['Dimensions']['Units']	= $shipping_obj->labelapi_dimension_unit;

					} else {
						unset($package[$key]['Dimensions']);
					}

					if ( isset($insurance_arr[$key]) && !empty($insurance_arr[$key]) ) {

						$package[$key]['InsuredValue']['Amount']	= $insurance_arr[$key];
					}
				}
			}

			$package_data[] = $package;
			$fedex_reqs = $shipping_obj->get_fedex_requests( $package, $address_package );

			if(is_array($fedex_reqs)){
				$fedex_requests	=	array_merge($fedex_requests,	$fedex_reqs);
			}

			// SmartPost Request
			if( $this->custom_services['SMART_POST']['enabled'] && $this->settings['smartpost_hub'] && $address_package['destination']['country'] == 'US') {
				$smart_post_request = $shipping_obj->get_fedex_requests( $package, $address_package, 'smartpost');
				$fedex_requests = array_merge( $fedex_requests, $smart_post_request );
			}

			// Freight Request
			if( $this->freight_enabled ) {

				$freight_request 		= $shipping_obj->get_fedex_requests( $package, $address_package, 'freight' );
				$fedex_requests 		= array_merge( $fedex_requests, $freight_request );
			}

			if ( $this->saturday_delivery ) {

				$satdelivery_rates 	= $shipping_obj->get_fedex_requests( $package, $address_package, 'saturday_delivery');
			}
		}
		
		// To save the rate request response
		$_GET['oid'] = $post_id;

		if( $get_stored_packages != $package_data) {

			// Update the packages in database
			update_post_meta( $post_id, '_wf_fedex_stored_packages', $package_data );
		}

		$shipping_obj->run_package_request( $fedex_requests );
		
		if ( $this->saturday_delivery && $shipping_obj->saturday_delivery && !empty($satdelivery_rates) ) {

			$shipping_obj->satday_rates 	= true;

			$shipping_obj->run_package_request( $satdelivery_rates );
		}
		
		// Redirect to same order page
		wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit#wf_fedex_metabox') );
		//To stay on same order page
		exit;
	}

	/**
     * Display fedex action button on orders table
     *
     * @access public
     * @return string
     */
    function fedex_action_column($order) {
		$order = $this->wf_load_order( $order );
        $shipmentIds = get_post_meta($order->id, 'wf_woo_fedex_shipmentId', false);
        // Some Customers Site wont allow adding duplicate Meta Keys in DB, Adding new meta key with custom build Shipment Id Array
		$shipment_ids 		= get_post_meta($order->id, 'ph_woo_fedex_shipmentIds', true);

		if( is_array($shipmentIds) && is_array($shipment_ids) ){
			$shipmentIds  		= array_unique(array_merge($shipmentIds,$shipment_ids));
		}

		if (!empty($shipmentIds) && is_admin() ) {
			foreach($shipmentIds as $shipmentId) {
				$shipping_label = get_post_meta($order->id, 'wf_woo_fedex_shippingLabel_'.$shipmentId, true);
				if(!empty($shipping_label)){
					$download_url = admin_url('/post.php?wf_fedex_viewlabel='.base64_encode($shipmentId.'|'.$order->id));
					printf('<a class="button tips" href="'.$download_url.'" target="_blank" data-tip="'.__('Fedex Print Label', 'wf-shipping-fedex').'"><img src="'.plugin_dir_url(__DIR__).'resources/images/fedex_label.png" style="width:24px;margin:2px;margin-left:0px;"/></a>');
				}
				$additional_labels = get_post_meta($order->id, 'wf_fedex_additional_label_'.$shipmentId, true);
				if(!empty($additional_labels) && is_array($additional_labels)){
					foreach($additional_labels as $additional_key => $additional_label){
						$download_add_label_url = admin_url('/post.php?wf_fedex_additional_label='.base64_encode($shipmentId.'|'.$order->id.'|'.$additional_key));
						printf('<a class="button tips" href="'.$download_add_label_url.'" target="_blank" data-tip="'.__('FedEx Additional Label', 'wf-shipping-fedex').'"><img src="'.plugin_dir_url(__DIR__).'resources/images/fedex_additional.png" style="width:24px;margin:2px;margin-left:0px;"/></a>');
					}		
				}
				
				//Fedex tracking icon
				$shipment_tracking_url = "https://www.fedex.com/fedextrack/no-results-found?trknbr=".$shipmentId;
				printf('<a class="button tips" href="'.$shipment_tracking_url.'" target="_blank" data-tip="'.__('FedEx Tracking-'.$shipmentId, 'wf-shipping-fedex').'"><img src="'.plugin_dir_url(__DIR__).'resources/images/fedex_tracking.png" style="width:24px;margin:2px;margin-left:0px;"/></a>');

			}
		}
    }

    // Automatic Package Generation
	public function ph_fedex_auto_generate_packages( $order_id, $fedex_settings, $minute = '')
	{

		// Check current time (minute) in Thank You Page for Automatic Package generation
		if( !$this->wf_user_permission( $minute ) )
		{
			return;
		}
		
		$post_id 	=	base64_decode( $order_id );
		$order 		=	$this->wf_load_order( $post_id );

		if ( !$order ) return;
		
		$this->xa_generate_package( $order );
		
	}

	// Automatic Label Generation
	public function ph_fedex_auto_create_shipment( $order_id, $fedex_settings, $weight_arr, $length_arr, $width_arr, $height_arr, $service_arr, $minute = '')
	{

		// Check current time (minute) in Thank You Page for Automatic Label generation
		$user_ok = $this->wf_user_permission( $minute );

		if(!$user_ok ){
			return;
		} 			

		$order 	= $this->wf_load_order( $order_id );
		$debug 	= ( $bool = $fedex_settings[ 'debug' ] ) && $bool == 'yes' ? true : false;

		if(isset($fedex_settings['dimension_weight_unit']) && $fedex_settings['dimension_weight_unit'] == 'LBS_IN'){
			
			$labelapi_dimension_unit 	=	'IN';
			$labelapi_weight_unit 		=	'LB';			
		}else{
			
			$labelapi_dimension_unit 	=	'CM';
			$labelapi_weight_unit		=	'KG';		
		}

		if( !$order )
		{
			return;
		}
		
		$shipment_ids = get_post_meta( $order_id, 'wf_woo_fedex_shipmentId', true );

		if( empty($shipment_ids) ) {

			$i 					= 0;
			$stored_packages    = get_post_meta( $order_id, '_wf_fedex_stored_packages', true );
			
			foreach($stored_packages as $package_key => $stored_package){

				foreach($stored_package as $key => $package){

					if( !empty($length_arr[$i]) || !empty($width_arr[$i]) || !empty($height_arr[$i]) ){

						if(isset($length_arr[$i])){
							$stored_packages[$package_key][$key]['Dimensions']['Length'] =  $length_arr[$i];
						}

						if(isset($width_arr[$i])){
							$stored_packages[$package_key][$key]['Dimensions']['Width']  =  $width_arr[$i];
						}

						if(isset($height_arr[$i])){
							$stored_packages[$package_key][$key]['Dimensions']['Height'] = $height_arr[$i];
						}
						$stored_packages[$package_key][$key]['Dimensions']['Units']	= $labelapi_dimension_unit;
					}

					if( !empty($service_arr[$i]) ){
						$stored_packages[$package_key][$key]['service']  			= $service_arr[$i];
					}

					if(isset($weight_arr[$i])){
						$weight =   $weight_arr[$i];
						$stored_packages[$package_key][$key]['Weight']['Value']   =   $weight;
						$stored_packages[$package_key][$key]['Weight']['Units']   =   $labelapi_weight_unit;
					}
					$i++;
				}
			}

			update_post_meta( $order_id, '_wf_fedex_stored_packages', $stored_packages );		

			$this->wf_create_shipment( $order, $service_arr );

		}else{

			if( $debug ) {
				_e( 'Fedex label generation Suspended. Label has been already generated.', 'wf-shipping-fedex' );
			}
			if( class_exists('WC_Admin_Meta_Boxes') ) {
				WC_Admin_Meta_Boxes::add_error( 'Fedex label generation Suspended. Label has been already generated.', 'wf-shipping-fedex' );
			}
		}

	}
}
new wf_fedex_woocommerce_shipping_admin();
?>
