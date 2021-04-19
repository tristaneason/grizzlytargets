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
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=coupon-code';
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_coupon_code_desk', 'map_wc_qbo_coupon_code_desk' ) ) {
		$item_ids = array();
		foreach ($_POST as $key=>$value){
			if ($MWQDC_LB->start_with($key, "map_coupon_code_")){
				$id = (int) str_replace("map_coupon_code_", "", $key);
				if($id){
					$item_ids[$id] = $value;
				}
			}
		}
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();			
				$save_data['qbo_product_id'] = $value;
				$save_data['class_id'] = (isset($_POST['class_map_coupon_code_'.$key]))?$_POST['class_map_coupon_code_'.$key]:'';
				
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_promo_code';
				if($MWQDC_LB->get_field_by_val($table,'id','promo_id',$key)){
					$wpdb->update($table,$save_data,array('promo_id'=>$key),'',array('%d'));
				}else{
					$save_data['promo_id'] = $key;
					$wpdb->insert($table, $save_data);
				}
			}
			$MWQDC_LB->set_session_val('map_page_update_message',__('Coupons mapped successfully.','mw_wc_qbo_desk'));
		}
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('coupon_map_search');
	$coupon_map_search = $MWQDC_LB->get_session_val('coupon_map_search');

	$wc_coupon_arr = $MWQDC_LB->get_custom_post_list('shop_coupon',$items_per_page,$coupon_map_search);

	$wc_coupon_codes = $wc_coupon_arr['post_array'];
	$pagination_links = $wc_coupon_arr['pagination_links'];
	
	$qbo_product_options = '';
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
		$qbo_product_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','name','','name ASC','',true);
	}
	
	$qbo_class_options_value = $MWQDC_LB->get_class_dropdown_list();
	$qbo_class_options = '<option value=""></option>';	
	$qbo_class_options.= ''.$qbo_class_options_value;
	
	$selected_options_script = '';
	
	$cpm_map_data = $MWQDC_LB->get_tbl($wpdb->prefix.'mw_wc_qbo_desk_qbd_map_promo_code');
	$cpm_map_data_kv_arr = array();
	if(is_array($cpm_map_data) && count($cpm_map_data)){
		foreach($cpm_map_data as $cpm_k=>$cpm_val){			
			if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
				$selected_options_script.='jQuery(\'#map_coupon_code_'.$cpm_val['promo_id'].'\').val(\''.$cpm_val['qbo_product_id'].'\');';
			}else{
				$cpm_map_data_kv_arr[$cpm_val['promo_id']] = $cpm_val['qbo_product_id'];
			}			
			$selected_options_script.='jQuery(\'#class_map_coupon_code_'.$cpm_val['promo_id'].'\').val(\''.$cpm_val['class_id'].'\');';
		}	
	}
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>
<div class="container map-coupon-code-outer">
	<div class="page_title"><h4><?php _e( 'Coupon Code Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="mw_wc_filter">
	 <span class="search_text">Search</span>
	  &nbsp;
	  <input type="text" id="coupon_map_search" value="<?php echo $coupon_map_search;?>">
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
	<div class="card">
		<div class="card-content">
			<div class="row">
			<?php if(is_array($wc_coupon_codes) && count($wc_coupon_codes)):?>
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table menu-blue-bg" width="100%">		
	                            	<thead>
	                                	<tr>
	                                    	<th width="50%">
											Woocommerce Coupon Code								    	
	                                        </th>
	                                        <th width="<?php echo !empty($qbo_class_options_value)?'25%':'50%' ?>">
	                                            Quickbooks Product								    	
	                                        </th>
											<?php if(!empty($qbo_class_options_value)):?>
	                                        <th width="25%">
	                                            Quickbooks Class
	                                        </th>
											<?php endif;?>
	                                	</tr>
	                                </thead>			
									
									<?php foreach($wc_coupon_codes as $cp_val):?>
									<tr>
										<td>
										<b><?php echo $cp_val->post_title;?></b>
										<p><?php echo stripslashes(strip_tags($cp_val->post_excerpt));?></p>
										</td>
										<td>
											<?php
												$dd_options = '<option value=""></option>';
												$dd_ext_class = '';
												if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
													$dd_ext_class = 'mwqs_dynamic_select_desk';
													if(count($cpm_map_data_kv_arr) && isset($cpm_map_data_kv_arr[$cp_val->ID]) && $cpm_map_data_kv_arr[$cp_val->ID]!=''){
														$itemid = $cpm_map_data_kv_arr[$cp_val->ID];
														$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
														if($qb_item_name!=''){
															$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
														}
													}
												}else{
													$dd_options.=$qbo_product_options;
												}
											?>
											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_coupon_code_<?php echo $cp_val->ID?>" id="map_coupon_code_<?php echo $cp_val->ID?>">
												<?php echo $dd_options;?>
											</select>
										</td>
										<?php if(!empty($qbo_class_options_value)):?>
										<td>										
											<select class="mw_wc_qbo_sync_select2_desk" name="class_map_coupon_code_<?php echo $cp_val->ID?>" id="class_map_coupon_code_<?php echo $cp_val->ID?>">
												<?php echo $qbo_class_options;?>
											</select>
										</td>
										<?php endif;?>
									</tr>
									<?php endforeach;?>
								</table>
							</div>
							<?php echo $pagination_links?>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_coupon_code_desk', 'map_wc_qbo_coupon_code_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button class="waves-effect waves-light btn save-btn mw-qbo-sync-green">Save</button>
						</div>
					</div>
				</form>
				<?php else:?>
				<p><?php _e( 'No coupon code found.', 'mw_wc_qbo_desk' );?></p>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function search_item(){		
		var coupon_map_search = jQuery('#coupon_map_search').val();
		coupon_map_search = jQuery.trim(coupon_map_search);
		if(coupon_map_search!=''){
			window.location = '<?php echo $page_url;?>&coupon_map_search='+coupon_map_search;
		}else{
			alert('<?php echo __('Please enter search keyword.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&coupon_map_search=';
	}
	<?php if($selected_options_script!=''):?>
	jQuery(document).ready(function(){
		<?php echo $selected_options_script;?>
	});
	<?php endif;?>	
 </script>
<?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_sync_select2_desk','qbo_product');?>