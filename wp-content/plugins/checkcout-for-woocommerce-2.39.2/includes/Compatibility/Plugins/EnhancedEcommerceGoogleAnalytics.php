<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class EnhancedEcommerceGoogleAnalytics extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'wc_enhanced_ecommerce_google_analytics_add_integration' );
	}

	public function run() {
		$integrations = WC()->integrations->get_integrations();

		if ( isset( $integrations['enhanced_ecommerce_google_analytics'] ) ) {
			$WC_Enhanced_Ecommerce_Google_Analytics = $integrations['enhanced_ecommerce_google_analytics'];

			// Checkout Actions
			add_action( 'cfw_checkout_before_customer_info_tab', array( $WC_Enhanced_Ecommerce_Google_Analytics, 'checkout_step_1_tracking' ) );
			add_action( 'cfw_checkout_before_shipping_method_tab', array( $WC_Enhanced_Ecommerce_Google_Analytics, 'checkout_step_2_tracking' ) );
			add_action( 'cfw_checkout_before_payment_method_tab', array( $WC_Enhanced_Ecommerce_Google_Analytics, 'checkout_step_3_tracking' ) );
		}
	}
}
