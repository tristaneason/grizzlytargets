import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class CO2OK extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'CO2OK' );
    }

    load( main: Main ): void {
        jQuery( document.body ).on( 'cfw_updated_checkout', function() {
            jQuery( 'a.co2ok_nolink' ).prop('href', '#cfw-payment-method' );
        } );
    }
}