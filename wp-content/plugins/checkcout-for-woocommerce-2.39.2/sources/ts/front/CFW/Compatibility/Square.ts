import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;
declare let window: any;

export class Square extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'Square' );
    }

    load( main: Main ): void {
        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        jQuery( window ).on( 'payment_method_selected cfw_updated_checkout', () => {
            let same_as_shipping = jQuery( 'input[name="bill_to_different_address"]:checked' ).val();

            if ( same_as_shipping === 'same_as_shipping' ) {
                jQuery( '#billing_postcode' ).val( jQuery( '#shipping_postcode' ).val() );
            }

            if ( typeof window.wc_square_credit_card_payment_form_handler !== 'undefined' ) {
                window.wc_square_credit_card_payment_form_handler.set_payment_fields();
            }
        } );

        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-payment-method' ) {
                if ( typeof window.wc_square_credit_card_payment_form_handler !== 'undefined' ) {
                    window.wc_square_credit_card_payment_form_handler.set_payment_fields();
                }
            }
        } );
    }
}