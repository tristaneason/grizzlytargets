<?php
/*
  Plugin Name: LTL Freight Quotes - Unishippers Edition
  Plugin URI: https://eniture.com/products/
  Description: Dynamically retrieves your negotiated LTL freight rates from Unishippers and displays the results in the WooCommerce shopping cart..
  Author: Eniture Technology
  Author URI: http://eniture.com/
  Version: 2.1.3
  Text Domain: eniture-technology
  License: GPL version 2 or later - http://www.eniture.com/
  WC requires at least: 5.7
  WC tested up to: 6.3.1
 */

if (!defined('ABSPATH')) {
    exit;
}
define('UNISHIPPERS_FREIGHT_DOMAIN_HITTING_URL', 'https://ws063.eniture.com');
define('UNISHIPPERS_FREIGHT_FDO_HITTING_URL', 'https://freightdesk.online/api/updatedWoocomData');

define('UNISHIPPERS_MAIN_FILE', __FILE__);

// Define reference
function en_unishippers_freight_plugin($plugins)
{
    $plugins['lfq'] = (isset($plugins['lfq'])) ? array_merge($plugins['lfq'], ['uni_ltl_shipping_method' => 'WC_unishippers_Shipping_Method']) : ['uni_ltl_shipping_method' => 'WC_unishippers_Shipping_Method'];
    return $plugins;
}

add_filter('en_plugins', 'en_unishippers_freight_plugin');

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($en = 1; $en <= 25; $en++) {
            $settings = get_option($eniture_plugins_id . $en);

            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }
        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

// Product detail set plans notification message for nested checkbox
if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('getHost')) {

    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }

}

/**
 * Get Domain Name
 */
if (!function_exists('unishippers_freight_get_domain')) {

    function unishippers_freight_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return getHost($url);
    }
}

/**
 * Load Css And Js Scripts
 */
if (!function_exists('unishippers_freight_admin_script')) {

    function unishippers_freight_admin_script()
    {
        // Cuttoff Time
        wp_register_style('unishippers_wickedpicker_style', plugin_dir_url(__FILE__) . 'css/wickedpicker.min.css', false, '1.0.0');
        wp_register_script('unishippers_wickedpicker_script', plugin_dir_url(__FILE__) . 'js/wickedpicker.js', false, '1.0.0');
        wp_enqueue_style('unishippers_wickedpicker_style');

        wp_enqueue_script('unishippers_wickedpicker_script');
        wp_register_style('unishippers_ltl_style', plugin_dir_url(__FILE__) . '/css/unishippers_ltl_style.css', array(), '1.1.3', 'screen');
        wp_enqueue_style('unishippers_ltl_style');
    }

    add_action('admin_enqueue_scripts', 'unishippers_freight_admin_script');
}


/**
 * Plugin Action
 * @staticvar $plugin
 * @param $actions
 * @param $plugin_file
 * @return array
 */
if (!function_exists('unishippers_freight_add_action_plugin')) {

    function unishippers_freight_add_action_plugin($actions, $plugin_file)
    {

        static $plugin;
        if (!isset($plugin))
            $plugin = plugin_basename(__FILE__);
        if ($plugin == $plugin_file) {

            $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=unishippers_freight">' . __('Settings', 'General') . '</a>');
            $site_link = array('support' => '<a href="https://support.eniture.com/" target="_blank">Support</a>');
            $actions = array_merge($settings, $actions);
            $actions = array_merge($site_link, $actions);
        }

        return $actions;
    }

    /**
     * Add Plugin Actions
     */
    add_filter('plugin_action_links', 'unishippers_freight_add_action_plugin', 10, 5);
}

add_action('admin_enqueue_scripts', 'en_unishippers_freight_script');

/**
 * Load Front-end scripts for abf
 */
function en_unishippers_freight_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_unishippers_freight_script', plugin_dir_url(__FILE__) . 'js/en-unishippers-freight.js', array(), '1.0.5');
    wp_localize_script('en_unishippers_freight_script', 'en_unishippers_freight_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
        'wc_settings_unishippers_freight_rate_method' => get_option("wc_settings_unishippers_freight_rate_method"),
        // Cuttoff Time
        'unishippers_freight_order_cutoff_time' => get_option("unishippers_freight_order_cut_off_time"),
        ));
}

/**
 * Inlude Plugin Files
 */
require_once('warehouse-dropship/wild-delivery.php');
require_once('standard-package-addon/standard-package-addon.php');
require_once('warehouse-dropship/get-distance-request.php');
require_once('update-plan.php');

require_once('fdo/en-fdo.php');
require_once('order/en-order-export.php');
require_once('order/en-order-widget.php');
require_once('order/rates/order-rates.php');
require_once('template/products-nested-options.php');
require_once 'template/csv-export.php';

register_activation_hook(__FILE__, 'en_unishippers_freight_activate_hit_to_update_plan');
register_deactivation_hook(__FILE__, 'en_unishippers_freight_deactivate_hit_to_update_plan');


