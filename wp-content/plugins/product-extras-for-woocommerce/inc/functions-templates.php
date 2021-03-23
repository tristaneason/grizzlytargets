<?php
/**
 * Functions for loading templates
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load templates according to a hierarchy
 * Look for a template in the child theme, then the parent theme, finally the plugin's default template
 *
 * @since 2.0.0
 * @param $template - the template file name, e.g. checkbox.php
 */
function pewc_include_frontend_template( $template ) {

	$template_path = '';

	// Check child theme first
	if( file_exists( trailingslashit( get_stylesheet_directory() ) . 'product-extras/' . $template ) ) {
		$template_path = trailingslashit( get_stylesheet_directory() ) . 'product-extras/' . $template;

	// Check parent theme next
	} else if( file_exists( trailingslashit( get_template_directory() ) . 'product-extras/' . $template ) ) {
		$template_path = trailingslashit( get_template_directory() ) . 'product-extras/' . $template;

	// Check plugin compatibility last
	} else if( file_exists ( trailingslashit( pewc_get_frontend_template_location() ) . $template ) ) {
		$path = pewc_get_frontend_template_location();
		$template_path = trailingslashit( $path ) . $template;
	}

	$template_path = apply_filters( 'pewc_filter_frontend_template_path', $template_path, $template );
	return $template_path;

}

/**
 * Get the default path for frontend templates
 *
 * @since 2.0.0
 */
function pewc_get_frontend_template_location() {
	$location = PEWC_DIRNAME . '/templates/frontend/';
	return apply_filters( 'pewc_filter_frontend_template_location', $location );
}
