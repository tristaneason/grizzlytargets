<?php

/**
 * Unishippers Carrier Service
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class unishippers_ltl_shipping_get_quotes
 */
if (!class_exists('unishippers_ltl_shipping_get_quotes')) {

    class unishippers_ltl_shipping_get_quotes extends Uinshipper_Ltl_Liftgate_As_Option
    {

        /**
         * $EndPointURL
         * @var string type
         */
        protected $EndPointURL = UNISHIPPERS_FREIGHT_DOMAIN_HITTING_URL . '/index.php';
        public $en_wd_origin_array;
        public $InstorPickupLocalDelivery;

        /**
         * details array
         * @var array type
         */
        public $quote_settings;

        function __construct()
        {
            $this->quote_settings = array();
        }

        /**
         * Get Web Service Array
         * @param $packages
         * @return array
         */
        function unishippers_ltl_shipping_get_web_service_array($packages, $package_plugin = "")
        {
            $EnUniShipfreightFdo = new EnUniShipfreightFdo();
            $en_fdo_meta_data = array();

            $destinationAddressUnishipper = $this->destinationAddressUnishipper();
            $unishipper_residential_delivery = 'N';

            if (get_option('wc_settings_unishipper_residential_delivery ') == 'yes') {
                $unishipper_residential_delivery = 'Y';
            }

            $unishipper_lift_gate_delivery = 'N';
            if (get_option('wc_settings_unishippers_freight_lift_gate_delivery') == 'yes') {
                $unishipper_lift_gate_delivery = 'Y';
            }

            $accessorial = array();
            (get_option('wc_settings_unishippers_freight_lift_gate_delivery') == 'yes') ? $accessorial['LG2'] = 'Liftgate Delivery' : '';
            ($this->quote_settings['liftgate_delivery_option'] == 'yes') ? $accessorial['LG2'] = 'Liftgate Delivery' : '';
            (get_option('wc_settings_unishipper_residential_delivery ') == 'yes') ? $accessorial['RES2'] = 'Residential Delivery' : '';
            // Cuttoff Time
            $shipment_week_days = "";
            $order_cut_off_time = "";
            $shipment_off_set_days = "";
            $modify_shipment_date_time = "";
            $store_date_time = "";
            $unishippers_delivery_estimates = get_option('unishippers_delivery_estimates');
            $unishippers_show_delivery_estimates = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'unishippers_show_delivery_estimates');
            $shipment_week_days = $this->unishippers_shipment_week_days();
            if ($unishippers_delivery_estimates == 'delivery_days' || $unishippers_delivery_estimates == 'delivery_date' && !is_array($unishippers_show_delivery_estimates)) {
                $order_cut_off_time = $this->quote_settings['orderCutoffTime'];
                $shipment_off_set_days = $this->quote_settings['shipmentOffsetDays'];
                $modify_shipment_date_time = ($order_cut_off_time != '' || $shipment_off_set_days != '' || (is_array($shipment_week_days) && count($shipment_week_days) > 0)) ? 1 : 0;
                $store_date_time = $today = date('Y-m-d H:i:s', current_time('timestamp'));
            }
            $freightClass_unishippers_ltl_gross = "";
            $productName = array();
            $productQty = array();
            $productPrice = array();
            $productWeight = array();
            $productLength = array();
            $productWidth = array();
            $productHeight = array();
            $productClass = array();
            $hazmat_line_item = array(
                'isHazmatLineItem' => 'Y', // Y / N
                'lineItemHazmatUNNumberHeader' => 'UN #', // Valid values are : UN #, ID #, NA #.
                'lineItemHazmatUNNumber' => 'UN 1139', // Every account have its own UN Number
                'lineItemHazmatClass' => '1.1', // valid hazmat class (1, 2.1, 2.2, 2.3, 3, 4.1, 4.2, 4.3, 5.1, 5.2, 6.1, 6.2,7, 8.,9) (Optional)
                'lineItemHazmatEmContactPhone' => '4043308699', // hazmat contact phone number (Required)
                'lineItemHazmatPackagingGroup' => 'I', // hazmat packaging group (I, II, III) (Optional)
            );
            $this->en_wd_origin_array = (isset($packages['origin'])) ? $packages['origin'] : array();

            $lineItem = array();
            $nested_plan = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'nested_material');
            $nestingPercentage = $nestedDimension = $nestedItems = $stakingProperty = [];
            $doNesting = false;
            foreach ($packages['items'] as $item) {
                // Standard Packaging
                $ship_as_own_pallet = isset($item['ship_as_own_pallet']) && $item['ship_as_own_pallet'] == 'yes' ? 1 : 0;
                $vertical_rotation_for_pallet = isset($item['vertical_rotation_for_pallet']) && $item['vertical_rotation_for_pallet'] == 'yes' ? 1 : 0;
                $uni_counter = (isset($item['variantId']) && $item['variantId'] > 0) ? $item['variantId'] : $item['productId'];

                $lineItem[$uni_counter] = array(
                    'piecesOfLineItem' => $item['productQty'],
                    'lineItemClass' => $item['productClass'],
                    'lineItemWeight' => $item['productWeight'],
                    'lineItemWidth' => $item['productWidth'],
                    'lineItemHeight' => $item['productHeight'],
                    'lineItemLength' => $item['productLength'],
                    'lineItemPackageCode' => 'PLT',
                    // Nested indexes
                    'nestingPercentage' => $item['nestedPercentage'],
                    'nestingDimension' => $item['nestedDimension'],
                    'nestedLimit' => $item['nestedItems'],
                    'nestedStackProperty' => $item['stakingProperty'],

                    // Shippable handling units
                    'lineItemPalletFlag' => $item['lineItemPalletFlag'],
                    'lineItemPackageType' => $item['lineItemPackageType'],

                    // Standard Packaging
                    'shipPalletAlone' => $ship_as_own_pallet,
                    'vertical_rotation' => $vertical_rotation_for_pallet
                );
                $lineItem[$uni_counter] = apply_filters('en_fdo_carrier_service', $lineItem[$uni_counter], $item);
                $product_name[] = $item['product_name'];
                isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
                $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";
            }

            $aPluginVersions = $this->unishippers_ltl_get_woo_version_number();
            $domain = unishippers_freight_get_domain();

            $residential_detecion_flag = get_option("en_woo_addons_auto_residential_detecion_flag");

            // FDO
            $en_fdo_meta_data = $EnUniShipfreightFdo->en_cart_package($packages);

            // Version numbers
            $plugin_versions = $this->en_version_numbers();

            $post_data = array(
                // Version numbers
                'plugin_version' => $plugin_versions["en_current_plugin_version"],
                'wordpress_version' => get_bloginfo('version'),
                'woocommerce_version' => $plugin_versions["woocommerce_plugin_version"],
                'platform' => 'WordPress',
                'carrierName' => 'unishippersLtl',
                'carrier_mode' => 'pro',
                'plugin_version' => $aPluginVersions["unishippers_freight_plugin_version"],
                'username' => get_option('wc_settings_unishippers_freight_username'),
                'password' => get_option('wc_settings_unishippers_freight_password'),
                'apiToken' => get_option('wc_settings_unishippers_freight_api_token'),
                'accountNumber' => get_option('wc_settings_unishippers_freight_account_number'),
                'id' => get_option('unishippers_account_id'),
                'licence_key' => get_option('wc_settings_unishippers_freight_licence_key'),
                'suspend_residential' => get_option('suspend_automatic_detection_of_residential_addresses'),
                'residential_detecion_flag' => $residential_detecion_flag,
                'server_name' => unishippers_freight_parse_url($domain),
                'receiverCity' => $destinationAddressUnishipper['city'],
                'receiverState' => $destinationAddressUnishipper['state'],
                'receiverZip' => $destinationAddressUnishipper['zip'],
                'receiverCountryCode' => $destinationAddressUnishipper['country'],
                'unishippers_residential_delivery' => $unishipper_residential_delivery,
                'unishippers_lift_gate_delivery' => $unishipper_lift_gate_delivery,
                'senderCity' => $packages['origin']['city'],
                'senderState' => $packages['origin']['state'],
                'senderZip' => $packages['origin']['zip'],
                'senderCountryCode' => $packages['origin']['country'],
                'sender_origin' => $packages['origin']['location'] . ": " . $packages['origin']['city'] . ", " . $packages['origin']['state'] . " " . $packages['origin']['zip'],
                'product_name' => $product_name,

                'accessorial' => $accessorial,
                'commdityDetails' => $lineItem,
                // FDO
                'en_fdo_meta_data' => $en_fdo_meta_data,
                'doNesting' => $doNesting,
                // Cuttoff Time
                'modifyShipmentDateTime' => $modify_shipment_date_time,
                'OrderCutoffTime' => $order_cut_off_time,
                'shipmentOffsetDays' => $shipment_off_set_days,
                'storeDateTime' => $store_date_time,
                'shipmentWeekDays' => $shipment_week_days,
            );

//          Hazardous Material
            $hazardous_material = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'hazardous_material');

            if (!is_array($hazardous_material)) {
                (isset($packages['hazardousMaterial'])) ? $post_data['accessorial']['HAZ'] = 'Hazardous Materials' : "";
                (isset($packages['hazardousMaterial']) == 'yes') ? $post_data['hazardous'][] = 'H' : '';
                // FDO
                $post_data['en_fdo_meta_data'] = array_merge($post_data['en_fdo_meta_data'], $EnUniShipfreightFdo->en_package_hazardous($packages, $en_fdo_meta_data));
            }

