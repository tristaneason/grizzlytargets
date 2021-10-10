<?php

/**
 *  Unishippers Product Template
 *
 * @package      Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Drop Ship For Shipping Section In Product Detail Page
 * @param $loop
 * @param $variation_data
 * @param $variation
 * @global $wpdb
 */
if (!function_exists('unishippers_freight_dropship')) {

    function unishippers_freight_dropship($loop, $variation_data = array(), $variation = array())
    {
        global $wpdb;
        $dropship_list = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship'");
        if (!empty($dropship_list)) {

            (isset($variation->ID) && $variation->ID > 0) ? $variationID = $variation->ID : $variationID = get_the_ID();

            /**
             * create enable dropship checkbox.
             */
            woocommerce_wp_checkbox(
                array(
                    'id' => '_enable_dropship[' . $variationID . ']',
                    'label' => __('Enable drop ship location', 'woocommerce'),
                    'value' => get_post_meta($variationID, '_enable_dropship', true),
                )
            );

            $attributes = array(
                'id' => '_dropship_location[' . $variationID . ']',
                'class' => 'p_ds_location',
            );

            $get_loc = maybe_unserialize(get_post_meta($variationID, '_dropship_location', true));
            $valuesArr = array();

            foreach ($dropship_list as $list) {

                (isset($list->nickname) && $list->nickname == '') ? $nickname = '' : $nickname = $list->nickname . ' - ';
                (isset($list->country) && $list->country == '') ? $country = '' : $country = '(' . $list->country . ')';
                $location = $nickname . $list->zip . ', ' . $list->city . ', ' . $list->state . ' ' . $country;
                $finalValue['option_id'] = $list->id;
                $finalValue['option_value'] = $list->id;
                $finalValue['option_label'] = $location;
                $valuesArr[] = $finalValue;
            }

            $aFields[] = array(
                'attributes' => $attributes,
                'label' => 'Drop ship location',
                'value' => $valuesArr,
                'name' => '_dropship_location[' . $variationID . '][]',
                'type' => 'select',
                'selected_value' => $get_loc,
                'variant_id' => $variationID
            );

            $aFields = apply_filters('before_unishippers_freight_product_detail_fields', $aFields);

            apply_filters('En_Plugins_dropship_filter', $aFields, $get_loc, $variationID);
        }
    }

}

/**
 * Dropship Filter
 */
if (!has_filter('En_Plugins_dropship_filter')) {
    add_action('woocommerce_product_options_shipping', 'unishippers_freight_dropship');
    add_action('woocommerce_product_after_variable_attributes', 'unishippers_freight_dropship', 10, 3);
    add_filter('En_Plugins_dropship_filter', 'unishippers_freight_dropship_filter', 10, 3);
}

/**
 * Unishippers Dropship Filter
 * @param $aFields
 * @param $get_loc
 * @param $variationID
 */
if (!function_exists('unishippers_freight_dropship_filter')) {

    function unishippers_freight_dropship_filter($aFields, $get_loc, $variationID)
    {
        $fieldsHtml = '';
        foreach ($aFields as $key => $sField) {
            $sField = apply_filters('unishippers_freight_product_detail_fields', $sField);
            $fieldsHtml = unishippers_freight_dropship_html($sField, $fieldsHtml, $get_loc, $variationID);
        }
        $fieldsHtml = apply_filters('after_unishippers_freight_product_detail_fields', $fieldsHtml);
        echo $fieldsHtml;
    }

}

/**
 * Attribute For Drop Ship Dropdown
 * @param $attributes
 * @return string
 */
if (!function_exists('unishippers_freight_attributes_string')) {

    function unishippers_freight_attributes_string($attributes)
    {
        $str = '';
        foreach ($attributes as $key => $sAttribute) {
            $str .= ' ' . $key . ' ="' . $sAttribute . '" ';
        }
        return $str;
    }

}

/**
 * Drop Ship Dropdown Select
 * @param $sField
 * @param $fieldsHtml
 * @return string
 */
