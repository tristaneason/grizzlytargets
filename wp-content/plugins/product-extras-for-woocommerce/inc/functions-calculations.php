<?php
/**
 * Functions for calculations
 * @since 3.5.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set our look up tables for calculation fields
 */
function pewc_calculation_look_up_tables() {

	$tables = apply_filters( 'pewc_calculation_look_up_tables', array() );
	$fields = apply_filters( 'pewc_calculation_look_up_fields', array() ); ?>

		<script>
		var pewc_look_up_tables = <?php echo json_encode( $tables ); ?>;
		var pewc_look_up_fields = <?php echo json_encode( $fields ); ?>;
		</script>

	<?php

}
add_action( 'wp_head', 'pewc_calculation_look_up_tables' );
