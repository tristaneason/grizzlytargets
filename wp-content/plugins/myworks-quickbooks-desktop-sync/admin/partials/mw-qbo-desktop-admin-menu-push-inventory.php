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
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=inventory';
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('inventory_push_search');
	$inventory_push_search = $MWQDC_LB->get_session_val('inventory_push_search');
	
	$MWQDC_LB->set_and_get('inventory_stock_srch');
	$inventory_stock_srch = $MWQDC_LB->get_session_val('inventory_stock_srch');
	
	$MWQDC_LB->set_and_get('inventory_cat_search');
	$inventory_cat_search = $MWQDC_LB->get_session_val('inventory_cat_search');
	

	$total_records = $MWQDC_LB->count_woocommerce_product_list($inventory_push_search,true,$inventory_stock_srch,$inventory_cat_search);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_product_list = $MWQDC_LB->get_woocommerce_product_list($inventory_push_search," $offset , $items_per_page",true,$inventory_stock_srch,$inventory_cat_search);
	$wc_currency_symbol = get_woocommerce_currency_symbol();
	
	//$MWQDC_LB->_p($wc_product_list);
	
	//$show_sync_status = $MWQDC_LB->if_show_sync_status($items_per_page);
	$show_sync_status = true;
	/*		
	$push_map_data_arr = array();
	if($show_sync_status && is_array($wc_product_list) && count($wc_product_list)){
		$payment_item_ids_arr = array();
		foreach($wc_product_list as $p_val){
			if($p_val['quickbook_product_id']){
				$product_item_ids_arr[] = "'".$p_val['quickbook_product_id']."'";
			}		
		}
		$push_map_data_arr = $MWQDC_LB->get_push_inventory_map_data($product_item_ids_arr);
		//$MWQDC_LB->_p($push_map_data_arr);
	}
	*/
?>
<div class="mwqs_page_tab_cont">
	<span class="tab_one active"><a href="<?php echo $page_url;?>"><?php _e( 'Products', 'mw_wc_qbo_sync' );?></a></span>
	&nbsp;
	<span class="tab_two"><a href="<?php echo $page_url;?>&variation=1"><?php _e( 'Variations', 'mw_wc_qbo_sync' );?></a></span>
</div>

