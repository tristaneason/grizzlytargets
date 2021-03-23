<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class BlueCheck extends Base {
	public function is_available() {
		return class_exists( '\\WC_Integration_BlueCheck_Integration' );
	}

	public function run() {
		add_action( 'woocommerce_before_order_notes', array( $this, 'add_shipping_different_checkbox' ) );
	}

	function add_shipping_different_checkbox() {
	    if ( ! WC()->cart->needs_shipping_address() ) return;
		?>
		<div style="display:none;">
			<input id="ship-to-different-address-checkbox" type="checkbox" name="ship_to_different_address" disabled="disabled" value="1" checked />
		</div>
		<?php
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'BlueCheck',
			'params' => [],
		];

		return $compatibility;
	}
}
