<?php

class EnRadAddressAjax
{

    function __construct()
    {
        add_action('wp_ajax_nopriv_en_rad_get_address', array($this, 'get_rad_address_api_ajax'));
        add_action('wp_ajax_en_rad_get_address', array($this, 'get_rad_address_api_ajax'));

        add_action('wp_ajax_nopriv_en_rad_save_address', [$this, 'en_rad_save_address_list']);
        add_action('wp_ajax_en_rad_save_address', [$this, 'en_rad_save_address_list']);

        add_action('wp_ajax_nopriv_en_default_unconfirmed_address_types_to', [$this, 'en_default_unconfirmed_address_types_to']);
        add_action('wp_ajax_en_default_unconfirmed_address_types_to', [$this, 'en_default_unconfirmed_address_types_to']);

        add_action('wp_ajax_nopriv_en_rad_delete_address', [$this, 'en_rad_delete_address']);
        add_action('wp_ajax_en_rad_delete_address', [$this, 'en_rad_delete_address']);
    }

    public function en_default_unconfirmed_address_types_to()
    {
        update_option('en_default_unconfirmed_address_types_to', $_POST['en_default_unconfirmed_selected_address_types_to']);
        echo wp_json_encode([]);
        exit;
    }

    public function en_rad_save_address_list()
    {

        $input_data = $_POST;
        $en_res_address_form_data = $input_data["formData"];
        $addr_hidden = $input_data["address_rad"];
        $en_res_address_resp_array = array();

        if (isset($en_res_address_form_data) && $en_res_address_form_data != '' && isset($addr_hidden) && $addr_hidden == "address") {
            parse_str($en_res_address_form_data, $en_search_array);

            $input_data = array(
                'address' => $en_search_array['en_res_address_addr'],
                'address_2' => $en_search_array['en_res_address_addr_2'],
                'city' => $en_search_array['en_res_address_city'],
                'state' => $en_search_array['en_res_address_state'],
                'zip' => $en_search_array['en_res_address_zip'],
                'country' => $en_search_array['en_res_country_code'],
            );

            isset($en_search_array['en_res_nickname']) ? $en_search_array['en_res_nickname'] = stripslashes($en_search_array['en_res_nickname']) : '';
            $input_data_implode = strtolower(implode(", ", $input_data));
            $en_nickname = ['nickname' => $en_search_array['en_res_nickname']];
            $en_address_type = ['en_address_type' => $en_search_array['en_res_address_type']];
            $input_data_arr = array_merge($en_nickname, $input_data, $en_address_type);

            $validate_data = $this->res_address_validate_data($input_data_arr);
            $zipcode = $en_search_array['en_res_address_zip'];
            $res_post_id = (isset($en_search_array['en_res_edit_form_id'])) ? $en_search_array['en_res_edit_form_id'] : 0;

            /* ----Save custom post type---- */
            $post_meta = $input_data_arr;
            $meta_key = "en_rad_addresses";
            $wp_res_addr_post = array(
                "ID" => $res_post_id,
                "post_title" => $input_data_implode,
                "post_content" => $input_data_arr['en_address_type'],
                "post_type" => 'en_rad_addresses',
                "post_excerpt" => 'custom_post',
            );

            /* If Post exist already */
            $en_res_address_addr_id = post_exists($input_data_implode);
            if ($en_res_address_addr_id > 0 && $en_res_address_addr_id != $res_post_id) {
                $en_res_address_resp_array = array(
                    'is_updated_en_res' => false,
                    'is_existing_en_res' => true,
                );
                /**
                 * Encode Json Response array
                 */
                $this->en_response_json_encode($en_res_address_resp_array, false, $en_res_address_addr_id, $post_meta, 'Address already exists.');
            }

            $wp_post_id = wp_insert_post($wp_res_addr_post, true);

            if ($res_post_id && $res_post_id != 0 && $wp_post_id && !is_wp_error($wp_post_id)) {
                update_post_meta($wp_post_id, $meta_key, $post_meta);
                $en_res_address_resp_array = array(
                    'is_updated_en_res' => true,
                );
                /**
                 * Encode Json Response array
                 */
                $this->en_response_json_encode($en_res_address_resp_array, true, $wp_post_id, $post_meta, 'Address updated successfully.');
            } else if ($wp_post_id && !is_wp_error($wp_post_id)) {
                add_post_meta($wp_post_id, $meta_key, $post_meta);
                $en_res_address_resp_array = array(
                    'is_updated_en_res' => false,
                );
                /**
                 * Encode Json Response array
                 */
                $this->en_response_json_encode($en_res_address_resp_array, true, $wp_post_id, $post_meta, 'New address added successfully.');
            } else {
                $en_res_address_resp_array = array();
                /**
                 * Encode Json Response array
                 */
                $this->en_response_json_encode($en_res_address_resp_array, true, 0, '', $wp_post_id->get_error_message());
            }
        } else {
            $en_res_address_resp_array = array();
            /**
             * Encode Json Response array
             */
            $this->en_response_json_encode($en_res_address_resp_array, true, 0, '', 'Unable to add the address.');
        }

        exit;
    }

    /* End save address form */

