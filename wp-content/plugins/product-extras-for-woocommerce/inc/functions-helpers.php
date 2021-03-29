<?php
/**
 * Functions for exporting Product Add-Ons
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter post class
 * @since 1.4.0
 */
function pewc_filter_post_classes( $classes ) {
	global $post;
	if( is_single() && 'product' == get_post_type( $post->ID ) && pewc_has_extra_fields( $post->ID ) ) {
		$classes[] = 'has-extra-fields';
		if( pewc_has_flat_rate_field( $post->ID ) ) {
			$classes[] = 'has-flat-rate';
		}
	}
	return $classes;
}
add_filter( 'post_class', 'pewc_filter_post_classes' );

/**
 * Filter body class
 * @since 1.7.0
 */
function pewc_filter_body_classes( $classes ) {
	global $post;
	if( isset( $post->ID ) && 'product' == get_post_type( $post->ID ) && pewc_has_extra_fields( $post->ID ) ) {
		$product = wc_get_product( $post->ID );
		$classes[] = 'pewc-has-extra-fields';
		if( $product->get_type() == 'variable' ) {
			$classes[] = 'pewc-variable-product';
		}
		if( pewc_has_flat_rate_field( $post->ID ) ) {
			$classes[] = 'has-flat-rate';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'pewc_filter_body_classes' );

/**
 * Get the product's extra fields
 * @since 1.6.0
 * @param $post_id
 * @return Array
 */
function pewc_get_extra_fields( $post_id ) {

	$group_transient = get_transient( 'pewc_extra_fields_' . $post_id );

	// An empty array means the product has no fields.
	// We don't need to check it again
	if( is_array( $group_transient ) ) {

		$product_extra_groups = $group_transient;

	} else {

		$has_migrated = pewc_has_migrated();

		if( ! $has_migrated ) {
			// This is the old, pre-3.0.0 method and will be deprecated in future versions
			$product_extra_groups = get_post_meta( $post_id, '_product_extra_groups', true );

		} else {
			// This is the post-3.0.0 method using post types
			// However, it still returns a big groups array like the old method for backwards compatibility
			$product_extra_groups = pewc_get_pewc_groups( $post_id );
		}

		set_transient( 'pewc_extra_fields_' . $post_id, $product_extra_groups, pewc_get_transient_expiration() );

	}

	// Filter the groups
	// Only filter on the front end, since 3.7.24
	if( ! is_admin() || wp_doing_ajax() ) {
		$product_extra_groups = apply_filters( 'pewc_filter_product_extra_groups', $product_extra_groups, $post_id );
	}

	return $product_extra_groups;

}

/**
 * Get child groups for this product
 * @since 3.0.0
 * @return Array
 */
function pewc_get_pewc_groups( $post_id ) {

	$groups = array();
	$group_ids = pewc_get_group_order( $post_id );
	// Iterate through the group IDs and build a big array
	if( $group_ids ) {
		$group_ids = explode( ',', $group_ids );
		foreach( $group_ids as $index=>$group_id ) {
			// Confirm that the group ID is an actual group
			if( 'publish' === get_post_status( $group_id ) ) {
				$groups[$group_id]['items'] = pewc_get_group_fields( $group_id );
			}
		}
	}

	return $groups;

}


/**
 * Get global a correctly formatted global group
 * Post 3.0.0 this is passed a group ID
 * @since 3.0.0
 * @param $group_param Mixed Either integer or array
 * @return Array
 */
function pewc_get_global_groups( $group_param ) {
	$has_migrated = pewc_has_migrated();
	if( ! $has_migrated ) {
		// This is the old, pre-3.0.0 method and will be deprecated in future versions
		return $group_param;
	} else {
		// This is the post-3.0.0 method using post types
		// We want it to return a big groups array like the old method for backwards compatibility
		$group['items'] = pewc_get_group_fields( $group_param );
		return $group;
	}
}

/**
 * Get the list of all global group IDs
 * @since 3.3.0
 * @return List
 */
function pewc_get_all_global_group_ids() {

	if( pewc_is_group_public() != 'yes' ) {

		$global_order = get_option( 'pewc_global_group_order' );

	} else {

		$global_order = get_transient( 'pewc_global_order' );

		if( ! $global_order ) {

			// Get all groups with no parent
			$args = array(
				'post_type'				=> 'pewc_group',
				'post_parent'			=> 0,
				'fields'					=> 'ids',
				'posts_per_page'	=> 999,
				'orderby'					=> 'menu_order',
				'order'						=> 'ASC'
			);
			$groups = new WP_Query( $args );
			$global_order = join( ',', $groups->posts );

			//
			set_transient( 'pewc_global_order', $global_order, pewc_get_transient_expiration() );

		}

	}

	return $global_order;

}

/**
 * Set the list of all global group IDs
 * @since 3.5.0
 */
function pewc_set_global_group_ids( $group_id ) {

	// Get all groups with no parent
	$args = array(
		'post_type'				=> 'pewc_group',
		'post_parent'			=> 0,
		'fields'					=> 'ids',
		'posts_per_page'	=> 999,
		'orderby'					=> 'menu_order',
		'order'						=> 'ASC'
	);

	// For WPML
	if( function_exists( 'icl_object_id' ) ) {
    $args['suppress_filters'] = true;
	}

	$groups = new WP_Query( $args );
	$global_order = join( ',', $groups->posts );

	update_option( 'pewc_global_group_order', $global_order );

}
add_action( 'pewc_after_save_group_metabox_data', 'pewc_set_global_group_ids' );

/**
 * Get the display order of groups for this product
 * @since 3.0.0
 * @return String
 */
function pewc_get_group_order( $product_id ) {
	$order = get_post_meta( $product_id, 'group_order', true );
	return $order;
}

/**
 * Get child fields for this group
 * @since 3.0.0
 * @return Array
 */
function pewc_get_group_fields( $group_id ) {
	$all_fields = array();
	$fields = get_post_meta( $group_id, 'field_ids', true );
	if( $fields ) {
		foreach( $fields as $field_id ) {
			// Confirm that the field ID is an actual field
			if( 'publish' === get_post_status( $field_id ) ) {
				$all_fields[$field_id] = pewc_create_item_object( $field_id );
			}
		}
	}
	return $all_fields;
}

/**
 * Before 3.0.0, field data was stored as a serialised array
 * This function just gets our post meta and formats it in an array so we can continue using pre-3.0 templates
 * @since 3.0.0
 * @return Array
 */
function pewc_create_item_object( $field_id ) {

	$item = get_transient( 'pewc_item_object_' . $field_id );

	if( ! $item ) {

		$item = array(
			'field_id' 	=> $field_id
		);
		$params = pewc_get_field_params( $field_id );

		$all_params = get_post_meta( $field_id, 'all_params', true );

		if( ! empty( $all_params ) ) {
			$item = $all_params;
		} else {
			if( $params ) {
				foreach( $params as $param ) {
					$value = get_post_meta( $field_id, $param, true );
					$item[$param] = $value;
				}
			}
		}

		set_transient( 'pewc_item_object_' . $field_id, $item, pewc_get_transient_expiration() );

	}

	$item = apply_filters( 'pewc_item_object', $item, $field_id );

	return $item;

}

/**
 * Returns a list of all params for a field
 * @since 3.0.0
 * @return Array
 */
function pewc_get_field_params( $field_id=null ) {

	$params = array(
		'id',
		'group_id',
		'field_label',
		'field_type',
		'field_price',
		'field_options',
		'first_field_empty',
		'field_minchecks',
		'field_maxchecks',
		'child_products',
		'products_layout',
		'products_quantities',
		'allow_none',
		'number_columns',
		'hide_labels',
		'allow_multiple',
		'select_placeholder',
		'min_products',
		'max_products',
		'child_discount',
		'discount_type',
		'field_required',
		'field_flatrate',
		'field_percentage',
		'field_minchars',
		'field_maxchars',
		'per_character',
		'field_freechars',
		'field_alphanumeric',
		'field_alphanumeric_charge',
		'field_minval',
		'field_maxval',
		'multiply',
		'min_date_today',
		'field_mindate',
		'field_maxdate',
		'field_color',
		'field_width',
		'field_show',
		'field_palettes',
		'field_default',
		'field_default_hidden',
		'field_image',
		'field_description',
		'condition_action',
		'condition_match',
		'condition_field',
		'condition_rule',
		'condition_value',
		'variation_field',
		'formula',
		'formula_action',
		'formula_round',
		'decimal_places',
		'field_rows',
		'multiple_uploads',
		'max_files',
		'multiply_price',
		'hidden_calculation'
	);

	return apply_filters( 'pewc_item_params', $params, $field_id );

}

/**
 * Returns the group title
 * @since 3.0.0
 * @return Array
 */
function pewc_get_group_title( $group_id, $group, $has_migrated ) {

	$group_title = '';

	if( $has_migrated ) {
		$group_title = get_post_meta( $group_id, 'group_title', true );
	} else if( isset( $group['meta']['group_title'] ) ) {
		$group_title = $group['meta']['group_title'];
	}

	return apply_filters( 'pewc_get_group_title', $group_title, $group_id, $has_migrated );

}

/**
 * Returns the group title
 * @since 3.0.0
 * @return Array
 */
function pewc_get_group_description( $group_id, $group, $has_migrated ) {
	$group_description = '';
	if( $has_migrated ) {
		$group_description = get_post_meta( $group_id, 'group_description', true );
	} else if( isset( $group['meta']['group_description'] ) ) {
		$group_description = $group['meta']['group_description'];
	}

	return apply_filters( 'pewc_get_group_description', $group_description, $group_id, $has_migrated );
}

/**
 * Returns the group layout
 * @since 3.1.1
 * @return Array
 */
function pewc_get_group_layout( $group_id ) {
	$group_layout = get_post_meta( $group_id, 'group_layout', true );
	if( ! $group_layout ) $group_layout = 'ul';
	return apply_filters( 'pewc_get_group_layout', $group_layout, $group_id );
}

/**
 * Returns the group condition action
 * @since 3.8.0
 * @return Array
 */
function pewc_get_group_condition_action( $group_id, $group ) {

	$group_action = get_post_meta( $group_id, 'condition_action', true );
	return $group_action;

}

/**
 * Returns the group condition match
 * @since 3.8.0
 * @return Array
 */
function pewc_get_group_condition_match( $group_id ) {

	$condition_match = get_post_meta( $group_id, 'condition_match', true );
	return $condition_match;

}

/**
 * Returns the group conditions
 * @since 3.8.0
 * @return Array
 */
function pewc_get_group_conditions( $group_id ) {

	$conditions = get_post_meta( $group_id, 'conditions', true );
	return $conditions;

}

/**
 * Get all the fields on the page which will trigger a group condition
 * Maps field IDs to group
 * @since 3.8.0
 * @return Array
 */
function pewc_get_all_group_conditions_fields( $group_ids ) {

	$conditions_fields = array();
	if( $group_ids ) {
		foreach( $group_ids as $group_id ) {
			$conditions = pewc_get_group_conditions( $group_id );
			if( $conditions ) {
				foreach( $conditions as $condition ) {
					if( ! isset( $conditions_fields[$condition['field']] ) ) {
						$conditions_fields[$condition['field']] = array( $group_id );
					} else {
						$conditions_fields[$condition['field']][] = $group_id;
					}
				}
			}
		}
	}

	return $conditions_fields;

}

/**
 * Get all the fields on the page which a field is conditional on
 * @since 3.9.0
 * @return Array
 */
function pewc_get_all_field_conditions_fields( $groups, $product_id ) {

	$conditions_fields = array();

	if( $groups ) {
		foreach( $groups as $group_id=>$group ) {
			if( isset( $group['items'] ) ) {
				foreach( $group['items'] as $field_id=>$field ) {

					$conditions = get_post_meta( $field_id, 'condition_field', true );
					$field_ids = array();
					if( $conditions ) {
						foreach( $conditions as $condition ) {
							// Get the field ID
							$condition = explode( '_', $condition );
							if( isset( $condition[3] ) ) {
								$field_ids[] = $condition[3];
							}
						}
						$conditions_fields[$field_id] = $field_ids;
					}

				}
			}
		}
	}

	return $conditions_fields;

}

/**
 * Get an array of each field's conditions that we can use on the front end
 * @return Array
 * @since 3.9.0
 */
function pewc_get_conditions_by_field_id( $groups, $product_id ) {

	$field_conditions = array();

	if( $groups ) {
		foreach( $groups as $group_id=>$group ) {
			if( isset( $group['items'] ) ) {
				foreach( $group['items'] as $field_id=>$field ) {
					$conditions = pewc_get_field_conditions( $field, $product_id );
					if( $conditions ) {
						// Get the field type for each condition
						foreach( $conditions as $condition_id=>$condition ) {
							$condition_field = explode( '_', $condition['field'] );
							$condition_field_id = isset( $condition_field[3] ) ? $condition_field[3] : false;
							if( $condition_field_id ) {
								$conditions[$condition_id]['field_type'] = get_post_meta( $condition_field[3], 'field_type', true );
							} else {
								$conditions[$condition_id]['field_type'] = $condition['field'];
							}

						}

						$field_conditions[$field_id] = $conditions;

					}
				}
			}
		}
	}

	return $field_conditions;

}

/**
 * Get fields which fields are conditional on
 * If field 1234 has a condition to display if field 4567 is checked, then 4567 is a trigger for 1234
 * @return Array
 * @since 3.9.0
 */
function pewc_get_all_conditional_triggers( $all_field_conditions, $post_id ) {

	$triggers = array();
	if( $all_field_conditions ) {
		foreach( $all_field_conditions as $field_id=>$field_triggers ) {
			$triggers = array_merge( $triggers, array_values( $field_triggers ) );
		}
	}

	return $triggers;

}

/**
 * Get a list of field IDs that each field is a trigger for
 * If field 1234 is a trigger for field 4567, then add 4567 to $triggers_for[1234]
 * @return Array
 * @since 3.9.0
 */
function pewc_get_triggers_for_fields( $all_field_conditions, $post_id ) {

	$triggers_for = array();
	if( $all_field_conditions ) {
		foreach( $all_field_conditions as $field_id=>$field_triggers ) {
			if( isset( $field_triggers ) ) {
				foreach( $field_triggers as $trigger_id=>$field_trigger ) {
					if( isset( $triggers_for[$field_trigger] ) ) {
						$triggers_for[$field_trigger][] = $field_id;
					} else {
						$triggers_for[$field_trigger] = array( $field_id );
					}
				}
			}

		}
	}

	return $triggers_for;

}

/**
 * Get fields triggered by cost and quantity conditions
 * @return Array
 * @since 3.9.0
 */
function pewc_get_triggered_by_field_type( $field_conditions, $type ) {

	$triggered_by = array();
	if( $field_conditions ) {
		foreach( $field_conditions as $field_id=>$field_triggers ) {
			if( $field_triggers ) {
				foreach( $field_triggers as $trigger_id=>$trigger ) {
					if( isset( $trigger['field_type'] ) && $trigger['field_type'] == $type ) {
						$triggered_by[] = $field_id;
					}
				}
			}
		}
	}

	return $triggered_by;

}

/**
 * Get all the fields on the page which are a component of a calculation field
 * @since 3.8.0
 * @return Array
 */
function pewc_get_all_calculation_components( $groups ) {
	$components = array();
	if( $groups ) {
		foreach( $groups as $group_id=>$group ) {
			if( $group['items'] ) {
				foreach( $group['items'] as $field_id=>$field ) {

					if( isset( $field['field_type'] ) && $field['field_type'] == 'calculation' ) {
						$formula = isset( $field['formula'] ) ? $field['formula'] : false;
						$formula = str_replace( '_field_price', '', $formula );

						if( $formula ) {

							if( $formula == '{look_up_table}' ) {

								// Find the elements for the look up table
								$lookup_fields = apply_filters( 'pewc_calculation_look_up_fields', array() );
								if( isset( $lookup_fields[$field_id][1] ) ) {
									$component_id = $lookup_fields[$field_id][1];
									if( isset( $components[$field_id] ) ) {
										$components[$component_id][] = $field_id;
									} else {
										$components[$component_id] = array( $field_id );
									}
								}
								if( isset( $lookup_fields[$field_id][2] ) ) {
									$component_id = $lookup_fields[$field_id][2];
									if( isset( $components[$field_id] ) ) {
										$components[$component_id][] = $field_id;
									} else {
										$components[$component_id] = array( $field_id );
									}
								}

							} else {

								// Component field ID => Calculation field ID
								$last_pos = 0;
								$opening_pos = 0;
								$positions = array();

								while( ( $last_pos = strpos( $formula, 'field_', $last_pos ) ) !== false ) {
							    $positions[] = $last_pos;
									$closing_pos = strpos( $formula, '}', $last_pos );
									$component_id = substr( $formula, $last_pos, $closing_pos-$last_pos );
									$component_id = str_replace( array( 'field_', '_option_price', '_field_price' ), '', $component_id );

									// $components works like this:
									// $component_id is the input field => $field_id is the field containing the calculation

									if( isset( $components[$field_id] ) ) {
										$components[$component_id][] = $field_id;
									} else {
										$components[$component_id] = array( $field_id );
									}
									$last_pos = $last_pos + strlen( 'field_' );
								}

							}

						}

					}
				}
			}
		}
	}
	return $components;
}

/**
 * Returns the field condition action
 * @since 3.9.0
 * @return Array
 */
function pewc_get_field_condition_action( $field_id, $field ) {

	$field_action = get_post_meta( $field_id, 'condition_action', true );
	return $field_action;

}

/**
 * Returns the field condition match
 * @since 3.9.0
 * @return Array
 */
function pewc_get_field_condition_match( $field_id ) {

	$field_match = get_post_meta( $field_id, 'condition_match', true );
	return $field_match;

}

/**
 * Returns the global group rules
 * @since 3.0.0
 * @return Array
 */
function pewc_get_global_rules( $group_id, $group ) {
	$has_migrated = pewc_has_migrated();
	if( $has_migrated ) {
		$rules = get_post_meta( $group_id, 'global_rules', true );
	} else {
		$rules = isset( $group['global_rules'] ) ? $group['global_rules'] : false;
	}
	return $rules;
}

/**
 * Returns the global group operator
 * @since 3.0.0
 * @return Array
 */
function pewc_get_group_operator( $group_id, $group ) {
	$rules = pewc_get_global_rules( $group_id, $group );
	$operator = ( isset( $rules['operator'] ) && $rules['operator'] == 'any' ) ? 'any' : 'all';
	return $operator;
}

/**
 * Check if this product has extra fields
 * @since 1.4.0
 * @return Boolean
 */
function pewc_has_extra_fields( $product_id ) {
	return pewc_has_product_extra_groups( $product_id ) == 'yes' ? true : false;
	// $has_extra = get_post_meta( $product_id, 'has_addons', true );
	// return $has_extra;
}

/**
 * Check if this cart item has extra fields
 * @since 3.7.10
 * @return Boolean
 */
function pewc_cart_item_has_extra_fields( $cart_item ) {
	$has_extra = ! empty( $cart_item['product_extras']['groups'] ) ? true : false;
	return $has_extra;
}

function pewc_get_group_id( $id ) {
	// Work out group and field IDs from the $id
	$last_index = strrpos( $id, '_' );
	$field_id = substr( $id, $last_index + 1 ); // Find last instance of _
	$group_id = substr( $id, 0, $last_index ); // Remove _field_id from $id
	//$field_id = str_replace( '_', '', $field_id );
	$group_id = strrchr( $group_id, '_' );
	$group_id = str_replace( '_', '', $group_id );
	return $group_id;
}

function pewc_get_field_id( $id ) {
	// Work out group and field IDs from the $id
	$last_index = strrpos( $id, '_' );
	$field_id = substr( $id, $last_index + 1 ); // Find last instance of _
	return $field_id;
}

function pewc_get_field_type( $id, $items ) {
	if( $items ) {
		foreach( $items as $item ) {
			if( $item['id'] == $id ) {
				$field_type = $item['field_type'];
				return $field_type;
			}
		}
	}
	return '';
}

/**
 * Can we edit global groups as post types?
 * Disables the pre-3.2.20 Global Add-Ons page
 */
 function pewc_enable_groups_as_post_types() {
	 $display = get_option( 'pewc_enable_groups_as_post_types', 'no' );
	 return apply_filters( 'pewc_enable_groups_as_post_types', $display=='yes' );
 }

 /**
  * Can we edit fields as post types?
  * Disables the pre-3.2.20 Global Add-Ons page
	* @since 3.6.0
  */
  function pewc_enable_fields_as_post_types() {
 	 return apply_filters( 'pewc_enable_fields_as_post_types', false );
  }

/**
 * Abbreviated form of wc_price
 * @param $price	Price
 * @param $args		Args
 * @return HTML
 */
function pewc_wc_format_price( $price, $args=array() ) {
	extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
		'ex_tax_label'       => false,
		'currency'           => '',
		'decimal_separator'  => wc_get_price_decimal_separator(),
		'thousand_separator' => wc_get_price_thousand_separator(),
		'decimals'           => wc_get_price_decimals(),
		'price_format'       => get_woocommerce_price_format(),
	) ) ) );
	$negative = $price < 0;
	$price = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
		$price = wc_trim_zeros( $price );
	}
	$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $currency ) . '</span>', $price );
	return $formatted_price;
}

