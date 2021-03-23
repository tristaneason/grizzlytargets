<?php

/**
 * Includes Carrier Service Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsWebQuotes")) {

    class EnWooAddonsWebQuotes extends EnWooAddonsQuoteSettings {

        public $quote_arr;
        public $plugin_id;
        public $label_sufex;

        public function __construct() {

            parent::__construct();
            add_filter('en_woo_addons_web_quotes', array($this, 'en_woo_addons_web_quotes_array'), 10, 2);
        }

        public function en_woo_addons_web_quotes_array($rate, $plugin_id) {

            $this->plugin_id = $plugin_id;
            $this->label_sufex = (isset($rate['label_sufex'])) ? $rate['label_sufex'] : array();

            if ($this->rtrn_valid_option_val("option_1", $plugin_id) &&
                    $this->rtrn_valid_fr_addon('addon_1', $this->plugin_id) &&
                    $this->get_validate_street_address()) {

                isset($rate['label_sfx_arr']) && (in_array("R", $rate['label_sfx_arr'])) ? array_unshift($this->label_sufex, "R") : "";

                if ($this->rtrn_valid_option_val("option_2", $plugin_id) &&
                        $this->rtrn_valid_fr_addon('addon_2', $this->plugin_id)) {

                    isset($rate['label_sfx_arr']) && (in_array("L", $rate['label_sfx_arr'])) ? array_push($this->label_sufex, "L") : "";
                }

                $rate['label_sufex'] = array_unique($this->label_sufex);
            }

            return $rate;
        }

    }

    new EnWooAddonsWebQuotes();
}