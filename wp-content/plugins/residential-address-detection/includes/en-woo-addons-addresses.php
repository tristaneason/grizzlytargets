<?php

/**
 * Residential Address Detection Woo Changes
 * 
 * @package     Residential Address Detection
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsAddresses")) {

    class EnWooAddonsAddresses {

        /**
         * Postcode
         * @return string type 
         */
        function addons_postcode($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_postcode() : WC()->customer->get_billing_postcode();
        }

        /**
         * State
         * @return string type
         */
        function addons_getState($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_state() : WC()->customer->get_billing_state();
        }

        /**
         * City
         * @return string type
         */
        function addons_getCity($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_city() : WC()->customer->get_billing_city();
        }

        /**
         * Country
         * @return string type
         */
        function addons_getCountry($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_country() : WC()->customer->get_billing_country();
        }

        /**
         * Address
         * @return string type
         */
        function addons_getAddress1($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_address() : WC()->customer->get_billing_address_1();
        }

        /**
         * Address
         * @return string type
         */
        function addons_getAddress2($WC_VERSION) {

            return (version_compare($WC_VERSION, '3.0', '<')) ? WC()->customer->get_address_2() : WC()->customer->get_billing_address_2();
        }

    }

}
