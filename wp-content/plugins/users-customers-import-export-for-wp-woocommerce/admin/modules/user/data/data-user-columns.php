<?php

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$columns = array(
    'ID' => 'ID',
    'customer_id' => 'customer_id',
    'user_login' => 'user_login',
    'user_pass' => 'user_pass',
    'user_nicename' => 'user_nicename',
    'user_email' => 'user_email',
    'user_url' => 'user_url',
    'user_registered' => 'user_registered',
    'display_name' => 'display_name',
    'first_name' => 'first_name',
    'last_name' => 'last_name',
    'user_status' => 'user_status',
    'roles' => 'roles'
);
// default meta
$columns['nickname'] = 'nickname';
$columns['first_name'] = 'first_name';
$columns['last_name'] = 'last_name';
$columns['description'] = 'description';
$columns['rich_editing'] = 'rich_editing';
$columns['syntax_highlighting'] = 'syntax_highlighting';
$columns['admin_color'] = 'admin_color';
$columns['use_ssl'] = 'use_ssl';
$columns['show_admin_bar_front'] = 'show_admin_bar_front';
$columns['locale'] = 'locale';
$columns[$wpdb->prefix.'user_level'] = $wpdb->prefix.'user_level';
$columns['dismissed_wp_pointers'] = 'dismissed_wp_pointers';
$columns['show_welcome_panel'] = 'show_welcome_panel';
$columns['session_tokens'] = 'session_tokens';
$columns['last_update'] = 'last_update';


if (!function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if( is_plugin_active( 'woocommerce/woocommerce.php' ) ):
    
    $columns['billing_first_name'] = 'Billing first name';
    $columns['billing_last_name'] = 'Billing last name';
    $columns['billing_company'] = 'Billing company';
    $columns['billing_email'] = 'Billing email';
    $columns['billing_phone'] = 'Billing phone';
    $columns['billing_address_1'] = 'Billing address 1';
    $columns['billing_address_2'] = 'Billing address 2';
    $columns['billing_postcode'] = 'Billing postcode';
    $columns['billing_city'] = 'Billing city';
    $columns['billing_state'] = 'Billing state';
    $columns['billing_country'] = 'Billing country';
    $columns['shipping_first_name'] = 'Shipping first name';
    $columns['shipping_last_name'] = 'Shipping last name';
    $columns['shipping_company'] = 'Shipping company';
    $columns['shipping_phone'] = 'Shipping phone';
    $columns['shipping_address_1'] = 'Shipping address 1';
    $columns['shipping_address_2'] = 'Shipping address 2';
    $columns['shipping_postcode'] = 'Shipping postcode';
    $columns['shipping_city'] = 'Shipping city';
    $columns['shipping_state'] = 'Shipping state';
    $columns['shipping_country'] = 'Shipping country';
    
endif;

/*
global $wpdb;

$meta_keys = $wpdb->get_col("SELECT distinct(meta_key) FROM $wpdb->usermeta");

foreach ($meta_keys as $meta_key) {
    if (empty($columns[$meta_key])) {        
        $columns['meta:'.$meta_key] = 'meta:'.$meta_key; // adding an extra prefix for identifying meta while import process
    }
}
 */
return apply_filters('hf_csv_customer_post_columns', $columns);