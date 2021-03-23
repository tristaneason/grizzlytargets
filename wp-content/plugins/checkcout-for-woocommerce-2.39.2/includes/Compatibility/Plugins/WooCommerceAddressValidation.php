<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooCommerceAddressValidation extends Base {
	public function is_available() {
		if ( function_exists( 'wc_address_validation' ) ) {
			return wc_address_validation()->get_handler_instance()->get_active_provider()->id == 'smartystreets';
		}
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'WooCommerceAddressValidation',
			'params' => [],
		];

		return $compatibility;
	}
}