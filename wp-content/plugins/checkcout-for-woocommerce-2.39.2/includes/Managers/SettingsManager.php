<?php
/**
 * Provides standard object for accessing user-defined plugin settings
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Managers
 * @author Brandon Tassone <brandontassone@gmail.com>
 */


namespace Objectiv\Plugins\Checkout\Managers;

/**
 * Class SettingsManager
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Managers
 * @author Clifton Griffin <clif@checkoutwc.com>
 */
class SettingsManager extends \WordPress_SimpleSettings {

	var $prefix = '_cfw_';

	/**
	 * SettingsManager constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		// Silence is golden
	}

	public function add_setting( $setting = false, $value, $keys = array() ) {
		if ( $setting === false ) {
			return false;
		}

		$suffix = '';

		if ( ! empty( $keys ) ) {
			asort( $keys );

			$suffix = '_' . join( '', $keys );
		}

		if ( ! isset( $this->settings[ $setting . $suffix ] ) ) {
			return $this->update_setting( $setting . $suffix, $value );
		} else {
			return false;
		}
	}

	public function update_setting( $setting = false, $value, $keys = array() ) {
		$suffix = '';

		if ( ! empty( $keys ) ) {
			asort( $keys );

			$suffix = '_' . join( '', $keys );
		}

		return parent::update_setting( $setting . $suffix, $value );
	}

	public function delete_setting( $setting = false, $keys = array() ) {
		$suffix = '';
		if ( ! empty( $keys ) ) {
			asort( $keys );

			$suffix = '_' . join( '', $keys );
		}

		return parent::delete_setting( $setting . $suffix );
	}

	function get_setting( $setting = false, $keys = array() ) {
		$suffix = '';

		if ( ! empty( $keys ) ) {
			asort( $keys );

			$suffix = '_' . join( '', $keys );
		}

		return parent::get_setting( $setting . $suffix, 'string' );
	}

	public function get_field_name( $setting, $keys = array() ) {
		$suffix = '';

		if ( ! empty( $keys ) ) {
			asort( $keys );

			$suffix = '_' . join( '', $keys );
		}

		return parent::get_field_name( $setting . $suffix, 'string' );
	}
}
