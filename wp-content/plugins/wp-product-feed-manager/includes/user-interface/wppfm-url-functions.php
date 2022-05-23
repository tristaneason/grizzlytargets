<?php

/**
 * Checks if the user is on one of the plugin pages or not.
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 * @since 2.30.0 Modified the function to use the actual parameters and not the url as a string.
 *
 * @return boolean
 */
function wppfm_on_any_own_plugin_page() {
	$page_param = isset( $_GET['page'] ) ? $_GET['page'] : '';

	if ( false === stripos( $page_param, WPPFM_PLUGIN_NAME ) && false === stripos( $page_param, 'wppfm-' ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Checks if the user is on the options page
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 * @since 2.30.0 Modified the function to use the actual parameters and not the url as a string.
 *
 * @return boolean
 */
function wppfm_on_plugins_settings_page() {
	$page_param = isset( $_GET['page'] ) ? $_GET['page'] : '';

	if ( false === stripos( $page_param, 'wppfm-' ) ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Checks if the user is on one of the plugins pages but not on the options page
 * Also returns true when current page can not be defined
 *
 * @since 2.3.4
 * @since 2.30.0 Modified the function to use the actual parameters and not the url as a string.
 *
 * @return boolean
 */
function wppfm_on_own_main_plugin_page() {
	$ref_url = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';

	// return true if the current page url has not been identified
	if ( empty( $ref_url ) ) {
		return true;
	}

	$page_param = isset( $_GET['page'] ) ? $_GET['page'] : '';

	// return true if the url contains the plugin name under the page attribute or if it's an ajax call
	if ( false === stripos( $page_param, WPPFM_PLUGIN_NAME ) && false === stripos( $ref_url, '/wp-admin/admin-ajax.php' ) ) {
		return false;
	} else {
		return true;
	}
}
