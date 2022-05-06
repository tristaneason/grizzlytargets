<?php

global $porto_settings, $porto_post_image_size;

if ( $porto_post_image_size ) {
	$image_size = $porto_post_image_size;
} else {
	$image_size = isset( $atts['image_size'] ) ? $atts['image_size'] : 'full';
}

$image_id    = false;
$image_link  = '';
$link_target = '';

if ( isset( $atts['add_link'] ) && 'custom' == $atts['add_link'] && ! empty( $atts['custom_url'] ) ) {
	$image_link = $atts['custom_url'];
	if ( isset( $atts['link_target'] ) ) {
		$link_target = $atts['link_target'];
	}
}

if ( ( $current_object = get_queried_object() ) && $current_object->term_id ) {
	$image_id = get_term_meta( $current_object->term_id, 'thumbnail_id', true );
	if ( ! $image_link && 'no' != $atts['add_link'] ) {
		$image_link = get_term_link( $current_object );
	}
} else {
	$featured_images = porto_get_featured_images();
	if ( count( $featured_images ) ) {
		$image_id = $featured_images[0]['attachment_id'];
		if ( ! $image_link && 'no' != $atts['add_link'] ) {
			$image_link = get_permalink();
		}
	}
}

if ( ! $image_id ) {
	return;
}

$wrap_cls   = 'porto-tb-featured-image' . ( ! empty( $atts['image_type'] ) ? ' tb-image-type-' . $atts['image_type'] : '' );
$wrap_attrs = '';
// image types
$attachment_ids = array();
if ( 'hover' == $atts['image_type'] || 'slider' == $atts['image_type'] || 'gallery' == $atts['image_type'] ) {
	global $product;
	if ( $product ) {
		$attachment_ids = $product->get_gallery_image_ids();
		if ( ! empty( $attachment_ids ) ) {
			$attachment_ids = array_unshift( $attachment_ids, $image_id );
		}
	}
	if ( empty( $attachment_ids ) ) {
		$attachment_ids = porto_get_featured_images();
	}

	if ( count( $attachment_ids ) > 1 ) {
		if ( 'slider' == $atts['image_type'] ) {
			$wrap_cls   .= ' porto-carousel owl-carousel nav-inside nav-inside-center nav-style-2 show-nav-hover has-ccols ccols-1';
			$wrap_attrs .= " data-plugin-options='" . json_encode( array( 'nav' => true ) ) . "'";
		} elseif ( 'gallery' == $atts['image_type'] ) {
			$wrap_cls   .= ' has-ccols ccols-2 ccols-md-3 lightbox';
			$wrap_attrs .= " data-plugin-options='" . json_encode(
				array(
					'delegate'  => 'a',
					'type'      => 'image',
					'gallery'   => array( 'enabled' => true ),
					'mainClass' => 'mfp-with-zoom',
					'zoom'      => array(
						'enabled'  => true,
						'duration' => 300,
					),
				)
			) . "'";
			if ( empty( $atts['image_size'] ) ) {
				$image_size = ! empty( $porto_settings['enable-portfolio'] ) ? 'portfolio-grid' : 'blog-medium';
			}
		}
	}
} elseif ( 'video' == $atts['image_type'] ) {
	$video_html = '';

	global $product;
	if ( $product ) {
		$ids = get_post_meta( get_the_ID(), 'porto_product_video_thumbnails' );
		if ( ! empty( $ids ) ) {
			$url    = wp_get_attachment_url( $ids[0] );
			$poster = get_the_post_thumbnail_url( $ids[0] );
			if ( ! $poster ) {
				$poster = wp_get_attachment_image_url( $image_id, 'full' );
			}
			$video_html .= do_shortcode( '[video src="' . esc_url( $url ) . '" poster="' . esc_url( $poster ) . '"]' );
		} else {
			// with video thumbnail shortcode
			$video_code = get_post_meta( get_the_ID(), 'porto_product_video_thumbnail_shortcode', true );
			if ( false !== strpos( $video_code, '[video ' ) ) {
				preg_match( '/poster="([^\"]*)"/', $video_code, $poster );
				$poster      = empty( $poster ) ? wp_get_attachment_image_url( $image_id, 'full' ) : $poster[1];
				$video_html .= do_shortcode( preg_replace( '/poster="([^\"]*)"/', 'poster="' . esc_url( $poster ) . '"', $video_code ) );
			} else {
				$youtube_id = preg_match( '/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/', $video_code, $matches );
				if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
					$youtube_id = $matches[1];
				} else {
					$youtube_id = '';
				}
				if ( $youtube_id ) {
					$video_html .= '<div id="ytplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-youtube" data-video="' . esc_attr( $youtube_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
				} else {
					$vimeo_id = preg_match( '/^(?:https?:\/\/)?(?:www|player\.)?(?:vimeo\.com\/)?(?:video\/|external\/)?(\d+)([^.?&#"\'>]?)/', $video_code, $matches );
					if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
						$vimeo_id = $matches[1];
					} else {
						$vimeo_id = '';
					}
					if ( $vimeo_id ) {
						$video_html .= '<div id="vmplayer_' . porto_generate_rand( 4 ) . '" class="porto-video-social video-vimeo" data-video="' . esc_attr( $vimeo_id ) . '" data-loop="0" data-audio="0" data-controls="1"></div>';
					}
				}
			}
		}
	} else {
		$video_html .= do_shortcode( get_post_meta( get_the_ID(), 'video_code', true ) );
	}
}

