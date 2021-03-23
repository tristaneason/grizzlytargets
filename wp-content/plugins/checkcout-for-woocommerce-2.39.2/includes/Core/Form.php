<?php

namespace Objectiv\Plugins\Checkout\Core;

use Objectiv\Plugins\Checkout\Main;

/**
 * Class Form
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Core
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class Form {

	/**
	 * @var bool
	 */
	private $debug = false;

	/**
	 * @since 1.1.5
	 * @access private
	 * @var string Is the phone enabled in the settings?
	 */
	private $phone_enabled;

	/**
	 * Form constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$cfw = Main::instance();
		$this->phone_enabled = $cfw->is_phone_fields_enabled();

		add_filter( 'woocommerce_default_address_fields', array( $this, 'get_custom_default_address_fields' ), 100000, 1 );
		add_filter( 'woocommerce_default_address_fields', array( $this, 'calculate_rows' ), 200000, 1 ); // seriously, run this last
		add_filter( 'woocommerce_get_country_locale', array($this, 'prevent_postcode_sort_change') );

		if ( $this->phone_enabled ) {
			add_action( 'woocommerce_checkout_create_order', array( $this, 'update_shipping_phone_on_order_create' ), 10, 2 );
			add_filter( 'woocommerce_billing_fields', array( $this, 'add_billing_phone_to_address_fields' ), 10, 1 );
		}

		// Run all fields through our function instead
		add_filter( 'woocommerce_form_field', array($this, 'cfw_form_field'), 10, 4 );
	}

	/**
	 * @since 1.1.5
	 * @param $address_fields
	 * @param $country
	 *
	 * @return mixed
	 */
	public function add_billing_phone_to_address_fields( $address_fields ) {
		$fields = WC()->countries->get_default_address_fields();

		if ( ! empty($fields['phone']) ) {
			$address_fields['billing_phone'] = $fields['phone'];
		}

		return $address_fields;
	}

	/**
	 * @since 1.1.5
	 * @param $order
	 * @param $data
	 */
	public function update_shipping_phone_on_order_create( $order, $data ) {
		if ( ! empty( $_POST['shipping_phone'] ) ) {
			$order->update_meta_data( '_shipping_phone', sanitize_text_field( $_POST['shipping_phone'] ) );
		}
	}

	/**
	 * @param $fields
	 * @return array
	 */
	public function get_custom_default_address_fields( $fields ) {
		// First Name
		$fields['first_name']['placeholder']       = $fields['first_name']['label'];
		$fields['first_name']['class']             = array();
		$fields['first_name']['autocomplete']      = 'given-name';
		$fields['first_name']['input_class']       = array( 'garlic-auto-save' );
		$fields['first_name']['priority']          = 05;
		$fields['first_name']['columns']           = 6;
		$fields['first_name']['label_class']       = 'cfw-input-label';
		$fields['first_name']['custom_attributes'] = array(
			'data-parsley-trigger' => 'change focusout',
		);

		// Last Name
		$fields['last_name']['placeholder']       = $fields['last_name']['label'];
		$fields['last_name']['class']             = array();
		$fields['last_name']['autocomplete']      = 'family-name';
		$fields['last_name']['input_class']       = array( 'garlic-auto-save' );
		$fields['last_name']['priority']          = 10;
		$fields['last_name']['columns']           = 6;
		$fields['last_name']['label_class']       = 'cfw-input-label';
		$fields['last_name']['custom_attributes'] = array(
			'data-parsley-trigger' => 'change focusout',
		);

		// Address 1
		$fields['address_1']['placeholder']       = $fields['address_1']['label'];
		$fields['address_1']['class']             = array( 'address-field' );
		$fields['address_1']['autocomplete']      = 'address-line1';
		$fields['address_1']['input_class']       = array( 'garlic-auto-save' );
		$fields['address_1']['priority']          = 15;
		$fields['address_1']['columns']           = 12;
		$fields['address_1']['label_class']       = 'cfw-input-label';
		$fields['address_1']['custom_attributes'] = array(
			'data-parsley-trigger' => 'change focusout',
		);

		// Address 2
		if ( isset( $fields['address_2'] ) ) {
			$fields['address_2']['label']        = cfw__( 'Apartment, suite, unit etc.', 'woocommerce' );
			$fields['address_2']['class']        = array( 'address-field' );
			$fields['address_2']['autocomplete'] = 'address-line2';
			$fields['address_2']['input_class']  = array( 'garlic-auto-save' );
			$fields['address_2']['priority']     = 20;
			$fields['address_2']['columns']      = 12;
			$fields['address_2']['label_class']  = 'cfw-input-label';
		}

		// Company
		if ( isset( $fields['company'] ) ) {
			$fields['company']['placeholder']  = $fields['company']['label'];
			$fields['company']['class']        = array();
			$fields['company']['autocomplete'] = 'organization';
			$fields['company']['input_class']  = array( 'garlic-auto-save' );
			$fields['company']['priority']     = 30;
			$fields['company']['columns']      = 12;
			$fields['company']['label_class']  = 'cfw-input-label';
		}

		// Country
		$fields['country']['type']         = 'country';
		$fields['country']['class']        = array( 'address-field', 'update_totals_on_change' );
		$fields['country']['autocomplete'] = 'country';
		$fields['country']['input_class']  = array( 'garlic-auto-save' );
		$fields['country']['priority']     = 40;
		$fields['country']['columns']      = 4;
		$fields['country']['label_class']  = 'cfw-input-label';

		// Postcode
		$fields['postcode']['placeholder']       = $fields['postcode']['label'];
		$fields['postcode']['class']             = array( 'address-field' );
		$fields['postcode']['validate']          = array( 'postcode' );
		$fields['postcode']['autocomplete']      = 'postal-code';
		$fields['postcode']['input_class']       = array( 'garlic-auto-save' );
		$fields['postcode']['priority']          = 45;
		$fields['postcode']['columns']           = 4;
		$fields['postcode']['label_class']       = 'cfw-input-label';
		$fields['postcode']['custom_attributes'] = array(
			'data-parsley-validate-if-empty' => '',
			'data-parsley-length'            => '[2,12]',
			'data-parsley-trigger'           => 'keyup change focusout',
		);

		// State
		$fields['state']['type']              = 'state';
		$fields['state']['placeholder']       = $fields['state']['label'];
		$fields['state']['class']             = array( 'address-field' );
		$fields['state']['validate']          = array( 'state' );
		$fields['state']['autocomplete']      = 'address-level1';
		$fields['state']['priority']          = 50;
		$fields['state']['columns']           = 4;
		$fields['state']['label_class']       = 'cfw-input-label';
		$fields['state']['input_class']       = array( 'garlic-auto-save' );
		$fields['state']['custom_attributes'] = array(
			'data-parsley-validate-if-empty' => '',
			'data-parsley-trigger'           => 'keyup change focusout',
		);

		// City
		$fields['city']['placeholder']       = $fields['city']['label'];
		$fields['city']['class']             = array( 'address-field' );
		$fields['city']['autocomplete']      = 'address-level2';
		$fields['city']['input_class']       = array( 'garlic-auto-save' );
		$fields['city']['priority']          = 60;
		$fields['city']['columns']           = 12;
		$fields['city']['label_class']       = 'cfw-input-label';
		$fields['city']['custom_attributes'] = array(
			'data-parsley-trigger' => 'change focusout',
		);

		// Phone
		if ( $this->phone_enabled ) {
			$fields['phone'] = array(
				'type'         => 'tel',
				'label'        => cfw__( 'Phone', 'woocommerce' ),
				'placeholder'  => cfw__( 'Phone', 'woocommerce' ),
				'required'     => 'required' === get_option( 'woocommerce_checkout_phone_field' ),
				'class'        => array( 'address-field' ),
				'autocomplete' => 'tel',
				'input_class'  => array( 'garlic-auto-save' ),
				'priority'     => 70,
				'columns'      => 12,
				'label_class'  => 'cfw-input-label',
				'validate'     => array( 'phone' ),
				'custom_attributes' => array(
					'data-parsley-trigger' => 'keyup change focusout',
				),
			);

			if ( 'required' === get_option( 'woocommerce_checkout_phone_field' ) ) {
				$fields['phone']['custom_attributes']['data-parsley-validate-if-empty'] = '';
			}
		}

		return $fields;
	}

	function calculate_wrap( $field, $start_end = null ) {
		if ( empty( $field['wrap']) ) {
			// Convert to field types for wrap
			if ( ! empty( $field['type'] ) && in_array( $field['type'], array( 'state', 'country' ) ) ) {
				$wrap_type = 'select';
			} elseif( empty( $field['type'] ) ) {
				$wrap_type = 'text';
			} else {
				$wrap_type = $field['type'];
			}

			if ( ! isset( $field['priority'] ) ) {
				$field['priority'] = 1000; // don't assume high priority (lower number)
			}

			// Add our wrap
			$field['wrap'] = $this->input_wrap( $wrap_type, $field['columns'], $field['priority'] );

			// Default these to false
			$field['start'] = $start_end;
			$field['end'] = $start_end;

			/**
			 * If neither start or end are set and $start_end is a boolean value,
			 * init both values to passed in $start_end value
			 */
			if ( is_bool( $start_end ) ) {
				if ( ! isset( $field['start'] ) ) {
					$field['start'] = $start_end;
				}

				if ( ! isset( $field['end'] ) ) {
					$field['end'] = $start_end;
				}
			}
		}

		return $field;
	}

	function calculate_rows( $fields ) {
		// Native WooCommerce sort
		if ( function_exists( 'wc_checkout_fields_uasort_comparison' ) ) {
			uasort( $fields, 'wc_checkout_fields_uasort_comparison' );
		}

		$start              = true;
		$summed_column_size = 0;
		$max_size           = 12;
		$last_index         = false;

		foreach ( $fields as $index => $field ) {
			// Clean up our state
			unset( $fields[ $index ]['start'] );
			unset( $fields[ $index ]['end'] );
			unset( $fields[ $index ]['wrap'] );

			// Set our wrap
			$fields[ $index ] = $this->calculate_wrap( $field );

			if ( $this->debug ) {
				$fields[ $index ]['label'] = $fields[ $index ]['label'] . ': ' . $summed_column_size;
				$fields[ $index ]['placeholder'] = $fields[ $index ]['placeholder'] . ': ' . $summed_column_size;
			}

			// If we flagged this field in the last loop iteration to be
			// the start of a row, or we are on the first iteration, set start to true
			if ( $start === true ) {
				$fields[ $index ]['start'] = true;

				// Make sure the last field was an end, if this is a start
				if ( $last_index !== false ) {
					$fields[ $last_index ]['end'] = true;
				}

				// Set start to null
				$start = null;
			}

			/**
			 * If the field is the max possible size, it should be the start and end of the row
			 *
			 * OR if the summed column size + this field is over the max size, set to start of row
			 * and set last item to end of row
			 *
			 * OR if summed column size + this field is under the max size, set end to false
			 */
			if ( isset( $fields[ $index ]['columns'] ) && $fields[ $index ]['columns'] == $max_size ) {
				$fields[ $index ]['start'] = true;
				$fields[ $index ]['end'] = true;

				// Next field should be start of row
				$start = true;

				// Since this is the start, last field should be the end
				if ( $last_index !== false ) {
					$fields[ $last_index ][ 'end' ] = true;
				}
			} elseif ( isset( $fields[ $index ]['columns'] ) && $summed_column_size + $fields[ $index ]['columns'] > $max_size  ) {
				$fields[ $index ]['start'] = true;

				// Since this is the start, last field should be the end
				if ( $last_index !== false ) {
					$fields[ $last_index ][ 'end' ] = true;
				}

				// Reset size counter
				$summed_column_size = 0;
			} elseif ( isset( $fields[ $index ]['columns'] ) && $summed_column_size + $fields[ $index ]['columns'] < $max_size ) {
				if ( $this->debug ) {
					var_dump($index . ' ' . $summed_column_size . ' ' . $field['columns']);
				}

				// Add to summed size
				$summed_column_size = $summed_column_size + $field['columns'];

				// Not the end, so set to false
				$fields[ $index ]['end'] = false;
			} elseif ( isset( $fields[ $index ]['columns'] ) && $summed_column_size + $fields[ $index ]['columns'] === $max_size ) {
				// Reset summed size to 0
				$summed_column_size = 0;

				// This is the end
				$fields[ $index ]['end'] = true;

				// So the next field is logically the beginning
				$start = true;
			}

			/**
			 * If for some reason neither start or end are set above,
			 * Init both values to false
			 */
			if ( ! isset( $fields[ $index ]['start'] ) ) {
				$fields[ $index ]['start'] = false;
			}

			if ( ! isset( $fields[ $index ]['end'] ) ) {
				$fields[ $index ]['end'] = false;
			}

			// Store this index so we can use it for backwards lookups later
			$last_index = $index;
		}

		return $fields;
	}

	/**
	 * @param $locales
	 *
	 * Some locales reprioritize the postcode to be later than we do. This is undesirable behavior
	 * In the future, we should probably adjust our form styles to allow for reordering like this on a locale basis
	 *
	 * @return array $locales
	 */
	function prevent_postcode_sort_change( $locales ) {
		foreach( $locales as $key => $value ) {
			if ( ! empty( $value['postcode'] ) && ! empty( $value['postcode']['priority'] ) ) {
				$locales[ $key ]['postcode']['priority'] = 45;
			}
		}

		return $locales;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param $type
	 * @param $col
	 * @param $priority
	 * @return object
	 */
	public function input_wrap( $type, $col, $priority ) {

		if ( 'hidden' == $type ) {
			return (object) [
				'start' => '',
				'end'   => '',
			];
		}

		$inner_start = '';
		$inner_end   = '';

		switch ( $type ) {

			case 'number':
				$inner_start = '<div class="cfw-input-wrap cfw-number-input">';
				$inner_end   = '</div>';
				break;
			case 'password':
				$inner_start = '<div class="cfw-input-wrap cfw-password-input">';
				$inner_end   = '</div>';
				break;
			case 'select':
				$inner_start = '<div class="cfw-input-wrap cfw-select-input">';
				$inner_end   = '</div>';
				break;
			case 'multiselect':
				$inner_start = '<div class="cfw-input-wrap cfw-select-input">';
				$inner_end   = '</div>';
				break;
			case 'textarea':
				$inner_start = '<div class="cfw-input-wrap cfw-textarea-input">';
				$inner_end   = '</div>';
				break;
			case 'tel':
				$inner_start = '<div class="cfw-input-wrap cfw-tel-input">';
				$inner_end   = '</div>';
				break;
			default:
				$inner_start = '<div class="cfw-input-wrap cfw-text-input">';
				$inner_end   = '</div>';
				break;
		}

		$priority = esc_attr( $priority );

		$start = '<div class="cfw-column-' . $col . '" id="%1$s" data-priority="' . $priority . '">' . $inner_start . '%2$s';

		$end = "$inner_end</div>";

		$start = apply_filters( 'cfw_input_wrap_start', $start, $type, $col, $priority );
		$end   = apply_filters( 'cfw_input_wrap_end', $end, $type, $col, $priority );

		return (object) [
			'start' => $start,
			'end'   => $end,
		];
	}

	function cfw_form_field( $field, $key, $args, $value ) {
		$args['return'] = true;
		$args['placeholder'] = $args['label'];
		$args['start'] = true;
		$args['end'] = true;
		$args['columns'] = 12;

		if ( is_string( $args['input_class'] ) ){
			$args['input_class'] = array( $args['input_class'] );
		}

		$args['input_class'][] = 'garlic-auto-save';


		if ( stripos( $args['type'], 'checkbox') === false && stripos( $args['type'], 'radio') === false && apply_filters( 'cfw_use_floating_label', true, $args['type'], $key, $args, $value ) ) {
			if ( is_string( $args['label_class'] ) ) {
				$args['label_class'] = array( $args['label_class'] );
			}
			$args['label_class'][] = 'cfw-input-label';
		}

		if ( empty($value) && ! empty( $_POST[ $key ] ) ) {
			$value = $_POST[ $key ];
		}

		return cfw_form_field( $key, $args, $value );
	}
}
