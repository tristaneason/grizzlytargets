<?php

namespace Objectiv\Plugins\Checkout\Compatibility;

use Objectiv\Plugins\Checkout\Compatibility\Gateways\AfterPayKrokedil;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\AmazonPay;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\Braintree;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\BraintreeForWooCommerce;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\In3;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\InpsydePayPalPlus;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\KlarnaCheckout;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\KlarnaPayment;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\PayPalCheckout;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\PayPalForWooCommerce;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\Square;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\Square1x;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\SquareRecurring;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\Stripe;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\ToCheckout;
use Objectiv\Plugins\Checkout\Compatibility\Gateways\WooSquarePro;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\ActiveCampaign;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\BlueCheck;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CheckoutAddressAutoComplete;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CheckoutFieldEditor;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CheckoutManager;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\Chronopost;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CO2OK;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CountryBasedPayments;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\CraftyClicks;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\DiviUltimateFooter;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\DiviUltimateHeader;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\Elementor;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\EUVATNumber;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\ExtraCheckoutFieldsBrazil;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\FacebookForWooCommerce;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\Fattureincloud;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\GermanMarketplace;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\GoogleAnalyticsPro;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\IndeedAffiliatePro;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\JupiterXCore;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\MailChimpforWooCommerce;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\MartfuryAddons;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\MixPanel;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\MondialRelay;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\MyCredPartialPayments;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\NLPostcodeChecker;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\OneClickUpsells;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\OnePageCheckout;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\OrderDeliveryDate;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\OrderDeliveryDateLite;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\OxygenBuilder;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\PixelCaffeine;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\PostNL;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\SendCloud;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\ShipMondo;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\SkyVergeCheckoutAddons;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\SkyVergeSocialLogin;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\SmartSend;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\StrollikCore;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\SUMOPaymentPlans;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\Tickera;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\EnhancedEcommerceGoogleAnalytics;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\UltimateRewardsPoints;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WCFieldFactory;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\Webshipper;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceAddressValidation;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceCheckoutFieldEditor;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceCore;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceGermanized;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommercePriceBasedOnCountry;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceSmartCoupons;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooCommerceSubscriptions;
use Objectiv\Plugins\Checkout\Compatibility\Plugins\WooFunnelsOrderBumps;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Astra;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Atelier;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Atik;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Avada;
use Objectiv\Plugins\Checkout\Compatibility\Themes\BeaverBuilder;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Divi;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Electro;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Flevr;
use Objectiv\Plugins\Checkout\Compatibility\Themes\GeneratePress;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Jupiter;
use Objectiv\Plugins\Checkout\Compatibility\Themes\JupiterX;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Listable;
use Objectiv\Plugins\Checkout\Compatibility\Themes\OceanWP;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Optimizer;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Porto;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Savoy;
use Objectiv\Plugins\Checkout\Compatibility\Themes\The7;
use Objectiv\Plugins\Checkout\Compatibility\Themes\TMOrganik;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Verso;
use Objectiv\Plugins\Checkout\Compatibility\Themes\Zidane;

/**
 * Class Compatibility
 *
 * @link objectiv.co
 * @since 1.0.1
 * @package Objectiv\Plugins\Checkout\Core
 * @author Clifton Griffin <clif@checkoutwc.com>
 */
