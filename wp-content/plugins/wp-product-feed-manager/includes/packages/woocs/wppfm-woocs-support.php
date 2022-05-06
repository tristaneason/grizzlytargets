<?php

/**
 * Package that adds support for the WooCommerce Currency Switcher (WOOCS) to the Feed Manager
 *
 * @package WPML Support.
 * @since 2.28.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wppfm_woocs_setup();

/**
 * Includes all required classes and files for the woocs support package
 *
 * @since 2.28.0
 */
function wppfm_woocs_setup() {
	require_once __DIR__ . '/includes/wppfm-woocs-functions.php';
}
