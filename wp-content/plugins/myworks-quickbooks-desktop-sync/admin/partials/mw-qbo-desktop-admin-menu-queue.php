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
	$page_url = 'admin.php?page=mw-qbo-desktop-queue';
	
	 MW_QBO_Desktop_Admin::is_trial_version_check();
	
	$del_queue_id = (isset($_GET['del_queue']))?(int) $_GET['del_queue']:0;
	if($del_queue_id){
		$wpdb->query($wpdb->prepare("DELETE FROM `quickbooks_queue` WHERE `quickbooks_queue_id` = %d",$del_queue_id));
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();
	 
	$MWQDC_LB->set_and_get('queue_search');
	$queue_search = $MWQDC_LB->get_session_val('queue_search');	 
	$queue_search = $MWQDC_LB->sanitize($queue_search);
	
	
	$MWQDC_LB->set_and_get('queue_st_search');
	$queue_st_search = $MWQDC_LB->get_session_val('queue_st_search');	 
	$queue_st_search = $MWQDC_LB->sanitize($queue_st_search);
	if(empty($queue_st_search)){
		$queue_st_search = 'pending';
	}
	
	$whr = '';
	if($queue_st_search!=''){
		if($queue_st_search=='pending'){
			$whr.=" AND qb_status = 'q' AND dequeue_datetime IS NULL";
		}
		
		if($queue_st_search=='previous'){
			$whr.=" AND qb_status != 'q' AND dequeue_datetime !=''";
		}
		
		if($queue_st_search=='all'){
			$whr.=" AND qb_status != '' ";
		}
	}	
	
	if($queue_search!=''){
	$whr.=" AND (`qb_action` LIKE '%$queue_search%' OR `ident` = '$queue_search' ) ";
	}
	$total_records = $wpdb->get_var("SELECT COUNT(*) FROM `quickbooks_queue` WHERE `quickbooks_queue_id` >0 $whr ");
	 
	$page = $MWQDC_LB->get_page_var();
	 
	$offset = ( $page * $items_per_page ) - $items_per_page;
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);
	 
	$queue_q = "SELECT * FROM `quickbooks_queue` WHERE `quickbooks_queue_id` >0 $whr ORDER BY `quickbooks_queue_id` DESC LIMIT $offset , $items_per_page";
	$queue_data = $MWQDC_LB->get_data($queue_q);	
	
 ?>
 
 <div class="container log-outr-sec mwqbd-queue">
   <div class="page_title">
      <h4>Sync Queue</h4>
   </div>
   <div class="mw_wc_filter mwqbd">
      <span class="search_text">Search Queue</span>
      &nbsp;
      <input type="text" value="<?php echo $queue_search;?>" id="queue_search">
      &nbsp;
	  
	  <select id="queue_st_search">
		<?php echo  $MWQDC_LB->only_option($queue_st_search,$MWQDC_LB->queue_st_search_options);?>
	  </select>
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
   <div style="padding:10px;">Current Datetime: <?php echo date('Y-m-d H:i:s');?></div>
   <br>
   <div class="myworks-wc-qbd-sync-table-responsive">
   <table class="wp-list-table widefat fixed striped posts  menu-blue-bg">
      <thead>
         <tr>
            <th width="7%" style="text-align:center;">#</th>
            <th width="8%">Ticket ID</th>
            <th width="22%">Action</th>
            <th width="8%">Item #</th>			
            <th width="14%">Added</th>
			<th width="14%">Run At</th>
			<th width="9%">QB Status</th>
			<th width="7%">Priority</th>
			<th width="14%">Msg</th>
            <th width="7%" style="text-align:center;">Action</th>
         </tr>
      </thead>
      <tbody id="mwqs-queue-list">
	  <?php if(count($queue_data)): $i=1;?>
	  <?php foreach($queue_data as $data):?>
	  <?php 
		//
		$tr_class = '';
		if(!is_null($data['dequeue_datetime'])){
			$tr_class = 'dequeued';
		}
		if($tr_class!=''){
			$tr_class = 'class="'.$tr_class.'" style="opacity:0.5;"';
		}
	  ?>
         <tr <?php echo $tr_class;?> title="qb_username: <?php echo $data['qb_username']?>">
            <td style="text-align:center;"><?php echo $data['quickbooks_queue_id']?></td>
			 <td style="text-align:center;"><?php echo $data['quickbooks_ticket_id']?></td>
			 <td style="font-size:12px;"><?php echo $data['qb_action']?></td>
			 <td><?php echo $data['ident']?></td>
			<td><?php echo $data['enqueue_datetime']?></td>
			<td><?php echo $data['dequeue_datetime']?></td>
			<td><?php echo $data['qb_status']?></td>
			<td><?php echo $data['priority']?></td>
			<td><?php echo $data['msg']?></td>
           <td style="text-align:center;">
		   <a class="mwqslld_btn" title="Delete" href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo __('Are you sure, you want to delete this!','mw_wc_qbo_sync')?>')){window.location='<?php echo  $page_url;?>&del_queue=<?php echo $data['quickbooks_queue_id']?>';}">x</a>
		   </td>
         </tr>
		<?php $i++;endforeach;?>
		<?php endif;?>
        
      </tbody>
   </table>
   </div>
   <?php echo $pagination_links?>
  
   <?php if(count($queue_data)):?>
	 <br />
	 <div> 
		<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_queue_desk', 'mwqs_clear_all_queue_desk' ); ?>
		<button style="display:none;" id="mwqs_clear_all_queue_btn"><?php _e( 'Clear Queue', 'mw_wc_qbo_sync' );?></button>
		&nbsp;
		<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_queue_pending_desk', 'mwqs_clear_all_queue_pending_desk' ); ?>
		<button id="mwqs_clear_all_queue_pending_btn"><?php _e( 'Clear Queue', 'mw_wc_qbo_sync' );?></button>
		<br/>
		<br/>
		<br/>
	</div>
	<?php endif;?>
   
   <?php if(count($queue_data)):?>
    <br />
   <div class="mwqbd-queue-std-cnt">
	<h4>QB Status - Details</h4>
	 <div class="myworks-wc-qbd-sync-table-responsive">
		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<td width="15%">QB Status</td>
				<td>Description</td>
			</tr>
			
			<tr>
				<td>q</td>
				<td>Queuing status for queued QuickBooks transactions - QUEUED</td>
			</tr>
			
			<tr>
				<td>s</td>
				<td>QuickBooks status for queued QuickBooks transactions - was queued, then SUCCESSFULLY PROCESSED</td>
			</tr>
			
			<tr>
				<td>e</td>
				<td>QuickBooks status for queued QuickBooks transactions - was queued, an ERROR OCCURED when processing it</td>
			</tr>
			
			<tr>
				<td>i</td>
				<td>QuickBooks status for items that have been dequeued and are being processed by QuickBooks (we assume) but we havn't received a response back about them yet</td>
			</tr>
			
			<tr>
				<td>h</td>
				<td>QuickBooks status for items that were dequeued, had an error occured, and then the error was handled by an error handler</td>
			</tr>
			
			<tr>
				<td>c</td>
				<td>QuickBooks status for items that were cancelled</td>
			</tr>
			
			<tr>
				<td>r</td>
				<td>QuickBooks status for items that were forcibly removed from the queue</td>
			</tr>
			
			<tr>
				<td>n</td>
				<td>QuickBooks status for items that were NoOp</td>
			</tr>
			
		</table>
	 </div>
   </div>
   <?php endif;?>
   
