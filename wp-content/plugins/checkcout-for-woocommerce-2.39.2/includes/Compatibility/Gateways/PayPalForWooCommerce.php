<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Gateways;

use Closure;
use Objectiv\Plugins\Checkout\Compatibility\Base;
use Objectiv\Plugins\Checkout\Main;

class PayPalForWooCommerce extends Base {

	private $_CompatibilityManager;
	private $gateway_instance;

	public function __construct( $CompatibilityManager ) {
		parent::__construct();

		$this->_CompatibilityManager = $CompatibilityManager;
	}

	public function is_available() {
		return class_exists( '\\Angelleye_PayPal_Express_Checkout_Helper' );
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'PayPalForWooCommerce',
			'params' => [],
		];

		return $compatibility;
	}

	public function run() {
		if ( version_compare( VERSION_PFW, '1.5.7', '>=' ) ) {
			$Angelleye_PayPal_Express_Checkout_Helper = \Angelleye_PayPal_Express_Checkout_Helper::instance();

			add_filter( 'angelleye_ec_checkout_page_buy_now_nutton', array( $this, 'modify_payment_button_output' ), 10, 1 );
			add_action( 'cfw_payment_request_buttons', array( $this, 'add_paypal_express_to_checkout' ) );

			// Remove top of checkout message
			remove_action( 'woocommerce_before_checkout_form', array( $Angelleye_PayPal_Express_Checkout_Helper, 'checkout_message' ), 5 );
		}
	}

	function modify_payment_button_output( $button_output ) {
		$content_strings_to_remove = [
			'<div style="clear:both; margin-bottom:10px;"></div>',
			'<div class="clear"></div>',
		];

		// Remove unwanted strings
		foreach ( $content_strings_to_remove as $content_str ) {
			$button_output = str_replace( $content_str, '', $button_output );
		}

		return $button_output;
	}

	function add_paypal_express_to_checkout() {
		// This is required because it's used down below in anonymous functions
		global $Angelleye_PayPal_Express_Checkout_Helper;

		if ( Main::is_checkout() ) {

			$gateways = \WC_Payment_Gateways::instance()->payment_gateways();

			if ( isset( $gateways['paypal_express'] ) && class_exists( '\\AngellEYE_Gateway_Paypal' ) ) {
				$this->gateway_instance = $gateways['paypal_express'];
			} else {
				return;
			}

			$Angelleye_PayPal_Express_Checkout_Helper = \Angelleye_PayPal_Express_Checkout_Helper::instance();

			if ( ! empty( $Angelleye_PayPal_Express_Checkout_Helper ) && ! empty($Angelleye_PayPal_Express_Checkout_Helper->show_on_checkout) && ( $Angelleye_PayPal_Express_Checkout_Helper->show_on_checkout == 'top' || $Angelleye_PayPal_Express_Checkout_Helper->show_on_checkout == 'both' ) ) {
				add_action(
					'cfw_checkout_after_payment_methods', function () {
						global $Angelleye_PayPal_Express_Checkout_Helper;

						echo '<p class="paypal-cancel-wrapper">' . $Angelleye_PayPal_Express_Checkout_Helper->angelleye_woocommerce_order_button_html( '' ) . '</p>';
					}
				);

				$Angelleye_PayPal_Express_Checkout_Helper->checkout_message();

				if ( empty( $Angelleye_PayPal_Express_Checkout_Helper ) ) {
					return;
				}

				if ( ! $Angelleye_PayPal_Express_Checkout_Helper->function_helper->ec_is_express_checkout() ) {
					add_action( 'cfw_checkout_before_customer_info_tab', array( $this, 'add_separator' ), 10 );
				} else {
					add_action( 'cfw_checkout_before_customer_info_tab', array( $this, 'add_notice' ), 10 );
				}
			}
		}
	}

	function add_notice() {
		?>
		<div class="woocommerce-info">
			<?php _e( 'Logged in with PayPal. Please continue your order below.', 'checkout-wc' ); ?>
		</div>
		<?php
	}
}
