<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/admin
 * @author     MyWorks Design <sales@myworks.design>
 */
class MW_QBO_Desktop_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	 
	private $cur_db_version = '1.4.3';
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		global $MWQDC_LB,$MWQDC_AD;
		if(class_exists('MW_QBO_Desktop_Sync_Lib')){
			$MWQDC_LB = new MW_QBO_Desktop_Sync_Lib();
		}
		
		$MWQDC_AD = $this;
		
		require_once plugin_dir_path( __FILE__ ).'class-mw-qbo-desktop-ajax-actions.php';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in MW_QBO_Desktop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The MW_QBO_Desktop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		$query_string = explode('=',$_SERVER['QUERY_STRING']);
		//echo '<pre>'; print_r($query_string); echo '</pre>';
		if(isset($query_string[1])){
			$ext_qs_chk = false;
			/*			
			if($query_string[0] == 's' && isset($query_string[3]) && strpos($query_string[3],"shop_order") !== false){
				$ext_qs_chk = true;
			}
			*/
			
			if(stripos(json_encode($query_string),'shop_order') !== false){
				$ext_qs_chk = true;
			}
			
			if( strpos( $query_string[1], "mw-qbo-desktop" ) !== false){
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mw-qbo-desktop-admin.css', array(), $this->version, 'all' );
			}
			
			if( strpos( $query_string[1], "shop_order" ) !== false || $ext_qs_chk){
				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mw-qbo-desktop-admin-others.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in MW_QBO_Desktop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The MW_QBO_Desktop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		$query_string = explode('=',$_SERVER['QUERY_STRING']);
		//echo '<pre>'; print_r($query_string); echo '</pre>';
		if(isset($query_string[1])){
			$ext_qs_chk = false;
			if($query_string[0] == 's' && isset($query_string[3]) && strpos($query_string[3],"shop_order") !== false){
				$ext_qs_chk = true;
			}
			
			if(stripos(json_encode($query_string),'shop_order') !== false){
				$ext_qs_chk = true;
			}
			
			if( strpos( $query_string[1], "mw-qbo-desktop" ) !== false || strpos( $query_string[1], "shop_order" ) !== false || $ext_qs_chk ) {
			    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mw-qbo-desktop-admin.js', array( 'jquery' ), $this->version, false );
			}			
		}

	}

	public function create_admin_menus() {
		global $MWQDC_LB;
		add_menu_page( 
			__( 'MyWorks Sync</br><span style="font-size:10px;">QuickBooks Desktop</span>', 'mw_wc_qbo_desk' ),
			__( 'MyWorks Sync</br><span style="font-size:10px;">QuickBooks Desktop</span>', 'mw_wc_qbo_desk' ), 
			'read', 
			'mw-qbo-desktop', 
			array($this, 'mw_qbo_admin_menu'),
			plugin_dir_url( __FILE__ ) . 'image/menu-icon-sync.png', 
			3
		);

		add_submenu_page( 
			'mw-qbo-desktop', 
			__( 'Dashboard', 'mw_wc_qbo_desk' ),
			__( 'Dashboard', 'mw_wc_qbo_desk' ),
			'read',
			'mw-qbo-desktop',
			array($this, 'mw_qbo_admin_menu')
		);
		
		if($MWQDC_LB->is_license_active()){
			$queue_count = $MWQDC_LB->get_queue_count();
			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Queue', 'mw_wc_qbo_desk' ),
				__( 'Queue('.$queue_count.')', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-queue',
				array($this, 'mw_qbo_admin_menu_queue')
			);
		}
		

		add_submenu_page( 
			'mw-qbo-desktop', 
			__( 'Connection', 'mw_wc_qbo_desk' ),
			__( 'Connection', 'mw_wc_qbo_desk' ),
			'read',
			'mw-qbo-desktop-qwc-file',
			array($this, 'mw_qbo_admin_menu_qwc_file')
		);
		
		if($MWQDC_LB->is_license_active()){
			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Settings', 'mw_wc_qbo_desk' ),
				__( 'Settings', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-settings',
				array($this, 'mw_qbo_admin_menu_settings')
			);
			
			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Log', 'mw_wc_qbo_desk' ),
				__( 'Log', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-log',
				array($this, 'mw_qbo_admin_menu_log')
			);

			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Map', 'mw_wc_qbo_desk' ),
				__( 'Map', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-map',
				array($this, 'mw_qbo_admin_menu_map')
			);
			
			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Push', 'mw_wc_qbo_desk' ),
				__( 'Push', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-push',
				array($this, 'mw_qbo_admin_menu_push')
			);
			
			if($MWQDC_LB->option_checked('mw_wc_qbo_desk_pull_enable')){
				$sub_page = add_submenu_page( 
					'mw-qbo-desktop', 
					__( 'Pull', 'mw_wc_qbo_desk' ),
					__( 'Pull', 'mw_wc_qbo_desk' ),
					'read',
					'mw-qbo-desktop-pull',
					array($this, 'mw_qbo_admin_menu_pull')
				);
			}
			
			add_submenu_page( 
				'mw-qbo-desktop', 
				__( 'Refresh Data', 'mw_wc_qbo_desk' ),
				__( 'Refresh Data', 'mw_wc_qbo_desk' ),
				'read',
				'mw-qbo-desktop-refresh-data',
				array($this, 'mw_qbo_admin_menu_refresh_data')
			);
			
			if($MWQDC_LB->option_checked('mw_wc_qbo_desk_enable_db_status_chk_sb')){
				add_submenu_page( 
					'mw-qbo-desktop', 
					__( 'Database Status', 'mw_wc_qbo_desk' ),
					__( 'Database Status', 'mw_wc_qbo_desk' ),
					'read',
					'myworks-wc-qbd-sync-db-fix',
					array($this, 'myworks_wc_qbd_sync_db_fix')
				);
			}			
		}		
		
		/*add_submenu_page( 
			'mw-qbo-desktop', 
			__( 'Export', 'mw_wc_qbo_desk' ),
			__( 'Export - IIF', 'mw_wc_qbo_desk' ),
			'read',
			'mw-qbo-desktop-export',
			array($this, 'mw_qbo_admin_menu_export')
		);*/
		
	}

	public function myworks_wc_qbd_sync_db_fix(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/myworks-wc-qbd-sync-db-fix.php';
	}
	
	public function mw_qbo_admin_menu(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-dashboard.php';
	}

	public function mw_qbo_admin_menu_settings(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-settings.php';
	}
	
	public function mw_qbo_admin_menu_map(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-map.php';
	}
	
	public function mw_qbo_admin_menu_push(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-push.php';
	}
	
	public function mw_qbo_admin_menu_pull(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-pull.php';
	}	

	public function mw_qbo_admin_menu_export(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-export.php';
	}
	public function mw_qbo_admin_menu_qwc_file(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-qwc-file.php';
	}

	public function mw_qbo_admin_menu_queue(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-queue.php';
	}

	public function mw_qbo_admin_menu_log(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-menu-log.php';
	}

	public function mw_qbo_admin_menu_refresh_data(){
		require_once plugin_dir_path( __FILE__ ) . 'partials/mw-qbo-desktop-admin-quick-refresh-data.php';
	}
	
	public function return_plugin_version(){
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php', false, false );
		return $plugin_data['Version'];
	}

	public function fix_db_alter_issue($redirect=false,$rd_url=''){		
		global $MWQDC_LB;
		global $wpdb;
		$server_db = $MWQDC_LB->db_check_get_fields_details();
		//$MWQDC_LB->_p($server_db);
		if(is_array($server_db) && count($server_db)){
			foreach($server_db as $k=>$v){
				$is_db_updated = false;
				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod'){				
					if(!array_key_exists("term_id_str",$v)){					
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` CHANGE `term_id` `term_id_str` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
					
					if(!array_key_exists("enable_refund",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` ADD `enable_refund` INT(1) NOT NULL AFTER `ps_order_status`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
					
					//
					if(!array_key_exists("order_sync_as",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` ADD `order_sync_as` VARCHAR(255) NOT NULL AFTER `enable_refund`;";
						$wpdb->query($sql);
						$is_db_updated = true;			
					}
					
					//
					if(!array_key_exists("qb_ip_ar_acc_id",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` ADD `qb_ip_ar_acc_id` VARCHAR(255) NOT NULL AFTER `qb_cr_ba_id`;";
						$wpdb->query($sql);
						$is_db_updated = true;			
					}
					
					if(!array_key_exists("qb_cr_ba_id",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` ADD `qb_cr_ba_id` VARCHAR(255) NOT NULL AFTER `order_sync_as`;";
						$wpdb->query($sql);
						$is_db_updated = true;			
					}
				}

				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs'){
					if(!array_key_exists("ext_data",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs` ADD `ext_data` VARCHAR(255) NOT NULL AFTER `d_type`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
				}
				
				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_shipping_product'){
					if(!array_key_exists("qb_shipmethod_id",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_shipping_product` ADD `qb_shipmethod_id` VARCHAR(255) NOT NULL AFTER `class_id`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
				}
				
				//
				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers'){
					if(!array_key_exists("fullname",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_customers` ADD `fullname` VARCHAR(255) NOT NULL AFTER `d_name`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
					
					if(!array_key_exists("acc_num",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_customers` ADD `acc_num` VARCHAR(255) NOT NULL AFTER `fullname`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
				}
				
				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs'){
					if(!array_key_exists("a_line_item_desc",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs` ADD `a_line_item_desc` INT(1) NOT NULL AFTER `class_id`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
					
					if(!array_key_exists("qb_ar_acc_id",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_product_pairs` ADD `qb_ar_acc_id` VARCHAR(255) NOT NULL AFTER `a_line_item_desc`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
				}
				
				//
				if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite'){
					if(!array_key_exists("parent_id",$v)){
						$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite` ADD `parent_id` VARCHAR(255) NOT NULL AFTER `name`;";
						$wpdb->query($sql);
						$is_db_updated = true;					
					}
				}
				
				if($is_db_updated && $redirect){
					if(empty($rd_url)){
						$rd_url = admin_url('admin.php?page=mw-qbo-desktop');
					}
					//wp_redirect($rd_url);
					//exit(0);
				}
			}
			
			//New Tables
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite (
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
				$wpdb->query($sql);
			}
			
			/*
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_othername'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_othername (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  qbd_id varchar(255) NOT NULL,
				  name varchar(255) NOT NULL,
				  companyname varchar(255) NOT NULL,
				  is_active int(1) NOT NULL,		  
				  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
				$wpdb->query($sql);
			}
			*/
			
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salesrep (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  qbd_id varchar(255) NOT NULL,				 
				  sr_e_ref_id varchar(255) NOT NULL,
				  sr_e_ref_name varchar(255) NOT NULL,
				  is_active int(1) NOT NULL,		  
				  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
			}
			
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_customertype'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_customertype (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  qbd_id varchar(255) NOT NULL,
				  name varchar(255) NOT NULL,		  
				  is_active int(1) NOT NULL,		  
				  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
				$wpdb->query($sql);
			}
			
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_wq_cf'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_wq_cf (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  wc_field varchar(255) NOT NULL,
				  qb_field varchar(255) NOT NULL,		 
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
				$wpdb->query($sql);
			}
			
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_shipmethod'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_shipmethod (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  qbd_id varchar(255) NOT NULL,
				  name varchar(255) NOT NULL,		  
				  is_active int(1) NOT NULL,		  
				  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
				$wpdb->query($sql);
			}
			
			//
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_template'])){
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_template (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  qbd_id varchar(255) NOT NULL,
				  name varchar(255) NOT NULL,
				  t_type varchar(255) NOT NULL,
				  is_active int(1) NOT NULL,		  
				  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
				  PRIMARY KEY (id)
				) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
				$wpdb->query($sql);
			}
			
			if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_vendors'])){				
				$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_vendors (
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
				$wpdb->query($sql);
			}
		}
		
		/**/
		$sql = "ALTER TABLE `quickbooks_ticket` CHANGE `ipaddr` `ipaddr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;";
		$wpdb->query($sql);
		
		update_option('mw_wc_qbo_desk_cur_db_version',$this->cur_db_version);
	}
	
	public function mw_qbo_desktop_admin_init(){
		if(!session_id()) {
			session_start();
		}
		
		$this->mw_wc_qbo_enable_big_select_join();
		/**/
		if(!current_user_can('manage_woocommerce') && !current_user_can('view_woocommerce_report')){
			remove_menu_page( 'mw-qbo-desktop' );
			return false;
		}
		
		$this->mw_qbo_desktop_version_control();
		$this->mw_qbo_desktop_add_qbo_status_column();
		
		$this->qwc_user_pass_notice();
		
		$this->mw_qbo_desktop_bulk_action_admin_footer();
	}
	
	public function qwc_user_pass_notice(){
		global $MWQDC_LB;
		$query_string = explode('=',$_SERVER['QUERY_STRING']);
		if(is_array($query_string) && isset($query_string[1])){
			if( strpos( $query_string[1], "mw-qbo-desktop" ) !== false){
				$mw_wc_qbo_sync_license = $MWQDC_LB->get_option('mw_wc_qbo_desk_license','');
				if(!empty($mw_wc_qbo_sync_license)){
					$mw_qbo_dts_qwc_username = $MWQDC_LB->get_option('mw_qbo_dts_qwc_username');
					$mw_qbo_dts_qwc_password = $MWQDC_LB->get_option('mw_qbo_dts_qwc_password');
					
					$is_qup_ntc = true;					
					if(!empty($mw_qbo_dts_qwc_username) && !empty($mw_qbo_dts_qwc_password)){
						$mw_qbo_dts_qwc_password = $MWQDC_LB->decrypt($mw_qbo_dts_qwc_password);
						if(!empty($mw_qbo_dts_qwc_password)){
							$is_qup_ntc = false;
						}
					}
					
					if($is_qup_ntc){
						function qbd_admin_notice_qwc_up(){
							echo '<div class="notice notice-error mwqbd_admin_notice"><p>'.__('Please add Quickbooks Web Connector (QWC) username and password in connection page','mw_wc_qbo_desk').'</p></div>';
						}
						add_action( 'admin_notices', 'qbd_admin_notice_qwc_up' );
					}			
				}
			}
		}				
	}
	
	public function mw_wc_qbo_enable_big_select_join(){
		global $wpdb;
		$wpdb->query('SET SESSION SQL_BIG_SELECTS = 1');
		$wpdb->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
	}
	
	public static function is_trial_version_check(){
		global $MWQDC_LB;
		if($MWQDC_LB->option_checked('mw_wc_qbo_desk_trial_license')){
			$query_string = explode('=',$_SERVER['QUERY_STRING']);
			if(isset($query_string[1])){
				if( strpos( $query_string[1], "mw-qbo-desktop" ) !== false ) {
					$image = plugin_dir_url( __FILE__ ) . 'image/minilogo-square.png';
				    echo '
					<div class="myworks-trial-container text-btn" style="position: relative;">
						<img width="20"  alt="MyWorks Desktop Sync" title="MyWorks Desktop Sync" src="'.$image.'">&nbsp;
						<h3><b>'.(int) $MWQDC_LB->get_option('mw_wc_qbo_desk_trial_days_left').'</b> &nbsp;DAYS LEFT ON YOUR FREE TRIAL</h3>
						<a target="_blank" href="https://myworks.design/account/clientarea.php?action=productdetails&id='.$MWQDC_LB->get_option('mw_wc_qbo_desk_trial_license_serviceid').'" class="btn btn-info" role="button">UPGRADE NOW!
						</a></br>
						&nbsp;
						<a id="mwqs_tl_chk_again" style="font-size:12px;text-align: center;" href="javascript:void(0);">Check Again...</a>
					</div>
					';
					
					echo wp_nonce_field( 'myworks_wc_qbo_sync_trial_license_check_again_desk', 'trial_license_check_again_desk' );
					
					echo '
					<script>
					jQuery(document).ready(function($){
						$(\'#mwqs_tl_chk_again\').click(function(){
							jQuery(this).html(\'Loading...\');
							var data = {
								"action": \'mw_wc_qbo_sync_trial_license_check_again_desk\',
								"trial_license_check_again_desk": jQuery(\'#trial_license_check_again_desk\').val(),
							};
							jQuery.ajax({
							   type: "POST",
							   url: ajaxurl,
							   data: data,
							   cache:  false ,
							   //datatype: "json",
							   success: function(result){
								   if(result!=0 && result!=\'\'){
									location.reload();
								   }else{
									 jQuery(\'#mwqs_tl_chk_again\').html(\'Error!\');					 
								   }				  
							   },
							   error: function(result) { 		
									jQuery(\'#mwqs_tl_chk_again\').html(\'Error!\');
							   }
							});
						});
					});
					</script>
					';
							
				}
			}
		}
	}
	
	public function mw_qbo_desktop_bulk_action_admin_footer(){
		add_action('admin_footer-edit.php', 'custom_bulk_admin_footer');
		add_action('load-edit.php', 'custom_bulk_atq_process');
		
		function custom_bulk_admin_footer() {
			global $MWQDC_LB;
			global $post_type;
			if($post_type == 'shop_order') {
				//
				//if($MWQDC_LB->is_pl_res_tml()){return '';}
				
				$wco_ids = $MWQDC_LB->get_session_val('wc_order_id_list_ba_atq',array(),true);				
			?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {					
					/*
					jQuery('<option>').val('mw_qbd_add_to_queue').text('<?php _e('Add to Queue')?>').appendTo("select[name='action']");
					jQuery('<option>').val('mw_qbd_add_to_queue').text('<?php _e('Add to Queue')?>').appendTo("select[name='action2']");
					*/
					
					
					var optgroup = $('<optgroup>');
					optgroup.attr('label','QuickBooks Sync');					
					var option = $('<option>').val('mw_qbd_add_to_queue').text('<?php _e('Add to Queue')?>');
					optgroup.append(option);
					
					var optgroup_2 = $('<optgroup>');
					optgroup_2.attr('label','QuickBooks Sync');					
					var option = $('<option>').val('mw_qbd_add_to_queue').text('<?php _e('Add to Queue')?>');
					optgroup_2.append(option);
					
					optgroup.appendTo("select[name='action']");
					optgroup_2.appendTo("select[name='action2']");					
					
					<?php 
						if(is_array($wco_ids) && count($wco_ids)):
						$sync_window_url = $MWQDC_LB->get_sync_window_url().'&sync_type=push&item_ids='.implode(',',$wco_ids).'&item_type=invoice&fwop=1';
					?>
					popUpWindowDesk('<?php echo $sync_window_url;?>','mw_qs_invoice_push_desk',0,0,650,350);
					<?php endif;?>
				});
			</script>
			<?php
			}
		}
		
		function custom_bulk_atq_process(){
			global $MWQDC_LB;
			global $typenow;
			$post_type = $typenow;
			
			if($post_type == 'shop_order') {
				$wp_list_table = _get_list_table('WP_Posts_List_Table');
				$action = $wp_list_table->current_action();
				$allowed_actions = array("mw_qbd_add_to_queue");
				if(!in_array($action, $allowed_actions)) return;
				
				check_admin_referer('bulk-posts');
				if(isset($_REQUEST['post'])) {
					$post_ids = array_map('intval', $_REQUEST['post']);
				}
				
				if(empty($post_ids)) return;
				$sendback = remove_query_arg( array('queued', 'untrashed', 'deleted', 'ids'), wp_get_referer() );
				if ( ! $sendback )
					$sendback = admin_url( "edit.php?post_type=$post_type" );
				
				$pagenum = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );
				
				switch($action) {
					case 'mw_qbd_add_to_queue':
					$queued = 0;
					$MWQDC_LB->set_session_val('wc_order_id_list_ba_atq',$post_ids);
					//$sendback = add_query_arg( array('queued' => $queued, 'ids' => join(',', $post_ids) ), $sendback );
					break;
					
					default: return;
				}
				
				$sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status',  'post', 'bulk_edit', 'post_view'), $sendback );
				
				wp_redirect($sendback);
				exit();
			}
		}
	}
	
	public function mw_qbo_desktop_add_qbo_status_column(){
		add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column',11);
		function custom_shop_order_column($columns){
		    $columns['mw_qbo_desktop_inv_status'] = __( 'QBD Status','mw_wc_qbo_desk');
		    return $columns;
		}
		
		add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content_qbd', 10, 2 );
		function custom_orders_list_column_content_qbd( $column ){
			global $MWQDC_LB;			
		    global $post, $woocommerce, $the_order, $wpdb;
			
			/*
			$woo_version = $MWQDC_LB->get_woo_version_number();
			if ( $woo_version >= 3.0 ) {
				$order_id = (int) $the_order->get_id();
			}else{
				$order_id = (int) $the_order->id;
			}
			*/
			
			$order_id = 0;
			if(is_object($post) && !empty($post)){
				if(isset($post->ID) && isset($post->post_type) && $post->post_type == 'shop_order'){
					$order_id = (int) $post->ID;
				}
			}
			
			$sync_status_html = '<i class="fa fa-times-circle mwqd_wo_ic">Not Synced</i>';
			$dp_tbl = $wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs';
			
			switch ( $column ){
		        case 'mw_qbo_desktop_inv_status' :
					if($order_id){
						$dp_data = $MWQDC_LB->get_row("SELECT `qbd_id` FROM {$dp_tbl} WHERE `wc_id` = {$order_id} AND `d_type` = 'Order' AND `qbd_id` !='' ");
						if(is_array($dp_data) && count($dp_data)){
							$sync_status_html = '<i title="QBD Txn ID #'.$dp_data['qbd_id'].'" class="fa fa-check-circle mwqd_wo_ic">Synced</i>';
						}else{
							/**/
							$chk_qbq = $MWQDC_LB->get_row("SELECT `quickbooks_queue_id` FROM `quickbooks_queue` WHERE ident = {$order_id} AND `qb_status` = 'q' AND `qb_action` IN('InvoiceAdd','EstimateAdd','SalesReceiptAdd','SalesOrderAdd') ");
							if(is_array($chk_qbq) && count($chk_qbq)){
								$sync_status_html = '<i title="Queue ID #'.$chk_qbq['quickbooks_queue_id'].'" class="fa fa-check-circle mwqd_wo_ic mwop_sqc_l" style="background-color:#f8940a;">Queued</i>';
								$sync_status_html.= '
									<style>
										.mwop_sqc_l:before{background-color:#f8940a !important;}
									</style>
								';
								
							}else{
								$swu = true;
								if($MWQDC_LB->is_pl_res_tml() && !empty($post->post_date) && strtotime($post->post_date) < strtotime('-30 days')){
									$swu = false;
								}
								
								if($swu){
									$sync_window_url = $MWQDC_LB->get_sync_window_url().'&sync_type=push&item_ids='.$order_id.'&item_type=invoice&fwop=1';
									$sync_status_html.= '&nbsp;<a class="mwqd_wo_aqb" href="javascript:void(0)" onclick="javascript:popUpWindowDesk(\''.$sync_window_url.'\',\'mw_qs_invoice_push_desk\',0,0,650,350)">Add to queue</a>';
								}								
							}
						}
					}
					echo $sync_status_html;
		            break;
		    }
		}
	}
	
	public function mw_qbo_desktop_version_control(){
		global $MWQDC_LB;
		//$mw_qbo_desktop_license = $MWQDC_LB->get_option('mw_qbo_desktop_license');
		//$mw_qbo_desktop_localkey = $MWQDC_LB->get_option('mw_qbo_desktop_localkey');
		//update_option('mw_qbd_sync_last_updated_version', $this->return_plugin_version());	
		//$mw_qbo_desktop_beta_update = $MWQDC_LB->get_option('mw_qbo_desktop_beta_update');
		
		/*
		if (version_compare(get_option('mw_qbd_sync_last_updated_version'), '1.0.15', '<=')){
			$this->fix_db_alter_issue();
		}
		*/		
		
		if($this->cur_db_version!=$MWQDC_LB->get_option('mw_wc_qbo_desk_cur_db_version')){
			$this->fix_db_alter_issue();
		}
		
		if( $MWQDC_LB->is_license_active() ){

			
			require_once( WP_PLUGIN_DIR . '/myworks-quickbooks-desktop-sync/wp-updates-plugin.php' );
			new WPUpdatesPluginUpdater_1707( 'http://wp-updates.com/api/2/plugin', 'myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php' );
			
			/*$version_control = new WPUpdatesPluginUpdater_1707( 'http://wp-updates.com/api/2/plugin', 'myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php' );
			echo '<h1>I AM HERE!</h1>';
			echo '<pre>';
            print_r($version_control);
            echo '</pre>';
			die;*/
			/*if($mw_qbo_desktop_beta_update == 'true'){
				require_once( WP_PLUGIN_DIR . '/myworks-quickbooks-desktop-sync/wp-updates-plugin-beta.php' );
				new WPUpdatesPluginUpdater_1604( 'http://wp-updates.com/api/2/plugin', 'myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php' );
			}*/
		}
		
		function mwqbd_db_pass_admin_notice() {

			echo '<div title="MyWorks QuickBooks Desktop Setup Error" class="notice notice-error mwqs-setup-notice">'.__('<b>WooCommerce Sync for QuickBooks Desktop -</b> There is an unsupported character in your wp-config.php file - in either the database host, name, user or password. Simply check for and remove these characters + / # % \ ? from these fields. Your web developer/web host can also assist with this.', 'mw_wc_qbo_desk').'</div>';
		}
		
		/*
		if($MWQDC_LB->get_session_val('unsupported_db_chars',false,true)){
			add_action( 'admin_notices', 'mwqbd_db_pass_admin_notice' );
		}
		*/
		
		if($MWQDC_LB->check_invalid_chars_in_db_conn_info()){
			add_action( 'admin_notices', 'mwqbd_db_pass_admin_notice' );
		}
	}
	
	public function hook_variation_add($variation_info){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		
		if(is_array($variation_info)){
			$variation_id = (int) $variation_info['variation_id'];
			$manual = true;
		}else{
			$variation_id = (int) $variation_info;
			$manual = false;
		}
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('variation')){
			return false;
		}
		
		if($variation_id>0){
			$variation = get_post($variation_id);
			if(!is_object($variation) || empty($variation)){
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Variation','log_title'=>'Export Variation Error #'.$variation_id,'details'=>'Woocommerce variation not found','status'=>0));
				}
				return false;
			}
			
			if($variation->post_type!='product_variation'){
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Variation','log_title'=>'Export Variation Error #'.$variation_id,'details'=>'Woocommerce variation is not valid.','status'=>0));
				}
				return false;
			}			
			
			if($variation->post_status=='auto-draft'){
				return false;
			}
			
			if(!$manual && $variation->post_status=='draft'){
				return false;
			}
			
			$variation_data = $MWQDC_LB->get_wc_variation_info($variation_id,$manual);
			
			if(!isset($variation_data['_manage_stock'])){
				return false;
			}
			
			$_manage_stock = $MWQDC_LB->get_array_isset($variation_data,'_manage_stock','no',true);
			$_downloadable = $MWQDC_LB->get_array_isset($variation_data,'_downloadable','no',true);
			$_virtual = $MWQDC_LB->get_array_isset($variation_data,'_virtual','no',true);				
			
			if($_manage_stock=='yes'){
				$qbd_item_type = 'V_'.QUICKBOOKS_ADD_INVENTORYITEM;
			}elseif($_virtual=='yes'){
				$qbd_item_type = 'V_'.QUICKBOOKS_ADD_SERVICEITEM;
			}else{
				$qbd_item_type = 'V_'.QUICKBOOKS_ADD_NONINVENTORYITEM;
			}
			
			if(!$quickbook_product_id = $MWQDC_LB->if_qbo_product_exists($variation_data)){	
				$priority = ($manual)?1:$variation_id;
				if(!$MWQDC_LB->if_queue_exists($qbd_item_type,$variation_id)){
					$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());				
					$Queue->enqueue($qbd_item_type, $variation_id,$priority,array('is_variation'=>1),$MWQDC_LB->get_qbun());
					$MWQDC_LB->save_log(array('log_type'=>'Variation','log_title'=>'Export Variation #'.$variation_id,'details'=>'Variation added into queue','status'=>3));
					return true;
				}
			}else{
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Variation','log_title'=>'Export Variation Error #'.$variation_id,'details'=>'Variation already exists','status'=>0));				
				}
			}
			
		}
	}
	
	public function hook_product_add($product_info){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		
		if(is_array($product_info)){
			$product_id = (int) $product_info['product_id'];
			$manual = true;
		}else{
			$product_id = (int) $product_info;
			$manual = false;
		}
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('product')){
			return false;
		}
		
		if($product_id>0){
			$_product = wc_get_product( $product_id );
			if(empty($_product)){
				$MWQDC_LB->save_log(array('log_type'=>'Product','log_title'=>'Export Product Error #'.$product_id,'details'=>'Woocommerce product not found','status'=>0));
				return false;
			}
			
			if($_product->post->post_status=='auto-draft'){
				return false;
			}
			
			if(!$manual && $_product->post->post_status=='draft'){
				return false;
			}
			
			$product_data = $MWQDC_LB->get_wc_product_info($product_id,$manual);
			
			if(!isset($product_data['_manage_stock'])){
				return false;
			}
			
			$_manage_stock = $MWQDC_LB->get_array_isset($product_data,'_manage_stock','no',true);
			$_downloadable = $MWQDC_LB->get_array_isset($product_data,'_downloadable','no',true);
			$_virtual = $MWQDC_LB->get_array_isset($product_data,'_virtual','no',true);				
			
			if($_manage_stock=='yes'){
				$qbd_item_type = QUICKBOOKS_ADD_INVENTORYITEM;
			}elseif($_virtual=='yes'){
				$qbd_item_type = QUICKBOOKS_ADD_SERVICEITEM;
			}else{
				$qbd_item_type = QUICKBOOKS_ADD_NONINVENTORYITEM;
			}
			
			if(!$quickbook_product_id = $MWQDC_LB->if_qbo_product_exists($product_data)){				
				$priority = ($manual)?1:$product_id;
				if(!$MWQDC_LB->if_queue_exists($qbd_item_type,$product_id)){
					$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());				
					$Queue->enqueue($qbd_item_type, $product_id,$priority,null,$MWQDC_LB->get_qbun());
					$MWQDC_LB->save_log(array('log_type'=>'Product','log_title'=>'Export Product #'.$product_id,'details'=>'Product added into queue','status'=>3));
					return true;
				}
			}else{
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Product','log_title'=>'Export Product Error #'.$product_id,'details'=>'Product already exists','status'=>0));				
				}
			}
		}
	}	
	
	public function hook_user_register($user_info,$from_order=false){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		if(is_array($user_info)){
			$user_id = (int) $user_info['user_id'];
			$manual = true;
		}else{
			$user_id = (int) $user_info;
			$manual = false;
		}
		
		if(!$manual && !$from_order && !$MWQDC_LB->check_if_real_time_push_enable_for_item('customer')){	
			return false;
		}
		
		if(!$manual && $MWQDC_LB->is_plugin_active('woocommerce-aelia-currencyswitcher') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){
			return false;
		}
		
		/**/
		if(!$manual && $MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
			return false;
		}
		
		if($user_id){
			$user_data = get_userdata($user_id);			
			
			if($MWQDC_LB->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
				/*
				$io_cs = false;
				if(isset($user_data->roles) && is_array($user_data->roles)){
					$sc_roles_as_cus = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
					if(!empty($sc_roles_as_cus)){
						$sc_roles_as_cus = explode(',',$sc_roles_as_cus);
						if(is_array($sc_roles_as_cus) && count($sc_roles_as_cus)){
							foreach($sc_roles_as_cus as $sr){
								if(in_array($sr,$user_data->roles)){
									$io_cs = true;
									break;
								}
							}
						}
					}
				}
				*/
				
				$wc_user_role = '';
				if(isset($user_data->roles) && is_array($user_data->roles)){
					$wc_user_role = $user_data->roles[0];
				}
				
				$io_cs = true;
				if(!empty($wc_user_role)){
					$mw_wc_qbo_desk_aotc_rcm_data = get_option('mw_wc_qbo_desk_aotc_rcm_data');
					if(is_array($mw_wc_qbo_desk_aotc_rcm_data) && !empty($mw_wc_qbo_desk_aotc_rcm_data)){
						if(isset($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role])){
							if($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role] != 'Individual'){
								$io_cs = false;
							}
						}
					}
				}				
				
				if(!$io_cs){
					return false;
				}
			}			
			
			$is_sync_user_role = false;
			if(isset($user_data->roles) && is_array($user_data->roles)){
				$sc_roles = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cust_role');
				if(!empty($sc_roles)){
					$sc_roles = explode(',',$sc_roles);
					if(is_array($sc_roles) && count($sc_roles)){
						foreach($sc_roles as $sr){
							if(in_array($sr,$user_data->roles)){
								$is_sync_user_role = true;
								break;
							}
						}
					}
				}
				if(!$is_sync_user_role){
					if(in_array('customer',$user_data->roles)){
						$is_sync_user_role = true;						
					}
				}
			}			
			
			if($is_sync_user_role){
				$customer_data = $MWQDC_LB->get_wc_customer_info($user_id,$manual);
				
				if(!$qbd_customerid = $MWQDC_LB->if_qbo_customer_exists($customer_data)){
					$already_in_queue = false;
					if($customer_data['firstname']!='' || $manual || $from_order){
						if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_CUSTOMER,$user_id)){							
							$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
							/**/
							//if($MWQDC_LB->option_checked('mw_wc_qbo_desk_cus_push_append_client_id')){}
							if(!$MWQDC_LB->check_qbd_customer_by_display_name($customer_data) && !$MWQDC_LB->check_cg_ibn($customer_data)){
								if(!$MWQDC_LB->if_queue_exists('CustomerImport_ByName',$user_id)){
									$Queue->enqueue('CustomerImport_ByName', $user_id,3,null,$MWQDC_LB->get_qbun());
								}
							}
							
							//$action, $ident = null, $priority = 0, $extra = null, $user = null, $qbxml = null, $replace = true
							$Queue->enqueue(QUICKBOOKS_ADD_CUSTOMER, $user_id,2,null,$MWQDC_LB->get_qbun());
							$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Export Customer #'.$customer_data['wc_customerid'],'details'=>'Customer added into queue','status'=>3));
							return true;
						}else{
							$already_in_queue = true;
						}						
					}
					
					if($manual || $from_order){
						if(!$already_in_queue){
							$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Export Customer Error #'.$customer_data['wc_customerid'],'details'=>'Customer name can\'t be empty','status'=>0));
						}												
					}
					
				}else{
					if($manual){
						/*
						$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Export Customer Error #'.$customer_data['wc_customerid'],'details'=>'Customer already exists','status'=>0));
						*/
						$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
						$Queue->enqueue('CustomerMod_Query', $user_id,3,array('ListID'=>$qbd_customerid),$MWQDC_LB->get_qbun());
						
						$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Update Customer #'.$customer_data['wc_customerid'],'details'=>'Customer added into queue','status'=>3));
						
						return true;
					}					
				}				
			}			
		}
		return false;
	}
	
	public function hook_refund_add($order_sync_info=0,$refund_id=0){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		
		if(!(int) $refund_id && !is_array($order_sync_info)){
			global $wpdb;
			$ID = (int) $order_sync_info;
			$rf_data = $wpdb->get_row("SELECT ID FROM `{$wpdb->posts}` WHERE `post_type` = 'shop_order_refund' AND `post_parent` = {$ID} ORDER BY ID DESC LIMIT 0,1 ");
			if(is_object($rf_data) && count($rf_data)){
				$refund_id = $rf_data->ID;
			}
		}
		
		if(is_array($order_sync_info)){
			$order_id = (int) $order_sync_info['order_id'];
			$manual = true;
		}else{
			$order_id = (int) $order_sync_info;
			$manual = false;
		}
		$refund_id = (int) $refund_id;
		
		//$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Test #'.$refund_id.' Order #'.$order_id,'details'=>'Hook Testing.','status'=>2));
		
		/*
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('refund')){		
			return false;
		}
		*/
		
		if($MWQDC_LB->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync')){
			return false;
		}
		
		if($order_id && $refund_id > 0){
			if(!$MWQDC_LB->ord_pmnt_is_mt_ls_check_by_ord_id($refund_id,'shop_order_refund')){
				return false;
			}
			
			$order = get_post($order_id);
			
			$_payment_method = get_post_meta( $order_id, '_payment_method', true );
			$_order_currency = get_post_meta( $order_id, '_order_currency', true );
			
			if(!$MWQDC_LB->if_sync_refund(array('_payment_method'=>$_payment_method,'_order_currency'=>$_order_currency))){
				return false;
			}
			
			if(!is_object($order) || empty($order)){
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id.' Order #'.$order_id,'details'=>'Woocommerce order not found.','status'=>0));
				}
				return false;
			}
			
			if($order->post_type!='shop_order'){
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id.' Order #'.$order_id,'details'=>'Woocommerce order is not valid.','status'=>0));
				}
				return false;
			}
			
			$refund_data = $MWQDC_LB->get_wc_order_details_from_order($order_id,$order);
			if(!is_array($refund_data) || empty($refund_data)){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id.' Order #'.$order_id,'details'=>'Woocommerce order details not found.','status'=>0));
				}
				return false;
			}
			
			$wc_inv_num = $MWQDC_LB->get_array_isset($refund_data,'wc_inv_num','');
			$ord_id_num = ($wc_inv_num!='')?$wc_inv_num:$order_id;
			
			$refund_post = get_post($refund_id);
			if(empty($refund_post)){
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id.' Order #'.$order_id,'details'=>'Woocommerce refund not found!','status'=>0));
				}
				return false;
			}
			
			$refund_meta = get_post_meta($refund_id);
			$refund_data['refund_id'] = $refund_id;
	
			$refund_data['refund_date'] = $refund_post->post_date;
			$refund_data['refund_post_parent'] = $refund_post->post_parent;
			$refund_data['refund_note'] = $refund_post->post_excerpt;
			
			$_refund_amount = isset($refund_meta['_refund_amount'][0])?$refund_meta['_refund_amount'][0]:0;
			if($_refund_amount<= 0){
				return false;
			}
			$refund_data['_refund_amount'] = $_refund_amount;			
			
			if(!$MWQDC_LB->check_quickbooks_refund($refund_id,$order_id)){
				if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_CHECK,$refund_id)){
					$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
					$Queue->enqueue(QUICKBOOKS_ADD_CHECK, $refund_id,0,array('order_id'=>$order_id),$MWQDC_LB->get_qbun());
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund #'.$refund_id.' Order #'.$order_id,'details'=>'Refund added into queue','status'=>3));
					return true;
				}
			}else{
				if($manual){				
					$MWQDC_LB->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id.' Order #'.$order_id,'details'=>'Refund already exists','status'=>0));
				}					
			}
		}
		
		return false;
	}
	
	//Renewal Order
	public function myworks_wc_qbo_desk_comt_hook_wsrpc($subscription,$last_order){
		global $MWQDC_LB;
		global $wpdb;
		/*		
		$MWQDC_LB->save_log(array('log_type'=>'Test','log_title'=>'Renewal Order Hook Run','details'=>print_r($subscription,true).print_r($last_order,true),'status'=>2));
		*/
		
		if(is_object($subscription) && !empty($subscription) && is_object($last_order) && !empty($last_order)){
			$renewal_order_id = (int) $last_order->get_id();
			if($renewal_order_id>0){
				$this->hook_order_add($renewal_order_id);
				/**/
				$order_id = $renewal_order_id;
				
				/*
				$order = get_post($order_id);
				$invoice_data = $MWQDC_LB->get_wc_order_details_from_order($order_id,$order);
				$is_os_p_sync = $MWQDC_LB->if_sync_os_payment($invoice_data);
				if(!$is_os_p_sync){
					$this->hook_payment_add($order_id);
				}
				*/
				
				$pm_r = $MWQDC_LB->get_row("SELECT `meta_id` FROM `{$wpdb->postmeta}` WHERE `post_id` = '{$order_id}' AND `meta_key` = '_transaction_id' ");
				if(is_array($pm_r) && !empty($pm_r)){
					$payment_id = (int) $pm_r['meta_id'];
					if($payment_id > 0 && !$MWQDC_LB->if_queue_exists('ReceivePaymentAdd',$payment_id)){
						$this->hook_payment_add(array('payment_id'=>$payment_id));
					}
				}
			}
		}
	}
	
	public function hook_order_add($order_info){		
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		global $wpdb;
		
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		if(is_array($order_info)){
			$order_id = (int) $order_info['order_id'];
			$manual = true;
		}else{
			$order_id = (int) $order_info;
			$manual = false;
		}
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('order')){		
			return false;
		}
		
		if($order_id){
			/**/
			if($order_id < (int) $MWQDC_LB->get_option('mw_wc_qbo_desk_invoice_min_id')){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Invoice','log_title'=>'Export Order #'.$order_id,'details'=>'Order sync not allowed for ID less than #'.(int) $MWQDC_LB->get_option('mw_wc_qbo_desk_invoice_min_id'),'status'=>2));
				}
				return false;
			}
			
			if(!$MWQDC_LB->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			
			if(!is_object($order) || empty($order)){				
				if($manual){					
					$MWQDC_LB->save_log(array('log_type'=>'Invoice','log_title'=>'Export Order Error #'.$order_id,'details'=>'Woocommerce order not found.','status'=>0));
				}
				return false;
			}
			if($order->post_type!='shop_order'){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Invoice','log_title'=>'Export Order Error #'.$order_id,'details'=>'Woocommerce order is not valid.','status'=>0));
				}
				return false;
			}
			
			if($order->post_status=='auto-draft'){
				return false;
			}
			
			if(!$manual && $order->post_status=='draft'){
				return false;
			}
			
			$invoice_data = $MWQDC_LB->get_wc_order_details_from_order($order_id,$order);
			/*
			$wc_inv_num = $MWQDC_LB->get_array_isset($invoice_data,'wc_inv_num','');
			$ord_id_num = ($wc_inv_num!='')?$wc_inv_num:$order_id;
			*/
			
			$is_os_p_sync = false;$is_os_err = false;
			
			
			$only_sync_status = $MWQDC_LB->get_option('mw_wc_qbo_desk_specific_order_status');
			if($only_sync_status!=''){$only_sync_status = explode(',',$only_sync_status);}
			
			if(!$manual && (!is_array($only_sync_status) || (is_array($only_sync_status) && !in_array($order->post_status,$only_sync_status)))){			
				//$is_os_err = true;
				return false;
			}			
			
			if(!is_array($invoice_data) || !isset($invoice_data['_order_key']) || !isset($invoice_data['_order_total'])){
				if($manual && !$is_os_err){					
					$MWQDC_LB->save_log(array('log_type'=>'Invoice','log_title'=>'Export Order Error #'.$order_id,'details'=>'Woocommerce order details not found.','status'=>0));
				}
				/*
				if($is_os_p_sync){			
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error Order #'.$order_id,'details'=>'Woocommerce order details not found.','status'=>0));
				}
				*/
				return false;
			}
			
			//Prevent Multiple Entry			
			if(!$manual && !is_admin()){
				if((int) $MWQDC_LB->get_session_val('current_rt_order_id_'.$order_id,0)==$order_id){
					return false;
				}
				$MWQDC_LB->set_session_val('current_rt_order_id_'.$order_id,$order_id);
			}			
			
			$or_queue_added = false;
			$po_queue_added = false;
			
			if(!$MWQDC_LB->check_quickbooks_invoice($order_id)){				
				$is_valid = true;
				/*Customer Check*/
				
				$qbo_cus_id = '';
				
				/**/
				if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $MWQDC_LB->check_sh_fitbodywrap_cuscompt_hash()){
					if($MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) $MWQDC_LB->get_array_isset($invoice_data,'account_number','');
						if($c_account_number > 0){
							$qbo_cus_id = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($MWQDC_LB->is_plugin_active('woocommerce-aelia-currencyswitcher') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){
					$_order_currency = $MWQDC_LB->get_array_isset($invoice_data,'_order_currency','',true);
					if($_order_currency!=''){
						$aelia_cur_cus_map = get_option('mw_wc_qbo_desk_wacs_satoc_map_cur_cus');
						if(is_array($aelia_cur_cus_map) && count($aelia_cur_cus_map)){
							if(isset($aelia_cur_cus_map[$_order_currency]) && trim($aelia_cur_cus_map[$_order_currency])!=''){
								$qbo_cus_id = trim($aelia_cur_cus_map[$_order_currency]);
							}
						}
					}					
				}
				
				/**/
				if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = get_post_meta($order_id,'_shipping_country',true);
							if(empty($ord_country)){
								$ord_country = get_post_meta($order_id,'_billing_country',true);
							}
							
							if(!empty($ord_country)){
								if($ord_country == 'US'){
									$occ_mp_key = 'us_order';
								}else{
									$occ_mp_key = 'intl_order';
								}
							}
						}
						
						if(!empty($occ_mp_key)){
							if(isset($cccgunnar_qb_cus_map[$occ_mp_key]) && trim($cccgunnar_qb_cus_map[$occ_mp_key])!=''){
								$qbo_cus_id = trim($cccgunnar_qb_cus_map[$occ_mp_key]);
							}
						}
					}
				}
				
				$wc_cus_id = (int) get_post_meta($order_id,'_customer_user',true);
				
				/**/
				if($MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $MWQDC_LB->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
					if($wc_cus_id>0){						
						$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
					}else{						
						$shipping_country = get_post_meta($order_id,'_shipping_country',true);
					}
					
					if($shipping_country == 'US'){
						if($wc_cus_id>0){
							$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
						}else{
							$shipping_state = get_post_meta($order_id,'_shipping_state',true);
						}
						
						if($shipping_state!=''){
							$sus_qb_cus_map = get_option('mw_wc_qbo_desk_ship_us_st_qb_cus_map');
							if(is_array($sus_qb_cus_map) && count($sus_qb_cus_map)){
								if(isset($sus_qb_cus_map[$shipping_state]) && trim($sus_qb_cus_map[$shipping_state])!=''){
									$qbo_cus_id = trim($sus_qb_cus_map[$shipping_state]);
								}
							}
						}
					}else{
						$qbo_cus_id = $MWQDC_LB->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}				
				
				$user_info;
				$wc_user_role = '';
				
				$existing_qbo_user_type = '';
				$existing_qbo_user_id = '';
				
				if(empty($qbo_cus_id)){
					if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_all_order_to_customer')){						
						if($wc_cus_id>0){
							$customer_data = $MWQDC_LB->get_wc_customer_info($wc_cus_id);
							$qbo_cus_id = $MWQDC_LB->if_qbo_customer_exists($customer_data);
							if(empty($qbo_cus_id)){
								$this->hook_user_register($wc_cus_id,true);
							}					
						}else{
							$customer_data = $MWQDC_LB->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $MWQDC_LB->if_qbo_guest_exists($customer_data);
							if(empty($qbo_cus_id)){
								//Prevent repeated guest add for multiple non customer order sync - only first time								
								$is_guest_already_in_queue = $MWQDC_LB->check_guest_in_queue($customer_data,$order_id);
								
								if(!$is_guest_already_in_queue){
									$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
									/**/
									//if($MWQDC_LB->option_checked('mw_wc_qbo_desk_cus_push_append_client_id')){}
									if(!$MWQDC_LB->check_qbd_customer_by_display_name($customer_data) && !$MWQDC_LB->check_cg_ibn($customer_data)){
										if(!$MWQDC_LB->if_queue_exists('GuestImport_ByName',$order_id)){
											$Queue->enqueue('GuestImport_ByName', $order_id,3,array('is_guest'=>1),$MWQDC_LB->get_qbun());
										}
									}									
									
									$Queue->enqueue(QUICKBOOKS_ADD_GUEST, $order_id,2,null,$MWQDC_LB->get_qbun());
									$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Export Guest/Customer','details'=>'Customer added into queue','status'=>3));
								}								
							}
						}
					}else{
						//New Option
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
							
							/*
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
								if(!empty($sc_roles_as_cus)){
									$sc_roles_as_cus = explode(',',$sc_roles_as_cus);
									if(is_array($sc_roles_as_cus) && count($sc_roles_as_cus)){
										foreach($sc_roles_as_cus as $sr){
											if(in_array($sr,$user_info->roles)){
												$io_cs = true;
												break;
											}
										}
									}
								}
							}
							
							if($io_cs){
								$customer_data = $MWQDC_LB->get_wc_customer_info($wc_cus_id);
								$qbo_cus_id = $MWQDC_LB->if_qbo_customer_exists($customer_data);
								if(empty($qbo_cus_id)){
									$this->hook_user_register($wc_cus_id,true);
								}
							}
							*/
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
						/**/
						if(!empty($wc_user_role)){
							$io_cs = true;
							$mw_wc_qbo_desk_aotc_rcm_data = get_option('mw_wc_qbo_desk_aotc_rcm_data');
							if(is_array($mw_wc_qbo_desk_aotc_rcm_data) && !empty($mw_wc_qbo_desk_aotc_rcm_data)){
								if(isset($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role])){
									if($mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role] != 'Individual'){
										$io_cs = false;
									}
								}
							}
							
							if($io_cs){
								if($wc_cus_id>0){
									$customer_data = $MWQDC_LB->get_wc_customer_info($wc_cus_id);
									$qbo_cus_id = $MWQDC_LB->if_qbo_customer_exists($customer_data);
									if(empty($qbo_cus_id)){
										$this->hook_user_register($wc_cus_id,true);
									}
								}else{
									$customer_data = $MWQDC_LB->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $MWQDC_LB->if_qbo_guest_exists($customer_data);
									
									if(empty($qbo_cus_id)){
										$is_guest_already_in_queue = $MWQDC_LB->check_guest_in_queue($customer_data,$order_id);
										if(!$is_guest_already_in_queue){
											$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());											
											if(!$MWQDC_LB->check_qbd_customer_by_display_name($customer_data) && !$MWQDC_LB->check_cg_ibn($customer_data)){
												if(!$MWQDC_LB->if_queue_exists('GuestImport_ByName',$order_id)){
													$Queue->enqueue('GuestImport_ByName', $order_id,3,array('is_guest'=>1),$MWQDC_LB->get_qbun());
												}
											}
											
											$Queue->enqueue(QUICKBOOKS_ADD_GUEST, $order_id,2,null,$MWQDC_LB->get_qbun());
											$MWQDC_LB->save_log(array('log_type'=>'Customer','log_title'=>'Export Guest/Customer','details'=>'Customer added into queue','status'=>3));
										}								
									}
								}
							}
						}
					}
				}
				
				/**/				
				$is_guest_user = ($wc_cus_id>0)?false:true;
				if(empty($wc_user_role)){
					if(!$is_guest_user){
						if(empty($user_info)){
							$user_info = get_userdata($wc_cus_id);
						}
						
						if(isset($user_info->roles) && is_array($user_info->roles)){
							$wc_user_role = $user_info->roles[0];
						}
					}else{
						$wc_user_role = 'wc_guest_user';
					}
				}

				/**/
				if(!empty($qbo_cus_id)){
					$existing_qbo_user_id = $qbo_cus_id;
					if(!$is_guest_user){
						$existing_qbo_user_type = 'Customer';
					}else{
						$existing_qbo_user_type = 'Guest';
					}
				}
				
				if($is_valid){					
					$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
					$is_ord_pmnt_q_add = false;
					
					/**/
					$oq_add_extra = null;
					if(!empty($existing_qbo_user_id)){
						$oq_add_extra = array('existing_qbo_user_type'=>$existing_qbo_user_type,'existing_qbo_user_id'=>$existing_qbo_user_id);
					}
					
					/**/
					$pr_pg_ost = '';
					if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role' || $MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
						$pr_pg_ost = 'Invoice';
					}
					
					$qost_arr = array(
						'Invoice' => 'Invoice',
						'SalesReceipt' => 'SalesReceipt',
						'SalesOrder' => 'SalesOrder',
						'Estimate' => 'Estimate'										
					);
					
					//
					if($wc_user_role!='' && $MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role'){
						$mw_wc_qbo_desk_oqsa_pr_data = get_option('mw_wc_qbo_desk_oqsa_pr_data');
						if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && !empty($mw_wc_qbo_desk_oqsa_pr_data)){
							if(isset($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role])){
								if(isset($qost_arr[$mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]])){
									$pr_pg_ost = $mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role];
								}								
							}
						}						
					}
					
					//
					if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
						$_payment_method = $MWQDC_LB->get_array_isset($invoice_data,'_payment_method','',true);
						$_order_currency = $MWQDC_LB->get_array_isset($invoice_data,'_order_currency','',true);						
						if(!empty($_payment_method) && !empty($_order_currency)){
							$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$_order_currency);
							$order_sync_as = $MWQDC_LB->get_array_isset($pm_map_data,'order_sync_as','',true);
							if(!empty($order_sync_as) && isset($qost_arr[$order_sync_as])){
								$pr_pg_ost = $order_sync_as;
							}
						}
					}
					
					if($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt') || $pr_pg_ost == 'SalesReceipt'){
						if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_SALESRECEIPT,$order_id)){							
							$Queue->enqueue(QUICKBOOKS_ADD_SALESRECEIPT, $order_id,1,$oq_add_extra,$MWQDC_LB->get_qbun());
							$or_queue_added = true;
						}
						
					}elseif($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_order') || $pr_pg_ost == 'SalesOrder'){
						$is_ord_pmnt_q_add = true;
						if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_SALESORDER,$order_id)){
							$Queue->enqueue(QUICKBOOKS_ADD_SALESORDER, $order_id,1,$oq_add_extra,$MWQDC_LB->get_qbun());
							$or_queue_added = true;
						}
						
					}elseif($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_estimate') || $pr_pg_ost == 'Estimate'){
						if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_ESTIMATE,$order_id)){
							$Queue->enqueue(QUICKBOOKS_ADD_ESTIMATE, $order_id,1,$oq_add_extra,$MWQDC_LB->get_qbun());
							$or_queue_added = true;
						}
						
					}else{
						if(empty($pr_pg_ost) || $pr_pg_ost == 'Invoice'){
							$is_ord_pmnt_q_add = true;
							if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_INVOICE,$order_id)){
								$Queue->enqueue(QUICKBOOKS_ADD_INVOICE, $order_id,1,$oq_add_extra,$MWQDC_LB->get_qbun());
								$or_queue_added = true;
							}
						}						
					}					
					
					if($or_queue_added){
						$MWQDC_LB->save_log(array('log_type'=>'Order','log_title'=>'Export Order #'.$order_id,'details'=>'Order added into queue','status'=>3));
					}					
					
					/**/
					//woocommerce_stripe_settings
					$oea_ocnd = false;
					if(!$manual && ($order->post_status == 'wc-completed' || $order->post_status == 'wc-processing')){
						$oea_ocnd = true;
					}
					
					if($is_ord_pmnt_q_add && $oea_ocnd && $or_queue_added){
						$pm_r = $MWQDC_LB->get_row("SELECT `meta_id` FROM `{$wpdb->postmeta}` WHERE `post_id` = '{$order_id}' AND `meta_key` = '_transaction_id' ");
						if(is_array($pm_r) && !empty($pm_r)){
							$payment_id = (int) $pm_r['meta_id'];
							if($payment_id > 0 && !$MWQDC_LB->if_queue_exists('ReceivePaymentAdd',$payment_id)){
								$this->hook_payment_add(array('payment_id'=>$payment_id));
							}
						}
					}
					
				}
				
			}else{
				if($manual){				
					$MWQDC_LB->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$order_id,'details'=>'Order already exists','status'=>0));						
				}					
			}
			
			//PO			
			if($MWQDC_LB->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){
				if(!empty($MWQDC_LB->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor'))){
					if(!$MWQDC_LB->get_wc_data_pair_val('PurchaseOrder',$order_id)){						
						$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
						$is_po_add = $MWQDC_LB->chk_is_po_add($qbo_inv_items);
						
						/*
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
						*/
						
						if($is_po_add){
							if(!$MWQDC_LB->if_queue_exists(QUICKBOOKS_ADD_PURCHASEORDER,$order_id)){
								$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
								$Queue->enqueue(QUICKBOOKS_ADD_PURCHASEORDER, $order_id,0,null,$MWQDC_LB->get_qbun());
								$MWQDC_LB->save_log(array('log_type'=>'PurchaseOrder','log_title'=>'Export PurchaseOrder #'.$order_id,'details'=>'PurchaseOrder added into queue','status'=>3));
								//return true;
								$po_queue_added = true;
							}
						}
					}												
				}
			}
			
			if($or_queue_added || $po_queue_added){
				return true;
			}
			
		}
		return false;
	}
	
	public function hook_payment_add($pmnt_sync_info=''){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}
		
		global $wpdb;
		$_transaction_id = '';
		$payment_id = 0;
		$from_wc_order_page = false;
		if(is_array($pmnt_sync_info)){
			$payment_id = (int) $pmnt_sync_info['payment_id'];
			$_transaction_id = $MWQDC_LB->get_field_by_val($wpdb->postmeta,'meta_value','meta_id',$payment_id);
			$order_id = (int) $MWQDC_LB->get_field_by_val($wpdb->postmeta,'post_id','meta_id',$payment_id);
			$manual = true;
			
			if(isset($pmnt_sync_info['from_wc_order_page'])){
				$from_wc_order_page = true;
			}
			
		}else{
			$order_id = (int) $pmnt_sync_info;
			$_transaction_id = get_post_meta($order_id, '_transaction_id', true );
			$manual = false;
		}
		
		if(!$order_id){
			return false;
		}
		
		if($manual && !$payment_id){
			return false;
		}
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('payment')){
			return false;
		}		
		
		if($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){
			return false;
		}
		
		/**/
		if($payment_id > 0 && $MWQDC_LB->if_queue_exists('ReceivePaymentAdd',$payment_id)){
			return false;
		}
		
		if($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
			//return false;
		}
		
		if(!$MWQDC_LB->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
			return false;
		}
		
		if($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_estimate')){
			return false;
		}
		
		/**/
		if(is_array($pmnt_sync_info) && isset($pmnt_sync_info['opp_a_ord_push']) && $pmnt_sync_info['opp_a_ord_push']){
			$manual = false;
		}		
		
		/**/
		$qost_arr = array(
			'Invoice' => 'Invoice',
			'SalesReceipt' => 'SalesReceipt',
			'SalesOrder' => 'SalesOrder',
			'Estimate' => 'Estimate'										
		);
		
		$pr_pg_ost = '';
		
		if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role'){			
			$wc_user_role = '';
			$wc_cus_id = get_post_meta($order_id, '_customer_user', true );
			$user_info = get_userdata($wc_cus_id);
			if($wc_cus_id > 0){
				$user_info = get_userdata($wc_cus_id);
				if(isset($user_info->roles) && is_array($user_info->roles)){
					$wc_user_role = $user_info->roles[0];
				}
			}else{
				$wc_user_role = 'wc_guest_user';
			}
			
			if(!empty($wc_user_role)){
				$mw_wc_qbo_desk_oqsa_pr_data = get_option('mw_wc_qbo_desk_oqsa_pr_data');
				if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && !empty($mw_wc_qbo_desk_oqsa_pr_data)){
					if(isset($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role])){
						if(isset($qost_arr[$mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]])){
							$pr_pg_ost = $mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role];
						}								
					}
				}
			}			
			
		}
		
		if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
			$_payment_method = get_post_meta($order_id, '_payment_method', true );			
			if(!empty($_payment_method) && !empty($_order_currency)){
				if($MWQDC_LB->wacs_base_cur_enabled()){
					$base_currency = get_woocommerce_currency();
					$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$base_currency);
				}else{
					$_order_currency = get_post_meta($order_id, '_order_currency', true );
					$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$_order_currency);
				}				
				$order_sync_as = $MWQDC_LB->get_array_isset($pm_map_data,'order_sync_as','',true);
				if(!empty($order_sync_as) && isset($qost_arr[$order_sync_as])){
					$pr_pg_ost = $order_sync_as;
				}
			}
		}
		
		if($pr_pg_ost == 'SalesReceipt' || $pr_pg_ost == 'Estimate'){
			return false;
		}		
		
		$wc_inv_no = '';
		
		if($MWQDC_LB->is_plugin_active('woocommerce-sequential-order-numbers-pro','woocommerce-sequential-order-numbers') && $MWQDC_LB->option_checked('mw_wc_qbo_sync_compt_p_wsnop')){
			if($MWQDC_LB->is_plugin_active('woocommerce-sequential-order-numbers')){
				$wc_inv_no = get_post_meta( $order_id, '_order_number', true );
			}else{
				$wc_inv_no = get_post_meta( $order_id, '_order_number_formatted', true );
			}			
		}		
		
		$ord_id_num = ($wc_inv_no!='')?$wc_inv_no:$order_id;
		
		$_transaction_id_tmp = (!$_transaction_id)?'TXN-'.$order_id:$_transaction_id;
		if($_transaction_id_tmp){
			$payment_data = $MWQDC_LB->wc_get_payment_details_by_txn_id($_transaction_id,$order_id);
			if(empty($payment_data)){				
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$payment_id,'details'=>'Woocommerce payment info not found','status'=>0));
				}else{			
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error for Order #'.$ord_id_num,'details'=>'Woocommerce payment info not found','status'=>0));				
				}
				return false;
			}
			
			//10-11-2017
			$payment_id = (int) $MWQDC_LB->get_array_isset($payment_data,'payment_id','',true);
			if($payment_id<1){
				$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error for Order #'.$ord_id_num,'details'=>'Woocommerce payment ID not found','status'=>0));
				return false;
			}
			
			$_payment_method = $MWQDC_LB->get_array_isset($payment_data,'payment_method','',true);
			$_payment_method_title = $MWQDC_LB->get_array_isset($payment_data,'payment_method_title','',true);
			
			$_order_currency = $MWQDC_LB->get_array_isset($payment_data,'order_currency','',true);
			
			$base_currency = '';
			if($MWQDC_LB->wacs_base_cur_enabled()){
				$base_currency = get_woocommerce_currency();
				$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$base_currency);
			}else{
				$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$_order_currency);
			}
			
			/**/
			$qost_arr = array(
				'Invoice' => 'Invoice',
				'SalesReceipt' => 'SalesReceipt',
				'SalesOrder' => 'SalesOrder',
				'Estimate' => 'Estimate'										
			);
			
			$wc_user_role = '';
			$pr_pg_ost = '';
			$wc_cus_id = $MWQDC_LB->get_array_isset($payment_data,'customer_user',0);
			if($wc_cus_id>0){
				$user_info = get_userdata($wc_cus_id);
				if(isset($user_info->roles) && is_array($user_info->roles)){
					$wc_user_role = $user_info->roles[0];
				}
			}else{
				$wc_user_role = 'wc_guest_user';
			}
			
			if(!empty($wc_user_role)){
				if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
					$order_sync_as = $MWQDC_LB->get_array_isset($pm_map_data,'order_sync_as','',true);
					if(!empty($order_sync_as) && isset($qost_arr[$order_sync_as])){
						$pr_pg_ost = $order_sync_as;
					}
				}
				
				if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role'){
					$mw_wc_qbo_desk_oqsa_pr_data = get_option('mw_wc_qbo_desk_oqsa_pr_data');
					if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && !empty($mw_wc_qbo_desk_oqsa_pr_data)){
						if(isset($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role])){
							if(isset($qost_arr[$mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]])){
								$pr_pg_ost = $mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role];
							}
						}
					}
				}
			}
			
			if($pr_pg_ost == 'SalesReceipt' || $pr_pg_ost == 'Estimate'){
				return false;
			}
			
			$enable_payment = (int) $MWQDC_LB->get_array_isset($pm_map_data,'enable_payment',0);
			if($MWQDC_LB->wacs_base_cur_enabled()){
				$payment_amount = get_post_meta($order_id,'_order_total_base_currency',true);
			}else{
				$payment_amount = $MWQDC_LB->get_array_isset($payment_data,'order_total',0);
			}
			
			$payment_amount = floatval($payment_amount);
			
			$is_valid_payment = false;
			if($enable_payment && $payment_amount>0){
				$is_valid_payment = true;
			}
			$log_currency = ($base_currency!='')?$base_currency:$_order_currency;
			
			if(!$is_valid_payment){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$payment_id,'details'=>'Payment not enabled or invalid payment amount for gateway:'.$_payment_method.', currency:'.$log_currency,'status'=>0));
				}
				return false;
			}
			
			if(!$MWQDC_LB->check_quickbooks_payment($payment_id)){
				if(!$MWQDC_LB->if_queue_exists('ReceivePaymentAdd',$payment_id)){
					$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());
					$Queue->enqueue('ReceivePaymentAdd', $payment_id,0,null,$MWQDC_LB->get_qbun());
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment #'.$payment_id,'details'=>'Payment added into queue','status'=>3));
					return true;
				}
			}else{
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$payment_data['payment_id'],'details'=>'Payment already exists','status'=>0));
				}
			}
		}else{			
			$MWQDC_LB->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error for Order #'.$ord_id_num,'details'=>'Woocommerce Payment TXN ID Not Found','status'=>0));
		}
		
		return false;
	}
	
	public function hook_product_stock_update($ivnt_sync_info){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}		
		$product_id = 0;		
		if(is_array($ivnt_sync_info)){
			$product_id = (int) $ivnt_sync_info['product_id'];
			$manual = true;
		}else{
			if(is_object($ivnt_sync_info) && !empty($ivnt_sync_info)){
				$product_id = (int) $ivnt_sync_info->get_id();
			}
			$manual = false;
		}

		/**/
		if(!$manual && $MWQDC_LB->get_session_val('prevent_rt_inventory_push_ot',0,true)){
			return false;
		}
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('inventory')){
			return false;
		}
		if($product_id){
			$_product = wc_get_product( $product_id );
			if(empty($_product)){
				$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Inventory Error #'.$product_id,'details'=>'Woocommerce product not found','status'=>0));
				return false;
			}
			
			$product_data = $MWQDC_LB->get_wc_product_info($product_id,$manual);
			$_manage_stock = $MWQDC_LB->get_array_isset($product_data,'_manage_stock','no',true);
			
			if($_manage_stock!='yes'){
				//$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Inventory Error #'.$product_id,'details'=>'Invalid Woocommerce inventory','status'=>0));
				return false;
			}
			
			if(!$quickbook_product_id = $MWQDC_LB->if_qbo_product_exists($product_data,true)){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Inventory Error #'.$product_id,'details'=>'QuickBooks inventory not found','status'=>0));
				}
				return false;
			}
			
			//$_stock = $MWQDC_LB->get_array_isset($product_data,'_stock',0,true);
			
			//$priority = ($manual)?1:$product_id;
			$priority = 4;
			if(!$MWQDC_LB->if_queue_exists('InventoryAdjustmentAdd',$product_id)){
				$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());		
				$Queue->enqueue('InventoryAdjustmentAdd', $product_id,$priority,null,$MWQDC_LB->get_qbun());
				$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Inventory #'.$product_id,'details'=>'Inventory added into queue','status'=>3));
				return true;
			}
			
			
		}
		return false;
		
	}
	
	public function hook_variation_stock_update($ivnt_sync_info){
		if(!class_exists('WooCommerce')) return;
		global $MWQDC_LB;
		if(!$MWQDC_LB->is_qwc_connected()) {
			return false;
		}		
		$variation_id = 0;		
		if(is_array($ivnt_sync_info)){
			$variation_id = (int) $ivnt_sync_info['variation_id'];
			$manual = true;
		}else{
			if(is_object($ivnt_sync_info) && !empty($ivnt_sync_info)){
				$variation_id = (int) $ivnt_sync_info->get_id();
			}
			$manual = false;
		}		
		
		if(!$manual && !$MWQDC_LB->check_if_real_time_push_enable_for_item('inventory')){
			return false;
		}
		if($variation_id){
			$variation = get_post($variation_id);
			if(empty($variation)){
				$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Variation Inventory Error #'.$variation_id,'details'=>'Woocommerce product not found','status'=>0));
				return false;
			}
			
			$variation_data = $MWQDC_LB->get_wc_variation_info($variation_id,$manual);
			$_manage_stock = $MWQDC_LB->get_array_isset($variation_data,'_manage_stock','no',true);
			
			if($_manage_stock!='yes'){
				//$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Variation Inventory Error #'.$variation_id,'details'=>'Invalid Woocommerce inventory','status'=>0));
				return false;
			}
			
			if(!$quickbook_product_id = $MWQDC_LB->if_qbo_product_exists($variation_data,true)){
				if($manual){
					$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Variation Inventory Error #'.$variation_id,'details'=>'QuickBooks inventory not found','status'=>0));
				}
				return false;
			}
			
			//$_stock = $MWQDC_LB->get_array_isset($variation_data,'_stock',0,true);
			
			//$priority = ($manual)?1:$variation_id;
			$priority = 4;
			if(!$MWQDC_LB->if_queue_exists('V_InventoryAdjustmentAdd',$variation_id)){
				$Queue = new QuickBooks_WebConnector_Queue($MWQDC_LB->get_dsn());	
				$Queue->enqueue('V_InventoryAdjustmentAdd', $variation_id,$priority,array('is_variation'=>1),$MWQDC_LB->get_qbun());
				$MWQDC_LB->save_log(array('log_type'=>'Inventory','log_title'=>'Export Variation Inventory #'.$variation_id,'details'=>'Inventory added into queue','status'=>3));
				return true;
			}
			
			
		}
		return false;
		
	}
	
	public static function return_qbd_plugin_version(){
		$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/myworks-quickbooks-desktop-sync/myworks-quickbooks-desktop-sync.php', false, false );
		return $plugin_data['Version'];
	}

}
