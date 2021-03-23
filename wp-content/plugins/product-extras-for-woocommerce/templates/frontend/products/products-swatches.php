<?php
/**
 * A products field template for the column layout
 * @since 2.6.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked pewc_enqueue_variations_scripts
 */
do_action( 'pewc_products_column_layout' );

$checkboxes_wrapper_classes = array(
	'pewc-swatches-wrapper',
	'child-product-wrapper'
);
$manage_stock = false;

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

			$variant_wrapper = '';
			$product_type = $child_product->get_type();

			$wrapper_classes = array(
				'pewc-checkbox-image-wrapper',
				'pewc-child-variation-main',
				'pewc-swatches-' . $product_type
			);

			if( $product_type == 'variable' ) {

				$variants = $child_product->get_children();
				$default_variation_id = pewc_get_default_variation_id( $child_product );
				$default_variation = wc_get_product( $default_variation_id );

				$child_product_title = get_the_title( $child_product_id );
				$default_variation_title = str_replace( $child_product_title . ' &#8211; ', '', get_the_title( $default_variation_id ) );
				$main_sku = $default_variation->get_sku();

				$main_image_url = ( get_post_thumbnail_id( $default_variation_id ) ) ? wp_get_attachment_url( get_post_thumbnail_id( $default_variation_id, apply_filters( 'pewc_child_product_image_size', 'thumb' ) ) ) : wc_placeholder_img_src();
				$main_image = '<img src="' . esc_url( $main_image_url ) . '">';

				if( $variants ) {

					$available_variations = $child_product->get_available_variations();
					$variant_wrapper = '<div class="pewc-variable-child-swatches-wrapper"><div class="pewc-variable-swatches-viewer-wrapper">';

					foreach( $variants as $variant_id ) {

						$variant = wc_get_product( $variant_id );
						$variant_price = pewc_maybe_include_tax( $variant, $variant->get_price() );

						// Check stock availability
						$disabled = '';
						if( ! $variant->is_purchasable() || ! $variant->is_in_stock() ) {
							$disabled = 'disabled';
						}

						// Check available stock if stock is managed
						$available_stock = '';
						if( $variant->managing_stock() ) {
							$manage_stock = true;
							$available_stock = $variant->get_stock_quantity();
						}

						// Get the image
						$image_id = $variant->get_image_id();
						$image = wp_get_attachment_image(
							$image_id,
							apply_filters( 'pewc_variation_swatch_image_size', array( 50, 50, true ) )
						);

						if( ! $image ) $image = wc_placeholder_img( apply_filters( 'pewc_variation_swatch_image_size', array( 50, 50, true ) ) );
						$sku = $variant->get_sku();

						$viewer_image_url = ( get_post_thumbnail_id( $variant_id ) ) ? wp_get_attachment_url( get_post_thumbnail_id( $variant_id, apply_filters( 'pewc_child_product_image_size', 'thumb' ) ) ) : wc_placeholder_img_src();

						$variation_title = str_replace( $child_product_title . apply_filters( 'pewc_option_price_separator', '+', $item ), '', $variant->get_name() );

						if( ! $variant_price ) {
							$variant_price = 0;
						}

						// Write the option
						$variant_wrapper .= sprintf(
							'<div class="pewc-variation-swatch" data-option-cost="%s" data-stock="%s" data-variation-id="%s" data-disabled="%s" data-name="%s" data-sku="%s" data-viewer-image="%s"><a href="#">%s<span>%s</span></a></div>',
							esc_attr( number_format( $variant_price, get_option( 'woocommerce_price_num_decimals', 2 ) ) ),
							// get_post_meta( $variant_id, 'price', true ),
							esc_attr( $available_stock ),
							$variant_id,
							$disabled,
							$variation_title,
							$sku,
							$viewer_image_url,
							$image,
							$variation_title
						);
					}

					$variant_wrapper .= '</div>';

					$image = wp_get_attachment_image(
						$default_variation_id,
						apply_filters( 'pewc_variation_viewer_image_size', 'thumb' )
					);
					if( ! $image ) $image = wc_placeholder_img( apply_filters( 'pewc_variation_viewer_image_size', 'thumb' ) );

					$variant_wrapper .= sprintf(
						'<div class="pewc-swatch-viewer"><span class="pewc-viewer-thumb">%s</span><span class="pewc-viewer-title">%s</span></div>',
						$main_image,
						$default_variation_title
					);
					$variant_wrapper .= '</div>';

					$wrapper_classes[] = 'pewc-variable-child-product-wrapper';

				}

				$description = '<div class="pewc-column-description"></div>';

				$child_price = $child_product->get_price();

				if( ! empty( $item['child_discount'] ) && ! empty( $item['discount_type'] ) ) {

					$discounted_price = pewc_get_discounted_child_price( $child_price, $item['child_discount'], $item['discount_type'] );
					$price = wc_format_sale_price( $child_price, $discounted_price );
					$option_cost = pewc_maybe_include_tax( $child_product, $discounted_price );

				} else {

					$price = $child_product->get_price_html();
					$option_cost = pewc_maybe_include_tax( $child_product, $child_price );

				}

				$label = sprintf(
					'<h4 class="pewc-swatches-main-title"><span class="pewc-variation-name">%s</span> <span class="pewc-variation-sku">%s</span> <span class="pewc-variation-price">%s</span></h4>',
					$variation_title,
					$sku,
					$price
				);

				if( ! $option_cost ) $option_cost = 0;

				// Check stock availability
				$disabled = '';
				if( ! $child_product->is_purchasable() || ! $child_product->is_in_stock() ) {
					$disabled = 'disabled';
				}
				// Check available stock if stock is managed
				$available_stock = '';
				if( $child_product->managing_stock() ) {
					$manage_stock = true;
					$available_stock = $child_product->get_stock_quantity();
				}

				$parent_name = $id . '_parent_product';
				$child_name = $id . '_child_variation';

				$checkbox_id = $id . '_' . $child_product_id;

				if( $disabled ) {
					$wrapper_classes[] = 'pewc-checkbox-disabled';
				}

				$checked = ( $value == $id ) ? 'checked="checked"' : '';

				$quantity_field = '';
				if( $products_quantities == 'independent' ) {
					// Add a quantity field for each child checkbox
					// The name format is {$id}_child_quantity_{$child_product_id}
					// Where $id is the field ID and $child_product_id is the child product ID
					$quantity_field = sprintf(
						'<span class="pewc-quantity-wrapper"><input type="number" min="0" step="1" max="%s" class="pewc-form-field pewc-child-quantity-field" name="%s" value="0" %s></span>',
						$available_stock,
						esc_attr( $id ) . '_child_quantity_' . esc_attr( $child_product_id ),
						$disabled
					);
				}

				$checkbox_label = sprintf(
			    '<label for="%s"><input data-option-cost="%s" type="checkbox" name="%s[]" id="%s" class="pewc-checkbox-form-field pewc-swatch-form-field pewc-column-form-field" value="%s" %s %s>%s</label>',
			    esc_attr( $checkbox_id ),
			    esc_attr( number_format( $option_cost, get_option( 'woocommerce_price_num_decimals', 2 ) ) ),
			    esc_attr( $parent_name ),
			    esc_attr( $checkbox_id ),
			    esc_attr( $child_product_id ),
					esc_attr( $checked ),
					esc_attr( $disabled ),
					$label
			  );

				echo '<div class="pewc-swatches-child-product-outer">';

					// Each child product
					printf(
						'<div class="%s" data-manage-stock="%s" data-price="" data-quantity=""><span class="pewc-child-thumb">%s</span><span class="pewc-child-name">%s</span><span class="pewc-child-qty">%s</span></div>',
						join( ' ', $wrapper_classes ),
						$manage_stock,
						$main_image,
						$checkbox_label,
						$quantity_field
					);

					// If we've got variations, add a toggle
					printf(
						'<div class="pewc-swatches-toggle-wrapper"><a class="pewc-swatches-toggle" href="#">%s</a></div>',
						__( 'Toggle', 'pewc' )
					);

					// Then add our variations
					echo $variant_wrapper;

					printf(
						'<input type="hidden" name="%s_child_product" class="" value="%s">',
						$id,
						$child_product_id
					);

					printf(
						'<input type="hidden" name="%s[%s]" class="pewc-child-variant" value="">',
						$child_name,
						$child_product_id
					);

				echo '</div><!-- pewc-child-product-outer -->';

			} // End product_type

		}

	} ?>

</div><!-- .pewc-radio-images-wrapper -->
