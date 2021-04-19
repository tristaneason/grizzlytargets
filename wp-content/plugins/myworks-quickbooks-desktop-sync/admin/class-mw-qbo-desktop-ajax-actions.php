<?php
if ( ! defined( 'ABSPATH' ) )
exit;

function myworks_wc_qbo_sync_check_license_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_check_license_desk', 'check_plugin_license_desk' ) ) {
		// process form data
		global $MWQDC_LB;
		
		$mw_wc_qbo_sync_localkey = get_option('mw_wc_qbo_desk_localkey','');
		$mw_wc_qbo_sync_localkey = $MWQDC_LB->sanitize($mw_wc_qbo_sync_localkey);
		
		$mw_wc_qbo_sync_license =  $MWQDC_LB->var_p('mw_wc_qbo_sync_license_desk');
		$mw_wc_qbo_sync_license = $MWQDC_LB->sanitize($mw_wc_qbo_sync_license);
		
		if($mw_wc_qbo_sync_license!=$MWQDC_LB->get_option('mw_wc_qbo_desk_license')){
			$MWQDC_LB->set_session_val('new_license_check',1);
		}		
		
		if($MWQDC_LB->is_valid_license($mw_wc_qbo_sync_license,$mw_wc_qbo_sync_localkey,true)){
			echo 'License Activated';
		}else{
			echo 'Invalid License key';
		}		
	}
	wp_die();
}

function mw_wc_qbo_sync_refresh_log_chart_desk(){
	global $MWQDC_LB;
	$vp = $MWQDC_LB->var_p('period');
	$vp  = $MWQDC_LB->sanitize($vp);
	$MWQDC_LB->set_session_val('dashboard_graph_period',$vp);
	echo $MWQDC_LB->get_log_chart_output($vp);
	wp_die();
}

