<?php

/**
 * WPPFM Product Feed Category Wrapper Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Product_Feed_Category_Wrapper' ) ) :

	class WPPFM_Product_Feed_Category_Wrapper extends WPPFM_Category_Wrapper {

		/**
		 * Display the product feed category mapping table.
		 *
		 * @return void
		 */
		public function display() {

			// Start with the section code.
			echo '<section class="wppfm-edit-feed-form-element-wrapper wppfm-category-mapping-wrapper" id="wppfm-category-map" style="display:none;">';
			echo '<div id="category-mapping-header" class="header"><h3>' . __( 'Category Mapping', 'wp-product-feed-manager' ) . ':</h3></div>';
			echo '<table class="fm-category-mapping-table widefat" cellspacing="0" id="category-mapping-table">';

			// The category mapping table header.
			echo WPPFM_Category_Selector_Element::category_selector_table_head( 'mapping' );

			echo '<tbody id="wppfm-category-mapping-body">';

			// The content of the table.
			echo $this->category_table_content( 'mapping' );

			echo '</tbody>';

			// Closing the section.
			echo '</table></section>';

			// Add the product filter element.
			echo $this->product_filter();
		}
	}

	// end of WPPFM_Product_Feed_Category_Wrapper class

endif;
