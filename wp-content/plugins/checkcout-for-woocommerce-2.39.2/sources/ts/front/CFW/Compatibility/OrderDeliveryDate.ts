import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class OrderDeliveryDate extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'OrderDeliveryDate' );
    }

    load( main: Main ): void {
        jQuery( document.body ).one( 'updated_checkout', function() {
            jQuery( `input[name="shipping_method[0]"]:checked` ).trigger( 'change' );
        } );
    }
}