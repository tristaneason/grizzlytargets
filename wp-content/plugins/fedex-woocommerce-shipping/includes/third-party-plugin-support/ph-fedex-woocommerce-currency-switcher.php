<?php

if( ! function_exists('ph_fedex_get_currency_conversion_rate') ) {
	function ph_fedex_get_currency_conversion_rate($conversion_rate,$fedex_currency) {
		$wc_store_currency		= get_woocommerce_currency();
		$woocommerce_currency_conversion_rate=get_option('woocommerce_multicurrency_rates');

		if( $fedex_currency!=$wc_store_currency && !empty( $woocommerce_currency_conversion_rate ) && isset( $woocommerce_currency_conversion_rate[ $wc_store_currency ]) )
		{
			$store_currency_conversion_rate = $woocommerce_currency_conversion_rate[ $wc_store_currency ];
			$fedex_currency_conversion_rate = $woocommerce_currency_conversion_rate[ $fedex_currency ];
			$conversion_rate = $fedex_currency_conversion_rate / $store_currency_conversion_rate;
		}
		return $conversion_rate;
	}

}
add_filter( 'ph_fedex_currency_conversion_rate', 'ph_fedex_get_currency_conversion_rate' ,10,2);
add_filter( 'ph_fedex_currency_conversion_rate_from_fedex_currency', 'ph_fedex_get_currency_conversion_rate' ,10,2);