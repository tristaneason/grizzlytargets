<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class Astra extends Base {
	public function is_available() {
		return defined( 'ASTRA_THEME_VERSION' );
	}

	public function run() {
		$this->remove_astra_scripts();
	}

	public function remove_scripts( $scripts ) {
		// This prevents basically all Astra Add-on scripts from loading
		$scripts['astra-addon-js'] = 'astra-addon-js';

		return $scripts;
	}

	public function remove_astra_scripts() {
		if ( Main::is_checkout() ) {
			remove_all_actions( 'astra_get_js_files' );
		}
	}
}