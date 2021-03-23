<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooCommercePriceBasedOnCountry extends Base {
	public function is_available() {
		return class_exists( '\\WC_Product_Price_Based_Country' );
	}

	public function run() {
		add_filter( 'cfw_needs_post_compatibility', '__return_true' );
	}
}