/**
 * Check if this product has a flat rate field
 * @since 1.4.0
 * @return Boolean
 */
function pewc_has_flat_rate_field( $product_id ) {
	$product_extra_groups = pewc_get_extra_fields( $product_id );
	if( ! empty( $product_extra_groups ) ) {
		foreach( $product_extra_groups as $group ) {
			if( ! empty( $group['items'] ) ) {
				foreach( $group['items'] as $key=>$item ) {
					if( ! empty( $item['field_flatrate'] ) ) {
						return true;
					}
				}
			}
		}
	}
	return false;
}

/**
 * Return attributes for text or textarea field
 * @since 2.1.0
 * @return Array
 */
function pewc_get_text_field_attributes( $item ) {
	$attributes = array(
		'data-minchars'							=> ! empty( $item['field_minchars'] ) ? $item['field_minchars'] : '',
		'data-maxchars'							=> ! empty( $item['field_maxchars'] ) ? $item['field_maxchars'] : '',
		'data-freechars'						=> '0',
		'data-alphanumeric'					=> '',
		'data-alphanumeric-charge'	=> '',
	);
	if( pewc_is_pro() ) {
		$attributes['data-freechars'] = ! empty( $item['field_freechars'] ) ? $item['field_freechars'] : '';
		$attributes['data-alphanumeric'] = ! empty( $item['field_alphanumeric'] ) ? $item['field_alphanumeric'] : '';
		$attributes['data-alphanumeric-charge'] = ! empty( $item['field_alphanumeric_charge'] ) ? $item['field_alphanumeric_charge'] : '';
	}
	$attributes = apply_filters( 'pewc_filter_text_field_attributes', $attributes, $item );
	$return = '';
	if( $attributes ) {
		foreach( $attributes as $attribute=>$value ) {
			$return .= $attribute . '="' . $value . '" ';
		}
	}

	return $return;
}

