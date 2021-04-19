<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/public/partials
 */

#error_reporting(E_ALL | E_STRICT);

require_once plugin_dir_path( dirname(dirname( __FILE__ )) ) . 'includes/class-mw-qbo-desktop-qwc-server-lib.php';
$dsn = 'mysqli://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME;
$MWQDC_QWC = new MW_QBO_Desktop_Sync_Qwc_Server_Lib($dsn);

$user = $MWQDC_QWC->get_option('mw_qbo_dts_qwc_username');
$pass = $MWQDC_QWC->get_option('mw_qbo_dts_qwc_password');

$is_config_ok = false;
if($user!='' && $pass!=''){
	$pass = $MWQDC_QWC->decrypt($pass);
	$is_config_ok = true;
}

if($is_config_ok && $MWQDC_QWC->is_qwc_connected(true)){
	try {
		global $wpdb;
		// Map QuickBooks actions to handler functions
		$map = array(
			QUICKBOOKS_ADD_CUSTOMER => array( array( $MWQDC_QWC, 'AddCustomerRequest' ), array( $MWQDC_QWC, 'AddCustomerResponse' ) ),
			QUICKBOOKS_ADD_GUEST => array( array( $MWQDC_QWC, 'AddGuestRequest' ), array( $MWQDC_QWC, 'AddGuestResponse' ) ),
			
			'CustomerJobAdd' => array( array( $MWQDC_QWC, 'AddCustomerJobRequest' ), array( $MWQDC_QWC, 'AddCustomerJobResponse' ) ),
			
			QUICKBOOKS_ADD_INVOICE => array( array( $MWQDC_QWC, 'AddInvoiceRequest' ), array( $MWQDC_QWC, 'AddInvoiceResponse' ) ),
			QUICKBOOKS_ADD_SALESRECEIPT => array( array( $MWQDC_QWC, 'AddSalesReceiptRequest' ), array( $MWQDC_QWC, 'AddSalesReceiptResponse' ) ),
			
			//
			'SalesReceiptMod_GPI' => array( array( $MWQDC_QWC, 'UpdateSalesReceiptRequest_GPI' ), array( $MWQDC_QWC, 'UpdateSalesReceiptResponse_GPI' ) ),
			
			/**/
			'InvoiceDataExt' => array( array( $MWQDC_QWC, 'OrderDataExtAddRequest' ), array( $MWQDC_QWC, 'OrderDataExtAddResponse' ) ),
			'SalesReceiptDataExt' => array( array( $MWQDC_QWC, 'OrderDataExtAddRequest' ), array( $MWQDC_QWC, 'OrderDataExtAddResponse' ) ),
			'SalesOrderDataExt' => array( array( $MWQDC_QWC, 'OrderDataExtAddRequest' ), array( $MWQDC_QWC, 'OrderDataExtAddResponse' ) ),
			'EstimateDataExt' => array( array( $MWQDC_QWC, 'OrderDataExtAddRequest' ), array( $MWQDC_QWC, 'OrderDataExtAddResponse' ) ),
			
			QUICKBOOKS_ADD_SALESORDER => array( array( $MWQDC_QWC, 'AddSalesOrderRequest' ), array( $MWQDC_QWC, 'AddSalesOrderResponse' ) ),
			
			//
			QUICKBOOKS_ADD_ESTIMATE => array( array( $MWQDC_QWC, 'AddEstimateRequest' ), array( $MWQDC_QWC, 'AddEstimateResponse' ) ),
			
			//
			QUICKBOOKS_ADD_PURCHASEORDER => array( array( $MWQDC_QWC, 'AddPurchaseOrderRequest' ), array( $MWQDC_QWC, 'AddPurchaseOrderResponse' ) ),
			
			'ReceivePaymentAdd' => array( array( $MWQDC_QWC, 'AddPaymentRequest' ), array( $MWQDC_QWC, 'AddPaymentResponse' ) ),
			
			'OrderPaymentAdd' => array( array( $MWQDC_QWC, 'AddOrderPaymentRequest' ), array( $MWQDC_QWC, 'AddOrderPaymentResponse' ) ),
			
			/*Update*/
			'CustomerMod_Query' => array( array( $MWQDC_QWC, 'CustomerMod_Query_Request' ), array( $MWQDC_QWC, 'CustomerMod_Query_Response' ) ),
			QUICKBOOKS_MOD_CUSTOMER => array( array( $MWQDC_QWC, 'UpdateCustomerRequest' ), array( $MWQDC_QWC, 'UpdateCustomerResponse' ) ),
			
			//
			'CheckAdd' => array( array( $MWQDC_QWC, 'AddRefundRequest' ), array( $MWQDC_QWC, 'AddRefundResponse' ) ),
			
			'CreditMemoAdd' => array( array( $MWQDC_QWC, 'AddRefundRequest' ), array( $MWQDC_QWC, 'AddRefundResponse' ) ),
			
			QUICKBOOKS_ADD_INVENTORYITEM => array( array( $MWQDC_QWC, 'AddInventoryItemRequest' ), array( $MWQDC_QWC, 'AddInventoryItemResponse' ) ),
			QUICKBOOKS_ADD_NONINVENTORYITEM => array( array( $MWQDC_QWC, 'AddNonInventoryRequest' ), array( $MWQDC_QWC, 'AddNonInventoryResponse' ) ),
			QUICKBOOKS_ADD_SERVICEITEM => array( array( $MWQDC_QWC, 'AddServiceItemRequest' ), array( $MWQDC_QWC, 'AddServiceItemResponse' ) ),
			
			'V_'.QUICKBOOKS_ADD_INVENTORYITEM => array( array( $MWQDC_QWC, 'AddInventoryItemRequest' ), array( $MWQDC_QWC, 'AddInventoryItemResponse' ) ),
			'V_'.QUICKBOOKS_ADD_NONINVENTORYITEM => array( array( $MWQDC_QWC, 'AddNonInventoryRequest' ), array( $MWQDC_QWC, 'AddNonInventoryResponse' ) ),
			'V_'.QUICKBOOKS_ADD_SERVICEITEM => array( array( $MWQDC_QWC, 'AddServiceItemRequest' ), array( $MWQDC_QWC, 'AddServiceItemResponse' ) ),
			
			'InventoryAdjustmentAdd' => array( array( $MWQDC_QWC, 'InventoryAdjustmentAddRequest' ), array( $MWQDC_QWC, 'InventoryAdjustmentAddResponse' ) ),
			'V_InventoryAdjustmentAdd' => array( array( $MWQDC_QWC, 'InventoryAdjustmentAddRequest' ), array( $MWQDC_QWC, 'InventoryAdjustmentAddResponse' ) ),
		);
		
		$errmap = array(
		'*' => array( $MWQDC_QWC, 'Qwc_Catch_All_Errors' ),
		);
		
		$hooks = array(
			QuickBooks_WebConnector_Handlers::HOOK_LOGINSUCCESS => array( array( $MWQDC_QWC, 'Hook_Login_Success' ) ),
		);
		
		/*Import*/
		$qbd_import = false;$extra_import = false;$a_allow = true;
		if($a_allow || $MWQDC_QWC->option_checked('mw_wc_qbo_desk_cp_refresh_data_enable')){
			$qbd_import = true;
			$extra_import = true;
		}
		
		/**/
		$map['CustomerImport_ByName'] = array( array( $MWQDC_QWC, 'Customer_Import_ByName_Request'), array( $MWQDC_QWC, 'Customer_Import_ByName_Response') );
		$map['GuestImport_ByName'] = array( array( $MWQDC_QWC, 'Customer_Import_ByName_Request'), array( $MWQDC_QWC, 'Customer_Import_ByName_Response') );
		
		if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_oth_refresh_data_enable')){
			//$extra_import = true;
		}
		
		if($qbd_import || $extra_import || $MWQDC_QWC->option_checked('mw_wc_qbo_desk_rt_pull_enable') || $MWQDC_QWC->is_debug_queue() || $MWQDC_QWC->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
			define('QB_QUICKBOOKS_CONFIG_LAST', 'last');
			define('QB_QUICKBOOKS_CONFIG_CURR', 'curr');
			
			$mw_qbo_dts_qwc_qb_max_returned = (int) $MWQDC_QWC->get_option('mw_qbo_dts_qwc_qb_max_returned');
			if($mw_qbo_dts_qwc_qb_max_returned < 1){
				$mw_qbo_dts_qwc_qb_max_returned = 100;
			}
			define('QB_QUICKBOOKS_MAX_RETURNED', $mw_qbo_dts_qwc_qb_max_returned);
			
			//New
			$last_post_id = (int) $wpdb->get_var("SELECT ID FROM {$wpdb->posts} ORDER BY ID DESC LIMIT 0,1");
			$rf_imp_pr = $last_post_id+1000;
			define('QB_PRIORITY_REFRESH_DATA_IMPORT', $rf_imp_pr);
		}
		
		if($MWQDC_QWC->get_session_val('mw_wc_qbo_desk_cp_refresh_data_enable',false,true)){
			//$qbd_import = true;
		}
		
		if($qbd_import){			
			define('QB_PRIORITY_CUSTOMER', 2);
			define('QB_PRIORITY_ITEM', 1);
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('customer')){
				$map[QUICKBOOKS_IMPORT_CUSTOMER] = array( array( $MWQDC_QWC, 'Customer_Import_Request'), array( $MWQDC_QWC, 'Customer_Import_Response') );
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('product')){
				$map[QUICKBOOKS_IMPORT_ITEM] = array( array( $MWQDC_QWC, 'Item_Import_Request'), array( $MWQDC_QWC, 'Item_Import_Response') );
			}					
			
		}
		
		if($MWQDC_QWC->get_session_val('mw_wc_qbo_desk_oth_refresh_data_enable',false,true)){
			//$extra_import = true;
		}
		
		if($extra_import){
			define('QB_PRIORITY_EXTRA', 0);
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('payment_method',true)){
				$map[QUICKBOOKS_IMPORT_PAYMENTMETHOD] = array( array( $MWQDC_QWC, 'PaymentMethod_Import_Request'), array( $MWQDC_QWC, 'PaymentMethod_Import_Response') );
			}			
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('account',true)){
				$map[QUICKBOOKS_IMPORT_ACCOUNT] = array( array( $MWQDC_QWC, 'Account_Import_Request'), array( $MWQDC_QWC, 'Account_Import_Response') );
			}			
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('class',true)){
				$map[QUICKBOOKS_IMPORT_CLASS] = array( array( $MWQDC_QWC, 'Class_Import_Request'), array( $MWQDC_QWC, 'Class_Import_Response') );
			}			
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('sales_tax_code',true)){
				$map[QUICKBOOKS_IMPORT_SALESTAXCODE] = array( array( $MWQDC_QWC, 'SalesTaxCode_Import_Request'), array( $MWQDC_QWC, 'SalesTaxCode_Import_Response') );
			}			
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('term',true)){
				$map[QUICKBOOKS_IMPORT_TERMS] = array( array( $MWQDC_QWC, 'Terms_Import_Request'), array( $MWQDC_QWC, 'Terms_Import_Response') );
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('preferences',true)){
				$map[QUICKBOOKS_IMPORT_PREFERENCES] = array( array( $MWQDC_QWC, 'Preferences_Import_Request'), array( $MWQDC_QWC, 'Preferences_Import_Response') );
				
				$map[QUICKBOOKS_IMPORT_COMPANY] = array( array( $MWQDC_QWC, 'Company_Import_Request'), array( $MWQDC_QWC, 'Company_Import_Response') );
				
				$map[QUICKBOOKS_IMPORT_CURRENCY] = array( array( $MWQDC_QWC, 'Currency_Import_Request'), array( $MWQDC_QWC, 'Currency_Import_Response') );
			}
			
			//
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('InventorySite',true)){
				$map[QUICKBOOKS_IMPORT_INVENTORYSITE] = array( array( $MWQDC_QWC, 'InventorySite_Import_Request'), array( $MWQDC_QWC, 'InventorySite_Import_Response') );				
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('OtherName',true)){
				//$map[QUICKBOOKS_IMPORT_OTHERNAME] = array( array( $MWQDC_QWC, 'OtherName_Import_Request'), array( $MWQDC_QWC, 'OtherName_Import_Response') );
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('SalesRep',true)){
				$map[QUICKBOOKS_IMPORT_SALESREP] = array( array( $MWQDC_QWC, 'SalesRep_Import_Request'), array( $MWQDC_QWC, 'SalesRep_Import_Response') );
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('CustomerType',true)){
				$map[QUICKBOOKS_IMPORT_CUSTOMERTYPE] = array( array( $MWQDC_QWC, 'CustomerType_Import_Request'), array( $MWQDC_QWC, 'CustomerType_Import_Response') );
			}
			
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('ShipMethod',true)){
				$map[QUICKBOOKS_IMPORT_SHIPMETHOD] = array( array( $MWQDC_QWC, 'ShipMethod_Import_Request'), array( $MWQDC_QWC, 'ShipMethod_Import_Response') );
			}
			
			//
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('Template',true)){
				$map[QUICKBOOKS_IMPORT_TEMPLATE] = array( array( $MWQDC_QWC, 'Template_Import_Request'), array( $MWQDC_QWC, 'Template_Import_Response') );
			}
			
			//
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('Vendor',true)){
				$map[QUICKBOOKS_IMPORT_VENDOR] = array( array( $MWQDC_QWC, 'Vendor_Import_Request'), array( $MWQDC_QWC, 'Vendor_Import_Response') );
			}
			
			//
			if($MWQDC_QWC->check_refresh_data_enabled_by_item('PriceLevel',true)){
				// && $MWQDC_QWC->is_plugin_active('myworks-quickbooks-desktop-role-based-price-qb-price-level-compt')
				$map[QUICKBOOKS_IMPORT_PRICELEVEL] = array( array( $MWQDC_QWC, 'PriceLevel_Import_Request'), array( $MWQDC_QWC, 'PriceLevel_Import_Response') );
			}
			
		}
		
		/*Pull*/
		
		/*Realtime*/
		if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_rt_pull_enable')){
			if($MWQDC_QWC->check_if_real_time_pull_enable_for_item('inventory')){
				//Auto_Pull_Update_Wc_Inventory_Request , Auto_Pull_Update_Wc_Inventory_Response
				//Auto_Wc_Inventory_AdjustmentQuery_Request , Auto_Wc_Inventory_AdjustmentQuery_Response
				
				if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_rt_all_invnt_pull')){
					$wmior_active =$MWQDC_QWC->is_plugin_active('myworks-warehouse-routing','mw_warehouse_routing');
					$mw_wc_qbo_desk_compt_wmior_lis_mv = get_option('mw_wc_qbo_desk_compt_wmior_lis_mv');					
					
					if(($wmior_active && $MWQDC_QWC->option_checked('mw_wc_qbo_desk_w_miors_ed') && is_array($mw_wc_qbo_desk_compt_wmior_lis_mv)) || (!$wmior_active && $MWQDC_QWC->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync') && $MWQDC_QWC->get_option('mw_wc_qbo_desk_compt_qbd_invt_sites_for_invnt_pull')!='')){
						$map['ALL_PULL_INVT_ISQ'] = array( array( $MWQDC_QWC, 'All_Pull_ItemSitesQuery_Request'), array( $MWQDC_QWC, 'All_Pull_ItemSitesQuery_Response') );
					}else{
						$map['ALL_WC_UPDATE_INVENTORY'] = array( array( $MWQDC_QWC, 'All_Pull_Update_Wc_Inventory_Request'), array( $MWQDC_QWC, 'All_Pull_Update_Wc_Inventory_Response') );
					
						$map['ALL_WC_UPDATE_INVENTORY_A'] = array( array( $MWQDC_QWC, 'All_Pull_Update_Wc_Inventory_A_Request'), array( $MWQDC_QWC, 'All_Pull_Update_Wc_Inventory_A_Response') );
					}					
				}else{
					/*
					$map['AUTO_WC_UPDATE_INVENTORY'] = array( array( $MWQDC_QWC, 'Auto_Wc_Inventory_AdjustmentQuery_Request'), array( $MWQDC_QWC, 'Auto_Wc_Inventory_AdjustmentQuery_Response') );
					*/
				}
			}
			
			if($MWQDC_QWC->check_if_real_time_pull_enable_for_item('pricing')){
				$map['ALL_WC_UPDATE_PRICING'] = array( array( $MWQDC_QWC, 'All_Pull_Update_Wc_Pricing_Request'), array( $MWQDC_QWC, 'All_Pull_Update_Wc_Pricing_Response') );
			}
			
			//
			if($MWQDC_QWC->check_sh_woorbp_qbpricelevel_hash()){
				$map['All_PriceLevel_Inventory'] = array( array( $MWQDC_QWC, 'All_PriceLevel_Inventory_Request'), array( $MWQDC_QWC, 'All_PriceLevel_Inventory_Response') );
			}			
		}
		
		/**/
		if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_compt_np_fitbodywrap_cust_inv_oc')){
			$map['InvoiceQuery_FBW'] = array( array( $MWQDC_QWC, 'InvoiceQuery_FBW_Request'), array( $MWQDC_QWC, 'InvoiceQuery_FBW_Response') );
		}
		
		/**/
		if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_auto_refresh_new_cust_prod')){
			$map['New_'.QUICKBOOKS_IMPORT_CUSTOMER] = array( array( $MWQDC_QWC, 'Customer_Import_Request'), array( $MWQDC_QWC, 'Customer_Import_Response') );
			
			$map['New_'.QUICKBOOKS_IMPORT_ITEM] = array( array( $MWQDC_QWC, 'Item_Import_Request'), array( $MWQDC_QWC, 'Item_Import_Response') );
		}
		
		/*Manual*/
		$forced_pull_enable = true;
		if($MWQDC_QWC->option_checked('mw_wc_qbo_desk_pull_enable') || $forced_pull_enable){
			$map['WC_UPDATE_INVENTORY'] = array( array( $MWQDC_QWC, 'Pull_Update_Wc_Inventory_Request'), array( $MWQDC_QWC, 'Pull_Update_Wc_Inventory_Response') );
			
			$map['WC_UPDATE_PRODUCT_PRICE'] = array( array( $MWQDC_QWC, 'Pull_Update_Wc_Product_Price_Request'), array( $MWQDC_QWC, 'Pull_Update_Wc_Product_Price_Response') );
			
			//
			if($MWQDC_QWC->check_sh_woorbp_qbpricelevel_hash()){
				$map['PriceLevel_Inventory'] = array( array( $MWQDC_QWC, 'PriceLevel_Inventory_Request'), array( $MWQDC_QWC, 'PriceLevel_Inventory_Response') );
			}
			
			//
			$map['UPDATE_INVENTORY_A_MAX'] = array( array( $MWQDC_QWC, 'Max_Inventory_Assembly_Request'), array( $MWQDC_QWC, 'Max_Inventory_Assembly_Response') );
		}
		
		//Debug
		$map['DEBUG_QUEUE'] = array( array( $MWQDC_QWC, 'Debug_Queue_Request'), array( $MWQDC_QWC, 'Debug_Queue_response') );
		
		/*SOAP*/
		//$log_level = QUICKBOOKS_LOG_DEVELOP;
		$log_level = QUICKBOOKS_LOG_NORMAL;
		
		$soapserver = QUICKBOOKS_SOAPSERVER_BUILTIN;	
		$soap_options = array();
		
		$handler_options = array(
		'deny_concurrent_logins' => false, 
		'deny_reallyfast_logins' => false, 
		);
		
		$driver_options = array();	
		$callback_options = array();		
		
		/*
		if($MWQDC_QWC->check_invalid_chars_in_db_conn_info()){
			throw new Exception("Database info has invalid chars - not supportrd by our plugin");			
		}		
		
		if (!QuickBooks_Utilities::initialized($dsn)){
			QuickBooks_Utilities::initialize($dsn);		
		}
		
		QuickBooks_Utilities::createUser($dsn, $user, $pass);
		*/
		if(!isset($_GET['debug'])){
			ob_clean();
		}		
		$Server = new QuickBooks_WebConnector_Server($dsn, $map, $errmap, $hooks, $log_level, $soapserver, QUICKBOOKS_WSDL, $soap_options, $handler_options, $driver_options, $callback_options);
		$response = $Server->handle(true, true);
	}catch(Exception $e) {
	  echo 'Error: ' .$e->getMessage();
	}
}