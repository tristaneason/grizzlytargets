<?php

/**
 * WP Channels Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 1.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_Channel' ) ) :

	/**
	 * Channel Class
	 */
	class WPPFM_Channel {
		/**
		 * Placeholder for the Channel Classes
		 *
		 * @var string
		 */
		private $_channels;

		public function __construct() {
			// WPPFM_CHANNEL_RELATED
			$this->_channels = array(
				new Wppfm_Channel_Info( '0', 'usersetup', 'Free User Setup' ),
				new Wppfm_Channel_Info( '1', 'google', 'Google Merchant Centre' ),
				new Wppfm_Channel_Info( '2', 'bing', 'Bing Merchant Centre' ),
				new Wppfm_Channel_Info( '3', 'beslis', 'Beslis.nl' ),
				new Wppfm_Channel_Info( '4', 'pricegrabber', 'PriceGrabber' ),
				new Wppfm_Channel_Info( '5', 'shopping', 'Shopping.com (eBay)' ),
				new Wppfm_Channel_Info( '6', 'amazon', 'Amazon product ads' ),
				new Wppfm_Channel_Info( '7', 'connexity', 'Connexity' ),
				new Wppfm_Channel_Info( '8', 'become', 'Become' ),
				// Become has been taken over by Connexity, https://merchants.become.com/DataFeedSpecification.html links to Connexity
				new Wppfm_Channel_Info( '9', 'nextag', 'Nextag' ),
				new Wppfm_Channel_Info( '10', 'kieskeurig', 'Kieskeurig.nl' ),
				new Wppfm_Channel_Info( '11', 'vergelijk', 'Vergelijk.nl' ),
				new Wppfm_Channel_Info( '12', 'koopjespakker', 'Koopjespakker.nl' ),
				new Wppfm_Channel_Info( '13', 'avantlink', 'AvantLink' ),
				new Wppfm_Channel_Info( '14', 'zbozi', 'Zbozi' ),
				new Wppfm_Channel_Info( '15', 'comcon', 'Commerce Connector' ),
				new Wppfm_Channel_Info( '16', 'facebook', 'Facebook' ),
				new Wppfm_Channel_Info( '17', 'bol', 'Bol.com' ),
				new Wppfm_Channel_Info( '18', 'adtraction', 'Adtraction' ),
				new Wppfm_Channel_Info( '19', 'ricardo', 'Ricardo.ch' ),
				new Wppfm_Channel_Info( '20', 'ebay', 'eBay' ),
				new Wppfm_Channel_Info( '21', 'shopzilla', 'Shopzilla' ),
				new Wppfm_Channel_Info( '22', 'converto', 'Converto' ),
				new Wppfm_Channel_Info( '23', 'idealo', 'Idealo' ),
				new Wppfm_Channel_Info( '24', 'heureka', 'Heureka' ),
				new Wppfm_Channel_Info( '25', 'pepperjam', 'Pepperjam' ),
				new Wppfm_Channel_Info( '26', 'galaxus_data', 'Galaxus Product Data' ),
				new Wppfm_Channel_Info( '27', 'galaxus_properties', 'Galaxus Product Properties' ),
				new Wppfm_Channel_Info( '28', 'galaxus_stock_pricing', 'Galaxus Product Stock Pricing' ),
				new Wppfm_Channel_Info( '29', 'vivino', 'Vivino' ),
				new Wppfm_Channel_Info( '996', 'marketingrobot_tsv', 'Custom TSV Export' ),
				new Wppfm_Channel_Info( '997', 'marketingrobot_txt', 'Custom TXT Export' ),
				new Wppfm_Channel_Info( '998', 'marketingrobot_csv', 'Custom CSV Export' ),
				new Wppfm_Channel_Info( '999', 'marketingrobot', 'Custom XML Export' ),
			);
		}

		/**
		 * Returns channel data from a specific channel
		 *
		 * @param string $channel_name channel name
		 *
		 * @return string Channel class
		 */
		public function get_active_channel_details( $channel_name ) {
			foreach ( $this->_channels as $channel ) {
				if ( $channel->channel_short === $channel_name ) {
					return $channel;
				}
			}

			return false;
		}

		public function get_channel_short_name( $channel_id ) {
			foreach ( $this->_channels as $channel ) {
				if ( $channel->channel_id === $channel_id ) {
					return $channel->channel_short;
				}
			}

			return false;
		}

		public function get_channel_name( $channel_id ) {
			foreach ( $this->_channels as $channel ) {
				if ( $channel->channel_id === $channel_id ) {
					return $channel->channel_name;
				}
			}

			return false;
		}

		public function get_installed_channel_names() {
			$file_class = new WPPFM_File();

			return $file_class->get_installed_channels_from_file();
		}

		public function remove_channel( $channel, $nonce ) {
			if ( wp_verify_nonce( $nonce, 'delete-channel-nonce' ) ) {
				$this->remove_channel_source( $channel );
			}
		}

		public function update_channel( $channel, $code, $nonce ) {
			if ( wp_verify_nonce( $nonce, 'update-channel-nonce' ) ) {
				$this->update_channel_source( $channel, $code );
			} else {
				wppfm_write_log_file( sprintf( 'Failed to update channel %s because then nonce was not accepted. Given nonce = %s', $channel, $nonce ) );
			}
		}

		public function install_channel( $channel, $code, $nonce ) {
			if ( wp_verify_nonce( $nonce, 'install-channel-nonce' ) ) {
				$this->install_channel_source( $channel, $code );
			} else {
				wppfm_write_log_file( sprintf( 'Failed to install channel %s because then nonce was not accepted. Given nonce = %s', $channel, $nonce ) );
			}
		}

		public function get_channels_from_server() {
			$url = trailingslashit( WPPFM_EDD_SL_STORE_URL ) . 'wpmr/channels/channels.php';

			$response = wp_remote_post(
				$url,
				array(
					'body' => array(
						'unique-site-id' => trim( get_option( 'wppfm_lic_key' ) ),
						'item_name'      => WPPFM_EDD_SL_ITEM_NAME,
					),
				)
			);

			// @since 2.3.0
			if ( is_wp_error( $response ) ) {
				wppfm_handle_wp_errors_response(
					$response,
					sprintf(
						/* translators: %s: url to the wp marketingrobot server */
						__(
							'2832 - Failed to connect with the wp marketingrobot server. Please wait a few minutes and try again. If the issue persists, open a support ticket at %s.',
							'wp-product-feed-manager'
						),
						WPPFM_SUPPORT_PAGE_URL
					)
				);
			}

			return $response;
		}

		public function get_number_of_updates_from_server( $channel_updated ) {
			if ( date( 'Ymd' ) === get_option( 'wppfm_channel_update_check_date' ) ) {
				if ( $channel_updated ) {
					wppfm_decrease_update_ready_channels();
				}

				return get_option( 'wppfm_channels_to_update' );
			} else {
				$response = $this->get_channels_from_server();

				if ( ! is_wp_error( $response ) ) {
					$available_channels = json_decode( $response['body'] );

					if ( $available_channels ) {
						$installed_channels_names = $this->get_installed_channel_names();

						$this->add_status_data_to_available_channels( $available_channels, $installed_channels_names, false );

						$stored_count = $this->count_updatable_channels( $available_channels );

						$count = $channel_updated ? ( $stored_count - 1 ) : $stored_count;
						update_option( 'wppfm_channels_to_update', $count > 0 ? $count : 0 );
						update_option( 'wppfm_channel_update_check_date', date( 'Ymd' ) );

						return $count;
					}
				} else {
					echo wppfm_handle_wp_errors_response(
						$response,
						sprintf(
							/* translators: %s: url to the support page */
							__(
								'2141 - Your WP Product Feed Manager plugin is unable to check for channel updates. This could be due to a temporary problem with our server. If this message does not clear in 15 minutes, please open a support ticket at %s for support on this issue.',
								'wp-product-feed-manager'
							),
							WPPFM_SUPPORT_PAGE_URL
						)
					);

					return false;
				}
			}

			return 0;
		}

		public function add_status_data_to_available_channels( &$available_channels, $installed_channels, $updated ) {
			for ( $i = 0; $i < count( $available_channels ); $i ++ ) {
				if ( in_array( $available_channels[ $i ]->short_name, $installed_channels ) ) {
					$available_channels[ $i ]->status = 'installed';

					$available_channels[ $i ]->installed_version = $available_channels[ $i ]->short_name === $updated ? $available_channels[ $i ]->version
						: $this->get_channel_file_version( $available_channels[ $i ]->short_name, 0 );
				} else {
					$available_channels[ $i ]->status            = 'not installed';
					$available_channels[ $i ]->installed_version = '0';
				}
			}
		}

		/**
		 * Returns the name of the channel for a specific feed.
		 *
		 * @param string $feed_id The feed id.
		 *
		 * @since 2.20.0
		 * @return string The name of the channel
		 */
		public function get_channel_name_from_feed_id( $feed_id ) {
			$queries_class = new WPPFM_Queries();

			$feed_data = $queries_class->get_feed_row( $feed_id );
			return $this->get_channel_name( $feed_data->channel_id );
		}

		/**
		 * Returns the short name of the channel for a specific feed.
		 *
		 * @param string $feed_id The feed id.
		 *
		 * @since 2.20.0
		 * @return string The short name of the channel
		 */
		public function get_channel_short_name_from_feed_id( $feed_id ) {
			$queries_class = new WPPFM_Queries();

			$feed_data = $queries_class->get_feed_row( $feed_id );
			return $this->get_channel_short_name( $feed_data->channel_id );
		}

		private function get_channel_file_version( $channel_name, $rerun_counter ) {
			if ( $rerun_counter < 3 ) {
				if ( class_exists( 'WPPFM_' . ucfirst( $channel_name ) . '_Feed_Class' ) ) {
					$class_var = 'WPPFM_' . ucfirst( $channel_name ) . '_Feed_Class';

					$channel_class = new $class_var();

					return $channel_class->get_version();
				} else {
					// reset the registered channels in the channel table
					$db_class = new WPPFM_Database_Management();
					$db_class->reset_channel_registration();

					include_channels(); // include the channel classes

					$rerun_counter ++;

					return $this->get_channel_file_version( $channel_name, $rerun_counter );
				}
			} else {
				if ( wppfm_on_any_own_plugin_page() ) {
					/* translators: %s: Name of a channel */
					echo wppfm_show_wp_error( sprintf( __( 'Channel %s is not installed correctly. Please try to Deactivate and then Activate the Feed Manager Plugin in your Plugins page.', 'wp-product-feed-manager' ), $channel_name ) );
					wppfm_write_log_file( sprintf( 'Error: Channel %s is not installed correctly.', $channel_name ) );
				}

				return 'unknown';
			}
		}

		private function count_updatable_channels( $channel_data ) {
			$counter = 0;

			foreach ( $channel_data as $channel ) {
				if ( 'installed' === $channel->status && ( $channel->version > $channel->installed_version ) ) {
					$counter ++;
				}
			}

			return $counter;
		}

		private function update_channel_source( $channel, $code ) {
			$file_class = new WPPFM_File();
			$ftp_class  = new WPPFM_Channel_FTP();

			// remove the out dated channel source files from the server
			$file_class->delete_channel_source_files( $channel );

			$get_result = $ftp_class->get_channel_source_files( $channel, $code );

			// get the update files from wp marketingrobot.com
			if ( false !== $get_result ) {
				// unzip the file
				$file_class->unzip_channel_file( $channel );

				// register the update
				wppfm_decrease_update_ready_channels();
			}
		}

		private function remove_channel_source( $channel_short ) {
			$data_class = new WPPFM_Data();
			$file_class = new WPPFM_File();

			// get the channel id that needs to be removed
			$channel_id = $data_class->get_channel_id_from_short_name( $channel_short );

			// unregister the channel
			wp_dequeue_script( 'wppfm_' . $channel_short . '-source-script' );

			if ( $channel_id ) {
				// remove channel related feed files
				$file_class->delete_channel_feed_files( $channel_id );

				// remove any channel related feed data and feed meta
				$data_class->delete_channel_feeds( $channel_id );
			}

			// remove the channel from the feedmanager_channel table
			$data_class->delete_channel( $channel_short );

			// remove the channel source files from the server
			$file_class->delete_channel_source_files( $channel_short );
		}

		private function install_channel_source( $channel_name, $code ) {
			$ftp_class  = new WPPFM_Channel_FTP();
			$file_class = new WPPFM_File();
			$data_class = new WPPFM_Data();

			if ( plugin_version_supports_channel( $channel_name ) ) {
				$get_result = $ftp_class->get_channel_source_files( $channel_name, $code );

				// get the update files from wp marketingrobot.com
				if ( false !== $get_result ) {

					// unzip the file
					$file_class->unzip_channel_file( $channel_name );

					// register the new channel
					$channel_details = $this->get_active_channel_details( $channel_name );

					if ( false !== $channel_details ) {
						$data_class->register_channel( $channel_name, $channel_details );
					} else {
						wppfm_write_log_file( sprintf( 'Unable to register channel %s' . $channel_name ) );
					}
				} else {
					wppfm_write_log_file(
						sprintf(
							'Could not get the %s channel file from the server. Get_result message is %s.',
							$channel_name,
							$get_result
						)
					);
				}
			} else {
				echo wppfm_show_wp_warning(
					sprintf(
						/* translators: %s: Name of the selected channel */
						__(
							'Channel %s is not supported by your current plugin version. Please update your plugin to the latest version and try uploading this channel again.',
							'wp-product-feed-manager'
						),
						$channel_name
					),
					'wp-product-feed-manager'
				);
			}
		}
	}

	// end of WPPFM_Channel class

	class Wppfm_Channel_Info {
		public $channel_id;
		public $channel_short;
		public $channel_name;

		public function __construct( $id, $short, $name ) {
			$this->channel_id    = $id;
			$this->channel_short = $short;
			$this->channel_name  = $name;
		}
	}

	// end of Wppfm_Channel_Info class
endif;
