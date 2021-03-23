<?php
ob_start();
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://myworks.software/integrations/sync-woocommerce-quickbooks-desktop
 * @since             1.0.0
 * @package           MyWorks_WC_QBO_desk
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Sync for QuickBooks Desktop - by MyWorks Software
 * Plugin URI:        https://myworks.software/integrations/sync-woocommerce-quickbooks-desktop
 * Description:       Automatically sync your WooCommerce store to QuickBooks Desktop! Easily sync your orders, customers, inventory and more from your WooCommerce store to QuickBooks Desktop Pro, Premier or Enterprise. Your complete solution to streamline your accounting workflow - with no limits.
 * Version:           1.4.12
 * Author:            MyWorks Software
 * Author URI:        https://myworks.software/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mw_wc_qbo_desk
 * Domain Path:       /languages
 * WC requires at least: 2.0.0
 * WC tested up to: 3.6.3
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mw-qbo-desktop-activator.php
 */
function activate_mw_qbo_desktop() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mw-qbo-desktop-activator.php';
	MW_QBO_Desktop_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mw-qbo-desktop-deactivator.php
 */
function deactivate_mw_qbo_desktop() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mw-qbo-desktop-deactivator.php';
	MW_QBO_Desktop_Deactivator::deactivate();
}

function mw_qbo_desktop_save_error(){
	file_put_contents(ABSPATH. 'wp-content/plugins/myworks-quickbooks-desktop-sync/error_activation.html', ob_get_contents());
}

register_activation_hook( __FILE__, 'activate_mw_qbo_desktop' );
register_deactivation_hook( __FILE__, 'deactivate_mw_qbo_desktop' );
add_action('activated_plugin','mw_qbo_desktop_save_error');

/**
* Admin action links
*/

function add_mw_qbo_desktop_action_links($links) {
		$adminlinks = array(
			'<a href="' . admin_url( 'admin.php?page=mw-qbo-desktop-settings' ) . '">Settings</a>',
			'<a target="_blank" href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop">Docs</a>',
			'<a target="_blank" href="https://myworks.software/account/submitticket.php?step=2&deptid=2">Support</a>',
			'<a target="_blank" href="https://myworks.software/changelogs/myworks-quickbooks-desktop-sync/changelog.txt">Changelog</a>'
		 );
		$adminlinks[] = '';
		return array_merge( $links, $adminlinks );
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_mw_qbo_desktop_action_links' );




/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mw-qbo-desktop.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mw_qbo_desktop() {

	$plugin = new MW_QBO_Desktop();
	$plugin->run();

}
run_mw_qbo_desktop();
