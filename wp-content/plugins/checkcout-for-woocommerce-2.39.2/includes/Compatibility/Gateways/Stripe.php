<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class Stripe extends Base {

	protected $stripe_request_button_height = '35';

	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		if ( class_exists( '\\WC_Stripe' ) && defined( 'WC_STRIPE_VERSION' ) ) {
			if ( version_compare( WC_STRIPE_VERSION, '4.0.0' ) >= 0 && version_compare( WC_STRIPE_VERSION, '5.0.0', '<' ) ) {
				return true;
			}
		}

		return false;
	}

	function pre_init() {
		// If this filter returns true, override the btn height settings in 2 places
		if ( apply_filters( 'cfw_stripe_compat_override_request_btn_height', '__return_true' ) ) {
			add_filter( 'option_woocommerce_stripe_settings', array( $this, 'override_btn_height_settings_on_update' ), 10, 1 );
			add_filter( 'wc_stripe_settings', array( $this, 'filter_default_settings' ), 1 );
		}
	}

	function run() {
		// Apple Pay
		$this->add_stripe_apple_pay();
	}

	function override_btn_height_settings_on_update( $value ) {
		$value['payment_request_button_height'] = $this->stripe_request_button_height;

		return $value;
	}

	function filter_default_settings( $settings ) {
		$settings['payment_request_button_height']['default'] = $this->stripe_request_button_height;

		return $settings;
	}

	function add_stripe_apple_pay() {
		// Setup Apple Pay
		if ( class_exists( '\\WC_Stripe_Payment_Request' ) && Main::is_checkout() ) {
			$stripe_payment_request = \WC_Stripe_Payment_Request::instance();

			if ( class_exists( '\\WC_Stripe_Apple_Pay_Registration' ) ) {
				$apple_pay_reg = new \WC_Stripe_Apple_Pay_Registration();

				if (
					$apple_pay_reg->stripe_enabled == 'yes' &&
					$apple_pay_reg->apple_pay_domain_set &&
					$apple_pay_reg->payment_request
				) {
					add_filter( 'wc_stripe_show_payment_request_on_checkout', '__return_true' );

					// Remove default stripe request placement
					remove_action( 'woocommerce_checkout_before_customer_details', array( $stripe_payment_request, 'display_payment_request_button_html' ), 1 );
					remove_action( 'woocommerce_checkout_before_customer_details', array( $stripe_payment_request, 'display_payment_request_button_separator_html' ), 2 );

					// Add our own stripe requests
					add_action( 'cfw_payment_request_buttons', array( $stripe_payment_request, 'display_payment_request_button_html' ), 1 );
					add_action( 'cfw_checkout_before_customer_info_tab', array( $this, 'add_apple_pay_separator' ), 100 );
				}
			}
		}
	}

	function add_apple_pay_separator() {
		$this->add_separator( '', 'wc-stripe-payment-request-button-separator', 'text-align: center;' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'Stripe',
			'params' => [],
		];

		return $compatibility;
	}
}
