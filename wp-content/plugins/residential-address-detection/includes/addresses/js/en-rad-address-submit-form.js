/**
 * Timeout variable to reset on new instance
 */
var en_rad_address_notice_timeout = '';


/**
 * Address Form Submit 
 */
if (typeof en_res_address_cf_submit != 'function') {
    function en_res_address_cf_submit() {

        var validate = en_rad_validate_input("#en_residential_addresses_form");

        switch (true) {
            case (validate == false):
                jQuery('.en_res_popup_content').delay(200).animate({scrollTop: 0}, 300);
                return false;
        }


        var serialized_address_form = jQuery("#en_residential_addresses_form").serialize();
        var address_hidden = jQuery("#en_res_address_hidden").val();
        /* Check form serialized is empty or not */
        if (serialized_address_form != '') {

            var data = {
                action: 'en_rad_save_address',
                formData: serialized_address_form,
                address_rad: address_hidden
            }

            /* AJAX call for address */
            var en_res_ajax_response = jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                dataType: 'json',
                async: false,
                success: function (response) {
                    en_res_ajax_response = response;

                    if (en_res_ajax_response) {

                        /* Remove no record default Row */
                        jQuery("tr#en_res_address_no_record").remove();

                        if (en_res_ajax_response.rad_address) {
                            jQuery(".en_success_alert").html('<p><strong>Success!</strong> ' + en_res_ajax_response.message + '</p>');
                            jQuery(".en_success_alert").show('slow');
                            jQuery('.en_success_alert').delay(200).animate({scrollTop: 0}, 300);
                            jQuery("#en_residential_addresses_form").trigger("reset");
                            jQuery('#en_residential_addresses_form')[0].reset();
                            jQuery('#en_res_edit_form_id').val('');
                            jQuery('#en_res_address_city').css('background', 'none');
                            jQuery("#en_res_address_city").val('');
                            jQuery('#en_res_nickname').css('background', 'none');
                            jQuery("#en_res_nickname").val('');
                            jQuery('.en_res_city_input').show('slow');
                            jQuery('.en_res_select_city').val('').hide();
                            jQuery("#en_res_address_state").val('');
                            jQuery("#en_res_address_zip").val('');
                            jQuery("#en_res_country_code").val('');
                            jQuery('#en_residential_addresses_form').find("input[type='text']").val("");
                            jQuery(".en_res_address_err").html("");
                            en_woo_addons_popup_rad_address_hide();

                        } else {
                            /** Show error message on form popup */
                            jQuery(".dynamic_res_address_error").html('<p><strong>Error!</strong> ' + en_res_ajax_response.message + '</p>').show('slow');
                            jQuery('.en_res_popup_content').delay(200).animate({scrollTop: 0}, 300);
                        }

                        /* Hide Alert message box */
                        clearTimeout(en_rad_address_notice_timeout);
                        en_timeout_address_alert_notice();

                        let en_post_meta = en_res_ajax_response.en_res_post_meta;
                        if (en_res_ajax_response.is_updated_en_res && en_res_ajax_response.is_updated_en_res == true && en_res_ajax_response.en_res_post_meta) {
                            /* Record Updated, so update table row */
                            en_res_update_row(en_res_ajax_response, en_post_meta);
                            en_woo_addons_popup_rad_address_hide();

                        } else if (!en_res_ajax_response.is_existing_en_res || en_res_ajax_response.is_existing_en_res == false && en_res_ajax_response.en_res_post_meta) {
                            /* Insert new Address row with column values */
                            let en_res_new_row = `<tr id="row_` + en_res_ajax_response.en_res_id + `">
                        <td class="en_res_data"></td>
                        </tr>`;

                            /* Append recent inserted post and meta data */
                            jQuery("#en_residential_address_table").append(en_res_new_row);
                            /* Update columns of created row */
                            en_res_update_row(en_res_ajax_response, en_post_meta);
                            en_woo_addons_popup_rad_address_hide();
                        }

                    }
                },
                error: function (err) {
                    console.log('error AJAX...', err);

                    jQuery(".dynamic_res_address_error").html('<p><strong>Error!</strong> Unable to add the address.</p>').show('slow');

                    /* Hide Alert message box */
                    clearTimeout(en_rad_address_notice_timeout);
                    en_timeout_address_alert_notice();

                }
            });

        } else {
            jQuery(".dynamic_res_address_error").html('<p><strong>Error!</strong> Please fill form!</p>').show('slow');

            /* Hide Alert message box */
            clearTimeout(en_rad_address_notice_timeout);
            en_timeout_address_alert_notice();
        }
        return false;
    }
}


/**
 * Update Table row record fields
 * @param en_res_ajax_response
 * @param en_post_meta
 */
if (typeof en_res_update_row != 'function') {
    function en_res_update_row(en_res_ajax_response, en_post_meta) {
        if (en_res_ajax_response && en_post_meta) {
            let en_res_row_to_update = jQuery('table#en_residential_address_table').find('tr#row_' + en_res_ajax_response.en_res_id);
            /* Update columns of existing row */
            let en_updated_table_row = `<td class="en_res_data">` + en_post_meta.nickname + `</td>
                <td class="en_res_data">` + en_post_meta.address + `</td>
                <td class="en_res_data">` + en_post_meta.address_2 + `</td>
                <td class="en_res_data">` + en_post_meta.city + `</td>
                <td class="en_res_data txt-upper">` + en_post_meta.state + `</td>
                <td class="en_res_data">` + en_post_meta.zip + `</td>
                <td class="en_res_data txt-upper">` + en_post_meta.country + `</td>
                <td class="en_res_data txt-capital">` + en_post_meta.en_address_type + `</td> 
                <td class="en_res_data">
                    <a class="en_res_icon_link" href="#en_res_add_address_btn" onclick="en_rad_edit_address(` + en_res_ajax_response.en_res_id + `)">
                        <img class="edit-icon" src="` + en_res_ajax_response.en_plugin_url + `/residential-address-detection/includes/addresses/imgs/edit.png" title="Edit">
                    </a>
                    <a class="en_res_icon_link" href="#delete_en_address_btn" onclick="en_rad_delete_current_address(` + en_res_ajax_response.en_res_id + `)">
                        <img src="` + en_res_ajax_response.en_plugin_url + `/residential-address-detection/includes/addresses/imgs/delete.png" title = "Delete"> 
                    </a>
                </td>`;

            /* update entries of updated row. */
            en_res_row_to_update.html(en_updated_table_row);
        }
    }
}