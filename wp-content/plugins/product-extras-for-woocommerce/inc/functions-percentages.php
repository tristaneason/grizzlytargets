<?php
/**
 * Functions for calculating percentages
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter labels for percentages
 * @since 2.0.0
 */
function pewc_filter_label_percentage( $label, $item, $id ) {
	if( ! pewc_is_pro() ) {
		return $label;
	}
	if( empty( $item['field_percentage'] ) ) {
		return $label;
	}

	$label = '';

	if( isset( $item['field_label'] ) || isset( $item['field_price'] ) ) {

		$label = '<label for="' . esc_attr( $id ) . '">';
		if( isset( $item['field_label'] ) ) {
			$label .= esc_html( $item['field_label'] );
		}
		$label .= '<span class="required"> &#42;</span>';
		if( ! empty( $item['field_price'] ) && $item['field_type'] != 'name_price' ) {

			$price = $item['field_price'] . '%';

			$label .= '<span class="pewc-field-price"> ' . $price;
			if( ! empty( $item['per_character'] ) ) {
				$label .= ' <span class="pewc-per-character-label">' . __( 'per character', 'pewc' ) . '</span>';
			}
			$label .= '</span>';
		}
		$label .= '</label>';

	}

	return $label;
}
// add_filter( 'pewc_filter_field_label', 'pewc_filter_label_percentage', 10, 3 );

function pewc_filter_label_price_for_percentages( $price, $product, $item ) {

	if( ! empty( $item['field_percentage'] ) ) {

		$product_price = $product->get_price();
		$field_price = pewc_get_field_price( $item, $product );
		$price = ( floatval( $field_price ) / 100 ) * $product_price;
		
	}

	return $price;

}
add_filter( 'pewc_filter_display_price_for_percentages', 'pewc_filter_label_price_for_percentages', 10, 3 );

/**
 * Get the calculated percentage price for a field
 * @param $percentage	The percentage value, e.g. 50
 * @param $product 		The product object
 */
function pewc_calculate_percentage_price( $percentage, $product ) {
	$product_price = $product->get_price();
	$price = ( floatval( $percentage ) / 100 ) * $product_price;
	return $price;
}
