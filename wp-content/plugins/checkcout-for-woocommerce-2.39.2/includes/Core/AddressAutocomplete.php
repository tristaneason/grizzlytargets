<?php

namespace Objectiv\Plugins\Checkout\Core;

use Objectiv\Plugins\Checkout\Main;

class AddressAutocomplete {
	public function __construct() {
		if ( Main::instance()->get_settings_manager()->get_setting('enable_address_autocomplete' ) == 'yes' ) {
			add_filter( 'cfw_enable_zip_autocomplete', '__return_false' );
		}
	}
}