/**
 * Return attributes for color picker field.
 *
 * @since   3.7.7
 * @version 3.7.7
 *
 * @param   array   $item
 *
 * @return  string
 */
function pewc_get_color_field_attributes( $item ) {
    $return = '';

    $attributes = array(
        'data-color'        => ! empty( $item['field_color'] ) ? $item['field_color'] : '',
        'data-box-width'    => ! empty( $item['field_width'] ) ? $item['field_width'] : '',
        'data-show'         => ! empty( $item['field_show']) ? 'true' : 'false',
        'data-palettes'     => ! empty( $item['field_palettes']) ? 'true' : 'false'
    );

    $attributes = apply_filters( 'pewc_filter_color_picker_field_attributes', $attributes, $item );
    if( $attributes ) {
        foreach( $attributes as $attribute => $value ) {
            $return .= $attribute . '="' . $value . '" ';
        }
    }

    return $return;
}

/**
 * Get a formatted price but without any HTML
 */
function pewc_get_semi_formatted_price( $child_product ) {
	$price = $child_product->get_price();
	$semi_formatted_price = $price;
	$negative = $price < 0;
	$price = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ), $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && wc_get_price_decimals() > 0 ) {
		$price = wc_trim_zeros( $price );
	}
	$semi_formatted_price = ( $negative ? '-' : '' ) . sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $price );
	return $semi_formatted_price;
}

