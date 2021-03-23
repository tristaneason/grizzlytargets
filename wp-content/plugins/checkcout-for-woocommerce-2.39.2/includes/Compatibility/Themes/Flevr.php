<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Flevr extends Base {
	function is_available() {
		return function_exists( 'ci_print_fancybox_selectors' );
	}

	public function run() {
		remove_action( 'wp_footer', 'ci_print_fancybox_selectors', 20 );
		remove_filter( 'woocommerce_shipping_fields', 'remove_shipping_phone_field', 20 );
		remove_filter( 'woocommerce_billing_fields', 'remove_billing_phone_field', 20 );
	}
}
