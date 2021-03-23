<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class PostNL extends Base {
	function is_available() {
		return class_exists('\\WooCommerce_PostNL');
	}

	function run() {
		$this->disable_nl_hooks();

		$cfw = \Objectiv\Plugins\Checkout\Main::instance();
		remove_filter( 'woocommerce_get_country_locale', array($cfw->get_form(), 'prevent_postcode_sort_change') );

		add_filter( 'woocommerce_default_address_fields', array( $this, 'add_new_fields' ), 100001, 1 ); // run after our normal hook
		add_filter( 'woocommerce_get_country_locale', array($this, 'prevent_postcode_sort_change') );
		add_filter( 'cfw_enable_zip_autocomplete', '__return_false' );

		// Fix shipping preview
		add_filter( 'cfw_get_shipping_details_address', array($this, 'fix_shipping_preview'), 10, 2 );

		// Move form-row class to input container from row
		add_filter( 'cfw_input_wrap_start', array($this, 'input_wrap_start') );
		add_filter( 'cfw_input_row_wrap', array($this, 'input_row_wrap') );

		// Fool the JS into using shipping address
		add_action( 'woocommerce_before_order_notes', array( $this, 'add_shipping_different_checkbox' ) );
	}

	function disable_nl_hooks() {
		global $wp_filter;

		$existing_hooks                      = $wp_filter['woocommerce_billing_fields'];

		$priority = apply_filters( 'nl_checkout_fields_priority', 10, 'billing' );

		if ( $existing_hooks[$priority] ) {
			foreach ( $existing_hooks[$priority] as $key => $callback ) {
				if ( false !== stripos( $key, 'nl_billing_fields' ) ) {
					global $WC_NLPostcode_Fields;

					$WC_NLPostcode_Fields = $callback['function'][0];
				}
			}
		}

		if ( empty($WC_NLPostcode_Fields) ) return;

		remove_filter( 'woocommerce_billing_fields', array( $WC_NLPostcode_Fields, 'nl_billing_fields' ), $priority );
		remove_filter( 'woocommerce_shipping_fields', array( $WC_NLPostcode_Fields, 'nl_shipping_fields' ),$priority );
	}

	function add_new_fields( $fields ) {
		$cfw = \Objectiv\Plugins\Checkout\Main::instance();

		// Adjust postcode field
		$fields['postcode']['priority'] = 11;

		// Add street name
		$fields['street_name'] = array(
			'label'             => cfw__( 'Street name', 'woocommerce-postnl' ),
			'placeholder'       => cfw_esc_attr__( 'Street name', 'woocommerce-postnl' ),
			'required'          => true,
			'class'             => array(),
			'autocomplete'      => '',
			'input_class'       => array( 'garlic-auto-save' ),
			'priority'          => 14,
			'label_class'       => 'cfw-input-label',
			'columns'           => 12,
			'custom_attributes' => array(
				'data-parsley-trigger' => 'change focusout',
			),
		);

		// Then add house number
		$fields['house_number'] = array(
			'label'             => cfw__( 'Nr.', 'woocommerce-postnl' ),
			'placeholder'       => cfw_esc_attr__( 'Nr.', 'woocommerce-postnl' ),
			'required'          => true,
			'class'             => array(),
			'autocomplete'      => '',
			'input_class'       => array( 'garlic-auto-save' ),
			'priority'          => 12,
			'label_class'       => 'cfw-input-label',
			'custom_attributes' => array(
				'data-parsley-trigger' => 'change focusout',
			),
			'columns'           => 4,
		);

		// Then house number suffix
		$fields['house_number_suffix'] = array(
			'label'             => cfw__( 'Suffix', 'woocommerce-postnl' ),
			'placeholder'       => cfw_esc_attr__( 'Suffix', 'woocommerce-postnl' ),
			'required'          => false,
			'class'             => array(),
			'autocomplete'      => '',
			'input_class'       => array( 'garlic-auto-save' ),
			'priority'          => 13,
			'columns'           => 4,
			'label_class'       => 'cfw-input-label',
			'custom_attributes' => array(
				'data-parsley-trigger' => 'change focusout',
			),
		);

		$fields['state']['columns'] = 8;

        // Set address 1 / address 2 to hidden
		$fields['address_1']['type'] = 'hidden';
		$fields['address_1']['start'] = false;
		unset( $fields['address_1']['custom_attributes'] );
		unset( $fields['address_1']['input_class'] );
		$fields['address_2']['type'] = 'hidden';
		$fields['address_2']['end'] = false;
		unset( $fields['address_2']['custom_attributes'] );
		unset( $fields['address_2']['input_class'] );

		return $fields;
	}

	function prevent_postcode_sort_change( $locales ) {
		foreach( $locales as $key => $value ) {
			if ( ! empty( $value['postcode'] ) && ! empty( $value['postcode']['priority'] ) ) {
				$locales[ $key ]['postcode']['priority'] = 11;
			}
		}

		return $locales;
	}

	function input_wrap_start( $input_wrap_start ) {
		$input_wrap_start = str_replace( 'cfw-col', 'form-row cfw-col', $input_wrap_start );

		return $input_wrap_start;
	}

	function input_row_wrap( $input_row_wrap ) {
		$input_row_wrap = str_replace( 'form-row', '', $input_row_wrap );

		return $input_row_wrap;
	}

	function fix_shipping_preview( $address, $checkout ) {
		$address['address_1'] = $checkout->get_value( 'shipping_street_name' ) . ' ' . $checkout->get_value( 'shipping_house_number' );

		if ( ! empty( $checkout->get_value( 'shipping_house_number_suffix' ) ) ) {
			$address['address_1'] = $address['address_1'] . '-' . $checkout->get_value( 'shipping_house_number_suffix' );
		}

		return $address;
	}

	function add_shipping_different_checkbox() {
		if ( ! WC()->cart->needs_shipping_address() ) return;
		?>
		<div style="display:none;">
			<input id="ship-to-different-address-checkbox" type="checkbox" name="ship_to_different_address" disabled="disabled" value="1" checked />
		</div>
		<?php
	}

	function typescript_class_and_params( $compatibility ) {
		$compatibility[] = [
			'class'  => 'PostNL',
			'params' => [],
		];

		return $compatibility;
	}
}