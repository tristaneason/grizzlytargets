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
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Authorize_Net\Emulation\API;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Authorize.Net Emulation Response Class.
 *
 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
 *
 * @since 3.0.0
 * @deprecated 3.6.0
 */
class Response implements Framework\SV_WC_Payment_Gateway_API_Response, Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/**
	 * Constructs the class.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string $raw_response raw response data
	 */
	public function __construct( $raw_response ) {

		wc_deprecated_function( __CLASS__, '3.6.0' );
	}


	/**
	 * Parses the response string and set the parsed response object.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 */
	protected function parse_response() {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Determines if the transaction was successful.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return bool
	 */
	public function transaction_approved() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Determines if the transaction was held.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return bool
	 */
	public function transaction_held() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Gets the response transaction ID.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_transaction_id() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the transaction status message.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_status_message() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the transaction status code.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_status_code() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the transaction authorization code.
	 *
	 * This is returned from the credit card processor to indicate that the charge will be paid by the card issuer.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_authorization_code() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the result of the AVS check.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_avs_result() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the result of the CSC check.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_csc_result() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Determines if the CSC check was successful.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return bool
	 */
	public function csc_match() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Gets the result of the CAVV (Cardholder authentication verification) check.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string|null
	 */
	public function get_cavv_result() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets a message appropriate for a frontend user.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string
	 */
	public function get_user_message() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


	/**
	 * Gets the payment type: 'credit-card', 'echeck', etc.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string payment type or null if not available
	 */
	public function get_payment_type() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return 'credit-card';
	}


	/**
	 * Gets the string representation of the response.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string
	 */
	public function to_string() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


	/**
	 * Gets the string representation of the response.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string
	 */
	public function to_string_safe() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


}

