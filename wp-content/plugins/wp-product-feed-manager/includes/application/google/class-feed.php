<?php
/**
 * WP Product Feed Manager Google Feed Class.
 *
 * @package WP Product Feed Manager/Channels
 * @version 16.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Google_Feed_Class' ) ) :

	/**
	 * WPPFM_Google_Feed_Class class
	 */
	class WPPFM_Google_Feed_Class extends WPPFM_Feed_Master_Class {

		private $_version = '16.0';

		public function __construct() {
			parent::__construct();
		}

		public function get_version() {
			return $this->_version;
		}

		public function get_file_text() {
			return $this->generate_file_text( '1', 'google_product_category', 'description', 'xml' );
		}

		public function woocommerce_to_feed_fields() {
			$fields = new stdClass();

			// ALERT! Any changes made to this object also need to be done to the woocommerceToGoogleFields() function in the google-source.js file
			$fields->id                        = '_sku';
			$fields->title                     = 'post_title';
			$fields->google_product_category   = 'category';
			$fields->description               = 'post_content';
			$fields->link                      = 'permalink';
			$fields->image_link                = 'attachment_url';
			$fields->additional_image_link     = '_wp_attachement_metadata';
			$fields->price                     = '_regular_price';
			$fields->identifier_exists         = 'Fill with a static value';
			$fields->sale_price_effective_date = '_sale_price_dates_from';
			$fields->item_group_id             = 'item_group_id';
			$fields->mpn                       = 'ID';
			$fields->tax                       = 'Use the settings in the Merchant Center';
			$fields->shipping                  = 'Use the settings in the Merchant Center';

			return $fields;
		}

		// overrides the set_feed_output_attribute_levels function in WPPFM_Feed_Master_Class
		// ALERT! This function is equivalent for the setGoogleOutputAttributeLevels() function in google-source.js
		public function set_feed_output_attribute_levels( &$main_data ) {
			$country = $main_data->country;

			for ( $i = 0; $i < count( $main_data->attributes ); $i ++ ) {
				if ( '0' === $main_data->attributes[ $i ]->fieldLevel ) {
					switch ( $main_data->attributes[ $i ]->fieldName ) {
						case 'google_product_category':
							$main_data->attributes[ $i ]->fieldLevel = $this->google_needs_product_cat( $main_data->mainCategory ) === true ? 1 : 4;
							break;

						case 'is_bundle':
						case 'multipack':
							$main_data->attributes[ $i ]->fieldLevel = in_array( $country, $this->special_product_countries() ) ? 1 : 4;
							break;

						case 'brand':
							$main_data->attributes[ $i ]->fieldLevel = $this->google_requires_brand( $main_data->mainCategory ) === true ? 1 : 4;
							break;

						case 'item_group_id':
							$main_data->attributes[ $i ]->fieldLevel = in_array( $country, $this->special_clothing_group_countries() ) ? 1 : 4;
							break;

						case 'gender':
						case 'age_group':
						case 'color':
						case 'size':
							if ( in_array( $country, $this->special_clothing_group_countries() )
								&& $this->google_clothing_and_accessories( $main_data->mainCategory ) === true ) {
								$main_data->attributes[ $i ]->fieldLevel = 1;
							} else {
								$main_data->attributes[ $i ]->fieldLevel = 4;
							}

							break;

						case 'tax':
							// In accordance with the Google Feed Specifications update of september 2015
							$main_data->attributes[ $i ]->fieldLevel = 'US' === $country ? 1 : 4;
							break;

						case 'shipping':
							// In accordance with the Google Feed Specifications update of september 2015
							$main_data->attributes[ $i ]->fieldLevel = in_array( $country, $this->special_shipping_countries() ) ? 1 : 4;
							break;

						case 'subscription_cost':
						case 'subscription_cost-period':
						case 'subscription_cost-period_length':
						case 'subscription_cost-amount':
							$main_data->attributes[ $i ]->fieldLevel = in_array( $country, $this->special_subscription_countries() ) ? 4 : 0;
							break;

						default:
							break;
					}

					$main_data->attributes[ $i ]->isActive =
						$this->set_attribute_status( (int) $main_data->attributes[ $i ]->fieldLevel, $main_data->attributes[ $i ]->value );
				}
			}
		}

		public function keys_that_have_sub_tags() {
			return array( 'installment', 'loyalty_points', 'shipping', 'tax', 'subscription_cost', 'product_detail' );
		}

		public function keys_that_can_be_used_more_than_once() {
			return array( 'display_ads_similar_id', 'excluded_destination', 'shopping_ads_excluded_country', 'product_highlight', 'adwords_labels', 'shipping', 'product_detail' );
		}

		public function sub_keys_for_sub_tags() {
			return array(
				'installment-months',
				'installment-amount',
				'loyalty_points-name',
				'loyalty_points-pointsValue',
				'loyalty_points-ratio',
				'shipping-country',
				'shipping-region',
				'shipping-service',
				'shipping-price',
				'tax-country',
				'tax-region',
				'tax-rate',
				'tax-tax_ship',
				'subscription_cost-period',
				'subscription_cost-period_length',
				'subscription_cost-amount',
				'product_detail-section_name',
				'product_detail-attribute_name',
				'product_detail-attribute_value',
			);
		}

		// ALERT! This function is equivalent to the googleSpecialClothingGroupCountries() function in google-source.js
		private function special_clothing_group_countries() {
			return array( 'US', 'GB', 'DE', 'FR', 'JP', 'BR' ); // Brazil added based on the new Feed Specifications from september 2015
		}

		// ALERT! This function is equivalent to the googleSpecialShippingCountries() function in google-source.js
		private function special_shipping_countries() {
			return array( 'US', 'GB', 'DE', 'AU', 'FR', 'CH', 'CZ', 'NL', 'IT', 'ES', 'JP' );
		}

		// ALERT! This function is equivalent to the googleSpecialProductCountries() function in google-source.js
		private function special_product_countries() {
			return array( 'US', 'GB', 'DE', 'AU', 'FR', 'CH', 'CZ', 'NL', 'IT', 'ES', 'JP', 'BR' );
		}

		// ALERT! This function is equivalent to the googleSpecialSubscriptionCountries() function in google-source.js
		private function special_subscription_countries() {
			return array( 'ZA', 'HK', 'IN', 'JP', 'MY', 'NZ', 'SG', 'KR', 'TW', 'TH', 'AT', 'BE', 'CZ', 'DK', 'FI', 'DE', 'FR', 'GR', 'HU', 'IE',
				'IT', 'NO', 'PL', 'PT', 'RO', 'SK', 'ES', 'SE', 'CH', 'TR', 'GB', 'IL', 'SA', 'AE', 'CA' );
		}

		private function google_clothing_and_accessories( $category ) {
			return stristr( $category, 'Apparel & Accessories' ) !== false ? true : false;
		}

		private function google_needs_product_cat( $category ) {
			return stristr( $category, 'Apparel & Accessories' ) !== false
				|| stristr( $category, 'Media' ) !== false
				|| stristr( $category, 'Software' ) !== false;
		}

		private function google_requires_brand( $category ) {
			return false === stristr( $category, 'Media' );
		}

		protected function header( $title, $description = '' ) {
			// the check for convert_to_data_string function can be remove when all users have switched to plugin version 1.6 or higher
			$title_string       = method_exists( $this, 'data_string' ) ? $this->data_string( $title )
				: $this->convert_to_character_data_string( $title );
			$home_link          = method_exists( $this, 'data_string' ) ? $this->data_string( get_option( 'home' ) )
				: $this->convert_to_character_data_string( get_option( 'home' ) );
			$descr              = '' !== $description ? $description : $title;
			$description_string = method_exists( $this, 'data_string' ) ? $this->data_string( $descr )
				: $this->convert_to_character_data_string( $descr );
			$title_tag          = '<wf-connection-string>';

			return '<?xml version="1.0"?>
					<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
					<channel>
					<title>' . $title_string . '</title>'
			       . $title_tag . $home_link . '</link>
					<description>' . $description_string . '</description>';
		}

		protected function footer() {
			return '</channel></rss>';
		}
	}

	// end of WPPFM_Google_Feed_Class

endif;
