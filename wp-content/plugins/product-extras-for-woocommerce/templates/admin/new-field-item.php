<?php
/**
 * The markup for a new field item in the admin
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<li data-size-count="" data-item-id="" class="new-field-item field-item">

	<div class="pewc-fields-wrapper pewc-clickable-heading">
		<?php
		printf(
			'<h3 class="pewc-field-meta-heading">%s &#35;<span class="meta-item-id"></span>: <span class="pewc-display-field-title"></span></h3>',
			__( 'Field', 'pewc' )
		);

		include( PEWC_DIRNAME . '/templates/admin/field-meta-actions.php' ); ?>
	</div>

	<div class="product-extra-field">

		<div class="pewc-fields-wrapper pewc-fields-heading">

			<div class="product-extra-field-third">
				<input type="hidden" class="pewc-id pewc-hidden-id-field" name="" value="">
				<input type="hidden" class="pewc-group-id pewc-hidden-id-field" name="" value="">
				<input type="hidden" class="pewc-field-id pewc-hidden-id-field" name="" value="">
				<label>
					<?php _e( 'Field Label', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter a label to appear with this field', 'pewc' ); ?>
				</label>
				<input type="text" class="pewc-field-item pewc-field-label" name="" value="">
			</div>
			<div class="product-extra-field-third">
				<label>
					<?php _e( 'Field Type', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select the field type', 'pewc' ); ?>
				</label>
				<select class="pewc-field-item pewc-field-type" name="" id="">
					<?php $field_types = pewc_field_types();
					foreach( $field_types as $key=>$value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					} ?>
				</select>
			</div>
			<div class="product-extra-field-third pewc-field-price-wrapper">
				<label>
					<?php _e( 'Field Price', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Enter the amount that will be added to the price if the user enters a value for this field', 'pewc' ); ?>
				</label>
				<input type="number" class="pewc-field-item pewc-field-price" name="" value="" step="<?php echo apply_filters( 'pewc_field_item_price_step', '0.01', false ); ?>">
			</div>
		</div>

		<?php include( PEWC_DIRNAME . '/templates/admin/views/role-based-prices.php' ); ?>

		<?php do_action( 'pewc_end_fields_heading' ); ?>

		<div class="pewc-hide-if-not-pro">

			<?php
			include( PEWC_DIRNAME . '/templates/admin/views/calculation-fields.php' );
			include( PEWC_DIRNAME . '/templates/admin/views/option-fields-new.php' );
			include( PEWC_DIRNAME . '/templates/admin/views/information.php' ); ?>

			<?php if( pewc_is_pro() ) { ?>
				<div class="pewc-fields-wrapper pewc-checkbox-group-fields">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Min Number', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional minimum number of checkboxes that the user can select for this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-minchecks" name="">
					</div>
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Max Number', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional maximum number of checkboxes that the user can select for this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-field-maxchecks" name="">
					</div>
				</div><!-- .pewc-checkbox-group-wrapper -->
		<?php } ?>

			<?php if( pewc_is_pro() ) { ?>
				<div class="pewc-fields-wrapper pewc-products-extras">
					<div class="product-extra-field-full">
						<div class="product-extra-field-third">
							<label>
								<?php _e( 'Child Products', 'pewc' ); ?>
								<?php echo wc_help_tip( 'Select which products you\'d like to associate with this product', 'pewc' ); ?>
							</label>
						</div>
						<div class="product-extra-field-two-thirds-right">
							<select class="wc-product-search pewc-field-child_products pewc-data-options" multiple="multiple" style="width: 100%;" name="" data-action="woocommerce_json_search_products" data-include="" data-exclude="">
							</select>
						</div>
						<?php printf( '<p>%s</p>', __( 'You need to save or publish this product before this field becomes available', 'pewc' ) ); ?>
					</div>
				</div>
			<?php } ?>

			<?php if( pewc_is_pro() ) { ?>
				<div class="pewc-fields-wrapper pewc-products-extras products-layout-checkboxes">
					<div class="pewc-products-layout product-extra-field-third">
						<label>
							<?php _e( 'Products Layout', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose how child products will be displayed.', 'pewc' ); ?>
						</label>
						<select class="pewc-field-products_layout" name="">
							<option value="checkboxes"><?php _e( 'Checkboxes', 'pewc' ); ?></option>
							<option value="column"><?php _e( 'Column', 'pewc' ); ?></option>
							<option value="radio"><?php _e( 'Radio', 'pewc' ); ?></option>
							<option value="select"><?php _e( 'Select', 'pewc' ); ?></option>
						</select>
					</div>
					<div class="pewc-products-quantities product-extra-field-third">
						<label>
							<?php _e( 'Products Quantities', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Choose whether to link the quantities of the parent product and child product so that they are always the same, or to limit the quantity of the child product to one only.', 'pewc' ); ?>
						</label>
						<select class="pewc-field-products_quantities" name="">
							<option value="independent"><?php _e( 'Independent', 'pewc' ); ?></option>
							<option value="linked"><?php _e( 'Linked', 'pewc' ); ?></option>
							<option value="one-only"><?php _e( 'One only', 'pewc' ); ?></option>
						</select>
					</div>
					<div class="pewc-products-select-placeholder product-extra-field-third">
						<label>
							<?php _e( 'Select Field Placeholder', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enter instructional text in here to appear as the first option in the select field.', 'pewc' ); ?>
						</label>
						<input type="type" class="pewc-field-select_placeholder" name="" value="">
					</div>
					<div class="pewc-allow-none product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Child Product Not Required', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Select this option if the child product is not required. Select this option if the child product is not required. Note that if you choose the checkboxes layout, child products are automatically set to not required.', 'pewc' ); ?>
						</label>
						<input type="checkbox" class="pewc-checkbox-block pewc-field-allow_none" name="" value="1" checked disabled>
					</div>
				</div>
			<?php } ?>

			<div class="pewc-fields-wrapper pewc-radio-image-extras">
				<div class="product-extra-field-third">
					<input type="number" class="pewc-number-columns" name="" value="3" min="1" max="10" step="1">
					<label>
						<?php _e( 'Number Columns', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Choose how many columns to display your images in', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-third">
					<input type="checkbox" class="pewc-hide-labels" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Hide Labels?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to just display the images with no text', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-third pewc-allow-multiple-wrapper">
					<input type="checkbox" class="pewc-allow-multiple" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Allow multiple?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to allow multiple selections (checkbox instead of radio)', 'pewc' ); ?>
					</label>
				</div>
			</div>

			<?php if( pewc_is_pro() ) {
				// Min, max and discount fields for Products field ?>
				<div class="pewc-fields-wrapper pewc-child-product-min-max-extras">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Min Child Products', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Specify a minimum number of products the user must choose from this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-min-child-products" name="" value="" min="0" max="" step="1">
					</div>
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Max Child Products', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Specify a maximum number of products the user must choose from this field', 'pewc' ); ?>
						</label>
						<input type="number" class="pewc-max-child-products" name="" value="" min="0" max="" step="1">
					</div>

				</div>
			<?php } ?>

			<div class="pewc-fields-wrapper pewc-misc-fields">
				<div class="product-extra-field-third pewc-required">
					<input type="checkbox" class="pewc-field-required" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Required Field?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to require this field', 'pewc' ); ?>
					</label>
				</div>
				<div class="pewc-flatrate product-extra-field-third">
					<input type="checkbox" class="pewc-field-flatrate" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Flat Rate?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option if you only want to charge for this field once, irrespective of how many times it\'s added to the cart', 'pewc' ); ?>
					</label>
				</div>
				<?php do_action( 'pewc_end_checkbox_row_new_item' ); ?>
				<?php if( pewc_is_pro() ) { ?>
					<div class="pewc-percentage product-extra-field-third">
						<input type="checkbox" class="pewc-field-percentage" name="" value="1">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Percentage?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this option for the field price to be set as a percentage of the product price', 'pewc' ); ?>
						</label>
					</div>
				<?php } ?>
			</div>

			<div class="pewc-fields-wrapper pewc-upload-fields">

				<div class="product-extra-field-third">
					<input type="checkbox" class="pewc-field-item pewc-multiple-uploads" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Allow multiple uploads?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to allow the user to upload multiple files', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-third pewc-ajax-upload-only">
					<input type="checkbox" class="pewc-field-item pewc-multiply-price" name="" value="1">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Price per upload?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enable this option to multiply the field price by the number of uploaded files', 'pewc' ); ?>
					</label>
				</div>
				<div class="product-extra-field-third pewc-ajax-upload-only">
					<label>
						<?php _e( 'Max Files', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Maximum number of files if multiple files uploads are enabled', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-item pewc-field-max-files" name="" min="1" value="1">
				</div>

			</div><!-- .pewc-checkbox-group-wrapper -->

			<div class="pewc-fields-wrapper pewc-char-fields">
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Min Chars', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional minimum number of characters for this field', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-minchars" name="" value="">
				</div>
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Max Chars', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional maximum number of characters for this field', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-maxchars" name="" value="">
				</div>
				<div class="product-extra-field-third">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Price Per Character?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Select this if you want to charge per character', 'pewc' ); ?>
					</label>
					<input type="checkbox" class="pewc-checkbox-block pewc-field-per-character" name="" value="1">
				</div>
			</div>
			<?php if( pewc_is_pro() ) { ?>
				<div class="pewc-fields-wrapper pewc-extrachar-fields">
					<div class="product-extra-field-third">
						<label>
							<?php _e( 'Free Chars', 'pewc' ); ?>
							<?php echo wc_help_tip( 'An optional number of free characters to allow before pricing per character kicks in', 'pewc' ); ?>
						</label>
						<input type="number" min="0" class="pewc-field-freechars" name="" value="">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Only Allow Alphanumeric?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this to allow only alphanumeric characters in this field', 'pewc' ); ?>
						</label>
						<input type="checkbox" class="pewc-checkbox-block pewc-field-alphanumeric" name="" value="1">
					</div>
					<div class="product-extra-field-third">
						<label class="pewc-checkbox-field-label">
							<?php _e( 'Only Charge Alphanumeric?', 'pewc' ); ?>
							<?php echo wc_help_tip( 'Enable this to charge just the alphanumeric characters in the field', 'pewc' ); ?>
						</label>
						<input type="checkbox" class="pewc-checkbox-block pewc-field-alphanumeric-charge" name="" value="1">
					</div>
				</div><!-- .pewc-fields-wrapper -->
			<?php } ?>
			<div class="pewc-fields-wrapper pewc-num-fields">
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Min Value', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional minimum value for the field', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-minval" name="" value="">
				</div>
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Max Value', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional maximum value for the field', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-maxval" name="" value="">
				</div>
				<div class="product-extra-field-third">
					<label class="pewc-checkbox-field-label">
						<?php _e( 'Multiply Price?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Select this to multiply the value of the field by its price', 'pewc' ); ?>
					</label>
					<input type="checkbox" class="pewc-checkbox-block pewc-field-multiply" name="" value="1">
				</div>
			</div>

			<div class="pewc-fields-wrapper pewc-date-fields">
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Min date today?', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Select this to prevent entering a date in the past', 'pewc' ); ?>
					</label>
					<input type="checkbox" class="pewc-field-min_date_today" name="" value="1">
				</div>
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Min date', 'pewc' ); ?>
						<?php echo wc_help_tip( 'The earliest allowable date', 'pewc' ); ?>
					</label>
					<input type="text" class="pewc-date-field pewc-field-mindate" name="" value="">
				</div>
				<div class="product-extra-field-third">
					<label>
						<?php _e( 'Max date', 'pewc' ); ?>
						<?php echo wc_help_tip( 'The latest allowable date', 'pewc' ); ?>
					</label>
					<input type="text" class="pewc-date-field pewc-field-maxdate" name="" value="">
				</div>
			</div><!-- .pewc-fields-wrapper -->

            <div class="pewc-fields-wrapper pewc-color-picker-fields">
                <div class="product-extra-field-third">
                    <label>
                        <?php _e( 'Default color', 'pewc' ); ?>
                        <?php echo wc_help_tip( 'Optionally select a default color for this field', 'pewc' ); ?>
                    </label>
                    <input type="text" class="pewc-field-color" name="" value="">
                </div>
                <div class="product-extra-field-third">
                    <label>
                        <?php _e( 'Element width', 'pewc' ); ?>
                        <?php echo wc_help_tip( 'Optionally chose a different width for the color-picker dropdown (px)', 'pewc' ); ?>
                    </label>
                    <input type="text" class="pewc-field-width" name="" value="">
                </div>
            </div><!-- .pewc-fields-wrapper -->
            <div class="pewc-fields-wrapper pewc-color-picker-fields">
                <div class="pewc-show product-extra-field-third">
                    <input type="checkbox" class="pewc-field-show" name="" value="1">
                    <label class="pewc-checkbox-field-label">
                        <?php _e( 'Show by default?', 'pewc' ); ?>
                        <?php echo wc_help_tip( 'Enable this option if you want to show the color-picker dropdown by default.', 'pewc' ); ?>
                    </label>
                </div>
                <div class="pewc-palettes product-extra-field-third">
                    <input type="checkbox" class="pewc-field-palettes" name="" value="1">
                    <label class="pewc-checkbox-field-label">
                        <?php _e( 'Display common palettes?', 'pewc' ); ?>
                        <?php echo wc_help_tip( 'Enable this option to display a row of common palette colors. This is particularly useful in situations where the currently selected color seems to make no colors available.', 'pewc' ); ?>
                    </label>
                </div>
            </div><!-- .pewc-fields-wrapper -->

						<?php do_action( 'pewc_new_field_item_extra_fields' ); /* DWS */ ?>

			<div class="pewc-fields-wrapper pewc-default-fields">
				<div class="pewc-default product-extra-field-third">
					<input type="checkbox" class="pewc-field-default pewc-field-default-field-checkbox" name="" value="1">
					<label class="pewc-checkbox-field-label pewc-field-default-field-checkbox">
						<?php _e( 'Default', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Check this to enable this checkbox field by default', 'pewc' ); ?>
					</label>
					<label class="pewc-checkbox-field-label pewc-field-default-field-number">
						<?php _e( 'Default', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enter a default value', 'pewc' ); ?>
					</label>
					<input type="number" class="pewc-field-default pewc-field-default-field-number" name="" value="">

					<label class="pewc-checkbox-field-label pewc-field-default-field-text">
						<?php _e( 'Default', 'pewc' ); ?>
						<?php echo wc_help_tip( 'Enter a default value', 'pewc' ); ?>
					</label>
					<input type="text" class="pewc-field-default pewc-field-default-field-text" name="" value="">
					<input type="hidden" class="pewc-field-default pewc-field-default-hidden" name="" value="">
				</div>
			</div><!-- .pewc-fields-wrapper -->

			<div class="pewc-fields-wrapper pewc-desc-image-wrapper">
				<?php $src = wc_placeholder_img_src(); ?>
				<div class="product-extra-field-third pewc-field-image">
					<label>
						<?php _e( 'Field Image', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional image to accompany the field', 'pewc' ); ?>
					</label>
					<div class='image-preview-wrapper'>
						<a href="#" class="pewc-upload-button" data-item-id="">
							<img src="<?php echo esc_url( $src ); ?>" width="100" height="100" style="max-height: 100px; width: 100px;">
						</a>
					</div>
					<input type="hidden" name="" class="pewc-image-attachment-id" value="">
				</div>
				<div class="product-extra-field-two-thirds-right pewc-description">
					<label>
						<?php _e( 'Field Description', 'pewc' ); ?>
						<?php echo wc_help_tip( 'An optional description for the field', 'pewc' ); ?>
					</label>
					<textarea class="pewc-field-description" name=""></textarea>
				</div>
			</div>

			<div class="pewc-fields-wrapper pewc-fields-conditionals">
				<label><?php _e( 'Conditions', 'pewc' ); ?></label>
				<?php include( PEWC_DIRNAME . '/templates/admin/new-condition.php' ); ?>
			</div><!-- .pewc-fields-wrapper -->

		</div>

	</div><!-- .product-extra-field -->
</li>
