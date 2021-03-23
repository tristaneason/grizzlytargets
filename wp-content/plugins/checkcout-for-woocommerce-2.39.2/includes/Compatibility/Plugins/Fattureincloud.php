<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class Fattureincloud extends Base {
	public function is_available() {
		return function_exists( 'billing_fields_woofc' );
	}

	function run_immediately() {
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'update_checkout_fragments' ), 1000 );
	}

	public function run() {
		remove_filter( 'woocommerce_billing_fields', 'billing_fields_woofc', 10 );
		add_action( 'cfw_payment_tab_content', array( $this, 'output_fields' ), 21 );
	}

	function get_fields_wrapped() {
		return '<div id="fattureincloud-fields">' . $this->get_fields() . '</div>';
	}

	function output_fields() {
		echo $this->get_fields_wrapped();
	}

	function get_fields() {
		ob_start();

		$fields = billing_fields_woofc( array() );

		foreach ( $fields as $key => $field ) {

			if ( $key === 'billing_cod_fisc' && ! $field['required'] && apply_filters( 'cfw_hide_optional_fiscal_code', true ) ) {
				continue;
			}

			cfw_form_field( $key, $field );
		}

		return ob_get_clean();
	}

	function update_checkout_fragments( $fragments ) {
		$fragments['#fattureincloud-fields'] = $this->get_fields_wrapped();

		return $fragments;
	}

	function remove_scripts( $scripts ) {
		$scripts['woo_fic_cf'] = 'woo_fic_cf';

		return $scripts;
	}
}
