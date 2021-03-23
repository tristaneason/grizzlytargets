<?php
/**
 * The option summary panel
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<div class="pewc-summary-panel-wrapper">';

echo '<table class="pewc-summary-panel-table">';
	echo '<thead>';

		printf(
			'<tr class="pewc-summary-panel-row pewc-summary-panel-header"><th class="pewc-summary-panel-label">%s</th><th class="pewc-summary-panel-price">%s</th></tr>',
			$product->get_title(),
			$product->get_price_html()
		);

	echo '</thead>';
	echo '<tbody>';
		// echo '<tr class="pewc-summary-panel-row pewc-summary-panel-row-new"><td></td><td></td><td></td></tr>';

		if( $summary_panel ) {

			foreach( $summary_panel as $group ) {

				echo '<tr class="pewc-summary-panel-row"><td class="pewc-summary-line-item" colspan=2>';

					echo '<table class="pewc-summary-sub-panel-table"><tbody>';

					if( ! empty( $group['title'] ) ) {

						printf(
							'<tr class="pewc-summary-panel-row pewc-summary-panel-group-title"><th colspan=2>%s</th></tr>',
							$group['title']
						);

					}

					if( ! empty( $group['fields'] ) ) {

						foreach( $group['fields'] as $field_id=>$field ) {

							$value = ! empty( $field['value'] ) ? $field['value'] : '';
							if( is_array( $value ) ) {
								$value = join( ' ', $value );
							}

							$row_class = array(
								'pewc-summary-panel-field-row'
							);
							if( ! $value ) {
								$row_class[] = 'pewc-summary-panel-field-row-inactive';
							}

							if( $field['price'] ) {
								$price = wc_price( $field['price'] );
							} else {
								$price = '';
							}
							if( is_array( $price ) ) {
								$price = '';
							}

							printf(
								'<tr id="pewc-summary-row-%s" class="%s"><td class="pewc-summary-panel-label"><span class="pewc-summary-panel-product-name">%s</span><span class="pewc-summary-panel-separator">%s</span><span class="pewc-summary-panel-product-value">%s</span></td><td class="pewc-summary-panel-price">%s</td></tr>',
								esc_attr( $field_id ),
								join( ' ', $row_class ),
								$field['label'],
								apply_filters( 'pewc_summary_panel_separator', ' - ', $field_id ),
								$value,
								$price
							);

						}

					}

					echo '</tbody></table>';

				echo '</td></tr>';

			}

		}

		// $summary_panel[$group_id]['fields'][$item['field_id']] = array(
		// 	'label' => $item['field_label'],
		// 	'value'	=> $value
		// );

	echo '</tbody>';

	echo '<tfoot>';

	printf(
		'<tr class="pewc-summary-panel-row pewc-summary-panel-subtotal"><th class="pewc-summary-panel-label">%s</th><th id="pewc-summary-panel-subtotal">%s</th></tr>',
		__ ( 'Subtotal', 'pewc' ),
		''
	);

	echo '</tfoot></table>';

	do_action( 'pewc_after_summary_panel_table', $product );

echo '</div><!-- .pewc-summary-panel-wrapper -->';
