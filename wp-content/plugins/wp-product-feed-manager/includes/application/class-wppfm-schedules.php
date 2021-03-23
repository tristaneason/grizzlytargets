<?php
/**
 * WP Product Schedules Class.
 *
 * @package WP Product Feed Manager/Application/Classes
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WPPFM_Schedules' ) ) :

	/**
	 * Feed Schedules Class
	 */
	class WPPFM_Schedules {

		/**
		 * Initiates the automatic feed updates
		 */
		public function update_active_feeds() {
			$data_class = new WPPFM_Data();

			$current_timestamp      = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
			$active_feeds_schedules = $data_class->get_schedule_data();
			$failed_feeds           = $data_class->get_failed_feeds();
			$active_feed_id         = null;

			// Update scheduled feeds.
			foreach ( $active_feeds_schedules as $schedule ) {
				$active_feed_id = $schedule['product_feed_id'];
				$update_time    = $this->new_activation_time( $schedule['updated'], $schedule['schedule'] );

				// Activate the feed update when the update time is reached.
				if ( $update_time < $current_timestamp ) {
					WPPFM_Feed_Controller::add_id_to_feed_queue( $schedule['product_feed_id'] );

					$data_class->update_feed_status( $schedule['product_feed_id'], 4 ); // Feed status to waiting in queue.
				}
			}

			// If there is no feed processing in progress and the feed queue is not empty, start updating the current feed.
			if ( ! WPPFM_Feed_Controller::feed_queue_is_empty() && ! WPPFM_Feed_Controller::feed_is_processing() ) {
				do_action( 'wppfm_automatic_feed_update_triggered', $active_feed_id );
				$feed_master_class = new WPPFM_Feed_Master_Class( $active_feed_id );
				$feed_master_class->update_feed_file( true );
			}

			// Update previously failed feeds.
			if ( 'true' === get_option( 'wppfm_auto_feed_fix' ) ) {
				foreach ( $failed_feeds as $failed_feed ) {
					WPPFM_Feed_Controller::add_id_to_feed_queue( $failed_feed['product_feed_id'] );

					// If there is no feed processing in progress, start updating the current feed.
					if ( ! WPPFM_Feed_Controller::feed_is_processing() ) {
						do_action( 'wppfm_automatic_feed_prepare_update_triggered', $active_feed_id );

						$feed_master_class = new WPPFM_Feed_Master_Class( $active_feed_id );
						$feed_master_class->update_feed_file( true );
					} else {
						$data_class->update_feed_status( $failed_feed['product_feed_id'], 4 ); // Feed status to waiting in queue.
					}
				}
			}
		}

		/**
		 * Returns the time at which the feed should be updated
		 *
		 * @param string $last_update       time string with the data and time the feed has been update last.
		 * @param string $update_frequency  registered update frequency.
		 *
		 * @return string Containing the time in Y-m-d H:i:s format
		 */
		private function new_activation_time( $last_update, $update_frequency ) {
			$update_split = explode( ':', $update_frequency );

			$hrs  = isset( $update_split[1] ) ? $update_split[1] : '00';
			$min  = isset( $update_split[2] ) ? $update_split[2] : '00';
			$freq = isset( $update_split[3] ) ? $update_split[3] : 1;

			$planned_update_time = $hrs . ':' . $min . ':00';
			$planned_update_time = '00:00:00' !== $planned_update_time ? $planned_update_time : '23:59:00';
			$last_update_time    = date( 'H:i:s', strtotime( $last_update ) );
			$days                = $update_split[0] <= 1
								&& ( ( strtotime( $last_update_time ) <= strtotime( $planned_update_time ) )
								|| ( '00' === $hrs && '00' === $min ) )
				? 0 : $update_split[0];

			if ( $freq < 2 ) { // Update only once a day, every $update_split[0] days.
				$update_date = date_add( date_create( date( 'Y-m-d', strtotime( $last_update ) ) ), date_interval_create_from_date_string( $days . ' days' ) );

				return date_format( $update_date, 'Y-m-d' ) . ' ' . $planned_update_time;
			} else { // Update more than once a day.
				$update_hrs  = $this->get_update_hours( $freq );
				$update_date = date_add( date_create( $last_update ), date_interval_create_from_date_string( $update_hrs . ' hours' ) );

				return date_format( $update_date, 'Y-m-d H:i' ) . ':00';
			}
		}

		/**
		 * Returns the daily update options
		 *
		 * @param string $selection     Selected number of hours.
		 *
		 * @return int Hours difference between updates
		 */
		private function get_update_hours( $selection ) {
			switch ( $selection ) {
				case '2':
					return 12;

				case '4':
					return 6;

				case '6':
					return 4;

				case '8':
					return 3;

				case '12':
					return 2;

				case '24':
					return 1;

				default:
					return 24;
			}
		}

	}

	// End of WPPFM_Schedules class.

endif;
