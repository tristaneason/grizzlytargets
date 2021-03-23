<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Square extends Base {
	public function is_available() {
		return class_exists( '\\WooCommerce_Square_Loader' );
	}

	public function run() {
		add_action( 'cfw_payment_tab_before_content', array($this, 'reorder_payment_tab') );
	}

	function reorder_payment_tab() {
		remove_action( 'cfw_payment_tab_content', 'cfw_payment_methods', 10 );
		remove_action( 'cfw_payment_tab_content', 'cfw_payment_tab_content_billing_address', 20 );

		add_action( 'cfw_payment_tab_content', 'cfw_payment_tab_content_billing_address', 10 );
		add_action( 'cfw_payment_tab_content', 'cfw_payment_methods', 20 );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'Square',
			'params' => [],
		];

		return $compatibility;
	}
}