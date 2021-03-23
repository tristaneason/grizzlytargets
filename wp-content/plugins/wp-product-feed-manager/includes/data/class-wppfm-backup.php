<?php

/**
 * WPPFM Backup Class.
 *
 * @package WP Product Feed Manager/Data/Classes
 * @version 1.0.0
 */

if ( ! defined('ABSPATH') )
{
	exit;
}

if ( ! class_exists('WPPFM_Backup') ) :

	class WPPFM_Backup {

		private $_wpdb;

		/**
		 * @var string placeholder containing the wp table prefix
		 */
		private $_table_prefix;

		/**
		 * WPPFM_Queries Constructor
		 */
		public function __construct() {
			// get global WordPress database functions
			global $wpdb;

			// assign the global wpdb to a variable
			$this->_wpdb = &$wpdb;

			// assign the wp table prefix to a variable
			$this->_table_prefix = $this->_wpdb->prefix;
		}

		/**
		 * Reads the data from all plugin related tables ands stores the data in a sql like string
		 * The return string contains a timestamp, the database version, the option settings and the table content
		 *
		 * @return string sql like string with the backup data
		 * @since 1.7.0
		 */
		public function read_full_backup_data() {
			$main_table    = $this->_table_prefix . 'feedmanager_product_feed';
			$meta_table    = $this->_table_prefix . 'feedmanager_product_feedmeta';
			$channel_table = $this->_table_prefix . 'feedmanager_channel';

			$main_table_columns    = $this->_wpdb->get_col("DESC {$main_table}", 0);
			$meta_table_columns    = $this->_wpdb->get_col("DESC {$meta_table}", 0);
			$channel_table_columns = $this->_wpdb->get_col("DESC {$channel_table}", 0);

			$main_table_content    = $this->make_table_backup_string($this->_wpdb->get_results("SELECT * FROM $main_table", ARRAY_N), $main_table_columns);
			$meta_table_content    = $this->make_table_backup_string($this->_wpdb->get_results("SELECT * FROM $meta_table", ARRAY_N), $meta_table_columns);
			$channel_table_content = $this->make_table_backup_string($this->_wpdb->get_results("SELECT * FROM $channel_table", ARRAY_N), $channel_table_columns);

			$db_version                 = get_option('wppfm_db_version');
			$ftp_passive                = 'inactive';
			$auto_fix                   = get_option('wppfm_auto_feed_fix', 'false');
			$third_party_attributes     = get_option('wppfm_third_party_attribute_keywords', '%wpmr%,%cpf%,%unit%,%bto%,%yoast%');
			$disabled_background_mode   = get_option('wppfm_disabled_background_mode', 'false'); // @since 2.0.7
			$process_logger_option      = get_option('wppfm_process_logger_status', 'false'); // @since 2.9.0
			$show_pi_option             = get_option('wppfm_show_product_identifiers', 'false'); // @since 2.10.0
			$wpml_url_resolution_option = get_option('wppfm_use_full_url_resolution', 'false'); // @since 2.15.0
			$sep_string                 = '# backup string for database ->';
			$time_stamp                 = current_time('timestamp');

			$table_content = "$time_stamp#$db_version#$ftp_passive#$auto_fix#$third_party_attributes#$disabled_background_mode#$process_logger_option#$show_pi_option#$wpml_url_resolution_option";
			$table_content .= "$sep_string $main_table # <- # $main_table_content ";
			$table_content .= "$sep_string $meta_table # <- # $meta_table_content ";
			$table_content .= "$sep_string $channel_table # <- # $channel_table_content";

			return $table_content;
		}

		/**
		 * Restores the data in the database tables
		 *
		 * @param array $table_queries
		 *
		 * @return boolean
		 * @since 1.7.0
		 *
		 */
		public function restore_backup_data($table_queries) {
			// retrieve the initial data strings
			$product_feed_table_data     = explode(' # ', $table_queries[0][1]);
			$product_feedmeta_table_data = explode(' # ', $table_queries[1][1]);
			$channel_table_data          = explode(' # ', $table_queries[2][1]);

			// table names
			$main_table    = $this->_table_prefix . 'feedmanager_product_feed';
			$meta_table    = $this->_table_prefix . 'feedmanager_product_feedmeta';
			$channel_table = $this->_table_prefix . 'feedmanager_channel';

			// clear the current data
			$this->_wpdb->query("TRUNCATE TABLE $main_table");
			$this->_wpdb->query("TRUNCATE TABLE $meta_table");
			$this->_wpdb->query("TRUNCATE TABLE $channel_table");

			// get the columns
			$product_feed_table_columns     = explode(', ', $product_feed_table_data[0]);
			$product_feedmeta_table_columns = explode(', ', $product_feedmeta_table_data[0]);
			$channel_table_columns          = explode(', ', $channel_table_data[0]);

			// get the data
			$product_feed_table_queries     = explode(PHP_EOL, $product_feed_table_data[1]);
			$product_feedmeta_table_queries = explode(PHP_EOL, $product_feedmeta_table_data[1]);
			$channel_table_queries          = explode(PHP_EOL, $channel_table_data[1]);

			// restore the feedmanager_product_feed table
			foreach ( $product_feed_table_queries as $table_data )
			{
				$product_feed_data = explode("\t", $table_data);

				if ( count($product_feed_table_columns) === count($product_feed_data) )
				{
					$data = array();

					for ( $i = 0; $i < count($product_feed_data); $i ++ )
					{
						$data[$product_feed_table_columns[$i]] = $product_feed_data[$i];
					}

					$this->_wpdb->replace($main_table, $data);
				}
			}

			// restore the feedmanager_product_feedmeta table
			foreach ( $product_feedmeta_table_queries as $table_metadata )
			{
				$product_feed_metadata = explode("\t", $table_metadata);

				if ( count($product_feedmeta_table_columns) === count($product_feed_metadata) )
				{
					$data = array();

					for ( $i = 0; $i < count($product_feed_metadata); $i ++ )
					{
						$data[$product_feedmeta_table_columns[$i]] = $product_feed_metadata[$i];
					}

					$this->_wpdb->replace($meta_table, $data);
				}
			}

			// restore the feedmanager_channel table
			foreach ( $channel_table_queries as $table_channeldata )
			{
				$channel_data = explode("\t", $table_channeldata);

				if ( count($channel_table_columns) === count($channel_data) )
				{
					$data = array();

					for ( $i = 0; $i < count($channel_data); $i ++ )
					{
						$data[$channel_table_columns[$i]] = $channel_data[$i];
					}

					$this->_wpdb->replace($channel_table, $data);
				}
			}

			return TRUE;
		}

		/**
		 * Returns a tab separated string with the query results.
		 *
		 * @param array $query_result
		 * @param array $columns
		 *
		 * @return string backup string.
		 */
		private function make_table_backup_string($query_result, $columns) {
			$string = implode(', ', $columns) . ' # ';

			foreach ( $query_result as $row )
			{
				$string .= implode("\t", $row) . "\r\n";
			}

			return $string;
		}
	}

	// end of WPPFM_Backup class

endif;
