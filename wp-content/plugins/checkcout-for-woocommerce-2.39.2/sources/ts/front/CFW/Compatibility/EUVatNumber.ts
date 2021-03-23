import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;
declare let wc_eu_vat_params: any;

export class EUVatNumber extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'EUVatNumber' );
    }

    load( main: Main ): void {
        // If shipping country or ship to different address value changes, we need to catch it
        jQuery( 'form.checkout' ).on( 'change', 'select#shipping_country, input[name="bill_to_different_address"]', function() {
            let country         = jQuery( 'select#shipping_country' ).val();
            let check_countries = wc_eu_vat_params.eu_countries;
            let same_as_shipping = jQuery( 'input[name="bill_to_different_address"]:checked' ).val();

            if ( country && jQuery.inArray( country, check_countries ) >= 0 && same_as_shipping === "same_as_shipping" ) {
                // If shipping country is in EU and same as shipping address is checked, show vat number
                jQuery( '#woocommerce_eu_vat_number' ).fadeIn();
            } else if ( country &&  jQuery.inArray( country, check_countries ) === -1 && same_as_shipping === "same_as_shipping" ) {
                // If shipping country is not in EU and same as shipping address is checked, hide vat number
                jQuery( '#woocommerce_eu_vat_number' ).fadeOut();
            } else {
                // Otherwise, trigger a change on the billing country so that EU Vat Number's native JS will run
                jQuery( 'select#billing_country' ).change();
            }
        });

        // Make sure that on refresh, we trigger a change on shipping country so that the field renders in the right state
        jQuery( window ).load( function() {
            jQuery( 'select#shipping_country' ).change();
        } );
    }
}