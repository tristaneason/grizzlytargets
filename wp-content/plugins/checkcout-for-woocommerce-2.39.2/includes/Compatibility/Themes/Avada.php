<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Themes;

use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class Avada extends Base {
	public function is_available() {
		return defined( 'AVADA_VERSION' ); // determining if themes are available is a bit difficult and not really helpful here, so let's just always load it
	}

	public function run() {
		global $avada_woocommerce;

		// Remove actions
		remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'avada_top_user_container' ), 1 );
		remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'before_checkout_form' ) );
		remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'checkout_coupon_form' ), 10 );
		remove_action( 'woocommerce_before_checkout_form', array( $avada_woocommerce, 'checkout_coupon_form' ), 10 );

		add_action( 'wp_head', array( $this, 'cleanup_css'), 0 );

		$this->disable_lazy_loading();
	}

	function run_on_order_received() {
		global $avada_woocommerce;

		remove_action( 'woocommerce_thankyou', array( $avada_woocommerce, 'view_order' ) );

		$this->disable_lazy_loading();
	}

	function disable_lazy_loading() {
		if ( ! class_exists('\\Fusion') ) {
			return;
		}
		
		$fusion = \Fusion::get_instance();
		remove_filter( 'wp_get_attachment_image_attributes', [ $fusion->images, 'lazy_load_attributes' ], 10 );
	}

	function cleanup_css() {
		global $wp_filter;

		$existing_hooks                      = $wp_filter['wp_head'];

		if ( $existing_hooks[ 999 ] ) {
			foreach ( $existing_hooks[ 999 ] as $key => $callback ) {
				if ( false !== stripos( $key, 'add_inline_css_wp_head' ) ) {
					global $Fusion_Dynamic_CSS_File;

					$Fusion_Dynamic_CSS_File = $callback['function'][0];
				}
			}
		}

		if ( empty($Fusion_Dynamic_CSS_File) ) return;

		$action = fusion_should_defer_styles_loading() ? 'wp_body_open' : 'wp_enqueue_scripts';
		remove_action( $action, [ $Fusion_Dynamic_CSS_File, 'add_inline_css' ] );
		remove_action( 'wp_head', [ $Fusion_Dynamic_CSS_File, 'add_custom_css_to_wp_head' ], 999 );
		remove_action( 'wp_head', [ $Fusion_Dynamic_CSS_File, 'add_inline_css_wp_head' ], 999 );
	}
}
