<?php
namespace Leadin;

use Leadin\options\AccountOptions;

const NA1_HUBLET = 'na1';

/**
 * Class containing all the custom filters defined to be used instead of constants.
 */
class LeadinFilters {
	/**
	 * Return the current hublet.
	 */
	public static function get_leadin_hublet() {
		return apply_filters( 'leadin_hublet', AccountOptions::get_hublet() );
	}

	/**
	 * Add hublet to the prefix.
	 *
	 * @param String $prefix Prefix to add the hublet to.
	 */
	private static function apply_hublet( $prefix ) {
		$hublet = self::get_leadin_hublet();
		$result = $prefix;
		if ( ! empty( $hublet ) && NA1_HUBLET !== $hublet ) {
			$result = "$prefix-$hublet";
		}
		return $result;
	}

	/**
	 * Return the prefix for UI urls.
	 */
	public static function get_leadin_app_prefix() {
		return self::apply_hublet( apply_filters( 'leadin_app_prefix', 'app' ) );
	}

	/**
	 * Return the prefix for API urls.
	 */
	public static function get_leadin_api_prefix() {
		return self::apply_hublet( apply_filters( 'leadin_api_prefix', 'api' ) );
	}

	/**
	 * Return the Hubspot domain.
	 */
	public static function get_leadin_domain() {
		return apply_filters( 'leadin_hubspot_domain', 'hubspot.com' );
	}

	/**
	 * Apply leadin_base_url filter.
	 */
	public static function get_leadin_base_url() {
		$prefix = self::get_leadin_app_prefix();
		$domain = self::get_leadin_domain();
		return apply_filters( 'leadin_base_url', "https://$prefix.$domain" );
	}

	/**
	 * Apply filter to get the base url for the HubSpot api.
	 */
	public static function get_leadin_base_api_url() {
		$prefix = self::get_leadin_api_prefix();
		$domain = self::get_leadin_domain();
		return apply_filters( 'leadin_base_api_url', "https://$prefix.$domain" );
	}

	/**
	 * Apply leadin_signup_base_url filter.
	 */
	public static function get_leadin_signup_base_url() {
		$domain = self::get_leadin_domain();
		return apply_filters( 'leadin_signup_base_url', "https://app.$domain" );
	}

	/**
	 * Apply leadin_forms_script_url filter.
	 */
	public static function get_leadin_forms_script_url() {
		$hublet_domain = self::apply_hublet( 'js' );
		return apply_filters( 'leadin_forms_script_url', "https://$hublet_domain.hsforms.net/forms/v2.js" );
	}

	/**
	 * Apply leadin_script_loader_domain filter.
	 */
	public static function get_leadin_script_loader_domain() {
		$hublet_domain = self::apply_hublet( 'js' );
		return apply_filters( 'leadin_script_loader_domain', "$hublet_domain.hs-scripts.com" );
	}

	/**
	 * Apply leadin_forms_payload filter.
	 */
	public static function get_leadin_forms_payload() {
		return apply_filters( 'leadin_forms_payload', '' );
	}
}
