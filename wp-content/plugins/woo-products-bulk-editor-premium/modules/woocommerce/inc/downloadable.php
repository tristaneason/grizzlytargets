<?php

if (!class_exists('WPSE_WC_Products_Downloadable')) {

	class WPSE_WC_Products_Downloadable {

		static private $instance = false;

		private function __construct() {
			
		}

		function init() {

			add_action('wp_ajax_vgse_save_download_files', array($this, 'save_download_files'));
			add_action('wp_ajax_vgse_wc_get_downloadable_files', array($this, 'get_download_files'));
			add_filter('vg_sheet_editor/handsontable_cell_content/existing_value', array($this, 'get_download_files_for_text_cell'), 10, 3);
			add_filter('vg_sheet_editor/formulas/form_settings', array($this, 'filter_formula_builder_for_downloadable_files'), 10, 2);
			add_filter('vg_sheet_editor/formulas/execute_formula/custom_formula_handler_executed', array($this, 'execute_formula_on_downloadable_files'), 10, 7);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'));
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			$post_type = VGSE()->WC->post_type;
			if (!in_array($post_type, $editor->args['enabled_post_types'])) {
				return;
			}
			$editor->args['columns']->register_item('_downloadable', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_downloadable'),
				'column_width' => 150,
				'title' => __('Downloadable', VGSE()->textname),
				'type' => '',
				'supports_formulas' => true,
				'formatted' => array('data' => '_downloadable',
					'type' => 'checkbox',
					'checkedTemplate' => 'yes',
					'uncheckedTemplate' => 'no',
				),
				'default_value' => 'no',
				'allow_to_hide' => true,
				'allow_to_rename' => true,
			));

			$editor->args['columns']->register_item('_download_limit', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_download_limit'),
				'column_width' => 150,
				'title' => __('Download limit', VGSE()->textname),
				'type' => '',
				'supports_formulas' => true,
				'formatted' => array('data' => '_download_limit',),
				'allow_to_hide' => true,
				'allow_to_rename' => true,
				'value_type' => 'number',
			));
			$editor->args['columns']->register_item('_download_expiry', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_download_expiry'),
				'column_width' => 150,
				'title' => __('Download expiry', VGSE()->textname),
				'type' => '',
				'supports_formulas' => true,
				'formatted' => array('data' => '_download_expiry',),
				'allow_to_hide' => true,
				'allow_to_rename' => true,
				'value_type' => 'number',
			));
			$editor->args['columns']->register_item('_download_type', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_download_type'),
				'column_width' => 250,
				'title' => __('Download type', VGSE()->textname),
				'type' => '',
				'supports_formulas' => true,
				'formatted' => array('data' => '_download_type', 'editor' => 'select', 'selectOptions' => array(
						'' => __('Standard Product', 'woocommerce'),
						'application' => __('Application/Software', 'woocommerce'),
						'music' => __('Music', 'woocommerce'),
					)),
				'allow_to_hide' => true,
				'allow_to_rename' => true,
			));
			$editor->args['columns']->register_item('_downloadable_files', $post_type, array(
				'data_type' => 'meta_data',
				'unformatted' => array('data' => '_downloadable_files', 'renderer' => 'html'),
				'column_width' => 160,
				'title' => __('Download files', VGSE()->textname),
				'type' => 'handsontable',
				'edit_button_label' => __('Edit files', VGSE()->textname),
				'edit_modal_id' => 'vgse-download-files',
				'edit_modal_title' => __('Download files', VGSE()->textname),
				'edit_modal_description' => '<div class="vgse-copy-files-from-product-wrapper"><label>' . __('Copy files from this product: (You need to save the changes afterwards.)', VGSE()->textname) . ' </label><br/><select name="copy_from_product" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="' . VGSE()->WC->post_type . '" data-nonce="' . wp_create_nonce('bep-nonce') . '" data-placeholder="' . __('Select product...', VGSE()->textname) . '" class="select2 vgse-copy-files-from-product">
									<option></option>
								</select><a href="#" class="button vgse-copy-files-from-product-trigger">Copy</a></div>',
				'edit_modal_local_cache' => true,
				'edit_modal_save_action' => 'vgse_save_download_files',
				'handsontable_columns' => array(
					VGSE()->WC->post_type => array(
						array(
							'data' => 'name'
						),
						array(
							'data' => 'file'
						),
					),
					'product_variation' => array(
						array(
							'data' => 'name'
						),
						array(
							'data' => 'file'
						),
					)),
				'handsontable_column_names' => array(
					VGSE()->WC->post_type => array(__('Name', VGSE()->textname), __('File (url or path)', VGSE()->textname)),
					'product_variation' => array(__('Name', VGSE()->textname), __('File (url or path)', VGSE()->textname)),
				),
				'handsontable_column_widths' => array(
					VGSE()->WC->post_type => array(160, 300),
					'product_variation' => array(160, 300),
				),
				'supports_formulas' => true,
				'supports_sql_formulas' => false,
				'forced_supports_formulas' => true,
				'value_type' => 'wc_downloadable_files',
				'formatted' => array('data' => '_downloadable_files', 'renderer' => 'html'),
				'allow_to_hide' => true,
				'use_new_handsontable_renderer' => true,
				'save_value_callback' => array($this, 'save_downloadable_files_from_cell'),
			));
		}

		function filter_formula_builder_for_downloadable_files($settings, $post_type) {
			if ($post_type !== VGSE()->WC->post_type) {
				return $settings;
			}
			$settings['columns_actions']['wc_downloadable_files'] = array(
				'replace' => 'default',
				'clear_value' => 'default',
				'set_value' =>
				array(
					'description' => __('We will save these files. Existing files will be overwritten. Enter file URL only, you can enter multiple URLs separated by comma.', VGSE()->textname),
				),
				'append' =>
				array(
					'description' => __('We will append the new file to the existing files in the products. Enter file URL only, you can enter multiple URLs separated by comma.', VGSE()->textname),
				),
			);

			return $settings;
		}

		function execute_formula_on_downloadable_files($results, $post_id, $spreadsheet_column, $formula, $post_type, $spreadsheet_columns, $raw_form_data) {

			if ($post_type !== VGSE()->WC->post_type || $spreadsheet_column['key'] !== '_downloadable_files') {
				return $results;
			}

			$initial_data = VGSE()->helpers->get_current_provider()->get_item_meta($post_id, '_downloadable_files', true);
			$modified_data = $initial_data;

			// Replace
			if ($raw_form_data['action_name'] === 'replace') {
				$search = $raw_form_data['formula_data'][0];
				$replace = $raw_form_data['formula_data'][1];

				$regex_flag = WP_Sheet_Editor_Formulas::$regex_flag;
				if (strpos($search, $regex_flag) !== false) {
					$search = untrailingslashit(ltrim(str_replace($regex_flag, '', $search), '/'));
				}

				if (is_array($modified_data)) {
					foreach ($modified_data as $file_key => $file) {

						if (strpos($search, $regex_flag) !== false) {
							$modified_data[$file_key]['file'] = preg_replace("$search", $replace, $file['file']);
							$modified_data[$file_key]['name'] = preg_replace("$search", $replace, $file['name']);
						} else {
							$modified_data[$file_key]['file'] = str_replace($search, $replace, $file['file']);
							$modified_data[$file_key]['name'] = str_replace($search, $replace, $file['name']);
						}
					}
				}
			} elseif ($raw_form_data['action_name'] === 'set_value') {
				$files = explode(',', $raw_form_data['formula_data'][0]);

				$modified_data = array();
				foreach ($files as $new_file) {
					$modified_data[] = array(
						'name' => basename($new_file),
						'file' => $new_file
					);
				}
			} elseif ($raw_form_data['action_name'] === 'append') {
				$files = explode(',', $raw_form_data['formula_data'][0]);

				foreach ($files as $new_file) {
					$modified_data[] = array(
						'name' => basename($new_file),
						'file' => $new_file
					);
				}
			} elseif ($raw_form_data['action_name'] === 'clear_value') {
				$modified_data = array();
			}

			$response = $this->_save_download_files(array_values($modified_data), $post_id);

			$out = array(
				'initial_data' => $initial_data,
				'modified_data' => $modified_data,
			);
			return $out;
		}

		/**
		 * Get download files via ajax
		 */
		function get_download_files() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_view_post_type(VGSE()->WC->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', VGSE()->textname)));
			}
			if (empty($data['product_id'])) {
				wp_send_json_error(array('message' => __('Please select a product.', VGSE()->textname)));
			}

			$product_id = (int) VGSE()->helpers->_get_post_id_from_search($data['product_id']);

			$out = maybe_unserialize(VGSE()->helpers->get_current_provider()->get_item_meta($product_id, '_downloadable_files', true));

			wp_send_json_success($out);
		}

		/**
		 * Save dowload files via ajax
		 */
		function save_download_files() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type(VGSE()->WC->post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to view this page.', VGSE()->textname)));
			}
			$post_id = (int) $data['postId'];

			$response = $this->_save_download_files($data['data'], $post_id);

			if ($response) {
				wp_send_json_success(array('message' => __('Files saved.', VGSE()->textname)));
			}

			wp_send_json_error(array('message' => __('The files could not be saved.', VGSE()->textname)));
		}

		function _save_download_files($data, $post_id) {

			if (!empty($data)) {
				foreach ($data as $file_index => $file) {
					if (!isset($file['name'])) {
						$data[$file_index]['name'] = '';
					}
				}
			}

			$formatted = current(WPSE_WC_Products_Data_Formatting_Obj()->convert_row_to_api_format(array(array(
							'ID' => $post_id,
							'_downloadable' => true,
							'_downloadable_files' => $data
			))));
			$api_response = VGSE()->WC->update_products_with_api($formatted);

			$out = false;
			if ($api_response->status === 200 || $api_response->status === 201) {
				$out = true;
			}

			return $out;
		}

		function save_downloadable_files_from_cell($post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns) {
			if (is_string($data_to_save)) {
				$data_to_save = json_decode(wp_unslash($data_to_save), true);
			} else {
				$data_to_save = '';
			}
			$response = $this->_save_download_files($data_to_save, $post_id);
		}

		function get_download_files_for_text_cell($value, $post, $column_key) {

			if (is_string($value) && !empty($value)) {
				$value = maybe_unserialize(maybe_unserialize($value));
			}
			if (!empty($value) && $column_key === '_downloadable_files' && is_array($value)) {
				$value = array_values($value);
			}
			return $value;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_WC_Products_Downloadable::$instance) {
				WPSE_WC_Products_Downloadable::$instance = new WPSE_WC_Products_Downloadable();
				WPSE_WC_Products_Downloadable::$instance->init();
			}
			return WPSE_WC_Products_Downloadable::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_WC_Products_Downloadable_Obj')) {

	function WPSE_WC_Products_Downloadable_Obj() {
		return WPSE_WC_Products_Downloadable::get_instance();
	}

}
WPSE_WC_Products_Downloadable_Obj();
