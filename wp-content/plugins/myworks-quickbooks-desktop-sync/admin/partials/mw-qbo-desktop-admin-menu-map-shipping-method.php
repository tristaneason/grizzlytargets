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
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=shipping-method';
	
	$wc_sh_methods = WC()->shipping->load_shipping_methods();
	//$MWQDC_LB->_p($wc_sh_methods);
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_shipping_method_desk', 'map_wc_qbo_shipping_method_desk' ) ) {
		//$MWQDC_LB->_p($_POST);die;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_shipping_product';
		$wpdb->query($wpdb->prepare("DELETE FROM `$table` WHERE `id` > %d",0));
		$wpdb->query("TRUNCATE table {$table}");
		
		$item_ids = array();
		foreach ($_POST as $key=>$value){
			if ($MWQDC_LB->start_with($key, "map_shipping_method_")){
				$id = str_replace("map_shipping_method_", "", $key);
				$id = trim($id);
				if($id!='' && is_array($wc_sh_methods) && isset($wc_sh_methods[$id])){
					$item_ids[$id] = $value;
				}
				
				/**/
				$is_wf_multi_carrier_shipping = false;
				$smd_mc = array();				
				if(strpos( $id, 'wf_multi_carrier_shipping:' ) !== false){
					$is_wf_multi_carrier_shipping = true;
					$smd_mc = $MWQDC_LB->get_smd_s_opts('wf_multi_carrier_shipping');
				}
				
				/**/
				$is_ups_instance = false;
				if(strpos( $id, 'wf_shipping_ups:' ) !== false){
					$is_ups_instance = true;
				}				
				
				//New
				if(strpos( $id, ':' ) !== false && !$is_wf_multi_carrier_shipping && !$is_ups_instance){
					$sm = $MWQDC_LB->wc_get_sm_data_from_method_id_str($id);
					$id_int = (int) $MWQDC_LB->wc_get_sm_data_from_method_id_str($id,'id');
					$sm = trim($sm);
					if($id_int>0 && $sm!=''){
						$smd = $MWQDC_LB->get_wc_smt_data_from_sm_and_id($sm,$id_int);						
						if(is_array($smd) && count($smd)){
							$item_ids[$id] = $value;
						}
					}
				}
				
				/**/
				if($is_ups_instance){
					$uii = str_replace("wf_shipping_ups:", "", $id);
					if((int) $uii > 0){
						$ups_il_arr = $MWQDC_LB->get_ups_sm_instance_list();
						if(is_array($ups_il_arr) && isset($ups_il_arr[$uii])){
							$item_ids[$id] = $value;
						}
					}					
				}
				
				/**/
				if($is_wf_multi_carrier_shipping){
					if(is_array($smd_mc) && !empty($smd_mc)){
						foreach($smd_mc as $mci){
							if(is_array($mci) && isset($mci['sm_id']) && (int) $mci['sm_id'] > 0){
								if(str_replace('wf_multi_carrier_shipping:','',$id) == $mci['sm_id']){
									$item_ids[$id] = $value;
								}
							}
						}
					}					
				}
			}
		}
		
		//$MWQDC_LB->_p($item_ids);die;
		//$item_ids = array();
		
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();			
				$save_data['qbo_product_id'] = $value;
				$save_data['class_id'] = (isset($_POST['class_map_shipping_method_'.$key]))?$_POST['class_map_shipping_method_'.$key]:'';
				
				$save_data['qb_shipmethod_id'] = (isset($_POST['qbdsm_map_shipping_method_'.$key]))?$_POST['qbdsm_map_shipping_method_'.$key]:'';
				
				if($MWQDC_LB->get_field_by_val($table,'id','wc_shippingmethod',$key)){
					$wpdb->update($table,$save_data,array('wc_shippingmethod'=>$key),'',array('%s'));
				}else{
					$save_data['wc_shippingmethod'] = $key;
					$wpdb->insert($table, $save_data);
				}
			}
			$MWQDC_LB->set_session_val('map_page_update_message',__('Shipping methods mapped successfully.','mw_wc_qbo_desk'));
		}
		
		/**/
		$is_custom_map_post = false;
		if(isset($_POST['wq_mcf_wcf']) && is_array($_POST['wq_mcf_wcf']) && isset($_POST['wq_mcf_qbp']) && is_array($_POST['wq_mcf_qbp'])){
			$wq_mcf_wcf = array_map('trim',$_POST['wq_mcf_wcf']);
			if(array_filter($wq_mcf_wcf)) {
				$is_custom_map_post = true;
				$wq_mcf_qbp = array_map('trim',$_POST['wq_mcf_qbp']);
				
				$wq_mcf_qbc = (isset($_POST['wq_mcf_qbc']) && is_array($_POST['wq_mcf_qbc']))?$_POST['wq_mcf_qbc']:array();
				$wq_mcf_qbs = (isset($_POST['wq_mcf_qbs']) && is_array($_POST['wq_mcf_qbs']))?$_POST['wq_mcf_qbs']:array();
				
				$wq_mcf_qbc = array_map('trim',$wq_mcf_qbc);
				$wq_mcf_qbs = array_map('trim',$wq_mcf_qbs);
				
				$csma_new = array();
				for($i = 0; $i < count($wq_mcf_wcf); $i++){
					if($wq_mcf_wcf[$i]!='' && isset($wq_mcf_qbp[$i]) && $wq_mcf_qbp[$i]!=''){
						if(empty($MWQDC_LB->get_custom_shipping_map_data_from_name($wq_mcf_wcf[$i]))){
							$csma_new[] = array(
								'wc_shipping_name' => $wq_mcf_wcf[$i],
								'qb_product' => $wq_mcf_qbp[$i],
								'qb_class' => (isset($wq_mcf_qbc[$i]) && !empty($wq_mcf_qbc[$i]))?$wq_mcf_qbc[$i]:'',
								'qb_sv' => (isset($wq_mcf_qbs[$i]) && !empty($wq_mcf_qbs[$i]))?$wq_mcf_qbs[$i]:'',
							);
						}
					}
				}
				
				//$MWQDC_LB->_p($csma_new);die;
				update_option('mw_wc_qbo_desk_ed_cust_ship_map_val_arr',$csma_new);
				$MWQDC_LB->set_session_val('map_page_update_message',__('Shipping methods mapped successfully.','mw_wc_qbo_desk'));
			}
		}
		
		if(!$is_custom_map_post){
			delete_option('mw_wc_qbo_desk_ed_cust_ship_map_val_arr');
		}
		
		$MWQDC_LB->redirect($page_url);
	}
	
	$qbo_product_options = '';
	if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
		$qbo_product_options = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','name','','name ASC','',true);
	}
	
	$qbo_class_options_value = $MWQDC_LB->get_class_dropdown_list();
	$qbo_class_options = '<option value=""></option>';	
	$qbo_class_options.= ''.$qbo_class_options_value;
	
	$qbd_shipmethods_values = $MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_shipmethod','qbd_id','name','','name ASC','',true);
	$qbd_shipmethods_options = '<option value=""></option>';
	$qbd_shipmethods_options.= ''.$qbd_shipmethods_values;

	$selected_options_script = '';
	$sm_map_data = $MWQDC_LB->get_tbl($wpdb->prefix.'mw_wc_qbo_desk_qbd_map_shipping_product');
	$sm_map_data_kv_arr = array();
	if(is_array($sm_map_data) && count($sm_map_data)){
		foreach($sm_map_data as $sm_k=>$sm_val){
			$wc_shippingmethod = $sm_val['wc_shippingmethod'];
			$wc_shippingmethod = str_replace(':','_',$wc_shippingmethod);
			
			if(!$MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
				$selected_options_script.='jQuery(\'#map_shipping_method_'.$wc_shippingmethod.'\').val(\''.$sm_val['qbo_product_id'].'\');';
			}else{
				$sm_map_data_kv_arr[$sm_val['wc_shippingmethod']] = $sm_val['qbo_product_id'];
			}			
			$selected_options_script.='jQuery(\'#class_map_shipping_method_'.$wc_shippingmethod.'\').val(\''.$sm_val['class_id'].'\');';
			
			$selected_options_script.='jQuery(\'#qbdsm_map_shipping_method_'.$wc_shippingmethod.'\').val(\''.$sm_val['qb_shipmethod_id'].'\');';
		}	
	}
	
	$custom_shipping_map_arr = get_option('mw_wc_qbo_desk_ed_cust_ship_map_val_arr');
	$custom_shipping_map_count = is_array($custom_shipping_map_arr)?count($custom_shipping_map_arr):0;
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>
<div class="container map-shipping-method-outer">
	<div class="page_title"><h4><?php _e( 'Shipping Method Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">
			<div class="row">
			<?php if((is_array($wc_sh_methods) && count($wc_sh_methods)) || $MWQDC_LB->option_checked('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp')):?>
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-map-table menu-blue-bg" width="100%">	
	                            	<thead>				
	                                    <tr>
	                                        <th width="40%">
	                                            Woocommerce Shipping Method								    	
	                                        </th>
	                                        <th width="<?php echo !empty($qbo_class_options_value)?'20%':'30%' ?>">
	                                            Quickbooks Product								    	
	                                        </th>
											<?php if(!empty($qbo_class_options_value)):?>
	                                        <th width="20%">
	                                            Quickbooks Class
	                                        </th>
											<?php endif;?>
											
											<th width="<?php echo !empty($qbo_class_options_value)?'20%':'30%' ?>">
	                                            Quickbooks ShipVia
	                                        </th>
											
											<th width="1%" >&nbsp;</th>
	                                    </tr>
	                                 </thead>   
									<?php foreach($wc_sh_methods as $sm_key => $sm_val):?>
									<?php
										$smt_data = $MWQDC_LB->get_smd_s_opts($sm_key);
										//$MWQDC_LB->_p($sm_val->method_title);
										//$MWQDC_LB->_p($smt_data);
									?>
									<tr>
										<td>
										<b><?php echo $sm_val->method_title;?></b> (<?php echo $sm_key;?>)
										<p><?php echo stripslashes(strip_tags($sm_val->method_description));?></p>
										</td>
										<td>
											<?php
												$dd_options = '<option value=""></option>';
												$dd_ext_class = '';
												if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
													$dd_ext_class = 'mwqs_dynamic_select_desk';
													if(count($sm_map_data_kv_arr) && isset($sm_map_data_kv_arr[$sm_key]) && $sm_map_data_kv_arr[$sm_key]!=''){
														$itemid = $sm_map_data_kv_arr[$sm_key];
														$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
														if($qb_item_name!=''){
															$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
														}
													}
												}else{
													$dd_options.=$qbo_product_options;
												}
											?>
											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_shipping_method_<?php echo $sm_key?>" id="map_shipping_method_<?php echo $sm_key?>">
												<?php echo $dd_options;?>
											</select>
										</td>
										<?php if(!empty($qbo_class_options_value)):?>
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="class_map_shipping_method_<?php echo $sm_key?>" id="class_map_shipping_method_<?php echo $sm_key?>">
												<?php echo $qbo_class_options;?>
											</select>										
										</td>
										<?php endif;?>
										
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="qbdsm_map_shipping_method_<?php echo $sm_key?>" id="qbdsm_map_shipping_method_<?php echo $sm_key?>">
												<?php echo $qbd_shipmethods_options;?>
											</select>										
										</td>
										<td>&nbsp;</td>
									</tr>
									
									<?php if(is_array($smt_data) && count($smt_data)):?>
									<?php foreach($smt_data as $smt_v):?>
									<tr>
										<td>
											<div style="padding-left:2em;">
												<b><?php echo $smt_v['title'];?></b> (<?php echo $sm_key;?>)
											</div>
										</td>
										<td>
											<?php
												$dd_options = '<option value=""></option>';
												$dd_ext_class = '';
												if($MWQDC_LB->option_checked('mw_wc_qbo_desk_select2_ajax')){
													$dd_ext_class = 'mwqs_dynamic_select_desk';
													if(count($sm_map_data_kv_arr) && isset($sm_map_data_kv_arr[$sm_key.':'.$smt_v['sm_id']]) && $sm_map_data_kv_arr[$sm_key.':'.$smt_v['sm_id']]!=''){
														$itemid = $sm_map_data_kv_arr[$sm_key.':'.$smt_v['sm_id']];
														$qb_item_name = $MWQDC_LB->get_field_by_val($wpdb->prefix.'mw_wc_qbo_desk_qbd_items','name','qbd_id',$itemid);
														if($qb_item_name!=''){
															$dd_options = '<option value="'.$itemid.'">'.$qb_item_name.'</option>';
														}
													}
												}else{
													$dd_options.=$qbo_product_options;
												}
											?>
											<select class="mw_wc_qbo_sync_select2_desk <?php echo $dd_ext_class;?>" name="map_shipping_method_<?php echo $sm_key?>:<?php echo $smt_v['sm_id'];?>" id="map_shipping_method_<?php echo $sm_key?>_<?php echo $smt_v['sm_id'];?>">
												<?php echo $dd_options;?>
											</select>
										</td>
										<?php if(!empty($qbo_class_options_value)):?>
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="class_map_shipping_method_<?php echo $sm_key?>:<?php echo $smt_v['sm_id'];?>" id="class_map_shipping_method_<?php echo $sm_key?>_<?php echo $smt_v['sm_id'];?>">
												<?php echo $qbo_class_options;?>
											</select>										
										</td>
										<?php endif;?>
										
										<td>
											<select class="mw_wc_qbo_sync_select2_desk" name="qbdsm_map_shipping_method_<?php echo $sm_key?>:<?php echo $smt_v['sm_id'];?>" id="qbdsm_map_shipping_method_<?php echo $sm_key?>_<?php echo $smt_v['sm_id'];?>">
												<?php echo $qbd_shipmethods_options;?>
											</select>										
										</td>
										
										<td>&nbsp;</td>
									</tr>
									<?php endforeach;?>
									<?php endif;?>
									
									<?php endforeach;?>							
								</table>
								
								<!---->
								<?php if($MWQDC_LB->option_checked('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp')):?>
								<h4><?php _e( 'Custom Mappings', 'mw_wc_qbo_desk' );?></h4>
								<table class="mw-qbo-sync-map-table menu-blue-bg" width="100%">	
	                            	<thead>				
	                                    <tr>
	                                        <th width="39%">
	                                            Woocommerce Shipping Method	Text							    	
	                                        </th>
	                                        <th width="<?php echo !empty($qbo_class_options_value)?'20%':'30%' ?>">
	                                            Quickbooks Product								    	
	                                        </th>
											<?php if(!empty($qbo_class_options_value)):?>
	                                        <th width="20%">
	                                            Quickbooks Class
	                                        </th>
											<?php endif;?>
											
											<th width="<?php echo !empty($qbo_class_options_value)?'20%':'30%' ?>">
	                                            Quickbooks ShipVia
	                                        </th>
											
											<th width="1%" >&nbsp;</th>
	                                    </tr>
	                                </thead>
									
									<tbody id="wq_mcf_tb">
										<?php if(is_array($custom_shipping_map_arr) && count($custom_shipping_map_arr)):?>
										<?php foreach($custom_shipping_map_arr as $cfm_key => $cfm_data):?>
										<tr>
											<td><input type="text" name="wq_mcf_wcf[]" value="<?php echo $cfm_data['wc_shipping_name'];?>"></td>
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbp[]" id="wq_mcf_qbp_<?php echo $cfm_key;?>">
													<?php echo $qbo_product_options;?>
												</select>
												<?php $selected_options_script.='jQuery(\'#wq_mcf_qbp_'.$cfm_key.'\').val(\''.$cfm_data['qb_product'].'\');';?>
											</td>
											
											<?php if(!empty($qbo_class_options_value)):?>
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbc[]" id="wq_mcf_qbc_<?php echo $cfm_key;?>">
													<?php echo $qbo_class_options;?>
												</select>
												<?php $selected_options_script.='jQuery(\'#wq_mcf_qbc_'.$cfm_key.'\').val(\''.$cfm_data['qb_class'].'\');';?>
											</td>
											<?php endif;?>
											
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbs[]" id="wq_mcf_qbs_<?php echo $cfm_key;?>">
													<?php echo $qbd_shipmethods_options;?>
												</select>
												<?php $selected_options_script.='jQuery(\'#wq_mcf_qbs_'.$cfm_key.'\').val(\''.$cfm_data['qb_sv'].'\');';?>
											</td>
											<td><a href="#" class="remove_field">X</a></td>
										</tr>
										 
										<?php endforeach;?>
										<?php endif;?>
									</tbody>
								</table>
								
								<div style="padding:10px 0px 0px 20px;">
									<a data-cft="shipping" class="wq_mcf_afb" href="javascript:void(0)">Add Mappings</a>
								</div>
								
								<div id="wq_mcf_clone_fields" style="display:none;">
									<table>
										<tr>
											<td>
												<input type="text" name="wq_mcf_wcf[]" value="">
											</td>
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbp[]">
													<?php echo $qbo_product_options;?>
												</select>
											</td>
											
											<?php if(!empty($qbo_class_options_value)):?>
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbc[]">
													<?php echo $qbo_class_options;?>
												</select>
											</td>
											<?php endif;?>
											
											<td>
												<select class="mw_wc_qbo_sync_select2_desk mcf_select mcfws_nf" name="wq_mcf_qbs[]">
													<?php echo $qbd_shipmethods_options;?>
												</select>
											</td>
											
											<td><a href="#" class="remove_field">X</a></td>
											
										</tr>
									</table>
								</div>
								
								<?php endif;?>
								
							</div>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_shipping_method_desk', 'map_wc_qbo_shipping_method_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button id="mw_smm_sbtn" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" disabled>Save</button>
						</div>
					</div>
				</form>
			<?php else:?>
				<p><?php _e( 'No shipping method found.', 'mw_wc_qbo_desk' );?></p>
			<?php endif;?>
			</div>
			
		</div>
	</div>
