<?php

/**
 * @package WP Product Feed Manager/User Interface/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds links to the started guide and premium site in the plugin description on the Plugins page.
 *
 * @since 2.6.0
 *
 * @param   array   $actions        Associative array of action names to anchor tags.
 * @param   string  $plugin_file    Plugin file name.
 * @param   array   $plugin_data    Array of plugin data from the plugin file.
 * @param   string  $context        Plugin status context.
 *
 * @return  array   Html code that adds links to the plugin description.
 */
function wppfm_plugins_action_links( $actions, $plugin_file, $plugin_data, $context ) {
	$actions['starter_guide'] = '<a href="' . WPPFM_EDD_SL_STORE_URL . '/support/documentation" target="_blank">' . __( 'Starter Guide', 'wp-product-feed-manager' ) . '</a>';

	if ( 'WP Product Feed Manager' === WPPFM_EDD_SL_ITEM_NAME ) {
		$actions['go_premium'] = '<a style="color:green;" href="' . WPPFM_EDD_SL_STORE_URL . '" target="_blank"><b>' . __( 'Go Premium', 'wp-product-feed-manager' ) . '</b></a>';
	} else {
		$actions['support'] = '<a href="' . WPPFM_EDD_SL_STORE_URL . '/support" target="_blank">' . __( 'Get Support', 'wp-product-feed-manager' ) . '</a>';
	}

	return $actions;
}

add_filter( 'plugin_action_links_' . WPPFM_PLUGIN_CONSTRUCTOR, 'wppfm_plugins_action_links', 10, 4 );

function wppfm_footer_links( $footer_text ) {
	if ( wppfm_on_own_main_plugin_page() || wppfm_on_plugins_settings_page() ) {
		return wppfm_page_footer() . '<br>' . $footer_text;
	} else {
		return $footer_text;
	}
}

add_filter( 'admin_footer_text', 'wppfm_footer_links', 10, 1 );

function wppfm_change_query_filter() {
	return 100;
}

add_filter( 'wppfm_product_query_limit', 'wppfm_change_query_filter' );