function mw_wc_qbo_sync_window_desk(){
	global $MWQDC_LB;
	global $MWQDC_AD;
	global $wpdb;
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_window_desk', 'window_qbo_sync_desk' ) ) {
		global $wpdb;
		$sync_type = $MWQDC_LB->var_p('sync_type');
		$item_type = $MWQDC_LB->var_p('item_type');
		$id = (int) $MWQDC_LB->var_p('id');
		$cur_item = (int) $MWQDC_LB->var_p('cur_item');
		$tot_item = (int ) $MWQDC_LB->var_p('tot_item');
		
		//
		$from_wc_order_page = false;
		if(isset($_POST['ext_options'])){
			$ext_options = @json_decode(stripslashes($_POST['ext_options']));
			if(is_object($ext_options) && !empty($ext_options)){
				if(isset($ext_options->from_wc_order_page) && $ext_options->from_wc_order_page){
					$from_wc_order_page = $ext_options->from_wc_order_page;
				}
			}
		}		
		
		$check_sync_valid = true;
		if($sync_type!='push' && $sync_type!='pull'){
			$check_sync_valid = false;
		}
		if($item_type!='customer' && $item_type!='invoice' && $item_type!='product' && $item_type!='payment' && $item_type!='inventory' && $item_type!='v_inventory' && $item_type!='variation' && $item_type!='productprice' && $item_type!='refund'){
			// && $item_type!='category'
			$check_sync_valid = false;
		}
		if(!$id || !$cur_item || !$tot_item){
			$check_sync_valid = false;
		}
		
		if($check_sync_valid){
			try{
				$key =  $cur_item;		  
				$per = $key/$tot_item*100;
				$per = ceil($per);			
				$msg = '';
				
				//Push
				if($sync_type=='push'){
					if($item_type=='customer'){
						$return = $MWQDC_AD->hook_user_register(array('user_id'=>$id));
						//$manual_push_update = $MWQDC_LB->get_session_val('sync_window_push_manual_update',false,true);						
						//$manual_push_session_err_msg = $MWQDC_LB->get_session_msg('manual_push_session_err_msg','error');
						
						if($return){
							$msg = "<span style='color:green;'>Customer #$id has been added into queue</span>";							
						}else{
							$msg = "<span style='color:red;'>There was an error adding customer into queue #$id</span>";							
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
					}
					
					if($item_type=='invoice'){
						$return = $MWQDC_AD->hook_order_add(array('order_id'=>$id));
						
						//$manual_push_update = $MWQDC_LB->get_session_val('sync_window_push_manual_update',false,true);						
						//$manual_push_session_err_msg = $MWQDC_LB->get_session_msg('manual_push_session_err_msg','error');
						
						if($return){
							$msg = "<span style='color:green;'>Order #$id has been added into queue</span>";
							
							/**/
							$order_id = $id;
							$order = get_post($order_id);
							$invoice_data = $MWQDC_LB->get_wc_order_details_from_order($order_id,$order);
							
							$pr_pg_ost = '';
							$wc_user_role = '';
							$wc_cus_id = (int) get_post_meta($order_id,'_customer_user',true);
							if($wc_cus_id>0){
								$user_info = get_userdata($wc_cus_id);
								if(isset($user_info->roles) && is_array($user_info->roles)){
									$wc_user_role = $user_info->roles[0];
								}
								
							}else{
								$wc_user_role = 'wc_guest_user';
							}
							
							$qost_arr = array(
								'Invoice' => 'Invoice',
								'SalesReceipt' => 'SalesReceipt',
								'SalesOrder' => 'SalesOrder',
								'Estimate' => 'Estimate'										
							);
							
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
							
							if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_qbd_sync_as') == 'Per Gateway'){
								$_payment_method = $MWQDC_LB->get_array_isset($invoice_data,'_payment_method','',true);
								$_order_currency = $MWQDC_LB->get_array_isset($invoice_data,'_order_currency','',true);						
								if(!empty($_payment_method) && !empty($_order_currency)){
									$base_currency = '';
									if($MWQDC_LB->wacs_base_cur_enabled()){
										$base_currency = get_woocommerce_currency();
										$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$base_currency);
									}else{
										$pm_map_data = $MWQDC_LB->get_mapped_payment_method_data($_payment_method,$_order_currency);
									}
									
									$order_sync_as = $MWQDC_LB->get_array_isset($pm_map_data,'order_sync_as','',true);
									if(!empty($order_sync_as) && isset($qost_arr[$order_sync_as])){
										$pr_pg_ost = $order_sync_as;
									}
								}
							}							
							
							/**/							
							if($from_wc_order_page && !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt') && !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_estimate') && $pr_pg_ost!='SalesReceipt' && $pr_pg_ost!='Estimate'){
								$pm_r = $MWQDC_LB->get_row("SELECT `meta_id` FROM `{$wpdb->postmeta}` WHERE `post_id` = '{$id}' AND `meta_key` = '_transaction_id' ");
								if(is_array($pm_r) && !empty($pm_r)){
									$payment_id = (int) $pm_r['meta_id'];
									if($payment_id > 0){
										$return_ext = $MWQDC_AD->hook_payment_add(array('payment_id'=>$payment_id,'opp_a_ord_push'=>true));
										if($return_ext){
											$msg.= PHP_EOL ."<span style='color:green;'>Payment #$payment_id has been added into queue</span>";
										}else{
											//$msg.= PHP_EOL ."<span style='color:red;'>There was an error adding payment into queue #$payment_id</span>";
										}
									}
								}
							}
							
						}else{
							$msg = "<span style='color:red;'>There was an error adding order into queue #$id</span>";							
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
					}
					
					if($item_type=='product'){						
						$return = $MWQDC_AD->hook_product_add(array('product_id'=>$id));
						//$manual_push_update = $MWQDC_LB->get_session_val('sync_window_push_manual_update',false,true);						
						//$manual_push_session_err_msg = $MWQDC_LB->get_session_msg('manual_push_session_err_msg','error');
						
						if($return){
							$msg = "<span style='color:green;'>Product #$id has been added into queue</span>";							
						}else{
							$msg = "<span style='color:red;'>There was an error adding product into queue #$id</span>";							
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
					}
					
					if($item_type=='variation'){					
						$return = $MWQDC_AD->hook_variation_add(array('variation_id'=>$id));
						//$manual_push_update = $MWQDC_LB->get_session_val('sync_window_push_manual_update',false,true);						
						//$manual_push_session_err_msg = $MWQDC_LB->get_session_msg('manual_push_session_err_msg','error');
						
						if($return){
							$msg = "<span style='color:green;'>Variation #$id has been added into queue</span>";							
						}else{
							$msg = "<span style='color:red;'>There was an error adding variation into queue #$id</span>";							
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
					}
					
					if($item_type=='payment' && !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_sales_receipt') && !$MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_estimate')){
						$return = $MWQDC_AD->hook_payment_add(array('payment_id'=>$id));						
						if($return){
							$msg = "<span style='color:green;'>Payment #$id has been added into queue</span>";
						}else{
							$msg = "<span style='color:red;'>There was an error adding payment into queue #$id</span>";
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);				
					}
					
					if($item_type=='inventory'){
						$return = $MWQDC_AD->hook_product_stock_update(array('product_id'=>$id));						
						if($return){
							$msg = "<span style='color:green;'>Inventory #$id has been added into queue</span>";
						}else{
							$msg = "<span style='color:red;'>There was an error adding inventory into queue #$id</span>";
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);				
					}
					
					if($item_type=='v_inventory'){
						$return = $MWQDC_AD->hook_variation_stock_update(array('variation_id'=>$id));						
						if($return){
							$msg = "<span style='color:green;'>Variation Inventory #$id has been added into queue</span>";
						}else{
							$msg = "<span style='color:red;'>There was an error adding variation inventory into queue #$id</span>";
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);				
					}
					
					if($item_type=='refund'){
						$order_id = 0; 
						$rf_data = $wpdb->get_row("SELECT `post_parent` FROM `{$wpdb->posts}` WHERE `post_type` = 'shop_order_refund' AND `ID` = {$id} ");
						if(is_object($rf_data) && count($rf_data)){
							$order_id = $rf_data->post_parent;
						}
						
						$return = $MWQDC_AD->hook_refund_add(array('order_id'=>$order_id),$id);
						if($return){
							$msg = "<span style='color:green;'>Refund #$id has been added into queue</span>";							
						}else{
							$msg = "<span style='color:red;'>There was an error adding refund into queue #$id</span>";							
						}
						$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
					}
				}
				
				/*Pull*/
				
				if($sync_type=='pull'){
					if($item_type=='inventory'){						
						$qbd_id = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','id',$id);						
						if($qbd_id!=''){
							$return = $MWQDC_LB->Add_Pull_Inventory_Queue($qbd_id);
							if($return){
								$msg = "<span style='color:green;'>QBD Inventory #$qbd_id has been added into queue</span>";
							}else{
								$msg = "<span style='color:red;'>There was an error adding qbd inventory into queue #$qbd_id</span>";
							}							
							$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
						}
					}
					
					if($item_type=='productprice'){						
						$qbd_id = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','id',$id);						
						if($qbd_id!=''){
							$return = $MWQDC_LB->Add_Pull_ProductPrice_Queue($qbd_id);
							if($return){
								$msg = "<span style='color:green;'>QBD Product Price #$qbd_id has been added into queue</span>";
							}else{
								$msg = "<span style='color:red;'>There was an error adding qbd product price into queue #$qbd_id</span>";
							}							
							$MWQDC_LB->show_sync_window_message($key, $msg , $per, $tot_item);
						}
					}
				}
				
			}catch (Exception $e) {
				$Exception = $e->getMessage();
			}
		}		
	}
	wp_die();
}

