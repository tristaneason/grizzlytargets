<?php

namespace Objectiv\Plugins\Checkout\Compatibility\Plugins;

use Objectiv\Plugins\Checkout\Compatibility\Base;

class CheckoutFieldEditor extends Base {
	public function is_available() {
		return function_exists('thwcfd_init_checkout_field_editor_lite');
	}

	public function run() {
		remove_filter('woocommerce_default_address_fields' , 'thwcfd_woo_default_address_fields' );
		remove_filter('woocommerce_get_country_locale_default', 'thwcfd_prepare_country_locale');
		remove_filter('woocommerce_get_country_locale_base', 'thwcfd_prepare_country_locale');
		remove_filter('woocommerce_get_country_locale', 'thwcfd_woo_get_country_locale');
		remove_filter('woocommerce_billing_fields', 'thwcfd_billing_fields_lite', apply_filters('thwcfd_billing_fields_priority', 1000), 2);
		remove_filter('woocommerce_shipping_fields', 'thwcfd_shipping_fields_lite', apply_filters('thwcfd_shipping_fields_priority', 1000), 2);
		remove_filter('woocommerce_checkout_fields', 'thwcfd_checkout_fields_lite', apply_filters('thwcfd_checkout_fields_priority', 1000));
	}
}