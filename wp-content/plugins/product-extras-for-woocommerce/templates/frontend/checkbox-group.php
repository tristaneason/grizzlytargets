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

// Labels are used with the inputs
// echo pewc_field_label( $item, $id );

if( isset( $item['field_options'] ) ) {

  $index = 0;

	$checkboxes = '<ul class="pewc-checkbox-group-wrapper">';

	  foreach( $item['field_options'] as $key=>$option_value ) {

			$classes = array( 'pewc-checkbox-form-field' );
	    $name = esc_html( $option_value['value'] );
			$option_percentage = '';

			// Set price differently if percentage is enabled
			if( pewc_is_pro() && ! empty( $item['field_percentage'] ) && ! empty( $option_value['price'] ) ) {

				// Set the option price as a percentage of the product price
				$product_price = $product->get_price();
				$option_price = pewc_get_option_price( $option_value, $item, $product );
				$option_price = ( floatval( $option_price ) / 100 ) * $product_price;
				$option_price = pewc_maybe_include_tax( $product, $option_price );
				$option_percentage = floatval( $option_price );
				$classes[] = 'pewc-option-has-percentage';

			} else {

				$option_price = pewc_get_option_price( $option_value, $item, $product );

			}

	    if( ! empty( $option_price ) && apply_filters( 'pewc_show_option_prices', true, $item ) ) {
				$name .= apply_filters( 'pewc_option_price_separator', '+', $item ) . '<span class="pewc-option-cost-label">' . pewc_get_semi_formatted_raw_price( $option_price ) . '</span>';
				$name = apply_filters( 'pewc_option_name', $name, $item, $product, $option_price );
			}

	    $radio_id = $id . '_' . strtolower( str_replace( ' ', '_', $option_value['value'] ) );

			if( ! is_array( $value ) ) {
				$value = explode ( ' | ', $value );
			}

			$checked = ( is_array( $value ) && in_array( $option_value['value'], $value ) ) ? 'checked="checked"' : '';

	    $radio = sprintf(
	      '<li><label class="pewc-checkbox-form-label" for="%s"><input data-option-cost="%s" data-option-percentage="%s" type="checkbox" name="%s[]" id="%s" class="%s" value="%s" %s>&nbsp;%s</label></li>',
	      esc_attr( $radio_id ),
				esc_attr( $option_price ),
	      esc_attr( $option_percentage ),
	      esc_attr( $id ),
	      esc_attr( $radio_id ),
				join( ' ', $classes ),
	      esc_attr( $option_value['value'] ),
				esc_attr( $checked ),
	      $name
	    );

	    $checkboxes .= apply_filters( 'pewc_filter_checkbox_group_field', $radio, $radio_id, $option_price, $id, $name, $option_value, $item );

	  }

	$checkboxes .= '</ul>';

	echo $open_td;
	echo $checkboxes;
	echo $close_td;

}
