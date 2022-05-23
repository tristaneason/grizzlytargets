<?php
/**
 * Hubspot Compatibility class
 *
 * @since 6.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Hubspot_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'leadin_impact_code', array( $this, 'get_hubspot_affiliate_code' ) );
        add_action( 'leadin_activate', array( $this, 'disable_redirect' ) );
	}

    public function get_hubspot_affiliate_code() {
        return '2reRgz';
    }

    public function disable_redirect() {
        remove_all_actions( 'leadin_redirect' );
    }
}

new Porto_Hubspot_Compatibility();