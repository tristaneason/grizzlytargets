<?php

/**
 * WP Product Feed Manager Add Feed Page Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Product_Feed_Page' ) ) :

	/**
	 * WPPFM Feed Form Class
	 */
	class WPPFM_Product_Feed_Page extends WPPFM_Admin_Page {

		/**
		 * @var string|null contains the feed id, null for a new feed.
		 */
		private $_feed_id;

		/**
		 * @var array|null  contains the feed data.
		 */
		private $_feed_data;

		public function __construct() {

			parent::__construct();

			wppfm_check_db_version();

			$this->_feed_id = array_key_exists( 'id', $_GET ) && $_GET['id'] ? $_GET['id'] : null;

			$this->set_feed_data();

			add_option( 'wp_enqueue_scripts', WPPFM_i18n_Scripts::wppfm_feed_settings_i18n() );
			add_option( 'wp_enqueue_scripts', WPPFM_i18n_Scripts::wppfm_list_table_i18n() );
		}

		/**
		 * Collects the html code for the product feed form page and displays it.
		 */
		public function show() {

			$tab_header_sub_title = $this->_feed_id ? __( 'Here you can edit the parameters of your feed.', 'wp-product-feed-manager' ) :
				__( 'Here you can setup your new feed. Start by entering a name for your feed and selecting a channel.', 'wp-product-feed-manager' );

			echo $this->admin_page_header();

			echo $this->message_field();

			if ( wppfm_wc_installed_and_active() ) {
				if ( ! wppfm_wc_min_version_required() ) {
					echo wppfm_update_your_woocommerce_version_message();
					exit;
				}

				echo $this->tabs();

				echo $this->tab_header( __( 'Edit Product Feed', 'wp-product-feed-manager' ), $tab_header_sub_title );

				echo $this->product_feed_page_data_holder();

				echo $this->main_input_table_wrapper();

				echo $this->category_selector_table_wrapper();

				echo $this->feed_top_buttons();

				echo $this->attribute_mapping_table_wrapper();

				echo $this->feed_bottom_buttons();

				echo $this->feed_list_button();
			} else {
				echo wppfm_you_have_no_woocommerce_installed_message();
			}
		}

		/**
		 * Fills the $feed_data_holder with the correct data that then can be passed through to the edit feed page.
		 *
		 * @return  string  Containing the data that is required to build the edit feed page.
		 */
		private function product_feed_page_data_holder() {
			$feed_data_holder  = WPPFM_Form_Element::feed_data_holder( $this->_feed_data );
			$feed_data_holder .= WPPFM_Form_Element::ajax_to_db_conversion_data_holder();
			$feed_data_holder .= WPPFM_Form_Element::feed_url_holder();
			$feed_data_holder .= WPPFM_Form_Element::used_feed_names();

			return $feed_data_holder;
		}

		/**
		 * Fetches feed data from the database and stores it in the _feed_data variable. This data is required to build the edit feed page. Stores empty
		 * data when the page is opened from a new feed.
		 */
		private function set_feed_data() {

			if ( $this->_feed_id ) {
				$queries_class = new WPPFM_Queries();
				$data_class    = new WPPFM_Data();

				$feed_data      = $queries_class->read_feed( $this->_feed_id )[0];
				$feed_filter    = $queries_class->get_product_filter_query( $this->_feed_id );
				$source_fields  = $data_class->get_source_fields( '1' );
				$attribute_data = $data_class->get_attribute_data( $this->_feed_id, $feed_data['channel'] );

				// Verify the categories in the stored category mapping are still active.
				$feed_data['category_mapping'] = $data_class->verify_categories_in_mapping( $feed_data['category_mapping'] );
			} else {
				$source_fields  = [];
				$attribute_data = [];
				$feed_filter    = '';
				$feed_data      = null; // a new feed
			}

			$this->_feed_data = array(
				'feed_id'            => $this->_feed_id ? $this->_feed_id : false,
				'feed_file_name'     => $feed_data ? $feed_data['title'] : '',
				'channel_id'         => $feed_data ? $feed_data['channel'] : '',
				'language'           => $feed_data ? $feed_data['language'] : '',
				'currency'           => $feed_data ? $feed_data['currency'] : '',
				'target_country'     => $feed_data ? $feed_data['country'] : '',
				'category_mapping'   => $feed_data ? $feed_data['category_mapping'] : '',
				'main_category'      => $feed_data ? $feed_data['main_category'] : '',
				'include_variations' => $feed_data ? $feed_data['include_variations'] : '',
				'is_aggregator'      => $feed_data ? $feed_data['is_aggregator'] : '',
				'url'                => $feed_data ? $feed_data['url'] : '',
				'source'             => $feed_data ? $feed_data['source'] : '',
				'feed_title'         => $feed_data ? $feed_data['feed_title'] : '',
				'feed_description'   => $feed_data ? $feed_data['feed_description'] : '',
				'schedule'           => $feed_data ? $feed_data['schedule'] : '',
				'status_id'          => $feed_data ? $feed_data['status_id'] : '',
				'feed_filter'        => $feed_filter ? $feed_filter : null,
				'attribute_data'     => $attribute_data,
				'source_fields'      => $source_fields,
			);
		}

		/**
		 * Returns the html code for the main input table.
		 */
		private function main_input_table_wrapper() {
			$main_input_wrapper = new WPPFM_Product_Feed_Main_Input_Wrapper();
			$main_input_wrapper->display();
		}

		/**
		 * Returns the html code for the category mapping table.
		 */
		private function category_selector_table_wrapper() {
			$category_table_wrapper = new WPPFM_Product_Feed_Category_Wrapper();
			$category_table_wrapper->display();
		}

		/**
		 * Return the html code for the attribute mapping table.
		 */
		private function attribute_mapping_table_wrapper() {
			$attribute_mapping_wrapper = new WPPFM_Product_Feed_Attribute_Mapping_Wrapper();
			$attribute_mapping_wrapper->display();
		}

		/**
		 * Returns the html code for the Save & Generate Feed and Save Feed buttons at the top of the attributes list.
		 *
		 * @return string
		 */
		private function feed_top_buttons() {
			return WPPFM_Form_Element::feed_generation_buttons( 'wppfm-generate-feed-button-top', 'wppfm-save-feed-button-top', 'wppfm-view-feed-button-top' );
		}

		/**
		 * Returns the html code for the Save & Generate Feed and Save Feed buttons at the bottom of the attributes list.
		 *
		 * @return string
		 */
		private function feed_bottom_buttons() {
			return WPPFM_Form_Element::feed_generation_buttons( 'wppfm-generate-feed-button-bottom', 'wppfm-save-feed-button-bottom', 'wppfm-view-feed-button-bottom' );
		}

		/**
		 * Returns the html code for the Open Feed List button.
		 *
		 * @return string
		 */
		private function feed_list_button() {
			return WPPFM_Form_Element::open_feed_list_button();
		}
	}

	// end of WPPFM_Product_Feed_Form class

endif;
