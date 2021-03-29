<?php
/**
 * Functions for setting up the Product Add-Ons conditions
 * @since 1.1.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all the product extra fields for a product or group
 * Use this to populate the field parameter in conditionals
 * @param $group 			Group data
 * @param $is_ajax		Are we loading add-ons via AJAX?
 * @param $product_id	Only passed from product page
 * @return Array
 */
function pewc_get_all_fields( $group=false, $is_ajax=false, $product_id=false ) {

	$fields = array( 'not-selected' => __( ' -- Select a field -- ', 'pewc' ) );

	if( $is_ajax || ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'product' ) ) {

		// Product
		if( ! $product_id ) {
			$product_id = $_GET['post'];
		}

		$groups = pewc_get_extra_fields( $product_id );

		if( $groups ) {
			foreach( $groups as $group ) {
				if( ! empty( $group['items'] ) ) {
					foreach( $group['items'] as $item ) {
						$label = ! empty( $item['field_label'] ) ? $item['field_label'] : __( '[no label]', 'pewc' );
						if( ! empty( $item['id'] ) ) {
							$fields[$item['id']] = $label;
						}
					}
				}
			}
		}

	} else if( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'pewc_group' ) {

		// Group
		if( ! empty( $group ) ) {
			foreach( $group as $item ) {
				$label = ! empty( $item['field_label'] ) ? $item['field_label'] : __( '[no label]', 'pewc' );
				$fields[$item['id']] = $label;
			}
		}

	} else if( $group ) {

		// If $group is passed, we are on the global extras
		// @since 2.2.3 use all global fields
		// Check if we've migrated @since 3.0
		if( ! pewc_has_migrated() ) {
			// Pre 3.0
			$globals = get_option( 'pewc_global_extras' );
			if( $globals ) {
				foreach( $globals as $group ) {
					foreach( $group['items'] as $item_key=>$item ) {
						$label = ! empty( $item['field_label'] ) ? $item['field_label'] : __( '[no label]', 'pewc' );
						$fields[$item['id']] = $label;
					}
				}
			}
		} else {

			// Post 3.0
			$group_order = get_option( 'pewc_global_group_order', '' );
			if( $group_order ) {
				// pewc_display_product_groups expects an array with the group_id as the key
				$new_order = explode( ',', $group_order );
				$combined_order = array_combine( $new_order, $new_order );
				foreach( $combined_order as $group_id ) {
					$group['items'] = pewc_get_group_fields( $group_id );
					foreach( $group['items'] as $item_key=>$item ) {
						$label = ! empty( $item['field_label'] ) ? $item['field_label'] : __( '[no label]', 'pewc' );
						$fields[$item['id']] = $label;
					}
				}
			}

		}

	}

	$fields['cost'] = __( 'Cost', 'pewc' );
	$fields['quantity'] = __( 'Quantity', 'pewc' );

	return apply_filters( 'pewc_conditional_fields', $fields );

}

/**
 * Conditional actions
 * @return Array
 */
function pewc_get_actions() {
	$actions = array(
		'hide'		=> __( 'Hide this field if', 'pewc' ),
		'show'		=> __( 'Show this field if', 'pewc' )
	);
	return $actions;
}

/**
 * Conditional conditions, yes really
 * @return Array
 */
function pewc_get_matches() {
	$matches = array(
		'all'		=> __( 'All rules match', 'pewc' ),
		'any'		=> __( 'Any rule matches', 'pewc' )
	);
	return $matches;
}

/**
 * Conditional rules
 * @return Array
 */
function pewc_get_rules() {
	$rules = array(
		'is'									=> __( 'Is', 'pewc' ),
		'is-not'							=> __( 'Is Not', 'pewc' ),
		'contains'						=> __( 'Contains', 'pewc' ),
		'does-not-contain'		=> __( 'Does Not Contain', 'pewc' ),
		'cost-equals'					=> __( 'Equals', 'pewc' ),
		'cost-greater'				=> __( 'Greater Than', 'pewc' ),
		'cost-less'						=> __( 'Less Than', 'pewc' ),
		'greater-than-equals'	=> '>=',
		'less-than-equals'		=> '<='
	);
	return $rules;
}

/**
 * Return conditional rules for each item
 * @param $item
 * @return Array
 */
