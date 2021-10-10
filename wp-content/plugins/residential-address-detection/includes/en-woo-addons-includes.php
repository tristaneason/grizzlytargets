<?php
/**
 * Includes Engine class
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonsInclude")) {

    class EnWooAddonsInclude extends EnWooAddonPluginDetail
    {

        public $plugin_id;
        public $plugin_dependencies;
        public $plugin_standards;

        /**
         * construct
         */
        public function __construct()
        {

            $this->en_woo_addons_load_common_files();

            add_action('admin_print_scripts', array($this, 'admin_inline_js'));
            add_action('admin_enqueue_scripts', array($this, 'en_woo_addons_common_style'));
        }

        public function en_woo_addons_common_style()
        {

            wp_register_style('en-woo-addons-common-style', plugin_dir_url(__FILE__) . '../admin/assets/css/en-woo-addons-common-style.css', false, '1.1.0');
            wp_enqueue_style('en-woo-addons-common-style');
        }

        /**
         * common files include for all addons
         */
        public function en_woo_addons_load_common_files()
        {

            include_once(addon_plugin_url . '/includes/en-woo-addons-ajax-request.php');
            include_once(addon_plugin_url . '/includes/en-woo-addons-forms-handler.php');
        }

        /**
         * globally script variable
         */
        public function admin_inline_js()
        {

            wp_enqueue_script('trial-script', plugin_dir_url(__FILE__) . '../admin/assets/js/trial-script.js', array(), '1.1.1');
            wp_localize_script('trial-script', 'script', array('pluginsUrl' => plugins_url(),));

            ?>
            <script>
                var plugins_url = "<?php echo plugins_url(); ?>";
            </script>
            <?php
        }

        /**
         * plugins_dependies_script_css_files details using in en-woo-addons-forms-handler.php
         * @return array type
         */
        public function plugins_dependencies_script_css_files()
        {

            return $this->addon_files_script_style_arr();
        }

        /**
         * Plugins dependencies array merge
         * @return array
         */
        public function plugins_dependencies()
        {

            $plugins_dependies = array();
            $plugins_dependies_function_arr = array(
                'wwe_small_packages_quotes_dependencies',
                'wwe_quests_dependencies',
                'ups_freight_quotes_dependencies',
                'fedex_small_dependencies',
                'xpo_quotes_dependencies',
                'estes_ltl_quotes_dependencies',
                'odfl_quotes_dependencies',
                'purolator_ltl_quotes_dependencies',
                'abf_quotes_dependencies',
                'yrc_quotes_dependencies',
                'rnl_quotes_dependencies',
                'purolator_small_dependencies',
                'sefl_quotes_dependencies',
                'fedex_freight_dependencies',
                'ups_small_plugin_dependencies',
                'freightview_quotes_dependencies',
                'cerasis_freights_dependencies',
                'cortigo_freights_dependencies',
                'freightquote_quests_dependencies',
                'saia_quotes_dependencies',
                'unishepper_small_dependencies',
                'unishippers_freight_dependencies',
                'echo_freight_dependencies',
                'transportation_insight_freight_dependencies',
                'trinet_small_dependencies',
                'daylight_quotes_dependencies',
                'freightview_dependencies',
                'dayross_quotes_dependencies'
            );

            foreach ($plugins_dependies_function_arr as $value) {

                $plugins_dependies = array_merge($plugins_dependies, $this->$value());
            }

            $plugins_dependies = apply_filters('en_woo_addons_plugin_dependencies_apply_filters', $plugins_dependies);
            return $plugins_dependies;
        }

    }

    new EnWooAddonsInclude();
}