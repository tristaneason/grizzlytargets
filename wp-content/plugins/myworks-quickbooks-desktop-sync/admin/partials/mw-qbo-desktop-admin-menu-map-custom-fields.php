<?php
if ( ! defined( 'ABSPATH' ) )
exit;

global $MWQDC_LB;
global $wpdb;

$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=custom-fields';
$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_wq_cf';

if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_cf_desk', 'map_wc_qbo_cf_desk' ) ) {
	
	//$MWQDC_LB->_p($_POST);
	//die;
	$wpdb->query("DELETE FROM `".$table."` WHERE `id` > 0 ");
	$wpdb->query("TRUNCATE TABLE `".$table."` ");
	
	if(isset($_POST['wq_mcf_wcf']) && is_array($_POST['wq_mcf_wcf']) && isset($_POST['wq_mcf_qcf']) && is_array($_POST['wq_mcf_qcf'])){
		$wq_mcf_wcf = array_map('trim',$_POST['wq_mcf_wcf']);
		$wq_mcf_wcf_tf = (isset($_POST['wq_mcf_wcf_tf']))?array_map('trim',$_POST['wq_mcf_wcf_tf']):array();
		$wq_mcf_wcf_tf_qbd = (isset($_POST['wq_mcf_wcf_tf_qbd']))?array_map('trim',$_POST['wq_mcf_wcf_tf_qbd']):array();
		if(array_filter($wq_mcf_wcf)) {
			$wq_mcf_qcf = array_map('trim',$_POST['wq_mcf_qcf']);
			
			$values = array();
			$place_holders = array();
			$query = "INSERT INTO `{$table}` (wc_field, qb_field) VALUES ";
			
			for($i = 0; $i < count($wq_mcf_wcf); $i++){
				if($wq_mcf_wcf[$i]!='' && isset($wq_mcf_qcf[$i]) && $wq_mcf_qcf[$i]!=''){
					/*
					if($wq_mcf_wcf[$i]=='mcf_wc_oth_cus_field_manual_add'){
						if(isset($wq_mcf_wcf_tf[$i]) && !empty($wq_mcf_wcf_tf[$i]) && $wq_mcf_wcf_tf[$i]!='mcf_wc_oth_cus_field_manual_add'){
							array_push($values, esc_sql($wq_mcf_wcf_tf[$i]), esc_sql($wq_mcf_qcf[$i]));
						}						
					}else{
						array_push($values, esc_sql($wq_mcf_wcf[$i]), esc_sql($wq_mcf_qcf[$i]));
					}
					
					$place_holders[] = "('%s', '%s')";
					*/
					$p_wc_field_list = array();
					if($wq_mcf_wcf[$i]=='mcf_wc_oth_cus_field_manual_add'){
						if(isset($wq_mcf_wcf_tf[$i]) && !empty($wq_mcf_wcf_tf[$i]) && $wq_mcf_wcf_tf[$i]!='mcf_wc_oth_cus_field_manual_add'){
							$p_wc_field_list = $wq_mcf_wcf_tf;
						}
					}else{
						$p_wc_field_list = $wq_mcf_wcf;
					}
					
					$p_qb_field_list = array();
					if($wq_mcf_qcf[$i]=='mcf_qbd_oth_cus_field_manual_add'){						
						if(isset($wq_mcf_wcf_tf_qbd[$i]) && !empty($wq_mcf_wcf_tf_qbd[$i]) && $wq_mcf_wcf_tf_qbd[$i]!='mcf_qbd_oth_cus_field_manual_add'){
							$p_qb_field_list = $wq_mcf_wcf_tf_qbd;							
						}
					}else{
						$p_qb_field_list = $wq_mcf_qcf;
					}
					
					if(is_array($p_wc_field_list) && count($p_wc_field_list) && is_array($p_qb_field_list) && count($p_qb_field_list)){
						if(count($p_wc_field_list) == count($p_qb_field_list)){
							array_push($values, esc_sql($p_wc_field_list[$i]), esc_sql($p_qb_field_list[$i]));
							$place_holders[] = "('%s', '%s')";
						}
					}					
					
				}				
			}
			$query .= implode(', ', $place_holders);
			//$MWQDC_LB->_p($place_holders);
			//$MWQDC_LB->_p($values);
			
			if(count($values)){
				$query = $wpdb->prepare("$query ", $values);
				//echo $query;
				$wpdb->query($query);
			}			
		}
	}
	
	$MWQDC_LB->set_session_val('map_page_update_message',__('Custom fields mapped successfully.','mw_wc_qbo_desk'));
	$MWQDC_LB->redirect($page_url);
}

