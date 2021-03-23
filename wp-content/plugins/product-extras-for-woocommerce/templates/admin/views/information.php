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

<div class="pewc-fields-wrapper pewc-information-fields">

	<div>
		<div class="pewc-row-image">&nbsp;</div>
		<div class="product-extra-field-quarter">
			<?php printf( '<div class="pewc-label">%s</div>', __( 'Label', 'pewc' ) ); ?>
		</div>
		<div class="product-extra-field-half">
			<?php printf( '<div class="pewc-label">%s</div>', __( 'Data', 'pewc' ) ); ?>
		</div>
		<div class="product-extra-field-10 pewc-actions pewc-select-actions">&nbsp;</div>

	</div>

	<div class="pewc-field-information-wrapper pewc-data-information">
		<?php $row_count = 0;
		if( ! empty( $item['field_rows'] ) ) {
			foreach( $item['field_rows'] as $key=>$value ) {
				include( PEWC_DIRNAME . '/templates/admin/views/information-row.php' );
				$row_count++;
			}

		} ?>
	</div>

	<p><a href="#" class="button add_new_row"><?php _e( 'Add Row', 'pewc' ); ?></a></p>

</div><!-- .pewc-fields-wrapper -->
