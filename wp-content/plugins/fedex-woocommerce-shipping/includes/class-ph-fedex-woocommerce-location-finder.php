<?php

if( ! class_exists('Ph_Fedex_Woocommerce_Location_Finder') ) {
    class Ph_Fedex_Woocommerce_Location_Finder {

        /**
         * Array of FedEx Hold at Location.
         */
        public static $fedex_locations;

        /**
         * Array of Supported location type for hold at location.
         */
        public static $supported_hold_at_location_type = array(
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
        private function is_soap_available(){
            if( extension_loaded( 'soap' ) ){
                return true;
            }
            return false;
        }
        /**
         * Constructor.
         */
        public function __construct($settings=array()) {
            $this->fedex_settings = $settings;
            $this->api_key          = $this->fedex_settings['api_key'];
            $this->api_pass         = $this->fedex_settings['api_pass'];
            $this->account_number   = $this->fedex_settings['account_number'];
            $this->meter_number     = $this->fedex_settings['meter_number'];
            $this->production       = isset($this->fedex_settings['production']) && !empty($this->fedex_settings['production']) && $this->fedex_settings['production'] == 'yes' ? true: false;
            $this->debug            = $this->fedex_settings['debug'];
            $this->request_hash='';

            $this->soap_method = $this->is_soap_available() ? 'soap' : 'nusoap';
            if( $this->soap_method == 'nusoap' && !class_exists('nusoap_client') ){
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nusoap/lib/nusoap.php';
            }
        }

        /**
         */
        public function init( ) {

            // Add fileds to checkout
            add_filter( 'woocommerce_checkout_fields' , array( $this, 'ph_fedex_add_hold_at_location_to_checkout_fields'),10,1 );
            // Add FedEx hold at locations as option of select field
            add_filter( 'woocommerce_update_order_review_fragments', array($this,'ph_fedex_update_fedex_hold_at_location_select_options'), 90, 1);
            
            // Handle the Fedex Hold at location information in rate request.
            add_filter( 'xa_fedex_rate_request', array($this, 'ph_fedex_update_hold_at_location_data_in_rate_request') ,10,2);
            // Add Hold at Location to the Order
            add_action( 'woocommerce_checkout_update_order_meta' , array($this, 'ph_fedex_update_hold_at_location_in_order_meta' ) ,10, 2 );

            // Add selected hold at location details in Woocommerce cart shipping packages.
            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'ph_fedex_update_hold_at_location_details_in_checkout_package'),10,1 );

            add_filter( 'woocommerce_order_formatted_shipping_address', array($this,'ph_fedex_add_hold_at_location_to_formatted_shipping_address'),10,2 );

            add_filter( 'woocommerce_formatted_address_replacements',  array($this,'ph_fedex_formatted_address_replacements'),10,2  );
            add_filter('woocommerce_localisation_address_formats', array($this,'ph_fedex_address_formats'),10,1);
            
            // Handle the Fedex Hold at location information in shipping request.
            add_filter( 'wf_fedex_request', array($this, 'ph_fedex_update_hold_at_location_data_in_shipment_request') ,10,2);

            // Handle the Fedex Hold at location information in retun shippment request.
            add_filter( 'ph_fedex_return_label_request', array($this, 'ph_fedex_update_hold_at_location_data_in_return_shipment_request') ,10,1);
       
        }

        /**
         * Perform FedEx Locations.
         * 
         */
        public function fedex_on_hold_locations() {
            if( ! empty( $_POST['country']) ) {
                $request = $this->get_request();
            }

            if( ! empty($request) ) {
                $result = $this->get_result($request);
                $this->process_result($result);
            }

        }

        /**
         * Get Fedex Location Search Request.
         */
        public function get_request( $package = array() ){
            
            $hold_at_location_carrier_code = isset($this->fedex_settings['hold_at_location_carrier_code'])  && !empty($this->fedex_settings['hold_at_location_carrier_code']) ? $this->fedex_settings['hold_at_location_carrier_code']: '';

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
                'PostalCode'            => $_POST['s_postcode'],
                'City'                  => $_POST['s_city'],
                'StateOrProvinceCode'   => $_POST['s_state'],
                'CountryCode'           => $_POST['s_country'],
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
            return $request;
        }

        /**
         * Get Fedex Response from FedEx server.
         * @param $request array FedEx Location search request.
         * @return object.
         */
        public function get_result( $request ){
            $this->request_hash=md5(json_encode($request));
            $transient_data = get_transient($this->request_hash);
            if( !empty($transient_data) )
            {
                return json_decode(get_transient( $this->request_hash ));
            }
            $this->location_service_version = '12';          // Search Location WSDL version
            $client = $this->ph_create_soap_client( plugin_dir_path( dirname( __FILE__ ) ) . 'fedex-wsdl/'. ( $this->production ? 'production' : 'test' ) .'/LocationsService_v'.$this->location_service_version.'.wsdl' );
            $result = null;
            if( $this->soap_method == 'nusoap' ){
                $result = $client->call( 'searchLocations', array( 'SearchLocationsRequest' => $request ) );
                $result = json_decode( json_encode( $result ), false );
            }
            else{
                try {
                    $result = $client->searchLocations( $request );
                }
                catch( Exception $e ) {
                    $exception_message = $e->getMessage();
                }
            }
            // Set request and response in session to handle duplicate request
            if( ! empty($result->HighestSeverity) && $result->HighestSeverity == 'SUCCESS' ) {
                // WC()->session->set('ph_fedex_drop_location_request', $request);
                // WC()->session->set('ph_fedex_drop_location_response', $result);
            }
            try{
                $xml_request    = $this->soap_method != 'nusoap' ? $client->__getLastRequest() : $client->request;
                $xml_response   = $this->soap_method != 'nusoap' ? $client->__getLastResponse() : $client->response;
                $this->debug( 'FedEx Hold at Location Request: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_request ), true ) . "</pre>\n" );
                $this->debug( 'FedEx Hold at Location Response: <a href="#" class="debug_reveal">Reveal</a><pre class="debug_info" style="background:#EEE;border:1px solid #DDD;padding:5px;">' . print_r( htmlspecialchars( $xml_response ), true ) . "</pre>\n" );
            }
            catch( Exception $e ){
                $this->debug( __('Something Went Wrong while Fetching Search Loactions Request & Response', 'wf-shipping-fedex') );
            }
            set_transient($this->request_hash,json_encode($result),HOUR_IN_SECONDS);  // store result in transient using request hash, it will expiry within one hour.
            return $result;
        }

        /**
         * Create Soap or NuSoap client .
         * @param $wsdl string Path to WSDL file.
         * @return object.
         */
        public function ph_create_soap_client( $wsdl ){
            if( $this->soap_method=='nusoap' ){
                $soapclient = new nusoap_client( $wsdl, 'wsdl' );
            }else{
                $soapclient = new SoapClient( $wsdl, 
                    array(
                        'trace' =>  true
                    )
                );
            }
            return $soapclient;
        }

        /**
         * Process the response returned from FedEx Server.
         * @param $response object Response returned from FedEx Server.
         */
        public function process_result( $response ){
            if( ! empty($response->AddressToLocationRelationships) ) {
                if( ! empty($response->AddressToLocationRelationships->DistanceAndLocationDetails) && is_array($response->AddressToLocationRelationships->DistanceAndLocationDetails) ) {
                    foreach( $response->AddressToLocationRelationships->DistanceAndLocationDetails as $location ) {
                        $all_locations[$location->LocationDetail->LocationId] = $location;
                    }
                    self::$fedex_locations = $all_locations;
                    WC()->session->set('ph_fedex_hold_at_locations', $all_locations);
                }else{
                    self::$fedex_locations = array();
                    WC()->session->set('ph_fedex_hold_at_locations', array());
                }
            }
        }

        /**
         * Add FedEx Hold at Location field to checkout.
         */
        public function ph_fedex_add_hold_at_location_to_checkout_fields( $fields ) {
            
            $cart = array();

            if( WC() != null && WC()->cart != null) {

                $cart = WC()->cart->cart_contents;
            }

            if( !empty($cart) && is_array($cart)  ) {

                foreach( $cart as $cart_item) {

                    $product = $cart_item['data'];

                    if( !$product->is_virtual() ) {
                        
                        $fields['billing']['ph_fedex_hold_at_locations'] = array(
                            'label'       => __('FedEx Hold at Location', 'wf-shipping-fedex'),
                            'placeholder' => _x('', 'placeholder', 'wf-shipping-fedex'),
                            'required'    => false,
                            'clear'       => false,
                            'type'        => 'select',
                            'class'       => array ('address-field', 'update_totals_on_change' ),
                            'options'     => array(
                                '' => __('Select FedEx Hold at Location', 'wf-shipping-fedex' )
                            )
                        );
                        break;
                    }
                }
            }

            return apply_filters('ph_fedex_checkout_fields', $fields, $this->fedex_settings);
        }
        
        public function ph_fedex_update_fedex_hold_at_location_select_options( $array ){
            $this->fedex_on_hold_locations();

            self::$fedex_locations              =   WC()->session->get('ph_fedex_hold_at_locations');
            $hold_at_location_types             =   apply_filters('ph_fedex_supported_hold_at_location_types', self::$supported_hold_at_location_type);

            parse_str($_POST['post_data'], $data );
            $selected_location_id = isset($data['ph_fedex_hold_at_locations']) ? $data['ph_fedex_hold_at_locations'] : null;
            $locator     = '<select id="ph_fedex_hold_at_locations" name="ph_fedex_hold_at_locations" class="select" data-select2="1">';
            $locator    .=  "<option value=''>". __('Select FedEx Hold at Location', 'wf-shipping-fedex') ."</option>";
            if( ! empty(self::$fedex_locations) ) {
                foreach( self::$fedex_locations as $location_id => $location ) {

                    if( ! empty($location->LocationDetail->LocationType) && in_array($location->LocationDetail->LocationType, $hold_at_location_types) ) {
                    
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
                        if( $selected_location_id != $location_id) {
                            $locator .= "<option value= '".$location_id."'> ". $address ."</option>";
                        }
                        else{
                            $locator .= "<option value= '".$location_id."' selected> ". $address ."</option>";
                        }
                    }
                }
            }
            
            $locator .= "</select>";
            $array['#ph_fedex_hold_at_locations'] = $locator;
            return $array;
        }

        /**
         * Update FedEx Hold at Location information in rate request.
         */
        public function ph_fedex_update_hold_at_location_data_in_rate_request( $request , $parcels) {

            $selected_fedex_hold_at_location   = '';

            // Checkout Page $_POST['post_data'] will be available
            if( isset($_POST['post_data']) )
            {
                parse_str($_POST['post_data'], $data );
                $selected_fedex_hold_at_location = isset($data['ph_fedex_hold_at_locations'])?$data['ph_fedex_hold_at_locations']:'';
            }
            // After proceeding from Checkout Page $_POST['post_data'] will not be available
            else if( isset( $_POST['ph_fedex_hold_at_locations'] ) )
            {
                $selected_fedex_hold_at_location = $_POST['ph_fedex_hold_at_locations'];
            }
            else // 133908 - Order Edit Page - Rate Request
            {
                $order_id = isset($_REQUEST['oid']) ? $_REQUEST['oid'] : '';
                $order = !empty($order_id) ? wc_get_order($order_id) : '';
                if(!empty($order) && is_object($order))
                {
                    $selected_fedex_hold_at_location = $order->get_meta( 'ph_fedex_hold_at_location', true );
                }
            }

            if( is_object($selected_fedex_hold_at_location) || (! empty($selected_fedex_hold_at_location) && !empty($selected_fedex_hold_at_location=$this->get_fedex_hold_at_location_details($selected_fedex_hold_at_location))) ) {
                $contact=array('CompanyName'=>isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName)?$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName:'',
                    'PhoneNumber'=>isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->PhoneNumber)?$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->PhoneNumber:'',
                    'FaxNumber'=>isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->FaxNumber)?$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->FaxNumber:'',
                    'EMailAddress'=>isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->EMailAddress)?$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->EMailAddress:'',
                );


                $address=array('StreetLines'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines,
                    'City'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->City,
                    'StateOrProvinceCode'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode,
                    'PostalCode'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode,
                    'CountryCode'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode,
                    'Residential'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->Residential,
                    'GeographicCoordinates'=>$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->GeographicCoordinates
                );
                
                if( !isset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']) ) {
                    $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'] = [];
                }
                
                $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'HOLD_AT_LOCATION';
                $request['RequestedShipment']['SpecialServicesRequested']['HoldAtLocationDetail'] = array(
                    'LocationType'              => $selected_fedex_hold_at_location->LocationDetail->LocationType,
                    'LocationId'                => $selected_fedex_hold_at_location->LocationDetail->LocationId,
                    'LocationContactAndAddress' => array('Contact' =>$contact,'Address'=>$address)
                );
            }

            return $request;
        }
        /*
        * Return the loction details based on the location id.
        */
        public function get_fedex_hold_at_location_details($location_id)
        {
            $fedex_hold_at_locations=WC()->session->get('ph_fedex_hold_at_locations');
            if(empty($fedex_hold_at_locations))
            {
                $this->fedex_on_hold_locations();
                $this->get_fedex_hold_at_location_details($location_id);
            }
            else
            {
                foreach( $fedex_hold_at_locations as $key => $location ) {
                    if($location_id==$key)
                    {
                        return $location;
                    }
                }
                return array();
                // $this->fedex_on_hold_locations();
                // $this->get_fedex_hold_at_location_details($location_id);
            }
        }
        /**
         * Add debug data.
         */
        public function debug( $message, $type = 'notice' ) {
            if ( ! is_admin() && $this->debug == 'yes' && function_exists('wc_add_notice') ) {
                wc_add_notice( $message, $type );
            }
        }

        /**
         * Update selected Hold at Location in Order meta.
         */
        public function ph_fedex_update_hold_at_location_in_order_meta( $order_id ,$data) {
            
            $selected_fedex_hold_at_location = isset($data['ph_fedex_hold_at_locations'])?$data['ph_fedex_hold_at_locations']:'';

            if( ! empty($selected_fedex_hold_at_location) ) {

                $selected_fedex_hold_at_location=$this->get_fedex_hold_at_location_details($selected_fedex_hold_at_location);

                $address = null;

                // Street Address
                if( ! empty($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines) ) {
                    $address = $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines;
                }

                // City
                if( ! empty($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->City) ) {
                    $address = ! empty($address) ? $address.', '. $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->City : $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->City;
                }

                // State
                if( ! empty($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode) ) {
                    $address = ! empty($address) ? $address.', '. $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode : $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode;
                }

                // Postal Code
                if( ! empty($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode) ) {
                    $address = ! empty($address) ? $address.', '. $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode : $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode;
                }

                // Country
                $address = !empty($address) ? $address.', '.$selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode : $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode;

                $selectedAddress    = $address;
                
                //Distance
                if( ! empty($selected_fedex_hold_at_location->Distance->Value) ) {
                    $address = $address.'. ('. $selected_fedex_hold_at_location->Distance->Value.' '. $selected_fedex_hold_at_location->Distance->Units.')';
                }

                update_post_meta( $order_id, 'ph_fedex_hold_at_location', $selected_fedex_hold_at_location );

                if( isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact) && isset($selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName) ) {

                    $selectedAddress    = $selected_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName.', '.$selectedAddress;
                }

                update_post_meta( $order_id, 'ph_fedex_selected_hold_at_location', $selectedAddress );
            }
        }
       
        /**
         * Update Hold at location in Woocommerce Packages.
         * @param array $packages Array of Woocommerce Packages.
         * @return array
         */
        public function ph_fedex_update_hold_at_location_details_in_checkout_package( $packages ) {
            if(isset($_POST['post_data']))
            {
                parse_str($_POST['post_data'], $data );
                foreach( $packages as &$package ) {
                    if( ! empty($package['contents']) ) {
                        $selected_fedex_hold_at_location = isset($data['ph_fedex_hold_at_locations'])?$data['ph_fedex_hold_at_locations']:'';
                        if( ! empty($selected_fedex_hold_at_location) )
                        {
                            $selected_hold_at_location_details=$this->get_fedex_hold_at_location_details($selected_fedex_hold_at_location);
                            $package['ph_fedex_hold_at_location'] = $selected_hold_at_location_details;
                        }
                    }
                }
            }
            return $packages;
        }
        /**
         * Add to Formatted Shipping Address.
         * @param array $shipping_address Shipping Address.
         * @param object $order WC_Order Object.
         * @return array
         */
        public function ph_fedex_add_hold_at_location_to_formatted_shipping_address( $shipping_address, $order ) {
            $hold_at_location = $order->get_meta('ph_fedex_hold_at_location');
            if( ! empty($hold_at_location) ) {
                $hold_at_location=$hold_at_location;
                $order_shipping_hold_at_location = (isset($hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines)) ? $hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines : '';
                $order_shipping_hold_at_location .= (isset($hold_at_location->LocationDetail->LocationContactAndAddress->Address->City)) ? ', '.$hold_at_location->LocationDetail->LocationContactAndAddress->Address->City : '';
                $order_shipping_hold_at_location .= (isset($hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode)) ? ', '.$hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode : '';
                $order_shipping_hold_at_location .= (isset($hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode)) ? ', '.$hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode : '';
                $order_shipping_hold_at_location .= (isset($hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode)) ? ', '.$hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode : '';
                $shipping_address['ph_fedex_hold_at_location'] = $order_shipping_hold_at_location;
            }
            return $shipping_address;
        }



        public function ph_fedex_formatted_address_replacements( $formatted_address, $address ) {
            $hold_at_location_tag = ! empty($address['ph_fedex_hold_at_location']) ? __( 'Hold At Location:  ', 'wf-shipping-fedex' ).$address['ph_fedex_hold_at_location'] :'';
            $formatted_address['{ph_fedex_hold_at_location}'] = $hold_at_location_tag;
            return $formatted_address; 
        }
        
        public function ph_fedex_address_formats( $formats ) {
            foreach ($formats as $key => $format) {
                $formats[$key]=$format."\n{ph_fedex_hold_at_location}";
            }       
            return $formats;
        }
        /**
         * Update FedEx Hold at Location information in shipment request.
         */
        public function ph_fedex_update_hold_at_location_data_in_shipment_request( $request,$order ) {
            if( !empty($order->get_meta( 'ph_fedex_hold_at_location', true ) ) ) {
                    $ph_fedex_hold_at_location=$order->get_meta( 'ph_fedex_hold_at_location', true );
                    $contact=array();
                    isset($ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName)?$contact['CompanyName']=$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->CompanyName:'';
                    isset($ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->PhoneNumber)?$contact['PhoneNumber']=$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->PhoneNumber:'';
                    isset($ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->FaxNumber)?$contact['FaxNumber']=$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->FaxNumber:'';
                    isset($ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->EMailAddress)?$contact['EMailAddress']=$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Contact->EMailAddress:'';


                    $address=array('StreetLines'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StreetLines,
                                'City'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->City,
                                'StateOrProvinceCode'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->StateOrProvinceCode,
                                'PostalCode'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->PostalCode,
                                'CountryCode'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->CountryCode,
                                'Residential'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->Residential,
                                'GeographicCoordinates'=>$ph_fedex_hold_at_location->LocationDetail->LocationContactAndAddress->Address->GeographicCoordinates
                                );
                    $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][] = 'HOLD_AT_LOCATION';
                    $request['RequestedShipment']['SpecialServicesRequested']['HoldAtLocationDetail'] = array(
                        'PhoneNumber'               => $request['RequestedShipment']['Recipient']['Contact']['PhoneNumber'],
                        'LocationType'              => $ph_fedex_hold_at_location->LocationDetail->LocationType,
                        'LocationId'                => $ph_fedex_hold_at_location->LocationDetail->LocationId,
                        'LocationContactAndAddress' => array('Contact' =>$contact,'Address'=>$address)
                    );
                }
            return $request;
        }
        /**
         * Update FedEx Hold at Location information in return shipment request.
         */
        public function ph_fedex_update_hold_at_location_data_in_return_shipment_request( $request ) {
            if(  in_array('HOLD_AT_LOCATION',$request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']) ) {
                    $index=array_search('HOLD_AT_LOCATION', $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']);
                    unset($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes'][$index]);
                    unset($request['RequestedShipment']['SpecialServicesRequested']['HoldAtLocationDetail']);
                    $request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']=array_values($request['RequestedShipment']['SpecialServicesRequested']['SpecialServiceTypes']);
                    
                }
            return $request;
        }

        
    }

    // new Ph_Fedex_Woocommerce_Location_Finder();
}