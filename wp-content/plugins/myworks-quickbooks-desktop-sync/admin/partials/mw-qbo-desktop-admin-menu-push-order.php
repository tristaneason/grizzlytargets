﻿<?php
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
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=order';
	
	$clear_dpid = (isset($_GET['clear_dpid']))?(int) $_GET['clear_dpid']:0;
	$qbd_id = (isset($_GET['qbd_id']))?$MWQDC_LB->sanitize($_GET['qbd_id']):'';
	if($clear_dpid && $qbd_id){
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs` WHERE `d_type` = 'Order' AND `wc_id` = %d AND `qbd_id` = %s ",$clear_dpid,$qbd_id));
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('invoice_push_search');
	$invoice_push_search = $MWQDC_LB->get_session_val('invoice_push_search');

	$MWQDC_LB->set_and_get('invoice_date_from');
	$invoice_date_from = $MWQDC_LB->get_session_val('invoice_date_from');

	$MWQDC_LB->set_and_get('invoice_date_to');
	$invoice_date_to = $MWQDC_LB->get_session_val('invoice_date_to');
	
	/*Default 30 days limit*/
	if(!isset($_GET['invoice_date_from'])){
		$f_cdt = $MWQDC_LB->now('Y-m-d');
		$invoice_date_from = date('Y-m-d', strtotime($f_cdt . ' -30 days'));
	}
	
	$MWQDC_LB->set_and_get('invoice_status_srch');
	$invoice_status_srch = $MWQDC_LB->get_session_val('invoice_status_srch');


	$total_records = $MWQDC_LB->count_order_list($invoice_push_search,$invoice_date_from,$invoice_date_to,$invoice_status_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_order_list = $MWQDC_LB->get_order_list($invoice_push_search," $offset , $items_per_page",$invoice_date_from,$invoice_date_to,$invoice_status_srch);
	$order_statuses = wc_get_order_statuses();

	$wc_currency = get_woocommerce_currency();
	$wc_currency_symbol = get_woocommerce_currency_symbol($wc_currency);
	
	//$MWQDC_LB->_p($wc_order_list);
	
	$odf_t = 'Order Date';
	$odf_k = 'post_date';
	
	if($MWQDC_LB->get_option('mw_wc_qbo_desk_order_sync_qbd_dt_fld') == '_paid_date'){
		$odf_t = 'Paid Date';
		$odf_k = 'paid_date';
	}
	
?>

<div class="container">
	<div class="page_title"><h4><?php _e( 'Order Push', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">			
						<div class="col s12 m12 l12">

						        <div class="panel panel-primary">
						            <div class="mw_wc_filter">
									 <span class="search_text">Search</span>
									  &nbsp;
									  <input placeholder="<?php echo __('Name / Company / ID / NUM','mw_wc_qbo_desk')?>" type="text" id="invoice_push_search" value="<?php echo $invoice_push_search;?>">
									  &nbsp;
									  <input style="width:130px;" class="mwqs_datepicker" placeholder="<?php echo __('From yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="invoice_date_from" value="<?php echo $invoice_date_from;?>">
									  &nbsp;
									  <input style="width:130px;" class="mwqs_datepicker" placeholder="<?php echo __('To yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="invoice_date_to" value="<?php echo $invoice_date_to;?>">
									  &nbsp;
									  <span>
										  <select style="width:130px;" name="invoice_status_srch" id="invoice_status_srch">
											<option value="">All</option>
											<?php echo  $MWQDC_LB->only_option($invoice_status_srch,$order_statuses);?>
										  </select>
									  </span>
									  &nbsp;
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
									 <div class="row">
										<div class="input-field col s12 m12 14">
											<button id="push_selected_invoice_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Orders','mw_wc_qbo_desk')?></button>
											<button style="display:none;" id="push_all_invoice_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Orders','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_unsynced_invoice_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Orders','mw_wc_qbo_desk')?></button>
										</div>
									</div>
									 <br />

									 <?php if(is_array($wc_order_list) && count($wc_order_list)):?>
									<div class="table-m">
									    <div class="myworks-wc-qbd-sync-table-responsive">
										   <table id="mwqs_invoice_push_table" class="table tablesorter">
												<thead>
													<tr>
														<th width="2%">
														<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'invoice_push_')">
														</th>
														<th width="8%">Order ID</th>
														<th width="2%">&nbsp;</th>
														<th width="15%">Customer</th>
														<th width="15%">Company</th>
														<th width="13%"><?php echo $odf_t;?></th>
														<th width="10%">Amount</th>
														<th width="13%">Payment</br>Method</th>
														<th width="14%">Order</br>Status</th>
														<th width="8%">Sync</br>Status</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($wc_order_list as $order_details):?>
													<?php
													$trash_link = '';
													$s_chk_disabled = '';
													$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
													if($order_details['qbd_lt_id']){													
														$sync_status_html = '<i title="QBD Txn ID #'.$order_details['qbd_lt_id'].'" class="fa fa-check-circle" style="color:green"></i>';
														$s_chk_disabled = 'disabled="disabled"';
													}
													
													?>
													<tr>
														<td><input <?php echo $s_chk_disabled;?> type="checkbox" id="invoice_push_<?php echo $order_details['ID']?>"></td>
														
														<?php if($MWQDC_LB->is_plugin_active('woocommerce-sequential-order-numbers-pro','woocommerce-sequential-order-numbers') && $order_details['order_number_formatted']!='' && $order_details['ID']!=$order_details['order_number_formatted']):?>
														<td colspan="2">
														<a target="_blank" href="post.php?post=<?php echo (int) $order_details['ID'] ?>&action=edit">
														<?php echo $order_details['order_number_formatted']; ?><br />
														<?php echo 'ID: '.$order_details['ID']; ?>
														</a>
														</td>
														<?php else:?>
														<td><a target="_blank" href="post.php?post=<?php echo (int) $order_details['ID'] ?>&action=edit"><?php echo $order_details['ID'] ?></a></td>
														<td><?php //echo $order_details['order_key'] ?></td>
														<?php endif;?>
														
														<td <?php if(!(int) $order_details['customer_user']):?> style="color:#039be5;" title="Guest Order"<?php endif;?>>
														<?php echo $order_details['billing_first_name'] ?> <?php echo $order_details['billing_last_name'] ?>
														</td>
														<td><?php echo $order_details['billing_company'] ?></td>
														<td><?php echo (isset($order_details[$odf_k]))?$order_details[$odf_k]:''; ?></td>
														<td>
														<?php
														if($wc_currency==$order_details['order_currency']){
															echo $wc_currency_symbol;
														}else{
															echo $MWQDC_LB->get_array_isset($MWQDC_LB->get_world_currency_list(true),$order_details['order_currency'],$order_details['order_currency'],false);
														}													
														echo ($order_details['order_total']!='')?$order_details['order_total']:'0.00';
														?>
														</td>
														<td title="<?php echo $order_details['payment_method_title'] ?>">
															<?php echo $order_details['payment_method'] ?>
														</td>
														
														<td><?php echo $MWQDC_LB->get_array_isset($order_statuses,$order_details['post_status'],$order_details['post_status']); ?></td>
														<td>
														<?php echo $sync_status_html;?>
														<?php if($order_details['qbd_lt_id']):?>
														&nbsp;
														<a class="mwqslld_btn_tsh" title="Clear From Data Pair" href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo __('Are you sure, you want to clear this from data pair!','mw_wc_qbo_desk')?>')){window.location='<?php echo  $page_url;?>&clear_dpid=<?php echo $order_details['ID']?>&qbd_id=<?php echo $order_details['qbd_lt_id']?>';}">Unlink</a>
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
									<p><?php _e( 'No orders found.', 'mw_wc_qbo_desk' );?></p>
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
		var invoice_push_search = jQuery('#invoice_push_search').val();
		var invoice_date_from = jQuery('#invoice_date_from').val();
		var invoice_date_to = jQuery('#invoice_date_to').val();
		var invoice_status_srch = jQuery('#invoice_status_srch').val();
		
		invoice_push_search = jQuery.trim(invoice_push_search);
		invoice_date_from = jQuery.trim(invoice_date_from);
		invoice_date_to = jQuery.trim(invoice_date_to);
		invoice_status_srch = jQuery.trim(invoice_status_srch);
		
		if(invoice_push_search!='' || invoice_date_from!='' || invoice_date_to!='' || invoice_status_srch!=''){		
			window.location = '<?php echo $page_url;?>&invoice_push_search='+invoice_push_search+'&invoice_date_from='+invoice_date_from+'&invoice_date_to='+invoice_date_to+'&invoice_status_srch='+invoice_status_srch;
		}else{
			alert('<?php echo __('Please enter search keyword or dates and status.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&invoice_push_search=&invoice_date_from=&invoice_date_to=&invoice_status_srch=';
	}
	
	jQuery(document).ready(function($) {		 
		var item_type = 'invoice';
		$('#push_selected_invoice_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='invoice_push_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('invoice_push_','');
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
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_invoice_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_invoice_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_invoice_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_unsynced_invoice_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_invoice_push_desk',0,0,650,350);
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
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_invoice_push_table');?>
