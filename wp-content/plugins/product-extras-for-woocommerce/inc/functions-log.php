<?php
/**
 * Functions for logging
 * @since 3.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pewc_error_log( $message ) {
	// Have we got a log file yet?
	$pewc_log_file = get_option( 'pewc_log_file', false );
	if( ! $pewc_log_file ) {
		$pewc_log_file = md5( time() );
		update_option( 'pewc_log_file', $pewc_log_file );
	}
	$file = trailingslashit( PEWC_PLUGIN_DIR_PATH ). "logs/" . $pewc_log_file . ".log";
	if( ! file_exists( $file ) ) {
		$handle = fopen( $file, "a" );
	}
	$current = file_get_contents( $file );
	$current .= date( 'Y-m-d h:i:s' ) . ": " . $message . "\n";
	file_put_contents( $file, $current );
}
