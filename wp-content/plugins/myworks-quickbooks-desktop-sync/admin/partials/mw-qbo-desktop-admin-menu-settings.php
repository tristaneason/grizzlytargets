<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/admin/partials
 */
?>
<?php
global $MWQDC_LB;
global $wpdb;
$page_url = 'admin.php?page=mw-qbo-desktop-settings';

 MW_QBO_Desktop_Admin::is_trial_version_check();
$settings_settings_current_tab = 'mw_qbo_sybc_settings_tab_one' ;

if(isset($_POST['mw_wc_qbd_desk_settings']) && check_admin_referer( 'myworks_wc_qbd_sync_desktop_settings', 'myworks_wc_qbd_sync_desktop_settings' )){
	//$MWQDC_LB->_p($_POST);die;
	
	update_option('mw_wc_qbo_desk_default_qbo_item', isset($_POST['mw_wc_qbo_desk_default_qbo_item'])?$_POST['mw_wc_qbo_desk_default_qbo_item']:'');
	update_option('mw_wc_qbo_desk_default_qbo_product_account', isset($_POST['mw_wc_qbo_desk_default_qbo_product_account'])?$_POST['mw_wc_qbo_desk_default_qbo_product_account']:'');
	
	update_option('mw_wc_qbo_desk_default_qbo_asset_account', isset($_POST['mw_wc_qbo_desk_default_qbo_asset_account'])?$_POST['mw_wc_qbo_desk_default_qbo_asset_account']:'');
	
	
	update_option('mw_wc_qbo_desk_display_name_pattern', isset($_POST['mw_wc_qbo_desk_display_name_pattern'])?$_POST['mw_wc_qbo_desk_display_name_pattern']:'');
	
	update_option('mw_wc_qbo_desk_customer_qbo_check_billing_company', isset($_POST['mw_wc_qbo_desk_customer_qbo_check_billing_company'])?$_POST['mw_wc_qbo_desk_customer_qbo_check_billing_company']:'');
	
	update_option('mw_wc_qbo_desk_hide_vpp_fmp_pages', isset($_POST['mw_wc_qbo_desk_hide_vpp_fmp_pages'])?$_POST['mw_wc_qbo_desk_hide_vpp_fmp_pages']:'');	
	
	update_option('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp', isset($_POST['mw_wc_qbo_desk_ed_cust_ship_mpng_smmp'])?$_POST['mw_wc_qbo_desk_ed_cust_ship_mpng_smmp']:'');
	
	update_option('mw_wc_qbo_desk_customer_match_by_name', isset($_POST['mw_wc_qbo_desk_customer_match_by_name'])?$_POST['mw_wc_qbo_desk_customer_match_by_name']:'');
	
	update_option('mw_wc_qbo_desk_customer_match_by_zipcode', isset($_POST['mw_wc_qbo_desk_customer_match_by_zipcode'])?$_POST['mw_wc_qbo_desk_customer_match_by_zipcode']:'');	
	
	update_option('mw_wc_qbo_desk_wc_cus_view_name', isset($_POST['mw_wc_qbo_desk_wc_cus_view_name'])?$_POST['mw_wc_qbo_desk_wc_cus_view_name']:'display_name');
	
	update_option('mw_wc_qbo_desk_qb_cus_view_name', isset($_POST['mw_wc_qbo_desk_qb_cus_view_name'])?$_POST['mw_wc_qbo_desk_qb_cus_view_name']:'d_name');
	
	update_option('mw_wc_qbo_desk_automap_cus_name', isset($_POST['mw_wc_qbo_desk_automap_cus_name'])?$_POST['mw_wc_qbo_desk_automap_cus_name']:'');
	
	
	update_option('mw_wc_qbo_desk_default_qbo_cogs_account', isset($_POST['mw_wc_qbo_desk_default_qbo_cogs_account'])?$_POST['mw_wc_qbo_desk_default_qbo_cogs_account']:'');
	
	update_option('mw_wc_qbo_desk_wc_cust_role', isset($_POST['mw_wc_qbo_desk_wc_cust_role'])?implode(',',$_POST['mw_wc_qbo_desk_wc_cust_role']):'');
	
	update_option('mw_wc_qbo_desk_specific_order_status', isset($_POST['mw_wc_qbo_desk_specific_order_status'])?implode(',',$_POST['mw_wc_qbo_desk_specific_order_status']):'');
	
	/*
	update_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus', isset($_POST['mw_wc_qbo_desk_wc_cust_role_sync_as_cus'])?implode(',',$_POST['mw_wc_qbo_desk_wc_cust_role_sync_as_cus']):'');
	*/
	
	
	
	update_option('mw_wc_qbo_desk_ord_push_entered_by', isset($_POST['mw_wc_qbo_desk_ord_push_entered_by'])?$_POST['mw_wc_qbo_desk_ord_push_entered_by']:'');
	//	
	update_option('mw_wc_qbo_desk_invoice_min_id', isset($_POST['mw_wc_qbo_desk_invoice_min_id'])?(int) $_POST['mw_wc_qbo_desk_invoice_min_id']:0);
	
	update_option('mw_wc_qbo_desk_ord_push_rep_othername', isset($_POST['mw_wc_qbo_desk_ord_push_rep_othername'])?$_POST['mw_wc_qbo_desk_ord_push_rep_othername']:'');
	
	update_option('mw_wc_qbo_desk_inv_sr_txn_qb_class', isset($_POST['mw_wc_qbo_desk_inv_sr_txn_qb_class'])?$_POST['mw_wc_qbo_desk_inv_sr_txn_qb_class']:'');
	
	update_option('mw_wc_qbo_desk_cus_push_rep_othername', isset($_POST['mw_wc_qbo_desk_cus_push_rep_othername'])?$_POST['mw_wc_qbo_desk_cus_push_rep_othername']:'');
	
	update_option('mw_wc_qbo_desk_cus_push_customertype', isset($_POST['mw_wc_qbo_desk_cus_push_customertype'])?$_POST['mw_wc_qbo_desk_cus_push_customertype']:'');
	
	
	update_option('mw_wc_qbo_desk_cus_push_append_client_id', isset($_POST['mw_wc_qbo_desk_cus_push_append_client_id'])?$_POST['mw_wc_qbo_desk_cus_push_append_client_id']:'');
	
	//
	update_option('mw_wc_qbo_desk_cus_imp_inc_inactive_sts', isset($_POST['mw_wc_qbo_desk_cus_imp_inc_inactive_sts'])?$_POST['mw_wc_qbo_desk_cus_imp_inc_inactive_sts']:'');
	
	//	
	update_option('mw_wc_qbo_desk_cus_skip_usa_country', isset($_POST['mw_wc_qbo_desk_cus_skip_usa_country'])?$_POST['mw_wc_qbo_desk_cus_skip_usa_country']:'');
	

	update_option('mw_wc_qbo_desk_default_shipping_product', isset($_POST['mw_wc_qbo_desk_default_shipping_product'])?$_POST['mw_wc_qbo_desk_default_shipping_product']:'');
	
	//
	update_option('mw_wc_qbo_desk_default_subtotal_product', isset($_POST['mw_wc_qbo_desk_default_subtotal_product'])?$_POST['mw_wc_qbo_desk_default_subtotal_product']:'');
	
	update_option('mw_wc_qbo_desk_default_coupon_code', isset($_POST['mw_wc_qbo_desk_default_coupon_code'])?$_POST['mw_wc_qbo_desk_default_coupon_code']:'');

	update_option('mw_wc_qbo_desk_tax_format', isset($_POST['mw_wc_qbo_desk_tax_format'])?$_POST['mw_wc_qbo_desk_tax_format']:'');
	//
	update_option('mw_wc_qbo_desk_sl_tax_map_entity', isset($_POST['mw_wc_qbo_desk_sl_tax_map_entity'])?$_POST['mw_wc_qbo_desk_sl_tax_map_entity']:'');
	
	update_option('mw_wc_qbo_desk_tax_rule', isset($_POST['mw_wc_qbo_desk_tax_rule'])?$_POST['mw_wc_qbo_desk_tax_rule']:'');
	
	update_option('mw_wc_qbo_desk_tax_rule_taxable', isset($_POST['mw_wc_qbo_desk_tax_rule_taxable'])?$_POST['mw_wc_qbo_desk_tax_rule_taxable']:'');
	
	update_option('mw_wc_qbo_desk_shipping_tax_rule_taxable', isset($_POST['mw_wc_qbo_desk_shipping_tax_rule_taxable'])?$_POST['mw_wc_qbo_desk_shipping_tax_rule_taxable']:'');
	
	update_option('mw_wc_qbo_desk_odr_tax_as_li', isset($_POST['mw_wc_qbo_desk_odr_tax_as_li'])?$_POST['mw_wc_qbo_desk_odr_tax_as_li']:'');
	
	update_option('mw_wc_qbo_desk_otli_qbd_product', isset($_POST['mw_wc_qbo_desk_otli_qbd_product'])?$_POST['mw_wc_qbo_desk_otli_qbd_product']:'');
	
	update_option('mw_wc_qbo_desk_pull_prd_price_field', isset($_POST['mw_wc_qbo_desk_pull_prd_price_field'])?$_POST['mw_wc_qbo_desk_pull_prd_price_field']:'');
	
	
	update_option('mw_wc_qbo_desk_pull_invnt_qty_field', isset($_POST['mw_wc_qbo_desk_pull_invnt_qty_field'])?$_POST['mw_wc_qbo_desk_pull_invnt_qty_field']:'');	
	
	
	/*
	update_option('mw_wc_qbo_desk_order_as_sales_receipt', isset($_POST['mw_wc_qbo_desk_order_as_sales_receipt'])?$_POST['mw_wc_qbo_desk_order_as_sales_receipt']:'');
	*/
	
	update_option('mw_wc_qbo_desk_order_qbd_sync_as', isset($_POST['mw_wc_qbo_desk_order_qbd_sync_as'])?$_POST['mw_wc_qbo_desk_order_qbd_sync_as']:'');
	/**/
	if($_POST['mw_wc_qbo_desk_order_qbd_sync_as'] == 'Per Role'){
		$mw_wc_qbo_desk_oqsa_pr_data = '';
		$mw_wc_qbo_desk_oqsa_pr_template_data = '';
		if(isset($_POST['vpr_wr']) && is_array($_POST['vpr_wr']) && isset($_POST['vpr_qost']) && is_array($_POST['vpr_qost'])){
			if(is_array($_POST['vpr_wr']) && !empty($_POST['vpr_wr']) && is_array($_POST['vpr_qost']) && !empty($_POST['vpr_qost'])){
				if(count($_POST['vpr_wr']) == count($_POST['vpr_qost'])){
					$vpr_wr = $_POST['vpr_wr'];
					$vpr_qost = $_POST['vpr_qost'];
					
					$vpr_template = $_POST['vpr_template'];
					
					$qosa_pa_data = array();
					$qosa_pa_template_data = array();
					foreach($vpr_wr as $k => $v){
						if(!empty($v)){
							$v = trim($v);
							if(isset($vpr_qost[$k]) && !empty($vpr_qost[$k])){
								$qv = trim($vpr_qost[$k]);
								$qosa_pa_data[$v] = $qv;
							}
							
							if(isset($vpr_template[$k]) && !empty($vpr_template[$k])){
								$qv = trim($vpr_template[$k]);
								$qosa_pa_template_data[$v] = $qv;
							}
							
						}
					}
					
					if(!empty($qosa_pa_data)){
						$mw_wc_qbo_desk_oqsa_pr_data = $qosa_pa_data;
					}
					
					if(!empty($qosa_pa_template_data)){
						$mw_wc_qbo_desk_oqsa_pr_template_data = $qosa_pa_template_data;
					}
				}
			}
		}
		//$MWQDC_LB->_p($mw_wc_qbo_desk_oqsa_pr_data);die;
		update_option('mw_wc_qbo_desk_oqsa_pr_data',$mw_wc_qbo_desk_oqsa_pr_data);
		update_option('mw_wc_qbo_desk_oqsa_pr_template_data',$mw_wc_qbo_desk_oqsa_pr_template_data);
	}
	
	
	update_option('mw_wc_qbo_desk_invoice_memo', isset($_POST['mw_wc_qbo_desk_invoice_memo'])?$_POST['mw_wc_qbo_desk_invoice_memo']:'');
	
	update_option('mw_wc_qbo_desk_cname_into_memo', isset($_POST['mw_wc_qbo_desk_cname_into_memo'])?$_POST['mw_wc_qbo_desk_cname_into_memo']:'');
	
	
	update_option('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num', isset($_POST['mw_wc_qbo_desk_use_qb_in_sr_so_ref_num'])?$_POST['mw_wc_qbo_desk_use_qb_in_sr_so_ref_num']:'');
	

	update_option('mw_wc_qbo_desk_all_order_to_customer', isset($_POST['mw_wc_qbo_desk_all_order_to_customer'])?$_POST['mw_wc_qbo_desk_all_order_to_customer']:'');
	/**/
	if($_POST['mw_wc_qbo_desk_all_order_to_customer'] == 'true'){
		$mw_wc_qbo_desk_aotc_rcm_data = '';
		if(isset($_POST['saoqc_wr']) && is_array($_POST['saoqc_wr']) && isset($_POST['saoqc_qc']) && is_array($_POST['saoqc_qc'])){
			if(is_array($_POST['saoqc_wr']) && !empty($_POST['saoqc_wr']) && is_array($_POST['saoqc_qc']) && !empty($_POST['saoqc_qc'])){
				if(count($_POST['saoqc_wr']) == count($_POST['saoqc_qc'])){
					$saoqc_wr = $_POST['saoqc_wr'];
					$saoqc_qc = $_POST['saoqc_qc'];
					$aotc_rcm_data = array();
					foreach($saoqc_wr as $k => $v){
						if(!empty($v)){
							$v = trim($v);
							if(isset($saoqc_qc[$k]) && !empty($saoqc_qc[$k])){
								$qv = trim($saoqc_qc[$k]);
								$aotc_rcm_data[$v] = $qv;
							}
						}
					}
					
					if(!empty($aotc_rcm_data)){
						$mw_wc_qbo_desk_aotc_rcm_data = $aotc_rcm_data;
					}					
				}
			}
		}
		//$MWQDC_LB->_p($mw_wc_qbo_desk_aotc_rcm_data);die;
		update_option('mw_wc_qbo_desk_aotc_rcm_data',$mw_wc_qbo_desk_aotc_rcm_data);
	}
	
	/*
	update_option('mw_wc_qbo_desk_customer_to_sync_all_orders', isset($_POST['mw_wc_qbo_desk_customer_to_sync_all_orders'])?$_POST['mw_wc_qbo_desk_customer_to_sync_all_orders']:'');
	*/
	
	update_option('mw_wc_qbo_desk_ord_bill_addr_map', isset($_POST['mw_wc_qbo_desk_ord_bill_addr_map'])?$_POST['mw_wc_qbo_desk_ord_bill_addr_map']:'');
	update_option('mw_wc_qbo_desk_ord_ship_addr_map', isset($_POST['mw_wc_qbo_desk_ord_ship_addr_map'])?$_POST['mw_wc_qbo_desk_ord_ship_addr_map']:'');	
	
	update_option('mw_wc_qbo_desk_add_sku_af_lid', isset($_POST['mw_wc_qbo_desk_add_sku_af_lid'])?$_POST['mw_wc_qbo_desk_add_sku_af_lid']:'');
	
	update_option('mw_wc_qbo_desk_skip_os_lid', isset($_POST['mw_wc_qbo_desk_skip_os_lid'])?$_POST['mw_wc_qbo_desk_skip_os_lid']:'');
	
	update_option('mw_wc_qbo_desk_set_cur_date_as_inv_date', isset($_POST['mw_wc_qbo_desk_set_cur_date_as_inv_date'])?$_POST['mw_wc_qbo_desk_set_cur_date_as_inv_date']:'');
	
	update_option('mw_wc_qbo_desk_no_ad_discount_li', isset($_POST['mw_wc_qbo_desk_no_ad_discount_li'])?$_POST['mw_wc_qbo_desk_no_ad_discount_li']:'');
	
	update_option('mw_wc_qbo_desk_use_qb_ba_for_eqc', isset($_POST['mw_wc_qbo_desk_use_qb_ba_for_eqc'])?$_POST['mw_wc_qbo_desk_use_qb_ba_for_eqc']:'');
	
	update_option('mw_wc_qbo_desk_qbo_push_invoice_is_print_true', isset($_POST['mw_wc_qbo_desk_qbo_push_invoice_is_print_true'])?$_POST['mw_wc_qbo_desk_qbo_push_invoice_is_print_true']:'');
	
	update_option('mw_wc_qbo_desk_order_status_after_qbd_sync', isset($_POST['mw_wc_qbo_desk_order_status_after_qbd_sync'])?$_POST['mw_wc_qbo_desk_order_status_after_qbd_sync']:'');	
	
	//
	update_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld', isset($_POST['mw_wc_qbo_desk_order_sync_qbd_dt_fld'])?$_POST['mw_wc_qbo_desk_order_sync_qbd_dt_fld']:'');
	
	update_option('mw_wc_qbo_desk_pull_enable', isset($_POST['mw_wc_qbo_desk_pull_enable'])?$_POST['mw_wc_qbo_desk_pull_enable']:'');
	
	update_option('mw_wc_qbo_desk_rt_push_enable', isset($_POST['mw_wc_qbo_desk_rt_push_enable'])?$_POST['mw_wc_qbo_desk_rt_push_enable']:'');
	update_option('mw_wc_qbo_desk_rt_pull_enable', isset($_POST['mw_wc_qbo_desk_rt_pull_enable'])?$_POST['mw_wc_qbo_desk_rt_pull_enable']:'');
	
	update_option('mw_wc_qbo_desk_rt_push_items', isset($_POST['mw_wc_qbo_desk_rt_push_items'])?implode(',',$_POST['mw_wc_qbo_desk_rt_push_items']):'');
	update_option('mw_wc_qbo_desk_rt_pull_items', isset($_POST['mw_wc_qbo_desk_rt_pull_items'])?implode(',',$_POST['mw_wc_qbo_desk_rt_pull_items']):'');
	
	update_option('mw_wc_qbo_desk_store_currency', isset($_POST['mw_wc_qbo_desk_store_currency'])?implode(',',$_POST['mw_wc_qbo_desk_store_currency']):'');
	
	
	//update_option('mw_wc_qbo_desk_rt_inventory_import_interval_time', isset($_POST['mw_wc_qbo_desk_rt_inventory_import_interval_time'])?$_POST['mw_wc_qbo_desk_rt_inventory_import_interval_time']:'');
	
	update_option('mw_wc_qbo_desk_all_invnt_pull_interver_time', isset($_POST['mw_wc_qbo_desk_all_invnt_pull_interver_time'])?$_POST['mw_wc_qbo_desk_all_invnt_pull_interver_time']:'');
	
	update_option('mw_wc_qbo_desk_rt_all_invnt_pull','true');
	
	update_option('mw_wc_qbo_desk_qbd_timezone_for_calc', isset($_POST['mw_wc_qbo_desk_qbd_timezone_for_calc'])?$_POST['mw_wc_qbo_desk_qbd_timezone_for_calc']:'');
	
	update_option('mw_wc_qbo_desk_select2_status', isset($_POST['mw_wc_qbo_desk_select2_status'])?$_POST['mw_wc_qbo_desk_select2_status']:'');
	update_option('mw_wc_qbo_desk_select2_ajax', isset($_POST['mw_wc_qbo_desk_select2_ajax'])?$_POST['mw_wc_qbo_desk_select2_ajax']:'');
	
	update_option('mw_wc_qbo_desk_add_xml_req_into_log', isset($_POST['mw_wc_qbo_desk_add_xml_req_into_log'])?$_POST['mw_wc_qbo_desk_add_xml_req_into_log']:'');	
	
	update_option('mw_wc_qbo_desk_enable_db_status_chk_sb', isset($_POST['mw_wc_qbo_desk_enable_db_status_chk_sb'])?$_POST['mw_wc_qbo_desk_enable_db_status_chk_sb']:'');
	
	update_option('mw_wc_qbo_desk_xml_req_encoding', isset($_POST['mw_wc_qbo_desk_xml_req_encoding'])?$_POST['mw_wc_qbo_desk_xml_req_encoding']:'utf-8');
	
	update_option('mw_wc_qbo_desk_xml_req_locale', isset($_POST['mw_wc_qbo_desk_xml_req_locale'])?$_POST['mw_wc_qbo_desk_xml_req_locale']:'US');
	
	
	update_option('mw_wc_qbo_desk_save_log_for', isset($_POST['mw_wc_qbo_desk_save_log_for'])?(int) $_POST['mw_wc_qbo_desk_save_log_for']:'10');
	
	//
	update_option('mw_wc_qbo_desk_save_pqe_for', isset($_POST['mw_wc_qbo_desk_save_pqe_for'])?(int) $_POST['mw_wc_qbo_desk_save_pqe_for']:'0');
	
	$settings_current_tab = isset($_POST['mw_qbd_sync_settings_current_tab']) ? $_POST['mw_qbd_sync_settings_current_tab'] : 'mw_qbo_sybc_settings_tab_one';

	$MWQDC_LB->set_session_val('settings_update_message',__('Settings updated successfully.','mw_wc_qbo_desk'));
	$MWQDC_LB->set_session_val('settings_settings_current_tab',$settings_current_tab);
	$MWQDC_LB->redirect($page_url);
}

