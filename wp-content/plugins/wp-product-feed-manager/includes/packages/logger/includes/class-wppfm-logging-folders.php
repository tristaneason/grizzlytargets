<?php

/* * ******************************************************************
 * Version 1.0
 * Package: Logger
 * Modified: 18-10-2019
 * Copyright 2019 Accentio. All rights reserved.
 * License: None
 * By: Michel Jongbloed
 * ****************************************************************** */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Folder Class
 */
class WPPFM_Logging_Folders {

	/**
	 * Checks if the logging folder exists and makes it if not
	 *
	 * @since 2.7.0
	 */
	public static function make_logs_folder() {
		if ( ! file_exists( WPPFM_LGR_LOGGINGS_DIR ) ) {
			wp_mkdir_p( WPPFM_LGR_LOGGINGS_DIR );
		}
	}

	/**
	 * Deletes a directory and all its content
	 *
	 * @since 2.7.0
	 * @param  string  $folder_name
	 * @return boolean true when the directory has been deleted
	 */
	public static function delete_folder( $folder_name ) {
		if ( is_dir( $folder_name ) ) {

			$dir_handle = opendir( $folder_name );

			if ( ! $dir_handle ) {
				return false;
			}

			while ( $file = readdir( $dir_handle ) ) {
				if ( '.' != $file && '.. ' != $file ) {
					if ( ! is_dir( $folder_name . '/' . $file ) ) {
						unlink( $folder_name . '/' . $file );
					} else {
						wp_mkdir_p( $folder_name . '/' . $file );
					}
				}
			}

			closedir( $dir_handle );
			rmdir( $folder_name );

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Checks if a folder is empty
	 *
	 * @since 2.7.0
	 *
	 * @param  string $folder name and path of the folder
	 * @return boolean returns true if folder is empty
	 */
	public static function folder_is_empty( $folder ) {
		if ( ! is_readable( $folder ) ) {
			return null;
		}

		$handle = opendir( $folder );

		while ( false !== ( $entry = readdir( $handle ) ) ) {
			if ( '.' != $entry && '..' != $entry ) {
				return false;
			}
		}

		return true;
	}
}

// end of WPPFM_Logging_Folders
