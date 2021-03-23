<?php
/**
 * A checkbox field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

printf(
	'%s<input type="checkbox" class="pewc-form-field" id="%s" name="%s" %s value="__checked__">%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $id ),
	esc_attr( $id ),
	checked( 1, $value, false ),
	$close_td
);

// echo pewc_field_label( $item, $id, $group_layout ); ?>
