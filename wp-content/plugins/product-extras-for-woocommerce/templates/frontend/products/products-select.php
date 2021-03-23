<?php
/**
 * A products field template for the select layout
 * @since 2.2.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$child_product_wrapper_class = array( 'child-product-wrapper' );
if( ! empty( $item['products_quantities'] ) ) {
	$products_quantities = ! empty( $item['products_quantities'] ) ? $item['products_quantities'] : '';
	$child_product_wrapper_class[] = 'products-quantities-' . $item['products_quantities'];
} ?>

<div class="<?php echo join( ' ', $child_product_wrapper_class ); ?>" data-products-quantities="<?php echo esc_attr( $item['products_quantities'] ); ?>">

	<select class="pewc-form-field pewc-child-select-field" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>_child_product">

	<?php if( ! empty( $item['select_placeholder'] ) ) {
		// Add the placeholder instruction text
		echo '<option value="">' . esc_html( $item['select_placeholder'] ) . '</option>';
	}

	if( $item['child_products'] ) {

		foreach( $item['child_products'] as $child_product_id ) {

			$child_product = wc_get_product( $child_product_id );
			if( ! is_object( $child_product ) || get_post_status( $child_product_id ) != 'publish' ) {
				continue;
			}

			$child_price = pewc_maybe_include_tax( $child_product, $child_product->get_price() );

			if( ! empty( $item['child_discount'] ) && ! empty( $item['discount_type'] ) ) {

				$price = pewc_get_discounted_child_price( $child_price, $item['child_discount'], $item['discount_type'] );
				// $price = wc_format_sale_price( $child_price, $discounted_price );
				$option_cost = $price;

			} else {

				$price = $child_product->get_price();
				$option_cost = $price;

			}
			// $price = wc_price( $price );

			$disabled = '';
			if( ! $child_product->is_purchasable() || ( ! $child_product->is_in_stock() && ! $child_product->backorders_allowed() ) ) {
				$disabled = 'disabled';
			}

			// Check available stock if stock is managed
			$available_stock = '';
			if( $child_product->managing_stock() ) {
				$available_stock = $child_product->get_stock_quantity();
			}

			$selected = ( $value == $child_product_id || ( is_array( $value ) && in_array( $child_product_id, $value ) ) ) ? 'selected' : '';
			// $price = pewc_get_semi_formatted_price( $child_product );

			$name = get_the_title( $child_product_id );

			// Include prices in option labels
			if( apply_filters( 'pewc_show_option_prices', true, $item ) ) {
				$name .= apply_filters( 'pewc_option_price_separator', '+', $item ) . pewc_get_semi_formatted_raw_price( $child_price );
			}

			// $option_cost = pewc_maybe_include_tax( $child_product, $child_product->get_price() );
			printf(
				'<option data-option-cost="%s" %s %s data-field-value="%s" value="%s" data-stock="%s">%s</option>',
				esc_attr( $option_cost ),
				$disabled,
				$selected,
				esc_attr( get_the_title( $child_product_id ) ),
				esc_attr( $child_product_id ),
				esc_attr( $available_stock ),
				$name
			);

		}

	} ?>
	</select>

	<?php if( $products_quantities == 'independent' ) {

		pewc_child_product_independent_quantity_field( $quantity_field_values, $child_product_id, $id );

	} ?>

</div><!-- .child-product-wrapper -->
