<?php
/**
 * Unishippers JS
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_footer', 'unishippers_ltl_ajax_carrrier_button');

/**
 * JS Function
 */
function unishippers_ltl_ajax_carrrier_button()
{
    ?>
    <script>

        // Update plan
        if (typeof en_update_plan != 'function') {
            function en_update_plan(input) {
                let action = jQuery(input).attr('data-action');
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {action: action},
                    success: function (data_response) {
                        window.location.reload(true);
                    }
                });
            }
        }

        jQuery(document).ready(function () {

            jQuery("#order_shipping_line_items .shipping .view .display_meta").css('display', 'none');

            jQuery("#wc_settings_unishipper_residential_delivery ").closest('tr').addClass("wc_settings_unishipper_residential_delivery ");
            jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");
            jQuery("#avaibility_lift_gate").closest('tr').addClass("avaibility_lift_gate");
            jQuery("#wc_settings_unishippers_freight_lift_gate_delivery").closest('tr').addClass("wc_settings_unishippers_freight_lift_gate_delivery");
            jQuery("#unishippers_freight_liftgate_delivery_as_option").closest('tr').addClass("unishippers_freight_liftgate_delivery_as_option");
            jQuery("#residential_delivery_options_label").closest('tr').addClass("residential_delivery_options_label");
            jQuery("#liftgate_delivery_options_label").closest('tr').addClass("liftgate_delivery_options_label");

            /**
             * Offer lift gate delivery as an option and Always include residential delivery fee
             * @returns {undefined}
             */

            jQuery(".checkbox_fr_add").on("click", function () {
                var id = jQuery(this).attr("id");
                if (id == "wc_settings_unishippers_freight_lift_gate_delivery") {
                    jQuery("#unishippers_freight_liftgate_delivery_as_option").prop({checked: false});
                    jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({checked: false});

                } else if (id == "unishippers_freight_liftgate_delivery_as_option" ||
                    id == "en_woo_addons_liftgate_with_auto_residential") {
                    jQuery("#wc_settings_unishippers_freight_lift_gate_delivery").prop({checked: false});
                }
            });

            var url = getUrlVarsunishipperUnishippers()["tab"];
            if (url === 'unishippers_freight') {
                jQuery('#footer-left').attr('id', 'wc-footer-left');
            }

            /*
             * Restrict Handling Fee with 8 digits limit
             */

            jQuery("#wc_settings_unishippers_freight_hand_free_mark_up").attr('maxlength', '8');

        });

        jQuery(".unishippers_ltl_connection_section_class .button-primary").click(function () {
            var input = validateInput('.unishippers_ltl_connection_section_class');
            if (input === false) {
                return false;
            }
        });
        jQuery(".unishippers_ltl_connection_section_class .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary unishippers_ltl_test_connection">Test Connection</a>');
        jQuery('.unishippers_ltl_test_connection').click(function (e) {
            var input = validateInput('.unishippers_ltl_connection_section_class');

            if (input === false) {
                return false;
            }

            var postForm = {
                'unishippers_account_number': jQuery('#wc_settings_unishippers_freight_account_number').val(),
                'unishippers_username': jQuery('#wc_settings_unishippers_freight_username').val(),
                'unishippers_password': jQuery('#wc_settings_unishippers_freight_password').val(),
                'unishippers_licence_key': jQuery('#wc_settings_unishippers_freight_licence_key').val(),
                'api_token': jQuery('#wc_settings_unishippers_freight_api_token').val(),
                'unishippers_account_id': jQuery('#unishippers_account_id').val(),
                'action': 'unishippers_ltl_validate_keys'
            };
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                data: postForm,
                dataType: 'json',
                beforeSend: function () {
                    jQuery(".unishippers_ltl_test_connection").css("color", "#fff");
                    jQuery(".unishippers_ltl_connection_section_class .button-primary").css("cursor", "pointer");
                    jQuery('#wc_settings_unishippers_freight_account_number').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                    jQuery('#wc_settings_unishippers_freight_username').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                    jQuery('#wc_settings_unishippers_freight_password').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                    jQuery('#wc_settings_unishippers_freight_licence_key').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                    jQuery('#wc_settings_unishippers_freight_api_token').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                    jQuery('#unishippers_account_id').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER . 'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
                },
                success: function (data) {

                    //                    if (data.success) {
                    if (typeof data.severity != 'undefined' && data.severity == 'SUCCESS') {
                        jQuery(".updated").hide();
                        jQuery('#wc_settings_unishippers_freight_account_number').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_username').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_password').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_licence_key').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_api_token').css('background', '#fff');
                        jQuery('#unishippers_account_id').css('background', '#fff');
                        jQuery(".test_connection_success_message").remove();
                        jQuery(".test_connection_error_message").remove();
                        jQuery(".unishippers_ltl_connection_section_class .button-primary").attr("disabled", false);
                        jQuery('.warning-msg-unishippers_ltl').before('<p class="test_connection_success_message" ><b> Success! The test resulted in a successful connection. </b></p>');

                        jQuery('html, body').animate({
                            'scrollTop': jQuery('.test_connection_success_message').position().top
                        });

                    } else {
                        jQuery(".updated").hide();
                        jQuery(".test_connection_error_message").remove();
                        jQuery('#wc_settings_unishippers_freight_account_number').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_username').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_password').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_licence_key').css('background', '#fff');
                        jQuery('#wc_settings_unishippers_freight_api_token').css('background', '#fff');
                        jQuery('#unishippers_account_id').css('background', '#fff');
                        jQuery(".test_connection_success_message").remove();
                        jQuery(".unishippers_ltl_connection_section_class .button-primary").attr("disabled", false);
                        if (data.error) {
                            jQuery('.warning-msg-unishippers_ltl').before('<p class="test_connection_error_message" ><b>Error! ' + data.error + ' </b></p>');
                        } else {
                            jQuery('.warning-msg-unishippers_ltl').before('<p class="test_connection_error_message" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>');
                        }

                        jQuery('html, body').animate({
                            'scrollTop': jQuery('.test_connection_error_message').position().top
                        });
                    }
                }
            });
            e.preventDefault();
        })

        /**
         * Read a page's GET URL variables and return them as an associative array.
         */
        function getUrlVarsunishipperUnishippers() {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for (var i = 0; i < hashes.length; i++) {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }

        function validateInput(form_id) {
            var has_err = true;
            jQuery(form_id + " input[type='text']").each(function () {
                var input = jQuery(this).val();
                var response = validateString(input);

                var errorElement = jQuery(this).parent().find('.err');
                jQuery(errorElement).html('');
                var errorText = jQuery(this).attr('title');
                var optional = jQuery(this).data('optional');
                optional = (optional === undefined) ? 0 : 1;
                errorText = (errorText != undefined) ? errorText : '';
                if ((optional == 0) && (response == false || response == 'empty')) {
                    errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';
                    jQuery(errorElement).html(errorText);
                }
                has_err = (response != true && optional == 0) ? false : has_err;
            });
            return has_err;
        }

        function validateString(string) {
            if (string == '') {
                return 'empty';
            } else {
                return true;
            }
        }
    </script>
    <?php
}

