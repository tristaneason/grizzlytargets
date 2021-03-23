<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class Atelier extends Base {
	public function is_available() {
		return function_exists( 'sf_custom_styles' );
	}

	public function run() {
		$this->wp();
	}

	function wp() {
		if ( Main::is_checkout() ) {
			remove_action( 'wp_head', 'sf_custom_styles' );
		}
	}
}