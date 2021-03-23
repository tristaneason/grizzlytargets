<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/public/partials
 */
 
$is_valid_user = false;
if(is_user_logged_in() && (current_user_can('editor') || current_user_can('administrator'))){	
	$is_valid_user = true;
}

if($is_valid_user){	
	global $MWQDC_LB;
	global $wpdb;
	if($MWQDC_LB->is_qwc_connected()){		
		$item = (isset($_GET['item']))?$_GET['item']:'';
		
		$search = (isset($_GET['q']))?$_GET['q']:'';		
		$search = $MWQDC_LB->sanitize($search);
		
		$limit = ' LIMIT 0,50';
		
		if($item=='qbo_product'){
			
			$query = "SELECT `qbd_id` as `id`, `name` as `text` FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_items` WHERE `name` LIKE '%%%s%%'  OR `sku` LIKE '%%%s%%' ORDER BY `name` ASC {$limit} ";
			
			$query = $wpdb->prepare($query,$search,$search);
			header('Content-Type: application/json');
			echo json_encode($MWQDC_LB->get_data($query));
		}
		
		if($item=='qbo_customer'){			
			$query = "SELECT `qbd_customerid` as `id`, `d_name` as `text` FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_customers` WHERE `d_name` LIKE '%%%s%%'  OR `email` LIKE '%%%s%%'  OR `first_name` LIKE '%%%s%%'  OR `last_name` LIKE '%%%s%%'  OR `company` LIKE '%%%s%%' OR `fullname` LIKE '%%%s%%' ORDER BY `d_name` ASC {$limit} ";
			
			$query = $wpdb->prepare($query,$search,$search,$search,$search,$search,$search);
			header('Content-Type: application/json');
			echo json_encode($MWQDC_LB->get_data($query));			
		}
		
	}
	die();
}