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
	
	$page_url_product = 'admin.php?page=mw-qbo-desktop-map&tab=product';
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=product&variation=1';
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_variation_desk', 'map_wc_qbo_variation_desk' ) ) {
		$item_ids = array();
		foreach ($_POST as $key=>$value){
			if ($MWQDC_LB->start_with($key, "map_variation_")){
				$id = (int) str_replace("map_variation_", "", $key);
				if($id){
					$item_ids[$id] = $value;
				}
			}
		}
		
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_variation_pairs';
		
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();			
				$save_data['quickbook_product_id'] = $value;
				$save_data['class_id'] = (isset($_POST['class_map_variation_'.$key]))?$_POST['class_map_variation_'.$key]:'';			
				
				if($MWQDC_LB->get_field_by_val($table,'id','wc_variation_id',$key)){
					$wpdb->update($table,$save_data,array('wc_variation_id'=>$key),'',array('%d'));
				}else{
					$save_data['wc_variation_id'] = $key;
					$wpdb->insert($table, $save_data);
				}
			}
			$MWQDC_LB->set_session_val('map_page_update_message',__('Variations mapped successfully.','mw_wc_qbo_sync'));
		}
		//
		$wpdb->query("DELETE FROM `".$table."` WHERE `quickbook_product_id` = '' ");
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('variation_map_search');
	$variation_map_search = $MWQDC_LB->get_session_val('variation_map_search');
	
	$MWQDC_LB->set_and_get('variation_um_srch');
	$variation_um_srch = $MWQDC_LB->get_session_val('variation_um_srch');

	$total_records = $MWQDC_LB->count_woocommerce_variation_list($variation_map_search,false,'',$variation_um_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_variation_list = $MWQDC_LB->get_woocommerce_variation_list($variation_map_search,false," $offset , $items_per_page",'',$variation_um_srch);
	
	$qbo_product_options = '';
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
		if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_compt_qbd_adv_invt_sync')){
			$qbo_product_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','name','','name ASC','',true);
		}else{
			$qbd_p_q = "SELECT `qbd_id` , `name` , `info_arr` FROM {$wpdb->prefix}mw_wc_qbo_desk_qbd_items ORDER BY `name` ASC ";
			$qbd_p_arr = $MWQDC_LB->get_data($qbd_p_q);
			//$MWQDC_LB->_p($qbd_p_arr);
			if(is_array($qbd_p_arr) && !empty($qbd_p_arr)){
				foreach($qbd_p_arr as $qp){
					$qp_ov = $qp['qbd_id'];
					$qp_ot = $qp['name'];
					if(!empty($qp['info_arr'])){
						$info_arr = @unserialize($qp['info_arr']);
						if(is_array($info_arr) && !empty($info_arr)){
							$is_mpn_added = false;
							if(isset($info_arr['ManufacturerPartNumber']) && !empty($info_arr['ManufacturerPartNumber'])){
								$is_mpn_added = true;
								$qp_ot.= ': '.$info_arr['ManufacturerPartNumber'];
							}
							if(isset($info_arr['BarCodeValue']) && !empty($info_arr['BarCodeValue'])){
								if(!$is_mpn_added){
									$qp_ot.= ': '.$info_arr['BarCodeValue'];
								}else{
									$qp_ot.= ', '.$info_arr['BarCodeValue'];
								}
							}
						}
					}
					$qbo_product_options.= '<option value="'.$qp_ov.'">'.$qp_ot.'</option>';
				}
			}
		}
	}
	
	$qbo_class_options_value = $MWQDC_LB->get_class_dropdown_list();
	$qbo_class_options = '<option value=""></option>';	
	$qbo_class_options.= ''.$qbo_class_options_value;
	
	$selected_options_script = '';	
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>

