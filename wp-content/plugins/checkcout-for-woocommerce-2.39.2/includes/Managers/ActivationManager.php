<?php

namespace Objectiv\Plugins\Checkout\Managers;

/**
 * Class ActivationManager
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Managers
 * @author Clifton Griffin <clif@checkoutwc.com>
 */
class ActivationManager {
	private $checks = [];

	public function __construct( $checks ) {
		$this->checks = $checks;
	}

	public function activate() {
		$errors = 0;

		foreach( $this->checks as $check ) {
			if ( ! function_exists($check['function']) || ! $check['function']( $check['value'] ) ) {
				add_option($check['id'], [ $check['message'] ]);
				$errors++;
			}
		}

		return $errors != 0;
	}

	/**
	 * Method to be run on unsuccessful plugin activation. The function that generates the error admin notice for plugin
	 * activation
	 *
	 * @since 1.0.0
	 * @access public
	 * @param ExtendedPathManager $path_manager
	 */
	public function activate_admin_notice( $path_manager ) {

		foreach ( $this->checks as $check ) {
			$notice_name = $check['id'];

			$activation_error = get_option( $notice_name );

			if ( ! empty( $activation_error ) ) {

				// Get rid of "Plugin Activated" message on error.
				unset( $_GET['activate'] );

				foreach ( $activation_error as $error ) {
					if ( ! $error['success'] ) {
						// Print the error notice
						printf( "<div class='%s'><p>%s</p></div>", $error['class'], $error['message'] );
					}
				}

				// Remove the option after all error messages displayed
				delete_option( $notice_name );

				// Deactivate the plugin
				deactivate_plugins( $path_manager->get_path_main_file() );
			}
		}
	}
}