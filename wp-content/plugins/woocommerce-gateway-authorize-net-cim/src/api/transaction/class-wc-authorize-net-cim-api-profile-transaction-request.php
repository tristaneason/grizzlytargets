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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;


/**
 * Authorize.Net API Request Class
 *
 * Generates XML for CIM profile transaction requests, used when a logged-in (or new)
 * customer has opted to save their payment method
 *
 * @since 2.0.0
 */
class WC_Authorize_Net_CIM_API_Profile_Transaction_Request extends WC_Authorize_Net_CIM_API_Transaction_Request  {


	/** auth/capture transaction type */
	const AUTH_CAPTURE = 'authCaptureTransaction';

	/** authorize only transaction type */
	const AUTH_ONLY = 'authOnlyTransaction';

	/** prior auth-only capture transaction type */
	const PRIOR_AUTH_CAPTURE = 'priorAuthCaptureTransaction';

	/** refund transaction type */
	const REFUND = 'refundTransaction';

	/** void transaction type */
	const VOID = 'voidTransaction';

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

		$this->request_type = 'createTransactionRequest';
	}


	/**
	 * Create the transaction XML for profile auth-only/auth-capture transactions -- this
	 * handles both credit cards and eChecks
	 *
	 * @since 2.0.0
	 * @param string $type transaction type
	 */
	protected function create_transaction( $type ) {

		$transaction_type = ( $type === 'auth_only' ) ? self::AUTH_ONLY : self::AUTH_CAPTURE;

		$request_data = array(
			'refId'              => $this->order->get_id(),
			'transactionRequest' => array(
				'transactionType'    => $transaction_type,
				'amount'             => $this->order->payment_total,
				'currencyCode'       => $this->order->get_currency(),
				'profile'            => array(
					'customerProfileId'    => $this->order->customer_id,
					'paymentProfile'       => array(
						'paymentProfileId' => $this->order->payment->token,
					),
				),
				'payment'           => array(
					'creditCard' => array(
						'cardCode' => ! empty( $this->order->payment->csc ) ? $this->order->payment->csc : null,
					),
				),
				'order'             => array(
					'invoiceNumber'       => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-cim' ) ),
					'description'         => Framework\SV_WC_Helper::str_truncate( $this->order->description, 255 ),
					'purchaseOrderNumber' => Framework\SV_WC_Helper::str_truncate( preg_replace( '/\W/', '', $this->order->payment->po_number ), 25 ),
				),
				'lineItems'         => $this->get_line_items(),
				'tax'               => $this->get_taxes(),
				'shipping'          => $this->get_shipping(),
				'poNumber'          => Framework\SV_WC_Helper::str_truncate( preg_replace( '/\W/', '', $this->order->payment->po_number ), 25 ),
				'customerIP'        => $this->order->get_customer_ip_address(),
				'processingOptions' => $this->get_processing_options()
			),
		);

		$this->request_data = $this->set_sub_auth_info( $transaction_type, $request_data);
	}


	/**
	 * Adds order line items to the request.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_line_items() {

		$line_items = array();

		// order line items
		foreach ( Framework\SV_WC_Helper::get_order_line_items( $this->order ) as $item ) {

			if ( $item->item_total >= 0 ) {

				$line_items['lineItem'][] = array(
					'itemId'      => Framework\SV_WC_Helper::str_truncate( $item->id, 31 ),
					'name'        => Framework\SV_WC_Helper::str_to_sane_utf8( Framework\SV_WC_Helper::str_truncate( htmlentities( $item->name, ENT_QUOTES, 'UTF-8', false ), 31 ) ),
					'description' => Framework\SV_WC_Helper::str_to_sane_utf8( Framework\SV_WC_Helper::str_truncate( htmlentities( $item->description, ENT_QUOTES, 'UTF-8', false ), 255 ) ),
					'quantity'    => $item->quantity,
					'unitPrice'   => Framework\SV_WC_Helper::number_format( $item->item_total ),
					'taxable'     => 'taxable' === $item->item->get_tax_status(),
				);
			}
		}

		// order fees
		foreach ( $this->order->get_fees() as $fee_id => $fee ) {

			/** @var \WC_Order_Item_Fee $fee object */
			if ( $this->order->get_item_total( $fee ) >= 0 ) {

				$line_items['lineItem'][] = array(
					'itemId'      => Framework\SV_WC_Helper::str_truncate( $fee_id, 31 ),
					'name'        => ! empty( $fee['name'] ) ? Framework\SV_WC_Helper::str_truncate( htmlentities( $fee['name'], ENT_QUOTES, 'UTF-8', false ), 31 ) : __( 'Fee', 'woocommerce-gateway-authorize-net-cim' ),
					'description' => __( 'Order Fee', 'woocommerce-gateway-authorize-net-cim' ),
					'quantity'    => 1,
					'unitPrice'   => Framework\SV_WC_Helper::number_format( $this->order->get_item_total( $fee ) ),
					'taxable'     => 'taxable' === $fee->get_tax_status(),
				);
			}
		}

		// maximum of 30 line items per order
		if ( count( $line_items ) > 30 ) {
			$line_items = array_slice( $line_items, 0, 30 );
		}

		return $line_items;
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
			'refId'              => $this->order->get_id(),
			'transactionRequest' => array(
				'transactionType' => self::PRIOR_AUTH_CAPTURE,
				'amount'          => $order->capture->amount,
				'refTransId'      => $order->capture->trans_id,
				'order'           => array(
					'invoiceNumber' => ltrim( $this->order->get_order_number(), _x( '#', 'hash before the order number', 'woocommerce-gateway-authorize-net-cim' ) ),
					'description'   => Framework\SV_WC_Helper::str_truncate( $this->order->description, 255 ),
				)
			),
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
			'refId'       			=> $this->order->get_id(),
			'transactionRequest' => array(
				'transactionType'		 => self::REFUND,
				'amount'  				 => $order->refund->amount,
				'refTransId' 			 => $order->refund->trans_id,
				'payment'        => array(
					'creditCard' => array(
						'cardNumber'     => $order->refund->last_four,
						'expirationDate' => $order->refund->expiry_date,
					),
				),
			),
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
			'refId'              => $this->order->get_id(),
			'transactionRequest' => array(
				'transactionType' => self::VOID,
				'refTransId'      => $order->refund->trans_id,
			),
		);
	}

	/**
	 * Set processing options according to transaction type to compliance COF
	 * @see https://developer.authorize.net/api/reference/features/card-on-file.html
	 *
	 * @since 3.7.0
	 *
	 * @return array $params processing options params
	 */
	protected function get_processing_options() {

		$params = [];
		if( $this->is_current_user_customer() ) {
			$params['isStoredCredentials'] = 'true';
		} else {
			$params['isSubsequentAuth'] = 'true';
		}

		return $params;
	}

	/**
	 * Set conditional subsequent auth information for MIT to compliance COF
	 * Only adds parameter in case of Subscription Renewal
	 * @see https://developer.authorize.net/api/reference/features/card-on-file.html#MerchantInitiated_Transactions_MITs
	 *
	 * @since 3.7.0
	 *
	 * @param string $transaction_type transaction type
	 * @param array $request_data transaction request data
	 *
	 * @return array $request_data transaction request data
	 */
	protected function set_sub_auth_info( $transaction_type, $request_data ) {

		if ( $this->is_subscription_renewal_order() ) {
			$request_data['transactionRequest']['subsequentAuthInformation'] = array( 'reason' => 'resubmission' );
		}

		return $request_data;
	}

	/**
	 * Whether the logged in user is customer
	 *
	 * @since 3.7.0
	 * @return boolean
	 */
	protected function is_current_user_customer() {
		return get_current_user_id() == $this->order->get_user_id();
	}

	/**
	 * Whether the order for subscription renewal
	 *
	 * @since 3.7.0
	 * @return boolean $is_renewal_order
	 */
	protected function is_subscription_renewal_order() {

		$is_renewal_order = false;
		if ( function_exists( 'wcs_order_contains_renewal' ) ) {
			$is_renewal_order = wcs_order_contains_renewal( $this->order->get_id() );
		}

		return $is_renewal_order;
	}

}
