<?php
if ( ! defined( 'ABSPATH' ) )
     exit;
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://myworks.design/software/wordpress/woocommerce/myworks-wc-qbo-sync
 * @since      1.0.0
 *
 * @package    MyWorks_WC_QBO_Sync
 * @subpackage MyWorks_WC_QBO_Sync/admin/partials
 */
 ?>
 <?php
	global $MWQDC_LB;
	global $wpdb;
	$page_url = 'admin.php?page=mw-qbo-desktop-log';
	 MW_QBO_Desktop_Admin::is_trial_version_check();
	
	$del_log_id = (isset($_GET['del_log']))?(int) $_GET['del_log']:0;
	if($del_log_id){
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE `id` = %d",$del_log_id));
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	 $items_per_page = $MWQDC_LB->get_item_per_page();
	 
	$MWQDC_LB->set_and_get('log_search');
	$log_search = $MWQDC_LB->get_session_val('log_search');
	 
	$log_search = $MWQDC_LB->sanitize($log_search);
	$whr = '';
	if($log_search!=''){
	$whr.=" AND (`details` LIKE '%$log_search%' OR `log_type` LIKE '%$log_search%' OR `log_title` LIKE '%$log_search%' ) ";
	}
	$total_records = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE `id` >0 $whr ");
	 
	$page = $MWQDC_LB->get_page_var();
	 
	$offset = ( $page * $items_per_page ) - $items_per_page;
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);
	 
	$log_q = "SELECT * FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_log` WHERE `id` >0 $whr ORDER BY `id` DESC LIMIT $offset , $items_per_page";
	$log_data = $MWQDC_LB->get_data($log_q);
	
	
 ?>
<div class="container log-outr-sec">
   <div class="page_title">
      <h4>Sync Log</h4>
   </div>
   <div class="mw_wc_filter mwqbd">
      <span class="search_text">Search Log</span>
      &nbsp;
      <input type="text" value="<?php echo $log_search;?>" id="log_search">
      &nbsp;		
      <button class="btn btn-info" onclick="javascript:search_item();">Filter</button>
      &nbsp;
      <button class="btn btn-info" onclick="javascript:reset_item();">Reset</button>
      &nbsp;
      <span class="filter-right-sec">
         <span class="entries">Show entries</span>
         &nbsp;	
         <select style="width:50px;" onchange="javascript:window.location='<?php echo $page_url;?>&<?php echo $MWQDC_LB->per_page_keyword;?>='+this.value;">
			<?php echo  $MWQDC_LB->only_option($items_per_page,$MWQDC_LB->show_per_page);?>
		 </select>
      </span>
   </div>
   <br>
   <!--<div style="padding:10px;">Current Datetime: <?php echo $MWQDC_LB->get_cdt();?></div>-->
   <br>
   <div class="myworks-wc-qbd-sync-table-responsive">
   <table class="wp-list-table widefat fixed striped posts  menu-blue-bg">
      <thead>
         <tr>
            <th width="8%" style="text-align:center;">#</th>
            <th width="13%">&nbsp;</th>
            <th width="25%">&nbsp;</th>
            <th width="40%">Message</th>
            <th width="17%">Date</th>
            <th width="7%" style="text-align:center;">Action</th>
         </tr>
      </thead>
      <tbody id="mwqs-log-list">
	  <?php if(count($log_data)): $i=1;?>
	  <?php foreach($log_data as $data):?>
	  <?php 
		$tr_class = '';
		
		switch ($data['log_type']) {
			case "Customer":
				$tr_class = 'mwqbd_cust';
				break;
			case "Order":
				$tr_class = 'mwqbd_ord';
				break;
			case "Payment":
				$tr_class = 'mwqbd_pay';
				break;
			case "Product":
				$tr_class = 'mwqbd_pro';
				break;
			default:
				$tr_class = '';
		}
		
		if($tr_class!=''){
			$tr_class = 'class="'.$tr_class.'"';
		}
	  ?>
         <tr <?php echo $tr_class;?>>
            <td style="text-align:center;"><?php echo $data['id']?></td>
			<td><?php echo $data['log_type']?></td>
			<td <?php if( !$data['status']):?>style="color:red;"<?php endif;?>>
			<?php echo nl2br(stripslashes($data['log_title']));?>
			</td>
			<td <?php if( !$data['status']):?>style="color:red;"<?php endif;?>><?php echo nl2br(stripslashes($data['details']));?></td>
			<td><?php echo $data['added_date']?></td>
           <td style="text-align:center;">
		   <a class="mwqslld_btn" title="Delete" href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo __('Are you sure, you want to delete this!','mw_wc_qbo_sync')?>')){window.location='<?php echo  $page_url;?>&del_log=<?php echo $data['id']?>';}">x</a>
		   </td>
         </tr>
		<?php $i++;endforeach;?>
		<?php endif;?>
        
      </tbody>
   </table>
   </div>
   <?php echo $pagination_links?>
 
	 <?php if(count($log_data)):?>
	 <br />
	 <div> 
		<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_logs_desk', 'mwqs_clear_all_logs_desk' ); ?>
		<button id="mwqs_clear_all_logs_btn"><?php _e( 'Clear all logs', 'mw_wc_qbo_sync' );?></button>
		&nbsp;
		<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_log_errors_desk', 'mwqs_clear_all_log_errors_desk' ); ?>
		<button id="mwqs_clear_all_log_errors_btn"><?php _e( 'Clear all log errors', 'mw_wc_qbo_sync' );?></button>
		<br/>
		<br/>
		<br/>
	</div>
	<?php endif;?>
