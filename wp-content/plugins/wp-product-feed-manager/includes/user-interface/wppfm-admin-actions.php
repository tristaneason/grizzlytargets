<?php

/**
 * @package WP Product Feed Manager/User Interface/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the feed manager menu in the Admin page
 *
 * @param bool $channel_updated default false
 */
function wppfm_add_feed_manager_menu( $channel_updated = false ) {
	// defines the feed manager menu
	add_menu_page(
		__( 'WP Feed Manager', 'wp-product-feed-manager' ),
		__( 'Feed Manager', 'wp-product-feed-manager' ),
		'manage_woocommerce',
		'wp-product-feed-manager',
		'wppfm_feed_manager_main_page',
		esc_url( WPPFM_PLUGIN_URL . '/images/app-rss-plus-xml-icon.png' )
	);

	// add the settings
	add_submenu_page(
		'wp-product-feed-manager',
		__( 'Settings', 'wp-product-feed-manager' ),
		__( 'Settings', 'wp-product-feed-manager' ),
		'manage_woocommerce',
		'wppfm-options-page',
		'wppfm_options_page'
	);
}

add_action( 'admin_menu', 'wppfm_add_feed_manager_menu' );

/**
 * Checks if the backups are valid for the current database version and warns the user if not
 *
 * @since 1.9.6
 */
function wppfm_check_backups() {
	if ( ! wppfm_check_backup_status() ) {
		$msg = __( 'Due to an update of your Feed Manager plugin, your feed backups are no longer valid! Please open the Feed Manager Settings page, remove all current backups, and make a new one.', 'wp-product-feed-manager' )
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo $msg; ?></p>
		</div>
		<?php
	}
}

add_action( 'admin_notices', 'wppfm_check_backups' );

/**
 * Sets the global background process
 *
 * @since 1.10.0
 *
 * @global WPPFM_Feed_Processor $background_process
 */
function initiate_background_process() {
	global $background_process;

	if ( isset( $_GET['tab'] ) ) {
		$active_tab = $_GET['tab'];
		set_transient( 'wppfm_set_global_background_process', $active_tab, WPPFM_TRANSIENT_LIVE );
	} else {
		$active_tab = ! get_transient( 'wppfm_set_global_background_process' ) ? 'feed-list' : get_transient( 'wppfm_set_global_background_process' );
	}

	if ( ( 'product-feed' === $active_tab || 'feed-list' === $active_tab ) ) {
		if ( ! class_exists( 'WPPFM_Feed_Processor' ) ) {
			require_once __DIR__ . '/../application/class-wppfm-feed-processor.php';
		}

		$background_process = new WPPFM_Feed_Processor();
	}

	if ( 'product-review-feed' === $active_tab ) {
		if ( ! class_exists( 'WPPRFM_Review_Feed_Processor' ) && function_exists( 'wpprfm_include_background_classes' ) ) {
			wpprfm_include_background_classes();
		}

		// @since 2.29.0 to prevent a PHP fatal error when a review feed fails and the user deactivates the plugin.
		if ( class_exists( 'WPPRFM_Review_Feed_Processor' ) ) {
			$background_process = new WPPRFM_Review_Feed_Processor();
		}
	}
}

// register the background process
add_action( 'wp_loaded', 'initiate_background_process' );

/**
 * Provides debug information after a failed HTTP request.
 *
 * Output in `wp-content\debug.log` file:
 *
 * @since 2.25.0.
 *
 * @param array|WP_Error $response HTTP response or WP_Error object.
 * @param string         $context  Context under which the hook is fired.
 * @param string         $requests    HTTP transport used.
 * @param array          $r        HTTP request arguments.
 * @param string         $url      The request URL.
 */
function wppfm_debug_wp_remote_post_and_get_request( $response, $context, $requests, $r, $url ) {
	error_log( $url );
	error_log( json_encode( $response ) );
	error_log( $requests );
	error_log( $context );
	error_log( json_encode( $r ) );
}

//add_action( 'http_api_debug', 'wppfm_debug_wp_remote_post_and_get_request', 10, 5 );

/**
 * Makes sure the automatic feed update cron schedule is still installed.
 *
 * @since 2.20.0
 */
function wppfm_verify_feed_update_schedule_registration() {
	wppfm_check_feed_update_schedule();
}

add_action( 'admin_menu', 'wppfm_verify_feed_update_schedule_registration' );
