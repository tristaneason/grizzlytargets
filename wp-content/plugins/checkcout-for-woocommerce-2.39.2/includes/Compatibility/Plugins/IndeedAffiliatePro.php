<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class IndeedAffiliatePro extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return class_exists( '\\UAP_Main' );
	}

	function remove_scripts( $scripts ) {
		$scripts['uap-select2'] = 'uap-select2';

		return $scripts;
	}

	function remove_styles( $styles ) {
		$styles['uap_select2_style'] = 'uap_select2_style';

		return $styles;
	}
}
