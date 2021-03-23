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

require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-export-nav.php';

$tab = isset($_GET['tab']) ? $_GET['tab'] : '' ;

if($tab=='customer'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-export-customer.php';
}elseif($tab=='order'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-export-order.php';
}elseif($tab=='payment'){
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-export-payment.php';
}else{
	require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-export-customer.php';
}
?>