<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/public
 * @author     MyWorks Design <sales@myworks.design>
 */
class MW_QBO_Desktop_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in MW_QBO_Desktop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The MW_QBO_Desktop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mw-qbo-desktop-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in MW_QBO_Desktop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The MW_QBO_Desktop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mw-qbo-desktop-public.js', array( 'jquery' ), $this->version, false );

	}
	
	public function mw_qbo_dts_public_api_rewrite(){
		/*
		if(!session_id()) {
			session_start();
		}
		*/
		
		add_rewrite_rule( 'mw-qbo-desktop-public-qwc-server.php$', 'index.php?mw_qbo_desktop_qwc_server=1', 'top' );
		add_rewrite_rule( 'mw-qbo-desktop-public-qwc-support.php$', 'index.php?mw_qbo_desktop_qwc_support=1', 'top' );
		
		add_rewrite_rule( 'mw-qbo-desktop-public-sync-window.php$', 'index.php?mw_qbo_desktop_public_sync_window=1', 'top' );
		
		add_rewrite_rule( 'mw-qbo-desktop-public-json-item-list.php$', 'index.php?mw_qbo_desktop_public_get_json_item_list=1', 'top' );
		
	}
	
	public function  mw_qbo_dts_public_api_query_vars( $query_vars ){
		$query_vars[] = 'mw_qbo_desktop_qwc_server';
		$query_vars[] = 'mw_qbo_desktop_qwc_support';
		
		$query_vars[] = 'mw_qbo_desktop_public_sync_window';
		
		$query_vars[] = 'mw_qbo_desktop_public_get_json_item_list';
		return $query_vars;
	}
	
	public function mw_qbo_dts_public_api_request(&$wp) {
		if ( array_key_exists( 'mw_qbo_desktop_qwc_server', $wp->query_vars ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-public-qwc-server.php';
			exit();
		}
		
		if ( array_key_exists( 'mw_qbo_desktop_qwc_support', $wp->query_vars ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-public-qwc-support.php';
			exit();
		}
		
		if ( array_key_exists( 'mw_qbo_desktop_public_sync_window', $wp->query_vars ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-public-sync-window.php';
			exit();
		}
		
		if ( array_key_exists( 'mw_qbo_desktop_public_get_json_item_list', $wp->query_vars ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-public-json-item-list.php';
			exit();
		}
	}
}
