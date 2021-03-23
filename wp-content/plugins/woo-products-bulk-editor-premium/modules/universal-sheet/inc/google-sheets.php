<?php
// Beta testing for now
if (!isset($_GET['jkljasdoi87asdf'])) {
	return;
}
// Exit If sheet editor's rest api is not available
if (!class_exists('WPSE_REST_API')) {
	return;
}
if (!class_exists('WPSE_Google_Sheets')) {

	class WPSE_Google_Sheets {

		static private $instance = false;
		var $chrome_extension_url = 'http://google.com';

		private function __construct() {
			
		}

		function init() {

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
			add_action('admin_footer', array($this, 'render_quick_button_on_posts_lists'));
		}

		function render_quick_button_on_posts_lists() {
			if (strpos($_SERVER['REQUEST_URI'], '/edit.php') === false) {
				return;
			}
			vgse_universal_sheet()->enqueue_assets();
			?>
			<style>
				.wpsegs-quick-access {
					padding: 10px;
					border: 1px solid #bbb;
					margin-bottom: 20px;
					display: none;
				}
				.wpsegs-quick-access input {
					width: 100%;
					display: inline-block;
					background-color: white;
					max-width: 400px;
					color: grey;
					font-size: 13px;
				}
			</style>
			<script>
				jQuery(window).load(function () {
					jQuery('.page-title-action').last().after('<a href="#" class="page-title-action wpse-quick-access-link"><?php echo esc_html(__('Edit in Google Sheets', VGSE()->textname)); ?></a><div class="wpsegs-quick-access"><p><?php echo __('1. <a href="http://sheets.new" target="_blank">Open Google Sheets</a>.<br>2. Click on "wp sheet editor" in the menu<br>3. Enter this link in the "quick access" option in the Google Sheet sidebar.', VGSE()->textname); ?></p><input readonly onFocus="this.select()" class="access-link-visible">				<small class="access-link-visible"><?php echo esc_html(__('Use this link privately for security reasons, this link expires after one usage.', VGSE()->textname)); ?></small></div>');
				});
			</script>
			<?php
		}

		function render_edit_google_sheets_modal($post_type) {
			$nonce = wp_create_nonce('bep-nonce');
			include dirname(__DIR__) . '/views/edit-google-sheets-modal.php';
		}

		function register_toolbar_items($editor) {
			$editor->args['toolbars']->register_item('edit_google_sheets', array(
				'type' => 'button', // html | switch | button
				'content' => __('Edit in Google Sheets', VGSE()->textname),
				'id' => 'edit_google_sheets',
				'toolbar_key' => 'primary',
				'extra_html_attributes' => 'data-remodal-target="edit-google-sheets-modal"',
				'footer_callback' => array($this, 'render_edit_google_sheets_modal')
					), $editor->args['provider']);
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @return  Foo A single instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_Google_Sheets::$instance) {
				WPSE_Google_Sheets::$instance = new WPSE_Google_Sheets();
				WPSE_Google_Sheets::$instance->init();
			}
			return WPSE_Google_Sheets::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_Google_Sheets_Obj')) {

	function WPSE_Google_Sheets_Obj() {
		return WPSE_Google_Sheets::get_instance();
	}

}

WPSE_Google_Sheets_Obj();
