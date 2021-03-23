<?php
/**
* 
*/
class xa_my_account_order_return
{
	function __construct(){
		$this->init();
		if( $this->frontend_retun_label ){
			add_action( 'woocommerce_order_details_after_order_table', array($this,'xa_return_from_my_account_form'), 5 , 1 );
		}
		if (isset($_GET['generate_fedex_return_label'])) {
			add_action('init', array($this, 'generate_fedex_return_label'));
		}
	}
	private function init(){
		$this->settings 	= get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );
		$this->debug        = ( isset($this->settings[ 'debug' ]) && ( $bool = $this->settings[ 'debug' ] ) && $bool == 'yes' ) ? true : false;

		//This option is removed since 3.2.3 (Released 09-Nov-17 ), Kept here for backward compatibility
		$this->retun_label_dom_service 	= isset( $this->settings['retun_label_dom_service'] ) ? $this->settings['retun_label_dom_service'] : '';
		$this->retun_label_int_service 	= isset( $this->settings['retun_label_int_service'] ) ? $this->settings['retun_label_int_service'] : '';
				
		if(empty($this->retun_label_dom_service))
			$this->retun_label_dom_service 	= isset( $this->settings['default_dom_service'] ) ? $this->settings['default_dom_service'] : '';
		
		if( empty($this->retun_label_int_service))
			$this->retun_label_int_service 	= isset( $this->settings['default_int_service'] ) ? $this->settings['default_int_service'] : '';
		
