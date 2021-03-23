<?php
/**
 * A text field template
 * @since 3.7.7
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

// echo pewc_field_label( $item, $id );
$attributes = pewc_get_color_field_attributes( $item );

printf(
	'%s<input type="text" class="pewc-form-field pewc-color-picker-field" id="%s" name="%s" %s value="%s">%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $id ),
	esc_attr( $id ),
	$attributes,
	esc_attr( $value ),
	$close_td
); ?>
