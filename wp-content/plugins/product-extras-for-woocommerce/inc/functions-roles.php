<?php
/**
 * Functions for roles
 * @since 3.6.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether role-based pricing is enabled
 * @since 3.6.0
 */
function pewc_is_role_based_pricing() {

	$enable_roles = get_option( 'pewc_enable_roles', 'no' );
	if( $enable_roles == 'yes' && pewc_is_pro() ) {
		return true;
	}

	return false;

}

/**
 * Get the list of all roles
 * @return Array
 * @since 3.6.0
 */
function pewc_get_all_roles() {

	$roles = array();

	// The function get_editable_roles isn't available when we save our settings
	if( function_exists( 'get_editable_roles' ) ) {

		$editable_roles = get_editable_roles();
		set_transient( 'pewc_editable_roles', $editable_roles, pewc_get_transient_expiration() );

	} else {

		$editable_roles = get_transient( 'pewc_editable_roles' );

	}

	if( $editable_roles ) {
		foreach( $editable_roles as $id=>$role ) {
			$roles[$id]	= $role['name'];
		}
	}

	return $roles;

}

/**
 * Get the list of enabled roles
 * @return Array
 * @since 3.6.0
 */
function pewc_get_enabled_roles() {

	$roles = get_option( 'pewc_role_prices', array() );
	return $roles;

}

/**
 * Add role-based pricing fields to list of parameters to save
 * @since 3.6.0
 */
function pewc_add_roles_to_item_params( $params, $field_id ) {

	if( pewc_get_enabled_roles() ) {
		$roles = pewc_get_enabled_roles();
		foreach( $roles as $role ) {
			$params[] = 'field_price_' . $role;
		}
	}

	return $params;

}
add_filter( 'pewc_item_params', 'pewc_add_roles_to_item_params', 10, 2 );

/**
 * Add role-based pricing fields to options
 * @since 3.6.0
 */
function pewc_add_roles_after_option_params_titles( $group_id, $item_key, $item ) {

	if( pewc_get_enabled_roles() ) {

		$roles = pewc_get_enabled_roles();
		foreach( $roles as $role ) {

			printf(
				'<th class="pewc-option-extra-title"><div class="pewc-label">%s</div></th>',
				ucfirst( $role )
			);

		}

	}

}
add_action( 'pewc_after_option_params_titles', 'pewc_add_roles_after_option_params_titles', 10, 3 );

/**
 * Add role-based pricing fields to options
 * @since 3.6.0
 */
function pewc_add_roles_after_option_params( $option_count, $group_id, $item_key, $item, $key ) {

	$roles = pewc_get_enabled_roles();
	foreach( $roles as $role ) {

		$name = sprintf(
			'_product_extra_groups_%s_%s[field_options][%s]',
			$group_id,
			$item_key,
			$option_count
		);

		$role_price = isset( $item['field_options'][esc_attr( $key )]['price_' . $role] ) ? $item['field_options'][esc_attr( $key )]['price_' . $role] : '';

		?>
		<td class="pewc-option-extra">
			<input type="text" class="pewc-field-option-extra" name="<?php echo $name; ?>[price_<?php echo $role; ?>]" value="<?php echo esc_attr( $role_price ); ?>">
		</td>

<?php }

}
add_action( 'pewc_after_option_params', 'pewc_add_roles_after_option_params', 10, 5 );

/**
 * Get any role-based prices for this field by user
 * @since 3.6.0
 */
function pewc_filter_field_price_for_role( $price, $item, $product ) {

	$enabled_roles = pewc_get_enabled_roles();

	if( $enabled_roles ) {

		// Get the current user's role(s) and check for any different pricing
		$user_roles = pewc_get_current_user_roles();
		foreach( $user_roles as $role ) {

			if( ! empty( $item['field_price_' . $role] ) && in_array( $role, $enabled_roles ) ) {

				$role_price = $item['field_price_' . $role];
				// Return the lowest available role-based price for this user
				$price = min( $price, $role_price );

			}

		}

	}

	return $price;

}
add_filter( 'pewc_get_field_price_before_maybe_include_tax', 'pewc_filter_field_price_for_role', 10, 3 );

/**
 * Get any role-based prices for this field by user
 * @since 3.6.0
 */
function pewc_filter_option_price_for_role( $option_price, $option_value, $product ) {

	if( pewc_get_enabled_roles() ) {

		// Get the current user's role(s) and check for any different pricing
		$user_roles = pewc_get_current_user_roles();

		if( $user_roles ) {

			foreach( $user_roles as $role ) {

				if( ! empty( $option_value['price_' . $role] ) ) {

					$role_price = $option_value['price_' . $role];
						// Return the lowest available role-based price for this user
					$option_price = min( $option_price, $role_price );

				}

			}

		}

	}

	return $option_price;

}
add_filter( 'pewc_get_option_price_before_maybe_include_tax', 'pewc_filter_option_price_for_role', 10, 3 );

/**
 * Get the current user's roles
 * @since 3.6.0
 */
function pewc_get_current_user_roles() {

  if( is_user_logged_in() ) {

    $user = wp_get_current_user();
    $roles = ( array ) $user->roles;
    return $roles; // This returns an array

  } else {

    return false;

  }

}
