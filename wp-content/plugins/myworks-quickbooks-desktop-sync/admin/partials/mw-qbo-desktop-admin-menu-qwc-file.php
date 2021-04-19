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
	$page_url = 'admin.php?page=mw-qbo-desktop-qwc-file';
	
	MW_QBO_Desktop_Admin::is_trial_version_check();
	 
	$qb_max_returned_options = array(
		'10' =>10,
		'25' =>25,
		'50' =>50,
		'100' =>100,
		'200' =>200,
		'500' =>500,
		'1000' =>1000,
		'3000' =>3000,
		'5000' =>5000,
	);
	
	$error = array();
	if ( ! empty( $_POST['mw_qbo_dts_gen_qwc_file'] ) && check_admin_referer( 'myworks_wc_qb_dts_gen_qwc_file', 'mw_qbo_dts_gen_qwc_file' ) ) {
		//$MWQDC_LB->_p($_POST);die;
		$qwc_un = (isset($_POST['mw_qbo_dts_qwc_username']))?trim($_POST['mw_qbo_dts_qwc_username']):'';
		$qwc_pw = (isset($_POST['mw_qbo_dts_qwc_password']))?trim($_POST['mw_qbo_dts_qwc_password']):'';
		$qwc_ri= (isset($_POST['mw_qbo_dts_qwc_sec_interval']))?(int) $_POST['mw_qbo_dts_qwc_sec_interval']:0;
		
		$qwc_mr= (isset($_POST['mw_qbo_dts_qwc_qb_max_returned']))?(int) $_POST['mw_qbo_dts_qwc_qb_max_returned']:100;
		if(is_array($qb_max_returned_options) && !in_array($qwc_mr,$qb_max_returned_options)){
			$qwc_mr = 100;
		}
		
		$qwc_cfp = (isset($_POST['mw_qbo_dts_qwc_company_file_path']))?trim($_POST['mw_qbo_dts_qwc_company_file_path']):'';
		
		if(!empty($qwc_cfp)){
			$qwc_cfp = stripslashes($qwc_cfp);
		}
		
		if($qwc_ri < 60){
			$qwc_ri = 60;
		}
		if(strlen($qwc_un)<6){
			$error[] = 'QWC Username should be altleast 6 chars.';
		}
		if(strlen($qwc_pw)<8){
			$error[] = 'QWC Password should be altleast 8 chars.';
		}

		if(empty($error)){
			update_option('mw_qbo_dts_qwc_username',$qwc_un);
			update_option('mw_qbo_dts_qwc_password',$MWQDC_LB->encrypt($qwc_pw));
			update_option('mw_qbo_dts_qwc_sec_interval',$qwc_ri);
			
			update_option('mw_qbo_dts_qwc_qb_max_returned',$qwc_mr);
			
			update_option('mw_qbo_dts_qwc_company_file_path',$qwc_cfp);
			
			//02-11-2017 - Need to update new password into quickbooks_user table
			$qb_hash_func = QUICKBOOKS_HASH;
			$qwc_pw_qb_enc = $qb_hash_func($qwc_pw . QUICKBOOKS_SALT);
			global $wpdb;
			$wpdb->update('quickbooks_user',array('qb_password'=>$qwc_pw_qb_enc,'qb_company_file'=>$qwc_cfp),array('qb_username'=>$qwc_un),'',array('%s','%s'));
			
			if(isset($_POST['gen_qwc'])){
				$MWQDC_LB->generate_qwc_file();
			}else{
				$MWQDC_LB->redirect($page_url);
			}
		}
	}

	$mw_qbo_dts_qwc_username = $MWQDC_LB->get_option('mw_qbo_dts_qwc_username');
	$mw_qbo_dts_qwc_password = $MWQDC_LB->get_option('mw_qbo_dts_qwc_password');

	$mw_qbo_dts_qwc_sec_interval = (int) $MWQDC_LB->get_option('mw_qbo_dts_qwc_sec_interval');
	if($mw_qbo_dts_qwc_sec_interval < 60){
		$mw_qbo_dts_qwc_sec_interval = 60;
		update_option('mw_qbo_dts_qwc_sec_interval',$mw_qbo_dts_qwc_sec_interval);
	}
	
	$mw_qbo_dts_qwc_qb_max_returned = (int) $MWQDC_LB->get_option('mw_qbo_dts_qwc_qb_max_returned');
	if(is_array($qb_max_returned_options) && !in_array($mw_qbo_dts_qwc_qb_max_returned,$qb_max_returned_options)){
		$mw_qbo_dts_qwc_qb_max_returned = 100;
	}
	
	$mw_qbo_dts_qwc_company_file_path = $MWQDC_LB->get_option('mw_qbo_dts_qwc_company_file_path');
	/*
	if(!empty($mw_qbo_dts_qwc_company_file_path)){
		$mw_qbo_dts_qwc_company_file_path = stripslashes($mw_qbo_dts_qwc_company_file_path);
	}
	*/
	
	if($mw_qbo_dts_qwc_username==''){
		$mw_qbo_dts_qwc_username = $MWQDC_LB->get_random_username(6,10);
		update_option('mw_qbo_dts_qwc_username',$mw_qbo_dts_qwc_username);
	}

	if($mw_qbo_dts_qwc_password==''){
		$mw_qbo_dts_qwc_password = $MWQDC_LB->encrypt($MWQDC_LB->get_random_password(8,10));
		update_option('mw_qbo_dts_qwc_password',$mw_qbo_dts_qwc_password);
	}

	$mw_wc_qbo_sync_license = $MWQDC_LB->get_option('mw_wc_qbo_desk_license','');
	$mw_wc_qbo_sync_localkey = $MWQDC_LB->get_option('mw_wc_qbo_desk_localkey','');
	
	$ldfcpv = $MWQDC_LB->get_ldfcpv();

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
echo '<form style="position:absolute; top:-100px;">
	  <label for="email">E-Mail</label>
	  <input id="email" readonly type="email" onfocus="if (this.hasAttribute(\'readonly\')) { this.removeAttribute(\'readonly\');
	    this.blur();    this.focus();  }" />
	  <label for="pw">Password</label>
	  <input id="pw" readonly type="password" onfocus="if (this.hasAttribute(\'readonly\')) { this.removeAttribute(\'readonly\');
	    this.blur();    this.focus();  }" />
	</form>
	';
	
