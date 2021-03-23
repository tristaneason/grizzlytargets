import { Compatibility } 			from "./Compatibility";
import { Main } 					from "../Main";
import {EasyTabDirection, EasyTabService} from "../Services/EasyTabService";

declare let jQuery: any;

export class KlarnaPayments extends Compatibility {
	/**
	 * @param {Main} main The Main object
	 * @param {any} params Params for the child class to run on load
	 * @param {boolean} load Should load be fired on instantiation
	 */
	constructor( main: Main, params, load: boolean = true ) {
		super( main, params, load, 'KlarnaPayments' );
	}

	load( main: Main ): void {
		let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

		easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => this.refreshKlarnaPayments( main, event, clicked, target ) );

		jQuery( document.body ).on( 'cfw_updated_checkout', function() {
			let same_as_shipping = jQuery( `input[name="bill_to_different_address"]:checked` ).val();

			if ( same_as_shipping === 'same_as_shipping' ) {
				jQuery( '#billing_first_name' ).val( jQuery( '#shipping_first_name' ).val() );
				jQuery( '#billing_last_name' ).val( jQuery( '#shipping_last_name' ).val() );
				jQuery( '#billing_address_1' ).val( jQuery( '#shipping_address_1' ).val() );
				jQuery( '#billing_address_2' ).val( jQuery( '#shipping_address_2' ).val() );
				jQuery( '#billing_company' ).val( jQuery( '#shipping_company' ).val() );
				jQuery( '#billing_country' ).val( jQuery( '#shipping_country' ).val() );
				jQuery( '#billing_state' ).val( jQuery( '#shipping_state' ).val() );
				jQuery( '#billing_postcode' ).val( jQuery( '#shipping_postcode' ).val() );
			}
		} );
	}

	refreshKlarnaPayments( main: Main, event: any, clicked: any, target: any ): void {
		let easyTabDirection: EasyTabDirection = EasyTabService.getTabDirection( target );
		let easyTabID: string = EasyTabService.getTabId( easyTabDirection.target );
		let paymentContainerId: string = main.tabContainer.tabContainerSectionBy( 'name', 'payment_method' ).jel.attr( 'id' );

		if( paymentContainerId === easyTabID ) {
			main.force_updated_checkout = true;
			main.tabContainer.triggerUpdateCheckout();
		}
	}
}