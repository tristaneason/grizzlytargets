<?php

class wf_fedex_pickup_admin{
	
	var $_pickup_confirmation_number	=	'_pickup_confirmation_number';
	var $_pickup_location				=	'_pickup_location';
	var $_pickup_scheduled_date			=	'_pickup_scheduled_date';	
	
	public function __construct(){
		$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		$this->pickup_enabled 			= (isset($this->settings[ 'pickup_enabled']) && $this->settings[ 'pickup_enabled']=='yes') ? true : false;
		if($this->pickup_enabled){
			$this->init();
		}		
	}
	
	private function init(){
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_pickup_column_header' ), 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_order_status_column_content' ) );


		add_action('admin_footer', 	array($this, 'add_pickup_request_option'));
		add_action('admin_footer', 	array($this, 'add_pickup_cancel_option'));
		add_action('load-edit.php',	array($this, 'perform_pickup_list_action'));
		add_action('manage_shop_order_posts_custom_column' , array($this,'display_order_list_pickup_status'),10,2);
	}

	public function add_order_status_column_content( $column ) {
	    global $post;
	    if ( 'fedex_pickup_info' == $column ) {
	        $order = $this->wf_load_order( $post->ID );
	        $is_exported = true;

	        if( $this->is_pickup_requested( $order->get_id() ) ){
				echo('<span class="dashicons dashicons-yes"></span>').__('Requested', 'wf-shipping-fedex');
	        }else{
	        	echo('<span class="dashicons dashicons-marker"></span>').__('Not Requested','wf-shipping-fedex' );
			}
	    }
	}

	public function add_pickup_column_header( $columns ) {
	   $new_columns = array();
	   foreach ( $columns as $column_name => $column_info ) {
	       $new_columns[ $column_name ] = $column_info;
	       if ( 'shipping_address' == $column_name ) {
	           $new_columns['fedex_pickup_info'] = __( 'FedEx Pickup' );
	       }
	   }
	   return $new_columns;
	}
	
	public function add_pickup_request_option(){
		global $post_type;
 
		if($post_type == 'shop_order') {
		?>
		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery('<option>').val('fedex_pickup_request').text('<?php _e('Request FedEx Pickup','wf-shipping-fedex')?>').appendTo("select[name='action']");
			jQuery('<option>').val('fedex_pickup_request').text('<?php _e('Request FedEx Pickup','wf-shipping-fedex')?>').appendTo("select[name='action2']");
		  });
		</script>
		<?php
		}
	}
	
	public function add_pickup_cancel_option(){
		global $post_type;
 
		if($post_type == 'shop_order') {
		?>
		<script type="text/javascript">
		  jQuery(document).ready(function() {
			jQuery('<option>').val('fedex_pickup_cancel').text('<?php _e('Cancel FedEx Pickup','wf-shipping-fedex')?>').appendTo("select[name='action']");
			jQuery('<option>').val('fedex_pickup_cancel').text('<?php _e('Cancel FedEx Pickup','wf-shipping-fedex')?>').appendTo("select[name='action2']");
		  });
		</script>
		<?php
		}
	}
	
	public function perform_pickup_list_action(){
		$wp_list_table = _get_list_table('WP_Posts_List_Table');
		$action = $wp_list_table->current_action();	
		
		if($action == 'fedex_pickup_request'){// Pickup Request
			//$order_ids	=	$_REQUEST['post']?$_REQUEST['post']:array();
			if(isset($_REQUEST['post']) && is_array($_REQUEST['post'])){
				$order_ids	=	$_REQUEST['post'];
				if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
					include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
				$helper			=	new wf_fedex_woocommerce_shipping_admin_helper();
				$result_array	=	$helper->request_pickup($order_ids);

				if( is_array($result_array) && !empty($result_array) )
				{
					$count = true;
					foreach ($result_array as $order_id => $result) {
						
						if( !empty($result) ){

							$pickup_array 		= array();
							$location_array 	= array();
							$date_array 		= array();

							foreach ($result as $service => $pickup_data) {

								if(isset($pickup_data) && isset($pickup_data['data'])){
								
									$pickup_array[$service] 	= $pickup_data['data']['PickupConfirmationNumber'];
									$location_array[$service] 	= $pickup_data['data']['Location'];
									$date_array[$service] 		= $pickup_data['data']['ScheduledDate'];
								
									update_post_meta($order_id,$this->_pickup_confirmation_number, $pickup_array);
									update_post_meta($order_id,$this->_pickup_location,$location_array);
									update_post_meta($order_id,$this->_pickup_scheduled_date,$date_array);

									if ( $count ) {
										wf_admin_notice::add_notice('FedEx Pickup Requested for Order ID(s): '.implode(", ",$order_ids),'notice');
										$count = false;
									}

									wf_admin_notice::add_notice('FedEx Pickup Scheduled Succesfully for Order ID: '.$order_id,'notice');
									
								}else if(isset($pickup_data['error']) && $pickup_data['error']>0){
									wf_admin_notice::add_notice('Order #'.$order_id.' - Pickup Request Error: '.$pickup_data['message'],'error');
								}
							}
						}
					}
				}else{
					wf_admin_notice::add_notice('Pickup Request Error');
				}
				
			}
		}else if($action == 'fedex_pickup_cancel'){//Cancel Pickup
			$order_ids	=	isset( $_REQUEST['post'] ) ?$_REQUEST['post']:array();
			foreach($order_ids as $order_id){
				$result	=	$this->pickup_cancel($order_id);
				if(isset($result) && isset($result['error']) && $result['error']==0){
					$this->delete_pickup_details($order_id);
					wf_admin_notice::add_notice('FedEx Pickup Cancelled for Order #'.$order_id.'.','notice');
				}
				else{
					wf_admin_notice::add_notice('Order #'.$order_id.': '.$result['message'],'error');
				}
			}
		}
	}
	
	public function get_pickup_no($order_id){
		if(empty($order_id))
			return false;

		$pickup_confirmation_number 		= '';
		$pickup_confirmation_number_array	= get_post_meta($order_id,$this->_pickup_confirmation_number, 1);
		$pickup_location_array				= get_post_meta($order_id,$this->_pickup_location, 1);

		if( is_array($pickup_confirmation_number_array) && !empty($pickup_confirmation_number_array) )
		{
			foreach ($pickup_confirmation_number_array as $service => $pickup_number) {

				if( empty($pickup_location_array[$service]) ){
					$pickup_confirmation_number 	.= $pickup_number.', ';	
				}else{
					$pickup_confirmation_number 	.= $pickup_location_array[$service].'-'.$pickup_number.', ';	
				}
			}

			if( !empty($pickup_confirmation_number) )
			{
				$pickup_confirmation_number = rtrim( $pickup_confirmation_number, ', ');
			}
		}
		
		return $pickup_confirmation_number;				
	}
	public function get_pickup_details($order_id){
		$details	=	array(
			'pickup_confirmation_number'	=>	get_post_meta($order_id,$this->_pickup_confirmation_number,1),
			'pickup_location'				=>	get_post_meta($order_id,$this->_pickup_location,1),
			'pickup_scheduled_date'			=>	get_post_meta($order_id,$this->_pickup_scheduled_date,1),
		);
		return $details;				
	}
	public function delete_pickup_details($order_id){
		delete_post_meta($order_id, $this->_pickup_confirmation_number);
		delete_post_meta($order_id, $this->_pickup_location);
		delete_post_meta($order_id, $this->_pickup_scheduled_date);
	}
	public function is_pickup_requested($order_id){		
		return $this->get_pickup_no($order_id)?true:false;
	}
	public function pickup_request($order_ids){
		$pickup_result	=	$helper->pickup_request($order, $order_id);
		return $pickup_result;
	}
	
	public function pickup_cancel($order_id){
		if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
			include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
		$helper	=	new wf_fedex_woocommerce_shipping_admin_helper();
		
		$order = 	$this->wf_load_order($order_id);
		if (!$order) 
			return;		
		
		$pickup_result	=	$helper->pickup_cancel($order, $order_id, $this->get_pickup_details($order_id));
		return $pickup_result;
	}
	
	function display_order_list_pickup_status($column, $order_id){

		if( $this->is_pickup_requested($order_id) )
		{
			switch ( $column ) {
				case 'shipping_address':
					printf('<small class="meta">'.__('FedEx Pickup Number(s): '.$this->get_pickup_no($order_id),'wf-shipping-fedex').'</small>');
				break;
				case 'fedex_pickup_info':
					printf('<small class="meta">'.__('Pickup Number(s): '.$this->get_pickup_no($order_id),'wf-shipping-fedex').'</small>');
				break;
			}
		}
	}
	
	private function wf_load_order($orderId){
		if (!class_exists('WC_Order')) {
			return false;
		}
		return new WC_Order($orderId);      
	}	
}
new wf_fedex_pickup_admin();