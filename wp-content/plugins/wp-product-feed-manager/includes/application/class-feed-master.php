<?php
/** @noinspection PhpUndefinedMethodInspection */

/**
 * WP Product Feed Master Class.
 *
 * @package WP Product Feed Manager/Application/Classes
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Feed_Master_Class' ) ) :

	/**
	 * Feed Master Class
	 *
	 * @since 2.0.0
	 */
	class WPPFM_Feed_Master_Class {

		use WPPFM_Processing_Support;

		/**
		 * Contains the general feed data
		 *
		 * @var object
		 */
		protected $_feed = null;

		/**
		 * Contains the feed is
		 *
		 * @var string
		 */
		protected $_feed_id;

		/**
		 * Instantiation of global background process class
		 *
		 * @var stdClass
		 */
		protected $_background_process;

		/**
		 * Placeholder for the correct channel class
		 *
		 * @var stdClass
		 */
		protected $_channel_class;

		/**
		 * Placeholder for the WPPFM_Data_Class
		 *
		 * @var stdClass
		 */
		protected $_data_class;

		/**
		 * Path and name of the feed file
		 *
		 * @var string
		 */
		protected $_feed_file_path;

		/**
		 * Initiate new Feed Master class.
		 *
		 * @param string $feed_id The id of the feed. Default 0.
		 *
		 * @global stdClass $background_process
		 */
		public function __construct( $feed_id = '0' ) {
			$query_class = new WPPFM_Queries();

			// When this construct is called from a channel class, it will not specify the feed_id so the default 0 is used
			// in that case the feed_type_id will always be 1.
			$feed_type_id = $feed_id > 0 ? $query_class->get_feed_type_id( $feed_id ) : '1';

			$background_class = apply_filters( 'wppfm_background_class', 'WPPFM_Feed_Processor', $feed_type_id );

			$this->_background_process = new $background_class();
			$this->_data_class         = new WPPFM_Data();
		}

		/**
		 * The update feed file function that starts the update process
		 *
		 * @param bool $silent  Indicates whether process messages should be shown or not (default true).
		 *
		 * @return string|void or false
		 */
		public function update_feed_file( $silent = true ) {

			$feed_id = WPPFM_Feed_Controller::get_next_id_from_feed_queue();

			do_action( 'wppfm_feed_process_prepared', $feed_id, $silent );

			if ( false === $feed_id ) {
				return false;
			}

			if ( $silent ) {
				set_transient( 'wppfm_running_silent', true, WPPFM_TRANSIENT_LIVE );
			}

			$feed_data = $this->_data_class->get_feed_data( $feed_id );

			if ( ! $feed_data ) {
				do_action( 'wppfm_feed_generation_message', $feed_id, 'The update_feed_file function failed to get the feed data', 'ERROR' );
				if ( ! $silent ) {
					esc_attr_e( '1428 - Failed to load the feed data, please try to generate a feed in the foreground mode (see Settings page) and then try the background mode again.', 'wp-product-feed-manager' );
				}

				echo 'activation_error';

				return false;
			}

			// Store the feed data in a property.
			$this->_feed = $feed_data;

			// Only one feed can be processing so if other feeds than the current feed are on a processing
			// status, set these to an error status.
			$this->_data_class->check_for_failed_feeds( $this->_feed->feedId );

			$prepare_update = $this->prepare_feed_file_update();

			if ( true !== $prepare_update ) {
				if ( ! $silent ) {
					echo esc_attr( $prepare_update );
				}

				return false;
			}

			WPPFM_Feed_Controller::set_feed_processing_flag( true );

			$this->prepare_background_process();

			$this->fill_the_background_queue();

			$this->activate_feed_file_update( $this->_feed->feedId );

			delete_transient( 'wppfm_running_silent' );

			if ( ! $silent ) {
				echo 'started_processing';
			}
		}

		/**
		 * Checks if the feed is still processing correctly. If not it will change the feed status to failed.
		 *
		 * @param string $feed_id   The id of the feed to be checked.
		 *
		 * @return array   An array with feed status data.
		 */
		public function feed_status_check( $feed_id ) {
			$queries_class = new WPPFM_Queries();

			$current_feed_status = $queries_class->get_feed_status_data( $feed_id );

			$current_feed_status['feed_type_name'] = wppfm_list_feed_type_text()[ $current_feed_status['feed_type_id'] ];

			if ( '3' === $current_feed_status['status_id'] ) { // Status still processing.
				// Get file name, including path.
				$file_extension = function_exists( 'get_file_type' ) ? get_file_type( $current_feed_status['channel_id'] ) : 'xml';
				$feed_file      = wppfm_get_file_path( $current_feed_status['title'] . '.' . $file_extension );

				// If it is, set the feed status to fail and change the $current_feed_status['status_id'] to 6.
				if ( WPPFM_Feed_Controller::feed_processing_failed( $feed_file ) ) {
					// Change the status of the feed to failed processing.
					$this->_data_class->update_feed_status( $feed_id, 6 ); // Feed status to failed.

					// Update the current_feed_status variable before returning.
					$current_feed_status['status_id'] = '6';

					// Clear this feed from the feed queue.
					WPPFM_Feed_Controller::remove_id_from_feed_queue( $feed_id );
					WPPFM_Feed_Controller::set_feed_processing_flag( false );

					// If running silent (automatic feed update) inform the user about the failed feed.
					if ( get_transient( 'wppfm_running_silent' ) ) {
						WPPFM_Email::send_feed_failed_message();
					}

					if ( ! WPPFM_Feed_Controller::feed_queue_is_empty() ) {
						$next_feed_id = WPPFM_Feed_Controller::get_next_id_from_feed_queue();

						if ( $next_feed_id ) {
							// So there is another feed in the queue.
							$feed_master_class = new WPPFM_Feed_Master_Class( $next_feed_id );
							$feed_master_class->update_feed_file();
						}
					}

					do_action( 'wppfm_feed_processing_failed_file_size_stopped_increasing', $feed_id, WPPFM_Feed_Controller::nr_ids_remaining_in_queue() );
					do_action( 'wppfm_register_feed_url', $feed_id, $feed_file );
				}

				return $current_feed_status;
			} else {
				return $current_feed_status;
			}
		}

		/**
		 * Perform all preparations for the feed update starts
		 *
		 * @return bool true if feed file has been updated successfully
		 */
		private function prepare_feed_file_update() {
			// Prepare the folder structure to support saving feed files.
			if ( ! file_exists( WPPFM_FEEDS_DIR ) ) {
				WPPFM_Folders::make_feed_support_folder();
			}

			if ( ! is_writable( WPPFM_FEEDS_DIR ) ) {
				/* translators: %s: Folder where the feeds are stored */
				return sprintf( __( '1430 - %s is not a writable folder. Make sure you have admin rights to this folder.', 'wp-product-feed-manager' ), WPPFM_FEEDS_DIR );
			}

			$initial_feed_status = $this->_feed->status;

			if ( ! $this->set_properties() ) {
				$message = sprintf( 'Failed to set the properties of feed %s.', $this->_feed->feedId );
				do_action( 'wppfm_feed_generation_message', $this->_feed->feedId, $message, 'ERROR' );

				return false;
			}

			$this->_data_class->set_nr_of_feed_products( $this->_feed->feedId, '0' ); // 0 products.
			$this->_data_class->update_feed_status( $this->_feed->feedId, 3 ); // Set status to "Processing".

			$file_extension = function_exists( 'get_file_type' ) ? get_file_type( $this->_feed->channel ) : 'xml';

			$this->_feed_file_path = wppfm_get_file_path( $this->_feed->title . '.' . $file_extension );

			// Clear the existing feed.
			file_put_contents( $this->_feed_file_path, '' );

			// Clear the file size checker.
			delete_transient( 'wppfm_feed_file_size' );

			// clear the list of processed products @since 2.10.0.
			delete_option( 'wppfm_processed_products' );

			$channel_class = new WPPFM_Channel();
			$channel_name  = $channel_class->get_channel_short_name( $this->_feed->channel );

			$logger_message = sprintf( 'Feed %s is a %s feed stored as %s, with an original feed status %s.', $this->_feed->feedId, $channel_name, $this->_feed_file_path, $initial_feed_status );
			do_action( 'wppfm_feed_generation_message', $this->_feed->feedId, $logger_message );

			return true;
		}

		/**
		 * Store common product metadata in the Background Process properties
		 */
		private function prepare_background_process() {
			// Start counting from zero.
			delete_option( 'wppfm_processed_products' );

			$this->_background_process->set_feed_data( $this->_feed );
			$this->_background_process->set_file_path( $this->_feed_file_path );
			$this->_background_process->set_pre_data( $this->get_required_pre_data() );
			$this->_background_process->set_channel_details( $this->get_channel_details() );
			$this->_background_process->set_relations_table( $this->get_channel_to_woocommerce_field_relations() );
		}

		/**
		 * Fills the background queue
		 */
		private function fill_the_background_queue() {
			// Start with an empty queue.
			$this->_background_process->clear_the_queue();
			$sw_status_control = 30 * 3.3;
			$product_counter = 0;

			// Add the header to the queue.
			$header_string = $this->get_feed_header();
			$this->_background_process->push_to_queue( array( 'file_format_line' => $header_string ) );

			do {
				$product_ids = $this->get_product_ids_for_feed();

				// Add the product ids to the queue.
				foreach ( $product_ids as $product_id ) {
					$this->_background_process->push_to_queue( $product_id );

					$product_counter++;

					if ( $product_counter > $sw_status_control ) {
						break;
					}
				}
			} while ( ! empty( $product_ids ) && $sw_status_control > $product_counter );

			delete_transient( 'wppfm_start_product_id' );

			// implement the wppfm_feed_ids_in_queue filter on the queue.
			$this->_background_process->apply_filter_to_queue( $this->_feed->feedId );

			do_action( 'wppfm_feed_queue_filled', $this->_feed->feedId, $product_counter );

			$product_ids = null;

			$file_extension = function_exists( 'get_file_type' ) ? get_file_type( $this->_feed->channel ) : 'xml';

			// Add the xml footer to the queue.
			if ( 'xml' === $file_extension ) {
				$this->_background_process->push_to_queue(
					array(
						'file_format_line' => apply_filters(
							'wppfm_footer_string',
							$this->_channel_class->footer(),
							$this->_feed->feedId,
							$this->_feed->feedTypeId
						),
					)
				);
			}
		}

		/**
		 * Start the feed update process in the background
		 *
		 * @param string $feed_id   The id of the feed that needs to be updated.
		 */
		private function activate_feed_file_update( $feed_id ) {
			// Save the queue data and then run the wppfm-background-process dispatch function.
			$this->_background_process->save( $this->_feed->feedId )->dispatch( $feed_id );
		}

		/**
		 * Set all class properties
		 *
		 * @return bool
		 */
		private function set_properties() {
			// Some channels do not use channels and leave the main category empty which causes issues.
			if ( function_exists('channel_uses_own_category') && ! channel_uses_own_category( $this->_feed->channel ) ) {
				$this->_feed->mainCategory = 'No Category Required';
			}

			// Some channels only accept category id numbers, for these channels retrieve the category numbers.
			if ( stripos( strrev( $this->_feed->mainCategory ), ')' ) === 0 ) {
				$start                     = stripos( $this->_feed->mainCategory, '(' ) + 1;
				$end                       = stripos( $this->_feed->mainCategory, '(' ) - $start;
				$this->_feed->mainCategory = substr( $this->_feed->mainCategory, $start, $end );
			}

			// instantiate the correct channel class.
			$this->_channel_class = new WPPFM_Google_Feed_Class();
			return true;
		}

		/**
		 * Returns the header that is correct for the selected feed type
		 *
		 * @return string
		 */
		private function get_feed_header() {
			$header_string = '';

			if ( $this->_feed->channel ) {
				$file_extension = function_exists( 'get_file_type' ) ? get_file_type( $this->_feed->channel ) : 'xml';
				if ( '1' === $this->_feed->channel && ! empty( $this->_feed->feedTitle ) ) {
					$header_string = $this->_channel_class->header( $this->_feed->feedTitle, $this->_feed->feedDescription );
				} elseif ( 'xml' === $file_extension ) {
					$header_string = $this->_channel_class->header( $this->_feed->title );
				} elseif ( 'txt' === $file_extension ) {
					$txt_sep = apply_filters( 'wppfm_txt_separator', get_correct_txt_separator( $this->_feed->channel ) );
					$header_string = $this->make_feed_string_from_data_array( $this->get_active_fields(), $txt_sep );
				} elseif ( 'csv' === $file_extension ) {
					$csv_sep = apply_filters( 'wppfm_csv_separator', get_correct_csv_header_separator( $this->_feed->channel ) );
					$string  = $this->make_custom_header_string( $this->get_active_fields(), $csv_sep );

					$header_string = $this->_channel_class->header( $string );
				} elseif ( 'tsv' === $file_extension ) {
					$string  = $this->make_custom_header_string( $this->get_active_fields(), "\t" );
					$header_string = $this->_channel_class->header( $string );
				}
			}

			return apply_filters( 'wppfm_header_string', $header_string, $this->_feed->feedId, $this->_feed->feedTypeId );
		}

		/**
		 * Sets the activity status of a specific attribute to true or false depending on its level.
		 * ALERT! has a javascript equivalent in channel-functions.js called setAttributeStatus().
		 *
		 * @param int    $field_level   The level of the field.
		 * @param string $field_value   The value of the field.
		 *
		 * @return boolean
		 */
		protected function set_attribute_status( $field_level, $field_value ) {
			if ( $field_level > 0 && $field_level < 3 ) {
				return true;
			}
			$clean_field_value = trim( $field_value );
			if ( ! empty( $clean_field_value ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Produces an array with the ids of all products that should be added into the feed
		 *
		 * @return array with ids
		 */
		private function get_product_ids_for_feed() {
			$queries_class = new WPPFM_Queries();

			$selected_categories = apply_filters( 'wppfm_selected_categories', $this->make_category_selection_string(), $this->_feed->feedId );

			$include_variations = '1' === $this->_feed->includeVariations;

			$products = $queries_class->get_post_ids( $selected_categories, $include_variations );

			array_filter( $products ); // Just to make sure, remove all empty elements.

			return apply_filters( 'wppfm_products_in_feed_queue', $products, $this->_feed->feedId );
		}

		/**
		 * Returns a comma separated string with selected category numbers to be used as part of a query
		 *
		 * @return string
		 */
		private function make_category_selection_string() {
			$category_selection_string = '';
			$category_mapping          = json_decode( $this->_feed->categoryMapping );

			if ( ! empty( $category_mapping ) ) {
				foreach ( $category_mapping as $category ) {
					$category_selection_string .= $category->shopCategoryId . ', '; // phpcs:ignore
				}
			}

			return $category_selection_string ? substr( $category_selection_string, 0, - 2 ) : '';
		}

		/**
		 * Get all general data required to make a feed
		 *
		 * @return array
		 */
		private function get_required_pre_data() {
			// Get the feed query string if the user has added to filter out specific products from the feed (Paid version only).
			$feed_filter = $this->_data_class->get_filter_query( $this->_feed->feedId );

			// Should the feed include product variations?
			$include_variations = '1' === $this->_feed->includeVariations;

			// Get an array with all the field names that are required to make the feed (including the source fields, fields for the queries and fields for static data).
			$required_column_names = $this->get_column_names_required_for_feed( $feed_filter );

			// Get the fields that are active and have to go into the feed.
			$active_fields = $this->get_active_fields();

			$database_fields = $this->get_database_fields( $required_column_names );

			return array(
				'filters'         => $feed_filter,
				'include_vars'    => $include_variations,
				'column_names'    => $required_column_names,
				'active_fields'   => $active_fields,
				'database_fields' => $database_fields,
			);
		}

		/**
		 * Get category name and description name from the active channel
		 *
		 * @return array
		 */
		private function get_channel_details() {
			return function_exists( 'channel_file_text_data' ) ? channel_file_text_data( $this->_feed->channel ) :
				array(
					'channel_id'       => $this->_feed->channel,
					'category_name'    => 'google_product_category',
					'description_name' => 'description',
				);
		}

		/**
		 * Returns the column names from the database that are required to get the data necessary to make the feed.
		 *
		 * @param object $feed_filter_object    The feed filter object.
		 *
		 * @return array
		 */
		private function get_column_names_required_for_feed( $feed_filter_object ) {
			$support_class = new WPPFM_Feed_Support();

			$fields         = array();
			$filter_columns = $support_class->get_column_names_from_feed_filter_array( $feed_filter_object );

			foreach ( $this->_feed->attributes as $attribute ) {
				if ( 'category_mapping' !== $attribute->fieldName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$column_names = $this->get_db_column_name_from_attribute( $attribute );
					foreach ( $column_names as $name ) {
						if ( ! empty( $name ) ) {
							array_push( $fields, $name );
						}
					}
				}
			}

			$result = array_unique( array_merge( $fields, $filter_columns ) ); // Remove doubles.

			if ( empty( $result ) ) {
				wppfm_write_log_file( 'Function get_column_names_required_for_feed returned zero columns' );
			}

			return array_merge( $result ); // And resort the result before returning.
		}

		/**
		 * Returns all active column names that are stored in the feed attributes.
		 *
		 * @param array $attribute  The attributes array.
		 *
		 * @return array
		 */
		public function get_db_column_name_from_attribute( $attribute ) {
			$column_names = array();

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			if ( property_exists( $attribute, 'isActive' ) && $attribute->isActive ) { // Only select the active attributes.
				// Source columns.
				if ( ! empty( $attribute->value ) ) {
					$source_columns    = $this->get_source_columns_from_attribute_value( $attribute->value );
					$condition_columns = $this->get_condition_columns_from_attribute_value( $attribute->value );
					$query_columns     = $this->get_queries_columns_from_attribute_value( $attribute->value );

					// TODO: I think the first $column_names array can be removed from the array_merge.
					$column_names = array_merge( $column_names, $source_columns, $condition_columns, $query_columns );
				}

				// Advised sources.
				if ( ! empty( $attribute->advisedSource ) // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					&& strpos( $attribute->advisedSource, __( 'Fill with a static value', 'wp-product-feed-manager' ) ) === false // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					&& strpos( $attribute->advisedSource, __( 'Use the settings in the Merchant Center', 'wp-product-feed-manager' ) ) === false ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					// Add the relevant advised sources.
					array_push( $column_names, $attribute->advisedSource ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				} elseif ( property_exists( $attribute, 'advisedSource' )
					&& strpos( $attribute->advisedSource, __( 'Use the settings in the Merchant Center', 'wp-product-feed-manager' ) ) !== false ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					array_push( $column_names, 'woo_shipping' );
				}
			}

			return $column_names;
		}

		/**
		 * Extract the active fields from the attributes.
		 *
		 * @return array
		 */
		private function get_active_fields() {
			$active_fields = array();

			foreach ( $this->_feed->attributes as $attribute ) {
				if ( $attribute->isActive && 'category_mapping' !== $attribute->fieldName ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					$push = false;

					if ( '1' === $attribute->fieldLevel ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						$push = true;
					} else {
						$value_object = property_exists( $attribute, 'value' ) ? json_decode( $attribute->value ) : new stdClass();

						if ( ! empty( $attribute->value ) && property_exists( $value_object, 'm' ) && property_exists( $value_object->m[0], 's' ) ) {
							$push = true;
						} elseif ( ! empty( $attribute->advisedSource ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
							$push = true;
						} elseif ( ! empty( $attribute->value ) && property_exists( $value_object, 't' ) ) {
							$push = true;
						} elseif ( ! empty( $attribute->value ) && property_exists( $value_object, 'v' ) ) {
							$push = true;
						}
					}

					if ( true === $push ) {
						array_push( $active_fields, $attribute->fieldName ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}
				}
			}

			if ( empty( $active_fields ) ) {
				wppfm_write_log_file( 'Function get_active_fields returned zero fields.' );
			}

			return $active_fields;
		}

		/**
		 * Gather all required column names from the database.
		 *
		 * @param array $active_field_names     Array with the names of the active fields.
		 *
		 * @return array
		 */
		private function get_database_fields( $active_field_names ) {
			$queries_class = new WPPFM_Queries();

			$post_fields                      = array();
			$meta_fields                      = array();
			$custom_fields                    = array();
			$active_custom_fields             = array();
			$active_third_party_custom_fields = array();
			$post_columns_string              = '';

			$columns_in_post_table     = $queries_class->get_columns_from_post_table(); // Get all post table column names.
			$all_custom_columns        = $queries_class->get_custom_product_attributes(); // Get all custom name labels.
			$third_party_custom_fields = $this->_data_class->get_third_party_custom_fields();

			// Convert the query results to an array with only the name labels.
			foreach ( $columns_in_post_table as $column ) {
				array_push( $post_fields, $column->Field ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			} // $post_fields containing the required names from the post table.
			foreach ( $all_custom_columns as $custom ) {
				array_push( $custom_fields, $custom->attribute_name );
			} // $custom_fields containing the custom names.
			// Filter the post columns, the meta columns and the custom columns to only those that are actually in use.

			foreach ( $active_field_names as $column ) {
				if ( in_array( $column, $post_fields, true ) && 'ID' !== $column ) { // Because ID is always required, it's excluded here and hard coded in the query.
					$post_columns_string .= $column . ', '; // Here a string is required to push in the query.
				} elseif ( in_array( $column, $custom_fields, true ) ) {
					array_push( $active_custom_fields, $column );
				} elseif ( in_array( $column, $third_party_custom_fields, true ) ) {
					array_push( $active_third_party_custom_fields, $column );
				} else {
					array_push( $meta_fields, $column );
				}
			}

			return array(
				'post_column_string'        => $post_columns_string,
				'meta_fields'               => $meta_fields,
				'active_custom_fields'      => $active_custom_fields,
				'third_party_custom_fields' => $third_party_custom_fields,
			);
		}

		/**
		 * Header text, override this function in the class-feed.php if required for a channel specific header.
		 *
		 * @param string $title     Title string.
		 *
		 * @return string
		 */
		protected function header( $title ) {
			return apply_filters( 'wppfm_xml_header', $title );
		}

		/**
		 * Footer text, override if required for a channel specific footer.
		 *
		 * @return string
		 */
		protected function footer() {
			return apply_filters( 'wppfm_xml_footer', '</products></rss>' );
		}
	}

	// End of WPPFM_Feed_Master_Class.

endif;
