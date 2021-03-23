<?php

namespace Objectiv\Plugins\Checkout\Action;

use Objectiv\BoosterSeat\Base\Action;

class UpdateCheckoutAction extends Action {

	public function __construct( $id, $no_privilege, $action_prefix ) {
		parent::__construct( $id, $no_privilege, $action_prefix );
	}

	public function action() {
		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		do_action( 'woocommerce_checkout_update_order_review', $_POST['post_data'] );
		do_action( 'cfw_checkout_update_order_review' );

		$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

		if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
			foreach ( $_POST['shipping_method'] as $i => $value ) {
				$chosen_shipping_methods[ $i ] = wc_clean( $value );
			}
		}

		$redirect = apply_filters( 'cfw_update_checkout_redirect', false );

		WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
		WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : $_POST['payment_method'] );
		WC()->customer->set_props(
			array(
				'billing_country'   => isset( $_POST['billing_country'] ) ? wc_clean( wp_unslash( $_POST['billing_country'] ) ) : null,
				'billing_state'     => isset( $_POST['billing_state'] ) ? wc_clean( wp_unslash( $_POST['billing_state'] ) ) : null,
				'billing_postcode'  => isset( $_POST['billing_postcode'] ) ? wc_clean( wp_unslash( $_POST['billing_postcode'] ) ) : null,
				'billing_city'      => isset( $_POST['billing_city'] ) ? wc_clean( wp_unslash( $_POST['billing_city'] ) ) : null,
				'billing_address_1' => isset( $_POST['billing_address_1'] ) ? wc_clean( wp_unslash( $_POST['billing_address_1'] ) ) : null,
				'billing_address_2' => isset( $_POST['billing_address_2'] ) ? wc_clean( wp_unslash( $_POST['billing_address_2'] ) ) : null,
			)
		);

		if ( wc_ship_to_billing_address_only() || ! WC()->cart->needs_shipping() ) {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['billing_country'] ) ? wc_clean( wp_unslash( $_POST['billing_country'] ) ) : null,
					'shipping_state'     => isset( $_POST['billing_state'] ) ? wc_clean( wp_unslash( $_POST['billing_state'] ) ) : null,
					'shipping_postcode'  => isset( $_POST['billing_postcode'] ) ? wc_clean( wp_unslash( $_POST['billing_postcode'] ) ) : null,
					'shipping_city'      => isset( $_POST['billing_city'] ) ? wc_clean( wp_unslash( $_POST['billing_city'] ) ) : null,
					'shipping_address_1' => isset( $_POST['billing_address_1'] ) ? wc_clean( wp_unslash( $_POST['billing_address_1'] ) ) : null,
					'shipping_address_2' => isset( $_POST['billing_address_2'] ) ? wc_clean( wp_unslash( $_POST['billing_address_2'] ) ) : null,
				)
			);
		} else {
			WC()->customer->set_props(
				array(
					'shipping_country'   => isset( $_POST['shipping_country'] ) ? wc_clean( wp_unslash( $_POST['shipping_country'] ) ) : null,
					'shipping_state'     => isset( $_POST['shipping_state'] ) ? wc_clean( wp_unslash( $_POST['shipping_state'] ) ) : null,
					'shipping_postcode'  => isset( $_POST['shipping_postcode'] ) ? wc_clean( wp_unslash( $_POST['shipping_postcode'] ) ) : null,
					'shipping_city'      => isset( $_POST['shipping_city'] ) ? wc_clean( wp_unslash( $_POST['shipping_city'] ) ) : null,
					'shipping_address_1' => isset( $_POST['shipping_address_1'] ) ? wc_clean( wp_unslash( $_POST['shipping_address_1'] ) ) : null,
					'shipping_address_2' => isset( $_POST['shipping_address_2'] ) ? wc_clean( wp_unslash( $_POST['shipping_address_2'] ) ) : null,
				)
			);
		}

		if ( isset( $_POST['has_full_address'] ) && wc_string_to_bool( wc_clean( wp_unslash( $_POST['has_full_address'] ) ) ) ) {
			WC()->customer->set_calculated_shipping( true );
		} else {
			WC()->customer->set_calculated_shipping( false );
		}

		WC()->customer->save();

		// Calculate shipping before totals. This will ensure any shipping methods that affect things like taxes are chosen prior to final totals being calculated. Ref: #22708.
		WC()->cart->calculate_shipping();
		WC()->cart->calculate_totals();

		unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

		$payment_methods_html    = cfw_get_payment_methods_html();
		$updated_payment_methods = apply_filters( 'cfw_update_payment_methods', cfw_get_payment_methods( false, false, true, $payment_methods_html ) );

		/**
		 * If gateways haven't changed, set to false so that we don't replace
		 */
		if ( WC()->cart->needs_payment() && cfw_get_payment_methods_html_fingerprint( $payment_methods_html ) == $_POST['cfw_payment_methods_fingerprint'] && ( empty( $_POST['force_updated_checkout'] ) || $_POST['force_updated_checkout'] !== 'true' ) ) {
			$updated_payment_methods = false;
		}

		do_action( 'woocommerce_check_cart_items' );

		$all_notices  = WC()->session->get( 'wc_notices', array() );

		// Filter out empty messages
		foreach( $all_notices as $key => $notice ) {
			if ( empty( array_filter( $notice ) ) ) {
				unset( $all_notices[ $key ] );
			}
		}

		$notice_types = apply_filters( 'woocommerce_notice_types', array( 'error', 'success', 'notice' ) );
		$notices      = [];

		foreach ( $notice_types as $notice_type ) {
			if ( wc_notice_count( $notice_type ) > 0 && isset( $all_notices[ $notice_type ] ) ) {
				$notices[ $notice_type ] = $all_notices[ $notice_type ];
			}
		}

		wc_clear_notices();

		$this->out(
			array(
				'needs_payment'           => WC()->cart->needs_payment(),
				'updated_payment_methods' => $updated_payment_methods,
				'fragments'               => apply_filters(
					'woocommerce_update_order_review_fragments', array(
						'#cfw-shipping-details-fields'     => '<div id="cfw-shipping-details-fields">' . cfw_get_shipping_details( WC()->checkout() ) . '</div>',
						'#shipping_method'                 => '<div id="shipping_method">' . cfw_get_shipping_methods_html() . '</div>',
						'#cfw_checkout_before_order_review' => $this->get_action_output( 'woocommerce_checkout_before_order_review' ),
						'#cfw_checkout_after_order_review' => $this->get_action_output( 'woocommerce_checkout_after_order_review' ),
						'#cfw-place-order'                 => cfw_get_place_order(),
						'#cfw-totals-list'                 => cfw_get_totals_html(),
						'#cfw-cart-list'                   => cfw_get_cart_html(),
						'#cfw-mobile-total'                => '<span id="cfw-mobile-total" class="total amount">' . WC()->cart->get_total() . '</span>',
					)
				),
				'redirect'                => $redirect,
				'notices'                 => $notices,
			)
		);
	}

	function get_action_output( $action ) {
		ob_start();

		do_action( $action );

		return ob_get_clean();
	}
}