class Manager {
	public function __construct() {
		/**
		 * Plugins
		 */
		// WooCommerce Core
		new WooCommerceCore();

		// MixPanel
		new MixPanel();

		// Checkout Add-ons
		new SkyVergeCheckoutAddons();

		// Tickera
		new Tickera();

		// Pixel Caffeine
		new PixelCaffeine();

		// One Click Upsells
		new OneClickUpsells();

		// Google Analytics Pro
		new GoogleAnalyticsPro();

		// One Page Checkout
		new OnePageCheckout();

		// WooCommerce Subscriptions
		new WooCommerceSubscriptions();

		// WooCommerce Germanized
		new WooCommerceGermanized();

		// CraftyClicks
		new CraftyClicks();

		// WooCommerce Checkout Manager
		new CheckoutManager();

		// Woo Checkout Field Editor Pro
		new CheckoutFieldEditor();

		// Checkout Address Autocomplete
		new CheckoutAddressAutoComplete();

		// NL Postcode Checker
		new NLPostcodeChecker();

		// WooCommerce PostNL
		new PostNL();

		// MailChimp for WooCommerce
		new MailChimpforWooCommerce();

		// ActiveCampaign for WooCommerce
		new ActiveCampaign();

		// Ultimate Points and Rewards
		new UltimateRewardsPoints();

		// Smart Coupons
		new WooCommerceSmartCoupons();

		// EU VAT Number
		new EUVATNumber();

		// SkyVerge WooCommerce Social Login
		new SkyVergeSocialLogin();

		// WooCommerce Price Based on Country
		new WooCommercePriceBasedOnCountry();

		// Facebook for WooCommerce
		new FacebookForWooCommerce();

		// Webshipper
		new Webshipper();

		// Smart Send
		new SmartSend();

		// Order Delivery Date
		new OrderDeliveryDate();
		new OrderDeliveryDateLite();

		// BlueCheck
		new BlueCheck();

		// WooFunnels Order Bumps
		new WooFunnelsOrderBumps();

		// Martfury Addons
		new MartfuryAddons();

		// WC Field Factory
		new WCFieldFactory();

		// Mondial Relay
		new MondialRelay();

		// SUMO Payment Plans
		new SUMOPaymentPlans();

		// WooCommerce Address Validation
		new WooCommerceAddressValidation();

		// Elementor
		new Elementor();

		// SendCloud
		new SendCloud();

		// CO2OK
		new CO2OK();

		// Divi Ultimate Header
		new DiviUltimateHeader();

		// Divi Ultimate Footer
		new DiviUltimateFooter();

		// Extra Checkout Fields for Brazil
		new ExtraCheckoutFieldsBrazil();

		// MyCred Partial Payments
		new MyCredPartialPayments();

		// Country Based Payments
		new CountryBasedPayments();

		// German Marketplace
		new GermanMarketplace();

		// Strollik Core
		new StrollikCore();

		// WooCommerce Checkout Field Editor
		new WooCommerceCheckoutFieldEditor();

		// Indeed Affiliate Pro
		new IndeedAffiliatePro();

		// ShipMondo
		new ShipMondo();

		// Chronopost
		new Chronopost();

		// JupiterX Core
		new JupiterXCore();

		// Oxygen Builder
		new OxygenBuilder();

		// Fattureincloud
		new Fattureincloud();


		/**
		 * Gateways
		 */
		// PayPal Express
		new PayPalCheckout( $this );

		// Stripe 4.x
		new Stripe();

		// Enhanced Ecommerce Google Analytics
		new EnhancedEcommerceGoogleAnalytics();

		// PayPal for WooCommerce
		new PayPalForWooCommerce( $this );

		// Braintree
		new Braintree();

		// Braintree for WooCommerce
		new BraintreeForWooCommerce();

		// Amazon Pay
		new AmazonPay();

		// Klarna
		new KlarnaCheckout();

		// Klarna Payment
		new KlarnaPayment();

		// AfterPay
		new AfterPayKrokedil();

		// ToCheckout
		new ToCheckout();

		// Square
		new Square();

		// Square 1.x
		new Square1x();

		// Square Recurring
		new SquareRecurring();

		// In3
		new In3();

		// Inpsyde PayPal Plus
		new InpsydePayPalPlus();

		// WooSquarePro
		new WooSquarePro();


		/**
		 * Themes
		 */
		// Avada
		new Avada();

		// Porto
		new Porto();

		// GeneratePress / GP Premium
		new GeneratePress();

		// TM Organik / any theme that uses InsightFramework
		new TMOrganik();

		// Beaver Builder Theme
		new BeaverBuilder();

		// Astra
		new Astra();

		// Savoy
		new Savoy();

		// OceanWP
		new OceanWP();

		// Atelier
		new Atelier();

		// Jupiter
		new Jupiter();

		// The7
		new The7();

		// Zidane
		new Zidane();

		// Atik
		new Atik();

		// Optimizer
		new Optimizer();

		// Verso
		new Verso();

		// Listable
		new Listable();

		// Flevr
		new Flevr();

		// Divi
		new Divi();

		// Electro
		new Electro();

		// JupiterX
		new JupiterX();
	}
}
