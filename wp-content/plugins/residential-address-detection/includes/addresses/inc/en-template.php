<?php
add_action('woocommerce_settings_wc_settings_quote_section_end_residential_addresses_after', 'en_woo_addons_end_residential_addresses_table');

function en_woo_addons_end_residential_addresses_table() {
    ?>

    <!-- Close WordPress Default Form -->
    </form>
    </div>
    <div class="en_residential_addresses_template">
        <!-- Show Success Alert Wordpress -->
        <div class="en_success_alert"></div>

        <!-- Show Error Alert Wordpress -->
        <div class="en_error_alert"></div>

        <!-- Residential Table Heading Title -->
        <h2>Address Type Overrides</h2>

        <!-- Address text -->
        <div>
            <p style="float: left;">Add addresses to the table below to override the address type identified in the USPS database.</p>
            <p class="en_res_add_new_address_btn" onclick="en_res_add_address_btn()">Add new address</p>
        </div>
        <!-- Center the table -->
        <center>
            <!-- Addresses Table -->
            <table id="en_residential_address_table" class="en_residential_address_table" border="1px" style="border-collapse:collapse;">
                <tr>
                    <th class="en_res_heading">Nickname</th>
                    <th class="en_res_heading">Street Address</th>
                    <th class="en_res_heading">Suite / Apt / Bldg</th>
                    <th class="en_res_heading">City</th>
                    <th class="en_res_heading">State</th>
                    <th class="en_res_heading">Postal Code</th>
                    <th class="en_res_heading">Country</th>
                    <th class="en_res_heading">Type</th>
                    <th class="en_res_heading">Action</th>
                </tr>

                <?php
                /* ============ Get data from custom POST Type of en_rad_addresses ============  */

                $args = [
                    'post_type' => "en_rad_addresses",
                    'posts_per_page' => -1,
                ];
                $query = new WP_Query($args);
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $res_addr_postId = get_the_ID();
                        $get_post_meta = get_post_meta($res_addr_postId, 'en_rad_addresses', true);
                        $res_addr_content = $get_post_meta;

                        $obj = array();
                        ?>

                        <!-- Row Addresses loop of custom post -->

                        <tr id="row_<?php echo $res_addr_postId; ?>">

                            <?php
                            if (is_array($res_addr_content) || is_object($res_addr_content)) {
                                foreach ($res_addr_content as $key => $value) {
                                    $obj["$key"] = $value;
                                    if ($key == "state" || $key == "country") {
                                        ?>
                                        <td class="en_res_data txt-upper"><?php echo $value; ?></td>
                                        <?php
                                    } else if ($key == "en_address_type") {
                                        ?>
                                        <td class="en_res_data txt-capital"><?php echo $value; ?></td>
                                        <?php
                                    } else {
                                        ?>
                                        <td class="en_res_data"><?php echo $value; ?></td>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                }
                            }
                            ?>

                            <td class="en_res_data">
                                <a class="en_res_icon_link" onclick="return en_rad_edit_address(<?php echo ( isset($res_addr_postId) ) ? $res_addr_postId : ''; ?>);"><img class="edit-icon" src="<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/edit.png" title="Edit"></a>
                                <a class="en_res_icon_link" onclick="return en_rad_delete_current_address(<?php echo ( isset($res_addr_postId) ) ? $res_addr_postId : ''; ?>);"><img src="<?php echo plugins_url(); ?>/residential-address-detection/includes/addresses/imgs/delete.png" title="Delete"></a>
                            </td>
                        </tr>

                        <?php
                    }
                } else {
                    ?>
                    <!-- No row found -->
                    <tr id="en_res_address_no_record">
                        <td colspan="8" class="en_res_data">
                            No Record Found
                        </td>
                    </tr>

                    <?php
                }

                /* Restore original Post Data */
                wp_reset_postdata();
                ?>

            </table>
        </center>


        <!-- Popup Modal to add or edit address -->
        <div id="en_res_add_address_btn" class="en_res_address_overlay">
            <div class="en_res_add_address_popup">
                <h2 class="address_heading">Address</h2>
                <a class="close" onclick="en_rad_close_popup()" href="javascript:;" >×</a>
                <div class="en_res_popup_content">
                    <div class="en_res_already_exist">
                        <strong>Error!</strong> Zip code already exists.
                    </div>
                    <div class="en_res_not_allowed">
                        <p><strong>Error!</strong> Please enter US zip code.</p>
                    </div>
                    <div class="en_res_zero_results">
                        <p><strong>Error!</strong> Please enter valid US zip code.</p>
                    </div>
                    <div class="en_res_wrng_credential">
                        <p><strong>Error!</strong> Please verify credentials at connection settings panel.</p>
                    </div>
                    <div class="dynamic_res_address_error">
                        <p></p>
                    </div>

                    <!-- Add Residential Address Form -->
                    <form method="post" id="en_residential_addresses_form" name="en_residential_addresses_form">
                        <input type="hidden" name="en_res_edit_form_id" value="" id="en_res_edit_form_id">
                        <div class="en_res_address_form_input address_input">
                            <label for="en_res_nickname">Nickname</label>
                            <input type="text" title="Nickname" value="" required name="en_res_nickname" placeholder="Nickname" id="en_res_nickname">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input address_input">
                            <label for="en_res_address_addr">Street Address</label>
                            <input type="text" title="Street Address" value="" required name="en_res_address_addr" placeholder="Street Address" id="en_res_address_addr">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input address2_input">
                            <label for="en_res_address_addr_2">Suite / Apt / Bldg</label>
                            <input type="text" title="Suite / Apt / Bldg" data-optional="1" value="" required name="en_res_address_addr_2" placeholder="Suite / Apartment / Building (optional)" id="en_res_address_addr_2">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input">
                            <label for="en_res_address_zip">Postal Code</label>
                            <input class="numberonly" type="text" title="Postal Code" onchange="en_rad_address_zip_change()" required maxlength="7" value="" name="en_res_address_zip" placeholder="30214" id="en_res_address_zip">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input en_res_city_input">
                            <label for="en_res_address_city">City</label>
                            <input type="text" class="en_rad_alphaonly" title="City" value="" required minlength="2" name="en_res_address_city" placeholder="Fayetteville" id="en_res_address_city">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input en_res_select_city" style="display: none;">
                            <label for="en_res_address_city">City</label>
                            <select id="actname"></select>
                            <span class="en_res_address_err"></span>
                        </div>

                        <div class="en_res_address_form_input">
                            <label for="en_res_address_state">State</label>
                            <input type="text" class="en_rad_alphaonly txt-upper" required maxlength="2" title="State" value="" name="en_res_address_state" placeholder="GA" id="en_res_address_state">
                            <span class="en_res_address_err"></span>
                        </div>
                        <div class="en_res_address_form_input">
                            <label for="en_res_country_code">Country</label>
                            <input type="text" class="en_rad_alphaonly txt-upper" required maxlength="2" title="Country" name="en_res_country_code" value="" placeholder="US" id="en_res_country_code">
                            <input type="hidden" name="en_res_address_hidden" value="address" id="en_res_address_hidden">
                            <span class="en_res_address_err"></span>
                        </div>

                        <div class="en_res_address_form_input">
                            <div class="en-label-div-radio">
                                <label for="en_res_address_type">Type</label> 
                            </div>
                            <div class="en-label-div-input-radio">
                                <span><input type="radio" id="residential" name="en_res_address_type" checked value="residential" title="Residential"> <label class="en_res_address_radio_label" for="residential">Residential</label>
                                    <input type="radio" id="commercial" name="en_res_address_type" value="commercial" title="Commercial"> <label class="en_res_address_radio_label" for="commercial">Commercial</label>
                                </span>
                                <span class="en_res_address_err"></span>
                            </div>
                        </div>

                        <div style="clear: both;"></div>
                        <br>

                        <div class="en-res-form-btns">
                            <input type="button" id="en_res_submit_address" name="en_res_submit_address" value="Save" class="button-primary save_res_address_form" onclick="en_res_address_cf_submit(); return false;">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Popup confirmation Modal to delete Address -->
        <div class="en_res_setting_section">
            <a href="#delete_en_address_btn" class="delete_en_address_btn hide_drop_val"></a>
            <div id="delete_en_address_btn" class="en_res_delete_address_overlay">
                <div class="en_res_add_address_popup">
                    <h2 class="del_hdng">
                        Warning!
                    </h2>
                    <p class="delete_p">
                        Are you sure you want to delete the address?
                    </p>
                    <div class="del_btns">
                        <a href="#" class="en_rad_cancel_delete">Cancel</a>
                        <a href="#" class="button-primary en_res_confirm_delete">OK</a>
                    </div>
                </div>
            </div>
        </div>

    <?php } ?>