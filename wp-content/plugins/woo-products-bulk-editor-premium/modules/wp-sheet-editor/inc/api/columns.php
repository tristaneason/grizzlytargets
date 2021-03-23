<?php

if (!class_exists('WP_Sheet_Editor_Columns')) {

	class WP_Sheet_Editor_Columns {

		private $registered_items = array();

		function __construct() {
			
		}

		function has_item($key, $provider = null) {
			if (empty($provider)) {
				$provider = 'post';
			}
			return isset($this->registered_items[$provider][$key]);
		}

		function get_blacklisted_column_keywords($provider) {
			return $blacklisted_keys = apply_filters('vg_sheet_editor/columns/blacklisted_columns', array('_nxs_snap', '_edit_lock', '_edit_last', '_wp_old_slug', '_wpcom_is_markdown', 'vgse_column_sizes', 'wxr_import', '_oembed', '^\d+_\d+_\d+$', '_user_wished_', '_user_wished_user', '_rehub_views_date', '-wpfoof-', '^_transient_tribe', '_learndash_memberpress_enrolled_courses_access', 'course_\d+_access_from', 'ld_sent_notification_enroll_course_', 'learndash_last_known_course_', 'learndash_group_users_', '_badgeos_achievements_', 'learndash_group_leaders_', 'course_timer_completed_', 'course_completed_', 'screen_layout_', 'enrolled_courses_access_counter_', '_sfwd-quizzes_', '_uo-course-cert-', 'screen_options_per_page', 'gform_recent_forms_', '^manage.+columnshidden_', '^edit_.+_per_page', 'uo_timer_', '_screen_options_default', '_edd_download_limit_override', '_wcj_product_input_fields', 'seopress_pro_rich_snippets', 'seopress_analysis_data', '[a-zA-Z0-9]{28,}', 'yith_wcgpf_product_feed_configuration'), $provider);
		}

		function is_column_blacklisted($key, $provider) {

			$blacklisted_keys = $this->get_blacklisted_column_keywords($provider);
			$out = false;
			// We use preg_match to allow core and other plugins to use advanced 
			// conditions and because some fields might have wp prefix
			foreach ($blacklisted_keys as $blacklisted_field) {
				if (preg_match('/' . $blacklisted_field . '/', $key)) {
					$out = true;
					break;
				}
			}
			return $out;
		}

		/**
		 * Register spreadsheet column 
		 * @param string $key
		 * @param string $provider
		 * @param array $args
		 */
		function register_item($key, $provider = null, $args = array(), $update_existing = false) {
			if (empty($key)) {
				return;
			}

			if ($update_existing && $this->has_item($key, $provider)) {
				$args = wp_parse_args($args, $this->get_item($key, $provider, false, true));
			}

			$args['provider'] = $provider;
			$args = $this->_register_item($key, $args);

			// Enforce columns limit to avoid performance bottlenecks
			// columns with allow_to_hide=false or columns already registered previously 
			// are always allowed to avoid errors during saving.
			if ($args['allow_to_hide'] && !$this->has_item($key, $provider) && !empty($this->registered_items[$provider]) && count($this->registered_items[$provider]) > VGSE()->helpers->get_columns_limit()) {
				return;
			}


			$blacklisted = $this->is_column_blacklisted($key, $provider);
			if ($args['allow_to_hide'] && $blacklisted) {
				return;
			}

			// Skip if column doesn't have title
			if (empty($args['title'])) {
				return;
			}
			if (empty($provider)) {
				$provider = 'post';
			}

			if (!apply_filters('vg_sheet_editor/columns/can_add_item', true, $key, $args, $provider)) {
				return;
			}
			if (!isset($this->registered_items[$provider])) {
				$this->registered_items[$provider] = array();
			}
			$this->registered_items[$provider][$key] = $args;
		}

		function remove_item($key, $provider) {
			if (isset($this->registered_items[$provider][$key])) {
				unset($this->registered_items[$provider][$key]);
			}
		}

		function _register_item($key, $args = array()) {
			$defaults = array(
				'data_type' => 'post_data', //String (post_data,post_meta|meta_data)	
				'unformatted' => array(), //Array (Valores admitidos por el plugin de handsontable)
				'column_width' => 100, //int (Ancho de la columna)
				'title' => ucwords(str_replace(array('-', '_'), ' ', $key)), //String (Titulo de la columna)
				'type' => '', // String (Es para saber si será un boton que abre popup, si no dejar vacio) boton_gallery|boton_gallery_multiple|view_post|handsontable|metabox|(vacio)
				'get_value_callback' => '', // Callable. We'll use this to get the cell value during all contexts,
				'save_value_callback' => '', // Callable. We'll use this to get the cell value during all contexts,
				'edit_button_label' => null,
				'edit_modal_id' => null,
				'edit_modal_title' => null,
				'edit_modal_description' => null,
				'edit_modal_local_cache' => true,
				'edit_modal_save_action' => null, // js_function_name:<function name>, <wp ajax action>
				'edit_modal_cancel_action' => null,
				'metabox_show_selector' => null,
				'metabox_value_selector' => null,
				'handsontable_columns' => array(), // array( 'product' => array( array( 'data' => 'name' ), ) ),
				'handsontable_column_names' => array(), // array('product' => array('Column name'),),
				'handsontable_column_widths' => array(), // array('product' => array(160),),
				'supports_formulas' => false,
				'supports_sql_formulas' => true,
				'key_for_formulas' => $key,
				'supported_formula_types' => array(),
				'formatted' => array(), //Array (Valores admitidos por el plugin de handsontable)
				'allow_to_hide' => true,
				'allow_to_rename' => true,
				'allow_to_save' => true,
				'allow_to_save_sanitization' => true,
				'allow_plain_text' => true,
				'export_key' => $key,
				'default_value' => '',
				'is_locked' => false, // We'll add a lock icon before the cell value and disable editing
				'lock_template_key' => false, // We'll add a lock icon before the cell value and disable editing
				// Tmp. We use the new handsontable renderer only for _default_attributes for now
				// we will use it for all in the future
				'use_new_handsontable_renderer' => false,
				'forced_allow_to_save' => null,
				'forced_supports_formulas' => null,
			);

			$args = wp_parse_args($args, $defaults);

			if (empty($args['key'])) {
				$args['key'] = $key;
			}
			if (empty($args['export_key'])) {
				$args['export_key'] = $key;
			}
			if (in_array($args['type'], array('boton_gallery_multiple'))) {
				$args['wp_media_multiple'] = true;
			}
			if (in_array($args['type'], array('boton_gallery_multiple', 'boton_gallery'))) {
				unset($args['unformatted']);
				$args['column_width'] = 200;
				$args['formatted'] = array(
					'data' => $args['key'],
					'renderer' => 'wp_media_gallery'
				);
			}
			if (in_array($args['type'], array('boton_tiny'))) {
				$args['type'] = '';
				unset($args['unformatted']);
				$args['formatted'] = array(
					'data' => $args['key'],
					'renderer' => 'wp_tinymce'
				);
				$args['allow_to_save'] = true;
			}
			if (in_array($args['type'], array('metabox', 'handsontable'))) {
				$args['supports_formulas'] = false;
				$args['allow_plain_text'] = false;
				$args['allow_to_save'] = false;
			}
			if (in_array($args['type'], array('metabox', 'handsontable'))) {
				if (empty($args['edit_modal_title'])) {
					$args['edit_modal_title'] = $args['title'];
				}
				if (empty($args['edit_button_label'])) {
					$args['edit_button_label'] = sprintf(__('Edit %s', VGSE()->textname), $args['title']);
				}
				if (empty($args['edit_modal_id'])) {
					$args['edit_modal_id'] = 'vgse-modal-editor-' . wp_generate_password(5, false);
				}
			}
			if (in_array($args['type'], array('handsontable')) && $args['use_new_handsontable_renderer']) {
				$args['supports_formulas'] = true;
				$args['allow_plain_text'] = true;
				$args['allow_to_save'] = true;
				$args['formatted']['renderer'] = 'wp_handsontable';
				$args['formatted']['readOnly'] = false;
				$args['unformatted']['renderer'] = 'text';
				$args['unformatted']['readOnly'] = false;
			}

			if (empty($args['default_title'])) {
				$args['default_title'] = $args['title'];
			}

			if (empty($args['unformatted'])) {
				$args['unformatted'] = array(
					'data' => $args['key']
				);
			}
			if (empty($args['formatted'])) {
				$args['formatted'] = array(
					'data' => $args['key']
				);
			}
			if (empty($args['formatted']['data'])) {
				$args['formatted']['data'] = $args['key'];
			}
			if (empty($args['unformatted']['data'])) {
				$args['unformatted']['data'] = $args['key'];
			}

			if (empty($args['value_type'])) {
				if (!empty($args['type'])) {
					$args['value_type'] = $args['type'];
				} elseif ($args['data_type'] === 'post_terms') {
					$args['value_type'] = 'post_terms';
				} else {
					$args['value_type'] = 'text';
				}
			}

			// post_meta is an alias of meta_data
			if ($args['data_type'] === 'post_meta') {
				$args['data_type'] = 'meta_data';
			}

			if (!$args['allow_to_save'] && $args['allow_to_save_sanitization']) {
				$args['formatted']['renderer'] = 'html';
				$args['formatted']['readOnly'] = true;
				$args['unformatted']['renderer'] = 'html';
				$args['unformatted']['readOnly'] = true;
			}
			if (in_array($args['type'], array('external_button'))) {
				$args['formatted']['renderer'] = 'wp_external_button';
				$args['formatted']['readOnly'] = true;
				$args['unformatted']['renderer'] = 'wp_external_button';
				$args['unformatted']['readOnly'] = true;
			}
			if ($args['is_locked']) {
				$args['formatted']['readOnly'] = true;
				$args['unformatted']['readOnly'] = true;
				if (empty($args['formatted']['renderer']) || strpos($args['formatted']['renderer'], 'wp_') === false) {
					$args['formatted']['renderer'] = 'wp_locked';
				}
				if (empty($args['unformatted']['renderer']) || strpos($args['unformatted']['renderer'], 'wp_') === false) {
					$args['unformatted']['renderer'] = 'wp_locked';
				}
			}
			if (is_bool($args['forced_allow_to_save'])) {
				$args['formatted']['readOnly'] = !$args['forced_allow_to_save'];
				$args['unformatted']['readOnly'] = !$args['forced_allow_to_save'];
			}
			if (is_bool($args['forced_supports_formulas'])) {
				$args['supports_formulas'] = $args['forced_supports_formulas'];
			}

			return $args;
		}

		/**
		 * Get all spreadsheet columns
		 * @return array
		 */
		function get_items($skip_filters = false) {
			// Order columns by default, to show the enabled columns first and locked columns after
			foreach ($this->registered_items as $post_type => $columns) {
				$enabled_columns = wp_list_filter($columns, array(
					'lock_template_key' => 'lock_cell_template_pro',
						), 'NOT');
				$locked_pro_columns = wp_list_filter($columns, array(
					'lock_template_key' => 'lock_cell_template_pro',
				));

				$this->registered_items[$post_type] = array_merge($enabled_columns, $locked_pro_columns);
			}

			$spreadsheet_columns = ( $skip_filters ) ? $this->registered_items : apply_filters('vg_sheet_editor/columns/all_items', $this->registered_items);
			return $spreadsheet_columns;
		}

		/**
		 * Get individual spreadsheet column
		 * @return array
		 */
		function get_item($item_key, $provider = 'post', $run_callbacks = false, $skip_filters = false) {
			$items = $this->get_provider_items($provider, $run_callbacks, $skip_filters);
			if (isset($items[$item_key])) {
				return $items[$item_key];
			} else {
				return false;
			}
		}

		function _remove_callbacks_on_items($items) {
			if (empty($items) || !is_array($items)) {
				return array();
			}
			foreach ($items as $column_key => $column_args) {
				if (isset($column_args['formatted'])) {
					if (isset($column_args['formatted']['selectOptions']) && is_callable($column_args['formatted']['selectOptions'])) {
						$items[$column_key]['formatted']['selectOptions'] = array();
					}
					if (isset($column_args['formatted']['source']) && is_callable($column_args['formatted']['source'])) {
						$items[$column_key]['formatted']['source'] = array();
					}
				}
			}
			return $items;
		}

		function _run_callbacks_on_items($items) {
			if (empty($items) || !is_array($items)) {
				return array();
			}
			foreach ($items as $column_key => $column_args) {
				if (isset($column_args['formatted'])) {
					if (empty($column_args['formatted']['callback_args'])) {
						$column_args['formatted']['callback_args'] = array();
					}
					if (isset($column_args['formatted']['selectOptions']) && is_callable($column_args['formatted']['selectOptions'])) {
						$items[$column_key]['formatted']['selectOptions'] = call_user_func_array($column_args['formatted']['selectOptions'], $column_args['formatted']['callback_args']);
					}
					if (isset($column_args['formatted']['source']) && is_callable($column_args['formatted']['source'])) {
						$items[$column_key]['formatted']['source'] = call_user_func_array($column_args['formatted']['source'], $column_args['formatted']['callback_args']);
					}
					if (isset($column_args['formatted']['chosenOptions']) && is_callable($column_args['formatted']['source'])) {
						$data = call_user_func_array($column_args['formatted']['source'], $column_args['formatted']['callback_args']);
						$final_data = array();
						foreach ($data as $term_name) {
							$final_data[] = array(
								'id' => $term_name,
								'label' => $term_name,
							);
						}
						$items[$column_key]['formatted']['chosenOptions']['data'] = $final_data;
					}
				}
			}
			return $items;
		}

		/**
		 * Get all spreadsheet columns by post type
		 * @return array
		 */
		function get_provider_items($provider, $run_callbacks = false, $skip_filters = false) {
			$items = $this->get_items($skip_filters);
			$out = array();
			if (isset($items[$provider])) {
				$out = $items[$provider];

				if ($run_callbacks) {
					$out = $this->_run_callbacks_on_items($out);
				}
			}

			$out = apply_filters('vg_sheet_editor/columns/provider_items', $out, $provider, $run_callbacks, $this);
			return $out;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}