</div>

<script type="text/javascript">
	function search_item(){		
		var queue_search = jQuery('#queue_search').val();
		var queue_st_search = jQuery('#queue_st_search').val();
		if(queue_search!='' || queue_st_search!=''){
			window.location = '<?php echo $page_url;?>&queue_search='+queue_search+'&queue_st_search='+queue_st_search;
		}else{
			alert('<?php echo __('Please enter search keyword.','mw_wc_qbo_sync')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&queue_search=';
	}
	<?php if(count($queue_data)):?>
	jQuery(document).ready(function($){		
		$('#mwqs_clear_all_queue_btn').click(function(){
			if(confirm('<?php echo __('This will clear the current activity in the queue.','mw_wc_qbo_sync')?>')){
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_queue_desk',
					"mwqs_clear_all_queue_desk": jQuery('#mwqs_clear_all_queue_desk').val(),
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
					   $('#mwqs_clear_all_queue_btn').html(btn_text);
					   if(result!=0 && result!=''){
						 //alert('Success');
						 window.location='<?php echo $page_url;?>';
					   }else{
						 alert('Error!');			 
					   }					   	
				   },
				   error: function(result) {
						$('#mwqs_clear_all_queue_btn').html(btn_text);
						alert('Error!');					
				   }
				});
			}
		});
		
		$('#mwqs_clear_all_queue_pending_btn').click(function(){			
			if(confirm('<?php echo __('This will clear the current activity in the queue.','mw_wc_qbo_sync')?>')){
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_queue_pending_desk',
					"mwqs_clear_all_queue_pending_desk": jQuery('#mwqs_clear_all_queue_pending_desk').val(),
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
					    $('#mwqs_clear_all_queue_pending_btn').html(btn_text);
					   if(result!=0 && result!=''){
						 //alert('Success');
						 window.location='<?php echo $page_url;?>';
					   }else{
						 alert('Error!');			 
					   }				  
				   },
				   error: function(result) {
						$('#mwqs_clear_all_queue_pending_btn').html(btn_text);
						alert('Error!');					
				   }
				});
			}
		});
	});
	<?php endif;?>
</script>