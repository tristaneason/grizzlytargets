<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com/
 * @since             1.0.0
 * @package           makewebbetter-hubspot-for-woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:         HubSpot for WooCommerce
 * Plugin URI:          https://wordpress.org/plugins/makewebbetter-hubspot-for-woocommerce
 * Description:         Integrate WooCommerce with HubSpot’s free CRM, abandoned cart tracking, email marketing, marketing automation, analytics & more.
 * Version:             1.0.6
 * Requires at least:   4.4.0
 * Tested up to:        5.4.2
 * WC requires at least:    3.0.0
 * WC tested up to:         4.3.1
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       makewebbetter-hubspot-for-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$hubwoo_pro_activated = false;
$hubwoo_pro_flag      = 0;
$activated_plugins    = get_option( 'active_plugins', array() );
$plugin_dependencies  = array( 'hubspot-woocommerce-integration/hubspot-woocommerce-integration.php', 'hubspot-woocommerce-integration-starter/hubspot-woocommerce-integration-starter.php', 'hubspot-woocommerce-integration-complimentary/hubspot-woocommerce-integration-complimetary.php', 'hubspot-woocommerce-integration-pro/hubspot-woocommerce-integration-pro.php' );

/**
 * Checking if WooCommerce is active
 * and other woocommerce integration versions.
 */

if ( function_exists( 'is_multisite' ) && is_multisite() ) {

	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

		$hubwoo_pro_activated = true;
		$hubwoo_pro_flag      = 1;
	}
}

if ( in_array( 'woocommerce/woocommerce.php', $activated_plugins, true ) ) {
	$hubwoo_pro_activated = true;
	$hubwoo_pro_flag      = 1;
}

if ( $hubwoo_pro_activated && $hubwoo_pro_flag ) {
	foreach ( $plugin_dependencies as $dependency ) {
		if ( in_array( $dependency, $activated_plugins, true ) ) {
			$hubwoo_pro_activated = false;
			$hubwoo_pro_flag      = -1;
			break;
		}
	}
}

