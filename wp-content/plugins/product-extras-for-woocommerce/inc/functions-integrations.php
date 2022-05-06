<?php
/**
 * Functions for integrating with various plugins
 * @since 2.2.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filters the table row item data in the PIP plugin.
 *
 * @since 1.5.1
 * @param array $item_data The item data.
 * @param array $item WC_Order item meta.
 * @param WC_Product $product Product object.
 * @param int $order_id WC_Order ID.
 * @param string $document_type The document type.
 * @param \WC_PIP_Document $document The document object.
 */
function pewc_pip_document_table_row_item_data( $item_data, $item, $product, $order_id, $doc_type, $document ) {
	$data = $item->get_data();
	$meta_data = $data['meta_data'];
	foreach( $meta_data as $meta_item ) {
		if( $meta_item->key == 'product_extras' ) {
			$extras = $meta_item->value;
			if( isset( $extras['groups'] ) ) {
				foreach( $extras['groups'] as $group ) {
					foreach( $group as $item ) {
						$item_data['product'] .= $item['label'] . ': ' . $item['value'];
					}
				}
			}
		};
	}
	return $item_data;
}
add_filter( 'wc_pip_document_table_row_item_data', 'pewc_pip_document_table_row_item_data', 10, 6 );

function pewc_get_current_wpml_language() {
	if( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
		return false;
	}
	return ICL_LANGUAGE_CODE;
}

function pewc_get_default_wpml_language() {
	if( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
		return false;
	}
	global $sitepress;
	return $sitepress->get_default_language();
}

/**
 * Ensure image replacemeent works with different themes
 */
function pewc_product_img_wrap( $img_wrap ) {
	if( wp_get_theme()->template == 'porto' ) {
		$img_wrap = '.owl-item.active';
	}
	return $img_wrap;
}
add_filter( 'pewc_product_img_wrap', 'pewc_product_img_wrap' );

/**
 * Add some data to the cart item for Aelia
 * @since 3.9.6
 */
function pewc_aelia_after_add_cart_item_data( $cart_item_data ) {

	// Save the selected currency
	$currency = pewc_aelia_get_selected_currency();
	$cart_item_data['product_extras']['aelia_currency'] = $currency;

	// We can revert to this data if the user switches back to this currency
	$cart_item_data['product_extras_' . $currency] = $cart_item_data['product_extras'];

	return $cart_item_data;

}
add_filter( 'pewc_after_add_cart_item_data', 'pewc_aelia_after_add_cart_item_data' );

/**
 * Filter the price for Aelia
 * @since 3.9.5
 */
function pewc_aelia_cs_convert( $amount, $item, $product=null, $from_currency='', $to_currency='' ) {

	if( ! $from_currency ) {
		$from_currency = pewc_aelia_get_from_currency();
	}
	if( ! $to_currency ) {
		$to_currency = pewc_aelia_get_to_currency();
	}

	return apply_filters( 'wc_aelia_cs_convert', $amount, $from_currency, $to_currency );

}
add_filter( 'pewc_filter_field_price', 'pewc_aelia_cs_convert', 10, 3 );
add_filter( 'pewc_filter_option_price', 'pewc_aelia_cs_convert', 10, 3 );

function pewc_aelia_get_from_currency() {
	return get_option( 'woocommerce_currency' );
}

function pewc_aelia_get_to_currency() {
	return get_woocommerce_currency();
}

function pewc_aelia_get_selected_currency() {
	if( ! class_exists( 'WC_Aelia_CurrencySwitcher' ) ) return false;
	return WC_Aelia_CurrencySwitcher::instance()->get_selected_currency();
}

/**
 * Check if currency has changed in Aelia CS
 * If so, convert add-on prices
 * @since 3.9.5
 */
function pewc_aelia_get_cart_item_from_session( $cart_item, $values ) {

	if( ! class_exists( 'Aelia_WC_AFC_RequirementsChecks' ) ) {
		return $cart_item;
	}

	// Check if Aelia CS is active
	if( ! class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
		return $cart_item;
	}

	if( ! isset( $cart_item['product_extras']['aelia_currency'] ) ) {
		return $cart_item;
	}

	$selected_currency = pewc_aelia_get_selected_currency();
	$from_currency = pewc_aelia_get_from_currency();
	$to_currency = pewc_aelia_get_to_currency();

	// We have to convert add-on prices
	if( isset( $cart_item['product_extras_' . $to_currency] ) ) {

		// First, if add-on data for the 'to currency' already exists, then just switch back
		$cart_item['product_extras'] = $cart_item['product_extras_' . $to_currency];
		if( isset ( $cart_item['product_extras_' . $to_currency]['price_with_extras'] ) ) {
			$cart_item['product_extras']['price_with_extras'] = $cart_item['product_extras_' . $to_currency]['price_with_extras'];
		}
		if( isset ( $cart_item['product_extras_' . $to_currency]['original_price'] ) ) {
			$cart_item['product_extras']['original_price'] = $cart_item['product_extras_' . $to_currency]['original_price'];
		}

	} else if( isset( $cart_item['product_extras']['groups'] ) ) {

		// Ensure we are converting from the correct currency
		$from_currency = $cart_item['product_extras']['aelia_currency'];

		// We have to iterate through every price and convert to the new currency
		foreach( $cart_item['product_extras']['groups'] as $group_id=>$group ) {
			foreach( $group as $field_id=>$field ) {
				if( ! empty( $field['price'] ) ) {
					// Convert the add-on price
					$cart_item['product_extras']['groups'][$group_id][$field_id]['price'] = pewc_aelia_cs_convert( $field['price'], $field, null, $from_currency, $to_currency );
				}
			}
		}
		$cart_item['product_extras']['price_with_extras'] = pewc_aelia_cs_convert( $cart_item['product_extras']['price_with_extras'], $field, null, $from_currency, $to_currency );
		$cart_item['product_extras']['original_price'] = pewc_aelia_cs_convert( $cart_item['product_extras']['original_price'], $field, null, $from_currency, $to_currency );

		// Save a copy at the end so that we can switch back
		if( ! isset( $cart_item['product_extras_' . $to_currency] ) ) {
			$cart_item['product_extras_' . $to_currency] = $cart_item['product_extras'];
			$cart_item['product_extras_' . $to_currency]['price_with_extras'] = $cart_item['product_extras']['price_with_extras'];
			$cart_item['product_extras_' . $to_currency]['original_price'] = $cart_item['product_extras']['original_price'];
		}

		// Update our current currency
		$cart_item['product_extras']['aelia_currency'] = $to_currency;

	}

	return $cart_item;

}
add_filter( 'woocommerce_get_cart_item_from_session', 'pewc_aelia_get_cart_item_from_session', 10, 2 );
