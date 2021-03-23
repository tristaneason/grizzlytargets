<?php

/**
 * WWE LTL Group Packaging
 *
 * @package     WWE LTL Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists("en_unishippers_freight_view_Order_Widget_Details")) {
    class en_unishippers_freight_view_Order_Widget_Details
    {
        public $sender_origin;
        public $accessorials;
        public $label_sufex;
        public $product_name;
        public $count;
        public $_address;

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->en_call_hooks();
        }

        /**
         * Call needed hooks.
         */
        public function en_call_hooks()
        {

            /* Woocommerce order action hook */
            add_action(
                'woocommerce_order_actions', array($this, 'en_assign_order_details'), 10
            );
        }

        /**
         * Adding Meta container admin shop_order pages
         * @param $actions
         */
        function en_create_meta_box_order_details()
        {

            $this->en_assign_order_details();
        }

        /**
         * Get order details from meta data.
         */
        function en_assign_order_details($actions)
        {
            global $wpdb;
            $this->shipment_status = 'single';
            $order_id = get_the_ID();

            $this->label_sufex = array();
            $this->accessorials = array();

            $order = new WC_Order($order_id);
            $this->order_key = $order->get_order_key();
            $shipping_details = $order->get_items('shipping');
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $this->shipping_method_title = $shipping_item_obj->get_method_title() . ' ' . ' : ';
                $this->_shipping_method_title = $shipping_item_obj->get_method_title() . ' ' . ' ';
                $this->shipping_method_total = $shipping_item_obj->get_total();
                $this->result_details = $shipping_item_obj->get_formatted_meta_data();

            }
            /* Add metabox if user selected our service */
            if (!empty($this->result_details) && count($this->result_details) > 0) {
                /* Add metabox for 3dbin visual details */
                add_meta_box(
                    'en_additional_order_details', __('Additional Order Details', 'woocommerce'), array($this, 'en_add_meta_box_order_widget'), 'shop_order', 'side', 'low', 'core');
            }

            return $actions;
        }

        /**
         * Add order details in metabox.
         */
        public function en_add_meta_box_order_widget()
        {
            /* Remove index 0 */
            $order_details = $this->result_details;
            $this->en_origin_services_details($order_details);
        }

        /**
         * Origin & Services details.
         * @param array $order_data
         * @param string $shipment_status
         * @param int $ship_count
         * @param array $single_price_details
         */
        function en_origin_services_details($order_data)
        {

            $this->currency_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
            $this->count = 0;

            $shipment = 'single';
            foreach ($order_data as $key => $is_meta_data) {
                (isset($is_meta_data->key) && $is_meta_data->key === "min_prices") ? $shipment = 'multiple' : '';
            }

            if ($shipment == 'multiple') {
                $order_data = reset($order_data);

                if (isset($order_data->key) && $order_data->key == "min_prices") {
                    $order_data = json_decode($order_data->value, TRUE);
                    foreach ($order_data as $key => $quote) {
                        $this->sender_origin = (isset($quote['meta_data']['sender_origin'])) ? ucwords($quote['meta_data']['sender_origin']) : "";
                        $this->accessorials = (isset($quote['meta_data']['accessorials'])) ? json_decode($quote['meta_data']['accessorials'], TRUE) : array();
                        $this->product_name = (isset($quote['meta_data']['product_name'])) ? json_decode($quote['meta_data']['product_name'], TRUE) : array();
                        $this->quote_id = (isset($quote['meta_data']['quote_id'])) ? $quote['meta_data']['quote_id'] : '';
                        $this->_address = (isset($quote['meta_data']['_address'])) ? $quote['meta_data']['_address'] : array();
                        $this->label_sufex = (isset($quote['label_sufex'])) ? $quote['label_sufex'] : array();
                        $this->shipping_method_total = (isset($quote['cost'])) ? $quote['cost'] : "";

                        $_label = (isset($quote['label']) && strlen($quote['label']) > 0) ? $quote['label'] : "Freight";

                        $_label_append = " : ";
                        if (isset($quote['code']) && ($quote['code'] == "no_quotes")) {
                            $_label = "";
                            $_label_append = "";
                        }

                        $this->shipping_method_title = $this->filter_from_label_sufex($this->label_sufex, $_label) . $_label_append;

                        if (array_key_exists('hat_append_label', $quote) && isset($quote['hat_append_label']) && is_string($quote['hat_append_label'])) {
                            $this->_shipping_method_title = $this->filter_from_label_sufex($this->label_sufex, $_label) . ' ' . $quote['hat_append_label'];
                        } else {
                            $this->_shipping_method_title = $this->shipping_method_title;
                        }

                        $this->shipping_method_total = (isset($quote['cost'])) ? $quote['cost'] : "";
                        $this->count++;
                        $this->show_order_widget_detail();
                    }
                }
            } else {
                foreach ($order_data as $key => $value) {
                    (isset($value->key) && $value->key == "sender_origin") ? $this->sender_origin = ucwords($value->value) : "";
                    (isset($value->key) && $value->key == "accessorials") ? $this->accessorials = json_decode($value->value, TRUE) : "";
                    (isset($value->key) && $value->key == "label_sufex") ? $this->label_sufex = json_decode($value->value, TRUE) : "";
                    (isset($value->key) && $value->key == "product_name") ? $this->product_name = json_decode($value->value, TRUE) : "";
                    (isset($value->key) && $value->key == "quote_id") ? $this->quote_id = json_decode($value->value, TRUE) : "";

                }

                $this->count++;
                $this->show_order_widget_detail();
            }


        }

        /**
         * Show Order Detai on order page
         */
        public function show_order_widget_detail()
        {
            if (!strlen($this->sender_origin) > 0) {
                return;
            }
            echo '<h4 style="text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . $this->count . " > Origin & Services </h4>";
            echo '<ul class="en-list" style="list-style: disc;list-style-position: inside;">';
            echo '<li>';

            echo $this->sender_origin;

            echo '<br />';

            echo '</li>';

            if (isset($this->_address) && is_string($this->_address) && strlen($this->_address) > 0) {
                echo '<li>' . $this->_shipping_method_title . $this->_address . ' ' . $this->en_format_price($this->shipping_method_total) . '</li>';
            } else {
                echo '<li>' . $this->shipping_method_title . $this->en_format_price($this->shipping_method_total) . '</li>';
            }

            if (isset($this->quote_id) && !empty($this->quote_id)) {
                echo '<li> Quote ID : ' . $this->quote_id . '</li>';
            }

            /* Show accessorials */
            $this->en_show_accessorials(array_unique(array_merge($this->accessorials, $this->label_sufex)));


            echo "</ul>";
            echo "<br />";
            echo '<h4 style="    text-decoration: underline;margin: 4px 0px 4px 0px;">Shipment ' . $this->count . " > items </h4>";
            echo '<ul id="product-details-order" class="en-list" style="list-style: disc;list-style-position: inside;">';
            foreach (array_filter($this->product_name) as $product_str) {

                echo '<li>' . $product_str . '</li>';

            }
            echo '</ul>';
            echo "<br /><br />";
        }

        /**
         * set accessorials in label of rate
         * @param array $label_sufex
         * @param string $label
         * @return string
         */
        public function filter_from_label_sufex($label_sufex, $label)
        {
            $accessorials = [
                'L' => 'liftgate delivery',
                'T' => 'tailgate delivery',
            ];

            if (strpos($label, 'residential delivery') == false) {
                $accessorials['R'] = 'residential delivery';
            }

            $label_sufex = is_array($label_sufex) ? $label_sufex : [];
            $label_sufex = array_intersect_key($accessorials, array_flip($label_sufex));
            $label .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';
            return $label;
        }

        /**
         * Price format.
         * @param int/double/string $dollars
         * @return string
         */
        function en_format_price($dollars)
        {
            return $this->currency_symbol . number_format(sprintf('%0.2f',
                    preg_replace("/[^0-9.]/", "", $dollars)), 2);
        }

        /**
         * Show accessorial.
         */
        public function en_show_accessorials($service_order_data)
        {
            foreach ($service_order_data as $key => $value) {
                echo ($value == "R") ? '<li>Residential delivery</li>' : "";
                echo ($value == "L") ? '<li>Lift gate delivery</li>' : "";
                echo ($value == "H") ? '<li>Hazardous Material</li>' : "";
            }
        }
    }

    /* Initialize class object */
    new en_unishippers_freight_view_Order_Widget_Details();
}    