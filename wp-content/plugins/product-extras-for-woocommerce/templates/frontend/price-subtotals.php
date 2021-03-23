<?php
/**
 * The subtotals that appear on a product single page
 * @since 2.4.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<div class="pewc-total-field-wrapper">';

	$product_label = get_option( 'pewc_product_total_label', __( 'Product total', 'pewc' ) );
	printf(
		'<p><span id="pewc-per-product-label">%s</span>',
		apply_filters( 'pewc_filter_single_product_total', $product_label, $product )
	);
	echo '<span id="pewc-per-product-total" class="pewc-total-field"></span></p>';

	// Options total
	$options_label = get_option( 'pewc_options_total_label', __( 'Options total', 'pewc' ) );
	printf(
		'<p><span id="pewc-options-total-label">%s</span>',
		apply_filters( 'pewc_filter_single_product_options_label', $options_label, $product )
	);
	echo '<span id="pewc-options-total" class="pewc-total-field"></span></p>';

	if( pewc_has_flat_rate_field( $post_id ) ) {
		// Flat rate
		$flatrate_label = get_option( 'pewc_flatrate_total_label', __( 'Flat rate total', 'pewc' ) );
		printf(
			'<p><span id="pewc-flat-rate-total-label">%s</span>',
			apply_filters( 'pewc_filter_single_product_flat_rate_total', $flatrate_label, $product )
		);
		echo '<span id="pewc-flat-rate-total" class="pewc-total-field"></span></p>';
	}

	// Grand total
	$grand_label = get_option( 'pewc_grand_total_label', __( 'Grand total', 'pewc' ) );
	printf(
		'<p><span id="pewc-grand-total-label">%s</span>',
		apply_filters( 'pewc_filter_single_product_grand_total', $grand_label, $product )
	);
	echo '<span id="pewc-grand-total" class="pewc-total-field"></span></p>';

	do_action( 'pewc_after_price_subtotal_table', $product );

echo '</div><!-- .pewc-flat-rate-total-field-wrapper -->';