//          In-store pickup and local delivery
            $instore_pickup_local_devlivery_action = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
            if (!is_array($instore_pickup_local_devlivery_action)) {
                $post_data = apply_filters('en_wd_standard_plans', $post_data, $post_data['receiverZip'], $this->en_wd_origin_array, $package_plugin);
            }

            $post_data = $this->unishippers_freight_update_carrier_service($post_data);
            $post_data = apply_filters("en_woo_addons_carrier_service_quotes_request", $post_data, en_woo_plugin_unishippers_freight);

            // Standard Packaging
            $post_data = apply_filters('en_pallet_identify', $post_data);

            do_action("eniture_debug_mood", "Quotes Request (Unishippers Freight)", $post_data);

            return $post_data;
        }

        /**
         * @return shipment days of a week  - Cuttoff time
         */
        public function unishippers_shipment_week_days()
        {
            $shipment_days_of_week = array();

            if (get_option('all_shipment_days_unishippers') == 'yes') {
                return $shipment_days_of_week;
            }

            if (get_option('monday_shipment_day_unishippers') == 'yes') {
                $shipment_days_of_week[] = 1;
            }
            if (get_option('tuesday_shipment_day_unishippers') == 'yes') {
                $shipment_days_of_week[] = 2;
            }
            if (get_option('wednesday_shipment_day_unishippers') == 'yes') {
                $shipment_days_of_week[] = 3;
            }
            if (get_option('thursday_shipment_day_unishippers') == 'yes') {
                $shipment_days_of_week[] = 4;
            }
            if (get_option('friday_shipment_day_unishippers') == 'yes') {
                $shipment_days_of_week[] = 5;
            }

            return $shipment_days_of_week;
        }

        /**
         * destinationAddressUnishipper
         * @return array type
         */
        function destinationAddressUnishipper()
        {
            $en_order_accessories = apply_filters('en_order_accessories', []);
            if (isset($en_order_accessories) && !empty($en_order_accessories)) {
                return $en_order_accessories;
            }

            $changObj = new Unishippers_Freight_Woo_Update_Changes();
            $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $changObj->unishippers_postcode();
            $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $changObj->unishippers_getState();
            $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $changObj->unishippers__getCountry();
            $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $changObj->unishippers_getCity();
            return array(
                'city' => $freight_city,
                'state' => $freight_state,
                'zip' => $freight_zipcode,
                'country' => $freight_country
            );
        }

        /**
         * Return version numbers
         * @return int
         */

        function en_version_numbers()
        {
            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';
            $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
            $get_plugin_data = get_plugin_data(UNISHIPPERS_MAIN_FILE);
            $plugin_version = (isset($get_plugin_data['Version'])) ? $get_plugin_data['Version'] : '';

            $versions = array(
                "woocommerce_plugin_version" => $wc_plugin,
                "en_current_plugin_version" => $plugin_version
            );

            return $versions;

        }


        /**
         * Get Web Quotes CURL Call
         * @param $request_data
         * @return json
         */
        function unishippers_ltl_shipping_get_web_quotes($request_data)
        {

//          Eniture debug mood
            do_action("eniture_debug_mood", "Build Query (Unishippers Freight)", http_build_query($request_data));
//            check response from session 
            $currentData = md5(json_encode($request_data));
            $requestFromSession = WC()->session->get('previousRequestData');
            $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : array();

            if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {
//              Eniture debug mood
                do_action("eniture_debug_mood", "Plugin Features (Unishippers Freight)", get_option('eniture_plugin_22'));

                do_action("eniture_debug_mood", "Quotes session Response (Unishippers Freight)", json_decode($requestFromSession[$currentData]));

                $quotes['quotes'] = json_decode($requestFromSession[$currentData]);
                $quotes['markup'] = (isset($request_data['markup'])) ? $request_data['markup'] : "";
                return $this->parse_unishippers_freight_output($quotes, $request_data);


            }

            if (is_array($request_data) && count($request_data) > 0) {

                $unishippers_freight_curl_obj = new Unishippers_Curl_Request();
                $output = $unishippers_freight_curl_obj->unishippers_freight_get_curl_response($this->EndPointURL, $request_data);

//              Eniture debug mood
                do_action("eniture_debug_mood", "Quotes Response (Unishippers Freight)", json_decode($output));

//              set response in session                
                $response = json_decode($output);
                $errorDescriptions = (isset($response->q->quoteSpeedFreightShipmentReturn->errorDescriptions) ? $response->q->quoteSpeedFreightShipmentReturn->errorDescriptions : NULL);

                if (isset($response->q) && (!empty($response->q)) && ($errorDescriptions == NULL)) {
                    if (isset($response->autoResidentialSubscriptionExpired) &&
                        ($response->autoResidentialSubscriptionExpired == 1)) {
                        $flag_api_response = "no";
                        $request_data['residential_detecion_flag'] = $flag_api_response;
                        $currentData = md5(json_encode($request_data));
                    }

                    $requestFromSession[$currentData] = $output;
                    WC()->session->set('previousRequestData', $requestFromSession);
                }

                $quotes['quotes'] = $response;
                $quotes['markup'] = "";
                return $this->parse_unishippers_freight_output($quotes, $request_data);

            }
        }

        /**
         * Get Shipping Array For Single Shipment
         * @param $output
         * @return Single Quote Array
         */
        function parse_unishippers_freight_output($result, $request_data)
        {
            // FDO
            $en_fdo_meta_data = (isset($request_data['en_fdo_meta_data'])) ? $request_data['en_fdo_meta_data'] : '';
            if (isset($result['quotes']->debug)) {
                $en_fdo_meta_data['handling_unit_details'] = $result['quotes']->debug;
            }

            // Standard Packaging
            $standard_packaging_data = [];
            if (isset($result['quotes']->standardPackagingData)) {
                $standard_packaging_data = json_encode($result['quotes']->standardPackagingData);
            }
            $this->InstorPickupLocalDelivery = (isset($result['quotes']->InstorPickupLocalDelivery)) ? $result['quotes']->InstorPickupLocalDelivery : array();
            $accessorials = [];
            ($this->quote_settings['liftgate_delivery'] == "yes") ? $accessorials[] = "L" : "";
            ($this->quote_settings['residential_delivery'] == "yes") ? $accessorials[] = "R" : "";
            (isset($request_data['hazardous']) && is_array($request_data['hazardous']) && in_array('H', $request_data['hazardous'])) ? $accessorials[] = "H" : "";

            $quote_results = (isset($result['quotes']->q)) ? $result['quotes']->q : array();
            $quote_error = (isset($result['quotes']->q->quoteSpeedFreightShipmentReturn->errorDescriptions) ? $result['quotes']->q->quoteSpeedFreightShipmentReturn->errorDescriptions : NULL);
            $api_single_quote = new stdClass();
            $allServices = array();

            if (!isset($quote_error) && (count($quote_results) > 0)) {
                $label_sufex_arr = $this->filter_label_sufex_array_unishippers_freight($result['quotes']);

                $count = 0;
                $price_sorted_key = array();
                $_price_sorted_key = array();
                $simple_quotes = array();

                foreach ($quote_results as $quote) {
                    // Cuttoff Time
                    $delivery_estimates = (isset($quote->totalTransitTimeInDays)) ? $quote->totalTransitTimeInDays : '';
                    $delivery_time_stamp = (isset($quote->deliveryTimestamp)) ? $quote->deliveryTimestamp : '';
                    if (isset($quote->service) && ($quote->service == 'Standard')) {

                        if (isset($quote->carrierCode) && in_array($quote->carrierCode, $this->quote_settings['enable_carriers'])) {
                            $surcharges = $quote->accessorialCharges;

                            $meta_data['accessorials'] = json_encode($accessorials);
                            $meta_data['sender_origin'] = $request_data['sender_origin'];
                            $meta_data['product_name'] = json_encode($request_data['product_name']);
                            // Standard Packaging
                            $meta_data['standard_packaging'] = $standard_packaging_data;

                            $allServices[$count] = array(
                                'id' => $quote->carrierCode,
                                'code' => $quote->carrierCode,
                                'label' => $quote->carrierName,
                                'cost' => $quote->totalNetCharge,
                                // Cuttoff Time
                                'delivery_estimates' => $delivery_estimates,
                                'delivery_time_stamp' => $delivery_time_stamp,
                                'meta_data' => $meta_data,
                                'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                                'label_sfx_arr' => $label_sufex_arr,
                                'surcharges' => $surcharges,
                            );

                            // FDO
                            $en_fdo_meta_data['rate'] = $allServices[$count];
                            if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                                unset($en_fdo_meta_data['rate']['meta_data']);
                            }
                            $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                            $allServices[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

                            $_price_sorted_key[$count] = (isset($allServices[$count]['cost'])) ? $allServices[$count]['cost'] : 0;

                            $allServices[$count] = apply_filters("en_woo_addons_web_quotes", $allServices[$count], en_woo_plugin_unishippers_freight);

                            $label_sufex = (isset($allServices[$count]['label_sufex'])) ? $allServices[$count]['label_sufex'] : array();
                            $label_sufex = $this->label_R_unishippers_freight($label_sufex);
                            $allServices[$count]['label_sufex'] = $label_sufex;

                            in_array('R', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['residential'] = true : '';
                            ($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)) && in_array('L', $label_sufex_arr) ? $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true : '';

                            if ((($this->quote_settings['liftgate_delivery_option'] == "yes") && (!isset($result['quotes']->liftgateExcluded)))
                                && ((($this->quote_settings['liftgate_resid_delivery'] == "yes") && (!in_array("R", $label_sufex))) ||
                                    ($this->quote_settings['liftgate_resid_delivery'] != "yes"))) {
                                (isset($allServices[$count]['label_sufex']) &&
                                    (!empty($allServices[$count]['label_sufex']))) ?
                                    array_push($allServices[$count]['label_sufex'], "L") : // IF
                                    $allServices[$count]['label_sufex'] = array("L");       // ELSE

                                // FDO
                                $allServices[$count]['meta_data']['en_fdo_meta_data']['accessorials']['liftgate'] = true;
                                $allServices[$count]['append_label'] = " with lift gate delivery ";
                            }

                            $count++;
                        }
                    }
                }

                if (isset($result['quotes']->quotesWithoutLiftGate) && !isset($result->error) && (!empty($result['quotes']->quotesWithoutLiftGate))) {
                    $price_simple_quotes = array();
                    $simple_quotes = array();
                    $count = 0;
                    foreach ($result['quotes']->quotesWithoutLiftGate as $key => $service) {
                        $delivery_estimates = (isset($service->totalTransitTimeInDays)) ? $service->totalTransitTimeInDays : '';
                        $delivery_time_stamp = (isset($service->deliveryTimestamp)) ? $service->deliveryTimestamp : '';

                        if (isset($service->service) && ($service->service == 'Standard')) {

                            if (isset($service->carrierCode) && in_array($service->carrierCode, $this->quote_settings['enable_carriers'])) {
                                $surcharges = (isset($quote->accessorialCharges)) ? $quote->accessorialCharges : '';

                                $meta_data['accessorials'] = json_encode($accessorials);
                                $meta_data['sender_origin'] = $request_data['sender_origin'];
                                $meta_data['product_name'] = json_encode($request_data['product_name']);
                                // Standard Packaging
                                $meta_data['standard_packaging'] = $standard_packaging_data;

                                $simple_quotes[$count] = array(
                                    'id' => $service->carrierCode . $count . 'WL',
                                    'code' => $service->carrierCode,
                                    'label' => $service->carrierName,
                                    'cost' => $service->totalNetCharge,
                                    // Cuttoff Time
                                    'delivery_estimates' => $delivery_estimates,
                                    'delivery_time_stamp' => $delivery_time_stamp,
                                    'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                                    'label_sfx_arr' => $label_sufex_arr,
                                    'surcharges' => $surcharges,
                                    'meta_data' => $meta_data,
                                );

                                // FDO
                                $en_fdo_meta_data['rate'] = $simple_quotes[$count];
                                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                                    unset($en_fdo_meta_data['rate']['meta_data']);
                                }
                                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                                $simple_quotes[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

                                $price_simple_quotes[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;

                                $simple_quotes[$count] = apply_filters("en_woo_addons_web_quotes", $simple_quotes[$count], en_woo_plugin_unishippers_freight);
                                $label_sufex = (isset($simple_quotes[$count]['label_sufex'])) ? $simple_quotes[$count]['label_sufex'] : array();

                                if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                                    (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)))) {
                                    $simple_quotes = array();
                                    continue;
                                }

                                $label_sufex = $this->label_R_freight_view($label_sufex);
                                $simple_quotes[$count]['label_sufex'] = $label_sufex;

                                (!empty($simple_quotes[$count])) && (in_array("R", $simple_quotes[$count]['label_sufex'])) ? $simple_quotes[$count]['label_sufex'] = array("R") : $simple_quotes[$count]['label_sufex'] = array();

                                $count++;
                            }
                        }
                    }
                }
            } else {

                return [];
                $label_sufex_arr = $this->filter_label_sufex_array_unishippers_freight($result['quotes']);
                $count = 0;
                $meta_data['accessorials'] = json_encode($accessorials);
                $meta_data['sender_origin'] = $request_data['sender_origin'];
                $meta_data['product_name'] = json_encode($request_data['product_name']);
                $meta_data['standard_packaging'] = '';

                $simple_quotes[$count] = array(
                    'id' => 'no_quotes',
                    'label' => '',
                    'cost' => 0,
                    'markup' => (isset($result['markup'])) ? $result['markup'] : "",
                    'label_sfx_arr' => $label_sufex_arr,
                    'surcharges' => [],
                    'meta_data' => $meta_data,
                );

                // FDO
                $en_fdo_meta_data['rate'] = $simple_quotes[$count];
                if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                    unset($en_fdo_meta_data['rate']['meta_data']);
                }
                $en_fdo_meta_data['quote_settings'] = $this->quote_settings;
                $simple_quotes[$count]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;

                $price_simple_quotes[$count] = (isset($simple_quotes[$count]['cost'])) ? $simple_quotes[$count]['cost'] : 0;

                $simple_quotes[$count] = apply_filters("en_woo_addons_web_quotes", $simple_quotes[$count], en_woo_plugin_unishippers_freight);
                $label_sufex = (isset($simple_quotes[$count]['label_sufex'])) ? $simple_quotes[$count]['label_sufex'] : array();


                if (($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                    (($this->quote_settings['liftgate_resid_delivery'] == "yes") && (in_array("R", $label_sufex)))) {
                    $simple_quotes = array();
                    //continue;
                }

                $label_sufex = $this->label_R_freight_view($label_sufex);
                $simple_quotes[$count]['label_sufex'] = $label_sufex;

                (!empty($simple_quotes[$count])) && (in_array("R", $simple_quotes[$count]['label_sufex'])) ? $simple_quotes[$count]['label_sufex'] = array("R") : $simple_quotes[$count]['label_sufex'] = array();

                $count++;
            }

