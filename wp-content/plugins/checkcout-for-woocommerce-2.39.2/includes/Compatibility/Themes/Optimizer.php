<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Optimizer extends Base {
	public function is_available() {
		return function_exists( 'optimizer_setup' );
	}

	public function run() {
		remove_action( 'wp_footer', 'optimizer_load_js' );
	}
}