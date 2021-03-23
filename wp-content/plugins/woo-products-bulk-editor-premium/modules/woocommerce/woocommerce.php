<?php

if (!defined('VGSE_WC_FILE')) {
	define('VGSE_WC_FILE', __FILE__);
}
if (!defined('VGSE_WC_DIR')) {
	define('VGSE_WC_DIR', __DIR__);
}
if (!class_exists('WP_Sheet_Editor_WooCommerce')) {

	/**
	 * Edit all your products information in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_WooCommerce {

		static private $instance = false;
		var $post_type = null;
		var $variations = null;
		var $core_columns_list = array();
		var $core_to_woo_importer_columns_list = array();
		var $special_columns_import_prefixes = array();

		private function __construct() {
			
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WooCommerce::$instance) {
				WP_Sheet_Editor_WooCommerce::$instance = new WP_Sheet_Editor_WooCommerce();
				WP_Sheet_Editor_WooCommerce::$instance->init();
			}
			return WP_Sheet_Editor_WooCommerce::$instance;
		}

		/**
		 * Convert a value to boolean
		 * @param str|bool $item
		 * @return boolean
		 */
		function _do_booleable($item) {
			if (in_array($item, array('yes', 'instock', 'open', '1', 1, true, 'true', 'on'), true)) {
				return true;
			}
			return false;
		}

		/**
		 * is woocommerce plugin active?
		 * @return boolean
		 */
		function is_woocommerce_active() {
			return function_exists('WC');
		}

		function init() {

			$this->post_type = apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product');

			// exit if woocommerce plugin is not active
			if (!$this->is_woocommerce_active()) {
				return;
			}

			$this->special_columns_import_prefixes = array(
				'downloads_name',
				'downloads_url',
				'attributes_name',
				'attributes_value',
				'attributes_taxonomy',
				'attributes_visible',
				'attributes_default',
			);
			$this->core_to_woo_importer_columns_list = array_merge(array(
				'ID' => 'ID',
				'post_content' => 'description',
				'post_title' => 'name',
				'post_excerpt' => 'short_description',
				'post_status' => 'published',
				'comment_status' => 'reviews_allowed',
				'post_parent' => 'parent_id',
				'post_type' => 'type',
				"_sale_price" => 'sale_price',
				"_sku" => 'sku',
				"_price" => 'price',
				"_sale_price_dates_from" => 'sale_price_dates_from',
				"_sale_price_dates_to" => 'sale_price_dates_to',
				"_tax_status" => 'tax_status',
				"_tax_class" => 'tax_class',
				"_manage_stock" => '_manage_stock',
				"_backorders" => 'backorders',
				"_low_stock_amount" => 'low_stock_amount',
				"_sold_individually" => 'sold_individually',
				"_weight" => 'weight',
				"_length" => 'length',
				"_width" => 'width',
				"_height" => 'height',
				"_upsell_ids" => 'upsell_ids',
				"_crosssell_ids" => 'crosssell_ids',
				"_purchase_note" => 'purchase_note',
				"_default_attributes" => 'attributes',
				"_virtual" => 'type',
				"_downloadable" => 'type',
				"_product_image_gallery" => 'images',
				"_download_limit" => 'download_limit',
				"_download_expiry" => 'download_expiry',
				"_stock" => 'stock',
				"_stock_status" => 'stock_status',
				"_downloadable_files" => 'downloads',
				"_product_attributes" => 'attributes',
				"_regular_price" => 'regular_price',
				"_thumbnail_id" => 'images',
				"_variation_description" => 'short_description',
				"_children" => '_children',
				"_product_url" => 'product_url',
				"_button_text" => 'button_text',
				"product_cat" => 'category_ids',
				"product_tag" => 'tag_ids',
				"product_visibility" => 'catalog_visibility',
				"product_type" => 'type',
				"_download_type" => 'download_type',
				"_featured" => 'featured',
				"default_attributes" => 'attributes',
				"_vgse_create_attribute" => 'attributes',
					), array_fill_keys(wc_get_attribute_taxonomy_names(), 'attributes'));


			// Include files
			require_once 'inc/attributes.php';
			require_once 'inc/variations.php';
			require_once 'inc/import-export.php';
			require_once 'inc/downloadable.php';
			require_once 'inc/formatting.php';
			$this->variations = WP_Sheet_Editor_WooCommerce_Variations::get_instance();

			$this->core_columns_list = array_unique(array_merge(array_keys($this->core_to_woo_importer_columns_list), WP_Sheet_Editor_WooCommerce_Variations::get_instance()->get_variation_whitelisted_columns()));


			// init wp hooks
			add_action('vg_sheet_editor/columns/all_items', array($this, 'filter_columns_settings'), 10, 3);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'));
			add_filter('vg_sheet_editor/allowed_post_types', array($this, 'allow_product_post_type'));
			add_filter('vg_sheet_editor/add_new_posts/create_new_posts', array($this, 'create_new_products'), 10, 3);

			add_filter('vg_sheet_editor/after_enqueue_assets', array($this, 'enqueue_assets'));
			add_filter('vg_sheet_editor/load_rows/full_output', array($this, 'calculate_inventory_totals'), 10, 2);
			add_filter('vg_sheet_editor/formulas/sql_execution/can_execute', array($this, 'disallow_formula_sql_execution_on_special_columns'), 10, 4);
			add_filter('vg_sheet_editor/columns/blacklisted_columns', array($this, 'disable_wc_private_columns'), 10, 2);
			add_filter('vg_sheet_editor/js_data', array($this, 'watch_cells_to_lock'), 10, 2);
			add_filter('vg_sheet_editor/filters/allowed_fields', array($this, 'register_filters'), 11, 2);
			add_filter('vg_sheet_editor/custom_columns/all_meta_keys', array($this, 'disable_serialized_keys_from_automatic_columns'), 10, 2);
			add_filter('vg_sheet_editor/formulas/execute/get_duplicate_ids', array($this, 'find_duplicate_skus_to_delete'), 10, 6);

			add_filter('redux/options/' . VGSE()->options_key . '/sections', array($this, 'add_settings_page_options'));
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		function add_settings_page_options($sections) {
			$sections[] = array(
				'icon' => 'el-icon-cogs',
				'title' => __('WooCommerce products', VGSE()->textname),
				'fields' => array(
					array(
						'id' => 'be_disable_woocommerce_inventory_stats',
						'type' => 'switch',
						'title' => __('Disable product inventory stats?', VGSE()->textname),
						'desc' => __('The WooCommerce products spreadsheet automatically generates inventory units and inventory price stats and shows the stats above the spreadsheet. This can slow down the sheet if you have several thousand products. Disable it if the spreadsheet is too slow or you see errors when loading rows.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'wc_product_attributes_is_not_visible',
						'type' => 'text',
						'title' => __('Product attributes not visible', VGSE()->textname),
						'desc' => __('The plugin will mark as visible all the attributes that DONT contain these keywords, enter multiple separated by comma. I.e. "car, airplane" would match "Car model, Car marker, Expensive Airplane, airplanes". This applies after editing a product in the spreadsheet cells.', VGSE()->textname),
					),
					array(
						'id' => 'wc_product_attributes_not_variation',
						'type' => 'text',
						'title' => __('Product attributes not used for variations', VGSE()->textname),
						'desc' => __('The plugin will mark as used for variations all the attributes that DONT contain these keywords, enter multiple separated by comma. I.e. "car, airplane" would match "Car model, Car marker, Expensive Airplane, airplanes". This applies after editing a product in the spreadsheet cells.', VGSE()->textname),
					),
				)
			);
			return $sections;
		}

		function find_duplicate_skus_to_delete($duplicate_ids_to_delete, $column, $post_type, $raw_form_data, $column_settings, $query) {
			global $wpdb;
			if ($post_type !== $this->post_type || $column !== '_sku') {
				return $duplicate_ids_to_delete;
			}

			$main_sql = str_replace(array("SQL_CALC_FOUND_ROWS  $wpdb->posts.*", 'SQL_CALC_FOUND_ROWS'), array("$wpdb->posts.ID", ''), substr($query->request, 0, strripos($query->request, 'ORDER BY')));
			$get_items_sql = "SELECT meta_value 'value', count(meta_value) 'count' FROM $wpdb->postmeta pm WHERE post_id IN ($main_sql) AND meta_key = '_sku' AND meta_value <> '' GROUP BY meta_value having count(*) >= 2";

			$items_with_duplicates = $wpdb->get_results($get_items_sql, ARRAY_A);
			// Get all items with duplicates, we use the main sql query and wrap it to add our own conditions
			// We iterate over each post containing duplicates, and we fetch all the duplicates.
			// Note. We use limit = count-1 to leave one item only 
			$duplicate_ids_to_delete = array();
			foreach ($items_with_duplicates as $item) {
				$get_all_duplicate_ids = "SELECT post_id FROM $wpdb->postmeta WHERE meta_value = '" . esc_sql($item['value']) . "' LIMIT " . ((int) $item['count'] - 1);
				$duplicate_ids_to_delete = array_merge($duplicate_ids_to_delete, $wpdb->get_col($get_all_duplicate_ids));
			}

			return $duplicate_ids_to_delete;
		}

		/**
		 * The custom columns module finds all the meta keys and registers columns for them.
		 * In this case we remove the "serialized fields" from the list because we already register
		 * special columns for them.
		 * 
		 * @param array $columns
		 * @param string $post_type
		 * @return array
		 */
		function disable_serialized_keys_from_automatic_columns($columns, $post_type) {
			if ($post_type === $this->post_type) {
				$disallowed_keys = array('_crosssell_ids', '_upsell_ids', '_product_attributes', '_downloadable_files');
				$columns = array_diff($columns, $disallowed_keys);
			}
			return $columns;
		}

		function register_filters($filters, $post_type) {

			if ($post_type === $this->post_type && isset($filters['post_parent'])) {
				unset($filters['post_parent']);
			}
			return $filters;
		}

		function watch_cells_to_lock($data, $post_type) {
			if ($post_type === $this->post_type) {
				$data['watch_cells_to_lock'] = true;
				if (empty($data['export_keys_mapping'])) {
					$data['export_keys_mapping'] = array();
				}
				$data['export_keys_mapping'] = array_merge($data['export_keys_mapping'], $this->core_to_woo_importer_columns_list);
			}
			return $data;
		}

		function disable_wc_private_columns($blacklisted_columns, $provider) {
			if ($provider === $this->post_type) {
				$blacklisted_columns = array_merge($blacklisted_columns, array(
					'_max_price_variation_id',
					'_max_regular_price_variation_id',
					'_max_sale_price_variation_id',
					'_max_variation_price',
					'_max_variation_regular_price',
					'_max_variation_sale_price',
					'_min_price_variation_id',
					'_min_regular_price_variation_id',
					'_min_sale_price_variation_id',
					'_min_variation_price',
					'_min_variation_regular_price',
					'_min_variation_sale_price',
					'^_price$',
					'^_visibility$',
					'_wc_attachment_source',
					'_product_version'
				));
			}
			return $blacklisted_columns;
		}

		function disallow_formula_sql_execution_on_special_columns($allowed, $formula, $column, $post_type) {
			if ($post_type !== $this->post_type) {
				return $allowed;
			}
			$disallowed = array();
			// When we change post type from product to variation, we need to 
			// migrate data so we're forced to use the slow formulas
			$disallowed[] = 'post_type';

			if (in_array($column['key'], $disallowed)) {
				$allowed = false;
			}
			return $allowed;
		}

		function get_product_type($product_id) {
			return VGSE()->helpers->get_current_provider()->get_item_terms($product_id, 'product_type');
		}

		function calculate_inventory_totals($data, $qry) {
			global $wpdb;
			if ($qry['post_type'] !== $this->post_type || !empty(VGSE()->options['be_disable_woocommerce_inventory_stats'])) {
				return $data;
			}

			// We use custom queries for performance reasons.

			$main_query_sql = $GLOBALS['wpse_main_query']->request;
			$main_products_ids_sql = str_replace(array(
				'SQL_CALC_FOUND_ROWS',
				"$wpdb->posts.*"
					), array(
				'',
				"$wpdb->posts.ID"
					), $main_query_sql);
			$main_products_ids_sql = substr($main_products_ids_sql, 0, strpos($main_products_ids_sql, ' ORDER BY '));
			if (empty($main_products_ids_sql)) {
				return;
			}
			$variable_products_ids_sql = "SELECT ID FROM $wpdb->posts WHERE post_parent IN (" . $main_products_ids_sql . ")";

			$meta_table_name = VGSE()->helpers->get_current_provider()->get_meta_table_name($this->post_type);

			$main_products_sql = "SELECT SUM(m1.meta_value) as stock, SUM(m1.meta_value * m2.meta_value) as price FROM $meta_table_name as m1 JOIN $meta_table_name as m2 ON m1.post_id = m2.post_id WHERE m1.meta_key = '_stock' AND m2.meta_key = '_regular_price' AND m1.post_id IN (" . $main_products_ids_sql . ") ";
			$variable_products_sql = "SELECT SUM(m1.meta_value) as stock, SUM(m1.meta_value * m2.meta_value) as price FROM $meta_table_name as m1 JOIN $meta_table_name as m2 ON m1.post_id = m2.post_id  WHERE m1.meta_key = '_stock' AND m2.meta_key = '_regular_price' AND m1.post_id IN (" . $variable_products_ids_sql . ") ";

			$main_products_results = $wpdb->get_row($main_products_sql, ARRAY_A);
			$variable_products_results = $wpdb->get_row($variable_products_sql, ARRAY_A);

			$total_units = $main_products_results['stock'] + $variable_products_results['stock'];
			$total_inventory_price = $main_products_results['price'] + $variable_products_results['price'];

			$data['total_inventory_units'] = $total_units;
			$data['total_inventory_price'] = wc_price($total_inventory_price);

			return $data;
		}

		function enqueue_assets() {
			$current_post = VGSE()->helpers->get_provider_from_query_string();

			if ($current_post !== $this->post_type) {
				return;
			}

			wp_enqueue_script('wp-sheet-editor-wc-attributes', plugins_url('/assets/js/init.js', VGSE_WC_FILE), array('jquery'), VGSE()->version);
			wp_localize_script('wp-sheet-editor-wc-attributes', 'vgse_wc_attr_data', array(
				'texts' => array(
					'variations_on_reload_needed' => __('We need to reload the spreadsheet rows to load the variations. Please save your changes first or you will lose those changes. Do you want to reload now?', VGSE()->textname),
					'variations_off_reload_needed' => __('We need to reload the Spreadsheet to remove the variations. Please save your changes first or you will lose those changes. Do you want to reload now?', VGSE()->textname),
				),
			));
		}

		/**
		 * Ejemplo de uso: $this->update_products_with_api( $this->convert_row_to_api_format( $rows ) );
		 */
		function update_products_with_api($product, $version = 1) {
			if (isset($product['ID'])) {
				$out = VGSE()->helpers->create_rest_request('PUT', '/wc/v' . $version . '/products/' . $product['ID'], $product);
			} else {
				$out = VGSE()->helpers->create_rest_request('POST', '/wc/v' . $version . '/products', $product);
			}
			return $out;
		}

		/**
		 * Allow woocomerce product post type
		 * @param array $post_types
		 * @return array
		 */
		function allow_product_post_type($post_types) {

			if (!isset($post_types[$this->post_type])) {
				$post_types[$this->post_type] = VGSE()->helpers->get_post_type_label($this->post_type);
			}
			return $post_types;
		}

		/**
		 * Modify spreadsheet columns settings.
		 * 
		 * It changes the names and settings of some columns.
		 * @param array $spreadsheet_columns
		 * @param string $post_type
		 * @param bool $exclude_formatted_settings
		 * @return array
		 */
		function filter_columns_settings($spreadsheet_columns) {

			if (!isset($spreadsheet_columns[$this->post_type])) {
				return $spreadsheet_columns;
			}

			if (!empty($spreadsheet_columns[$this->post_type]['post_excerpt'])) {
				$spreadsheet_columns[$this->post_type]['post_excerpt']['title'] = __('Short description', 'woocommerce');
				$spreadsheet_columns[$this->post_type]['post_excerpt']['formatted']['renderer'] = 'wp_tinymce';
			}
			if (!empty($spreadsheet_columns[$this->post_type]['comment_status'])) {
				$spreadsheet_columns[$this->post_type]['comment_status']['title'] = __('Enable reviews', 'woocommerce');
			}

			return $spreadsheet_columns;
		}

		/**
		 * Create new products using WC API
		 * @param array $post_ids
		 * @param str $post_type
		 * @param int $number
		 * @return array Post ids
		 */
		public function create_new_products($post_ids, $post_type, $number) {

			if ($post_type !== $this->post_type || !empty($post_ids)) {
				return $post_ids;
			}

			for ($i = 0; $i < $number; $i++) {
				$api_response = $this->update_products_with_api(array(
					'name' => __('...', VGSE()->textname),
					'status' => 'draft'
				));

				if ($api_response->status === 200 || $api_response->status === 201) {
					$api_data = $api_response->get_data();
					$post_ids[] = $api_data['id'];
				}
			}

			return $post_ids;
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			$post_type = $this->post_type;

			if (!in_array($post_type, $editor->args['enabled_post_types'])) {
				return;
			}

			$product_type_tax = 'product_type';
			$editor->args['columns']->register_item($product_type_tax, $post_type, array(
				'data_type' => 'post_terms',
				'column_width' => 150,
				'title' => __('Type', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => $product_type_tax, 'type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'),
			));
			$editor->args['columns']->register_item('_product_image_gallery', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_product_image_gallery', 'renderer' => 'html', 'readOnly' => true),
				'column_width' => 300,
				'supports_formulas' => true,
				'title' => __('Product gallery', 'woocommerce'),
				'type' => 'boton_gallery_multiple',
				'formatted' => array('data' => '_product_image_gallery', 'renderer' => 'html', 'readOnly' => true),
			));
			$editor->args['columns']->register_item('_sku', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('SKU', 'woocommerce'),
				'supports_formulas' => true,
			));

			$editor->args['columns']->register_item('_regular_price', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Regular price', 'woocommerce'),
				'supports_formulas' => true,
				'value_type' => 'number',
			));

			$editor->args['columns']->register_item('_sale_price', $post_type, array(
				'value_type' => 'number',
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Sale price', 'woocommerce'),
				'supports_formulas' => true,
			));


			$editor->args['columns']->register_item('_sale_price_dates_from', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Sale start date', 'woocommerce'),
				'supports_formulas' => true,
				'supports_sql_formulas' => false,
				'formatted' => array('data' => '_sale_price_dates_from', 'type' => 'date', 'dateFormat' => 'YYYY-MM-DD', 'correctFormat' => true, 'defaultDate' => '', 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1)),
			));

			$editor->args['columns']->register_item('_sale_price_dates_to', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Sale end date', 'woocommerce'),
				'supports_formulas' => true,
				'supports_sql_formulas' => false,
				'formatted' => array('data' => '_sale_price_dates_to', 'type' => 'date', 'dateFormat' => 'YYYY-MM-DD', 'correctFormat' => true, 'defaultDate' => '', 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1)),
			));
			$editor->args['columns']->register_item('_manage_stock', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Manage stock', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array(
					'data' => '_manage_stock',
					'type' => 'checkbox',
					'checkedTemplate' => 'yes',
					'uncheckedTemplate' => 'no',
				),
				'default_value' => 'no',
			));

			$editor->args['columns']->register_item('_stock_status', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Stock status', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array(
					'data' => '_stock_status',
					'type' => 'checkbox',
					'checkedTemplate' => 'instock',
					'uncheckedTemplate' => 'outofstock',
				),
				'default_value' => 'instock',
			));

			$editor->args['columns']->register_item('_stock', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 75,
				'title' => __('Stock', 'woocommerce'),
				'supports_formulas' => true,
			));

			$editor->args['columns']->register_item('_weight', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 100,
				'title' => __('Weight', 'woocommerce'),
				'supports_formulas' => true,
			));

			$editor->args['columns']->register_item('_width', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 100,
				'title' => __('Width', 'woocommerce'),
				'supports_formulas' => true,
			));

			$editor->args['columns']->register_item('_height', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 100,
				'title' => __('Height', 'woocommerce'),
				'supports_formulas' => true,
			));

			$editor->args['columns']->register_item('_length', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 100,
				'title' => __('Length', 'woocommerce'),
				'supports_formulas' => true,
			));
			$editor->args['columns']->register_item('_crosssell_ids', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Cross-sells', 'woocommerce'),
				'supports_formulas' => true,
			));
			$editor->args['columns']->register_item('_upsell_ids', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Upsells', 'woocommerce'),
				'supports_formulas' => true,
			));

			$visibility_taxonomy = 'product_visibility';
			$editor->args['columns']->register_item($visibility_taxonomy, $post_type, array(
				'data_type' => 'post_terms',
				'column_width' => 150,
				'title' => __('Visibility', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => $visibility_taxonomy, 'type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'),
			));



			$editor->args['columns']->register_item('_virtual', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Virtual', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => '_virtual',
					'type' => 'checkbox',
					'checkedTemplate' => 'yes',
					'uncheckedTemplate' => 'no',
				),
				'default_value' => 'no',
			));
			$editor->args['columns']->register_item('_sold_individually', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Sold individually', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => '_sold_individually',
					'type' => 'checkbox',
					'checkedTemplate' => 'yes',
					'uncheckedTemplate' => 'no',
				),
				'default_value' => 'no',
			));
			$editor->args['columns']->register_item('_featured', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Is featured?', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => '_featured',
					'type' => 'checkbox',
					'checkedTemplate' => 'featured',
					'uncheckedTemplate' => 'no',
				),
				'default_value' => 'no',
			));
			$editor->args['columns']->register_item('_backorders', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 150,
				'title' => __('Backorders allowed?', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => '_backorders',
					'editor' => 'select',
					'selectOptions' => array(
						'no' => __('Do not allow', 'woocommerce'),
						'notify' => __('Allow, but notify customer', 'woocommerce'),
						'yes' => __('Allow', 'woocommerce'),
					)
				),
				'default_value' => 'no',
			));

			$editor->args['columns']->register_item('_purchase_note', $post_type, array(
				'data_type' => 'meta_data',
				'column_width' => 250,
				'title' => __('Purchase note', 'woocommerce'),
				'supports_formulas' => true,
			));

			$shipping_tax_name = 'product_shipping_class';
			$editor->args['columns']->register_item($shipping_tax_name, $post_type, array(
				'data_type' => 'post_terms',
				'column_width' => 150,
				'title' => __('Shipping class', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array('data' => $shipping_tax_name, 'type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'),
			));


			$editor->args['columns']->register_item('_wc_average_rating', $post_type, array(
				'data_type' => 'meta_data',
				'title' => __('Average rating', 'woocommerce'),
				'allow_to_save' => false,
				'is_locked' => true
			));
			$editor->args['columns']->register_item('_wc_review_count', $post_type, array(
				'data_type' => 'meta_data',
				'title' => __('Review count', 'woocommerce'),
				'allow_to_save' => false,
				'is_locked' => true
			));
			$editor->args['columns']->register_item('total_sales', $post_type, array(
				'data_type' => 'meta_data',
				'title' => __('Total sales', 'woocommerce'),
				'allow_to_save' => false,
				'is_locked' => true
			));
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

	add_action('vg_sheet_editor/initialized', 'vgse_woocommerce_init');

	function vgse_woocommerce_init() {
		WP_Sheet_Editor_WooCommerce::get_instance();
		VGSE()->WC = WP_Sheet_Editor_WooCommerce::get_instance();
	}

}