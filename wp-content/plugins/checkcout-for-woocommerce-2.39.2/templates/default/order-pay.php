<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="overlay">
    <div class="spinner-wrap">
        <div class="loader">Loading...</div>
    </div>
</div>
<main id="cfw-content" class="<?php echo $css_classes; ?> cfw-payment-method-active cfw-order-pay">
    <div class="wrap">
        <div class="cfw-container">
            <div class="cfw-column-12">
	            <?php cfw_wc_print_notices(); ?>
            </div>
        </div>

	    <?php if ( ! empty( $order ) ): ?>
            <div id="cfw-main-container" class="cfw-container" customer="<?php echo $customer->get_id(); ?>">
                <div class="cfw-left-column cfw-column-7 woocommerce-checkout-review-order">
                    <!-- Payment Method Panel -->
                    <div id="cfw-payment-method" class="cfw-panel">
                        <h4 class="cfw-module-title"><?php echo cfw__( 'Pay for order', 'woocommerce' ); ?></h4>

                        <?php if ( ! current_user_can( 'pay_for_order', $order->get_id() ) && ! is_user_logged_in() ): ?>
                            <form method="post" novalidate>

                                <?php do_action( 'woocommerce_login_form_start' ); ?>

                                <?php echo ( $message ) ? wpautop( wptexturize( $message ) ) : ''; // @codingStandardsIgnoreLine ?>

                                <?php
                                cfw_form_field( 'username', array(
                                    'label'    => 'Email',
                                    'type'     => 'text',
                                    'required' => true,
                                    'autocomplete' => 'username',
                                ) );
                                ?>

                                <?php
                                cfw_form_field( 'password', array(
                                    'label'    => 'Password',
                                    'type'     => 'password',
                                    'required' => true,
                                    'autocomplete' => 'current-password',
                                ) );
                                ?>

                                <div class="clear"></div>

                                <?php do_action( 'woocommerce_login_form' ); ?>

                                <p class="form-row">
                                    <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                                    <input type="hidden" name="redirect" value="<?php echo esc_url( $order->get_checkout_payment_url() ) ?>" />

                                    <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button>

                                    <span class="login-optional cfw-small"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a></span>
                                </p>

                                <div class="clear"></div>

                                <?php do_action( 'woocommerce_login_form_end' ); ?>

                            </form>
                        <?php else: ?>
                            <form id="order_review" name="order_review" method="POST" novalidate>
                                <?php
                                // Some gateways need this when they use order-pay
                                // to take payment right after checkout
                                if ( ! empty( $call_receipt_hook ) ): ?>
                                    <?php do_action( 'woocommerce_receipt_' . $order->get_payment_method(), $order->get_id() ); ?>
                                <?php else: ?>
                                    <?php cfw_payment_methods( $available_gateways, $order, false ); ?>

	                                <?php wc_get_template('checkout/terms.php'); ?>

                                    <div id="cfw-payment-action" class="cfw-bottom-controls">
                                        <div class="previous-button"></div>

                                        <input type="hidden" name="woocommerce_pay" value="1" />

                                        <div class="place-order" id="cfw-place-order">
                                            <?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

                                            <?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="cfw-primary-btn cfw-next-tab validate" id="place_order" formnovalidate="formnovalidate" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

                                            <?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

                                            <?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
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
