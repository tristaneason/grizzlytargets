<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class PixelCaffeine extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		if ( class_exists( '\\PixelCaffeine' ) && class_exists( '\\AEPC_Pixel_Scripts' ) && class_exists( '\\AEPC_Woocommerce_Addon_Support' ) ) {
			return true;
		}

		return false;
	}

	function run() {
		add_filter( 'cfw_body_classes', array( $this, 'add_pixel_caffeine_body_class' ) );
	}

	function add_pixel_caffeine_body_class( $classes ) {
		$classes[] = 'woocommerce-page';

		return $classes;
	}
}
