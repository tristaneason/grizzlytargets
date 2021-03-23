<?php
/**
 * WooCommerce Authorize.Net Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-cim/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2021, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Authorize_Net\Emulation\Migration\Installer;
use SkyVerge\WooCommerce\PluginFramework\v5_10_4 as Framework;


/**
 * WooCommerce Authorize.Net Gateway main plugin class.
 *
 * @since 2.0.0
 *
 * @method \WC_Gateway_Authorize_Net_CIM[] get_gateways()
 */
class WC_Authorize_Net_CIM extends Framework\SV_WC_Payment_Gateway_Plugin {


	/** string version number */
	const VERSION = '3.5.0';


	/** @var \WC_Authorize_Net_CIM_Webhooks the webhooks handler */
	protected $webhooks;

	/** @var \WC_Authorize_Net_CIM single instance of this plugin */
	protected static $instance;

	/** plugin ID */
	const PLUGIN_ID = 'authorize_net_cim';

	/** string the gateway class name */
	const CREDIT_CARD_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_CIM_Credit_Card';

	/** string the gateway ID */
	const CREDIT_CARD_GATEWAY_ID = 'authorize_net_cim_credit_card';

	/** string the gateway class name */
	const ECHECK_GATEWAY_CLASS_NAME = 'WC_Gateway_Authorize_Net_CIM_eCheck';

	/** string the gateway ID */
	const ECHECK_GATEWAY_ID = 'authorize_net_cim_echeck';

	/** string the emulation gateway ID */
	const EMULATION_GATEWAY_ID = 'authorize_net_aim_emulation';

