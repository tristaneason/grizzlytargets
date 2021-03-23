<?php
/**
 * The markup for a field item in the admin
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<table id="pewc_option_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>" class="pewc-fields-wrapper pewc-option-fields">

	<thead>

		<tr>

			<th class="pewc-option-image">&nbsp;</th>
			<th class="pewc-option-option">
				<?php printf( '<div class="pewc-label">%s</div>', __( 'Option', 'pewc' ) ); ?>
			</th>
			<th class="pewc-option-price">
				<?php printf( '<div class="pewc-label">%s</div>', __( 'Price', 'pewc' ) ); ?>
			</th>


			<?php do_action( 'pewc_after_option_params_titles', $group_id, $item_key, $item ); ?>

			<th class="product-extra-field-10 pewc-actions pewc-select-actions">&nbsp;</th>

		</tr>

	</thead>

	<?php // Add option data to wrapper
	$option_count = 0;
	$data = array();
	if( ! empty( $item['field_options'] ) ) {
		foreach( $item['field_options'] as $key=>$value ) {
			// Escaped this 2.4.5
			$data[] = isset( $value['value'] ) ? $value['value'] : '';
		}
	}
	$data = json_encode( $data ); ?>

	<tbody class="pewc-field-options-wrapper pewc-data-options" data-options='<?php echo esc_attr( $data ); ?>'>

		<?php $option_count = 0;
		if( ! empty( $item['field_options'] ) ) {
			foreach( $item['field_options'] as $key=>$value ) {
				include( PEWC_DIRNAME . '/templates/admin/views/option.php' );
				$option_count++;
			}
		} ?>

	</tbody>

	<tfoot>

		<tr>
			<td colspan="3"><a href="#" class="button add_new_option"><?php _e( 'Add Option', 'pewc' ); ?></a></td>
		</tr>

		<tr>
			<td colspan="3" class="pewc-select-field-only">
				<?php $checked = ! empty( $item['first_field_empty'] ); ?>
				<input <?php checked( $checked, 1, true ); ?> type="checkbox" class="pewc-field-item pewc-first-field-empty" name="_product_extra_groups_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $item_key ); ?>[first_field_empty]" value="1">
				<label class="pewc-checkbox-field-label">
					<?php _e( 'First field is instruction only', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this if your first option is an instruction to the user, e.g. "Pick an item"', 'pewc' ); ?>
				</label>
			</td>
		</tr>

	</tfoot>

</table><!-- .pewc-fields-wrapper -->
