<?php

namespace Objectiv\BoosterSeat\Base;

/**
 * Class Tracked
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Base
 * @author Brandon Tassone <brandon@objectiv.co>
 */
abstract class Tracked {

	/**
	 * @since 1.0.0
	 * @access protected
	 * @var string $id
	 */
	protected $id = '';

	/**
	 * @since 1.0.0
	 * @access protected
	 * @var array $i
	 */
	protected static $i = array();

	/**
	 * CFW constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param $id
	 */
	public function __construct( $id ) {
		$this->set_id( $id );

		self::add_i( $this );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public function __destruct() {
		self::remove_i( $this );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param $i
	 */
	public static function add_i( $i ) {
		$i_with_id = array_filter(
			self::$i, function( $instance ) use ( $i ) {
				return $instance->get_id() == $i->get_id();
			}
		);

		if ( empty( $i_with_id ) ) {
			self::$i[] = $i;
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param Tracked | string $identifier
	 */
	public static function remove_i( $identifier ) {
		foreach ( self::$i as $key => $i ) {
			$found = false;

			// If its a string check by the id. If its an object, compare it to another instance
			if ( is_string( $identifier ) ) {
				if ( $i->get_id() == $identifier ) {
					$found = true;
				}
			} else {
				if ( $i == $identifier ) {
					$found = true;
				}
			}

			// If found, unset the item
			if ( $found ) {
				unset( self::$i[ $key ] );
			}
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param string $id
	 * @return array
	 */
	public static function get_i( $id = '' ) {
		$ret = self::$i;

		if ( $id != '' ) {
			$ret = array_filter(
				self::$i, function( $instance ) use ( $id ) {
					return $instance->get_id() == $id;
				}
			);
		}

		return $ret;
	}
}
