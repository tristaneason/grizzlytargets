<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once 'wild/includes/wild-delivery-save.php';


/**
 * Admin Warehouse Dropship Scripts
 */
if (!function_exists("en_woo_wd_admin_script_style")) {

    function en_woo_wd_admin_script_style() {
        wp_enqueue_script('en_woo_wd_tagging', plugin_dir_url(__FILE__) . '/wild/assets/js/tagging.js', array(), '1.0.0');
        wp_localize_script('en_woo_wd_tagging', 'script', array(
            'pluginsUrl' => plugins_url(),
        ));

        wp_enqueue_script('en_woo_wd_script', plugin_dir_url(__FILE__) . '/wild/assets/js/warehouse_section.js', array(), '1.0.3');
        wp_localize_script('en_woo_wd_script', 'script', array(
            'pluginsUrl' => plugins_url(),
        ));

        wp_register_style('warehouse_section', plugin_dir_url(__FILE__) . '/wild/assets/css/warehouse_section.css', false, '1.0.3');
        wp_enqueue_style('warehouse_section');
    }

    add_action('admin_enqueue_scripts', 'en_woo_wd_admin_script_style');
}

/**
 * Warehouse Template
 */
if (!function_exists('warehouse_template')) {

    function warehouse_template($action = FALSE) {

        ob_start();

        global $wpdb;
        $warehous_list = $wpdb->get_results(
                "SELECT *
                  FROM " . $wpdb->prefix . "warehouse WHERE location = 'warehouse'"
        );

        $multi_warehouse_disabled = "";
        $multi_warehouse_package_required = "";
        $tr_disabled_me = "";
        $add_space = "";

        $plugin_tab = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : "";
        $multi_warehouse = apply_filters($plugin_tab . "_quotes_plans_suscription_and_features", 'multi_warehouse');

        if (is_array($multi_warehouse) && count($warehous_list) > 0) {
            $add_space = "<br><br>";
            $multi_warehouse_disabled = "wild_disabled_me";
            $tr_disabled_me = "tr_disabled_me";
            $multi_warehouse_package_required = apply_filters($plugin_tab . "_plans_notification_link", $multi_warehouse);
        }
        ?>


        <div class="add_btn_warehouse">

            <a href="#en_wd_add_warehouse_btn" onclick="en_wd_add_warehouse_btn()" title="Add Warehouse" class="en_wd_add_warehouse_btn <?php echo $multi_warehouse_disabled; ?>" name="avc">Add</a>

            <div class="wild_warehouse pakage_notify heading_right">
                <?php echo $multi_warehouse_package_required; ?>
            </div>

            <br><?php echo $add_space; ?>

            <div class="warehouse_text">
                <p>Warehouses that inventory all products not otherwise identified as drop shipped items. The warehouse with the lowest shipping cost to the destination is used for quoting purposes.</p>
            </div>
            <div id="message" class="updated inline warehouse_deleted">
                <p><strong>Success! Warehouse deleted successfully.</strong></p>
            </div>
            <div id="message" class="updated inline warehouse_created">
                <p><strong>Success! New warehouse added successfully.</strong></p>
            </div>
            <div id="message" class="updated inline warehouse_updated">
                <p><strong>Success! Warehouse updated successfully.</strong></p>
            </div>
            <table class="en_wd_warehouse_list" id="append_warehouse">
                <thead>
                    <tr>
                        <th class="en_wd_warehouse_list_heading">
                            City
                        </th>
                        <th class="en_wd_warehouse_list_heading">
                            State
                        </th>
                        <th class="en_wd_warehouse_list_heading">
                            Zip
                        </th>
                        <th class="en_wd_warehouse_list_heading">
                            Country
                        </th>
                        <th class="en_wd_warehouse_list_heading">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($warehous_list) > 0) {
                        $count = 0;
                        foreach ($warehous_list as $list) {
                            ?>
                            <tr class="<?php echo (strlen($tr_disabled_me) > 0 && $count != 0) ? $tr_disabled_me : ""; ?>" id="row_<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>" data-id="<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>">
                                <td class="en_wd_warehouse_list_data">
                                    <?php echo ( isset($list->city) ) ? esc_attr($list->city) : ''; ?>
                                </td>
                                <td class="en_wd_warehouse_list_data">
                                    <?php echo ( isset($list->state) ) ? esc_attr($list->state) : ''; ?>
                                </td>
                                <td class="en_wd_warehouse_list_data">
                                    <?php echo ( isset($list->zip) ) ? esc_attr($list->zip) : ''; ?>
                                </td>
                                <td class="en_wd_warehouse_list_data">
                                    <?php echo ( isset($list->country) ) ? esc_attr($list->country) : ''; ?>
                                </td>
                                <td class="en_wd_warehouse_list_data">
                                    <a href="javascript(0)" onclick="return en_wd_edit_warehouse(<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>);"><img src="<?php echo plugins_url(); ?>/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/edit.png" title="Edit"></a>
                                    <a href="javascript(0)" onclick="return en_wd_delete_current_warehouse(<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>);"><img src="<?php echo plugins_url(); ?>/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/delete.png" title="Delete"></a>
                                </td>
                            </tr>
                            <?php
                            $count++;
                        }
                    } else {
                        ?>
                        <tr class="new_warehouse_add" data-id=0></tr>
                    <?php } ?>
                </tbody>
            </table>


            <?php
            echo '</div>';

            if ($action) {
                $ob_get_clean = ob_get_clean();
                return $ob_get_clean;
            }
        }

    }

    /**
     * Dropship Template
     */
    if (!function_exists('dropship_template')) {

        function dropship_template($action = FALSE) {

            ob_start();

            global $wpdb;
            $dropship_list = $wpdb->get_results(
                    "SELECT * FROM " . $wpdb->prefix . "warehouse WHERE location = 'dropship'"
            );

            $multi_dropship_disabled = "";
            $multi_dropship_package_required = "";
            $tr_disabled_me = "";
            $add_space = "";

            $plugin_tab = (isset($_REQUEST['tab'])) ? sanitize_text_field($_REQUEST['tab']) : "";
            $multi_dropship = apply_filters($plugin_tab . "_quotes_plans_suscription_and_features", 'multi_dropship');

            if (is_array($multi_dropship) && count($dropship_list) > 0) {
                $add_space = "<br><br>";
                $multi_dropship_disabled = "wild_disabled_me";
                $tr_disabled_me = "tr_disabled_me";
                $multi_dropship_package_required = apply_filters($plugin_tab . "_plans_notification_link", $multi_dropship);
            }
            ?>

            <div class="add_btn_dropship">
                <a href="#add_dropship_btn" onclick="hide_drop_val()" title="Add Drop Ship" class="en_wd_add_dropship_btn hide_drop_val <?php echo $multi_dropship_disabled; ?>">Add</a>

                <div class="wild_warehouse pakage_notify heading_right">
                    <?php echo $multi_dropship_package_required; ?>
                </div>

                <br><?php echo $add_space; ?>
                <div class="warehouse_text">
                    <p>Locations that inventory specific items that are drop shipped to the destination. Use the product's settings page to identify it as a drop shipped item and its associated drop ship location. Orders that include drop shipped items will display a single figure for the shipping rate estimate that is equal to the sum of the cheapest option of each shipment required to fulfill the order.</p>
                </div>
                <div id="message" class="updated inline dropship_created">
                    <p><strong>Success! New drop ship added successfully.</strong></p>
                </div>
                <div id="message" class="updated inline dropship_updated">
                    <p><strong>Success! Drop ship updated successfully.</strong></p>
                </div>
                <div id="message" class="updated inline dropship_deleted">
                    <p><strong>Success! Drop ship deleted successfully.</strong></p>
                </div>
                <table class="en_wd_dropship_list" id="append_dropship">
                    <thead>
                        <tr>
                            <th class="en_wd_dropship_list_heading">
                                Nickname
                            </th>
                            <th class="en_wd_dropship_list_heading">
                                City
                            </th>
                            <th class="en_wd_dropship_list_heading">
                                State
                            </th>
                            <th class="en_wd_dropship_list_heading">
                                Zip
                            </th>
                            <th class="en_wd_dropship_list_heading">
                                Country
                            </th>
                            <th class="en_wd_dropship_list_heading">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($dropship_list) > 0) {
                            $count = 0;
                            foreach ($dropship_list as $list) {
                                ?>
                                <tr class="<?php echo (strlen($tr_disabled_me) > 0 && $count != 0) ? $tr_disabled_me : ""; ?>" id="row_<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>">
                                    <td class="en_wd_dropship_list_data">
                                        <?php echo ( isset($list->nickname) ) ? esc_attr($list->nickname) : ''; ?>
                                    </td>
                                    <td class="en_wd_dropship_list_data">
                                        <?php echo ( isset($list->city) ) ? esc_attr($list->city) : ''; ?>
                                    </td>
                                    <td class="en_wd_dropship_list_data">
                                        <?php echo ( isset($list->state) ) ? esc_attr($list->state) : ''; ?>
                                    </td>
                                    <td class="en_wd_dropship_list_data">
                                        <?php echo ( isset($list->zip) ) ? esc_attr($list->zip) : ''; ?>
                                    </td>
                                    <td class="en_wd_dropship_list_data">
                                        <?php echo ( isset($list->country) ) ? esc_attr($list->country) : ''; ?>
                                    </td>
                                    <td class="en_wd_dropship_list_data">
                                        <a href="javascript(0)" onclick="return en_wd_edit_dropship(<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>);"><img src="<?php echo plugins_url(); ?>/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/edit.png" title="Edit"></a>
                                        <a href="javascript(0)" onclick="return en_wd_delete_current_dropship(<?php echo ( isset($list->id) ) ? esc_attr($list->id) : ''; ?>);"><img src="<?php echo plugins_url(); ?>/ltl-freight-quotes-unishippers-edition/warehouse-dropship/wild/assets/images/delete.png" title="Delete"></a>
                                    </td>
                                </tr>
                                <?php
                                $count++;
                            }
                        } else {
                            ?>
                            <tr class="new_dropship_add" data-id=0></tr>
                        <?php } ?>
                    </tbody>
                </table>

                <?php
                echo '</div>';

                if ($action) {
                    return ob_get_clean();
                }
            }

        }