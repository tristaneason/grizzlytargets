<?php

/**
 * The uninstall functions.
 *
 * @package WP Product Feed Manager/Functions
 * @version 3.5.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$upload_dir = wp_get_upload_dir();

if ( ! class_exists( 'WPPFM_Folders' ) ) {
	require_once __DIR__ . '/includes/setup/class-wppfm-folders.php';
}

// Stop the scheduled feed update actions.
wp_clear_scheduled_hook( 'wppfm_feed_update_schedule' );

// Remove the support folders.
WPPFM_Folders::delete_folder( $upload_dir['basedir'] . '/wppfm-channels' );
WPPFM_Folders::delete_folder( $upload_dir['basedir'] . '/wppfm-feeds' );
WPPFM_Folders::delete_folder( $upload_dir['basedir'] . '/wppfm-logs' );

$tables = array(
	$wpdb->prefix . 'feedmanager_country',
	$wpdb->prefix . 'feedmanager_feed_status',
	$wpdb->prefix . 'feedmanager_field_categories',
	$wpdb->prefix . 'feedmanager_channel',
	$wpdb->prefix . 'feedmanager_product_feed',
	$wpdb->prefix . 'feedmanager_product_feedmeta',
	$wpdb->prefix . 'feedmanager_source',
	$wpdb->prefix . 'feedmanager_errors',
);

// Remove the feedmanager tables.
foreach ( $tables as $table ) {
	//phpcs:ignore
	$wpdb->query( "DROP TABLE IF EXISTS $table" );
}

// unregister the plugin
unregister_plugin();

/**
 * Removes the registration info from the database
 */
function unregister_plugin() {
	// Retrieve the license from the database.
	$license = get_option( 'wppfm_lic_key' );

	delete_option( 'wppfm_db_version' );
	delete_option( 'wppfm_lic_status' );
	delete_option( 'wppfm_lic_status_date' ); // deprecated.
	delete_option( 'wppfm_lic_key' );
	delete_option( 'wppfm_lic_expires' );
	delete_option( 'wppfm_channel_update_check_date' );
	delete_option( 'wppfm_channels_to_update' );
	delete_option( 'wppfm_ftp_passive' ); // deprecated as of 1.9.3.
	delete_option( 'wppfm_auto_feed_fix' );
	delete_option( 'wppfm_disabled_background_mode' ); // @since 2.0.7
	delete_option( 'wppfm_debug_mode' );
	delete_option( 'wppfm_prep_check' ); // deprecated.
	delete_option( 'wppfm_third_party_attribute_keywords' );
	delete_option( 'wppfm_license_notice_suppressed' ); // @since 1.9.0
	delete_option( 'wppfm_feed_queue' ); // @since 1.10.0
	delete_option( 'wppfm_background_process_is_running' ); // @since 1.10.0
	delete_option( 'wppfm_background_process_time_limit' ); // @since 2.2.0 (deprecated)
	delete_option( 'wppfm_notice_mailaddress' ); // @since 2.3.0
	delete_option( 'wppfm_batch_counter' ); // @since 2.11.0

	if ( $license ) { // If the plugin is a licensed version then deactivate it on the license server.
		// Data to send in our API request.
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_name'  => rawurlencode( 'Woocommerce Google Feed Manager' ), // the name of the plugin in EDD
			'url'        => home_url(),
		);

		// Call the custom API.
		wp_remote_post(
			'https://www.wpmarketingrobot.com/',
			array(
				'timeout' => 15,
				'body'    => $api_params,
			)
		);
	}
}
