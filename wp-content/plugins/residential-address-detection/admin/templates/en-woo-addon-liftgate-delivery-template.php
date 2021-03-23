<?php

/**
 * Liftgate delivery template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonLiftgateTemplate")) {

    class EnWooAddonLiftgateTemplate extends EnWooAddonsFormHandler {

        public $settings;
        public $liftgate_dependencies;
        public $reset_always_liftgate;
        public $plugin_id;

        /**
         * construct
         */
        public function __construct() {

            add_filter('en_woo_addons_check_for_lift_gate_delivery_option', array($this, 'liftgate_delivery'), 10, 3);
        }

        /**
         * reset the existing Lift gate field 
         * @return array type
         */
        public function reset_liftgate_fields() {

            $this->reset_always_liftgate = $this->get_arr_filterd_val($this->liftgate_dependencies['reset_always_lift_gate']);
            if (isset($this->reset_always_liftgate) && (!empty($this->reset_always_liftgate))) {
                $after_index_fields_liftgate = "liftgate_delivery_options_label";
                $this->settings[$this->reset_always_liftgate]['class'] = (isset($this->settings[$this->reset_always_liftgate]['class'])) ? $this->settings[$this->reset_always_liftgate]['class'] : "";
                $this->settings[$this->reset_always_liftgate]['class'] .= " en_woo_addons_always_lift_gate_delivery checkbox_class";
                $reset_liftgate[$this->reset_always_liftgate] = $this->settings[$this->reset_always_liftgate];
                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields_liftgate, $reset_liftgate);
            }
            return $this->settings;
        }

        /**
         * unset the given fields from settings array
         * @return array type
         */
        public function unset_liftgate_fields() {

            $unset_fields = $this->liftgate_dependencies['unset_fields'];
//          unset fields from @param $settings array standard plugin 
            if (isset($unset_fields) && (!empty($unset_fields)) && (is_array($unset_fields))) {
                foreach ($unset_fields as $key => $value) {
                    unset($this->settings[$value]);
                }
            }
            return $this->settings;
        }

        /**
         * web setting api array for liftgate delivery
         * @param array type $settings
         * @param array type $addons
         * @return array type
         */
        public function liftgate_delivery($settings, $addons, $plugin_id) {

            $this->settings = $settings;
            $this->plugin_id = $plugin_id;
            $this->liftgate_dependencies = $addons['lift_gate_delivery_addon'];
            $after_index_fields_liftgate = $this->get_arr_filterd_val($this->liftgate_dependencies['after_index_fields']);
            if (isset($after_index_fields_liftgate) &&
                    (!empty($after_index_fields_liftgate)) &&
                    (isset($this->settings[$after_index_fields_liftgate]))) {

                $this->settings = $this->addon_array_insert_after($this->settings, $after_index_fields_liftgate, $this->liftgate_delivery_arr());
                if (isset($addons['lift_gate_delivery_addon']['unset_fields'])) {

                    $unset_fields = $addons['lift_gate_delivery_addon']['unset_fields'];
                    if (!in_array("en_woo_addons_liftgate_delivery_as_option", $unset_fields) &&
                            ($this->settings[$this->plugin_id . "_liftgate_delivery_as_option"])) {

                        $this->settings[$this->plugin_id . "_liftgate_delivery_as_option"]['class'] .= " en_woo_addons_always_lift_gate_delivery";
                        $liftgate_dev_as_option = array($this->plugin_id . "_liftgate_delivery_as_option" => $this->settings[$this->plugin_id . "_liftgate_delivery_as_option"]);
                        $this->settings = $this->addon_array_insert_after($this->settings, "liftgate_delivery_options_label", $liftgate_dev_as_option);
                    }
                }
                $this->settings = $this->reset_liftgate_fields();
                $this->settings = $this->unset_liftgate_fields();
            }
            $this->settings = apply_filters('en_woo_addons_liftgate_updated_filters', $this->settings);
            return $this->settings;
        }

        /**
         * liftgate detection updated fields
         * @return array
         */
        public function liftgate_delivery_arr() {

            $liftgate_updated_settings = array(
                'liftgate_delivery_options_label' => array(
                    'name' => __('Lift Gate Delivery Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'text',
                    'class' => 'hidden',
                    'id' => 'liftgate_delivery_options_label'
                ),
                'en_woo_addons_liftgate_with_auto_residential' => array(
                    'name' => __('Always include lift gate delivery when a residential address is detected', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'type' => 'checkbox',
                    'desc' => __('Requires <b><i>Automatically detect residential addresses</i></b> feature', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                    'id' => 'en_woo_addons_liftgate_with_auto_residential',
                    'class' => 'checkbox_class checkbox_fr_add en_woo_addons_liftgate_with_auto_residential'
                )
            );
            $liftgate_updated_settings = apply_filters('en_woo_addons_liftgate_new_fields_filters', $liftgate_updated_settings);
            return $liftgate_updated_settings;
        }

    }

    new EnWooAddonLiftgateTemplate();
}