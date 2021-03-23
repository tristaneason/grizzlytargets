<?php

if (!class_exists('WP_Sheet_Editor_WooCommerce_Variations')) {

	/**
	 * Display woocommerce item in the toolbar to tease users of the free 
	 * version into purchasing the premium plugin.
	 */
	class WP_Sheet_Editor_WooCommerce_Variations {

		static private $instance = false;
		var $post_type = null;
		var $variation_post_type = 'product_variation';
		var $wc_variation_columns = null;
		var $posts_to_inject_query = null;

		private function __construct() {
			
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items($editor) {

			$editor->args['toolbars']->register_item('create_variation', array(
				'type' => 'button', // html | switch | button
				'content' => __('Create variations', VGSE()->textname),
				'id' => 'create-variation',
				'help_tooltip' => __('Create and copy variations for variable products.', VGSE()->textname),
				'extra_html_attributes' => 'data-remodal-target="create-variation-modal"',
				'footer_callback' => array($this, 'render_create_variation_modal')
					), $this->post_type);

			$editor->args['toolbars']->register_item('display_variations', array(
				'type' => 'switch', // html | switch | button
				'content' => __('Display variations', VGSE()->textname),
				'id' => 'display-variations',
				'help_tooltip' => __('When this is enabled the products variations will be displayed and you will be able to edit them.', VGSE()->textname),
				'default_value' => false
					), $this->post_type);
		}

		/**
		 * Add a lock icon to the cells enabled for variations or products.
		 * 
		 * @param array $posts Rows for display in spreadsheet
		 * @param array $wp_query Arguments used to query the posts.
		 * @param array $spreadsheet_columns
		 * @param array $request_data Data received in the ajax request
		 * @return array
		 */
		function maybe_lock_general_columns_in_variations($posts, $wp_query, $spreadsheet_columns, $request_data) {
			if (VGSE()->helpers->get_provider_from_query_string() !== $this->post_type || empty($posts) || !is_array($posts) || VGSE()->helpers->is_plain_text_request()) {
				return $posts;
			}
			VGSE()->helpers->profile_record('Before ' . __FUNCTION__);

			$products = wp_list_filter($posts, array(
				'post_type' => $this->post_type
			));
			// We need at least one parent product to detect the parent vs variations columns and lock them
			if (empty($products)) {
				return $posts;
			}
			$first_product_keys = array_keys(current($products));

			$whitelist_variations = $this->get_variation_whitelisted_columns();
			$columns_with_visibility = array_keys($spreadsheet_columns);

			// Lock keys on variation rows for fields used in parent products that are not used in variations
			$locked_keys_in_variations = array_intersect(array_diff($first_product_keys, $whitelist_variations), $columns_with_visibility);

			// Lock keys on parent rows for fields used in variations that are not used by parent products
			$locked_keys_in_general = array_intersect(array_diff($whitelist_variations, $first_product_keys), $columns_with_visibility);

			$locked_keys_in_variations = apply_filters('vg_sheet_editor/woocommerce/locked_keys_in_variations', $locked_keys_in_variations, $whitelist_variations);
			$lock_icon = '<i class="fa fa-lock vg-cell-blocked vg-variation-lock"></i>';

			foreach ($posts as $index => $post) {


				if ($post['post_type'] === $this->post_type) {
					$locked_keys = $locked_keys_in_general;
				} else {
					$locked_keys = $locked_keys_in_variations;
				}
				if (isset($posts[$index]['_stock'])) {
					$posts[$index]['_stock'] = (int) $posts[$index]['_stock'];
				}
				$product_type = !empty($post['product_type']) ? $post['product_type'] : VGSE()->WC->get_product_type($post['ID']);
				// We are locking keys here because the automatic locking works with fields 
				// used by all parent products or all variations, not fields used by some parents only.
				// That's why in this case, we need to check the product type and disable them manually
				if ($product_type === 'variable') {
					$locked_keys[] = '_regular_price';
					$locked_keys[] = '_sale_price';
					$locked_keys[] = '_sale_price_dates_from';
					$locked_keys[] = '_sale_price_dates_to';
				}
				$posts[$index] = array_merge($posts[$index], array_fill_keys(array_diff($locked_keys, array_keys($post)), ''));
				foreach ($locked_keys as $locked_key) {

					if (strpos($posts[$index][$locked_key], 'vg-cell-blocked') !== false) {
						continue;
					}
					if (in_array($locked_key, array('title', 'post_title'))) {
						$posts[$index][$locked_key] = $lock_icon . ' ' . $posts[$index][$locked_key];
					} else {
						$posts[$index][$locked_key] = $lock_icon;
					}
				}
			}

			VGSE()->helpers->profile_record('After ' . __FUNCTION__);
			return $posts;
		}

		/**
		 * Are variations enabled in the spreadsheet according to the request data?
		 * @param str $post_type
		 * @param array $request_data Data received in the ajax request
		 * @return boolean
		 */
		function variations_enabled($post_type = null, $request_data = null) {
			if (!$post_type) {
				$post_type = VGSE()->helpers->get_provider_from_query_string();
			}
			if (empty($request_data) || empty($request_data['filters'])) {
				$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters();
			} elseif (!empty($request_data['filters'])) {
				parse_str(html_entity_decode($request_data['filters']), $filters);
			} else {
				return false;
			}

			if (empty($filters['wc_display_variations']) || $post_type !== $this->post_type) {
				return false;
			}

			return true;
		}

		/**
		 * Include variations posts to the posts list before processing.
		 * 
		 * Note. The search variations logic is very good because it allows pagination by variations
		 * but we can't use it without searching because it would exclude the non-variable products.
		 * 
		 * @param type $posts
		 * @param type $wp_query
		 * @param array $request_data Data received in the ajax request
		 * @return array
		 */
		function maybe_include_variations_posts($posts, $wp_query, $request_data) {

			if (!$this->variations_enabled(null, $request_data) || empty($posts) || !is_array($posts)) {
				return $posts;
			}

			// If this is a variations search, the main query contains variations 
			// and we need to include the parent products
			if (!empty($wp_query['wpse_search_variations'])) {
				unset($wp_query['wpse_search_variations']);
				// We merge args with $wp_query to use the special search parameters
				$posts_to_inject_query = new WP_Query(array(
					'post_type' => $this->post_type,
					'nopaging' => true,
					'post_status' => 'any',
					'post__in' => array_unique(wp_list_pluck($posts, 'post_parent')),
				));

				if (!$posts_to_inject_query->have_posts()) {
					return $posts;
				}

				// Cache list of variations for future use
				$this->posts_to_inject_query = $posts_to_inject_query;

				$new_posts = array();

				foreach ($posts_to_inject_query->posts as $post) {
					$new_posts[] = $post;

					$product_variations = wp_list_filter($posts, array(
						'post_parent' => $post->ID
					));

					$new_posts = array_merge($new_posts, $product_variations);
				}
			} else {
				// We merge args with $wp_query to use the special search parameters
				$posts_to_inject_query = new WP_Query(array(
					'post_type' => 'product_variation',
					'nopaging' => true,
					'post_parent__in' => wp_list_pluck($posts, 'ID'),
					'orderby' => array('menu_order' => 'ASC', 'ID' => 'ASC'),
				));

				if (!$posts_to_inject_query->have_posts()) {
					return $posts;
				}

				// Cache list of variations for future use
				$this->posts_to_inject_query = $posts_to_inject_query;

				$new_posts = array();
				$wc_default_non_variable_types = array('simple', 'grouped', 'external');

				foreach ($posts as $post) {
					$new_posts[] = $post;

					if (in_array(VGSE()->WC->get_product_type($post->ID), $wc_default_non_variable_types, true)) {
						continue;
					}

					$product_variations = wp_list_filter($posts_to_inject_query->posts, array(
						'post_parent' => $post->ID
					));

					$new_posts = array_merge($new_posts, $product_variations);
				}
			}
			return $new_posts;
		}

		function init() {
			$this->post_type = apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product');

			$this->wc_variation_only_columns = array(
				'_vgse_variation_enabled',
				'_variation_description',
			);

			// We need to set the properties
			$this->get_variation_whitelisted_columns();

			// Register toolbar button to enable the display of variations and create variations
			add_action('vg_sheet_editor/editor/before_init', array(
				$this,
				'register_toolbar_items'
			));

			// Filter load_rows to include variations if toolbar item is enabled.
			// The general fields will contain the same info as the parent post.
			add_action('vg_sheet_editor/load_rows/output', array(
				$this,
				'maybe_modify_variations_output'
					), 10, 4);

			// Filter load_rows to preload variations custom data
			add_action('vg_sheet_editor/load_rows/found_posts', array(
				$this,
				'maybe_include_variations_posts'
					), 10, 3);

			// Filter load_rows output to remove data in general columns and display a lock icon instead, also modify some columns values
			add_action('vg_sheet_editor/load_rows/output', array(
				$this,
				'maybe_lock_general_columns_in_variations'
					), 10, 4);


			// Exclude variations from the saving list
			add_action('vg_sheet_editor/save_rows/incoming_data', array(
				$this,
				'exclude_variations_from_saving_list'
					), 10, 2);

			// Save variations
			add_action('vg_sheet_editor/save_rows/response', array(
				$this,
				'maybe_save_variations'
					), 10, 5);

			// Create variations via ajax
			add_action('wp_ajax_vgse_create_variations', array(
				$this,
				'create_variations_rows'
			));

			// Save default attributes via ajax
			add_action('wp_ajax_vgse_save_default_attributes', array(
				$this,
				'update_default_attributes'
			));

			// When loading posts, disable product columns in variations
			add_action('vg_sheet_editor/load_rows/allowed_post_columns', array(
				$this,
				'disable_general_columns_for_variations'
					), 10, 2);

			// When we create the products in the spreadsheet, the variations will be enabled automatically
			// so we display the new rows with the right cells disabled
			add_action('vg_sheet_editor/add_new_posts/get_rows_args', array(
				$this,
				'enable_variations_when_fetching_created_rows'
					), 10, 2);

			add_action('woocommerce_rest_save_product_variation', array($this, 'add_variation_meta_after_copy'), 10, 3);
			add_filter('vg_sheet_editor/provider/post/get_items_terms', array($this, 'get_variation_attributes'), 10, 3);

			add_filter('vg_sheet_editor/filters/after_fields', array($this, 'add_search_on_variations_field'), 10, 2);
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'search_on_variations_query'), 20, 2);
			add_filter('posts_clauses', array($this, 'search_on_variations_query_sql'), 10, 2);
			add_filter('vg_sheet_editor/handsontable_cell_content/existing_value', array($this, 'get_default_attributes_for_cell'), 10, 3);
			add_filter('vg_sheet_editor/save_rows/row_data_before_save', array($this, 'save_default_attributes_from_cell'), 10, 3);
			add_filter('vg_sheet_editor/custom_columns/all_meta_keys', array($this, 'disable_default_attributes_from_custom_columns'), 10, 2);
			add_filter('vg_sheet_editor/custom_columns/all_meta_keys', array($this, 'register_custom_meta_columns'), 10, 3);
			add_action('woocommerce_variable_product_before_variations', array($this, 'render_variations_metabox_quick_access'));

			add_filter('vg_sheet_editor/bootstrap/post_type_column_dropdown_options', array($this, 'add_variation_to_post_types_dropdown'), 10, 2);
			add_action('wp_ajax_vgse_get_product_variations', array($this, 'get_product_variations'));
			// Force WC to generate variation titles with all attributes, even when having a lot of attributes
			// because the spreadsheet needs it for "delete duplicates" based on title and some search functionality
			add_filter('woocommerce_product_variation_title_include_attributes', '__return_true', 99999);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'));
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			$post_type = $this->post_type;


			if (!in_array($post_type, $editor->args['enabled_post_types'])) {
				return;
			}
			$editor->args['columns']->register_item('_variation_description', $post_type, array(
				'key' => '_variation_description',
				'data_type' => 'post_meta',
				'column_width' => 175,
				'title' => __('Variation description', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array(
					'data' => '_variation_description',
				),
				'default_value' => '',
			));
			$editor->args['columns']->register_item('_vgse_variation_enabled', $post_type, array(
				'key' => '_vgse_variation_enabled',
				'data_type' => 'post_data',
				'column_width' => 140,
				'title' => __('Variation enabled?', 'woocommerce'),
				'supports_formulas' => true,
				'formatted' => array(
					'data' => '_vgse_variation_enabled',
					'type' => 'checkbox',
					'checkedTemplate' => 'on',
					'uncheckedTemplate' => ''
				),
				'default_value' => 'on',
			));

			$editor->args['columns']->register_item('_default_attributes', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_default_attributes', 'renderer' => 'html'),
				'column_width' => 160,
				'title' => __('Default attributes', 'woocommerce'),
				'type' => 'handsontable',
				'edit_button_label' => __('Default attributes', 'woocommerce'),
				'edit_modal_id' => 'vgse-default-attributes',
				'edit_modal_title' => __('Default attributes', 'woocommerce'),
				'edit_modal_description' => sprintf(__('Attributes appear as dropdowns in the product page where the user can select the variation colors, sizes, and any attribute. Here you can define the default options selected in the dropdowns.<br>Separate values with the character %s<br/>This only works for Variable Products and it must have variations, otherwise the default attributes won´t be saved.</span>', 'woocommerce'), WC_DELIMITER),
				'edit_modal_save_action' => 'vgse_save_default_attributes',
				'edit_modal_get_action' => 'vgse_save_default_attributes',
				'edit_modal_local_cache' => false,
				'handsontable_columns' => array(
					$this->post_type => array(
						array(
							'data' => 'name'
						),
						array(
							'data' => 'option'
						),
					)),
				'handsontable_column_names' => array(
					$this->post_type => array(
						__('Name', 'woocommerce'),
						__('Value', 'woocommerce')
					)
				),
				'handsontable_column_widths' => array(
					$this->post_type => array(160, 300),
				),
				'supports_formulas' => true,
				'forced_supports_formulas' => true,
				'supported_formula_types' => array('clear_value'),
				'key_for_formulas' => '_default_attributes',
				'formatted' => array('data' => '_default_attributes', 'renderer' => 'html'),
				'use_new_handsontable_renderer' => true
			));
		}

		function add_variation_to_post_types_dropdown($post_types, $post_type) {
			if ($post_type === $this->post_type && !isset($post_types[$this->variation_post_type])) {
				$post_types[$this->variation_post_type] = $this->variation_post_type;
			}
			return $post_types;
		}

		function render_variations_metabox_quick_access() {
			global $post;
			$spreadsheet_url = add_query_arg(array(
				'wpse_custom_filters' => array(
					'keyword' => $post->post_title,
					'search_variations' => 'on',
					'wc_display_variations' => 'yes',
				)
					), VGSE()->helpers->get_editor_url('product'));
			include VGSE_WC_DIR . '/views/variation-metabox-shortcut.php';
		}

		function register_custom_meta_columns($meta_keys, $post_type, $editor) {

			if ($post_type !== $this->post_type) {
				return $meta_keys;
			}
			$variation_meta_keys = $this->get_variation_meta_keys();
			$meta_keys = array_unique(array_merge($meta_keys, $variation_meta_keys));
			return $meta_keys;
		}

		function disable_default_attributes_from_custom_columns($meta_keys, $post_type) {

			if ($post_type !== $this->post_type) {
				return $meta_keys;
			}

			$position = array_search('_default_attributes', $meta_keys);
			if ($position !== false) {
				unset($meta_keys[$position]);
			}
			return $meta_keys;
		}

		function save_default_attributes_from_cell($item, $post_id, $post_type) {
			if (!isset($item['_default_attributes']) || $post_type !== $this->post_type) {
				return $item;
			}
			$this->save_default_attributes(json_decode(wp_unslash($item['_default_attributes']), true), $post_id);
			unset($item['_default_attributes']);
			return $item;
		}

		function get_default_attributes_for_cell($value, $post, $column_key) {
			if ($column_key !== '_default_attributes' || empty($post->post_type) || $post->post_type !== $this->post_type) {
				return $value;
			}

			return $this->get_default_attributes($post->ID);
		}

		function add_variation_meta_after_copy($variation_id, $menu_order, $data) {
			if (empty($data['wpse_custom_meta'])) {
				return;
			}

			$existing_meta_keys = get_post_meta($variation_id);
			$overwrite_meta_keys = array('_variation_description');
			foreach ($data['wpse_custom_meta'] as $meta_key => $meta_values) {
				if (isset($existing_meta_keys[$meta_key]) && !in_array($meta_key, $overwrite_meta_keys)) {
					continue;
				}
				foreach ($meta_values as $value) {
					// Unserialize before saving to prevent double serialization done by WP
					$value = maybe_unserialize($value);
					if (in_array($meta_key, $overwrite_meta_keys)) {
						update_post_meta($variation_id, $meta_key, $value);
					} else {
						add_post_meta($variation_id, $meta_key, $value);
					}
				}
			}
		}

		function search_on_variations_query_sql($clauses, $wp_query) {
			global $wpdb;

			if (!empty($wp_query->query['wpse_search_variations'])) {

				$wp_query->query['wpse_product_query_vars']['post_parent__in'] = array(PHP_INT_MAX);
				$parent_query = new WP_Query($wp_query->query['wpse_product_query_vars']);
				$parent_sql_query = str_replace(array("AND $wpdb->posts.post_parent IN (" . PHP_INT_MAX . ")", "$wpdb->posts.ID FROM"), array('', "$wpdb->posts.ID, $wpdb->posts.post_date FROM"), $parent_query->request);

				$clauses['orderby'] = " parent.post_date DESC, " . $clauses['orderby'];
				$clauses['join'] .= " INNER JOIN ($parent_sql_query) as parent  ON parent.ID = $wpdb->posts.post_parent ";
			}
			return $clauses;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query
		 * @param array $data
		 * @return array
		 */
		function search_on_variations_query($query, $data) {
			$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters($data);
			if (empty($data['filters']) ||
					empty($filters['search_variations']) ||
					!isset($query['wpse_source']) ||
					!in_array($query['wpse_source'], array('load_rows', 'formulas'))) {
				return $query;
			}


			$query['wpse_search_variations'] = 1;
			$query['wpse_product_query_vars'] = $query;
			$query['post_type'] = $this->variation_post_type;
			$query['orderby'] = array('menu_order' => 'ASC', 'ID' => 'ASC');

			$query['wpse_product_query_vars']['fields'] = 'ids';
			$query['wpse_product_query_vars']['nopaging'] = true;

			$product_vars = array('post_status', 'tax_query', 'date_query', 'wpse_contains_keyword', 'wpse_not_contains_keyword');
			$variation_vars = array('post__in', 'meta_query', 'wpse_search_variations');

			if (!empty($filters['search_variations'])) {
				if (isset($query['tax_query'])) {
					if (!isset($query['meta_query'])) {
						$query['meta_query'] = array(
							'relation' => 'AND'
						);
					}

					foreach ($query['tax_query'] as $tax_query) {
						if (!isset($tax_query['taxonomy']) || strpos($tax_query['taxonomy'], 'pa_') !== 0) {
							continue;
						}

						$query['meta_query'][] = array(
							'key' => 'attribute_' . $tax_query['taxonomy'],
							'value' => $tax_query['terms'],
						);
					}
				}

				foreach ($product_vars as $product_var) {
					if (!empty($query[$product_var])) {
						unset($query[$product_var]);
					}
				}
				foreach ($variation_vars as $variation_var) {
					if (!empty($query['wpse_product_query_vars'][$variation_var])) {
						unset($query['wpse_product_query_vars'][$variation_var]);
					}
					if (!empty($query['wpse_product_query_vars']['wpse_original_filters']) && !empty($query['wpse_product_query_vars']['wpse_original_filters'][$variation_var])) {
						unset($query['wpse_product_query_vars']['wpse_original_filters'][$variation_var]);
					}
				}

				// We removed the meta_query from the product query and original filters above
				// Here we take the original_filters from the variation query and we 
				// will go through each meta query
				// The meta queries for attribute taxonomies will be added to the $query 
				// of variations and converted to meta search
				// and the meta queries for taxonomies that are not attributes are added 
				// to the $query of products and removed from the $query of variations
				// This way we can use the advanced filters freely. All meta filters applied to variations,
				// all taxonomies (except attributes) to parents, and attributes to variations

				if (!empty($query['wpse_original_filters']) && !empty($query['wpse_original_filters']['meta_query'])) {
					if (!isset($query['wpse_product_query_vars']['wpse_original_filters']['meta_query'])) {
						$query['wpse_product_query_vars']['wpse_original_filters']['meta_query'] = array();
					}
					foreach ($query['wpse_original_filters']['meta_query'] as $index => $meta_query) {
						if ($meta_query['source'] !== 'taxonomy_keys') {
							continue;
						}
						if (strpos($meta_query['key'], 'pa_') === 0) {
							$query['wpse_original_filters']['meta_query'][$index]['source'] = 'meta';
							$query['wpse_original_filters']['meta_query'][$index]['key'] = 'attribute_' . $meta_query['key'];
						} else {
							$query['wpse_product_query_vars']['wpse_original_filters']['meta_query'][] = $meta_query;
							unset($query['wpse_original_filters']['meta_query'][$index]);
						}
					}
					$query['meta_query'] = $query['wpse_original_filters']['meta_query'];
				}
			}

			return $query;
		}

		function add_search_on_variations_field($post_type) {
			if ($post_type !== $this->post_type) {
				return;
			}
			include VGSE_WC_DIR . '/views/spreadsheet-search-on-variation.php';
		}

		function get_variation_whitelisted_columns() {
			$this->wc_variation_columns = array(
				'_vgse_variation_enabled',
				'ID',
				'post_type',
				'post_status',
				'_sku',
				'_regular_price',
				'_sale_price',
				'_sale_price_dates_from',
				'_sale_price_dates_to',
				'_downloadable',
				'_virtual',
				'_downloadable_files',
				'_download_expiry',
				'_download_limit',
				'_tax_status',
				'_tax_class',
				'_manage_stock',
				'_stock_status',
				'_stock',
				'_backorders',
				'product_shipping_class',
				'_variation_description',
				'_thumbnail_id',
				'_vgse_create_attribute',
				'_weight',
				'_width',
				'_height',
				'_length',
				'post_parent'
			);
			$this->wc_core_variation_columns = $this->wc_variation_columns;

			// We enable the global attribute and custom meta columns for variations too
			$this->wc_variation_columns = array_unique(array_merge($this->wc_variation_columns, wc_get_attribute_taxonomy_names(), $this->get_variation_meta_keys()));
			return apply_filters('vg_sheet_editor/woocommerce/variation_columns', $this->wc_variation_columns);
		}

		function get_variation_meta_keys() {

			$transient_key = 'vgse_variation_meta_keys';
			$meta_keys = get_transient($transient_key);

			if (!empty($_GET['wpse_rescan_db_fields'])) {
				$meta_keys = false;
			}

			if (empty($meta_keys)) {
				$provider = VGSE()->helpers->get_current_provider();
				if (!is_object($provider)) {
					return array();
				}
				$variation_meta_keys = array_diff(VGSE()->helpers->get_all_meta_keys($this->variation_post_type, 1000), array_keys(WP_Sheet_Editor_WooCommerce::get_instance()->core_to_woo_importer_columns_list));

				foreach ($variation_meta_keys as $index => $meta_key) {
					if (strpos($meta_key, 'attribute_') === 0) {
						unset($variation_meta_keys[$index]);
					}
				}

				$meta_keys = $variation_meta_keys;
				set_transient($transient_key, $meta_keys, VGSE()->helpers->columns_cache_expiration());
			}
			return apply_filters('vg_sheet_editor/woocommerce/variations/custom_meta_keys', $meta_keys);
		}

		function get_variation_attributes($terms, $id, $taxonomy) {
			if (get_post_type($id) !== $this->variation_post_type || strpos($taxonomy, 'pa_') === false) {
				return $terms;
			}


			$term_slug = VGSE()->helpers->get_current_provider()->get_item_meta($id, 'attribute_' . $taxonomy, true);

			if (!empty($term_slug) && $term = get_term_by('slug', $term_slug, $taxonomy)) {
				$terms = VGSE()->data_helpers->prepare_post_terms_for_display(array($term));
			}
			return $terms;
		}

		function enable_variations_when_fetching_created_rows($args) {
			if ($args['post_type'] !== $this->post_type) {
				return $args;
			}

			if (!isset($args['filters'])) {
				$args['filters'] = '';
			}
			$args['filters'] .= '&wc_display_variations=yes';
			return $args;
		}

		/**
		 * Modify variations fields before returning the spreadsheet rows.
		 * @param type $rows
		 * @param array $wp_query
		 * @param array $spreadsheet_columns
		 * @return array
		 */
		function maybe_modify_variations_output($rows, $wp_query, $spreadsheet_columns) {

			if (empty($rows) || !is_array($rows) || VGSE()->helpers->get_provider_from_query_string() !== $this->post_type) {
				return $rows;
			}

			VGSE()->helpers->profile_record('before ' . __FUNCTION__);

			$args = apply_filters('vg_sheet_editor/woocommerce/variations/modify_variation_output_args', array(
				'add_variation_title_prefix' => true,
					), $rows, $wp_query, $spreadsheet_columns);

			foreach ($rows as $row_index => $post) {

				if (isset($post['_download_expiry']) && $post['_download_expiry'] === '-1') {
					$rows[$row_index]['_download_expiry'] = '';
				}
				if (isset($post['_download_limit']) && $post['_download_limit'] === '-1') {
					$rows[$row_index]['_download_limit'] = '';
				}

				if ($post['post_type'] !== $this->variation_post_type) {
					continue;
				}
				$post_obj = get_post($post['ID']);
				$rows[$row_index]['_vgse_variation_enabled'] = ($post_obj->post_status !== 'publish') ? '' : 'on';
				$rows[$row_index]['post_status'] = 'publish';

				// Set variation titles
				if ($args['add_variation_title_prefix']) {
					$rows[$row_index]['post_title'] = sprintf(__('Variation: %s', VGSE()->textname), $post_obj->post_title);
				} else {
					$rows[$row_index]['post_title'] = $post_obj->post_title;
				}
			}

			VGSE()->helpers->profile_record('After ' . __FUNCTION__);
			return $rows;
		}

		/**
		 * Make sure that product variations dont have the columns exclusive to general products.
		 * @param array $columns
		 * @param obj $post
		 * @return array
		 */
		function disable_general_columns_for_variations($columns, $post) {

			if ($post->post_type !== $this->variation_post_type && $post->post_type !== $this->post_type) {
				return $columns;
			}

			if ($post->post_type === $this->variation_post_type) {
				$disallowed = array_diff(array_keys($columns), $this->get_variation_whitelisted_columns());
			} else {
				$disallowed = $this->wc_variation_only_columns;
			}

			$new_columns = array();

			foreach ($columns as $key => $column) {
				if (!in_array($key, $disallowed)) {
					$new_columns[$key] = $column;
				}
			}

			return $new_columns;
		}

		function copy_variations($data, $product_ids) {
			global $wpdb;
			$copy_from_product = VGSE()->helpers->_get_post_id_from_search($data['copy_from_product']);

			// We let the user select variation rows in the dropdown,
			// so we automatically switch to the parent
			$post = get_post($copy_from_product);
			if ($post->post_parent > 0) {
				$copy_from_product = $post->post_parent;
			}
			$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $copy_from_product);
			$product_data = $api_response->get_data();
			$variations = array();
			$source_attributes = array();
			$variations_to_copy = (!empty($data['copy_individual_variations']) && is_array($data['individual_variations']) ) ? array_filter(array_map('intval', $data['individual_variations'])) : array();

			if (empty($product_data['variations'])) {
				wp_send_json_error(array('message' => __('The source product doesn´t have variations.', VGSE()->textname)));
			}

			$placeholder_image_url = wc_placeholder_img_src();

			foreach ($product_data['variations'] as $variation) {
				if (!empty($variations_to_copy) && !in_array((int) $variation['id'], $variations_to_copy, true)) {
					continue;
				}
				// Save all meta data of the variation, we'll increase it later using another hook
				// This allow copying meta data added by other plugins
				$variation['wpse_custom_meta'] = get_post_meta($variation['id']);

				// These fields should be auto generated by WC
				unset($variation['id']);
				unset($variation['date_created']);
				unset($variation['date_modified']);
				unset($variation['permalink']);
				unset($variation['sku']);
				unset($variation['price']);

				// Remove all fields that inherit value from the parent to avoid error 400s
				// when the parent doesn't have the field value or has it with wrong format,
				// Let WC use the default.
				foreach ($variation as $field_key => $value) {
					if (is_string($value) && $value === 'parent') {
						unset($variation[$field_key]);
					}
				}

				// Prepare variation attributes for saving
				if (!empty($variation['attributes'])) {
					$variation['wpse_original_attributes'] = $variation['attributes'];
					foreach ($variation['attributes'] as $variation_attribute_index => $variation_attribute) {

						$attribute_name = wc_attribute_taxonomy_name_by_id($variation['attributes'][$variation_attribute_index]['id']);

						if ($variation['attributes'][$variation_attribute_index]['id']) {
							$variation['attributes'][$variation_attribute_index]['name'] = $attribute_name;
						}
						unset($variation['attributes'][$variation_attribute_index]['id']);
					}
				}
				if (!empty($variation['image'])) {
					$first_image = current($variation['image']);

					// Ignore the variation image if the image is the blank placeholder, 
					// or if we selected the option ignore_variation_image
					if ($first_image['src'] === $placeholder_image_url || !empty($data['ignore_variation_image'])) {
						unset($variation['image']);

						if (isset($variation['wpse_custom_meta']['_thumbnail_id'])) {
							unset($variation['wpse_custom_meta']['_thumbnail_id']);
						}
					}
				}
				$variations[] = array_filter($variation);
			}

			// Add index to source attributes so we can access them easily later
			foreach ($product_data['attributes'] as $attribute) {
				$attribute_key = $attribute['id'] > 0 ? wc_attribute_taxonomy_name_by_id($attribute['id']) : sanitize_title($attribute['name']);
				$source_attributes[$attribute_key] = $attribute;
			}

			foreach ($product_ids as $product_id) {
				if (!empty($data['use_parent_product_price'])) {
					foreach ($variations as $variation_index => $variation) {
						$variations[$variation_index]['regular_price'] = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, '_regular_price', true);
						$variations[$variation_index]['sale_price'] = VGSE()->helpers->get_current_provider()->get_item_meta($product_id, '_sale_price', true);
					}
				}

				if (empty($variations_to_copy)) {
					// Delete existing variations if we're copying all variations
					$existing_variations = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = '$this->variation_post_type' AND post_parent = " . (int) $product_id);
					VGSE()->deleted_rows_ids = array_merge(VGSE()->deleted_rows_ids, $existing_variations);
					foreach ($existing_variations as $variation_id) {
						$variation = wc_get_product($variation_id);
						$variation->delete(true);
					}
					$new_attributes = $source_attributes;
				} else {

					$target_product_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $product_id);
					$target_product_data = $target_product_response->get_data();

					// Add index to existing attributes so we can update them easily later
					$new_attributes = array();
					foreach ($target_product_data['attributes'] as $index => $attribute) {
						$attribute_key = $attribute['id'] > 0 ? wc_attribute_taxonomy_name_by_id($attribute['id']) : sanitize_title($attribute['name']);
						$new_attributes[$attribute_key] = $attribute;
					}

					// Iterate over new variations to find missing attributes and add them to the target product
					$variations_attributes = wp_list_pluck($variations, 'attributes');
					foreach ($variations as $variation_index => $variation) {

						// Update existing variations with same attributes to avoid duplicating variations
						foreach ($target_product_data['variations'] as $target_variation) {
							if ($target_variation['attributes'] == $variation['wpse_original_attributes']) {
								$variations[$variation_index]['id'] = $target_variation['id'];
							}
						}

						// Add missing attributes to the target product
						foreach ($variation['attributes'] as $variation_attribute) {
							$attribute_key = $variation_attribute['name'];
							$attribute_option = $variation_attribute['option'];
							if (isset($new_attributes[$attribute_key]) && !in_array($attribute_option, $new_attributes[$attribute_key]['options'])) {
								$new_attributes[$attribute_key]['options'][] = $attribute_option;
							} elseif (!isset($new_attributes[$attribute_key])) {
								$new_attributes[$attribute_key] = $source_attributes[$attribute_key];
								$new_attributes[$attribute_key]['options'] = array($attribute_option);
							}
						}
					}
				}

				$api_response = VGSE()->WC->update_products_with_api(array(
					'ID' => $product_id,
					'type' => 'variable',
					'default_attributes' => $product_data['default_attributes'],
					'attributes' => array_values($new_attributes),
					'variations' => $variations,
				));
			}

			$variations_count = count($variations) * count($product_ids);
			$out = array(
				'created' => $variations_count,
				'deleted' => array_unique(VGSE()->deleted_rows_ids)
			);
			return $out;
		}

		/**
		 * Create variations rows
		 */
		function create_variations_rows() {
			global $wpdb;
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('Request not allowed. Try again later.', VGSE()->textname)));
			}

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($this->post_type);

			if ((isset($data['vgse_variation_manager_source']) && $data['vgse_variation_manager_source'] === 'individual' ) || $data['vgse_variation_tool'] === 'create') {

				if (empty($data[$this->post_type])) {
					wp_send_json_error(array('message' => __('Please select a product.', VGSE()->textname)));
				}
				$product_ids = array();
				if (is_string($data[$this->post_type])) {
					$product_ids[] = VGSE()->helpers->_get_post_id_from_search($data[$this->post_type]);
				} elseif (is_array($data[$this->post_type])) {
					foreach ($data[$this->post_type] as $product) {
						$product_ids[] = VGSE()->helpers->_get_post_id_from_search($product);
					}
				}
			} elseif ($data['vgse_variation_manager_source'] === 'search') {

				$get_rows_args = apply_filters('vg_sheet_editor/woocommerce/copy_variations/search_query/get_rows_args', array(
					'nonce' => wp_create_nonce('bep-nonce'),
					'post_type' => $this->post_type,
					'filters' => $_REQUEST['filters'],
					'paged' => $data['page'],
					'wpse_source' => 'create_variations'
				));
				$base_query = VGSE()->helpers->prepare_query_params_for_retrieving_rows($get_rows_args, $get_rows_args);
				$base_query = apply_filters('vg_sheet_editor/woocommerce/copy_variations/posts_query', $base_query, $data);

				$base_query['fields'] = 'ids';
				$per_page = (!empty(VGSE()->options) && !empty(VGSE()->options['be_posts_per_page_save']) ) ? (int) VGSE()->options['be_posts_per_page_save'] / 2 : 2;
				$base_query['posts_per_page'] = ( $per_page < 1 ) ? 1 : $per_page;
				$editor = VGSE()->helpers->get_provider_editor($this->post_type);
				VGSE()->current_provider = $editor->provider;
				$query = $editor->provider->get_items($base_query);
				$product_ids = $query->posts;
			}
			if (empty($product_ids)) {
				wp_send_json_error(array('message' => __('Target products not found.', VGSE()->textname)));
			}

			if ($data['vgse_variation_tool'] === 'copy') {
				$copy_result = $this->copy_variations($data, $product_ids);
				$variations_count = $copy_result['created'];
			} else {
				foreach ($product_ids as $product_id) {

					// We let the user select variation rows in the dropdown,
					// so we automatically switch to the parent
					$post = get_post($product_id);
					if ($post->post_parent > 0) {
						$product_id = $post->post_parent;
					}


					// Link variations using WC ajax function
					if ($data['link_attributes'] === 'on') {
						if (VGSE()->WC->get_product_type($product_id) !== 'variable') {
							wp_set_object_terms($product_id, 'variable', 'product_type', false);
						}
						$variations = $this->link_all_variations($product_id);
						if (is_wp_error($variations)) {
							wp_send_json_error($variations->get_error_message());
						}

						$variations_count = (int) $variations;
					} else {
						$variations_count = (int) $data['number'];

						$x = $variations_count;
						$api_request_data = array(
							'ID' => $product_id,
							'type' => 'variable',
							'variations' => array()
						);
						while ($x > 0) {
							$api_request_data['variations'][] = array('stock' => '');
							$x--;
						}

						VGSE()->WC->update_products_with_api($api_request_data);
					}
				}
			}

			// We don't retrieve the rows when using the search to reduce
			// memory usage because we might copy to a lot of products at once
			if (isset($data['vgse_variation_manager_source']) && $data['vgse_variation_manager_source'] === 'search') {
				$data = array();
			} else {
				$rows = VGSE()->helpers->get_rows(array(
					'nonce' => $data['nonce'],
					'post_type' => $this->post_type,
					'wp_query_args' => array(
						'post__in' => $product_ids,
					),
					'filters' => '&wc_display_variations=yes',
					'wpse_source' => 'create_variations'
				));

				if (is_wp_error($rows)) {
					wp_send_json_error($rows->get_error_message());
				}
				$data = array_values($rows['rows']);
			}
			wp_send_json_success(array(
				'message' => sprintf(__('%s variations created.', VGSE()->textname), $variations_count),
				'deleted' => array_unique(VGSE()->deleted_rows_ids),
				'data' => $data
			));
		}

		/**
		 * Create variations for every possible combination of attributes
		 * @param int $post_id
		 * @return \WP_Error|int
		 */
		function link_all_variations($post_id) {
			if (version_compare(WC()->version, '3.0') < 0) {
				return new WP_Error('wpse', array('message' => __('The option to create variations for every combination of attributes requires WooCommerce 3.0 or higher. Please update WooCommerce.', VGSE()->textname)));
			}

			if (!current_user_can('edit_products')) {
				return new WP_Error('wpse', array('message' => __('User not allowed', VGSE()->textname)));
			}

			if (!$post_id) {
				return new WP_Error('wpse', array('message' => __('Data missing, try again later.', VGSE()->textname)));
			}

			wc_maybe_define_constant('WC_MAX_LINKED_VARIATIONS', 200);
			wc_set_time_limit(0);
			$product = wc_get_product($post_id);
			$data_store = $product->get_data_store();

			if (!is_callable(array($data_store, 'create_all_product_variations'))) {
				return new WP_Error('wpse', array('message' => __('Wrong product type. Make sure it is a variable product.', VGSE()->textname)));
			}

			$added = intval($data_store->create_all_product_variations($product, WC_MAX_LINKED_VARIATIONS));

			$data_store->sort_all_product_variations($product->get_id());
			return $added;
		}

		/**
		 * Render modal for creating variations
		 * @param str $post_type
		 * @return null
		 */
		function render_create_variation_modal($post_type) {
			if ($this->post_type !== $post_type) {
				return;
			}
			$nonce = wp_create_nonce('bep-nonce');
			$random_id = rand();
			include VGSE_WC_DIR . '/views/spreadsheet-create-variations-modal.php';
		}

		/**
		 * Save / get default product attributes
		 */
		function get_default_attributes($post_id, $api_data = null) {
			$out = array();
			if (VGSE()->WC->get_product_type($post_id) !== 'variable') {
				return $out;
			}
			// Is get
			if (!$api_data) {
				$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $post_id);
				$api_data = $api_response->get_data();
			}

			$default_attributes_out = $api_data['default_attributes'];

			foreach ($default_attributes_out as $default_attribute) {
				if ($default_attribute['id'] > 0) {
					$out[] = $default_attribute;
				} else {
					$attributes_found = wp_list_filter($api_data['attributes'], array('name' => sanitize_title($default_attribute['name'])));

					$attribute = ( $attributes_found) ? current($attributes_found) : $default_attribute;
					$out[] = wp_parse_args(array(
						'name' => $attribute['name']
							), $default_attribute);
				}
			}
			return $out;
		}

		function save_default_attributes($data, $post_id) {
			$_product = wc_get_product($post_id);
			$attributes = $_product->get_attributes();
			$new_data = array();

			if (is_array($data)) {
				foreach ($data as $default_attribute) {
					if (empty($default_attribute['name'])) {
						continue;
					}
					$sanitized_title = sanitize_title($default_attribute['name']);
					if (isset($attributes['pa_' . $sanitized_title])) {
						$key = 'pa_' . $sanitized_title;

						$new_data[] = wp_parse_args(array(
							'id' => wc_attribute_taxonomy_id_by_name($key)
								), $default_attribute);
					} else {
						$new_data[] = wp_parse_args($default_attribute, array(
							'id' => 0,
							'name' => $sanitized_title
						));
					}
				}
			}


			$api_response = VGSE()->WC->update_products_with_api(array(
				'ID' => $post_id,
				'variations' => array(),
				'default_attributes' => $new_data
			));
			$api_data = $api_response->get_data();
			return $api_data;
		}

		function get_product_variations() {
			global $wpdb;
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', VGSE()->textname)));
			}
			if (empty($data['product_id'])) {
				wp_send_json_error(array('message' => __('Please select a source product.', VGSE()->textname)));
			}
			$product_id = VGSE()->helpers->_get_post_id_from_search($data['product_id']);

			$out = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_parent = " . (int) $product_id . " AND post_type = '" . esc_sql($this->variation_post_type) . "' AND post_status IN ('publish', 'draft') ", ARRAY_A);
			wp_send_json_success($out);
		}

		/**
		 * Save / get default product attributes
		 */
		function update_default_attributes() {

			$data = VGSE()->helpers->clean_data($_REQUEST);


			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($this->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', VGSE()->textname)));
			}

			// Disable post actions to prevent conflicts with other plugins
			VGSE()->helpers->remove_all_post_actions($this->post_type);
			$post_id = (int) $data['postId'];

			// Is update
			if (isset($data['data'])) {

				if (!is_array($data['data'])) {
					$data['data'] = array($data['data']);
				}

				$api_data = $this->save_default_attributes($data['data'], $post_id);
			} else {
				$api_response = VGSE()->helpers->create_rest_request('GET', '/wc/v1/products/' . $post_id);
				$api_data = $api_response->get_data();
			}

			$out = array(
				'data' => $this->get_default_attributes($post_id, $api_data)
			);

			$out['custom_handsontable_args'] = array(
				'columns' => array(
					array(
						'data' => 'name',
						'type' => 'autocomplete',
						'source' => array_values(wp_list_pluck(wp_list_filter($api_data['attributes'], array(
							'variation' => true
										)), 'name'))
					),
					array(
						'data' => 'option',
						'type' => 'autocomplete',
						'source' => array_reduce(wp_list_pluck(wp_list_filter($api_data['attributes'], array(
							'variation' => true
										)), 'options'), 'array_merge', array())
					),
				)
			);
			wp_send_json_success($out);
		}

		/**
		 * Save variations rows using WC API
		 * @param array $data
		 * @param str $post_type
		 * @param array $spreadsheet_columns
		 * @param array $request
		 * @return array
		 */
		function maybe_save_variations($response, $data, $post_type, $spreadsheet_columns, $request) {

			if (!$this->variations_enabled(null, $request) || empty($GLOBALS['be_wc_variations_rows'])) {
				return $response;
			}
			$variations_rows = $GLOBALS['be_wc_variations_rows'];

			if (empty($variations_rows)) {
				return $response;
			}

			// We save attributes without using the API because the documentation 
			// is not clear and it was too difficult to find the right parameters
			foreach ($variations_rows as $row_index => $row) {
				foreach ($row as $key => $column_value) {
					if (strpos($key, 'pa_') !== false) {
						if ($column_value && $term = get_term_by('name', $column_value, $key)) {
							$value = $term->slug;
						} else {
							$value = '';
						}

						update_post_meta($row['ID'], 'attribute_' . $key, $value);
						unset($variations_rows[$row_index][$key]);
					}
					if ($key === 'post_parent') {
						$parent = get_page_by_title($column_value, OBJECT, 'product');
						if ($parent) {
							$wc_variation = wc_get_product($row['ID']);
							$wc_variation->set_parent_id($parent->ID);
							$wc_variation->save();
							unset($variations_rows[$row_index][$key]);
						}
					}
					if ($key === 'post_status' && $column_value === 'delete') {
						wp_delete_post($row['ID']);
						unset($variations_rows[$row_index][$key]);
					}

					// If file cells, convert URLs to file IDs					
					if (isset($spreadsheet_columns[$key]) && in_array($spreadsheet_columns[$key]['value_type'], array('boton_gallery', 'boton_gallery_multiple')) && filter_var($column_value, FILTER_VALIDATE_URL)) {

						$variations_rows[$row_index][$key] = intval(implode(',', array_filter(VGSE()->helpers->maybe_replace_urls_with_file_ids(explode(',', $column_value), $row['ID']))));
					}
				}
			}

			$formatted_for_api = WPSE_WC_Products_Data_Formatting_Obj()->convert_row_to_api_format($variations_rows);

			$error_messages = array();
			foreach ($formatted_for_api as $row_to_save) {
				$parent_id = $row_to_save['ID'];
				$final = $row_to_save;

				// Reset the variation index because it's used as menu order and we don't want to change it
				$final['variations'] = array();
				foreach ($row_to_save['variations'] as $variation_row) {
					$menu_order = (int) get_post_field('menu_order', $variation_row['id']);
					$variation_row['menu_order'] = $menu_order;
					// If the variation has a duplicate menu order, we assign the next number
					// we obey the menu order only if no other variation uses it
					if (isset($final['variations'][$menu_order])) {
						$final['variations'][] = $variation_row;
					} else {
						$final['variations'][$menu_order] = $variation_row;
					}
				}

				$api_response = VGSE()->helpers->create_rest_request('POST', '/wc/v2/products/' . $parent_id . '/variations/batch', array(
					'update' => $final['variations']
				));

				$response_data = $api_response->get_data();
				if (!empty($response_data['update'])) {
					foreach ($response_data['update'] as $variation_response) {
						if (empty($variation_response['error'])) {
							continue;
						}
						$error_messages[] = sprintf(__('Error on row ID: %d - %s', VGSE()->textname), $variation_response['id'], $variation_response['error']['message']);
					}
				}
				do_action('vg_sheet_editor/woocommerce/variable_product_updated', $final, $request, $variations_rows);
			}

			if (!empty($error_messages)) {
				return new WP_Error('wpse', __('Please correct the error and save again.', VGSE()->textname) . '<br>' . implode('<br>', $error_messages));
			}

			return $response;
		}

		/**
		 * Remove variations rows from the posts list before saving
		 * @param array $data
		 * @param array $request
		 * @return array
		 */
		function exclude_variations_from_saving_list($data, $request) {
			if (!$this->variations_enabled(null, $request) ||
					empty($data) || !is_array($data)) {
				return $data;
			}

			$data_with_post_type = VGSE()->helpers->add_post_type_to_rows($data);

			$general_products = wp_list_filter($data_with_post_type, array(
				'post_type' => $this->post_type
			));

			$variation_rows = wp_list_filter($data_with_post_type, array(
				'post_type' => $this->variation_post_type
			));

			$variations_to_save_without_wc_api = array();
			foreach ($variation_rows as $index => $variation_row) {
				$variations_to_save_without_wc_api[$index] = array(
					'ID' => $variation_row['ID']
				);

				foreach ($variation_row as $key => $value) {
					if (!in_array($key, $this->wc_core_variation_columns)) {
						$variations_to_save_without_wc_api[$index][$key] = $value;
					}
				}
			}

			$general_products = array_merge($general_products, $variations_to_save_without_wc_api);

			$GLOBALS['be_wc_variations_rows'] = $variation_rows;

			return $general_products;
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * 
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WooCommerce_Variations::$instance) {
				WP_Sheet_Editor_WooCommerce_Variations::$instance = new WP_Sheet_Editor_WooCommerce_Variations();
				WP_Sheet_Editor_WooCommerce_Variations::$instance->init();
			}
			return WP_Sheet_Editor_WooCommerce_Variations::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}


if (!function_exists('vgse_init_WooCommerce_Variations')) {

	function vgse_init_WooCommerce_Variations() {
		return WP_Sheet_Editor_WooCommerce_Variations::get_instance();
	}

	vgse_init_WooCommerce_Variations();
}