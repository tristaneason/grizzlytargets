<?php
// Add Theme Scripts
function add_theme_scripts() {
    wp_enqueue_style('style', get_stylesheet_uri(), [], wp_rand(333, 999));
    // wp_enqueue_script('script', get_template_directory_uri() . '/js/script.js', '', 1.1, true);
}
add_action('wp_enqueue_scripts', 'add_theme_scripts');

// Add "Free Shipping" Label
function add_free_shipping_label($label, $method) {
    if ($method->cost == 0) {
        $label = 'Free Shipping';
    }
    return $label;
}
add_filter('woocommerce_cart_shipping_method_full_label', 'add_free_shipping_label', 10, 2);

// PLUGIN: MY CUSTOM FUNCTIONS

// Business Bloomer Sort Shipping Methods
function businessbloomer_sort_shipping_methods($rates, $package) {
    if (empty($rates)) return;
    if (!is_array($rates)) return;
   
    uasort($rates, function ($a, $b) { 
        if ($a == $b) return 0;
        return ($a->cost < $b->cost) ? -1 : 1; 
    });
    return $rates; // NOTE: BEFORE TESTING EMPTY YOUR CART
}
add_filter('woocommerce_package_rates', 'businessbloomer_sort_shipping_methods', 10, 2);

// WC Ninja Change Flat Rates Cost
function wc_ninja_change_flat_rates_cost($rates, $package) {
	// Make sure flat rate is available
	if (isset($rates['flat_rate:42'])) {
		// Set the cost to $100
		$rates['flat_rate:42']->cost = Custom;
	}
	return $rates;
}
add_filter('woocommerce_package_rates', 'wc_ninja_change_flat_rates_cost', 10, 2);