?>
<div class="container qwc-outer">
<?php if($MWQDC_LB->is_valid_license($mw_wc_qbo_sync_license,$mw_wc_qbo_sync_localkey)):?>
<div class="mwqs_conection_options">
    <div class="myworks-wc-qbd-sync-table-responsive">
		<table class="widefat fixed">
			<tr>
				<td width="20%"><label for="mw_wc_qbo_sync_license_update_desk" class="mw_wc_qbo_sync_label">License Key:</label></td>
				<td  width="50%">
				<input class="mw_wc_qbo_sync_input" type="text" name="mw_wc_qbo_sync_license_update_desk" id="mw_wc_qbo_sync_license_update_desk" value="<?php echo $mw_wc_qbo_sync_license; ?>" required="required" disabled="disabled"/>
				&nbsp;<span class="mw_wc_qbo_sync_span"></span>
				
				&nbsp;
				<a id="mwqs_dllk" title="<?php echo __('Refresh local license data by doing remote license check','mw_wc_qbo_desk');?>" href="javascript:void(0)" onclick="">
					<i class="fa fa-refresh"></i> Refresh
				</a>
				<?php echo wp_nonce_field( 'myworks_wc_qbo_sync_del_license_local_key_desk', 'del_license_local_key_desk' );?>
				
				</td>
				<td  width="30%">
					<p class="mw_wc_qbo_sync_paragraph">
					<?php
					echo __('To update your license key, deactivate and re-activate the plugin. All your settings and mappings will be saved unless the plugin is deleted.','mw_wc_qbo_desk');
					?>
					</p>
				</td>
			</tr>
			
			<!---->
			<tr>
				<td>Plan Details</td>
				<td>
					<div class="licence-list">
						  <ul>
							 <li class="current">
								<div class="left-status">
								   Status
								</div>
								<div class="right-status">
								   <?php echo (isset($ldfcpv['status']))?$ldfcpv['status']:''?>
								</div>
							 </li>
							 <li>
								<div class="left-status">
								   Plan
								</div>
								<div class="right-status">
								   <?php echo (isset($ldfcpv['plan']))?$ldfcpv['plan']:''?>
								</div>
							 </li>
							 <li>
								<div class="left-status">
								   Next Due Date
								</div>
								<div class="right-status">
								  <?php echo (isset($ldfcpv['nextduedate']) && !empty($ldfcpv['nextduedate']) && $ldfcpv['nextduedate'] != '0000-00-00')?date('M j, Y',strtotime($ldfcpv['nextduedate'])):''?>
								</div>
							 </li>
							 <li>
								<div class="left-status">
								   Billing Cycle
								</div>
								<div class="right-status">
								  <?php echo (isset($ldfcpv['billingcycle']))?$ldfcpv['billingcycle']:''?>
								</div>
							 </li>
							 <?php if($MWQDC_LB->is_plg_lc_p_l() || $MWQDC_LB->is_plg_lc_p_g()):?>
							 <li>
								<div class="left-status">
								   Monthly Orders
								</div>
								<div class="right-status">
								  <?php echo (int) $MWQDC_LB->get_osl_sm_val();?> of <?php echo (int) $MWQDC_LB->get_osl_lp_count();?>
								</div>
							 </li>
							 <?php endif;?>
						  </ul>						  
					   </div>
				</td>
				<td>&nbsp;</td>
			</tr>
			
		</table>
	</div>