add_action('admin_footer', 'unishippers_ltl_no_carrier_select');

/**
 * No CArrier Select JS
 */
function unishippers_ltl_no_carrier_select()
{
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery('.unishippers_ltl_connection_section_class .form-table').before('<div class="warning-msg-unishippers_ltl"><p> <b>Note!</b> You must have a Unishippers Freight account to use this application. If you do not have one, click <a href="https://www.unishippers.com/request-new-account" target="_blank">here</a> to access the new account request form. </p>');

            jQuery('.unishippers_carrier_section_class .button-primary').on('click', function () {
                jQuery(".updated").hide();
                var num_of_checkboxes = jQuery('.carrier_check:checked').size();
                if (num_of_checkboxes < 1) {
                    jQuery(".unishippers_carrier_section_class:first-child").before('<div id="message" class="error inline no_srvc_select"><p><strong>Please select at least one carrier service.</strong></p></div>');

                    jQuery('html, body').animate({
                        'scrollTop': jQuery('.no_srvc_select').position().top
                    });

                    return false;
                }
            });


            jQuery('.quote_section_class_unishippers_ltl .button-primary').on('click', function () {
                jQuery(".updated").hide();
                jQuery('.error').remove();
                var handling_fee = jQuery('#wc_settings_unishippers_freight_hand_free_mark_up').val();
                if (handling_fee.slice(handling_fee.length - 1) == '%') {
                    handling_fee = handling_fee.slice(0, handling_fee.length - 1)
                }
                if (handling_fee === "") {
                    return true;
                } else {
                    if (isValidNumber(handling_fee) === false) {

                        jQuery("#mainform .quote_section_class_unishippers_ltl").prepend('<div id="message" class="error inline handlng_fee_error"><p><strong>Handling fee format should be 100.20 or 10%.</strong></p></div>');
                        jQuery('html, body').animate({
                            'scrollTop': jQuery('.handlng_fee_error').position().top
                        });
                        return false;
                    } else if (isValidNumber(handling_fee) === 'decimal_point_err') {
                        jQuery("#mainform .quote_section_class_unishippers_ltl").prepend('<div id="message" class="error inline handlng_fee_error"><p><strong>Handling fee format should be 100.2000 or 10% and only 4 digits are allowed after decimal</strong></p></div>');
                        jQuery('html, body').animate({
                            'scrollTop': jQuery('.handlng_fee_error').position().top
                        });
                        return false;
                    } else {
                        return true;
                    }
                }
            });
        });
    </script>
    <?php
}

