import { Compatibility } from "./Compatibility";
import { Main } from "../Main";

declare let jQuery: any;
declare let bootstrapPayPalApp: any;

export class InpsydePayPalPlus extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'InpsydePayPalPlus' );
    }

    load(main: Main): void {
        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-payment-method' ) {
                this.refreshPayPalPlus();
            }
        } );
    }

    refreshPayPalPlus(): void {
        let main = Main.instance;

        main.force_updated_checkout = true;
        main.tabContainer.triggerUpdateCheckout();
    }
}