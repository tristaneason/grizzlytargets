<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Zidane extends Base {
	function is_available() {
		return function_exists( 'zidane_framework' );
	}

	function run() {
		$Zidane_Framework = zidane_framework();

		remove_action( 'wp_footer', array( $Zidane_Framework, 'init_javascript' ) );
	}
}