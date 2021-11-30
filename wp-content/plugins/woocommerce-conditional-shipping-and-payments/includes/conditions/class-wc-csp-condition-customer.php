<?php
/**
 * WC_CSP_Condition_Customer class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customer Condition.
 *
 * @class    WC_CSP_Condition_Customer
 * @version  1.10.0
 */
class WC_CSP_Condition_Customer extends WC_CSP_Condition {

	/**
	 * Configuration settings for the available modifiers.
	 *
	 * @var array|array[]
	 **/
	protected $available_modifiers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                             = 'customer';
		$this->title                          = __( 'Customer', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_product_restrictions = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
		$this->supported_global_restrictions  = array( 'shipping_countries', 'payment_gateways', 'shipping_methods' );
		$this->available_modifiers            = array(
			'in'                  => array(
				'label'     => __( 'e-mail is', 'woocommerce-conditional-shipping-and-payments' ),
			),
			'not-in'              => array(
				'label'     => __( 'e-mail is not', 'woocommerce-conditional-shipping-and-payments' ),
			),
			'is-returning'        => array(
				'label'     => __( 'is returning', 'woocommerce-conditional-shipping-and-payments' ),
			),
			'is-new'              => array(
				'label'     => __( 'is new', 'woocommerce-conditional-shipping-and-payments' ),
			)
		);
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) && ! in_array( $data[ 'modifier' ], array( 'is-new', 'is-returning' ) ) ) {
			return true;
		}

		$message = false;

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {
			$message = __( 'use an authorized account', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {
			$message = __( 'use an authorized account', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'is-new' ) ) ) {
			$message = __( 'checkout with an account that you have previously placed orders with', 'woocommerce-conditional-shipping-and-payments' );
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'is-returning' ) ) ) {
			$message = __( 'checkout with a new customer account', 'woocommerce-conditional-shipping-and-payments' );
		}

		return $message;
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Empty conditions always apply (not evaluated).
		if ( empty( $data[ 'value' ] ) && ! in_array( $data[ 'modifier' ], array( 'is-new', 'is-returning' ) ) ) {
			return true;
		}

		$this->set_active();

		if ( in_array( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {
			return self::check_emails( $data, $args );
		}

		if ( isset( $args[ 'order' ] ) ) {

			$order   = $args[ 'order' ];
			$user_id = $order->get_customer_id();

		} else {

			if ( ! is_user_logged_in() ) {
				$user_id = 0;
			} else {
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;
			}
		}

		if ( in_array( $data[ 'modifier' ], array( 'is-new', 'is-returning' ) ) ) {
			return self::check_customer_status( $data, $args, $user_id );
		}

		return false;
	}

	/**
	 * Check if customer's e-mail is restricted.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_emails( $data, $args ) {

		$check_emails      = array();
		$restricted_emails = array_filter( array_map( 'sanitize_email', array_map( 'strtolower', $data[ 'value'] ) ), 'is_email' );

		if ( is_user_logged_in() ) {
			$current_user   = wp_get_current_user();
			$check_emails[] = $current_user->user_email;
		}

		if ( ! empty( $args[ 'order' ] ) ) {

			$order         = $args[ 'order' ];
			$billing_email = WC_CSP_Core_Compatibility::is_wc_version_gte( '2.7' ) ? $order->get_billing_email() : $order->billing_email;

			if ( $billing_email ) {
				$check_emails[] = $billing_email;
			}

		} else {

			// Validating checkout fields?
			if ( ! empty( $_POST[ 'billing_email' ] ) ) {

				$check_emails[] = wc_clean( $_POST[ 'billing_email' ] );

			// Updating order review?
			} elseif ( did_action( 'woocommerce_checkout_update_order_review' ) && ! empty( $_POST[ 'post_data' ] ) ) {
				parse_str( wp_unslash( $_POST[ 'post_data' ] ), $billing_data ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				if ( is_array( $billing_data ) && isset( $billing_data[ 'billing_email' ] ) ) {
					$check_emails[] = wc_clean( $billing_data[ 'billing_email' ] );
				}
			}

			$check_emails = array_map( 'sanitize_email', array_map( 'strtolower', $check_emails ) );
		}

		$identified_email = false;

		if ( ! empty( $check_emails ) ) {
			foreach ( $check_emails as $check_email ) {
				if ( in_array( $check_email, $restricted_emails ) ) {
					$identified_email = true;
					break;
				}
			}
		}

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $identified_email ) {
			return true;
		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $identified_email ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if customer is a new or a returing one.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restrictions.
	 * @return boolean
	 */
	public function check_customer_status( $data, $args, $user_id ) {

		// Treat guests as new customers.
		if ( 0 === $user_id ) {
			if ( $this->modifier_is( $data[ 'modifier' ], array( 'is-new' ) ) ) {
				return true;
			} else {
				return false;
			}
		}

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.0' ) ) {

			$current_customer = new WC_Customer( $user_id );

			// Return if customer couldn't be found or created.
			if ( ! is_a( $current_customer, 'WC_Customer' ) ) {
				return false;
			}

			$last_order = $current_customer->get_last_order();

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'is-returning' ) ) && false !== $last_order ) {
				return true;
			} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'is-new' ) ) && false === $last_order ) {
				return true;
			}

		} else {

			global $wpdb;

			$results = (int) $wpdb->get_var( $wpdb->prepare( "
					SELECT DISTINCT count( * ) FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
					WHERE p.post_type = %s
					AND pm.meta_key = %s
					AND pm.meta_value = %d
				", 'shop_order', '_customer_user', $user_id ) );

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'is-returning' ) ) && 0 !== $results ) {
				return true;
			} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'is-new' ) ) && 0 === $results ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		$processed_condition_data[ 'condition_id' ] = $this->id;
		$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );

		if ( ! empty( $posted_condition_data[ 'value' ] ) && in_array( $processed_condition_data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {

			$emails                               = is_array( $posted_condition_data[ 'value' ] ) ? array_pop( $posted_condition_data[ 'value' ] ) : array();

			$processed_condition_data[ 'value' ]  = array_filter( array_map( 'trim', array_map( 'strtolower', explode( ',', wc_clean( $emails ) ) ) ) );

			$processed_condition_data[ 'value' ]  = $this->filter_invalid_emails( $processed_condition_data[ 'value' ], intval( $posted_condition_data[ 'restriction_position' ] ) + 1 );

			// Don't save with empty value.
			if ( empty( $processed_condition_data[ 'value' ] ) ) {
				return false;
			}
		}

		return $processed_condition_data;
	}

	/**
	 * Filter out invalid e-mails.
	 *
	 * @param  array   $posted_emails
	 * @param  string  $position
	 */
	public function filter_invalid_emails( $posted_emails, $position ) {

		$invalid_emails = array();
		foreach ( $posted_emails as $email ) {
			if ( ! is_email( $email ) ) {
				$invalid_emails[] = $email;
			}
		}

		if ( empty( $invalid_emails ) ) {
			return $posted_emails;
		}

		if ( 1 === count( $invalid_emails ) ) {
			WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Rule <strong>#%1$s</strong>: Invalid e-mail found (%2$s) and removed from the <strong>Customer</strong> condition.', 'woocommerce-conditional-shipping-and-payments' ),
				$position,
				implode( ', ', $invalid_emails )
			) );
		} else {
			WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Rule <strong>#%1$s</strong>: Invalid e-mails found (%2$s) and removed from the <strong>Customer</strong> condition.', 'woocommerce-conditional-shipping-and-payments' ),
				$position,
				implode( ', ', $invalid_emails )
			) );
		}

		return array_diff( $posted_emails, $invalid_emails );
	}

	/**
	 * Get Customer condition content for restriction metaboxes.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = 'in'; // Default modifier
		$values   = ! empty( $condition_data[ 'value' ] ) ? $condition_data[ 'value' ] : array();
		$values   = ! is_array( $values ) ? array( $values ) : $values;

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$email_modifiers              = array( 'in', 'not-in' );
		$returning_customer_modifiers = array( 'is-returning', 'is-new' );
		$value_input_name             = 'restriction[' . $index . '][conditions][' . $condition_index . '][value][]';

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>"/>
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]"
							class="has_conditional_values" data-value_input_name="<?php echo $value_input_name; ?>"
					>
						<?php foreach ( $this->available_modifiers as $modifier_key => $modifier_content ) { ?>
							<option value="<?php echo $modifier_key; ?>" <?php selected( $modifier, $modifier_key, true ) ?>>
								<?php echo $modifier_content[ 'label' ] ?>
							</option>
						<?php } ?>
					</select>
				</div>
			</div>

			<?php
			echo $this->get_admin_fields_freetext_html( $modifier, $email_modifiers, $values, $value_input_name );
			echo $this->get_admin_fields_placeholder_html( $modifier, $returning_customer_modifiers );
			?>
		</div>
		<?php
	}

	/**
	 * Prepare and return the freetext html fields
	 *
	 * @param string $current_modifier
	 * @param array  $modifiers
	 * @param array  $values
	 * @param string $value_input_name
	 *
	 * @return false|string
	 */
	protected function get_admin_fields_freetext_html( $current_modifier, $modifiers, $values, $value_input_name ) {

		$values = in_array( $current_modifier, $modifiers ) ? $values : array();

		if ( ! empty( $values ) ) {
			$formatted_values = implode( ', ', $values );
		} else {
			$formatted_values = '';
		}

		ob_start();

		?>

		<div class="condition_value select-field"
			 data-modifiers="<?php echo implode( ',', $modifiers ); ?>"
			<?php echo in_array( $current_modifier, $modifiers ) ? '' : ' style="display:none;"'; ?>
		>
			<textarea class="csp_conditional_values_input" name="<?php echo in_array( $current_modifier, $modifiers ) ? $value_input_name : ''; ?>" placeholder="<?php _e( 'List of e-mails separated by comma.', 'woocommerce-conditional-shipping-and-payments' ); ?>"><?php echo in_array( $current_modifier, $modifiers ) ? esc_textarea( $formatted_values ) : ''; ?></textarea>
		</div>

		<?php

		return ob_get_clean();
	}

	/**
	 * Prepare and return the value placeholder field.
	 *
	 * @param string $current_modifier
	 * @param array  $modifiers
	 *
	 * @return false|string
	 */
	protected function get_admin_fields_placeholder_html( $current_modifier, $modifiers ) {

		ob_start();

		?>

		<div class="condition_value condition--disabled"
			 data-modifiers="<?php echo implode( ',', $modifiers ); ?>"
			<?php echo in_array( $current_modifier, $modifiers ) ? '' : ' style="display:none;"'; ?>
		></div>

		<?php

		return ob_get_clean();
	}
}