/**
 * Get a formatted price without any HTML for a price string
 */
function pewc_get_semi_formatted_raw_price( $price ) {
	$semi_formatted_price = $price;
	$negative = $price < 0;
	$price = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
	$price = apply_filters( 'formatted_woocommerce_price', number_format( $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() ), $price, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
	if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && wc_get_price_decimals() > 0 ) {
		$price = wc_trim_zeros( $price );
	}
	$semi_formatted_price = ( $negative ? '-' : '' ) . sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $price );
	return $semi_formatted_price;
}

/**
 * Get all simple products
 */
function pewc_get_simple_products() {
	$args = array(
		'type'		=> 'simple',
		'return'	=> 'ids',
		'limit'		=> 999
	);
	$products = wc_get_products( $args );
	return $products;
}

/**
 * Check whether we're displaying prices with tax or not
 */
function pewc_maybe_include_tax( $product, $price ) {

	// global $product;
	$ignore = get_option( 'pewc_ignore_tax', 'no' );
	if ( $price === '' || $price == '0' || $ignore == 'yes' ) {
		return $price;
	}

	$is_negative = ( $price < 0 ) ? true : false;
	if( $is_negative ) {
		return $price;
	}

	if( is_object( $product ) ) {
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$display_price = $tax_display_mode == 'incl' ? wc_get_price_including_tax( $product, array( 'price' => $price, 'qty' => 1 ) ) : wc_get_price_excluding_tax( $product, array( 'price' => $price, 'qty' => 1 ) );
	} else {
		$display_price = $price;
	}

	return $display_price;

}

