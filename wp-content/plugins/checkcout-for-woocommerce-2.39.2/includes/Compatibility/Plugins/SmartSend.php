<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class SmartSend extends Base {
	public function is_available() {
		return function_exists( 'SS_SHIPPING_WC' );
	}

	public function run_immediately() {
		add_filter( 'cfw_needs_post_compatibility', '__return_true' );
	}
}