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
 * @copyright Copyright (c) 2013-2021, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Authorize_Net\Emulation\Migration;

use Automatic_Upgrader_Skin;
use Exception;
use WP_Upgrader;

defined( 'ABSPATH' ) or exit;

class Installer {

	/**
	 * Emulation plugin basename.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	const EMULATION_PLUGIN_BASENAME = 'authorize-net-emulation-for-woocommerce/authorize-net-emulation-for-woocommerce.php';


	/**
	 * Emulation plugin package.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	const EMULATION_PLUGIN_PACKAGE = 'https://github.com/skyverge/authorize-net-emulation-for-woocommerce/releases/download/initial/authorize-net-emulation-for-woocommerce.zip';


	/**
	 * Installs and activates the new Emulation plugin.
	 *
	 * @since 3.5.0
	 *
	 * @param string $redirect_url
	 *
	 * @throws Exception
	 */
	public static function install_and_activate( string $redirect_url ) {

		// install the plugin if not already installed
		if ( ! self::is_installed() ) {
			self::install();
		}

		// active the plugin
		self::activate( $redirect_url );
	}


	/**
	 * Checks if the Emulation plugin is installed.
	 *
	 * @since 3.5.0
	 *
	 * @return bool
	 */
	public static function is_installed(): bool {

		$active_plugins = get_plugins();

		return isset( $active_plugins[ self::EMULATION_PLUGIN_BASENAME ] );
	}


	/**
	 * Downloads the Emulation plugin from GitHub and installs it.
	 *
	 * @since 3.5.0
	 *
	 * @throws Exception
	 */
	protected static function install() {

		// security check
		if ( ! current_user_can( 'install_plugins' ) ) {
			throw new Exception( 'You are not allowed to install plugins' );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		WP_Filesystem();

		$skin        = new Automatic_Upgrader_Skin();
		$wp_upgrader = new WP_Upgrader( $skin );

		$package = $wp_upgrader->download_package( self::EMULATION_PLUGIN_PACKAGE );

		if ( is_wp_error( $package ) ) {
			throw new Exception( $package->get_error_message() ? : $package->get_error_code() );
		}

		$working_dir = $wp_upgrader->unpack_package( $package, true );

		if ( is_wp_error( $working_dir ) ) {
			throw new Exception( $working_dir->get_error_message() ? : $working_dir->get_error_code() );
		}

		$result = $wp_upgrader->install_package( [
			'source'        => $working_dir,
			'destination'   => WP_PLUGIN_DIR,
			'clear_working' => true,
			'hook_extra'    => [
				'type'   => 'plugin',
				'action' => 'install',
			],
		] );

		if ( is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() ? : $result->get_error_code() );
		}

		wp_clean_plugins_cache();
	}


	/**
	 * Activates the Emulation plugin.
	 *
	 * @since 3.5.0
	 *
	 * @param string $redirect_url
	 *
	 * @throws Exception
	 */
	protected static function activate( string $redirect_url ) {

		// security check
		if ( ! current_user_can( 'activate_plugins' ) ) {
			throw new Exception( 'You are not allowed to activate plugins' );
		}

		// flag the plugin installed
		update_option( 'wc_authorize_net_emulation_plugin_installed', 'yes' );
		delete_option( 'wc_authorize_net_emulation_enabled' );

		$result = activate_plugin( self::EMULATION_PLUGIN_BASENAME, $redirect_url );

		if ( $result && is_wp_error( $result ) ) {
			throw new Exception( $result->get_error_message() );
		}
	}


}