/**
 * We might need to remove tax from the add-ons so that tax isn't doubled in the cart
 */
function pewc_get_price_without_tax( $price, $product ) {

	// global $product;
	$ignore = get_option( 'pewc_ignore_tax', 'no' );
	if ( $price === '' || $price == '0' || $ignore == 'yes' ) {
		// No tax has been added here
		return $price;
	}

	$is_negative = ( $price < 0 ) ? true : false;
	if( $is_negative ) {
		return $price;
	}

	// Taken from wc_get_price_excluding_tax
	$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
	$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );
	$remove_taxes   = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $price, $base_tax_rates, true ) : WC_Tax::calc_tax( $price, $tax_rates, true );
	$return_price   = $price - array_sum( $remove_taxes );

	return $return_price;

}

/**
 * Do we need to remove the tax from the add-on field price?
 */
function pewc_adjust_tax() {

	$adjust = false;

	$tax_display = get_option( 'woocommerce_tax_display_cart' );

	if( ( ! wc_prices_include_tax() && $tax_display == 'incl' ) ) {

		// We need to remove tax if prices are entered exclusive of tax but prices are displayed including tax
		$adjust = 'remove';

	} else if( wc_prices_include_tax() && $tax_display == 'excl' ) {

		// We need to add tax if prices are entered including tax but prices are displayed without tax
		$adjust = 'add';

	}

	return apply_filters( 'pewc_adjust_tax', $adjust );

}

