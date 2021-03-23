<?php
/**
 * The markup for variations
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="product-extra-variation product-extra-variation-row">
	<?php printf(
		'<p>%s</p>',
		__( 'If you only want to show this field for certain variations, enter the variation IDs below. Leave empty to show for all variations.', 'pewc' )
	);
	if( ! empty( $variations ) ) { ?>
		<div class="product-extra-variation-row product-extra-variation-rule">
			<select class="pewc-variation-field pewc-variation-select" name="_product_extra_groups_<?php echo esc_attr( $group_key ); ?>_<?php echo esc_attr( $item_key ); ?>[variation_field][]" id="_product_extra_groups<?php echo esc_attr( $group_key ); ?>_<?php echo esc_attr( $item_key ); ?>_variation_field" data-group-id="<?php echo esc_attr( $group_key ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>" multiple>
			<?php
				foreach( $variations as $variation_id ) {
					$selected = '';
					if( isset( $item['variation_field'] ) && in_array( $variation_id, $item['variation_field'] ) ) {
						$selected = 'selected';
					}
					$variation = wc_get_product( $variation_id );
					$variation_name = $variation->get_name();
					echo '<option ' . $selected . ' value="' . esc_attr( $variation_id ) . '">' . esc_html( $variation_name ) . ' #' . esc_html( $variation_id ) . '</option>';
				} ?>
			</select>

		</div><!-- .product-extra-conditional-row -->
	<?php
	} ?>
</div>
