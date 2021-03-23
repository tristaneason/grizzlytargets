<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class DiviUltimateHeader extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return function_exists( 'divi_ultimate_header_plugin_init' );
	}

	function run() {
		remove_action('get_header', 'divi_ultimate_header_plugin_ob_start', 1);
		remove_action('wp_footer', 'divi_ultimate_header_plugin_custom_header');
		remove_action( 'wp_enqueue_scripts', 'divi_ultimate_header_plugin_main_css', 20 );
		remove_action( 'customize_controls_enqueue_scripts', 'divi_ultimate_header_plugin_customize_controls_js_css' );
	}
}
