<?php

/**
 * WPPFM Attribute Mapping Wrapper Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Attribute_Mapping_Wrapper' ) ) :

	abstract class WPPFM_Attribute_Mapping_Wrapper {

		abstract public function display();

		protected function attribute_mapping_wrapper_table_start( $display = 'none' ) {
			return '<section class="wppfm-edit-feed-form-element-wrapper wppfm-attribute-mapping-wrapper" id="wppfm-attribute-map" style="display:' . $display . ';">';
		}

		protected function attribute_mapping_wrapper_table_title() {
			return '<div class="header" id="fields-form-header"><h3>' . __( 'Attribute Mapping', 'wp-product-feed-manager' ) . ':</h3></div>';
		}

		protected function attribute_mapping_wrapper_table_end() {
			return '</section></div>';
		}
	}

	// end of WPPFM_Attribute_Mapping_Wrapper class

endif;
