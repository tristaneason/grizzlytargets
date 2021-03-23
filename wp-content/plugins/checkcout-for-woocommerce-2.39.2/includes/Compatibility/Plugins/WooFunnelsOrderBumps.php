<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooFunnelsOrderBumps extends Base {
	public function is_available() {
		return function_exists( 'WFOB_Core' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'WooFunnelsOrderBumps',
			'params' => [],
		];

		return $compatibility;
	}
}