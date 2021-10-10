<?php

/**
 * Includes Carrier Service Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsCarrierService")) {

    class EnWooAddonsCarrierService extends EnWooAddonsQuoteSettings
    {

        public $always_auto_residential_accessorial;
        public $always_lift_gate_quote_accessorial;
        public $post_data;
        public $cart;
        public $cart_arry;
        public $request_key;
        public $microtime_carrier;
        public $EnWooAddonsQuoteSettings;
        public $plugin_id;
        public $en_addresses_enabled;

        public function __construct()
        {

            parent::__construct();
            add_filter('en_woo_addons_carrier_service_quotes_request', array($this, 'carrier_service_quotes_request'), 10, 2);
        }

        /**
         * set index autoResidentials for carrier request
         * @return array type
         */
        public function auto_residential_enable()
        {

            $this->post_data['autoResidentials'] = '1';
        }

        /**
         * set index liftGateWithAutoResidentials for carrier request
         * @return array type
         */
        public function auto_resid_wd_lift_enable()
        {

            $this->post_data['liftGateWithAutoResidentials'] = '1';
        }

        /**
         * carrier request receive from standard plugin with plugin id
         * @param array type $post_data
         * @param string type $plugin_id
         * @return array type
         */
        public function carrier_service_quotes_request($post_data, $plugin_id)
        {

            $this->post_data = $post_data;
            $this->plugin_id = $plugin_id;
            $this->en_addresses_enabled = FALSE;

            if (isset($this->plugins_dependencies[$plugin_id]) && $this->get_validate_street_address()) {

                $this->post_data = $this->update_carrier_service();
            }
            return $this->post_data;
        }

        /**
         * update carrier service for every carrier request
         * @return array type
         */
        public function update_carrier_common()
        {
            $en_post_data = [];
            $EnWooAddonsGenrtRequestKey = new EnWooAddonsGenrtRequestKey();
            $this->post_data['requestKey'] = $EnWooAddonsGenrtRequestKey->en_woo_addons_genrt_request_key();
            $this->post_data['server_name'] = en_residential_get_domain();
            $en_post_data['addressLine'] = $this->post_data['addressLine'] = (isset($_POST['s_address'])) ? $this->trim_string($_POST['s_address']) : $this->shipping_address;
            $en_post_data['addressLine2'] = $this->post_data['addressLine2'] = (isset($_POST['s_address_2'])) ? $this->trim_string($_POST['s_address_2']) : $this->shipping_address_2;

            if (!$this->en_addresses_enabled) {
                $en_default_unconfirmed_address_types_to = get_option('en_default_unconfirmed_address_types_to');
                $en_post_data['defaultRADAddressType'] = $this->post_data['defaultRADAddressType'] = ($en_default_unconfirmed_address_types_to && strlen($en_default_unconfirmed_address_types_to) > 0) ? $en_default_unconfirmed_address_types_to : 'residential';
            }

            if (!isset($this->post_data['receiverCountryCode'])) {
                $en_post_data['receiverCountryCode'] = $this->post_data['receiverCountryCode'] = (isset($_POST['s_country'])) ? $this->trim_string($_POST['s_country']) : $this->shipping_country;
            }

            if ($this->plugin_id == 'cerasis_freights' && isset($this->post_data['receiverAddress'])) {
                $this->post_data['receiverAddress'] = $this->post_data['receiverAddress'] + $en_post_data;
            }

            if ($this->plugin_id == "fedex_small") {
                $this->post_data['receiverStreetLines'] = (isset($_POST['s_address'])) ? array($this->trim_string($_POST['s_address'])) : array($this->shipping_address);
            }
        }

        /**
         * update carrier service for autoResidentials & liftGateWithAutoResidentials
         * @return array type
         */
        public function update_carrier_service()
        {

            if ($this->rtrn_valid_option_val("option_1", $this->plugin_id) &&
                $this->rtrn_valid_fr_addon('addon_1', $this->plugin_id) &&
                $this->get_validate_street_address()) {

                $eniture_small_apps = [
                    'fedex_small' => ['residential_delivery' => 'on'],
                    'unishepper_small' => ['REP'],
                    'ups_small' => ['ups_small_pkg_resid_delivery' => 'yes'],
                    'wwe_small_packages_quotes' => ['residentials_delivery' => 'yes'],
                ];

                if (isset($eniture_small_apps[$this->plugin_id])) {
                    $address_fixed = post_exists($this->get_address_on_checkout_page);
                    if ($address_fixed > 0) {
                        $post_content = get_post_field('post_content', $address_fixed);
                        if ($post_content == 'residential') {
                            if ($this->plugin_id == 'unishepper_small') {
                                (isset($this->post_data['accessorial'])) ? $this->post_data['accessorial'] = array_merge($this->post_data['accessorial'], $eniture_small_apps[$this->plugin_id]) : $this->post_data['accessorial'] = $eniture_small_apps[$this->plugin_id];
                            } else {
                                $this->post_data = array_merge($this->post_data, $eniture_small_apps[$this->plugin_id]);
                            }
                        }

                        $this->en_addresses_enabled = TRUE;
                    } else {
                        $this->auto_residential_enable();
                    }
                } else {
                    $this->auto_residential_enable();
                }
                $this->rtrn_valid_option_val("option_2", $this->plugin_id) && $this->rtrn_valid_fr_addon('addon_2', $this->plugin_id) ?
                    $this->auto_resid_wd_lift_enable() : "";
            }

            $this->update_carrier_common();

            return $this->post_data;
        }

    }

    new EnWooAddonsCarrierService();
}