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

require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-pull-nav.php';

$tab = isset($_GET['tab']) ? $_GET['tab'] : '' ;

if($tab=='inventory'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-pull-inventory.php';
}elseif($tab=='productprice'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-pull-productprice.php';
}else{
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-pull-index.php';
}

}
?>