<?php

namespace Objectiv\BoosterSeat\Utilities;

use Objectiv\BoosterSeat\Managers\PathManager;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Utilities
 * @author Brandon Tassone <brandon@objectiv.co>
 */

class Activator {

	/**
	 * @var array The plugin list and error handlers/handles for said plugins
	 */
	private $plugins_to_check_for_activation;

	/**
	 * Activator constructor.
	 *
	 * @param $plugins_to_check
	 */
	public function __construct( $plugins_to_check ) {
		$this->set_plugins_to_check_for_activation( $plugins_to_check );
	}

	/**
	 * Method to be run on plugin activation.
	 *
	 * Place the plugin dependncy checks in relevantly named functions
	 *
	 * @since 1.0.0
	 * @access public
	 * @return boolean
	 */
	public function activate() {

		$plugins = $this->get_plugins_to_check_for_activation();
		$errors  = 0;

		foreach ( $plugins as $plugin_file_location => $plugin_error_info ) {
			// The notice name to look up in the options table
			$notice_name = $plugin_error_info[0];

			// The error information if the plugin is indeed not activated
			$error_info = $plugin_error_info[1];

			// If the plugin isn't active assign the error info the array.
			if ( ! is_plugin_active( $plugin_file_location ) ) {
				add_option( $notice_name, [ $error_info ] );
				$errors++;
			}
		}

		// Return true / false based on if 0 or more than 0 errors
		return $errors != 0;
	}

	/**
	 * Method to be run on unsuccessful plugin activation. The function that generates the error admin notice for plugin
	 * activation
	 *
	 * @since 1.0.0
	 * @access public
	 * @param PathManager $path_manager
	 * @param string $text_domain
	 */
	public function activate_admin_notice( $path_manager, $text_domain = '' ) {

		foreach ( $this->get_plugins_to_check_for_activation() as $plugin ) {
			$notice_name = $plugin[0];

			$activation_error = get_option( $notice_name );

			if ( ! empty( $activation_error ) ) {

				// Get rid of "Plugin Activated" message on error.
				unset( $_GET['activate'] );

				foreach ( $activation_error as $error ) {
					if ( ! $error['success'] ) {
						// Print the error notice
						printf( "<div class='%s'><p>%s</p></div>", $error['class'], __( $error['message'], $text_domain ) );
					}
				}

				// Remove the option after all error messages displayed
				delete_option( $notice_name );

				// Deactivate the plugin
				deactivate_plugins( $path_manager->get_path_main_file() );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_plugins_to_check_for_activation() {
		return $this->plugins_to_check_for_activation;
	}

	/**
	 * @param mixed $plugins_to_check_for_activation
	 */
	public function set_plugins_to_check_for_activation( $plugins_to_check_for_activation ) {
		$this->plugins_to_check_for_activation = $plugins_to_check_for_activation;
	}
}