	/** string the emulation gateway class name */
	const EMULATION_GATEWAY_CLASS_NAME = SkyVerge\WooCommerce\Authorize_Net\Emulation\Credit_Card::class;


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'text_domain'  => 'woocommerce-gateway-authorize-net-cim',
				'gateways'     => $this->get_enabled_gateways(),
				'dependencies' => array(
					'php_extensions' => array( 'SimpleXML', 'xmlwriter', 'dom' ),
				),
				'require_ssl' => true,
				'supports'    => array(
					self::FEATURE_CAPTURE_CHARGE,
					self::FEATURE_MY_PAYMENT_METHODS,
					self::FEATURE_CUSTOMER_ID,
				),
			)
		);

		// Load gateway files
		$this->includes();

		// display the admin Shipping Address ID user field
		add_action( 'wc_payment_gateway_' . $this->get_id() . '_user_profile', array( $this, 'display_shipping_address_id_field' ), 15 );

		if ( is_admin() && ! is_ajax() ) {

			// handle activating/deactivating emulation gateway
			add_action( 'admin_action_wc_authorize_net_toggle_emulation', array( $this, 'toggle_emulation' ) );

			// save the admin Shipping Address ID user field
			add_action( 'personal_options_update',  array( $this, 'save_shipping_address_id_field' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_shipping_address_id_field' ) );

			// handle installing emulation plugin
			add_action( 'admin_action_wc_authorize_net_install_emulation_gateway_plugin', [ $this, 'install_emulation_gateway_plugin' ] );
		}
	}


	/**
	 * Installs the Authorize.Net Emulation for WooCommerce gateway plugin.
	 *
	 * @internal
	 *
	 * @since 3.5.0
	 */
	public function install_emulation_gateway_plugin() {

		$redirect_url = wp_get_referer();

		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'wc_authorize_net_install_emulation_gateway_plugin' ) ) {
			wp_safe_redirect( $redirect_url );
			exit();
		}

		try {

			Installer::install_and_activate( $redirect_url );

		} catch ( Exception $exception ) {

			wc_authorize_net_cim()->log( 'Could not install emulation plugin. ' . $exception->getMessage() );

			wc_authorize_net_cim()->get_message_handler()->add_error( sprintf(
				/* translators: %1$s error message, %2$s <a> tag, %3$s </a> tag */
				__( 'Oops! It looks like an error occurred while installing the new plugin: %1$s. We recommend you to %2$sdownload%3$s and install it manually.', 'woocommerce-gateway-authorize-net-cim' ),
				$exception->getMessage(),
				'<a href="https://github.com/skyverge/authorize-net-emulation-for-woocommerce/releases/download/initial/authorize-net-emulation-for-woocommerce.zip" target="_blank">', '</a>'
			) );

			wp_safe_redirect( $redirect_url );
			exit();
		}
	}


	/**
	 * Gets the enabled gateway IDs and class names.
	 *
	 * The core Credit Card and eCheck gateways are always enabled, with the emulation gateway being optional.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_enabled_gateways() {

		$gateways = [
			self::CREDIT_CARD_GATEWAY_ID => self::CREDIT_CARD_GATEWAY_CLASS_NAME,
			self::ECHECK_GATEWAY_ID      => self::ECHECK_GATEWAY_CLASS_NAME,
		];

		if ( $this->is_emulation_enabled() ) {
			$gateways[ self::EMULATION_GATEWAY_ID ] = self::EMULATION_GATEWAY_CLASS_NAME;
		}

		return $gateways;
	}


	/**
	 * Determines if the emulation gateway is enabled.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function is_emulation_enabled() {

		return 'yes' === get_option( 'wc_authorize_net_emulation_enabled' );
	}


	/**
	 * Loads API and gateway classes.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		require_once( $this->get_plugin_path() . '/includes/Payment_Form.php' );

		require_once( $this->get_plugin_path() . '/includes/Emulation/Migration/Installer.php' );

		require_once( $this->get_plugin_path() . '/includes/Handlers/Capture.php' );
		require_once( $this->get_plugin_path() . '/includes/Handlers/Hosted_Payment_Handler.php' );

		require_once( $this->get_plugin_path() . '/includes/api/Hosted/Abstract_Payment_Response.php' );
		require_once( $this->get_plugin_path() . '/includes/api/Hosted/eCheck_Payment_Response.php' );

		// gateway classes
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim-credit-card.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-gateway-authorize-net-cim-echeck.php' );

		// profile classes
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile-handler.php' );
		require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-shipping-address.php' );

		require_once( $this->get_plugin_path() . '/includes/webhooks/class-wc-authorize-net-cim-webhooks.php' );
		require_once( $this->get_plugin_path() . '/includes/webhooks/abstract-wc-authorize-net-cim-webhook.php' );
		require_once( $this->get_plugin_path() . '/includes/webhooks/abstract-wc-authorize-net-cim-customer-webhook.php' );
		require_once( $this->get_plugin_path() . '/includes/webhooks/class-wc-authorize-net-cim-customer-profile-webhook.php' );
		require_once( $this->get_plugin_path() . '/includes/webhooks/class-wc-authorize-net-cim-customer-payment-profile-webhook.php' );

		$this->webhooks = new WC_Authorize_Net_CIM_Webhooks( $this );

		if ( is_admin() ) {

			require_once( $this->get_plugin_path() . '/includes/class-wc-authorize-net-cim-payment-profile-editor.php' );

		// require checkout billing fields for non-US stores, as all European card processors require the billing fields
		// in order to successfully process transactions
		} elseif ( ! strncmp( get_option( 'woocommerce_default_country' ), 'US:', 3 ) ) {

			// remove blank arrays from the state fields, otherwise it's hidden
			add_action( 'woocommerce_states', array( $this, 'tweak_states' ), 1 );

			//  require the billing fields
			add_filter( 'woocommerce_get_country_locale', array( $this, 'require_billing_fields' ), 100 );
		}

		if ( $this->is_emulation_enabled() ) {
			require_once( $this->get_plugin_path() . '/includes/Emulation/Credit_Card.php' );
		}
	}


	/**
	 * Gets the webhooks handler instance.
	 *
	 * @since 2.8.0
	 *
	 * @return \WC_Authorize_Net_CIM_Webhooks
	 */
	public function get_webhooks_instance() {

		return $this->webhooks;
	}


	/**
	 * Determine if TLS v1.2 is required for API requests.
	 *
	 * @see SV_WC_Plugin::require_tls_1_2()
	 *
	 * @since 3.2.0
	 *
	 * @return bool
	 */
	public function require_tls_1_2() {

		return true;
	}


	/** Frontend methods ******************************************************/


	/**
	 * Removes blank State array values from countries.
	 *
	 * Before requiring all billing fields, the state array has to be removed of blank arrays, otherwise
	 * the field is hidden.
	 *
	 * @internal
	 *
	 * @see WC_Countries::__construct()
	 *
	 * @since 2.0.0
	 *
	 * @param array $countries the available countries
	 * @return array the available countries
	 */
	public function tweak_states( $countries ) {

		foreach ( $countries as $country_code => $states ) {

			if ( is_array( $countries[ $country_code ] ) && empty( $countries[ $country_code ] ) ) {
				$countries[ $country_code ] = null;
			}
		}

		return $countries;
	}


	/**
	 * Sets all state billing fields as required.
	 *
	 * This is hooked in when using a European payment processor.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $locales countries and locale-specific address field info
	 * @return array
	 */
	public function require_billing_fields( $locales ) {

		foreach ( $locales as $country_code => $fields ) {

			if ( isset( $locales[ $country_code ]['state']['required'] ) ) {
				$locales[ $country_code ]['state']['required'] = true;
				$locales[ $country_code ]['state']['label']    = $this->get_state_label( $country_code );
			}
		}

		return $locales;
	}


	/**
	 * Gets a label for states that don't have one set by WooCommerce.
	 *
	 * @since 2.6.1
	 *
	 * @param string $country_code the 2-letter country code for the billing country
	 * @return string the label for the "billing state" field at checkout
	 */
	protected function get_state_label( $country_code ) {

		switch( $country_code ) {

			case 'AF':
			case 'AT':
			case 'BI':
			case 'KR':
			case 'PL':
			case 'PT':
			case 'LK':
			case 'SE':
			case 'VN':
				$label = __( 'Province', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'AX':
			case 'YT':
				$label = __( 'Island', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'DE':
				$label = __( 'State', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'EE':
			case 'NO':
				$label = __( 'County', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case 'FI':
			case 'IL':
			case 'LB':
				$label = __( 'District', 'woocommerce-gateway-authorize-net-cim' );
			break;

			default:
				$label = __( 'Region', 'woocommerce-gateway-authorize-net-cim' );
		}

		return $label;
	}


	/** Admin methods ******************************************************/


	/**
	 * Wither to display the thank you admin notice for installing the emulation plugin.
	 *
	 * @since 3.5.0
	 */
	private function maybe_add_emulation_gateway_plugin_installed_admin_notice() {

		if ( 'yes' === get_option( 'wc_authorize_net_emulation_plugin_installed' ) ) {
			$this->add_emulation_gateway_plugin_installed_admin_notice();
		}
	}


	/**
	 * Adds thank you admin notice for installing the emulation plugin.
	 *
	 * @since 3.5.0
	 */
	private function add_emulation_gateway_plugin_installed_admin_notice() {

		$plugin_file = 'woocommerce-gateway-authorize-net-cim/woocommerce-gateway-authorize-net-cim.php';

		$deactivate_link = wp_nonce_url( add_query_arg( [
			'action' => 'deactivate',
			'plugin' => $plugin_file,
		], admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $plugin_file );

		$this->get_admin_notice_handler()->add_admin_notice(
			/** translators: Placeholders: %1$s - <b> tag, %2$s - </b> tag, %3$s - <a> tag, %4$s - </a> tag, %5$s - <a> tag, %6$s - </b> tag */
			sprintf( __( '%1$sThank you for migrating to the Authorize.Net Emulation Gateway plugin!%2$s You may now safely %3$sdeactivate WooCommerce Authorize.Net%4$s. If you need assistance using the new plugin, please %5$scontact support%6$s.', 'woocommerce-gateway-authorize-net-cim' ),
				'<b>', '</b>',
				'<a href="' . $deactivate_link . '">', '</a>',
				'<a target="_blank" href="https://woocommerce.com/my-account/create-a-ticket/">', '</a>'
			), 'emulation-gateway-plugin-installed', [ 'dismissible' => true, 'notice_class' => 'updated' ]
		);
	}


	/**
	 * Tweaks the plugin action links.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @param string[] $actions
	 * @return string[]
	 */
	public function plugin_action_links( $actions ) {

		// get the standard action links
		$actions = parent::plugin_action_links( $actions );

		// don't show the Emulation Gateway enable/disable action links if the emulation gateway was not enabled previously
		if ( null === get_option( 'wc_authorize_net_emulation_previously_enabled', null ) ) {
			return $actions;
		}

		// don't allow merchants to re-enable the Emulation gateway if it was disabled before
		if ( ! $this->is_emulation_enabled() ) {
			return $actions;
		}

		$url   = wp_nonce_url( add_query_arg( 'action', 'wc_authorize_net_toggle_emulation', 'admin.php' ), $this->get_file() );
		$title = esc_html__( 'Disable Emulation Gateway', 'woocommerce-gateway-authorize-net-cim' );

		$actions['toggle_emulation'] = sprintf( '<a href="%1$s" title="%2$s">%2$s</a>', esc_url( $url ), $title );

		return $actions;
	}


	/**
	 * Gets the "Configure Credit Cards" or "Configure eCheck" plugin action links that go
	 * directly to the gateway settings page.
	 *
	 * @see Framework\SV_WC_Payment_Gateway_Plugin::get_settings_url()
	 *
	 * @since 2.0.0
	 *
	 * @param string $gateway_id the gateway ID
	 * @return string plugin configure link
	 */
	public function get_settings_link( $gateway_id = null ) {

		switch ( $gateway_id ) {

			case self::EMULATION_GATEWAY_ID:
				$label = __( 'Configure Emulator', 'woocommerce-gateway-authorize-net-cim' );
			break;

			case self::ECHECK_GATEWAY_ID:
				$label = __( 'Configure eChecks', 'woocommerce-gateway-authorize-net-cim' );
			break;

			default:
				$label = __( 'Configure Credit Cards', 'woocommerce-gateway-authorize-net-cim' );
		}

		return sprintf( '<a href="%s">%s</a>',
			$this->get_settings_url( $gateway_id ),
			$label
		);
	}


	/**
	 * Toggles the emulation gateway.
	 *
	 * @since 3.0.0
	 */
	public function toggle_emulation() {

		try {

			// security check
			if ( ! current_user_can( 'manage_woocommerce' ) || ! wp_verify_nonce( $_GET['_wpnonce'], $this->get_file() ) ) {
				throw new Framework\SV_WC_Plugin_Exception( __( 'You do not have sufficient permissions', 'woocommerce-gateway-authorize-net-cim' ) );
			}

			if ( $this->is_emulation_enabled() ) {
				$message = esc_html__( 'Authorize.Net Emulation is now disabled', 'woocommerce-gateway-authorize-net-cim' );
			} else {
				$message = esc_html__( 'Authorize.Net Emulation is now enabled', 'woocommerce-gateway-authorize-net-cim' );
			}

			update_option( 'wc_authorize_net_emulation_enabled', $this->is_emulation_enabled() ? 'no' : 'yes' );

			$this->get_message_handler()->add_message( $message );

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			$action = $this->is_emulation_enabled() ? _x( 'disable', 'an action for a plugin feature', 'woocommerce-gateway-authorize-net-cim' ) : _x( 'enable', 'an action for a plugin feature', 'woocommerce-gateway-authorize-net-cim' );

			$this->get_message_handler()->add_error(
				sprintf(
					esc_html__( 'Could not %1$s Authorize.Net Emulation. %1$s', 'woocommerce-gateway-authorize-net-cim' ),
					$action,
					$exception->getMessage()
				)
			);
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Adds any necessary admin notices for configuration issues.
	 *
	 * @see Framework\SV_WC_Plugin::add_admin_notices()
	 *
	 * @since 2.0.0
	 */
	public function add_admin_notices() {

		// show any dependency notices
		parent::add_admin_notices();

		if ( $migrated_from = get_option( 'wc_' . $this->get_id() . '_migrated_from_legacy', false ) ) {

			switch ( $migrated_from ) {

				case 'aim': $migrated_from = __( 'Authorize.Net AIM', 'woocommerce-gateway-authorize-net-cim' ); break;
				case 'sim': $migrated_from = __( 'Authorize.Net SIM', 'woocommerce-gateway-authorize-net-cim' ); break;
				case 'dpm': $migrated_from = __( 'Authorize.Net DPM', 'woocommerce-gateway-authorize-net-cim' ); break;
			}

			$message = sprintf(
			/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
				__( 'Migration successful! %1$s was deactivated, and %2$s has been %3$sconfigured with your previous settings%4$s.', 'woocommerce-gateway-authorize-net-cim' ),
				$migrated_from,
				$this->get_plugin_name(),
				'<a href="' . esc_url( $this->get_settings_url() ) . '">', '</a>'
			);

			$this->get_message_handler()->add_message( $message );

			delete_option( 'wc_' . $this->get_id() . '_migrated_from_legacy' );

			// try and enable webhooks if a gateway was migrated with a signature key
			foreach ( $this->get_gateways() as $gateway ) {

				// skip the emulation gateway
				if ( self::EMULATION_GATEWAY_ID === $gateway->get_id() ) {
					continue;
				}

				$gateway->init_settings();
				$gateway->load_settings();

				if ( $gateway->get_api_signature_key() ) {
					$this->get_webhooks_instance()->create_webhooks();
				}
			}
		}

		$this->get_message_handler()->show_messages();

		$settings = get_option( 'woocommerce_authorize_net_cim_credit_card_settings' );

		// install notice
		if ( empty( $settings ) && ! $this->get_admin_notice_handler()->is_notice_dismissed( 'install-notice' ) ) {

			$this->get_admin_notice_handler()->add_admin_notice(
				sprintf( __( 'Thanks for installing the WooCommerce Authorize.Net Gateway! To start accepting payments, %sset your Authorize.Net API credentials%s. Need help? See the %sdocumentation%s.', 'woocommerce-gateway-authorize-net-cim' ),
					'<a href="' . $this->get_settings_url() . '">', '</a>',
					'<a target="_blank" href="' . $this->get_documentation_url() . '">', '</a>'
				), 'install-notice', array( 'notice_class' => 'updated' )
			);
		}

		$this->maybe_add_emulation_gateway_deprecated_admin_notice();

		$this->maybe_add_emulation_gateway_plugin_installed_admin_notice();
	}


	/**
	 * Adds delayed admin notices.
	 *
	 * @since 3.0.0
	 */
	public function add_delayed_admin_notices() {

		parent::add_delayed_admin_notices();

		$this->add_gateway_feature_notices();
	}


	/**
	 * Adds a notice if the CIM feature is disabled.
	 *
	 * @since 3.0.0
	 */
	protected function add_gateway_feature_notices() {

		// loop through each gateway and look for one that's connected & available
		foreach ( $this->get_gateways() as $gateway ) {

			// skip the emulation gateway
			if ( self::EMULATION_GATEWAY_ID === $gateway->get_id() ) {
				continue;
			}

			// if not available (enabled & configured), skip it
			if ( ! $gateway->is_available() ) {
				continue;
			}

			if ( $this->is_payment_gateway_configuration_page( $gateway->get_id() ) ) {

				$message = '';

				// general error for inability to connect
				try {

					$gateway->get_api()->get_merchant_details();

					// check if the configured gateway is missing a client key for Accept.js (required)
					// this shouldn't happen as all accounts should have them generated automatically, but just in case
					if ( ! $gateway->get_client_key() ) {

						$message = sprintf(
						/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
							__( 'Unable to get a Public Client Key from your Authorize.Net account. Please %1$scontact Authorize.Net%2$s to set up your Public Client Key. ', 'woocommerce-gateway-authorize-net-cim' ),
							'<a href="http://support.authorize.net" target="_blank">', '</a>'
						);
					}

				} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

					$message = __( 'Unable to reach your Authorize.Net account. Please double check your API Login ID & Transaction Key to ensure payments can be processed.', 'woocommerce-gateway-authorize-net-cim' );
				}

				if ( $message ) {

					$this->get_admin_notice_handler()->add_admin_notice( $message, $gateway->get_id_dasherized() . '-connection-notice', [
						'dismissible'  => false,
						'notice_class' => 'error',
					] );

					// we're done here until the above is fixed
					break;
				}
			}

			// check if CIM feature is enabled on customer's authorize.net account if tokenization is enabled
			if ( ! get_option( 'wc_authorize_net_cim_feature_enabled' ) && $gateway->supports_tokenization() && $gateway->tokenization_enabled() ) {

				if ( $gateway->is_cim_feature_enabled() ) {

					update_option( 'wc_authorize_net_cim_feature_enabled', true );

				} elseif ( ! $this->get_admin_notice_handler()->is_notice_dismissed( 'cim-add-on-notice' ) ) {

					$message = sprintf(
						/** translators: Placeholders: %1$s - <a> tag, %2$s - </a> tag */
						__( 'The CIM Add-On is not enabled on your Authorize.Net account. Please %1$scontact Authorize.Net%2$s to enable CIM. You will be unable to process transactions until CIM is enabled. ', 'woocommerce-gateway-authorize-net-cim' ),
						'<a href="http://support.authorize.net" target="_blank">', '</a>'
					);

					$this->get_admin_notice_handler()->add_admin_notice( $message, $gateway->get_id_dasherized() . '-cim-add-on-notice' );
				}
			}
		}
	}


	/**
	 * Checks whether to display the emulation gateway deprecated notice.
	 *
	 * @since 3.5.0
	 */
	private function maybe_add_emulation_gateway_deprecated_admin_notice() {

		if ( $this->is_emulation_enabled() && 'yes' === get_option( 'wc_authorize_net_emulation_previously_enabled' ) ) {

			$this->add_emulation_gateway_deprecated_admin_notice();
		}
	}


	/**
	 * Displays the emulation gateway deprecated notice.
	 *
	 * @since 3.5.0
	 */
	private function add_emulation_gateway_deprecated_admin_notice() {

		$deadline = strtotime( '2021-03-31' );
		$url      = wp_nonce_url( add_query_arg( array( 'action' => 'wc_authorize_net_install_emulation_gateway_plugin' ), admin_url() ), 'wc_authorize_net_install_emulation_gateway_plugin' );

		/** translators: Placeholders: %1$s - <em> tag, %2$s - </em> tag, %3$s - a date, %4$s - <a> tag, %4$s - </a> tag */
		$message = sprintf(__( '%1$sHeads up!%2$s We are deprecating support for the emulation gateway in WooCommerce Authorize.Net on %3$s. After that date, the emulation gateway will be removed from WooCommerce Authorize.Net. To keep using the emulation gateway, please migrate to our Authorize.Net Emulation for WooCommerce plugin. We will support this plugin until %4$sthe end of your current WooCommerce Authorize.Net subscription.%5$s', 'woocommerce-gateway-authorize-net-cim' ),
				'<strong>',
				'</strong>',
				'<strong>' . date_i18n( wc_date_format(), $deadline ) . '</strong>',
				'<a href="https://woocommerce.com/my-account/my-subscriptions/" target="_blank">',
				'</a>');

		$message .= '<p style="text-align: right">';
		$message .= '<a href="https://docs.woocommerce.com/document/authorize-net/#emulation-mode" style="margin-right:1rem" target="_blank">' . __( 'Learn more', 'woocommerce-gateway-authorize-net-cim' ) . '</a>';
		$message .= '<a href="' . $url . '" class="button button-primary">' . __( 'Migrate to Authorize.Net Emulation Gateway', 'woocommerce-gateway-authorize-net-cim' ) . '</a>';
		$message .= '</p>';

		$gateway = $this->get_gateway( self::EMULATION_GATEWAY_ID );

		$this->get_admin_notice_handler()->add_admin_notice(
			$message,
			$gateway->get_id_dasherized() . '-cim-emulation-deprecated-notice',
			['dismissible' => false, 'notice_class' => 'notice-warning']
		);
	}


	/**
	 * Displays the admin Shipping Address ID user field.
	 *
	 * @since 2.6.3
	 *
	 * @param \WP_User $user user object
	 */
	public function display_shipping_address_id_field( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$unique_meta_key = '';
		$environments    = array();

		foreach ( $this->get_gateways() as $gateway ) {
			$environments[] = $gateway->get_environment();
		}

		$environments = array_unique( $environments );

		foreach ( $this->get_gateways() as $gateway ) {

			// skip the emulation gateway
			if ( self::EMULATION_GATEWAY_ID === $gateway->get_id() ) {
				continue;
			}

			$meta_key = sprintf( 'wc_%s_shipping_address_id', $this->get_id() );

			if ( ! $gateway->is_production_environment() ) {
				$meta_key .= '_' . $gateway->get_environment();
			}

			// If a field with this meta key has already been set, skip this gateway
			if ( $meta_key === $unique_meta_key ) {
				continue;
			}

			$label = __( 'Shipping Address ID', 'woocommerce-gateway-authorize-net-cim' );

			if ( count( $environments ) > 1 ) {
				$label .= ' (' . $gateway->get_environment_name() . ')';
			}

			$value = get_user_meta( $user->ID, $meta_key, true );

			?>

			<tr>
				<th><label for="<?php esc_attr_e( $meta_key ); ?>"><?php echo esc_html( $label ); ?></label></th>
				<td>
					<input class="regular-text" name="<?php esc_attr_e( $meta_key ); ?>" value="<?php esc_attr_e( $value ); ?>" type="text" /><br/>
					<span class="description"><?php esc_html_e( 'The shipping profile ID for the user. Only edit this if necessary.', 'woocommerce-gateway-authorize-net-cim' ); ?></span>
				</td>
			</tr>

			<?php

			$unique_meta_key = $meta_key;
		}
	}


	/**
	 * Saves the admin Shipping Address ID user field.
	 *
	 * @since 2.6.3
	 *
	 * @param int $user_id user profile ID
	 */
	public function save_shipping_address_id_field( $user_id ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		foreach ( $this->get_gateways() as $gateway ) {

			// skip the emulation gateway
			if ( self::EMULATION_GATEWAY_ID === $gateway->get_id() ) {
				continue;
			}

			$field_name = sprintf( 'wc_%s_shipping_address_id', $this->get_id() );

			if ( ! $gateway->is_production_environment() ) {
				$field_name .= '_' . $gateway->get_environment();
			}

			if ( isset( $_POST[ $field_name ] ) ) {
				update_user_meta( $user_id, $field_name, wc_clean( $_POST[ $field_name ] ) );
			}
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Gets the My Payment Methods handler instance.
	 *
	 * @since 3.2.7
	 *
	 * @return \SkyVerge\WooCommerce\Authorize_Net\My_Payment_Methods
	 */
	protected function get_my_payment_methods_instance() {

		require_once( $this->get_plugin_path() . '/includes/My_Payment_Methods.php' );

		return new \SkyVerge\WooCommerce\Authorize_Net\My_Payment_Methods( $this );
	}


	/**
	 * Builds the Apple Pay instance.
	 *
	 * @since 3.2.7
	 *
	 * @return \SkyVerge\WooCommerce\Authorize_Net\Apple_Pay
	 */
	protected function build_apple_pay_instance() {

		require_once( $this->get_plugin_path() . '/includes/Apple_Pay.php' );
		require_once( $this->get_plugin_path() . '/includes/Apple_Pay/Frontend.php' );

		return new \SkyVerge\WooCommerce\Authorize_Net\Apple_Pay( $this );
	}


	/**
	 * Initializes the REST API handler.
	 *
	 * @since 3.0.0
	 */
	protected function init_rest_api_handler() {

		require_once( $this->get_payment_gateway_framework_path() . '/rest-api/class-sv-wc-payment-gateway-plugin-rest-api.php' );
		require_once( $this->get_plugin_path() . '/includes/Handlers/REST_API.php' );

		$this->rest_api_handler = new SkyVerge\WooCommerce\Authorize_Net\Handlers\REST_API( $this );
	}


	/**
	 * Gets the main plugin instance.
	 *
	 * Ensures only one instance is/can be loaded.
	 *
	 * @see wc_authorize_net_cim()
	 *
	 * @since 1.4.0
	 *
	 * @return \WC_Authorize_Net_CIM
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Gets the plugin documentation URL.
	 *
	 * @see Framework\SV_WC_Plugin::get_documentation_url()
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_documentation_url() {
		return 'https://docs.woocommerce.com/document/authorize-net-cim/';
	}


	/**
	 * Gets the plugin support URL.
	 *
	 * @see Framework\SV_WC_Plugin::get_support_url()
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_support_url() {

		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Gets the plugin sales page URL.
	 *
	 * @since 2.10.0
	 *
	 * @return string
	 */
	public function get_sales_page_url() {

		return 'https://woocommerce.com/products/authorize-net-cim/';
	}


	/**
	 * Gets the plugin name, localized.
	 *
	 * @see Framework\SV_WC_Plugin::get_plugin_name()
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Authorize.Net Gateway', 'woocommerce-gateway-authorize-net-cim' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @see Framework\SV_WC_Plugin::get_file()
	 *
	 * @since 1.1.0
	 *
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle methods *****/


	protected function init_lifecycle_handler() {

		require_once( $this->get_plugin_path() . '/includes/Lifecycle.php' );

		$this->lifecycle_handler = new \SkyVerge\WooCommerce\Authorize_Net\CIM\Lifecycle( $this );
	}


} // end WC_Authorize_Net_CIM


/**
 * Returns the One True Instance of Authorize.Net.
 *
 * @since 1.4.0
 *
 * @return \WC_Authorize_Net_CIM
 */
function wc_authorize_net_cim() {
	return WC_Authorize_Net_CIM::instance();
}
