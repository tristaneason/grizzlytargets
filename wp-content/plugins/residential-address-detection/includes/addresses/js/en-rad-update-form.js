/**
 * Timeout variable to reset on new instance
 */
var en_rad_address_notice_timeout = '';

/**
 * Residential address Validation Form JS
 */
if (typeof en_rad_validate_input != 'function') {
    function en_rad_validate_input(en_form_id) {
        var has_err = true;

        jQuery(en_form_id + " input[type='text'], " + en_form_id + " input[name='en_res_address_type']:checked").each(function () {
            var input = jQuery(this).val();
            var response = en_validate_string(input);
            var error_text = jQuery(this).attr('title');
            var optional = jQuery(this).data('optional');
            var minlength_field = jQuery(this).attr('minlength');
            var maxlength_field = jQuery(this).attr('maxlength');

            var error_element = jQuery(this).parent().find('.en_res_address_err');
            jQuery(error_element).html('');

            optional = (optional === undefined) ? 0 : 1;
            error_text = (error_text != undefined) ? error_text : '';

            if ((optional == 0) && (response == false || response == 'empty')) {
                error_text = (response == 'empty') ? error_text + ' is required.' : 'Invalid input.';
                jQuery(error_element).html(error_text);

            } else if (minlength_field && minlength_field != 'undefined' && input.length < minlength_field) {
                error_text = (response != 'empty') ? error_text + ' minimum ' + minlength_field + ' characters are required.' : 'Invalid input, minimum ' + minlength_field + ' are required.';
                jQuery(error_element).html(error_text);

            } else if (maxlength_field && maxlength_field != 'undefined' && input.length > maxlength_field) {
                error_text = (response != 'empty') ? error_text + ' can not exceed ' + maxlength_field + ' characters limit.' : 'Invalid input, maximum ' + maxlength_field + ' characters allowed.';
                jQuery(error_element).html(error_text);
            }

            has_err = (response != true && optional == 0) ? false : has_err;
        });

        return has_err;
    }
}

/**
 * Validate Input String
 * @param string
 */
if (typeof en_validate_string != 'function') {
    function en_validate_string(string) {
        if (string == '')
            return 'empty';
        else
            return true;
    }
}

/**
 * when user switch from disable to plan popup hide
 * @returns {jQuery}
 */
var en_woo_addons_popup_rad_address_hide = function () {
    return jQuery(".en_res_address_overlay").css({visibility: "hidden", opacity: "0"});
};

/**
 * when user switch from disable to plan popup show
 * @returns {jQuery}
 */
var en_woo_addons_popup_rad_address_show = function () {
    return jQuery(".en_res_address_overlay").css({visibility: "visible", opacity: "1"});
};

/**
 * when user switch from disable to plan popup hide
 * @returns {jQuery}
 */
var en_woo_addons_popup_rad_address_delete_hide = function () {
    return jQuery(".en_res_delete_address_overlay").css({visibility: "hidden", opacity: "0"});
};

/**
 * when user switch from disable to plan popup show
 * @returns {jQuery}
 */
var en_woo_addons_popup_rad_address_delete_show = function () {
    return jQuery(".en_res_delete_address_overlay").css({visibility: "visible", opacity: "1"});
};

/**
 * Show popup to add Address
 *
 * */
if (typeof en_res_add_address_btn != 'function') {
    function en_res_add_address_btn() {
        jQuery('#en_residential_addresses_form')[0].reset();
        jQuery('#en_res_edit_form_id').val('');
        jQuery('#en_res_address_addr').val('');
        jQuery('#en_res_address_addr_2').val('');
        jQuery('#en_res_address_city').css('background', 'none');
        jQuery("#en_res_address_city").val('');
        jQuery('.en_res_city_input').show('slow');
        jQuery('.en_res_select_city').css('display', 'none');
        jQuery('.en_res_select_city').hide();
        jQuery("#en_res_address_state").val('');
        jQuery("#en_res_address_zip").val('');
        jQuery("#en_res_country_code").val('');
        jQuery('#en_residential_addresses_form').find("input[type='text']").val("");
        jQuery(".en_res_address_err").html("");
        en_woo_addons_popup_rad_address_show();
        setTimeout(function () {
            if (jQuery('.en_res_add_address_popup').is(':visible')) {
                jQuery('.en_res_address_form_input > input').eq(0).focus();
            }
        }, 100);
    }
}

/**
 * Edit Address Custom Post
 * @param id
 * */
