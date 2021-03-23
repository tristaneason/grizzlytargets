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
$tab = isset($_GET['tab']) ? $_GET['tab'] : '' ;

if($MWQDC_LB->is_license_active()){

if($tab=='customer'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-customer.php';
}elseif($tab=='product'){
	if(isset($_GET['variation'])){
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-variation.php';
	}else{
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-product.php';
	}	
}elseif($tab=='paymentmethod'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-paymentmethod.php';
}elseif($tab=='tax-class'){
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-tax-class.php';
	}	
}elseif($tab=='custom-fields'){
	if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $MWQDC_LB->check_sh_cfm_hash()){
		require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-custom-fields.php';
	}
}elseif($tab=='coupon-code'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-coupon-code.php';
}elseif($tab=='shipping-method'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-shipping-method.php';
}else{
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-index.php';
}

}
?>

<?php
if($save_status = $MWQDC_LB->get_session_val('map_page_update_message','',true)){
	$save_status = ($save_status!='')?$save_status:'error';
	$MWQDC_LB->set_admin_sweet_alert($save_status);
}
?>