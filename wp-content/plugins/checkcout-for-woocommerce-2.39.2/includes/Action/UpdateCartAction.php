<?php

namespace Objectiv\Plugins\Checkout\Action;

use Objectiv\BoosterSeat\Base\Action;
use Objectiv\Plugins\Checkout\Main;

/**
 * Class LogInAction
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Action
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class UpdateCartAction extends Action {

	/**
	 * LogInAction constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param $id
	 */
	public function __construct( $id, $no_privilege, $action_prefix ) {
		parent::__construct( $id, $no_privilege, $action_prefix );
	}

	/**
	 * Logs in the user based on the information passed. If information is incorrect it returns an error message
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function action() {
		// Used to tell checkout to redirect somewhere
		$redirect = false;

		$cart_editing_enabled = Main::instance()->get_settings_manager()->get_setting( 'enable_cart_editing' );

		if ( $cart_editing_enabled == 'yes' ) {
			/**
			 * Cart Updates
			 */
			if ( isset( $_POST['cart'] ) ) {
				foreach ( $_POST['cart'] as $cart_item_key => $value ) {
					WC()->cart->set_quantity( $cart_item_key, $value['qty'], false );

					// Remove items from the cart contents
					// Ensures things like subscriptions update their output properly
					if ( 0 == $value['qty'] ) {
						WC()->cart->remove_cart_item( $cart_item_key );
					}
				}
			}

			if ( WC()->cart->get_cart_contents_count() == 0 ) {
				if ( false === apply_filters( 'cfw_cart_edit_redirect_suppress_notice', false ) ) {
					wc_add_notice( cfw__( 'Checkout is not available whilst your cart is empty.', 'woocommerce' ), 'notice' );
				}

				$cart_editing_redirect_url = Main::instance()->get_settings_manager()->get_setting( 'cart_edit_empty_cart_redirect' );

				if ( empty( $cart_editing_redirect_url ) ) {
					$redirect = wc_get_cart_url();
				} else {
					$redirect = $cart_editing_redirect_url;
				}
			}
		}

		WC()->cart->calculate_totals();

		$this->out(
			array(
				'redirect' => $redirect,
			)
		);
	}
}
