<?php
if( !class_exists('WF_Admin_Options') ){
	class WF_Admin_Options{
		function __construct(){
			$this->freight_classes  =   include( 'data-wf-freight-classes.php' );
			$this->init();
		}

		function init(){
			$this->settings = get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null );

			if( is_admin() ){
				// Add a custome field in product page variation level
				// add_action( 'woocommerce_product_after_variable_attributes', array($this,'wf_variation_settings_fields'), 10, 3 );
				// Save a custome field in product page variation level
				// add_action( 'woocommerce_save_product_variation', array($this,'wf_save_variation_settings_fields'), 10, 2 );

				// Add a custome field in product page variation level
				add_action( 'woocommerce_product_after_variable_attributes', array($this, 'wf_add_custome_product_fields_at_variation'), 10, 3 );
				// Save a custome field in product page variation level
				add_action( 'woocommerce_save_product_variation', array( $this, 'wf_save_custome_product_fields_at_variation'), 10, 2 );

				//add a custome field in product page
				add_action( 'woocommerce_product_options_shipping', array($this,'wf_custome_product_page')  );
				//Saving the values
				add_action( 'woocommerce_process_product_meta', array( $this, 'wf_save_custome_product_fields' ) );
			}

			//add_action( 'woocommerce_product_options_shipping', array($this, 'admin_add_frieght_class'));
			//add_action( 'woocommerce_process_product_meta',       array( $this, 'admin_save_frieght_class' ));

			// add_action( 'woocommerce_product_after_variable_attributes', array($this, 'wf_add_custome_product_fields_at_variation'), 10, 3 );
			// add_action( 'woocommerce_save_product_variation', array( $this, 'wf_save_custome_product_fields_at_variation'), 10, 2 );
		}

		function wf_custome_product_page() {

			?><hr style="border-top: 1px solid #eee;" /><p class="ph_fedex_other_details">FedEx Shipping Details<span class="toggle_symbol" aria-hidden="true"></span></p><div class="ph_fedex_hide_show_product_fields"><?php

			//HS code field
			woocommerce_wp_text_input( array(
				'id' => '_wf_hs_code',
				'label' => __('HS Tariff Number (FedEx)','wf-shipping-fedex'),
				'description' => __('HS is a standardized system of names and numbers to classify products.','wf-shipping-fedex'),
				'desc_tip' => 'true',
				'placeholder' => __('Harmonized Code','wf-shipping-fedex')
			) );

			// Commodity Description
			woocommerce_wp_textarea_input( array(
				'id' => '_ph_commodity_description',
				'label' => __('Commodity Description (FedEx)','wf-shipping-fedex'),
				'description' => __('Enter Commodity Description <br/> NOTE: Commodity description should match the Harmonized Code','wf-shipping-fedex'),
				'desc_tip' => 'true',
				'placeholder' => __('Commodity Description [3 to 450 characters required]','wf-shipping-fedex')
			) );

			//Country of manufacture
			woocommerce_wp_text_input( array(
				'id' => '_wf_manufacture_country',
				'label' => __('Country of Manufacture (FedEx)','wf-shipping-fedex'),
				'description' => __('Country Code of Manufacture','wf-shipping-fedex'),
				'desc_tip' => 'true',
				'placeholder' => __('Country Code','wf-shipping-fedex')
			) );

			if( isset($this->settings['dry_ice_enabled']) && $this->settings['dry_ice_enabled']=='yes' ){
				//dry ice
				woocommerce_wp_checkbox( array(
					'id' => '_wf_dry_ice',
					'label' => __('Dry Ice (FedEx)','wf-shipping-fedex'),
					'description' => __('Enable if this product requires Dry Ice shipment.','wf-shipping-fedex'),
					'desc_tip' => 'true',
				) );
				
				woocommerce_wp_text_input( array(
					'id' => '_wf_dry_ice_weight',
					'data_type'=> 'decimal',
					'label' => __('Dry Ice Weight (FedEx)','wf-shipping-fedex'),
					'description' => __('Enter the weight of Dry Ice.','wf-shipping-fedex'),
					'desc_tip' => 'true',
					'placeholder' => __('Dry Ice Weight','wf-shipping-fedex')
				) );
			}

			// Freight Class
			woocommerce_wp_select(array(
				'id' =>     '_wf_freight_class',
				'label' =>   __('Freight Class (FedEx)','wf-shipping-fedex'),
				'options' => array(''=>__('None'))+$this->freight_classes,
				'description' => __('FedEx Freight class for shipping calculation.','wf-shipping-fedex'),
				'desc_tip' => 'true',
			));

			// Special Services
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_special_service_types',
				'label'     => __( 'Special Services (FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'Select the special service types if applicable .', 'wf-shipping-fedex').'<br />'.__( 'ALCOHOL - Select it, if this product is alcohal.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					null        => __( 'Select Anyone', 'wf-shipping-fedex' ),
					'ALCOHOL'   => __( 'ALCOHOL', 'wf-shipping-fedex' ),
				),
			));

			// Alcohal Recipient Type
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_sst_alcohal_recipient',
				'label'     => __( 'Alcohal Recipient type(FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'Select the special service Recipient types if applicable .', 'wf-shipping-fedex').'<br />'.__( 'CONSUMER - Select, if no license is required for recipient.', 'wf-shipping-fedex' ).'<br />'.__( 'LICENSEE - Select, if license is required for recipient.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					null        => __( 'Select Anyone', 'wf-shipping-fedex' ),
					'CONSUMER'  => __( 'CONSUMER', 'wf-shipping-fedex' ),
					'LICENSEE'  => __( 'LICENSEE', 'wf-shipping-fedex' ),
				),
			));

			// Signature Option PDS-179
			woocommerce_wp_select( array(
				'id'        	=> '_ph_fedex_signature_option',
				'label'     	=> __( 'Delivery Signature(FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'FedEx Web Services selects the appropriate signature option for your shipping service.', 'wf-shipping-fedex'),
				'desc_tip'  	=> true,
				'options'   	=> array(
					null        			=> __( 'Select Anyone', 'wf-shipping-fedex' ),
					'ADULT'	   				=> __( 'Adult', 'wf-shipping-fedex' ),
					'DIRECT'	  			=> __( 'Direct', 'wf-shipping-fedex' ),
					'INDIRECT'	  			=> __( 'Indirect', 'wf-shipping-fedex' ),
					'NO_SIGNATURE_REQUIRED' => __( 'No Signature Required', 'wf-shipping-fedex' ),
					'SERVICE_DEFAULT'	  	=> __( 'Service Default', 'wf-shipping-fedex' ),
				),
			));

			//Pre packed
			woocommerce_wp_checkbox( array(
				'id' => '_wf_fedex_pre_packed',
				'label' => __('Pre packed product (FedEx)','wf-shipping-fedex'),
				'description' => __('Check this if the item comes in boxes. It will consider as a separate package and ship in its own box.', 'wf-shipping-fedex'),
				'desc_tip' => 'true',
			) );

			//Non-Standard Prducts
			woocommerce_wp_checkbox( array(
				'id' => '_wf_fedex_non_standard_product',
				'label' => __('Non-Standard product (FedEx)','wf-shipping-fedex'),
				'description' => __('Check this if the product belongs to Non Standard Container. Non-Stantard product will be charged higher', 'wf-shipping-fedex'),
				'desc_tip' => 'true',
			) );

			//Customs declared value
			woocommerce_wp_text_input( array(
				'id'        => '_wf_fedex_custom_declared_value',
				'data_type' => 'decimal',
				'label'     => __( 'Custom Declared Value (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('This amount will be reimbursed from FedEx if products get damaged and you have opt for Insurance.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( 'Insurance amount FedEx', 'wf-shipping-fedex')
			) );
			
			//Dangerous Goods Checkbox
			woocommerce_wp_checkbox( array(
				'id' => '_dangerous_goods',
				'label' => __('Dangerous Goods (FedEx)','wf-shipping-fedex'),
				'description' => __('Check this to mark the product as a dangerous goods.','wf-shipping-fedex'),
				'desc_tip' => 'true',
			));

			?><div class="ph_fedex_dangerous_goods"><?php

			//Dangerous Goods Regulations
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_dg_regulations',
				'label'     => __( 'Dangerous Goods Regulation (FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'Select the regulation .', 'wf-shipping-fedex').'<br />'.__( 'ADR - European Agreement concerning the International Carriage of Dangerous Goods by Road.', 'wf-shipping-fedex' ).'<br />'.__( 'DOT - U.S. Department of Transportation has primary responsibility for overseeing the transportation in commerce of hazardous materials, commonly called "HazMats".', 'wf-shipping-fedex' ).'<br />'.__( 'IATA - International Air Transport Association Dangerous Goods.', 'wf-shipping-fedex' ).'<br />'.__( 'ORMD - Other Regulated Materials for Domestic transport only.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'DOT'   => __( 'DOT', 'wf-shipping-fedex' ),
					'ADR'   => __( 'ADR', 'wf-shipping-fedex' ),
					'IATA'  => __( 'IATA', 'wf-shipping-fedex' ),
					'ORMD'  => __( 'ORMD', 'wf-shipping-fedex' )
				),
			));

			//Dangerous Goods Accessibility
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_dg_accessibility',
				'label'     => __( 'Dangerous Goods Accessibility (FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'Select the accessibility type .', 'wf-shipping-fedex').'<br />'.__( 'ACCESSIBLE - Dangerous Goods shipments must be accessible to the flight crew in-flight.', 'wf-shipping-fedex' ).'<br />'.__( 'INACCESSIBLE - Inaccessible Dangerous Goods (IDG) do not need to be loaded so they are accessible to the flight crew in-flight.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'INACCESSIBLE'  => __( 'INACCESSIBLE', 'wf-shipping-fedex' ),
					'ACCESSIBLE'    => __( 'ACCESSIBLE', 'wf-shipping-fedex' ),
				),
			));

			?></div><?php

			//Hazmat Products Checkbox
			woocommerce_wp_checkbox( array(
				'id' => '_hazmat_products',
				'label' => __('Hazardous Materials (FedEx)','wf-shipping-fedex'),
				'description' => __('Check this to mark the product as a hazardous materials. Service is available from U.S. origins only (except Alaska and Hawaii)','wf-shipping-fedex'),
				'desc_tip' => 'true',
			));

			?><div class="ph_fedex_hazardous_materials"><?php

			//Hazmat Identification Number
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_id_num',
				'label'     => __( 'Identificaton No. (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('Hazardous material regulatory commodity identifier referred to as Department of Transportation (DOT) location ID number (UN or NA).','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( 'UN1088', 'wf-shipping-fedex'),
			) );

			//Hazmat Packaging Group
			woocommerce_wp_select( array(
				'id'        => '_ph_fedex_hp_packaging_group',
				'label'     => __( 'Packaging Group (FedEx)', 'wf-shipping-fedex'),
				'description'   => __( 'Hazardous material packaging group.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'DEFAULT'   => __( 'DEFAULT', 'ups-woocommerce-shipping' ),
					'I'         => __( 'I', 'ups-woocommerce-shipping' ),
					'II'        => __( 'II', 'ups-woocommerce-shipping' ),
					'III'       => __( 'III', 'ups-woocommerce-shipping' )
				),
			));

			//Hazmat Proper Shipping Name
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_proper_shipping_name',
				'label'     => __( 'Proper Shipping Name (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('Hazardous material proper shipping name. Up to three description lines of 50 characters each are allowed for a HazMat shipment. These description elements are formatted on the OP950 form in 25-character columns (up to 6 printed lines).','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( 'Acetal', 'wf-shipping-fedex')
			) );

			//Hazmat Hazard Class
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_hazard_class',
				'label'     => __( 'Hazard Class (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('DOT hazardous material class or division.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( '3', 'wf-shipping-fedex')
			) );

			//Hazmat Subsidiary Classes
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_subsidiary_classes',
				'label'     => __( 'Subsidiary Classes (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('Hazardous material subsidiary classes.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
			) );

			//Hazmat Label Text
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_label_text',
				'label'     => __( 'Label Text (FedEx)', 'wf-shipping-fedex' ),
				'description'   => __('DOT diamond hazard label type. Can also include limited quantity or exemption number.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder' =>  __( 'FLAMMABLE LIQUID', 'wf-shipping-fedex')
			) );

			?></div></div><?php
			
		}

		// public function wf_variation_settings_fields( $loop, $variation_data, $variation ){
		//  $is_pre_packed_var = get_post_meta( $variation->ID, '_wf_fedex_pre_packed_var', true );
		//  if( empty( $is_pre_packed_var ) ){
		//      $is_pre_packed_var = get_post_meta( wp_get_post_parent_id($variation->ID), '_wf_fedex_pre_packed', true );
		//  }
		//  woocommerce_wp_checkbox( array(
		//      'id' => '_wf_fedex_pre_packed_var[' . $variation->ID . ']',
		//      'label' => __(' Pre packed product(FedEx)', 'wf-shipping-fedex'),
		//      'description' => __('Check this if the item comes in boxes. It will override global product settings', 'wf-shipping-fedex'),
		//      'desc_tip' => 'true',
		//      'value'         => $is_pre_packed_var,
		//  ) );
		// }

		// public function wf_save_variation_settings_fields( $post_id ){
		//  $checkbox = isset( $_POST['_wf_fedex_pre_packed_var'][ $post_id ] ) ? 'yes' : 'no';
		//  update_post_meta( $post_id, '_wf_fedex_pre_packed_var', $checkbox );
		// }

		function wf_save_custome_product_fields( $post_id ) {
			
			//HS code value
			if ( isset( $_POST['_wf_hs_code'] ) ) {
				update_post_meta( $post_id, '_wf_hs_code', esc_attr( $_POST['_wf_hs_code'] ) );
			}
			//Commodity Descriptiom
			if ( isset( $_POST['_ph_commodity_description'] ) ) {
				update_post_meta( $post_id, '_ph_commodity_description', esc_attr( $_POST['_ph_commodity_description'] ) );
			}
			//dryice weight
			if( isset( $_POST['_wf_dry_ice_weight'] )  )
			{
				$dry_ice_weight=$_POST['_wf_dry_ice_weight'];
				update_post_meta( $post_id, '_wf_dry_ice_weight', $dry_ice_weight );
			}
			//dry ice
			$is_dry_ice =  ( isset( $_POST['_wf_dry_ice'] ) && esc_attr($_POST['_wf_dry_ice']=='yes')  ) ? esc_attr($_POST['_wf_dry_ice']) : false;
			update_post_meta( $post_id, '_wf_dry_ice', $is_dry_ice );

			// Country of manufacture
			if ( isset( $_POST['_wf_manufacture_country'] ) ) {
				update_post_meta( $post_id, '_wf_manufacture_country', esc_attr( $_POST['_wf_manufacture_country'] ) );
			}
			
			// Freight Class
			if ( isset( $_POST['_wf_freight_class']) && !is_array($_POST['_wf_freight_class']) ) {
				update_post_meta( $post_id, '_wf_freight_class', esc_attr( $_POST['_wf_freight_class'] ) );
			}

			// Special Service type
			if( isset($_POST['_wf_fedex_special_service_types']) && !is_array($_POST['_wf_fedex_special_service_types']) ){
				update_post_meta( $post_id, '_wf_fedex_special_service_types', $_POST['_wf_fedex_special_service_types'] );
			}
			
			
			// Alcohol recipient type
			if( isset($_POST['_wf_fedex_sst_alcohal_recipient']) && !is_array($_POST['_wf_fedex_sst_alcohal_recipient']) ) {
				update_post_meta( $post_id, '_wf_fedex_sst_alcohal_recipient', $_POST['_wf_fedex_sst_alcohal_recipient'] );
			}

			// Signature Option PDS-179
			if( isset($_POST['_ph_fedex_signature_option']) && !is_array($_POST['_ph_fedex_signature_option']) ) {
				update_post_meta( $post_id, '_ph_fedex_signature_option', $_POST['_ph_fedex_signature_option'] );
			}
			
			//Dangerous Goods
			$dangerous_goods =  ( isset( $_POST['_dangerous_goods'] ) && !is_array($_POST['_dangerous_goods']) && esc_attr($_POST['_dangerous_goods'])=='yes') ? esc_attr($_POST['_dangerous_goods'])  : false;
			update_post_meta( $post_id, '_dangerous_goods', $dangerous_goods );
			

			//Save Dangerous goods regulation
			if( ! empty ($_POST['_wf_fedex_dg_regulations']) && !is_array($_POST['_wf_fedex_dg_regulations']) ) {
				update_post_meta( $post_id, '_wf_fedex_dg_regulations', $_POST['_wf_fedex_dg_regulations'] );
			}

			//Save dangerous goods accessibility
			if( ! empty( $_POST['_wf_fedex_dg_accessibility']) && !is_array($_POST['_wf_fedex_dg_accessibility']) ) {
				update_post_meta( $post_id, '_wf_fedex_dg_accessibility', $_POST['_wf_fedex_dg_accessibility'] );
			}

			// Save Hazmat Products
			$hazmat_products =  ( isset( $_POST['_hazmat_products'] ) && !is_array($_POST['_hazmat_products']) && esc_attr($_POST['_hazmat_products'])=='yes') ? esc_attr($_POST['_hazmat_products'])  : false;
			update_post_meta( $post_id, '_hazmat_products', $hazmat_products );

			// Save Hazmat Identification Number
			if( isset($_POST['_ph_fedex_hp_id_num']) && !is_array($_POST['_ph_fedex_hp_id_num']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_id_num', esc_attr( $_POST['_ph_fedex_hp_id_num'] ) );
			}

			//Save Hazmat Packaging Group
			if( ! empty ($_POST['_ph_fedex_hp_packaging_group']) && !is_array($_POST['_ph_fedex_hp_packaging_group']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_packaging_group', $_POST['_ph_fedex_hp_packaging_group'] );
			}

			// Save Hazmat Proper Shipping Name
			if( isset($_POST['_ph_fedex_hp_proper_shipping_name']) && !is_array($_POST['_ph_fedex_hp_proper_shipping_name']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_proper_shipping_name', esc_attr( $_POST['_ph_fedex_hp_proper_shipping_name'] ) );
			}

			// Save Hazmat Hazard Class
			if( isset($_POST['_ph_fedex_hp_hazard_class']) && !is_array($_POST['_ph_fedex_hp_hazard_class']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_hazard_class', esc_attr( $_POST['_ph_fedex_hp_hazard_class'] ) );
			}

			// Save Hazmat Subsidiary Classes
			if( isset($_POST['_ph_fedex_hp_subsidiary_classes']) && !is_array($_POST['_ph_fedex_hp_subsidiary_classes']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_subsidiary_classes', esc_attr( $_POST['_ph_fedex_hp_subsidiary_classes'] ) );
			}

			// Save Hazmat Label Text
			if( isset($_POST['_ph_fedex_hp_label_text']) && !is_array($_POST['_ph_fedex_hp_label_text']) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_label_text', esc_attr( $_POST['_ph_fedex_hp_label_text'] ) );
			}

			// Pre packed
			if ( isset( $_POST['_wf_fedex_pre_packed']) ) {
				update_post_meta( $post_id, '_wf_fedex_pre_packed', esc_attr( $_POST['_wf_fedex_pre_packed'] ) );
			} else {
				update_post_meta( $post_id, '_wf_fedex_pre_packed', '' );
			}
			
			//non-standard product
			$non_standard_product =  ( isset( $_POST['_wf_fedex_non_standard_product'] ) && !is_array($_POST['_wf_fedex_non_standard_product']) && esc_attr($_POST['_wf_fedex_non_standard_product'])=='yes') ? esc_attr($_POST['_wf_fedex_non_standard_product'])  : false;
			update_post_meta( $post_id, '_wf_fedex_non_standard_product', $non_standard_product );

			// Update the Insurance amount on individual product page
			 if( isset($_POST['_wf_fedex_custom_declared_value'] ) ) {
                update_post_meta( $post_id, '_wf_fedex_custom_declared_value', esc_attr( $_POST['_wf_fedex_custom_declared_value'] ) );
            }
			
		}
		
		// function admin_add_frieght_class() {
		//  woocommerce_wp_select(array(
		//      'id' =>     '_wf_freight_class',
		//      'label' =>   __('Freight Class (FedEx)','wf-shipping-fedex'),
		//      'options' => array(''=>__('None'))+$this->freight_classes,
		//      'description' => __('FedEx Freight class for shipping calculation.','wf-shipping-fedex'),
		//      'desc_tip' => 'true',
		//  ));
		// }

		//Function to add option in products at variation level
		function wf_add_custome_product_fields_at_variation($loop, $variation_data, $variation){
			
			?><hr style="border-top: 1px solid #eee;" /><p class="ph_fedex_var_other_details">FedEx Shipping Details<span class="var_toggle_symbol" aria-hidden="true"></span></p><div class="ph_fedex_hide_show_var_product_fields"><?php

			// Freight Class Dropdown
			woocommerce_wp_select( 
				array( 
					'id'        => '_wf_freight_class[' . $variation->ID . ']',
					'class'     => 'ph_fedex_variation_class_select',
					'label'     => __( 'Freight Class (FedEx)', 'wf-shipping-fedex' ), 
					'value'     => get_post_meta( $variation->ID, '_wf_freight_class', true ),
					'options'   =>  array(''=>__('Default','wf-shipping-fedex'))+$this->freight_classes,
					'description'   => __('Leaving default will inherit parent FedEx Freight class.','wf-shipping-fedex'),
					'desc_tip'  => 'true',
				)
			);
			
			// Special Services
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_special_service_types[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_select',
				'label'     => __( 'Special Services (FedEx)', 'wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_wf_fedex_special_service_types', true ),
				'description'   => __( 'Select the special service types if applicable .', 'wf-shipping-fedex').'<br />'.__( 'ALCOHOL - Select it, if this product is alcohal.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					null        => __( 'Select Anyone', 'wf-shipping-fedex' ),
					'ALCOHOL'   => __( 'ALCOHOL', 'wf-shipping-fedex' ),
				),
			));

			// Alcohal Recipient Type
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_sst_alcohal_recipient[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_select',
				'label'     => __( 'Alcohal Recipient type(FedEx)', 'wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_wf_fedex_sst_alcohal_recipient', true ),
				'description'   => __( 'Select the special service Recipient types if applicable .', 'wf-shipping-fedex').'<br />'.__( 'CONSUMER - Select, if no license is required for recipient.', 'wf-shipping-fedex' ).'<br />'.__( 'LICENSEE - Select, if license is required for recipient.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					null        => __( 'Select Anyone', 'wf-shipping-fedex' ),
					'CONSUMER'  => __( 'CONSUMER', 'wf-shipping-fedex' ),
					'LICENSEE'  => __( 'LICENSEE', 'wf-shipping-fedex' ),
				),
			));

			// Signature Option PDS-179
			woocommerce_wp_select( array(
				'id'        	=> '_ph_fedex_signature_option[' . $variation->ID . ']',
				'class'     	=> 'ph_fedex_variation_class_select',
				'label'     	=> __( 'Delivery Signature(FedEx)', 'wf-shipping-fedex'),
				'value'     	=> get_post_meta( $variation->ID, '_ph_fedex_signature_option', true ),
				'description'   => __( 'FedEx Web Services selects the appropriate signature option for your shipping service.', 'wf-shipping-fedex'),
				'desc_tip'  	=> true,
				'options'   	=> array(
					null        			=> __( 'Select Anyone', 'wf-shipping-fedex' ),
					'ADULT'	   				=> __( 'Adult', 'wf-shipping-fedex' ),
					'DIRECT'	  			=> __( 'Direct', 'wf-shipping-fedex' ),
					'INDIRECT'	  			=> __( 'Indirect', 'wf-shipping-fedex' ),
					'NO_SIGNATURE_REQUIRED' => __( 'No Signature Required', 'wf-shipping-fedex' ),
					'SERVICE_DEFAULT'	  	=> __( 'Service Default', 'wf-shipping-fedex' ),
				),
			));

			// Pre-Packed
			$is_pre_packed_var = get_post_meta( $variation->ID, '_wf_fedex_pre_packed_var', true );
			if( empty( $is_pre_packed_var ) ){
				$is_pre_packed_var = get_post_meta( wp_get_post_parent_id($variation->ID), '_wf_fedex_pre_packed', true );
			}
			woocommerce_wp_checkbox( array(
				'id' => '_wf_fedex_pre_packed_var[' . $variation->ID . ']',
				'label' => __(' Pre packed product(FedEx)', 'wf-shipping-fedex'),
				'description' => __('Check this if the item comes in boxes. It will override global product settings', 'wf-shipping-fedex'),
				'desc_tip' => 'true',
				'value'         => $is_pre_packed_var,
			) );

			//Non-Standard Prducts
			woocommerce_wp_checkbox(
				array(
					'id'            => '_wf_fedex_non_standard_product[' . $variation->ID . ']',
					'label'         => __('Non-Standard product (FedEx)','wf-shipping-fedex'),
					'value'         => get_post_meta( $variation->ID, '_wf_fedex_non_standard_product', true ),
					'description'   => __('Check this if the product belongs to Non Standard Container. Non-Stantard product will be charged heigher', 'wf-shipping-fedex'),
					'desc_tip'      => 'true',
				)
			);
			
			//Dangerous Goods Checkbox
			woocommerce_wp_checkbox( array(
				'id'        => '_dangerous_goods[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_dangerous_goods',
				'label'     => __(' Dangerous Goods (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_dangerous_goods', true ),
				'description'   => __('Check this to mark the product as a dangerous goods.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
			));
			
			?><div class="ph_fedex_var_dangerous_goods"><?php

			//Dangerous Goods Regulations
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_dg_regulations[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_select',
				'label'     => __( 'Dangerous Goods Regulation (FedEx)', 'wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_wf_fedex_dg_regulations', true ),
				'description'   => __( 'Select the regulation .', 'wf-shipping-fedex').'<br />'.__( 'ADR - European Agreement concerning the International Carriage of Dangerous Goods by Road.', 'wf-shipping-fedex' ).'<br />'.__( 'DOT - U.S. Department of Transportation has primary responsibility for overseeing the transportation in commerce of hazardous materials, commonly called "HazMats".', 'wf-shipping-fedex' ).'<br />'.__( 'IATA - International Air Transport Association Dangerous Goods.', 'wf-shipping-fedex' ).'<br />'.__( 'ORMD - Other Regulated Materials for Domestic transport only.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'DOT'   => __( 'DOT', 'wf-shipping-fedex' ),
					'ADR'   => __( 'ADR', 'wf-shipping-fedex' ),
					'IATA'  => __( 'IATA', 'wf-shipping-fedex' ),
					'ORMD'  => __( 'ORMD', 'wf-shipping-fedex' )
				),
			));
			
			//Dangerous Goods Accessibility
			woocommerce_wp_select( array(
				'id'        => '_wf_fedex_dg_accessibility[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_select',
				'label'     => __( 'Dangerous Goods Accessibility (FedEx)', 'wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_wf_fedex_dg_accessibility', true ),
				'description'   => __( 'Select the accessibility type .', 'wf-shipping-fedex').'<br />'.__( 'ACCESSIBLE - Dangerous Goods shipments must be accessible to the flight crew in-flight.', 'wf-shipping-fedex' ).'<br />'.__( 'INACCESSIBLE - Inaccessible Dangerous Goods (IDG) do not need to be loaded so they are accessible to the flight crew in-flight.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'INACCESSIBLE'  => __( 'INACCESSIBLE', 'wf-shipping-fedex' ),
					'ACCESSIBLE'    => __( 'ACCESSIBLE', 'wf-shipping-fedex' ),
				),
			));

			?></div><?php

			//Hazmat Products Checkbox
			woocommerce_wp_checkbox( array(
				'id' => '_hazmat_products[' . $variation->ID . ']',
				'class' => 'ph_fedex_variation_hazmat_product',
				'label' => __('Hazardous Materials (FedEx)','wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_hazmat_products', true ),
				'description' => __('Check this to mark the product as a hazardous materials. Service is available from U.S. origins only (except Alaska and Hawaii)','wf-shipping-fedex'),
				'desc_tip' => 'true',
			));

			?><div class="ph_fedex_var_hazardous_materials"><?php

			//Hazmat Identification Number
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_id_num[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_text',
				'label'     => __( 'Identificaton No. (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_id_num', true ),
				'description'   => __('Hazardous material regulatory commodity identifier referred to as Department of Transportation (DOT) location ID number (UN or NA).','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( 'UN1088', 'wf-shipping-fedex')
			) );

			//Hazmat Packaging Group
			woocommerce_wp_select( array(
				'id'        => '_ph_fedex_hp_packaging_group[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_select',
				'label'     => __( 'Packaging Group (FedEx)', 'wf-shipping-fedex'),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_packaging_group', true ),
				'description'   => __( 'Hazardous material packaging group.', 'wf-shipping-fedex' ),
				'desc_tip'  => true,
				'options'   => array(
					'DEFAULT'   => __( 'DEFAULT', 'ups-woocommerce-shipping' ),
					'I'         => __( 'I', 'ups-woocommerce-shipping' ),
					'II'        => __( 'II', 'ups-woocommerce-shipping' ),
					'III'       => __( 'III', 'ups-woocommerce-shipping' )
				),
			));

			//Hazmat Proper Shipping Name
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_proper_shipping_name[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_text',
				'label'     => __( 'Proper Shipping Name (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_proper_shipping_name', true ),
				'description'   => __('Hazardous material proper shipping name. Up to three description lines of 50 characters each are allowed for a HazMat shipment. These description elements are formatted on the OP950 form in 25-character columns (up to 6 printed lines).','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( 'Acetal', 'wf-shipping-fedex')
			) );

			//Hazmat Hazard Class
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_hazard_class[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_text',
				'label'     => __( 'Hazard Class (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_hazard_class', true ),
				'description'   => __('DOT hazardous material class or division.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder'   => __( '3', 'wf-shipping-fedex')
			) );

			//Hazmat Subsidiary Classes
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_subsidiary_classes[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_text',
				'label'     => __( 'Subsidiary Classes (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_subsidiary_classes', true ),
				'description'   => __('Hazardous material subsidiary classes.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
			) );

			//Hazmat Label Text
			woocommerce_wp_text_input( array(
				'id'        => '_ph_fedex_hp_label_text[' . $variation->ID . ']',
				'class'     => 'ph_fedex_variation_class_text',
				'label'     => __( 'Label Text (FedEx)', 'wf-shipping-fedex' ),
				'value'     => get_post_meta( $variation->ID, '_ph_fedex_hp_label_text', true ),
				'description'   => __('DOT diamond hazard label type. Can also include limited quantity or exemption number.','wf-shipping-fedex'),
				'desc_tip'  => 'true',
				'placeholder' =>  __( 'FLAMMABLE LIQUID', 'wf-shipping-fedex')
			) );

			?></div></div><?php

		}

		// function admin_save_frieght_class( $post_id ) {
		//  if ( isset( $_POST['_wf_freight_class'] ) ) {
		//      update_post_meta( $post_id, '_wf_freight_class', esc_attr( $_POST['_wf_freight_class'] ) );
		//  }
		// }

		function wf_save_custome_product_fields_at_variation( $post_id ) {
			
			$select = $_POST['_wf_freight_class'][ $post_id ];
			if( ! empty( $select ) ) {
				update_post_meta( $post_id, '_wf_freight_class', esc_attr( $select ) );
			}
			
			// Save Special service types
			if( isset($_POST['_wf_fedex_special_service_types'][$post_id]) ) {
				update_post_meta( $post_id, '_wf_fedex_special_service_types', $_POST['_wf_fedex_special_service_types'][$post_id] );
			}
			
			// Save alcohal recipient types
			if( isset($_POST['_wf_fedex_sst_alcohal_recipient'][$post_id]) ) {
				update_post_meta( $post_id, '_wf_fedex_sst_alcohal_recipient', $_POST['_wf_fedex_sst_alcohal_recipient'][$post_id] );
			}

			// Signature Option PDS-179
			if( isset($_POST['_ph_fedex_signature_option'][$post_id]) ) {
				update_post_meta( $post_id, '_ph_fedex_signature_option', $_POST['_ph_fedex_signature_option'][$post_id] );
			}
			
			// Save dangerous goods options for variation
			$dangerous_goods =  ( isset( $_POST['_dangerous_goods'][$post_id] ) && esc_attr($_POST['_dangerous_goods'][$post_id])=='yes') ? esc_attr($_POST['_dangerous_goods'][$post_id])  : false;
			update_post_meta( $post_id, '_dangerous_goods', $dangerous_goods );
			
			// Save dangerous goods regulations for variation
			if( ! empty($_POST['_wf_fedex_dg_regulations'][$post_id]) ) {
				update_post_meta( $post_id, '_wf_fedex_dg_regulations', $_POST['_wf_fedex_dg_regulations'][$post_id] );
			}
			
			// Save dangerous goods accessibility for variation
			if( ! empty($_POST['_wf_fedex_dg_accessibility'][$post_id]) ) {
				update_post_meta( $post_id, '_wf_fedex_dg_accessibility', $_POST['_wf_fedex_dg_accessibility'][$post_id] );
			}

			// Pre-packed
			$checkbox = isset( $_POST['_wf_fedex_pre_packed_var'][ $post_id ] ) ? 'yes' : 'no';
			update_post_meta( $post_id, '_wf_fedex_pre_packed_var', $checkbox );

			// Save Non-Standard product for variation
			$non_standard_product =  ( isset( $_POST['_wf_fedex_non_standard_product'][$post_id] ) && esc_attr($_POST['_wf_fedex_non_standard_product'][$post_id])=='yes') ? esc_attr($_POST['_wf_fedex_non_standard_product'][$post_id])  : false;
			update_post_meta( $post_id, '_wf_fedex_non_standard_product', $non_standard_product );

			// Save Hazmat Products
			$hazmat_products =  ( isset( $_POST['_hazmat_products'][$post_id] ) && esc_attr($_POST['_hazmat_products'][$post_id])=='yes') ? esc_attr($_POST['_hazmat_products'][$post_id])  : false;
			update_post_meta( $post_id, '_hazmat_products', $hazmat_products );

			// Save Hazmat Identification Number
			if( isset($_POST['_ph_fedex_hp_id_num'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_id_num', esc_attr( $_POST['_ph_fedex_hp_id_num'][$post_id] ) );
			}

			//Save Hazmat Packaging Group
			if( ! empty ($_POST['_ph_fedex_hp_packaging_group'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_packaging_group', $_POST['_ph_fedex_hp_packaging_group'][$post_id] );
			}

			// Save Hazmat Proper Shipping Name
			if( isset($_POST['_ph_fedex_hp_proper_shipping_name'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_proper_shipping_name', esc_attr( $_POST['_ph_fedex_hp_proper_shipping_name'][$post_id] ) );
			}

			// Save Hazmat Hazard Class
			if( isset($_POST['_ph_fedex_hp_hazard_class'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_hazard_class', esc_attr( $_POST['_ph_fedex_hp_hazard_class'][$post_id] ) );
			}

			// Save Hazmat Subsidiary Classes
			if( isset($_POST['_ph_fedex_hp_subsidiary_classes'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_subsidiary_classes', esc_attr( $_POST['_ph_fedex_hp_subsidiary_classes'][$post_id] ) );
			}

			// Save Hazmat Label Text
			if( isset($_POST['_ph_fedex_hp_label_text'][$post_id] ) ) {
				update_post_meta( $post_id, '_ph_fedex_hp_label_text', esc_attr( $_POST['_ph_fedex_hp_label_text'][$post_id] ) );
			}

		}
	}
	new WF_Admin_Options();
}
