<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wrap about-wrap porto-wrap">
	<h1><?php esc_html_e( 'Welcome to Porto!', 'porto' ); ?></h1>
	<div class="about-text"><?php echo esc_html__( 'Porto is now installed and ready to use! Read below for additional information. We hope you enjoy it!', 'porto' ); ?></div>
	<div class="porto-logo"><span class="porto-version"><?php esc_html_e( 'Version', 'porto' ); ?> <?php echo PORTO_VERSION; ?></span></div>
	<h2 class="nav-tab-wrapper">
		<?php
		printf( '<a href="%s" class="nav-tab">%s</a>', esc_url( admin_url( 'admin.php?page=porto' ) ), esc_html__( 'Theme License', 'porto' ) );
		printf( '<a href="#" class="nav-tab nav-tab-active">%s</a>', esc_html__( 'Change Log', 'porto' ) );
		printf( '<a href="%s" class="nav-tab">%s</a>', esc_url( admin_url( 'customize.php' ) ), esc_html__( 'Theme Options', 'porto' ) );
		printf( '<a href="%s" class="nav-tab">%s</a>', esc_url( admin_url( 'themes.php?page=porto_settings' ) ), esc_html__( 'Advanced', 'porto' ) );
		printf( '<a href="%s" class="nav-tab">%s</a>', esc_url( admin_url( 'admin.php?page=porto-setup-wizard' ) ), esc_html__( 'Setup Wizard', 'porto' ) );
		printf( '<a href="%s" class="nav-tab">%s</a>', esc_url( admin_url( 'admin.php?page=porto-speed-optimize-wizard' ) ), esc_html__( 'Speed Optimize Wizard', 'porto' ) );
		?>
	</h2>
	<div class="porto-section porto-changelog">
		<?php

			require_once PORTO_PLUGINS . '/importer/importer-api.php';
			$importer_api = new Porto_Importer_API();
			$result       = $importer_api->get_response( 'changelog', array(), 'text' );
		if ( ! is_wp_error( $result ) ) {
			echo porto_strip_script_tags( $result );
		}
		?>
	</div>
	<div class="porto-thanks">
		<p class="description"><?php esc_html_e( 'Thank you, we hope you to enjoy using Porto!', 'porto' ); ?></p>
	</div>
</div>
