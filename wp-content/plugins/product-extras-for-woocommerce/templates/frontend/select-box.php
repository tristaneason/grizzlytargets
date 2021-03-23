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
		'%s<select class="pewc-form-field pewc-select-box" data-select-id="%s" id="%s" name="%s" style="display: none">',
		$open_td,
		esc_attr( $id ),
		esc_attr( $id ) . '_select_box',
		esc_attr( $id )
	);

	$all_options = array();

	foreach( $item['field_options'] as $key=>$option_value ) {

		$src = pewc_get_swatch_image_url( $option_value, $item );

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

		// Get the price
		$option_cost = pewc_get_semi_formatted_raw_price( $option_price );

		$this_value = ( $first_option && $option_count === 0 ) ? '' : $option_value['value'];
		$selected = ( $this_value == $value ) ? 'selected="selected"' : '';

		// These options are going to be replaced by divs by ddslick
		printf(
			'<option class="%s" data-option-cost="%s" data-imagesrc="%s" data-description="%s" value="%s" %s data-option-percentage="%s">%s</option>',
			'pewc-select-option-has-percentage',
			esc_attr( $option_price ),
			esc_url( $src[0] ),
			$option_cost,
			esc_attr( $this_value ),
			$selected,
			$option_percentage,
			$name
		);

		$option_id = $id . '_' . $option_count;

		$all_options[$option_id] = array(
			'option_count'				=> $option_count,
			'option_cost'					=> $option_price,
			'option_value'				=> $this_value,
			'option_percentage'		=> $option_percentage
		);

		$option_count++;

	}

	echo '</select>';

	// printf(
	// 	'<input type="hidden" class="pewc-form-field pewc-select-box-hidden" id="%s" name="%s" value="%s" data-selected-option-price="%s">',
	// 	esc_attr( $id ),
	// 	esc_attr( $id ),
	// 	$value,
	// 	''
	// );

	// Store the options elsewhere so we can get prices etc
	if( $all_options ) {
		foreach( $all_options as $option_id=>$option ) {
			$hidden_option_id = $option_id . '_hidden';
			printf(
				'<input type="hidden" id="%s" data-option-cost="%s" value="%s" data-option-percentage="%s">',
				$hidden_option_id,
				esc_attr( $option['option_cost'] ),
				esc_attr( $option['option_value'] ),
				esc_attr( $option['option_percentage'] )
			);
		}
	}

	echo $close_td;

}