    /**
     * Send JSON encoded response
     * @param $en_res_address_resp_array
     * @param $is_rad_address
     * @param $en_res_post_id
     * @param $post_meta
     * @param $en_res_ajax_message
     * @return Json Array
     */
    public function en_response_json_encode($en_res_address_resp_array, $is_rad_address, $en_res_post_id, $post_meta, $en_res_ajax_message)
    {
        $en_res_address_resp_array2 = array(
            'message' => $en_res_ajax_message ? __($en_res_ajax_message) : 'No Message',
            'rad_address' => $is_rad_address ? $is_rad_address : false,
            'en_plugin_url' => plugins_url(),
        );

        $en_new_res_addr_array = $en_res_address_resp_array + $en_res_address_resp_array2;
        if ($en_res_post_id && $post_meta) {
            /* Insert response */
            $en_res_address_resp_array3 = array(
                'en_res_id' => $en_res_post_id ? $en_res_post_id : 0,
                'en_res_post_meta' => $post_meta,
            );

            $en_new_final_res_addr_array = $en_new_res_addr_array + $en_res_address_resp_array3;

            echo json_encode($en_new_final_res_addr_array);
            exit;
        } else {

            echo json_encode($en_new_res_addr_array);
            exit;
        }

        exit;
    }

    /**
     * Validate Input Fields
     * @param type $en_res_post_data
     * @return string
     */
    public function res_address_validate_data($en_res_post_data)
    {

        foreach ($en_res_post_data as $key => &$tag) {
            $check_characters = preg_match('/[#$%@^&!_*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $tag);
            if ($check_characters != 1) {
                $data[$key] = sanitize_text_field($tag);
            } else {
                $data[$key] = 'Error';
            }
        }
        return $data;
    }

    /**
     * Delete Post address AJAX
     */
    public function en_rad_delete_address()
    {
        $input_data = $_POST;
        $en_res_address_form_id = sanitize_text_field($input_data["form_del_id"]);
        $addr_hidden = $input_data["address_rad_del"];

        if (isset($en_res_address_form_id) && $en_res_address_form_id != 0 && isset($addr_hidden) && $addr_hidden == "address_delete") {

            $delete_res_addr_id = wp_delete_post($en_res_address_form_id);

            if ($delete_res_addr_id && $delete_res_addr_id != 0 && !is_wp_error($delete_res_addr_id)) {
                echo json_encode(array('rad_address' => true, 'message' => __('Address deleted successfully.')));
                exit;
            } else {
                echo json_encode(array('rad_address' => false, 'message' => __('Unable to delete the address.')));
                exit;
            }
        }

        exit;
    }

    public function en_rad_small_address($map_address, $access_level, $destination_zip = array())
    {

        $domain = en_residential_get_domain();
        $postData = array(
            'acessLevel' => $access_level,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destination_zip)) ? $destination_zip : "",
            'eniureLicenceKey' => get_option('fedex_small_licence_key'),
            'ServerName' => $domain,
        );
        $en_rad_http_response = new EnRadHttpResponse();
        $output = $en_rad_http_response->en_rad_http_response('https://eniture.com/ws/addon/google-location.php', $postData);

        return $output;
    }

    /**
     * Get Address From ZipCode Using API
     */
    public function get_rad_address_api_ajax()
    {
        if (isset($_POST['res_addr_zip'])) {
            $map_address = (isset($_POST['res_addr_zip'])) ? sanitize_text_field($_POST['res_addr_zip']) : "";
            $zipCode = str_replace(' ', '', $map_address);
            $accessLevel = 'address';
            $resp_json = $this->en_rad_small_address($zipCode, $accessLevel);
            $map_result = json_decode($resp_json, true);
            $city = "";
            $state = "";
            $country = "";
            $postcode_localities = 0;
            $address_type = $city_name = $city_option = '';
            if (isset($map_result['error']) && !empty($map_result['error'])) {
                echo json_encode(array('apiResp' => 'apiErr'));
                exit;
            }
            if (isset($map_result['results'], $map_result['status']) && (empty($map_result['results'])) && ($map_result['status'] == "ZERO_RESULTS")) {
                echo json_encode(array('result' => 'ZERO_RESULTS'));
                exit;
            }
            if (count($map_result['results']) == 0) {
                echo json_encode(array('result' => 'false'));
                exit;
            }
            $first_city = '';
            if (count($map_result['results']) > 0) {
                $arrComponents = $map_result['results'][0]['address_components'];
                if (isset($map_result['results'][0]['postcode_localities']) && $map_result['results'][0]['postcode_localities']) {
                    foreach ($map_result['results'][0]['postcode_localities'] as $index => $component) {
                        $first_city = ($index == 0) ? $component : $first_city;
                        $city_option .= '<option value="' . trim($component) . ' "> ' . $component . ' </option>';
                    }
                    $city = '<select id="' . $address_type . 'en_res_city" class="city-multiselect select en_res_addr_multi_state en_res_select_city_css" name="' . $address_type . 'en_res_city" aria-required="true" aria-invalid="false">
                                ' . $city_option . '</select>';
                    $postcode_localities = 1;
                } elseif ($arrComponents) {
                    foreach ($arrComponents as $index => $component) {
                        $type = $component['types'][0];
                        if ($city == "" && ($type == "sublocality_level_1" || $type == "locality")) {
                            $city_name = trim($component['long_name']);
                        }
                    }
                }
                if ($arrComponents) {
                    foreach ($arrComponents as $index => $state_app) {
                        $type = $state_app['types'][0];
                        if ($state == "" && ($type == "administrative_area_level_1")) {
                            $state_name = trim($state_app['short_name']);
                            $state = $state_name;
                        }
                        if ($country == "" && ($type == "country")) {
                            $country_name = trim($state_app['short_name']);
                            $country = $country_name;
                        }
                    }
                }
                echo json_encode(array('first_city' => $first_city, 'city' => $city_name, 'city_option' => $city, 'state' => $state, 'country' => $country, 'postcode_localities' => $postcode_localities));
                exit;
            }
        }
    }

    /* ========= End Class EnRadAddressAjax ========== */
}

new EnRadAddressAjax();
?>