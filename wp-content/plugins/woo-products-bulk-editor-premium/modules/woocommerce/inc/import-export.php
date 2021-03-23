<?php
if (!class_exists('WPSE_WC_Products_Universal_Sheet')) {

	class WPSE_WC_Products_Universal_Sheet {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {

			// Export
			add_filter('vg_sheet_editor/export/allowed_column_keys', array($this, 'allow_wc_core_columns_keys_for_export'), 10, 3);
			add_filter('vg_sheet_editor/export/final_headers', array($this, 'add_friendly_column_headers_for_export'), 10, 2);
			add_action('vg_sheet_editor/export/pre_cleanup', array($this, 'add_special_columns_data_to_export'), 10, 4);
			add_filter('vg_sheet_editor/export/columns', array($this, 'add_special_columns_to_export_list'), 10, 2);
			add_action('vg_sheet_editor/export/columns', array($this, 'remove_core_fields_from_export_list'), 20, 2);
			add_filter('vg_sheet_editor/columns/all_items', array($this, 'add_export_keys'));
			add_filter('vg_sheet_editor/export/existing_file_keys', array($this, 'convert_file_labels_to_keys_for_export'), 10, 4);
			add_filter('vg_sheet_editor/export/is_not_supported', array($this, 'is_import_export_supported'), 10, 2);

			// Import
			add_action('vg_sheet_editor/import/before_available_columns_options', array($this, 'add_special_columns_to_import_list'));
			add_action('vg_sheet_editor/import/columns', array($this, 'add_special_columns_to_api_import_list'), 10, 2);
			add_action('vg_sheet_editor/import/columns', array($this, 'remove_core_fields_from_export_list'), 20, 2);
			add_filter('vg_sheet_editor/import/wp_check/available_columns_options', array($this, 'filter_wp_check_options_for_import'), 10, 2);
			add_action('vg_sheet_editor/save_rows/row_data_before_save', array($this, 'save_columns_data_during_import'), 10, 5);
			add_action('vg_sheet_editor/save_rows/incoming_data', array($this, 'maybe_create_template_products'), 10, 2);
			add_action('vg_sheet_editor/save_rows/after_saving_rows', array($this, 'remove_placeholder_products_after_import'), 10, 4);
			add_filter('vg_sheet_editor/import/is_not_supported', array($this, 'is_import_export_supported'), 10, 2);
			add_filter('vg_sheet_editor/import/after_data_sources', array($this, 'render_import_sample_csv_link'));
			add_filter('vg_sheet_editor/import/find_post_id', array($this, 'find_product_id_for_import'), 10, 6);
			add_action('vg_sheet_editor/import/after_advanced_options', array($this, 'import_after_advanced_options'));
		}

		function import_after_advanced_options($post_type) {
			if ($post_type !== VGSE()->WC->post_type) {
				return;
			}
			?>
			<div class="field">
				<label><input type="checkbox" name="skip_broken_images" class="skip-broken-images"/> <?php _e('Skip broken images?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('By the default, the import stops when a product references a broken images and you have to correct the issue in the file and start a new import. Enable this option and we will let you import products without images when the image url is broken', VGSE()->textname); ?>">( ? )</a></label>								
			</div>
			<?php
		}

		function get_wc_product_core_columns_for_export() {
			$columns = $this->get_exporter()->get_default_column_names();
			$columns['downloads'] = __('Downloads', 'woocommerce');
			$columns['attributes'] = __('Attributes', 'woocommerce');

			$columns = apply_filters('vg_sheet_editor/woocommerce/export/columns_list', $columns);
			return $columns;
		}

		function add_special_columns_to_export_list($columns, $post_type) {
			if ($post_type !== VGSE()->WC->post_type) {
				return $columns;
			}

			$sheet_to_wc_keys = array_flip(VGSE()->WC->core_to_woo_importer_columns_list);
			$special_columns = $this->get_wc_product_core_columns_for_export();
			$new_columns = array();
			foreach ($special_columns as $column_id => $column_name) {
				$old_column_args = (isset($sheet_to_wc_keys[$column_id]) && isset($columns[$sheet_to_wc_keys[$column_id]])) ? $columns[$sheet_to_wc_keys[$column_id]] : array();
				$new_columns[$column_id] = array_merge($old_column_args, array(
					'title' => $column_name,
					'key' => $column_id
				));
			}

			$out = array_merge($new_columns, $columns);
			return $out;
		}

		function add_special_columns_to_api_import_list($columns, $post_type) {
			if ($post_type !== VGSE()->WC->post_type || !VGSE()->helpers->is_rest_request()) {
				return $columns;
			}

			$sheet_to_wc_keys = array_flip(VGSE()->WC->core_to_woo_importer_columns_list);
			$importer_controller = $this->get_importer_controller();
			$new_columns = array();
			foreach ($importer_controller->get_mapping_options('') as $key => $value) {
				if (is_array($value)) {
					foreach ($value['options'] as $sub_key => $sub_value) {
						$new_columns[$sub_key] = array(
							'title' => $sub_value,
							'key' => $sub_key
						);
					}
				} else {
					$new_columns[$key] = array(
						'title' => $value,
						'key' => $key
					);
				}
			}
			foreach ($new_columns as $column_id => $column_args) {
				$old_column_args = (isset($sheet_to_wc_keys[$column_id]) && isset($columns[$sheet_to_wc_keys[$column_id]])) ? $columns[$sheet_to_wc_keys[$column_id]] : array();
				$new_columns[$column_id] = array_merge($old_column_args, $column_args);
			}

			$out = array_merge($new_columns, $columns);
			return $out;
		}

		function add_export_keys($columns) {
			if (isset($columns[VGSE()->WC->post_type])) {
				foreach ($columns[VGSE()->WC->post_type] as $column_key => $column) {
					if (isset(VGSE()->WC->core_to_woo_importer_columns_list[$column_key])) {
						$columns[VGSE()->WC->post_type][$column_key]['export_key'] = VGSE()->WC->core_to_woo_importer_columns_list[$column_key];
					}
				}
			}
			return $columns;
		}

		function filter_wp_check_options_for_import($columns, $post_type) {

			if ($post_type === VGSE()->WC->post_type) {
				// The array elements contain the <option> html, so we use str_replace to change the option key
				$columns = array(
					'ID' => $columns['ID'],
					'sku' => str_replace('_sku', 'sku', $columns['_sku']),
					'name' => str_replace('post_title', 'name', $columns['post_title'])
				);
			}
			return $columns;
		}

		// Note. The $row uses the same keys as the WooCommerce core importer
		// and in order to receive those keys, we need to use the wp filter to rename the 
		// option keys selected by the user: vg_sheet_editor/import/wp_check/available_columns_options.
		// See filter_wp_check_options_for_import() se example
		function find_product_id_for_import($post_id, $row, $post_type, $meta_query, $writing_type, $check_wp_fields) {

			if ($post_type === VGSE()->WC->post_type) {
				$post_id = 0;

				if (!empty($row['ID']) && in_array('ID', $check_wp_fields)) {
					$post_id = get_post_status($row['ID']) ? (int) $row['ID'] : 0;
				} elseif (!empty($row['sku']) && in_array('sku', $check_wp_fields)) {
					$post_id = (int) wc_get_product_id_by_sku(str_replace('&', 'and', $row['sku']));
				} elseif (!empty($row['name']) && in_array('name', $check_wp_fields)) {
					$post = get_page_by_title($row['name'], OBJECT, $post_type);
					if ($post) {
						$post_id = $post->ID;
					}
				}
			}
			return $post_id;
		}

		function render_import_sample_csv_link($post_type) {

			if ($post_type !== VGSE()->WC->post_type) {
				return;
			}
			?>

			<p><?php printf(__('Here is a <a href="%s" target="_blank">sample CSV</a> containing all types of products, including simple, grouped, <br>variable products; variations, attributes, product downloads, and more.', VGSE()->textname), 'https://github.com/woocommerce/woocommerce/blob/master/sample-data/sample_products.csv'); ?></p>
			<?php
		}

		function is_import_export_supported($supported, $post_type) {

			if ($post_type === VGSE()->WC->post_type && version_compare(WC()->version, '3.1.0') < 0) {
				$supported = false;
			}
			return $supported;
		}

		function maybe_create_template_products($data, $settings) {

			if ($settings['post_type'] !== VGSE()->WC->post_type || empty($settings['wpse_source']) || $settings['wpse_source'] !== 'import') {
				return $data;
			}


			if (!empty($settings['allow_to_create_new'])) {
				$rows_missing_ids = wp_list_filter($data, array('ID' => null));
				foreach ($rows_missing_ids as $row_index => $item) {
					$row_id = null;
					$row_sku = null;
					if (!empty($item['sku'])) {
						$row_sku = str_replace('&', 'and', $item['sku']);
						$row_id = wc_get_product_id_by_sku($row_sku);
					}
					if (!$row_id) {
						$product = new WC_Product_Simple();
						$product->set_name('Import placeholder');
						$product->set_status('importing');

						// If row has a SKU, make sure placeholder has it too.
						if ($row_sku) {
							$product->set_sku($row_sku);
						}
						$row_id = $product->save();
					}
					if ($row_id) {
						$data[$row_index]['ID'] = $row_id;
					}
				}
			}

			return $data;
		}

		function get_importer($args = array()) {
			$this->include_importer();
			$importer = new WPSE_WC_Importer($args);
			return $importer;
		}

		function get_importer_controller() {
			$this->include_importer();
			include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
			require_once VGSE_WC_DIR . '/inc/wc-core-importer-controller.php';
			$importer_controller = new WPSE_WC_Importer_Controller();
			return $importer_controller;
		}

		function get_exporter() {

			if (!class_exists('WC_Product_CSV_Exporter')) {
				include_once WC_ABSPATH . 'includes/export/class-wc-product-csv-exporter.php';
			}
			require_once VGSE_WC_DIR . '/inc/wc-core-exporter.php';
			$exporter = new WPSE_WC_Exporter();
			return $exporter;
		}

		function include_importer() {
			if (!class_exists('WP_Importer')) {
				$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

				if (file_exists($class_wp_importer)) {
					require $class_wp_importer;
				}
			}
			if (!class_exists('WC_Product_CSV_Importer')) {
				include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
			}
			require_once VGSE_WC_DIR . '/inc/wc-core-importer.php';
		}

		function save_columns_data_during_import($data, $post_id, $post_type, $spreadsheet_columns, $settings) {
			if ($post_type !== VGSE()->WC->post_type || empty($settings['wpse_source']) || $settings['wpse_source'] !== 'import') {
				return $data;
			}

			$original_data = $data;

			$product_type = !empty($data['type']) ? $data['type'] : VGSE()->WC->get_product_type($data['id']);

			// Convert the special column keys from attribute_name to attribute:name,
			// required by the WC importer class.
			// When we import on wp-admin, the columns already have the attribute:name syntax.
			// we need this when we import through the REST API.
			foreach ($data as $key => $value) {
				$key_without_number = preg_replace("/[^a-zA-Z_]/", "", $key);
				if (in_array($key_without_number, VGSE()->WC->special_columns_import_prefixes)) {
					$data[str_replace('_', ':', $key)] = $value;
					unset($data[$key]);
				}

				// WC uses the ID as id
				if (!empty($data['ID'])) {
					$data['id'] = $data['ID'];
				}

				// Make sure there is a default attribute always, otherwise WC won't save the variations
				if (strpos($key, 'attributes:value') !== false && $product_type === 'variable') {
					$default_attribute_key = str_replace('attributes:value', 'attributes:default', $key);
					if (empty($data[$default_attribute_key])) {
						$data[$default_attribute_key] = current(array_map('trim', explode(',', $value)));
					}
				}
			}

			// Weird bug on WC's side. It returns a "SKU duplicated" error when 
			// the SKU contains & and it's not really duplicated.
			if (!empty($data['sku'])) {
				$data['sku'] = str_replace('&', 'and', $data['sku']);
			}


			// If the user allows skipping broken images, we try to download them with our CORE function
			// and save the downloaded ids, this way WooCommerce won't stop the import if the images fail
			if (!empty($settings['wpse_import_settings']['skip_broken_images']) && !empty($data['images'])) {
				$image_ids = VGSE()->helpers->maybe_replace_urls_with_file_ids($data['images']);
				$data['images'] = $image_ids;
			}

			$update_existing = get_post_status($data['ID']) !== 'importing';

			$mapping = array_combine(array_keys($data), array_keys($data));
			// The mapping somehow contains an element with empty key, 
			// we can't remove it, if we remove it the mapping breaks in the WC importer class
			// Save row
			$importer = $this->get_importer(array(
				'data' => array($data), // This is designed to import multiple rows at once, we import one in this case
				'mapping' => $mapping, // wpse already mapped the fields
				'update_existing' => $update_existing
			));
			$result = $importer->import();

			if (!empty($result['skipped'])) {
				return current($result['skipped']);
			}
			if (!empty($result['failed'])) {
				return current($result['failed']);
			}

			// Remove the special columns from the $data, so sheet editor core saves the other fields only
			$data = array_diff_key($data, VGSE()->helpers->array_flatten($this->get_importer_controller()->get_mapping_options('')));
			if (isset($data[''])) {
				unset($data['']);
			}

			// The ID should not be removed
			if (!isset($data['ID'])) {
				$data['ID'] = $original_data['ID'];
			}

			return $data;
		}

		function allow_wc_core_columns_keys_for_export($column_keys, $cleaned_rows, $clean_data) {
			if ($clean_data['post_type'] === VGSE()->WC->post_type && !empty($GLOBALS['wpse_wc_last_exported_keys'])) {
				$column_keys = array_unique(array_merge($column_keys, array_keys($GLOBALS['wpse_wc_last_exported_keys'])));
			}
			return $column_keys;
		}

		function convert_file_labels_to_keys_for_export($existing_file_keys, $first_row, $cleaned_rows, $clean_data) {
			if ($clean_data['post_type'] === VGSE()->WC->post_type && !empty($GLOBALS['wpse_wc_last_exported_keys'])) {

				foreach ($existing_file_keys as $index => $header) {
					$column_key = array_search($header, $GLOBALS['wpse_wc_last_exported_keys']);
					if ($column_key !== false) {
						$existing_file_keys[$index] = $column_key;
					}
				}
			}
			return $existing_file_keys;
		}

		function add_friendly_column_headers_for_export($headers, $clean_data) {
			if ($clean_data['post_type'] === VGSE()->WC->post_type && !empty($GLOBALS['wpse_wc_last_exported_keys'])) {
				foreach ($headers as $index => $header) {
					if (isset($GLOBALS['wpse_wc_last_exported_keys'][$header])) {
						$headers[$index] = $GLOBALS['wpse_wc_last_exported_keys'][$header];
					}
				}
			}

			return $headers;
		}

		function remove_placeholder_products_after_import($data, $post_type, $spreadsheet_columns, $settings) {
			global $wpdb;
			if ($post_type !== VGSE()->WC->post_type || empty($settings['wpse_source']) || $settings['wpse_source'] !== 'import') {
				return;
			}

			$placeholder_ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = '" . VGSE()->WC->post_type . "' AND post_status = 'importing' ");
			foreach ($placeholder_ids as $post_id) {
				wp_delete_post($post_id, true);
			}
		}

		function add_special_columns_data_to_export($cleaned_rows, $clean_data, $wp_query_args, $spreadsheet_columns) {

			if ($clean_data['post_type'] === VGSE()->WC->post_type && !empty($clean_data['custom_enabled_columns'])) {
				$exporter = $this->get_exporter();
				$exporter->set_column_names(wp_unslash($this->get_exporter()->get_default_column_names()));
				$exporter->set_columns_to_export(wp_unslash(explode(',', $clean_data['custom_enabled_columns']))); // WPCS: input var ok, sanitization ok.

				$all_exported_keys = array();
				foreach ($cleaned_rows as $cleaned_row_index => $cleaned_row) {
					$new_data = $exporter->generate_row_data(wc_get_product($cleaned_row['ID']));
					// WPSE core has the ID key, remove duplicate from WC
					if (isset($new_data['id'])) {
						unset($new_data['id']);
						unset($new_data['ID']);
					}
					$all_exported_keys = array_unique(array_merge($all_exported_keys, array_keys($new_data)));
					$cleaned_rows[$cleaned_row_index] = array_merge($cleaned_row, $new_data);
				}

				$column_headers = $exporter->get_export_column_headers();
				$id_index = array_search('ID', $column_headers, true);
				if ($id_index !== false) {
					unset($column_headers[$id_index]);
				}

				$GLOBALS['wpse_wc_last_exported_keys'] = array_combine($all_exported_keys, $column_headers);
			}

			return $cleaned_rows;
		}

		function add_special_columns_to_import_list($post_type) {
			if ($post_type !== VGSE()->WC->post_type) {
				return;
			}
			$mapped_value = '';
			$importer_controller = $this->get_importer_controller();
			?>
			<?php foreach ($importer_controller->get_mapping_options($mapped_value) as $key => $value) : ?>
				<?php if (is_array($value)) : ?>
					<optgroup label="<?php echo esc_attr($value['name']); ?>">
						<?php foreach ($value['options'] as $sub_key => $sub_value) : ?>
							<option value="<?php echo esc_attr($sub_key); ?>" <?php selected($mapped_value, $sub_key); ?>><?php echo esc_html($sub_value); ?></option>
						<?php endforeach ?>
					</optgroup>
				<?php else : ?>
					<option value="<?php echo esc_attr($key); ?>" <?php selected($mapped_value, $key); ?>><?php echo esc_html($value); ?></option>
				<?php endif; ?>
			<?php endforeach ?>
			<?php
		}

		function remove_core_fields_from_export_list($column_options, $post_type) {
			if ($post_type !== VGSE()->WC->post_type) {
				return $column_options;
			}

			// Exclude variation custom fields from the list (not handled by WC)
			$core_columns_list = array_diff(VGSE()->WC->core_columns_list, WP_Sheet_Editor_WooCommerce_Variations::get_instance()->get_variation_meta_keys());
			$column_options = array_diff_key($column_options, array_flip($core_columns_list));

			return $column_options;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_WC_Products_Universal_Sheet::$instance) {
				WPSE_WC_Products_Universal_Sheet::$instance = new WPSE_WC_Products_Universal_Sheet();
				WPSE_WC_Products_Universal_Sheet::$instance->init();
			}
			return WPSE_WC_Products_Universal_Sheet::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_WC_Products_Universal_Sheet_Obj')) {

	function WPSE_WC_Products_Universal_Sheet_Obj() {
		return WPSE_WC_Products_Universal_Sheet::get_instance();
	}

}
WPSE_WC_Products_Universal_Sheet_Obj();
