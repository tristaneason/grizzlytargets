<?php

if (!defined('WPINC')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_basic_User_Import')){
class Wt_Import_Export_For_Woo_basic_User_Import {

    public $parent_module = null;
    public $parsed_data = array();
         
    
    var $merge;
    
    public $merge_with = 'id';
    public $found_action = 'skip';
    public $id_conflict = 'skip';
    
    public $is_user_exist = false;
    
    // Results
    var $import_results = array();
    
    
    var $row;
    var $post_defaults;		// Default post data
    var $postmeta_defaults;		// default post meta
    var $postmeta_allowed;		// post meta validation

    public function __construct($parent_object) {

        $this->parent_module = $parent_object;
        $this->user_all_fields = include plugin_dir_path( __FILE__).'../data/data-user-columns.php';
        $this->user_base_fields  = array_slice($this->user_all_fields, 0, 13);
        $this->user_meta_fields = array_slice($this->user_all_fields, 13);  
        
        
    }
    
    public function hf_log_data_change($content = 'review-csv-import', $data = '') {
        Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', $data);
    }
    
    public function prepare_data_to_import($import_data,$form_data, $batch_offset, $is_last_batch){

        $this->merge_with = !empty($form_data['advanced_form_data']['wt_iew_merge_with']) ? $form_data['advanced_form_data']['wt_iew_merge_with'] : 'email'; 
        $this->found_action = !empty($form_data['advanced_form_data']['wt_iew_found_action']) ? $form_data['advanced_form_data']['wt_iew_found_action'] : 'skip'; 
        $this->use_same_password = isset($form_data['advanced_form_data']['wt_iew_use_same_password']) ? $form_data['advanced_form_data']['wt_iew_use_same_password'] : 1; 
        
        
        wp_defer_term_counting(true);
        wp_defer_comment_counting(true);
        wp_suspend_cache_invalidation(true);
        
        Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Preparing for import.");
        
        $success = 0;
        $failed = 0;
        $msg = 'User imported successfully.';
        foreach ($import_data as $key => $data) {
           $row = $batch_offset+$key+1;
           
           Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing item.");                      
           $parsed_data = $this->parse_users($data); 
           
           if (!is_wp_error($parsed_data)){
               Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing item.");
               
                $result = $this->process_users($parsed_data,$data);
                
                if(!is_wp_error($result)){                                                            
                    if($this->is_user_exist){
                        $msg = 'User updated successfully.';
                    }
                    $this->import_results[$row] = array('row'=>$row, 'message'=>$msg, 'status'=>true, 'post_id'=>$result['id']); 
                    Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - ".$msg);
                    $success++;                     
                }else{
                   $this->import_results[$row] = array('row'=>$row, 'message'=>$result->get_error_message(), 'status'=>false, 'post_id'=>'');
                   Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing failed. Reason: ".$result->get_error_message());
                   $failed++;
                }  
           }else{
               $this->import_results[$row] = array('row'=>$row, 'message'=>$parsed_data->get_error_message(), 'status'=>false, 'post_id'=>'');
               Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing failed. Reason: ".$parsed_data->get_error_message());
                $failed++; 
           }           
            unset($data, $parsed_data);            
        }
        wp_suspend_cache_invalidation(false);
        wp_defer_term_counting(false);
        wp_defer_comment_counting(false);
        
        $import_response=array(
                'total_success'=>$success,
                'total_failed'=>$failed,
                'log_data'=>$this->import_results,
            );
        
        return $import_response;  
                             
    }
    
    /**
     * Parse users
     * @param  array  $item
     * @return array
     */     
    public function parse_users( $data ) {
        
        try{
            $data = apply_filters('wt_user_importer_pre_parse_data', $data); 
            $item = $data['mapping_fields'];
            foreach ($data['meta_mapping_fields'] as $value) {
                $item = array_merge($item,$value);            
            }
            global $wpdb;
            
            $this->is_user_exist = false;
            $this->merge = false; 
            
            $id = $user_id = isset($item['ID']) && !empty($item['ID']) ? absint($item['ID']) : 0;         
            $id_found_with_id = '';
            if($id && 'id' == $this->merge_with){                                 
                $id_found_with_id = get_userdata($id);
                if ($id_found_with_id) {
                    $this->is_user_exist = true;
                    $user_id = $id;
                }          
            } 

            $email = isset($item['user_email']) && '' != $item['user_email'] ? trim($item['user_email']) : '';
            $id_found_with_email = '';
            if(!empty($email) && 'email' == $this->merge_with){   

                if (is_email($email) && false !== email_exists($email)) {
                    $id_found_with_email = email_exists($email);
                    $this->is_user_exist = true;
                    $user_id = $id_found_with_email;
                }
                                 
            }
            
            $username = isset($item['user_login']) && '' != $item['user_login'] ? trim($item['user_login']) : '';
            $id_found_with_username = '';
            if(!empty($username) && 'username' == $this->merge_with){  
                
                if (false !== username_exists($username)) {
                    $id_found_with_username = username_exists($username);
                    $this->is_user_exist = true;
                    $user_id = $id_found_with_username;
                    

                }
                                  
            }
            
            if($this->is_user_exist){                
                if('skip' == $this->found_action){
                    if($id && $id_found_with_id ){
                        throw new Exception(sprintf('User with same ID already exists. ID: %d',$id ));
                    }elseif($email && $id_found_with_email ){
                        throw new Exception(sprintf('User with same Email already exists. Email: %s',$email ));
                    }elseif($username && $id_found_with_username ){
                        throw new Exception(sprintf('User with same Username already exists. Username: %s',$username ));
                    }else{
                        throw new Exception('User already exists.');
                    }                 
                }elseif('update' == $this->found_action){
                    $this->merge = true;                     
                }
            }

//            if(!$this->is_user_exist && $this->skip_new){
//                throw new Exception('Skipping new item' );
//            } 
             
            if (!$this->is_user_exist) {
                    $create_user_without_email = apply_filters('wt_create_user_without_email', FALSE);  // create user without email address
                    if (empty($email) && $create_user_without_email === FALSE) {
                        $this->hf_log_data_change('user-csv-import', __('> skipped: cannot insert user without email.'));
                        unset($item);
                        return new WP_Error('parse-error', __('> skipped: cannot insert user without email.'));
                    } elseif (!is_email($email) && $create_user_without_email === FALSE) {
                        $this->hf_log_data_change('user-csv-import', sprintf(__('> skipped: Email is not valid. %s'), $item['user_email']));
                        unset($item);
                        return new WP_Error('parse-error', __('>  skipped: Email is not valid.'));
                    }
            }

            $user_meta = $user_details = array();
            
            $user_details['ID'] = $user_id;
                                    
            foreach ($this->user_base_fields as $key => $value) {
                if(in_array($key, array('ID'))){  // ID alreay set
                    continue;
                }                
                if(isset($item[$value]))
                    $user_details[$key] = trim($item[$value]);
                //$user_details[$key] = isset( $item[$value] ) ? $item[$value] : "" ;
            }

            foreach ($this->user_meta_fields as $key => $value){
                $user_meta[] = array( 'key' => $key, 'value' => isset( $item[$key] ) ? trim($item[$key]) : "" );
            }

            // the $user_details array will now contain the necessary name-value pairs for the wp_users table, and also any meta data in the 'usermeta' array
            $parsed_details = array();

            $parsed_details['user_details'] = $user_details;
            $parsed_details['user_meta'] = $user_meta;

            return $parsed_details;
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }
    }
        
        
    /**
     * Create new posts based on import information
     */
    private function process_users($post, $parsed_item) {
        try{            
            global $wpdb;

            $this->hf_log_data_change('user-csv-import', __('Processing users.'));

            $user_id = !empty($post['user_details']['ID']) && $post['user_details']['ID'] ? $post['user_details']['ID'] : 0;  
            
            //  user exists when importing &  merge not ticked
            $new_added = false;

            if ($user_id && $this->merge) {
                $current_user = get_current_user_id();
                if ($current_user == $user_id) {
                    $usr_msg = 'This user is currently logged in hence we cannot update.';
                    $this->hf_log_data_change('user-csv-import', sprintf(__('> &#8220;%s&#8221;' . $usr_msg), $user_id), true);
                    unset($post);
                return new WP_Error( 'parse-error',sprintf(__('> &#8220;%s&#8221;' . $usr_msg), $user_id));
                }
                $user_id = $this->hf_update_customer($user_id, $post);
            } else {

                $user_id = $this->hf_create_customer($post);
                $new_added = true;
                if (is_wp_error($user_id)) {

                    $this->hf_log_data_change('user-csv-import', sprintf(__('> Error inserting %s: %s'), 1, $user_id->get_error_message()), true);
                    //$skipped++;
                    unset($post);
                        return new WP_Error( 'parse-error',sprintf(__('> Error inserting %s: %s'), 1, $user_id->get_error_message()));
                } elseif (empty($user_id)) {
                    
                    //$skipped++;
                    unset($post);
                    return new WP_Error( 'parse-error',__('An error occurred with the customer information provided.'));
                }
            }

            if ($this->merge && !$new_added)
                $out_msg = "User updated successfully. ID:$user_id";
            else
                $out_msg = "User imported successfully. ID:$user_id";
            
            $this->hf_log_data_change('user-csv-import', sprintf(__('> &#8220;%s&#8221;' . $out_msg), $user_id), true);
            
            $this->hf_log_data_change('user-csv-import', sprintf(__('> Finished importing user %s'), $user_id ));
            
            do_action('wt_customer_csv_import_data', $parsed_item, $user_id);
            unset($post);
            return $result =  array(
                'id' => $user_id,
                'updated' => $this->merge,
            );
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }
    }

    public function hf_create_customer($data) { 
        $customer_email = (!empty($data['user_details']['user_email']) ) ? $data['user_details']['user_email'] : '';
        $username = (!empty($data['user_details']['user_login']) ) ? $data['user_details']['user_login'] : '';
        $customer_id = (!empty($data['user_details']['customer_id']) ) ? $data['user_details']['customer_id'] : '';
        $insertion_id = (!empty($data['user_details']['ID']) ) ? $data['user_details']['ID'] : '';
        $merge_empty_cells = apply_filters('wt_user_import_empty_csv_column', FALSE); 
        
		if('email' == $this->merge_with){
			$insertion_id = '';
		}
               
        if (!empty($data['user_details']['user_pass'])) {
            $password = ($this->use_same_password) ? $data['user_details']['user_pass'] : wp_hash_password($data['user_details']['user_pass']);
            $password_generated = false;
        } else {
            $password = wp_generate_password(12, true);
            $password_generated = true;            
        }
        $found_customer = false;
        $create_user_without_email = apply_filters('wt_create_user_without_email', FALSE);   // create user without email address
        if (is_email($customer_email) || $create_user_without_email === TRUE) {
            $maybe_username = $username;
            // Not in test mode, create a user account for this email
            if (empty($username)) {
                $maybe_username = explode('@', $customer_email);
                $maybe_username = sanitize_user($maybe_username[0]);
            }
            $counter = 1;
            $username = $maybe_username;
            while (username_exists($username)) {
                $username = $maybe_username . $counter;
                $counter++;
            }                        
              
            if (!$this->is_user_exist && $insertion_id) {
                global $wpdb;
                $insertion_id = (int) $insertion_id;
                $username = sanitize_user($username);
                $customer_email = sanitize_email($customer_email);
                if($password_generated && !$this->use_same_password){
                    $password = wp_hash_password($password);
                }
                $result = $wpdb->insert($wpdb->users, array('ID' => $insertion_id, 'user_login' => $username, 'user_email' => $customer_email, 'user_pass' => $password));
                if ($result) {
                    $found_customer = $insertion_id;
                }
                
            }
            if(!$found_customer) { 
                $found_customer = wp_create_user($username, $password, $customer_email);
            }
            $user_data = array('ID' => $found_customer, 'user_login' => $username, 'user_email' => $customer_email);
            if (!$password_generated) {
                $user_data['user_pass'] = $password;
            }
                        
            if (!is_wp_error($found_customer)) {
                wp_insert_user($user_data);
                $wp_user_object = new WP_User($found_customer);
                 if ( ! function_exists( 'get_editable_roles' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/user.php';
                }
                $roles = get_editable_roles();
                $new_roles_str = str_replace(' ', '', strtolower(isset($data['user_details']['roles']) ? $data['user_details']['roles'] : ''));

                if(empty($new_roles_str)){
                    $new_roles_str = get_option('default_role');
                }

                $new_roles = explode(',', $new_roles_str);
                $new_roles = array_intersect($new_roles, array_keys($roles));
                $roles_to_remove = array();
                $user_roles = array_intersect(array_values($wp_user_object->roles), array_keys($roles));
                if (!$new_roles) {
                    // If there are no roles, delete all of the user's roles
                    $roles_to_remove = $user_roles;
                } else {
                    $roles_to_remove = array_diff($user_roles, $new_roles);
                }
                array_push($roles_to_remove, 'subscriber');
                if (!empty($new_roles)) {
                    foreach ($roles_to_remove as $_role) {
                        $wp_user_object->remove_role($_role);   //remove the default role before adding new roles
                    }
                    // Make sure that we don't call $wp_user_object->add_role() any more than it's necessary
                    $_new_roles = array_diff($new_roles, array_intersect(array_values($wp_user_object->roles), array_keys($roles)));
                    foreach ($_new_roles as $_role) {
                        $wp_user_object->add_role($_role);
                    }
                }
                $meta_array = array();
                foreach ($data['user_meta'] as $meta) {
                    $meta_array[$meta['key']] = $meta['value'];
                }

                $user_nicename = (!empty($data['user_details']['user_nicename'])) ? $data['user_details']['user_nicename'] : '';
                $website = (!empty($data['user_details']['user_url'])) ? $data['user_details']['user_url'] : '';
                $user_registered = (!empty($data['user_details']['user_registered'])) ? $data['user_details']['user_registered'] : '';
                $display_name = (!empty($data['user_details']['display_name'])) ? $data['user_details']['display_name'] : '';
                $first_name = (!empty($data['user_details']['first_name'])) ? $data['user_details']['first_name'] : '';
                $last_name = (!empty($data['user_details']['last_name'])) ? $data['user_details']['last_name'] : '';
                $user_status = (!empty($data['user_details']['user_status'])) ? $data['user_details']['user_status'] : '';
                wp_update_user(array(
                    'ID' => $found_customer,
                    'user_nicename' => $user_nicename,
                    'user_url' => $website,
                    'user_registered' => $user_registered,
                    'display_name' => $display_name,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'user_status' => $user_status,
                        )
                );

                //unset($this->user_meta_fields['role']);
                // update user meta data
                global $wpdb;
                foreach ($this->user_meta_fields as $key => $meta) {
                    if (($key == $wpdb->prefix.'capabilities') || (in_array($key, $this->user_base_fields))) {                        continue;
                    }
                    $key = trim(str_replace('meta:', '', $key));
//                    $meta = trim(str_replace('meta:', '', $meta));
                    $meta_value = (!empty($meta_array[$key]) ) ? maybe_unserialize($meta_array[$key]) : '';
                    if ($key == $wpdb->prefix.'user_level' && $meta_value == ''){
                        $meta_value = 0;
                    }
                    if ('session_tokens' == $key) {
                        if (!empty($meta_array[$key]) ) {
                            $session_json = base64_decode($meta_array[$key]);
                            $meta_value = json_decode($session_json, true);
                        }
                    }
                    if (empty($meta_value) && !$merge_empty_cells) {
                        continue;
                    }
                    update_user_meta($found_customer, $key, $meta_value);
                }

            }
        } else {
            $found_customer = new WP_Error('hf_invalid_customer', sprintf(__('User could not be created without Email.'), $customer_id));
        }
        return apply_filters('xa_user_impexp_alter_user_meta', $found_customer, $this->user_meta_fields, $meta_array);
    }

    public function hf_update_customer($found_customer, $data) { 
        $meta_array = array();
        if ($found_customer) {
            $wp_user_object = new WP_User($found_customer);                                    
            $roles = get_editable_roles();
			$new_rolse_input = isset($data['user_details']['roles']) ? strtolower($data['user_details']['roles']) : '';
            $new_roles_str = str_replace(' ', '', $new_rolse_input);
            $new_roles = explode(',', $new_roles_str);
            $new_roles = array_intersect($new_roles, array_keys($roles));
            $roles_to_remove = array();
            $user_roles = array_intersect(array_values($wp_user_object->roles), array_keys($roles));
            $merge_empty_cells = apply_filters('wt_user_import_empty_csv_column', FALSE); 
            if (!$new_roles) {
                // If there are no roles, delete all of the user's roles
                $roles_to_remove = $user_roles;
            } else {
                $roles_to_remove = array_diff($user_roles, $new_roles);
            }
            if (!empty($new_roles)) {
                foreach ($roles_to_remove as $_role) {
                    $wp_user_object->remove_role($_role);   //remove the default role before adding new roles
                }
                // Make sure that we don't call $wp_user_object->add_role() any more than it's necessary
                $_new_roles = array_diff($new_roles, array_intersect(array_values($wp_user_object->roles), array_keys($roles)));
                foreach ($_new_roles as $_role) {
                    $wp_user_object->add_role($_role);
                }
            }

            foreach ($data['user_meta'] as $meta) {
                $meta_array[$meta['key']] = $meta['value'];
            }
            // update user meta data
            global $wpdb;
            foreach ($this->user_meta_fields as $key => $meta) {
                if (($key == $wpdb->prefix.'capabilities') || (in_array($key, $this->user_base_fields))) {                    continue;
                }
                $key = trim(str_replace('meta:', '', $key));
//                    $meta = trim(str_replace('meta:', '', $meta));
                $meta_value = (!empty($meta_array[$key]) ) ? maybe_unserialize($meta_array[$key]) : '';
                if ($key == $wpdb->prefix.'user_level' && $meta_value == ''){
                    $meta_value = 0;
                }
                if (empty($meta_value) && !$merge_empty_cells) {
                    continue;
                }
                update_user_meta($found_customer, $key, $meta_value);
            }

            $user_data = array(
                'ID' => $found_customer
            );
            if (isset($data['user_details']['user_nicename'])) {
                $user_data['user_nicename'] = $data['user_details']['user_nicename'];
            }

			$email_updated = false;
            //added when implement merge with user id use for update email
            if (isset($data['user_details']['user_email']) && !empty($data['user_details']['user_email'])) {
                if ($wp_user_object->user_email != $data['user_details']['user_email']) {
                    $email_updated = true;
                    $customer_email = $data['user_details']['user_email'];
                }                
                $user_data['user_email'] = $data['user_details']['user_email'];
            }
            if (isset($data['user_details']['user_url'])) {
                $user_data['user_url'] = $data['user_details']['user_url'];
            }
            if (isset($data['user_details']['user_registered'])) {
                $user_data['user_registered'] = $data['user_details']['user_registered'];
            }
            if (isset($data['user_details']['user_pass'])) {
                $user_data['user_pass'] = $data['user_details']['user_pass'];                
            }
            if (isset($data['user_details']['display_name'])) {
                $user_data['display_name'] = $data['user_details']['display_name'];
            }
            if (isset($data['user_details']['first_name'])) {
                $user_data['first_name'] = $data['user_details']['first_name'];
            }
            if (isset($data['user_details']['last_name'])) {
                $user_data['last_name'] = $data['user_details']['last_name'];
            }
            if (isset($data['user_details']['user_status'])) {
                $user_data['user_status'] = $data['user_details']['user_status'];
            }
            add_filter('send_password_change_email', '__return_false'); // for preventing sending password change notification mail on by wp_update_user.
            if (sizeof($user_data) > 1) {                               
                wp_update_user($user_data);
            }    

            if ($this->use_same_password && isset($data['user_details']['user_pass']) && !empty($data['user_details']['user_pass'])) {
                $password = $data['user_details']['user_pass'];
                $user_data = array_merge($user_data, array('user_login' => $wp_user_object->user_login, 'user_email' => ( $email_updated ) ? $customer_email : $wp_user_object->user_email, 'user_url' =>$wp_user_object->user_url));                
                $user_data['user_pass'] = $password; 
                $user_default_meta = array('nickname','first_name','last_name','display_name','description','rich_editing','syntax_highlighting','comment_shortcuts','admin_color','use_ssl','show_admin_bar_front','locale');
                foreach ($user_default_meta as $meta_key => $meta) {
                    $user_data[$meta] = $wp_user_object->$meta;
                }
                wp_insert_user($user_data);
            }

        } else {
            $found_customer = new WP_Error('hf_invalid_customer', sprintf(__('User could not be found with given Email or username.'), $customer_email));
        }        
        return apply_filters('xa_user_impexp_alter_user_meta', $found_customer, $this->user_meta_fields, $meta_array);
    }
}
}
