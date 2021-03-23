<?php
/**
 * The admin-facing file for contacts sync.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    makewebbetter-hubspot-for-woocommerce
 * @subpackage makewebbetter-hubspot-for-woocommerce/admin/templates/setup
 */

if ( isset( $_GET['action'] ) && 'hubwoo-osc-schedule-sync' == $_GET['action'] ) {
	update_option( 'hubwoo_greeting_displayed_setup', 'yes' );
	Hubwoo_Admin::hubwoo_schedule_sync_listener();
	wp_safe_redirect( admin_url( 'admin.php?page=hubwoo&hubwoo_tab=hubwoo-overview&hubwoo_key=sync' ) );
}
	$total_registered_users          = Hubwoo_Admin::hubwoo_get_all_users_count();
	$sync_process['display_sync']    = 'block';
	$sync_process['display_greet']   = 'none';
	$sync_process['display_onboard'] = 'none';

if ( 'yes' == get_option( 'hubwoo_greeting_displayed_setup', 'no' ) ) {
	$sync_process['display_sync']    = 'none';
	$sync_process['display_greet']   = 'none';
	$sync_process['display_onboard'] = 'block';
}

if ( 'yes' == get_option( 'hubwoo_onboard_user', 'no' ) ) {
	$sync_process['display_sync']    = 'none';
	$sync_process['display_onboard'] = 'none';
	$sync_process['display_greet']   = 'block';
}

if ( empty( get_option( 'hubwoo_customers_role_settings', array() ) ) ) {
	update_option( 'hubwoo_customers_role_settings', array_keys( Hubwoo_Admin::get_all_user_roles() ) );
}

$onboarding_data = Hubwoo::hubwoo_onboarding_questionaire();

?>

