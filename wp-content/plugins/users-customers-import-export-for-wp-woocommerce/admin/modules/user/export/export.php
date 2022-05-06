<?php

if (!defined('WPINC')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_basic_User_Export')){
class Wt_Import_Export_For_Woo_basic_User_Export {

    public $parent_module = null;
	

	public function __construct($parent_object) {

        $this->parent_module = $parent_object;
    }

    public function prepare_header() {

        $export_columns = $this->parent_module->get_selected_column_names();

        return apply_filters('hf_csv_customer_post_columns', $export_columns);
    }

    /**
     * Prepare data that will be exported.
     */
    public function prepare_data_to_export($form_data, $batch_offset) {
        
        $export_user_roles = !empty($form_data['filter_form_data']['wt_iew_roles']) ? $form_data['filter_form_data']['wt_iew_roles'] : array();
        $export_sortby = !empty($form_data['filter_form_data']['wt_iew_sort_columns']) ? $form_data['filter_form_data']['wt_iew_sort_columns'] : array('user_login');
        $export_sort_order = !empty($form_data['filter_form_data']['wt_iew_order_by']) ? $form_data['filter_form_data']['wt_iew_order_by'] : 'ASC';
        $user_ids = !empty($form_data['filter_form_data']['wt_iew_email']) ? $form_data['filter_form_data']['wt_iew_email'] : array(); // user email fields return user ids
        $export_start_date = !empty($form_data['filter_form_data']['wt_iew_date_from']) ? $form_data['filter_form_data']['wt_iew_date_from'] : '';
        $export_end_date = !empty($form_data['filter_form_data']['wt_iew_date_to']) ? $form_data['filter_form_data']['wt_iew_date_to'] : '';

		$v_export_guest_user = ( !empty( $form_data['advanced_form_data']['wt_iew_export_guest_user'] ) && ( 'Yes' === $form_data['advanced_form_data']['wt_iew_export_guest_user'] || $form_data['advanced_form_data']['wt_iew_export_guest_user'] == 1 ) ) ? true : false;
		
        $export_limit = !empty($form_data['filter_form_data']['wt_iew_limit']) ? intval($form_data['filter_form_data']['wt_iew_limit']) : 999999999; //user limit
        $current_offset = !empty($form_data['filter_form_data']['wt_iew_offset']) ? intval($form_data['filter_form_data']['wt_iew_offset']) : 0; //user offset
        $batch_count = !empty($form_data['advanced_form_data']['wt_iew_batch_count']) ? $form_data['advanced_form_data']['wt_iew_batch_count'] : Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings('default_export_batch');
        

        $real_offset = ($current_offset + $batch_offset);

        if($batch_count<=$export_limit)
        {
            if(($batch_offset+$batch_count)>$export_limit) //last offset
            {
                $limit=$export_limit-$batch_offset;
            }else
            {
                $limit=$batch_count;
            }
        }else
        {
            $limit=$export_limit;
        }

        $data_array = array();
        if ($batch_offset < $export_limit)
        {

            $sortby_check = array_intersect($export_sortby, array('ID', 'user_registered', 'user_email', 'user_login', 'user_nicename'));
            if (empty($sortby_check)) {
                $wt_export_sortby = $export_sortby[0];
                $args = array(
                    'fields' => 'ID', // exclude standard wp_users fields from get_users query -> get Only ID##
                    'role__in' => $export_user_roles, //An array of role names. Matched users must have at least one of these roles. Default empty array.
                    'number' => $limit,
                    'offset' => $real_offset,
                    'orderby' => 'meta_value',
                    'meta_key' => $wt_export_sortby,
                    'order' => $export_sort_order,
                    'date_query' => array(
                        array(
                            'after' => $export_start_date,
                            'before' => $export_end_date,
                            'inclusive' => true
                        )),
                );
            } else {

                $args = array(
                    'fields' => 'ID', // exclude standard wp_users fields from get_users query -> get Only ID##
                    'role__in' => $export_user_roles, //An array of role names. Matched users must have at least one of these roles. Default empty array.
                    'number' => $limit,
                    'offset' => $real_offset,
                    'orderby' => $export_sortby,
                    'order' => $export_sort_order,
                    'date_query' => array(
                        array(
                            'after' => $export_start_date,
                            'before' => $export_end_date,
                            'inclusive' => true
                        )),
                );
            }
            if (!empty($user_ids)) {
                $args['include'] = $user_ids;
            }


            $users = get_users($args);

            /**
            *   taking total records
            */
            $total_records=0;
            if($batch_offset==0) //first batch
            {
                $total_item_args=$args;
                $total_item_args['fields'] = 'ids';  
                $total_item_args['number']=$export_limit; //user given limit
                $total_item_args['offset']=$current_offset; //user given offset
                $total_record_count = get_users($total_item_args);                
                $total_records=count($total_record_count);
				set_transient( 'wt_total_order_count', $total_records, 60*60*1); // 1 hour
            }
			
			
            // Loop users
            foreach ($users as $user) {
                $data = self::get_customers_csv_row($user);
                $data_array[] = apply_filters('hf_customer_csv_exclude_admin', $data);
            }
			
			$is_last_offset = false;
			$last_batch_count = $real_offset + $batch_count;
			if($last_batch_count>=get_transient('wt_total_order_count')) //finished
			{
				$is_last_offset=true;
				delete_transient('wt_total_order_count');
			}
			if($is_last_offset) //last batch
			{
			if ($v_export_guest_user) {
                $query_args = array(
                    'fields' => 'ids',
                    'post_type' => 'shop_order',
                    'post_status' => 'any',
                    'posts_per_page' => -1,
                );
                $query_args['meta_query'] = array(array(
                        'key' => '_customer_user',
                        'value' => 0,
                        'compare' => '',
                ));
                $query = new WP_Query($query_args);

                $order_ids = $query->posts;
				$guest_email_list = array();
                foreach ($order_ids as $order_id) {
                    $order = new WC_Order($order_id);
					if($order->get_billing_email()){
						$user = get_user_by('email', $order->get_billing_email());
						if (!isset($user->ID)) {
							if(!in_array($order->get_billing_email(), $guest_email_list)){
								$data = self::get_guest_customers_csv_row($order);
								$data_array[] = apply_filters('hf_customer_csv_exclude_admin', $data);
								$guest_email_list[] = $order->get_billing_email();
							}
						}
					}
                }
            }
			}
			

            $return['total'] = $total_records;
            $return['data'] = $data_array;
            return $return;
        }
    }

    public function get_customers_csv_row($id) {
        global $wpdb;
        $csv_columns = $this->parent_module->get_selected_column_names();
        
        $user = get_user_by('id', $id);
        
        $customer_data = array();
        foreach ($csv_columns as $key => $value) {

            $key = trim(str_replace('meta:', '', $key));
            if ($key == 'roles') {
                $user_roles = (!empty($user->roles)) ? $user->roles : array();
                $customer_data['roles'] = implode(', ', $user_roles);
                continue;
            }            
            if ($key == 'customer_id') {
                $customer_data[$key] = !empty($user->ID) ? maybe_serialize($user->ID) : '';
                continue;
            }            
            if ($key == 'session_tokens') {
                $customer_data[$key] = !empty($user->{$key}) ? base64_encode(json_encode(maybe_unserialize($user->{$key}))) : '';
                continue;
            }            
            if($key == $wpdb->prefix.'user_level'){
                $customer_data[$key] = (!empty($user->{$key})) ? $user->{$key} : 0;
                continue;
            }

            $customer_data[$key] = isset($user->{$key}) ? maybe_serialize($user->{$key}) : '';
        }
        /*
         * CSV Customer Export Row.
         * Filter the individual row data for the customer export
         * @since 3.0
         * @param array $customer_data 
         */
        return apply_filters('hf_customer_csv_export_data', $customer_data, $csv_columns);
    }

	/**
	 * CSV Guest Customer Export Row
	 *
	 * @param WC_Order $order Order object.
	 * @return array $customer_data
	 */
    public function get_guest_customers_csv_row($order) {
        $customer_data = array();
        $csv_columns = $this->parent_module->get_selected_column_names();
        $key_array = array('user_email', 'billing_first_name', 'billing_last_name', 'billing_company', 'billing_email', 'billing_phone', 'billing_address_1', 'billing_address_2', 'billing_postcode', 'billing_city', 'billing_state', 'billing_country', 'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_phone', 'shipping_address_1', 'shipping_address_2', 'shipping_postcode', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_method');
        foreach ( $csv_columns as $key ) {
			$data = '';
            if ( in_array( $key, $key_array ) ) {
                if ( 'user_email' === $key ) {
                    $customer_data[$key] = $order->get_billing_email();
                    continue;
                }
                $method_name = "get_{$key}";
				if( is_callable( array( $order, $method_name ) ) ){
					$data = $order->$method_name();
				}
                if ( !empty( $data ) ) {
                    $data = maybe_serialize( $order->$method_name() );
                } else {
                    $data = '';
                }
                $customer_data[$key] = $data;
            } else {
                $customer_data[$key] = '';
            }
        }

        /*
         * CSV Guest Customer Export Row.
         * Filter the individual row data for the Guest customer export
         * @since 3.0
         * @param array $customer_data 
         */
        return apply_filters('wt_guest_customer_export_data', $customer_data, $csv_columns);
    }

}
}
