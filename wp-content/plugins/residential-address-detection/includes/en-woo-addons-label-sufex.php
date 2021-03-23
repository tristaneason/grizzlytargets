<?php

/**
 * Includes Carrier Service Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsLabelSufex")) {

    class EnWooAddonsLabelSufex extends EnWooAddonsQuoteSettings {

        public function __construct() {
            
            parent::__construct();
        }
    }

    new EnWooAddonsLabelSufex();
}