<div class="mwb-heb-welcome sync-page" style="display: <?php echo esc_html( $sync_process['display_sync'] ); ?>">
	<div class="hubwoo-box">
		<div class="mwb-heb-wlcm__title">			
			<h2>
				<?php esc_html_e( 'Sync WooCommerce data with HubSpot', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</h2>
		</div>
		<div class="mwb-heb-wlcm__content">
			<div class="hubwoo-content__para">
				<p>
					<?php esc_html_e( "You're almost done! The last step is to sync your WooCommerce data with HubSpot.", 'makewebbetter-hubspot-for-woocommerce' ); ?>
				</p>
				<p>
					<?php esc_html_e( "Once you sync your data, you'll be able to see all your WooCommerce information in HubSpot, so you can start engaging with your contacts and customers right away.", 'makewebbetter-hubspot-for-woocommerce' ); ?>
				</p>			
			</div>				
			<div class="mwb-heb-wlcm__btn-wrap">
				<?php
				if ( $total_registered_users < 500 ) {
					?>
							<a href="javascript:void(0);" id = "hubwoo-osc-instant-sync" class="hubwoo-osc-instant-sync hubwoo-btn--primary" data-total_users= "<?php echo esc_attr( $total_registered_users ); ?>"><?php esc_html_e( 'Sync Now', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>		
						<?php
				} else {
					?>
							
							<a href="?page=hubwoo&hubwoo_tab=hubwoo-sync-contacts&action=hubwoo-osc-schedule-sync" id = "hubwoo-osc-schedule-sync" id="hubwoo-osc-schedule-sync" class="hubwoo-osc-schedule-sync hubwoo__btn"><?php esc_html_e( 'Schedule Sync', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>
						<?php
				}
				?>
			</div>
		</div>
		<div>
			<div class="hubwoo-progress-wrap" style="display: none;">
				<p>
					<strong><?php esc_html_e( 'Contact sync is in progress. This should only take a few moments. Thanks for your patience!', 'makewebbetter-hubspot-for-woocommerce' ); ?></strong>
				</p>					
				<div class="hubwoo-progress">
					<div class="hubwoo-progress-bar" role="progressbar" style="width:0"></div>
				</div>
			</div>					
		</div>
	</div>
</div>

<div id="hubwoo-visit-dashboard" class="acc-connected mwb-heb-welcome" style="display: <?php echo esc_attr( $sync_process['display_greet'] ); ?>">
	<div class="hubwoo-box">
		<div class="mwb-heb-wlcm__title">			
			<h2>
				<?php esc_html_e( 'Congrats! You’ve successfully set up the HubSpot for WooCommerce plugin', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</h2>
		</div>
		<div class="mwb-heb-wlcm__content">
			<div class="hubwoo-content__para hubwoo-content__para--greeting">
				<div class="hubwoo-content__para--greeting-img" >
					<p>
						<?php esc_html_e( "What's next? Go to your dashboard to learn more about the integration." ); ?>
					</p>
					<div class="mwb-heb-wlcm__btn-wrap">
						<a href="javascript:void(0);" class="hubwoo__btn hubwoo_manage_screen" data-process="greet-to-dashboard" data-tab="hubwoo_tab" ><?php esc_html_e( 'Visit DashBoard', 'makewebbetter-hubspot-for-woocommerce' ); ?></a>
					</div>														
				</div>
				<div class="hubwoo-content__para--greeting-content" >
					<img height="150px" width="150px" src="<?php echo esc_url( HUBWOO_URL . 'admin/images/congo.jpg' ); ?>">
				</div>
			</div>
		</div>
	</div>
</div>

<div id="hubwoo-onboard-user" class="acc-connected mwb-heb-welcome" style="display: <?php echo esc_attr( $sync_process['display_onboard'] ); ?>">
	<div class="hubwoo-box">
		<div class="mwb-heb-wlcm__title">			
			<h2>
				<?php esc_html_e( 'Get tailored onboarding emails straight to your inbox', 'makewebbetter-hubspot-for-woocommerce' ); ?>
			</h2>
		</div>
		<div class="hubwoo-onboard-suburb">
			<p><?php esc_html_e( 'Help us make your experience even better by telling us:' ); ?></p>
		</div>		
		<div class="hubwoo-onboarding-email__body mwb-heb-wlcm__content">
			<form action="#" method="POST" id="hubwoo-onboarding-form">			
				<div class="hubwoo-onboarding-email__body-content">
					<div class="hubwoo-onboard-notice">
						<span><?php esc_html_e( 'Please fill all of the below fields before submission' ); ?></span>
					</div>					
					<?php
					foreach ( $onboarding_data as $name => $data ) {
						?>
					<div class="hubwoo-onboarding-email__items">
						<label class="hubwoo-onboard-suburb-label"><?php echo esc_textarea( $data['label'] ); ?></label>
						<select name="<?php echo esc_attr( $name ); ?>[]" <?php echo esc_attr( $data['allow'] ); ?> class="hubwoo-form-control hubwoo-onquest">
							<?php foreach ( $data['options'] as $option ) : ?>
								<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_attr( $option ); ?></option>							
							<?php endforeach ?>
						</select>
						<img class="hubwoo-onboard-img" name="<?php echo esc_attr( $name ); ?>" height="20px" width="20px" src="<?php echo esc_url( HUBWOO_URL . 'admin/images/checked.png' ); ?>">
					</div>
						<?php
					}
					?>
					<div class="hubwoo-onboarding-email__items">
						<label class="hubwoo-onboard-suburb-label"><?php esc_html_e( 'Email Address' ); ?></label>
						<input type="email" placeholder="Email Address" name="email" class="hubwoo-form-control" value="<?php echo esc_textarea( get_user_by( 'id', get_current_user_id() )->data->user_email ); ?>">
						<img class="hubwoo-onboard-img" name="email" height="20px" width="20px" src="<?php echo esc_url( HUBWOO_URL . 'admin/images/checked.png' ); ?>">
					</div>					
					<div class="onboard-spinner"><span class="fa fa-spinner fa-spin"></span></div>
					<div class="hubwoo-onboarding-email__butttons">
						<a href="javascript:void" data-type='sync' id= "hubwoo-complete-onboarding" class="hubwoo-onboard-manage hubwoo-btn--dashboard hubwoo-btn--primary"><?php esc_html_e( 'Complete Onboarding' ); ?></a>
						<div class="hubwoo-onboard-manage hubwoo-onboarding-skip--link">
							<a href="javascript:void" data-type='skip' class="hubwoo-onboard-manage" ><?php esc_html_e( 'Skip for now' ); ?></a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
