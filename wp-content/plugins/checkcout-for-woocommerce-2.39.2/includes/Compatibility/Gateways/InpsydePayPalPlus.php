<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class InpsydePayPalPlus extends Base {
	public function is_available() {
		return class_exists( '\\WCPayPalPlus\\PayPalPlus' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'InpsydePayPalPlus',
			'params' => [],
		];

		return $compatibility;
	}
}