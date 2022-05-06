<?php
/**
 * Functions for setting product weights using calculations
 * @since 3.9.5
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set the product weight based on the value of a field
 * @since 3.9.5
 */
function pewc_set_product_weight_meta( $cart_item_data, $item, $group_id, $field_id, $value ) {

	$item_weight = ! empty( $cart_item_data['product_extras']['weight'] ) ? $cart_item_data['product_extras']['weight'] : 0;
	if( $item['field_type'] == 'calculation' && ( ! empty( $item['formula_action'] ) && $item['formula_action'] == 'weight' ) ) {
		$cart_item_data['product_extras']['weight'] = $item_weight + $value;
	}

	return $cart_item_data;

}
add_filter( 'pewc_filter_end_add_cart_item_data', 'pewc_set_product_weight_meta', 10, 5 );

/**
 * Update the product weight
 * @since 3.9.5
 */
function pewc_set_product_weight( $cart ) {

	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
		return;
	}

	foreach( $cart->get_cart() as $cart_item ) {
		$item_weight = $cart_item['data']->get_weight();
		if ( ! empty( $cart_item['product_extras']['weight'] ) ) {
			$item_weight += $cart_item['product_extras']['weight'];
			$cart_item['data']->set_weight( $item_weight );
		}
	}

}
add_filter( 'woocommerce_before_calculate_totals', 'pewc_set_product_weight', 100 );
