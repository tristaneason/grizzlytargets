<?php
/*
	Plugin Name: WooCommerce FedEx Shipping Plugin with Print Label
	Plugin URI: https://www.pluginhive.com/product/woocommerce-fedex-shipping-plugin-with-print-label/
	Description: This plugin helps you completely automate FedEx shipping. It displays live shipping rates on WooCommerce cart page, helps you pay postage & print labels from within WooCommerce, and track your shipments.
	Version: 4.7.5
	Author: PluginHive
	Author URI: http://pluginhive.com/about/
	WC requires at least: 3.0.0
	WC tested up to: 5.1.0
	Text Domain : wf-shipping-fedex
*/

if (!defined('WF_Fedex_ID')){
	define("WF_Fedex_ID", "wf_fedex_woocommerce_shipping");
}

if( !defined('WF_FEDEX_ADV_DEBUG_MODE') ){
	define("WF_FEDEX_ADV_DEBUG_MODE", "on"); // Turn 'off' to disable advanced debug mode.
}

// Define PH_FEDEX_PLUGIN_VERSION
if ( !defined( 'PH_FEDEX_PLUGIN_VERSION' ) )
{
	define( 'PH_FEDEX_PLUGIN_VERSION', '4.7.5' );
}

// Include API Manager
if ( !class_exists( 'PH_FedEx_API_Manager' ) ) {

	include_once( 'ph-api-manager/ph_api_manager_fedex.php' );
}

if ( class_exists( 'PH_FedEx_API_Manager' ) ) {

	$ph_fedex_api_obj 	= new PH_FedEx_API_Manager( __FILE__, '', PH_FEDEX_PLUGIN_VERSION, 'plugin', 'https://www.pluginhive.com/', 'FedEx' );
}

/**
 * Plugin activation check
 */
function wf_fedex_pre_activation_check(){
	//check if basic version is there
	if ( is_plugin_active('fedex-woocommerce-shipping-method/fedex-woocommerce-shipping.php') ){
        deactivate_plugins( basename( __FILE__ ) );
		wp_die( __("Oops! You tried installing the premium version without deactivating and deleting the basic version. Kindly deactivate and delete FedEx(Basic) Woocommerce Extension and then try again", "wf-shipping-fedex" ), "", array('back_link' => 1 ));
	}
	set_transient('wf_fedex_welcome_screen_activation_redirect', true, 30);
}

register_activation_hook( __FILE__, 'wf_fedex_pre_activation_check' );

/**
 * Common Class.
 */
if( ! class_exists('Ph_Fedex_Woocommerce_Shipping_Common') ) {
	require_once 'class-ph-fedex-woocommerce-shipping-common.php';
}

/**
 * Check if WooCommerce is active
 */
