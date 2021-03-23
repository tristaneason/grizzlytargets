<?php

namespace Objectiv\Plugins\Checkout\Managers;

use Objectiv\Plugins\Checkout\Core\Loader;

/**
 * Class AjaxManager
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Managers
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class AjaxManager {

	/**
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $ajax_modules;

	/**
	 * @since 1.0.0
	 * @access protected
	 * @var Loader
	 */
	protected $loader;

	/**
	 * AjaxManager constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param array $ajax_modules
	 * @param Loader $loader
	 */
	public function __construct( $ajax_modules, $loader ) {
		$this->ajax_modules = $ajax_modules;
		$this->loader       = $loader;
	}

	/**
	 * Iterates through each ajax module and loads it (registers it) with the system
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function load_all() {
		foreach ( $this->ajax_modules as $ajax ) {
			$ajax->load();
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_ajax_modules() {
		return $this->ajax_modules;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @return mixed
	 */
	public function get_loader() {
		return $this->loader;
	}
}
