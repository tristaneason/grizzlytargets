<?php
/*
  Plugin Name: WP Sheet Editor - WooCommerce Products (Premium)
  Description: Edit WooCommerce products in spreadsheet.
  Version: 1.3.0
  Author:      WP Sheet Editor
  Author URI:  http://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=products
  Plugin URI: https://wpsheeteditor.com/go/woocommerce-addon?utm_source=wp-admin&utm_medium=plugins-list&utm_campaign=products
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain: vg_sheet_editor_wc_products
  Domain Path: /lang
  @fs_premium_only /modules/user-path/send-user-path.php, /modules/advanced-filters/, /modules/columns-renaming/, /modules/formulas/, /modules/custom-columns/, /modules/spreadsheet-setup/, /modules/woocommerce/, /modules/acf/, /modules/posts-templates/, /modules/universal-sheet/, /modules/yoast-seo/,  
 */

if (!defined('ABSPATH')) {
	exit;
}
if (function_exists('wpsewcp_freemius')) {
	wpsewcp_freemius()->set_basename(true, __FILE__);
}
require_once 'vendor/vg-plugin-sdk/index.php';
require_once 'vendor/freemius/start.php';
require_once 'inc/freemius-init.php';

if (wpsewcp_freemius()->can_use_premium_code__premium_only()) {
	if (!defined('VGSE_WC_PRODUCTS_IS_PREMIUM')) {
		define('VGSE_WC_PRODUCTS_IS_PREMIUM', true);
	}
}
if (!class_exists('WP_Sheet_Editor_WC_Products')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_WC_Products {

		static private $instance = false;
		var $plugin_url = null;
		var $plugin_dir = null;
		var $textname = 'vg_sheet_editor_wc_products';
		var $buy_link = null;
		var $version = '1.3.0';
		var $settings = null;
		var $args = null;
		var $vg_plugin_sdk = null;
		var $post_type = null;

		private function __construct() {
			
		}

		function init_plugin_sdk() {
			$this->args = array(
				'main_plugin_file' => __FILE__,
				'show_welcome_page' => true,
				'welcome_page_file' => $this->plugin_dir . '/views/welcome-page-content.php',
				'logo' => plugins_url('/assets/imgs/logo-248x102.png', __FILE__),
				'buy_link' => $this->buy_link,
				'plugin_name' => 'Bulk Edit Products',
				'plugin_prefix' => 'wpsewcp_',
				'show_whatsnew_page' => true,
				'whatsnew_pages_directory' => $this->plugin_dir . '/views/whats-new/',
				'plugin_version' => $this->version,
				'plugin_options' => $this->settings,
			);
			$this->vg_plugin_sdk = new VG_Freemium_Plugin_SDK($this->args);
		}

		function notify_wrong_core_version() {
			$plugin_data = get_plugin_data(__FILE__, false, false);
			?>
			<div class="notice notice-error">
				<p><?php _e('Please update the WP Sheet Editor plugin and all its extensions to the latest version. The features of the plugin "' . $plugin_data['Name'] . '" will be disabled to prevent errors and they will be enabled automatically after you install the updates.', vgse_wc_products()->textname); ?></p>
			</div>
			<?php
		}

		function init() {
			$this->post_type = apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product');
			require_once __DIR__ . '/modules/init.php';
			$this->modules_controller = new WP_Sheet_Editor_CORE_Modules_Init(__DIR__, wpsewcp_freemius());

			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;
			$this->buy_link = wpsewcp_freemius()->checkout_url();

			$this->init_plugin_sdk();

			// After core has initialized
			add_filter('vg_sheet_editor/woocommerce/teasers/allowed_columns', array($this, 'allow_columns'));
			add_action('vg_sheet_editor/initialized', array($this, 'after_core_init'));
			add_action('vg_sheet_editor/after_init', array($this, 'after_full_core_init'));

			add_action('admin_init', array($this, 'disable_free_plugins_when_premium_active'), 1);
			add_action('init', array($this, 'after_init'));
		}

		function after_init() {
			load_plugin_textdomain($this->textname, false, basename(dirname(__FILE__)) . '/lang/');
		}

		function allow_columns($columns) {
			$enable = array(
				'ID',
				'post_title',
				'_sku',
				'_regular_price',
				'_sale_price',
				'_manage_stock',
				'_stock_status',
				'_stock',
				'post_content',
				'_virtual'
			);

			return array_unique(array_merge($enable, $columns));
		}

		function disable_free_plugins_when_premium_active() {
			$free_plugins_path = array(
				'woo-bulk-edit-products/products.php',
				'woo-products-bulk-editor/products.php',
				'wp-sheet-editor-woocommerce-inventory/woocommerce-inventory.php'
			);
			if (is_plugin_active('woo-products-bulk-editor-premium/products.php')) {
				foreach ($free_plugins_path as $relative_path) {
					$path = wp_normalize_path(WP_PLUGIN_DIR . '/' . $relative_path);
					if (is_plugin_active($relative_path)) {
						deactivate_plugins(plugin_basename($path));
					}
				}
			}
		}

		function after_core_init() {
			if (version_compare(VGSE()->version, '2.5.3') < 0) {
				add_action('admin_notices', array($this, 'notify_wrong_core_version'));
				return;
			}

			// Override core buy link with this plugin´s
			VGSE()->buy_link = $this->buy_link;

			// Enable admin pages in case "frontend sheets" addon disabled them
			add_filter('vg_sheet_editor/register_admin_pages', '__return_true', 11);
			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_toolbar_items'));
		}

		function register_toolbar_items($editor) {
			if ($editor->args['provider'] !== $this->post_type) {
				return;
			}
			if (!current_user_can('manage_options')) {
				return;
			}
			$editor->args['toolbars']->register_item('wpse_license', array(
				'type' => 'button',
				'content' => __('My license', vgse_wc_products()->textname),
				'url' => wpsewcp_freemius()->get_account_url(),
				'toolbar_key' => 'secondary',
				'extra_html_attributes' => ' target="_blank" ',
				'allow_in_frontend' => false,
					), $this->post_type);
		}

		function after_full_core_init() {
			// Set up spreadsheet.
			// Allow to bootstrap editor manually, later.
			if (!apply_filters('vg_sheet_editor/wc_products/bootstrap/manual_init', false)) {
				$this->sheets_bootstrap = new WP_Sheet_Editor_Bootstrap(array(
					'allowed_post_types' => array(),
					'only_allowed_spreadsheets' => false,
					'enabled_post_types' => array($this->post_type),
					'register_toolbars' => true,
					'register_columns' => true,
					'register_taxonomy_columns' => true,
					'register_admin_menus' => true,
					'register_spreadsheet_editor' => true,
					'current_provider' => VGSE()->helpers->get_provider_from_query_string()
				));
			}
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_WC_Products::$instance) {
				WP_Sheet_Editor_WC_Products::$instance = new WP_Sheet_Editor_WC_Products();
				WP_Sheet_Editor_WC_Products::$instance->init();
			}
			return WP_Sheet_Editor_WC_Products::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('vgse_wc_products')) {

	function vgse_wc_products() {
		return WP_Sheet_Editor_WC_Products::get_instance();
	}

	vgse_wc_products();
}	