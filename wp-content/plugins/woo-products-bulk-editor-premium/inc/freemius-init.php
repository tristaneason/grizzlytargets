<?php

if ( !function_exists( 'wpsewcp_freemius' ) ) {
    // Create a helper function for easy SDK access.
    function wpsewcp_freemius()
    {
        global  $wpsewcp_freemius ;
        if ( !isset( $wpsewcp_freemius ) ) {
            $wpsewcp_freemius = fs_dynamic_init( array(
                'id'             => '2812',
                'slug'           => 'woo-bulk-edit-products',
                'type'           => 'plugin',
                'public_key'     => 'pk_76f83bd212746e2bef295215819a6',
                'is_premium'     => true,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'       => 'wpsewcp_welcome_page',
                'first-path' => 'admin.php?page=wpsewcp_welcome_page',
                'support'    => false,
            ),
                'is_live'        => true,
            ) );
        }
        return $wpsewcp_freemius;
    }
    
    // Init Freemius.
    wpsewcp_freemius();
    // Signal that SDK was initiated.
    do_action( 'wpsewcp_freemius_loaded' );
}
