<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class BraintreeForWooCommerce extends Base {
	public function is_available() {
		return defined( 'BFWC_PLUGIN_NAME' ) || defined( 'WC_BRAINTREE_PLUGIN_NAME' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'BraintreeForWooCommerce',
			'params' => [],
		];

		return $compatibility;
	}
}