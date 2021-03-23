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
	$page_url = 'admin.php?page=mw-qbo-desktop-pull&tab=productprice';
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('productprice_pull_search');
	$productprice_pull_search = $MWQDC_LB->get_session_val('productprice_pull_search');
	
	$productprice_pull_search = $MWQDC_LB->sanitize($productprice_pull_search);
	$whr = '';
	
	if($productprice_pull_search!=''){
		$whr.= " AND `name` LIKE '%$productprice_pull_search%' ";
	}
	
	$total_records = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_items` WHERE `id` >0 {$whr} ");
	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);

	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$productprice_q = "SELECT * FROM `".$wpdb->prefix."mw_wc_qbo_desk_qbd_items` WHERE `id` >0 {$whr} ORDER BY `id` DESC LIMIT {$offset} , {$items_per_page} ";
	$qbd_productprice_list = $MWQDC_LB->get_data($productprice_q);
	//$MWQDC_LB->_p($qbd_productprice_list);
	
	$show_sync_status = true;
	$cs = get_woocommerce_currency_symbol();
?>

<div class="container mwqbd-cnt">
	<div class="page_title"><h4><?php _e( 'Pricing Pull', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">
			<div class="col s12 m12 l12">
				<div class="panel panel-primary">
					<div class="mw_wc_filter">
						<span class="search_text">Search</span>
						&nbsp;
						<input type="text" id="productprice_pull_search" value="<?php echo $productprice_pull_search;?>">					  
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
							<button id="pull_selected_productprice_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green"><?php echo __('Pull Selected Pricing','mw_wc_qbo_desk')?></button>
							<button style="display:none;" id="pull_all_productprice_btn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green hide"><?php echo __('Pull All Inventories','mw_wc_qbo_desk')?></button>						
						</div>
					</div>
					<br />
					
					<?php if(is_array($qbd_productprice_list) && count($qbd_productprice_list)):?>
					<div class="table-m">
						<div class="myworks-wc-qbd-sync-table-responsive">
							<table class="table tablesorter" id="mwqs_productprice_pull_table_desk">
								<thead>
									<tr>
										<th width="2%">
										<input type="checkbox" onclick="javascript:mw_qbo_sync_check_all_desk(this,'productprice_pull_')">
										</th>
										<th width="20%">QuickBooks ID #</th>
										<th width="30%">Product Name</th>
										<th width="20%">WooCommerce Price</th>
										<th width="20%">QuickBooks Price</th>
										<th width="5%">Sync</br>Status</th>								
									</tr>
								</thead>
								<tbody>
								
								<?php foreach($qbd_productprice_list as $p_val):?>
								<?php
								
								$sync_status_html = '';
								$wc_price = '';
								if($show_sync_status){
									$sync_status_html = '<i class="fa fa-times-circle" style="color:red"></i>';
									if(isset($p_val['wc_product_id']) && (int) $p_val['wc_product_id']){
										$wc_product_id = (int) $p_val['wc_product_id'];
									}else{
										$wc_product_id = (int) $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs','wc_product_id','quickbook_product_id',$p_val['qbd_id']);
										
										if(!$wc_product_id){
											$wc_product_id = (int) $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_variation_pairs','wc_variation_id','quickbook_product_id',$p_val['qbd_id']);
										}
									}
									if($wc_product_id>0){
										$sync_status_html = '<i title="Mapped to #'.$wc_product_id.'" class="fa fa-check-circle" style="color:green"></i>';
										$wc_price = get_post_meta($wc_product_id,'_price',true);
									}								
								}
								
								?>
								<tr>
									<td><input type="checkbox" id="productprice_pull_<?php echo $p_val['id']?>"></td>
									<td><?php echo $p_val['qbd_id']?></td>
									<td><?php _e( $p_val['name'], 'mw_wc_qbo_desk' );?></td>
									<td><?php echo ($wc_price!='')?$cs.$wc_price:$wc_price;?></td>
									<td>
										<?php 
											$qbd_price = '';
											$qp_info_arr = $p_val['info_arr'];
											if($qp_info_arr!=''){
												$qp_info_arr = unserialize($qp_info_arr);
												if(is_array($qp_info_arr) && count($qp_info_arr)){
													if(isset($qp_info_arr['Price'])){
														$qbd_price = $qp_info_arr['Price'];
													}
													
													if(empty($qbd_price)){
														if(isset($qp_info_arr['SalesPrice'])){
															$qbd_price = $qp_info_arr['SalesPrice'];
														}
													}
												}
											}
											echo ($qbd_price!='')?$cs.$qbd_price:$qbd_price;
										?>
									</td>
								
									<td><?php echo $sync_status_html;?></td>
								</tr>
								<?php endforeach;?>									
								</tbody>
							</table>
						</div>
					</div>
					<?php echo $pagination_links?>
					<?php else:?>
					<p><?php _e( 'No products found.', 'mw_wc_qbo_desk' );?></p>
					<?php endif;?>
									 
				</div>
			</div>
		</div>
	</div>
</div>

<?php $sync_window_url = $MWQDC_LB->get_sync_window_url();?>
 <script type="text/javascript">
	function search_item(){		
		var productprice_pull_search = jQuery('#productprice_pull_search').val();
		productprice_pull_search = jQuery.trim(productprice_pull_search);
		
		if(productprice_pull_search!=''){			
			window.location = '<?php echo $page_url;?>&productprice_pull_search='+productprice_pull_search;
		}else{
			alert('<?php echo __('Please enter search keyword','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&productprice_pull_search=';
	}
	
	jQuery(document).ready(function($) {
		var item_type = 'productprice';
		$('#pull_selected_productprice_btn').click(function(){
			var item_ids = '';
			var item_checked = 0;
			
			jQuery( "input[id^='productprice_pull_']" ).each(function(){
				if(jQuery(this).is(":checked")){
					item_checked = 1;
					var only_id = jQuery(this).attr('id').replace('productprice_pull_','');
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
			
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=pull&item_ids='+item_ids+'&item_type='+item_type,'mw_qs_productprice_pull_desk',0,0,650,350);
			return false;
		});
		
		$('#pull_all_productprice_btn').click(function(){
			popUpWindowDesk('<?php echo $sync_window_url;?>&sync_type=pull&sync_all=1&item_type='+item_type,'mw_qs_productprice_pull_desk',0,0,650,350);
			return false;
		});		
	});
 </script>
 <?php echo $MWQDC_LB->get_tablesorter_js('#mwqs_productprice_pull_table_desk');?>
