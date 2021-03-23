<?php

/**
 * @package WP Product Feed Manager/User Interface/Functions
 * @version 2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the html for a standard WordPress error message
 *
 * @param string $message
 * @param bool $dismissible (default false)
 * @param string $permanent_dismissible_id (default '')
 *
 * @return string html
 */
function wppfm_show_wp_error( $message, $dismissible = false, $permanent_dismissible_id = '' ) {
	return wppfm_show_wp_message( $message, 'error', $dismissible, $permanent_dismissible_id );
}

/**
 * Returns the html for a standard WordPress warning message
 *
 * @param string $message
 * @param bool $dismissible (default false)
 * @param string $permanent_dismissible_id (default '')
 *
 * @return string html
 */
function wppfm_show_wp_warning( $message, $dismissible = false, $permanent_dismissible_id = '' ) {
	return wppfm_show_wp_message( $message, 'warning', $dismissible, $permanent_dismissible_id );
}

/**
 * Returns the html for a standard WordPress success message
 *
 * @param string $message
 * @param bool $dismissible (default false)
 * @param string $permanent_dismissible_id (default '')
 *
 * @return string html
 */
function wppfm_show_wp_success( $message, $dismissible = false, $permanent_dismissible_id = '' ) {
	return wppfm_show_wp_message( $message, 'success', $dismissible, $permanent_dismissible_id );
}

/**
 * Returns the html for a standard WordPress info message
 *
 * @param string $message
 * @param bool $dismissible (default false)
 * @param string $permanent_dismissible_id (default '')
 *
 * @return string html
 */
function wppfm_show_wp_info( $message, $dismissible = false, $permanent_dismissible_id = '' ) {
	return wppfm_show_wp_message( $message, 'info', $dismissible, $permanent_dismissible_id );
}

/**
 * Returns the html for a standard WordPress message
 *
 * @param string $message
 * @param string $type
 * @param bool $dismissible
 * @param string $permanent_dismissible_id
 *
 * @return string html
 */
function wppfm_show_wp_message( $message, $type, $dismissible, $permanent_dismissible_id ) {
	$dismissible_text    = $dismissible ? ' is-dismissible' : '';
	$perm_dismissible    = $permanent_dismissible_id ? ' id="disposable-warning-message"' : '';
	$dismiss_permanently = '' !== $permanent_dismissible_id ? '<p id=dismiss-permanently>dismiss permanently<p>' : '';

	return '<div' . $perm_dismissible . ' class="notice notice-' . $type . $dismissible_text . '"><p>' . $message . '</p>' . $dismiss_permanently . '</div>';
}

/**
 * Shows an error message to the user and writes an error log based on the wp_error given
 *
 * @since 1.9.3
 *
 * @param wp_error object $response
 * @param string $message
 *
 * @return string html
 */
function wppfm_handle_wp_errors_response( $response, $message ) {
	$error_messages = method_exists( $response, 'get_error_messages' ) ? $response->get_error_messages() : array( 'Error unknown' );
	$error_message  = method_exists( $response, 'get_error_message' ) ? $response->get_error_message() : 'Error unknown';
	$error_text     = ! empty( $error_messages ) ? implode( ' :: ', $error_messages ) : 'error unknown!';

	wppfm_write_log_file( $message . ' ' . $error_text );

	return wppfm_show_wp_error( $message . ' Error message: ' . $error_message );
}

/**
 * enables writing log files in the plugin folder
 *
 * @since 1.5.1
 *
 * @param string $error_message
 * @param string $filename (default 'error')
 */
function wppfm_write_log_file( $error_message, $filename = 'error' ) {
	$file = WPPFM_PLUGIN_DIR . $filename . '.log';

	if ( is_null( $error_message ) || is_string( $error_message ) || is_int( $error_message ) || is_bool( $error_message ) || is_float( $error_message ) ) {
		$message_line = $error_message;
	} elseif ( is_array( $error_message ) || is_object( $error_message ) ) {
		$message_line = wp_json_encode( $error_message );
	} else {
		$message_line = 'ERROR! Could not write messages of type ' . gettype( $error_message );
	}

	if ( false === file_put_contents( $file, date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . ' - ' . ucfirst( $filename ) . ' Message: ' . $message_line . PHP_EOL, FILE_APPEND ) ) {
		/* translators: %s: Error message */
		wppfm_show_wp_error( sprintf( __( 'There was an error but I was unable to store the error message in the log file. The message was %s', 'wp-product-feed-manager' ), $error_message ) );
	}
}

/**
 * Returns an html string containing a message to inform the user that he has to update the WooCommerce plugin
 *
 * @return string html
 */
function wppfm_update_your_woocommerce_version_message() {
	// To prevent several PHP Warnings if the WC folder name has been changed whilst the plugin is still registered.
	// @since 2.11.0.
	if ( file_exists( WPPFM_PLUGIN_DIR . '../woocommerce/woocommerce.php' ) ) {
		$wc_version = get_plugin_data( WPPFM_PLUGIN_DIR . '../woocommerce/woocommerce.php' )['Version'];
	} else {
		$wc_version = '"UNKNOWN"';
	}

	$message_code  = '<div class="full-screen-message-field">';
	$message_code .= '<p>*** ' . sprintf(
		/* translators: %1$s: minimum version of the WooCommerce plugin, %2$s: installed version of the WooCommerce plugin */
			esc_html__(
				'This plugin requires WooCommerce version %1$s as a minimum!
				It seems you have installed WooCommerce version %2$s which is a version that is not supported.
				Please update to the latest version ***',
				'wp-product-feed-manager'
			),
		WPPFM_MIN_REQUIRED_WC_VERSION,
		$wc_version
	) . '</p>';
	$message_code .= '</div>';

	return $message_code;
}

/**
 * Returns an html string containing a message to the user that WooCommerce is not installed on the server
 *
 * @return string html
 */
function wppfm_you_have_no_woocommerce_installed_message() {
	$message_code  = '<div class="full-screen-message-field">';
	$message_code .= '<p>*** ' . esc_html__(
		'This plugin only works in conjunction with the WooCommerce Plugin!
				It seems you have not installed the WooCommerce Plugin yet, so please do so before using this Plugin.',
		'wp-product-feed-manager'
	) . ' ***</p>';
	/* translators: %s: link to information about the WooCommerce plugin */
	$message_code .= '<p>' . sprintf( __( 'You can find more information about the Woocommerce Plugin %sby clicking here</a>.', 'wp-product-feed-manager' ), '<a href="https://wordpress.org/plugins/woocommerce/">' ) . '</p>';
	$message_code .= '</div>';

	return $message_code;
}

/**
 * Writes a http_requests_error.log file in the plugin folder when there is a http request failed
 *
 * @since 1.9.0
 *
 * @param string $response
 * @param array $args
 * @param string $url
 *
 * @return string
 */
function wppfm_log_http_requests( $response, $args, $url ) {
	if ( false !== is_wp_error( $response ) && wppfm_on_any_own_plugin_page() ) {
		$logfile = WPPFM_PLUGIN_DIR . 'http_request_error.log';
		file_put_contents( $logfile, sprintf( "### %s, URL: %s\nREQUEST: %sRESPONSE: %s\n", date( 'c' ), $url, print_r( $args, true ), print_r( $response, true ) ), FILE_APPEND );
	}

	return $response;
}

// hook into WP_Http::_dispatch_request()
add_filter( 'http_response', 'wppfm_log_http_requests', 10, 3 );
