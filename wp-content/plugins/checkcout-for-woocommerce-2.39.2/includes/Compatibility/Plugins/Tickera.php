<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Tickera extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		global $tc_woocommerce_bridge;

		return ( ! empty( $tc_woocommerce_bridge ) );
	}

	public function run() {
		global $tc_woocommerce_bridge;

		add_action( 'cfw_checkout_before_payment_method_terms_checkbox', array( $tc_woocommerce_bridge, 'add_standard_tc_fields_to_checkout' ) );
	}
}
