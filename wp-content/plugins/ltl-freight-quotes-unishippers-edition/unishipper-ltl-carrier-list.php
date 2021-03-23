<?php

/**
 * Unishippers Carriers
 *
 * @package     Unishippers Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class unishippers_freight_carriers
 */
if (!class_exists('unishippers_freight_carriers')) {

    class unishippers_freight_carriers
    {

        /**
         * Carriers
         * @global $wpdb
         */
        function carriers()
        {

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            global $wpdb;
            $table_name = 'wp_unishippers_freight_carriers';
            $installed_carriers = $wpdb->get_results("SELECT COUNT(*) AS carriers FROM " . $table_name);
            if ($installed_carriers[0]->carriers < 1) {
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'AACT',
                    'unishippers_carrierName' => 'AAA Cooper',
                    'carrier_logo' => 'aact.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'CENF',
                    'unishippers_carrierName' => 'Central Freight Lines',
                    'carrier_logo' => 'cenf.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'CLNI',
                    'unishippers_carrierName' => 'Clear Lane Freight Systems',
                    'carrier_logo' => 'clni.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'CNWY',
                    'unishippers_carrierName' => 'XPO Logistics',
                    'carrier_logo' => 'cnwy.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'CTII',
                    'unishippers_carrierName' => 'Central Transport Intl',
                    'carrier_logo' => 'ctii.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'DAFG',
                    'unishippers_carrierName' => 'Dayton Freight',
                    'carrier_logo' => 'dafg.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'DHRN',
                    'unishippers_carrierName' => 'Dohrn',
                    'carrier_logo' => 'dhrn.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'DYLT',
                    'unishippers_carrierName' => 'Daylight Transportation',
                    'carrier_logo' => 'dylt.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'EXLA',
                    'unishippers_carrierName' => 'Estes Express Lines',
                    'carrier_logo' => 'exla.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'HMES',
                    'unishippers_carrierName' => 'USF Holland',
                    'carrier_logo' => 'hmes.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'MIDW',
                    'unishippers_carrierName' => 'Midwest Motor',
                    'carrier_logo' => 'midw.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'OAKH',
                    'unishippers_carrierName' => 'Oak Harbor Freight',
                    'carrier_logo' => 'oakh.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'ODFL',
                    'unishippers_carrierName' => 'Old Dominion',
                    'carrier_logo' => 'odfl.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'RDFS',
                    'unishippers_carrierName' => 'Roadrunner Freight',
                    'carrier_logo' => 'rdfs.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'RDWY',
                    'unishippers_carrierName' => 'YRC Worldwide',
                    'carrier_logo' => 'rdwy.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'RETL',
                    'unishippers_carrierName' => 'USF Reddaway',
                    'carrier_logo' => 'retl.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'RLCA',
                    'unishippers_carrierName' => 'R L Carriers',
                    'carrier_logo' => 'rlca.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'SAIA',
                    'unishippers_carrierName' => 'SAIA',
                    'carrier_logo' => 'saia.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'SEFL',
                    'unishippers_carrierName' => 'Southeastern Freight',
                    'carrier_logo' => 'sefl.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'UPGF',
                    'unishippers_carrierName' => 'UPS Freight',
                    'carrier_logo' => 'upgf.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'WARD',
                    'unishippers_carrierName' => 'Ward Trucking',
                    'carrier_logo' => 'ward.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'UPSF',
                    'unishippers_carrierName' => 'UPS SUPPLY CHAIN SOLUTIONS',
                    'carrier_logo' => 'ups-supply-chain.jpg'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'PAAF',
                    'unishippers_carrierName' => 'PILOT FREIGHT SERVICES',
                    'carrier_logo' => 'Pilot.jpg'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'LMEL',
                    'unishippers_carrierName' => 'LME',
                    'carrier_logo' => 'lme.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'UPPN',
                    'unishippers_carrierName' => 'US Special Delivery Inc',
                    'carrier_logo' => 'us-special.jpeg'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'FCSY',
                    'unishippers_carrierName' => 'FrontLine Freight',
                    'carrier_logo' => 'frontLine-freight.png'
                ));
                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'FWDA',
                    'unishippers_carrierName' => 'ForwardAir',
                    'carrier_logo' => 'forward-air.png'
                ));

                // New carriers added ref ticket #213132148

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'NPME',
                    'unishippers_carrierName' => 'New Penn Motor Express Inc',
                    'carrier_logo' => 'newpennmotorexpress.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'RXIC',
                    'unishippers_carrierName' => 'Ross Express',
                    'carrier_logo' => 'rossexpress.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'SMTL',
                    'unishippers_carrierName' => 'Southwestern Motor Transport',
                    'carrier_logo' => 'southwesternmotor.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'BEAV',
                    'unishippers_carrierName' => 'BEAVER EXPRESS SERVICE LLC',
                    'carrier_logo' => 'beaverexpress.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'DPHE',
                    'unishippers_carrierName' => 'Dependable Highway Express',
                    'carrier_logo' => 'dependhighwayexpress.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'GOJI',
                    'unishippers_carrierName' => 'DICOM FREIGHT',
                    'carrier_logo' => 'dicomfreight.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'ETPE',
                    'unishippers_carrierName' => 'STG Express',
                    'carrier_logo' => 'stgexpress.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'JPXS',
                    'unishippers_carrierName' => 'JP Express',
                    'carrier_logo' => 'jpexpress.gif'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'LAEA',
                    'unishippers_carrierName' => 'Land Air Express',
                    'carrier_logo' => 'land-air-express.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'MTVL',
                    'unishippers_carrierName' => 'Mountain Valley Express',
                    'carrier_logo' => 'mountainvalleyexpress.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'NMTF',
                    'unishippers_carrierName' => 'N&M Transfer Co Inc',
                    'carrier_logo' => 'nmtransfer.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'PITD',
                    'unishippers_carrierName' => 'Pitt Ohio',
                    'carrier_logo' => 'pittohio.png'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'STDF',
                    'unishippers_carrierName' => 'STANDARD FORWARDING',
                    'carrier_logo' => 'tandardfowarding.jpg'
                ));

                $wpdb->insert(
                    $table_name, array(
                    'unishippers_carrierSCAC' => 'BRTC',
                    'unishippers_carrierName' => 'BC Freightways',
                    'carrier_logo' => 'brtc.png'
                ));
            }
        }

        /**
         * Create LTL Class
         */
        function create_unishippers_ltl_class()
        {

            wp_insert_term(
                'LTL Freight', 'product_shipping_class', array(
                    'description' => 'The plugin is triggered to provide an LTL freight quote when the shopping cart contains an item that has a designated shipping class. Shipping class? is a standard WooCommerce parameter not to be confused with freight class? or the NMFC classification system.',
                    'slug' => 'ltl_freight'
                )
            );
        }

    }

}