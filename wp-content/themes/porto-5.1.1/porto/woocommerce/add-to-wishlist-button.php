<?php
/**
 * Add to wishlist button template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.0
 */
global $product, $porto_settings;

$tag  = ( isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? 'span' : 'a';
$link = ( isset( $porto_settings['category-addlinks-convert'] ) && $porto_settings['category-addlinks-convert'] ) ? '' : 'href="' . esc_url( add_query_arg( 'add_to_wishlist', $product_id ) ) . '" rel="nofollow"';
?>

<<?php echo porto_filter_output( $tag ); ?> <?php echo porto_filter_output( $link ); ?> data-product-id="<?php echo porto_filter_output( $product_id ); ?>" data-product-type="<?php echo porto_filter_output( $product_type ); ?>" class="<?php echo porto_filter_output( $link_classes ); ?>" >
	<?php echo porto_filter_output( $icon ); ?>
	<?php echo porto_filter_output( $label ); ?>
</<?php echo porto_filter_output( $tag ); ?>>

<span class="ajax-loading"></span>