add_action('admin_footer', 'unishippers_ltl_check_all');

/**
 * Check all JS
 */
function unishippers_ltl_check_all()
{
    ?>
    <script>
        var all_checkboxes = jQuery('.carrier_check');
        if (all_checkboxes.length === all_checkboxes.filter(":checked").length) {
            jQuery('.include_all').prop('checked', true);
        }

        jQuery(".include_all").change(function () {
            if (this.checked) {
                jQuery(".carrier_check").each(function () {
                    this.checked = true;
                })
            } else {
                jQuery(".carrier_check").each(function () {
                    this.checked = false;
                })
            }
        });

        /*
         * Uncheck Select All Checkbox
         */

        jQuery(".carrier_check").on('change load', function () {
            var int_checkboxes = jQuery('.carrier_check:checked').size();
            var int_un_checkboxes = jQuery('.carrier_check').size();
            if (int_checkboxes === int_un_checkboxes) {
                jQuery('.include_all').attr('checked', true);
            } else {
                jQuery('.include_all').attr('checked', false);
            }
        });

    </script>
    <?php
}

add_action('admin_footer', 'unishippers_ltl_admin_quote_setting_input');

/**
 * Quote settings JS
 */
function unishippers_ltl_admin_quote_setting_input()
{
    ?>
    <input type="hidden" id="show_unishipper_saved_method"
           value="<?php echo get_option('wc_settings_unishippers_freight_rate_method'); ?>"/>
    <script>
        jQuery(window).load(function () {
            var saved_mehod_value = jQuery('#show_unishipper_saved_method').val();
            if (saved_mehod_value == 'Cheapest') {
                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays only the cheapest returned Rate.');
                jQuery('.unishipper_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.');
            }
            if (saved_mehod_value == 'cheapest_options') {

                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
                jQuery('.unishipper_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
            }
            if (saved_mehod_value == 'average_rate') {

                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
                jQuery('.unishipper_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
                jQuery('.unishipper_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".');

            }

        });

        //        changed
        var wc_settings_unishippers_freight_rate_method = jQuery("#wc_settings_unishippers_freight_rate_method").val();
        if (wc_settings_unishippers_freight_rate_method == 'Cheapest') {
            jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
            jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').css("display", "none");
        }

        jQuery("#wc_settings_unishippers_freight_rate_method").change(function () {
            var rating_method = jQuery(this).val();
            if (rating_method == 'Cheapest') {

                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays only the cheapest returned Rate.');
                jQuery('.unishipper_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.');

            }
            if (rating_method == 'cheapest_options') {

                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays a list of a specified number of least expensive options.');
                jQuery('.unishipper_Number_of_options_class td span').html('Number of options to display in the shopping cart.');
            }
            if (rating_method == 'average_rate') {

                jQuery(".unishipper_delivery_estimate").removeAttr('style');
                jQuery(".unishipper_Number_of_label_as").removeAttr('style');
                jQuery(".unishipper_Number_of_options_class").removeAttr('style');

                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').addClass("unishipper_delivery_estimate");
                jQuery("#wc_settings_unishippers_freight_delivery_estimate").closest('tr').css("display", "none");
                jQuery("#wc_settings_unishippers_freight_label_as").closest('tr').addClass("unishipper_Number_of_label_as");
                jQuery("#wc_settings_unishippers_freight_Number_of_options").closest('tr').addClass("unishipper_Number_of_options_class");
                jQuery("#wc_settings_unishippers_freight_rate_method").closest('tr').addClass("unishipper_rate_mehod");

                jQuery('.unishipper_rate_mehod td span').html('Displays a single rate based on an average of a specified number of least expensive options.');
                jQuery('.unishipper_Number_of_options_class td span').html('Number of options to include in the calculation of the average.');
                jQuery('.unishipper_Number_of_label_as td span').html('What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".');
            }
        });

        jQuery(document).ready(function () {

            //          JS for edit product nested fields
            jQuery("._nestedMaterials").closest('p').addClass("_nestedMaterials_tr");
            jQuery("._nestedPercentage").closest('p').addClass("_nestedPercentage_tr");
            jQuery("._maxNestedItems").closest('p').addClass("_maxNestedItems_tr");
            jQuery("._nestedDimension").closest('p').addClass("_nestedDimension_tr");
            jQuery("._nestedStakingProperty").closest('p').addClass("_nestedStakingProperty_tr");

            if (!jQuery('._nestedMaterials').is(":checked")) {
                jQuery('._nestedPercentage_tr').hide();
                jQuery('._nestedDimension_tr').hide();
                jQuery('._maxNestedItems_tr').hide();
                jQuery('._nestedDimension_tr').hide();
                jQuery('._nestedStakingProperty_tr').hide();
            } else {
                jQuery('._nestedPercentage_tr').show();
                jQuery('._nestedDimension_tr').show();
                jQuery('._maxNestedItems_tr').show();
                jQuery('._nestedDimension_tr').show();
                jQuery('._nestedStakingProperty_tr').show();
            }

            jQuery("input[name=_nestedPercentage]").attr('min', '0');
            jQuery("input[name=_maxNestedItems]").attr('min', '0');
            jQuery("input[name=_nestedPercentage]").attr('max', '100');
            jQuery("input[name=_maxNestedItems]").attr('max', '100');
            jQuery("input[name=_nestedPercentage]").attr('maxlength', '3');
            jQuery("input[name=_maxNestedItems]").attr('maxlength', '3');

            if (jQuery("input[name=_nestedPercentage]").val() == '') {
                jQuery("input[name=_nestedPercentage]").val(0);
            }

            jQuery("._nestedPercentage").keydown(function (eve) {
                unishippers_lfq_stop_special_characters(eve);
                var nestedPercentage = jQuery('._nestedPercentage').val();
                if (nestedPercentage.length == 2) {
                    var newValue = nestedPercentage + '' + eve.key;
                    if (newValue > 100) {
                        return false;
                    }
                }
            });

            jQuery("._nestedDimension").keydown(function (eve) {
                unishippers_lfq_stop_special_characters(eve);
                var nestedDimension = jQuery('._nestedDimension').val();
                if (nestedDimension.length == 2) {
                    var newValue1 = nestedDimension + '' + eve.key;
                    if (newValue1 > 100) {
                        return false;
                    }
                }
            });

            jQuery("._maxNestedItems").keydown(function (eve) {
                unishippers_lfq_stop_special_characters(eve);
            });

            jQuery("input[name=_nestedMaterials]").change(function () {
                if (!jQuery('._nestedMaterials').is(":checked")) {
                    jQuery('._nestedPercentage_tr').hide();
                    jQuery('._nestedDimension_tr').hide();
                    jQuery('._maxNestedItems_tr').hide();
                    jQuery('._nestedDimension_tr').hide();
                    jQuery('._nestedStakingProperty_tr').hide();
                } else {
                    jQuery('._nestedPercentage_tr').show();
                    jQuery('._nestedDimension_tr').show();
                    jQuery('._maxNestedItems_tr').show();
                    jQuery('._nestedDimension_tr').show();
                    jQuery('._nestedStakingProperty_tr').show();
                }
            });

            jQuery('.unishippers_ltl_connection_section_class input[type="text"]').each(function () {
                if (jQuery(this).parent().find('.err').length < 1) {
                    jQuery(this).after('<span class="err"></span>');
                }
            });

            jQuery('#wc_settings_unishippers_freight_account_number').attr('title', 'Account Number');
            jQuery('#wc_settings_unishippers_freight_username').attr('title', 'Username');
            jQuery('#wc_settings_unishippers_freight_password').attr('title', 'Password');
            jQuery('#wc_settings_unishippers_freight_licence_key').attr('title', 'Plugin License Key');
            jQuery('#wc_settings_unishippers_freight_api_token').attr('title', 'API Token');
            jQuery('#unishippers_account_id').attr('title', 'ID');
            jQuery('#wc_settings_unishippers_freight_text_for_own_arrangment').attr('title', 'Text For Own Arrangement');
            jQuery('#wc_settings_unishippers_freight_hand_free_mark_up').attr('title', 'Handling Fee / Markup');
            jQuery('#wc_settings_unishippers_freight_label_as').attr('title', 'Label As');
        })


        function isValidNumber(value, noNegative) {
            if (typeof (noNegative) === 'undefined')
                noNegative = false;
            var isValidNumber = false;
            var validNumbunishippers_freight_submiter = (noNegative == true) ? parseFloat(value) >= 0 : true;
            if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
                if (value.indexOf(".") >= 0) {
                    var n = value.split(".");
                    if (n[n.length - 1].length <= 4) {
                        isValidNumber = true;
                    } else {
                        isValidNumber = 'decimal_point_err';
                    }
                } else {
                    isValidNumber = true;
                }
            }
            return isValidNumber;
        }

        function unishippers_lfq_stop_special_characters(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if (jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
                // Allow: Ctrl+A, Command+A
                (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                // Allow: home, end, left, right, down, up
                (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                e.preventDefault();
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186 && e.keyCode != 8) {
                e.preventDefault();
            }
            if (e.keyCode == 186 || e.keyCode == 190 || e.keyCode == 189 || (e.keyCode > 64 && e.keyCode < 91)) {
                e.preventDefault();
                return;
            }
        }
    </script>
    <?php
}
