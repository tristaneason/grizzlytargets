<?php

use Objectiv\Plugins\Checkout\Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="overlay">
	<div class="spinner-wrap">
		<div class="loader">Loading...</div>
	</div>
</div>
<main id="cfw-content" class="<?php echo $css_classes; ?> cfw-order-received">
	<div class="wrap">
        <div id="cfw-logo-container-mobile">
            <div class="cfw-logo">
                <a title="<?php echo get_bloginfo( 'name' ); ?>" href="<?php echo get_home_url(); ?>" class="logo"></a>
            </div>
        </div>

		<?php if ( ! empty( $order ) ): ?>
			<div id="cfw-main-container" class="cfw-container">
				<div class="cfw-left-column cfw-column-7 woocommerce-checkout-review-order">
                    <div id="cfw-logo-container">
                        <div class="cfw-logo">
                            <a title="<?php echo get_bloginfo( 'name' ); ?>" href="<?php echo get_home_url(); ?>" class="logo"></a>
                        </div>
                    </div>

                    <div id="cfw-thank-you" class="cfw-panel">
                        <div class="title">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50" fill="none" stroke-width="2" class="checkmark"><path class="checkmark__circle" d="M25 49c13.255 0 24-10.745 24-24S38.255 1 25 1 1 11.745 1 25s10.745 24 24 24z"></path><path class="checkmark__check" d="M15 24.51l7.307 7.308L35.125 19"></path></svg>
                            <h5><?php echo sprintf( __( 'Order %s', 'checkout-wc' ), $order->get_id() ); ?></h5>
                            <h4 class="cfw-module-title"><?php echo sprintf( __( 'Thank you %s!', 'checkout-wc' ), $order->get_billing_first_name() ); ?></h4>
                        </div>

                        <section class="cfw-order-status">
                            <div class="inner status-row">
                                <?php if ( $show_shipping && function_exists( 'wc_order_status_manager' ) ): ?>
                                    <ul class="status-steps">
                                        <?php $count = 0; ?>
                                        <?php foreach( $order_statuses as $order_status ):
                                            $order_status = new \WC_Order_Status_Manager_Order_Status( $order_status );
                                            ?>
                                            <li class="status-step <?php if( $order->get_status() == $order_status->get_slug() ) echo 'status-step-selected'; ?>">
                                                <i class="<?php echo $order_status->get_icon(); ?>"></i>

                                                <span class="title">
                                                    <?php echo wc_get_order_status_name( $order_status->get_slug() ); ?>
                                                </span>

                                                <span class="date">
                                                    <?php
                                                    $date = cfw_order_status_date( $order->get_id(), wc_get_order_status_name( $order_status->get_slug() ) );

                                                    if ( $date ) {
                                                        echo date_i18n( get_option( 'date_format' ), strtotime( $date ) );
                                                    } elseif ( $count === 0 ) {
	                                                    echo date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );
                                                    }
                                                    ?>
                                                </span>
                                            </li>
                                            <?php $count++; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php elseif ( $show_shipping ): ?>
                                    <ul class="status-steps">
		                                <?php $count = 0; ?>
		                                <?php foreach( $order_statuses as $order_status ):
			                                ?>
                                            <li class="status-step <?php if( $order->get_status() == $order_status ) echo 'status-step-selected status-step-current'; ?>">
                                                <i class="<?php echo apply_filters('cfw_thank_you_status_icon_' . $order_status, 'fa fa-chevron-circle-right' ); ?>"></i>

                                                <span class="title">
                                                    <?php echo wc_get_order_status_name( $order_status ); ?>
                                                </span>

                                                <span class="date">
                                                    <?php
                                                    $date = cfw_order_status_date( $order->get_id(), wc_get_order_status_name( $order_status ) );

                                                    if ( $date ) {
	                                                    echo date_i18n( get_option( 'date_format' ), strtotime( $date ) );
                                                    } elseif ( $count === 0 ) {
	                                                    echo date_i18n( get_option( 'date_format' ), strtotime( $order->get_date_created() ) );
                                                    }
                                                    ?>
                                                </span>
                                            </li>
			                                <?php $count++; ?>
		                                <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <h3><?php _e('Order status', 'checkout-wc'); ?></h3>
                                    <p><?php echo wc_get_order_status_name( $order->get_status() ); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ( $show_shipping ): ?>
	                            <?php if ( 'yes' == Main::instance()->get_settings_manager()->get_setting( 'enable_map_embed' ) ): ?>
                                    <div id="map"></div>
	                            <?php endif; ?>

	                            <?php cfw_maybe_output_tracking_numbers( $order ); ?>
                            <?php endif; ?>

                        </section>

						<?php if ( $show_shipping ): ?>
                            <section class="cfw-order-updates">
                                <div class="inner">
                                    <h3><?php _e( 'Order updates', 'checkout-wc' ); ?></h3>
                                    <p>
                                        <?php echo apply_filters( 'cfw_order_updates_text', __( 'You’ll get shipping and delivery updates by email.', 'checkout-wc' ), $order ); ?>
                                    </p>
                                </div>
                            </section>
						<?php endif; ?>

                        <section class="cfw-customer-information">
                            <div class="inner">
                                <h3><?php _e( 'Customer information', 'checkout-wc' ); ?></h3>

                                <?php do_action( 'cfw_before_thank_you_customer_information', $order ); ?>

                                <div class="cfw-sg-container">
                                    <div class="cfw-column-6">
                                        <h6><?php _e( 'Contact information', 'checkout-wc' ); ?></h6>
                                        <p><?php echo $order->get_billing_email(); ?></p>
                                    </div>
                                    <div class="cfw-column-6">
                                        <h6><?php _e( 'Payment method', 'checkout-wc' ); ?></h6>
                                        <p><?php echo $order->get_payment_method_title(); ?></p>
                                    </div>
                                </div>

                                <div class="cfw-sg-container">
                                    <?php if ( $show_shipping ): ?>
                                        <div class="cfw-column-6">
                                            <h6><?php _e( 'Shipping address', 'checkout-wc' ); ?></h6>
                                            <address>
                                                <?php echo wp_kses_post( $order->get_formatted_shipping_address( cfw_esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                                            </address>
                                        </div>
                                    <?php endif; ?>

                                    <div class="cfw-column-6">
                                        <h6><?php _e( 'Billing address', 'checkout-wc' ); ?></h6>
                                        <address>
                                            <?php echo wp_kses_post( $order->get_formatted_billing_address( cfw_esc_html__( 'N/A', 'woocommerce' ) ) ); ?>
                                        </address>
                                    </div>
                                </div>

	                            <?php if ( $show_shipping ): ?>
                                    <div class="cfw-sg-container">
                                        <div class="cfw-column-6">
                                            <h6><?php _e( 'Shipping method', 'checkout-wc' ); ?></h6>
                                            <p>
                                                <?php echo $order->get_shipping_method(); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="clear"></div>

	                            <?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
                            </div>
                        </section>

						<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
						<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

                        <div id="cfw-shipping-info-action" class="cfw-bottom-controls">
		                    <?php
		                    $return_to = apply_filters( 'woocommerce_continue_shopping_redirect', wc_get_page_permalink( 'shop' ) );
		                    $message   = sprintf( '<a href="%s" tabindex="1" class="cfw-primary-btn cfw-next-tab">%s</a>', esc_url( $return_to ), cfw_esc_html__( 'Continue shopping', 'woocommerce' ) );
		                    ?>
                            <!--- Placeholder -->
                            <div></div>
		                    <?php echo $message; ?>
                        </div>
					</div>

                    <footer id="cfw-footer">
                        <div class="wrap">
                            <div class="cfw-container cfw-column-12">
                                <div class="cfw-footer-inner entry-footer">
									<?php do_action( 'cfw_before_footer' ); ?>
									<?php if ( ! empty( $footer_text = Objectiv\Plugins\Checkout\Main::instance()->get_settings_manager()->get_setting('footer_text') ) ): ?>
										<?php echo do_shortcode( $footer_text ); ?>
									<?php else: ?>
                                        Copyright &copy; <?php echo date("Y"); ?>, <?php echo get_bloginfo('name'); ?>. All rights reserved.
									<?php endif; ?>
									<?php do_action( 'cfw_after_footer' ); ?>
                                </div>
                            </div>
                        </div>
                    </footer>
				</div>

				<!-- Cart / Sidebar Column -->
				<div id="cfw-cart-details" class="cfw-right-column cfw-column-5">
					<div id="cfw-cart-details-review-bar" class="cfw-sg-container">
						<div class="cfw-column-8">
							<a id="cfw-show-cart-details">
                                <span class="cfw-link">
                                    <?php if ( ! empty( $cart_summary_mobile_label = Objectiv\Plugins\Checkout\Main::instance()->get_settings_manager()->get_setting('cart_summary_mobile_label') ) ): ?>
	                                    <?php echo $cart_summary_mobile_label; ?>
                                    <?php else: ?>
	                                    <?php echo apply_filters( 'cfw_show_order_summary_link_text', esc_html__( 'Show order summary', 'checkout-wc' ) ); ?>
                                    <?php endif; ?>
                                </span>
								<svg id="cfw-cart-details-arrow" height="512px" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,115.4 180.7,96 352,256 180.7,416 160,396.7 310.5,256 "/></svg>
							</a>
						</div>
						<div class="cfw-column-4">
                            <span class="total amount">
                                <?php echo $cart->get_total(); ?>
                            </span>
						</div>
					</div>

					<div id="cfw-cart-details-collapse-wrap">
						<?php if ( count( $order->get_items() ) > 0 ): ?>
							<?php cfw_order_cart_html( $order ); ?>
						<?php endif; ?>

						<?php cfw_order_totals_html( $order ); ?>
					</div>

					<?php do_action( 'cfw_after_cart_summary' ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</main>
