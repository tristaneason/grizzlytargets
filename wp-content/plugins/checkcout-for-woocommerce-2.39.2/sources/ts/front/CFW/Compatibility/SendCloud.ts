import { Compatibility }                    from "./Compatibility";
import { Main }                             from "../Main";
import { Alert, AlertInfo }                 from "../Elements/Alert";

declare let jQuery: any;

export class SendCloud extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'SendCloud' );
    }

    load( main: Main, params ): void {
        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        easyTabsWrap.bind( 'easytabs:before', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-payment-method' ) {
                let selected_shipping_method = jQuery("input[name='shipping_method[0]']:checked").val();
                let selected_service_point = jQuery( '#sendcloudshipping_service_point_selected' );
                if ( typeof selected_shipping_method != 'undefined' && selected_shipping_method.indexOf( 'service_point_shipping_method' ) !== -1 ) {
                    if ( selected_service_point.length == 0 || selected_service_point.val() == '' ) {
                        // Prevent removing alert on next update checkout
                        Main.instance.preserve_alerts = true;

                        let alertInfo: AlertInfo = {
                            type: "error",
                            message: params.notice,
                            cssClass: "cfw-alert-error"
                        };

                        let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
                        alert.addAlert( true );

                        event.stopImmediatePropagation();

                        return false;
                    }
                }
            }
        } );
    }
}