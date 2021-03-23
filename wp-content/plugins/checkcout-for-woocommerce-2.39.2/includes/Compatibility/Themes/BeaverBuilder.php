<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class BeaverBuilder extends Base {
	public function is_available() {
		return class_exists( '\\FLThemeCompat' );
	}

	/**
	 * Add WP theme styles to list of blocked style handles.
	 *
	 * @param $styles
	 *
	 * @return mixed
	 */
	function remove_theme_styles( $styles ) {
		global $wp_styles;

		foreach ( $wp_styles->registered as $wp_style ) {
			if ( ! empty( $wp_style->src ) && ( stripos( $wp_style->src, '/themes/' ) !== false || stripos( $wp_style->src, '/bb-theme/' ) !== false ) && stripos( $wp_style->src, '/checkout-wc/' ) === false ) {
				$styles[] = $wp_style->handle;
			}
		}

		return $styles;
	}
}
