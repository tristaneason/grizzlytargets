<?php

/**
 * Adds a currency selector to the feed setup page
 *
 * @since 2.28.0
 */
function add_woocs_currency_selector_to_feed_form() {
	global $WOOCS;

	$currencies = $WOOCS->get_currencies();

	?>
	<tr class="wppfm-main-feed-input-row">
		<th id="wppfm-main-feed-input-label"><label for="wppfm-feed-currency-selector">Feed Currency</label> :</th>
		<td>
			<select class="wppfm-main-input-selector" id="wppfm-feed-currency-selector">
				<option value="0"><?php _e('-- Select the WOOCS currency for the feed --', 'wppfm_woocs'); ?></option>
				<?php foreach ( $currencies as $currency ) : ?>
					<option value="<?php echo $currency['name']; ?>"><?php echo $currency['name']; ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<?php
}

add_action('wppfm_add_feed_attribute_selector', 'add_woocs_currency_selector_to_feed_form');

/**
 * Converts a money value to a selected currency.
 *
 * @param float $value
 * @param string $selected_currency
 *
 * @return    float
 * @since 2.28.0
 *
 */
function convert_to_woocs_money_value( $value, $selected_currency ) {
	if ( ! $value || ! $selected_currency )
	{
		return $value;
	}

	global $WOOCS;

	$currencies_data = $WOOCS->get_currencies();

	if ( ! array_key_exists( $selected_currency, $currencies_data ) ) {
		return $value;
	}

	$selected_currency_rate            = array_key_exists( 'rate', $currencies_data[$selected_currency] ) ? $currencies_data[$selected_currency]['rate'] : 1;
	$selected_currency_number_decimals = array_key_exists( 'decimals', $currencies_data[$selected_currency] ) ? $currencies_data[$selected_currency]['decimals'] : 2;

	return number_format($selected_currency_rate * $value, $selected_currency_number_decimals, '.', '');
}

add_filter('wppfm_woocs_exchange_money_values', 'convert_to_woocs_money_value', 10, 2);

/**
 * Returns the currency symbol of the selected currency.
 *
 * @param string $selected_currency
 *
 * @return mixed|string
 * @since 2.28.0
 */
function get_woocs_currency_symbol( $selected_currency ) {
	if ( ! $selected_currency )
	{
		return get_woocommerce_currency();
	}

	global $WOOCS;

	$currencies_data = $WOOCS->get_currencies();

	if ( ! array_key_exists( $selected_currency, $currencies_data ) || ! array_key_exists( 'name', $currencies_data[$selected_currency] ) ) {
		return get_woocommerce_currency();
	}

	return $currencies_data[$selected_currency]['name'];
}

add_filter('wppfm_get_woocs_currency', 'get_woocs_currency_symbol', 10, 1);

/**
 * Adds the selected currency as a parameter to the products' permalink.
 *
 * @param string $permalink
 * @param string $selected_currency
 *
 * @return mixed|string
 * @since 2.29.0
 */
function make_woocs_product_permalink( $permalink, $selected_currency ) {
	if ( ! $selected_currency )
	{
		return $permalink;
	}

	return add_query_arg( 'currency', $selected_currency, $permalink );
}

add_filter('wppfm_woocs_product_permalink', 'make_woocs_product_permalink', 10, 2);