if (typeof en_rad_edit_address != 'function') {
    function en_rad_edit_address(id) {
        jQuery('#en_residential_addresses_form')[0].reset();
        jQuery('.en_res_address_err').html('');
        jQuery('#en_res_edit_form_id').val(id);
        jQuery('.en_res_city_input').show('slow');
        jQuery('.en_res_select_city').val('').hide();
        jQuery('#en_res_edit_form_id').val(id);
        jQuery("#en_residential_address_table:has(td)").click(function (e) {
            var currentRow = jQuery(e.target).closest("tr");
            var col1 = currentRow.find("td").eq(0).text();
            var col2 = currentRow.find("td").eq(1).text();
            var col3 = currentRow.find("td").eq(2).text();
            var col4 = currentRow.find("td").eq(3).text();
            var col5 = currentRow.find("td").eq(4).text().toUpperCase();
            var col6 = currentRow.find("td").eq(5).text();
            var col7 = currentRow.find("td").eq(6).text().toUpperCase();
            var col8 = currentRow.find("td").eq(7).text();

            jQuery('#en_res_nickname').val(col1);
            jQuery('#en_res_address_addr').val(col2);
            jQuery('#en_res_address_addr_2').val(col3);
            jQuery('#en_res_address_city').val(col4);
            jQuery('#en_res_address_state').val(col5);
            jQuery('#en_res_address_zip').val(col6);
            jQuery('#en_res_country_code').val(col7);
            jQuery('input[name=en_res_address_type][value=' + col8 + ']').prop('checked', true);
        });
        en_woo_addons_popup_rad_address_show();
        setTimeout(function () {
            if (jQuery('.en_res_add_address_popup').is(':visible')) {
                jQuery('.en_res_address_form_input > input').eq(0).focus();
            }
        }, 100);
    }
}

/**
 * Delete current Address Popup
 * @param e
 * @returns {Boolean}
 */
if (typeof en_rad_delete_current_address != 'function') {
    function en_rad_delete_current_address(e) {
        en_woo_addons_popup_rad_address_delete_show();
        var en_res_del_id = (e && e != 0) ? e : 0;
        jQuery('.en_rad_cancel_delete').on('click', function () {
            en_woo_addons_popup_rad_address_delete_hide();
        });
        jQuery('.en_res_confirm_delete').off('click').on('click', function () {
            en_woo_addons_popup_rad_address_delete_hide();
            return en_rad_delete_address(en_res_del_id);

        });
        return false;
    }
}

/**
 * close rad popup.
 */
if (typeof en_rad_close_popup != 'function') {
    function en_rad_close_popup() {
        en_woo_addons_popup_rad_address_hide();
    }
}


/**
 * Delete Address Custom Post and Meta
 * @param en_res_id
 */
if (typeof en_rad_delete_address != 'function') {
    function en_rad_delete_address(en_res_id) {
        if (en_res_id && en_res_id != 0) {
            var address_hidden = "address_delete";
            let del_id = en_res_id | 0;
            var data = {
                action: 'en_rad_delete_address',
                form_del_id: del_id,
                address_rad_del: address_hidden
            }

            /** AJAX call for address */
            var ajax_res_del_response = jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                dataType: 'json',
                async: false,
                success: function (response) {
                    ajax_res_del_response = response;

                    if (ajax_res_del_response) {
                        jQuery('#en_res_edit_form_id').val('');
                        jQuery('#en_residential_addresses_form').find("input[type='text']").val("");
                        jQuery(".en_res_address_err").html("");

                        if (ajax_res_del_response && ajax_res_del_response.rad_address) {
                            /** Delete row from table */
                            jQuery('#row_' + en_res_id).remove();
                            jQuery(".en_success_alert").attr('style', 'display:block;').html('<p><strong>Success!</strong> ' + ajax_res_del_response.message + '</p>');
                        } else {
                            jQuery(".en_error_alert").attr('style', 'display:block;').html('<p><strong>Error!</strong> ' + ajax_res_del_response.message + '</p>');
                        }

                        /** Hide Alert message box */
                        clearTimeout(en_rad_address_notice_timeout);
                        en_timeout_address_alert_notice();
                    }
                },
                error: function (err) {
                    console.log('error AJAX deleting post...', err);
                    jQuery(".en_error_alert").attr('style', 'display:block;').html("<p><strong>Error!</strong> Address not deleted!</p>");
                    /** Hide Alert message box */
                    clearTimeout(en_rad_address_notice_timeout);
                    en_timeout_address_alert_notice();
                }
            });
        }
        return false;
    }
}

/** Hide alert notice */
if (typeof en_timeout_address_alert_notice != 'function') {
    function en_timeout_address_alert_notice() {
        /** Hide Alert message box */
        en_rad_address_notice_timeout = setTimeout(function () {
            jQuery(".en_success_alert, .en_error_alert").attr('style', 'display:none;').html("");
            jQuery(".en_success_alert, .en_error_alert, .dynamic_res_address_error").hide();
        }, 5000);
    }
}


/**
 * Set city form value
 * @param e
 * */
if (typeof en_rad_set_city != 'function') {
    function en_rad_set_city(e) {
        var city = jQuery(e).val();
        jQuery('#en_res_address_city').val(city);
    }
}

/**
 * Change en_rad_alphaonly input classes value
 * */
jQuery(function () {
    jQuery('input.en_rad_alphaonly').keyup(function () {
        var location_field_id = jQuery(this).attr("id");
        var location_regex = location_field_id == 'en_res_address_city' ? /[^a-zA-Z-]/g : /[^a-zA-Z]/g;
        if (this.value.match(location_regex)) {
            this.value = this.value.replace(location_regex, '');
        }
    });

    /** Change number inputs values */
    jQuery('input.numberonly').keyup(function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

});

/**
 * End of Script file 
 */