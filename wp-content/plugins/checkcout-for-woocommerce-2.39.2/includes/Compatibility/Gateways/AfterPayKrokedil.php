<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class AfterPayKrokedil extends Base {
	public function is_available() {
		return defined('ARVATO_CHECKOUT_LIVE');
	}

	public function run() {
		$this->add_thickbox();
		$this->customer_precheck();
	}

	function add_thickbox() {
		if ( Main::is_checkout() ) {
			add_thickbox();
		}
	}

	function customer_precheck() {
		global $wc_afterpay_pre_check_customer;

		add_action( 'cfw_checkout_before_payment_method_terms_checkbox', array($wc_afterpay_pre_check_customer, 'display_pre_check_form') );
	}
}
