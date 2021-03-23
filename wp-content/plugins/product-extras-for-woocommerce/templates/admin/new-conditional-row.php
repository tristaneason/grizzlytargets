<?php
/**
 * The markup for a new set of conditions
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="product-extra-conditional-row new-conditional-row">
	<div class="product-extra-field-third">
		<?php // $fields = pewc_get_all_fields( false, $groups );
		// if( ! empty( $fields ) ) { ?>
			<select class="pewc-condition-field pewc-condition-select" name="" id="" data-group-id="" data-item-id="" data-condition-id="">
			<?php // foreach( $fields as $key=>$value ) {
				// echo '<option value="not-selected">' . __( ' -- Select a field -- ', 'pewc' ) . '</option>';
			// } ?>
			</select>
			<input type="hidden" class="pewc-hidden-field-type" name="" id="" value="">
		<?php // } ?>
	</div>
	<div class="product-extra-field-sixth">
		<select class="pewc-condition-rule pewc-condition-select" name="" id="" data-group-id="" data-item-id="" data-condition-id="">
			<?php $rules = pewc_get_rules();
			foreach( $rules as $key=>$value ) {
				echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			} ?>
		</select>
	</div>
	<div class="product-extra-field-half product-extra-field-last pewc-condition-value-field">
		<span class="remove-condition pewc-action"><?php _e( 'Remove', 'pewc' ); ?></span>
	</div>
</div><!-- .new-conditional-row -->

<div class="new-condition-value-field">
	<input class="pewc-condition-value pewc-input-text" type="text" name="" id="" data-group-id="" data-item-id="" data-condition-id="" value="">
	<input class="pewc-condition-value pewc-input-number" type="number" name="" id="" data-group-id="" data-item-id="" data-condition-id="" value="">
	<select class="pewc-condition-value pewc-value-select" name="" id="" data-group-id="" data-item-id="" data-condition-id=""></select>
	<input class="pewc-condition-value pewc-value-checkbox" type="hidden" name="" id="" data-group-id="" data-item-id="" data-condition-id="">
	<span class="pewc-checked-placeholder"><?php _e( 'Checked', 'pewc' ); ?></span>
</div>
