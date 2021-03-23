<?php

/**
 * Includes Ajax Request class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsCurlReqIncludes")) {

    class EnWooAddonsCurlReqIncludes extends EnWooAddonsFormHandler
    {

        public $plugin_standards;
        public $plugin_license_key;

        /**
         * Return version numbers
         * @return int
         */
        function en_version_numbers()
        {
            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');

            $plugin_folder = get_plugins('/' . 'woocommerce');
            $plugin_file = 'woocommerce.php';
            $wc_plugin = (isset($plugin_folder[$plugin_file]['Version'])) ? $plugin_folder[$plugin_file]['Version'] : "";
            $get_plugin_data = get_plugin_data(RAD_MAIN_FILE);
            $plugin_version = (isset($get_plugin_data['Version'])) ? $get_plugin_data['Version'] : '';

            $versions = array(
                "woocommerce_plugin_version" => $wc_plugin,
                "en_current_plugin_version" => $plugin_version
            );

            return $versions;
        }

        /**
         * Smart street curl api response from server
         * @param type $action
         * @return type
         */
        public function smart_street_api_curl_request($action, $plugin_name = "", $selected_plan = "")
        {
            // Version numbers
            $plugin_versions = $this->en_version_numbers();

            if (isset($plugin_name) && (!empty($plugin_name))) {
                $this->plugin_standards = array("plugin_name" => $plugin_name);
            }

            $plugin_standards = $this->plugin_standards;
            $plugin_dependies = $this->plugins_dependencies();
            $plugin_dependies = isset($plugin_dependies[$plugin_standards['plugin_name']]) ? $plugin_dependies[$plugin_standards['plugin_name']] : [];
            $this->plugin_license_key = isset($plugin_dependies['license_key']) ? get_option($this->get_arr_filterd_val($plugin_dependies['license_key'])) : '';

            $postArr = array(
                // Version numbers
                'plugin_version' => $plugin_versions["en_current_plugin_version"],
                'wordpress_version' => get_bloginfo('version'),
                'woocommerce_version' => $plugin_versions["woocommerce_plugin_version"],

                'platform' => 'Wordpress',
                'version' => '1.0',
                'requestKey' => md5(microtime() . rand()),
                'action' => $action,
                'package' => (isset($selected_plan) && (!empty($selected_plan))) ? $selected_plan : "",
                'serverName' => en_residential_get_domain(),
                'licenseKey' => $this->plugin_license_key,
            );

            /* Check if URL contains folder */
            if ($this->en_check_url_contains_folder()) {
                $postArr['webHookUrl'] = get_site_url();
            }

            $url = "https://ws050.eniture.com/addon/rad/index.php";
            return $this->en_get_residential_curl_response($url, $postArr);
        }

        /**
         * Send https request.
         * @param string $url
         * @param array $postData
         * @return type
         */
        public function en_get_residential_curl_response($url, $postData)
        {
            if (!empty($url) && !empty($postData)) {
                $field_string = http_build_query($postData);
                $response = wp_remote_post($url, array(
                        'method' => 'POST',
                        'timeout' => 60,
                        'redirection' => 5,
                        'blocking' => true,
                        'body' => $field_string,
                    )
                );

                $output = wp_remote_retrieve_body($response);
                return $output;
            }
        }

        /**
         * Function detect site contains folder.
         */
        public function en_check_url_contains_folder()
        {
            $url = get_site_url();
            $url = preg_replace('#^https?://#', '', $url);
            $urlArr = explode("/", $url);
            if (isset($urlArr[1]) && !empty($urlArr[1])) {
                return true;
            }
            return false;
        }

    }

    new EnWooAddonsCurlReqIncludes();
}