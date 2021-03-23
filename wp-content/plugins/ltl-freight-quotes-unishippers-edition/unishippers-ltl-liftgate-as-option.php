<?php

if(!defined("ABSPATH"))
{
    exit();
}

if(!class_exists("Uinshipper_Ltl_Liftgate_As_Option"))
{
    class Uinshipper_Ltl_Liftgate_As_Option{
        
        /**
         * set flag for lift gate as option
         * @var string type 
         */
        public $unishippers_freight_as_option;
        
        /**
         *  label sufex
         * @var array type 
         */
        public $label_sfx_arr;
        
        public function __construct()
        {
            $this->unishippers_freight_as_option = get_option("unishippers_freight_liftgate_delivery_as_option");
            $this->label_sfx_arr = array();
        }
        
        /**
         * update request array when lift-gate as an option
         * @param array string $post_data
         * @return array string
         */
        public function unishippers_freight_update_carrier_service( $post_data )
        {
            if(get_option("unishippers_freight_liftgate_delivery_as_option") == "yes")
            {
                $post_data['liftGateAsAnOption'] = '1';
            }
            
            return $post_data;
        }
        
        /**
         * get surcharges from api response 
         * @param array type $surcharges
         * @return array type
         */
        public function update_parse_unishippers_freight_output( $surcharges )
        {   
            $surcharge_amount = array();      
            foreach ($surcharges as $key => $surcharge) {
                if(isset($surcharge->carrierNotification)){
                    $carrierNotification = $surcharge->carrierNotification;
                    $carrierNotification = explode(":", $carrierNotification);
                    $surcharge_amount[$carrierNotification[0]] = (isset($carrierNotification[1])) ? $carrierNotification[1] : 0;
                }
            }

            return $surcharge_amount;
        }
        
        /**
         * update quotes
         * @param array type $rate
         * @return array type
         */
        public function update_rate_whn_as_option_unishippers_freight( $rate )
        {
            
            $this->unishippers_freight_as_option = get_option("unishippers_freight_liftgate_delivery_as_option");
            
            if(isset($rate) && (!empty($rate))){
                $rate = apply_filters("en_woo_addons_web_quotes" , $rate , en_woo_plugin_unishippers_freight);
                
                $label_sufex = (isset($rate['label_sufex'])) ? $rate['label_sufex'] : array();  
                $label_sufex = $this->label_R_unishippers_freight($label_sufex);
                $rate['label_sufex'] = $label_sufex;
                
                if(isset($this->unishippers_freight_as_option,$rate['grandTotalWdoutLiftGate']) && 
                        ($this->unishippers_freight_as_option == "yes") && ($rate['grandTotalWdoutLiftGate'] > 0))
                {
                    $lift_resid_flag = get_option( 'en_woo_addons_liftgate_with_auto_residential' );

                    if(isset($lift_resid_flag) && 
                            ( $lift_resid_flag == "yes" ) && 
                            (in_array("R", $label_sufex)))
                    {
                        return $rate;
                    }
                    
                    $wdout_lft_gte = $rate;
                    $rate['append_label'] = " with lift gate delivery ";
                    (!empty($label_sufex)) ? array_push($rate['label_sufex'], "L") : $rate['label_sufex'] = array("L");
                    ((!empty($label_sufex)) && (in_array("R", $wdout_lft_gte['label_sufex']))) ? $wdout_lft_gte['label_sufex'] = array("R") : $wdout_lft_gte['label_sufex'] = array();
                    
                    $rate = array($rate , $wdout_lft_gte);
                }
            }
            return $rate;
        }
        
        /**
         * filter label from api response
         * @param array type $result
         * @return aray type
         */
        public function filter_label_sufex_array_unishippers_freight($result)
        {
            $this->label_sfx_arr = ($this->label_sfx_arr) ? $this->label_sfx_arr : array();
            $this->check_residential_status( $result );
            (isset($result->residentialStatus) && ($result->residentialStatus == "r")) ? array_push($this->label_sfx_arr, "R") : "";
            (isset($result->liftGateStatus) && ($result->liftGateStatus == "l")) ? array_push($this->label_sfx_arr, "L") : "";
            return array_unique($this->label_sfx_arr);
        }

        /**
         * check and update residential status
         * @param array type $result
         */
        public function check_residential_status( $result )
        {
            $residential_detecion_flag          = get_option("en_woo_addons_auto_residential_detecion_flag");
            $auto_renew_plan                    = get_option("auto_residential_delivery_plan_auto_renew");
          
            if(($auto_renew_plan == "disable") && 
                    ($residential_detecion_flag == "yes") && 
                    (isset($result->autoResidentialSubscriptionExpired)) &&
                    ( $result->autoResidentialSubscriptionExpired == 1 ))
            {
                update_option("en_woo_addons_auto_residential_detecion_flag" , "no");
            }
        }
        
        /**
         * check "R" in array
         * @param array type $label_sufex
         * @return array type
         */
        public function label_R_unishippers_freight($label_sufex)
        {
            if(get_option('wc_settings_unishipper_residential_delivery ') == 'yes' && (in_array("R", $label_sufex)))
            {
                $label_sufex = array_flip($label_sufex);
                unset($label_sufex['R']);
                $label_sufex = array_keys($label_sufex);

            }
            
            return $label_sufex;
        }
    }
    
    new Uinshipper_Ltl_Liftgate_As_Option();
}