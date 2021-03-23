<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooCommerceGermanized extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'WC_germanized' );
	}

	public function pre_init() {
		/**
		 * Don't monkey around with gateways
		 */
		add_filter( 'woocommerce_gzd_compatibilities', array( $this, 'override_ppec_compat' ), 1000, 1 );
	}

	public function run_immediately() {
		add_action( 'cfw_checkout_before_payment_method_tab_nav', 'woocommerce_gzd_template_render_checkout_checkboxes' );
		add_action( 'cfw_checkout_before_payment_method_tab_nav', 'woocommerce_gzd_template_checkout_set_terms_manually' );

		/**
		 * Don't let WooCommerce Germanized Eff Up the Submit Button
		 */
		$WC_GZD_Checkout = \WC_GZD_Checkout::instance();
		remove_filter( 'woocommerce_update_order_review_fragments', array( $WC_GZD_Checkout, 'refresh_order_submit' ), 150 );
		remove_action( 'woocommerce_review_order_before_submit', 'woocommerce_gzd_template_set_order_button_remove_filter', PHP_INT_MAX );
		remove_action( 'woocommerce_review_order_after_submit', 'woocommerce_gzd_template_set_order_button_show_filter', PHP_INT_MAX );
		remove_action( 'woocommerce_gzd_review_order_before_submit', 'woocommerce_gzd_template_set_order_button_show_filter', PHP_INT_MAX );
	}

	function override_ppec_compat( $plugins ) {
		if ( ( $key = array_search( 'woocommerce-gateway-paypal-express-checkout', $plugins ) ) !== false ) {
			unset( $plugins[ $key ] );
		}

		return $plugins;
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'WooCommerceGermanized',
			'params' => [],
		];

		return $compatibility;
	}
}