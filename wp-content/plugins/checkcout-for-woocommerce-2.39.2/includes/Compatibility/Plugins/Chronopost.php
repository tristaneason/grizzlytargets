<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Chronopost extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'activate_chronopost' );
	}

	public function run() {
		add_action( 'cfw_payment_tab_content', function() {
			do_action( 'woocommerce_review_order_before_payment' );
		} );
	}
}
