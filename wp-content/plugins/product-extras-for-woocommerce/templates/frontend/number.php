<?php
/**
 * A number field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$min = isset( $item['field_minval'] ) ? $item['field_minval'] : '0';
$max = isset( $item['field_maxval'] ) ? $item['field_maxval'] : '';

printf(
	'%s<input type="number" class="pewc-form-field pewc-number-field pewc-number-field-%s" id="%s" name="%s" value="%s" min="%s" max="%s" step="%s" autocomplete="%s">%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $item['field_id'] ),
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $value ),
	esc_attr( $min ),
	esc_attr( $max ),
	apply_filters( 'pewc_number_field_step', '1', $item ),
	apply_filters( 'pewc_autocomplete', 'off', $item ),
	$close_td
);
