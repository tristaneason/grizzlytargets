<?php

/**
 * WPPFM Attribute Selector Element Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Attribute_Selector_Element' ) ) :

	class WPPFM_Attribute_Selector_Element {

		/**
		 * Returns the code for the required fields.
		 *
		 * @return string
		 */
		public static function required_fields() {
			return '<div id="required-fields" style="display:initial;">
				<legend class="field-level">
				<h4 id="wppfm-required-attributes-header">' . __( 'Required attributes', 'wp-product-feed-manager' ) . ':</h4>
				</legend>'
				. self::field_form_table_titles() .
				'<div class="field-table" id="required-field-table"></div>
				</div>';
		}

		/**
		 * Returns the code for the highly recommended fields.
		 *
		 * @return string
		 */
		public static function highly_recommended_fields() {
			return '<div id="highly-recommended-fields" style="display:none;">
				<legend class="field-level">
				<h4 id="wppfm-highly-recommended-attributes-header">' . __( 'Highly recommended attributes', 'wp-product-feed-manager' ) . ':</h4>
				</legend>'
				. self::field_form_table_titles() .
				'<div class="field-table" id="highly-recommended-field-table"></div>
				</div>';
		}

		/**
		 * Returns the code for the recommended fields.
		 *
		 * @return string
		 */
		public static function recommended_fields() {
			return '<div id="recommended-fields" style="display:none;">
				<legend class="field-level">
				<h4 id="wppfm-recommended-attributes-header">' . __( 'Recommended attributes', 'wp-product-feed-manager' ) . ':</h4>
				</legend>'
				. self::field_form_table_titles() .
				'<div class="field-table" id="recommended-field-table"></div>
				</div>';
		}

		/**
		 * Returns the code for the optional fields.
		 *
		 * @return string
		 */
		public static function optional_fields() {
			return '<div id="optional-fields" style="display:initial;">
				<legend class="field-level">
				<h4 id="wppfm-optional-attributes-header">' . __( 'Optional attributes', 'wp-product-feed-manager' ) . ':</h4>
				</legend>'
				. self::field_form_table_titles() .
				'<div class="field-table" id="optional-field-table"></div>
				</div>';
		}

		/**
		 * Returns the code for the custom fields.
		 *
		 * @return string
		 */
		public static function custom_fields() {
			return '<div id="custom-fields" style="display:initial;">
				<legend class="field-level">
				<h4 id="wppfm-custom-attributes-header">' . __( 'Custom attributes', 'wp-product-feed-manager' ) . ':</h4>
				</legend>'
				. self::field_form_table_titles() .
				'<div class="field-table" id="custom-field-table"></div>
				</div>';
		}

		/**
		 * Returns the feed form table titles
		 *
		 * @return string
		 */
		private static function field_form_table_titles() {
			return '<div class="wppfm-field-header-wrapper">
				<div class="field-header col20w">' . __( 'Add to feed', 'wp-product-feed-manager' ) . '</div>
				<div
					class="field-header col30w">' . __( 'From WooCommerce source', 'wp-product-feed-manager' ) . '</div>
				<div class="field-header col40w">' . __( 'Condition', 'wp-product-feed-manager' ) . '</div>
				<div class="end-row">&nbsp;</div>
			</div>';
		}
	}

	// end of WPPFM_Attribute_Selector_Element class

endif;
