<?php
/**
 * The markup for an information row
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>


<div class="product-extra-row-wrapper" data-row-count="<?php echo esc_attr( $row_count ); ?>">

	<div class="pewc-row-image">
		<div class="pewc-field-image">
			<div class='image-preview-wrapper'>
				<a href="#" class="pewc-upload-button pewc-upload-option-image" data-item-id="">
					<?php	$placeholder = trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/placeholder-small.png'; ?>
					<img data-placeholder="<?php echo $placeholder; ?>" src="<?php echo esc_url( $placeholder ); ?>" style="height: 30px; width: 30px;">
				</a>
			</div>
			<input type="hidden" name="" class="pewc-image-attachment-id" value="">
		</div>

	</div>

	<div class="product-extra-field-quarter">
		<input type="text" class="pewc-field-row-label" name="" value="">
	</div>
	<div class="product-extra-field-half">
		<input type="text" class="pewc-field-row-data" name="" value="">
	</div>
	<div class="product-extra-field-10 pewc-actions pewc-select-actions">
		<span class="sort-row pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-row pewc-action"><?php _e( 'Remove', 'pewc' ); ?></span>
	</div>

</div>
