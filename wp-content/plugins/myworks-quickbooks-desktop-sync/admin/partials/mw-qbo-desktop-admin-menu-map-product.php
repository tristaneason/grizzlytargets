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
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=product';
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_product_desk', 'map_wc_qbo_product_desk' ) ) {
		//$MWQDC_LB->_p($_POST);die;
		
		$item_ids = array();
		foreach ($_POST as $key=>$value){
			if ($MWQDC_LB->start_with($key, "map_product_")){
				$id = (int) str_replace("map_product_", "", $key);
				if($id){
					$item_ids[$id] = $value;
				}
			}
		}
		
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_product_pairs';
		
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();			
				$save_data['quickbook_product_id'] = $value;
				$save_data['class_id'] = (isset($_POST['class_map_product_'.$key]))?$_POST['class_map_product_'.$key]:'';
				
				$save_data['qb_ar_acc_id'] = (isset($_POST['aracc_map_product_'.$key]))?$_POST['aracc_map_product_'.$key]:'';
				$save_data['qb_ivnt_site'] = (isset($_POST['ivntst_map_product_'.$key]))?$_POST['ivntst_map_product_'.$key]:'';
				
				//
				if(isset($_POST['allow_lid_product_'.$key])){
					$save_data['a_line_item_desc'] = 1;
				}else{
					$save_data['a_line_item_desc'] = 0;
				}
				
				if($MWQDC_LB->get_field_by_val($table,'id','wc_product_id',$key)){
					$wpdb->update($table,$save_data,array('wc_product_id'=>$key),'',array('%d'));
				}else{
					$save_data['wc_product_id'] = $key;
					$wpdb->insert($table, $save_data);
				}
			}
			$MWQDC_LB->set_session_val('map_page_update_message',__('Products mapped successfully.','mw_wc_qbo_sync'));
		}
		//
		$wpdb->query("DELETE FROM `".$table."` WHERE `quickbook_product_id` = '' ");
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('product_map_search');
	$product_map_search = $MWQDC_LB->get_session_val('product_map_search');
	
	$MWQDC_LB->set_and_get('product_stock_srch');
	$product_stock_srch = $MWQDC_LB->get_session_val('product_stock_srch');
	
	$MWQDC_LB->set_and_get('product_cat_search');
	$product_cat_search = $MWQDC_LB->get_session_val('product_cat_search');
	
	
	$MWQDC_LB->set_and_get('product_um_srch');
	$product_um_srch = $MWQDC_LB->get_session_val('product_um_srch');
	
	$total_records = $MWQDC_LB->count_woocommerce_product_list($product_map_search,false,$product_stock_srch,$product_cat_search,$product_um_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);

	$wc_product_list = $MWQDC_LB->get_woocommerce_product_list($product_map_search," $offset , $items_per_page",false,$product_stock_srch,$product_cat_search,$product_um_srch);
	
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
	
	$check_sh_paamc_hash = $MWQDC_LB->check_sh_paamc_hash();
	$check_sh_qbispplm_hash = $MWQDC_LB->check_sh_qbispplm_hash();	
	
	/**/
	$qbo_ar_acc_options_value = '';
	if($check_sh_paamc_hash){
		$dd_whr = "acc_type ='AccountsReceivable'";
		$qbo_ar_acc_options_value = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_account','qbd_id','name',$dd_whr,'name ASC','',true);
	}	
	
	$qbo_ar_acc_options = '<option value=""></option>';	
	$qbo_ar_acc_options.= ''.$qbo_ar_acc_options_value;
	
	$qbo_ivnt_site_options_value = '';
	if($check_sh_qbispplm_hash){
		if($MWQDC_LB->is_inv_site_bin_allowed()){
			$qbo_ivnt_site_options_value = $MWQDC_LB->get_inventory_site_bin_dd_options();//true
			//$MWQDC_LB->_p($qbo_ivnt_site_options_value);
		}else{
			$qbo_ivnt_site_options_value = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite','qbd_id','name','','name ASC','',true);
		}
	}	
	
	$qbo_ivnt_site_options = '<option value=""></option>';	
	$qbo_ivnt_site_options.= ''.$qbo_ivnt_site_options_value;
	
	$selected_options_script = '';
	
	$mw_wc_qbo_desk_skip_os_lid = $MWQDC_LB->option_checked('mw_wc_qbo_desk_skip_os_lid');
	
	$t_qp_w = '14%';
	$t_qc_w = '13%';
	$t_aa_w = '13%';
	
	$t_is_w = '13%';
	
	if(!$check_sh_paamc_hash){
		$t_qp_w = '20%';
		$t_qc_w = '20%';
		
		if(!$qbo_class_options_value){
			$t_qp_w = '40%';
		}
	}else{
		if(!$qbo_class_options_value){
			$t_qp_w = '27%';
		}
	}	
	
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>

