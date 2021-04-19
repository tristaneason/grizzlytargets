<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 * @author     MyWorks Design <sales@myworks.design>
 */
class MW_QBO_Desktop {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MW_QBO_Desktop_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'myworks-quickbooks-desktop-sync';
		$this->version = '';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - MW_QBO_Desktop_Loader. Orchestrates the hooks of the plugin.
	 * - MW_QBO_Desktop_i18n. Defines internationalization functionality.
	 * - MW_QBO_Desktop_Admin. Defines all hooks for the admin area.
	 * - MW_QBO_Desktop_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mw-qbo-desktop-lib.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mw-qbo-desktop-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mw-qbo-desktop-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mw-qbo-desktop-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mw-qbo-desktop-public.php';

		$this->loader = new MW_QBO_Desktop_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the MW_QBO_Desktop_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new MW_QBO_Desktop_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new MW_QBO_Desktop_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_ajax_redirect_mw_deactivation_popup', $plugin_admin, 'redirect_mw_deactivation_popup_func' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_admin_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'mw_qbo_desktop_admin_init' );
		
		//$this->loader->add_action( 'user_register', $plugin_admin, 'hook_user_register' );
		
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_admin, 'hook_order_add' );
		//$this->loader->add_action( 'save_post_shop_order', $plugin_admin, 'hook_order_add' );
		
		if(is_admin()){
			$a_os_l = get_option('mw_wc_qbo_desk_specific_order_status');
			if(!empty($a_os_l)){
				$a_os_l = trim($a_os_l);
				if($a_os_l!=''){$a_os_l = explode(',',$a_os_l);}
				if(is_array($a_os_l) && count($a_os_l)){
					foreach($a_os_l as $os){
						$os = substr($os,3);
						if(!empty($os)){
							$os_action = 'woocommerce_order_status_'.$os;
							$this->loader->add_action( $os_action, $plugin_admin, 'hook_order_add' , 10,1 );
						}						
					}
				}
			}
			
			$this->loader->add_action( 'post_updated', $plugin_admin, 'hook_order_update' , 10,3 );
			
			$this->loader->add_action( 'post_updated', $plugin_admin, 'myworks_wc_qbo_desk_pu_product_stock_update' , 10,3 );
			$this->loader->add_action( 'post_updated', $plugin_admin, 'myworks_wc_qbo_desk_pu_variation_stock_update' , 10,3 );
		}
		
		//
		$this->loader->add_action( 'woocommerce_order_refunded', $plugin_admin, 'hook_refund_add' );
		
		$this->loader->add_action( 'woocommerce_payment_complete', $plugin_admin, 'hook_payment_add' );
		
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'hook_product_add', 999, 1 );
		
		//$this->loader->add_action( 'woocommerce_process_product_meta_variable', $plugin_admin, 'hook_variation_add', 999, 1 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'hook_variation_add', 999, 1 );
		
		//create_product_cat
		
		//
		//$this->loader->add_action( 'woocommerce_product_set_stock', $plugin_admin, 'hook_product_stock_update' );
		//$this->loader->add_action( 'woocommerce_variation_set_stock', $plugin_admin, 'hook_variation_stock_update' );
		
		/**/
		if(get_option('mw_wc_qbo_desk_enable_wc_subs_rnord_sync') == 'true'){
			$this->loader->add_action( 'woocommerce_subscription_renewal_payment_complete', $plugin_admin, 'myworks_wc_qbo_desk_comt_hook_wsrpc' , 10,2 );
		}
		
		$this->loader->add_action( 'woocommerce_delete_product_variation', $plugin_admin, 'delete_variation_mapping' );
		
		$this->loader->add_action( 'delete_post', $plugin_admin, 'delete_product_mapping' );
		$this->loader->add_action( 'wp_trash_post', $plugin_admin, 'delete_product_mapping' );
		
		//Ajax Actions
		//add_action( 'wp_ajax_mw_qbo_dts_generate_qwc_file', 'mw_qbo_dts_generate_qwc_file' );
		add_action( 'wp_ajax_myworks_wc_qbo_sync_check_license_desk', 'myworks_wc_qbo_sync_check_license_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_refresh_log_chart_desk', 'mw_wc_qbo_sync_refresh_log_chart_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_window_desk', 'mw_wc_qbo_sync_window_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_mappings_desk', 'mw_wc_qbo_sync_clear_all_mappings_desk' );
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_customers_desk', 'mw_wc_qbo_sync_automap_customers_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_automap_customers_desk_wf_qf', 'mw_wc_qbo_sync_automap_customers_desk_wf_qf' );
		
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_products_desk', 'mw_wc_qbo_sync_automap_products_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_automap_products_desk_wf_qf', 'mw_wc_qbo_sync_automap_products_desk_wf_qf' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_logs_desk', 'mw_wc_qbo_sync_clear_all_logs_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_log_errors_desk', 'mw_wc_qbo_sync_clear_all_log_errors_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_queue_desk', 'mw_wc_qbo_sync_clear_all_queue_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_queue_pending_desk', 'mw_wc_qbo_sync_clear_all_queue_pending_desk' );
		
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_variations_desk', 'mw_wc_qbo_sync_automap_variations_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_automap_variations_desk_wf_qf', 'mw_wc_qbo_sync_automap_variations_desk_wf_qf' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_trial_license_check_again_desk', 'mw_wc_qbo_sync_trial_license_check_again_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_del_license_local_key_desk', 'mw_wc_qbo_sync_del_license_local_key_desk' );
		
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_customers_by_name_desk', 'mw_wc_qbo_sync_automap_customers_by_name_desk' );
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_products_by_name_desk', 'mw_wc_qbo_sync_automap_products_by_name_desk' );
		//add_action( 'wp_ajax_mw_wc_qbo_sync_automap_variations_by_name_desk', 'mw_wc_qbo_sync_automap_variations_by_name_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_mappings_products_desk', 'mw_wc_qbo_sync_clear_all_mappings_products_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_mappings_customers_desk', 'mw_wc_qbo_sync_clear_all_mappings_customers_desk' );
		add_action( 'wp_ajax_mw_wc_qbo_sync_clear_all_mappings_variations_desk', 'mw_wc_qbo_sync_clear_all_mappings_variations_desk' );
		
		add_action( 'wp_ajax_mw_wc_qbo_sync_rg_all_inc_variation_names_desk', 'mw_wc_qbo_sync_rg_all_inc_variation_names_desk' );
		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		
		$plugin_public = new MW_QBO_Desktop_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_public, 'mw_qbo_dts_public_api_rewrite' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'mw_qbo_dts_public_api_query_vars' );
		$this->loader->add_action( 'parse_request', $plugin_public, 'mw_qbo_dts_public_api_request' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    MW_QBO_Desktop_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}