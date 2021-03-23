jQuery(function($){
	xa_fedex_show_selected_tab($(".tab_general"),"general");
	$(".tab_general").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"general");
	});
	$(".tab_rates").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"rates");
	});
	$(".tab_labels").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"label");
	});
	$(".tab_commercial_invoice").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"commercial_invoice");
	});
	$(".tab_packaging").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"packaging");
	});
	$(".tab_pickup").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"pickup");
	});
	$(".tab_freight").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"freight");
	});
	$(".tab_help_and_support").on("click",function(){
		return xa_fedex_show_selected_tab($(this),"help_and_support");
	});

	function xa_fedex_show_selected_tab($element,$tab)
	{	
		$(".ph-fedex-tab").removeClass("nav-tab-active");
		$element.addClass("nav-tab-active");
			   
		$(".fedex_rates_tab").closest("tr,h3").hide();
		$(".fedex_rates_tab").next("p").hide();

		$(".fedex_general_tab").closest("tr,h3").hide();
		$(".fedex_general_tab").next("p").hide();

		$(".fedex_label_tab").closest("tr,h3").hide();
		$(".fedex_label_tab").next("p").hide();

		$(".fedex_commercial_invoice_tab").closest("tr,h3").hide();
		$(".fedex_commercial_invoice_tab").next("p").hide();

		$(".fedex_packaging_tab").closest("tr,h3").hide();
		$(".fedex_packaging_tab").next("p").hide();

		$(".fedex_pickup_tab").closest("tr,h3").hide();
		$(".fedex_pickup_tab").next("p").hide();

		$(".fedex_freight_tab").closest("tr,h3").hide();
		$(".fedex_freight_tab").next("p").hide();

		$(".fedex_help_and_support_tab").closest("tr,h3").hide();
		$(".fedex_help_and_support_tab").next("p").hide();

		$(".fedex_"+$tab+"_tab").closest("tr,h3").show();
		$(".fedex_"+$tab+"_tab").next("p").show();

		if( $tab == 'label' ){
			wf_fedex_return_label_options();
			wf_fedex_custom_shipment_message();
			wf_fedex_automatic_label_generation();
			ph_fedex_toggle_doc_tab();
			ph_fedex_toggle_csb_shipments();
		}
		if( $tab == 'general' ){
			xa_fedex_duties_payer_options();
			xa_fedex_payment_type_options();
			ph_fedex_toggle_alt_return_address();
			ph_fedex_toggle_alt_return_address_as_billing();
		}
		if( $tab == 'pickup' ){
			wf_fedex_load_pickup_options();
			wf_fedex_load_pickup_address_options();
		}
		if( $tab == 'packaging' ){
			wf_fedex_load_packing_method_options();
			xa_fedex_packing_method_options();
			ph_fedex_hazmat_package_options();
		}
		if( $tab == 'freight' ){
			wf_fedex_load_freight_options()
		}
		if( $tab == 'rates' ){
			ph_fedex_toggle_alt_estimated_delivery();
			wf_fedex_load_availability_options()
		}
		if( $tab == 'commercial_invoice' ){
			wf_fedex_load_commercialinvoice_image_uploader();
			ph_fedex_load_commercial_invoice_toggler();
		}

		return_reason = jQuery('.ph_return_label_return').val();

		if( return_reason == 'OTHER' && $tab == 'label' )
		{
			jQuery('.ph_return_label_desc').closest('tr').show();
		}else{
			jQuery('.ph_return_label_desc').closest('tr').hide();
		}

		if( $tab == 'help_and_support' ){
			jQuery(".woocommerce-save-button").hide();
		}else{
			jQuery(".woocommerce-save-button").show();	
		}

		return false;
	}

	// Toggle Doc Tab
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_doc_tab_content').click(function(){
		ph_fedex_toggle_doc_tab();
	});

	// Toggle CSB Shipments
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_csb5_shipments').click(function(){
		ph_fedex_toggle_csb_shipments();
	});

	// Toggle pickup options pickup
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_pickup_enabled').click(function(){
		wf_fedex_load_pickup_options();
		wf_fedex_load_pickup_address_options();
	});

	// Toggle Freight options pickup
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_freight_enabled').click(function(){
		wf_fedex_load_freight_options();
	});

	// Toggle Image uploader
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_commercial_invoice').click(function(){
		wf_fedex_load_commercialinvoice_image_uploader();
		ph_fedex_load_commercial_invoice_toggler();
	});

	// Toggle Est. Delivery Date
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_delivery_time').click(function(){
		ph_fedex_toggle_alt_estimated_delivery();
	});

	// Toggle Alternative Return Address
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_alternate_return_address').click(function(){
		ph_fedex_toggle_alt_return_address();
	});

	// Toggle Alternative Return Address as Billing
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_billing_as_alternate_return_address').click(function(){
		ph_fedex_toggle_alt_return_address_as_billing();
	});

	// Toggle pickup options pickup address
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_use_pickup_address').click(function(){
		wf_fedex_load_pickup_address_options();
	});

	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_charges_payment_type').change(function(){
		xa_fedex_payment_type_options();
	});


	//myaccount return label
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_frontend_retun_label').click(function(){
		wf_fedex_return_label_options();
	});


	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_customs_duties_payer').change(function(){
		xa_fedex_duties_payer_options()
	});

	jQuery('.packing_method').change(function(){
		wf_fedex_load_packing_method_options();
		xa_fedex_packing_method_options();
	});

	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_hazmat_enabled').click(function(){
		ph_fedex_hazmat_package_options();
	});

	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_tracking_shipmentid').click(function(){
		wf_fedex_custom_shipment_message();
	});
	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_automate_package_generation').click(function(){
		wf_fedex_automatic_label_generation();
	});

	// FedEx Shipping Details Toggle
	jQuery('.ph_fedex_other_details').next('.ph_fedex_hide_show_product_fields').hide();
	jQuery('.ph_fedex_other_details').click(function(event){
		event.stopImmediatePropagation();
		jQuery('.toggle_symbol').toggleClass('toggle_symbol_click');
		jQuery(this).next('.ph_fedex_hide_show_product_fields').toggle();
	});

	// FedEx Shipping Details Toggle - Variation Level
	jQuery(document).on('click','.ph_fedex_var_other_details',function(){
		event.stopImmediatePropagation();
		jQuery(this).find('.var_toggle_symbol').toggleClass('var_toggle_symbol_click');
		jQuery(this).next('.ph_fedex_hide_show_var_product_fields').toggle();
	});

	// Toggle Dangerous Goods
	ph_fedex_toggle_dangerous_goods();
	jQuery('#_dangerous_goods').change(function(){
		ph_fedex_toggle_dangerous_goods();
	});

	// Toggle Dangerous Goods - Variation Level - By Default
	jQuery(document).on('click','.woocommerce_variation',function(){
		ph_fedex_toggle_var_dangerous_goods_on_load(this);
	});

	// Toggle Dangerous Goods - Variation Level - On Click
	jQuery(document).on('change','input.ph_fedex_variation_dangerous_goods',function(){
		ph_fedex_toggle_var_dangerous_goods(this);
	});
	// End of Toggle Dangerous Goods

	// Toggle Hazardous Materials
	ph_fedex_toggle_hazardous_materials();
	jQuery('#_hazmat_products').change(function(){
		ph_fedex_toggle_hazardous_materials();
	});

	// Toggle Hazardous Materials - Variation Level - By Default
	jQuery(document).on('click','.woocommerce_variation',function(){
		ph_fedex_toggle_var_hazardous_materials_on_load(this);
	});

	// Toggle Hazardous Materials - Variation Level - On Click
	jQuery(document).on('change','input.ph_fedex_variation_hazmat_product',function(){
		ph_fedex_toggle_var_hazardous_materials(this);
	});
	// End of Toggle Hazardous Materials

	jQuery(document).on('change','select.ph_return_label_return',function(){
		if ($(this).val() == 'OTHER') {
			jQuery('.ph_return_label_desc').closest('tr').show();
		}else{
			jQuery('.ph_return_label_desc').closest('tr').hide();
		}
	});

	jQuery('.ph_return_label_desc').attr({'maxlength':25});

	/********************************************* Help & Support Send Report Settings ************************************************/

	jQuery('#ph_fedex_ticket_number').keyup( function(){
		jQuery('#ph_fedex_ticket_number').removeClass('required_field');
		jQuery('.ph_fedex_ticket_number_error').hide();
	});

	jQuery("#ph_fedex_consent").click( function() {
		jQuery('#ph_fedex_consent').removeClass('required_field');
		jQuery('.ph_fedex_consent_error').hide();
	});

	jQuery("#ph_fedex_submit_ticket").click( function() {

		jQuery('.ph_error_message').remove();

		var required 	= false;
		var ticket_num 	= jQuery('#ph_fedex_ticket_number').val();
		var consent 	= jQuery('#ph_fedex_consent').is(':checked');

		if( !ticket_num ) {
			jQuery('#ph_fedex_ticket_number').addClass('required_field');
			jQuery('.ph_fedex_ticket_number_error').show();
			required 	= true;
		}

		if( !consent ) {
			jQuery('#ph_fedex_consent').addClass('required_field');
			jQuery('.ph_fedex_consent_error').show();
			required 	= true;
		}

		if( required ) {
			return false;
		}
		// Change Text and Disable the Button
		jQuery("#ph_fedex_submit_ticket").prop("value", "Please Wait...");
		jQuery("#ph_fedex_submit_ticket").attr( 'disabled', 'disabled');
		
		let key_data = {
			action 		: 'ph_get_fedex_log_data',
		}

		jQuery.post( ajaxurl, key_data, function( result, status ) {

			console.log(result);

			try{

				let response = JSON.parse(result);

				if( response.status == true ) {

						let key_data = {
							action 		: 'ph_fedex_submit_support_ticket',
							ticket_num 	: ticket_num,
							log_file	: response.file_path
						}

						jQuery.post( ajaxurl, key_data, function( result, status ) {

							let response2 = JSON.parse(result);

							if( response2.status == true ) {
								message = "<b>Diagnostic report sent successfully.</b> PluginHive Support Team will contact you shortly via email."
								jQuery( ".ph_fedex_help_table" ).after( "<p style='color:green;' class='ph_error_message'>"+message+"</p>" );

								// Add original text and enable the button
								jQuery("#ph_fedex_submit_ticket").prop("value", "Send Report");
								jQuery("#ph_fedex_submit_ticket").removeAttr("disabled");
							} else {

								// Add original text and enable the button
								jQuery("#ph_fedex_submit_ticket").prop("value", "Send Report");
								jQuery("#ph_fedex_submit_ticket").removeAttr("disabled");
							}
							
						});

				}else{
					message = response.message;
					jQuery( ".ph_fedex_help_table" ).after( "<p style='color:red;' class='ph_error_message'>"+message+"</p>" );

					// Add original text and enable the button
					jQuery("#ph_fedex_submit_ticket").prop("value", "Send Report");
					jQuery("#ph_fedex_submit_ticket").removeAttr("disabled");
				}

			} catch(err) {
				alert(err.message);

				// Add original text and enable the button
				jQuery("#ph_fedex_submit_ticket").prop("value", "Send Report");
				jQuery("#ph_fedex_submit_ticket").removeAttr("disabled");
			}
			
		});
	});

});

