<?php

/**
 * Order export.
 */
if (!class_exists('EnOrderExport')) {

    class EnOrderExport
    {
        public $en_orders_per_step = 50;

        public function __construct()
        {
            add_action('woocommerce_thankyou', [$this, 'en_woocommerce_thankyou'], 10, 1);
            add_action('init', [$this, 'en_orders_ids']);
            add_action('en_async_orders_exporting_process', [$this, 'en_async_orders_exporting_process']);
            add_filter('cron_schedules', [$this, 'en_async_cron_schedule'], 10, 1);
        }

        // Async cron schedule.
        public function en_async_cron_schedule($schedules)
        {
            $schedules['en_async_cron_every_5_minute'] = array(
                'interval' => 60 * 5,
                'display' => __('Every 5 minute'),
            );
            return $schedules;
        }

        // Async order exported.
        public function en_async_orders_exporting_process()
        {
            try {
                $en_orders_ids_flag = get_option('en_orders_ids_flag');
                $en_async_orders_exporting_process = get_option('en_async_orders_exporting_process');
                if ($en_orders_ids_flag == 'executed' && $en_async_orders_exporting_process != 'completed') {
                    $en_orders_id = get_option('en_orders_ids');
                    $en_orders_id = (isset($en_orders_id) && strlen($en_orders_id) > 0) ? json_decode($en_orders_id, true) : [];
                    if (!empty($en_orders_id) && is_array($en_orders_id)) {
                        $orders = array_slice($en_orders_id, 0, $this->en_orders_per_step);
                        $en_orders_ids = $en_orders = [];
                        foreach ($orders as $key => $order) {
                            if (isset($order['ID'])) {
                                $order_id = $order['ID'];
                                if (in_array($order_id, $en_orders_ids)) {
                                    continue;
                                }

                                $en_orders_ids[] = $order_id;
                                $order_meta = wc_get_order($order_id);
                                $en_order_data = $this->en_order_details_by_id($order_meta, $order_id);
                                if (!empty($en_order_data)) {
                                    $en_orders = array_merge($en_orders, $en_order_data);
                                }
                            }
                        }

                        $this->en_orders_sending($en_orders, $en_orders_ids);
                        $en_remain_orders_id = array_slice($en_orders_id, $this->en_orders_per_step);
                        update_option('en_orders_ids', json_encode($en_remain_orders_id));
                    } else {
                        $en_orders_ids = ['completed'];
                        update_option('en_async_orders_exporting_process', 'completed');
                        function_exists('wp_clear_scheduled_hook') ? wp_clear_scheduled_hook('en_async_orders_exporting_process') : '';
                    }

                    $post_content = 'Note! Exported Orders : ' . json_encode($en_orders_ids);
                    $this->en_custom_post_save($post_content);
                }
            } catch (\Exception $ex) {
                $post_content = 'Error! Exporting Orders : ' . $ex->getMessage();
                $this->en_custom_post_save($post_content);
            }
        }

        // First time when order exporting process in action.
        public function en_orders_ids()
        {
            try {
                $en_orders_ids_flag = get_option('en_orders_ids_flag');
                if ($en_orders_ids_flag != 'executed') {
                    global $wpdb;

                    $date_from = date('Y-m-d', strtotime('-3 month'));;
                    $date_to = date('Y-m-d');
                    $orders = $wpdb->get_results("SELECT ID FROM $wpdb->posts 
                        WHERE post_type = 'shop_order'
                        AND post_date BETWEEN '{$date_from}  00:00:00' AND '{$date_to} 23:59:59'
                    ");

                    update_option('en_orders_ids', json_encode($orders));
                    update_option('en_orders_ids_flag', 'executed');

                    $post_content = 'Note! Total Orders Ids: ' . json_encode($orders);
                    $this->en_custom_post_save($post_content);

                    // Schedules the event if it's NOT already scheduled.
                    if (!wp_next_scheduled('en_async_orders_exporting_process')) {
                        wp_schedule_event(time(), 'en_async_cron_every_5_minute', 'en_async_orders_exporting_process');
                    }
                }
            } catch (\Exception $ex) {
                $post_content = 'Error! Getting Orders Ids: ' . $ex->getMessage();
                $this->en_custom_post_save($post_content);
            }
        }

        // When order places on the checkout page.
        public function en_woocommerce_thankyou($order_id)
        {
            if (!$order_id)
                return;
            // Allow code execution only once
            if (!get_post_meta($order_id, 'en_woocommerce_thankyou_done', true)) {

                $order = wc_get_order($order_id);
                $current_date = date('Y-m-d h:i:s', time());
                $order_details = $this->en_order_details_by_id($order, $order_id, $current_date);
                $this->en_orders_sending($order_details, [$order_id]);
                // Flag the action as done (to avoid repetitions on reload for example)
                $order->update_meta_data('en_woocommerce_thankyou_done', true);
                $order->save();
            }
        }

        // Get order details by id.
        public function en_order_details_by_id($order, $order_id, $current_date = '')
        {
            $en_widget_details_data = [];
            if (empty($order)) {
                return $en_widget_details_data;
            }
            // Get an instance of the WC_Order object
            $order_created_date = isset($current_date) && strlen($current_date) > 0 ? $current_date : $order->order_date;
            $shipping_details = $order->get_items('shipping');
            foreach ($shipping_details as $item_id => $shipping_item_obj) {
                $get_formatted_meta_data = $shipping_item_obj->get_formatted_meta_data();
                foreach ($get_formatted_meta_data as $key => $meta_data) {
                    switch ($meta_data->key) {
                        case 'en_fdo_meta_data':
                            $en_widget_details = json_decode($meta_data->value, true);
                            if (!empty($en_widget_details)) {
                                $en_widget_details_data[] = $en_widget_details;
                            }
                            break;
                    }
                }
            }

            $en_orders = [];
            if (!empty($en_widget_details_data)) {
                foreach ($en_widget_details_data as $key => $shipments) {
                    $data = (isset($shipments['data'])) ? $shipments['data'] : [];
                    $shipment_type = (isset($shipments['shipment'])) ? $shipments['shipment'] : 'single';
                    if (is_array($data) && !empty($data)) {
                        foreach ($data as $ship_nbr => $shipment) {
                            $plugin_name = (isset($shipment['plugin_name'])) ? $shipment['plugin_name'] : '';
                            $rate = (isset($shipment['rate'])) ? $shipment['rate'] : [];
                            $cost = (isset($rate['cost'])) ? $rate['cost'] : 0;
                            $label = (isset($rate['label'])) ? $rate['label'] : '';
                            $en_orders[] = [
                                'orderId' => $order_id,
                                'serviceId' => $ship_nbr,
                                'serviceName' => $label,
                                'serviceCharge' => $cost,
                                'orderCreatedDate' => $order_created_date,
                                'carrierName' => $plugin_name,
                                'shipmentType' => $shipment_type,
                                'orderMeta' => base64_encode(json_encode($shipment))
                            ];
                        }
                    }
                }
            }

            return $en_orders;
        }

        // Orders sending.
        public function en_custom_post_save($post_content)
        {
            $wp_post = array(
                "post_title" => date("F j, Y, g:i a"),
                "post_content" => $post_content,
                "post_excerpt" => 'custom_post',
                "post_type" => 'en_orders_exporting'
            );

            wp_insert_post($wp_post, true);
        }

        // Orders sending.
        public function en_orders_sending($orders, $en_orders_ids)
        {
            $url = 'https://analytic-data.eniture.com/index.php';
            $post_data = [
                'serverName' => unishippers_freight_get_domain(),
                'licenseKey' => get_option('wc_settings_unishippers_freight_licence_key'),
                'currencyUnit' => get_option('woocommerce_currency'),
                'platform' => 'wordpress',
                'orders' => $orders,
            ];

            $curl_obj = new Unishippers_Curl_Request();
            $response = $curl_obj->unishippers_freight_get_curl_response($url, $post_data);
            $post_content = "Response: " . $response . "<br>Count: " . count($en_orders_ids) . "<br>Orders ID: " . json_encode($en_orders_ids);
            $this->en_custom_post_save($post_content);
            return $response;
        }
    }

    new EnOrderExport();
}