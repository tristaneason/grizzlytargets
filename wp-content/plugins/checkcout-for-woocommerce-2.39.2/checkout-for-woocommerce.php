<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              checkoutwc.com
 * @since             1.0.0
 * @package           Objectiv\Plugins\Checkout
 *
 * @wordpress-plugin
 * Plugin Name:       Checkout for WooCommerce
 * Plugin URI:        https://www.CheckoutWC.com
 * Description:       Beautiful, conversion optimized checkout templates for WooCommerce.
 * Version:           2.39.2
 * Author:            Objectiv
 * Author URI:        https://objectiv.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       checkout-wc
 * Domain Path:       /languages
 * Tested up to: 5.2.4
 * WC tested up to: 3.8.0
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CFW_NAME', 'Checkout for WooCommerce' );
define( 'CFW_UPDATE_URL', 'https://www.checkoutwc.com' );
define( 'CFW_VERSION', '2.39.2' );
define( 'CFW_PATH', dirname( __FILE__ ) );
define( 'CFW_URL', plugins_url( '/', __FILE__ ) );
define( 'CFW_MAIN_FILE', __FILE__ );

/*
 * Protect our gentle, out of date users from our fancy modern code
 */
if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
	return;
}

/**
 * Auto-loader (composer)
 */
require_once CFW_PATH . '/vendor/autoload.php';

/**
 * Our language function wrappers that we only use for
 * external translation domains
 */
require_once CFW_PATH . '/sources/php/language-wrapper-functions.php';

use Objectiv\Plugins\Checkout\Main;
use Objectiv\Plugins\Checkout\Core\Admin;

if ( class_exists( '\Kint' ) && property_exists( '\Kint', 'enabled_mode' ) ) {
	/**
	 * Kint disabled by default. Enable by enabling developer mode (see docs)
	 */
	\Kint::$enabled_mode = false;
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function cfw_plugin_init() {

	global $cfw;

	/**
	 * If bypass-cfw flag is present
	 * don't even bootstrap the plugin
	 */
	if ( empty( $_GET['bypass-cfw'] ) ) {
		$cfw = Main::instance();
		$cfw->run( CFW_MAIN_FILE );
	}
}
cfw_plugin_init();

// Use the global instance
global $cfw;

/**
 * Activation hook
 */
register_activation_hook( __FILE__, array( $cfw, 'activation' ) );

/**
 * Deactivation hook
 */
register_deactivation_hook( __FILE__, array( $cfw, 'deactivation' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ! wp_doing_ajax() ) {
	global $cfw_admin, $cfw;

	$cfw_admin = new Admin( $cfw );
	$cfw_admin->run();
}
