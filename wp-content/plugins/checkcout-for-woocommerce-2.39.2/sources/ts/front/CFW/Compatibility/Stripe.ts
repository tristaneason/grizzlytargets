import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;

export class Stripe extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'Stripe' );
    }

    load(main: Main): void {
        jQuery(document).on('stripeError', this.onError);
    }

    onError(): void {
        window.location.hash = 'cfw-payment-method';
    }
}