if (!function_exists('unishippers_freight_dropship_html')) {

    function unishippers_freight_dropship_html($sField, $fieldsHtml)
    {

//          Multi Dropship
        $description = "";
        $disable_me = FALSE;
        $dropship_flag = count($sField['value']);
        $dropship_flag = isset($dropship_flag) && ($dropship_flag > 1) ? true : false;

        $plan_notifi = apply_filters('en_woo_plans_notification_action', array());

        if (!empty($plan_notifi) && (isset($plan_notifi['multi_dropship']))) {
            $enable_plugins = (isset($plan_notifi['multi_dropship']['enable_plugins'])) ? $plan_notifi['multi_dropship']['enable_plugins'] : "";
            $disable_plugins = (isset($plan_notifi['multi_dropship']['disable_plugins'])) ? $plan_notifi['multi_dropship']['disable_plugins'] : "";
            if (strlen($disable_plugins) > 0) {
                if (strlen($enable_plugins) > 0) {
                    $description = "<br><br>" . apply_filters('en_woo_plans_notification_message_action', $enable_plugins, $disable_plugins);
                } else {
                    $description = apply_filters('unishippers_freight_plans_notification_link', array(2));
                    $disable_me = TRUE;
                }
            }
        }

        $str = unishippers_freight_attributes_string($sField['attributes']);

        $disable_dropship_flage = true;
        $multi_dropship = apply_filters('unishippers_freight_quotes_plans_suscription_and_features', 'multi_dropship');
        if (get_option('unishippers_freight_store_type') == "0" && get_option('en_old_user_dropship_status') == "0") {
            $disable_dropship_flage = false;
        }
        $fieldsHtml .= '<p class="form-field _dropship_location">';
        $fieldsHtml .= '<label for="_dropship_location">' . $sField['label'] . '</label>';
        if ($sField['type'] == 'select') {
            $fieldsHtml .= '<select name="' . $sField['name'] . '" ' . $str . '>';
            if ($sField['value']) {
                $count = 0;
                foreach ($sField['value'] as $option) {

                    $disabled_option = isset($disable_dropship_flage) && ($disable_dropship_flage == true && $count > 0 && (is_array($multi_dropship))) ? 'disabled' : '';
                    $selected_option = unishippers_freight_product_ds_selected_option($sField['selected_value'], $option['option_value']);
                    $fieldsHtml .= '<option ' . $disabled_option . ' value="' . esc_attr($option['option_value']) . '" ' . $selected_option . '>' . esc_html($option['option_label']) . ' </option>';

                    $count++;
                }
            }
            $fieldsHtml .= '</select>';
            $fieldsHtml .= $description;
        }
        $fieldsHtml .= '</p>';
        return $fieldsHtml;
    }

}

/**
 * Drop Ship Dropdown Selected Options
 * @param $get_loc
 * @param $option_val
 */
if (!function_exists('unishippers_freight_product_ds_selected_option')) {

    function unishippers_freight_product_ds_selected_option($get_loc, $option_val)
    {
        $selected = '';
        if (is_array($get_loc)) {
            if (in_array($option_val, $get_loc)) {
                $selected = 'selected="selected"';
            }
        } else {
            $selected = selected($get_loc, $option_val, false);
        }
        return $selected;
    }

}


/**
 * Unishippers Save Variable Fields
 * @param $post_id
 */
if (!function_exists('unishippers_freight_save_variable_fields')) {

    function unishippers_freight_save_variable_fields($post_id)
    {

        if (isset($post_id) && $post_id > 0) :

            $freight_class = (isset($_POST['_ltl_freight_variation'][$post_id])) ? sanitize_text_field($_POST['_ltl_freight_variation'][$post_id]) : "";

            $enable_ds = (isset($_POST['_enable_dropship'][$post_id]) ? sanitize_text_field($_POST['_enable_dropship'][$post_id]) : "");
            $ds_locaton = $_POST['_dropship_location'][$post_id];
            $ds_location_val = isset($ds_locaton) && is_array($ds_locaton) ? array_map('intval', $ds_locaton) : $ds_locaton;

            update_post_meta($post_id, '_enable_dropship', esc_attr($enable_ds));
            update_post_meta($post_id, '_dropship_location', maybe_serialize($ds_location_val));
            update_post_meta($post_id, '_ltl_freight_variation', esc_attr($freight_class));

        endif;
    }

    /**
     * Drop Ship Save For Variations
     */
    add_action('woocommerce_save_product_variation', 'unishippers_freight_save_variable_fields', 10, 1);
}


/**
 *  Unishippers Product Fields Save
 * @param $post_id
 */
