<?php
/**
 * The admin-facing file for setting up connection with HubSpot.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    makewebbetter-hubspot-for-woocommerce
 * @subpackage makewebbetter-hubspot-for-woocommerce/admin/templates/setup
 */

$hubspot_url = Hubwoo::hubwoo_get_auth_url();

if ( 'yes' == get_option( 'hubwoo_connection_complete', 'no' ) ) {
	$connect['will_connect']  = 'none';
	$connect['did_connected'] = 'block';
} else {
	$connect['will_connect']  = 'block';
	$connect['did_connected'] = 'none';
}
?>

<div class="mwb-heb-welcome" style="display: <?php echo esc_attr( $connect['will_connect'] ); ?>";>
	<div class="hubwoo-box">
		<div class="mwb-heb-wlcm__title">			
			<h2>
				<?php esc_html_e( 'Getting started with HubSpot and WooCommerce', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</h2>
		</div>
		<div class="mwb-heb-wlcm__content">
			<div class="hubwoo-content__para">
				<p>
					<?php
						esc_html_e( 'With this WooCommerce HubSpot integration, you can automatically sync all your WooCommerce contacts and customers with HubSpot’s CRM and marketing platform.', 'makewebbetter-hubspot-for-woocommerce' );
					?>
				</p>
				<p>
					<?php
						esc_html_e( 'Once you set up this integration, you will be able to:', 'makewebbetter-hubspot-for-woocommerce' );
					?>
					<ul class="connect-list">
						<li><?php esc_html_e( 'See every action each contact has taken including their page views, past orders, abandoned carts, and more — in HubSpot CRM’s tidy timeline view', 'makewebbetter-hubspot-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Segment contacts and customers into lists based on their previous interactions with your store', 'makewebbetter-hubspot-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Easily create and send beautiful, responsive emails to drive sales', 'makewebbetter-hubspot-for-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Measure your store’s performance with custom reports and dashboards', 'makewebbetter-hubspot-for-woocommerce' ); ?></li>
					</ul>
				</p>
			</div>
			<p>
				<?php esc_html_e( 'To get started, connect your HubSpot account. If you don’t have a HubSpot account, create one then return to this window to connect it.', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</p>

			<div class="mwb-heb-wlcm__btn-wrap">
				<a id="hubwoo-initiate-oauth" href="<?php echo esc_url( $hubspot_url ); ?>" class="hubwoo-btn--primary"><?php esc_html_e( 'Connect your Account', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>
				<a href="https://app.hubspot.com/signup-v2/crm/step/user-info?utm_medium=integration&utm_source=makewebbetter&utm_campaign=wordpress-woocommerce-integration" target="_blank" class="hubwoo-btn--primary hubwoo-btn--secondary"><?php esc_html_e( 'Create a free HubSpot Account', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>

<div class="acc-connected mwb-heb-welcome" style="display: <?php echo esc_attr( $connect['did_connected'] ); ?>";>
	<div class="hubwoo-box">
		<div class="mwb-heb-wlcm__title">			
			<h2>
				<?php esc_html_e( 'Congratulation your account is now connected.', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</h2>
		</div>
		<div class="mwb-heb-wlcm__content">
			<div class="hubwoo-content__para">
				<?php
				$connected_portal_id = get_option( 'hubwoo_pro_hubspot_id', '' );
				if ( ! empty( $connected_portal_id ) ) {
					?>
					<p>
					<?php esc_html_e( 'Connected Portal ID : ', 'makewebbetter-hubspot-for-woocommerce' ); ?><strong><?php echo esc_html( $connected_portal_id ); ?><strong>
					</p>

					<?php
				}
				?>
				<p>
					<?php
						esc_html_e( 'Switch to Another Account -', 'makewebbetter-hubspot-for-woocommerce' );
					?>
					<span class="changeAccount"><a class="hubwoo-manage-account" href="javascript:;" data-type="change-account" style="text-decoration: none; margin-left: 5px;"><?php esc_html_e( 'Click Here', 'makewebbetter-hubspot-for-woocommerce' ); ?></a></span>
				</p>
			</div>
			<div class="mwb-heb-wlcm__btn-wrap">
				<a href="javascript:;" class="hubwoo-btn--primary hubwoo_manage_screen" data-process="moveToGrpPr">
					<?php esc_html_e( 'Continue', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>
			</div>
		</div>
	</div>
</div>

<?php

if ( 'no' == get_option( 'hubwoo_clear_previous_options', 'no' ) ) {

	global $hubwoo;
	$hubwoo->hubwoo_switch_account( false );
}
?>
