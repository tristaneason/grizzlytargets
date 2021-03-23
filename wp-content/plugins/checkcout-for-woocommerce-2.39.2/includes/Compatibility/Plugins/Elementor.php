<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Elementor extends Base {
	function is_available() {
		return defined( 'ELEMENTOR_VERSION' );
	}

	public function run() {
		if ( apply_filters( 'cfw_block_elementor_assets', true ) ) {
			add_filter( 'cfw_blocked_style_handles', array( $this, 'remove_elementor_styles' ), 20, 1 );
			add_filter( 'cfw_blocked_script_handles', array( $this, 'remove_elementor_scripts' ), 20, 1 );
		}
	}

	function remove_elementor_styles( $styles ) {
		global $wp_styles;

		foreach ( $wp_styles->registered as $wp_style ) {
			if ( ! empty($wp_style->src) && stripos( $wp_style->src, 'elementor' ) !== false ) {
				$styles[] = $wp_style->handle;
			}
		}

		return $styles;
	}

	function remove_elementor_scripts( $scripts ) {
		global $wp_scripts;

		foreach ( $wp_scripts->registered as $wp_script ) {
			if ( ! empty($wp_script->src) && stripos( $wp_script->src, 'elementor' ) !== false ) {
				$scripts[] = $wp_script->handle;
			}
		}

		return $scripts;
	}
}
