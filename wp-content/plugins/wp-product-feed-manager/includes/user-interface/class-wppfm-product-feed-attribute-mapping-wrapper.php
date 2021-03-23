<?php

/**
 * WPPFM Product Feed Attribute Mapping Wrapper Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Product_Feed_Attribute_Mapping_Wrapper' ) ) :

	class WPPFM_Product_Feed_Attribute_Mapping_Wrapper extends WPPFM_Attribute_Mapping_Wrapper {

		/**
		 * Display the product feed attribute mapping table.
		 *
		 * @return void
		 */
		public function display() {

			// Start the section code.
			echo $this->attribute_mapping_wrapper_table_start( 'none' );

			// Add the header.
			echo $this->attribute_mapping_wrapper_table_title();

			echo WPPFM_Attribute_Selector_Element::required_fields();

			echo WPPFM_Attribute_Selector_Element::highly_recommended_fields();

			echo WPPFM_Attribute_Selector_Element::recommended_fields();

			echo WPPFM_Attribute_Selector_Element::optional_fields();

			echo WPPFM_Attribute_Selector_Element::custom_fields();

			// Close the section.
			echo $this->attribute_mapping_wrapper_table_end();
		}
	}

	// end of WPPFM_Product_Feed_Attribute_Mapping_Wrapper class

endif;
