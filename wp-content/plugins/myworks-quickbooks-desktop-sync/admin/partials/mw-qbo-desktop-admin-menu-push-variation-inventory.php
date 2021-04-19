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
	$page_url_product = 'admin.php?page=mw-qbo-desktop-push&tab=inventory';
	$page_url = 'admin.php?page=mw-qbo-desktop-push&tab=inventory&variation=1';
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('variation_inventory_push_search');
	$variation_inventory_push_search = $MWQDC_LB->get_session_val('variation_inventory_push_search');
	
	//$MWQDC_LB->set_and_get('variation_stock_srch');
	//$variation_stock_srch = $MWQDC_LB->get_session_val('variation_stock_srch');
	$variation_stock_srch = '';

	$total_records = $MWQDC_LB->count_woocommerce_variation_list($variation_inventory_push_search,true,$variation_stock_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_variation_list = $MWQDC_LB->get_woocommerce_variation_list($variation_inventory_push_search,true," $offset , $items_per_page",$variation_stock_srch);
	$wc_currency_symbol = get_woocommerce_currency_symbol();
	//$MWQDC_LB->_p($wc_variation_list);
	
	$show_sync_status = true;
	/*
	$show_sync_status = $MWQDC_LB->if_show_sync_status($items_per_page);	
	$push_map_data_arr = array();
	if($show_sync_status && is_array($wc_variation_list) && count($wc_variation_list)){
		$product_item_ids_arr = array();
		foreach($wc_variation_list as $p_val){
			if((int) $p_val['quickbook_product_id']){
				$product_item_ids_arr[] = "'".(int) $p_val['quickbook_product_id']."'";
			}		
		}
		$push_map_data_arr = $MWQDC_LB->get_push_product_map_data($product_item_ids_arr);
		//$MWQDC_LB->_p($push_map_data_arr);
	}
	*/

	?>
	
	<div class="mwqs_page_tab_cont">
		<span class="tab_one"><a href="<?php echo $page_url_product;?>"><?php _e( 'Products', 'mw_wc_qbo_sync' );?></a></span>
		&nbsp;
		<span class="tab_two active"><a href="<?php echo $page_url;?>"><?php _e( 'Variations', 'mw_wc_qbo_sync' );?></a></span>
	</div>
	
	<div class="container">
		<div class="page_title"><h4><?php _e( 'Variation Inventory Push', 'mw_wc_qbo_desk' );?></h4></div>
		<div class="card">
			<div class="card-content">

							<div class="col s12 m12 l12">

									<div class="panel panel-primary">
										 <div class="mw_wc_filter">
										 <span class="search_text">Search</span>
										  &nbsp;
										  <input type="text" id="variation_inventory_push_search" value="<?php echo $variation_inventory_push_search;?>">
										  &nbsp;
										<!--
										<span class="search_text">Stock Status</span>
										  &nbsp;										  
										  <span>
											  <select style="width:130px;" name="variation_stock_srch" id="variation_stock_srch">
												<option value="">All</option>
												<?php //echo  $MWQDC_LB->only_option($variation_stock_srch,array('instock'=>'In Stock','outofstock'=>'Out of Stock'));?>
											  </select>
										  </span>
										  &nbsp;
										  -->
									  
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
												<button id="push_selected_variation_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Push Selected Variation Inventory','mw_wc_qbo_desk')?></button>
												<button style="display:none;" disabled="disabled" id="push_all_variation_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push All Variations','mw_wc_qbo_desk')?></button>
												<button style="display:none;" disabled="disabled" id="push_all_unsynced_variation_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Push Un-synced Variations','mw_wc_qbo_desk')?></button>
											</div>
										</div>
										 <br />

										<?php if(is_array($wc_variation_list) && count($wc_variation_list)):?>
										<div class="table-m">
											<div class="myworks-wc-qbd-sync-table-responsive">
												<table class="table" id="mwqs_variation_inventory_push_table_desk">
													<thead>
														<tr>
															<th width="2%">
															<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'variation_inventory_push_')">
															</th>
															<th width="5%">ID</th>
															<th width="23%">Variation Name</th>
															<th width="24%">Parent Product</th>
															<th width="10%">SKU</th>
															<th width="8%">Price</th>														
															<th width="8%">WooCommerce</br>Stock</th>
															<th width="8%">QuickBooks</br>Stock</th>
															<th width="8%">Stock</br>Status</th>
															<th width="5%">Sync</br>Status</th>									
														</tr>
													</thead>
													<tbody>
													
													<?php foreach($wc_variation_list as $p_val):?>
													<?php
													$sync_status_html = '';
													if($show_sync_status){
														$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
														if($p_val['quickbook_product_id']!=''){
															$quickbook_product_id = $p_val['quickbook_product_id'];
															//if(is_array($push_map_data_arr) && in_array($quickbook_product_id,$push_map_data_arr)){}
															$sync_status_html = '<i title="Mapped to #'.$p_val['quickbook_product_id'].'" class="fa fa-check-circle" style="color:green"></i>';
														}
													}
													
													?>
													<tr>
														<td><input type="checkbox" id="variation_inventory_push_<?php echo $p_val['ID']?>"></td>
														<td><?php echo $p_val['ID']?></td>
														<td><?php _e( $p_val['name'], 'mw_wc_qbo_desk' );?></td>
														<td>
														<a title="<?php echo $p_val['parent_id']?>" target="_blank" href="post.php?post=<?php echo $p_val['parent_id']?>&action=edit">
														<?php _e( $p_val['parent_name'], 'mw_wc_qbo_desk' );?>
														</a>
														</td>
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
														
														<td><?php echo $p_val['stock_status'];?></td>
														
														<td><?php echo $sync_status_html;?></td>
													</tr>
													<?php endforeach;?>									
													</tbody>
												</table>
											</div>
										</div>
										<?php echo $pagination_links?>
										<?php else:?>
										<p><?php _e( 'No variations found.', 'mw_wc_qbo_desk' );?></p>
										<?php endif;?>						           
									</div>

							</div>
			</div>
		</div>
	</div>
	<?php $sync_window_url = $MWQDC_LB->get_sync_window_url();?>
	 <script type="text/javascript">
		function search_item(){		
			var variation_inventory_push_search = jQuery('#variation_inventory_push_search').val();
			variation_inventory_push_search = jQuery.trim(variation_inventory_push_search);
			var variation_stock_srch = jQuery('#variation_stock_srch').val();
			variation_stock_srch = jQuery.trim(variation_stock_srch);
			
			if(variation_inventory_push_search!='' || variation_stock_srch!=''){			
				window.location = '<?php echo $page_url;?>&variation_inventory_push_search='+variation_inventory_push_search+'&variation_stock_srch='+variation_stock_srch;
			}else{
				alert('<?php echo __('Please enter search keyword or select stock status.','mw_wc_qbo_desk')?>');
			}
		}

		function reset_item(){		
			window.location = '<?php echo $page_url;?>&variation_inventory_push_search=&variation_stock_srch=';
		}
		
		jQuery(document).ready(function($) {
			var item_type = 'v_inventory';
			$('#push_selected_variation_btn').click(function(){
				var item_ids = '';
				var item_checked = 0;
				
				jQuery( "input[id^='variation_inventory_push_']" ).each(function(){
					if(jQuery(this).is(":checked")){
						item_checked = 1;
						var only_id = jQuery(this).attr('id').replace('variation_inventory_push_','');
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
				
				popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_variation_inventory_push_desk',0,0,650,350);
				return false;
			});
			
			$('#push_all_variation_btn').click(function(){
				popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_all=1&item_type='+item_type,'mw_qs_variation_inventory_push_desk',0,0,650,350);
				return false;
			});
			
			$('#push_all_unsynced_variation_btn').click(function(){
				popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=push&sync_unsynced=1&item_type='+item_type,'mw_qs_variation_inventory_push_desk',0,0,650,350);
				return false;
			});
		});
	 </script>
	 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_variation_inventory_push_table_desk');?>