<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class CraftyClicks extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return class_exists( '\\WC_CraftyClicks_Postcode_Lookup' );
	}

	function run() {
		$all_integrations = WC()->integrations->get_integrations();

		if ( ! empty( $all_integrations['craftyclicks_postcode_lookup'] ) ) {
			$craftyclicks_postcode_lookup = $all_integrations['craftyclicks_postcode_lookup'];

			add_action( 'cfw_checkout_before_customer_info_tab', array( $craftyclicks_postcode_lookup, 'addCheckoutJs' ) );
		}
	}
}
