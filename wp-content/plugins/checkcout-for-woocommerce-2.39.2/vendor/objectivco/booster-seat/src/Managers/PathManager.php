<?php

namespace Objectiv\BoosterSeat\Managers;

/**
 * Manages plugin related path information.
 *
 * This class is mainly used in the template manager and other classes related to plugin setup and file management
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Managers
 * @author Brandon Tassone <brandon@objectiv.co>
 */

class PathManager {
	/**
	 * @since 0.0.1
	 * @access private
	 * @var string The base path to the plugin
	 */
	private $base;

	/**
	 * @since 0.0.1
	 * @access private
	 * @var string The url base path to the plugin
	 */
	private $url_base;

	/**
	 * @since 0.0.1
	 * @access private
	 * @var string The main file name that initiates the plugin
	 */
	private $main_file;

	/**
	 * @since 0.0.1
	 * @access private
	 * @var string The raw file name
	 */
	private $raw_file;

	/**
	 * PathManager constructor.
	 *
	 * @since 0.0.1
	 * @access public
	 * @param string $base The plugin base path
	 * @param string $url_base The plugin url base path
	 * @param string $main_file The main plugin file
	 */
	public function __construct( $base, $url_base, $main_file ) {
		$this->base      = $base;
		$this->url_base  = $url_base;
		$this->main_file = basename( $main_file ); // TODO: Rename main_file to main_file_basename
		$this->raw_file  = $main_file;
	}

	/**
	 * Get raw file
	 *
	 * @since 0.0.1
	 * @return string
	 */
	public function get_raw_file() {
		return $this->raw_file;
	}

	/**
	 * Set raw file
	 *
	 * @since 0.0.1
	 * @param string $raw_file
	 */
	public function set_raw_file( $raw_file ) {
		$this->raw_file = $raw_file;
	}

	/**
	 * Return the base plugin path
	 *
	 * @since 0.0.1
	 * @access public
	 * @return string
	 */
	public function get_base() {
		return $this->base;
	}

	/**
	 * Return the main file name
	 *
	 * @since 0.0.1
	 * @access public
	 * @return string
	 */
	public function get_main_file() {
		return $this->main_file;
	}

	/**
	 * Returns the concatenated folder name with the main file name in one strong
	 *
	 * @since 0.0.1
	 * @access public
	 * @return string Returns the concatenated folder name with the main file name in one strong
	 */
	public function get_path_main_file() {
		return $this->base . $this->main_file;
	}

	/**
	 * Returns the value of variable url_base
	 *
	 * @since 0.0.1
	 * @access public
	 * @return string The url base path
	 */
	public function get_url_base() {
		return $this->url_base;
	}
}
