<?php
/**
 * Functions for multilingual plugins
 * @since 3.7.10
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add support for pewc_group custom post type in Polylang
 * @since 3.7.10
 */
function pewc_add_groups_to_pll( $post_types, $is_settings ) {

	if ( $is_settings ) {
		unset( $post_types['pewc_group'] );
	} else {
		$post_types['pewc_group'] = 'pewc_group';
	}

	return $post_types;

}
add_filter( 'pll_get_post_types', 'pewc_add_groups_to_pll', 10, 2 );
