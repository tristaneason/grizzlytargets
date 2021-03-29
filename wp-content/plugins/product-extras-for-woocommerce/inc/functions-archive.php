<?php
/**
 * Functions for the product archive
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Does the product have product_extra groups?
 * @return Boolean
 */
function pewc_has_product_extra_groups( $product_id ) {

	if( apply_filters( 'pewc_bypass_extra_fields_transient', false ) ) {
		$has_fields = false;
	} else {
		$has_fields = get_transient( 'pewc_has_extra_fields_' . $product_id, false );
	}

	if( ! $has_fields ) {
		$has_fields = 'no';
		// If there's no transient, check the product
		$product_extra_groups = pewc_get_extra_fields( $product_id );
		if( $product_extra_groups ) {
			$has_fields = 'yes';
		}

		// Set the transient
		set_transient( 'pewc_has_extra_fields_' . $product_id, $has_fields, pewc_get_transient_expiration() );

	}
	
	return $has_fields;
}

/**
 * Products with product_extra groups can't be purchased from archive
 * @return Boolean
 */
function pewc_filter_is_purchasable( $is_purchasable, $product ) {
	if( is_archive() ) {
		$product_id = $product->get_id();
		$product_extra_groups = pewc_get_extra_fields( $product_id );
		if( $product_extra_groups ) {
			return false;
		}
	}
	return $is_purchasable;
}
// add_filter( 'woocommerce_is_purchasable', 'pewc_filter_is_purchasable', 10, 2 );

/**
 * Replace add to cart button in archive, shop, home and products shortcode
 * @return HTML
 */
function pewc_view_product_button( $button, $product, $args=array( 'class' => '' ) ) {

	$product_id = $product->get_id();
	if( pewc_has_product_extra_groups( $product_id ) != 'yes' ) {
		return $button;
	}

	$text = pewc_get_product_add_to_cart_text( $product );

  $button = sprintf(
		'<a class="%s" href="%s">%s</a>',
		apply_filters( 'pewc_add_to_cart_button_class', $args['class'], $product ),
		$product->get_permalink(),
		esc_html( $text )
	);

  return $button;

}
add_filter( 'woocommerce_loop_add_to_cart_link', 'pewc_view_product_button', 10, 3 );

/**
 * Filter the add to cart URL in archives etc
 * @since 3.3.5
 */
function pewc_product_add_to_cart_url( $url, $product ) {

	$product_id = $product->get_id();
	if( pewc_has_product_extra_groups( $product_id ) != 'yes' ) {
		return $url;
	}

	// If the product has add-ons then link to the product page, rather than trying to add it to the cart
	return $product->get_permalink();

}
add_filter( 'woocommerce_product_add_to_cart_url', 'pewc_product_add_to_cart_url', 10, 2 );

/**
 * Filter the add to cart URL in archives etc
 * @since 3.3.5
 */
function pewc_product_add_to_cart_text( $text, $product ) {

	$product_id = $product->get_id();
	if( pewc_has_product_extra_groups( $product_id ) != 'yes' ) {
		return $text;
	}

	// If the product has add-ons then link to the product page, rather than trying to add it to the cart
	return pewc_get_product_add_to_cart_text( $product );

}
add_filter( 'woocommerce_product_add_to_cart_text', 'pewc_product_add_to_cart_text', 10, 2 );

/**
 * Get our alternative text for the add to cart button in loops
 * @since 3.3.5
 */
function pewc_get_product_add_to_cart_text( $product ) {
	return apply_filters(
		'pewc_filter_view_product_text',
		__( 'Select options', 'pewc' ),
		$product
	);
}