$wc_avl_cf_list = $MWQDC_LB->get_wc_avl_cf_map_fields();
$qbo_avl_cf_list = $MWQDC_LB->get_qbo_avl_cf_map_fields();
//$MWQDC_LB->_p($wc_avl_cf_list);
//$MWQDC_LB->_p($qbo_avl_cf_list);

$wc_avl_cf_list_by_group = $MWQDC_LB->get_wc_avl_cf_map_fields_by_group();
//$MWQDC_LB->_p($wc_avl_cf_list_by_group);

$cf_map_data = $MWQDC_LB->get_tbl($table);
//$MWQDC_LB->_p($cf_map_data);
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php' ?>
<style type="text/css">
	select.mcf_select{float:none;width:220px;}
	optgroup[label="Billing"] {
        /*background: #FFFFFF;*/
		font-size: 12px;
    }
</style>
<div class="container map-tax-class-outer">
	<div class="page_title"><h4><?php _e( 'Custom Fields Mappings', 'mw_wc_qbo_desk' );?></h4></div>
	<div class="card">
		<div class="card-content">
			<div class="row mcf_cont">
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-map-table menu-blue-bg" width="100%">
									<thead>
										<tr>									
											<th width="45%" class="title-description">
												WooCommerce Order Field
											</th>
											<th width="5%" >&nbsp;</th>
											<th width="45%" class="title-description">
												QuickBooks Order Field
											</th>
											<th width="5%" >&nbsp;</th>
										</tr>
									</thead>
									<tbody id="wq_mcf_tb">
										<?php if(is_array($cf_map_data) && count($cf_map_data)):?>
										<?php foreach($cf_map_data as $cfm_data):?>
										<tr>
											<td>
												<?php if(is_array($wc_avl_cf_list) && count($wc_avl_cf_list) && array_key_exists($cfm_data['wc_field'],$wc_avl_cf_list)):?>
													<select class="mcf_select" name="wq_mcf_wcf[]">
														<?php //echo $MWQDC_LB->only_option($cfm_data['wc_field'],$wc_avl_cf_list);?>
														<?php
															if(is_array($wc_avl_cf_list_by_group) && count($wc_avl_cf_list_by_group)){
																foreach($wc_avl_cf_list_by_group as $waclbg){
																	$og_s = (isset($waclbg['sub']))?'style="color:gray;"':'';
																	
																	echo '<optgroup '.$og_s.' label="'.$waclbg['title'].'">';
																	if(isset($waclbg['fields']) && is_array($waclbg['fields']) && count($waclbg['fields'])){
																		foreach($waclbg['fields'] as $wcf_k => $wcf_v){
																			$selected = ($cfm_data['wc_field']==$wcf_k)?'selected':'';
																			echo '<option '.$selected.' value="'.$wcf_k.'">'.$wcf_v.'</option>';
																		}
																	}
																}
															}
														?>
													</select>
												<?php else:?>
													<input type="text" value="<?php echo $cfm_data['wc_field'];?>" class="mcf_txt" name="wq_mcf_wcf[]"/>
												<?php endif;?>
												<input type="hidden" class="mcf_txt" name="wq_mcf_wcf_tf[]"/>
											</td>
											<td></td>										
											<td>											
												<?php if(is_array($qbo_avl_cf_list) && count($qbo_avl_cf_list) && array_key_exists($cfm_data['qb_field'],$qbo_avl_cf_list)):?>
													<select class="mcf_select" name="wq_mcf_qcf[]">
														<?php echo $MWQDC_LB->only_option($cfm_data['qb_field'],$qbo_avl_cf_list);?>
													</select>
												<?php else:?>
													<input type="text" value="<?php echo $cfm_data['qb_field'];?>" class="mcf_txt" name="wq_mcf_qcf[]"/>
												<?php endif;?>
												<input type="hidden" class="mcf_txt" name="wq_mcf_wcf_tf_qbd[]"/>
											</td>
											<td><a href="#" class="remove_field">Remove</a></td>
										</tr>
										<?php endforeach;?>
										<?php endif;?>
									</tbody>
								</table>
							</div>
							<div style="padding:10px 0px 0px 20px;">
								<a data-cft="order" class="wq_mcf_afb" href="javascript:void(0)">Add Fields</a>
							</div>							
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_cf_desk', 'map_wc_qbo_cf_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button id="mcf_sb" class="waves-effect waves-light btn save-btn mw-qbo-sync-green" disabled="disabled">Save</button>
						</div>
					</div>
				</form>
				
				<div id="wq_mcf_clone_fields" style="display:none;">
			
						<table>
						<tr>
							<td>
								<select class="mcf_select mcfws_nf" name="wq_mcf_wcf[]">
									<option value=""></option>
									<?php //echo $MWQDC_LB->only_option('',$wc_avl_cf_list);?>
									<?php
										if(is_array($wc_avl_cf_list_by_group) && count($wc_avl_cf_list_by_group)){
											foreach($wc_avl_cf_list_by_group as $waclbg){
												$og_s = (isset($waclbg['sub']))?'style="color:gray;"':'';
												
												echo '<optgroup '.$og_s.' label="'.$waclbg['title'].'">';
												if(isset($waclbg['fields']) && is_array($waclbg['fields']) && count($waclbg['fields'])){
													foreach($waclbg['fields'] as $wcf_k => $wcf_v){													
														echo '<option value="'.$wcf_k.'">'.$wcf_v.'</option>';
													}
												}
											}
										}
									?>
									<option value="mcf_wc_oth_cus_field_manual_add">Others(Add manually)</option>
								</select>
								&nbsp;
								<input type="hidden" class="mcf_txt wmwt_cl" name="wq_mcf_wcf_tf[]"/>
							</td>
							<td></td>
							<td>
								<select class="mcf_select mcfws_nf_qbd" name="wq_mcf_qcf[]">
									<option value=""></option>
									<?php echo $MWQDC_LB->only_option('',$qbo_avl_cf_list);?>
									<option value="mcf_qbd_oth_cus_field_manual_add">Custom Field</option>
								</select>
								&nbsp;
								<input type="hidden" class="mcf_txt wmwt_cl_qbd" name="wq_mcf_wcf_tf_qbd[]"/>
							</td>
							<td><a href="#" class="remove_field">Remove</a></td>
						</tr>
						</table>
				</div>
				
			</div>
		</div>
	</div>