if ( $hubwoo_pro_activated && $hubwoo_pro_flag ) {
	if ( ! function_exists( 'activate_hubwoo_pro' ) ) {

		/**
		 * The code that runs during plugin activation.
		 */
		function activate_hubwoo_pro() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo-activator.php';
			Hubwoo_Activator::activate();
		}
	}

	if ( ! function_exists( 'deactivate_hubwoo_pro' ) ) {

			/**
			 * The code that runs during plugin deactivation.
			 */
		function deactivate_hubwoo_pro() {
			require_once plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo-deactivator.php';
			Hubwoo_Deactivator::deactivate();
		}
	}

	register_activation_hook( __FILE__, 'activate_hubwoo_pro' );
	register_deactivation_hook( __FILE__, 'deactivate_hubwoo_pro' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-hubwoo.php';

	/**
	 * Define HubWoo constants.
	 *
	 * @since 1.0.0
	 */
	function hubwoo_pro_define_constants() {
		hubwoo_pro_define( 'HUBWOO_ABSPATH', dirname( __FILE__ ) . '/' );
		hubwoo_pro_define( 'HUBWOO_URL', plugin_dir_url( __FILE__ ) );
		hubwoo_pro_define( 'HUBWOO_VERSION', '1.0.5' );
		hubwoo_pro_define( 'HUBWOO_PLUGINS_PATH', plugin_dir_path( __DIR__ ) );
		hubwoo_pro_define( 'HUBWOO_CLIENT_ID', '769fa3e6-79b1-412d-b69c-6b8242b2c62a' );
		hubwoo_pro_define( 'HUBWOO_SECRET_ID', '2893dd41-017e-4208-962b-12f7495d16b0' );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string $name name for the constant.
	 * @param string $value value for the constant.
	 * @since 1.0.0
	 */
	function hubwoo_pro_define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Setting Page Link.
	 *
	 * @param array  $actions actions for the plugin.
	 * @param string $plugin_file name of the plugin.
	 * @return array
	 * @since 1.0.0
	 */
	function hubwoo_pro_admin_settings( $actions, $plugin_file ) {
		static $plugin;

		if ( ! isset( $plugin ) ) {
			$plugin = plugin_basename( __FILE__ );
		}

		if ( $plugin === $plugin_file ) {
			$settings = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=hubwoo' ) . '">' . esc_html__( 'Settings', 'makewebbetter-hubspot-for-woocommerce' ) . '</a>',
			);

			$actions = array_merge( $settings, $actions );
		}

		return $actions;
	}

	// add link for settings.
	add_filter( 'plugin_action_links', 'hubwoo_pro_admin_settings', 10, 2 );

	/**
	 * Auto Redirection to settings page after plugin activation
	 *
	 * @since    1.0.0
	 * @param string $plugin name of the plugin.
	 * @link  https://makewebbetter.com/
	 */
	function hubwoo_pro_activation_redirect( $plugin ) {
		if ( WC()->is_rest_api_request() ) {
			return;
		}

		if ( plugin_basename( __FILE__ ) === $plugin ) {
			wp_safe_redirect( admin_url( 'admin.php?page=hubwoo&hubwoo_tab=hubwoo-overview&hubwoo_key=connection-setup' ) );
			exit();
		}
	}
	// redirect to settings page as soon as plugin is activated.
	add_action( 'activated_plugin', 'hubwoo_pro_activation_redirect' );

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_hubwoo_pro() {
		// define contants if not defined..
		hubwoo_pro_define_constants();

		$hub_woo = new Hubwoo();
		$hub_woo->run();

		$GLOBALS['hubwoo'] = $hub_woo;
	}
	run_hubwoo_pro();
} elseif ( ! $hubwoo_pro_activated && 0 === $hubwoo_pro_flag ) {
	add_action( 'admin_init', 'hubwoo_pro_plugin_deactivate' );

	/**
	 * Call Admin notices
	 *
	 * @link https://www.makewebbetter.com/
	 */
	function hubwoo_pro_plugin_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'hubwoo_pro_plugin_error_notice' );
	}

	/**
	 * Show warning message if woocommerce is not install
	 *
	 * @since 1.0.0
	 * @link https://www.makewebbetter.com/
	 */
	function hubwoo_pro_plugin_error_notice() {         ?>
		<div class="error notice is-dismissible">
		<p><?php esc_html_e( 'WooCommerce is not activated. Please activate WooCommerce first to install HubSpot for WooCommerce', 'makewebbetter-hubspot-for-woocommerce' ); ?></p>
		</div>
		<style>
		#message{display:none;}
		</style>
		<?php
	}
} elseif ( ! $hubwoo_pro_activated && -1 === $hubwoo_pro_flag ) {

	/**
	 * Show warning message if any other HubSpot WooCommerce Integration version is activated
	 *
	 * @since 1.0.0
	 * @link https://www.makewebbetter.com/
	 */
	function hubwoo_pro_plugin_basic_error_notice() {
		?>
		<div class="error notice is-dismissible">
		<p><?php esc_html_e( 'Oops! You tried activating the HubSpot for WooCommerce without deactivating the another version of the integration created by MakewebBetter. Kindly deactivate the other version of HubSpot WooCommerce Integration and then try again.', 'makewebbetter-hubspot-for-woocommerce' ); ?></p>
		</div>
		<style>
		#message{display:none;}
		</style>
		<?php
	}

	add_action( 'admin_init', 'hubwoo_pro_plugin_deactivate_dueto_basicversion' );


	/**
	 * Call Admin notices
	 *
	 * @link https://www.makewebbetter.com/
	 */
	function hubwoo_pro_plugin_deactivate_dueto_basicversion() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'hubwoo_pro_plugin_basic_error_notice' );
	}
}

register_uninstall_hook( __FILE__, 'uninstall_hubwoo_pro' );

if ( ! function_exists( 'uninstall_hubwoo_pro' ) ) {

	/**
	 * The code that runs during uninstalling the plugin.
	 */
	function uninstall_hubwoo_pro() {
		if ( file_exists( WC_LOG_DIR . 'hubspot-for-woocommerce-logs.log' ) ) {
			unlink( WC_LOG_DIR . 'hubspot-for-woocommerce-logs.log' );
		}
	}
}
