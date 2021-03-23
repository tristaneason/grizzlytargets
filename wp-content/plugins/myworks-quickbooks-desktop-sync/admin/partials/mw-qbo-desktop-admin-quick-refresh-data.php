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
$page_url = 'admin.php?page=mw-qbo-desktop-refresh-data';
 MW_QBO_Desktop_Admin::is_trial_version_check();

if(isset($_POST['mw_wc_qbd_desk_settings']) && check_admin_referer( 'myworks_wc_qbd_sync_desktop_refresh_data', 'myworks_wc_qbd_sync_desktop_refresh_data' )){
	//
	$cpopt_d = $MWQDC_LB->get_option('mw_wc_qbo_desk_cp_rf_data_options');
	$odopt_d = $MWQDC_LB->get_option('mw_wc_qbo_desk_oth_rf_data_options');	
	
	update_option('mw_wc_qbo_desk_cp_refresh_data_enable', isset($_POST['mw_wc_qbo_desk_cp_refresh_data_enable'])?$_POST['mw_wc_qbo_desk_cp_refresh_data_enable']:'');
	
	update_option('mw_wc_qbo_desk_cp_rf_data_options', isset($_POST['mw_wc_qbo_desk_cp_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_cp_rf_data_options']) && count($_POST['mw_wc_qbo_desk_cp_rf_data_options']) ? implode(',', $_POST['mw_wc_qbo_desk_cp_rf_data_options']) : '');
	
	//Changes in Sync Queue
	if(isset($_POST['mw_wc_qbo_desk_cp_refresh_data_enable'])){
		$s_queue_change = false;
		if(isset($_POST['mw_wc_qbo_desk_cp_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_cp_rf_data_options']) && count($_POST['mw_wc_qbo_desk_cp_rf_data_options'])){
			$s_queue_change = true;
		}
		
		if(isset($_POST['mw_wc_qbo_desk_oth_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_oth_rf_data_options']) && count($_POST['mw_wc_qbo_desk_oth_rf_data_options'])){
			$s_queue_change = true;
		}
		
		if($s_queue_change){			
			//$wpdb->query($wpdb->prepare("DELETE FROM `quickbooks_queue` WHERE `qb_status` = %s AND `dequeue_datetime` = NULL ",'q'));
		}
	}
	
	if(isset($_POST['mw_wc_qbo_desk_cp_refresh_data_enable'])){
		update_option('mw_wc_qbo_desk_oth_refresh_data_enable','true');
	}else{
		update_option('mw_wc_qbo_desk_oth_refresh_data_enable','');
	}	
	
	update_option('mw_wc_qbo_desk_oth_rf_data_options', isset($_POST['mw_wc_qbo_desk_oth_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_oth_rf_data_options']) && count($_POST['mw_wc_qbo_desk_oth_rf_data_options']) ? implode(',', $_POST['mw_wc_qbo_desk_oth_rf_data_options']) : '');
	
	//
	$cpopt_p = (isset($_POST['mw_wc_qbo_desk_cp_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_cp_rf_data_options']))?implode(',',$_POST['mw_wc_qbo_desk_cp_rf_data_options']):'';
	
	$odopt_p = (isset($_POST['mw_wc_qbo_desk_oth_rf_data_options']) && is_array($_POST['mw_wc_qbo_desk_oth_rf_data_options']))?implode(',',$_POST['mw_wc_qbo_desk_oth_rf_data_options']):'';
	
	if($cpopt_d != $cpopt_p || $odopt_d != $odopt_p){
		$rddt_l = ((!empty($cpopt_p))?$cpopt_p.',':$cpopt_p).$odopt_p;
		$MWQDC_LB->save_log(array('log_type'=>'Refresh Data','log_title'=>'Refresh Data Data Types Updated','details'=>'Date Types Enabled: '.$rddt_l,'status'=>2));
	}
	
	$MWQDC_LB->set_session_val('refresh_data_settings_update_message',__('Refresh data settings updated successfully.','mw_wc_qbo_desk'));
	$MWQDC_LB->redirect($page_url);
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php echo $MWQDC_LB->get_admin_get_extra_css_js();?>
<?php echo $MWQDC_LB->get_checkbox_switch_css_js();?>
<div class="mw_wc_qbo_desk_container">
<form method="post">
<?php wp_nonce_field( 'myworks_wc_qbd_sync_desktop_refresh_data', 'myworks_wc_qbd_sync_desktop_refresh_data' ); ?>
<div class="container mwqbd" id="mw_qbo_sybc_settings_tables">
	<div class="card">
		<div class="card-content">
			<div class="row">
				<div class="col s12 m12 l12">
					<div class="row">
						<div class="col s12 m12 l12">
                         
							<div id="mw_qbo_sybc_settings_tab_wh_body">
							<h4>Summary</h4>
							<p style="margin: 0px 20px;">"Refreshing Data" is when our sync scans QuickBooks for the latest list of data for any of the data types enabled on this page - when the web connector runs. The primary purpose of this is if something changed in one of these lists in QuickBooks and you need our sync to recognize that change. These switches should ONLY be ON when you'd like to refresh data - and should be turned OFF once the data refresh/web connector is finishd running. <a href="https://docs.myworks.software/woocommerce-sync-for-quickbooks-desktop/general-usage/how-to-refresh-data">See our documentation for more details.</a></p>
							<hr>
							
							<h4>Tips</h4>
							<p style="margin: 0px 20px;">
								
								- AutoRun should not be enabled in the Web Connector when refreshing data - so the web connector can fully complete, and you can disable the switches here before it runs again.</br>
								- The Queue (MyWorks Sync > Queue) should be empty (0) before running the web connector to refresh data after these switches are enabled.</br>
								
							</p>
							<hr>
							
							<h4>How to refresh data</h4>
						<p style="margin: 0px 20px;">
								1. First, turn on the master switch below, as well as the switches next to the data types you'd like to refresh - then save.</br>
								2. Ensure the Queue (MyWorks Sync > Queue) is at 0, then visit QuickBooks and click Update in the Web Connector.</br>
								3. After the web connector has completed running (and before it runs again), turn all the switches off on this page - then save.</br>
							</p>
							
							<table class="mw-qbo-sync-settings-table mwqs_setting_tab_body">
							<tbody>
								
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Master on/off','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cp_refresh_data_enable" id="mw_wc_qbo_desk_cp_refresh_data_enable" value="true" <?php if(get_option('mw_wc_qbo_desk_cp_refresh_data_enable')=='true') echo 'checked' ?>>
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Check to enable or disable Refresh Data the next time the Web Connector runs.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Data Types','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<?php $qbo_rt_import_items = array('customer' => 'Customer', 'product' => 'Product') ?>
													<?php if(is_array($qbo_rt_import_items) && count($qbo_rt_import_items)):?>
													<?php $rpi_val_arr = explode(',',get_option('mw_wc_qbo_desk_cp_rf_data_options'));?>
													<?php foreach($qbo_rt_import_items as $rpi_key => $rpi_val):?>
													<?php
														$rpi_checked = '';
														if(is_array($rpi_val_arr) && in_array($rpi_key,$rpi_val_arr)){
															$rpi_checked = ' checked="checked"';
														}
													?>
													
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_cp_rf_data_options[]" id="mw_wc_qbo_desk_cp_rf_data_options_<?php echo $rpi_key;?>" value="<?php echo $rpi_key;?>" <?php echo $rpi_checked;?>>
													&nbsp;<span class="rt_item_hd"><?php echo $rpi_val;?></span>
													<br /><br />
													<?php endforeach;?>
													<?php endif;?>										            
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose data types to Refresh.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>
								
								<tr>
									<th class="title-description">
								    	<?php echo __('Other Data Types','mw_wc_qbo_desk') ?>
								    	
								    </th>
									<td>
										<div class="row">
											<div class="input-field col s12 m12 l12">
												<p>
													<?php $qbo_rt_import_items = array('account' => 'Account', 'class' => 'Class', 'payment_method' => 'Payment Method', 'sales_tax_code' => 'SalesTax Code', 'term' => 'Term', 'preferences' => 'Preferences') ?>
													
													<?php
														$qbo_rt_import_items['InventorySite'] = 'InventorySite';
														
														//$qbo_rt_import_items['OtherName'] = 'OtherName';
														$qbo_rt_import_items['SalesRep'] = 'SalesRep';
														
														$qbo_rt_import_items['CustomerType'] = 'CustomerType';
														$qbo_rt_import_items['ShipMethod'] = 'ShipMethod';
														
														$qbo_rt_import_items['Template'] = 'Template';
														
														if($MWQDC_LB->is_import_vendor()){
															$qbo_rt_import_items['Vendor'] = 'Vendor';
														}
														
													?>
													
													<?php if(is_array($qbo_rt_import_items) && count($qbo_rt_import_items)):?>
													<?php $rpi_val_arr = explode(',',get_option('mw_wc_qbo_desk_oth_rf_data_options'));?>
													<?php foreach($qbo_rt_import_items as $rpi_key => $rpi_val):?>
													<?php
														$rpi_checked = '';
														if(is_array($rpi_val_arr) && in_array($rpi_key,$rpi_val_arr)){
															$rpi_checked = ' checked="checked"';
														}
													?>
													
													<input type="checkbox" class="filled-in mwqs_st_chk  production-option" name="mw_wc_qbo_desk_oth_rf_data_options[]" id="mw_wc_qbo_desk_oth_rf_data_options_<?php echo $rpi_key;?>" value="<?php echo $rpi_key;?>" <?php echo $rpi_checked;?>>
													&nbsp;<span class="rt_item_hd"><?php echo $rpi_val;?></span>
													<br /><br />
													<?php endforeach;?>
													<?php endif;?>										            
												</p>
											</div>
										</div>
									</td>
									<td>
										<div class="material-icons tooltipped right tooltip"><?php echo __('?','mw_wc_qbo_desk') ?>
										  <span class="tooltiptext"><?php echo __('Choose data types to Refresh.','mw_wc_qbo_desk') ?></span>
										</div>
									</td>
								</tr>

            				</tbody>
							</table>
							</div>
							
							<div class="mw_wc_qbo_desk_clear"></div>
							<div class="row">
								<div class="input-field col s12 m6 l4">
									<input type="submit" name="mw_wc_qbd_desk_settings" id="mw_wc_qbd_desk_settings" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" value="Save All">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</form>
</div>

<script >
	jQuery(document).ready(function(){
		jQuery('input.mwqs_st_chk').attr('data-size','small');
		jQuery('input.mwqs_st_chk').bootstrapSwitch();
	});
</script>

<?php
if($save_status = $MWQDC_LB->get_session_val('refresh_data_settings_update_message','',true)){
	$save_status = ($save_status!='')?$save_status:'error';
	$MWQDC_LB->set_admin_sweet_alert($save_status);
}
?>