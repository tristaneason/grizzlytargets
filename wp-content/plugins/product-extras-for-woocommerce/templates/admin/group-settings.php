<?php
/**
 * The markup for options under groups
 *
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! pewc_is_pro() ) {
	return;
} ?>

<div class="options_group">

	<p class="form-field">
		<?php $args = array(
			'id'			=> 'pewc_display_groups',
			'class' 	=> 'pewc-display-groups',
			'label'		=> __( 'Display groups as', 'pewc' ),
			'options'	=> array(
				'standard'		=> __( 'Standard', 'pewc' ),
				'accordion'		=> __( 'Accordion', 'pewc' ),
				'lightbox'		=> __( 'Lightbox', 'pewc' ),
				'steps'				=> __( 'Steps', 'pewc' ),
				'tabs'				=> __( 'Tabs', 'pewc' ),
			)
		);
		woocommerce_wp_select( $args ); ?>
	</p>

</div>

<?php
if( ! apply_filters( 'pewc_enable_assign_duplicate_groups', false ) ) {
	return;
}

?>

<div class="options_group">

	<div class="pewc-group-options-wrap">
		<?php printf(
			'<h3 class="pewc-group-meta-heading">%s</h3>',
			__( 'Assign groups to other products', 'pewc' )
		); ?>
	</div>

	<p class="form-field">
		<?php printf(
			'<label>%s</label>',
			__( 'Assign to products', 'pewc' )
		); ?>
		<select class="wc-product-search" data-options="" multiple="multiple" style="width: 100%;" name="pewc_assign_to_products[]" id="pewc_assign_to_products" data-sortable="true" data-placeholder="<?php esc_attr_e( 'Choose the products', 'pewc' ); ?>" data-action="woocommerce_json_search_products" data-include="" data-exclude="">
		</select>
	</p>

	<?php $args = array(
		'id'			=> 'pewc_replace_existing_groups',
		'class' 	=> 'pewc-replace-existing-groups',
		'label'		=> __( 'Replace existing groups', 'pewc' )
	);
	woocommerce_wp_checkbox( $args ); ?>

	<p>
		<?php printf(
			'<a href="#" class="pewc_assign_groups_to_products button button-primary" id="pewc_assign_groups_to_products">%s</a>',
			__( 'Assign', 'pewc' )
		); ?>
	</p>

</div>