function mw_wc_qbo_sync_clear_all_mappings_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_mappings_desk', 'clear_all_mappings_desk' ) ) {	
		global $wpdb;
		
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` ");
		
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` ");
		
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_paymentmethod` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_paymentmethod` ");
		
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_tax` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_map_tax` ");
		
		if(isset($_POST['payment_map_delete'])){
			
		}
		
		echo 'Success';
	}
	wp_die();
}

function mw_wc_qbo_sync_clear_all_mappings_products_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_mappings_products_desk', 'clear_all_mappings_products_desk' ) ) {	
		global $wpdb;
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_product_pairs` ");
		echo 'Success';
	}
	wp_die();
}

function mw_wc_qbo_sync_clear_all_mappings_customers_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_mappings_customers_desk', 'clear_all_mappings_customers_desk' ) ) {	
		global $wpdb;
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_customers_pairs` ");
		echo 'Success';
	}
	wp_die();
}

function mw_wc_qbo_sync_clear_all_mappings_variations_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_mappings_variations_desk', 'clear_all_mappings_variations_desk' ) ) {	
		global $wpdb;
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_variation_pairs` ");
		echo 'Success';
	}
	wp_die();
}

function mw_wc_qbo_sync_automap_customers_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_customers_desk', 'automap_customers_desk' ) ) {
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapCustomer();
		//echo 'Success';
		echo 'Total Customer Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_customers_desk_wf_qf(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_customers_desk_wf_qf', 'automap_customers_desk_wf_qf' ) ) {
		global $MWQDC_LB;
		
		$cam_wf = (isset($_POST['cam_wf']))?trim($_POST['cam_wf']):'';
		$cam_qf = (isset($_POST['cam_qf']))?trim($_POST['cam_qf']):'';
		
		$mo_um = false;
		if(isset($_POST['mo_um']) && $_POST['mo_um'] == 'true'){
			$mo_um = true;
		}
		
		$map_count = (int) $MWQDC_LB->AutoMapCustomerWfQf($cam_wf,$cam_qf,$mo_um);
		//echo 'Success';
		echo 'Total Customer Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_customers_by_name_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_customers_by_name_desk', 'automap_customers_by_name_desk' ) ) {
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapCustomerByName();
		//echo 'Success';
		echo 'Total Customer Mapped: '.$map_count;
	}	
	wp_die();
}


function mw_wc_qbo_sync_automap_products_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_products_desk', 'automap_products_desk' ) ) {
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapProduct();
		//echo 'Success';
		echo 'Total Product Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_products_desk_wf_qf(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_products_desk_wf_qf', 'automap_products_desk_wf_qf' ) ) {
		global $MWQDC_LB;
		
		$pam_wf = (isset($_POST['pam_wf']))?trim($_POST['pam_wf']):'';
		$pam_qf = (isset($_POST['pam_qf']))?trim($_POST['pam_qf']):'';
		
		$mo_um = false;
		if(isset($_POST['mo_um']) && $_POST['mo_um'] == 'true'){
			$mo_um = true;
		}
		
		$map_count = (int) $MWQDC_LB->AutoMapProductWfQf($pam_wf,$pam_qf,$mo_um);
		//echo 'Success';
		echo 'Total Product Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_products_by_name_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_products_by_name_desk', 'automap_products_by_name_desk' ) ) {		
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapProductByName();
		//echo 'Success';
		echo 'Total Product Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_variations_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_variations_desk', 'automap_variations_desk' ) ) {
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapVariation();
		//echo 'Success';
		echo 'Total Variation Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_variations_desk_wf_qf(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_variations_desk_wf_qf', 'automap_variations_desk_wf_qf' ) ) {
		global $MWQDC_LB;
		
		$vam_wf = (isset($_POST['vam_wf']))?trim($_POST['vam_wf']):'';
		$vam_qf = (isset($_POST['vam_qf']))?trim($_POST['vam_qf']):'';
		
		$mo_um = false;
		if(isset($_POST['mo_um']) && $_POST['mo_um'] == 'true'){
			$mo_um = true;
		}
		
		$map_count = (int) $MWQDC_LB->AutoMapVariationWfQf($vam_wf,$vam_qf,$mo_um);
		//echo 'Success';
		echo 'Total Variation Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_automap_variations_by_name_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_automap_variations_by_name_desk', 'automap_variations_by_name_desk' ) ) {		
		global $MWQDC_LB;
		$map_count = (int) $MWQDC_LB->AutoMapVariationByName();
		//echo 'Success';
		echo 'Total Variation Mapped: '.$map_count;
	}	
	wp_die();
}

function mw_wc_qbo_sync_clear_all_logs_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_logs_desk', 'mwqs_clear_all_logs_desk' ) ) {
		global $wpdb;	
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` ");
		echo 'Success';
	}	
	wp_die();
}

