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

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $MWQDC_LB;
if($MWQDC_LB->is_license_active()){

require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-nav.php';

$tab = isset($_GET['tab']) ? $_GET['tab'] : '' ;

if($tab=='customer'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-customer.php';
}elseif($tab=='order'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-order.php';
}elseif($tab=='refund'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-refund.php';
}elseif($tab=='payment'){
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){ //&& !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_order')
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-payment.php';
	}	
}elseif($tab=='product'){
	if(isset($_GET['variation'])){
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-variation.php';
	}else{
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-product.php';
	}	
}elseif($tab=='inventory'){
	if(isset($_GET['variation'])){
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-variation-inventory.php';
	}else{
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-inventory.php';
	}
	
}else{
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-push-index.php';
}

}
?>