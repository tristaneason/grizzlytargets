<?php
/**
 * WooCommerce Authorize.Net Gateway
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.Net Gateway to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.Net Gateway for your
 * needs please refer to http://docs.woocommerce.com/document/authorize-net-cim/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Authorize_Net\Emulation\Migration;

use Automatic_Upgrader_Skin;
use Exception;
use WP_Upgrader;

defined( 'ABSPATH' ) or exit;

/**
 * Emulation plugin installer.
 *
 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
 *
 * @since 3.5.0
 * @deprecated 3.6.0
 */
class Installer {


	/**
	 * Installs and activates the new Emulation plugin.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.5.0
	 * @deprecated 3.6.0
	 *
	 * @param string $redirect_url
	 */
	public static function install_and_activate( string $redirect_url ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Checks if the Emulation plugin is installed.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.5.0
	 * @deprecated 3.6.0
	 *
	 * @return bool
	 */
	public static function is_installed() {

		wc_deprecated_function( __METHOD__, '3.6.0' );

		return false;
	}


	/**
	 * Downloads the Emulation plugin from GitHub and installs it.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.5.0
	 * @deprecated 3.6.0
	 */
	protected static function install() {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


	/**
	 * Activates the Emulation plugin.
	 *
	 * @TODO remove this by version 4.0.0 or March 2022 {FN 2021-03-24}
	 *
	 * @since 3.5.0
	 * @deprecated 3.6.0
	 *
	 * @param string $redirect_url
	 */
	protected static function activate( string $redirect_url ) {

		wc_deprecated_function( __METHOD__, '3.6.0' );
	}


}
