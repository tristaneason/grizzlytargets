<?php
/**
 * script for ajax load
 */
add_action('admin_footer', 'en_rad_address_scripting_table');

/**
 * JS
 */
function en_rad_address_scripting_table() {
    ?>

    <script>
        function en_rad_address_zip_change() {
            var regex_zip = /^[a-zA-Z0-9]{1,6}$/;
            var zip_code = jQuery("#en_res_address_zip").val();
            if (zip_code && !regex_zip.test(zip_code)) {
                jQuery('.dynamic_res_address_error').show('slow');
                jQuery('.dynamic_res_address_error p').html('<strong>Error!</strong> Invalid Zipcode.');
                jQuery("#en_residential_addresses_form").css({'border-color': '#e81123'});
                jQuery('.en_res_popup_content').delay(200).animate({scrollTop: 0}, 300);
                setTimeout(function () {
                    jQuery('.dynamic_res_address_error').hide('slow');
                }, 5000);
                return false;
            }

            jQuery('#en_res_address_city').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/processing.gif") no-repeat scroll 50% 50%');
            jQuery('#en_res_address_state').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/processing.gif") no-repeat scroll 50% 50%');
            jQuery('.en_res_select_city_css').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/processing.gif") no-repeat scroll 50% 50%');
            jQuery('#en_res_country_code').css('background', 'rgba(255, 255, 255, 1) url("<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/processing.gif") no-repeat scroll 50% 50%');

            var postForm = {
                'action': 'en_rad_get_address',
                'res_addr_zip': jQuery('#en_res_address_zip').val(),
            };

            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                success: function (data) {
                    if (data) {
                        if (data.country === 'US' || data.country === 'CA') {
                            if (data.postcode_localities == 1) {
                                jQuery('.en_res_select_city').show();
                                jQuery('#actname').replaceWith(data.city_option);
                                jQuery('.en_res_addr_multi_state').replaceWith(data.city_option);
                                jQuery('.city-multiselect').change(function () {
                                    en_rad_set_city(this);
                                });
                                jQuery('#en_res_address_city').val(data.first_city);
                                jQuery('#en_res_address_state').val(data.state);
                                jQuery('#en_res_country_code').val(data.country);
                                jQuery('#en_res_address_city').css('background', 'none');
                                jQuery('#en_res_address_state').css('background', 'none');
                                jQuery('.en_res_select_city_css').css('background', 'none');
                                jQuery('#en_res_country_code').css('background', 'none');
                                jQuery('.en_res_city_input').hide();
                            } else {
                                jQuery('.en_res_city_input').show();
                                jQuery('#en_res_city').removeAttr('value');
                                jQuery('.en_res_select_city').hide();
                                jQuery('#en_res_address_city').val(data.city);
                                jQuery('#en_res_address_state').val(data.state);
                                jQuery('#en_res_country_code').val(data.country);
                                jQuery('#en_res_address_city').css('background', 'none');
                                jQuery('#en_res_address_state').css('background', 'none');
                                jQuery('#en_res_country_code').css('background', 'none');
                            }
                        } else if (data.result === 'ZERO_RESULTS') {
                            jQuery('.en_res_zero_results').show('slow');
                            jQuery('#en_res_address_city').css('background', 'none');
                            jQuery('#en_res_address_state').css('background', 'none');
                            jQuery('#en_res_country_code').css('background', 'none');
                            setTimeout(function () {
                                jQuery('.en_res_zero_results').hide('slow');
                            }, 5000);
                        } else if (data.result === 'false') {
                            jQuery('.en_res_zero_results').show('slow').delay(5000).hide('slow');
                            jQuery('#en_res_address_city').css('background', 'none');
                            jQuery('#en_res_address_state').css('background', 'none');
                            jQuery('#en_res_country_code').css('background', 'none');
                            jQuery('#en_res_address_city').val('');
                            jQuery('#en_res_address_state').val('');
                            jQuery('#en_res_country_code').val('');
                        } else if (data.apiResp === 'apiErr') {
                            jQuery('.en_res_wrng_credential').show('slow');
                            jQuery('#en_res_address_city').css('background', 'none');
                            jQuery('#en_res_address_state').css('background', 'none');
                            jQuery('#en_res_country_code').css('background', 'none');
                            setTimeout(function () {
                                jQuery('.en_res_wrng_credential').hide('slow');
                            }, 5000);
                        } else {
                            jQuery('.en_res_not_allowed').show('slow');
                            jQuery('#en_res_address_city').css('background', 'none');
                            jQuery('#en_res_address_state').css('background', 'none');
                            jQuery('#en_res_country_code').css('background', 'none');
                            setTimeout(function () {
                                jQuery('.en_res_not_allowed').hide('slow');
                            }, 5000);
                        }
                    }
                },
            });
            return false;
        }
    </script>

    <?php
}