jQuery( document ).ready( function( $ ) {
	if($('#_wf_dry_ice').is(':checked'))
	{
		$('#shipping_product_data ._wf_dry_ice_weight_field').show();
	}
	else
	{
		$('#shipping_product_data ._wf_dry_ice_weight_field').hide();
	}
	$('#_wf_dry_ice').click(function(){
		if($('#_wf_dry_ice').is(':checked'))
		{
			$('#shipping_product_data ._wf_dry_ice_weight_field').show();
		}
		else
		{
			$('#shipping_product_data ._wf_dry_ice_weight_field').hide();
		}
	});
});
function wf_fedex_load_packing_method_options(){
	pack_method	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_packing_method').val();      // class packing_method
	jQuery('#fedex_packing_options').hide();
	jQuery('.fedex_weight_based_option').closest('tr').hide();
	switch(pack_method){
		case 'per_item':
		default:
			break;
			
		case 'box_packing':
			jQuery('#fedex_packing_options').show();
			break;
			
		case 'weight_based':
			jQuery('.fedex_weight_based_option').closest('tr').show();
			break;
	}
}


function xa_fedex_packing_method_options(){
	pack_method	=	jQuery('.packing_method').val();
	if( pack_method != 'box_packing'){
		jQuery('.speciality_box').closest('tr').hide();
	}

	jQuery('.packing_method').change(function(){
		if( pack_method == 'box_packing'){
			jQuery('.speciality_box').closest('tr').show();
		}else{
			jQuery('.speciality_box').closest('tr').hide();
		}
	});
}

