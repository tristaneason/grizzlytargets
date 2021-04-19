<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Fired during plugin activation
 *
 * @link       http://myworks.design/software/wordpress/woocommerce/myworks-wc-qbo-sync
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 * @author     My Works <support@myworks.design>
 */

if( !defined( "QUICKBOOKS_BASEDIR" )){
	require_once plugin_dir_path( __FILE__ ) . 'lib/qbo-lib/QuickBooks.php';
	/*Extra Constants*/
	if( !defined( "QUICKBOOKS_ADD_GUEST" )){
		define('QUICKBOOKS_ADD_GUEST','GuestAdd');
	}
	
	if( !defined( "QUICKBOOKS_ADD_SALESRECEIPT" )){
		define('QUICKBOOKS_ADD_SALESRECEIPT','SalesReceiptAdd');
	}	
}

require_once plugin_dir_path( __FILE__ ) . 'lib/simple-http-client-master/lib/SimpleHTTPClient.php';

class MW_QBO_Desktop_Sync_Lib {
	protected $dsn;
	private $qwc_user_created;

	protected $quickbooks_connection_dashboard_url='http://myworks.software/account';
	protected $plugin_license_status = '';

	protected $mw_wc_qbo_sync_plugin_options_desk;
	
	protected $qb_username;
	
	protected $server_timezone;
	
	private $license_data_for_conn_page_view;

	public function __construct(){
		/*
		if(!session_id()) {
			session_start();
		}
		*/		
		
		if (function_exists('date_default_timezone_get')){
			$this->server_timezone = date_default_timezone_get();
		}

		$dsn = 'mysqli://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME;
		if(!$this->check_invalid_chars_in_db_conn_info()){
			$this->dsn = $dsn;
		}else{
			$this->set_session_val('unsupported_db_chars',true);
		}
		
		if(!$this->mw_wc_qbo_sync_plugin_options_desk || empty($this->mw_wc_qbo_sync_plugin_options_desk)){
			$this->set_plugin_options();
		}
		
		$this->p_option_check_default_in_update();
		
		global $wpdb;		

		/*Initialize*/
		if($this->dsn){
			if (!QuickBooks_Utilities::initialized($dsn)){
				QuickBooks_Utilities::initialize($dsn);
			}

			$user = $this->get_option('mw_qbo_dts_qwc_username');
			$pass = $this->get_option('mw_qbo_dts_qwc_password');

			if($user!='' && $pass!=''){
				$pass = $this->decrypt($pass);
				QuickBooks_Utilities::createUser($dsn, $user, $pass);
				$this->qwc_user_created = true;
				$this->qb_username = $user;
			}
			
			/**/
			$max_p_queue_save_day = intval($this->get_option('mw_wc_qbo_desk_save_pqe_for'));
			if($max_p_queue_save_day<1){
				$wpdb->query("DELETE FROM `quickbooks_log` WHERE `quickbooks_log_id` > 0 ");
				$wpdb->query("TRUNCATE TABLE `quickbooks_log` ");
				
				//$wpdb->query("DELETE FROM `quickbooks_queue` WHERE `quickbooks_queue_id` > 0 AND `qb_status` != 'q' "); 
			}
		}

		if(!$this->is_valid_license){
			$this->is_valid_license($this->get_option('mw_wc_qbo_desk_license'),$this->get_option('mw_wc_qbo_desk_localkey'));
		}
	}
	
	/**/
	private function p_option_check_default_in_update(){
		$arncp_o = $this->get_option('mw_wc_qbo_desk_auto_refresh_new_cust_prod');
		if(empty($arncp_o)){
			update_option('mw_wc_qbo_desk_auto_refresh_new_cust_prod','true');
		}
	}
	
	public function get_dsn(){
		return $this->dsn;
	}
	
	public function get_qbun(){
		return $this->qb_username;
	}
	
	public function is_qwc_connected($webconnector=false){
		/**/		
		if($this->dsn && $this->qwc_user_created){			
			if($webconnector && !isset($_GET['debug'])){
				if(!$this->is_license_active()){
					$this->is_valid_license($this->get_option('mw_wc_qbo_desk_license'),$this->get_option('mw_wc_qbo_desk_localkey'),true);
				}
			}
			
			if($this->is_valid_license && $this->plugin_license_status=='Active'){
				return true;
			}			
		}
		return false;
	}
	
	public function is_license_active(){
		if($this->plugin_license_status=='Active'){
			return true;
		}
		return false;
	}
	
	public function debug(){
		if($this->is_qwc_connected()){
			//$this->_p($this->db_check_get_fields_details());
			//$Queue = new QuickBooks_WebConnector_Queue($this->dsn);
			//$Queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, rand(999,999999),0,null,$this->get_qbun());
			//$this->_p($this->get_wc_customer_info(15));
			//$this->add_test_log('#Test');
			//$this->_p($this->get_wc_order_details_from_order(269,get_post(269)));
			//echo $base_currency = get_woocommerce_currency();
			//if($this->wacs_base_cur_enabled()){echo 'Testing...';}
			//$this->_p($this->get_order_base_currency_total_from_order_id(200));
			//$this->_p($this->get_wc_customer_info_from_order(436));
			//$this->_p($this->get_wc_data_pair_val('Customer',56),true);
			//$this->_p($this->check_refresh_data_enabled_by_item('term',true),true);
			//$this->qbxml_debug();
			//$this->get_wc_product_cat_arr();
			//$this->_p($this->get_wc_product_info(80));
			//$this->_p($this->wc_get_payment_details_by_txn_id('ch_Bk7Cis6o7UHTXx',158));
			//$this->_p($this->option_checked('mw_wc_qbo_desk_order_as_sales_order'),true);
			//$this->_p($this->get_wc_user_meta_key_list());
			//$this->_p($this->get_qbd_cus_cf_map_fl());
			//echo $log_last_date = date('Y-m-d',strtotime("-90 days",strtotime($this->get_cdt())));
			//$this->_p($this->get_custom_shipping_map_data_from_name('QuickBooks Shipping'));
			//$this->_p($this->get_local_key_results());			
		}
	}

	public function qbxml_debug($xml=''){
		$errnum = 0;
		$errmsg = '';
		$xml = '';
		if($xml==''){
			return;
		}
		$Parser = new QuickBooks_XML_Parser($xml);

		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CompanyQueryRs');
			foreach ($List->children() as $Object){
				$ret = $Object->name();
				$arr = array();
				//$arr['CompanyName'] = $Object->getChildDataAt($ret.' CompanyName');

				//$this->_p($arr);
			}
		}
	}

	public function set_plugin_options(){
		global $wpdb;
		$option_arr = array();
		$option_data = $this->get_data("SELECT * FROM ".$wpdb->options." WHERE `option_name` LIKE 'mw_wc_qbo_desk%' ");
		if(is_array($option_data) && count($option_data)){
			foreach($option_data as $Option){
				$option_arr[$Option['option_name']] = $Option['option_value'];
			}
		}
		$this->mw_wc_qbo_sync_plugin_options_desk = $option_arr;
	}

	public function get_option($key='',$default=''){
		$option = $default;
		if($key!=''){
			//$this->_p($this->mw_wc_qbo_sync_plugin_options_desk);
			if(is_array($this->mw_wc_qbo_sync_plugin_options_desk) && count($this->mw_wc_qbo_sync_plugin_options_desk) && isset($this->mw_wc_qbo_sync_plugin_options_desk[$key])){
				$option = $this->mw_wc_qbo_sync_plugin_options_desk[$key];
			}else{
				$option = get_option($key);
			}
		}
		$option = trim($option);
		
		/**/
		if($key == 'mw_wc_qbo_desk_shipping_tax_rule_taxable'){
			if(!$this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') || !$this->check_sh_stc_hash()){
				$option = '';
			}
		}
		
		/**/
		if($key == 'mw_wc_qbo_desk_wc_cust_role'){
			$wra = array();
			if(!function_exists('get_editable_roles')){
				require_once(ABSPATH.'wp-admin/includes/user.php');
			}
			
			$wu_roles = get_editable_roles();
			if(is_array($wu_roles) && count($wu_roles)){
				foreach ($wu_roles as $role_name => $role_info){
					$wra[] = $role_name;
				}
			}
			return implode(',',$wra);
		}
		
		return $option;
	}

	public function get_all_options($keys=array()){
		$option_arr = array();
		if(isset($this->mw_wc_qbo_sync_plugin_options_desk)){
			$option_arr =  $this->mw_wc_qbo_sync_plugin_options_desk;
		}
		//
		if(is_array($keys) && count($keys)){
			foreach($keys as $val){
				if(!isset($option_arr[$val])){
					$option_arr[$val] = '';
				}
			}
		}
		return $option_arr;
	}
	
	/**/
	public function get_woo_v_name_trimmed($v_name){
		$v_name = trim($v_name);
		//50 24 25
		if(!empty($v_name) && strlen($v_name) > 100){
			$fs = substr($v_name, 0, 49);
			$ls = substr($v_name, -50);
			$v_name = $fs.' '.$ls;
		}
		return $v_name;
	}
	
	public function get_wc_variation_info($variation_id=0,$manual=false){
		$variation_id = (int) $variation_id;
		if($variation_id>0){
			$variation = get_post($variation_id);
			if(!is_object($variation) || empty($variation)){
				return;
			}
			$variation_meta = get_post_meta($variation_id);
			
			$variation_data = array();
			$variation_data['wc_product_id'] = $variation->ID;
			$variation_data['wc_variation_id'] = $variation->ID;
			
			//$variation_data['name'] = $variation->post_title;
			$variation_data['name'] = $this->get_variation_name_from_id($variation->post_title,'',$variation_id);
			$variation_data['name_t'] = $this->get_woo_v_name_trimmed($variation_data['name']);
			
			$variation_data['description'] = $variation->post_content;
			$variation_data['short_description'] = $variation->post_excerpt;
			
			$variation_data['is_variation'] = true;
			
			$variation_data['manual'] = $manual;
			
			if(is_array($variation_meta) && count($variation_meta)){
				foreach ($variation_meta as $key => $value){
					$variation_data[$key] = ($value[0])?$value[0]:'';
				}
			}
			return $variation_data;
		}
	}
	
	public function get_wc_product_info($product_id=0,$manual=false){
		$product_id = (int) $product_id;
		if($product_id>0){
			$_product = wc_get_product( $product_id );
			//$this->_p($_product);
			
			if(!is_object($_product) || empty($_product)){
				return;
			}
			$product_meta = get_post_meta($product_id);
			
			$product_data = array();
			
			$woo_version = $this->get_woo_version_number();
			if ( $woo_version >= 3.0 ) {
				$product_data['wc_product_id'] = $_product->get_id();
				$p_data = $_product->get_data();
				$product_data['product_type'] = '';
				$product_data['total_stock'] = '';
				
				$product_data['name'] = $p_data['name'];			
				$product_data['description'] = $p_data['description'];
				$product_data['short_description'] = $p_data['short_description'];
			}else{
				$product_data['wc_product_id'] = $_product->id;
				$product_data['product_type'] = $_product->product_type;
				$product_data['total_stock'] = $_product->total_stock;
				
				$product_data['name'] = $_product->post->post_title;			
				$product_data['description'] = $_product->post->post_content;
				$product_data['short_description'] = $_product->post->post_excerpt;
			}			
			
			if(is_array($product_meta) && count($product_meta)){
				foreach ($product_meta as $key => $value){
					$product_data[$key] = ($value[0])?$value[0]:'';
				}
			}
			$product_data['manual'] = $manual;
			return $product_data;	
		}
	}
	
	public function get_wc_customer_info($customer_id=0,$manual=false){
		$customer_id = (int) $customer_id;
		if($customer_id>0){
			$user_info = get_userdata($customer_id);
			if(empty($user_info)){
				return;
			}
			//$this->_p($user_info);
			$user_id = $user_info->ID;
			$user_meta = get_user_meta($user_id);
			//$this->_p($user_meta);
			$customer_data = array();

			$customer_data['wc_cus_id'] = $user_id;
			$customer_data['wc_customerid'] = $user_id;
			$customer_data['firstname'] = ($user_info->first_name)?$user_info->first_name:'';
			$customer_data['lastname'] = ($user_info->last_name)?$user_info->last_name:'';

			$customer_data['email'] = ($user_info->user_email)?$user_info->user_email:'';
			$customer_data['display_name'] = ($user_info->display_name)?$user_info->display_name:'';
			$customer_data['company'] = (isset($user_meta['billing_company'][0]))?$user_meta['billing_company'][0]:'';
			
			//
			$customer_data['username'] = ($user_info->user_login)?$user_info->user_login:'';
			
			$s_all_meta = true;
			if(is_array($user_meta) && count($user_meta)){
				foreach ($user_meta as $key => $value){
					if($this->start_with($key,'billing_') || $this->start_with($key,'shipping_') || $s_all_meta){
						$customer_data[$key] = ($value[0])?$value[0]:'';
					}
				}
			}
			
			if($s_all_meta){
				$us_k = $this->cus_meta_usk();
				if(is_array($customer_data) && count($customer_data) && is_array($us_k) && count($us_k)){
					foreach($us_k as $v){
						if(isset($customer_data[$v])){
							unset($customer_data[$v]);
						}
					}
				}
			}			
			
			$customer_data['currency'] = (string) $this->get_wc_customer_currency($user_id);
			$customer_data['note'] = '';

			$mw_wc_display_name = $this->wc_get_display_name($customer_data);
			$customer_data['display_name'] = $mw_wc_display_name;
			
			//
			global $wpdb;
			$name_replace_chars = array(':','\t','\n');
			$display_name_r = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			$customer_data['display_name_r'] = $display_name_r;
			
			/*
			$display_name_r_a = $display_name_r;
			if($this->option_checked('mw_wc_qbo_desk_cus_push_append_client_id')){
				if($this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','d_name',$display_name_r)){
					$display_name_r_a = $display_name_r.'-'.$user_id;
				}
			}
			$customer_data['display_name_r_a'] = $display_name_r_a;
			*/
			
			$customer_data['manual'] = $manual;

			return $customer_data;
		}
	}

	public function get_wc_customer_info_from_order($order_id=0,$manual=false){
		$order_id = (int) $order_id;
		if($order_id>0){
			$order_meta = get_post_meta($order_id);
			if(empty($order_meta)){
				return;
			}

			$customer_data = array();
			$_customer_user = (isset($order_meta['_customer_user'][0]))?(int) $order_meta['_customer_user'][0]:0;
			$customer_data['wc_cus_id'] = $_customer_user;
			$customer_data['wc_customerid'] = $_customer_user;
			if(is_array($order_meta) && count($order_meta)){
				foreach ($order_meta as $key => $value){
					if($this->start_with($key,'_billing_') || $this->start_with($key,'_shipping_')){
						if($this->start_with($key,'_billing_')){
							$key = str_replace('_billing_','billing_',$key);
						}else{
							$key = str_replace('_shipping_','shipping_',$key);
						}
						$customer_data[$key] = ($value[0])?$value[0]:'';
					}
				}
			}

			$customer_data['firstname'] = $this->get_array_isset($customer_data,'billing_first_name','',true);
			$customer_data['lastname'] = $this->get_array_isset($customer_data,'billing_last_name','',true);
			$customer_data['email'] = $this->get_array_isset($customer_data,'billing_email','',true);
			$customer_data['company'] = $this->get_array_isset($customer_data,'billing_company','',true);

			$_order_currency = (isset($order_meta['_order_currency'][0]))?$order_meta['_order_currency'][0]:'';
			$customer_data['currency'] = $_order_currency;

			$customer_data['note'] = '';

			$mw_wc_display_name = $this->wc_get_display_name($customer_data,true);
			$customer_data['display_name'] = $mw_wc_display_name;
			
			//
			global $wpdb;
			$name_replace_chars = array(':','\t','\n');
			$display_name_r = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			$customer_data['display_name_r'] = $display_name_r;
			
			/*
			$display_name_r_a = $display_name_r;
			if($this->option_checked('mw_wc_qbo_desk_cus_push_append_client_id')){
				if($this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','d_name',$display_name_r)){
					$display_name_r_a = $display_name_r.'-'.$order_id;
				}
			}
			$customer_data['display_name_r_a'] = $display_name_r_a;
			*/
			
			$customer_data['manual'] = $manual;
			return $customer_data;

		}
	}
	
	public function wc_get_display_name($customer_data,$guest=false){
		$display_name = '';
		$wc_cus_id = 0;
		$name_replace_chars = array();
		if($guest){
			$firstname = $this->get_array_isset($customer_data,'billing_first_name','',true);
			$lastname = $this->get_array_isset($customer_data,'billing_last_name','',true);
			$company = $this->get_array_isset($customer_data,'billing_company','',true);
			$email = $this->get_array_isset($customer_data,'billing_email','',true);
		}else{
			$firstname = $this->get_array_isset($customer_data,'firstname','',true);
			$lastname = $this->get_array_isset($customer_data,'lastname','',true);
			$company = $this->get_array_isset($customer_data,'company','',true);
			$email = $this->get_array_isset($customer_data,'email','',true);

			//$wc_cus_id = $this->get_array_isset($customer_data,'wc_cus_id',0);
		}
		
		$d_name = $this->get_array_isset($customer_data,'display_name','',true);
		
		//$shipping_company = $this->get_array_isset($customer_data,'shipping_company','',true);
		$billing_company = $this->get_array_isset($customer_data,'billing_company','',true);
		
		$billing_phone = $this->get_array_isset($customer_data,'billing_phone','',true);
		
		if($this->is_dmcb_fval_ext_ccfv()){
			$display_name = $this->get_dmcb_fval_ext_ccfv_val($customer_data);
		}else{
			$display_name = $this->wc_get_formated_qbo_display_name($firstname,$lastname,$company,$email,$wc_cus_id,$d_name,$billing_phone);
		}		

		if(trim($display_name)==''){
			$display_name = $this->get_array_isset($customer_data,'display_name','',true);
		}
		/**/
		if(trim($display_name)==''){
			$display_name = $firstname." ".$lastname;
		}
		
		if(trim($display_name)==''){
			$display_name = $email;
		}
		return $display_name;

	}
	
	public function wc_get_formated_qbo_display_name($firstname,$lastname,$company,$email,$wc_customerid=0,$d_name='',$billing_phone=''){
		$format = $this->get_option('mw_wc_qbo_desk_display_name_pattern');
		if(empty($format)){
			$format = '{firstname} {lastname}';
		}
		
		if($format!=''){
			$format = str_replace('{phone_number}','{billing_phone}',$format);
			$s_arr = array('{firstname}','{lastname}','{companyname}','{email}');
			$r_arr = array($firstname,$lastname,$company,$email);
			$wc_customerid = (int) $wc_customerid;

			if($wc_customerid){
				$s_arr[] = '{id}';
				$r_arr[] = $wc_customerid;
			}
			//
			if(!empty($d_name)){
				$s_arr[] = '{display_name}';
				$r_arr[] = $d_name;
			}
			
			//
			if(!empty($billing_phone)){
				$s_arr[] = '{billing_phone}';
				$r_arr[] = $billing_phone;
			}
			
			$display_name = str_replace($s_arr,$r_arr,$format);
		}else{
			$display_name = $firstname." ".$lastname;
		}
		return $display_name;
	}

	public function get_wc_customer_currency($wc_cus_id){
		$wc_cus_id = (int) $wc_cus_id;
		if($wc_cus_id){
			global $wpdb;
			$om = $this->get_row("SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_customer_user' AND `meta_value` = '{$wc_cus_id}' LIMIT 0,1 ");
			if(is_array($om) && count($om)){
				$order_id = (int) $om['post_id'];
				if($order_id){
					$om = $this->get_row("SELECT `meta_value` FROM `{$wpdb->postmeta}` WHERE `meta_key` = '_order_currency' AND `post_id` = {$order_id} LIMIT 0,1 ");
					if(is_array($om) && count($om)){
						return $om['meta_value'];
					}
				}
			}
		}
	}

	//25-08-2017
	
	public function save_log($log_data,$add_into_loggly=false){
		if(is_array($log_data) && count($log_data) && isset($log_data['status']) && isset($log_data['log_title']) && trim($log_data['log_title'])!=''){
			global $wpdb;
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_log';

			$max_log_save_day = intval($this->get_option('mw_wc_qbo_desk_save_log_for'));
			$max_log_save_day = ($max_log_save_day<10)?30:$max_log_save_day;

			$log_last_date = date('Y-m-d',strtotime("-$max_log_save_day days",strtotime($this->get_cdt())));
			$log_last_date = $log_last_date.' 23:59:59';

			$wpdb->query(
			$wpdb->prepare(
					"
					DELETE FROM $table
					WHERE `added_date` < %s
					",
					$log_last_date
					)
			);			
			
			/**/
			$max_p_queue_save_day = intval($this->get_option('mw_wc_qbo_desk_save_pqe_for'));
			//$max_p_queue_save_day = ($max_p_queue_save_day<10)?10:$max_p_queue_save_day;
			if($max_p_queue_save_day<1){
				$max_p_queue_save_day = 1;
			}
			
			if($max_p_queue_save_day>0){
				$p_queue_last_date = date('Y-m-d',strtotime("-$max_p_queue_save_day days",strtotime($this->get_cdt())));
				$p_queue_last_date = $p_queue_last_date.' 23:59:59';
				
				/**/
				$pq_tbl = 'quickbooks_queue';
				$wpdb->query(
				$wpdb->prepare(
						"
						DELETE FROM $pq_tbl
						WHERE `qb_status` != 'q'
						AND `dequeue_datetime` < %s
						",
						$p_queue_last_date
						)
				);
				
				/**/
				$qbl_tbl = 'quickbooks_log';
				$wpdb->query(
				$wpdb->prepare(
						"
						DELETE FROM $qbl_tbl
						WHERE `log_datetime` < %s
						",
						$p_queue_last_date
						)
				);
			}			
			
			$log_data['status'] = (int) $log_data['status'];

			$log_data['added_date'] = $this->get_cdt();
			$log_data = array_map('trim',$log_data);
			$wpdb->insert($table, $log_data);

			if($add_into_loggly){

				$loggly_msg = array();
				$status = $log_data['status'];
				if($status<1){
					$ls_type = 'error';
				}
				if($status==1){
					$ls_type = 'success';
				}
				if($status>1){
					$ls_type = 'other';
				}
				
				if($this->start_with($log_data['log_title'],'Import')){
					$ls_type = 'refreshdata';
				}
				
				$loggly_msg['type'] = $ls_type;

				$licensekey = $this->get_option('mw_wc_qbo_desk_license');
				$loggly_msg['licensekey'] = $licensekey;

				$loggly_msg['url'] = get_site_url();
				$loggly_msg['title'] = $log_data['log_title'];
				$loggly_msg['message'] = $log_data['details'];

				$loggly_msg['log_type'] =$log_data['log_type'];

				$loggly_msg['product'] = 'WOOQBD';

				$this->loggly_api_add_log($loggly_msg);
			}
		}
	}

	public function loggly_api_add_log($log_data){
		if(!empty($log_data)){
			if(is_array($log_data) && count($log_data)){
				$log_data = json_encode($log_data);
			}
			$client = new MyWorks_WC_QBO_Desktop_SimpleHTTPClient();
			$requestHeader = array(
				"content-type:text/plain"
			);
			$api_key = 'cbb22de2-5cca-4f43-a028-da5f00a2cebd';
			$api_url = "http://logs-01.loggly.com/inputs/".$api_key."/tag/http/";

			$response = $client->makeRequest($api_url, 'POST', $log_data,$requestHeader);
			//$this->_p($response);
		}
	}
	
	public function get_data_pair_id($type,$qbd_id,$extra=false){
		$dp_id = 0;
		$type = trim($type);$qbd_id = trim($qbd_id);
		if($type!='' && $qbd_id!=''){
			global $wpdb;
			$dp_tbl = $wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs';
			$dp_q = "SELECT `id` FROM `{$dp_tbl}` WHERE `d_type` = '{$type}' AND `qbd_id` = '{$qbd_id}' LIMIT 0,1";
			$dp_row = $this->get_row($dp_q);
			if(is_array($dp_row) && count($dp_row)){
				$dp_id = $dp_row;
			}
		}
		return $dp_id;
	}
	
	public function save_item_map($wc_product_id,$quickbook_product_id,$is_variation=false){
		$wc_product_id = intval($wc_product_id);
		$quickbook_product_id = trim($quickbook_product_id);
		if($wc_product_id && $quickbook_product_id!=''){
			global $wpdb;
			$save_data = array();
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs';
			$w_p_f = 'wc_product_id';
			if($is_variation){
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_variation_pairs';
				$w_p_f = 'wc_variation_id';
			}
			
			$save_data[$w_p_f] = $wc_product_id;
			if($this->get_field_by_val($table,'id','quickbook_product_id',$quickbook_product_id)){
				$wpdb->update($table,$save_data,array('quickbook_product_id'=>$quickbook_product_id),'',array('%d'));
			}else{
				$save_data['quickbook_product_id'] = $quickbook_product_id;
				$wpdb->insert($table, $save_data);
			}
		}
	}
	
	public function save_customer_map($wc_customerid,$qbo_customerid){
		$wc_customerid = intval($wc_customerid);
		$qbo_customerid = trim($qbo_customerid);
		if($wc_customerid && $qbo_customerid!=''){
			global $wpdb;
			$save_data = array();
			$save_data['qbd_customerid'] = $qbo_customerid;
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers_pairs';

			if($this->get_field_by_val($table,'id','wc_customerid',$wc_customerid)){
				$wpdb->update($table,$save_data,array('wc_customerid'=>$wc_customerid),'',array('%d'));
			}else{
				$save_data['wc_customerid'] = $wc_customerid;
				$wpdb->insert($table, $save_data);
			}
		}
	}
	
	public function save_qbo_item_local($quickbook_product_id,$wc_product_id,$is_variation=false,$product_data=array()){
		if(empty($product_data)){
			$wc_product_id = (int) $wc_product_id;
			if($wc_product_id>0){
				if($is_variation){
					$product_data = $this->get_wc_variation_info($wc_product_id);
				}else{
					$product_data = $this->get_wc_product_info($wc_product_id);
				}				
			}
		}
		if($product_data!='' && is_array($product_data) && count($product_data)){
			global $wpdb;
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';
			$save_data = array();

			$save_data['name'] = $product_data['name'];
			$_manage_stock = $this->get_array_isset($product_data,'_manage_stock','no',true);
			$_downloadable = $this->get_array_isset($product_data,'_downloadable','no',true);
			$_virtual = $this->get_array_isset($product_data,'_virtual','no',true);	
			
			if($_manage_stock=='yes'){
				$product_type = 'Inventory';
			}elseif($_virtual=='yes'){
				$product_type = 'Service';
			}else{
				$product_type = 'NonInventory';
			}
			
			$save_data['product_type'] = $product_type;

			$save_data = array_map(array($this, 'trim_add_slash'), $save_data);

			if($this->get_field_by_val($table,'id','qbd_id',$quickbook_product_id)){
				$wpdb->update($table,$save_data,array('qbd_id'=>$quickbook_product_id),'',array('%s'));
				return $quickbook_product_id;
			}else{
				$save_data['qbd_id'] = $quickbook_product_id;
				$wpdb->insert($table, $save_data);
				$insert_id = $wpdb->insert_id;
				return $insert_id;
			}
		}
	}
	
	public function save_qbo_customer_local($qbo_customerid,$wc_cus_id,$customer_data=array()){
		if(empty($customer_data)){
			$wc_cus_id = (int) $wc_cus_id;
			if($wc_cus_id>0){
				$customer_data = $this->get_wc_customer_info($wc_cus_id);
			}
		}
		if($qbo_customerid!='' && is_array($customer_data) && count($customer_data)){
			global $wpdb;
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
			$save_data = array();

			$save_data['first_name'] = $customer_data['firstname'];
			$save_data['last_name'] = $customer_data['lastname'];
			$save_data['d_name'] = $customer_data['display_name'];

			$save_data['company'] = $customer_data['company'];
			$save_data['email'] = $customer_data['email'];

			$save_data = array_map(array($this, 'trim_add_slash'), $save_data);

			if($this->get_field_by_val($table,'id','qbd_customerid',$qbo_customerid)){
				$wpdb->update($table,$save_data,array('qbd_customerid'=>$qbo_customerid),'',array('%d'));
				return $qbo_customerid;
			}else{
				$save_data['qbd_customerid'] = $qbo_customerid;
				$wpdb->insert($table, $save_data);
				$insert_id = $wpdb->insert_id;
				return $insert_id;
			}
		}
	}

	public function trim_add_slash($str){
		return addslashes(trim($str));
	}

	/*DB Functions*/
	public function get_data($query){
		global $wpdb;
		$query = trim($query);
		if($query!=''){
			return $wpdb->get_results($query,ARRAY_A);
		}
	}

	public function get_row($query){
		global $wpdb;
		$query = trim($query);
		if($query!=''){
			return $wpdb->get_row($query,ARRAY_A);
		}
	}

	public function get_row_by_val($tbl,$field,$field_val){
        global $wpdb;
        if($tbl!='' && $field!='' && $field_val!=''){
            $tbl_q = "SELECT * FROM $tbl WHERE $field= '%s'";
            $tbl_data = $this->get_row($wpdb->prepare($tbl_q,$field_val));
            return $tbl_data;
        }
        else{
            return array();
        }
    }

    public function get_field_by_val($tbl,$get_field,$field,$field_val){
        global $wpdb;
        if($tbl!='' && $get_field!='' && $field!='' && $field_val!=''){
            $tbl_q = "SELECT $get_field FROM $tbl WHERE $field= '%s'";
            $tbl_data = $this->get_row($wpdb->prepare($tbl_q,$field_val));
            return (isset($tbl_data[$get_field]))?$tbl_data[$get_field]:'';
        }
        else{
            return '';
        }
    }

	public function get_tbl($tbl='',$fields='*',$whr='',$orderby='',$limit='',$group_by='',$having=''){
		if($tbl!=''){

			if(trim($fields)==''){$fields='*';}

			$tl_q = "SELECT $fields FROM $tbl ";

			if($whr!=''){
				$tl_q.="WHERE $whr ";
			}

			if($group_by!=''){
				$tl_q.="GROUP BY $group_by ";
			}

			if($having!=''){
				$tl_q.="HAVING $having ";
			}

			if($orderby!=''){
				$tl_q.="ORDER BY $orderby ";
			}

			if($limit!=''){
				$tl_q.="LIMIT $limit ";
			}


			return $this->get_data($tl_q);
		}
	}

	public function only_option($selected='',$opt_arr = array(),$s_key='',$s_val='',$return=false){
		$options='';
		if(is_array($opt_arr) && count($opt_arr)>0){
			foreach ($opt_arr as $key => $value) {
				$sel_text = '';

				if($s_key!='' && $s_val!=''){
                    //change for multi
                    if(is_array($selected) && count($selected)){
                        if(in_array($value[$s_key],$selected)){$sel_text = 'selected="selected"';}
                    }else{
                        if($value[$s_key] == $selected){$sel_text = 'selected="selected"';}
                    }
					if($return){
						$options.='<option value="'.$value[$s_key].'" '.$sel_text.'>'.$value[$s_val].'</option>';
					}else{
						echo '<option value="'.$value[$s_key].'" '.$sel_text.'>'.$value[$s_val].'</option>';
					}

				}else{
                    //change for multi
                    if(is_array($selected) && count($selected)){
                        if(in_array($key,$selected)){$sel_text = 'selected="selected"';}
                    }else{
                        if($key == $selected){$sel_text = 'selected="selected"';}
                    }
					if($return){
						$options.='<option value="'.$key.'" '.$sel_text.'>'.$value.'</option>';
					}else{
						echo '<option value="'.$key.'" '.$sel_text.'>'.$value.'</option>';
					}

				}
			}
		}
		if($return){
			return $options;
		}
	}

	public function option_html($selected='',$t_name='',$key_field='',$val_field='',$whr='',$orderby='',$limit='',$return=false){
		if($t_name!='' && $key_field!='' && $val_field!=''){
			$op_fields = "$key_field,$val_field";
			$op_data = $this->get_tbl($t_name,$op_fields,$whr,$orderby,$limit);			
			
			if($this->start_with($val_field,'CONCAT(') || $this->start_with($val_field,'CONCAT_WS(')){
				$vfa = preg_split('/\s+/', $val_field);
				$val_field = end($vfa);
			}
			
			if($return){
				return $this->only_option($selected,$op_data,$key_field,$val_field,$return);
			}
			$this->only_option($selected,$op_data,$key_field,$val_field,$return);
		}
	}
	
	/**/
	public function get_key_value_options_from_table($blank_option=false,$t_name='',$key_field='',$val_field='',$whr='',$orderby='',$limit=''){
		$kv_arr = array();
		if($t_name!='' && $key_field!='' && $val_field!=''){
			$op_fields = "$key_field,$val_field";
			$op_data = $this->get_tbl($t_name,$op_fields,$whr,$orderby,$limit);
			
			if($this->start_with($val_field,'CONCAT(') || $this->start_with($val_field,'CONCAT_WS(')){
				$vfa = preg_split('/\s+/', $val_field);
				$val_field = end($vfa);
			}
			
			if(is_array($op_data) && count($op_data)>0){
				if($blank_option){
					$kv_arr[''] = '';
				}
				foreach ($op_data as $key => $value) {
					$kv_arr[$value[$key_field]] = $value[$val_field];
				}
			}
		}
		return $kv_arr;
	}
	
	public function option_checked($option=''){
		/**/
		if($option == 'mw_wc_qbo_desk_invoice_memo'){
			if($this->get_option('mw_wc_qbo_desk_sync_ord_notes_to_qbq_as') == 's_memo'){
				return true;
			}
			return false;
		}
		
		if($option == 'mw_wc_qbo_desk_cpfmpocjh_cuscompt_ed'){return true;}
		//05-12-2017
		if($option=='mw_wc_qbo_desk_order_as_sales_receipt'){
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as')=='SalesReceipt'){
				return true;
			}
			return false;
		}
		
		if($option=='mw_wc_qbo_desk_order_as_sales_order'){
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as')=='SalesOrder'){
				return true;
			}
			return false;
		}
		
		if($option=='mw_wc_qbo_desk_order_as_estimate'){
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as')=='Estimate'){
				return true;
			}
			return false;
		}
		
		if($option=='mw_wc_qbo_desk_order_as_per_role'){
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as')=='Per Role'){
				return true;
			}
			return false;
		}
		
		if($option=='mw_wc_qbo_desk_order_as_per_gateway'){
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as')=='Per Gateway'){
				return true;
			}
			return false;
		}
		
		//
		if($option == 'mw_wc_qbo_desk_rt_all_invnt_pull'){
			return true;
		}
		
		/**/
		if($option == 'mw_wc_qbo_desk_compt_wpbs'){
			return true;
		}
		
		if($this->get_option($option)=='true'){
			return true;
		}
		return false;
	}

	public function redirect($url){
		if($url!=''){
			wp_redirect( $url );
			exit;
		}
	}

	public function start_with($haystack, $needle){
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	public function sanitize($txt=''){
		$txt = trim($txt);
		$txt   = esc_html( $txt );
		$txt   = esc_sql( $txt );
		$txt   = sanitize_text_field( $txt );
		return $txt;
	}

	public function generate_qwc_file(){
		$mw_qbo_dts_qwc_username = $this->get_option('mw_qbo_dts_qwc_username');
		$mw_qbo_dts_qwc_password = $this->get_option('mw_qbo_dts_qwc_password');
		$mw_qbo_dts_qwc_sec_interval = (int) $this->get_option('mw_qbo_dts_qwc_sec_interval');

		if($mw_qbo_dts_qwc_username!='' && $mw_qbo_dts_qwc_password!='' && $mw_qbo_dts_qwc_sec_interval>0){
			$name = 'MyWorks QuickBooks Desktop Sync';
			$descrip = 'MyWorks Software';

			$appurl = get_site_url('','index.php?mw_qbo_desktop_qwc_server=1','https');
			//$appsupport = get_site_url('','index.php?mw_qbo_desktop_qwc_support=1','https');
			$appsupport = get_site_url('','','https');
			
			$username = $mw_qbo_dts_qwc_username;
			$fileid = $this->get_random_guid();
			$ownerid = $this->get_random_guid();

			$qbtype = QUICKBOOKS_TYPE_QBFS;
			$readonly = false;
			//$run_every_n_seconds = $mw_qbo_dts_qwc_sec_interval;
			$run_every_n_seconds = null;

			// Generate the XML file
			$QWC = new QuickBooks_WebConnector_QWC($name, $descrip, $appurl, $appsupport, $username, $fileid, $ownerid, $qbtype, $readonly, $run_every_n_seconds);
			$xml = $QWC->generate();
			
			
			// Send as a file download
			ob_clean();
			header('Content-type: text/xml');
			header('Content-Disposition: attachment; filename="myworks-qb-desktop.qwc"');
			print($xml);
			exit;
		}
	}

	public function get_random_password( $min, $max){
		// Set length
		$length = rand($min, $max);

		// Set charachters to use
		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$chars = '123456789';//@#$%&

		// Calculate string length
		$lower_length = strlen($lower);
		$upper_length = strlen($upper);
		$chars_length = strlen($chars);

		$lower = str_split($lower);
		$upper = str_split($upper);
		$chars = str_split($chars);

		// Generate password char for char
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++)
		{
			if ($alt == 0)
			{
				$password .= (isset($lower[mt_rand(0, $lower_length-1)]))?$lower[mt_rand(0, $lower_length-1)]:''; $alt = 1;
			}
			if ($alt == 1)
			{
				$password .= (isset($upper[mt_rand(0, $upper_length-1)]))?$upper[mt_rand(0, $upper_length-1)]:''; $alt = 2;
			}
			else
			{
				$password .= (isset($chars[mt_rand(0, $chars_length-1)]))?$chars[mt_rand(0, $chars_length-1)]:''; $alt = 0;
			}
		}
		return $password;
	}

	public function get_random_username( $min, $max, $case_sensitive = false ){
		// Set length
		$length = rand($min, $max);

		// Set allowed chars (And whether they should use case)
		if ( $case_sensitive )
		{
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		}
		else
		{
			$chars = "abcdefghijklmnopqrstuvwxyz";
		}

		// Get string length
		$chars_length = strlen($chars);
		$chars = str_split($chars);

		// Create username char for char
		$username = "";

		for ( $i = 0; $i < $length; $i++ )
		{
			$username .= (isset($chars[mt_rand(0, $chars_length-1)]))?$chars[mt_rand(0, $chars_length-1)]:'';
		}

		return $username;

	}

	public function get_random_guid(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}
		else {
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
				.substr($charid, 0, 8).$hyphen
				.substr($charid, 8, 4).$hyphen
				.substr($charid,12, 4).$hyphen
				.substr($charid,16, 4).$hyphen
				.substr($charid,20,12)
				.chr(125);// "}"
			return $uuid;
		}
	}

	private function get_encryption_key(){
		$key = '?!a^"<6eZA=]c<Uw<~WkQ~2bWm7AA0B2<UTXzd[#oVs.M>[A$J*}';
		return $key;
	}
	
	private function get_encryption_iv(){
		$iv = 't4q^#n&{VAZ"R.2b';
		return $iv;
	}
	
	public function encrypt($input_string, $key=''){
		return $this->encrypt_n($input_string,$key);
		return false;
		$key = $this->get_encryption_key();
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$h_key = hash('sha256', $key, TRUE);
		return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $h_key, $input_string, MCRYPT_MODE_ECB, $iv));
	}
	
	public function encrypt_n($input_string, $key=''){		
		$key = $this->get_encryption_key();
		$iv = $this->get_encryption_iv();
		
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $key );
		$iv = substr( hash( 'sha256', $iv ), 0, 16 );
		return base64_encode( openssl_encrypt( $input_string, $encrypt_method, $key, 0, $iv ) );
	}
	
	public function decrypt($encrypted_input_string, $key=''){
		return $this->decrypt_n($encrypted_input_string,$key);
		return false;
		
		$key = $this->get_encryption_key();
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
		$h_key = hash('sha256', $key, TRUE);
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $h_key, base64_decode($encrypted_input_string), MCRYPT_MODE_ECB, $iv));
	}
	
	public function decrypt_n($input_string, $key=''){
		$key = $this->get_encryption_key();
		$iv = $this->get_encryption_iv();
		
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $key );
		$iv = substr( hash( 'sha256', $iv ), 0, 16 );
		return openssl_decrypt( base64_decode( $input_string ), $encrypt_method, $key, 0, $iv );
	}

	public function check_invalid_chars_in_db_conn_info(){		
		//$invalid_chars = array('@',':','/','\\','\'','+','?','%','#');
		$invalid_chars = array('+','/','#','%','\'','?');
		foreach($invalid_chars as $char){
			if( strpos( DB_USER, $char ) !== false || strpos( DB_PASSWORD, $char ) !== false || strpos( DB_HOST, $char ) !== false || strpos( DB_NAME, $char ) !== false) {
				return true;
			}
		}
		return false;
	}

	public function _p($item='',$dump=false){
		echo '<pre>';
		if(is_object($item) || is_array($item)){
			if($dump){
				var_dump($item);
			}else{
				print_r($item);
			}
		}else{
			if($dump){
				var_dump($item);
			}else{
				echo $item;
			}

		}
		echo '</pre>';
	}

	public function is_plugin_active($plugin='',$diff_filename='',$fc=false){
		$active = false;
		$plugin = trim($plugin);
		$diff_filename = trim($diff_filename);
		$plugin_file = ($diff_filename!='')?$diff_filename:$plugin;
		
		$compt_addon_active = false;
		if($plugin!=''){
			//require_once(ABSPATH.'wp-admin/includes/plugin.php');
			if(function_exists('is_plugin_active')){				
				if( is_plugin_active( $plugin.'/'.$plugin_file.'.php' ) ) {
					$active = true;
				}
				
				/**/
				if(!$active && $plugin == 'woocommerce-sequential-order-numbers-pro' && $diff_filename == 'woocommerce-sequential-order-numbers'){
					if( is_plugin_active( $plugin.'/'.$plugin.'.php' ) ) {
						$active = true;
					}
				}
				
				if( is_plugin_active( 'myworks-quickbooks-desktop-sync-compatibility/myworks-quickbooks-desktop-sync-compatibility.php' ) ) {
					$compt_addon_active = true;
				}
			}else{
				//$this->_p((array) get_option( 'active_plugins', array() ));
				$active = in_array( $plugin.'/'.$plugin_file.'.php', (array) get_option( 'active_plugins', array() ) );
				
				/**/
				if(!$active && $plugin == 'woocommerce-sequential-order-numbers-pro' && $diff_filename == 'woocommerce-sequential-order-numbers'){
					$active = in_array( $plugin.'/'.$plugin.'.php', (array) get_option( 'active_plugins', array() ) );
				}
				
				$compt_addon_active = in_array( 'myworks-quickbooks-desktop-sync-compatibility/myworks-quickbooks-desktop-sync-compatibility.php', (array) get_option( 'active_plugins', array() ) );				
			}
		}
		$compt_p_arr = array();
		$compt_p_arr[] = 'woocommerce-sequential-order-numbers-pro';
		$compt_p_arr[] = 'woocommerce-subscriptions';
		
		
		if(in_array($plugin,$compt_p_arr) && !$compt_addon_active){
			$active = false;
		}
		
		if($plugin=='woocommerce-sequential-order-numbers-pro' && !$active){
			if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
				$active = true;
			}
		}
		
		if(!$fc){
			if($plugin=='woocommerce-sequential-order-numbers-pro' && !$active){
				if($this->option_checked('mw_wc_qbo_desk_compt_p_wsnop')){
					//$active = true;
				}
			}
		}		
		return $active;
	}

	public function get_array_isset($data,$keyword,$default='',$decode=true,$trim=0,$addslash=false,$replace_array=array()){
		$return = $default;
		if(is_array($data) && count($data)){
			if(isset($data[$keyword])){
				$return = $data[$keyword];
				$return = trim($return);
				if($decode){
					$return = htmlspecialchars_decode($return,ENT_QUOTES);
					//27-06-2017
					$return = html_entity_decode($return,ENT_QUOTES);
				}
				if($trim){
					if(strlen($return) > $trim){
						$return = substr($return,0,$trim);
					}
				}
				if($addslash){
					$return = addslashes($return);
				}
				if(is_array($replace_array) && count($replace_array)){
					$return = str_replace($replace_array,'',$return);
				}
			}
		}
		return $return;
	}
	
	/*Avl QBD CF Map Fields*/
	public function get_qbo_avl_cf_map_fields($not_actual_field=false){
		$qbo_cf_arr = array();
		//$this->_p($qbo_cf_arr);
		$qbo_avl_cf_list = array();
		
		$if_cfm_active = false;
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
			$if_cfm_active = true;
		}
		if(!$if_cfm_active){
			return $qbo_avl_cf_list;
		}
		
		//
		$qbo_avl_cf_list['ShipTo'] = 'ShipTo';
		
		if(!$not_actual_field){
			$qbo_avl_cf_list['ShipDate'] = 'ShipDate';
			$qbo_avl_cf_list['Memo'] = 'Memo';
			//$qbo_avl_cf_list['ShipMethodRef'] = 'ShipMethodRef';
			$qbo_avl_cf_list['PONumber'] = 'PONumber';
			$qbo_avl_cf_list['FOB'] = 'FOB';
			$qbo_avl_cf_list['Other'] = 'Other';
			
			if($this->is_plugin_active('woocommerce-order-delivery')){
				$qbo_avl_cf_list['TxnDate'] = 'TxnDate';
			}
		}
		
		if(!$not_actual_field && is_array($qbo_cf_arr) && count($qbo_cf_arr)){
			$qbo_avl_cf_list = array_merge($qbo_avl_cf_list,$qbo_cf_arr);
		}
		return $qbo_avl_cf_list;
	}
	
	/*Avl WC CF Map Fields*/
	public function get_wc_static_billing_order_fields(){
		$wsbof = array();
		$wsbof[] = '_billing_first_name';
		$wsbof[] = '_billing_last_name';
		$wsbof[] = '_billing_company';
		$wsbof[] = '_billing_address_1';
		$wsbof[] = '_billing_address_2';
		$wsbof[] = '_billing_city';
		$wsbof[] = '_billing_state';
		$wsbof[] = '_billing_postcode';
		$wsbof[] = '_billing_country';
		$wsbof[] = '_billing_email';
		$wsbof[] = '_billing_phone';
		return $wsbof;
		
	}
	
	public function get_wc_static_shipping_order_fields(){
		$wscof = array();
		$wscof[] = '_shipping_first_name';
		$wscof[] = '_shipping_last_name';
		$wscof[] = '_shipping_company';
		$wscof[] = '_shipping_address_1';
		$wscof[] = '_shipping_address_2';
		$wscof[] = '_shipping_city';
		$wscof[] = '_shipping_state';
		$wscof[] = '_shipping_postcode';
		$wscof[] = '_shipping_country';
		
		//$wscof[] = '_shipping_phone';
		return $wscof;
		
	}
	
	public function get_wc_avl_cf_map_fields_by_group($not_actual_field=false){
		$wc_avl_cf_list = array();
		
		$if_cfm_active = false;
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
			$if_cfm_active = true;
		}
		if(!$if_cfm_active){
			return $wc_avl_cf_list;
		}
		
		//WooCommerce Admin Custom Order Fields
		if($this->is_plugin_active('woocommerce-admin-custom-order-fields')){
			$wacof_fl = get_option('wc_admin_custom_order_fields');
			if(is_array($wacof_fl) && count($wacof_fl)){
				$tfa = array();$tfa_fl = array();
				$tfa['title'] = 'WooCommerce Admin Custom Order Fields';
				foreach($wacof_fl as $aof_k => $aof){
					$tfa_fl['_wc_acof_'.$aof_k] = $aof['label'].' ('.$aof['type'].')';
				}
				$tfa['fields'] = $tfa_fl;
				$wc_avl_cf_list[] = $tfa;
			}
		}
		
		$is_bdofa = false;$is_sdofa = false;
		//WooCommerce Checkout Field Editor Pro
		if($this->is_plugin_active('woocommerce-checkout-field-editor-pro')){
			$thwcfe_sections = get_option('thwcfe_sections');			
			if(is_array($thwcfe_sections) && count($thwcfe_sections)){
				$tfa = array();$tfa_fl = array();
				$tfa['title'] = 'WooCommerce Checkout Field Editor Pro';
				$tfa['fields'] = $tfa_fl;
				$wc_avl_cf_list[] = $tfa;
					
				//Billing
				if(isset($thwcfe_sections['billing']) && count($thwcfe_sections['billing']) && isset($thwcfe_sections['billing']->fields) && count($thwcfe_sections['billing']->fields)){
					$tfa = array();$tfa_fl = array();
					$tfa['title'] = 'Billing';
					$tfa['f_type'] = 'billing';
					$tfa['sub'] = true;
					$thwcfe_sections_add = $thwcfe_sections['billing']->fields;
					//$this->_p($thwcfe_sections_add);
					foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
						if(in_array('_'.$tsa_k,$this->get_wc_static_billing_order_fields())){
							$tfa_fl['_'.$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
						}else{
							$tfa_fl[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
						}						
					}					
					$tfa['fields'] = $tfa_fl;
					$wc_avl_cf_list[] = $tfa;
					$is_bdofa = true;
				}
				
				//Shipping
				if(isset($thwcfe_sections['shipping']) && count($thwcfe_sections['shipping']) && isset($thwcfe_sections['shipping']->fields) && count($thwcfe_sections['shipping']->fields)){
					$tfa = array();$tfa_fl = array();
					$tfa['title'] = 'Shipping';
					$tfa['f_type'] = 'shipping';
					$tfa['sub'] = true;
					$thwcfe_sections_add = $thwcfe_sections['shipping']->fields;
					//$this->_p($thwcfe_sections_add);
					foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
						if(in_array('_'.$tsa_k,$this->get_wc_static_shipping_order_fields())){
							$tfa_fl['_'.$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
						}else{
							$tfa_fl[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
						}						
					}
					$tfa['fields'] = $tfa_fl;
					$wc_avl_cf_list[] = $tfa;
					$is_sdofa = true;
				}
				
				//Additional
				if(isset($thwcfe_sections['additional']) && count($thwcfe_sections['additional']) && isset($thwcfe_sections['additional']->fields) && count($thwcfe_sections['additional']->fields)){
					$tfa = array();$tfa_fl = array();
					$tfa['title'] = 'Additional';
					$tfa['f_type'] = 'additional';
					$tfa['sub'] = true;
					$thwcfe_sections_add = $thwcfe_sections['additional']->fields;
					//$this->_p($thwcfe_sections_add);
					foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
						$tfa_fl[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
					}
					$tfa['fields'] = $tfa_fl;
					$wc_avl_cf_list[] = $tfa;
				}
				
			}
		}
		
		$is_bdofa_wcfe = false;
		//WooCommerce Checkout Field Editor
		if($this->is_plugin_active('woocommerce-checkout-field-editor')){
			$tfa = array();$tfa_fl = array();
			$tfa['title'] = 'WooCommerce Checkout Field Editor';
			$tfa['fields'] = $tfa_fl;
			$wc_avl_cf_list[] = $tfa;
			
			//Billing
			$wc_fields_billing = get_option('wc_fields_billing');
			if(empty($wc_fields_billing)){
				$wc_fields_billing = $this->get_wc_static_billing_order_fields();
			}
			//$this->_p($wc_fields_billing);
			if(is_array($wc_fields_billing) && count($wc_fields_billing)){
				$tfa = array();$tfa_fl = array();
				$tfa['title'] = 'Billing';
				$tfa['f_type'] = 'billing';
				$tfa['sub'] = true;
				$wcfe_bfa = false;
				foreach($wc_fields_billing as $wfb_k => $wfb_v){
					if(in_array('_'.$wfb_k,$this->get_wc_static_billing_order_fields())){
						if(!$is_bdofa){
							$is_bdofa_wcfe = true;
							$tfa_fl['_'.$wfb_k] = $wfb_k.'('.$wfb_v['type'].')';
							$wcfe_bfa = true;
						}
					}else{
						$wcfe_bfa = true;
						$tfa_fl[$wfb_k] = $wfb_k.'('.$wfb_v['type'].')';
					}
				}
				$tfa['fields'] = $tfa_fl;
				if($wcfe_bfa){
					$wc_avl_cf_list[] = $tfa;
				}				
			}
			
			//Shipping
			$wc_fields_shipping = get_option('wc_fields_shipping');
			if(empty($wc_fields_shipping)){
				//$wc_fields_shipping = $this->get_wc_static_shipping_order_fields();
			}
			//$this->_p($wc_fields_shipping);
			if(is_array($wc_fields_shipping) && count($wc_fields_shipping)){
				$tfa = array();$tfa_fl = array();
				$tfa['title'] = 'Shipping';
				$tfa['f_type'] = 'shipping';
				$tfa['sub'] = true;
				$wcfe_sfa = false;
				foreach($wc_fields_shipping as $wfs_k => $wfs_v){
					if(in_array('_'.$wfs_k,$this->get_wc_static_shipping_order_fields())){
						if(!$is_sdofa){
							$tfa_fl['_'.$wfs_k] = $wfs_k.'('.$wfs_v['type'].')';
							$wcfe_sfa = true;
						}
					}else{
						$wcfe_sfa = true;
						$tfa_fl[$wfs_k] = $wfs_k.'('.$wfs_v['type'].')';
					}
				}
				$tfa['fields'] = $tfa_fl;
				if($wcfe_sfa){
					$wc_avl_cf_list[] = $tfa;
				}				
			}
			
			//Additional
			$wc_fields_additional = get_option('wc_fields_additional');
			
			if(is_array($wc_fields_additional) && count($wc_fields_additional)){
				$tfa = array();$tfa_fl = array();
				$tfa['title'] = 'Additional';
				$tfa['f_type'] = 'additional';
				$tfa['sub'] = true;
				$wcfe_afa = false;
				foreach($wc_fields_additional as $wfa_k => $wfa_v){
					$tfa_fl[$wfa_k] = $wfa_k.'('.$wfa_v['type'].')';
					$wcfe_afa = true;
				}
				$tfa['fields'] = $tfa_fl;
				if($wcfe_afa){
					$wc_avl_cf_list[] = $tfa;
				}				
			}
		}
		
		//Others
		$tfa = array();$tfa_fl = array();
		$tfa['title'] = 'Others';		
		
		$tfa_fl['wc_order_shipping_method_name'] = 'Order Shipping Method Name';
		$tfa_fl['wc_order_phone_number'] = 'Order Phone Number';
		if($this->is_plugin_active('woocommerce-order-delivery')){
			$tfa_fl['_delivery_date'] = 'Order Delivery Date';
		}
		
		if(!$is_bdofa && !$is_bdofa_wcfe){
			//$tfa_fl['_billing_phone'] = 'billing_phone';
		}
		
		/**/		
		if(!$this->wc_check_group_cus_field_already_exists($wc_avl_cf_list,'_billing_email')){
			$tfa_fl['_billing_email'] = 'billing_email';
		}
		
		if(!$this->wc_check_group_cus_field_already_exists($wc_avl_cf_list,'_order_number') && !$this->wc_check_group_cus_field_already_exists($wc_avl_cf_list,'_order_number_formatted')){
			// && $this->option_checked('mw_wc_qbo_desk_compt_p_wsnop')
			//woocommerce-sequential-order-numbers
			if($this->is_plugin_active('woocommerce-sequential-order-numbers-pro','')){
				if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
					$tfa_fl['_order_number'] = 'Order Number';
				}else{
					$tfa_fl['_order_number_formatted'] = 'Order Number';
				}
			}
		}
		
		$tfa['fields'] = $tfa_fl;
		$wc_avl_cf_list[] = $tfa;
		
		return $wc_avl_cf_list;
	}
	
	/**/
	protected function wc_check_group_cus_field_already_exists($wc_avl_cf_list,$field_name){
		if(is_array($wc_avl_cf_list) && !empty($wc_avl_cf_list) && !empty($field_name)){
			foreach($wc_avl_cf_list as $wcf){
				if(isset($wcf['fields']) && is_array($wcf['fields']) && isset($wcf['fields'][$field_name])){
					return true;
				}
			}
		}
		
		return false;
	}
	
	public function get_wc_avl_cf_map_fields($not_actual_field=false){
		$wc_avl_cf_list = array();
		
		$if_cfm_active = false;
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
			$if_cfm_active = true;
		}
		if(!$if_cfm_active){
			return $wc_avl_cf_list;
		}
		
		if(!$not_actual_field){
			//WooCommerce Admin Custom Order Fields
			if($this->is_plugin_active('woocommerce-admin-custom-order-fields')){
				$wacof_fl = get_option('wc_admin_custom_order_fields');
				if(is_array($wacof_fl) && count($wacof_fl)){
					foreach($wacof_fl as $aof_k => $aof){
						$wc_avl_cf_list['_wc_acof_'.$aof_k] = $aof['label'].' ('.$aof['type'].')';
					}
				}
			}
			
			$is_bdofa = false;$is_sdofa = false;
			//WooCommerce Checkout Field Editor Pro
			if($this->is_plugin_active('woocommerce-checkout-field-editor-pro')){
				$thwcfe_sections = get_option('thwcfe_sections');
				if(is_array($thwcfe_sections) && count($thwcfe_sections)){
					//Billing
					if(isset($thwcfe_sections['billing']) && count($thwcfe_sections['billing']) && isset($thwcfe_sections['billing']->fields) && count($thwcfe_sections['billing']->fields)){						
						$thwcfe_sections_add = $thwcfe_sections['billing']->fields;
						//$this->_p($thwcfe_sections_add);
						foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
							if(in_array('_'.$tsa_k,$this->get_wc_static_billing_order_fields())){
								$wc_avl_cf_list['_'.$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
							}else{
								$wc_avl_cf_list[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
							}					
						}
						$is_bdofa = true;
					}
					
					//Shipping
					if(isset($thwcfe_sections['shipping']) && count($thwcfe_sections['shipping']) && isset($thwcfe_sections['shipping']->fields) && count($thwcfe_sections['shipping']->fields)){						
						$thwcfe_sections_add = $thwcfe_sections['shipping']->fields;
						//$this->_p($thwcfe_sections_add);
						foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
							if(in_array('_'.$tsa_k,$this->get_wc_static_shipping_order_fields())){
								$wc_avl_cf_list['_'.$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
							}else{
								$wc_avl_cf_list[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
							}						
						}						
						$is_sdofa = true;
					}
					
					//Additional
					if(isset($thwcfe_sections['additional']) && count($thwcfe_sections['additional']) && isset($thwcfe_sections['additional']->fields) && count($thwcfe_sections['additional']->fields)){
						$thwcfe_sections_add = $thwcfe_sections['additional']->fields;
						//$this->_p($thwcfe_sections_add);
						foreach($thwcfe_sections_add as $tsa_k => $tsa_v){
							$wc_avl_cf_list[$tsa_k] =  $tsa_v->name.'('.$tsa_v->type.')';
						}
					}
					
				}
			}
			
			$is_bdofa_wcfe = false;
			//WooCommerce Checkout Field Editor
			if($this->is_plugin_active('woocommerce-checkout-field-editor')){
				//Billing
				$wc_fields_billing = get_option('wc_fields_billing');
				if(is_array($wc_fields_billing) && count($wc_fields_billing)){
					foreach($wc_fields_billing as $wfb_k => $wfb_v){
						if(in_array('_'.$wfb_k,$this->get_wc_static_billing_order_fields())){
							if(!$is_bdofa){
								$wc_avl_cf_list['_'.$wfb_k] = $wfb_k.'('.$wfb_v['type'].')';
								$is_bdofa_wcfe = true;
							}
						}else{
							$wcfe_bfa = true;
							$wc_avl_cf_list[$wfb_k] = $wfb_k.'('.$wfb_v['type'].')';
						}
					}					
				}
				
				//Shipping
				$wc_fields_shipping = get_option('wc_fields_shipping');
				if(is_array($wc_fields_shipping) && count($wc_fields_shipping)){					
					foreach($wc_fields_shipping as $wfs_k => $wfs_v){
						if(in_array('_'.$wfs_k,$this->get_wc_static_shipping_order_fields())){
							if(!$is_sdofa){
								$wc_avl_cf_list['_'.$wfs_k] = $wfs_k.'('.$wfs_v['type'].')';								
							}
						}else{							
							$wc_avl_cf_list[$wfs_k] = $wfs_k.'('.$wfs_v['type'].')';
						}
					}									
				}
				
				//Additional
				$wc_fields_additional = get_option('wc_fields_additional');
				if(is_array($wc_fields_additional) && count($wc_fields_additional)){					
					foreach($wc_fields_additional as $wfa_k => $wfa_v){
						$wc_avl_cf_list[$wfa_k] = $wfa_k.'('.$wfa_v['type'].')';						
					}					
				}
			}			
			
			//WooCommerce Custom Fields
			if($this->is_plugin_active('woocommerce-custom-fields')){
				//$wccf_fields = $this->get_compt_checkout_fields();
				//$this->_p($wccf_fields);
			}			
			
		}		
		
		$wc_avl_cf_list['wc_order_shipping_method_name'] = 'Order Shipping Method Name';		
		$wc_avl_cf_list['wc_order_phone_number'] = 'Order Phone Number';
		if($this->is_plugin_active('woocommerce-order-delivery')){
			$wc_avl_cf_list['_delivery_date'] = 'Order Delivery Date';
		}
		
		if(!$is_bdofa && !$is_bdofa_wcfe){
			//$wc_avl_cf_list['_billing_phone'] = 'billing_phone';
		}
		
		/**/
		if(!isset($wc_avl_cf_list['_billing_email'])){
			$wc_avl_cf_list['_billing_email'] = 'billing_email';
		}
		
		if(!isset($wc_avl_cf_list['_order_number']) && !isset($wc_avl_cf_list['_order_number_formatted'])){
			// && $this->option_checked('mw_wc_qbo_desk_compt_p_wsnop')
			//woocommerce-sequential-order-numbers
			if($this->is_plugin_active('woocommerce-sequential-order-numbers-pro','')){
				if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
					$wc_avl_cf_list['_order_number'] = 'Order Number';
				}else{
					$wc_avl_cf_list['_order_number_formatted'] = 'Order Number';
				}
			}
		}		
		
		return $wc_avl_cf_list;
	}
	
	public function check_sh_cfm_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_cfm_hash');
		$ch_hash = sha1('j6k+EU' . 'h7,BS8"AGGXb;XqE');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_easton_ccfm_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_easton_ccfm_hash');
		$ch_hash = sha1('R.w<6*' . 'bCmgYrre!}%EPT97');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_sj_csfm_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_sj_csfm_hash');
		$ch_hash = sha1('Juj4@6' . 'jucZrQ3_Auf!G3W#');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_ecet_etdilt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_ecet_etdilt_hash');
		$ch_hash = sha1('kH&6aX' . 'n?JxXQ#nKJCmLn7T');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_ccc_gunnar_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_ccc_gunnar_hash');
		$ch_hash = sha1('bN7h!k' . '&A!RQm*HXnuFaqdp');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_cbsafc_shawshawnk_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_cbsafc_shawshawnk_hash');
		$ch_hash = sha1('Xr6@JG' . 'aJ+8^?yW6#R^v8Uy');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_csrm_seasonalliving_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_csrm_seasonalliving_hash');
		$ch_hash = sha1('=TmsVE' . 'rR-%u_KWNJvX?@Kq');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_liqtycustcolumn_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_liqtycustcolumn_hash');
		$ch_hash = sha1('7+*Nq9' . 'jX4nQyPG593Q@Hmu');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_fmmwoo_exilt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_fmmwoo_exilt_hash');
		$ch_hash = sha1('^@C8zF' . 'H^53evU?PxzPa?vT');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_fitbodywrap_cuscompt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_fitbodywrap_cuscompt_hash');
		$ch_hash = sha1('8d*W=P' . 'y-3b#gKe_HeNS=+w');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_northamericangamebird_cuscompt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_northamericangamebird_cuscompt_hash');
		$ch_hash = sha1('!+H3wY' . 'ZZy-GUPq6jB+Rzxd');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_cpfmpocjh_cuscompt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_cpfmpocjh_cuscompt_hash');
		$ch_hash = sha1('4$MkMn' . 'wqrr$njut2e#8=D+');
		if($sh_cfm_h==$ch_hash && $this->is_plugin_active('myworks-quickbooks-desktop-compt-cpfm-po-cjh')){
			return true;
		}
		return false;
	}
	
	public function check_sh_cuscalusnonusjob_cuscompt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_cuscalusnonusjob_cuscompt_hash');
		$ch_hash = sha1('k7^xHP' . 'wHK+6RqR6kKX_xp=');
		if($sh_cfm_h==$ch_hash && $this->is_plugin_active('myworks-quickbooks-desktop-compt-cus-cal-us-nonus-job')){
			return true;
		}
		return false;
	}
	
	public function check_sh_wosfsus_cuscompt_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_wosfsus_cuscompt_hash');
		$ch_hash = sha1('#zY2c$' . '6pM#Rb7P@mdV+H!V');
		if($sh_cfm_h==$ch_hash && $this->is_plugin_active('myworks-quickbooks-desktop-compt-order-sync-for-specific-us-state')){
			return true;
		}
		return false;
	}
	
	public function check_sh_woorbp_qbpricelevel_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_woorbp_qbpricelevel_hash');
		$ch_hash = sha1('8_KUb=' . 'Ptv3$bKYrrcB4_QB');
		if($sh_cfm_h==$ch_hash && $this->is_plugin_active('myworks-quickbooks-desktop-role-based-price-qb-price-level-compt')){
			return true;
		}
		return false;
	}
	
	public function check_sh_cmfpicw_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_cmfpicw_hash');
		$ch_hash = sha1('6e?M2#' . 'qA6bMCg2WwEt#tq%');
		if($sh_cfm_h==$ch_hash && $this->is_plugin_active('myworks-quickbooks-desktop-custom-multiplication-fpicw')){
			return true;
		}
		return false;
	}
	
	/**/
	protected function get_northamericangamebird_custom_map_order_values($invoice_data){
		$ord_c_data = array(
			'membership_expires' => '',
			'membership_type' => '',
			'activity' => '',
			'bird_types' => '',
		);
		if(is_array($invoice_data) && count($invoice_data)){
			global $wpdb;
			$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
			$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
			
			$bird_types_s = $this->get_array_isset($invoice_data,'business_type','');
			if(!empty($bird_types_s)){
				$bird_types_s = @unserialize($bird_types_s);
				if(is_array($bird_types_s) && count($bird_types_s)){
					$ord_c_data['bird_types'] = implode(',',$bird_types_s);
				}
			}			
			
			if($wc_cus_id > 0 && $wc_inv_id > 0){
				$_wcs_subscription_ids_cache = get_user_meta($wc_cus_id,'_wcs_subscription_ids_cache',true);
				if(is_array($_wcs_subscription_ids_cache) && !empty($_wcs_subscription_ids_cache)){
					$shop_subscription_id = (int) $_wcs_subscription_ids_cache[0];
					if($shop_subscription_id > 0){
						/*
						$shop_order_id = (int) $this->get_field_by_val($wpdb->posts,'post_parent','ID',$shop_subscription_id);
						if($shop_order_id > 0){							
							$sql = $wpdb->prepare("SELECT `post_id` FROM {$wpdb->postmeta} WHERE `meta_key` = '_order_id' AND `meta_value` = %s LIMIT 0,1",$shop_order_id);							
						}
						*/
						
						$sql = $wpdb->prepare("SELECT `post_id` FROM {$wpdb->postmeta} WHERE `meta_key` = '_subscription_id' AND `meta_value` = %s LIMIT 0,1",$shop_subscription_id);						
						$wc_user_membership_id_meta = $this->get_row($sql);
						if(is_array($wc_user_membership_id_meta) && count($wc_user_membership_id_meta)){
							$wc_user_membership_id = (int) $wc_user_membership_id_meta['post_id'];
							if($wc_user_membership_id > 0){
								//$membership_started = get_post_meta($wc_user_membership_id,'_start_date',true);
								$membership_expires = get_post_meta($wc_user_membership_id,'_end_date',true);
								if(!empty($membership_expires)){
									$membership_expires = date('Y-m-d',strtotime($membership_expires));
								}
								$ord_c_data['membership_expires'] = $membership_expires;
								$wc_membership_plan_id = (int) $this->get_field_by_val($wpdb->posts,'post_parent','ID',$wc_user_membership_id);
								if($wc_membership_plan_id > 0){
									$membership_type = $this->get_field_by_val($wpdb->posts,'post_title','ID',$wc_membership_plan_id);
									$ord_c_data['membership_type'] = $membership_type;
								}
								
								$sql = $wpdb->prepare("SELECT `comment_content` FROM {$wpdb->comments} WHERE `comment_post_ID` = %d AND `comment_type` = 'user_membership_note' AND comment_content != ''  LIMIT 0,1",$wc_user_membership_id);
								$comment_data =  $this->get_row($sql);
								if(is_array($comment_data) && count($comment_data)){
									$ord_c_data['activity'] = $membership_type.' (#'.$wc_membership_plan_id.'):'.$comment_data['comment_content'];
								}
							}
						}
					}
				}
			}
		}
		return $ord_c_data;
	}
	
	public function check_sh_so_cpo_is_ofs_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_so_cpo_is_ofs_hash');
		$ch_hash = sha1('5).Vc9' . 'X\kp2Cg5:%yAp);5');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_stc_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_stc_hash');
		$ch_hash = sha1('_c}4Xt' . '@7AbP8Re7/<VP~.Q');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_paamc_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_paamc_hash');
		$ch_hash = sha1('_c}4Xt' . 'DRpB!K7$JD7vV#Pj');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_qbispplm_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_qbispplm_hash');
		$ch_hash = sha1('X+4ybU' . '44X5&p5-AjdMF$_k');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_psusqcc_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_psusqcc_hash');
		$ch_hash = sha1('2n$Wa#' . 'w4kZq^94u#ZR_XUu');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_wcmslscqb_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_wcmslscqb_hash');
		$ch_hash = sha1('n(2V{z' . 'xwV&3c77Fp+pXt8E');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function check_sh_wcmcsps_hash(){
		$sh_cfm_h = $this->get_option('mw_wc_qbo_desk_sh_wcmcsps_hash');
		$ch_hash = sha1('e%NE8c' . 'VVYVHb6czD&b&6C-');
		if($sh_cfm_h==$ch_hash){
			return true;
		}
		return false;
	}
	
	public function get_qbd_locale_list(){
		return $this->get_wc_country_list();
	}
	
	public function get_wc_country_list(){
		$countries_obj   = new WC_Countries();
		$countries   = $countries_obj->__get('countries');
		return $countries;
	}
	
	public function get_country_name_from_code($code=''){
		if($code!=''){			 
			 $countries   = $this->get_wc_country_list();
			 if(is_array($countries) && isset($countries[$code])){
				 return $countries[$code];
			 }
		}
		return $code;
	}

	public function add_test_log($log_txt,$clear_last_day=true,$append=true){
		if(trim($log_txt)==''){
			return;
		}
		$f_log_txt = trim($log_txt).PHP_EOL;
		$log_file_name = plugin_dir_path( dirname( __FILE__ ) ) .'log'.DIRECTORY_SEPARATOR.'dev.log';
		$f_ot = ($append)?'a':'w';
		
		if($clear_last_day && $append && file_exists($log_file_name)){
			if((time()-filemtime($log_file_name)) > 86400){
				$f_ot = 'w';
			}
		}

		$log_file = fopen($log_file_name, $f_ot);
		fwrite($log_file, "\n". $f_log_txt);
		fclose($log_file);
	}
	
	public function get_qbd_timezone(){
		//$tz = $this->get_option('mw_wc_qbo_desk_qbd_timezone_for_calc');
		$tz = $this->get_option('timezone_string');
		if(empty($tz)){
			$tz = 'America/Los_Angeles';
		}
		return $tz;
	}
	
	public function get_cdt($timezone='',$format='Y-m-d H:i:s'){
		//return date('Y-m-d H:i:s');
		if(empty($timezone)){
			//$timezone = $this->server_timezone;
			$timezone = $this->get_qbd_timezone();
		}
		
		if($timezone!=''){
			$now = new DateTime(null, new DateTimeZone($timezone));
			$datetime = $now->format($format);
			return $datetime;
		}
		return date($format);
	}
	
	public function now($format='Y-m-d H:i:s',$timezone=''){
		return $this->get_cdt($timezone,$format);
	}
	
	function convert_dt_timezone($d_time="",$toTz='',$fromTz=''){
		if(!$toTz || !$d_time){
			return $d_time;
		}
		
		if(empty($fromTz)){
			$fromTz = $this->server_timezone;
		}
        $date = new DateTime($d_time, new DateTimeZone($fromTz));
        $date->setTimezone(new DateTimeZone($toTz));
        $d_time= $date->format('Y-m-d H:i:s');
        return $d_time;
    }
	
	public function save_data_pair($type,$wc_id,$qbd_id,$ext_data=''){
		global $wpdb;
		$wc_id = intval($wc_id);
		$ext_data = $this->sanitize($ext_data);
		if($type!='' && $wc_id>0 && $qbd_id!=''){
			$save_data = array();
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs';

			$save_data['wc_id'] = $wc_id;
			//$this->get_field_by_val($table,'id','qbd_id',$qbd_id)
			$existing_data = $this->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE `qbd_id` = %s AND `ext_data` = %s ",$qbd_id,$ext_data));
			if(is_array($existing_data) && count($existing_data)){
				$save_data['modify_date'] = $this->get_cdt();
				$wpdb->update($table,$save_data,array('qbd_id'=>$qbd_id,'ext_data'=>$ext_data),'',array('%d','%s'));
			}else{
				$save_data['added_date'] = $this->get_cdt();
				$save_data['modify_date'] = $this->get_cdt();
				$save_data['qbd_id'] = $qbd_id;
				$save_data['d_type'] = $type;
				$save_data['ext_data'] = $ext_data;
				$wpdb->insert($table, $save_data);
			}
		}
	}
	
	public function get_wc_data_pair_val($d_type,$wc_id,$ext_data=''){
		$ext_data = $this->sanitize($ext_data);
		$wc_id = (int) $wc_id;
		if($d_type!='' && $wc_id>0){
			global $wpdb;
			if(!empty($ext_data)){
				$q = $wpdb->prepare("SELECT `qbd_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs WHERE `wc_id` = %d AND `d_type` = %s AND `qbd_id` !='' AND `ext_data` = %s ",$wc_id,$d_type,$ext_data);
			}else{
				$q = $wpdb->prepare("SELECT `qbd_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs WHERE `wc_id` = %d AND `d_type` = %s AND `qbd_id` !='' ",$wc_id,$d_type);
			}			
			$p_data = $this->get_row($q);
			if(is_array($p_data) && count($p_data) && isset($p_data['qbd_id'])){
				return $p_data['qbd_id'];
			}
		}
	}
	
	public function get_qbd_data_pair_val($d_type,$qbd_id,$ext_data=''){
		$ext_data = $this->sanitize($ext_data);
		if($d_type!='' && $qbd_id!=''){
			global $wpdb;
			if(!empty($ext_data)){
				$q = $wpdb->prepare("SELECT `wc_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs WHERE `qbd_id` = %s AND `d_type` = %s AND `wc_id` > 0 AND `ext_data` = %s ",$qbd_id,$d_type,$ext_data);
			}else{
				$q = $wpdb->prepare("SELECT `wc_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs WHERE `qbd_id` = %s AND `d_type` = %s AND `wc_id` > 0 ",$qbd_id,$d_type);
			}
			
			$p_data = $this->get_row($q);
			if(is_array($p_data) && count($p_data) && isset($p_data['wc_id'])){
				return $p_data['wc_id'];
			}
		}
	}

	public function get_custom_post_list($post_type='post',$items_per_page,$search_txt='',$orderby='post_date',$order='desc',$post_status='publish',$meta_query=array()){

		$offset = $this->get_offset($this->get_page_var(),$items_per_page);

		$args = array(
			'posts_per_page'   => $items_per_page,
			'orderby'          => $orderby,
			'order'            => $order,
			'post_type'        => $post_type,
			'post_status'      => $post_status,
			'offset'          => $offset,
		);

		$search_txt = trim($search_txt);
		if($search_txt!=''){
			$args['s'] = $search_txt;
		}

		if(is_array($meta_query) && count($meta_query)){
			$args['meta_query'] = $meta_query;
		}

		$post_query_obj = new WP_Query( $args );
		$post_array = $post_query_obj->posts;

		$total_records = $post_query_obj->found_posts;
		wp_reset_query();
		//$this->_p($post_query_obj);
		$pagination_links = $this->get_paginate_links($total_records,$items_per_page);
		return array('post_array'=>$post_array, 'pagination_links'=>$pagination_links);
	}
	
	/**/
	public function get_woo_ord_number_from_order($order_id,$invoice_data=array()){
		$o_num = '';
		$order_id = (int) $order_id;
		
		$onk_f = '';
		if($order_id > 0){
			if($this->is_plugin_active('woocommerce-sequential-order-numbers-pro','') && $this->option_checked('mw_wc_qbo_desk_compt_p_wsnop')){
				if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
					$onk_f = '_order_number';
				}else{
					$onk_f = '_order_number_formatted';
				}
			}
			
			if(!empty($onk_f)){
				if(is_array($invoice_data) && !empty($invoice_data) && isset($invoice_data[$onk_f])){
					$o_num = $invoice_data[$onk_f];
				}else{
					$o_num = get_post_meta($order_id,$onk_f,true);
				}
				
				if($o_num!=''){
					$o_num = trim($o_num);
				}					
			}
			
			if(empty($o_num) && $this->option_checked('mw_wc_qbo_desk_compt_woo_cust_onum_ph')){
				$o_num = apply_filters( 'woocommerce_order_number', $order_id, wc_get_order($order_id) );
			}
		}
		
		return $o_num;
	}
	
	public function get_wc_order_details_from_order($order_id,$order){
		global $wpdb;
		$order_id = (int) $order_id;
		if($order_id && is_object($order) && !empty($order)){
			//$this->_p($order);
			$order_meta = get_post_meta($order_id);
			//$this->_p($order_meta);
			$invoice_data = array();
			$invoice_data['wc_inv_id'] = $order_id;
			//
			$invoice_data['wc_inv_num'] = $this->get_woo_ord_number_from_order($order_id);
			
			$invoice_data['order_type'] = '';
			
			if($this->option_checked('mw_wc_qbo_desk_set_cur_date_as_inv_date')){
				$invoice_data['wc_inv_date'] = $this->get_cdt('','Y-m-d');
				$invoice_data['wc_inv_due_date'] = $this->get_cdt('','Y-m-d');
				
				$invoice_data['wc_inv_date_ori'] = $order->post_date;
				$invoice_data['wc_inv_due_date_ori'] = $order->post_date;
			}else{
				if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
					$invoice_data['wc_inv_date'] = get_post_meta($order_id,'_paid_date',true);
					if(empty($invoice_data['wc_inv_date'])){
						$invoice_data['wc_inv_date'] = $order->post_date;
					}
					$invoice_data['wc_inv_due_date'] = $invoice_data['wc_inv_date'];
				}else{
					$invoice_data['wc_inv_date'] = $order->post_date;
					$invoice_data['wc_inv_due_date'] = $order->post_date;
				}				
			}			
			
			$invoice_data['customer_note'] = $order->post_excerpt;
			$invoice_data['order_status'] = $order->post_status;

			$wc_cus_id = isset($order_meta['_customer_user'][0])?(int) $order_meta['_customer_user'][0]:0;
			$invoice_data['wc_cus_id'] = $wc_cus_id;
			
			/**/			
			if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role'){
				$wc_user_role = '';
				if($wc_cus_id > 0){
					$user_info = get_userdata($wc_cus_id);
					if(isset($user_info->roles) && is_array($user_info->roles)){
						$wc_user_role = $user_info->roles[0];
					}
				}else{
					$wc_user_role = 'wc_guest_user';
				}
				
				$invoice_data['wc_user_role'] = $wc_user_role;
				
				if(!empty($wc_user_role)){
					$mw_wc_qbo_desk_oqsa_pr_template_data = get_option('mw_wc_qbo_desk_oqsa_pr_template_data');
					if(is_array($mw_wc_qbo_desk_oqsa_pr_template_data) && isset($mw_wc_qbo_desk_oqsa_pr_template_data[$wc_user_role])){
						$invoice_data['TemplateRef'] = $mw_wc_qbo_desk_oqsa_pr_template_data[$wc_user_role];
					}
				}				
			}
			
			if(is_array($order_meta) && count($order_meta)){
				foreach ($order_meta as $key => $value){
					$invoice_data[$key] = ($value[0])?$value[0]:'';
				}
			}
			
			/*PM Due Date*/
			$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);
			$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
			
			if($this->wacs_base_cur_enabled()){
				$base_currency = get_woocommerce_currency();
				$payment_method_map_data  = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
			}else{
				$payment_method_map_data  = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
			}
			
			$inv_due_date_days = (int) $this->get_array_isset($payment_method_map_data,'inv_due_date_days',0);			
			if(!empty($invoice_data['wc_inv_date']) && $inv_due_date_days > 0){
				$invoice_data['wc_inv_due_date'] = date('Y-m-d H:i:s',strtotime($invoice_data['wc_inv_date'] . "+{$inv_due_date_days} days"));
			}
			
			$wc_oi_table = $wpdb->prefix.'woocommerce_order_items';
			$wc_oi_meta_table = $wpdb->prefix.'woocommerce_order_itemmeta';

			$order_items = $this->get_data("SELECT * FROM {$wc_oi_table} WHERE `order_id` = {$order_id} ORDER BY order_item_id ASC ");
			//$this->_p($order_items);
			$line_items = $used_coupons = $tax_details = $shipping_details = array();
			$dc_gt_fees = array();
			//
			$pw_gift_card = array();
			if(is_array($order_items) && count($order_items)){
				foreach($order_items as $oi){
					$order_item_id = (int) $oi['order_item_id'];
					$oi_meta = $this->get_data("SELECT * FROM {$wc_oi_meta_table} WHERE `order_item_id` = {$order_item_id} ");
					//$this->_p($oi_meta);
					$om_arr = array();
					if(is_array($oi_meta) && count($oi_meta)){
						foreach($oi_meta as $om){
							$om_arr[$om['meta_key']] = $om['meta_value'];
						}
					}

					$om_arr['name'] = $oi['order_item_name'];
					$om_arr['type'] = $oi['order_item_type'];

					if($oi['order_item_type']=='line_item'){
						$line_items[] = $om_arr;
					}

					if($oi['order_item_type']=='coupon'){
						$used_coupons[] = $om_arr;
					}

					if($oi['order_item_type']=='shipping'){
						if(isset($om_arr['name'])){
							$om_arr['name'] = $this->get_array_isset($om_arr,'name');
						}
						$shipping_details[] = $om_arr;
					}

					if($oi['order_item_type']=='tax'){
						if(isset($om_arr['label'])){
							$om_arr['label'] = $this->get_array_isset($om_arr,'label');
						}
						$tax_details[] = $om_arr;
					}

					//16-05-2017
					if($oi['order_item_type']=='fee'){
						if(isset($om_arr['name'])){
							$om_arr['name'] = $this->get_array_isset($om_arr,'name');
						}
						$dc_gt_fees[] = $om_arr;
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed')){
						if($oi['order_item_type']=='pw_gift_card'){
							if(isset($om_arr['name'])){
								$om_arr['name'] = $this->get_array_isset($om_arr,'name');
							}
							$pw_gift_card[] = $om_arr;
						}
					}
				}
			}

			/**/
			if(!$this->option_checked('mw_wc_qbo_desk_no_ad_discount_li') && empty($used_coupons) && isset($invoice_data['_cart_discount']) && $invoice_data['_cart_discount'] > 0){
				$t_uc = array(
					'name' => '',
					'discount_amount' => $invoice_data['_cart_discount'],
					'discount_amount_tax' => $invoice_data['_cart_discount_tax'],
				);
				
				if($this->wacs_base_cur_enabled() && isset($invoice_data['_cart_discount_base_currency'])){
					$t_uc['discount_amount_base_currency'] = $invoice_data['_cart_discount_base_currency'];
					$t_uc['discount_amount_tax_base_currency'] = $invoice_data['_cart_discount_tax_base_currency'];
				}
				
				$used_coupons[] = $t_uc;
			}
			
			$qbo_inv_items = array();
			//$this->_p($line_items);
			if(is_array($line_items) && count($line_items)){
				foreach ( $line_items as $item ) {
					$product_data = array();
					foreach($item as $key=>$val){
						if($this->start_with($key,'_') && $key != '_qty' && $key != '_order_item_wh'){
							$key = substr($key,1);
						}
						$product_data[$key] = $val;
					}
					$l_up = ($product_data['line_subtotal']/$product_data['_qty']);
					//
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						if($product_data['line_total']<$product_data['line_subtotal']){
							$l_up = ($product_data['line_total']/$product_data['_qty']);
						}
					}					
					
					$l_up = $this->qbd_limit_decimal_points($l_up);
					$product_data['unit_price'] = $l_up;
					//
					if($this->wacs_base_cur_enabled()){
						$product_data['unit_price_base_currency'] = $product_data['unit_price'];
						if(isset($product_data['line_subtotal_base_currency'])){
							$l_up_bc = ($product_data['line_subtotal_base_currency']/$product_data['_qty']);
							//
							if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
								if($product_data['line_total_base_currency']<$product_data['line_subtotal_base_currency']){
									$l_up_bc = ($product_data['line_total_base_currency']/$product_data['_qty']);
								}
							}						
							
							$l_up_bc = $this->qbd_limit_decimal_points($l_up_bc);
							$product_data['unit_price_base_currency'] = $l_up_bc;
						}						
					}
					
					$qbo_inv_items[] = $this->get_mapped_qbo_items_from_wc_items($product_data,$invoice_data);
				}
			}

			$invoice_data['used_coupons'] = $used_coupons;

			$order_shipping_total = isset($order_meta['_order_shipping'][0])?$order_meta['_order_shipping'][0]:0;

			$invoice_data['shipping_details'] = $shipping_details;
			$invoice_data['order_shipping_total'] = $order_shipping_total;

			$invoice_data['tax_details'] = $tax_details;

			$invoice_data['qbo_inv_items'] = $qbo_inv_items;

			$invoice_data['dc_gt_fees'] = $dc_gt_fees;
			
			$invoice_data['pw_gift_card'] = $pw_gift_card;
			
			//$this->_p($invoice_data);
			if($this->check_sh_cmfpicw_hash()){
				$invoice_data = $this->custom_order_and_p_details_amounts_multiplication($invoice_data);
			}
			//
			$invoice_data = $this->order_and_p_details_amounts_round($invoice_data);
			return $invoice_data;
		}
	}
	
	public function qbd_limit_decimal_points($amount,$dp=5,$sep='.',$t_sep=''){
		$amount = trim($amount);
		$dp = (int) $dp;
		if ($amount!='' && $d_pos = strpos($amount, '.') !== false && $dp>0) {
			$a_dp = substr($amount, $d_pos+1);
			if(strlen($a_dp) > $dp){
				$amount = number_format((float)$amount, 5, $sep, $t_sep);
			}
		}
		return $amount;
	}
	
	public function get_variation_ids_from_product_id($product_id){
		$v_ids = array();
		$product_id = (int) $product_id;
		
		if($product_id>0){
			global $wpdb;
			$q = "SELECT `ID` FROM {$wpdb->posts} WHERE `post_parent` = {$product_id} AND `post_type` = 'product_variation' AND `post_status` = 'publish' ";
			$va_data = $this->get_data($q);
			if(is_array($va_data) && count($va_data)){
				foreach($va_data as $vd){
					$v_ids[] = $vd['ID'];
				}
			}
		}
		return 	$v_ids;
	}
	
	//10-01-2018
	public function wacs_base_cur_enabled(){
		if($this->is_plugin_active('woocommerce-aelia-currencyswitcher')){
			if($this->option_checked('mw_wc_qbo_desk_wacs_base_cur_support')){
				return true;
			}			
		}
		return false;
	}
	
	public function get_mapped_qbo_items_from_wc_items($wc_items=array(),$invoice_data=array()){
		$qbo_items = array();
		if(is_array($wc_items) && count($wc_items)){
			$map_data = array();
			global $wpdb;
			
			$wc_variation_id = 0;
			$wc_product_id = 0;
			
			if(empty($map_data)){
				$wc_variation_id = (isset($wc_items['variation_id']))?(int) $wc_items['variation_id']:0;
				if($wc_variation_id){
					$map_data = $this->get_row("SELECT `quickbook_product_id` AS itemid , `class_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `wc_variation_id` = $wc_variation_id AND `quickbook_product_id` !='' ");
				}
			}
			$wc_product_id = (isset($wc_items['product_id']))?(int) $wc_items['product_id']:0;
			if(empty($map_data)){
				$map_data = $this->get_row("SELECT `quickbook_product_id` AS itemid , `class_id` , `a_line_item_desc` , `qb_ar_acc_id` , `qb_ivnt_site` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `wc_product_id` = $wc_product_id AND `quickbook_product_id` !='' ");
			}
			
			if(!empty($map_data)){
				$qbo_item_id = $map_data['itemid'];
				$product_type = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','product_type','qbd_id',$qbo_item_id);
				$map_data['product_type'] = $product_type;
			}

			if(empty($map_data)){
				$def_qbd_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
				$map_data = $this->get_row("SELECT `qbd_id` AS itemid , `product_type` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_items` WHERE `qbd_id` = '$def_qbd_item' ");
			}
			
			$Description = $this->get_array_isset($wc_items,'name','');			
			
			//Woo Extra Product Options
			$ext_desc = '';
			if($this->is_plugin_active('woo-extra-product-options') && $this->option_checked('mw_wc_qbo_desk_compt_woo_ext_prd_opt_pd')){
				$ext_desc.= $this->get_wepo_options_str($wc_items);
			}
			
			//WooCommerce TM Extra Product Options
			if($this->is_plugin_active('woocommerce-tm-extra-product-options','tm-woo-extra-product-options') && $this->option_checked('mw_wc_qbo_desk_compt_p_wtmepo')){
				$ext_options_str = '';
				if(isset($wc_items['tmcartepo_data']) && $wc_items['tmcartepo_data']!=''){
					$tmcartepo_data = unserialize($wc_items['tmcartepo_data']);
					if(is_array($tmcartepo_data) && count($tmcartepo_data)){
						foreach($tmcartepo_data as $ed){
							$ext_options_str.=$ed['name'].': '.$ed['value'].PHP_EOL;
						}
					}
				}
				if($ext_options_str!=''){
					$Description.=PHP_EOL.$ext_options_str;
				}
			}						
			
			//Event Ticket Details Line Item Support
			if($this->check_sh_ecet_etdilt_hash()){
				if(is_array($invoice_data) && isset($invoice_data['_tribe_tickets_meta']) && !empty($invoice_data['_tribe_tickets_meta']) && isset($invoice_data['_tribe_has_tickets']) && $invoice_data['_tribe_has_tickets'] == 1){
					$ext_options_str = '';
					
					$_tribe_tickets_meta = trim($invoice_data['_tribe_tickets_meta']);
					$_tribe_tickets_meta = unserialize($_tribe_tickets_meta);
					
					$_tribe_tickets_meta_arr = array();
					if(is_array($_tribe_tickets_meta) && !empty($_tribe_tickets_meta)){
						if($wc_product_id>0 && isset($_tribe_tickets_meta[$wc_product_id]) && is_array($_tribe_tickets_meta[$wc_product_id])){
							//$_tribe_tickets_meta_arr = $_tribe_tickets_meta[$wc_product_id][0];
							$_tribe_tickets_meta_arr = $_tribe_tickets_meta[$wc_product_id];
						}
					}
					
					/*
					if(is_array($_tribe_tickets_meta_arr) && !empty($_tribe_tickets_meta_arr)){
						foreach($_tribe_tickets_meta_arr as $ek => $ev){
							$ext_options_str.=$ek.': '.$ev.PHP_EOL;
						}
					}
					*/
					
					if(is_array($_tribe_tickets_meta_arr) && !empty($_tribe_tickets_meta_arr)){
						foreach($_tribe_tickets_meta_arr as $ek => $ev){
							if(is_array($ev) && !empty($ev)){
								foreach($ev as $ev_k => $ev_v){
									/**/
									if($ev_k == 'contacts' && is_numeric($ev_v)){
										$t_cnt_name = '';
										$t_wc_user_id = intval($ev_v);
										if($t_wc_user_id > 0){
											$tc_fn = trim(get_user_meta($t_wc_user_id,'first_name',true));
											$tc_ln = trim(get_user_meta($t_wc_user_id,'last_name',true));
											if(!empty($tc_fn) || !empty($tc_ln)){
												if(!empty($tc_fn)){$t_cnt_name = $tc_fn;}
												if(!empty($tc_ln)){
													if(!empty($t_cnt_name)){
														$t_cnt_name .= ' ' . $tc_ln;
													}else{
														$t_cnt_name = $tc_ln;
													}
												}
												
											}else{
												$t_cnt_name = trim(get_user_meta($t_wc_user_id,'nickname',true));
											}
											$t_cnt_name = trim($t_cnt_name);
										}
										
										if(!empty($t_cnt_name)){
											$ev_v = $t_cnt_name;
										}
									}
									
									$ext_options_str.=$ev_k.': '.$ev_v.PHP_EOL;
								}
							}
						}
					}
					
					if($ext_options_str!=''){
						$Description.=PHP_EOL .$ext_options_str;
					}
				}
			}
			
			$ext_ord_line_item_meta_keys = array();
			
			//FMM WooCommerce Extend (CP)
			if($this->is_plugin_active('fmm-woo-extend') && $this->is_plugin_active('myworks-quickbooks-desktop-fmm-woo-extend-line-item-support') && $this->check_sh_fmmwoo_exilt_hash()){								
				$ext_ord_line_item_meta_keys['Frame'] = 'Frame';
				
				/*
				$ext_ord_line_item_meta_keys['Framed Mirror'] = 'Framed Mirror';
				$ext_ord_line_item_meta_keys['LinearFootage'] = 'LinearFootage';
				$ext_ord_line_item_meta_keys['Splicing Fee'] = 'Splicing Fee';
				*/
			}
			
			/**/
			if(!empty($ext_ord_line_item_meta_keys)){
				$ext_options_str = '';
				foreach($ext_ord_line_item_meta_keys as $eoli_k => $eoli_v){
					if(isset($wc_items[$eoli_k])){
						$ext_options_str.=$eoli_v.': '.$wc_items[$eoli_k].PHP_EOL;
					}
				}
				
				if($ext_options_str!=''){
					$ext_options_str = str_replace('<br>',PHP_EOL,$ext_options_str);
					$Description.=PHP_EOL .$ext_options_str;
				}
			}
			
			/**/
			if($this->option_checked('mw_wc_qbo_desk_wolim_iqilid_desc')){
				$solm_arr = array(
					'name', '_qty', 'qty',
					'unit_price', 'product_id', 'variation_id',
					'tax_class', 'line_subtotal', 'line_subtotal_tax',
					'line_total', 'line_tax', 'line_tax_data',
					'wc_avatax_rate', 'wc_avatax_code', 'wc_cog_item_cost',
					'wc_cog_item_total_cost', 'reduced_stock', 'type','order_item_id',
					'tmcartepo_data', 'vpc-cart-data', '_order_item_wh',
					
					'line_subtotal_base_currency', 'line_total_base_currency', 'line_tax_base_currency',
				);
				
				$ext_olim_d = '';
				foreach($wc_items as $wk => $wv){
					if(empty($wv)){continue;}
					
					$is_olim_lid_add = true;
					if(in_array($wk,$solm_arr)){
						$is_olim_lid_add = false;
					}
					
					if($wc_variation_id && $this->start_with($wk,'pa_')){
						$is_olim_lid_add = false;
					}
					
					if($is_olim_lid_add){
						$olim_csd = @unserialize($wv);
						if ($wv === 'b:0;' || $olim_csd !== false) {
							$is_olim_lid_add = false;
						}
					}
					
					if($is_olim_lid_add){
						$eolm_k = ucfirst(str_replace('_',' ',$wk));
						$eolm_v = trim($wv);
						$ext_olim_d.=$eolm_k.': '.$eolm_v.PHP_EOL;
						
					}
				}
				
				if($ext_olim_d!=''){
					$Description.=PHP_EOL.$ext_olim_d;
				}
			}
			
			$Description = $this->get_array_isset(array('Description'=>$Description),'Description','');
			
			if(is_array($map_data) && count($map_data)){
				$qbo_items_tmp = array();
				$qbo_items_tmp['Description'] = $Description;
				//
				if(!empty($ext_desc)){
					$qbo_items_tmp['Qbd_Ext_Description'] = $ext_desc;
				}				
				
				$qbo_items_tmp['Qty'] = $wc_items['_qty'];
				
				if(isset($wc_items['qty'])){
					$qbo_items_tmp['Qty_Str'] = $wc_items['qty'];
					unset($wc_items['qty']);
				}
				
				$qbo_items_tmp['UnitPrice'] = $wc_items['unit_price'];
				//
				if($this->wacs_base_cur_enabled()){
					$qbo_items_tmp['UnitPrice_base_currency'] = $wc_items['unit_price_base_currency'];
				}
				$qbo_items_tmp['ItemRef'] = $map_data['itemid'];

				$qbo_items_tmp['qbo_product_type'] = $map_data['product_type'];

				//$qbo_items_tmp['Taxed'] = ($wc_items['line_tax']>0)?1:0;
				$qbo_items_tmp['Taxed'] = ($wc_items['line_tax']>0 || $wc_items['line_subtotal_tax']>0)?1:0;
				
				/**/
				$qbo_items_tmp['ClassRef'] = (isset($map_data['class_id']))?$map_data['class_id']:'';
				if(empty($qbo_items_tmp['ClassRef'])){
					$qbo_items_tmp['ClassRef'] = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
				}				
				
				//
				if(isset($map_data['a_line_item_desc']) && $map_data['a_line_item_desc'] ==1){
					$qbo_items_tmp['AllowPvLid'] = true;
				}else{
					$qbo_items_tmp['AllowPvLid'] = false;
				}

				$qbo_items_tmp['QbArAccId'] = (isset($map_data['qb_ar_acc_id']))?$map_data['qb_ar_acc_id']:'';
				$qbo_items_tmp['QbIvntSiteref'] = (isset($map_data['qb_ivnt_site']))?$map_data['qb_ivnt_site']:'';
				
				//
				if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $this->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){					
					$wc_prd_var_id = ($wc_variation_id)?$wc_variation_id:$wc_product_id;
					if($wc_prd_var_id){
						$qbo_items_tmp['socpo_manage_stock'] = get_post_meta($wc_prd_var_id,'_manage_stock',true);
						$qbo_items_tmp['socpo_stock'] = get_post_meta($wc_prd_var_id,'_stock',true);
						$qbo_items_tmp['socpo_stock_status'] = get_post_meta($wc_prd_var_id,'_stock_status',true);
					}
				}
				
				$qbo_items = $qbo_items_tmp;
				foreach($wc_items as $k => $val){
					if($k!='name' && $k!='_qty' && $k!='unit_price'){
						$qbo_items[$k] = $val;
					}
				}
			}
		}
		return $qbo_items;
	}
	
	/**/
	public function chk_is_po_add($qbo_inv_items){
		$is_po_add = false;
		if(is_array($qbo_inv_items) && count($qbo_inv_items)){
			foreach($qbo_inv_items as $qbo_item){
				if(isset($qbo_item['socpo_manage_stock']) && $qbo_item['socpo_manage_stock'] == 'yes'){
					if(isset($qbo_item['socpo_stock']) && floatval($qbo_item['socpo_stock']) < 0){
						$is_po_add = true;
						break;
					}
				}
			}
		}
		
		return $is_po_add;
	}
	
	public function get_wepo_options_str($wc_items){
		$ext_opt_str = '';
		if(is_array($wc_items) && count($wc_items)){
			if($this->is_plugin_active('woo-extra-product-options') && $this->option_checked('mw_wc_qbo_desk_compt_woo_ext_prd_opt_pd')){
				$wrpo_fields = array();
				$thwepof_custom_product_fields = get_option('thwepof_custom_product_fields');
				if(is_array($thwepof_custom_product_fields) && count($thwepof_custom_product_fields)){
					foreach($thwepof_custom_product_fields as $tcpf){
						if(is_array($tcpf) && count($tcpf)){
							foreach($tcpf as $tcpf_ck => $tcpf_cv){
								if(is_object($tcpf_cv) && isset($tcpf_cv->title) && !empty($tcpf_cv->title)){
									$wrpo_fields[$tcpf_ck] = trim($tcpf_cv->title);
								}
							}
						}
					}
				}
				
				if(count($wrpo_fields)){
					$wrpo_str = '';
					foreach($wc_items as $wk => $wv){
						if(isset($wrpo_fields[$wk])){
							$wrpo_str.=$wrpo_fields[$wk].': '.$wv.PHP_EOL;
						}
					}
					
					if($wrpo_str!=''){
						$ext_opt_str.=PHP_EOL.$wrpo_str;
					}
				}
			}
		}		
		return $ext_opt_str;
	}
	
	public function get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2,$tax_details=array(),$invoice_data=array()){
		$qbo_tax_code = '';
		global $wpdb;
		$tax_map_table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_tax';

		$tax_map_data = $this->get_row($wpdb->prepare("SELECT `qbo_tax_code` FROM ".$tax_map_table." WHERE `wc_tax_id` = %d AND `wc_tax_id_2` = %d ",$tax_rate_id,$tax_rate_id_2));

		if(is_array($tax_map_data) && count($tax_map_data)){
			$qbo_tax_code = $tax_map_data['qbo_tax_code'];
		}
		return $qbo_tax_code;
	}

	public function mod_qbo_get_tx_dtls($qbo_tax_code=''){
		$tx_dtls = array();
		if($qbo_tax_code!='' && $this->is_qwc_connected()){
			global $wpdb;
			$tc_table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode';
			$tc_data = $this->get_row($wpdb->prepare("SELECT * FROM ".$tc_table." WHERE `qbd_id` = %s ",$qbo_tax_code));
			if(is_array($tc_data) && count($tc_data)){
				$tx_dtls['Name'] = $tc_data['name'];
				$tx_dtls['Active'] = ($tc_data['is_active']==1)?true:false;
				$tx_dtls['Taxable'] = ($tc_data['is_taxable']==1)?true:false;
				$tx_dtls['TaxGroup'] = $tc_data['stc_desc'];
				$tx_dtls['TaxRateDetail'] = array();
			}
		}
		return $tx_dtls;
	}

	public function get_qbo_tax_rate_value_by_key($qbo_tax_rate_code='',$key="RateValue"){
		if(!$this->is_qwc_connected()){
			return;
		}
		$return =($key=="RateValue")?0:'';
		if($qbo_tax_rate_code!=''){

		}
		return $return;
	}

	public function get_tax_type($prices_include_tax='no'){
		if($prices_include_tax=='yes'){
			//return 'TaxInclusive';
		}
		return $this->get_option('mw_wc_qbo_desk_tax_format');
	}

	public function is_tax_inclusive($tax_type=''){
		$tax_type = ($tax_type=='')?$this->get_tax_type():$tax_type;
		return ($tax_type=='TaxInclusive')?true:false;
	}
	
	//New
	public function get_smd_s_opts($sm_key){
		/**/
		if($sm_key == 'wf_multi_carrier_shipping'){
			return $this->wcmcsps_gsmd_sopts();
		}
		
		if($sm_key == 'wf_shipping_ups'){
			return $this->wcups_instance_f_arr();
		}
		
		$smt_ra = array();
		$sm_key = $this->sanitize($sm_key);
		global $wpdb;
		if($sm_key!=''){
			$sqk = 'woocommerce_'.$sm_key.'_';
			$sm_id_data = $this->get_data("SELECT * FROM ".$wpdb->options." WHERE `option_name` LIKE '%{$sqk}%' AND `option_value` !='' ");
			if(is_array($sm_id_data) && count($sm_id_data)){
				foreach($sm_id_data as $smov){
					if(!empty($smov['option_value'])){
						$smok = $smov['option_name'];						
						if(strpos( $smok, $sqk ) !== false && strpos( $smok, '_settings' ) !== false){
							$sm_id_p = (int) ((int) strlen($smok)-((int) strlen($sqk) + (int) strlen('_settings')));							
							$sm_id = substr($smok,strlen($sqk),$sm_id_p);							
							if(is_numeric($sm_id)){
								$sm_id = (int) $sm_id;
								if($sm_id>0){
									$smov_a = @unserialize($smov['option_value']);
									if(is_array($smov_a) && count($smov_a) && isset($smov_a['title'])){
										$smov_a['sm_id'] = $sm_id;
										$smt_ra[] = $smov_a;
									}
								}
							}
						}
					}					
				}
			}
		}
		return $smt_ra;
	}
	
	/**/
	private function wcmcsps_gsmd_sopts($r_ms_title=false){
		$smt_ra = array();
		if($this->check_sh_wcmcsps_hash()){
			$wf_mcss = get_option('woocommerce_wf_multi_carrier_shipping_settings');
			if(is_array($wf_mcss) && !empty($wf_mcss) && isset($wf_mcss['rate_matrix']) && is_array($wf_mcss['rate_matrix']) && !empty($wf_mcss['rate_matrix'])){
				/**/
				if($r_ms_title){
					return (isset($wf_mcss['title']))?$wf_mcss['title']:'';
				}
				
				foreach($wf_mcss['rate_matrix'] as $rm_k => $rm_v){
					$smov_a = array();
					
					$smov_a['title'] = $rm_v['shipping_name'];
					$smov_a['tax_status'] = '';
					$smov_a['cost'] = $rm_v['fee'];
					
					$smov_a['sm_id'] = $rm_k;
					
					$smov_a['cost_per_unit'] = $rm_v['cost_per_unit'];
					$smov_a['shipping_companies'] = $rm_v['shipping_companies'];
					$smov_a['shipping_services'] = $rm_v['shipping_services'];
					
					$smt_ra[] = $smov_a;
				}
			}
		}
		
		return $smt_ra;
	}
	
	public function wc_get_sm_data_from_method_id_str($method_id='',$key='',$sd_v=array()){
		$shipping_method = '';
		if($method_id!=''){
			/**/
			if($method_id == 'wf_multi_carrier_shipping' && is_array($sd_v) && !empty($sd_v) && $this->check_sh_wcmcsps_hash()){
				if($key=='id'){
					$sm_id = 0;
					if(isset($sd_v['name']) && !empty($sd_v['name']) && $sd_v['name'] != $this->wcmcsps_gsmd_sopts(true)){
						$smd_mc = $this->wcmcsps_gsmd_sopts();
						if(is_array($smd_mc) && !empty($smd_mc)){
							foreach($smd_mc as $mci){
								if(is_array($mci) && isset($mci['sm_id']) && (int) $mci['sm_id'] > 0 && isset($mci['title']) && !empty($mci['title'])){
									if($sd_v['name'] == $mci['title']){
										return (int) $mci['sm_id'];
									}
								}
							}
						}
					}
					return $sm_id;
				}				
				return $method_id;
			}
			
			/**/
			if($method_id == 'wf_shipping_ups'){
				if($key=='id' && is_array($sd_v) && !empty($sd_v)){
					$sm_id = '';
					$si_name = $sd_v['name'];
					if(!empty($si_name)){
						$ups_il_arr = $this->get_ups_sm_instance_list();
						if(is_array($ups_il_arr) && !empty($ups_il_arr)){
							foreach($ups_il_arr as $k => $v){
								if($v.' (UPS)' == $si_name){
									return $k;
								}
							}
						}
					}
					
					return $sm_id;
				}
				return $method_id;
			}
			
			if(strpos( $method_id, ':' ) !== false){
				$shipping_method = substr($method_id, 0, strpos($method_id, ":"));
				//New
				if($key=='id'){
					$sm_id = substr($method_id, strpos($method_id, ":")+1);
					return $sm_id;
				}
				
			}else{
				$sm_arr = explode('_',$method_id);
				if(is_array($sm_arr) && count($sm_arr)>2){
					$sm_count = count($sm_arr);

					$sm_id_index = (int) ($sm_count-2);
					$sm_id = 0;
					if(isset($sm_arr[$sm_id_index]) && is_numeric($sm_arr[$sm_id_index])){
						$sm_id = (int) $sm_arr[$sm_id_index];
						//New
						if($key=='id'){
							return $sm_id;
						}
						unset($sm_arr[$sm_id_index]);
					}


					$sm_reg_id_index = (int) ($sm_count-1);
					$sm_reg_id = 0;
					if(isset($sm_arr[$sm_reg_id_index]) && is_numeric($sm_arr[$sm_reg_id_index])){
						$sm_reg_id = (int) $sm_arr[$sm_reg_id_index];
						unset($sm_arr[$sm_reg_id_index]);
					}
					$shipping_method = implode('_',$sm_arr);
				}else{
					//
					$shipping_method = $method_id;
				}
			}
		}
		return $shipping_method;
	}
	
	public function get_wc_smt_data_from_sm_and_id($sm,$sm_id){
		$sm_data = array();
		$sm = trim($sm);
		$sm_id = (int) $sm_id;
		if($sm!='' && $sm_id>0){
			$sm_opt_str = 'woocommerce_'.$sm.'_'.$sm_id.'_settings';
			$sm_opt_val = get_option($sm_opt_str);
			if(!empty($sm_opt_val)){
				$sm_data = (array) $sm_opt_val;
			}
		}
		return $sm_data;
	}
	
	public function get_mapped_shipping_product($wc_shippingmethod='',$sd_v=array(),$is_instance=false){
		//echo $wc_shippingmethod;
		global $wpdb;
		$qbo_shipping_product = array();
		$qbo_shipping_product['ItemRef'] = $this->get_option('mw_wc_qbo_desk_default_shipping_product');

		if($wc_shippingmethod=='no_method_found'){
			return $qbo_shipping_product;
		}
		
		$wc_shippingmethod = $this->sanitize($wc_shippingmethod);
		/**/
		$is_custom_map_applied = false;
		//$this->_p($wc_shippingmethod);$this->_p($sd_v);
		if(is_array($sd_v) && !empty($sd_v) && $this->option_checked('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp')){
			if(isset($sd_v['name']) && !empty($sd_v['name'])){
				$csma = $this->get_custom_shipping_map_data_from_name($sd_v['name']);
				//$this->_p($csma);
				if(is_array($csma) && !empty($csma) && isset($csma['qb_product']) && !empty($csma['qb_product'])){
					$qbo_shipping_product['ItemRef'] = $csma['qb_product'];
					$qbo_shipping_product['ClassRef'] = $csma['qb_class'];
					$qbo_shipping_product['qb_shipmethod_id'] = $csma['qb_sv'];					
					$is_custom_map_applied = true;
				}
			}
		}
		
		if($wc_shippingmethod!='' && !$is_custom_map_applied){
			$map_data = $this->get_row($wpdb->prepare("SELECT `qbo_product_id` , `class_id` , `qb_shipmethod_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_shipping_product` WHERE `wc_shippingmethod` = %s AND  `qbo_product_id` !='' ",$wc_shippingmethod));
			if(is_array($map_data) && count($map_data)){
				$qbo_shipping_product['ItemRef'] = $map_data['qbo_product_id'];
				
				/**/
				$qbo_shipping_product['ClassRef'] = (isset($map_data['class_id']))?$map_data['class_id']:'';
				if(empty($qbo_shipping_product['ClassRef'])){
					$qbo_shipping_product['ClassRef'] = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
				}
				
				$qbo_shipping_product['qb_shipmethod_id'] = $map_data['qb_shipmethod_id'];
			}
		}
		return $qbo_shipping_product;
	}

	public function get_mapped_coupon_product($wc_couponcode=''){
		global $wpdb;
		$promo_id = 0;
		$description = '';

		$qbo_coupon_product = array();
		$qbo_coupon_product['ItemRef'] = $this->get_option('mw_wc_qbo_desk_default_coupon_code');
		$wc_couponcode = $this->sanitize($wc_couponcode);
		
		if($wc_couponcode!=''){
			$promo_data = $this->get_row($wpdb->prepare("SELECT `ID` , `post_excerpt` FROM `".$wpdb->posts."` WHERE `post_type` = 'shop_coupon' AND `post_title` = %s ",$wc_couponcode));
			if(is_array($promo_data) && count($promo_data)){
				$promo_id = (int) $promo_data['ID'];
				$description = $promo_data['post_excerpt'];
			}

			$map_data = $this->get_row($wpdb->prepare("SELECT `qbo_product_id` , `class_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_promo_code` WHERE `promo_id` = %s AND  `qbo_product_id` !='' ",$promo_id));
			if(is_array($map_data) && count($map_data)){
				$qbo_coupon_product['ItemRef'] = $map_data['qbo_product_id'];
				
				/**/
				$qbo_coupon_product['ClassRef'] = (isset($map_data['class_id']))?$map_data['class_id']:'';
				if(empty($qbo_coupon_product['ClassRef'])){
					$qbo_coupon_product['ClassRef'] = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
				}
			}
		}
		$qbo_coupon_product['Description'] = 'Coupon: '.$wc_couponcode;
		$qbo_coupon_product['Description'] = $this->get_array_isset($qbo_coupon_product,'Description');
		
		/*New*/
		$qbo_coupon_product['qbo_product_type'] = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','product_type','qbd_id',$qbo_coupon_product['ItemRef']);
		
		return $qbo_coupon_product;
	}
	
	public function get_cf_map_data(){
		$rd = array();
		global $wpdb;
		$cmd = $this->get_tbl($wpdb->prefix.'mw_wc_qbo_desk_qbd_map_wq_cf');
		if(is_array($cmd) && count($cmd)){
			foreach($cmd as $row){
				$rd[$row['wc_field']] = $row['qb_field'];
			}
		}
		return $rd;
	}
	
	public function get_class_dropdown_list($s_val='',$txl_lavel=false){
		$options = '';
		/*
		if(!$txl_lavel){
			if(!empty($this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class'))){
				return $options;
			}
		}
		*/
		global $wpdb;
		$options = $this->option_html($s_val, $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_class','qbd_id','name','','name ASC','',true);
		return $options;
	}
	
	public function get_wc_tax_rate_dropdown($wc_tax_rates,$selected='',$skip_rate_id='',$skip_rate_class='None'){
		$options='<option value=""></option>';
		if(is_array($wc_tax_rates) && count($wc_tax_rates)){
			foreach($wc_tax_rates as $rates){
				if($skip_rate_id!=$rates['tax_rate_id'] && $skip_rate_class!=$rates['tax_rate_class']){
					$options.='<option  data-tax_rate_country="'.$rates['tax_rate_country'].'"  data-tax_rate_state="'.$rates['tax_rate_state'].'"  data-tax_rate="'.$rates['tax_rate'].'"  data-tax_rate_name="'.$rates['tax_rate_name'].'"  data-tax_rate_priority="'.$rates['tax_rate_priority'].'"  data-tax_rate_compound="'.$rates['tax_rate_compound'].'"  data-tax_rate_shipping="'.$rates['tax_rate_shipping'].'" data-tax_rate_order="'.$rates['tax_rate_order'].'" data-tax_rate_class="'.$rates['tax_rate_class'].'" data-tax_rate_city="'.$rates['location_code'].'" value="'.$rates['tax_rate_id'].'">'.$rates['tax_rate_name'].'</option>';
				}
			}
		}
		return $options;
	}
	
	public function get_wc_tax_rate_id_array($wc_tax_rates){
		$tx_rate_arr = array();
		if(is_array($wc_tax_rates) && count($wc_tax_rates)){
			foreach($wc_tax_rates as $rates){
				$tx_rate_arr[$rates['tax_rate_id']] = $rates;
			}
		}
		return $tx_rate_arr;
	}

	public function get_qbo_zero_rated_tax_code($country=''){
		if($country==''){
			$country = $this->get_qbo_company_info('country');
		}
		$qbo_tax_code = '';
		if($this->is_qwc_connected()){
			if($country=='US'){
				//$qbo_tax_code = 'NON';
				$qbo_tax_code = $this->get_option('mw_wc_qbo_desk_tax_rule');
			}else{
				$qbo_tax_code = $this->get_option('mw_wc_qbo_desk_tax_rule');
			}
		}
		return $qbo_tax_code;
	}
	
	//
	public function get_qbd_cus_cf_map_fl(){
		$qccf = array(
			'Notes' => 'Notes',
			'AccountNumber' => 'AccountNumber',
			'Source' => 'Source',
			'CustomerNamePrefix' => 'Customer Name Prefix',
			//'' => '',			
		);
		
		return $qccf;
	}
	
	//
	public function get_compt_wc_ccf_map_fl(){
		$ccf_l = array(			
			'ID' => 'WooCommerce User ID',
			'gf_officeContactFirstName' => 'gf_officeContactFirstName',
			'gf_officeContactJobTitle' => 'gf_officeContactJobTitle',
			'gf_officeContactLastName' => 'gf_officeContactLastName',
			'gf_officeName' => 'gf_officeName',
			'gf_officeOfficePhoneNumber' => 'gf_officeOfficePhoneNumber',
			'gf_userHowDidYouHear' => 'gf_userHowDidYouHear',
			'gf_userTitle' => 'gf_userTitle',
		);
		//gf_userSpecialty
		return $ccf_l;
	}
	
	//
	public function cus_meta_usk(){
		global $wpdb;
		$us_k = array();
		$us_k[] = 'rich_editing';
		$us_k[] = 'syntax_highlighting';
		$us_k[] = 'comment_shortcuts';
		$us_k[] = 'admin_color';
		$us_k[] = 'use_ssl';
		$us_k[] = 'show_admin_bar_front';
		$us_k[] = 'locale';
		$us_k[] = $wpdb->prefix.'capabilities';
		$us_k[] = $wpdb->prefix.'user_level';
		$us_k[] = 'session_tokens';
		$us_k[] = 'last_update';
		$us_k[] = '_stripe_customer_id';
		$us_k[] = '_woocommerce_persistent_cart_1';
		
		return $us_k ;
	}
	
	//
	public function get_wc_user_meta_key_list(){
		$cmk = array();
		$args = array('role__in'=>array('customer'),'number'=>1,'orderby'=>'ID','order'=>'DESC');
		$wu = get_users($args);		
		if(is_array($wu) && count($wu) && isset($wu[0]->ID)){
			$customer_id = $wu[0]->ID;
			$cm = get_user_meta($customer_id);
			if(is_array($cm) && count($cm)){
				foreach($cm as $k=>$v){
					$cmk[$k] = $k;
				}
			}
		}
		
		$us_k = $this->cus_meta_usk();
		if(is_array($cmk) && count($cmk) && is_array($us_k) && count($us_k)){
			foreach($us_k as $v){
				if(isset($cmk[$v])){
					unset($cmk[$v]);
				}
			}
		}
		
		return $cmk;
	}
	
	/*List Functions*/
	public function get_users_id_name_list_by_roles($roles){
		global $wpdb;
		$u_id_name_arr = array();
		$roles = trim($roles);
		if(!empty($roles)){
			$roles = $roles;
			if(!is_array( $roles )){
				$roles = array_map('trim',explode( ",", $roles ));
			}
			
			$ext_join = '';
			$ext_whr = '';
			
			$ext_whr .= ' AND     (';
			$i = 1;
			foreach ( $roles as $role ) {
				$ext_whr .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%"' . $role . '"%\' ';
				if ( $i < count( $roles ) ) $ext_whr .= ' OR ';
				$i++;
			}
			$ext_whr .= ' ) ';
			
			$sql = '
				SELECT  DISTINCT(' . $wpdb->users . '.ID) , ' . $wpdb->users . '.display_name, ' . $wpdb->users . '.user_email
				FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
				ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
				'.$ext_join.'
				WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'				
			';
			
			$sql .= $ext_whr;
			
			$orderby = $wpdb->users.'.display_name ASC';
			$sql .= ' ORDER BY  '.$orderby;
			
			//echo $sql;
			$q_data =  $this->get_data($sql);
			//$this->_p($q_data);
			
			if(is_array($q_data) && count($q_data)){
				foreach($q_data as $rd){
					$c_meta = get_user_meta($rd['ID']);
					$fn = (is_array($c_meta) && isset($c_meta['first_name'][0]))?$c_meta['first_name'][0]:'';
					$ln = (is_array($c_meta) && isset($c_meta['last_name'][0]))?$c_meta['last_name'][0]:'';
					$fn_ln = trim($fn.' '.$ln);
					if(empty($fn_ln)){
						$fn_ln = $rd['display_name'];
					}
					
					$u_id_name_arr[$rd['ID']] = $fn_ln;
				}
			}
		}
		return $u_id_name_arr;
	}
	
	public function count_customers($search_txt='',$list_page=false,$cl_role_search='',$cl_um_srch='') {
		global $wpdb;
		
		$roles = 'customer';
		
		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}
		
		$cl_role_search = $this->sanitize($cl_role_search);
		if(!empty($cl_role_search)){
			$roles = $cl_role_search;
		}
		
		if(!is_array( $roles )){
			$roles = array_map('trim',explode( ",", $roles ));
		}
		
		$ext_join = '';
		$ext_whr = '';
		
		$ext_whr .= ' AND     (';
		$i = 1;
		foreach ( $roles as $role ) {
			$ext_whr .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%"' . $role . '"%\' ';
			if ( $i < count( $roles ) ) $ext_whr .= ' OR ';
			$i++;
		}
		$ext_whr .= ' ) ';
		
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um3 ON ( um3.user_id = ' . $wpdb->users . '.ID
			AND um3.meta_key =  \'billing_company\' ) ';
			
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um1 ON ( um1.user_id = ' . $wpdb->users . '.ID
			AND um1.meta_key =  \'first_name\' ) ';
			
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um2 ON ( um2.user_id = ' . $wpdb->users . '.ID
			AND um2.meta_key =  \'last_name\' ) ';
			
			$ext_whr .= $wpdb->prepare(" AND (".$wpdb->users.".display_name LIKE '%%%s%%' OR ".$wpdb->users.".user_email LIKE '%%%s%%' OR um3.meta_value LIKE '%%%s%%' OR ".$wpdb->users.".ID = %s OR um1.meta_value LIKE '%%%s%%' OR um2.meta_value LIKE '%%%s%%' OR CONCAT(um1.meta_value,' ', um2.meta_value) LIKE '%%%s%%' ) ", $search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		
		$cl_um_srch = $this->sanitize($cl_um_srch);
		if($cl_um_srch == 'only_m'){
			$ext_join .= '
			INNER JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON ' . $wpdb->users . '.ID = ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid';
		}
		
		if($cl_um_srch == 'only_um'){
			$ext_join .= ' 
			LEFT JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON ' . $wpdb->users . '.ID = ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid ';
			
			$ext_whr .= " AND ({$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid IS NULL OR {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = '') ";
		}
		
		$sql = '
			SELECT  COUNT(DISTINCT(' . $wpdb->users . '.ID))
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			'.$ext_join.'
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'				
		';
		
		$sql .= $ext_whr;
		
		//echo $sql;		
		return $wpdb->get_var($sql);		
	}
	
	public function count_customers_old($search_txt='',$list_page=false,$cl_role_search='',$cl_um_srch='') {
		$roles = 'customer'; // we can use multiple role comma separeted
		global $wpdb;
		
		//if(!$list_page){}
		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}
		
		/**/
		$cl_role_search = $this->sanitize($cl_role_search);
		if(!empty($cl_role_search)){
			$roles = $cl_role_search;
		}
		
		/**/
		$mu_whr = '';
		$mu_jt = 'LEFT';
		$cl_um_srch = $this->sanitize($cl_um_srch);
		if($cl_um_srch == 'only_m'){
			$mu_jt = 'INNER';
		}

		if($cl_um_srch == 'only_um'){
			$mu_whr = " AND ({$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid IS NULL OR {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = '')";
		}		
		
		if ( ! is_array( $roles ) )
			$roles = array_map('trim',explode( ",", $roles ));
		$sql = '
			SELECT  COUNT(DISTINCT(' . $wpdb->users . '.ID))
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			LEFT JOIN ' . $wpdb->usermeta . ' um1 ON ( um1.user_id = ' . $wpdb->users . '.ID
			AND um1.meta_key =  \'first_name\' )
			LEFT JOIN ' . $wpdb->usermeta . ' um2 ON ( um2.user_id = ' . $wpdb->users . '.ID
			AND um2.meta_key =  \'last_name\' )
			LEFT JOIN ' . $wpdb->usermeta . ' um3 ON ( um3.user_id = ' . $wpdb->users . '.ID
			AND um3.meta_key =  \'billing_company\' )
			'.$mu_jt.' JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON          ' . $wpdb->users . '.ID             =       ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid
			LEFT JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers qc ON ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = qc.qbd_customerid
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'
			AND     (
		';
		$i = 1;
		foreach ( $roles as $role ) {
			$sql .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%"' . $role . '"%\' ';
			if ( $i < count( $roles ) ) $sql .= ' OR ';
			$i++;
		}
		$sql .= ' ) ';

		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND (".$wpdb->users.".display_name LIKE '%%%s%%' OR ".$wpdb->users.".user_email LIKE '%%%s%%' OR um3.meta_value LIKE '%%%s%%' ) ";
		}
		
		//
		$sql .= $mu_whr;

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
	}
	
	public function get_customers($search_txt='',$limit='',$list_page=false,$cl_role_search='',$cl_um_srch='') {
		global $wpdb;
		
		$roles = 'customer';
		
		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}
		
		$cl_role_search = $this->sanitize($cl_role_search);
		if(!empty($cl_role_search)){
			$roles = $cl_role_search;
		}
		
		if(!is_array( $roles )){
			$roles = array_map('trim',explode( ",", $roles ));
		}
		
		$ext_join = '';
		$ext_whr = '';
		
		$ext_whr .= ' AND     (';
		$i = 1;
		foreach ( $roles as $role ) {
			$ext_whr .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%"' . $role . '"%\' ';
			if ( $i < count( $roles ) ) $ext_whr .= ' OR ';
			$i++;
		}
		$ext_whr .= ' ) ';
		
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um3 ON ( um3.user_id = ' . $wpdb->users . '.ID
			AND um3.meta_key =  \'billing_company\' ) ';
			
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um1 ON ( um1.user_id = ' . $wpdb->users . '.ID
			AND um1.meta_key =  \'first_name\' ) ';
			
			$ext_join .= ' LEFT JOIN ' . $wpdb->usermeta . ' um2 ON ( um2.user_id = ' . $wpdb->users . '.ID
			AND um2.meta_key =  \'last_name\' ) ';
			
			$ext_whr .= $wpdb->prepare(" AND (".$wpdb->users.".display_name LIKE '%%%s%%' OR ".$wpdb->users.".user_email LIKE '%%%s%%' OR um3.meta_value LIKE '%%%s%%' OR ".$wpdb->users.".ID = %s OR um1.meta_value LIKE '%%%s%%' OR um2.meta_value LIKE '%%%s%%' OR CONCAT(um1.meta_value,' ', um2.meta_value) LIKE '%%%s%%' ) ", $search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		
		$cl_um_srch = $this->sanitize($cl_um_srch);
		if($cl_um_srch == 'only_m'){
			$ext_join .= '
			INNER JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON ' . $wpdb->users . '.ID = ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid';
		}
		
		if($cl_um_srch == 'only_um'){
			$ext_join .= ' 
			LEFT JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON ' . $wpdb->users . '.ID = ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid ';
			
			$ext_whr .= " AND ({$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid IS NULL OR {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = '') ";
		}
		
		$sql = '
			SELECT  DISTINCT(' . $wpdb->users . '.ID) , ' . $wpdb->users . '.display_name, ' . $wpdb->users . '.user_email
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			'.$ext_join.'
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'				
		';
		
		$sql .= $ext_whr;
		
		$orderby = $wpdb->users.'.display_name ASC';
		$sql .= ' ORDER BY  '.$orderby;
		
		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}
		
		//echo $sql;
		$r_data = array();
		$q_data =  $this->get_data($sql);
		//$this->_p($q_data);
		
		if(is_array($q_data) && count($q_data)){
			foreach($q_data as $rd){
				$cu_tmp_arr = array();
				$cu_tmp_arr['ID'] = $rd['ID'];
				$cu_tmp_arr['display_name'] = $rd['display_name'];
				$cu_tmp_arr['user_email'] = $rd['user_email'];
				
				$c_meta = get_user_meta($rd['ID']);		
				$cu_tmp_arr['first_name'] = (is_array($c_meta) && isset($c_meta['first_name'][0]))?$c_meta['first_name'][0]:'';
				$cu_tmp_arr['last_name'] = (is_array($c_meta) && isset($c_meta['last_name'][0]))?$c_meta['last_name'][0]:'';
				
				$cu_tmp_arr['billing_company'] = (is_array($c_meta) && isset($c_meta['billing_company'][0]))?$c_meta['billing_company'][0]:'';
				
				$ext_cq = "
				SELECT cmap.qbd_customerid, qc.d_name as `qbo_dname`, qc.email as `qbo_email`
				FROM " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_customers_pairs cmap		
				LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_customers qc ON cmap.qbd_customerid = qc.qbd_customerid
				WHERE cmap.wc_customerid = ".$rd['ID']."
				AND (cmap.qbd_customerid IS NOT NULL OR cmap.qbd_customerid != '')
				LIMIT 0,1
				";
				
				$ext_data =  $this->get_row($ext_cq);
				$cu_tmp_arr['qbd_customerid'] = (is_array($ext_data) && isset($ext_data['qbd_customerid']))?$ext_data['qbd_customerid']:'';
				$cu_tmp_arr['qbo_dname'] = (is_array($ext_data) && isset($ext_data['qbo_dname']))?$ext_data['qbo_dname']:'';
				$cu_tmp_arr['qbo_email'] = (is_array($ext_data) && isset($ext_data['qbo_email']))?$ext_data['qbo_email']:'';
				
				$r_data[] = $cu_tmp_arr;
			}
		}
		
		unset($q_data);
		//$this->_p($r_data);
		return $r_data;		
	}
	
	public function get_customers_old($search_txt='',$limit='',$list_page=false,$cl_role_search='',$cl_um_srch='') {
		$roles = 'customer'; // we can use multiple role comma separeted
		global $wpdb;
		//if(!$list_page){}
		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}
		
		/**/
		$cl_role_search = $this->sanitize($cl_role_search);
		if(!empty($cl_role_search)){
			$roles = $cl_role_search;
		}
		
		/**/
		$mu_whr = '';
		$mu_jt = 'LEFT';
		$cl_um_srch = $this->sanitize($cl_um_srch);
		if($cl_um_srch == 'only_m'){
			$mu_jt = 'INNER';
		}

		if($cl_um_srch == 'only_um'){
			$mu_whr = " AND ({$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid IS NULL OR {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = '')";
		}	
		
		if ( ! is_array( $roles ) )
			//$roles = array_walk( explode( ",", $roles ), 'trim' );
			$roles = array_map('trim',explode( ",", $roles ));
			//$this->_p($roles);
		$sql = '
			SELECT  DISTINCT(' . $wpdb->users . '.ID), ' . $wpdb->users . '.display_name, ' . $wpdb->users . '.user_email, ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid, um1.meta_value AS first_name, um2.meta_value AS last_name, um3.meta_value AS billing_company, qc.d_name as `qbo_dname`, qc.email as `qbo_email`
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			LEFT JOIN ' . $wpdb->usermeta . ' um1 ON ( um1.user_id = ' . $wpdb->users . '.ID
			AND um1.meta_key =  \'first_name\' )
			LEFT JOIN ' . $wpdb->usermeta . ' um2 ON ( um2.user_id = ' . $wpdb->users . '.ID
			AND um2.meta_key =  \'last_name\' )
			LEFT JOIN ' . $wpdb->usermeta . ' um3 ON ( um3.user_id = ' . $wpdb->users . '.ID
			AND um3.meta_key =  \'billing_company\' )
			'.$mu_jt.' JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs
			ON          ' . $wpdb->users . '.ID             =       ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.wc_customerid
			LEFT JOIN ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers qc ON ' . $wpdb->prefix . 'mw_wc_qbo_desk_qbd_customers_pairs.qbd_customerid = qc.qbd_customerid
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'
			AND     (
		';
		$i = 1;
		foreach ( $roles as $role ) {
			$sql .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%%"' . $role . '"%%\' ';
			if ( $i < count( $roles ) ) $sql .= ' OR ';
			$i++;
		}
		$sql .= ' ) ';

		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			//$sql .=" AND ".$wpdb->users.".display_name LIKE '%".$search_txt."%' ";
			$sql .=" AND (".$wpdb->users.".display_name LIKE '%%%s%%' OR ".$wpdb->users.".user_email LIKE '%%%s%%' OR um3.meta_value LIKE '%%%s%%' ) ";
		}
		
		//
		$sql .= $mu_whr;

		//
		$sql.=' GROUP BY '. $wpdb->users . '.ID';

		$orderby = $wpdb->users.'.display_name ASC';
		$sql .= ' ORDER BY  '.$orderby;


		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;

		return $this->get_data($sql);
	}
	
	public function count_woocommerce_variation_list($search_txt='',$is_inventory=false,$stock_status='',$variation_um_srch='') {
		global $wpdb;

		$search_txt = $this->sanitize($search_txt);
		$ext_join = '';
		$ext_sql = '';
		
		if($search_txt!=''){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID	AND pm1.meta_key =  '_sku' )";
			$ext_join.= "LEFT JOIN " . $wpdb->posts . " p1 ON p.post_parent = p1.ID";
		}
		
		$stock_status = $this->sanitize($stock_status);
		if($stock_status!=''){
			$ext_join.= " LEFT JOIN ".$wpdb->postmeta." pm7 ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_stock_status' ) ";
			
		}
		
		if($is_inventory){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm8 ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_manage_stock')";
		}
		
		$variation_um_srch = $this->sanitize($variation_um_srch);
		if($variation_um_srch == 'only_m'){
			$ext_join.= " INNER JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id";
		}
		
		if($variation_um_srch == 'only_um'){
			$ext_join.= " LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id";
			$ext_sql.= " AND (pmap.quickbook_product_id IS NULL OR pmap.quickbook_product_id = '')";
		}
		
		$sql = "
		SELECT COUNT(DISTINCT(p.ID))
		FROM ".$wpdb->posts." p
		{$ext_join}

		WHERE p.post_type =  'product_variation'
		AND p.post_status NOT IN('trash','auto-draft','inherit')
		{$ext_sql}
		";
		
		if($is_inventory){
			$sql .=" AND pm8.meta_value='yes' ";
		}
		if($search_txt!=''){
			$sql .=" AND ( p.post_title LIKE '%%%s%%' OR p1.post_title LIKE '%%%s%%' OR pm1.meta_value LIKE '%%%s%%' ) ";
		}
		
		
		if($stock_status!=''){
			$sql.= " AND pm7.meta_value='{$stock_status}' ";
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
	}
	
	public function get_woocommerce_variation_list($search_txt='',$is_inventory=false,$limit='',$stock_status='',$variation_um_srch='') {
		global $wpdb;

		$search_txt = $this->sanitize($search_txt);
		$ext_join = '';
		$ext_sql = '';
		
		if($search_txt!=''){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID	AND pm1.meta_key =  '_sku' )";
			$ext_join.= "LEFT JOIN " . $wpdb->posts . " p1 ON p.post_parent = p1.ID";
		}
		
		$stock_status = $this->sanitize($stock_status);
		if($stock_status!=''){
			$ext_join.= " LEFT JOIN ".$wpdb->postmeta." pm7 ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_stock_status' ) ";
			
		}
		
		if($is_inventory){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm8 ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_manage_stock')";
		}
		
		$variation_um_srch = $this->sanitize($variation_um_srch);
		if($variation_um_srch == 'only_m'){
			$ext_join.= " INNER JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id";
		}
		
		if($variation_um_srch == 'only_um'){
			$ext_join.= " LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id";
			$ext_sql.= " AND (pmap.quickbook_product_id IS NULL OR pmap.quickbook_product_id = '')";
		}
		
		$sql = "
		SELECT DISTINCT(p.ID), p.post_title AS name, p.post_parent as parent_id, p.post_name
		FROM ".$wpdb->posts." p
		{$ext_join}

		WHERE p.post_type =  'product_variation'
		AND p.post_status NOT IN('trash','auto-draft','inherit')
		{$ext_sql}
		";
		
		if($is_inventory){
			$sql .=" AND pm8.meta_value='yes' ";
		}
		if($search_txt!=''){
			$sql .=" AND ( p.post_title LIKE '%%%s%%' OR p1.post_title LIKE '%%%s%%' OR pm1.meta_value LIKE '%%%s%%' ) ";
		}
		
		
		if($stock_status!=''){
			$sql.= " AND pm7.meta_value='{$stock_status}' ";
		}
		
		if($search_txt!=''){
			$orderby = 'p.ID DESC, p1.post_parent ASC';
		}else{
			$orderby = 'p.ID DESC';
		}		
		
		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}
		
		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt);
		}
		
		//echo $sql;
		$r_data = array();
		$q_data =  $this->get_data($sql);
		//$this->_p($q_data);
		
		if(is_array($q_data) && count($q_data)){
			foreach($q_data as $rd){
				$pd_tmp_arr = array();
				$pd_tmp_arr['ID'] = $rd['ID'];
				$pd_tmp_arr['name'] = $rd['name'];
				$pd_tmp_arr['post_name'] = $rd['post_name'];
				$pd_tmp_arr['parent_id'] = $rd['parent_id'];
				
				$pd_tmp_arr['parent_name'] = $this->get_field_by_val($wpdb->posts,'post_title','ID',$rd['parent_id']);
				
				$p_meta = get_post_meta($rd['ID']);
				
				$attribute_names = array();$attribute_values = array();
				if(is_array($p_meta) && !empty($p_meta)){
					foreach($p_meta as $k => $v){
						if($this->start_with($k,'attribute_')){
							$attribute_names[] = $k;$attribute_values[] = $v[0];
						}
					}
				}
				
				$pd_tmp_arr['attribute_names'] = (count($attribute_names))?implode(',',$attribute_names):'';
				$pd_tmp_arr['attribute_values'] = (count($attribute_values))?implode(',',$attribute_values):'';
				
				$pd_tmp_arr['sku'] = (count($p_meta) && isset($p_meta['_sku'][0]))?$p_meta['_sku'][0]:'';
				$pd_tmp_arr['regular_price'] = (count($p_meta) && isset($p_meta['_regular_price'][0]))?$p_meta['_regular_price'][0]:'';
				$pd_tmp_arr['sale_price'] = (count($p_meta) && isset($p_meta['_sale_price'][0]))?$p_meta['_sale_price'][0]:'';
				$pd_tmp_arr['price'] = (count($p_meta) && isset($p_meta['_price'][0]))?$p_meta['_price'][0]:'';
				$pd_tmp_arr['stock'] = (count($p_meta) && isset($p_meta['_stock'][0]))?$p_meta['_stock'][0]:'';
				$pd_tmp_arr['backorders'] = (count($p_meta) && isset($p_meta['_backorders'][0]))?$p_meta['_backorders'][0]:'';
				$pd_tmp_arr['stock_status'] = (count($p_meta) && isset($p_meta['_stock_status'][0]))?$p_meta['_stock_status'][0]:'';
				$pd_tmp_arr['manage_stock'] = (count($p_meta) && isset($p_meta['_manage_stock'][0]))?$p_meta['_manage_stock'][0]:'';
				$pd_tmp_arr['total_sales'] = (count($p_meta) && isset($p_meta['total_sales'][0]))?$p_meta['total_sales'][0]:'';
				
				$ext_cq = "
				SELECT pmap.quickbook_product_id, pmap.class_id, qp.name as qp_name, qp.sku as qp_sku, qp.product_type as qp_product_type, qp.info_arr as info_arr
				FROM ".$wpdb->posts." p
				LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id
				LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_items qp ON pmap.quickbook_product_id = qp.qbd_id
				WHERE p.ID = ".$rd['ID']."
				LIMIT 0,1
				";
				$ext_data =  $this->get_row($ext_cq);
				$pd_tmp_arr['quickbook_product_id'] = (count($ext_data) && isset($ext_data['quickbook_product_id']))?$ext_data['quickbook_product_id']:'';
				$pd_tmp_arr['class_id'] = (count($ext_data) && isset($ext_data['class_id']))?$ext_data['class_id']:'';				
				//$pd_tmp_arr['a_line_item_desc'] = (count($ext_data) && isset($ext_data['a_line_item_desc']))?$ext_data['a_line_item_desc']:0;
				
				//$pd_tmp_arr['qb_ar_acc_id'] = (count($ext_data) && isset($ext_data['qb_ar_acc_id']))?$ext_data['qb_ar_acc_id']:'';

				$pd_tmp_arr['qp_name'] = (count($ext_data) && isset($ext_data['qp_name']))?$ext_data['qp_name']:'';
				$pd_tmp_arr['qp_sku'] = (count($ext_data) && isset($ext_data['qp_sku']))?$ext_data['qp_sku']:'';
				$pd_tmp_arr['qp_product_type'] = (count($ext_data) && isset($ext_data['qp_product_type']))?$ext_data['qp_product_type']:'';
				$pd_tmp_arr['qp_info_arr'] = (count($ext_data) && isset($ext_data['info_arr']))?$ext_data['info_arr']:'';
				
				$r_data[] = $pd_tmp_arr;
			}
		}
		
		unset($q_data);
		//$this->_p($r_data);
		return $r_data;
	}
	
	public function get_woocommerce_variation_list_old($search_txt='',$is_inventory=false,$limit='',$stock_status='',$variation_um_srch='') {
		global $wpdb;
		$ext_whr = '';
		if($is_inventory){
			$ext_whr.=" AND pm8.meta_value='yes' ";
		}		
		
		$mu_jt = 'LEFT';
		$variation_um_srch = $this->sanitize($variation_um_srch);
		if($variation_um_srch == 'only_m'){
			$mu_jt = 'INNER';
		}
		
		if($variation_um_srch == 'only_um'){
			$ext_whr.= " AND (pmap.quickbook_product_id IS NULL OR pmap.quickbook_product_id = '') ";
		}
		
		$sql = "
		SELECT DISTINCT(p.ID), p.post_title AS name, pmap.quickbook_product_id, pmap.class_id, pm1.meta_value AS sku, pm2.meta_value AS regular_price, pm3.meta_value AS sale_price, pm4.meta_value AS price, pm5.meta_value AS stock, pm6.meta_value AS backorders, pm7.meta_value AS stock_status, pm8.meta_value AS manage_stock, pm9.meta_value AS total_sales, p.post_parent as parent_id, p.post_name, p1.post_title AS parent_name, GROUP_CONCAT(pm_attr.meta_key SEPARATOR ',') as attribute_names, GROUP_CONCAT(pm_attr.meta_value  SEPARATOR ',') as attribute_values,
		qp.name as qp_name, qp.sku as qp_sku, qp.product_type as qp_product_type,qp.info_arr as qp_info_arr
		FROM ".$wpdb->posts." p
		LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID
		AND pm1.meta_key =  '_sku' )
		LEFT JOIN ".$wpdb->postmeta." pm2 ON ( pm2.post_id = p.ID
		AND pm2.meta_key =  '_regular_price' )
		LEFT JOIN ".$wpdb->postmeta." pm3 ON ( pm3.post_id = p.ID
		AND pm3.meta_key =  '_sale_price' )
		LEFT JOIN ".$wpdb->postmeta." pm4 ON ( pm4.post_id = p.ID
		AND pm4.meta_key =  '_price' )
		LEFT JOIN ".$wpdb->postmeta." pm5 ON ( pm5.post_id = p.ID
		AND pm5.meta_key =  '_stock' )
		LEFT JOIN ".$wpdb->postmeta." pm6 ON ( pm6.post_id = p.ID
		AND pm6.meta_key =  '_backorders' )
		LEFT JOIN ".$wpdb->postmeta." pm7 ON ( pm7.post_id = p.ID
		AND pm7.meta_key =  '_stock_status' )
		LEFT JOIN ".$wpdb->postmeta." pm8 ON ( pm8.post_id = p.ID
		AND pm8.meta_key =  '_manage_stock' )
		LEFT JOIN ".$wpdb->postmeta." pm9 ON ( pm9.post_id = p.ID
		AND pm9.meta_key =  'total_sales' )
		LEFT JOIN ".$wpdb->postmeta." pm_attr ON ( pm_attr.post_id = p.ID
		AND pm_attr.meta_key LIKE 'attribute_%%' )
		{$mu_jt} JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_variation_pairs pmap ON p.ID = pmap.wc_variation_id
		LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_items qp ON pmap.quickbook_product_id = qp.qbd_id
		LEFT JOIN " . $wpdb->posts . " p1 ON p.post_parent = p1.ID
		WHERE p.post_type =  'product_variation'
		AND p.post_status NOT IN('trash','auto-draft','inherit')
		{$ext_whr}
		";		
		
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( p.post_title LIKE '%%%s%%' OR p1.post_title LIKE '%%%s%%' OR pm1.meta_value LIKE '%%%s%%' ) ";
		}
		
		$stock_status = $this->sanitize($stock_status);
		if($stock_status!=''){
			$sql.= " AND pm7.meta_value='{$stock_status}' ";
		}

		$sql .='GROUP BY p.ID';
		
		$orderby = 'p.ID DESC, p1.post_parent ASC';

		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt);
		}

		//echo $sql;
		return $this->get_data($sql);
	}
	
	public function count_qbd_product_list($search_txt='',$is_inventory=false){
		//
	}
	
	public function get_qbd_product_list($search_txt='',$limit='',$is_inventory=false) {
		//
	}
	
	
	public function count_woocommerce_product_list($search_txt='',$is_inventory=false,$stock_status='',$product_cat_search=0,$product_um_srch='') {
		$status = 'publish';
		global $wpdb;
		$search_txt = $this->sanitize($search_txt);
		
		$stock_status = $this->sanitize($stock_status);
		
		$product_cat_search = (int) $product_cat_search;

		$ext_join = '';
		if($search_txt!=''){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_sku' )";
		}
		
		if($stock_status!=''){
			$ext_join.= " LEFT JOIN ".$wpdb->postmeta." pm7 ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_stock_status' ) ";
			
		}
		
		if($product_cat_search>0){
			$ext_join.= " 
			JOIN   {$wpdb->term_relationships} TR ON p.ID=TR.object_id 
			JOIN   {$wpdb->term_taxonomy} T ON TR.term_taxonomy_id=T.term_taxonomy_id
			JOIN  {$wpdb->terms} TS ON T.term_id = TS.term_id
			";
		}

		if($is_inventory){
			$ext_join.= " LEFT JOIN ".$wpdb->postmeta." pm8 ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_manage_stock')";
		}

		$ext_sql = ($is_inventory)?" AND pm8.meta_value='yes' ":'';
		
		if($stock_status!=''){
			$ext_sql.= " AND pm7.meta_value='{$stock_status}' ";
		}
		
		if($product_cat_search>0){
			$ext_sql.= " AND  T.taxonomy = 'product_cat' AND T.term_id = {$product_cat_search} ";
		}
		
		/**/		
		$product_um_srch = $this->sanitize($product_um_srch);
		if($product_um_srch == 'only_m'){
			$ext_join.= " INNER JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_product_pairs pmap ON p.ID = pmap.wc_product_id";
		}
		
		if($product_um_srch == 'only_um'){
			$ext_join.= " LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_product_pairs pmap ON p.ID = pmap.wc_product_id";
			$ext_sql.= " AND (pmap.quickbook_product_id IS NULL OR pmap.quickbook_product_id = '')";
		}
		
		//
		if($this->option_checked('mw_wc_qbo_desk_hide_vpp_fmp_pages')){
			$ext_sql.= " AND p.ID NOT IN(SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'product_variation' AND post_parent>0) ";
		}
		
		$sql = "
		SELECT COUNT(DISTINCT(p.ID))
		FROM ".$wpdb->posts." p
		{$ext_join}
		WHERE p.post_type =  'product'
		AND p.post_status NOT IN('trash','auto-draft','inherit')
		{$ext_sql}
		";

		if($search_txt!=''){
			$sql .=" AND ( p.post_title LIKE '%%%s%%' OR pm1.meta_value LIKE '%%%s%%' ) ";
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
	}
	
	public function get_woocommerce_product_list($search_txt='',$limit='',$is_inventory=false,$stock_status='',$product_cat_search=0,$product_um_srch='') {
		$status = 'publish';
		global $wpdb;
		$search_txt = $this->sanitize($search_txt);
		
		$stock_status = $this->sanitize($stock_status);
		
		$product_cat_search = (int) $product_cat_search;

		$ext_join = '';
		if($search_txt!=''){
			$ext_join.= "LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_sku' ) ";
		}
		
		if($stock_status!=''){
			$ext_join.= " LEFT JOIN ".$wpdb->postmeta." pm7 ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_stock_status' ) ";
			
		}
		
		if($is_inventory){
			$ext_join.= " LEFT  JOIN ".$wpdb->postmeta." pm8 ON ( pm8.post_id = p.ID	AND pm8.meta_key =  '_manage_stock') ";
		}
		
		if($product_cat_search>0){
			$ext_join.= " 
			JOIN   {$wpdb->term_relationships} TR ON p.ID=TR.object_id 
			JOIN   {$wpdb->term_taxonomy} T ON TR.term_taxonomy_id=T.term_taxonomy_id
			JOIN  {$wpdb->terms} TS ON T.term_id = TS.term_id
			";
		}
		
		$ext_sql = ($is_inventory)?" AND pm8.meta_value='yes' ":'';
		
		if($stock_status!=''){
			$ext_sql.= " AND pm7.meta_value='{$stock_status}' ";
		}
		
		if($product_cat_search>0){
			$ext_sql.= " AND  T.taxonomy = 'product_cat' AND T.term_id = {$product_cat_search} ";
		}
		
		/**/		
		$product_um_srch = $this->sanitize($product_um_srch);
		if($product_um_srch == 'only_m'){
			$ext_join.= " INNER JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_product_pairs pmap ON p.ID = pmap.wc_product_id";
		}
		
		if($product_um_srch == 'only_um'){
			$ext_join.= " LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_product_pairs pmap ON p.ID = pmap.wc_product_id";
			$ext_sql.= " AND (pmap.quickbook_product_id IS NULL OR pmap.quickbook_product_id = '')";
		}
		
		//
		if($this->option_checked('mw_wc_qbo_desk_hide_vpp_fmp_pages')){
			$ext_sql.= " AND p.ID NOT IN(SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'product_variation' AND post_parent>0) ";
		}
		
		$sql = "
		SELECT DISTINCT(p.ID), p.post_title AS name
		FROM ".$wpdb->posts." p
		{$ext_join}
		WHERE p.post_type =  'product'
		AND p.post_status NOT IN('trash','auto-draft','inherit')
		{$ext_sql}
		";

		if($search_txt!=''){
			$sql .=" AND ( p.post_title LIKE '%%%s%%' OR pm1.meta_value LIKE '%%%s%%' ) ";
		}

		$orderby = 'p.post_title ASC';
		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt);
		}
		
		//echo $sql;
		$r_data = array();
		$q_data =  $this->get_data($sql);
		//$this->_p($q_data);

		if(is_array($q_data) && count($q_data)){
			foreach($q_data as $rd){
				$pd_tmp_arr = array();
				$pd_tmp_arr['ID'] = $rd['ID'];
				$pd_tmp_arr['name'] = $rd['name'];

				$p_meta = get_post_meta($rd['ID']);
				$pd_tmp_arr['sku'] = (count($p_meta) && isset($p_meta['_sku'][0]))?$p_meta['_sku'][0]:'';
				$pd_tmp_arr['regular_price'] = (count($p_meta) && isset($p_meta['_regular_price'][0]))?$p_meta['_regular_price'][0]:'';
				$pd_tmp_arr['sale_price'] = (count($p_meta) && isset($p_meta['_sale_price'][0]))?$p_meta['_sale_price'][0]:'';
				$pd_tmp_arr['price'] = (count($p_meta) && isset($p_meta['_price'][0]))?$p_meta['_price'][0]:'';
				$pd_tmp_arr['stock'] = (count($p_meta) && isset($p_meta['_stock'][0]))?$p_meta['_stock'][0]:'';
				$pd_tmp_arr['backorders'] = (count($p_meta) && isset($p_meta['_backorders'][0]))?$p_meta['_backorders'][0]:'';
				$pd_tmp_arr['stock_status'] = (count($p_meta) && isset($p_meta['_stock_status'][0]))?$p_meta['_stock_status'][0]:'';
				$pd_tmp_arr['manage_stock'] = (count($p_meta) && isset($p_meta['_manage_stock'][0]))?$p_meta['_manage_stock'][0]:'';
				$pd_tmp_arr['total_sales'] = (count($p_meta) && isset($p_meta['total_sales'][0]))?$p_meta['total_sales'][0]:'';

				$pd_tmp_arr['wc_product_type'] = $this->get_product_type_by_id($rd['ID']);

				$ext_cq = "
				SELECT pmap.quickbook_product_id, pmap.class_id, pmap.a_line_item_desc, pmap.qb_ar_acc_id, pmap.qb_ivnt_site, qp.name as qp_name, qp.sku as qp_sku, qp.product_type as qp_product_type, qp.info_arr as info_arr
				FROM ".$wpdb->posts." p
				LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_product_pairs pmap ON p.ID = pmap.wc_product_id
				LEFT JOIN " . $wpdb->prefix . "mw_wc_qbo_desk_qbd_items qp ON pmap.quickbook_product_id = qp.qbd_id
				WHERE p.ID = ".$rd['ID']."
				LIMIT 0,1
				";
				$ext_data =  $this->get_row($ext_cq);
				$pd_tmp_arr['quickbook_product_id'] = (count($ext_data) && isset($ext_data['quickbook_product_id']))?$ext_data['quickbook_product_id']:'';
				$pd_tmp_arr['class_id'] = (count($ext_data) && isset($ext_data['class_id']))?$ext_data['class_id']:'';				
				$pd_tmp_arr['a_line_item_desc'] = (count($ext_data) && isset($ext_data['a_line_item_desc']))?$ext_data['a_line_item_desc']:0;
				
				$pd_tmp_arr['qb_ar_acc_id'] = (count($ext_data) && isset($ext_data['qb_ar_acc_id']))?$ext_data['qb_ar_acc_id']:'';
				$pd_tmp_arr['qb_ivnt_site'] = (count($ext_data) && isset($ext_data['qb_ivnt_site']))?$ext_data['qb_ivnt_site']:'';
				
				$pd_tmp_arr['qp_name'] = (count($ext_data) && isset($ext_data['qp_name']))?$ext_data['qp_name']:'';
				$pd_tmp_arr['qp_sku'] = (count($ext_data) && isset($ext_data['qp_sku']))?$ext_data['qp_sku']:'';
				$pd_tmp_arr['qp_product_type'] = (count($ext_data) && isset($ext_data['qp_product_type']))?$ext_data['qp_product_type']:'';
				$pd_tmp_arr['qp_info_arr'] = (count($ext_data) && isset($ext_data['info_arr']))?$ext_data['info_arr']:'';
				$r_data[] = $pd_tmp_arr;
			}
		}

		unset($q_data);
		//$this->_p($r_data);
		return $r_data;
	}
	
	public function get_product_type_by_id($product_id){
		$pt = '';
		$product_id = (int) $product_id;
		if($product_id>0){
			global $wpdb;
			$pt_q = "
			SELECT DISTINCT(p.ID), terms.name as wc_pt
			FROM ".$wpdb->posts." p			
			INNER JOIN {$wpdb->term_relationships} AS term_relationships ON p.ID = term_relationships.object_id
			INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy ON term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id
			INNER JOIN {$wpdb->terms} AS terms ON term_taxonomy.term_id = terms.term_id
			WHERE p.post_type =  'product'
			AND p.ID = {$product_id}
			AND term_taxonomy.taxonomy = 'product_type'
			";
			$pt_row = $this->get_row($pt_q);
			if(is_array($pt_row) && count($pt_row)){
				$pt = $pt_row['wc_pt'];
			}
		}
		return $pt;
	}
	
	//
	public function count_refund_list($search_txt='',$date_from='',$date_to='',$status=''){
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));
			$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
		}
		
		global $wpdb;
		$sql = "
		SELECT COUNT(DISTINCT(p.ID))
		FROM
		{$wpdb->prefix}posts as p
		
		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = p.ID AND dp.d_type =  'Refund' AND dp.ext_data =  'CreditMemo' )
		
		WHERE
		p.post_type = 'shop_order_refund'
		{$ext_whr}
		";
		
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( p.ID = %d OR p.post_parent = %d ) ";
		}
		
		//
		$status = $this->sanitize($status);
		if($status!=''){
			$sql .=$wpdb->prepare(" AND p.post_status = %s",$status);
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){
			$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
		}

		$date_to = $this->sanitize($date_to);
		if($date_to!=''){
			$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
		}

		//$sql .='GROUP BY p.ID';

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
		
	}
	
	public function get_refund_list($search_txt='',$limit='',$date_from='',$date_to='',$status=''){
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));
			$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
		}
		
		global $wpdb;
		$sql = "
		SELECT DISTINCT(p.ID), p.post_status, p.post_date as refund_date,p.post_parent as order_id, dp.qbd_id as qbd_lt_id
		FROM
		{$wpdb->prefix}posts as p
		
		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = p.ID AND dp.d_type =  'Refund' AND dp.ext_data =  'CreditMemo' )
		
		WHERE
		p.post_type = 'shop_order_refund'
		AND p.post_parent > 0
		{$ext_whr}
		";
		
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( p.ID = %d OR p.post_parent = %d ) ";
		}
		
		//
		$status = $this->sanitize($status);
		if($status!=''){
			$sql .=$wpdb->prepare(" AND p.post_status = %s",$status);
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){
			$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
		}

		$date_to = $this->sanitize($date_to);
		if($date_to!=''){
			$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
		}

		//$sql .='GROUP BY p.ID';
		
		$orderby = 'p.post_date DESC';
		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt);
		}
		//echo $sql;
		return $this->get_data($sql);
		
	}
	
	public function count_order_list($search_txt='',$date_from='',$date_to='',$status=''){
		$onc_mf = '_order_number_formatted';
		if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
			$onc_mf = '_order_number';
		}
		
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$ext_whr = " AND pm11.meta_value BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}else{
				$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}
		}
		
		global $wpdb;
		$sql = "
		SELECT COUNT(DISTINCT(p.ID))
		FROM
		{$wpdb->prefix}posts as p
		LEFT JOIN ".$wpdb->postmeta." pm1
		ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_billing_first_name' )
		LEFT JOIN ".$wpdb->postmeta." pm2
		ON ( pm2.post_id = p.ID AND pm2.meta_key =  '_billing_last_name' )
		LEFT JOIN ".$wpdb->postmeta." pm3
		ON ( pm3.post_id = p.ID AND pm3.meta_key =  '_order_total' )
		LEFT JOIN ".$wpdb->postmeta." pm4
		ON ( pm4.post_id = p.ID AND pm4.meta_key =  '_order_key' )
		LEFT JOIN ".$wpdb->postmeta." pm5
		ON ( pm5.post_id = p.ID AND pm5.meta_key =  '_customer_user' )
		LEFT JOIN ".$wpdb->postmeta." pm6
		ON ( pm6.post_id = p.ID AND pm6.meta_key =  '_order_currency' )
		LEFT JOIN ".$wpdb->postmeta." pm7
		ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_billing_company' )
		LEFT JOIN ".$wpdb->postmeta." pm8
		ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_payment_method' )
		LEFT JOIN ".$wpdb->postmeta." pm9
		ON ( pm9.post_id = p.ID AND pm9.meta_key =  '_payment_method_title' )
		LEFT JOIN ".$wpdb->postmeta." pm10
		ON ( pm10.post_id = p.ID AND pm10.meta_key =  '{$onc_mf}' )
		
		LEFT JOIN ".$wpdb->postmeta." pm11
		ON ( pm11.post_id = p.ID AND pm11.meta_key =  '_paid_date' )
		
		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = p.ID AND dp.d_type =  'Order' )
		
		WHERE
		p.post_type = 'shop_order'
		{$ext_whr}
		";
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( pm1.meta_value LIKE '%%%s%%' OR pm2.meta_value LIKE '%%%s%%' OR pm7.meta_value LIKE '%%%s%%' OR CONCAT(pm1.meta_value,' ', pm2.meta_value) LIKE '%%%s%%' OR p.ID = %s OR pm10.meta_value = %s ) ";
		}

		//
		$status = $this->sanitize($status);
		if($status!=''){
			$sql .=$wpdb->prepare(" AND p.post_status = %s",$status);
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm11.meta_value>='".$date_from." 00:00:00'";
			}else{
				$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
			}			
		}

		$date_to = $this->sanitize($date_to);
		if($date_to!=''){
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm11.meta_value<='".$date_to." 23:59:59'";
			}else{
				$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
			}			
		}

		//$sql .='GROUP BY p.ID';

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
	}

	public function get_order_list($search_txt='',$limit='',$date_from='',$date_to='',$status=''){
		$onc_mf = '_order_number_formatted';
		if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
			$onc_mf = '_order_number';
		}
		
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$ext_whr = " AND pm11.meta_value BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}else{
				$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}			
		}
		
		global $wpdb;
		$sql = "
		SELECT DISTINCT(p.ID), p.post_status, p.post_date, pm1.meta_value as billing_first_name, pm2.meta_value as billing_last_name, pm3.meta_value as order_total, pm4.meta_value as order_key, pm5.meta_value as customer_user, pm6.meta_value as order_currency, pm8.meta_value as payment_method, pm9.meta_value as payment_method_title, pm10.meta_value as order_number_formatted, pm7.meta_value as billing_company, pm11.meta_value as paid_date, dp.qbd_id as qbd_lt_id
		FROM
		{$wpdb->prefix}posts as p
		LEFT JOIN ".$wpdb->postmeta." pm1
		ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_billing_first_name' )
		LEFT JOIN ".$wpdb->postmeta." pm2
		ON ( pm2.post_id = p.ID AND pm2.meta_key =  '_billing_last_name' )
		LEFT JOIN ".$wpdb->postmeta." pm3
		ON ( pm3.post_id = p.ID AND pm3.meta_key =  '_order_total' )
		LEFT JOIN ".$wpdb->postmeta." pm4
		ON ( pm4.post_id = p.ID AND pm4.meta_key =  '_order_key' )
		LEFT JOIN ".$wpdb->postmeta." pm5
		ON ( pm5.post_id = p.ID AND pm5.meta_key =  '_customer_user' )
		LEFT JOIN ".$wpdb->postmeta." pm6
		ON ( pm6.post_id = p.ID AND pm6.meta_key =  '_order_currency' )
		LEFT JOIN ".$wpdb->postmeta." pm7
		ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_billing_company' )
		LEFT JOIN ".$wpdb->postmeta." pm8
		ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_payment_method' )
		LEFT JOIN ".$wpdb->postmeta." pm9
		ON ( pm9.post_id = p.ID AND pm9.meta_key =  '_payment_method_title' )
		LEFT JOIN ".$wpdb->postmeta." pm10
		ON ( pm10.post_id = p.ID AND pm10.meta_key =  '{$onc_mf}' )
		
		LEFT JOIN ".$wpdb->postmeta." pm11
		ON ( pm11.post_id = p.ID AND pm11.meta_key =  '_paid_date' )
		
		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = p.ID AND dp.d_type =  'Order' )
		
		WHERE
		p.post_type = 'shop_order'
		{$ext_whr}
		";

		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( pm1.meta_value LIKE '%%%s%%' OR pm2.meta_value LIKE '%%%s%%' OR pm7.meta_value LIKE '%%%s%%' OR CONCAT(pm1.meta_value,' ', pm2.meta_value) LIKE '%%%s%%' OR p.ID = %s OR pm10.meta_value = %s ) ";
		}

		//
		$status = $this->sanitize($status);
		if($status!=''){
			$sql .=$wpdb->prepare(" AND p.post_status = %s",$status);
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm11.meta_value>='".$date_from." 00:00:00'";
			}else{
				$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
			}			
		}

		$date_to = $this->sanitize($date_to);
		if($date_to!=''){
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm11.meta_value<='".$date_to." 23:59:59'";
			}else{
				$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
			}			
		}
		
		$sql .='GROUP BY p.ID';

		$orderby = 'p.post_date DESC';
		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $this->get_data($sql);
	}
	
	public function count_wc_payment_list($search_txt='',$date_from='',$date_to=''){
		$onc_mf = '_order_number_formatted';
		if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
			$onc_mf = '_order_number';
		}
		
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$ext_whr = " AND pm9.meta_value BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}else{
				$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}
		}
		
		global $wpdb;
		$sql = "
		SELECT COUNT(DISTINCT(pm8.meta_id))
		FROM
		{$wpdb->prefix}posts as p
		LEFT JOIN ".$wpdb->postmeta." pm1
		ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_billing_first_name' )
		LEFT JOIN ".$wpdb->postmeta." pm2
		ON ( pm2.post_id = p.ID AND pm2.meta_key =  '_billing_last_name' )
		LEFT JOIN ".$wpdb->postmeta." pm3
		ON ( pm3.post_id = p.ID AND pm3.meta_key =  '_order_total' )
		LEFT JOIN ".$wpdb->postmeta." pm4
		ON ( pm4.post_id = p.ID AND pm4.meta_key =  '_order_key' )
		LEFT JOIN ".$wpdb->postmeta." pm5
		ON ( pm5.post_id = p.ID AND pm5.meta_key =  '_customer_user' )
		LEFT JOIN ".$wpdb->postmeta." pm6
		ON ( pm6.post_id = p.ID AND pm6.meta_key =  '_order_currency' )
		LEFT JOIN ".$wpdb->postmeta." pm7
		ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_billing_company' )
		INNER JOIN ".$wpdb->postmeta." pm8
		ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_transaction_id' )
		LEFT JOIN ".$wpdb->postmeta." pm9
		ON ( pm9.post_id = p.ID AND pm9.meta_key =  '_paid_date' )
		INNER JOIN ".$wpdb->postmeta." pm10
		ON ( pm10.post_id = p.ID AND pm10.meta_key =  '_payment_method' )
		INNER JOIN ".$wpdb->postmeta." pm11
		ON ( pm11.post_id = p.ID AND pm11.meta_key =  '_payment_method_title' )

		LEFT JOIN ".$wpdb->postmeta." pm12
		ON ( pm12.post_id = p.ID AND pm10.meta_value = 'stripe' AND pm12.meta_key =  'Stripe Fee' )

		LEFT JOIN ".$wpdb->postmeta." pm13
		ON ( pm13.post_id = p.ID AND pm13.meta_key =  '{$onc_mf}' )

		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = pm8.meta_id AND dp.d_type =  'Payment' )
		
		WHERE
		p.post_type = 'shop_order'
		{$ext_whr}
		AND pm8.meta_id > 0
		
		AND pm10.meta_value!=''
		";
		//AND pm9.meta_value!=''
		//AND pm8.meta_value!=''
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( pm1.meta_value LIKE '%%%s%%' OR pm2.meta_value LIKE '%%%s%%' OR pm7.meta_value LIKE '%%%s%%' OR CONCAT(pm1.meta_value,' ', pm2.meta_value) LIKE '%%%s%%' OR p.ID = %s OR pm13.meta_value = %s OR pm8.meta_value = %s ) ";
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm9.meta_value>='".$date_from." 00:00:00'";
			}else{
				$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
			}
		}
		
		$date_to = $this->sanitize($date_to);
		if($date_to!=''){			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm9.meta_value<='".$date_to." 23:59:59'";
			}else{
				$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
			}
		}
		
		//$sql .='GROUP BY pm8.meta_id';

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $wpdb->get_var($sql);
	}
	
	public function get_wc_payment_list($search_txt='',$limit='',$date_from='',$date_to=''){
		$onc_mf = '_order_number_formatted';
		if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
			$onc_mf = '_order_number';
		}
		
		$ext_whr = '';
		if($this->is_pl_res_tml()){
			//$ext_whr = " AND p.post_date BETWEEN NOW() - INTERVAL 30 DAY AND NOW() ";
			
			$wp_date_time_c = $this->now();
			$last_30_days_dt = date('Y-m-d H:i:s', strtotime('-'.$this->get_hd_ldys_lmt().' days', strtotime($wp_date_time_c)));			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$ext_whr = " AND pm9.meta_value BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}else{
				$ext_whr = " AND p.post_date BETWEEN '{$last_30_days_dt}' AND '{$wp_date_time_c}' ";
			}
		}
		
		global $wpdb;
		$sql = "
		SELECT DISTINCT(p.ID) as order_id, p.post_status as order_status, p.post_date as order_date, pm1.meta_value as billing_first_name, pm2.meta_value as billing_last_name, pm3.meta_value as order_total, pm4.meta_value as order_key, pm5.meta_value as customer_user, pm6.meta_value as order_currency,
		pm8.meta_id as payment_id, pm8.meta_value as transaction_id, pm9.meta_value as paid_date, pm10.meta_value as payment_method, pm11.meta_value as payment_method_title, dp.qbd_id as qbd_payment_id, pm12.meta_value as stripe_txn_fee , pm13.meta_value as order_number_formatted
		FROM
		{$wpdb->prefix}posts as p
		LEFT JOIN ".$wpdb->postmeta." pm1
		ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_billing_first_name' )
		LEFT JOIN ".$wpdb->postmeta." pm2
		ON ( pm2.post_id = p.ID AND pm2.meta_key =  '_billing_last_name' )
		LEFT JOIN ".$wpdb->postmeta." pm3
		ON ( pm3.post_id = p.ID AND pm3.meta_key =  '_order_total' )
		LEFT JOIN ".$wpdb->postmeta." pm4
		ON ( pm4.post_id = p.ID AND pm4.meta_key =  '_order_key' )
		LEFT JOIN ".$wpdb->postmeta." pm5
		ON ( pm5.post_id = p.ID AND pm5.meta_key =  '_customer_user' )
		LEFT JOIN ".$wpdb->postmeta." pm6
		ON ( pm6.post_id = p.ID AND pm6.meta_key =  '_order_currency' )
		LEFT JOIN ".$wpdb->postmeta." pm7
		ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_billing_company' )
		INNER JOIN ".$wpdb->postmeta." pm8
		ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_transaction_id' )
		LEFT JOIN ".$wpdb->postmeta." pm9
		ON ( pm9.post_id = p.ID AND pm9.meta_key =  '_paid_date' )
		INNER JOIN ".$wpdb->postmeta." pm10
		ON ( pm10.post_id = p.ID AND pm10.meta_key =  '_payment_method' )
		INNER JOIN ".$wpdb->postmeta." pm11
		ON ( pm11.post_id = p.ID AND pm11.meta_key =  '_payment_method_title' )

		LEFT JOIN ".$wpdb->postmeta." pm12
		ON ( pm12.post_id = p.ID AND pm10.meta_value = 'stripe' AND pm12.meta_key =  'Stripe Fee' )

		LEFT JOIN ".$wpdb->postmeta." pm13
		ON ( pm13.post_id = p.ID AND pm13.meta_key =  '{$onc_mf}' )

		LEFT JOIN ".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs dp
		ON ( dp.wc_id = pm8.meta_id AND dp.d_type =  'Payment' )
		
		WHERE
		p.post_type = 'shop_order'
		{$ext_whr}
		AND pm8.meta_id > 0
		
		AND pm10.meta_value!=''
		";
		//AND pm9.meta_value!=''
		//AND pm8.meta_value!=''
		$search_txt = $this->sanitize($search_txt);
		if($search_txt!=''){
			$sql .=" AND ( pm1.meta_value LIKE '%%%s%%' OR pm2.meta_value LIKE '%%%s%%' OR pm7.meta_value LIKE '%%%s%%' OR CONCAT(pm1.meta_value,' ', pm2.meta_value) LIKE '%%%s%%' OR p.ID = %s OR pm13.meta_value = %s OR pm8.meta_value = %s ) ";
		}

		$date_from = $this->sanitize($date_from);
		if($date_from!=''){			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm9.meta_value>='".$date_from." 00:00:00'";
			}else{
				$sql .=" AND p.post_date>='".$date_from." 00:00:00'";
			}
		}

		$date_to = $this->sanitize($date_to);
		if($date_to!=''){			
			if($this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
				$sql .=" AND pm9.meta_value<='".$date_to." 23:59:59'";
			}else{
				$sql .=" AND p.post_date<='".$date_to." 23:59:59'";
			}
		}
		
		$sql .='GROUP BY pm8.meta_id';

		$orderby = '(pm9.meta_value IS NULL) DESC, p.ID DESC';
		$sql .= ' ORDER BY  '.$orderby;

		if($limit!=''){
			$sql .= ' LIMIT  '.$limit;
		}

		if($search_txt!=''){
			$sql = $wpdb->prepare($sql,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt,$search_txt);
		}
		//echo $sql;
		return $this->get_data($sql);
	}
	
	public function wc_get_payment_details_by_txn_id($transaction_id='',$order_id=0){
		$order_id = (int) $order_id;
		$payment_row = array();
		$transaction_id = $this->sanitize($transaction_id);
		global $wpdb;
		$whr = '';
		
		if($transaction_id!=''){
			$whr.=" AND pm8.meta_value='{$transaction_id}' ";
		}
		
		if($order_id){
			$whr.=" AND p.ID = {$order_id} ";
		}
		
		$sql = "
		SELECT DISTINCT(p.ID) as order_id, p.post_status as order_status, p.post_date as order_date, pm1.meta_value as billing_first_name, pm2.meta_value as billing_last_name, pm3.meta_value as order_total, pm4.meta_value as order_key, pm5.meta_value as customer_user, pm6.meta_value as order_currency,
		pm8.meta_id as payment_id, pm8.meta_value as transaction_id, pm9.meta_value as paid_date, pm10.meta_value as payment_method, pm11.meta_value as payment_method_title, pm12.meta_value as stripe_txn_fee
		FROM
		{$wpdb->prefix}posts as p
		LEFT JOIN ".$wpdb->postmeta." pm1
		ON ( pm1.post_id = p.ID AND pm1.meta_key =  '_billing_first_name' )
		LEFT JOIN ".$wpdb->postmeta." pm2
		ON ( pm2.post_id = p.ID AND pm2.meta_key =  '_billing_last_name' )
		LEFT JOIN ".$wpdb->postmeta." pm3
		ON ( pm3.post_id = p.ID AND pm3.meta_key =  '_order_total' )
		LEFT JOIN ".$wpdb->postmeta." pm4
		ON ( pm4.post_id = p.ID AND pm4.meta_key =  '_order_key' )
		LEFT JOIN ".$wpdb->postmeta." pm5
		ON ( pm5.post_id = p.ID AND pm5.meta_key =  '_customer_user' )
		LEFT JOIN ".$wpdb->postmeta." pm6
		ON ( pm6.post_id = p.ID AND pm6.meta_key =  '_order_currency' )
		LEFT JOIN ".$wpdb->postmeta." pm7
		ON ( pm7.post_id = p.ID AND pm7.meta_key =  '_billing_company' )
		INNER JOIN ".$wpdb->postmeta." pm8
		ON ( pm8.post_id = p.ID AND pm8.meta_key =  '_transaction_id' )
		LEFT JOIN ".$wpdb->postmeta." pm9
		ON ( pm9.post_id = p.ID AND pm9.meta_key =  '_paid_date' )
		INNER JOIN ".$wpdb->postmeta." pm10
		ON ( pm10.post_id = p.ID AND pm10.meta_key =  '_payment_method' )
		INNER JOIN ".$wpdb->postmeta." pm11
		ON ( pm11.post_id = p.ID AND pm11.meta_key =  '_payment_method_title' )

		LEFT JOIN ".$wpdb->postmeta." pm12
		ON ( pm12.post_id = p.ID AND pm10.meta_value = 'stripe' AND pm12.meta_key =  'Stripe Fee' )

		WHERE
		p.post_type = 'shop_order'
		
		AND pm10.meta_value!=''
		AND pm8.meta_id > 0
		
		$whr
		";
		//AND pm9.meta_value!=''
		//AND pm8.meta_value!=''

		$payment_row = $this->get_row($sql);
		/**/
		if($this->check_sh_cmfpicw_hash()){
			$payment_row = $this->custom_order_and_p_details_amounts_multiplication($payment_row);
		}
		return $payment_row;
	}
	
	public function ord_pmnt_is_mt_ls_check_by_ord_id($post_id,$post_type='shop_order'){
		$post_type = trim($post_type);
		$post_id = (int) $post_id;
		if($post_id>0 && $post_type!=''){
			if(!$this->is_pl_res_tml()){return true;}	
			global $wpdb;
			$pa = $this->get_row("SELECT `post_date` FROM {$wpdb->posts} WHERE `post_type` = '{$post_type}' AND `ID` = {$post_id} ");
			if(is_array($pa) && count($pa)){
				if($post_type == 'shop_order' && $this->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
					$pd = get_post_meta($post_id,'_paid_date',true);
				}else{
					$pd = $pa['post_date'];
				}
				
				if(empty($pd)){return false;}
				$pd = strtotime($pd);				
				if ($pd < strtotime('-'.$this->get_hd_ldys_lmt().' days')){
					return false;
				}else{
					return true;
				}
			}
		}
	}
	
	public function if_qbo_product_exists($product_data, $check_only_map=false){
		if(is_array($product_data) && count($product_data)){
			$name_replace_chars = array(':','\t','\n');
			global $wpdb;
			$wc_product_id = (int) $this->get_array_isset($product_data,'wc_product_id',0,true);
			$name = $this->get_array_isset($product_data,'name','',true,100,false,$name_replace_chars);
			$sku = $this->get_array_isset($product_data,'_sku','',true);
			
			$is_variation = $this->get_array_isset($product_data,'is_variation',false,false);
			$qbd_id = '';
			
			$map_tbl = ($is_variation)?$wpdb->prefix.'mw_wc_qbo_desk_qbd_variation_pairs':$wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs';
			$w_p_f = ($is_variation)?'wc_variation_id':'wc_product_id';
			
			$query = $wpdb->prepare("SELECT `quickbook_product_id` FROM `{$map_tbl}` WHERE `{$w_p_f}` = %d AND `quickbook_product_id` !='' AND `{$w_p_f}` > 0 ",$wc_product_id);
			
			$query_product = $this->get_row($query);
			if(!empty($query_product)){
				$qbd_id =  $query_product['quickbook_product_id'];
			}
			
			if($check_only_map){
				return $qbd_id;
			}
			
			/**/
			if($is_variation){
				$name = $this->get_variation_name_from_id($name,'',$wc_product_id);
				$name = $this->get_woo_v_name_trimmed($name);
			}
			if($qbd_id=='' && $name!=''){
				$qbd_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','id','name',$name);
			}
			
			if($qbd_id=='' && $sku!=''){
				$qbd_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','id','sku',$sku);
			}
			return $qbd_id;
		}
		return false;
	}
	
	public function check_qbd_customer_by_display_name($customer_data){
		if(is_array($customer_data) && count($customer_data)){
			$name_replace_chars = array(':','\t','\n');
			global $wpdb;
			$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			if(!empty($display_name)){
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
				$query = $wpdb->prepare("SELECT `qbd_customerid` , `info_arr` FROM `$table` WHERE `d_name` = %s AND `d_name` !='' ",$display_name);
				$query_customer = $this->get_row($query);
				if(is_array($query_customer) && !empty($query_customer)){
					return $query_customer['qbd_customerid'];
				}
			}			
		}
		return false;
	}
	
	public function get_dmcb_fval_ext_ccfv_val($customer_data){
		$ext_dn = '';
		if(is_array($customer_data) && count($customer_data)){						
			if($this->is_dmcb_fval_ext_ccfv()){
				$name_replace_chars = array(':','\t','\n');
				$dmcb_fval = $this->get_option('mw_wc_qbo_desk_dmcb_fval');
				
				if($dmcb_fval == 'o_bc_name'){
					$billing_company = $this->get_array_isset($customer_data,'billing_company','',true,100,false,$name_replace_chars);
					$ext_dn = $billing_company;
				}
				
				if($dmcb_fval == 'o_bfl_name'){
					$billing_first_name = $this->get_array_isset($customer_data,'billing_first_name','',true,100,false,$name_replace_chars);
					$billing_last_name = $this->get_array_isset($customer_data,'billing_last_name','',true,100,false,$name_replace_chars);
					$ext_dn = $billing_first_name.' '.$billing_last_name;
				}				
				
				if($dmcb_fval == 'o_sc_name'){
					$shipping_company = $this->get_array_isset($customer_data,'shipping_company','',true,100,false,$name_replace_chars);
					$ext_dn = $shipping_company;
				}
				
				if($dmcb_fval == 'o_sfl_name'){
					$shipping_first_name = $this->get_array_isset($customer_data,'shipping_first_name','',true,100,false,$name_replace_chars);
					$shipping_last_name = $this->get_array_isset($customer_data,'shipping_last_name','',true,100,false,$name_replace_chars);
					$ext_dn = $shipping_first_name.' '.$shipping_last_name;
				}
			}
			
		}
		return $ext_dn;
	}
	
	public function is_dmcb_fval_ext_ccfv(){
		$dmcb_fval = $this->get_option('mw_wc_qbo_desk_dmcb_fval');
		if($dmcb_fval == 'o_bfl_name' || $dmcb_fval == 'o_bc_name' || $dmcb_fval == 'o_sfl_name' || $dmcb_fval == 'o_sc_name'){
			return true;
		}
		return false;
	}
	
	public function if_qbo_customer_exists($customer_data){
		if(is_array($customer_data) && count($customer_data)){
			$name_replace_chars = array(':','\t','\n');
			global $wpdb;
			$wc_customerid = (int) $this->get_array_isset($customer_data,'wc_customerid','',true);
			$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			$email = $this->get_array_isset($customer_data,'email','',true);
			
			$billing_company = $this->get_array_isset($customer_data,'billing_company','',true,100,false,$name_replace_chars);
			$shipping_company = $this->get_array_isset($customer_data,'shipping_company','',true,100,false,$name_replace_chars);
			
			$dmcb_fval = $this->get_option('mw_wc_qbo_desk_dmcb_fval');
			$ext_ccfv = '';
			
			if($this->is_dmcb_fval_ext_ccfv()){
				if($dmcb_fval == 'o_bc_name'){
					$ext_ccfv = $billing_company;
				}
				
				if($dmcb_fval == 'o_bfl_name'){
					$billing_first_name = $this->get_array_isset($customer_data,'billing_first_name','',true,100,false,$name_replace_chars);
					$billing_last_name = $this->get_array_isset($customer_data,'billing_last_name','',true,100,false,$name_replace_chars);
					$ext_ccfv = $billing_first_name.' '.$billing_last_name;
				}				
				
				if($dmcb_fval == 'o_sc_name'){
					$ext_ccfv = $shipping_company;
				}
				
				if($dmcb_fval == 'o_sfl_name'){
					$shipping_first_name = $this->get_array_isset($customer_data,'shipping_first_name','',true,100,false,$name_replace_chars);
					$shipping_last_name = $this->get_array_isset($customer_data,'shipping_last_name','',true,100,false,$name_replace_chars);
					$ext_ccfv = $shipping_first_name.' '.$shipping_last_name;
				}
			}
			
			//$this->_p($customer_data);
			if(!empty($ext_ccfv)){
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
				$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `d_name` = %s AND `d_name` !='' ",$ext_ccfv);
			}else{
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers_pairs';
				$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `wc_customerid` = %d AND `qbd_customerid` !='' AND `wc_customerid` > 0 ",$wc_customerid);

				//Qbo customer table
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
				if(empty($this->get_data($query))){
					$is_dn_chk = false;
					$chk_by_email = true;
					
					if($dmcb_fval == 'm_uid_accn'){
						$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
						$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `acc_num` = %s AND `acc_num` !='' ",$wc_customerid);
						$chk_by_email = false;
					}
					
					if($chk_by_email){
						if(!empty($email)){
							$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `email` = %s AND `email` !='' ",$email);
							if(empty($this->get_data($query))){
								$is_dn_chk = true;
							}
						}else{
							$is_dn_chk = true;
						}
					}					
					
					if($is_dn_chk && !empty($display_name) && $dmcb_fval == 'm_email_dn'){
						$query = $wpdb->prepare("SELECT `qbd_customerid` , `info_arr` FROM `$table` WHERE `d_name` = %s AND `d_name` !='' ",$display_name);
					}
				}
			}
			
			$query_customer = array();
			if(!empty($query)){
				$query_customer = $this->get_row($query);
			}
			
			if(!empty($query_customer)){
				/**/
				if(isset($query_customer['info_arr']) && $dmcb_fval == 'm_email_dn_zc'){
					$is_zip_matched = false;
					$c_info_arr = $query_customer['info_arr'];
					if(!empty($c_info_arr)){
						$c_info_arr = unserialize($c_info_arr);
						if(is_array($c_info_arr) && !empty($c_info_arr)){
							$BillAddress_PostalCode = $this->get_array_isset($c_info_arr,'BillAddress_PostalCode','');
							$billing_postcode = $this->get_array_isset($customer_data,'billing_postcode','');
							if($billing_postcode !='' && strtolower($BillAddress_PostalCode) == strtolower($billing_postcode)){
								$is_zip_matched = true;
							}
						}
					}
					
					if(!$is_zip_matched){
						return false;
					}
				}
				
				return $query_customer['qbd_customerid'];
			}
		}

		return false;
	}

	public function if_qbo_guest_exists($customer_data){
		if(is_array($customer_data) && count($customer_data)){
			$name_replace_chars = array(':','\t','\n');
			global $wpdb;

			$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			$email = $this->get_array_isset($customer_data,'email','',true);

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
			
			$billing_company = $this->get_array_isset($customer_data,'billing_company','',true,100,false,$name_replace_chars);
			$shipping_company = $this->get_array_isset($customer_data,'shipping_company','',true,100,false,$name_replace_chars);
			
			$dmcb_fval = $this->get_option('mw_wc_qbo_desk_dmcb_fval');
			$ext_ccfv = '';
			
			if($this->is_dmcb_fval_ext_ccfv()){
				if($dmcb_fval == 'o_bc_name'){
					$ext_ccfv = $billing_company;
				}
				
				if($dmcb_fval == 'o_bfl_name'){
					$billing_first_name = $this->get_array_isset($customer_data,'billing_first_name','',true,100,false,$name_replace_chars);
					$billing_last_name = $this->get_array_isset($customer_data,'billing_last_name','',true,100,false,$name_replace_chars);
					if(!empty($billing_first_name) || !empty($billing_last_name)){
						$ext_ccfv = $billing_first_name.' '.$billing_last_name;
					}					
				}				
				
				if($dmcb_fval == 'o_sc_name'){
					$ext_ccfv = $shipping_company;
				}
				
				if($dmcb_fval == 'o_sfl_name'){
					$shipping_first_name = $this->get_array_isset($customer_data,'shipping_first_name','',true,100,false,$name_replace_chars);
					$shipping_last_name = $this->get_array_isset($customer_data,'shipping_last_name','',true,100,false,$name_replace_chars);
					if(!empty($shipping_first_name) || !empty($shipping_last_name)){
						$ext_ccfv = $shipping_first_name.' '.$shipping_last_name;
					}					
				}
			}
			
			//$this->_p($customer_data);			
			if(!empty($ext_ccfv)){
				$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `d_name` = %s AND `d_name` !='' ",$ext_ccfv);
			}else{
				$is_dn_chk = false;
				if(!empty($email)){
					$query = $wpdb->prepare("SELECT `qbd_customerid` FROM `$table` WHERE `email` = %s AND `email` !='' ",$email);
					if(empty($this->get_data($query))){
						$is_dn_chk = true;
					}
				}else{
					$is_dn_chk = true;
				}
				
				if($is_dn_chk && !empty($display_name) && $dmcb_fval == 'm_email_dn'){
					$query = $wpdb->prepare("SELECT `qbd_customerid` , `info_arr` FROM `$table` WHERE `d_name` = %s AND `d_name` !='' ",$display_name);
				}
			}
			
			$query_customer = array();
			if(!empty($query)){
				$query_customer = $this->get_row($query);
			}
			
			if(!empty($query_customer)){
				/**/
				if(isset($query_customer['info_arr']) && $dmcb_fval == 'm_email_dn_zc'){
					$is_zip_matched = false;
					$c_info_arr = $query_customer['info_arr'];
					if(!empty($c_info_arr)){
						$c_info_arr = unserialize($c_info_arr);
						if(is_array($c_info_arr) && !empty($c_info_arr)){
							$BillAddress_PostalCode = $this->get_array_isset($c_info_arr,'BillAddress_PostalCode','');
							$billing_postcode = $this->get_array_isset($customer_data,'billing_postcode','');
							if($billing_postcode !='' && strtolower($BillAddress_PostalCode) == strtolower($billing_postcode)){
								$is_zip_matched = true;
							}
						}
					}
					
					if(!$is_zip_matched){
						return false;
					}
				}
				
				return $query_customer['qbd_customerid'];
			}
		}
		return false;
	}
	
	public function check_guest_in_queue($customer_data,$order_id){
		$name_replace_chars = array(':','\t','\n');
		
		$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
		$email = $this->get_array_isset($customer_data,'email','',true);
		$billing_company = $this->get_array_isset($customer_data,'billing_company','',true,100,false,$name_replace_chars);
		
		$pm_mk = '';$pm_mv = '';
		
		$ext_join = '';
		$ext_whr = '';
		$pm_mk_c = '';$pm_mv_c = '';
		if($this->is_dmcb_fval_ext_ccfv()){
			$dmcb_fval = $this->get_option('mw_wc_qbo_desk_dmcb_fval');
			if($dmcb_fval == 'o_bc_name'){
				$pm_mk = '_billing_company';
				$pm_mv = $billing_company;
			}
			
			if($dmcb_fval == 'o_bfl_name'){
				$pm_mk = '_billing_first_name';
				$pm_mk_c = '_billing_last_name';
				
				$pm_mv = $this->get_array_isset($customer_data,'billing_first_name','',true,100,false,$name_replace_chars);
				$pm_mv_c = $this->get_array_isset($customer_data,'billing_last_name','',true,100,false,$name_replace_chars);
				$ext_join = "INNER JOIN {$wpdb->postmeta} pm2 ON (pm.post_id = pm2.post_id AND pm.meta_key = '{$pm_mk_c}')";				
				$ext_whr = "AND pm2.meta_value = %s";
			}
			
			if($dmcb_fval == 'o_sc_name'){
				$shipping_company = $this->get_array_isset($customer_data,'shipping_company','',true,100,false,$name_replace_chars);
				$pm_mk = '_shipping_company';
				$pm_mv = $shipping_company;
			}
			
			if($dmcb_fval == 'o_sfl_name'){
				$pm_mk = '_shipping_first_name';
				$pm_mk_c = '_shipping_last_name';
				
				$pm_mv = $this->get_array_isset($customer_data,'shipping_first_name','',true,100,false,$name_replace_chars);
				$pm_mv_c = $this->get_array_isset($customer_data,'shipping_last_name','',true,100,false,$name_replace_chars);
				$ext_join = "INNER JOIN {$wpdb->postmeta} pm2 ON (pm.post_id = pm2.post_id AND pm.meta_key = '{$pm_mk_c}')";				
				$ext_whr = "AND pm2.meta_value = %s";
			}
			
		}else{
			if($email!=''){
				$pm_mk = '_billing_email';
				$pm_mv = $email;
			}
		}
		
		if(!empty($pm_mk) && !empty($pm_mv)){
			global $wpdb;
			$gcq = "
			SELECT GROUP_CONCAT(pm.post_id) AS order_ids 
			FROM {$wpdb->postmeta} pm 
			INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			INNER JOIN {$wpdb->postmeta} pm1 ON (pm.post_id = pm1.post_id AND pm.meta_key = '{$pm_mk}')
			{$ext_join}
			WHERE p.post_type = 'shop_order'			
			AND pm.meta_value = %s
			{$ext_whr}
			AND pm1.meta_key = '_customer_user' AND pm1.meta_value = '0'
			";
			if($pm_mk_c!=''){
				$gcq = $wpdb->prepare($gcq,$pm_mv,$pm_mv_c);
			}else{
				$gcq = $wpdb->prepare($gcq,$pm_mv);
			}
			
			$gq_data = $this->get_row($gcq);
			if(is_array($gq_data) && count($gq_data)){
				$order_ids = $gq_data['order_ids'];
				$order_ids = $this->sanitize($order_ids);
				$qb_action = QUICKBOOKS_ADD_GUEST;
				if(!empty($order_ids)){
					$qq ="SELECT * FROM `quickbooks_queue` WHERE `qb_action` = '{$qb_action}' AND `ident` IN($order_ids) AND `qb_status` = 'q' ";
					$qq_d = $this->get_data($qq);
					if(is_array($qq_d) && count($qq_d)){
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public function get_mapped_payment_method_data($wc_paymentmethod='',$wc_currency=''){
		global $wpdb;
		$wc_paymentmethod = $this->sanitize($wc_paymentmethod);
		if($wc_paymentmethod!=''){
			$map_data = $this->get_row($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_paymentmethod` WHERE `wc_paymentmethod` = %s AND `currency` = %s ",$wc_paymentmethod,$wc_currency));
			return $map_data;
		}
		return array();
	}

	public function check_quickbooks_invoice($wc_inv_id,$wc_inv_num=''){
		if($this->is_qwc_connected()){
			$qbd_id = $this->get_wc_data_pair_val('Order',$wc_inv_id);
			return $qbd_id;
		}
		return false;
	}
	
	public function check_quickbooks_refund($refund_id,$wc_inv_id=0,$wc_inv_num=''){
		if($this->is_qwc_connected()){
			$qbd_id = $this->get_wc_data_pair_val('Refund',$refund_id,'CreditMemo');
			return $qbd_id;
		}
		return false;
	}
	
	//
	public function check_quickbooks_refund_c($refund_id,$wc_inv_id=0,$wc_inv_num=''){
		if($this->is_qwc_connected()){
			$qbd_id = $this->get_wc_data_pair_val('Refund',$refund_id,'Check');
			return $qbd_id;
		}
		return false;
	}
	
	public function check_quickbooks_payment($payment_id){
		if($this->is_qwc_connected()){
			$qbd_id = $this->get_wc_data_pair_val('Payment',$payment_id);
			return $qbd_id;
		}
		return false;
	}
	
	public function check_quickbooks_os_payment($order_id){
		if($this->is_qwc_connected()){
			//$qbd_id = $this->get_wc_data_pair_val('Payment',$order_id,'Order');
			$qbd_id = $this->get_wc_data_pair_val('Order_Payment',$order_id);
			return $qbd_id;
		}
		return false;
	}
	
	//
	public function check_save_automap_customer_data_wf_qf($w_cus,$all_qbo_customers,$cam_wf,$cam_qf,$mo_um=false){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs";
		
		//New for Only Unmapped
		if($mo_um){
			$ID = $w_cus['ID'];							
			$e_mr = $this->get_row("SELECT `id` FROM {$map_tbl} WHERE `wc_customerid` = {$ID} ");
			if(!empty($e_mr)){
				return;
			}
		}
		
		if(!isset($w_cus[$cam_wf])){
			if($cam_wf=='first_name_last_name'){
				$w_cus[$cam_wf] = get_user_meta($w_cus['ID'],'first_name',true) . ' '. get_user_meta($w_cus['ID'],'last_name',true);
			}else{
				$w_cus[$cam_wf] = get_user_meta($w_cus['ID'],$cam_wf,true);
			}			
		}
		
		$wf_v = $this->get_array_isset($w_cus,$cam_wf,'',true);
		//
		$wf_v = $this->sanitize($wf_v);
		$wf_v = str_replace('&amp;', '&', $wf_v);
		
		if(!empty($cam_wf) && !empty($cam_qf)){
			foreach($all_qbo_customers as $q_cus){
				$is_match_map_customer = false;
				if(isset($q_cus[$cam_qf]) || $cam_qf == 'first_last'){
					if($cam_qf == 'first_last'){
						$qf_v = $this->get_array_isset($q_cus,'first','',true) . ' '. $this->get_array_isset($q_cus,'last','',true);
					}else{
						$qf_v = $this->get_array_isset($q_cus,$cam_qf,'',true);
					}
					
					if($wf_v!='' && strtoupper($wf_v) == strtoupper($qf_v)){
						$is_match_map_customer = true;
					}
					
					if($is_match_map_customer){
						$save_data = array();
						$save_data['wc_customerid'] = $w_cus['ID'];
						$save_data['qbd_customerid'] = $q_cus['qbd_customerid'];
						$wpdb->insert($map_tbl,$save_data);
						return (int) $wpdb->insert_id;
						break;
					}
				}
			}
		}
	}
	
	public function check_save_automap_customer_data($w_cus,$all_qbo_customers,$map_by='email'){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs";
		
		$user_email = $this->get_array_isset($w_cus,'user_email','');
		$display_name = $this->get_array_isset($w_cus,'display_name','');
		
		$user_email = $this->sanitize($user_email);
		$display_name = $this->sanitize($display_name);
		
		//$user_email = str_replace('&amp;', '&', $user_email);
		$display_name = str_replace('&amp;', '&', $display_name);

		foreach($all_qbo_customers as $q_cus){
			$is_match_map_customer = false;
			if($map_by=='email' && $user_email!='' && strtolower($user_email)==strtolower($q_cus['email'])){
				$is_match_map_customer = true;
			}
			
			if($map_by=='name' && $display_name!='' && strtoupper($display_name)==strtoupper($q_cus['d_name'])){
				$is_match_map_customer = true;
			}

			if($is_match_map_customer){
				$save_data = array();
				$save_data['wc_customerid'] = $w_cus['ID'];
				$save_data['qbd_customerid'] = $q_cus['qbd_customerid'];
				$wpdb->insert($map_tbl,$save_data);
				return (int) $wpdb->insert_id;
				break;
			}
		}
	}
	
	//
	public function AutoMapCustomerWfQf($cam_wf,$cam_qf,$mo_um=false){
		global $wpdb;
		$map_count = 0;
		
		if(empty($cam_wf) || empty($cam_qf)){
			return $map_count;
		}
		
		if(!is_array($this->get_n_cam_wf_list()) || !is_array($this->get_n_cam_qf_list())){
			return $map_count;
		}
		$cam_wf_la = $this->get_n_cam_wf_list();
		$cam_qf_la = $this->get_n_cam_qf_list();
		if(!isset($cam_wf_la[$cam_wf]) || !isset($cam_qf_la[$cam_qf])){
			return $map_count;
		}
		
		$roles = 'customer'; // we can use multiple role comma separeted
		
		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}

		if ( ! is_array( $roles ) ){			
			$roles = array_map('trim',explode( ",", $roles ));
		}
		
		$sql = '
			SELECT  ' . $wpdb->users . '.ID, ' . $wpdb->users . '.display_name, ' . $wpdb->users . '.user_email
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'
			AND     (
		';
		$i = 1;
		foreach ( $roles as $role ) {
			$sql .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%%"' . $role . '"%%\' ';
			if ( $i < count( $roles ) ) $sql .= ' OR ';
			$i++;
		}
		$sql .= ' ) ';
		
		if($cam_qf=='first_last'){
			$cam_qf_cl = "`first` , `last`";
		}else{
			$cam_qf_cl = "`$cam_qf`";
		}		
		
		$all_wc_customers = $this->get_data($sql);
		$all_qbo_customers = $this->get_data("SELECT `qbd_customerid`, {$cam_qf_cl} FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers");
		
		if(!$mo_um){
			$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` WHERE `id` > 0 ");
			$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` ");
		}
		
		if(is_array($all_wc_customers) && count($all_wc_customers) && is_array($all_qbo_customers) && count($all_qbo_customers)){
			foreach($all_wc_customers as $w_cus){
				$insert_id = (int) $this->check_save_automap_customer_data_wf_qf($w_cus,$all_qbo_customers,$cam_wf,$cam_qf,$mo_um);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_customers);
		unset($all_qbo_customers);
		
		return $map_count;
	}
	
	public function AutoMapCustomer($map_by='email'){
		global $wpdb;
		$map_count = 0;
		$roles = 'customer'; // we can use multiple role comma separeted

		$ext_roles = $this->get_option('mw_wc_qbo_desk_wc_cust_role');
		if($ext_roles!=''){
			$roles.=','.$ext_roles;
		}

		if ( ! is_array( $roles ) ){
			//$roles = array_walk( explode( ",", $roles ), 'trim' );
			$roles = array_map('trim',explode( ",", $roles ));
		}

		$sql = '
			SELECT  ' . $wpdb->users . '.ID, ' . $wpdb->users . '.display_name, ' . $wpdb->users . '.user_email
			FROM        ' . $wpdb->users . ' INNER JOIN ' . $wpdb->usermeta . '
			ON          ' . $wpdb->users . '.ID = ' . $wpdb->usermeta . '.user_id
			WHERE       ' . $wpdb->usermeta . '.meta_key        =       \'' . $wpdb->prefix . 'capabilities\'
			AND     (
		';
		$i = 1;
		foreach ( $roles as $role ) {
			$sql .= ' ' . $wpdb->usermeta . '.meta_value    LIKE    \'%%"' . $role . '"%%\' ';
			if ( $i < count( $roles ) ) $sql .= ' OR ';
			$i++;
		}
		$sql .= ' ) ';

		//$sql = "SELECT `ID` , `user_email` , `display_name` FROM ".$wpdb->users."";

		$all_wc_customers = $this->get_data($sql);
		$all_qbo_customers = $this->get_data("SELECT `qbd_customerid`, `email` , `d_name` FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers");

		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` ");

		if(is_array($all_wc_customers) && count($all_wc_customers) && is_array($all_qbo_customers) && count($all_qbo_customers)){
			foreach($all_wc_customers as $w_cus){
				$insert_id = (int) $this->check_save_automap_customer_data($w_cus,$all_qbo_customers,$map_by);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_customers);
		unset($all_qbo_customers);
		return $map_count;
	}

	public function AutoMapCustomerByName(){
		return $this->AutoMapCustomer('name');
	}
	
	//
	public function check_save_automap_product_data_wf_qf($w_pro,$all_qbo_products,$pam_wf,$pam_qf,$mo_um=false){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs";
		
		//New for Only Unmapped
		if($mo_um){
			$ID = $w_pro['ID'];
			$e_mr = $this->get_row("SELECT `id` FROM {$map_tbl} WHERE `wc_product_id` = {$ID} ");
			if(!empty($e_mr)){
				return;
			}
		}
		
		$wf_v = $this->get_array_isset($w_pro,$pam_wf,'',true);
		//
		$wf_v = $this->sanitize($wf_v);
		$wf_v = str_replace('&amp;', '&', $wf_v);
		
		//$this->_p($wf_v);
		
		foreach($all_qbo_products as $q_pro){
			$is_match_map_product = false;
			if(isset($q_pro[$pam_qf])){
				//$this->_p($wf_v);
				$qf_v = $this->get_array_isset($q_pro,$pam_qf,'',true);				
				if($wf_v!='' && strtoupper($wf_v) == strtoupper($qf_v)){
					$is_match_map_product = true;
				}
				
				/**/
				if(!$is_match_map_product && $pam_wf == 'sku' && $pam_qf == 'sku'){
					if ($wf_v!='' && strpos(strtoupper($wf_v), ':'.strtoupper($qf_v)) !== false) {
						$qbd_parent_id = $this->get_array_isset($q_pro,'parent_id','');
						$qbd_parent_id = $this->sanitize($qbd_parent_id);
						if(!empty($qbd_parent_id)){
							$qbd_parent_sku = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','sku','qbd_id',$qbd_parent_id);
							if(!empty($qbd_parent_sku) && strtoupper($wf_v) == strtoupper($qbd_parent_sku).':'.strtoupper($qf_v)){
								$is_match_map_product = true;
							}
						}
					}
				}
			}
			
			/**/
			if(($pam_qf == 'mpn' || $pam_qf == 'barcode' || $pam_qf == 'Woo-Product-ID' || $pam_qf == 'SalesDesc') && ($pam_qf == 'SalesDesc' || $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') || $this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap'))){
				$qf_v = '';
				if(isset($q_pro['info_arr'])){
					$info_arr = $q_pro['info_arr'];
					if(!empty($info_arr)){
						$info_arr = @unserialize($info_arr);
						if(is_array($info_arr) && !empty($info_arr)){
							if($pam_qf == 'mpn' && isset($info_arr['ManufacturerPartNumber'])){
								$qf_v = $this->get_array_isset($info_arr,'ManufacturerPartNumber','',true);
							}
							
							if($pam_qf == 'barcode' && isset($info_arr['BarCodeValue'])){
								$qf_v = $this->get_array_isset($info_arr,'BarCodeValue','',true);
							}
							
							if($pam_qf == 'Woo-Product-ID' && isset($info_arr['Woo-Product-ID'])){
								$qf_v = $this->get_array_isset($info_arr,'Woo-Product-ID','',true);
							}
							
							if($pam_qf == 'SalesDesc' && isset($info_arr['Desc'])){
								$qf_v = $this->get_array_isset($info_arr,'Desc','',true);
							}
							
							if($wf_v!='' && strtoupper($wf_v) == strtoupper($qf_v)){
								$is_match_map_product = true;
							}
						}
					}
				}
			}			
			
			if($is_match_map_product){
				$save_data = array();
				$save_data['wc_product_id'] = $w_pro['ID'];
				$save_data['quickbook_product_id'] = $q_pro['qbd_id'];
				$wpdb->insert($map_tbl,$save_data);
				return (int) $wpdb->insert_id;
				break;
			}
		}
	}

	public function check_save_automap_product_data($w_pro,$all_qbo_products,$map_by='sku'){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs";
		
		$sku = $this->get_array_isset($w_pro,'sku','');
		$name = $this->get_array_isset($w_pro,'name','');
		
		$sku = $this->sanitize($sku);
		$name = $this->sanitize($name);
		
		$sku = str_replace('&amp;', '&', $sku);
		$name = str_replace('&amp;', '&', $name);
		
		foreach($all_qbo_products as $q_pro){
			$is_match_map_product = false;

			if($map_by=='sku' && $sku!=''){
				if(strtoupper($sku)==strtoupper($q_pro['sku'])){
					$is_match_map_product = true;
				}
				if($q_pro['sku']=='' && strtoupper($sku)==strtoupper($q_pro['name'])){
					$is_match_map_product = true;
				}
			}

			if($map_by=='name' && $name!=''){
				if(strtoupper($name)==strtoupper($q_pro['name'])){
					$is_match_map_product = true;
				}
			}
			
			if($is_match_map_product){
				$save_data = array();
				$save_data['wc_product_id'] = $w_pro['ID'];
				$save_data['quickbook_product_id'] = $q_pro['qbd_id'];
				$wpdb->insert($map_tbl,$save_data);
				return (int) $wpdb->insert_id;
				break;
			}
		}
	}
	
	//
	public function AutoMapProductWfQf($pam_wf,$pam_qf,$mo_um=false){
		global $wpdb;
		$map_count = 0;
		
		if(empty($pam_wf) || empty($pam_qf)){
			return $map_count;
		}
		
		if(!is_array($this->get_n_pam_wf_list()) || !is_array($this->get_n_pam_qf_list())){
			return $map_count;
		}
		$pam_wf_la = $this->get_n_pam_wf_list();
		$pam_qf_la = $this->get_n_pam_qf_list();
		if(!isset($pam_wf_la[$pam_wf]) || !isset($pam_qf_la[$pam_qf])){
			return $map_count;
		}
		
		$m_whr = '';
		if($pam_wf=='sku'){
			$m_whr.=" AND pm1.meta_value!=''";
		}
		
		$sql = "
			SELECT DISTINCT(p.ID), p.post_title AS name, pm1.meta_value AS sku
			FROM ".$wpdb->posts." p
			LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID
			AND pm1.meta_key =  '_sku' )
			WHERE p.post_type =  'product'
			AND p.post_status NOT IN('trash','auto-draft','inherit')
			{$m_whr}
		";
		
		$all_wc_products = $this->get_data($sql);
		$q_ef = '';
		if($pam_qf == 'SalesDesc' || $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') || $this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$q_ef = ', `info_arr` ';
		}
		/**/
		if($pam_wf == 'sku' && $pam_qf == 'sku'){
			$q_ef.=' , `parent_id` ';
		}
		
		$all_qbo_products = $this->get_data("SELECT `qbd_id`, `sku` , `name` {$q_ef} FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_items");
		
		if(!$mo_um){
			$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `id` > 0 ");
			$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` ");
		}
		
		if(is_array($all_wc_products) && count($all_wc_products) && is_array($all_qbo_products) && count($all_qbo_products)){
			foreach($all_wc_products as $w_pro){
				$insert_id = (int) $this->check_save_automap_product_data_wf_qf($w_pro,$all_qbo_products,$pam_wf,$pam_qf,$mo_um);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_products);
		unset($all_qbo_products);
		return $map_count;
	}
	
	public function AutoMapProduct($map_by='sku'){
		global $wpdb;
		$map_count = 0;
		$status = 'publish';

		$m_whr = '';
		if($map_by=='sku'){
			$m_whr.=" AND pm1.meta_value!=''";
		}
		
		$sql = "
			SELECT DISTINCT(p.ID), p.post_title AS name, pm1.meta_value AS sku
			FROM ".$wpdb->posts." p
			LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID
			AND pm1.meta_key =  '_sku' )
			WHERE p.post_type =  'product'
			AND p.post_status NOT IN('trash','auto-draft','inherit')
			{$m_whr}
		";
		$all_wc_products = $this->get_data($sql);
		$all_qbo_products = $this->get_data("SELECT `qbd_id`, `sku` , `name` FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_items");

		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` ");

		if(is_array($all_wc_products) && count($all_wc_products) && is_array($all_qbo_products) && count($all_qbo_products)){
			foreach($all_wc_products as $w_pro){
				$insert_id = (int) $this->check_save_automap_product_data($w_pro,$all_qbo_products,$map_by);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_products);
		unset($all_qbo_products);
		return $map_count;
	}

	public function AutoMapProductByName(){
		return $this->AutoMapProduct('name');
	}
	
	//
	public function check_save_automap_variation_data_wf_qf($w_pro,$all_qbo_products,$vam_wf,$vam_qf,$mo_um=false){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs";
		
		//New for Only Unmapped
		if($mo_um){
			$ID = $w_pro['ID'];
			$e_mr = $this->get_row("SELECT `id` FROM {$map_tbl} WHERE `wc_variation_id` = {$ID} ");
			if(!empty($e_mr)){
				return;
			}
		}
		
		$wf_v = $this->get_array_isset($w_pro,$vam_wf,'',true);
		//
		$wf_v = $this->sanitize($wf_v);		
		$wf_v = str_replace('&amp;', '&', $wf_v);
		
		foreach($all_qbo_products as $q_pro){
			$is_match_map_variation = false;
			if(isset($q_pro[$vam_qf])){
				//$this->_p($wf_v);
				$qf_v = $this->get_array_isset($q_pro,$vam_qf,'',true);				
				if($wf_v!='' && strtoupper($wf_v) == strtoupper($qf_v)){
					$is_match_map_variation = true;
				}
			}
			
			/**/
			if(($vam_qf == 'mpn' || $vam_qf == 'barcode' || $vam_qf == 'Woo-Product-ID' || $vam_qf == 'SalesDesc') && ($vam_qf == 'SalesDesc' || $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') || $this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap'))){
				$qf_v = '';
				if(isset($q_pro['info_arr'])){
					$info_arr = $q_pro['info_arr'];
					if(!empty($info_arr)){
						$info_arr = @unserialize($info_arr);
						if(is_array($info_arr) && !empty($info_arr)){
							if($vam_qf == 'mpn' && isset($info_arr['ManufacturerPartNumber'])){
								$qf_v = $this->get_array_isset($info_arr,'ManufacturerPartNumber','',true);
							}
							
							if($vam_qf == 'barcode' && isset($info_arr['BarCodeValue'])){
								$qf_v = $this->get_array_isset($info_arr,'BarCodeValue','',true);
							}
							
							if($vam_qf == 'Woo-Product-ID' && isset($info_arr['Woo-Product-ID'])){
								$qf_v = $this->get_array_isset($info_arr,'Woo-Product-ID','',true);
							}
							
							if($vam_qf == 'SalesDesc' && isset($info_arr['Desc'])){
								$qf_v = $this->get_array_isset($info_arr,'Desc','',true);
							}
							
							if($wf_v!='' && strtoupper($wf_v) == strtoupper($qf_v)){
								$is_match_map_variation = true;
							}
						}
					}
				}
			}
			
			if($is_match_map_variation){
				$save_data = array();
				$save_data['wc_variation_id'] = $w_pro['ID'];
				$save_data['quickbook_product_id'] = $q_pro['qbd_id'];
				$wpdb->insert($map_tbl,$save_data);
				return (int) $wpdb->insert_id;
				break;
			}
		}
	}
	
	public function check_save_automap_variation_data($w_pro,$all_qbo_products,$map_by='sku'){
		global $wpdb;
		$map_tbl = $wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs";
		
		$sku = $this->get_array_isset($w_pro,'sku','');
		$name = $this->get_array_isset($w_pro,'name','');
		
		$sku = $this->sanitize($sku);
		$name = $this->sanitize($name);
		
		$sku = str_replace('&amp;', '&', $sku);
		$name = str_replace('&amp;', '&', $name);

		foreach($all_qbo_products as $q_pro){
			$is_match_map_variation = false;

			if($map_by=='sku' && $sku!=''){
				if(strtoupper($sku)==strtoupper($q_pro['sku'])){
					$is_match_map_variation = true;
				}
				if(!$is_match_map_variation && strtoupper($sku)==strtoupper($q_pro['name'])){ //$q_pro['sku']=='' && 
					$is_match_map_variation = true;
				}
			}

			if($map_by=='name' && $name!=''){
				if(strtoupper($name)==strtoupper($q_pro['name'])){
					$is_match_map_variation = true;
				}
			}
			
			if($is_match_map_variation){
				$save_data = array();
				$save_data['wc_variation_id'] = $w_pro['ID'];
				$save_data['quickbook_product_id'] = $q_pro['qbd_id'];
				$wpdb->insert($map_tbl,$save_data);
				return (int) $wpdb->insert_id;
				break;
			}
		}
	}
	
	//
	public function AutoMapVariationWfQf($vam_wf,$vam_qf,$mo_um=false){
		global $wpdb;
		$map_count = 0;
		
		if(empty($vam_wf) || empty($vam_qf)){
			return $map_count;
		}
		
		if(!is_array($this->get_n_vam_wf_list()) || !is_array($this->get_n_vam_qf_list())){
			return $map_count;
		}
		$vam_wf_la = $this->get_n_vam_wf_list();
		$vam_qf_la = $this->get_n_vam_qf_list();
		if(!isset($vam_wf_la[$vam_wf]) || !isset($vam_qf_la[$vam_qf])){
			return $map_count;
		}
		
		$m_whr = '';
		if($vam_wf=='sku'){
			$m_whr.=" AND pm1.meta_value!=''";
		}
		
		$sql = "
			SELECT DISTINCT(p.ID), p.post_title AS name, pm1.meta_value AS sku, p.post_parent AS Parent_Product_ID
			FROM ".$wpdb->posts." p
			LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID
			AND pm1.meta_key =  '_sku' )
			WHERE p.post_type =  'product_variation'
			AND p.post_status NOT IN('trash','auto-draft','inherit')
			{$m_whr}
		";
		$all_wc_variations = $this->get_data($sql);
		$q_ef = '';
		if($vam_qf == 'SalesDesc' || $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') || $this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$q_ef = ', `info_arr` ';
		}
		$all_qbo_products = $this->get_data("SELECT `qbd_id`, `sku` , `name` {$q_ef} FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_items");
		
		if(!$mo_um){
			$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `id` > 0 ");
			$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` ");
		}		
		
		if(is_array($all_wc_variations) && count($all_wc_variations) && is_array($all_qbo_products) && count($all_qbo_products)){
			foreach($all_wc_variations as $w_pro){
				$insert_id = (int) $this->check_save_automap_variation_data_wf_qf($w_pro,$all_qbo_products,$vam_wf,$vam_qf,$mo_um);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_variations);
		unset($all_qbo_products);
		return $map_count;
	}
	
	public function AutoMapVariation($map_by='sku'){
		global $wpdb;
		$map_count = 0;
		$status = 'publish';

		$m_whr = '';
		if($map_by=='sku'){
			$m_whr.=" AND pm1.meta_value!=''";
		}
		
		$sql = "
			SELECT DISTINCT(p.ID), p.post_title AS name, pm1.meta_value AS sku
			FROM ".$wpdb->posts." p
			LEFT JOIN ".$wpdb->postmeta." pm1 ON ( pm1.post_id = p.ID
			AND pm1.meta_key =  '_sku' )
			WHERE p.post_type =  'product_variation'
			AND p.post_status NOT IN('trash','auto-draft','inherit')
			{$m_whr}
		";
		$all_wc_variations = $this->get_data($sql);
		$all_qbo_products = $this->get_data("SELECT `qbd_id`, `sku` , `name` FROM ".$wpdb->prefix."mw_wc_qbo_desk_qbd_items");
		
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` ");

		if(is_array($all_wc_variations) && count($all_wc_variations) && is_array($all_qbo_products) && count($all_qbo_products)){
			foreach($all_wc_variations as $w_pro){
				$insert_id = (int) $this->check_save_automap_variation_data($w_pro,$all_qbo_products,$map_by);
				if($insert_id>0){
					$map_count++;
				}
			}
		}
		unset($all_wc_variations);
		unset($all_qbo_products);
		return $map_count;
	}

	public function AutoMapVariationByName(){
		return $this->AutoMapVariation('name');
	}
	
	/*25-01-2018*/
	private function clear_invalid_mappings($type,$loop=false){
		$list_table = '';$it_id_field = '';$it_qb_id_field = '';
		$map_table = '';$mt_id_field = '';$mt_qb_id_field = '';
		
		switch ($type) {
			case "product":
				$list_table = 'mw_wc_qbo_desk_qbd_items';
				$map_table = 'mw_wc_qbo_desk_qbd_product_pairs';
				
				$it_qb_id_field = 'qbd_id';
				$mt_qb_id_field = 'quickbook_product_id';
				
				break;
			case "variation":
				$list_table = 'mw_wc_qbo_desk_qbd_items';
				$map_table = 'mw_wc_qbo_desk_qbd_variation_pairs';
				
				$it_qb_id_field = 'qbd_id';
				$mt_qb_id_field = 'quickbook_product_id';
				
				break;
			case "customer":
				$list_table = 'mw_wc_qbo_desk_qbd_customers';
				$map_table = 'mw_wc_qbo_desk_qbd_customers_pairs';
				
				$it_qb_id_field = 'qbd_customerid';
				$mt_qb_id_field = 'qbd_customerid';
				
				break;
			case "paymentmethod":
				
				break;
			default:
				
		}
		
		if($list_table!='' && $map_table!='' && $it_qb_id_field!='' && $mt_qb_id_field!=''){
			global $wpdb;
			$list_table = $wpdb->prefix.$list_table;
			$map_table = $wpdb->prefix.$map_table;
			
			if(empty($it_id_field)){
				$it_id_field = 'id';
			}
			
			if(empty($mt_id_field)){
				$mt_id_field = 'id';
			}
			
			if($loop){
				return $this->clear_invalid_mappings_by_loop($list_table,$map_table,$it_id_field,$mt_id_field,$it_qb_id_field,$mt_qb_id_field);
			}
			
			/*
			$sq = " SELECT `{$it_qb_id_field}` FROM {$list_table} ";
			$q = " DELETE FROM {$map_table} WHERE `{$mt_qb_id_field}` NOT IN ({$sq}) ";
			*/
			
			$sq = "SELECT `{$it_qb_id_field}` FROM {$list_table} WHERE {$list_table}.{$it_qb_id_field} = {$map_table}.{$mt_qb_id_field}";
			$q = "DELETE FROM {$map_table} WHERE NOT EXISTS ({$sq}); ";
			$wpdb->query($q);
			return true;
		}
	}
	
	//
	private function clear_invalid_mappings_by_loop($list_table,$map_table,$it_id_field,$mt_id_field,$it_qb_id_field,$mt_qb_id_field){
		global $wpdb;
		$map_data = $this->get_data("SELECT `{$mt_id_field}` , `{$mt_qb_id_field}` FROM {$map_table}");
		$tot_deleted = 0;
		if(is_array($map_data) && count($map_data)){
			foreach($map_data as $md){
				$mt_id_val = (int) $md[$mt_id_field];
				$mt_qb_val = $md[$mt_qb_id_field];
				$ld = $this->get_row("SELECT `{$it_id_field}` FROM {$list_table} WHERE `{$it_qb_id_field}` !='' AND `{$it_qb_id_field}` = '{$mt_qb_val}' ");
				if(empty($ld)){
					$wpdb->query("DELETE FROM `{$map_table}` WHERE `{$mt_id_field}` = {$mt_id_val} AND `{$mt_qb_id_field}` = '{$mt_qb_val}' ");
					$tot_deleted++;
				}
			}
		}
		return $tot_deleted;
	}
	
	public function clear_customer_invalid_mappings(){
		return $this->clear_invalid_mappings('customer',true);
	}
	
	public function clear_product_invalid_mappings(){
		return $this->clear_invalid_mappings('product',true);
	}
	
	public function clear_variation_invalid_mappings(){
		return $this->clear_invalid_mappings('variation',true);
	}
	
	/*---------*/
	public function format_date($date,$format="Y-m-d"){
		if($date!='' && $date!=NULL && $date!='0000-00-00 00:00:00'){
			$date = strtotime($date);
			return date($format,$date);
		}
	}

	/*################################-################################*/
	public function get_offset($page, $items_per_page){
		return ( $page * $items_per_page ) - $items_per_page;
	}

	var $per_page_keyword = 'mwqs_desk_per_page';
	public function set_per_page_from_url($unique=''){
		if(isset($_GET[$this->per_page_keyword]) && (int) $_GET[$this->per_page_keyword]>0){
			$pp = (int) $_GET[$this->per_page_keyword];
			if(!$pp){$pp=$this->default_show_per_page;}
			$_SESSION[$this->session_prefix.'item_per_page'.$unique] = $pp;
		}

	}
	
	public function get_item_per_page($unique='',$default=50){
		$default = (!(int) $default)?(int) $this->default_show_per_page:$default;
		$itemPerPage = (isset($_SESSION[$this->session_prefix.'item_per_page'.$unique]))?$_SESSION[$this->session_prefix.'item_per_page'.$unique]:$default;
		return $itemPerPage;
	}
	
	public function get_page_var(){
		//$page = (get_query_var('paged')) ? (int) get_query_var('paged') : 1;
		$page = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;
		if(!$page){$page=1;}
		return $page;
	}
	var $session_prefix = 'mw_wc_qbo_desk';
	public function set_and_get($keyword){
		if(isset($_GET[$keyword])){
		  $_SESSION[$this->session_prefix.$keyword] = $_GET[$keyword];
		}
	}

	public function set_and_post($keyword){
		if(isset($_POST[$keyword])){
		  $_SESSION[$this->session_prefix.$keyword] = $_POST[$keyword];
		}
	}

	public function set_session_val($keyword,$value){
		$_SESSION[$this->session_prefix.$keyword] = $value;
	}

	public function get_session_val($keyword,$default='',$reset=false){
		$val = $default;
		if(isset($_SESSION[$this->session_prefix.$keyword])){
			$val = $_SESSION[$this->session_prefix.$keyword];
			if($reset){
				unset($_SESSION[$this->session_prefix.$keyword]);
			}
		}

		return $val;
	}

	public function isset_session($keyword){
		if(isset($_SESSION[$this->session_prefix.$keyword])){
			return true;
		}
		return false;
	}

	public function unset_session($keyword){
		if(isset($_SESSION[$this->session_prefix.$keyword])){
			unset($_SESSION[$this->session_prefix.$keyword]);
		}
	}

	public  function get_paginate_links($total_records=0,$items_per_page=20,$show_total=true,$page=''){
		if($page==''){
			$page = $this->get_page_var();
		}

		if($total_records>0){
			$pagination_data = '<div class="mwqs_paginate_div mwqbd_pd">';

			$i_text = ($total_records>1)?'items':'item';

			if($show_total){
				//$pagination_data.='<div class="tot_div">Total <span>'.$total_records.'</span> '.$i_text.'</div>';
				//
				$total_pages = ceil($total_records / $items_per_page);
				$pgn_txt = $this->get_pagination_count_txt($page,$total_pages,$total_records,$items_per_page);

				$pagination_data.= '<div>'.$pgn_txt.'</div>';
			}

			if($total_records>$items_per_page){

			$pagination_data.='<div class="pagination">';

			$pagination_data.=paginate_links( array(
								'base' => add_query_arg( 'paged', '%#%' ),
								'format' => '',
								'prev_text' => __('&laquo;'),
								'next_text' => __('&raquo;'),
								'total' => ceil($total_records / $items_per_page),
								'current' => $page,
								'end_size' =>2,
								'mid_size' =>3

								));

			$pagination_data.='</div>';
			}

			$pagination_data.='</div>';

			return $pagination_data;

		}
	}

	public function get_pagination_count_txt($page,$total_pages,$count,$itemPerPage){
		$cur_page = ($page==0)?1:$page;
		if ($page != 0) $page--;

		$txt = '';
		if($cur_page<=$total_pages){
			$e_text = ($count>1)?'entries':'entry';
			$txt = 'Showing '.($page*$itemPerPage+1).' to '.(($total_pages==$cur_page || $itemPerPage>=$count)?$count:($page+1)*$itemPerPage).' of '.$count.' '.$e_text;
		}
		return $txt;
	}

	/*################################-################################*/
	var $show_per_page = array(
		'10'=>'10',
		'20'=>'20',
		'50'=>'50',
		'100'=>'100',
		'200'=>'200',
		'500'=>'500',
	);

	var $log_save_days = array(
		'30'=>'30',
		'10'=>'10',
		'15'=>'15',		
		'60'=>'60',
		'90'=>'90',		
	);
	//'120'=>'120',
	
	var $qb_log_queue_save_days = array(
		'0'=>'0',
		'10'=>'10',
		'15'=>'15',
		'30'=>'30',
		'60'=>'60',
		'90'=>'90',		
	);
	
	var $tax_format = array(
		'TaxExclusive'=>'Exclusive of Tax',
		'TaxInclusive'=>'Inclusive of Tax'
	);

	var $default_show_per_page = 20;

	var $queue_st_search_options = array(
		'pending'=>'Pending',
		'previous'=>'Previous',
		'all'=>'All',
	);

	public function get_select2_js($item='select',$d_item='',$prevent_lib_load=false){
		if(!$this->option_checked('mw_wc_qbo_desk_select2_status')){
			return '';
		}

		$is_ajax_dd = 0;
		if($this->option_checked('mw_wc_qbo_desk_select2_ajax')){
			$is_ajax_dd = 1;
		}

		$json_data_url = '';
		if($d_item=='qbo_product'){
			$json_data_url = site_url('index.php?mw_qbo_desktop_public_get_json_item_list=1&item=qbo_product');
		}

		if($d_item=='qbo_customer'){
			$json_data_url = site_url('index.php?mw_qbo_desktop_public_get_json_item_list=1&item=qbo_customer');
		}

		$s2_lib = '';
		if(!$prevent_lib_load){
			$s2_lib = '
			<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
			<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
			';
		}

		return <<<EOF
		{$s2_lib}
		<script type="text/javascript">
		  jQuery(document).ready(function(){
			 jQuery('{$item}').addClass('mwqs_s2_desk');
		  });

		  jQuery(function($){
			  //jQuery('{$item}').select2();
			   jQuery('{$item}').each(function(){
				   if(jQuery(this).prop('multiple')){
						jQuery(this).select2();
						 jQuery(this).removeClass('mwqs_s2_desk');
				   }
			   });

			  jQuery('{$item}').hover(function(){
				  var is_ajax_dd = {$is_ajax_dd};
				  if(jQuery(this).hasClass('mwqs_dynamic_select_desk') && is_ajax_dd==1){
					   jQuery(this).select2({
						   ajax: {
							url: "{$json_data_url}",
							dataType: 'json',
							delay: 250,
							data: function (params) {
								return {
									q: params.term // search term
								};
							},
							processResults: function (data) {
								return {
									results: data
								};
							},
							cache: true
						},
						minimumInputLength: 3
					   });
				  }else{
					  jQuery(this).select2();
				  }

				  jQuery(this).removeClass('mwqs_s2_desk');

			  });
			  var head = $("head");
			  var headlinklast = head.find("link[rel='stylesheet']:last");
			  var linkElement = "<style type='text/css'>ul.select2-results__options li:first-child{padding:12px 0;}</style>";
			  if (headlinklast.length){
			    headlinklast.after(linkElement);
			  }
			  else {
			   head.append(linkElement);
			  }
		  });
		</script>
EOF;
	}

	public function get_admin_get_extra_css_js(){
		$ext_assets = '';
		$ext_assets.='<link href="'.esc_url( plugins_url( "admin/css/sweetalert.css", dirname(__FILE__) ) ).'" rel="stylesheet" type="text/css">';
		//$ext_assets.='<link href="'.esc_url( plugins_url( "admin/css/woocommerce-custom.css", dirname(__FILE__) ) ).'" rel="stylesheet" type="text/css">';
		$ext_assets.='<link href="'.esc_url( plugins_url( "admin/css/font-awesome.css", dirname(__FILE__) ) ).'" rel="stylesheet" type="text/css">';
		$ext_assets.='<script type="text/javascript" src="'.esc_url( plugins_url( "admin/js/sweetalert-dev.js", dirname(__FILE__) ) ).'"></script>';
		return $ext_assets;
	}
	
	public function get_admin_get_toggle_switch_css_js(){
		$ext_assets = '';
		$ext_assets.='<link href="'.esc_url( plugins_url( "admin/css/toggle-switch.css", dirname(__FILE__) ) ).'" rel="stylesheet" type="text/css">';
		return $ext_assets;
	}
	
	public function get_checkbox_switch_css_js(){
		//
		return <<<EOF
	   <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/css/bootstrap2/bootstrap-switch.css' type='text/css' media='all' />
	   <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.js'></script>
EOF;

	}

	public function set_admin_sweet_alert($save_status=''){

		if($save_status){
			if($save_status=='admin-success-green'){
				echo '<script>swal("Rock On!", "Your settings have been saved.", "success")</script>';
			}elseif($save_status=='red lighten-2'){
				echo '<script>swal("Oops!", "Hmmmm something went wrong.", "error")</script>';
			}elseif($save_status!='admin-success-green' && $save_status!='red lighten-2' && $save_status!='error'){
				echo '<script>swal("Rock On!", "'.$save_status.'", "success")</script>';
			}else{
				echo '<script>swal("Oops!", "Hmmmm something went wrong.", "error")</script>';
			}
			echo '<script type="text/javascript">
			jQuery(document).ready(function(e){
				jQuery(".confirm").on("click",function(e){
					jQuery(".sweet-overlay").hide();
					jQuery(".showSweetAlert").hide();
					jQuery("body").removeClass("stop-scrolling");
				});
			});
			</script>';
		}
	}

	public function check_refresh_data_enabled_by_item($item,$ext_data=false){
		if($item!='' && ($this->option_checked('mw_wc_qbo_desk_cp_refresh_data_enable') || $this->option_checked('mw_wc_qbo_desk_oth_refresh_data_enable'))){
			if(!$ext_data){
				$rf_options = $this->get_option('mw_wc_qbo_desk_cp_rf_data_options');
			}else{
				$rf_options = $this->get_option('mw_wc_qbo_desk_oth_rf_data_options');
			}

			if($rf_options!=''){
				$rf_options = explode(',',$rf_options);
			}
			if(is_array($rf_options) && in_array($item,$rf_options)){
				return true;
			}
		}
		return false;
	}

	public function get_plugin_version(){
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php', false, false );
		//$this->_p($plugin_data);
		return $plugin_data['Version'];
	}
	
	/**/
	private function mw_license_lk_blank_check_run($licensekey,$localkey="",$realtime=false){
		$recent_llk = get_option('mw_wc_qbo_desk_localkey','');		
		if(empty($recent_llk)){
			$is_lbcr = true;
			$td = date('Y-m-d');
			$td_rc = 0;
			
			$lbcr_chk_opt = get_option('mw_wc_qbo_desk_lbcr_chk_count_dt','');
			if(!empty($lbcr_chk_opt) && is_array($lbcr_chk_opt)){
				if(isset($lbcr_chk_opt[$td])){
					$td_rc = (int) $lbcr_chk_opt[$td];
					if($td_rc  < 0){$td_rc = 0;}
					if($td_rc  >= 2){
						$is_lbcr = false;
					}
				}
			}
			
			if($is_lbcr){
				$td_rc++;
				$lbcr_nd = array();
				$lbcr_nd[$td] = $td_rc;
				
				update_option('mw_wc_qbo_desk_lbcr_chk_count_dt',$lbcr_nd);
				
				if($this->is_valid_license($licensekey,$localkey,true)){
					return true;
				}				
			}
		}
		
		return false;
	}
	
	/*******************************************/
	public function is_valid_license($licensekey,$localkey="",$realtime=false){
		if(!$this->is_valid_license){
			$license_data = $this->myworks_wc_qbo_sync_check_license($licensekey,$localkey,$realtime);
			/**/
			if(!$realtime){
				$this->mw_license_lk_blank_check_run($licensekey,$localkey,$realtime);
			}			
			
			$this->plugin_license_status = (isset($license_data['status']))?$license_data['status']:'';
			if(isset($license_data['status']) && $license_data['status']=='Active' && !isset($license_data['trial_expired'])){
				$this->is_valid_license = true;
			}else{
				if(isset($license_data['trial_expired'])){
					$this->plugin_license_status = 'Invalid';
				}
			}
		}
		return $this->is_valid_license;
	}

	public function get_license_status(){
		return $this->plugin_license_status;
	}

	public function get_plugin_domain(){
		$domain = $_SERVER['SERVER_NAME'];
		return $domain;
	}

	public function get_plugin_ip(){
		$usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
		if(empty($usersip) && isset($_SERVER['SERVER_NAME'])){
			$usersip = gethostbyname($_SERVER['SERVER_NAME']);
		}
		return $usersip;
	}

	public function get_plugin_connection_dir(){
		$dirpath = dirname(__FILE__);
		return $dirpath;
	}
	protected $is_valid_license = false;
	protected function myworks_wc_qbo_sync_check_license($licensekey,$localkey="",$realtime=false,$only_remote_check=false) {
		$results_df = array('status'=>'');
		$results = $results_df;
		if(empty($licensekey)){
			return $results;
		}
		
		//$realtime = true; // Temporary
		$licensing_secret_key = 'QD8FR0LSI2P1SA'; #ALL
		

		// Enter the url to your WHMCS installation here
		$whmcsurl = $this->quickbooks_connection_dashboard_url.'/';
		// Must match what is specified in the MD5 Hash Verification field
		// of the licensing product that will be used with this check.

		// The number of days to wait between performing remote license checks
		$localkeydays = 5;
		// The number of days to allow failover for after local key expiry
		$allowcheckfaildays = 5;

		// -----------------------------------
		//  -- Do not edit below this line --
		// -----------------------------------


		$check_token = time() . md5(mt_rand(1000000000, 9999999999) . $licensekey);
		$checkdate = date("Ymd");
		$domain = $_SERVER['SERVER_NAME'];
	   //$usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
	    $usersip = $this->get_plugin_ip();

		$dirpath = dirname(__FILE__);
		$verifyfilepath = 'modules/servers/licensing/verify.php';
		$localkeyvalid = false;
		
		$localkeyresults = array();
		if (!$only_remote_check && $localkey) {
			$localkey = str_replace("\n", '', $localkey); # Remove the line breaks
			$localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
			$md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
			if ($md5hash == md5($localdata . $licensing_secret_key)) {
				$localdata = strrev($localdata); # Reverse the string
				$md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
				$localdata = substr($localdata, 32); # Extract License Data
				$localdata = base64_decode($localdata);
				$localkeyresults = unserialize($localdata);
				$originalcheckdate = $localkeyresults['checkdate'];
				if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
					$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
					if ($originalcheckdate > $localexpiry) {
						$localkeyvalid = true;
						$results = $localkeyresults;
						if(is_array($results)){
							if(isset($results['validdomain'])){
								$validdomains = explode(',', $results['validdomain']);
								if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
									$localkeyvalid = false;
									$localkeyresults['status'] = "Invalid";
									$results = $results_df;
								}
							}
							
							/*
							if(isset($results['validip'])){
								$validips = explode(',', $results['validip']);
								if (!in_array($usersip, $validips)) {
									$localkeyvalid = false;
									$localkeyresults['status'] = "Invalid";
									$results = $results_df;
								}
							}
							*/
							
							/*
							if(isset($results['validdirectory'])){
								$validdirs = explode(',', $results['validdirectory']);
								if (!in_array($dirpath, $validdirs)) {
									$localkeyvalid = false;
									$localkeyresults['status'] = "Invalid";
									$results = $results_df;
								}
							}
							*/
						}
					}else{
						//delete_option('mw_wc_qbo_desk_localkey');
						$realtime = true;						
					}
				}
			}
		}
		
		if ((!$localkeyvalid && $realtime) || $only_remote_check) {			
			$responseCode = 0;
			$postfields = array(
				'licensekey' => $licensekey,
				'domain' => $domain,
				'ip' => $usersip,
				'dir' => $dirpath,
			);
			if ($check_token) $postfields['check_token'] = $check_token;
			$query_string = '';
			foreach ($postfields AS $k=>$v) {
				$query_string .= $k.'='.urlencode($v).'&';
			}
			if (function_exists('curl_exec')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_POSTREDIR, 3);
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$data = curl_exec($ch);
				$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
			} else {
				$responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
				$fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
				if ($fp) {
					$newlinefeed = "\r\n";
					$header = "POST ".$whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
					$header .= "Host: ".$whmcsurl . $newlinefeed;
					$header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
					$header .= "Content-length: ".@strlen($query_string) . $newlinefeed;
					$header .= "Connection: close" . $newlinefeed . $newlinefeed;
					$header .= $query_string;
					$data = $line = '';
					@stream_set_timeout($fp, 20);
					@fputs($fp, $header);
					$status = @socket_get_status($fp);
					while (!@feof($fp)&&$status) {
						$line = @fgets($fp, 1024);
						$patternMatches = array();
						if (!$responseCode
							&& preg_match($responseCodePattern, trim($line), $patternMatches)
						) {
							$responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
						}
						$data .= $line;
						$status = @socket_get_status($fp);
					}
					@fclose ($fp);
				}
			}
			if ($responseCode != 200) {
				$localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
				if (isset($originalcheckdate) && $originalcheckdate > $localexpiry) {
					$results = $localkeyresults;
				} else {
					$results = array();
					$results['status'] = "Invalid";
					$results['description'] = "Remote Check Failed";
					return $results;
				}
			} else {
				preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
				$results = array();
				foreach ($matches[1] AS $k=>$v) {
					$results[$v] = $matches[2][$k];
				}
			}
			if (!is_array($results)) {
				//die("Invalid License Server Response");
				return $results;
			}
			if (isset($results['md5hash'])) {
				if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
					$results['status'] = "Invalid";
					$results['description'] = "MD5 Checksum Verification Failed";
					return $results;
				}
			}
			if ($results['status'] == "Active") {
				$results['checkdate'] = $checkdate;
				$data_encoded = serialize($results);
				$data_encoded = base64_encode($data_encoded);
				$data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
				$data_encoded = strrev($data_encoded);
				$data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
				$data_encoded = wordwrap($data_encoded, 80, "\n", true);
				$results['localkey'] = $data_encoded;
			}else{
				delete_option('mw_wc_qbo_desk_localkey');
			}
			$results['remotecheck'] = true;
		}
		//$this->_p($results);
		
		/**/		
		if(!empty($results)){
			$ldfcpv = array();
			$ldfcpv['status'] = $results['status'];
			$ldfcpv['nextduedate'] = (isset($results['nextduedate']))?$results['nextduedate']:'';
			$ldfcpv['billingcycle'] = (isset($results['billingcycle']))?$results['billingcycle']:'';
			$l_pln = '';
			if((isset($results['productname'])) && !empty($results['productname']) && strpos($results['productname'],' | ')!==false){
				$pn_arr = explode(' | ',$results['productname']);
				if(is_array($pn_arr) && count($pn_arr) == 2){
					$l_pln = $pn_arr[1];
				}
			}
			$ldfcpv['plan'] = $l_pln;
			$ldfcpv['productname'] = (isset($results['productname']))?$results['productname']:'';
			$this->license_data_for_conn_page_view = $ldfcpv;
			
			$pd_ff_ext_ld = array();
			$pd_ff_ext_ld['email'] = (isset($results['email']))?$results['email']:'';
			$pd_ff_ext_ld['validdomain'] = (isset($results['validdomain']))?$results['validdomain']:'';
			update_option('mw_wc_qbo_desk_pd_ff_ext_ld',$pd_ff_ext_ld);
		}
		
		//
		if($licensekey!=''){
			update_option('mw_wc_qbo_desk_license',$licensekey);
		}
		if (isset($results["status"]) && $results["status"]=="Active" && isset($results["localkey"]) && $results["localkey"]!='') {
			update_option('mw_wc_qbo_desk_localkey',$results["localkey"]);
		}
		if (isset($results["status"]) && $results["status"]=="Active"){
			//24-03-2017
			$productname = $results["productname"];
			$billingcycle = $results["billingcycle"];
			$serviceid = $results["serviceid"];
			if(strpos($productname,'Free Trial')!==false){
				$trialdaysleft = (int) 14-((strtotime(date("Y-m-d")) - strtotime($results["regdate"]))/86400);
				if($trialdaysleft<0){
					//
					delete_option('mw_wc_qbo_desk_localkey');
					delete_option('mw_wc_qbo_desk_access_token');
					$results['trial_expired'] = true;
					$trialdaysleft = 0;
				}
				
				update_option('mw_wc_qbo_desk_trial_license','true');
				update_option('mw_wc_qbo_desk_trial_days_left',$trialdaysleft);
				update_option('mw_wc_qbo_desk_trial_license_serviceid',$serviceid);
			}else{
				delete_option('mw_wc_qbo_desk_trial_license');
				delete_option('mw_wc_qbo_desk_trial_days_left');
				delete_option('mw_wc_qbo_desk_trial_license_serviceid');
			}
			
			//
			if(strpos($billingcycle,'Monthly')!==false){
				update_option('mw_wc_qbo_desk_monthly_license','true');
			}else{
				delete_option('mw_wc_qbo_desk_monthly_license');
			}
		}
		unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
		return $results;
	}
	
	public function get_ldfcpv(){
		return (array) $this->license_data_for_conn_page_view;
	}
	
	public function is_pl_res_tml(){
		//return true;
		if($this->option_checked('mw_wc_qbo_desk_trial_license')){
			return true;
		}
		
		if($this->option_checked('mw_wc_qbo_desk_monthly_license')){
			return true;
		}
		
		return false;
	}
	
	public function get_slmt_hstry_msg(){
		$mag = __( '<h2>Need to sync more than '.$this->get_hd_ldys_lmt().' days of history? <a href="https://myworks.software/account/clientarea.php?action=services">Upgrade</a> to an annual plan!</h2>', 'mw_wc_qbo_sync' );
		if($this->is_plg_lc_p_l()){
			$mag = __( '<h2>Need to sync more than '.$this->get_hd_ldys_lmt().' days of history? <a href="https://myworks.software/account/clientarea.php?action=services">Upgrade</a> to a paid plan!</h2>', 'mw_wc_qbo_sync' );
		}
		
		return $mag;
	}
	
	/*Dashboard Chart*/
	public function get_log_chart_data(){
		global $wpdb;
		$today = date("Y-m-d").' 00:00:00';
        $month = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
        $year = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m") - 12, 1, date("Y")));

		$invoiceData = array();
		$result_inv_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `log_type`='Order' AND `status`=1 AND `details` NOT LIKE '%Draft Invoice not allowed%' GROUP BY date_format(added_date, '%k')");
		if(count($result_inv_today)){
			foreach($result_inv_today as $data){
				$invoiceData['today'][$data['date']] = $data['count'];
			}
		}
		$result_inv_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `log_type`='Order' AND `status`=1 AND `details` NOT LIKE '%Draft Invoice not allowed%' GROUP BY date_format(added_date, '%e')");
		if(count($result_inv_month)){
			foreach($result_inv_month as $data){
				$invoiceData['month'][$data['date']] = $data['count'];
			}
		}

		$result_inv_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `log_type`='Order' AND `status`=1 AND `details` NOT LIKE '%Draft Invoice not allowed%' GROUP BY date_format(added_date, '%M')");
		if(count($result_inv_year)){
			foreach($result_inv_year as $data){
				$invoiceData['year'][$data['date']] = $data['count'];
			}
		}

		//
		$paymentData = array();
		$result_pmnt_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `log_type`='Payment' AND `status`=1 GROUP BY date_format(added_date, '%k')");
		if(count($result_pmnt_today)){
			foreach($result_pmnt_today as $data){
				$paymentData['today'][$data['date']] = $data['count'];
			}
		}
		$result_pmnt_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `log_type`='Payment' AND `status`=1 GROUP BY date_format(added_date, '%e')");
		if(count($result_pmnt_month)){
			foreach($result_pmnt_month as $data){
				$paymentData['month'][$data['date']] = $data['count'];
			}
		}

		$result_pmnt_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `log_type`='Payment' AND `status`=1 GROUP BY date_format(added_date, '%M')");
		if(count($result_pmnt_year)){
			foreach($result_pmnt_year as $data){
				$paymentData['year'][$data['date']] = $data['count'];
			}
		}

		//
		$clientData = array();
		$result_cl_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `log_type`='Customer' AND `status`=1 GROUP BY date_format(added_date, '%k')");
		if(count($result_cl_today)){
			foreach($result_cl_today as $data){
				$clientData['today'][$data['date']] = $data['count'];
			}
		}
		$result_cl_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `log_type`='Customer' AND `status`=1 GROUP BY date_format(added_date, '%e')");
		if(count($result_cl_month)){
			foreach($result_cl_month as $data){
				$clientData['month'][$data['date']] = $data['count'];
			}
		}

		$result_cl_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `log_type`='Customer' AND `status`=1 GROUP BY date_format(added_date, '%M')");
		if(count($result_cl_year)){
			foreach($result_cl_year as $data){
				$clientData['year'][$data['date']] = $data['count'];
			}
		}

		//
		$errorData = array();
		$result_er_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `status`=0 GROUP BY date_format(added_date, '%k')");
		if(count($result_er_today)){
			foreach($result_er_today as $data){
				$errorData['today'][$data['date']] = $data['count'];
			}
		}
		$result_er_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `status`=0 GROUP BY date_format(added_date, '%e')");
		if(count($result_er_month)){
			foreach($result_er_month as $data){
				$errorData['month'][$data['date']] = $data['count'];
			}
		}

		$result_er_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `status`=0 GROUP BY date_format(added_date, '%M')");
		if(count($result_er_year)){
			foreach($result_er_year as $data){
				$errorData['year'][$data['date']] = $data['count'];
			}
		}

		//
		$depositData = array();
		$result_dp_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `log_type`='Deposit' AND `status`=1 GROUP BY date_format(added_date, '%k')");
		if(count($result_dp_today)){
			foreach($result_dp_today as $data){
				$depositData['today'][$data['date']] = $data['count'];
			}
		}

		$result_dp_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `log_type`='Deposit' AND `status`=1 GROUP BY date_format(added_date, '%e')");
		if(count($result_dp_month)){
			foreach($result_dp_month as $data){
				$depositData['month'][$data['date']] = $data['count'];
			}
		}

		$result_dp_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `log_type`='Deposit' AND `status`=1 GROUP BY date_format(added_date, '%M')");
		if(count($result_dp_year)){
			foreach($result_dp_year as $data){
				$depositData['year'][$data['date']] = $data['count'];
			}
		}

		//
		$productData = array();
		$result_prd_today = $this->get_data("SELECT date_format(added_date, '%k') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$today' AND `log_type`='Product' AND `status`=1 GROUP BY date_format(added_date, '%k')");
		if(count($result_prd_today)){
			foreach($result_prd_today as $data){
				$productData['today'][$data['date']] = $data['count'];
			}
		}

		$result_prd_month = $this->get_data("SELECT date_format(added_date, '%e %M') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$month' AND `log_type`='Product' AND `status`=1 GROUP BY date_format(added_date, '%e')");
		if(count($result_prd_month)){
			foreach($result_prd_month as $data){
				$productData['month'][$data['date']] = $data['count'];
			}
		}

		$result_prd_year = $this->get_data("SELECT date_format(added_date, '%M %Y') AS date, COUNT(id) AS count FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE added_date>'$year' AND `log_type`='Product' AND `status`=1 GROUP BY date_format(added_date, '%M')");
		if(count($result_prd_year)){
			foreach($result_prd_year as $data){
				$productData['year'][$data['date']] = $data['count'];
			}
		}

		//
		return array(
            'invoices' => array(
                'total' => $invoiceData,
            ),
            'clients' => array(
                'total' => $clientData,
            ),
			 'errors' => array(
                'total' => $errorData,
            ),
			'payments' => array(
                'total' => $paymentData,
            ),
			'deposits' => array(
                'total' => $depositData,
            ),
			'products' => array(
                'total' => $productData,
            ),

        );
	}

	public function get_log_chart_output($viewPeriod=''){
		$data = $this->get_log_chart_data();
		//$this->_p($data);
		if (!in_array($viewPeriod, array('today', 'month', 'year'))) {
            $viewPeriod = 'today';
        }

		$invoiceData = (isset($data['invoices']['total'][$viewPeriod]))?$data['invoices']['total'][$viewPeriod]:array();
		$clientData = (isset($data['clients']['total'][$viewPeriod]))?$data['clients']['total'][$viewPeriod]:array();
		//$errorData = (isset($data['errors']['total'][$viewPeriod]))?$data['errors']['total'][$viewPeriod]:array();

		$paymentData = (isset($data['payments']['total'][$viewPeriod]))?$data['payments']['total'][$viewPeriod]:array();

		$productData = (isset($data['products']['total'][$viewPeriod]))?$data['products']['total'][$viewPeriod]:array();
		//$depositData = (isset($data['deposits']['total'][$viewPeriod]))?$data['deposits']['total'][$viewPeriod]:array();

		if ($viewPeriod == 'today') {

            $graphLabels = array();

            $graphDataInv = array();
            $graphDataCus = array();
			//$graphDataErr = array();

			$graphDataPmnt = array();

			$graphDataPrdt = array();
			$graphDataDpst = array();


            for ($i = 0; $i <= date("H"); $i++) {
                $graphLabels[] = date("ga", mktime($i, date("i"), date("s"), date("m"), date("d"), date("Y")));
                $graphDataInv[] = isset($invoiceData[$i]) ? $invoiceData[$i] : 0;
                $graphDataCus[] = isset($clientData[$i]) ? $clientData[$i] : 0;
			//	$graphDataErr[] = isset($errorData[$i]) ? $errorData[$i] : 0;

				$graphDataPmnt[] = isset($paymentData[$i]) ? $paymentData[$i] : 0;

				$graphDataPrdt[] = isset($productData[$i]) ? $productData[$i] : 0;
				$graphDataDpst[] = isset($depositData[$i]) ? $depositData[$i] : 0;
            }

        } elseif ($viewPeriod == 'month') {

            $graphLabels = array();

		    $graphDataInv = array();
            $graphDataCus = array();
		//	$graphDataErr = array();

			$graphDataPmnt = array();
			$graphDataPrdt = array();
			$graphDataDpst = array();

            for ($i = 0; $i < 30; $i++) {
                $time = mktime(0, 0, 0, date("m"), date("d") - $i, date("Y"));
                $graphLabels[] = date("jS", $time);
                $graphDataInv[] = isset($invoiceData[date("j F", $time)]) ? $invoiceData[date("j F", $time)] : 0;
                $graphDataCus[] = isset($clientData[date("j F", $time)]) ? $clientData[date("j F", $time)] : 0;
			//	$graphDataErr[] = isset($errorData[date("j F", $time)]) ? $errorData[date("j F", $time)] : 0;

				$graphDataPmnt[] = isset($paymentData[date("j F", $time)]) ? $paymentData[date("j F", $time)] : 0;

				$graphDataPrdt[] = isset($productData[date("j F", $time)]) ? $productData[date("j F", $time)] : 0;
				$graphDataDpst[] = isset($depositData[date("j F", $time)]) ? $depositData[date("j F", $time)] : 0;
            }

            $graphLabels = array_reverse($graphLabels);

            $graphDataInv = array_reverse($graphDataInv);
            $graphDataCus = array_reverse($graphDataCus);
		//	$graphDataErr = array_reverse($graphDataErr);

			$graphDataPmnt = array_reverse($graphDataPmnt);

			$graphDataPrdt = array_reverse($graphDataPrdt);
			$graphDataDpst = array_reverse($graphDataDpst);

        } elseif ($viewPeriod == 'year') {

            $graphLabels = array();

			$graphDataInv = array();
            $graphDataCus = array();
		//	$graphDataErr = array();

			$graphDataPmnt = array();
			$graphDataPrdt = array();
			$graphDataDpst = array();

            for ($i = 0; $i < 12; $i++) {
                $time = mktime(0, 0, 0, date("m") - $i, 1, date("Y"));
                $graphLabels[] = date("F y", $time);
                $graphDataInv[] = isset($invoiceData[date("F Y", $time)]) ? $invoiceData[date("F Y", $time)] : 0;
                $graphDataCus[] = isset($clientData[date("F Y", $time)]) ? $clientData[date("F Y", $time)] : 0;
		//		$graphDataErr[] = isset($errorData[date("F Y", $time)]) ? $errorData[date("F Y", $time)] : 0;

				$graphDataPmnt[] = isset($paymentData[date("F Y", $time)]) ? $paymentData[date("F Y", $time)] : 0;
				$graphDataPrdt[] = isset($productData[date("F Y", $time)]) ? $productData[date("F Y", $time)] : 0;
				$graphDataDpst[] = isset($depositData[date("F Y", $time)]) ? $depositData[date("F Y", $time)] : 0;
            }

            $graphLabels = array_reverse($graphLabels);

            $graphDataInv = array_reverse($graphDataInv);
            $graphDataCus = array_reverse($graphDataCus);
		//	$graphDataErr = array_reverse($graphDataErr);

			$graphDataPmnt = array_reverse($graphDataPmnt);
			$graphDataPrdt = array_reverse($graphDataPrdt);
			$graphDataDpst = array_reverse($graphDataDpst);

        }

        $graphLabels = '"' . implode('","', $graphLabels) . '"';

        $graphDataInv = implode(',', $graphDataInv);

        $graphDataCus = implode(',', $graphDataCus);
		//$graphDataErr = implode(',', $graphDataErr);

		$graphDataPmnt = implode(',', $graphDataPmnt);
		$graphDataPrdt = implode(',', $graphDataPrdt);
		$graphDataDpst = implode(',', $graphDataDpst);

        $activeToday = ($viewPeriod == 'today') ? ' active' : '';
        $activeThisMonth = ($viewPeriod == 'month') ? ' active' : '';
        $activeThisYear = ($viewPeriod == 'year') ? ' active' : '';

		//colors
		$client_bg_color_rgb = '220,220,220,0.5';
		$client_border_color_rgb = '220,220,220,1';
		$client_point_bg_color_rgb = '220,220,220,1';
		$client_point_border_color = '#fff';




		$payment_bg_color_rgb = '66, 134, 244, 0.5';
		$payment_border_color_rgb = '66, 134, 244, 1';
		$payment_point_bg_color_rgb = '66, 134, 244, 1';
		$payment_point_border_color = '#fff';

		$deposit_bg_color_rgb = '66, 238, 244, 0.5';
		$deposit_border_color_rgb = '66, 238, 244, 1';
		$deposit_point_bg_color_rgb = '66, 238, 244, 1';
		$deposit_point_border_color = '#fff';

		$product_bg_color_rgb = '232, 163, 2, 0.5';
		$product_border_color_rgb = '232, 163, 2,1';
		$product_point_bg_color_rgb = '232, 163, 2, 1';
		$product_point_border_color = '#fff';

		$help_txt = __('Click on colors or labels for enable/disable','mw_wc_qbo_sync');

		//
		return <<<EOF
    <div style="padding:20px;">
    <div class="btn-group btn-group-sm btn-period-chooser" role="group" aria-label="...">
        <button type="button" class="btn btn-default{$activeToday}" data-period="today">Today</button>
        <button type="button" class="btn btn-default{$activeThisMonth}" data-period="month">This Month</button>
        <button type="button" class="btn btn-default{$activeThisYear}" data-period="year">This Year</button>
    </div>
	<p>{$help_txt}</p>
</div>

<div style="width:100%;height:450px;">
    <div id="ChartParent_MWQS">
        <canvas id="Chart_MWQS" height="400"></canvas>
    </div>
</div>

<script>

jQuery(document).ready(function($) {

    $('.btn-period-chooser button').click(function() {
        $('.btn-period-chooser button').removeClass('active');
        $(this).addClass('active');
		var period = $(this).data('period');
		mw_wc_qbo_sync_refresh_log_chart(period);
    });

    var lineData = {
        labels: [{$graphLabels}],
        datasets: [
            {
                label: "Customer",
                backgroundColor: "rgba({$client_bg_color_rgb})",
                borderColor: "rgba({$client_border_color_rgb})",
                pointBackgroundColor: "rgba({$client_point_bg_color_rgb})",
                pointBorderColor: "{$client_point_border_color}",
                data: [{$graphDataCus}]
            },
            {
                label: "Order",
                backgroundColor: "rgba(93,197,96,0.5)",
                borderColor: "rgba(93,197,96,1)",
                pointBackgroundColor: "rgba(93,197,96,1)",
                pointBorderColor: "#fff",
                data: [{$graphDataInv}]
            },
			{
                label: "Payment",
                backgroundColor: "rgba({$payment_bg_color_rgb})",
                borderColor: "rgba({$payment_border_color_rgb})",
                pointBackgroundColor: "rgba({$payment_point_bg_color_rgb})",
                pointBorderColor: "{$payment_point_border_color}",
                data: [{$graphDataPmnt}]
            },
			/*
			{
                label: "Deposit",
                backgroundColor: "rgba({$deposit_bg_color_rgb})",
                borderColor: "rgba({$deposit_border_color_rgb})",
                pointBackgroundColor: "rgba({$deposit_point_bg_color_rgb})",
                pointBorderColor: "{$deposit_point_border_color}",
                data: [{$graphDataDpst}]
            },
			*/
			{
                label: "Product",
                backgroundColor: "rgba({$product_bg_color_rgb})",
                borderColor: "rgba({$product_border_color_rgb})",
                pointBackgroundColor: "rgba({$product_point_bg_color_rgb})",
                pointBorderColor: "{$product_point_border_color}",
                data: [{$graphDataPrdt}]
            },

        ]
    };

    var canvas = document.getElementById("Chart_MWQS");
    var parent = document.getElementById('ChartParent_MWQS');

    canvas.width = parent.offsetWidth;
    canvas.height = parent.offsetHeight;

    var ctx = $("#Chart_MWQS");
	//var ctx = $("#Chart_MWQS").get(0).getContext("2d");
	//var chartDisplay = new Chart(document.getElementById("Chart_MWQS").getContext("2d")).Line(lineData);
	//var ctx = document.getElementById("Chart_MWQS").getContext("2d");
	var options = {
	 responsive: true,
		maintainAspectRatio: false,
		scales: {
			 yAxes: [{
				 ticks: {
					 beginAtZero: true,
					 userCallback: function(label, index, labels) {
						 // when the floored value is the same as the value we have a whole number
						 if (Math.floor(label) === label) {
							 return label;
						 }

					 },
				 }
			 }],
		},
	}
	var Chart_MWQS = Chart.Line(ctx, {
		data: lineData,
		options: options
	});

	/*
    new Chart(ctx, {
        type: 'line',
        data: lineData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
			scales: {
				 yAxes: [{
					 ticks: {
						 beginAtZero: true,
						 userCallback: function(label, index, labels) {
							 // when the floored value is the same as the value we have a whole number
							 if (Math.floor(label) === label) {
								 return label;
							 }

						 },
					 }
				 }],
			},
        }
    });
	*/
});
</script>
EOF;

	}

	public function get_tablesorter_js($item='table'){
		return <<<EOF
		<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/css/theme.blue.css" rel="stylesheet" />-->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.28.5/js/jquery.tablesorter.js"></script>
		<script type="text/javascript">
		  jQuery(function($){
			  //jQuery('{$item}').addClass('tablesorter-blue');
			  jQuery('{$item} th').css('cursor','pointer');
			  jQuery('{$item} th').each(function(){
				  var sort_th_title = jQuery(this).attr('title');
				  if (sort_th_title == null){
					  sort_th_title = '';
				  }
				  if(sort_th_title==''){
					  sort_th_title = jQuery(this).text();
				  }
				  sort_th_title = jQuery.trim(sort_th_title);
				  if(sort_th_title!=''){
					  sort_th_title = 'Sort By '+sort_th_title;
					jQuery(this).attr('title',sort_th_title);
				  }else{
					  //jQuery(this).addClass('{sorter: false}');
					  jQuery(this).attr('data-sorter','false');
					  jQuery(this).attr('data-filter','false');
				  }
			  });
			  jQuery('{$item}').tablesorter();
		  });
		</script>
EOF;
	}

	public function get_html_msg($title='',$body=''){
		$display = '
		<html>
			<head>
			<title>'.$title.'</title>
			</head>

			<body>
			'.$body.'
			</body>
		</html>';
		return $display;
	}

	public function var_p($key=''){
		if($key!=''){
			if(isset($_POST[$key])){
				if(!is_array($_POST[$key])){
					return trim($_POST[$key]);
				}
				else{
					return $_POST[$key];
				}
			}
		}
	}

	public function var_g($key=''){
		if($key!=''){
			if(isset($_GET[$key])){
				return trim($_GET[$key]);
			}
		}
	}

	public function show_sync_window_message($id, $message, $progress=0, $tot=0) {
		$d = array('message' => $message , 'progress' => $progress,'total' => $tot,'cur' => $id);
		echo json_encode($d);
		ob_flush();
		flush();
		die();
	}

	public function get_current_request_protocol(){
		if(!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])){
			 return $_SERVER['HTTP_X_FORWARDED_PROTO'];
		}
		return (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!='OFF') ? "https" : "http";
	}

	public function get_sync_window_url(){
		//$this->_p($_SERVER);
		$request_protocol = $this->get_current_request_protocol();

		$current_url = $request_protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
		$sync_window_url = site_url('index.php?mw_qbo_desktop_public_sync_window=1');

		if(strpos($current_url, 's://')===false){
			$sync_window_url = str_replace('s://','://',$sync_window_url);
		}else{
			if(strpos($sync_window_url, 's://')===false){
				$sync_window_url = str_replace('://','s://',$sync_window_url);
			}
		}

		if(strpos($current_url, '://www.')===false){
			$sync_window_url = str_replace('://www.','://',$sync_window_url);
		}else{
			if(strpos($sync_window_url, '://www.')===false){
				$sync_window_url = str_replace('://','://www.',$sync_window_url);
			}
		}

		return $sync_window_url;
	}
	
	public function get_dashboard_status_items(){
		$items = array();
		global $wpdb;

		//$quickbooks_connection = ($this->get_option('mw_wc_qbo_desk_qbo_is_connected'))?true:false;
		$quickbooks_connection = ($this->is_qwc_connected())?true:false;		

		$initial_quickbooks_data_loaded = $this->option_checked('mw_wc_qbo_desk_qbo_is_refreshed');
		$default_setting_saved = $this->option_checked('mw_wc_qbo_desk_qbo_is_default_settings');
		$mapping_active = $this->option_checked('mw_wc_qbo_desk_qbo_is_data_mapped');
		
		//
		$customer_loaded = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers WHERE `id` > 0 ");
		$product_loaded = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_items WHERE `id` > 0 ");
		$account_loaded = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_account WHERE `id` > 0 ");
		
		$items['customer_loaded'] = $customer_loaded;
		$items['product_loaded'] = $product_loaded;
		$items['account_loaded'] = $account_loaded;

		$items['quickbooks_connection'] = $quickbooks_connection;
		$items['initial_quickbooks_data_loaded'] = $initial_quickbooks_data_loaded;
		$items['default_setting_saved'] = $default_setting_saved;
		$items['mapping_active'] = $mapping_active;

		$customer_mapped = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs WHERE `qbd_customerid` !='' ");
		$items['customer_mapped'] = $customer_mapped;

		$product_mapped = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs WHERE `quickbook_product_id` !='' ");
		$items['product_mapped'] = $product_mapped;
		
		$variation_mapped = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_variation_pairs WHERE `quickbook_product_id` !='' ");
		$items['variation_mapped'] = $variation_mapped;

		$gateway_mapped = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod WHERE `qbo_account_id` !='' ");
		$items['gateway_mapped'] = $gateway_mapped;

		$tax_mapped = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_tax WHERE `qbo_tax_code` !='' ");
		$items['tax_mapped'] = $tax_mapped;

		//from log table
		$customer_synced = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_log WHERE `log_type` = 'Customer' AND `status` = 1 ");
		$items['customer_synced'] = $customer_synced;

		$order_synced = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_log WHERE `log_type` = 'Invoice' AND `status` = 1 ");
		$items['order_synced'] = $order_synced;

		$product_synced = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_log WHERE `log_type` = 'Product' AND `status` = 1 ");
		$items['product_synced'] = $product_synced;

		$error = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_log WHERE `status` = 0 ");
		$items['error'] = $error;
		
		//woocommerce stats
		$wc_total_customer = (int) $this->count_customers();
		$wc_total_product = (int) $this->count_woocommerce_product_list();
		$wc_total_variation = (int) $this->count_woocommerce_variation_list();
		
		$wc_p_methods = array();
		$available_gateways = WC()->payment_gateways()->payment_gateways;
		if(is_array($available_gateways) && count($available_gateways)){
			foreach($available_gateways as $key=>$value){
				if($value->enabled=='yes'){
					$wc_p_methods[$value->id] = $value->title;
				}		
			}
		}
		
		$wc_total_gateway = count($wc_p_methods);
		
		$items['wc_total_customer'] = $wc_total_customer;
		$items['wc_total_product'] = $wc_total_product;
		$items['wc_total_variation'] = $wc_total_variation;
		$items['wc_total_gateway'] = $wc_total_gateway;
		
		return $items;
	}

	public function get_world_currency_list($symbol=false){
		if($symbol){
			return array(
				'AED' => '&#1583;.&#1573;', // ?
				'AFN' => '&#65;&#102;',
				'ALL' => '&#76;&#101;&#107;',
				'AMD' => 'AMD',
				'ANG' => '&#402;',
				'AOA' => '&#75;&#122;', // ?
				'ARS' => '&#36;',
				'AUD' => '&#36;',
				'AWG' => '&#402;',
				'AZN' => '&#1084;&#1072;&#1085;',
				'BAM' => '&#75;&#77;',
				'BBD' => '&#36;',
				'BDT' => '&#2547;', // ?
				'BGN' => '&#1083;&#1074;',
				'BHD' => '.&#1583;.&#1576;', // ?
				'BIF' => '&#70;&#66;&#117;', // ?
				'BMD' => '&#36;',
				'BND' => '&#36;',
				'BOB' => '&#36;&#98;',
				'BRL' => '&#82;&#36;',
				'BSD' => '&#36;',
				'BTN' => '&#78;&#117;&#46;', // ?
				'BWP' => '&#80;',
				'BYR' => '&#112;&#46;',
				'BZD' => '&#66;&#90;&#36;',
				'CAD' => '&#36;',
				'CDF' => '&#70;&#67;',
				'CHF' => '&#67;&#72;&#70;',
				'CLF' => 'CLF', // ?
				'CLP' => '&#36;',
				'CNY' => '&#165;',
				'COP' => '&#36;',
				'CRC' => '&#8353;',
				'CUP' => '&#8396;',
				'CVE' => '&#36;', // ?
				'CZK' => '&#75;&#269;',
				'DJF' => '&#70;&#100;&#106;', // ?
				'DKK' => '&#107;&#114;',
				'DOP' => '&#82;&#68;&#36;',
				'DZD' => '&#1583;&#1580;', // ?
				'EGP' => '&#163;',
				'ETB' => '&#66;&#114;',
				'EUR' => '&#8364;',
				'FJD' => '&#36;',
				'FKP' => '&#163;',
				'GBP' => '&#163;',
				'GEL' => '&#4314;', // ?
				'GHS' => '&#162;',
				'GIP' => '&#163;',
				'GMD' => '&#68;', // ?
				'GNF' => '&#70;&#71;', // ?
				'GTQ' => '&#81;',
				'GYD' => '&#36;',
				'HKD' => '&#36;',
				'HNL' => '&#76;',
				'HRK' => '&#107;&#110;',
				'HTG' => '&#71;', // ?
				'HUF' => '&#70;&#116;',
				'IDR' => '&#82;&#112;',
				'ILS' => '&#8362;',
				'INR' => '&#8377;',
				'IQD' => '&#1593;.&#1583;', // ?
				'IRR' => '&#65020;',
				'ISK' => '&#107;&#114;',
				'JEP' => '&#163;',
				'JMD' => '&#74;&#36;',
				'JOD' => '&#74;&#68;', // ?
				'JPY' => '&#165;',
				'KES' => '&#75;&#83;&#104;', // ?
				'KGS' => '&#1083;&#1074;',
				'KHR' => '&#6107;',
				'KMF' => '&#67;&#70;', // ?
				'KPW' => '&#8361;',
				'KRW' => '&#8361;',
				'KWD' => '&#1583;.&#1603;', // ?
				'KYD' => '&#36;',
				'KZT' => '&#1083;&#1074;',
				'LAK' => '&#8365;',
				'LBP' => '&#163;',
				'LKR' => '&#8360;',
				'LRD' => '&#36;',
				'LSL' => '&#76;', // ?
				'LTL' => '&#76;&#116;',
				'LVL' => '&#76;&#115;',
				'LYD' => '&#1604;.&#1583;', // ?
				'MAD' => '&#1583;.&#1605;.', //?
				'MDL' => '&#76;',
				'MGA' => '&#65;&#114;', // ?
				'MKD' => '&#1076;&#1077;&#1085;',
				'MMK' => '&#75;',
				'MNT' => '&#8366;',
				'MOP' => '&#77;&#79;&#80;&#36;', // ?
				'MRO' => '&#85;&#77;', // ?
				'MUR' => '&#8360;', // ?
				'MVR' => '.&#1923;', // ?
				'MWK' => '&#77;&#75;',
				'MXN' => '&#36;',
				'MYR' => '&#82;&#77;',
				'MZN' => '&#77;&#84;',
				'NAD' => '&#36;',
				'NGN' => '&#8358;',
				'NIO' => '&#67;&#36;',
				'NOK' => '&#107;&#114;',
				'NPR' => '&#8360;',
				'NZD' => '&#36;',
				'OMR' => '&#65020;',
				'PAB' => '&#66;&#47;&#46;',
				'PEN' => '&#83;&#47;&#46;',
				'PGK' => '&#75;', // ?
				'PHP' => '&#8369;',
				'PKR' => '&#8360;',
				'PLN' => '&#122;&#322;',
				'PYG' => '&#71;&#115;',
				'QAR' => '&#65020;',
				'RON' => '&#108;&#101;&#105;',
				'RSD' => '&#1044;&#1080;&#1085;&#46;',
				'RUB' => '&#1088;&#1091;&#1073;',
				'RWF' => '&#1585;.&#1587;',
				'SAR' => '&#65020;',
				'SBD' => '&#36;',
				'SCR' => '&#8360;',
				'SDG' => '&#163;', // ?
				'SEK' => '&#107;&#114;',
				'SGD' => '&#36;',
				'SHP' => '&#163;',
				'SLL' => '&#76;&#101;', // ?
				'SOS' => '&#83;',
				'SRD' => '&#36;',
				'STD' => '&#68;&#98;', // ?
				'SVC' => '&#36;',
				'SYP' => '&#163;',
				'SZL' => '&#76;', // ?
				'THB' => '&#3647;',
				'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
				'TMT' => '&#109;',
				'TND' => '&#1583;.&#1578;',
				'TOP' => '&#84;&#36;',
				'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
				'TTD' => '&#36;',
				'TWD' => '&#78;&#84;&#36;',
				'TZS' => 'TZS',
				'UAH' => '&#8372;',
				'UGX' => '&#85;&#83;&#104;',
				'USD' => '&#36;',
				'UYU' => '&#36;&#85;',
				'UZS' => '&#1083;&#1074;',
				'VEF' => '&#66;&#115;',
				'VND' => '&#8363;',
				'VUV' => '&#86;&#84;',
				'WST' => '&#87;&#83;&#36;',
				'XAF' => '&#70;&#67;&#70;&#65;',
				'XCD' => '&#36;',
				'XDR' => 'XDR',
				'XOF' => 'XOF',
				'XPF' => '&#70;',
				'YER' => '&#65020;',
				'ZAR' => '&#82;',
				'ZMK' => '&#90;&#75;', // ?
				'ZWL' => '&#90;&#36;',
			);
		}
		$cur_arr = array (
			'ALL' => 'Albania Lek',
			'AFN' => 'Afghanistan Afghani',
			'ARS' => 'Argentina Peso',
			'AWG' => 'Aruba Guilder',
			'AUD' => 'Australia Dollar',
			'AZN' => 'Azerbaijan New Manat',
			'BSD' => 'Bahamas Dollar',
			'BBD' => 'Barbados Dollar',
			'BDT' => 'Bangladeshi taka',
			'BYR' => 'Belarus Ruble',
			'BZD' => 'Belize Dollar',
			'BMD' => 'Bermuda Dollar',
			'BOB' => 'Bolivia Boliviano',
			'BAM' => 'Bosnia and Herzegovina Convertible Marka',
			'BWP' => 'Botswana Pula',
			'BGN' => 'Bulgaria Lev',
			'BRL' => 'Brazil Real',
			'BND' => 'Brunei Darussalam Dollar',
			'KHR' => 'Cambodia Riel',
			'CAD' => 'Canada Dollar',
			'KYD' => 'Cayman Islands Dollar',
			'CLP' => 'Chile Peso',
			'CNY' => 'China Yuan Renminbi',
			'COP' => 'Colombia Peso',
			'CRC' => 'Costa Rica Colon',
			'HRK' => 'Croatia Kuna',
			'CUP' => 'Cuba Peso',
			'CZK' => 'Czech Republic Koruna',
			'DKK' => 'Denmark Krone',
			'DOP' => 'Dominican Republic Peso',
			'XCD' => 'East Caribbean Dollar',
			'EGP' => 'Egypt Pound',
			'SVC' => 'El Salvador Colon',
			'EEK' => 'Estonia Kroon',
			'EUR' => 'Euro Member Countries',
			'FKP' => 'Falkland Islands (Malvinas) Pound',
			'FJD' => 'Fiji Dollar',
			'GHC' => 'Ghana Cedis',
			'GIP' => 'Gibraltar Pound',
			'GTQ' => 'Guatemala Quetzal',
			'GGP' => 'Guernsey Pound',
			'GYD' => 'Guyana Dollar',
			'HNL' => 'Honduras Lempira',
			'HKD' => 'Hong Kong Dollar',
			'HUF' => 'Hungary Forint',
			'ISK' => 'Iceland Krona',
			'INR' => 'India Rupee',
			'IDR' => 'Indonesia Rupiah',
			'IRR' => 'Iran Rial',
			'IMP' => 'Isle of Man Pound',
			'ILS' => 'Israel Shekel',
			'JMD' => 'Jamaica Dollar',
			'JPY' => 'Japan Yen',
			'JEP' => 'Jersey Pound',
			'KZT' => 'Kazakhstan Tenge',
			'KPW' => 'Korea (North) Won',
			'KRW' => 'Korea (South) Won',
			'KGS' => 'Kyrgyzstan Som',
			'LAK' => 'Laos Kip',
			'LVL' => 'Latvia Lat',
			'LBP' => 'Lebanon Pound',
			'LRD' => 'Liberia Dollar',
			'LTL' => 'Lithuania Litas',
			'MKD' => 'Macedonia Denar',
			'MYR' => 'Malaysia Ringgit',
			'MUR' => 'Mauritius Rupee',
			'MXN' => 'Mexico Peso',
			'MNT' => 'Mongolia Tughrik',
			'MZN' => 'Mozambique Metical',
			'NAD' => 'Namibia Dollar',
			'NPR' => 'Nepal Rupee',
			'ANG' => 'Netherlands Antilles Guilder',
			'NZD' => 'New Zealand Dollar',
			'NIO' => 'Nicaragua Cordoba',
			'NGN' => 'Nigeria Naira',
			'NOK' => 'Norway Krone',
			'OMR' => 'Oman Rial',
			'PKR' => 'Pakistan Rupee',
			'PAB' => 'Panama Balboa',
			'PYG' => 'Paraguay Guarani',
			'PEN' => 'Peru Nuevo Sol',
			'PHP' => 'Philippines Peso',
			'PLN' => 'Poland Zloty',
			'QAR' => 'Qatar Riyal',
			'RON' => 'Romania New Leu',
			'RUB' => 'Russia Ruble',
			'SHP' => 'Saint Helena Pound',
			'SAR' => 'Saudi Arabia Riyal',
			'RSD' => 'Serbia Dinar',
			'SCR' => 'Seychelles Rupee',
			'SGD' => 'Singapore Dollar',
			'SBD' => 'Solomon Islands Dollar',
			'SOS' => 'Somalia Shilling',
			'ZAR' => 'South Africa Rand',
			'LKR' => 'Sri Lanka Rupee',
			'SEK' => 'Sweden Krona',
			'CHF' => 'Switzerland Franc',
			'SRD' => 'Suriname Dollar',
			'SYP' => 'Syria Pound',
			'TWD' => 'Taiwan New Dollar',
			'THB' => 'Thailand Baht',
			'TTD' => 'Trinidad and Tobago Dollar',
			'TRY' => 'Turkey Lira',
			'TRL' => 'Turkey Lira',
			'TVD' => 'Tuvalu Dollar',
			'UAH' => 'Ukraine Hryvna',
			'GBP' => 'United Kingdom Pound',
			'UGX' => 'Uganda Shilling',
			'USD' => 'United States Dollar',
			'UYU' => 'Uruguay Peso',
			'UZS' => 'Uzbekistan Som',
			'VEF' => 'Venezuela Bolivar',
			'VND' => 'Viet Nam Dong',
			'YER' => 'Yemen Rial',
			'ZWD' => 'Zimbabwe Dollar'
		);
		if($symbol=='name'){
			return $cur_arr;
		}
		return array_combine(array_keys($cur_arr),array_keys($cur_arr));
	}
	
	public function check_if_real_time_push_enable_for_item($item=''){
		if($item!=''){
			//
			if($item == 'inventory'){return true;}
			$mw_wc_qbo_desk_rt_push_enable = $this->option_checked('mw_wc_qbo_desk_rt_push_enable');
			if(!$mw_wc_qbo_desk_rt_push_enable){
				return false;
			}
			$mw_wc_qbo_sync_rt_push_items = (string) $this->get_option('mw_wc_qbo_desk_rt_push_items');
			if($mw_wc_qbo_sync_rt_push_items!=''){
				$mw_wc_qbo_sync_rt_push_items = explode(',',$mw_wc_qbo_sync_rt_push_items);
				if(is_array($mw_wc_qbo_sync_rt_push_items) && count($mw_wc_qbo_sync_rt_push_items)){
					if(in_array($item,$mw_wc_qbo_sync_rt_push_items)){
						return true;
					}
				}
			}else{
				//return true;
			}
		}
		return false;
	}
	
	public function check_if_real_time_pull_enable_for_item($item=''){
		if($item!=''){
			$mw_wc_qbo_desk_rt_pull_enable = $this->option_checked('mw_wc_qbo_desk_rt_pull_enable');
			if(!$mw_wc_qbo_desk_rt_pull_enable){
				return false;
			}
			$mw_wc_qbo_desk_rt_pull_items = (string) $this->get_option('mw_wc_qbo_desk_rt_pull_items');
			if($mw_wc_qbo_desk_rt_pull_items!=''){
				$mw_wc_qbo_desk_rt_pull_items = explode(',',$mw_wc_qbo_desk_rt_pull_items);
				if(is_array($mw_wc_qbo_desk_rt_pull_items) && count($mw_wc_qbo_desk_rt_pull_items)){
					if(in_array($item,$mw_wc_qbo_desk_rt_pull_items)){
						return true;
					}
				}
			}else{
				//return true;
			}
		}
		return false;
	}
	
	public function if_show_sync_status($it_pp,$ss_spe_limit=100,$i_type='',$s_type='push'){
		if($it_pp<=$ss_spe_limit){
			return true;
		}
		return false;
	}
	
	public function get_plugin_db_tbl_list(){
		global $wpdb;
		$tl_q = "SHOW TABLES LIKE '{$wpdb->prefix}mw_wc_qbo_desk_qbd\_%'";
		$tbl_list = $this->get_data($tl_q);

		$p_tbls = array();
		if(is_array($tbl_list) && count($tbl_list)){
			foreach($tbl_list as $tl){
				if(is_array($tl) && count($tl)){
					$tl_v = current($tl);$tl_v = (string) $tl_v;$tl_v = trim($tl_v);
					if($tl_v!=''){
						$p_tbls[] = $tl_v;
					}
				}
			}
		}
		return $p_tbls;
	}
	
	public function db_check_get_fields_details($s_tbf_list=array()){
		$tb_f_list = array();
		$tbls = $this->get_plugin_db_tbl_list();
		if(is_array($tbls) && count($tbls)){
			foreach($tbls as $tln){
				$tcq = "SHOW COLUMNS FROM {$tln}";
				$tc_list = $this->get_data($tcq);
				$tc_tmp_arr = array();
				if(is_array($tc_list) && count($tc_list)){
					foreach($tc_list as $tc_l){
						$tc_tmp_arr[$tc_l['Field']] = $tc_l;
					}
				}				
				$tb_f_list[$tln] = $tc_tmp_arr;
			}
		}		
		return $tb_f_list;
	}
	
	public function get_woo_version_number(){
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
			// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';
		
		// If the plugin version number is set, return it 
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			return $plugin_folder[$plugin_file]['Version'];

		} else {
		// Otherwise return null
			return NULL;
		}
	}
	
	public function get_queue_count($qt='pending'){
		$cnt = 0;
		global $wpdb;
		$tbl = 'quickbooks_queue';
		
		if($qt=='pending'){
			$whr = " AND qb_status = 'q' AND dequeue_datetime IS NULL";
			$cnt = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$tbl} WHERE `quickbooks_queue_id` > 0 {$whr} ");
		}
		return $cnt;
	}
	
	public function get_wc_product_cat_arr(){
		$cl = get_categories(array('taxonomy'=>'product_cat','orderby'=>'name','hide_empty'=>0));
		$cl_arr = array();		
		if(count($cl)){
			foreach($cl as $pc){
				$cl_arr[$pc->term_id] = $pc->name;
			}
		}
		return $cl_arr;
	}
	
	public function get_str_between($content,$start,$end){
		$r = explode($start, $content);
		if (isset($r[1])){
			$r = explode($end, $r[1]);
			return $r[0];
		}
		return '';
	}
	
	public function get_default_ord_ba(){
		$mw_wc_qbo_desk_ord_bill_addr_map = '{_billing_first_name} {_billing_last_name}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_bill_addr_map.= '{_billing_company}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_bill_addr_map.= '{_billing_address_1}, {_billing_address_2}'.PHP_EOL;		
		$mw_wc_qbo_desk_ord_bill_addr_map.= '{_billing_city} {_billing_state} {_billing_postcode}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_bill_addr_map.= '{_billing_country}';
		return $mw_wc_qbo_desk_ord_bill_addr_map;
	}
	
	public function get_default_ord_sa(){
		$mw_wc_qbo_desk_ord_ship_addr_map = '{_shipping_first_name} {_shipping_last_name}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_ship_addr_map.= '{_shipping_company}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_ship_addr_map.= '{_shipping_address_1}, {_shipping_address_2}'.PHP_EOL;		
		$mw_wc_qbo_desk_ord_ship_addr_map.= '{_shipping_city} {_shipping_state} {_shipping_postcode}'.PHP_EOL;
		$mw_wc_qbo_desk_ord_ship_addr_map.= '{_shipping_country}';
		return $mw_wc_qbo_desk_ord_ship_addr_map;
	}
	
	/**/
	public function ord_ba_ext_formats(){
		$ext_f = array();
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-address-field-compt-shawshawnk') && $this->check_sh_cbsafc_shawshawnk_hash()){
			$ext_f['store_number_billing'] = '{store_number_billing}';	
		}			
		return $ext_f;
	}
	
	public function ord_sa_ext_formats(){
		$ext_f = array();
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-address-field-compt-shawshawnk') && $this->check_sh_cbsafc_shawshawnk_hash()){
			$ext_f['store_number_shipping'] = '{store_number_shipping}';
		}
		return $ext_f;
	}
	
	public function get_qbd_product_price_field_list(){
		$pfl = array();
		$pfl['Price'] = 'Price';
		$pfl['SalesPrice'] = 'SalesPrice';
		$pfl['Retail Price'] = 'Retail Price';
		return $pfl;
	}
	
	public function get_qbd_ext_price_field(){
		$cpf = array();
		$cpf['Retail Price'] = 'Retail Price';
		return $cpf;
	}
	
	public function is_valid_qbd_product($qbd_id,$type=''){
		global $wpdb;
		$qbd_id = trim($qbd_id);
		
		if($qbd_id!=''){
			$ext_whr = '';
			$type = $this->sanitize($type);
			if($type!=''){
				$ext_whr.= " AND `product_type` = '{$type}' ";
			}
			$icq = $wpdb->prepare("SELECT `qbd_id` FROM  {$wpdb->prefix}mw_wc_qbo_desk_qbd_items WHERE `qbd_id` = %s AND qbd_id !='' {$ext_whr} ",$qbd_id);
			$ic_d = $this->get_row($icq);
			
			if(is_array($ic_d) && count($ic_d)){
				return true;
			}
		}
		
		return false;
	}
	
	public function Add_Pull_Inventory_Queue($qbd_id,$auto=false){		
		if(!$this->is_qwc_connected()){
			return false;
		}
		global $wpdb;
		
		$qbd_id = trim($qbd_id);
		if($qbd_id!=''){
			$map_data = $this->get_row("SELECT `wc_product_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `quickbook_product_id` = '{$qbd_id}' AND `wc_product_id` > 0 ");
			
			$is_variation = false;
			if(empty($map_data)){
				$map_data = $this->get_row("SELECT `wc_variation_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `quickbook_product_id` = '{$qbd_id}' AND `wc_variation_id` > 0 ");
				$is_variation = true;
			}
			
			if(is_array($map_data) && count($map_data)){
				$extra = null;
				$qp_type = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','product_type','qbd_id',$qbd_id);
				if($qp_type=='InventoryAssembly'){
					if($this->option_checked('mw_wc_qbo_desk_use_max_as_qoh_iasmbly_invnt_pull')){
						if(!$this->if_queue_exists('UPDATE_INVENTORY_A_MAX',$qbd_id)){
							$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
							$Queue->enqueue('UPDATE_INVENTORY_A_MAX', $qbd_id, 0,array('manual'=>true),$this->get_qbun());							
						}
						return true;
					}
					
					$extra = array();
					$extra['InventoryAssembly'] = true;
				}
				
				$priority = 0;
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('WC_UPDATE_INVENTORY', $qbd_id,$priority,$extra,$this->get_qbun());
				$this->save_log(array('log_type'=>'Inventory','log_title'=>'Pull QBD Inventory #'.$qbd_id,'details'=>'Inventory added into queue','status'=>3));
				return true;
			}else{
				if(!$auto){
					$this->save_log(array('log_type'=>'Inventory','log_title'=>'Pull QBD Inventory #'.$qbd_id,'details'=>'Inventory not mapped','status'=>3));
				}
				
			}			
		}
		
		return false;
	}
	
	public function Add_Pull_ProductPrice_Queue($qbd_id){
		if(!$this->is_qwc_connected()){
			return false;
		}
		global $wpdb;
		
		$qbd_id = trim($qbd_id);
		if($qbd_id!=''){
			$map_data = $this->get_row("SELECT `wc_product_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `quickbook_product_id` = '{$qbd_id}' AND `wc_product_id` > 0 ");
			
			$is_variation = false;
			if(empty($map_data)){
				$map_data = $this->get_row("SELECT `wc_variation_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `quickbook_product_id` = '{$qbd_id}' AND `wc_variation_id` > 0 ");
				$is_variation = true;
			}
			
			if(is_array($map_data) && count($map_data)){
				$priority = 0;
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('WC_UPDATE_PRODUCT_PRICE', $qbd_id,$priority,null,$this->get_qbun());
				/**/
				if($this->check_sh_woorbp_qbpricelevel_hash() && $this->option_checked('mw_wc_qbo_desk_woorbp_qbpricelevel_compt_ed')){
					$Queue->enqueue('PriceLevel_Inventory', $qbd_id,$priority,null,$this->get_qbun());
				}
				
				$this->save_log(array('log_type'=>'Price','log_title'=>'Pull QBD Product Price #'.$qbd_id,'details'=>'Product Price added into queue','status'=>3));
				return true;
			}else{
				$this->save_log(array('log_type'=>'Price','log_title'=>'Pull QBD Product Price #'.$qbd_id,'details'=>'Product Price not mapped','status'=>3));
			}			
		}
		
		return false;
	}
	
	public function UpdateWooCommerceInventory($inventory_data){
		if(!$this->is_qwc_connected()){
			return false;
		}		
		
		//$this->add_test_log(print_r($inventory_data,true));
		
		global $wpdb;
		$qbo_inventory_id = $this->get_array_isset($inventory_data,'qbo_inventory_id','');
		$manual = $this->get_array_isset($inventory_data,'manual',false);

		$QtyOnHand = $this->get_array_isset($inventory_data,'QuantityOnHand',0);
		$Name = $this->get_array_isset($inventory_data,'Name','');
		
		$Max = $this->get_array_isset($inventory_data,'Max',0);
		
		$ext_log_txt = '';
		
		if($qbo_inventory_id!=''){
			//Changes needed here for optimization
			$is_Inventory = $this->is_valid_qbd_product($qbo_inventory_id,'Inventory');
			
			$is_InventoryAssembly = false;
			if(!$is_Inventory){
				if(isset($inventory_data['is_InventoryAssembly'])){
					$is_InventoryAssembly = $this->get_array_isset($inventory_data,'is_InventoryAssembly',false);
				}else{
					$is_InventoryAssembly = $this->is_valid_qbd_product($qbo_inventory_id,'InventoryAssembly');
				}				
			}
			
			if($is_Inventory || $is_InventoryAssembly){
				/**/
				if($Max == 1 && $is_InventoryAssembly){
					$qb_item_assembly = $this->get_row_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id',$qbo_inventory_id);
					if(is_array($qb_item_assembly) && !empty($qb_item_assembly)){
						$info_arr = $qb_item_assembly['info_arr'];
						if(!empty($info_arr)){
							$info_arr = @unserialize($info_arr);
							if(is_array($info_arr) && !empty($info_arr)){
								$info_arr['MAX_Q'] = $QtyOnHand;
								$save_data = array();
								$save_data['info_arr'] = serialize($info_arr);
								$wpdb->update($wpdb->prefix.'mw_wc_qbo_desk_qbd_items',$save_data,array('id'=>$qb_item_assembly['id']),'',array('%d'));
							}
						}
					}
					
				}
				
				//$ext_log = "\n".'Name: '.$Name;
				
				//get_row
				$map_data = $this->get_data("SELECT `wc_product_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `quickbook_product_id` = '{$qbo_inventory_id}' AND `wc_product_id` > 0 ");
				
				$is_variation = false;
				if(empty($map_data)){
					//get_row
					$map_data = $this->get_data("SELECT `wc_variation_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `quickbook_product_id` = '{$qbo_inventory_id}' AND `wc_variation_id` > 0 ");
					$is_variation = true;
				}
				
				if(empty($map_data)){
					if($manual){
						//Log Entry
					}
					return false;
				}				
				
				//$this->add_test_log(print_r($map_data,true));
				
				//Multiple Support
				if(is_array($map_data)){
					foreach($map_data as $map_data_c){						
						$wc_product_id = 0;
						$is_variation_parent = false;
						
						if($is_variation){
							$wc_variation_id = $map_data_c['wc_variation_id'];
							$variation_manage_stock = get_post_meta($wc_variation_id,'_manage_stock',true);

							if($variation_manage_stock=='yes'){
								$wc_product_id = $wc_variation_id;
							}else{
								$parent_id = (int) $this->get_field_by_val($wpdb->posts,'post_parent','ID',$wc_variation_id);
								if($parent_id){
									$wc_product_id = $parent_id;
									$is_variation_parent = true;
								}
							}
						}else{
							$wc_product_id = $map_data_c['wc_product_id'];
						}				
						
						$product_meta = get_post_meta($wc_product_id);
						if(!$product_meta){
							if($manual){
								//Log Entry
							}
							return false;
						}
						
						$P_Name = $this->get_field_by_val($wpdb->posts,'post_title','ID',(int) $wc_product_id);
						$ext_log = "\n".'Name: '.$P_Name;
						
						/**/
						$P_Sku = get_post_meta($wc_product_id,'_sku',true);
						$ext_log .= "\n".'SKU: '.$P_Sku;
						
						$_manage_stock = (isset($product_meta['_manage_stock'][0]))?$product_meta['_manage_stock'][0]:'no';
						$_backorders = (isset($product_meta['_backorders'][0]))?$product_meta['_backorders'][0]:'no';
						$_stock = (isset($product_meta['_stock'][0]))?$product_meta['_stock'][0]:0;
						
						$is_valid_wc_inventory = false;
						if($_manage_stock=='yes'){
							$is_valid_wc_inventory = true;
						}
						
						if(!$is_valid_wc_inventory){
							if($manual){
								//Log Entry
							}
							return false;
						}
						
						if($is_variation_parent){
							$parent_qbo_inventory_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs','quickbook_product_id','wc_product_id',$wc_product_id);

							if($parent_qbo_inventory_id!=''){
								if($manual){
									//Log Entry
									//$this->save_log(array('log_type'=>'Product','log_title'=>'','details'=>'','status'=>''));
								}
								return false;
							}

							//Needs to do Something
							$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
							$Queue->enqueue('WC_UPDATE_INVENTORY', $wc_product_id,null,null,$this->get_qbun());
							return false;		
						}
						
						/*New*/
						if(isset($inventory_data['wmior_compt'])){
							$InventorySiteRef = trim($inventory_data['InventorySiteRef']);
							$InventorySiteRef_FullName = $this->get_array_isset($inventory_data,'InventorySiteRef_FullName','');
							
							$InventorySiteLocationRef = trim($inventory_data['InventorySiteLocationRef']);
							$InventorySiteLocationRef_FullName = $this->get_array_isset($inventory_data,'InventorySiteLocationRef_FullName','');
							
							$Inv_Site_Check_Val = '';
							if($this->is_inv_site_bin_allowed()){
								if(!empty($InventorySiteLocationRef)){
									$Inv_Site_Check_Val = $InventorySiteRef.':'.$InventorySiteLocationRef;
								}						
							}else{
								$Inv_Site_Check_Val = $InventorySiteRef;
							}
							//$this->add_test_log('Test'.PHP_EOL);
							if(!empty($InventorySiteRef) && !empty($Inv_Site_Check_Val)){
								$mw_wc_qbo_desk_compt_wmior_lis_mv = $inventory_data['mw_wc_qbo_desk_compt_wmior_lis_mv'];
								//$this->add_test_log(print_r($mw_wc_qbo_desk_compt_wmior_lis_mv,true));
								//$this->add_test_log(print_r($Inv_Site_Check_Val,true));
								if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && in_array($Inv_Site_Check_Val,$mw_wc_qbo_desk_compt_wmior_lis_mv)){
									$w_loc_id = (int) array_search ($Inv_Site_Check_Val, $mw_wc_qbo_desk_compt_wmior_lis_mv);
									if($w_loc_id > 0){
										$loc_stock = (isset($product_meta['_stocks_location_'.$w_loc_id][0]))?$product_meta['_stocks_location_'.$w_loc_id][0]:0;
										$L_Name = $this->get_field_by_val($wpdb->posts,'post_title','ID',$w_loc_id);
										if($QtyOnHand!=$loc_stock){
											update_post_meta($wc_product_id, '_stocks_location_'.$w_loc_id, $QtyOnHand);										
											$log_title = $ext_log_txt.'Import Inventory #'.$qbo_inventory_id;										
											$log_details = "WooCommerce Product #{$wc_product_id} {$L_Name} location stock updated from {$loc_stock} to {$QtyOnHand} ".$ext_log;										
											$this->save_log(array('log_type'=>'Inventory','log_title'=>$log_title,'details'=>$log_details,'status'=>1),true);
											
											update_option('mw_wc_qbo_desk_rt_inventory_p_s_time',$this->get_cdt());
											return $wc_product_id;
										}else{
											$log_title = $ext_log_txt.'Import Inventory #'.$qbo_inventory_id;
											$log_details = "Stocks on both ends are same in location {$L_Name} (".$QtyOnHand.").".$ext_log;
											//$this->save_log(array('log_type'=>'Inventory','log_title'=>$log_title,'details'=>$log_details,'status'=>2),true);
										}										
									}
								}
							}
							return false;
						}
						
						if($QtyOnHand!=$_stock){
							$_stock = number_format(floatval($_stock),2);
							//update_post_meta($wc_product_id, '_stock', $QtyOnHand);
							
							if($QtyOnHand && $QtyOnHand>0){
								//update_post_meta($wc_product_id, '_stock_status', 'instock');
							}else{
								//update_post_meta($wc_product_id, '_stock_status', 'outofstock');
							}
							
							//wp_set_post_terms( $wc_product_id, 'outofstock', 'product_visibility', true );
							//wc_delete_product_transients( $wc_product_id ); // Clear/refresh the variation cache
							
							wc_update_product_stock($wc_product_id,$QtyOnHand);
							
							//
							$this->set_session_val('prevent_rt_inventory_push_ot',1);
							
							$log_title = $ext_log_txt.'Import Inventory #'.$qbo_inventory_id;
							$log_details = "WooCommerce Product #{$wc_product_id} stock updated from {$_stock} to {$QtyOnHand} ".$ext_log;
							
							$this->save_log(array('log_type'=>'Inventory','log_title'=>$log_title,'details'=>$log_details,'status'=>1),true);
							//
							if(!$manual){
								update_option('mw_wc_qbo_desk_rt_inventory_p_s_time',$this->get_cdt());
							}					
						}else{
							if($manual){
								$log_title = $ext_log_txt.'Import Inventory #'.$qbo_inventory_id;
								$log_details = "Stocks on both ends are same (".$QtyOnHand.").".$ext_log;
								$this->save_log(array('log_type'=>'Inventory','log_title'=>$log_title,'details'=>$log_details,'status'=>2),true);
							}					
						}
					}
				}				
			}
		}
		
	}
	
	/**/
	public function Adjust_wmior_product_total_stock_after_locations_stock_update($wc_product_id,$qbo_inventory_id,$qpn){
		global $wpdb;
		$wc_product_id = (int) $wc_product_id;
		if($wc_product_id > 0 && !empty($qbo_inventory_id)){
			$wi_loc_whr = "post_type='mw_location'";// AND post_status='publish'
			$wi_loc_arr = $this->get_tbl($wpdb->posts,'ID, post_title',$wi_loc_whr,'post_title ASC');			
			$warehouse_ids = array();
			if(is_array($wi_loc_arr) && !empty($wi_loc_arr)){
				foreach($wi_loc_arr as $wld){
					$warehouse_ids[$wld['ID']] = $wld['post_title'];
				}
			}
			//$this->_p($warehouse_ids);
			if(!empty($warehouse_ids)){
				$pr_stock_old = get_post_meta($wc_product_id,'_stock',true);
				$pr_stock_new = 0;
				foreach($warehouse_ids as $wh_id => $wh_name) {
					$mw_whstock = get_post_meta($wc_product_id,"_stocks_location_{$wh_id}",true);
					if(!empty($mw_whstock)) {
						$pr_stock_new  += $mw_whstock;
					}
				}
				
				if($pr_stock_old != $pr_stock_new){					
					$pr_stock_old = number_format(floatval($pr_stock_old),2);
					wc_update_product_stock($wc_product_id,$pr_stock_new);
					
					$this->set_session_val('prevent_rt_inventory_push_ot',1);
					
					$P_Name = $this->get_field_by_val($wpdb->posts,'post_title','ID',(int) $wc_product_id);
					$ext_log = "\n".'Name: '.$P_Name;
					$ext_log_txt = '';
					
					$log_title = $ext_log_txt.'Import Inventory #'.$qbo_inventory_id;
					$log_details = "WooCommerce Product #{$wc_product_id} total stock updated from {$pr_stock_old} to {$pr_stock_new} ".$ext_log;
					
					$this->save_log(array('log_type'=>'Inventory','log_title'=>$log_title,'details'=>$log_details,'status'=>1),true);
					
					update_option('mw_wc_qbo_desk_rt_inventory_p_s_time',$this->get_cdt());
				}
			}
		}
	}
	
	/**/
	public function UpdateWooCommercePriceLevelPrice($product_data,$qbpricelevel_wrpl_mv=array()){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		if(is_array($qbpricelevel_wrpl_mv) && !empty($qbpricelevel_wrpl_mv)){
			global $wpdb;
			$qbo_product_id = $this->get_array_isset($product_data,'qbo_product_id','');
			$manual = $this->get_array_isset($product_data,'manual',false);

			$Price = $this->get_array_isset($product_data,'Price',0);
			$Name = $this->get_array_isset($product_data,'Name','');
			
			$PriceLevelListID = $this->get_array_isset($product_data,'PriceLevelListID','');
			$PriceLevelName = $this->get_array_isset($product_data,'PriceLevelName','');
			
			if(empty($Price)){
				return false;
			}
			
			$ext_log_txt = '';
			
			if($qbo_product_id!=''){
				if($this->is_valid_qbd_product($qbo_product_id)){
					//$ext_log = "\n".'Name: '.$Name;
					
					//get_row
					$map_data = $this->get_data("SELECT `wc_product_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `quickbook_product_id` = '{$qbo_product_id}' AND `wc_product_id` > 0 ");
					
					$is_variation = false;
					if(empty($map_data)){
						//get_row
						$map_data = $this->get_data("SELECT `wc_variation_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `quickbook_product_id` = '{$qbo_product_id}' AND `wc_variation_id` > 0 ");
						$is_variation = true;
					}
					
					if(empty($map_data)){
						if($manual){
							//Log Entry
						}
						return false;
					}
					
					//Multiple Support
					if(is_array($map_data)){
						foreach($map_data as $map_data_c){
							$wc_product_id = 0;
					
							if($is_variation){
								$wc_variation_id = $map_data_c['wc_variation_id'];
								$wc_product_id = $wc_variation_id;
							}else{
								$wc_product_id = $map_data_c['wc_product_id'];
							}				
							
							$product_meta = get_post_meta($wc_product_id);
							if(!$product_meta){
								if($manual){
									//Log Entry
								}
								return false;
							}
							
							$P_Name = $this->get_field_by_val($wpdb->posts,'post_title','ID',(int) $wc_product_id);
							$P_Sku = get_post_meta($wc_product_id,'_sku',true);
							$ext_log = "\n".'Name: '.$P_Name;
							$ext_log .= "\n".'SKU: '.$P_Sku;
							
							$_enable_role_based_price = (isset($product_meta['_enable_role_based_price'][0]))?$product_meta['_enable_role_based_price'][0]:0;
							if(!$_enable_role_based_price){
								return false;
							}
							
							$_role_based_price = (isset($product_meta['_role_based_price'][0]))?$product_meta['_role_based_price'][0]:'';
							if(!empty($_role_based_price)){
								$_role_based_price_a = @unserialize($_role_based_price);
								if(is_array($qbpricelevel_wrpl_mv) && !empty($qbpricelevel_wrpl_mv)){
									$_role_based_price_a = (is_array($_role_based_price_a))?$_role_based_price_a:array();
									foreach($qbpricelevel_wrpl_mv as $wrole => $qpl){
										if($qpl == $PriceLevelListID){
											$is_pl_p_added = false;
											/**/
											$rbp_pf = 'regular_price';
											if($this->get_option('mw_wc_qbo_desk_woorbp_qbpricelevel_psf') == 'selling_price'){
												$rbp_pf = 'selling_price';
											}
											
											if(!empty($_role_based_price_a) && isset($_role_based_price_a[$wrole][$rbp_pf])){
												$_price = $_role_based_price_a[$wrole][$rbp_pf];
												if($Price!=$_price){
													$_role_based_price_a[$wrole][$rbp_pf] = $Price;
													$is_pl_p_added = true;
												}												
											}else{
												$_role_based_price_a[$wrole] = array($rbp_pf => $Price);
												$is_pl_p_added = true;
											}
											//$this->_p($_role_based_price_a);return;
											$log_title = $ext_log_txt.'Import PriceLevel Product Price #'.$qbo_product_id;
											$wrole_f = ucfirst(str_replace('_',' ',$wrole));
											if($is_pl_p_added){
												update_post_meta($wc_product_id,'_role_based_price',$_role_based_price_a);
												$log_details = "WooCommerce Product #{$wc_product_id} {$wrole_f} role price updated from {$_price} to {$Price} ".$ext_log;
												$this->save_log(array('log_type'=>'Product Price','log_title'=>$log_title,'details'=>$log_details,'status'=>1),true);
											}else{
												if($manual){
													$log_details = "Prices ({$wrole_f}) on both ends are same (".$Price.").".$ext_log;
													$this->save_log(array('log_type'=>'Product Price','log_title'=>$log_title,'details'=>$log_details,'status'=>2),true);
												}												
											}
										}
									}
								}
							}					
							
						}
					}				
					
				}
			}
		}
	}
	
	public function UpdateWooCommerceProductPrice($product_data){
		if(!$this->is_qwc_connected()){
			return false;
		}
		global $wpdb;
		$qbo_product_id = $this->get_array_isset($product_data,'qbo_product_id','');
		$manual = $this->get_array_isset($product_data,'manual',false);

		$Price = $this->get_array_isset($product_data,'Price',0);
		$Name = $this->get_array_isset($product_data,'Name','');
		
		if(empty($Price)){
			return false;
		}
		
		$ext_log_txt = '';
		
		if($qbo_product_id!=''){
			if($this->is_valid_qbd_product($qbo_product_id)){
				//$ext_log = "\n".'Name: '.$Name;
				
				//get_row
				$map_data = $this->get_data("SELECT `wc_product_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `quickbook_product_id` = '{$qbo_product_id}' AND `wc_product_id` > 0 ");
				
				$is_variation = false;
				if(empty($map_data)){
					//get_row
					$map_data = $this->get_data("SELECT `wc_variation_id` FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `quickbook_product_id` = '{$qbo_product_id}' AND `wc_variation_id` > 0 ");
					$is_variation = true;
				}
				
				if(empty($map_data)){
					if($manual){
						//Log Entry
					}
					return false;
				}
				
				//Multiple Support
				if(is_array($map_data)){
					foreach($map_data as $map_data_c){
						$wc_product_id = 0;
				
						if($is_variation){
							$wc_variation_id = $map_data_c['wc_variation_id'];
							$wc_product_id = $wc_variation_id;
						}else{
							$wc_product_id = $map_data_c['wc_product_id'];
						}				
						
						$product_meta = get_post_meta($wc_product_id);
						if(!$product_meta){
							if($manual){
								//Log Entry
							}
							return false;
						}
						
						$P_Name = $this->get_field_by_val($wpdb->posts,'post_title','ID',(int) $wc_product_id);
						$P_Sku = get_post_meta($wc_product_id,'_sku',true);
						$ext_log = "\n".'Name: '.$P_Name;
						$ext_log .= "\n".'SKU: '.$P_Sku;
						
						/**/				
						if(isset($product_data['Wholesale_Price']) && $this->is_plugin_active('woocommerce-wholesale-prices','woocommerce-wholesale-prices.bootstrap')){
							if($this->option_checked('mw_wc_qbo_desk_compt_whl_price_pull')){
								$whl_p_wf = $this->get_option('mw_wc_qbo_desk_compt_whl_price_pull_wf');
								$whl_p_qf = $this->get_option('mw_wc_qbo_desk_compt_whl_price_pull_qf');
								// && isset($product_meta[$whl_p_wf][0])
								if(!empty($whl_p_wf) && !empty($whl_p_qf)){
									$woo_wholesale_price = (isset($product_meta[$whl_p_wf][0]))?$product_meta[$whl_p_wf][0]:0;
									$qbd_wholesale_price = $this->get_array_isset($product_data,'Wholesale_Price',0);
									if($qbd_wholesale_price!=$woo_wholesale_price){
										update_post_meta($wc_product_id, $whl_p_wf, $qbd_wholesale_price);
										
										//
										$woo_wholesale_price = number_format(floatval($woo_wholesale_price),2);
										
										$log_title_w = $ext_log_txt.'Import Product Wholesale Price #'.$qbo_product_id;
										$log_details_w = "WooCommerce Product #{$wc_product_id} wholesale price updated from {$woo_wholesale_price} to {$qbd_wholesale_price} ".$ext_log;
										
										$this->save_log(array('log_type'=>'Product Price','log_title'=>$log_title_w,'details'=>$log_details_w,'status'=>1),true);
										
									}
								}
							}
						}
						
						//
						$update_price_f = true;
						$_sale_price = (isset($product_meta['_sale_price'][0]))?$product_meta['_sale_price'][0]:0;
						$_sale_price = floatval($_sale_price);
						if($_sale_price > 0){
							$_price = (isset($product_meta['_regular_price'][0]))?$product_meta['_regular_price'][0]:0;
							$update_price_f = false;
						}else{
							$_price = (isset($product_meta['_price'][0]))?$product_meta['_price'][0]:0;							
						}
						
						if($Price!=$_price){
							$_price = number_format(floatval($_price),2);
							if($update_price_f){
								update_post_meta($wc_product_id, '_price', $Price);
							}
							
							//
							update_post_meta($wc_product_id, '_regular_price', $Price);
							
							
							$log_title = $ext_log_txt.'Import Product Price #'.$qbo_product_id;
							$log_details = "WooCommerce Product #{$wc_product_id} price updated from {$_price} to {$Price} ".$ext_log;
							
							$this->save_log(array('log_type'=>'Product Price','log_title'=>$log_title,'details'=>$log_details,'status'=>1),true);
							//
							/*if(!$manual){}*/
						}else{
							if($manual){
								$log_title = $ext_log_txt.'Import Product Price #'.$qbo_product_id;
								$log_details = "Prices on both ends are same (".$Price.").".$ext_log;
								$this->save_log(array('log_type'=>'Product Price','log_title'=>$log_title,'details'=>$log_details,'status'=>2),true);
							}					
						}
					}
				}				
				
			}
		}
		
	}
	
	public function get_order_base_currency_total_from_order_id($order_id){
		$o_tot = 0;
		$order_id = (int) $order_id;
		if($order_id>0){
			$od = $this->get_wc_order_details_from_order($order_id,get_post($order_id));
			if(is_array($od) && count($od)){
				if(isset($od['qbo_inv_items']) && is_array($od['qbo_inv_items']) && count($od['qbo_inv_items'])){
					foreach($od['qbo_inv_items'] as $oi){
						$o_tot+= $oi['line_subtotal_base_currency'];
						//$o_tot+= $oi['line_total_base_currency'];						
					}
				}
				
				if(isset($od['tax_details']) && is_array($od['tax_details']) && count($od['tax_details'])){
					foreach($od['tax_details'] as $oi){
						$o_tot+= $oi['tax_amount_base_currency'];
						$o_tot+= $oi['shipping_tax_amount_base_currency'];
					}
				}
				
				if(isset($od['used_coupons']) && is_array($od['used_coupons']) && count($od['used_coupons'])){
					foreach($od['used_coupons'] as $uc){
						if(isset($uc['discount_amount_base_currency'])){
							$o_tot-= $uc['discount_amount_base_currency'];
						}
						
						if(isset($uc['[discount_amount_tax_base_currency'])){
							$o_tot-= $uc['[discount_amount_tax_base_currency'];
						}
					}
				}
				
				$o_tot+= $od['_order_shipping_base_currency'];
				
				//$o_tot-= $od['_order_shipping_tax_base_currency'];
				
				//$o_tot-= $od['_cart_discount_base_currency'];
				
				$o_tot = $this->qbd_limit_decimal_points($o_tot);
			}
		}
		return $o_tot;
	}
	
	public function is_import_vendor(){
		return true; //
		if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync')){
			return true;
		}
		
		if($this->is_plugin_active('myworks-quickbooks-desktop-compt-cpfm-po-cjh') && $this->check_sh_cpfmpocjh_cuscompt_hash()){
			return true;
		}
		
		return false;
	}
	
	public function is_wc_ord_has_qbd_group_item($order_id){
		//return true;
		$order_id = (int) $order_id;
		if($order_id>0){
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			if(is_array($invoice_data) && count($invoice_data)){
				$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
				if(is_array($qbo_inv_items) && count($qbo_inv_items)){
					foreach($qbo_inv_items as $qbo_item){
						if($qbo_item['qbo_product_type'] == 'Group'){
							return true;
						}
					}
				}
			}
		}		
		return false;
	}
	
	//
	public function get_wc_fee_plugin_check(){
		//Forced Enabled
		return true;
		
		$enabled = false;
		
		if($this->is_plugin_active('woocommerce-gateways-discounts-and-fees') && $this->option_checked('mw_wc_qbo_desk_compt_gf_qbo_is')){
			$enabled = true;
		}

		if($this->is_plugin_active('woocommerce-additional-fees','woocommerce_additional_fees_plugin') && $this->option_checked('mw_wc_qbo_desk_compt_gf_qbo_is_gbf')){
			$enabled = true;
		}

		if($this->is_plugin_active('woocommerce-custom-fields') && $this->option_checked('mw_wc_qbo_desk_compt_wccf_fee')){
			$enabled = true;
		}
		
		if($this->is_plugin_active('woocommerce-checkout-field-editor-pro') && $this->option_checked('mw_wc_qbo_desk_wcfep_add_fld')){
			$enabled = true;
		}

		return $enabled;
	}
	
	public function get_wc_fee_qbo_product($dfn='',$efd='',$invoice_data=array()){
		//Forced Default
		//return $this->get_option('mw_wc_qbo_desk_default_qbo_item');
		return $this->get_option('mw_wc_qbo_desk_default_shipping_product');
		
		$fee_qp = '';
		$isdf = false;
		if($this->is_plugin_active('woocommerce-gateways-discounts-and-fees') && $this->option_checked('mw_wc_qbo_desk_compt_gf_qbo_is')){
			$isdf = true;
		}

		if($this->is_plugin_active('woocommerce-additional-fees','woocommerce_additional_fees_plugin') && $this->option_checked('mw_wc_qbo_desk_compt_gf_qbo_is_gbf')){
			$isdf = true;
		}
		if($isdf){
			$fee_qp = $this->get_option('mw_wc_qbo_desk_compt_gf_qbo_item');
		}
		if($this->is_plugin_active('woocommerce-custom-fields') && $this->option_checked('mw_wc_qbo_desk_compt_wccf_fee')){
			$fee_qp = '';
			if($dfn!=''){
				$ccf_data = $this->get_compt_checkout_fields($dfn);
				if(is_array($ccf_data) && count($ccf_data)){
					$ccf_id = $ccf_data['ID'];
					if($ccf_id){
						$mw_wc_qbo_sync_compt_wccf_fee_wf_qi_map = $this->get_option('mw_wc_qbo_desk_compt_wccf_fee_wf_qi_map');
						if($mw_wc_qbo_sync_compt_wccf_fee_wf_qi_map!=''){
							$ccf_map_arr = unserialize($mw_wc_qbo_sync_compt_wccf_fee_wf_qi_map);
							if(is_array($ccf_map_arr) && count($ccf_map_arr)){
								if(isset($ccf_map_arr[$ccf_id]) && $ccf_map_arr[$ccf_id]){
									$fee_qp = $ccf_map_arr[$ccf_id];
								}
							}
						}
					}
				}
			}

		}
		
		if($this->is_plugin_active('woocommerce-checkout-field-editor-pro') && $this->option_checked('mw_wc_qbo_desk_wcfep_add_fld')){
			$fee_qp = '';
			if($dfn!=''){
				$thwcfe_sections = get_option('thwcfe_sections');
				if(is_array($thwcfe_sections) && count($thwcfe_sections) && isset($thwcfe_sections['additional']) && count($thwcfe_sections['additional']) && isset($thwcfe_sections['additional']->fields) && count($thwcfe_sections['additional']->fields)){
					$thwcfe_sections_add = $thwcfe_sections['additional']->fields;
					$mw_wc_qbo_sync_compt_wcfep_price_wf_qi_map = $this->get_option('mw_wc_qbo_desk_compt_wcfep_price_wf_qi_map');
					if($mw_wc_qbo_sync_compt_wcfep_price_wf_qi_map!=''){
						$wcfep_map_arr = unserialize($mw_wc_qbo_sync_compt_wcfep_price_wf_qi_map);
						if(is_array($wcfep_map_arr) && count($wcfep_map_arr)){
							$wcfep_add_f_name = '';
							foreach($thwcfe_sections_add as $thwcfe_add){
								if($thwcfe_add->price_field==1){
									if($this->start_with($dfn,$thwcfe_add->title)){
										if(is_array($invoice_data) && isset($invoice_data[$thwcfe_add->id])){
											$wcfep_add_f_name = $thwcfe_add->id;
											break;
										}
									}
								}
							}

							if($wcfep_add_f_name!=''){
								if(isset($wcfep_map_arr[$wcfep_add_f_name]) && $wcfep_map_arr[$wcfep_add_f_name]){
									$fee_qp = $wcfep_map_arr[$wcfep_add_f_name];
								}
							}
						}
					}
				}
			}
		}


		if(!$fee_qp){
			$fee_qp = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
		}
		return $fee_qp;
	}
	
	public function get_n_cam_wf_list(){
		return array(
			'user_email' => 'Email',
			'display_name' => 'Display Name',
			'first_name_last_name' => 'First Name + Last Name',
			//'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'billing_company' => 'Company Name',
		);
	}
	
	public function get_n_cam_qf_list(){
		return array(
			'email' => 'Email',
			'd_name' => 'Display Name',
			'first_last' => 'First Name + Last Name',
			//'first' => 'First Name',
			'last' => 'Last Name',
			'company' => 'Company Name',
		);
	}
	
	public function get_n_pam_wf_list(){
		$f_arr = array(
			'name' => 'Name',
			'sku' => 'SKU',
		);
		
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$f_arr['ID'] = 'ID';
		}
		
		return $f_arr;
	}
	
	public function get_n_pam_qf_list(){
		$f_arr = array(
			'name' => 'Full Name',
			'sku' => 'Item Name / Number',
			'SalesDesc' => 'Item Description',
		);
		
		if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
			$f_arr['mpn'] = 'Manufacturers Part Number';
			$f_arr['barcode'] = 'Barcode Number';
		}
		
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$f_arr['Woo-Product-ID'] = 'Woo-Product-ID (Custom Field)';
		}
		
		return $f_arr;
	}
	
	public function get_n_vam_wf_list(){
		$f_arr = array(
			'name' => 'Name',
			'sku' => 'SKU',
		);
		
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$f_arr['ID'] = 'ID';
			$f_arr['Parent_Product_ID'] = 'Parent Product ID';
		}
		
		return $f_arr;
	}
	
	public function get_n_vam_qf_list(){
		$f_arr = array(
			'name' => 'Full Name',
			'sku' => 'Item Name / Number',
			'SalesDesc' => 'Item Description',
		);
		
		if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
			$f_arr['mpn'] = 'Manufacturers Part Number';
			$f_arr['barcode'] = 'Barcode Number';
		}
		
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-product-automap')){
			$f_arr['Woo-Product-ID'] = 'Woo-Product-ID (Custom Field)';
		}
		
		return $f_arr;
	}
	
	public function get_string_between($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
	
	public function get_string_after($string, $start){
		$arr = explode($start, $string);
		if(is_array($arr) && isset($arr[1])){
			return $arr[1];
		}
		return '';
	}
	
	public function if_sync_os_payment($invoice_data){
		/**/
		if(is_array($invoice_data) && isset($invoice_data['mw_qbo_yithwgcp']) && isset($invoice_data['mw_qbo_yithwgcp'])){
			return true;
		}
		
		$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);
		$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
		$order_status = $this->get_array_isset($invoice_data,'order_status','',true);

		$payment_method_map_data  = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
		$ps_order_status = $this->get_array_isset($payment_method_map_data,'ps_order_status','',true);
		if($order_status!='' && $order_status==$ps_order_status){
			return true;
		}
		return false;
	}
	
	public function if_sync_refund($refund_data){
		if($this->is_qwc_connected()){
			$_payment_method = $this->get_array_isset($refund_data,'_payment_method','',true);
			$_order_currency = $this->get_array_isset($refund_data,'_order_currency','',true);
			$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);			
			$enable_refund = (int) $this->get_array_isset($pm_map_data,'enable_refund',0);
			if($enable_refund){
				return true;
			}
		}
		return false;
	}
	
	public function if_queue_exists($action,$id=0,$status='q',$return_arr=false){
		$status = 'q';
		$action = $this->sanitize($action);
		if(!empty($action)){
			$id = intval($id);
			$qc_whr = '';
			if($id>0){
				$qc_whr = " AND `ident` = '{$id}' ";
			}
			
			/*
			if(is_admin()){
				$qs_whr = " AND `qb_status` = '{$status}' ";
			}else{
				$qs_whr = " AND (`qb_status` = '{$status}' OR (`qb_status` = 'i' AND `dequeue_datetime` IS NOT NULL)) ";
			}
			*/
			
			$qs_whr = " AND `qb_status` = '{$status}' ";
			
			global $wpdb;
			$qq = "SELECT * FROM `quickbooks_queue` WHERE `qb_action` = '{$action}' {$qc_whr} {$qs_whr} ORDER BY `quickbooks_queue_id` DESC LIMIT 0,1 ";
			$qd = $this->get_row($qq);			
			if(is_array($qd) && !empty($qd)){
				if($return_arr){
					return $qd;
				}
				return true;
			}
		}
		return false;
	}
	
	public function check_cg_ibn($customer_data){		
		if(is_array($customer_data) && count($customer_data)){
			$name_replace_chars = array(':','\t','\n');
			global $wpdb;
			$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
			if(!empty($display_name)){
				global $wpdb;
				$qq = "SELECT `ident` , `extra` , `qb_action` FROM `quickbooks_queue` WHERE (`qb_action` = 'GuestImport_ByName' OR `qb_action` = 'CustomerImport_ByName') AND `qb_status` = 'q' ORDER BY `quickbooks_queue_id` DESC";
				$q_list = $this->get_data($qq);
				//$this->_p($q_list);
				if(is_array($q_list) && !empty($q_list)){
					foreach($q_list as $ql){
						$ident = (int) $ql['ident'];
						if($ident >0){
							$is_guest_q = (isset($extra['qb_action']) && $extra['qb_action'] == 'GuestImport_ByName')?true:false;
							
							if($is_guest_q){
								$customer_data_q = $this->get_wc_customer_info_from_order($ident);
							}else{
								$customer_data_q = $this->get_wc_customer_info($ident);
							}
							
							if(is_array($customer_data_q) && count($customer_data_q)){
								$display_name_q = $this->get_array_isset($customer_data_q,'display_name','',true,100,false,$name_replace_chars);
								if($display_name == $display_name_q){									
									return true;
								}
							}
						}
					}
				}
			}
		}
		return false;
	}
	
	public function remove_add_guest_customer_queue_after_ibn_success($ID,$is_guest=false){
		global $wpdb;
		$qq = "SELECT `ident` , `extra` , `qb_action` FROM `quickbooks_queue` WHERE (`qb_action` = 'GuestAdd' OR `qb_action` = 'CustomerAdd') AND `qb_status` = 'q' ORDER BY `quickbooks_queue_id` DESC";
		
		$q_list = $this->get_data($qq);
		//$this->_p($q_list);
		if(is_array($q_list) && !empty($q_list)){
			$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
			foreach($q_list as $ql){
				$ident = (int) $ql['ident'];
				if($ident >0){
					$is_guest_q = (isset($ql['qb_action']) && $ql['qb_action'] == 'GuestAdd')?true:false;
					if($is_guest_q){
						$customer_data = $this->get_wc_customer_info_from_order($ident);
						if($this->if_qbo_guest_exists($customer_data)){
							$Queue->remove('GuestAdd',$ident);
						}
					}else{
						$customer_data = $this->get_wc_customer_info($ident);
						if($this->if_qbo_customer_exists($customer_data)){
							$Queue->remove('CustomerAdd',$ident);
						}
					}
				}
			}
		}
	}
	
	public function Fix_All_WooCommerce_Variations_Names(){
		/*Disabled*/
		return 0;
		
		global $wpdb;
		$sql = "
			SELECT p.ID, p.post_title AS name, p.post_parent as parent_id, p1.post_title AS parent_name
			FROM ".$wpdb->posts." p
			LEFT JOIN " . $wpdb->posts . " p1 ON p.post_parent = p1.ID			
			WHERE p.post_type =  'product_variation'
			AND p1.post_title != ''
			AND (p.post_title = '' OR p.post_title = p1.post_title)
			AND p.post_status NOT IN('trash','auto-draft','inherit')
			ORDER BY p.ID ASC
		";
		//echo $sql;
		$v_list = $this->get_data($sql);
		//$this->_p($v_list);
		$total_v_name_changed = 0;
		if(is_array($v_list) && !empty($v_list)){
			foreach($v_list as $vl_d){
				$v_name_suffix = '';
				$ID = intval($vl_d['ID']);
				$p_id = (int) $vl_d['parent_id'];
				$_product_attributes_a = get_post_meta($p_id,'_product_attributes',true);
				//$this->_p($_product_attributes_a);
				if(is_array($_product_attributes_a) && count($_product_attributes_a)){
					$vm_sql = "
						SELECT `meta_key` , `meta_value` FROM {$wpdb->postmeta} 
						WHERE `post_id` = {$ID}
						AND meta_key LIKE 'attribute_%%'
					";
					$vm_list = $this->get_data($vm_sql);
					//$this->_p($vm_list);
					if(is_array($vm_list) && count($vm_list)){
						foreach($vm_list as $vmk => $vmv){
							if (substr($vmv['meta_key'], 0, strlen('attribute_')) == 'attribute_') {
								$att_key = substr($vmv['meta_key'], strlen('attribute_'));
								$att_val = $vmv['meta_value'];
								if(!empty($att_key) && isset($_product_attributes_a[$att_key])){
									if($vmk == 0){
										$v_name_suffix.= ' - '.$att_val;
									}else{
										$v_name_suffix.= ', '.$att_val;
									}
								}
							}							
						}
					}
				}
				
				//$this->_p($v_name_suffix);
				if(!empty($v_name_suffix)){
					$new_variation_name = $vl_d['parent_name'] . $v_name_suffix;
					//$this->_p($new_variation_name);
					//wp_update_post
					$vnu_sql = $wpdb->prepare("UPDATE {$wpdb->posts} SET `post_title` = %s WHERE `ID` = %d AND `post_type` = 'product_variation' ",$new_variation_name,$ID);
					//echo $vnu_sql;
					$wpdb->query($vnu_sql);
					$total_v_name_changed++;
				}
			}
		}
		
		if($total_v_name_changed>0){
			$this->save_log(array('log_type'=>'Variation_NU','log_title'=>'Variations Name Update','details'=>'Number of variations name updated: '.$total_v_name_changed,'status'=>1),true);
		}
		
		return $total_v_name_changed;
	}
	
	public function get_variation_name_from_id($v_name,$p_name='',$v_id,$p_id=0){
		$v_name = trim($v_name);$p_name = trim($p_name);
		/*New*/
		//return $v_name;
		
		$v_id = intval($v_id);$p_id = intval($p_id);
		if($v_name!='' && $v_id>0){
			global $wpdb;
			if(!$p_id || empty($p_name)){
				$p_data = $this->get_row($wpdb->prepare("SELECT * FROM `{$wpdb->posts}` WHERE  `ID` = %d AND `post_type` = 'product_variation'  ",$v_id));
				if(is_array($p_data) && count($p_data)){
					$p_id = (int) $p_data['post_parent'];
					//$p_name = $p_data['post_title'];
					$p_name = $this->get_field_by_val($wpdb->posts,'post_title','ID',$p_id);
				}
			}
			
			if($p_id>0 && !empty($p_name)){
				$_product_attributes_a = get_post_meta($p_id,'_product_attributes',true);
				if(is_array($_product_attributes_a) && count($_product_attributes_a)){
					$pa_k_a = array();
					foreach($_product_attributes_a as $pak => $pav){
						$pa_k_a[] = $pak;
					}
					
					$v_meta = get_post_meta($v_id);					
					
					if(is_array($v_meta) && count($v_meta)){
						$v_av_pa = array();
						foreach($v_meta as $vmk => $vmv){								
							if (substr($vmk, 0, strlen('attribute_')) == 'attribute_') {
								$vmk = substr($vmk, strlen('attribute_'));
								if(in_array($vmk,$pa_k_a)){
									$vmv = ($vmv[0])?$vmv[0]:'';
									if(!is_numeric($vmv)){
										$vmv = ucfirst($vmv);
									}
									$p_name.=' - '.$vmv;
									/*
									if($this->start_with($vmk,'pa_')){
										$vmk = $this->sanitize(substr($vmk,3));
									}
									$v_av_pa[$vmk] = $vmv;
									*/
								}
							}								
						}
					}
					
					return $p_name;
				}
			}
		}
		return $v_name;
	}
	
	/**/
	public function wc_get_wst_data($_wc_shipment_tracking_items){
		$wsti_data = array();
		if($_wc_shipment_tracking_items!=''){
			$_wc_shipment_tracking_items = unserialize($_wc_shipment_tracking_items);
			if(is_array($_wc_shipment_tracking_items) && count($_wc_shipment_tracking_items)){
				$wsti = $_wc_shipment_tracking_items[0];
				$tracking_provider = ($wsti['tracking_provider']!='')?$wsti['tracking_provider']:$wsti['custom_tracking_provider'];
				$tracking_number = $wsti['tracking_number'];
				$date_shipped = $wsti['date_shipped'];
				if($date_shipped!=''){
					$date_shipped = date('Y-m-d',$date_shipped);
				}
				$wsti_data['tracking_provider'] = $tracking_provider;
				$wsti_data['tracking_number'] = $tracking_number;
				$wsti_data['date_shipped'] = $date_shipped;
			}
		}
		return $wsti_data;
	}
	
	public function wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result){
		$wsti_data = array();
		if($wf_wc_shipment_source!='' && $wf_wc_shipment_result!=''){
			$wf_wc_shipment_source = @unserialize($wf_wc_shipment_source);
			$wf_wc_shipment_result = @unserialize($wf_wc_shipment_result);
			
			if(is_array($wf_wc_shipment_source) && count($wf_wc_shipment_source)){
				$tracking_number = $wf_wc_shipment_source['shipment_id_cs'];
				
				if(is_array($wf_wc_shipment_result) && count($wf_wc_shipment_result) && isset($wf_wc_shipment_result['tracking_info']) && is_array($wf_wc_shipment_result['tracking_info']) && count($wf_wc_shipment_result['tracking_info'])){
					$wsti = $wf_wc_shipment_result['tracking_info'][0];
					$tracking_number = $wsti['tracking_id'];
					
				}
				
				$tracking_provider = $wf_wc_shipment_source['shipping_service'];
				$date_shipped = $wf_wc_shipment_source['order_date'];					
				if($date_shipped!=''){
					//$date_shipped = date('Y-m-d',$date_shipped);
				}
				
				$wsti_data['tracking_provider'] = $tracking_provider;
				$wsti_data['tracking_number'] = $tracking_number;
				$wsti_data['date_shipped'] = $date_shipped;
				
			}
		}
		return $wsti_data;
	}
	
	public function check_cf_map_data_ext_field_value_exists($order_id,$ost='Invoice'){
		$cf_map_data = array();
		if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash() && $order_id > 0){
			$cf_map_data = $this->get_cf_map_data();
		}
		
		if(is_array($cf_map_data) && !empty($cf_map_data)){
			$qacfm = $this->get_qbo_avl_cf_map_fields();
			if(is_array($qacfm) && !empty($qacfm)){
				$invoice_data = $this->get_wc_order_details_from_order($order_id,get_post($order_id));
				if(is_array($invoice_data) && !empty($invoice_data)){
					foreach($cf_map_data as $wcfm_k => $wcfm_v){
						$wcfm_k = trim($wcfm_k);
						$wcfm_v = trim($wcfm_v);
						
						if(!empty($wcfm_v) && !isset($qacfm[$wcfm_v])){
							$wcf_val = '';
							switch ($wcfm_k) {									
								case "wc_order_shipping_method_name":
									/**/									
									$shipping_method_name = '';									
									$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
									if(isset($shipping_details[0])){
										if($this->get_array_isset($shipping_details[0],'type','')=='shipping'){
											$shipping_method_name =  $this->get_array_isset($shipping_details[0],'name','',true,30);
										}
									}
									
									$wcf_val = $shipping_method_name;
									break;
								case "wc_order_phone_number":
									$wcf_val = $this->get_array_isset($invoice_data,'_billing_phone','',true);
									break;
								default:
									if(isset($invoice_data[$wcfm_k])){
										//is_string
										if(!is_array($invoice_data[$wcfm_k]) && !is_object($invoice_data[$wcfm_k])){
											$wcf_val = $this->get_array_isset($invoice_data,$wcfm_k,'',true);
										}										
									}
							}
							
							if(!empty($wcf_val)){
								return true;
							}
						}
					}
				}
			}			
		}
		
		return false;
	}
	
	public function is_inv_site_bin_allowed(){
		global $wpdb;
		if(!empty($wpdb->get_var("SELECT `qbd_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite WHERE parent_id !='' LIMIT 0,1"))){
			return true;
		}
		return false;
	}
	
	public function get_inventory_site_bin_dd_options($r_arr=false){
		global $wpdb;
		$options = '';
		$opt_arr = array();
		
		if(!$this->is_inv_site_bin_allowed()){
			return $options;
		}
		
		$inv_site_arr = $this->get_data("SELECT `qbd_id` , `name` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite WHERE `parent_id` = '' ORDER BY `name` ASC ");
		
		if(is_array($inv_site_arr) && !empty($inv_site_arr)){
			foreach($inv_site_arr as $k => $v){				
				$inv_site_id = $v['qbd_id'];
				$inv_site_id = $this->sanitize($inv_site_id);				
				$inv_site_name = $v['name'];
				if(!empty($inv_site_id)){
					$bin_arr = $this->get_data("SELECT `qbd_id` , `name` , `parent_id` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite WHERE `parent_id` = '{$inv_site_id}' ORDER BY `name` ASC ");
					if(is_array($bin_arr) && !empty($bin_arr)){
						foreach($bin_arr as $bk => $bv){
							if($inv_site_id == $bv['parent_id']){
								$bin_id = $bv['qbd_id'];
								$bin_id = $this->sanitize($bin_id);
								
								$op_k = $inv_site_id.':'.$bin_id;
								$op_v = $inv_site_name.':'.$bv['name'];
								
								$options .= '<option value="'.$op_k.'">'.$op_v.'</option>';
								if($r_arr){
									$opt_arr[$op_k] = $op_v;
								}
							}
						}
					}
				}
			}
		}
		
		if($r_arr){
			return $opt_arr;
		}
		
		return $options;
	}
	
	protected function is_invnt_imp_q_mapped_list_id_filter(){
		return true;
	}
	
	protected function get_mapped_qbd_invnt_and_assmbly_items($ityp=''){
		global $wpdb;
		if($ityp == 'InventoryAssembly'){
			$q = "
			SELECT quickbook_product_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs` pp INNER JOIN {$wpdb->prefix}mw_wc_qbo_desk_qbd_items qi ON pp.quickbook_product_id = qi.qbd_id AND qi.product_type = 'InventoryAssembly'
			";
			
			$qv = "
			SELECT quickbook_product_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_variation_pairs` pp INNER JOIN {$wpdb->prefix}mw_wc_qbo_desk_qbd_items qi ON pp.quickbook_product_id = qi.qbd_id AND qi.product_type = 'InventoryAssembly'
			";
		}else{
			$q = "
			SELECT quickbook_product_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs` pp INNER JOIN {$wpdb->prefix}mw_wc_qbo_desk_qbd_items qi ON pp.quickbook_product_id = qi.qbd_id AND (qi.product_type = 'Inventory' OR qi.product_type = 'InventoryAssembly')
			";
			
			$qv = "
			SELECT quickbook_product_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_variation_pairs` pp INNER JOIN {$wpdb->prefix}mw_wc_qbo_desk_qbd_items qi ON pp.quickbook_product_id = qi.qbd_id AND (qi.product_type = 'Inventory' OR qi.product_type = 'InventoryAssembly')
			";
		}
		
		//return $this->get_data($q);
		
		$pma = (array) $this->get_data($q);
		$vma = (array) $this->get_data($qv);
		
		return array_merge($pma, $vma);
	}
	
	protected function get_mwr_oiw_mw_idls($qbo_item, $invoice_data){
		$mw_warehouse = 0;
		if(is_array($qbo_item) && !empty($qbo_item) && is_array($invoice_data) && !empty($invoice_data)){
			if(isset($qbo_item["_order_item_wh"]) && !empty($qbo_item["_order_item_wh"])){				
				if (!is_numeric($qbo_item["_order_item_wh"])) {
					$oiw_a = @unserialize($qbo_item["_order_item_wh"]);
					if(is_array($oiw_a) && !empty($oiw_a)){
						foreach($oiw_a as $oiw_a_k => $oiw_a_v){
							$mw_warehouse = (int) $oiw_a_k;
							break;
						}
					}
				}else{
					$mw_warehouse = (int) $qbo_item["_order_item_wh"];
				}
			}else{
				$mw_warehouse = (int) $this->get_array_isset($invoice_data,'mw_warehouse',0);
			}
		}
		
		return $mw_warehouse;
	}
	
	/**/
	public function get_osl_sm_val($prm=array()){
		$stk = base64_decode('T3JkZXJBZGQ=');
		$sl_okv = base64_decode('bXdfd2NfcWJvX2Rlc2tfaW1wX29zbGNkX2RjYQ==');
		$oslcd = get_option($sl_okv);
		
		$cy = $this->now('Y');
		$cm = $this->now('F');
		
		if(is_array($oslcd) && !empty($oslcd)){
			if(isset($oslcd[$cy]) && is_array($oslcd)){
				if(isset($oslcd[$cy][$cm]) && is_array($oslcd[$cy][$cm])){
					if(isset($oslcd[$cy][$cm][$stk]) && (int) $oslcd[$cy][$cm][$stk] > 0){
						$e_scv = (int) $oslcd[$cy][$cm][$stk];
						return $e_scv;
					}
				}
			}
		}
		return 0;
	}
	
	public function get_osl_lp_count($prm=array()){
		$osl_pm_v = 20;
		if($this->is_plg_lc_p_g()){
			$osl_pm_v = 1000;
		}
		return $osl_pm_v;
	}
	
	public function lp_chk_osl_allwd($prm=array()){
		if($this->is_plg_lc_p_l() || $this->is_plg_lc_p_g()){
			$e_scv = (int) $this->get_osl_sm_val();
			$osl_pm_v = $this->get_osl_lp_count();							
			if($e_scv >= $osl_pm_v){
				return false;
			}
		}
		return true;
	}
	
	protected function set_imp_sync_data($prm=array()){
		/*
		if(is_array($prm) && isset($prm['stk']) && !empty($prm['stk'])){
			$stk = base64_decode($prm['stk']);
		}else{
			$stk = base64_decode('T3JkZXJBZGQ=');
		}
		*/
		
		$E_ID = 0;
		if(is_array($prm) && isset($prm['ID']) && (int) $prm['ID'] > 0){
			$E_ID = (int) $prm['ID'];
		}
		
		$stk = base64_decode('T3JkZXJBZGQ=');
		
		$sl_okv = base64_decode('bXdfd2NfcWJvX2Rlc2tfaW1wX29zbGNkX2RjYQ==');
		$oslcd = get_option($sl_okv);
		
		$cy = $this->now('Y');
		$cm = $this->now('F');		
		//
		
		if(is_array($oslcd) && !empty($oslcd)){
			$e_scv_inc = false;
			if(isset($oslcd[$cy]) && is_array($oslcd)){
				if(isset($oslcd[$cy][$cm]) && is_array($oslcd[$cy][$cm])){
					if(isset($oslcd[$cy][$cm][$stk]) && (int) $oslcd[$cy][$cm][$stk] > 0){
						$ii_cv = true;						
						if($E_ID > 0 && isset($oslcd[$cy][$cm][$stk.'_IDs'])){
							$stk_id_arr = $oslcd[$cy][$cm][$stk.'_IDs'];
							if(is_array($stk_id_arr) && in_array($E_ID,$stk_id_arr)){
								$ii_cv = false;
							}else{
								$stk_id_arr[] = $E_ID;
								$oslcd[$cy][$cm][$stk.'_IDs'] = $stk_id_arr;
							}
						}
						
						if($ii_cv){
							$e_scv = (int) $oslcd[$cy][$cm][$stk];
							$e_scv++;
							$oslcd[$cy][$cm][$stk] = $e_scv;
						}
						
						$e_scv_inc = true;
					}
				}
			}
			
			if(!$e_scv_inc){
				$oslcd[$cy][$cm][$stk] = 1;				
			}
			
			if($E_ID > 0 && !isset($oslcd[$cy][$cm][$stk.'_IDs'])){
				$oslcd[$cy][$cm][$stk.'_IDs'] = array($E_ID);
			}
		}else{
			$oslcd = array();
			$oslcd[$cy] = array(
				$cm => array(
					$stk => 1,
				),
			);
			
			if($E_ID > 0 ){
				$oslcd[$cy][$cm][$stk.'_IDs'] = array($E_ID);
			}			
		}
		
		update_option($sl_okv,$oslcd);
	}
	
	/**/
	private function get_local_key_results(){
		$localkeyresults = array();
		$localkey = $this->get_option('mw_wc_qbo_desk_localkey');
		if(!empty($localkey)){
			$localkey = str_replace("\n", '', $localkey);
			$localdata = substr($localkey, 0, strlen($localkey) - 32);
			$localdata = strrev($localdata);
			$localdata = substr($localdata, 32);
			$localdata = @base64_decode($localdata);
			$localkeyresults = @unserialize($localdata);
		}
		return $localkeyresults;
	}
	
	private function get_plg_lc_plan(){
		$pln = '';
		$lkr = $this->get_local_key_results();
		if(is_array($lkr) && count($lkr) && isset($lkr[base64_decode('cHJvZHVjdG5hbWU=')]) && !empty($lkr[base64_decode('cHJvZHVjdG5hbWU=')])){
			if(strpos($lkr[base64_decode('cHJvZHVjdG5hbWU=')],base64_decode('TGF1bmNo'))!==false){
				$pln = base64_decode('TGF1bmNo');
			}
			
			if(strpos($lkr[base64_decode('cHJvZHVjdG5hbWU=')],base64_decode('R3Jvdw=='))!==false){
				$pln = base64_decode('R3Jvdw==');
			}
			
			if(strpos($lkr[base64_decode('cHJvZHVjdG5hbWU=')],base64_decode('U2NhbGU='))!==false){
				$pln = base64_decode('U2NhbGU=');
			}
		}
		return $pln;
	}
	
	public function is_plg_lc_p_l(){
		//return false;
		if($this->get_plg_lc_plan() == base64_decode('TGF1bmNo')){
			return true;
		}
		return false;
	}
	
	public function is_plg_lc_p_g(){
		if($this->get_plg_lc_plan() == base64_decode('R3Jvdw==')){
			return true;
		}
		return false;
	}
	
	public function is_plg_lc_p_s(){
		if($this->get_plg_lc_plan() == base64_decode('U2NhbGU=')){
			return true;
		}
		return false;
	}
	
	public function get_hd_ldys_lmt(){
		/**/
		if($this->is_plg_lc_p_l()){
			return 7;
		}		
		
		if($this->option_checked('mw_wc_qbo_desk_trial_license')){
			return 7;
		}		
		
		return 30;
	}
	
	public function get_ups_sm_instance_list(){
		return array(
			'12' => '3 Day Select',
			'03' => 'Ground',
			'02' => '2nd Day Air',
			'59' => '2nd Day Air AM',
			'01' => 'Next Day Air',
			'13' => 'Next Day Air Saver',
			'14' => 'Next Day Air Early AM',
			'11' => 'Standard',
			'07' => 'Worldwide Express',
			'54' => 'Worldwide Express Plus',
			'08' => 'Worldwide Expedited',
			'65' => 'Saver',
			'92' => 'SurePost Less than 1 lb',
			'93' => 'SurePost 1 lb or Greater',
			'94' => 'SurePost BPM',
			'95' => 'SurePost Media',
		);
	}
	
	private function wcups_instance_f_arr(){
		$ups_il_arr = $this->get_ups_sm_instance_list();
		$ifa = array();
		if(is_array($ups_il_arr) && !empty($ups_il_arr)){
			foreach($ups_il_arr as $k => $v){
				$ifa[] = array(
					'title' => $v,
					'sm_id' => $k,
				);
			}
		}
		return $ifa;
	}
	
	public function get_custom_shipping_map_data_from_name($ship_name){
		if(!empty($ship_name)){
			$custom_shipping_map_arr = get_option('mw_wc_qbo_desk_ed_cust_ship_map_val_arr');
			if(is_array($custom_shipping_map_arr) && !empty($custom_shipping_map_arr)){
				foreach($custom_shipping_map_arr as $csma){
					if(isset($csma['wc_shipping_name']) && !empty($csma['wc_shipping_name'])){
						if(strtolower($csma['wc_shipping_name']) == strtolower($ship_name)){
							return $csma;
						}
					}
				}
			}
		}
		return array();
	}
	
	/**/
	protected function get_liqtycustcolumn_c_qty($Qty,$qbo_item){
		//return $Qty;
		$C_Qty = $Qty;
		if($Qty>0 && is_array($qbo_item) && !empty($qbo_item)){
			$product_id = (int) $qbo_item['product_id'];
			$variation_id = (isset($qbo_item['variation_id']))?(int) $qbo_item['variation_id']:0;
			$case_count = 0;
			if($product_id > 0){
				$case_count = (int) get_post_meta($product_id,'case_count',true);
			}
			
			if($case_count > 0){
				$C_Qty = $Qty / $case_count;
			}
		}
		return $C_Qty;
	}
	
	public function get_qbd_item_sales_tax_dd_options(){
		global $wpdb;
		$opt_s = '';
		$dd_q = "SELECT `qbd_id` , `name` , `info_arr` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_items WHERE `qbd_id` != '' AND (product_type='SalesTax' OR product_type='SalesTaxGroup') ORDER BY `name` ASC";
		$dd_data = $this->get_data($dd_q);
		if(is_array($dd_data) && !empty($dd_data)){
			foreach($dd_data as $v){
				$ot = $v['name'];
				if(!empty($v['info_arr'])){
					$info_arr = @unserialize($v['info_arr']);
					if(is_array($info_arr) && !empty($info_arr) && isset($info_arr['TaxRate']) && !empty($info_arr['TaxRate'])){
						$ot .= '('.$info_arr['TaxRate'].'%)';
					}
				}
				$opt_s .= '<option value="'.$v['qbd_id'].'">'.$ot.'</option>';
			}
		}
		return $opt_s;
	}
	
	public function get_woo_latest_order_id_by_customer_id($customer_id){
		/**/
	}
	
	public function get_woo_latest_order_id_by_customer_id_from_queue($customer_id){
		if($customer_id > 0){
			global $wpdb;
			$qq = "SELECT * FROM `quickbooks_queue` WHERE `qb_status` = 'q' AND `qb_action` IN('".QUICKBOOKS_ADD_INVOICE."','".QUICKBOOKS_ADD_SALESRECEIPT."','".QUICKBOOKS_ADD_SALESORDER."','".QUICKBOOKS_ADD_ESTIMATE."')  ORDER BY `quickbooks_queue_id` DESC LIMIT 0,1 ";
			$qd = $this->get_row($qq);
			if(is_array($qd) && !empty($qd)){
				$order_id = (int) $qd['ident'];
				$ord_customer_id = get_post_meta($order_id,'_customer_user',true);
				if($customer_id == $ord_customer_id){
					return $order_id;
				}
			}
		}
	}
	
	public function get_vendor_id_cpfmpocjh_cuscompt($invoice_data){
		return '80000006-1516216199';
		$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
		if($wc_inv_id > 0){
			//
		}
	}
	
	public function order_and_p_details_amounts_round($invoice_p_data){
		$c_invoice_p_data = array();
		if(is_array($invoice_p_data) && !empty($invoice_p_data)){
			$order_amount_fields_keys = array(
				'_cart_discount',
				'_cart_discount_tax',
				'_order_shipping',
				'_order_shipping_tax',
				'_order_tax',
				'_order_total',
				//'_stripe_fee',
				//'_stripe_net',
				'discount_amount',
				'discount_amount_tax',
				'cost',
				'total_tax',
				'tax_amount',
				'shipping_tax_amount',
				'order_shipping_total',
				'UnitPrice',
				'line_subtotal',
				'line_subtotal_tax',
				'line_total',
				'line_tax',
				'amount',
				'order_total',
			);
			
			$oafk_arr_keys = array(
				'used_coupons',
				'shipping_details',
				'tax_details',
				'qbo_inv_items',
				'dc_gt_fees',
				'pw_gift_card',
			);			
			
			foreach($invoice_p_data as $k => $v){
				if(!is_array($v) && in_array($k,$order_amount_fields_keys) && (float) $v > 0 ){
					$v = floatval($v);						
					//qbd_limit_decimal_points
					//$v = round($v,2);
				}
				
				if(is_array($v) && in_array($k,$oafk_arr_keys) && !empty($v)){
					foreach($v as $k_c => $v_c){
						if(is_array($v_c) && !empty($v_c)){
							foreach($v_c as $k_c_c => $v_c_v){
								if(in_array($k_c_c,$order_amount_fields_keys)){
									//
									if($k_c_c != 'UnitPrice' && $k_c_c != 'line_total'){
										continue;
									}
									$v[$k_c][$k_c_c] = round($v[$k_c][$k_c_c],2);									
								}
							}
						}
					}
				}
				
				$c_invoice_p_data[$k] = $v;
			}
		}
		
		if(empty($c_invoice_p_data)){
			return $invoice_p_data;
		}
		return $c_invoice_p_data;
	}
	
	public function custom_order_and_p_details_amounts_multiplication($invoice_p_data){
		$c_invoice_p_data = array();
		if(is_array($invoice_p_data) && !empty($invoice_p_data)){
			$order_amount_fields_keys = array(
				'_cart_discount',
				'_cart_discount_tax',
				'_order_shipping',
				'_order_shipping_tax',
				'_order_tax',
				'_order_total',
				//'_stripe_fee',
				//'_stripe_net',
				'discount_amount',
				'discount_amount_tax',
				'cost',
				'total_tax',
				'tax_amount',
				'shipping_tax_amount',
				'order_shipping_total',
				'UnitPrice',
				'line_subtotal',
				'line_subtotal_tax',
				'line_total',
				'line_tax',
				'amount',
				'order_total',
			);
			
			$oafk_arr_keys = array(
				'used_coupons',
				'shipping_details',
				'tax_details',
				'qbo_inv_items',
				'dc_gt_fees',
				'pw_gift_card',
			);
			
			/*
			if(isset($invoice_p_data['order_currency'])){
				$_order_currency = $this->get_array_isset($invoice_p_data,'order_currency','',true);
			}else{
				$_order_currency = $this->get_array_isset($invoice_p_data,'_order_currency','',true);
			}
			*/
			
			if(isset($invoice_p_data['payment_method'])){
				$_payment_method = $this->get_array_isset($invoice_p_data,'payment_method','',true);
			}else{
				$_payment_method = $this->get_array_isset($invoice_p_data,'_payment_method','',true);
			}
			
			//$_payment_method = 'cod';
			if($_payment_method == 'cod' || $_payment_method == 'cxpay_redirect'|| $_payment_method == 'cheque'){
				$d_by = 100;
				$m_by = 1.82;
				foreach($invoice_p_data as $k => $v){
					if(!is_array($v) && in_array($k,$order_amount_fields_keys) && (float) $v > 0 ){
						$v = floatval($v);						
						if($_payment_method == 'cod' || $_payment_method == 'cheque'){
							$v = $this->coapdam_nf($v/$d_by);//qbd_limit_decimal_points							
						}else{
							$v = $this->coapdam_nf($v*$m_by);
						}						
					}
					
					if(is_array($v) && in_array($k,$oafk_arr_keys) && !empty($v)){
						foreach($v as $k_c => $v_c){
							if(is_array($v_c) && !empty($v_c)){
								foreach($v_c as $k_c_c => $v_c_v){
									if(in_array($k_c_c,$order_amount_fields_keys)){
										if($_payment_method == 'cod' || $_payment_method == 'cheque'){											
											$v[$k_c][$k_c_c] = $this->coapdam_nf($v[$k_c][$k_c_c]/$d_by);
										}else{
											$v[$k_c][$k_c_c] = $this->coapdam_nf($v[$k_c][$k_c_c]*$m_by);
										}
									}
								}
							}
						}
					}
					
					$c_invoice_p_data[$k] = $v;
				}
			}
		}
		
		if(empty($c_invoice_p_data)){
			return $invoice_p_data;
		}
		return $c_invoice_p_data;
	}
	
	public function coapdam_nf($amount){
		return number_format((float)$amount, 5, ',', '.');
	}
	
	public function get_qbo_company_setting($setting=''){
		$p_arr = get_option('mw_wc_qbo_desk_qbd_preferences_arr');		
		if(!empty($setting) && is_array($p_arr) && !empty($p_arr) && isset($p_arr[$setting])){
			$rt = $p_arr[$setting];
			if($rt == 'false'){$rt = false;}			
			return $rt;
		}
		return '';
	}
	
	public function due_days_list_arr(){
		return array_combine(range(1,100), range(1,100));
	}
	
	/*End of Class*/
}

require_once plugin_dir_path( __FILE__ ) . 'class-mw-qbo-desktop-lib-ext.php';