function pewc_get_field_conditional( $item, $product_id ) {

	// Don't bother running the rest if we don't have any conditions
	if( empty( $item['condition_field'] ) ) {
		return false;
	}

	$id = isset( $item['id'] ) ? $item['id'] : false;
	if( ! $id ) {
		return;
	}

	$rules = get_transient( 'pewc_rules_transient_' . $id );

	if( ! $rules ) {

		$group_id = pewc_get_group_id( $id );
		$field_id = pewc_get_field_id( $id );
		$rules = array();
		$groups = pewc_get_extra_fields( $product_id );

		$rules = array();
		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_action'] ) ) {
			$rules['action'] = $groups[$group_id]['items'][$field_id]['condition_action'];
		}
		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_match'] ) ) {
			$rules['match'] = $groups[$group_id]['items'][$field_id]['condition_match'];
		}

		$field = '';
		$rule = '';
		$value = '';
		$key = '';
		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_field'] ) ) {
			$field = $groups[$group_id]['items'][$field_id]['condition_field'];
		}
		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_rule'] ) ) {
			$rule = $groups[$group_id]['items'][$field_id]['condition_rule'];
		}
		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_value'] ) ) {
			$value = $groups[$group_id]['items'][$field_id]['condition_value'];
			// Since 2.4.5
			// key is value run through pewc_keyify_field
			$key = pewc_keyify_field( $groups[$group_id]['items'][$field_id]['condition_value'] );
		}

		if( ! empty( $groups[$group_id]['items'][$field_id]['condition_field'] ) ) {
			$rules['conditions'][] = array(
				'field'	=> $field,
				'rule'	=> $rule,
				'value'	=> $value,
				'key'		=> $key
			);
		}

		set_transient( 'pewc_rules_transient_' . $id, $rules, pewc_get_transient_expiration() );

	}

	return $rules;

}

// function pewc_esc_array( $var ) {
// 	if( is_array( $var ) ) {
// 		return array_map( 'pewc_esc_array', $var );
// 	} else {
// 		return is_scalar( $var ) ? esc_attr( $var ) : $var;
// 	}
// }

/**
 * Return conditions, reorganised
 * @param $item
 * @return Array
 */
function pewc_get_field_conditions( $item, $product_id ) {

	$rules = pewc_get_field_conditional( $item, $product_id );
	$conditions = array();
	$count = 0;
	if( isset( $rules['conditions'] ) ) {
		/** Reorganise the conditions so that field, rule and value are grouped by condition
		 * That is, original $rules['conditions'] looks like
		 * [0] => Array(
		 *	[field] => array( [0] => 'pewc_group_0_0', [1] => 'pewc_group_0_1' ),
		 *	[rule] => array( [0] => 'is', [1] => 'is-not' ),
		 *	[value] => array( [0] => 'hello', [1] => '5' ),
		 * )
		 * Converts to
		 * [0] => ( 'field' => 'pewc_group_0_0', 'rule' => 'is', 'value' => 'hello' )
		 * [1] => ( 'field' => 'pewc_group_0_1', 'rule' => 'is-not', 'value' => '5' )
		*/
		if( $rules['conditions'][$count]['field'] ) {
			foreach( $rules['conditions'][$count]['field'] as $field ) {
				$conditions[$count]['field'] = $field;
				$count++;
			}
		}

		$count = 0;
		if( $rules['conditions'][$count]['rule'] ) {
			foreach( $rules['conditions'][$count]['rule'] as $rule ) {
				$conditions[$count]['rule'] = $rule;
				$count++;
			}
		}

		$count = 0;
		if( $rules['conditions'][$count]['value'] ) {
			foreach( $rules['conditions'][$count]['value'] as $value ) {
				$conditions[$count]['value'] = $value;
				$count++;
			}
		}

		$count = 0;
		if( $rules['conditions'][$count]['key'] ) {
			foreach( $rules['conditions'][$count]['key'] as $key ) {
				$conditions[$count]['key'] = $key;
				$count++;
			}
		}

	}
	return $conditions;
}

/**
 * Return whether a field is initially hidden by a conditional rule
 * @param $item
 * @return Boolean
 */
function pewc_is_field_hidden( $item, $product_id ) {

	$rules = pewc_get_field_conditional( $item, $product_id );
	// If it's set to show after conditions are met, it must be hidden to start with
	if( isset( $rules['action'] ) && $rules['action'] == 'show' ) {
		return true;
	}
	return false;
}

