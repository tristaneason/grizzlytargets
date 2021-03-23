<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class The7 extends Base {
	function is_available() {
		return function_exists( 'presscore_enqueue_dynamic_stylesheets' );
	}

	function run() {
		remove_action( 'wp_enqueue_scripts', 'presscore_enqueue_dynamic_stylesheets', 20 );
	}
}