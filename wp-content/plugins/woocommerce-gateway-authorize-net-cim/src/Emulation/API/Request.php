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
 * Authorize.Net Emulation API Request Class.
 *
 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
 *
 * @since 3.0.0
 * @deprecated 3.6.0
 */
class Request implements Framework\SV_WC_Payment_Gateway_API_Request {


	/**
	 * Constructs request object.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string $api_login_id API login ID
	 * @param string $api_transaction_key API transaction key
	 */
	public function __construct( $api_login_id, $api_transaction_key ) {

		wc_deprecated_function( __CLASS__, '3.6.0' );
	}


	/**
	 * Creates a credit card charge request.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order the order object
	 */
	public function create_credit_card_charge( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Creates a credit card auth request.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order the order object
	 */
	public function create_credit_card_auth( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Captures funds for a previous credit card authorization.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order the order object
	 */
	public function create_credit_card_capture( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Adds line items to the request.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return array
	 */
	protected function get_line_items() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return [];
	}


	/**
	 * Gets the request data.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return array
	 */
	public function get_data() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return [];
	}


	/**
	 * Gets the string representation of the request.
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
	 * Gets the string representation of this request with any and all sensitive elements masked or removed.
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


	/**
	 * Gets the order associated with this request, if there was one.
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return \WC_Order
	 */
	public function get_order() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the request method.
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string
	 */
	public function get_method() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return 'POST';
	}


	/**
	 * Gets the request path.
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return string
	 */
	public function get_path() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


	/**
	 * Gets the request parameters.
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return array
	 */
	public function get_params() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return [];
	}


}
