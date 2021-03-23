<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class DiviUltimateFooter extends Base {
	public function __construct() {
		parent::__construct();
	}

	function is_available() {
		return function_exists( 'divi_ultimate_footer_plugin_init' );
	}

	function run() {
		remove_action( 'wp_enqueue_scripts', 'divi_ultimate_footer_plugin_main_css', 20 );
		remove_action('get_header', 'divi_ultimate_footer_plugin_ob_start', 1);
		remove_action('wp_footer', 'divi_ultimate_footer_plugin_custom_footer', 5);
		remove_filter('body_class', 'divi_ultimate_footer_plugin_add_body_class');
		remove_action('wp_head', 'divi_ultimate_footer_plugin_css_edit');
	}
}
