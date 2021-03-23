<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class MartfuryAddons extends Base {
	public function __construct() {
		parent::__construct();
	}

	public function is_available() {
		return function_exists( 'martfury_vc_addons_init' );
	}

	function remove_scripts( $scripts ) {
		$scripts['martfury-shortcodes'] = 'martfury-shortcodes';

		return $scripts;
	}
}
