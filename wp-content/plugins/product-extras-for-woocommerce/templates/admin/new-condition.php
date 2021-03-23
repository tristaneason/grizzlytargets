<?php
/**
 * The markup for a conditional row, i.e. one condition
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php $style = 'style="display: none;"';
if( ! empty( $item['condition_field'] ) ) {
	$style = 'style="display: block;"';
} ?>
<div class="product-extra-conditional-row product-extra-action-match-row" <?php echo $style; ?>>

	<div class="product-extra-field-half">
		<?php $actions = pewc_get_actions();
		$action = '';
		if( ! empty( $actions ) ) { ?>
			<select class="pewc-condition-action" name="">
			<?php foreach( $actions as $key=>$value ) {
				$selected = selected( $action, $key, false );
				echo '<option ' . $selected . ' value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			} ?>
			</select>
		<?php } ?>
	</div>

	<div class="product-extra-field-half">
		<?php $matches = pewc_get_matches();
		$match = '';
		if( ! empty( $matches ) ) { ?>
			<select class="pewc-condition-condition" name="">
			<?php foreach( $matches as $key=>$value ) {
				echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
			} ?>
			</select>
		<?php } ?>
	</div>

</div><!-- .product-extra-conditional-row -->

<p><a href="#" class="button add_new_condition"><?php _e( 'Add Condition', 'pewc' ); ?></a></p>
