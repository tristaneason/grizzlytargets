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
	$page_url = 'admin.php?page=mw-qbo-desktop-map&tab=tax-class';
	
	if ( ! empty( $_POST ) && check_admin_referer( 'myworks_wc_qbo_sync_map_wc_qbo_tax_desk', 'map_wc_qbo_tax_desk' ) ) {
		$item_ids = array();
		$item_ids_combo = array();
		foreach ($_POST as $key=>$value){
			$value = trim($value);
			if ($MWQDC_LB->start_with($key, "wtax_")){
				$id = (int) str_replace("wtax_", "", $key);			
				if($id && $value!=''){ 
					$item_ids[$id] = $value;
				}
			}
			
			if ($MWQDC_LB->start_with($key, "cobmbo_wtax_")){
				$id = (int) str_replace("cobmbo_wtax_", "", $key);			
				if($id && $value!=''){
					$item_ids_combo[$id] = $value;
				}
			}	
		}
		//$MWQDC_LB->_p($item_ids);die;
		$is_tax_saved = false;
		$table = $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_tax';
		
		/*
		$wpdb->query("DELETE FROM `".$table."` WHERE `id` > 0 ");
		$wpdb->query("TRUNCATE TABLE `".$table."` ");
		*/
		
		if(count($item_ids)){
			foreach ($item_ids as $key=>$value){
				$save_data = array();
				$save_data['qbo_tax_code'] = $value;
				
				$eq = $wpdb->prepare("SELECT `id` FROM {$table} WHERE `wc_tax_id` = %d AND `wc_tax_id_2` = '0' AND qbo_tax_code != '' ",$key);
				$ed = $MWQDC_LB->get_row($eq);
				
				if(is_array($ed) && !empty($ed)){
					$wpdb->update($table,$save_data,array('id'=>$ed['id']),'',array('%d'));
				}else{
					$save_data['wc_tax_id'] = $key;
					$save_data['wc_tax_id_2'] = 0;
					$wpdb->insert($table, $save_data);
				}				
			}
			
			$is_tax_saved = true;		
		}
		if(count($item_ids_combo)){		
			foreach ($item_ids_combo as $key=>$value){
				$save_data = array();
				$save_data['qbo_tax_code'] = $value;
				
				$save_data['wc_tax_id_2'] = (isset($_POST['sc_wtax_'.$key]))?(int) $_POST['sc_wtax_'.$key]:0;
				
				if($save_data['wc_tax_id_2'] > 0){
					$eq = $wpdb->prepare("SELECT `id` FROM {$table} WHERE `wc_tax_id` = %d AND `wc_tax_id_2` = %s AND qbo_tax_code != '' ",$key,$save_data['wc_tax_id_2']);
					$ed = $MWQDC_LB->get_row($eq);
					
					if(is_array($ed) && !empty($ed)){
						unset($save_data['wc_tax_id_2']);
						$wpdb->update($table,$save_data,array('id'=>$ed['id']),'',array('%d'));
					}else{
						$save_data['wc_tax_id'] = $key;
						$wpdb->insert($table, $save_data);
					}
				}				
			}
			$is_tax_saved = true;
		}
		if($is_tax_saved){
			$MWQDC_LB->set_session_val('map_page_update_message',__('Tax rates mapped successfully.','mw_wc_qbo_desk'));
		}
		$MWQDC_LB->redirect($page_url);
	}
	
	$MWQDC_LB->set_per_page_from_url();
	$items_per_page = $MWQDC_LB->get_item_per_page('',10);

	$MWQDC_LB->set_and_get('tax_map_search');
	$tax_map_search = $MWQDC_LB->get_session_val('tax_map_search');
	
	//$wc_tax_classes = WC_Tax::get_tax_classes();
	//$wc_tax_rates = $MWQDC_LB->get_tbl($wpdb->prefix.'woocommerce_tax_rates','','','tax_rate_class ASC');
	
	$tax_map_search = $MWQDC_LB->sanitize($tax_map_search);
	$whr = '';
	if($tax_map_search!=''){
		$whr.=" AND (`tax_rate_name` LIKE '%$tax_map_search%' OR `tax_rate_class` LIKE '%$tax_map_search%' ) ";
		// OR `tax_rate_country` LIKE '%$tax_map_search%' OR `tax_rate_state` LIKE '%$tax_map_search%'
	}

	$total_records = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."woocommerce_tax_rates` WHERE `tax_rate_id` >0 {$whr} ");
	$offset = $MWQDC_LB->get_offset($MWQDC_LB->get_page_var(),$items_per_page);

	$pagination_links = $MWQDC_LB->get_paginate_links($total_records,$items_per_page);
	
	$tax_q = "SELECT * FROM `".$wpdb->prefix."woocommerce_tax_rates` WHERE `tax_rate_id` >0 {$whr} ORDER BY `tax_rate_class` ASC LIMIT {$offset} , {$items_per_page} ";
	$wc_tax_rates = $MWQDC_LB->get_data($tax_q);

	$qbo_tax_options = '<option value=""></option>';
	
	if($MWQDC_LB->get_option('mw_wc_qbo_desk_sl_tax_map_entity') == 'Sales_Tax_Codes'){
		$qbo_tax_options.=$MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salestaxcode','qbd_id','name','','name ASC','',true);
	}	
	
	if($MWQDC_LB->get_option('mw_wc_qbo_desk_sl_tax_map_entity') != 'Sales_Tax_Codes'){
		//$qbd_sales_tax_item_options = '<option value=""></option>';
		$sti_whr = "(product_type='SalesTax' OR product_type='SalesTaxGroup')";
		$qbo_tax_options.=$MWQDC_LB->option_html('', $wpdb->prefix.'mw_wc_qbo_desk_qbd_items','qbd_id','name',$sti_whr,'name ASC','',true);	
	}	
	
	$selected_options_script = '';
	$wc_all_tax_rates = $MWQDC_LB->get_wc_tax_rate_id_array($wc_tax_rates);
	$tm_map_data = $MWQDC_LB->get_tbl($wpdb->prefix.'mw_wc_qbo_desk_qbd_map_tax');
	if(is_array($tm_map_data) && count($tm_map_data)){
		foreach($tm_map_data as $tm_k=>$tm_val){
			if($tm_val['wc_tax_id_2']>0){
				$tl_tax_rate_class = (isset($wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_class']))?$wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_class']:'';
				$tl_tax_rate_class = ($tl_tax_rate_class=='')?'Standard rate':ucfirst(str_replace('-',' ',$tl_tax_rate_class));
				$tl_country = (isset($wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_country']))?$wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_country']:'';
				$tl_state = (isset($wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_state']))?$wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate_state']:'';
				$tl_taxrate = (isset($wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate']))?$wc_all_tax_rates[$tm_val['wc_tax_id']]['tax_rate']:'';
				
				$selected_options_script.='jQuery(\'#sc_wtax_'.$tm_val['wc_tax_id'].'\').val(\''.$tm_val['wc_tax_id_2'].'\');';
				$selected_options_script.='jQuery(\'#cobmbo_wtax_'.$tm_val['wc_tax_id'].'\').val(\''.$tm_val['qbo_tax_code'].'\');';
				
				$selected_options_script.='jQuery(\'#tl_tax_rate_class_'.$tm_val['wc_tax_id'].'\').html(\''.$tl_tax_rate_class.'\');';
				$selected_options_script.='jQuery(\'#tl_country_'.$tm_val['wc_tax_id'].'\').html(\''.$tl_country.'\');';
				$selected_options_script.='jQuery(\'#tl_state_'.$tm_val['wc_tax_id'].'\').html(\''.$tl_state.'\');';
				$selected_options_script.='jQuery(\'#tl_taxrate_'.$tm_val['wc_tax_id'].'\').html(\''.$tl_taxrate.'\');';
			}else{
				$selected_options_script.='jQuery(\'#wtax_'.$tm_val['wc_tax_id'].'\').val(\''.$tm_val['qbo_tax_code'].'\');';
			}		
		}	
	}
?>
<?php require_once plugin_dir_path( __FILE__ ) . 'mw-qbo-desktop-admin-menu-map-nav.php';?>
<div class="container map-tax-class-outer cnt-mwqbd">
	<div class="page_title flex-box">
		<h4><?php _e( 'Tax Mappings', 'mw_wc_qbo_desk' );?></h4>
		<div class="dashboard_main_buttons p-mapbtn">
			<button class="sh_compound_tx show_advanced_payment_sync">Show Compound Taxes</button>			
		</div>
	</div>
	
	<div class="mw_wc_filter">
	 <span class="search_text">Search</span>
	  &nbsp;
	  <input type="text" id="tax_map_search" value="<?php echo $tax_map_search;?>">
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
	
	<div class="card">
		<div class="card-content">
			<div class="row">
				<?php if(is_array($wc_tax_rates) && count($wc_tax_rates)):?>
				<form method="POST" class="col s12 m12 l12" action="<?php echo $page_url;?>">
					<div class="row">
						<div class="col s12 m12 l12">
							<div class="myworks-wc-qbd-sync-table-responsive">
								<table class="mw-qbo-sync-map-table menu-blue-bg" width="100%">
	                            	<thead>
	                                	<tr>
	                                    	<th width="5%" class="title-description">
												ID							    	
											</th>
	                                        <th width="25%" class="title-description">
												Tax	Name							    	
	                                        </th>
	                                        <th width="20%" class="title-description">
	                                            Tax	Class						    	
	                                        </th>
	                                        <th width="10%" class="title-description">
	                                            Country								    	
	                                        </th>
	                                        <th width="10%" class="title-description">
	                                            State								    	
	                                        </th>
	                                        <th width="10%" class="title-description">
	                                            Rate								    	
	                                        </th>
	                                        <th width="20%" class="title-description">
	                                            Quickbooks Tax
	                                        </th>
	                                    </tr>
	                                </thead>

									<?php 
									foreach($wc_tax_rates as $rates):
									$tax_rate_class = ($rates['tax_rate_class']=='')?'Standard rate':ucfirst(str_replace('-',' ',$rates['tax_rate_class']));
									?>
									<tr>
										<td><?php echo $rates['tax_rate_id'];?></td>
										<td><?php echo $rates['tax_rate_name'];?></td>
										<td><?php echo $tax_rate_class;?></td>
										<td><?php echo $rates['tax_rate_country'];?></td>
										<td><?php echo $rates['tax_rate_state'];?></td>
										<td><?php echo $rates['tax_rate'];?></td>
										<td>
										<select class="mw_wc_qbo_sync_select2_desk qbo_select sc_sel_tx" name="wtax_<?php echo $rates['tax_rate_id'];?>" id="wtax_<?php echo $rates['tax_rate_id'];?>">
										<?php echo $qbo_tax_options;?>
										</select>							
										</td>
									</tr>
									<tr class="crs_tr" style="display:none;" id="sc_tx_row_<?php echo $rates['tax_rate_id'];?>">
										<td>+&nbsp;</td>
										<td>
										<?php echo $rates['tax_rate_name'];?><br />
										<select class="qbo_select mw_wc_qbo_sync_select2_desk sc_sel_tx" name="sc_wtax_<?php echo $rates['tax_rate_id'];?>" id="sc_wtax_<?php echo $rates['tax_rate_id'];?>">
											<?php echo $MWQDC_LB->get_wc_tax_rate_dropdown($wc_tax_rates,'',$rates['tax_rate_id']);?>
										</select>
										</td>
										
										<td id="tl_tax_rate_class_<?php echo $rates['tax_rate_id'];?>"></td>
										<td id="tl_country_<?php echo $rates['tax_rate_id'];?>"></td>
										<td id="tl_state_<?php echo $rates['tax_rate_id'];?>"></td>
										<td id="tl_taxrate_<?php echo $rates['tax_rate_id'];?>"></td>
										<td>
											<select class="qbo_select mw_wc_qbo_sync_select2_desk" name="cobmbo_wtax_<?php echo $rates['tax_rate_id'];?>" id="cobmbo_wtax_<?php echo $rates['tax_rate_id'];?>">
												<?php echo $qbo_tax_options;?>
											</select>
										</td>
									</tr>
									<?php endforeach;?>
								</table>
							</div>
							<?php echo $pagination_links?>
						</div>
					</div>
					<div class="row">
						<?php wp_nonce_field( 'myworks_wc_qbo_sync_map_wc_qbo_tax_desk', 'map_wc_qbo_tax_desk' ); ?>
						<div class="input-field col s12 m6 l4">
							<button class="waves-effect waves-light btn save-btn mw-qbo-sync-green">Save</button>
						</div>
					</div>
				</form>
				<?php else:?>
				<p class="mqd-nf"><?php _e( 'No tax found.', 'mw_wc_qbo_desk' );?></p>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function search_item(){		
		var tax_map_search = jQuery('#tax_map_search').val();
		tax_map_search = jQuery.trim(tax_map_search);
		if(tax_map_search!=''){
			window.location = '<?php echo $page_url;?>&tax_map_search='+tax_map_search;
		}else{
			alert('<?php echo __('Please enter search keyword.','mw_wc_qbo_sync')?>');
		}
	}

	function reset_item(){		
		window.location = '<?php echo $page_url;?>&tax_map_search=';
	}
	
	jQuery(document).ready(function($){
		jQuery('.sc_sel_tx').change(function(){			
			var p_tx = jQuery(this).attr('id');
			p_tx = p_tx.replace('sc_wtax_','');
			
			var tx_val = $('option:selected', this).val();
					
			if(tx_val!=''){				
				var tax_rate_class = $('option:selected', this).attr('data-tax_rate_class');
				var tx_country = $('option:selected', this).attr('data-tax_rate_country');
				var tx_state = $('option:selected', this).attr('data-tax_rate_state');
				var tx_taxrate = $('option:selected', this).attr('data-tax_rate');
				
				jQuery('#tl_tax_rate_class_'+p_tx).html(tax_rate_class);
				jQuery('#tl_country_'+p_tx).html(tx_country);
				jQuery('#tl_state_'+p_tx).html(tx_state);
				jQuery('#tl_taxrate_'+p_tx).html(tx_taxrate);
			}else{
				jQuery('#tl_tax_rate_class_'+p_tx).html('');
				jQuery('#tl_country_'+p_tx).html('');
				jQuery('#tl_state_'+p_tx).html('');
				jQuery('#tl_taxrate_'+p_tx).html('');
			}
		});
		<?php if($selected_options_script!=''):?>		
			<?php echo $selected_options_script;?>		
		<?php endif;?>
		
		jQuery('.sh_compound_tx').click(function($){
			var crs = jQuery(this).text();			
			if(crs=='Show Compound Taxes'){
				jQuery(this).addClass('hide_advanced_payment_sync').removeClass('show_advanced_payment_sync');
				jQuery('.crs_tr').show();			
				jQuery(this).text('Hide Compound Taxes');	
			}
			
			if(crs=='Hide Compound Taxes'){
				jQuery(this).addClass('show_advanced_payment_sync').removeClass('hide_advanced_payment_sync');		
				jQuery('.crs_tr').hide();				
				jQuery(this).text('Show Compound Taxes');
			}
		});
	});				
</script>
 <?php echo $MWQDC_LB->get_select2_js('.mw_wc_qbo_sync_select2_desk');?>