<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class ExtraCheckoutFieldsBrazil extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return class_exists( '\\Extra_Checkout_Fields_For_Brazil' );
	}

	public function run() {
		$cfw = Main::instance();

		if ( $cfw->is_phone_fields_enabled() ) {
			add_filter( 'wcbcf_shipping_fields', array( $this, 'add_back_phone_field' ) );
		}
	}

	function add_back_phone_field( $address_fields ) {
		$fields = WC()->countries->get_default_address_fields();

		if ( ! empty($fields['phone']) ) {
			$address_fields['shipping_phone'] = $fields['phone'];
		}

		return $address_fields;
	}
}
