<?php

namespace Objectiv\BoosterSeat\Base;

/**
 * Enforces a single instance of an object. Useful for mission critical objects that should never be duplicated beyond
 * plugin initialization
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Base
 * @author Brandon Tassone <brandon@objectiv.co>
 */

abstract class Singleton {

	/**
	 * @since 1.0.0
	 * @access private
	 * @var null
	 */
	protected static $instance = array();

	/**
	 * Singleton constructor. Just a stub. Do not fill with logic
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {}

	/**
	 * Clone method. Just a stub. Do not fill with logic
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __clone() {}

	/**
	 * Wakeup method. Just a stub. Do not fill with logic
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __wakeup() {}

	/**
	 * Returns the class instantiated instance. Will return the first instance generated, and nothing else.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return null|static
	 */
	final public static function instance() {
		$class = (string) get_called_class();

		if (!array_key_exists($class, self::$instance)) {
			self::$instance[$class] = new static(...func_get_args());
		}

		return self::$instance[ $class ];
	}
}
