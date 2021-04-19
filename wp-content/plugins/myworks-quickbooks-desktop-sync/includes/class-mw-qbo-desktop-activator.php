<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fired during plugin activation
 *
 * @link       http://myworks.design/
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
 * @author     MyWorks Design <sales@myworks.design>
 */
class MW_QBO_Desktop_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		/*
		if (!extension_loaded('mcrypt'))
		die(__('This plugin requires <a target="_blank" href="http://php.net/manual/en/book.mcrypt.php">PHP Mcrypt Extension loaded into your server</a> to be active!', 'mw_wc_qbo_desk'));
	    */
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	    // Set pointer notices
	    $admin_pointer_content = '<h3>' . __( 'QuickBooks Desktop' ) . '</h3>';
	    $admin_pointer_content .= '<p>' . __( 'Automatically sync your WooCommerce store to QuickBooks Desktop.' ) . '</p>';
	    update_option('mw_wc_qbo_desk_admin_pointers', $admin_pointer_content);
	    delete_option( 'mw_wc_qbo_desk_deactivation_popup' );
		
		/*DB Illegal character check*/
		if(MW_QBO_Desktop_Activator::check_invalid_chars_in_db_conn_info()){
			$error_message = __('There is an unsupported character in your wp-config.php file - in either the database host, name, user or password. Simply check for and remove these characters + / # % \ ? from these fields. Your web developer/web host can also assist with this.', 'mw_wc_qbo_desk');
			die($error_message);
		}
		
		if (MW_QBO_Desktop_Activator::check_if_woocommerce_active()) {
			activate_plugins( plugin_dir_path( __FILE__ ) . 'myworks-quickbooks-desktop-sync.php', admin_url('?page=mw-qbo-desktop-qwc-file'), true, false);
			$is_plugin_activate = true;
			$is_pos_plugin_active = false;
			if (class_exists( 'MyWorks_WC_QBO_Sync_QBO_Lib' ) && in_array('myworks-woo-sync-for-quickbooks-online/myworks-woo-sync-for-quickbooks-online.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))) {
				$is_plugin_activate = false;
			}
			if (class_exists( 'MW_QBO_Desktop_Sync_Qwc_Server_Lib' ) && in_array('myworks-quickbooks-pos-sync/myworks-quickbooks-pos-sync.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))) {
				$is_pos_plugin_active = true;
				$is_plugin_activate = false;
			}
			if (!$is_plugin_activate) {
				if ($is_pos_plugin_active) {
					$error_message = __('Plugin conflict - QuickBooks Online plugin is already activated', 'mw_wc_qbo_desk');
				} else {
					$error_message = __('Plugin conflict - QuickBooks POS plugin is already activated', 'mw_wc_qbo_desk');
				}
				die($error_message);
				
			}

			MW_QBO_Desktop_Activator::create_databases();
			MW_QBO_Desktop_Activator::do_after_activate();
			return $is_plugin_activate;
		} else {

			$error_message = __('This plugin requires <a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be active!', 'mw_wc_qbo_desk');
			die($error_message);
		}

	}
	
	protected static function check_if_woocommerce_active() { 
	  if ( is_multisite() ) {
		if(class_exists( 'WooCommerce' )) {
			return true;
		}
		return false;
	 }else{
		if(class_exists( 'WooCommerce' ) && in_array('woocommerce/woocommerce.php',apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))){
			return true;
		}
		return false;
	 }
	 return false;
	}

	protected static function create_databases(){

		global $wpdb;
		$sql = array();

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_customerid varchar(255) NOT NULL,
		  email varchar(255) NOT NULL,
		  first_name varchar(255) NOT NULL,
		  middle_name varchar(255) NOT NULL,
		  last_name varchar(255) NOT NULL,
		  company varchar(255) NOT NULL,
		  d_name varchar(255) NOT NULL,
		  fullname varchar(255) NOT NULL,
		  acc_num varchar(255) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_customers_pairs (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_customerid int(11) NOT NULL,
		  qbd_customerid varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `wc_id` int(11) NOT NULL,
		  `qbd_id` varchar(255) NOT NULL,
		  `d_type` varchar(255) NOT NULL,
		  `ext_data` varchar(255) NOT NULL,
		  `added_date` datetime NOT NULL,
		  `modify_date` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_items (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  sku varchar(255) NOT NULL,
		  product_type varchar(255) NOT NULL,
		  parent_id varchar(255) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_account (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  acc_num varchar(255) NOT NULL,
		  acc_type varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_class (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_paymentmethod (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  pm_type varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salestaxcode (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  stc_desc text NOT NULL,
		  is_active int(1) NOT NULL,
		  is_taxable int(1) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  t_type varchar(255) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  parent_id varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  is_default int(1) NOT NULL,
		  s_desc text NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		/*
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_othername (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  companyname varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		*/
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salesrep (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,		 
		  sr_e_ref_id varchar(255) NOT NULL,
		  sr_e_ref_name varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_customertype (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,		  
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_shipmethod (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,		  
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		//
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_template (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  t_type varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		
		//

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_log (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  log_title varchar(500) NOT NULL,
		  details text NOT NULL,
		  status int(1) NOT NULL,
		  log_type varchar(255) NOT NULL,
		  note text NOT NULL,
		  qbd_id varchar(255) NOT NULL,
		  added_date datetime NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=271 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_paymentmethod varchar(255) NOT NULL,
		  enable_payment int(1) NOT NULL,
		  qbo_account_id varchar(255) NOT NULL,
		  currency varchar(255) NOT NULL,
		  qb_p_method_id varchar(255) NOT NULL,
		  term_id_str varchar(255) NOT NULL,
		  ps_order_status varchar(255) NOT NULL,
		  enable_refund int(1) NOT NULL,
		  order_sync_as varchar(255) NOT NULL,
		  qb_cr_ba_id varchar(255) NOT NULL,
		  qb_ip_ar_acc_id varchar(255) NOT NULL,
		  `inv_due_date_days` int(3) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_promo_code (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  promo_id int(11) NOT NULL,
		  qbo_product_id varchar(255) NOT NULL,
		  class_id varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_shipping_product (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_shippingmethod varchar(255) NOT NULL,
		  qbo_product_id varchar(255) NOT NULL,
		  class_id varchar(255) NOT NULL,
		  qb_shipmethod_id varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_tax (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_tax_id int(11) NOT NULL,
		  qbo_tax_code varchar(255) NOT NULL,
		  wc_tax_id_2 varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
		
		//
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_wq_cf (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_field varchar(255) NOT NULL,
		  qb_field varchar(255) NOT NULL,		 
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_product_id int(11) NOT NULL,
		  quickbook_product_id varchar(255) NOT NULL,
		  class_id varchar(255) NOT NULL,
		  a_line_item_desc int(1) NOT NULL,
		  qb_ar_acc_id varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;";

		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_variation_pairs (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_variation_id int(11) NOT NULL,
		  quickbook_product_id varchar(255) NOT NULL,
		  class_id varchar(255) NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;";
		
		
		$sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_vendors (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_vendorid varchar(255) NOT NULL,
		  email varchar(255) NOT NULL,
		  first_name varchar(255) NOT NULL,
		  middle_name varchar(255) NOT NULL,
		  last_name varchar(255) NOT NULL,
		  company varchar(255) NOT NULL,
		  d_name varchar(255) NOT NULL,
		  jobtitle varchar(255) NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=165 DEFAULT CHARSET=latin1;";
		
		if($sql){

			foreach($sql as $query){

				$wpdb->query($query);

			}

		}		
		
		update_option( 'mw_wc_qbo_desk_save_log_for', 30);
		//update_option( 'mw_wc_qbo_desk_tax_format', 'TaxExclusive');		
		
		update_option( 'mw_wc_qbo_desk_rt_push_enable', 'true');
		//update_option( 'mw_wc_qbo_desk_select2_ajax', 'true');
		update_option( 'mw_wc_qbo_desk_select2_status', 'true');
		update_option( 'mw_wc_qbo_desk_rt_push_items', 'customer,order,payment');
		//update_option( 'mw_wc_qbo_desk_store_currency', get_option('woocommerce_currency'));
		
		update_option( 'mw_wc_qbo_desk_specific_order_status', 'wc-processing,wc-completed');
		
		//
		update_option( 'mw_wc_qbo_desk_cus_push_append_client_id', 'true');
		update_option( 'mw_wc_qbo_desk_xml_req_encoding', 'ISO-8859-1');
		
		update_option( 'mw_wc_qbo_desk_wc_cust_role', 'administrator,subscriber');
		
		//
		update_option( 'mw_wc_qbo_desk_hide_vpp_fmp_pages', 'true');
		
		update_option( 'mw_wc_qbo_desk_customer_match_by_name', 'true');
		
		/**/
		update_option( 'mw_wc_qbo_desk_wc_cus_view_name', 'first_name_last_name');
		
		update_option( 'mw_wc_qbo_desk_dmcb_fval', 'm_email_dn');
		
		//
		update_option( 'mw_wc_qbo_desk_auto_refresh_new_cust_prod', 'true');
	}
	
	protected static function do_after_activate(){

		$url = get_bloginfo('url');
		$company = get_bloginfo('name');
		$email = get_bloginfo('admin_email');
		$wordpress_version = get_bloginfo('version');

		$license_key = get_option('mw_wc_qbo_desk_license');
		$version = MW_QBO_Desktop_Admin::return_qbd_plugin_version();
		
		global $woocommerce;
		$woocommerce_version = $woocommerce->version;
		
		$message = '';
		$message .= "<b>WooCommerce Sync for QuickBooks Desktop</b></br>";
		$message .= "</br>";
		$message .= "<b>Company:</b> ".$company."</br>";
		$message .= "<b>Email:</b> ".$email."</br>";
		$message .= "<b>WooCommerce URL:</b> ".$url ."</br>";
		$message .= "<b>Wordpress Version:</b> ".$wordpress_version ."</br>";
		$message .= "<b>WooCommerce Version:</b> ".$woocommerce_version ."</br>";
		
		
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type:text/html;charset=UTF-8',
		);
		
		$to = 'notifications@myworks.design';
		
		wp_mail($to, 'New Install - Quickbooks Desktop', $message, $headers);
		
		$post_url = 'https://myworks.design/dashboard/api/dashboard/product/saveModule';
		
		$params = array(
			'api_version'=>'0.1',
			'result_type'=>'json',
			'process'=>'activated',
			'version'=>$version,
			'company'=>$company,
			'email'=>$email,
			'system_url'=>$url
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128); curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$response = curl_exec($ch);
		curl_close($ch);
	}
	
	protected static function check_invalid_chars_in_db_conn_info(){		
		//$invalid_chars = array('@',':','/','\\','\'','+','?','%','#');
		$invalid_chars = array('+','/','#','%','\'','?');
		foreach($invalid_chars as $char){
			if( strpos( DB_USER, $char ) !== false || strpos( DB_PASSWORD, $char ) !== false || strpos( DB_HOST, $char ) !== false || strpos( DB_NAME, $char ) !== false) {
				return true;
			}
		}
		return false;
	}

}