function pewc_print_condition_script() {
	if( apply_filters( 'pewc_conditions_timer', 0 ) > 0 ) {
		return;
	} ?>
	<script>
		jQuery(document).ready(function($) {
			<?php
			global $post;
			// Iterate through each item on the page
			$groups = pewc_get_extra_fields( $post->ID );

			if( $groups ) {

				// Do group conditions first

				$all_checks = array();

				foreach( $groups as $group_id=>$group ) {

					if( isset( $group['items'] ) ) {

						foreach( $group['items'] as $item_key=>$item ) {

							// Add rule checks for each item
							$id = isset( $item['id'] ) ? $item['id'] : false;

							$field_type = isset( $item['field_type'] ) ? $item['field_type'] : false;
							$rules = pewc_get_field_conditional( $item, $post->ID );
							$conditions = pewc_get_field_conditions( $item, $post->ID );

							if( ! empty( $conditions ) ) {

								$check_conditions[$id] = $conditions;

								$arr = array();
								foreach( $conditions as $condition ) {
									// Each rule is an element in this array
									// When the rule is met, the element is switched to 1
									// If not met, it's switched to 0
									// When all elements are 1, all conditions have been met
									$arr[] = '0';
								}
								echo "var conditions_met_{$id} = [" . join( ',', $arr ) . "];\n";
								$rule_count = 0;

								// Make a function to check all the rules for this field
								$all_checks[] = "pewc_check_rules_for_{$id}";

								echo "function pewc_check_rules_for_{$id}( field_value ) {\n";

									// Specify the action when all conditions are met
									echo "var action_{$id} = '{$rules['action']}';\n";
									echo "var match_{$id} = '{$rules['match']}';\n";

									foreach( $conditions as $condition ) {

										// Get the operator
										$operator = '==';
										if( ! isset( $condition['rule'] ) ) $condition['rule'] = 'is';
										if( $condition['rule'] == 'is' || $condition['rule'] == 'cost-equals' ) {
											$operator = '==';
										} else if( $condition['rule'] == 'is-not' ) {
											$operator = '!=';
										} else if( $condition['rule'] == 'cost-greater' ) {
											$operator = '>';
										} else if( $condition['rule'] == 'cost-less' ) {
											$operator = '<';
										} else if( $condition['rule'] == 'greater-than-equals' ) {
											$operator = '>=';
										} else if( $condition['rule'] == 'less-than-equals' ) {
											$operator = '<=';
										} else {
											$operator = 'array';
										}

										$is_number = false;

										if( strpos( $condition['rule'], 'cost' ) !== false || strpos( $condition['rule'], 'quantity' ) !== false || strpos( $condition['rule'], 'equals' ) !== false ) {
											$is_number = true;
										}

										// Get the field value
										echo "var field_value_{$rule_count} = $('#{$condition['field']}').val();\n";

										echo "if( $('.{$condition['field']}').hasClass('pewc-item-radio') ){\n";
											// Radio buttons work slightly differently
											echo "field_value_{$rule_count} = $('.{$condition['field']} input:radio:checked').val();\n";
											echo "if( field_value_{$rule_count} == undefined ){\n";
												echo "field_value_{$rule_count} = '';\n";
											echo "}\n";
										echo "}\n";
										// Radio buttons for child products work differently again
										echo "if($('.{$condition['field']}').hasClass('pewc-item-products-radio')){\n";
											echo "field_value_{$rule_count} = $('.{$condition['field']} input:radio:checked').val();\n";
										echo "}\n";
										// And checkboxes for child products work even more differently
										echo "if($('.{$condition['field']}').hasClass('pewc-item-products-checkboxes')){\n";
											echo "var field_value_{$rule_count} = [];\n";
											echo "$.each( $(\"input[name='" . $condition['field'] . "_child_product[]']:checked\"), function(){\n
												field_value_{$rule_count}.push($(this).val());\n
											});\n";
										echo "}\n";
										// Image swatch with multiple select
										echo "if($('.{$condition['field']}').hasClass('pewc-item-image-swatch-checkbox') || $('.{$condition['field']}').hasClass('pewc-item-checkbox_group') ){\n";
											echo "var field_value_{$rule_count} = [];\n";
											echo "$.each( $(\"input[name='" . $condition['field'] . "[]']:checked\"), function(){\n
												field_value_{$rule_count}.push($(this).val());\n
											});\n";
										echo "}\n";
										// Uploads field
										echo "if( $( '.{$condition['field']}' ).hasClass( 'pewc-item-upload' ) ){\n";
											echo "field_value_{$rule_count} = $( '.{$condition['field']}' ).find( '.pewc-number-uploads' ).val();\n";
										echo "}\n";
										if( $condition['field'] == 'cost' ) {
											// Conditions based on cost of product added to cart
											// So the 'field value' is actually the value of the product in the cart
											echo "field_value_{$rule_count} = $( '#pewc_total_calc_price' ).val();\n";
										} else if( $condition['field'] == 'quantity' ) {
											// Conditions based on cost of product added to cart
											// So the 'field value' is actually the value of the product in the cart
											echo "field_value_{$rule_count} = $('.quantity input.qty').val();\n";
										}

										// Get the condition value
										// The condition value is what the field must equal for the condition to obtain
										// $value = isset( $condition['value'] ) ? $condition['value'] : '';
										// Switched to using the key in 2.4.5
										$value = isset( $condition['key'] ) ? $condition['key'] : '';
										echo "var condition_value_{$rule_count} = '{$value}';\n";

										// Check if the condition is met
										// Need to hand this off to separate functions
										if( $condition['field'] == 'cost' || $condition['field'] == 'quantity' ) {
											echo "if( parseFloat( field_value_{$rule_count} ) {$operator} parseFloat( condition_value_{$rule_count} ) ) {\n
												conditions_met_{$id}[{$rule_count}] = 1;\n
											} else {\n
												conditions_met_{$id}[{$rule_count}] = 0;\n
											}\n";
										} else if( $operator != 'array' ) {
											echo "if( condition_value_{$rule_count} != undefined ) {\n
												condition_value_{$rule_count} = condition_value_{$rule_count}.replace( /'/g, '_' );\n
												condition_value_{$rule_count} = condition_value_{$rule_count}.replace( /\"/g, '_' );\n
											}\n";
											echo "if( field_value_{$rule_count} != undefined ) {\n
												field_value_{$rule_count} = field_value_{$rule_count}.replace( /'/g, '_' );\n
												field_value_{$rule_count} = field_value_{$rule_count}.replace( /\"/g, '_' );\n
											}\n";

											if( $is_number ) {
												echo "var condition_value_{$rule_count} = parseFloat( condition_value_{$rule_count} );\n";
												echo "var field_value_{$rule_count} = parseFloat( field_value_{$rule_count} );\n";
											}

											// Standard Is / Is not
											echo "
											if( field_value_{$rule_count} == '__checked__' ) {\n
												// It's a checkbox\n
												var checked = $('#{$condition['field']}').prop('checked');\n
												if( checked {$operator} true ) {\n
													// This condition is met\n
													conditions_met_{$id}[{$rule_count}] = 1;\n
												} else {\n
													conditions_met_{$id}[{$rule_count}] = 0;\n
												}\n
											} else if( field_value_{$rule_count} {$operator} condition_value_{$rule_count} ) {\n
												conditions_met_{$id}[{$rule_count}] = 1;\n
											} else {\n
												conditions_met_{$id}[{$rule_count}] = 0;\n
											}\n";
										} else {
											// Contains / Does not contain
											// Check if the condition value is in the field value
											echo "if( field_value_{$rule_count} != undefined && field_value_{$rule_count}.indexOf( condition_value_{$rule_count} ) !== -1 ) {\n";
												echo "conditions_met_{$id}[{$rule_count}] = 1;\n";
											echo "} else {\n";
												echo "conditions_met_{$id}[{$rule_count}] = 0;\n";
											echo "}\n";
										}

										// Add an early return if first rule(s) not met
										// Don't bother to check subsequent conditions
										echo "if( conditions_met_{$id}[{$rule_count}] == 0 && match_{$id} == 'all' ) {\n
											conditions_met_{$id}[{$rule_count}] = 0;\n
											// return conditions_met_{$id};\n
										}\n";

										// This checks for every conditional row
										// It can soon add up...
										echo "pewc_check_all_conditions( '{$id}', conditions_met_{$id}, action_{$id}, match_{$id} );\n";

										$rule_count++;

									} // foreach( $conditions as $condition )

									echo "return conditions_met_{$id};\n";

								echo "}\n"; // Close the function pewc_check_rules_for_{$id}

								// Moved block from here
								// Now we listen to each field that has a rule set against it for this field
								foreach( $conditions as $condition ) {

									// Check when quantity is updated for any cost-based conditions
									// Get field type and pass extra param if a number or calculation
									if( $condition['field'] == 'cost' ) {
										echo "$('body').on('input change','.qty',function(){\n
											pewc_check_rules_for_{$id}( $('#pewc_total_calc_price').val() );\n
										});\n";
									} else if( $condition['field'] == 'quantity' ) {
										echo "$('body').on('input change','.qty',function(){\n
											pewc_check_rules_for_{$id}( $( this ).val() );\n
										});\n";
									} else {
										echo "$( 'body' ).on( 'change update keyup paste click', '#{$condition['field']}, .{$condition['field']} .pewc-radio-form-field', function( e ) {\n
											pewc_check_rules_for_{$id}( parseFloat( $(this).val() ) );\n
										});\n";
										echo "$( 'body' ).on( 'calculation_field_updated', function() {\n
											pewc_check_rules_for_{$id}( parseFloat( $(this).val() ) );\n
										});\n";
										echo "$('body').on('change','.{$condition['field']} .pewc-checkbox-form-field',function(){\n
											pewc_check_rules_for_{$id}( $(this).val() );\n
										});\n";
									}

								}

							}

						}

					}

				}

				// Check each field on page load
				if( ! empty( $all_checks ) ) {
					echo "function pewc_do_initial_check() {\n";
						foreach( $all_checks as $check ) {
							echo $check . "();\n";
						}
					echo "}\n";
				}
				echo "if( typeof pewc_do_initial_check == 'function' ) {\n
					pewc_do_initial_check();\n
				}\n";
				echo "$( 'body' ).on( 'pewc_check_conditions', function() {
					if( typeof pewc_do_initial_check == 'function' ) {
						pewc_do_initial_check();
					}
				});\n";
			} ?>

			// These functions are fired when a set of rules are met for a field
			function pewc_check_all_conditions( id, conditions_met, action, match ) {
				if( match == 'all' ) {
					var all_met=true;
					num_req = conditions_met.length;
					for(var i=0;i<num_req;i++) {
						if(conditions_met[i] != 1) {
							all_met=false;
							break;
						}
					}
					if( all_met ) {
						pewc_conditions_met( id, action );
					} else {
						pewc_conditions_not_met( id, action );
					}
				} else {
					var any_met=false;
					num_req = conditions_met.length;
					for(var i=0;i<num_req;i++) {
						if(conditions_met[i] == 1){
							any_met=true;
							break;
						}
					}
					if( any_met ) {
						pewc_conditions_met( id, action );
					} else {
						pewc_conditions_not_met( id, action );
					}
				}
				if( typeof pewc_update_total_js == 'function' ) {
					pewc_update_total_js();
				}
				// Use this to check for hidden groups
				$( 'body' ).one( 'pewc_conditions_checked' );
			}
			function pewc_conditions_met( id, action ) {
				// Check the outcome for conditions being met, e.g. show field / hide field
				if( action == 'show' ) {
					$('.'+id).removeClass('pewc-hidden-field');
				} else if( action == 'hide' ) {
					$('.'+id).addClass('pewc-hidden-field');
					pewc_reset_field_value( id, action );
				}
				// Use this to retrigger calculations
				$( 'body' ).trigger( 'pewc_field_visibility_updated', [ id, action ] );
			}
			function pewc_conditions_not_met( id, action ) {
				// Check the outcome for conditions being met, e.g. show field / hide field
				if( action == 'show' ) {
					$('.'+id).addClass('pewc-hidden-field');
					pewc_reset_field_value( id, action );
				} else if( action == 'hide' ) {
					$('.'+id).removeClass('pewc-hidden-field');
				}
				// Use this to retrigger calculations
				$( 'body' ).trigger( 'pewc_field_visibility_updated', [ id, action ] );
			}
			function pewc_reset_field_value( id, action ) {
				if( pewc_vars.reset_fields == 'yes' ) {
					// Reset the field value
					var field = '.' + id;
					var inputs = ['date', 'name_price', 'number', 'text', 'textarea'];
					var checks = ['checkbox', 'checkbox_group', 'radio'];
					var field_type = $( field ).attr( 'data-field-type' );
					if( inputs.includes( field_type ) ) {
						$( field ).find( '.pewc-form-field' ).val( '' ).trigger( 'change' );
					} else if( field_type == 'image_swatch' ) {
						$( field ).find( 'input' ).prop( 'checked', false );
						$( field ).find( '.pewc-radio-image-wrapper, .pewc-checkbox-image-wrapper' ).removeClass( 'checked' ).trigger( 'change' );
					} else if( field_type == 'products' ) {
						$( field ).find( 'input' ).prop( 'checked', false );
						$( field ).find( '.pewc-form-field' ).val( '' ).trigger( 'change' );
						$( field ).find( '.pewc-radio-image-wrapper, .pewc-checkbox-image-wrapper' ).removeClass( 'checked' );
					} else if( checks.includes( field_type ) ) {
						$( field ).find( 'input' ).prop( 'checked', false );
					} else if( field_type == 'select' ) {
						$( field ).find( '.pewc-form-field' ).prop( 'selectedIndex', 0 ).trigger( 'change' );
					}

				}
			}
		});
	</script>
