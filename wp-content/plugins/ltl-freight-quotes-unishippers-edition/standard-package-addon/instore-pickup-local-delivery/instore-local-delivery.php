<?php

/**
 * Includes Ajax Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWDQuotes")) {

    class EnWDQuotes
    {

        public function __construct()
        {
            add_filter('en_wd_update_query_string', array($this, 'en_wd_update_query_string'), 10, 1);
            add_filter('en_wd_origin_array_set', array($this, 'en_wd_origin_array_set'), 10, 1);
            add_filter('en_wd_standard_plans', array($this, 'en_wd_standard_plans'), 10, 4);
            add_filter('suppress_local_delivery', array($this, 'suppress_local_delivery'), 10, 4);
        }

        public function suppress_local_delivery($quotes, $origin_array, $package_plugin, $Instor_pickup_local_delivery)
        {

            $action = $this->check_package_plan($package_plugin);

            $local_delivery = (isset($Instor_pickup_local_delivery->localDelivery) && ($Instor_pickup_local_delivery->localDelivery->status == 1)) ? FALSE : TRUE;
            $in_store_pickup = (isset($Instor_pickup_local_delivery->inStorePickup) && ($Instor_pickup_local_delivery->inStorePickup->status == 1)) ? FALSE : TRUE;

            if ($action || ($local_delivery && $in_store_pickup)) {
                return $quotes;
            }

            if ($local_delivery) {
                $origin_array['suppress_local_delivery'] = 0;
            }

            return ($origin_array['suppress_local_delivery'] != 1 || is_admin()) ? $quotes : array();
        }

        /**
         * check enable for store pickup and local delivery
         * @param string type $destination_zip
         * @param array type $origin_array
         * @return boolean type
         */
        function set_enabled_enable_packages($destination_zip, $origin_array, $post_data)
        {
            $InstorPickupLocalDelivery = array();

            if (isset($origin_array['enable_store_pickup']) && ($origin_array['enable_store_pickup'] == 1)) {
                $InstorPickupLocalDelivery['inStorePickup']['addressWithInMiles'] = $origin_array['miles_store_pickup'];
                $InstorPickupLocalDelivery['inStorePickup']['postalCodeMatch'] = (strpos(strtolower($origin_array['match_postal_store_pickup']), strtolower($destination_zip)) !== FALSE) ? 1 : 0;
            }

            if (isset($origin_array['enable_local_delivery']) && ($origin_array['enable_local_delivery'] == 1)) {

                $InstorPickupLocalDelivery['localDelivery']['addressWithInMiles'] = $origin_array['miles_local_delivery'];
                $InstorPickupLocalDelivery['localDelivery']['postalCodeMatch'] = (strpos(strtolower($origin_array['match_postal_local_delivery']), strtolower($destination_zip)) !== FALSE) ? 1 : 0;
            }

            (!empty($InstorPickupLocalDelivery)) ? $post_data['InstorPickupLocalDelivery'] = $InstorPickupLocalDelivery : $post_data;

            return $post_data;
        }

        public function en_wd_standard_plans($post_data, $destination_zip, $origin_array, $package_plugin)
        {
            $action = $this->check_package_plan($package_plugin);
            if ($action) {
                return $post_data;
            }

            return $this->set_enabled_enable_packages($destination_zip, $origin_array, $post_data);
        }

        /**
         * updated string for warehouse table
         * @return string
         */
        public function en_wd_update_query_string()
        {
            return " , enable_store_pickup , fee_local_delivery , suppress_local_delivery , miles_store_pickup , match_postal_store_pickup , checkout_desc_store_pickup , enable_local_delivery , miles_local_delivery , match_postal_local_delivery , checkout_desc_local_delivery ";
        }

        /**
         * Origin array
         * @param array type $origin
         * @return array type
         */
        public function en_wd_origin_array_set($origin)
        {
            $zip = (isset($origin->zip)) ? $origin->zip : "";
            $city = (isset($origin->city)) ? $origin->city : "";
            $state = (isset($origin->state)) ? $origin->state : "";
            $country = (isset($origin->country)) ? $origin->country : "";
            $location = (isset($origin->location)) ? $origin->location : "";
            $locationId = (isset($origin->id)) ? $origin->id : "";

            $enable_store_pickup = (isset($origin->enable_store_pickup)) ? $origin->enable_store_pickup : "";
            $fee_local_delivery = (isset($origin->fee_local_delivery)) ? $origin->fee_local_delivery : "";
            $suppress_local_delivery = (isset($origin->suppress_local_delivery)) ? $origin->suppress_local_delivery : "";
            $miles_store_pickup = (isset($origin->miles_store_pickup)) ? $origin->miles_store_pickup : "";
            $match_postal_store_pickup = (isset($origin->match_postal_store_pickup)) ? $origin->match_postal_store_pickup : "";
            $checkout_desc_store_pickup = (isset($origin->checkout_desc_store_pickup)) ? $origin->checkout_desc_store_pickup : "";
            $enable_local_delivery = (isset($origin->enable_local_delivery)) ? $origin->enable_local_delivery : "";
            $miles_local_delivery = (isset($origin->miles_local_delivery)) ? $origin->miles_local_delivery : "";
            $match_postal_local_delivery = (isset($origin->match_postal_local_delivery)) ? $origin->match_postal_local_delivery : "";
            $checkout_desc_local_delivery = (isset($origin->checkout_desc_local_delivery)) ? $origin->checkout_desc_local_delivery : "";


            return array(
                'locationId' => $locationId,
                'zip' => $zip,
                'city' => $city,
                'state' => $state,
                'location' => $location,
                'country' => $country,
                'enable_store_pickup' => $enable_store_pickup,
                'fee_local_delivery' => $fee_local_delivery,
                'suppress_local_delivery' => $suppress_local_delivery,
                'miles_store_pickup' => $miles_store_pickup,
                'match_postal_store_pickup' => $match_postal_store_pickup,
                'checkout_desc_store_pickup' => $checkout_desc_store_pickup,
                'enable_local_delivery' => $enable_local_delivery,
                'miles_local_delivery' => $miles_local_delivery,
                'match_postal_local_delivery' => $match_postal_local_delivery,
                'checkout_desc_local_delivery' => $checkout_desc_local_delivery,
            );
        }

        /**
         * Check package plan
         */
        function check_package_plan($package)
        {
//          In-store pickup and local delivery
            $instore_pickup_local_devlivery_action = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');

            return (!is_array($instore_pickup_local_devlivery_action)) ? FALSE : TRUE;
        }

    }

    new EnWDQuotes();
}