$session_settings_tab = $MWQDC_LB->get_session_val('settings_settings_current_tab','',true);
if(!empty($session_settings_tab)){
	$settings_settings_current_tab = $session_settings_tab;
}

$qbo_product_options = '';
if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
	$qbo_product_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','name','','name ASC','',true);
}

$cnt_key = "CONCAT(name,' (',acc_type,')') AS acc_name";
$qbo_account_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_account','qbd_id',$cnt_key,'','name ASC','',true);

//
$cnt_key = "CONCAT(name,' (',t_type,')') AS template_name";
$qbo_template_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_template','qbd_id',$cnt_key,'','name ASC','',true);

$list_selected = '';
if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_qbo_item\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_item').'\');';
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_customer_to_sync_all_orders\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders').'\');';
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_shipping_product\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_shipping_product').'\');';
	//
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_subtotal_product\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_subtotal_product').'\');';
	
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_coupon_code\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_coupon_code').'\');';
	
	$list_selected.='jQuery(\'#mw_wc_qbo_desk_otli_qbd_product\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_otli_qbd_product').'\');';
}



$qbo_customer_options = '';
if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
	$cdd_sb = 'd_name';
	$mw_wc_qbo_sync_client_sort_order = $MWQDC_LB->sanitize($MWQDC_LB->get_option('mw_wc_qbo_desk_client_sort_order'));
	if($mw_wc_qbo_sync_client_sort_order!=''){
		$cdd_sb = $mw_wc_qbo_sync_client_sort_order;
		if($cdd_sb!='d_name' && $cdd_sb!='first_name' && $cdd_sb!='last_name' && $cdd_sb!='company'){
			$cdd_sb = 'd_name';
		}
	}
	$qbo_customer_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','d_name','',$cdd_sb.' ASC','',true);
}

