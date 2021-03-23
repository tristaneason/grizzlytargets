<?php
/**
 * A select field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

// echo pewc_field_label( $item, $id );

if( isset( $item['field_options'] ) ) {
	$index = 0;
	$first_option = ! empty( $item['first_field_empty'] ) ? true : false;
	$option_count = 0;

	printf(
		'%s<select class="pewc-form-field" id="%s" name="%s">',
		$open_td,
		esc_attr( $id ),
		esc_attr( $id )
	);

	foreach( $item['field_options'] as $key=>$option_value ) {

		$name = apply_filters( 'prefix_filter_field_option_name', esc_html( $option_value['value'] ), $key, $item, $product );
		$option_price = pewc_get_option_price( $option_value, $item, $product );
		$option_percentage = '';

		// Check for percentages
		if( ! empty( $item['field_percentage'] ) && ! empty( $option_price ) ) {
			// Set the option price as a percentage of the product price
			$option_percentage = floatval( $option_price );
			$product_price = $product->get_price();
			$option_price = ( floatval( $option_price ) / 100 ) * $product_price;
			// Get display price according to inc tax / ex tax setting
			$option_price = pewc_maybe_include_tax( $product, $option_price );
			// $option_percentage = floatval( $item['field_price'] );
		}

		// Include prices in option labels
		if( ! empty( $option_price ) && apply_filters( 'pewc_show_option_prices', true, $item ) ) {
			$name .= apply_filters( 'pewc_option_price_separator', '+', $item ) . pewc_get_semi_formatted_raw_price( $option_price );
			$name = apply_filters( 'pewc_option_name', $name, $item, $product, $option_price );
		}

		$this_value = ( $first_option && $option_count === 0 ) ? '' : $option_value['value'];
		$selected = ( $this_value == $value ) ? 'selected="selected"' : '';

		printf(
			'<option class="%s" data-option-cost="%s" value="%s" %s data-option-percentage="%s">%s</option>',
			'pewc-select-option-has-percentage',
			esc_attr( $option_price ),
			esc_attr( $this_value ),
			$selected,
			$option_percentage,
			$name
		);

		$option_count++;

	}

	printf(
		'</select>%s',
		$close_td
	);

}
