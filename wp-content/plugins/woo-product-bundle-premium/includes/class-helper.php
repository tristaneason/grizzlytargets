<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPCleverWoosb_Helper' ) ) {
	class WPCleverWoosb_Helper {
		public static function woosb_get_price( $product, $min_or_max = 'min' ) {
			if ( get_option( '_woosb_bundled_price_from', 'sale_price' ) === 'regular_price' ) {
				if ( $product->is_type( 'variable' ) ) {
					if ( $min_or_max === 'max' ) {
						return $product->get_variation_regular_price( 'max' );
					} else {
						return $product->get_variation_regular_price( 'min' );
					}
				} else {
					return $product->get_regular_price();
				}
			} else {
				if ( $product->is_type( 'variable' ) ) {
					if ( $min_or_max === 'max' ) {
						return $product->get_variation_price( 'max' );
					} else {
						return $product->get_variation_price( 'min' );
					}
				} else {
					return $product->get_price();
				}
			}
		}

		public static function woosb_get_price_to_display( $product, $qty = 1, $min_or_max = 'min' ) {
			return (float) wc_get_price_to_display( $product, array(
				'price' => self::woosb_get_price( $product, $min_or_max ),
				'qty'   => $qty
			) );
		}

		public static function woosb_clean_ids( $ids ) {
			return apply_filters( 'woosb_clean_ids', $ids );
		}

		public static function woosb_minify_items( $items ) {
			$minify_items = array();

			foreach ( $items as $item ) {
				if ( empty( $minify_items ) ) {
					$minify_items[] = $item;
				} else {
					$has_item = false;

					foreach ( $minify_items as $key => $minify_item ) {
						if ( $minify_item['id'] === $item['id'] ) {
							$minify_items[ $key ]['qty'] += $item['qty'];
							$has_item                    = true;
						}
					}

					if ( ! $has_item ) {
						$minify_items[] = $item;
					}
				}
			}

			return $minify_items;
		}

		public static function woosb_localization( $key = '', $default = '' ) {
			$str = '';

			if ( ! empty( $key ) && ! empty( WPCleverWoosb::$localization[ $key ] ) ) {
				$str = WPCleverWoosb::$localization[ $key ];
			} elseif ( ! empty( $default ) ) {
				$str = $default;
			}

			return apply_filters( 'woosb_localization_' . $key, $str );
		}
	}

	new WPCleverWoosb_Helper();
}