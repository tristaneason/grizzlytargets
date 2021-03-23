<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\Helpers\AmazonPayShippingInfoHelper;

class AmazonPay extends Base {

	protected $amazon_payments = null;

	protected $wc_gateway = null;

	protected $ref_id = '';

	protected $shipping_info_helper = null;

	protected $gateway_classes = [
		'WC_Gateway_Amazon_Payments_Advanced_Subscriptions',
		'WC_Gateway_Amazon_Payments_Advanced',
	];

	protected $available = false;

	/**
	 * AmazonPay constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Kick off the search for the instantiated gateway
	 */
	function pre_init() {
		$this->shipping_info_helper = new AmazonPayShippingInfoHelper();
	}

	/**
	 * @param $gateways
	 *
	 * @return void
	 */
	function get_amazon_gateway() {
		$gateways = WC()->payment_gateways->get_available_payment_gateways();

		foreach ( $gateways as $gateway ) {
			// If class is a string. It's not the gateway
			if ( is_string( $gateway ) ) {
				continue;
			}

			// Get the gateway class
			$class = get_class( $gateway );

			// If in the set classes we are looking for, set the wc_gateway
			if ( in_array( $class, $this->gateway_classes ) ) {
				$this->wc_gateway = $gateway;
			}
		}

		do_action( 'cfw_amazon_payment_gateway_found', $this->wc_gateway );
	}

	function is_available() {
		$available = false;

		if ( class_exists( '\\WC_Amazon_Payments_Advanced_API' ) ) {
			$settings = \WC_Amazon_Payments_Advanced_API::get_settings();

			if ( class_exists( '\\WC_Amazon_Payments_Advanced' ) && $settings['enabled'] === 'yes' ) {

				$this->amazon_payments = $GLOBALS['wc_amazon_payments_advanced'];

				$available = true;
			}
		}

		return $available;
	}

	function run_immediately() {
		add_action( 'woocommerce_checkout_init', array( $this, 'checkout_init' ), 9 );
		add_filter( 'woocommerce_pa_hijack_checkout_fields', '__return_false' );
		add_action( 'woocommerce_checkout_init', array($this, 'remove_banners' ), 100 );

		$this->get_amazon_gateway();
	}

	function remove_banners() {
		// Remove default locations
		$apa = wc_apa();

		remove_action( 'woocommerce_checkout_before_customer_details', array( $apa, 'payment_widget' ), 20 );
		remove_action( 'woocommerce_checkout_before_customer_details', array( $apa, 'address_widget' ), 10 );

		$reference_id = \WC_Amazon_Payments_Advanced_API::get_reference_id();
		$access_token = \WC_Amazon_Payments_Advanced_API::get_access_token();

		// Remove before the form messages
		if ( empty( $reference_id ) && empty( $access_token ) ) {
			remove_action( 'woocommerce_before_checkout_form', array( $this->amazon_payments, 'checkout_message' ), 5 );
			remove_action( 'before_woocommerce_pay', array( $this->amazon_payments, 'checkout_message' ), 5 );
		}
		
		remove_action( 'woocommerce_before_checkout_form', array( $this->amazon_payments, 'placeholder_checkout_message_container' ), 5 );
	}

	/**
	 * Mimics amazons checkout_init but with our hooks instead. See the same function name in WC_Amazon_Payments_Advanced
	 * for more details
	 */
	function checkout_init() {
		$settings     = \WC_Amazon_Payments_Advanced_API::get_settings();
		$reference_id = \WC_Amazon_Payments_Advanced_API::get_reference_id();
		$access_token = \WC_Amazon_Payments_Advanced_API::get_access_token();

		$enable_login_app = ( 'yes' === $settings['enable_login_app'] );

		if ( ! WC()->cart ) {
			return;
		}

		if ( ! WC()->cart->needs_payment() && ! $enable_login_app ) {
			add_action( 'cfw_checkout_before_customer_info_tab', array( $this->amazon_payments, 'placeholder_widget_container' ) );
		}

		if ( empty( $reference_id ) && empty( $access_token ) ) {
			add_action( 'cfw_payment_request_buttons', array( $this->amazon_payments, 'checkout_message' ) );
			add_action( 'cfw_checkout_before_customer_info_tab', array( $this, 'add_separator' ), 10 );
		} else {
			remove_all_actions( 'cfw_payment_request_buttons' );
		}

		if ( ( ! $enable_login_app && empty( $reference_id ) ) || ( $enable_login_app && empty( $access_token ) ) ) {
			return;
		}

		$this->are_extra_login_fields_needed();

		add_action( 'cfw_checkout_before_customer_info_address', array( $this, 'output_address_widget' ), 10 );
		add_action( 'cfw_checkout_after_payment_methods', array( $this, 'output_payment_widget' ), 20 );

		$checkout                             = \WC_Checkout::instance();
		$checkout->checkout_fields['billing'] = null;

		$this->amazon_payments->hijack_checkout_fields( $checkout );
	}

	function are_extra_login_fields_needed() {
		$checkout = \WC_Checkout::instance();

		if ( ! is_user_logged_in() && $checkout->enable_signup ) {
			add_action( 'cfw_checkout_after_email', 'cfw_extra_account_fields' );
		}
	}

	/**
	 * Since we are splitting these up we need to end the div sequence
	 */
	function output_address_widget() {
		ob_start();
		$this->amazon_payments->address_widget();
		$contents = ob_get_contents();
		ob_end_clean();

		$contents .= '</div></div>';

		echo $contents;
	}

	/**
	 * We need to add 2 div tags here to the content output
	 */
	function output_payment_widget() {
		$contents = '<div><div>';

		ob_start();
		$this->amazon_payments->payment_widget();
		$contents .= ob_get_contents();
		ob_end_clean();

		echo $contents;
	}

	function typescript_class_and_params( $compatibility ) {

		$compatibility[] = [
			'class'  => 'AmazonPay',
			'params' => [],
		];

		return $compatibility;
	}
}
