<div class="tool-box bg-white p-20p">
    <?php $order_statuses = wc_get_order_statuses(); ?>
    <h3 class="title"><?php _e('Export Settings:', 'order-import-export-for-woocommerce'); ?></h3>
    <p><?php _e('Export and download your orders in CSV format. This file can be used to import orders back into your Woocommerce shop.', 'order-import-export-for-woocommerce'); ?></p>
    <form action="<?php echo admin_url('admin.php?page=wf_woocommerce_order_im_ex&action=export'); ?>" method="post">
        <table class="form-table">
            <tr>
                <th>
                    <label for="ord_offset"><?php _e('Offset', 'order-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <input type="text" name="offset" id="ord_offset" placeholder="0" class="input-text" />
                    <p style="font-size: 12px"><?php _e('Number of orders to skip before exporting. If the value is 0 no orders are skipped. If value is 100, orders from order id 101 will be exported.', 'order-import-export-for-woocommerce'); ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="ord_limit"><?php _e('Limit', 'order-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <input type="text" name="limit" id="ord_limit" placeholder="<?php _e('Unlimited', 'order-import-export-for-woocommerce'); ?>" class="input-text" />
                    <p style="font-size: 12px"><?php _e('Number of orders to export. If no value is given all orders will be exported. This is useful if you have large number of orders and want to export partial list of orders.', 'order-import-export-for-woocommerce'); ?></p>
                </td>
            </tr>
            
            
                <tr>
                <th>
                    <label for="v_order_status"><?php _e('Order Statuses', 'order-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <select id="v_order_status" name="order_status[]" data-placeholder="<?php _e('All Orders', 'order-import-export-for-woocommerce'); ?>" class="wc-enhanced-select" multiple="multiple">
                        <?php
                        foreach ($order_statuses as $key => $column) {
                            echo '<option value="' . $key . '">' . $column . '</option>';
                        }
                        ?>
                    </select>
                    <p style="font-size: 12px"><?php _e('Orders with these status will be exported.', 'order-import-export-for-woocommerce'); ?></p>
                </td>
            </tr>
              <tr>
                <th>
                    <label for="v_start_date"><?php _e('Start Date', 'order-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <input type="text" name="start_date"  id="v_start_date" />
                    <p>Format: <code>YYYY-MM-DD.</code></p>         
                </td>
            </tr>
            <tr>
                <th>
                    <label for="v_end_date"><?php _e('End Date', 'order-import-export-for-woocommerce'); ?></label>
                </th>
                <td>
                    <input type="text" name="end_date"  id="v_end_date" />
                    <p>Format: <code>YYYY-MM-DD.</code></p>
                </td>
            </tr>
        </table>
        <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Export Orders', 'order-import-export-for-woocommerce'); ?>" /></p>
    </form>
</div>
</div>
        <?php include(WT_OrdImpExpCsv_BASE . 'includes/views/market.php'); ?>
        <div class="clearfix"></div>
</div>

<script>
jQuery(document).ready(function(a) {

    jQuery( "#v_start_date" ).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,//this option for allowing user to select month
        changeYear: true, //this option for allowing user to select from year range
    });
    jQuery( "#v_end_date" ).datepicker({
        dateFormat: "yy-mm-dd",
        changeMonth: true,//this option for allowing user to select month
        changeYear: true, //this option for allowing user to select from year range
    });
    

});
</script>