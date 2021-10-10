<?php

/**
 * Auto residential template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonAutoResidDetectionTemplate")) {

    class EnWooAddonAutoResidDetectionTemplate extends EnWooAddonsFormHandler
    {

        public $subscriptionInfo;
        public $subscribedPackage;
        public $subscribedPackageHitsStatus;
        public $nextSubcribedPackage;
        public $statusRequestTime;
        public $subscriptionStatus;
        public $plugin_name;
        public $status;
        public $EnWooAddonsAjaxReqIncludes;
        public $EnWooAddonsCurlReqIncludes;
        public $reset_always_auto_residential;
        public $reset_always_auto_residential_id;
        public $settings;
        public $auto_resid_dependencies;
        public $next_subcribed_package;
        public $subscription_details;
        public $lastUsageTime;
        public $subscription_packages_response;

        public function __construct()
        {

            $this->EnWooAddonsAjaxReqIncludes = new EnWooAddonsAjaxReqIncludes();
            $this->EnWooAddonsCurlReqIncludes = new EnWooAddonsCurlReqIncludes();
        }

        /**
         * unset the given fields from settings array
         * @return array type
         */
        public function unset_autoresidential_fields($auto_resid_dependencies)
        {

            $unset_fields = $auto_resid_dependencies['unset_fields'];
//             unset fields from @param $settings array standard plugin 
            if (isset($unset_fields) && (!empty($unset_fields)) && (is_array($unset_fields))) {

                foreach ($unset_fields as $key => $value) {

                    unset($this->settings[$value]);
                }
            }

            return $this->settings;
        }

        /**
         * reset the existing Always residential detection field
         * @return array type
         */
        public function reset_autoresidential_fields($auto_resid_dependencies, $en_need_suspended_rad = FALSE, $en_always_rad_need_disabled = FALSE)
        {

            $this->reset_always_auto_residential = $this->get_arr_filterd_val($auto_resid_dependencies['reset_always_auto_residential']);

            if (isset($this->reset_always_auto_residential) && (!empty($this->reset_always_auto_residential))) {
                $after_index_fields = "residential_delivery_options_label";
                $this->reset_always_auto_residential_id = (isset($this->settings[$this->reset_always_auto_residential]['id'])) ? $this->settings[$this->reset_always_auto_residential]['id'] : "en_woo_addons_always_include_residential_fee";
                $this->settings[$this->reset_always_auto_residential]['class'] = (isset($this->settings[$this->reset_always_auto_residential]['class'])) ? $this->settings[$this->reset_always_auto_residential]['class'] : "NA";
                if ($en_always_rad_need_disabled) {
                    $this->settings[$this->reset_always_auto_residential]['desc'] = "<span class='en_need_suspended_rad_content'>Residential address settings are being managed under the RAD (Residential Address Detection) page, which you can access in this plugin's navigation above. To use this setting, you must first deactivate or suspend the Residential Address Detection plugin</span>";
                } else if ($en_need_suspended_rad) {
//                    $this->settings[$this->reset_always_auto_residential]['desc'] = "";
                    $this->settings[$this->reset_always_auto_residential]['class'] .= " en_woo_addons_always_include_residential_fee en_need_suspended_rad";
                } else {
                    $this->settings[$this->reset_always_auto_residential]['class'] .= " en_woo_addons_always_include_residential_fee";
                }
                $reset_auto_residential[$this->reset_always_auto_residential] = $this->settings[$this->reset_always_auto_residential];
                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields, $reset_auto_residential);
            }

            return $this->settings;
        }

        /**
         * Updated auto_residential_detection_addon array return to standard plugin
         * @return array type
         */
        public function auto_residential_detection_addon($settings, $addons, $plugin_name)
        {

            $this->plugin_name = $plugin_name;
            $this->settings = $settings;
            $auto_resid_dependencies = $this->auto_resid_dependencies = $addons['auto_residential_detection_addon'];
            $after_index_fields = $this->get_arr_filterd_val($this->auto_resid_dependencies['after_index_fields']);

            if (isset($after_index_fields) && (!empty($after_index_fields)) && (isset($this->settings[$after_index_fields]))) {

                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields, $this->auto_residential_update_fields_arr());
                $this->settings = $this->reset_autoresidential_fields($auto_resid_dependencies);
                $this->settings = $this->unset_autoresidential_fields($auto_resid_dependencies);

                if (isset($this->subscription_packages_response, $this->reset_always_auto_residential_id) &&
                    $this->subscription_packages_response == "yes" &&
                    (!empty($this->reset_always_auto_residential_id)) &&
                    (get_option("suspend_automatic_detection_of_residential_addresses") != "yes")) {

                    update_option($this->reset_always_auto_residential_id, "no");
                }
            }

            $this->settings = apply_filters('en_woo_addons_auto_residential_detection_updated_filters', $this->settings);
            return $this->settings;
        }

        /**
         * Updated auto_residential_detection_addon array return to standard plugin
         * @return array type
         */
        public function always_rad_and_auto_residential_detection_action($settings, $addons, $plugin_name)
        {

            $this->plugin_name = $plugin_name;
            $this->settings = $settings;
            $auto_resid_dependencies = $this->auto_resid_dependencies = $addons['auto_residential_detection_addon'];
            $after_index_fields = $this->get_arr_filterd_val($this->auto_resid_dependencies['after_index_fields']);

            if (isset($after_index_fields) && (!empty($after_index_fields)) && (isset($this->settings[$after_index_fields]))) {

                $this->settings = $this->reset_autoresidential_fields($auto_resid_dependencies, TRUE);
                $this->settings = $this->unset_autoresidential_fields($auto_resid_dependencies);
                $this->subscription_packages_response = get_option('en_woo_addons_auto_residential_detecion_flag');
                if (isset($this->subscription_packages_response, $this->reset_always_auto_residential_id) &&
                    $this->subscription_packages_response == "yes" &&
                    (!empty($this->reset_always_auto_residential_id)) &&
                    (get_option("suspend_automatic_detection_of_residential_addresses") != "yes")) {

                    $this->settings = $this->reset_autoresidential_fields($auto_resid_dependencies, FALSE, TRUE);
                    update_option($this->reset_always_auto_residential_id, "no");
                }
            }

            $this->settings = apply_filters('en_woo_addons_auto_residential_detection_updated_filters', $this->settings);
            return $this->settings;
        }

        /**
         * Smart street api response curl from server
         * @return array type
         */
        public function customer_subscription_status()
        {
            $this->en_trial_activation_residential();
            $status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("s", $this->plugin_name);
            $this->en_check_plugin_status($status);
            $status = json_decode($status, true);
            return $status;
        }

        /**
         * Trial activation of Residential.
         */
        public function en_trial_activation_residential()
        {
            $trial_status = '';
            /* Trial activation code */
            $trial_status_residential = get_option('en_trial_residential_flag');

            if (!$trial_status_residential) {
                $trial_status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("c", $this->plugin_name, 'TR');
                $response_status = json_decode($trial_status);
                /* Trial Package activated succesfully */
                if (isset($response_status->severity) && $response_status->severity == "SUCCESS") {
                    update_option('en_trial_residential_flag', 1);
                }
                /* Error response */
                if (isset($response_status->severity) && $response_status->severity == "ERROR") {
                    /* Do anthing in case of error */
                }
            }
        }

        /**
         * Check plugin status.
         */
        public function en_check_plugin_status($current_status)
        {
            $current_status = json_decode($current_status);
            if (
                isset($current_status->status->subscribedPackage->packageSCAC) &&
                $current_status->status->subscribedPackage->packageSCAC == 'TR'
            ) {
                $plugin_status = $this->EnWooAddonsCurlReqIncludes->smart_street_api_curl_request("pluginType", $this->plugin_name, '');
                $decoded_plugin_status = json_decode($plugin_status);
                if ($decoded_plugin_status->severity == "SUCCESS") {
                    if ($decoded_plugin_status->pluginType == "trial") {
                        /* Plugin not activated notification */
                        echo '<div id="message" class="notice-dismiss-residential notice-dismiss-residential-php notice-warning notice is-dismissible"><p>The LTL Freight / Small Packages Quotes plugin must have an active paid license to continue to use this feature.</p><button type="button" class="notice-dismiss notice-dismiss-bin"><span class="screen-reader-text notice-dismiss-bin">Dismiss this notice.</span></button></div>';
                    }
                }
            }
        }

        /**
         * All packages list auto residential detection
         * @param type $packages_list
         * @return string
         */
        public function packages_list($packages_list)
        {

            $packages_list_arr = array();
            if (isset($packages_list) && (!empty($packages_list))) {

                $packages_list_arr['options']['disable'] = 'Disable (default)';
                foreach ($packages_list as $key => $value) {

                    $value['pPeriod'] = (isset($value['pPeriod']) && ($value['pPeriod'] == "Month")) ? "mo" : $value['pPeriod'];
                    $value['pHits'] = is_numeric($value['pHits']) ? number_format($value['pHits']) : $value['pHits'];
                    $value['pCost'] = is_numeric($value['pCost']) ? number_format($value['pCost']) : $value['pCost'];

                    $trial = (isset($value['pSCAC']) && $value['pSCAC'] == "TR") ? "(Trial)" : "";
                    $packages_list_arr['options'][$value['pSCAC']] = $value['pHits'] . "/" . $value['pPeriod'] . " ($" . $value['pCost'] . ".00)" . " " . $trial;
                }
            }
            return $packages_list_arr;
        }

        /**
         * Ui display for next plan
         * @return string type
         */
        public function next_subcribed_package()
        {
            $this->next_subcribed_package = (isset($this->nextSubcribedPackage['nextToBeChargedStatus']) && $this->nextSubcribedPackage['nextToBeChargedStatus'] == 1) ? $this->nextSubcribedPackage['nextSubscriptionSCAC'] : "disable";
            return $this->next_subcribed_package;
        }

        /**
         * UI display subcribed package
         * @return string type
         */
        public function subscribed_package()
        {

            $subscribed_package = $this->subscribedPackage;
            $subscribed_package['packageDuration'] = (isset($subscribed_package['packageDuration']) && ($subscribed_package['packageDuration'] == "Month")) ? "mo" : $subscribed_package['packageDuration'];
            $subscribed_package['packageHits'] = is_numeric($subscribed_package['packageHits']) ? number_format($subscribed_package['packageHits']) : $subscribed_package['packageHits'];
            $subscribed_package['packageCost'] = is_numeric($subscribed_package['packageCost']) ? number_format($subscribed_package['packageCost']) : $subscribed_package['packageCost'];
            return $subscribed_package['packageHits'] . "/" . $subscribed_package['packageDuration'] . " ($" . $subscribed_package['packageCost'] . ".00)";
        }

        /**
         * Response from smart street api and set in public attributes
         */
        function set_curl_res_attributes()
        {

            $this->subscriptionInfo = (isset($this->status['status']['subscriptionInfo'])) ? $this->status['status']['subscriptionInfo'] : "";
            $this->lastUsageTime = (isset($this->status['status']['lastUsageTime'])) ? $this->status['status']['lastUsageTime'] : "";
            $this->subscribedPackage = (isset($this->status['status']['subscribedPackage'])) ? $this->status['status']['subscribedPackage'] : "";
            $this->subscriptionStatus = (isset($this->status['status']['subscriptionInfo']['subscriptionStatus'])) ? ($this->status['status']['subscriptionInfo']['subscriptionStatus'] == 1) ? "yes" : "no" : "";
            $this->subscribedPackageHitsStatus = (isset($this->status['status']['subscribedPackageHitsStatus'])) ? $this->status['status']['subscribedPackageHitsStatus'] : "";
            $this->nextSubcribedPackage = (isset($this->status['status']['nextSubcribedPackage'])) ? $this->status['status']['nextSubcribedPackage'] : "";
            $this->statusRequestTime = (isset($this->status['statusRequestTime'])) ? $this->status['statusRequestTime'] : "";
        }

        /**
         * UI display Current Subscription & Current Usage
         * @param array type $status
         * @return array type
         */
        public function subscription($status = array())
        {

            if (isset($status) && (!empty($status)) && (is_array($status))) {

                $this->status = $status;
            } else {

                $this->status = $this->customer_subscription_status();
                $packages_list = isset($this->status['ListOfPackages']['Info']) ? $this->status['ListOfPackages']['Info'] : [];
                if (isset($packages_list) && (!empty($packages_list)) && is_array($packages_list)) {

                    $packages_list = $this->packages_list($packages_list);
                } else {

                    $packages_list = array(
                        'options' => array(
                            'disable' => 'Disable (default)'
                        )
                    );
                }
            }

            // Request handle separately with every plugin
            $auto_residential_detecion_extra_flag = 'en_woo_addons_auto_residential_detecion_flag_' . $this->plugin_name;
            if (isset($this->status['severity']) && ($this->status['severity'] == "SUCCESS")) {

                $this->set_curl_res_attributes();
                if (isset($this->lastUsageTime) && $this->lastUsageTime == '0000-00-00 00:00:00') {

                    $this->lastUsageTime = 'yyyy-mm-dd hrs-min-sec';
                }
                $subscription_time = (isset($this->subscriptionInfo) && (!empty($this->subscriptionInfo['subscriptionTime']))) ? "Start date: " . $this->formate_date_time($this->subscriptionInfo['subscriptionTime']) : "NA";
                $status_request_time = (isset($this->lastUsageTime) && (!empty($this->lastUsageTime))) ? '(' . $this->lastUsageTime . ')' : "NA";
                $expiry_time = (isset($this->subscriptionInfo) && (!empty($this->subscriptionInfo['expiryTime']))) ? "End date: " . $this->formate_date_time($this->subscriptionInfo['expiryTime']) : "NA";
                $subscribed_package = (isset($this->subscribedPackage) && (!empty($this->subscribedPackage))) ? $this->subscribed_package() : "NA";
                $consumed_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHits']))) ? $this->subscribedPackageHitsStatus['consumedHits'] : "";
                $available_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['availableHits']))) ? $this->subscribedPackageHitsStatus['availableHits'] . "/" : "NA";
                $consumedHits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHits']))) ? $this->subscribedPackageHitsStatus['consumedHits'] . "/" : "0/";
                $consumed_hits_prcent = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['consumedHitsPrcent']))) ? $this->subscribedPackageHitsStatus['consumedHitsPrcent'] . "%" : "0%";
                $package_hits = (isset($this->subscribedPackageHitsStatus) && (!empty($this->subscribedPackageHitsStatus['packageHits']))) ? $this->subscribedPackageHitsStatus['packageHits'] : "/NA";
                $next_subcribed_package = (isset($this->nextSubcribedPackage) && (!empty($this->nextSubcribedPackage))) ? $this->next_subcribed_package() : "NA";

                if ($this->subscriptionStatus == "yes") {
                    // Request handle separately with every plugin
                    $options = array(
                        $auto_residential_detecion_extra_flag => $this->subscriptionStatus,
                        "en_woo_addons_auto_residential_detecion_flag" => $this->subscriptionStatus,
                        "auto_residential_delivery_plan_auto_renew" => $this->next_subcribed_package
                    );
                    $this->EnWooAddonsAjaxReqIncludes->update_db($options);
                    $current_subscription = '<span id="subscribed_package">' . $subscribed_package . '</span>'
                        . '&nbsp;&nbsp;&nbsp; '
                        . '<span id="subscription_time">' . $subscription_time . '</span>'
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="expiry_time">' . $expiry_time . '</span>';
                    $current_usage = '<span id="subscribed_package_status">' . $consumedHits . $package_hits . '</span> '
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="consumed_hits_prcent">' . $consumed_hits_prcent . '</span>'
                        . '&nbsp;&nbsp;&nbsp;'
                        . '<span id="status_request_time">' . $status_request_time . '</span>';

                    $this->subscription_packages_response = "yes";
                } else {

                    $this->subscription_packages_response = "no";
                    $current_subscription = '<span id="subscribed_package">Your current subscription is expired.</span>';
                    $current_usage = 'Not available.';
                    if (isset($next_subcribed_package) && ($next_subcribed_package != 'NA')) {

                        $options = array("suspend_automatic_detection_of_residential_addresses" => "no",
                            "en_woo_addons_liftgate_with_auto_residential" => "no");
                        $this->EnWooAddonsAjaxReqIncludes->update_db($options);
                    }
                }
            } else {
                // Request handle separately with every plugin
                $this->subscription_packages_response = "no";
                $options = array(
                    "suspend_automatic_detection_of_residential_addresses" => "no",
                    "en_woo_addons_liftgate_with_auto_residential" => "no",
                    "en_woo_addons_auto_residential_detecion_flag" => "no",
                    $auto_residential_detecion_extra_flag => "no",
                );
                $this->EnWooAddonsAjaxReqIncludes->update_db($options);
                $current_subscription = '<span id="subscribed_package">Not subscribed.</span>';
                $current_usage = 'Not available.';
//               when no plan exist plan go to dislable 
                $next_subcribed_package = "disable";
            }
            $this->subscription_details = array('current_usage' => (isset($current_usage)) ? $current_usage : "",
                'current_subscription' => (isset($current_subscription)) ? $current_subscription : "",
                'next_subcribed_package' => (isset($next_subcribed_package)) ? $next_subcribed_package : "",
                'packages_list' => (isset($packages_list)) ? $packages_list : "",
                'subscription_packages_response' => $this->subscription_packages_response);
            return $this->subscription_details;
        }

        /**
         * new fields add for auto residential detection
         * @return array
         */
        public function auto_residential_update_fields_arr($settings = [], $addons = [], $plugin_name = '', $reset_always_auto_residential_id = '')
        {
            if (isset($plugin_name, $settings, $addons) && strlen($plugin_name) > 0) {
                $this->plugin_name = $plugin_name;
                $this->settings = $settings;
                $this->auto_resid_dependencies = $addons['auto_residential_detection_addon'];
            }

            extract($this->subscription());

            if (isset($reset_always_auto_residential_id) && strlen($reset_always_auto_residential_id) > 0) {
                if (isset($this->subscription_packages_response) &&
                    $this->subscription_packages_response == "yes" &&
                    (get_option("suspend_automatic_detection_of_residential_addresses") != "yes")) {

                    update_option($reset_always_auto_residential_id, "no");
                }
            }

            $auto_residential_updated_settings = array(
                'residential_delivery_options_label' => array(
                    'name' => __('Residential Delivery', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'id' => 'residential_delivery_options_label'
                ),
                'residential_delivery_options_label_heading' => array(
                    'name' => __('Auto-detect residential addresses ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'id' => 'residential_delivery_options_label_heading',
                ),
                'residential_delivery_options_label_description' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'desc' => 'The plugin will automatically detect residential addresses when this feature is enabled. When a residential address is detected, the residential delivery fee will be included in the carrier’s rate estimates. The next subscription begins when the current one expires or is depleted, which ever comes first. Refer to the <a href="https://eniture.com/woocommerce-residential-address-detection/#documentation" target="_blank">User Guide</a> for more detailed information.',
                    'class' => 'hidden',
                    'id' => 'residential_delivery_options_label_description'
                ),
                'auto_residential_delivery_plan_auto_renew' => array(
                    'name' => __('Auto-renew ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'select',
                    'default' => $next_subcribed_package,
                    'id' => 'auto_residential_delivery_plan_auto_renew',
                    'options' => $packages_list['options']
                ),
                'residential_delivery_current_subscription' => array(
                    'name' => __('Current plan', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'desc' => $current_subscription,
                    'id' => 'residential_delivery_current_subscription'
                ),
                'residential_delivery_current_usage' => array(
                    'name' => __('Current usage', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'desc' => $current_usage,
                    'id' => 'residential_delivery_current_usage'
                ),
                'suspend_automatic_detection_of_residential_addresses' => array(
                    'name' => __('Suspend use', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'checkbox',
                    'id' => 'suspend_automatic_detection_of_residential_addresses',
                    'desc' => __(' ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'class' => 'suspend_automatic_detection_of_residential_addresses'
                ),
                'en_default_unconfirmed_address_types_to' => [
                    'name' => __('Default unconfirmed address types to:', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'radio',
                    'default' => 'residential',
                    'id' => 'en_default_unconfirmed_address_types_to',
                    'class' => 'en_default_unconfirmed_address_types_to',
                    'options' => [
                        'residential' => __('Residential', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'commercial' => __('Commercial', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    ]
                ],
                'residential_delivery_plugin_name' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'placeholder' => $this->plugin_name,
                    'id' => "residential_delivery_plugin_name",
                ),
                'residential_delivery_subscription_status' => array(
                    'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => "hidden",
                    'placeholder' => $this->subscriptionStatus,
                    'id' => "residential_delivery_subscription_status",
                ),
            );

            $auto_residential_updated_settings = apply_filters('en_woo_addons_auto_residential_new_fields_filters', $auto_residential_updated_settings);
            return $auto_residential_updated_settings;
        }

    }

    new EnWooAddonAutoResidDetectionTemplate();
}