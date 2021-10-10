<?php
/**
 * Create our admin menu
 * @since 3.9.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a custom menu page.
 */
function pewc_register_menu_page() {
  add_menu_page(
    __( 'Product Add-Ons', 'pewc' ),
    __( 'Product Add-Ons', 'pewc' ),
    'edit_others_shop_orders',
    'pewc_home',
    'pewc_home_page',
    'dashicons-plus-alt',
  	apply_filters( 'pewc_menu_position', 56 )
  );

	add_submenu_page('pewc_home', 'Home', 'Home', 'edit_others_shop_orders', 'pewc_home' );
}
add_action( 'admin_menu', 'pewc_register_menu_page', 1 );

function pewc_home_page() { ?>
	<div class="wrap">

		<div id="root">

			<div class="woocommerce-layout">
				<div class="woocommerce-layout__header">
					<h1 class="woocommerce-layout__header-heading">Home</h1>
				</div>
			</div>

			<div class="pewc-home-outer">

				<div class="pewc-home-inner">

					<div class="pewc-home-wrapper">

						<div class="pewc-home-column">

							<div class="pewc-box">
								<?php
								printf(
									'<h2>%s %s</h2>',
									__( 'WooCommerce Product Add-Ons Ultimate', 'pewc' ),
									PEWC_PLUGIN_VERSION
								); ?>
								<?php
								printf(
									'<p>%s</p>',
									__( 'Use this page to find help quickly, whether you\'ve just started using the plugin or you\'ve been using it for years.', 'pewc' )
								);
								printf(
									'<p>%s</p>',
									__( 'You\'ll find links and videos here that will answer the most common questions.', 'pewc' )
								); ?>
							</div>

							<div class="pewc-box">
								<?php
								printf(
									'<h3>%s</h3>',
									__( 'Getting Started', 'pewc' )
								); ?>
								<?php
								printf(
									'<p>%s</p>',
									__( 'Here are some links to documents to help with setting the plugin up for the first time.', 'pewc' )
								); ?>
								<?php

								$links = array(
									array(
										'url'		=> 'https://pluginrepublic.com/documentation/adding-your-first-product-extra-field/',
										'title'	=> __( 'Adding your first Add-Ons field', 'pewc' )
									),
									array(
										'url'		=> 'https://pluginrepublic.com/documentation/field-types/',
										'title'	=> __( 'A guide to field types', 'pewc' )
									),
									array(
										'url'		=> 'https://pluginrepublic.com/documentation/global-add-ons/',
										'title'	=> __( 'Global add-ons', 'pewc' )
									),
									array(
										'url'		=> 'https://pluginrepublic.com/support-categories/product-extras-for-woocommerce/',
										'title'	=> __( 'All the plugin documentation', 'pewc' )
									)
								);
								foreach( $links as $link ) {
									printf(
										'<ul><li><a target="_blank" href="%s">%s&nbsp;%s</a></li></ul>',
										$link['url'],
										$link['title'],
										'<span class="dashicons dashicons-external"></span>'
									);
								} ?>

							</div>

							<div class="pewc-box">
								<?php
								printf(
									'<h3>%s</h3>',
									__( 'Advanced', 'pewc' )
								); ?>
								<?php
								printf(
									'<p>%s</p>',
									__( 'If you\'d like some help with more advanced features, check out the links below.', 'pewc' )
								); ?>
								<?php

								$links = array(
									array(
										'url'		=> 'https://pluginrepublic.com/documentation/conditions/',
										'title'	=> __( 'Adding conditions to your fields', 'pewc' )
									),
									array(
										'url'		=> 'https://pluginrepublic.com/documentation/upload-fields/',
										'title'	=> __( 'Uploading files', 'pewc' )
									),
									array(
										'url'		=> 'https://pluginrepublic.com/support-categories/product-extras-for-woocommerce/',
										'title'	=> __( 'All the plugin documentation', 'pewc' )
									)
								);
								foreach( $links as $link ) {
									printf(
										'<ul><li><a target="_blank" href="%s">%s&nbsp;%s</a></li></ul>',
										$link['url'],
										$link['title'],
										'<span class="dashicons dashicons-external"></span>'
									);
								} ?>

							</div>

						</div>

						<div class="pewc-home-column">

							<div class="pewc-box pewc-video-box">
								<p>
									<iframe width="853" height="480" src="https://www.youtube.com/embed/O1AHCnvdhKQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
								</p>
								<p>
									<iframe width="853" height="480" src="https://www.youtube.com/embed/u5TS7jBL-H0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
								</p>

								<?php
								printf(
									'<p><a target="_blank" href="%s">%s</a></p>',
									'https://www.youtube.com/channel/UCT2RaWidqmyzJgyL0zGoDfQ',
									__( 'Find more videos on YouTube.', 'pewc' )
								); ?>
							</div>

						</div>

					</div>

				</div><!-- inner -->

				<div class="pewc-home-inner pewc-promo-wraper">

						<?php
						printf(
							'<h2>%s</h2>',
							__( 'Power up Add-Ons Ultimate with these extensions', 'pewc' )
						); ?>

						<?php
						$upsells = array(
							array(
								'url'		=> 'https://pluginrepublic.com/wordpress-plugins/add-ons-ultimate-advanced-uploads/',
								'src'		=> 'wcpauau-600x300.png',
								'id'		=> 'wcpauau',
								'file'	=> 'wcpau-advanced-uploads/wcpau-advanced-uploads.php'
							),
							array(
								'url'		=> 'https://pluginrepublic.com/wordpress-plugins/text-preview-plugin/',
								'src'		=> 'apaou-thumb.jpg',
								'id'		=> 'apaou',
								'file'	=> 'apaou/bootstrap.php'
							),
							array(
								'url'		=> 'https://pluginrepublic.com/wordpress-plugins/image-preview-for-add-ons-ultimate/',
								'src'		=> 'aipaou-thumb.jpg',
								'id'		=> 'aipaou',
								'file'	=> 'aipaou/bootstrap.php'
							),
							array(
								'url'		=> 'https://pluginrepublic.com/wordpress-plugins/advanced-calculations/',
								'src'		=> 'acaou-thumb.jpg',
								'id'		=> 'acaou',
								'file'	=> 'acaou/acaou.php'
							),
						);

						foreach( $upsells as $upsell ) {

							$url = add_query_arg(
								array(
									'utm_source'		=> 'ftue',
									'utm_medium'		=> 'pewc',
									'utm_campaign'	=> $upsell['id']
								),
								$upsell['url']
							);

							if( in_array( $upsell['file'], apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
								// Don't promote if the plugin is already active
								continue;
							}

							printf(
								'<div class="pewc-promo"><a href="%s" target="_blank"><img src="%s"></a></div>',
								$url,
								trailingslashit( PEWC_PLUGIN_URL ) . 'assets/images/upsells/' . $upsell['src']
							);

						} ?>

						<?php
						// printf(
						// 	'<h2>%s</h2>',
						// 	__( 'You might also be interested in', 'pewc' )
						// ); ?>


				</div><!-- inner -->

			</div>

		</div>

	</div>
<?php }

function pewc_admin_body_class( $classes ) {
	if( isset( $_GET['page'] ) && $_GET['page'] == 'pewc_home' ) {
		$classes .= ' woocommerce-page';
	}
	return $classes;
}
add_filter( 'admin_body_class', 'pewc_admin_body_class' );
