<?php

if (!class_exists('WPSE_WC_Exporter')) {

	class WPSE_WC_Exporter extends WC_Product_CSV_Exporter {

		function __construct() {
			
		}

		/**
		 * Export column headers in CSV format.
		 *
		 * @since 3.1.0
		 * @return string
		 */
		function get_export_column_headers() {

			// WooCommerce sets the attribute value in different order than the attribute column names.
			// the values come as taxonomy,visible. And column names as visible,taxonomy.
			// So we modify the list of column names to put the taxonomy name before the visible name.
			$new_names = array();
			$prev_id = null;
			foreach ($this->column_names as $column_id => $label) {
				$new_names[$column_id] = $label;
				if (strpos($column_id, 'attributes:taxonomy') !== false && is_string($prev_id) && strpos($prev_id, 'attributes:visible') !== false) {
					unset($new_names[$prev_id]);
					$new_names[$prev_id] = $this->column_names[$prev_id];
				}
				$prev_id = $column_id;
			}
			$this->column_names = $new_names;

			// Get the friendly column names
			$headers = parent::export_column_headers();
			return str_getcsv($headers);
		}

		/**
		 * Take a product and generate row data from it for export.
		 *
		 * @param WC_Product $product WC_Product object.
		 * @return array
		 */
		function generate_row_data($product) {
			return parent::generate_row_data($product);
		}

	}

}