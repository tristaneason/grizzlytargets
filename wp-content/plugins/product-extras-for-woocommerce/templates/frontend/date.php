<?php
/**
 * A date field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

printf(
	'%s<input type="text" autocomplete="off" readonly class="pewc-form-field pewc-date-field pewc-date-field-%s" id="%s" name="%s" value="%s">%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $item['field_id'] ),
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $value ),
	$close_td
);

$params = pewc_get_date_field_params( $item ); ?>

<script>
	jQuery( document ).ready( function($) {
		$( 'body' ).on( 'focus', '.pewc-date-field-<?php echo esc_attr( $item['field_id'] ); ?>', function() {
			var params = $( this ).attr( 'data-params' );
	    $( this ).datepicker(
				<?php
				if( $params ) {
					printf(
						'{ %s }',
						$params
					);
				} ?>
			);
		});
	});
</script>
