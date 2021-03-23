<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/admin/partials
 */
?>
<?php
	global $MWQDC_LB;
	global $wpdb;
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=paymentmethod';
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_payment_methods_desk', 'map_wc_qbo_payment_method_desk' ) ) {
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod';
		
		$wpdb->query($wpdb->prepare("DELETE FROM `$table` WHERE `id` > %d",0));
		$wpdb->query("TRUNCATE table {$table}");
		
		 foreach($_POST as $k=>$val){
			 $check_pm = false;
			 if (preg_match('#^pm__#', $k) === 1) {
				$check_pm = true;
			 }
			 
			 if($check_pm){
				$p_method_str = $k;
				$p_method_arr = explode('__',$p_method_str);
				 
				$p_method = '';
				$p_cur = '';
				if(count($p_method_arr)>2){
					$p_method = $p_method_arr[1];
					$p_cur = $p_method_arr[2];
				}
				
				$q_a_id  = $val;
				
				if($p_method!='' && $p_cur!=''){
					$qb_p_method_id = (isset($_POST[$p_method.'__'.$p_cur.'_qbpmethod']))?$_POST[$p_method.'__'.$p_cur.'_qbpmethod']:'';
					if(!empty($q_a_id) && !empty($qb_p_method_id)){
						
						$p_map_ep = 0;
						
						if(isset($_POST[$p_method.'__'.$p_cur.'_ep'])){
							$p_map_ep = 1;
						}
						
						//
						$p_map_er = 0;
						
						if(isset($_POST[$p_method.'__'.$p_cur.'_er'])){
							$p_map_er = 1;
						}						
						
						$term_id = (isset($_POST[$p_method.'__'.$p_cur.'_term']))?$_POST[$p_method.'__'.$p_cur.'_term']:'';
						//$qb_p_method_id = (isset($_POST[$p_method.'__'.$p_cur.'_qbpmethod']))?$_POST[$p_method.'__'.$p_cur.'_qbpmethod']:'';
						
						$ps_order_status = (isset($_POST[$p_method.'__'.$p_cur.'_orst']))?$_POST[$p_method.'__'.$p_cur.'_orst']:'';
						
						$order_sync_as = (isset($_POST[$p_method.'__'.$p_cur.'_qosa']))?$_POST[$p_method.'__'.$p_cur.'_qosa']:'';
						
						$qb_cr_ba_id = (isset($_POST[$p_method.'__'.$p_cur.'_qcrba']))?$_POST[$p_method.'__'.$p_cur.'_qcrba']:'';
						
						$qb_ip_ar_acc_id = (isset($_POST[$p_method.'__'.$p_cur.'_qipara']))?$_POST[$p_method.'__'.$p_cur.'_qipara']:'';
						
						//save
						$pm_map_save_data = array();
						
						$pm_map_save_data['wc_paymentmethod'] = $p_method;
						$pm_map_save_data['qbo_account_id'] = $q_a_id;
						$pm_map_save_data['currency'] = $p_cur;
						
						$pm_map_save_data['enable_payment'] = $p_map_ep;
						//
						$pm_map_save_data['enable_refund'] = $p_map_er;
						
						$pm_map_save_data['qb_p_method_id'] = $qb_p_method_id;
						
						$pm_map_save_data['term_id_str'] = (string) $term_id;
						
						
						//
						$pm_map_save_data['ps_order_status'] = $ps_order_status;
						
						$pm_map_save_data['order_sync_as'] = $order_sync_as;
						
						$pm_map_save_data['qb_cr_ba_id'] = $qb_cr_ba_id;
						
						$pm_map_save_data['qb_ip_ar_acc_id'] = $qb_ip_ar_acc_id;
						
						$pm_map_save_data = array_map(array($MWQDC_LB, 'sanitize'), $pm_map_save_data);
						//$MWQDC_LB->_p($pm_map_save_data);die;					
						$wpdb->insert($table, $pm_map_save_data);					
							
					}
				}
			 }
		 }
		 $MWQDC_LB->set_session_val('map_page_update_message',__('Payment methods mapped successfully.','mw_wc_qbo_desk'));
		 $MWQDC_LB->redirect($page_url);
	}
	
	$wc_p_methods = array();
	$wc_currency_list = array();
	$mw_wc_qbo_desk_store_currency = $MWQDC_LB->get_option('mw_wc_qbo_desk_store_currency');
	if($mw_wc_qbo_desk_store_currency!=''){
		$wc_currency_list = explode(',',$mw_wc_qbo_desk_store_currency);
	}else{
		$wc_currency_list[] = get_woocommerce_currency();
	}
	
	//
	$available_gateways = WC()->payment_gateways()->payment_gateways;
	if(is_array($available_gateways) && count($available_gateways)){
		foreach($available_gateways as $key=>$value){
			if($value->enabled=='yes'){
				$wc_p_methods[$value->id] = $value->title;
			}
		}
	}
	
	$is_valid_pm = false;
	if(is_array($wc_p_methods) && count($wc_p_methods) && is_array($wc_p_methods) && count($wc_currency_list)){
		$is_valid_pm = true;
	}
	
	$pm_map_data = $MWQDC_LB->get_tbl($wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod');

	//$qbo_account_options = '<option value=""></option>';
	$qbo_account_options = '';
	$cnt_key = "CONCAT(name,' (',acc_type,')') AS acc_name";
	$qbo_account_options.= $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_account','qbd_id',$cnt_key,'','name ASC','',true);

	//$qbo_account_options_pm = '<option value=""></option>';
	$qbo_account_options_pm = '';
	$qbo_account_options_pm.= $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_paymentmethod','qbd_id','name','','name ASC','',true);

	$qbo_account_options_term = '<option value=""></option>';
	$qbo_account_options_term.= $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_term','qbd_id','name','','name ASC','',true);
	
	$order_statuses = wc_get_order_statuses();
	
	$qost_arr = array(
		'Invoice' => 'Invoice',
		'SalesReceipt' => 'SalesReceipt',
		'SalesOrder' => 'SalesOrder',
		'Estimate' => 'Estimate'										
	);
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>
<style>
.advanced_payment_sync{display:none;}
.hide_advanced_payment_sync{display:none;}
</style>
<div class="container">
	<div class="page_title flex-box"><h4><?php _e( 'Payment Method Mappings', 'mw_wc_qbo_desk' );?></h4> 
		<div class="dashboard_main_buttons p-mapbtn">
			<button class="show_advanced_payment_sync" id="show_advanced_payment_sync">Show Advanced Options</button>
			<button class="hide_advanced_payment_sync" id="hide_advanced_payment_sync">Hide Advanced Options</button>
		</div>
	</div>
	
	<div class="card">
		<div class="card-content">
			<div class="row">
			<?php if($is_valid_pm):?>
				<form method="POST" class="col s12 m12 l12" onsubmit="javascript:return mw_pmm_f_validation();">
					<div class="row">
						<div class="col s12 m12 l12">
						<?php foreach($wc_p_methods as $pm_key => $pm_val):?>
							<div class="pm_map_list" style="margin:10px 0px 10px 0px;">
								<h5><?php echo $pm_val.' ('.$pm_key.')';?></h5>
								<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table menu-blue-bg menu-bg-a new-table" style="width:100%" cellpadding="5" cellspacing="5">
									<thead>
										<tr>
										<th width="40%">&nbsp;</th>
										<?php foreach($wc_currency_list as $c_val){?>			
										<th><b><?php echo $c_val;?></b></th>
										<?php }?>
										<th>&nbsp;</th>
										</tr>
									</thead>
									
									<tr class="default_payment_sync">
										<td height="40">
											Enable Payment Syncing											
										</td>
										<?php foreach($wc_currency_list as $c_val){?>			
										<td>
											<input data-cba="pm__<?php echo $pm_key;?>__<?php echo $c_val;?>" data-cba-qbpmethod="<?php echo $pm_key;?>__<?php echo $c_val;?>_qbpmethod" type="checkbox" class="pm_chk_ep pm_chk" value="1" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_ep" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_ep">
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Enable the syncing of payments for this gateway & specific currency. If not enabled, payments will not be synced in real time to QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
                                        </td>
									</tr>
									
									<?php if($MWQDC_LB->option_checked('mw_wc_qbo_desk_order_as_per_gateway')):?>
									<tr class="default_payment_sync">
										<td height="40">
											WooCommerce Order Sync As
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_qosa" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_qosa">												
												<?php echo $MWQDC_LB->only_option('',$qost_arr);?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose whether the WooCommerce order as Invoice, SalesReceipt, SalesOrder or Estimate.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>	
									</tr>
									<?php endif;?>
									
									<tr class="default_payment_sync">
										<td height="40">
											QuickBooks Payment Method											
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select style="background-color:#f4f4f4" disabled="disabled" title="Enable payment first" class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_qbpmethod" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_qbpmethod">
												<?php echo $qbo_account_options_pm;?>
											</select>
										</td>
										<?php }?>
                                        <td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select the Payment Method that this woocommerce Gateway corresponds to in QuickBooks. This will be reflected in invoice payments made in QuickBooks, and deposits made if batch support is enabled.','mw_wc_qbo_desk') ?></span>
											</div>
                                        </td>
									</tr>
									
									<tr class="default_payment_sync">
										<td height="40">
											QuickBooks Bank Account											
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select style="background-color:#f4f4f4" disabled="disabled" title="Enable payment first" class="qbo_select dd_qoba" name="pm__<?php echo $pm_key;?>__<?php echo $c_val;?>" id="pm__<?php echo $pm_key;?>__<?php echo $c_val;?>">
												<?php echo $qbo_account_options;?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the Bank Account in QuickBooks that payments from your woocommerce gateway will be deposited into in real life / in QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>										
									</tr>
									
									<tr class="advanced_payment_sync">
										<td height="40">
											Terms Mapping											
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_term" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_term">
												<?php echo $qbo_account_options_term;?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Select the Term in the QuickBooks Invoice, if any, that should be assigned to invoices paid with this payment method.','mw_wc_qbo_desk') ?></span>
											</div>
                                        </td>	
									</tr>
									
									<tr class="advanced_payment_sync">
										<td height="40">
											Enable Refund Syncing											
										</td>
										<?php foreach($wc_currency_list as $c_val){?>			
										<td>
											<input class="pm_chk" type="checkbox" value="1" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_er" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_er">
										</td>
										<?php }?>
										<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											<span class="tooltiptext"><?php echo __('Enable the syncing of refunds made with this gateway in this specific currency. If not enabled, refunds will not be synced in real time to QuickBooks.','mw_wc_qbo_desk') ?></span>
										</div>
										</td>
									</tr>
									
									<tr class="advanced_payment_sync">
										<td height="40">
											QuickBooks Check Refund Bank Account										
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_qcrba" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_qcrba">
												<?php echo $qbo_account_options;?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Choose the Bank Account in QuickBooks that check refunds from your woocommerce gateway will be deposited into in real life / in QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>										
									</tr>
									
									<tr class="advanced_payment_sync">
										<td height="40">
											QuickBooks A/R Account										
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_qipara" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_qipara">
												<option value=""></option>
												<?php echo $qbo_account_options;?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Optional. Only needs to be selected if multiple A/R accounts are used in QuickBooks. This selection will apply to all orders placed by this gateway and synced to QuickBooks.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>										
									</tr>
									
									<tr class="advanced_payment_sync">
										<td height="40">
											Sync artificial payment when order is marked as: 	</br>
												<span style="font-size:10px;color:grey;">This setting is ONLY for gateways like COD or Check where the payment is actually not recorded in WooCommerce.</span> 											
										</td>
										
										<?php foreach($wc_currency_list as $c_val){?>			
										<td class="new-widt">
											<select class="qbo_select" name="<?php echo $pm_key;?>__<?php echo $c_val;?>_orst" id="<?php echo $pm_key;?>__<?php echo $c_val;?>_orst">
												<option value=""></option>
												<?php echo $MWQDC_LB->only_option('',$order_statuses);?>
											</select>
										</td>
										<?php }?>
										<td>
											<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
											  <span class="tooltiptext"><?php echo __('Leave blank to sync payment when order is placed. To sync the payment to QuickBooks only when the order reaches a certain status. This is specifically helpful for gateways like COD or BACS where the payment is actually not recorded in WooCommerce.','mw_wc_qbo_desk') ?></span>
											</div>
										</td>	
									</tr>
									
								</table>
								</div>
							</div>
						<?php endforeach;?>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_payment_methods_desk', 'map_wc_qbo_payment_method_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button id="mw_pmm_sbtn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" disabled>Save</button>
						</div>
					</div>
				</form>
				
				<script type="text/javascript">
					jQuery(document).ready(function($){
						
						/**/
						jQuery('.default_payment_sync').show();
						jQuery('#show_advanced_payment_sync').show();
						jQuery('.advanced_payment_sync').hide();
						jQuery('#hide_advanced_payment_sync').hide();
						jQuery('.pm_map_list').removeClass('active');

						jQuery('#show_advanced_payment_sync').on('click', function(e){
							jQuery('#show_advanced_payment_sync').hide();
							jQuery('.advanced_payment_sync').show();
							jQuery('#hide_advanced_payment_sync').show();
							jQuery('.pm_map_list').addClass('active');
						});

						jQuery('#hide_advanced_payment_sync').on('click', function(e){
							jQuery('#show_advanced_payment_sync').show();
							jQuery('.advanced_payment_sync').hide();
							jQuery('#hide_advanced_payment_sync').hide();
							jQuery('.pm_map_list').removeClass('active');
						});
						
						jQuery('input.pm_chk').attr('data-size','small');					
						jQuery('.pm_chk_ep , .pm_chk_trn , .pm_chk_batch').on('switchChange.bootstrapSwitch', function () {
				
							var chk_ba = jQuery(this).attr('data-cba');
							if(chk_ba==''){return false;}
							
							var data_cba_vendor = false;
							if (typeof jQuery(this).attr('data-cba-vendor') !== typeof undefined && jQuery(this).attr('data-cba-vendor') !== false) {
								data_cba_vendor = jQuery(this).attr('data-cba-vendor');
							}
							
							var data_cba_qbpmethod = false;
							if (typeof jQuery(this).attr('data-cba-qbpmethod') !== typeof undefined && jQuery(this).attr('data-cba-qbpmethod') !== false) {
								data_cba_qbpmethod = jQuery(this).attr('data-cba-qbpmethod');
							}
							
							var data_cba_lwb = false;
							if (typeof jQuery(this).attr('data-cba-lwb') !== typeof undefined && jQuery(this).attr('data-cba-lwb') !== false) {
								data_cba_lwb = jQuery(this).attr('data-cba-lwb');
							}
							
							var data_cba_ibs = false;
							if (typeof jQuery(this).attr('data-cba-ibs') !== typeof undefined && jQuery(this).attr('data-cba-ibs') !== false) {
								data_cba_ibs = jQuery(this).attr('data-cba-ibs');
							}
							
							if(jQuery(this).is(':checked')){
								jQuery('#'+chk_ba).removeAttr('disabled');
								jQuery('#'+chk_ba).css('background-color','#ffffff');
								jQuery('#'+chk_ba).removeAttr('title');
								
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).removeAttr('disabled');
									jQuery('#'+data_cba_vendor).css('background-color','#ffffff');
									jQuery('#'+data_cba_vendor).removeAttr('title');
								}
								
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).bootstrapSwitch('disabled',false);
									jQuery('#'+data_cba_lwb).removeAttr('disabled');
									jQuery('#'+data_cba_lwb).css('background-color','#ffffff');
									jQuery('#'+data_cba_lwb).removeAttr('title');
								}
								
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).bootstrapSwitch('disabled',false);
									jQuery('#'+data_cba_ibs).removeAttr('disabled');
									jQuery('#'+data_cba_ibs).css('background-color','#ffffff');
									jQuery('#'+data_cba_ibs).removeAttr('title');
								}
								//
								if(data_cba_qbpmethod){					
									jQuery('#'+data_cba_qbpmethod).removeAttr('disabled');
									jQuery('#'+data_cba_qbpmethod).css('background-color','#ffffff');
									jQuery('#'+data_cba_qbpmethod).removeAttr('title');
								}
								
							}else{
								jQuery('#'+chk_ba).val('');
								jQuery('#'+chk_ba).attr('disabled','disabled');
								jQuery('#'+chk_ba).css('background-color','#f4f4f4');
								
								//
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).val('');
									jQuery('#'+data_cba_vendor).attr('disabled','disabled');
									jQuery('#'+data_cba_vendor).css('background-color','#f4f4f4');
								}
								
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).prop('checked', false);
									jQuery('#'+data_cba_lwb).bootstrapSwitch('disabled',true);
									jQuery('#'+data_cba_lwb).attr('disabled','disabled');
									jQuery('#'+data_cba_lwb).css('background-color','#f4f4f4');
								}
								
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).prop('checked', false);
									jQuery('#'+data_cba_ibs).bootstrapSwitch('disabled',true);
									jQuery('#'+data_cba_ibs).attr('disabled','disabled');
									jQuery('#'+data_cba_ibs).css('background-color','#f4f4f4');
								}
								
								if(data_cba_qbpmethod){
									jQuery('#'+data_cba_qbpmethod).val('');
									jQuery('#'+data_cba_qbpmethod).attr('disabled','disabled');
									jQuery('#'+data_cba_qbpmethod).css('background-color','#f4f4f4');
								}
								
								var c_title = '';							
								
								if(jQuery(this).hasClass('pm_chk_ep')){
									c_title = 'Enable payment first';
								}
								
								if(jQuery(this).hasClass('pm_chk_trn')){
									c_title = 'Enable transaction fee first';
								}
								
								if(jQuery(this).hasClass('pm_chk_batch')){
									c_title = 'Enable batch payment first';
								}
								
								jQuery('#'+chk_ba).attr('title',c_title);
								
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).attr('title',c_title);
								}
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).attr('title',c_title);
								}
								
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).attr('title',c_title);
								}
								
								if(data_cba_qbpmethod){
									jQuery('#'+data_cba_qbpmethod).attr('title',c_title);
								}
								
							}
						});
						
						<?php if(is_array($pm_map_data) && count($pm_map_data)):?>
						<?php foreach($pm_map_data as $list):?>
						<?php
							$p_map_ac_id = $list['qbo_account_id'];
							$w_p_method = $list['wc_paymentmethod'];
							$p_map_cur = $list['currency'];
							$p_map_ep = $list['enable_payment'];
							//
							$p_map_er = $list['enable_refund'];
							
							$qb_p_method_id = $list['qb_p_method_id'];
							$term_id = $list['term_id_str'];
							$ps_order_status = $list['ps_order_status'];
							$order_sync_as = $list['order_sync_as'];
							$qb_cr_ba_id = $list['qb_cr_ba_id'];
							
							$qb_ip_ar_acc_id = $list['qb_ip_ar_acc_id'];
							
						?>
						jQuery('#pm__<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>').val('<?php echo $p_map_ac_id;?>');
						
						<?php if($p_map_ep==1):?>
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_ep').prop('checked', true);
						<?php endif;?>
						
						<?php if($p_map_er==1):?>
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_er').prop('checked', true);
						<?php endif;?>
						
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_term').val('<?php echo $term_id;?>');
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_orst').val('<?php echo $ps_order_status;?>');
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_qbpmethod').val('<?php echo $qb_p_method_id;?>');						
						
						<?php if(!empty($order_sync_as)):?>
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_qosa').val('<?php echo $order_sync_as;?>');
						<?php endif;?>
						
						<?php if(!empty($qb_cr_ba_id)):?>
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_qcrba').val('<?php echo $qb_cr_ba_id;?>');
						<?php endif;?>
						
						<?php if(!empty($qb_ip_ar_acc_id)):?>
						jQuery('#<?php echo $w_p_method;?>__<?php echo $p_map_cur;?>_qipara').val('<?php echo $qb_ip_ar_acc_id;?>');
						<?php endif;?>
						
						<?php endforeach;?>
						<?php endif;?>
						
						jQuery('.pm_chk_ep , .pm_chk_trn , .pm_chk_batch').each(function(){
							var chk_ba = jQuery(this).attr('data-cba');
							if(chk_ba==''){return false;}
							
							var data_cba_vendor = false;
							if (typeof jQuery(this).attr('data-cba-vendor') !== typeof undefined && jQuery(this).attr('data-cba-vendor') !== false) {
								data_cba_vendor = jQuery(this).attr('data-cba-vendor');
							}
							
							var data_cba_lwb = false;
							if (typeof jQuery(this).attr('data-cba-lwb') !== typeof undefined && jQuery(this).attr('data-cba-lwb') !== false) {
								data_cba_lwb = jQuery(this).attr('data-cba-lwb');
							}
							
							var data_cba_ibs = false;
							if (typeof jQuery(this).attr('data-cba-ibs') !== typeof undefined && jQuery(this).attr('data-cba-ibs') !== false) {
								data_cba_ibs = jQuery(this).attr('data-cba-ibs');
							}
							
							
							var data_cba_qbpmethod = false;
							if (typeof jQuery(this).attr('data-cba-qbpmethod') !== typeof undefined && jQuery(this).attr('data-cba-qbpmethod') !== false) {
								data_cba_qbpmethod = jQuery(this).attr('data-cba-qbpmethod');
							}
							
							if(jQuery(this).is(':checked')){
								jQuery('#'+chk_ba).removeAttr('disabled');
								jQuery('#'+chk_ba).css('background-color','#ffffff');
								jQuery('#'+chk_ba).removeAttr('title');
								
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).removeAttr('disabled');
									jQuery('#'+data_cba_vendor).css('background-color','#ffffff');
									jQuery('#'+data_cba_vendor).removeAttr('title');
								}
								
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).bootstrapSwitch('disabled',false);
									jQuery('#'+data_cba_lwb).removeAttr('disabled');
									jQuery('#'+data_cba_lwb).css('background-color','#ffffff');
									jQuery('#'+data_cba_lwb).removeAttr('title');
								}
								
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).bootstrapSwitch('disabled',false);
									jQuery('#'+data_cba_ibs).removeAttr('disabled');
									jQuery('#'+data_cba_ibs).css('background-color','#ffffff');
									jQuery('#'+data_cba_ibs).removeAttr('title');
								}
								//
								if(data_cba_qbpmethod){
									jQuery('#'+data_cba_qbpmethod).removeAttr('disabled');
									jQuery('#'+data_cba_qbpmethod).css('background-color','#ffffff');
									jQuery('#'+data_cba_qbpmethod).removeAttr('title');
								}
								
							}else{
								jQuery('#'+chk_ba).val('');
								jQuery('#'+chk_ba).attr('disabled','disabled');
								jQuery('#'+chk_ba).css('background-color','#f4f4f4');
								
								//
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).val('');
									jQuery('#'+data_cba_vendor).attr('disabled','disabled');
									jQuery('#'+data_cba_vendor).css('background-color','#f4f4f4');
								}
								
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).prop('checked', false);
									jQuery('#'+data_cba_lwb).bootstrapSwitch('disabled',true);
									jQuery('#'+data_cba_lwb).attr('disabled','disabled');
									jQuery('#'+data_cba_lwb).css('background-color','#f4f4f4');
								}
								
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).prop('checked', false);
									jQuery('#'+data_cba_ibs).bootstrapSwitch('disabled',true);
									jQuery('#'+data_cba_ibs).attr('disabled','disabled');
									jQuery('#'+data_cba_ibs).css('background-color','#f4f4f4');
								}
								
								if(data_cba_qbpmethod){
									jQuery('#'+data_cba_qbpmethod).val('');
									jQuery('#'+data_cba_qbpmethod).attr('disabled','disabled');
									jQuery('#'+data_cba_qbpmethod).css('background-color','#f4f4f4');
								}
								
								var c_title = '';
								
								
								if(jQuery(this).hasClass('pm_chk_ep')){
									c_title = 'Enable payment first';
								}
								
								if(jQuery(this).hasClass('pm_chk_trn')){
									c_title = 'Enable transaction fee first';
								}
								
								if(jQuery(this).hasClass('pm_chk_batch')){
									c_title = 'Enable batch payment first';
								}
								
								jQuery('#'+chk_ba).attr('title',c_title);
								
								if(data_cba_vendor){
									jQuery('#'+data_cba_vendor).attr('title',c_title);
								}
								if(data_cba_lwb){
									jQuery('#'+data_cba_lwb).attr('title',c_title);
								}
								if(data_cba_ibs){
									jQuery('#'+data_cba_ibs).attr('title',c_title);
								}
								if(data_cba_qbpmethod){
									jQuery('#'+data_cba_qbpmethod).attr('title',c_title);
								}
							}
						});
						
						jQuery('#mw_pmm_sbtn').removeAttr('disabled');
						
						jQuery('input.pm_chk').bootstrapSwitch();
						
						/**/
						$('.dd_qoba option').filter(function() {
						return $.trim(this.text).indexOf('(Bank)') === -1 && $.trim(this.text).indexOf('(OtherCurrentAsset)') === -1;
						}).remove();
						//$(".dd_qoba").prepend('<option value=""></option>');
						
					});
					
					function Mw_isEmpty(val){
						return (val === undefined || val == null || val.length <= 0) ? true : false;
					}
					
					function mw_pmm_f_validation(){						
						var ive = false;
						jQuery('.pm_chk_ep').each(function(){
							if(jQuery(this).is(':checked')){
								epf_id = jQuery(this).attr('id');
								epf_id_st = epf_id.substring(0,epf_id.length - 3);															
								var qmpm_v = jQuery('#'+epf_id_st+'_qbpmethod').val();
								var qmba_v = jQuery('#pm__'+epf_id_st).val();
								
								if(Mw_isEmpty(qmpm_v) || Mw_isEmpty(qmba_v)){									
									ive = true;
								}
							}
						});
						
						if(ive){
							alert('Plesae select QuickBooks payment method and bank account for all enabled payments gateways');
							return false;
						}						
					}
				</script>
				<?php echo $MWQDC_LB->get_select2_js();?>
			<?php endif;?>
			</div>
		</div>
	</div>
</div>