</div>

<script type="text/javascript">
	function search_item(){		
		var log_search = jQuery('#log_search').val();
		if(log_search!=''){
			window.location = '<?php echo $page_url;?>&log_search='+log_search;
		}else{
			alert('<?php echo __('Please enter search keyword.','mw_wc_qbo_sync')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&log_search=';
	}
	
	<?php if(count($log_data)):?>
	jQuery(document).ready(function($){		
		$('#mwqs_clear_all_logs_btn').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to clear all logs?','mw_wc_qbo_sync')?>')){
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_logs_desk',
					"mwqs_clear_all_logs_desk": jQuery('#mwqs_clear_all_logs_desk').val(),
				};
				var btn_text = $(this).html();
				var loading_msg = 'Loading...';
				$(this).html(loading_msg);
				
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   $('#mwqs_clear_all_logs_btn').html(btn_text);
					   if(result!=0 && result!=''){
						 //alert('Success');
						 window.location='<?php echo $page_url;?>';
					   }else{
						 alert('Error!');			 
					   }					   	
				   },
				   error: function(result) {
						$('#mwqs_clear_all_logs_btn').html(btn_text);
						alert('Error!');					
				   }
				});
			}
		});
		
		$('#mwqs_clear_all_log_errors_btn').click(function(){			
			if(confirm('<?php echo __('Are you sure, you want to clear all log errors?','mw_wc_qbo_sync')?>')){
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_log_errors_desk',
					"mwqs_clear_all_log_errors_desk": jQuery('#mwqs_clear_all_log_errors_desk').val(),
				};
				
				var btn_text = $(this).html();				
				var loading_msg = 'Loading...';
				$(this).html(loading_msg);
				
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					    $('#mwqs_clear_all_log_errors_btn').html(btn_text);
					   if(result!=0 && result!=''){
						 //alert('Success');
						 window.location='<?php echo $page_url;?>';
					   }else{
						 alert('Error!');			 
					   }				  
				   },
				   error: function(result) {
						$('#mwqs_clear_all_log_errors_btn').html(btn_text);
						alert('Error!');					
				   }
				});
			}
		});
	});
	<?php endif;?>
 </script>