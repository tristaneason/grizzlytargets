<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fired during plugin deactivation
 *
 * @link       http://myworks.design/
 * @since      1.0.0
 *
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    MW_QBO_Desktop
 * @subpackage MW_QBO_Desktop/includes
 * @author     MyWorks Design <sales@myworks.design>
 */
class MW_QBO_Desktop_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		$url = get_bloginfo('url');
		$company = get_bloginfo('name');
		$email = get_bloginfo('admin_email');
		$wordpress_version = get_bloginfo('version');
		
		$license_key = get_option('mw_wc_qbo_desk_license');
		$plugin_version = MW_QBO_Desktop_Admin::return_qbd_plugin_version();
		
		$message = "<b>WooCommerce Sync for QuickBooks Desktop</b></br>";
		$message .= "</br>";
		$message .= "<b>License Key:</b> " . $license_key ."</br>";
		$message .= "<b>Version:</b> " . $plugin_version ."</br>";
		$message .= "</br>";
		$message .= "<b>Company:</b> " .$company ."</br>";
		$message .= "<b>Email:</b> " .$email ."</br>";
		$message .= "<b>WooCommerce URL:</b> " .$url ."</br>";
		
		$headers = array(
			'MIME-Version: 1.0',
			'Content-type:text/html;charset=UTF-8',
		);		
		
		$to = 'notifications@myworks.design';
		
		wp_mail($to, 'Deactivate - Quickbooks Desktop', $message, $headers);
		
		$post_url = 'https://myworks.design/dashboard/api/dashboard/product/saveModule';
		
		$params = array(
			'api_version'=>'0.1',
			'result_type'=>'json',
			'process'=>'de-activated',
			'licensekey'=>$license_key,
			'version'=>$plugin_version,
			'company'=>$company,
			'email'=>$email,
			'system_url'=>$url
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url); curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128); curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$response = curl_exec($ch);
		curl_close($ch);
		
		delete_option('mw_wc_qbo_desk_license');
		delete_option('mw_wc_qbo_desk_localkey');
		
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		deactivate_plugins( plugin_dir_path( __FILE__ ) . 'myworks-quickbooks-desktop-sync.php' );
	}

}