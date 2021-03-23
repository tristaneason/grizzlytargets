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
<main id="cfw-content" class="<?php echo $css_classes; ?> cfw-tabs-initialize cfw-tabs-not-initialized">
    <div class="wrap">
        <div class="cfw-container">
            <div class="cfw-column-12">
	            <?php cfw_wc_print_notices(); ?>
            </div>
        </div>

	    <?php if ( ! WC()->cart->is_empty() ): ?>

	        <?php do_action( 'cfw_checkout_before_form' ); ?>

            <div class="cfw-container">
                <div class="cfw-column-12">
                    <?php do_action( 'woocommerce_before_checkout_form', WC()->checkout() ); ?>
                </div>
            </div>

	        <?php if( ! apply_filters('cfw_replace_form', false) ): ?>
                <form id="checkout" name="checkout" class="woocommerce-checkout checkout" method="POST" formnovalidate="" data-parsley-focus="first">
                    <div id="cfw-main-container" class="cfw-container" customer="<?php echo $customer->get_id(); ?>">
                        <!-- Easy Tab Container -->
                        <div id="cfw-tab-container" class="cfw-left-column cfw-column-7 tab-container">

                            <ul id="cfw-breadcrumb" class="etabs">
                                <li>
                                    <a href="<?php echo wc_get_cart_url(); ?>#cart">
	                                    <?php echo apply_filters( 'cfw_breadcrumb_cart_label', cfw_esc_html__('Cart', 'woocommerce') ); ?>
                                    </a>
                                </li>
                                <li class="tab" id="default-tab">
                                    <a href="#cfw-customer-info" class="cfw-small">
	                                    <?php echo apply_filters( 'cfw_breadcrumb_customer_info_label', esc_html__( 'Customer information', 'checkout-wc' ) ); ?>
                                    </a>
                                </li>
                                <?php if ( WC()->cart->needs_shipping() && apply_filters('cfw_show_shipping_tab', true) === true ): ?>
                                <li class="tab">
                                    <a href="#cfw-shipping-method" class="cfw-small">
	                                    <?php echo apply_filters( 'cfw_breadcrumb_shipping_label', esc_html__( 'Shipping method', 'checkout-wc' ) ); ?>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li class="tab">
                                    <a href="#cfw-payment-method" class="cfw-small">
	                                    <?php echo apply_filters( 'cfw_breadcrumb_payment_label', esc_html__( 'Payment method', 'checkout-wc' ) ); ?>
                                    </a>
                                </li>
                            </ul>

                            <div id="order_review" class="woocommerce-checkout-review-order">
                                <!-- Customer Info Panel -->
                                <div id="cfw-customer-info" class="cfw-panel">

                                    <div id="cfw-payment-request-buttons">
                                        <?php do_action('cfw_payment_request_buttons'); ?>
                                    </div>

                                    <?php do_action('cfw_checkout_before_customer_info_tab'); ?>

                                    <div id="cfw-login-details" class="cfw-module">
                                        <h3 class="cfw-module-title">
                                            <?php echo apply_filters('cfw_customer_information_heading', __( 'Customer information', 'checkout-wc' ) ); ?>
                                        </h3>

	                                    <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                                        <?php if( ! is_user_logged_in() ): ?>

	                                        <?php if ( 'yes' === get_option( 'woocommerce_enable_checkout_login_reminder' ) && apply_filters( 'cfw_suppress_default_login_form', true ) ): ?>
                                                <div class="cfw-have-acc-text cfw-small">
                                                    <span>
                                                        <?php esc_html_e('Already have an account with us?', 'checkout-wc'); ?>
                                                    </span>

                                                    <a id="cfw-ci-login" class="cfw-link" href="javascript:;">
                                                        <?php esc_html_e('Log in for a faster checkout experience.', 'checkout-wc'); ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <div id="" class="cfw-input-container create-account">

                                                <div id="cfw-email-wrap" class="cfw-input-wrap cfw-text-input">
                                                    <label class="cfw-input-label" for="billing_email"><?php esc_html_e('Email', 'checkout-wc'); ?></label>
                                                    <input type="email" name="billing_email" id="billing_email" data-parsley-group="account" autocomplete="email" size="30" title="<?php esc_attr_e('Email', 'checkout-wc'); ?>" placeholder="<?php esc_attr_e('Email', 'checkout-wc'); ?>" class="garlic-auto-save" value="" required="" data-parsley-required="true" data-parsley-trigger="keyup blur">
                                                </div>

                                                <div id="cfw-login-slide">

                                                    <div id="cfw-password-wrap" class="cfw-input-wrap cfw-password-input">
                                                        <label class="cfw-input-label" for="cfw-password"><?php esc_html_e('Password', 'checkout-wc'); ?></label>
                                                        <input type="password" name="cfw-password" id="cfw-password" autocomplete="off" title="<?php esc_attr_e('Password', 'checkout-wc'); ?>" placeholder="<?php esc_attr_e('Password', 'checkout-wc'); ?>">
                                                    </div>

                                                    <div class="cfw-input-wrap cfw-button-input">
                                                        <input type="button" name="cfw-login-btn" id="cfw-login-btn" value="<?php esc_attr_e('Login', 'checkout-wc'); ?>" />
                                                        <?php if( ! WC()->checkout()->is_registration_required() ): ?>
                                                            <span class="login-optional cfw-small">
                                                                <?php echo apply_filters( 'cfw_login_optional_text', esc_html__( 'Login is optional. You may continue with your order below.', 'checkout-wc' ) ); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>

                                                </div>

                                                <?php do_action('cfw_checkout_after_email'); ?>

                                                <div class="cfw-input-wrap cfw-check-input">
                                                    <?php if( ! WC()->checkout()->is_registration_required() && WC()->checkout()->is_registration_enabled() ): ?>
                                                        <input type="checkbox" id="createaccount" class="garlic-auto-save" name="createaccount" />
                                                        <label class="cfw-small" for="createaccount"><?php printf( apply_filters('cfw_create_account_checkbox_label', esc_html__('Create %s shopping account.', 'checkout-wc') ), apply_filters( 'cfw_create_account_site_name', get_bloginfo('name') ) ); ?></label>
                                                    <?php elseif ( WC()->checkout()->is_registration_required() ): ?>
                                                        <span class="cfw-small"><?php esc_html_e('If you do not have an account, we will create one for you.', 'checkout-wc'); ?></span>
                                                    <?php endif; ?>
                                                </div>

                                                <div id="cfw-account-password-slide">
		                                            <?php if ( ! apply_filters( 'cfw_registration_generate_password', true ) ):
			                                            cfw_form_field( 'account_password', array(
				                                            'type'              => 'password',
				                                            'label'             => cfw__( 'Create account password', 'woocommerce' ),
				                                            'required'          => true,
				                                            'placeholder'       => cfw_esc_attr__( 'Password', 'woocommerce' ),
				                                            'custom_attributes' => array(
					                                            'data-parsley-validate-if-empty' => '',
					                                            'data-parsley-trigger'           => 'keyup change focusout',
					                                            'data-parsley-group'             => 'account',
				                                            ),
			                                            ) );
		                                            endif; ?>
                                                </div>
                                            </div>

                                        <?php else: ?>
                                            <input type="hidden" name="billing_email" id="billing_email" value="<?php echo WC()->checkout()->get_value('billing_email'); ?>">

                                            <div class="cfw-have-acc-text cfw-small">
                                                <?php printf( esc_html__('Welcome back, %s (%s).', 'checkout-wc'), "<strong>" . wp_get_current_user()->display_name . "</strong>", wp_get_current_user()->user_email ); ?>

                                                <?php if ( apply_filters( 'cfw_show_logout_link', false ) ): ?>
                                                    <a class="cfw-link" href="<?php echo wp_logout_url( wc_get_checkout_url() ); ?>"><?php _e( 'Log out.', 'checkout-wc' ); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php do_action('cfw_checkout_after_login'); ?>
                                    </div>

                                    <?php do_action('cfw_checkout_before_customer_info_address'); ?>

                                    <div id="cfw-shipping-info" class="cfw-module">
		                                <?php do_action( sprintf( 'cfw_checkout_before_%s_address', WC()->cart->needs_shipping() ? 'shipping' : 'billing' ) ); ?>

                                        <h3 class="cfw-module-title">
                                            <?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ): ?>
	                                            <?php echo apply_filters('cfw_billing_shipping_address_heading', esc_html__( 'Billing and Shipping address', 'checkout-wc' ) ); ?>
			                                <?php elseif ( ! WC()->cart->needs_shipping() ): ?>
				                                <?php echo apply_filters('cfw_billing_address_heading', esc_html__( 'Billing address', 'checkout-wc' ) ); ?>
			                                <?php else: ?>
				                                <?php echo apply_filters('cfw_shipping_address_heading', esc_html__( 'Shipping address', 'checkout-wc' ) ); ?>
			                                <?php endif; ?>
                                        </h3>

                                        <div class="cfw-shipping-info-container cfw-parsley-shipping-details <?php cfw_address_class_wrap( WC()->cart->needs_shipping() ); ?>">
			                                <?php if ( ! WC()->cart->needs_shipping() ): ?>
				                                <?php cfw_get_billing_checkout_fields( WC()->checkout() ); ?>
			                                <?php else: ?>
				                                <?php cfw_get_shipping_checkout_fields( WC()->checkout() ); ?>
			                                <?php endif; ?>
                                        </div>

		                                <?php do_action( sprintf( 'cfw_checkout_after_%s_address', WC()->cart->needs_shipping() ? 'shipping' : 'billing' ) ); ?>
                                    </div>

                                    <?php do_action('cfw_checkout_after_customer_info_address'); ?>

                                    <?php do_action('cfw_checkout_before_customer_info_tab_nav'); ?>

                                    <div id="cfw-shipping-info-action" class="cfw-bottom-controls">
                                        <div class="previous-button">
                                            <?php cfw_return_to_cart_link(); ?>
                                        </div>
                                        <?php if ( WC()->cart->needs_shipping() && apply_filters('cfw_show_shipping_tab', true) === true ): ?>
                                            <?php cfw_continue_to_shipping_button(); ?>
                                        <?php else: ?>
                                            <?php cfw_continue_to_payment_button(); ?>
                                        <?php endif; ?>
                                    </div>

	                                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
                                    <?php do_action('cfw_checkout_after_customer_info_tab'); ?>
                                </div>

                                <!-- Shipping Method Panel -->
                                <div id="cfw-shipping-method" class="cfw-panel" style="<?php echo ( ! WC()->cart->needs_shipping() || apply_filters('cfw_show_shipping_tab', true) === false ) ? "display: none" : ""; ?>">
                                    <?php do_action('cfw_checkout_before_shipping_method_tab'); ?>

                                    <div id="cfw-shipping-details" class="cfw-module">
                                        <h3 class="cfw-module-title">
                                            <?php echo apply_filters('cfw_shipping_address_recap_heading', esc_html__( 'Shipping address', 'checkout-wc' ) ); ?>
                                        </h3>

                                        <div id="cfw-shipping-details-fields"></div>

                                        <div>
                                            <a href="javascript:;" data-tab="#cfw-customer-info" class="cfw-link cfw-tab-link"><?php esc_html_e( 'Edit shipping address', 'checkout-wc' ); ?></a>
                                        </div>
                                    </div>

                                    <?php do_action('cfw_checkout_before_shipping_methods'); ?>

                                    <?php if ( WC()->cart->needs_shipping() && apply_filters('cfw_show_shipping_tab', true) === true ) : ?>
                                        <div id="cfw-shipping-method-list" class="cfw-module">
                                            <h3 class="cfw-module-title">
                                                <?php echo apply_filters('cfw_shipping_method_heading', esc_html__( 'Shipping method', 'checkout-wc' ) ); ?>
                                            </h3>
                                            <span><?php esc_html_e( 'Select a shipping method:', 'checkout-wc' ); ?></span>
                                            <div id="shipping_method">
                                                <?php cfw_shipping_methods_html(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php do_action('cfw_checkout_after_shipping_methods'); ?>

                                    <?php do_action('cfw_checkout_before_shipping_method_tab_nav'); ?>

                                    <div id="cfw-shipping-action" class="cfw-bottom-controls">
                                        <div class="previous-button">
                                            <?php cfw_return_to_customer_information_link(); ?>
                                        </div>
                                        <div>
	                                        <?php cfw_continue_to_payment_button(); ?>
                                        </div>
                                    </div>

                                    <?php do_action('cfw_checkout_after_shipping_method_tab'); ?>
                                </div>

                                <!-- Payment Method Panel -->
                                <div id="cfw-payment-method" class="cfw-panel">
                                    <?php do_action('cfw_checkout_before_payment_method_tab'); ?>

                                    <?php cfw_payment_tab_content(); ?>

	                                <?php do_action('cfw_checkout_before_payment_method_tab_nav'); ?>

                                    <div id="cfw-payment-action" class="cfw-bottom-controls">
                                        <div class="previous-button">
			                                <?php if ( WC()->cart->needs_shipping() && apply_filters( 'cfw_show_shipping_tab', true ) === true ): ?>
                                                <?php cfw_return_to_shipping_method_link(); ?>
			                                <?php else: ?>
				                                <?php cfw_return_to_customer_information_link(); ?>
			                                <?php endif; ?>
                                        </div>

		                                <?php cfw_place_order(); ?>
                                    </div>

                                    <?php do_action('cfw_checkout_after_payment_methods_tab'); ?>
                                </div>
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
                                    <span id="cfw-mobile-total" class="total amount">
                                        <?php echo $cart->get_total(); ?>
                                    </span>
                                </div>
                            </div>

	                        <?php do_action( 'cfw_before_cart_summary' ); ?>
                            <div id="cfw-cart-details-collapse-wrap">
                                <div id="cfw_checkout_before_order_review">
		                            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
                                </div>

	                            <?php cfw_cart_html(); ?>

                                <div id="cfw-deductors-list" class="cfw-module">
                                    <?php if ( wc_coupons_enabled() ): ?>
                                    <div class="cfw-sg-container cfw-promo-row cfw-input-wrap-row">
                                        <div class="cfw-column-8">
                                            <div class="cfw-input-wrap cfw-text-input">
                                                <input type="text" name="cfw-promo-code" id="cfw-promo-code" size="30" title="<?php echo apply_filters( 'cfw_promo_code_label', esc_attr__(  'Enter Promo Code', 'checkout-wc' ) ); ?>" placeholder="<?php echo apply_filters( 'cfw_promo_code_label', esc_attr__(  'Enter Promo Code', 'checkout-wc' ) ); ?>">
                                            </div>
                                        </div>
                                        <div class="cfw-column-4">
                                            <div class="cfw-input-wrap cfw-button-input">
                                                <input type="button" name="cfw-promo-code-btn" id="cfw-promo-code-btn" class="cfw-def-action-btn" value="<?php esc_attr_e('Apply','checkout-wc'); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div id="cfw_checkout_after_order_review">
		                            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                                </div>

                                <?php cfw_totals_html(); ?>
                            </div>

                            <?php do_action( 'cfw_after_cart_summary' ); ?>
                        </div>
                    </div>
                </form>
		    <?php else: ?>
			    <?php do_action('cfw_checkout_form'); ?>
		    <?php endif; ?>

		    <?php do_action( 'woocommerce_after_checkout_form', WC()->checkout() ); ?>
		    <?php do_action('cfw_checkout_after_form'); ?>
	    <?php endif; ?>
    </div>
</main>
