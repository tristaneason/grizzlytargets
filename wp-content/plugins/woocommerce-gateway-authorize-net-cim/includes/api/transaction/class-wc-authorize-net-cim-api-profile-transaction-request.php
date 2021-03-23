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
 * Authorize.Net API Request Class
 *
 * Generates XML for CIM profile transaction requests, used when a logged-in (or new)
 * customer has opted to save their payment method
 *
 * @since 2.0.0
 */
class WC_Authorize_Net_CIM_API_Profile_Transaction_Request extends WC_Authorize_Net_CIM_API_Transaction_Request  {


	/**
	 * Construct request object, overrides parent to set the request type for
	 * every request in the class, as all profile transactions use the same
	 * root element
	 *
	 * @since 2.0.0
	 * @see WC_Authorize_Net_CIM_API_Request::__construct()
	 * @param string $api_login_id API login ID
	 * @param string $api_transaction_key API transaction key
	 */
	public function __construct( $api_login_id, $api_transaction_key ) {

		parent::__construct( $api_login_id, $api_transaction_key );

		$this->request_type = 'createCustomerProfileTransactionRequest';
	}


	/**
	 * Create the transaction XML for profile auth-only/auth-capture transactions -- this
	 * handles both credit cards and eChecks
	 *
	 * @since 2.0.0
	 * @param string $type transaction type
	 */
	protected function create_transaction( $type ) {

		$transaction_type = ( $type === 'auth_only' ) ? 'profileTransAuthOnly' : 'profileTransAuthCapture';

		$this->request_data = array(
			'refId'        => $this->order->get_id(),
			'transaction'  => array(
				$transaction_type => array(
					'amount'                    => $this->order->payment_total,
					'tax'                       => $this->get_taxes(),
					'shipping'                  => $this->get_shipping(),
					'lineItems'                 => $this->get_line_items(),
					'customerProfileId'         => $this->order->customer_id,
					'customerPaymentProfileId'  => $this->order->payment->token,
					'customerShippingAddressId' => $this->order->payment->shipping_address_id,
					'order'                     => array(
						'invoiceNumber'       => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-cim' ) ),
						'description'         => Framework\SV_WC_Helper::str_truncate( $this->order->description, 255 ),
						'purchaseOrderNumber' => Framework\SV_WC_Helper::str_truncate( preg_replace( '/\W/', '', $this->order->payment->po_number ), 25 ),
					),
					'cardCode'                  => ! empty( $this->order->payment->csc ) ? $this->order->payment->csc : null,
				)
			),
			'extraOptions' => $this->get_extra_options(),
		);
	}


	/**
	 * Capture funds for a previous credit card authorization
	 *
	 * @since 2.0.0
	 * @param WC_Order $order the order object
	 */
	public function create_credit_card_capture( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'refId'       => $this->order->get_id(),
			'transaction' => array(
				'profileTransPriorAuthCapture' => array(
					'amount'  => $order->capture->amount,
					'transId' => $order->capture->trans_id,
				),
			),
			'extraOptions' => $this->get_extra_options(),
		);
	}


	/** Create a refund for the given $order
	 *
	 * @since 2.0.0
	 * @param WC_Order $order order object
	 */
	public function create_refund( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'refId'       => $this->order->get_id(),
			'transaction' => array(
				'profileTransRefund' => array(
					'amount'                   => $order->refund->amount,
					'customerProfileId'        => $order->refund->customer_profile_id,
					'customerPaymentProfileId' => $order->refund->customer_payment_profile_id,
					'order'                    => array(
						'invoiceNumber' => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-cim' ) ),
						'description'   => Framework\SV_WC_Helper::str_truncate( $this->order->refund->reason, 255 ),
					),
					'transId' => $order->refund->trans_id,
				),
			),
			'extraOptions' => $this->get_extra_options(),
		);
	}


	/** Create a void for the given $order
	 *
	 * @since 2.0.0
	 * @param WC_Order $order order object
	 */
	public function create_void( WC_Order $order ) {

		$this->order = $order;

		$this->request_data = array(
			'refId'       => $this->order->get_id(),
			'transaction' => array(
				'profileTransVoid' => array(
					'transId' => $order->refund->trans_id,
				),
			),
			'extraOptions' => $this->get_extra_options(),
		);
	}


	/**
	 * Get extra options for the CIM transaction.
	 *
	 * Extra options are fields that auth.net accepts but aren't part of the CIM API
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_extra_options() {

		$options = array(
			'x_solution_id'      => 'A1000065',
			'x_customer_ip'      => $this->order->get_customer_ip_address(),
			'x_currency_code'    => $this->order->get_currency(),
			// TODO: this can be improved by detecting certain failure conditions (AVS/CVV failures) and dynamically setting the duplicate window to 0 as needed @MR
			'x_duplicate_window' => 0,
			'x_delim_char' => '|',
			'x_encap_char' => ':',
		);

		return http_build_query( $options, '', '&' );
	}


}
