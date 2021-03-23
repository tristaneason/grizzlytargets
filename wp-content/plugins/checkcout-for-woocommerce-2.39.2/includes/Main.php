<?php

namespace Objectiv\Plugins\Checkout;

// Base package classes
use Exception;
use Objectiv\BoosterSeat\Language\i18n;
use Objectiv\BoosterSeat\Utilities\Activator;
use Objectiv\BoosterSeat\Utilities\Deactivator;
use Objectiv\BoosterSeat\Base\Singleton;

// Checkout for WooCommerce
use Objectiv\Plugins\Checkout\Action\ApplyCouponAction;
use Objectiv\Plugins\Checkout\Action\CompleteOrderAction;
use Objectiv\Plugins\Checkout\Action\UpdateCartAction;
use Objectiv\Plugins\Checkout\Action\UpdateCheckoutAction;
use Objectiv\Plugins\Checkout\Action\UpdatePaymentMethodAction;
use Objectiv\Plugins\Checkout\Core\AddressAutocomplete;
use Objectiv\Plugins\Checkout\Core\Customizer;
use Objectiv\Plugins\Checkout\Core\Form;
use Objectiv\Plugins\Checkout\Core\Redirect;
use Objectiv\Plugins\Checkout\Core\Loader;
use Objectiv\Plugins\Checkout\Managers\TemplatesManager;
use Objectiv\Plugins\Checkout\Stats\StatCollection;
use Objectiv\Plugins\Checkout\Managers\ActivationManager;
use Objectiv\Plugins\Checkout\Managers\SettingsManager;
use Objectiv\Plugins\Checkout\Managers\AjaxManager;
use Objectiv\Plugins\Checkout\Managers\ExtendedPathManager;
use Objectiv\Plugins\Checkout\Action\AccountExistsAction;
use Objectiv\Plugins\Checkout\Action\LogInAction;
use Objectiv\Plugins\Checkout\Compatibility\Manager as CompatibilityManager;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout
 * @author Brandon Tassone <brandontassone@gmail.com>
 */

class Main extends Singleton {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var Loader $loader Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * Template related functionality manager
	 *
	 * @since 1.0.0
	 * @access private
	 * @var TemplatesManager $templates_manager Handles all template related functionality.
	 */
	private $templates_manager;

	/**
	 * @since 1.1.4
	 * @access private
	 * @var ExtendedPathManager $path_manager Handles the path information for the plugin
	 */
	private $path_manager;

	/**
	 * @since 1.0.0
	 * @access private
	 * @var AjaxManager $ajax_manager
	 */
	private $ajax_manager;

	/**
	 * Language class dealing with translating the various parts of the plugin
	 *
	 * @since 1.0.0
	 * @access private
	 * @var i18n The language class
	 */
	private $i18n;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of the plugin.
	 */
	private $version;

	/**
	 * Settings class for accessing user defined settings.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var SettingsManager $settings The settings object.
	 */
	private $settings_manager;

	/**
	 * Updater class for handling licenses
	 *
	 * @since 1.0.0
	 * @access private
	 * @var \CGD_EDDSL_Magic $updater The updater object.
	 */
	private $updater;

	/**
	 * Customizer compatibility class
	 *
	 * @since 2.4.0
	 * @access private
	 * @var Customizer $customizer The updater object.
	 */
	private $customizer;

	/**
	 * Customizer compatibility class
	 *
	 * @since 2.4.0
	 * @access private
	 * @var Customizer $customizer The updater object.
	 */
	private $address_autocomplete;

	/**
	 * Activation manager for handling activation conditions
	 *
	 * @since 1.1.4
	 * @access private
	 * @var ActivationManager $activation_manager Handles activation
	 */
	private $activation_manager;

	/**
	 * Settings class for accessing user defined settings.
	 *
	 * @since 1.1.4
	 * @access private
	 * @var Deactivator $deactivator Handles deactivation
	 */
	private $deactivator;

	/**
	 * @since 1.1.5
	 * @access private
	 * @var Form $form Handles the WooCommerce form changes
	 */
	private $form;

	/**
	 * @since 2.4.12
	 * @access private
	 * @var StatCollection Handles the stat collection for CFW
	 */
	private $stat_collection;

	/**
	 * Main constructor.
	 */
	public function __construct() {
		// Program Details
		$this->plugin_name = 'Checkout for WooCommerce';
		$this->version     = CFW_VERSION;
	}

