<?php

/**
 * Auto residential template
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWooAddonPluginDetail")) {

    class EnWooAddonPluginDetail
    {

        public $plugin_details;

        /**
         * construct
         */
        public function __construct()
        {

        }

        /**
         * setter plugin details
         * @param type $plugin_details
         */
        public function set_details($plugin_details)
        {

            $this->plugin_details = $plugin_details;
        }

        /**
         * getter plugin details
         * @return type
         */
        public function get_details()
        {

            return $this->plugin_details;
        }

        function addon_files_script_style_arr()
        {

            $addon_files_script_style_arr = array(
                'auto_residential_detection_addon' => array(
                    'templates' => array(
                        'en-woo-addon-auto-residential-detection-template'
                    ),
                    'css' => array(
                        'auto-residential-detected-style'
                    ),
                    'script' => array(
                        'auto-residential-detected-script'
                    )
                ),
                'lift_gate_delivery_addon' => array(
                    'templates' => array(
                        'en-woo-addon-liftgate-delivery-template'
                    ),
                    'css' => array(
                        'liftgate-delivery-style'
                    ),
                    'script' => array(
                        'liftgate-delivery-script'
                    )
                ),
            );

            return $addon_files_script_style_arr;
        }

        /**
         * freightview_dependencies for eniture woo addons
         * @return array
         */
        public function freightview_dependencies()
        {
            $freightview = array(
                "freightview" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_quote_settings_show_delivery_estimate_freightview'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'en_quote_settings_residential_delivery_freightview',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'en_quote_settings_liftgate_delivery_freightview'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'en_connection_settings_license_key_freightview'
                    )
                )
            );

            return $freightview;
        }

        /**
         * ups_freight_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function freightview_quotes_dependencies()
        {

            $freightview_quotes = array(
                "freightview_freight_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'freightview_freight_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'freightview_freight_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'freightview_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'freightview_freight_setting_licnse_key'
                    )
                )
            );

            return $freightview_quotes;
        }

        /**
         * trinet_small_dependencies for eniture woo addons
         * @return array
         */
        public function trinet_small_dependencies()
        {
            $trinet_small = array(
                "trinet" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'shipping_methods_do_not_sort_by_price'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'en_trinet_residential_delivery',
                            ),
                            'reset_always_auto_residential_id' => array(
                                'en_trinet_residential_delivery',
                            ),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(
                                ''
                            ),
                            'reset_always_lift_gate' => array(
                                ''
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'en_connection_settings_license_key_trinet'
                    )
                )
            );

            return $trinet_small;
        }

        /**
         * wwe_small_packages_quotes_dependencies for eniture addons
         * @return array
         */
        public function wwe_small_packages_quotes_dependencies()
        {

            $wwe_small_packages_quotes = array(
                "wwe_small_packages_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'Service_UPS_Next_Day_Early_AM_small_packages_quotes'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'quest_as_residential_delivery_wwe_small_packages',
                            ),
                            'reset_always_auto_residential_id' => array(
                                'wc_settings_quest_as_residential_delivery_wwe_small_packages',
                            ),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_plugin_licence_key_wwe_small_packages_quotes'
                    )
                )
            );

            return $wwe_small_packages_quotes;
        }

        /**
         * wwe_quests_dependencies for eniture woo addons
         * @return array
         */
        public function wwe_quests_dependencies()
        {

            $wwe_quests = array(
                "wwe_quests" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_wwe'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_wwe',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_wwe'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_wwe_licence_key'
                    )
                )
            );
            return $wwe_quests;
        }

        /**
         * wwe_quests_dependencies for eniture woo addons
         * @return array
         */
        public function unishippers_freight_dependencies()
        {

            $unishippers_freight = array(
                "unishippers_freight" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_unishipper'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_unishipper',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_unishipper'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_unishippers_freight_licence_key'
                    )
                )
            );
            return $unishippers_freight;
        }

        /**
         * echo_freight_dependencies for eniture woo addons
         * @return array
         */
        public function echo_freight_dependencies()
        {

            $echo_freight = array(
                "echo_freight" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_echo'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_echo',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_echo'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_echo_freight_licence_key'
                    )
                )
            );
            return $echo_freight;
        }

        /**
         * transportation_insight_freight_dependencies for eniture woo addons
         * @return array
         */
        public function transportation_insight_freight_dependencies()
        {

            $transportation_insight_freight = array(
                "transportation_insight" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_transportation_insight'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_transportation_insight',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_transportation_insight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_transportation_insight_licence_key'
                    )
                )
            );

            return $transportation_insight_freight;
        }

        /**
         * freightquote_quests_dependencies for eniture woo addons
         * @return array
         */
        public function freightquote_quests_dependencies()
        {

            $freightquote_quests = array(
                "freightquote_quests" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_freightquote'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_freightquote',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_freightquote'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_freightquote_license_key'
                    )
                )
            );
            return $freightquote_quests;
        }

        /**
         * saia_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function saia_quotes_dependencies()
        {

            $saia_quotes = array(
                "saia_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'accessorial_quoted_saia'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_saia',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_saia'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_saia_plugin_licence_key'
                    )
                )
            );
            return $saia_quotes;
        }

        /**
         * ups_freight_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function ups_freight_quotes_dependencies()
        {

            $ups_freight_quotes = array(
                "ups_freight_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'ups_freight_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'ups_freight_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'ups_freight_setting_licnse_key'
                    )
                )
            );

            return $ups_freight_quotes;
        }

        /**
         * fedex_small_dependencies for eniture woo addons
         * @return array
         */
        public function fedex_small_dependencies()
        {

            $fedex_small = array(
                "fedex_small" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'publish_negotiated_fedex_small_rates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'fedex_small_residential_delivery',
                            ),
                            'reset_always_auto_residential_id' => array(
                                'fedex_small_quote_as_residential_delivery',
                            ),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                            'reset_always_lift_gate' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'fedex_small_licence_key'
                    )
                )
            );

            return $fedex_small;
        }

        /**
         * fedex_small_dependencies for eniture woo addons
         * @return array
         */
        public function unishepper_small_dependencies()
        {

            $unishepper_small = array(
                "unishepper_small" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'restrict_radio_btn_transit_unishepper_small'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'unishepper_small_residential_delivery',
                            ),
                            'reset_always_auto_residential_id' => array(
                                'unishepper_small_quote_as_residential_delivery',
                            ),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                            'reset_always_lift_gate' => array(
                                'ups_freight_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'unishepper_small_licence_key'
                    )
                )
            );

            return $unishepper_small;
        }

        /**
         * daylight_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function daylight_quotes_dependencies()
        {
            $daylight = array(
                "daylight" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_quote_settings_show_delivery_estimate_daylight'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'en_quote_settings_residential_delivery_daylight',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'en_quote_settings_liftgate_delivery_daylight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'en_connection_settings_license_key_daylight'
                    )
                )
            );

            return $daylight;
        }

        /**
         * xpo_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function xpo_quotes_dependencies()
        {

            $xpo_quotes = array(
                "xpo_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'xpo_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_xpo',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_xpo'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_xpo_plugin_licence_key'
                    )
                )
            );

            return $xpo_quotes;
        }

        /**
         * estes_ltl_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function estes_ltl_quotes_dependencies()
        {

            $estes_ltl_quotes = array(
                "estes_ltl_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'estes_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'estes_residential_delivery',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'estes_liftgate_delivery'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'estes_setting_licnse_key'
                    )
                )
            );

            return $estes_ltl_quotes;
        }

        /**
         * odfl_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function odfl_quotes_dependencies()
        {

            $odfl_quotes = array(
                "odfl_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'accessorial_quoted_odfl'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_odfl',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_odfl'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_odfl_plugin_licence_key'
                    )
                ),
            );

            return $odfl_quotes;
        }

        /**
         * purolator_ltl_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function purolator_ltl_quotes_dependencies()
        {

            $purolator_ltl_quotes = array(
                "purolator_ltl_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'service_purolator_ltl_expedited'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_purolator_ltl',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'purolator_ltl_plugin_licence_key'
                    )
                )
            );

            return $purolator_ltl_quotes;
        }

        /**
         * abf_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function abf_quotes_dependencies()
        {

            $abf_quotes = array(
                "abf_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'abf_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_abf',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_abf'
                            )
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_abf_plugin_licence_key'
                    )
                )
            );

            return $abf_quotes;
        }

        /**
         * yrc_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function yrc_quotes_dependencies()
        {

            $yrc_quotes = array(
                "yrc_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'accessorial_quoted_yrc'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_yrc',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_yrc'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_yrc_plugin_licence_key'
                    )
                )
            );

            return $yrc_quotes;
        }

        /**
         * rnl_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function rnl_quotes_dependencies()
        {

            $rnl_quotes = array(
                "rnl_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'rnl_show_delivery_estimates'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_rnl',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_rnl'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_rnl_plugin_licence_key'
                    )
                )
            );

            return $rnl_quotes;
        }

        /**
         * sefl_quotes_dependencies for eniture woo addons
         * @return array
         */
        public function sefl_quotes_dependencies()
        {

            $sefl_quotes = array(
                "sefl_quotes" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'accessorial_quoted_sefl'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_sefl',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_sefl'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_sefl_plugin_licence_key'
                    )
                )
            );

            return $sefl_quotes;
        }

        /**
         * purolator_small_dependencies for eniture woo addons
         * @return array
         */
        public function purolator_small_dependencies()
        {

            $purolator_small = array(
                "purolator_small" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'ground_transit_label'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_rnl'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'purolator_small_licence_key'
                    )
                )
            );

            return $purolator_small;
        }

        /**
         * fedex_freight_dependencies for eniture woo addons
         * @return array
         */
        public function fedex_freight_dependencies()
        {

            $fedex_freight = array(
                "fedex_freight" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'accessorial_quoted_fedex_freight'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'accessorial_residential_delivery_fedex_freight',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_fedex_freight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'fedex_freight_plugin_licence_key'
                    )
                )
            );

            return $fedex_freight;
        }

        /**
         * wwe_quests_dependencies for eniture woo addons
         * @return array
         */
        public function cerasis_freights_dependencies()
        {

            $cerasis_freights = array(
                "cerasis_freights" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_cerasis'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_cerasis',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_cerasis'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_cerasis_licence_key'
                    )
                )
            );
            return $cerasis_freights;
        }

        /**
         * wwe_quests_dependencies for eniture woo addons
         * @return array
         */
        public function cortigo_freights_dependencies()
        {

            $cortigo_freights = array(
                "cortigo_freights" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'show_delivery_estimate_cortigo'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'residential_delivery_cortigo',
                            ),
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => true,
                            'section' => 'section-2',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'lift_gate_delivery_cortigo'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => false,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'wc_settings_cortigo_licence_key'
                    )
                )
            );
            return $cortigo_freights;
        }

        /**
         * ups_small_plugin_dependencies for eniture woo addons
         * @return array
         */
        public function ups_small_plugin_dependencies()
        {

            $ups_small_plugin = array(
                "ups_small" => array(
                    'addons' => array(
                        'auto_residential_detection_addon' => array(
                            'active' => true,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'restrict_radio_btn_transit_ups_small'
                            ),
                            'unset_fields' => array(),
                            'reset_always_auto_residential' => array(
                                'ups_small_residential_delivery',
                            ),
                            'reset_always_auto_residential_id' => array(
                                'ups_small_quote_as_residential_delivery',
                            ),
                            'en_residential_addresses' => array(
                                'active' => true,
                                'section' => 'section-addresses',
                            )
                        ),
                        'lift_gate_delivery_addon' => array(
                            'active' => false,
                            'section' => 'section-1',
                            'after_index_fields' => array(
                                'en_default_unconfirmed_address_types_to'
                            ),
                            'unset_fields' => array(),
                            'reset_always_lift_gate' => array(
                                'accessorial_liftgate_delivery_fedex_freight'
                            ),
                        ),
                        'box_sizing_addon' => array(
                            'active' => true,
                            'section' => 'section-box',
                            'after_index_fields' => array(),
                            'unset_fields' => array(),
                        )
                    ),
                    'license_key' => array(
                        'ups_small_licence_key'
                    )
                )
            );

            return $ups_small_plugin;
        }

    }

    new EnWooAddonPluginDetail();
}
