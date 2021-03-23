<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class SquareRecurring extends Base {
	public function is_available() {
		return class_exists( '\\WC_Square_Recurring_simple' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'SquareRecurring',
			'params' => [],
		];

		return $compatibility;
	}
}