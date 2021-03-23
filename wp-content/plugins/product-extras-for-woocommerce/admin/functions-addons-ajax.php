<?php
/**
 * Functions for loading add-ons via AJAX
 * @since 3.3.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Are we using AJAX to load the add-ons?
 */
function pewc_enable_ajax_load_addons() {
	return apply_filters( 'pewc_enable_ajax_load_addons', false );
}

function pewc_load_addons() {
	ob_start();
	$post_id = $_POST['post_id'];
	$groups = pewc_get_extra_fields( $post_id );
	pewc_display_product_groups( $groups, $post_id, true );
	wp_die();
}
add_action( 'wp_ajax_pewc_load_addons', 'pewc_load_addons' );