<?php 
if($check_sh_paamc_hash || $check_sh_qbispplm_hash):
$s2_dd_w = ($check_sh_paamc_hash && $check_sh_qbispplm_hash)?150:200;
?>
<style>
	.mw-qbo-sync-settings-table select{
		float: none !important;
		width:<?php echo $s2_dd_w;?>px !important;
	}
	
	.mw-qbo-sync-settings-table .select2-container{
		float: none !important;
		width: <?php echo $s2_dd_w;?>px !important;
	}
</style>
<?php endif;?>

<div class="container map-product-outer cnt-mwqbd">
	<div class="page_title"><h4><?php _e( 'Product Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="mw_wc_filter">
		 <span class="search_text">Search</span>
		  &nbsp;
		  <input type="text" id="product_map_search" value="<?php echo $product_map_search;?>">
		   &nbsp;
		   <span class="search_text">Stock Status</span>
		  &nbsp;
		  <span>
			  <select style="width:80px;" name="product_stock_srch" id="product_stock_srch">
			  <?php if(empty($product_stock_srch)):?>
				<option value="">All</option>
				<?php endif;?>
				<?php echo  $MWQDC_LB->only_option($product_stock_srch,array('instock'=>'In Stock','outofstock'=>'Out of Stock'));?>
			  </select>
		  </span>
		  &nbsp;
		<span class="search_text">Category</span>
		&nbsp;
		<span>
		<select style="width:100px;" name="product_cat_search" id="product_cat_search">
		<?php if(empty($product_cat_search)):?>
		<option value="">All</option>
		<?php endif;?>
		<?php echo  $MWQDC_LB->only_option($product_cat_search,$MWQDC_LB->get_wc_product_cat_arr());?>
		</select>
		</span>
		&nbsp;
		
		<span>
		<select title="Mapped/UnMapped" style="width:80px;" name="product_um_srch" id="product_um_srch">
			<?php if(empty($product_um_srch)):?>
			<option value="">All</option>
			<?php endif;?>
			<?php echo  $MWQDC_LB->only_option($product_um_srch,array('only_um'=>'Only Unmapped','only_m'=>'Only Mapped'));?>
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
	 <div class="card">
		<div class="card-content">
			<div class="row">
				<?php if(is_array($wc_product_list) && count($wc_product_list)):?>
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-settings-table menu-blue-bg menu-bg-a new-table" width="100%">
									<thead>
										<tr>
											<th width="5%">&nbsp; ID</th>
											<th width="<?php echo ($mw_wc_qbo_desk_skip_os_lid)?'32%':'35%';?>">
												WooCommerce Product								    	
											</th>
											<th width="10%">
												SKU								    	
											</th>
											<th width="10%">Type</th>
											<th width="<?php echo $t_qp_w;?>">
												QuickBooks Product								    	
											</th>
											<?php if(!empty($qbo_class_options_value)){ ?>
											<th width="<?php echo $t_qc_w;?>">
												QuickBooks Class
											</th>
											<?php } ?>
											
											<?php if($check_sh_paamc_hash):?>
											<th width="<?php echo $t_aa_w;?>">
												QuickBooks AR Account
											</th>
											<?php endif;?>
											
											<?php if($check_sh_qbispplm_hash):?>
											<th width="<?php echo $t_is_w;?>">
												QB Inventory Site
											</th>
											<?php endif;?>
											
											<?php if($mw_wc_qbo_desk_skip_os_lid):?>
											<th width="3%">&nbsp;</th>
											<?php endif;?>
										</tr>
									</thead>
									<?php foreach($wc_product_list as $p_val):?>
									<tr>
										<td><?php echo $p_val['ID']?></td>
										<td>
										<b><a href="<?php echo admin_url('post.php?action=edit&post=').$p_val['ID'] ?>" target="_blank"><?php _e( $p_val['name'], 'mw_wc_qbo_desk' );?></b>
										</a>					
										</td>
										<td><?php echo $p_val['sku']?></td>
										
										<td><?php echo $p_val['wc_product_type'];?></td>
										
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
													$selected_options_script.='jQuery(\'#map_product_'.$p_val['ID'].'\').val(\''.$p_val['quickbook_product_id'].'\');';												
												}
											}																		
											?>
											
											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_product_<?php echo $p_val['ID']?>" id="map_product_<?php echo $p_val['ID']?>">
												<?php echo $dd_options;?>
											</select>
											
										</td>
										<?php if(!empty($qbo_class_options_value)){ ?>
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="class_map_product_<?php echo $p_val['ID']?>" id="class_map_product_<?php echo $p_val['ID']?>">
												<?php echo $qbo_class_options;?>
											</select>
											<?php 
											if($p_val['class_id']!=''){
												$selected_options_script.='jQuery(\'#class_map_product_'.$p_val['ID'].'\').val(\''.$p_val['class_id'].'\');';
											}
											?>	
										</td>
										<?php } ?>
										
										<?php if($check_sh_paamc_hash):?>
										<td>									
											<select class="mw_wc_qbo_sync_select2_desk" name="aracc_map_product_<?php echo $p_val['ID']?>" id="aracc_map_product_<?php echo $p_val['ID']?>">
												<?php echo $qbo_ar_acc_options;?>
											</select>
											<?php 
											if($p_val['qb_ar_acc_id']!=''){
												$selected_options_script.='jQuery(\'#aracc_map_product_'.$p_val['ID'].'\').val(\''.$p_val['qb_ar_acc_id'].'\');';
											}
											?>
										</td>
										<?php endif;?>
										
										<?php if($check_sh_qbispplm_hash):?>
										<td>									
											<select class="mw_wc_qbo_sync_select2_desk" name="ivntst_map_product_<?php echo $p_val['ID']?>" id="ivntst_map_product_<?php echo $p_val['ID']?>">
												<?php echo $qbo_ivnt_site_options;?>
											</select>
											<?php 
											if($p_val['qb_ivnt_site']!=''){
												$selected_options_script.='jQuery(\'#ivntst_map_product_'.$p_val['ID'].'\').val(\''.$p_val['qb_ivnt_site'].'\');';
											}
											?>
										</td>
										<?php endif;?>
										
										<?php if($mw_wc_qbo_desk_skip_os_lid):?>
										<td>										
											<input <?php if($p_val['a_line_item_desc']){echo ' checked';}?> title="Allow WooCommerce Line Item Description for Order Sync" type="checkbox" name="allow_lid_product_<?php echo $p_val['ID']?>" id="allow_lid_product_<?php echo $p_val['ID']?>">										
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
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_product_desk', 'map_wc_qbo_product_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button class="waves-effect waves-light btn save-btn mw-qbo-sync-green">Save</button>
						</div>
					</div>
				</form>
				
				<br />
				<div class="col col-m">
				<h5><?php _e( 'Clear All Products Mappings', 'mw_wc_qbo_desk' );?></h5>
				<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_mappings_products_desk', 'clear_all_mappings_products_desk' ); ?>
				<button id="mwqs_capm_btn"><?php _e( 'Clear Mappings', 'mw_wc_qbo_desk' );?></button>
				&nbsp;
				<span id="mwqs_capm_msg"></span>
				</div>
				
				<?php else:?>
				<p class="mwqbd_mp_msg"><?php _e( 'No products found.', 'mw_wc_qbo_desk' );?></p>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function search_item(){		
		var product_map_search = jQuery('#product_map_search').val();
		product_map_search = jQuery.trim(product_map_search);
		
		var product_stock_srch = jQuery('#product_stock_srch').val();
		product_stock_srch = jQuery.trim(product_stock_srch);
		
		var product_cat_search = jQuery('#product_cat_search').val();
		product_cat_search = jQuery.trim(product_cat_search);
		
		var product_um_srch = jQuery('#product_um_srch').val();
		product_um_srch = jQuery.trim(product_um_srch);		
		
		if(product_map_search!='' || product_stock_srch!='' || product_cat_search!='' || product_um_srch!=''){
			window.location = '<?php echo $page_url;?>&product_map_search='+product_map_search+'&product_stock_srch='+product_stock_srch+'&product_cat_search='+product_cat_search+'&product_um_srch='+product_um_srch;
		}else{
			alert('<?php echo __('Please enter search keyword or select stock status or category or mapped/unmapped.','mw_wc_qbo_sync')?>');
		}
	}
	
	function reset_item(){		
		window.location = '<?php echo $page_url;?>&product_map_search=&product_stock_srch=&product_cat_search=&product_um_srch=';
	}
	<?php if($selected_options_script!=''):?>
	jQuery(document).ready(function(){
		<?php echo $selected_options_script;?>
	});
	<?php endif;?>
	
	jQuery(document).ready(function($){
		
		$('#mwqs_automap_products_wf_qf').click(function(){
			var pam_wf = $('#pam_wf').val().trim();
			var pam_qf = $('#pam_qf').val().trim();
			
			var mo_um = '';
			if($('#pam_moum_chk').is(':checked')){
				mo_um = 'true';
			}
			
			if(pam_wf!='' && pam_qf!=''){
				$('#pam_wqf_e_msg').html('');
				if(confirm('<?php echo __('This will override any previous product mappings, and scan your WooCommerce & QuickBooks Desktop products by selected fields to automatically match them for you.')?>')){
					var data = {
						"action": 'mw_wc_qbo_sync_automap_products_desk_wf_qf',
						"automap_products_desk_wf_qf": jQuery('#automap_products_desk_wf_qf').val(),
						"pam_wf": pam_wf,
						"pam_qf": pam_qf,
						"mo_um": mo_um,
					};
					
					var loading_msg = 'Loading...';
					jQuery('#mwqs_automap_products_msg').html(loading_msg);
					
					jQuery.ajax({
					   type: "POST",
					   url: ajaxurl,
					   data: data,
					   cache:  false ,
					   //datatype: "json",
					   success: function(result){
						   if(result!=0 && result!=''){							
							jQuery('#mwqs_automap_products_msg').html(result);							
							window.location='<?php echo admin_url($page_url)?>';
						   }else{
							 jQuery('#mwqs_automap_products_msg').html('Error!');							
						   }				  
					   },
					   error: function(result) {							
							jQuery('#mwqs_automap_products_msg').html('Error!');
					   }
					});
					
				}
			}else{				
				$('#pam_wqf_e_msg').html('Please select automap fields.');
			}
		});
		
		<?php if($js_section=false):?>
		$('#mwqs_automap_products').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all products?')?>')){
				jQuery('#mwqs_automap_products_msg').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_products_desk',
					"automap_products_desk": jQuery('#automap_products_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_products_msg').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_products_msg').html('Success');
						jQuery('#mwqs_automap_products_msg').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_products_msg').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_products_msg').html('Error!');
				   }
				});
			}
		});
		
		$('#mwqs_automap_products_by_name').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all products by name?')?>')){
				jQuery('#mwqs_automap_products_msg_by_name').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_products_by_name_desk',
					"automap_products_by_name_desk": jQuery('#automap_products_by_name_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_products_msg_by_name').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_products_msg_by_name').html('Success');
						jQuery('#mwqs_automap_products_msg_by_name').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_products_msg_by_name').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_products_msg_by_name').html('Error!');
				   }
				});
			}
		});
		
		<?php endif;?>
		
		$('#mwqs_capm_btn').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to clear all product mappings?','mw_wc_qbo_sync')?>')){
				var loading_msg = 'Loading...';
				jQuery('#mwqs_capm_msg').html(loading_msg);
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_mappings_products_desk',
					"clear_all_mappings_products_desk": jQuery('#clear_all_mappings_products_desk').val(),
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
<?php //echo $MWQDC_LB->get_select2_js('#product_cat_search');?>