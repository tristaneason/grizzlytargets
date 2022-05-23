<?php
global $porto_settings;

$featured_images = porto_get_featured_images();
$image_count     = count( $featured_images );

if ( ! $image_count ) {
	return;
}
if ( ! isset( $carousel_options ) ) {
	$carousel_options = array();
}
$carousel_options['nav'] = true;
?>
	<div class="post-image<?php echo 1 == $image_count ? ' single' : ''; ?>">
		<div class="post-slideshow porto-carousel owl-carousel nav-inside nav-inside-center nav-style-2 show-nav-hover has-ccols ccols-1" data-plugin-options='<?php echo json_encode( $carousel_options ); ?>'>
			<?php
			foreach ( $featured_images as $featured_image ) {
				$attachment_large = porto_get_attachment( $featured_image['attachment_id'], ( isset( $image_size ) ? $image_size : 'blog-large' ) );
				$attachment       = porto_get_attachment( $featured_image['attachment_id'] );
				if ( ! $attachment ) {
					continue;
				}
				$placeholder = porto_generate_placeholder( $attachment_large['width'] . 'x' . $attachment_large['height'] );
				?>
				<?php if ( is_single() ) : ?>
				<div>
			<?php else : ?>
				<a href="<?php echo esc_url( apply_filters( 'the_permalink', get_permalink() ) ); ?>" aria-label="post image">
			<?php endif; ?>
					<div class="img-thumbnail">
						<?php echo wp_get_attachment_image( $featured_image['attachment_id'], ( isset( $image_size ) ? $image_size : 'blog-large' ), false, array( 'class' => 'owl-lazy img-responsive' ) ); ?>
						<?php if ( ! empty( $porto_settings['post-zoom'] ) ) { ?>
							<span class="zoom" data-src="<?php echo esc_url( $attachment['src'] ); ?>" data-title="<?php echo esc_attr( $attachment_large['caption'] ); ?>"><i class="fas fa-search"></i></span>
						<?php } ?>
					</div>
				<?php if ( is_single() ) : ?>
				</div>
			<?php else : ?>
				</a>
			<?php endif; ?>
			<?php } ?>
		</div>
		<?php if ( is_single() && isset( $porto_settings['post-share-position'] ) && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<?php get_template_part( 'views/posts/single/share' ); ?>
		<?php elseif ( ! is_single() && isset( $porto_settings['blog-post-share-position'] ) && 'advance' === $porto_settings['blog-post-share-position'] ) : ?>
			<div class="post-block post-share post-share-advance">
				<div class="post-share-advance-bg">
					<?php get_template_part( 'share' ); ?>
					<i class="fa fa-share-alt"></i>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( isset( $extra_html ) ) : ?>
			<?php // @codingStandardsIgnoreLine ?>
			<?php echo porto_filter_output( $extra_html ); ?>
		<?php endif; ?>
	</div>
