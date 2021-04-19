<?php
global $MWQDC_LB;
global $wpdb;
$server_db = $MWQDC_LB->db_check_get_fields_details();
$error = false;
if(is_array($server_db) && count($server_db)){
	foreach($server_db as $k=>$v){
		$is_db_updated = false;
		if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod'){				
			if(!array_key_exists("term_id_str",$v)){

				/*echo '<div class="mw_qbo_sync_db_fix_section">
					<p class="mw_qbo_sync_db_fix_has_error">You\'ve an issue with database table <span class="mw_qbo_sync_highlight">'.$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod'.'</span> on server. Please click the following link to fix.</p>
					<a class="mw_qbo_sync_db_fix_repair" href="'.admin_url('admin.php?page=myworks-wc-qbd-sync-db-fix&issue=mw_wc_qbo_desk_qbd_map_paymentmethod').'">Click here.</a>
					</div>';*/	

				$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_paymentmethod` CHANGE `term_id` `term_id_str` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
				$wpdb->query($sql);
				$is_db_updated = true;
				$error = true;		
				echo '<div class="mw_qbo_sync_db_fix_section">
				<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_paymentmethod'.' table, and it got resolved now!.</p>
				</div>';
			}
		}
		
		if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs'){
			if(!array_key_exists("ext_data",$v)){
				$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_data_pairs` ADD `ext_data` VARCHAR(255) NOT NULL AFTER `d_type`;";
				$wpdb->query($sql);
				$is_db_updated = true;
				$error = true;		
				echo '<div class="mw_qbo_sync_db_fix_section">
				<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_data_pairs'.' table, and it got resolved now!.</p>
				</div>';		
			}
		}
		
		if($k == $wpdb->prefix.'mw_wc_qbo_desk_qbd_map_shipping_product'){
			if(!array_key_exists("qb_shipmethod_id",$v)){
				$sql = "ALTER TABLE `{$wpdb->prefix}mw_wc_qbo_desk_qbd_map_shipping_product` ADD `qb_shipmethod_id` VARCHAR(255) NOT NULL AFTER `class_id`;";
				$wpdb->query($sql);
				$is_db_updated = true;
				$error = true;		
				echo '<div class="mw_qbo_sync_db_fix_section">
				<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_shipping_product'.' table, and it got resolved now!.</p>
				</div>';		
			}
		}
	}
	
	//New Tables
	if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite'])){
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_inventorysite (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,
		  is_default int(1) NOT NULL,
		  s_desc text NOT NULL,
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		$wpdb->query($sql);
		
		$is_db_updated = true;
		$error = true;		
		echo '<div class="mw_qbo_sync_db_fix_section">
		<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_inventorysite'.' table, and it got resolved now!.</p>
		</div>';
	}
	
	if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep'])){
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_salesrep (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,				 
		  sr_e_ref_id varchar(255) NOT NULL,
		  sr_e_ref_name varchar(255) NOT NULL,
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		
		$is_db_updated = true;
		$error = true;		
		echo '<div class="mw_qbo_sync_db_fix_section">
		<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_salesrep'.' table, and it got resolved now!.</p>
		</div>';
	}
	
	if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_customertype'])){
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_customertype (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,		  
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		$wpdb->query($sql);
		
		$is_db_updated = true;
		$error = true;		
		echo '<div class="mw_qbo_sync_db_fix_section">
		<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_customertype'.' table, and it got resolved now!.</p>
		</div>';
	}
	
	if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_wq_cf'])){
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_map_wq_cf (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  wc_field varchar(255) NOT NULL,
		  qb_field varchar(255) NOT NULL,		 
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;";
		$wpdb->query($sql);
		
		$is_db_updated = true;
		$error = true;		
		echo '<div class="mw_qbo_sync_db_fix_section">
		<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_map_wq_cf'.' table, and it got resolved now!.</p>
		</div>';
	}
	
	if(!isset($server_db[$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_shipmethod'])){
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mw_wc_qbo_desk_qbd_list_shipmethod (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  qbd_id varchar(255) NOT NULL,
		  name varchar(255) NOT NULL,		  
		  is_active int(1) NOT NULL,		  
		  `info_arr` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  `info_qbxml_obj` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
		  PRIMARY KEY (id)
		) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;";
		$wpdb->query($sql);
		
		$is_db_updated = true;
		$error = true;		
		echo '<div class="mw_qbo_sync_db_fix_section">
		<p class="mw_qbo_sync_db_fix_no_error">You had an issue with '.$wpdb->prefix.'mw_wc_qbo_desk_qbd_list_shipmethod'.' table, and it got resolved now!.</p>
		</div>';
	}
}

if(!$error){
	echo '<div class="mw_qbo_sync_db_fix_section">
	<p class="mw_qbo_sync_db_fix_no_error">You don\'t have any issue with database tables.</p>
	</div>';
}
?>
