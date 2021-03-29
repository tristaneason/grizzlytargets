<?php
/*
Plugin Name: WooCommerce Product Add-Ons Ultimate
Description: Add extra fields to WooCommerce products
Version: 3.8.9
Author: Plugin Republic
Author URI: https://pluginrepublic.com/
Plugin URI: https://pluginrepublic.com/wordpress-plugins/woocommerce-product-add-ons-ultimate/
Text Domain: pewc
WC requires at least: 3.2
WC tested up to: 5.2
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants
 **/
if ( ! defined( 'PEWC_FILE' ) ) {
	define( 'PEWC_FILE', __FILE__ );
}
if ( ! defined( 'PEWC_PLUGIN_VERSION' ) ) {
	define( 'PEWC_PLUGIN_VERSION', '3.8.9' );
}
if ( ! defined( 'PEWC_DIRNAME' ) ) {
	define( 'PEWC_DIRNAME', dirname( __FILE__ ) );
}
if ( ! defined( 'PEWC_PLUGIN_URL' ) ) {
	define( 'PEWC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'PEWC_PLUGIN_DIR_PATH' ) ) {
	define( 'PEWC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

function pewc_load_plugin_textdomain() {
  load_plugin_textdomain( 'pewc', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'pewc_load_plugin_textdomain' );

/**
 * Runs on activation
 * @since 3.0.0
 */
function pewc_register_activation() {
	// Save the current version so we know which version was installed originally
	$original_version = get_option( 'pewc_original_version', false );
	if( ! $original_version ) {
		// Don't overwrite the version, in case we're reactivating
		update_option( 'pewc_original_version', PEWC_PLUGIN_VERSION );
	}
}
register_activation_hook( __FILE__, 'pewc_register_activation' );

function pewc_woocommerce_version_notice() { ?>
	<div class="notice notice-error">
		<p><?php _e( 'WooCommerce Product Add-Ons Ultimate requires WooCommerce version 3.0.0 minimum. Please upgrade your version of WooCommerce.', 'pewc' ); ?></p>
	</div>
<?php }

function pewc_woocommerce_required_notice() { ?>
	<div class="notice notice-error">
		<p><?php _e( 'WooCommerce Product Add-Ons Ultimate requires WooCommerce to be installed. Please install and activate WooCommerce before activating Add-Ons Ultimate.', 'pewc' ); ?></p>
	</div>
<?php }

// AJAX call to permanently dismiss a notice
function pewc_dismiss_notification() {
	$option = $_POST['option'];
	update_option( 'pewc_' . $option, 'dismissed' );
}
add_action( 'wp_ajax_pewc_dismiss_notification', 'pewc_dismiss_notification' );

/**
 * Check WooCommerce is active
 * @since 3.6.0
 */
function pewc_plugins_loaded() {

	if( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'pewc_woocommerce_required_notice' );
		// add_action( 'admin_init', 'pewc_deactivate' );
	}

}
add_action( 'plugins_loaded', 'pewc_plugins_loaded' );

function pewc_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}


if( is_admin() ) {
	require_once dirname( __FILE__ ) . '/classes/class-pewc-settings-tab.php';
}
require_once dirname( __FILE__ ) . '/admin/customizer.php';
require_once dirname( __FILE__ ) . '/admin/functions-addons-ajax.php';
require_once dirname( __FILE__ ) . '/admin/functions-admin-export.php';
require_once dirname( __FILE__ ) . '/admin/functions-admin-import.php';
require_once dirname( __FILE__ ) . '/admin/functions-admin-order.php';
require_once dirname( __FILE__ ) . '/admin/functions-admin-post-types.php';
require_once dirname( __FILE__ ) . '/admin/functions-admin-settings.php';
require_once dirname( __FILE__ ) . '/admin/functions-custom-panel.php';
require_once dirname( __FILE__ ) . '/admin/functions-duplication.php';
require_once dirname( __FILE__ ) . '/admin/functions-fields.php';
require_once dirname( __FILE__ ) . '/admin/functions-multilingual.php';
require_once dirname( __FILE__ ) . '/admin/functions-sanitisation.php';

require_once dirname( __FILE__ ) . '/classes/class-pewc-product-extra-post-type.php';
require_once dirname( __FILE__ ) . '/inc/functions-archive.php';
require_once dirname( __FILE__ ) . '/inc/functions-calculations.php';
require_once dirname( __FILE__ ) . '/inc/functions-cart.php';
require_once dirname( __FILE__ ) . '/inc/functions-conditionals.php';
require_once dirname( __FILE__ ) . '/inc/functions-global-extras.php';
require_once dirname( __FILE__ ) . '/inc/functions-helpers.php';
require_once dirname( __FILE__ ) . '/inc/functions-integrations.php';
require_once dirname( __FILE__ ) . '/inc/functions-lightbox.php';
require_once dirname( __FILE__ ) . '/inc/functions-log.php';
require_once dirname( __FILE__ ) . '/inc/functions-media.php';
require_once dirname( __FILE__ ) . '/inc/functions-migration.php';
require_once dirname( __FILE__ ) . '/inc/functions-order.php';
require_once dirname( __FILE__ ) . '/inc/functions-quickview.php';
require_once dirname( __FILE__ ) . '/inc/functions-roles.php';
require_once dirname( __FILE__ ) . '/inc/functions-single-product.php';
require_once dirname( __FILE__ ) . '/inc/functions-summary-panel.php';
require_once dirname( __FILE__ ) . '/inc/functions-templates.php';
require_once dirname( __FILE__ ) . '/inc/functions-tooltips.php';
require_once dirname( __FILE__ ) . '/inc/functions-variations.php';
require_once dirname( __FILE__ ) . '/inc/functions-uploads.php';
require_once dirname( __FILE__ ) . '/inc/functions-updater.php';
require_once dirname( __FILE__ ) . '/inc/functions-pro-helpers.php';

if( pewc_is_pro() ) {
	require_once dirname( __FILE__ ) . '/inc/functions-percentages.php';
	require_once dirname( __FILE__ ) . '/inc/functions-products.php';
}

/**
 * Load our files
 * @since 3.6.0
 */
function pewc_init() {





}
