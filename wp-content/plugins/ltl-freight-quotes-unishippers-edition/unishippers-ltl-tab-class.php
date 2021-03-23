<?php
/**
 * Unishippers Tab Class
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Class WC_unishippers_ltl_Settings_tabs
 */
if (!class_exists('WC_unishippers_ltl_Settings_tabs')) {

    class WC_unishippers_ltl_Settings_tabs extends WC_Settings_Page
    {

        /**
         * Constructor
         */
        public function __construct()
        {

            $this->id = 'unishippers_freight';
            add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
            add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
        }

        /**
         * Add Setting Tab
         * @param $settings_tabs
         * @return array
         */
        public function add_settings_tab($settings_tabs)
        {

            $settings_tabs[$this->id] = __('Unishippers Freight', 'woocommerce-settings-unishipper_quetes');
            return $settings_tabs;
        }

        /**
         * Get Section
         * @return array
         */
        public function get_sections()
        {

            $sections = array(
                '' => __('Connection Settings', 'woocommerce-settings-unishipper_quetes'),
                'section-1' => __('Carriers', 'woocommerce-settings-unishipper_quetes'),
                'section-2' => __('Quote Settings', 'woocommerce-settings-unishipper_quetes'),
                'section-3' => __('Warehouses', 'woocommerce-settings-unishipper_quetes'),
                'section-4' => __('User Guide', 'woocommerce-settings-unishipper_quetes'),
            );

            $sections = apply_filters('en_woo_addons_sections', $sections, en_woo_plugin_unishippers_freight);
            $sections = apply_filters('en_woo_pallet_addons_sections', $sections, en_woo_plugin_unishippers_freight);
            return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
        }

        /**
         * Warehouses
         */
        public function unishippers_ltl_warehouse()
        {
            require_once 'warehouse-dropship/wild/warehouse/warehouse_template.php';
            require_once 'warehouse-dropship/wild/dropship/dropship_template.php';
        }

        /**
         * User Guide
         */
        public function unishippers_ltl_user_guide()
        {

            include_once('template/unishipper-ltl-guide.php');
        }

        /**
         * Setting Tab
         * @return array
         */
        public function unishippers_ltl_section_setting_tab()
        {

            $settings = array(
                'section_title_unishipper' => array(
                    'name' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'title',
                    'desc' => '<br> ',
                    'id' => 'wc_settings_unishippers_freight_title_section_connection',
                ),
                'unishippers_account_number_unishipper' => array(
                    'name' => __('Account Number ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishippers_freight_account_number'
                ),
                'unishippers_username_unishipper' => array(
                    'name' => __('Username ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishippers_freight_username'
                ),
                'unishippers_password_unishipper' => array(
                    'name' => __('Password ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishippers_freight_password'
                ),
                'unishippers_account_id' => array(
                    'name' => __('ID ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'unishippers_account_id'
                ),
                'authentication_key_unishipper' => array(
                    'name' => __('API Token ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishippers_freight_api_token'
                ),
                'plugin_licence_key' => array(
                    'name' => __('Plugin License Key ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'text',
                    'desc' => __('Obtain a License Key from <a href="https://eniture.com/products/" target="_blank" >eniture.com </a>', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishippers_freight_licence_key'
                ),
                'save_unishipper_buuton' => array(
                    'name' => __('Save Button ', 'woocommerce-settings-unishipper_quetes'),
                    'type' => 'button',
                    'desc' => __('', 'woocommerce-settings-unishipper_quetes'),
                    'id' => 'wc_settings_unishipper_button'
                ),
                'section_end_unishipper' => array(
                    'type' => 'sectionend',
                    'id' => 'wc_settings_unishipper_end-section_connection'
                ),
            );
            return $settings;
        }

        /**
         * Get Settings
         * @param $section
         * @return array
         * @global $wpdb
         */
        public function get_settings($section = null)
        {

            ob_start();
            switch ($section) {
                case 'section-0' :
                    echo '<div class="unishippers_ltl_connection_section_class">';
                    $settings = $this->unishippers_ltl_section_setting_tab();
                    break;

                case 'section-1':
                    echo '<div class="unishippers_carrier_section_class">';
                    ?>
                    <div class="unishippers_carrier_section_class wrap woocommerce">
                        <p>
                            Identifies which carriers are included in the quote response, not what is displayed in the
                            shopping cart. Identify what displays in the shopping cart in the Quote Settings. For
                            example, you may include quote responses from all carriers, but elect to only show the
                            cheapest three in the shopping cart. <br> <br>
                            Not all carriers service all origin and destination points. If a carrier doesn`t service the
                            ship to address, it is automatically omitted from the quote response. Consider conferring
                            with your Unishipper representative if you`d like to narrow the number of carrier responses.
                            <br> <br> <br>
                        </p>
                        <table>
                            <tbody>
                            <thead>
                            <tr class="unishipper_even_odd_class">
                                <th class="unishipper_carrier_carrier">Carrier Name</th>
                                <th class="unishipper__carrier_logo">Logo</th>
                                <th class="unishipper_carrier_include"><input type="checkbox" name="include_all"
                                                                              class="include_all"/></th>
                            </tr>
                            </thead>
                            <?php
                            global $wpdb;
                            $all_freight_array = array();
                            $count_carrier = 1;
                            $unishippers_ltl_freight_all = $wpdb->get_results("SELECT * FROM wp_unishippers_freight_carriers group by unishippers_carrierSCAC order by unishippers_carrierName ASC");
                            foreach ($unishippers_ltl_freight_all as $unishippers_ltl_freight_value):
                                ?>
                                <tr <?php
                                if ($count_carrier % 2 == 0) {

                                    echo 'class="unishipper_even_odd_class"';
                                }
                                ?> >

                                    <td class="unishipper_carrier_Name_td">
                                        <?php echo $unishippers_ltl_freight_value->unishippers_carrierName; ?>
                                    </td>
                                    <td>
                                        <img src="<?php echo plugins_url('Carrier_Logos/' . $unishippers_ltl_freight_value->carrier_logo, __FILE__) ?> ">
                                    </td>
                                    <td>
                                        <input <?php
                                        if ($unishippers_ltl_freight_value->carrier_status == '1') {
                                            echo 'checked="checked"';
                                        }
                                        ?>
                                                name="<?php echo $unishippers_ltl_freight_value->unishippers_carrierSCAC . $unishippers_ltl_freight_value->id; ?>"
                                                class="carrier_check"
                                                id="<?php echo $unishippers_ltl_freight_value->unishippers_carrierSCAC . $unishippers_ltl_freight_value->id; ?>"
                                                type="checkbox">
                                    </td>
                                </tr>
                                <?php
                                $count_carrier++;
                            endforeach;
                            ?>
                            <input name="action" value="save_unishippers_carrier_status" type="hidden"/>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    break;

                case 'section-2':
                    echo '<div class="quote_section_class_unishippers_ltl">';
                    $settings = array(
                        'section_title_quote' => array(
                            'title' => __('', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'title',
                            'desc' => '',
                            'id' => 'wc_settings_unishipper_section_title_quote'
                        ),
                        'rating_method_unishipper' => array(
                            'name' => __('Rating Method ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'select',
                            'desc' => __('Displays only the cheapest returned Rate.', 'woocommerce-settings-unishipper_quetes'),
                            'id' => 'wc_settings_unishippers_freight_rate_method',
                            'options' => array(
                                'Cheapest' => __('Cheapest', 'Cheapest'),
                                'cheapest_options' => __('Cheapest Options', 'cheapest_options'),
                                'average_rate' => __('Average Rate', 'average_rate')
                            )
                        ),
                        'number_of_options_unishipper' => array(
                            'name' => __('Number Of Options ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'select',
                            'default' => '3',
                            'desc' => __('Number of options to display in the shopping cart.', 'woocommerce-settings-unishipper_quetes'),
                            'id' => 'wc_settings_unishippers_freight_Number_of_options',
                            'options' => array(
                                '1' => __('1', '1'),
                                '2' => __('2', '2'),
                                '3' => __('3', '3'),
                                '4' => __('4', '4'),
                                '5' => __('5', '5'),
                                '6' => __('6', '6'),
                                '7' => __('7', '7'),
                                '8' => __('8', '8'),
                                '9' => __('9', '9'),
                                '10' => __('10', '10')
                            )
                        ),
                        'label_as_unishipper' => array(
                            'name' => __('Label As ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'text',
                            'desc' => __('What The User Sees During Checkout, e.g "Freight" Leave Blank to Display The Carrier Name.', 'woocommerce-settings-unishipper_quetes'),
                            'id' => 'wc_settings_unishippers_freight_label_as'
                        ),

                        'price_sort_unishippers_freight' => array(
                            'name' => __("Don't sort shipping methods by price  ", 'woocommerce-settings-abf_quotes'),
                            'type' => 'checkbox',
                            'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                            'id' => 'shipping_methods_do_not_sort_by_price'
                        ),

                        'show_delivery_estimate_unishipper' => array(
                            'name' => __('Show Delivery Estimate ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'checkbox',
                            'id' => 'wc_settings_unishippers_freight_delivery_estimate'
                        ),
                        'Services_to_include_in_quoted_price_unishipper' => array(
                            'title' => __('', 'woocommerce'),
                            'name' => __('', 'woocommerce-settings-unishipper_quetes'),
                            'desc' => '',
                            'id' => 'woocommerce_unishippers_freight_specific_Qurt_Price',
                            'css' => '',
                            'default' => '',
                            'type' => 'title'
                        ),
                        'residential_delivery_options_label' => array(
                            'name' => __('Residential Delivery', 'woocommerce-settings-unishipper_small_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'residential_delivery_options_label'
                        ),
                        'residential_delivery_unishipper' => array(
                            'name' => __('Always quote as residential delivery ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'checkbox',
                            'desc' => '',
                            'id' => 'wc_settings_unishipper_residential_delivery',
                            'class' => 'accessorial_service checkbox_fr_add',
                        ),
//                      Auto-detect residential addresses notification
                        'avaibility_auto_residential' => array(
                            'name' => __('Auto-detect residential addresses', 'woocommerce-settings-unishipper_small_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                            'id' => 'avaibility_auto_residential'
                        ),
                        'liftgate_delivery_options_label' => array(
                            'name' => __('Lift Gate Delivery ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'id' => 'liftgate_delivery_options_label'
                        ),
                        'lift_gate_delivery_unishipper' => array(
                            'name' => __('Always quote lift gate delivery ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'checkbox',
                            'desc' => '',
                            'id' => 'wc_settings_unishippers_freight_lift_gate_delivery',
                            'class' => 'accessorial_service checkbox_fr_add',
                        ),
                        'unishippers_freight_liftgate_delivery_as_option' => array(
                            'name' => __('Offer lift gate delivery as an option ', 'woocommerce-settings-fedex_freight'),
                            'type' => 'checkbox',
                            'desc' => __('', 'woocommerce-settings-fedex_freight'),
                            'id' => 'unishippers_freight_liftgate_delivery_as_option',
                            'class' => 'accessorial_service checkbox_fr_add',
                        ),
//                      Use my liftgate notification
                        'avaibility_lift_gate' => array(
                            'name' => __('Always include lift gate delivery when a residential address is detected', 'woocommerce-settings-unishipper_small_packages_quotes'),
                            'type' => 'text',
                            'class' => 'hidden',
                            'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Residential Address Detection module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                            'id' => 'avaibility_lift_gate'
                        ),
                        'hand_free_mark_up_unishipper' => array(
                            'name' => __('Handling Fee / Markup ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'text',
                            'desc' => 'Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.',
                            'id' => 'wc_settings_unishippers_freight_hand_free_mark_up'
                        ),
                        'allow_for_own_arrangment_unishipper' => array(
                            'name' => __('Allow For Own Arrangement ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'checkbox',
                            'desc' => __('<span class="description">Adds an option in the shipping cart for users to indicate that they will make and pay for their own Unishippers shipping arrangements.</span>', 'woocommerce-settings-unishipper_quetes'),
                            'id' => 'wc_settings_unishippers_freight_allow_for_own_arrangment'
                        ),
                        'text_for_own_arrangment_unishipper' => array(
                            'name' => __('Text For Own Arrangement ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'text',
                            'desc' => '',
                            'default' => "I'll arrange my own freight",
                            'id' => 'wc_settings_unishippers_freight_text_for_own_arrangment'
                        ),
                        'allow_other_plugins' => array(
                            'name' => __('Show WooCommerce Shipping Options ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'select',
                            'default' => '3',
                            'desc' => __('Enabled options on WooCommerce Shipping page are included in quote results.', 'woocommerce-settings-unishipper_quetes'),
                            'id' => 'wc_settings_unishippers_freight_allow_other_plugins',
                            'options' => array(
                                'yes' => __('YES', 'YES'),
                                'no' => __('NO', 'NO'),
                            )
                        ),
                        'return_Unishippers_quotes_unishipper' => array(
                            'name' => __('Return Unishippers Freight quotes when an order parcel shipment weight exceeds 150 lbs ', 'woocommerce-settings-unishipper_quetes'),
                            'type' => 'checkbox',
                            'desc' => '<span class="description" >When checked, the Unishippers Freight Quote plugin will return quotes when an order’s total weight exceeds 150 lbs (the maximum permitted by FedEx and UPS), even if none of the products have settings to indicate that it will ship Unishippers Freight. To increase the accuracy of the returned quote(s), all products should have accurate weights and dimensions. </span>',
                            'id' => 'en_plugins_return_Unishippers_quotes'
                        ),
                        'section_end_quote' => array(
                            'type' => 'sectionend',
                            'id' => 'wc_settings_quote_section_end'
                        )
                    );
                    break;

                case 'section-3' :

                    $this->unishippers_ltl_warehouse();
                    $settings = array();
                    break;

                case 'section-4' :

                    $this->unishippers_ltl_user_guide();
                    $settings = array();
                    break;

                default:

                    echo '<div class="unishippers_ltl_connection_section_class">';
                    $settings = $this->unishippers_ltl_section_setting_tab();
                    break;
            }

            $settings = apply_filters('en_woo_addons_settings', $settings, $section, en_woo_plugin_unishippers_freight);
            $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, en_woo_plugin_unishippers_freight);
            $settings = $this->avaibility_addon($settings);
            return apply_filters('woocommerce-settings-unishipper_quetes', $settings, $section);
        }

        /**
         * avaibility_addon
         * @param array type $settings
         * @return array type
         */
        function avaibility_addon($settings)
        {
            if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
                unset($settings['avaibility_lift_gate']);
                unset($settings['avaibility_auto_residential']);
            }

            return $settings;
        }

        /**
         * Output
         * @global $current_section
         */
        public function output()
        {

            error_reporting(0);
            global $current_section;
            $settings = $this->get_settings($current_section);
            WC_Admin_Settings::output_fields($settings);
        }

        /**
         * Save
         * @global $current_section
         */
        public function save()
        {

            global $current_section;
            if ($current_section != 'section-1') {
                $settings = $this->get_settings($current_section);
                WC_Admin_Settings::save_fields($settings);
            }
        }

    }

    return new WC_unishippers_ltl_Settings_tabs();
}