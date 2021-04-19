<?php
/*
Plugin Name: WPC Product Bundles for WooCommerce (Premium)
Plugin URI: https://wpclever.net/
Description: WPC Product Bundles is a plugin help you bundle a few products, offer them at a discount and watch the sales go up!
Version: 5.5.3
Author: WPClever
Author URI: https://wpclever.net
Text Domain: woo-product-bundle
Domain Path: /languages/
Requires at least: 4.0
Tested up to: 5.7.1
WC requires at least: 3.0
WC tested up to: 5.2.2
*/

defined( 'ABSPATH' ) || exit;

! defined( 'WOOSB_VERSION' ) && define( 'WOOSB_VERSION', '5.5.3' );
! defined( 'WOOSB_KEY' ) && define( 'WOOSB_KEY', 'j5k0m2t8' );
! defined( 'WOOSB_FILE' ) && define( 'WOOSB_FILE', __FILE__ );
! defined( 'WOOSB_URI' ) && define( 'WOOSB_URI', plugin_dir_url( __FILE__ ) );
! defined( 'WOOSB_DIR' ) && define( 'WOOSB_DIR', plugin_dir_path( __FILE__ ) );
! defined( 'WOOSB_DOCS' ) && define( 'WOOSB_DOCS', 'https://doc.wpclever.net/woosb/' );
! defined( 'WOOSB_SUPPORT' ) && define( 'WOOSB_SUPPORT', 'https://wpclever.net/support?utm_source=support&utm_medium=woosb&utm_campaign=wporg' );
! defined( 'WOOSB_REVIEWS' ) && define( 'WOOSB_REVIEWS', 'https://wordpress.org/support/plugin/woo-product-bundle/reviews/?filter=5' );
! defined( 'WOOSB_CHANGELOG' ) && define( 'WOOSB_CHANGELOG', 'https://wordpress.org/plugins/woo-product-bundle/#developers' );
! defined( 'WOOSB_DISCUSSION' ) && define( 'WOOSB_DISCUSSION', 'https://wordpress.org/support/plugin/woo-product-bundle' );
! defined( 'WPC_URI' ) && define( 'WPC_URI', WOOSB_URI );

include 'includes/wpc-dashboard.php';
include 'includes/wpc-menu.php';
include 'includes/wpc-kit.php';

if ( ! function_exists( 'woosb_init' ) ) {
	add_action( 'plugins_loaded', 'woosb_init', 11 );

	function woosb_init() {
		// load text-domain
		load_plugin_textdomain( 'woo-product-bundle', false, basename( __DIR__ ) . '/languages/' );

		if ( ! function_exists( 'WC' ) || ! version_compare( WC()->version, '3.0', '>=' ) ) {
			add_action( 'admin_notices', 'woosb_notice_wc' );

			return;
		}

		include_once 'includes/wpc-checker.php';
		include_once 'includes/class-helper.php';
		include_once 'includes/class-product.php';
		include_once 'includes/class-woosb.php';
		include_once 'includes/class-compatible.php';

		// start
		$GLOBALS['woosb'] = WPCleverWoosb();
	}
} else {
	add_action( 'admin_notices', 'woosb_notice_premium' );
}

if ( ! function_exists( 'woosb_notice_wc' ) ) {
	function woosb_notice_wc() {
		?>
        <div class="error">
            <p><strong>WPC Product Bundles</strong> requires WooCommerce version 3.0 or greater.</p>
        </div>
		<?php
	}
}

if ( ! function_exists( 'woosb_notice_premium' ) ) {
	function woosb_notice_premium() {
		?>
        <div class="error">
            <p>Seems you're using both free and premium version of <strong>WPC Product Bundles</strong>. Please
                deactivate the free version when using the premium version.</p>
        </div>
		<?php
	}
}