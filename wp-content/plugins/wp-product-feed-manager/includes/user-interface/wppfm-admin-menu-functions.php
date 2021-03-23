<?php

/**
 * @package WP Product Feed Manager/User Interface/Functions
 * @version 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wppfm_feed_manager_main_page() {

	global $wppfm_tab_data;

	$active_tab          = isset( $_GET['tab'] ) ? $_GET['tab'] : 'feed-list';
	$page_start_function = 'wppfm_main_admin_page'; // default

	$list_tab = new WPPFM_Tab(
		'feed-list',
		'feed-list' === $active_tab ? true : false,
		__( 'Feed List', 'wp-product-feed-manager' ),
		'wppfm_main_admin_page'
	);

	$product_feed_tab = new WPPFM_Tab(
		'product-feed',
		'product-feed' === $active_tab ? true : false,
		__( 'Product Feed', 'wp-product-feed-manager' ),
		'wppfm_add_product_feed_page'
	);

	$wppfm_tab_data = apply_filters( 'wppfm_main_form_tabs', array( $list_tab, $product_feed_tab ), $active_tab );

	foreach ( $wppfm_tab_data as $tab ) {
		if ( $tab->get_page_identifier() === $active_tab ) {
			$page_start_function = $tab->get_class_identifier();
			break;
		}
	}

	$page_start_function();
}

/**
 * starts the main admin page
 */
function wppfm_main_admin_page() {
	$start = new WPPFM_Main_Admin_Page();

	// now let's get things going
	$start->show();
}

function wppfm_add_product_feed_page() {
	$add_new_feed_page = new WPPFM_Product_Feed_Page();
	$add_new_feed_page->show();
}

/**
 * options page
 */
function wppfm_options_page() {
	$add_options_page = new WPPFM_Add_Options_Page();
	$add_options_page->show();
}

/**
 * Returns an array of possible feed types that can be altered using the wppfm_feed_types filter.
 *
 * @return array with possible feed types
 */
function wppfm_list_feed_type_text() {

	return apply_filters(
		'wppfm_feed_types',
		array(
			'1'  => 'Product Feed',
			'10' => 'API Product Feed',
		)
	);
}

/**
 * Returns a string containing the footer for the plugin pages. This footer contains links to the About Us and
 * Contact Us pages and the Terms and Conditions and Documentation.
 *
 * @return  string  Html code containing the footer for the plugin pages.
 */
function wppfm_page_footer() {
	return '<a href="' . WPPFM_EDD_SL_STORE_URL . '" target="_blank">' . esc_html__( 'About Us', 'wp-product-feed-manager' ) . '</a>
			 | <a href="' . WPPFM_EDD_SL_STORE_URL . 'support/" target="_blank">' . esc_html__( 'Contact Us', 'wp-product-feed-manager' ) . '</a>
			 | <a href="' . WPPFM_EDD_SL_STORE_URL . 'terms/" target="_blank">' . esc_html__( 'Terms and Conditions', 'wp-product-feed-manager' ) . '</a>
			 | <a href="' . WPPFM_EDD_SL_STORE_URL . 'support/documentation/create-product-feed/" target="_blank">' . esc_html__( 'Documentation', 'wp-product-feed-manager' ) . '</a>
			 | '
	. sprintf(
		/* translators: %s: five stars link */
		__( 'If you like working with our Feed Manager please leave us a %s rating. A huge thanks in advance!', 'wp-product-feed-manager' ),
		'<a href="https://wordpress.org/support/plugin/wp-product-feed-manager/reviews?rate=5#new-post" target="_blank" class="wppfm-rating-request">' . '&#9733;&#9733;&#9733;&#9733;&#9733;' . '</a>'
	);
}
