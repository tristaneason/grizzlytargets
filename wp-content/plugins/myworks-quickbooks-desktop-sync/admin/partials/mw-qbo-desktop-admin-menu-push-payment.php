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
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=payment';
	
	$clear_dpid = (isset($_GET['clear_dpid']))?(int) $_GET['clear_dpid']:0;
	$qbd_id = (isset($_GET['qbd_id']))?$MWQDC_LB->sanitize($_GET['qbd_id']):'';
	if($clear_dpid && $qbd_id){
		$wpdb->query($wpdb->prepare("DELETE FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_data_pairs` WHERE `d_type` = 'Payment' AND `wc_id` = %d AND `qbd_id` = %s ",$clear_dpid,$qbd_id));
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('payment_push_search');
	$payment_push_search = $MWQDC_LB->get_session_val('payment_push_search');

	$MWQDC_LB->set_and_get('payment_date_from');
	$payment_date_from = $MWQDC_LB->get_session_val('payment_date_from');

	$MWQDC_LB->set_and_get('payment_date_to');
	$payment_date_to = $MWQDC_LB->get_session_val('payment_date_to');
	
	/*Default 30 days limit*/
	if(!isset($_GET['payment_date_from'])){
		$f_cdt = $MWQDC_LB->now('Y-m-d');
		$payment_date_from = date('Y-m-d', strtotime($f_cdt . ' -30 days'));
	}
	
	$total_records = $MWQDC_LB->count_wc_payment_list($payment_push_search,$payment_date_from,$payment_date_to);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_payment_list = $MWQDC_LB->get_wc_payment_list($payment_push_search," $offset , $items_per_page",$payment_date_from,$payment_date_to);
	$order_statuses = wc_get_order_statuses();

	$wc_currency = get_woocommerce_currency();
	$wc_currency_symbol = get_woocommerce_currency_symbol($wc_currency);
	//$MWQDC_LB->_p($wc_payment_list);
?>
<div class="container">
	<div class="page_title"><h4><?php _e( 'Payment Push', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">


						<div class="col s12 m12 l12">

								<div class="panel panel-primary">
									<div class="mw_wc_filter">
									 <span class="search_text">Search</span>
									  &nbsp;
									  <input placeholder="<?php echo __('Name / Company / Order ID / NUM','mw_wc_qbo_desk')?>" type="text" id="payment_push_search" value="<?php echo $payment_push_search;?>">
									  &nbsp;
									  <input class="mwqs_datepicker" placeholder="<?php echo __('From yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="payment_date_from" value="<?php echo $payment_date_from;?>">
									  &nbsp;
									  <input class="mwqs_datepicker" placeholder="<?php echo __('To yyyy-mm-dd','mw_wc_qbo_desk')?>" type="text" id="payment_date_to" value="<?php echo $payment_date_to;?>">
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
											<button id="push_selected_payment_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Payments','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_payment_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Payments','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_unsynced_payment_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Payments','mw_wc_qbo_desk')?></button>
										</div>
									</div>
									 <br />
									 
									 <?php if(is_array($wc_payment_list) && count($wc_payment_list)):?>
									<div class="table-m">
										<div class="myworks-wc-qbd-sync-table-responsive">
											<table id="mwqs_payment_push_table" class="table tablesorter" width="100%">
												<thead>
													<tr>
														<th width="2%">
															<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'payment_push_')">
														</th>
														<th width="5%">#</th>
														<th width="9%"><?php _e( 'TXN', 'mw_wc_qbo_desk' ) ?></th>
														<th width="19%"><?php _e( 'Customer', 'mw_wc_qbo_desk' ) ?></th>
														<th width="12%"><?php _e( 'Order', 'mw_wc_qbo_desk' ) ?></th>
														<th width="8%" title="<?php _e( 'Order Amount', 'mw_wc_qbo_desk' ) ?>">
														<?php _e( 'Amount', 'mw_wc_qbo_desk' ) ?>
														</th>
														<th width="6%"><?php _e( 'Txn Fee', 'mw_wc_qbo_desk' ) ?></th>
														<th width="13%"><?php _e( 'Date', 'mw_wc_qbo_desk' ) ?></th>
														<th width="8%"><?php _e( 'Method', 'mw_wc_qbo_desk' ) ?></th>
														<th width="10%"><?php _e( 'Order Status', 'mw_wc_qbo_desk' ) ?></th>
														<th width="8%">&nbsp;</th>
													</tr>
												</thead>
												
												 <tbody>
													<?php foreach($wc_payment_list as $payment_details):?>
													<?php											
													$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
													$s_chk_disabled = '';
													if($payment_details['qbd_payment_id']){													
														$sync_status_html = '<i title="QBD Txn ID #'.$payment_details['qbd_payment_id'].'" class="fa fa-check-circle" style="color:green"></i>';
														$s_chk_disabled = 'disabled="disabled"';
													}												
													?>
													<tr>
														<td><input <?php echo $s_chk_disabled;?> type="checkbox" id="payment_push_<?php echo $payment_details['payment_id']?>"></td>
														<td><?php echo $payment_details['payment_id'] ?></td>
														<td><?php echo $payment_details['transaction_id'] ?></td>
														<td <?php if(!(int) $payment_details['customer_user']):?> style="color:red;" title="Guest Order"<?php endif;?>>
														<?php echo $payment_details['billing_first_name'] ?> <?php echo $payment_details['billing_last_name'] ?>
														</td>
														
														<?php if($MWQDC_LB->is_plugin_active('woocommerce-sequential-order-numbers-pro','woocommerce-sequential-order-numbers') && $payment_details['order_number_formatted']!='' && $payment_details['order_id']!=$payment_details['order_number_formatted']):?>
														
														<td>
														<a target="_blank" href="post.php?post=<?php echo (int) $payment_details['order_id'] ?>&action=edit"><?php echo $payment_details['order_number_formatted'] ?></a>
														</td>
														
														<?php else:?>
														
														<td>
														<a target="_blank" href="post.php?post=<?php echo (int) $payment_details['order_id'] ?>&action=edit"><?php echo $payment_details['order_id'] ?></a>
														</td>
														
														<?php endif;?>
														
														<td>
														<?php 
														if($wc_currency==$payment_details['order_currency']){
															echo $wc_currency_symbol;
														}else{
															echo $MWQDC_LB->get_array_isset($MWQDC_LB->get_world_currency_list(true),$payment_details['order_currency'],$payment_details['order_currency'],false);
														}													
														echo $payment_details['order_total'];
														?>
														</td>
														<td>
														<?php 
															if(isset($payment_details[$payment_details['payment_method'].'_txn_fee'])){
																echo $payment_details[$payment_details['payment_method'].'_txn_fee'];
															}else{
																echo '0.00';
															}
														?>
														</td>
														<td><?php echo ($payment_details['paid_date']!='')?$payment_details['paid_date']:$payment_details['order_date']; ?></td>
														<td title="<?php echo $payment_details['payment_method_title'] ?>">
														<?php echo $payment_details['payment_method'] ?>
														</td>
														<td><?php echo $MWQDC_LB->get_array_isset($order_statuses,$payment_details['order_status'],$payment_details['order_status']); ?></td>
														<td>
														<?php echo $sync_status_html;?>
														<?php if($payment_details['qbd_payment_id']):?>
														&nbsp;
														<a class="mwqslld_btn_tsh" title="Clear From Data Pair" href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo __('Are you sure, you want to clear this from data pair!','mw_wc_qbo_desk')?>')){window.location='<?php echo  $page_url;?>&clear_dpid=<?php echo $payment_details['payment_id']?>&qbd_id=<?php echo $payment_details['qbd_payment_id']?>';}">Unlink</a>
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
											<?php echo $MWQDC_LB->get_slmt_hstry_msg();?>
										</div>
									<?php endif;?>
									
									<?php echo $pagination_links?>
									<?php else:?>
									<p><?php _e( 'No payments found.', 'mw_wc_qbo_desk' );?></p>
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
		var payment_push_search = jQuery('#payment_push_search').val();
		var payment_date_from = jQuery('#payment_date_from').val();
		var payment_date_to = jQuery('#payment_date_to').val();
		
		payment_push_search = jQuery.trim(payment_push_search);
		payment_date_from = jQuery.trim(payment_date_from);
		payment_date_to = jQuery.trim(payment_date_to);
		
		if(payment_push_search!='' || payment_date_from!='' || payment_date_to!=''){		
			window.location = '<?php echo $page_url;?>&payment_push_search='+payment_push_search+'&payment_date_from='+payment_date_from+'&payment_date_to='+payment_date_to;
		}else{
			alert('<?php echo __('Please enter search keyword or dates.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&payment_push_search=&payment_date_from=&payment_date_to=';
	}
	
	jQuery(document).ready(function($) {
		var item_type = 'payment';
		$('#push_selected_payment_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='payment_push_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('payment_push_','');
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
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_payment_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_payment_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_payment_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_unsynced_payment_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_payment_push_desk',0,0,650,350);
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
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_payment_push_table');?>
