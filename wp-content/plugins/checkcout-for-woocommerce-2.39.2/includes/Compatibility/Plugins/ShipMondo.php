<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class ShipMondo extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'shipmondo_load_shipping_methods_init' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'ShipMondo',
			'params' => [
				'notice' => cfw__( 'Please select a pickup point before placing your order.', 'pakkelabels-for-woocommerce' ),
			],
		];

		return $compatibility;
	}
}
