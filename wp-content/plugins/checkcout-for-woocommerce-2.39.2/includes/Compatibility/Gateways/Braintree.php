<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Braintree extends Base {

	/**
	 * @var array
	 * @private
	 */
	private $braintree_gateways_available;

	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		$available = false;
		if ( function_exists( 'wc_braintree' ) ) {
			$braintree      = wc_braintree();
			$cc_gateway     = $braintree->get_gateway( \WC_Braintree::CREDIT_CARD_GATEWAY_ID );
			$paypal_gateway = $braintree->get_gateway( \WC_Braintree::PAYPAL_GATEWAY_ID );

			$this->set_braintree_gateways_available(
				[
					'cc'     => $cc_gateway->is_available(),
					'paypal' => $paypal_gateway->is_available(),
				]
			);

			if ( $cc_gateway->is_available() || $paypal_gateway->is_available() ) {
				$available = true;
			}
		}

		return $available;
	}

	function typescript_class_and_params( $compatibility ) {
		$braintree_gateways_available = $this->get_braintree_gateways_available();

		$compatibility[] = [
			'class'  => 'Braintree',
			'params' => [
				'cc_gateway_available'     => $braintree_gateways_available['cc'],
				'paypal_gateway_available' => $braintree_gateways_available['paypal'],
			],
		];

		return $compatibility;
	}

	/**
	 * @return array
	 */
	public function get_braintree_gateways_available() {
		return $this->braintree_gateways_available;
	}

	/**
	 * @param array $braintree_gateways_available
	 */
	public function set_braintree_gateways_available( $braintree_gateways_available ) {
		$this->braintree_gateways_available = $braintree_gateways_available;
	}
}