</div>
<div class="qwc-inner">
	<h2>Generate Quickbooks Web Connector (QWC) File</h2>
	<?php if(count($error)):?>
	<div style="color:red;"><?php echo implode('<br/>',$error);?></div>
	<?php endif;?>
	<form method="POST" action="<?php echo $page_url;?>" autocomplete="off">
		<div class="myworks-wc-qbd-sync-table-responsive">
			<table width="100%" class="qwc-tbl">
				<tr>
					<td>Web Connector User (Min 6 chars): </td>
					<td>&nbsp;</td>
					<td>
						<input type="text" name="mw_qbo_dts_qwc_username" value="<?php echo $mw_qbo_dts_qwc_username;?>">
					</td>
				</tr>

				<tr>
					<td>Web Connector Password (Min 8 chars): </td>
					<td>&nbsp;</td>
					<td>
						<input type="text" name="mw_qbo_dts_qwc_password" id="mw_qbo_dts_qwc_password" value="<?php echo $MWQDC_LB->decrypt($mw_qbo_dts_qwc_password);?>">
					</td>
				</tr>
				
				<tr style="display:none;">
					<td>Request Interval: </td>
					<td>&nbsp;</td>
					<td>
						<input type="text" size="5" name="mw_qbo_dts_qwc_sec_interval" value="<?php echo $mw_qbo_dts_qwc_sec_interval;?>">&nbsp;Default: 600 (in seconds)
					</td>
				</tr>
				
				<tr>
					<td>QuickBooks Company Full Path: </td>
					<td>&nbsp;</td>
					<td>
						<input type="text" name="mw_qbo_dts_qwc_company_file_path" placeholder="C:\Users\Public\Documents\Intuit\QuickBooks\Company Files\My Company File.qbw" value="<?php echo $mw_qbo_dts_qwc_company_file_path;?>">
						&nbsp;
						Optional - only if you would like to sync while QuickBooks is completely closed.
					</td>
				</tr>
				
				<tr>
					<td>Batch Size</td>
					<td>&nbsp;</td>
					<td>
						<select name="mw_qbo_dts_qwc_qb_max_returned">
							<?php $MWQDC_LB->only_option($mw_qbo_dts_qwc_qb_max_returned,$qb_max_returned_options);?>
						</select>
					</br>
					Maximum activity/items processed in a single request (Default: 100)
					</td>
				</tr>
				
				<tr>
					<td>
						<?php wp_nonce_field( 'myworks_wc_qb_dts_gen_qwc_file', 'mw_qbo_dts_gen_qwc_file' ); ?>
						<input class="button button-primary button-large" type="submit" name="gen_qwc" value="Generate QWC File">
					</td>
					<td>&nbsp;</td>
					<td style="text-align:right;">
						<input class="button button-primary button-large cosb_r" type="submit" name="o_save" value="Save">
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script>
jQuery(document).ready(function($){
	$('#mwqs_dllk').click(function(){
		if(confirm('Are you sure, you want to refresh local license data?')){
			jQuery(this).html('<i class="fa fa-refresh"></i> Loading...');
			var data = {
				"action": 'mw_wc_qbo_sync_del_license_local_key_desk',
				"del_license_local_key_desk": jQuery('#del_license_local_key_desk').val(),
			};
			jQuery.ajax({
			   type: "POST",
			   url: ajaxurl,
			   data: data,
			   cache:  false ,
			   //datatype: "json",
			   success: function(result){
				   if(result!=0 && result!=''){					
					location.reload();
				   }else{
					 jQuery('#mwqs_dllk').html('<i class="fa fa-refresh"></i> Error!');					 
				   }				  
			   },
			   error: function(result) { 		
					jQuery('#mwqs_dllk').html('<i class="fa fa-refresh"></i> Error!');
			   }
			});
		}		
	});
});

