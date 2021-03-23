<?php
/**
 * Eniture Dropship Template
 */
if (!defined('ABSPATH')) {
    exit;
}


?>
<script type="text/javascript">
    jQuery(document).ready(function () {
                
        jQuery("#en_wd_dropship_zip").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });
    });
    
    function hide_drop_val()
    {
        jQuery('#add_dropships')[0].reset();
            
        jQuery('#edit_dropship_form_id').val('');
        jQuery("#en_wd_dropship_zip").val('');
        jQuery('.city_select').hide();
        jQuery('.city_input').show();
        jQuery('#en_wd_dropship_city').css('background', 'none');
        jQuery("#en_wd_dropship_nickname").val('');
        jQuery("#en_wd_dropship_city").val('');
        jQuery('.en_wd_multi_state').empty();
        jQuery("#en_wd_dropship_state").val('');
        jQuery("#en_wd_dropship_country").val('');
        jQuery('.en_wd_zip_validation_err').hide();
        jQuery('.en_wd_city_validation_err').hide();
        jQuery('.en_wd_state_validation_err').hide();
        jQuery('.en_wd_country_validation_err').hide();
        jQuery('.not_allowed').hide();
        jQuery('.zero_results').hide();
        jQuery('.already_exist').hide();
        jQuery('.wrng_credential').hide();
        jQuery('#add_dropships').find("input[type='text']").val("");
        jQuery(".en_wd_err").html("");
        jQuery('#add_dropships').find("input[type='checkbox']").prop('checked',false);
        jQuery('#instore-pickup-zipmatch .tag-i, #local-delivery-zipmatch .tag-i').trigger('click');

        setTimeout(function () {
        if (jQuery('.ds_popup').is(':visible')) {
                jQuery('.ds_input > input').eq(0).focus();
            }
        }, 100);
    }
    function change_dropship_zip()
    {
        if (jQuery("#en_wd_dropship_zip").val() == '') {
            return false;
        }

        jQuery('#en_wd_dropship_city').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER.'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
        jQuery('#en_wd_dropship_state').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER.'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
        jQuery('.city_select_css').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER.'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');
        jQuery('#en_wd_dropship_country').css('background', 'rgba(255, 255, 255, 1) url("<?php echo EN_UNISHIPPER_LOADER.'warehouse-dropship/wild/assets/images/processing.gif'; ?>") no-repeat scroll 50% 50%');

        var postForm = {
            'action': 'en_wd_get_address',
            'origin_zip': jQuery('#en_wd_dropship_zip').val(),
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function ()
            {
                jQuery('.en_wd_zip_validation_err').hide();
                jQuery('.en_wd_city_validation_err').hide();
                jQuery('.en_wd_state_validation_err').hide();
                jQuery('.en_wd_country_validation_err').hide();
            },
            success: function (data)
            {
                if (data) {
                    if (data.country === 'US' || data.country === 'CA') {
                        if (data.postcode_localities == 1) {
                            jQuery('.city_select').show();
                            jQuery('#dropship_actname').replaceWith(data.city_option);
                            jQuery('.en_wd_multi_state').replaceWith(data.city_option);
                            jQuery('#en_wd_dropship_state').val(data.state);
                            jQuery('#en_wd_dropship_country').val(data.country);
                            jQuery('.city-multiselect').change(function () {
                                setDSCity(this);
                            });
                            jQuery('#en_wd_dropship_city').val(data.first_city);
                            jQuery('#en_wd_dropship_state').css('background', 'none');
                            jQuery('.city_select_css').css('background', 'none');
                            jQuery('#en_wd_dropship_country').css('background', 'none');
                            jQuery('.city_input').hide();
                        } else {
                            jQuery('.city_input').show();
                            jQuery('#_city').removeAttr('value');
                            jQuery('.city_select').hide();
                            jQuery('#en_wd_dropship_city').val(data.city);
                            jQuery('#en_wd_dropship_state').val(data.state);
                            jQuery('#en_wd_dropship_country').val(data.country);
                            jQuery('#en_wd_dropship_city').css('background', 'none');
                            jQuery('#en_wd_dropship_state').css('background', 'none');
                            jQuery('#en_wd_dropship_country').css('background', 'none');
                        }
                    } else if (data.result === 'ZERO_RESULTS') {
                        jQuery('.zero_results').show('slow');
                        jQuery('#en_wd_dropship_city').css('background', 'none');
                        jQuery('#en_wd_dropship_state').css('background', 'none');
                        jQuery('#en_wd_dropship_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.zero_results').hide('slow');
                        }, 5000);
                    } else if (data.result === 'false') {
                        jQuery('.zero_results').show('slow').delay(5000).hide('slow');
                        jQuery('#en_wd_dropship_city').css('background', 'none');
                        jQuery('#en_wd_dropship_state').css('background', 'none');
                        jQuery('#en_wd_dropship_country').css('background', 'none');
                        jQuery('#en_wd_dropship_city').val('');
                        jQuery('#en_wd_dropship_state').val('');
                        jQuery('#en_wd_dropship_country').val('');
                    } else if (data.apiResp === 'apiErr') {
                        jQuery('.wrng_credential').show('slow');
                        jQuery('#en_wd_dropship_city').css('background', 'none');
                        jQuery('#en_wd_dropship_state').css('background', 'none');
                        jQuery('#en_wd_dropship_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.wrng_credential').hide('slow');
                        }, 5000);
                    } else {
                        jQuery('.not_allowed').show('slow');
                        jQuery('#en_wd_dropship_city').css('background', 'none');
                        jQuery('#en_wd_dropship_state').css('background', 'none');
                        jQuery('#en_wd_dropship_country').css('background', 'none');
                        setTimeout(function () {
                            jQuery('.not_allowed').hide('slow');
                        }, 5000);
                    }
                }
            },
        });
        return false;
    }
    function setDSCity($this) {
        var city = jQuery($this).val();
        jQuery('#en_wd_dropship_city').val(city);
    }

    jQuery(function () {
        jQuery('input.alphaonly').keyup(function () {
            var location_field_id = jQuery(this).attr("id");
            var location_regex = location_field_id == 'en_wd_origin_city' || location_field_id == 'en_wd_dropship_city' ? /[^a-zA-Z-]/g : /[^a-zA-Z]/g;
            if (this.value.match(location_regex)) {
                this.value = this.value.replace(location_regex, '');
            }
        });
    });
