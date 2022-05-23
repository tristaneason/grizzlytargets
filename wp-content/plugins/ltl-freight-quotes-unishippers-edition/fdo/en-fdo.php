<?php

class EnUniShipfreightFdo
{

    public $en_fdo_meta_data;

    /**
     * product hazardous.
     * @param array $package
     * @param array $en_fdo_meta_data
     * @return array
     */
    public function en_package_hazardous($package, $en_fdo_meta_data)
    {
        $hazmat = (isset($package['hazardousMaterial']) && $package['hazardousMaterial'] == 'yes') ? true : false;
        $en_fdo_meta_data['accessorials']['hazmat'] = $hazmat;
        return $en_fdo_meta_data;
    }

    /**
     * arrange cart objects.
     * @param type $package
     * @return array
     */
    public function en_cart_package($package)
    {

        $this->en_fdo_meta_data['plugin_type'] = 'ltl';
        $this->en_fdo_meta_data['plugin_name'] = 'unishippers_freight';
        $accessorials['residential'] = get_option('wc_settings_unishipper_residential_delivery') == 'yes' ? true : false;
        $accessorials['liftgate'] = get_option('wc_settings_unishippers_freight_lift_gate_delivery') == 'yes' ? true : false;
        $this->en_fdo_meta_data['accessorials'] = $accessorials;

        (isset($package['items'])) ? $this->en_package_items($package['items']) : '';
        (isset($package['origin'])) ? $this->en_package_address($package['origin']) : '';

        return $this->en_fdo_meta_data;
    }

    /**
     * arrange items.
     * @param type $items
     */
    public function en_package_items($items)
    {
        $this->en_fdo_meta_data['items'] = [];
        foreach ($items as $item_key => $item_data) {
            $nmfc_number = $productId = $productName = $productQty = $actualProductPrice = $products = $productPrice = $productWeight = $productLength = $productWidth = $productHeight = $ptype = $hazardousMaterial = $productType = $productSku = $productClass = $attributes = $variantId = $nestedMaterial = $nestedPercentage = $nestedDimension = $nestedItems = $stakingProperty = '';
            extract($item_data);
            $meta_data = [];
            if (!empty($attributes)) {
                foreach ($attributes as $attr_key => $attr_value) {
                    $meta_data[] = [
                        'key' => $attr_key,
                        'value' => $attr_value,
                    ];
                }
            }

            $item = [
                'id' => $productId,
                'name' => $productName,
                'quantity' => $productQty,
                'price' => $productPrice,
                'price' => $actualProductPrice,
                'weight' => $productWeight,
                'length' => $productLength,
                'width' => $productWidth,
                'height' => $productHeight,
                'type' => $ptype,
                'hazmat' => $hazardousMaterial,
                'product' => $productType,
                'sku' => $productSku,
                'attributes' => $attributes,
                'shipping_class' => $productClass,
                'variant_id' => $variantId,
                'meta_data' => $meta_data,
                // Nesting
                'nested_material' => $nestedMaterial,
                'nested_percentage' => $nestedPercentage,
                'nested_dimension' => $nestedDimension,
                'nested_items' => $nestedItems,
                'staking_property' => $stakingProperty,
                'nmfc_number' => $nmfc_number
            ];

            // Hook for flexibility adding to package
            $item = apply_filters('en_fdo_package', $item, $item_data);
            $this->en_fdo_meta_data['items'][$item_key] = $item;
        }
    }

    /**
     * Get address.
     * @param array $address
     */
    public function en_package_address($address)
    {
        (isset($address['locationId'])) ? $address['id'] = $address['locationId'] : '';
        $this->en_fdo_meta_data['address'] = $address;
    }

