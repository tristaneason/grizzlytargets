<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class OxygenBuilder extends Base {
	public function is_available() {
		return defined( 'CT_VERSION' );
	}

	public function run() {
		remove_action( 'wp_head', 'oxy_print_cached_css', 999999 );
	}
}