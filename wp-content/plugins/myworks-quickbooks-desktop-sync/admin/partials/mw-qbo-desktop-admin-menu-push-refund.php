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
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=refund';
	
	$clear_dpid = (isset($_GET['clear_dpid']))?(int) $_GET['clear_dpid']:0;
	$qbd_id = (isset($_GET['qbd_id']))?$MWQDC_LB->sanitize($_GET['qbd_id']):'';
	if($clear_dpid && $qbd_id){
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs` WHERE `d_type` = 'Refund' AND `wc_id` = %d AND `qbd_id` = %s ",$clear_dpid,$qbd_id));
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('refund_push_search');
	$refund_push_search = $MWQDC_LB->get_session_val('refund_push_search');

	$MWQDC_LB->set_and_get('refund_date_from');
	$refund_date_from = $MWQDC_LB->get_session_val('refund_date_from');

	$MWQDC_LB->set_and_get('refund_date_to');
	$refund_date_to = $MWQDC_LB->get_session_val('refund_date_to');
	
	/*Default 30 days limit*/
	if(!isset($_GET['refund_date_from'])){
		$f_cdt = $MWQDC_LB->now('Y-m-d');
		$refund_date_from = date('Y-m-d', strtotime($f_cdt . ' -30 days'));
	}
	
	//$MWQDC_LB->set_and_get('refund_status_srch');
	//$refund_status_srch = $MWQDC_LB->get_session_val('refund_status_srch');
	$refund_status_srch = '';


	$total_records = $MWQDC_LB->count_refund_list($refund_push_search,$refund_date_from,$refund_date_to,$refund_status_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_refund_list = $MWQDC_LB->get_refund_list($refund_push_search," $offset , $items_per_page",$refund_date_from,$refund_date_to,$refund_status_srch);
	$order_statuses = wc_get_order_statuses();

	$wc_currency = get_woocommerce_currency();
	$wc_currency_symbol = get_woocommerce_currency_symbol($wc_currency);
	
	//$MWQDC_LB->_p($wc_refund_list);	
	
?>

<div class="container">
	<div class="page_title"><h4><?php _e( 'Refund Push', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">			
						<div class="col s12 m12 l12">

						        <div class="panel panel-primary">
						            <div class="mw_wc_filter">
									 <span class="search_text">Search</span>
									  &nbsp;
									  <!--Name / Company / ID / NUM-->
									  <input placeholder="<?php echo __('Refund/Order ID','mw_wc_qbo_desk')?>" type="text" id="refund_push_search" value="<?php echo $refund_push_search;?>">
									  &nbsp;
									  <input style="width:130px;" class="mwqs_datepicker" placeholder="<?php echo __('From yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="refund_date_from" value="<?php echo $refund_date_from;?>">
									  &nbsp;
									  <input style="width:130px;" class="mwqs_datepicker" placeholder="<?php echo __('To yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="refund_date_to" value="<?php echo $refund_date_to;?>">									  
									  &nbsp;
									  <?php if( $html_section=false):?>
									  <span>
										  <select style="width:130px;" name="refund_status_srch" id="refund_status_srch">
											<option value="">All</option>
											<?php //echo $MWQDC_LB->only_option($refund_status_srch,$order_statuses);?>
										  </select>
									  </span>
									  &nbsp;
									  <?php endif;?>
									  <button onclick="javascript:search_item();" class="btn btn-info">Filter</button>
									  &nbsp;
									  <button onclick="javascript:reset_item();" class="btn btn-info">Reset</button>
									  &nbsp;
									  <span class="filter-right-sec"> 
										  <span class="entries">Show entries</span>
										  &nbsp;
										  <select style="width:50px;" onchange="javascript:window.location='<?php echo $page_url;?>&<?php echo $MWQDC_LB->per_page_keyword;?>='+this.value;">
											<?php echo  $MWQDC_LB->only_option($items_per_page,$MWQDC_LB->show_per_page);?>
										 </select>
									 </span>
									 </div>
									 <br />
									 
									 <?php if(is_array($wc_refund_list) && count($wc_refund_list)):?>
									 <div class="row">
										<div class="input-field col s12 m12 14">
											<button id="push_selected_refund_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Refunds','mw_wc_qbo_desk')?></button>
											<button style="display:none;" id="push_all_refund_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Refunds','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_unsynced_refund_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Refunds','mw_wc_qbo_desk')?></button>
										</div>
									</div>
									 <br />
									 <?php endif;?>

									 <?php if(is_array($wc_refund_list) && count($wc_refund_list)):?>
									<div class="table-m">
										<div class="myworks-wc-qbd-sync-table-responsive">
										   <table id="mwqs_refund_push_table" class="table tablesorter">
												<thead>
													<tr>
														<th width="2%">
														<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'refund_push_')">
														</th>
														<th>Refund ID</th>
														<th>Order ID</th>
														<th>Refund Date</th>
														<th>Refund Amount</th>
														<th>Order Amount</th>
														<th width="25%">Reason</th>
														<th>Sync Status</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($wc_refund_list as $refund_details):?>
													<?php
													$refund_meta = get_post_meta($refund_details['ID']);
													if(!is_array($refund_meta)){
														$refund_meta = array(
														'_order_currency' => array(''),
														'_order_total' => array(''),
														'_refund_amount' => array(''),
														'_refund_reason' => array('')
														);
													}
													
													?>
													<?php
													$trash_link = '';
													$s_chk_disabled = '';
													$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
													if($refund_details['qbd_lt_id']){													
														$sync_status_html = '<i title="QBD Txn ID #'.$refund_details['qbd_lt_id'].'" class="fa fa-check-circle" style="color:green"></i>';
														$s_chk_disabled = 'disabled="disabled"';
													}
													
													?>
													<tr>
														<td><input <?php echo $s_chk_disabled;?> type="checkbox" id="refund_push_<?php echo $refund_details['ID']?>"></td>
														
														<td><?php echo $refund_details['ID']?></td>
														<td>
														<a href="<?php echo admin_url('post.php?post='.$refund_details['order_id'].'&action=edit');?>" target="_blank">
														<?php echo $refund_details['order_id']?>
														</a>
														</td>
														<td><?php echo $refund_details['refund_date']?></td>
														
														<td>
														<?php 
														if($wc_currency==$refund_meta['_order_currency'][0]){
															echo $wc_currency_symbol;
														}else{
															echo $MWQDC_LB->get_array_isset($MWQDC_LB->get_world_currency_list(true),$refund_meta['order_currency'][0],$refund_meta['_order_currency'][0],false);
														}													
														echo ($refund_meta['_refund_amount'][0]!='')?$refund_meta['_refund_amount'][0]:'0.00';
														?>
														</td>
														<td>
														<?php 
														if($wc_currency==$refund_meta['_order_currency'][0]){
															echo $wc_currency_symbol;
														}else{
															echo $MWQDC_LB->get_array_isset($MWQDC_LB->get_world_currency_list(true),$refund_meta['order_currency'][0],$refund_meta['_order_currency'][0],false);
														}													
														//echo ($refund_meta['_order_total'][0]!='')?$refund_meta['_order_total'][0]:'0.00';
														echo get_post_meta($refund_details['order_id'],'_order_total',true);
														?>
														</td>
														<td><?php echo strip_tags($refund_meta['_refund_reason'][0]);?></td>
														
														<td>
														<?php echo $sync_status_html;?>
														<?php if($refund_details['qbd_lt_id']):?>
														&nbsp;
														<a class="mwqslld_btn_tsh" title="Clear From Data Pair" href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo __('Are you sure, you want to clear this from data pair!','mw_wc_qbo_desk')?>')){window.location='<?php echo  $page_url;?>&clear_dpid=<?php echo $refund_details['ID']?>&qbd_id=<?php echo $refund_details['qbd_lt_id']?>';}">Unlink</a>
														<?php endif;?>
														</td>
													</tr>
													<?php endforeach;?>		    	
												</tbody>
										   </table>
										</div>
									</div>
									
									<?php if($MWQDC_LB->is_pl_res_tml()):?>
										<div class="pp_mt_lsk_msg" style="text-align:center; padding:10px 5px;">
											<?php _e( '<h2>Need more than 30 days of history? Upgrade to a yearly license!</h2>', 'mw_wc_qbo_desk' );?>
										</div>
									<?php endif;?>
									
									<?php echo $pagination_links?>
									<?php else:?>
									<p><?php _e( 'No refunds found.', 'mw_wc_qbo_desk' );?></p>
									<?php endif;?>
						        </div>

						</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php $sync_window_url = $MWQDC_LB->get_sync_window_url();?>
 <script type="text/javascript">
	function search_item(){		
		var refund_push_search = jQuery('#refund_push_search').val();
		var refund_date_from = jQuery('#refund_date_from').val();
		var refund_date_to = jQuery('#refund_date_to').val();
		var refund_status_srch = jQuery('#refund_status_srch').val();
		
		refund_push_search = jQuery.trim(refund_push_search);
		refund_date_from = jQuery.trim(refund_date_from);
		refund_date_to = jQuery.trim(refund_date_to);
		refund_status_srch = jQuery.trim(refund_status_srch);
		
		if(refund_push_search!='' || refund_date_from!='' || refund_date_to!='' || refund_status_srch!=''){		
			window.location = '<?php echo $page_url;?>&refund_push_search='+refund_push_search+'&refund_date_from='+refund_date_from+'&refund_date_to='+refund_date_to+'&refund_status_srch='+refund_status_srch;
		}else{
			alert('<?php echo __('Please enter search keyword or dates and status.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&refund_push_search=&refund_date_from=&refund_date_to=&refund_status_srch=';
	}
	
	jQuery(document).ready(function($) {		 
		var item_type = 'refund';
		$('#push_selected_refund_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='refund_push_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('refund_push_','');
					only_id = parseInt(only_id);
					if(only_id>0){
						item_ids+=only_id+',';
					}					
				}
			});
			
			if(item_ids!=''){
				item_ids = item_ids.substring(0, item_ids.length - 1);
			}
			
			if(item_checked==0){
				alert('<?php echo __('Please select at least one item.','mw_wc_qbo_desk');?>');
				return false;
			}
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_refund_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_refund_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_refund_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_unsynced_refund_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_refund_push_desk',0,0,650,350);
			return false;
		});
	});
	
	jQuery( function($) {
		$('.mwqs_datepicker').css('cursor','pointer');
		$( ".mwqs_datepicker" ).datepicker(
			{ 
			dateFormat: 'yy-mm-dd',
			yearRange: "-50:+0",
			changeMonth: true,
			changeYear: true
			}
		);
	  } );
 </script>
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_refund_push_table');?>