    /**
     * Fuction which is responsible to return fdo coupon data
     */
    public function get_fdo_coupon_data(){

        $fdo_coupon_data = get_option('en_fdo_coupon_data');
        if(empty($fdo_coupon_data)){
            try{
                $fdo_coupon_data = $this->get_fdo_coupon_data_from_api();
                $data_decoded = json_decode($fdo_coupon_data);
                if(isset($data_decoded->promo)){
                    update_option('en_fdo_coupon_data', $fdo_coupon_data);
                }else{
                    return [];
                }
            }catch(Exception $e){
                return [];
            }
        }

        return $this->get_fdo_coupon_parsed_data($fdo_coupon_data);
    }

    /**
     * Fuction which is responsible to return fdo coupon data from API
     */
    public function get_fdo_coupon_data_from_api(){
        $data = array(
            'marketplace' => 'wp',
            'shop' => unishippers_freight_get_domain()
        );
    
        $url = UNISHIPPERS_FREIGHT_FDO_COUPON_BASE_URL . "/use_coupon";
        $response = wp_remote_get($url,
            array(
                'method' => 'GET',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $data,
            )
        );
        return wp_remote_retrieve_body($response);
    }

    public function get_fdo_coupon_parsed_data($fdo_coupon_data)
    {
        $fdo_coupon_decoded = json_decode($fdo_coupon_data);
        $parsed_data = [];
        $parsed_data['coupon'] = !empty($fdo_coupon_decoded->promo->coupon) ? $fdo_coupon_decoded->promo->coupon : '';
        $parsed_data['status'] = !empty($fdo_coupon_decoded->promo->status) ? $fdo_coupon_decoded->promo->status : 0;
        $parsed_data['fdo_user'] = isset($fdo_coupon_decoded->fdo_user) ? $fdo_coupon_decoded->fdo_user : false;
        $parsed_data['fdo_company_id'] = isset($fdo_coupon_decoded->fdo_company_id) ? $fdo_coupon_decoded->fdo_company_id : 0;

        if(!empty($parsed_data['fdo_company_id'])){
            $parsed_data['fdo_company_text'] = ' with FreightDesk Online account <strong>['.$parsed_data['fdo_company_id'].']</strong>';
        }else{
            $parsed_data['fdo_company_text'] = '';
        }

        $active_discounted_carriers = $this->get_active_discounted_carriers();
        $user_data = wp_get_current_user();

        $params = [
            'shop' => unishippers_freight_get_domain(),
            'promocode' => $parsed_data['coupon'],
            'email' => $user_data->user_email,
            'apps' => $active_discounted_carriers
        ];

        $query_params = http_build_query($params);

        $base64string = base64_encode($query_params);

        $parsed_data['register_url'] = UNISHIPPERS_FREIGHT_FDO_COUPON_BASE_URL.'/register?code='.$base64string;
        $parsed_data['login_url'] = UNISHIPPERS_FREIGHT_FDO_COUPON_BASE_URL.'/login?code='.$base64string;
        // $parsed_data['user_guide_get_started'] = UNISHIPPERS_FREIGHT_FDO_COUPON_BASE_URL.'/help/Getting_Started';

        return $parsed_data;
    }
    /**
     * Function that returns active discounted carriers
     */
    public function get_active_discounted_carriers(){

        $activaCarrArr = apply_filters('active_plugins', get_option('active_plugins'));

        $carrierslist = 'Unishiper';

        foreach ($activaCarrArr as $key => $carrier) {
            if(strpos($carrier, 'small-package-quotes-wwe-edition') !== false){ 
                $carrierslist .= '|WWE_PL';
            }else if(strpos($carrier, 'small-package-quotes-unishippers-edition') !== false){ 
                $carrierslist .= '|UNI_PL';
            }else if(strpos($carrier, 'ltl-freight-quotes-worldwide-express-edition') !== false){ 
                $carrierslist .= '|WWE_LTL';
            }else if(strpos($carrier, 'ltl-freight-quotes-globaltranz-edition') !== false){ 
                $carrierslist .= '|GTZ';
            }
        }

        return $carrierslist;
    }

}