/**
 * Get the adjusted add-on field price in the cart
 */
function pewc_get_adjusted_product_addon_price( $cart_item, $cart_key ) {

	$new_price = apply_filters( 'pewc_filter_calculated_cost_before_calculate_totals', $cart_item['product_extras']['price_with_extras'], $cart_item, $cart_key );

	if( apply_filters( 'pewc_ignore_tax_adjustments', false, $cart_item ) || ! wc_tax_enabled() ) {
		return $new_price;
	}

	// Do we need the price without tax?
	$adjust_tax = pewc_adjust_tax();

	$original_price = isset( $cart_item['product_extras']['original_price'] ) ? $cart_item['product_extras']['original_price'] : $new_price;
	$original_extras = $new_price - $original_price;

	if( $adjust_tax == 'remove' ) {

		if( ! empty( $cart_item['product_extras']['use_calc_set_price'] ) ) {
			// Just use the calculated price
			$new_price = pewc_get_price_without_tax( $new_price, $cart_item['data'] );
		} else {
			// Strip the tax off the extras to get a price without tax
			$extras_without_tax = pewc_get_price_without_tax( $original_extras, $cart_item['data'] );
			$original_extras = pewc_get_price_without_tax( $new_price, $cart_item['data'] );
			$new_price = $original_price + $extras_without_tax;
		}

	} else if( $adjust_tax == 'add' ) {

		$original_extras = $original_extras * 1.2;
		$new_price = $original_price + $original_extras;

	}

	return $new_price;

}

