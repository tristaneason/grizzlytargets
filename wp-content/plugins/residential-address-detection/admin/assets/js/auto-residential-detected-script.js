jQuery(document).ready(function () {

//   Add classes for further style css  
    jQuery("#en_residential_addresses_template_start").closest('tr').addClass('en_residential_addresses_template_start_style');
    jQuery("#en_residential_addresses_template_start").closest('table').addClass('en_residential_addresses_template_table_start');

    jQuery(".en_woo_addons_always_include_residential_fee").closest('tr').addClass('en_woo_addons_always_include_residential_fee_style');
    jQuery("#residential_delivery_options_label_heading").closest('tr').addClass('residential_delivery_options_label_heading_style');
    jQuery(".en_default_unconfirmed_address_types_to").closest('tr').addClass('en_default_unconfirmed_address_types_to_style');
    jQuery("#residential_delivery_options_label_description,#residential_delivery_options_addon_description,#en_default_unconfirmed_address_types_label_description").closest('tr').addClass('residential_delivery_options_label_description_style');
    jQuery("#residential_delivery_options_addon_description").closest('tr').addClass('residential_delivery_options_addon_description_style');
    jQuery("#en_default_unconfirmed_address_types_label_description").closest('tr').addClass('residential_delivery_options_addon_description_style');
    jQuery("#suspend_automatic_detection_of_residential_addresses").closest('tr').addClass('suspend_automatic_detection_of_residential_addresses_style');
    jQuery("#auto_residential_delivery_plan_auto_renew").closest('tr').addClass('residential_delivery_options_plans_style');
    jQuery("#residential_delivery_current_subscription").closest('tr').addClass('residential_delivery_current_subscription_style');
    jQuery("#residential_delivery_current_usage").closest('tr').addClass('residential_delivery_current_usage_style');
    jQuery("#residential_delivery_plugin_name").closest('tr').addClass('residential_delivery_plugin_name_style');
    jQuery("#residential_delivery_subscription_status").closest('tr').addClass('residential_delivery_subscription_status_style');

    jQuery(".residential_delivery_options_label_description_style td").attr("colspan", "2");

    /**
     * When RAD plan suspended.
     * @param object params
     * @param object response
     * @returns null
     */
    var en_need_suspended_rad = function (params = "", response = "") {
        response = JSON.parse(response);
        var en_need_suspended_rad_content = "Residential address settings are being managed under the RAD (Residential Address Detection) page, which you can access in this plugin's navigation above. To use this setting, you must first deactivate or suspend the Residential Address Detection plugin";
        if (typeof response.en_need_suspended_rad != 'undefined' && response.en_need_suspended_rad == true) {
            jQuery(".en-need-suspended-rad-message").remove();
            jQuery(".en_need_suspended_rad").prop("checked", false);
            if (jQuery('.en_need_suspended_rad_content').length) {
                jQuery('.en_need_suspended_rad_content').css('color', 'red');
            } else {
                jQuery(".en_need_suspended_rad").after("<span style='color: red;' class='en_need_suspended_rad_content'>" + en_need_suspended_rad_content + "</span>");
            }
        } else {
            if (jQuery('.en_need_suspended_rad_content').length) {
                jQuery('.en_need_suspended_rad_content').remove();
            }
        }
    };

    /*
     * When click on always RAD with disabled plan of RAD.
     */
    jQuery(".en_need_suspended_rad").on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery('.en-need-suspended-rad-message').remove();
            var params = data = {
                action: "en_need_suspended_rad_ajax",
                loading_id: jQuery(".en_need_suspended_rad").attr('id'),
            };
            ajax_request(params, data, en_need_suspended_rad);
        }
    });

    /**
     * when user switch from disable to plan popup hide
     * @returns {jQuery}
     */
    var en_woo_addons_popup_notifi_disabl_to_plan_hide = function () {
        jQuery(".sm_notification_disable_to_plan_overlay").hide();
        return jQuery(".sm_notification_disable_to_plan_overlay").css({visibility: "hidden", opacity: "0"});
    };

    /**
     * when user switch from disable to plan popup show
     * @returns {jQuery}
     */
    var en_woo_addons_popup_notifi_disabl_to_plan_show = function () {
        var selected_plan = jQuery("#auto_residential_delivery_plan_auto_renew").find("option:selected").text();
        jQuery(".sm_notification_disable_to_plan_overlay").last().find("#selected_plan_popup").text(selected_plan);
        jQuery(".sm_notification_disable_to_plan_overlay").show();
        return jQuery(".sm_notification_disable_to_plan_overlay").css({visibility: "visible", opacity: "1"});
    };

    /**
     * when user from disable to plan popup actions
     * @returns {undefined}
     */

    jQuery(".cancel_plan").on('click', function () {
        en_woo_addons_popup_notifi_disabl_to_plan_hide();
        jQuery('#auto_residential_delivery_plan_auto_renew').prop('selectedIndex', 0);
        return false;
    });

    jQuery(".confirm_plan").on('click', function () {
        var params = "";
        en_woo_addons_popup_notifi_disabl_to_plan_hide();
        var monthly_pckg = jQuery("#auto_residential_delivery_plan_auto_renew").val();
        var plugin_name = jQuery("#residential_delivery_plugin_name").attr("placeholder");
        var data = {plugin_name: plugin_name, selected_plan: monthly_pckg, action: 'en_woo_addons_upgrade_plan_submit'};
        params = {
            loading_id: "auto_residential_delivery_plan_auto_renew",
            message_id: "plan_to_disable_message",
            message_ph: " Success! Your choice of plans has been updated. "
        };
        ajax_request(params, data, monthly_packg_response);
        return false;
    });

    var monthly_packg_response = function (params, response) {

        var parsRes = JSON.parse(response);
        if (parsRes.severity == "SUCCESS") {

            jQuery("#auto_residential_delivery_plan_auto_renew option[value='TR']").remove();

            if (typeof parsRes.subscription_packages_response != 'undefined' && parsRes.subscription_packages_response == "yes") {
                jQuery("#residential_delivery_current_subscription").next('.description').html(parsRes.current_subscription);
                jQuery("#residential_delivery_current_usage").next('.description').html(parsRes.current_usage);
                jQuery("#residential_delivery_subscription_status").attr("placeholder", "yes");
                jQuery("#suspend_automatic_detection_of_residential_addresses").prop('disabled', false);
            }
            if (typeof params.message_ph != 'undefined' && params.message_ph.length > 0) {
                if (jQuery(".en_woo_addons_always_include_residential_fee").length) {
                    jQuery(".en_woo_addons_always_include_residential_fee").closest('tr').after(' <tr><td colspan="2"><div class="alert-plan-messages alert-success auto_resid_package_msg">  ' + params.message_ph + ' </div> </td> </tr>');
                } else {
                    jQuery(".residential_delivery_options_label_heading_style").after(' <tr><td colspan="2"><div class="alert-plan-messages alert-success auto_resid_package_msg">  ' + params.message_ph + ' </div> </td> </tr>');
                }
            }
            suspend_automatic_detection();
        } else {
            if (jQuery(".en_woo_addons_always_include_residential_fee").length) {
                jQuery(".en_woo_addons_always_include_residential_fee").closest('tr').after(' <tr><td colspan="2"><div class="alert-plan-messages alert-danger auto_resid_package_msg">  ' + parsRes.Message + ' </div> </td> </tr>');
            } else {
                jQuery(".residential_delivery_options_label_heading_style").after(' <tr><td colspan="2"><div class="alert-plan-messages alert-danger auto_resid_package_msg">  ' + parsRes.Message + ' </div> </td> </tr>');
            }
            jQuery('#auto_residential_delivery_plan_auto_renew').prop('selectedIndex', 0);
        }

        setTimeout(function () {
            jQuery('.alert-plan-messages').closest('tr').fadeOut('fast');
        }, 3000);
        jQuery("#auto_residential_delivery_plan_auto_renew").focus();
    };

    /**
     * monthly package select actions
     * @param {type} monthly_pckg
     * @returns {Boolean}
     */
    var en_woo_addons_monthly_packg = function (monthly_pckg) {
        jQuery(".auto_resid_package_msg").closest('tr').remove();
        var plugin_name = jQuery("#residential_delivery_plugin_name").attr("placeholder");
        var data = {plugin_name: plugin_name, selected_plan: monthly_pckg, action: 'en_woo_addons_upgrade_plan_submit'};
        var params = "";
        if (window.existing_plan == "disable") {

            en_woo_addons_popup_notifi_disabl_to_plan_show();
            return false;
        } else if (monthly_pckg == "disable") {

            params = {
                loading_id: "auto_residential_delivery_plan_auto_renew",
                disabled_id: "auto_residential_delivery_plan_auto_renew",
                message_ph: " You have disabled the Residential Address Detection plugin. The plugin will stop working when the current plan is depleted or expires."
            };
        } else {

            params = {
                loading_id: "auto_residential_delivery_plan_auto_renew",
                disabled_id: "auto_residential_delivery_plan_auto_renew",
                message_ph: " Success! Your choice of plans has been updated. "
            };
        }
        ajax_request(params, data, monthly_packg_response);
    };

    /**
     * When RAD plan suspended.
     * @param object params
     * @param object response
     * @returns null
     */
    var suspend_automatic_detection = function (params = "", response = "") {
        var insi_with_auto_residential = jQuery("#en_woo_addons_inside_with_auto_residential").length ? true : false;
        var selected_plan = jQuery("#auto_residential_delivery_plan_auto_renew").val();
        window.existing_plan = selected_plan;
        var suspend_automatic = jQuery("#suspend_automatic_detection_of_residential_addresses").prop("checked");
        var subscription_status = jQuery("#residential_delivery_subscription_status").attr("placeholder");
        if (subscription_status == "yes") {
            jQuery(".residential_delivery_options_plans_style th label").text("Auto-renew");
            if (suspend_automatic) {
                jQuery(".en_woo_addons_always_include_residential_fee").prop('disabled', false);
                jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false, disabled: true});
                // White Glove
                if (insi_with_auto_residential == true) {
                    jQuery("#en_woo_addons_inside_with_auto_residential").prop({checked: false, disabled: true});
                }
            } else {
                jQuery(".en_woo_addons_always_include_residential_fee").prop({checked: false, disabled: true});
                if (subscription_status == "yes") {
                    jQuery("#en_woo_addons_liftgate_with_auto_residential").prop('disabled', false);
                    // White Glove
                    if (insi_with_auto_residential == true) {
                        jQuery("#en_woo_addons_inside_with_auto_residential").prop('disabled', false);
                    }
                }
            }
        } else {
            jQuery(".residential_delivery_options_plans_style th label").text("Select a plan");
            jQuery("#suspend_automatic_detection_of_residential_addresses").prop({checked: false, disabled: true});
            jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false, disabled: true});
            // White Glove
            if (insi_with_auto_residential == true) {
                jQuery("#en_woo_addons_inside_with_auto_residential").prop({checked: false, disabled: true});
            }
        }
    };

    /**
     * existing user plan for auto residential detection
     * @param {type} params
     * @param {type} data
     * @param {type} call_back_function
     * @returns {undefined}
     */
    suspend_automatic_detection();

    /**
     *
     * @param object params
     * @param object data
     * @param string call_back_function
     * @returns null
     */
    function ajax_request(params, data, call_back_function) {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            beforeSend: function () {
                (typeof params.loading_id != 'undefined' && params.loading_id.length > 0) ? jQuery("#" + params.loading_id).css('background', 'rgba(255, 255, 255, 1) url(' + plugins_url + '/residential-address-detection/admin/assets/images/processing.gif) no-repeat scroll 50% 50%') : "";
                (typeof params.disabled_id != 'undefined' && params.disabled_id.length > 0) ? jQuery("#" + params.disabled_id).prop({disabled: true}) : "";
                (typeof params.loading_msg != 'undefined' && params.loading_msg.length > 0 && typeof params.disabled_id != 'undefined' && params.disabled_id.length > 0) ? jQuery("#" + params.disabled_id).after(params.loading_msg) : "";
            },
            success: function (response) {
                jQuery('.notice-dismiss-residential-php').remove();
                (typeof params.loading_id != 'undefined' && params.loading_id.length > 0) ? jQuery("#" + params.loading_id).css('background', '#fff') : "";
                (typeof params.loading_id != 'undefined' && params.loading_id.length > 0) ? jQuery("#" + params.loading_id).focus() : "";
                (typeof params.disabled_id != 'undefined' && params.disabled_id.length > 0) ? jQuery("#" + params.disabled_id).prop({disabled: false}) : "";
                (typeof params.loading_msg != 'undefined' && params.loading_msg.length > 0 && typeof params.disabled_id != 'undefined' && params.disabled_id.length > 0) ? jQuery("#" + params.disabled_id).next('.suspend-loading').remove() : "";
                return call_back_function(params, response);
            },
            error: function () {
                console.log('error');
            }
        });
    }

    /**
     * plan change function for auto residential detection
     */
    jQuery("#auto_residential_delivery_plan_auto_renew").on('change', function () {
        en_woo_addons_monthly_packg(jQuery(this).val());
        return false;
    });

    /**
     * When RAD plan suspend message should be shown.
     * @returns {auto-residential-detected-scriptL#1.suspend_automatic_detection_params.auto-residential-detected-scriptAnonym$9}
     */
    var suspend_automatic_detection_params = function () {
        return {
            loading_msg: " <span class='suspend-loading'>Loading ...</span>",
            disabled_id: "suspend_automatic_detection_of_residential_addresses",
        };
    };

    /**
     * When RAD Plan enabled.
     * @returns {auto-residential-detected-scriptL#1.suspend_automatic_detection_anable.auto-residential-detected-scriptAnonym$10}
     */
    var suspend_automatic_detection_anable = function () {
        return {
            suspend_automatic_detection_of_residential_addresses: "yes",
            en_woo_addons_liftgate_with_auto_residential: "no",
            action: "suspend_automatic_detection",
        };
    };

    /**
     * When RAD plan enabled.
     * @returns {auto-residential-detected-scriptL#1.suspend_automatic_detection_disabled.auto-residential-detected-scriptAnonym$11}
     */
    var suspend_automatic_detection_disabled = function () {
        var always_include_residential_ind = jQuery(".en_woo_addons_always_include_residential_fee").attr("id");
        return {
            always_include_residential_ind: always_include_residential_ind,
            always_include_residential_val: "no",
            // White Glove
            rad_include_inside_ind: 'en_woo_addons_inside_with_auto_residential',
            rad_include_inside_val: "no",
            suspend_automatic_detection_of_residential_addresses: "no",
            action: "suspend_automatic_detection"
        };
    };

    /*
     * Suspend plan of RAD.
     */
    jQuery("#suspend_automatic_detection_of_residential_addresses").on('click', function () {
        var data = "";
        var params = "";
        if (this.checked) {
            data = suspend_automatic_detection_anable();
            params = suspend_automatic_detection_params();
        } else {
            data = suspend_automatic_detection_disabled();
            params = suspend_automatic_detection_params();
        }
        ajax_request(params, data, suspend_automatic_detection);
    });

    /*
     * Suspend plan of RAD.
     */
    jQuery(".en_default_unconfirmed_address_types_to").on('click', function () {
        jQuery('.en-default-unconfirmed-address-change').remove();
        var params = data = {
            en_default_unconfirmed_selected_address_types_to: jQuery(this).val(),
            action: "en_default_unconfirmed_address_types_to"
        };
        ajax_request(params, data, suspend_automatic_detection);
        jQuery(this).closest('label').after("<span class='en-default-unconfirmed-address-change'>Saved</span>");
        setTimeout(function () {
            jQuery('.en-default-unconfirmed-address-change').fadeOut('fast');
        }, 3000);
    });

});