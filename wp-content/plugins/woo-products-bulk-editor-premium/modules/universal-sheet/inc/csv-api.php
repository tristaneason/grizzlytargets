<?php

if (!class_exists('WPSE_CSV_API')) {

	class WPSE_CSV_API {

		static private $instance = false;
		var $uploads_dir = null;
		var $imports_dir = null;
		var $exports_dir = null;

		private function __construct() {
			
		}

		function remove_duplicates_from_file($file_path) {
			$lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$lines = array_unique($lines);
			file_put_contents($file_path, implode(PHP_EOL, $lines));
		}

		function init() {
			$this->uploads_dir = apply_filters('vg_sheet_editor/csv/base_folder', WP_CONTENT_DIR . '/wp-sheet-editor-universal-sheet');
			$this->imports_dir = $this->uploads_dir . '/imports/';
			$this->exports_dir = $this->uploads_dir . '/exports/';

			if (is_admin()) {
				$this->maybe_create_directories();

				// Schedule trash collection
				if (!wp_next_scheduled('wpse_delete_old_csvs') && !wp_installing()) {
					wp_schedule_event(time(), 'daily', 'wpse_delete_old_csvs');
				}
			}


			add_action('vg_sheet_editor/initialized', array($this, 'late_init'));
			$this->maybe_download_file();
		}

		function remove_directory() {
			if (!is_dir($this->uploads_dir)) {
				return;
			}
			require_once ( ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php' );
			$fileSystemDirect = new WP_Filesystem_Direct(false);
			$fileSystemDirect->rmdir($this->uploads_dir, true);
		}

		function late_init() {

			add_action('vg_sheet_editor/on_uninstall', array($this, 'remove_directory'));
			add_filter('vg_sheet_editor/load_rows/full_output', array($this, 'export_csv'), 10, 4);
			add_action('wp_ajax_vgse_import_csv', array($this, 'import_csv'));
			add_action('wp_ajax_vgse_upload_file_for_import', array($this, 'upload_data_for_import'));
			do_action('wpse_delete_old_csvs', array($this, 'delete_old_csvs'));
			$this->delete_old_csvs();
		}

// Read a file and display its content chunk by chunk
		function readfile_chunked($filename, $retbytes = TRUE) {
			$buffer = '';
			$cnt = 0;
			$handle = fopen($filename, 'rb');

			if ($handle === false) {
				return false;
			}

			while (!feof($handle)) {
				$buffer = fread($handle, 1024 * 1024);
				echo $buffer;
				ob_flush();
				flush();

				if ($retbytes) {
					$cnt += strlen($buffer);
				}
			}

			$status = fclose($handle);

			if ($retbytes && $status) {
				return $cnt; // return num. bytes delivered like readfile() does.
			}

			return $status;
		}

		function maybe_download_file() {
			if (empty($_GET['wpseefn'])) {
				return;
			}

			if (strpos($_GET['wpseefn'], '.') !== false || strpos($_GET['wpseefn'], '/') !== false) {
				die();
			}
			$file_name = sanitize_file_name($_GET['wpseefn']);
			$path = $this->exports_dir . $file_name . '.csv';

			if (!file_exists($path)) {
				die(__('The export does not exist. Please export again.', VGSE()->textname));
			}

			// output headers so that the file is downloaded rather than displayed
			header("Content-type: text/csv");
			header("Content-disposition: attachment; filename = $file_name.csv");
			$this->readfile_chunked($path);
			die();
		}

		function maybe_create_directories() {

			if (!is_dir($this->imports_dir)) {
				wp_mkdir_p($this->imports_dir);
				file_put_contents($this->imports_dir . 'index.html', '');
			}
			if (!is_dir($this->exports_dir)) {
				wp_mkdir_p($this->exports_dir);
				file_put_contents($this->exports_dir . 'index.html', '');
			}
		}

		function delete_old_csvs() {
			$files = array_merge(VGSE()->helpers->get_files_list($this->imports_dir, '.csv'), VGSE()->helpers->get_files_list($this->exports_dir, '.csv'));
			foreach ($files as $file) {
				// Delete csv files older than 6 hours to avoid deleting exports in progress.
				$expiration_hours = (int) $this->file_expiration_hours();
				if (file_exists($file) && (time() - filemtime($file) > $expiration_hours * 3600)) {
					unlink($file);
				}
			}
		}

		function count_rows_in_file($file_path, $separator) {
			if (!file_exists($file_path)) {
				return 0;
			}
			$handle = fopen($file_path, 'r');

			$headers = fgetcsv($handle, 0, $separator);

			if (strpos($headers[0], 'sep=') !== false) {
				$headers = fgetcsv($handle, 0, $separator);
			}

			$count = 0;
			while ($line = fgetcsv($handle, 0, $separator)) {

				if (count($headers) > count($line)) {
					$line = array_merge($line, array_fill(0, count($headers) - count($line), ''));
				}
				if (count($headers) !== count($line) && VGSE_DEBUG) {
					continue;
				}
				$count++;
			}

			fclose($handle);
			return $count;
		}

		function replace_file($path, $string, $replace) {
			if (!file_exists($path)) {
				return false;
			}
			$file = fopen($path, 'r');

			$tmp_file_path = $this->imports_dir . sanitize_file_name('tmp-' . date('Y-m-d-H-i-s') . '-' . wp_generate_password(10, false)) . '.csv';
			file_put_contents($tmp_file_path, '');

			if (is_resource($file)) {
				while (feof($file) === false) {
					file_put_contents($tmp_file_path, str_replace($string, $replace, fgets($file)), FILE_APPEND);
				}

				fclose($file);
			}

			unlink($path);

			return rename($tmp_file_path, $path);
		}

		function get_separator($file_path, $separator = ',') {
			if (!file_exists($file_path)) {
				return $separator;
			}
			$handle = fopen($file_path, 'r');
			$headers = fgetcsv($handle, 0, $separator);

			if (strpos($headers[0], 'sep=') !== false) {
				$headers = fgetcsv($handle, 0, $separator);
			}

			if (count($headers) === 1) {
				$separator = $this->_detect_csv_delimiter($handle);
			}
			return $separator;
		}

		function get_rows($file_path, $separator = ',', $decode_quotes = false, $per_page = null, $start_position = 0) {
			$out = array(
				'rows' => array(),
				'file_position' => 0
			);
			if (!file_exists($file_path)) {
				return $out;
			}

			if ($decode_quotes) {
				$this->replace_in_file($file_path, '&quot;', '"');
			}
			$handle = fopen($file_path, 'r');
			$headers = fgetcsv($handle, 0, $separator);

			if (isset($headers[0]) && strpos($headers[0], 'sep=') !== false) {
				$headers = fgetcsv($handle, 0, $separator);
			}

			$headers = array_map('trim', $headers);
			// Remove BOM signature from the first item.
			if (isset($headers[0])) {
				$headers[0] = $this->remove_utf8_bom($headers[0]);
			}

			if ($start_position) {
				fseek($handle, $start_position);
			}
			if (!$per_page) {
				$per_page = PHP_INT_MAX;
			}

			while (count($out['rows']) < $per_page && $line = fgetcsv($handle, 0, $separator)) {

				if (count($headers) > count($line)) {
					$line = array_merge($line, array_fill(0, count($headers) - count($line), ''));
				}
				if (count($headers) < count($line)) {
					$headers = array_merge($headers, array_fill(0, count($line) - count($headers), ''));
				}
				if (count($headers) !== count($line) && VGSE_DEBUG) {
					var_dump('$headers', $headers, '$line', $line);
					die();
				}
				$out['rows'][] = array_combine($headers, $line);
			}
			$out['file_position'] = ftell($handle);

			fclose($handle);
			return $out;
		}

		function prepare_json_import($settings) {

			if (!isset($settings['data'])) {
				return new WP_Error('wpse', __('Missing required field "data".', VGSE()->textname));
			}

			$out = array(
				'rows' => $settings['data'],
				'total' => $settings['total_rows']
			);
			return $out;
		}

		/**
		 * Remove UTF-8 BOM signature.
		 *
		 * @param  string $string String to handle.
		 * @return string
		 */
		protected function remove_utf8_bom($string) {
			if ('efbbbf' === substr(bin2hex($string), 0, 6)) {
				$string = substr($string, 3);
			}

			return $string;
		}

		function _detect_csv_delimiter($fh) {
			$delimiters = ["\t", ";", "|", ","];
			$data_1 = null;
			$data_2 = null;
			$delimiter = $delimiters[0];
			foreach ($delimiters as $d) {
				$data_1 = fgetcsv($fh, 4096, $d);
				if (is_array($data_1) && count($data_1) > count($data_2)) {
					$delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
					$data_2 = $data_1;
				}
				rewind($fh);
			}

			return $delimiter;
		}

		function prepare_csv_import($settings) {

			$separator = ( empty($settings['separator'])) ? ',' : sanitize_text_field($settings['separator']);
			$decode_quotes = ( empty($settings['decode_quotes'])) ? false : true;
			$file_path = $settings['import_file'];
			$per_page = (!empty(VGSE()->options['be_posts_per_page_save']) ) ? (int) VGSE()->options['be_posts_per_page_save'] : 4;
			$file_position = !empty($settings['file_position']) ? (int) $settings['file_position'] : 0;
			$separator = $this->get_separator($file_path, $separator);
			$start_row = (!empty($settings['start_row'])) ? (int) $settings['start_row'] : 0;



			// If we are skipping rows (start from a row > 0), we retrieve the skipped rows to get the file position
			// and reset the file position, so the normal import starts from there.
			if ($start_row > 0 && (int) $settings['page'] === 1) {
				$rows_to_skip = $this->get_rows($file_path, $separator, $decode_quotes, $start_row - 1, 0);
				$file_position = $rows_to_skip['file_position'];
				unset($rows_to_skip);
			}

			$total = $this->count_rows_in_file($file_path, $separator);
			$file_content = $this->get_rows($file_path, $separator, $decode_quotes, $per_page, $file_position);

			$out = array(
				'rows' => $file_content['rows'],
				'total' => $total,
				'file_position' => $file_content['file_position']
			);
			return $out;
		}

		function import_csv() {
			$required_fields = array(
				'nonce',
				'post_type',
				'page',
				'sheet_editor_column',
				'source_column',
				'writing_type',
				'import_type',
				'total_rows',
				'vgse_plain_mode',
				'vgse_import',
			);

			foreach ($required_fields as $required_field) {
				if (empty($_REQUEST[$required_field])) {
					wp_send_json_error(array('message' => __('Missing required field. Please start the process again.', VGSE()->textname)));
				}
			}

			if (!wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($_REQUEST['post_type'])) {
				wp_send_json_error(array('message' => __('Not allowed.', VGSE()->textname)));
			}
			$settings = VGSE()->helpers->clean_data($_REQUEST);
			$out = $this->import_data($settings);

			if (is_wp_error($out)) {
				wp_send_json_error(array_merge(array('message' => $out->get_error_message()), (array) $out->get_error_data()));
			}

			wp_send_json_success($out);
		}

		function import_data($settings) {
			$post_type = $settings['post_type'];
			$writing_type = $settings['writing_type'];
			$nonce = wp_create_nonce('bep-nonce');
			$per_page = (empty($settings['per_page']) && !empty(VGSE()->options) && !empty(VGSE()->options['be_posts_per_page_save']) ) ? (int) VGSE()->options['be_posts_per_page_save'] : (int) $settings['per_page'];

			$editor = VGSE()->helpers->get_provider_editor($post_type);
			VGSE()->current_provider = $editor->provider;

			if ($settings['import_type'] === 'csv') {
				$prepared_rows = $this->prepare_csv_import($settings);
			} elseif ($settings['import_type'] === 'json') {
				$prepared_rows = $this->prepare_json_import($settings);
			}

			$rows = $prepared_rows['rows'];
			$total = $prepared_rows['total'];
			$check_wp_fields = apply_filters('vg_sheet_editor/import/existing_check_wp_field', array_filter($settings['existing_check_wp_field']), $settings, $prepared_rows);

			if (empty($rows)) {
				return new WP_Error('wpse', __('<p>Complete</p>.', VGSE()->textname), array('code' => 400,
					'force_complete' => true));
			}
			if (is_wp_error($prepared_rows)) {
				return $prepared_rows;
			}


			// Prepare headers mapping
			$csv_headers = array_keys(current($rows));
			$sheet_editor_column = $settings['sheet_editor_column'];
			$final_headers_map = array();
			foreach ($csv_headers as $header) {
				$map_index = array_search($header, $settings['source_column']);
				$final_headers_map[$header] = ($map_index !== false && isset($sheet_editor_column[$map_index])) ? $sheet_editor_column[$map_index] : $header;
			}

			$use_mb = function_exists('mb_convert_encoding');
			foreach ($rows as $row_index => $row) {
				foreach ($row as $column_key => $column_value) {
					// Convert UTF8.
					if ($use_mb) {
						$encoding = mb_detect_encoding($column_value, mb_detect_order(), true);
						if ($encoding) {
							$column_value = mb_convert_encoding($column_value, 'UTF-8', $encoding);
						} else {
							$column_value = mb_convert_encoding($column_value, 'UTF-8', 'UTF-8');
						}
					} else {
						$column_value = wp_check_invalid_utf8($column_value, true);
					}

					$new_column_key = $final_headers_map[$column_key];
					if ($column_key !== $new_column_key) {
						// Rename header according to the columns mapping
						$rows[$row_index][$new_column_key] = $column_value;
						unset($rows[$row_index][$column_key]);
					} else {
						$rows[$row_index][$column_key] = $column_value;
					}
				}

				if (!empty($rows[$row_index]['id']) && empty($rows[$row_index]['ID'])) {
					$rows[$row_index]['ID'] = $rows[$row_index]['id'];
				}
			}

			// If we don't have fields to find existing rows, but we are importing IDs, we auto set the ID as check_wp_field
			if (empty($check_wp_fields) && in_array($writing_type, array('both', 'only_update'), true) && array_intersect($sheet_editor_column, array('id', 'ID'))) {
				$check_wp_fields = array('ID');
			}

			// If we don't have fields to find existing rows, we create everything as new
			if (empty($check_wp_fields)) {
				$writing_type = 'all_new';
			}

			// If writing_type says all rows are new posts			
			if ($writing_type === 'all_new') {
				foreach ($rows as $row_index => $row) {
					$rows[$row_index]['ID'] = null;
				}
			} else {
				$all_meta_keys = VGSE()->helpers->get_all_meta_keys($post_type);
				foreach ($rows as $row_index => $row) {
					$search_args = array_filter(array_intersect_key($row, array_combine($check_wp_fields, array_fill(0, count($check_wp_fields), ''))));

					$meta_query = array(
						'meta_query' => array()
					);
					// If the row has all the wp fields required for the search and they're not empty, make the search
					if (count($search_args) === count($check_wp_fields) && !empty($check_wp_fields)) {
						$rows[$row_index]['ID'] = null;
						foreach ($check_wp_fields as $field_key) {
							$meta_query['meta_query'][] = array(
								'key' => $field_key,
								'value' => $row[$field_key],
								'source' => in_array($field_key, $all_meta_keys) ? 'meta' : 'post_data',
								'compare' => '='
							);
						}
						$found_post_id = apply_filters('vg_sheet_editor/import/find_post_id', null, $row, $post_type, $meta_query, $writing_type, $check_wp_fields);
						if (is_null($found_post_id)) {
							$find_row_args = apply_filters('vg_sheet_editor/import/find_post_id_args', array(
								'nonce' => $nonce,
								'post_type' => $post_type,
								'return_raw_results' => true,
								'wp_query_args' => array(
									'posts_per_page' => 1,
									'fields' => 'ids'
								),
								'filters' => http_build_query($meta_query),
								'wpse_source' => 'load_rows'
							));
							$_REQUEST['filters'] = $find_row_args['filters'];
							$found = VGSE()->helpers->get_rows($find_row_args);

							if (is_array($found) && !empty($found[0]) && is_numeric($found[0])) {
								$found_post_id = $found[0];
							}
						}
						if (!empty($found_post_id)) {
							$rows[$row_index]['ID'] = $found_post_id;
						}
					}
				}
				// We used this inside the previous foreach to make the advanced search work with get_rows()
				if (isset($_REQUEST['filters'])) {
					unset($_REQUEST['filters']);
				}

				if ($writing_type === 'only_update') {
					$rows = wp_list_filter($rows, array('ID' => null), 'NOT');
				} elseif ($writing_type === 'only_new') {
					$rows = wp_list_filter($rows, array('ID' => null));
				}
			}

			// If writing_type allows to create (either only new or both)
			$total_updated = count($rows);
			$created = count(wp_list_filter($rows, array('ID' => null)));

			$save_result = VGSE()->helpers->save_rows(apply_filters('vg_sheet_editor/import/save_rows_args', array(
				'nonce' => $nonce,
				'data' => $rows,
				'post_type' => $post_type,
				'allow_to_create_new' => true,
				'wpse_source' => 'import',
				'wpse_import_settings' => $settings
			)));
			if (is_wp_error($save_result)) {
				return $save_result;
			}

			$processed = ( $per_page >= $total || ($per_page * $settings['page'] ) >= $total ) ? $total : $per_page * $settings['page'];
			$out = array(
				'message' => '<p>' . sprintf(__('%d of %d items have been processed from the file. {total_updated} items updated and {total_created} items created.', VGSE()->textname), $processed, $total, $total_updated, $created) . '</p>',
				'total' => (int) $total,
				'processed' => (int) $processed,
				'updated' => (int) $total_updated,
				'created' => (int) $created,
				'file_position' => $prepared_rows['file_position']
			);

			return $out;
		}

		function upload_data_for_import() {
			if (empty($_REQUEST['nonce']) || empty($_REQUEST['post_type']) || !isset($_REQUEST['data']) || empty($_REQUEST['data_type']) || !wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($_REQUEST['post_type'])) {
				wp_send_json_error(array('message' => __('Not allowed. Please start the process again.', VGSE()->textname)));
			}

			$data_type = sanitize_text_field($_REQUEST['data_type']);
			$post_type = sanitize_text_field($_REQUEST['post_type']);
			$separator = sanitize_text_field($_REQUEST['separator']);
			$decode_quotes = ( empty($_REQUEST['decode_quotes'])) ? false : true;
			$data = $_REQUEST['data'];

			$base_dir = $this->imports_dir;

			$file_path = $base_dir . sanitize_file_name($post_type . '-' . date('Y-m-d-H-i-s') . '-' . wp_generate_password(10, false)) . '.csv';


			$allowed_input_type = array('csv', 'json');
			if ($data_type === 'url') {
				if (filter_var($data, FILTER_VALIDATE_URL) === FALSE) {
					wp_send_json_error(array('message' => __('Wrong file url', VGSE()->textname)));
				}
				$file_type = pathinfo(basename(strtok($data, "?")), PATHINFO_EXTENSION);
				if (!in_array($file_type, $allowed_input_type)) {
					wp_send_json_error(array('message' => __('Wrong file extension. We accept CSV only', VGSE()->textname)));
				}
				$tmp_file = download_url($data);

				if (!is_wp_error($tmp_file) && file_exists($tmp_file)) {
					rename($tmp_file, $file_path);
				}
			} elseif ($data_type === 'local') {

				if (empty($_FILES)) {
					wp_send_json_error(array('message' => __('File could not be uploaded. Please start the process again.', VGSE()->textname)));
				}
				$file_type = pathinfo(basename(strtok($_FILES["file"]["name"], "?")), PATHINFO_EXTENSION);

				if (!in_array($file_type, $allowed_input_type)) {
					wp_send_json_error(array('message' => __('Wrong file extension. We accept CSV only', VGSE()->textname)));
				}

				if (file_exists($_FILES["file"]["tmp_name"])) {
					move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
				}
			} elseif ($data_type === 'json') {
				if (!is_array($data)) {
					wp_send_json_error(array('message' => __('Wrong data format', VGSE()->textname)));
				}
				$headers = $data[0];
				unset($data[0]);

				$filtered = array();
				foreach ($data as $row) {
					$only_filled_cells = array_filter($row);
					if (!empty($only_filled_cells)) {
						$filtered[] = $row;
					}
				}


				$this->_array_to_csv($filtered, $file_path, implode($separator, $headers), $separator);
			} elseif ($data_type === 'server_file') {
				$file_path = str_replace('"', '', wp_unslash($data));
				$file_type = pathinfo(basename($file_path), PATHINFO_EXTENSION);
				if (!in_array($file_type, $allowed_input_type)) {
					wp_send_json_error(array('message' => __('Wrong file extension. We accept CSV only', VGSE()->textname)));
				}
			}

			if (!file_exists($file_path)) {
				wp_send_json_error(array('message' => __('File could not be uploaded. Please start the process again.', VGSE()->textname)));
			}


			$separator = $this->get_separator($file_path, $separator);
			$total = $this->count_rows_in_file($file_path, $separator);
			$file_content = $this->get_rows($file_path, $separator, $decode_quotes, 5);
			$first_rows = $file_content['rows'];

			if (empty($first_rows)) {
				wp_send_json_error(array('message' => __('File uploaded succesfully but it\'s not a valid CSV file or it uses the wrong encoding. If you edited the file in Excel, verify it was saved as UTF-8 and keep in mind that, sometimes copy pasting from external places adds invalid characters. So make sure you paste only the values and not paste the formatting to avoid pasting invalid characters.', VGSE()->textname)));
			}

			wp_send_json_success(array(
				'rowHeaders' => array_map('strval', array_keys(current($first_rows))),
				'firstRows' => array_values($first_rows),
				'totalRows' => $total,
				'filePath' => $file_path,
				'fileUrl' => str_replace(WP_CONTENT_DIR, WP_CONTENT_URL, $file_path),
			));
		}

		function get_saved_exports($post_type) {

			$saved_exports = get_option('vgse_saved_exports');
			if (empty($saved_exports)) {
				$saved_exports = array();
			}

			if (!isset($saved_exports[$post_type])) {
				$saved_exports[$post_type] = array();
			}
			return $saved_exports[$post_type];
		}

		function save_export($data) {
			if (empty($data['name'])) {
				return;
			}
			$post_type = $data['post_type'];
			$saved_exports = get_option('vgse_saved_exports');
			if (empty($saved_exports)) {
				$saved_exports = array();
			}

			if (!isset($saved_exports[$post_type])) {
				$saved_exports[$post_type] = array();
			}

			$same_name = wp_list_filter($saved_exports[$post_type], array('name' => $data['name']));
			foreach ($same_name as $index => $same_name_export) {
				unset($saved_exports[$post_type][$index]);
			}
			$saved_exports[$post_type][] = $data;
			update_option('vgse_saved_exports', $saved_exports);
		}

		function export_csv($out, $wp_query_args, $spreadsheet_columns, $clean_data) {
			if (empty($clean_data['export_key'])) {
				return $out;
			}

			if (!empty($clean_data['save_for_later']) && current_user_can('manage_options')) {
				$this->save_export($clean_data['save_for_later']);
			}

			$base_dir = $this->exports_dir;
			$csv_file = $base_dir . sanitize_file_name($clean_data['export_key']) . '.csv';

			$cleaned_rows = apply_filters('vg_sheet_editor/export/pre_cleanup', array_values($out['rows']), $clean_data, $wp_query_args, $spreadsheet_columns);
			$allowed_column_keys = apply_filters('vg_sheet_editor/export/allowed_column_keys', array_keys($spreadsheet_columns), $cleaned_rows, $clean_data, $wp_query_args);
			$required_column_keys = array_keys($spreadsheet_columns);

			// If this is not the first page, we make sure that columns from page 1 are allowed
			if ((int) $wp_query_args['paged'] > 1) {
				$first_rows_raw = $this->get_rows($csv_file, ',', false, 3);
				$first_rows = $first_rows_raw['rows'];
				$headers_with_labels = wp_list_pluck($spreadsheet_columns, 'key', 'title');
				unset($headers_with_labels['ID']);
				$headers_with_labels['record_id'] = 'ID';
				$existing_file_keys = array();

				foreach ($first_rows[0] as $column_key => $column_value) {
					$existing_file_keys[] = isset($headers_with_labels[$column_key]) ? $headers_with_labels[$column_key] : $column_key;
				}

				$existing_file_keys = apply_filters('vg_sheet_editor/export/existing_file_keys', $existing_file_keys, $first_rows[0], $cleaned_rows, $clean_data, $wp_query_args);

				$allowed_column_keys = array_unique(array_merge($allowed_column_keys, $existing_file_keys));
				$required_column_keys = array_unique(array_merge($existing_file_keys, $required_column_keys));
			} elseif (file_exists($csv_file)) {
				// this is a new export if we're exporting the page 1 again, so delete the file				
				unlink($csv_file);
			}

			foreach ($cleaned_rows as $row_index => $row) {

				// Remove all fields from the row that are not found in the list of fields to export
				$row = array_intersect_key($row, array_fill_keys($allowed_column_keys, ''));
				$required_column_keys = array_unique(array_merge($required_column_keys, array_keys($row)));

				foreach ($required_column_keys as $column_key) {
					if (!isset($row[$column_key])) {
						$row[$column_key] = '';
					}
					// Convert line breaks to p and br tags before we strip the line breaks
					if (isset($spreadsheet_columns[$column_key]) && in_array($spreadsheet_columns[$column_key]['type'], array('boton_tiny')) && isset($row[$column_key])) {
						$row[$column_key] = wpautop($row[$column_key]);
					}

					// Fields with objects as value aren't compatible, so we export them as an empty string
					if (is_object($row[$column_key])) {
						$row[$column_key] = '';
					}

					// Remove line breaks from all values because they dont work well with csv editors
					$row[$column_key] = preg_replace('~[\r\n]+~', '', $row[$column_key]);

					// Fields with empty spaces as value are returned as empty string
					$trimmed = trim($row[$column_key]);
					if (empty($trimmed)) {
						$row[$column_key] = $trimmed;
					}
				}
				$cleaned_rows[$row_index] = $row;
			}


			// If the column used in the first page of the export, is not found on the next pages,
			// we'll add an empty value. All pages must include the same columns.
			// This also helps sort the columns. All items must have same values in same order.
			$all_column_keys = array_fill_keys($required_column_keys, '');
			foreach ($cleaned_rows as $row_index => $row) {
				$cleaned_rows[$row_index] = array_merge($all_column_keys, $row);
			}

			$headers_with_labels = wp_list_pluck($spreadsheet_columns, 'title', 'key');
			$headers = array_values(array_merge(array_combine($required_column_keys, $required_column_keys), $headers_with_labels));

			// Replace ID column with record_id to prevent issue with Excel
			$id_column_index = array_search('ID', $headers);
			$headers[$id_column_index] = 'record_id';

			$final_rows = apply_filters('vg_sheet_editor/export/final_rows', $cleaned_rows, $clean_data, $wp_query_args);
			$final_headers = apply_filters('vg_sheet_editor/export/final_headers', array_filter($headers), $clean_data, $wp_query_args);
			$this->_array_to_csv($final_rows, $csv_file, implode(',', $final_headers));
			$out['rows'] = $cleaned_rows;

			$out['message'] = sprintf(__('Processing: %d of %d rows have been exported.', VGSE()->textname), ($out['total'] > ( $wp_query_args['posts_per_page'] * $wp_query_args['paged'] ) ) ? $wp_query_args['posts_per_page'] * $wp_query_args['paged'] : $out['total'], $out['total']);

			if (($wp_query_args['posts_per_page'] * $wp_query_args['paged']) >= $out['total']) {
				if ((bool) $clean_data['add_excel_separator_flag']) {
					$csv_contents = file_get_contents($csv_file);
					file_put_contents($csv_file, $this->add_utf8_bom('sep=,' . PHP_EOL . $this->remove_utf8_bom($csv_contents)));
				}
				$this->remove_duplicates_from_file($csv_file);
				$out['export_file_url'] = add_query_arg('wpseefn', sanitize_file_name($clean_data['export_key']), home_url());
				$expiration_hours = (int) $this->file_expiration_hours();
				$out['message'] .= sprintf(__('<br><br>The export finished.<br><br>The download should start automatically. If it doesn\'t start automatically you can find the file in the folder /wp-content/wp-sheet-editor-universal-sheet/exports/ on your server.<br><br>The export files are deleted automatically after %d hours.', VGSE()->textname), $expiration_hours);
			}

			return $out;
		}

		function file_expiration_hours() {
			return apply_filters('vg_sheet_editor/csv/file_expiration_hours', 6);
		}

		function _array_to_csv($data, $filepath, $csv_headers = null, $delimiter = ',') {

			// Create the csv headers if missing
			if (empty($csv_headers)) {
				$first_row = current($data);
				$csv_headers = implode($delimiter, array_keys($first_row));
			}

			// If file exists, we update the headers on every update
			// If file not exists, we create empty file with just the csv headers
			if (file_exists($filepath)) {
				$file = file($filepath, FILE_IGNORE_NEW_LINES);
			} else {
				$file = array();
			}

			$file[0] = $csv_headers;
			file_put_contents($filepath, $this->add_utf8_bom(implode(PHP_EOL, array_filter($file))) . PHP_EOL);

			// Append the data
			$fp = fopen($filepath, 'a');
			foreach ($data as $row) {
				if (empty($row)) {
					continue;
				}
				fputcsv($fp, $row, $delimiter);
			}

			fclose($fp);
		}

		// chr( 239 ) . chr( 187 ) . chr( 191 ) .  Excel requires this to read the utf-8 file properly
		function add_utf8_bom($text) {
			// We remove it first to avoid adding it twice
			$text = $this->remove_utf8_bom($text);
			return chr(239) . chr(187) . chr(191) . $text;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_CSV_API::$instance) {
				WPSE_CSV_API::$instance = new WPSE_CSV_API();
				WPSE_CSV_API::$instance->init();
			}
			return WPSE_CSV_API::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_CSV_API_Obj')) {

	function WPSE_CSV_API_Obj() {
		return WPSE_CSV_API::get_instance();
	}

}

WPSE_CSV_API_Obj();
