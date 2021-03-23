<?php

namespace Objectiv\Plugins\Checkout\Action;

use Objectiv\BoosterSeat\Base\Action;

/**
 * Class CompleteOrderAction
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Action
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class CompleteOrderAction extends Action {

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
	 * Takes in the information from the order form and hands it off to Woocommerce.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function action() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		// If the user is logged in don't try and get the user from the front end, just get it on the back before we checkout
		if ( ! isset( $_POST['billing_email'] ) || ! $_POST['billing_email'] ) {
			$current_user = wp_get_current_user();
			if ( $current_user ) {
				$_POST['billing_email'] = $current_user->user_email;
			}
		}

		do_action( 'cfw_before_process_checkout' );

		WC()->checkout()->process_checkout();
	}
}