</script>

<div class="en_wd_setting_section">
    <a href="#delete_dropship_btn" class="delete_dropship_btn hide_drop_val"></a>
    <div id="delete_dropship_btn" class="en_wd_warehouse_overlay">
        <div class="en_wd_add_warehouse_popup">
            <h2 class="del_hdng">
                Warning!
            </h2>
            <p class="delete_p">
                Warning! If you delete this location, Drop ship location settings will be disable against products if any.
            </p>
            <div class="del_btns">
                <a href="#" class="cancel_delete">Cancel</a>
                <a href="#" class="confirm_delete">OK</a>
            </div>
        </div>
    </div>

    <h1>Drop ships</h1><br>
    
    <?php dropship_template(); ?>

    <!-- Add Popup for new dropship -->
    <div id="add_dropship_btn" class="en_wd_warehouse_overlay">
        <div class="en_wd_add_warehouse_popup ds_popup">
            <h2 class="dropship_heading">Drop ship</h2>
            <a class="close" href="#">&times;</a>
            <div class="content">
                <div class="already_exist">
                    <strong>Error!</strong> Zip code already exists.
                </div>
                <div class="not_allowed">
                    <p><strong>Error!</strong> Please enter US zip code.</p>
                </div>
                <div class="zero_results">
                    <p><strong>Error!</strong> Please enter valid US zip code.</p>
                </div>
                <div class="wrng_credential">
                    <p><strong>Error!</strong> Please verify credentials at connection settings panel.</p>
                </div>
                <div class="wrng_local wrng_standard_pckg">
                    <p><strong>Error!</strong> Local delivery is enabled you must enter miles or postal codes.</p>
                </div>
                <div class="wrng_instore wrng_standard_pckg">
                    <p><strong>Error!</strong> in-store pick up is enabled you must enter miles or postal codes.</p>
                </div>
                
                <!-- Wordpress Form closed --> 
                </form>
                
                <form method="post" id="add_dropships">
                    <input type="hidden" name="edit_dropship_form_id" value="" id="edit_dropship_form_id">
                    <div class="en_wd_add_warehouse_input ds_input">
                        <label for="en_wd_dropship_nickname">Nickname</label>
                        <input type="text" title="Nickname" value="" data-optional="1" name="en_wd_dropship_nickname" placeholder="Nickname" id="en_wd_dropship_nickname">
                        <span class="en_wd_err"></span>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label for="en_wd_origin_zip">Zip </label>
                        <input title="Zip" type="text" maxlength="7" value="" onchange="change_dropship_zip()" name="en_wd_dropship_zip" placeholder="30214" id="en_wd_dropship_zip">
                        <span class="en_wd_err"></span>
                    </div>
                    <div class="en_wd_add_warehouse_input city_input">
                        <label for="en_wd_origin_city">City</label>
                        <input type="text" class="alphaonly" title="City" value="" name="en_wd_dropship_city" placeholder="Fayetteville" id="en_wd_dropship_city">
                        <span class="en_wd_err"></span>
                    </div>
                    <div class="en_wd_add_warehouse_input city_select">
                        <label for="en_wd_origin_city">City</label>
                        <select id="dropship_actname"></select>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label for="en_wd_origin_state">State</label>
                        <input type="text" class="alphaonly" maxlength="2" title="State" value="" name="en_wd_dropship_state" placeholder="GA" id="en_wd_dropship_state">
                        <span class="en_wd_err"></span>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label for="en_wd_origin_country">Country</label>
                        <input type="text" class="alphaonly" maxlength="2" title="Country" name="en_wd_dropship_country" value="" placeholder="US" id="en_wd_dropship_country">
                        <span class="en_wd_err"></span>
                        <input type="hidden" name="en_wd_dropship_location" value="dropship" id="en_wd_dropship_location">
                    </div>
                    
                    <div style="clear: both;"></div>
                    <br>
                    
                    <?php 
                        $disabled = "";
                        $package_required = "";
                        $plugin_tab = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : "";
                        $action_instore = apply_filters($plugin_tab."_quotes_plans_suscription_and_features" , 'instore_pickup_local_devlivery');
                        if(is_array($action_instore))
                        {
                            $disabled = "disabled_me";  
                            $package_required = apply_filters($plugin_tab . "_plans_notification_link" , $action_instore);
                        }
                    ?>

                <!--
                    Instore Pick Up Starts
                -->
                    <div class="heading">
                        <h2 class="warehouse_heading heading_left">In-store pick up</h2>
                        <a href="#">
                            <h2 class="warehouse_heading pakage_notify_instore_dropship heading_right"><?php echo $package_required; ?></h2>
                        </a>
                    </div>
                
                    <div class="en_wd_add_warehouse_input">
                        <label>Enable in-store pick up</label>
                        <div class="pickup-delivery-checkboxes">
                            <input type="checkbox" title="Enable in-store pick up" id="enable-instore-pickup" data-optional="1" name="enable-instore-pickup" value="" class="enable-instore-pickup <?php echo esc_attr($disabled); ?>" />
                        </div>
                    </div>
                    <div class="en_wd_add_warehouse_input ">
                        <label>Offer if address is within (miles):</label>
                        <input type="text" title="Offer if address is within (miles):" data-optional="1" onchange="validate_delivery_fee(this);" id="instore-pickup-address" class="<?php echo esc_attr($disabled); ?>" name="instore-pickup-address">
                    </div>
                    <div class="en_wd_add_warehouse_tagging">
                        <label>Offer if postal code matches:</label>
                        <div data-tags-input-name="tag" title="Offer if postal code matches:" data-optional="1" id="instore-pickup-zipmatch" value="" name="instore-pickup-zipmatch" class="tagging-js <?php echo esc_attr($disabled); ?>"></div>
                    </div>

                    <div class="en_wd_add_warehouse_input">
                        <label>Checkout description:</label>
                        <input type="text" class="<?php echo esc_attr($disabled); ?>" title="Checkout description:" id="instore-pickup-desc" placeholder="In-store pick up" data-optional="1" name="instore-pickup-desc">
                    </div>


                    <div style="clear: both;"></div>
                    <br>

                <!--
                    Local Delivery Starts
                -->
                
                    <div class="heading">
                        <h2 class="local-delivery-heading heading_left">Local Delivery</h2>
                        <a href="#">
                            <h2 class="local-delivery-heading pakage_notify_local_dropship heading_right"><?php echo $package_required; ?></h2>
                        </a>
                    </div>
                
                    <div class="en_wd_add_warehouse_input">
                        <label>Enable local delivery</label>
                        <div class="pickup-delivery-checkboxes">
                            <input type="checkbox" title="Enable local delivery" id="enable-local-delivery" data-optional="1" name="enable-local-delivery" value="" class="enable-local-delivery <?php echo esc_attr($disabled); ?>" />
                        </div>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label>Offer if address is within (miles):</label>
                        <input type="text" title="Offer if address is within (miles):" class="<?php echo esc_attr($disabled); ?>" data-optional="1" onchange="validate_delivery_fee(this);" id="local-delivery-address" name="local-delivery-address">
                    </div>
                    <div class="en_wd_add_warehouse_tagging">  
                        <label>Offer if postal code matches:</label>
                        <div data-tags-input-name="tag" title="Offer if postal code matches:" data-optional="1" id="local-delivery-zipmatch" value="" name="local-delivery-zipmatch" class="tagging-js <?php echo esc_attr($disabled); ?>"></div>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label>Checkout description:</label>
                        <input id="local-delivery-desc" title="Checkout description:" placeholder="Local delivery" type="text" class="<?php echo esc_attr($disabled); ?>" data-optional="1" name="local-delivery-desc">
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label>Local delivery fee</label>
                        <input type="text" class="<?php echo esc_attr($disabled); ?>" title="Local delivery fee" data-optional="1" onchange="validate_delivery_fee(this);" id="local-delivery-fee" name="local-delivery-fee">
                        <span class="en_wd_err"></span>
                    </div>
                    <div class="en_wd_add_warehouse_input">
                        <label>Suppress other rates <span class="suppress-span" title="This setting only suppresses rates that would otherwise be returned by the Eniture Technology products.">[?]</span></label>
                        <div class="pickup-delivery-checkboxes <?php echo esc_attr($disabled); ?>">
                            <input type="checkbox" title="Suppress other rates" id="suppress-local-delivery" name="suppress-local-delivery" value="" class="suppress-local-delivery" />
                        </div>
                    </div>

                <!--
                    Local Delivery Ends
                -->
                	<div class="form-btns">
                    <input type="submit" name="en_wd_submit_dropship" value="Save" class="save_warehouse_form" onclick="return en_wd_save_dropship();">
                    </div>
                </form>
            </div>
        </div>
    </div>