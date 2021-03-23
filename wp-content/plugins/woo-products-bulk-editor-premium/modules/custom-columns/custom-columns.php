<?php

if (!class_exists('WP_Sheet_Editor_Custom_Columns')) {

	/**
	 * This class enables the autofill cells features.
	 * Also known as fillHandle in handsontable arguments.
	 */
	class WP_Sheet_Editor_Custom_Columns {

		static private $instance = false;
		var $addon_helper = null;
		var $key = 'vg_sheet_editor_custom_columns';
		var $default_column_settings = null;
		var $required_column_settings = null;
		var $found_columns = array();
		var $bool_column_settings = null;
		var $automatic_column_post_types_initialized = array();

		private function __construct() {
			
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Custom_Columns::$instance) {
				WP_Sheet_Editor_Custom_Columns::$instance = new WP_Sheet_Editor_Custom_Columns();
				WP_Sheet_Editor_Custom_Columns::$instance->init();
			}
			return WP_Sheet_Editor_Custom_Columns::$instance;
		}

		function init() {

			$this->default_column_settings = array(
				'name' => '',
				'key' => '',
				'data_source' => 'post_meta',
				'post_types' => 'post',
				'read_only' => 'no',
				'allow_formulas' => 'yes',
				'allow_hide' => 'yes',
				'allow_rename' => 'yes',
				'plain_renderer' => 'text',
				'formatted_renderer' => 'text',
				'width' => '150',
				'cell_type' => '',
			);

			$this->required_column_settings = array(
				'name',
				'key',
				'data_source',
				'post_types',
			);

			$this->bool_column_settings = array(
				'read_only',
				'allow_formulas',
				'allow_hide',
				'allow_rename',
			);
			$this->true_default_column_settings = array(
				'allow_formulas' => 'yes',
				'allow_hide' => 'yes',
				'allow_rename' => 'yes',
			);
			add_action('admin_menu', array($this, 'register_menu_page'), 99);
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'register_frontend_assets'));
			add_action('wp_ajax_vgse_save_columns', array($this, 'save_columns'));
			add_action('wp_ajax_vgse_rename_meta_key', array($this, 'rename_meta_key'));
			add_action('wp_ajax_vgse_delete_meta_key', array($this, 'delete_meta_key'));

			// We use priority 40 to overwrite any column through the UI
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'), 40);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
			// CORE columns are registered automatically without the hook.
			// Here we use priority 8 to register the automatic columns early and other
			// modules/plugins will just overwrite them
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns_automatically'), 8);
		}

		/**
		 * Allow letters, numbers, spaces, and ()
		 * @param string $input
		 * @return string
		 */
		function _convert_key_to_label($input) {
			return ucwords(trim(str_replace(array('-', '_'), ' ', preg_replace("/[^a-zA-Z0-9\:\.\-\_\s\(\)]/", "", $input))));
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns_automatically($editor) {
			$post_type = $editor->args['provider'];

			// Only do this once per post type
			if (in_array($post_type, $this->automatic_column_post_types_initialized)) {
				return;
			}

			VGSE()->helpers->profile_record("Start " . __FUNCTION__);
			$this->automatic_column_post_types_initialized[] = $post_type;
			$transient_key = 'vgse_detected_fields_' . $post_type;
			$columns_detected = get_transient($transient_key);

			// Clear cache on demand
			if (!empty($_GET['wpse_rescan_db_fields'])) {
				$columns_detected = false;
				// Increase columns limit every time we rescan
				if (empty($_GET['wpse_dont_increase_limit'])) {
					$options = get_option(VGSE()->options_key);
					$options['be_columns_limit'] = VGSE()->helpers->get_columns_limit() + 300;
					update_option(VGSE()->options_key, $options);
				}
			}

			if (empty($columns_detected)) {
				// We will process meta keys limited to the columns limit+200
				// we can't limit to the columns limit because sometimes columns are blacklisted and we miss good columns
				$meta_keys = apply_filters('vg_sheet_editor/custom_columns/all_meta_keys', VGSE()->helpers->get_all_meta_keys($post_type, VGSE()->helpers->get_columns_limit() + 200), $post_type, $editor);

				$this->found_columns[$post_type] = array();

				$columns_detected = array(
					'serialized' => array(),
					'normal' => array()
				);
				foreach ($meta_keys as $meta_key) {
					$label = $this->_convert_key_to_label($meta_key);
					$this->found_columns[$post_type][$label] = $meta_key;

					$detected_type = $this->detect_column_type($meta_key, $editor);

					if (empty($detected_type)) {
						continue;
					}
					if ($detected_type['type'] === 'serialized') {
						$columns_detected['serialized'][$meta_key] = array(
							'sample_field_key' => $meta_key,
							'sample_field' => $detected_type['sample_field'],
							'column_width' => 175,
							'column_title_prefix' => $label, // to remove the field key from the column title
							'level' => ( $detected_type['is_single_level'] ) ? 3 : count($detected_type['sample_field']),
							'allowed_post_types' => array($post_type),
							'is_single_level' => $detected_type['is_single_level'],
							'allow_in_wc_product_variations' => false,
							'wpse_source' => 'custom_columns',
						);
					} elseif ($detected_type['type'] === 'infinite_serialized') {
						$columns_detected['infinite_serialized'][$meta_key] = array_merge($detected_type, array(
							'sample_field_key' => $meta_key,
							'allowed_post_types' => array($post_type),
							'allow_in_wc_product_variations' => false,
							'wpse_source' => 'custom_columns',
						));
					} else {
						if ($editor->args['columns']->has_item($meta_key, $post_type)) {
							continue;
						}
						$column_settings = array(
							'data_type' => 'meta_data',
							'unformatted' => array('data' => $meta_key),
							'column_width' => (int) (6.1 * strlen($label)) + 75, // Set the width based on the label length+the locked icon length
							'title' => $label,
							'type' => '',
							'supports_formulas' => true,
							'formatted' => array('data' => $meta_key),
							'allow_to_hide' => true,
							'allow_to_rename' => true,
							'allow_to_save' => true
						);
						if ($detected_type['type'] === 'checkbox') {
							$column_settings['formatted']['type'] = 'checkbox';
							$column_settings['formatted']['checkedTemplate'] = $detected_type['positive_value'];
							$column_settings['formatted']['uncheckedTemplate'] = $detected_type['negative_value'];
							$column_settings['default_value'] = $detected_type['negative_value'];
						}

						$columns_detected['normal'][$meta_key] = $column_settings;
					}
				}
				$total_rows = (int) $editor->provider->get_total($post_type);
				// If the spreadsheet has < 200 rows in total, we refresh the automatic columns more often
				$cache_expiration = VGSE()->helpers->columns_cache_expiration($total_rows);
				set_transient($transient_key, $columns_detected, $cache_expiration);
				update_option($transient_key . '_updated', current_time('timestamp'));
			}
			$columns_detected = apply_filters('vg_sheet_editor/custom_columns/columns_detected_settings', $columns_detected, $post_type);
			VGSE()->helpers->profile_record("Before registering serialized field " . __FUNCTION__);
			if (!empty($columns_detected['normal'])) {
				foreach ($columns_detected['normal'] as $column_key => $column_settings) {
					$editor->args['columns']->register_item($column_key, $post_type, $column_settings);
				}
			}

			if (!empty($columns_detected['serialized'])) {
				foreach ($columns_detected['serialized'] as $column_key => $column_settings) {
					new WP_Sheet_Editor_Serialized_Field($column_settings);
				}
			}
			if (!empty($columns_detected['infinite_serialized'])) {
				foreach ($columns_detected['infinite_serialized'] as $column_key => $column_settings) {
					new WP_Sheet_Editor_Infinite_Serialized_Field($column_settings);
				}
			}
			VGSE()->helpers->profile_record("End " . __FUNCTION__);
		}

		function detect_column_type($meta_key, $editor) {
			$post_type = $editor->args['provider'];
			$values = $editor->provider->get_meta_field_unique_values($meta_key, $post_type);

			$out = array(
				'type' => 'text',
				'positive_value' => '',
				'negative_value' => '',
			);
			$positive_values = array();
			$negative_values = array();
			$forced_infinite_serialized_handler = isset(VGSE()->options['keys_for_infinite_serialized_handler']) ? array_map('trim', explode(',', VGSE()->options['keys_for_infinite_serialized_handler'])) : array();
			foreach ($values as $value) {

				$value = maybe_unserialize($value);

				if (is_array($value)) {
					$array_level = $this->_array_depth($value);
					if (!empty($value)) {
						if ($array_level < 3 && $this->_array_depth_uniform($value) && !in_array($meta_key, $forced_infinite_serialized_handler, true)) {
							$out['type'] = 'serialized';
							$out['is_single_level'] = $array_level === 1;
							if ($array_level === 1) {
								$out['sample_field'] = (is_numeric(implode('', array_keys($value)))) ? array('') : array_fill_keys(array_keys($value), '');
							} else {
								$out['sample_field'] = array();
								foreach ($value as $row) {
									if (is_array($row)) {
										$out['sample_field'][] = array_fill_keys(array_keys($row), '');
									}
								}
							}
						} else {
							$out['type'] = 'infinite_serialized';
							$out['serialization_level'] = $array_level;
							$out['sample_field'] = $value;
						}
						break;
					} else {
						$out = array();
					}
				} else {
					if (in_array($value, array('1', 1, 'yes', true, 'true', 'on'), true)) {
						$positive_values[] = $value;
					} elseif (in_array($value, array('0', 0, 'no', false, 'false', null, 'null', 'off', ''), true)) {
						$negative_values[] = (string) $value;
					}
				}
			}

			if (count($positive_values) === 1) {
				$out['type'] = 'checkbox';
				$out['positive_value'] = current($positive_values);
				$out['negative_value'] = ( empty($negative_values) ) ? '0' : current($negative_values);
			}
			return apply_filters('vg_sheet_editor/custom_columns/column_type', $out, $meta_key, $editor);
		}

		function _array_depth_uniform(array $array) {
			$first_item = current($array);
			if (!is_array($first_item)) {
				return true;
			}
			$depth = $this->_array_depth($first_item);
			$out = true;
			foreach ($array as $value) {
				if (is_array($value)) {
					$new_depth = $this->_array_depth($value);

					if ($new_depth !== $depth) {
						$out = false;
						break;
					}
				}
			}

			return $out;
		}

		function _array_depth(array $array) {
			$max_depth = 1;

			foreach ($array as $value) {
				if (is_array($value)) {
					$depth = $this->_array_depth($value) + 1;

					if ($depth > $max_depth) {
						$max_depth = $depth;
					}
				}
			}

			return $max_depth;
		}

		function get_all_registered_columns_keys() {
			$out = array();

			foreach (VGSE()->editors as $provider_key => $editor) {
				$columns = $editor->args['columns']->get_items();

				foreach ($columns as $post_type => $columns) {

					$out = array_unique(array_merge($out, array_keys($columns)));
				}
			}

			return $out;
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items($editor) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('add_columns', array(
					'type' => 'button',
					'content' => __('Add columns for custom fields', VGSE()->textname),
					'icon' => 'fa fa-plus',
					'url' => admin_url('admin.php?page=' . $this->key),
					'toolbar_key' => 'secondary',
					'allow_in_frontend' => false,
					'parent' => 'settings',
						), $post_type);
			}
		}

		function register_columns($editor) {
			$columns = get_option($this->key, array());

			if (empty($columns)) {
				return;
			}

			foreach ($columns as $column_index => $column_settings) {

				if (!is_array($column_settings['post_types'])) {
					$column_settings['post_types'] = array($column_settings['post_types']);
				}
				foreach ($column_settings['post_types'] as $post_type) {
					if ($editor->provider->key === 'user' && 'user' !== $post_type) {
						continue;
					}

					if (!empty($column_settings['cell_type'])) {
						$column_settings['read_only'] = true;
						$column_settings['plain_renderer'] = 'html';
						$column_settings['formatted_renderer'] = 'html';
					}

					if (($column_settings['cell_type'] === 'boton_gallery' || $column_settings['cell_type'] === 'boton_gallery_multiple' ) && $column_settings['width'] < 280) {
						$column_settings['width'] = 300;
					}
					if ($column_settings['data_source'] === 'post_terms') {
						if (!in_array($column_settings['formatted_renderer'], array('text', 'taxonomy_dropdown'))) {
							$column_settings['formatted_renderer'] = 'text';
						} elseif (!in_array($column_settings['plain_renderer'], array('text', 'taxonomy_dropdown'))) {
							$column_settings['plain_renderer'] = 'text';
						}
					}

					$column_args = array(
						'data_type' => $column_settings['data_source'], //String (post_data,meta_data)	
						'unformatted' => array(
							'data' => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes') ? true : false,
						), //Array (Valores admitidos por el plugin de handsontable)
						'column_width' => $column_settings['width'], //int (Ancho de la columna)
						'title' => $column_settings['name'], //String (Titulo de la columna)
						'type' => $column_settings['cell_type'], // String (Es para saber si será un boton que abre popup, si no dejar vacio) boton_tiny|boton_gallery|boton_gallery_multiple|(vacio)
						'supports_formulas' => ( $column_settings['allow_formulas'] === 'yes') ? true : false,
						'allow_to_hide' => ( $column_settings['allow_hide'] === 'yes') ? true : false,
						'allow_to_save' => ( $column_settings['read_only'] === 'yes' && !in_array($column_settings['cell_type'], array('boton_gallery', 'boton_gallery_multiple'))) ? false : true,
						'allow_to_rename' => ( $column_settings['allow_rename'] === 'yes') ? true : false,
						'formatted' => array(
							'data' => $column_settings['key'],
							'readOnly' => ( $column_settings['read_only'] === 'yes') ? true : false,
						),
					);

					if (in_array($column_settings['plain_renderer'], array('html', 'text'))) {
						$column_args['unformatted']['renderer'] = $column_settings['plain_renderer'];
					}
					if (in_array($column_settings['formatted_renderer'], array('html', 'text'))) {
						$column_args['formatted']['renderer'] = $column_settings['formatted_renderer'];
					}

					if ($column_settings['plain_renderer'] === 'date') {
						$column_args['unformatted'] = array_merge($column_args['unformatted'], array('type' => 'date', 'dateFormat' => 'MM-DD-YYYY', 'correctFormat' => true, 'defaultDate' => date('m-d-Y'), 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1)));
						unset($column_args['unformatted']['renderer']);
					}
					if ($column_settings['formatted_renderer'] === 'date') {
						$column_args['formatted'] = array_merge($column_args['formatted'], array('type' => 'date', 'dateFormat' => 'MM-DD-YYYY', 'correctFormat' => true, 'defaultDate' => date('m-d-Y'), 'datePickerConfig' => array('firstDay' => 0, 'showWeekNumber' => true, 'numberOfMonths' => 1,)));
						unset($column_args['formatted']['renderer']);
					}
					if ($column_settings['data_source'] === 'post_terms') {
						if ($column_settings['plain_renderer'] === 'taxonomy_dropdown') {
							$column_args['unformatted'] = array_merge($column_args['unformatted'], array('type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'));
						} elseif ($column_settings['formatted_renderer'] === 'taxonomy_dropdown') {
							$column_args['formatted'] = array_merge($column_args['formatted'], array('type' => 'autocomplete', 'source' => 'loadTaxonomyTerms'));
						}
					}

					$editor->args['columns']->register_item($column_settings['key'], $post_type, $column_args);
				}
			}
		}

		function make_columns_visible($columns) {
			if (!class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				return;
			}
			$columns_visibility = WP_Sheet_Editor_Columns_Visibility::get_instance();
			$columns_visibility->change_columns_status($columns);
		}

		function add_columns($columns, $args = array()) {
			$defaults = array(
				'append' => false,
			);
			$args = wp_parse_args($args, $defaults);

			if (empty($columns) || !is_array($columns)) {
				return new WP_Error('wpse', __('Missing columns'));
			}

			if ($args['append']) {
				$existing_columns = get_option($this->key, array());
			} else {
				$existing_columns = array();
			}
			foreach ($columns as $index => $column_settings) {
				$column_settings = wp_parse_args($column_settings, $this->default_column_settings);

				$column_settings['key'] = sanitize_key($column_settings['key']);
				$column_accepted = true;


				foreach ($column_settings as $setting_key => $setting_value) {
					if (isset($columns[$index][$setting_key]) && in_array($setting_key, $this->bool_column_settings)) {
						$setting_value = 'yes';
						$column_settings[$setting_key] = 'yes';
					}
					if (in_array($setting_key, $this->required_column_settings) && empty($setting_value)) {
						$column_accepted = false;
						break;
					}
				}

				if (!$column_accepted) {
					continue;
				}

				if ($args['append']) {
					$existing_columns[] = $column_settings;
				} else {
					$existing_columns[$index] = $column_settings;
				}
			}

			update_option($this->key, $existing_columns);

			if (!empty($existing_columns)) {
				$this->make_columns_visible($existing_columns);
			}
			return true;
		}

		function delete_meta_key() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error(array('message' => __('You are not allowed to do this action. Please reload the page or log in again.', VGSE()->textname)));
			}
			if (empty($data['post_type']) || empty($data['column_key'])) {
				wp_send_json_error(__('Missing post type or column_key'));
			}

			$post_type = sanitize_text_field($data['post_type']);
			$column_key = sanitize_text_field($data['column_key']);
			$result = VGSE()->helpers->get_current_provider()->delete_meta_key($column_key, $post_type);

			if (is_numeric($result)) {
				wp_send_json_success(array(
					'message' => __('The meta field was deleted successfully', VGSE()->textname)
				));
			} else {
				wp_send_json_error(array('message' => __('The meta field could not be deleted.', VGSE()->textname)));
			}
		}

		function rename_meta_key() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error(array('message' => __('You are not allowed to do this action. Please reload the page or log in again.', VGSE()->textname)));
			}
			if (empty($data['post_type']) || empty($data['old_column_key']) || empty($data['new_column_key']) || $data['old_column_key'] === $data['new_column_key']) {
				wp_send_json_error(__('Missing post type, old_column_key, or new_column_key; or the old and new key are the same.'));
			}

			$post_type = sanitize_text_field($data['post_type']);
			$old_column_key = sanitize_text_field($data['old_column_key']);
			$new_column_key = sanitize_text_field($data['new_column_key']);
			$result = VGSE()->helpers->get_current_provider()->rename_meta_key($old_column_key, $new_column_key, $post_type);

			if (is_numeric($result)) {
				wp_send_json_success(array(
					'label' => $this->_convert_key_to_label($new_column_key),
					'key' => $new_column_key,
					'message' => __('The meta key was renamed successfully', VGSE()->textname)
				));
			} else {
				wp_send_json_error(array('message' => __('The meta key couldnt be renamed.', VGSE()->textname)));
			}
		}

		function save_columns() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error(__('You are not allowed to do this action. Please reload the page or log in again.'));
			}

			if (empty($data['columns'])) {
				$data['columns'] = array();
				update_option($this->key, array());
			} else {
				$saved = $this->add_columns($data['columns']);


				if (!$saved || is_wp_error($saved)) {
					wp_send_json_error(__('Columns could not be saved. Try again.', VGSE()->textname));
				}
			}
			wp_send_json_success(__('Changes saved', VGSE()->textname));
		}

		function register_frontend_assets() {
			wp_enqueue_script($this->key . '-repeater', plugins_url('/', __FILE__) . 'assets/vendor/jquery.repeater/jquery.repeater.js', array('jquery'), VGSE()->version, true);
			wp_enqueue_script($this->key . '-init', plugins_url('/', __FILE__) . 'assets/js/init.js', array('jquery'), VGSE()->version, true);

			wp_localize_script($this->key . '-init', $this->key, array(
				'default_values' => wp_parse_args($this->true_default_column_settings, $this->default_column_settings),
				'required_settings' => $this->required_column_settings,
				'texts' => array(
					'confirm_delete' => __('Are you sure you want to delete this column?', VGSE()->textname),
				)
			));
			wp_enqueue_style($this->key . '-styles', plugins_url('/', __FILE__) . 'assets/css/styles.css');
		}

		function register_menu_page() {
			add_submenu_page('vg_sheet_editor_setup', __('Custom columns', VGSE()->textname), __('Custom columns', VGSE()->textname), 'manage_options', $this->key, array($this, 'render_settings_page'));
		}

		function render_settings_page() {
			require 'views/settings-page.php';
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

	add_action('vg_sheet_editor/initialized', 'vgse_custom_columns_init');

	function vgse_custom_columns_init() {
		return WP_Sheet_Editor_Custom_Columns::get_instance();
	}

}