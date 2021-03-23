<?php
/**
 * A calculation field template
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

$formula = isset( $item['formula'] ) ? $item['formula'] : false;
$formula_action = ! empty( $item['formula_action'] ) ? $item['formula_action'] : 'cost';
$formula_round = ! empty( $item['formula_round'] ) ? $item['formula_round'] : '';
$decimal_places = ! empty( $item['decimal_places'] ) ? $item['decimal_places'] : '0';

if( ! $formula ) {
	return;
}

$formula = str_replace( ' ', '', $formula );

// Parse the formula to find tags and fields being used
preg_match_all( "|{(.*)}|U", $formula, $all_tags, PREG_PATTERN_ORDER );
preg_match_all( "|{field_(.*)}|U", $formula, $all_fields, PREG_PATTERN_ORDER );

$fields = '';
if( ! empty( $all_fields[1]) ) {
	$fields = json_encode( $all_fields[1] );
}
$tags = '';
if( ! empty( $all_tags[1]) ) {
	$tags = json_encode( $all_tags[1] );
}
$calculation_classes = array( 'pewc-calculation-field-wrapper', 'pewc-calculation-price-wrapper' ); ?>

<<?php echo $cell_tag; ?> class="<?php echo join( ' ', $calculation_classes ); ?>">

	<input type="hidden" class="pewc-data-formula" value="<?php echo esc_attr( $formula ); ?>">
	<input type="hidden" class="pewc-data-fields" value="<?php echo esc_attr( $fields ); ?>">
	<input type="hidden" class="pewc-data-tag" value="<?php echo esc_attr( $tags ); ?>">
	<input type="hidden" class="pewc-action" value="<?php echo esc_attr( $formula_action ); ?>">
	<input type="hidden" class="pewc-formula-round" value="<?php echo esc_attr( $formula_round ); ?>">
	<input type="hidden" class="pewc-decimal-places" value="<?php echo esc_attr( $decimal_places ); ?>">

	<?php
	printf(
		'<span class="pewc-calculation-span" id="pewc-calculation-value"></span></span><input type="hidden" class="pewc-form-field pewc-calculation-value pewc-number-field pewc-number-field-%s" id="%s" name="%s" value="">',
		esc_attr( $item['field_id'] ),
		esc_attr( $id ),
		esc_attr( $id )
	); ?>
	
</<?php echo $cell_tag; ?>>
