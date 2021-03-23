<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class OrderDeliveryDate extends Base {
	public function is_available() {
		return class_exists('\\order_delivery_date');
	}

	public function pre_init() {
		add_filter( 'orddd_shopping_cart_hook', array($this, 'set_delivery_field_hook') );
	}

	function set_delivery_field_hook( $hook ) {
		return 'cfw_checkout_after_shipping_methods';
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'OrderDeliveryDate',
			'params' => [],
		];

		return $compatibility;
	}
}