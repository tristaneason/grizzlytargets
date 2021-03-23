<?php

/**
 * Includes Carrier Service Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsQuoteSettings")) {

    class EnWooAddonsQuoteSettings extends EnWooAddonsFormHandler {

        public $quote_settings;
        public $plugins_dependencies;
        public $liftgate_description;
        public $addons_name;
        public $shipping_address;
        public $shipping_address_2;
        public $shipping_city;
        public $shipping_state;
        public $shipping_postcode;
        public $shipping_country;
        public $get_address_on_checkout_page;

        public function __construct() {
            $this->plugins_dependencies = $this->plugins_dependencies();
            $this->addons_name = array('addon_1' => 'auto_residential_detection_addon',
                'addon_2' => 'lift_gate_delivery_addon',
                'addon_3' => 'box_sizing_addon');

            $this->option_name = array('option_1' => 'auto_residential_detecion_flag',
                'option_2' => 'liftgate_with_auto_residential',
                'option_3' => 'liftgate_delivery_as_option',
                'option_4' => 'auto_residential_plan',
                'option_5' => 'automatic_detection_residential_addresses');

            $this->liftgate_description = " with liftgate delivery";
            $this->quote_settings['auto_residential_detecion_flag'] = get_option("en_woo_addons_auto_residential_detecion_flag");
            $this->quote_settings['liftgate_delivery_as_option'] = get_option("en_woo_addons_liftgate_delivery_as_option");
            $this->quote_settings['liftgate_with_auto_residential'] = get_option("en_woo_addons_liftgate_with_auto_residential");
            $this->quote_settings['auto_residential_plan'] = get_option("auto_residential_delivery_plan_auto_renew");
            $this->quote_settings['suspend_automatic_detection_of_residential_addresses'] = get_option("suspend_automatic_detection_of_residential_addresses");
        }

        /**
         * Get Woocommerce Version
         * @return type
         */
        public function en_get_woo_version_number() {
            // If get_plugins() isn't available, require it
            if (!function_exists('get_plugins'))
                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            // Create the plugins folder and file variables
            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';

            // If the plugin version number is set, return it 
            if (isset($plugin_folder[$plugin_file]['Version'])) {
                return $plugin_folder[$plugin_file]['Version'];
            } else {
                // Otherwise return null
                return NULL;
            }
        }

        /**
         * validation for address, city, post code, city, state on checkout page
         * @return boolean
         */
        public function get_validate_street_address() {
            $WC_VERSION = $this->en_get_woo_version_number();
            $EnWooAddonsAddresses = new EnWooAddonsAddresses($WC_VERSION);
            $version_compare = (version_compare($WC_VERSION, '3.0', '<'));
            $get_shipping_address_1 = $version_compare ? WC()->customer->get_address() : WC()->customer->get_shipping_address_1();
            $get_shipping_address_2 = $version_compare ? WC()->customer->get_address_2() : WC()->customer->get_shipping_address_2();
            $get_shipping_city = $version_compare ? WC()->customer->get_city() : WC()->customer->get_shipping_city();
            $get_shipping_state = $version_compare ? WC()->customer->get_state() : WC()->customer->get_shipping_state();
            $get_shipping_postcode = $version_compare ? WC()->customer->get_postcode() : WC()->customer->get_shipping_postcode();
            $get_shipping_country = $version_compare ? WC()->customer->get_country() : WC()->customer->get_shipping_country();

            $this->shipping_address = (strlen($get_shipping_address_1) > 0) ? $get_shipping_address_1 : $EnWooAddonsAddresses->addons_getAddress1($WC_VERSION);
            $this->shipping_address_2 = (strlen($get_shipping_address_2) > 0) ? $get_shipping_address_2 : $EnWooAddonsAddresses->addons_getAddress2($WC_VERSION);
            $this->shipping_city = (strlen($get_shipping_city) > 0) ? WC()->customer->get_shipping_city() : $EnWooAddonsAddresses->addons_getCity($WC_VERSION);
            $this->shipping_state = (strlen($get_shipping_state) > 0) ? WC()->customer->get_shipping_state() : $EnWooAddonsAddresses->addons_getState($WC_VERSION);
            $this->shipping_postcode = (strlen($get_shipping_postcode) > 0) ? WC()->customer->get_shipping_postcode() : $EnWooAddonsAddresses->addons_postcode($WC_VERSION);
            $this->shipping_country = (strlen($get_shipping_country) > 0) ? WC()->customer->get_shipping_country() : $EnWooAddonsAddresses->addons_getCountry($WC_VERSION);

            $this->get_address_on_checkout_page = strtolower("$this->shipping_address, $this->shipping_address_2, $this->shipping_city, $this->shipping_state, $this->shipping_postcode, $this->shipping_country");

            if ($this->not_empty($this->shipping_address) &&
                    $this->not_empty($this->shipping_city) &&
                    $this->not_empty($this->shipping_state) &&
                    $this->not_empty($this->shipping_country) &&
                    $this->not_empty($this->shipping_postcode)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * return valid addon is active or not 
         * @param string type $addon_name
         * @param strin type $plugin_id
         * @param string type $liftgate_option
         * @return boolean type
         */
        public function rtrn_valid_fr_addon($addon_name, $plugin_id, $liftgate_option = "") {

            $plugin_dep = (isset($this->plugins_dependencies[$plugin_id])) ? $this->plugins_dependencies[$plugin_id] : "";
            $addon_name = (isset($this->addons_name[$addon_name])) ? $this->addons_name[$addon_name] : "";
            $valid_addon = (isset($plugin_dep['addons'][$addon_name]) && ($plugin_dep['addons'][$addon_name]['active'] === true)) ? TRUE : FALSE;
            return $valid_addon;
        }

        /**
         * return the given @apram option is yes or not
         * @param string type $option_name
         * @return boolean type
         */
        public function rtrn_valid_option_val($option_name, $plugin_id) {
            // Request handle separately with every plugin
            $auto_residential_detecion_extra_flag = get_option('en_woo_addons_auto_residential_detecion_flag_' . $plugin_id);
            if ($option_name == 'option_1' && $auto_residential_detecion_extra_flag) {
                return ($auto_residential_detecion_extra_flag == "yes" &&
                        isset($this->quote_settings["suspend_automatic_detection_of_residential_addresses"]) &&
                        $this->quote_settings["suspend_automatic_detection_of_residential_addresses"] != "yes") ? TRUE : FALSE;
            } else {
                $option_name = (isset($this->option_name[$option_name])) ? $this->option_name[$option_name] : "";
                return (isset($this->quote_settings[$option_name]) && $this->quote_settings[$option_name] == "yes" &&
                        isset($this->quote_settings["suspend_automatic_detection_of_residential_addresses"]) &&
                        $this->quote_settings["suspend_automatic_detection_of_residential_addresses"] != "yes") ? TRUE : FALSE;
            }
        }

        /**
         * return filterd unique values
         * @param array type $arr
         * @return array type
         */
        public function rtrn_arr_unique($arr) {

            return array_values(array_unique($arr));
        }

        /**
         * function return array is multidimensional or not
         * @param array type $arr
         * @return array type
         */
        function is_multi_array($arr) {

            rsort($arr);
            return isset($arr[0]) && is_array($arr[0]);
        }

        /**
         * get label sufex for quotes
         * @param array type $label_sufex_arr
         * @param boolean type $liftgate_option
         * @return string type
         */
        public function get_quote_label_sufex_string($label_sufex_arr, $liftgate_option = "") {

            $label_sufex_str = "";
            (isset($liftgate_option) && ($liftgate_option === TRUE)) ?
                            $label_sufex_str = (isset($label_sufex_arr) && (!empty($label_sufex_arr)) && (in_array("R", $label_sufex_arr))) ? "R" : "" :
                            $label_sufex_str = (isset($label_sufex_arr) && (!empty($label_sufex_arr))) ? " ( " . implode(" | ", $label_sufex_arr) . " )" : "";
            return $label_sufex_str;
        }

        /**
         * trim replace whitespaces
         * @param string type $var
         * @return string type
         */
        public function trim_string($var) {

            return sanitize_text_field($var);
        }

        /**
         * validation string validation
         * @param string type $var
         * @return boolean
         */
        public function not_empty($var) {

            $var = $this->trim_string($var);
            if (isset($var) === true && $var === '') {

                return false;
            } else {

                return true;
            }
        }

    }

    new EnWooAddonsQuoteSettings();
}