function ph_fedex_toggle_doc_tab() {

	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_doc_tab_content').is(":checked");
	
	if(checked) {
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_doc_tab_orientation').closest('tr').show();
	}else{
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_doc_tab_orientation').closest('tr').hide();
	}
}

function ph_fedex_toggle_csb_shipments() {

	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_csb5_shipments').is(":checked");
	
	if(checked) {
		jQuery('.ph_fedex_csb5').closest('tr').show();
	}else{
		jQuery('.ph_fedex_csb5').closest('tr').hide();
	}
}

function ph_fedex_hazmat_package_options(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_hazmat_enabled').is(":checked");
	if(checked){
		jQuery('.ph_fedex_hazmat_grp').closest('tr').show();
	}else{
		jQuery('.ph_fedex_hazmat_grp').closest('tr').hide();
	}
}

function xa_fedex_payment_type_options(){
	me = jQuery('#woocommerce_wf_fedex_woocommerce_shipping_charges_payment_type');
	if( me.val() =='THIRD_PARTY' ){
		jQuery('.thirdparty_grp').closest('tr').show();
	}else{
		jQuery('.thirdparty_grp').closest('tr').hide();
	}
}

function xa_fedex_duties_payer_options(){
	me = jQuery('#woocommerce_wf_fedex_woocommerce_shipping_customs_duties_payer');
	if( me.val() =='THIRD_PARTY' ){
		jQuery('.broker_grp').closest('tr').show();
	}else{
		jQuery('.broker_grp').closest('tr').hide();
	}
}

