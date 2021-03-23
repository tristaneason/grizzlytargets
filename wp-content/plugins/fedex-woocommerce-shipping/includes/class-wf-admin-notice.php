<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if(!class_exists('wf_admin_notice')){
	define('WF_NOTICE_OPTION','wf_fedex_notice');
	class wf_admin_notice{		
		
		public function __construct(){
			//nothing right now
		}
		
		public static function add_notice($message, $type='error'){
			$notices	=	get_option(WF_NOTICE_OPTION);
			if(!$notices){
				add_option(WF_NOTICE_OPTION);
				$notices	=	array(
					'error' => array(),
					'notice' => array(),
				);
			}
			switch($type){
				case 'error':
					$notices['error'][]	=	$message;
					break;
				case 'notice':
					$notices['notice'][]=	$message;
					break;
			}
			update_option(WF_NOTICE_OPTION,$notices);
		}
		
		public static function throw_notices(){
			$notices	=	get_option(WF_NOTICE_OPTION);
			delete_option(WF_NOTICE_OPTION);
			if(!$notices){
				return;
			}
			if(isset($notices['error']) && is_array($notices['error']) && count($notices['error'])){
				?>
				<div class="notice error is-dismissible" >
				<?php foreach($notices['error'] as $error){?>
					<p><?php _e( $error, 'wf-shipping-fedex' ); ?></p>
				<?php }?>
				</div>
				<?php
			}
			if(isset($notices['notice']) && is_array($notices['notice']) && count($notices['notice'])){
				?>
				<div class="notice  notice-success is-dismissible" >
				<?php foreach($notices['notice'] as $notice){?>
					<p><?php _e( $notice, 'wf-shipping-fedex' ); ?></p>
				<?php }?>
				</div>
				<?php
			}		
		}
	}
}