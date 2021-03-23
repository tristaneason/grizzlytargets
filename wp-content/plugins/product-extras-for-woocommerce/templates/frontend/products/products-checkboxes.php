<?php
/**
 * A products field template for the checkboxes layout
 * @since 2.2.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$number_columns = ( isset( $item['number_columns'] ) ) ? $item['number_columns'] : 3;
$checkboxes_wrapper_classes = array(
	'pewc-checkboxes-images-wrapper',
	'child-product-wrapper'
);
$checkboxes_wrapper_classes[] = 'pewc-columns-' . intval( $number_columns );
if( ! empty( $item['hide_labels'] ) ) {
	$checkboxes_wrapper_classes[] = 'pewc-hide-labels';
}

if( ! empty( $item['products_quantities'] ) ) {
	$products_quantities = ! empty( $item['products_quantities'] ) ? $item['products_quantities'] : '';
	$checkboxes_wrapper_classes[] = 'products-quantities-' . $item['products_quantities'];
} ?>

<div class="<?php echo join( ' ', $checkboxes_wrapper_classes ); ?>" data-products-quantities="<?php echo esc_attr( $item['products_quantities'] ); ?>">

<?php if( $item['child_products'] ) {

	foreach( $item['child_products'] as $child_product_id ) {

		$child_product = wc_get_product( $child_product_id );
		if( ! is_object( $child_product ) || get_post_status( $child_product_id ) != 'publish' ) {
			continue;
		}

		$child_price = pewc_maybe_include_tax( $child_product, $child_product->get_price() );
		if( ! empty( $item['child_discount'] ) && ! empty( $item['discount_type'] ) ) {
			$discounted_price = pewc_get_discounted_child_price( $child_price, $item['child_discount'], $item['discount_type'] );
			$price = wc_format_sale_price( $child_price, $discounted_price );
			$option_cost = $discounted_price;
		} else {
			$price = $child_product->get_price_html();
			$option_cost = $child_price;
		}

		// Check stock availability
		$disabled = '';
		if( ! $child_product->is_purchasable() || ( ! $child_product->is_in_stock() && ! $child_product->backorders_allowed() ) ) {
			$disabled = 'disabled';
		}
		// Check available stock if stock is managed
		$available_stock = '';
		$max = '';
		if( $child_product->managing_stock() ) {
			$available_stock = $child_product->get_stock_quantity();
			if( $available_stock > 0 ) {
				$max = ' max="' . $available_stock . '"';
			}
		}

		$image_url = ( get_post_thumbnail_id( $child_product_id ) ) ? wp_get_attachment_url( get_post_thumbnail_id( $child_product_id ) ) : wc_placeholder_img_src();
		$image = '<img src="' . esc_url( $image_url ) . '">';

	  $name = apply_filters( 'pewc_child_product_title', get_the_title( $child_product_id ), $child_product ) . apply_filters( 'pewc_option_price_separator', '+', $item ) . $price;

		$field_name = $id . '_child_product';

		$checkbox_id = $id . '_' . $child_product_id;

		$wrapper_classes = array(
			'pewc-checkbox-image-wrapper'
		);
		if( $disabled ) {
			$wrapper_classes[] = 'pewc-checkbox-disabled';
		}

		$checked = ( $value == $id || ( is_array( $value ) && in_array( $child_product_id, $value ) ) ) ? 'checked="checked"' : '';

		$quantity_field = '';
		$quantity_field_value = 0;

		// Look for child quantity when we're editing a product
		if( ! empty( $cart_item['product_extras']['products']['child_products'][$child_product_id]['quantity'] ) ) {
			// If we're editing a product, this sets the quantity
			$quantity_field_value = $cart_item['product_extras']['products']['child_products'][$child_product_id]['quantity'];
		}
		$quantity_field_value = apply_filters( 'pewc_child_product_independent_quantity', $quantity_field_value, $child_product_id, $item );
		if( $quantity_field_value > 0 ) {
			$checked = 'checked="checked"';
		}

		if( $products_quantities == 'independent' ) {
			// Add a quantity field for each child checkbox
			// The name format is {$id}_child_quantity_{$child_product_id}
			// Where $id is the field ID and $child_product_id is the child product ID
			$quantity_field = sprintf(
				'<input type="number" min="0" step="1" %s class="pewc-form-field pewc-child-quantity-field" name="%s" value="%s" %s>',
				$max,
				esc_attr( $id ) . '_child_quantity_' . esc_attr( $child_product_id ),
				$quantity_field_value,
				$disabled
			);
		}

		// $option_cost = pewc_maybe_include_tax( $child_product, $child_product->get_price() );

	  $checkbox = sprintf(
	    '<div class="%s"><label for="%s"><input data-option-cost="%s" data-field-label="%s" type="checkbox" name="%s[]" id="%s" class="pewc-checkbox-form-field" value="%s" %s %s>%s</label><div class="pewc-checkbox-desc-wrapper">%s<div class="pewc-radio-image-desc">%s</div></div></div>',
			join( ' ', $wrapper_classes ),
	    esc_attr( $checkbox_id ),
	    esc_attr( $option_cost ),
			get_the_title( $child_product_id ),
	    esc_attr( $field_name ),
	    esc_attr( $checkbox_id ),
	    esc_attr( $child_product_id ),
			esc_attr( $checked ),
			esc_attr( $disabled ),
	    $image,
			$quantity_field,
			apply_filters( 'pewc_child_product_name', $name, $item, $available_stock, $child_product )
	  );
	  echo apply_filters( 'pewc_filter_checkbox', $checkbox, $child_product_id, $price, $id, $name, $item );

		do_action( 'pewc_after_child_product_item', $child_product, $child_product_id );
	}

} ?>

</div><!-- .pewc-radio-images-wrapper -->