function wf_fedex_return_label_options(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_frontend_retun_label').is(":checked");
	if(checked){
		jQuery('.ph_fedex_return_label').closest('tr').show();
	}else{
		jQuery('.ph_fedex_return_label').closest('tr').hide();
	}
}
function wf_fedex_load_freight_options(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_freight_enabled').is(":checked");
	if(checked){
		jQuery('.freight_group').closest('tr').show();
	}else{
		jQuery('.freight_group').closest('tr').hide();
	}
}
function wf_fedex_load_pickup_options(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_pickup_enabled').is(":checked");
	if(checked){
		jQuery('.wf_fedex_pickup_grp').closest('tr').show();
	}else{
		jQuery('.wf_fedex_pickup_grp').closest('tr').hide();
	}
}
function wf_fedex_load_pickup_address_options(){
	var pickup_checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_use_pickup_address').is(":checked");
	var address_checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_pickup_enabled').is(":checked");
	if( pickup_checked && address_checked ){
		jQuery('.wf_fedex_pickup_address_grp').closest('tr').show();
	}else{
		jQuery('.wf_fedex_pickup_address_grp').closest('tr').hide();
	}
}

function wf_fedex_load_commercialinvoice_image_uploader(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_commercial_invoice').is(":checked");
	if(checked){
		jQuery('.commercialinvoice-image-uploader').closest('tr').show();
	}else{
		jQuery('.commercialinvoice-image-uploader').closest('tr').hide();
	}
}

function ph_fedex_load_commercial_invoice_toggler(){
	var checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_commercial_invoice').is(":checked");
	if(checked){
		jQuery('.commercial_invoice_toggle').closest('tr').show();
	}else{
		jQuery('.commercial_invoice_toggle').closest('tr').hide();
	}
}

