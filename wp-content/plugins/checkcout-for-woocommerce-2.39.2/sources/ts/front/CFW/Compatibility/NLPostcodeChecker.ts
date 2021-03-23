import { Compatibility } from "./Compatibility";
import { Main } from "../Main";
import {FormElement} from "../Elements/FormElement";

declare let jQuery: any;

export class NLPostcodeChecker extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'MondialRelay' );
    }

    load( main: Main ): void {
        jQuery( 'body' ).on( 'wpo_wcnlpc_fields_updated', () => {
            // Shipping address
            let shipping_street_name = jQuery( '#shipping_street_name' );
            let shipping_house_number = jQuery( '#shipping_house_number' );
            let shipping_house_number_suffix = jQuery( '#shipping_house_number_suffix' );
            let shipping_city = jQuery( '#shipping_city' );
            let shipping_address_1 = '';

            // Fix float labels
            if ( shipping_street_name.val() ) {
                shipping_street_name.parent().addClass( FormElement.labelClass );
            }

            if ( shipping_city.val() ) {
                shipping_city.parent().addClass( FormElement.labelClass );
            }

            // Set address 1
            if ( shipping_street_name.val() && shipping_house_number.val() ) {
                shipping_address_1 = shipping_street_name.val() + ' ' + shipping_house_number.val();
            }

            if ( shipping_house_number_suffix.val() && shipping_address_1 ) {
                shipping_address_1 = shipping_address_1 + '-' + shipping_house_number_suffix.val();
            }

            if ( shipping_address_1 ) {
                jQuery( '#shipping_address_1' ).val( shipping_address_1 );
            }

            // Billing address
            let billing_street_name = jQuery( '#billing_street_name' );
            let billing_house_number = jQuery( '#billing_house_number' );
            let billing_house_number_suffix = jQuery( '#billing_house_number_suffix' );
            let billing_city = jQuery( '#billing_city' );
            let billing_address_1 = '';

            // Fix float labels
            if ( billing_street_name.val() ) {
                billing_street_name.parent().addClass( FormElement.labelClass );
            }

            if ( billing_city.val() ) {
                billing_city.parent().addClass( FormElement.labelClass );
            }

            // Set address 1
            if ( billing_street_name.val() && billing_house_number.val() ) {
                billing_address_1 = billing_street_name.val() + ' ' + billing_house_number.val();
            }

            if ( billing_house_number_suffix.val() && billing_address_1 ) {
                billing_address_1 = billing_address_1 + '-' + billing_house_number_suffix.val();
            }

            if ( billing_address_1 ) {
                jQuery( '#billing_address_1' ).val( billing_address_1 );
            }
        } );

        jQuery( window ).load( function() {
            // Hide empty containers from WC Postcode Checker NL moving fields around
            jQuery( '.cfw-sg-container:not(:has(*))' ).hide();

            // Add spacing due to moving fields around
            jQuery( '.cfw-column-12' ).filter( function() {
                return jQuery( this ).next( '.cfw-column-12' ).length;
            } ).css('margin-bottom', '12.5px');
        } );
    }
}