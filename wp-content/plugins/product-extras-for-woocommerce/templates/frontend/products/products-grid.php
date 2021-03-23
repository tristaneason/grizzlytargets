<?php
/**
 * A products field template for a variations grid
 * @since 3.7.21
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
	'grid-layout',
	'child-product-wrapper',
	'products-quantities-independent'
);
$manage_stock = false; ?>

<div class="<?php echo join( ' ', $checkboxes_wrapper_classes ); ?>" data-products-quantities="independent">

	<?php
	// We can only use one child product
	if( isset( $item['child_products'][0] ) ) {

		$child_product_id = $item['child_products'][0];

		$child_product = wc_get_product( $child_product_id );
		if( ! is_object( $child_product ) || get_post_status( $child_product_id ) != 'publish' ) {
			return;
		}

		$product_type = $child_product->get_type();

		// We can only use variable products
		if( $product_type != 'variable' && $product_type != 'variable-subscription' ) {

			printf(
				'<p>%s</p>',
				__( 'Child product needs to be variable', 'pewc' )
			);
			return;

		}

		// We need two attributes in order to make a grid
		$attributes = $child_product->get_variation_attributes();
		if( count( $attributes ) != 2 ) {

			printf(
				'<p>%s</p>',
				__( 'Child product needs to have two attributes', 'pewc' )
			);
			return;

		}

		$variant_wrapper = '';
		$wrapper_classes = array(
			'pewc-checkbox-image-wrapper',
			'pewc-child-variation-main'
		);

		$available_variations = $child_product->get_available_variations();

		$h_attribute_name = 'attribute_' . array_key_first( $attributes );
		$v_attribute_name = 'attribute_' . array_key_last( $attributes );
		$h_attributes = current( $attributes );
		$v_attributes = end( $attributes );

		$product_data_store = new WC_Product_Data_Store_CPT();

		echo '<table>';

			echo '<thead><tr>';

				echo '<th>&nbsp;</th>';

				foreach( $v_attributes as $term ) {

					$term_obj = get_term_by( 'slug', $term, array_key_last( $attributes ) );
					if ( ! is_wp_error( $term_obj ) && isset( $term_obj->name ) ) {
						$name = $term_obj->name;
					} else {
						$name = $term;
					}

					printf(
						'<th>%s</th>',
						$name
					);

				}

			echo '</tr></thead>';

			echo '<tbody>';

				foreach( $h_attributes as $h_term ) {

					$h_term_obj = get_term_by( 'slug', $h_term, array_key_first( $attributes ) );
					if ( ! is_wp_error( $h_term_obj ) && isset( $h_term_obj->name ) ) {
						$h_slug = $h_term_obj->slug;
						$name = $h_term_obj->name;
					} else {
						$h_slug = $h_term;
						$name = $h_term;
					}

					echo '<tr>';

						printf(
							'<th>%s</th>',
							$name
						);

						foreach( $v_attributes as $v_term ) {

							// Iterate through each term in the first attribute
							// Find an available variation that includes the term
							$v_term_obj = get_term_by( 'slug', $v_term, array_key_last( $attributes ) );
							if ( ! is_wp_error( $v_term_obj ) && isset( $v_term_obj->name ) ) {
								$v_slug = $v_term_obj->slug;
							} else {
								$v_slug = $v_term_obj;
							}

							$cell_attributes = array(
								'attribute_' . array_key_first( $attributes ) => $h_slug,
								'attribute_' . array_key_last( $attributes ) => $v_slug
							);

							$variation_id = $product_data_store->find_matching_product_variation( $child_product, $cell_attributes );
							$variation_obj = wc_get_product( $variation_id );

							if( ! is_object( $variation_obj ) || is_wp_error( $variation_obj ) ) {
								continue;
							}

							$disabled = '';
							if( is_object( $variation_obj ) && ( ! $variation_obj->is_purchasable() || ! $variation_obj->is_in_stock() ) ) {
								$disabled = 'disabled';
							}

							$available_stock = '';
							if( is_object( $variation_obj ) && $variation_obj->managing_stock() ) {
								$manage_stock = true;
								$available_stock = $variation_obj->get_stock_quantity();
							}

							// Add a quantity field for each child checkbox
							// The name format is {$id}_child_quantity_{$child_product_id}
							// Where $id is the field ID and $child_product_id is the child product ID

							$child_name = $id . '_grid_child_variation';

							$quantity_field = sprintf(
								'<span class="pewc-quantity-wrapper"><input type="number" data-option-cost="%s" min="0" step="1" max="%s" class="pewc-grid-quantity-field" name="%s[%s]" value="0" %s></span>',
								// esc_attr( number_format( $variation_obj->get_price(), get_option( 'woocommerce_price_num_decimals', 2 ) ) ),
								esc_attr( $variation_obj->get_price() ),
								$available_stock,
								$child_name,
								$variation_id,
								// esc_attr( $id ) . '_child_variation_' . $variation_id,
								$disabled
							);

							printf (
								'<td>%s</td>',
								$quantity_field
							);

						}

					echo '</tr>';

				}

			echo '</tbody>';

		echo '</table>';

	} ?>

</div><!-- .pewc-radio-images-wrapper -->
