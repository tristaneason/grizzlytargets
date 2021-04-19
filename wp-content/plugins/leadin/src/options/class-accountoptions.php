<?php

namespace Leadin\options;

use Leadin\options\LeadinOptions;

/**
 * Class that wraps the functions to access options related to the HubSpot account.
 */
class AccountOptions extends LeadinOptions {
	const PORTAL_ID     = 'portalId';
	const PORTAL_DOMAIN = 'portal_domain';
	const ACCOUNT_NAME  = 'account_name';

	/**
	 * Return portal id.
	 */
	public static function get_portal_id() {
		return self::get( self::PORTAL_ID );
	}

	/**
	 * Return portal's domain.
	 */
	public static function get_portal_domain() {
		return self::get( self::PORTAL_DOMAIN );
	}

	/**
	 * Return account name.
	 */
	public static function get_account_name() {
		return self::get( self::ACCOUNT_NAME );
	}

	/**
	 * Set portal id.
	 *
	 * @param Number $portal_id HubSpot portal id.
	 */
	public static function add_portal_id( $portal_id ) {
		return self::add( self::PORTAL_ID, $portal_id );
	}

	/**
	 * Set portal domain.
	 *
	 * @param String $domain domain.
	 */
	public static function add_portal_domain( $domain ) {
		return self::add( self::PORTAL_DOMAIN, $domain );
	}

	/**
	 * Set account name.
	 *
	 * @param String $name name.
	 */
	public static function add_account_name( $name ) {
		return self::add( self::ACCOUNT_NAME, $name );
	}

	/**
	 * Delete portal id.
	 */
	public static function delete_portal_id() {
		return self::delete( self::PORTAL_ID );
	}

	/**
	 * Delete portal domain.
	 */
	public static function delete_portal_domain() {
		return self::delete( self::PORTAL_DOMAIN );
	}

	/**
	 * Delete account name.
	 */
	public static function delete_account_name() {
		return self::delete( self::ACCOUNT_NAME );
	}
}
