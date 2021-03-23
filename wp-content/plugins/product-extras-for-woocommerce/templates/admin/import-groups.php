<?php
/**
 * The markup for importing groups from another product
 * Deprecated in 2.1.0
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="options_group">
	<p class="form-field">
		<label for="pewc_import_groups"><?php _e( 'Import groups from', 'woocommerce' ); ?></label>
		<select class="wc-product-search pewc-import-groups" style="width: 50%;" id="pewc_import_groups" name="pewc_import_groups" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'pewc' ); ?>">
			<?php
			global $post;
			$args = array(
				'post_type'			=> 'product',
				'posts_per_page'	=> 99,
				'post__not_in'		=> array( $post->ID ),
				'fields'			=> 'ids'
			);
			$products = new WP_Query( $args );
			if( $products ) {
				foreach( $products->posts as $product_id ) {
					$product = wc_get_product( $product_id );
					$groups = pewc_get_extra_fields( $product_id );
					if ( is_object( $product ) && ! empty( $groups ) ) {
						echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
					}
				}
			} ?>
		</select>
		<?php echo wc_help_tip( __( 'Imported groups will appear after any groups already registered to this product.', 'pewc' ) ); ?>
	</p>

	<p class="form-field">
		<?php wp_nonce_field( 'pewc_import_nonce', 'pewc_import_nonce' ); ?>
		<a href="#" class="button import_groups"><?php _e( 'Import Groups', 'pewc' ); ?></a>
	</p>

</div>
