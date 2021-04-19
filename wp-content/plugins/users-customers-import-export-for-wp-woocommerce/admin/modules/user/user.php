<?php
/**
 * Product section of the plugin
 *
 * @link         
 *
 * @package  Wt_Import_Export_For_Woo 
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_basic_User')){
class Wt_Import_Export_For_Woo_basic_User {

    public $module_id = '';
    public static $module_id_static = '';
    public $module_base = 'user';
    public $module_name = 'User Import Export for WordPress/WooCommerce';
    public $min_base_version= '1.0.0'; /* Minimum `Import export plugin` required to run this add on plugin */

    private $all_meta_keys = array();
    private $found_meta = array();
    private $found_hidden_meta = array();
    private $selected_column_names = null;

    public function __construct()
    {
        /**
        *   Checking the minimum required version of `Import export plugin` plugin available
        */
        if(!Wt_Import_Export_For_Woo_Basic_Common_Helper::check_base_version($this->module_base, $this->module_name, $this->min_base_version))
        {
            return;
        }


        $this->module_id = Wt_Import_Export_For_Woo_Basic::get_module_id($this->module_base);
        
        self::$module_id_static = $this->module_id;
        
        add_filter('wt_iew_exporter_post_types_basic', array($this, 'wt_iew_exporter_post_types'), 10, 1);
        add_filter('wt_iew_importer_post_types_basic', array($this, 'wt_iew_exporter_post_types'), 10, 1);

        add_filter('wt_iew_exporter_alter_filter_fields_basic', array($this, 'exporter_alter_filter_fields'), 10, 3);
        
        add_filter('wt_iew_exporter_alter_mapping_fields_basic', array($this, 'exporter_alter_mapping_fields'), 10, 3);        
        add_filter('wt_iew_importer_alter_mapping_fields_basic', array($this, 'get_importer_post_columns'), 10, 3);  
        
        add_filter('wt_iew_importer_alter_advanced_fields_basic', array($this, 'importer_alter_advanced_fields'), 10, 3);

        add_filter('wt_iew_exporter_alter_meta_mapping_fields_basic', array($this, 'exporter_alter_meta_mapping_fields'), 10, 3);

        add_filter('wt_iew_exporter_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);
        add_filter('wt_iew_importer_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);

        add_filter('wt_iew_exporter_do_export_basic', array($this, 'exporter_do_export'), 10, 7);
        add_filter('wt_iew_importer_do_import_basic', array($this, 'importer_do_import'), 10, 8);

        add_filter('wt_iew_importer_steps_basic', array($this, 'importer_steps'), 10, 2);                
        
    }

    /**
    *   Altering advanced step description
    */
    public function importer_steps($steps, $base)
    {
        if($this->module_base==$base)
        {
            $steps['advanced']['description']=__('Use advanced options from below to retain user passwords or not, set batch import count. You can also save the template file for future imports.');
        }
        return $steps;
    }
    
    public function importer_do_import($import_data, $base, $step, $form_data, $selected_template_data, $method_import, $batch_offset, $is_last_batch) {                
        if ($this->module_base != $base) {
            return $import_data;
        }
                        
        if(0 == $batch_offset){                        
            $memory = size_format(wt_let_to_num_basic(ini_get('memory_limit')));
            $wp_memory = size_format(wt_let_to_num_basic(WP_MEMORY_LIMIT));                      
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ New import started at '.date('Y-m-d H:i:s').' ] PHP Memory: ' . $memory . ', WP Memory: ' . $wp_memory);
        }
        
        include plugin_dir_path(__FILE__) . 'import/import.php';
        $import = new Wt_Import_Export_For_Woo_basic_User_Import($this);
        
        $response = $import->prepare_data_to_import($import_data,$form_data, $batch_offset, $is_last_batch);
         
        if($is_last_batch){
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ Import ended at '.date('Y-m-d H:i:s').']---');
        }
        
        return $response;
    }

    public function exporter_do_export($export_data, $base, $step, $form_data, $selected_template_data, $method_export, $batch_offset) {
        if ($this->module_base != $base) {
            return $export_data;
        }        

        switch ($method_export) {
            case 'quick':
                $this->set_export_columns_for_quick_export($form_data);  
                break;

            case 'template':
            case 'new':
                $this->set_selected_column_names($form_data);
                break;
            
            default:
                break;
        }
        
        include plugin_dir_path(__FILE__) . 'export/export.php';
        $export = new Wt_Import_Export_For_Woo_basic_User_Export($this);

        $header_row = $export->prepare_header();

        $data_row = $export->prepare_data_to_export($form_data, $batch_offset);

        $export_data = array(
            'head_data' => $header_row,
            'body_data' => $data_row['data'],
        );
        
        if(isset($data_row['total']) && !empty($data_row['total'])){
            $export_data['total'] = $data_row['total'];
        }

        return $export_data;
    }
    
    /*
     * Setting default export columns for quick export
     */
    
    public function set_export_columns_for_quick_export($form_data) {

        $post_columns = self::get_user_post_columns();

        $this->selected_column_names = array_combine(array_keys($post_columns), array_keys($post_columns));
        
        if (isset($form_data['method_export_form_data']['mapping_enabled_fields']) && !empty($form_data['method_export_form_data']['mapping_enabled_fields'])) {
            foreach ($form_data['method_export_form_data']['mapping_enabled_fields'] as $value) {
                $additional_quick_export_fields[$value] = array('fields' => array());
            }

            $export_additional_columns = $this->exporter_alter_meta_mapping_fields($additional_quick_export_fields, $this->module_base, array());
            foreach ($export_additional_columns as $value) {
                $this->selected_column_names = array_merge($this->selected_column_names, $value['fields']);
            }
        }
    }

    /**
     * Adding current post type to export list
     *
     */
    public function wt_iew_exporter_post_types($arr) {
        $arr['user'] = __('User/Customer');
        return $arr;
    }
    
    public static function get_user_sort_columns() {
        $sort_columns = array('ID'=>'ID', 'user_registered'=>'user_registered','user_email'=> 'user_email', 'user_login'=>'user_login', 'user_nicename'=>'user_nicename','user_url'=>'user_url');
        return apply_filters('wt_iew_export_user_sort_columns', $sort_columns);
    }
    
    public static function get_user_roles() {
        global $wp_roles;                                
        $roles = array();
        foreach ( $wp_roles->role_names as $role => $name ) {
            $roles[esc_attr( $role )] = esc_html( $name );
        }
        return apply_filters('wt_iew_export_user_roles', $roles);
    }


    public static function get_user_post_columns() {
        return include plugin_dir_path(__FILE__) . 'data/data-user-columns.php';
    }
    
    public function get_importer_post_columns($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $colunm = include plugin_dir_path(__FILE__) . 'data/data/data-wf-reserved-fields-pair.php';
//        $colunm = array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $arr); 
        return $colunm;
    }

    public function exporter_alter_mapping_enabled_fields($mapping_enabled_fields, $base, $form_data_mapping_enabled_fields) {        
        if ($base != $this->module_base) {
            return $mapping_enabled_fields;
        }
            $mapping_enabled_fields = array();
        
        return $mapping_enabled_fields;
    }

    public function exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }

        foreach ($fields as $key => $value) {
            switch ($key) {

                default:
                    break;
            }
        }

        return $fields;
    }
    
    
    public function importer_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $fields=$this->exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data);
        $out=array();
        foreach ($fields as $key => $value) 
        {
            $value['fields']=array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $value['fields']);
            $out[$key]=$value;
        }
        return $out;
    }


    public function wt_get_found_meta() {

        if (!empty($this->found_meta)) {
            return $this->found_meta;
        }

        // Loop products and load meta data
        $found_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.

        $all_meta_keys = $this->wt_get_all_meta_keys();

        $csv_columns = self::get_user_post_columns();
        
        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) == '_') || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_meta[] = $meta;
        }

        $found_meta = array_diff($found_meta, array_keys($csv_columns));

        $this->found_meta = $found_meta;
        return $this->found_meta;
    }

    

    public function wt_get_all_meta_keys() {

        if (!empty($this->all_meta_keys)) {
            return $this->all_meta_keys;
        }

        $all_meta_pkeys = self::get_all_metakeys();

        $this->all_meta_keys = $all_meta_pkeys;

        return $this->all_meta_keys;
    }

    /**
     * Get a list of all the meta keys for a post type. This includes all public, private,
     * used, no-longer used etc. They will be sorted once fetched.
     */
    public static function get_all_metakeys() {
        global $wpdb;
        return apply_filters('wt_alter_user_meta_data', $wpdb->get_col("SELECT distinct(meta_key) FROM $wpdb->usermeta WHERE meta_key NOT IN ('".$wpdb->prefix."capabilities')"));
    }
    
    
    
    public function wt_get_found_hidden_meta() {

        if (!empty($this->found_hidden_meta)) {
            return $this->found_hidden_meta;
        }

        // Loop products and load meta data
        $found_hidden_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.
                
        $all_meta_keys = $this->wt_get_all_meta_keys();
        $csv_columns = self::get_user_post_columns();
        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) != '_') || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_hidden_meta[] = $meta;
        }

        $found_hidden_meta = array_diff($found_hidden_meta, array_keys($csv_columns));

        $this->found_hidden_meta = $found_hidden_meta;
        return $this->found_hidden_meta;
    }

    public function set_selected_column_names($full_form_data) {

        if (is_null($this->selected_column_names)) {
            if (isset($full_form_data['mapping_form_data']['mapping_selected_fields']) && !empty($full_form_data['mapping_form_data']['mapping_selected_fields'])) {
                $this->selected_column_names = $full_form_data['mapping_form_data']['mapping_selected_fields'];
            }
            if (isset($full_form_data['meta_step_form_data']['mapping_selected_fields']) && !empty($full_form_data['meta_step_form_data']['mapping_selected_fields'])) {
                $export_additional_columns = $full_form_data['meta_step_form_data']['mapping_selected_fields'];
                foreach ($export_additional_columns as $value) {
                    $this->selected_column_names = array_merge($this->selected_column_names, $value);
                }
            }
        }

        return $full_form_data;
    }

    public function get_selected_column_names() {
        return $this->selected_column_names;
    }

    public function exporter_alter_mapping_fields($fields, $base, $mapping_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }

        $fields = self::get_user_post_columns();
        return $fields;
    }


    /**
     *  Customize the items in filter export page
     */
    public function exporter_alter_filter_fields($fields, $base, $filter_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }  
        $fields = array();
        /* altering help text of default fields */
        
        $fields['email'] = array(
            'label' => __('User Email'),
            'placeholder' => __('All User'),
            'field_name' => 'email',
            'sele_vals' => '',
            'help_text' => __('Input the customer emails separated by comma to export information pertaining to only these customers.'),            
            'validation_rule' => array('type'=>'text_arr')
        );        
        if(is_plugin_active('woocommerce/woocommerce.php'))
        {
            $fields['email']['help_text']=__('Input the customer email to export information pertaining to only these customers.');
            $fields['email']['type']='multi_select';
            $fields['email']['css_class']='wc-customer-search';
        }
        
        $fields['roles'] = array(
            'label' => __('User Roles'),
            'placeholder' => __('All Roles'),
            'field_name' => 'roles',
            'sele_vals' => self::get_user_roles(),
            'help_text' => __('Input specific roles to export information pertaining to all customers with the respective role/s.'),
            'type' => 'multi_select',
            'css_class' => 'wc-enhanced-select',
            'validation_rule' => array('type'=>'text_arr')
        );
        
        $fields['date_from'] = array(
            'label' => __('Customer Registration - From Date'),
            'placeholder' => __('Date'),
            'field_name' => 'date_from',
            'sele_vals' => '',
            'help_text' => __('Date on which the customer registered. Export customers registered on and after the specified date.'),
            'type' => 'text',
            'css_class' => 'wt_iew_datepicker',
        );
        
        $fields['date_to'] = array(
            'label' => __('Customer Registration - To Date'),
            'placeholder' => __('Date'),
            'field_name' => 'date_to',
            'sele_vals' => '',
            'help_text' => __('Export customers registered upto the specified date.'),
            'type' => 'text',
            'css_class' => 'wt_iew_datepicker',
        );
        
		$fields['limit'] = array(
            'label' => __('Total number of users to export'),
            'placeholder' => __('Unlimited'),
            'field_name' => 'limit',
            'help_text'=>__('Exports specified number of users. e.g. Entering 500 with a skip count of 10 will export users from 11th to 510th position.'),
            'type' => 'number',
            'value'=>'',
            'attr'=>array('step'=>1,'min_val'=>0),
            'validation_rule' => array('type'=>'absint')
        ); 
        
        $fields['offset'] = array(            
            'label' => __('Skip first <i>n</i> users'),
            'placeholder' => __('0'),
            'field_name' => 'offset',
            'help_text'=>__('Skips specified number of users from the beginning. e.g. Enter 10 to skip first 10 users from export.'),
            'type' => 'number', 
            'value'=>0,
            'attr'=>array('step'=>1,'min_val'=>0),
            'validation_rule' => array('type'=>'absint')
        );
                                     
        
        $fields['sort_columns'] = array(
                'label' => __('Sort Columns'),
                'placeholder' => __('user_login'),
                'field_name' => 'sort_columns',
                'sele_vals' => self::get_user_sort_columns(),
                'help_text' => __('Sort the exported data based on the selected columns in order specified. Defaulted to ascending order.'),
                'type' => 'multi_select',
                'css_class' => 'wc-enhanced-select',
                'validation_rule' => array('type'=>'text_arr')
            );

        $fields['order_by'] = array(
            'label' => __('Sort By'),
            'placeholder' => __('ASC'),
            'field_name' => 'order_by',
            'sele_vals' => array('ASC' => 'Ascending', 'DESC' => 'Descending'),
            'help_text' => __('Defaulted to Ascending. Applicable to above selected columns in the order specified.'),
            'type' => 'select',
        );

        return $fields;
    }
    
    
    public function exporter_alter_advanced_fields($fields, $base, $advanced_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }
        unset($fields['export_shortcode_tohtml']);
        $out = array();
        $out['export_guest_user'] = array(
            'label' => __("Export guest users"),
            'type' => 'radio',
            'radio_fields' => array(
                'Yes' => __('Yes'),
                'No' => __('No')
            ),
            'value' => 'No',
            'field_name' => 'export_guest_user',
            'help_text' => __('Enable this option to export information related to guest users'),
        );
        
        foreach ($fields as $fieldk => $fieldv) {
            $out[$fieldk] = $fieldv;
        }
        return $out;
    }
    
    public function importer_alter_advanced_fields($fields, $base, $advanced_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }
        $out = array();
        

        $out['found_action_merge'] = array(
            'label' => __("Existing user"),
            'type' => 'radio',
            'radio_fields' => array(
                'skip' => __('Skip'),                                
                'update' => __('Update'),
//                'import' => __('Import as new item'),                
            ),
            'value' => 'skip',
            'field_name' => 'found_action',
            'help_text_conditional'=>array(
                array(
                    'help_text'=> __('Retains the user in the store as is and skips the matching user from the input file.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'skip')
                    )
                ),
                array(
                    'help_text'=> __('Update user as per data from the input file'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'update')
                    )
                )
            ),
            'form_toggler'=>array(
                'type'=>'parent',
                'target'=>'wt_iew_found_action'
            )
        );  
          
             
        $out['use_same_password'] = array(
            'label' => __("Retain user passwords"),
            'type' => 'radio',
            'radio_fields' => array(
                '1' => __('Yes'),
                '0' => __('No')
            ),
            'value' => '1',
            'field_name' => 'use_same_password',
            'help_text' => __("Choose 'Yes' to migrate user passwords as is. Option 'No' will encrypt the password; use this option particularly while using a plain text(unencrypted) password."),
        );

        
        foreach ($fields as $fieldk => $fieldv) {
            $out[$fieldk] = $fieldv;
        }
        return $out;
    }
    
    public function get_item_by_id($id) {
        $post['edit_url']=get_edit_user_link($id);
        $user_info = get_userdata($id);
        $post['title'] = $user_info->user_login;        
        return $post; 
    }
    
}
}

new Wt_Import_Export_For_Woo_basic_User();
