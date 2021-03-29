<?php
/**
 * Functions for exporting Product Add-Ons
 * @since 1.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PEWC_STORE_URL', 'https://pluginrepublic.com' );
define( 'PEWC_ITEM_NAME', 'WooCommerce Product Add-Ons Ultimate' ); // you should use your own CONSTANT name, and be sure to replace it throughout this file
define( 'PEWC_ITEM_ID', 3930 );

if( ! class_exists( 'PEWC_SL_Plugin_Updater' ) ) {
	// load our custom updater
	include( PEWC_DIRNAME . '/classes/class-pewc-sl-plugin-updater.php' );
}

function pewc_plugin_updater( $license_key='' ) {

	if( ! $license_key ) {
		$license_key = trim( get_option( 'pewc_license_key' ) );
	}

	$beta_tester = get_option( 'pewc_beta_testing', 'no' );
	$beta = $beta_tester == 'yes' ? true : false;

	// setup the updater
	$pewc_updater = new PEWC_SL_Plugin_Updater( PEWC_STORE_URL, PEWC_FILE,
		array(
			'version' => PEWC_PLUGIN_VERSION,                    // current version number
			'license' => $license_key,       	// license key (used get_option above to retrieve from DB)
			'item_id' => PEWC_ITEM_ID,       	// ID of the product
			'author'  => 'Plugin Republic', 		// author of this plugin
			'beta'    => $beta,
		)
	);
}
add_action( 'admin_init', 'pewc_plugin_updater', 0 );

/**
 * Activate the license
 */
function pewc_activate_license( $license='' ) {
	if( ! isset( $_POST['pewc_license_key'] ) || ! isset( $_POST['pewc_license_key_nonce'] ) || ! wp_verify_nonce( $_POST['pewc_license_key_nonce'], 'pewc_license_key_nonce' ) ) {
		return;
	}
	$license = trim( $_POST['pewc_license_key'] );
	pewc_do_license_activation( $license );
}
add_action( 'admin_init', 'pewc_activate_license' );

/**
 * Activate the license
 */
function pewc_daily_check_license() {
	$license = trim( get_option( 'pewc_license_key' ) );
	pewc_do_license_activation( $license );
}
// Changed to weekly 3.7.7
add_action( 'wp_site_health_scheduled_check', 'pewc_daily_check_license' );

/**
 * Activate the license
 */
