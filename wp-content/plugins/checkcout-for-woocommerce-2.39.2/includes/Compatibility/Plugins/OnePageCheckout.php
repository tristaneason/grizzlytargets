<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class OnePageCheckout extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'initialize_one_page_checkout' );
	}

	public function run() {
		add_filter( 'cfw_checkout_is_enabled', array( $this, 'override_is_enabled' ), 10, 1 );
	}

	function override_is_enabled( $enabled ) {
		if ( $enabled && is_wcopc_checkout() ) {
			return false;
		}

		return $enabled;
	}
}
