<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class ToCheckout extends Base {
	public function is_available() {
		return function_exists('woocommerce_tocheckoutcw_init');
	}

	public function run() {
		$_POST['post_data'] = array();
	}
}