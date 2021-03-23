import { Action }                               from "./Action";
import { AjaxInfo }                             from "../Types/Types";
import { AlertInfo }                            from "../Elements/Alert";
import { Alert }                                from "../Elements/Alert";
import { Main }                                 from "../Main";

declare let jQuery: any;

export class CompleteOrderAction extends Action {

    /**
     * @type {boolean}
     * @static
     * @private
     */
    private static _preppingOrder: boolean = false;

	/**
     * @type {boolean}
     * @static
     * @private
	 */
	private static _initCompleteOrder: boolean = false;

    /**
     *
     * @param id
     * @param ajaxInfo
     * @param checkoutData
     */
    constructor( id: string, ajaxInfo: AjaxInfo, checkoutData: any ) {
        super( id, Action.prep( id, ajaxInfo, checkoutData ));

        Main.addOverlay();

        this.setup();
    }

    /**
     * The setup function which mainly determines if we need a stripe token to continue
     */
    setup(): void {
        Main.instance.checkoutForm.off( 'form:validate' );

        this.load();
    }


    /**
     *
     * @param resp
     */
    public response( resp: any ): void {
        if ( typeof resp !== "object" ) {
            resp = JSON.parse( resp );
        }

        if( resp.result === "success" ) {
            // Destroy all the cache!
            jQuery( '.garlic-auto-save' ).each(( index: number, elem: Element ) => jQuery( elem ).garlic( 'destroy' ));

            // Destroy all the parsley!
            Main.instance.checkoutForm.parsley().destroy();

            // Redirect all the browsers! ( well just the 1)
            window.location.href = resp.redirect;
        } else if( resp.result === "failure") {

            window.dispatchEvent( new CustomEvent( 'cfw-checkout-failed-before-error-message', { detail: { response: resp } } ) );

            Alert.removeAlerts( Main.instance.alertContainer );

            if( resp.messages !== "") {
                // Wrapping the response in a <div /> is required for correct parsing
                let messages = jQuery( jQuery.parseHTML( `<div>${resp.messages}</div>` ) );

                jQuery.each( messages.find( 'li' ), function( i, el ) {
                    let alert: Alert = new Alert( Main.instance.alertContainer, <AlertInfo> {
                        type: "error",
                        message: jQuery.trim( jQuery( el ).text() ),
                        cssClass: "cfw-alert-error"
                    } );
                    alert.addAlert();
                } );

                jQuery.each( messages.find( '.woocommerce-info' ), function( i, el ) {
                    let alert: Alert = new Alert( Main.instance.alertContainer, <AlertInfo> {
                        type: "notice",
                        message: jQuery.trim( jQuery( el ).text() ),
                        cssClass: "cfw-alert-info"
                    } );
                    alert.addAlert();
                } );

                jQuery.each( messages.find( '.woocommerce-message' ), function( i, el ) {
                    let alert: Alert = new Alert( Main.instance.alertContainer, <AlertInfo> {
                        type: "success",
                        message: jQuery.trim( jQuery( el ).text() ),
                        cssClass: "cfw-alert-success"
                    } );
                    alert.addAlert();
                } );
            } else {
                /**
                 * If the payment gateway comes back with no message, show a generic error.
                 */
                let alertInfo: AlertInfo = {
                    type: "error",
                    message: 'An unknown error occurred. Response from payment gateway was empty.',
                    cssClass: "cfw-alert-error"
                };

                let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
                alert.addAlert();
            }

            CompleteOrderAction.initCompleteOrder = false;

            // Fire updated_checkout event.
            jQuery( document.body ).trigger( 'updated_checkout' );
        }
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        let message: string;

        if ( xhr.status === 0)  {
            message = 'Could not connect to server. Please refresh and try again or contact site administrator.';
        } else if ( xhr.status === 404 ) {
            message = 'Requested resource could not be found. Please contact site administrator. (404)';
        } else if ( xhr.status === 500 ) {
            message = 'An internal server error occurred. Please contact site administrator. (500)';
        } else if ( textStatus === 'parsererror' ) {
            message = 'Server response could not be parsed. Please contact site administrator.';
        } else if ( textStatus === 'timeout' || xhr.status === 504 ) {
            message = 'The server timed out while processing your request. Please refresh and try again or contact site administrator.';
        } else if ( textStatus === 'abort' ) {
            message = 'Request was aborted. Please contact site administrator.';
        } else {
            message = `Uncaught Error: ${xhr.responseText}`;
        }

        console.log( `CheckoutWC XHR response: ${xhr.response}`);
        console.log( `CheckoutWC XHR responseText: ${xhr.responseText}`);
        console.log( `CheckoutWC XHR status: ${xhr.status}`);
        console.log( `CheckoutWC XHR errorThrown: ${errorThrown}`);

        let alertInfo: AlertInfo = {
            type: "error",
            message: message,
            cssClass: "cfw-alert-error"
        };

        let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
        alert.addAlert();
    }

    /**
     * @returns {boolean}
     */
    static get preppingOrder(): boolean {
        return this._preppingOrder;
    }

    /**
     * @param {boolean} value
     */
    static set preppingOrder( value: boolean ) {
        this._preppingOrder = value;
    }

	/**
     * @return {boolean}
	 */
	static get initCompleteOrder(): boolean {
		return this._initCompleteOrder;
	}

	/**
	 * @param {boolean} value
	 */
	static set initCompleteOrder( value: boolean ) {
		this._initCompleteOrder = value;
	}
}