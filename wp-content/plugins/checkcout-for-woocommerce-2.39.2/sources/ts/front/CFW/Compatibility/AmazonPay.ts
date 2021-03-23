import { Compatibility } from "./Compatibility";
import {Main} from "../Main";
import {Alert, AlertInfo} from "../Elements/Alert";

declare let OffAmazonPayments: any;
declare let amazon_payments_advanced_params: any;
declare let jQuery: any;

export class AmazonPay extends Compatibility {
	/**
	 * @param {Main} main The Main object
	 * @param {any} params Params for the child class to run on load
	 * @param {boolean} load Should load be fired on instantiation
	 */
	constructor( main: Main, params, load: boolean = true ) {
		super( main, params, load, 'AmazonPay' );
	}

	/**
	 * Run the compatibility
	 *
	 * @param main
	 */
	load( main: Main ): void {
		const errorKey = 'cfw_amazon_redirect_error';
		let easyTabsWrap: any = main.easyTabService.easyTabsWrap;
		let getParams = this.getUrlParamsMap();

		jQuery( window.document ).on( 'wc_amazon_pa_widget_ready', () => {
			jQuery( '#cfw-first-for-plugins, #cfw-last-for-plugins, #cfw-email-wrap' ).addClass( 'cfw-floating-label' );
		});

		if( getParams[errorKey] !== undefined ) {
			let alertInfo: AlertInfo = {
				type: "error",
				message: jQuery( '.woocommerce-error' ).html(),
				cssClass: "cfw-alert-error"
			};

			jQuery( '.woocommerce-error' ).remove();

			let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
			alert.addAlert();

			jQuery( '#checkout' ).addClass( 'has-overlay' );
			jQuery( '#cfw-deductors-list' ).addClass( 'has-overlay' );
			jQuery( '#checkout' ).append( '<div class="amazon-pay-overlay"></div>' );
			jQuery( '#cfw-deductors-list' ).append( '<div class="amazon-pay-overlay"></div>' );

			if( amazon_payments_advanced_params !== undefined &&
				amazon_payments_advanced_params.declined_code !== undefined &&
				amazon_payments_advanced_params.declined_code === 'InvalidPaymentMethod' ) {

				setTimeout(() => {
					location.href = location.href = location.href.replace(`${errorKey}=1`, '' );
				}, 3000);
			}

			return;
		}

		/**
		 * If the OffAmazonPayments and amazon_payments_advanced_params exist we can then check to see if there is a reference
		 * id set. If not we are not logged in. If there is we are logged in.
		 */
		try {
			if ( OffAmazonPayments !== undefined &&
				amazon_payments_advanced_params !== undefined &&
				( amazon_payments_advanced_params.reference_id !== "" || amazon_payments_advanced_params.access_token !== "")
			) {
				jQuery( window ).on( 'load cfw_updated_checkout', () => {
                    this.cleanUpExtraStuff();
				});

				easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => this.amazonRefresh());

				(<any>window).addEventListener( 'cfw-checkout-failed-before-error-message', ({detail}) => {
					let response = detail.response;

					if ( response.reload ) {
						let errorParam = `&${errorKey}=1`;

						location.href = amazon_payments_advanced_params.redirect + errorParam;
					}
				});
			}
		} catch( error ) {
			console.log( error );
		}
	}

	getUrlParamsMap() {
		let map = {};
		let urlGetParams = location.href.split( '&' ).splice(1).map( paramSet => {
			let keyValue = paramSet.split( '=' );
			let key = keyValue[0];
			let value = keyValue[1];

			map[key] = value;
		});

		return map;
	}

	cleanUpExtraStuff() {
        jQuery( '#cfw-billing-methods .create-account' ).remove();
        jQuery( '#payment-info-separator-wrap' ).hide();
        jQuery( '#cfw-shipping-same-billing' ).hide();
        jQuery( '#cfw-billing-methods > .cfw-module-title' ).hide();
        jQuery( '#cfw-shipping-info > .cfw-module-title' ).hide();
        jQuery( '#cfw-payment-method > .cfw-module-title' ).hide();
	}

	/**
	 * Refreshes and loads the split amazon setup
	 */
	amazonRefresh() {
		OffAmazonPayments.Widgets.Utilities.setup();
        this.cleanUpExtraStuff();
	}
}