<?php

/**
 * WP Product Feed Manager i18n Scripts Class.
 *
 * @package WP Product Feed Manager/User Interface/Classes
 * @since 2.2.0
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPPFM_i18n_Scripts' ) ) :

	/**
	 * Internationalisation Class
	 */
	class WPPFM_i18n_Scripts {

		/**
		 * Localizes the javascript strings that are used on the feed settings pages
		 *
		 * @return string translated text
		 */
		public static function wppfm_feed_settings_i18n() {
			$pars = array(
				'prohibited_feed_name_characters'  => esc_html__( 'You are using characters in your file name that are not allowed!', 'wp-product-feed-manager' ),
				'feed_name_exists'                 => esc_html__( 'You already have a feed with this name! Please use another name.', 'wp-product-feed-manager' ),
				'invalid_url'                      => esc_html__( 'The url you entered is invalid. Please try again', 'wp-product-feed-manager' ),
				'save_data_failed'                 => esc_html__( 'Saving the data to the database has failed! Please try again.', 'wp-product-feed-manager' ),
				'no_category_required'             => esc_html__( 'no category required', 'wp-product-feed-manager' ),
				'no_feed_generated'                => esc_html__( 'no feed generated', 'wp-product-feed-manager' ),
				'feed_started'                     => esc_html__( 'Started processing your feed in the background.', 'wp-product-feed-manager' ),
				'feed_queued'                      => esc_html__( 'Pushed the feed into the background queue. Processing starts after all other feeds are processed.', 'wp-product-feed-manager' ),
				'feed_writing_error'               => esc_html__( 'Error writing the feed. You do not have the correct authorities to write the file.', 'wp-product-feed-manager' ),
				'feed_initiation_error'            => esc_html__( 'Error generating the feed. Feed generation initialization failed. Please check your error logs for more information about the issue.', 'wp-product-feed-manager' ),
				/* translators: %xmlResult%: a string containing the error message */
				'feed_general_error'               => esc_html__( 'Generating the feed has failed! Error return code = %xmlResult%', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'feed_status_unknown'              => esc_html__( 'The status of feed %feedname% is unknown.', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'feed_status_ready'                => esc_html__( 'Product feed %feedname% is now ready. It contains %prodnr% products.', 'wp-product-feed-manager' ),
				'feed_status_still_processing'     => esc_html__( 'Still processing the feed in the background. You can wait for it to finish, but you can also close this form if you want.', 'wp-product-feed-manager' ),
				'feed_status_added_to_queue'       => esc_html__( 'This feed has been added to the feed queue and will be processed when it is next.', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'feed_status_error'                => esc_html__( 'Product feed %feedname% has some errors!', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'feed_status_failed'               => esc_html__( 'Product feed %feedname% has failed!', 'wp-product-feed-manager' ),
				'variation_only_for_premium'       => esc_html__( 'The option to add product variations to the feed is not available in the free version. Unlock this option by upgrading to the Premium plugin. For more information goto https://www.wpmarketingrobot.com/.', 'wp-product-feed-manager' ),
				'select_a_sub_category'            => esc_html__( 'Select a sub-category', 'wp-product-feed-manager' ),
				'select_by_category_number'        => esc_html__( 'Select by category number', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'duplicated_field'                 => esc_html__( 'You already have a field %fieldname% defined!', 'wp-product-feed-manager' ),
				'select_all_source_fields_warning' => esc_html__( 'Make sure to select all source fields before adding a new one!', 'wp-product-feed-manager' ),
				'fill_current_condition_warning'   => esc_html__( 'Please fill in the current condition before adding a new one!', 'wp-product-feed-manager' ),
				'select_a_source_field_warning'    => esc_html__( 'Please select a source field first before you select the conditions.', 'wp-product-feed-manager' ),
				'select_a_valid_source_warning'    => esc_html__( 'Please select a valid source before adding a condition to that source.', 'wp-product-feed-manager' ),
				'advanced_filter_only_for_premium' => esc_html__( 'The Advanced Filter option is not available in the free version. Unlock the Advanced Filter option by upgrading to the Premium plugin. For more information goto https://www.wpmarketingrobot.com/.', 'wp-product-feed-manager' ),
				'all_products_except'              => esc_html__( 'except the ones where' ),
				'fill_filter_warning'              => esc_html__( 'Please fill in the filter values before adding a new one' ),
				'no_separator'                     => esc_html__( 'No separator', 'wp-product-feed-manager' ),
				'space'                            => esc_html__( 'space', 'wp-product-feed-manager' ),
				'comma'                            => esc_html__( 'comma', 'wp-product-feed-manager' ),
				'point'                            => esc_html__( 'point', 'wp-product-feed-manager' ),
				'semicolon'                        => esc_html__( 'semicolon', 'wp-product-feed-manager' ),
				'colon'                            => esc_html__( 'colon', 'wp-product-feed-manager' ),
				'dash'                             => esc_html__( 'dash', 'wp-product-feed-manager' ),
				'slash'                            => esc_html__( 'slash', 'wp-product-feed-manager' ),
				'backslash'                        => esc_html__( 'backslash', 'wp-product-feed-manager' ),
				'double_pipe'                      => esc_html__( 'double pipe', 'wp-product-feed-manager' ),
				'underscore'                       => esc_html__( 'underscore', 'wp-product-feed-manager' ),
				'other'                            => esc_html__( 'other', 'wp-product-feed-manager' ),
				/* translators: %other%: either the word "other" or an empty space */
				'all_other_products'               => esc_html__( 'for all %other% products', 'wp-product-feed-manager' ),
				'edit_values'                      => esc_html__( 'edit values', 'wp-product-feed-manager' ),
				'and_change_values'                => esc_html__( 'and change values', 'wp-product-feed-manager' ),
				'remove_value_editor'              => esc_html__( 'remove value editor', 'wp-product-feed-manager' ),
				'to'                               => esc_html__( 'to', 'wp-product-feed-manager' ),
				'with_element_name'                => esc_html__( 'with element name', 'wp-product-feed-manager' ),
				'defined_by_category_mapping_tble' => esc_html__( 'Defined by the Category Mapping Table.', 'wp-product-feed-manager' ),
				'use_advised_source'               => esc_html__( 'Use advised source', 'wp-product-feed-manager' ),
				'combined_source_fields'           => esc_html__( 'Combine source fields', 'wp-product-feed-manager' ),
				'category_mapping'                 => esc_html__( 'Category Mapping', 'wp-product-feed-manager' ),
				'select_a_source_field'            => esc_html__( 'Select a source field', 'wp-product-feed-manager' ),
				'fill_with_static_value'           => esc_html__( 'Fill with a static value', 'wp-product-feed-manager' ),
				'map_to_default_category'          => esc_html__( 'Map to Default Category', 'wp-product-feed-manager' ),
				'use_shop_category'                => esc_html__( 'Use Shop Category', 'wp-product-feed-manager' ),
				'an_empty_field'                   => esc_html__( 'an empty field', 'wp-product-feed-manager' ),
				'add_recommended_output'           => esc_html__( 'Add recommended output', 'wp-product-feed-manager' ),
				'add_optional_output'              => esc_html__( 'Add optional output', 'wp-product-feed-manager' ),
				'no_category_selected'             => esc_html__( 'You\'ve not selected a Shop Category in the Category Mapping Table. With no Shop Category selected, your feed will be empty. Are you sure you still want to save this feed?', 'wp-product-feed-manager' ),
				'file_name_required'               => esc_html__( 'A file name is required!', 'wp-product-feed-manager' ),
				'query_requirements'               => esc_html__( 'Add at least one query in the previous change value row before adding a new row.', 'wp-product-feed-manager' ),
				'first_fill_in_change_value'       => esc_html__( 'Please first fill in a change value option before adding a query to it.', 'wp-product-feed-manager' ),
			);

			self::add_general_words( $pars );

			wp_localize_script(
				'wppfm_feed-settings-script',
				'wppfm_feed_settings_form_vars',
				$pars
			);
		}

		/**
		 * Localizes the javascript strings that are used on the feed list pages
		 *
		 * @return string translated text
		 */
		public static function wppfm_list_table_i18n() {
			$pars = array(
				'processing_the_feed' => esc_html__( 'Processing the feed, please wait...', 'wp-product-feed-manager' ),
				'processing_failed'   => esc_html__( 'Processing failed, please try again', 'wp-product-feed-manager' ),
				'processing_queue'    => esc_html__( 'In processing queue', 'wp-product-feed-manager' ),
				'no_data_found'       => esc_html__( 'No data found', 'wp-product-feed-manager' ),
				'list_deactivate'     => esc_html__( 'Auto-off', 'wp-product-feed-manager' ),
				'list_activate'       => esc_html__( 'Auto-on', 'wp-product-feed-manager' ),
				'list_edit'           => esc_html__( 'Edit', 'wp-product-feed-manager' ),
				'list_view'           => esc_html__( 'View', 'wp-product-feed-manager' ),
				'other'               => esc_html__( 'Other', 'wp-product-feed-manager' ),
				'unknown_text'        => esc_html__( 'Unknown', 'wp-product-feed-manager' ),
				'on_hold'             => esc_html__( 'Ready (manual)', 'wp-product-feed-manager' ),
				'processing'          => esc_html__( 'Processing', 'wp-product-feed-manager' ),
				'has_errors'          => esc_html__( 'Has errors', 'wp-product-feed-manager' ),
				'failed_processing'   => esc_html__( 'Failed processing', 'wp-product-feed-manager' ),
				'status_ok'           => esc_html__( 'Ready (auto)', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'added_feed_copy'     => esc_html__( 'Added a copy of feed %feedname% to the list.', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'confirm_delete_feed' => esc_html__( 'Please confirm you want to delete feed %feedname%.', 'wp-product-feed-manager' ),
				/* translators: %feedname%: name of the feed */
				'feed_removed'        => esc_html__( 'Feed %feedname% removed from the server.', 'wp-product-feed-manager' ),
				'list_language'       => esc_html__( 'Feed Language', 'wp-product-feed-manager' ),
				'feed_not_generated'  => esc_html__( 'This feed does not yet exists, please (re)generate this feed first.', 'wp-product-feed-manager' ),
				'missing_channel'     => esc_html__( 'This feed requires the "%channelname%" Channel to be installed. Please open the Manage Channels page and install the "%channelname%" before regenerating this feed.', 'wp-product-feed-manager' ),
				'no_channel'          => esc_html__( 'Channel not installed', 'wp-product-feed-manager' ),
			);

			self::add_general_words( $pars );

			wp_localize_script(
				'wppfm_feed-list-script',
				'wppfm_feed_list_form_vars',
				$pars
			);
		}

		/**
		 * Localizes the javascript strings that are used on the settings page
		 *
		 * @return string translated text
		 */
		public static function wppfm_settings_i18n() {
			$pars = array(
				'first_enter_file_name'  => esc_html__( 'First enter a file name for the backup file.', 'wp-product-feed-manager' ),
				/* translators: %backup_file_name%: name of the backup file*/
				'confirm_file_deletion'  => esc_html__( 'Please confirm you want to delete backup %backup_file_name%.', 'wp-product-feed-manager' ),
				/* translators: %backup_file_name%: name of the backup file*/
				'file_deleted'           => esc_html__( '%backup_file_name% deleted.', 'wp-product-feed-manager' ),
				/* translators: %backup_file_name%: name of the backup file*/
				'confirm_file_restoring' => esc_html__( 'Are you sure you want to restore backup %backup_file_name%? This will overwrite your current settings and feed data!', 'wp-product-feed-manager' ),
				/* translators: %backup_file_name%: name of the backup file*/
				'file_restored'          => esc_html__( '%backup_file_name% restored', 'wp-product-feed-manager' ),
				/* translators: %backup_file_name%: name of the backup file*/
				'file_duplicated'        => esc_html__( '%backup_file_name% duplicated', 'wp-product-feed-manager' ),
			);

			wp_localize_script(
				'wppfm_setting-script',
				'wppfm_setting_form_vars',
				$pars
			);
		}

		/**
		 * Localizes the javascript strings that are used in the backup list
		 *
		 * @return string translated text
		 */
		public static function wppfm_backup_list_i18n() {
			$pars = array(
				'list_restore' => esc_html__( 'Restore', 'wp-product-feed-manager' ),
				'no_backup'    => esc_html__( 'No backup found', 'wp-product-feed-manager' ),
			);

			self::add_general_words( $pars );

			wp_localize_script(
				'wppfm_backup-list-script',
				'wppfm_backup_list_form_vars',
				$pars
			);
		}

		/**
		 * Localizes the javascript strings that are used on the manage channels page
		 *
		 * @return string translated text
		 */
		public static function wppfm_manage_channels_i18n() {
			$pars = array(
				'confirm_removing_channel' => esc_html__( 'Please confirm you want to remove this channel! Removing this channel will also remove all its feed files.', 'wp-product-feed-manager' ),
			);

			wp_localize_script(
				'wppfm_data-script',
				'wppfm_manage_channels_vars',
				$pars
			);
		}

		/**
		 * Adds localized words that are used on more than one page
		 *
		 * @param array $pars page specific words
		 *
		 * @return string translated text
		 */
		private static function add_general_words( &$pars ) {
			$pars['edit']                  = esc_html__( 'edit', 'wp-product-feed-manager' );
			$pars['select']                = esc_html__( 'select', 'wp-product-feed-manager' );
			$pars['selected']              = esc_html__( 'selected', 'wp-product-feed-manager' );
			$pars['delete']                = esc_html__( 'delete', 'wp-product-feed-manager' );
			$pars['remove']                = esc_html__( 'remove', 'wp-product-feed-manager' );
			$pars['add']                   = esc_html__( 'add', 'wp-product-feed-manager' );
			$pars['if_pref']               = esc_html__( 'if', 'wp-product-feed-manager' );
			$pars['or']                    = esc_html__( 'or', 'wp-product-feed-manager' );
			$pars['and']                   = esc_html__( 'and', 'wp-product-feed-manager' );
			$pars['all_products_included'] = esc_html__( 'All products from the selected Shop Categories will be included in the feed', 'wp-product-feed-manager' );
			$pars['list_duplicate']        = esc_html__( 'Duplicate', 'wp-product-feed-manager' );
			$pars['list_regenerate']       = esc_html__( 'Regenerate', 'wp-product-feed-manager' );
			$pars['list_delete']           = esc_html__( 'Delete', 'wp-product-feed-manager' );
			$pars['ok']                    = esc_html__( 'Ready (auto)', 'wp-product-feed-manager' );
		}

	}


	// end of WPPFM_i18n_Scripts class

endif;
