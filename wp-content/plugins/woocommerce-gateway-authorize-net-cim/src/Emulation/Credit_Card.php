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
 * Authorize.Net Emulation Gateway class (credit cards only).
 *
 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
 *
 * @since 3.0.0
 * @deprecated 3.6.0
 */
class Credit_Card extends Framework\SV_WC_Payment_Gateway_Direct {


	/**
	 * Initialize the gateway.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 */
	public function __construct() {

		wc_deprecated_function( __CLASS__, '3.6.0' );
	}


	/**
	 * Gets an array of form fields specific for this method.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return array
	 */
	protected function get_method_form_fields() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return [];
	}


	/**
	 * Determines if the gateway is properly configured to perform transactions.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @return bool
	 */
	public function is_configured() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Gets the API Login ID based on the current environment.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string the API login ID to use
	 */
	public function get_api_login_id( $environment_id = null ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


	/**
	 * Gets the API Transaction Key based on the current environment.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string the API transaction key to use
	 */
	public function get_api_transaction_key( $environment_id = null ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


	/**
	 * Gets the payment gateway URL based on the current environment.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.0.0
	 * @deprecated 3.6.0
	 *
	 * @param string|null $environment_id either 'production' or 'test'
	 * @return string payment gateway URL
	 */
	public function get_gateway_url( $environment_id = null ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return '';
	}


}