</div>
<script>
	jQuery(document).ready(function($){
		var max_fields = 1000;
		var x = <?php echo (int) count($cf_map_data);?>;
		
		if(x>0){
			jQuery('#mcf_sb').removeAttr('disabled');
		}
		jQuery('.wq_mcf_afb').click(function(e){
			e.preventDefault();
			var cft = jQuery(this).data('cft');
			jQuery('#mcf_sb').removeAttr('disabled');
			if(x < max_fields){
				x++;
				/*
				$("#wq_mcf_tb").append('<tr><td><input type="text" class="mcf_txt" name="wq_mcf_wcf[]"/></td><td></td><td><input type="text" class="mcf_txt"  name="wq_mcf_qcf[]"/></td><td><a href="#" class="remove_field">Remove</a></td></tr>');
				*/
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
			//if(x==1){jQuery('#mcf_sb').attr('disabled','disabled');}
			x--;
		})
		
		$(document).on('change', '.mcfws_nf', function() {	
			if($(this).val()=='mcf_wc_oth_cus_field_manual_add'){
				$(this).next('input.wmwt_cl').attr('type','text');				
			}else{
				$(this).next('input.wmwt_cl').val('');
				$(this).next('input.wmwt_cl').attr('type','hidden');
			}
		});		
		
		$(document).on('change', '.mcfws_nf_qbd', function() {	
			if($(this).val()=='mcf_qbd_oth_cus_field_manual_add'){
				$(this).next('input.wmwt_cl_qbd').attr('type','text');			
			}else{
				$(this).next('input.wmwt_cl_qbd').val('');
				$(this).next('input.wmwt_cl_qbd').attr('type','hidden');
			}
		});
	});
</script>