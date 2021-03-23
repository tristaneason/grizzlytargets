<?php

/**
 * WP Ajax Data Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 1.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Ajax_Data' ) ) :

	/**
	 * Ajax Data Class
	 */
	class WPPFM_Ajax_Data extends WPPFM_Ajax_Calls {

		public function __construct() {
			parent::__construct();

			$this->_queries = new WPPFM_Queries();
			$this->_files   = new WPPFM_File();

			// hooks
			add_action( 'wp_ajax_myajax-get-list-of-feeds', array( $this, 'myajax_get_list_of_feeds' ) );
			add_action( 'wp_ajax_myajax-get-list-of-backups', array( $this, 'myajax_get_list_of_backups' ) );
			add_action( 'wp_ajax_myajax-get-settings-options', array( $this, 'myajax_get_settings_options' ) );
			add_action( 'wp_ajax_myajax-get-output-fields', array( $this, 'myajax_get_output_fields' ) );
			add_action( 'wp_ajax_myajax-get-input-fields', array( $this, 'myajax_get_input_fields' ) );
			add_action( 'wp_ajax_myajax-get-feed-data', array( $this, 'myajax_get_feed_data' ) );
			add_action( 'wp_ajax_myajax-get-feed-status', array( $this, 'myajax_get_feed_status' ) );
			add_action( 'wp_ajax_myajax-get-main-feed-filters', array( $this, 'myajax_get_feed_filters' ) );
			add_action( 'wp_ajax_myajax-switch-feed-status', array( $this, 'myajax_switch_feed_status_between_hold_and_ok' ) );
			add_action( 'wp_ajax_myajax-duplicate-existing-feed', array( $this, 'myajax_duplicate_feed_data' ) );
			add_action( 'wp_ajax_myajax-update-feed-data', array( $this, 'myajax_update_feed_data' ) );
			add_action( 'wp_ajax_myajax-delete-feed', array( $this, 'myajax_delete_feed' ) );
			add_action( 'wp_ajax_myajax-backup-current-data', array( $this, 'myajax_backup_current_data' ) );
			add_action( 'wp_ajax_myajax-delete-backup-file', array( $this, 'myajax_delete_backup_file' ) );
			add_action( 'wp_ajax_myajax-restore-backup-file', array( $this, 'myajax_restore_backup_file' ) );
			add_action( 'wp_ajax_myajax-duplicate-backup-file', array( $this, 'myajax_duplicate_backup_file' ) );
			add_action( 'wp_ajax_myajax-get-next-feed-in-queue', array( $this, 'myajax_get_next_feed_in_queue' ) );
			add_action( 'wp_ajax_myajax-register-notice-dismission', array( $this, 'myajax_register_notice_dismission' ) );
		}

		/**
		 * Returns a list of all active feeds to an ajax caller
		 */
		public function myajax_get_list_of_feeds() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'postFeedsListNonce' ), 'myajax-post-feeds-list-nonce' ) ) {
				$list = $this->_queries->get_feeds_list();

				// @since 2.1.0 due to implementation of i18n to the plugin and for backwards compatibility we need to change
				// the status string entries from the database to identification strings (i.e. OK to ok and On hold in on_hold)
				if ( $list && ! ctype_lower( $list[0]->status ) ) {
					wppfm_correct_old_feeds_list_status( $list );
				}

				$this->convert_type_numbers_to_text( $list );

				// add information about the wppfm_special_feeds_add_on_active filter to the feed list.
				$result = array(
					'list'                        => $list,
					'special_feed_add_ons_active' => apply_filters( 'wppfm_special_feeds_add_on_active', false ),
				);

				echo wp_json_encode( $result );
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Returns a list of backups the user has made
		 */
		public function myajax_get_list_of_backups() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'postBackupListNonce' ), 'myajax-backups-list-nonce' ) ) {
				echo wp_json_encode( $this->_files->make_list_of_active_backups() );
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		public function myajax_get_settings_options() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'postSetupOptionsNonce' ), 'myajax-setting-options-nonce' ) ) {
				$options = [
					get_option( 'wppfm_auto_feed_fix' ),
					get_option( 'wppfm_third_party_attribute_keywords' ),
					get_option( 'wppfm_notice_mailaddress' ),
					get_option( 'wppfm_disabled_background_mode' ),
				];
				echo wp_json_encode( $options );
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		/**
		 * Retrieves the output fields that are specific for a given merchant and
		 * also adds stored meta data to the output fields
		 *
		 * @access public (ajax triggered)
		 */
		public function myajax_get_output_fields() {

			// check: if the call is safe
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'outputFieldsNonce' ), 'myajax-output-fields-nonce' ) ) {
				$data_class = new WPPFM_Data();

				// get the posted inputs
				$channel_id = filter_input( INPUT_POST, 'channelId' );
				$feed_id    = filter_input( INPUT_POST, 'feedId' );
				$channel    = trim( $this->_queries->get_channel_short_name_from_db( $channel_id ) );
				$is_custom  = function_exists( 'wppfm_channel_is_custom_channel' ) ? wppfm_channel_is_custom_channel( $channel_id ) : false;

				if ( ! $is_custom ) {
					// read the output fields
					$outputs = $this->_files->get_output_fields_for_specific_channel( $channel );

					// if the feed is a stored feed, look for meta data to add (a feed an id of -1 is a new feed that not yet has been saved)
					if ( $feed_id >= 0 ) {
						// add meta data to the feeds output fields
						$outputs = $data_class->fill_output_fields_with_metadata( $feed_id, $outputs );
					}
				} else {
					$data_class = new WPPFM_Data();
					$outputs    = $data_class->get_custom_fields_with_metadata( $feed_id );
				}

				echo wp_json_encode( $outputs );
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Gets all the different source fields from the custom products and third party sources and combines them into one list
		 *
		 * @access public (ajax triggered)
		 */
		public function myajax_get_input_fields() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'inputFieldsNonce' ), 'myajax-input-fields-nonce' ) ) {
				$source_id = filter_input( INPUT_POST, 'sourceId' );

				switch ( $source_id ) {
					case '1':
						$data_class = new WPPFM_Data();

						$custom_product_attributes = $this->_queries->get_custom_product_attributes();
						$custom_product_fields     = $this->_queries->get_custom_product_fields();
						$product_attributes        = $this->_queries->get_all_product_attributes();
						$product_taxonomies        = get_taxonomies();
						$third_party_custom_fields = $data_class->get_third_party_custom_fields();

						$all_source_fields = $this->combine_custom_attributes_and_feeds(
							$custom_product_attributes,
							$custom_product_fields,
							$product_attributes,
							$product_taxonomies,
							$third_party_custom_fields
						);

						echo wp_json_encode( apply_filters( 'wppfm_all_source_fields', $all_source_fields ) );
						break;

					default:
						if ( 'valid' === get_option( 'wppfm_lic_status' ) ) { // error message for paid versions
							echo '<div id="error">' . __(
								'Could not add custom fields because I could not identify the channel.
									If not already done add the correct channel in the Manage Channels page.
									Also try to deactivate and then activate the plugin.',
								'wp-product-feed-manager'
							) . '</div>';

							wppfm_write_log_file( sprintf( 'Could not define the channel in a valid Premium plugin version. Feed id = %s', $source_id ) );
						} else { // error message for free version
							echo '<div id="error">' . __(
								'Could not identify the channel.
								Try to deactivate and then activate the plugin.
								If that does not work remove the plugin through the WordPress Plugins page and than reinstall and activate it again.',
								'wp-product-feed-manager'
							) . '</div>';

							wppfm_write_log_file( sprintf( 'Could not define the channel in a free plugin version. Feed id = %s', $source_id ) );
						}

						break;
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_get_feed_filters() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'inputFeedFiltersNonce' ), 'myajax-feed-filters-nonce' ) ) {
				$feed_id = filter_input( INPUT_POST, 'feedId' );

				$data_class = new WPPFM_Data();
				$filters    = $data_class->get_filter_query( $feed_id );

				echo $filters ? wp_json_encode( $filters ) : '0';
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_get_feed_data() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'feedDataNonce' ), 'myajax-feed-data-nonce' ) ) {
				$feed_id   = filter_input( INPUT_POST, 'sourceId' );
				$feed_data = $this->_queries->read_feed( $feed_id );

				echo wp_json_encode( $feed_data );
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		public function myajax_get_feed_status() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'feedStatusNonce' ), 'myajax-feed-status-nonce' ) ) {
				$feed_id = filter_input( INPUT_POST, 'sourceId' );

				$feed_master = new WPPFM_Feed_Master_Class( $feed_id );
				$feed_data   = $feed_master->feed_status_check( $feed_id );

				echo wp_json_encode( $feed_data );
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		public function myajax_update_feed_data() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'updateFeedDataNonce' ), 'myajax-update-feed-data-nonce' ) ) {
				// Gget the posted feed data.
				$ajax_feed_data = json_decode( filter_input( INPUT_POST, 'feed' ) );
				$feed_filter    = filter_input( INPUT_POST, 'feedFilter' );
				$m_data         = filter_input( INPUT_POST, 'metaData' );

				echo WPPFM_Feed_CRUD_Handler::create_or_update_feed_data( $ajax_feed_data, $m_data, $feed_filter );
			}

			exit;
		}

		public function myajax_switch_feed_status_between_hold_and_ok() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'switchFeedStatusNonce' ), 'myajax-switch-feed-status-nonce' ) ) {
				$feed_id = filter_input( INPUT_POST, 'feedId' );

				$feed_status    = $this->_queries->get_current_feed_status( $feed_id );
				$current_status = $feed_status[0]->status_id;

				$new_status = '1' === $current_status ? '2' : '1'; // only allow status 1 or 2

				$result = $this->_queries->switch_feed_status( $feed_id, $new_status );

				echo ( false === $result ) ? $current_status : $new_status;
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_duplicate_feed_data() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'duplicateFeedNonce' ), 'myajax-duplicate-existing-feed-nonce' ) ) {
				$feed_id = filter_input( INPUT_POST, 'feedId' );

				echo WPPFM_Db_Management::duplicate_feed( $feed_id );
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_delete_feed() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'deleteFeedNonce' ), 'myajax-delete-feed-nonce' ) ) {
				$feed_id = filter_input( INPUT_POST, 'feedId' );

				// only return results when the user is an admin with manage options
				if ( is_admin() ) {
					WPPFM_Feed_Controller::remove_id_from_feed_queue( $feed_id );
					$this->_queries->delete_meta( $feed_id );
					echo $this->_queries->delete_feed( $feed_id );
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_backup_current_data() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'backupNonce' ), 'myajax-backup-nonce' ) ) {
				// only take action when the user is an admin with manage options
				if ( is_admin() ) {
					$backup_file_name = filter_input( INPUT_POST, 'fileName' );
					echo WPPFM_Db_Management::backup_database_tables( $backup_file_name );
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_delete_backup_file() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'deleteBackupNonce' ), 'myajax-delete-backup-nonce' ) ) {
				// only take action when the user is an admin with manage options
				if ( is_admin() ) {
					$backup_file_name = filter_input( INPUT_POST, 'fileName' );
					WPPFM_Db_Management::delete_backup_file( $backup_file_name );
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_restore_backup_file() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'restoreBackupNonce' ), 'myajax-restore-backup-nonce' ) ) {
				// only take action when the user is an admin with manage options
				if ( is_admin() ) {
					$backup_file_name = filter_input( INPUT_POST, 'fileName' );
					echo WPPFM_Db_Management::restore_backup( $backup_file_name );
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_duplicate_backup_file() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'duplicateBackupNonce' ), 'myajax-duplicate-backup-nonce' ) ) {
				// only take action when the user is an admin with manage options
				if ( is_admin() ) {
					$backup_file_name = filter_input( INPUT_POST, 'fileName' );
					WPPFM_Db_Management::duplicate_backup_file( $backup_file_name );
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_get_next_feed_in_queue() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'nextFeedInQueueNonce' ), 'myajax-next-feed-in-queue-nonce' ) ) {
				$next_feed_id = WPPFM_Feed_Controller::get_next_id_from_feed_queue();
				echo false !== $next_feed_id ? $next_feed_id : 'false';
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		public function myajax_register_notice_dismission() {
			if ( $this->safe_ajax_call( filter_input( INPUT_POST, 'noticeDismissionNonce' ), 'myajax-duplicate-backup-nonce' ) ) {

				// only take action when the user is an admin with manage options
				if ( is_admin() ) {
					update_option( 'wppfm_license_notice_suppressed', true );
					echo 'true';
				} else {
					echo 'false';
				}
			}

			// IMPORTANT: don't forget to exit
			exit;
		}

		private function combine_custom_attributes_and_feeds( $attributes, $feeds, $product_attributes, $product_taxonomies, $third_party_fields ) {
			$prev_dup_array = array(); // used to prevent doubles

			foreach ( $feeds as $feed ) {
				$obj = new stdClass();

				$obj->attribute_name  = $feed;
				$obj->attribute_label = $feed;

				array_push( $attributes, $obj );
				array_push( $prev_dup_array, $obj->attribute_label );
			}

			foreach ( $product_taxonomies as $taxonomy ) {
				if ( ! in_array( $taxonomy, $prev_dup_array ) ) {
					$obj                  = new stdClass();
					$obj->attribute_name  = $taxonomy;
					$obj->attribute_label = $taxonomy;

					array_push( $attributes, $obj );
					array_push( $prev_dup_array, $taxonomy );
				}
			}

			foreach ( $product_attributes as $attribute_string ) {
				$attribute_object = maybe_unserialize( $attribute_string->meta_value );

				if ( $attribute_object && ( is_object( $attribute_object ) || is_array( $attribute_object ) ) ) {
					foreach ( $attribute_object as $attribute ) {
						if ( ! in_array( $attribute['name'], $prev_dup_array ) ) {
							$obj                  = new stdClass();
							$obj->attribute_name  = $attribute['name'];
							$obj->attribute_label = $attribute['name'];

							array_push( $attributes, $obj );
							array_push( $prev_dup_array, $attribute['name'] );
						}
					}
				} else {
					if ( $attribute_object ) {
						wppfm_write_log_file( $attribute_object, 'debug' );
					}
				}
			}

			foreach ( $third_party_fields as $field_label ) {
				if ( ! in_array( $field_label, $prev_dup_array ) ) {
					$obj                  = new stdClass();
					$obj->attribute_name  = $field_label;
					$obj->attribute_label = $field_label;

					array_push( $attributes, $obj );
					array_push( $prev_dup_array, $field_label );
				}
			}

			return $attributes;
		}

		private function convert_type_numbers_to_text( &$list ) {
			$feed_types = wppfm_list_feed_type_text();

			foreach ( $list as $feed ) {
				$feed->feed_type_name = $feed_types[ $feed->feed_type_id ];
			}
		}
	}

	// end of WPPFM_Ajax_Data_Class

endif;

$my_ajax_data_class = new WPPFM_Ajax_Data();
