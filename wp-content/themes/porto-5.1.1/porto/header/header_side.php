<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-side sticky-menu-header<?php echo ! $porto_settings['logo-overlay'] || ! $porto_settings['logo-overlay']['url'] ? '' : ' logo-overlay-header'; ?>" data-plugin-sticky data-plugin-options="<?php echo esc_attr( '{"autoInit": true, "minWidth": 992, "containerSelector": ".page-wrapper","autoFit":true, "paddingOffsetBottom": 0, "paddingOffsetTop": 0}' ); ?>">
	<div class="header-main<?php echo ! $porto_settings['show-minicart'] || ! class_exists( 'WooCommerce' ) ? '' : ' show-minicart'; ?>">

		<div class="side-top">
			<div class="container">
				<?php
				// show currency and view switcher
				$minicart = porto_minicart();

				echo porto_view_switcher();

				echo porto_currency_switcher();

				?>

				<div class="header-minicart">
					<?php echo porto_filter_output( $minicart ); ?>
				</div>
			</div>
		</div>

		<div class="container">

			<?php
				get_template_part( 'header/header_tooltip' );
			?>

			<div class="header-left">
				<?php
				// show logo
				echo porto_logo();
				?>
			</div>

			<div class="header-center">
				<?php
				// show search form
				echo porto_search_form();
				// show mobile toggle
				?>

				<?php
				$sidebar_menu = porto_header_side_menu();
				if ( $sidebar_menu ) :
					echo porto_filter_output( $sidebar_menu );
				endif;
				?>
				<a class="mobile-toggle"><i class="fas fa-bars"></i></a>

				<div class="d-xl-none d-lg-none inline-block">
					<?php echo porto_filter_output( $minicart ); ?>
				</div>

				<?php
				// show top navigation
				echo porto_mobile_top_navigation();
				?>
			</div>

			<div class="header-right">
				<div class="side-bottom">
					<?php
					// show contact info and mini cart
					$contact_info = $porto_settings['header-contact-info'];

					if ( $contact_info ) {
						echo '<div class="header-contact">' . do_shortcode( $contact_info ) . '</div>';
					}
					?>

					<?php
					// show social links
					echo porto_header_socials();
					?>

					<?php
					// show copyright
					$copyright = $porto_settings['header-copyright'];

					if ( $copyright ) {
						echo '<div class="header-copyright">' . do_shortcode( $copyright ) . '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
			get_template_part( 'header/mobile_menu' );
		?>
	</div>
</header>
