<?php
/**
 * Functions for tooltips
 * @since 3.5.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


function pewc_enable_tooltips() {
	$enable_tooltips = get_option( 'pewc_enable_tooltips', 'no' );
	return apply_filters( 'pewc_enable_tooltips', $enable_tooltips );
}

// Tooltips
function pewc_add_tooltip_icon( $label, $product, $item ) {

	if( pewc_enable_tooltips() == 'yes' ) {

		$field_description = ! empty( $item['field_description'] ) ? $item['field_description'] : '';
		if( ! empty( $item['field_description'] ) ) {

			$label .= sprintf(
				'&nbsp;<span title="%s" class="dashicons dashicons-editor-help tooltip"></span>',
				esc_attr( $item['field_description'] )
			);

		}

	}

	return $label;

}
add_filter( 'pewc_field_label_end', 'pewc_add_tooltip_icon', 10, 3 );
