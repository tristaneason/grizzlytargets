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

use SkyVerge\WooCommerce\PluginFramework\v5_10_4 as Framework;


/**
 * Authorize.Net Transaction Response Class
 *
 * Parses XML received from CIM Profile Transaction requests, the general response body looks like:
 *
 * <?xml version="1.0" encoding="utf-8"?>
 * <createCustomerProfileTransactionResponse xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">
 *  	<messages>
 *  		<resultCode>Ok</resultCode>
 *  		<message>
 *  			<code>I00001</code>
 *  			<text>Successful.</text>
 *  		</message>
 *  	</messages>
 *  	<directResponse>1,1,1,This transaction has been approved.,000000,Y,2000000001,INV000001,description of
 *  		transaction,10.95,CC,auth_capture,custId123,John,Doe,,123 Main
 *  		St.,Bellevue,WA,98004,USA,000-000-0000,,mark@example.com,John,Doe,,123
 *  		Main St.,Bellevue,WA,98004,USA,1.00,0.00,2.00,FALSE,PONUM000001,
 *  		D18EB6B211FE0BBF556B271FDA6F92EE,M,2,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 *  	</directResponse>
 * </createCustomerProfileTransactionResponse>
 *
 * @link http://developer.authorize.net/api/reference/#payment-transactions-charge-a-credit-card
 * @link http://www.authorize.net/support/CIM_guide_XML.pdf
 *
 * @since 2.0.0
 * @see Framework\SV_WC_Payment_Gateway_API_Response
 */
class WC_Authorize_Net_CIM_API_Profile_Transaction_Response extends WC_Authorize_Net_CIM_API_Profile_Response implements Framework\SV_WC_Payment_Gateway_API_Response, Framework\SV_WC_Payment_Gateway_API_Authorization_Response {


	/**
	 * Checks if the transaction was successful. Note that this overrides the
	 * standard behavior for profile responses (customer profiles & payment profiles)
	 * which additional considers held transactions as approved because the
	 * profile is created regardless of whether it's flagged for AVS/CVV rules.
	 *
	 * For profile *transactions* however, only an approval is an approval :)
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_API_Response::transaction_approved()
	 * @return bool true if approved, false otherwise
	 */
	public function transaction_approved() {

		return ! $this->has_api_error() && ( self::TRANSACTION_APPROVED === $this->get_transaction_response_code() );
	}


}
