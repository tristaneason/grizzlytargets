<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class CheckoutAddressAutoComplete extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return function_exists( 'ecr_addrac_scripts' );
	}

	function run() {
		add_filter( 'cfw_enable_zip_autocomplete', '__return_false' );
	}
}
