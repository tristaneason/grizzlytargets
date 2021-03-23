<?php
/**
 * Common FedEx Class.
 */
if( ! defined('ABSPATH') )	exit();

if( ! class_exists('Ph_Fedex_Woocommerce_Shipping_Common') ) {
	class Ph_Fedex_Woocommerce_Shipping_Common {

		/**
		 * Active plugins.
		 */
		private static $active_plugins;
		
		/**
		 * Active plugins.
		 * @return array.
		 */
		public static function get_active_plugins() {
			if( empty(self::$active_plugins) ) {
				self::$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) );
				// Multisite case
				if ( is_multisite() ) {
					self::$active_plugins = array_merge( self::$active_plugins, array_keys( get_site_option( 'active_sitewide_plugins', array() ) ) );
				}
			}
			return self::$active_plugins;
		}
		
		/**
		 * Get the Converted Weight.
		 * @param mixed $to_unit To Unit.
		 * @param string $from_unit (Optional) From unit if noting is passed then store dimension unit will be taken.
		 * @return float
		 */
		public static function ph_get_converted_weight( $weight, $to_unit, $from_unit=''){
			$weight = (float) $weight;
			$converted_weight = wc_get_weight( $weight, $to_unit, $from_unit );
			return apply_filters( 'ph_fedex_get_converted_weight',$converted_weight, $weight, $to_unit, $from_unit );
		}

		/**
		 * Get the Converted Dimension.
		 * @param mixed $to_unit To Unit.
		 * @param string $from_unit (Optional) From unit if noting is passed then store weight unit will be taken.
		 * @return float
		 */
		public static function ph_get_converted_dimension( $dimension, $to_unit, $from_unit='' ){
			$dimension = (float) $dimension;
			$converted_dimension = wc_get_dimension( $dimension, $to_unit, $from_unit );
			return apply_filters( 'ph_fedex_get_converted_dimension', $converted_dimension, $dimension, $to_unit, $from_unit );
		}
	}
}