/**
 * Check if product has a calculation field
 */
function pewc_has_calculation_field( $product_id ) {

	if( ! pewc_is_pro() ) return false;

	$has_calculation = false;
	$groups = pewc_get_extra_fields( $product_id );
	foreach( $groups as $group ) {
		if( isset( $group['items'] ) ) {
			foreach( $group['items'] as $field ) {
				if( isset( $field['field_type'] ) && $field['field_type'] == 'calculation' ) {
					$has_calculation = true;
					break;
				}
			}
		}
	}
	return $has_calculation;
}

/**
 * Check if product has a color picker field
 */
function pewc_has_color_picker_field( $product_id ) {
  $has_color_picker = false;
  $groups = pewc_get_extra_fields( $product_id );
  foreach( $groups as $group ) {
    if( isset( $group['items'] ) ) {
      foreach( $group['items'] as $field ) {
        if( isset( $field['field_type'] ) && $field['field_type'] == 'color-picker' ) {
          $has_color_picker = true;
          break;
      	}
      }
    }
  }
  return $has_color_picker;
}

/**
 * Have we enabled DropZone.js uploads?
 */
function pewc_enable_ajax_upload() {
	$enable_js = get_option( 'pewc_enable_dropzonejs', 'no' );
	return apply_filters( 'pewc_enable_dropzonejs', $enable_js );
}

function pewc_get_max_upload() {
	$pewc_max_upload = get_option( 'pewc_max_upload', 1 );
	return apply_filters( 'pewc_filter_max_upload', $pewc_max_upload );
}

/**
 * Get a list of all subscription variations
 * @return Array
 */
