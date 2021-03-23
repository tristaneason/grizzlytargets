<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Square1x extends Base {
	public function is_available() {
		return class_exists( '\\Woocommerce_Square' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'Square1x',
			'params' => [],
		];

		return $compatibility;
	}
}