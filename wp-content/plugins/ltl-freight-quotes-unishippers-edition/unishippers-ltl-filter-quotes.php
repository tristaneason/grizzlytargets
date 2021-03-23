<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Unishippers_Freight_Quotes')) {
    class Unishippers_Freight_Quotes
    {
        /**
         * rating method from quote settings
         * @var string type
         */
        public $rating_method;

        /**
         * rates from web service
         * @var array type
         */
        public $quotes;

        /**
         * Unishippers settings
         * @var array type
         */
        public $quote_settings;

        /**
         * label from quote settings
         * @var atring type
         */
        public $unishipper_label;

        /**
         * class name
         * @var class type
         */
        public $VersionCompat;

        function __construct()
        {
//                construct
        }

        /**
         * set values in class attributes and return quotes
         * @param array type $quotes
         * @param array type $quote_settings
         * @return array type
         */
        public function calculate_quotes($quotes, $quote_settings)
        {
            $this->quotes = $quotes;
            $this->quote_settings = $quote_settings;
            $this->total_carriers = (isset($this->quote_settings['total_carriers'])) ? $this->quote_settings['total_carriers'] : 0;

            $this->VersionCompat = new VersionCompat();
            $rating_method = $this->quote_settings['rating_method'];
            return $this->$rating_method();
        }

        function rand_string()
        {
            return md5(uniqid(mt_rand(), true));
        }

        /**
         * calculate average for quotes
         * @return array type
         */
        public function average_rate()
        {
            $this->quotes = (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, $this->total_carriers) : array();
            $rate_list = $this->VersionCompat->enArrayColumn($this->quotes, 'cost');
            $rate_sum = array_sum($rate_list) / count($this->quotes);
            $quotes_reset = reset($this->quotes);


            $rate[] = array(
                'id' => $this->rand_string(),
                'id' => 'en_unishippers_average_method',
                'cost' => $rate_sum,
                'markup' => (isset($quotes_reset['markup'])) ? $quotes_reset['markup'] : "",
                'label_sufex' => (isset($quotes_reset['label_sufex'])) ? $quotes_reset['label_sufex'] : array(),
                'append_label' => (isset($quotes_reset['append_label'])) ? $quotes_reset['append_label'] : "",
                'meta_data' => (isset($quotes_reset['meta_data'])) ? $quotes_reset['meta_data'] : [],
            );

            return $rate;
        }

        /**
         * calculate cheapest rate
         * @return type
         */
        public function Cheapest()
        {
            return (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, 1) : array();
        }

        /**
         * calculate cheapest rate numbers
         * @return array type
         */
        public function cheapest_options()
        {
            return (isset($this->quotes) && (is_array($this->quotes))) ? array_slice($this->quotes, 0, $this->total_carriers) : array();
        }

    }
}