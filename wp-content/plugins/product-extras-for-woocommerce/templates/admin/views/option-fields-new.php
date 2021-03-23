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

<table id="" class="pewc-fields-wrapper pewc-option-fields">

	<thead>

		<tr>

			<th class="pewc-option-image">&nbsp;</th>
			<th class="pewc-option-option">
				<?php printf( '<div class="pewc-label">%s</div>', __( 'Option', 'pewc' ) ); ?>
			</th>
			<th class="pewc-option-price">
				<?php printf( '<div class="pewc-label">%s</div>', __( 'Price', 'pewc' ) ); ?>
			</th>

			<?php do_action( 'pewc_after_option_params_titles', false, false, array() ); ?>

			<th class="product-extra-field-10 pewc-actions pewc-select-actions">&nbsp;</th>

		</tr>

	</thead>

	<tbody class="pewc-field-options-wrapper pewc-data-options" data-options='[]'>
	</tbody>

	<tfoot>

		<tr>
			<td colspan="3"><a href="#" class="button add_new_option"><?php _e( 'Add Option', 'pewc' ); ?></a></td>
		</tr>

		<tr>
			<td colspan="3" class="pewc-select-field-only">
				<input type="checkbox" class="pewc-field-item pewc-first-field-empty" name="]" value="1">
				<label class="pewc-checkbox-field-label">
					<?php _e( 'First field is instruction only', 'pewc' ); ?>
					<?php echo wc_help_tip( 'Select this if your first option is an instruction to the user, e.g. "Pick an item"', 'pewc' ); ?>
				</label>
			</td>
		</tr>

	</tfoot>

</table><!-- .pewc-fields-wrapper -->
