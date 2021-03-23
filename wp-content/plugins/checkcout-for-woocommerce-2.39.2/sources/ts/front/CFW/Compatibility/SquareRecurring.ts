import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class SquareRecurring extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'SquareRecurring' );
    }

    load( main: Main ): void {
        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        jQuery( window ).on( 'payment_method_selected cfw_updated_checkout', () => {
            if ( typeof jQuery.WooSquare_payments !== 'undefined' ) {
                jQuery.WooSquare_payments.loadForm();
            }
        } );

        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-payment-method' ) {
                if ( typeof jQuery.WooSquare_payments !== 'undefined' ) {
                    jQuery.WooSquare_payments.loadForm();
                }
            }
        } );
    }
}