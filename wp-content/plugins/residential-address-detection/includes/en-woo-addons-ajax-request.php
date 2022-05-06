<?php

/**
 * Includes Ajax Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsAjaxReqIncludes")) {

    class EnWooAddonsAjaxReqIncludes extends EnWooAddonsInclude {

        public $plugin_standards;
        public $selected_plan;
        public $EnWooAddonAutoResidDetectionTemplate;

        /**
         * construct
         */
        public function __construct() {
            /**
             * Auto detect residential ajax request
             */
            add_action('wp_ajax_nopriv_en_woo_addons_upgrade_plan_submit', array($this, 'en_woo_addons_upgrade_plan_submit'));
            add_action('wp_ajax_en_woo_addons_upgrade_plan_submit', array($this, 'en_woo_addons_upgrade_plan_submit'));

            /**
             * Suspend automatic detection of residential addressest
             */
            add_action('wp_ajax_nopriv_suspend_automatic_detection', array($this, 'suspend_automatic_detection'));
            add_action('wp_ajax_suspend_automatic_detection', array($this, 'suspend_automatic_detection'));

            /**
             * When click on always RAD with disabled plan of RAD.
             */
            add_action('wp_ajax_nopriv_en_need_suspended_rad_ajax', array($this, 'en_need_suspended_rad_ajax'));
            add_action('wp_ajax_en_need_suspended_rad_ajax', array($this, 'en_need_suspended_rad_ajax'));
        }

        /**
         * When click on always RAD with disabled plan of RAD.
         */
        public function en_need_suspended_rad_ajax() {
            $en_need_suspended_rad = FALSE;
            $subscription_packages_response = get_option('en_woo_addons_auto_residential_detecion_flag');
            if (isset($subscription_packages_response) &&
                    $subscription_packages_response == "yes" &&
                    (get_option("suspend_automatic_detection_of_residential_addresses") != "yes")) {

                $en_need_suspended_rad = TRUE;
            }

            echo json_encode(['en_need_suspended_rad' => $en_need_suspended_rad]);
            die();
        }

        public function suspend_automatic_detection() {

            $options = array();
            $always_include_residential_ind = ( isset($_POST['always_include_residential_ind']) ) ? sanitize_text_field($_POST['always_include_residential_ind']) : '';
            $always_include_residential_val = ( isset($_POST['always_include_residential_val']) ) ? sanitize_text_field($_POST['always_include_residential_val']) : '';
            // White Glove
            $rad_include_inside_ind = ( isset($_POST['rad_include_inside_ind']) ) ? sanitize_text_field($_POST['rad_include_inside_ind']) : '';
            $rad_include_inside_val = ( isset($_POST['rad_include_inside_val']) ) ? sanitize_text_field($_POST['rad_include_inside_val']) : '';
            (isset($rad_include_inside_ind) && (!empty($rad_include_inside_ind))) &&
            (isset($rad_include_inside_val) && (!empty($rad_include_inside_val))) ?
                $options[$rad_include_inside_ind] = $rad_include_inside_val : "";
            $suspend_automatic_detection_of_residential_addresses = ( isset($_POST['suspend_automatic_detection_of_residential_addresses']) ) ? sanitize_text_field($_POST['suspend_automatic_detection_of_residential_addresses']) : '';
            $en_woo_addons_liftgate_with_auto_residential = ( isset($_POST['en_woo_addons_liftgate_with_auto_residential']) ) ? sanitize_text_field($_POST['en_woo_addons_liftgate_with_auto_residential']) : '';

            (isset($always_include_residential_ind) && (!empty($always_include_residential_ind))) &&
                    (isset($always_include_residential_val) && (!empty($always_include_residential_val))) ?
                            $options[$always_include_residential_ind] = $always_include_residential_val : "";

            (isset($suspend_automatic_detection_of_residential_addresses) && (!empty($suspend_automatic_detection_of_residential_addresses))) ?
                            $options["suspend_automatic_detection_of_residential_addresses"] = $suspend_automatic_detection_of_residential_addresses : "";

            (isset($en_woo_addons_liftgate_with_auto_residential) && (!empty($en_woo_addons_liftgate_with_auto_residential))) ?
                            $options["en_woo_addons_liftgate_with_auto_residential"] = $en_woo_addons_liftgate_with_auto_residential : "";

            $this->update_db($options);
            echo json_encode($options);
            die();
        }

        /** Update Options 
         * @param type $options
         */
        public function update_db($options) {

            if (isset($options) && (is_array($options))) {
                foreach ($options as $options_name => $options_value) {
                    update_option($options_name, $options_value);
                }
            }
        }

        /**
         * Auto detect residential ajax request
         */
        public function en_woo_addons_upgrade_plan_submit() {

            $packgInd = ( isset($_POST['selected_plan']) ) ? sanitize_text_field($_POST['selected_plan']) : '';
            $plugin_name = ( isset($_POST['plugin_name']) ) ? sanitize_text_field($_POST['plugin_name']) : '';

            $this->plugin_standards = $plugin_name;
            $this->selected_plan = $packgInd;
            $action = isset($packgInd) && ($packgInd == "disable") ? "d" : "c";

            $EnWooAddonsCurlReqIncludes = new EnWooAddonsCurlReqIncludes();
            $status = $EnWooAddonsCurlReqIncludes->smart_street_api_curl_request($action, $this->plugin_standards, $this->selected_plan);
            $status = json_decode($status, true);

            if (isset($status['severity']) && $status['severity'] == "SUCCESS") {
                $options = array("auto_residential_delivery_plan_auto_renew" => $packgInd);
                $this->update_db($options);

//              next subcribed package in database using update option wordpress 
                if (!class_exists("EnWooAddonAutoResidDetectionTemplate")) {

                    include_once( addon_plugin_url . '/admin/templates/en-woo-addon-auto-residential-detection-template.php' );
                }

                $this->EnWooAddonAutoResidDetectionTemplate = new EnWooAddonAutoResidDetectionTemplate();
                $status = $this->EnWooAddonAutoResidDetectionTemplate->subscription($status);
                $status['severity'] = "SUCCESS";
            }

            echo json_encode($status);
            die();
        }

    }

    new EnWooAddonsAjaxReqIncludes();
}