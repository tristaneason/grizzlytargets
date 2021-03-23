<?php

/**
 * Unishippers Grouping
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class group_unishippers_ltl_shipments
 */
if (!class_exists('group_unishippers_ltl_shipments')) {

    class group_unishippers_ltl_shipments extends Uinshipper_Ltl_Liftgate_As_Option
    {

        /** hasUnishippersShipment */
        public $hasUnishippersShipment;

        /** $errors */
        public $errors = array();
        public $ValidShipmentsArr = array();
        public $products_details = [];

        /**
         * Shipment Packages
         * @param $package
         * @param $unishippers_ltl_res_inst
         * @param $freight_zipcode
         * @return boolean|int
         */
        function unishippers_ltl_package_shipments($package, $unishippers_ltl_res_inst, $freight_zipcode)
        {
            if (empty($freight_zipcode)) {
                return [];
            }
            $changObj = new Unishippers_Freight_Woo_Update_Changes();
            $weight = 0;
            $dimensions = 0;
            $unishippers_ltl_enable = false;
            $unishippers_ltl_package = [];

            $unishippers_ltl_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $changObj->unishippers_postcode();

            $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
            $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
            $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

            $flat_rate_shipping_addon = apply_filters('en_add_flat_rate_shipping_addon', false);
            // make package of Cart items
            foreach ($package['contents'] as $item_id => $values) {
                $_product = (isset($values['data'])) ? $values['data'] : "";

                // Flat rate pricing
                $product_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                $en_flat_rate_price = get_post_meta($product_id, 'en_flat_rate_price', true);
                if ($flat_rate_shipping_addon && isset($en_flat_rate_price) && strlen($en_flat_rate_price) > 0) {
                    continue;
                }

                // Get product shipping class
                $en_ship_class = strtolower($values['data']->get_shipping_class());
                if (in_array($en_ship_class, $en_get_current_classes_arr)) {
                    continue;
                }

                // Shippable handling units
                $values = apply_filters('en_shippable_handling_units_request', $values, $values, $_product);
                $shippable = [];
                if (isset($values['shippable']) && !empty($values['shippable'])) {
                    $shippable = $values['shippable'];
                }

                // Nesting
                $nestedPercentage = 0;
                $nestedDimension = "";
                $nestedItems = "";
                $StakingProperty = "";

                // Start compatability with Woocommerce Product Add-ons
                $product_id = (isset($values['product_id'])) ? $values['product_id'] : 0;
                $variation_id = (isset($values['variation_id'])) ? $values['variation_id'] : 0;
                $product_unique_key = $product_id . $variation_id;
                if (in_array($product_unique_key, $this->products_details)) {
                    continue;
                }

                $this->products_details[] = $product_unique_key;
                // End compatability with Woocommerce Product Add-ons

                $height = wc_get_dimension($_product->get_height(), 'in');
                $width = wc_get_dimension($_product->get_width(), 'in');
                $length = wc_get_dimension($_product->get_length(), 'in');
                $product_weight = wc_get_weight($_product->get_weight(), 'lbs');

                $height = (strlen($height) > 0) ? $height : "0";
                $width = (strlen($width) > 0) ? $width : "0";
                $length = (strlen($length) > 0) ? $length : "0";
                $product_weight = (strlen($product_weight) > 0) ? $product_weight : "0";

                $weight = ($values['quantity'] == 1) ? $product_weight : $product_weight * $values['quantity'];
                $dimensions = (($length * $values['quantity']) * $width * $height);
//                  grouping 
                $locationId = 0;
                $origin_address = $this->unishippers_freight_get_origin($_product, $values, $unishippers_ltl_res_inst, $unishippers_ltl_zipcode);
//                  get product class
                $freightClass_unishippers_ltl_gross = $this->unishippers_ltl_freight_class($values, $_product);

                if (!empty($origin_address)) {

                    // Hazardous Material
                    $hazardous_material = $this->en_hazardous_material($values, $_product);
                    $hm_plan = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'hazardous_material');
                    $hm_status = (!is_array($hm_plan) && $hazardous_material == 'yes') ? TRUE : FALSE;

                    $locationId = $origin_address['locationId'];
                    $unishippers_ltl_package[$locationId]['origin'] = $origin_address;

                    $product_title = str_replace(array("'", '"'), '', $_product->get_title());
                    // Nesting
                    $nested_material = $this->en_nested_material($values, $_product);
                    if ($nested_material == "yes") {
                        $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
                        $nestedPercentage = get_post_meta($post_id, '_nestedPercentage', true);
                        $nestedDimension = get_post_meta($post_id, '_nestedDimension', true);
                        $nestedItems = get_post_meta($post_id, '_maxNestedItems', true);
                        $StakingProperty = get_post_meta($post_id, '_nestedStakingProperty', true);
                    }

                    if (!$_product->is_virtual()) {
                        // Shippable handling units
                        $lineItemPalletFlag = $lineItemPackageCode = $lineItemPackageType = '0';
                        extract($shippable);

                        $en_items = array(
                            'productId' => $_product->get_id(),
                            'productName' => $product_title,
                            'productQty' => $values['quantity'],
                            'product_name' => $values['quantity'] . " x " . $product_title,
                            'products' => $product_title,
                            'productPrice' => $_product->get_price(),
                            'productWeight' => $product_weight,
                            'productLength' => $length,
                            'productWidth' => $width,
                            'productHeight' => $height,
                            'productClass' => $freightClass_unishippers_ltl_gross,
                            'hazardousMaterial' => $hm_status,
                            'hazardous_material' => $hm_status,
                            'productType' => ($_product->get_type() == 'variation') ? 'variant' : 'simple',
                            'productSku' => $_product->get_sku(),
                            'actualProductPrice' => $_product->get_price(),
                            'attributes' => $_product->get_attributes(),
                            'variantId' => ($_product->get_type() == 'variation') ? $_product->get_id() : '',
                            'nestedMaterial' => $nested_material,
                            'nestedPercentage' => $nestedPercentage,
                            'nestedDimension' => $nestedDimension,
                            'nestedItems' => $nestedItems,
                            'stakingProperty' => $StakingProperty,

                            // Shippable handling units
                            'lineItemPalletFlag' => $lineItemPalletFlag,
                            'lineItemPackageCode' => $lineItemPackageCode,
                            'lineItemPackageType' => $lineItemPackageType
                        );

                        // Hook for flexibility adding to package
                        $en_items = apply_filters('en_group_package', $en_items, $values, $_product);

                        $unishippers_ltl_package[$locationId]['items'][] = $en_items;

                        // Hazardous Material
                        if ($hazardous_material == "yes" && !isset($unishippers_ltl_package[$locationId]['hazardous_material'])) {
                            $unishippers_ltl_package[$locationId]['hazardousMaterial'] = TRUE;
                        }
                    }
                }

                // check if Unishippers enable 
                $unishippers_ltl_enable = $this->unishippers_freight_enable_shipping_class($_product);

                // Quotes settings option get Unishippers rates if weight > 150
                $exceedWeight = get_option('en_plugins_return_Unishippers_quotes');

                $unishippers_ltl_package[$locationId]['shipment_weight'] = isset($unishippers_ltl_package[$locationId]['shipment_weight']) ? $unishippers_ltl_package[$locationId]['shipment_weight'] + $weight : $weight;

                $smallPluginExist = 0;
                $calledMethod = array();
                $eniturePluigns = json_decode(get_option('EN_Plugins'));
                if (!empty($eniturePluigns)) {
                    foreach ($eniturePluigns as $enIndex => $enPlugin) {

                        $freightSmallClassName = 'WC_' . $enPlugin;

                        if (!in_array($freightSmallClassName, $calledMethod)) {

                            if (class_exists($freightSmallClassName)) {
                                $smallPluginExist = 1;
                            }

                            $calledMethod[] = $freightSmallClassName;
                        }
                    }
                }

                if ($unishippers_ltl_enable == true || ($unishippers_ltl_package[$locationId]['shipment_weight'] > 150 && $exceedWeight == 'yes')) {
                    $unishippers_ltl_package[$locationId]['unishippers_ltl'] = 1;
                    $this->hasUnishippersShipment = 1;
                    $this->ValidShipmentsArr[] = "unishippers_ltl_freight";
                } elseif (isset($unishippers_ltl_package[$locationId]['unishippers_ltl'])) {
                    $unishippers_ltl_package[$locationId]['unishippers_ltl'] = 1;
                    $this->hasUnishippersShipment = 1;
                    $this->ValidShipmentsArr[] = "unishippers_ltl_freight";
                } elseif ($smallPluginExist == 1) {
                    $unishippers_ltl_package[$locationId]['small'] = 1;
                    $this->ValidShipmentsArr[] = "small_shipment";
                } else {
                    $this->ValidShipmentsArr[] = "no_shipment";
                }

                if (empty($unishippers_ltl_package[$locationId]['items'])) {
                    unset($unishippers_ltl_package[$locationId]);
                    $unishippers_ltl_package[$locationId]["NOPARAM"] = 1;
                }
            }

//              Eniture debug mood
            do_action("eniture_debug_mood", "Product Detail (Unishippers Freight)", $unishippers_ltl_package);
            return $unishippers_ltl_package;
        }

        /**
         * Nested Material
         * @param array type $values
         * @param array type $_product
         * @return string type
         */
        function en_nested_material($values, $_product)
        {
            $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            return get_post_meta($post_id, '_nestedMaterials', true);
        }

        /**
         * Hazardous Material
         * @param array type $values
         * @param array type $_product
         * @return string type
         */
        function en_hazardous_material($values, $_product)
        {
            $post_id = (isset($values['variation_id']) && $values['variation_id'] > 0) ? $values['variation_id'] : $_product->get_id();
            return get_post_meta($post_id, '_hazardousmaterials', true);
        }

        /**
         * Small Pakages Cost
         * will return $result['error'] = false even if any of the plugins returns quotes
         * @param $smallQuotes
         * @return int
         */
        function getSmallPackagesCost($smallQuotes)
        {
            $result = array();
            $minCostArr = array();

            if (isset($smallQuotes) && count($smallQuotes) > 0) {
                foreach ($smallQuotes as $smQuotes) { // applications
                    $CostArr = array();
                    if (!isset($smQuotes['error'])) {
                        foreach ($smQuotes as $smQuote) { // services
                            $CostArr[] = $smQuote['cost']; // pick cheapest value of all services of single apps
                            $result['error'] = false;
                        }
                        $minCostArr[] = !empty($CostArr) ? min($CostArr) : '';
                    } else {
                        $result['error'] = !isset($result['error']) ? true : $result['error'];
                    }
                }
                // get cheapest of all aplications
                $result['price'] = (isset($minCostArr) && count($minCostArr) > 0) ? min($minCostArr) : "";
            } else {
                //no small quotes required
                $result['error'] = false;
                $result['price'] = 0;
            }

            return $result;
        }

        /**
         * Get Shipment Origin
         * @param $_product
         * @param $values
         * @param $unishippers_ltl_res_inst
         * @param $unishippers_ltl_zipcode
         * @return array
         * @global $wpdb
         */
        function unishippers_freight_get_origin($_product, $values, $unishippers_ltl_res_inst, $unishippers_ltl_zipcode)
        {

            global $wpdb;

            //      UPDATE QUERY In-store pick up                           
            $en_wd_update_query_string = apply_filters("en_wd_update_query_string", "");

            (isset($values['variation_id']) && $values['variation_id'] > 0) ? $post_id = $values['variation_id'] : $post_id = $_product->get_id();
            $enable_dropship = get_post_meta($post_id, '_enable_dropship', true);
            if ($enable_dropship == 'yes') {
                $get_loc = get_post_meta($post_id, '_dropship_location', true);
                if ($get_loc == '') {
                    return array('error' => 'unishipper small dp location not found!');
                }

                //          Multi Dropship
                $multi_dropship = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'multi_dropship');

                if (is_array($multi_dropship)) {
                    $locations_list = $wpdb->get_results(
                        "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship' LIMIT 1"
                    );
                } else {
                    $get_loc = ($get_loc !== '') ? maybe_unserialize($get_loc) : $get_loc;
                    $get_loc = is_array($get_loc) ? implode(" ', '", $get_loc) : $get_loc;
                    $locations_list = $wpdb->get_results(
                        "SELECT id, city, state, zip, country, location, nickname " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE id IN ('" . $get_loc . "')"
                    );
                }

                $eniture_debug_name = "Dropships";
            } else {

                //          Multi Warehouse
                $multi_warehouse = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'multi_warehouse');
                if (is_array($multi_warehouse)) {
                    $locations_list = $wpdb->get_results(
                        "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse' LIMIT 1"
                    );
                } else {
                    $locations_list = $wpdb->get_results(
                        "SELECT id, city, state, zip, country, location " . $en_wd_update_query_string . "FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
                    );
                }

                $eniture_debug_name = "Warehouses";
            }

            do_action("eniture_debug_mood", "Quotes $eniture_debug_name (Unishippers Freight)", $locations_list);

            $origin_address = $unishippers_ltl_res_inst->unishippers_freight_multi_warehouse($locations_list, $unishippers_ltl_zipcode);
            return $origin_address;
        }

        /**
         * Check Product Freight Class
         * @param $values
         * @param $_product
         * @return array
         */
        function unishippers_ltl_freight_class($values, $_product)
        {

            if ($_product->get_type() == 'variation') {
                $variation_class = get_post_meta($values['variation_id'], '_ltl_freight_variation', true);
                if ($variation_class == 0) {
                    $variation_class = get_post_meta($values['product_id'], '_ltl_freight', true);
                    $freightClass_unishippers_ltl_gross = $variation_class;
                } else {
                    if ($variation_class > 0) {
                        $freightClass_unishippers_ltl_gross = get_post_meta($values['variation_id'], '_ltl_freight_variation', true);
                    } else {
                        $freightClass_unishippers_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
                    }
                }
            } else {
                $freightClass_unishippers_ltl_gross = get_post_meta($_product->get_id(), '_ltl_freight', true);
            }

            return $freightClass_unishippers_ltl_gross;
        }

        /**
         * Check Product Enable Against Unishippers Freight
         * @param $_product
         * @return string
         */
        function unishippers_freight_enable_shipping_class($_product)
        {
            if ($_product->get_type() == 'variation') {
                $ship_class_id = $_product->get_shipping_class_id();

                if ($ship_class_id == 0) {
                    $parent_data = $_product->get_parent_data();
                    $get_parent_term = get_term_by('id', $parent_data['shipping_class_id'], 'product_shipping_class');
                    $get_shipping_result = (isset($get_parent_term->slug)) ? $get_parent_term->slug : '';
                } else {
                    $get_shipping_result = $_product->get_shipping_class();
                }

                $unishippers_ltl_enable = ($get_shipping_result && $get_shipping_result == 'ltl_freight') ? true : false;
            } else {
                $get_shipping_result = $_product->get_shipping_class();
                $unishippers_ltl_enable = ($get_shipping_result == 'ltl_freight') ? true : false;
            }

            return $unishippers_ltl_enable;
        }

    }

}