function pewc_do_license_activation( $license ) {

	pewc_plugin_updater( $license );

	// data to send in our API request
	$api_params = array(
		'edd_action' 	=> 'activate_license',
		'license'    	=> $license,
		// 'item_name'  	=> urlencode( PEWC_ITEM_NAME ),
		'item_id' 		=> PEWC_ITEM_ID,
		'url'        	=> home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( PEWC_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	update_option( 'pewc_test_response', $response );

	$message = '';

	// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
		}

	} else {

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( false === $license_data->success ) {

			$message = __( 'There was a problem in activating your license: ', 'pewc' );

			switch( $license_data->error ) {

				case 'expired' :

					$message .= sprintf(
						__( 'your license key expired on %s.' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'disabled' :
				case 'revoked' :

					$message .= __( 'your license key has been disabled.' );
					break;

				case 'missing' :

					$message .= __( 'the license is invalid.' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message .= __( 'the license is not active for this URL.' );
					break;

				case 'item_name_mismatch' :

					$message .= sprintf( __( 'this appears to be an invalid license key for %s.' ), PEWC_ITEM_NAME );
					break;

				case 'no_activations_left':

					$message .= __( 'your license key has reached its activation limit.' );
					break;

				default :

					$message .= __( 'an error occurred, please try again.' );
					break;
			}

			$message .= sprintf(
				__( ' Please ensure you enter the correct licence key as the plugin may not work correctly without it. Your licence key will be on the email you were sent with the download link or on <a target="_blank" href="%s">your account page</a>.', 'pewc' ),
				esc_url( PEWC_STORE_URL . '/my-account/' )
			);

			$settings_url = pewc_get_settings_url();
			$message .= sprintf(
				__( '<p><a href="%s">Enter your licence key here</a></p>', 'pewc' ),
				$settings_url
			);

		} else {
			$message = false;
		}

		if( isset( $license_data->expires ) ) {
			update_option( 'pewc_licence_expires', $license_data->expires );
		}

		update_option( 'pewc_license_status_message', $message );

		// $license_data->license will be either "valid" or "invalid"
		update_option( 'pewc_license_status', $license_data->license );

		if( isset( $license_data->payment_id ) ) {
			update_option( 'pewc_payment_id', $license_data->payment_id );
		}

		if( isset( $license_data->license_id ) ) {
			update_option( 'pewc_license_id', $license_data->license_id );
		}

		if( isset( $license_data->license_limit ) ) {
			update_option( 'pewc_license_level', $license_data->license_limit );
		}

	}

}

/***********************************************
* Illustrates how to deactivate a license key.
* This will decrease the site count
***********************************************/

function pewc_deactivate_license() {

	if( ! isset( $_POST['pewc_deactivate_license_key'] ) || ! isset( $_POST['pewc_license_key_nonce'] ) || ! wp_verify_nonce( $_POST['pewc_license_key_nonce'], 'pewc_license_key_nonce' ) ) {
		return;
	}

	// retrieve the license from the database
	$license = trim( get_option( 'pewc_license_key' ) );

	// data to send in our API request
	$api_params = array(
		'edd_action' 	=> 'deactivate_license',
		'license'    	=> $license,
		'item_name'  	=> urlencode( PEWC_ITEM_NAME ), // the name of our product in EDD
		'item_id' 		=> PEWC_ITEM_ID,
		'url'        	=> home_url()
	);

	// Call the custom API.
	$response = wp_remote_post( PEWC_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

	// make sure the response came back okay
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

		if ( is_wp_error( $response ) ) {
			$message = $response->get_error_message();
		} else {
			$message = __( 'An error occurred, please try again.' );
		}

	}

	// decode the license data
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	// $license_data->license will be either "deactivated" or "failed"
	if( $license_data->license == 'deactivated' ) {
		update_option( 'pewc_license_status', 'deactivated' );
	}

}
add_action('admin_init', 'pewc_deactivate_license');

function pewc_check_license() {
	$status = trim( get_option( 'pewc_license_status' ) );
	return $status;
}

/**
 * This is a means of catching errors from the activation method above and displaying it to the customer
 */
function pewc_license_admin_notices() {
	$status = pewc_check_license();
	$message = get_option( 'pewc_license_status_message' );
	if ( ! empty( $message ) ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong></p><p>%s</p></div>',
			__( 'WooCommerce Product Add-Ons Ultimate', 'pewc' ),
			$message
		);
	} else if( $status != 'valid' ) {
		$message = sprintf(
			__( 'Your license is not currently activated. Please ensure you activate your license in order to use all the features of this plugin. Your license number will be on the email you were sent with the download link or on <a target="_blank" href="%s">your account page</a>.', 'pewc' ),
			esc_url( PEWC_STORE_URL . '/my-account/' )
		);
		$settings_url = pewc_get_settings_url();
		$message .= sprintf(
			__( '<p><a href="%s">Enter your license key here</a></p>', 'pewc' ),
			$settings_url
		);
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong></p><p>%s</p></div>',
			__( 'WooCommerce Product Add-Ons Ultimate', 'pewc' ),
			$message
		);
	}

	if( isset( $_GET['show_response'] ) ) {
		$error = get_option( 'pewc_test_response', false );
		if( $error ) {
			printf(
				'<div class="notice notice-error"><pre>%s</pre></div>',
				print_r( $error, true )
			);
		}
	}

}
// add_action( 'admin_notices', 'pewc_license_admin_notices' );

/**
 * Display license status message
 */
function pewc_license_not_valid_message() {
	$message = sprintf(
		__( 'Your licence is not currently activated. Please ensure you activate your licence in order to use all the features of this plugin. Your licence number will be on the email you were sent with the download link or on <a target="_blank" href="%s">your account page</a>.', 'pewc' ),
		esc_url( PEWC_STORE_URL . '/my-account/' )
	);
	$settings_url = pewc_get_settings_url();
	$link = sprintf(
		__( '<p><a href="%s">Enter your licence key here</a></p>', 'pewc' ),
		$settings_url
	);
	printf(
		'<div class="field-table"><p>%s</p>%s</div>',
		$message,
		$link
	);
}

/**
 * Display license status message
 */
function pewc_license_validation_notice() {
	$status = get_option( 'pewc_license_status' );
	$message = get_option( 'pewc_license_status_message' );
	if( $status == 'invalid' ) {
		update_option( 'pewc_license_message_displayed', 0 ); ?>
		<div class="notice notice-error">
			<p><?php echo $message; ?></p>
		</div>
	<?php } else if( isset( $_GET['tab'] ) && $_GET['tab'] == 'pewc' ) {
		$message_displayed = get_option( 'pewc_license_message_displayed', 0 );
		if( ! $message_displayed ) {
			// Only show success notice on Product Add-Ons tab once license is activated ?>
			<div class="notice notice-success">
				<p><?php echo $message; ?></p>
			</div>
		<?php update_option( 'pewc_license_message_displayed', 1 );
		}
	}
}

/**
 * Check what license level we have
 * @return Mixed
 */
function pewc_get_license_level() {
 	$status = get_option( 'pewc_license_status' );
 	if( $status == 'invalid' ) {
 		return -1;
 	}
 	$license = get_option( 'pewc_license_level', -1 );
 	return $license;
}

/**
 * Are we a pro?
 * @return Boolean
 */
function pewc_is_pro() {
 	$license = pewc_get_license_level();
 	if( $license == 0 && is_numeric( $license ) ) {
 		return true;
 	}
	return false;
}

function pewc_get_settings_url() {
	$settings_url = get_admin_url( null, 'admin.php' );
	$settings_url = add_query_arg(
		array(
			'page'		=> 'wc-settings',
			'tab'			=> 'pewc',
			'section'	=> 'pewc_lk'
		),
		$settings_url
	);
	return $settings_url;
}

function pewc_plugin_update_message( $data, $response ) {
	// if( is_multisite() ) return;
	$file = file_get_contents( 'https://pluginrepublic.com/wp-content/uploads/readme.txt' );
	if( $file ) {
		$notice = str_replace( "== Upgrade Notice ==\n", '', stristr( $file, '== Upgrade Notice ==', false ) );
		$notice = trim( $notice, "\n" );
		if( $notice ) {
			echo '<style type="text/css">
			.update-message.notice.inline.notice-warning.notice-alt p:empty {
				display: none;
			}
			.update-message .pewc-update-message p:before {
				content: "";
				margin: 0;
			}
			.pewc-update-message ul {
				list-style: disc;
				margin-left: 2em;
			}
			</style>';
			printf(
				'<div class="pewc-update-message">%s</div>',
				wpautop( $notice )
			);
		}
	}

}
// add_action( 'in_plugin_update_message-product-extras-for-woocommerce/product-extras-for-woocommerce.php', 'pewc_plugin_update_message', 10, 2 );

function pewc_ms_plugin_update_message( $file, $plugin ) {
	$file = file_get_contents( 'https://pluginrepublic.com/wp-content/uploads/readme.txt' );
	if( $file ) {
		$notice = str_replace( "== Upgrade Notice ==\n", '', stristr( $file, '== Upgrade Notice ==', false ) );
		if( is_multisite() && version_compare( $plugin['Version'], $plugin['new_version'], '<') ) {
			$wp_list_table = _get_list_table( 'WP_Plugins_List_Table' );
			printf(
				'<tr class="plugin-update-tr"><td colspan="%s" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message"><h4 style="margin: 0; font-size: 14px;">%s</h4>%s</div></td></tr>',
				$wp_list_table->get_column_count(),
				$plugin['Name'],
				wpautop( $notice )
			);
		}
	}
}
// add_action( 'after_plugin_row_product-extras-for-woocommerce/product-extras-for-woocommerce.php', 'pewc_ms_plugin_update_message', 10, 2 );
