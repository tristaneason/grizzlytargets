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

/**
 * Adds Product Add-Ons data to the WooCommerce PDF Invoices & Packing Slips plugin.
 * @link https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/
 * @since 2.2.0
 * @param $type Type of document, e.g. invoice.
 * @param $item Product data.
 * @param $order Order object.
 */
function pewc_wcpdf_after_item_meta( $type, $item, $order ) {
	if( isset( $item['item']['product_extras']['groups'] ) ) {
		$groups = $item['item']['product_extras']['groups'];
		foreach( $groups as $group ) {
			foreach( $group as $extra_item ) { ?>
				<dl class="meta">
					<?php $description_label = $extra_item['label']; ?>
					<dt class="sku"><?php echo esc_html( $extra_item['label'] ); ?></dt>
					<dd class="sku"><?php echo esc_html( $extra_item['value'] ); ?></dd>
				</dl>
			<?php }
		}
	}
}
add_action( 'wpo_wcpdf_after_item_meta', 'pewc_wcpdf_after_item_meta', 10, 3 );

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
