<?php
if (!defined('VGSE_POST_TEMPLATES_DIR')) {
	define('VGSE_POST_TEMPLATES_DIR', __DIR__);
}

if (!class_exists('WP_Sheet_Editor_Post_Templates')) {

	/**
	 * Rename the columns of the spreadsheet editor to something more meaningful.
	 */
	class WP_Sheet_Editor_Post_Templates {

		static private $instance = false;
		var $plugin_url = null;
		var $plugin_dir = null;
		var $textname = 'wpsept';

		private function __construct() {
			
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Post_Templates::$instance) {
				WP_Sheet_Editor_Post_Templates::$instance = new WP_Sheet_Editor_Post_Templates();
				WP_Sheet_Editor_Post_Templates::$instance->init();
			}
			return WP_Sheet_Editor_Post_Templates::$instance;
		}

		function init() {

			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;

			// Priority 9 to execute before everything else.
			// The WC extension uses this filter with priority 10 to create products using the WC api
			add_filter('vg_sheet_editor/add_new_posts/create_new_posts', array($this, 'duplicate_post'), 9, 3);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar'));
			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'register_assets'));
			add_filter('vg_sheet_editor/duplicate/new_post_id', array($this, 'duplicate_woocommerce_product'), 10, 3);
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script('wpse-duplicate_js', plugins_url('/assets/js/init.js', __FILE__), array(), VGSE()->version, false);
		}

		/**
		 * Render modal html
		 * @param string $current_post_type
		 */
		function render_form($current_post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			?>


			<div class="remodal remodal-duplicate" data-remodal-id="modal-duplicate">

				<div class="modal-content">
					<h3><?php _e('Duplicate items in bulk', VGSE()->textname); ?></h3>

					<?php do_action('vg_sheet_editor/duplicate/above_form_fields', $current_post_type); ?>

					<ul class="unstyled-list">
						<li>
							<label><?php _e('Duplicate this item:', VGSE()->textname); ?></label>									
							<select name="duplicate_this" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo $current_post_type; ?>" data-nonce="<?php echo $nonce; ?>" data-placeholder="<?php _e('Enter item name...', VGSE()->textname); ?>" class="select2" required>
								<option></option>
							</select>
						</li>
						<li>
							<label><?php _e('How many copies do you want?', VGSE()->textname); ?></label>
							<input type="number" name="number_of_copies" value="1">
							<p class="posts-templates-small-batches-message hidden"><?php _e('Note. More than 75 copies at once might overload the server. Do it in small batches.<br>You can continue if you think your server can handle it.', VGSE()->textname); ?></p>
						</li>
						<?php
						do_action('vg_sheet_editor/duplicate/after_fields', $current_post_type);
						?>
					</ul>

					<button class="remodal-confirm wpse-duplicate-trigger"><?php _e('Execute', VGSE()->textname); ?></button>
					<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel', VGSE()->textname); ?></button>
				</div>
				<br>
			</div>
			<?php
		}

		function register_toolbar($editor) {
			if ($editor->provider->key === 'user') {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			foreach ($post_types as $post_type) {
				$editor->args['toolbars']->register_item('duplicate', array(
					'type' => 'button',
					'help_tooltip' => __('Duplicate items in bulk.', VGSE()->textname),
					'content' => __('Duplicate', VGSE()->textname),
					'icon' => 'fa fa-copy',
					'extra_html_attributes' => 'data-remodal-target="modal-duplicate"',
					'footer_callback' => array($this, 'render_form')
						), $post_type);
			}
		}

		function _duplicate_post($post_id = null, $custom_post_data = array()) {

			if (empty($post_id)) {
				return new WP_Error('wpse', 'Empty $post_id');
			}
			$post_data = get_post($post_id, ARRAY_A);

			$post_data = wp_parse_args($custom_post_data, $post_data);

			$post_meta = get_post_custom($post_id);
			$taxonomies = get_object_taxonomies($post_data['post_type']); // returns array of taxonomy names for post type, ex array("category", "post_tag");
			$taxonomies_terms = array();
			foreach ($taxonomies as $taxonomy) {
				$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs', 'update_term_meta_cache' => false));
				$taxonomies_terms[$taxonomy] = $post_terms;
			}

			// We wont copy the ID and dates
			unset($post_data['ID']);
			unset($post_data['post_date']);
			unset($post_data['post_modified']);
			unset($post_data['post_date_gmt']);
			unset($post_data['post_modified_gmt']);


			$new_post_id = wp_insert_post(apply_filters('vg_sheet_editor/duplicate/new_post_data', $post_data));
			// Copy post metadata
			foreach ($post_meta as $key => $values) {

				if (!empty($custom_post_data['meta_input']) && isset($custom_post_data['meta_input'][$key])) {
					continue;
				}
				foreach ($values as $value) {
					add_post_meta($new_post_id, $key, maybe_unserialize($value));
				}
			}
			foreach ($taxonomies_terms as $taxonomy => $post_terms) {
				wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
			}

			return $new_post_id;
		}

		function duplicate_post($post_ids, $post_type, $rows) {

			if (empty($_REQUEST['extra_data']) || strpos($_REQUEST['extra_data'], 'duplicate:') === false) {
				return $post_ids;
			}
			$template_id = VGSE()->helpers->_get_post_id_from_search(str_replace('duplicate:', '', VGSE()->helpers->clean_data($_REQUEST['extra_data'])));

			if (empty($template_id) || get_post_type($template_id) !== $post_type || !VGSE()->helpers->user_can_edit_post_type($post_type)) {
				return new WP_Error('wpse', __('Template item not found or not allowed to be duplicated.', VGSE()->textname));
			}

			for ($i = 0; $i < $rows; $i++) {
				$new_post_id = apply_filters('vg_sheet_editor/duplicate/new_post_id', null, $template_id, $post_type);

				if (!is_int($new_post_id)) {
					$new_post_id = $this->_duplicate_post($template_id, array(
						'post_status' => 'draft',
						'post_title' => get_the_title($template_id) . ' (Copy)'
					));
				}

				if (is_int($new_post_id)) {
					$post_ids[] = apply_filters('vg_sheet_editor/duplicate/final_post_id', $new_post_id, $template_id, $post_type);
				}
			}

			return $post_ids;
		}

		function duplicate_woocommerce_product($new_post_id, $template_id, $post_type) {
			if ($post_type === apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product') && function_exists('WC') && empty($new_post_id)) {

				if (!class_exists('WC_Admin_Duplicate_Product')) {
					include_once( WC_ABSPATH . 'includes/admin/class-wc-admin-duplicate-product.php' );
				}
				$duplicate = new WC_Admin_Duplicate_Product();
				$template_product = wc_get_product($template_id);

				if (empty($template_product)) {
					return $new_post_id;
				}

				$new_post = $duplicate->product_duplicate($template_product);
				$new_post_id = $new_post->get_id();
			}
			return $new_post_id;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

	add_action('vg_sheet_editor/initialized', 'vgse_post_templates_init');

	function vgse_post_templates_init() {
		WP_Sheet_Editor_Post_Templates::get_instance();
	}

}