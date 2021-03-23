<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class EUVATNumber extends Base {
	public function is_available() {
		return class_exists( '\\WC_EU_VAT_Number' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'EUVatNumber',
			'params' => [],
		];

		return $compatibility;
	}
}
