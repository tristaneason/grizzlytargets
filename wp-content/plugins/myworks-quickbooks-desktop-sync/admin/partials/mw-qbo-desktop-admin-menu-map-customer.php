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
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=customer';
	
	/*Save Mapping*/
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_customer_desk', 'map_wc_qbo_customer_desk' ) ) {
		//$MWQDC_LB->_p($_POST);
		$item_ids = array();
		foreach ($_POST as $key=>$value){
			if ($MWQDC_LB->start_with($key, "map_client_")){
				$id = (int) str_replace("map_client_", "", $key);
				if($id){ //&& (int) $value
					$item_ids[$id] = $value;
				}
			}
		}
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();			
				$save_data['qbd_customerid'] = $value;
				
				$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers_pairs';
				if($MWQDC_LB->get_field_by_val($table,'id','wc_customerid',$key)){
					$wpdb->update($table,$save_data,array('wc_customerid'=>$key),'',array('%d'));
				}else{
					$save_data['wc_customerid'] = $key;
					$wpdb->insert($table, $save_data);
				}
			}
			//$MWQDC_LB->set_session_msg('map_client_msg',__('Customers mapped successfully.','mw_wc_qbo_desk'));		
			$MWQDC_LB->set_session_val('map_page_update_message',__('Customers mapped successfully.','mw_wc_qbo_desk'));
		}
		
		$wpdb->query("DELETE FROM `".$table."` WHERE `qbd_customerid` = '' ");
		$MWQDC_LB->redirect($page_url);
		//$MWQDC_LB->_p($item_ids);
	}
	
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page();

	$MWQDC_LB->set_and_get('cl_map_search');
	$cl_map_search = $MWQDC_LB->get_session_val('cl_map_search');
	
	$MWQDC_LB->set_and_get('cl_role_srch');
	$cl_role_srch = $MWQDC_LB->get_session_val('cl_role_srch');
	
	$MWQDC_LB->set_and_get('cl_um_srch');
	$cl_um_srch = $MWQDC_LB->get_session_val('cl_um_srch');
	
	$total_records = $MWQDC_LB->count_customers($cl_map_search,false,$cl_role_srch,$cl_um_srch);

	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);
	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);
	
	$cl_map_data = $MWQDC_LB->get_customers($cl_map_search," $offset , $items_per_page",false,$cl_role_srch,$cl_um_srch);
	$qbo_customer_options = '';
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
		$cdd_sb = 'd_name';
		/*
		$mw_wc_qbo_sync_client_sort_order = $MWQDC_LB->sanitize($MWQDC_LB->get_option('mw_wc_qbo_desk_client_sort_order'));
		if($mw_wc_qbo_sync_client_sort_order!=''){
			$cdd_sb = $mw_wc_qbo_sync_client_sort_order;
			if($cdd_sb!='d_name' && $cdd_sb!='first_name' && $cdd_sb!='last_name' && $cdd_sb!='company'){
				$cdd_sb = 'd_name';
			}
		}
		*/
		
		$cdn_fn = 'd_name';
		$mw_wc_qbo_desk_qb_cus_view_name = $MWQDC_LB->sanitize($MWQDC_LB->get_option('mw_wc_qbo_desk_qb_cus_view_name'));
		if($mw_wc_qbo_desk_qb_cus_view_name!=''){
			$cdn_fn = $mw_wc_qbo_desk_qb_cus_view_name;
			if($cdn_fn == 'first_name_last_name'){
				$cdd_sb = $cdn_fn;
				$cdn_fn = 'CONCAT(first_name, " ", last_name) AS '.$cdn_fn;
			}
			
			if($cdd_sb!='d_name' && $cdd_sb!='first_name_last_name' && $cdd_sb!='fullname' && $cdd_sb!='company'){
				$cdn_fn = 'd_name';
			}
		}
		
		if(empty($cdd_sb)){
			$cdd_sb = $cdn_fn;
		}
		
		$qbo_customer_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_customers','qbd_customerid',$cdn_fn,'',$cdd_sb.' ASC','',true);
	}

	$selected_options_script = '';
	//$MWQDC_LB->_p($cl_map_data);
	
	$wc_nf = $MWQDC_LB->get_option('mw_wc_qbo_desk_wc_cus_view_name');
	if($wc_nf!='first_name_last_name' && $wc_nf!='billing_company' && $wc_nf!='display_name'){ //&& $wc_nf!='first_name' && $wc_nf!='last_name' 
		$wc_nf = 'display_name';
	}
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>
<div class="container map-customer-outer cnt-mwqbd">
	<div class="page_title"><h4><?php _e( 'Customer Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	
	<div class="mw_wc_filter mwqbd_fl">
		 <span class="search_text">Search</span>
		  &nbsp;
		  <input type="text" id="cl_map_search" value="<?php echo $cl_map_search;?>">
		  &nbsp;
		 <span class="search_text">Role</span>
		 &nbsp;
		 <select style="width:130px;" name="cl_role_srch" id="cl_role_srch">
			<?php if(empty($cl_role_srch)):?>
			<option value="">All</option>
			<?php endif;?>
			<?php 
				foreach (get_editable_roles() as $role_name => $role_info):				
				$selected = '';
				if($cl_role_srch != ''){
					if( $role_name == $cl_role_srch ){
						$selected = 'selected="selected"';
					}
				}
				echo '<option value="'.$role_name.'" '.$selected.'>'.$role_info['name'].'</option>';
				endforeach;
			?>
			
		 </select>
		 
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
	 
	 <div class="card">
		<div class="card-content">
			<div class="row">
				<form method="POST" class="col s12 m12 l12">
					<div class="row">
						<div class="col s12 m12 l12">
						<div class="myworks-wc-qbd-sync-table-responsive">
							<table class="mw-qbo-sync-settings-table menu-blue-bg menu-bg-a new-table">
								<thead>
									<tr>
										<th width="5%">&nbsp; ID</th>
										<th width="23%">WooCommerce Customer Name</th>
										<th width="23%">Email</th>
										<th width="23%">Company</th>
										<th width="25%" class="title-description">
											QuickBooks Customer								    	
										</th>
									</tr>
								</thead>
								<tbody>                					
									<?php if(count($cl_map_data)):?>
									<?php foreach($cl_map_data as $data):?>
									<tr>
										<td><?php echo $data['ID']?></td>
											<td>
												<a href="<?php echo admin_url('user-edit.php?user_id=').$data['ID'] ?>" target="_blank">
											<?php 
											$fn_lm =  $data['first_name']. ' '. $data['last_name'];
											if($wc_nf == 'first_name_last_name'){
												echo $fn_lm;
											}else{
												echo (!empty($wc_nf) && isset($data[$wc_nf]) && !empty($data[$wc_nf]))?$data[$wc_nf]:$fn_lm;
											}											
											?>
										</a>									
											</td>
										<td><?php echo $data['user_email']?></td>
										<td><?php echo $data['billing_company']?></td>									
										<td>											
											<?php
											$dd_options = '<option value=""></option>';
											$dd_ext_class = '';
											if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
												$dd_ext_class = 'mwqs_dynamic_select_desk';
												if($data['qbd_customerid']!=''){
													$dd_options = '<option value="'.$data['qbd_customerid'].'">'.$data['qbo_dname'].'</option>';
												}
											}else{
												$dd_options.=$qbo_customer_options;
												if($data['qbd_customerid']!=''){
													$selected_options_script.='jQuery(\'#map_client_'.$data['ID'].'\').val(\''.$data['qbd_customerid'].'\');';
												}
											}										
											?>

											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_client_<?php echo $data['ID']?>" id="map_client_<?php echo $data['ID']?>">
												<?php echo $dd_options;?>
											</select>
										
										</td>
										
									</tr>
									<?php endforeach;?>
									<?php endif;?>
            				    </tbody>
							</table>
							</div>
							<?php echo $pagination_links?>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_customer_desk', 'map_wc_qbo_customer_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button class="waves-effect waves-light btn save-btn mw-qbo-sync-green">Save</button>
						</div>
					</div>
				</form>
				
				<br />
				<div class="col col-m">
				<h5><?php _e( 'Clear All Customers Mappings', 'mw_wc_qbo_desk' );?></h5>
				<?php wp_nonce_field( 'myworks_wc_qbo_sync_clear_all_mappings_customers_desk', 'clear_all_mappings_customers_desk' ); ?>
				<button id="mwqs_cacm_btn"><?php _e( 'Clear Mappings', 'mw_wc_qbo_desk' );?></button>
				&nbsp;
				<span id="mwqs_cacm_msg"></span>
				</div>
				
			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
	function search_item(){		
		var cl_map_search = jQuery('#cl_map_search').val();
		cl_map_search = jQuery.trim(cl_map_search);
		
		var cl_role_srch = jQuery('#cl_role_srch').val();
		cl_role_srch = jQuery.trim(cl_role_srch);
		
		var cl_um_srch = jQuery('#cl_um_srch').val();
		cl_um_srch = jQuery.trim(cl_um_srch);
		
		
		if(cl_map_search!='' || cl_role_srch!='' || cl_um_srch!=''){
			window.location = '<?php echo $page_url;?>&cl_map_search='+cl_map_search+'&cl_role_srch='+cl_role_srch+'&cl_um_srch='+cl_um_srch;
		}else{
			alert('<?php echo __('Please enter search keyword or select user role or mapped/unmapped.','mw_wc_qbo_desk')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&cl_map_search=&cl_role_srch=&cl_um_srch=';
	}
	<?php if($selected_options_script!=''):?>
	jQuery(document).ready(function(){
		<?php echo $selected_options_script;?>
	});
	<?php endif;?>
	
	jQuery(document).ready(function($){
		
		$('#mwqs_automap_customers_wf_qf').click(function(){
			var cam_wf = $('#cam_wf').val().trim();
			var cam_qf = $('#cam_qf').val().trim();
			
			var mo_um = '';
			if($('#cam_moum_chk').is(':checked')){
				mo_um = 'true';
			}
			
			if(cam_wf!='' && cam_qf!=''){
				$('#cam_wqf_e_msg').html('');
				if(confirm('<?php echo __('This will override any previous customer mappings, and scan your WooCommerce & QuickBooks Desktop customers by selected fields to automatically match them for you.')?>')){
					var data = {
						"action": 'mw_wc_qbo_sync_automap_customers_desk_wf_qf',
						"automap_customers_desk_wf_qf": jQuery('#automap_customers_desk_wf_qf').val(),
						"cam_wf": cam_wf,
						"cam_qf": cam_qf,
						"mo_um": mo_um,
					};
					
					var loading_msg = 'Loading...';
					jQuery('#mwqs_automap_customers_msg').html(loading_msg);
					
					jQuery.ajax({
					   type: "POST",
					   url: ajaxurl,
					   data: data,
					   cache:  false ,
					   //datatype: "json",
					   success: function(result){
						   if(result!=0 && result!=''){							
							jQuery('#mwqs_automap_customers_msg').html(result);							
							window.location='<?php echo admin_url($page_url)?>';
						   }else{
							 jQuery('#mwqs_automap_customers_msg').html('Error!');							
						   }				  
					   },
					   error: function(result) {							
							jQuery('#mwqs_automap_customers_msg').html('Error!');
					   }
					});
					
				}
			}else{				
				$('#cam_wqf_e_msg').html('Please select automap fields.');
			}
		});
		
		<?php if($js_section=false):?>
		$('#mwqs_automap_customers').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all customers?')?>')){
				jQuery('#mwqs_automap_customers_msg').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_customers_desk',
					"automap_customers_desk": jQuery('#automap_customers_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_customers_msg').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_customers_msg').html('Success');
						jQuery('#mwqs_automap_customers_msg').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_customers_msg').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_customers_msg').html('Error!');
				   }
				});
			}
		});
		
		$('#mwqs_automap_customers_by_name').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to automap all customers by display name?')?>')){
				jQuery('#mwqs_automap_customers_msg_by_name').html('');
				var data = {
					"action": 'mw_wc_qbo_sync_automap_customers_by_name_desk',
					"automap_customers_by_name_desk": jQuery('#automap_customers_by_name_desk').val(),
				};
				var loading_msg = 'Loading...';
				jQuery('#mwqs_automap_customers_msg_by_name').html(loading_msg);
				jQuery.ajax({
				   type: "POST",
				   url: ajaxurl,
				   data: data,
				   cache:  false ,
				   //datatype: "json",
				   success: function(result){
					   if(result!=0 && result!=''){
						//alert(result);
						//jQuery('#mwqs_automap_customers_msg_by_name').html('Success');
						jQuery('#mwqs_automap_customers_msg_by_name').html(result);
						//alert('Success!');
						//location.reload();
						window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 jQuery('#mwqs_automap_customers_msg_by_name').html('Error!');
						 //alert('Error!');			 
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_automap_customers_msg_by_name').html('Error!');
				   }
				});
			}
		});
		
		<?php endif;?>
		
		$('#mwqs_cacm_btn').click(function(){
			if(confirm('<?php echo __('Are you sure, you want to clear all customer mappings?','mw_wc_qbo_desk')?>')){
				var loading_msg = 'Loading...';
				jQuery('#mwqs_cacm_msg').html(loading_msg);
				var data = {
					"action": 'mw_wc_qbo_sync_clear_all_mappings_customers_desk',
					"clear_all_mappings_customers_desk": jQuery('#clear_all_mappings_customers_desk').val(),
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
						 jQuery('#mwqs_cacm_msg').html('Success!');
						 window.location='<?php echo admin_url($page_url)?>';
					   }else{
						 //alert('Error!');
						jQuery('#mwqs_cacm_msg').html('Error!');
					   }				  
				   },
				   error: function(result) {  
						//alert('Error!');
						jQuery('#mwqs_cacm_msg').html('Error!');
				   }
				});
			}
		});
		
	});
 </script>
 <?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_sync_select2_desk','qbo_customer');?>