$xa_active_plugins = Ph_Fedex_Woocommerce_Shipping_Common::get_active_plugins();
if ( in_array( 'woocommerce/woocommerce.php', $xa_active_plugins ) ) {
	
	if (!function_exists('wf_get_settings_url')){
		function wf_get_settings_url(){
			return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
		}
	}
	
	if (!function_exists('wf_plugin_override')){
		add_action( 'plugins_loaded', 'wf_plugin_override' );
		function wf_plugin_override() {
			if (!function_exists('WC')){
				function WC(){
					return $GLOBALS['woocommerce'];
				}
			}
		}
	}
	if (!function_exists('wf_get_shipping_countries')){
		function wf_get_shipping_countries(){
			$woocommerce = WC();
			$shipping_countries = method_exists($woocommerce->countries, 'get_shipping_countries')
					? $woocommerce->countries->get_shipping_countries()
					: $woocommerce->countries->countries;
			return $shipping_countries;
		}
	}

	include('includes/wf-automatic-label-generation.php');
	if(!class_exists('wf_fedEx_wooCommerce_shipping_setup')){
		class wf_fedEx_wooCommerce_shipping_setup {
			
			public function __construct() {

				$this->active_plugins = Ph_Fedex_Woocommerce_Shipping_Common::get_active_plugins();		// List of active plugins
				$fedex_settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', array() );
				// Define Fedex Settings
				global $xa_fedex_settings;
				$xa_fedex_settings = $fedex_settings;

				add_action( 'init', array( $this, 'init' ) );
				add_action('admin_init', array($this,'wf_fedex_welcome'));
				add_action('admin_menu', array($this,'wf_fedex_welcome_screen'));
				add_action('admin_head', array($this,'wf_fedex_welcome_screen_remove_menus'));

				$this->wf_init();
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
				add_action( 'woocommerce_shipping_init', array( $this, 'wf_fedEx_wooCommerce_shipping_init' ) );
				add_filter( 'woocommerce_shipping_methods', array( $this, 'wf_fedEx_wooCommerce_shipping_methods' ) );		
				add_filter( 'admin_enqueue_scripts', array( $this, 'wf_fedex_scripts' ) );

				if ( isset( $fedex_settings['freight_enabled'] ) && 'yes' === $fedex_settings['freight_enabled'] ) {
					// Make the city field show in the calculator (for freight)
					add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );

					// Add freight class option for shipping classes (for freight)
					if ( is_admin() ) {
						include( 'includes/class-wf-fedex-freight-mapping.php' );
					}
				}
				// For FedEx Hold at Location
				if( isset($fedex_settings['hold_at_location']) && $fedex_settings['hold_at_location'] === 'yes' ) {
					if( ! class_exists('Ph_Fedex_Woocommerce_Location_Finder') ) {
						require_once 'includes/class-ph-fedex-woocommerce-location-finder.php';
						$fedex_hold_at=new Ph_Fedex_Woocommerce_Location_Finder($fedex_settings);
						$fedex_hold_at->init();

					}
					add_filter( 'wp_enqueue_scripts', array( $this, 'ph_fedex_checkout_scripts_for_hold_at_location' ) );
				}			
			}
			public function init() {

				if( ! class_exists('PH_FedEx_Help_and_Support') ) {
					include_once('includes/class-ph-fedex-help-and-support.php');
				}

				if ( ! class_exists( 'wf_order' ) ) {
			  		include_once 'includes/class-wf-legacy.php';
			  	}
			}
			public function wf_fedex_welcome()
            {
	          	if (!get_transient('wf_fedex_welcome_screen_activation_redirect')) {
	           		 return;
	        	}
	       	 	delete_transient('wf_fedex_welcome_screen_activation_redirect');
	        	wp_safe_redirect(add_query_arg(array('page' => 'Fedex-Welcome'), admin_url('index.php')));
            }
            public function wf_fedex_welcome_screen()
            {
            	add_dashboard_page('Welcome To Fedex', 'Welcome To Fedex', 'read', 'Fedex-Welcome', array($this,'wf_fedex_screen_content'));
            }
            public function wf_fedex_screen_content()
            {
            	include 'includes/wf_fedex_welcome.php';
            }
            public function wf_fedex_welcome_screen_remove_menus()
            {
            	 remove_submenu_page('index.php', 'Fedex-Welcome');
            }
			public function wf_init() {
				
				// To Support Third Party plugins
				$this->third_party_plugin_support();

				include_once ( 'includes/class-wf-admin-notice.php' );
				include_once ( 'includes/class-wf-fedex-woocommerce-shipping-admin.php' );
				include_once ( 'includes/class-wf-admin-options.php' );
				include_once ( 'includes/class-wf-tracking-admin.php' );
				include_once ( 'includes/class-wf-request.php' );
				include_once ( 'includes/class-xa-my-account-order-return.php' );
				if ( is_admin() ) {
					include_once ( 'includes/class-wf-fedex-pickup-admin.php' );				
					
					include_once ( 'includes/class-xa-fedex-image-upload.php' );

				}
				// Localisation
				load_plugin_textdomain( 'wf-shipping-fedex', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/' );
			}

			/**
			 * It will decide that which third party plugin support file has to be included depending on active plugins.
			 */
			public function third_party_plugin_support() {

				// Woocommerce Bundle Product Plugin support
				if( in_array( 'woocommerce-product-bundles/woocommerce-product-bundles.php', $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/wf-fedex-woocommerce-product-bundle-support.php';
				}

				// Include file to support for WooCommerce Measurement Price Calculator Plugins
				if ( in_array( 'woocommerce-measurement-price-calculator/woocommerce-measurement-price-calculator.php', $this->active_plugins ) ){
					require_once 'includes/third-party-plugin-support/wf-fedex-woocommerce-measurement-price-calculator.php';
				}

				// Support for Mixed-and-matched product plugin by woocommerce
				if( in_array( "woocommerce-mix-and-match-products/woocommerce-mix-and-match-products.php", $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/ph-fedex-woocommerce-mixed-and-match-product-support.php';
				}
				// For YITH multiple shipping address
				if( in_array( 'yith-multiple-shipping-addresses-for-woocommerce/init.php', $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/ph-fedex-yith-multiple-shipping-addresses-for-woocommerce.php';
				}
				// For woocommerce currency switcher
				if( in_array( 'woocommerce-multicurrency/woocommerce-multicurrency.php', $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/ph-fedex-woocommerce-currency-switcher.php';
				}
				// For WooCommerce Ship to Multiple Addresses
				if( in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-address.php', $this->active_plugins) || in_array( 'woocommerce-shipping-multiple-addresses/woocommerce-shipping-multiple-addresses.php', $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/ph-fedex-woocommerce-shipping-multiple-address.php';
				}
				//	WOOCS - WooCommerce Currency Switcher Plugin support
				if( in_array( 'woocommerce-currency-switcher/index.php', $this->active_plugins) ) {
					require_once 'includes/third-party-plugin-support/ph-fedex-woocs-currency-switcher.php';
				}
			}
			
			public function wf_fedex_scripts() {
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'ph-fedex-common-script', plugins_url( '/resources/js/wf_common.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'wf-fedex-script', plugins_url( '/resources/js/wf_fedex.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_style( 'ph-fedex-common-style', plugins_url( '/resources/css/wf_common_style.css', __FILE__ ));
				wp_enqueue_style( 'wf-fedex-style', plugins_url( '/resources/css/wf_fedex_style.css', __FILE__ ));
			}
			
			public function plugin_action_links( $links ) {
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=' . wf_get_settings_url() . '&tab=shipping&section=wf_fedex_woocommerce_shipping' ) . '">' . __( 'Settings', 'wf_fedEx_wooCommerce_shipping' ) . '</a>',
					'<a href="https://www.pluginhive.com/knowledge-base/category/woocommerce-fedex-shipping-plugin-with-print-label/" target="_blank">' . __('Documentation', 'wf_fedEx_wooCommerce_shipping') . '</a>',
                '<a href="https://www.pluginhive.com/support/" target="_blank">' . __('Support', 'wf_fedEx_wooCommerce_shipping') . '</a>',
                '<a href="'.admin_url('index.php?page=Fedex-Welcome').'" style="color:green;" target="_blank">' . __('Get Started', 'wf_fedEx_wooCommerce_shipping') . '</a>'
				);
				return array_merge( $plugin_links, $links );
			}			
			
			public function wf_fedEx_wooCommerce_shipping_init() {
				include_once( 'includes/class-wf-fedex-woocommerce-shipping.php' );
				$shipping_obj = new wf_fedex_woocommerce_shipping_method();
				//This filer kept outside of 'wf_fedex_woocommerce_shipping_method'. Because, the scope of filter should be avail outside of calculate_shipping() method.
            	add_filter( 'woocommerce_cart_shipping_method_full_label', array($shipping_obj, 'wf_add_delivery_time'), 10, 2 );
			}

			
			public function wf_fedEx_wooCommerce_shipping_methods( $methods ) {
				$methods[] = 'wf_fedex_woocommerce_shipping_method';
				return $methods;
			}	
			public function ph_fedex_checkout_scripts_for_hold_at_location( ) {
					if(is_checkout()) {
						wp_enqueue_script( 'ph-fedex-checkout-script', plugins_url( '/resources/js/ph_fedex_checkout.js', __FILE__ ), array( 'jquery' ) );
					}
			}	
		}
		new wf_fedEx_wooCommerce_shipping_setup();
	}
}