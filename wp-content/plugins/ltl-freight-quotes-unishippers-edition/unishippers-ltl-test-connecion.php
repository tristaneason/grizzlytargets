<?php

/**
 * Unishippers Test connection
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Test connection Function
 */
if (!function_exists('unishippers_freight_submit')) {

    function unishippers_freight_submit()
    {

        $sp_user = sanitize_text_field($_POST['unishippers_username']);
        $sp_pass = sanitize_text_field($_POST['unishippers_password']);
        $api_token = sanitize_text_field($_POST['api_token']);
        $sp_acc = sanitize_text_field($_POST['unishippers_account_number']);
        $sp_licence_key = sanitize_text_field($_POST['unishippers_licence_key']);
        $sp_unishippers_account_id = sanitize_text_field($_POST['unishippers_account_id']);

        $domain = unishippers_freight_get_domain();

        $post = array(
            'username' => $sp_user,
            'password' => $sp_pass,
            'id' => $sp_unishippers_account_id,
            'apiToken' => $api_token,
            'accountNumber' => $sp_acc,
            'licence_key' => $sp_licence_key,
            'server_name' => unishippers_freight_parse_url($domain),
            'platform' => 'WordPress',
            'carrierName' => 'unishippersLtl',
            'carrier_mode' => 'test'
        );

        if (is_array($post) && count($post) > 0) {

            $unishippers_ltl_curl_obj = new Unishippers_Curl_Request();
            $output = $unishippers_ltl_curl_obj->unishippers_freight_get_curl_response(UNISHIPPERS_FREIGHT_DOMAIN_HITTING_URL . '/index.php', $post);

            print_r($output);
            die;
        }
    }

    add_action('wp_ajax_nopriv_unishippers_ltl_validate_keys', 'unishippers_freight_submit');
    add_action('wp_ajax_unishippers_ltl_validate_keys', 'unishippers_freight_submit');
}

/**
 * URL parsing
 * @param $domain
 * @return url
 */
if (!function_exists('unishippers_freight_parse_url')) {

    function unishippers_freight_parse_url($domain)
    {

        $domain = trim($domain);
        $parsed = parse_url($domain);
        if (empty($parsed['scheme'])) {

            $domain = 'http://' . ltrim($domain, '/');
        }
        $parse = parse_url($domain);
        $refinded_domain_name = $parse['host'];
        $domain_array = explode('.', $refinded_domain_name);
        if (in_array('www', $domain_array)) {

            $key = array_search('www', $domain_array);
            unset($domain_array[$key]);
            $refinded_domain_name = implode($domain_array, '.');
        }

        return $refinded_domain_name;
    }

}