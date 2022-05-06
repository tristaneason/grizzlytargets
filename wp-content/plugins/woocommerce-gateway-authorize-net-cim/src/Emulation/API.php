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

namespace SkyVerge\WooCommerce\Authorize_Net\Emulation;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Authorize.Net AIM Emulation API Class.
 *
 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
 *
 * @since 3.0.0
 * @deprecated 3.6.0
 */
class API extends Framework\SV_WC_API_Base implements Framework\SV_WC_Payment_Gateway_API {


	/**
	 * Constructs the class.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param Credit_Card $gateway instance
	 */
	public function __construct( $gateway ) {

		wc_deprecated_function( __CLASS__, '3.6.0' );
	}


	/**
	 * Creates a new credit card charge transaction.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order order object
	 * @return null
	 */
	public function credit_card_charge( \WC_Order $order ) {

		return null;
	}


	/**
	 * Creates a new credit card auth transaction.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order order object
	 * @return null
	 */
	public function credit_card_authorization( \WC_Order $order ) {

		wc_deprecated_function( __CLASS__, '3.6.0' );

		return null;
	}


	/**
	 * Captures funds for a credit card authorization.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order order object
	 * @return null
	 */
	public function credit_card_capture( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}

	/**
	 * Authorize.Net Emulation does not support getting tokenized payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return false
	 */
	public function supports_get_tokenized_payment_methods() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Authorize.Net Emulation does not support removing tokenized payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return false
	 */
	public function supports_remove_tokenized_payment_method() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Determines if this API supports updating tokenized payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return false
	 */
	public function supports_update_tokenized_payment_method() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Builds and returns a new API request object.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param array $type
	 * @return API\Request
	 */
	protected function get_new_request( $type = array() ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return new API\Request( '', '' );
	}


	/**
	 * Gets the order associated with the request, if any.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return \WC_Order|null
	 */
	public function get_order() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return null;
	}


	/**
	 * Gets the main plugin instance.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return \WC_Authorize_Net_CIM
	 */
	protected function get_plugin() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return wc_authorize_net_cim();
	}


	/**
	 * No-op, as emulation does not support refund transactions.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function refund( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * No-op, as emulation does not support void transactions.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function void( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * No-op, as emulation does not support eCheck transactions.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function check_debit( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Authorize.Net Emulation does not support tokenizing payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order order object
	 */
	public function tokenize_payment_method( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Authorize.Net Emulation does not support removing tokenized payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string $token payment method token
	 * @param string $customer_id unique customer ID
	 */
	public function remove_tokenized_payment_method( $token, $customer_id ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Authorize.Net Emulation does not support getting tokenized payment methods.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string $customer_id unique customer ID
	 */
	public function get_tokenized_payment_methods( $customer_id ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * No-op: Authorize.Net does not support tokenization.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param \WC_Order $order
	 */
	public function update_tokenized_payment_method( \WC_Order $order ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


}