		$this->frontend_retun_label 		= isset( $this->settings['frontend_retun_label'] ) && $this->settings['frontend_retun_label'] =='yes' ? true : false;
		$this->return_label_reason_required	= ( isset($this->settings['frontend_retun_label_reason']) && $this->settings['frontend_retun_label_reason'] == 'yes' ) ? true : false;
	}

	public function xa_return_from_my_account_form( $order ) {
		global $woocommerce;
		global $wp;

		$this->order_id = isset($wp->query_vars['view-order']) ? $wp->query_vars['view-order'] : 0;
		
		$shipmentIds = get_post_meta($this->order_id, 'wf_woo_fedex_shipmentId', false);
		if (!empty($shipmentIds)) {
			echo "<h2>Shipping details</h2>";
			foreach($shipmentIds as $shipmentId) {
				$shipping_return_label = get_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_'.$shipmentId, true);
				$return_shipment_id = get_post_meta($this->order_id, 'wf_woo_fedex_returnShipmetId', true);
				if(!empty($shipping_return_label)){
					$download_url = admin_url('/post.php?wf_fedex_viewReturnlabel='.base64_encode($shipmentId.'|'.$this->order_id) );
					echo '<li style="padding:10px"><strong>Return Shipment Tracking ID:</strong> '.$return_shipment_id;?>
					<a class="button tips" href="<?php echo $download_url; ?>" data-tip="<?php _e('Print Return Label', 'wf-shipping-fedex'); ?>"><?php _e('Print Return Label', 'wf-shipping-fedex'); ?></a>
					</li>
					<?php 
				}else{
					$selected_sevice = $this->wf_get_shipping_service($order);
					$generate_url = home_url("/my-account/view-order/$this->order_id/?generate_fedex_return_label=".base64_encode($shipmentId ."|".$this->order_id) );
					?>
					<li style="padding: 10px">
					<strong>Shipment id: </strong><a style="padding: 5px"><?php echo $shipmentId?></a>
					<input type="hidden" class="fedex_return_service" value="<?php echo $selected_sevice;?>" /><br />
					<?php
					if( $this->return_label_reason_required ) {
						?>
						Reason for Return : <input type="text" id="fedex_return_service_reason_<?php echo $shipmentId ?>" name="fedex_return_service_reason" required />
						<script>
							
							function create_return_label(shipmentId, encoded_shipmentid_order_id) {
								reason_for_return =jQuery("#fedex_return_service_reason_"+shipmentId).val();
								if( reason_for_return == "" ){
									alert('Please provide the reason for generating the return label .');
								}
								else{
									jQuery(".fedex_create_return_shipment").attr("disabled", true);
									window.location.href='?generate_fedex_return_label='+encoded_shipmentid_order_id+'&fedex_return_reason='+escape(reason_for_return);
								}
							}
						</script>
						<button type='button' class='fedex_create_return_shipment' onclick=create_return_label("<?php echo $shipmentId ?>","<?php echo base64_encode($shipmentId.'|'.$this->order_id) ?>") data-tip="<?php _e( 'Generate return label', 'wf-shipping-fedex' ); ?>"> Generate Return Label </button>
						<?php
					}
					else {
						?>
						<a class="button button-primary fedex_create_return_shipment tips" href="<?php echo $generate_url?>" data-tip="<?php _e( 'Generate return label', 'wf-shipping-fedex' ); ?>"><?php _e( 'Generate return label', 'wf-shipping-fedex' ); ?></a>
						<?php
					}
					?>
					</li><?php
				}
			}
		}
	}

	public function generate_fedex_return_label(){

		?>
			<style>
				.ph_go_back_button_my_account_page{
					background-color: #404CC1;
					border: none;
					border-radius: 8px;
					color: white;
					padding: 20px;
					text-align: center;
					text-decoration: none;
					display: inline-block;
					font-size: 16px;
					margin: 4px 2px;
					cursor: pointer;
				}
			</style>
		<?php

		if( empty($_GET['generate_fedex_return_label']) ){
			return false;
		}
		
		$return_params = explode('|', base64_decode($_GET['generate_fedex_return_label']));
		
		if(empty($return_params) || !is_array($return_params) || count($return_params) != 2)
			return;

		$shipment_id 	= $return_params[0];
		$this->order_id =  $return_params[1];
		$return_label	= get_post_meta($this->order_id, 'wf_woo_fedex_returnLabel_'.$shipment_id, true);
		$view_current_order_page_link = get_permalink( wc_get_page_id( 'myaccount' ) ).'view-order/'.$this->order_id;
		// Check whether return label is already generated for this shipment or not, generate only if it is not generated already
		if( ! empty($return_label) ) {
			if( ! headers_sent() && ! $this->debug ) {
				if( function_exists('wc_add_notice') ){
					wc_add_notice( __( 'FedEx Return Label has already been generated for this Shipment ', 'wf-shipping-fedex' ).print_r($shipment_id,true) );
				}
				wp_redirect($view_current_order_page_link);
			}
			else{
				echo __( 'FedEx Return Label has already been generated for this Shipment ', 'wf-shipping-fedex' ).print_r($shipment_id,true);
				echo "<br/><br/><a href='$view_current_order_page_link'><button class='ph_go_back_button_my_account_page'>".__( 'Go Back', 'wf-shipping-fedex' )."</button></a>";
			}
		}
		else{
			// Add return Label Reason to Order note
			if( ! empty($_GET['fedex_return_reason']) ) {
				$order = wc_get_order($this->order_id);
				$order->add_order_note( __('Reason for return of shipment '). $shipment_id .' : '.utf8_encode($_GET['fedex_return_reason']), 0, 1 );

			}
			
			if ( ! class_exists( 'wf_fedex_woocommerce_shipping_admin_helper' ) )
				include_once 'class-wf-fedex-woocommerce-shipping-admin-helper.php';
			
			$woofedexwrapper = new wf_fedex_woocommerce_shipping_admin_helper();
			$this->order = $this->wf_load_order($this->order_id);

			$serviceCode = $this->wf_get_shipping_service( $this->order,false);
			$woofedexwrapper->print_return_label( $shipment_id, $this->order_id, $serviceCode  );
			if( ! $this->debug ){
				if( ! headers_sent() ){
					wp_redirect($view_current_order_page_link);
				}
				else{
					echo "<a href='$view_current_order_page_link'><button class='ph_go_back_button_my_account_page'>".__( 'Go Back', 'wf-shipping-fedex' )."</button></a>";
				}
			}
			else{
				echo "<a href='$view_current_order_page_link'><button class='ph_go_back_button_my_account_page'>".__( 'Go Back', 'wf-shipping-fedex' )."</button></a>";
			}
		}
		exit;
	}

	private function wf_load_order($orderId){
		if (!class_exists('WC_Order')) {
			return false;
		}
		
		if(!class_exists('wf_order')){
			include_once('class-wf-legacy.php');
		}
		return ( WC()->version < '2.7.0' ) ? new WC_Order( $orderId ) : new wf_order( $orderId );    
	}

	private function wf_get_shipping_service($order,$retrive_from_order = false, $shipment_id=false){
		//Origin country cannot initialize from constructor, because global WC is not getting loaded there.
		$origin_country 	= isset( $this->settings['origin_country'] ) ? $this->settings['origin_country'] : WC()->countries->get_base_country() ;

		if( strstr( $origin_country, ':') ) {
			list( $origin_country, $origin_state ) = explode( ':', $origin_country ) ;
		}

		$order = $this->wf_load_order($this->order_id);
		$this->is_international = ( $order->shipping_country != $origin_country ) ? true : false;

		if( !$this->is_international && !empty($this->retun_label_dom_service) ){
			return $this->retun_label_dom_service;	
		}

		if( $this->is_international && !empty($this->retun_label_int_service) ){
			return $this->retun_label_int_service;
		}

		if($retrive_from_order == true){
			$service_code = get_post_meta($order->id, 'wf_woo_fedex_service_code'.$shipment_id, true);
			if(!empty($service_code)) return $service_code;
		}
		
		if(!empty($_GET['service'])){			
		    $service_arr    =   json_decode(stripslashes(html_entity_decode($_GET["service"])));  
			return $service_arr[0];
		}
			
		//TODO: Take the first shipping method. It doesnt work if you have item wise shipping method
		$shipping_methods = $order->get_shipping_methods();
		
		if ( ! $shipping_methods ) {
			return '';
		}
		$shipping_method = array_shift($shipping_methods);
		if( strpos($shipping_method, WF_Fedex_ID)==false ){
			return false;
		}

		return str_replace(WF_Fedex_ID.':', '', $shipping_method['method_id']);
	}
}
new xa_my_account_order_return;