</div>
<?php if($selected_options_script!=''):?>
<script type="text/javascript">
jQuery(document).ready(function(){
	<?php if($MWQDC_LB->option_checked('mw_wc_qbo_desk_ed_cust_ship_mpng_smmp')):?>
	var max_fields = 1000;
	var x = <?php echo (int) $custom_shipping_map_count;?>;
	
	if(x>0){
		jQuery('#mcf_sb').removeAttr('disabled');
	}
	jQuery('.wq_mcf_afb').click(function(e){
		e.preventDefault();
		var cft = jQuery(this).data('cft');
		jQuery('#mcf_sb').removeAttr('disabled');
		if(x < max_fields){
			x++;			
			var na_fields = $('#wq_mcf_clone_fields').html();
			na_fields = na_fields.replace('<table>','').replace('<tbody>','').replace('</tbody>','').replace('</table>','');
			na_fields = na_fields.trim();
			//alert(na_fields);
			$("#wq_mcf_tb").append(na_fields);
		}else{
			alert('Max '+max_fields+' allowed.')
		}
	});	
	
	$("#wq_mcf_tb").on("click",".remove_field", function(e){ //user click on remove text
		e.preventDefault();			
		$(this).parent('td').parent('tr').remove();		
		x--;
	})
	<?php endif;?>
	
	<?php echo $selected_options_script;?>
	
	jQuery('#mw_smm_sbtn').removeAttr('disabled');
});
</script>
<?php endif;?>
<?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_sync_select2_desk','qbo_product');?>