if (!function_exists('unishippers_freight_product_fields_save')) {

    function unishippers_freight_product_fields_save($post_id)
    {
        $woocommerce_checkbox = (isset($_POST['_enable_dropship'][$post_id])) ? sanitize_text_field($_POST['_enable_dropship'][$post_id]) : "";
        $dropship_location = $_POST['_dropship_location'][$post_id];
        $dropship_location_val = isset($dropship_location) && is_array($dropship_location) ? array_map('intval', $dropship_location) : $dropship_location;

        $shipping_freight_class = (isset($_POST['_ltl_freight'])) ? sanitize_text_field($_POST['_ltl_freight']) : "";

        update_post_meta($post_id, '_enable_dropship', esc_attr($woocommerce_checkbox));
        update_post_meta($post_id, '_dropship_location', maybe_serialize($dropship_location_val));
        update_post_meta($post_id, '_ltl_freight', esc_attr($shipping_freight_class));
    }

    /**
     * Save Product Custom Shipping Options
     */
    add_action('woocommerce_process_product_meta', 'unishippers_freight_product_fields_save');
}
/**
 * Product Unishippers Freight Classification
 */
if (!has_filter('En_Plugins_freight_classification_filter')) {
    add_action('woocommerce_product_options_shipping', 'unishippers_ltl_freight_class', 20);
    add_filter('En_Plugins_freight_classification_filter', 'unishippers_freight_class_filter', 20, 1);
}

/**
 *  Unishippers Class Filter
 * @param $freight_clasification
 */
if (!function_exists('unishippers_freight_class_filter')) {

    function unishippers_freight_class_filter($freight_clasification)
    {
        return $freight_clasification;
    }

}

/**
 *  Unishippers Freight Class
 */
if (!function_exists('unishippers_ltl_freight_class')) {

    function unishippers_ltl_freight_class()
    {
        $classes = unishippers_ltl_freight_class_array();
        $freight_clasification = woocommerce_wp_select(
            array(
                'id' => '_ltl_freight',
                'label' => __('Freight classification', 'woocommerce'),
                'options' => $classes
            )
        );
        apply_filters('En_Plugins_freight_classification_filter', $freight_clasification);
    }

}

/**
 * Freight Class Array
 * @return array
 */
if (!function_exists('unishippers_ltl_freight_class_array')) {

    function unishippers_ltl_freight_class_array()
    {
        $classification = array(
            '0' => __('No Freight Class', 'woocommerce'),
            '50' => __('50', 'woocommerce'),
            '55' => __('55', 'woocommerce'),
            '60' => __('60', 'woocommerce'),
            '65' => __('65', 'woocommerce'),
            '70' => __('70', 'woocommerce'),
            '77.5' => __('77.5', 'woocommerce'),
            '85' => __('85', 'woocommerce'),
            '92.5' => __('92.5', 'woocommerce'),
            '100' => __('100', 'woocommerce'),
            '110' => __('110', 'woocommerce'),
            '125' => __('125', 'woocommerce'),
            '150' => __('150', 'woocommerce'),
            '175' => __('175', 'woocommerce'),
            '200' => __('200', 'woocommerce'),
            '225' => __('225', 'woocommerce'),
            '250' => __('250', 'woocommerce'),
            '300' => __('300', 'woocommerce'),
            '400' => __('400', 'woocommerce'),
            '500' => __('500', 'woocommerce'),
            'DensityBased' => __('Density Based', 'woocommerce')
        );
        return $classification;
    }

}
/**
 * Freight Classification For Shipping Section In Product Detail Page ( Variation Products )
 */
if (!has_filter('En_Plugins_variable_freight_classification_filter')) {
    add_action('woocommerce_product_after_variable_attributes', 'unishippers_freight_variable_fields', 20, 3);
    add_filter('En_Plugins_variable_freight_classification_filter', 'unishippers_freight_class_filter', 20, 1);
}

/**
 * Variable Class Filter
 * @param $freight_clasification
 * @return string
 */
if (!function_exists('unishippers_freight_variable_freight_class_filter')) {

    function unishippers_freight_variable_freight_class_filter($freight_clasification)
    {
        return $freight_clasification;
    }

}

/**
 * Variable Fields
 * @param $loop
 * @param $variation_data
 * @param $variation
 */
if (!function_exists('unishippers_freight_variable_fields')) {

    function unishippers_freight_variable_fields($loop, $variation_data, $variation)
    {
        $classes = unishippers_ltl_freight_class_array();
        $replacement = array(0 => "Same as parent");
        $options = array_replace($classes, $replacement);

        $freight_clasification = woocommerce_wp_select(
            array(
                'id' => '_ltl_freight_variation[' . $variation->ID . ']',
                'label' => __('Freight classification <br>', 'woocommerce'),
                'value' => get_post_meta($variation->ID, '_ltl_freight_variation', true),
                'options' => $options
            )
        );
        apply_filters('En_Plugins_variable_freight_classification_filter', $freight_clasification);
    }

}