<?php

if (!class_exists('WPSE_WC_Importer_Controller')) {

	class WPSE_WC_Importer_Controller extends WC_Product_CSV_Importer_Controller {

		function __construct() {
			
		}

		/**
		 * Get mapping options.
		 *
		 * @param  string $item Item name.
		 * @return array
		 */
		function get_mapping_options($item = '') {
			return parent::get_mapping_options($item);
		}

		/**
		 * Get special columns.
		 *
		 * @param  array $columns Raw special columns.
		 * @return array
		 */
		function get_special_columns($columns) {
			return parent::get_special_columns($columns);
		}

	}

}