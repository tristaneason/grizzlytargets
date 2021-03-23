<?php

/**
 * Unishippers Distance Get
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Get_unishippers_ltl_distance
 */
if (!class_exists('Get_unishippers_ltl_distance')) {

    class Get_unishippers_ltl_distance
    {

        /**
         * Get Distance Function
         * @param $map_address
         * @param $accessLevel
         * @return json
         */
        function unishippers_ltl_get_distance($map_address, $accessLevel, $destinationZip = array())
        {

            $domain = unishippers_freight_get_domain();
            $post = array(
                'acessLevel' => $accessLevel,
                'address' => $map_address,
                'originAddresses' => (isset($map_address)) ? $map_address : "",
                'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
                'eniureLicenceKey' => get_option('wc_settings_unishippers_freight_licence_key'),
                'ServerName' => $domain,
            );

            if (is_array($post) && count($post) > 0) {

                $unishippers_ltl_curl_obj = new Unishippers_Curl_Request();
                $output = $unishippers_ltl_curl_obj->unishippers_freight_get_curl_response(UNISHIPPERS_FREIGHT_DOMAIN_HITTING_URL . '/addon/google-location.php', $post);

                return $output;
            }
        }

    }

}