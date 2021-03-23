<?php
/**
 * The markup for a field item in the admin
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item_key = $item['field_id'];
$per_char_checked = ! empty( $item['per_character'] );

// Update radio-image to image-swatch
if( $item['field_type'] == 'radio_image' ) $item['field_type'] = 'image_swatch';

$item_classes = array(
	'field-item',
	'collapsed-field',
	'field-type-' . esc_attr( $item['field_type'] )
);
if( $per_char_checked ) {
	$item_classes[] = 'per-char-selected';
}
if( ! empty( $item['products_layout'] ) ) {
	$item_classes[] = 'products-layout-' . $item['products_layout'];
}
if( ! empty( $item['products_quantities'] ) ) {
	$item_classes[] = 'products-quantities-' . $item['products_quantities'];
} ?>

<li id="pewc_group_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-size-count="<?php echo esc_attr( $item_key ); ?>" data-item-id="<?php echo esc_attr( $item['field_id'] ); ?>" class="<?php echo join( ' ', $item_classes ); ?>">

	<div class="pewc-fields-wrapper pewc-clickable-heading">
		<?php
		$field_label = ! empty( $item['field_label'] ) ? $item['field_label'] : '';
		printf(
			'<h3 class="pewc-field-meta-heading">%s <span class="meta-item-id">%s</span>: <span class="pewc-display-field-title">%s</span></h3>',
			__( 'Field', 'pewc' ),
			'&#35;' . $item_key,
			stripslashes( $field_label )
		); ?>

		<?php include( PEWC_DIRNAME . '/templates/admin/field-meta-actions.php' ); ?>

	</div>

	<div class="product-extra-field">

		<div class="pewc-fields-wrapper pewc-fields-heading">
			<div class="product-extra-field-third">
				<input type="hidden" class="pewc-id pewc-hidden-id-field" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[id]" value="pewc_group_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>">
				<input type="hidden" class="pewc-group-id pewc-hidden-id-field" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[group_id]" value="<?php echo esc_attr( $group_id ); ?>">
				<input type="hidden" class="pewc-field-id pewc-hidden-id-field" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_id]" value="<?php echo esc_attr( $item_key ); ?>">
				<label>
					<?php _e( 'Field Label', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter a label to appear with this field', 'pewc' ); ?>
				</label>
				<input type="text" class="pewc-field-item pewc-field-label" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_label]" value="<?php echo stripslashes( $field_label ); ?>">
			</div>
			<div class="product-extra-field-third">
				<label>
					<?php _e( 'Field Type', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select the field type', 'pewc' ); ?>
				</label>
				<?php $type = $item['field_type']; ?>
				<select class="pewc-field-item pewc-field-type" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_type]" id="field_type_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" data-field-type="<?php echo $type; ?>">
					<?php
					foreach( $field_types as $key=>$value ) {
						$selected = selected( $type, $key, false );
						echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					} ?>
				</select>
			</div>
			<div class="product-extra-field-third pewc-field-price-wrapper">
				<?php $field_price = isset( $item['field_price'] ) ? $item['field_price'] : ''; ?>
				<label>
					<?php _e( 'Field Price', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter the amount that will be added to the price if the user enters a value for this field', 'pewc' ); ?>
				</label>
				<input type="number" class="pewc-field-item pewc-field-price" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_price]" value="<?php echo esc_attr( $field_price ); ?>" step="<?php echo apply_filters( 'pewc_field_item_price_step', '0.01', $item ); ?>">
			</div>

		</div><!-- .pewc-fields-wrapper -->

		<?php include( PEWC_DIRNAME . '/templates/admin/views/role-based-prices.php' ); ?>

		<?php do_action( 'pewc_end_fields_heading', $item ); ?>

		<div class="pewc-hide-if-not-pro">

			<?php

			if( apply_filters( 'pewc_show_calculation_params', true, $item, $post_id ) ) {
				include( PEWC_DIRNAME . '/templates/admin/views/calculation-fields.php' );
				do_action( 'pewc_after_calculation_fields', $item, $group_id, $item_key );
			}
			if( apply_filters( 'pewc_show_option_field_params', true, $item, $post_id ) ) {
				include( PEWC_DIRNAME . '/templates/admin/views/option-fields.php' );
			}
			if( apply_filters( 'pewc_show_information_params', true, $item, $post_id ) ) {
				include( PEWC_DIRNAME . '/templates/admin/views/information.php' );
			} ?>

			<?php if( apply_filters( 'pewc_show_checkbox_group_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-checkbox-group-fields">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Min Number', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional minimum number of checkboxes that the user can select for this field', 'pewc' ); ?>
						</label>
						<?php $minchecks = isset( $item['field_minchecks'] ) ? $item['field_minchecks'] : ''; ?>
						<input type="number" class="pewc-field-item pewc-field-minchecks" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_minchecks]" value="<?php echo esc_attr( $minchecks ); ?>">
					</div>
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Max Number', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional maximum number of checkboxes that the user can select for this field', 'pewc' ); ?>
						</label>
						<?php $maxchecks = isset( $item['field_maxchecks'] ) ? $item['field_maxchecks'] : ''; ?>
						<input type="number" class="pewc-field-item pewc-field-maxchecks" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_maxchecks]" value="<?php echo esc_attr( $maxchecks ); ?>">
					</div>
				</div><!-- .pewc-checkbox-group-wrapper -->

		<?php } ?>

		<?php if( apply_filters( 'pewc_show_products_params', true, $item, $post_id ) ) { ?>

			<div class="pewc-fields-wrapper pewc-products-extras">
				<div class="product-extra-field-full">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Child Products', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select which products you\'d like to associate with this product', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-two-thirds-right">
						<?php $simple_products = pewc_get_simple_products();
						$child_product_method = pewc_child_products_method( $post_id, $item_key );
						if( $child_product_method != 'variable_subscriptions' ) {
							// Use the standard WooCommerce AJAX methods to search for child products and/or child variations ?>
							<select class="pewc-field-item wc-product-search pewc-field-child_products pewc-data-options" data-options="" multiple="multiple" style="width: 100%;" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[child_products][]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose the child products', 'pewc' ); ?>" data-action="<?php echo $child_product_method; ?>" data-include="" data-exclude="<?php echo intval( $post_id ); ?>">
								<?php
								if( ! empty( $item['child_products'] ) ) {
									$child_products = $item['child_products'];
									foreach( $child_products as $product_id ) {
										$product = wc_get_product( $product_id );
										// if( is_object( $product ) && $product->is_type( 'simple' ) ) {
										if( is_object( $product ) ) {
											echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, true ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
										}
									}
								} ?>
							</select>
							<?php } else {
								// Populate field with subscription variations ?>
								<select class="pewc-field-item pewc-variation-field pewc-field-child_products pewc-data-options" data-options="" multiple="multiple" style="width: 100%;" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[child_products][]" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose the child subscription variations', 'pewc' ); ?>">
									<?php
									$subscription_variations = pewc_get_subscription_variations();
									$child_products = ! empty( $item['child_products'] ) ? $item['child_products'] : array();
									foreach( $subscription_variations as $variation_id=>$variation_name ) {
										// $product = wc_get_product( $product_id );
										$selected = ( is_array( $child_products ) && in_array( $variation_id, $child_products ) ) ? 'selected' : '';
										echo '<option value="' . esc_attr( $variation_id ) . '"' . $selected . '>' . wp_kses_post( $variation_name ) . '</option>';
									} ?>
								</select>
							<?php } ?>
						</div>
					</div>
				</div>

			<?php } ?>

			<?php if( apply_filters( 'pewc_show_products_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-products-extras">

					<div class="pewc-products-layout product-extra-field-third">
						<label>
							<?php _e( 'Products Layout', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose how child products will be displayed.', 'pewc' ); ?>
						</label>
						<?php $products_layout = isset( $item['products_layout'] ) ? $item['products_layout'] : ''; ?>
						<select class="pewc-field-item pewc-field-products_layout" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[products_layout]">
							<option value="checkboxes" <?php selected( $products_layout, 'checkboxes', true ); ?>><?php _e( 'Checkboxes', 'pewc' ); ?></option>
							<option value="column" <?php selected( $products_layout, 'column', true ); ?>><?php _e( 'Column', 'pewc' ); ?></option>
							<option value="radio" <?php selected( $products_layout, 'radio', true ); ?>><?php _e( 'Radio', 'pewc' ); ?></option>
							<option value="select" <?php selected( $products_layout, 'select', true ); ?>><?php _e( 'Select', 'pewc' ); ?></option>
							<option value="swatches" <?php selected( $products_layout, 'swatches', true ); ?>><?php _e( 'Swatches', 'pewc' ); ?></option>
							<option value="grid" <?php selected( $products_layout, 'grid', true ); ?>><?php _e( 'Variations Grid', 'pewc' ); ?></option>
						</select>
					</div>

					<div class="pewc-products-quantities product-extra-field-third">
						<label>
							<?php _e( 'Products Quantities', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose whether to link the quantities of the parent product and child product so that they are always the same, or to limit the quantity of the child product to one only.', 'pewc' ); ?>
						</label>
						<?php $products_quantities = isset( $item['products_quantities'] ) ? $item['products_quantities'] : ''; ?>
						<select class="pewc-field-item pewc-field-products_quantities" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[products_quantities]">
							<option value="independent" <?php selected( $products_quantities, 'independent', true ); ?>><?php _e( 'Independent', 'pewc' ); ?></option>
							<option value="linked" <?php selected( $products_quantities, 'linked', true ); ?>><?php _e( 'Linked', 'pewc' ); ?></option>
							<option value="one-only" <?php selected( $products_quantities, 'one-only', true ); ?>><?php _e( 'One only', 'pewc' ); ?></option>
						</select>
					</div>

					<div class="pewc-products-select-placeholder product-extra-field-third">
						<label>
							<?php _e( 'Select Field Placeholder', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter instructional text in here to appear as the first option in the select field.', 'pewc' ); ?>
						</label>
						<?php $placeholder = ( ! empty( $item['select_placeholder'] ) ) ? $item['select_placeholder'] : ''; ?>
						<input type="text" class="pewc-field-item pewc-field-select_placeholder" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[select_placeholder]" value="<?php echo esc_attr( $placeholder ); ?>">
					</div>

					<div class="pewc-allow-none product-extra-field-third product-extra-field-last">
						<?php $checked = ! empty( $item['allow_none'] ); ?>
						<?php $disabled = ( isset( $item['products_layout'] ) && ( $item['products_layout'] == 'checkboxes' || $item['products_layout'] == 'column' ) ) ? 'disabled' : '';
						if( $disabled ) $checked = 0; ?>
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Child Product Not Required', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select this option if the child product is not required. Note that if you choose the checkboxes layout, this setting is disabled.', 'pewc' ); ?>
						</label>
						<input <?php checked( $checked, 1, true ); ?> <?php echo $disabled; ?> type="checkbox" class="pewc-field-item pewc-checkbox-block pewc-field-allow_none" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[allow_none]" value="1">
					</div>

				</div>
			<?php } ?>

			<?php if( apply_filters( 'pewc_show_image_swatch_params', true, $item, $post_id ) ) { ?>
				<div class="pewc-fields-wrapper pewc-radio-image-extras">
					<div class="product-extra-field-third">
						<?php $number_columns = ( isset( $item['number_columns'] ) ) ? intval( $item['number_columns'] ) : 3;
						$number_columns = max( 1, $number_columns ); ?>
						<label>
							<?php _e( 'Number Columns', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose how many columns to display your images in', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-number-columns" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[number_columns]" value="<?php echo esc_attr( $number_columns ); ?>" min="1" max="10" step="1">
					</div>
					<div class="product-extra-field-third">
						<?php $checked = ! empty( $item['hide_labels'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-hide-labels" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[hide_labels]" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Hide Labels?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option to just display the images with no text', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-third pewc-allow-multiple-wrapper">
						<?php $checked = ! empty( $item['allow_multiple'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-allow-multiple" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[allow_multiple]" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Allow Multiple?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option to allow multiple selections (checkbox instead of radio)', 'pewc' ); ?>
						</label>
					</div>
				</div>
			<?php } ?>

			<?php if( apply_filters( 'pewc_show_products_params', true, $item, $post_id ) ) {
				// Min, max and discount fields for Products field ?>
				<div class="pewc-fields-wrapper pewc-child-product-min-max-extras">
					<div class="product-extra-field-third">
						<?php $min_products = ( isset( $item['min_products'] ) ) ? intval( $item['min_products'] ) : ''; ?>
						<label>
							<?php _e( 'Min Child Products', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Specify a minimum number of products the user must choose from this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-min-child-products" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[min_products]" value="<?php echo esc_attr( $min_products ); ?>" min="0" max="" step="1">
					</div>
					<div class="product-extra-field-third">
						<?php $max_products = ( isset( $item['max_products'] ) ) ? intval( $item['max_products'] ) : ''; ?>
						<label>
							<?php _e( 'Max Child Products', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Specify a maximum number of products the user must choose from this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-max-child-products" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[max_products]" value="<?php echo esc_attr( $max_products ); ?>" min="0" max="" step="1">
					</div>
				</div>
			<?php } ?>

			<?php if( apply_filters( 'pewc_show_products_params', true, $item, $post_id ) ) {
				// Min, max and discount fields for Products field ?>
				<div class="pewc-fields-wrapper pewc-child-product-discount-extras">
					<div class="product-extra-field-third">
						<?php $child_discount = ( isset( $item['child_discount'] ) ) ? floatval( $item['child_discount'] ) : ''; ?>
						<label>
							<?php _e( 'Discount', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter a discount for products purchased in this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-child-discount" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[child_discount]" value="<?php echo esc_attr( $child_discount ); ?>" min="0" max="" step="0.01">
					</div>
					<div class="product-extra-field-third">
						<?php $discount_type = ( isset( $item['discount_type'] ) ) ? $item['discount_type'] : ''; ?>
						<label>
							<?php _e( 'Discount Type', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose how the discount is calculated', 'pewc' ); ?>
						</label>
						<select class="pewc-field-item pewc-child-discount-type" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[discount_type]">
							<option value="fixed" <?php selected( $discount_type, 'fixed', true ); ?>><?php _e( 'Fixed Amount', 'pewc' ); ?></option>
							<option value="percentage" <?php selected( $discount_type, 'percentage', true ); ?>><?php _e( 'Percentage', 'pewc' ); ?></option>
						</select>
					</div>
				</div>
			<?php } ?>

			<div class="pewc-fields-wrapper pewc-misc-fields">
				<div class="pewc-required product-extra-field-third">
					<?php
					$checked = ! empty( $item['field_required'] ); ?>
					<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-required" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_required]" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Required Field?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to require this field', 'pewc' ); ?>
					</label>
				</div>
				<div class="pewc-flatrate product-extra-field-third">
					<?php $checked = ! empty( $item['field_flatrate'] ); ?>
					<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-flatrate" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_flatrate]" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Flat Rate?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option if you only want to charge for this field once, irrespective of how many times it\'s added to the cart', 'pewc' ); ?>
					</label>
				</div>
				<?php do_action( 'pewc_end_checkbox_row', $item, $group_id, $item_key ); ?>
				<?php if( pewc_is_pro() ) { ?>
					<div class="pewc-percentage product-extra-field-third">
						<?php $checked = ! empty( $item['field_percentage'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-percentage" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_percentage]" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Percentage?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option for the field price to be set as a percentage of the product price', 'pewc' ); ?>
						</label>
					</div>
				<?php } ?>
			</div><!-- .pewc-fields-wrapper -->

			<?php if( apply_filters( 'pewc_show_upload_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-upload-fields">

					<div class="product-extra-field-third">
						<?php $checked = ! empty( $item['multiple_uploads'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-multiple-uploads" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[multiple_uploads]" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Allow multiple uploads?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option to allow the user to upload multiple files', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-third pewc-ajax-upload-only">
						<?php $checked = ! empty( $item['multiply_price'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-multiply-price" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[multiply_price]" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Price per upload?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option to multiply the field price by the number of uploaded files', 'pewc' ); ?>
						</label>
					</div>
					<div class="product-extra-field-third pewc-ajax-upload-only">
						<?php $max_files = ! empty( $item['max_files'] ) ? $item['max_files'] : ''; ?>
						<label>
							<?php _e( 'Max Files', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Maximum number of files if multiple files uploads are enabled', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-field-max-files" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[max_files]" min="1" value="<?php echo esc_attr( $max_files ); ?>">
					</div>

				</div><!-- .pewc-upload-fields -->

			<?php do_action( 'pewc_after_uploads_fields', $group_id, $item_key, $item, $post_id );

			} ?>

			<?php if( apply_filters( 'pewc_show_character_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-char-fields">
					<div class="product-extra-field-third">
						<?php $min_chars = ! empty( $item['field_minchars'] ) ? $item['field_minchars'] : ''; ?>
						<label>
							<?php _e( 'Min Chars', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional minimum number of characters for this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-field-minchars" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_minchars]" value="<?php echo esc_attr( $min_chars ); ?>">
					</div>
					<div class="product-extra-field-third">
						<?php $max_chars = ! empty( $item['field_maxchars'] ) ? $item['field_maxchars'] : ''; ?>
						<label>
							<?php _e( 'Max Chars', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional maximum number of characters for this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-field-maxchars" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_maxchars]" value="<?php echo esc_attr( $max_chars ); ?>">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Price Per Character?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select this if you want to charge per character', 'pewc' ); ?>
						</label>
						<input <?php checked( $per_char_checked, 1, true ); ?> type="checkbox" class="pewc-checkbox-block pewc-field-per-character" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[per_character]" value="1">
					</div>
				</div><!-- .pewc-fields-wrapper -->

				<div class="pewc-fields-wrapper pewc-extrachar-fields">
					<div class="product-extra-field-third">
						<?php $field_freechars = ! empty( $item['field_freechars'] ) ? $item['field_freechars'] : ''; ?>
						<label>
							<?php _e( 'Free Chars', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional number of free characters to allow before pricing per character kicks in', 'pewc' ); ?>
						</label>
						<input type="number" min="0" class="pewc-field-item pewc-field-freechars" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_freechars]" value="<?php echo esc_attr( $field_freechars ); ?>">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Only Allow Alphanumeric?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this to allow only alphanumeric characters in this field', 'pewc' ); ?>
						</label>
						<?php $checked = ! empty( $item['field_alphanumeric'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-checkbox-block pewc-field-alphanumeric" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_alphanumeric]" value="1">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Only Charge Alphanumeric?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this to charge just the alphanumeric characters in the field', 'pewc' ); ?>
						</label>
						<?php $checked = ! empty( $item['field_alphanumeric_charge'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-checkbox-block pewc-field-alphanumeric-charge" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_alphanumeric_charge]" value="1">
					</div>
				</div><!-- .pewc-fields-wrapper -->

			<?php } ?>

			<?php if( apply_filters( 'pewc_show_number_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-num-fields">
					<div class="product-extra-field-third">
						<?php $field_minval = ( ! empty( $item['field_minval'] ) || ( isset( $item['field_minval'] ) && $item['field_minval'] === '0' ) ) ? $item['field_minval'] : ''; ?>
						<label>
							<?php _e( 'Min Value', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional minimum value for the field', 'pewc' ); ?>
						</label>
						<input type="number" step="<?php echo apply_filters( 'pewc_min_max_val_step', '1', $item ); ?>" class="pewc-field-item pewc-field-minval" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_minval]" value="<?php echo esc_attr( $field_minval ); ?>">
					</div>
					<div class="product-extra-field-third">
						<?php $field_maxval = ! empty( $item['field_maxval'] ) ? $item['field_maxval'] : ''; ?>
						<label>
							<?php _e( 'Max Value', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional maximum value for the field', 'pewc' ); ?>
						</label>
						<input type="number" step="<?php echo apply_filters( 'pewc_min_max_val_step', '1', $item ); ?>" class="pewc-field-item pewc-field-maxval" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_maxval]" value="<?php echo esc_attr( $field_maxval ); ?>">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Multiply Price?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select this to multiply the value of the field by its price', 'pewc' ); ?>
						</label>
						<?php $checked = ! empty( $item['multiply'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-checkbox-block pewc-field-multiply" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[multiply]" value="1">
					</div>
				</div><!-- .pewc-fields-wrapper -->

			<?php } ?>

			<?php if( apply_filters( 'pewc_show_date_params', true, $item, $post_id ) ) { ?>

				<div class="pewc-fields-wrapper pewc-date-fields">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Min date today?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select this to prevent entering a date in the past', 'pewc' ); ?>
						</label>
						<?php $checked = ! empty( $item['min_date_today'] ); ?>
						<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-field-min_date_today" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[min_date_today]" value="1">
					</div>
					<div class="product-extra-field-third">
						<?php $mindate = isset( $item['field_mindate'] ) ? $item['field_mindate'] : ''; ?>
						<label>
							<?php _e( 'Min date', 'pewc' ); ?>
							<?php echo wc_help_tip( 'The earliest allowable date', 'pewc' ); ?>
						</label>
						<input type="text" class="pewc-field-item pewc-date-field pewc-field-mindate" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_mindate]" value="<?php echo esc_attr( $mindate ); ?>">
					</div>
					<div class="product-extra-field-third">
						<?php $maxdate = isset( $item['field_maxdate'] ) ? $item['field_maxdate'] : ''; ?>
						<label>
							<?php _e( 'Max date', 'pewc' ); ?>
							<?php echo wc_help_tip( 'The latest allowable date', 'pewc' ); ?>
						</label>
						<input type="text" class="pewc-field-item pewc-date-field pewc-field-maxdate" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_maxdate]" value="<?php echo esc_attr( $maxdate ); ?>">
					</div>
				</div><!-- .pewc-fields-wrapper -->

			<?php } ?>

      <?php if( apply_filters( 'pewc_show_color_picker_params', true, $item, $post_id ) ) { ?>

          <div class="pewc-fields-wrapper pewc-color-picker-fields">
              <div class="product-extra-field-third">
                  <?php $color = isset( $item['field_color'] ) ? $item['field_color'] : ''; ?>
                  <label>
                      <?php _e( 'Default color', 'pewc' ); ?>
                      <?php echo wc_help_tip( 'Optionally select a default color for this field', 'pewc' ); ?>
                  </label>
                  <input type="text" class="pewc-field-item pewc-field-color" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_color]" value="<?php echo esc_attr( $color ) ?>">
              </div>
              <div class="product-extra-field-third">
                  <?php $width = isset( $item['field_width'] ) ? $item['field_width'] : ''; ?>
                  <label>
                      <?php _e( 'Element width', 'pewc' ); ?>
                      <?php echo wc_help_tip( 'Optionally chose a different width for the color-picker dropdown (px)', 'pewc' ); ?>
                  </label>
                  <input type="number" class="pewc-field-item pewc-field-width" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_width]" value="<?php echo esc_attr( $width ) ?>">
              </div>
          </div>

          <div class="pewc-fields-wrapper pewc-color-picker-fields">
              <div class="pewc-show product-extra-field-third">
                  <?php $checked = ! empty( $item['field_show'] ); ?>
                  <input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-show" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_show]" value="1">
                  <label class="pewc-checkbox-field-label">
                      <?php _e( 'Show by default?', 'pewc' ); ?>
                      <?php echo wc_help_tip( 'Enable this option if you want to show the color-picker dropdown by default.', 'pewc' ); ?>
                  </label>
              </div>
              <div class="pewc-palettes product-extra-field-third">
                  <?php $checked = ! empty( $item['field_palettes'] ); ?>
                  <input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-palettes" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_palettes]" value="1">
                  <label class="pewc-checkbox-field-label">
                      <?php _e( 'Display common palettes?', 'pewc' ); ?>
                      <?php echo wc_help_tip( 'Enable this option to display a row of common palette colors. This is particularly useful in situations where the currently selected color seems to make no colors available.', 'pewc' ); ?>
                  </label>
              </div>
          </div><!-- .pewc-fields-wrapper -->

      <?php } ?>

			<?php do_action( 'pewc_field_item_extra_fields', $group_id, $item_key, $item, $post_id ); /* DWS */ ?>

			<div class="pewc-fields-wrapper pewc-default-fields">

				<?php $default = pewc_get_field_default( $item ); ?>

				<div class="pewc-default product-extra-field-third">
					<?php // if( $item['field_type'] == 'checkbox' ) {
						$checked = ! empty( $item['field_default_hidden'] ); ?>
						<input <?php checked( $default, 'checked', true ); ?> type="checkbox" class="pewc-field-item pewc-field-default pewc-field-default-field-checkbox" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_default]" value="1">
						<label class="pewc-checkbox-field-label pewc-field-default-field-checkbox">
							<?php _e( 'Default', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Check this to enable this checkbox field by default', 'pewc' ); ?>
						</label>
					<?php // } else if( $item['field_type'] == 'number' ) { ?>
						<label class="pewc-checkbox-field-label pewc-field-default-field-number">
							<?php _e( 'Default', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter a default value', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-item pewc-field-default pewc-field-default-field-number" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_default]" step="<?php echo apply_filters( 'pewc_number_field_step', '1', $item ); ?>" value="<?php echo esc_attr( $default ); ?>">
					<?php // } else { ?>
						<label class="pewc-checkbox-field-label pewc-field-default-field-text">
							<?php _e( 'Default', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter a default value', 'pewc' ); ?>
						</label>
						<input type="text" class="pewc-field-item pewc-field-default pewc-field-default-field-text" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_default]" value="<?php echo esc_attr( $default ); ?>">
					<?php // }
					$default_hidden = isset( $item['field_default_hidden'] ) ? $item['field_default_hidden'] : ''; ?>
					<input type="hidden" class="pewc-field-item pewc-field-default pewc-field-default-hidden" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_default_hidden]" value="<?php echo esc_attr( $default ); ?>">
				</div>
			</div><!-- .pewc-fields-wrapper -->

			<div class="pewc-fields-wrapper pewc-desc-image-wrapper">
				<?php $src = wc_placeholder_img_src();
				$image_wrapper_classes = array(
					'pewc-field-image-' . $item_key
				);
				$remove_class = '';
				$field_image = '';
				if( ! empty( $item['field_image'] ) ) {
					$field_image = $item['field_image'];
					$src = wp_get_attachment_image_src( $item['field_image'] );
					$src = $src[0];
					$image_wrapper_classes[] = 'has-image';
					$remove_class = 'remove-image';
				} ?>
				<div class="product-extra-field-third pewc-field-image <?php echo join( ' ', $image_wrapper_classes ); ?>">
					<label>
						<?php _e( 'Field Image', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional image to accompany the field', 'pewc' ); ?>
					</label>
					<div class='image-preview-wrapper'>
						<a href="#" class="pewc-upload-button <?php echo esc_attr( $remove_class ); ?>" data-item-id="<?php echo esc_attr( $item_key ); ?>">
							<img data-placeholder="<?php echo esc_attr( wc_placeholder_img_src() ); ?>" src="<?php echo esc_url( $src ); ?>" width="100" height="100" style="max-height: 100px; width: 100px;">
						</a>
					</div>
					<input type="hidden" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_image]" class="pewc-field-item pewc-image-attachment-id" value="<?php echo esc_attr( $field_image ); ?>">
				</div>
				<div class="product-extra-field-two-thirds-right pewc-description">
					<?php $description = isset( $item['field_description'] ) ? $item['field_description'] : ''; ?>
					<label>
						<?php _e( 'Field Description', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional description for the field', 'pewc' ); ?>
					</label>
					<textarea class="pewc-field-item pewc-field-description" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[field_description]"><?php echo esc_html( $description ); ?></textarea>
				</div>
			</div><!-- .pewc-fields-wrapper -->

			<div class="pewc-fields-wrapper pewc-fields-conditionals">
				<label><?php _e( 'Conditions', 'pewc' ); ?></label>
				<?php include( PEWC_DIRNAME . '/templates/admin/condition.php' ); ?>
			</div><!-- .pewc-fields-wrapper -->

			<?php // Add your own stuff here
			do_action( 'pewc_end_product_extra_field', $group_id, $item_key, $item, $post_id ); ?>

		</div>

	</div><!-- .product-extra-field -->
</li>
