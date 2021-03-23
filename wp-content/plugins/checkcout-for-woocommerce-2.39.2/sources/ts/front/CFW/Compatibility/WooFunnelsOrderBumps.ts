import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class WooFunnelsOrderBumps extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'WooFunnelsOrderBumps' );
    }

    load( main: Main ): void {
        jQuery( document.body ).on( 'wfob_bump_trigger', function() {
            main.tabContainer.queueUpdateCheckout();
        } );
    }
}