function mw_wc_qbo_sync_clear_all_log_errors_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_log_errors_desk', 'mwqs_clear_all_log_errors_desk' ) ) {
		global $wpdb;
		$wpdb->query("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE `status` = 0 ");
		echo 'Success';
	}	
	wp_die();
}

function mw_wc_qbo_sync_clear_all_queue_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_queue_desk', 'mwqs_clear_all_queue_desk' ) ) {
		global $wpdb;	
		$wpdb->query("DELETE FROM `quickbooks_queue` WHERE `quickbooks_queue_id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `quickbooks_queue` ");
		echo 'Success';
	}	
	wp_die();
}

function mw_wc_qbo_sync_clear_all_queue_pending_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_clear_all_queue_pending_desk', 'mwqs_clear_all_queue_pending_desk' ) ) {
		global $wpdb;
		$wpdb->query("DELETE FROM `quickbooks_queue` WHERE `quickbooks_queue_id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `quickbooks_queue` ");
		echo 'Success';
	}	
	wp_die();
}

function mw_wc_qbo_sync_trial_license_check_again_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_trial_license_check_again_desk', 'trial_license_check_again_desk' ) ) {
		global $MWQDC_LB;
		if($MWQDC_LB->option_checked('mw_wc_qbo_desk_trial_license')){
			delete_option('mw_wc_qbo_desk_localkey');
			//			
			if($MWQDC_LB->is_valid_license($MWQDC_LB->get_option('mw_wc_qbo_desk_license'),'',true)){
				//Action
			}
			echo 'Success';
		}		
	}	
	wp_die();
}

function mw_wc_qbo_sync_del_license_local_key_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_del_license_local_key_desk', 'del_license_local_key_desk' ) ) {
		delete_option('mw_wc_qbo_desk_localkey');
		echo 'Success';
	}	
	wp_die();
}

//
function mw_wc_qbo_sync_rg_all_inc_variation_names_desk(){
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_rg_all_inc_variation_names_desk', 'rg_all_inc_variation_names_desk' ) ) {
		global $MWQDC_LB;		
		//$tot_vn_updated =  $MWQDC_LB->Fix_All_WooCommerce_Variations_Names();
		$tot_vn_updated = 0;
		echo 'Total number of variations name updated: '.$tot_vn_updated;
	}	
	wp_die();
}