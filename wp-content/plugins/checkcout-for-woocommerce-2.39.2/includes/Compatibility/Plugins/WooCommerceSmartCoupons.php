<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooCommerceSmartCoupons extends Base {
	public function is_available() {
		return class_exists( '\\WC_Smart_Coupons' );
	}

	public function run() {
		$WC_SC_Purchase_Credit = \WC_SC_Purchase_Credit::get_instance();
		add_action( 'cfw_checkout_before_payment_method_terms_checkbox', array( $WC_SC_Purchase_Credit, 'gift_certificate_receiver_detail_form' ) );
		remove_action( 'woocommerce_checkout_after_customer_details', array( $WC_SC_Purchase_Credit, 'gift_certificate_receiver_detail_form' ) );
	}
}