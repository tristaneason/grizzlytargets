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
 global $MWQDC_LB;
 if(isset($_GET['debug']) && $_GET['debug']=='1'){
	 $MWQDC_LB->debug();
 }
 
 $dashboard_graph_period = $MWQDC_LB->get_session_val('dashboard_graph_period','month');
 $db_graph = $MWQDC_LB->get_log_chart_output($dashboard_graph_period);
 
 
 $plugin_version = $MWQDC_LB->get_plugin_version();
 
 MW_QBO_Desktop_Admin::is_trial_version_check();
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.js"></script>

<div id="mw_wc_qbo_sync_grph_div" style="background:white;">
<div class="page_title">
	<h4 title="<?php echo $plugin_version;?>"><?php _e( 'MyWorks WooCommerce Sync for QuickBooks Desktop', 'mw_wc_qbo_desk' );?></h4>
	<div class="dashboard_main_buttons">
	<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_mappings_desk', 'clear_all_mappings_desk' ); ?>
	<button title="<?php _e( 'Clear all data from map tables', 'mw_wc_qbo_desk' );?>" id="mwqs_clear_all_mappings"><?php _e( 'Clear All Mappings', 'mw_wc_qbo_desk' );?></button>
	
	<div id="mwqs_dashboard_ajax_loader"></div>
	</div>
</div>

<div id="mw_wc_qbo_sync_grph_div_new">
<?php echo $db_graph;?>
</div>

</div>

<?php 
	$dashboard_status_data = $MWQDC_LB->get_dashboard_status_items();
	//$MWQDC_LB->_p($dashboard_status_data);
?>
<div class="dash-bottm mwqs_db_status_cont qbd_db_sc">
     <div class="col-sm3 module-stat">
         <h3> QuickBooks Status</h3>
         <ul>
         	<li>
				<a <?php if(!$MWQDC_LB->get_array_isset($dashboard_status_data,'quickbooks_connection',false)){echo ' class="mwqbd_dbst_err"';}else{echo 'class="mwqbd_dbst_ok"';}?>>
					QuickBooks Connection
				</a>
			</li>
			
			<li style="display:none;">
				<a <?php if(!$MWQDC_LB->get_array_isset($dashboard_status_data,'initial_quickbooks_data_loaded',false)){echo ' class="mwqbd_dbst_err"';}else{echo 'class="mwqbd_dbst_ok"';}?>>
					Initial QuickBooks Data Loaded
				</a>
			</li>
			
			<li style="display:none;">
				<a <?php if(!$MWQDC_LB->get_array_isset($dashboard_status_data,'default_setting_saved',false)){echo ' class="mwqbd_dbst_err"';}else{echo 'class="mwqbd_dbst_ok"';}?>>
					Default Settings Saved
				</a>
			</li>
			
			<li style="display:none;">
				<a <?php if(!$MWQDC_LB->get_array_isset($dashboard_status_data,'mapping_active',false)){echo ' class="mwqbd_dbst_err"';}else{echo 'class="mwqbd_dbst_ok"';}?>>
					Mapping Active
				</a>
			</li>
			
			<!--New Items-->
			<li class="qsd_lcd">
				<a>
					<b>QuickBooks Customers</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'customer_loaded',0)?></span>
				</a>
			</li>
			
			<li class="qsd_lcd">
				<a>
					<b>QuickBooks Products</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'product_loaded',0)?></span>
				</a>
			</li>
			
			<li class="qsd_lcd">
				<a>
					<b>QuickBooks Accounts</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'account_loaded',0)?></span>
				</a>
			</li>
			
         </ul>
     </div>
     <div class="col-sm3 mapping-stat map-sta-a">
     	<h3> Mapping Status</h3>
         <ul>
         	<li>
				<a>
					<b>Customers Mapped</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'customer_mapped',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Products Mapped</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'product_mapped',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Variations Mapped</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'variation_mapped',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Gateways Mapped</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'gateway_mapped',0)?></span>
				</a>
			</li>
			
			
			
         </ul>  
     </div>
     <div class="col-sm3 mapping-stat sync-a">
     	<h3> WooCommerce Status</h3>
         <ul>         	
			<li>
				<a>
					<b>Customers</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'wc_total_customer',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Products</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'wc_total_product',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Variations</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'wc_total_variation',0)?></span>
				</a>
			</li>
			
			<li>
				<a>
					<b>Active Gateways</b>
					<span class="right-btnn"><?php echo $MWQDC_LB->get_array_isset($dashboard_status_data,'wc_total_gateway',0)?></span>
				</a>
			</li>
         </ul>  
     </div>
</div>

<?php 
	$logfile_path = plugin_dir_path( dirname(dirname( __FILE__ )) ) .'log'.DIRECTORY_SEPARATOR.'dev.log';
	if($MWQDC_LB->option_checked('mw_wc_qbo_desk_add_xml_req_into_log') && file_exists($logfile_path) && isset($_GET['debug'])):
	$logfile = @fopen($logfile_path, "r") or die("Unable to open plugin dev log file");
	$log_content = @fread($logfile,filesize($logfile_path));
?>

<div style="margin:20px 20px 0px 0px;">
<h5>Debug Log File</h5>
<textarea readonly="true" style="height:600px;background:white;width:100%;"><?php echo $log_content;?></textarea>
</div>

<?php
 fclose($logfile);
 endif;
 ?>

<script>
function mw_wc_qbo_sync_refresh_log_chart(period){	
	var data = {
		"action": 'mw_wc_qbo_sync_refresh_log_chart_desk',
		"period": period,
	};
	
	jQuery('#mw_wc_qbo_sync_grph_div_new').css('opacity',0.6);
	jQuery.ajax({
	   type: "POST",
	   url: ajaxurl,
	   data: data,
	   cache:  false ,
	   //datatype: "json",
	   success: function(result){
		   if(result!=0 && result!=''){
			jQuery('#mw_wc_qbo_sync_grph_div_new').html(result);
		   }else{
			 alert('Error!');			 
		   }
		   jQuery('#mw_wc_qbo_sync_grph_div_new').css('opacity',1);
	   },
	   error: function(result) {  
			alert('Error!');
			jQuery('#mw_wc_qbo_sync_grph_div_new').css('opacity',1);
	   }
	});
}

jQuery(document).ready(function($){
	
	$('#mwqs_clear_all_mappings').click(function(){
		if(confirm('<?php echo __('Are you sure, you want to clear all mappings?','mw_wc_qbo_desk')?>')){
			var loading_msg = 'Loading...';
			jQuery('#mwqs_dashboard_ajax_loader').html(loading_msg);
			var data = {
				"action": 'mw_wc_qbo_sync_clear_all_mappings_desk',
				"clear_all_mappings_desk": jQuery('#clear_all_mappings_desk').val(),
			};
			jQuery.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: data,
			   cache:  false ,
			   //datatype: "json",
			   success: function(result){
				   if(result!=0 && result!=''){
					 //alert('Success');
					 jQuery('#mwqs_dashboard_ajax_loader').html('Success!');
				   }else{
					 //alert('Error!');
					jQuery('#mwqs_dashboard_ajax_loader').html('Error!');
				   }				  
			   },
			   error: function(result) {  
					//alert('Error!');
					jQuery('#mwqs_dashboard_ajax_loader').html('Error!');
			   }
			});
		}
	});
});
</script>
