<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings 			= apply_filters( 'xa_fedex_settings',get_option( 'woocommerce_'.WF_Fedex_ID.'_settings', null ) );
$saturday_pickup 	= ( isset($settings['saturday_pickup']) && !empty($settings['saturday_pickup']) && $settings['saturday_pickup'] == 'yes' ) ? true : false;
$default_invoice_commodity_value	= ( isset($settings['discounted_price']) && !empty($settings['discounted_price']) && $settings['discounted_price'] == 'yes' ) ? 'discount_price' : 'declared_price';

if( $saturday_pickup ) {

	$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
} else {
	$working_days = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri' );
}

$freight_classes = include( 'data-wf-freight-classes.php' );
$smartpost_hubs  = include( 'data-wf-smartpost-hubs.php' );

$ship_from_address_option = array(
				'origin_address' => __('Origin Address', 'wf-shipping-fedex'),
				'shipping_address' => __('Shipping Address', 'wf-shipping-fedex')
				);
$ship_from_address_options = apply_filters('wf_filter_label_ship_from_address_options', $ship_from_address_option);

$auto_email_label_option 	= array(
							'shipper' 	=> __( 'Shipper', 'wf-shipping-fedex' ),
							'customer'	=> __( 'Customer', 'wf-shipping-fedex' ),
						);
$auto_email_label_options 	= apply_filters( 'ph_fedex_filter_label_send_in_email_to_options', $auto_email_label_option );

$pickup_start_time_options	=	array();
foreach(range(8,18,0.5) as $pickup_start_time){ // Pickup ready time must contain a time between 08:00am and 06:00pm
	$pickup_start_time_options[(string)$pickup_start_time]	=	date("H:i",strtotime(date('Y-m-d'))+3600*$pickup_start_time);
}

$pickup_close_time_options	=	array();
foreach(range(8.5,24,0.5) as $pickup_close_time){ // Pickup ready time must contain a time between 08:00am and 06:00pm
	$pickup_close_time_options[(string)$pickup_close_time]	=	date("H:i",strtotime(date('Y-m-d'))+3600*$pickup_close_time);
}


$wc_countries   = new WC_Countries();
// This function will not support prior to WC 2.2
$country_list   = $wc_countries->get_countries();
global $woocommerce;
array_unshift( $country_list, "" );

$services = include('data-wf-service-codes.php');
$int_services = array();
$dom_services = array();
foreach ($services as $key => $value) {
	if( strpos($key, 'INTERNATIONAL') !== false ){
		$int_services = array_merge($int_services, array($key=>$value));
	}
	else {
		$dom_services = array_merge($dom_services, array($key=>$value));
	}
}
$shipping_type	= ( isset($settings['fedex_duties_and_taxes_rate']) && !empty($settings['fedex_duties_and_taxes_rate']) && $settings['fedex_duties_and_taxes_rate'] == 'yes' ) ? 'DUTIES_AND_TAXES' : 'NET_CHARGE';

/**
 * Array of settings
 */
