<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class GermanMarketplace extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return class_exists( '\\WGM_Template' );
	}

	function run_immediately() {
		add_filter( 'cfw_gateway_order_button_text', array( $this, 'override_gateway_order_button_text' ), 10, 2 );
		remove_filter( 'woocommerce_billing_fields', array( 'WGM_Template', 'billing_fields' ) );
		remove_filter( 'woocommerce_shipping_fields', array( 'WGM_Template', 'shipping_fields' ) );
	}

	function override_gateway_order_button_text( $button_text, $gateway ) {
		$button_text = \WGM_Template::change_order_button_text( $button_text );
		$button_text = apply_filters( 'woocommerce_de_buy_button_text_gateway_' . $gateway->id, $button_text, $gateway->order_button_text );

		return $button_text;
	}
}
