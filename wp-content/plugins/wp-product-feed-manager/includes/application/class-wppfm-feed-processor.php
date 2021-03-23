<?php

/**
 * WP Product Feed Controller Class.
 *
 * @package WP Product Feed Manager/Application/Classes
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Feed_Processor' ) ) :


	/**
	 * Feed Processor Class
	 *
	 * @since 1.10.0
	 */
	class WPPFM_Feed_Processor extends WPPFM_Background_Process {

		use WPPFM_Processing_Support;
		use WPPFM_Feed_Processor_Functions;

		/**
		 * Action identifier
		 *
		 * @var string
		 */
		protected $action = 'feed_generation_process';

		/**
		 * Path to the feed file
		 *
		 * @var string
		 */
		private $_feed_file_path;

		/**
		 * General feed data
		 *
		 * @var stdClass
		 */
		private $_feed_data;

		/**
		 * Required pre feed generation data
		 *
		 * @var array
		 */
		private $_pre_data;

		/**
		 * Contains the channel specific main category title and description title
		 *
		 * @var array
		 */
		private $_channel_details;

		/**
		 * Contains the relations between WooCommerce and channel fields
		 *
		 * @var array
		 */
		private $_relation_table;

		/**
		 * Placeholder for the correct channel class
		 *
		 * @var string
		 */
		private $_channel_class;

		/**
		 * Task
		 *
		 * Starts a single feed update task
		 *
		 * @param array product
		 * @param stdClass feed_data
		 * @param string feed_file_path
		 * @param array pre_data
		 * @param array channel_details
		 * @param array relation_table
		 *
		 * @return boolean
		 */
		protected function task( $task_data, $feed_data, $feed_file_path, $pre_data, $channel_details, $relation_table ) {
			if ( ! $task_data ) {
				return false;
			}

			$this->_feed_data       = $feed_data;
			$this->_feed_file_path  = $feed_file_path;
			$this->_pre_data        = $pre_data;
			$this->_channel_details = $channel_details;
			$this->_relation_table  = $relation_table;

			if ( ! $this->_channel_details['channel_id'] ) {
				return false;
			}

			// instantiate the correct channel class
			$this->_channel_class = new WPPFM_Google_Feed_Class();

			return $this->do_task( $task_data );
		}

		/**
		 * Complete
		 */
		public function complete() {
			parent::complete();

			// Remove the properties from the options table.
			$properties_key = get_site_option( 'wppfm_background_process_key' );
			delete_site_option( 'wppfm_background_process_key' );
			delete_site_option( 'file_path_' . $properties_key );
			delete_site_option( 'feed_data_' . $properties_key );
			delete_site_option( 'pre_data_' . $properties_key );
			delete_site_option( 'channel_details_' . $properties_key );
			delete_site_option( 'relations_table_' . $properties_key );

			$feed_status = '0' !== $this->_feed_data->status && '3' !== $this->_feed_data->status && '4' !== $this->_feed_data->status ? $this->_feed_data->status : $this->_feed_data->baseStatusId;
			$feed_title  = $this->_feed_data->title . '.' . pathinfo( $this->_feed_file_path, PATHINFO_EXTENSION );
			$this->register_feed_update( $this->_feed_data->feedId, $feed_title, count( $this->processed_products ), $feed_status );
			$this->clear_the_queue();

			// Now the feed is ready to go, remove the feed id from the feed queue.
			WPPFM_Feed_Controller::remove_id_from_feed_queue( $this->_feed_data->feedId );
			WPPFM_Feed_Controller::set_feed_processing_flag( false );

			$message = sprintf( 'Completed feed %s. The feed should contain %d products and its status has been set to %s.', $this->_feed_data->feedId, count( $this->processed_products ), $feed_status );
			do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );
			do_action( 'wppfm_register_feed_url', $this->_feed_data->feedId, $this->_feed_data->url );

			if ( ! WPPFM_Feed_Controller::feed_queue_is_empty() ) {
				do_action( 'wppfm_next_in_queue_feed_update_activated', $this->_feed_data->feedId );

				// So there is another feed in the queue.
				$feed_master_class = new WPPFM_Feed_Master_Class( $this->_feed_data->feedId );
				$feed_master_class->update_feed_file();
			}
		}

		/**
		 * Task action
		 * Selects the required action
		 *
		 * @param array $task_data
		 *
		 * @return boolean
		 */
		private function do_task( $task_data ) {

			if ( array_key_exists( 'product_id', $task_data ) ) {
				return $this->add_product_to_feed( $task_data['product_id'] );
			} elseif ( array_key_exists( 'file_format_line', $task_data ) ) {
				// the Wordfence plugin sometimes identifies the <link> string as a XSS vulnerability and blocks wp_remote_post action starting the feed process
				// To counter that, I changed the <link> string in the (google) xml feed header to <wf-connection-string> (in the Google channels class-feed.php file)
				// and now I need to change that back to <link> again.
				$task_data['file_format_line'] = str_replace( '<wf-connection-string>', '<link>', $task_data['file_format_line'] );

				return $this->add_file_format_line_to_feed( $task_data );
			} elseif ( array_key_exists( 'error_message', $task_data ) ) {
				return $this->add_error_message_to_feed( $task_data );
			} else {
				return false;
			}
		}

		/**
		 * Ads a single product based on a product id to the feed file.
		 *
		 * @param string $product_id The id of the product to be added to the feed.
		 *
		 * @return boolean
		 */
		private function add_product_to_feed( $product_id ) {
			if ( ! $product_id ) {
				$message = 'Add product to feed process started without product id';
				do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );

				return false;
			}

			$wc_product = wc_get_product( $product_id );

			if ( false === $wc_product ) {
				$message = sprintf( 'Failed to get the WooCommerce product data from product with id %s.', $product_id );
				do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );

				return false;
			}

			do_action( 'wppfm_started_product_processing', $this->_feed_data->feedId, $product_id );

			$class_data = new WPPFM_Data();

			$product_placeholder       = array();
			$post_columns_query_string = $this->_pre_data['database_fields']['post_column_string'] ? substr( $this->_pre_data['database_fields']['post_column_string'], 0, - 2 ) : '';
			$product_parent_id         = $product_id;
			$product_data              = (array) $this->get_products_main_data( $product_id, $wc_product->get_parent_id(), $post_columns_query_string );

			/**
			 * Users can use the wppfm_leave_links_in_descriptions filter if they want to keep links in the product descriptions by changing the
			 * filter output to true. They can also target specific feeds.
			 *
			 * @since 2.6.0
			 */
			if ( ! apply_filters( 'wppfm_leave_links_in_descriptions', false, $this->_feed_data->feedId ) ) {
				$this->remove_links_from_product_data_description( $product_data );
			}

			if ( ( $wc_product instanceof WC_Product_Variation && $this->_pre_data['include_vars'] )
				|| ( $wc_product instanceof WC_Product_Variable ) && $this->_pre_data['include_vars'] ) {

				$product_parent_id = $wc_product->get_parent_id();

				// Add parent data when this item is not available in the variation.
				if ( $post_columns_query_string ) {
					$class_data->add_parent_data( $product_data, $product_parent_id, $post_columns_query_string );
				}

				$wpmr_variation_data = $class_data->get_own_variation_data( $product_id );

				// Get correct variation data.
				WPPFM_Variations::fill_product_data_with_variation_data( $product_data, $wc_product, $wpmr_variation_data, $this->_feed_data->language );
			}

			$row_category = $this->get_mapped_category( $product_parent_id, $this->_feed_data->mainCategory, json_decode( $this->_feed_data->categoryMapping ) );

			if ( ! $row_category ) {
				$message = sprintf( 'Could not identify the correct category map for product %s', $product_id );
				do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );

				return false;
			}

			$row_filtered = $this->is_product_filtered( $this->_pre_data['filters'], $product_data );

			// Only process the product if its not filtered out.
			if ( ! $row_filtered ) {
				// For each row loop through each field.
				foreach ( $this->_pre_data['active_fields'] as $field ) {
					$field_meta_data = $this->get_meta_data_from_specific_field( $field, $this->_feed_data->attributes );

					// Get the field data based on the user settings.
					$feed_object = $this->process_product_field(
						$product_data,
						$field_meta_data,
						$this->_channel_details['category_name'],
						$row_category,
						$this->_feed_data->language,
						$this->_relation_table
					);

					$key = key( $feed_object );

					// For an xml file only add fields that contain data.
					if ( ( ! empty( $feed_object[ $key ] ) || '0' === $feed_object[ $key ] ) || 'xml' !== pathinfo( $this->_feed_file_path, PATHINFO_EXTENSION ) ) {

						// Keep money values that have a 0 value out of the feed. @since 2.11.2.
						if ( wppfm_meta_key_is_money( $key ) ) {
							if ( 0.0 === floatval( $feed_object[ $key ] ) ) { continue; }
						}

						// Catch the DraftImages key for the Ricardo.ch channel.
						if ( 'DraftImages' !== $key ) {
							$product_placeholder[ $key ] = $feed_object[ $key ];
						} else {
							$support_class = new WPPFM_Feed_Support();
							$support_class->process_ricardo_draftimages( $product_placeholder, $feed_object[ $key ] );
						}
					}
				}
			} else {
				$message = sprintf( 'Product %s is filtered out', $product_id );
				do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );

				return 'filtered';
			}

			if ( $product_placeholder ) {
				// The wppfm_feed_item_value filter allows users to modify the data that goes into the feed. The $data variable contains an array
				// with all the data that goes into the feed, with the items name as key.
				$product_placeholder = apply_filters( 'wppfm_feed_item_value', $product_placeholder, $this->_feed_data->feedId, $product_id );

				return $this->write_product_object( $product_placeholder, $this->_feed_data->feedId, $product_id );
			} else {
				return false;
			}
		}

		/**
		 * Appends a product to the feed
		 *
		 * @param array $feed_product_object
		 * @param string $feed_id
		 * @param string $product_id
		 *
		 * @return string product added or boolean false
		 */
		private function write_product_object( $feed_product_object, $feed_id, $product_id ) {
			//@since 2.3.0
			do_action( 'wppfm_add_product_to_feed', $feed_id, $product_id );

			$product_text = $this->generate_feed_text( $feed_product_object );

			if ( false === file_put_contents( $this->_feed_file_path, $product_text, FILE_APPEND ) ) {
				wppfm_write_log_file( sprintf( 'Could not write product %s to the feed', $product_id ) );

				return false;
			} else {
				return 'product added';
			}
		}

		/**
		 * convert the feed data of a single product into xml or csv text depending on the channel
		 *
		 * @param array $data
		 *
		 * @return string
		 */
		protected function generate_feed_text( $data ) {
			switch ( pathinfo( $this->_feed_file_path, PATHINFO_EXTENSION ) ) {
				case 'xml':
					return $this->convert_data_to_xml( $data, $this->_channel_details['category_name'], $this->_channel_details['description_name'], $this->_channel_details['channel_id'] );

				case 'txt':
					$txt_sep = apply_filters( 'wppfm_txt_separator', get_correct_txt_separator($this->_channel_details['channel_id']) );
					return $this->convert_data_to_txt( $data, $txt_sep );

				case 'csv':
					$csv_sep = apply_filters( 'wppfm_csv_separator', get_correct_csv_separator( $this->_channel_details['channel_id'] ) );
					return $this->convert_data_to_csv( $data, $this->_pre_data['active_fields'], $csv_sep );

				case 'tsv':
					return $this->convert_data_to_tsv( $data );
			}

			return '';
		}

	}

endif;
