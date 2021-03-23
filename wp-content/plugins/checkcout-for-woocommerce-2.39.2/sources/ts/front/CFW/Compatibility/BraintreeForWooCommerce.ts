import { Compatibility } from "./Compatibility";
import { Main } from "../Main";
import {EasyTabDirection, EasyTabService} from "../Services/EasyTabService";

declare let jQuery: any;

export class BraintreeForWooCommerce extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'BraintreeForWooCommerce' );
    }


    load( main: Main ): void {
        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => this.refreshBraintree( main, event, clicked, target ) );

        jQuery( window ).on( 'payment_method_selected', () => {
            main.force_updated_checkout = true;
            main.tabContainer.triggerUpdateCheckout();
        } );
    }

    refreshBraintree( main: Main, event: any, clicked: any, target: any ): void {
        let easyTabDirection: EasyTabDirection = EasyTabService.getTabDirection( target );
        let easyTabID: string = EasyTabService.getTabId( easyTabDirection.target );
        let paymentContainerId: string = main.tabContainer.tabContainerSectionBy( 'name', 'payment_method' ).jel.attr( 'id' );

        if( paymentContainerId === easyTabID ) {
            main.force_updated_checkout = true;
            main.tabContainer.triggerUpdateCheckout();
        }
    }
}