$order_statuses = wc_get_order_statuses();

$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_qbo_product_account\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_product_account').'\');';
$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_qbo_asset_account\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_asset_account').'\');';
$list_selected.='jQuery(\'#mw_wc_qbo_desk_default_qbo_cogs_account\').val(\''.$MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_cogs_account').'\');';
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php echo $MWQDC_LB->get_admin_get_toggle_switch_css_js();?>

<?php 
	$wu_roles = get_editable_roles();
?>

<div class="mw_wc_qbo_desk_container">
<form method="post">
<?php wp_nonce_field( 'myworks_wc_qbd_sync_desktop_settings', 'myworks_wc_qbd_sync_desktop_settings' ); ?>
<input type="hidden" name="mw_qbd_sync_settings_current_tab" id="mw_qbd_sync_settings_current_tab" value="<?php echo $settings_settings_current_tab ?>">
<nav class="mw-qbo-sync-grey">
	<div class="nav-wrapper">
		<a class="brand-logo left" href="javascript:void(0)">
			<img src="<?php echo plugins_url( 'myworks-quickbooks-desktop-sync/admin/image/mwd-logo.png' ) ?>">
		</a>
		<ul class="hide-on-med-and-down right">
			<li class="default-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_one"><?php echo __('Default','mw_wc_qbo_desk') ?></a></li>
			<li class="invoice-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_order"><?php echo __('Order','mw_wc_qbo_desk') ?></a></li>
			
			<li class="customer-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_customer"><?php echo __('Customer','mw_wc_qbo_desk') ?></a></li>
			
			<li class="tax-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_tax"><?php echo __('Taxes','mw_wc_qbo_desk') ?></a></li>
			<li class="mapping-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_mapping"><?php echo __('Mapping','mw_wc_qbo_desk') ?></a></li>
			
			<li class="pull-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_pull"><?php echo __('Pull','mw_wc_qbo_desk') ?></a></li>
			
			<li class="webhook-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_wh"><?php echo __('Automatic Sync','mw_wc_qbo_desk') ?></a></li>
			<li class="misc-menu mwqs_stb"><a href="javascript:void(0)" id="mw_qbo_sybc_settings_tab_two"><?php echo __('Miscellaneous','mw_wc_qbo_desk') ?></a></li>			
		</ul>
	</div>
</nav>

