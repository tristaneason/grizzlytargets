<?php

if (!class_exists('WP_Sheet_Editor_Ajax')) {

	class WP_Sheet_Editor_Ajax {

		static private $instance = false;

		private function __construct() {
			
		}

		/*
		 * Controller for loading posts to the spreadsheet
		 */

		function delete_row_ids() {

			$settings = VGSE()->helpers->clean_data($_REQUEST);
			if (empty($settings['post_type']) || !VGSE()->helpers->user_can_edit_post_type($settings['post_type']) || empty($settings['nonce']) || !wp_verify_nonce($settings['nonce'], 'bep-nonce') || empty($settings['ids'])) {
				$message = array('message' => __('You dont have enough permissions to do this action.', VGSE()->textname));
				wp_send_json_error($message);
			}

			foreach ($settings['ids'] as $id) {
				VGSE()->helpers->get_current_provider()->update_item_data(array(
					'ID' => (int) $id,
					'post_status' => 'delete',
					'wpse_status' => 'delete',
					'comment_approved' => 'delete',
				));
			}
			wp_send_json_success(array('message' => __('Rows deleted successfully', VGSE()->textname)));
		}

		function get_taxonomy_terms() {

			$settings = VGSE()->helpers->clean_data($_REQUEST);
			if (empty($settings['post_type']) || !VGSE()->helpers->user_can_view_post_type($settings['post_type']) || empty($settings['nonce']) || !wp_verify_nonce($settings['nonce'], 'bep-nonce') || !taxonomy_exists($settings['taxonomy_key'])) {
				$message = array('message' => __('You dont have enough permissions to do this action.', VGSE()->textname));
				wp_send_json_error($message);
			}

			$source = (!empty($settings['wpse_source'])) ? $settings['wpse_source'] : '';
			$out = VGSE()->data_helpers->get_taxonomy_terms($settings['taxonomy_key'], $source);
			wp_send_json_success($out);
		}

		function load_rows() {

			$settings = $_REQUEST;
			if (empty($settings['nonce']) || !wp_verify_nonce($settings['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_view_post_type($settings['post_type'])) {
				$message = array('message' => __('You dont have enough permissions to load rows.', VGSE()->textname));
				wp_send_json_error($message);
			}

			// Reset the number of rows per page, we receive this parameter from the client when
			// the current rows per page > 300 and the request failed
			if (!empty($settings['wpse_reset_posts_per_page'])) {
				$options = get_option(VGSE()->options_key);
				$options['be_posts_per_page'] = (int) $settings['wpse_reset_posts_per_page'];
				VGSE()->options['be_posts_per_page'] = (int) $settings['wpse_reset_posts_per_page'];
				update_option(VGSE()->options_key, $options);
			}

			$source_prefix = (!empty($settings['wpse_source_suffix'])) ? (string) $settings['wpse_source_suffix'] : '';
			$settings['wpse_source'] = 'load_rows' . $source_prefix;
			$rows = VGSE()->helpers->get_rows($settings);

			if (is_wp_error($rows)) {
				wp_send_json_error(array(
					'message' => $rows->get_error_message()
				));
			}

			$rows['rows'] = array_values($rows['rows']);
			$rows['deleted'] = array_unique(VGSE()->deleted_rows_ids);
			wp_send_json_success($rows);
		}

		/*
		 * Controller for saving posts changes
		 */

		function save_rows() {

			if (empty($_REQUEST['post_type']) || !VGSE()->helpers->user_can_edit_post_type($_REQUEST['post_type'])) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to save changes.', VGSE()->textname)));
			}
			$result = VGSE()->helpers->save_rows($_REQUEST);

			if (is_wp_error($result)) {
				wp_send_json_error(array(
					'message' => $result->get_error_message()
				));
			}

			// We use this flag to customize the user experience and hide safety notifications
			// if the user already knows how to save data on the sheet
			update_user_meta(get_current_user_id(), 'wpse_has_saved_sheet', 1);
			wp_send_json_success(array('message' => __('Changes saved successfully', VGSE()->textname), 'deleted' => array_unique(VGSE()->deleted_rows_ids)));
		}

		/*
		 * Controller for saving new post.
		 */

		function insert_individual_post() {
			$data = VGSE()->helpers->clean_data($_REQUEST);
			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_edit_post_type($data['post_type'])) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to create new rows.', VGSE()->textname)));
			}
			$post_type = $data['post_type'];
			$rows = (int) $data['rows'];

			$result = VGSE()->helpers->create_placeholder_posts($post_type, $rows);

			if (is_wp_error($result)) {
				wp_send_json_error(array(
					'message' => $result->get_error_message()
				));
			}
			wp_send_json_success(array('message' => $result, 'deleted' => array_unique(VGSE()->deleted_rows_ids)));
		}

		/**
		 * Find posts by name
		 */
		function find_post_by_name() {
			global $wpdb;
			$data = VGSE()->helpers->clean_data($_REQUEST);
			$nonce = $data['nonce'];

			$post_type = (!empty($data['post_type']) ) ? sanitize_text_field($data['post_type']) : false;
			$search = (!empty($data['search']) ) ? sanitize_text_field($data['search']) : false;

			if (!wp_verify_nonce($nonce, 'bep-nonce') || !VGSE()->helpers->user_can_view_post_type($data['post_type'])) {
				wp_send_json_error(array('message' => __('Request not allowed. Try again later.', VGSE()->textname)));
			}

			if (empty($post_type) || empty($search)) {
				wp_send_json_error(array('message' => __('Missing parameters.', VGSE()->textname)));
			}

			$posts_found = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_type = '" . esc_sql($post_type) . "' AND post_title LIKE '%" . esc_sql($search) . "%' LIMIT 10");

			if (empty($posts_found)) {
				wp_send_json_error(array('message' => __('No items found.', VGSE()->textname)));
			}

			$out = array();
			foreach ($posts_found as $post) {
				$out[] = array(
					'id' => $post->post_type . '--' . $post->ID,
					'text' => $post->post_title . ' ( ID: ' . $post->ID . ', ' . $post->post_type . ' )',
					'title' => $post->post_title
				);
			}
			wp_send_json_success(array('data' => $out));
		}

		/**
		 * Controller for saving individual field of post
		 */
		function save_single_post_data() {
			$_REQUEST = VGSE()->helpers->clean_data($_REQUEST);
			if (!wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce')) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to save changes.', VGSE()->textname)));
			}
			$content = html_entity_decode($_REQUEST['content']);
			$id = (int) $_REQUEST['post_id'];
			$key = $_REQUEST['key'];
			$type = $_REQUEST['type'];
			$post_type = $_REQUEST['post_type'];

			if (VGSE()->options['be_disable_post_actions']) {
				$post_type = get_post_type($id);
				VGSE()->helpers->remove_all_post_actions($post_type);
			}

			if (!VGSE()->helpers->user_can_edit_post_type($post_type)) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to save changes.', VGSE()->textname)));
			}
			do_action('vg_sheet_editor/save_single_post_data/before', $id, $content, $key, $type);
			$result = VGSE()->data_helpers->save_single_post_data($id, $content, $key, $type);

			do_action('vg_sheet_editor/save_single_post_data/after', $result, $id, $content, $key, $type);
			if (is_wp_error($result)) {

				$errors = $result->get_error_messages();
				wp_send_json_success(array('message' => sprintf(__('Error: %s', VGSE()->textname), implode(', ', $errors))));
			} else {
				VGSE()->helpers->increase_counter('editions');
				VGSE()->helpers->increase_counter('processed');

				$title = VGSE()->data_helpers->get_post_data('post_title', $id);
				wp_send_json_success(array('message' => sprintf(__('Saved: %s', VGSE()->textname), $title)));
			}
		}

		/*
		 * Get tinymce editor content
		 */

		function get_wp_post_single_data() {
			$data = VGSE()->helpers->clean_data($_REQUEST);
			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || !VGSE()->helpers->user_can_view_post_type($data['post_type'])) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to load rows.', VGSE()->textname)));
			}

			$post_id = (int) $data['pid'];
			$key = $data['key'];
			$type = $data['type'];
			$raw = (!empty($data['raw']) ) ? $data['raw'] : false;
			$post_type = $data['post_type'];

			$column_settings = VGSE()->helpers->get_column_settings($key, $post_type);

			$content = '';
			if ($type === 'post_data') {
				$content = VGSE()->data_helpers->get_post_data($key, $post_id);
			} elseif ($type === 'meta_data' || $type === 'post_meta') {
				$content = VGSE()->helpers->get_current_provider()->get_item_meta($post_id, $key, true);
			}
			if ($raw) {
				$out = $content;
			} else {
				$out = html_entity_decode(htmlspecialchars_decode($content));

				if (!empty($column_settings['formatted']) && !empty($column_settings['formatted']['renderer']) && $column_settings['formatted']['renderer'] === 'wp_tinymce' && empty(VGSE()->options['be_disable_wpautop'])) {
					$out = wpautop($out);
				}
			}


			wp_send_json_success(array('message' => $out));
		}

		/**
		 * Search taxonomy term
		 * @global obj $wpdb
		 */
		function search_users() {
			global $wpdb;
			$data = VGSE()->helpers->clean_data($_REQUEST);

			if (empty($data['nonce']) || !wp_verify_nonce($data['nonce'], 'bep-nonce') || empty($data['post_type']) || !VGSE()->helpers->user_can_view_post_type($data['post_type'])) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to search taxonomy terms.', VGSE()->textname)));
			}
			$search = (!empty($data['search']) ) ? sanitize_text_field($data['search']) : false;

			if (empty($search)) {
				wp_send_json_error(array('message' => __('Missing parameters.', VGSE()->textname)));
			}

			$out = $wpdb->get_col("SELECT user_login FROM $wpdb->users WHERE user_email LIKE '%" . esc_sql($search) . "%' OR user_nicename LIKE '%" . esc_sql($search) . "%' OR user_login LIKE '%" . esc_sql($search) . "%' OR display_name LIKE '%" . esc_sql($search) . "%' LIMIT 5");
			wp_send_json_success(array('data' => $out));
		}

		function search_taxonomy_terms() {
			$data = VGSE()->helpers->clean_data($_REQUEST);
			$is_global_search = false;
			if (!empty($data['global_search'])) {
				$is_global_search = true;
			}
			// Note. The global search is allowed for administrators only
			if (!wp_verify_nonce($data['nonce'], 'bep-nonce') || (!$is_global_search && !VGSE()->helpers->user_can_view_post_type($data['post_type'])) || ($is_global_search && !current_user_can('manage_options'))) {
				wp_send_json_error(array('message' => __('You dont have enough permissions to search taxonomy terms.', VGSE()->textname)));
			}

			if (!$is_global_search) {
				$post_type = (!empty($data['post_type']) ) ? sanitize_text_field($data['post_type']) : false;
				if (empty($post_type)) {
					wp_send_json_error(array('message' => __('Missing parameters.', VGSE()->textname)));
				}
			}
			$search = (!empty($data['search']) ) ? sanitize_text_field($data['search']) : false;

			if (empty($search)) {
				wp_send_json_error(array('message' => __('Missing parameters.', VGSE()->textname)));
			}

			if ($is_global_search) {
				$taxonomies = get_taxonomies(array(
					'show_ui' => true,
					'hierarchical' => true,
						), 'names');
			} else {
				$taxonomies = VGSE()->helpers->get_post_type_taxonomies_single_data($post_type, 'name');
			}

			if (!empty($data['taxonomies'])) {
				$taxonomies = is_string($data['taxonomies']) ? explode(',', sanitize_text_field($data['taxonomies'])) : array_map('sanitize_text_field', $data['taxonomies']);
			}

			if (empty($taxonomies)) {
				wp_send_json_error(array('message' => __('No taxonomies found.', VGSE()->textname)));
			}
			global $wpdb;

			$sql = "SELECT term.slug id,term.name text,tax.taxonomy taxonomy FROM $wpdb->term_taxonomy as tax JOIN $wpdb->terms as term ON term.term_id = tax.term_id WHERE tax.taxonomy IN ('" . implode("','", $taxonomies) . "') AND term.name LIKE '%" . esc_sql($search) . "%' ";
			$results = $wpdb->get_results($sql, ARRAY_A);

			if (!$results || is_wp_error($results)) {
				$results = array();
			}

			$output_format = ( isset($data['output_format'])) ? $data['output_format'] : '';
			if (empty($output_format)) {
				$output_format = '%taxonomy%--%slug%';
			} else {
				$output_format = sanitize_text_field($output_format);
			}
			$taxonomies_labels = array();
			$out = array();
			foreach ($results as $result) {

				if (!isset($taxonomies_labels[$result['taxonomy']])) {
					$tmp_tax = get_taxonomy($result['taxonomy']);
					$taxonomies_labels[$result['taxonomy']] = $tmp_tax->label;
				}

				$output_key = strtr($output_format, array(
					'%name%' => $result['text'],
					'%taxonomy%' => $result['taxonomy'],
					'%slug%' => $result['id'],
				));
				$out[] = array(
					'id' => $output_key,
					'text' => $result['text'] . ' ( ' . $taxonomies_labels[$result['taxonomy']] . ' )',
				);
			}
			wp_send_json_success(array('data' => $out));
		}

		/**
		 * Enable the spreadsheet editor on some post types
		 */
		function save_post_types_setting() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			$post_types = $data['post_types'];
			$nonce = $data['nonce'];
			$append = $data['append'];

			if (!empty($post_types) && wp_verify_nonce($nonce, 'bep-nonce') && current_user_can('manage_options')) {
				$settings = get_option(VGSE()->options_key, array());

				if ($append === 'yes') {
					$new_post_types = array_unique(array_merge($settings['be_post_types'], $post_types));
				} else {
					$new_post_types = $post_types;
				}
				$settings['be_post_types'] = $new_post_types;


				update_option(VGSE()->options_key, $settings);

				do_action('vg_sheet_editor/quick_setup/post_types_saved/after', $new_post_types);

				wp_send_json_success();
			}
			wp_send_json_error();
		}

		function save_gutenberg_content() {
			$_REQUEST['content'] = $_REQUEST['data'];
			$_REQUEST['post_id'] = $_REQUEST['postId'];
			$_REQUEST['post_type'] = $_REQUEST['postType'];
			$_REQUEST['type'] = 'post_data';
			$_REQUEST['key'] = 'post_content';
			$this->save_single_post_data();
		}

		/**
		 * Disable quick setup screen. It will show "quick usage screen" instead.
		 */
		function disable_quick_setup() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			$nonce = $data['nonce'];

			if (!wp_verify_nonce($nonce, 'bep-nonce')) {
				wp_send_json_error();
			}
			update_option('vgse_disable_quick_setup', true);

			wp_send_json_success();
		}

		function dismiss_review_tip() {
			$data = VGSE()->helpers->clean_data($_REQUEST);

			$nonce = $data['nonce'];

			if (!wp_verify_nonce($nonce, 'bep-nonce')) {
				wp_send_json_error();
			}
			update_option('vgse_dismiss_review_tip', 1);

			wp_send_json_success();
		}

		function notice_dismiss() {
			if (!current_user_can('manage_options')) {
				wp_send_json_error();
			}
			$key = sanitize_text_field($_REQUEST['key']);
			// Only allow to dismiss notices with keys starting with wpse_hide_
			if (strpos($key, 'wpse_hide_') !== 0) {
				wp_send_json_error();
			}
			update_option($key, 1);
			wp_send_json_success();
		}

		function init() {

// Ajax actions
			add_action('wp_ajax_vgse_delete_row_ids', array($this, 'delete_row_ids'));
			add_action('wp_ajax_vgse_dismiss_review_tip', array($this, 'dismiss_review_tip'));
			add_action('wp_ajax_vgse_notice_dismiss', array($this, 'notice_dismiss'));
			add_action('wp_ajax_vgse_get_taxonomy_terms', array($this, 'get_taxonomy_terms'));
			add_action('wp_ajax_vgse_load_data', array($this, 'load_rows'));
			add_action('wp_ajax_vgse_save_gutenberg_content', array($this, 'save_gutenberg_content'));
			add_action('wp_ajax_vgse_save_data', array($this, 'save_rows'));
			add_action('wp_ajax_vgse_find_post_by_name', array($this, 'find_post_by_name'));
			add_action('wp_ajax_vgse_save_individual_post', array($this, 'save_single_post_data'));
			add_action('wp_ajax_vgse_insert_individual_post', array($this, 'insert_individual_post'));
			add_action('wp_ajax_vgse_get_wp_post_single_data', array($this, 'get_wp_post_single_data'));
			add_action('wp_ajax_vgse_search_taxonomy_terms', array($this, 'search_taxonomy_terms'));
			add_action('wp_ajax_vgse_find_users_by_keyword', array($this, 'search_users'));
			add_action('wp_ajax_vgse_save_post_types_setting', array($this, 'save_post_types_setting'));
			add_action('wp_ajax_vgse_disable_quick_setup', array($this, 'disable_quick_setup'));
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @return  Foo A single instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Ajax::$instance) {
				WP_Sheet_Editor_Ajax::$instance = new WP_Sheet_Editor_Ajax();
				WP_Sheet_Editor_Ajax::$instance->init();
			}
			return WP_Sheet_Editor_Ajax::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WP_Sheet_Editor_Ajax_Obj')) {

	function WP_Sheet_Editor_Ajax_Obj() {
		return WP_Sheet_Editor_Ajax::get_instance();
	}

}