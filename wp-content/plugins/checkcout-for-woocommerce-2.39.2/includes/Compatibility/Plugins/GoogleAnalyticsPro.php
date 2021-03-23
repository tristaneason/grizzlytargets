<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class GoogleAnalyticsPro extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return function_exists( 'wc_google_analytics_pro' );
	}

	function run() {
		$wc_google_analytics_pro             = wc_google_analytics_pro();
		$wc_google_analytics_pro_integration = $wc_google_analytics_pro->get_integration();

		// selected payment method
		if ( $wc_google_analytics_pro_integration->has_event( 'selected_payment_method' ) ) {
			add_action( 'cfw_checkout_after_payment_methods', array( $wc_google_analytics_pro_integration, 'selected_payment_method' ) );
		}
	}
}
