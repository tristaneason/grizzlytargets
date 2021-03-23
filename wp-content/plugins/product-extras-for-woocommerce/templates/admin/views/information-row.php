<?php
/**
 * The markup for an information row
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}
if( ! isset( $group_id ) ) {
	$name = '';
} else {
	$name_image = '_product_extra_groups_' . $group_id . '_' . $item_key . '[field_rows][' . $row_count . '][image]';
	$name_label = '_product_extra_groups_' . $group_id . '_' . $item_key . '[field_rows][' . $row_count . '][label]';
	$name_data = '_product_extra_groups_' . $group_id . '_' . $item_key . '[field_rows][' . $row_count . '][data]';
}  ?>


<div class="product-extra-row-wrapper" data-row-count="<?php echo esc_attr( $row_count ); ?>">

	<div class="pewc-row-image">
		<?php $image_wrapper_classes = array(
			'pewc-field-image-' . $item_key . '_' . $row_count
		);
		$remove_class = '';
		$field_image = '';
		$src = trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/placeholder-small.png';
		$placeholder = trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/placeholder-small.png';
		$row_image = '';
		if( ! empty( $item['field_rows'][$row_count]['image'] ) ) {
			$row_image = $item['field_rows'][$row_count]['image'];
			$src = wp_get_attachment_image_src( $row_image );
			$src = $src[0];
			$image_wrapper_classes[] = 'has-image';
			$remove_class = 'remove-image';
		} ?>

		<div class="pewc-field-image <?php echo join( ' ', $image_wrapper_classes ); ?>">
			<div class='image-preview-wrapper'>
				<a href="#" class="pewc-upload-button pewc-upload-row-image <?php echo esc_attr( $remove_class ); ?>" data-item-id="<?php echo esc_attr( $item_key . '_' . $row_count ); ?>">
					<img data-placeholder="<?php echo $placeholder; ?>" src="<?php echo esc_url( $src ); ?>" style="height: 30px; width: 30px;">
				</a>
			</div>
			<input type="hidden" name="<?php echo $name_image; ?>" class="pewc-image-attachment-id" value="<?php echo esc_attr( $row_image ); ?>">
		</div>

	</div>

	<div class="product-extra-field-quarter">
		<?php $label = ( isset( $key ) && isset( $item['field_rows'][esc_attr( $key )]['label'] ) ) ? $item['field_rows'][esc_attr( $key )]['label'] : ''; ?>
		<input type="text" class="pewc-field-row-label" name="<?php echo $name_label; ?>" value="<?php echo esc_attr( $label ); ?>">
	</div>
	<div class="product-extra-field-half">
		<?php $data = ( isset( $key ) && isset( $item['field_rows'][esc_attr( $key )]['data'] ) ) ? $item['field_rows'][esc_attr( $key )]['data'] : ''; ?>
		<input type="text" class="pewc-field-row-data" name="<?php echo $name_data; ?>" value="<?php echo esc_attr( $data ); ?>">
	</div>
	<div class="product-extra-field-10 pewc-actions pewc-select-actions">
		<span class="sort-option pewc-action"><span class="dashicons dashicons-menu"></span></span>
		<span class="remove-row pewc-action"><?php _e( 'Remove', 'pewc' ); ?></span>
	</div>

</div>
