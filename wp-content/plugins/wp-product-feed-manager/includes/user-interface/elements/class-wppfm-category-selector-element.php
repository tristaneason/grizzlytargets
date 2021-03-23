<?php

/**
 * WPPFM Category Selector Element Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.4.0
 * @version 1.0.0
 */

if ( ! defined('ABSPATH') )
{
	exit;
}

if ( ! class_exists('WPPFM_Category_Selector_Element') ) :

	class WPPFM_Category_Selector_Element {

		/**
		 * Returns the category mapping table head code.
		 *
		 * @param string $mode displays a normal category selector or a category mapping selector when 'mapping' is given. Default = 'normal'.
		 *
		 * @return string
		 */
		public static function category_selector_table_head($mode = 'normal') {
			$mode_column = 'mapping' === $mode ? __('Feed Category', 'wp-product-feed-manager') : __('Description', 'wp-product-feed-manager');

			return '<thead class="wp-list-table widefat fixed striped"><tr>
				<td id="shop-category-selector" class="manage-column column-cb check-column" style="width:5%;">
				<label class="screen-reader-text" for="wppfm-categories-select-all">Select All</label>
				<input id="wppfm-categories-select-all" type="checkbox">
				</td>
				<th scope="row" class="manage-column column-name col30w">' . __('Shop Category', 'wp-product-feed-manager') . '</th>
				<th scope="row" class="manage-column column-name col55w">' . $mode_column . '</th>
				<th scope="row" class="manage-column column-name col10w">' . __('Products', 'wp-product-feed-manager') . '</th>
				</tr></thead>';
		}

		/**
		 * Returns the code for a single row meant for the category mapping table.
		 *
		 * @param object $category object containing data of the active category like term_id and name
		 * @param string $category_children a string with the children of the active category
		 * @param string $level_indicator current active level
		 * @param string $mode defines if the category mapping row should contain a description (normal) or a category mapping (mapping) column
		 *
		 * @return string
		 */
		public static function category_mapping_row($category, $category_children, $level_indicator, $mode) {
			$category_row_class = 'mapping' === $mode ? 'wppfm-category-mapping-selector' : 'wppfm-category-selector';
			$mode_column        = 'mapping' === $mode
				? self::category_mapping_selector('catmap', $category->term_id, FALSE)
				: self::category_description_data_item($category->term_id);

			return '<tr id="category-' . $category->term_id . '"><th class="check-column" scope="row" id="shop-category-selector">
				<input class="' . $category_row_class . '" data-children="' . $category_children . '" id="feed-selector-' . $category->term_id . '"
				type="checkbox" value="' . $category->term_id . '" title="Select ' . $category->name . '">
				</th><td id="shop-category" class="col30w">' .
			       $level_indicator . $category->name .
			       '</td><td class="field-header col55w"><div id="feed-category-' . $category->term_id . '"></div>' .
			       $mode_column . '</td>
				<td class="category-count col10w">' . $category->category_count . '</td></tr>';
		}

		/**
		 * Returns the code for a category input selector.
		 *
		 * @param string $identifier identifier for the selector
		 * @param string $id id of the selector
		 * @param boolean $start_visible should this selector start visible
		 *
		 * @return string
		 */
		public static function category_mapping_selector($identifier, $id, $start_visible) {
			$display         = $start_visible ? 'initial' : 'none';
			$ident           = '-1' !== $id ? $identifier . '-' . $id : $identifier;
			$category_levels = apply_filters('wppfm_category_selector_level', 6);

			$html_code = '<div id="category-selector-' . $ident . '" style="display:' . $display . '">';
			$html_code .= '<div id="selected-categories"></div><select class="wppfm-main-input-selector wppfm-cat-selector" id="' . $ident . '_0" disabled></select>';

			for ( $i = 1; $i < $category_levels; $i ++ )
			{
				$html_code .= '<select class="wppfm-main-input-selector wppfm-cat-selector" id="' . $ident . '_' . $i . '" value="0" style="display:none;"></select>';
			}

			$html_code .= '<div>';

			return $html_code;
		}

		/**
		 * Returns the code for the category description column.
		 *
		 * @param string $category_id
		 *
		 * @return string
		 */
		private static function category_description_data_item($category_id) {
			$category_description = '' !== category_description($category_id) ? category_description($category_id) : '—';

			return '<span aria-hidden="true">' . $category_description . '</span>';
		}

		/**
		 * Returns the code for the product filter selector.
		 *
		 * @return string
		 */
		public static function product_filter_selector() {
			return '<section class="main-product-filter-wrapper" id="main-product-filter-wrapper" style="display:none;">
				<div class="product-filter-condition-wrapper">
				</div>
				</section>
				<section class=master-feed-filter-wrapper></section>';
		}
	}

	// end of WPPFM_Category_Selector_Element class

endif;
