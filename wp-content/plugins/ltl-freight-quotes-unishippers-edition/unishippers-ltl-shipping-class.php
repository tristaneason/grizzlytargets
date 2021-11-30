<?php

/**
 * Unishippers Shipping Class
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Unishippers Shipping Method Init
 */
if (!function_exists('unishippers_uni_ltl_shipping_method_init')) {

    function unishippers_uni_ltl_shipping_method_init()
    {

        if (!class_exists('WC_unishippers_Shipping_Method')) {

            /**
             * Class WC_unishippers_Shipping_Method
             */
            class WC_unishippers_Shipping_Method extends WC_Shipping_Method
            {
                public $forceAllowShipMethodUnishipper = array();
                public $getPkgObjUnishipper;
                public $Uinshipper_Ltl_Liftgate_As_Option;
                public $unishippers_ltl_res_inst;
                public $quote_settings;
                public $instore_pickup_and_local_delivery;
                public $InstorPickupLocalDelivery;
                public $group_small_shipments;
                public $web_service_inst;
                public $package_plugin;
                public $woocommerce_package_rates;
                public $shipment_type;
                // FDO
                public $en_fdo_meta_data = [];
                public $en_fdo_meta_data_third_party = [];

                /**
                 * smpkgFoundErr
                 * @var array type
                 */
                public $smpkgFoundErr = array();

                /**
                 * Constructor
                 * @param $instance_id
                 */
                public function __construct($instance_id = 0)
                {
                    $this->id = 'uni_ltl_shipping_method';
                    $this->instance_id = absint($instance_id);
                    $this->method_title = __('Unishippers Freight');
                    $this->method_description = __('Real-time Unishippers freight quotes from Unishipper.');
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                        'instance-settings-modal',
                    );
                    $this->enabled = "yes";
                    $this->title = "LTL Freight Quotes - Unishippers Edition";
                    $this->init();

                    $this->Uinshipper_Ltl_Liftgate_As_Option = new Uinshipper_Ltl_Liftgate_As_Option();
                }

                /**
                 * Initialization
                 */
                function init()
                {
                    $this->init_form_fields();
                    $this->init_settings();
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                /**
                 * Form Fields
                 */
                public function init_form_fields()
                {

                    $this->instance_form_fields = array(
                        'enabled' => array(
                            'title' => __('Enable / Disable', 'woocommerce'),
                            'type' => 'checkbox',
                            'label' => __('Enable This Shipping Service', 'woocommerce'),
                            'default' => 'yes',
                            'id' => 'unishippers_enable_disable_shipping'
                        )
                    );
                }

                /**
                 * Third party quotes
                 * @param type $forceShowMethods
                 * @return type
                 */
                public function forceAllowShipMethodUnishipper($forceShowMethods)
                {
                    if (!empty($this->getPkgObjUnishipper->ValidShipmentsArr) && (!in_array("unishippers_ltl_freight", $this->getPkgObjUnishipper->ValidShipmentsArr))) {
                        $this->forceAllowShipMethodUnishipper[] = "free_shipping";
                        $this->forceAllowShipMethodUnishipper[] = "valid_third_party";
                    } else {

                        $this->forceAllowShipMethodUnishipper[] = "unishippers_ltl_shipment";
                    }

                    $forceShowMethods = array_merge($forceShowMethods, $this->forceAllowShipMethodUnishipper);
                    return $forceShowMethods;
                }

                /**
                 * Virtual Products
                 */
                public function en_virtual_products()
                {
                    global $woocommerce;
                    $products = $woocommerce->cart->get_cart();
                    $items = $product_name = [];
                    foreach ($products as $key => $product_obj) {
                        $product = $product_obj['data'];
                        $is_virtual = $product->get_virtual();

                        if ($is_virtual == 'yes') {
                            $attributes = $product->get_attributes();
                            $product_qty = $product_obj['quantity'];
                            $product_title = str_replace(array("'", '"'), '', $product->get_title());
                            $product_name[] = $product_qty . " x " . $product_title;

                            $meta_data = [];
                            if (!empty($attributes)) {
                                foreach ($attributes as $attr_key => $attr_value) {
                                    $meta_data[] = [
                                        'key' => $attr_key,
                                        'value' => $attr_value,
                                    ];
                                }
                            }

                            $items[] = [
                                'id' => $product_obj['product_id'],
                                'name' => $product_title,
                                'quantity' => $product_qty,
                                'price' => $product->get_price(),
                                'weight' => 0,
                                'length' => 0,
                                'width' => 0,
                                'height' => 0,
                                'type' => 'virtual',
                                'product' => 'virtual',
                                'sku' => $product->get_sku(),
                                'attributes' => $attributes,
                                'variant_id' => 0,
                                'meta_data' => $meta_data,
                            ];
                        }
                    }

                    $virtual_rate = [];

                    if (!empty($items)) {
                        $virtual_rate = [
                            'id' => 'en_virtual_rate',
                            'label' => 'Virtual Quote',
                            'cost' => 0,
                        ];

                        $virtual_fdo = [
                            'plugin_type' => 'ltl',
                            'plugin_name' => 'wwe_quests',
                            'accessorials' => '',
                            'items' => $items,
                            'address' => '',
                            'handling_unit_details' => '',
                            'rate' => $virtual_rate,
                        ];

                        $meta_data = [
                            'sender_origin' => 'Virtual Product',
                            'product_name' => wp_json_encode($product_name),
                            'en_fdo_meta_data' => $virtual_fdo,
                        ];

                        $virtual_rate['meta_data'] = $meta_data;

                    }

                    return $virtual_rate;
                }

                /**
                 * Calculate Shipping
                 * @param $package
                 * @global $current_user
                 * @global $wpdb
                 */
                public function calculate_shipping($package = [], $eniture_admin_order_action = false)
                {
                    if (is_admin() && !wp_doing_ajax() && !$eniture_admin_order_action) {
                        return [];
                    }

                    $this->package_plugin = get_option('unishippers_freight_packages_quotes_package');
                    $this->instore_pickup_and_local_delivery = FALSE;

                    // Eniture debug mood
                    do_action("eniture_error_messages", "Errors");

                    $changObj = new Unishippers_Freight_Woo_Update_Changes();
                    $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $changObj->unishippers_postcode();

                    $coupn = WC()->cart->get_coupons();
                    if (isset($coupn) && !empty($coupn)) {
                        $freeShipping = $this->UnishippersFreeShipping($coupn);
                        if ($freeShipping == 'y')
                            return FALSE;
                    }

                    $this->create_unishippers_ltl_option();
                    global $wpdb;
                    global $current_user;
                    $sandbox = "";
                    $quotes = array();
                    $smallQuotes = array();
                    $rate = array();
                    $own_freight = array();

                    $smallPackages = false;

                    $allowArrangements = get_option('wc_settings_unishippers_freight_allow_for_own_arrangment');
                    $unishippers_ltl_res_inst = new unishippers_ltl_shipping_get_quotes();
                    $this->unishippers_ltl_res_inst = $unishippers_ltl_res_inst;
                    $this->web_service_inst = $unishippers_ltl_res_inst;

                    $this->unishippers_ltl_shipping_quote_settings();

                    // Eniture debug mood
                    do_action("eniture_debug_mood", "Quote Settings (Unishippers Freight)", $this->unishippers_ltl_res_inst->quote_settings);

                    if (isset($this->unishippers_ltl_res_inst->quote_settings['handling_fee']) &&
                        ($this->unishippers_ltl_res_inst->quote_settings['handling_fee'] == "-100%")) {
                        return FALSE;
                    }

                    $group_unishippers_ltl_shipments = new group_unishippers_ltl_shipments();
                    $this->getPkgObjUnishipper = $group_unishippers_ltl_shipments;

                    $unishippers_ltl_package = $group_unishippers_ltl_shipments->unishippers_ltl_package_shipments($package, $unishippers_ltl_res_inst, $freight_zipcode);

                    add_filter('force_show_methods', array($this, 'forceAllowShipMethodUnishipper'));

                    $no_param_multi_ship = 0;

                    if (is_array($unishippers_ltl_package) && count($unishippers_ltl_package) > 1) {
                        foreach ($unishippers_ltl_package as $key => $value) {
                            if (isset($value["NOPARAM"]) && $value["NOPARAM"] === 1 && empty($value["items"])) {
                                $no_param_multi_ship = 1;
                                unset($unishippers_ltl_package[$key]);
                            }
                        }
                    }

                    $eniturePluigns = json_decode(get_option('EN_Plugins'));
                    $calledMethod = array();
                    $smallPluginExist = 0;

                    if (!empty($unishippers_ltl_package)) {
                        $ltl_products = $small_products = [];
                        foreach ($unishippers_ltl_package as $key => $sPackage) {
                            if (array_key_exists('unishippers_ltl', $sPackage)) {
                                $ltl_products[] = $sPackage;
                                $web_service_arr = $unishippers_ltl_res_inst->unishippers_ltl_shipping_get_web_service_array($sPackage, $this->package_plugin);
                                $response = $unishippers_ltl_res_inst->unishippers_ltl_shipping_get_web_quotes($web_service_arr);
                                if (empty($response)) {
                                    return [];
                                }
                                (!empty($response)) ? $quotes[$key] = $response : "";
                                continue;
                            } elseif (array_key_exists('small', $sPackage)) {
                                $sPackage['is_shipment'] = 'small';
                                $small_products[] = $sPackage;
                            }
                        }

                        if (isset($small_products) && !empty($small_products) && !empty($ltl_products)) {
                            foreach ($eniturePluigns as $enIndex => $enPlugin) {
                                $freightSmallClassName = 'WC_' . $enPlugin;
                                if (!in_array($freightSmallClassName, $calledMethod)) {
                                    if (class_exists($freightSmallClassName)) {
                                        $smallPluginExist = 1;
                                        $SmallClassNameObj = new $freightSmallClassName();
                                        $package['itemType'] = 'ltl';
                                        $package['sPackage'] = $small_products;
                                        $smallQuotesResponse = $SmallClassNameObj->calculate_shipping($package, true);
                                        $smallQuotes[] = $smallQuotesResponse;
                                    }
                                    $calledMethod[] = $freightSmallClassName;
                                }
                            }
                        }
                    }

                    $smallQuotes = (is_array($smallQuotes) && (!empty($smallQuotes))) ? reset($smallQuotes) : $smallQuotes;
                    $smallMinRate = (is_array($smallQuotes) && (!empty($smallQuotes))) ? current($smallQuotes) : $smallQuotes;

                    // Virtual products
                    $virtual_rate = $this->en_virtual_products();
                    // FDO
                    if (isset($smallMinRate['meta_data']['en_fdo_meta_data'])) {


                        if (!empty($smallMinRate['meta_data']['en_fdo_meta_data']) && !is_array($smallMinRate['meta_data']['en_fdo_meta_data'])) {
                            $en_third_party_fdo_meta_data = json_decode($smallMinRate['meta_data']['en_fdo_meta_data'], true);
                            isset($en_third_party_fdo_meta_data['data']) ? $smallMinRate['meta_data']['en_fdo_meta_data'] = $en_third_party_fdo_meta_data['data'] : '';
                        }
                        $this->en_fdo_meta_data_third_party = (isset($smallMinRate['meta_data']['en_fdo_meta_data']['address'])) ? [$smallMinRate['meta_data']['en_fdo_meta_data']] : $smallMinRate['meta_data']['en_fdo_meta_data'];
                    }

                    $smpkgCost = (isset($smallMinRate['cost'])) ? $smallMinRate['cost'] : 0;


                    if (isset($smallMinRate) && (!empty($smallMinRate))) {
                        switch (TRUE) {
                            case (isset($smallMinRate['minPrices'])):
                                $small_quotes = $smallMinRate['minPrices'];
                                break;
                            default :
                                $shipment_zipcode = key($smallQuotes);
                                $small_quotes = array($shipment_zipcode => $smallMinRate);
                                break;
                        }
                    }

                    $this->quote_settings = $this->unishippers_ltl_res_inst->quote_settings;
                    $this->quote_settings = json_decode(json_encode($this->quote_settings), true);
                    $quotes = json_decode(json_encode($quotes), true);
                    $handling_fee = $this->quote_settings['handling_fee'];
                    // When rating method as average rate.
                    $rating_method = $this->quote_settings['rating_method'];

                    $Unishippers_Freight_Quotes = new Unishippers_Freight_Quotes();
                    if ((count($quotes) > 1 || $smpkgCost > 0) || $no_param_multi_ship == 1 || !empty($virtual_rate)) {
                        $multi_cost = 0;
                        $s_multi_cost = 0;
                        $_label = "";

//                    Custom client work "unishippers_ltl_remove_small_minimum_value_By_zero_when_coupon_add" 
                        if (has_filter('small_min_remove_zero_type_params')) {
                            $smpkgCost = apply_filters('small_min_remove_zero_type_params', $package, $smpkgCost);
                        }


                        $this->quote_settings['shipment'] = "multi_shipment";

                        (isset($small_quotes) && count($small_quotes) > 0) ? $this->minPrices['UNISHIPPERS_LIFT'] = $small_quotes : "";
                        (isset($small_quotes) && count($small_quotes) > 0) ? $this->minPrices['UNISHIPPERS_NOTLIFT'] = $small_quotes : "";
//                      Virtual products
                        if (!empty($virtual_rate)) {
                            $en_virtual_fdo_meta_data[] = $virtual_rate['meta_data']['en_fdo_meta_data'];
                            $virtual_meta_rate['virtual_rate'] = $virtual_rate;
                            $this->minPrices['UNISHIPPERS_LIFT'] = isset($this->minPrices['UNISHIPPERS_LIFT']) && !empty($this->minPrices['UNISHIPPERS_LIFT']) ? array_merge($this->minPrices['UNISHIPPERS_LIFT'], $virtual_meta_rate) : $virtual_meta_rate;
                            $this->minPrices['UNISHIPPERS_NOTLIFT'] = isset($this->minPrices['UNISHIPPERS_NOTLIFT']) && !empty($this->minPrices['UNISHIPPERS_NOTLIFT']) ? array_merge($this->minPrices['UNISHIPPERS_NOTLIFT'], $virtual_meta_rate) : $virtual_meta_rate;
                            $this->en_fdo_meta_data_third_party = !empty($this->en_fdo_meta_data_third_party) ? array_merge($this->en_fdo_meta_data_third_party, $en_virtual_fdo_meta_data) : $en_virtual_fdo_meta_data;
                        }

                        foreach ($quotes as $key => $quote) {
                            $key = "LTL_" . $key;

                            $simple_quotes = (isset($quote['simple_quotes'])) ? $quote['simple_quotes'] : array();
                            $quote = $this->remove_array($quote, 'simple_quotes');

                            $rates = $Unishippers_Freight_Quotes->calculate_quotes($quote, $this->quote_settings, 'UNISHIPPERS_LIFT');
                            $rates = reset($rates);
                            $this->minPrices['UNISHIPPERS_LIFT'][$key] = $rates;

                            $_cost = (isset($rates['cost'])) ? $rates['cost'] : 0;

                            // FDO
                            $this->en_fdo_meta_data['UNISHIPPERS_LIFT'][$key] = (isset($rates['meta_data']['en_fdo_meta_data'])) ? $rates['meta_data']['en_fdo_meta_data'] : [];
                            // When rating method as average rate.
                            if ($rating_method == 'average_rate' && isset($this->en_fdo_meta_data['UNISHIPPERS_LIFT'][$key]['rate']['cost'], $this->en_fdo_meta_data['UNISHIPPERS_LIFT'][$key]['rate']['label'])) {
                                $this->en_fdo_meta_data['UNISHIPPERS_LIFT'][$key]['rate']['cost'] = $_cost;
                                $this->en_fdo_meta_data['UNISHIPPERS_LIFT'][$key]['rate']['label'] = 'Freight';
                            }

                            $_label = (isset($rates['label_sufex'])) ? $rates['label_sufex'] : "";
                            $append_label = (isset($rates['append_label'])) ? $rates['append_label'] : "";
                            $handling_fee = (isset($rates['markup']) && (strlen($rates['markup']) > 0)) ? $rates['markup'] : $handling_fee;

//                          Offer lift gate delivery as an option is enabled
                            if (isset($this->quote_settings['liftgate_delivery_option']) &&
                                ($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                                (!empty($simple_quotes))) {
                                $s_rates = $Unishippers_Freight_Quotes->calculate_quotes($simple_quotes, $this->quote_settings, 'UNISHIPPERS_NOTLIFT');
                                $s_rates = reset($s_rates);
                                $this->minPrices['UNISHIPPERS_NOTLIFT'][$key] = $s_rates;
                                $s_cost = (isset($s_rates['cost'])) ? $s_rates['cost'] : 0;

                                // FDO
                                $this->en_fdo_meta_data['UNISHIPPERS_NOTLIFT'][$key] = (isset($s_rates['meta_data']['en_fdo_meta_data'])) ? $s_rates['meta_data']['en_fdo_meta_data'] : [];
                                // When rating method as average rate.
                                if ($rating_method == 'average_rate' && isset($this->en_fdo_meta_data['UNISHIPPERS_NOTLIFT'][$key]['rate']['cost'], $this->en_fdo_meta_data['UNISHIPPERS_NOTLIFT'][$key]['rate']['label'])) {
                                    $this->en_fdo_meta_data['UNISHIPPERS_NOTLIFT'][$key]['rate']['cost'] = $s_cost;
                                    $this->en_fdo_meta_data['UNISHIPPERS_NOTLIFT'][$key]['rate']['label'] = 'Freight';
                                }

                                $s_label = (isset($s_rates['label_sufex'])) ? $s_rates['label_sufex'] : "";
                                $s_append_label = (isset($s_rates['append_label'])) ? $s_rates['append_label'] : "";
                                $s_multi_cost += $this->add_handling_fee($s_cost, $handling_fee);
                            }

                            $multi_cost += $this->add_handling_fee($_cost, $handling_fee);
                        }

                        ($s_multi_cost > 0) ? $rate[] = $this->arrange_multiship_freight(($s_multi_cost + $smpkgCost), 'UNISHIPPERS_NOTLIFT', $s_label, $s_append_label) : "";
                        ($multi_cost > 0) ? $rate[] = $this->arrange_multiship_freight(($multi_cost + $smpkgCost), 'UNISHIPPERS_LIFT', $_label, $append_label) : "";

                        $this->shipment_type = 'multiple';

                        $rates = $this->unishippers_freight_add_rate_arr($rate);
                    } else {

//                  Dispaly Local and In-store PickUp Delivery 
                        $this->InstorPickupLocalDelivery = $unishippers_ltl_res_inst->unishippers_freight_return_local_delivery_store_pickup();
                        $quote = reset($quotes);
                        $simple_quotes = (isset($quote['simple_quotes'])) ? $quote['simple_quotes'] : array();
                        $quote = $this->remove_array($quote, 'simple_quotes');

                        $rates = $Unishippers_Freight_Quotes->calculate_quotes($quote, $this->quote_settings, 'UNISHIPPERS_LIFT');

//                      Offer lift gate delivery as an option is enabled
                        if (isset($this->quote_settings['liftgate_delivery_option']) &&
                            ($this->quote_settings['liftgate_delivery_option'] == "yes") &&
                            (!empty($simple_quotes))) {
                            $simple_rates = $Unishippers_Freight_Quotes->calculate_quotes($simple_quotes, $this->quote_settings, 'UNISHIPPERS_NOTLIFT');
                            $rates = array_merge($rates, $simple_rates);
                        }

                        $cost_sorted_key = array();

                        $this->quote_settings['shipment'] = "single_shipment";

                        foreach ($rates as $key => $quote) {
                            $handling_fee = (isset($rates['markup']) && (strlen($rates['markup']) > 0)) ? $rates['markup'] : $handling_fee;
                            $_cost = (isset($quote['cost'])) ? $quote['cost'] : 0;

                            // When rating method as average rate.
                            if ($rating_method == 'average_rate' && isset($quote['meta_data']['en_fdo_meta_data']['rate']['cost'], $quote['meta_data']['en_fdo_meta_data']['rate']['label'])) {
                                $rates[$key]['meta_data']['en_fdo_meta_data']['rate']['cost'] = $_cost;
                                $rates[$key]['meta_data']['en_fdo_meta_data']['rate']['label'] = 'Freight';
                            }

                            $rates[$key]['cost'] = $this->add_handling_fee($_cost, $handling_fee);
                            $cost_sorted_key[$key] = (isset($quote['cost'])) ? $quote['cost'] : 0;
                            $rates[$key]['shipment'] = "single_shipment";
                        }

//                       array_multisort
                        array_multisort($cost_sorted_key, SORT_ASC, $rates);


                        $this->shipment_type = 'single';

                        $rates = $this->unishippers_freight_add_rate_arr($rates);
                    }
                    // Origin terminal address
                    if ($this->shipment_type == 'single') {
                        /**
                         * call local-delivery and instore-pickup function to show the data on shipping page
                         */
                        (isset($this->unishippers_ltl_res_inst->InstorPickupLocalDelivery->localDelivery) && ($this->unishippers_ltl_res_inst->InstorPickupLocalDelivery->localDelivery->status == 1)) ? $this->local_delivery($this->unishippers_ltl_res_inst->en_wd_origin_array['fee_local_delivery'], $this->unishippers_ltl_res_inst->en_wd_origin_array['checkout_desc_local_delivery'], $this->unishippers_ltl_res_inst->en_wd_origin_array) : "";
                        (isset($this->unishippers_ltl_res_inst->InstorPickupLocalDelivery->inStorePickup) && ($this->unishippers_ltl_res_inst->InstorPickupLocalDelivery->inStorePickup->status == 1)) ? $this->pickup_delivery($this->unishippers_ltl_res_inst->en_wd_origin_array['checkout_desc_store_pickup'], $this->unishippers_ltl_res_inst->en_wd_origin_array, $this->unishippers_ltl_res_inst->InstorPickupLocalDelivery->totalDistance) : "";
                    }

                    return $rates;
                }

                public function en_sort_woocommerce_available_shipping_methods($rates, $package)
                {
                    //  if there are no rates don't do anything
                    if (!$rates) {
                        return [];
                    }

                    // Check the option to sort shipping methods by price on quote settings
                    if (get_option('shipping_methods_do_not_sort_by_price') != 'yes') {
                        // get an array of prices
                        $prices = array();
                        foreach ($rates as $rate) {
                            $prices[] = $rate->cost;
                        }

                        // use the prices to sort the rates
                        array_multisort($prices, $rates);
                    }

                    // return the rates
                    return $rates;
                }

                /**
                 * Pickup delivery quote
                 * @return array type
                 */
                function pickup_delivery($label, $en_wd_origin_array, $total_distance)
                {
                    $this->woocommerce_package_rates = 1;
                    $this->instore_pickup_and_local_delivery = TRUE;

                    $label = (isset($label) && (strlen($label) > 0)) ? $label : 'In-store pick up';
                    // Origin terminal address
                    $address = (isset($en_wd_origin_array['address'])) ? $en_wd_origin_array['address'] : '';
                    $city = (isset($en_wd_origin_array['city'])) ? $en_wd_origin_array['city'] : '';
                    $state = (isset($en_wd_origin_array['state'])) ? $en_wd_origin_array['state'] : '';
                    $zip = (isset($en_wd_origin_array['zip'])) ? $en_wd_origin_array['zip'] : '';
                    $phone_instore = (isset($en_wd_origin_array['phone_instore'])) ? $en_wd_origin_array['phone_instore'] : '';
                    strlen($total_distance) > 0 ? $label .= ': Free | ' . str_replace("mi", "miles", $total_distance) . ' away' : '';
                    strlen($address) > 0 ? $label .= ' | ' . $address : '';
                    strlen($city) > 0 ? $label .= ', ' . $city : '';
                    strlen($state) > 0 ? $label .= ' ' . $state : '';
                    strlen($zip) > 0 ? $label .= ' ' . $zip : '';
                    strlen($phone_instore) > 0 ? $label .= ' | ' . $phone_instore : '';

                    $pickup_delivery = array(
                        'id' => 'in-store-pick-up',
                        'cost' => 0,
                        'label' => $label,
                    );

                    add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                    $this->add_rate($pickup_delivery);
                }

                /**
                 * Local delivery quote
                 * @param string type $cost
                 * @return array type
                 */
                function local_delivery($cost, $label, $en_wd_origin_array)
                {
                    $this->woocommerce_package_rates = 1;
                    $this->instore_pickup_and_local_delivery = TRUE;
                    $label = (isset($label) && (strlen($label) > 0)) ? $label : 'Local Delivery';
                    $local_delivery = array(
                        'id' => 'local-delivery',
                        'cost' => $cost,
                        'label' => $label,
                    );

                    add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                    $this->add_rate($local_delivery);
                }

                /**
                 * Remove array
                 * @return array
                 */
                function remove_array($quote, $remove_index)
                {
                    unset($quote[$remove_index]);

                    return $quote;
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
                 * Multishipment
                 * @return array
                 */
                function arrange_multiship_freight($cost, $id, $label_sufex, $append_label)
                {

                    return array(
                        'id' => $id,
                        'label' => "Freight",
                        'cost' => $cost,
                        'label_sufex' => $label_sufex,
                        'append_label' => $append_label,
                    );
                }

                /**
                 *
                 * @param string type $price
                 * @param string type $handling_fee
                 * @return float type
                 */
                function add_handling_fee($price, $handling_fee)
                {
                    $handling_fee = $price > 0 ? $handling_fee : 0;
                    $handelingFee = 0;
                    if ($handling_fee != '' && $handling_fee != 0) {
                        if (strrchr($handling_fee, "%")) {

                            $prcnt = (float)$handling_fee;
                            $handelingFee = (float)$price / 100 * $prcnt;
                        } else {
                            $handelingFee = (float)$handling_fee;
                        }
                    }

                    $handelingFee = $this->smooth_round($handelingFee);
                    $price = (float)$price + $handelingFee;
                    return $price;
                }

                /**
                 *
                 * @param float type $val
                 * @param int type $min
                 * @param int type $max
                 * @return float type
                 */
                function smooth_round($val, $min = 2, $max = 4)
                {
                    $result = round($val, $min);
                    if ($result == 0 && $min < $max) {
                        return $this->smooth_round($val, ++$min, $max);
                    } else {
                        return $result;
                    }
                }

                /**
                 * sort array
                 * @param array type $rate
                 * @return array type
                 */
                public function sort_asec_order_arr($rate, $index)
                {
                    $price_sorted_key = array();
                    foreach ($rate as $key => $cost_carrier) {
                        $price_sorted_key[$key] = (isset($cost_carrier[$index])) ? $cost_carrier[$index] : 0;
                    }
                    array_multisort($price_sorted_key, SORT_ASC, $rate);

                    return $rate;
                }

                /**
                 * Label from quote settings tab
                 * @return string type
                 */
                public function unishipper_label_as()
                {
                    return (strlen($this->quote_settings['unishipper_label']) > 0) ? $this->quote_settings['unishipper_label'] : "Freight";
                }

                /**
                 * Append label in quote
                 * @param array type $rate
                 * @return string type
                 */
                public function set_label_in_quote($rate)
                {
                    $rate_label = "";
                    $label_sufex = (isset($rate['label_sufex'])) ? array_unique($rate['label_sufex']) : array();
                    $rate_label = (!isset($rate['label']) ||
                        ($this->quote_settings['shipment'] == "single_shipment" &&
                            strlen($this->quote_settings['unishipper_label']) > 0)) ?
                        $this->unishipper_label_as() : $rate['label'];

                    $rate_label .= (isset($this->quote_settings['sandbox'])) ? ' (Sandbox) ' : '';

                    $rate_label .= isset($label_sufex) && (!empty($label_sufex)) ? $this->unishipper_label_sufex(array_unique($label_sufex)) : '';
                    $delivery_estimate_unishippers = isset($this->quote_settings['delivery_estimates']) ? $this->quote_settings['delivery_estimates'] : '';
                    // Cuttoff Time
                    $unishippers_show_delivery_estimates_plan = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'unishippers_show_delivery_estimates');
                    $shipment_type = isset($this->quote_settings['shipment']) && !empty($this->quote_settings['shipment']) ? $this->quote_settings['shipment'] : '';
                    if (isset($this->quote_settings['delivery_estimates']) && !empty($this->quote_settings['delivery_estimates'])
                        && $this->quote_settings['delivery_estimates'] != 'dont_show_estimates' &&
                        !is_array($unishippers_show_delivery_estimates_plan) && $shipment_type != 'multi_shipment') {
                        if ($this->quote_settings['delivery_estimates'] == 'delivery_date') {
                            isset($rate['delivery_time_stamp']) && is_string($rate['delivery_time_stamp']) && strlen($rate['delivery_time_stamp']) > 0 ? $rate_label .= ' ( Expected delivery by ' . date('Y-m-d', strtotime($rate['delivery_time_stamp'])) . ')' : '';
                        } else if ($delivery_estimate_unishippers == 'delivery_days') {
                            $correct_word = (isset($rate['delivery_estimates']) && $rate['delivery_estimates'] == 1) ? 'is' : 'are';
                            isset($rate['delivery_estimates']) && is_string($rate['delivery_estimates']) && strlen($rate['delivery_estimates']) > 0 ? $rate_label .= ' ( Estimated number of days until delivery ' . $correct_word . ' ' . $rate['delivery_estimates'] . ' )' : '';
                        }
                    }
                    return $rate_label;
                }

                public function unishipper_label_sufex($label_sufex)
                {
                    $append_label = "";

                    switch (TRUE) {
                        case(count($label_sufex) == 1):
                            (in_array('L', $label_sufex)) ? $append_label = " with lift gate delivery " : "";
                            (in_array('R', $label_sufex)) ? $append_label = " with residential delivery " : "";
                            break;
                        case(count($label_sufex) == 2):
                            (in_array('L', $label_sufex)) ? $append_label = " with lift gate delivery " : "";
                            (in_array('R', $label_sufex)) ? $append_label .= (strlen($append_label) > 0) ? " and residential delivery " : " with residential delivery " : "";
                            break;
                    }

                    return $append_label;
                }

                /**
                 * rates to add_rate woocommerce
                 * @param array type $add_rate_arr
                 */
                public function unishippers_freight_add_rate_arr($add_rate_arr)
                {

                    if (isset($add_rate_arr) && (!empty($add_rate_arr)) && (is_array($add_rate_arr))) {

                        // Images for FDO
                        $image_urls = apply_filters('en_fdo_image_urls_merge', []);

                        add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);

                        $instore_pickup_local_devlivery_action = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');

                        foreach ($add_rate_arr as $key => $rate) {

                            if (isset($rate['cost']) && $rate['cost'] > 0) {
                                $rate['label'] = $this->set_label_in_quote($rate);

                                if (isset($rate['meta_data'])) {
                                    $rate['meta_data']['label_sufex'] = (isset($rate['label_sufex'])) ? json_encode($rate['label_sufex']) : array();
                                }

                                $rate['id'] = (isset($rate['id'])) ? $rate['id'] : '';

                                if (isset($this->minPrices[$rate['id']])) {
                                    $rate['meta_data']['min_prices'] = json_encode($this->minPrices[$rate['id']]);
                                    $rate['meta_data']['en_fdo_meta_data']['data'] = array_values($this->en_fdo_meta_data[$rate['id']]);
                                    (!empty($this->en_fdo_meta_data_third_party)) ? $rate['meta_data']['en_fdo_meta_data']['data'] = array_merge($rate['meta_data']['en_fdo_meta_data']['data'], $this->en_fdo_meta_data_third_party) : '';
                                    $rate['meta_data']['en_fdo_meta_data']['shipment'] = 'multiple';
                                    $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($rate['meta_data']['en_fdo_meta_data']);
                                } else {
                                    $en_set_fdo_meta_data['data'] = [$rate['meta_data']['en_fdo_meta_data']];
                                    $en_set_fdo_meta_data['shipment'] = 'sinlge';
                                    $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($en_set_fdo_meta_data);
                                }

                                // Images for FDO
                                $rate['meta_data']['en_fdo_image_urls'] = wp_json_encode($image_urls);

                                if ($this->web_service_inst->en_wd_origin_array['suppress_local_delivery'] == "1" && (!is_array($instore_pickup_local_devlivery_action)) && ($this->shipment_type != 'multiple')) {

                                    $rate = apply_filters('suppress_local_delivery', $rate, $this->web_service_inst->en_wd_origin_array, $this->package_plugin, $this->InstorPickupLocalDelivery);

                                    if (!empty($rate)) {
                                        $this->add_rate($rate);
                                        $add_rate_arr[$key] = $rate;
                                        $this->woocommerce_package_rates = 1;
                                    }
                                } else {
                                    $this->add_rate($rate);
                                    $add_rate_arr[$key] = $rate;
                                }
                            }

                        }

                        (isset($this->quote_settings['own_freight']) && ($this->quote_settings['own_freight'] == "yes")) ? $this->add_rate($this->arrange_own_freight()) : "";
                    }

                    return $add_rate_arr;
                }


                /**
                 * quote settings array
                 * @global $wpdb $wpdb
                 */
                function unishippers_ltl_shipping_quote_settings()
                {
                    global $wpdb;
                    $enable_carriers = $wpdb->get_results("SELECT `unishippers_carrierSCAC` FROM wp_unishippers_freight_carriers Where carrier_status ='1'");
                    $enable_carriers = json_decode(json_encode($enable_carriers), TRUE);
                    $VersionCompat = new VersionCompat();
                    $enable_carriers = $VersionCompat->enArrayColumn($enable_carriers, 'unishippers_carrierSCAC');

                    $rating_method = get_option('wc_settings_unishippers_freight_rate_method');
                    $unishipper_label = get_option('wc_settings_unishippers_freight_label_as');

                    $this->unishippers_ltl_res_inst->quote_settings['transit_days'] = get_option('wc_settings_unishippers_freight_delivery_estimate');
                    $this->unishippers_ltl_res_inst->quote_settings['own_freight'] = get_option('wc_settings_unishippers_freight_allow_for_own_arrangment');
                    $this->unishippers_ltl_res_inst->quote_settings['total_carriers'] = get_option('wc_settings_unishippers_freight_Number_of_options');
                    $this->unishippers_ltl_res_inst->quote_settings['rating_method'] = (isset($rating_method) && (strlen($rating_method)) > 0) ? $rating_method : "Cheapest";
                    $this->unishippers_ltl_res_inst->quote_settings['unishipper_label'] = ($rating_method == "average_rate" || $rating_method == "Cheapest") ? $unishipper_label : "";
                    $this->unishippers_ltl_res_inst->quote_settings['handling_fee'] = get_option('wc_settings_unishippers_freight_hand_free_mark_up');
                    $this->unishippers_ltl_res_inst->quote_settings['enable_carriers'] = $enable_carriers;
                    $this->unishippers_ltl_res_inst->quote_settings['liftgate_delivery'] = get_option('wc_settings_unishippers_freight_lift_gate_delivery');
                    $this->unishippers_ltl_res_inst->quote_settings['liftgate_delivery_option'] = get_option('unishippers_freight_liftgate_delivery_as_option');
                    $this->unishippers_ltl_res_inst->quote_settings['residential_delivery'] = get_option('wc_settings_unishipper_residential_delivery ');
                    $this->unishippers_ltl_res_inst->quote_settings['liftgate_resid_delivery'] = get_option('en_woo_addons_liftgate_with_auto_residential');
                    // Cuttoff Time
                    $this->unishippers_ltl_res_inst->quote_settings['delivery_estimates'] = get_option('unishippers_delivery_estimates');
                    $this->unishippers_ltl_res_inst->quote_settings['orderCutoffTime'] = get_option('unishippers_freight_order_cut_off_time');
                    $this->unishippers_ltl_res_inst->quote_settings['shipmentOffsetDays'] = get_option('unishippers_freight_shipment_offset_days');

                }

                /**
                 * Create plugin option
                 */
                function create_unishippers_ltl_option()
                {
                    $eniture_plugins = get_option('EN_Plugins');
                    if (!$eniture_plugins) {
                        add_option('EN_Plugins', json_encode(array('uni_ltl_shipping_method')));
                    } else {
                        $plugins_array = json_decode($eniture_plugins);
                        if (!in_array('uni_ltl_shipping_method', $plugins_array)) {
                            array_push($plugins_array, 'uni_ltl_shipping_method');
                            update_option('EN_Plugins', json_encode($plugins_array));
                        }
                    }
                }

                /**
                 * Check is free shipping or not
                 * @param $coupon
                 * @return string
                 */
                function UnishippersFreeShipping($coupon)
                {
                    foreach ($coupon as $key => $value) {
                        if ($value->get_free_shipping() == 1) {
                            $free = array(
                                'id' => 'free',
                                'label' => 'Free Shipping',
                                'cost' => 0
                            );
                            $this->add_rate($free);
                            return 'y';
                        }
                    }
                    return 'n';
                }

            }

        }
    }

}