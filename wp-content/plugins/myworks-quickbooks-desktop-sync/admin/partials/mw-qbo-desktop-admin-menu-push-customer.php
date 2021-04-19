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
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=customer';
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('cl_push_search');
	$cl_push_search = $MWQDC_LB->get_session_val('cl_push_search');
	
	$MWQDC_LB->set_and_get('cl_um_srch');
	$cl_um_srch = $MWQDC_LB->get_session_val('cl_um_srch');

	$total_records = $MWQDC_LB->count_customers($cl_push_search,true,'',$cl_um_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$cl_push_data = $MWQDC_LB->get_customers($cl_push_search," $offset , $items_per_page",true,'',$cl_um_srch);
	//$MWQDC_LB->_p($cl_push_data);
	
	/*
	$push_map_data_arr = array();
	if(is_array($cl_push_data) && count($cl_push_data)){
		$cust_item_ids_arr = array();
		foreach($cl_push_data as $data){
			if((int) $data['qbd_customerid']){
				$cust_item_ids_arr[] = "'".$data['qbd_customerid']."'";
			}		
		}
		$push_map_data_arr = $MWQDC_LB->get_push_customer_map_data($cust_item_ids_arr);
		//$MWQDC_LB->_p($push_map_data_arr);
	}
	*/
?>
<div class="container mwqbd-cnt">
	<div class="page_title"><h4><?php _e( 'Customer Push', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">
			<div class="">
					<div class="">
						<div class="col s12 m12 l12">
							<div class="">
						        <div class="panel panel-primary">
						             <div class="mw_wc_filter">
									  <span class="search_text">Search</span>
									  &nbsp;
									  <input type="text" id="cl_push_search" placeholder="NAME / EMAIL / COMPANY / ID" value="<?php echo $cl_push_search;?>">
									  &nbsp;
									
									  <span class="search_text">Show</span>
									  &nbsp;
									  <select style="width:130px;" name="cl_um_srch" id="cl_um_srch">
										<?php if(empty($cl_um_srch)):?>
										<option value="">All</option>
										<?php endif;?>
										<?php echo  $MWQDC_LB->only_option($cl_um_srch,array('only_um'=>'Only Unmapped','only_m'=>'Only Mapped'));?>
									  </select>
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
											<button id="push_selected_customer_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Customers','mw_wc_qbo_desk')?></button>
											<button style="display:none;" id="push_all_customer_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Clients','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_unsynced_customer_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Clients','mw_wc_qbo_desk')?></button>
										</div>
									</div>
									<br />
									<div class="table-m">
										<div class="myworks-wc-qbd-sync-table-responsive">
											<table id="mwqs_customer_push_table_desk" class="table tablesorter">
												<thead>
													<tr>
														<th width="2%">
														<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'cl_push_')">
														</th>
														<th width="4%">ID</th>
														<th width="15%">Username</th>
														<th width="15%">First Name</th>
														<th width="15%">Last Name</th>
														<th width="15%">Email</th>
														<th width="29%">Company</th>
														<th width="5%">Sync</br>Status</th>
													</tr>
												</thead>
												<tbody>
												<?php if(count($cl_push_data)):?>
												<?php foreach($cl_push_data as $data):?>
												<?php
												
												$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
												if($data['qbd_customerid']){
													$qbd_customerid = $data['qbd_customerid'];												
													//if(is_array($push_map_data_arr) && in_array($qbd_customerid,$push_map_data_arr)){}
													
													$sync_status_html = '<i title="Mapped to #'.$data['qbd_customerid'].'" class="fa fa-check-circle" style="color:green"></i>';
												}
												
												?>
												<tr>
													<td><input type="checkbox" id="cl_push_<?php echo $data['ID']?>"></td>
													<td><?php echo $data['ID']?></td>
													<td><a href="<?php echo admin_url('user-edit.php?user_id=').$data['ID'] ?>" target="_blank"><?php echo $data['display_name']?></a></td>
													<td><?php echo $data['first_name']?></td>
													<td><?php echo $data['last_name']?></td>
													<td><?php echo $data['user_email']?></td>
													<td><?php echo $data['billing_company']?></td>
													<td><?php echo $sync_status_html;?></td>
												</tr>
												<?php endforeach;?>
												<?php endif;?>
												</tbody>
											</table>
										</div>
									</div>
						           <?php echo $pagination_links?>
						        </div>
						    </div>
						</div>
					</div>
			</div>
		</div>
	</div>
</div>
<?php $sync_window_url = $MWQDC_LB->get_sync_window_url();?>
 <script type="text/javascript">
	function search_item(){		
		var cl_push_search = jQuery('#cl_push_search').val();
		cl_push_search = jQuery.trim(cl_push_search);
		
		var cl_um_srch = jQuery('#cl_um_srch').val();
		cl_um_srch = jQuery.trim(cl_um_srch);
		
		if(cl_push_search!='' || cl_um_srch!=''){			
			window.location = '<?php echo $page_url;?>&cl_push_search='+cl_push_search+'&cl_um_srch='+cl_um_srch;;
		}else{
			alert('<?php echo __('Please enter search keyword or select mapped/unmapped.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&cl_push_search=&cl_um_srch=';
	}
	
	jQuery(document).ready(function($) {
		var item_type = 'customer';
		$('#push_selected_customer_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='cl_push_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('cl_push_','');
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
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_customer_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_customer_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_customer_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_unsynced_customer_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_customer_push_desk',0,0,650,350);
			return false;
		});
	});
 </script>
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_customer_push_table_desk');?>