return array(
	'tabs_wrapper'=>array(
		'type'=>'settings_tabs'
	),

	'api'					=> array(
		'title'			  => __( 'Generic API Settings', 'wf-shipping-fedex' ),
		'type'			   => 'title',
		'description'		=> __( 'Get your <a href="https://www.pluginhive.com/register-fedex-account-get-developer-test-credentials/" target="_blank"><strong>FedEx Developer Key</strong></a> and <a href="https://www.pluginhive.com/get-fedex-production-credentials-enable-shipping-labels/" target="_blank"><strong>FedEx Production Key</strong></a> to use this plugin.', 'wf-shipping-fedex' ),
		'class'			=>'fedex_general_tab',
	),
	'account_number'		   => array(
		'title'		   => __( 'FedEx Account Number', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	 => '',
		'default'		 => '',
		'class'			=>'fedex_general_tab',
	),
	'meter_number'		   => array(
		'title'		   => __( 'FedEx Meter Number', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	 => '',
		'default'		 => '',
		'class'			=>'fedex_general_tab',

	),
	'api_key'		   => array(
		'title'		   => __( 'Web Services Key', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	 => '',
		'default'		 => '',
		'class'			=>'fedex_general_tab',
		'custom_attributes' => array('autocomplete' => 'off'),
	),
	'api_pass'		   => array(
		'title'		   => __( 'Web Services Password', 'wf-shipping-fedex' ),
		'type'			=> 'password',
		'description'	 => '',
		'default'		 => '',
		'class'			=>'fedex_general_tab',
		'custom_attributes' => array('autocomplete' => 'off'),
	),
	'production'	  => array(
		'title'		   => __( 'Production Key', 'wf-shipping-fedex' ),
		'label'		   => __( 'This is a FedEx Production Key', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'desc_tip'	=> true,
		'class'			=>'fedex_general_tab',
		'description'	 => __( 'If this is a production API key and not a developer key, check this box.', 'wf-shipping-fedex' ),
	),

	'validate_credentials' => array(
		'type'			=> 'validate_button',
	),

	'debug'	  => array(
		'title'		   => __( 'Debug Mode', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'desc_tip'	=> true,
		'description'	 => __( 'Enable debug mode to show debugging information on the cart/checkout.', 'wf-shipping-fedex' ),
		'class'			=>'fedex_general_tab',
	),
	'silent_debug'	  => array(
		'title'		   => __( 'Silent Debug Mode', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'desc_tip'	=> true,
		'description'	 => __( 'Enable silent debug mode to create debug information without showing debugging information on the cart/checkout.', 'wf-shipping-fedex' ),
		'class'			=>'fedex_general_tab ph_fedex_silent_debug',
	),
	'dimension_weight_unit' => array(
			'title'		   => __( 'Dimension/Weight Unit', 'wwf-shipping-fedex' ),
			'label'		   => __( 'This unit will be passed to FedEx.', 'wf-shipping-fedex' ),
			'type'			=> 'select',
			'default'		 => 'LBS_IN',
			'class'		   => 'wc-enhanced-select fedex_general_tab',
			'desc_tip'	=> true,
			'description'	 => 'Product dimensions and weight will be converted to the selected unit and will be passed to FedEx.',
			'options'		 => array(
				'LBS_IN'	=> __( 'Pounds & Inches', 'wf-shipping-fedex'),
				'KG_CM' 	=> __( 'Kilograms & Centimeters', 'wf-shipping-fedex')			
			)
	),
	'residential'	  => array(
		'title'		   => __( 'Residential Delivery', 'wf-shipping-fedex' ),
		'label'		   => __( 'Default to residential delivery.', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'desc_tip'	=> true,
		'class'		=>'fedex_general_tab',
		'description'	 => __( 'Enables Residential Delivery and validates the shipping address automatically (if your FedEx Account has this functionality enabled).', 'wf-shipping-fedex' ),
	),
	'insure_contents'	  => array(
		'title'	   => __( 'Insurance', 'wf-shipping-fedex' ),
		'label'	   => __( 'Enable Insurance', 'wf-shipping-fedex' ),
		'type'		=> 'checkbox',
		'default'	 => 'yes',
		'class'			=>'fedex_general_tab',
		'desc_tip'	=> true,
		'description' => __( 'Sends the package value to FedEx for insurance. SmartPost shipments will cover upto $100 only.', 'wf-shipping-fedex' ),
	),
	
	'ship_from_address'   => array(
		'title'		   => __( 'Ship From Address Preference', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_general_tab',
		'default'		 => 'origin_address',
		'options'		 => $ship_from_address_options,
		'description'	 => __( 'Change the preference of Ship From Address printed on the label. You can make  use of Billing Address from Order admin page, if you ship from a different location other than shipment origin address given below.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true
	),
	'origin'		   => array(
		'title'		   => __( 'Origin Zipcode', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'desc_tip'	=> true,
		'class'	=> 'fedex_general_tab',
		'description'	 => __( 'Enter postcode for the <strong>Shipper</strong>.', 'wf-shipping-fedex' ),
		'default'		 => ''
	),
	'shipper_person_name'		   => array(
			'title'		   => __( 'Shipper Person Name', 'wf-shipping-fedex' ),
			'type'			=> 'text',
			'default'		 => '',
			'desc_tip'	=> true,
			'class'	=> 'fedex_general_tab',
			'description'	 => 'Required for label Printing'			
	),	
	'shipper_company_name'		   => array(
			'title'		   => __( 'Shipper Company Name', 'wf-shipping-fedex' ),
			'type'			=> 'text',
			'default'		 => ''	,
			'desc_tip'	=> true,
			'class'	=> 'fedex_general_tab',
			'description'	 => 'Required for label Printing'
	),	
	'shipper_phone_number'		   => array(
			'title'		   => __( 'Shipper Phone Number', 'wf-shipping-fedex' ),
			'type'			=> 'text',
			'default'		 => ''	,
			'desc_tip'	=> true,
			'class'	=> 'fedex_general_tab',
			'description'	 => 'Required for label Printing'
	),
	'shipper_email'		   => array(
			'title'		   => __( 'Shipper Email', 'wf-shipping-fedex' ),
			'type'			=> 'text',
			'default'		 => ''	,
			'desc_tip'	=> true,
			'class'	=> 'fedex_general_tab',
			'description'	 => 'Required for sending email notification'
	),
	//freight_shipper_street
	'frt_shipper_street'		   => array(
		'title'		   => __( 'Shipper Street Address', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
			'class'	=> 'fedex_general_tab',
		'description'	 => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
	),
	'shipper_street_2'		   => array(
		'title'		   => __( 'Shipper Street Address 2', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'class'	=> 'fedex_general_tab',
		'description'	 => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
	),
	'freight_shipper_city'		   => array(
		'title'		   => __( 'Shipper City', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'class'	=> 'fedex_general_tab',
		'description'	 => 'Required for label Printing. And should be filled if LTL Freight is enabled.'
	),
    'origin_country'    => array(
		'type'                => 'single_select_country',
	),
	'shipper_residential' 	=> array(
		'title'		   => __( 'Shipper Address is Residential', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'	=> 'fedex_general_tab',
		'default'		 => 'no'
	),
	'charges_payment_type'   => array(
		'title'		   => __( 'Shipping Charges', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip'	=> true,
		'description'	 => 'Choose who is going to pay shipping and customs charges. Please fill Third Party settings below if Third Party is choosen. It will override freight shipement also',
		'default'		 => 'SENDER',
		'class'		   => 'wc-enhanced-select fedex_general_tab',
		'options'		 => array(
			'SENDER' 							  	=> __( 'Sender', 						'wf-shipping-fedex'),
			//'RECIPIENT' 							  	=> __( 'Recipient', 						'wf-shipping-fedex'),
			'THIRD_PARTY' 							  	=> __( 'Third Party', 						'wf-shipping-fedex'),
		)				
	),
	'shipping_payor_acc_no'	=> array(
		'title'		   => __( 'Third party Account Number', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'description'	 => 'Third Party Account Number. Required if third party payment selected',
	),
	'shipping_payor_cname'	 => array(
		'title'		   => __( 'Contact Person', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Contact Person. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	//shipping_payor_company
	'shipp_payor_company'   => array(
		'title'		   => __( 'Company', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Company. Required if third party payment selected',
		'desc_tip'		  => true,
	),
	'shipping_payor_phone'	 => array(
		'title'		   => __( 'Contact Number', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Contact Number. Required if third party payment selected',
		'desc_tip'		  => true,
	),
	'shipping_payor_email'	 => array(
		'title'		   => __( 'Contact Email', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Contact Email. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	//shipping_payor_address1
	'shipp_payor_address1'   => array(
		'title'		   => __( 'Address Line 1', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Address Line 1. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	//shipping_payor_address2
	'shipp_payor_address2'   => array(
		'title'		   => __( 'Address Line 2', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Address Line 2. Required if third party payment selected',
		'desc_tip'		  => true,
	),
	'shipping_payor_city'	   => array(
		'title'		   => __( 'City', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer City. Required if third party payment selected',
		'desc_tip'		  => true,
	),
	'shipping_payor_state'	   => array(
		'title'		   => __( 'State Code', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer State Code. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	//shipping_payor_postal_code
	'shipping_payor_zip' => array(
		'title'		   => __( 'Postal Code', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp fedex_general_tab',
		'type'			=> 'text',
		'default'		 => '',
		'description'	 => 'Third Party Payer Postal Code. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	//shipping_payor_country
	'shipp_payor_country'	=> array(
		'title'		   => __( 'Country', 'wf-shipping-fedex' ),
		'class'			  => 'thirdparty_grp wc-enhanced-select fedex_general_tab',
		'type'			=> 'select',
		'default'		 => '',
		'options'		  => $country_list,
		'description'	 => 'Third Party Payer Country. Required if third party payment selected',
		'desc_tip'		  => true,
	),

	'alternate_return_address'	=>	array(
		'title'			=>	__( 'Display Alternate Return Address on Label', 'wf-shipping-fedex' ),
		'label'			=>	__( 'Enable', 'wf-shipping-fedex'),
		'description'	=>	__( 'Alternate return address option that allows you to display different address on the shipping label. For example, if you send a package that is undeliverable, you may use this option to display your returns processing facility address so that FedEx will return the package to that address instead of your shipping facility address.', 'wf-shipping-fedex'),
		'desc_tip'		=> true,
		'type'			=>	'checkbox',
		'default'		=>	'no',
		'class'			=> 'fedex_general_tab',
	),
	'billing_as_alternate_return_address'	=>	array(
		'title'			=>	__( 'Billing Address as Alternate Return Address', 'wf-shipping-fedex' ),
		'label'			=>	__( 'Enable', 'wf-shipping-fedex'),
		'type'			=>	'checkbox',
		'default'		=>	'no',
		'class'			=> 'fedex_general_tab',
	),
	'alt_return_streetline'  => array(
		'title' 		=> __( 'Alternate Return Address Line', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'default' 		=> '',
		'class' 		=> 'fedex_general_tab ph_fedex_alt_return_address'
	),
	'alt_return_city'	  	  => array(
		'title' 		=> __( 'Alternate Return City', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'default' 		=> '',
		'class' 		=>	'fedex_general_tab ph_fedex_alt_return_address'
	),
	'alt_return_country_state'	=> array(
		'type'			=> 'alt_return_country_state',
	),
	'alt_return_custom_state'		=> array(
		'title' 		=> __( 'Alternate Return State Code', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'default'		=> '',
		'class'			=> 'fedex_general_tab ph_fedex_alt_return_address'
	),
	'alt_return_postcode'	 => array(
		'title' 		=> __( 'Alternate Return Zipcode', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'default'		=> '',
		'class'			=> 'fedex_general_tab ph_fedex_alt_return_address'
	),
	'fedex_working_days' 	=> array(
		'title'			=> __( 'Working Days', 'wf-shipping-fedex' ),
		'type'			=> 'multiselect',
		'desc_tip'		=> true,
		'description'	=> __( 'Select the Working Days. This will be used for Shipping Rates, Labels and Pickup.', 'wf-shipping-fedex' ),
		'class'			=> 'fedex_general_tab chosen_select',
		'css'			=> 'width: 400px;',
		'default'		=> $working_days,
		'options'		=> array( 'Sun'=>'Sunday', 'Mon'=>'Monday','Tue'=>'Tuesday', 'Wed'=>'Wednesday', 'Thu'=>'Thursday', 'Fri'=>'Friday', 'Sat'=>'Saturday' ),
	),
	'client_side_reset'	  => array(
		'title'	   => __( 'Clear Data & Recreate Shipment', 'wf-shipping-fedex' ),
		'label'	   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'		=> 'checkbox',
		'default'	 => 'yes',
		'class'			=>'fedex_general_tab',
		'desc_tip'	=> true,
		'description' => __( 'By enabling this option you can delete the shipment from the order page and thereby recreate the shipping labels.', 'wf-shipping-fedex' ),
	),

	'title_special_services'	=> array(
		'title'		   => __( 'Special Services', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_special_services_tab',
		'description'	 => __( 'Configure special services related setting.', 'wf-shipping-fedex' ),
	),
	'signature_option'	 => array(
		'title'		   => __( 'Delivery Signature', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => '',
		'class'		   => 'wc-enhanced-select fedex_special_services_tab',
		'desc_tip'		=> true,
		'options'		 => array(
			''	   				=> __( '-Select one-', 'wf-shipping-fedex' ),
			'ADULT'	   			=> __( 'Adult', 'wf-shipping-fedex' ),
			'DIRECT'	  			=> __( 'Direct', 'wf-shipping-fedex' ),
			'INDIRECT'	  		=> __( 'Indirect', 'wf-shipping-fedex' ),
			'NO_SIGNATURE_REQUIRED' => __( 'No Signature Required', 'wf-shipping-fedex' ),
			'SERVICE_DEFAULT'	  	=> __( 'Service Default', 'wf-shipping-fedex' ),
		),
		'description'	 => __( 'FedEx Web Services selects the appropriate signature option for your shipping service.', 'wf-shipping-fedex' )
	),
	'smartpost_hub'		   => array(
		'title'		   => __( 'FedEx SmartPost Hub', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_special_services_tab',
		'description'	 => __( 'Only required if using SmartPost.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'default'		 => '',
		'options'		 => $smartpost_hubs
	),
	'indicia'   => array(
		'title'		   => __( 'Indicia', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip'	=> true,
		'description'	 => 'Applicable only for SmartPost. Ex: Parcel Select option requires weight of at-least 1LB. Automatic will choose PRESORTED STANDARD if the weight is less than 1lb and PARCEL SELECT if the weight is more than 1lb',
		'default'		 => 'PARCEL_SELECT',
		'class'		   => 'wc-enhanced-select fedex_special_services_tab',
		'options'		 => array(
			'MEDIA_MAIL'		 => __( 'MEDIA MAIL', 'wf-shipping-fedex' ),
			'PARCEL_RETURN'	=> __( 'PARCEL RETURN', 'wf-shipping-fedex' ),
			'PARCEL_SELECT'	=> __( 'PARCEL SELECT', 'wf-shipping-fedex' ),
			'PRESORTED_BOUND_PRINTED_MATTER' => __( 'PRESORTED BOUND PRINTED MATTER', 'wf-shipping-fedex' ),
			'PRESORTED_STANDARD' => __( 'PRESORTED STANDARD', 'wf-shipping-fedex' ),
			'AUTOMATIC' => __( 'AUTOMATIC', 'wf-shipping-fedex' )
		),
	),

	//shipping_customs_duties_payer
	'customs_duties_payer'  => array(
		'title' 		=> __( 'Customs Duties Payer', 'wf-shipping-fedex' ),
		'type' 			=> 'select',
		'desc_tip' 		=> true,
		'description' 	=> 'Select customs duties payer',
		'default' 		=> 'SENDER',
		'class' 		=> 'wc-enhanced-select fedex_special_services_tab',
		'options'		=> array(
			'SENDER' 	  			=> __( 'Sender', 'wf-shipping-fedex'),
			'RECIPIENT'	  			=> __( 'Recipient', 'wf-shipping-fedex'),
			'THIRD_PARTY'	  		=> __( 'Third Party (Broker)', 'wf-shipping-fedex'),
			'THIRD_PARTY_ACCOUNT'	=> __( 'Third Party', 'wf-shipping-fedex'),
		)				
	),

	'third_party_acc_no' 	=> array(
		'title' 		=> __( 'Third Party Account number', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'class' 		=> 'third_party_grp fedex_special_services_tab',
		'default' 		=> '',
		'desc_tip' 		=> true,
		'description' 	=> 'Third Party Account number'			
	),
	'broker_acc_no'		   => array(
		'title'		   => __( 'Broker Account number', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			  => 'broker_grp fedex_special_services_tab',
		'default'		 => '',
		'desc_tip'	=> true,
		'description'	 => 'Broker account number'			
	),	
	'broker_name'		   => array(
		'title'		   => __( 'Broker name', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'description'	 => 'Broker name'			
	),	
	'broker_company'		   => array(
		'title'		   => __( 'Broker Company name', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'description'	 => 'Broker Company Name'			
	),	
	'broker_phone'		   => array(
		'title'		   => __( 'Broker phone number', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
		'description'	 => 'Broker phone number'			
	),	
	'broker_email'		   => array(
		'title'		   => __( 'Brocker Email Address', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),	
	'broker_address'		   => array(
		'title'		   => __( 'Broker Address', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),	
	'broker_city'		   => array(
		'title'		   => __( 'Broker City', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),	
	'broker_state'		   => array(
		'title'		   => __( 'Broker State', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),	
	'broker_zipcode'		   => array(
		'title'		   => __( 'Zip Code', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),	
	'broker_country'		   => array(
		'title'		   => __( 'Country Code', 'wf-shipping-fedex' ),
		'class'			  => 'broker_grp fedex_special_services_tab',
		'type'			=> 'text',
		'default'		 => '',
		'desc_tip'	=> true,
	),
	'dropoff_type'  => array(
		'title' 		=> __( 'Dropoff Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip' 		=> true,
		'description' 	=> 'Select the option that identifies the method by which the package is to be tendered to FedEx.',
		'default' 		=> 'REGULAR_PICKUP',
		'class' 		=> 'wc-enhanced-select fedex_special_services_tab',
		'options' 		=> array(
			'BUSINESS_SERVICE_CENTER' 	=> __( 'Business Service Center', 'wf-shipping-fedex'),
			'DROP_BOX' 					=> __( 'Drop Box', 'wf-shipping-fedex'),
			'REGULAR_PICKUP' 			=> __( 'Regular Pickup', 'wf-shipping-fedex'),
			'REQUEST_COURIER' 			=> __( 'Request Courier', 'wf-shipping-fedex'),
			'STATION' 					=> __( 'Station', 'wf-shipping-fedex'),
		)				
	),
	'document_content'	=> array(
		'title'		=> __('Document Content', 'wf-shipping-fedex'),
		'type'		=> 'select',
		'class'		=> 'wc-enhanced-select fedex_special_services_tab',
		'default'	=> '',
		'options'	=> array(
			''					=> __( '-Select one-', 'wf-shipping-fedex'),
			'DERIVED'			=> __( 'Derived', 'wf-shipping-fedex'),
			'DOCUMENTS_ONLY'	=> __( 'Documents Only', 'wf-shipping-fedex'),
			'NON_DOCUMENTS'		=> __( 'Non Documents', 'wf-shipping-fedex'),
		)
	),
	// 'saturday_pickup'	  => array(
	// 	'title'	   => __( 'FedEx Saturday Pickup', 'wf-shipping-fedex' ),
	// 	'label'		=> __( 'Enable', 'wf-shipping-fedex' ),
	// 	'type'		=> 'checkbox',
	// 	'class'		=>'fedex_special_services_tab',
	// 	'default'	 => 'yes',
	// 	'desc_tip'	=> true,
	// 	'description' => __( 'If enabled, FedEx will charge additional amount and the pickup will be requested for Saturdays too. Otherwise, the pickups will not happen on Saturdays and will be re-scheduled for Mondays instead.', 'wf-shipping-fedex' ),
	// ),
	'dry_ice_enabled'	  => array(
		'title'		   => __( 'Ship Dry Ice', 'wf-shipping-fedex' ),
		'description'	 => __( 'Enable this to activate dry ice option to product level', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'	=>'fedex_special_services_tab',
		'default'		 => 'no'
	),
	'exclude_tax'	  => array(
		'title'		   => __( 'Exclude Tax', 'wf-shipping-fedex' ),
		'description'	 => __( 'Taxes will be excluded from product prices while generating label', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			=>'fedex_special_services_tab',
		'default'		 => 'no'
	),
	'home_delivery_premium'	  => array(
		'title'		  	=> __( 'Home Delivery Premium', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'label' 		=> __( 'Enable this option to select from various FedEx Premium delivery services', 'wf-shipping-fedex' ),
		'class'			=> 'fedex_special_services_tab'
	),
	'home_delivery_premium_type' => array(
		'title'		 	=> __('Home Delivery Premium Types', 'wf-shipping-fedex'),
		'type'		 	=> 'select',
		'class'		 	=> 'fedex_special_services_tab',
		'default'	 	=> '',
		'description'	=> __( '<small>Note: For Date Certain delivery type, make sure to select the date while fulfilling the order under WooCommerce Order Edit page.</small>' ),
		'options'	 	=> array(
			'APPOINTMENT'	=> __( 'Appointment', 'wf-shipping-fedex' ),
			'DATE_CERTAIN'	=> __( 'Date Certain', 'wf-shipping-fedex' ),
			'EVENING'		=> __( 'Evening', 'wf-shipping-fedex' ),
		)
	),
	'fedex_tracking'	=> array(
		'title'		  	=> __( 'FedEx Tracking', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'	 	=> 'no',
		'label' 		=> __( 'Enable FedEx Shipment Tracking', 'wf-shipping-fedex' ),
		'class'			=> 'fedex_special_services_tab'
	),
	

	'title_rate'		   => array(
		'title'		   => __( 'Rate Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_rates_tab',
		'description'	 => __( 'Configure the rate related settings here. You can enable the desired FedEx services and other rate options.', 'wf-shipping-fedex' ),
	),
	'enabled'		  => array(
		'title'		   	=> __( 'Real-time Rates', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'default'		=> 'no',
		'class'			=>'fedex_rates_tab'
	),
	'title'			=> array(
		'title'		   => __( 'Method Title', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	 => __( 'This controls the title which the user sees during checkout.', 'wf-shipping-fedex' ),
		'default'		 => __( 'FedEx', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'class'			=>'fedex_rates_tab'
	),
	'availability'		=> array(
		'title'		   => __( 'Method Available to', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => 'all',
		'class'		   => 'availability wc-enhanced-select fedex_rates_tab',
		'options'		 => array(
			'all'			=> __( 'All Countries', 'wf-shipping-fedex' ),
			'specific'	   => __( 'Specific Countries', 'wf-shipping-fedex' ),
		),
	),
	'countries'		   => array(
		'title'		   => __( 'Specific Countries', 'wf-shipping-fedex' ),
		'type'			=> 'multiselect',
		'class'		   => 'chosen_select fedex_rates_tab',
		'css'			 => 'width: 450px;',
		'default'		 => '',
		'options'		 => $wc_countries->get_allowed_countries(),
	),
	'delivery-title'		   => array(
		'title'		   => __( 'FedEx Estimated Delivery Date', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_rates_tab',
	),
	'delivery_time'	  => array(
		'title'		   => __( 'Display Delivery Date', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'desc_tip'	=> true,
		'class'			=>'fedex_rates_tab',
		'description'	 => __( 'Show delivery information on the cart/checkout. Applicable for US destinations only.', 'wf-shipping-fedex' )
	),
	'ship_time_adjustment'	  => array(
		'title'		   => __( 'Shipping Time Adjustment', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'decimal',
		'default'		 => 1,
		'desc_tip'	=> true,
		'class'			=>'fedex_rates_tab ph_fedex_est_delivery_date',
		'description'	 => __( 'Adjust number of days to get the estimated delivery accordingly (Numeric Only).', 'wf-shipping-fedex' )
	),
	'cut_off_time'	=>	array(
		'title' 		=>	__( 'Cut-Off Time', 'wf-shipping-fedex' ),
		'type'			=>	'time',
		'placeholder'	=>	'23:00',
		'css'			=>	'width:400px',
		'desc_tip'		=> __( 'Estimated delivery will be adjusted to the next day if any Rate Request is made after cut off time. Use 24 hour format (Hour:Minute). Example - 23:00.', 'wf-shipping-fedex' ),
		'class'			=> 'fedex_rates_tab ph_fedex_est_delivery_date'
	),
	'fedex_one_rate'	  => array(
		'title'	   => __( 'FedEx One Rate', 'wf-shipping-fedex' ),
		'label'	   => sprintf( __( 'Enable %sFedEx One Rates%s', 'wf-shipping-fedex' ), '<a href="https://www.fedex.com/us/onerate/" target="_blank">', '</a>' ),
		'type'		=> 'checkbox',
		'class'		=>'fedex_rates_tab',
		'default'	 => 'yes',
		'desc_tip'	=> true,
		'description' => __( 'FedEx One Rates will be offered if the items are packed into a valid FedEx One box, and the origin and destination is the US. For other countries this option will enable FedEx packing. Note: All FedEx boxes are not available for all countries, disable this option or disable different boxes if you are not receiving any shipping services.', 'wf-shipping-fedex' ),
	),
	'fedex_cod_rate' 		=> array(
		'title' 		=> __( 'FedEx COD', 'wf-shipping-fedex' ),
		'label' 		=> __( 'Enable', 'wf-shipping-fedex' ),
		'type' 			=> 'checkbox',
		'class' 		=> 'fedex_rates_tab',
		'default' 		=> 'no',
		'desc_tip' 		=> true,
		'description' 	=> __( 'Additional charges will be applied on Shipping Rates on enabling this service', 'wf-shipping-fedex' ),
	),
	'saturday_delivery'	=> array(
		'title'				=> __( 'FedEx Saturday Delivery', 'wf-shipping-fedex' ),
		'label'				=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'				=> 'checkbox',
		'default'			=> 'no',
		'desc_tip'			=> true,
		'class'				=> 'fedex_rates_tab',
		'description'		=> __( 'This option will enable Saturday Delivery Shipping Services.', 'wf-shipping-fedex' ),
	),
	'hold_at_location'	=> array(
		'title'				=> __( 'FedEx Hold at Location', 'wf-shipping-fedex' ),
		'label'				=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'				=> 'checkbox',
		'default'			=> 'no',
		'desc_tip'			=> true,
		'class'				=> 'fedex_rates_tab',
		'description'		=> __( 'This option will enable FedEx Hold at Location service. If it is enabled, customers can select any hold at location while checkout. FedEx will then hold the shipment at the selected location and the customers will have to pick their shipment from that location .', 'wf-shipping-fedex' ),
	),
	'hold_at_location_carrier_code'	 => array(
		'title'		   => __( 'FedEx Service', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => '',
		'class'		   => 'wc-enhanced-select fedex_rates_tab',
		'desc_tip'		=> true,
		'options'		 => array(
			''		     => __( 'Any', 'wf-shipping-fedex' ),
			'FDXE'	    => __( 'FedEx Express', 'wf-shipping-fedex' ),
			'FDXG'		=> __( 'FedEx Ground', 'wf-shipping-fedex' ),
			'FXFR'	    => __( 'FedEx Freight', 'wf-shipping-fedex' ),
		),
		'description'	 => __( 'Select the FedEx Service based on which the hold at location will be displayed at the cart & checkout page.', 'wf-shipping-fedex' )
	),
	'request_type'	 => array(
		'title'		   => __( 'Request Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => 'LIST',
		'class'		   => 'wc-enhanced-select fedex_rates_tab',
		'desc_tip'		=> true,
		'options'		 => array(
			'LIST'		=> __( 'List Rates', 'wf-shipping-fedex' ),
			'ACCOUNT'	 => __( 'Account Rates', 'wf-shipping-fedex' ),
		),
		'description'	 => __( 'Choose whether to return List or Account (discounted) rates from the API.', 'wf-shipping-fedex' )
	),
	'shipping_quote_type'	  => array(
		'title'			=> __( 'Shipping Quote Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		=> $shipping_type,
		'description'	=> __( '<small>Base Shipping Cost: Shipping Cost without any discounts, taxes & surcharges.<br/>Total Net Shipping Cost without Tax: Shipping Cost with discount & surcharges.<br/>Total Net Shipping Cost: Shipping Cost with discount, surcharges & transportation taxes.<br/>Total Net Shipping Cost With Duties & Taxes: Shipping Cost with discount, surcharges, transportation taxes & all other international taxes.</small>' ),
		'options'		 => array(
			'BASE_CHARGE'	    => __( 'Base Shipping Cost', 'wf-shipping-fedex' ),
			'NET_FEDEX_CHARGE'	=> __( 'Total Net Shipping Cost without Tax', 'wf-shipping-fedex' ),
			'NET_CHARGE'		=> __( 'Total Net Shipping Cost', 'wf-shipping-fedex' ),
			'DUTIES_AND_TAXES'	=> __( 'Total Net Shipping Cost With Duties & Taxes', 'wf-shipping-fedex' ),
		),
		'class'			=> 'fedex_rates_tab',
	),
	'offer_rates'   => array(
		'title'		   => __( 'Offer Rates', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'description'	 => '',
		'default'		 => 'all',
		'class'		   => 'wc-enhanced-select fedex_rates_tab',
		'options'		 => array(
			'all'		 => __( 'Offer the customer all returned rates', 'wf-shipping-fedex' ),
			'cheapest'	=> __( 'Offer the customer the cheapest rate only, anonymously', 'wf-shipping-fedex' ),
		),
	),
	'services'  => array(
		'type'			=> 'services'
	),
	'fedex_currency'	=> array(
		'title'			=> __('FedEx Currency', 'wf-shipping-fedex'),
		'type'			=> 'select',
		'default'		=> get_woocommerce_currency(),
		'options'		=>	get_woocommerce_currencies(),
		'class'			=>'fedex_rates_tab',
		'description'	=> __('Currency used to Communicate with FedEx. Conversion Rate required from store to FedEx Currency if it is different from Store Currency','wf-shipping-fedex'),
		'desc_tip'		=> true
	),

	'fedex_conversion_rate'	 => array(
		'title' 		  => __('Conversion Rate', 'wf-shipping-fedex'),
		'type' 			  => 'decimal',
		'default'		 => 1,
		'class'			=>'fedex_rates_tab',
		'description' 	  => __('Enter the conversion amount in case you have a different currency set up in store comparing to the currency of FedEx Account. This amount will be multiplied with all the cost of Store.','wf-shipping-fedex'),
		'desc_tip' 		  => true
	),

	'conversion_rate'	 => array(
		'title' 		  => __('Adjustment', 'wf-shipping-fedex'),
		'type' 			  => 'decimal',
		'default'		 => '',
		'class'			=>'fedex_rates_tab',
		'description' 	  => __('Enter the conversion amount in case you have a different currency set up comparing to the currency of origin location. This amount will be multiplied with the shipping rates. Leave it empty if no conversion required.','wf-shipping-fedex'),
		'desc_tip' 		  => true
	),
	'convert_currency' => array(
		'title'		   => __( 'Rates in Base Currency', 'wf-shipping-fedex' ),
		'label'		   => __( 'Convert FedEx returned rates to base currency.', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_rates_tab',
		'default'		 => 'no',
		'desc_tip'		  => true,
		'description'	 => 'Ex: FedEx returned rates in USD and would like to convert to the base currency EUR. Convertion happens only FedEx API provide the exchange rate.'
	),
	'min_amount'  => array(
		'title'		   => __( 'Minimum Order Amount', 'wf-shipping-fedex' ),
		'type'			=> 'decimal',
		'placeholder'	=> wc_format_localized_price( 0 ),
		'default'		 => '0',
		'class'			=>'fedex_rates_tab',
		'description'	 => __( 'Users will need to spend this amount to get this shipping available.', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
	),
	'min_shipping_cost'  => array(
		'title'		   => __( 'Minimum Shipping Cost', 'wf-shipping-fedex' ),
		'type'			=> 'decimal',
		'placeholder'	=> 0,
		'class'			=>'fedex_rates_tab',
		'description'	 => __( 'If rates returned by FedEx API will be less than Minimum Shipping Cost then Customer will be charged Minimum Shipping Cost.', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
	),
	'max_shipping_cost'  => array(
		'title'		   => __( 'Maximum Shipping Cost', 'wf-shipping-fedex' ),
		'type'			=> 'decimal',
		'placeholder'	=> 0,
		'class'			=>'fedex_rates_tab',
		'description'	 => __( 'If rates returned by FedEx API will be greater than Maximun Shipping Cost then Customer will be charged Maximum Shipping Cost.', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
	),
	'fedex_fallback' 				=> array(
		'title' 		=> __( 'Fallback', 'wf-shipping-fedex' ),
		'type'			=> 'decimal',
		'default' 		=> '',
		'desc_tip' 		=> true,
		'class'			=>'fedex_rates_tab',
		'description' 	=> __( 'If FedEx returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'wf-shipping-fedex' ),
	),





	'title_label'		   => array(
		'title'		   => __( 'Label Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_label_tab',
		'description'	 => __( 'Configure the label and tracking related settings here.', 'wf-shipping-fedex' ),
	),

	'display_fedex_meta_box_on_order'	=>	array(
		'title'			=>	__( 'FedEx Label Printing', 'wf-shipping-fedex' ),
		'class'			=>	'fedex_label_tab',
		'type'			=>	'select',
		'options'			=> array(
				'yes'			=> __( 'Enable', 'wf-shipping-fedex' ),
				'no' 			=> __( 'Disable', 'wf-shipping-fedex' ),
			),
		'default'		=>	'yes',
		'description'	=>	__( 'Disable this to hide FedEx meta boxes (Generate label and tracking meta box) on order page).', 'wf-shipping-fedex' ),
		'desc_tip'		=>	true,

	),
	'label_maskable_type'	=> array(
		'title'			=> __( 'Masking Data on the Shipping Labels', 'wf-shipping-fedex' ),
		'description'	=> __( 'Names for data elements / areas which may be masked from printing on the shipping labels.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'type'			=> 'multiselect',
		'class'			=> 'fedex_label_tab chosen_select',
		'default'		=> '',
		'options'	 	=> array(
			'CUSTOMS_VALUE'									=> __( 'Custom Value', 'wf-shipping-fedex' ),
			'DUTIES_AND_TAXES_PAYOR_ACCOUNT_NUMBER'			=> __( 'Duties And Taxes Payor Account Number', 'wf-shipping-fedex' ),
			'SECONDARY_BARCODE'								=> __( 'Secondary Barcode', 'wf-shipping-fedex' ),
			'SHIPPER_ACCOUNT_NUMBER'						=> __( 'Shipper Account Number', 'wf-shipping-fedex' ),
			'TERMS_AND_CONDITIONS'							=> __( 'Terms And Conditions', 'wf-shipping-fedex' ),
			'TRANSPORTATION_CHARGES_PAYOR_ACCOUNT_NUMBER'	=> __( 'Transportation Charges Payor Account Number', 'wf-shipping-fedex' ),
		)
	),
	'timezone_offset' => array(
		'title' 		=> __('Time Zone Offset (Minutes)', 'wf-shipping-fedex'),
		'type' 			=> 'text',
		'description' 	=> __('Please enter a value in this field, if you want to change the shipment time while Label Printing. Enter a negetive value to reduce the time.','wf-shipping-fedex'),
		'class'			=>'fedex_label_tab',
		'desc_tip' 		=> true
	),
	//shipping_customs_shipment_purpose
	'customs_ship_purpose'   => array(
		'title'		   => __( 'Purpose of Shipment', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip'	=> true,
		'description'	 => 'Select purpose of shipment',
		'default'		 => 'SOLD',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		 => array(
			'GIFT' 				=> __( 'Gift', 				'wf-shipping-fedex'),
			'NOT_SOLD' 			=> __( 'Not Sold', 			'wf-shipping-fedex'),
			'PERSONAL_EFFECTS' 	=> __( 'Personal effects', 	'wf-shipping-fedex'),
			'REPAIR_AND_RETURN' => __( 'Repair and return', 'wf-shipping-fedex'),
			'SAMPLE' 			=> __( 'Sample', 			'wf-shipping-fedex'),
			'SOLD' 				=> __( 'Sold', 	 			'wf-shipping-fedex'),
		)				
	),
	'email_notification'	  => array(
		'title'		   => __( 'Email Notification', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => '',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		  => array(
			''					=> __('None',					'wf-shipping-fedex'),
			'CUSTOMER'			=> __('Customer',			'wf-shipping-fedex'),
			'SHIPPER'			=> __('Shipper',			'wf-shipping-fedex'),
			//'BOTH'				=> __('Customer and Shipper',	'wf-shipping-fedex'), Only One Email Node Supported in WSDL
		),
		'desc_tip'	=> true,
		'description'	 => __( 'Select recipients for email notifications regarding the shipment from FedEx', 'wf-shipping-fedex' )
	),
	'output_format'   => array(
		'title'		   => __( 'Print Label Size', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip'	=> true,
		'description'	 => '8.5x11 indicates paper and 4x6 indicates thermal size.',
		'default'		 => 'PAPER_4X6',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		 => array(
			'PAPER_4X6' 							  	=> __( 'PAPER_4X6',	'wf-shipping-fedex'),
			'PAPER_4X6.75' 							  	=> __( 'PAPER_4X6.75',	'wf-shipping-fedex'),
			'PAPER_4X8' 							  	=> __( 'PAPER_4X8', 'wf-shipping-fedex'),
			'PAPER_4X9' 							  	=> __( 'PAPER_4X9', 'wf-shipping-fedex'),
			'PAPER_7X4.75' 						  		=> __( 'PAPER_7X4.75', 'wf-shipping-fedex'),
			'PAPER_8.5X11_BOTTOM_HALF_LABEL' 		  	=> __( 'PAPER_8.5X11_BOTTOM_HALF_LABEL', 'wf-shipping-fedex'),
			'PAPER_8.5X11_TOP_HALF_LABEL'			  	=> __( 'PAPER_8.5X11_TOP_HALF_LABEL', 'wf-shipping-fedex'),
			'PAPER_LETTER' 						  		=> __( 'PAPER_LETTER', 'wf-shipping-fedex'),
			'STOCK_4X6' 						  		=> __( 'STOCK_4X6 (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X6.75' 						  		=> __( 'STOCK_4X6.75 (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X6.75_LEADING_DOC_TAB' 				=> __( 'STOCK_4X6.75_LEADING_DOC_TAB (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X6.75_TRAILING_DOC_TAB' 			=> __( 'STOCK_4X6.75_TRAILING_DOC_TAB (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X8' 						  		=> __( 'STOCK_4X8 (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X9' 						  		=> __( 'STOCK_4X9 (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X9_LEADING_DOC_TAB' 				=> __( 'STOCK_4X9_LEADING_DOC_TAB (For Thermal Printer Only)', 'wf-shipping-fedex'),
			'STOCK_4X9_TRAILING_DOC_TAB' 				=> __( 'STOCK_4X9_TRAILING_DOC_TAB (For Thermal Printer Only)', 'wf-shipping-fedex'),
		)				
	),
	'image_type'   => array(
		'title'		   => __( 'Image Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'desc_tip'	=> true,
		'description'	 => '4x6 output format best fit with type PNG',
		'default'		 => 'pdf',
		'options'		 => array(
			'pdf' 							  	=> __( 'PDF', 'wf-shipping-fedex'),
			'png' 							  	=> __( 'PNG', 'wf-shipping-fedex'),
			'epl2' 							  	=> __( 'EPL2', 'wf-shipping-fedex'),
			'zplii' 							=> __( 'ZPLII', 'wf-shipping-fedex')
		)				
	),
	'show_label_in_browser'  => array(
		'title'			=> __( 'Display Labels in Browser for Individual Order', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable' ),
		'type'			=> 'checkbox',
		'default'		=> 'no',
		'description'	=> __( 'Enabling this will display the label in the browser instead of downloading it. Useful if your downloaded file is getting currupted because of PHP BOM (ByteOrderMark).', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'class'			=> 'fedex_label_tab',
	),
	'label_custom_scaling'  => array(
		'title'			=> __( 'Custom Scaling (%)', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable' ),
		'type'			=> 'decimal',
		'default'		=> '100',
		'description'	=> __( 'Provide a percentage value to scale the shipping label image based on your preference for bulk printing.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'class'			=> 'fedex_label_tab',
	),
	'doc_tab_content'	=> array(
		'title'			=> __( 'Doc Tab Content', 'wf-shipping-fedex' ),
		'label'			=> __( 'Applicable only for ZPLII Type', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		=> 'no',
		'class'			=> 'fedex_label_tab',
		'description'	=> '',
	),
	'doc_tab_orientation'   => array(
		'title' 		=> __( 'Doc Tab Orientation', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'			=> 'wc-enhanced-select fedex_label_tab',
		'default'		=> 'TOP_EDGE_OF_TEXT_FIRST',
		'options'		=> array(
			'TOP_EDGE_OF_TEXT_FIRST' 		=> __( 'Top Edge of Text First', 'wf-shipping-fedex'),
			'BOTTOM_EDGE_OF_TEXT_FIRST'		=> __( 'Bottom Edge of Text First', 'wf-shipping-fedex'),
		),
	),
	'tracking_shipmentid'	=> array(
			'title'			=> __( 'FedEx Shipment Tracking', 'wf-shipping-fedex' ),
			'label'			=> __( 'Enable Shipment Tracking for your WooCommerce Orders', 'wf-shipping-fedex' ),
			'type'			=> 'checkbox',
			'default'		=> 'no',
			'class'			=> 'fedex_label_tab',
			'description'	=> '',
		),
	'disable_customer_tracking' => array(
			'title' 		=> __( 'Disable Tracking for Customers', 'wf-shipping-fedex' ),
			'label'			=> __( 'Disable the tracking message sent to the customers via Email and on the My Account page', 'wf-shipping-fedex' ),
			'type'			=> 'checkbox',
			'default'		=> 'no',
			'class'			=> 'fedex_label_tab',
			'description'	=> '',
		),
	'custom_message'		=> array(
			'title'				=> __( 'Custom Shipment Message', 'wf-shipping-fedex' ),
			'type'				=> 'text',
			'class'			=> 'fedex_label_tab',
			'description'		=> __( 'Define your own shipment message. Use the place holder tags [ID], [SERVICE] and [DATE] for Shipment Id, Shipment Service and Shipment Date respectively. Leave it empty for default message.<br>', 'wf-shipping-fedex' ),
			'css'				=> 'width:900px',
			//'id'				=> Ph_FedEx_Tracking_Util::TRACKING_SETTINGS_TAB_KEY.Ph_FedEx_Tracking_Util::TRACKING_MESSAGE_KEY,
			'placeholder'		=> 'Your order was shipped on [DATE] via [SERVICE]. To track shipment, please follow the link of shipment ID(s) [ID]',
			'desc_tip'		   => true
		),
	'cod_collection_type'   => array(
		'title'		   => __( 'COD Collection Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'desc_tip'	=> true,
		'description'	 => 'Identifies the type of funds FedEx should collect upon shipment delivery.',
		'default'		 => 'ANY',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		 => array(
			'ANY' 							  	=> __( 'ANY', 						'wf-shipping-fedex'),
			'CASH' 							  	=> __( 'CASH', 						'wf-shipping-fedex'),
			'COMPANY_CHECK'					=> __( 'COMPANY CHECK',		'wf-shipping-fedex'),
			'PERSONAL_CHECK'					=> __( 'PERSONAL CHECK',		'wf-shipping-fedex'),
			'GUARANTEED_FUNDS'   			  	=> __( 'GUARANTEED FUNDS',			'wf-shipping-fedex')
			)				
	),
	'default_dom_service' => array(
		'title'		   => __( 'Default Service for Domestic Shipment', 'wf-shipping-fedex' ),
		'description'	 => __( 'FedEx labels will be generated for this Domestic Service if no FedEx Shipping Method is selected on the cart page and the shipping address is a Domestic Address', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'select',
		'default'		 => '',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		  => array_merge(array(''=>'Select once'), $dom_services)
	),
	'default_int_service'	=> array(
		'title'		   => __( 'Default Service for International Shipment', 'wf-shipping-fedex' ),
		'description'	 => __( 'FedEx labels will be generated for this International Service if no FedEx Shipping Method is selected on the cart page and the shipping address is a International Address', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'default'		 => '',
		'options'		  => array_merge(array(''=>'Select once'), $int_services)
	),
	'item_description'  => array(
		'title'		   => __( 'Item Description', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	 => __( 'Required for UAE; Otherwise: Optional – This element is for the customer to describe the content of the package for customs clearance purposes. This applies to intra-UAE, intra-Columbia and intra-Brazil shipments.', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'class'			=> 'fedex_label_tab',
	),
	'tin_number'  => array(
		'title'		   => __( 'TIN number', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'placeholder'	  => __( 'TIN number', 'wf-shipping-fedex' ),
		'description'	 => __( 'TIN or VAT number .', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'class'			=> 'fedex_label_tab',
	),
	'tin_type'	=> array(
		'title'		   => __( 'TIN type', 'wf-shipping-fedex' ),
		'description'	 => __( 'The category of the taxpayer identification', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'select',
		'default'		 => 'BUSINESS_STATE',
		'class'		   => 'wc-enhanced-select fedex_label_tab',
		'options'		  => array(
			'BUSINESS_STATE'	=>'BUSINESS_STATE',
			'BUSINESS_NATIONAL'	=>'BUSINESS_NATIONAL',
			'BUSINESS_UNION'	=>'BUSINESS_UNION',
			'PERSONAL_NATIONAL'	=>'PERSONAL_NATIONAL',
			'PERSONAL_STATE'	=>'PERSONAL_STATE',
		)
	),
	'frontend_retun_label'	  => array(
		'title'		   => __( 'Enable Return Label in My Account Page', 'wf-shipping-fedex' ),
		'description'	 => __( 'By enabling this the customers can generate the return label themself from my account page', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'class'			=> 'fedex_label_tab',
	),
	'frontend_retun_label_reason'	  => array(
		'title'				=> __( 'Reason for Return Label', 'wf-shipping-fedex' ),
		'label'				=> __( 'Allow customers to provide the reason for return before generating a return label', 'wf-shipping-fedex' ),
		'description'		=> __( "By enabling this customer will be able to generate return label only after providing the reason. Reason will be displayed in order notes.", 'wf-shipping-fedex' ),
		'desc_tip'			=> true,
		'type'				=> 'checkbox',
		'default'			=> 'no',
		'class'				=> 'fedex_label_tab ph_fedex_return_label',
	),
	'int_return_label_reason'	=> array(
		'title' 		=> __( 'Customs Options Type', 'wf-shipping-fedex' ),
		'description' 	=> __( 'Details the return reason used for clearance processing of international dutiable outbound and international dutiable return shipments.', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type' 			=> 'select',
		'default' 		=> 'TRIAL',
		'class' 		=> 'wc-enhanced-select fedex_label_tab ph_return_label_return',
		'options' 		=> array(
			'COURTESY_RETURN_LABEL'	=> 'COURTESY_RETURN_LABEL',
			'EXHIBITION_TRADE_SHOW'	=> 'EXHIBITION_TRADE_SHOW',
			'FAULTY_ITEM' 			=> 'FAULTY_ITEM',
			'FOLLOWING_REPAIR' 		=> 'FOLLOWING_REPAIR',
			'FOR_REPAIR' 			=> 'FOR_REPAIR',
			'ITEM_FOR_LOAN' 		=> 'ITEM_FOR_LOAN',
			'OTHER' 				=> 'OTHER',
			'REJECTED' 				=> 'REJECTED',
			'REPLACEMENT' 			=> 'REPLACEMENT',
			'TRIAL' 				=> 'TRIAL',
		)
	),
	'int_return_label_desc'		=> array(
		'title' 		=> __( 'Customs Options Description', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'class'			=> 'fedex_label_tab ph_return_label_desc',
		'desc_tip' 		=> true,
		'description' 	=> __( 'Specifies additional description about customs options. This is a required field when the customs options type is "OTHER".', 'wf-shipping-fedex' ),
	),
	'csb5_shipments'	=> array(
		'title' 		=> __( 'CSB V International Shipments - India', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		=> 'no',
		'class'			=> 'fedex_label_tab',
		'description'	=> __( 'Applicable for International Shipments', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
	),
	'ad_code'		=> array(
		'title' 		=> __( 'Bank AD Code', 'wf-shipping-fedex' ),
		'type' 			=> 'text',
		'class'			=> 'fedex_label_tab ph_fedex_csb5',
		'desc_tip' 		=> true,
		'description' 	=> __( 'Authorized Dealer code is normally given by the Bank.', 'wf-shipping-fedex' ),
	),
	'gst_shipment'	=> array(
		'title' 		=> __( 'Is GST', 'wf-shipping-fedex' ),
		'description' 	=> __( 'Specifies Shipment has a GST Invoice or not', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type' 			=> 'select',
		'default' 		=> 'N',
		'class' 		=> 'wc-enhanced-select fedex_label_tab ph_fedex_csb5',
		'options' 		=> array(
			'G'	=> 'Yes',
			'N'	=> 'No',
		)
	),
	'csb_termsofsale'	=> array(
		'title' 		=> __( 'Terms of Sale', 'wf-shipping-fedex' ),
		'description' 	=> __( 'Terms of Sale', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type' 			=> 'select',
		'default' 		=> 'FOB',
		'class' 		=> 'wc-enhanced-select fedex_label_tab ph_fedex_csb5',
		'options' 		=> array(
			'FOB'	=> 'FOB',
			'CFR'	=> 'CFR',
			'CIF'	=> 'CIF',
			'DAT'	=> 'DAT',
			'DDP'	=> 'DDP',
			'EXW'	=> 'EXW',
			'FCA'	=> 'FCA',
			'CIP'	=> 'CIP',
			'CPT'	=> 'CPT',
			'DAP'	=> 'DAP',
		)
	),
	'under_bond'	=> array(
		'title' 		=> __( 'Shipments under BOND', 'wf-shipping-fedex' ),
		'type' 			=> 'select',
		'default' 		=> 'U',
		'class' 		=> 'wc-enhanced-select fedex_label_tab ph_fedex_csb5',
		'options' 		=> array(
			'B'	=> 'BOND',
			'U'	=> 'Letter of Undertaking',
			'-'	=> 'NONE',
		)
	),
	'meis_shipment'	=> array(
		'title' 		=> __( 'MEIS Shipments', 'wf-shipping-fedex' ),
		'type' 			=> 'select',
		'default' 		=> 'M',
		'class' 		=> 'wc-enhanced-select fedex_label_tab ph_fedex_csb5',
		'options' 		=> array(
			'M'	=> 'MEIS',
			'-'	=> 'Non MEIS',
		)
	),
	'xa_show_all_shipping_methods' => array(
		'title'		   => __( 'Show All Services In Order Edit Page', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'yes',
		'class'			=> 'fedex_label_tab',
		'description'	 => __( 'Check this option to show all services in create label drop down(FEDEX).', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
	),
	'saturday_delivery_label' => array(
		'title'			=> __( 'FedEx Saturday Delivery', 'wf-shipping-fedex' ),
		'label'		   	=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		=> 'no',
		'class'			=> 'fedex_label_tab',
		'description'	=> __('This option will enable Saturday Delivery Shipping Services, It will effect for all orders.', 'wf-shipping-fedex'),
		'desc_tip'		=> true,
	),
	'remove_special_char_product' => array(
		'title'		   => __('Remove Special Characters from Product Name','wf-shipping-fedex'),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'class'			=> 'fedex_label_tab',
		'description'	 => __('While passing product details in request to the FedEx API, remove special characters from product name.','wf-shipping-fedex'),
		'desc_tip'		   => true,
	),
	'automate_package_generation'	  => array(
		'title'		   => __( 'Generate Packages Automatically After Order Received', 'wf-shipping-fedex' ),
		'label'			  => __( 'Enable', 'wf-shipping-fedex' ),			
		'description'	 => __( 'This will generate packages automatically after order is received and payment is successful', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'default'		 => 'no',
		'class'			=> 'fedex_label_tab',
	),
	'automate_label_generation'	  => array(
		'title'		   => __( 'Generate Shipping Labels Automatically After Order Received', 'wf-shipping-fedex' ),
		'label'			  => __( 'Enable', 'wf-shipping-fedex' ),			
		'description'	 => __( 'This will generate shipping labels automatically after order is received and payment is successful', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			=> 'fedex_label_tab',
		'default'		 => 'no'
	),
	'auto_label_trigger' 	=> array(
		'title' 		=> __( 'Trigger Automatic Label Generation', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		=> 'thankyou_page',
		'class'			=> 'fedex_label_tab',
		'options' 		=> array(
			'thankyou_page'	=> __( 'Default - When the order is placed successfully', 'wf-shipping-fedex'),
			'payment_status'=> __( 'When the payment is confirmed', 'wf-shipping-fedex'),
		),
	),
	'allow_label_btn_on_myaccount'	  => array(
		'title'		   => __( 'Allow customers to print label from their My Account->Orders page', 'wf-shipping-fedex' ),
		'label'			  => __( 'Enable', 'wf-shipping-fedex' ),			
		'description'	 => __( 'A button will be available for downloading the label and printing', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			=> 'fedex_label_tab',
		'default'		 => 'no'
	),
	'auto_email_label'	=> array(
		'title'			=> __( 'Send Shipping Label To', 'wf-shipping-fedex' ),
		'description'	=> __( 'Choose the recipient who will get the shipping label(s) via Email.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'type'			=> 'multiselect',
		'class'			=> 'fedex_label_tab chosen_select',
		'default'		=> '',
		'options'		=> $auto_email_label_options,
	),
	'email_subject'	  => array(
		'title'		  	=> __( 'Email Subject', 'wf-shipping-fedex' ),
		'description'	=> __( 'Subject of Email sent for FedEx Label. Supported Tags : [ORDER NO] - Order Number.', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type'			=> 'text',
		'placeholder'	=> __( 'Shipment Label For Your Order', 'wf-shipping-fedex' ).' [ORDER NO]',
		'class'			=>	'fedex_label_tab'
	),
	'email_content'  => array(
		'title'		   => __( 'Email Format', 'wf-shipping-fedex' ),
		'type'			=> 'textarea',
		'class'			=> 'fedex_label_tab',
		'default'		 => '',
		'desc_tip'		   => true,
		'description'	 => __( 'Define your own email html here. Use the place holder tag [DOWNLOAD LINK] to get the label dowload link. Supported tag holders - [DOWNLOAD LINK], [ORDER NO], [ORDER AMOUNT], [PRODUCTS ID], [PRODUCTS SKU], [PRODUCTS NAME], [CUSTOMER EMAIL], [CUSTOMER NAME], [ADDITIONAL LABELS], [PRODUCT_INFO] - Print Product details as table .', 'wf-shipping-fedex' ),
		'css' =>		'width:70%;height: 150px;',
		'placeholder'	=> "<html><body>
	<div>Please Download the label</div>
	<a href='[DOWNLOAD LINK]' ><button>Download the label here</button> </a>
</body></html>",
	),

	// Commercial Invoice
	'title_commercial_invoice' 		=> array(
		'title' 		=> __( 'Commercial Invoice Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_commercial_invoice_tab',
		'description'	=> __( 'Configure the commercial invoice related settings here.', 'wf-shipping-fedex' ),
	),
	'commercial_invoice' => array(
		'title'			=> __( 'Commercial Invoice', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> __( 'On enabling this option Commercial Invoice will be received as an additional label. Applicable for international shipping only.', 'wf-shipping-fedex' ),
	),
	'etd_label' => array(
		'title'			=> __( 'ETD - Electronic Trade Documents', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_commercial_invoice_tab commercial_invoice_toggle',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> __( 'On enabling this option the shipment details will be sent electronically and ETD will be printed in the Shipping Label', 'wf-shipping-fedex' ),
	),
	//PDS-149
	'invoice_commodity_value'   => array(
		'title'		   => __( 'Price Value', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_commercial_invoice_tab commercial_invoice_toggle',
		'desc_tip'	=> true,
		'description'	 => 'Select whether you want to display the discounted price, original product price or the declared value to be printed on the commercial invoice.',
		'default'		 => $default_invoice_commodity_value,
		'options'		 => array(
			'discount_price' 			=> __( 'Discounted', 'wf-shipping-fedex'),
			'product_declared' 			=> __( 'Product', 'wf-shipping-fedex'),
			'declared_price' 			=> __( 'Declared', 'wf-shipping-fedex')
		)				
	),	
	'commercial_invoice_shipping' => array(
		'title'			=> __( 'Shipping Charges in Commercial Invoice', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> 'Enabling this option will display shipping charges (if any) in Commercial Invoice.'
	),
	'commercial_invoice_order_currency' => array(
		'title'			=> __( 'Order Currency in Commercial Invoice', 'wf-shipping-fedex' ),
		'label'			=> __( 'Supports only WOOCS Multi-Currency', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> 'Enabling this option will display Order Currency in Commercial Invoice.'
	),
	'shipment_comments'  => array(
		'title'		   	=> __( 'Comments', 'wf-shipping-fedex' ),
		'type'			=> 'textarea',
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
		'default' 		=> '',
		'desc_tip' 		=> true,
		'description'	=> __( 'Any comments that need to be communicated about this shipment.', 'wf-shipping-fedex' ),
		'css' 			=> 'width:44%;height: 100px;',
	),
	'special_instructions'  => array(
		'title'			=> __( 'Special Instructions', 'wf-shipping-fedex' ),
		'type'			=> 'textarea',
		'description'	=> __( 'Specify Special Instructions for Commercial Invoice.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'css' 			=> 'width:44%;height: 100px;',
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
	),
	'payment_terms'  => array(
		'title'			=> __( 'Payment Terms', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	=> __( 'Specify Payment Terms for Commercial Invoice.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
	),
	'global_hs_code'  => array(
		'title'			=> __( 'HS Tariff Number ', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'description'	=> __( 'This HS Code will be used for Commercial Invoice, when Product Level HS Code is not available.', 'wf-shipping-fedex' ),
		'desc_tip'		=> true,
		'class'			=> 'commercial_invoice_toggle fedex_commercial_invoice_tab',
	),
	'company_logo' => array(
		'title' 		=> __('Company Logo', 'wf-shipping-fedex'),
		'description' 	=> sprintf('<span class="button" id="company_logo_picker">Choose Image</span> <div id="company_logo_result"></div>'),
		'class'			=> 'commercialinvoice-image-uploader fedex_commercial_invoice_tab',
		'type' 			=> 'text',
		'placeholder' 	=> 'Upload an image to set Company Logo on Commercial Invoice'
	),
	'digital_signature' => array(
		'title' 		=> __('Digital Signature', 'wf-shipping-fedex'),
		'description' 	=> sprintf('<span class="button" id="digital_signature_picker">Choose Image</span> <div id="digital_signature_result"></div>'),
		'class'			=> 'commercialinvoice-image-uploader fedex_commercial_invoice_tab',
		'type' 			=> 'text',
		'placeholder' 	=> 'Upload an image to set Digital Signature on Commercial Invoice'
	),
	//PRO_FORMA_INVOICE
	'ph_pro_forma_invoice' => array(
		'title'			=> __( 'Pro Forma Invoice', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> __( 'On enabling this option PRO FORMA INVOICE will be received as an additional label. Applicable for international shipping only.', 'wf-shipping-fedex' ),
	),
	//USMCA Certificate
	'usmca_certificate' => array(
		'title'			=> __( 'USMCA Certificate', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> __( 'On enabling this option USMCA Certificate will be received as an additional label. Applicable for international shipping only.', 'wf-shipping-fedex' ),
	),
	'usmca_ci_certificate_of_origin' => array(
		'title'			=> __( 'USMCA Commercial Invoice Certificate', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_commercial_invoice_tab',
		'default'		=> 'no',
		'desc_tip'		=> true,
		'description'	=> __( 'On enabling this option USMCA Commercial Invoice Certification Of Origin will be received as an additional label. Applicable for international shipping only.', 'wf-shipping-fedex' ),
	),
	'certifier_specification'   => array(
		'title'		   => __( 'Certifier Specification', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		   => 'wc-enhanced-select fedex_commercial_invoice_tab usmca_and_usmcaci_toggle',
		'default'		 => 'IMPORTER',
		'options'		 => array(
			'EXPORTER' 			=> __( 'Exporter', 'wf-shipping-fedex'),
			'IMPORTER' 			=> __( 'Importer', 'wf-shipping-fedex'),
			'PRODUCER' 			=> __( 'Producer', 'wf-shipping-fedex')
		)				
	),
	'producer_specification'   => array(
		'title'		    => __( 'Producer Specification', 'wf-shipping-fedex' ),
		'type' 			=> 'select',
		'class'		   	=> 'wc-enhanced-select fedex_commercial_invoice_tab usmca_and_usmcaci_toggle',
		'default'		=> 'SAME_AS_EXPORTER',
		'options'		=> array(
			'SAME_AS_EXPORTER' 	=> __( 'Same as Exporter', 'wf-shipping-fedex'),
			'VARIOUS' 			=> __( 'Various', 'wf-shipping-fedex'),
		)				
	),
	'importer_specification'   => array(
		'title'		    => __( 'Importer Specification', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'		    => 'wc-enhanced-select fedex_commercial_invoice_tab usmca_toggle',
		'default'		=> 'UNKNOWN',
		'options'		=> array(
			'UNKNOWN' 			=> __( 'Unknown', 'wf-shipping-fedex'),
			'VARIOUS' 			=> __( 'Various', 'wf-shipping-fedex'),
		)				
	),
	'blanket_begin_period' => array(
		'title' 		=> __( 'Blanket Period Begin Date', 'wf-shipping-fedex' ),
		'label' 		=> __( 'Enable', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type' 			=> 'date',
		'css'			=> 'width:400px',
		'description' 	=> __('Begin date of the blanket period. It is the date upon which the Certificate becomes applicable to the good covered by the blanket Certificate (it may be prior to the date of signing this Certificate)', 'wf-shipping-fedex'),
		'class'			=> 'fedex_commercial_invoice_tab usmca_toggle'
	),
	'blanket_end_period' => array(
		'title' 		=> __( 'Blanket Period End Date', 'wf-shipping-fedex' ),
		'label' 		=> __( 'Enable', 'wf-shipping-fedex' ),
		'desc_tip' 		=> true,
		'type' 			=> 'date',
		'css'			=> 'width:400px',
		'description' 	=> __('End Date of the blanket period. It is the date upon which the blanket period expires', 'wf-shipping-fedex'),
		'class'			=> 'fedex_commercial_invoice_tab usmca_toggle'
	),

	'title_packaging'		   => array(
		'title'		   => __( 'Packaging Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_packaging_tab',
		'description'	 => __( 'Choose the packing options suitable for your store here.', 'wf-shipping-fedex' ),
	),
	'packing_method'   => array(
		'title'		   => __( 'Parcel Packing Method', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => 'weight_based',
		'class'		   => 'packing_method wc-enhanced-select fedex_packaging_tab',
		'options'		 => array(
			'per_item'	   => __( 'Pack items individually', 'wf-shipping-fedex' ),
			'box_packing'	=> __( 'Pack into boxes with weights and dimensions', 'wf-shipping-fedex' ),
			'weight_based'   => __( 'Recommended: Weight based, calculate shipping based on weight', 'wf-shipping-fedex' ),
		),
		'desc_tip'	=> true,
		'description'	 => __( 'Determine how items are packed before being sent to FedEx.', 'wf-shipping-fedex' ),
	),

	'volumetric_weight'	=> array(
		'title'   			=> __( 'Enable Volumetric weight', 'wf-shipping-fedex' ),
		'type'				=> 'checkbox',
		'class'				=> 'fedex_weight_based_option fedex_packaging_tab',
		'label'				=> __( 'This option will calculate the volumetric weight. Then a comparison is made on the total weight of cart to the volumetric weight.</br>The higher weight of the two will be sent in the request.', 'wf-shipping-fedex' ),
		'default' 			=> 'no',
	), 

	'box_max_weight'		   => array(
		'title'		   => __( 'Max Package Weight', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'default'		 => '10',
		'class'		   => 'fedex_weight_based_option fedex_packaging_tab',
		'desc_tip'		=> true,
		'description'	 => __( 'Maximum weight allowed for single box.', 'wf-shipping-fedex' ),
	),

	//weight_packing_process
	'weight_pack_process'   => array(
		'title'		   => __( 'Packing Process', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default'		 => '',
		'class'		   => 'fedex_weight_based_option wc-enhanced-select fedex_packaging_tab',
		'options'		 => array(
			'pack_descending'	   => __( 'Pack heavier items first', 'wf-shipping-fedex' ),
			'pack_ascending'		=> __( 'Pack lighter items first.', 'wf-shipping-fedex' ),
			'pack_simple'			=> __( 'Pack purely divided by weight.', 'wf-shipping-fedex' ),
		),
		'desc_tip'	=> true,
		'description'	 => __( 'Select your packing order.', 'wf-shipping-fedex' ),
	),

	'boxes'  => array(
		'type'			=> 'box_packing'
	),
	'enable_speciality_box'	  => array(
		'title'	   => __( 'Include Speciality Boxes', 'wf-shipping-fedex' ),
		'label'	   => __( 'Enable', 'wf-shipping-fedex' ),
		'class'		  => 'speciality_box fedex_packaging_tab',
		'type'		=> 'checkbox',
		'default'	 => 'yes',
		'desc_tip'	=> true,
		'description' => __( 'Check this to load Speciality Boxes with boxes.', 'wf-shipping-fedex' ),
	),

	// Hazmat Packaging
	'hazmat_enabled'	  => array(
		'title'		   => __( 'Hazardous(HazMat) Packaging', 'wf-shipping-fedex' ),
		'description'	 => __( 'Enable this option if you are shipping Hazardous Materials', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			=> 'fedex_packaging_tab',
		'default'		 => 'no'
	),

	'hp_packaging_type' => array(
		'title'			=> __( 'Type of Packaging', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'			=> 'wc-enhanced-select fedex_packaging_tab ph_fedex_hazmat_grp',
		'options'		=> array(
			'1'			=> __( 'Drum', 'wf-shipping-fedex' ),
			'2'			=> __( 'Wooden Barrel', 'wf-shipping-fedex' ),
			'3'			=> __( 'Jerrican', 'wf-shipping-fedex' ),
			'4'			=> __( 'Box', 'wf-shipping-fedex' ),
			'5'			=> __( 'Bag', 'wf-shipping-fedex' ),
			'6'			=> __( 'Composite Package', 'wf-shipping-fedex' ),
			'7'			=> __( 'Pressure Receptacle', 'wf-shipping-fedex' ),
		),
		'desc_tip'		=> true,
		'description'	=> __( 'Choose Type of Packaging', 'wf-shipping-fedex' ),
	),

	'hp_packaging_material' => array(
		'title'			=> __( 'Packaging Material', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'class'			=> 'wc-enhanced-select fedex_packaging_tab ph_fedex_hazmat_grp',
		'options'		=> array(
			'A'			=> __( 'Steel', 'wf-shipping-fedex' ),
			'B'			=> __( 'Aluminum', 'wf-shipping-fedex' ),
			'C'			=> __( 'Natural Wood', 'wf-shipping-fedex' ),
			'D'			=> __( 'Plywood', 'wf-shipping-fedex' ),
			'F'			=> __( 'Reconstituted Wood', 'wf-shipping-fedex' ),
			'G'			=> __( 'Fiberboard', 'wf-shipping-fedex' ),
			'H'			=> __( 'Plastic', 'wf-shipping-fedex' ),
			'L'			=> __( 'Textile', 'wf-shipping-fedex' ),
			'M'			=> __( 'Paper, Multi-wall', 'wf-shipping-fedex' ),
			'N'			=> __( 'Metal', 'wf-shipping-fedex' ),
			'P'			=> __( 'Glass, Porcelain or Stoneware', 'wf-shipping-fedex' ),
		),
		'desc_tip'		=> true,
		'description'	=> __( 'Choose Packaging Material', 'wf-shipping-fedex' ),
	),

	
	

	
	'title_pickup'		   => array(
		'title'		   => __( 'Pickup Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_pickup_tab',
		'description'	 => __( 'Configure the pickup options here to avail FedEx pickup for your orders', 'wf-shipping-fedex' ),
	),
	'pickup_enabled'	  => array(
		'title'		   => __( 'Enable FedEx Pickup', 'wf-shipping-fedex' ),
		'description'	 => __( 'Enable this to setup pickup request', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			=> 'fedex_pickup_tab',
		'default'		 => 'no'
	),
	'use_pickup_address'	  => array(
		'title'		   => __( 'Use Different Pickup Address', 'wf-shipping-fedex' ),
		'description'	 => __( 'Check this to set a defferent store address to pick up from', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'checkbox',
		'class'			  => 'wf_fedex_pickup_grp fedex_pickup_tab',
		'default'		 => 'no',
	),
	'pickup_contact_name'		   => array(
		'title'		   => __( 'Contact Person Name', 'wf-shipping-fedex' ),
		'description'	 => __( 'Contact person name', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_company_name'		   => array(
		'title'		   => __( 'Pickup Company Name', 'wf-shipping-fedex' ),
		'description'	 => __( 'Name of the company', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_phone_number'		   => array(
		'title'		   => __( 'Pickup Phone Number', 'wf-shipping-fedex' ),
		'description'	 => __( 'Contact number', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_address_line'		   => array(
		'title'		   => __( 'Pickup Address', 'wf-shipping-fedex' ),
		'description'	 => __( 'Address line', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_address_city'		   => array(
		'title'		   => __( 'Pickup City', 'wf-shipping-fedex' ),
		'description'	 => __( 'City', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_address_state_code'		   => array(
		'title'		   => __( 'Pickup State Code', 'wf-shipping-fedex' ),
		'description'	 => __( 'State code. Eg: CA', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_address_postal_code'		   => array(
		'title'		   => __( 'Pickup Zipcode', 'wf-shipping-fedex' ),
		'description'	 => __( 'Postal code', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_address_country_code'		   => array(
		'title'		   => __( 'Pickup Country Code', 'wf-shipping-fedex' ),
		'description'	 => __( 'Country code Eg: US', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'text',
		'class'			  => 'wf_fedex_pickup_grp wf_fedex_pickup_address_grp fedex_pickup_tab',
		'default'		 => '',
	),
	'pickup_start_time'		   => array(
		'title'		   => __( 'Pickup Start Time', 'wf-shipping-fedex' ),
		'description'	 => __( 'Items will be ready for pickup by this time from shop', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'select',
		'class'			  => 'wf_fedex_pickup_grp wc-enhanced-select fedex_pickup_tab',
		'default'		 => current($pickup_start_time_options),
		'options'		  => $pickup_start_time_options,
	),
	'pickup_close_time'		   => array(
		'title'		   => __( 'Company Close Time', 'wf-shipping-fedex' ),
		'description'	 => __( 'Your shop closing time. It must be greater than company open time', 'wf-shipping-fedex' ),
		'desc_tip'		   => true,
		'type'			=> 'select',
		'class'			  => 'wf_fedex_pickup_grp wc-enhanced-select fedex_pickup_tab',
		'default'		 => '18',
		'options'		  => $pickup_close_time_options,
	),

	'freight'		   => array(
		'title'		   => __( 'FedEx LTL Freight Settings', 'wf-shipping-fedex' ),
		'type'			=> 'title',
		'class'			=> 'fedex_freight_tab',
		'description'	 => __( 'If your account supports Freight, we need some additional details to get LTL rates. Note: These rates require the customers CITY so won\'t display until checkout.', 'wf-shipping-fedex' ),
	),
	'freight_enabled'	  => array(
		'title'		   => __( 'FedEx Freight', 'wf-shipping-fedex' ),
		'label'		   => __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_freight_tab',
		'default'		 => 'no'
	),
	'freight_number' => array(
		'title'	   => __( 'Freight Account Number', 'wf-shipping-fedex' ),
		'type'		=> 'text',
		'class'			=> 'fedex_freight_tab freight_group ',
		'description' => '',
		'default'	 => '',
		'placeholder' => __( 'Defaults to your main account number', 'wf-shipping-fedex' )
	),
	'freight_bill_street'		   => array(
		'title'		   => __( 'Billing Street Address', 'wf-shipping-fedex' ),
		'class'			=> 'fedex_freight_tab freight_group',
		'type'			=> 'text',
		'default'		 => ''
	),
	'billing_street_2'		   => array(
		'title'		   => __( 'Billing Street Address 2', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		 => ''
	),
	'freight_billing_city'		   => array(
		'title'		   => __( 'Billing City', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		 => ''
	),
	'freight_billing_state'		   => array(
		'title'		   => __( 'Billing State Code', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		 => '',
	),
	'billing_postcode'		   => array(
		'title'		   => __( 'Billing Zipcode', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		 => '',
	),
	'billing_country'		   => array(
		'title'		   => __( 'Billing Country Code', 'wf-shipping-fedex' ),
		'type'			=> 'text',
		'class'			=> 'fedex_freight_tab ph_fedex_frieght_billing_country freight_group',
		'default'		 => '',
	),
	
	'freight_class'		   => array(
		'title'		   => __( 'Default Freight Class', 'wf-shipping-fedex' ),
		'desc_tip'	=> true,
		'description'	 => sprintf( __( 'This is the default freight class for shipments. This can be overridden using <a href="%s">shipping classes</a>', 'wf-shipping-fedex' ), admin_url( 'edit-tags.php?taxonomy=product_shipping_class&post_type=product' ) ),
		'type'			=> 'select',
		'default'		 => '50',
		'class'		   => 'wc-enhanced-select fedex_freight_tab freight_group',
		'options'		 => $freight_classes
	),

	'freight_document_type' 		=> array(
		'title' 		=> __( 'Freight Document Type', 'wf-shipping-fedex' ),
		'type'			=> 'select',
		'default' 		=> 'VICS_BILL_OF_LADING',
		'class' 		=> 'wc-enhanced-select fedex_freight_tab freight_group',
		'options' 		=> array(
			'VICS_BILL_OF_LADING' 						=> __( 'VICS BILL OF LADING', 'wf-shipping-fedex' ),
			'FEDEX_FREIGHT_STRAIGHT_BILL_OF_LADING' 	=> __( 'FEDEX FREIGHT STRAIGHT BILL OF LADING', 'wf-shipping-fedex' ),
		),
	),

	'lift_gate_for_delivery'	=> array(
		'title'			=> __( 'Lift Gate Delivery', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		=> 'no'
	),

	'lift_gate_for_pickup'	=> array(
		'title'			=> __( 'Lift Gate Pickup', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		=> 'no'
	),

	'inside_delivery'	=> array(
		'title'			=> __( 'Inside Delivery', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		=> 'no'
	),
	
	'inside_pickup'	=> array(
		'title'			=> __( 'Inside Pickup', 'wf-shipping-fedex' ),
		'label'			=> __( 'Enable', 'wf-shipping-fedex' ),
		'type'			=> 'checkbox',
		'class'			=> 'fedex_freight_tab freight_group',
		'default'		=> 'no'
	),

	// Help & Support

	'help_and_support'  => array(
		'type'			=> 'help_support_section'
	),
	
);