	/**
	 * Returns the i18n language class
	 *
	 * @since 1.0.0
	 * @access public
	 * @return i18n
	 */
	public function get_i18n() {
		return $this->i18n;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Returns the path manager
	 *
	 * @since 1.1.4
	 * @access public
	 * @return ExtendedPathManager
	 */
	public function get_path_manager() {
		return $this->path_manager;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @return AjaxManager
	 */
	public function get_ajax_manager() {
		return $this->ajax_manager;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Returns the template manager
	 *
	 * @since 1.0.0
	 * @access public
	 * @return TemplatesManager
	 */
	public function get_templates_manager() {
		return $this->templates_manager;
	}

	/**
	 * Returns the template manager
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function set_templates_manager( $templates_manager ) {
		$this->templates_manager = $templates_manager;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Get the settings manager
	 *
	 * @since 1.0.0
	 * @access public
	 * @return SettingsManager The settings manager object
	 */
	public function get_settings_manager() {
		return $this->settings_manager;
	}

	/**
	 * Set the settings manager
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function set_settings_manager( $settings_manager ) {
		$this->settings_manager = $settings_manager;
	}

	/**
	 * Get the updater object
	 *
	 * @since 1.0.0
	 * @access public
	 * @return \CGD_EDDSL_Magic The updater object
	 */
	public function get_updater() {
		return $this->updater;
	}

	/**
	 * Get the updater object
	 *
	 * @since 1.1.4
	 * @access public
	 * @return ActivationManager The class handling activation of the plugin
	 */
	public function get_activation_manager() {
		return $this->activation_manager;
	}

	/**
	 * Get the updater object
	 *
	 * @since 1.1.4
	 * @access public
	 * @return Deactivator The class handling deactivation of the plugin
	 */
	public function get_deactivator() {
		return $this->deactivator;
	}

	/**
	 * @since 1.1.5
	 * @access public
	 * @return Form The form object
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * @since 2.4.12
	 * @access public
	 * @return Stats\StatCollection The form object
	 */
	public function get_stat_collection() {
		return $this->stat_collection;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $file The file path to the main plugin file
	 */
	public function run( $file ) {
		// Enable program flags
		$this->check_flags();

		// Create and setup the plugins main objects
		$this->create_main_objects( $file );

		// Data upgrades
		$this->upgrades();

		// Loads all the ajax handlers on the php side
		$this->configure_objects();

		// Run this as early as we can to maximize integrations
		add_action(
			'plugins_loaded', function() {
				// Adds the plugins hooks
				$this->add_plugin_hooks();
			}, 0
		);
	}

	function upgrades() {
		$db_version = get_option( 'cfw_db_version', '0.0.0' );

		// < 2.22.0
		if ( version_compare( $db_version, '2.22.0', '<' ) ) {
			if ( $this->get_settings_manager()->get_setting( 'enable_phone_fields' ) == 'yes' ) {
				update_option( 'woocommerce_checkout_phone_field', 'required' );
			} else {
				update_option( 'woocommerce_checkout_phone_field', 'hidden' );
			}
		}

		// Only update db version if the current version is greater than the db version
		if ( version_compare( CFW_VERSION, $db_version, '>' ) ) {
			update_option( 'cfw_db_version', CFW_VERSION );
		}
	}

	/**
	 * When run checks to see if the flag is defined and its value (inversely). If found to be active, it runs the
	 * function
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function check_flags() {
		( ! defined( 'CFW_DEV_MODE' ) || ! CFW_DEV_MODE ) ?: $this->enable_dev_mode();
	}

	/**
	 * Enables libraries and functions for the specific task of aiding in development
	 *
	 * Kint - Pretty Debug
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function enable_dev_mode() {
		// Enable Kint
		if ( class_exists( '\Kint' ) && property_exists( '\Kint', 'enabled_mode' ) ) {
			\Kint::$enabled_mode = true;
		}
	}

	/**
	 * Creates the main objects used in this plugins setup and processing
	 *
	 * Note: Realistically the only function that would be needed for testing.
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $file The file path to the main plugin file
	 */
	private function create_main_objects( $file ) {

		// Create the loader for actions and filters
		$this->loader = new Loader();

		// Set up localization
		$this->i18n = new i18n( 'checkout-wc' );

		// Activation Manager
		$this->activation_manager = new ActivationManager( $this->get_activator_checks() );

		// Deactivator
		$this->deactivator = new Deactivator();

		// The path manager for the plugin
		$this->path_manager = new ExtendedPathManager( plugin_dir_path( $file ), plugin_dir_url( $file ), $file );

		// The settings manager for the plugin
		$this->settings_manager = new SettingsManager();

		// Stat collection
		$this->stat_collection = StatCollection::instance( $this->settings_manager );

		$active_template = $this->settings_manager->get_setting( 'active_template' );

		// Create the template manager
		$this->templates_manager = new TemplatesManager( $this->path_manager, empty( $active_template ) ? 'default' : $active_template );

		// Create the ajax manager
		$this->ajax_manager = new AjaxManager( $this->get_ajax_actions(), $this->loader );

		// License updater
		$this->updater = new \CGD_EDDSL_Magic( '_cfw_licensing', false, CFW_UPDATE_URL, $this->get_version(), CFW_NAME, 'Objectiv', $file, $theme = false, $this->get_settings_manager()->get_setting( 'beta_updates' ), $this->get_home_url() );

		// Customizer
		$this->customizer = new Customizer( $this->get_settings_manager(), $this->get_templates_manager(), $this->get_path_manager() );

		// Address Autocomplete
		$this->address_autocomplete = new AddressAutocomplete();
	}

	public function get_activator_checks() {
		return array(
			array(
				'id'       => 'checkout-woocommerce_activation',
				'function' => 'class_exists',
				'value'    => 'WooCommerce',
				'message'  => array(
					'success' => false,
					'class'   => 'notice error',
					'message' => __( 'Activation failed: Please activate WooCommerce in order to use Checkout for WooCommerce', 'checkout-wc' ),
				),
			),
		);
	}

	/**
	 * @since 1.0.0
	 * @access private
	 */
	private function configure_objects() {
		$this->ajax_manager->load_all();
	}

	/**
	 * @return array
	 */
	public function get_ajax_actions() {
		// Setting no_privilege to false because wc_ajax doesn't have a no privilege endpoint.
		return array(
			new AccountExistsAction( 'account_exists', false, 'wc_ajax_' ),
			new LogInAction( 'login', false, 'wc_ajax_' ),
			new CompleteOrderAction( 'complete_order', false, 'wc_ajax_' ),
			new ApplyCouponAction( 'cfw_apply_coupon', false, 'wc_ajax_' ),
			new UpdateCheckoutAction( 'update_checkout', false, 'wc_ajax_' ),
			new UpdatePaymentMethodAction( 'update_payment_method', false, 'wc_ajax_' ),
			new UpdateCartAction( 'update_cart', false, 'wc_ajax_' ),
		);
	}

	/**
	 * Set the plugin assets
	 */
	public function set_assets() {
		if ( ! Main::is_checkout() && ! Main::is_checkout_pay_page() && ! Main::is_order_received_page() ) {
			return;
		}

		/**
		 * WP Rocket
		 *
		 * Disable minify / cdn features while we're on the checkout page due to strange issues.
		 */
		if ( ! defined( 'DONOTROCKETOPTIMIZE' ) ) {
			define( 'DONOTROCKETOPTIMIZE', true );
		}

		$front = trailingslashit( $this->path_manager->get_assets_path() ) . 'front';

		// Minified extension
		$min = ( ! CFW_DEV_MODE ) ? '.min' : '';

		// Google API Key
		$google_api_key = $this->get_settings_manager()->get_setting( 'google_places_api_key' );

		/**
		 * Dequeue Native Scripts
		 */
		// Many plugins enqueue their scripts with 'woocommerce' and 'wc-checkout' as a dependent scripts
		// So, instead of modifying these scripts we dequeue WC's native scripts and then
		// queue our own scripts using the same handles. Magic!
		wp_dequeue_script( 'woocommerce' );
		wp_deregister_script( 'woocommerce' );
		wp_dequeue_script( 'wc-checkout' );
		wp_deregister_script( 'wc-checkout' );

		if ( Main::is_checkout() ) {
			/**
			 * Styles
			 */
			wp_enqueue_style( 'cfw_front_css', "{$front}/css/checkoutwc-front{$min}.css", array(), $this->get_version() );

			/**
			 * Scripts
			 */
			wp_enqueue_script( 'woocommerce', "{$front}/js/checkoutwc-front{$min}.js", array( 'jquery', 'jquery-blockui', 'jquery-migrate', 'js-cookie' ), $this->get_version() );

			// Address Autocomplete Script
			if ( 'yes' == $this->get_settings_manager()->get_setting( 'enable_address_autocomplete' ) ) {
				$google_places_api_key = $this->get_settings_manager()->get_setting( 'google_places_api_key' );

				wp_enqueue_script( 'cfw-google-places', "https://maps.googleapis.com/maps/api/js?key=$google_api_key&libraries=places", array( 'woocommerce' ) );
			}
		} elseif ( Main::is_checkout_pay_page() ) {
			/**
			 * Styles
			 */
			wp_enqueue_style( 'cfw_front_css', "{$front}/css/checkoutwc-order-pay{$min}.css", array(), $this->get_version() );

			/**
			 * Scripts
			 */
			wp_enqueue_script( 'woocommerce', "{$front}/js/checkoutwc-order-pay{$min}.js", array( 'jquery', 'jquery-blockui', 'jquery-migrate', 'js-cookie' ), $this->get_version() );
		} elseif ( Main::is_order_received_page() ) {
			/**
			 * Styles
			 */
			wp_enqueue_style( 'cfw_front_css', "{$front}/css/checkoutwc-thank-you{$min}.css", array(), $this->get_version() );

			// FontAwesome
			wp_enqueue_style( 'cfw-fontawesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );

			/**
			 * Scripts
			 */
			if ( 'yes' == $this->get_settings_manager()->get_setting( 'enable_map_embed' ) && 'yes' == $this->get_settings_manager()->get_setting( 'enable_thank_you_page' ) ) {
				wp_enqueue_script( 'cfw-google-places', "https://maps.googleapis.com/maps/api/js?key=$google_api_key", array( 'woocommerce' ) );
			}
			wp_enqueue_script( 'woocommerce', "{$front}/js/checkoutwc-thank-you{$min}.js", array( 'jquery', 'jquery-blockui', 'jquery-migrate', 'js-cookie' ), $this->get_version() );
		}

		// Load template scripts / styles
		do_action( 'cfw_load_template_assets' );

		$cfwEventData = apply_filters(
			'cfw_event_data', array(
				'elements'      => array(
					'easyTabsWrapElClass'  => apply_filters( 'cfw_template_easy_tabs_wrap_el_id', '.cfw-tabs-initialize' ),
					'breadCrumbElId'       => apply_filters( 'cfw_template_breadcrumb_id', '#cfw-breadcrumb' ),
					'customerInfoElId'     => apply_filters( 'cfw_template_customer_info_el', '#cfw-customer-info' ),
					'shippingMethodElId'   => apply_filters( 'cfw_template_shipping_method_el', '#cfw-shipping-method' ),
					'paymentMethodElId'    => apply_filters( 'cfw_template_payment_method_el', '#cfw-payment-method' ),
					'tabContainerElId'     => apply_filters( 'cfw_template_tab_container_el', '#cfw-tab-container' ),
					'alertContainerId'     => apply_filters( 'cfw_template_alert_container_el', '#cfw-alert-container' ),
					'checkoutFormSelector' => apply_filters( 'cfw_checkout_form_selector', '.woocommerce-checkout' ),
				),
				'ajaxInfo'      => array(
					'url' => trailingslashit( get_home_url() ),
				),
				'compatibility' => apply_filters( 'cfw_typescript_compatibility_classes_and_params', array() ),
				'settings'      => array(
					'locale'                          => $this->get_locale(), // required for parsley localization
					'user_logged_in'                  => ( is_user_logged_in() ) ? true : false,
					'default_address_fields'          => array_keys( WC()->countries->get_default_address_fields() ),
					'enable_zip_autocomplete'         => apply_filters( 'cfw_enable_zip_autocomplete', true ) ? true : false,
					'check_create_account_by_default' => apply_filters( 'cfw_check_create_account_by_default', true ) ? true : false,
					'enable_checkout_login_reminder'  => 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) && apply_filters( 'cfw_suppress_default_login_form', true ) && Main::is_checkout(),
					'suppress_default_login_form'     => apply_filters( 'cfw_suppress_default_login_form', true ),
					'needs_shipping_address'          => WC()->cart->needs_shipping_address(),
					'show_shipping_tab'               => apply_filters( 'cfw_show_shipping_tab', true ),
					'enable_address_autocomplete'     => $this->get_settings_manager()->get_setting( 'enable_address_autocomplete' ) == 'yes',
					'enable_map_embed'                => $this->get_settings_manager()->get_setting( 'enable_map_embed' ) == 'yes',
					'load_tabs'                       => apply_filters( 'cfw_load_tabs', Main::is_checkout() ),
					'is_checkout_pay_page'            => Main::is_checkout_pay_page(),
					'is_order_received_page'          => Main::is_order_received_page(),
					'address_autocomplete_shipping_countries' => apply_filters( 'cfw_address_autocomplete_shipping_countries', false ),
					'address_autocomplete_billing_countries' => apply_filters( 'cfw_address_autocomplete_billing_countries', false ),
					'quantity_prompt_message'         => __( 'Please enter a new quantity:', 'checkout-wc' ),
					'is_registration_required'        => WC()->checkout()->is_registration_required(),
					'account_already_registered_notice' => apply_filters( 'woocommerce_registration_error_email_exists', cfw__( 'An account is already registered with your email address. Please log in.', 'woocommerce' ) ),
					'registration_generate_password'  => apply_filters( 'cfw_registration_generate_password', true ),
					'thank_you_shipping_address'      => false,
					'shipping_address_label'          => __( 'Shipping address', 'checkout-wc' ),
				),
				'checkout_params' => array(
					'ajax_url'                  => WC()->ajax_url(),
					'wc_ajax_url'               => \WC_AJAX::get_endpoint( '%%endpoint%%' ),
					'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
					'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
					'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
					'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
					'checkout_url'              => \WC_AJAX::get_endpoint( 'checkout' ),
					'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
					'debug_mode'                => defined( 'WP_DEBUG' ) && WP_DEBUG,
					'i18n_checkout_error'       => esc_attr__( 'Error processing checkout. Please try again.', 'woocommerce' ),
				)
			)
		);

		if ( Main::is_order_received_page() ) {
			$order = $this->get_order_received_order();
			$address = $order->get_address( 'shipping' );

			// Remove name and company before generate the Google Maps URL.
			unset( $address['first_name'], $address['last_name'], $address['company'] );

			$address = apply_filters( 'woocommerce_shipping_address_map_url_parts', $address, $order );
			$address = array_filter( $address );
			$address = implode( ', ', $address );

			$cfwEventData['settings']['thank_you_shipping_address'] = $address;
		}

		wp_localize_script(
			'woocommerce', 'cfwEventData', $cfwEventData
		);

		if ( Main::is_checkout() || Main::is_checkout_pay_page() ) {
			// Workaround for WooCommerce 3.8 Beta 1
			global $wp_scripts;
			$wp_scripts->registered['wc-country-select']->deps = array( 'jquery' );

			// WooCommerce Native Localization Handling
			wp_enqueue_script( 'wc-country-select' );
			wp_enqueue_script( 'wc-address-i18n' );
		}
	}

	function get_locale() {
		$raw_locale = get_user_locale();

		$locale = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : strstr( get_user_locale(), '_', true );

		if ( ! $locale ) {
			$locale = get_user_locale();
		} elseif ( $raw_locale == 'pt_BR' ) {
			$locale = 'pt-br';
		}

		if ( $locale == 'nb' || $locale == 'nn' ) {
			$locale = 'no';
		}

		return $locale;
	}

	/**
	 * Add the actions and hooks used by the plugin (filtered through the Loader class) then run register them with
	 * WordPress
	 */
	public function add_plugin_hooks() {
		// Load the plugin actions
		$this->load_actions();

		// Load the plugin filters
		$this->load_filters();

		// Image size for cart thumbnails
		add_image_size( 'cfw_cart_thumbnail', 150, 0, false );

		if ( $this->is_enabled() ) {
			// Load Compatibility Class
			$this->compatibility();

			// Load Assets
			$this->loader->add_action( 'wp_enqueue_scripts', array( $this, 'set_assets' ), 11 ); // 11 is 1 after 10, which is where WooCommerce loads their scripts

			// Init hooks
			$this->loader->add_action( 'init', array( $this, 'init_hooks' ) );
		}

		// Add the actions and filters to the system. They were added to the class, this registers them in WordPress.
		$this->loader->run();
	}

	/**
	 * Check if theme should enabled
	 *
	 * @return bool
	 */
	function is_enabled() {
		$result = false;

		if ( ! function_exists( 'WC' ) ) {
			$result = false; // superfluous, but sure
		}

		if ( ( $this->license_is_valid() && $this->settings_manager->get_setting( 'enable' ) == 'yes' ) || current_user_can( 'manage_options' ) ) {
			$result = true;
		}

		return apply_filters( 'cfw_checkout_is_enabled', $result );
	}

	function compatibility() {
		new CompatibilityManager();
	}

	/**
	 * Handles general purpose WordPress actions.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_actions() {

		// Add the Language class
		$this->i18n->load_plugin_textdomain( $this->path_manager );

		// Override some WooCommerce Options
		if ( ( $this->license_is_valid() && $this->settings_manager->get_setting( 'enable' ) == 'yes' ) ) {
			// For some reason, using the loader add_filter here doesn't work *shrug*
			add_filter( 'pre_option_woocommerce_registration_generate_password', array( $this, 'override_woocommerce_registration_generate_password' ), 10, 1 );
		}

		// Handle the Activation notices
		$this->loader->add_action(
			'admin_notices', function() {
				$this->get_activation_manager()->activate_admin_notice( $this->get_path_manager() );
			}
		);

		// Setup the Checkout redirect
		$this->loader->add_action(
			'template_redirect', function() {
				if ( $this->is_enabled() ) {
					if ( Main::is_checkout() ) {
						Redirect::checkout( $this->settings_manager, $this->path_manager, $this->templates_manager, $this->version );
					} elseif ( Main::is_checkout_pay_page() ) {
						Redirect::order_pay( $this->settings_manager, $this->path_manager, $this->templates_manager, $this->version );
					} elseif ( Main::is_order_received_page() ) {
						Redirect::order_received( $this->settings_manager, $this->path_manager, $this->templates_manager, $this->version, $this->get_order_received_order() );
					}
				}
			}, 11
		);

		// Admin toolbar
		$this->loader->add_action( 'admin_bar_menu', array( $this, 'add_admin_buttons' ), 100 );
	}

	function init_hooks() {
		// Required to render form fields
		$this->form = new Form();
	}

	/**
	 * @return bool|\WC_Order|\WC_Order_Refund
	 */
	function get_order_received_order() {
		global $wp;

		$order_id = $wp->query_vars['order-received'];
		$order    = false;

		// Get the order.
		$order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
		$order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

		if ( $order_id > 0 ) {
			$order = wc_get_order( $order_id );
			if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
				$order = false;
			}
		}

		return $order;
	}

	/**
	 * Get phone field setting
	 *
	 * @return boolean
	 */
	function is_phone_fields_enabled() {
		return apply_filters( 'cfw_enable_phone_fields', 'hidden' !== get_option( 'woocommerce_checkout_phone_field' ) );
	}

	function add_admin_buttons( $admin_bar ) {
		if ( ! Main::is_checkout() ) {
			return;
		}

		// Remove irrelevant buttons
		$admin_bar->remove_node( 'new-content' );
		$admin_bar->remove_node( 'updates' );
		$admin_bar->remove_node( 'edit' );
		$admin_bar->remove_node( 'comments' );

		$admin_bar->add_node(
			array(
				'id'    => 'cfw-settings',
				'title' => '<span class="ab-icon dashicons dashicons-cart"></span>' . __( 'Checkout for WooCommerce', 'checkout-wc' ),
				'href'  => admin_url( 'options-general.php?page=cfw-settings' ),
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'cfw-general-settings',
				'title'  => __( 'General', 'checkout-wc' ),
				'href'   => admin_url( 'options-general.php?page=cfw-settings' ),
				'parent' => 'cfw-settings',
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'cfw-template-settings',
				'title'  => __( 'Template', 'checkout-wc' ),
				'href'   => admin_url( 'options-general.php?page=cfw-settings&subpage=templates' ),
				'parent' => 'cfw-settings',
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'cfw-design-settings',
				'title'  => __( 'Design', 'checkout-wc' ),
				'href'   => admin_url( 'options-general.php?page=cfw-settings&subpage=design' ),
				'parent' => 'cfw-settings',
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'cfw-license-settings',
				'title'  => __( 'License', 'checkout-wc' ),
				'href'   => admin_url( 'options-general.php?page=cfw-settings&subpage=license' ),
				'parent' => 'cfw-settings',
			)
		);

		$admin_bar->add_node(
			array(
				'id'     => 'cfw-support-settings',
				'title'  => __( 'Support', 'checkout-wc' ),
				'href'   => admin_url( 'options-general.php?page=cfw-settings&subpage=support' ),
				'parent' => 'cfw-settings',
			)
		);
	}

	/**
	 * Filters in this plugin allow you to augment a lot of the default functionality present. Anything mission critical
	 * that needs to be augmented will probably have a filter attached
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function load_filters() {
		if ( $this->get_settings_manager()->get_setting('enable_thank_you_page') == 'yes' ) {
			add_filter( 'woocommerce_get_view_order_url', array( $this, 'override_view_order_url' ), 100, 2 );
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-midas-activator.php
	 */
	public static function activation() {
		// Get main
		$main = Main::instance();

		$errors = $main->get_activation_manager()->activate();

		// Init settings
		$main->get_settings_manager()->add_setting( 'enable', 'no' );
		$main->get_settings_manager()->add_setting( 'enable_phone_fields', 'no' );
		$main->get_settings_manager()->add_setting( 'active_template', 'default' );
		$main->get_settings_manager()->add_setting( 'settings_version', '200' );

		// Set defaults
		$templates = $main->get_templates_manager()->getAvailableTemplates();

		foreach ( $templates as $template ) {
			$supports = $template->get_supports();

			if ( in_array( 'header-background', $supports ) ) {
				if ( $template->get_slug() == 'futurist' ) {
					$main->get_settings_manager()->add_setting( 'header_background_color', '#000000', array( $template->get_slug() ) );
					$main->get_settings_manager()->add_setting( 'header_text_color', '#ffffff', array( $template->get_slug() ) );
				} else {
					$main->get_settings_manager()->add_setting( 'header_background_color', '#ffffff', array( $template->get_slug() ) );
				}
			}

			if ( in_array( 'footer-background', $supports ) ) {
				$main->get_settings_manager()->add_setting( 'footer_background_color', '#ffffff', array( $template->get_slug() ) );
				$main->get_settings_manager()->add_setting( 'footer_color', '#999999', array( $template->get_slug() ) );
			}

			if ( in_array( 'summary-background', $supports ) ) {
				if ( $template->get_slug() == 'copify' ) {
					$main->get_settings_manager()->add_setting( 'summary_background_color', '#fafafa', array( $template->get_slug() ) );
				} else {
					$main->get_settings_manager()->add_setting( 'summary_background_color', '#ffffff', array( $template->get_slug() ) );
				}
			}

			$main->get_settings_manager()->add_setting( 'header_text_color', '#2b2b2b', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'footer_color', '#999999', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'link_color', '#e9a81d', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'button_color', '#e9a81d', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'button_text_color', '#000000', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'secondary_button_color', '#999999', array( $template->get_slug() ) );
			$main->get_settings_manager()->add_setting( 'secondary_button_text_color', '#ffffff', array( $template->get_slug() ) );
		}

		// Updater license status cron
		$main->updater->set_license_check_cron();

		// Set the stat collection cron
		$main->set_stat_collection_cron();

		if ( ! $errors ) {

			// Welcome screen transient
			set_transient( '_cfw_welcome_screen_activation_redirect', true, 30 );
		}
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-midas-deactivator.php
	 */
	public static function deactivation() {
		// Get main
		$main = Main::instance();

		$main->get_deactivator()->deactivate();

		// Remove cron for license update check
		$main->updater->unset_license_check_cron();

		// Unset the stat collection cron
		$main->unset_stat_collection_cron();
	}

	/**
	 * Stat collection cron
	 */
	public function set_stat_collection_cron() {
		if ( ! wp_next_scheduled( 'cfw_weekly_scheduled_events_tracking' ) ) {
			wp_schedule_event( time(), 'weekly', 'cfw_weekly_scheduled_events_tracking' );
		}
	}

	/**
	 * Unset the collection cron
	 */
	public function unset_stat_collection_cron() {
		wp_clear_scheduled_hook( 'cfw_weekly_scheduled_events_tracking' );
	}

	/**
	 * @param string $result
	 *
	 * @return string
	 */
	function override_woocommerce_registration_generate_password( $result ) {
		if ( Main::is_checkout() && apply_filters( 'cfw_registration_generate_password', true ) ) {
			return 'yes';
		}

		return $result;
	}

	/**
	 * @return bool True if license valid, false if it is invalid
	 */
	function license_is_valid() {
		// Get main
		$main = Main::instance();

		$key_status  = $main->updater->get_field_value( 'key_status' );
		$license_key = $main->updater->get_field_value( 'license_key' );

		$valid = true;

		if ( getenv( 'TRAVIS' ) ) {
			return $valid;
		}

		// Validate Key Status
		if ( empty( $license_key ) || ( ( $key_status !== 'valid' || $key_status == 'inactive' || $key_status == 'site_inactive' ) ) ) {
			$valid = true;
		}

		return $valid;
	}

	public static function is_checkout() {
		return apply_filters( 'cfw_is_checkout', function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() && ! is_checkout_pay_page() );
	}

	public static function is_checkout_pay_page() {
		return apply_filters( 'cfw_is_checkout_pay_page', function_exists( 'is_checkout_pay_page' ) && is_checkout_pay_page() && Main::instance()->get_templates_manager()->getActiveTemplate()->supports( 'order-pay' ) && Main::instance()->get_settings_manager()->get_setting( 'enable_order_pay' ) == 'yes' );
	}

	public static function is_order_received_page() {
		return apply_filters( 'cfw_is_order_received_page', function_exists( 'is_order_received_page' ) && is_order_received_page() && Main::instance()->get_templates_manager()->getActiveTemplate()->supports( 'order-received' ) && Main::instance()->get_settings_manager()->get_setting( 'enable_thank_you_page' ) == 'yes' );
	}

	public static function is_cfw_page() {
		return Main::is_checkout() || Main::is_checkout_pay_page() || Main::is_order_received_page();
	}

	/**
	 * Retrieves the URL for a given site where the front end is accessible.
	 *
	 * Returns the 'home' option with the appropriate protocol. The protocol will be 'https'
	 * if is_ssl() evaluates to true; otherwise, it will be the same as the 'home' option.
	 * If `$scheme` is 'http' or 'https', is_ssl() is overridden.
	 *
	 * Copied from WordPress 5.2.0
	 *
	 * @since 3.0.0
	 *
	 * @global string $pagenow
	 *
	 * @param  int         $blog_id Optional. Site ID. Default null (current site).
	 * @param  string      $path    Optional. Path relative to the home URL. Default empty.
	 * @param  string|null $scheme  Optional. Scheme to give the home URL context. Accepts
	 *                              'http', 'https', 'relative', 'rest', or null. Default null.
	 * @return string Home URL link with optional path appended.
	 */
	function get_home_url( $blog_id = null, $path = '', $scheme = null ) {
		global $pagenow;

		$orig_scheme = $scheme;

		if ( empty( $blog_id ) || ! is_multisite() ) {
			$url = get_option( 'home' );
		} else {
			switch_to_blog( $blog_id );
			$url = get_option( 'home' );
			restore_current_blog();
		}

		if ( ! in_array( $scheme, array( 'http', 'https', 'relative' ) ) ) {
			if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $pagenow ) {
				$scheme = 'https';
			} else {
				$scheme = parse_url( $url, PHP_URL_SCHEME );
			}
		}

		$url = set_url_scheme( $url, $scheme );

		if ( $path && is_string( $path ) ) {
			$url .= '/' . ltrim( $path, '/' );
		}

		/**
		 * Filters the home URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string      $url         The complete home URL including scheme and path.
		 * @param string      $path        Path relative to the home URL. Blank string if no path is specified.
		 * @param string|null $orig_scheme Scheme to give the home URL context. Accepts 'http', 'https',
		 *                                 'relative', 'rest', or null.
		 * @param int|null    $blog_id     Site ID, or null for the current site.
		 */
		return apply_filters( 'cfw_home_url', $url, $path, $orig_scheme, $blog_id );
	}

	/**
	 * @param string $url
	 * @param \WC_Order $order
	 *
	 * @return string
	 */
	function override_view_order_url( $url, $order ) {
		return $order->get_checkout_order_received_url();
	}
}