<?php
}
add_action( 'pewc_after_product_fields', 'pewc_print_condition_script' );

/**
 * Returns whether field is visible, based on conditions
 * This is called by pewc_is_field_required during cart validation
 * @param $id					Field ID
 * @param $item				Current item
 * @param $items			Product items
 * @param $product_id	Product ID
 * @param $posted			$_POST
 * @return Boolean		True if field is visible, false if field is hidden
 */
function pewc_get_conditional_field_visibility( $id, $item, $items, $product_id, $posted=array(), $variation_id=null, $cart_item_data=array(), $quantity=0, $group_id=false, $group=false ) {

	if( empty( $posted ) ) {
		$posted = $_POST;
	}

	// Check if the field is in a hidden group
	if( ! pewc_is_group_visible( $group_id, $group, $posted ) ) {
		return false;
	}

	$cart_item = pewc_get_cart_item_by_extras( $product_id, $variation_id, $cart_item_data );
	$line_total = isset( $cart_item['line_total'] ) ? $cart_item['line_total'] : false;

	/**
	 * If $line_total hasn't yet been calculated, use the price_with_extras value
	 * @since 3.7.13
	 */
	if( ! $line_total ) {
		$line_total =	isset( $cart_item_data['product_extras']['price_with_extras'] ) ? $cart_item_data['product_extras']['price_with_extras'] * $quantity : false;
	}

	// First, does the field have any conditions?
	$conditions = pewc_get_field_conditions( $item, $product_id );

	if( empty( $conditions ) ) {

		// No conditions so the field must be visible
		$is_visible = true;

	} else {

		// Check the rules for the conditions
		$rules = pewc_get_field_conditional( $item, $product_id );

		// Was the field initially visible?
		$is_visible = true;
		if( $rules['action'] == 'show' ) {
			// Field is hidden
			$is_visible = false;
		}

		// We've got conditions so establish whether the field is currently visible or not
		if( $rules['match'] == 'all' ) {

			// If all conditions need to obtain
			$rules_obtain = true;

			foreach( $conditions as $condition ) {

				$field = isset( $condition['field'] ) ? $condition['field'] : '';
				$rule = isset( $condition['rule'] ) ? $condition['rule'] : '';

				// $value = isset( $condition['value'] ) ? $condition['value'] : '';
				// Switched to key since 2.4.5
				$value = isset( $condition['key'] ) ? $condition['key'] : '';

				// We need to get the field type of the field that triggers the condition
				$condition_field_id = explode( '_', $field );
				$condition_field_id = isset( $condition_field_id[3] ) ? $condition_field_id[3] : false;
				$field_type = isset( $items[$condition_field_id]['field_type'] ) ? $items[$condition_field_id]['field_type'] : false;

				// Use this variable for fields that have arrays as values, e.g. checkbox groups and products
				$posted_field = isset( $posted[$field] ) ? $posted[$field] : false;
				$posted_field = isset( $posted[$field. '_child_product'] ) ? $posted[$field. '_child_product'] : $posted_field;

				// Ensure we remove any backslashes, apostrophes, etc
				if( is_array( $posted_field ) ) {
					foreach( $posted_field as $pf_key=>$pf_value ) {
						$posted_field[$pf_key] = pewc_keyify_field( $pf_value );
					}
				} else {
					$posted_field = pewc_keyify_field( $posted_field );
				}

				// Check each condition
				if( $rule == 'is' ) {

					// $posted[$field] is the value of the field on which the condition depends
					if( $field_type == 'checkbox' && ! isset( $posted[$field] ) ) {

						$rules_obtain = false;
						break;

					} else if( $posted_field && is_array( $posted_field ) && ! in_array( $value, $posted_field ) ) {

						// Fields which return an array for their value, e.g. radio groups
						$rules_obtain = false;
						break; // Restored this in 3.7.2 to ensure fields with multiple conditions were getting hidden correctly

					} else if( isset( $posted_field ) && ! is_array( $posted_field ) && $field_type != 'checkbox' && $posted_field != $value ) {

						// Fields which don't return an array of values
						$rules_obtain = false;
						break;

					}

				} else if( $rule == 'is-not' ) {

					if( $posted_field && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

						// Fields which return an array for their value, e.g. radio groups
						$rules_obtain = false;
						break;

					} else if( isset( $posted[$field] ) && $posted[$field] == $value ) {
						$rules_obtain = false;
						break;
					}

				} else if( $rule == 'contains' ) {

					if( $posted_field && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

						$rules_obtain = true;

					} else {

						$rules_obtain = false;

					}

				} else if( $rule == 'cost-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads == $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity == $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total == $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else {

						// Probably calculation

						if( $posted_field && $posted_field == $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					}

				} else if( $rule == 'cost-greater' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads > $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity > $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total > $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else {

						// Probably calculation

						if( $posted_field && $posted_field > $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					}

				} else if( $rule == 'cost-less' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads < $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity < $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'cost' ) {

						if( $line_total < $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else {

						// Probably calculation
						if( $posted_field && $posted_field < $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					}

				} else if( $rule == 'greater-than-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads >= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity >= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'cost' ) {

						if( $line_total >= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else {

						// Probably calculation
						if( $posted_field && $posted_field <= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					}

				} else if( $rule == 'less-than-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads <= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity <= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'cost' ) {

						if( $line_total <= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;

						}

					} else {

						// Probably calculation
						if( $posted_field && $posted_field <= $value ) {

							$rules_obtain = true;

						} else {

							$rules_obtain = false;
							break;

						}

					}

				} //

			}

		} else if( $rules['match'] == 'any' ) {

			// If any condition needs to obtain
			$rules_obtain = false;

			foreach( $conditions as $condition ) {

				$field = isset( $condition['field'] ) ? $condition['field'] : '';
				$rule = isset( $condition['rule'] ) ? $condition['rule'] : '';

				// $value = isset( $condition['value'] ) ? $condition['value'] : '';
				// Switched to key since 2.4.5
				$value = $key = isset( $condition['key'] ) ? $condition['key'] : '';

				// We need to get the field type of the field that triggers the condition
				$condition_field_id = explode( '_', $field );
				$condition_field_id = $condition_field_id[3];
				$field_type = isset( $items[$condition_field_id]['field_type'] ) ? $items[$condition_field_id]['field_type'] : false;

				// Use this variable for fields that have arrays as values, e.g. checkbox groups and products
				$posted_field = isset( $posted[$field] ) ? $posted[$field] : false;
				$posted_field = isset( $posted[$field. '_child_product'] ) ? $posted[$field. '_child_product'] : $posted_field;

				// Check each condition
				if( $rule == 'is' ) {

					if( is_array( $posted_field ) && in_array( $key, $posted_field ) ) {
						$rules_obtain = true;
						break;
					} else if( isset( $posted_field ) && $posted_field == $key ) {
						$rules_obtain = true;
						// break;
					}

				} else if( $rule == 'is-not' ) {

					if( is_array( $posted_field ) && ! in_array( $key, $posted_field ) ) {
						$rules_obtain = true;
						break;
					} else if( $posted_field != $key ) {
						$rules_obtain = true;
						// break;
					}

				} else if( $rule == 'contains' ) {

					if( isset( $posted_field ) && is_array( $posted_field ) && in_array( $value, $posted_field ) ) {

						$rules_obtain = true;
						break;

					} else {

						$rules_obtain = false;
						// break;

					}

				} else if( $rule == 'cost-greater' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads > $value ) {

							$rules_obtain = true;
							break;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity > $value ) {

							$rules_obtain = true;
							break;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total > $value ) {

							$rules_obtain = true;
							break;

						}

					} else {

						// Calculation or number field
						if( $posted_field > $value ) {

							$rules_obtain = true;
							break;

						}

					}

				} else if( $rule == 'cost-less' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads < $value ) {

							$rules_obtain = true;
							break;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity < $value ) {

							$rules_obtain = true;
							break;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total < $value ) {

							$rules_obtain = true;
							break;

						}

					} else {

						// Calculation or number field
						if( $posted_field < $value ) {

							$rules_obtain = true;
							break;

						}

					}

				} else if( $rule == 'cost-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads == $value ) {

							$rules_obtain = true;
							break;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity == $value ) {

							$rules_obtain = true;
							break;

						// } else {
						//
						// 	$rules_obtain = false;
						// 	break;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total == $value ) {

							$rules_obtain = true;
							break;

						}

					} else {

						// Cost
						if( $posted_field == $value ) {

							$rules_obtain = true;
							break;

						}

					}

				} else if( $rule == 'greater-than-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads >= $value ) {

							$rules_obtain = true;
							break;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity >= $value ) {

							$rules_obtain = true;
							break;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total >= $value ) {

							$rules_obtain = true;
							break;

						}

					} else {

						// Calculation or number field
						if( $posted_field >= $value ) {

							$rules_obtain = true;
							break;

						}

					}

				} else if( $rule == 'less-than-equals' ) {

					if( $field_type == 'upload' ) {

						$number_uploads = isset( $posted[$field . '_number_uploads'] ) ? $posted[$field . '_number_uploads'] : 0;

						if( $number_uploads <= $value ) {

							$rules_obtain = true;
							break;

						} else {

							$rules_obtain = false;

						}

					} else if( $field == 'quantity' ) {

						// Quantity
						if( $quantity <= $value ) {

							$rules_obtain = true;
							break;

						}

					} else if( $field == 'cost' ) {

						// Cost
						if( $line_total <= $value ) {

							$rules_obtain = true;
							break;

						}

					} else {

						// Calculation or number field
						if( $posted_field <= $value ) {

							$rules_obtain = true;
							break;

						}

					}

				}

			}

		}

		if( $rules['action'] == 'show' ) {

			$is_visible = $rules_obtain;

		} else {

			$is_visible = ! $rules_obtain;

		}

		// return $is_visible;

	}

	return apply_filters( 'pewc_get_conditional_field_visibility', $is_visible, $id, $item, $items, $product_id, $variation_id, $cart_item_data, $group_id, $group );

}

