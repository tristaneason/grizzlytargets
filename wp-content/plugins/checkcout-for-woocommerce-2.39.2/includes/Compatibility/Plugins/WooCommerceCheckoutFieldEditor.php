<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class WooCommerceCheckoutFieldEditor extends Base {
	public function is_available() {
		return defined( 'WC_CHECKOUT_FIELD_EDITOR_VERSION' );
	}

	public function run_immediately() {
		// Add styles for WooCommerce Checkout Field Editor admin page
		add_action( 'admin_head', array( $this, 'output_custom_styles' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_to_additional_fields_tab' ) );

		add_filter( 'woocommerce_enable_order_notes_field', array( $this, 'enable_notes_field' ) );

		// Do this here so that it applies to WC ajax calls
		remove_filter( 'woocommerce_billing_fields', 'wc_checkout_fields_modify_billing_fields', 1 );
		remove_filter( 'woocommerce_shipping_fields', 'wc_checkout_fields_modify_shipping_fields', 1 );
	}

	public function run() {
		remove_filter( 'woocommerce_form_field_date', 'wc_checkout_fields_date_picker_field', 10 );
		remove_action( 'wp_enqueue_scripts', 'wc_checkout_fields_dequeue_address_i18n', 15 );

		add_filter( 'cfw_form_field_element_date', array( $this, 'date_field_element' ), 10, 4 );

		add_filter( 'woocommerce_form_field_multiselect', array( $this, 'fix_fields' ), 100, 5 );
		add_filter( 'woocommerce_form_field_radio', array( $this, 'fix_fields' ), 100, 5 );
	}

	function fix_fields( $field, $key, $args, $value, $row_wrap ) {
		if ( in_array( $args['type'], array( 'multiselect', 'radio' ), true ) && isset( $row_wrap ) ) {
			$row_wrap = str_replace( 'form-row ', 'cfw-input-wrap cfw-floating-label ', $row_wrap );
			$field    = str_replace( array( 'form-row-first', 'form-row-last' ), 'cfw-column-12', $field );
			$field = $row_wrap . $field . '</div>';
		}

		return $field;
    }

	function output_custom_styles() {
		if ( empty( $_GET['page'] ) || $_GET['page'] !== 'checkout_field_editor' ) {
			return;
		}
		?>
		<style type="text/css">
			/* Hide Billing and Shipping Fields */
			.woo-nav-tab-wrapper a:nth-child(1), .woo-nav-tab-wrapper a:nth-child(2) {
				display: none;
			}
		</style>
		<?php
	}

	function maybe_redirect_to_additional_fields_tab() {
		if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'checkout_field_editor' && ( empty( $_GET['tab'] ) || $_GET['tab'] !== 'additional' ) ) {
			wp_safe_redirect( 'admin.php?page=checkout_field_editor&tab=additional' );
			exit();
		}
	}

	function enable_notes_field() {
		return  'yes' == get_option( 'woocommerce_enable_order_comments', 'yes' );
	}

	function date_field_element( $element, $key, $value, $args ) {
	    return '<input type="text" class="checkout-date-picker input-text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" placeholder="' . $args['placeholder'] . '" ' . $args['maxlength'] . ' value="' . esc_attr( $value ) . '" />';
    }
}
