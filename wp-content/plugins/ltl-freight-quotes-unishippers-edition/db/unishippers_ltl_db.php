<?php

/**
 * Unishippers Database
 *
 * @package      Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create Warehouse Table
 * @global $wpdb
 */
if (!function_exists('create_unishippers_ltl_wh_db')) {

    function create_unishippers_ltl_wh_db()
    {
        global $wpdb;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $en_table = "wp_unishippers_freight_carriers";
        if ($wpdb->query("SHOW TABLES LIKE '" . $en_table . "'") === 0) {
            $carriers = "CREATE TABLE `wp_unishippers_freight_carriers` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
            `unishippers_shipmentQuoteId` varchar(600) NOT NULL,
            `unishippers_carrierSCAC` varchar(600) NOT NULL,
            `unishippers_carrierName` varchar(600) NOT NULL,
            `unishippers_transitDays` varchar(600) NOT NULL,
            `unishippers_guaranteedService` varchar(600) NOT NULL,
            `unishippers_highCostDeliveryShipment` varchar(600) NOT NULL,
            `unishippers_interline` varchar(600) NOT NULL,
            `unishippers_nmfcRequired` varchar(600) NOT NULL,
            `unishippers_carrierNotifications` varchar(600) NOT NULL,
            `carrier_logo` varchar(255) NOT NULL,
            `carrier_status` varchar(8) NOT NULL,
            PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

            dbDelta($carriers);
        }

        $warehouse_table = $wpdb->prefix . "warehouse";
        if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
            $origin = 'CREATE TABLE ' . $warehouse_table . '(
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    city varchar(200) NOT NULL,
                    state varchar(200) NOT NULL,
                    zip varchar(200) NOT NULL,
                    country varchar(200) NOT NULL,
                    location varchar(200) NOT NULL,
                    nickname varchar(200) NOT NULL,
                    enable_store_pickup VARCHAR(255) NOT NULL,
                    miles_store_pickup VARCHAR(255) NOT NULL ,
                    match_postal_store_pickup VARCHAR(255) NOT NULL ,
                    checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
                    enable_local_delivery VARCHAR(255) NOT NULL ,
                    miles_local_delivery VARCHAR(255) NOT NULL ,
                    match_postal_local_delivery VARCHAR(255) NOT NULL ,
                    checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
                    fee_local_delivery VARCHAR(255) NOT NULL ,
                    suppress_local_delivery VARCHAR(255) NOT NULL,
                    PRIMARY KEY  (id) )';
            dbDelta($origin);
        }

        $myCustomer = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
        if (!(isset($myCustomer->Field) && $myCustomer->Field == 'enable_store_pickup')) {

            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));
        }
    }

}

/**
 * Install Carriers On Activation
 */
if (!function_exists('unishippers_ltl_freihgt_installation_carrier')) {

    function unishippers_ltl_freihgt_installation_carrier()
    {
        $carriers_obj = new unishippers_freight_carriers();
        $create_class_obj = new unishippers_freight_carriers();
        $carriers_obj->carriers();
        if (!function_exists('create_unishippers_ltl_class')) {
            $create_class_obj->create_unishippers_ltl_class();
        }
    }

}

/**
 * Truncate All Carriers On Deactivation
 */
if (!function_exists('unishippers_ltl_truncat_carrier_table')) {

    function unishippers_ltl_truncat_carrier_table()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;
        $wpdb->query('TRUNCATE TABLE wp_unishippers_freight_carriers');
    }

}