function wf_fedex_load_availability_options(){
	me = jQuery('#woocommerce_wf_fedex_woocommerce_shipping_availability');
	if( me.val() =='all' ){
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_countries').closest('tr').hide();
	}else{
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_countries').closest('tr').show();
	}
}
function wf_fedex_custom_shipment_message(){
	checked = jQuery('#woocommerce_wf_fedex_woocommerce_shipping_tracking_shipmentid').is(":checked");
	if(checked){
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_custom_message').closest('tr').show();
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_disable_customer_tracking').closest('tr').show();
	}else{
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_custom_message').closest('tr').hide();
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_disable_customer_tracking').closest('tr').hide();
	}
}
function wf_fedex_automatic_label_generation(){
	checked = jQuery('#woocommerce_wf_fedex_woocommerce_shipping_automate_package_generation').is(":checked");
	if(checked){
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_automate_label_generation').closest('tr').show();
	}else{
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_automate_label_generation').closest('tr').hide();
	}
}

/**
 * Toggle Estimated Delivery
**/
function ph_fedex_toggle_alt_estimated_delivery(){

	var est_delivery	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_delivery_time').is(":checked");

	if( est_delivery ){
		jQuery('.ph_fedex_est_delivery_date').closest('tr').show();
	}else{
		jQuery('.ph_fedex_est_delivery_date').closest('tr').hide();
	}
}

/**
 * Toggle Alternative Return Address
**/
function ph_fedex_toggle_alt_return_address(){

	var alt_address_checked	=	jQuery('#woocommerce_wf_fedex_woocommerce_shipping_alternate_return_address').is(":checked");

	if( alt_address_checked ){
		jQuery('.ph_fedex_alt_return_address').closest('tr').show();
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_billing_as_alternate_return_address').closest('tr').show();
	}else{
		jQuery('.ph_fedex_alt_return_address').closest('tr').hide();
		jQuery('#woocommerce_wf_fedex_woocommerce_shipping_billing_as_alternate_return_address').closest('tr').hide();
	}

	ph_fedex_toggle_alt_return_address_as_billing();
}

/**
 * Toggle Billing as Alternative Return Address
**/
function ph_fedex_toggle_alt_return_address_as_billing(){

	var alt_address_checked		= jQuery('#woocommerce_wf_fedex_woocommerce_shipping_alternate_return_address').is(":checked");
	var alt_billing_address		= jQuery('#woocommerce_wf_fedex_woocommerce_shipping_billing_as_alternate_return_address').is(":checked");

	if( alt_address_checked && !alt_billing_address ){
		jQuery('.ph_fedex_alt_return_address').closest('tr').show();
	}else{
		jQuery('.ph_fedex_alt_return_address').closest('tr').hide();
	}
}

/**
 * Toggle Dangerous Goods Settings
**/
function ph_fedex_toggle_dangerous_goods(){
	if( jQuery("#_dangerous_goods").is(":checked") ){
		jQuery(".ph_fedex_dangerous_goods").show();
	}
	else{
		jQuery(".ph_fedex_dangerous_goods").hide();
	}
}

/**
 * Toggle Dangerous Goods Settings - Variation Level - Onload
**/
 function ph_fedex_toggle_var_dangerous_goods_on_load(e){
 	if( jQuery(e).find(".ph_fedex_variation_dangerous_goods").is(':checked') ){
 		jQuery(e).find(".ph_fedex_var_dangerous_goods").show();
 	}else{
 		jQuery(e).find(".ph_fedex_var_dangerous_goods").hide();
 	}
 }

/**
 * Toggle Dangerous Goods Settings - Variation Level - Onclick
**/
 function ph_fedex_toggle_var_dangerous_goods(e){
 	if( jQuery(e).is(':checked') ){
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_fedex_var_dangerous_goods").show();
 	}else{
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_fedex_var_dangerous_goods").hide();
 	}
 }

/**
 * Toggle Hazardous Materials Settings
**/
function ph_fedex_toggle_hazardous_materials(){
	if( jQuery("#_hazmat_products").is(":checked") ){
		jQuery(".ph_fedex_hazardous_materials").show();
	}
	else{
		jQuery(".ph_fedex_hazardous_materials").hide();
	}
}

