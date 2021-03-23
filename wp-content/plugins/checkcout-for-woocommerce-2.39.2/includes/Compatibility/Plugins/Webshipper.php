<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Webshipper extends Base {
	public function is_available() {
		return class_exists( '\\WebshipperAPI' );
	}

	public function run_immediately() {
		global $wp_filter;

		$existing_hooks = $wp_filter['woocommerce_review_order_before_order_total'];

		if ( $existing_hooks[10] ) {
			foreach ( $existing_hooks[10] as $key => $callback ) {
				if ( isset( $callback['function'] ) && $callback['function'] instanceof \Closure ) {
					unset( $wp_filter['woocommerce_review_order_before_order_total']->callbacks[10][$key] );
				}
			}
		}
		
		add_action( 'woocommerce_review_order_after_shipping', array(  \WebshipperAPI::instance(), 'printDropPointSelector' ) );
	}
}