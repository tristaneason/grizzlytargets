import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class WooCommerceGermanized extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'WooCommerceGermanized' );
    }

    load( main: Main ): void {
        jQuery( window ).load( function() {
            jQuery( document ).off( 'change', '.payment_methods input[name="payment_method"]' );
        } );
    }
}