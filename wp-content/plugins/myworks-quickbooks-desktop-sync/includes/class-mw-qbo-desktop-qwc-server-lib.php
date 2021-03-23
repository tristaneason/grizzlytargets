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

class MW_QBO_Desktop_Sync_Qwc_Server_Lib extends MW_QBO_Desktop_Sync_Lib{
	private $qbd_timezone;
	private $debug_queue;
	public function __construct($dsn){
		if(!session_id()) {
			session_start();
		}
		
		parent::__construct();
		
		if($this->dsn && QuickBooks_Utilities::initialized($this->dsn)){
			QuickBooks_WebConnector_Queue_Singleton::initialize($this->dsn);
		}

		if (function_exists('date_default_timezone_set')){			
			$qbd_tz_setting = $this->get_qbd_timezone();
			//$qbd_tz_setting = 'America/New_York';
			date_default_timezone_set($qbd_tz_setting);
			$this->qbd_timezone = $qbd_tz_setting;
		}
		
		if(isset($_GET['debug'])){
			$this->debug();
		}
		
		//$this->debug_queue = true;
	}
	
	public function debug(){
		global $wpdb;
		$order_id = 465;
		$order_id_ext = 0;
		//echo $this->GetProductQbxml(75,'Inventory');
		//echo $this->GetProductQbxml(42,'NonInventory');
		//echo $this->GetSalesReceiptQbxml($order_id);
		//echo $this->GetSalesReceiptQbxml_GPI($order_id_ext);
		//echo $this->GetSalesOrderQbxml($order_id);
		//echo $this->GetEstimateQbxml($order_id);
		//$this->_p($this->get_order_base_currency_total_from_order_id(0));		
		//$this->_p($this->get_wc_order_details_from_order($order_id,get_post($order_id)));
		//$this->_p($this->get_wc_order_details_from_order(397,get_post(397)));
		//$this->_p($this->get_wc_order_details_from_order(403,get_post(403)));
		//$this->_p($this->get_wc_country_list());
		//echo $this->GetInvoiceQbxml($order_id);
		//echo $this->GetPurchaseOrderQbxml($order_id);
		//echo $this->GetCustomerQbxml(3);
		//echo $this->GetCustomerQbxml($order_id,true);
		
		/*
		$customer_data = $this->get_wc_customer_info_from_order($order_id);
		$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
		$this->_p($customer_data);
		$this->_p($qbo_cus_id,true);
		*/
		
		//$this->_p($this->get_wc_customer_info(11));
		//$this->_p($this->get_wc_customer_info_from_order(305));
		
		//echo $this->GetPaymentQbxml(10969);
		//echo $this->GetInventoryAdjustmentAddQbxml(76);
		//$this->_p($this->get_wc_variation_info(86));
		//echo $this->GetProductQbxml(86,'NonInventory',1);
		//echo $this->server_timezone;
		//echo $this->get_auto_inventory_pull_m_dt_from();
		//$user_data = get_userdata(17);
		//$this->_p($user_data);		
		//echo $this->GetRefundQbxml_Check(284,283);
		//die;
		
		/*
		if($this->check_cg_ibn($this->get_wc_customer_info_from_order(310))){
			echo 'Testing...';
		}
		*/
		
		/*
		if($this->check_cf_map_data_ext_field_value_exists(317,'Invoice')){
			echo 'Testing...';
		}
		*/
		//echo $this->GetOrderDataExtAddQbxml(317,array('Qos_Type'=>'Invoice','TxnID'=>'000-000000000000'));
		//$this->oth_debug_function();
		
		//$this->Adjust_wmior_product_total_stock_after_locations_stock_update(74,'8000001C-1538436286','');
		//$this->_p($this->wc_get_sm_data_from_method_id_str('wf_shipping_ups','id',array('name'=>'3 Day Select (UPS)')));
		//$this->_p($this->get_custom_shipping_map_data_from_name('QuickBooks Shipping'));
		
	}
	
	private function oth_debug_function(){
		//		
	}
	
	public function is_debug_queue(){
		return $this->debug_queue;
	}
	
	public function Debug_Queue_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		// Build the request
		/*
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="' . $version . '"?>
		<QBXML>
			<QBXMLMsgsRq onError="'.$this->getonError().'">
				<CustomerQueryRq  requestID="' . $requestID . '">
				<ListID >80000005-1515687113</ListID>
				<OwnerID >0</OwnerID>
				</CustomerQueryRq >
			</QBXMLMsgsRq>
		</QBXML>';
		*/
		
		$display_name = 'New Testing Filter';
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="' . $version . '"?>
		<QBXML>
			<QBXMLMsgsRq onError="'.$this->getonError().'">
				<CustomerQueryRq requestID="' . $requestID . '">
					<NameFilter>
						<MatchCriterion>Contains</MatchCriterion>
						<Name>'.$display_name.'</Name>
					</NameFilter>								
				</CustomerQueryRq>
			</QBXMLMsgsRq>
		</QBXML>';
		
		$this->add_test_log($xml);
		return $xml;
	}
	
	public function Debug_Queue_response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$this->add_test_log($xml);
	}

	private function getonError(){
		$onError = 'continueOnError';//stopOnError
		return $onError;
	}

	private function get_qbxml_prefix($version='6.0'){
		$version = '13.0';
		$xml_encoding = $this->get_option('mw_wc_qbo_desk_xml_req_encoding');
		if($xml_encoding!='utf-8' && $xml_encoding!='ISO-8859-1'){
			$xml_encoding = 'utf-8';
		}
		//qbxml version="2.0"
		return '
			<?xml version="1.0" encoding="'.$xml_encoding.'"?>
			<?qbxml version="'.$version.'"?>
			<QBXML>
			<QBXMLMsgsRq onError="'.$this->getonError().'">
		';
		//continueOnError#stopOnError
	}

	private function get_qbxml_suffix(){
		return '
			</QBXMLMsgsRq>
			</QBXML>
		';
	}
	
	public function get_qbxml_locale(){
		$sl = trim($this->get_option('mw_wc_qbo_desk_xml_req_locale'));
		if($sl==''){$sl = 'US';}
		return $sl;
	}
	
	/*QBXML Functions*/
	
	public function GetPurchaseOrderQbxml($order_id,$update=false){
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->_p($invoice_data);
			if(is_array($invoice_data) && count($invoice_data)){
				global $wpdb;
				
				$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
				$qbo_cus_id = '';
				
				$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
				$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
				
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);
				
				$qbo_vendor_id = $this->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor');
				if(empty($qbo_vendor_id)){
					$this->save_log(array('log_type'=>'PurchaseOrder','log_title'=>'Export PurchaseOrder Error #'.$order_id,'details'=>'QuickBooks Vendor Not Found','status'=>0));
					return false;
				}
				
				$qbd_vendor_data = $this->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_vendors WHERE `qbd_vendorid` = %s",$qbo_vendor_id));
				if(empty($qbd_vendor_data)){
					$this->save_log(array('log_type'=>'PurchaseOrder','log_title'=>'Export PurchaseOrder Error #'.$order_id,'details'=>'QuickBooks Vendor Not Found','status'=>0));
					return false;
				}
				
				$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
				$DocNumber_Po = 'PO-'.$DocNumber;
				
				$PurchaseOrder = new QuickBooks_QBXML_Object_PurchaseOrder();
				$PurchaseOrder->set('RefNumber',$DocNumber_Po);
				
				$PurchaseOrder->set('Memo',$DocNumber);
				
				$PurchaseOrder->set('VendorRef ListID',$qbo_vendor_id);
				
				$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
				$wc_inv_date = $this->format_date($wc_inv_date);
				if($wc_inv_date!=''){
					$PurchaseOrder->set('TxnDate',$wc_inv_date);
				}

				$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
				$wc_inv_due_date = $this->format_date($wc_inv_due_date);
				if($wc_inv_due_date!=''){
					$PurchaseOrder->set('DueDate',$wc_inv_due_date);
				}
				
				
				$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
				
				//
				$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
				if(is_array($qbo_inv_items) && count($qbo_inv_items)){
					foreach($qbo_inv_items as $qbo_item){
						if(isset($qbo_item['socpo_manage_stock']) && $qbo_item['socpo_manage_stock'] == 'yes'){
							if(isset($qbo_item['socpo_stock']) && floatval($qbo_item['socpo_stock']) < 0){ //<=
								$PurchaseOrderLine = new QuickBooks_QBXML_Object_PurchaseOrder_PurchaseOrderLine();
								$PurchaseOrderLine->set('ItemRef ListID',$qbo_item["ItemRef"]);
								
								if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
									$PurchaseOrderLine->set('ClassRef ListID',$qbo_item["ClassRef"]);
								}
								
								$Description = $qbo_item['Description'];
								if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
									$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
									$li_sku = get_post_meta( $li_item_id, '_sku', true );
									if($li_sku!=''){
										$Description.=' ('.$li_sku.')';
									}
								}
								
								//Extra Description
								if(isset($qbo_item["Qbd_Ext_Description"])){
									$Description.= $qbo_item["Qbd_Ext_Description"];
								}
								
								if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
									$PurchaseOrderLine->set('Desc',$Description);
								}							
								
								$UnitPrice = $qbo_item["UnitPrice"];
								$Qty = $qbo_item["Qty"];
								$Amount = $Qty*$UnitPrice;
								
								//$PurchaseOrderLine->set('Rate',$UnitPrice);
								$PurchaseOrderLine->set('Quantity',$Qty);
								//$PurchaseOrderLine->set('Amount',$Amount);
								
								$PurchaseOrder->addPurchaseOrderLine($PurchaseOrderLine);
							}
						}
					}
				}
				
				$qbdv_info_arr = $qbd_vendor_data['info_arr'];
				if(!empty($qbdv_info_arr)){					
					$qbdv_info_arr = @unserialize($qbdv_info_arr);
				}
				
				if(is_array($qbdv_info_arr) && count($qbdv_info_arr)){					
					if(!empty($qbdv_info_arr['VendorAddress_Addr1'])){
						$PurchaseOrder->set('VendorAddress Addr1',$qbdv_info_arr['VendorAddress_Addr1']);
						$PurchaseOrder->set('VendorAddress Addr2',$qbdv_info_arr['VendorAddress_Addr2']);
						$PurchaseOrder->set('VendorAddress Addr3',$qbdv_info_arr['VendorAddress_Addr3']);
						$PurchaseOrder->set('VendorAddress Addr4',$qbdv_info_arr['VendorAddress_Addr4']);
						$PurchaseOrder->set('VendorAddress Addr5',$qbdv_info_arr['VendorAddress_Addr5']);
						
						$PurchaseOrder->set('VendorAddress City',$qbdv_info_arr['VendorAddress_City']);
						$PurchaseOrder->set('VendorAddress State',$qbdv_info_arr['VendorAddress_State']);
						$PurchaseOrder->set('VendorAddress PostalCode',$qbdv_info_arr['VendorAddress_PostalCode']);
						$PurchaseOrder->set('VendorAddress Country',$qbdv_info_arr['VendorAddress_Country']);
						
						if(isset($qbdv_info_arr['VendorAddress_Note'])){
							$PurchaseOrder->set('VendorAddress Note',$qbdv_info_arr['VendorAddress_Note']);
						}
					}
					
					/*
					if(!empty($qbdv_info_arr['ShipAddress_Addr1'])){
						$PurchaseOrder->set('ShipAddress Addr1',$qbdv_info_arr['ShipAddress_Addr1']);
						$PurchaseOrder->set('ShipAddress Addr2',$qbdv_info_arr['ShipAddress_Addr2']);
						$PurchaseOrder->set('ShipAddress Addr3',$qbdv_info_arr['ShipAddress_Addr3']);
						$PurchaseOrder->set('ShipAddress Addr4',$qbdv_info_arr['ShipAddress_Addr4']);
						$PurchaseOrder->set('ShipAddress Addr5',$qbdv_info_arr['ShipAddress_Addr5']);
						
						$PurchaseOrder->set('ShipAddress City',$qbdv_info_arr['ShipAddress_City']);
						$PurchaseOrder->set('ShipAddress State',$qbdv_info_arr['ShipAddress_State']);
						$PurchaseOrder->set('ShipAddress PostalCode',$qbdv_info_arr['ShipAddress_PostalCode']);
						$PurchaseOrder->set('ShipAddress Country',$qbdv_info_arr['ShipAddress_Country']);
						
						if(isset($qbdv_info_arr['ShipAddress_Note'])){
							$PurchaseOrder->set('ShipAddress Note',$qbdv_info_arr['ShipAddress_Note']);
						}
					}
					*/
				}
				
				if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!='' || $this->get_array_isset($invoice_data,'_shipping_company','',true)!=''){
					$_shipping_first_name = $this->get_array_isset($invoice_data,'_shipping_first_name','',true);
					$_shipping_last_name = $this->get_array_isset($invoice_data,'_shipping_last_name','',true);
					
					$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
					$country = $this->get_country_name_from_code($country);
					
					$_shipping_company = $this->get_array_isset($invoice_data,'_shipping_company','',true);
					$_shipping_address_1 = $this->get_array_isset($invoice_data,'_shipping_address_1','',true);
					$_shipping_address_2 = $this->get_array_isset($invoice_data,'_shipping_address_2','',true);
					$_shipping_city = $this->get_array_isset($invoice_data,'_shipping_city','',true);
					$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
					$_shipping_postcode = $this->get_array_isset($invoice_data,'_shipping_postcode','',true);
					
					$rfs_arr = array($_shipping_first_name,$_shipping_last_name,$_shipping_company,$_shipping_address_1,$_shipping_address_2,$_shipping_city,$_shipping_state,$_shipping_postcode,$country);
					
					$r_fa = $this->get_ord_saf_addrs($rfs_arr,$invoice_data);
					$PurchaseOrder->set('ShipAddress Addr1',$this->get_array_isset($r_fa,0,'',true));
					$PurchaseOrder->set('ShipAddress Addr2',$this->get_array_isset($r_fa,1,'',true));
					$PurchaseOrder->set('ShipAddress Addr3',$this->get_array_isset($r_fa,2,'',true));
					$PurchaseOrder->set('ShipAddress Addr4',$this->get_array_isset($r_fa,3,'',true));
					$PurchaseOrder->set('ShipAddress Addr5',$this->get_array_isset($r_fa,4,'',true));
				}
				
				//$this->_p($PurchaseOrder);
				
				$qbxml = $PurchaseOrder->asQBXML(QUICKBOOKS_ADD_PURCHASE_ORDER,null,$this->get_qbxml_locale());					
				$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();					
				$qbxml = $this->qbxml_search_replace($qbxml);
				
				return $qbxml;
			}
		}
	}
	
	protected function qbxml_search_replace($qbxml){
		$qbxml = str_replace('<Rate></Rate>','<Rate>0.00</Rate>',$qbxml);
		return $qbxml;
	}
	
	//
	public function GetInvoiceQbxml($order_id,$extra=null){
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->add_test_log(print_r($invoice_data,true));
			//$this->_p($invoice_data);
			if(is_array($invoice_data) && count($invoice_data)){
				global $wpdb;
				$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
				$qbo_cus_id = '';

				$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
				$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
				
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);

				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) $this->get_array_isset($invoice_data,'account_number','');
						if($c_account_number > 0){
							$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
				if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
							if(empty($ord_country)){
								$ord_country = $this->get_array_isset($invoice_data,'_billing_country','',true);
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
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
					if($wc_cus_id>0){						
						$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
					}else{						
						//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
						$shipping_country = $this->get_array_isset($invoice_data,'_shipping_country','');
					}
					
					if($shipping_country == 'US'){
						if($wc_cus_id>0){
							$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
						}else{
							//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
							$shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','');
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
						$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}
				
				if(empty($qbo_cus_id)){
					if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
						if($wc_cus_id>0){
							//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
							if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
							}else{
								$customer_data = $this->get_wc_customer_info($wc_cus_id);
							}						
							//$this->_p($customer_data);
							$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
						}else{
							$customer_data = $this->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
						}
					}else{
						/*
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}							
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							
						}else{
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
						}
						*/
						
						/**/
						$wc_user_role = '';
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
								}
							}else{
								$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
							}
						}
						//
					}
				}				
				
				if(empty($qbo_cus_id)){
					$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$order_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
					return false;
				}
				
				if($qbo_cus_id!=''){
					$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
					$Invoice = new QuickBooks_QBXML_Object_Invoice();

					//$Invoice->setCustomerName();
					$Invoice->setCustomerListID($qbo_cus_id);
					
					if(!$this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						$Invoice->setRefNumber($DocNumber);
					}
					
					/**/
					$TemplateRef = $this->get_array_isset($invoice_data,'TemplateRef','');
					if(!empty($TemplateRef)){
						$Invoice->set('TemplateRef ListID', $TemplateRef);
					}
					
					$inv_sr_txn_class = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
					if($inv_sr_txn_class!=''){
						$Invoice->setClassListID($inv_sr_txn_class);
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_desk_qbo_push_invoice_is_print_true')){
						$Invoice->set('IsToBePrinted',1);
					}
					
					/**/
					$customer_note = $this->get_array_isset($invoice_data,'customer_note','');
					//$Invoice->set('PONumber',$customer_note);
					
					//PO
					if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $this->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){
						if(!empty($this->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor'))){
							$DocNumber_Po = 'PO-'.$DocNumber;
							//
							$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
							if($this->chk_is_po_add($qbo_inv_items)){
								$Invoice->set('PONumber',$DocNumber_Po);
							}							
						}
					}
					
					
					//NP Billing State - QBD Class Map					
					if($this->option_checked('mw_wc_qbo_desk_compt_np_bus_qbc_map_ed')){
						$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
						if(!empty($_billing_state)){
							$bus_qbc_map = get_option('mw_wc_qbo_desk_np_bill_us_st_qb_cl_map');
							if(is_array($bus_qbc_map) && count($bus_qbc_map)){
								if(isset($bus_qbc_map[$_billing_state]) && !empty($bus_qbc_map[$_billing_state])){
									$qbd_classid = $bus_qbc_map[$_billing_state];
									$Invoice->setClassListID($qbd_classid);
								}
							}
						}						
					}
					
					$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
					$wc_inv_date = $this->format_date($wc_inv_date);
					if($wc_inv_date!=''){
						$Invoice->setTxnDate($wc_inv_date);
					}

					$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
					$wc_inv_due_date = $this->format_date($wc_inv_due_date);
					if($wc_inv_due_date!=''){
						$Invoice->setDueDate($wc_inv_due_date);
					}
					
					
					$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
					
					/*Count Total Amounts*/
					$_cart_discount = $this->get_array_isset($invoice_data,'_cart_discount',0);
					$_cart_discount_tax = $this->get_array_isset($invoice_data,'_cart_discount_tax',0);

					$_order_tax = (float) $this->get_array_isset($invoice_data,'_order_tax',0);
					$_order_shipping_tax = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax',0);
					$_order_total_tax = ($_order_tax+$_order_shipping_tax);

					$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);
					
					if($this->wacs_base_cur_enabled()){
						$_cart_discount_base_currency = $this->get_array_isset($invoice_data,'_cart_discount_base_currency',0);
						$_cart_discount_tax_base_currency = $this->get_array_isset($invoice_data,'_cart_discount_tax_base_currency',0);
						
						$_order_tax_base_currency = (float) $this->get_array_isset($invoice_data,'_order_tax_base_currency',0);
						$_order_shipping_tax_base_currency = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax_base_currency',0);
						$_order_total_tax_base_currency = ($_order_tax_base_currency+$_order_shipping_tax_base_currency);
						
						$order_shipping_total_base_currency = $this->get_array_isset($invoice_data,'_order_shipping_base_currency',0);
					}
					
					/*Qbd settings*/
					$qbo_is_sales_tax = true;
					$qbo_company_country = 'US';
					$qbo_is_shipping_allowed = false;

					/*Tax rates*/
					$qbo_tax_code = '';
					$apply_tax = false;
					$is_tax_applied = false;
					$is_inclusive = false;

					$qbo_tax_code_shipping = '';

					$tax_rate_id = 0;
					$tax_rate_id_2 = 0;

					$tax_details = (isset($invoice_data['tax_details']))?$invoice_data['tax_details']:array();
					
					//Tax Totals From tax Lines
					$calc_order_tax_totals_from_tax_lines = true;					
					if($calc_order_tax_totals_from_tax_lines){
						$_order_tax = 0;
						$_order_shipping_tax = 0;
						$_order_total_tax = 0;
						
						if($this->wacs_base_cur_enabled()){
							$_order_tax_base_currency = 0;
							$_order_shipping_tax_base_currency = 0;
							$_order_total_tax_base_currency = 0;
						}
						
						if(count($tax_details)){
							foreach($tax_details as $td){
								$_order_tax+=$td['tax_amount'];
								$_order_shipping_tax+=$td['shipping_tax_amount'];
								$_order_total_tax+=$td['tax_amount']+$td['shipping_tax_amount'];
								
								if($this->wacs_base_cur_enabled()){
									$_order_tax_base_currency+=$td['tax_amount_base_currency'];
									$_order_shipping_tax_base_currency+=$td['shipping_tax_amount_base_currency'];
									$_order_total_tax_base_currency+=$td['tax_amount_base_currency']+$td['shipping_tax_amount_base_currency'];
								}
							}
						}
					}
					$_order_total_tax = $this->qbd_limit_decimal_points($_order_total_tax);
					if($this->wacs_base_cur_enabled()){
						$_order_total_tax_base_currency = $this->qbd_limit_decimal_points($_order_total_tax_base_currency);
					}
					
					//TaxJar Settings
					$is_taxjar_active = false;
					$woocommerce_taxjar_integration_settings = get_option('woocommerce_taxjar-integration_settings');
					$wc_taxjar_enable_tax_calculation = 0;
					if(is_array($woocommerce_taxjar_integration_settings) && count($woocommerce_taxjar_integration_settings)){
						if(isset($woocommerce_taxjar_integration_settings['enabled']) && $woocommerce_taxjar_integration_settings['enabled']=='yes'){
							$wc_taxjar_enable_tax_calculation = 1;
						}
					}
					
					if($this->is_plugin_active('taxjar-simplified-taxes-for-woocommerce','taxjar-woocommerce') && $this->option_checked('mw_wc_qbo_desk_wc_taxjar_support') && $wc_taxjar_enable_tax_calculation=='1'){
						$is_taxjar_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//Avatax Settings
					$is_avatax_active = false;
					$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
					if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_desk_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
						$is_avatax_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//
					$is_so_tax_as_li = false;
					if($this->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
						$is_so_tax_as_li = true;
						$qbo_is_sales_tax = false;
					}
					
					if($qbo_is_sales_tax){
						if(count($tax_details)){
							$tax_rate_id = $tax_details[0]['rate_id'];
						}

						if(count($tax_details)>1){
							if($tax_details[1]['tax_amount']>0){
								$tax_rate_id_2 = $tax_details[1]['rate_id'];
							}
						}

						/*
						if(count($tax_details)>1 && $qbo_is_shipping_allowed){
							foreach($tax_details as $td){
								if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
									$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
									break;
								}
							}
						}
						*/
						
						$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
						if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
							$apply_tax = true;
						}

						//$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
						$is_qbo_dual_tax = false;

						/*
						if(count($Tax_Code_Details)){
							if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
								$is_qbo_dual_tax = true;
							}
						}

						$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
						$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

						$NetAmountTaxable = 0;

						if($is_qbo_dual_tax){
							$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
							$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
							$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
							$NetAmountTaxable_2 = 0;
						}
						*/

						/*
						if($qbo_tax_code_shipping!=''){
							$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
							$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
							$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
							$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
							$NetAmountTaxable_Shipping = 0;
						}
						*/

						$_prices_include_tax = $this->get_array_isset($invoice_data,'_prices_include_tax','no',true);
						if($qbo_is_sales_tax){
							$tax_type = $this->get_tax_type($_prices_include_tax);
							$is_inclusive = $this->is_tax_inclusive($tax_type);
						}
					}


					$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
					
					/**/
					if($this->check_sh_paamc_hash()){
						$ARAccountRef = '';
						if(is_array($qbo_inv_items) && count($qbo_inv_items)){
							foreach($qbo_inv_items as $qbo_item){
								if(isset($qbo_item['QbArAccId']) && !empty($qbo_item['QbArAccId'])){
									$ARAccountRef = $qbo_item['QbArAccId'];
								}
							}
						}
						
						if(!empty($ARAccountRef)){
							$Invoice->setARAccountListID($ARAccountRef);
						}
					}					
					
					$is_bundle_order = false;
					$map_bundle_support = false;
					
					if(!$is_bundle_order){
						if(is_array($qbo_inv_items) && count($qbo_inv_items)){
							foreach($qbo_inv_items as $qbo_item){
								if($qbo_item['qbo_product_type'] == 'Group'){
									$map_bundle_support = true;
									$InvoiceLineGroup = new QuickBooks_QBXML_Object_Invoice_InvoiceLineGroup();
									$InvoiceLineGroup->setItemListID($qbo_item["ItemRef"]);
									$Description = $qbo_item['Description'];
									$Qty = $qbo_item["Qty"];									
									$InvoiceLineGroup->setQuantity($Qty);
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										//$InvoiceLineGroup->setDesc($Description);
									}
									//TotalAmount
									$Invoice->addInvoiceLineGroup($InvoiceLineGroup);
								}
							}
						}
					}
					
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							
							if($map_bundle_support && $qbo_item['qbo_product_type'] == 'Group'){
								continue;
							}
							
							$InvoiceLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
							//$InvoiceLine->setItemName($qbo_item['Description']);
							$InvoiceLine->setItemListID($qbo_item["ItemRef"]);
							if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
								$InvoiceLine->setClassListID($qbo_item["ClassRef"]);
							}
							
							$Description = $qbo_item['Description'];
							if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
								$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
								$li_sku = get_post_meta( $li_item_id, '_sku', true );
								if($li_sku!=''){
									$Description.=' ('.$li_sku.')';
								}
							}
							
							$UnitPrice = $qbo_item["UnitPrice"];
							if($this->wacs_base_cur_enabled()){
								$UnitPrice = $qbo_item["UnitPrice_base_currency"];
								$Description.= " ({$_order_currency} ".$qbo_item["UnitPrice"].")";
							}
							
							//Extra Description
							if(isset($qbo_item["Qbd_Ext_Description"])){
								$Description.= $qbo_item["Qbd_Ext_Description"];
							}
							
							if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
								$InvoiceLine->setDesc($Description);
							}
							
							$Qty = $qbo_item["Qty"];
							$Amount = $Qty*$UnitPrice;
							$InvoiceLine->setRate($UnitPrice);
							$InvoiceLine->setQuantity($Qty);
							$InvoiceLine->setAmount($Amount);
							
							if($this->option_checked('mw_wc_qbo_desk_compt_wqclns_ed')){
								$LotNumber  = $this->get_array_isset($qbo_item,'lot','');
								if(!empty($LotNumber)){
									$InvoiceLine->set('LotNumber',$LotNumber);
								}
							}
							
							/*
							SerialNumber
							LotNumber
							ServiceDate
							*/
							
							if($qbo_is_sales_tax){
								if($apply_tax && $qbo_item["Taxed"]){
									$is_tax_applied = true;
									/*$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;*/
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									/*
									if($is_inclusive){
										$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
									}
									*/
									if($TaxCodeRef!=''){
										$InvoiceLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$InvoiceLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$InvoiceLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							/**/
							$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							if($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								/*
								$mw_warehouse = 0;
								if(isset($qbo_item["_order_item_wh"])){
									$mw_warehouse = (int) $qbo_item["_order_item_wh"];
								}else{
									$mw_warehouse = (int) $this->get_array_isset($invoice_data,'mw_warehouse',0);
								}
								*/
								
								$mw_warehouse = $this->get_mwr_oiw_mw_idls($qbo_item, $invoice_data);
								
								if($mw_warehouse > 0){
									$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
									if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && isset($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse])){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = trim($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse]);
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											if($this->is_inv_site_bin_allowed()){
												if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
													$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
													if(is_array($site_bin_arr) && !empty($site_bin_arr)){
														$InvoiceLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
														if(isset($site_bin_arr[1])){
															$InvoiceLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
														}
													}
												}
											}else{
												$InvoiceLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											}									
										}
									}
								}
							}							
							
							if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									if($this->is_inv_site_bin_allowed()){
										if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
											$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
											if(is_array($site_bin_arr) && !empty($site_bin_arr)){
												$InvoiceLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
												if(isset($site_bin_arr[1])){
													$InvoiceLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
												}
											}
										}
									}else{
										$InvoiceLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}									
								}
							}
							
							/**/
							if($this->option_checked('mw_wc_qbo_desk_compt_np_liqtycustcolumn_ed') && $this->check_sh_liqtycustcolumn_hash()){
								$cqtyf = $this->get_option('mw_wc_qbo_desk_compt_np_liqtycustcolumn_cqtyf');
								if(empty($cqtyf)){
									$cqtyf = 'Other1';
								}
								$InvoiceLine->set($cqtyf , $Qty);
							}
							
							$Invoice->addInvoiceLine($InvoiceLine);
						}
					}
					
					//pgdf compatibility
					if($this->get_wc_fee_plugin_check()){
						$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
						if(is_array($dc_gt_fees) && count($dc_gt_fees)){
							foreach($dc_gt_fees as $df){
								$InvoiceLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
								
								$UnitPrice = $df['_line_total'];
								$Qty = 1;
								$Amount = $Qty*$UnitPrice;
								
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$invoice_data);
								$InvoiceLine->setItemListID($df_ItemRef);
								
								$InvoiceLine->setRate($UnitPrice);
								$InvoiceLine->setQuantity($Qty);
								$InvoiceLine->setAmount($Amount);
								
								$InvoiceLine->setDesc($df['name']);
								
								$_line_tax = $df['_line_tax'];
								if($_line_tax && $qbo_is_sales_tax){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$InvoiceLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$InvoiceLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
								
								/*
								if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
									$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
									if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
										$InvoiceLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
								*/
								
								$Invoice->addInvoiceLine($InvoiceLine);
							}
							
						}						
					}
					
					//pw_gift_card compatibility
					if($this->is_plugin_active('pw-woocommerce-gift-cards','pw-gift-cards') && $this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed') && !empty($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'))){
						$pw_gift_card = (isset($invoice_data['pw_gift_card']))?$invoice_data['pw_gift_card']:array();
						if(is_array($pw_gift_card) && count($pw_gift_card)){
							foreach($pw_gift_card as $pgc){
								$pgc_amount = $pgc['amount'];
								if($pgc_amount > 0){
									$pgc_amount = -1 * abs($pgc_amount);
								}
								
								$Qty = 1;
								$Description = $pgc['card_number'];
								$InvoiceLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
								$InvoiceLine->setItemListID($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'));
								$InvoiceLine->setRate($pgc_amount);
								$InvoiceLine->setQuantity($Qty);
								$InvoiceLine->setAmount($pgc_amount);
								
								$InvoiceLine->setDesc($Description);
								
								/*
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$InvoiceLine->setSalesTaxCodeListID($zero_rated_tax_code);
								*/
								
								$Invoice->addInvoiceLine($InvoiceLine);
							}
						}
					}
					
					/*Add Invoice Coupons*/
					$used_coupons  = (isset($invoice_data['used_coupons']))?$invoice_data['used_coupons']:array();
					$qbo_is_discount_allowed = true;
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						$qbo_is_discount_allowed = false;
					}
					
					if($qbo_is_discount_allowed && count($used_coupons)){
						foreach($used_coupons as $coupon){
							$coupon_name = $coupon['name'];
							$coupon_discount_amount = $coupon['discount_amount'];
							$coupon_discount_amount = -1 * abs($coupon_discount_amount);
							$coupon_discount_amount_tax = $coupon['discount_amount_tax'];
							
							if($this->wacs_base_cur_enabled()){
								$coupon_discount_amount_base_currency = $this->get_array_isset($coupon,'discount_amount_base_currency',0);
								$coupon_discount_amount_base_currency = -1 * abs($coupon_discount_amount_base_currency);
								
								$coupon_discount_amount_tax_base_currency = $coupon['discount_amount_tax_base_currency'];
							}

							$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
							$DiscountLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
							$DiscountLine->setItemListID($coupon_product_arr["ItemRef"]);
							if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
								$DiscountLine->setClassListID($coupon_product_arr["ClassRef"]);
							}
							$Description = $coupon_product_arr['Description'];							
							
							if($this->wacs_base_cur_enabled()){
								$Description.= " ({$_order_currency} {$coupon_discount_amount})";
								$DiscountLine->setRate($coupon_discount_amount_base_currency);
								if($coupon_product_arr['qbo_product_type'] != 'Discount'){
									$DiscountLine->setAmount($coupon_discount_amount_base_currency);
								}																
							}else{
								$DiscountLine->setRate($coupon_discount_amount);
								if($coupon_product_arr['qbo_product_type'] != 'Discount'){
									$DiscountLine->setAmount($coupon_discount_amount);
								}								
							}
							
							if($coupon_product_arr['qbo_product_type'] != 'Discount'){
								$DiscountLine->setQuantity(1);
							}							
							$DiscountLine->setDesc($Description);
							
							if($qbo_is_sales_tax){
								if($coupon_discount_amount_tax > 0 || $is_tax_applied){
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$DiscountLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}								
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									//$DiscountLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
								}
							}
							
							$Invoice->addInvoiceLine($DiscountLine);
						}
					}					
					
					/*Add Invoice Shipping*/
					$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
					
					$sp_arr_first = array();
					if(is_array($shipping_details) && !empty($shipping_details)){
						foreach($shipping_details as $sd_k => $sd_v){
							
							$shipping_method = '';
							$shipping_method_name = '';
							$shipping_taxes = '';
							$smt_id = 0;
							
							if(isset($shipping_details[$sd_k])){
								if($this->get_array_isset($shipping_details[$sd_k],'type','')=='shipping'){
									$shipping_method_id = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
									if($shipping_method_id!=''){
										if(isset($shipping_details[$sd_k]['instance_id']) && $shipping_details[$sd_k]['instance_id']>0){
											$shipping_method = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
											$smt_id = (int) $this->get_array_isset($shipping_details[$sd_k],'instance_id',0);
										}else{
											$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'',$sd_v);
											$smt_id = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'id',$sd_v);
										}								
									}
									
									$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
									$shipping_method_name =  $this->get_array_isset($shipping_details[$sd_k],'name','',true,30);
									$shipping_taxes = $this->get_array_isset($shipping_details[$sd_k],'taxes','');
								}
							}										
							
							$shipping_product_arr = array();
							
							if($shipping_method!=''){
								if(!$qbo_is_shipping_allowed){
									if($smt_id>0){
										$smt_id_str = $shipping_method.':'.$smt_id;
										$shipping_product_arr = $this->get_mapped_shipping_product($smt_id_str,$sd_v,true);
									}
									
									if(!count($shipping_product_arr) || empty($shipping_product_arr['ItemRef'])){
										$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method,$sd_v);
									}
									
									if(empty($sp_arr_first)){
										$sp_arr_first = $shipping_product_arr;
									}
									
									$ShippingLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
									$ShippingLine->setItemListID($shipping_product_arr["ItemRef"]);
									if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
										$ShippingLine->setClassListID($shipping_product_arr["ClassRef"]);
									}
									$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';							
									if(!$this->check_sh_wcmslscqb_hash()){
										if($this->wacs_base_cur_enabled()){
											$shipping_description.= " ({$_order_currency} {$order_shipping_total})";
											$ShippingLine->setRate($order_shipping_total_base_currency);
											$ShippingLine->setAmount($order_shipping_total_base_currency);
										}else{
											$ShippingLine->setRate($order_shipping_total);								
											$ShippingLine->setAmount($order_shipping_total);
										}
									}else{
										$ShippingLine->setRate($sd_v['cost']);						
										$ShippingLine->setAmount($sd_v['cost']);
									}									
									
									//$ShippingLine->setQuantity(1);
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										$ShippingLine->setDesc($shipping_description);
									}
									
									if($qbo_is_sales_tax){
										if(($this->check_sh_wcmslscqb_hash() && $sd_v['total_tax']>0) || (!$this->check_sh_wcmslscqb_hash() && $_order_shipping_tax>0)){
											$TaxCodeRef = '';
											if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
												$TaxCodeRef =$qbo_tax_code;
											}
											
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}
											if(empty($TaxCodeRef)){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
											}
											
											if($TaxCodeRef!=''){
												$ShippingLine->setSalesTaxCodeListID($TaxCodeRef);
											}
										}else{
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
											}
											$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
										}
									}
									
									if(!$qbo_is_sales_tax){
										if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
											$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
										}else{
											$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
										}
										$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
									}
									
									if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											//$ShippingLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
										}
									}

									$Invoice->addInvoiceLine($ShippingLine);

								}
							}
							
							if(!$this->check_sh_wcmslscqb_hash()){
								break;
							}
						}
					}

					if(!$is_taxjar_active){
						//$order_shipping_total+=$_order_shipping_tax;
						if($this->wacs_base_cur_enabled()){
							//$order_shipping_total_base_currency+=$_order_shipping_tax_base_currency;
						}
					}
					
					//TaxJar Line
					if($is_taxjar_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
						$taxjar_item = $this->get_option('mw_wc_qbo_desk_wc_taxjar_map_qbo_product');
						if(empty($taxjar_item)){
							$taxjar_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($taxjar_item);
						$Description = 'TaxJar - QBD Line Item';
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Invoice->addInvoiceLine($ExtLine);
					}
					
					//Avatax Line
					if($is_avatax_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
						$avatax_item = $this->get_option('mw_wc_qbo_desk_wc_avatax_map_qbo_product');
						if(empty($avatax_item)){
							$avatax_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($avatax_item);
						$Description = 'Avatax - QBD Line Item';				
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Invoice->addInvoiceLine($ExtLine);
					}
					
					//Order Tax as Line Item					
					if($is_so_tax_as_li && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
						$otli_item = $this->get_option('mw_wc_qbo_desk_otli_qbd_product');
						if(empty($otli_item)){
							$otli_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($otli_item);
						
						$Description = '';
						if(is_array($tax_details) && count($tax_details)){
							if(isset($tax_details[0]['label'])){
								$Description = $tax_details[0]['label'];
							}
							
							if(isset($tax_details[1]) && $tax_details[1]['label']){
								if(!empty(tax_details[1]['label'])){
									$Description = $Description.', '.$tax_details[1]['label'];
								}
							}
						}
						
						if(empty($Description)){
							$Description = 'Woocommerce Order Tax - QBD Line Item';
						}
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Invoice->addInvoiceLine($ExtLine);
					}
					
					/**/
					$qbd_subtotal_product = $this->get_option('mw_wc_qbo_desk_default_subtotal_product');
					if(!empty($qbd_subtotal_product)){
						$StLine = new QuickBooks_QBXML_Object_Invoice_InvoiceLine();
						$StLine->setItemListID($qbd_subtotal_product);
						$Invoice->addInvoiceLine($StLine);
					}
					
					//
					if($is_tax_applied){
						$TaxCodeRef =$qbo_tax_code;
						if($TaxCodeRef!=''){
							if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')!= 'Sales_Tax_Codes'){
								$Invoice->setSalesTaxItemListID($TaxCodeRef);
							}							
						}
					}					
					
					//31-10-2017
					$is_as_addr_format = true;
					

					$billing_name = $this->get_array_isset($invoice_data,'_billing_first_name','',true).' '.$this->get_array_isset($invoice_data,'_billing_last_name','',true);
					$billing_name_fl = $billing_name;

					$country = $this->get_array_isset($invoice_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);
					//$country = '';
					
					$_billing_company = $this->get_array_isset($invoice_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($invoice_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($invoice_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($invoice_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($invoice_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
					
					/**/
					$skip_billing_address = false;
					if($this->option_checked('mw_wc_qbo_desk_use_qb_ba_for_eqc') && is_array($extra) && isset($extra['existing_qbo_user_id']) && !empty($extra['existing_qbo_user_id'])){
						$skip_billing_address = true;
					}
					
					if(!$skip_billing_address){
						if(!$is_as_addr_format){
							if($_billing_company!=''){
								$Invoice->setBillAddress(
									$billing_name,$_billing_company,$_billing_address_1,$_billing_address_2,'',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}else{
								$Invoice->setBillAddress(
									$billing_name,$_billing_address_1,$_billing_address_2,'','',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_billing_first_name','',true),$this->get_array_isset($invoice_data,'_billing_last_name','',true),$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
							$r_fa = $this->get_ord_baf_addrs($rfs_arr,$invoice_data);
							$Invoice->setBillAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}
					}					
					
					if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!='' || $this->get_array_isset($invoice_data,'_shipping_company','',true)!=''){
						$shipping_name = $this->get_array_isset($invoice_data,'_shipping_first_name','',true).' '.$this->get_array_isset($invoice_data,'_shipping_last_name','',true);

						$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
						$country = $this->get_country_name_from_code($country);
						
						$_shipping_company = $this->get_array_isset($invoice_data,'_shipping_company','',true);
						$_shipping_address_1 = $this->get_array_isset($invoice_data,'_shipping_address_1','',true);
						$_shipping_address_2 = $this->get_array_isset($invoice_data,'_shipping_address_2','',true);
						$_shipping_city = $this->get_array_isset($invoice_data,'_shipping_city','',true);
						$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
						$_shipping_postcode = $this->get_array_isset($invoice_data,'_shipping_postcode','',true);
						
						if(!$is_as_addr_format){
							if($_shipping_company!=''){
								$Invoice->setShipAddress(
									$shipping_name,$_shipping_company,$_shipping_address_1,$_shipping_address_2,'',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}else{
								$Invoice->setShipAddress(
									$shipping_name,$_shipping_address_1,$_shipping_address_2,'','',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_shipping_first_name','',true),$this->get_array_isset($invoice_data,'_shipping_last_name','',true),$_shipping_company,$_shipping_address_1,$_shipping_address_2,$_shipping_city,$_shipping_state,$_shipping_postcode,$country);
							$r_fa = $this->get_ord_saf_addrs($rfs_arr,$invoice_data);
							$Invoice->setShipAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}						
						
					}
					
					/*
					setShipAddress($addr1, $addr2 = '', $addr3 = '', $addr4 = '', $addr5 = '', $city = '', $state = '', $province = '', $postalcode = '', $country = '', $note = '')
					*/

					/*
					setBillAddress($addr1, $addr2 = '', $addr3 = '', $addr4 = '', $addr5 = '', $city = '', $state = '', $province = '', $postalcode = '', $country = '', $note = '')
					*/

					/*
					setShipMethodName
					setShipMethodListID
					setShipDate

					setPaymentMethodName
					setPaymentMethodListID
					
					setPONumber
					setSalesRepListID

					*/

					$Q_Memo = '';
					
					if($this->option_checked('mw_wc_qbo_desk_invoice_memo')){
						$Q_Memo = $customer_note;
					}
					
					if($this->option_checked('mw_wc_qbo_desk_cname_into_memo')){
						$cname_memo = $billing_name_fl;
						$Q_Memo = $cname_memo;
					}
					
					$Q_Memo = trim($Q_Memo);
					/*
					if($this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						if(!empty($Q_Memo)){
							$Q_Memo.= PHP_EOL . 'Order: '. $DocNumber;
						}else{
							$Q_Memo = 'Order: '. $DocNumber;
						}						
					}
					*/
					
					$Invoice->setMemo($Q_Memo);
					
					//New - Extra Fields
					$mw_wc_qbo_desk_ord_push_rep_othername = $this->get_option('mw_wc_qbo_desk_ord_push_rep_othername');
					if($mw_wc_qbo_desk_ord_push_rep_othername!=''){
						$Invoice->setSalesRepListID($mw_wc_qbo_desk_ord_push_rep_othername);
					}
					
					//WWLC CF SalesRep QBD SalesRep Map
					if($wc_cus_id>0 && $this->is_plugin_active('woocommerce-wholesale-lead-capture','woocommerce-wholesale-lead-capture.bootstrap') && $this->option_checked('mw_wc_qbo_desk_compt_wwlc_rf_srm_ed')){
						$wwlc_cf_rep_map = get_option('mw_wc_qbo_desk_wwlc_cf_rep_map');
						if(is_array($wwlc_cf_rep_map) && count($wwlc_cf_rep_map)){
							$wwlc_cf_rep = get_user_meta($wc_cus_id,'wwlc_cf_rep',true);
							if(!empty($wwlc_cf_rep)){
								if(isset($wwlc_cf_rep_map[$wwlc_cf_rep]) && !empty($wwlc_cf_rep_map[$wwlc_cf_rep])){
									$qbd_salesrep_id = $wwlc_cf_rep_map[$wwlc_cf_rep];
									$Invoice->setSalesRepListID($qbd_salesrep_id);
								}
							}
						}
					}
					
					//WCFE CF SalesRep QBD SalesRep Map
					if($this->is_plugin_active('woocommerce-checkout-field-editor') && $this->option_checked('mw_wc_qbo_desk_compt_wcfe_rf_srm_ed')){
						$wcfe_cf_rep_map = get_option('mw_wc_qbo_desk_wcfe_cf_rep_map');
						if(is_array($wcfe_cf_rep_map) && count($wcfe_cf_rep_map)){
							$wcfe_cf_rep = $this->get_array_isset($invoice_data,'sales-rep','');
							if(!empty($wcfe_cf_rep)){
								if(isset($wcfe_cf_rep_map[$wcfe_cf_rep]) && !empty($wcfe_cf_rep_map[$wcfe_cf_rep])){
									$qbd_salesrep_id = $wcfe_cf_rep_map[$wcfe_cf_rep];
									$Invoice->setSalesRepListID($qbd_salesrep_id);
								}
							}
						}
					}
					
					if($this->is_plugin_active('woocommerce-gateway-purchase-order') && $this->option_checked('mw_wc_qbo_desk_wpopg_po_support')){
						//if($_payment_method == 'woocommerce_gateway_purchase_order'){}
						$_po_number = $this->get_array_isset($invoice_data,'_po_number','',true);
						if($_po_number!=''){
							$Invoice->setPONumber($_po_number);
						}else{
							$Invoice->setPONumber('Web');
						}
					}
					
					$mw_wc_qbo_desk_ord_push_entered_by = $this->get_option('mw_wc_qbo_desk_ord_push_entered_by');
					if($mw_wc_qbo_desk_ord_push_entered_by!=''){
						$Invoice->setOther($mw_wc_qbo_desk_ord_push_entered_by);
					}
					
					
					if($this->wacs_base_cur_enabled()){					
						$base_currency = get_woocommerce_currency();
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
					}else{
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					}

					$term_id_str = $this->get_array_isset($pm_map_data,'term_id_str','',true);
					if($term_id_str!=''){
						$Invoice->set('TermsRef ListID',$term_id_str);
					}
					
					$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','',true);
					if($qb_p_method_id!=''){
						$Invoice->set('PaymentMethodRef ListID',$qb_p_method_id);
					}
					//$this->_p($shipping_product_arr);
					if(count($sp_arr_first)){
						$qb_shipmethod_id = $this->get_array_isset($sp_arr_first,'qb_shipmethod_id','',true);
						if($qb_shipmethod_id!=''){
							$Invoice->set('ShipMethodRef ListID',$qb_shipmethod_id);
						}
					}
					
					/**/
					$qb_ip_ar_acc_id = $this->get_array_isset($pm_map_data,'qb_ip_ar_acc_id','');
					if(!empty($qb_ip_ar_acc_id)){
						$Invoice->set('ARAccountRef ListID',$qb_ip_ar_acc_id);
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-sj-custom-shipping-field-mapping') && $this->check_sh_sj_csfm_hash() && $this->option_checked('mw_wc_qbo_desk_compt_np_sj_ocsf_map')){
						$_carrier_name = $this->get_array_isset($invoice_data,'_carrier_name','',true);
						$mw_wc_qbo_desk_compt_sjocsfm_mv = get_option('mw_wc_qbo_desk_compt_sjocsfm_mv');
						if(!empty($_carrier_name) && is_array($mw_wc_qbo_desk_compt_sjocsfm_mv) && isset($mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name])){
							$sj_cn_qb_sm_id = $mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name];
							if(!empty($sj_cn_qb_sm_id)){
								$Invoice->set('ShipMethodRef ListID',$sj_cn_qb_sm_id);
							}
						}
					}
					
					$cf_map_data = array();
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
						$cf_map_data = $this->get_cf_map_data();
					}
					
					if(is_array($cf_map_data) && count($cf_map_data)){
						$qacfm = $this->get_qbo_avl_cf_map_fields();
						foreach($cf_map_data as $wcfm_k => $wcfm_v){
							$wcfm_k = trim($wcfm_k);
							$wcfm_v = trim($wcfm_v);
							
							if(!empty($wcfm_v)){
								$wcf_val = '';
								switch ($wcfm_k) {									
									case "wc_order_shipping_method_name":
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
								
								if(!empty($wcf_val) && isset($qacfm[$wcfm_v])){
									$qbo_cf_arr = array();
									switch ($wcfm_v) {
										case "":								
											break;
											
										default:
										try {
											if(is_array($qbo_cf_arr) && count($qbo_cf_arr) && isset($qbo_cf_arr[$wcfm_v])){
												//
											}else{
												$qacfm_naf = $this->get_qbo_avl_cf_map_fields(true);
												$ivqf = true;
												if(is_array($qacfm_naf) && count($qacfm_naf) && isset($qacfm_naf[$wcfm_v])){
													$ivqf = false;
												}
												if($ivqf){
													$Invoice->set("{$wcfm_v}",$wcf_val);
												}else{
													if($wcfm_v == 'ShipTo'){
														$cfst = $this->get_fr_cf_ship_to($wcf_val);
														$Invoice->setShipAddress(
															$this->get_array_isset($cfst,0,'',true),
															$this->get_array_isset($cfst,1,'',true),
															$this->get_array_isset($cfst,2,'',true),
															$this->get_array_isset($cfst,3,'',true),
															$this->get_array_isset($cfst,4,'',true)
															
														);
													}
												}
											}
										}catch(Exception $e) {
											$cfm_err = $e->getMessage();
										}
									}
								}
							}
						}
					}
					
					/*Tracking Num Compatibility*/
					if($this->is_plugin_active('woocommerce-shipment-tracking') && $this->option_checked('mw_wc_qbo_desk_w_shp_track')){
						$_wc_shipment_tracking_items = $this->get_array_isset($invoice_data,'_wc_shipment_tracking_items','',true);
						
						$wf_wc_shipment_source = $this->get_array_isset($invoice_data,'wf_wc_shipment_source','',true);
						$wf_wc_shipment_result = $this->get_array_isset($invoice_data,'wf_wc_shipment_result','',true);
						
						if($_wc_shipment_tracking_items!='' || $wf_wc_shipment_source!=''){
							if($_wc_shipment_tracking_items!=''){
								$wsti_data = $this->wc_get_wst_data($_wc_shipment_tracking_items);
							}else{
								$wsti_data = $this->wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result);
							}
							if(count($wsti_data)){
								$tracking_provider = $this->get_array_isset($wsti_data,'tracking_provider','',true);
								$tracking_number = $this->get_array_isset($wsti_data,'tracking_number','',true);
								$date_shipped = $this->get_array_isset($wsti_data,'date_shipped','',true);
								if($tracking_provider!=''){
									$wst_tp_qsm_mp = get_option('mw_wc_qbo_desk_compt_wshptrack_tp_mv');
									if(is_array($wst_tp_qsm_mp) && isset($wst_tp_qsm_mp[$tracking_provider]) && !empty($wst_tp_qsm_mp[$tracking_provider])){
										$qb_sm_id = $wst_tp_qsm_mp[$tracking_provider];
										$Invoice->set('ShipMethodRef ListID',$qb_sm_id);
									}									
								}
								$Invoice->set('Other',$tracking_number);
								$Invoice->set('ShipDate',$date_shipped);
							}
						}
					}
					
					//
					$exchange_rate = '';					
					if(!empty($_order_currency) && isset($invoice_data['_woocs_order_base_currency']) && $invoice_data['_woocs_order_base_currency'] == $_order_currency){
						//$exchange_rate = 1.82;
					}					
					
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$fb_cust_term = $this->get_array_isset($invoice_data,'net_terms','');							
							$fb_cust_term = $this->sanitize($fb_cust_term);
							if(!empty($fb_cust_term)){
								$tm_r = $this->get_row($wpdb->prepare("SELECT qbd_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term` WHERE name = %s",$fb_cust_term));
								if(is_array($tm_r) && !empty($tm_r) && isset($tm_r['qbd_id']) && !empty($tm_r['qbd_id'])){
									$qbd_term_id = $tm_r['qbd_id'];
									$Invoice->set('TermsRef ListID',$qbd_term_id);
								}
							}
						}
					}
					
					//$this->_p($Invoice);
					
					$qbxml = $Invoice->asQBXML(QUICKBOOKS_ADD_INVOICE,null,$this->get_qbxml_locale());					
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
					$qbxml = $this->qbxml_search_replace($qbxml);
					
					if($exchange_rate != ''){
						$qbxml = str_replace('</TxnDate>','</TxnDate>'. PHP_EOL .'		<ExchangeRate>1.82</ExchangeRate>',$qbxml);
					}			
					
					return $qbxml;

				}
			}
		}
	}
	
	private function get_fr_cf_ship_to($st){
		$st_a = array('','','','','');
		if(empty($st)){
			return $st_a;
		}
		
		$st = explode(PHP_EOL, $st);
		if(is_array($st) && !empty($st)){
			$st_a = $st;
		}
		
		return $st_a;
	}
	
	public function format_xml($xml){
		$xml = trim($xml);		
		if(!empty($xml)){
			$dom = new DOMDocument;
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadXML($xml);
			$dom->formatOutput = TRUE;
			$xml = $dom->saveXml();
		}
		return $xml;
	}
	
	/**/
	public function GetOrderDataExtAddQbxml($order_id,$extra){
		if($this->is_qwc_connected()){
			$order_id = intval($order_id);
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$qbxml = '';
			if($order_id>0 && is_array($extra) && !empty($extra) && isset($extra['Qos_Type']) && !empty($extra['Qos_Type'])){
				if(isset($extra['TxnID']) && !empty($extra['TxnID'])){
					$order = get_post($order_id);
					$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
					if(is_array($invoice_data) && count($invoice_data)){
						if($this->check_cf_map_data_ext_field_value_exists($order_id,$extra['Qos_Type'])){
							$cf_map_data = array();
							if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash() && $order_id > 0){
								$cf_map_data = $this->get_cf_map_data();
							}
							
							if(is_array($cf_map_data) && !empty($cf_map_data)){
								$qacfm = $this->get_qbo_avl_cf_map_fields();
								if(is_array($qacfm) && !empty($qacfm)){
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
												$DataExt = new QuickBooks_QBXML_Object_DataExt();
												$DataExt->setOwnerID($this->get_owner_id());
												$DataExt->setDataExtName($wcfm_v);
												$DataExt->set('DataExtType',$this->qbd_dext_type_str());
												$DataExt->setDataExtValue($wcf_val);
												
												$DataExt->setTxnDataExtType($extra['Qos_Type']);
												$DataExt->setTxnID($extra['TxnID']);
												//$this->_p($DataExt);
												
												$qbxml_tmp = $DataExt->asQBXML(QUICKBOOKS_MOD_DATAEXT,null,$this->get_qbxml_locale());
												/*
												if (strpos($qbxml_tmp, '<DataExtType>') === false) {
													$qbxml_tmp = str_replace('</OwnerID>', '</OwnerID>' .PHP_EOL .'<DataExtType>'.$this->qbd_dext_type_str().'</DataExtType>', $qbxml_tmp);
												}
												*/												
												$qbxml.= $qbxml_tmp;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			if(!empty($qbxml)){
				$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
			}
			
			return $qbxml;
		}
	}
	
	/*SalesReceipt*/	
	public function GetSalesReceiptQbxml_GPI($order_id){		
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->_p($invoice_data);
			if(is_array($invoice_data) && count($invoice_data)){
				//$q_whr = " AND `qb_status` = 'q' ";
				$q_whr = '';
				$queue_data = $this->get_row("SELECT * FROM `quickbooks_queue` WHERE `qb_action` = 'SalesReceiptMod_GPI' {$q_whr} AND `ident` = {$order_id} ORDER BY `quickbooks_queue_id` DESC");
				
				if(is_array($queue_data) && count($queue_data)){
					$extra = $queue_data['extra'];
					if(!empty($extra)){
						$extra = @unserialize($extra);
						if(is_array($extra) && count($extra)){
							$xml = $queue_data['qbxml'];
							$xml_c = '';
							if(!empty($xml)){
								//echo $xml;
								$errnum = 0;
								$errmsg = '';
								$Parser = new QuickBooks_XML_Parser($xml);
								
								$gp_li_dtls_arr = array();
								$gp_li_dtls_arr_c = array();
								
								$np_li_dtls_arr = array();
								
								if ($Doc = $Parser->parse($errnum, $errmsg)){
									$Root = $Doc->getRoot();
									$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/SalesReceiptAddRs');
									foreach ($List->children() as $Item){										
										$xml_c = $Item->asXML();
										$ret = $Item->name();										
										if(count($Item->children())){
											foreach ($Item->children() as $Item_c){
												$ret_c = $Item_c->name();
												if($ret_c == 'SalesReceiptLineGroupRet'){
													$gp_tmp_arr = array();
													$gp_tmp_arr['TxnLineID'] = $Item_c->getChildDataAt($ret_c . ' TxnLineID');
													$gp_tmp_arr['ItemGroupRef_ListID'] = $Item_c->getChildDataAt($ret_c . ' ItemGroupRef ListID');
													$gp_tmp_arr['TotalAmount'] = $Item_c->getChildDataAt($ret_c . ' TotalAmount');
													$gp_tmp_arr['Quantity'] = $Item_c->getChildDataAt($ret_c . ' Quantity');
													
													$gp_li_dtls_arr[] = $gp_tmp_arr;
													
													$TxnLineID_Gl = $Item_c->getChildDataAt($ret_c . ' TxnLineID');
													if(count($Item_c->children())){
														foreach ($Item_c->children() as $Item_c_c){
															$ret_c_c = $Item_c_c->name();
															if($ret_c_c == 'SalesReceiptLineRet'){
																$gp_tmp_arr_c = array();
																$gp_tmp_arr_c['TxnLineID'] = $Item_c_c->getChildDataAt($ret_c_c . ' TxnLineID');
																$gp_tmp_arr_c['ItemRef_ListID'] = $Item_c_c->getChildDataAt($ret_c_c . ' ItemRef ListID');
																$gp_tmp_arr_c['Quantity'] = $Item_c_c->getChildDataAt($ret_c_c . ' Quantity');
																$gp_tmp_arr_c['Rate'] = $Item_c_c->getChildDataAt($ret_c_c . ' Rate');
																$gp_tmp_arr_c['Amount'] = $Item_c_c->getChildDataAt($ret_c_c . ' Amount');
																
																//
																$gp_tmp_arr_c['InventorySiteRef_ListID'] = $Item_c_c->getChildDataAt($ret_c_c . ' InventorySiteRef ListID');
																
																$gp_tmp_arr_c['SalesTaxCodeRef_ListID'] = $Item_c_c->getChildDataAt($ret_c_c . ' SalesTaxCodeRef ListID');
																
																$gp_li_dtls_arr_c[$TxnLineID_Gl][] = $gp_tmp_arr_c;
																
															}
														}
													}
												}
												
												if($ret_c == 'SalesReceiptLineRet'){
													$np_tmp_arr = array();
													$np_tmp_arr['TxnLineID'] = $Item_c->getChildDataAt($ret_c . ' TxnLineID');
													
													$np_li_dtls_arr[] = $np_tmp_arr;
												}
											}
										}
									}									
								}
								
								//$this->_p($gp_li_dtls_arr);
								//$this->_p($gp_li_dtls_arr_c);
								
								//
								$gi_last_li_data = array();
								$adjust_last_gi_price = false;
								
								$gp_li_pd_arr = array();
								if(is_array($gp_li_dtls_arr) && !empty($gp_li_dtls_arr) && is_array($gp_li_dtls_arr_c) && !empty($gp_li_dtls_arr_c)){
									$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
									if(is_array($qbo_inv_items) && !empty($qbo_inv_items)){
										foreach($qbo_inv_items as $qbo_item){
											if($qbo_item['qbo_product_type'] == 'Group'){
												$UnitPrice = $qbo_item["UnitPrice"];
												$Qty = $qbo_item["Qty"];
												$Amount = $Qty*$UnitPrice;
												
												foreach($gp_li_dtls_arr as $gld){
													if($qbo_item["ItemRef"] == $gld['ItemGroupRef_ListID'] && $Qty == $gld['Quantity']){
														$wc_b_price = $Amount;
														$qbo_b_tp = $gld['TotalAmount'];
														$gp_p_diff = ($wc_b_price-$qbo_b_tp);
														if($gp_p_diff!=0){
															$gp_li_pd_arr[$gld['TxnLineID']] = $gp_p_diff;
														}
													}
												}												
											}											
										}
									}
								}								
								
								if(is_array($gp_li_pd_arr) && count($gp_li_pd_arr)){
									$SalesReceipt = new QuickBooks_QBXML_Object_SalesReceipt();
									$SalesReceipt->set('TxnID',$extra['TxnID']);									
									
									$SalesReceipt->set('EditSequence',$extra['EditSequence']);
									
									if(is_array($np_li_dtls_arr) && !empty($np_li_dtls_arr)){
										foreach($np_li_dtls_arr as $nld){
											$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
											$SalesReceiptLine->set('TxnLineID',$nld['TxnLineID']);
											$SalesReceipt->addSalesReceiptLine($SalesReceiptLine);
											
										}
									}
									
									foreach($gp_li_dtls_arr as $gld){										
										if(isset($gp_li_dtls_arr_c[$gld['TxnLineID']]) && !empty($gp_li_dtls_arr_c[$gld['TxnLineID']])){
											$SalesReceiptLineGroup = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLineGroup();
											$SalesReceiptLineGroup->set('TxnLineID',$gld['TxnLineID']);
											$gc_lines = array();
											
											$gi_last_key = (count($gp_li_dtls_arr_c[$gld['TxnLineID']]) - 1);
											foreach($gp_li_dtls_arr_c[$gld['TxnLineID']] as $gld_c_k => $gld_c_v){
												$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
												$SalesReceiptLine->set('TxnLineID',$gld_c_v['TxnLineID']);
												//
												if($gld_c_k == $gi_last_key){													
													$gi_last_li_data['ItemRef_ListID'] = $gld_c_v['ItemRef_ListID'];
													$gi_last_li_data['InventorySiteRef_ListID'] = $gld_c_v['InventorySiteRef_ListID'];
													$gi_last_li_data['SalesTaxCodeRef_ListID'] = $gld_c_v['SalesTaxCodeRef_ListID'];
													
													if($adjust_last_gi_price && isset($gp_li_pd_arr[$gld['TxnLineID']])){
														$gp_p_diff = $gp_li_pd_arr[$gld['TxnLineID']];
														$Quantity = ($gld_c_v['Quantity']!='')?$gld_c_v['Quantity']:0;
														$A_Rate = ($gp_p_diff/$Quantity);
														$A_Rate = $this->qbd_limit_decimal_points($A_Rate);
														
														$N_Rate = ($gld_c_v['Rate']+$A_Rate);
														$N_Amount = $N_Rate*$Quantity;
														
														$SalesReceiptLine->setItemListID($gld_c_v['ItemRef_ListID']);
														$SalesReceiptLine->setRate($N_Rate);
														$SalesReceiptLine->setQuantity($Quantity);
														$SalesReceiptLine->setAmount($N_Amount);
														
														if(!empty($gld_c_v['InventorySiteRef_ListID'])){
															$SalesReceiptLine->set('InventorySiteRef ListID',$gld_c_v['InventorySiteRef_ListID']);
														}
														
														if(!empty($gld_c_v['SalesTaxCodeRef_ListID'])){
															$SalesReceiptLine->set('SalesTaxCodeRef ListID',$gld_c_v['SalesTaxCodeRef_ListID']);
														}
													}
												}
												
												$gc_lines[] = $SalesReceiptLine;												
											}
											
											//
											if(!$adjust_last_gi_price && isset($gp_li_pd_arr[$gld['TxnLineID']])){
												$gp_p_diff = $gp_li_pd_arr[$gld['TxnLineID']];
												$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
												//
												//$SalesReceiptLine->set('TxnLineID','0');
												
												$gp_pa_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
												if(isset($gi_last_li_data['ItemRef_ListID'])){
													//$gp_pa_item = $gi_last_li_data['ItemRef_ListID'];
												}
												
												$SalesReceiptLine->setItemListID($gp_pa_item);
												$SalesReceiptLine->setDesc('Group Product Price Adjustment');
												
												$SalesReceiptLine->setQuantity(1);
												$SalesReceiptLine->setRate($gp_p_diff);
												
												$SalesReceiptLine->setAmount($gp_p_diff);
												
												if(isset($gi_last_li_data['InventorySiteRef_ListID'])){
													//$SalesReceiptLine->set('InventorySiteRef ListID',$gi_last_li_data['InventorySiteRef_ListID']);
												}
												
												if(isset($gi_last_li_data['SalesTaxCodeRef_ListID'])){
													//$SalesReceiptLine->set('SalesTaxCodeRef ListID',$gi_last_li_data['SalesTaxCodeRef_ListID']);
												}
												
												$gc_lines[] = $SalesReceiptLine;
												
											}
											
											//$gc_lines = $SalesReceiptLineGroup->get('SalesReceiptLine');
											//$SalesReceiptLineGroup->set('SalesReceiptLine',$gc_lines);
											$SalesReceiptLineGroup->set('SalesReceiptLineMod',$gc_lines);
											$SalesReceipt->addSalesReceiptLineGroup($SalesReceiptLineGroup);
										}
									}
									
									/*
									if(!empty($xml_c)){
										$Object = QuickBooks_QBXML_Object::fromQBXML($xml_c, QUICKBOOKS_OBJECT_SALESRECEIPT);
										$this->_p($Object);
									}
									*/
									
									//$this->_p($SalesReceipt);
									$qbxml = $SalesReceipt->asQBXML(QUICKBOOKS_MOD_SALESRECEIPT,null,$this->get_qbxml_locale());
									$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
									$qbxml = $this->qbxml_search_replace($qbxml);
									//
									if(!empty($qbxml)){										
										$qbxml = $this->sr_gpi_pxml($qbxml);
									}
									
									return $qbxml;
								}
							}							
						}
					}					
				}				
			}
		}
	}
	
	private function sr_gpi_pxml($xml){
		$search = array('<SalesReceiptLine>', '</SalesReceiptLine>');
		$replace = array('<SalesReceiptLineMod>', '</SalesReceiptLineMod>');
		$xml = str_replace($search, $replace, $xml);
		return $xml;
	}
	
	public function GetSalesReceiptQbxml($order_id,$extra=null){
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->add_test_log(print_r($invoice_data,true));
			//$this->_p($invoice_data);
	
			if(is_array($invoice_data) && count($invoice_data)){
				global $wpdb;
				$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
				$qbo_cus_id = '';

				$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
				$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
				
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) $this->get_array_isset($invoice_data,'account_number','');
						if($c_account_number > 0){
							$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
				if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
							if(empty($ord_country)){
								$ord_country = $this->get_array_isset($invoice_data,'_billing_country','',true);
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
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
					if($wc_cus_id>0){						
						$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
					}else{						
						//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
						$shipping_country = $this->get_array_isset($invoice_data,'_shipping_country','');
					}
					
					if($shipping_country == 'US'){
						if($wc_cus_id>0){
							$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
						}else{
							//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
							$shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','');
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
						$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}
				
				if(empty($qbo_cus_id)){
					if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
						if($wc_cus_id>0){
							//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
							if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
							}else{
								$customer_data = $this->get_wc_customer_info($wc_cus_id);
							}
							
							$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
						}else{
							$customer_data = $this->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
						}
					}else{
						/*
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}							
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							
						}else{
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
						}
						*/
						
						/**/
						$wc_user_role = '';
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
								}
							}else{
								$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
							}
						}
						//
					}
				}				

				if(empty($qbo_cus_id)){
					$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$order_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
					return false;
				}

				if($qbo_cus_id!=''){
					$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
					$SalesReceipt = new QuickBooks_QBXML_Object_SalesReceipt();

					//$SalesReceipt->setCustomerName();
					$SalesReceipt->setCustomerListID($qbo_cus_id);
					
					if(!$this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						$SalesReceipt->setRefNumber($DocNumber);
					}
					
					/**/
					$TemplateRef = $this->get_array_isset($invoice_data,'TemplateRef','');
					if(!empty($TemplateRef)){
						$SalesReceipt->set('TemplateRef ListID', $TemplateRef);
					}
					
					$inv_sr_txn_class = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
					if($inv_sr_txn_class!=''){
						$SalesReceipt->setClassListID($inv_sr_txn_class);
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_desk_qbo_push_invoice_is_print_true')){
						$SalesReceipt->set('IsToBePrinted',1);
					}
					
					/**/
					$customer_note = $this->get_array_isset($invoice_data,'customer_note','');
					//$SalesReceipt->set('PONumber',$customer_note);
					
					//PO
					if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $this->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){
						if(!empty($this->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor'))){
							$DocNumber_Po = 'PO-'.$DocNumber;
							//
							$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
							if($this->chk_is_po_add($qbo_inv_items)){
								$SalesReceipt->set('PONumber',$DocNumber_Po);
							}							
						}
					}
					
					//NP Billing State - QBD Class Map					
					if($this->option_checked('mw_wc_qbo_desk_compt_np_bus_qbc_map_ed')){
						$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
						if(!empty($_billing_state)){
							$bus_qbc_map = get_option('mw_wc_qbo_desk_np_bill_us_st_qb_cl_map');
							if(is_array($bus_qbc_map) && count($bus_qbc_map)){
								if(isset($bus_qbc_map[$_billing_state]) && !empty($bus_qbc_map[$_billing_state])){
									$qbd_classid = $bus_qbc_map[$_billing_state];
									$SalesReceipt->setClassListID($qbd_classid);
								}
							}
						}						
					}
					
					$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
					$wc_inv_date = $this->format_date($wc_inv_date);
					if($wc_inv_date!=''){
						$SalesReceipt->setTxnDate($wc_inv_date);
					}

					$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
					$wc_inv_due_date = $this->format_date($wc_inv_due_date);
					if($wc_inv_due_date!=''){
						$SalesReceipt->setDueDate($wc_inv_due_date);
					}
					
					
					$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
					
					/*Count Total Amounts*/
					$_cart_discount = $this->get_array_isset($invoice_data,'_cart_discount',0);
					$_cart_discount_tax = $this->get_array_isset($invoice_data,'_cart_discount_tax',0);

					$_order_tax = (float) $this->get_array_isset($invoice_data,'_order_tax',0);
					$_order_shipping_tax = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax',0);
					$_order_total_tax = ($_order_tax+$_order_shipping_tax);
					
					$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);

					/*Qbd settings*/
					$qbo_is_sales_tax = true;
					$qbo_company_country = 'US';
					$qbo_is_shipping_allowed = false;

					/*Tax rates*/
					$qbo_tax_code = '';
					$apply_tax = false;
					$is_tax_applied = false;
					$is_inclusive = false;

					$qbo_tax_code_shipping = '';

					$tax_rate_id = 0;
					$tax_rate_id_2 = 0;

					$tax_details = (isset($invoice_data['tax_details']))?$invoice_data['tax_details']:array();
					
					//Tax Totals From tax Lines
					$calc_order_tax_totals_from_tax_lines = true;					
					if($calc_order_tax_totals_from_tax_lines){
						$_order_tax = 0;
						$_order_shipping_tax = 0;
						$_order_total_tax = 0;
						if(count($tax_details)){
							foreach($tax_details as $td){
								$_order_tax+=$td['tax_amount'];
								$_order_shipping_tax+=$td['shipping_tax_amount'];
								$_order_total_tax+=$td['tax_amount']+$td['shipping_tax_amount'];
							}
						}
					}
					
					$_order_total_tax = $this->qbd_limit_decimal_points($_order_total_tax);
					
					//TaxJar Settings
					$is_taxjar_active = false;
					$woocommerce_taxjar_integration_settings = get_option('woocommerce_taxjar-integration_settings');
					$wc_taxjar_enable_tax_calculation = 0;
					if(is_array($woocommerce_taxjar_integration_settings) && count($woocommerce_taxjar_integration_settings)){
						if(isset($woocommerce_taxjar_integration_settings['enabled']) && $woocommerce_taxjar_integration_settings['enabled']=='yes'){
							$wc_taxjar_enable_tax_calculation = 1;
						}
					}
					
					if($this->is_plugin_active('taxjar-simplified-taxes-for-woocommerce','taxjar-woocommerce') && $this->option_checked('mw_wc_qbo_desk_wc_taxjar_support') && $wc_taxjar_enable_tax_calculation=='1'){
						$is_taxjar_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//Avatax Settings
					$is_avatax_active = false;
					$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
					if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_desk_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
						$is_avatax_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//
					$is_so_tax_as_li = false;
					if($this->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
						$is_so_tax_as_li = true;
						$qbo_is_sales_tax = false;
					}

					if($qbo_is_sales_tax){
						if(count($tax_details)){
							$tax_rate_id = $tax_details[0]['rate_id'];
						}

						if(count($tax_details)>1){
							if($tax_details[1]['tax_amount']>0){
								$tax_rate_id_2 = $tax_details[1]['rate_id'];
							}
						}

						/*
						if(count($tax_details)>1 && $qbo_is_shipping_allowed){
							foreach($tax_details as $td){
								if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
									$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
									break;
								}
							}
						}
						*/

						$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
						if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
							$apply_tax = true;
						}

						//$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
						$is_qbo_dual_tax = false;

						/*
						if(count($Tax_Code_Details)){
							if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
								$is_qbo_dual_tax = true;
							}
						}

						$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
						$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

						$NetAmountTaxable = 0;

						if($is_qbo_dual_tax){
							$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
							$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
							$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
							$NetAmountTaxable_2 = 0;
						}
						*/

						/*
						if($qbo_tax_code_shipping!=''){
							$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
							$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
							$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
							$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
							$NetAmountTaxable_Shipping = 0;
						}
						*/

						$_prices_include_tax = $this->get_array_isset($invoice_data,'_prices_include_tax','no',true);
						if($qbo_is_sales_tax){
							$tax_type = $this->get_tax_type($_prices_include_tax);
							$is_inclusive = $this->is_tax_inclusive($tax_type);
						}
					}


					$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
					
					$is_bundle_order = false;
					$map_bundle_support = false;
					
					if(!$is_bundle_order){
						if(is_array($qbo_inv_items) && count($qbo_inv_items)){
							foreach($qbo_inv_items as $qbo_item){
								if($qbo_item['qbo_product_type'] == 'Group'){
									$map_bundle_support = true;
									$SalesReceiptLineGroup = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLineGroup();
									$SalesReceiptLineGroup->setItemListID($qbo_item["ItemRef"]);
									$Description = $qbo_item['Description'];
									$Qty = $qbo_item["Qty"];									
									$SalesReceiptLineGroup->setQuantity($Qty);
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										//$SalesReceiptLineGroup->setDesc($Description);
									}
									//TotalAmount
									$SalesReceipt->addSalesReceiptLineGroup($SalesReceiptLineGroup);
								}
							}
						}
					}
					
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							
							if($map_bundle_support && $qbo_item['qbo_product_type'] == 'Group'){
								continue;
							}
							
							$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
							//$SalesReceiptLine->setItemName($qbo_item['Description']);
							$SalesReceiptLine->setItemListID($qbo_item["ItemRef"]);
							if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
								//$SalesReceiptLine->setClassListID($qbo_item["ClassRef"]);
							}
							
							$Description = $qbo_item['Description'];
							if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
								$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
								$li_sku = get_post_meta( $li_item_id, '_sku', true );
								if($li_sku!=''){
									$Description.=' ('.$li_sku.')';
								}
							}
							
							//Extra Description
							if(isset($qbo_item["Qbd_Ext_Description"])){
								$Description.= $qbo_item["Qbd_Ext_Description"];
							}
							
							if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
								$SalesReceiptLine->setDesc($Description);
							}							
							
							$UnitPrice = $qbo_item["UnitPrice"];
							$Qty = $qbo_item["Qty"];
							$Amount = $Qty*$UnitPrice;
							$SalesReceiptLine->setRate($UnitPrice);
							$SalesReceiptLine->setQuantity($Qty);
							$SalesReceiptLine->setAmount($Amount);
							
							if($this->option_checked('mw_wc_qbo_desk_compt_wqclns_ed')){
								$LotNumber  = $this->get_array_isset($qbo_item,'lot','');
								if(!empty($LotNumber)){
									$SalesReceiptLine->set('LotNumber',$LotNumber);
								}
							}
							
							/*
							SerialNumber
							LotNumber
							ServiceDate
							*/

							if($qbo_is_sales_tax){
								if($apply_tax && $qbo_item["Taxed"]){
									$is_tax_applied = true;
									/*$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;*/
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}

									/*
									if($is_inclusive){
										$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
									}
									*/
									if($TaxCodeRef!=''){
										$SalesReceiptLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$SalesReceiptLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$SalesReceiptLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							/**/
							$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							if($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								/*
								$mw_warehouse = 0;
								if(isset($qbo_item["_order_item_wh"])){
									$mw_warehouse = (int) $qbo_item["_order_item_wh"];
								}else{
									$mw_warehouse = (int) $this->get_array_isset($invoice_data,'mw_warehouse',0);
								}
								*/
								
								$mw_warehouse = $this->get_mwr_oiw_mw_idls($qbo_item, $invoice_data);
								
								if($mw_warehouse > 0){
									$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
									if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && isset($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse])){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = trim($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse]);
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											if($this->is_inv_site_bin_allowed()){
												if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
													$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
													if(is_array($site_bin_arr) && !empty($site_bin_arr)){
														$SalesReceiptLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
														if(isset($site_bin_arr[1])){
															$SalesReceiptLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
														}
													}
												}
											}else{
												$SalesReceiptLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											}									
										}
									}
								}
							}
							
							if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){									
									if($this->is_inv_site_bin_allowed()){
										if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
											$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											if(is_array($site_bin_arr) && !empty($site_bin_arr)){
												$SalesReceiptLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
												if(isset($site_bin_arr[1])){
													$SalesReceiptLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
												}
											}
										}
									}else{
										$SalesReceiptLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
							}
							
							/**/
							if($this->option_checked('mw_wc_qbo_desk_compt_np_liqtycustcolumn_ed') && $this->check_sh_liqtycustcolumn_hash()){
								$cqtyf = $this->get_option('mw_wc_qbo_desk_compt_np_liqtycustcolumn_cqtyf');
								if(empty($cqtyf)){
									$cqtyf = 'Other1';
								}
								$SalesReceiptLine->set($cqtyf , $Qty);
							}
							
							$SalesReceipt->addSalesReceiptLine($SalesReceiptLine);
						}
					}
					
					//pgdf compatibility
					if($this->get_wc_fee_plugin_check()){
						$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
						if(is_array($dc_gt_fees) && count($dc_gt_fees)){
							foreach($dc_gt_fees as $df){
								$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
								
								$UnitPrice = $df['_line_total'];
								$Qty = 1;
								$Amount = $Qty*$UnitPrice;
								
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$invoice_data);
								$SalesReceiptLine->setItemListID($df_ItemRef);
								
								$SalesReceiptLine->setRate($UnitPrice);
								$SalesReceiptLine->setQuantity($Qty);
								$SalesReceiptLine->setAmount($Amount);
								
								$SalesReceiptLine->setDesc($df['name']);
								
								$_line_tax = $df['_line_tax'];
								if($_line_tax && $qbo_is_sales_tax){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$SalesReceiptLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$SalesReceiptLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
								
								/*
								if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
									$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
									if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
										$SalesReceiptLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
								*/
								
								$SalesReceipt->addSalesReceiptLine($SalesReceiptLine);
							}
							
						}						
					}
					
					//pw_gift_card compatibility
					if($this->is_plugin_active('pw-woocommerce-gift-cards','pw-gift-cards') && $this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed') && !empty($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'))){
						$pw_gift_card = (isset($invoice_data['pw_gift_card']))?$invoice_data['pw_gift_card']:array();
						if(is_array($pw_gift_card) && count($pw_gift_card)){
							foreach($pw_gift_card as $pgc){
								$pgc_amount = $pgc['amount'];
								if($pgc_amount > 0){
									$pgc_amount = -1 * abs($pgc_amount);
								}
								
								$Qty = 1;
								$Description = $pgc['card_number'];
								$SalesReceiptLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
								$SalesReceiptLine->setItemListID($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'));
								$SalesReceiptLine->setRate($pgc_amount);
								$SalesReceiptLine->setQuantity($Qty);
								$SalesReceiptLine->setAmount($pgc_amount);
								
								$SalesReceiptLine->setDesc($Description);
								
								/*
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$SalesReceiptLine->setSalesTaxCodeListID($zero_rated_tax_code);
								*/
								
								$SalesReceipt->addSalesReceiptLine($SalesReceiptLine);
							}
						}
					}
					
					/*Add SalesReceipt Coupons*/
					$used_coupons  = (isset($invoice_data['used_coupons']))?$invoice_data['used_coupons']:array();
					$qbo_is_discount_allowed = true;
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						$qbo_is_discount_allowed = false;
					}
					
					if($qbo_is_discount_allowed && count($used_coupons)){
						foreach($used_coupons as $coupon){
							$coupon_name = $coupon['name'];
							$coupon_discount_amount = $coupon['discount_amount'];
							$coupon_discount_amount = -1 * abs($coupon_discount_amount);
							$coupon_discount_amount_tax = $coupon['discount_amount_tax'];

							$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
							$DiscountLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
							$DiscountLine->setItemListID($coupon_product_arr["ItemRef"]);
							if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
								//$DiscountLine->setClassListID($coupon_product_arr["ClassRef"]);
							}
							$DiscountLine->setDesc($coupon_product_arr['Description']);
							$DiscountLine->setRate($coupon_discount_amount);
							
							if($coupon_product_arr['qbo_product_type'] != 'Discount'){
								//$DiscountLine->setQuantity(1);
								$DiscountLine->setAmount($coupon_discount_amount);
							}							
							
							if($qbo_is_sales_tax){
								if($coupon_discount_amount_tax > 0 || $is_tax_applied){
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$DiscountLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}								
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									//$DiscountLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
								}
							}

							$SalesReceipt->addSalesReceiptLine($DiscountLine);
						}
					}					
					
					/*Add SalesReceipt Shipping*/
					$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
					
					$sp_arr_first = array();					
					if(is_array($shipping_details) && !empty($shipping_details)){
						foreach($shipping_details as $sd_k => $sd_v){
							$shipping_method = '';
							$shipping_method_name = '';
							$shipping_taxes = '';
							$smt_id = 0;
							if(isset($shipping_details[$sd_k])){
								if($this->get_array_isset($shipping_details[$sd_k],'type','')=='shipping'){
									$shipping_method_id = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
									if($shipping_method_id!=''){
										if(isset($shipping_details[$sd_k]['instance_id']) && $shipping_details[$sd_k]['instance_id']>0){
											$shipping_method = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
											$smt_id = (int) $this->get_array_isset($shipping_details[$sd_k],'instance_id',0);
										}else{
											$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'',$sd_v);
											$smt_id = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'id',$sd_v);
										}
									}
									
									$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
									$shipping_method_name =  $this->get_array_isset($shipping_details[$sd_k],'name','',true,30);
									$shipping_taxes = $this->get_array_isset($shipping_details[$sd_k],'taxes','');
								}
							}					
							
							$shipping_product_arr = array();
							
							if($shipping_method!=''){
								if(!$qbo_is_shipping_allowed){
									if($smt_id>0){
										$smt_id_str = $shipping_method.':'.$smt_id;
										$shipping_product_arr = $this->get_mapped_shipping_product($smt_id_str,$sd_v,true);
									}
									
									if(!count($shipping_product_arr) || empty($shipping_product_arr['ItemRef'])){
										$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method,$sd_v);
									}
									
									if(empty($sp_arr_first)){
										$sp_arr_first = $shipping_product_arr;
									}
									
									$ShippingLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
									$ShippingLine->setItemListID($shipping_product_arr["ItemRef"]);
									if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
										//$ShippingLine->setClassListID($shipping_product_arr["ClassRef"]);
									}
									$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										$ShippingLine->setDesc($shipping_description);
									}
									
									//$ShippingLine->setQuantity(1);
									if(!$this->check_sh_wcmslscqb_hash()){
										$ShippingLine->setRate($order_shipping_total);
										$ShippingLine->setAmount($order_shipping_total);
									}else{
										$ShippingLine->setRate($sd_v['cost']);						
										$ShippingLine->setAmount($sd_v['cost']);
									}									
									
									if($qbo_is_sales_tax){
										if(($this->check_sh_wcmslscqb_hash() && $sd_v['total_tax']>0) || (!$this->check_sh_wcmslscqb_hash() && $_order_shipping_tax>0)){
											$TaxCodeRef = '';
											if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
												$TaxCodeRef =$qbo_tax_code;
											}									
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}
											if(empty($TaxCodeRef)){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
											}
											
											if($TaxCodeRef!=''){
												$ShippingLine->setSalesTaxCodeListID($TaxCodeRef);
											}
										}else{
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
											}									
											$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
										}
									}
									
									if(!$qbo_is_sales_tax){
										if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
											$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
										}else{
											$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
										}
										$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
									}
									
									if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											//$ShippingLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
										}
									}

									$SalesReceipt->addSalesReceiptLine($ShippingLine);

								}
							}
							
							if(!$this->check_sh_wcmslscqb_hash()){
								break;
							}
						}
					}
					
					if(!$is_taxjar_active){
						//$order_shipping_total+=$_order_shipping_tax;
					}
					
					//TaxJar Line
					if($is_taxjar_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
						$taxjar_item = $this->get_option('mw_wc_qbo_desk_wc_taxjar_map_qbo_product');
						if(empty($taxjar_item)){
							$taxjar_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($taxjar_item);
						$ExtLine->setDesc('TaxJar - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}
						
						$SalesReceipt->addSalesReceiptLine($ExtLine);
					}
					
					//Avatax Line
					if($is_avatax_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
						$avatax_item = $this->get_option('mw_wc_qbo_desk_wc_avatax_map_qbo_product');
						if(empty($avatax_item)){
							$avatax_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($avatax_item);
						$ExtLine->setDesc('Avatax - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$SalesReceipt->addSalesReceiptLine($ExtLine);
					}
					
					//Order Tax as Line Item					
					if($is_so_tax_as_li && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
						$otli_item = $this->get_option('mw_wc_qbo_desk_otli_qbd_product');
						if(empty($otli_item)){
							$otli_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($otli_item);
						
						$Description = '';
						if(is_array($tax_details) && count($tax_details)){
							if(isset($tax_details[0]['label'])){
								$Description = $tax_details[0]['label'];
							}
							
							if(isset($tax_details[1]) && $tax_details[1]['label']){
								if(!empty(tax_details[1]['label'])){
									$Description = $Description.', '.$tax_details[1]['label'];
								}
							}
						}
						
						if(empty($Description)){
							$Description = 'Woocommerce Order Tax - QBD Line Item';
						}
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$SalesReceipt->addSalesReceiptLine($ExtLine);
					}
					
					/**/
					$qbd_subtotal_product = $this->get_option('mw_wc_qbo_desk_default_subtotal_product');
					if(!empty($qbd_subtotal_product)){
						$StLine = new QuickBooks_QBXML_Object_SalesReceipt_SalesReceiptLine();
						$StLine->setItemListID($qbd_subtotal_product);
						$SalesReceipt->addSalesReceiptLine($StLine);
					}
					
					//
					if($is_tax_applied){
						$TaxCodeRef =$qbo_tax_code;
						if($TaxCodeRef!=''){
							if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')!= 'Sales_Tax_Codes'){
								//$SalesReceipt->setSalesTaxItemListID($TaxCodeRef);
								$SalesReceipt->set('ItemSalesTaxRef ListID', $TaxCodeRef);
							}							
						}
					}
					
					//31-10-2017
					$is_as_addr_format = true;
					
					$billing_name = $this->get_array_isset($invoice_data,'_billing_first_name','',true).' '.$this->get_array_isset($invoice_data,'_billing_last_name','',true);
					
					$billing_name_fl = $billing_name;
					
					$country = $this->get_array_isset($invoice_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);
					//$country = '';
					
					
					$_billing_company = $this->get_array_isset($invoice_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($invoice_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($invoice_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($invoice_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($invoice_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);

					/**/
					$skip_billing_address = false;
					if($this->option_checked('mw_wc_qbo_desk_use_qb_ba_for_eqc') && is_array($extra) && isset($extra['existing_qbo_user_id']) && !empty($extra['existing_qbo_user_id'])){
						$skip_billing_address = true;
					}
					
					if(!$skip_billing_address){
						if(!$is_as_addr_format){
							if($_billing_company!=''){
								$SalesReceipt->setBillAddress(
									$billing_name,$_billing_company,$_billing_address_1,$_billing_address_2,'',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
								
							}else{
								$SalesReceipt->setBillAddress(
									$billing_name,$_billing_address_1,$_billing_address_2,'','',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_billing_first_name','',true),$this->get_array_isset($invoice_data,'_billing_last_name','',true),$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
							$r_fa = $this->get_ord_baf_addrs($rfs_arr,$invoice_data);
							$SalesReceipt->setBillAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}
					}
					

					if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!='' || $this->get_array_isset($invoice_data,'_shipping_company','',true)!=''){
						$shipping_name = $this->get_array_isset($invoice_data,'_shipping_first_name','',true).' '.$this->get_array_isset($invoice_data,'_shipping_last_name','',true);

						$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
						$country = $this->get_country_name_from_code($country);
						//$country = '';
						
						$_shipping_company = $this->get_array_isset($invoice_data,'_shipping_company','',true);
						$_shipping_address_1 = $this->get_array_isset($invoice_data,'_shipping_address_1','',true);
						$_shipping_address_2 = $this->get_array_isset($invoice_data,'_shipping_address_2','',true);
						$_shipping_city = $this->get_array_isset($invoice_data,'_shipping_city','',true);
						$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
						$_shipping_postcode = $this->get_array_isset($invoice_data,'_shipping_postcode','',true);
						
						if(!$is_as_addr_format){
							if($_shipping_company!=''){
								$SalesReceipt->setShipAddress(
									$shipping_name,$_shipping_company,$_shipping_address_1,$_shipping_address_2,'',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}else{
								$SalesReceipt->setShipAddress(
									$shipping_name,$_shipping_address_1,$_shipping_address_2,'','',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_shipping_first_name','',true),$this->get_array_isset($invoice_data,'_shipping_last_name','',true),$_shipping_company,$_shipping_address_1,$_shipping_address_2,$_shipping_city,$_shipping_state,$_shipping_postcode,$country);
							$r_fa = $this->get_ord_saf_addrs($rfs_arr,$invoice_data);
							$SalesReceipt->setShipAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}
						
					}
					
					/*
					setShipAddress($addr1, $addr2 = '', $addr3 = '', $addr4 = '', $addr5 = '', $city = '', $state = '', $province = '', $postalcode = '', $country = '', $note = '')
					*/

					/*
					setBillAddress($addr1, $addr2 = '', $addr3 = '', $addr4 = '', $addr5 = '', $city = '', $state = '', $province = '', $postalcode = '', $country = '', $note = '')
					*/

					/*
					setShipMethodName
					setShipMethodListID
					setShipDate

					setPaymentMethodName
					setPaymentMethodListID

					*/

					$Q_Memo = '';
					
					if($this->option_checked('mw_wc_qbo_desk_invoice_memo')){
						$Q_Memo = $customer_note;
					}
					
					if($this->option_checked('mw_wc_qbo_desk_cname_into_memo')){
						$cname_memo = $billing_name_fl;
						$Q_Memo = $cname_memo;
					}
					
					$Q_Memo = trim($Q_Memo);
					/*
					if($this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						if(!empty($Q_Memo)){
							$Q_Memo.= PHP_EOL . 'Order: '. $DocNumber;
						}else{
							$Q_Memo = 'Order: '. $DocNumber;
						}						
					}
					*/
					
					$SalesReceipt->setMemo($Q_Memo);
					
					//New - Extra Fields
					$mw_wc_qbo_desk_ord_push_rep_othername = $this->get_option('mw_wc_qbo_desk_ord_push_rep_othername');
					if($mw_wc_qbo_desk_ord_push_rep_othername!=''){
						$SalesReceipt->setSalesRepListID($mw_wc_qbo_desk_ord_push_rep_othername);
					}
					
					//WWLC CF SalesRep QBD SalesRep Map
					if($wc_cus_id>0 && $this->is_plugin_active('woocommerce-wholesale-lead-capture','woocommerce-wholesale-lead-capture.bootstrap') && $this->option_checked('mw_wc_qbo_desk_compt_wwlc_rf_srm_ed')){
						$wwlc_cf_rep_map = get_option('mw_wc_qbo_desk_wwlc_cf_rep_map');
						if(is_array($wwlc_cf_rep_map) && count($wwlc_cf_rep_map)){
							$wwlc_cf_rep = get_user_meta($wc_cus_id,'wwlc_cf_rep',true);
							if(!empty($wwlc_cf_rep)){
								if(isset($wwlc_cf_rep_map[$wwlc_cf_rep]) && !empty($wwlc_cf_rep_map[$wwlc_cf_rep])){
									$qbd_salesrep_id = $wwlc_cf_rep_map[$wwlc_cf_rep];
									//$SalesReceipt->setSalesRepListID($qbd_salesrep_id);
									$SalesReceipt->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					//WCFE CF SalesRep QBD SalesRep Map
					if($this->is_plugin_active('woocommerce-checkout-field-editor') && $this->option_checked('mw_wc_qbo_desk_compt_wcfe_rf_srm_ed')){
						$wcfe_cf_rep_map = get_option('mw_wc_qbo_desk_wcfe_cf_rep_map');
						if(is_array($wcfe_cf_rep_map) && count($wcfe_cf_rep_map)){
							$wcfe_cf_rep = $this->get_array_isset($invoice_data,'sales-rep','');
							if(!empty($wcfe_cf_rep)){
								if(isset($wcfe_cf_rep_map[$wcfe_cf_rep]) && !empty($wcfe_cf_rep_map[$wcfe_cf_rep])){
									$qbd_salesrep_id = $wcfe_cf_rep_map[$wcfe_cf_rep];
									$SalesReceipt->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					if($this->is_plugin_active('woocommerce-gateway-purchase-order') && $this->option_checked('mw_wc_qbo_desk_wpopg_po_support')){
						//if($_payment_method == 'woocommerce_gateway_purchase_order'){}
						$_po_number = $this->get_array_isset($invoice_data,'_po_number','',true);
						if($_po_number!=''){
							$SalesReceipt->setPONumber($_po_number);
						}else{
							$SalesReceipt->setPONumber('Web');
						}
					}
					
					if($this->wacs_base_cur_enabled()){					
						$base_currency = get_woocommerce_currency();
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
					}else{
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					}
					
					$qbo_account_id = $this->get_array_isset($pm_map_data,'qbo_account_id','');					
					if($qbo_account_id!=''){
						$SalesReceipt->setDepositToAccountListID($qbo_account_id);
					}					
					
					$term_id_str = $this->get_array_isset($pm_map_data,'term_id_str','',true);
					if($term_id_str!=''){
						$SalesReceipt->set('TermsRef ListID',$term_id_str);
					}
					
					$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','',true);
					if($qb_p_method_id!=''){
						$SalesReceipt->set('PaymentMethodRef ListID',$qb_p_method_id);
					}
					
					if(count($sp_arr_first)){
						$qb_shipmethod_id = $this->get_array_isset($sp_arr_first,'qb_shipmethod_id','',true);
						if($qb_shipmethod_id!=''){
							$SalesReceipt->set('ShipMethodRef ListID',$qb_shipmethod_id);
						}
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-sj-custom-shipping-field-mapping') && $this->check_sh_sj_csfm_hash() && $this->option_checked('mw_wc_qbo_desk_compt_np_sj_ocsf_map')){
						$_carrier_name = $this->get_array_isset($invoice_data,'_carrier_name','',true);
						$mw_wc_qbo_desk_compt_sjocsfm_mv = get_option('mw_wc_qbo_desk_compt_sjocsfm_mv');
						if(!empty($_carrier_name) && is_array($mw_wc_qbo_desk_compt_sjocsfm_mv) && isset($mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name])){
							$sj_cn_qb_sm_id = $mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name];
							if(!empty($sj_cn_qb_sm_id)){
								$SalesReceipt->set('ShipMethodRef ListID',$sj_cn_qb_sm_id);
							}
						}
					}
					
					$cf_map_data = array();
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
						$cf_map_data = $this->get_cf_map_data();
					}
					
					if(is_array($cf_map_data) && count($cf_map_data)){
						$qacfm = $this->get_qbo_avl_cf_map_fields();
						foreach($cf_map_data as $wcfm_k => $wcfm_v){
							$wcfm_k = trim($wcfm_k);
							$wcfm_v = trim($wcfm_v);
							
							if(!empty($wcfm_v)){
								$wcf_val = '';
								switch ($wcfm_k) {									
									case "wc_order_shipping_method_name":
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
								
								if(!empty($wcf_val) && isset($qacfm[$wcfm_v])){
									$qbo_cf_arr = array();
									switch ($wcfm_v) {
										case "":								
											break;
											
										default:
										try {
											if(is_array($qbo_cf_arr) && count($qbo_cf_arr) && isset($qbo_cf_arr[$wcfm_v])){
												//
											}else{
												$qacfm_naf = $this->get_qbo_avl_cf_map_fields(true);
												$ivqf = true;
												if(is_array($qacfm_naf) && count($qacfm_naf) && isset($qacfm_naf[$wcfm_v])){
													$ivqf = false;
												}
												if($ivqf){
													$SalesReceipt->set("{$wcfm_v}",$wcf_val);
												}else{
													if($wcfm_v == 'ShipTo'){
														$cfst = $this->get_fr_cf_ship_to($wcf_val);
														$SalesReceipt->setShipAddress(
															$this->get_array_isset($cfst,0,'',true),
															$this->get_array_isset($cfst,1,'',true),
															$this->get_array_isset($cfst,2,'',true),
															$this->get_array_isset($cfst,3,'',true),
															$this->get_array_isset($cfst,4,'',true)
															
														);
													}
												}
											}
										}catch(Exception $e) {
											$cfm_err = $e->getMessage();
										}
									}
								}
							}
						}
					}
					
					/*Tracking Num Compatibility*/
					if($this->is_plugin_active('woocommerce-shipment-tracking') && $this->option_checked('mw_wc_qbo_desk_w_shp_track')){
						$_wc_shipment_tracking_items = $this->get_array_isset($invoice_data,'_wc_shipment_tracking_items','',true);
						
						$wf_wc_shipment_source = $this->get_array_isset($invoice_data,'wf_wc_shipment_source','',true);
						$wf_wc_shipment_result = $this->get_array_isset($invoice_data,'wf_wc_shipment_result','',true);
						
						if($_wc_shipment_tracking_items!='' || $wf_wc_shipment_source!=''){
							if($_wc_shipment_tracking_items!=''){
								$wsti_data = $this->wc_get_wst_data($_wc_shipment_tracking_items);
							}else{
								$wsti_data = $this->wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result);
							}
							if(count($wsti_data)){
								$tracking_provider = $this->get_array_isset($wsti_data,'tracking_provider','',true);
								$tracking_number = $this->get_array_isset($wsti_data,'tracking_number','',true);
								$date_shipped = $this->get_array_isset($wsti_data,'date_shipped','',true);
								if($tracking_provider!=''){
									$wst_tp_qsm_mp = get_option('mw_wc_qbo_desk_compt_wshptrack_tp_mv');
									if(is_array($wst_tp_qsm_mp) && isset($wst_tp_qsm_mp[$tracking_provider]) && !empty($wst_tp_qsm_mp[$tracking_provider])){
										$qb_sm_id = $wst_tp_qsm_mp[$tracking_provider];
										$SalesReceipt->set('ShipMethodRef ListID',$qb_sm_id);
									}									
								}
								$SalesReceipt->set('Other',$tracking_number);
								$SalesReceipt->set('ShipDate',$date_shipped);
							}
						}
					}
							
					$mw_wc_qbo_desk_ord_push_entered_by = $this->get_option('mw_wc_qbo_desk_ord_push_entered_by');
					if($mw_wc_qbo_desk_ord_push_entered_by!=''){
						$SalesReceipt->setOther($mw_wc_qbo_desk_ord_push_entered_by);
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$fb_cust_term = $this->get_array_isset($invoice_data,'net_terms','');							
							$fb_cust_term = $this->sanitize($fb_cust_term);
							if(!empty($fb_cust_term)){
								$tm_r = $this->get_row($wpdb->prepare("SELECT qbd_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term` WHERE name = %s",$fb_cust_term));
								if(is_array($tm_r) && !empty($tm_r) && isset($tm_r['qbd_id']) && !empty($tm_r['qbd_id'])){
									$qbd_term_id = $tm_r['qbd_id'];
									$SalesReceipt->set('TermsRef ListID',$qbd_term_id);
								}
							}
						}
					}
					
					//$this->_p($SalesReceipt);
					
					$qbxml = $SalesReceipt->asQBXML(QUICKBOOKS_ADD_SALESRECEIPT,null,$this->get_qbxml_locale());					
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
					$qbxml = $this->qbxml_search_replace($qbxml);

					return $qbxml;

				}
			}
		}
	}	
	
	/*SalesOrder QBXML*/
	public function GetSalesOrderQbxml($order_id,$extra=null){
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->_p($invoice_data);
			if(is_array($invoice_data) && count($invoice_data)){
				global $wpdb;
				$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
				$qbo_cus_id = '';
				
				$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
				$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
				
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);

				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) $this->get_array_isset($invoice_data,'account_number','');
						if($c_account_number > 0){
							$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
				if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
							if(empty($ord_country)){
								$ord_country = $this->get_array_isset($invoice_data,'_billing_country','',true);
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
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
					if($wc_cus_id>0){						
						$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
					}else{						
						//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
						$shipping_country = $this->get_array_isset($invoice_data,'_shipping_country','');
					}
					
					if($shipping_country == 'US'){
						if($wc_cus_id>0){
							$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
						}else{
							//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
							$shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','');
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
						$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}
				
				if(empty($qbo_cus_id)){
					if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
						if($wc_cus_id>0){
							//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
							if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
							}else{
								$customer_data = $this->get_wc_customer_info($wc_cus_id);
							}						
							//$this->_p($customer_data);
							$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
						}else{
							$customer_data = $this->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
						}
					}else{
						/*
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}							
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							
						}else{
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
						}
						*/
						
						/**/
						$wc_user_role = '';
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
								}
							}else{
								$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
							}
						}
						//
					}
				}
				
				if(empty($qbo_cus_id)){
					$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$order_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
					return false;
				}
				
				if($qbo_cus_id!=''){
					$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
					$SalesOrder = new QuickBooks_QBXML_Object_SalesOrder();
					
					//$SalesOrder->setCustomerName();
					$SalesOrder->setCustomerListID($qbo_cus_id);
					
					if(!$this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						$SalesOrder->setRefNumber($DocNumber);
					}
					
					/**/
					$TemplateRef = $this->get_array_isset($invoice_data,'TemplateRef','');
					if(!empty($TemplateRef)){
						$SalesOrder->set('TemplateRef ListID', $TemplateRef);
					}
					
					$inv_sr_txn_class = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
					if($inv_sr_txn_class!=''){
						$SalesOrder->setClassListID($inv_sr_txn_class);
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_desk_qbo_push_invoice_is_print_true')){
						$SalesOrder->set('IsToBePrinted',1);
					}
					
					/**/
					$customer_note = $this->get_array_isset($invoice_data,'customer_note','');
					//$SalesOrder->set('PONumber',$customer_note);
					
					//PO
					if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $this->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){
						if(!empty($this->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor'))){
							$DocNumber_Po = 'PO-'.$DocNumber;
							//
							$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
							if($this->chk_is_po_add($qbo_inv_items)){
								$SalesOrder->set('PONumber',$DocNumber_Po);
							}							
						}
					}
					
					//NP Billing State - QBD Class Map					
					if($this->option_checked('mw_wc_qbo_desk_compt_np_bus_qbc_map_ed')){
						$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
						if(!empty($_billing_state)){
							$bus_qbc_map = get_option('mw_wc_qbo_desk_np_bill_us_st_qb_cl_map');
							if(is_array($bus_qbc_map) && count($bus_qbc_map)){
								if(isset($bus_qbc_map[$_billing_state]) && !empty($bus_qbc_map[$_billing_state])){
									$qbd_classid = $bus_qbc_map[$_billing_state];
									$SalesOrder->setClassListID($qbd_classid);
								}
							}
						}						
					}
					
					$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
					$wc_inv_date = $this->format_date($wc_inv_date);
					if($wc_inv_date!=''){
						$SalesOrder->setTxnDate($wc_inv_date);
					}
					
					$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
					$wc_inv_due_date = $this->format_date($wc_inv_due_date);
					if($wc_inv_due_date!=''){
						$SalesOrder->setDueDate($wc_inv_due_date);
					}
					
					
					$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
					
					/*Count Total Amounts*/
					$_cart_discount = $this->get_array_isset($invoice_data,'_cart_discount',0);
					$_cart_discount_tax = $this->get_array_isset($invoice_data,'_cart_discount_tax',0);

					$_order_tax = (float) $this->get_array_isset($invoice_data,'_order_tax',0);
					$_order_shipping_tax = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax',0);
					$_order_total_tax = ($_order_tax+$_order_shipping_tax);

					$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);

					/*Qbd settings*/
					$qbo_is_sales_tax = true;
					$qbo_company_country = 'US';
					$qbo_is_shipping_allowed = false;

					/*Tax rates*/
					$qbo_tax_code = '';
					$apply_tax = false;
					$is_tax_applied = false;
					$is_inclusive = false;

					$qbo_tax_code_shipping = '';

					$tax_rate_id = 0;
					$tax_rate_id_2 = 0;

					$tax_details = (isset($invoice_data['tax_details']))?$invoice_data['tax_details']:array();
					
					//Tax Totals From tax Lines
					$calc_order_tax_totals_from_tax_lines = true;					
					if($calc_order_tax_totals_from_tax_lines){
						$_order_tax = 0;
						$_order_shipping_tax = 0;
						$_order_total_tax = 0;
						
						if(count($tax_details)){
							foreach($tax_details as $td){
								$_order_tax+=$td['tax_amount'];
								$_order_shipping_tax+=$td['shipping_tax_amount'];
								$_order_total_tax+=$td['tax_amount']+$td['shipping_tax_amount'];
							}
						}
					}
					$_order_total_tax = $this->qbd_limit_decimal_points($_order_total_tax);
					
					//TaxJar Settings
					$is_taxjar_active = false;
					$woocommerce_taxjar_integration_settings = get_option('woocommerce_taxjar-integration_settings');
					$wc_taxjar_enable_tax_calculation = 0;
					if(is_array($woocommerce_taxjar_integration_settings) && count($woocommerce_taxjar_integration_settings)){
						if(isset($woocommerce_taxjar_integration_settings['enabled']) && $woocommerce_taxjar_integration_settings['enabled']=='yes'){
							$wc_taxjar_enable_tax_calculation = 1;
						}
					}
					
					if($this->is_plugin_active('taxjar-simplified-taxes-for-woocommerce','taxjar-woocommerce') && $this->option_checked('mw_wc_qbo_desk_wc_taxjar_support') && $wc_taxjar_enable_tax_calculation=='1'){
						$is_taxjar_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//Avatax Settings
					$is_avatax_active = false;
					$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
					if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_desk_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
						$is_avatax_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//
					$is_so_tax_as_li = false;
					if($this->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
						$is_so_tax_as_li = true;
						$qbo_is_sales_tax = false;
					}
					
					if($qbo_is_sales_tax){
						if(count($tax_details)){
							$tax_rate_id = $tax_details[0]['rate_id'];
						}

						if(count($tax_details)>1){
							if($tax_details[1]['tax_amount']>0){
								$tax_rate_id_2 = $tax_details[1]['rate_id'];
							}
						}

						/*
						if(count($tax_details)>1 && $qbo_is_shipping_allowed){
							foreach($tax_details as $td){
								if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
									$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
									break;
								}
							}
						}
						*/

						$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
						if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
							$apply_tax = true;
						}
						
						//$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
						$is_qbo_dual_tax = false;

						/*
						if(count($Tax_Code_Details)){
							if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
								$is_qbo_dual_tax = true;
							}
						}

						$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
						$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

						$NetAmountTaxable = 0;

						if($is_qbo_dual_tax){
							$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
							$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
							$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
							$NetAmountTaxable_2 = 0;
						}
						*/

						/*
						if($qbo_tax_code_shipping!=''){
							$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
							$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
							$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
							$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
							$NetAmountTaxable_Shipping = 0;
						}
						*/

						$_prices_include_tax = $this->get_array_isset($invoice_data,'_prices_include_tax','no',true);
						if($qbo_is_sales_tax){
							$tax_type = $this->get_tax_type($_prices_include_tax);
							$is_inclusive = $this->is_tax_inclusive($tax_type);
						}
					}
					
					$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
					
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							$SalesOrderLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
							//$SalesOrderLine->setItemName($qbo_item['Description']);
							$SalesOrderLine->setItemListID($qbo_item["ItemRef"]);
							if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
								$SalesOrderLine->setClassListID($qbo_item["ClassRef"]);
							}
							
							$Description = $qbo_item['Description'];
							if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
								$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
								$li_sku = get_post_meta( $li_item_id, '_sku', true );
								if($li_sku!=''){
									$Description.=' ('.$li_sku.')';
								}
							}
							
							//Extra Description
							if(isset($qbo_item["Qbd_Ext_Description"])){
								$Description.= $qbo_item["Qbd_Ext_Description"];
							}
							
							if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
								$SalesOrderLine->setDescription($Description);
							}							
							
							$UnitPrice = $qbo_item["UnitPrice"];
							$Qty = $qbo_item["Qty"];
							$Amount = $Qty*$UnitPrice;
							$SalesOrderLine->setRate($UnitPrice);
							$SalesOrderLine->setQuantity($Qty);
							$SalesOrderLine->setAmount($Amount);
							
							if($this->option_checked('mw_wc_qbo_desk_compt_wqclns_ed')){
								$LotNumber  = $this->get_array_isset($qbo_item,'lot','');
								if(!empty($LotNumber)){
									$SalesOrderLine->set('LotNumber',$LotNumber);
								}
							}
							
							/*
							ServiceDate
							*/
							
							if($qbo_is_sales_tax){
								if($apply_tax && $qbo_item["Taxed"]){
									$is_tax_applied = true;
									/*$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;*/
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									/*
									if($is_inclusive){
										$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
									}
									*/
									if($TaxCodeRef!=''){
										$SalesOrderLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$SalesOrderLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$SalesOrderLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							/**/
							$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							if($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								/*
								$mw_warehouse = 0;
								if(isset($qbo_item["_order_item_wh"])){
									$mw_warehouse = (int) $qbo_item["_order_item_wh"];
								}else{
									$mw_warehouse = (int) $this->get_array_isset($invoice_data,'mw_warehouse',0);
								}
								*/
								
								$mw_warehouse = $this->get_mwr_oiw_mw_idls($qbo_item, $invoice_data);
								
								if($mw_warehouse > 0){
									$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
									if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && isset($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse])){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = trim($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse]);
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											if($this->is_inv_site_bin_allowed()){
												if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
													$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
													if(is_array($site_bin_arr) && !empty($site_bin_arr)){
														$SalesOrderLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
														if(isset($site_bin_arr[1])){
															$SalesOrderLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
														}
													}
												}
											}else{
												$SalesOrderLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											}									
										}
									}
								}
							}
							
							if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){									
									if($this->is_inv_site_bin_allowed()){
										if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
											$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											if(is_array($site_bin_arr) && !empty($site_bin_arr)){
												$SalesOrderLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
												if(isset($site_bin_arr[1])){
													$SalesOrderLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
												}
											}
										}
									}else{
										$SalesOrderLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
							}
							
							/**/
							if($this->option_checked('mw_wc_qbo_desk_compt_np_liqtycustcolumn_ed') && $this->check_sh_liqtycustcolumn_hash()){
								$cqtyf = $this->get_option('mw_wc_qbo_desk_compt_np_liqtycustcolumn_cqtyf');
								if(empty($cqtyf)){
									$cqtyf = 'Other1';
								}
								$SalesOrderLine->set($cqtyf , $Qty);
							}
							
							$SalesOrder->addSalesOrderLine($SalesOrderLine);
						}
					}
					
					//pgdf compatibility
					if($this->get_wc_fee_plugin_check()){
						$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
						if(is_array($dc_gt_fees) && count($dc_gt_fees)){
							foreach($dc_gt_fees as $df){
								$SalesOrderLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
								
								$UnitPrice = $df['_line_total'];
								$Qty = 1;
								$Amount = $Qty*$UnitPrice;
								
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$invoice_data);
								$SalesOrderLine->setItemListID($df_ItemRef);
								
								$SalesOrderLine->setRate($UnitPrice);
								$SalesOrderLine->setQuantity($Qty);
								$SalesOrderLine->setAmount($Amount);
								
								$SalesOrderLine->setDescription($df['name']);
								
								$_line_tax = $df['_line_tax'];
								if($_line_tax && $qbo_is_sales_tax){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$SalesOrderLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$SalesOrderLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
								
								/*
								if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
									$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
									if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
										$SalesOrderLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
								*/
								
								$SalesOrder->addSalesOrderLine($SalesOrderLine);
							}
							
						}						
					}
					
					//pw_gift_card compatibility
					if($this->is_plugin_active('pw-woocommerce-gift-cards','pw-gift-cards') && $this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed') && !empty($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'))){
						$pw_gift_card = (isset($invoice_data['pw_gift_card']))?$invoice_data['pw_gift_card']:array();
						if(is_array($pw_gift_card) && count($pw_gift_card)){
							foreach($pw_gift_card as $pgc){
								$pgc_amount = $pgc['amount'];
								if($pgc_amount > 0){
									$pgc_amount = -1 * abs($pgc_amount);
								}
								
								$Qty = 1;
								$Description = $pgc['card_number'];
								$SalesOrderLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
								$SalesOrderLine->setItemListID($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'));
								$SalesOrderLine->setRate($pgc_amount);
								$SalesOrderLine->setQuantity($Qty);
								$SalesOrderLine->setAmount($pgc_amount);
								
								$SalesOrderLine->setDescription($Description);
								
								/*
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$SalesOrderLine->setSalesTaxCodeListID($zero_rated_tax_code);
								*/
								
								$SalesOrder->addSalesOrderLine($SalesOrderLine);
							}
						}
					}
					
					/*Add SalesOrder Coupons*/
					$used_coupons  = (isset($invoice_data['used_coupons']))?$invoice_data['used_coupons']:array();
					$qbo_is_discount_allowed = true;
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						$qbo_is_discount_allowed = false;
					}
					
					if($qbo_is_discount_allowed && count($used_coupons)){
						foreach($used_coupons as $coupon){
							$coupon_name = $coupon['name'];
							$coupon_discount_amount = $coupon['discount_amount'];
							$coupon_discount_amount = -1 * abs($coupon_discount_amount);
							$coupon_discount_amount_tax = $coupon['discount_amount_tax'];

							$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
							$DiscountLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
							$DiscountLine->setItemListID($coupon_product_arr["ItemRef"]);
							if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
								$DiscountLine->setClassListID($coupon_product_arr["ClassRef"]);
							}
							$DiscountLine->setDescription($coupon_product_arr['Description']);
							$DiscountLine->setRate($coupon_discount_amount);
							
							if($coupon_product_arr['qbo_product_type'] != 'Discount'){
								//$DiscountLine->setQuantity(1);
								$DiscountLine->setAmount($coupon_discount_amount);
							}							
							
							if($qbo_is_sales_tax){
								if($coupon_discount_amount_tax > 0 || $is_tax_applied){
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$DiscountLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}								
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									//$DiscountLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
								}
							}

							$SalesOrder->addSalesOrderLine($DiscountLine);
						}
					}
					
					/*Add SalesOrder Shipping*/
					$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
					
					$sp_arr_first = array();					
					if(is_array($shipping_details) && !empty($shipping_details)){
						foreach($shipping_details as $sd_k => $sd_v){
							
							$shipping_method = '';
							$shipping_method_name = '';
							$shipping_taxes = '';
							$smt_id = 0;
							if(isset($shipping_details[$sd_k])){
								if($this->get_array_isset($shipping_details[$sd_k],'type','')=='shipping'){
									$shipping_method_id = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
									if($shipping_method_id!=''){
										if(isset($shipping_details[$sd_k]['instance_id']) && $shipping_details[$sd_k]['instance_id']>0){
											$shipping_method = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
											$smt_id = (int) $this->get_array_isset($shipping_details[$sd_k],'instance_id',0);
										}else{
											$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'',$sd_v);
											$smt_id = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'id',$sd_v);
										}
									}
									
									$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
									$shipping_method_name =  $this->get_array_isset($shipping_details[$sd_k],'name','',true,30);
									$shipping_taxes = $this->get_array_isset($shipping_details[$sd_k],'taxes','');
								}
							}						
							
							$shipping_product_arr = array();
							
							if($shipping_method!=''){
								if(!$qbo_is_shipping_allowed){
									if($smt_id>0){
										$smt_id_str = $shipping_method.':'.$smt_id;
										$shipping_product_arr = $this->get_mapped_shipping_product($smt_id_str,$sd_v,true);
									}
									
									if(!count($shipping_product_arr) || empty($shipping_product_arr['ItemRef'])){
										$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method,$sd_v);
									}
									
									if(empty($sp_arr_first)){
										$sp_arr_first = $shipping_product_arr;
									}
									
									$ShippingLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
									$ShippingLine->setItemListID($shipping_product_arr["ItemRef"]);
									if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
										$ShippingLine->setClassListID($shipping_product_arr["ClassRef"]);
									}
									$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										$ShippingLine->setDescription($shipping_description);
									}
									
									//$ShippingLine->setQuantity(1);
									if(!$this->check_sh_wcmslscqb_hash()){
										$ShippingLine->setRate($order_shipping_total);
										$ShippingLine->setAmount($order_shipping_total);
									}else{
										$ShippingLine->setRate($sd_v['cost']);						
										$ShippingLine->setAmount($sd_v['cost']);
									}

									if($qbo_is_sales_tax){
										if(($this->check_sh_wcmslscqb_hash() && $sd_v['total_tax']>0) || (!$this->check_sh_wcmslscqb_hash() && $_order_shipping_tax>0)){
											$TaxCodeRef = '';
											if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
												$TaxCodeRef =$qbo_tax_code;
											}
											
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}
											if(empty($TaxCodeRef)){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
											}
											
											if($TaxCodeRef!=''){
												$ShippingLine->setSalesTaxCodeListID($TaxCodeRef);
											}
										}else{
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
											}
											$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
										}								
									}
									
									if(!$qbo_is_sales_tax){
										if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
											$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
										}else{
											$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
										}
										$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
									}
									
									if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											//$ShippingLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
										}
									}
									
									$SalesOrder->addSalesOrderLine($ShippingLine);

								}
							}
							
							if(!$this->check_sh_wcmslscqb_hash()){
								break;
							}
						}
					}
					
					if(!$is_taxjar_active){
						//$order_shipping_total+=$_order_shipping_tax;
					}
					
					//TaxJar Line
					if($is_taxjar_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
						$taxjar_item = $this->get_option('mw_wc_qbo_desk_wc_taxjar_map_qbo_product');
						if(empty($taxjar_item)){
							$taxjar_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($taxjar_item);
						$ExtLine->setDescription('TaxJar - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$SalesOrder->addSalesOrderLine($ExtLine);
					}
					
					//Avatax Line
					if($is_avatax_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
						$avatax_item = $this->get_option('mw_wc_qbo_desk_wc_avatax_map_qbo_product');
						if(empty($avatax_item)){
							$avatax_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($avatax_item);
						$ExtLine->setDescription('Avatax - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);

						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$SalesOrder->addSalesOrderLine($ExtLine);
					}
					
					//Order Tax as Line Item					
					if($is_so_tax_as_li && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
						$otli_item = $this->get_option('mw_wc_qbo_desk_otli_qbd_product');
						if(empty($otli_item)){
							$otli_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($otli_item);
						
						$Description = '';
						if(is_array($tax_details) && count($tax_details)){
							if(isset($tax_details[0]['label'])){
								$Description = $tax_details[0]['label'];
							}
							
							if(isset($tax_details[1]) && $tax_details[1]['label']){
								if(!empty(tax_details[1]['label'])){
									$Description = $Description.', '.$tax_details[1]['label'];
								}
							}
						}
						
						if(empty($Description)){
							$Description = 'Woocommerce Order Tax - QBD Line Item';
						}						
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDescription($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$SalesOrder->addSalesOrderLine($ExtLine);
					}
					
					/**/
					$qbd_subtotal_product = $this->get_option('mw_wc_qbo_desk_default_subtotal_product');
					if(!empty($qbd_subtotal_product)){
						$StLine = new QuickBooks_QBXML_Object_SalesOrder_SalesOrderLine();
						$StLine->setItemListID($qbd_subtotal_product);
						$SalesOrder->addSalesOrderLine($StLine);
					}
					
					//
					if($is_tax_applied){
						$TaxCodeRef =$qbo_tax_code;
						if($TaxCodeRef!=''){
							if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')!= 'Sales_Tax_Codes'){
								//$SalesOrder->setSalesTaxItemListID($TaxCodeRef);
								$SalesOrder->set('ItemSalesTaxRef ListID', $TaxCodeRef);
							}							
						}
					}
					
					$is_as_addr_format = true;

					$billing_name = $this->get_array_isset($invoice_data,'_billing_first_name','',true).' '.$this->get_array_isset($invoice_data,'_billing_last_name','',true);
					$billing_name_fl = $billing_name;

					$country = $this->get_array_isset($invoice_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);
					//$country = '';
					
					$_billing_company = $this->get_array_isset($invoice_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($invoice_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($invoice_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($invoice_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($invoice_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
					
					/**/
					$skip_billing_address = false;
					if($this->option_checked('mw_wc_qbo_desk_use_qb_ba_for_eqc') && is_array($extra) && isset($extra['existing_qbo_user_id']) && !empty($extra['existing_qbo_user_id'])){
						$skip_billing_address = true;
					}
					
					if(!$skip_billing_address){
						if(!$is_as_addr_format){
							if($_billing_company!=''){
								$SalesOrder->setBillAddress(
									$billing_name,$_billing_company,$_billing_address_1,$_billing_address_2,'',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}else{
								$SalesOrder->setBillAddress(
									$billing_name,$_billing_address_1,$_billing_address_2,'','',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_billing_first_name','',true),$this->get_array_isset($invoice_data,'_billing_last_name','',true),$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
							$r_fa = $this->get_ord_baf_addrs($rfs_arr,$invoice_data);
							$SalesOrder->setBillAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}
					}
					
					if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!='' || $this->get_array_isset($invoice_data,'_shipping_company','',true)!=''){
						$shipping_name = $this->get_array_isset($invoice_data,'_shipping_first_name','',true).' '.$this->get_array_isset($invoice_data,'_shipping_last_name','',true);

						$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
						$country = $this->get_country_name_from_code($country);
						
						$_shipping_company = $this->get_array_isset($invoice_data,'_shipping_company','',true);
						$_shipping_address_1 = $this->get_array_isset($invoice_data,'_shipping_address_1','',true);
						$_shipping_address_2 = $this->get_array_isset($invoice_data,'_shipping_address_2','',true);
						$_shipping_city = $this->get_array_isset($invoice_data,'_shipping_city','',true);
						$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
						$_shipping_postcode = $this->get_array_isset($invoice_data,'_shipping_postcode','',true);
						
						if(!$is_as_addr_format){
							if($_shipping_company!=''){
								$SalesOrder->setShipAddress(
									$shipping_name,$_shipping_company,$_shipping_address_1,$_shipping_address_2,'',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}else{
								$SalesOrder->setShipAddress(
									$shipping_name,$_shipping_address_1,$_shipping_address_2,'','',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_shipping_first_name','',true),$this->get_array_isset($invoice_data,'_shipping_last_name','',true),$_shipping_company,$_shipping_address_1,$_shipping_address_2,$_shipping_city,$_shipping_state,$_shipping_postcode,$country);
							$r_fa = $this->get_ord_saf_addrs($rfs_arr,$invoice_data);
							$SalesOrder->setShipAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}						
						
					}
					
					$Q_Memo = '';
					
					if($this->option_checked('mw_wc_qbo_desk_invoice_memo')){
						$Q_Memo = $customer_note;
					}
					
					if($this->option_checked('mw_wc_qbo_desk_cname_into_memo')){
						$cname_memo = $billing_name_fl;
						$Q_Memo = $cname_memo;
					}
					
					$Q_Memo = trim($Q_Memo);
					/*
					if($this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						if(!empty($Q_Memo)){
							$Q_Memo.= PHP_EOL . 'Order: '. $DocNumber;
						}else{
							$Q_Memo = 'Order: '. $DocNumber;
						}						
					}
					*/
					
					$SalesOrder->setMemo($Q_Memo);
					
					//New - Extra Fields
					$mw_wc_qbo_desk_ord_push_rep_othername = $this->get_option('mw_wc_qbo_desk_ord_push_rep_othername');
					if($mw_wc_qbo_desk_ord_push_rep_othername!=''){
						$SalesOrder->setSalesRepListID($mw_wc_qbo_desk_ord_push_rep_othername);
					}
					
					//WWLC CF SalesRep QBD SalesRep Map
					if($wc_cus_id>0 && $this->is_plugin_active('woocommerce-wholesale-lead-capture','woocommerce-wholesale-lead-capture.bootstrap') && $this->option_checked('mw_wc_qbo_desk_compt_wwlc_rf_srm_ed')){						
						$wwlc_cf_rep_map = get_option('mw_wc_qbo_desk_wwlc_cf_rep_map');
						if(is_array($wwlc_cf_rep_map) && count($wwlc_cf_rep_map)){
							$wwlc_cf_rep = get_user_meta($wc_cus_id,'wwlc_cf_rep',true);
							if(!empty($wwlc_cf_rep)){
								if(isset($wwlc_cf_rep_map[$wwlc_cf_rep]) && !empty($wwlc_cf_rep_map[$wwlc_cf_rep])){
									$qbd_salesrep_id = $wwlc_cf_rep_map[$wwlc_cf_rep];									
									//$SalesOrder->setSalesRepListID($qbd_salesrep_id);
									$SalesOrder->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					//WCFE CF SalesRep QBD SalesRep Map
					if($this->is_plugin_active('woocommerce-checkout-field-editor') && $this->option_checked('mw_wc_qbo_desk_compt_wcfe_rf_srm_ed')){
						$wcfe_cf_rep_map = get_option('mw_wc_qbo_desk_wcfe_cf_rep_map');
						if(is_array($wcfe_cf_rep_map) && count($wcfe_cf_rep_map)){
							$wcfe_cf_rep = $this->get_array_isset($invoice_data,'sales-rep','');
							if(!empty($wcfe_cf_rep)){
								if(isset($wcfe_cf_rep_map[$wcfe_cf_rep]) && !empty($wcfe_cf_rep_map[$wcfe_cf_rep])){
									$qbd_salesrep_id = $wcfe_cf_rep_map[$wcfe_cf_rep];
									$SalesOrder->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$fb_cust_rep = $this->get_array_isset($invoice_data,'rep','');							
							$fb_cust_rep = $this->sanitize($fb_cust_rep);
							if(!empty($fb_cust_rep)){
								$srm_r = $this->get_row("SELECT qbd_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salesrep` WHERE info_arr REGEXP '.*\"Initial\";s:[0-9]+:\"{$fb_cust_rep}\".*'");
								if(is_array($srm_r) && !empty($srm_r) && isset($srm_r['qbd_id']) && !empty($srm_r['qbd_id'])){
									$qbd_salesrep_id = $srm_r['qbd_id'];
									$SalesOrder->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					if($this->is_plugin_active('woocommerce-gateway-purchase-order') && $this->option_checked('mw_wc_qbo_desk_wpopg_po_support')){
						//if($_payment_method == 'woocommerce_gateway_purchase_order'){}
						$_po_number = $this->get_array_isset($invoice_data,'_po_number','',true);
						if($_po_number!=''){
							$SalesOrder->setPONumber($_po_number);
						}else{
							$SalesOrder->setPONumber('Web');
						}
					}
					
					$mw_wc_qbo_desk_ord_push_entered_by = $this->get_option('mw_wc_qbo_desk_ord_push_entered_by');
					if($mw_wc_qbo_desk_ord_push_entered_by!=''){
						$SalesOrder->setOther($mw_wc_qbo_desk_ord_push_entered_by);
					}
					
					if($this->wacs_base_cur_enabled()){					
						$base_currency = get_woocommerce_currency();
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
					}else{
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					}
					
					$term_id_str = $this->get_array_isset($pm_map_data,'term_id_str','',true);
					if($term_id_str!=''){
						$SalesOrder->setTermsListID($term_id_str);
					}
					
					$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','',true);
					if($qb_p_method_id!=''){
						$SalesOrder->set('PaymentMethodRef ListID',$qb_p_method_id);
					}
					
					if(count($sp_arr_first)){
						$qb_shipmethod_id = $this->get_array_isset($sp_arr_first,'qb_shipmethod_id','',true);
						if($qb_shipmethod_id!=''){
							$SalesOrder->set('ShipMethodRef ListID',$qb_shipmethod_id);
						}
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-sj-custom-shipping-field-mapping') && $this->check_sh_sj_csfm_hash() && $this->option_checked('mw_wc_qbo_desk_compt_np_sj_ocsf_map')){
						$_carrier_name = $this->get_array_isset($invoice_data,'_carrier_name','',true);
						$mw_wc_qbo_desk_compt_sjocsfm_mv = get_option('mw_wc_qbo_desk_compt_sjocsfm_mv');
						if(!empty($_carrier_name) && is_array($mw_wc_qbo_desk_compt_sjocsfm_mv) && isset($mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name])){
							$sj_cn_qb_sm_id = $mw_wc_qbo_desk_compt_sjocsfm_mv[$_carrier_name];
							if(!empty($sj_cn_qb_sm_id)){
								$SalesOrder->set('ShipMethodRef ListID',$sj_cn_qb_sm_id);
							}
						}
					}
					
					$cf_map_data = array();
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
						$cf_map_data = $this->get_cf_map_data();
					}
					
					if(is_array($cf_map_data) && count($cf_map_data)){
						$qacfm = $this->get_qbo_avl_cf_map_fields();
						foreach($cf_map_data as $wcfm_k => $wcfm_v){
							$wcfm_k = trim($wcfm_k);
							$wcfm_v = trim($wcfm_v);
							
							if(!empty($wcfm_v)){
								$wcf_val = '';
								switch ($wcfm_k) {									
									case "wc_order_shipping_method_name":
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
								
								if(!empty($wcf_val) && isset($qacfm[$wcfm_v])){
									$qbo_cf_arr = array();
									switch ($wcfm_v) {
										case "":								
											break;
											
										default:
										try {
											if(is_array($qbo_cf_arr) && count($qbo_cf_arr) && isset($qbo_cf_arr[$wcfm_v])){
												//
											}else{
												$qacfm_naf = $this->get_qbo_avl_cf_map_fields(true);
												$ivqf = true;
												if(is_array($qacfm_naf) && count($qacfm_naf) && isset($qacfm_naf[$wcfm_v])){
													$ivqf = false;
												}
												if($ivqf){
													$SalesOrder->set("{$wcfm_v}",$wcf_val);
												}else{
													if($wcfm_v == 'ShipTo'){
														$cfst = $this->get_fr_cf_ship_to($wcf_val);
														$SalesOrder->setShipAddress(
															$this->get_array_isset($cfst,0,'',true),
															$this->get_array_isset($cfst,1,'',true),
															$this->get_array_isset($cfst,2,'',true),
															$this->get_array_isset($cfst,3,'',true),
															$this->get_array_isset($cfst,4,'',true)
															
														);
													}
												}
											}
										}catch(Exception $e) {
											$cfm_err = $e->getMessage();
										}
									}
								}
							}
						}
					}
					
					/*Tracking Num Compatibility*/
					if($this->is_plugin_active('woocommerce-shipment-tracking') && $this->option_checked('mw_wc_qbo_desk_w_shp_track')){
						$_wc_shipment_tracking_items = $this->get_array_isset($invoice_data,'_wc_shipment_tracking_items','',true);
						
						$wf_wc_shipment_source = $this->get_array_isset($invoice_data,'wf_wc_shipment_source','',true);
						$wf_wc_shipment_result = $this->get_array_isset($invoice_data,'wf_wc_shipment_result','',true);
						
						if($_wc_shipment_tracking_items!='' || $wf_wc_shipment_source!=''){
							if($_wc_shipment_tracking_items!=''){
								$wsti_data = $this->wc_get_wst_data($_wc_shipment_tracking_items);
							}else{
								$wsti_data = $this->wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result);
							}
							if(count($wsti_data)){
								$tracking_provider = $this->get_array_isset($wsti_data,'tracking_provider','',true);
								$tracking_number = $this->get_array_isset($wsti_data,'tracking_number','',true);
								$date_shipped = $this->get_array_isset($wsti_data,'date_shipped','',true);
								if($tracking_provider!=''){
									$wst_tp_qsm_mp = get_option('mw_wc_qbo_desk_compt_wshptrack_tp_mv');
									if(is_array($wst_tp_qsm_mp) && isset($wst_tp_qsm_mp[$tracking_provider]) && !empty($wst_tp_qsm_mp[$tracking_provider])){
										$qb_sm_id = $wst_tp_qsm_mp[$tracking_provider];
										$SalesOrder->set('ShipMethodRef ListID',$qb_sm_id);
									}									
								}
								$SalesOrder->set('Other',$tracking_number);
								$SalesOrder->set('ShipDate',$date_shipped);
							}
						}
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$fb_cust_term = $this->get_array_isset($invoice_data,'net_terms','');							
							$fb_cust_term = $this->sanitize($fb_cust_term);
							if(!empty($fb_cust_term)){
								$tm_r = $this->get_row($wpdb->prepare("SELECT qbd_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term` WHERE name = %s",$fb_cust_term));
								if(is_array($tm_r) && !empty($tm_r) && isset($tm_r['qbd_id']) && !empty($tm_r['qbd_id'])){
									$qbd_term_id = $tm_r['qbd_id'];
									$SalesOrder->set('TermsRef ListID',$qbd_term_id);
								}
							}
						}
					}
					
					//$this->_p($SalesOrder);
					
					$qbxml = $SalesOrder->asQBXML(QUICKBOOKS_ADD_SALESORDER,null,$this->get_qbxml_locale());
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
					$qbxml = $this->qbxml_search_replace($qbxml);

					return $qbxml;
					
					/**/
				}
			}
		}
	}
	
	/*Estimate QBXML*/
	public function GetEstimateQbxml($order_id,$extra=null){
		if($this->is_qwc_connected()){
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$order = get_post($order_id);
			$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
			//$this->_p($invoice_data);
			if(is_array($invoice_data) && count($invoice_data)){
				global $wpdb;
				$wc_cus_id = (int) $this->get_array_isset($invoice_data,'wc_cus_id',0);
				$qbo_cus_id = '';
				
				$wc_inv_id = (int) $this->get_array_isset($invoice_data,'wc_inv_id',0);
				$wc_inv_num = $this->get_array_isset($invoice_data,'wc_inv_num','');
				
				$_order_currency = $this->get_array_isset($invoice_data,'_order_currency','',true);

				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) $this->get_array_isset($invoice_data,'account_number','');
						if($c_account_number > 0){
							$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
				if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
							if(empty($ord_country)){
								$ord_country = $this->get_array_isset($invoice_data,'_billing_country','',true);
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
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
					if($wc_cus_id>0){						
						$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
					}else{						
						//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
						$shipping_country = $this->get_array_isset($invoice_data,'_shipping_country','');
					}
					
					if($shipping_country == 'US'){
						if($wc_cus_id>0){
							$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
						}else{
							//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
							$shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','');
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
						$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}
				
				if(empty($qbo_cus_id)){
					if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
						if($wc_cus_id>0){
							//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
							if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
							}else{
								$customer_data = $this->get_wc_customer_info($wc_cus_id);
							}						
							//$this->_p($customer_data);
							$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
						}else{
							$customer_data = $this->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
						}
					}else{
						/*
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}							
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							
						}else{
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
						}
						*/
						
						/**/
						$wc_user_role = '';
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
								}
							}else{
								$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
							}
						}
						//
					}
				}
				
				if(empty($qbo_cus_id)){
					$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$order_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
					return false;
				}
				
				if($qbo_cus_id!=''){
					$DocNumber = ($wc_inv_num!='')?$wc_inv_num:$wc_inv_id;
					$Estimate = new QuickBooks_QBXML_Object_Estimate();
					
					$Estimate->setCustomerListID($qbo_cus_id);
					
					if(!$this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						$Estimate->setRefNumber($DocNumber);
					}
					
					/**/
					$TemplateRef = $this->get_array_isset($invoice_data,'TemplateRef','');
					if(!empty($TemplateRef)){
						$Estimate->set('TemplateRef ListID', $TemplateRef);
					}
					
					$inv_sr_txn_class = $this->get_option('mw_wc_qbo_desk_inv_sr_txn_qb_class');
					if($inv_sr_txn_class!=''){
						$Estimate->setClassListID($inv_sr_txn_class);
					}
					
					/**/
					if($this->option_checked('mw_wc_qbo_desk_qbo_push_invoice_is_print_true')){
						$Estimate->set('IsToBePrinted',1);
					}
					
					/*
					if($this->is_plugin_active('split-order-custom-po-for-myworks-quickbooks-desktop-sync') && $this->option_checked('mw_wc_qbo_desk_compt_p_ad_socpo_ed')){
						if(!empty($this->get_option('mw_wc_qbo_desk_compt_socpo_qbd_vendor'))){
							$DocNumber_Po = 'PO-'.$DocNumber;
							//
							$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
							if($this->chk_is_po_add($qbo_inv_items)){
								$Estimate->set('PONumber',$DocNumber_Po);
							}							
						}
					}
					*/
					
					//NP Billing State - QBD Class Map					
					if($this->option_checked('mw_wc_qbo_desk_compt_np_bus_qbc_map_ed')){
						$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
						if(!empty($_billing_state)){
							$bus_qbc_map = get_option('mw_wc_qbo_desk_np_bill_us_st_qb_cl_map');
							if(is_array($bus_qbc_map) && count($bus_qbc_map)){
								if(isset($bus_qbc_map[$_billing_state]) && !empty($bus_qbc_map[$_billing_state])){
									$qbd_classid = $bus_qbc_map[$_billing_state];
									$Estimate->setClassListID($qbd_classid);
								}
							}
						}						
					}
					
					$wc_inv_date = $this->get_array_isset($invoice_data,'wc_inv_date','');
					$wc_inv_date = $this->format_date($wc_inv_date);
					if($wc_inv_date!=''){
						$Estimate->setTxnDate($wc_inv_date);
					}
					
					$wc_inv_due_date = $this->get_array_isset($invoice_data,'wc_inv_due_date','');
					$wc_inv_due_date = $this->format_date($wc_inv_due_date);
					if($wc_inv_due_date!=''){
						$Estimate->setDueDate($wc_inv_due_date);
					}
					
					$_payment_method = $this->get_array_isset($invoice_data,'_payment_method','',true);
					
					/*Count Total Amounts*/
					$_cart_discount = $this->get_array_isset($invoice_data,'_cart_discount',0);
					$_cart_discount_tax = $this->get_array_isset($invoice_data,'_cart_discount_tax',0);

					$_order_tax = (float) $this->get_array_isset($invoice_data,'_order_tax',0);
					$_order_shipping_tax = (float) $this->get_array_isset($invoice_data,'_order_shipping_tax',0);
					$_order_total_tax = ($_order_tax+$_order_shipping_tax);

					$order_shipping_total = $this->get_array_isset($invoice_data,'order_shipping_total',0);

					/*Qbd settings*/
					$qbo_is_sales_tax = true;
					$qbo_company_country = 'US';
					$qbo_is_shipping_allowed = false;

					/*Tax rates*/
					$qbo_tax_code = '';
					$apply_tax = false;
					$is_tax_applied = false;
					$is_inclusive = false;

					$qbo_tax_code_shipping = '';

					$tax_rate_id = 0;
					$tax_rate_id_2 = 0;

					$tax_details = (isset($invoice_data['tax_details']))?$invoice_data['tax_details']:array();
					
					//Tax Totals From tax Lines
					$calc_order_tax_totals_from_tax_lines = true;					
					if($calc_order_tax_totals_from_tax_lines){
						$_order_tax = 0;
						$_order_shipping_tax = 0;
						$_order_total_tax = 0;
						
						if(count($tax_details)){
							foreach($tax_details as $td){
								$_order_tax+=$td['tax_amount'];
								$_order_shipping_tax+=$td['shipping_tax_amount'];
								$_order_total_tax+=$td['tax_amount']+$td['shipping_tax_amount'];
							}
						}
					}
					$_order_total_tax = $this->qbd_limit_decimal_points($_order_total_tax);
					
					//TaxJar Settings
					$is_taxjar_active = false;
					$woocommerce_taxjar_integration_settings = get_option('woocommerce_taxjar-integration_settings');
					$wc_taxjar_enable_tax_calculation = 0;
					if(is_array($woocommerce_taxjar_integration_settings) && count($woocommerce_taxjar_integration_settings)){
						if(isset($woocommerce_taxjar_integration_settings['enabled']) && $woocommerce_taxjar_integration_settings['enabled']=='yes'){
							$wc_taxjar_enable_tax_calculation = 1;
						}
					}
					
					if($this->is_plugin_active('taxjar-simplified-taxes-for-woocommerce','taxjar-woocommerce') && $this->option_checked('mw_wc_qbo_desk_wc_taxjar_support') && $wc_taxjar_enable_tax_calculation=='1'){
						$is_taxjar_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//Avatax Settings
					$is_avatax_active = false;
					$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
					if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_desk_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
						$is_avatax_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//
					$is_so_tax_as_li = false;
					if($this->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
						$is_so_tax_as_li = true;
						$qbo_is_sales_tax = false;
					}
					
					if($qbo_is_sales_tax){
						if(count($tax_details)){
							$tax_rate_id = $tax_details[0]['rate_id'];
						}

						if(count($tax_details)>1){
							if($tax_details[1]['tax_amount']>0){
								$tax_rate_id_2 = $tax_details[1]['rate_id'];
							}
						}

						/*
						if(count($tax_details)>1 && $qbo_is_shipping_allowed){
							foreach($tax_details as $td){
								if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
									$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
									break;
								}
							}
						}
						*/

						$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
						if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
							$apply_tax = true;
						}
						
						//$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
						$is_qbo_dual_tax = false;

						/*
						if(count($Tax_Code_Details)){
							if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
								$is_qbo_dual_tax = true;
							}
						}

						$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
						$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

						$NetAmountTaxable = 0;

						if($is_qbo_dual_tax){
							$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
							$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
							$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
							$NetAmountTaxable_2 = 0;
						}
						*/

						/*
						if($qbo_tax_code_shipping!=''){
							$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
							$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
							$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
							$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
							$NetAmountTaxable_Shipping = 0;
						}
						*/

						$_prices_include_tax = $this->get_array_isset($invoice_data,'_prices_include_tax','no',true);
						if($qbo_is_sales_tax){
							$tax_type = $this->get_tax_type($_prices_include_tax);
							$is_inclusive = $this->is_tax_inclusive($tax_type);
						}
					}
					
					$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							$EstimateLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
							
							$EstimateLine->setItemListID($qbo_item["ItemRef"]);
							if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
								$EstimateLine->setClassListID($qbo_item["ClassRef"]);
							}
							
							$Description = $qbo_item['Description'];
							if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
								$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
								$li_sku = get_post_meta( $li_item_id, '_sku', true );
								if($li_sku!=''){
									$Description.=' ('.$li_sku.')';
								}
							}
							
							//Extra Description
							if(isset($qbo_item["Qbd_Ext_Description"])){
								$Description.= $qbo_item["Qbd_Ext_Description"];
							}
							
							if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
								$EstimateLine->setDescription($Description);
							}							
							
							$UnitPrice = $qbo_item["UnitPrice"];
							$Qty = $qbo_item["Qty"];
							$Amount = $Qty*$UnitPrice;
							$EstimateLine->setRate($UnitPrice);
							$EstimateLine->setQuantity($Qty);
							$EstimateLine->setAmount($Amount);
							
							if($this->option_checked('mw_wc_qbo_desk_compt_wqclns_ed')){
								$LotNumber  = $this->get_array_isset($qbo_item,'lot','');
								if(!empty($LotNumber)){
									$EstimateLine->set('LotNumber',$LotNumber);
								}
							}
							
							/*
							ServiceDate
							*/

							if($qbo_is_sales_tax){
								if($apply_tax && $qbo_item["Taxed"]){
									$is_tax_applied = true;
									/*$TaxCodeRef = ($qbo_company_country=='US')?'TAX':$qbo_tax_code;*/
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}

									/*
									if($is_inclusive){
										$TaxInclusiveAmt = ($qbo_item['line_total']+$qbo_item['line_tax']);
									}
									*/
									if($TaxCodeRef!=''){
										$EstimateLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$EstimateLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$EstimateLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							/**/
							$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							if($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								/*
								$mw_warehouse = 0;
								if(isset($qbo_item["_order_item_wh"])){
									$mw_warehouse = (int) $qbo_item["_order_item_wh"];
								}else{
									$mw_warehouse = (int) $this->get_array_isset($invoice_data,'mw_warehouse',0);
								}
								*/
								
								$mw_warehouse = $this->get_mwr_oiw_mw_idls($qbo_item, $invoice_data);
								
								if($mw_warehouse > 0){
									$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
									if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && isset($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse])){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = trim($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse]);
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											if($this->is_inv_site_bin_allowed()){
												if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
													$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
													if(is_array($site_bin_arr) && !empty($site_bin_arr)){
														$EstimateLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
														if(isset($site_bin_arr[1])){
															$EstimateLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
														}
													}
												}
											}else{
												$EstimateLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											}									
										}
									}
								}
							}
							
							if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){									
									if($this->is_inv_site_bin_allowed()){
										if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
											$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											if(is_array($site_bin_arr) && !empty($site_bin_arr)){
												$EstimateLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
												if(isset($site_bin_arr[1])){
													$EstimateLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
												}
											}
										}
									}else{
										$EstimateLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
							}
							
							/**/
							if($this->option_checked('mw_wc_qbo_desk_compt_np_liqtycustcolumn_ed') && $this->check_sh_liqtycustcolumn_hash()){
								$cqtyf = $this->get_option('mw_wc_qbo_desk_compt_np_liqtycustcolumn_cqtyf');
								if(empty($cqtyf)){
									$cqtyf = 'Other1';
								}
								$EstimateLine->set($cqtyf , $Qty);
							}
							
							$Estimate->addEstimateLine($EstimateLine);
						}
					}
					
					//pgdf compatibility
					if($this->get_wc_fee_plugin_check()){
						$dc_gt_fees = (isset($invoice_data['dc_gt_fees']))?$invoice_data['dc_gt_fees']:array();
						if(is_array($dc_gt_fees) && count($dc_gt_fees)){
							foreach($dc_gt_fees as $df){
								$EstimateLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
								
								$UnitPrice = $df['_line_total'];
								$Qty = 1;
								$Amount = $Qty*$UnitPrice;
								
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$invoice_data);
								$EstimateLine->setItemListID($df_ItemRef);
								
								$EstimateLine->setRate($UnitPrice);
								$EstimateLine->setQuantity($Qty);
								$EstimateLine->setAmount($Amount);
								
								$EstimateLine->setDescription($df['name']);
								
								$_line_tax = $df['_line_tax'];
								if($_line_tax && $qbo_is_sales_tax){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$EstimateLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$EstimateLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
								
								/*
								if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
									$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
									if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
										$EstimateLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
								*/
								
								$Estimate->addEstimateLine($EstimateLine);
							}
							
						}						
					}
					
					//pw_gift_card compatibility
					if($this->is_plugin_active('pw-woocommerce-gift-cards','pw-gift-cards') && $this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed') && !empty($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'))){
						$pw_gift_card = (isset($invoice_data['pw_gift_card']))?$invoice_data['pw_gift_card']:array();
						if(is_array($pw_gift_card) && count($pw_gift_card)){
							foreach($pw_gift_card as $pgc){
								$pgc_amount = $pgc['amount'];
								if($pgc_amount > 0){
									$pgc_amount = -1 * abs($pgc_amount);
								}
								
								$Qty = 1;
								$Description = $pgc['card_number'];
								$EstimateLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
								$EstimateLine->setItemListID($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'));
								$EstimateLine->setRate($pgc_amount);
								$EstimateLine->setQuantity($Qty);
								$EstimateLine->setAmount($pgc_amount);
								
								$EstimateLine->setDescription($Description);
								
								/*
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$EstimateLine->setSalesTaxCodeListID($zero_rated_tax_code);
								*/
								
								$Estimate->addEstimateLine($EstimateLine);
							}
						}
					}
					
					/*Add Estimate Coupons*/
					$used_coupons  = (isset($invoice_data['used_coupons']))?$invoice_data['used_coupons']:array();
					$qbo_is_discount_allowed = true;
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						$qbo_is_discount_allowed = false;
					}
					
					if($qbo_is_discount_allowed && count($used_coupons)){
						foreach($used_coupons as $coupon){
							$coupon_name = $coupon['name'];
							$coupon_discount_amount = $coupon['discount_amount'];
							$coupon_discount_amount = -1 * abs($coupon_discount_amount);
							$coupon_discount_amount_tax = $coupon['discount_amount_tax'];

							$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
							$DiscountLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
							$DiscountLine->setItemListID($coupon_product_arr["ItemRef"]);
							if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
								$DiscountLine->setClassListID($coupon_product_arr["ClassRef"]);
							}
							$DiscountLine->setDescription($coupon_product_arr['Description']);
							$DiscountLine->setRate($coupon_discount_amount);
							
							if($coupon_product_arr['qbo_product_type'] != 'Discount'){
								//$DiscountLine->setQuantity(1);
								$DiscountLine->setAmount($coupon_discount_amount);
							}							
							
							if($qbo_is_sales_tax){
								if($coupon_discount_amount_tax > 0 || $is_tax_applied){
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										$DiscountLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}								
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									//$DiscountLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
								}
							}

							$Estimate->addEstimateLine($DiscountLine);
						}
					}					
					
					/*Add Estimate Shipping*/
					$shipping_details  = (isset($invoice_data['shipping_details']))?$invoice_data['shipping_details']:array();
					
					$sp_arr_first = array();					
					if(is_array($shipping_details) && !empty($shipping_details)){
						foreach($shipping_details as $sd_k => $sd_v){
							
							$shipping_method = '';
							$shipping_method_name = '';
							$shipping_taxes = '';
							$smt_id = 0;
							if(isset($shipping_details[$sd_k])){
								if($this->get_array_isset($shipping_details[$sd_k],'type','')=='shipping'){
									$shipping_method_id = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
									if($shipping_method_id!=''){
										if(isset($shipping_details[$sd_k]['instance_id']) && $shipping_details[$sd_k]['instance_id']>0){
											$shipping_method = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
											$smt_id = (int) $this->get_array_isset($shipping_details[$sd_k],'instance_id',0);
										}else{
											$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'',$sd_v);
											$smt_id = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'id',$sd_v);
										}
									}
									
									$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
									$shipping_method_name =  $this->get_array_isset($shipping_details[$sd_k],'name','',true,30);
									$shipping_taxes = $this->get_array_isset($shipping_details[$sd_k],'taxes','');
								}
							}
							
							$shipping_product_arr = array();
							
							if($shipping_method!=''){
								if(!$qbo_is_shipping_allowed){
									if($smt_id>0){
										$smt_id_str = $shipping_method.':'.$smt_id;
										$shipping_product_arr = $this->get_mapped_shipping_product($smt_id_str,$sd_v,true);
									}
									
									if(!count($shipping_product_arr) || empty($shipping_product_arr['ItemRef'])){
										$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method,$sd_v);
									}
									
									if(empty($sp_arr_first)){
										$sp_arr_first = $shipping_product_arr;
									}
									
									$ShippingLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
									$ShippingLine->setItemListID($shipping_product_arr["ItemRef"]);
									if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
										$ShippingLine->setClassListID($shipping_product_arr["ClassRef"]);
									}
									$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										$ShippingLine->setDescription($shipping_description);
									}
									
									//$ShippingLine->setQuantity(1);
									if(!$this->check_sh_wcmslscqb_hash()){
										$ShippingLine->setRate($order_shipping_total);
										$ShippingLine->setAmount($order_shipping_total);
									}else{
										$ShippingLine->setRate($sd_v['cost']);						
										$ShippingLine->setAmount($sd_v['cost']);
									}
									
									if($qbo_is_sales_tax){
										if(($this->check_sh_wcmslscqb_hash() && $sd_v['total_tax']>0) || (!$this->check_sh_wcmslscqb_hash() && $_order_shipping_tax>0)){
											$TaxCodeRef = '';
											if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
												$TaxCodeRef =$qbo_tax_code;
											}
											
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}
											if(empty($TaxCodeRef)){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
											}
											
											if($TaxCodeRef!=''){
												$ShippingLine->setSalesTaxCodeListID($TaxCodeRef);
											}
										}else{
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
											}
											$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
										}								
									}
									
									if(!$qbo_is_sales_tax){
										if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
											$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
										}else{
											$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
										}
										$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
									}
									
									if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											//$ShippingLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
										}
									}
									
									$Estimate->addEstimateLine($ShippingLine);

								}
							}
							
							if(!$this->check_sh_wcmslscqb_hash()){
								break;
							}
						}
					}
					
					if(!$is_taxjar_active){
						//$order_shipping_total+=$_order_shipping_tax;
					}
					
					//TaxJar Line
					if($is_taxjar_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
						$taxjar_item = $this->get_option('mw_wc_qbo_desk_wc_taxjar_map_qbo_product');
						if(empty($taxjar_item)){
							$taxjar_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($taxjar_item);
						$ExtLine->setDescription('TaxJar - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Estimate->addEstimateLine($ExtLine);
					}
					
					//Avatax Line
					if($is_avatax_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
						$avatax_item = $this->get_option('mw_wc_qbo_desk_wc_avatax_map_qbo_product');
						if(empty($avatax_item)){
							$avatax_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($avatax_item);
						$ExtLine->setDescription('Avatax - QBD Line Item');
						//$ExtLine->setRate($_order_tax);
						//$ExtLine->setQuantity(1);
						$ExtLine->setAmount($_order_total_tax);

						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Estimate->addEstimateLine($ExtLine);
					}
					
					//Order Tax as Line Item					
					if($is_so_tax_as_li && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
						$otli_item = $this->get_option('mw_wc_qbo_desk_otli_qbd_product');
						if(empty($otli_item)){
							$otli_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($otli_item);
						
						$Description = '';
						if(is_array($tax_details) && count($tax_details)){
							if(isset($tax_details[0]['label'])){
								$Description = $tax_details[0]['label'];
							}
							
							if(isset($tax_details[1]) && $tax_details[1]['label']){
								if(!empty(tax_details[1]['label'])){
									$Description = $Description.', '.$tax_details[1]['label'];
								}
							}
						}
						
						if(empty($Description)){
							$Description = 'Woocommerce Order Tax - QBD Line Item';
						}						
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";
							//$ExtLine->setRate($_order_tax_base_currency);
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{
							//$ExtLine->setRate($_order_tax);
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDescription($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Estimate->addEstimateLine($ExtLine);
					}
					
					/**/
					$qbd_subtotal_product = $this->get_option('mw_wc_qbo_desk_default_subtotal_product');
					if(!empty($qbd_subtotal_product)){
						$StLine = new QuickBooks_QBXML_Object_Estimate_EstimateLine();
						$StLine->setItemListID($qbd_subtotal_product);
						$Estimate->addEstimateLine($StLine);
					}
					
					//
					if($is_tax_applied){
						$TaxCodeRef =$qbo_tax_code;
						if($TaxCodeRef!=''){
							if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')!= 'Sales_Tax_Codes'){
								//$Estimate->setSalesTaxItemListID($TaxCodeRef);
								$Estimate->set('ItemSalesTaxRef ListID', $TaxCodeRef);
							}							
						}
					}
					
					$is_as_addr_format = true;

					$billing_name = $this->get_array_isset($invoice_data,'_billing_first_name','',true).' '.$this->get_array_isset($invoice_data,'_billing_last_name','',true);
					$billing_name_fl = $billing_name;

					$country = $this->get_array_isset($invoice_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);
					//$country = '';
					
					$_billing_company = $this->get_array_isset($invoice_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($invoice_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($invoice_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($invoice_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($invoice_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($invoice_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($invoice_data,'_billing_phone','',true);
					
					/**/
					$skip_billing_address = false;
					if($this->option_checked('mw_wc_qbo_desk_use_qb_ba_for_eqc') && is_array($extra) && isset($extra['existing_qbo_user_id']) && !empty($extra['existing_qbo_user_id'])){
						$skip_billing_address = true;
					}
					
					if(!$skip_billing_address){
						if(!$is_as_addr_format){
							if($_billing_company!=''){
								$Estimate->setBillAddress(
									$billing_name,$_billing_company,$_billing_address_1,$_billing_address_2,'',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}else{
								$Estimate->setBillAddress(
									$billing_name,$_billing_address_1,$_billing_address_2,'','',$_billing_city,$_billing_state,'',$_billing_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_billing_first_name','',true),$this->get_array_isset($invoice_data,'_billing_last_name','',true),$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
							$r_fa = $this->get_ord_baf_addrs($rfs_arr,$invoice_data);
							$Estimate->setBillAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}
					}
					
					if($this->get_array_isset($invoice_data,'_shipping_first_name','',true)!='' || $this->get_array_isset($invoice_data,'_shipping_company','',true)!=''){
						$shipping_name = $this->get_array_isset($invoice_data,'_shipping_first_name','',true).' '.$this->get_array_isset($invoice_data,'_shipping_last_name','',true);

						$country = $this->get_array_isset($invoice_data,'_shipping_country','',true);
						$country = $this->get_country_name_from_code($country);
						
						$_shipping_company = $this->get_array_isset($invoice_data,'_shipping_company','',true);
						$_shipping_address_1 = $this->get_array_isset($invoice_data,'_shipping_address_1','',true);
						$_shipping_address_2 = $this->get_array_isset($invoice_data,'_shipping_address_2','',true);
						$_shipping_city = $this->get_array_isset($invoice_data,'_shipping_city','',true);
						$_shipping_state = $this->get_array_isset($invoice_data,'_shipping_state','',true);
						$_shipping_postcode = $this->get_array_isset($invoice_data,'_shipping_postcode','',true);
						
						if(!$is_as_addr_format){
							if($_shipping_company!=''){
								$Estimate->setShipAddress(
									$shipping_name,$_shipping_company,$_shipping_address_1,$_shipping_address_2,'',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}else{
								$Estimate->setShipAddress(
									$shipping_name,$_shipping_address_1,$_shipping_address_2,'','',$_shipping_city,$_shipping_state,'',$_shipping_postcode,
									$country
								);
							}
						}else{
							$rfs_arr = array($this->get_array_isset($invoice_data,'_shipping_first_name','',true),$this->get_array_isset($invoice_data,'_shipping_last_name','',true),$_shipping_company,$_shipping_address_1,$_shipping_address_2,$_shipping_city,$_shipping_state,$_shipping_postcode,$country);
							$r_fa = $this->get_ord_saf_addrs($rfs_arr,$invoice_data);
							$Estimate->setShipAddress(
								$this->get_array_isset($r_fa,0,'',true),
								$this->get_array_isset($r_fa,1,'',true),
								$this->get_array_isset($r_fa,2,'',true),
								$this->get_array_isset($r_fa,3,'',true),
								$this->get_array_isset($r_fa,4,'',true)
								
							);
						}						
						
					}
					
					$Q_Memo = '';
					$customer_note = $this->get_array_isset($invoice_data,'customer_note','');
					if($this->option_checked('mw_wc_qbo_desk_invoice_memo')){
						$Q_Memo = $customer_note;
					}
					
					if($this->option_checked('mw_wc_qbo_desk_cname_into_memo')){
						$cname_memo = $billing_name_fl;
						$Q_Memo = $cname_memo;
					}
					
					$Q_Memo = trim($Q_Memo);
					/*
					if($this->option_checked('mw_wc_qbo_desk_use_qb_in_sr_so_ref_num')){
						if(!empty($Q_Memo)){
							$Q_Memo.= PHP_EOL . 'Order: '. $DocNumber;
						}else{
							$Q_Memo = 'Order: '. $DocNumber;
						}						
					}
					*/
					
					$Estimate->setMemo($Q_Memo);
					
					//New - Extra Fields
					$mw_wc_qbo_desk_ord_push_rep_othername = $this->get_option('mw_wc_qbo_desk_ord_push_rep_othername');
					if($mw_wc_qbo_desk_ord_push_rep_othername!=''){
						$Estimate->setSalesRepListID($mw_wc_qbo_desk_ord_push_rep_othername);
					}
					
					//WWLC CF SalesRep QBD SalesRep Map
					if($wc_cus_id>0 && $this->is_plugin_active('woocommerce-wholesale-lead-capture','woocommerce-wholesale-lead-capture.bootstrap') && $this->option_checked('mw_wc_qbo_desk_compt_wwlc_rf_srm_ed')){						
						$wwlc_cf_rep_map = get_option('mw_wc_qbo_desk_wwlc_cf_rep_map');
						if(is_array($wwlc_cf_rep_map) && count($wwlc_cf_rep_map)){
							$wwlc_cf_rep = get_user_meta($wc_cus_id,'wwlc_cf_rep',true);
							if(!empty($wwlc_cf_rep)){
								if(isset($wwlc_cf_rep_map[$wwlc_cf_rep]) && !empty($wwlc_cf_rep_map[$wwlc_cf_rep])){
									$qbd_salesrep_id = $wwlc_cf_rep_map[$wwlc_cf_rep];									
									//$Estimate->setSalesRepListID($qbd_salesrep_id);
									$Estimate->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					//WCFE CF SalesRep QBD SalesRep Map
					if($this->is_plugin_active('woocommerce-checkout-field-editor') && $this->option_checked('mw_wc_qbo_desk_compt_wcfe_rf_srm_ed')){
						$wcfe_cf_rep_map = get_option('mw_wc_qbo_desk_wcfe_cf_rep_map');
						if(is_array($wcfe_cf_rep_map) && count($wcfe_cf_rep_map)){
							$wcfe_cf_rep = $this->get_array_isset($invoice_data,'sales-rep','');
							if(!empty($wcfe_cf_rep)){
								if(isset($wcfe_cf_rep_map[$wcfe_cf_rep]) && !empty($wcfe_cf_rep_map[$wcfe_cf_rep])){
									$qbd_salesrep_id = $wcfe_cf_rep_map[$wcfe_cf_rep];
									$Estimate->set('SalesRepRef ListID',$qbd_salesrep_id);
								}
							}
						}
					}
					
					if($this->is_plugin_active('woocommerce-gateway-purchase-order') && $this->option_checked('mw_wc_qbo_desk_wpopg_po_support')){
						//if($_payment_method == 'woocommerce_gateway_purchase_order'){}
						$_po_number = $this->get_array_isset($invoice_data,'_po_number','',true);
						if($_po_number!=''){
							$Estimate->setPONumber($_po_number);
						}else{
							$Estimate->setPONumber('Web');
						}
					}
					
					$mw_wc_qbo_desk_ord_push_entered_by = $this->get_option('mw_wc_qbo_desk_ord_push_entered_by');
					if($mw_wc_qbo_desk_ord_push_entered_by!=''){
						$Estimate->setOther($mw_wc_qbo_desk_ord_push_entered_by);
					}
					
					if($this->wacs_base_cur_enabled()){					
						$base_currency = get_woocommerce_currency();
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
					}else{
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					}
					
					$term_id_str = $this->get_array_isset($pm_map_data,'term_id_str','',true);
					if($term_id_str!=''){
						$Estimate->setTermsListID($term_id_str);
					}
					
					/*
					$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','',true);
					if($qb_p_method_id!=''){
						//$Estimate->set('PaymentMethodRef ListID',$qb_p_method_id);
					}
					*/
					
					/*
					if(count($sp_arr_first)){
						$qb_shipmethod_id = $this->get_array_isset($sp_arr_first,'qb_shipmethod_id','',true);
						if($qb_shipmethod_id!=''){
							//$Estimate->set('ShipMethodRef ListID',$qb_shipmethod_id);
						}
					}
					*/
					
					$cf_map_data = array();
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-field-mapping') && $this->check_sh_cfm_hash()){
						$cf_map_data = $this->get_cf_map_data();
					}
					
					if(is_array($cf_map_data) && count($cf_map_data)){
						$qacfm = $this->get_qbo_avl_cf_map_fields();
						foreach($cf_map_data as $wcfm_k => $wcfm_v){
							$wcfm_k = trim($wcfm_k);
							$wcfm_v = trim($wcfm_v);
							
							if(!empty($wcfm_v)){
								$wcf_val = '';
								switch ($wcfm_k) {									
									case "wc_order_shipping_method_name":
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
								
								if(!empty($wcf_val) && isset($qacfm[$wcfm_v])){
									$qbo_cf_arr = array();
									switch ($wcfm_v) {
										case "":								
											break;
											
										default:
										try {
											if(is_array($qbo_cf_arr) && count($qbo_cf_arr) && isset($qbo_cf_arr[$wcfm_v])){
												//
											}else{
												$qacfm_naf = $this->get_qbo_avl_cf_map_fields(true);
												$ivqf = true;
												if(is_array($qacfm_naf) && count($qacfm_naf) && isset($qacfm_naf[$wcfm_v])){
													$ivqf = false;
												}
												if($ivqf){
													$Estimate->set("{$wcfm_v}",$wcf_val);
												}else{
													if($wcfm_v == 'ShipTo'){
														$cfst = $this->get_fr_cf_ship_to($wcf_val);
														$Estimate->setShipAddress(
															$this->get_array_isset($cfst,0,'',true),
															$this->get_array_isset($cfst,1,'',true),
															$this->get_array_isset($cfst,2,'',true),
															$this->get_array_isset($cfst,3,'',true),
															$this->get_array_isset($cfst,4,'',true)
															
														);
													}
												}
											}
										}catch(Exception $e) {
											$cfm_err = $e->getMessage();
										}
									}
								}
							}
						}
					}
					
					/*Tracking Num Compatibility*/
					if($this->is_plugin_active('woocommerce-shipment-tracking') && $this->option_checked('mw_wc_qbo_desk_w_shp_track')){
						$_wc_shipment_tracking_items = $this->get_array_isset($invoice_data,'_wc_shipment_tracking_items','',true);
						
						$wf_wc_shipment_source = $this->get_array_isset($invoice_data,'wf_wc_shipment_source','',true);
						$wf_wc_shipment_result = $this->get_array_isset($invoice_data,'wf_wc_shipment_result','',true);
						
						if($_wc_shipment_tracking_items!='' || $wf_wc_shipment_source!=''){
							if($_wc_shipment_tracking_items!=''){
								$wsti_data = $this->wc_get_wst_data($_wc_shipment_tracking_items);
							}else{
								$wsti_data = $this->wc_get_wst_data_pro($wf_wc_shipment_source,$wf_wc_shipment_result);
							}
							if(count($wsti_data)){
								$tracking_provider = $this->get_array_isset($wsti_data,'tracking_provider','',true);
								$tracking_number = $this->get_array_isset($wsti_data,'tracking_number','',true);
								$date_shipped = $this->get_array_isset($wsti_data,'date_shipped','',true);
								if($tracking_provider!=''){
									$wst_tp_qsm_mp = get_option('mw_wc_qbo_desk_compt_wshptrack_tp_mv');
									if(is_array($wst_tp_qsm_mp) && isset($wst_tp_qsm_mp[$tracking_provider]) && !empty($wst_tp_qsm_mp[$tracking_provider])){
										$qb_sm_id = $wst_tp_qsm_mp[$tracking_provider];
										//$Estimate->set('ShipMethodRef ListID',$qb_sm_id);
									}									
								}
								$Estimate->set('Other',$tracking_number);
								//$Estimate->set('ShipDate',$date_shipped);
							}
						}
					}
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$fb_cust_term = $this->get_array_isset($invoice_data,'net_terms','');							
							$fb_cust_term = $this->sanitize($fb_cust_term);
							if(!empty($fb_cust_term)){
								$tm_r = $this->get_row($wpdb->prepare("SELECT qbd_id FROM `{$wpdb->prefix}mw_wc_qbo_desk_qbd_list_term` WHERE name = %s",$fb_cust_term));
								if(is_array($tm_r) && !empty($tm_r) && isset($tm_r['qbd_id']) && !empty($tm_r['qbd_id'])){
									$qbd_term_id = $tm_r['qbd_id'];
									$Estimate->set('TermsRef ListID',$qbd_term_id);
								}
							}
						}
					}
					
					//$this->_p($Estimate);
					
					$qbxml = $Estimate->asQBXML(QUICKBOOKS_ADD_ESTIMATE,null,$this->get_qbxml_locale());
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
					$qbxml = $this->qbxml_search_replace($qbxml);

					return $qbxml;
					
					/**/
					
				}
			}
		}
	}
	
	//31-10-2017
	public function get_ord_baf_addrs($ba_data, $data_arr=array(), $is_cus=false){
		//$f_ba_data = array('line1'=>'','line2'=>'','line3'=>'','line4'=>'','line5'=>'');
		$f_ba_data = array();
		if(is_array($ba_data) && count($ba_data)){				
			$mw_wc_qbo_desk_ord_bill_addr_map = $this->get_option('mw_wc_qbo_desk_ord_bill_addr_map');
			if(empty($mw_wc_qbo_desk_ord_bill_addr_map)){
				$mw_wc_qbo_desk_ord_bill_addr_map = $this->get_default_ord_ba();
			}
			$fs_srch_arr = array('{_billing_first_name}','{_billing_last_name}','{_billing_company}','{_billing_address_1}','{_billing_address_2}','{_billing_city}','{_billing_state}','{_billing_postcode}','{_billing_phone}','{_billing_country}');
			$fsa_arr = explode(PHP_EOL,$mw_wc_qbo_desk_ord_bill_addr_map);
			
			/**/
			$ext_fk = $this->ord_ba_ext_formats();
			if(is_array($ext_fk) && !empty($ext_fk) && is_array($data_arr) && !empty($data_arr)){
				foreach($ext_fk as $fk => $fv){
					$fs_srch_arr[] = $fv;
					$ba_data[] = (isset($data_arr[$fk]))?$this->get_array_isset($data_arr,$fk,'',true):'';
				}
			}
			
			//$this->_p($fs_srch_arr);
			//$this->_p($fsa_arr);
			//$this->_p($ba_data);
			
			//United States (US) Minor Outlying Islands
			//United States (US) Virgin Islands
			$skip_us_country = $this->option_checked('mw_wc_qbo_desk_cus_skip_usa_country');
			if($skip_us_country && (end($ba_data) == 'United States (US)' || end($ba_data) == 'US')){				
				$l_key = key($ba_data);
				if(isset($ba_data[$l_key])){
					$ba_data[$l_key] = '';
				}
			}
			
			if(is_array($fsa_arr) && count($fsa_arr)){
				foreach($fsa_arr as $sk => $sd){
					if($sk>4){break;}
					//$sk_c = $sk+1;
					$sd = trim($sd);
					$fxa = str_replace($fs_srch_arr,$ba_data,$sd);
					$fxa = trim($fxa);
					//$f_ba_data['line'.$sk_c] = $fxa;
					$f_ba_data[] = $fxa;
				}
			}
		}
		$f_ba_data = array_filter($f_ba_data);
		$f_ba_data = array_values($f_ba_data);
		//$this->_p($f_ba_data);
		return $f_ba_data;
	}
	
	public function get_ord_saf_addrs($sa_data, $data_arr=array(), $is_cus=false){		
		//$f_sa_data = array('line1'=>'','line2'=>'','line3'=>'','line4'=>'','line5'=>'');
		$f_sa_data = array();
		if(is_array($sa_data) && count($sa_data)){
			$mw_wc_qbo_desk_ord_ship_addr_map = $this->get_option('mw_wc_qbo_desk_ord_ship_addr_map');
			if(empty($mw_wc_qbo_desk_ord_ship_addr_map)){
				$mw_wc_qbo_desk_ord_ship_addr_map = $this->get_default_ord_sa();
			}
			
			$fs_srch_arr = array('{_shipping_first_name}','{_shipping_last_name}','{_shipping_company}','{_shipping_address_1}','{_shipping_address_2}','{_shipping_city}','{_shipping_state}','{_shipping_postcode}','{_shipping_country}');
			$fsa_arr = explode(PHP_EOL,$mw_wc_qbo_desk_ord_ship_addr_map);
			
			/**/
			$ext_fk = $this->ord_sa_ext_formats();
			if(is_array($ext_fk) && !empty($ext_fk) && is_array($data_arr) && !empty($data_arr)){
				foreach($ext_fk as $fk => $fv){
					$fs_srch_arr[] = $fv;
					$sa_data[] = (isset($data_arr[$fk]))?$this->get_array_isset($data_arr,$fk,'',true):'';
				}
			}
			
			//$this->_p($fs_srch_arr);
			//$this->_p($fsa_arr);
			//$this->_p($sa_data);
			
			$skip_us_country = $this->option_checked('mw_wc_qbo_desk_cus_skip_usa_country');
			if($skip_us_country && (end($sa_data) == 'United States (US)' || end($sa_data) == 'US')){				
				$l_key = key($sa_data);
				if(isset($sa_data[$l_key])){
					$sa_data[$l_key] = '';
				}
			}
			
			if(is_array($fsa_arr) && count($fsa_arr)){
				foreach($fsa_arr as $sk => $sd){
					if($sk>4){break;}
					//$sk_c = $sk+1;
					$sd = trim($sd);
					$fxa = str_replace($fs_srch_arr,$sa_data,$sd);
					$fxa = trim($fxa);
					//$f_sa_data['line'.$sk_c] = $fxa;
					$f_sa_data[] = $fxa;
				}
			}
		}
		$f_sa_data = array_filter($f_sa_data);
		$f_sa_data = array_values($f_sa_data);
	
		//$this->_p($f_sa_data);
		return $f_sa_data;
	}
	
	//
	public function get_owner_id(){
		return '0';
	}
	
	public function qbd_dext_type_str(){
		return 'STR255TYPE';
	}
	
	public function GetCustomerQbxml($id,$is_guest=false,$edit_sequence='',$qb_list_id=''){
		if($is_guest){
			$edit_sequence = '';
		}
		
		if(!empty($edit_sequence) && empty($qb_list_id)){
			return false;
		}
		
		if($this->is_qwc_connected()){
			$ext_qbxml = '';
			if($is_guest){
				$order_id = (int) $id;
				$customer_data = $this->get_wc_customer_info_from_order($order_id);
			}else{
				$customer_id = (int) $id;
				$customer_data = $this->get_wc_customer_info($customer_id);
			}

			if(is_array($customer_data) && count($customer_data)){
				$Customer = new QuickBooks_QBXML_Object_Customer();

				$Customer->setFirstName($customer_data['firstname']);
				$Customer->setLastName($customer_data['lastname']);
				//
				$display_name = $customer_data['display_name'];
				global $wpdb;
				
				if($this->option_checked('mw_wc_qbo_desk_cus_push_append_client_id')){
					if($this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','d_name',$display_name)){
						if($is_guest){
							if($customer_data['email']!=''){
								$display_name.= '-'.$order_id;
							}							
						}else{
							$display_name.= '-'.$customer_id;
						}						
					}
				}
				
				
				$Customer->setName($display_name);
				$Customer->setCompanyName($customer_data['company']);
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = 0;
						if($is_guest){
							$c_account_number = (int) get_post_meta($order_id,'account_number',true);							
						}else{
							$coi_dt = $this->get_row("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_customer_user' AND meta_value = {$customer_id} ORDER BY post_id DESC LIMIT 0,1 ");
							if(is_array($coi_dt) && isset($coi_dt['post_id'])){
								$c_order_id = (int) $coi_dt['post_id'];
								$c_account_number = (int) get_post_meta($c_order_id,'account_number',true);
							}
						}
						
						if($c_account_number > 0){
							$Customer->set('AccountNumber',$c_account_number);
						}
					}
				}				
				
				/*
				if(empty($edit_sequence)){
					$Customer->setEmail($customer_data['email']);
				}
				*/
				
				$Customer->setEmail($customer_data['email']);
				
				$f_l_name = $customer_data['firstname'].' '.$customer_data['lastname'];
				
				//New - Extra Fields
				$mw_wc_qbo_desk_cus_push_customertype = $this->get_option('mw_wc_qbo_desk_cus_push_customertype');
				if($mw_wc_qbo_desk_cus_push_customertype!=''){
					$Customer->setCustomerTypeListID($mw_wc_qbo_desk_cus_push_customertype);
				}
				
				//Wc user Role - QBD Customer Type Map
				if($this->option_checked('mw_wc_qbo_desk_compt_wur_qct_map_ed') && !$is_guest && $customer_id){
					$user_data = get_userdata($customer_id);
					//if($this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){}
					$user_role = '';
					if(is_object($user_data) && isset($user_data->roles) && is_array($user_data->roles)){
						if(is_array($user_data->roles) && count($user_data->roles)){
							$ur_arr = $user_data->roles;
							$user_role = $ur_arr[0];
						}
					}
					
					if(!empty($user_role)){
						$wur_qct_map = get_option('mw_wc_qbo_desk_np_wur_qct_map');
						if(is_array($wur_qct_map) && count($wur_qct_map)){
							if(isset($wur_qct_map[$user_role]) && !empty($wur_qct_map[$user_role])){
								$qbd_customertype_id = $wur_qct_map[$user_role];
								$Customer->setCustomerTypeListID($qbd_customertype_id);
							}
						}
					}
				}
				
				$mw_wc_qbo_desk_cus_push_rep_othername = $this->get_option('mw_wc_qbo_desk_cus_push_rep_othername');
				if($mw_wc_qbo_desk_cus_push_rep_othername!=''){
					$Customer->setSalesRepListID($mw_wc_qbo_desk_cus_push_rep_othername);
				}
				
				$phone = $this->get_array_isset($customer_data,'billing_phone','',true,21);
				if($phone!=''){
					$Customer->setPhone($phone);
				}
				
				/**/
				if(!empty($this->get_option('mw_wc_qbo_desk_tax_rule_taxable'))){
					$Customer->set('SalesTaxCodeRef ListID',$this->get_option('mw_wc_qbo_desk_tax_rule_taxable'));
				}				
				
				//ccf map
				if($this->is_plugin_active('myworks-quickbooks-desktop-easton-customer-custom-field-mapping') && $this->check_sh_easton_ccfm_hash() && $this->option_checked('mw_wc_qbo_desk_compt_np_wc_cus_cf_map')){
					$mw_wc_qbo_desk_compt_nwcfm_fm = get_option('mw_wc_qbo_desk_compt_nwcfm_fm');
					if(is_array($mw_wc_qbo_desk_compt_nwcfm_fm) && count($mw_wc_qbo_desk_compt_nwcfm_fm)){
						$c_notes = '';
						foreach($mw_wc_qbo_desk_compt_nwcfm_fm as $k => $v){
							if(isset($customer_data[$k])){
								//
								if($k== 'gf_userTitle' && $v == 'CustomerNamePrefix'){
									$prefix = $this->get_array_isset($customer_data,$k,'',true);
									if(!empty($prefix)){
										$display_name = $prefix.' '.$display_name;
										$Customer->setName($display_name);
									}									
								}								
								
								if($v == 'Notes'){
									$tmp_note = $this->get_array_isset($customer_data,$k,'',true);
									if(!empty($tmp_note)){
										if(!empty($c_notes)){
											$c_notes.= $tmp_note . PHP_EOL;
										}else{
											$c_notes.= $tmp_note;
										}
									}
								}
							}							
							
							if($k == 'ID' && !$is_guest){
								$Customer->set('AccountNumber',$customer_id);
							}
						}
						
						foreach($mw_wc_qbo_desk_compt_nwcfm_fm as $k => $v){
							if(isset($customer_data[$k])){
								if($v == 'Source'){
									$source = $this->get_array_isset($customer_data,$k,'',true);
									if(!empty($source)){
										$DataExt = new QuickBooks_QBXML_Object_DataExt();
										$DataExt->setOwnerID($this->get_owner_id());
										$DataExt->setDataExtName('Source');
										$DataExt->set('DataExtType',$this->qbd_dext_type_str());
										$DataExt->setDataExtValue($source);
										$DataExt->setListDataExtType('Customer');
										//setListObjListID
										$DataExt->setListObjName($display_name);
										$ext_qbxml = $DataExt->asQBXML(QUICKBOOKS_MOD_DATAEXT,null,$this->get_qbxml_locale());
									}
								}
							}
						}
						
						if(!empty($c_notes)){
							$Customer->set('Notes',$c_notes);
						}
					}
					
					$mw_wc_qbo_desk_compt_nwcfm_cf_ct_fm = get_option('mw_wc_qbo_desk_compt_nwcfm_cf_ct_fm');
					if(is_array($mw_wc_qbo_desk_compt_nwcfm_cf_ct_fm) && count($mw_wc_qbo_desk_compt_nwcfm_cf_ct_fm)){
						if(isset($customer_data['gf_userSpecialty'])){
							$gf_userSpecialty = $this->get_array_isset($customer_data,'gf_userSpecialty','',true);
							foreach($mw_wc_qbo_desk_compt_nwcfm_cf_ct_fm as $k => $v){
								if($k == $gf_userSpecialty && !empty($v)){
									$Customer->setCustomerTypeListID($v);
									break;
								}
							}
						}						
					}
				}
				
				$skip_us_country = $this->option_checked('mw_wc_qbo_desk_cus_skip_usa_country');
				
				$address = $this->get_array_isset($customer_data,'billing_address_1','',true);
				if($address!=''){					
					$country = $this->get_array_isset($customer_data,'billing_country','',true);
					$country = $this->get_country_name_from_code($country);					
					
					/*
					if($skip_us_country && ($country == 'United States (US)' || $country == 'US')){
						$country = '';
					}

					$Customer->setBillAddress(
					$f_l_name,
					$address,
					$this->get_array_isset($customer_data,'billing_address_2','',true),
					
					'',
					'',
					$this->get_array_isset($customer_data,'billing_city','',true),
					$this->get_array_isset($customer_data,'billing_state','',true),
					'',
					$this->get_array_isset($customer_data,'billing_postcode','',true),
					$country
					);
					*/
					
					$rfs_arr = array(
						$this->get_array_isset($customer_data,'firstname','',true),
						$this->get_array_isset($customer_data,'lastname','',true),
						$this->get_array_isset($customer_data,'company','',true),
						$this->get_array_isset($customer_data,'billing_address_1','',true),
						$this->get_array_isset($customer_data,'billing_address_2','',true),
						$this->get_array_isset($customer_data,'billing_city','',true),
						$this->get_array_isset($customer_data,'billing_state','',true),
						$this->get_array_isset($customer_data,'billing_postcode','',true),
						$this->get_array_isset($customer_data,'billing_phone','',true),
						$country
					);
					
					$r_fa = $this->get_ord_baf_addrs($rfs_arr,$customer_data,true);
					$Customer->setBillAddress(
						$this->get_array_isset($r_fa,0,'',true),
						$this->get_array_isset($r_fa,1,'',true),
						$this->get_array_isset($r_fa,2,'',true),
						$this->get_array_isset($r_fa,3,'',true),
						$this->get_array_isset($r_fa,4,'',true)
						
					);
				}
				
				$shipping_address = $this->get_array_isset($customer_data,'shipping_address_1','',true);
				if($shipping_address!=''){
					$country = $this->get_array_isset($customer_data,'shipping_country','',true);
					$country = $this->get_country_name_from_code($country);
					
					/*
					if($skip_us_country && ($country == 'United States (US)' || $country == 'US')){
						$country = '';
					}

					$Customer->setShipAddress(
					$f_l_name,
					$shipping_address,
					$this->get_array_isset($customer_data,'shipping_address_2','',true),
					
					'',
					'',
					$this->get_array_isset($customer_data,'shipping_city','',true),
					$this->get_array_isset($customer_data,'shipping_state','',true),
					'',
					$this->get_array_isset($customer_data,'shipping_postcode','',true),
					$country
					);
					*/
					
					$rfs_arr = array(
						$this->get_array_isset($customer_data,'firstname','',true),
						$this->get_array_isset($customer_data,'lastname','',true),
						$this->get_array_isset($customer_data,'company','',true),
						$this->get_array_isset($customer_data,'shipping_address_1','',true),
						$this->get_array_isset($customer_data,'shipping_address_2','',true),
						$this->get_array_isset($customer_data,'shipping_city','',true),
						$this->get_array_isset($customer_data,'shipping_state','',true),
						$this->get_array_isset($customer_data,'shipping_postcode','',true),						
						$country
					);
					
					$r_fa = $this->get_ord_saf_addrs($rfs_arr,$customer_data,true);
					$Customer->setShipAddress(
						$this->get_array_isset($r_fa,0,'',true),
						$this->get_array_isset($r_fa,1,'',true),
						$this->get_array_isset($r_fa,2,'',true),
						$this->get_array_isset($r_fa,3,'',true),
						$this->get_array_isset($r_fa,4,'',true)
						
					);
				}
				
				/**/
				$Qbxml_Type = QUICKBOOKS_ADD_CUSTOMER;
				if(!empty($edit_sequence)){			
					$Customer->set('ListID',$qb_list_id);
					$Customer->set('EditSequence',$edit_sequence);
					$Qbxml_Type = QUICKBOOKS_MOD_CUSTOMER;
				}
				
				//$this->_p($customer_data);
				//$this->_p($Customer);
				
				$qbxml = $Customer->asQBXML($Qbxml_Type,null,$this->get_qbxml_locale());
				$qbxml.= $ext_qbxml;
				
				$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
				return $qbxml;
			}
		}
	}
	
	public function GetProductQbxml($id,$type='',$is_variation=0){
		if($this->is_qwc_connected()){
			if($is_variation){
				$product_data = $this->get_wc_variation_info($id);
			}else{
				$product_data = $this->get_wc_product_info($id);
			}
			//$this->_p($product_data);
			if(is_array($product_data) && count($product_data)){
				if($type=='NonInventory'){
					$Product = new QuickBooks_QBXML_Object_NonInventoryItem();
				}
				
				if($type=='Inventory'){
					$Product = new QuickBooks_QBXML_Object_InventoryItem();
				}
				
				if($type=='Service'){
					$Product = new QuickBooks_QBXML_Object_ServiceItem();
				}
				
				$name_replace_chars = array(':');
				$name = $this->get_array_isset($product_data,'name','',true,100,false,$name_replace_chars);
				/**/
				$name = $this->get_variation_name_from_id($name,'',$id);
				
				$sku = $this->get_array_isset($product_data,'_sku','',true);
				
				if($sku!=''){
					$Product->setName($sku);
				}else{
					$Product->setName($name);
				}				
				
				$desc = $this->get_array_isset($product_data,'short_description','',true,4000);
				if($desc==''){
					$desc = $this->get_array_isset($product_data,'description','',true,4000);
				}
				
				$_price = $this->get_array_isset($product_data,'_price',0);
				$_price = str_replace(',','',$_price);
				$_price = floatval($_price);
				
				
				
				$qbo_product_account = $this->get_option('mw_wc_qbo_desk_default_qbo_product_account');				
				
				//setPrice#setDescription#setAccountListID#setPurchaseDescription#setExpenseAccountListID#setPreferredVendorListID#setFullName#setIsActive
				
				if($type=='NonInventory'){
					if($sku!=''){
						$Product->setDescription($name);
					}else{
						$Product->setDescription($desc);
					}
					
					$Product->setPrice($_price);
					
					$Product->setAccountListID($qbo_product_account);
					//$this->_p($Product);
					$qbxml = $Product->asQBXML(QUICKBOOKS_ADD_NONINVENTORYITEM,null,$this->get_qbxml_locale());
				}
				
				if($type=='Inventory'){
					if($sku!=''){
						$Product->setSalesDescription($name);
					}else{
						$Product->setSalesDescription($desc);
					}
					
					$Product->setSalesPrice($_price);
					
					$_stock = $this->get_array_isset($product_data,'_stock',0,true);
					if($_stock==''){$_stock=0;}
					$Product->setQuantityOnHand($_stock);
					
					$Product->setIncomeAccountListID($qbo_product_account);
					
					//
					$qbo_product_account_asset = $this->get_option('mw_wc_qbo_desk_default_qbo_asset_account');
					if($qbo_product_account_asset!=''){
						$Product->setAssetAccountListID($qbo_product_account_asset);
					}
					
					$qbo_product_account_cogs = $this->get_option('mw_wc_qbo_desk_default_qbo_cogs_account');
					if($qbo_product_account_cogs!=''){
						$Product->setCOGSAccountListID($qbo_product_account_cogs);
					}
					
					$qbxml = $Product->asQBXML(QUICKBOOKS_ADD_INVENTORYITEM,null,$this->get_qbxml_locale());
				}
				
				if($type=='Service'){
					if($sku!=''){
						$Product->setDescription($name);
					}else{
						$Product->setDescription($desc);
					}
					
					$Product->setPrice($_price);
					
					$Product->setAccountListID($qbo_product_account);
					
					$qbxml = $Product->asQBXML(QUICKBOOKS_ADD_SERVICEITEM,null,$this->get_qbxml_locale());
				}
				
				//$this->_p($Product);
				$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
				return $qbxml;
				
			}
		}
	}
	
	public function GetInventoryAdjustmentAddQbxml($id,$is_variation=0){
		if($this->is_qwc_connected()){
			if($is_variation){
				$product_data = $this->get_wc_variation_info($id);
			}else{
				$product_data = $this->get_wc_product_info($id);
			}
			
			if(is_array($product_data) && count($product_data)){
				$quickbook_product_id = $this->if_qbo_product_exists($product_data,true);
				if(!empty($quickbook_product_id)){
					$InventoryAdjustment = new QuickBooks_QBXML_Object_InventoryAdjustment();
					
					$_stock = $this->get_array_isset($product_data,'_stock',0,true);
					if($_stock==''){$_stock=0;}
					
					$qbo_product_account_asset = $this->get_option('mw_wc_qbo_desk_default_qbo_asset_account');
					$InventoryAdjustment->setAccountListID($qbo_product_account_asset);
					
					$InventoryAdjustmentLine = new QuickBooks_QBXML_Object_InventoryAdjustment_InventoryAdjustmentLine();
					$InventoryAdjustmentLine->setLineItemListID($quickbook_product_id);
					$InventoryAdjustmentLine->setLineQuantityNew($_stock);
					
					$InventoryAdjustment->addInventoryAdjustmentLine($InventoryAdjustmentLine);
					
					$qbxml = $InventoryAdjustment->asQBXML('InventoryAdjustmentAdd',null,$this->get_qbxml_locale());
					$qbxml = str_replace('<NewQuantity></NewQuantity>','<NewQuantity>0</NewQuantity>',$qbxml);
					$qbxml = $this->get_qbxml_prefix('13.0').$qbxml. $this->get_qbxml_suffix();
					return $qbxml;
				}				
			}
		}
	}
	
	//
	public function GetRefundQbxml_Check($refund_id,$order_id){
		if($this->is_qwc_connected()){
			global $wpdb;
			
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$qbd_sa = 'Invoice';
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){
				$qbd_sa = 'SalesReceipt';
				//return false;
			}
			
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
				$qbd_sa = 'SalesOrder';
				//return false;
			}
			
			if($refund_id>0 && $order_id>0){
				$order = get_post($order_id);
				$refund_data = $this->get_wc_order_details_from_order($order_id,$order);
				//$this->_p($refund_data);
				if(is_array($refund_data) && !empty($refund_data)){
					$wc_inv_id = (int) $this->get_array_isset($refund_data,'wc_inv_id',0);
					$wc_cus_id = (int) $this->get_array_isset($refund_data,'customer_user',0);
					
					$refund_post = get_post($refund_id);
					if(empty($refund_post)){
						if($manual){
							$this->save_log('Export Refund Error #'.$refund_id.' Order #'.$ord_id_num,'Woocommerce refund not found!','Refund',0);
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
					
					$qbd_invoice_id = $this->check_quickbooks_invoice($wc_inv_id);
					
					if(!$qbd_invoice_id){
						$this->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id,'details'=>'QuickBooks '.strtolower($qbd_sa).' not found','status'=>0));
						return false;
					}
					
					$_order_currency = $this->get_array_isset($refund_data,'_order_currency','',true);
					
					$qbo_cus_id = '';
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$c_account_number = (int) get_post_meta($order_id,'account_number',true);
							if($c_account_number > 0){
								$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
							}
						}
					}
					
					/**/					
					if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
						$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
						if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
							$occ_mp_key = '';
							if($order->post_status == 'rx-processing'){
								$occ_mp_key = 'rx_order_status';
							}else{
								$ord_country = $this->get_array_isset($refund_data,'_shipping_country','',true);
								if(empty($ord_country)){
									$ord_country = $this->get_array_isset($refund_data,'_billing_country','',true);
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
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
						if($wc_cus_id>0){						
							$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
						}else{						
							//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
							$shipping_country = $this->get_array_isset($refund_data,'_shipping_country','');
						}
						
						if($shipping_country == 'US'){
							if($wc_cus_id>0){
								$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
							}else{
								//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
								$shipping_state = $this->get_array_isset($refund_data,'_shipping_state','');
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
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
						}
					}
					
					if(empty($qbo_cus_id)){
						if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
							if($wc_cus_id>0){
								//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}
								
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
								$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
							}
						}else{
							/*
							if($wc_cus_id>0){
								$user_info = get_userdata($wc_cus_id);
								$io_cs = false;
								if(isset($user_info->roles) && is_array($user_info->roles)){
									$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
								}
								
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							*/
							
							/**/
							$wc_user_role = '';
							if($wc_cus_id>0){
								$user_info = get_userdata($wc_cus_id);
								if(isset($user_info->roles) && is_array($user_info->roles)){
									$wc_user_role = $user_info->roles[0];
								}
							}else{
								$wc_user_role = 'wc_guest_user';
							}
							
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
										if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
											$customer_data = $this->get_wc_customer_info_from_order($order_id);
										}else{
											$customer_data = $this->get_wc_customer_info($wc_cus_id);
										}							
										$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
									}else{
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
										$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
									}
								}else{
									$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
								}
							}
							//
						}
					}				
					
					if(empty($qbo_cus_id)){
						$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Refund Error #'.$refund_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
						return false;
					}
					
					/*Count Total Amounts*/
					$_cart_discount = $this->get_array_isset($refund_data,'_cart_discount',0);
					$_cart_discount_tax = $this->get_array_isset($refund_data,'_cart_discount_tax',0);

					$_order_tax = (float) $this->get_array_isset($refund_data,'_order_tax',0);
					$_order_shipping_tax = (float) $this->get_array_isset($refund_data,'_order_shipping_tax',0);
					$_order_total_tax = ($_order_tax+$_order_shipping_tax);

					$order_shipping_total = $this->get_array_isset($refund_data,'order_shipping_total',0);
					
					if($this->wacs_base_cur_enabled()){
						$_cart_discount_base_currency = $this->get_array_isset($refund_data,'_cart_discount_base_currency',0);
						$_cart_discount_tax_base_currency = $this->get_array_isset($refund_data,'_cart_discount_tax_base_currency',0);
						
						$_order_tax_base_currency = (float) $this->get_array_isset($refund_data,'_order_tax_base_currency',0);
						$_order_shipping_tax_base_currency = (float) $this->get_array_isset($refund_data,'_order_shipping_tax_base_currency',0);
						$_order_total_tax_base_currency = ($_order_tax_base_currency+$_order_shipping_tax_base_currency);
						
						$order_shipping_total_base_currency = $this->get_array_isset($refund_data,'_order_shipping_base_currency',0);
					}
					
					$_order_total = $this->get_array_isset($refund_data,'_order_total',0);
					$is_partial = false;
					if($_order_total!=$_refund_amount){
						$is_partial = true;
					}
					
					/**/
					$order_refund_details = (isset($refund_data['order_refund_details']))?$refund_data['order_refund_details']:array();
					$r_order_tax = $this->get_array_isset($order_refund_details,'_order_tax',0);
					$r_order_shipping_tax = $this->get_array_isset($order_refund_details,'_order_shipping_tax',0);
					
					$Check = new QuickBooks_QBXML_Object_Check();
					
					$_payment_method = $this->get_array_isset($refund_data,'_payment_method','',true);					
					
					if($this->wacs_base_cur_enabled()){					
						$base_currency = get_woocommerce_currency();
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
					}else{
						$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					}
					
					$r_acc_id = $this->get_array_isset($pm_map_data,'qbo_account_id','');
					//$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','');
					$qb_cr_ba_id = $this->get_array_isset($pm_map_data,'qb_cr_ba_id','');
					
					if(!empty($qb_cr_ba_id)){
						$Check->setAccountListID($qb_cr_ba_id);
					}else{
						$Check->setAccountListID($r_acc_id);
					}					
					
					$wc_rfnd_date = $this->get_array_isset($refund_data,'refund_date','');
					$wc_rfnd_date = $this->format_date($wc_rfnd_date);
					
					$Check->setPayeeEntityListID($qbo_cus_id);
					$Check->setRefNumber($wc_inv_id.'-'.$refund_id);
					
					$Check->setTxnDate($wc_rfnd_date);
					
					//setMemo
					
					/*
					$ApplyCheckToTxn = new QuickBooks_QBXML_Object_Check_ApplyCheckToTxn();
					$ApplyCheckToTxn->setTxnID($qbd_invoice_id);
					$ApplyCheckToTxn->setAmount($_refund_amount);
					
					//$Check->addAddCheckToTxn($ApplyCheckToTxn);
					$Check->addListItem('ApplyCheckToTxn', $ApplyCheckToTxn);
					*/
					
					if(!$is_partial){
						//$Check->set('ApplyCheckToTxnAdd TxnID',$qbd_invoice_id);
						//$Check->set('ApplyCheckToTxnAdd Amount',$_refund_amount);
					}					
					
					/*Qbd settings*/
					$qbo_is_sales_tax = false;
					$qbo_company_country = 'US';
					$qbo_is_shipping_allowed = false;

					/*Tax rates*/
					$qbo_tax_code = '';
					$apply_tax = false;
					$is_tax_applied = false;
					$is_inclusive = false;

					$qbo_tax_code_shipping = '';

					$tax_rate_id = 0;
					$tax_rate_id_2 = 0;

					$tax_details = (isset($refund_data['tax_details']))?$refund_data['tax_details']:array();
					
					//Tax Totals From tax Lines
					$calc_order_tax_totals_from_tax_lines = true;					
					if($calc_order_tax_totals_from_tax_lines){
						$_order_tax = 0;
						$_order_shipping_tax = 0;
						$_order_total_tax = 0;
						
						if($this->wacs_base_cur_enabled()){
							$_order_tax_base_currency = 0;
							$_order_shipping_tax_base_currency = 0;
							$_order_total_tax_base_currency = 0;
						}
						
						if(count($tax_details)){
							foreach($tax_details as $td){
								$_order_tax+=$td['tax_amount'];
								$_order_shipping_tax+=$td['shipping_tax_amount'];
								$_order_total_tax+=$td['tax_amount']+$td['shipping_tax_amount'];
								
								if($this->wacs_base_cur_enabled()){
									$_order_tax_base_currency+=$td['tax_amount_base_currency'];
									$_order_shipping_tax_base_currency+=$td['shipping_tax_amount_base_currency'];
									$_order_total_tax_base_currency+=$td['tax_amount_base_currency']+$td['shipping_tax_amount_base_currency'];
								}
							}
						}
					}
					$_order_total_tax = $this->qbd_limit_decimal_points($_order_total_tax);
					if($this->wacs_base_cur_enabled()){
						$_order_total_tax_base_currency = $this->qbd_limit_decimal_points($_order_total_tax_base_currency);
					}
					
					//TaxJar Settings
					$is_taxjar_active = false;
					$woocommerce_taxjar_integration_settings = get_option('woocommerce_taxjar-integration_settings');
					$wc_taxjar_enable_tax_calculation = 0;
					if(is_array($woocommerce_taxjar_integration_settings) && count($woocommerce_taxjar_integration_settings)){
						if(isset($woocommerce_taxjar_integration_settings['enabled']) && $woocommerce_taxjar_integration_settings['enabled']=='yes'){
							$wc_taxjar_enable_tax_calculation = 1;
						}
					}
					
					if($this->is_plugin_active('taxjar-simplified-taxes-for-woocommerce','taxjar-woocommerce') && $this->option_checked('mw_wc_qbo_desk_wc_taxjar_support') && $wc_taxjar_enable_tax_calculation=='1'){
						$is_taxjar_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//Avatax Settings
					$is_avatax_active = false;
					$wc_avatax_enable_tax_calculation = get_option('wc_avatax_enable_tax_calculation');
					if($this->is_plugin_active('woocommerce-avatax') && $this->option_checked('mw_wc_qbo_desk_wc_avatax_support') && $wc_avatax_enable_tax_calculation=='yes'){
						$is_avatax_active = true;
						$qbo_is_sales_tax = false;
					}
					
					//
					$is_so_tax_as_li = false;
					if($this->option_checked('mw_wc_qbo_desk_odr_tax_as_li')){
						$is_so_tax_as_li = true;
						$qbo_is_sales_tax = false;
					}
					
					if($qbo_is_sales_tax){
						if(count($tax_details)){
							$tax_rate_id = $tax_details[0]['rate_id'];
						}

						if(count($tax_details)>1){
							if($tax_details[1]['tax_amount']>0){
								$tax_rate_id_2 = $tax_details[1]['rate_id'];
							}
						}

						/*
						if(count($tax_details)>1 && $qbo_is_shipping_allowed){
							foreach($tax_details as $td){
								if($td['tax_amount']==0 && $td['shipping_tax_amount']>0){
									$qbo_tax_code_shipping = $this->get_qbo_mapped_tax_code($td['rate_id'],0);
									break;
								}
							}
						}
						*/
						
						$qbo_tax_code = $this->get_qbo_mapped_tax_code($tax_rate_id,$tax_rate_id_2);
						if($qbo_tax_code!='' || $qbo_tax_code!='NON'){
							$apply_tax = true;
						}

						//$Tax_Code_Details = $this->mod_qbo_get_tx_dtls($qbo_tax_code);
						$is_qbo_dual_tax = false;

						/*
						if(count($Tax_Code_Details)){
							if($Tax_Code_Details['TaxGroup'] && count($Tax_Code_Details['TaxRateDetail'])>1){
								$is_qbo_dual_tax = true;
							}
						}

						$Tax_Rate_Ref = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']:'';
						$TaxPercent = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref);
						$Tax_Name = (isset($Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][0]['TaxRateRef_name']:'';

						$NetAmountTaxable = 0;

						if($is_qbo_dual_tax){
							$Tax_Rate_Ref_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']:'';
							$TaxPercent_2 = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_2);
							$Tax_Name_2 = (isset($Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef']))?$Tax_Code_Details['TaxRateDetail'][1]['TaxRateRef_name']:'';
							$NetAmountTaxable_2 = 0;
						}
						*/

						/*
						if($qbo_tax_code_shipping!=''){
							$Tax_Code_Details_Shipping = $this->mod_qbo_get_tx_dtls($qbo_tax_code_shipping);
							$Tax_Rate_Ref_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']:'';
							$TaxPercent_Shipping = $this->get_qbo_tax_rate_value_by_key($Tax_Rate_Ref_Shipping);
							$Tax_Name_Shipping = (isset($Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef']))?$Tax_Code_Details_Shipping['TaxRateDetail'][0]['TaxRateRef_name']:'';
							$NetAmountTaxable_Shipping = 0;
						}
						*/

						$_prices_include_tax = $this->get_array_isset($refund_data,'_prices_include_tax','no',true);
						if($qbo_is_sales_tax){
							$tax_type = $this->get_tax_type($_prices_include_tax);
							$is_inclusive = $this->is_tax_inclusive($tax_type);
						}
					}
					
					$qbo_inv_items = (isset($refund_data['qbo_inv_items']))?$refund_data['qbo_inv_items']:array();
					
					$is_bundle_order = false;
					$map_bundle_support = false;
					
					if(!$is_bundle_order){
						if(is_array($qbo_inv_items) && count($qbo_inv_items)){
							foreach($qbo_inv_items as $qbo_item){
								if($qbo_item['qbo_product_type'] == 'Group'){
									$map_bundle_support = true;
									$CheckLineGroup = new QuickBooks_QBXML_Object_Check_ItemGroupLine();
									$CheckLineGroup->setItemGroupListID($qbo_item["ItemRef"]);
									$Description = $qbo_item['Description'];
									$Qty = $qbo_item["Qty"];									
									$CheckLineGroup->setQuantity($Qty);
									
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										//$CheckLineGroup->setDesc($Description);
									}
									//TotalAmount
									$Check->addItemGroupLine($CheckLineGroup);
								}
							}
						}
					}
					
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							if($map_bundle_support && $qbo_item['qbo_product_type'] == 'Group'){
								continue;
							}
							
							$CheckLine = new QuickBooks_QBXML_Object_Check_ItemLine();							
							$CheckLine->setItemListID($qbo_item["ItemRef"]);
							if(isset($qbo_item["ClassRef"]) && $qbo_item["ClassRef"]!=''){
								$CheckLine->setClassListID($qbo_item["ClassRef"]);
							}
							$CheckLine->setCustomerListID($qbo_cus_id);
							
							$Description = $qbo_item['Description'];
							if($this->option_checked('mw_wc_qbo_desk_add_sku_af_lid')){
								$li_item_id = ($qbo_item["variation_id"]>0)?$qbo_item["variation_id"]:$qbo_item["product_id"];
								$li_sku = get_post_meta( $li_item_id, '_sku', true );
								if($li_sku!=''){
									$Description.=' ('.$li_sku.')';
								}
							}
							
							$UnitPrice = $qbo_item["UnitPrice"];
							if($this->wacs_base_cur_enabled()){
								$UnitPrice = $qbo_item["UnitPrice_base_currency"];
								$Description.= " ({$_order_currency} ".$qbo_item["UnitPrice"].")";
							}
							
							//Extra Description
							if(isset($qbo_item["Qbd_Ext_Description"])){
								$Description.= $qbo_item["Qbd_Ext_Description"];
							}
							
							if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid') || $qbo_item["AllowPvLid"]){
								$CheckLine->setDesc($Description);
							}
							
							$Qty = $qbo_item["Qty"];
							$Amount = $Qty*$UnitPrice;
							
							$CheckLine->setQuantity($Qty);
							$CheckLine->setAmount($Amount);
							
							if($this->option_checked('mw_wc_qbo_desk_compt_wqclns_ed')){
								$LotNumber  = $this->get_array_isset($qbo_item,'lot','');
								if(!empty($LotNumber)){
									$CheckLine->set('LotNumber',$LotNumber);
								}
							}
							
							if($qbo_is_sales_tax){
								if($apply_tax && $qbo_item["Taxed"]){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									if($TaxCodeRef!=''){
										//$CheckLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$CheckLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$CheckLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							/**/
							$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							if($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								/*
								$mw_warehouse = 0;
								if(isset($qbo_item["_order_item_wh"])){
									$mw_warehouse = (int) $qbo_item["_order_item_wh"];
								}else{
									$mw_warehouse = (int) $this->get_array_isset($refund_data,'mw_warehouse',0);
								}
								*/
								
								$mw_warehouse = $this->get_mwr_oiw_mw_idls($qbo_item, $refund_data);
								
								if($mw_warehouse > 0){
									$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
									if(is_array($mw_wc_qbo_desk_compt_wmior_lis_mv) && isset($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse])){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = trim($mw_wc_qbo_desk_compt_wmior_lis_mv[$mw_warehouse]);
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											if($this->is_inv_site_bin_allowed()){
												if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
													$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);											
													if(is_array($site_bin_arr) && !empty($site_bin_arr)){
														$CheckLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
														if(isset($site_bin_arr[1])){
															$CheckLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
														}
													}
												}
											}else{
												$CheckLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											}									
										}
									}
								}
							}
							
							if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){									
									if($this->is_inv_site_bin_allowed()){
										if (strpos($mw_wc_qbo_desk_compt_qbd_invt_site_ref, ':') !== false) {
											$site_bin_arr = explode(':',$mw_wc_qbo_desk_compt_qbd_invt_site_ref);
											if(is_array($site_bin_arr) && !empty($site_bin_arr)){
												$CheckLine->set('InventorySiteRef ListID' , $site_bin_arr[0]);
												if(isset($site_bin_arr[1])){
													$CheckLine->set('InventorySiteLocationRef ListID' , $site_bin_arr[1]);
												}
											}
										}
									}else{
										$CheckLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
							}
							
							$Check->addItemLine($CheckLine);
						}
					}
					
					//pgdf compatibility
					if($this->get_wc_fee_plugin_check()){
						$dc_gt_fees = (isset($refund_data['dc_gt_fees']))?$refund_data['dc_gt_fees']:array();
						if(is_array($dc_gt_fees) && count($dc_gt_fees)){
							foreach($dc_gt_fees as $df){
								$CheckLine = new QuickBooks_QBXML_Object_Check_ItemLine();
								
								$UnitPrice = $df['_line_total'];
								$Qty = 1;
								$Amount = $Qty*$UnitPrice;
								
								$df_ItemRef = $this->get_wc_fee_qbo_product($df['name'],'',$refund_data);
								$CheckLine->setItemListID($df_ItemRef);								
								
								$CheckLine->setQuantity($Qty);
								$CheckLine->setAmount($Amount);
								
								$CheckLine->setDesc($df['name']);
								
								$_line_tax = $df['_line_tax'];
								if($_line_tax && $qbo_is_sales_tax){
									$is_tax_applied = true;
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}
									
									if($TaxCodeRef!=''){
										//$CheckLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$CheckLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}
								
								/*
								if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && ($qbo_item["qbo_product_type"]=='Inventory' || $qbo_item["qbo_product_type"]=='InventoryAssembly')){
									$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
									if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
										$CheckLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
									}
								}
								*/
								
								$Check->addItemLine($CheckLine);
							}
							
						}						
					}
					
					//pw_gift_card compatibility
					if($this->is_plugin_active('pw-woocommerce-gift-cards','pw-gift-cards') && $this->option_checked('mw_wc_qbo_desk_compt_pwwgc_gpc_ed') && !empty($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'))){
						$pw_gift_card = (isset($refund_data['pw_gift_card']))?$refund_data['pw_gift_card']:array();
						if(is_array($pw_gift_card) && count($pw_gift_card)){
							foreach($pw_gift_card as $pgc){
								$pgc_amount = $pgc['amount'];
								if($pgc_amount > 0){
									$pgc_amount = -1 * abs($pgc_amount);
								}
								
								$Qty = 1;
								$Description = $pgc['card_number'];
								$CheckLine = new QuickBooks_QBXML_Object_Check_ItemLine();
								$CheckLine->setItemListID($this->get_option('mw_wc_qbo_desk_compt_pwwgc_gpc_qbo_item'));
								$CheckLine->setRate($pgc_amount);
								$CheckLine->setQuantity($Qty);
								$CheckLine->setAmount($pgc_amount);
								
								$CheckLine->setDesc($Description);
								
								/*
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								$CheckLine->setSalesTaxCodeListID($zero_rated_tax_code);
								*/
								
								$Check->addItemLine($CheckLine);
							}
						}
					}
					
					/*Add Check Coupons*/
					$used_coupons  = (isset($refund_data['used_coupons']))?$refund_data['used_coupons']:array();
					$qbo_is_discount_allowed = true;
					if($this->option_checked('mw_wc_qbo_desk_no_ad_discount_li')){
						$qbo_is_discount_allowed = false;
					}
					
					if($qbo_is_discount_allowed && count($used_coupons)){
						foreach($used_coupons as $coupon){
							$coupon_name = $coupon['name'];
							$coupon_discount_amount = $coupon['discount_amount'];
							$coupon_discount_amount = -1 * abs($coupon_discount_amount);
							$coupon_discount_amount_tax = $coupon['discount_amount_tax'];
							
							if($this->wacs_base_cur_enabled()){
								$coupon_discount_amount_base_currency = $this->get_array_isset($coupon,'discount_amount_base_currency',0);
								$coupon_discount_amount_base_currency = -1 * abs($coupon_discount_amount_base_currency);
								
								$coupon_discount_amount_tax_base_currency = $coupon['discount_amount_tax_base_currency'];
							}

							$coupon_product_arr = $this->get_mapped_coupon_product($coupon_name);
							$DiscountLine = new QuickBooks_QBXML_Object_Check_ItemLine();
							$DiscountLine->setItemListID($coupon_product_arr["ItemRef"]);
							if(isset($coupon_product_arr["ClassRef"]) && $coupon_product_arr["ClassRef"]!=''){
								$DiscountLine->setClassListID($coupon_product_arr["ClassRef"]);
							}
							$Description = $coupon_product_arr['Description'];							
							
							if($this->wacs_base_cur_enabled()){
								$Description.= " ({$_order_currency} {$coupon_discount_amount})";								
								$DiscountLine->setAmount($coupon_discount_amount_base_currency);								
							}else{								
								$DiscountLine->setAmount($coupon_discount_amount);
							}
							
							if($coupon_product_arr['qbo_product_type'] != 'Discount'){
								$DiscountLine->setQuantity(1);
							}							
							$DiscountLine->setDesc($Description);
							
							if($qbo_is_sales_tax){
								if($coupon_discount_amount_tax > 0 || $is_tax_applied){
									if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
										$TaxCodeRef =$qbo_tax_code;
									}else{
										$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
									}									
									
									if($TaxCodeRef!=''){
										//$DiscountLine->setSalesTaxCodeListID($TaxCodeRef);
									}
								}else{
									$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
									//$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
								}								
							}
							
							if(!$qbo_is_sales_tax){
								$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
								//$DiscountLine->setSalesTaxCodeListID($zero_rated_tax_code);
							}
							
							if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
								$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
								if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
									//$DiscountLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
								}
							}
							
							$Check->addItemLine($DiscountLine);
						}
					}				
					
					/*Add Check Shipping*/
					$shipping_details  = (isset($refund_data['shipping_details']))?$refund_data['shipping_details']:array();
					
					$sp_arr_first = array();					
					if(is_array($shipping_details) && !empty($shipping_details)){
						foreach($shipping_details as $sd_k => $sd_v){
							$shipping_method = '';
							$shipping_method_name = '';
							$shipping_taxes = '';
							$smt_id = 0;
							if(isset($shipping_details[$sd_k])){
								if($this->get_array_isset($shipping_details[$sd_k],'type','')=='shipping'){
									$shipping_method_id = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
									if($shipping_method_id!=''){
										if(isset($shipping_details[$sd_k]['instance_id']) && $shipping_details[$sd_k]['instance_id']>0){
											$shipping_method = $this->get_array_isset($shipping_details[$sd_k],'method_id','');
											$smt_id = (int) $this->get_array_isset($shipping_details[$sd_k],'instance_id',0);
										}else{
											$shipping_method = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'',$sd_v);
											$smt_id = $this->wc_get_sm_data_from_method_id_str($shipping_method_id,'id',$sd_v);
										}
									}
									
									$shipping_method = ($shipping_method=='')?'no_method_found':$shipping_method;
									$shipping_method_name =  $this->get_array_isset($shipping_details[$sd_k],'name','',true,30);
									$shipping_taxes = $this->get_array_isset($shipping_details[$sd_k],'taxes','');
								}
							}										
							
							$shipping_product_arr = array();
							
							if($shipping_method!=''){
								if(!$qbo_is_shipping_allowed){
									if($smt_id>0){
										$smt_id_str = $shipping_method.':'.$smt_id;
										$shipping_product_arr = $this->get_mapped_shipping_product($smt_id_str,$sd_v,true);
									}
									
									if(!count($shipping_product_arr) || empty($shipping_product_arr['ItemRef'])){
										$shipping_product_arr = $this->get_mapped_shipping_product($shipping_method,$sd_v);
									}
									
									if(empty($sp_arr_first)){
										$sp_arr_first = $shipping_product_arr;
									}
									
									$ShippingLine = new QuickBooks_QBXML_Object_Check_ItemLine();
									$ShippingLine->setItemListID($shipping_product_arr["ItemRef"]);
									if(isset($shipping_product_arr["ClassRef"]) && $shipping_product_arr["ClassRef"]!=''){
										$ShippingLine->setClassListID($shipping_product_arr["ClassRef"]);
									}
									$shipping_description = ($shipping_method_name!='')?'Shipping ('.$shipping_method_name.')':'Shipping';							
									
									if(!$this->check_sh_wcmslscqb_hash()){
										if($this->wacs_base_cur_enabled()){
											$shipping_description.= " ({$_order_currency} {$order_shipping_total})";
											//$ShippingLine->setRate($order_shipping_total_base_currency);
											$ShippingLine->setAmount($order_shipping_total_base_currency);
										}else{
											//$ShippingLine->setRate($order_shipping_total);								
											$ShippingLine->setAmount($order_shipping_total);
										}
									}else{
										//$ShippingLine->setRate($sd_v['cost']);						
										$ShippingLine->setAmount($sd_v['cost']);
									}
									
									//$ShippingLine->setQuantity(1);
									if(!$this->option_checked('mw_wc_qbo_desk_skip_os_lid')){
										$ShippingLine->setDesc($shipping_description);
									}
									
									if($qbo_is_sales_tax){
										if(($this->check_sh_wcmslscqb_hash() && $sd_v['total_tax']>0) || (!$this->check_sh_wcmslscqb_hash() && $_order_shipping_tax>0)){
											$TaxCodeRef = '';
											if($this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')== 'Sales_Tax_Codes'){
												$TaxCodeRef =$qbo_tax_code;
											}
											
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}
											if(empty($TaxCodeRef)){
												$TaxCodeRef = $this->get_option('mw_wc_qbo_desk_tax_rule_taxable');
											}
											
											if($TaxCodeRef!=''){
												//$ShippingLine->setSalesTaxCodeListID($TaxCodeRef);
											}
										}else{
											if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
												$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
											}else{
												$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
											}
											//$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
										}
									}
									
									if(!$qbo_is_sales_tax){
										if($this->is_plugin_active('myworks-quickbooks-desktop-shipping-tax-compt') && $this->check_sh_stc_hash()){
											$zero_rated_tax_code = $this->get_option('mw_wc_qbo_desk_shipping_tax_rule_taxable');
										}else{
											$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
										}
										//$ShippingLine->setSalesTaxCodeListID($zero_rated_tax_code);
									}
									
									if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
										$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
										if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
											//$ShippingLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
										}
									}

									$Check->addItemLine($ShippingLine);

								}
							}
							
							if(!$this->check_sh_wcmslscqb_hash()){
								break;
							}
						}
					}
					
					if(!$is_taxjar_active){
						//$order_shipping_total+=$_order_shipping_tax;
						if($this->wacs_base_cur_enabled()){
							//$order_shipping_total_base_currency+=$_order_shipping_tax_base_currency;
						}
					}
					
					//TaxJar Line
					if($is_taxjar_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Check_ItemLine();
						$taxjar_item = $this->get_option('mw_wc_qbo_desk_wc_taxjar_map_qbo_product');
						if(empty($taxjar_item)){
							$taxjar_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($taxjar_item);
						$Description = 'TaxJar - QBD Line Item';
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";							
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{							
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Check->addItemLine($ExtLine);
					}
					
					//Avatax Line
					if($is_avatax_active && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Check_ItemLine();
						$avatax_item = $this->get_option('mw_wc_qbo_desk_wc_avatax_map_qbo_product');
						if(empty($avatax_item)){
							$avatax_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($avatax_item);
						$Description = 'Avatax - QBD Line Item';				
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";							
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{							
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Check->addItemLine($ExtLine);
					}
					
					$force_stop = true;
					//Order Tax as Line Item					
					if(!$force_stop && $is_so_tax_as_li && count($tax_details) && $_order_total_tax >0){
						$ExtLine = new QuickBooks_QBXML_Object_Check_ItemLine();
						$otli_item = $this->get_option('mw_wc_qbo_desk_otli_qbd_product');
						if(empty($otli_item)){
							$otli_item = $this->get_option('mw_wc_qbo_desk_default_qbo_item');
						}
						
						$ExtLine->setItemListID($otli_item);
						
						$Description = '';
						if(is_array($tax_details) && count($tax_details)){
							if(isset($tax_details[0]['label'])){
								$Description = $tax_details[0]['label'];
							}
							
							if(isset($tax_details[1]) && $tax_details[1]['label']){
								if(!empty(tax_details[1]['label'])){
									$Description = $Description.', '.$tax_details[1]['label'];
								}
							}
						}
						
						if(empty($Description)){
							$Description = 'Woocommerce Order Tax - QBD Line Item';
						}
						
						if($this->wacs_base_cur_enabled()){
							$Description.= " ({$_order_currency} {$_order_total_tax})";							
							$ExtLine->setAmount($_order_total_tax_base_currency);
						}else{							
							$ExtLine->setAmount($_order_total_tax);
						}
						
						//$ExtLine->setQuantity(1);
						
						$ExtLine->setDesc($Description);
						
						if(!$qbo_is_sales_tax){
							$zero_rated_tax_code = $this->get_qbo_zero_rated_tax_code($qbo_company_country);
							//$ExtLine->setSalesTaxCodeListID($zero_rated_tax_code);
						}
						
						if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
							$mw_wc_qbo_desk_compt_qbd_invt_site_ref = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_site_ref');
							if($mw_wc_qbo_desk_compt_qbd_invt_site_ref!=''){
								//$ExtLine->set('InventorySiteRef ListID' , $mw_wc_qbo_desk_compt_qbd_invt_site_ref);
							}
						}

						$Check->addItemLine($ExtLine);
					}
					
					//
					if($is_tax_applied){
						$TaxCodeRef =$qbo_tax_code;
						if($TaxCodeRef!=''){							
							//$this->get_option('mw_wc_qbo_desk_sl_tax_map_entity')
							//Sales_Tax_Codes
							//$Check->getSalesTaxCodeListID($TaxCodeRef);//ItemSalesTaxRef ListID
						}
					}
					
					$country = $this->get_array_isset($refund_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);					
					
					$_billing_company = $this->get_array_isset($refund_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($refund_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($refund_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($refund_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($refund_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($refund_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($refund_data,'_billing_phone','',true);
					
					$rfs_arr = array($this->get_array_isset($refund_data,'_billing_first_name','',true),$this->get_array_isset($refund_data,'_billing_last_name','',true),$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
					$r_fa = $this->get_ord_baf_addrs($rfs_arr,$refund_data);
					$Check->setAddress(
						$this->get_array_isset($r_fa,0,'',true),
						$this->get_array_isset($r_fa,1,'',true),
						$this->get_array_isset($r_fa,2,'',true),
						$this->get_array_isset($r_fa,3,'',true),
						$this->get_array_isset($r_fa,4,'',true)
						
					);
					
					//$this->_p($Check);
					
					$qbxml = $Check->asQBXML(QUICKBOOKS_ADD_CHECK,null,$this->get_qbxml_locale());
					//
					$qbxml = str_replace(array('<Ref>','</Ref>'),'',$qbxml);
					//$qbxml = $this->format_xml($qbxml);
					$qbxml = $this->qbxml_search_replace($qbxml);
					
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();					

					return $qbxml;					
				}
			}
		}
	}
	
	//
	public function GetRefundQbxml_ArRCc($refund_id,$order_id){
		if($this->is_qwc_connected()){
			global $wpdb;
			
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$qbd_sa = 'Invoice';
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){
				$qbd_sa = 'SalesReceipt';
				//return false;
			}
			
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
				$qbd_sa = 'SalesOrder';
				//return false;
			}
			
			if($refund_id>0 && $order_id>0){
				$order = get_post($order_id);
				$refund_data = $this->get_wc_order_details_from_order($order_id,$order);
				
				if(is_array($refund_data) && !empty($refund_data)){
					$wc_inv_id = (int) $this->get_array_isset($refund_data,'wc_inv_id',0);
					$wc_cus_id = (int) $this->get_array_isset($refund_data,'customer_user',0);

					$refund_post = get_post($refund_id);
					if(empty($refund_post)){
						if($manual){
							$this->save_log('Export Refund Error #'.$refund_id.' Order #'.$ord_id_num,'Woocommerce refund not found!','Refund',0);
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
					
					$qbd_invoice_id = $this->check_quickbooks_invoice($wc_inv_id);
				
					if(!$qbd_invoice_id){
						$this->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$refund_id,'details'=>'QuickBooks '.strtolower($qbd_sa).' not found','status'=>0));
						return false;
					}
					
					$qbo_cus_id = '';
					
					$_order_currency = $this->get_array_isset($refund_data,'_order_currency','',true);
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
						if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
							$c_account_number = (int) get_post_meta($order_id,'account_number',true);
							if($c_account_number > 0){
								$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
							}
						}
					}
					
					/**/
					if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
					if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
						$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
						if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
							$occ_mp_key = '';
							if($order->post_status == 'rx-processing'){
								$occ_mp_key = 'rx_order_status';
							}else{
								$ord_country = $this->get_array_isset($refund_data,'_shipping_country','',true);
								if(empty($ord_country)){
									$ord_country = $this->get_array_isset($refund_data,'_billing_country','',true);
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
					
					/**/
					if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
						if($wc_cus_id>0){						
							$shipping_country = get_user_meta($wc_cus_id,'shipping_country',true);						
						}else{						
							//$shipping_country = get_post_meta($wc_inv_id,'_shipping_country',true);
							$shipping_country = $this->get_array_isset($refund_data,'_shipping_country','');
						}
						
						if($shipping_country == 'US'){
							if($wc_cus_id>0){
								$shipping_state = get_user_meta($wc_cus_id,'shipping_state',true);
							}else{
								//$shipping_state = get_post_meta($wc_inv_id,'_shipping_state',true);
								$shipping_state = $this->get_array_isset($refund_data,'_shipping_state','');
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
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
						}
					}
					
					if(empty($qbo_cus_id)){
						if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
							if($wc_cus_id>0){
								//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}
								
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
								$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
							}
						}else{
							/*
							if($wc_cus_id>0){
								$user_info = get_userdata($wc_cus_id);
								$io_cs = false;
								if(isset($user_info->roles) && is_array($user_info->roles)){
									$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
								}
								
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							*/
							
							/**/
							$wc_user_role = '';
							if($wc_cus_id>0){
								$user_info = get_userdata($wc_cus_id);
								if(isset($user_info->roles) && is_array($user_info->roles)){
									$wc_user_role = $user_info->roles[0];
								}
							}else{
								$wc_user_role = 'wc_guest_user';
							}
							
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
										if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
											$customer_data = $this->get_wc_customer_info_from_order($order_id);
										}else{
											$customer_data = $this->get_wc_customer_info($wc_cus_id);
										}							
										$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
									}else{
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
										$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
									}
								}else{
									$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
								}
							}
							//
						}
					}				
					
					if(empty($qbo_cus_id)){
						$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Payment Error #'.$refund_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
						return false;
					}
					
					//Consolibyte Lib Issue
					if(!class_exists('Quickbooks_QBXML_Object_CreditCardRefund')){
						require_once 'lib/qbo-lib/QuickBooks/QBXML/Object/CreditCardRefund.php';
					}
					
					$CreditCardRefund = new Quickbooks_QBXML_Object_CreditCardRefund();
					$CreditCardRefund->setCustomerListID($qbo_cus_id);
					
					$wc_rfnd_date = $this->get_array_isset($refund_data,'refund_date','');
					$wc_rfnd_date = $this->format_date($wc_rfnd_date);
					
					$CreditCardRefund->setTxnDate($wc_rfnd_date);
					$CreditCardRefund->setRefNumber($wc_inv_id.'-'.$refund_id);
					
					//AppliedToTxn
					$CreditCardRefund->setRefundAppliedToTxnID($qbd_invoice_id);
					$CreditCardRefund->setRefundAmount($_refund_amount);
					
					$_payment_method = $this->get_array_isset($refund_data,'_payment_method','',true);
					$_order_currency = $this->get_array_isset($refund_data,'_order_currency','',true);
					
					$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
					$r_acc_id = $this->get_array_isset($pm_map_data,'qbo_account_id','');
					
					if($r_acc_id!=''){
						$CreditCardRefund->set('RefundFromAccountRef ListID',$r_acc_id);
					}
					
					$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','');
					
					if($qb_p_method_id!=''){
						$CreditCardRefund->setPaymentMethodListID($qb_p_method_id);
					}					
					
					//setARAccountListID
					
					$_billing_first_name = $this->get_array_isset($refund_data,'_billing_first_name','',true);
					$_billing_last_name = $this->get_array_isset($refund_data,'_billing_last_name','',true);
					
					$_billing_company = $this->get_array_isset($refund_data,'_billing_company','',true);
					$_billing_address_1 = $this->get_array_isset($refund_data,'_billing_address_1','',true);
					$_billing_address_2 = $this->get_array_isset($refund_data,'_billing_address_2','',true);
					$_billing_city = $this->get_array_isset($refund_data,'_billing_city','',true);
					$_billing_state = $this->get_array_isset($refund_data,'_billing_state','',true);
					$_billing_postcode = $this->get_array_isset($refund_data,'_billing_postcode','',true);
					
					$_billing_phone = $this->get_array_isset($refund_data,'_billing_phone','',true);
					
					$country = $this->get_array_isset($refund_data,'_billing_country','',true);
					$country = $this->get_country_name_from_code($country);
					
					$rfs_arr = array($_billing_first_name,$_billing_last_name,$_billing_company,$_billing_address_1,$_billing_address_2,$_billing_city,$_billing_state,$_billing_postcode,$_billing_phone,$country);
					
					$r_fa = $this->get_ord_baf_addrs($rfs_arr,$refund_data);
					$CreditCardRefund->setAddress(
						$this->get_array_isset($r_fa,0,'',true),
						$this->get_array_isset($r_fa,1,'',true),
						$this->get_array_isset($r_fa,2,'',true),
						$this->get_array_isset($r_fa,3,'',true),
						$this->get_array_isset($r_fa,4,'',true)
						
					);
					
					$qbxml = $CreditCardRefund->asQBXML('ARRefundCreditCardAdd',null,$this->get_qbxml_locale());
					$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
					$qbxml = $this->qbxml_search_replace($qbxml);
					
					return $qbxml;
				}
			}			
		}
	}
	
	public function GetPaymentQbxml($id){
		if($this->is_qwc_connected()){			
			global $wpdb;
			$_transaction_id = $this->get_field_by_val($wpdb->postmeta,'meta_value','meta_id',$id);
			$order_id = (int) $this->get_field_by_val($wpdb->postmeta,'post_id','meta_id',$id);
			
			if(!$this->ord_pmnt_is_mt_ls_check_by_ord_id($order_id)){
				return false;
			}
			
			$payment_data = $this->wc_get_payment_details_by_txn_id($_transaction_id,$order_id);
			
			//$this->_p($payment_data);
			if(is_array($payment_data) && count($payment_data)){
				if($this->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){
					return false;
				}
				
				$qbd_sa = 'Invoice';
				if($this->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
					$qbd_sa = 'SalesOrder';
					//return false;
				}
				
				$_payment_method = $this->get_array_isset($payment_data,'payment_method','',true);
				$_payment_method_title = $this->get_array_isset($payment_data,'payment_method_title','',true);
				
				$_order_currency = $this->get_array_isset($payment_data,'order_currency','',true);
				if($this->wacs_base_cur_enabled()){
					//$p_order_details = $this->get_wc_order_details_from_order($order_id,get_post($order_id));
					$base_currency = get_woocommerce_currency();
					$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$base_currency);
				}else{
					$pm_map_data = $this->get_mapped_payment_method_data($_payment_method,$_order_currency);
				}
				
				//$this->_p($pm_map_data);
				$enable_payment = (int) $this->get_array_isset($pm_map_data,'enable_payment',0);
				if($this->wacs_base_cur_enabled()){
					//$payment_amount = get_post_meta($order_id,'_order_total_base_currency',true);
					$payment_amount = $this->get_order_base_currency_total_from_order_id($order_id);
				}else{
					$payment_amount = $this->get_array_isset($payment_data,'order_total',0);
				}
				
				$payment_amount = floatval($payment_amount);
				
				$is_valid_payment = false;
				if($enable_payment && $payment_amount>0){
					$is_valid_payment = true;
				}
				
				if(!$is_valid_payment){return false;}
				
				$manual = $this->get_array_isset($payment_data,'manual',false);
				$payment_id = (int) $this->get_array_isset($payment_data,'payment_id',0);
				$wc_inv_id = (int) $this->get_array_isset($payment_data,'order_id',0);
				$wc_cus_id = (int) $this->get_array_isset($payment_data,'customer_user',0);
				
				if(!$payment_id){
					$this->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$payment_id,'details'=>'Woocommerce payment id not found','status'=>0));
					return false;
				}
				
				/*
				$wc_inv_no = '';
				if($this->is_plugin_active('woocommerce-sequential-order-numbers-pro','woocommerce-sequential-order-numbers') && $this->option_checked('mw_wc_qbo_desk_compt_p_wsnop')){
					if($this->is_plugin_active('woocommerce-sequential-order-numbers')){
						$wc_inv_no = get_post_meta( $wc_inv_id, '_order_number', true );
					}else{
						$wc_inv_no = get_post_meta( $wc_inv_id, '_order_number_formatted', true );
					}
					
				}
				*/
				
				$qbd_invoice_id = $this->check_quickbooks_invoice($wc_inv_id);
				
				if(!$qbd_invoice_id){
					$this->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$payment_id,'details'=>'QuickBooks '.strtolower($qbd_sa).' not found','status'=>0));
					return false;
				}
				
				$qbo_cus_id = '';
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-fitbodywrap-custom-compt') && $this->check_sh_fitbodywrap_cuscompt_hash()){
					if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
						$c_account_number = (int) get_post_meta($order_id,'account_number',true);
						if($c_account_number > 0){
							$qbo_cus_id = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid','acc_num',$c_account_number);
						}
					}
				}
				
				/**/
				if($this->is_plugin_active('woocommerce-aelia-currencyswitcher') && $this->option_checked('mw_wc_qbo_desk_wacs_satoc_cb')){				
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
				if($this->is_plugin_active('myworks-quickbooks-desktop-custom-customer-compt-gunnar') && $this->option_checked('mw_wc_qbo_desk_compt_cccgunnar_ocs_qb_cus_map_ed')){
					$cccgunnar_qb_cus_map = get_option('mw_wc_qbo_desk_cccgunnar_qb_cus_map');
					if(is_array($cccgunnar_qb_cus_map) && count($cccgunnar_qb_cus_map)){
						$occ_mp_key = '';
						if($order->post_status == 'rx-processing'){
							$occ_mp_key = 'rx_order_status';
						}else{
							$ord_country = get_post_meta($wc_inv_id,'_shipping_country',true);
							if(empty($ord_country)){
								$ord_country = get_post_meta($wc_inv_id,'_billing_country',true);
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
				
				/**/
				if($this->is_plugin_active('myworks-quickbooks-desktop-sync-compatibility') && $this->is_plugin_active('myworks-quickbooks-desktop-shipping-us-state-quickbooks-customer-map-compt') && $this->option_checked('mw_wc_qbo_desk_compt_sus_qb_cus_map_ed')){					
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
						$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_sus_fb_qb_cus_foc');
					}
				}
				
				if(empty($qbo_cus_id)){
					if(!$this->option_checked('mw_wc_qbo_desk_all_order_to_customer')){
						if($wc_cus_id>0){
							//$qbo_cus_id = $this->get_wc_data_pair_val('Customer',$wc_cus_id);
							if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
								$customer_data = $this->get_wc_customer_info_from_order($order_id);
							}else{
								$customer_data = $this->get_wc_customer_info($wc_cus_id);
							}
							
							$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
						}else{
							$customer_data = $this->get_wc_customer_info_from_order($order_id);
							$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
						}
					}else{
						/*
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							$io_cs = false;
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$sc_roles_as_cus = $this->get_option('mw_wc_qbo_desk_wc_cust_role_sync_as_cus');
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
								if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
								}else{
									$customer_data = $this->get_wc_customer_info($wc_cus_id);
								}							
								$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
							}else{
								$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
							}
							
						}else{
							$qbo_cus_id = $this->get_option('mw_wc_qbo_desk_customer_to_sync_all_orders');
						}
						*/
						
						/**/
						$wc_user_role = '';
						if($wc_cus_id>0){
							$user_info = get_userdata($wc_cus_id);
							if(isset($user_info->roles) && is_array($user_info->roles)){
								$wc_user_role = $user_info->roles[0];
							}
						}else{
							$wc_user_role = 'wc_guest_user';
						}
						
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
									if($this->option_checked('mw_wc_qbo_desk_customer_qbo_check_billing_company')){
										$customer_data = $this->get_wc_customer_info_from_order($order_id);
									}else{
										$customer_data = $this->get_wc_customer_info($wc_cus_id);
									}							
									$qbo_cus_id = $this->if_qbo_customer_exists($customer_data);
								}else{
									$customer_data = $this->get_wc_customer_info_from_order($order_id);
									$qbo_cus_id = $this->if_qbo_guest_exists($customer_data);
								}
							}else{
								$qbo_cus_id = $mw_wc_qbo_desk_aotc_rcm_data[$wc_user_role];
							}
						}
						//
					}
				}				
				
				if(empty($qbo_cus_id)){
					$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Payment Error #'.$payment_id,'details'=>'QuickBooks Customer Not Found','status'=>0));
					return false;
				}

				/**/
				$qost_arr = array(
					'Invoice' => 'Invoice',
					'SalesReceipt' => 'SalesReceipt',
					'SalesOrder' => 'SalesOrder',
					'Estimate' => 'Estimate'										
				);
				
				if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Role'){
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

					if(!empty($wc_user_role)){
						$mw_wc_qbo_desk_oqsa_pr_data = get_option('mw_wc_qbo_desk_oqsa_pr_data');
						if(is_array($mw_wc_qbo_desk_oqsa_pr_data) && !empty($mw_wc_qbo_desk_oqsa_pr_data)){
							if(isset($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]) && !empty($mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role])){
								if(isset($qost_arr[$mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role]])){
									$qbd_sa = $mw_wc_qbo_desk_oqsa_pr_data[$wc_user_role];
								}								
							}
						}
					}
				}
				
				if($this->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
					$order_sync_as = $this->get_array_isset($pm_map_data,'order_sync_as','',true);
					if(!empty($order_sync_as) && isset($qost_arr[$order_sync_as])){
						$qbd_sa = $order_sync_as;
					}
				}
				
				$ReceivePayment = new QuickBooks_QBXML_Object_ReceivePayment();
				$ReceivePayment->setCustomerListID($qbo_cus_id);
				
				$_paid_date = $this->get_array_isset($payment_data,'paid_date','',true);
				$_paid_date = $this->format_date($_paid_date);
				
				/**/
				$qb_ip_ar_acc_id = $this->get_array_isset($pm_map_data,'qb_ip_ar_acc_id','');
				if(!empty($qb_ip_ar_acc_id)){
					$ReceivePayment->set('ARAccountRef ListID',$qb_ip_ar_acc_id);
				}
				
				$ReceivePayment->setTxnDate($_paid_date);
				$_transaction_id = $this->get_array_isset($payment_data,'transaction_id','',true);
				
				$qb_p_method_id = $this->get_array_isset($pm_map_data,'qb_p_method_id','');
				$qbo_account_id = $this->get_array_isset($pm_map_data,'qbo_account_id','');
				
				if($_transaction_id!=''){
					$ReceivePayment->setRefNumber($_transaction_id);
				}else{
					$ReceivePayment->setRefNumber($payment_id);
				}
				
				//echo $qbd_sa;
				if($qbd_sa == 'Invoice'){
					$AppliedToTxn = new QuickBooks_QBXML_Object_ReceivePayment_AppliedToTxn();
					$AppliedToTxn->setTxnID($qbd_invoice_id);
					$AppliedToTxn->setPaymentAmount($payment_amount);
					
					$ReceivePayment->addAppliedToTxn($AppliedToTxn);
				}
				
				if($qbd_sa == 'SalesOrder'){
					$ReceivePayment->set('IsAutoApply',"");
				}
				
				$ReceivePayment->setTotalAmount($payment_amount);
				//setARAccountListID
				$ReceivePayment->setPaymentMethodListID($qb_p_method_id);
				$ReceivePayment->setDepositToAccountListID($qbo_account_id);
				//setMemos
				
				/**/
				if($this->check_sh_paamc_hash()){
					$order = get_post($order_id);
					$invoice_data = $this->get_wc_order_details_from_order($order_id,$order);
					
					$qbo_inv_items = (isset($invoice_data['qbo_inv_items']))?$invoice_data['qbo_inv_items']:array();
					
					$ARAccountRef = '';
					if(is_array($qbo_inv_items) && count($qbo_inv_items)){
						foreach($qbo_inv_items as $qbo_item){
							if(isset($qbo_item['QbArAccId']) && !empty($qbo_item['QbArAccId'])){
								$ARAccountRef = $qbo_item['QbArAccId'];
							}
						}
					}
					
					if(!empty($ARAccountRef)){
						$ReceivePayment->setARAccountListID($ARAccountRef);
					}
				}
				
				$qbxml = $ReceivePayment->asQBXML('ReceivePaymentAdd',null,$this->get_qbxml_locale());
				$qbxml = $this->get_qbxml_prefix().$qbxml. $this->get_qbxml_suffix();
				$qbxml = $this->qbxml_search_replace($qbxml);
				
				if($qbd_sa != 'Invoice'){
					//$qbxml = $this->format_xml($qbxml);
				}
				
				return $qbxml;
				
			}
		}
	}
	
	/*Sync Functions*/
	
	public function AddInventoryItemRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
		$qbxml = $this->GetProductQbxml($ID,'Inventory',$is_variation);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			if($is_variation){
				$dlog_txt = PHP_EOL .'Product Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}else{
				$dlog_txt = PHP_EOL .'Variation Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		
		return $qbxml;
	}
	
	public function AddNonInventoryRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
		$qbxml = $this->GetProductQbxml($ID,'NonInventory',$is_variation);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			if($is_variation){
				$dlog_txt = PHP_EOL .'Product Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}else{
				$dlog_txt = PHP_EOL .'Variation Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}			
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddServiceItemRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
		$qbxml = $this->GetProductQbxml($ID,'Service',$is_variation);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			if($is_variation){
				$dlog_txt = PHP_EOL .'Product Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}else{
				$dlog_txt = PHP_EOL .'Variation Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddInventoryItemResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$this->AddProductResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
		return true;
	}
	
	public function AddNonInventoryResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$this->AddProductResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
		return true;
	}
	
	public function AddServiceItemResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$this->AddProductResponse($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
		return true;
	}
	
	public function AddProductResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));
		$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
		
		if(is_array($idents) && isset($idents['ListID']) && $idents['ListID']!=''){
			if($is_variation){
				$this->save_data_pair('Variation',$ID,$idents['ListID']);
			}else{
				$this->save_data_pair('Product',$ID,$idents['ListID']);
			}
			$lg_type = ($is_variation)?'Variation':'Product';
			
			$this->save_item_map($ID,$idents['ListID'],$is_variation);
			$this->save_qbo_item_local($idents['ListID'],$ID,$is_variation);
			
			$details = "{$lg_type} #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks List ID #".$idents['ListID'];
			$this->save_log(array('log_type'=>$lg_type,'log_title'=>'Export '.$lg_type.' #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>$lg_type,'log_title'=>'Export '.$lg_type.' Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	public function InventoryAdjustmentAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
		$qbxml = $this->GetInventoryAdjustmentAddQbxml($ID,$is_variation);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			if($is_variation){
				$dlog_txt = PHP_EOL .'Variation Inventiory Update Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}else{
				$dlog_txt = PHP_EOL .'Inventiory Update Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			}
			
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function InventoryAdjustmentAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}		
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));
		
		if(is_array($idents) && isset($idents['ListID']) && $idents['ListID']!=''){
			$is_variation = $this->get_array_isset($extra,'is_variation',0,true);
			$ext_lt = ($is_variation)?'Variation ':'';
			
			$details = "{$ext_lt}Inventory #{$ID} updated into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks List ID #".$idents['ListID'];
			$this->save_log(array('log_type'=>'Inventory','log_title'=>'Update '.$ext_lt.'Inventory #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Inventory','log_title'=>'Update '.$ext_lt.'Inventory Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	/**/
	public function UpdateCustomerRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$EditSequence = '';
		$ListID = '';		
		if(is_array($extra) && !empty($extra) && isset($extra['EditSequence'])  && isset($extra['ListID'])){
			$EditSequence = $extra['EditSequence'];
			$ListID = $extra['ListID'];
		}
		
		if(empty($EditSequence) || empty($ListID)){
			return false;
		}
		
		$qbxml = $this->GetCustomerQbxml($ID,false,$EditSequence,$ListID);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Customer Update Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddCustomerRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetCustomerQbxml($ID);
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Customer Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function UpdateCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		if(is_array($idents) && isset($idents['ListID']) && $idents['ListID']!=''){
			//$this->save_data_pair('Customer',$ID,$idents['ListID']);
			//$this->save_customer_map($ID,$idents['ListID']);
			$this->save_qbo_customer_local($idents['ListID'],$ID);
			$details = "Customer #{$ID} updated into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks List ID #".$idents['ListID'];
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Update Customer #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Update Customer Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
		
	}
	
	public function AddCustomerResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['ListID']) && $idents['ListID']!=''){
			$this->save_data_pair('Customer',$ID,$idents['ListID']);
			$this->save_customer_map($ID,$idents['ListID']);
			$this->save_qbo_customer_local($idents['ListID'],$ID);
			$details = "Customer #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks List ID #".$idents['ListID'];
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Customer #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Customer Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}

	public function AddGuestRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetCustomerQbxml($ID,true);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Customer/Guest Add Request Order #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}

	public function AddGuestResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['ListID']) && $idents['ListID']!=''){
			$customer_data = $this->get_wc_customer_info_from_order($ID);
			$this->save_qbo_customer_local($idents['ListID'],null,$customer_data);

			$details = "Customer added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks List ID #".$idents['ListID'].PHP_EOL;
			$details.="Order ID #{$ID}";
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Guest/Customer','details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err".PHP_EOL;
			$details.="Order ID #{$ID}";
			$this->save_log(array('log_type'=>'Customer','log_title'=>'Export Guest/Customer Error','details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	/*Payment Sync*/
	public function AddPaymentRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetPaymentQbxml($ID);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Payment Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddPaymentResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$pr_dp_ed = '';
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
				$pr_dp_ed = 'SalesOrder';
			}
			$this->save_data_pair('Payment',$ID,$idents['TxnID'],$pr_dp_ed);
			$details = "Payment #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Payment','log_title'=>'Export Payment Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	/*Refund Sync*/
	public function AddRefundRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$order_id = 0;
		if(is_array($extra) && isset($extra['order_id'])){
			$order_id = $extra['order_id'];
		}
		
		//$rfnd_entity = 'ARRefundCreditCard';
		//$qbxml = $this->GetRefundQbxml_ArRCc($ID,$order_id);
		
		$rfnd_entity = 'Check';
		$qbxml = $this->GetRefundQbxml_Check($ID,$order_id);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Refund Add Request ('.$rfnd_entity.') #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddRefundResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$pr_dp_ed = '';
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_order')){
				//$pr_dp_ed = 'SalesOrder';
			}
			$this->save_data_pair('Refund',$ID,$idents['TxnID'],$pr_dp_ed);
			$details = "Refund #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Refund','log_title'=>'Export Refund Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	/*Invoice Sync*/
	public function AddInvoiceRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetInvoiceQbxml($ID,$extra);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Invoice Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	/**/
	public function OrderDataExtAddRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		if(is_array($extra) && !empty($extra) && isset($extra['Qos_Type']) && !empty($extra['Qos_Type'])){
			if(isset($extra['TxnID']) && !empty($extra['TxnID'])){
				$qbxml = $this->GetOrderDataExtAddQbxml($ID,$extra);
				if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
					$dlog_txt = PHP_EOL .'Order Data Ext Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
					$dlog_txt.=$qbxml;
					$this->add_test_log($dlog_txt);
				}
				return $qbxml;
			}			
		}
	}
	
	//
	public function UpdateSalesReceiptRequest_GPI($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$qbxml = $this->GetSalesReceiptQbxml_GPI($ID);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'SalesReceipt Update Request (Group Line Item) #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddSalesReceiptRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetSalesReceiptQbxml($ID,$extra);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'SalesReceipt Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}

	public function AddSalesOrderRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetSalesOrderQbxml($ID,$extra);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'SalesOrder Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	//
	public function AddEstimateRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetEstimateQbxml($ID,$extra);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Estimate Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	public function AddPurchaseOrderRequest($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		$qbxml = $this->GetPurchaseOrderQbxml($ID);
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'PurchaseOrder Add Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$qbxml;
			$this->add_test_log($dlog_txt);
		}
		return $qbxml;
	}
	
	/**/
	private function Wc_Order_Status_Update_After_Sync($order_id,$status=''){
		$order_id = (int) $order_id;
		$oaqs_status = $this->get_option('mw_wc_qbo_desk_order_status_after_qbd_sync');
		if($order_id >0 && !empty($oaqs_status)){
			$_order = new WC_Order( $order_id );
			if (!empty($_order)) {
				$_order->update_status($oaqs_status);
			}			
		}
	}
	
	public function AddInvoiceResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$this->save_data_pair('Order',$ID,$idents['TxnID']);
			$details = "Order #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order #'.$ID,'details'=>$details,'status'=>1),true);
			
			/*Wc Order Status Update*/
			$this->Wc_Order_Status_Update_After_Sync($ID);
			
			/**/
			if($this->check_cf_map_data_ext_field_value_exists($ID,'Invoice')){
				$idents['Qos_Type'] = 'Invoice';
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('InvoiceDataExt', $ID,1,$idents,$this->get_qbun(),$xml);
			}
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	/**/
	public function OrderDataExtAddResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));
	}
	
	//
	public function UpdateSalesReceiptResponse_GPI($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$this->add_test_log($xml);
		$this->add_test_log(print_r($idents,true));
	}
	
	public function AddSalesReceiptResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$this->save_data_pair('Order',$ID,$idents['TxnID']);
			$details = "Order #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order #'.$ID,'details'=>$details,'status'=>1),true);
			
			/*Wc Order Status Update*/
			$this->Wc_Order_Status_Update_After_Sync($ID);
			
			//
			if($this->option_checked('mw_wc_qbo_desk_order_as_sales_receipt')){
				if($this->is_wc_ord_has_qbd_group_item($ID)){
					$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
					$Queue->enqueue('SalesReceiptMod_GPI', $ID,1,$idents,$this->get_qbun(),$xml);
				}
			}

			/**/
			if($this->check_cf_map_data_ext_field_value_exists($ID,'SalesReceipt')){
				$idents['Qos_Type'] = 'SalesReceipt';
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('SalesReceiptDataExt', $ID,1,$idents,$this->get_qbun(),$xml);
			}
			
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	public function AddSalesOrderResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$this->save_data_pair('Order',$ID,$idents['TxnID']);
			$details = "Order #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order #'.$ID,'details'=>$details,'status'=>1),true);
			
			/*Wc Order Status Update*/
			$this->Wc_Order_Status_Update_After_Sync($ID);
			
			/**/
			if($this->check_cf_map_data_ext_field_value_exists($ID,'SalesOrder')){
				$idents['Qos_Type'] = 'SalesOrder';
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('SalesOrderDataExt', $ID,1,$idents,$this->get_qbun(),$xml);
			}
			
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	//
	public function AddEstimateResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$this->save_data_pair('Order',$ID,$idents['TxnID']);
			$details = "Order #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order #'.$ID,'details'=>$details,'status'=>1),true);
			
			/*Wc Order Status Update*/
			$this->Wc_Order_Status_Update_After_Sync($ID);
			
			/**/
			if($this->check_cf_map_data_ext_field_value_exists($ID,'Estimate')){
				$idents['Qos_Type'] = 'Estimate';
				$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
				$Queue->enqueue('EstimateDataExt', $ID,1,$idents,$this->get_qbun(),$xml);
			}
			
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'Order','log_title'=>'Export Order Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}
	
	public function AddPurchaseOrderResponse($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));

		if(is_array($idents) && isset($idents['TxnID']) && $idents['TxnID']!=''){
			$this->save_data_pair('PurchaseOrder',$ID,$idents['TxnID']);
			$details = "PurchaseOrder #{$ID} added into quickbooks successfully".PHP_EOL;
			$details.="QuickBooks TxnID #".$idents['TxnID'];
			$this->save_log(array('log_type'=>'PurchaseOrder','log_title'=>'Export PurchaseOrder #'.$ID,'details'=>$details,'status'=>1),true);
		}else{
			$details = "Error:$err";
			$this->save_log(array('log_type'=>'PurchaseOrder','log_title'=>'Export PurchaseOrder Error #'.$ID,'details'=>$details,'status'=>0),true);
		}
		return true;
	}

	/*Errors And Hooks Function Start*/
	public function Qwc_Catch_All_Errors($requestID, $user, $action, $ID, $extra, &$err, $xml, $errnum, $errmsg){
		//$this->add_test_log($errmsg);
		$log_type = '';$log_status = 0;$log_title = '';$details = '';
		$is_log_error = true;
		
		if(empty($action) || $action == 'DEBUG_QUEUE' || $action == 'CustomerImport_ByName' || $action == 'GuestImport_ByName'){
			$is_log_error = false;
		}
		
		if(!$is_log_error){
			return true;
		}
		
		switch ($action) {
			case "InvoiceAdd":
				$log_type = 'Order';
				$log_title = 'Export Order Error #'.$ID;
				break;
			case "SalesReceiptAdd":
				$log_type = 'Order';
				$log_title = 'Export Order Error #'.$ID;
				break;
				
			case "SalesOrderAdd":
				$log_type = 'Order';
				$log_title = 'Export Order Error #'.$ID;
				break;
				
			case "EstimateAdd":
				$log_type = 'Order';
				$log_title = 'Export Order Error #'.$ID;
				break;				
				
			case "CustomerAdd":
				$log_type = 'Customer';
				$log_title = 'Export Customer Error #'.$ID;
				break;
			case "CustomerMod":
				$log_type = 'Customer';
				$log_title = 'Update Customer Error #'.$ID;
				break;
				
			case "ItemInventoryAdd":
				$log_type = 'Product';
				$log_title = 'Export Product Error #'.$ID;
				break;
				
			case "ItemNonInventoryAdd":
				$log_type = 'Product';
				$log_title = 'Export Product Error #'.$ID;
				break;
			
			case "ItemServiceAdd":
				$log_type = 'Product';
				$log_title = 'Export Product Error #'.$ID;
				break;
				
			case "ReceivePaymentAdd":
				$log_type = 'Payment';
				$log_title = 'Export Payment Error #'.$ID;
				break;
				
			case "ItemInventoryMod":
				$log_type = 'Inventory';
				$log_title = 'Update Inventory Error #'.$ID;
				break;
			
			/*Import Errors*/
			
			case "CustomerImport":
				$log_type = 'Customer';
				$log_title = 'Recognized Customer Error';
				break;
			case "ItemImport":
				$log_type = 'Product';
				$log_title = 'Recognized Product Error';
				break;
			case "AccountImport":
				$log_type = 'Account';
				$log_title = 'Recognized Account Error';
				break;
			case "TermsImport":
				$log_type = 'Terms';
				$log_title = 'Recognized Terms Error';
				break;
			case "SalesTaxCodeImport":
				$log_type = 'SalesTaxCode';
				$log_title = 'Recognized SalesTaxCode Error';
				break;
			case "ClassImport":
				$log_type = 'Class';
				$log_title = 'Recognized Class Error';
				break;
			case "PaymentMethodImport":
				$log_type = 'PaymentMethod';
				$log_title = 'Recognized PaymentMethod Error';
				break;			
			case "ShipMethodImport":
				$log_type = 'ShipMethod';
				$log_title = 'Recognized ShipMethod Error';
				break;
			case "AUTO_WC_UPDATE_INVENTORY":
				$log_type = 'InventoryPull';
				$log_title = 'Auto Import Inventory Error';
				break;
			case "ALL_WC_UPDATE_INVENTORY":
				$log_type = 'InventoryPull';
				$log_title = 'Auto Import Inventory Error';
				break;
			case "ALL_WC_UPDATE_INVENTORY_A":
				$log_type = 'InventoryPull';
				$log_title = 'Auto Import InventoryAssembly Error';
				break;				
			default:
				$log_type = 'Other';
				$log_title = $action.' Error';
		}

		$details.= "Error Number:{$errnum}".PHP_EOL;
		$details.= "Error:{$errmsg}";
		
		//
		if($log_type=='InventoryPull' && $errnum==1){
			if($this->start_with($errmsg,'A query request did not find')){
				$log_status = 2;
				if($action=='ALL_WC_UPDATE_INVENTORY_A'){
					$log_title = 'All Inventory Assembly products are up to date';
				}else{
					$log_title = 'All Inventory products are up to date';
				}
				
				$details = 'All Inventory levels are up to date';
			}
		}
		
		if($log_title!=''){
			$this->save_log(array('log_type'=>$log_type,'log_title'=>$log_title,'details'=>$details,'status'=>$log_status),true);
		}
		return true;
	}
	
	/*Auto Queue Add*/
	public function Hook_Login_Success($requestID, $user, $hook, &$err, $hook_data, $callback_config){
		if(!$this->is_qwc_connected()){
			return false;
		}

		$this->save_log(array('log_type'=>'Web Connector','log_title'=>'Web Connector Login Success','details'=>'New Web Connector Request Authenticated Successfully','status'=>1));

		//QuickBooks_Utilities::log($this->dsn, 'User logged in: ' . $user . ', params: ' . print_r($hook_data, true));		

		$qbd_import = false;$extra_import = false;

		if($this->option_checked('mw_wc_qbo_desk_cp_refresh_data_enable')){
			$qbd_import = true;
			$extra_import = true;
		}

		if($this->option_checked('mw_wc_qbo_desk_oth_refresh_data_enable')){
			//$extra_import = true;
		}
		
		//$qb_queue_instance_allow_always = true;
		if($qbd_import || $extra_import || $this->option_checked('mw_wc_qbo_desk_rt_pull_enable') || $this->debug_queue || $this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			//$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
			$date = date('Y-m-d H:i:s');
		}
		
		if($this->debug_queue){
			if(!$this->if_queue_exists('DEBUG_QUEUE')){
				$Queue->enqueue('DEBUG_QUEUE', null, 0,null,$this->get_qbun());
			}			
		}
		
		$import_queue_added = false;
		
		if($qbd_import){
			$import_queue_added = true;
			
			if($this->check_refresh_data_enabled_by_item('customer') && !$this->if_queue_exists(QUICKBOOKS_IMPORT_CUSTOMER)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_CUSTOMER, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, null, QB_PRIORITY_REFRESH_DATA_IMPORT,null,$this->get_qbun());
			}

			if($this->check_refresh_data_enabled_by_item('product') && !$this->if_queue_exists(QUICKBOOKS_IMPORT_ITEM)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_ITEM)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_ITEM, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_ITEM, null, QB_PRIORITY_REFRESH_DATA_IMPORT,null,$this->get_qbun());
			}

		}
		
		if($extra_import){
			$import_queue_added = true;
			
			$priority = QB_PRIORITY_REFRESH_DATA_IMPORT;

			/*PaymentMethod Import Queue*/

			if($this->check_refresh_data_enabled_by_item('payment_method',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_PAYMENTMETHOD)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_PAYMENTMETHOD)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_PAYMENTMETHOD, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_PAYMENTMETHOD, null, $priority,null,$this->get_qbun());
			}			
			
			/*Account Import Queue*/
			if($this->check_refresh_data_enabled_by_item('account',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_ACCOUNT)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_ACCOUNT)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_ACCOUNT, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_ACCOUNT, null, $priority,null,$this->get_qbun());
			}

			/*Class Import Queue*/
			if($this->check_refresh_data_enabled_by_item('class',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_CLASS)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_CLASS)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_CLASS, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_CLASS, null, $priority,null,$this->get_qbun());
			}

			/*SalesTaxCode Import Queue*/
			if($this->check_refresh_data_enabled_by_item('sales_tax_code',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_SALESTAXCODE)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_SALESTAXCODE)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_SALESTAXCODE, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_SALESTAXCODE, null, $priority,null,$this->get_qbun());
			}
			
			/*Term Import Queue*/
			if($this->check_refresh_data_enabled_by_item('term',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_TERMS)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_TERMS)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_TERMS, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_TERMS, null, $priority,null,$this->get_qbun());
			}

			/*Preferences Import Queue*/
			if($this->check_refresh_data_enabled_by_item('preferences',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_PREFERENCES)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_PREFERENCES)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_PREFERENCES, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_PREFERENCES, null, $priority,null,$this->get_qbun());
				
				if(!$this->if_queue_exists(QUICKBOOKS_IMPORT_COMPANY)){
					$Queue->enqueue(QUICKBOOKS_IMPORT_COMPANY, null, $priority,null,$this->get_qbun());
				}				
			}
			
			/*InventorySite Import Queue*/
			if($this->check_refresh_data_enabled_by_item('InventorySite',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_INVENTORYSITE)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_INVENTORYSITE)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_INVENTORYSITE, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_INVENTORYSITE, null, $priority,null,$this->get_qbun());
			}
			
			/*OtherName Import Queue*/
			if($this->check_refresh_data_enabled_by_item('OtherName',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_OTHERNAME)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_OTHERNAME)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_OTHERNAME, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_OTHERNAME, null, $priority,null,$this->get_qbun());
			}
			
			/*SalesRep Import Queue*/
			if($this->check_refresh_data_enabled_by_item('SalesRep',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_SALESREP)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_SALESREP)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_SALESREP, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_SALESREP, null, $priority,null,$this->get_qbun());
			}
			
			/*CustomerType Import Queue*/
			if($this->check_refresh_data_enabled_by_item('CustomerType',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_CUSTOMERTYPE)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_CUSTOMERTYPE)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_CUSTOMERTYPE, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMERTYPE, null, $priority,null,$this->get_qbun());
			}
			
			/*ShipMethod Import Queue*/
			if($this->check_refresh_data_enabled_by_item('ShipMethod',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_SHIPMETHOD)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_SHIPMETHOD)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_SHIPMETHOD, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_SHIPMETHOD, null, $priority,null,$this->get_qbun());
			}
			
			/*Template Import Queue*/
			if($this->check_refresh_data_enabled_by_item('Template',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_TEMPLATE)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_TEMPLATE)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_TEMPLATE, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_TEMPLATE, null, $priority,null,$this->get_qbun());
			}
			
			/*Vendor Import Queue*/
			if($this->check_refresh_data_enabled_by_item('Vendor',true) && !$this->if_queue_exists(QUICKBOOKS_IMPORT_VENDOR)){
				if (!$this->_quickbooks_get_last_run($user, QUICKBOOKS_IMPORT_VENDOR)){
					$this->_quickbooks_set_last_run($user, QUICKBOOKS_IMPORT_VENDOR, $date);
				}
				$Queue->enqueue(QUICKBOOKS_IMPORT_VENDOR, null, $priority,null,$this->get_qbun());
			}			
			
		}
		
		//Disable Refresh Data
		if($import_queue_added){
			/*
			$this->set_session_val('mw_wc_qbo_desk_cp_refresh_data_enable',true);
			$this->set_session_val('mw_wc_qbo_desk_oth_refresh_data_enable',true);
			
			update_option('mw_wc_qbo_desk_cp_refresh_data_enable','');
			update_option('mw_wc_qbo_desk_oth_refresh_data_enable','');
			*/
		}
		
		/*Auto Pull Queue*/		
		if($this->option_checked('mw_wc_qbo_desk_rt_pull_enable')){
			$rt_pull_enb_itm_chk = false;
			$rt_force_all_pl_enb = true;
			if($this->check_if_real_time_pull_enable_for_item('inventory') || $this->check_if_real_time_pull_enable_for_item('pricing')){
				$rt_pull_enb_itm_chk = true;
			}
			
			if($rt_pull_enb_itm_chk){
				if($rt_force_all_pl_enb || $this->option_checked('mw_wc_qbo_desk_rt_all_invnt_pull')){					
					$mw_wc_qbo_desk_aipit_ldt = $this->get_option('mw_wc_qbo_desk_aipit_ldt');
					$st_kwd = '';					
					$aip_ti = $this->get_option('mw_wc_qbo_desk_all_invnt_pull_interver_time');
					$is_aip_aq = false;					
					if($aip_ti == 'i_e_r'){
						$is_aip_aq = true;
					}else{
						if($aip_ti == 'e_30_m'){
							$st_kwd = '30 minutes';
						}
						
						if($aip_ti == 'e_1_h'){
							$st_kwd = '1 hour';
						}
						
						if($aip_ti == 'e_2_h'){
							$st_kwd = '2 hours';
						}
					}
					
					if(!$is_aip_aq){
						if($st_kwd!=''){
							$prev_time = strtotime("-{$st_kwd}");
							if(empty($mw_wc_qbo_desk_aipit_ldt)){
								$mw_wc_qbo_desk_aipit_ldt = date('Y-m-d H:i:s',$prev_time);
							}
							
							if(strtotime($mw_wc_qbo_desk_aipit_ldt) <= $prev_time) {
								$is_aip_aq = true;
								$s_dt = date('Y-m-d H:i:s');
								update_option('mw_wc_qbo_desk_aipit_ldt',$s_dt);
							}
						}						
					}
					
					if($is_aip_aq){
						if($this->check_if_real_time_pull_enable_for_item('inventory')){
							//
							$wmior_active =$this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
							$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
							
							if(($wmior_active && $this->option_checked('mw_wc_qbo_desk_w_miors_ed') && is_array($mw_wc_qbo_desk_compt_wmior_lis_mv)) || (!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_sites_for_invnt_pull')!='')){
								if(!$this->if_queue_exists('ALL_PULL_INVT_ISQ')){
									if (!$this->_quickbooks_get_last_run($user, 'ALL_PULL_INVT_ISQ')){
										$this->_quickbooks_set_last_run($user, 'ALL_PULL_INVT_ISQ', $date);
									}
									$Queue->enqueue('ALL_PULL_INVT_ISQ', null, 0,null,$this->get_qbun());
									$this->unset_session('invnt_st_qty_arr');
									$this->unset_session('item_name_arr');
									$this->unset_session('item_asmb_arr');
									
									$this->unset_session('nt_invnt_st_qty_arr');
								}								
							}else{
								if(!$this->if_queue_exists('ALL_WC_UPDATE_INVENTORY')){
									if (!$this->_quickbooks_get_last_run($user, 'ALL_WC_UPDATE_INVENTORY')){
										$this->_quickbooks_set_last_run($user, 'ALL_WC_UPDATE_INVENTORY', $date);
									}
									$Queue->enqueue('ALL_WC_UPDATE_INVENTORY', null, 0,null,$this->get_qbun());
								}
								
								if(!$this->if_queue_exists('ALL_WC_UPDATE_INVENTORY_A')){
									if (!$this->_quickbooks_get_last_run($user, 'ALL_WC_UPDATE_INVENTORY_A')){
										$this->_quickbooks_set_last_run($user, 'ALL_WC_UPDATE_INVENTORY_A', $date);
									}
									$Queue->enqueue('ALL_WC_UPDATE_INVENTORY_A', null, 0,null,$this->get_qbun());
								}
							}
						}
						
						/**/
						if($this->check_if_real_time_pull_enable_for_item('pricing') && !$this->if_queue_exists('ALL_WC_UPDATE_PRICING')){
							if (!$this->_quickbooks_get_last_run($user, 'ALL_WC_UPDATE_PRICING')){
								$this->_quickbooks_set_last_run($user, 'ALL_WC_UPDATE_PRICING', $date);
							}
							$Queue->enqueue('ALL_WC_UPDATE_PRICING', null, 0,null,$this->get_qbun());
						}
												
					}
				}else{
					if($this->check_if_real_time_pull_enable_for_item('inventory') && !$this->if_queue_exists('AUTO_WC_UPDATE_INVENTORY')){
						/*
						if (!$this->_quickbooks_get_last_run($user, 'AUTO_WC_UPDATE_INVENTORY')){
							$this->_quickbooks_set_last_run($user, 'AUTO_WC_UPDATE_INVENTORY', $date);
						}
						$Queue->enqueue('AUTO_WC_UPDATE_INVENTORY', null, 0,null,$this->get_qbun());
						*/
					}					
				}
			}
		}
		
		/**/
		if($this->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
			if(!empty($this->get_option('mw_wc_qbo_desk_compt_np_fitbodywrap_in_imp_ti'))){
				$mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt = $this->get_option('mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt');
				$st_kwd = '';					
				$aip_ti = $this->get_option('mw_wc_qbo_desk_compt_np_fitbodywrap_in_imp_ti');
				$is_aip_aq = false;					
				if($aip_ti == 'i_e_r'){
					$is_aip_aq = true;
				}else{
					if($aip_ti == 'e_30_m'){
						$st_kwd = '30 minutes';
					}
					
					if($aip_ti == 'e_1_h'){
						$st_kwd = '1 hour';
					}
					
					if($aip_ti == 'e_2_h'){
						$st_kwd = '2 hours';
					}
				}
				
				if(!$is_aip_aq){
					if($st_kwd!=''){
						$prev_time = strtotime("-{$st_kwd}");
						if(empty($mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt)){
							$mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt = date('Y-m-d H:i:s',$prev_time);
						}
						
						if(strtotime($mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt) <= $prev_time) {
							$is_aip_aq = true;
							$s_dt = date('Y-m-d H:i:s');
							update_option('mw_wc_qbo_desk_compt_np_fitbodywrap_aipit_ldt',$s_dt);
						}
					}						
				}
				
				if($is_aip_aq){
					if(!$this->if_queue_exists('InvoiceQuery_FBW')){
						if (!$this->_quickbooks_get_last_run($user, 'InvoiceQuery_FBW')){
							$this->_quickbooks_set_last_run($user, 'InvoiceQuery_FBW', $date);
						}
						$Queue->enqueue('InvoiceQuery_FBW', null, 0,null,$this->get_qbun());
					}
				}
			}
		}
		
	}
	/*Errors And Hooks Function End*/
	
	/*Pull Functions Start*/
	
	public function Pull_Update_Wc_Product_Price_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<ItemQueryRq  requestID="' . $requestID . '">			
						<ListID >'.$ID.'</ListID>
						<OwnerID>0</OwnerID>
					</ItemQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Product Price Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	public function All_Pull_Update_Wc_Inventory_A_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		$it_q_req = 'ItemInventoryAssemblyQueryRq';
		$dl_it = 'InventoryAssembly';
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.' '.$attr_iterator.' '.$attr_iteratorID.'  requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<OwnerID>0</OwnerID>
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	//Item Site Query	
	public function All_Pull_ItemSitesQuery_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		$adv_inv_site = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_sites_for_invnt_pull');
		
		$inv_site_filter = '';		
		if($this->is_invnt_imp_q_mapped_list_id_filter()){
			$miaa_qbd_ids = $this->get_mapped_qbd_invnt_and_assmbly_items();
			if(is_array($miaa_qbd_ids) && !empty($miaa_qbd_ids)){
				$inv_site_filter .= '<ItemFilter>'.PHP_EOL;
				foreach($miaa_qbd_ids as $mqi){
					$inv_site_filter .= '	<ListID >'.$mqi['quickbook_product_id'].'</ListID>'.PHP_EOL;
				}
				$inv_site_filter .= '</ItemFilter>';
			}			
			
			/*
			if(!$this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_inc_non_inv_st')){
				$inv_site_filter .= '<ItemSiteFilter></ItemSiteFilter>';
			}
			*/
			
			if(!empty($inv_site_filter)){
				$inv_site_filter = '
					<ItemSiteFilter>
					'.$inv_site_filter.'
					</ItemSiteFilter>
				';
			}
		}
		
		$it_q_req = 'ItemSitesQueryRq';
		$dl_it = 'ItemSites';		
		
		//$ais_xq = '';
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.' '.$attr_iterator.' '.$attr_iteratorID.'  requestID="' . $requestID . '">
						'.$inv_site_filter.'
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>						
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	public function All_Pull_Update_Wc_Inventory_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		$it_q_req = 'ItemInventoryQueryRq';
		$dl_it = 'Inventory';
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.' '.$attr_iterator.' '.$attr_iteratorID.'  requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<OwnerID>0</OwnerID>
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	public function All_Pull_Update_Wc_Pricing_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		$it_q_req = 'ItemQueryRq';
		$dl_it = 'Product Price';
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.' '.$attr_iterator.' '.$attr_iteratorID.'  requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<OwnerID>0</OwnerID>
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	/**/
	public function InvoiceQuery_FBW_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		//$timestamp_from = $this->get_auto_inventory_pull_m_dt_from($this->get_qbd_timezone());		
		$rt_ii_it_val = $this->get_option('mw_wc_qbo_desk_compt_np_fitbodywrap_in_imp_ti');
		
		$mw_qbo_dts_qwc_sec_interval = (int) $this->get_option('mw_qbo_dts_qwc_sec_interval'); //In Seconds
		$minute_interval = floor(($mw_qbo_dts_qwc_sec_interval / 60));
		
		$time_zone = $this->get_qbd_timezone();
		$now = new DateTime(null, new DateTimeZone($time_zone));
		$datetime = $now->format('Y-m-d H:i:s');
		
		switch ($rt_ii_it_val) {
			case "i_e_r":
				$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
				break;
			case "e_30_m":
				$datetime = date('Y-m-d H:i:s',strtotime("-30 minutes",strtotime($datetime)));
				break;
			case "e_1_h":
				$datetime = date('Y-m-d H:i:s',strtotime("-60 minutes",strtotime($datetime)));
				break;
			case "e_2_h":
				$datetime = date('Y-m-d H:i:s',strtotime("-120 minutes",strtotime($datetime)));
				break;
			default:
				$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
		}
		
		if(empty($datetime)){
			$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
		}
		
		$timestamp_from = date('Y-m-d', strtotime($datetime)) . 'T' . date('H:i:s', strtotime($datetime));
		
		$it_q_req = 'InvoiceQueryRq';
		$dl_it = 'Invoice #';
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.' '.$attr_iterator.' '.$attr_iteratorID.'  requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<ModifiedDateRangeFilter>
							<FromModifiedDate>' . $timestamp_from . '</FromModifiedDate>
						</ModifiedDateRangeFilter>						
						<IncludeLineItems>1</IncludeLineItems>
						<IncludeLinkedTxns>1</IncludeLinkedTxns>
						<OwnerID>0</OwnerID>
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	public function Pull_Update_Wc_Inventory_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		global $wpdb;
		$it_q_req = 'ItemInventoryQueryRq';
		
		$dl_it = 'Inventory';
		$qp_type = 'Inventory';
		
		if(is_array($extra) && count($extra)){
			if(isset($extra['InventoryAssembly']) && $extra['InventoryAssembly']){
				$qp_type = 'InventoryAssembly';
			}
		}
		
		//$qp_type = $this->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','product_type','qbd_id',$ID);
		if($qp_type=='InventoryAssembly'){
			$it_q_req = 'ItemInventoryAssemblyQueryRq';
			$dl_it = 'InventoryAssembly';
		}
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<'.$it_q_req.'  requestID="' . $requestID . '">			
						<ListID >'.$ID.'</ListID>
						<OwnerID>0</OwnerID>
					</'.$it_q_req.'>
				</QBXMLMsgsRq>
			</QBXML>';		
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .''.$dl_it.' Import Request #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			//$this->add_test_log($dlog_txt);
		}
		
		return $xml;
	}
	
	/*Date Functions*/	
	public function get_qbd_cdt_now($time_zone=''){
		if(empty($time_zone)){
			$time_zone = $this->get_qbd_timezone();
		}
		$now = new DateTime(null, new DateTimeZone($time_zone));
		$datetime = $now->format('Y-m-d H:i:s');
		
		return $datetime;
	}
	
	public function get_auto_inventory_pull_m_dt_from($time_zone=''){
		global $wpdb;
		if(empty($time_zone)){
			$time_zone = $this->get_qbd_timezone();
		}
		
		$mw_qbo_dts_qwc_sec_interval = (int) $this->get_option('mw_qbo_dts_qwc_sec_interval'); //In Seconds
		$minute_interval = floor(($mw_qbo_dts_qwc_sec_interval / 60));
		
		$now = new DateTime(null, new DateTimeZone($time_zone));
		$datetime = $now->format('Y-m-d H:i:s');
		//$timestamp_to = date('Y-m-d', strtotime($datetime)) . 'T' . date('H:i:s', strtotime($datetime));
		
		$rt_ii_it_val = $this->get_option('mw_wc_qbo_desk_rt_inventory_import_interval_time');		
		
		switch ($rt_ii_it_val) {
			case "c_i_t":
				$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
				break;
			case "l_1_h":
				$datetime = date('Y-m-d H:i:s',strtotime("-60 minutes",strtotime($datetime)));
				break;
			case "l_1_d":
				$datetime = date('Y-m-d H:i:s',strtotime("-1 days",strtotime($datetime)));
				break;
			case "l_i_s_t":
				$datetime = $this->get_option('mw_wc_qbo_desk_rt_inventory_p_s_time');
				$datetime = $this->convert_dt_timezone($datetime,$time_zone);
				break;
			case "l_w_c_a_s_t":
				$dq = "SELECT `added_date` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_log  WHERE `log_title` = 'Web Connector Login Success' AND `log_type` = 'Web Connector' ORDER BY `id` DESC LIMIT 0,1 ";
				$datetime = $wpdb->get_var($dq);
				$datetime = $this->convert_dt_timezone($datetime,$time_zone);
				break;
			default:
				$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
		}
		
		if(empty($datetime)){
			$datetime = date('Y-m-d H:i:s',strtotime("-{$minute_interval} minutes",strtotime($datetime)));
		}
		
		$timestamp_from = date('Y-m-d', strtotime($datetime)) . 'T' . date('H:i:s', strtotime($datetime));
		return $timestamp_from;
	}
	
	/*New Inventory Auto Pull*/
	public function Auto_Wc_Inventory_AdjustmentQuery_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}		
		
		$timestamp_from = $this->get_auto_inventory_pull_m_dt_from($this->get_qbd_timezone());
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<InventoryAdjustmentQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						
						<ModifiedDateRangeFilter>
							<FromModifiedDate >'.$timestamp_from.'</FromModifiedDate>										
						</ModifiedDateRangeFilter>
						
						<IncludeLineItems >true</IncludeLineItems>
						<IncludeRetElement >InventoryAdjustmentLineRet</IncludeRetElement>
					</InventoryAdjustmentQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';
		
		/*
		<ModifiedDateRangeFilter>
			<FromModifiedDate >'.$timestamp_from.'</FromModifiedDate>
			<ToModifiedDate >DATETIMETYPE</ToModifiedDate>			
		</ModifiedDateRangeFilter>		
		*/
		
		/*
		<TxnDateRangeFilter>
			<FromTxnDate >'.$timestamp_from.'</FromTxnDate>
			<ToTxnDate >DATETYPE</ToTxnDate>
			<DateMacro >Today</DateMacro>
		</TxnDateRangeFilter>
		*/
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Auto Inventory Import Request - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			$this->add_test_log($dlog_txt);
		}
		return $xml;
	}
	
	public function Auto_Wc_Inventory_AdjustmentQuery_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		//$this->add_test_log('Auto Inventory Import XML Response'.PHP_EOL .$xml);
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		global $wpdb;
		
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('AUTO_WC_UPDATE_INVENTORY', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}
		
		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$tot_updated = 0;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/InventoryAdjustmentQueryRs');
			
			foreach ($List->children() as $Item){
				foreach ($Item->children() as $Item_C){
					if($Item_C->name() == 'InventoryAdjustmentLineRet'){
						$ret = $Item_C->name();						
						$ItemRef = $Item_C->getChildDataAt($ret . ' ItemRef ListID');
						$this->Add_Pull_Inventory_Queue($ItemRef);
					}					
				}
			}
		}
		
		return true;
		
	}	
	
	
	public function Auto_Pull_Update_Wc_Inventory_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}		
		
		$timestamp_from = $this->get_auto_inventory_pull_m_dt_from($this->get_qbd_timezone());
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<ItemInventoryQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<FromModifiedDate>' . $timestamp_from . '</FromModifiedDate>
						<OwnerID>0</OwnerID>
					</ItemInventoryQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';
		
		/*ToModifiedDate*/
		
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Auto Inventory Import Request - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			$this->add_test_log($dlog_txt);
		}
		return $xml;
	}
	
	public function Pull_Update_Wc_Product_Price_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);
		global $wpdb;
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$tot_updated = 0;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemQueryRs');
			
			foreach ($List->children() as $Item){
				$type = substr(substr($Item->name(), 0, -3), 4);
				$ret = $Item->name();
				
				$ListID = $Item->getChildDataAt($ret . ' ListID');
				$Name = $Item->getChildDataAt($ret . ' Name');
				
				$qb_pf = $this->get_option('mw_wc_qbo_desk_pull_prd_price_field');
				if(empty($qb_pf)){$qb_pf = 'Price';}
				$Price = 0;
				
				$custom_price_field = $this->get_qbd_ext_price_field();
				if(is_array($custom_price_field) && in_array($qb_pf,$custom_price_field)){
					$is_cs_pf_ok = false;
					foreach ($Item->children() as $Item_C){
						if($is_cs_pf_ok){break;}
						
						if($Item_C->name() == 'DataExtRet'){
							$ret_c = $Item_C->name();						
							$DataExtName = $Item_C->getChildDataAt($ret_c . ' DataExtName');
							//$Item_C->getChildDataAt($ret_c . ' OwnerID')
							if($DataExtName == $qb_pf){
								$is_cs_pf_ok = true;
								$Price = $Item_C->getChildDataAt($ret_c . ' DataExtValue');
							}
						}					
					}
					
					if(empty($Price)){
						$qb_pf = 'Price';
						$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
					}
				}else{
					$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
				}				
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesOrPurchase Price');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesAndPurchase SalesPrice');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesPrice');
				}				
				
				$uwi_data = array();
				$uwi_data['qbo_product_id'] = $ListID;
				$uwi_data['Name'] = $Name;
				$uwi_data['Price'] = $Price;
				
				/**/
				if($this->is_plugin_active('woocommerce-wholesale-prices','woocommerce-wholesale-prices.bootstrap')){
					if($this->option_checked('mw_wc_qbo_desk_compt_whl_price_pull')){
						$whl_p_wf = $this->get_option('mw_wc_qbo_desk_compt_whl_price_pull_wf');
						$whl_p_qf = $this->get_option('mw_wc_qbo_desk_compt_whl_price_pull_qf');
						if(!empty($whl_p_wf) && !empty($whl_p_qf)){
							foreach ($Item->children() as $Item_C){
								if($Item_C->name() == 'DataExtRet'){
									$ret_c = $Item_C->name();						
									$DataExtName = $Item_C->getChildDataAt($ret_c . ' DataExtName');									
									if($DataExtName == $whl_p_qf){										
										$Wholesale_Price = $Item_C->getChildDataAt($ret_c . ' DataExtValue');
										$uwi_data['Wholesale_Price'] = $Wholesale_Price;
										break;
									}
								}
							}
						}
					}
				}
				
				
				if(is_array($extra) && count($extra) && isset($extra['manual']) && $extra['manual']){
					$uwi_data['manual'] = true;
				}
				//$this->add_test_log(print_r($uwi_data,true));
				
				$this->UpdateWooCommerceProductPrice($uwi_data);
				
				//Update Local Table Data
				$arr = array(
				'ListID' => $Item->getChildDataAt($ret . ' ListID'),
				'TimeCreated' => $Item->getChildDataAt($ret . ' TimeCreated'),
				'TimeModified' => $Item->getChildDataAt($ret . ' TimeModified'),
				'Name' => $Item->getChildDataAt($ret . ' Name'),
				'FullName' => $Item->getChildDataAt($ret . ' FullName'),
				'Type' => $type,
				'Parent_ListID' => $Item->getChildDataAt($ret . ' ParentRef ListID'),
				'Parent_FullName' => $Item->getChildDataAt($ret . ' ParentRef FullName'),
				'ManufacturerPartNumber' => $Item->getChildDataAt($ret . ' ManufacturerPartNumber'),
				'BarCodeValue' => $Item->getChildDataAt($ret . ' BarCodeValue'),
				'SalesTaxCode_ListID' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef ListID'),
				'SalesTaxCode_FullName' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef FullName'),
				'BuildPoint' => $Item->getChildDataAt($ret . ' BuildPoint'),
				'ReorderPoint' => $Item->getChildDataAt($ret . ' ReorderPoint'),
				'QuantityOnHand' => $Item->getChildDataAt($ret . ' QuantityOnHand'),
				'AverageCost' => $Item->getChildDataAt($ret . ' AverageCost'),
				'QuantityOnOrder' => $Item->getChildDataAt($ret . ' QuantityOnOrder'),
				'QuantityOnSalesOrder' => $Item->getChildDataAt($ret . ' QuantityOnSalesOrder'),
				'TaxRate' => $Item->getChildDataAt($ret . ' TaxRate'),
				'Price_ori' => $Item->getChildDataAt($ret . ' Price'),
				'Price' => $Price,
				);

				$look_for = array(
				'SalesPrice' => array( 'SalesOrPurchase Price', 'SalesAndPurchase SalesPrice', 'SalesPrice' ),
				'SalesDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase SalesDesc', 'SalesDesc' ),
				'PurchaseCost' => array( 'SalesOrPurchase Price', 'SalesAndPurchase PurchaseCost', 'PurchaseCost' ),
				'PurchaseDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase PurchaseDesc', 'PurchaseDesc' ),
				'PrefVendor_ListID' => array( 'SalesAndPurchase PrefVendorRef ListID', 'PrefVendorRef ListID' ),
				'PrefVendor_FullName' => array( 'SalesAndPurchase PrefVendorRef FullName', 'PrefVendorRef FullName' ),
				);

				foreach ($look_for as $field => $look_here){
					if (!empty($arr[$field])){
						break;
					}

					foreach ($look_here as $look){
						$arr[$field] = $Item->getChildDataAt($ret . ' ' . $look);
					}
				}
				
				$id = (int) $this->get_field_by_val($table,'id','qbd_id',$ListID);
				
				$save_data = array();
				$ip_name  = (!empty($arr['FullName']))?$arr['FullName']:$arr['Name'];
				$save_data['name'] = $ip_name;
				$save_data['sku'] = $arr['Name'];
				$save_data['product_type'] = $arr['Type'];
				$save_data['parent_id'] = $arr['Parent_ListID'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				if($id>0){
					$wpdb->update($table,$save_data,array('id'=>$id),'',array('%d'));
				}else{
					$save_data['qbd_id'] = $arr['ListID'];
					$wpdb->insert($table, $save_data);
				}
			}
		}
		
		return true;
	}
	
	public function Auto_Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		global $wpdb;
		
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('AUTO_WC_UPDATE_INVENTORY', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}
		
		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		
		$qp_type = 'Inventory';
		
		if(is_array($extra) && count($extra)){
			if(isset($extra['InventoryAssembly']) && $extra['InventoryAssembly']){
				$qp_type = 'InventoryAssembly';
			}
		}
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$tot_updated = 0;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			
			if($qp_type=='InventoryAssembly'){
				$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemInventoryAssemblyQueryRs');
			}else{
				$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemInventoryQueryRs');
			}
			
			foreach ($List->children() as $Item){
				$type = substr(substr($Item->name(), 0, -3), 4);
				$ret = $Item->name();
				
				$ListID = $Item->getChildDataAt($ret . ' ListID');
				$Name = $Item->getChildDataAt($ret . ' Name');
				$QuantityOnHand = $Item->getChildDataAt($ret . ' QuantityOnHand');
				
				$qb_pf = $this->get_option('mw_wc_qbo_desk_pull_prd_price_field');
				if(empty($qb_pf)){$qb_pf = 'Price';}
				$Price = 0;
				
				$custom_price_field = $this->get_qbd_ext_price_field();
				if(is_array($custom_price_field) && in_array($qb_pf,$custom_price_field)){
					$is_cs_pf_ok = false;
					foreach ($Item->children() as $Item_C){
						if($is_cs_pf_ok){break;}
						
						if($Item_C->name() == 'DataExtRet'){
							$ret_c = $Item_C->name();						
							$DataExtName = $Item_C->getChildDataAt($ret_c . ' DataExtName');
							//$Item_C->getChildDataAt($ret_c . ' OwnerID')
							if($DataExtName == $qb_pf){
								$is_cs_pf_ok = true;
								$Price = $Item_C->getChildDataAt($ret_c . ' DataExtValue');
							}
						}					
					}
					
					if(empty($Price)){
						$qb_pf = 'Price';
						$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
					}
				}else{
					$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesOrPurchase Price');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesAndPurchase SalesPrice');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesPrice');
				}
				
				$uwi_data = array();
				$uwi_data['qbo_inventory_id'] = $ListID;
				$uwi_data['Name'] = $Name;
				
				//
				$QuantityOnSalesOrder = $Item->getChildDataAt($ret . ' QuantityOnSalesOrder');
				
				$qqpf = $this->get_option('mw_wc_qbo_desk_pull_invnt_qty_field');
				if($qqpf == 'AvailableQuantity'){
					$qqpf_val = ($QuantityOnHand-$QuantityOnSalesOrder);
				}else{
					$qqpf_val = $QuantityOnHand;
				}
				
				$uwi_data['QuantityOnHand'] = $qqpf_val;
				
				if(is_array($extra) && count($extra) && isset($extra['manual']) && $extra['manual']){
					$uwi_data['manual'] = true;
				}
				
				$this->UpdateWooCommerceInventory($uwi_data);
				
				//Update Local Table Data
				$arr = array(
				'ListID' => $Item->getChildDataAt($ret . ' ListID'),
				'TimeCreated' => $Item->getChildDataAt($ret . ' TimeCreated'),
				'TimeModified' => $Item->getChildDataAt($ret . ' TimeModified'),
				'Name' => $Item->getChildDataAt($ret . ' Name'),
				'FullName' => $Item->getChildDataAt($ret . ' FullName'),
				'Type' => $type,
				'Parent_ListID' => $Item->getChildDataAt($ret . ' ParentRef ListID'),
				'Parent_FullName' => $Item->getChildDataAt($ret . ' ParentRef FullName'),
				'ManufacturerPartNumber' => $Item->getChildDataAt($ret . ' ManufacturerPartNumber'),
				'BarCodeValue' => $Item->getChildDataAt($ret . ' BarCodeValue'),
				'SalesTaxCode_ListID' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef ListID'),
				'SalesTaxCode_FullName' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef FullName'),
				'BuildPoint' => $Item->getChildDataAt($ret . ' BuildPoint'),
				'ReorderPoint' => $Item->getChildDataAt($ret . ' ReorderPoint'),
				'QuantityOnHand' => $Item->getChildDataAt($ret . ' QuantityOnHand'),
				'AverageCost' => $Item->getChildDataAt($ret . ' AverageCost'),
				'QuantityOnOrder' => $Item->getChildDataAt($ret . ' QuantityOnOrder'),
				'QuantityOnSalesOrder' => $Item->getChildDataAt($ret . ' QuantityOnSalesOrder'),
				'TaxRate' => $Item->getChildDataAt($ret . ' TaxRate'),
				'Price_ori' => $Item->getChildDataAt($ret . ' Price'),
				'Price' => $Price,
				);

				$look_for = array(
				'SalesPrice' => array( 'SalesOrPurchase Price', 'SalesAndPurchase SalesPrice', 'SalesPrice' ),
				'SalesDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase SalesDesc', 'SalesDesc' ),
				'PurchaseCost' => array( 'SalesOrPurchase Price', 'SalesAndPurchase PurchaseCost', 'PurchaseCost' ),
				'PurchaseDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase PurchaseDesc', 'PurchaseDesc' ),
				'PrefVendor_ListID' => array( 'SalesAndPurchase PrefVendorRef ListID', 'PrefVendorRef ListID' ),
				'PrefVendor_FullName' => array( 'SalesAndPurchase PrefVendorRef FullName', 'PrefVendorRef FullName' ),
				);

				foreach ($look_for as $field => $look_here){
					if (!empty($arr[$field])){
						break;
					}

					foreach ($look_here as $look){
						$arr[$field] = $Item->getChildDataAt($ret . ' ' . $look);
					}
				}
				
				$id = (int) $this->get_field_by_val($table,'id','qbd_id',$ListID);
				
				/*
				if($id>0){
					if($this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_sites_for_invnt_pull')!=''){
						unset($arr['QuantityOnHand']);
						unset($arr['QuantityOnOrder']);
						unset($arr['QuantityOnSalesOrder']);
					}					
				}
				*/
				
				$save_data = array();
				$ip_name  = (!empty($arr['FullName']))?$arr['FullName']:$arr['Name'];
				$save_data['name'] = $ip_name;
				$save_data['sku'] = $arr['Name'];
				$save_data['product_type'] = $arr['Type'];
				$save_data['parent_id'] = $arr['Parent_ListID'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				if($id>0){
					$wpdb->update($table,$save_data,array('id'=>$id),'',array('%d'));
				}else{
					$save_data['qbd_id'] = $arr['ListID'];
					$wpdb->insert($table, $save_data);
				}
			}
		}
		
		return true;
	}
	
	public function Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!is_array($extra)){
			$extra = array();
		}
		
		$extra['manual'] = true;
		$this->Auto_Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public function All_Pull_ItemSitesQuery_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		//$this->add_test_log('ItemSitesQuery Response: '.$this->get_cdt(). PHP_EOL .$xml);
		//return false;
		
		global $wpdb;
		
		$is_process_invnt_pull = false;
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('ALL_PULL_INVT_ISQ', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}else{
			$is_process_invnt_pull = true;
		}
		
		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		
		if($this->get_session_val('invnt_pull_item_site_iteratorID','',true)!=$iteratorID){			
			$this->unset_session('invnt_st_qty_arr');
			$this->unset_session('item_name_arr');
			$this->unset_session('item_asmb_arr');
			
			$this->unset_session('nt_invnt_st_qty_arr');
			
			$this->set_session_val('invnt_st_qty_arr',array());
			$this->set_session_val('item_name_arr',array());
			$this->set_session_val('item_asmb_arr',array());
			
			$this->set_session_val('nt_invnt_st_qty_arr',array());
		}
		
		$this->set_session_val('invnt_pull_item_site_iteratorID',$iteratorID);
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$tot_updated = 0;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemSitesQueryRs');
			
			//$invnt_st_qty_arr = array();			
			//$item_name_arr = array();
			//$item_asmb_arr = array();
			//$nt_invnt_st_qty_arr = array();
			
			$invnt_st_qty_arr = $this->get_session_val('invnt_st_qty_arr',array(),false);
			$item_name_arr = $this->get_session_val('item_name_arr',array(),false);
			$item_asmb_arr = $this->get_session_val('item_asmb_arr',array(),false);
			
			//
			$nt_invnt_st_qty_arr = $this->get_session_val('nt_invnt_st_qty_arr',array(),false);
			
			$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');
			
			foreach ($List->children() as $Item){
				$ret = $Item->name();				
				$InventoryRef = $Item->getChildDataAt($ret.' ItemInventoryRef ListID');
				$Name = $Item->getChildDataAt($ret.' ItemInventoryRef FullName');
				
				$InventoryAssembly = false;
				if(empty($InventoryRef)){
					$InventoryRef = $Item->getChildDataAt($ret.' ItemInventoryAssemblyRef ListID');
					$Name = $Item->getChildDataAt($ret.' ItemInventoryAssemblyRef FullName');
					$InventoryAssembly = true;
				}
				//$this->add_test_log($InventoryRef . ':' . $Name);
				if(!empty($InventoryRef)){
					$InventorySiteRef = $Item->getChildDataAt($ret.' InventorySiteRef ListID');
					$InventorySiteLocationRef = $Item->getChildDataAt($ret.' InventorySiteLocationRef ListID');					
					
					/*New Compatibility*/
					$wmior_active = $this->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
					$adv_inv_site = array();
					if($wmior_active){
						if($this->option_checked('mw_wc_qbo_desk_w_miors_ed') && is_array($mw_wc_qbo_desk_compt_wmior_lis_mv)){
							$adv_inv_site = $mw_wc_qbo_desk_compt_wmior_lis_mv;
							if(empty($adv_inv_site)){
								return true; //Prevent
							}
						}
					}else{
						$adv_inv_site = $this->get_option('mw_wc_qbo_desk_compt_qbd_invt_sites_for_invnt_pull');
						if(!empty($adv_inv_site)){
							$adv_inv_site = explode(',',$adv_inv_site);
						}
					}					
					
					//
					$Inv_Site_Check_Val = '';
					if($this->is_inv_site_bin_allowed()){
						if(!empty($InventorySiteLocationRef)){
							$Inv_Site_Check_Val = $InventorySiteRef.':'.$InventorySiteLocationRef;
						}						
					}else{
						$Inv_Site_Check_Val = $InventorySiteRef;
					}
					
					/*Added Later*/
					if(!$wmior_active && $this->option_checked('mw_wc_qbo_desk_compt_qbd_adv_inc_non_inv_st')){
						if(empty($InventorySiteRef)){
							$q_tmp_arr = array();
							$q_tmp_arr['QuantityOnHand'] = (float) $Item->getChildDataAt($ret.' QuantityOnHand');
							$q_tmp_arr['QuantityOnPurchaseOrders'] = (float) $Item->getChildDataAt($ret.' QuantityOnPurchaseOrders');
							$q_tmp_arr['QuantityOnSalesOrders'] = (float) $Item->getChildDataAt($ret.' QuantityOnSalesOrders');
							
							$s_tmp_arr = array(
								'Qty_Arr' => $q_tmp_arr
							);
							
							$nt_invnt_st_qty_arr[$InventoryRef][] = $s_tmp_arr;
							
							if(!isset($item_name_arr[$InventoryRef])){
								$item_name_arr[$InventoryRef] = $Name;
							}	

							if(!isset($item_asmb_arr[$InventoryRef])){
								$item_asmb_arr[$InventoryRef] = $InventoryAssembly;
							}
						}
					}
					
					if(!empty($Inv_Site_Check_Val) && is_array($adv_inv_site) && count($adv_inv_site) && in_array($Inv_Site_Check_Val,$adv_inv_site)){
						$QuantityOnHand = (float) $Item->getChildDataAt($ret.' QuantityOnHand');
						$QuantityOnPurchaseOrders = (float) $Item->getChildDataAt($ret.' QuantityOnPurchaseOrders');
						$QuantityOnSalesOrders = (float) $Item->getChildDataAt($ret.' QuantityOnSalesOrders');
						
						$InventorySiteRef_FullName = $Item->getChildDataAt($ret.' InventorySiteRef FullName');
						$InventorySiteLocationRef_FullName = $Item->getChildDataAt($ret.' InventorySiteLocationRef FullName');
						
						$q_tmp_arr = array();
						$q_tmp_arr['QuantityOnHand'] = $QuantityOnHand;
						$q_tmp_arr['QuantityOnPurchaseOrders'] = $QuantityOnPurchaseOrders;
						$q_tmp_arr['QuantityOnSalesOrders'] = $QuantityOnSalesOrders;
						
						$s_tmp_arr = array(
							'InventorySiteRef' => $InventorySiteRef,
							'InventorySiteRef_FullName' => $InventorySiteRef_FullName,
							'InventorySiteLocationRef' => $InventorySiteLocationRef,
							'InventorySiteLocationRef_FullName' => $InventorySiteLocationRef_FullName,
							'Qty_Arr' => $q_tmp_arr
						);
						
						$invnt_st_qty_arr[$InventoryRef][] = $s_tmp_arr;
						
						if(!isset($item_name_arr[$InventoryRef])){
							$item_name_arr[$InventoryRef] = $Name;
						}	

						if(!isset($item_asmb_arr[$InventoryRef])){
							$item_asmb_arr[$InventoryRef] = $InventoryAssembly;
						}
					}
				}				
				
			}
			
			/*
			$this->add_test_log(print_r($invnt_st_qty_arr,true));
			$this->add_test_log(print_r($item_name_arr,true));
			$this->add_test_log(print_r($item_asmb_arr,true));
			
			$this->add_test_log(print_r($nt_invnt_st_qty_arr,true));
			*/
			
			/**/
			$this->set_session_val('invnt_st_qty_arr',$invnt_st_qty_arr);
			$this->set_session_val('item_name_arr',$item_name_arr);
			$this->set_session_val('item_asmb_arr',$item_asmb_arr);
			
			//
			$this->set_session_val('nt_invnt_st_qty_arr',$nt_invnt_st_qty_arr);
			
			if(!$is_process_invnt_pull){
				return true;
			}
			
			/*New*/
			if($wmior_active){
				if(count($invnt_st_qty_arr)){
					$awptsu_arr = array();
					foreach($invnt_st_qty_arr as $sqa_k => $sqa_v){
						if(is_array($sqa_v) && count($sqa_v)){
							$wmior_tsu_wpid = 0;
							foreach($sqa_v as $sqa_v_c){
								//InventorySiteRef//InventorySiteRef_FullName//InventorySiteLocationRef//InventorySiteLocationRef_FullName
								if(is_array($sqa_v_c) && count($sqa_v_c)){
									if(isset($sqa_v_c['Qty_Arr']) && is_array($sqa_v_c['Qty_Arr']) && count($sqa_v_c['Qty_Arr'])){
										$Qty_Arr = $sqa_v_c['Qty_Arr'];
										$uwi_data = array();
										$uwi_data['qbo_inventory_id'] = $sqa_k;
										
										//$Name = $this->get_field_by_val($table,'name','qbd_id',$sqa_k);
										$uwi_data['Name'] = (isset($item_name_arr[$sqa_k]))?$item_name_arr[$sqa_k]:'';
										//
										$qqpf = $this->get_option('mw_wc_qbo_desk_pull_invnt_qty_field');
										if($qqpf == 'AvailableQuantity'){
											$qqpf_val = ($Qty_Arr['QuantityOnHand']-$Qty_Arr['QuantityOnSalesOrders']);
										}else{
											$qqpf_val = $Qty_Arr['QuantityOnHand'];
										}
										
										/**/
										$uwi_data['QuantityOnHand_Original'] = $Qty_Arr['QuantityOnHand'];
										$uwi_data['QuantityOnSalesOrders'] = $Qty_Arr['QuantityOnSalesOrders'];
										
										$uwi_data['QuantityOnHand'] = $qqpf_val;
										$uwi_data['InventoryAssembly'] = (isset($item_asmb_arr[$sqa_k]))?$item_asmb_arr[$sqa_k]:false;
										
										$uwi_data['wmior_compt'] = true;
										$uwi_data['InventorySiteRef'] = $sqa_v_c['InventorySiteRef'];
										$uwi_data['InventorySiteRef_FullName'] = $sqa_v_c['InventorySiteRef_FullName'];
										$uwi_data['InventorySiteLocationRef'] = $sqa_v_c['InventorySiteLocationRef'];
										$uwi_data['InventorySiteLocationRef_FullName'] = $sqa_v_c['InventorySiteLocationRef_FullName'];
										
										$uwi_data['mw_wc_qbo_desk_compt_wmior_lis_mv'] = $mw_wc_qbo_desk_compt_wmior_lis_mv;
										
										$uwi_rv = $this->UpdateWooCommerceInventory($uwi_data); //Need to Change the process later for optimization
										if(!$wmior_tsu_wpid && intval($uwi_rv)){
											$wmior_tsu_wpid = intval($uwi_rv);
										}
									}
								}
							}
							
							//
							if($wmior_tsu_wpid > 0 && !isset($awptsu_arr[$wmior_tsu_wpid])){
								$awptsu_arr[$wmior_tsu_wpid] = $sqa_k;
								$qpn = (isset($item_name_arr[$sqa_k]))?$item_name_arr[$sqa_k]:'';
								$this->Adjust_wmior_product_total_stock_after_locations_stock_update($wmior_tsu_wpid,$sqa_k,$qpn);
								//recalculate_total_product_locations_stocks($wmior_tsu_wpid);//WMIOR Plugin Function
							}							
						}
					}
				}
				return true;
			}
			
			$s_qty_sum_arr = array();
			if(count($invnt_st_qty_arr)){				
				foreach($invnt_st_qty_arr as $sqa_k => $sqa_v){
					if(is_array($sqa_v) && count($sqa_v)){
						$QuantityOnHand_Sum = 0;
						$QuantityOnPurchaseOrders_Sum = 0;
						$QuantityOnSalesOrders_Sum = 0;
						$is_sum_added = false;
						foreach($sqa_v as $sqa_v_c){
							if(is_array($sqa_v_c) && count($sqa_v_c)){
								if(isset($sqa_v_c['Qty_Arr']) && is_array($sqa_v_c['Qty_Arr']) && count($sqa_v_c['Qty_Arr'])){
									$Qty_Arr = $sqa_v_c['Qty_Arr'];
									$is_sum_added = true;
									$QuantityOnHand_Sum+= $Qty_Arr['QuantityOnHand'];
									$QuantityOnPurchaseOrders_Sum+= $Qty_Arr['QuantityOnPurchaseOrders'];
									$QuantityOnSalesOrders_Sum+= $Qty_Arr['QuantityOnSalesOrders'];
								}
							}
							
						}
						if($is_sum_added){
							$tmp_sum_arr = array();
							$tmp_sum_arr['QuantityOnHand'] = $QuantityOnHand_Sum;
							$tmp_sum_arr['QuantityOnPurchaseOrders'] = $QuantityOnPurchaseOrders_Sum;
							$tmp_sum_arr['QuantityOnSalesOrders'] = $QuantityOnSalesOrders_Sum;
							
							$s_qty_sum_arr[$sqa_k] = $tmp_sum_arr;
						}
						
					}
				}
			}
			
			/*Not Inventory Site Qty Sum*/
			if(count($nt_invnt_st_qty_arr) && count($s_qty_sum_arr)){
				foreach($s_qty_sum_arr as $qty_sum_k => $qty_sum_v){
					if(isset($nt_invnt_st_qty_arr[$qty_sum_k]) && is_array($nt_invnt_st_qty_arr[$qty_sum_k])){
						foreach($nt_invnt_st_qty_arr[$qty_sum_k] as $nsqa_v){
							if(is_array($nsqa_v) && count($nsqa_v)){
								if(isset($nsqa_v['Qty_Arr']) && is_array($nsqa_v['Qty_Arr']) && count($nsqa_v['Qty_Arr'])){
									$Qty_Arr = $nsqa_v['Qty_Arr'];
									if(is_array($Qty_Arr) && !empty($Qty_Arr)){
										$s_qty_sum_arr[$qty_sum_k]['QuantityOnHand'] += $Qty_Arr['QuantityOnHand'];
										$s_qty_sum_arr[$qty_sum_k]['QuantityOnPurchaseOrders'] += $Qty_Arr['QuantityOnPurchaseOrders'];
										$s_qty_sum_arr[$qty_sum_k]['QuantityOnSalesOrders'] += $Qty_Arr['QuantityOnSalesOrders'];
									}
								}
							}
						}
					}
				}
			}
			
			if(count($s_qty_sum_arr)){
				//$this->add_test_log(print_r($invnt_st_qty_arr,true));
				//$this->add_test_log(print_r($s_qty_sum_arr,true));
				
				foreach($s_qty_sum_arr as $qty_sum_k => $qty_sum_v){
					$uwi_data = array();
					$uwi_data['qbo_inventory_id'] = $qty_sum_k;
					
					//$Name = $this->get_field_by_val($table,'name','qbd_id',$qty_sum_k);
					$uwi_data['Name'] = (isset($item_name_arr[$qty_sum_k]))?$item_name_arr[$qty_sum_k]:'';
					//
					$qqpf = $this->get_option('mw_wc_qbo_desk_pull_invnt_qty_field');
					if($qqpf == 'AvailableQuantity'){
						$qqpf_val = ($qty_sum_v['QuantityOnHand']-$qty_sum_v['QuantityOnSalesOrders']);
					}else{
						$qqpf_val = $qty_sum_v['QuantityOnHand'];
					}
					
					/**/
					$uwi_data['QuantityOnHand_Original'] = $qty_sum_v['QuantityOnHand'];
					$uwi_data['QuantityOnSalesOrders'] = $qty_sum_v['QuantityOnSalesOrders'];
					
					$uwi_data['QuantityOnHand'] = $qqpf_val;
					$uwi_data['InventoryAssembly'] = (isset($item_asmb_arr[$qty_sum_k]))?$item_asmb_arr[$qty_sum_k]:false;
					
					$this->UpdateWooCommerceInventory($uwi_data);
					
					/*
					$InventoryRef = $this->sanitize($qty_sum_k);
					$e_qbd_prd = $this->get_row("SELECT * FROM {$table} WHERE `qbd_id` = '{$InventoryRef}' ");
					if(is_array($e_qbd_prd) && count($e_qbd_prd)){
						$id = (int) $e_qbd_prd['id'];
						if(!empty($e_qbd_prd['info_arr'])){
							$info_arr = @unserialize($e_qbd_prd['info_arr']);
							if(is_array($info_arr) && count($info_arr)){
								$info_arr['QuantityOnHand'] = $qty_sum_v['QuantityOnHand'];
								$info_arr['QuantityOnOrder'] = $qty_sum_v['QuantityOnPurchaseOrders'];
								$info_arr['QuantityOnSalesOrder'] = $qty_sum_v['QuantityOnSalesOrders'];
								
								$info_arr = serialize($info_arr);
								$save_data = array();
								$save_data['info_arr'] = $info_arr;
								$wpdb->update($table,$save_data,array('id'=>$id),'',array('%d'));
							}
						}						
					}
					*/
				}
			}
		}
		
		return true;
	}
	
	public function All_Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		//$this->save_log(array('log_type'=>'Test','log_title'=>'Test','details'=>'Test','status'=>2));
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('ALL_WC_UPDATE_INVENTORY', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}		
		//$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		$idents = array();		
		$this->Auto_Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	public function All_Pull_Update_Wc_Pricing_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){		
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('ALL_WC_UPDATE_PRICING', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}		
		//$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		$idents = array();		
		$this->Pull_Update_Wc_Product_Price_Response($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	/**/
	public function InvoiceQuery_FBW_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log('InvoiceQuery_FBW Response: '.$this->get_cdt(). PHP_EOL .$xml);
		global $wpdb;
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('InvoiceQuery_FBW', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}
		
		//$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';	
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$invoice_num_import_array = array();
		$inia_tn_arr = array();
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/InvoiceQueryRs');
			
			foreach ($List->children() as $Invoice){
				$ret = $Invoice->name();
				
				$TxnID = $Invoice->getChildDataAt($ret . ' TxnID');
				$TxnDate = $Invoice->getChildDataAt($ret . ' TxnDate');
				$RefNumber = $Invoice->getChildDataAt($ret . ' RefNumber');
				
				$DueDate = $Invoice->getChildDataAt($ret . ' DueDate');
				$TrackingNumber = '';
				
				if(count($Invoice->children())){
					foreach ($Invoice->children() as $Invoice_c){
						$ret_c = $Invoice_c->name();
						if(empty($TrackingNumber) && $ret_c == 'InvoiceLineRet'){
							if($Invoice_c->getChildDataAt($ret_c.' ItemRef FullName') == 'Shipping'){								
								$TrackingNumber = $Invoice_c->getChildDataAt($ret_c.' Desc');
								$TrackingNumber = str_replace('Tkng#: ','',$TrackingNumber);
								$inia_tn_arr[$TxnID] = $TrackingNumber;
							}							
						}
						
						if($ret_c == 'LinkedTxn'){
							$LinkedTxn_TxnType = $Invoice_c->getChildDataAt($ret_c.' TxnType');
							if($LinkedTxn_TxnType == 'SalesOrder'){
								$So_TxnID = $Invoice_c->getChildDataAt($ret_c.' TxnID');
								$So_TxnDate = $Invoice_c->getChildDataAt($ret_c.' TxnDate');
								$So_RefNumber = $Invoice_c->getChildDataAt($ret_c.' RefNumber');
								
								$So_LinkType = $Invoice_c->getChildDataAt($ret_c.' LinkType');
								$So_Amount = $Invoice_c->getChildDataAt($ret_c.' Amount');
								$invoice_num_import_array[$TxnID] = array(
									'TxnID' => $TxnID,
									'TxnDate' => $TxnDate,
									'RefNumber' => $RefNumber,									
									'DueDate' => $DueDate,
									//'TrackingNumber' => $TrackingNumber,									
									'L_So_Data' => array(
										'So_TxnID' => $So_TxnID,
										'So_TxnDate' => $So_TxnDate,
										'So_RefNumber' => $So_RefNumber,
										'So_LinkType' => $So_LinkType,
										'So_Amount' => $So_Amount,
									),
								);
								//break;
							}
						}
					}
				}
			}
		}
		
		//$this->add_test_log(print_r($invoice_num_import_array,true));
		if(is_array($invoice_num_import_array) && !empty($invoice_num_import_array)){
			foreach($invoice_num_import_array as $inia_k => $inia_v){
				$So_TxnID = $inia_v['L_So_Data']['So_TxnID'];
				$wmo_id = (int) $this->get_qbd_data_pair_val('Order',$So_TxnID);
				if($wmo_id > 0){
					$wmo_meta = get_post_meta($wmo_id);
					$invoice_number = isset($wmo_meta['invoice_number'][0])?$wmo_meta['invoice_number'][0]:'';
					$invoice_date = isset($wmo_meta['invoice_date'][0])?$wmo_meta['invoice_date'][0]:'';
					$tracking_number = isset($wmo_meta['tracking_number'][0])?$wmo_meta['tracking_number'][0]:'';
					$s_o_number = isset($wmo_meta['s_o_number'][0])?$wmo_meta['s_o_number'][0]:'';
					
					$RefNumber = $inia_v['RefNumber'];
					$TxnDate = $inia_v['TxnDate'];
					//$TrackingNumber = $inia_v['TrackingNumber'];
					$TrackingNumber = (isset($inia_tn_arr[$inia_k]))?$inia_tn_arr[$inia_k]:'';
					
					$So_RefNumber = $inia_v['L_So_Data']['So_RefNumber'];
					
					if(!empty($RefNumber) && $invoice_number != $RefNumber){
						update_post_meta($wmo_id,'invoice_number',$RefNumber);
					}
					
					if(!empty($TxnDate) && $invoice_date != $TxnDate){
						update_post_meta($wmo_id,'invoice_date',$TxnDate);
					}
					
					if(!empty($TrackingNumber) && $tracking_number != $TrackingNumber){
						update_post_meta($wmo_id,'tracking_number',$TrackingNumber);
					}
					
					if(!empty($So_RefNumber) && $s_o_number != $So_RefNumber){
						update_post_meta($wmo_id,'s_o_number',$So_RefNumber);
					}
				}
			}
		}
		
	}
	
	//
	public function All_Pull_Update_Wc_Inventory_A_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue('ALL_WC_UPDATE_INVENTORY_A', null, 0, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}		
		//$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';
		$idents = array();
		if(!is_array($extra)){
			$extra = array();
		}		
		$extra['InventoryAssembly'] = true;
		$idents = array();
		$this->Auto_Pull_Update_Wc_Inventory_Response($requestID, $user, $action, $ID, $extra, $err, $last_action_time, $last_actionident_time, $xml, $idents);
	}
	
	/*Pull Functions End*/

	/*Import Functions Start*/
	
	/*Vendor Import Request/Response*/
	public function Vendor_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<VendorQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<!--<FromModifiedDate>' . $last . '</FromModifiedDate>-->
						<OwnerID>0</OwnerID>
					</VendorQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';
		
		/*
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Vendor Import Request - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			$this->add_test_log($dlog_txt);
		}
		*/
		
		return $xml;

	}
	
	public function Vendor_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));
		global $wpdb;
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue(QUICKBOOKS_IMPORT_VENDOR, null, QB_PRIORITY_REFRESH_DATA_IMPORT, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}

		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/VendorQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_vendors';

			/*Delete Old Data*/
			if($this->get_session_val('import_vendor_iteratorID','',true)!=$iteratorID){
				$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
				$wpdb->query("TRUNCATE TABLE {$table}");
			}

			//
			$this->set_session_val('import_vendor_iteratorID',$iteratorID);
			
			foreach ($List->children() as $Vendor){
				$tot_imported++;
				$arr = array(
				'ListID' => $Vendor->getChildDataAt('VendorRet ListID'),
				'TimeCreated' => $Vendor->getChildDataAt('VendorRet TimeCreated'),
				'TimeModified' => $Vendor->getChildDataAt('VendorRet TimeModified'),
				'Email' => $Vendor->getChildDataAt('VendorRet Email'),
				'Name' => $Vendor->getChildDataAt('VendorRet Name'),				
				'FirstName' => $Vendor->getChildDataAt('VendorRet FirstName'),
				'MiddleName' => $Vendor->getChildDataAt('VendorRet MiddleName'),
				'LastName' => $Vendor->getChildDataAt('VendorRet LastName'),
				'JobTitle' => $Vendor->getChildDataAt('VendorRet JobTitle'),
				
				'Contact' => $Vendor->getChildDataAt('VendorRet Contact'),
				'Phone' => $Vendor->getChildDataAt('VendorRet Phone'),
				'AltPhone' => $Vendor->getChildDataAt('VendorRet AltPhone'),
				'Fax' => $Vendor->getChildDataAt('VendorRet Fax'),
				'CompanyName' => $Vendor->getChildDataAt('VendorRet CompanyName'),
				
				'AccountNumber' => $Vendor->getChildDataAt('VendorRet AccountNumber'),
				
				'IsVendorEligibleFor1099' => $Vendor->getChildDataAt('VendorRet IsVendorEligibleFor1099'),
				'VendorTaxIdent' => $Vendor->getChildDataAt('VendorRet VendorTaxIdent'),
				
				'CreditLimit' => $Vendor->getChildDataAt('VendorRet CreditLimit'),

				'VendorAddress_Addr1' => $Vendor->getChildDataAt('VendorRet VendorAddress Addr1'),
				'VendorAddress_Addr2' => $Vendor->getChildDataAt('VendorRet VendorAddress Addr2'),
				
				'VendorAddress_Addr3' => $Vendor->getChildDataAt('VendorRet VendorAddress Addr3'),
				'VendorAddress_Addr4' => $Vendor->getChildDataAt('VendorRet VendorAddress Addr4'),
				'VendorAddress_Addr5' => $Vendor->getChildDataAt('VendorRet VendorAddress Addr5'),
				
				'VendorAddress_City' => $Vendor->getChildDataAt('VendorRet VendorAddress City'),
				'VendorAddress_State' => $Vendor->getChildDataAt('VendorRet VendorAddress State'),
				'VendorAddress_PostalCode' => $Vendor->getChildDataAt('VendorRet VendorAddress PostalCode'),
				'VendorAddress_Country' => $Vendor->getChildDataAt('VendorRet VendorAddress Country'),
				'VendorAddress_Note' => $Vendor->getChildDataAt('VendorRet VendorAddress Note'),
				

				'ShipAddress_Addr1' => $Vendor->getChildDataAt('VendorRet ShipAddress Addr1'),
				'ShipAddress_Addr2' => $Vendor->getChildDataAt('VendorRet ShipAddress Addr2'),
				
				'ShipAddress_Addr3' => $Vendor->getChildDataAt('VendorRet ShipAddress Addr3'),
				'ShipAddress_Addr4' => $Vendor->getChildDataAt('VendorRet ShipAddress Addr4'),
				'ShipAddress_Addr5' => $Vendor->getChildDataAt('VendorRet ShipAddress Addr5'),
				
				'ShipAddress_City' => $Vendor->getChildDataAt('VendorRet ShipAddress City'),
				'ShipAddress_State' => $Vendor->getChildDataAt('VendorRet ShipAddress State'),
				'ShipAddress_PostalCode' => $Vendor->getChildDataAt('VendorRet ShipAddress PostalCode'),
				'ShipAddress_Country' => $Vendor->getChildDataAt('VendorRet ShipAddress Country'),
				'ShipAddress_Note' => $Vendor->getChildDataAt('VendorRet ShipAddress Note'),
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_vendorid'] = $arr['ListID'];
				$save_data['email'] = $arr['Email'];
				$save_data['first_name'] = $arr['FirstName'];
				$save_data['middle_name'] = $arr['MiddleName'];
				$save_data['last_name'] = $arr['LastName'];
				$save_data['company'] = $arr['CompanyName'];
				$save_data['d_name'] = $arr['Name'];
				//
				$save_data['jobtitle'] = $arr['JobTitle'];
				$save_data['info_arr'] = serialize($arr);
				
				$save_data['info_qbxml_obj'] = '';
				//$this->add_test_log(print_r($save_data,true));
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;
				//$this->add_test_log($wpdb->last_error);

			}
		}
		
		if($tot_imported>0){
			$log_title = "Import Vendor Successfully";
			$details = "Total Vendor Imported:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Import Vendor Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Vendor Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'Vendor','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/**/
	public function CustomerMod_Query_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$ListID = '';
		if(is_array($extra) && !empty($extra) && isset($extra['ListID'])){
			$ListID = $extra['ListID'];
		}
		
		if(empty($ListID)){
			return false;
		}
		
		$xml = '<?xml version="1.0" encoding="utf-8"?>
		<?qbxml version="' . $version . '"?>
		<QBXML>
			<QBXMLMsgsRq onError="'.$this->getonError().'">
				<CustomerQueryRq requestID="' . $requestID . '">								
					<ListID >'.$ListID.'</ListID>
				</CustomerQueryRq>
			</QBXMLMsgsRq>
		</QBXML>';
		
		return $xml;
	}
	
	/*Customer Import Request/Response*/
	public function Customer_Import_ByName_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$is_guest = false;
		if(is_array($extra) && !empty($extra)){
			if(isset($extra['is_guest']) && $extra['is_guest'] == 1){
				$is_guest = true;
			}
		}
		$ID = intval($ID);
		if($ID>0){
			if($is_guest){
				$customer_data = $this->get_wc_customer_info_from_order($ID);
			}else{
				$customer_data = $this->get_wc_customer_info($ID);
			}
			
			if(is_array($customer_data) && !empty($customer_data)){
				$name_replace_chars = array(':','\t','\n');
				$display_name = $this->get_array_isset($customer_data,'display_name','',true,100,false,$name_replace_chars);
				
				if(!empty($display_name)){
					/**/
					$active_filter = 'ActiveOnly';
					if($this->option_checked('mw_wc_qbo_desk_cus_imp_inc_inactive_sts')){
						$active_filter = 'All';
					}
					//<ActiveStatus>' . $active_filter . '</ActiveStatus>
					
					$xml = '<?xml version="1.0" encoding="utf-8"?>
					<?qbxml version="' . $version . '"?>
					<QBXML>
						<QBXMLMsgsRq onError="'.$this->getonError().'">
							<CustomerQueryRq requestID="' . $requestID . '">								
								<NameFilter>
									<MatchCriterion>Contains</MatchCriterion>
									<Name>'.$display_name.'</Name>
								</NameFilter>								
							</CustomerQueryRq>
						</QBXMLMsgsRq>
					</QBXML>';
					
					/*
					<MaxReturned>1</MaxReturned>							
					<OwnerID>0</OwnerID>
					*/
					
					$gc_txt = ($is_guest)?'Guest':'Customer';
					
					if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
						$dlog_txt = PHP_EOL . $gc_txt.' Import Request By Name #'.$ID.' - '.$this->get_cdt(). PHP_EOL;
						$dlog_txt.=$xml;
						$this->add_test_log($dlog_txt);
					}
					return $xml;
				}
			}
		}
	}
	
	public function Customer_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}
		
		/**/
		$active_filter = 'ActiveOnly';
		if($this->option_checked('mw_wc_qbo_desk_cus_imp_inc_inactive_sts')){
			$active_filter = 'All';
		}
		
		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<CustomerQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<!--<FromModifiedDate>' . $last . '</FromModifiedDate>-->
						<ActiveStatus>' . $active_filter . '</ActiveStatus>
						<OwnerID>0</OwnerID>
					</CustomerQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';
		
		/*
		if($this->option_checked('mw_wc_qbo_desk_add_xml_req_into_log')){
			$dlog_txt = PHP_EOL .'Customer Import Request - '.$this->get_cdt(). PHP_EOL;
			$dlog_txt.=$xml;
			$this->add_test_log($dlog_txt);
		}
		*/
		
		return $xml;

	}
	
	/**/
	public function CustomerMod_Query_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}		
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerQueryRs');
			
			$ListID = (is_array($extra) && isset($extra['ListID']))?$extra['ListID']:'';
			
			foreach ($List->children() as $Customer){
				$EditSequence = $Customer->getChildDataAt('CustomerRet EditSequence');
				$ListID_L = $Customer->getChildDataAt('CustomerRet ListID');
				
				if($ListID == $ListID_L){
					$Queue = new QuickBooks_WebConnector_Queue($this->get_dsn());
					$Queue->enqueue(QUICKBOOKS_MOD_CUSTOMER, $ID,3,array('ListID'=>$ListID, 'EditSequence'=>$EditSequence),$this->get_qbun());
				}
				
				break;
			}			
		}
	}
	
	/**/
	public function Customer_Import_ByName_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		
		$is_guest = false;
		if(is_array($extra) && !empty($extra)){
			if(isset($extra['is_guest']) && $extra['is_guest'] == 1){
				$is_guest = true;
			}
		}
		
		//$this->add_test_log($xml);
		
		global $wpdb;
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);
		
		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerQueryRs');
			
			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';
			
			foreach ($List->children() as $Customer){				
				$ListID = $Customer->getChildDataAt('CustomerRet ListID');
				if(!$this->get_field_by_val($table,'id','qbd_customerid',$ListID)){
					
					$arr = array(
					'ListID' => $ListID,
					'TimeCreated' => $Customer->getChildDataAt('CustomerRet TimeCreated'),
					'TimeModified' => $Customer->getChildDataAt('CustomerRet TimeModified'),
					'Email' => $Customer->getChildDataAt('CustomerRet Email'),
					'Name' => $Customer->getChildDataAt('CustomerRet Name'),
					'FullName' => $Customer->getChildDataAt('CustomerRet FullName'),
					'FirstName' => $Customer->getChildDataAt('CustomerRet FirstName'),
					'MiddleName' => $Customer->getChildDataAt('CustomerRet MiddleName'),
					'LastName' => $Customer->getChildDataAt('CustomerRet LastName'),
					'Contact' => $Customer->getChildDataAt('CustomerRet Contact'),
					'CompanyName' => $Customer->getChildDataAt('CustomerRet CompanyName'),
					
					'AccountNumber' => $Customer->getChildDataAt('CustomerRet AccountNumber'),
					
					'IsActive' => $Customer->getChildDataAt('CustomerRet IsActive'),

					'BillAddress_Addr1' => $Customer->getChildDataAt('CustomerRet BillAddress Addr1'),
					'BillAddress_Addr2' => $Customer->getChildDataAt('CustomerRet BillAddress Addr2'),
					'BillAddress_City' => $Customer->getChildDataAt('CustomerRet BillAddress City'),
					'BillAddress_State' => $Customer->getChildDataAt('CustomerRet BillAddress State'),
					'BillAddress_PostalCode' => $Customer->getChildDataAt('CustomerRet BillAddress PostalCode'),
					'BillAddress_Country' => $Customer->getChildDataAt('CustomerRet BillAddress Country'),

					'ShipAddress_Addr1' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr1'),
					'ShipAddress_Addr2' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr2'),
					'ShipAddress_City' => $Customer->getChildDataAt('CustomerRet ShipAddress City'),
					'ShipAddress_State' => $Customer->getChildDataAt('CustomerRet ShipAddress State'),
					'ShipAddress_PostalCode' => $Customer->getChildDataAt('CustomerRet ShipAddress PostalCode'),
					'ShipAddress_Country' => $Customer->getChildDataAt('CustomerRet ShipAddress Country'),
					);
					
					//
					$is_status_ok  = true;
					if($arr['IsActive'] != 'true' && !$this->option_checked('mw_wc_qbo_desk_cus_imp_inc_inactive_sts')){
						$is_status_ok  = false;
					}
					
					if($is_status_ok){
						$save_data = array();
						$save_data['qbd_customerid'] = $arr['ListID'];
						$save_data['email'] = $arr['Email'];
						$save_data['first_name'] = $arr['FirstName'];
						$save_data['middle_name'] = $arr['MiddleName'];
						$save_data['last_name'] = $arr['LastName'];
						$save_data['company'] = $arr['CompanyName'];
						
						$save_data['acc_num'] = $arr['AccountNumber'];
						
						$save_data['d_name'] = $arr['Name'];
						//
						$save_data['fullname'] = $arr['FullName'];
						$save_data['info_arr'] = serialize($arr);
						$save_data['info_qbxml_obj'] = '';
						
						$wpdb->insert($table, $save_data);
						$tot_imported++;
					}
					
				}
				
				break;
			}
		}
		
		if($tot_imported>0){
			$log_title = "Import Customer By Name Successfully";
			$details = "Total Customer Imported:{$tot_imported}";
			$status = 1;
			//$this->save_log(array('log_type'=>'Customer','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);
			
			/**/
			$this->remove_add_guest_customer_queue_after_ibn_success($ID,$is_guest);
			
		}else{
			/*
			$log_title = "Import Customer By Name Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Customer Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
			*/
		}
		//$this->save_log(array('log_type'=>'Customer','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	public function Customer_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//$this->add_test_log(print_r($idents,true));
		global $wpdb;
		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue(QUICKBOOKS_IMPORT_CUSTOMER, null, QB_PRIORITY_REFRESH_DATA_IMPORT, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}

		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers';

			/*Delete Old Data*/
			if($this->get_session_val('import_customer_iteratorID','',true)!=$iteratorID){
				$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
				$wpdb->query("TRUNCATE TABLE {$table}");
			}

			//
			$this->set_session_val('import_customer_iteratorID',$iteratorID);

			foreach ($List->children() as $Customer){
				$tot_imported++;
				$arr = array(
				'ListID' => $Customer->getChildDataAt('CustomerRet ListID'),
				'TimeCreated' => $Customer->getChildDataAt('CustomerRet TimeCreated'),
				'TimeModified' => $Customer->getChildDataAt('CustomerRet TimeModified'),
				'Email' => $Customer->getChildDataAt('CustomerRet Email'),
				'Name' => $Customer->getChildDataAt('CustomerRet Name'),
				'FullName' => $Customer->getChildDataAt('CustomerRet FullName'),
				'FirstName' => $Customer->getChildDataAt('CustomerRet FirstName'),
				'MiddleName' => $Customer->getChildDataAt('CustomerRet MiddleName'),
				'LastName' => $Customer->getChildDataAt('CustomerRet LastName'),
				'Contact' => $Customer->getChildDataAt('CustomerRet Contact'),
				'CompanyName' => $Customer->getChildDataAt('CustomerRet CompanyName'),
				
				'AccountNumber' => $Customer->getChildDataAt('CustomerRet AccountNumber'),
				
				'IsActive' => $Customer->getChildDataAt('CustomerRet IsActive'),

				'BillAddress_Addr1' => $Customer->getChildDataAt('CustomerRet BillAddress Addr1'),
				'BillAddress_Addr2' => $Customer->getChildDataAt('CustomerRet BillAddress Addr2'),
				'BillAddress_City' => $Customer->getChildDataAt('CustomerRet BillAddress City'),
				'BillAddress_State' => $Customer->getChildDataAt('CustomerRet BillAddress State'),
				'BillAddress_PostalCode' => $Customer->getChildDataAt('CustomerRet BillAddress PostalCode'),
				'BillAddress_Country' => $Customer->getChildDataAt('CustomerRet BillAddress Country'),

				'ShipAddress_Addr1' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr1'),
				'ShipAddress_Addr2' => $Customer->getChildDataAt('CustomerRet ShipAddress Addr2'),
				'ShipAddress_City' => $Customer->getChildDataAt('CustomerRet ShipAddress City'),
				'ShipAddress_State' => $Customer->getChildDataAt('CustomerRet ShipAddress State'),
				'ShipAddress_PostalCode' => $Customer->getChildDataAt('CustomerRet ShipAddress PostalCode'),
				'ShipAddress_Country' => $Customer->getChildDataAt('CustomerRet ShipAddress Country'),
				);

				//QuickBooks_Utilities::log($this->dsn, 'Importing customer ' . $arr['FullName'] . ': ' . print_r($arr, true));
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_customerid'] = $arr['ListID'];
				$save_data['email'] = $arr['Email'];
				$save_data['first_name'] = $arr['FirstName'];
				$save_data['middle_name'] = $arr['MiddleName'];
				$save_data['last_name'] = $arr['LastName'];
				$save_data['company'] = $arr['CompanyName'];
				
				$save_data['acc_num'] = $arr['AccountNumber'];
				
				$save_data['d_name'] = $arr['Name'];
				//
				$save_data['fullname'] = $arr['FullName'];
				$save_data['info_arr'] = serialize($arr);
				
				/*Now storing xml insted of qbxml obj due to serialize issue*/
				//$save_data['info_qbxml_obj'] = serialize($Customer);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				//$this->add_test_log(print_r($save_data,true));
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;
				//$this->add_test_log($wpdb->last_error);

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized Customer Successfully";
			$details = "Total Customer Recognized:{$tot_imported}";
			$status = 1;
			
			//Clear Invalid Mappings
			if (empty($idents['iteratorRemainingCount'])){
				$this->clear_customer_invalid_mappings();
			}			
		}else{
			$log_title = "Recognized Customer Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Customer Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'Customer','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*Product Import Request/Response*/
	public function Item_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		$attr_iteratorID = '';
		$attr_iterator = ' iterator="Start" ';
		if (empty($extra['iteratorID'])){
			$last = $this->_quickbooks_get_last_run($user, $action);
			$this->_quickbooks_set_last_run($user, $action);
			$this->_quickbooks_set_current_run($user, $action, $last);
		}else{
			$attr_iteratorID = ' iteratorID="' . $extra['iteratorID'] . '" ';
			$attr_iterator = ' iterator="Continue" ';

			$last = $this->_quickbooks_get_current_run($user, $action);
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<ItemQueryRq ' . $attr_iterator . ' ' . $attr_iteratorID . ' requestID="' . $requestID . '">
						<MaxReturned>' . QB_QUICKBOOKS_MAX_RETURNED . '</MaxReturned>
						<!--<FromModifiedDate>' . $last . '</FromModifiedDate>-->
						<OwnerID>0</OwnerID>
					</ItemQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;
	}

	public function Item_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		global $wpdb;

		if (!empty($idents['iteratorRemainingCount'])){
			$Queue = QuickBooks_WebConnector_Queue_Singleton::getInstance();
			$Queue->enqueue(QUICKBOOKS_IMPORT_ITEM, null, QB_PRIORITY_REFRESH_DATA_IMPORT, array( 'iteratorID' => $idents['iteratorID'] ),$this->get_qbun());
		}

		$iteratorID = (isset($idents['iteratorID']) && !empty($idents['iteratorID']))?$idents['iteratorID']:'';

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ItemQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_items';

			/*Delete Old Data*/
			if($this->get_session_val('import_product_iteratorID','',true)!=$iteratorID){
				$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
				$wpdb->query("TRUNCATE TABLE {$table}");
			}

			//
			$this->set_session_val('import_product_iteratorID',$iteratorID);

			foreach ($List->children() as $Item){
				$tot_imported++;
				$type = substr(substr($Item->name(), 0, -3), 4);
				$ret = $Item->name();
				
				$qb_pf = $this->get_option('mw_wc_qbo_desk_pull_prd_price_field');
				if(empty($qb_pf)){$qb_pf = 'Price';}
				$Price = 0;
				
				$custom_price_field = $this->get_qbd_ext_price_field();
				if(is_array($custom_price_field) && in_array($qb_pf,$custom_price_field)){
					$is_cs_pf_ok = false;
					foreach ($Item->children() as $Item_C){
						if($is_cs_pf_ok){break;}
						
						if($Item_C->name() == 'DataExtRet'){
							$ret_c = $Item_C->name();						
							$DataExtName = $Item_C->getChildDataAt($ret_c . ' DataExtName');
							//$Item_C->getChildDataAt($ret_c . ' OwnerID')
							if($DataExtName == $qb_pf){
								$is_cs_pf_ok = true;
								$Price = $Item_C->getChildDataAt($ret_c . ' DataExtValue');
							}
						}					
					}
					
					if(empty($Price)){
						$qb_pf = 'Price';
						$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
					}
				}else{
					$Price = $Item->getChildDataAt($ret . ' '.$qb_pf);
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesOrPurchase Price');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesAndPurchase SalesPrice');
				}
				
				if(empty($Price)){
					$Price = $Item->getChildDataAt($ret . ' SalesPrice');
				}
				
				$arr = array(
				'ListID' => $Item->getChildDataAt($ret . ' ListID'),
				'TimeCreated' => $Item->getChildDataAt($ret . ' TimeCreated'),
				'TimeModified' => $Item->getChildDataAt($ret . ' TimeModified'),
				'Name' => $Item->getChildDataAt($ret . ' Name'),
				'FullName' => $Item->getChildDataAt($ret . ' FullName'),
				'Type' => $type,
				'Parent_ListID' => $Item->getChildDataAt($ret . ' ParentRef ListID'),
				'Parent_FullName' => $Item->getChildDataAt($ret . ' ParentRef FullName'),
				'ManufacturerPartNumber' => $Item->getChildDataAt($ret . ' ManufacturerPartNumber'),
				'BarCodeValue' => $Item->getChildDataAt($ret . ' BarCodeValue'),
				'SalesTaxCode_ListID' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef ListID'),
				'SalesTaxCode_FullName' => $Item->getChildDataAt($ret . ' SalesTaxCodeRef FullName'),
				'BuildPoint' => $Item->getChildDataAt($ret . ' BuildPoint'),
				'ReorderPoint' => $Item->getChildDataAt($ret . ' ReorderPoint'),
				'QuantityOnHand' => $Item->getChildDataAt($ret . ' QuantityOnHand'),
				'AverageCost' => $Item->getChildDataAt($ret . ' AverageCost'),
				'QuantityOnOrder' => $Item->getChildDataAt($ret . ' QuantityOnOrder'),
				'QuantityOnSalesOrder' => $Item->getChildDataAt($ret . ' QuantityOnSalesOrder'),
				'TaxRate' => $Item->getChildDataAt($ret . ' TaxRate'),
				'Price_ori' => $Item->getChildDataAt($ret . ' Price'),
				'Price' => $Price,
				);

				$look_for = array(
				'SalesPrice' => array( 'SalesOrPurchase Price', 'SalesAndPurchase SalesPrice', 'SalesPrice' ),
				'SalesDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase SalesDesc', 'SalesDesc' ),
				'PurchaseCost' => array( 'SalesOrPurchase Price', 'SalesAndPurchase PurchaseCost', 'PurchaseCost' ),
				'PurchaseDesc' => array( 'SalesOrPurchase Desc', 'SalesAndPurchase PurchaseDesc', 'PurchaseDesc' ),
				'PrefVendor_ListID' => array( 'SalesAndPurchase PrefVendorRef ListID', 'PrefVendorRef ListID' ),
				'PrefVendor_FullName' => array( 'SalesAndPurchase PrefVendorRef FullName', 'PrefVendorRef FullName' ),
				);

				foreach ($look_for as $field => $look_here){
					if (!empty($arr[$field])){
						break;
					}

					foreach ($look_here as $look){
						$arr[$field] = $Item->getChildDataAt($ret . ' ' . $look);
					}
				}
				//QuickBooks_Utilities::log($this->dsn, 'Importing ' . $type . ' Item ' . $arr['FullName'] . ': ' . print_r($arr, true));
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$ip_name  = (!empty($arr['FullName']))?$arr['FullName']:$arr['Name'];
				$save_data['name'] = $ip_name; //Name
				$save_data['sku'] = $arr['Name']; // Blank value
				$save_data['product_type'] = $arr['Type'];
				$save_data['parent_id'] = $arr['Parent_ListID'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Item);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized Product Successfully";
			$details = "Total Product Recognized:{$tot_imported}";
			$status = 1;
			
			//Clear Invalid Mappings
			if (empty($idents['iteratorRemainingCount'])){
				$this->clear_product_invalid_mappings();
				$this->clear_variation_invalid_mappings();
			}
			
		}else{
			$log_title = "Recognized Product Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Product Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'Product','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}

	/*PaymentMethod Import Request/Response*/
	public function PaymentMethod_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<PaymentMethodQueryRq requestID="' . $requestID . '">
					</PaymentMethodQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function PaymentMethod_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);

		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/PaymentMethodQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_paymentmethod';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $PaymentMethod){
				$tot_imported++;
				$arr = array(
				'ListID' => $PaymentMethod->getChildDataAt('PaymentMethodRet ListID'),
				'Name' => $PaymentMethod->getChildDataAt('PaymentMethodRet Name'),
				'IsActive' => $PaymentMethod->getChildDataAt('PaymentMethodRet IsActive'),
				'PaymentMethodType' => $PaymentMethod->getChildDataAt('PaymentMethodRet PaymentMethodType'),
				);

				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['pm_type'] = $arr['PaymentMethodType'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($PaymentMethod);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized PaymentMethod Successfully";
			$details = "Total PaymentMethod Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized PaymentMethod Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No PaymentMethod Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'PaymentMethod','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}

	/*Account Import Request/Response*/
	public function Account_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<AccountQueryRq requestID="' . $requestID . '">
						<OwnerID>0</OwnerID>
					</AccountQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function Account_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);

		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/AccountQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_account';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $Account){
				$tot_imported++;
				$arr = array(
				'ListID' => $Account->getChildDataAt('AccountRet ListID'),
				'Name' => $Account->getChildDataAt('AccountRet Name'),
				'FullName' => $Account->getChildDataAt('AccountRet FullName'),
				'IsActive' => $Account->getChildDataAt('AccountRet IsActive'),
				'AccountType' => $Account->getChildDataAt('AccountRet AccountType'),
				'AccountNumber' => $Account->getChildDataAt('AccountRet AccountNumber'),
				'Desc' => $Account->getChildDataAt('AccountRet Desc'),
				'Balance' => $Account->getChildDataAt('AccountRet Balance'),
				'TotalBalance' => $Account->getChildDataAt('AccountRet TotalBalance'),
				'IsActive' => $Account->getChildDataAt('AccountRet IsActive'),
				);

				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['acc_type'] = $arr['AccountType'];
				$save_data['acc_num'] = $arr['AccountNumber'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Account);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;
			}
		}

		if($tot_imported>0){
			$log_title = "Recognized Account Successfully";
			$details = "Total Account Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized Account Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Account Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'Account','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}

	/*Class Import Request/Response*/
	public function Class_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<ClassQueryRq requestID="' . $requestID . '">
					</ClassQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function Class_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);

		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ClassQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_class';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $Class){
				$tot_imported++;
				$arr = array(
				'ListID' => $Class->getChildDataAt('ClassRet ListID'),
				'Name' => $Class->getChildDataAt('ClassRet Name'),
				'IsActive' => $Class->getChildDataAt('ClassRet IsActive'),
				'Sublevel' => $Class->getChildDataAt('ClassRet Sublevel'),

				'Parent_ListID' => $Class->getChildDataAt('ClassRet ParentRef ListID'),
				'Parent_FullName' => $Class->getChildDataAt('ClassRet ParentRef FullName'),

				);

				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Class);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized Class Successfully";
			$details = "Total Class Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized Class Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Class Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'Class','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}

	/*SalesTaxCode Import Request/Response*/
	public function SalesTaxCode_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<SalesTaxCodeQueryRq requestID="' . $requestID . '">
					</SalesTaxCodeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function SalesTaxCode_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);

		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/SalesTaxCodeQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $SalesTaxCode){
				$tot_imported++;
				$arr = array(
				'ListID' => $SalesTaxCode->getChildDataAt('SalesTaxCodeRet ListID'),
				'Name' => $SalesTaxCode->getChildDataAt('SalesTaxCodeRet Name'),
				'IsActive' => $SalesTaxCode->getChildDataAt('SalesTaxCodeRet IsActive'),
				'Desc' => $SalesTaxCode->getChildDataAt('SalesTaxCodeRet Desc'),
				'IsTaxable' => $SalesTaxCode->getChildDataAt('SalesTaxCodeRet IsTaxable'),
				);

				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['stc_desc'] = $arr['Desc'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;
				$save_data['is_taxable'] = ($arr['IsTaxable']=='true')?1:0;

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($SalesTaxCode);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized SalesTaxCode Successfully";
			$details = "Total SalesTaxCode Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized SalesTaxCode Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No SalesTaxCode Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";
			$status = 0;
		}
		$this->save_log(array('log_type'=>'SalesTaxCode','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}

	/*Terms Import Request/Response*/
	public function Terms_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<TermsQueryRq requestID="' . $requestID . '">
					</TermsQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function Terms_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/TermsQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_term';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $Term){
				$tot_imported++;
				$type = substr($Term->name(), 0, -8);
				$ret = $Term->name();

				$arr = array(
				'ListID' => $Term->getChildDataAt($ret.' ListID'),
				'Name' => $Term->getChildDataAt($ret.' Name'),
				'Type' => $type,
				'IsActive' => $Term->getChildDataAt($ret.' IsActive'),
				'Desc' => $Term->getChildDataAt($ret.' Desc'),
				'IsTaxable' => $Term->getChildDataAt($ret.' IsTaxable'),
				);

				if($type=='Standard'){
					$arr['StdDueDays'] = $Term->getChildDataAt($ret.' StdDueDays');
					$arr['StdDiscountDays'] = $Term->getChildDataAt($ret.' StdDiscountDays');
					$arr['DiscountPct'] = $Term->getChildDataAt($ret.' DiscountPct');
				}

				if($type=='DateDriven'){
					$arr['DayOfMonthDue'] = $Term->getChildDataAt($ret.' DayOfMonthDue');
					$arr['DueNextMonthDays'] = $Term->getChildDataAt($ret.' DueNextMonthDays');
					$arr['DiscountDayOfMonth'] = $Term->getChildDataAt($ret.' DiscountDayOfMonth');
					$arr['DiscountPct'] = $Term->getChildDataAt($ret.' DiscountPct');
				}

				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;
				$save_data['t_type'] = $arr['Type'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}

		if($tot_imported>0){
			$log_title = "Recognized Terms Successfully";
			$details = "Total Terms Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized Terms Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Terms Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'Terms','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*InventorySite Import Request/Response*/
	public function InventorySite_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<InventorySiteQueryRq requestID="' . $requestID . '">
					</InventorySiteQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function InventorySite_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/InventorySiteQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $InventorySite){
				$tot_imported++;				
				$ret = $InventorySite->name();

				$arr = array(
				'ListID' => $InventorySite->getChildDataAt($ret.' ListID'),
				'Name' => $InventorySite->getChildDataAt($ret.' Name'),
				
				'ParentSiteRefListID' => $InventorySite->getChildDataAt($ret.' ParentSiteRef ListID'),
				
				'IsActive' => $InventorySite->getChildDataAt($ret.' IsActive'),
				'IsDefaultSite' => $InventorySite->getChildDataAt($ret.' IsDefaultSite'),
				'SiteDesc' => $InventorySite->getChildDataAt($ret.' SiteDesc'),
				
				'Contact' => $InventorySite->getChildDataAt($ret.' Contact'),
				'Phone' => $InventorySite->getChildDataAt($ret.' Phone'),
				'Fax' => $InventorySite->getChildDataAt($ret.' Fax'),
				'Email' => $InventorySite->getChildDataAt($ret.' Email'),
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				//
				$save_data['parent_id'] = $arr['ParentSiteRefListID'];
				
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;
				$save_data['is_default'] = ($arr['IsDefaultSite']=='true')?1:0;
				$save_data['s_desc'] = $arr['SiteDesc'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized InventorySite Successfully";
			$details = "Total InventorySite Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized InventorySite Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No InventorySite Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'InventorySite','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*OtherName Import Request/Response*/
	public function OtherName_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<OtherNameQueryRq requestID="' . $requestID . '">
					</OtherNameQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function OtherName_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/OtherNameQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_othername';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $OtherName){
				$tot_imported++;				
				$ret = $OtherName->name();

				$arr = array(
				'ListID' => $OtherName->getChildDataAt($ret.' ListID'),
				'Name' => $OtherName->getChildDataAt($ret.' Name'),				
				'IsActive' => $OtherName->getChildDataAt($ret.' IsActive'),				
				'CompanyName' => $OtherName->getChildDataAt($ret.' CompanyName'),
				
				'Salutation' => $OtherName->getChildDataAt($ret.' Salutation'),
				'FirstName' => $OtherName->getChildDataAt($ret.' FirstName'),
				'MiddleName' => $OtherName->getChildDataAt($ret.' MiddleName'),
				'LastName' => $OtherName->getChildDataAt($ret.' LastName'),
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;				
				$save_data['companyname'] = $arr['CompanyName'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized OtherName Successfully";
			$details = "Total OtherName Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized OtherName Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No OtherName Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'OtherName','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*SalesRep Import Request/Response*/
	public function SalesRep_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<SalesRepQueryRq requestID="' . $requestID . '">
					</SalesRepQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function SalesRep_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;
		
		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/SalesRepQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $SalesRep){
				$tot_imported++;				
				$ret = $SalesRep->name();

				$arr = array(
				'ListID' => $SalesRep->getChildDataAt($ret.' ListID'),				
				'IsActive' => $SalesRep->getChildDataAt($ret.' IsActive'),				
				
				'SalesRepEntityRef_ListID' => $SalesRep->getChildDataAt($ret.' SalesRepEntityRef ListID'),
				'SalesRepEntityRef_FullName' => $SalesRep->getChildDataAt($ret.' SalesRepEntityRef FullName'),
				'Initial' => $SalesRep->getChildDataAt($ret.' Initial'),				
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;				
				$save_data['sr_e_ref_id'] = $arr['SalesRepEntityRef_ListID'];
				$save_data['sr_e_ref_name'] = $arr['SalesRepEntityRef_FullName'];

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				if(!empty($save_data['sr_e_ref_id'])){
					$wpdb->insert($table, $save_data);
				}
				
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized SalesRep Successfully";
			$details = "Total SalesRep Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized SalesRep Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No SalesRep Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'SalesRep','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	
	/*CustomerType Import Request/Response*/
	public function CustomerType_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<CustomerTypeQueryRq requestID="' . $requestID . '">
					</CustomerTypeQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function CustomerType_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CustomerTypeQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_customertype';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $CustomerType){
				$tot_imported++;
				$type = substr($CustomerType->name(), 0, -8);
				$ret = $CustomerType->name();

				$arr = array(
				'ListID' => $CustomerType->getChildDataAt($ret.' ListID'),
				'Name' => $CustomerType->getChildDataAt($ret.' Name'),				
				'IsActive' => $CustomerType->getChildDataAt($ret.' IsActive'),				
				'FullName' => $CustomerType->getChildDataAt($ret.' FullName'),
				
				'Sublevel' => $CustomerType->getChildDataAt($ret.' Sublevel'),				
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;				

				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized CustomerType Successfully";
			$details = "Total CustomerType Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized CustomerType Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No CustomerType Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'CustomerType','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*ShipMethod Import Request/Response*/
	public function ShipMethod_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<ShipMethodQueryRq requestID="' . $requestID . '">
					</ShipMethodQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function ShipMethod_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/ShipMethodQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_shipmethod';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $ShipMethod){
				$tot_imported++;
				$type = substr($ShipMethod->name(), 0, -8);
				$ret = $ShipMethod->name();

				$arr = array(
				'ListID' => $ShipMethod->getChildDataAt($ret.' ListID'),
				'Name' => $ShipMethod->getChildDataAt($ret.' Name'),				
				'IsActive' => $ShipMethod->getChildDataAt($ret.' IsActive'),
				);
				
				//$this->add_test_log(print_r($arr,true));

				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;				
				
				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized ShipMethod Successfully";
			$details = "Total ShipMethod Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized ShipMethod Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No ShipMethod Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'ShipMethod','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*Template Import Request/Response*/
	public function Template_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<TemplateQueryRq requestID="' . $requestID . '">
					</TemplateQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}
	
	public function Template_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}

		//$this->add_test_log($xml);
		//return;
		global $wpdb;

		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		$tot_imported = 0;
		if ($Doc = $Parser->parse($errnum, $errmsg)){

			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/TemplateQueryRs');

			$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_template';

			/*Delete Old Data*/
			$wpdb->query("DELETE FROM {$table} WHERE `id` > 0");
			$wpdb->query("TRUNCATE TABLE {$table}");

			foreach ($List->children() as $Template){
				$tot_imported++;
				$type = substr($Template->name(), 0, -8);
				$ret = $Template->name();

				$arr = array(
				'ListID' => $Template->getChildDataAt($ret.' ListID'),
				'Name' => $Template->getChildDataAt($ret.' Name'),
				'TemplateType' => $Template->getChildDataAt($ret.' TemplateType'),
				'IsActive' => $Template->getChildDataAt($ret.' IsActive'),
				);
				
				//$this->add_test_log(print_r($arr,true));
				
				$save_data = array();
				$save_data['qbd_id'] = $arr['ListID'];
				$save_data['name'] = $arr['Name'];
				$save_data['t_type'] = $arr['TemplateType'];
				$save_data['is_active'] = ($arr['IsActive']=='true')?1:0;				
				
				$save_data['info_arr'] = serialize($arr);
				//$save_data['info_qbxml_obj'] = serialize($Term);
				//$save_data['info_qbxml_obj'] = $xml;
				$save_data['info_qbxml_obj'] = '';
				$wpdb->insert($table, $save_data);
				//$lastid = $wpdb->insert_id;

			}
		}
		
		if($tot_imported>0){
			$log_title = "Recognized Template Successfully";
			$details = "Total Template Recognized:{$tot_imported}";
			$status = 1;
		}else{
			$log_title = "Recognized Template Error";
			$errmsg = (!empty($errmsg))?$errmsg:"No Template Found";
			$details = ($errnum)?"Error Number:{$errnum}\n"."Error:{$errmsg}":"Error:{$errmsg}";

			$status = 0;
		}
		$this->save_log(array('log_type'=>'Template','log_title'=>$log_title,'details'=>$details,'status'=>$status),true);

		return true;
	}
	
	/*Preferences Import Request/Response*/
	public function Preferences_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<PreferencesQueryRq requestID="' . $requestID . '">
					</PreferencesQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function Preferences_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);


		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/PreferencesQueryRs');
			foreach ($List->children() as $Preferences){
				$ret = $Preferences->name();
				$arr = array();
				$arr['IsMultiCurrencyOn'] = $Preferences->getChildDataAt($ret.' MultiCurrencyPreferences IsMultiCurrencyOn');
				$arr['PaySalesTax'] = $Preferences->getChildDataAt($ret.' SalesTaxPreferences PaySalesTax');
				$arr['added_date'] = $this->get_cdt();
				//$this->add_test_log(print_r($arr,true));
				update_option('mw_wc_qbo_desk_qbd_preferences_arr',$arr);
				update_option('mw_wc_qbo_desk_qbd_preferences_qbxml',$xml);
				$this->save_log(array('log_type'=>'Preferences','log_title'=>'Recognized Preferences','details'=>'Preferences Recognized Successfully','status'=>1),true);

			}
		}

	}
	
	/*Company Import Request/Response*/
	public function Company_Import_Request($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $version, $locale){
		if(!$this->is_qwc_connected()){
			return false;
		}

		// Build the request
		$xml = '<?xml version="1.0" encoding="utf-8"?>
			<?qbxml version="' . $version . '"?>
			<QBXML>
				<QBXMLMsgsRq onError="'.$this->getonError().'">
					<CompanyQueryRq requestID="' . $requestID . '">
					</CompanyQueryRq>
				</QBXMLMsgsRq>
			</QBXML>';

		//$this->add_test_log($xml);
		return $xml;

	}

	public function Company_Import_Response($requestID, $user, $action, $ID, $extra, &$err, $last_action_time, $last_actionident_time, $xml, $idents){
		if(!$this->is_qwc_connected()){
			return false;
		}
		//$this->add_test_log($xml);


		$errnum = 0;
		$errmsg = '';
		$Parser = new QuickBooks_XML_Parser($xml);

		if ($Doc = $Parser->parse($errnum, $errmsg)){
			$Root = $Doc->getRoot();
			$List = $Root->getChildAt('QBXML/QBXMLMsgsRs/CompanyQueryRs');
			foreach ($List->children() as $Company){
				$ret = $Company->name();
				$arr = array();
				$arr['CompanyName'] = $Company->getChildDataAt($ret.' CompanyName');
				$arr['Email'] = $Company->getChildDataAt($ret.' Email');
				$arr['Country'] = $Company->getChildDataAt($ret.' Address Country');
				$arr['added_date'] = $this->get_cdt();
				//$this->add_test_log(print_r($arr,true));
				update_option('mw_wc_qbo_desk_qbd_company_info_arr',$arr);
				update_option('mw_wc_qbo_desk_qbd_company_info_qbxml',$xml);
				$this->save_log(array('log_type'=>'Company','log_title'=>'Recognized Company','details'=>'Company info Recognized Successfully','status'=>1),true);
			}
		}

	}
	
	/*Import Functions End*/
	
	public function _quickbooks_get_last_run($user, $action){
		$type = null;
		$opts = null;
		return QuickBooks_Utilities::configRead($this->dsn, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $type, $opts);
	}

	public function _quickbooks_set_last_run($user, $action, $force = null){
		$value = date('Y-m-d') . 'T' . date('H:i:s');
		if ($force){
			$value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
		}
		return QuickBooks_Utilities::configWrite($this->dsn, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_LAST . '-' . $action, $value);
	}

	public function _quickbooks_get_current_run($user, $action){
		$type = null;
		$opts = null;
		return QuickBooks_Utilities::configRead($this->dsn, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $type, $opts);
	}

	public function _quickbooks_set_current_run($user, $action, $force = null){
		$value = date('Y-m-d') . 'T' . date('H:i:s');

		if ($force){
			$value = date('Y-m-d', strtotime($force)) . 'T' . date('H:i:s', strtotime($force));
		}

		return QuickBooks_Utilities::configWrite($this->dsn, $user, md5(__FILE__), QB_QUICKBOOKS_CONFIG_CURR . '-' . $action, $value);
	}
}