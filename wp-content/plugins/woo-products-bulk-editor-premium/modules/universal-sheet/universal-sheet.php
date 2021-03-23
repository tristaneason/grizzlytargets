<?php
if (!class_exists('WP_Sheet_Editor_Universal_Sheet')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Universal_Sheet {

		static private $instance = false;
		var $plugin_url = null;
		var $plugin_dir = null;
		var $buy_link = null;
		var $settings = null;
		var $args = null;
		var $vg_plugin_sdk = null;

		private function __construct() {
			
		}

		function init() {
			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;

			require __DIR__ . '/inc/csv-api.php';
			require __DIR__ . '/inc/rest-api.php';
			add_action('vg_sheet_editor/initialized', array($this, 'late_init'));
		}

		function late_init() {
			require __DIR__ . '/inc/google-sheets.php';

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));

			// Enqueue metabox css and js
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'enqueue_assets'));
		}

		/**
		 * Render modal for exporting csv
		 * @param str $post_type
		 * @return null
		 */
		function render_export_csv_modal($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include __DIR__ . '/views/export-modal.php';
		}

		/**
		 * Render modal for importing csv
		 * @param str $post_type
		 * @return null
		 */
		function render_import_csv_modal($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include __DIR__ . '/views/import-modal.php';
		}

		function get_export_options($post_type) {
			$columns = VGSE()->helpers->get_post_type_columns_options($post_type, array(
				'conditions' => array(
					'allow_plain_text' => true
				),
					), false, false, true);
			return apply_filters('vg_sheet_editor/export/columns', $columns, $post_type);
		}

		function render_wp_fields_export_options($post_type) {
			$columns = $this->get_export_options($post_type);
			do_action('vg_sheet_editor/export/before_available_columns_options', $post_type);
			foreach ($columns as $key => $column) {
				echo '<option value="' . esc_attr($key) . '">' . esc_html($column['title']) . '</option>';
			}
			do_action('vg_sheet_editor/export/after_available_columns_options', $post_type);
		}

		function get_import_options($post_type) {
			// we don't use allow_to_save => true here because we need 
			// readonly columns like the ID to find items to update		
			$columns = VGSE()->helpers->get_post_type_columns_options($post_type, array(
				'conditions' => array(
					'allow_plain_text' => true,
				),
					), false, false, true);
			return apply_filters('vg_sheet_editor/import/columns', $columns, $post_type);
		}

		function render_wp_fields_import_options($post_type) {
			$columns = $this->get_import_options($post_type);
			?>
			<option value=""><?php _e('Ignore this column', VGSE()->textname); ?></option>
			<?php
			do_action('vg_sheet_editor/import/before_available_columns_options', $post_type);
			foreach ($columns as $key => $column) {
				echo '<option value="' . esc_attr($key) . '">' . esc_html($column['title']) . '</option>';
			}
			do_action('vg_sheet_editor/import/after_available_columns_options', $post_type);
		}

		function register_toolbar_items($editor) {
			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('export_csv', array(
					'type' => 'button', // html | switch | button
					'content' => __('Export', VGSE()->textname),
					'id' => 'export_csv',
					'allow_in_frontend' => true,
					'toolbar_key' => 'secondary',
					'extra_html_attributes' => 'data-remodal-target="export-csv-modal"',
					'footer_callback' => array($this, 'render_export_csv_modal')
						), $post_type);

				if (current_user_can('manage_options')) {
					$saved_exports = WPSE_CSV_API_Obj()->get_saved_exports($post_type);
					foreach ($saved_exports as $index => $saved_export) {
						$editor->args['toolbars']->register_item('save_export' . $index, array(
							'type' => 'button',
							'content' => esc_html($saved_export['name']),
							'toolbar_key' => 'secondary',
							'allow_in_frontend' => false,
							'parent' => 'export_csv',
							'extra_html_attributes' => 'data-start-saved-export="' . esc_attr(json_encode($saved_export)) . '"',
								), $post_type);
					}
				}


				$editor->args['toolbars']->register_item('share_export', array(
					'type' => 'button',
					'content' => __('Download CSV file', VGSE()->textname),
					'extra_html_attributes' => 'data-remodal-target="export-csv-modal"',
					'toolbar_key' => 'primary',
					'allow_in_frontend' => false,
					'parent' => 'share'
						), $post_type);
				$editor->args['toolbars']->register_item('import_csv', array(
					'type' => 'button', // html | switch | button
					'content' => __('Import', VGSE()->textname),
					'id' => 'import_csv',
					'allow_in_frontend' => true,
					'toolbar_key' => 'secondary',
					'extra_html_attributes' => 'data-remodal-target="import-csv-modal"',
					'footer_callback' => array($this, 'render_import_csv_modal')
						), $post_type);
			}
		}

		/**
		 * Enqueue metabox assets
		 * @global obj $post
		 * @param str $hook
		 */
		function enqueue_assets($hook = null) {
			wp_enqueue_script('vgse-universal-sheet-init', $this->plugin_url . 'assets/js/init.js', array('jquery'), VGSE()->version, false);
			wp_localize_script('vgse-universal-sheet-init', 'vgse_universal_sheet_data', array(
				'rest_nonce' => wp_create_nonce('wp_rest'),
				'rest_base_url' => rest_url(),
				'post_type' => VGSE()->helpers->get_provider_from_query_string(),
			));
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Universal_Sheet::$instance) {
				WP_Sheet_Editor_Universal_Sheet::$instance = new WP_Sheet_Editor_Universal_Sheet();
				WP_Sheet_Editor_Universal_Sheet::$instance->init();
			}
			return WP_Sheet_Editor_Universal_Sheet::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

add_action('plugins_loaded', 'vgse_universal_sheet', 99);

if (!function_exists('vgse_universal_sheet')) {

	function vgse_universal_sheet() {
		return WP_Sheet_Editor_Universal_Sheet::get_instance();
	}

}
