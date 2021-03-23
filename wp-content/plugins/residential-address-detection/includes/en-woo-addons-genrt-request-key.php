<?php

/**
 * Includes Form Handler
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsGenrtRequestKey")) {

    class EnWooAddonsGenrtRequestKey {

        public function __construct() {
            add_action('woocommerce_checkout_order_processed', array($this, "unset_session_request_key"), 1);
        }

        /**
         * Verify session exist or not 
         */
        public function en_woo_addons_genrt_request_key() {
            $session_request_key = WC()->session->get('session_request_key');
            return (isset($session_request_key) && (strlen($session_request_key) > 0)) ? $session_request_key : $this->set_session_request_key();
        }

        /**
         * Rtrn session request key for carrier request
         * @return string type
         */
        public function get_session_request_key() {
            $session_request_key = WC()->session->get('session_request_key');
            return $session_request_key;
        }

        /**
         * unset session request key when user place order
         */
        public function unset_session_request_key() {
            $session_request_key = WC()->session->get('session_request_key');
            if (isset($session_request_key) && strlen($session_request_key) > 0) {
                WC()->session->set('session_request_key', '');
            }
        }

        /**
         * Generate session request key
         * @return string type
         */
        public function set_session_request_key() {
            $requestKey = md5(microtime() . rand());
            WC()->session->set('session_request_key', $requestKey);
            return $requestKey;
        }

    }

}