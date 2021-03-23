<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class JupiterXCore extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return class_exists( '\\JupiterX_Core' );
	}

	/**
	 * Add JupiterX compiled styles to list of blocked style handles.
	 *
	 * @param $styles
	 *
	 * @return mixed
	 */
	function remove_styles( $styles ) {
		global $wp_styles;

		foreach ( $wp_styles->registered as $wp_style ) {
			if ( ! empty($wp_style->src) && stripos( $wp_style->src, 'compiler/jupiterx' ) !== false ) {
				$styles[] = $wp_style->handle;
			}
		}

		return $styles;
	}
}
