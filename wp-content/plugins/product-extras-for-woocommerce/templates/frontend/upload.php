<?php
/**
 * A text field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

// echo pewc_field_label( $item, $id );

$can_upload = pewc_can_upload();

if( ! $can_upload ) {

	echo $open_td;
	printf(
		'<p>%s</p>',
		apply_filters( 'pewc_filter_not_permitted_message', __( 'You need to be logged in to upload files', 'pewc' ) )
	);
	do_action( 'pewc_after_not_permitted_message' );
	echo $close_td;

} else {

	echo $open_td;
	$allow_multiples = ! empty( $item['multiple_uploads' ] ) ? 'multiple' : '';
	$multiply_price = ! empty( $item['multiply_price' ] ) ? '1' : '0';
	$allow_multiples = apply_filters( 'pewc_allow_multiple_file_upload', $allow_multiples, $post_id, $id );

	if( pewc_enable_ajax_upload() == 'yes' ) { ?>

		<div class="dropzone" id="dz_<?php echo esc_attr( $id ); ?>"></div>
		<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>[]" value="<?php echo esc_attr( $id ); ?>">
		<input type="hidden" class="pewc_file_data" name="pewc_file_data[<?php echo $item['field_id']; ?>]" id="<?php echo esc_attr( $id ); ?>_file_data" value="">
		<input type="hidden" class="pewc-form-field pewc-number-uploads" name="<?php echo esc_attr( $id ); ?>_number_uploads" id="<?php echo esc_attr( $id ); ?>_number_uploads" value="">
		<input type="hidden" name="<?php echo esc_attr( $id ); ?>_multiply_price" id="<?php echo esc_attr( $id ); ?>_multiply_price" value="<?php echo esc_attr( $multiply_price ); ?>">
		<input type="hidden" name="<?php echo esc_attr( $id ); ?>_base_price" id="<?php echo esc_attr( $id ); ?>_base_price" value="<?php echo esc_attr( $field_price ); ?>">

		<?php do_action( 'pewc_do_ajax_upload_script', $id, $item, $multiply_price ); ?>

	<?php } else { ?>

		<div class="pewc-input-wrapper" id="<?php echo esc_attr( $id ); ?>-wrapper">
			<div class="pewc-placeholder" id="<?php echo esc_attr( $id ); ?>-placeholder">
				<img src="#">
				<small><a href="#" class="pewc-remove-image" data-id="<?php echo esc_attr( $id ); ?>"><?php _e( 'Remove', 'pewc' ); ?></a></small>
			</div>
			<div>
				<input class="pewc-form-field pewc-file-upload" type="file" <?php echo $allow_multiples; ?> id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>[]">
			</div>
		</div>

	<?php } ?>

<?php echo $close_td;

}
