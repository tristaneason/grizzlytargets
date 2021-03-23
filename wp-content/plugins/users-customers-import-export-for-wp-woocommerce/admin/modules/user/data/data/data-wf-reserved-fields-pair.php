<?php
if (!defined('ABSPATH')) {
    exit;
}

$columns = array(
    'ID' => array('title'=>'ID','description'=>'Customer/User ID'),
    'customer_id' => array('title'=>'Customer ID','description'=>'Customer ID'),
    'user_login' => array('title'=>'User Login','description'=>'User Login'),
    'user_pass' => array('title'=>'user_pass','description'=>'user_pass'),
    'user_nicename' => array('title'=>'user_nicename','description'=>'user_nicename'),
    'user_email' => array('title'=>'user_email','description'=>'user_email'),
    'user_url' => array('title'=>'user_url','description'=>'user_url'),
    'user_registered' => array('title'=>'user_registered','description'=>'user_registered'),
    'display_name' => array('title'=>'display_name','description'=>'display_name'),
    'first_name' => array('title'=>'first_name','description'=>'first_name'),
    'last_name' => array('title'=>'last_name','description'=>'last_name'),
    'user_status' => array('title'=>'user_status','description'=>'user_status'),
    'roles' => array('title'=>'roles','description'=>'roles'),
);

// default meta
$columns['nickname'] = array('title'=>'nickname','description'=>'');
$columns['first_name'] = array('title'=>'first_name','description'=>'');
$columns['last_name'] = array('title'=>'last_name','description'=>'');
$columns['description'] = array('title'=>'description','description'=>'');
$columns['rich_editing'] = array('title'=>'rich_editing','description'=>'');
$columns['syntax_highlighting'] = array('title'=>'syntax_highlighting','description'=>'');
$columns['admin_color'] = array('title'=>'admin_color','description'=>'');
$columns['use_ssl'] = array('title'=>'use_ssl','description'=>'');
$columns['show_admin_bar_front'] = array('title'=>'show_admin_bar_front','description'=>'');
$columns['locale'] = array('title'=>'locale','description'=>'');
$columns['wp_user_level'] = array('title'=>'wp_user_level','description'=>'');
$columns['dismissed_wp_pointers'] = array('title'=>'dismissed_wp_pointers','description'=>'');
$columns['last_update'] = array('title'=>'last_update','description'=>'');


if( is_plugin_active( 'woocommerce/woocommerce.php' ) ):
    
    $columns['billing_first_name'] = array('title'=>'billing_first_name','description'=>'');
    $columns['billing_last_name'] = array('title'=>'billing_last_name','description'=>'');
    $columns['billing_company'] = array('title'=>'billing_company','description'=>'');
    $columns['billing_email'] = array('title'=>'billing_email','description'=>'');
    $columns['billing_phone'] = array('title'=>'billing_phone','description'=>'');
    $columns['billing_address_1'] = array('title'=>'billing_address_1','description'=>'');
    $columns['billing_address_2'] = array('title'=>'billing_address_2','description'=>'');
    $columns['billing_postcode'] = array('title'=>'billing_postcode','description'=>'');
    $columns['billing_city'] = array('title'=>'billing_city','description'=>'');
    $columns['billing_state'] = array('title'=>'billing_state','description'=>'');
    $columns['billing_country'] = array('title'=>'billing_country','description'=>'');
    $columns['shipping_first_name'] = array('title'=>'shipping_first_name','description'=>'');
    $columns['shipping_last_name'] = array('title'=>'shipping_last_name','description'=>'');
    $columns['shipping_company'] = array('title'=>'shipping_company','description'=>'');
    $columns['shipping_address_1'] = array('title'=>'shipping_address_1','description'=>'');
    $columns['shipping_address_2'] = array('title'=>'shipping_address_2','description'=>'');
    $columns['shipping_postcode'] = array('title'=>'shipping_postcode','description'=>'');
    $columns['shipping_city'] = array('title'=>'shipping_city','description'=>'');
    $columns['shipping_state'] = array('title'=>'shipping_state','description'=>'');
    $columns['shipping_country'] = array('title'=>'shipping_country','description'=>'');
    
endif;




//global $wpdb;
//
//$meta_keys = $wpdb->get_col("SELECT distinct(meta_key) FROM $wpdb->usermeta WHERE meta_key NOT IN ('wp_capabilities')");
//
//foreach ($meta_keys as $meta_key) {
//    if (empty($columns[$meta_key])) {
//        $columns['meta:'.$meta_key] = array('title'=>'meta:'.$meta_key,'description'=>'');
//    }
//}
return apply_filters('hf_csv_customer_import_columns', $columns);