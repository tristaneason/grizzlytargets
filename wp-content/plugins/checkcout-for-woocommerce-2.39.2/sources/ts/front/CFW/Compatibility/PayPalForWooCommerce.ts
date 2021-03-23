import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class PayPalForWooCommerce extends Compatibility {
	/**
	 * @param {Main} main The Main object
	 * @param {any} params Params for the child class to run on load
	 * @param {boolean} load Should load be fired on instantiation
	 */
	constructor( main: Main, params, load: boolean = true ) {
		super( main, params, load, 'PayPalForWooCommerce' );
	}

	load( main: Main ): void {
		let interval = 0;

		jQuery( document ).ready( function() {

			// Don't run the below if on express checkout review page
			let is_set_class = jQuery(`div`).is(`.express-provided-address`);

			if ( is_set_class ) {
				return;
			}

			jQuery( window ).on( 'payment_method_selected cfw_updated_checkout', () => {
				let max_iterations = 200;
				let iterations = 0;

				interval = setInterval(() => {
					let main: Main = Main.instance;

					if ( jQuery( 'input[name="payment_method"]:checked' ).is( '#payment_method_paypal_express' ) && jQuery( '.angelleye_smart_button_checkout_bottom' ).first().is( ':empty' ) ) {
						main.tabContainer.triggerUpdatedCheckout();
					} else if( ! jQuery( 'input[name="payment_method"]:checked' ).is( '#payment_method_paypal_express' ) || ! jQuery( '.angelleye_smart_button_checkout_bottom' ).first().is( ':empty' ) ) {
						clearInterval( interval );
					} else if( iterations >= max_iterations ) {
						// Give up
						clearInterval( interval );
					} else {
						iterations++;
					}
				}, 50);
			} );

			jQuery( window ).on( 'cfw_updated_checkout', () => {
				let isPPEC = jQuery( 'input[name="payment_method"]:checked' ).is( '#payment_method_paypal_express' );
				jQuery( '#place_order' ).toggle( ! isPPEC );
				jQuery( '.angelleye_smart_button_checkout_bottom' ).toggle( isPPEC );
			} );
		} );
	}
}