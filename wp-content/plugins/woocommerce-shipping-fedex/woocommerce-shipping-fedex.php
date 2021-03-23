<?php
/**
 * Plugin Name: WooCommerce FedEx Shipping
 * Plugin URI: https://woocommerce.com/products/fedex-shipping-module/
 * Description: Obtain shipping rates dynamically via the FedEx API for your orders.
 * Version: 3.4.33
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * WC requires at least: 2.6
 * WC tested up to: 4.4
 * Tested up to: 5.5
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Developers: https://www.fedex.com/wpor/web/jsp/drclinks.jsp?links=wss/index.html
 * Woo: 18620:1a48b598b47a81559baadef15e320f64
 *
 * @package woocommerce-shipping-fedex
 */

define( 'WC_SHIPPING_FEDEX_VERSION', '3.4.33' ); // WRCS: DEFINED_VERSION.

/**
 * Main plugin class.
 */
class WC_Shipping_Fedex_Init {
	/**
	 * Plugin's version.
	 *
	 * @since 3.4.0
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the plugin's public actions.
	 */
	public function __construct() {
		$this->version = WC_SHIPPING_FEDEX_VERSION;

		if ( class_exists( 'WC_Shipping_Method' ) && class_exists( 'SoapClient' ) ) {
			add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'includes' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
			add_action( 'admin_notices', array( $this, 'environment_check' ) );
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
			add_action( 'wp_ajax_fedex_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );

			$fedex_settings = get_option( 'woocommerce_fedex_settings', array() );

			if ( isset( $fedex_settings['freight_enabled'] ) && 'yes' === $fedex_settings['freight_enabled'] ) {
				// Make the city field show in the calculator (for freight).
				add_filter( 'woocommerce_shipping_calculator_enable_city', '__return_true' );

				// Add freight class option for shipping classes (for freight).
				if ( is_admin() ) {
					include __DIR__ . '/includes/class-wc-fedex-freight-mapping.php';
				}
			}
		} else {
			add_action( 'admin_notices', array( $this, 'wc_deactivated' ) );
		}
	}

	/**
	 * Environment check function.
	 */
	public function environment_check() {

		$messages = array();

		// Currency check.
		if ( apply_filters( 'woocommerce_shipping_fedex_check_store_currency', true ) && ! in_array( get_woocommerce_currency(), array( 'USD', 'CAD' ), true ) ) {
			$messages[] = esc_html__( 'WooCommerce currency is set to US Dollars or CA Dollars', 'woocommerce-shipping-fedex' );
		}

		// Country check.
		if ( ! in_array( WC()->countries->get_base_country(), array( 'US', 'CA' ), true ) ) {
			$messages[] = esc_html__( 'Base country/region is set to United States or Canada', 'woocommerce-shipping-fedex' );
		}

		if ( ! empty( $messages ) ) {
			$prefix         = esc_html__( 'FedEx requires that %s', 'woocommerce-shipping-fedex' );
			$separator      = __( ' and ', 'woocommerce-shipping-fedex' );

			echo '<div class="error">
				<p>' . sprintf( $prefix, implode( $separator, $messages ) ) . '</p>
			</div>';
		}
	}

	/**
	 * Load includes.
	 *
	 * @since 3.4.0
	 * @version 3.4.0
	 * @return void
	 */
	public function includes() {
		include_once __DIR__ . '/includes/class-wc-fedex-privacy.php';
			include_once __DIR__ . '/includes/class-wc-shipping-fedex.php';
	}

	/**
	 * Add Fedex shipping method to WC.
	 *
	 * @param mixed $methods Shipping methods.
	 * @return array
	 */
	public function add_method( $methods ) {
			$methods['fedex'] = 'WC_Shipping_Fedex';
		return $methods;
	}

	/**
	 * Localisation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-fedex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Plugin page links.
	 *
	 * @version 3.4.9
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array Plugin action links.
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=fedex' ) . '">' . __( 'Settings', 'woocommerce-shipping-fedex' ) . '</a>',
			'<a href="https://support.woocommerce.com/">' . __( 'Support', 'woocommerce-shipping-fedex' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/fedex/">' . __( 'Docs', 'woocommerce-shipping-fedex' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * WooCommerce not installed notice.
	 */
	public function wc_deactivated() {
		if ( ! class_exists( 'SoapClient' ) ) {
			echo '<div class="error"><p>' . esc_html__( 'Your server does not provide SOAP support which is required functionality for communicating with FedEx. You will need to reach out to your web hosting provider to get information on how to enable this functionality on your server.', 'woocommerce-shipping-fedex' ) . '</p></div>';
		}

		if ( ! class_exists( 'WC_Shipping_Method' ) ) {
			/* translators: %s: WooCommerce link */
			echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce FedEx Shipping requires %s to be installed and active.', 'woocommerce-shipping-fedex' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>' ) . '</p></div>';
		}
	}

