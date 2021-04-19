<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$sql = "DROP TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_customers`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_items`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_account`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_class`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_paymentmethod`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salestaxcode`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_log`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_tax`, `{$wpdb->prefix}mw_wc_qbo_desk_qbd_variation_pairs`;";

$wpdb->query($sql);

$registered_options = array(
	'mw_wc_qbo_desk_invoice_memo',
	'mw_wc_qbo_desk_default_qbo_item',
	'mw_wc_qbo_desk_default_qbo_product_account',
	'mw_wc_qbo_desk_wc_cust_role',
	'mw_wc_qbo_desk_default_shipping_product',
	'mw_wc_qbo_desk_default_coupon_code',
	'mw_wc_qbo_desk_tax_format',
	'mw_wc_qbo_desk_tax_rule',
	'mw_wc_qbo_desk_all_order_to_customer', 
	'mw_wc_qbo_desk_customer_to_sync_all_orders',
	'mw_wc_qbo_desk_rt_push_enable',
	'mw_wc_qbo_desk_rt_push_items',
	'mw_wc_qbo_desk_select2_status',
	'mw_wc_qbo_desk_select2_ajax',
	'mw_qbd_sync_last_updated_version',
	'mw_wc_qbo_desk_order_as_sales_receipt',
	'mw_wc_qbo_desk_xml_req_encoding',
	'mw_wc_qbo_desk_ord_bill_addr_map',
	'mw_wc_qbo_desk_ord_ship_addr_map',
	'mw_wc_qbo_desk_add_sku_af_lid',
	'mw_wc_qbo_desk_skip_os_lid',
	'mw_wc_qbo_desk_display_name_pattern',
	'mw_wc_qbo_desk_customer_qbo_check_billing_company',
	'mw_wc_qbo_desk_specific_order_status',
	'mw_wc_qbo_desk_ord_push_po_no',
	'mw_wc_qbo_desk_ord_push_entered_by',
	'mw_wc_qbo_desk_ord_push_rep_othername',
	'mw_wc_qbo_desk_cus_push_rep_othername',
	'mw_wc_qbo_desk_cus_push_customertype',
	'mw_wc_qbo_desk_inv_sr_txn_qb_class',
	'mw_wc_qbo_desk_set_cur_date_as_inv_date',
);

foreach($registered_options as $option){
	if(get_option( $option ))
	delete_option( $option );
}