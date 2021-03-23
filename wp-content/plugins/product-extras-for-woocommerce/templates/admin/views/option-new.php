<?php
/**
 * The markup for an option
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<tr class="product-extra-option-wrapper" data-option-count="">

	<td class="pewc-option-image">

		<?php	$placeholder = trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/placeholder-small.png'; ?>
		<div class="pewc-field-image">
			<div class='image-preview-wrapper'>
				<a href="#" class="pewc-upload-button pewc-upload-option-image" data-item-id="">
					<img data-placeholder="<?php echo $placeholder; ?>" src="<?php echo esc_url( $placeholder ); ?>" style="height: 30px; width: 30px;">
				</a>
			</div>
			<input type="hidden" name="" class="pewc-image-attachment-id" value="">
		</div>

	</td>

	<td class="pewc-option-option">
		<input type="text" class="pewc-field-option-value" name="" value="">
	</td>

	<td class="pewc-option-price">
		<input type="number" class="pewc-field-option-price" name="" value="" step="<?php echo apply_filters( 'pewc_field_item_price_step', '0.01', false ); ?>">
	</td>

	<?php do_action( 'pewc_after_option_params', 'OPTION_KEY', 'GROUP_ID', 'ITEM_KEY', array(), '' ); ?>

	<td class="product-extra-field-10 pewc-actions pewc-select-actions">
		<span class="sort-option pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-option pewc-action"><?php _e( 'Remove', 'pewc' ); ?></span>
	</td>

</tr>
