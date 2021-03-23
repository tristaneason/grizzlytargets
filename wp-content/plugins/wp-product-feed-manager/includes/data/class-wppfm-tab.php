<?php

/**
 * WPPFM Tab Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Tab' ) ) :

	class WPPFM_Tab {

		/*
		 * Contains the page identifier string
		 */
		private $_page_identifier;

		/*
		 * Is the selected tab or not
		 */
		private $_tab_status;

		/*
		 * Contains the title of the tab
		 */
		private $_tab_title;

		/*
		 * Contains a class identifier
		 */
		private $_class_identifier;

		/**
		 * WPPFM_Tab constructor.
		 *
		 * @param string $page_identifier
		 * @param bool   $tab_status
		 * @param string $tab_title
		 * @param string $class_identifier
		 */
		public function __construct( $page_identifier, $tab_status, $tab_title, $class_identifier ) {

			$this->_page_identifier  = $page_identifier;
			$this->_tab_status       = $tab_status;
			$this->_tab_title        = $tab_title;
			$this->_class_identifier = $class_identifier;
		}

		/**
		 * Returns the tabs url parameters page and tab
		 *
		 * @return string
		 */
		public function get_page_tab_url() {
			return 'page=wp-product-feed-manager&tab=' . $this->_page_identifier;
		}

		public function get_page_identifier() {
			return $this->_page_identifier;
		}

		public function get_class_identifier() {
			return $this->_class_identifier;
		}

		/**
		 * Returns a string that can be used in the tabs html code to identify it as the active tab
		 *
		 * @return string
		 */
		public  function tab_selected_string() {
			return $this->_tab_status ? '  nav-tab-active' : '';
		}

		/**
		 * Returns the tab title
		 *
		 * @return string
		 */
		public function get_tab_title() {
			return $this->_tab_title;
		}

		/**
		 * Returns the main input wrapper class name
		 *
		 * @return string
		 */
		public function get_main_input_wrapper_class() {
			return 'WPPFM_' . $this->_class_identifier . '_Main_Input_Wrapper';
		}

		/**
		 * Returns the category selector wrapper class name
		 *
		 * @return string
		 */
		public function get_category_selector_wrapper_class() {
			return 'WPPFM_' . $this->_class_identifier . '_Category_Wrapper';
		}

		/**
		 * Returns the attribute mapping wrapper class name
		 *
		 * @return string
		 */
		public function get_attribute_mapping_wrapper_class() {
			return 'WPPFM_' . $this->_class_identifier . '_Attribute_Mapping_Wrapper';
		}

		/**
		 * Set this tab as the selected tab
		 */
		public function set_tab_as_selected() {
			$this->_tab_status = true;
		}
	}

	// end of WPPFM_Tab class

endif;
