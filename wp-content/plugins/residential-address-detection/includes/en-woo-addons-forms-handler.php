<?php
/**
 * Includes Form Handler.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsFormHandler")) {

    class EnWooAddonsFormHandler extends EnWooAddonsInclude {

        public $settings;
        public $sections;
        public $section;
        public $plugin_id;
        public $plugins_dependencies;
        public $plugins_dependencies_script_css_files;
        protected $plugin_detail;

        /**
         * construct
         */
        public function __construct() {
            $this->plugins_dependencies = $this->plugins_dependencies();
            $this->plugins_dependencies_script_css_files = $this->plugins_dependencies_script_css_files();
            add_filter('en_woo_addons_sections', array($this, 'en_woo_rad_addons_sections_arr'), 11, 2);
            add_filter('en_woo_addons_settings', array($this, 'en_woo_addons_settings_arr'), 10, 3);
            add_action('woocommerce_settings_tabs_array', array($this, 'en_woo_addons_popup_notifi_disabl_to_plan'), 10);
        }

        /**
         * update sections.
         * @return array type
         */
        public function en_woo_rad_addons_sections_arr($sections, $plugin_id) {
            $this->sections = $sections;
            if (isset($this->plugins_dependencies[$plugin_id])) {
                $plugin_detail = $this->plugins_dependencies[$plugin_id];
                $addons = $plugin_detail['addons'];
                $eniture_small_apps = [
                    'fedex_small',
                    'unishepper_small',
                    'ups_small',
                    'wwe_small_packages_quotes',
                ];
                if (in_array($plugin_id, $eniture_small_apps) && $addons['auto_residential_detection_addon']['active'] === true) {
                    $key = key(array_slice($this->sections, -2, 1));
                    $key = 'section-1';
                    $new = array('section-addresses' => 'RAD');
                    $this->sections = $this->addon_array_insert_after($this->sections, $key, $new);
                }
            }
            return $this->sections;
        }

        /**
         * load_files_plugin_dependencies including css, style, script
         * @param array type $files_arr
         */
        public function load_files_plugin_dependencies($files_arr, $addon_type) {

            foreach ($files_arr as $key => $value) {

                switch ($key) {

                    case "templates":
                        array_filter($value, function ($template) use($addon_type) {

                            include_once( addon_plugin_url . '/admin/templates/' . $template . '.php');
                        });
                        break;

                    case "css":
                        array_filter($value, function ($css_file) use($addon_type) {

                            wp_register_style($addon_type . '_' . $css_file . '_style', plugin_dir_url(__FILE__) . '../admin/assets/css/' . $css_file . '.css', false, '1.3.2');
                            wp_enqueue_style($addon_type . '_' . $css_file . '_style');
                        });
                        break;

                    case "script":
                        array_filter($value, function ($script_file) use($addon_type) {

                            wp_enqueue_script($addon_type . '_' . $script_file . '_script', plugin_dir_url(__FILE__) . '../admin/assets/js/' . $script_file . '.js', array(), '1.1.3');
                            wp_localize_script($addon_type . '_' . $script_file . '_script', 'script', array('pluginsUrl' => plugins_url(),));
                        });

                        break;
                }
            }
        }

        /**
         * Update web api settings array 
         * @param array $settings
         * @param string $section
         * @param string $plugin_id
         * @return array
         */
        public function en_woo_addons_settings_arr($settings, $section, $plugin_id) {

            $this->settings = $settings;
            $this->section = $section;
            $this->plugin_id = $plugin_id;
            $this->settings = $this->get_settings();
            return $this->settings;
        }

        /**
         * Find out exact module is running and return his fields web settings array 
         * @return array
         */
        public function get_settings() {

            if (isset($this->plugins_dependencies[$this->plugin_id])) {

                $plugin_detail = $this->plugins_dependencies[$this->plugin_id];
                $addons = $plugin_detail['addons'];
                $en_residential_addresses = (isset($addons['auto_residential_detection_addon']['en_residential_addresses'])) ? $addons['auto_residential_detection_addon']['en_residential_addresses'] : '';
                $eniture_small_apps = [
                    'fedex_small',
                    'unishepper_small',
                    'ups_small',
                    'wwe_small_packages_quotes',
                ];
                if (!in_array($this->plugin_id, $eniture_small_apps) && (isset($addons['auto_residential_detection_addon']['active'], $addons['auto_residential_detection_addon']['section']) && $addons['auto_residential_detection_addon']['active'] === true && $this->section == $addons['auto_residential_detection_addon']['section'])) {
                    $this->load_files_plugin_dependencies($this->plugins_dependencies_script_css_files['auto_residential_detection_addon'], 'auto_residential_detection_addon');
                    $EnWooAddonAutoResidDetectionTemplate = new EnWooAddonAutoResidDetectionTemplate();
                    $this->settings = $EnWooAddonAutoResidDetectionTemplate->auto_residential_detection_addon($this->settings, $addons, $this->plugin_id);

                    if ($addons['lift_gate_delivery_addon']['active'] === true && $this->section == $addons['lift_gate_delivery_addon']['section']) {

                        $this->load_files_plugin_dependencies($this->plugins_dependencies_script_css_files['lift_gate_delivery_addon'], 'lift_gate_delivery_addon');
                        $this->settings = apply_filters('en_woo_addons_check_for_lift_gate_delivery_option', $this->settings, $addons, $this->plugin_id);
                    }
                } elseif (isset($en_residential_addresses['active']) && $en_residential_addresses['active'] === true && $this->section == $en_residential_addresses['section']) {
                    $start_settings = [
                        'section_start_quote_residential_addresses' => array(
                            'title' => __('', 'woocommerce'),
                            'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'desc' => '',
                            'id' => 'section_start_quote_residential_addresses',
                            'css' => '',
                            'default' => '',
                            'type' => 'title',
                        ),
                        'en_residential_addresses_template_start' => array(
                            'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'en_residential_addresses_template_start'
                        ),
                        'residential_delivery_options_label' => array(
                            'name' => __('Residential Address Detection Plugin Settings', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'residential_delivery_options_label'
                        ),
                        'residential_delivery_options_addon_description' => array(
                            'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'desc' => 'The plugin will automatically detect residential addresses when this add-on plugin is enabled. When a residential address is detected, the residential delivery fee will be included in the carrier’s rate estimates.',
                            'class' => 'hidden',
                            'id' => 'residential_delivery_options_addon_description'
                        ),
                        'residential_delivery_options_label_heading' => array(
                            'name' => __('Subscription Selection ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'residential_delivery_options_label_heading',
                        ),
                        'residential_delivery_options_label_description' => array(
                            'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'desc' => 'Choose a subscription plan below. The next subscription begins when the current one expires or is depleted, which ever comes first. Refer to the <a href="https://eniture.com/woocommerce-residential-address-detection/#documentation" target="_blank">User Guide</a> for more detailed information.',
                            'class' => 'hidden',
                            'id' => 'residential_delivery_options_label_description'
                        ),
                    ];

                    $reset_always_auto_residential_id = (isset($addons['auto_residential_detection_addon']['reset_always_auto_residential_id'])) ? $this->get_arr_filterd_val($addons['auto_residential_detection_addon']['reset_always_auto_residential_id']) : '';

                    $this->load_files_plugin_dependencies($this->plugins_dependencies_script_css_files['auto_residential_detection_addon'], 'auto_residential_detection_addon');
                    $EnWooAddonAutoResidDetectionTemplate = new EnWooAddonAutoResidDetectionTemplate();
                    $rad_settings = $EnWooAddonAutoResidDetectionTemplate->auto_residential_update_fields_arr($this->settings, $addons, $this->plugin_id, $reset_always_auto_residential_id);
                    unset($rad_settings['residential_delivery_options_label']);
                    unset($rad_settings['residential_delivery_options_label_heading']);
                    unset($rad_settings['residential_delivery_options_label_description']);
                    unset($rad_settings['en_default_unconfirmed_address_types_to']);

                    $end_settings = [
                        'en_default_unconfirmed_address_types_label' => array(
                            'name' => __('Default Unconfirmed Addresses', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'en_default_unconfirmed_address_types_label'
                        ),
                        'en_default_unconfirmed_address_types_label_description' => array(
                            'name' => __('', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'desc' => 'In rare cases the plugin may be unable to confirm the address type. This occurs when a match for the address can’t be found in the USPS database. Invalid addresses are the most common reason for this. In extremely rare cases an address may be valid, but the match found in the USPS database has a low confidence metric. Choose how you want addresses treated in these cases.',
                            'class' => 'hidden',
                            'id' => 'en_default_unconfirmed_address_types_label_description'
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
                        'section_end_quote_residential_addresses' => array(
                            'type' => 'sectionend',
                            'id' => 'wc_settings_quote_section_end_residential_addresses'
                        )
                    ];

                    $this->settings = array_merge($start_settings, $rad_settings, $end_settings);
                } else if (in_array($this->plugin_id, $eniture_small_apps) && (isset($addons['auto_residential_detection_addon']['active'], $addons['auto_residential_detection_addon']['section']) && $addons['auto_residential_detection_addon']['active'] === true && $this->section == $addons['auto_residential_detection_addon']['section'])) {
                    $this->load_files_plugin_dependencies($this->plugins_dependencies_script_css_files['auto_residential_detection_addon'], 'auto_residential_detection_addon');
                    $EnWooAddonAutoResidDetectionTemplate = new EnWooAddonAutoResidDetectionTemplate();
                    $this->settings = $EnWooAddonAutoResidDetectionTemplate->always_rad_and_auto_residential_detection_action($this->settings, $addons, $this->plugin_id);
                }
            }

            return $this->settings;
        }

        /**
         * Array merge after specific index 
         * @param array $array
         * @param index of array $key
         * @param array $new
         * @return array
         */
        public function addon_array_insert_after(array $array, $key, array $new) {

            if (isset($key) && in_array($key, array_keys($array))) {

                $keys = array_keys($array);
                $index = array_search($key, $keys);
                $pos = false === $index ? count($array) : $index + 1;
                $array = array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
            }

            return $array;
        }

        /**
         * Formate the given date time @param $datetime like in sow
         * @param datetime $datetime
         * @return string 
         */
        public function formate_date_time($datetime) {

            $date = date_create($datetime);
            return date_format($date, "M. d, Y");
        }

        /**
         * get_arr_filterd_val function see for if @param $arr_val type is array reset value return
         * @param array or string type $arr_val
         * @return string type
         */
        public function get_arr_filterd_val($arr_val) {

            return (isset($arr_val) && (!empty($arr_val)) ) ? (is_array($arr_val)) ? reset($arr_val) : $arr_val : "";
        }

        /**
         * Popup notification for using notification show during disable to plan through using jquery
         * @return html
         */
        public function en_woo_addons_popup_notifi_disabl_to_plan() {
            ?>
            <div id="plan_confirmation_popup" class="sm_notification_disable_to_plan_overlay" style="display: none;">
                <div class="sm_wwe_small_notifi_disabl_to_plan">
                    <h2 class="del_hdng">
                        Note!
                    </h2>
                    <p class="confirmation_p">
                        Note! You have elected to enable the automatically detect residential addresses feature. By confirming this election you will be charged for the <span id="selected_plan_popup">[plan]</span> plan. To ensure service continuity the plan will automatically renew each month, or when the plan is depleted, whichever comes first. You can change which plan is put into effect on the next renewal date by updating the selection on this page at anytime.
                    </p>
                    <div class="confirmation_btns">
                        <a style="cursor: pointer" class="cancel_plan">Cancel</a>
                        <a style="cursor: pointer" class="confirm_plan">OK</a>
                    </div>
                </div>
            </div>
            <?php
        }

    }

    new EnWooAddonsFormHandler();
}