/**
 * Toggle Hazardous Materials Settings - Variation Level - Onload
**/
 function ph_fedex_toggle_var_hazardous_materials_on_load(e){
 	if( jQuery(e).find(".ph_fedex_variation_hazmat_product").is(':checked') ){
 		jQuery(e).find(".ph_fedex_var_hazardous_materials").show();
 	}else{
 		jQuery(e).find(".ph_fedex_var_hazardous_materials").hide();
 	}
 }

/**
 * Toggle Hazardous Materials Settings - Variation Level - Onclick
**/
 function ph_fedex_toggle_var_hazardous_materials(e){
 	if( jQuery(e).is(':checked') ){
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_fedex_var_hazardous_materials").show();
 	}else{
 		jQuery(e).closest( '.woocommerce_variation' ).find(".ph_fedex_var_hazardous_materials").hide();
 	}
 }

jQuery( document ).ready( function( $ ) {
	$('#xa_fedex_validate_credentials').on('click', function( event ){
		jQuery( ".fedex-validation-result").html('<span style="float:left" class="spinner is-active"&nbsp;</span>' );
		event.preventDefault();
		var data = {
			'action'		: 'xa_fedex_validate_credential',
			'production'	: $('#woocommerce_wf_fedex_woocommerce_shipping_production').is(":checked") ? true : false,
			'account_number': $('#woocommerce_wf_fedex_woocommerce_shipping_account_number').val(),
			'meter_number'	: $('#woocommerce_wf_fedex_woocommerce_shipping_meter_number').val(),
			'api_key'		: $('#woocommerce_wf_fedex_woocommerce_shipping_api_key').val(),
			'api_pass'		: $('#woocommerce_wf_fedex_woocommerce_shipping_api_pass').val(),
			'origin'		: $('#woocommerce_wf_fedex_woocommerce_shipping_origin').val(),
			'origin_country': $('#woocommerce_origin_country_state').val(),
		};

		jQuery.post(ajaxurl, data, function(response) {
			response = JSON.parse(response);
			if( response.success=='yes' ){
				$(".fedex-validation-result").html('<span style="color: green;">'+response.message+'</span>')
			}else{
				$(".fedex-validation-result").html('<span style="color: red">'+response.message+'</span>')
			}
		});
	});
	

	
	var file_frame;
	$('#company_logo_picker').on('click', function( event ){

		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select a image to set Company Logo on Commercial invoice',
			button: {
				text: 'Use this image',
			},
			multiple: false
		});
		file_frame.on( 'select', function() {
			$( "#company_logo_result").html('<span style="float:left" class="spinner is-active"&nbsp;</span>' );

			attachment = file_frame.state().get('selection').first().toJSON();
			$( '#woocommerce_wf_fedex_woocommerce_shipping_company_logo' ).val( attachment.url );
			
			var data = {
				'action': 'xa_fedex_upload_image',
				'image': attachment.url,
				'image_id': 'IMAGE_1' //If changed image id here, Change in admin_helper.php also
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				response = JSON.parse(response);
				if( response.success==true ){
					$("#company_logo_result").html('<span style="color: #35a335;">'+response.message+'</span>')
				}else{
					$("#company_logo_result").html('<span style="color: #d83434;">'+response.message+'</span>')
				}
			});
		});
		file_frame.open();
	});
	
	$('#digital_signature_picker').on('click', function( event ){
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select a image to set Digital Signature on Commercial invoice',
			button: {
				text: 'Use this image',
			},
			multiple: false
		});
		file_frame.on( 'select', function() {
			$( "#digital_signature_result").html('<span style="float:left" class="spinner is-active"&nbsp;</span>' );
			attachment = file_frame.state().get('selection').first().toJSON();
			$( '#woocommerce_wf_fedex_woocommerce_shipping_digital_signature' ).val( attachment.url );
			var data = {
				'action': 'xa_fedex_upload_image',
				'image': attachment.url,
				'image_id': 'IMAGE_2' //If changed image id here, Change in admin_helper.php also
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				response = JSON.parse(response);
				if( response.success==true ){
					$("#digital_signature_result").html('<span style="color: #35a335;">'+response.message+'</span>')
				}else{
					$("#digital_signature_result").html('<span style="color: #d83434;">'+response.message+'</span>')
				}
			});
		});
		file_frame.open();
	});

});