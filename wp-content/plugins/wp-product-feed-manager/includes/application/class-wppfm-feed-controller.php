<?php

/**
 * WP Product Feed Controller Class.
 *
 * @package WP Product Feed Manager/Application/Classes
 * @version 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Feed_Controller' ) ) :

	/**
	 * Feed Controller Class
	 *
	 * @since 1.10.0
	 */
	class WPPFM_Feed_Controller {

		/**
		 * Removes a feed id from the feed queue
		 *
		 * @param string $feed_id
		 */
		public static function remove_id_from_feed_queue( $feed_id ) {
			$feed_queue = self::get_feed_queue();
			$key        = array_search( $feed_id, $feed_queue );

			if ( false !== $key ) {
				unset( $feed_queue[ $key ] );
				$feed_queue = array_values( $feed_queue ); // resort after unset
				update_site_option( 'wppfm_feed_queue', $feed_queue );

				if ( self::feed_queue_is_empty() ) {
					wppfm_clear_feed_process_data();
				}
			}
		}

		/**
		 * Adds an feed id to the feed queue
		 *
		 * @param string $feed_id
		 */
		public static function add_id_to_feed_queue( $feed_id ) {
			$feed_queue = self::get_feed_queue();

			if ( ! in_array( $feed_id, $feed_queue ) ) {
				array_push( $feed_queue, $feed_id );
				update_site_option( 'wppfm_feed_queue', $feed_queue );
			}
		}

		/**
		 * Gets the next feed id from the feed queue
		 */
		public static function get_next_id_from_feed_queue() {
			$feed_queue = self::get_feed_queue();

			return count( $feed_queue ) > 0 ? $feed_queue[0] : false;
		}

		/**
		 * Empties the feed queue
		 */
		public static function clear_feed_queue() {
			update_site_option( 'wppfm_feed_queue', array() );
		}

		/**
		 * Checks if the feed queue is empty
		 *
		 * @return bool
		 */
		public static function feed_queue_is_empty() {
			$queue = self::get_feed_queue();

			return ( count( $queue ) < 1 ) ? true : false;
		}

		/**
		 * Returns the number of product ids that are still in the queue
		 *
		 * @since 2.3.0
		 * @return int number of product ids still in the queue
		 */
		public static function nr_ids_remaining_in_queue() {
			$queue = self::get_feed_queue();

			return count( $queue );
		}

		/**
		 * Sets the background_process_is_running option
		 *
		 * @param bool $set (default false)
		 */
		public static function set_feed_processing_flag( $set = false ) {
			$status = false !== $set ? 'true' : 'false';
			update_site_option( 'wppfm_background_process_is_running', $status );
		}

		/**
		 * Get the background_process_is_running status
		 *
		 * @return bool
		 */
		public static function feed_is_processing() {
			$status = get_option( 'wppfm_background_process_is_running', 'false' );

			return 'true' === $status;
		}

		/**
		 * Checks if a running feed size is still growing, in order to identify a failing feed process.
		 *
		 * @since 2.2.0.
		 *
		 * @param   string $feed_file   String with the full path and name of the feed file.
		 *
		 * @return  boolean False if the feed still grows, true if it stopped growing for a certain time.
		 */
		public static function feed_processing_failed( $feed_file ) {

			if ( '' === $feed_file ) {
				return null;
			}

			$trans = get_transient( 'wppfm_feed_file_size' );

			// Get the feed file name that's stored in the transient or take the $feed_file parameter.
			$trans_feed_file = $trans ? substr( $trans, strrpos( $trans, '|' ) + 1 ) : $feed_file;

			// if the transient was empty or the feed file in the transient is not the currently active file, reset the transient.
			if ( false === $trans || $feed_file !== $trans_feed_file ) {
				$trans = '0|0|' . $feed_file;
				set_transient( 'wppfm_feed_file_size', $trans, WPPFM_TRANSIENT_LIVE );
			}

			// Get the last data.
			$stored               = explode( '|', $trans );
			$prev_feed_size       = $stored[0];
			$prev_feed_time_stamp = $stored[1];
			$feed_file            = $trans_feed_file;
			$curr_feed_size       = file_exists( $feed_file ) ? filesize( $feed_file ) : false;

			// If file does not exist, return true.
			if ( false === $curr_feed_size ) {
				delete_transient( 'wppfm_feed_file_size' ); // Reset the counter.
				return true;
			}

			// If the size of the feed has not grown.
			if ( $curr_feed_size <= $prev_feed_size ) {
				// And the delay time has passed.
				if ( $prev_feed_time_stamp + apply_filters( 'wppfm_delay_failed_label', WPPFM_DELAY_FAILED_LABEL, $feed_file ) < time() ) {
					delete_transient( 'wppfm_feed_file_size' ); // Reset the counter.
					return true;
				} else {
					return false;
				}
			} else { // If the file size has increased, reset the timer and return false.
				set_transient( 'wppfm_feed_file_size', $curr_feed_size . '|' . time() . '|' . $feed_file, WPPFM_TRANSIENT_LIVE );
				return false;
			}
		}

		/**
		 * Updates the timer that is used as reference to monitor if a file is growing during the feed production process.
		 *
		 * @since 2.11.0
		 */
		public static function update_file_grow_monitoring_timer() {
			// Get the current monitor data.
			$grow_monitor_array = get_transient( 'wppfm_feed_file_size' );

			if ( ! $grow_monitor_array ) { // The wppfm_feed_file_size is not set in the non-background mode.
				return;
			}

			$grow_monitor_data = explode( '|', $grow_monitor_array );

			// Reset the timer part of the monitor.
			set_transient( 'wppfm_feed_file_size', $grow_monitor_data[0] . '|' . time() . '|' . $grow_monitor_data[2], WPPFM_TRANSIENT_LIVE );
		}

		/**
		 * Returns the current feed queue
		 *
		 * @return array with feed ids in the queue or an empty array
		 */
		protected static function get_feed_queue() {
			return get_site_option( 'wppfm_feed_queue', array() );
		}
	}

endif;
