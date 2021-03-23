import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class WooCommerceAddressValidation extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'WooCommerceAddressValidation' );
    }

    load( main: Main ): void {
        jQuery( document.body ).on( 'load', () => {
            // Trigger window resize event for plugins that need it
            this.reactivateBillingAddress();
        } );

        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
            this.reactivateBillingAddress();
        } );

        jQuery( '[type="radio"][name="bill_to_different_address"]' ).on( 'change click', () => {
            this.reactivateBillingAddress();
        } );
    }

    resizeWindow(): void {
        setTimeout( function() {
            jQuery( window ).resize();
        }, 400 );
    }

    reactivateBillingAddress(): void {
        if ( jQuery( '[type="radio"][name="bill_to_different_address"]:checked' ).val() === 'different_from_shipping' ) {
            this.deactivate_billing();
            this.activate_billing();

            this.resizeWindow();
        }
    }

    activate_billing(): void {
        let smartyui = jQuery('.deactivated.smarty-addr-billing_address_1' );

        if ( smartyui.length ) {
            smartyui.push( smartyui[0].parentElement );
            smartyui.removeClass( 'deactivated' );
            smartyui.addClass( 'activated' );
            smartyui.show();
        }
    }

    deactivate_billing(): void {
        let smartyui = jQuery( '.smarty-addr-billing_address_1:visible' );
        let autocompleteui = jQuery( '.smarty-autocomplete.smarty-addr-billing_address_1' );

        if ( smartyui.length ) {
            smartyui.addClass( 'deactivated' );
            smartyui.parent().addClass( 'deactivated' );
            autocompleteui.addClass( 'deactivated' );
            smartyui.hide();
            smartyui.parent().hide();
            autocompleteui.hide();
        }
    }
}