require_once(__DIR__ . '/unishippers-ltl-filter-quotes.php');
require_once 'unishippers-ltl-version-compact.php';
require_once('unishippers-ltl-test-connecion.php');
require_once 'unishippers-ltl-shipping-class.php';
require_once 'db/unishippers_ltl_db.php';
require_once 'unishippers-ltl-liftgate-as-option.php';
require_once 'unishippers-ltl-carrier-service.php';
require_once 'unishippers-ltl-grouping.php';
require_once 'unishipper-ltl-admin-filter.php';
require_once 'unishipper-ltl-carrier-list.php';
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once 'unishippers-ltl-update-change.php';
require_once 'unishippers-ltl-curl-class.php';
// Origin terminal address
add_action('admin_init', 'unishippers_freight_update_warehouse');

require_once('product/en-product-detail.php');

/**
 * Unishippers Action And Filters
 */
if (!is_plugin_active('woocommerce/woocommerce.php')) {

    add_action('admin_notices', 'unishippers_freight_woocommrec_avaibility_error');
} else {
    add_filter('woocommerce_get_settings_pages', 'unishippers_ltl_shipping_sections');
}

add_action('admin_init', 'unishippers_ltl_check_woo_version');
add_action('woocommerce_shipping_init', 'unishippers_uni_ltl_shipping_method_init');
add_filter('woocommerce_shipping_methods', 'unishippers_ltl_add_Unishippers_shipping_method');
add_filter('woocommerce_package_rates', 'unishippers_ltl_hide_shipping_based_on_class');
add_action('init', 'unishippers_ltl_save_unishippers_carrier_status');
add_filter('woocommerce_cart_shipping_method_full_label', 'unishippers_ltl_remove_free_label', 10, 2);

/**
 * Unishippers Activation Hook
 */
register_activation_hook(__FILE__, 'create_unishippers_ltl_wh_db');
register_activation_hook(__FILE__, 'unishippers_ltl_freihgt_installation_carrier');
register_deactivation_hook(__FILE__, 'unishippers_ltl_truncat_carrier_table');

/**
 * Unishippers plugin update now
 */
if (!function_exists('en_unishippers_freight_update_now')) {

    function en_unishippers_freight_update_now()
    {
        $index = 'ltl-freight-quotes-unishippers-edition/woocommercefrieght.php';
        $plugin_info = get_plugins();
        $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
        $update_now = get_option('en_unishippers_freight_update_now');

        if ($update_now != $plugin_version) {
            if (!function_exists('en_unishippers_freight_activate_hit_to_update_plan')) {
                require_once(__DIR__ . '/update-plan.php');
            }

            en_unishippers_freight_activate_hit_to_update_plan();
            create_unishippers_ltl_wh_db();
            unishippers_ltl_freihgt_installation_carrier();

            update_option('en_unishippers_freight_update_now', $plugin_version);
        }
    }

    add_action('init', 'en_unishippers_freight_update_now');
}

$arr = array();
apply_filters('product_detail_freight_class', $arr);

define("en_woo_plugin_unishippers_freight", "unishippers_freight");


/**
 * Load Frontend scripts for Unishipper
 */
if (!function_exists('en_unishippers_freight_frontend_checkout_script')) {

    function en_unishippers_freight_frontend_checkout_script()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('en_unishippers_freight_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-unishippers-ltl-checkout.js', array(), '1.0.0');
        wp_localize_script('en_unishippers_freight_frontend_checkout_script', 'frontend_script', array(
            'pluginsUrl' => plugins_url(),
        ));
    }

    add_action('wp_enqueue_scripts', 'en_unishippers_freight_frontend_checkout_script');
}


if (!function_exists('unishippers_freight_quotes_plans_suscription_and_features')) {

    function unishippers_freight_quotes_plans_suscription_and_features($feature)
    {
        $package = get_option('unishippers_freight_packages_quotes_package');

        $features = array
        (
            'instore_pickup_local_devlivery' => array('3'),
            'hazardous_material' => array('2', '3'),
            'multi_warehouse' => array('2', '3'),
            'nested_material' => array('3'),
            // Cuttoff Time
            'unishippers_cutt_off_time' => array('2', '3'),
            'unishippers_show_delivery_estimates' => array('1', '2', '3')
        );

        return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
    }

    add_filter('unishippers_freight_quotes_plans_suscription_and_features', 'unishippers_freight_quotes_plans_suscription_and_features', 1);
}


if (!function_exists('unishippers_freight_plans_notification_link')) {

    function unishippers_freight_plans_notification_link($plans)
    {
        $plan = current($plans);
        $plan_to_upgrade = "";
        switch ($plan) {
            case 2:
                $plan_to_upgrade = "<a target='_blank' class='plan_color' href='https://eniture.com/woocommerce-unishippers-ltl-freight/'>Standard Plan required</a>";
                break;
            case 3:
                $plan_to_upgrade = "<a target='_blank' href='https://eniture.com/woocommerce-unishippers-ltl-freight/'>Advanced Plan required</a>";
                break;
        }

        return $plan_to_upgrade;
    }

    add_filter('unishippers_freight_plans_notification_link', 'unishippers_freight_plans_notification_link', 1);
}

if (!defined('EN_UNISHIPPER_LOADER')) {

    define('EN_UNISHIPPER_LOADER', plugin_dir_url(__FILE__));
}