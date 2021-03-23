<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class PayPalCheckout extends Base {

	private $_CompatibilityManager;

	public function __construct( $CompatibilityManager ) {
		parent::__construct();

		$this->_CompatibilityManager = $CompatibilityManager;
	}

	public function is_available() {
		return ( function_exists( 'wc_gateway_ppec' ) && ! empty( wc_gateway_ppec()->settings ) && method_exists( wc_gateway_ppec()->settings, 'is_enabled' ) && wc_gateway_ppec()->settings->is_enabled() );
	}

	public function run() {
		add_filter( 'woocommerce_checkout_posted_data', array($this, 'set_billing_info_if_required'), 10, 1 );
		add_filter( 'cfw_is_checkout', array($this, 'is_checkout'), 10, 1 );
	}

	function is_checkout( $is_checkout ) {
		if ( wc_gateway_ppec()->checkout->has_active_session() ) {
			return false;
		}

		return $is_checkout;
	}

	function set_billing_info_if_required( $data ) {
		if ( $_POST['bill_to_different_address'] == "same_as_shipping" ) {
			foreach ( WC()->checkout()->get_checkout_fields( 'billing' ) as $key => $field ) {
				if ( $key == "billing_email" ) continue;
				$data[ $key ] = isset( $data[ 'shipping_' . substr( $key, 8 ) ] ) ? $data[ 'shipping_' . substr( $key, 8 ) ] : '';
			}
		}

		return $data;
	}

	function typescript_class_and_params( $compatibility ) {
		$settings = wc_gateway_ppec()->settings;

		if ( 'yes' == $settings->use_spb ) {
			$compatibility[] = [
				'class'  => 'PayPalCheckout',
				'params' => [],
			];
		}

		return $compatibility;
	}
}
