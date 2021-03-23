<?php

namespace Objectiv\Plugins\Checkout\Action;

use Objectiv\BoosterSeat\Base\Action;

/**
 * Class AccountExistsAction
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Action
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class AccountExistsAction extends Action {

	/**
	 * AccountExistsAction constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param $id
	 */
	public function __construct( $id, $no_privilege, $action_prefix ) {
		parent::__construct( $id, $no_privilege, $action_prefix );
	}

	/**
	 * Checks whether the account exists on the website or not
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function action() {
		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';

		$this->out(
			array(
				'account_exists' => (boolean) email_exists( $email ),
			)
		);
	}
}
