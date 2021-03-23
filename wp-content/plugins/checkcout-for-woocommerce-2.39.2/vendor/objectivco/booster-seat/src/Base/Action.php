<?php

namespace Objectiv\BoosterSeat\Base;

/**
 * Class Action
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\BoosterSeat\Base
 * @author Brandon Tassone <brandon@objectiv.co>
 */
abstract class Action extends Tracked {
	/**
	 * @since 1.0.6
	 * @access private
	 * @var bool
	 */
	private $no_privilege;

	/**
	 * @since 1.0.6
	 * @access private
	 * @var string
	 */
	private $action_prefix;

	/**
	 * Action constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param $id
	 * @param bool $no_privilege
	 * @param string $action_prefix
	 */
	public function __construct( $id, $no_privilege = true, $action_prefix = 'wp_ajax_' ) {
		$this->no_privilege  = $no_privilege;
		$this->action_prefix = $action_prefix;

		parent::__construct( $id );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param boolean $np
	 */
	public function load() {
		remove_all_actions( "{$this->action_prefix}{$this->get_id()}" );
		add_action( "{$this->action_prefix}{$this->get_id()}", array( $this, 'action' ) );

		if ( $this->no_privilege === true ) {
			remove_all_actions( "{$this->action_prefix}nopriv_{$this->get_id()}" );
			add_action( "{$this->action_prefix}nopriv_{$this->get_id()}", array( $this, 'action' ) );
		}
	}

	/**
	 * @since 1.0.0
	 * @access protected
	 * @param $out
	 */
	protected function out( $out ) {
		echo json_encode( $out, JSON_FORCE_OBJECT );
		wp_die();
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @return mixed
	 */
	abstract public function action();
}
