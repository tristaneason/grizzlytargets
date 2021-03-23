<?php
/**
 * Functions for child product quickviews
 * @since 3.8.6
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if QuickView is enabled
 * @since 3.8.6
 */
function pewc_child_product_quickview() {
	global $post;
	$quickview = get_option( 'pewc_child_product_quickview', 'no' );
	return apply_filters( 'pewc_child_product_quickview_enabled', $quickview, $post );
}

/**
 * Add a link to the product title if QuickView is enabled
 * @since 3.8.6
 */
function pewc_add_quickview_child_product( $name, $item, $available_stock, $child_product ) {
	if( pewc_child_product_quickview() == 'yes' ) {
		$child_product_id = $child_product->get_id();
		$name = sprintf(
			'<a href="#" class="pewc-show-quickview" data-child-product-id="%s">%s</a>',
			$child_product_id,
			$name
		);
	}
	return $name;
}
add_filter( 'pewc_child_product_name', 'pewc_add_quickview_child_product', 10, 4 );

/**
 * Add a class name
 * @since 3.8.6
 */
function pewc_add_quickview_field_class( $classes, $item ) {
	if( pewc_child_product_quickview() == 'yes' ) {
		$classes[] = 'pewc-has-quickview';
	}
	return $classes;
}
add_filter( 'pewc_filter_single_product_classes', 'pewc_add_quickview_field_class', 10, 2 );

/**
 * Build the QuickView template
 * @since 3.8.6
 */
function pewc_display_quickview_template( $child_product, $child_product_id ) {

	if( pewc_child_product_quickview() != 'yes' ) {
		return;
	}

	$original_post = $GLOBALS['post'];

	$GLOBALS['post'] = get_post( $child_product_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	setup_postdata( $GLOBALS['post'] );

	/**
	 * Hook: woocommerce_before_single_product.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 */
	remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );

	/**
	 * Hook: woocommerce_single_product_summary.
	 *
	 * @hooked woocommerce_template_single_title - 5
	 * @hooked woocommerce_template_single_rating - 10
	 * @hooked woocommerce_template_single_price - 10
	 * @hooked woocommerce_template_single_excerpt - 20
	 * @hooked woocommerce_template_single_add_to_cart - 30
	 * @hooked woocommerce_template_single_meta - 40
	 * @hooked woocommerce_template_single_sharing - 50
	 * @hooked WC_Structured_Data::generate_product_data() - 60
	 */
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

	/*
	* @hooked woocommerce_output_product_data_tabs - 10
	* @hooked woocommerce_upsell_display - 15
	* @hooked woocommerce_output_related_products - 20
	*/
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
 	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	?>
	<div id="pewc-quickview-<?php echo $child_product_id; ?>" class="pewc-quickview-product-wrapper">
		<?php wc_get_template_part( 'content', 'single-product' );
		printf(
			'<a href="#" class="pewc-close-quickview"><span>%s</span></a>',
			apply_filters( 'pewc_close_quickview_icon', '&times;' )
		); ?>
	</div>

	<?php
	$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

}
add_action( 'pewc_after_child_product_item', 'pewc_display_quickview_template', 10, 2 );

/**
 * Add the background
 * @since 3.8.6
 */
function pewc_display_quickview_background() {
	if( pewc_child_product_quickview() == 'yes' ) { ?>
		<div id="pewc-quickview-background"></div>
	<?php }
}
add_action( 'wp_footer', 'pewc_display_quickview_background' );