<div class="container map-product-outer cnt-mwqbd">
	<div class="page_title"><h4><?php _e( 'Variation Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="mw_wc_filter">
		<span class="search_text">Search</span>
		&nbsp;
		  <input type="text" id="variation_map_search" value="<?php echo $variation_map_search;?>">
		&nbsp;
		
		<span class="search_text">Show</span>
		&nbsp;
		<select title="Mapped/UnMapped" style="width:130px;" name="variation_um_srch" id="variation_um_srch">
			<?php if(empty($variation_um_srch)):?>
			<option value="">All</option>
			<?php endif;?>
			<?php echo  $MWQDC_LB->only_option($variation_um_srch,array('only_um'=>'Only Unmapped','only_m'=>'Only Mapped'));?>
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
	 <div class="card">
		<div class="card-content">
			<div class="row">
				<?php if(is_array($wc_variation_list) && count($wc_variation_list)):?>
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table menu-blue-bg menu-bg-a new-table" width="100%">
									<thead>
										<tr>
											<th width="5%">&nbsp; ID</th>
											<th width="25%">
												WooCommerce Variation								    	
											</th>
											<th width="15%">
												Variation SKU						    	
											</th>
											<th width="15%">
												Parent Product						    	
											</th>
											<th width="<?php echo !empty($qbo_class_options_value)?'25%':'40%' ?>">
												QuickBooks Product								    	
											</th>
											<?php if(!empty($qbo_class_options_value)){ ?>
											<th width="15%">
												Quickbooks Class
											</th>
											<?php } ?>
											</tr>
										</tr>
									</thead>
									<?php foreach($wc_variation_list as $p_val):?>
									<tr>
										<td><?php echo $p_val['ID']?></td>
										<td>
										<a href="<?php echo admin_url('post.php?action=edit&post=').wp_get_post_parent_id( $p_val['ID'] ) ?>" target="_blank"><b><?php _e( $p_val['name'], 'mw_wc_qbo_desk' );?></b>				
											
											<p>
											Price: <?php echo $p_val['price'];?>
											<?php 
												if($p_val['attribute_names']!='' && $p_val['attribute_values']!=''){
													$attr_key_arr = explode(',',$p_val['attribute_names']);
													$attr_val_arr = explode(',',$p_val['attribute_values']);
												
													$attr_arr = @array_combine($attr_key_arr,$attr_val_arr);
													if(is_array($attr_arr) && count($attr_arr)){
														echo '<br />';
														foreach($attr_arr as $key=>$val){
															echo $key.': '.$val.'<br />';
														}
													}
												}
											?>
											</p></a>
											
											
										</td>
										
										<td><?php echo $p_val['sku']?></td>
										
										<td>
											<a title="<?php echo $p_val['parent_name']?>" target="_blank" href="post.php?post=<?php echo $p_val['parent_id']?>&action=edit">
												<?php echo $p_val['parent_id']?>
											</a>
										</td>
										
										<td>										
											
											<?php
											$dd_options = '<option value=""></option>';
											$dd_ext_class = '';
											if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
												$dd_ext_class = 'mwqs_dynamic_select_desk';
												if($p_val['quickbook_product_id']!=''){												
													$dd_options = '<option value="'.$p_val['quickbook_product_id'].'">'.$p_val['qp_name'].'</option>';
												}
											}else{
												$dd_options.=$qbo_product_options;
												if($p_val['quickbook_product_id']!=''){
													$selected_options_script.='jQuery(\'#map_variation_'.$p_val['ID'].'\').val(\''.$p_val['quickbook_product_id'].'\');';												
												}
											}																		
											?>
											
											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_variation_<?php echo $p_val['ID']?>" id="map_variation_<?php echo $p_val['ID']?>">
												<?php echo $dd_options;?>
											</select>
											
										</td>
										<?php if(!empty($qbo_class_options_value)){ ?>
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="class_map_variation_<?php echo $p_val['ID']?>" id="class_map_variation_<?php echo $p_val['ID']?>">
												<?php echo $qbo_class_options;?>
											</select>
											<?php 
											if($p_val['class_id']!=''){
												$selected_options_script.='jQuery(\'#class_map_variation_'.$p_val['ID'].'\').val(\''.$p_val['class_id'].'\');';
											}
											?>	
										</td>
										<?php } ?>
									</tr>
									<?php endforeach;?>
								</table>
							</div>
							<?php echo $pagination_links?>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_variation_desk', 'map_wc_qbo_variation_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button class="waves-effect waves-light btn save-btn mw-qbo-sync-green">Save</button>
						</div>
					</div>
				</form>
				
				<br />				
				<div class="col col-m">
				<h5><?php _e( 'Clear All Variations Mappings', 'mw_wc_qbo_desk' );?></h5>
				<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_mappings_variations_desk', 'clear_all_mappings_variations_desk' ); ?>
				<button id="mwqs_capm_btn"><?php _e( 'Clear Mappings', 'mw_wc_qbo_desk' );?></button>
				&nbsp;
				<span id="mwqs_capm_msg"></span>
				</div>
				
				<?php else:?>
				<p class="mwqbd_mp_msg"><?php _e( 'No variations found.', 'mw_wc_qbo_desk' );?></p>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function search_item(){		
		var variation_map_search = jQuery('#variation_map_search').val();
		variation_map_search = jQuery.trim(variation_map_search);
		
		var variation_um_srch = jQuery('#variation_um_srch').val();
		variation_um_srch = jQuery.trim(variation_um_srch);
		
		if(variation_map_search!='' || variation_um_srch!=''){
			window.location = '<?php echo $page_url;?>&variation_map_search='+variation_map_search+'&variation_um_srch='+variation_um_srch;
		}else{
			alert('<?php echo __('Please enter search keyword or select mapped/unmapped.','mw_wc_qbo_sync')?>');
		}
	}
	
	function reset_item(){		
		window.location = '<?php echo $page_url;?>&variation_map_search=&variation_um_srch=';
	}
	<?php if($selected_options_script!=''):?>
	jQuery(document).ready(function(){
		<?php echo $selected_options_script;?>
	});
	<?php endif;?>
	
	jQuery(document).ready(function($){
		
		$('#mwqs_automap_variations_wf_qf').click(function(){
			var vam_wf = $('#vam_wf').val().trim();
			var vam_qf = $('#vam_qf').val().trim();
			
			var mo_um = '';
			if($('#vam_moum_chk').is(':checked')){
				mo_um = 'true';				
			}			
			
			if(vam_wf!='' && vam_qf!=''){
				$('#vam_wqf_e_msg').html('');
				if(confirm('<?php echo __('This will override any previous variation mappings, and scan your WooCommerce & QuickBooks Desktop variations by selected fields to automatically match them for you.')?>')){
					jQuery('#mwqs_automap_variations_msg').html('');
					var data = {
						"action": 'mw_wc_qbo_sync_automap_variations_desk_wf_qf',
						"automap_variations_desk_wf_qf": jQuery('#automap_variations_desk_wf_qf').val(),
						"vam_wf": vam_wf,
						"vam_qf": vam_qf,
						"mo_um": mo_um,
					};
					var loading_msg = 'Loading...';
					jQuery('#mwqs_automap_variations_msg').html(loading_msg);
					jQuery.ajax({
					   type: "POST",
					   url: ajaxurl,
					   data: data,
					   cache:  false ,
					   //datatype: "json",
					   success: function(result){
						   if(result!=0 && result!=''){						
							jQuery('#mwqs_automap_variations_msg').html(result);						
							window.location='<?php echo admin_url($page_url)?>';
						   }else{
							jQuery('#mwqs_automap_variations_msg').html('Error!');						 
						   }				  
					   },
					   error: function(result) {						
							jQuery('#mwqs_automap_variations_msg').html('Error!');
					   }
					});
				}
			}else{
				$('#vam_wqf_e_msg').html('Please select automap fields.');
			}			
		});
		
		<?php if($js_section=false):?>
		$('#mwqs_automap_variations').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all variations?')?>')){
				jQuery('#mwqs_automap_variations_msg').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_variations_desk',
					"automap_variations_desk": jQuery('#automap_variations_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_variations_msg').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_variations_msg').html('Success');
						jQuery('#mwqs_automap_variations_msg').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_variations_msg').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_variations_msg').html('Error!');
				   }
				});
			}
		});
		
		$('#mwqs_automap_variations_by_name').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all variations by name?')?>')){
				jQuery('#mwqs_automap_variations_msg_by_name').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_variations_by_name_desk',
					"automap_variations_by_name_desk": jQuery('#automap_variations_by_name_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_variations_msg_by_name').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_variations_msg_by_name').html('Success');
						jQuery('#mwqs_automap_variations_msg_by_name').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_variations_msg_by_name').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_variations_msg_by_name').html('Error!');
				   }
				});
			}
		});
		
		<?php endif;?>
		
		$('#mwqs_capm_btn').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to clear all variation mappings?','mw_wc_qbo_sync')?>')){
				var loading_msg = 'Loading...';
				jQuery('#mwqs_capm_msg').html(loading_msg);
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_mappings_variations_desk',
					"clear_all_mappings_variations_desk": jQuery('#clear_all_mappings_variations_desk').val(),
				};
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						 //alert('Success');
						 jQuery('#mwqs_capm_msg').html('Success!');
						 window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 //alert('Error!');
						jQuery('#mwqs_capm_msg').html('Error!');
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_capm_msg').html('Error!');
				   }
				});
			}
		});
		
	});
 </script>
<?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_sync_select2_desk','qbo_product');?>