<?php

/**
 * @package WP Product Review Feed Manager/Functions
 * @version 1.0.0
 * @since 2.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds custom fields to the products inventory card that can be used in the feeds.
 */
function wppfm_create_gtin_wc_support_field() {

	// Add the Brand field.
	woocommerce_wp_text_input(
		array(
			'id'          => 'wppfm_product_brand',
			'label'       => 'Product brand',
			'class'       => 'wppfm_product_brand',
			'desc_tip'    => true,
			'description' => __( 'Brand name of the product. If the product has no brand name you can use the manufacturer or supplier name.', 'wp-product-feed-manager' ),
		)
	);

	// Add the GTIN field.
	woocommerce_wp_text_input(
		array(
			'id'          => 'wppfm_product_gtin',
			'label'       => 'Product GTIN',
			'class'       => 'wppfm_product_gtin',
			'desc_tip'    => true,
			'description' => __( 'GTIN refers to a products Global Trade Item Number. You can also use a UPC, EAN, JAN, ISBN or ITF-14 number here.', 'wp-product-feed-manager' ),
		)
	);

	// Add the MPN field.
	woocommerce_wp_text_input(
		array(
			'id'          => 'wppfm_product_mpn',
			'label'       => 'Product MPN',
			'class'       => 'wppfm_product_mpn',
			'desc_tip'    => true,
			'description' => __( 'Add your product\'s Manufacturer Part Number (MPN).', 'wp-product-feed-manager' ),
		)
	);
}

add_action( 'woocommerce_product_options_inventory_product_data', 'wppfm_create_gtin_wc_support_field' );

/**
 * Saves the custom fields data.
 *
 * @param mixed     $post_id    Post ID of the product.
 */
function wppfm_save_custom_fields( $post_id ) {
	$product = wc_get_product( $post_id );

	// Get the custom fields data.
	$brand = isset( $_POST['wppfm_product_brand'] ) ? $_POST['wppfm_product_brand'] : '';
	$gtin  = isset( $_POST['wppfm_product_gtin'] ) ? $_POST['wppfm_product_gtin'] : '';
	$mpn   = isset( $_POST['wppfm_product_mpn'] ) ? $_POST['wppfm_product_mpn'] : '';

	// Save the custom fields data.
	$product->update_meta_data( 'wppfm_product_brand', sanitize_text_field( $brand ) );
	$product->update_meta_data( 'wppfm_product_gtin', sanitize_text_field( $gtin ) );
	$product->update_meta_data( 'wppfm_product_mpn', sanitize_text_field( $mpn ) );

	$product->save();
}

add_action( 'woocommerce_process_product_meta', 'wppfm_save_custom_fields' );

/**
 * Adds custom fields to the products inventory card of the product variations.
 *
 * @param   array   $loop
 * @param   object  $variation_data
 * @param   object  $variation
 */
function wppfm_create_mpn_wc_variation_support_field( $loop, $variation_data, $variation ) {

	echo '<div class="options_group form-row form-row-full">';

	// Add the MPN text field to the variation cards.
	woocommerce_wp_text_input(
		array(
			'id'          => 'wppfm_product_mpn[' . $variation->ID . ']',
			'label'       => __( 'Product MPN', 'wp-product-feed-manager' ),
			'desc_tip'    => true,
			'description' => __( 'Add your product\'s Manufacturer Part Number (MPN).', 'wp-product-feed-manager' ),
			'value'       => get_post_meta( $variation->ID, 'wppfm_product_mpn', true ),
		)
	);

	// Add the GTIN text field to the variation cards.
	woocommerce_wp_text_input(
		array(
			'id'          => 'wppfm_product_gtin[' . $variation->ID . ']',
			'label'       => 'Product GTIN',
			'desc_tip'    => true,
			'description' => __( 'GTIN refers to a products Global Trade Item Number. You can also use a UPC, EAN, JAN, ISBN or ITF-14 number here.', 'wp-product-feed-manager' ),
			'value'       => get_post_meta( $variation->ID, 'wppfm_product_gtin', true ),
		)
	);

	echo '</div>';
}

add_action( 'woocommerce_variation_options', 'wppfm_create_mpn_wc_variation_support_field', 10, 3 );

/**
 * Saves the custom fields data of the product variations.
 *
 * @param   int     $post_id
 */
function wppfm_save_variation_custom_fields( $post_id ) {

	// Get the variations mpn and gtin.
	$woocommerce_mpn_field = $_POST['wppfm_product_mpn'][ $post_id ];
	$woocommerce_gtin_field = $_POST['wppfm_product_gtin'][ $post_id ];

	// Update.
	update_post_meta( $post_id, 'wppfm_product_mpn', sanitize_text_field( $woocommerce_mpn_field ) );
	update_post_meta( $post_id, 'wppfm_product_gtin', sanitize_text_field( $woocommerce_gtin_field ) );
}

add_action( 'woocommerce_save_product_variation', 'wppfm_save_variation_custom_fields', 10, 2 );