<div class="container mwqbd-cnt">
	<div class="page_title"><h4><?php _e( 'Inventory Push', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">

						<div class="col s12 m12 l12">

						        <div class="panel panel-primary">
						             <div class="mw_wc_filter">
									 <span class="search_text">Search</span>
									  &nbsp;
									  <input type="text" id="inventory_push_search" value="<?php echo $inventory_push_search;?>">
									  &nbsp;
									  
									  <span class="search_text">Stock Status</span>
									  &nbsp;
									  <span>
										  <select style="width:130px;" name="inventory_stock_srch" id="inventory_stock_srch">
											<option value="">All</option>
											<?php echo  $MWQDC_LB->only_option($inventory_stock_srch,array('instock'=>'In Stock','outofstock'=>'Out of Stock'));?>
										  </select>
									  </span>
									  &nbsp;
									  
									  <span class="search_text">Category</span>
									  &nbsp;
									  <span>
										  <select style="width:130px;" name="inventory_cat_search" id="inventory_cat_search">
											<option value="">All</option>
											<?php echo  $MWQDC_LB->only_option($inventory_cat_search,$MWQDC_LB->get_wc_product_cat_arr());?>
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
											<button id="push_selected_inventory_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Inventory Levels','mw_wc_qbo_desk')?></button>
											<button style="display:none;" id="push_all_inventory_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Inventories','mw_wc_qbo_desk')?></button>
											<button style="display:none;" disabled="disabled" id="push_all_unsynced_inventory_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Inventories','mw_wc_qbo_desk')?></button>
										</div>
									</div>
									 <br />

									<?php if(is_array($wc_product_list) && count($wc_product_list)):?>
									<div class="table-m">
										<div class="myworks-wc-qbd-sync-table-responsive">
											<table class="table tablesorter" id="mwqs_inventory_push_table_desk">
												<thead>
													<tr>
														<th width="2%">
														<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'inventory_push_')">
														</th>
														<th width="5%">ID</th>
														<th width="30%">Product Name</th>
														<th width="14%">SKU</th>
														<th width="8%">Price</th>													
														<th width="7%">WooCommerce</br>Stock</th>
														<th width="9%">QuickBooks</br>Stock</th>
														<th width="7%">Backorders</th>
														<th width="8%">Stock</br>Status</th>
														<th width="6%">Total</br>Sales</th>
														<th width="5%">Sync</br>Status</th>									
													</tr>
												</thead>
												<tbody>
												
												<?php foreach($wc_product_list as $p_val):?>
												<?php
												
												$sync_status_html = '';
												if($show_sync_status){
													$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
													if($p_val['quickbook_product_id']){
														$quickbook_product_id = $p_val['quickbook_product_id'];
														//if(is_array($push_map_data_arr) && in_array($quickbook_product_id,$push_map_data_arr)){}
														$sync_status_html = '<i title="Mapped to #'.$p_val['quickbook_product_id'].'" class="fa fa-check-circle" style="color:green"></i>';
													}
												}
												
												?>
												<tr>
													<td><input type="checkbox" id="inventory_push_<?php echo $p_val['ID']?>"></td>
													<td><?php echo $p_val['ID']?></td>
													<td><a href="<?php echo admin_url('post.php?action=edit&post=').$p_val['ID'] ?>" target="_blank"><?php _e( $p_val['name'], 'mw_wc_qbo_desk' );?></a></td>
													<td><?php echo $p_val['sku'];?></td>
													<td>
													<?php
													echo $wc_currency_symbol;
													echo (isset($p_val['price']))?floatval($p_val['price']):'0.00';
													?>
													</td>
													
													<td><?php echo number_format(floatval($p_val['stock']),2);?></td>
													
													<?php if($p_val['quickbook_product_id']!=''):?>
													<td class="p_wc_stock_<?php echo (int) $p_val['quickbook_product_id'];?>">
													<?php
														$qbd_qty = 0;
														$qp_info_arr = $p_val['qp_info_arr'];
														if($qp_info_arr!=''){
															$qp_info_arr = unserialize($qp_info_arr);
															if(is_array($qp_info_arr) && count($qp_info_arr)){
																if(isset($qp_info_arr['QuantityOnHand'])){
																	$qbd_qty = $qp_info_arr['QuantityOnHand'];
																}
															}
														}
														echo $qbd_qty;
													?>
													</td>
													<?php else:?>
													<td></td>
													<?php endif;?>												
													
													<td><?php echo $p_val['backorders'];?></td>
													<td><?php echo $p_val['stock_status'];?></td>
													<td><?php echo $p_val['total_sales'];?></td>
													<td><?php echo $sync_status_html;?></td>
												</tr>
												<?php endforeach;?>									
												</tbody>
											</table>
										</div>
									</div>
									<?php echo $pagination_links?>
									<?php else:?>
									<p><?php _e( 'No inventory found.', 'mw_wc_qbo_desk' );?></p>
									<?php endif;?>						           
						        </div>

						</div>
		</div>
	</div>
</div>

<?php $sync_window_url = $MWQDC_LB->get_sync_window_url();?>
 <script type="text/javascript">
	function search_item(){		
		var inventory_push_search = jQuery('#inventory_push_search').val();
		inventory_push_search = jQuery.trim(inventory_push_search);
		
		var inventory_stock_srch = jQuery('#inventory_stock_srch').val();
		inventory_stock_srch = jQuery.trim(inventory_stock_srch);
		
		var inventory_cat_search = jQuery('#inventory_cat_search').val();
		inventory_cat_search = jQuery.trim(inventory_cat_search);
		
		if(inventory_push_search!='' || inventory_stock_srch!='' || inventory_cat_search!=''){			
			window.location = '<?php echo $page_url;?>&inventory_push_search='+inventory_push_search+'&inventory_stock_srch='+inventory_stock_srch+'&inventory_cat_search='+inventory_cat_search;
		}else{
			alert('<?php echo __('Please enter search keyword or select stock status/category.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&inventory_push_search=&inventory_stock_srch=&inventory_cat_search=';
	}
	
	jQuery(document).ready(function($) {
		var item_type = 'inventory';
		$('#push_selected_inventory_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='inventory_push_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('inventory_push_','');
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
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_inventory_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_inventory_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_inventory_push_desk',0,0,650,350);
			return false;
		});
		
		$('#push_all_unsynced_inventory_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_inventory_push_desk',0,0,650,350);
			return false;
		});
	});
 </script>
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_inventory_push_table_desk');?>
 <?php //echo $MWQDC_LB->get_select2_js('#inventory_cat_search');?>
