<?php

namespace Objectiv\BoosterSeat\Language;

use Objectiv\BoosterSeat\Managers\PathManager;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Language
 * @author Brandon Tassone <brandon@objectiv.co>
 */

class i18n {

	/**
	 * @var string The text domain of the plugin
	 */
	private $text_domain;

	/**
	 * @param $text_domain
	 */
	public function __construct( $text_domain ) {
		$this->set_text_domain( $text_domain );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param PathManager $path_manager
	 */
	public function load_plugin_textdomain( $path_manager ) {
		load_plugin_textdomain(
			$this->get_text_domain(),
			false,
			dirname( plugin_basename( $path_manager->get_path_main_file() ) ) . '/languages'
		);
	}

	/**
	 * @return mixed
	 */
	public function get_text_domain() {
		return $this->text_domain;
	}

	/**
	 * @param mixed $text_domain
	 */
	public function set_text_domain( $text_domain ) {
		$this->text_domain = $text_domain;
	}
}