/**
 * Returns whether a group is visible based on conditions
 * @since 3.8.0
 */
function pewc_is_group_visible( $group_id, $group, $posted ) {

	// Check the group for conditions
	$conditions = pewc_get_group_conditions( $group_id );
	if( ! $conditions ) {
		// No conditions so group must be visible
		return true;
	}

	// Iterate through each condition
	$match = pewc_get_group_condition_match( $group_id );
	$action = pewc_get_group_condition_action( $group_id, $group );

	$is_group_visible = false;

	if( $match == 'all' ) {
		$is_group_visible = true;
	}

	foreach( $conditions as $condition ) {

		$field_id = $condition['field'];
		$field_value = isset( $posted[$field_id] ) ? $posted[$field_id] : false;
		$meets_condition = pewc_is_field_visible( $field_value, $condition['rule'], $condition['value'] );

		// Reverse the visibility for groups that are hidden
		if( $action == 'hide' ) {
			$meets_condition = ! $meets_condition;
		}

		if( $meets_condition && $match =='any' ) {
			return true;
		} else if( ! $meets_condition && $match =='all' ) {
			return false;
		}

	}

	return $is_group_visible;

}

/**
 * Returns whether a field is visible based on conditions
 * @since 3.8.0
 */
function pewc_is_field_visible( $field_value, $rule, $required_value ) {

	if( $rule == 'is' || $rule == 'cost-equals' ) {
		if( is_array( $field_value ) ) { // Radio button values
			return in_array( $required_value, $field_value );
		}
		return $field_value == $required_value;
	} else if( $rule == 'is-not' ) {
		return $field_value != $required_value;
	} else if( $rule == 'contains' ) {
		return in_array( $required_value, $field_value );
	} else if( $rule == 'does-not-contain' ) {
		return ! in_array( $required_value, $field_value );
	} else if( $rule == 'greater-than' || $rule == 'greater-than-equals' ) {
		return $field_value >= $required_value;
	} else if( $rule == 'less-than' || $rule == 'less-than-equals' ) {
		return $field_value <= $required_value;
	}
	
}

/**
 * Find the cart item by the data we've got
 * @since 3.2.14
 */
function pewc_get_cart_item_by_extras( $product_id, $variation_id, $cart_item_data ) {

	$cart = WC()->cart->cart_contents;
	if( $cart ) {

		foreach( $cart as $id=>$cart_item ) {

			// Check if our parameters exactly match the cart item
			if( ( isset( $cart_item['product_id'] ) && $cart_item['product_id'] == $product_id ) && ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] == $variation_id ) ) {
				return $cart_item;
			}

		}

	}

	return false;

}
