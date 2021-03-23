<?php
namespace Objectiv\Plugins\Checkout\Core;

use Exception;
use Objectiv\Plugins\Checkout\Main;
use Objectiv\Plugins\Checkout\Managers\SettingsManager;
use Objectiv\Plugins\Checkout\Managers\ExtendedPathManager;
use Objectiv\Plugins\Checkout\Managers\TemplatesManager;

/**
 * Class Redirect
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package Objectiv\Plugins\Checkout\Core
 * @author Brandon Tassone <brandontassone@gmail.com>
 */
class Redirect {

	/**
	 * @since 1.0.0
	 * @access public
	 *
	 * @param SettingsManager $settings_manager
	 * @param ExtendedPathManager $path_manager
	 * @param TemplatesManager $templates_manager
	 * @param $version
	 */
	public static function checkout( $settings_manager, $path_manager, $templates_manager, $version ) {
		if ( apply_filters( 'cfw_load_checkout_template', Main::is_checkout() ) ) {
			/**
			 * PHP Warning / Notice Suppression
			 */
			if ( ! defined( 'CFW_DEV_MODE' ) || ! CFW_DEV_MODE ) {
				ini_set( 'display_errors', 'Off' );
			}

			/**
			 * Discourage Caching if Anyone Dares Try
			 */
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );

			/**
			 * Set Checkout Constant
			 */
			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

			// This seems to be a 3.5 requirement
			// Ensure gateways and shipping methods are loaded early.
			WC()->payment_gateways();
			WC()->shipping();

			// When on the checkout with an empty cart, redirect to cart page
			if ( WC()->cart->is_empty() ) {
				wc_add_notice( cfw__( 'Checkout is not available whilst your cart is empty.', 'woocommerce' ), 'notice' );
				wp_redirect( wc_get_cart_url() );
				exit;
			}

			// Allow global parameters accessible by the templates
			$global_template_parameters = apply_filters( 'cfw_template_global_params', array() );

			// Check cart contents for errors
			do_action( 'woocommerce_check_cart_items' );

			// Calc totals
			WC()->cart->calculate_totals();

			// Template conveniences items
			$global_template_parameters['woo']         = \WooCommerce::instance();         // WooCommerce Instance
			$global_template_parameters['checkout']    = WC()->checkout();                 // Checkout Object
			$global_template_parameters['cart']        = WC()->cart;                       // Cart Object
			$global_template_parameters['customer']    = WC()->customer;                   // Customer Object
			$global_template_parameters['css_classes'] = self::get_css_classes();

			do_action( 'cfw_checkout_loaded_pre_head' );

			/**
			 * Remove scripts and styles
			 *
			 * Do this at wp_head as well as wp_enqueue_scripts. This gives us two chances to win.
			 */
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 19 ); // 20 is when footer scripts are output
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 19 ); // 20 is when footer scripts are output

			// Setup default cfw_wp_head actions
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_meta_tags' ), 10, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_scripts' ), 20, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_page_title' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_wp_styles' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_styles' ), 40, 5 );

			$css_classes = array( 'checkout-wc', 'woocommerce', $templates_manager->getActiveTemplate()->get_slug() );

			// Output the contents of the <head></head> section
			self::head( $path_manager, $version, apply_filters( 'cfw_body_classes', $css_classes ), $settings_manager, $templates_manager );

			// Output the contents of the <body></body> section
			self::body( $templates_manager, $global_template_parameters, 'content.php' );

			// Output a closing </body> and closing </html> tag
			self::footer( $templates_manager, $settings_manager );

			// Exit out before WordPress can do anything else
			exit;
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 *
	 * @param SettingsManager $settings_manager
	 * @param ExtendedPathManager $path_manager
	 * @param TemplatesManager $templates_manager
	 * @param $version
	 */
	public static function order_pay( $settings_manager, $path_manager, $templates_manager, $version ) {
		if ( apply_filters( 'cfw_load_order_pay_template', is_checkout_pay_page() ) ) {
			global $wp;

			/**
			 * PHP Warning / Notice Suppression
			 */
			if ( ! defined( 'CFW_DEV_MODE' ) || ! CFW_DEV_MODE ) {
				ini_set( 'display_errors', 'Off' );
			}

			/**
			 * Discourage Caching if Anyone Dares Try
			 */
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );

			// Allow global parameters accessible by the templates
			$global_template_parameters = apply_filters( 'cfw_template_global_params', array() );

			// Template conveniences items
			$global_template_parameters['woo']         = \WooCommerce::instance();         // WooCommerce Instance
			$global_template_parameters['checkout']    = WC()->checkout();                 // Checkout Object
			$global_template_parameters['cart']        = WC()->cart;                       // Cart Object
			$global_template_parameters['customer']    = WC()->customer;                   // Customer Object
			$global_template_parameters['css_classes'] = self::get_css_classes();

			do_action( 'cfw_checkout_loaded_pre_head' );

			/**
			 * Remove scripts and styles
			 *
			 * Do this at wp_head as well as wp_enqueue_scripts. This gives us two chances to win.
			 */
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 19 ); // 20 is when footer scripts are output
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 19 ); // 20 is when footer scripts are output

			// Setup default cfw_wp_head actions
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_meta_tags' ), 10, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_scripts' ), 20, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_page_title' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_wp_styles' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_styles' ), 40, 5 );

			$css_classes = array( 'checkout-wc', 'woocommerce', $templates_manager->getActiveTemplate()->get_slug() );

			do_action( 'before_woocommerce_pay' );

			$order_id = absint( $wp->query_vars['order-pay'] );

			// Pay for existing order.
			if ( isset( $_GET['pay_for_order'], $_GET['key'] ) && $order_id ) { // WPCS: input var ok, CSRF ok.
				try {
					$order_key          = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // WPCS: input var ok, CSRF ok.
					$order              = wc_get_order( $order_id );
					$hold_stock_minutes = (int) get_option( 'woocommerce_hold_stock_minutes', 0 );

					// Order or payment link is invalid.
					if ( ! $order || $order->get_id() !== $order_id || ! hash_equals( $order->get_order_key(), $order_key ) ) {
						throw new Exception( cfw__( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ) );
					}

					if ( ! current_user_can( 'pay_for_order', $order->get_id() ) && ! is_user_logged_in() ) {
						wc_add_notice( cfw__( 'Please log in to your account below to continue to the payment form.', 'woocommerce' ), 'error' );
                    }

					// Logged in customer trying to pay for someone else's order.
					if ( is_user_logged_in() && ! current_user_can( 'pay_for_order', $order_id ) ) {
						throw new Exception( cfw__( 'This order cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ) );
					}

					// Does not need payment.
					if ( ! $order->needs_payment() ) {
						/* translators: %s: order status */
						throw new Exception( sprintf( cfw__( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), wc_get_order_status_name( $order->get_status() ) ) );
					}

					// Ensure order items are still stocked if paying for a failed order. Pending orders do not need this check because stock is held.
					if ( ! $order->has_status( wc_get_is_pending_statuses() ) ) {
						$quantities = array();

						foreach ( $order->get_items() as $item_key => $item ) {
							if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
								$product = $item->get_product();

								if ( ! $product ) {
									continue;
								}

								$quantities[ $product->get_stock_managed_by_id() ] = isset( $quantities[ $product->get_stock_managed_by_id() ] ) ? $quantities[ $product->get_stock_managed_by_id() ] + $item->get_quantity() : $item->get_quantity();
							}
						}

						foreach ( $order->get_items() as $item_key => $item ) {
							if ( $item && is_callable( array( $item, 'get_product' ) ) ) {
								$product = $item->get_product();

								if ( ! $product ) {
									continue;
								}

								if ( ! apply_filters( 'woocommerce_pay_order_product_in_stock', $product->is_in_stock(), $product, $order ) ) {
									/* translators: %s: product name */
									throw new Exception( sprintf( cfw__( 'Sorry, "%s" is no longer in stock so this order cannot be paid for. We apologize for any inconvenience caused.', 'woocommerce' ), $product->get_name() ) );
								}

								// We only need to check products managing stock, with a limited stock qty.
								if ( ! $product->managing_stock() || $product->backorders_allowed() ) {
									continue;
								}

								// Check stock based on all items in the cart and consider any held stock within pending orders.
								$held_stock     = ( $hold_stock_minutes > 0 ) ? wc_get_held_stock_quantity( $product, $order->get_id() ) : 0;
								$required_stock = $quantities[ $product->get_stock_managed_by_id() ];

								if ( $product->get_stock_quantity() < ( $held_stock + $required_stock ) ) {
									/* translators: 1: product name 2: quantity in stock */
									throw new Exception( sprintf( cfw__( 'Sorry, we do not have enough "%1$s" in stock to fulfill your order (%2$s available). We apologize for any inconvenience caused.', 'woocommerce' ), $product->get_name(), wc_format_stock_quantity_for_display( $product->get_stock_quantity() - $held_stock, $product ) ) );
								}
							}
						}
					}

					WC()->customer->set_props(
						array(
							'billing_country'  => $order->get_billing_country() ? $order->get_billing_country() : null,
							'billing_state'    => $order->get_billing_state() ? $order->get_billing_state() : null,
							'billing_postcode' => $order->get_billing_postcode() ? $order->get_billing_postcode() : null,
						)
					);
					WC()->customer->save();

					$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

					if ( count( $available_gateways ) ) {
						current( $available_gateways )->set_current();
					}

					$global_template_parameters['order']              = $order;
					$global_template_parameters['available_gateways'] = $available_gateways;
					$global_template_parameters['order_button_text']  = apply_filters( 'woocommerce_pay_order_button_text', cfw__( 'Pay for order', 'woocommerce' ) );
				} catch ( Exception $e ) {
					wc_add_notice( $e->getMessage(), 'error' );
				}
			} elseif ( $order_id ) {

				// Pay for order after checkout step.
				$order_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // WPCS: input var ok, CSRF ok.
				$order     = wc_get_order( $order_id );

				if ( $order && $order->get_id() === $order_id && hash_equals( $order->get_order_key(), $order_key ) ) {

					if ( $order->needs_payment() ) {

						$global_template_parameters['order']             = $order;
						$global_template_parameters['call_receipt_hook'] = true;

					} else {
						/* translators: %s: order status */
						wc_add_notice( sprintf( cfw__( 'This order&rsquo;s status is &ldquo;%s&rdquo;&mdash;it cannot be paid for. Please contact us if you need assistance.', 'woocommerce' ), wc_get_order_status_name( $order->get_status() ) ), 'error' );
					}
				} else {
					wc_add_notice( cfw__( 'Sorry, this order is invalid and cannot be paid for.', 'woocommerce' ), 'error' );
				}
			} else {
				wc_add_notice( cfw__( 'Invalid order.', 'woocommerce' ), 'error' );
			}

			// Output the contents of the <head></head> section
			self::head( $path_manager, $version, apply_filters( 'cfw_body_classes', $css_classes ), $settings_manager, $templates_manager );

			// Output the contents of the <body></body> section
			self::body( $templates_manager, $global_template_parameters, 'order-pay.php' );

			// Output a closing </body> and closing </html> tag
			self::footer( $templates_manager, $settings_manager );

			do_action( 'after_woocommerce_pay' );

			// Exit out before WordPress can do anything else
			exit;
		}
	}

	/**
	 * @param SettingsManager $settings_manager
	 * @param ExtendedPathManager $path_manager
	 * @param TemplatesManager $templates_manager
	 * @param $version
	 * @param \WC_Order $order
	 *
	 * @throws \WC_Data_Exception
	 * @since 2.39.0
	 * @access public
	 *
	 */
	public static function order_received( $settings_manager, $path_manager, $templates_manager, $version, $order ) {
		if ( apply_filters( 'cfw_load_order_received_template', Main::is_order_received_page() ) ) {
		    global $wp;

			/**
			 * PHP Warning / Notice Suppression
			 */
			if ( ! defined( 'CFW_DEV_MODE' ) || ! CFW_DEV_MODE ) {
				ini_set( 'display_errors', 'Off' );
			}

			/**
			 * Discourage Caching if Anyone Dares Try
			 */
			header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
			header( 'Cache-Control: post-check=0, pre-check=0', false );
			header( 'Pragma: no-cache' );

			// Allow global parameters accessible by the templates
			$global_template_parameters = apply_filters( 'cfw_template_global_params', array() );

			// Template conveniences items
			$global_template_parameters['woo']         = \WooCommerce::instance();         // WooCommerce Instance
			$global_template_parameters['checkout']    = WC()->checkout();                 // Checkout Object
			$global_template_parameters['cart']        = WC()->cart;                       // Cart Object
			$global_template_parameters['customer']    = WC()->customer;                   // Customer Object
			$global_template_parameters['css_classes'] = self::get_css_classes();

			// Empty awaiting payment session.
			unset( WC()->session->order_awaiting_payment );

			// In case order is created from admin, but paid by the actual customer, store the ip address of the payer.
			if ( $order ) {
				$order->set_customer_ip_address( \WC_Geolocation::get_ip_address() );
				$order->save();
			}

			$valid_order_statuses = array_flip( array_intersect_key( array_flip( (array)$settings_manager->get_setting('thank_you_order_statuses') ), wc_get_order_statuses() ) );

			$global_template_parameters['order']         = $order;
			$global_template_parameters['show_shipping'] = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
			$global_template_parameters['order_statuses'] = str_replace( 'wc-', '',  $valid_order_statuses);

			do_action( 'cfw_checkout_loaded_pre_head' );

			/**
			 * Remove scripts and styles
			 *
			 * Do this at wp_head as well as wp_enqueue_scripts. This gives us two chances to win.
			 */
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 100000 );
			add_action( 'wp_enqueue_scripts', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 100000 );
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_styles' ), 19 ); // 20 is when footer scripts are output
			add_action( 'wp_footer', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'remove_scripts' ), 19 ); // 20 is when footer scripts are output

			// Setup default cfw_wp_head actions
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_meta_tags' ), 10, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_scripts' ), 20, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_page_title' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_wp_styles' ), 30, 4 );
			add_action( 'cfw_wp_head', array( 'Objectiv\Plugins\Checkout\Core\Redirect', 'output_custom_styles' ), 40, 5 );

			$css_classes = array( 'checkout-wc', 'woocommerce', $templates_manager->getActiveTemplate()->get_slug() );

			// Output the contents of the <head></head> section
			self::head( $path_manager, $version, apply_filters( 'cfw_body_classes', $css_classes ), $settings_manager, $templates_manager );

			// Output the contents of the <body></body> section
			self::body( $templates_manager, $global_template_parameters, 'thank-you.php' );

			// Output a closing </body> and closing </html> tag
			self::footer( $templates_manager, $settings_manager );

			// Exit out before WordPress can do anything else
			exit;
		}
    }

	/**
	 * Initial classes for visibility states
	 *
	 * @return string
	 */
	public static function get_css_classes() {
		$css_classes = [];

		if ( ! WC()->cart->needs_payment() ) {
			$css_classes[] = 'cfw-payment-false';
		}

		if ( ! WC()->cart->needs_shipping_address() ) {
			$css_classes[] = 'cfw-shipping-address-false';
		}

		return implode( ' ', $css_classes );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 */
	public static function title_block() {
		// We use this instead of _wp_render_title_tag because it requires the theme support title-tag capability.
		echo '<title>' . wp_get_document_title() . '</title>' . "\n";
	}

	public static function wp_styles() {
		wp_print_styles();
	}

	/**
	 * @since 1.0.0
	 * @access public
	 *
	 * @param ExtendedPathManager $path_manager
	 * @param string $version
	 * @param array $classes
	 * @param SettingsManager $settings_manager
	 * @param TemplatesManager $templates_manager
	 */
	public static function head( $path_manager, $version, $classes, $settings_manager, $templates_manager ) {
		$classes[] = ( wp_is_mobile() ) ? 'wp-is-mobile' : '';

		if ( Main::is_checkout() ) {
			$classes[] = 'checkout';
		}
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<?php self::cfw_wp_head( $path_manager, $version, $classes, $settings_manager, $templates_manager ); ?>
		</head>
		<body class="<?php echo implode( ' ', $classes ); ?>">
		<?php
		$templates_manager->getActiveTemplate()->view( 'header.php' );
	}

	/**
	 *
	 */
	public static function output_meta_tags() {
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width">
		<?php
	}

	/**
	 * @param ExtendedPathManager $path_manager
	 * @param string $version
	 * @param array $classes
	 * @param SettingsManager $settings_manager
	 */
	public static function output_custom_scripts( $path_manager, $version, $classes, $settings_manager ) {
		echo $settings_manager->get_setting( 'header_scripts' );
	}

	/**
	 * @param ExtendedPathManager $path_manager
	 * @param string $version
	 * @param array $classes
	 * @param SettingsManager $settings_manager
	 */
	public static function output_page_title() {
		self::title_block();
	}

	public static function output_wp_styles() {
		self::wp_styles();
	}

	/**
	 * @param ExtendedPathManager $path_manager
	 * @param string $version
	 * @param array $classes
	 * @param SettingsManager $settings_manager
	 * @param TemplatesManager $templates_manager
	 */
	public static function output_custom_styles( $path_manager, $version, $classes, $settings_manager, $templates_manager ) {
		// Get logo attachment ID if available
		$logo_attachment_id = $settings_manager->get_setting( 'logo_attachment_id' );
		$active_theme       = $templates_manager->getActiveTemplate()->get_slug();

		$supports = $templates_manager->getActiveTemplate()->get_supports();
		?>
		<style>
			<?php if ( in_array( 'header-background', $supports ) ) : ?>
				#cfw-header {
					background: <?php echo $settings_manager->get_setting( 'header_background_color', array( $active_theme ) ); ?>;

					<?php if ( strtolower( $settings_manager->get_setting( 'header_background_color', array( $active_theme ) ) ) !== '#ffffff' ) : ?>
					margin-bottom: 2em;
					<?php endif; ?>
				}
			<?php endif; ?>

			<?php if ( in_array( 'footer-background', $supports ) ) : ?>
				#cfw-footer {
					color: <?php echo $settings_manager->get_setting( 'footer_color', array( $active_theme ) ); ?>;
					background: <?php echo $settings_manager->get_setting( 'footer_background_color', array( $active_theme ) ); ?>;

					<?php if ( strtolower( $settings_manager->get_setting( 'footer_background_color', array( $active_theme ) ) ) !== '#ffffff' ) : ?>
					margin-top: 2em;
					<?php endif; ?>
				}
			<?php endif; ?>

			<?php if ( in_array( 'summary-background', $supports ) ) : ?>
				#cfw-cart-details, #cfw-cart-details:before {
					background: <?php echo $settings_manager->get_setting( 'summary_background_color', array( $active_theme ) ); ?> !important;
				}
			<?php endif; ?>

			#cfw-cart-details-arrow, .lost_password a {
				color: <?php echo $settings_manager->get_setting( 'link_color', array( $active_theme ) ); ?> !important;
				fill: <?php echo $settings_manager->get_setting( 'link_color', array( $active_theme ) ); ?> !important;
			}
			.previous-button a, .cfw-link, .woocommerce-remove-coupon {
				color: <?php echo $settings_manager->get_setting( 'link_color', array( $active_theme ) ); ?> !important;
			}

			.cfw-bottom-controls .cfw-primary-btn, .place-order .cfw-primary-btn {
				background-color: <?php echo $settings_manager->get_setting( 'button_color', array( $active_theme ) ); ?>;
				color: <?php echo $settings_manager->get_setting( 'button_text_color', array( $active_theme ) ); ?>;
			}

			.cfw-def-action-btn, .woocommerce-button {
				background-color: <?php echo $settings_manager->get_setting( 'secondary_button_color', array( $active_theme ) ); ?>;
				color: <?php echo $settings_manager->get_setting( 'secondary_button_text_color', array( $active_theme ) ); ?>;
			}

			<?php if ( ! empty( $logo_attachment_id ) ) : ?>
			.cfw-logo .logo {
				background: transparent url( <?php echo wp_get_attachment_url( $logo_attachment_id ); ?> ) no-repeat;
				background-size: contain;
				background-position: left center;
			}
			<?php else : ?>
			.cfw-logo .logo {
				<?php if ( in_array( 'header-background', $supports ) ) : ?>
				background: <?php echo $settings_manager->get_setting( 'header_background_color', array( $active_theme ) ); ?>;
				<?php endif; ?>
				height: auto !important;
				width: auto;
				margin: 20px auto;
				color: <?php echo $settings_manager->get_setting( 'header_text_color', array( $active_theme ) ); ?>;
			}
			.cfw-logo .logo:after {
				content: "<?php echo get_bloginfo( 'name' ); ?>";
				font-size: 2em;
			}
			<?php endif; ?>

			.cfw-input-wrap > input[type="text"]:focus, .cfw-input-wrap > input[type="email"]:focus, .cfw-input-wrap > input[type="tel"]:focus, .cfw-input-wrap > input[type="number"]:focus, .cfw-input-wrap > input[type="password"]:focus, .cfw-input-wrap select:focus, .cfw-input-wrap textarea:focus {
				box-shadow: 0 0 0 2px <?php echo $settings_manager->get_setting( 'button_color', array( $active_theme ) ); ?>;
			}

			.woocommerce-info, .cfw-container > div > ul.woocommerce-error {
				padding: 1em 1.618em;
				margin-bottom: 1.3em;
				background-color: <?php echo $settings_manager->get_setting( 'secondary_button_color', array( $active_theme ) ); ?>;
				margin-left: 0;
				border-radius: 2px;
				color: #fff;
				clear: both;
				border-left: .6180469716em solid rgba(0, 0, 0, 0.15);
				box-sizing: border-box;
				width: 100%;
				display: inline-block;
			}
			<?php echo $settings_manager->get_setting( 'custom_css', array( $active_theme ) ); ?>;
		</style>
		<?php
	}

	/**
	 * @param ExtendedPathManager $path_manager
	 * @param string $version
	 * @param array $classes
	 * @param SettingsManager $settings_manager
	 * @param TemplatesManager $templates_manager
	 */
	public static function cfw_wp_head( $path_manager, $version, $classes, $settings_manager, $templates_manager ) {
		// Make sure gateways load before we call wp_head()
		WC()->payment_gateways->get_available_payment_gateways();
		\WC_Payment_Gateways::instance();

		wp_head();
		do_action_ref_array( 'cfw_wp_head', array( $path_manager, $version, $classes, $settings_manager, $templates_manager ) );
	}

	/**
	 * Remove specifically excluded styles
	 */
	public static function remove_styles() {
		$blocked_style_handles = apply_filters( 'cfw_blocked_style_handles', array() );

		foreach ( $blocked_style_handles as $blocked_style_handle ) {
			wp_dequeue_style( $blocked_style_handle );
			wp_deregister_style( $blocked_style_handle );
		}
	}

	/**
	 * Remove specifically excluded scripts
	 */
	public static function remove_scripts() {
		$blocked_script_handles = apply_filters( 'cfw_blocked_script_handles', array() );

		foreach ( $blocked_script_handles as $blocked_script_handle ) {
			wp_dequeue_script( $blocked_script_handle );
			wp_deregister_script( $blocked_script_handle );
		}
	}

	/**
	 * @since 1.0.0
	 * @access public
	 * @param TemplatesManager $templates_manager
	 * @param array $global_template_parameters
	 * @param string $template_file
	 */
	public static function body( $templates_manager, $global_template_parameters, $template_file ) {
		// Fire off an action before we load the template pieces
		do_action( 'cfw_template_before_load', $template_file );

		// Load content template
		$templates_manager->getActiveTemplate()->view( $template_file, $global_template_parameters );

		// Fire off an action after we load the template pieces
		do_action( 'cfw_template_after_load', $template_file );
	}

	/**
	 * @since 1.0.0
	 * @access public
	 *
	 * @param TemplatesManager $templates_manager
	 * @param SettingsManager $settings_manager
	 */
	public static function footer( $templates_manager, $settings_manager ) {
		$templates_manager->getActiveTemplate()->view( 'footer.php' );

		do_action( 'cfw_wp_footer_before_scripts' );

		// Prevent themes and plugins from injecting HTML on wp_footer
		echo '<div id="wp_footer">';
		wp_footer();
		echo '</div>';

		echo $settings_manager->get_setting( 'footer_scripts' );

		do_action( 'cfw_wp_footer' );
		?>
		</body>
		</html>
		<?php
	}
}
