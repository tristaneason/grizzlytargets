<?php
/**
 * Functions for the product page
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return whether user can upload files
 * @return Boolean
 */
function pewc_can_upload() {
	$require_log_in = get_option( 'pewc_require_log_in', 'yes' );
	if( $require_log_in == 'yes' && ! is_user_logged_in() ) {
		return false;
	}
	return true;
}

function pewc_enqueue_scripts() {

	if( ! function_exists( 'get_woocommerce_currency_symbol' ) ) {
		return;
	}

	// Better performance
	$dequeue = get_option( 'pewc_dequeue_scripts', 'no' );
	if( $dequeue == 'yes' && ! is_product() ) {
		return;
	}

	global $product, $post;
	// $post_id = $post->ID;
	$version = defined( 'PEWC_SCRIPT_DEBUG' ) && PEWC_SCRIPT_DEBUG ? time() : PEWC_PLUGIN_VERSION;

	if( pewc_enable_ajax_upload() == 'yes' ) {
		wp_enqueue_style( 'pewc-dropzone-basic', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/basic.min.css', array(), $version );
		wp_enqueue_style( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/dropzone.min.css', array(), $version );
	}

	wp_enqueue_style( 'pewc-style', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/style.css', array( 'dashicons' ), $version );

	$deps = array( 'jquery', 'jquery-blockui', 'jquery-ui-datepicker' );

	// Only load math.js if we have a calculation field
	// Need to override this for Elementor???
	if( apply_filters( 'pewc_enqueue_calculation_script', isset( $post->ID ) && pewc_has_calculation_field( $post->ID ) ) ) {
		wp_enqueue_script( 'pewc-math-js', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/math.min.js', array(), '5.10.3', true );
		$deps[] = 'pewc-math-js';
	}

	// Only load the Iris-JS library if we have a color-picker field
	if( apply_filters( 'pewc_enqueue_color-picker_script', isset ( $post->ID ) && pewc_has_color_picker_field( $post->ID ) ) ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
    wp_enqueue_script( 'wp-color-picker', admin_url( 'js/color-picker.js' ), array( 'iris' ), false, 1 );
		if( version_compare( $GLOBALS['wp_version'], '5.5', '<' ) ) {
			wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
				array(
					'clear' => __( 'Clear', 'pewc' ),
					'defaultString' => __( 'Default', 'pewc' ),
					'pick' => __( 'Select Color', 'pewc' ),
					'current' => __( 'Current Color', 'pewc' ),
				)
			);
		} else {
			wp_set_script_translations( 'wp-color-picker' );
		}
  }

	if( pewc_enable_tooltips() == 'yes' && ! apply_filters( 'pewc_dequeue_tooltips', false ) ) {
		wp_enqueue_style( 'pewc-tooltipster-style', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/tooltipster.bundle.min.css', array(), $version );
		wp_enqueue_style( 'pewc-tooltipster-shadow', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/css/tooltipster-sideTip-shadow.min.css', array(), $version );
		wp_register_script( 'tooltipster', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/tooltipster.bundle.min.js', $deps, $version, true );
		$deps[] = 'tooltipster';
	}

	wp_register_script( 'pewc-conditions', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/conditions.js', $deps, $version, true );
	$deps[] = 'pewc-conditions';

	if( pewc_enable_ajax_upload() == 'yes' ) {
		wp_register_script( 'pewc-dropzone', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/dropzone.js', $deps, $version, false );
		$deps[] = 'pewc-dropzone';
	}

	wp_register_script( 'dd-slick', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/select-box.js', array(), $version, true );
	$deps[] = 'dd-slick';

	// Using this for QuickView
	$deps[] = 'wc-single-product';

	wp_register_script( 'pewc-script', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/pewc.js', $deps, $version, true );

	$vars = array(
		'ajaxurl'								=> admin_url( 'admin-ajax.php' ),
		'currency_symbol'				=> get_woocommerce_currency_symbol(),
		'decimal_separator'  		=> wc_get_price_decimal_separator(),
		'thousand_separator' 		=> wc_get_price_thousand_separator(),
		'decimals'           		=> wc_get_price_decimals(),
		'price_format'       		=> get_woocommerce_price_format(),
		'currency_pos' 					=> get_option( 'woocommerce_currency_pos' ),
		'variable_1'						=> get_option( 'pewc_variable_1', 0 ),
		'variable_2'						=> get_option( 'pewc_variable_2', 0 ),
		'variable_3'						=> get_option( 'pewc_variable_3', 0 ),
		'replace_image'					=> pewc_get_add_on_image_action(),
		'enable_tooltips'				=> pewc_enable_tooltips(),
		'dequeue_tooltips'			=> apply_filters( 'pewc_dequeue_tooltips', false ),
		'separator'							=> pewc_add_on_price_separator(),
		'update_price'					=> pewc_get_update_price_label(),
		'disable_qty'						=> apply_filters( 'pewc_disable_child_quantities', true ),
		'product_gallery'				=> apply_filters( 'pewc_product_gallery', '.images' ),
		'product_img_wrap'			=> apply_filters( 'pewc_product_img_wrap', '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ),
		'calculations_timer'		=> apply_filters( 'pewc_calculations_timer', 0 ),
		'conditions_timer'			=> apply_filters( 'pewc_conditions_timer', 0 ),
		'remove_spaces'					=> apply_filters( 'pewc_remove_spaces_in_text', 'no' ),
		'math_round'						=> apply_filters( 'pewc_math_round', 'no' ),
		'disable_button_calcs'	=> apply_filters( 'pewc_disable_button_calcs', 'no' ),
		'null_signifier'				=> apply_filters( 'pewc_look_up_table_null_signifier', '*' )
	);

	if( is_product() ) {
		$product = wc_get_product( $post->ID );
		$vars['show_suffix'] 	= pewc_show_price_suffix();
		$vars['price_suffix'] = $product->get_price_suffix();
		$vars['price_suffix_setting'] = get_option( 'woocommerce_price_display_suffix' );
		$vars['percent_exc_tax'] = wc_get_price_excluding_tax( $product, $args = array( 'price' => 100, 'qty' => 1 ) );
		$vars['percent_inc_tax'] = wc_get_price_including_tax( $product, $args = array( 'price' => 100, 'qty' => 1 ) );
	}

	if( pewc_is_pro() && function_exists( 'pewc_multiply_independent_quantities_by_parent_quantity' ) ) {
		$vars['multiply_independent']	= pewc_multiply_independent_quantities_by_parent_quantity();
	}

	// Allow filterable global vars
	if( isset( $post->ID ) && pewc_has_calculation_field( $post->ID ) ) {
		$vars['global_calc_vars'] = apply_filters( 'pewc_calculation_global_calculation_vars', false );
	}

	if( isset( $post->ID ) ) {
		$vars['post_id'] = $post->ID;
		// $vars['drop_files_message'] = apply_filters( 'pewc_filter_drop_files_message', __( 'Drop files here to upload', 'pewc' ), $post->ID );
		$vars['accordion_toggle'] = apply_filters( 'pewc_filter_initial_accordion_states', false, $post->ID );
		$vars['close_accordion'] = apply_filters( 'pewc_close_accordion', 'no', $post->ID );
		$vars['reset_fields'] = pewc_reset_hidden_fields( $post->ID );
	}

	wp_localize_script(
		'pewc-script',
		'pewc_vars',
		$vars
	);

	wp_enqueue_script( 'pewc-script' );

}
add_action( 'wp_enqueue_scripts', 'pewc_enqueue_scripts' );

function pewc_enqueue_child_products_script() {

	$version = defined( 'PEWC_SCRIPT_DEBUG' ) && PEWC_SCRIPT_DEBUG ? time() : PEWC_PLUGIN_VERSION;
	wp_register_script( 'pewc-variations-script', trailingslashit( PEWC_PLUGIN_URL ) . 'assets/js/pewc-variations.js', array( 'jquery', 'pewc-script', 'wc-add-to-cart-variation' ), $version, true );
	wp_enqueue_script( 'pewc-variations-script' );

}
add_action( 'pewc_products_column_layout', 'pewc_enqueue_child_products_script', 10 );

/**
 * Display the product_extra fields
 */
function pewc_product_extra_fields() {

	// We added this to prevent some themes displaying fields twice
	// You can use the filter to ensure that other themes, i.e. Divi, will display the fields at all
	$did = did_action( 'woocommerce_before_add_to_cart_button' );
	if( $did > apply_filters( 'pewc_check_did_action', 1 ) ) {
		return;
	}

	global $product, $post;
	$post_id = $post->ID;
	$licence = pewc_get_license_level();

	if( $product->get_type() != 'simple' && $product->get_type() != 'variable' && $product->get_type() != 'simple_booking' ) {
		// return;
	}

	$product_extra_groups = pewc_get_extra_fields( $post_id );
	$group_ids = array_keys( $product_extra_groups );
	$all_group_conditions = pewc_get_all_group_conditions_fields( $group_ids );
	$calculation_components = pewc_get_all_calculation_components( $product_extra_groups );

	// This is a list of fields which have conditions
	$all_field_conditions = pewc_get_all_field_conditions_fields( $product_extra_groups, $post_id );
	$field_conditions_by_field_id = pewc_get_conditions_by_field_id( $product_extra_groups, $post_id );

	// Use this list to get fields which fields are conditional on, i.e. fields which need to trigger a condition check when their value changes
	// If field 1234 has a condition to display if field 4567 is checked, then 4567 is a trigger for 1234
	$triggers = pewc_get_all_conditional_triggers( $all_field_conditions, $post_id );
	$triggers_for = pewc_get_triggers_for_fields( $all_field_conditions, $post_id );

	?>
	<script>
		var pewc_cost_triggers = <?php echo json_encode( pewc_get_triggered_by_field_type( $field_conditions_by_field_id, 'cost' ), JSON_NUMERIC_CHECK ); ?>;
		var pewc_quantity_triggers = <?php echo json_encode( pewc_get_triggered_by_field_type( $field_conditions_by_field_id, 'quantity' ), JSON_NUMERIC_CHECK ); ?>;
	</script>
	<?php

	// Use this to populate the summary panel
	$summary_panel = array();

	if( $product_extra_groups ) {

		// Check if this product has been reloaded from the cart
		$cart_key = ! empty( $_GET['pewc_key'] ) ? $_GET['pewc_key'] : false;
		$edited_fields = false;
		$child_fields = array();

		$cart = WC()->cart->cart_contents;

		if( isset( $cart[$cart_key] ) && pewc_user_can_edit_products() ) {

			// This product has already been added to the cart, so we're now editing it
			$cart_item = $cart[$cart_key];
			if( ! empty( $cart_item['product_extras'] ) ) {
				// This is an array of add-ons and values for this product
				$edited_fields = $cart_item['product_extras']['groups'];
			}

			// If this product has child products in the cart, let's find them
			if( ! empty( $cart_item['product_extras']['products']['child_products'] ) ) {

				// This field has children
				$pewc_parent_product_id = $cart_item['product_extras']['product_id'];
				$parent_field_id = $cart_item['product_extras']['products']['field_id'];

				// Get the child field IDs
				foreach( $cart_item['product_extras']['products']['child_products'] as $child_field_id=>$child_field_data ) {
					// This builds an array of child products belonging to each Products field
					// E.g. pewc_group_4850_4866 => array( 525 => 525 )
					$child_fields[$child_field_data['field_id']][$child_field_id] = $child_field_id;
				}

				foreach( $cart as $child_cart_item_key=>$child_cart_item ) {

					if( ! empty( $child_cart_item['product_extras']['groups'] ) ) {
						// This is a parent item so we can skip it
						continue;
					}

					// Now we're looking for quantities
					// Child Product ID => Quantity
					if( ! empty( $child_cart_item['product_extras']['products']['field_id'] ) && ! empty( $child_cart_item['product_extras']['product_id'] ) ) {
						// This is a child product of the product we're currently editing
						$child_fields[$child_cart_item['product_extras']['products']['field_id']][$child_cart_item['product_extras']['product_id']] = $child_cart_item['quantity'];
					}
				}

			}

			// Add a field so that we can use this cart key after we have finished editing
			printf(
				'<input type="hidden" id="pewc_delete_cart_key" name="pewc_delete_cart_key" value="%s">',
				esc_attr( $cart_key )
			);

		}

		$display = pewc_get_group_display( $post_id );
		$groups_wrapper_classes = pewc_get_groups_wrapper_classes( $product_extra_groups, $post_id, $display );

		echo '<div class="' . join( ' ', $groups_wrapper_classes ) . '">';

		do_action( 'pewc_start_groups', array( $post_id, $product_extra_groups ) );

		// Check for permissions
		$can_upload = pewc_can_upload();
		$first_group_class = 'first-group';

		$number_teaser_fields = pewc_get_number_teaser_fields();
		$count_fields = 0;

		// Iterate through each group
		foreach( $product_extra_groups as $group_id=>$group ) {

			$wrapper_classes = apply_filters(
				'pewc_filter_group_wrapper_class',
				array(
					'pewc-group-wrap',
					'pewc-group-wrap-' . $group_id,
					$first_group_class
				),
				$group_id,
				$group,
				$post_id
			);

			$group_conditions = pewc_get_group_conditions( $group_id );
			$group_attributes = pewc_get_group_attributes( $group_conditions, $group_id, $group );

			$first_group_class = '';

			printf(
				'<div id="%s" class="%s" %s>',
				'pewc-group-' . $group_id,
				join( ' ', $wrapper_classes ),
				$group_attributes
			);

				echo '<div class="pewc-group-heading-wrapper">';

					$group_title = pewc_get_group_title( $group_id, $group, pewc_has_migrated() );

					if( $group_title ) {
						echo apply_filters( 'pewc_filter_group_title', sprintf( '<h3>%s</h3>', esc_html( $group_title ), $group ) );
					}
					$group_class = '';
					if( isset( $group['meta']['group_required'] ) ) {
						$group_class = 'require-' . $group['meta']['group_required'];
					}

					if( pewc_is_pro() && pewc_display_summary_panel_enabled() ) {
						$summary_panel[$group_id]['title'] = $group_title;
					}

				echo '</div><!-- .pewc-group-heading-wrapper -->';
				echo '<div class="pewc-group-content-wrapper">';

					$description = pewc_get_group_description( $group_id, $group, pewc_has_migrated() );

					if( $description ) {
						echo apply_filters(
							'pewc_filter_group_description',
							sprintf(
								'<p class="pewc-group-description">%s</p>',
								wp_kses_post( $description ),
								$group
							)
						);
					}

					$group_layout = pewc_get_group_layout( $group_id );

					if( strpos( $group_layout, 'cols' ) !== false ) {
						$group_class .= ' ' . $group_layout;
						$group_layout = 'ul';
					}

					echo '<' . $group_layout . ' class="pewc-product-extra-groups ' . esc_attr( $group_class ) . '">';

					if( $group_layout == 'table' ) {
						echo '<tbody>';
					}

					// Iterate through each field
					if( isset( $group['items'] ) ) {

						foreach( $group['items'] as $item ) {

							$label = isset( $item['field_label' ] ) ? $item['field_label' ] : '';

							$item = apply_filters( 'pewc_filter_item_start_list', $item, $group, $group_id, $post_id );

							if( isset( $item['field_type'] ) ) {

								$id = $item['id'];

								$value = pewc_get_default_value( $id, $item, $_POST );

								// Replace default value with editable value
								if( isset( $edited_fields[$group_id][$item['field_id']]['value_without_price'] ) ) {
									// This is a value from arrays without any prices getting in the way
									$value = isset( $edited_fields[$group_id][$item['field_id']]['value_without_price'] ) ? $edited_fields[$group_id][$item['field_id']]['value_without_price'] : $value;
								} else if( isset( $edited_fields[$group_id][$item['field_id']]['value'] ) ) {
									// Override any default values if we're editing a product - repopulate with existing values
									$value = isset( $edited_fields[$group_id][$item['field_id']]['value'] ) ? $edited_fields[$group_id][$item['field_id']]['value'] : $value;
								};

								// Check for existing values for product fields

								$quantity_field_values = array();

								if( $item['field_type'] == 'products' && ! empty( $child_fields[$id] ) ) {

									// Get the list of child products seleected for this field
									$value = array_keys( $child_fields[$id] );
									// Use this to set the quantities
									$quantity_field_values = $child_fields[$id];

								}

								// Ensure checkbox default is retained
								if( $value == 'checked' || $value == '__checked__' ) $value = 1;

								// Set the wrapper classes
								$required_class = '';
								if( isset( $item['field_required'] ) && $item['field_type'] != 'products' ) {
									$required_class = 'required-field';
								}

								$classes = pewc_get_field_classes( $item, $id, $post_id, $product, $count_fields, $number_teaser_fields, $display, $all_group_conditions, $calculation_components );

								$field_image = pewc_get_field_image( $item, $id );

								$field_price = pewc_get_field_price( $item, $product );

								if( pewc_is_pro() && pewc_display_summary_panel_enabled() ) {
									$summary_panel[$group_id]['fields'][$item['field_id']] = array(
										'label' => $label,
										'value'	=> $value,
										'price'	=> $field_price
									);
								}

								// Create a string for the existing value, used when editing the cart item
								$data_field_value = is_array( $value ) ? join( ', ', $value ) : $value;

								$attributes = array(
									'data-price'									=> $field_price,
									'data-id'											=> $id,
									'data-selected-option-price'	=> '',
									'data-field-id'								=> $item['field_id'],
									'data-field-type'							=> $item['field_type'],
									'data-field-price'						=> $field_price,
									'data-field-label'						=> $label,
									'data-field-value'						=> $data_field_value
								);

								// Set which groups this field is a condition trigger for
								if( isset( $all_group_conditions[$id] ) ) {
									$attributes['data-trigger-groups'] = json_encode( array_values( $all_group_conditions[$id] ) );
								}

								if( isset( $calculation_components[$item['field_id']] ) ) {
									$attributes['data-trigger-calculations'] = json_encode( array_values( $calculation_components[$item['field_id']] ) );
								}

								if( isset( $all_field_conditions[$item['field_id']] ) ) {
									// data-trigger-fields holds a list of the fields that this field is conditional on
									$attributes['data-trigger-fields'] = json_encode( array_values( $all_field_conditions[$item['field_id']] ), JSON_NUMERIC_CHECK ); // Removes the quotes
									// Add a class to show that this field has a condition
									$classes[] = 'pewc-field-has-condition';
								}

								if( isset( $triggers_for[$item['field_id']] ) ) {
									$attributes['data-triggers-for'] = json_encode( array_values( $triggers_for[$item['field_id']] ), JSON_NUMERIC_CHECK ); // Removes the quotes
								}

								if( ! empty( $field_conditions_by_field_id[$item['field_id']] ) ) {
									$attributes['data-field-conditions-match'] = get_post_meta( $item['field_id'], 'condition_match', true );
									$attributes['data-field-conditions-action'] = get_post_meta( $item['field_id'], 'condition_action', true );
									$attributes['data-field-conditions'] = json_encode( $field_conditions_by_field_id[$item['field_id']], JSON_NUMERIC_CHECK ); // Removes the quotes
								}

								if( in_array( $item['field_id'], $triggers ) ) {
									$classes[] = 'pewc-field-triggers-condition';
								}

								if( pewc_is_pro() ) {

									if( ! empty( $item['field_percentage'] ) && ! empty( $item['field_price'] ) ) {
										// Set the option price as a percentage of the product price
										$product_price = $product->get_price();
										$price = ( floatval( $field_price ) / 100 ) * $product_price;
										// Get display price according to inc tax / ex tax setting
										$price = pewc_maybe_include_tax( $product, $price );
										$attributes['data-price']	= $price;
										$attributes['data-percentage'] = floatval( $field_price );
									}

								}

								$attributes = apply_filters( 'pewc_filter_item_attributes', $attributes, $item );
								$attribute_string = '';
								foreach( $attributes as $attribute=>$attribute_value ) {
									$attribute_string .= " " . $attribute . "='" . $attribute_value . "'";
								}

								$group_inner_tag = 'li';
								$cell_tag = 'div';
								$open_td = '';
								$close_td = '';
								if( $group_layout == 'table' ) {
									$group_inner_tag = 'tr';
									$cell_tag = 'td';
									$open_td = '<td>';
									$close_td = '</td>';
								}	?>

								<<?php echo $group_inner_tag; ?> class="pewc-group pewc-item <?php echo join( ' ', $classes ); ?>" <?php echo $attribute_string; ?>>

									<?php // Check for an image
									if( $field_image ) {

										$full_size_image_url = pewc_get_field_image_url( $item, 'full' );

										// Don't display the image if we're using it to replace the main image
										if( pewc_get_add_on_image_action() == 'replace_hide' ) {

											printf(
												'<span data-image-full-size="%s" class="pewc-item-field-image-wrapper" style="display: none;"></span>',
												$full_size_image_url[0]
											);

										} else {

											printf(
												'<%s data-image-full-size="%s" class="pewc-item-field-image-wrapper">%s</%s>',
												$cell_tag,
												$full_size_image_url[0],
												$field_image,
												$cell_tag
											);

										}

									} else if( ! $field_image && $group_layout == 'table' ) {

										// Include an empty td to ensure table columns are equal
										echo '<td></td>';

									}

									if( $group_layout == 'ul' ) {
										echo '<' . $cell_tag . ' class="pewc-item-field-wrapper">';
									}

										// Include the field template
										$file = str_replace( '_', '-', $item['field_type'] ) . '.php';

										if( $file ) {

											if( $file == 'radio-image.php') $file = 'image-swatch.php';

											/**
											 * @hooked pewc_before_frontend_template
											 */
											do_action( 'pewc_before_include_frontend_template', $item, $id, $group_layout, $file );

											$path = pewc_include_frontend_template( $file );
											if( $path ) {
												include( $path );
											}

											/**
											 * @hooked pewc_after_frontend_template	10
											 */
											do_action( 'pewc_after_include_frontend_template', $item, $id, $group_layout, $file );

										}

										/**
										 * @hooked pewc_field_description_list_layout
										 */
										do_action( 'pewc_after_field_template', $item, $id, $group_layout );

										$count_fields++;

										if( $group_layout == 'ul' ) {
											echo '</' . $cell_tag . '>';
										} ?>

								</<?php echo $group_inner_tag; ?>><!-- .pewc-item -->

							<?php }

						}

						if( $group_layout == 'table' ) {
							echo '</tbody>';
						}

					}

					echo '</' . $group_layout . '><!-- .pewc-product-extra-groups -->';

					do_action( 'pewc_end_group_content_wrapper', $group, $group_id, $display, $product_extra_groups );

				echo '</div><!-- .pewc-group-content-wrapper -->';

			echo '</div><!-- .pewc-product-extra-group-wrap -->';

		}

		/**
		 * @hooked pewc_display_summary_panel				5
		 * @hooked pewc_totals_fields								10
		 * @hooked pewc_hidden_fields_product_page	20
		 */
		do_action( 'pewc_after_group_wrap', $post_id, $product, $summary_panel );

		echo '</div><!-- .pewc-product-extra-groups-wrap -->';

	}

	do_action( 'pewc_after_product_fields' );

}
add_action( 'woocommerce_before_add_to_cart_button', 'pewc_product_extra_fields' );
// add_action( 'woocommerce_before_single_variation', 'pewc_product_extra_fields' );

/**
 * Return the claasses for the groups wrapper
 * @since 3.6.0
 */
function pewc_get_groups_wrapper_classes( $product_extra_groups, $post_id, $display ) {

	$groups_wrapper_classes = apply_filters(
		'pewc_filter_groups_wrapper_classes',
		array(
			'pewc-product-extra-groups-wrap'
		),
		$product_extra_groups,
		$post_id
	);

	$groups_wrapper_classes[] = 'pewc-groups-' . $display;

	$number_teaser_options = pewc_get_number_teaser_options();
	if( $number_teaser_options ) {
		$groups_wrapper_classes[] = 'pewc-teaser-options-'. $number_teaser_options;
	}

	$retain_upload = get_option( 'pewc_retain_dropzone', 'no' );
	if( $retain_upload == 'yes' ) {
		$groups_wrapper_classes[] = 'retain-upload-graphic';
	}

	return apply_filters( 'pewc_groups_wrapper_classes', $groups_wrapper_classes, $product_extra_groups, $post_id );

}

/**
 * Return the attributes for the group wrapper
 * @since 3.8.0
 */
function pewc_get_group_attributes( $conditions, $group_id, $group ) {

	$attribute_string = "";

	$action = pewc_get_group_condition_action( $group_id, $group );
	if( ! $action ) {
		// If there's not an action set, then no point continuing
		return $attribute_string;
	}

	$match = pewc_get_group_condition_match( $group_id );

	$attributes = array(
		'data-group-id'						=> $group_id,
		'data-condition-action'		=> $action,
		'data-condition-match'		=> $match,
		'data-conditions'					=> json_encode( $conditions )
	);

	foreach( $attributes as $attribute=>$attribute_value ) {
		$attribute_string .= " " . $attribute . "= '" . $attribute_value . "'";
	}

	return $attribute_string;

}

/**
 * Insert some hidden fields with useful values
 * @since 3.5.3
 */
function pewc_hidden_fields_product_page( $post_id, $product, $summary_panel ) {

	// Hidden fields with product data
	if( $product->is_type( 'variable' ) ) {
		$default_price = 0;
	} else {
		$default_price = $product->get_price();
	}

	// Look for anything that might have changed the price
	$default_price = apply_filters( 'pewc_filter_default_price', $default_price, $product );

	// Fields used for pricing
	echo '<input type="hidden" id="pewc-product-price" name="pewc-product-price" value="' . pewc_maybe_include_tax( $product, $default_price ) . '">';
	echo '<input type="hidden" id="pewc_calc_set_price" name="pewc_calc_set_price" data-calc-set value="">';
	echo '<input type="hidden" id="pewc_total_calc_price" name="pewc_total_calc_price" value="' . pewc_maybe_include_tax( $product, $default_price ) . '">';
	echo '<input type="hidden" id="pewc_variation_price" name="pewc_variation_price" value="">';

	// Fields used for product dimensions
	printf(
		'<input type="hidden" id="pewc_product_length" name="pewc_product_length" value="%s">',
		$product->get_length()
	);
	printf(
		'<input type="hidden" id="pewc_product_width" name="pewc_product_width" value="%s">',
		$product->get_width()
	);
	printf(
		'<input type="hidden" id="pewc_product_height" name="pewc_product_height" value="%s">',
		$product->get_height()
	);
	printf(
		'<input type="hidden" id="pewc_product_weight" name="pewc_product_weight" value="%s">',
		$product->get_weight()
	);

	printf(
		'<input type="hidden" id="pewc_product_id" name="pewc_product_id" value="%s">',
		$product->get_id()
	);

	// Variations grid
	echo '<input type="hidden" name="pewc-grid-total-variations" id="pewc-grid-total-variations" value="">';

	// Nonces
	wp_nonce_field( 'pewc_file_upload', 'pewc_file_upload' );
	wp_nonce_field( 'pewc_total', 'pewc_total' );

}
add_action( 'pewc_after_group_wrap', 'pewc_hidden_fields_product_page', 10, 4 );

/**
 * Display the totals fields
 * @since 3.5.3
 */
function pewc_totals_fields( $post_id, $product, $summary_panel ) {

	$show_totals = apply_filters( 'pewc_product_show_totals', get_option( 'pewc_show_totals', 'all' ), $post_id );
	if( $show_totals == 'all' ) {
		$path = pewc_include_frontend_template( 'price-subtotals.php' );
		include( $path );
	} else if( $show_totals == 'total' ) {
		printf(
			'<p class="pewc-total-only">%s<span id="pewc-grand-total" class="pewc-total-field"></span></p>',
			apply_filters( 'pewc_total_only_text', '', $post_id )
		);
	}

}
add_action( 'pewc_after_group_wrap', 'pewc_totals_fields', 20, 4 );

/**
 * Display the field label
 * For all fields except checkbox in list view
 */
function pewc_before_frontend_template( $item, $id, $group_layout, $file ) {
	if( $group_layout == 'table' || $item['field_type'] != 'checkbox' ) {
		echo pewc_field_label( $item, $id, $group_layout );
	}
}
add_action( 'pewc_before_include_frontend_template', 'pewc_before_frontend_template', 10, 4 );

/**
 * Display the field label for the checkbox in list view
 */
function pewc_after_frontend_template( $item, $id, $group_layout, $file ) {
	if( $group_layout == 'ul' && $item['field_type'] == 'checkbox' ) {
		echo pewc_field_label( $item, $id, $group_layout );
	}
}
add_action( 'pewc_after_include_frontend_template', 'pewc_after_frontend_template', 10, 4 );

/**
 * Get the field classes
 * @since 3.6.0
 */
function pewc_get_field_classes( $item, $id, $post_id, $product, $count_fields, $number_teaser_fields, $display, $all_group_conditions=false, $calculation_components=false ) {

	$classes = array( $id );

	$classes[] = 'pewc-group-' . esc_attr( $item['field_type'] );
	$classes[] = 'pewc-item-' . esc_attr( $item['field_type'] );
	$classes[] = 'pewc-field-' . esc_attr( $item['field_id'] );
	$classes[] = 'pewc-field-count-' . esc_attr( $count_fields );

	// Hide certain fields if we're using the lightbox
	if( $display == 'lightbox' && $count_fields >= $number_teaser_fields ) {
		$classes[] = 'pewc-hidden-teaser-field';
	} else if( $display == 'lightbox' && $count_fields < $number_teaser_fields ) {
		$classes[] = 'pewc-lightbox-field';
	}

	if( ! empty( $item['field_required'] ) ) {
		$classes[] = 'required-field';
	}
	if( pewc_is_field_hidden( $item, $post_id ) ) {
		$classes[] = 'pewc-hidden-field';
	}
	if( ! empty( $item['field_price'] ) ) {
		$classes[] = 'pewc-has-field-price';
	}
	if( ! empty( $item['per_character'] ) ) {
		$classes[] = 'pewc-per-character-pricing';
	}
	if( ! empty( $item['field_maxchars'] ) ) {
		$classes[] = 'pewc-has-maxchars';
	}
	if( ! empty( $item['multiply'] ) ) {
		$classes[] = 'pewc-multiply-pricing';
	}
	if( ! empty( $item['field_flatrate'] ) ) {
		$classes[] = 'pewc-flatrate';
	}
	if( ! empty( $item['variation_field'] ) ) {
		$classes[] = 'pewc-variation-dependent';
	}
	if( ! empty( $item['field_percentage'] ) ) {
		$classes[] = 'pewc-percentage';
	}

	$hidden_calculation = ! empty( $item['hidden_calculation'] );
	if( $hidden_calculation && $item['field_type'] == 'calculation' ) {
		$classes[] = 'pewc-hidden-calculation';
	}

	$field_image = pewc_get_field_image( $item, $id );
	if( $field_image ) {
		$classes[] = 'pewc-has-field-image';
	}

	if( $item['field_type'] == 'products' && ! empty( $item['products_layout'] ) ) {
		$classes[] = 'pewc-item-products-' . esc_attr( $item['products_layout'] );
	}

	if( $item['field_type'] == 'upload' && ! empty( $item['field_preview_image'] ) ) {
		$classes[] = 'pewc-image-preview';
	}

	// Is this field part of a condition somewhere?
	if( isset( $all_group_conditions[$id] ) ) {
		$classes[] = 'pewc-condition-trigger';
	}

	if( isset( $calculation_components[$item['field_id']] ) ) {
		$classes[] = 'pewc-calculation-trigger';
	}

	$classes = apply_filters( 'pewc_filter_single_product_classes', $classes, $item );

	return $classes;

}

/**
 * Get the field price
 * @since 3.6.0
 */
function pewc_get_field_price( $item, $product ) {

	$price = 0;

	if( isset( $item['field_price'] ) && $item['field_type'] != 'products' ) {

		$price = floatval( $item['field_price'] );
		// Filter the field price, e.g. for role-based pricing
		$price = apply_filters( 'pewc_get_field_price_before_maybe_include_tax', $price, $item, $product );

	}

	$price = pewc_maybe_include_tax( $product, $price );

	/**
	 * Filtered by pewc_get_multicurrency_price
	 */
	return apply_filters( 'pewc_filter_field_price', $price, $item, $product );

}

/**
 * Get the option price
 * @since 3.6.0
 */
function pewc_get_option_price( $option_value, $item, $product ) {

	$option_price = ! empty( $option_value['price'] ) ? $option_value['price'] : 0;
	$option_price = apply_filters( 'pewc_get_option_price_before_maybe_include_tax', $option_price, $option_value, $product );
	$option_price = pewc_maybe_include_tax( $product, $option_price );

	/**
	 * Filtered by pewc_get_multicurrency_price
	 */
	return apply_filters( 'pewc_filter_option_price', $option_price, $item, $product );

}

/**
 * Get the field label
 */
function pewc_field_label( $item, $id, $group_layout='ul' ) {

	global $product;

	$open_td = '';
	$close_td = '';
	if( $group_layout == 'table' ) {
		$open_td = '<td>';
		$close_td = '</td>';
	}

	$label = $open_td;
	$price_label = '';

	if( isset( $item['field_label'] ) || isset( $item['field_price'] ) ) {

		$label .= '<label class="pewc-field-label" for="' . esc_attr( $id ) . '">';
		if( isset( $item['field_label'] ) ) {
			$label .= wp_kses_post( $item['field_label'] );
		}
		$label .= '<span class="required"> &#42;</span>';

		// Get the price
		if( ! empty( $item['field_price'] ) && $item['field_type'] != 'name_price' && $item['field_type'] != 'products' ) {

			$field_price = pewc_get_field_price( $item, $product );

			// Check if it's a percentage
			$price = apply_filters( 'pewc_filter_display_price_for_percentages', $field_price, $product, $item );

			// Get display price according to inc tax / ex tax setting

			// $price = pewc_maybe_include_tax( $product, $price );

			// Format the price
			$formatted_price = apply_filters(
				'pewc_field_formatted_price',
				pewc_wc_format_price( $price ),
				$item,
				$product,
				$price
			);

			$price_label .= '<span class="pewc-field-price"> ' . $formatted_price;
			if( ! empty( $item['per_character'] ) && ( $item['field_type'] == 'text' || $item['field_type'] == 'textarea' ) ) {
				$price_label .= ' <span class="pewc-per-character-label">' . __( 'per character', 'pewc' ) . '</span>';
			}
			$price_label .= '</span>';

		}

		$label .= $price_label;
		$label = apply_filters( 'pewc_field_label_end', $label, $product, $item );

		$label .= '</label>';

		if( $group_layout == 'table' && pewc_enable_tooltips() != 'yes' ) {
			$label .= pewc_get_field_description( $item, $id, $group_layout );
		}

		$label .= $close_td;

	}

	return apply_filters( 'pewc_filter_field_label', $label, $item, $id );

}

/**
 * Return the markup for the field image, if present
 * @since 1.7.2
 * @return Markup
 */
function pewc_get_field_image( $item, $id ) {
	$image = '';
	if( ! empty( $item['field_image'] ) ) {
		$attachment_id = $item['field_image'];
		$size = apply_filters( 'pewc_filter_field_image_size', 'thumbnail' );
		$image = wp_get_attachment_image( $attachment_id, $size );
	}
	return apply_filters( 'pewc_filter_field_image', $image, $item, $id );
}

/**
 * Return the URL for the field image, if present
 * @since 1.7.2
 * @return Markup
 */
function pewc_get_field_image_url( $item, $size ) {
	$url = '';
	if( ! empty( $item['field_image'] ) ) {
		$attachment_id = $item['field_image'];
		$url = wp_get_attachment_image_src( $attachment_id, $size );
	}
	return $url;
}

/**
 * Show the description for the list view
 */
function pewc_field_description_list_layout( $item, $id, $group_layout='ul' ) {
	if( $group_layout == 'ul' ) {
		pewc_field_description( $item, $id, $group_layout );
	}
}
add_action( 'pewc_after_field_template', 'pewc_field_description_list_layout', 10, 3 );

/**
 * Get the description
 */
function pewc_get_field_description( $item, $id, $group_layout='ul' ) {

	$additional_info = '';
	if( ! empty( $item['field_minval'] ) && ( $item['field_type'] == 'name_price' || $item['field_type'] == 'number' ) ) {
		if( $item['field_type'] == 'name_price' ) {
			$min = wc_price( $item['field_minval'] );
		} else {
			$min = esc_html( $item['field_minval'] );
		}
		$additional_info .= sprintf( '<small>%s: %s</small>',
			__( 'Min', 'pewc' ),
			$min
		);
	}
	if( ! empty( $item['field_maxval'] ) && ( $item['field_type'] == 'name_price' || $item['field_type'] == 'number' ) ) {
		if( $item['field_type'] == 'name_price' ) {
			$max = wc_price( $item['field_maxval'] );
		} else {
			$max = esc_html( $item['field_maxval'] );
		}
		$additional_info .= sprintf( '<small>%s: %s</small>',
			__( 'Max', 'pewc' ),
			$max
		);
	}
	if( ! empty( $item['field_minchars'] ) && ( $item['field_type'] == 'text' || $item['field_type'] == 'textarea' ) ) {
		$additional_info .= sprintf( '<small>%s: %s %s</small>',
			__( 'Min', 'pewc' ),
			esc_html( $item['field_minchars'] ),
			__( 'characters', 'pewc' )
		);
	}
	if( ! empty( $item['field_maxchars'] ) && ( $item['field_type'] == 'text' || $item['field_type'] == 'textarea' ) ) {
		$additional_info .= sprintf( '<small>%s: %s %s</small>',
			__( 'Max', 'pewc' ),
			esc_html( $item['field_maxchars'] ),
			__( 'characters', 'pewc' )
		);
	}
	if( $item['field_type'] == 'upload' ) {
		$max = pewc_get_max_upload();
		$file_types = pewc_get_pretty_permitted_mimes();
		$additional_info .= sprintf( '<small>%s: %s MB</small>',
			apply_filters( 'pewc_filter_max_file_size_message', __( 'Max file size', 'pewc' ) ),
			$max
		);
		$additional_info .= sprintf( '<small>%s: %s</small>',
			apply_filters( 'pewc_filter_permitted_file_types_message', __( 'Permitted file types', 'pewc' ) ),
			$file_types
		);
	}

	$field_description = '';
	if( pewc_enable_tooltips() != 'yes' && ! apply_filters( 'pewc_description_as_placeholder', false, $item ) ) {
		$field_description = ! empty( $item['field_description'] ) ? $item['field_description'] : '';
	}

	if( ! empty( $item['field_description'] ) || $additional_info ) {
		return apply_filters(
			'pewc_filter_field_description',
			sprintf(
				'<p class="pewc-description">%s%s</p>',
				// wp_kses_post( $field_description ),
				wp_kses_post( $field_description ),
				$additional_info
			),
			$item,
			$additional_info
		);
	}
}

function pewc_field_description( $item, $id, $group_layout='ul' ) {

	echo pewc_get_field_description( $item, $id, $group_layout='ul' );

}

/**
 * Filter the price label
 */
function pewc_get_price_html( $price, $product ) {

	// Only for products that have Product Add-Ons
	if( pewc_has_product_extra_groups( $product->get_id() ) != 'yes' ) {
		return $price;
	}
	// Override with any product specific settings
	$pewc_price_label = $product->get_meta( 'pewc_price_label' );
	$pewc_price_display = $product->get_meta( 'pewc_price_display' );
	if( $pewc_price_label && $pewc_price_display == 'before' ) {
		$price = sprintf(
			'<span class="pewc-label-before">%s</span> %s',
			$pewc_price_label,
			$price
		);
		// $price = $pewc_price_label . ' ' . $price;
	} else if( $pewc_price_label && $pewc_price_display == 'after' ) {
		$price = sprintf(
			'%s <span class="pewc-label-after">%s</span>',
			$price,
			$pewc_price_label
		);
	} else if( $pewc_price_display == 'hide' ) {
		$price = sprintf(
			'<span class="pewc-label-hidden">%s</span>',
			$pewc_price_label
		);
	} else {
		// If no product label set, check the global
		$pewc_price_label = get_option( 'pewc_price_label' );
		$pewc_price_display = get_option( 'pewc_price_display' );
		if( $pewc_price_label && $pewc_price_display == 'before' ) {
			$price = sprintf(
				'<span class="pewc-label-before">%s</span> %s',
				$pewc_price_label,
				$price
			);
		} else if( $pewc_price_label && $pewc_price_display == 'after' ) {
			$price = sprintf(
				'%s <span class="pewc-label-after">%s</span>',
				$price,
				$pewc_price_label
			);
		} else if( $pewc_price_display == 'hide' ) {
			$price = sprintf(
				'<span class="pewc-label-hidden">%s</span>',
				$pewc_price_label
			);
		}
	}

	return $price;

}
add_filter( 'woocommerce_get_price_html', 'pewc_get_price_html', 10, 2 );

/**
 * Filter the price label
 */
function pewc_minimum_price_html( $price, $product ) {

	$minimum_price = get_post_meta( $product->get_id(), 'pewc_minimum_price', true );

	if( $minimum_price ) {

		$min_price_html = sprintf(
			'<p class="pewc-minimum-price">%s %s</p>',
			__( 'Minimum price:', 'pewc' ),
			wc_price( $minimum_price )
		);

		return $price . $min_price_html;

	} else {

		return $price;

	}

}
add_filter( 'woocommerce_get_price_html', 'pewc_minimum_price_html', 100, 2 );

/**
 * Whether image replacement is enabled
 * @since 3.5.0
 */
function pewc_get_add_on_image_action() {
	// replace_hide	Hides the add-on image but replaces main image
	// replace			Displays add-on image and replaces main image
	// false				Just displays the add-on image
	return apply_filters( 'pewc_get_add_on_image_action', false );
}

/**
 * Get the default value for our field
 * Used on the front end
 * @since 3.5.0
 */
function pewc_get_default_value( $id, $item, $posted ) {

	// Set default value if it exists
	$value = ( ! empty( $item['field_default_hidden'] ) || ( isset( $item['field_default_hidden'] ) && $item['field_default_hidden'] === '0' ) ) ? $item['field_default_hidden'] : '';

	// Ensure fields are repopulated after failed validation
	$value = ! empty( $posted[$id] ) ? $posted[$id] : $value;

	return apply_filters( 'pewc_default_field_value', $value, $id, $item, $posted );

}

/**
 * Get the separator between the add-on label and the add-on price
 * The default separator used be a minus sign
 * Now it's a plus sign
 * @since 3.7.7
 */
function pewc_add_on_price_separator( $sep=false, $item=false ) {

	$separator = get_option( 'pewc_price_separator', false );
	$sep = ' ' . $separator . ' ';
	return $sep;

}
add_filter( 'pewc_option_price_separator', 'pewc_add_on_price_separator', 1, 2 );

/**
 * Do we update the product price label?
 * @since 3.7.14
 */
function pewc_get_update_price_label() {

	$update = get_option( 'pewc_update_price_label', 'no' );
	return $update;

}

/**
 * Add an extra class to the main price display so we can adjust it
 */
function pewc_filter_price_class( $class ) {
	return $class . ' pewc-main-price';
}
add_filter( 'woocommerce_product_price_class', 'pewc_filter_price_class' );

/**
 * Remove SKUs from child variant names
 * @since 3.7.15
 */
function pewc_exclude_skus_child_variants( $name, $variant ) {
	$exclude = get_option( 'pewc_exclude_skus', 'no' );
	if( $exclude == 'yes' ) {
		$name = $variant->get_name();
	}
	return $name;
}
add_filter( 'pewc_variation_name_variable_child_select', 'pewc_exclude_skus_child_variants', 10, 2 );

/**
 * Add the tax suffix after all add-on prices
 * @since 3.7.15
 */
function pewc_add_tax_suffix_options( $name, $item, $product, $price=false ) {
	$enable = pewc_show_price_suffix();
	if( $enable == 'yes' ) {
		$suffix = $product->get_price_suffix( $price, 1 );
		if( $suffix ) {
			$name .= ' ' . $suffix;
		}
	}
	return $name;
}
add_filter( 'pewc_option_name', 'pewc_add_tax_suffix_options', 10, 3 );
add_filter( 'pewc_field_formatted_price', 'pewc_add_tax_suffix_options', 10, 4 );
