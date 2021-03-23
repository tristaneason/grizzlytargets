<?php

/**
 * Package that adds a logger to the Feed Manager making it possible to make extensive loggings of the feed process.
 *
 * @package Logger.
 * @since 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wppfm_logger_setup();

/**
 * Sets-up the logger functionality.
 */
function wppfm_logger_setup() {

	if ( 'true' === get_option( 'wppfm_process_logger_status', 'false' ) && wppfm_logger_prerequisites() ) {
		require_once __DIR__ . '/includes/wppfm-logger-functions.php';

		if ( ! is_plugin_active( 'wp-product-feed-manager-logger/wp-product-feed-manager-logger.php' ) ) {
			// Include the required classes.
			if ( ! class_exists( 'WPPFM_Logging_Folders' ) ) {
				require_once __DIR__ . '/includes/class-wppfm-logging-folders.php';
			}

			if ( ! class_exists( 'WPPFM_Feed_Process_Logging' ) ) {
				require_once __DIR__ . '/includes/class-wppfm-feed-process-logging.php';
			}

			// Define constants.
			wppfm_logger_define_constants();
		}
	}
}

/**
 * Checks if all required plugins are installed and active
 *
 * Required are woocommerce and wp-product-feed-manager
 *
 * @since 2.7.0
 */
function wppfm_logger_prerequisites() {
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && is_plugin_active( 'wp-product-feed-manager/wp-product-feed-manager.php' ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Define the required constants
 *
 * @since 2.7.0
 */
function wppfm_logger_define_constants() {
	if ( ! defined( 'WPPFM_LGR_LOGGINGS_DIR' ) ) {
		define( 'WPPFM_LGR_LOGGINGS_DIR', WPPFM_UPLOADS_DIR . '/wppfm-logs' );
	}
}

/**
 * Verifies if the old version of the logger plugin is still installed and warns the user about it.
 *
 * @since 2.7.0
 */
function wppfm_check_old_logger_installed() {
	if ( is_plugin_active( 'wp-product-feed-manager-logger/wp-product-feed-manager-logger.php' ) ) {
		$msg = __( 'The logger code is now integrated in the Feed Manager plugin. To prevent conflicts please deactivate and remove the "WooCommerce Product Feed Manager Logger" plugin from your Plugins page.', 'wp-product-feed-manager' );
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo esc_html( $msg ); ?></p>
		</div>
		<?php
	}
}

add_action( 'admin_notices', 'wppfm_check_old_logger_installed' );