echo '<div class="' . esc_attr( $wrap_cls ) . '"' . $wrap_attrs . '>';

if ( count( $attachment_ids ) > 1 && ( 'slider' == $atts['image_type'] || 'gallery' == $atts['image_type'] ) ) {
	foreach ( $attachment_ids as $img_id ) {
		$attachment = porto_get_attachment( is_array( $img_id ) ? $img_id['attachment_id'] : $img_id );
		if ( ! $attachment ) {
			continue;
		}

		if ( 'gallery' == $atts['image_type'] ) {
			echo '<a href="' . esc_url_raw( $attachment['src'] ) . '">';
		} elseif ( $image_link ) {
			echo '<a href="' . esc_url_raw( $image_link ) . '"' . ( $link_target ? ' target="' . esc_attr( $link_target ) . '"' : '' ) . '>';
		}
		echo '<div class="img-thumbnail">';
		echo wp_get_attachment_image( is_array( $img_id ) ? $img_id['attachment_id'] : $img_id, $image_size, false, array( 'class' => 'img-responsive' ) );
		if ( $porto_settings['post-zoom'] ) {
			echo '<span class="zoom" data-src="' . esc_url( $attachment['src'] ) . '" data-title="' . esc_attr( $attachment['caption'] ) . '"><i class="fas fa-search"></i></span>';
		}
		echo '</div>';
		if ( 'gallery' == $atts['image_type'] || $image_link ) {
			echo '</a>';
		}
	}
} else {

	if ( $image_link && ! $video_html ) {
		echo '<a href="' . esc_url_raw( $image_link ) . '"' . ( $link_target ? ' target="' . esc_attr( $link_target ) . '"' : '' ) . '>';
	}

	if ( ! empty( $video_html ) ) {
		wp_enqueue_script( 'jquery-fitvids' );
		echo '<div class="img-thumbnail fit-video">';
		echo porto_filter_output( $video_html );
		echo '</div>';
	} else {
		echo wp_get_attachment_image( $image_id, $image_size, false, array( 'class' => 'img-responsive' ) );
	}

	if ( 'hover' == $atts['image_type'] && count( $attachment_ids ) > 1 ) {
		echo wp_get_attachment_image( is_array( $attachment_ids[1] ) ? $attachment_ids[1]['attachment_id'] : $attachment_ids[1], $image_size, false, array( 'class' => 'img-responsive hover-image' ) );
	}

	if ( $image_link && ! $video_html ) {
		echo '</a>';
	}
}

echo '</div>';
