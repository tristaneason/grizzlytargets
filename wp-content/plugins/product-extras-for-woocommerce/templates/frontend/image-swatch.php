<?php
/**
 * The template for Image Swatches
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! pewc_is_pro() ) {
	return;
}

// echo pewc_field_label( $item, $id );

$input_type = ! empty( $item['allow_multiple'] ) ? 'checkbox' : 'radio';

echo $open_td;

if( isset( $item['field_options'] ) ) {
  $index = 0;

	$number_columns = ( isset( $item['number_columns'] ) ) ? $item['number_columns'] : 3;
	$radio_wrapper_classes = array(
		'pewc-radio-images-wrapper',

	);
	$radio_wrapper_classes[] = 'pewc-columns-' . intval( $number_columns );
	if( ! empty( $item['hide_labels'] ) ) {
		$radio_wrapper_classes[] = 'pewc-hide-labels';
	} ?>

	<div class="<?php echo join( ' ', $radio_wrapper_classes ); ?>">

  <?php if( ! empty( $item['field_options'] ) ) {
		foreach( $item['field_options'] as $key=>$option_value ) {

			$image = pewc_get_swatch_image_html( $option_value, $item );

			if( ! isset( $option_value['value'] ) ) {
				$option_value['value'] = '';
			}

			$name = wp_kses_post( $option_value['value'] );

			$option_price = pewc_get_option_price( $option_value, $item, $product );
			$option_percentage = '';

			$classes = array( 'pewc-radio-form-field' );

			// Check for percentages
			if( ! empty( $item['field_percentage'] ) && ! empty( $option_price ) ) {
				// Set the option price as a percentage of the product price
				$option_percentage = floatval( $option_price );
				$product_price = $product->get_price();
				$option_price = ( floatval( $option_price ) / 100 ) * $product_price;
				// Get display price according to inc tax / ex tax setting
				$option_price = pewc_maybe_include_tax( $product, $option_price );
				$classes[] = 'pewc-option-has-percentage';
				// $option_percentage = floatval( $item['field_price'] );
			}

			if( ! empty( $option_price ) && apply_filters( 'pewc_show_option_prices', true, $item ) ) {
				$name .= apply_filters( 'pewc_option_price_separator', '+', $item ) . pewc_get_semi_formatted_raw_price( $option_price );
				$name = apply_filters( 'pewc_option_name', $name, $item, $product, $option_price );
			}

			if( ! empty( $option_value['value'] ) ) {
				$radio_id = $id . '_' . strtolower( str_replace( ' ', '_', $option_value['value'] ) );
			} else {
				$radio_id = $id . '_' . $key;
			}

			$wrapper_classes = array(
				'pewc-radio-image-wrapper'
			);

			if( $input_type == 'checkbox' && ! is_array( $value ) ) {
				$value = explode( ' | ', $value );
			}
			if( is_array( $value ) ) {
				$checked = ( in_array( $option_value['value'], $value ) ) ? 'checked="checked"' : '';
			} else {
				$checked = $value == $option_value['value'] ? 'checked="checked"' : '';
			}
			if( $checked ) {
				$wrapper_classes[] = 'checked';
			}

	    $radio = sprintf(
	      '<div class="%s"><label for="%s"><input data-option-cost="%s" type="%s" name="%s[]" id="%s" class="%s" data-option-percentage="%s" value="%s" %s>%s<div class="pewc-radio-image-desc">%s</div></label></div>',
				join( ' ', $wrapper_classes ),
	      esc_attr( $radio_id ),
	      esc_attr( $option_price ),
				$input_type,
	      esc_attr( $id ),
	      esc_attr( $radio_id ),
				join( ' ', $classes ),
				esc_attr( $option_percentage ),
	      esc_attr( $option_value['value'] ),
				esc_attr( $checked ),
	      $image,
				$name
	    );

	    echo apply_filters( 'pewc_filter_image_swatch_field', $radio, $radio_id, $option_price, $id, $name, $key, $option_value, $item );

	  }

	} ?>

</div><!-- .pewc-radio-images-wrapper -->

<?php }

echo $close_td;
