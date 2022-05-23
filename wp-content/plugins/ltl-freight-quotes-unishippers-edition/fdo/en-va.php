<?php

class EnUniShipfreightVa
{
    /**
     * Fuction which is responsible to return va coupon data
     */
    public function get_va_coupon_data(){

        $va_coupon_data = get_option('en_va_coupon_data');
        if(empty($va_coupon_data)){
            try{
                $va_coupon_data = $this->get_va_coupon_data_from_api();
                $data_decoded = json_decode($va_coupon_data);
                if(isset($data_decoded->promo)){
                    update_option('en_va_coupon_data', $va_coupon_data);
                }else{
                    return [];
                }
            }catch(Exception $e){
                return [];
            }
        }

        return $this->get_va_coupon_parsed_data($va_coupon_data);
    }

    /**
     * Fuction which is responsible to return va coupon data from API
     */
    public function get_va_coupon_data_from_api(){
        $data = array(
            'shop' => unishippers_freight_get_domain(),
            'marketplace' => 'wp'
        );

        $data_encoded = http_build_query($data);
    
        $url = UNISHIPPERS_FREIGHT_VA_COUPON_BASE_URL . "/use_coupon?".$data_encoded;
        $response = wp_remote_get($url,
            array(
                'method' => 'GET',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => [],
            )
        );

        return wp_remote_retrieve_body($response);
    }

    public function get_va_coupon_parsed_data($va_coupon_data)
    {
        $va_coupon_decoded = json_decode($va_coupon_data);
        $parsed_data = [];
        $parsed_data['coupon'] = !empty($va_coupon_decoded->promo->coupon) ? $va_coupon_decoded->promo->coupon : '';
        $parsed_data['status'] = !empty($va_coupon_decoded->promo->status) ? $va_coupon_decoded->promo->status : 0;
        $parsed_data['va_user'] = isset($va_coupon_decoded->va_user) ? $va_coupon_decoded->va_user : false;
        $parsed_data['va_company_id'] = isset($va_coupon_decoded->av_company_id) ? $va_coupon_decoded->av_company_id : false;

        if($parsed_data['va_company_id']){
            $parsed_data['va_company_text'] = ' with validate addresses account <strong>['.$parsed_data['va_company_id'].']</strong>';
        }else{
            $parsed_data['va_company_text'] = '';
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

        $parsed_data['register_url'] = UNISHIPPERS_FREIGHT_VA_COUPON_BASE_URL.'/register?code='.$base64string;
        $parsed_data['login_url'] = UNISHIPPERS_FREIGHT_VA_COUPON_BASE_URL.'/login?code='.$base64string;
        // $parsed_data['user_guide_get_started'] = UNISHIPPERS_FREIGHT_VA_COUPON_BASE_URL.'/help/Getting_Started';

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


