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

if( ! pewc_get_enabled_roles() ) {
	return;
}

$roles = pewc_get_enabled_roles();
if( ! $roles ) {
	return;
}

$role_price_field_classes = array( 'pewc-field-item', 'pewc-field-role-price' );
if( ! isset( $group_id ) ) {
	$role_price_field_classes[] = 'pewc-field-role-price-new';
} ?>

<div class="pewc-fields-wrapper pewc-role-based-pricing">

	<?php foreach( $roles as $role ) { ?>

		<div class="product-extra-field-third pewc-field-price-wrapper">

			<?php $field_key = 'field_price_'  . $role;

			if( isset( $group_id ) ) {

				$role_price_field_name = '_product_extra_groups_' . esc_attr( $group_id ) . '_' . esc_attr( $item_key ) . '[' . $field_key . ']';

			} else {

				// This must be a new field
				$role_price_field_name = '';
				$field_price = '';

			}

			$field_price = isset( $item['field_price_' . $role] ) ? $item[$field_key] : ''; ?>
			<label>
				<?php printf(
					'%s (%s)',
					__( 'Field Price', 'pewc' ),
					ucfirst( $role )
					); ?>
				<?php echo wc_help_tip( 'Enter the amount that will be added to the price if the user enters a value for this field', 'pewc' ); ?>
			</label>
			<input type="number" class="<?php echo join( ' ', $role_price_field_classes ); ?>" data-role="<?php echo esc_attr( $role ); ?>" name="<?php echo esc_attr( $role_price_field_name ); ?>" value="<?php echo esc_attr( $field_price ); ?>" step="<?php echo apply_filters( 'pewc_field_item_price_step', '0.01', false ); ?>">

		</div>

	<?php } ?>


</div><!-- .pewc-fields-wrapper -->
