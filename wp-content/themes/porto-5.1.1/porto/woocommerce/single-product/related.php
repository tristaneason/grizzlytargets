<?php
/**
 * Related Products
 *
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings, $porto_woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}
$related = wc_get_related_products( $product->get_id(), $porto_settings['product-related-count'] );
if ( sizeof( $related ) === 0 || ! $porto_settings['product-related'] ) {
	return;
}

$args = apply_filters(
	'woocommerce_related_products_args',
	array(
		'post_type'           => 'product',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
		'posts_per_page'      => $porto_settings['product-related-count'],
		'orderby'             => $orderby,
		'post__in'            => $related,
		'post__not_in'        => array( $product->get_id() ),
	)
);

$products = new WP_Query( $args );

$porto_woocommerce_loop['columns'] = isset( $porto_settings['product-related-cols'] ) ? $porto_settings['product-related-cols'] : $porto_settings['product-cols'];

if ( ! $porto_woocommerce_loop['columns'] ) {
	$porto_woocommerce_loop['columns'] = 4;
}

if ( $products->have_posts() ) : ?>
	<div class="related products">

		<h2 class="slider-title"><?php esc_html_e( 'Related Products', 'porto' ); ?></h2>

		<div class="slider-wrapper">

			<?php
			$porto_woocommerce_loop['view']       = 'products-slider';
			$porto_woocommerce_loop['navigation'] = false;
			$porto_woocommerce_loop['pagination'] = true;
			$porto_woocommerce_loop['el_class']   = 'show-dots-title-right';

			woocommerce_product_loop_start();
			?>

			<?php
			while ( $products->have_posts() ) :
				$products->the_post();
				?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; // end of the loop. ?>

			<?php
			woocommerce_product_loop_end();
			?>
		</div>

	</div>

	<?php
endif;

wp_reset_postdata();