</script>

<?php	if($MWQDC_LB->is_qwc_connected()):	?>
	<div class="mwqd_cmp_info">
		<h2>QuickBooks Desktop Company Info</h2>
		<?php
			$mw_wc_qbo_desk_qbd_company_info_arr = $MWQDC_LB->get_option('mw_wc_qbo_desk_qbd_company_info_arr');
			if($mw_wc_qbo_desk_qbd_company_info_arr!=''){
				$mw_wc_qbo_desk_qbd_company_info_arr = unserialize($mw_wc_qbo_desk_qbd_company_info_arr);
			}
			if(is_array($mw_wc_qbo_desk_qbd_company_info_arr) && count($mw_wc_qbo_desk_qbd_company_info_arr)):
		?>
		<table class="wp-list-table widefat fixed striped posts">
			<tr>
				<td width="8%">Name:</td>
				<td><?php echo $mw_wc_qbo_desk_qbd_company_info_arr['CompanyName'];?></td>
			</tr>

			<tr>
				<td>Email:</td>
				<td><?php echo $mw_wc_qbo_desk_qbd_company_info_arr['Email'];?></td>
			</tr>

			<tr>
				<td>Country:</td>
				<td><?php echo $mw_wc_qbo_desk_qbd_company_info_arr['Country'];?></td>
			</tr>
		</table>
		<small>Showing based on last preferences import - <?php echo $mw_wc_qbo_desk_qbd_company_info_arr['added_date'];?></small>
		<?php else:?>
			<p>No company information found.</p>
		<?php endif;?>		
	</div>
<?php endif;?>

<?php elseif($MWQDC_LB->get_license_status()=='Invalid'):?>
<div class="qbd_input_license"><p><?php echo __('This license key is not valid for this domain. Moving sites? Click Change Site in your account with us to open the license key, then save again here.','mw_wc_qbo_desk');?></p>
<?php elseif($MWQDC_LB->get_license_status()=='Expired'):?>
<div class="qbd_input_license"><p><?php echo __('Your license key is expired. Please check your account with us to renew your plan or enter a valid license key.','mw_wc_qbo_desk');?></p>
<?php elseif($MWQDC_LB->get_license_status()=='Suspended'):?>
<div class="qbd_input_license"><p><?php echo __('Your license key is suspended. Please check your account with us to renew your plan or enter a valid license key.','mw_wc_qbo_desk');?></p>
<?php else:?>
<div class="qbd_input_license"><p><?php echo __('Please enter a valid license key to proceed.','mw_wc_qbo_desk');?></p>
<?php endif;?>


<?php if($MWQDC_LB->get_license_status()!='Active'):?>

<div class="mwqs_conection_license_check_desk">
<form method="post" id="myworks_wc_qbo_sync_check_license_desk">
	<label for ="mw_wc_qbo_sync_license_desk">License Key: </label>
	<input type="text" name="mw_wc_qbo_sync_license_desk" id="mw_wc_qbo_sync_license_desk" value="<?php echo $mw_wc_qbo_sync_license;?>">
	 <?php wp_nonce_field( 'myworks_wc_qbo_sync_check_license_desk', 'check_plugin_license_desk' ); ?>
	<input size="30" type="submit" value="Enter" class="button button-primary">
	<span id="mwqs_license_chk_loader" style="visibility:hidden;">
	<img src="<?php echo esc_url( plugins_url( 'image/ajax-loader.gif', dirname(__FILE__) ) );?>" alt="Loading..." />
	</span>
</form>
</div>
</div>

<?php endif;?>
</div>