<div class="container mwqbd" id="mw_qbo_sybc_settings_tables">
	<div class="card">
		<div class="card-content">
			<div class="row">
				<div class="col s12 m12 l12">
					<div class="row">
						<div class="col s12 m12 l12">
                          	<div id="mw_qbo_sybc_settings_tab_one_body" style="display: none;">
							<h6><?php echo __('Default Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body_body">
								<tbody>                				
									<tr>
										<th class="title-description">
									    	<?php echo __('Default for unmatched products','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$dd_options = '<option value=""></option>';
															$dd_ext_class = '';
															if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
																$dd_ext_class = 'mwqs_dynamic_select_desk';
																if($MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_item')!=''){
																	$itemid = $MWQDC_LB->get_option('mw_wc_qbo_desk_default_qbo_item');
																	$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
																	if($qb_item_name!=''){
																		$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
																	}
																}
															}else{
																$dd_options.=$qbo_product_options;
															}
														?>													
														
														<select name="mw_wc_qbo_desk_default_qbo_item" id="mw_wc_qbo_desk_default_qbo_item" class="filled-in production-option mw_wc_qbo_desk_select <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
														
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Default QuickBooks Desktop Product assigned to order line items not mapped to a product.','mw_wc_qbo_desk') ?></span>
										</div>
	                                    </td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Default QuickBooks Sales Account for New Products ','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_default_qbo_product_account" id="mw_wc_qbo_desk_default_qbo_product_account" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
														<?php echo $qbo_account_options;?>
											            </select>
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Default account assigned to your WooCommerce products when pushing them over to QBD. This should be an income or expense account.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Default QuickBooks Inventory Asset Account for New Products','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_default_qbo_asset_account" id="mw_wc_qbo_desk_default_qbo_asset_account" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
											            <?php echo $qbo_account_options ?>
											            </select>
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Default inventory asset account assigned to your WooCommerce products when pushing them over to QBD.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Default QuickBooks COGS Account for New Products ','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_default_qbo_cogs_account" id="mw_wc_qbo_desk_default_qbo_cogs_account" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
											            <?php echo $qbo_account_options ?>
											            </select>
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Default Cost of Goods Sold account assigned to your WooCommerce products when pushing them over to QBD.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									
									<tr style="display:none;">
										<th class="title-description">
									    	<?php echo __('Discount Account for New Products','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_default_qbo_discount_account" id="mw_wc_qbo_desk_default_qbo_discount_account" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
											            <?php echo $qbo_account_options ?>
											            </select>
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Default Income Account in QuickBooks Desktop for unmapped Discounts in WooCommerce.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Default QuickBooks Shipping Product','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$dd_options = '<option value=""></option>';
															$dd_ext_class = '';
															if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
																$dd_ext_class = 'mwqs_dynamic_select_desk';
																if($MWQDC_LB->get_option('mw_wc_qbo_desk_default_shipping_product')!=''){
																	$itemid = $MWQDC_LB->get_option('mw_wc_qbo_desk_default_shipping_product');
																	$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
																	if($qb_item_name!=''){
																		$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
																	}
																}
															}else{
																$dd_options.=$qbo_product_options;
															}
														?>
														
														<select name="mw_wc_qbo_desk_default_shipping_product" id="mw_wc_qbo_desk_default_shipping_product" class="filled-in production-option mw_wc_qbo_desk_select <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
														
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose a QuickBooks Desktop Product to fallback to for unmapped Shipping Methods.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>

									<tr>
										<th class="title-description">
									    	<?php echo __('Default QuickBooks Coupon Code Product','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>													
														<?php
															$dd_options = '<option value=""></option>';
															$dd_ext_class = '';
															if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
																$dd_ext_class = 'mwqs_dynamic_select_desk';
																if($MWQDC_LB->get_option('mw_wc_qbo_desk_default_coupon_code')!=''){
																	$itemid = $MWQDC_LB->get_option('mw_wc_qbo_desk_default_coupon_code');
																	$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
																	if($qb_item_name!=''){
																		$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
																	}
																}
															}else{
																$dd_options.=$qbo_product_options;
															}
														?>
														
														<select name="mw_wc_qbo_desk_default_coupon_code" id="mw_wc_qbo_desk_default_coupon_code" class="filled-in production-option mw_wc_qbo_desk_select <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
														
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose a QuickBooks Desktop Product to fallback to in invoice line items for unmapped Coupon Codes.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									
									<tr style="display:none;">
										<th class="title-description">
									    	<?php echo __('QuickBooks Desktop Timezone For Time Diference Calculation','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<?php
													$qbd_timezone_arr = array();
													$tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
													if(is_array($tzlist) && count($tzlist)){
														$qbd_timezone_arr = array_combine($tzlist, $tzlist);
													}
													
													$mw_wc_qbo_desk_qbd_timezone_for_calc = $MWQDC_LB->get_option('mw_wc_qbo_desk_qbd_timezone_for_calc');
													if(empty($mw_wc_qbo_desk_qbd_timezone_for_calc)){
														$mw_wc_qbo_desk_qbd_timezone_for_calc = 'America/Los_Angeles';
													}
												?>
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_qbd_timezone_for_calc" id="mw_wc_qbo_desk_qbd_timezone_for_calc" class="filled-in production-option mw_wc_qbo_desk_select">										           
														<?php $MWQDC_LB->only_option($mw_wc_qbo_desk_qbd_timezone_for_calc,$qbd_timezone_arr)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose timezone for calculating time interval','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php if($MWQDC_LB->is_plugin_active('woocommerce-aelia-currencyswitcher')):?>
									<?php
										$aelia_enabled_currencies = array();
										$wc_aelia_currency_switcher = get_option('wc_aelia_currency_switcher');
										if(is_array($wc_aelia_currency_switcher) && count($wc_aelia_currency_switcher)){
											if(isset($wc_aelia_currency_switcher['enabled_currencies']) && is_array($wc_aelia_currency_switcher['enabled_currencies'])){
												$aelia_enabled_currencies = $wc_aelia_currency_switcher['enabled_currencies'];
												$aelia_enabled_currencies = array_combine($aelia_enabled_currencies, $aelia_enabled_currencies);
											}
										}
									?>
									<tr>
										<th class="title-description">
									    	<?php echo __('Enable currencies for your WooCommerce store','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_store_currency[]" id="mw_wc_qbo_desk_store_currency" class="filled-in production-option mw_wc_qbo_desk_select" multiple="multiple">
														<option value=""></option>
											            <?php 
															$sel_cur_list = $MWQDC_LB->get_option('mw_wc_qbo_desk_store_currency');
															if($sel_cur_list!=''){
																$sel_cur_list = explode(',',$sel_cur_list);
															}
														?>
											            <?php //get_world_currency_list ?>
														 <?php $MWQDC_LB->only_option($sel_cur_list,$aelia_enabled_currencies) ?>
											            </select>													
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select currencies for your WooCommerce store. You can select multiple currency as per requirement.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>
									<?php endif;?>
									
									</tbody>
								</table>
							</div>
							</div>							
							

							<div id="mw_qbo_sybc_settings_tab_order_body" style="display: none;">
							<h6><?php echo __('Order Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body_body">
								<tbody>
									<!--mw_wc_qbo_desk_order_as_sales_receipt-->
									<?php 
										$wo_qsa = $MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as');
										if($wo_qsa!='Invoice' && $wo_qsa!='SalesReceipt' && $wo_qsa!='SalesOrder' && $wo_qsa!='Estimate' && $wo_qsa!='Per Role' && $wo_qsa!='Per Gateway'){
											$wo_qsa = 'Invoice';
										}
									?>
									<tr>
										<th class="title-description" width="35%">
									    	<?php echo __('Sync WooCommerce Orders as','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">												
													<div class="switch-toggle switch-3 switch-candy">
														<input id="wo_qsa_inv" value="Invoice" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='Invoice'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_inv" onclick="">Invoice</label>
														
														<input id="wo_qsa_sr" value="SalesReceipt" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='SalesReceipt'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_sr" onclick="">SalesReceipt</label>
														
														<input id="wo_qsa_so" value="SalesOrder" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='SalesOrder'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_so" onclick="">SalesOrder</label>
														
														<input id="wo_qsa_est" value="Estimate" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='Estimate'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_est" onclick="">Estimate</label>
														
														<?php if(is_array($wu_roles) && count($wu_roles)):?>
														<input id="wo_qsa_vpr" value="Per Role" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='Per Role'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_vpr" onclick="">Per Role</label>
														<?php endif;?>
														
														<input id="wo_qsa_pg" value="Per Gateway" name="mw_wc_qbo_desk_order_qbd_sync_as" type="radio" <?php if($wo_qsa=='Per Gateway'){echo 'checked="checked"';}?>>
														<label for="wo_qsa_pg" onclick="">Per Gateway</label>
														
														<a></a>
													</div>
													
													<div id="mwoqsa_rm">
														<?php
														if($wo_qsa == 'Per Gateway'){
															echo '<small>Please select the order sync type per gateway in Map > Payment Method page.</small>';
														}
														?>
													</div>
													
												</div>
											</div>
										</td>
										<td width="5%">
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose Wocommerce Order Syns as QBD Invoice, SalesReceipt, SalesOrder or Estimate','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<!--**-->
									<?php
										$qost_arr = array(
											'Invoice' => 'Invoice',
											'SalesReceipt' => 'SalesReceipt',
											'SalesOrder' => 'SalesOrder',
											'Estimate' => 'Estimate'										
										);
										
										$mw_wc_qbo_desk_oqsa_pr_data = get_option('mw_wc_qbo_desk_oqsa_pr_data');
										$mw_wc_qbo_desk_oqsa_pr_template_data = get_option('mw_wc_qbo_desk_oqsa_pr_template_data');
									?>
									<?php if(is_array($wu_roles) && count($wu_roles)):?>
									<tr id="wo_qsa_vpr_map_tr" <?php if($wo_qsa != 'Per Role'){echo 'style="display:none;"';}?>>
										<th class="title-description">
											<?php echo __('WooCommerce User Role -> Order Sync Type Mapping','mw_wc_qbo_desk') ?>
										</th>
										<td>
											<table>
												<?php if(!empty($qbo_template_options)):?>
												<tr style="border:none; background:none;">
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>QuickBooks Template</td>
												</tr>
												<?php endif;?>
												
												<?php foreach ($wu_roles as $role_name => $role_info):?>
												<?php 
													$qost_va = '';
													if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && isset($mw_wc_qbo_desk_oqsa_pr_data[$role_name])){
														$qost_va = $mw_wc_qbo_desk_oqsa_pr_data[$role_name];
													}
												?>
												<tr style="border:none; background:none;">
													<td width="30%">
														<?php echo $role_info['name'];?>
														<input type="hidden" name="vpr_wr[]" value="<?php echo $role_name;?>">
													</td>
													
													<td>												
													<select name="vpr_qost[]" class="filled-in production-option mw_wc_qbo_desk_select">
														<?php echo $MWQDC_LB->only_option($qost_va,$qost_arr);?>
													</select>
													</td>
													
													<?php if(!empty($qbo_template_options)):?>
													<?php
														$qost_template = '';
														if(is_array($mw_wc_qbo_desk_oqsa_pr_template_data) && isset($mw_wc_qbo_desk_oqsa_pr_template_data[$role_name])){
															$qost_template = $mw_wc_qbo_desk_oqsa_pr_template_data[$role_name];
															$list_selected.='jQuery(\'#vpr_template_'.$role_name.'\').val(\''.$qost_template.'\');';
														}
													?>
													<td>
													<select id="vpr_template_<?php echo $role_name;?>" name="vpr_template[]" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
														<?php echo $qbo_template_options;?>
													</select>
													</td>
													<?php endif;?>
												</tr>
												<?php endforeach;?>
												<?php 
													$qost_va = '';
													if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && isset($mw_wc_qbo_desk_oqsa_pr_data['wc_guest_user'])){
														$qost_va = $mw_wc_qbo_desk_oqsa_pr_data['wc_guest_user'];
													}
												?>
												<tr style="border:none; background:none;">
													<td>
														<strong>Guest User</strong>
														<input type="hidden" name="vpr_wr[]" value="wc_guest_user">
													</td>
													
													<td>
													<select name="vpr_qost[]" class="filled-in production-option mw_wc_qbo_desk_select">
														<?php echo $MWQDC_LB->only_option($qost_va,$qost_arr);?>
													</select>
													</td>
													
													<?php if(!empty($qbo_template_options)):?>
													<?php
														$qost_template = '';
														if(is_array($mw_wc_qbo_desk_oqsa_pr_template_data) && isset($mw_wc_qbo_desk_oqsa_pr_template_data['wc_guest_user'])){
															$qost_template = $mw_wc_qbo_desk_oqsa_pr_template_data['wc_guest_user'];
															$list_selected.='jQuery(\'#vpr_template_wc_guest_user\').val(\''.$qost_template.'\');';
														}
													?>
													<td>
													<select id="vpr_template_wc_guest_user" name="vpr_template[]" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
														<?php echo $qbo_template_options;?>
													</select>
													</td>
													<?php endif;?>
													
												</tr>											
											</table>
										</td>
										
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose Wocommerce Order Syns as QBD Invoice, SalesReceipt, SalesOrder or Estimate','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									<?php endif;?>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Block syncing orders before ID','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="text" name="mw_wc_qbo_desk_invoice_min_id" id="mw_wc_qbo_desk_invoice_min_id" value="<?php echo (int) $MWQDC_LB->get_option('mw_wc_qbo_desk_invoice_min_id');?>">
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Disable/block syncing WooCommerce orders before this Order ID to QuickBooks Desktop. Default is 0 as previous orders will not be synced anyways unless edited and saved.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Sync Order Notes to Statement Memo','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_invoice_memo" id="mw_wc_qbo_desk_invoice_memo" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_invoice_memo')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to enable the syncing of the WooCommerce Order Note contents to the QBO Invoice Memo field.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Sync Customer Name to Statement Memo','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cname_into_memo" id="mw_wc_qbo_desk_cname_into_memo" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_cname_into_memo')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to enable the syncing of the WooCommerce Customer Name to the QBO Invoice Memo field.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Use QuickBooks # Sequence','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_use_qb_in_sr_so_ref_num" id="mw_wc_qbo_desk_use_qb_in_sr_so_ref_num" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __(' Check to use the existing QuickBooks numbering sequence instead of the WooCommerce order number, when syncing orders into QuickBooks. Enabling this will use the <b>next</b> QuickBooks number when creating the record in QuickBooks. Our custom field mapping helper plugin is available on request to send the WooCommerce order number to a different field in QuickBooks if needed for reference.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
								
									<tr>
										<th class="title-description">
									    	<?php echo __('Sync all orders to one QuickBooks Customer','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_all_order_to_customer" id="mw_wc_qbo_desk_all_order_to_customer" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_all_order_to_customer')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Turn on to sync WooCommerce orders to one QuickBooks Customer.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<!--Hidden -> Not in use For Now-->
									<tr id="mw_wc_qbo_desk_customer_to_sync_all_orders_tr" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_all_order_to_customer')!='true' || $hidden=true) echo 'style="display: none;"' ?>>
										<th class="title-description">
									    	<?php echo __('Select QuickBooks Customer Sync all orders','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
														$custId = $MWQDC_LB->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
														$dd_options = '<option value=""></option>';
														$dd_ext_class = '';
														if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
															$dd_ext_class = 'mwqs_dynamic_select_desk';
															if($custId!=''){
																$qbo_c_dname = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','d_name','qbd_customerid',$custId);
																$dd_options = '<option value="'.$custId.'">'.$qbo_c_dname.'</option>';
															}
														}else{
															$dd_options.=$qbo_customer_options;														
														}										
														?>
														<select name="mw_wc_qbo_desk_customer_to_sync_all_orders" id="mw_wc_qbo_desk_customer_to_sync_all_orders" class="filled-in production-option mw_wc_qbo_desk_select_cus <?php echo $dd_ext_class;?>">
														<?php echo $dd_options;?>
														</select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select QuickBooks Customer Sync all orders. Please enable the option above to make it working.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<!--Hidden -> Not in use For Now-->
									<tr <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_all_order_to_customer')!='true' || $hidden=true) echo 'style="display: none;"' ?> id="mw_wc_qbo_desk_wc_cust_role_sync_as_cus_tr">
										<th class="title-description">
									    	<?php _e('Ignore these roles / Sync to individual mapped customer','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>	
														<?php
														$role_dd_options = '';
														$mw_wc_qbo_desk_wc_cust_role_sync_as_cus = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
														$mw_wc_qbo_sync_wc_cust_role_exp = explode(',',$mw_wc_qbo_desk_wc_cust_role_sync_as_cus);
														
														if(is_array($wu_roles) && count($wu_roles)){
															foreach ($wu_roles as $role_name => $role_info):
																$selected = '';
																if($mw_wc_qbo_desk_wc_cust_role_sync_as_cus != ''){
																	if( in_array( $role_name, $mw_wc_qbo_sync_wc_cust_role_exp ) ){
																		$selected = 'selected="selected"';							
																	}else{
																		$selected = '';
																	}
																}
																$role_dd_options .= '<option value="'.$role_name.'" '.$selected.'>'.$role_name.'</option>';
															endforeach;
														}
														
	    												?>
														<select name="mw_wc_qbo_desk_wc_cust_role_sync_as_cus[]" id="mw_wc_qbo_desk_wc_cust_role_sync_as_cus" class="filled-in production-option mw_wc_qbo_desk_select" multiple="multiple">
															<?php echo $role_dd_options;?>
														</select>												
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('The user roles selected here will be ignored by the above setting to sync all orders to one QB customer. Orders for customers in the roles selected here will be synced to their own individual QuickBooks customer accounts.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<!--**-->
									<?php if(is_array($wu_roles) && count($wu_roles)):?>
									<?php
										$mw_wc_qbo_desk_aotc_rcm_data = get_option('mw_wc_qbo_desk_aotc_rcm_data');
									?>
									<tr id="saoqc_tr" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_all_order_to_customer')!='true') echo 'style="display: none;"' ?>>
										<th class="title-description">
										<?php echo __('WooCommerce User Role -> QuickBooks Customer Mapping','mw_wc_qbo_desk') ?>
										</th>
										<td>
											<table>
												<?php foreach ($wu_roles as $role_name => $role_info):?>
												<tr style="border:none; background:none;">
													<td width="30%">
														<?php echo $role_info['name'];?>
														<input type="hidden" name="saoqc_wr[]" value="<?php echo $role_name;?>">
													</td>
													<?php
													$custId = (is_array($mw_wc_qbo_desk_aotc_rcm_data) && isset($mw_wc_qbo_desk_aotc_rcm_data[$role_name]))?$mw_wc_qbo_desk_aotc_rcm_data[$role_name]:'';
													
													$dd_options = '<option value="Individual">Individual</option>';
													$dd_ext_class = '';
													if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
														$dd_ext_class = 'mwqs_dynamic_select_desk';
														if($custId!=''){
															$qbo_c_dname = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','d_name','qbd_customerid',$custId);
															$dd_options = '<option value="'.$custId.'">'.$qbo_c_dname.'</option>';
														}
													}else{
														$dd_options.=$qbo_customer_options;
													}
													
													if(!empty($custId)){
														$list_selected.='jQuery(\'#saoqc_qc_'.$role_name.'\').val(\''.$custId.'\');';
													}
													?>
													<td>												
														<select id="saoqc_qc_<?php echo $role_name;?>" name="saoqc_qc[]" class="filled-in production-option mw_wc_qbo_desk_select_cus <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
													</td>
												</tr>
												<?php endforeach;?>
												
												<tr style="border:none; background:none;">
													<td width="30%">
														<strong>Guest User</strong>
														<input type="hidden" name="saoqc_wr[]" value="wc_guest_user">
													</td>
													
													<?php
													$custId = (is_array($mw_wc_qbo_desk_aotc_rcm_data) && isset($mw_wc_qbo_desk_aotc_rcm_data['wc_guest_user']))?$mw_wc_qbo_desk_aotc_rcm_data['wc_guest_user']:'';
													$dd_options = '<option value="Individual">Individual</option>';
													$dd_ext_class = '';
													if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
														$dd_ext_class = 'mwqs_dynamic_select_desk';
														if($custId!=''){
															$qbo_c_dname = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','d_name','qbd_customerid',$custId);
															$dd_options = '<option value="'.$custId.'">'.$qbo_c_dname.'</option>';
														}
													}else{
														$dd_options.=$qbo_customer_options;
													}
													
													if(!empty($custId)){
														$list_selected.='jQuery(\'#saoqc_qc_wc_guest_user\').val(\''.$custId.'\');';
													}
													?>
													<td>												
														<select id="saoqc_qc_wc_guest_user" name="saoqc_qc[]" class="filled-in production-option mw_wc_qbo_desk_select_cus <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
													</td>
												</tr>
											</table>
										</td>
										
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select QuickBooks Customer.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									<?php endif;?>
									
									<!--Address Mapping-->
									<tr>
										<th class="title-description">
									    	<?php echo __('Billing Address Format (Max 5 Line)','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$mw_wc_qbo_desk_ord_bill_addr_map = $MWQDC_LB->get_option('mw_wc_qbo_desk_ord_bill_addr_map');
															if(empty($mw_wc_qbo_desk_ord_bill_addr_map)){
																$mw_wc_qbo_desk_ord_bill_addr_map = $MWQDC_LB->get_default_ord_ba();
															}
														?>
														<textarea rows="5" cols="50" name="mw_wc_qbo_desk_ord_bill_addr_map" id="mw_wc_qbo_desk_ord_bill_addr_map"><?php echo $mw_wc_qbo_desk_ord_bill_addr_map;?></textarea>
														<br />
														<span>Available Fields</span>
														<br />
														<i style="color:grey;">
														{_billing_first_name}&nbsp;
														{_billing_last_name}&nbsp;
														{_billing_company}&nbsp;													
														{_billing_address_1}&nbsp;
														<br />
														{_billing_address_2}&nbsp;													
														{_billing_city}&nbsp;													
														{_billing_state}&nbsp;
														{_billing_postcode}&nbsp;
														{_billing_phone}&nbsp;													
														{_billing_country}&nbsp;
														<?php 
														$ba_ext_f = $MWQDC_LB->ord_ba_ext_formats();
														echo (is_array($ba_ext_f) && !empty($ba_ext_f))?implode('&nbsp;',$ba_ext_f):'';
														?>
														</i>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Enter address fields separeted by newline','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Shipping Address Format (Max 5 Line)','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$mw_wc_qbo_desk_ord_ship_addr_map = $MWQDC_LB->get_option('mw_wc_qbo_desk_ord_ship_addr_map');
															if(empty($mw_wc_qbo_desk_ord_ship_addr_map)){
																$mw_wc_qbo_desk_ord_ship_addr_map = $MWQDC_LB->get_default_ord_sa();
															}
														?>
														<textarea rows="5" cols="50" name="mw_wc_qbo_desk_ord_ship_addr_map" id="mw_wc_qbo_desk_ord_ship_addr_map"><?php echo $mw_wc_qbo_desk_ord_ship_addr_map;?></textarea>
														<br />
														<span>Available Fields</span>
														<br />
														<i style="color:grey;">
														{_shipping_first_name}&nbsp;
														{_shipping_last_name}&nbsp;
														{_shipping_company}&nbsp;													
														{_shipping_address_1}&nbsp;
														<br />
														{_shipping_address_2}&nbsp;													
														{_shipping_city}&nbsp;													
														{_shipping_state}&nbsp;
														{_shipping_postcode}&nbsp;
														{_shipping_country}&nbsp;
														<?php 
														$sa_ext_f = $MWQDC_LB->ord_sa_ext_formats();
														echo (is_array($sa_ext_f) && !empty($sa_ext_f))?implode('&nbsp;',$sa_ext_f):'';
														?>
														</i>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Enter address fields separeted by newline','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Add Product SKU after Line Item Description','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_add_sku_af_lid" id="mw_wc_qbo_desk_add_sku_af_lid" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_add_sku_af_lid')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Turn on to add Product SKU after Invoice /Sales Receipts Line Item Description.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Use QuickBooks Line Item Descriptions','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_skip_os_lid" id="mw_wc_qbo_desk_skip_os_lid" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_skip_os_lid')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Enable this setting to let the QuickBooks item descriptions be used in the Description column when syncing orders into QuickBooks. When this setting is off, the name of the WooCommerce product in the order is used for the QuickBooks description field for the order.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php _e('Only sync orders if they are a specific status','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php 
															$mw_wc_qbo_desk_specific_order_status = $MWQDC_LB->get_option('mw_wc_qbo_desk_specific_order_status');
															if($mw_wc_qbo_desk_specific_order_status!=''){
																$mw_wc_qbo_desk_specific_order_status = explode(',',$mw_wc_qbo_desk_specific_order_status);
															}
														?>
														<select name="mw_wc_qbo_desk_specific_order_status[]" id="mw_wc_qbo_desk_specific_order_status" class="filled-in production-option mw_wc_qbo_desk_select" multiple="multiple">								
															<?php echo  $MWQDC_LB->only_option($mw_wc_qbo_desk_specific_order_status,$order_statuses);?>
														</select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Only sync orders (real time) if they are a specific status (Processing, On Hold, Completed, Cancelled etc). This is applicable if above option is enabled.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Value for Other field','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="text" name="mw_wc_qbo_desk_ord_push_entered_by" id="mw_wc_qbo_desk_ord_push_entered_by" value="<?php echo $MWQDC_LB->get_option('mw_wc_qbo_desk_ord_push_entered_by');?>">
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Other','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Rep to be assigned to all orders','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<select name="mw_wc_qbo_desk_ord_push_rep_othername" id="mw_wc_qbo_desk_ord_push_rep_othername" class="mw_wc_qbo_desk_select">
										            <option value=""></option>
													<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_ord_push_rep_othername'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep','qbd_id','sr_e_ref_name','','sr_e_ref_name ASC','',true);?>
										            </select>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose your QBD Rep.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Class to be assigned to all orders','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<select name="mw_wc_qbo_desk_inv_sr_txn_qb_class" id="mw_wc_qbo_desk_inv_sr_txn_qb_class" class="mw_wc_qbo_desk_select">
										            <option value=""></option>
													<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_class','qbd_id','name','','name ASC','',true);?>
										            </select>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select the class associated with the transaction for invoice, salesreceipt and salesorder','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Set QuickBooks order date according to the most recent order push date ','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_set_cur_date_as_inv_date" id="mw_wc_qbo_desk_set_cur_date_as_inv_date" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_set_cur_date_as_inv_date')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to set the QuickBooks Desktop order date to be the most recent date it was pushed from WooCommerce - instead of the original WooCommerce Order date.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Sync order discounts within original line item','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_no_ad_discount_li" id="mw_wc_qbo_desk_no_ad_discount_li" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_no_ad_discount_li')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('If left off, order discounts will be synced to QB in their own negative line item, using the product set as the Default Discount Item in Settings. If turned on, order discounts will be synced to QuickBooks within the original line item, as the discounted price - instead of the full price line item + dicount line item.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<!--#_#-->
									<tr>
										<th class="title-description">
									    	<?php _e('WooCommerce order status after synced into QuickBooks','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php 
															$mw_wc_qbo_desk_order_status_after_qbd_sync = $MWQDC_LB->get_option('mw_wc_qbo_desk_order_status_after_qbd_sync');
														?>
														<select name="mw_wc_qbo_desk_order_status_after_qbd_sync" id="mw_wc_qbo_desk_order_status_after_qbd_sync" class="filled-in production-option mw_wc_qbo_desk_select">
														<option value=""></option>
															<?php echo  $MWQDC_LB->only_option($mw_wc_qbo_desk_order_status_after_qbd_sync,$order_statuses);?>
														</select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('If selected then WooCommerce order status will be updated after it synced into QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Use QuickBooks Billing Address For Existing QuickBooks Customer','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_use_qb_ba_for_eqc" id="mw_wc_qbo_desk_use_qb_ba_for_eqc" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_use_qb_ba_for_eqc')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('If checked, it will not send the billing address to QuickBooks for existing QuickBooks customer (order,salesreceipt,salesorder,estimate.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>								
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Set order To Be Printed when synced','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_qbo_push_invoice_is_print_true" id="mw_wc_qbo_desk_qbo_push_invoice_is_print_true" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_qbo_push_invoice_is_print_true')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to set the QuickBooks Desktop order as ToBePrinted once synced into QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Select QuickBooks Subtotal Product','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$dd_options = '<option value=""></option>';
															$dd_ext_class = '';
															if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
																$dd_ext_class = 'mwqs_dynamic_select_desk';
																if($MWQDC_LB->get_option('mw_wc_qbo_desk_default_subtotal_product')!=''){
																	$itemid = $MWQDC_LB->get_option('mw_wc_qbo_desk_default_subtotal_product');
																	$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
																	if($qb_item_name!=''){
																		$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
																	}
																}
															}else{
																$dd_options.=$qbo_product_options;
															}
														?>
														
														<select name="mw_wc_qbo_desk_default_subtotal_product" id="mw_wc_qbo_desk_default_subtotal_product" class="filled-in production-option mw_wc_qbo_desk_select <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
														
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose a QuickBooks Subtotal Product for Subtotal Line.','mw_wc_qbo_desk') ?></span>
											</div>
	                                    </td>
									</tr>									
									
									<tr>
										<th class="title-description">
									    	<?php _e('Orders Date When Syncing Into QuickBooks','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php 
															$mw_wc_qbo_desk_order_sync_qbd_dt_fld = $MWQDC_LB->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld');
															$odf_opt = array(
																'post_date' => 'Order Date',
																'_paid_date' => 'Order Paid Date',
															);
														?>
														<select name="mw_wc_qbo_desk_order_sync_qbd_dt_fld" id="mw_wc_qbo_desk_order_sync_qbd_dt_fld" class="filled-in production-option mw_wc_qbo_desk_select">
															<?php echo  $MWQDC_LB->only_option($mw_wc_qbo_desk_order_sync_qbd_dt_fld,$odf_opt);?>
														</select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Orders date field value when syncing into QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>

	            				</tbody>
								</table>
							</div>
							</div>
							
							<div id="mw_qbo_sybc_settings_tab_customer_body" style="display: none;">
							<h6><?php echo __('Customer Settings','mw_wc_qbo_desk') ?></h6>
								<div class="myworks-wc-qbd-sync-table-responsive">
									<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body_body">
										<tbody>
											<tr>
												<th class="title-description">
													<?php echo __('QuickBooks Rep assigned to new customers','mw_wc_qbo_desk') ?>
													
												</th>
												<td>
													<div class="row">
														<div class="input-field col s12 m12 l12">
															<select name="mw_wc_qbo_desk_cus_push_rep_othername" id="mw_wc_qbo_desk_cus_push_rep_othername" class="mw_wc_qbo_desk_select">
															<option value=""></option>
															<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_cus_push_rep_othername'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep','qbd_id','sr_e_ref_name','','sr_e_ref_name ASC','',true);?>
															</select>
														</div>
													</div>
												</td>
												<td>
													<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
													  <span class="tooltiptext"><?php echo __('Choose your QBD Rep.','mw_wc_qbo_desk') ?></span>
													</div>
												</td>
											</tr>
											
											<tr>
												<th class="title-description">
													<?php echo __('Customer Type assigned to new customers','mw_wc_qbo_desk') ?>
													
												</th>
												<td>
													<div class="row">
														<div class="input-field col s12 m12 l12">
															<select name="mw_wc_qbo_desk_cus_push_customertype" id="mw_wc_qbo_desk_cus_push_customertype" class="mw_wc_qbo_desk_select">
															<option value=""></option>
															<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_cus_push_customertype'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_customertype','qbd_id','name','','name ASC','',true);?>
															</select>
														</div>
													</div>
												</td>
												<td>
													<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
													  <span class="tooltiptext"><?php echo __('Choose your QBD Customer Type.','mw_wc_qbo_desk') ?></span>
													</div>
												</td>
											</tr>
											
											<tr>
												<th class="title-description">
													<?php echo __('Sync new customers with appended user/order ID to name if </br> the same name exists in QuickBooks, but email does not match.','mw_wc_qbo_desk') ?>
													
												</th>
												<td>
													<div class="row">
														<div class="input-field col s12 m12 l12">
															<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cus_push_append_client_id" id="mw_wc_qbo_desk_cus_push_append_client_id" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_cus_push_append_client_id')=='true') echo 'checked' ?>>
														</div>
													</div>
												</td>
												<td>
													<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
													  <span class="tooltiptext"><?php echo __('Append the WooCommerce Client ID to the QuickBooks Desktop Display Name if the Client already exists in QuickBooks Desktop. Prevents errors from occuring when a duplicate client is being synced.','mw_wc_qbo_desk') ?></span>
													</div>
												</td>
											</tr>
											
											<tr>
												<th class="title-description">
													<?php echo __('Recognize inactive QuickBooks customers.','mw_wc_qbo_desk') ?>
													
												</th>
												<td>
													<div class="row">
														<div class="input-field col s12 m12 l12">
															<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cus_imp_inc_inactive_sts" id="mw_wc_qbo_desk_cus_imp_inc_inactive_sts" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_cus_imp_inc_inactive_sts')=='true') echo 'checked' ?>>
														</div>
													</div>
												</td>
												<td>
													<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
													  <span class="tooltiptext"><?php echo __('If enabled, we will scan and map to both active and inactive customers when Refreshing Data for customers.','mw_wc_qbo_desk') ?></span>
													</div>
												</td>
											</tr>
											
											<tr>
												<th class="title-description">
													<?php echo __('Skip Country Field For \'US\' Country','mw_wc_qbo_desk') ?>
													
												</th>
												<td>
													<div class="row">
														<div class="input-field col s12 m12 l12">
															<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cus_skip_usa_country" id="mw_wc_qbo_desk_cus_skip_usa_country" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_cus_skip_usa_country')=='true') echo 'checked' ?>>
														</div>
													</div>
												</td>
												<td>
													<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
													  <span class="tooltiptext"><?php echo __('If enabled, it wil not send the country value to billing/shipping address for customer,invoice,salesreceipt,salesorder etc.','mw_wc_qbo_desk') ?></span>
													</div>
												</td>
											</tr>
											
										</tbody>
									</table>
								</div>
							</div>

							<div id="mw_qbo_sybc_settings_tab_tax_body" style="display: none;">
							<h6><?php echo __('Tax Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body">
								<tbody>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Taxable Code','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<select name="mw_wc_qbo_desk_tax_rule_taxable" id="mw_wc_qbo_desk_tax_rule_taxable" class="mw_wc_qbo_desk_select">
										            <option value=""></option>
													<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_tax_rule_taxable'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode','qbd_id','name','','name ASC','',true);?>
										            </select>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose your QBD Tax rule for taxable items.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $MWQDC_LB->check_sh_stc_hash()):?>
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Tax Code for shipping','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<select name="mw_wc_qbo_desk_shipping_tax_rule_taxable" id="mw_wc_qbo_desk_shipping_tax_rule_taxable" class="mw_wc_qbo_desk_select">
										            <option value=""></option>
													<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode','qbd_id','name','','name ASC','',true);?>
										            </select>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose your QBD Shipping Tax rule for all shipping items.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									<?php endif;?>
									
	                				<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Non-Taxable Code','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<select name="mw_wc_qbo_desk_tax_rule" id="mw_wc_qbo_desk_tax_rule" class="mw_wc_qbo_desk_select">
										            <option value=""></option>
													<?php echo $MWQDC_LB->option_html($MWQDC_LB->get_option('mw_wc_qbo_desk_tax_rule'), $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode','qbd_id','name','','name ASC','',true);?>
										            </select>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose your QBD Tax rule with 0% tax for non-taxable items.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr style="display:none;">
										<th class="title-description">
									    	<?php echo __('QuickBooks Tax/Price Format','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_tax_format" id="mw_wc_qbo_desk_tax_format" class="filled-in production-option mw_wc_qbo_desk_select">
											            <option value=""></option>										            
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_tax_format'),$MWQDC_LB->tax_format)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose whether your tax setup is Inclusive - prices already include the tax, or Exclusive - taxes are additionally added on.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php
										$qb_tmap_items = array(									
										'Sales_Tax_Items' => 'Sales Tax Item',
										'Sales_Tax_Codes' => 'Sales Tax Code',
										);
									?>
									<tr>
										<th class="title-description">
									    	<?php echo __('Sales Tax Mapping Format','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_sl_tax_map_entity" id="mw_wc_qbo_desk_sl_tax_map_entity" class="filled-in production-option mw_wc_qbo_desk_select">										           
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_sl_tax_map_entity'),$qb_tmap_items)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select whether WooCommerce Tax Rates should be mapped to Sales Tax Items or Codes in QuickBooks. The default is Sales Tax Items, but for UK, AU or CA users, Tax Codes may need to be selected.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr style="display:none;">
										<th class="title-description">
									    	<?php echo __('QuickBooks Tax/Price Format','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_tax_format" id="mw_wc_qbo_desk_tax_format" class="filled-in production-option mw_wc_qbo_desk_select">
											            <option value=""></option>										            
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_tax_format'),$MWQDC_LB->tax_format)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose whether your tax setup is Inclusive - prices already include the tax, or Exclusive - taxes are additionally added on.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Sync WooCommerce Order Tax as a Line Item','mw_wc_qbo_desk') ?>
											</br><span style="font-size:10px;color:grey;">If enabled, this will override/invalidate any tax mappings set in MyWorks Sync > Map > Taxes, </br>and sync order tax as a line item instead of assigning it to a rate in QuickBooks.</span> 
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_odr_tax_as_li" id="mw_wc_qbo_desk_odr_tax_as_li" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_odr_tax_as_li')=='true') echo 'checked' ?>>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('If enabled, this will override/invalidate any tax mappings set in MyWorks Sync > Map > Taxes, and sync order tax as a line item instead of assigning it to a rate in QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr id="otli_qp_tr" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_odr_tax_as_li')!='true') echo 'style="display:none;"' ?>>
										<th class="title-description">
									    	<?php echo __('QuickBooks Product for Sales Tax line item','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<?php
															$dd_options = '<option value=""></option>';
															$dd_ext_class = '';
															if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
																$dd_ext_class = 'mwqs_dynamic_select_desk';
																if($MWQDC_LB->get_option('mw_wc_qbo_desk_otli_qbd_product')!=''){
																	$itemid = $MWQDC_LB->get_option('mw_wc_qbo_desk_otli_qbd_product');
																	$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
																	if($qb_item_name!=''){
																		$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
																	}
																}
															}else{
																$dd_options.=$qbo_product_options;
															}
														?>
														
														<select name="mw_wc_qbo_desk_otli_qbd_product" id="mw_wc_qbo_desk_otli_qbd_product" class="filled-in production-option mw_wc_qbo_desk_select <?php echo $dd_ext_class;?>">
															<?php echo $dd_options;?>
														</select>
														
													</p>
												</div>
											</div>
										</td>
	                                    <td>
	                                        <div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose a QuickBooks Product that will be the line item in the QuickBooks Invoice/Sales Receipt to represent the sales tax from the WooCommerce Order.') ?></span>
											</div>
	                                    </td>
									</tr>
									
	            				</tbody>
								</table>
							</div>
							</div>

							<div id="mw_qbo_sybc_settings_tab_mapping_body" style="display: none;">
							<h6><?php echo __('Mapping Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body_body">
								<tbody>                				
									<tr>
										<th class="title-description">
									    	<?php _e('Recognize additional roles as Customers','mw_wc_qbo_desk') ?>
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>	
														<?php
														$role_dd_options = '';
														$mw_wc_qbo_desk_wc_cust_role = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cust_role');
														$mw_wc_qbo_desk_wc_cust_role_exp = explode(',',$mw_wc_qbo_desk_wc_cust_role);
														foreach (get_editable_roles() as $role_name => $role_info):
															$selected = '';
															if( $role_name != 'customer' ){
																if($mw_wc_qbo_desk_wc_cust_role != ''){
																	if( in_array( $role_name, $mw_wc_qbo_desk_wc_cust_role_exp ) ){
																		$selected = 'selected="selected"';
																	}
																}
																$role_dd_options .= '<option value="'.$role_name.'" '.$selected.'>'.$role_name.'</option>';
															}
	    												endforeach;
	    												?>
														<select name="mw_wc_qbo_desk_wc_cust_role[]" id="mw_wc_qbo_desk_wc_cust_role" class="filled-in production-option mw_wc_qbo_desk_select mqs_multi" multiple="multiple">
															<?php echo $role_dd_options;?>
														</select>												
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Enable to map other custom customer roles with QuickBooks Desktop rather than only default "CUSTOMER". Please note that default customer will always be mapped.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Display Name format for new customers','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<textarea cols="50" name="mw_wc_qbo_desk_display_name_pattern" id="mw_wc_qbo_desk_display_name_pattern"><?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_display_name_pattern')!='') echo $MWQDC_LB->get_option('mw_wc_qbo_desk_display_name_pattern'); else echo '{firstname} {lastname}'; ?></textarea>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the WooCommerce client name values you would like to be assigned to the QBD "Display Name As" client field. This setting will determine the value in the QuickBooks Desktop Display Name for clients synced over. Choose either first/last name OR Company name - not both.<br><b>Available Tags: {firstname} , {lastname} , {companyname} ,{email} ,{display_name}</b>','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Use Display Name (if no email match found) to determine a matching customer','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_customer_match_by_name" id="mw_wc_qbo_desk_customer_match_by_name" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_customer_match_by_name')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Use the customer Display Name (if no email match found) when checking QuickBooks to find/match an unmapped customer - before syncing in a new customer record.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr id="tr_mw_wc_qbo_desk_customer_match_by_zipcode" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_customer_match_by_name')!='true') echo 'style="display:none;"' ?>>
										<th class="title-description">
									    	<?php echo __('Use Zip Code along with Display Name to validate a matching customer','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_customer_match_by_zipcode" id="mw_wc_qbo_desk_customer_match_by_zipcode" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_customer_match_by_zipcode')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Validate the Zip Code along with Display Name when checking QuickBooks to find/match an unmapped customer - before syncing in a new customer record.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Use Billing Company Name instead of Email to determine matching customers','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_customer_qbo_check_billing_company" id="mw_wc_qbo_desk_customer_qbo_check_billing_company" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_customer_qbo_check_billing_company')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Use the order billing company to match customers instead of by email address.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php 
										$wc_cus_vn_arr = array(
											'display_name' => 'display_name',
											//'first_name' => 'first_name',
											//'last_name' => 'last_name',
											'first_name_last_name' => 'first_name + last_name',
											'billing_company' => 'billing_company'
										);
									?>
									<tr>
										<th class="title-description">
									    	<?php echo __('WooCommerce Customer Name formatting in Mapping pages','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_wc_cus_view_name" id="mw_wc_qbo_desk_wc_cus_view_name" class="filled-in production-option mw_wc_qbo_desk_select">										            
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cus_view_name'),$wc_cus_vn_arr)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the format in which you would like the WooCommerce customer name to be displayed in on the Map > Customers page.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php 
										$qb_cus_vn_arr = array(
											'd_name' => 'display_name',
											//'first_name' => 'first_name',
											//'last_name' => 'last_name',
											'first_name_last_name' => 'first_name + last_name',
											'fullname' => 'fullname',
											'company' => 'company'
										);
									?>
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Customer Name formatting in Mapping pages','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_qb_cus_view_name" id="mw_wc_qbo_desk_qb_cus_view_name" class="filled-in production-option mw_wc_qbo_desk_select">										            
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_qb_cus_view_name'),$qb_cus_vn_arr)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the format in which you would like the QuickBooks customer name to be displayed in on the Map > Customers page.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Hide variable parent products from Map/Push > Products/Inventory','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_hide_vpp_fmp_pages" id="mw_wc_qbo_desk_hide_vpp_fmp_pages" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_hide_vpp_fmp_pages')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to hide variable parent products from Map/Push > Products/Inventory.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('Enable Custom Mapping in Map -> Shipping Method Page','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_ed_cust_ship_mpng_smmp" id="mw_wc_qbo_desk_ed_cust_ship_mpng_smmp" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Check to enable custom mapping in Map -> Shipping Method page.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									</tbody>
								</table>
								</div>
							</div>
							
							
							<div id="mw_qbo_sybc_settings_tab_pull_body" style="display: none;">
							<h6><?php echo __('Pull Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body_body">
								<tbody>								
									<tr>
										<th class="title-description">
									    	<?php echo __('Show Pull Page Tab','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_pull_enable" id="mw_wc_qbo_desk_pull_enable" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_pull_enable')=='true') echo 'checked' ?>>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('This will enable you to use the manual pull pages to manually pull data into WooCommerce from QuickBooks','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Product Price Field to pull from','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_pull_prd_price_field" id="mw_wc_qbo_desk_pull_prd_price_field" class="filled-in production-option mw_wc_qbo_desk_select">
											            <option value=""></option>										            
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_pull_prd_price_field'),$MWQDC_LB->get_qbd_product_price_field_list())?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the quickbook field for pull price','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									<?php 
										$qi_qpf_list = [
										'QuantityOnHand'=>'Quantity On Hand',
										'AvailableQuantity'=>'Available Quantity(QuantityOnHand - QuantityOnSalesOrder)',
										];
									?>
									<tr>
										<th class="title-description">
									    	<?php echo __('QuickBooks Inventory Quantity Field to pull from','mw_wc_qbo_desk') ?>
									    	
									    </th>
										<td>
											<div class="row">
												<div class="input-field col s12 m12 l12">
													<p>
														<select name="mw_wc_qbo_desk_pull_invnt_qty_field" id="mw_wc_qbo_desk_pull_invnt_qty_field" class="filled-in production-option mw_wc_qbo_desk_select">
														<?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_pull_invnt_qty_field'),$qi_qpf_list)?>
											            </select>
													</p>
												</div>
											</div>
										</td>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the quickbook field for pull inventory quantity','mw_wc_qbo_desk') ?></span>
											</div>
										</td>
									</tr>
									
									</tbody>
								</table>
							</div>
							</div>
							
							<div id="mw_qbo_sybc_settings_tab_wh_body" style="display: none;">
							<h6><?php echo __('Automatic Sync Settings','mw_wc_qbo_desk') ?></h6>
							<div class="myworks-wc-qbd-sync-table-responsive">
							<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body">
							<tbody>
								<tr class="wc_qb_tr">
									<td colspan="3" height="50">
										<b><?php echo __('WooCommerce -> QuickBooks Desktop','mw_wc_qbo_desk') ?></b>
										
									</td>									
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Enable Automatic Sync','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_rt_push_enable" id="mw_wc_qbo_desk_rt_push_enable" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_rt_push_enable')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('This is the master switch. Turn on to automatically add the below data types to the queue when they are added in QuickBooks.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Data Types','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<?php
													//
													$mwqbd_qbo_rt_push_items = array('customer' => 'Customer ', 'order' => 'Order', 'product' => 'Product', 'variation' => 'Variation', 'inventory' => 'Inventory', 'payment' => 'Payment') 
													?>
													<?php $rpi_val_arr = explode(',',$MWQDC_LB->get_option('mw_wc_qbo_desk_rt_push_items'));?>
													<?php foreach($mwqbd_qbo_rt_push_items as $rpi_key => $rpi_val):?>
													<?php
														$rpi_checked = '';
														if(is_array($rpi_val_arr) && in_array($rpi_key,$rpi_val_arr)){
															$rpi_checked = ' checked="checked"';
														}
													?>
													
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_rt_push_items[]" id="mw_wc_qbo_desk_rt_push_items" value="<?php echo $rpi_key;?>" <?php echo $rpi_checked;?>>
													&nbsp;<span class="rt_item_hd"><?php echo $rpi_val;?></span>
													<br /><br />
													<?php endforeach;?>									            
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose sync items (you can choose multiple items).','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr class="wc_qb_tr">
									<td colspan="3" height="50">
										<b><?php echo __('QuickBooks Desktop -> WooCommerce','mw_wc_qbo_desk') ?></b>
										
									</td>									
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Enable Automatic Sync','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_rt_pull_enable" id="mw_wc_qbo_desk_rt_pull_enable" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_rt_pull_enable')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('This is the master switch. Turn on to automatically sync the below data types into WooCommerce from QuickBooks when the Web Connector runs.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Data Types','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<?php													
													$mwqbd_qbo_rt_pull_items = array(
													'inventory' => 'Inventory',
													'pricing' => 'Pricing'
													);
													?>
													<?php $rpi_val_arr = explode(',',$MWQDC_LB->get_option('mw_wc_qbo_desk_rt_pull_items'));?>
													<?php foreach($mwqbd_qbo_rt_pull_items as $rpi_key => $rpi_val):?>
													<?php
														$rpi_checked = '';
														if(is_array($rpi_val_arr) && in_array($rpi_key,$rpi_val_arr)){
															$rpi_checked = ' checked="checked"';
														}
													?>
													
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_rt_pull_items[]" id="mw_wc_qbo_desk_rt_pull_items" value="<?php echo $rpi_key;?>" <?php echo $rpi_checked;?>>
													&nbsp;<span class="rt_item_hd"><?php echo $rpi_val;?></span>
													<br /><br />
													<?php endforeach;?>									            
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Select the data types to automatically sync.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<?php $d_iaq_pull = true;?>
								<tr id="qbd_s_tif_rt_inv_pull_tr" <?php if($d_iaq_pull || $MWQDC_LB->option_checked('mw_wc_qbo_desk_rt_all_invnt_pull')){echo 'style="display:none;"';}?>>
									<th class="title-description">
								    	<?php echo __('Interval for automatic sync','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<?php
												$rt_i_int_time_arr = array();
												$rt_i_int_time_arr['l_i_s_t'] = 'Last Auto Inventory Pull Success Time';
												$rt_i_int_time_arr['l_w_c_a_s_t'] = 'Last Web Connector Authentication Success Time';
												$rt_i_int_time_arr['c_i_t'] = 'Connection Interval Time - Web Connector Auto Request Time';
												$rt_i_int_time_arr['l_1_h'] = 'Last 1 Hour';
												$rt_i_int_time_arr['l_1_d'] = 'Last 1 Day';
												
												$rt_ii_it_val = $MWQDC_LB->get_option('mw_wc_qbo_desk_rt_inventory_import_interval_time');
												if(empty($rt_ii_it_val)){
													$rt_ii_it_val = 'l_w_c_a_s_t';
												}
											?>
											<div class="input-field col s12 m12 l12">
												<p>
													<select name="mw_wc_qbo_desk_rt_inventory_import_interval_time" id="mw_wc_qbo_desk_rt_inventory_import_interval_time" class="filled-in production-option mw_wc_qbo_desk_select">
													<?php $MWQDC_LB->only_option($rt_ii_it_val,$rt_i_int_time_arr)?>
										            </select>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Set the interval that you would like all inventory levels (for mapped products only) to be synced from QuickBooks to WooCommerce.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr style="display:none;">
									<th class="title-description">
								    	<?php echo __('Enable All Inventory Levels Pull','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_rt_all_invnt_pull" id="mw_wc_qbo_desk_rt_all_invnt_pull" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_rt_all_invnt_pull')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Check to enable all inventory pull - you also have to check inventory in above data type setting','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<?php //if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_rt_all_invnt_pull')){echo 'style="display:none;"';}?>
								<tr id="qbd_s_all_rt_p_invnt_ti_tr">
									<th class="title-description">
								    	<?php echo __('Time Interval For Realtime Inventory/Pricing Pull','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<?php
												$rt_i_int_time_arr = array();
												$rt_i_int_time_arr['i_e_r'] = 'Every Web Connector Update';
												$rt_i_int_time_arr['e_30_m'] = 'Every 30 Minutes';
												$rt_i_int_time_arr['e_1_h'] = 'Every 1 Hour';
												$rt_i_int_time_arr['e_2_h'] = 'Every 2 Hours';
												
												$rt_ii_it_val = $MWQDC_LB->get_option('mw_wc_qbo_desk_all_invnt_pull_interver_time');
												if(empty($rt_ii_it_val)){
													$rt_ii_it_val = 'i_e_r';
												}
											?>
											<div class="input-field col s12 m12 l12">
												<p>
													<select name="mw_wc_qbo_desk_all_invnt_pull_interver_time" id="mw_wc_qbo_desk_all_invnt_pull_interver_time" class="filled-in production-option mw_wc_qbo_desk_select">
													<?php $MWQDC_LB->only_option($rt_ii_it_val,$rt_i_int_time_arr)?>
										            </select>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Set the interval that you would like all inventory/pricing levels (for mapped products only) to be synced from QuickBooks to WooCommerce.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
            				</tbody>
							</table>
							</div>
							</div>

							<div id="mw_qbo_sybc_settings_tab_two_body" style="display: none;">
							<h6><?php echo __('Miscellaneous Settings','mw_wc_qbo_desk') ?></h6>							
							<div class="myworks-wc-qbd-sync-table-responsive">
							<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body">
							<tbody>
								<tr height="50">
									<td colspan="3">
										<b><?php echo __('Plugin Dropdown Settings','mw_wc_qbo_desk') ?></b>										
									</td>									
								</tr>

								<tr>
									<th class="title-description">
								    	<?php echo __('Enable Select2 searchable dropdown style','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_select2_status" id="mw_wc_qbo_desk_select2_status" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_select2_status')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('This setting is on by default - to enable the Select2 dropdown style. Turn this off to display a normal dropdown for the plugin.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<th class="title-description">
								    	<?php echo __('Restrict Select2 dropdowns only to AJAX search (customer and product)','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_select2_ajax" id="mw_wc_qbo_desk_select2_ajax" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_select2_ajax')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Enable AJAX search for our Select2 dropdown styles. This option is applicable if Select2 is enabled on above setting. This is efficient if your install has huge customer and product data lists.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr height="50">
									<td colspan="3">
										<b><?php echo __('Plugin Debug Settings','mw_wc_qbo_desk') ?></b>										
									</td>									
								</tr>

								<tr>
									<th class="title-description">
								    	<?php echo __('Add QuickBooks XML Request Into Log File ','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_add_xml_req_into_log" id="mw_wc_qbo_desk_add_xml_req_into_log" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_add_xml_req_into_log')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Only for debug, Add QuickBooks XML request into log file (last 24 hours).','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Enable Database Status Check Sidebar Menu','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_enable_db_status_chk_sb" id="mw_wc_qbo_desk_enable_db_status_chk_sb" value="true" <?php if($MWQDC_LB->get_option('mw_wc_qbo_desk_enable_db_status_chk_sb')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Only for debug, Add database check option into plugin sidebar menu.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr height="50">
									<td colspan="3">
										<b><?php echo __('Other Settings','mw_wc_qbo_desk') ?></b>										
									</td>									
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('QuickBooks XML Request Encoding','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<select name="mw_wc_qbo_desk_xml_req_encoding" id="mw_wc_qbo_desk_xml_req_encoding" class="mw_wc_qbo_desk_select">
												<?php 
													$xml_req_encodings = array('utf-8'=>'utf-8','ISO-8859-1'=>'ISO-8859-1');
												?>
									            <?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_xml_req_encoding'),$xml_req_encodings)?>
									            </select>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose QuickBooks XML request encoding','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('QuickBooks XML Request Locale','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<select name="mw_wc_qbo_desk_xml_req_locale" id="mw_wc_qbo_desk_xml_req_locale" class="mw_wc_qbo_desk_select">
												<?php 
													$xml_req_locales = $MWQDC_LB->get_wc_country_list();
													$sl_locale = ($MWQDC_LB->get_option('mw_wc_qbo_desk_xml_req_locale')!='')?$MWQDC_LB->get_option('mw_wc_qbo_desk_xml_req_locale'):'US';
												?>
									            <?php $MWQDC_LB->only_option($sl_locale,$xml_req_locales)?>
									            </select>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose QuickBooks XML request locale setting','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Save Logs for Days','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<select name="mw_wc_qbo_desk_save_log_for" id="mw_wc_qbo_desk_save_log_for" class=" mw_wc_qbo_desk_select">
									            <?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_save_log_for'),$MWQDC_LB->log_save_days)?>
									            </select>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose how many days log entry you want to save','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Enable Advanced Logging entries for Days','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<select name="mw_wc_qbo_desk_save_pqe_for" id="mw_wc_qbo_desk_save_pqe_for" class=" mw_wc_qbo_desk_select">
									            <?php $MWQDC_LB->only_option($MWQDC_LB->get_option('mw_wc_qbo_desk_save_pqe_for'),$MWQDC_LB->qb_log_queue_save_days)?>
									            </select>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose how many days previous queue and quickbooks_log entry you want to save','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								</tbody>
							</table>
							</div>
							<!--<>-->
							<br/>
							<!--<div class="mw_wc_qbo_desk_clear"></div>-->
							<div class="ms_vnu_cont row" style="display:none;">
								<button title="Re-Generate all incorrect variation names from parent product name and attribute values." class="waves-effect waves-light btn mw-qbo-sync-green" id="wp_avnu_btn" disabled>
									Adjust All Incorrect Variation Names
								</button>
								&nbsp;
								<span style="padding: 0px 20px;" id="wp_avnu_msg"></span>
								<?php //wp_nonce_field( 'myworks_wc_qbo_sync_rg_all_inc_variation_names_desk', 'rg_all_inc_variation_names_desk' ); ?>
							</div>
							
							</div>
							
							
							<div class="mw_wc_qbo_desk_clear"></div>
							<div class="row">
								<div class="input-field col s12 m6 l4">
									<input type="submit" name="mw_wc_qbd_desk_settings" id="mw_wc_qbd_desk_settings" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save All">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</form>
</div>
<?php echo $MWQDC_LB->get_admin_get_extra_css_js();?>
<?php echo $MWQDC_LB->get_checkbox_switch_css_js();?>
<script type="text/javascript">
jQuery(document).ready(function($){
	document.getElementById(document.getElementById("mw_qbd_sync_settings_current_tab").value + '_body').style.display = "block";
	jQuery('#'+jQuery('#mw_qbd_sync_settings_current_tab').val()).trigger('click');

	jQuery('.mwqs_stb a').on('click',function(e){
		var click_id = jQuery(this).attr('id');
		jQuery('#mw_qbd_sync_settings_current_tab').val(click_id);
		jQuery('.mwqs_stb a').each(function(){
			var tab_id = jQuery(this).attr('id');
			if(tab_id!=click_id){
				jQuery('#'+tab_id).parent().removeClass('active');					
				jQuery('#'+tab_id+'_body').hide();					
			}
		});
		jQuery('#'+click_id).parent().addClass('active');
		jQuery('#'+click_id+'_body').show();
	});
	
	//
	$("input:radio[name=mw_wc_qbo_desk_order_qbd_sync_as]").click(function(){
		if($(this).attr('id') == 'wo_qsa_vpr'){
			$('#wo_qsa_vpr_map_tr').fadeIn("slow");
		}else{
			$('#wo_qsa_vpr_map_tr').fadeOut("slow");
		}
		
		if($(this).attr('id') == 'wo_qsa_pg'){
			$('#mwoqsa_rm').html('<small>Please select the order sync type per gateway in Map > Payment Method page.</small>');
			$('#mwoqsa_rm').fadeIn("slow");
		}else{
			$('#mwoqsa_rm').fadeOut("slow");
		}
	});
})
</script>
<script type="text/javascript">
jQuery(document).ready(function($){
	jQuery('input.mwqs_st_chk').attr('data-size','small');
	<?php echo $list_selected;?>
	jQuery('input.mwqs_st_chk').bootstrapSwitch();
    jQuery('#mw_wc_qbo_desk_all_order_to_customer').on('switchChange.bootstrapSwitch', function (event, state) {
		if(jQuery("#mw_wc_qbo_desk_all_order_to_customer").is(':checked')) {
			/*
          	jQuery('#mw_wc_qbo_desk_customer_to_sync_all_orders_tr').fadeIn("slow");
			jQuery('#mw_wc_qbo_desk_wc_cust_role_sync_as_cus_tr').fadeIn("slow");
			*/
			
			//
			jQuery('#saoqc_tr').fadeIn("slow");
        } else {
			/*
          	jQuery('#mw_wc_qbo_desk_customer_to_sync_all_orders_tr').fadeOut("slow");
			jQuery('#mw_wc_qbo_desk_wc_cust_role_sync_as_cus_tr').fadeOut("slow");
			*/
			
			//
			jQuery('#saoqc_tr').fadeOut("slow");
        }
    });
	
	/*
	jQuery('#mw_wc_qbo_desk_rt_all_invnt_pull').on('switchChange.bootstrapSwitch', function (event, state) {
		if(jQuery(this).is(':checked')) {
			jQuery('#qbd_s_all_rt_p_invnt_ti_tr').fadeIn("slow");
			jQuery('#qbd_s_tif_rt_inv_pull_tr').fadeOut("slow");
		}else{
			jQuery('#qbd_s_tif_rt_inv_pull_tr').fadeIn("slow");
			jQuery('#qbd_s_all_rt_p_invnt_ti_tr').fadeOut("slow");
		}
	});
	*/
	
	jQuery('#mw_wc_qbo_desk_odr_tax_as_li').on('switchChange.bootstrapSwitch', function (event, state) {		
		if(jQuery("#mw_wc_qbo_desk_odr_tax_as_li").is(':checked')) {			
			jQuery('#otli_qp_tr').fadeIn("slow");			
        } else {			
			jQuery('#otli_qp_tr').fadeOut("slow");
        }
	});
	
	
	jQuery('#mw_wc_qbo_desk_customer_match_by_name').on('switchChange.bootstrapSwitch', function (event, state) {		
		if(jQuery("#mw_wc_qbo_desk_customer_match_by_name").is(':checked')) {			
			jQuery('#tr_mw_wc_qbo_desk_customer_match_by_zipcode').fadeIn("slow");
        } else {			
			jQuery('#tr_mw_wc_qbo_desk_customer_match_by_zipcode').fadeOut("slow");
        }
	});
	
	//
	$('#wp_avnu_btn').removeAttr('disabled');
	$('#wp_avnu_btn').click(function(e){
		e.preventDefault();
		if(confirm('<?php echo __('Are you sure, you re-generate all incorrect variation names?','mw_wc_qbo_desk')?>')){
			$('#wp_avnu_msg').html('Loading...');
			var data = {
				"action": 'mw_wc_qbo_sync_rg_all_inc_variation_names_desk',
				"rg_all_inc_variation_names_desk": jQuery('#rg_all_inc_variation_names_desk').val(),
			};
			
			jQuery.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: data,
			   cache:  false ,
			   //datatype: "json",
			   success: function(result){
				   if(result!=0 && result!=''){					 
					 jQuery('#wp_avnu_msg').html(result);
				   }else{					
					jQuery('#wp_avnu_msg').html('Error!');
				   }				  
			   },
			   error: function(result) {					
					jQuery('#wp_avnu_msg').html('Error!');
			   }
			});
		}
	});
	
})
</script>

<?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_desk_select','qbo_product');?>
<?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_desk_select_cus','qbo_customer',true);?>


<?php
if($save_status = $MWQDC_LB->get_session_val('settings_update_message','',true)){	
	$save_status = ($save_status!='')?$save_status:'error';
	$MWQDC_LB->set_admin_sweet_alert($save_status);
}
?>