//          array_multisort
            (!empty($allServices)) ? array_multisort($_price_sorted_key, SORT_ASC, $allServices) : "";
            (!empty($simple_quotes)) ? array_multisort($price_simple_quotes, SORT_ASC, $simple_quotes) : "";


            (!empty($simple_quotes)) ? $allServices['simple_quotes'] = $simple_quotes : "";

            return $allServices;
        }

        /**
         * check "R" in array
         * @param array type $label_sufex
         * @return array type
         */
        public function label_R_freight_view($label_sufex)
        {
            if ($this->quote_settings['residential_delivery'] == 'yes' && (in_array("R", $label_sufex))) {
                $label_sufex = array_flip($label_sufex);
                unset($label_sufex['R']);
                $label_sufex = array_keys($label_sufex);
            }

            return $label_sufex;
        }

        /**
         * check "R" in array
         * @param array type $label_sufex
         * @return array type
         */
        public function label_R_unishippers_freight($label_sufex)
        {
            if (get_option('wc_settings_unishipper_residential_delivery ') == 'yes' && (in_array("R", $label_sufex))) {
                $label_sufex = array_flip($label_sufex);
                unset($label_sufex['R']);
                $label_sufex = array_keys($label_sufex);
            }

            return $label_sufex;
        }

        /**
         * Check Unishippers Freight Class
         * @param $slug
         * @param $values
         * @return array
         * @global $woocommerce
         */
        function cart_has_product_with_Unishippers_class($slug, $values)
        {

            global $woocommerce;
            $product_in_cart = false;
            $_product = $values['data'];
            $terms = get_the_terms($_product->get_id(), 'product_shipping_class');
            if ($terms) {
                foreach ($terms as $term) {
                    $_shippingclass = $term->slug;
                    if ($slug === $_shippingclass) {
                        $product_in_cart[] = $_shippingclass;
                    }
                }
            }
            return $product_in_cart;
        }

        /**
         * Multi Warehouse
         * @param $warehous_list
         * @param $receiverZipCode
         * @return array
         */
        function unishippers_freight_multi_warehouse($warehous_list, $receiverZipCode)
        {

            if (count($warehous_list) == 1) {
                $warehous_list = reset($warehous_list);
                return $this->unishippers_ltl_origin_array($warehous_list);
            }
            require_once 'warehouse-dropship/get-distance-request.php';

            $unishippers_freight_distance_request = new Get_unishippers_ltl_distance();
            $accessLevel = "MultiDistance";
            $response_json = $unishippers_freight_distance_request->unishippers_ltl_get_distance($warehous_list, $accessLevel, $this->destinationAddressUnishipper());

            $response_obj = json_decode($response_json);
            return $this->unishippers_ltl_origin_array((isset($response_obj->origin_with_min_dist)) ? $response_obj->origin_with_min_dist : array());
        }

        /**
         * Arrange Own Freight
         * @return array
         */
        function arrange_own_freight()
        {

            return array(
                'id' => 'own_freight',
                'cost' => 0,
                'label' => get_option('wc_settings_unishippers_freight_text_for_own_arrangment'),
                'calc_tax' => 'per_item'
            );
        }

        /**
         * Origin
         * @param $origin
         * @return array
         */
        function unishippers_ltl_origin_array($origin)
        {

//          In-store pickup and local delivery
            if (has_filter("en_wd_origin_array_set")) {
                return apply_filters("en_wd_origin_array_set", $origin);
            }

            $zip = (isset($origin->zip)) ? $origin->zip : "";
            $city = (isset($origin->city)) ? $origin->city : "";
            $state = (isset($origin->state)) ? $origin->state : "";
            $country = (isset($origin->country)) ? $origin->country : "";
            $location = (isset($origin->location)) ? $origin->location : "";
            $locationId = (isset($origin->id)) ? $origin->id : "";
            return array('locationId' => $locationId, 'zip' => $zip, 'city' => $city, 'state' => $state, 'location' => $location, 'country' => $country);
        }

        /**
         * Return woocomerce and unishippers_ltl plugin versions
         */
        function unishippers_ltl_get_woo_version_number()
        {

            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';

            $plugin_folders = get_plugins('/' . 'ltl-freight-quotes-unishippers-edition');
            $plugin_files = 'woocommercefrieght.php';


            $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
            $unishippers_ltl_plugin = (isset($plugin_folders[$plugin_files]['Version'])) ? $plugin_folders[$plugin_files]['Version'] : "";

            $pluginVersions = array(
                "woocommerce_plugin_version" => $wc_plugin,
                "unishippers_freight_plugin_version" => $unishippers_ltl_plugin
            );

            return $pluginVersions;
        }

        /**
         * Return Unishippers LTL In-store Pickup Array
         */
        function unishippers_freight_return_local_delivery_store_pickup()
        {
            return $this->InstorPickupLocalDelivery;
        }

    }

}