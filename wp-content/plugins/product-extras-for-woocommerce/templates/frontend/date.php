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

// echo pewc_field_label( $item, $id );

$params = array();
if( ! empty( $item['min_date_today'] ) ) {
	$params[] = '"minDate" : 0';
} else if( ! empty( $item['field_mindate'] ) ) {
	$mindate = strtotime( $item['field_mindate'] );
	$year = date( 'Y', $mindate );
	$month = date( 'm', $mindate ) - 1;
	$day = date( 'd', $mindate );
	$params[] = '"minDate" : new Date( ' . $year .', ' . $month . ', ' . $day . ' )';
}
if( ! empty( $item['field_maxdate'] ) ) {
	$maxdate = strtotime( $item['field_maxdate'] );
	$year = date( 'Y', $maxdate );
	$month = date( 'm', $maxdate ) - 1;
	$day = date( 'd', $maxdate );
	$params[] = '"maxDate" : new Date( ' . $year .', ' . $month . ', ' . $day . ' )';
}
$params = apply_filters( 'pewc_filter_date_field_params', $params, $item );

printf(
	'%s<input type="text" autocomplete="off" readonly class="pewc-form-field pewc-date-field" id="%s" name="%s" value="%s">%s',
	$open_td, // Set in functions-single-product.php
	esc_attr( $id ),
	esc_attr( $id ),
	esc_attr( $value ),
	$close_td
); ?>

<script>
	jQuery(document).ready(function($){
		<?php if( $params ) {
			$params = join( ',', $params );
		} ?>
		$('#<?php echo esc_attr( $id ); ?>').datepicker(
			<?php if( $params ) {
				printf(
					'{ %s }',
					$params
				);
			} ?>
		);
	});
</script>
