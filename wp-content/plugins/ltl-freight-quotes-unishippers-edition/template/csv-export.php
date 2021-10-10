<?php

if (!class_exists('EnCsvExport')) {

    class EnCsvExport
    {
        public function __construct()
        {
            add_filter('woocommerce_product_export_product_column_en_nickname', [$this, 'en_nickname'], 10, 2);
            add_filter('woocommerce_product_export_product_column_en_city', [$this, 'en_city'], 10, 2);
            add_filter('woocommerce_product_export_product_column_en_state', [$this, 'en_state'], 10, 2);
            add_filter('woocommerce_product_export_product_column_en_zip', [$this, 'en_zip'], 10, 2);
            add_filter('woocommerce_product_export_product_column_en_country', [$this, 'en_country'], 10, 2);

            // Update new columns
            add_filter('woocommerce_product_export_product_column_en_product_freight_class', [$this, 'en_product_freight_class'], 10, 2);
            add_filter('woocommerce_product_export_product_column_en_product_freight_class_variation', [$this, 'en_product_freight_class_variation'], 10, 2);

            // Add columns
            add_filter('woocommerce_product_export_column_names', [$this, 'en_add_export_column'], 10, 2);
            add_filter('woocommerce_product_export_product_default_columns', [$this, 'en_add_export_column'], 10, 2);
        }

        /**
         * Add columns
         * @param array $columns
         * @return array
         */
        public function en_add_export_column($columns)
        {
            $columns['en_nickname'] = 'Meta:_dropship_location_nickname';
            $columns['en_city'] = 'Meta:_dropship_location_city';
            $columns['en_state'] = 'Meta:_dropship_location_state';
            $columns['en_zip'] = 'Meta:_dropship_location_zip_code';
            $columns['en_country'] = 'Meta:_dropship_location_country';

            // Update new columns
            $columns['en_product_freight_class'] = 'Meta:_product_freight_class';
            $columns['en_product_freight_class_variation'] = 'Meta:_product_freight_class_variation';
            if (isset($columns['meta:_dropship_location'])) {
                unset($columns['meta:_dropship_location']);
            }
            if (isset($columns['meta:_ltl_freight'])) {
                unset($columns['meta:_ltl_freight']);
            }
            if (isset($columns['meta:_ltl_freight_variation'])) {
                unset($columns['meta:_ltl_freight_variation']);
            }

            return $columns;
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_product_freight_class_variation($value, $product)
        {
            return get_post_meta($product->get_id(), '_ltl_freight_variation', true);
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_product_freight_class($value, $product)
        {
            return get_post_meta($product->get_id(), '_ltl_freight', true);
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */

        public function en_nickname($value, $product)
        {
            $locations = [];
            $_dropship_location = get_post_meta($product->get_id(), '_dropship_location', true);
            $ds_id_arr = maybe_unserialize($_dropship_location);
            if (isset($ds_id_arr) && !empty($ds_id_arr) && !is_string($ds_id_arr)) {
                foreach ($ds_id_arr as $key => $ds_id) {
                    $locations[] = $this->en_get_address($ds_id);
                }
            } else {
                $locations[] = $this->en_get_address($_dropship_location);
            }
            $index = '';
            foreach ($locations as $key => $location) {
                $index_value = (isset($location['nickname'])) ? $location['nickname'] : '';
                $index .= strlen($index) > 0 ? ',' . $index_value : $index_value;
            }
            return $index;
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_city($value, $product)
        {
            $locations = [];
            $_dropship_location = get_post_meta($product->get_id(), '_dropship_location', true);
            $ds_id_arr = maybe_unserialize($_dropship_location);
            if (isset($ds_id_arr) && !empty($ds_id_arr) && !is_string($ds_id_arr)) {
                foreach ($ds_id_arr as $key => $ds_id) {
                    $locations[] = $this->en_get_address($ds_id);
                }
            } else {
                $locations[] = $this->en_get_address($_dropship_location);
            }
            $index = '';
            foreach ($locations as $key => $location) {
                $index_value = (isset($location['city'])) ? $location['city'] : '';
                $index .= strlen($index) > 0 ? ',' . $index_value : $index_value;
            }
            return $index;
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_state($value, $product)
        {
            $locations = [];
            $_dropship_location = get_post_meta($product->get_id(), '_dropship_location', true);
            $ds_id_arr = maybe_unserialize($_dropship_location);
            if (isset($ds_id_arr) && !empty($ds_id_arr) && !is_string($ds_id_arr)) {
                foreach ($ds_id_arr as $key => $ds_id) {
                    $locations[] = $this->en_get_address($ds_id);
                }
            } else {
                $locations[] = $this->en_get_address($_dropship_location);
            }
            $index = '';
            foreach ($locations as $key => $location) {
                $index_value = (isset($location['state'])) ? $location['state'] : '';
                $index .= strlen($index) > 0 ? ',' . $index_value : $index_value;
            }
            return $index;
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_zip($value, $product)
        {
            $locations = [];
            $_dropship_location = get_post_meta($product->get_id(), '_dropship_location', true);
            $ds_id_arr = maybe_unserialize($_dropship_location);
            if (isset($ds_id_arr) && !empty($ds_id_arr) && !is_string($ds_id_arr)) {
                foreach ($ds_id_arr as $key => $ds_id) {
                    $locations[] = $this->en_get_address($ds_id);
                }
            } else {
                $locations[] = $this->en_get_address($_dropship_location);
            }
            $index = '';
            foreach ($locations as $key => $location) {
                $index_value = (isset($location['zip'])) ? $location['zip'] : '';
                $index .= strlen($index) > 0 ? ',' . $index_value : $index_value;
            }
            return $index;
        }

        /**
         * Method to create the data to be exported for one item in the column.
         *
         * @param mixed $value (default: '')
         *
         * @param WC_Product $product
         *
         * @return mixed $value - Should be in a format that can be output into a text file (string, numeric, etc).
         */
        public function en_country($value, $product)
        {
            $locations = [];
            $_dropship_location = get_post_meta($product->get_id(), '_dropship_location', true);
            $ds_id_arr = maybe_unserialize($_dropship_location);
            if (isset($ds_id_arr) && !empty($ds_id_arr) && !is_string($ds_id_arr)) {
                foreach ($ds_id_arr as $key => $ds_id) {
                    $locations[] = $this->en_get_address($ds_id);
                }
            } else {
                $locations[] = $this->en_get_address($_dropship_location);
            }
            $index = '';
            foreach ($locations as $key => $location) {
                $index_value = (isset($location['country'])) ? $location['country'] : '';
                $index .= strlen($index) > 0 ? ',' . $index_value : $index_value;
            }
            return $index;
        }

        /**
         * Multiple Drop ship address
         * @param type $ds_id
         * @return string
         * @global type $wpdb
         */
        public function en_get_address($ds_id)
        {
            global $wpdb;
            $location = [];

            if (!empty($ds_id) && strlen($ds_id) > 0) {
                $dropship_data = $wpdb->get_results(
                    "SELECT nickname, city, state, zip, country 
                  FROM " . $wpdb->prefix . "warehouse WHERE id=$ds_id"
                );
                if (isset($dropship_data) && !empty($dropship_data) && !is_string($dropship_data)) {
                    $dropship = reset($dropship_data);
                    $location ['nickname'] = $dropship->nickname;
                    $location ['city'] = $dropship->city;
                    $location ['state'] = $dropship->state;
                    $location ['zip'] = $dropship->zip;
                    $location ['country'] = $dropship->country;
                }
            }
            return $location;
        }
    }

    new EnCsvExport();
}