<?php

/**
 * Includes Carrier Service Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("UninstallResidentialAddressDetection")) {

    class UninstallResidentialAddressDetection {

        public function __construct() {
            delete_option("en_woo_addons_auto_residential_detecion_flag");
            delete_option("en_woo_addons_liftgate_delivery_as_option");
            delete_option("en_woo_addons_liftgate_with_auto_residential");
            delete_option("auto_residential_delivery_plan_auto_renew");
            delete_option("suspend_automatic_detection_of_residential_addresses");
        }

    }

    new UninstallResidentialAddressDetection();
}