	/**
	 * See if we need to install any upgrades
	 * and call the install.
	 *
	 * @since 3.4.0
	 * @version 3.4.0
	 * @return bool
	 */
	public function maybe_install() {
		// Only need to do this for versions less than 3.4.0 to migrate
		// settings to shipping zone instance.
		if ( ! defined( 'DOING_AJAX' )
			&& ! defined( 'IFRAME_REQUEST' )
			&& version_compare( get_option( 'wc_fedex_version' ), '3.4.0', '<' ) ) {

			$this->install();

		}

		return true;
	}

	/**
	 * Update/migration script.
	 *
	 * @since 3.4.0
	 * @version 3.4.0
	 */
	public function install() {
		// Get all saved settings and cache it.
		$fedex_settings = get_option( 'woocommerce_fedex_settings', false );

		// Settings exists.
		if ( $fedex_settings ) {
			global $wpdb;

			// Unset un-needed settings.
			unset( $fedex_settings['enabled'] );
			unset( $fedex_settings['availability'] );
			unset( $fedex_settings['countries'] );

			// Add it to the "rest of the world" zone when no fedex.
			if ( ! $this->is_zone_has_fedex( 0 ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'fedex', 1, 1 ) );
				// Add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_fedex_' . $instance . '_settings', $fedex_settings );
			}

			update_option( 'woocommerce_fedex_show_upgrade_notice', 'yes' );
		}

		update_option( 'wc_fedex_version', $this->version );
	}

	/**
	 * Show the user a notice for plugin updates.
	 *
	 * @since 3.4.0
	 */
	public function upgrade_notice() {
		$show_notice = get_option( 'woocommerce_fedex_show_upgrade_notice' );

		if ( 'yes' !== $show_notice ) {
			return;
		}

		$query_args      = array(
			'page' => 'wc-settings',
			'tab'  => 'shipping',
		);
		$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
		?>
		<div class="notice notice-success is-dismissible wc-fedex-notice">
			<p>
			<?php
				/* translators: %1$s: Shipping zones link start, %2$s: Link end */
				echo sprintf( esc_html__( 'FedEx now supports shipping zones. The zone settings were added to a new FedEx method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-shipping-fedex' ), '<a href="' . esc_url( $zones_admin_url ) . '">', '</a>' );
			?>
			</p>
		</div>

		<script type="application/javascript">
			jQuery( '.notice.wc-fedex-notice' ).on( 'click', '.notice-dismiss', function () {
				wp.ajax.post('fedex_dismiss_upgrade_notice');
			});
		</script>
		<?php
	}

	/**
	 * Turn of the dismisable upgrade notice.
	 *
	 * @since 3.4.0
	 */
	public function dismiss_upgrade_notice() {
		update_option( 'woocommerce_fedex_show_upgrade_notice', 'no' );
	}

	/**
	 * Helper method to check whether given zone_id has fedex method instance.
	 *
	 * @since 3.4.0
	 *
	 * @param int $zone_id Zone ID.
	 *
	 * @return bool True if given zone_id has fedex method instance.
	 */
	public function is_zone_has_fedex( $zone_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'fedex' AND zone_id = %d", $zone_id ) ) > 0;
	}
}

add_action( 'plugins_loaded', 'WC_Shipping_Fedex_Init::get_instance' );
