<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class SendCloud extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'sendcloudshipping_add_service_point_to_checkout' );
	}

	public function run() {
		add_action( 'cfw_checkout_after_form', 'sendcloudshipping_add_service_point_to_checkout' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'SendCloud',
			'params' => [
				'notice' => cfw__( 'Please choose a service point.', 'sendcloud-shipping' ),
			],
		];

		return $compatibility;
	}
}
