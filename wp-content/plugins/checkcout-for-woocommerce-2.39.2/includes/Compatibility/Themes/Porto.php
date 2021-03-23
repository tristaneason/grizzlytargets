<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Porto extends Base {
	function is_available() {
		return function_exists('porto_setup');
	}

	/**
	 * Add WP theme styles to list of blocked style handles.
	 *
	 * @param $scripts
	 *
	 * @return mixed
	 */
	function remove_theme_scripts( $scripts ) {
		global $wp_scripts;

		foreach ( $wp_scripts->registered as $wp_script ) {
			if ( ! empty($wp_script->src) && ( stripos( $wp_script->src, '/themes/' ) !== false || stripos( $wp_script->src, '/porto_styles/' ) !== false ) && stripos( $wp_script->src, '/checkout-wc/' ) === false ) {
				$scripts[] = $wp_script->handle;
			}
		}

		return $scripts;
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
			if ( ! empty($wp_style->src) && ( stripos( $wp_style->src, '/themes/' ) !== false || stripos( $wp_style->src, '/porto_styles/' ) !== false ) && stripos( $wp_style->src, '/checkout-wc/' ) === false ) {
				$styles[] = $wp_style->handle;
			}
		}

		return $styles;
	}
}