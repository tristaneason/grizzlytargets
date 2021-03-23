<?php

/*
  Plugin Name: Residential Address Detection
  Plugin URI:  https://eniture.com/products/
  Description: Detects residential addresses so that the carrier’s residential delivery fee is included in shipping rate estimates. For exclusive use with Eniture Technology’s Small Package Quotes and LTL Freight Quotes plugins.
  Version:     2.1.1
  Author:      Eniture Technology
  Author URI:  https://eniture.com
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: eniture-technology
  WC requires at least: 4.0.0
  WC tested up to: 4.9.0
 */


if (!defined('ABSPATH')) {
    exit; /* Not allowed to access directly */
}

define('RAD_MAIN_FILE', __FILE__);

if (!function_exists('is_plugin_active')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

/**
 * Check woocommerce installlation
 */
if (!function_exists('eo_woo_addons_avaibility_error')) {

    function eo_woo_addons_avaibility_error()
    {
        $class = "error";
        $message = "It requires WooCommerce in order to work , Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin.";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

}

if (!is_plugin_active('woocommerce/woocommerce.php')) {

    add_action('admin_notices', 'eo_woo_addons_avaibility_error');
}
register_deactivation_hook(__FILE__, "residential_address_detection_uninstall");


/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('en_residential_get_host')) {

    function en_residential_get_host($url)
    {
        $parse_url = parse_url(trim($url));
        if (isset($parse_url['host'])) {
            $host = $parse_url['host'];
        } else {
            $path = explode('/', $parse_url['path']);
            $host = $path[0];
        }
        return trim($host);
    }

}

/**
 * Get Domain Name
 */
if (!function_exists('en_residential_get_domain')) {

    function en_residential_get_domain()
    {
        global $wp;
        $url = home_url($wp->request);
        return en_residential_get_host($url);
    }

}

/**
 * Register and Enqueue script and style files
 */
function en_res_add_address_script_load()
{
    /** Register scripts */
    wp_register_script('rad-address-script', plugins_url('includes/addresses/js/en-rad-update-form.js', __FILE__), array('jquery'), '1.1.1', false);
    wp_register_script('rad-address-submit-form-script', plugins_url('includes/addresses/js/en-rad-address-submit-form.js', __FILE__), array('jquery'), '1.1.1', false);

    /** Enqueue registered scripts files */
    wp_enqueue_script('rad-address-script');
    wp_enqueue_script('rad-address-submit-form-script');

    /** Register Styles files */
    wp_register_style('rad-address-style', plugins_url('includes/addresses/css/en-rad-style.css', __FILE__));
    wp_enqueue_style('rad-address-style');
}

/**
 * Hook to add script and Styles files
 */
add_action('admin_enqueue_scripts', 'en_res_add_address_script_load');

/**
 * Define constants
 */
define('addon_plugin_url', __DIR__);
include_once(__DIR__ . '/includes/en-woo-addons-genrt-request-key.php');
include_once(__DIR__ . '/includes/en-woo-plugin-details.php');
include_once(__DIR__ . '/includes/en-woo-addons-includes.php');
include_once(__DIR__ . '/includes/en-woo-addons-addresses.php');
include_once(__DIR__ . '/includes/en-woo-addons-quote-settings.php');
include_once(__DIR__ . '/includes/en-woo-addons-curl-request.php');
include_once(__DIR__ . '/includes/en-woo-addons-carrier-service.php');
include_once(__DIR__ . '/includes/en-woo-addons-web-quotes.php');

require_once(__DIR__ . '/includes/addresses/inc/en-template.php');
require_once(__DIR__ . '/includes/addresses/inc/en-http-request.php');
require_once(__DIR__ . '/includes/addresses/inc/en-ajax-request.php');
require_once(__DIR__ . '/includes/addresses/js/en-rad-distance-request.php');

/**
 * Deactivavte the plugin
 */
if (!function_exists('residential_address_detection_uninstall')) {

    function residential_address_detection_uninstall()
    {

        include_once(__DIR__ . '/uninstall.php');
    }

}

/**
 * Will run after Webhook by our eniture to check the
 */
if (!function_exists('en_web_hook_to_update_residential_status')) {

    function en_web_hook_to_update_residential_status()
    {
        $action = isset($_POST['action']) ? sanitize_text_field($_POST['action']) : '';

        if ($action == '') {
            $residential_message = get_option('en_residential_message');
            $residential_message_status = get_option('en_residential_message_status');
            if (is_admin()) {
                /* if there is any message */
                if (!empty($residential_message) && $residential_message != '') {
                    $fullstop = (substr($residential_message, -1) == '.') ? '' : '.';
                    /* Error case */
                    if ($residential_message_status == 'ERROR') {
                        /* update these notifications after checking flags */
                        echo '<div id="message" class="notice-dismiss-residential notice-error notice is-dismissible "><p><strong>Residential Address Detection : </strong>' . $residential_message . $fullstop . ' Renewal attempt because of current subscription expired OR hits consumed.</p><span id="residential-del">Delete</span></div>';
                    }
                    /* Success case */
                    if ($residential_message_status == 'SUCCESS') {
                        echo '<div id="message" class="notice-dismiss-residential notice-success notice is-dismissible "><p><strong>Residential Address Detection : </strong>' . ' The subscription for this plugin was renewed because the previous plan expired or was depleted.</p><span id="residential-del">Delete</span></div>';
                    }
                }
            }
        }
    }

    add_action('admin_bar_menu', 'en_web_hook_to_update_residential_status');
}


/**
 * Ajax request to delete the bin notification.
 */
if (!function_exists('en_woo_addons_hide_residential_message_func')) {

    function en_woo_addons_hide_residential_message_func()
    {
        delete_option('en_residential_message');
        delete_option('en_residential_message_status');
        echo 'true';
        exit;
    }

    add_action('wp_ajax_nopriv_en_woo_addons_hide_residential_message', 'en_woo_addons_hide_residential_message_func');
    add_action('wp_ajax_en_woo_addons_hide_residential_message', 'en_woo_addons_hide_residential_message_func');
}