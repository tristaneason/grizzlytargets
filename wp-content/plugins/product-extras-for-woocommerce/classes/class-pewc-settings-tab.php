<?php
/**
 * Class to create Product Add-Ons tab in Settings
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'PEWC_Settings_Tab' ) ) {

	class PEWC_Settings_Tab {

		public function __construct() {
		}

		public function init() {
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
			add_action( 'woocommerce_settings_tabs_pewc', array( $this, 'settings_tab' ) );
			add_action( 'woocommerce_sections_pewc', array( $this, 'output_sections' ) );
			add_action( 'woocommerce_update_options_pewc', array( $this, 'update_settings' ) );

			add_action( 'woocommerce_admin_field_pewc_license_key', array( $this, 'licence_key' ) );
			add_action( 'woocommerce_admin_field_wcpauau_license_key', array( $this, 'wcpauau_licence_key' ) );
			add_action( 'woocommerce_admin_field_acaou_license_key', array( $this, 'acaou_license_key' ) );

			// Action links
			add_filter( 'plugin_action_links_product-extras-for-woocommerce/product-extras-for-woocommerce.php', array( $this, 'action_links' ) );
			add_action( 'admin_menu', array( $this, 'upgrade_submenu' ) );
		}

		public static function add_settings_tab( $settings_tabs ) {
			$settings_tabs['pewc'] = __( 'Product Add-Ons', 'pewc' );
			return $settings_tabs;
		}

		public function settings_tab() {
			woocommerce_admin_fields( $this->get_settings() );
		}

		public function update_settings() {
			woocommerce_update_options( $this->get_settings() );
		}

		public function get_settings() {

			global $current_section;
			$settings = array();

			if( $current_section == 'pewc' || ! $current_section ) {

				$settings = pewc_get_general_settings();

			} else if( $current_section == 'pewc_uploads' ) {

				$settings = pewc_get_uploads_settings();

			} else if( $current_section == 'pewc_products' && pewc_is_pro() ) {

				$settings = pewc_get_products_settings();

			} else if( $current_section == 'pewc_calculations' && pewc_is_pro() ) {

				$settings = pewc_get_calculations_settings();

			} else if( $current_section == 'pewc_lk' ) {

				$settings = pewc_get_licence_settings();

			}

			return $settings;

		}

		/**
		 * Output sections.
		 */
		public function output_sections() {

			global $current_section;

			echo '<ul class="subsubsub">';

				// Main settings tab
				$sections = array(
					'pewc'				 => __( 'General', 'pewc' ),
					'pewc_uploads' => __( 'Uploads', 'pewc' )
				);

				if( pewc_is_pro() ) {
					$sections['pewc_products'] = __( 'Products', 'pewc' );
					$sections['pewc_calculations'] = __( 'Calculations', 'pewc' );
				}

				$sections['pewc_lk'] = __( 'Licence', 'pewc' );

				$sections = apply_filters( 'pewc_settings_sections', $sections );

				$array_keys = array_keys( $sections );

				foreach( $sections as $id=>$label ) {
					echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=pewc&section=' . sanitize_title( $id ) ) . '" class="' . ( ( $current_section == $id || ( ! $current_section && $id == 'pewc' ) ) ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
				}

			echo '</ul><br class="clear" />';

		}

		public function action_links( $links ) {
			if( ! pewc_is_pro() ) {
				$url = pewc_get_upgrade_url();
				$links['upgrade'] = sprintf(
					'<a target="_blank" href="%s">%s</a>',
					esc_url( $url ),
					__( 'Upgrade', 'pewc' )
				);
			}
			return $links;
		}

		public function upgrade_submenu() {
	    global $submenu;
	    $submenu['edit.php?post_type=pewc_product_extra'][] = array( __( 'Upgrade', 'pewc' ), 'manage_plugins', pewc_get_upgrade_url() );
		}

		/**
		 * Custom setting for EDD SL licence key
		 */
		public function licence_key() {
			$key = get_option( 'pewc_license_key' ); ?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Licence key', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<input name="pewc_license_key" id="pewc_license_key" type="text" style="" value="<?php echo $key; ?>" class="" placeholder="">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Status', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<?php $status = ( false !== get_option( 'pewc_license_status' ) ) ? get_option( 'pewc_license_status' ) : 'invalid';
					if( $status == 'valid' ) {
						echo '<span class="dashicons dashicons-yes"></span>&nbsp;';
						echo ucfirst( $status );
					} else {
						// echo '<span class="dashicons dashicons-no-alt"></span>&nbsp;';
						if( ! $key ) {
							_e( 'Please enter your licence key', 'pewc' );
						} else if( $status == 'invalid' ) {
							// $error = get_option( 'pewc_test_response', false );
							printf(
								"<p>%s</p>",
							 	__( "It hasn't been possible to validate your licence key. Please check that you've entered it correctly. Remember, the licence key will be on the email you were sent with the downloadable plugin files.", 'pewc' )
							);
							printf(
								"<p>%s</p>",
							 	__( "If the licence key is correct and you're still seeing this message, please check the link below for help.", 'pewc' )
							);
							printf(
								"<p><strong><a target='_blank' href='https://pluginrepublic.com/documentation/problems-activating-your-licence/'>%s</a></strong></p>",
							 	__( "How to solve any problems with activating your licence.", 'pewc' )
							);
							printf(
								"<p>%s</p>",
							 	__( "If you are asked for an error code, please click the following link.", 'pewc' )
							);
							$url = pewc_get_settings_url();
							$url = add_query_arg(
								array(
									'show_response' => true
								),
								$url
							);
							printf(
								"<p><strong><a href='%s'>%s</a></strong></p>",
								$url,
							 	__( "Get error message.", 'pewc' )
							);
						}
					} ?>
				</td>
				<?php if( $status == 'valid' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="pewc_deactivate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Deactivate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } else if( $status == 'deactivated' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="pewc_activate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Activate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } ?>
			</tr>
			<?php
			wp_nonce_field( 'pewc_license_key_nonce', 'pewc_license_key_nonce' );
		}

		/**
		 * Custom setting for EDD SL licence key
		 */
		public function wcpauau_licence_key() {
			$key = get_option( 'wcpauau_license_key' ); ?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Licence key', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<input name="wcpauau_license_key" id="wcpauau_license_key" type="text" style="" value="<?php echo $key; ?>" class="" placeholder="">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Status', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<?php $status = ( false !== get_option( 'wcpauau_license_status' ) ) ? get_option( 'wcpauau_license_status' ) : 'invalid';
					if( $status == 'valid' ) {
						echo '<span class="dashicons dashicons-yes"></span>&nbsp;';
					} else {
						echo '<span class="dashicons dashicons-no-alt"></span>&nbsp;';
					}
					echo ucfirst( $status ); ?>
				</td>
				<?php if( $status == 'valid' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="wcpauau_deactivate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Deactivate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } else if( $status == 'deactivated' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="wcpauau_activate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Activate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } ?>
			</tr>
			<?php
			wp_nonce_field( 'wcpauau_license_key_nonce', 'wcpauau_license_key_nonce' );
		}

		/**
		 * Custom setting for EDD SL licence key
		 */
		public function acaou_license_key() {
			$key = get_option( 'acaou_license_key' ); ?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Licence key', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<input name="acaou_license_key" id="acaou_license_key" type="text" style="" value="<?php echo $key; ?>" class="" placeholder="">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Status', 'pewc' ); ?>
				</th>
				<td class="forminp forminp-text">
					<?php $status = ( false !== get_option( 'acaou_license_status' ) ) ? get_option( 'acaou_license_status' ) : 'invalid';
					if( $status == 'valid' ) {
						echo '<span class="dashicons dashicons-yes"></span>&nbsp;';
					} else {
						echo '<span class="dashicons dashicons-no-alt"></span>&nbsp;';
					}
					echo ucfirst( $status ); ?>
				</td>
				<?php if( $status == 'valid' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="acaou_deactivate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Deactivate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } else if( $status == 'deactivated' ) { ?>
					<tr>
						<th scope="row" class="titledesc">
							<?php _e( 'Action', 'pewc' ); ?>
						</th>
						<td class="forminp forminp-text">
							<?php printf(
								'<p><button type="submit" name="acaou_activate_license_key" class="button button-secondary">%s</button></p>',
								__( 'Activate this licence', 'pewc' )
							); ?>
						</td>
					</tr>
				<?php } ?>
			</tr>
			<?php
			wp_nonce_field( 'acaou_license_key_nonce', 'acaou_license_key_nonce' );
		}

	}

	$PEWC_Settings_Tab = new PEWC_Settings_Tab;
	$PEWC_Settings_Tab->init();

}
