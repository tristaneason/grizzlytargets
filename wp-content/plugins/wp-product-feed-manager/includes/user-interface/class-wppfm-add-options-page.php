<?php

/**
 * WP Product Feed Manager Add Options Page Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @version 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Add_Options_Page' ) ) :

	class WPPFM_Add_Options_Page extends WPPFM_Admin_Page {
		private $_options_form;

		public function __construct() {
			parent::__construct();

			add_option( 'wp_enqueue_scripts', WPPFM_i18n_Scripts::wppfm_settings_i18n() );
			add_option( 'wp_enqueue_scripts', WPPFM_i18n_Scripts::wppfm_backup_list_i18n() );

			$this->_options_form = new WPPFM_Options_Page();
		}

		public function show() {
			echo $this->options_page_header();

			echo $this->message_field();

			echo $this->options_page_body();

			echo $this->options_page_footer();
		}

		private function options_page_header() {
			$spinner_gif = WPPFM_PLUGIN_URL . '/images/ajax-loader.gif';

			return
				'
		<div class="wrap">
		<div class="feed-spinner" id="feed-spinner" style="display:none;">
			<img id="img-spinner" src="' . $spinner_gif . '" alt="Loading" />
		</div>
		<div class="wppfm-main-wrapper wppfm-header-wrapper" id="wppfm-header-wrapper">
		<div class="header-text"><h1>' . esc_html__( 'Feed Manager Settings', 'wp-product-feed-manager' ) . '</h1></div>
		<div class="logo"></div>
		</div>
		';
		}

		private function options_page_body() {
			$this->_options_form->display();
		}

		private function options_page_footer() {
		}
	}

	// end of WPPFM_Add_Options_Page class

endif;
