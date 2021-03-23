<?php
/**
 * Functions for the summary panel
 * @since 3.4.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pewc_display_summary_panel_enabled() {
	$enable = get_option( 'pewc_enable_summary_panel', false );
	return apply_filters( 'pewc_enable_summary_panel', $enable );
}

/**
 * Display the summary panel
 * @since 3.4.0
 */
function pewc_display_summary_panel( $post_id, $product, $summary_panel ) {

	// Check whether we can display the panel
	if( ! pewc_is_pro() || ! pewc_display_summary_panel_enabled() ) {
		return;
	}

	$path = pewc_include_frontend_template( 'summary-panel.php' );
	include( $path );

}
add_action( 'pewc_after_group_wrap', 'pewc_display_summary_panel', 5, 3 );
