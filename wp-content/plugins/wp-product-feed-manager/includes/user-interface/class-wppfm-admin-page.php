<?php

/**
 * WP Product Feed Manager Admin Page Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @version 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Admin_Page' ) ) :

	/**
	 *  WPPFM Admin Page Class
	 */
	class WPPFM_Admin_Page {

		public $tab_data;

		public function __construct() {

			$wppfm_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : null;

			$this->tab_data = apply_filters(
				'wppfm_main_form_tabs',
				array(
					array(
						'page'       => 'wp-product-feed-manager&tab=feed-list',
						'tab_status' => 'feed-list' === $wppfm_active_tab ? ' nav-tab-active' : '',
						'title'      => __( 'Feed List', 'wp-product-feed-manager' ),
					),
					array(
						'page'       => 'wp-product-feed-manager&tab=product-feed',
						'tab_status' => 'product-feed' === $wppfm_active_tab ? ' nav-tab-active' : '',
						'title'      => __( 'Product Feed', 'wp-product-feed-manager' ),
					),
				),
				$wppfm_active_tab
			);
		}

		/**
		 * Returns a string containing the standard header for an admin page.
		 *
		 * @param   string  $header_text
		 *
		 * @return  string
		 */
		protected function admin_page_header( $header_text = 'WP Product Feed Manager' ) {
			$spinner_gif = WPPFM_PLUGIN_URL . '/images/ajax-loader.gif';
			$ticket_link = WPPFM_EDD_SL_ITEM_NAME === 'WP Product Feed Manager' ? 'https://wordpress.org/support/plugin/wp-product-feed-manager'
				: WPPFM_EDD_SL_STORE_URL . '/support/'; // Get the link to the ticket system depending on the version of the plugin.
			$feed_queue  = implode( ',', get_site_option( 'wppfm_feed_queue', array() ) ); // Get the active feed queue.

			return
				'<div class="wrap">
			<div class="feed-spinner" id="feed-spinner" style="display:none;">
				<img id="img-spinner" src="' . $spinner_gif . '" alt="Loading" />
			</div>
			<div class="data" id="wp-product-feed-manager-data" style="display:none;"><div id="wp-plugin-url">' . WPPFM_UPLOADS_URL . '</div><div id="wppfm-feed-list-feeds-in-queue">' . $feed_queue . '</div></div>
			<div class="wppfm-main-wrapper wppfm-header-wrapper" id="header-wrapper">
			<div class="header-text"><h1>' . $header_text . '</h1></div>
			<div class="sub-header-text"><h3>' . esc_html__( 'Manage your feeds with ease', 'wp-product-feed-manager' ) . '</h3></div>
			<div class="links-wrapper" id="header-links"><a href="' . WPPFM_EDD_SL_STORE_URL . '/support/documentation/create-product-feed/" target="_blank">'
				. esc_html__( 'Click here for the documentation', 'wp-product-feed-manager' ) . '</a></div>
			<div class="links-wrapper" id="ticket-link"><a href="' . $ticket_link . '" target="_blank">' . esc_html__( 'Something not working? Click here for support', 'wp-product-feed-manager' ) . '</a></div>
			</div>';
		}

		protected function message_field( $alert = '' ) {
			$display_alert = empty( $alert ) ? 'none' : 'block';

			return
				'<div class="message-field notice notice-error" id="error-message" style="display:none;"></div>
			 <div class="message-field notice notice-success" id="success-message" style="display:none;"></div>
			 <div class="message-field notice notice-warning" id="disposable-warning-message" style="display:' . $display_alert . ';"><p>' . $alert . '</p>
			<button type="button" id="disposable-notice-button" class="notice-dismiss"></button>
			</div>';
		}

		/**
		 * returns the html code for the tabs
		 *
		 * @return string
		 */
		protected function tabs() {
			return WPPFM_Form_Element::main_form_tabs();
		}

		/**
		 * Returns the html code for the tab header.
		 *
		 * @param   string  $header_title       String for the tab header text.
		 * @param   string  $header_sub_title   String for the sub title below the tab header text.
		 *
		 * @return  string  Html code containing the tab header.
		 *@since 2.11.0.
		 */
		protected function tab_header( $header_title, $header_sub_title ) {
			return '<h1 class="screen-reader-text">Feed List</h1><h2>' . $header_title . '</h2>
			<div><p>' . $header_sub_title . '</p></div>';
		}

	}


	// end of WPPFM_Admin_Page class

endif;
