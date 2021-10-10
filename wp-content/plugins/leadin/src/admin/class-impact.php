<?php

namespace Leadin\admin;

const LEADIN_IMPACT_CODE = 'leadin_impact_code';
const IMPACT_BASE_URL    = 'https://hubspot.sjv.io/';
const IR_CLICK_ID        = 'irclickid';
const MPID               = 'mpid';

/**
 * Class containing the logic to get Impact affiliate information when necessary
 */
class Impact {
	/**
	 * Apply leadin_impact_code filter.
	 */
	public static function get_affiliate_link() {
		$link   = null;
		$params = self::get_params();

		$code = apply_filters( LEADIN_IMPACT_CODE, null );

		if ( ! empty( $code ) ) {
			$link = IMPACT_BASE_URL . $code;
		}

		return $link;
	}

	/**
	 * Get impact properties from query parameters.
	 */
	public static function get_params() {
		$params = array();

		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
		if ( isset( $_GET['leadin_irclickid'] ) ) {
			$params[ IR_CLICK_ID ] = \filter_var( \wp_unslash( $_GET['leadin_irclickid'] ), FILTER_SANITIZE_STRING );
		}

		if ( isset( $_GET['leadin_mpid'] ) ) {
			$params[ MPID ] = \filter_var( \wp_unslash( $_GET['leadin_mpid'] ), FILTER_SANITIZE_STRING );
		}
		// phpcs:enable

		return $params;
	}

	/**
	 * Return true if the function `get_params` returns both irclickid and mpid.
	 */
	public static function has_params() {
		return 2 === \count( self::get_params() );
	}
}
