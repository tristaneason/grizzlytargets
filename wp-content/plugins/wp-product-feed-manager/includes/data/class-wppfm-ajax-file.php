<?php
/**
 * WP Ajax File Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 2.9.0
 */

if ( ! defined('ABSPATH') )
{
	exit;
}

if ( ! class_exists('WPPFM_Ajax_File') ) :

	/**
	 * Ajax File Class
	 */
	class WPPFM_Ajax_File extends WPPFM_Ajax_Calls {

		/**
		 * WPPFM_Ajax_File constructor.
		 */
		public function __construct() {
			parent::__construct();

			// Add the hooks.
			add_action('wp_ajax_myajax-get-next-categories', array( $this, 'myajax_read_next_categories' ));
			add_action('wp_ajax_myajax-get-category-lists', array( $this, 'myajax_read_category_lists' ));
			add_action('wp_ajax_myajax-delete-feed-file', array( $this, 'myajax_delete_feed_file' ));
			add_action('wp_ajax_myajax-update-feed-file', array( $this, 'myajax_update_feed_file' ));
			add_action('wp_ajax_myajax-log-message', array( $this, 'myajax_log_message' ));
			add_action('wp_ajax_myajax-auto-feed-fix-mode-selection', array( $this, 'myajax_auto_feed_fix_mode_selection' ));
			add_action('wp_ajax_myajax-background-processing-mode-selection', array( $this, 'myajax_background_processing_mode_selection' ));
			add_action('wp_ajax_myajax-feed-logger-status-selection', array( $this, 'myajax_feed_logger_status_selection' ));
			add_action('wp_ajax_myajax-show-product-identifiers-selection', array( $this, 'myajax_show_product_identifiers_selection' ));
			add_action('wp_ajax_myajax-wpml-use-full-url-resolution-selection', array( $this, 'myajax_wpml_use_full_url_resolution_selection' ));
			add_action('wp_ajax_myajax-debug-mode-selection', array( $this, 'myajax_debut_mode_selection' ));
			add_action('wp_ajax_myajax-third-party-attribute-keywords', array( $this, 'myajax_set_third_party_attribute_keywords' ));
			add_action('wp_ajax_myajax-set-notice-mailaddress', array( $this, 'myajax_set_notice_mailaddress' ));
			add_action('wp_ajax_myajax-clear-feed-process-data', array( $this, 'myajax_clear_feed_process_data' ));
			add_action('wp_ajax_myajax-reinitiate-plugin', array( $this, 'myajax_reinitiate_plugin' ));
		}

		/**
		 * Returns the sub-categories from a selected category
		 */
		public function myajax_read_next_categories() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'nextCategoryNonce'), 'myajax-next-category-nonce') )
			{
				$file_class = new WPPFM_File();

				$channel_id      = filter_input(INPUT_POST, 'channelId');
				$requested_level = filter_input(INPUT_POST, 'requestedLevel');
				$parent_category = filter_input(INPUT_POST, 'parentCategory');
				$file_language   = filter_input(INPUT_POST, 'fileLanguage');
				$categories      = $file_class->get_categories_for_list($channel_id, $requested_level, $parent_category, $file_language);

				if ( ! is_array($categories) )
				{
					if ( '0' === substr($categories, - 1) )
					{
						chop($categories, '0');
					}
				}

				echo wp_json_encode($categories);
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Read the category list
		 */
		public function myajax_read_category_lists() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'categoryListsNonce'), 'myajax-category-lists-nonce') )
			{
				$file_class = new WPPFM_File();

				$channel_id             = filter_input(INPUT_POST, 'channelId');
				$main_categories_string = filter_input(INPUT_POST, 'mainCategories');
				$file_language          = filter_input(INPUT_POST, 'fileLanguage');
				$categories_array       = explode(' > ', $main_categories_string);
				$categories             = array();
				$required_levels        = count($categories_array) > 0 ? ( count($categories_array) + 1 ) : count($categories_array);

				for ( $i = 0; $i < $required_levels; $i ++ )
				{
					$parent_category = $i > 0 ? $categories_array[$i - 1] : '';
					$c               = $file_class->get_categories_for_list($channel_id, $i, $parent_category, $file_language);
					if ( $c )
					{
						array_push($categories, $c);
					}
				}

				echo wp_json_encode($categories);
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Delete a specific feed file
		 */
		public function myajax_delete_feed_file() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'deleteFeedNonce'), 'myajax-delete-feed-nonce') )
			{
				$file_name = filter_input(INPUT_POST, 'fileTitle');

				if ( file_exists(WP_PLUGIN_DIR . '/wp-product-feed-manager-support/feeds/' . $file_name) )
				{
					$file = WP_PLUGIN_DIR . '/wp-product-feed-manager-support/feeds/' . $file_name;
				} else
				{
					$file = WPPFM_FEEDS_DIR . '/' . $file_name;
				}

				// Only return results when the user is an admin with manage options.
				if ( is_admin() )
				{
					/* translators: %s: Title of the feed file */
					echo file_exists($file) ? unlink($file) : wppfm_show_wp_error(sprintf(__('Could not find file %s.', 'wp-product-feed-manager'), $file));
				} else
				{
					echo wppfm_show_wp_error(__('Error deleting the feed. You do not have the correct authorities to delete the file.', 'wp-product-feed-manager'));
				}
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * This function fetches the posted data and triggers the update of the feed file on the server.
		 */
		public function myajax_update_feed_file() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'updateFeedFileNonce'), 'myajax-update-feed-file-nonce') )
			{

				// Fetch the data from $_POST.
				$feed_id                  = filter_input(INPUT_POST, 'feedId');
				$background_mode_disabled = get_option('wppfm_disabled_background_mode', 'false');

				WPPFM_Feed_Controller::add_id_to_feed_queue($feed_id);

				// If there is no feed processing in progress, of background processing is switched off, start updating the current feed.
				if ( ! WPPFM_Feed_Controller::feed_is_processing() || 'true' === $background_mode_disabled )
				{
					do_action('wppfm_manual_feed_update_activated', $feed_id);

					$feed_master_class = new WPPFM_Feed_Master_Class($feed_id);
					$feed_master_class->update_feed_file(FALSE);
				} else
				{
					$data_class = new WPPFM_Data();
					$data_class->update_feed_status($feed_id, 4); // Feed status to waiting in queue.
					echo 'pushed_to_queue';
				}
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Logs a message from a javascript call to the server
		 */
		public function myajax_log_message() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'logMessageNonce'), 'myajax-log-message-nonce') )
			{
				// Fetch the data from $_POST.
				$message      = filter_input(INPUT_POST, 'messageList');
				$file_name    = filter_input(INPUT_POST, 'fileName');
				$text_message = wp_strip_all_tags($message);

				// Only return results when the user is an admin with manage options.
				if ( is_admin() )
				{
					wppfm_write_log_file($text_message, $file_name);
				} else
				{
					echo wppfm_show_wp_error(__('Error writing the feed. You do not have the correct authorities to write the file.', 'wp-product-feed-manager'));
				}
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the Auto Feed Fix setting from the Settings page
		 *
		 * @since 1.7.0
		 */
		public function myajax_auto_feed_fix_mode_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'updateAutoFeedFixNonce'), 'myajax-auto-feed-fix-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'fix_selection');
				update_option('wppfm_auto_feed_fix', $selection);

				echo get_option('wppfm_auto_feed_fix');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the Disable Background processing setting from the Settings page
		 *
		 * @since 2.0.7
		 */
		public function myajax_background_processing_mode_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'backgroundModeNonce'), 'myajax-background-mode-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'mode_selection');
				update_option('wppfm_disabled_background_mode', $selection);

				echo get_option('wppfm_disabled_background_mode');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the Feed Process Logger setting from the Settings page.
		 *
		 * @since 2.8.0
		 */
		public function myajax_feed_logger_status_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'feedLoggerStatusNonce'), 'myajax-logger-status-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'statusSelection');
				update_option('wppfm_process_logger_status', $selection);

				echo get_option('wppfm_process_logger_status');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the Show Product Identifiers setting from the Settings page.
		 *
		 * @since 2.10.0
		 */
		public function myajax_show_product_identifiers_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'showPINonce'), 'myajax-show-pi-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'showPiSelection');
				update_option('wppfm_show_product_identifiers', $selection);

				echo get_option('wppfm_show_product_identifiers');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the WPML Use full resolution URLs setting from the Settings page.
		 *
		 * @since 2.15.0
		 */
		public function myajax_wpml_use_full_url_resolution_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'urlResolutionNonce'), 'myajax-use-full-url-resolution-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'urlResolutionSelection');
				update_option('wppfm_use_full_url_resolution', $selection);

				echo get_option('wppfm_use_full_url_resolution');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Changes the Debug setting from the Settings page
		 *
		 * @since 1.9.0
		 */
		public function myajax_debug_mode_selection() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'debugNonce'), 'myajax-debug-nonce') )
			{
				$selection = filter_input(INPUT_POST, 'debug_selection');
				update_option('wppfm_debug_mode', $selection);

				echo get_option('wppfm_debug_mode');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		public function myajax_set_third_party_attribute_keywords() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'thirdPartyKeywordsNonce'), 'myajax-set-third-party-keywords-nonce') )
			{
				$keywords = filter_input(INPUT_POST, 'keywords');
				update_option('wppfm_third_party_attribute_keywords', $keywords);

				echo get_option('wppfm_third_party_attribute_keywords');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		public function myajax_set_notice_mailaddress() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'noticeMailaddressNonce'), 'myajax-set-notice-mailaddress-nonce') )
			{
				$mailaddress = filter_input(INPUT_POST, 'mailaddress');
				update_option('wppfm_notice_mailaddress', $mailaddress);

				echo get_option('wppfm_notice_mailaddress');
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Re-initiates the plugin, updates the database and loads all cron jobs
		 *
		 * @since 1.9.0
		 */
		public function myajax_reinitiate_plugin() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'reInitiateNonce'), 'myajax-reinitiate-nonce') )
			{

				if ( wppfm_reinitiate_plugin() )
				{
					echo 'Plugin re-initiated';
				} else
				{
					echo 'Re-initiation failed!';
				}
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}

		/**
		 * Clears all option data that is related to the feed processing
		 *
		 * @since 1.10.0
		 */
		public function myajax_clear_feed_process_data() {
			// Make sure this call is legal.
			if ( $this->safe_ajax_call(filter_input(INPUT_POST, 'clearFeedNonce'), 'myajax-clear-feed-nonce') )
			{

				if ( wppfm_clear_feed_process_data() )
				{
					echo __('Feed processing data cleared', 'wp-product-feed-manager');
				} else
				{
					/* translators: clearing the feed data failed */
					echo __('Clearing failed!', 'wp-product-feed-manager');
				}
			}

			// IMPORTANT: don't forget to exit.
			exit;
		}
	}

	// End of WPPFM_Ajax_File_Class.

endif;

$myajax_file_class = new WPPFM_Ajax_File();
