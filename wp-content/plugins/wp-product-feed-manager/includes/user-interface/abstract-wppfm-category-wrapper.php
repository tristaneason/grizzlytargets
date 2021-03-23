<?php

/**
 * WPPFM Category Wrapper Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Category_Wrapper' ) ) :

	abstract class WPPFM_Category_Wrapper {

		protected abstract function display();

		/**
		 * Returns the code for the category mapping, containing all shop categories as rows.
		 *
		 * @param  string   $mode   displays a normal category selector or a category mapping selector when 'mapping' is given. Default = 'normal'.
		 * @return string
		 */
		protected function category_table_content( $mode = 'normal' ) {
			$shop_categories = WPPFM_Taxonomies::get_shop_categories_list();

			return $this->category_rows( $shop_categories, 0, $mode );
		}

		/**
		 * Returns the code for the product filter.
		 *
		 * @return string
		 */
		protected function product_filter() {
			return WPPFM_Category_Selector_Element::product_filter_selector();
		}

		private function category_rows( $shop_categories, $category_depth_level, $mode ) {
			$html_code       = '';
			$level_indicator = '';

			for ( $i = 0; $i < $category_depth_level; $i ++ ) {
				$level_indicator .= '— ';
			}

			if ( $shop_categories ) {
				foreach ( $shop_categories as $category ) {
					$category_children = $this->get_sub_categories( $category );

					$html_code .= WPPFM_Category_Selector_Element::category_mapping_row( $category, $category_children, $level_indicator, $mode );

					if ( $category->children && count( (array) $category->children ) > 0 ) {
						$html_code .= self::category_rows( $category->children, $category_depth_level + 1, $mode );
					}
				}
			} else {
				$html_code .= esc_html__( 'No shop categories found.', 'wp-product-feed-manager' );
			}

			return $html_code;
		}

		private function get_sub_categories( $category ) {
			$array_string = '';

			if ( $category->children && count( (array) $category->children ) ) {
				$array_string .= '[';

				foreach ( $category->children as $child ) {
					$array_string .= $child->term_id . ', ';
				}

				$array_string  = substr( $array_string, 0, - 2 );
				$array_string .= ']';
			}

			return $array_string;
		}
	}

	// end of WPPFM_Category_Wrapper class

endif;
