<?php
/**
 * A products field template
 * @since 2.2.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

// echo pewc_field_label( $item, $id );

echo $open_td;

if( isset( $item['child_products'] ) ) {

	$layout = $item['products_layout'];
	$index = 0;
	// Set the allow_none parameter according to whether the field is required or not
	// allow_none means that child products can be deleted from the cart without deleting the parent product
	$allow_none = empty( $item['required'] ) ? true : false;
	$file = 'products/products-' . $layout . '.php';
	$path = pewc_include_frontend_template( $file );
	include( $path ); ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_quantities" value="<?php echo esc_attr( $item['products_quantities'] ); ?>">
	<?php $allow_none = ! empty( $item['allow_none'] ) ? 1 : 0; ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_allow_none" value="<?php echo esc_attr( $allow_none ); ?>">
	<?php $min_products = ! empty( $item['min_products'] ) ? absint( $item['min_products'] ) : ''; ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_min_products" value="<?php echo esc_attr( $min_products ); ?>">
	<?php $max_products = ! empty( $item['max_products'] ) ? absint( $item['max_products'] ) : ''; ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_max_products" value="<?php echo esc_attr( $max_products ); ?>">
	<?php $child_discount = ! empty( $item['child_discount'] ) ? absint( $item['child_discount'] ) : ''; ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_child_discount" value="<?php echo esc_attr( $child_discount ); ?>">
	<?php $discount_type = ! empty( $item['discount_type'] ) ? $item['discount_type'] : ''; ?>
	<input type="hidden" name="<?php echo esc_attr( $id ); ?>_discount_type" value="<?php echo esc_attr( $discount_type ); ?>">

<?php }

echo $close_td; ?>
