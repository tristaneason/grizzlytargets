<?php
if (!class_exists('WPSE_Post_Type_Setup_Wizard')) {

	class WPSE_Post_Type_Setup_Wizard {

		static private $instance = false;
		var $page_slug = 'vg_sheet_editor_post_type_setup';
		var $custom_post_types_key = 'vg_sheet_editor_custom_post_types';

		private function __construct() {
			
		}

		function init() {

			// Exit if "columns visibility " or "custom columns" extensions are disabled
			if (!class_exists('WP_Sheet_Editor_Columns_Visibility') || !class_exists('WP_Sheet_Editor_Custom_Columns')) {
				return;
			}

			// We register post types late to give other plugins the chance to register their own post types
			add_action('init', array($this, 'register_post_types'), 99);
			add_action('vg_sheet_editor/initialized', array($this, 'late_init'));
		}

		function late_init() {
			add_action('admin_menu', array($this, 'register_menu'), 99);
			add_action('wp_ajax_vgse_register_post_type', array($this, 'save_post_type'));
			add_action('wp_ajax_vgse_delete_post_type', array($this, 'delete_post_type'));

			add_action('wp_ajax_vgse_post_type_setup_columns_visibility', array($this, 'get_columns_visibility_html'));
			add_action('wp_ajax_vgse_post_type_setup_save_column', array($this, 'save_column'));
			add_filter('vg_sheet_editor/welcome_url', array($this, 'filter_welcome_url'));
			add_action('vg_sheet_editor/quick_setup_page/usage_screen/after_content', array($this, 'spreadsheet_already_setup_screen'));
			add_action('vg_sheet_editor/quick_setup/post_types_saved/after', array($this, 'after_post_type_saved'));
		}

		function after_post_type_saved($post_types) {
			if (!empty($post_types)) {
				update_option('vgse_post_type_setup_done', 1);
			}
		}

		function spreadsheet_already_setup_screen($post_type) {
			$spreadsheet_setup_done = get_option('vgse_post_type_setup_done');
			if (empty($spreadsheet_setup_done)) {
				return;
			}
			?>
			<style>
				#vgse-wrapper .progressbar,
				#vgse-wrapper .settings-button,
				#vgse-wrapper .setup-screen,
				#vgse-wrapper .modules,
				#vgse-wrapper .step-back {
					display: none !important;
				}
				#vgse-wrapper .usage-screen {
					display: block !important;
				}
			</style>
			<a class="button setup-button" href="<?php echo admin_url('admin.php?page=' . $this->page_slug); ?>"><i class="fa fa-cog"></i> <?php _e('Enable new spreadsheet', VGSE()->textname); ?></a>
			<?php
		}

		function filter_welcome_url($url) {
			$spreadsheet_setup_done = get_option('vgse_post_type_setup_done');
			if (!empty($spreadsheet_setup_done)) {
				return;
			}
			$url = admin_url('admin.php?page=' . $this->page_slug);
			return $url;
		}

		function save_column() {
			if (empty($_REQUEST['nonce']) || empty($_REQUEST['current_post_type']) || empty($_REQUEST['label']) || !wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !current_user_can('manage_option')) {
				wp_send_json_error();
			}

			if (empty($_REQUEST['key'])) {
				$_REQUEST['key'] = sanitize_key($_REQUEST['label']);
			}

			$columns = WP_Sheet_Editor_Custom_Columns::get_instance();

			$columns->add_columns(array(array(
					'name' => sanitize_text_field($_REQUEST['label']),
					'key' => sanitize_key($_REQUEST['key']),
					'post_types' => sanitize_text_field($_REQUEST['current_post_type']),
				)), array(
				'append' => true,
			));

			wp_send_json_success(array(
				'key' => sanitize_key($_REQUEST['key']),
				'label' => sanitize_text_field($_REQUEST['label']),
			));
		}

		function get_columns_visibility_html() {
			if (empty($_REQUEST['nonce']) || empty($_REQUEST['post_type']) || !wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error();
			}

			$post_type = sanitize_text_field($_REQUEST['post_type']);
			$editor = VGSE()->helpers->get_provider_editor($post_type);
			$out = '';
			// Columns visibility section
			if (class_exists('WP_Sheet_Editor_Columns_Visibility') && is_object($editor)) {
				$columns_visibility_module = WP_Sheet_Editor_Columns_Visibility::get_instance();
				ob_start();
				$columns_visibility_module->render_settings_modal($post_type, false, null, admin_url('admin.php?page=vg_sheet_editor_post_type_setup'));
				// Render the editor settings because some JS requires the texts and other info
				$editor_settings = $editor->get_editor_settings($post_type);
				?>
				<script>
					var vgse_editor_settings = <?php echo json_encode($editor_settings); ?>;

					// Reinitialize columns visibility
					window.vgseColumnsVisibilityAlreadyInit = false;
					vgseColumnsVisibilityInit();
				</script>
				<?php
				$columns_visibility_html = ob_get_clean();

				$out = str_replace(array(
					'data-remodal-id="modal-columns-visibility" data-remodal-options="closeOnOutsideClick: false, hashTracking: false" class="remodal remodal',
					'button primary hidden form-submit-inside',
					'Apply settings',
					'<form',
						), array(
					'class="',
					'button primary form-submit-inside button-primary',
					'Save',
					'<form data-callback="vgsePostTypeSetupColumnsVisibilitySaved" ',
						), $columns_visibility_html);
			}

			wp_send_json_success(array(
				'html' => $out,
			));
		}

		function delete_post_type() {

			if (!class_exists('WP_Sheet_Editor_CPTs')) {
				wp_send_json_error();
			}
			if (empty($_REQUEST['nonce']) || empty($_REQUEST['post_type']) || !wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error();
			}

			$existing_post_types = get_option($this->custom_post_types_key, array());
			$existing_post_types_slugs = array_map('sanitize_title', $existing_post_types);
			$new_post_type = sanitize_title($_REQUEST['post_type']);
			$post_type_index = array_search($new_post_type, $existing_post_types_slugs);

			if ($post_type_index !== false) {
				unset($existing_post_types[$post_type_index]);
			}

			update_option($this->custom_post_types_key, $existing_post_types);

			$posts = new WP_Query(array(
				'post_type' => $new_post_type,
				'post_status' => 'any',
				'fields' => 'ids',
				'posts_per_page' => -1,
			));
			foreach ($posts->posts as $post_id) {
				wp_delete_post($post_id, true);
			}

			wp_send_json_success(array(
				'message' => __('Post type deleted', VGSE()->textname),
			));
		}

		function save_post_type() {

			if (!class_exists('WP_Sheet_Editor_CPTs')) {
				wp_send_json_error();
			}
			if (empty($_REQUEST['nonce']) || empty($_REQUEST['post_type']) || !wp_verify_nonce($_REQUEST['nonce'], 'bep-nonce') || !current_user_can('manage_options')) {
				wp_send_json_error();
			}

			if ($this->is_protected_key($_REQUEST['post_type'])) {
				wp_send_json_error();
			}

			$existing_post_types = get_option($this->custom_post_types_key, array());
			$post_types = $existing_post_types;
			if (empty($post_types) || !is_array($post_types)) {
				$post_types = array();
			}
			$post_types[] = sanitize_title($_REQUEST['post_type']);

			$post_types = array_unique($post_types);

			$registered_post_type = current(VGSE()->helpers->get_all_post_types(array(
						'name' => sanitize_text_field($_REQUEST['post_type']),
						'label' => sanitize_text_field($_REQUEST['post_type']),
			)));

			if (!empty($registered_post_type)) {
				$out = array(
					'slug' => $registered_post_type->name,
					'label' => $registered_post_type->label
				);
			} else {
				if ($existing_post_types !== $post_types) {
					update_option($this->custom_post_types_key, $post_types);
				}
				$out = array(
					'slug' => sanitize_title($_REQUEST['post_type']),
					'label' => sanitize_text_field($_REQUEST['post_type']),
				);
			}

			wp_send_json_success($out);
		}

		function is_protected_key($post_type_key) {

			$protected = array(
				"post",
				"page",
				"attachment",
				"revision",
				"nav_menu_item",
				"custom_css",
				"customize_changeset",
				"oembed_cache",
				"user_request",
				"wp_block",
				"action",
				"author",
				"order",
				"theme",
			);

			return in_array($post_type_key, $protected);
		}

		function register_post_types() {

			if (!class_exists('WP_Sheet_Editor_CPTs')) {
				return;
			}
			$post_types = get_option($this->custom_post_types_key, array());

			if (empty($post_types) || !is_array($post_types)) {
				return;
			}

			if (isset($_GET['wpse_delete_all_post_types']) && current_user_can('manage_options')) {
				update_option($this->custom_post_types_key, null);
				return;
			}

			foreach ($post_types as $post_type) {
				$post_type_key = sanitize_title($post_type);
				if (!$this->is_protected_key($post_type_key) && !post_type_exists($post_type_key)) {
					$args = array(
						'public' => true,
						'label' => $post_type,
					);
					register_post_type($post_type_key, $args);
				}
			}
		}

		function register_menu() {
			add_submenu_page('vg_sheet_editor_setup', __('Setup spreadsheet', VGSE()->textname), __('Setup spreadsheet', VGSE()->textname), 'manage_options', $this->page_slug, array($this, 'render_post_type_setup'));
		}

		/**
		 * Render post type setup page
		 */
		function render_post_type_setup() {
			if (!current_user_can('manage_options')) {
				wp_die(__('You dont have enough permissions to view this page.', VGSE()->textname));
			}

			$custom_post_types_raw = get_option($this->custom_post_types_key);
			if (empty($custom_post_types_raw) || !is_array($custom_post_types_raw)) {
				$custom_post_types_raw = array();
			}
			$custom_post_types = implode(',', array_filter(array_map('sanitize_title', $custom_post_types_raw)));
			require __DIR__ . '/views/page.php';
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @return  Foo A single instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_Post_Type_Setup_Wizard::$instance) {
				WPSE_Post_Type_Setup_Wizard::$instance = new WPSE_Post_Type_Setup_Wizard();
				WPSE_Post_Type_Setup_Wizard::$instance->init();
			}
			return WPSE_Post_Type_Setup_Wizard::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

	if (!function_exists('WPSE_Post_Type_Setup_Wizard_Obj')) {

		function WPSE_Post_Type_Setup_Wizard_Obj() {
			return WPSE_Post_Type_Setup_Wizard::get_instance();
		}

	}
	WPSE_Post_Type_Setup_Wizard_Obj();
}