function pewc_get_subscription_variations() {

	$variations = array();

	$args = array(
		'type'		=> 'variable-subscription',
		'limit'		=> -1,
		'return'	=> 'ids'
	);
	$query = new WC_Product_Query( $args );
	$variable_subscriptions = $query->get_products();

	if( $variable_subscriptions ) {

		foreach( $variable_subscriptions as $variable_subscription ) {

			$variation = new WC_Product_Variable( $variable_subscription );
			$available_variations = $variation->get_available_variations();

			if( $available_variations ) {

				foreach( $available_variations as $available_variation ) {

					$v = wc_get_product( $available_variation['variation_id'] );
					$variations[$available_variation['variation_id']] = $v->get_name();

				}

			}

		}

	}

	return $variations;

}

/**
 * Is product add-on editing enabled?
 * @return Boolean
 */
function pewc_user_can_edit_products() {

	if( ! pewc_is_pro() ) {
		return false;
	}

	$can_edit = false;
	if( get_option( 'pewc_enable_cart_editing', 'no' ) == 'yes' ) {
		$can_edit = true;
	}

	return apply_filters( 'pewc_user_can_edit_products', $can_edit );

}

/**
 * Return some HTML for the image in an image swatch field
 * @param	$option_value	Array
 * @since 3.5.0
 */
function pewc_get_swatch_image_html( $option_value, $item ) {

	if( empty( $option_value['image'] ) ) {
		return wc_placeholder_img( apply_filters( 'pewc_image_swatch_thumbnail_size', 'thumbnail', $item ) );
	}

	$attachment_id = $option_value['image'];

	$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
	$full_src = wp_get_attachment_image_src( $attachment_id, $full_size );

	$image = wp_get_attachment_image(
		$attachment_id,
		apply_filters( 'pewc_image_swatch_thumbnail_size', 'thumbnail', $item ),
		false,
		array(
			'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
			'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
			'data-src'                => esc_url( $full_src[0] ),
			'data-large_image'        => esc_url( $full_src[0] ),
			'data-large_image_width'  => esc_attr( $full_src[1] ),
			'data-large_image_height' => esc_attr( $full_src[2] )
		)
	);

	return $image;
}

/**
 * Return the URL for an image in an image swatch field
 * @param	$image Array
 * @since 3.7.1
 */
function pewc_get_swatch_image_url( $option_value, $item ) {

	if( empty( $option_value['image'] ) ) {
		return array( wc_placeholder_img_src( apply_filters( 'pewc_image_swatch_thumbnail_size', 'thumbnail', $item ) ) );
	}

	$attachment_id = $option_value['image'];

	$size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'pewc_image_swatch_thumbnail_size', 'thumbnail', $item ) );
	$src = wp_get_attachment_image_src( $attachment_id, $size );

	return $src;

}

/**
 * Return renaming options for uploads
 * @since 3.7.0
 */
function pewc_get_rename_uploads() {

	$rename = get_option( 'pewc_rename_uploads', false );
	return $rename;

}

/**
 * Organise uploads into unique folders per order?
 * @since 3.7.0
 */
function pewc_get_pewc_organise_uploads() {

	$organise = get_option( 'pewc_organise_uploads', 'no' );
	return $organise;

}

function pewc_enable_pdf_uploads() {

	$enable = get_option( 'pewc_enable_pdf_uploads', 'no' );
	return $enable;

}

/**
 * Show the tax suffix after all add-on prices?
 * @since 3.7.15
 */
function pewc_show_price_suffix() {
	$enable = get_option( 'pewc_tax_suffix', 'no' );
	return $enable;
}

/**
 * Show the tax suffix after all add-on prices?
 * @since 3.7.15
 */
function pewc_reset_hidden_fields( $post_id ) {
	$reset = get_option( 'pewc_reset_fields', 'no' );
	return apply_filters( 'pewc_reset_hidden_fields', $reset, $post_id );
}

/**
 * Optimise conditions?
 * @since 3.8.7
 */
function pewc_conditions_timer( $time ) {
	$optimise = get_option( 'pewc_optimise_conditions', 'no' );
	if( $optimise == 'yes' ) {
		$time = 500;
	}
	return $time;
}
add_filter( 'pewc_conditions_timer', 'pewc_conditions_timer' );

/**
 * Optimise conditions?
 * @since 3.8.7
 */
function pewc_calculations_timer( $time ) {
	$optimise = get_option( 'pewc_optimise_calculations', 'no' );
	if( $optimise == 'yes' ) {
		$time = 500;
	}
	return $time;
}
add_filter( 'pewc_calculations_timer', 'pewc_calculations_timer' );
