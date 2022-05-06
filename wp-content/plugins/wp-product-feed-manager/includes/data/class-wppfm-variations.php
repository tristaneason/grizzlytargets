<?php

/**
 * WP Product Feed Manager Variations Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 1.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Variations' ) ) :

	/**
	 * Variations Class
	 */
	class WPPFM_Variations {

		/**
		 * Fills the product attributes with the correct variation data
		 *
		 * @param array $product_data
		 * @param WC_Product_Variation $woocommerce_variation_data
		 * @param array $wpmr_variation_data
		 * @param string $feed_language
		 * @param string $feed_currency
		 */
		public static function fill_product_data_with_variation_data( &$product_data, $woocommerce_variation_data, $wpmr_variation_data, $feed_language, $feed_currency ) {
			$permalink   = array_key_exists( 'permalink', $product_data ) ? $product_data['permalink'] : ''; // some channels don't require permalinks
			$conversions = self::variation_conversion_table( $woocommerce_variation_data, $permalink, $feed_language, $feed_currency );
			$variation_attributes = $woocommerce_variation_data->get_variation_attributes();

			foreach ( $product_data as $key => $field_value ) {
				if ( in_array( $key, array_keys( $conversions ) ) && $field_value !== $conversions[ $key ] && $conversions[ $key ] ) {
					$product_data[ $key ] = $conversions[ $key ];
				}

				if ( array_key_exists( $key, $variation_attributes ) && $variation_attributes[ $key ] ) {
					$slug_value           = $variation_attributes[ $key ];
					$attribute_term       = get_term_by( 'slug', $slug_value, ltrim( $key, 'attribute_' ) );
					$attribute_slug_value = $attribute_term && property_exists($attribute_term, 'name') ? $attribute_term->name : '';
					$attribute_value      = $attribute_slug_value ? $attribute_slug_value : $slug_value;
					$product_data[ $key ] = $attribute_value;
					continue;
				}

				if ( array_key_exists( 'attribute_pa_' . $key, $variation_attributes ) && $variation_attributes[ 'attribute_pa_' . $key ] ) {
					$slug_value           = $variation_attributes[ 'attribute_pa_' . $key ];
					$product_data[ $key ] = get_term_by( 'slug', $slug_value, 'pa_' . $key )->name;
					continue;
				}

				// process the wpmr variation data
				if ( $wpmr_variation_data && array_key_exists( $key, $wpmr_variation_data ) && $wpmr_variation_data[ $key ] ) {
					$product_data[ $key ] = $wpmr_variation_data[ $key ];
				}
			}
		}

		private static function variation_conversion_table( $variation_data, $main_permalink, $feed_language, $feed_currency ) {
			$attachment_url = wp_get_attachment_url( get_post_thumbnail_id( $variation_data->get_id() ) );

			return array(
				'ID'                     => (string) $variation_data->get_id(),
				'_downloadable'          => $variation_data->get_downloadable( 'feed' ),
				'_virtual'               => $variation_data->get_virtual( 'feed' ),
				'_manage_stock'          => $variation_data->get_manage_stock( 'feed' ),
				'_stock'                 => $variation_data->get_stock_quantity( 'feed' ),
				'_backorders'            => $variation_data->get_backorders( 'feed' ),
				'_stock_status'          => $variation_data->get_stock_status( 'feed' ),
				'_sku'                   => $variation_data->get_sku( 'feed' ),
				'_weight'                => $variation_data->get_weight( 'feed' ),
				'_length'                => $variation_data->get_length( 'feed' ),
				'_width'                 => $variation_data->get_width( 'feed' ),
				'_height'                => $variation_data->get_height( 'feed' ),
				'post_content'           => $variation_data->get_description( 'feed' ),
				'_regular_price'         => wppfm_prep_money_values( $variation_data->get_regular_price( 'feed' ), $feed_language, $feed_currency ),
				'_sale_price'            => wppfm_prep_money_values( $variation_data->get_sale_price( 'feed' ), $feed_language, $feed_currency ),
				'_sale_price_dates_from' => $variation_data->get_date_on_sale_from( 'feed' ) && ( $date = $variation_data->get_date_on_sale_from( 'feed' )->getTimestamp() ) ? wppfm_convert_price_date_to_feed_format( $date ) : '',
				'_sale_price_dates_to'   => $variation_data->get_date_on_sale_to( 'feed' ) && ( $date = $variation_data->get_date_on_sale_to( 'feed' )->getTimestamp() ) ? wppfm_convert_price_date_to_feed_format( $date ) : '',
				'attachment_url'         => has_filter( 'wppfm_get_wpml_permalink' ) ? apply_filters( 'wppfm_get_wpml_permalink', $attachment_url, $feed_language ) : $attachment_url,
				'permalink'              => $main_permalink,
			);
		}
	}


	// End of WPPFM_Variations_Class

endif;
