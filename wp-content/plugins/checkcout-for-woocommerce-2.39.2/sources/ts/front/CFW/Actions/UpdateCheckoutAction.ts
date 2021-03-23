import { Action }                           from "./Action";
import { AjaxInfo }          from "../Types/Types";
import { Main }                             from "../Main";
import {Alert, AlertInfo} from "../Elements/Alert";

declare let jQuery: any;

export class UpdateCheckoutAction extends Action {
    /**
     *
     */
    private static _underlyingRequest: any = null;

    /**
     *
     */
    private static _blockUISelector: string = '#cfw-billing-methods, #cfw-shipping-details-fields, #cfw-shipping-method-list, #cfw-cart-details, #cfw-place-order';

    /**
     * @param {string} id
     * @param {AjaxInfo} ajaxInfo
     * @param fields
     * @param args
     */
    constructor( id: string, ajaxInfo: AjaxInfo, fields: any, args: any ) {
        let main = Main.instance;

        // If update shipping method is false, strip out any shipping_method keys from fields object
        if ( false === args.update_shipping_method ) {
            Object.keys( fields ).filter( function( key ) {
                return key.match( /^shipping_method/ );
            } ).forEach( function( key ) {
                delete fields[ key ];
            } );
        }

        // This gives us another way to force updated_checkout
        if ( ! main.force_updated_checkout && typeof args.force_updated_checkout !== "undefined" && true === args.force_updated_checkout ) {
            main.force_updated_checkout = true;
        }

        if ( main.force_updated_checkout ) {
            fields['force_updated_checkout'] = main.force_updated_checkout;
        }

        super( id, Action.prep( id, ajaxInfo, fields ) );
    }

    public load(): void {
        this.blockUI();

        if( UpdateCheckoutAction.underlyingRequest !== null ) {
            UpdateCheckoutAction.underlyingRequest.abort();
        }

        UpdateCheckoutAction.underlyingRequest = jQuery.post( this.url, this.data, this.response.bind( this ) );
    }

    public blockUI(): void {
        jQuery( UpdateCheckoutAction.blockUISelector ).not('.blocked').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
    }

    public unblockUI(): void {
        jQuery( UpdateCheckoutAction.blockUISelector ).unblock().removeClass('blocked');
    }

    /**
     *
     * @param resp
     */
    public response( resp: any ): void {
        if ( typeof resp !== "object" ) {
            resp = JSON.parse( resp );
        }

        if ( resp.redirect !== false ) {
            window.location = resp.redirect;
        }

        let main: Main = Main.instance;

        // Payment methods
        let updated_payment_methods_container = jQuery( '#cfw-billing-methods' );

        /**
         * Updated payment methods will be false if md5 fingerprint hasn't changed
         */
        if ( false !== resp.updated_payment_methods ) {
            /**
             * Save payment details to a temporary object
             */
            let paymentDetails = {};
            jQuery( '.payment_box :input' ).each( function() {
                let ID = jQuery( this ).attr( 'id' );

                if ( ID ) {
                    if ( jQuery.inArray( jQuery( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                        paymentDetails[ ID ] = jQuery( this ).prop( 'checked' );
                    } else {
                        paymentDetails[ ID ] = jQuery( this ).val();
                    }
                }
            });

            updated_payment_methods_container.html(`${resp.updated_payment_methods}`);

            /**
             * Fill in the payment details if possible without overwriting data if set.
             */
            if ( ! jQuery.isEmptyObject( paymentDetails ) ) {
                jQuery( '.payment_box :input' ).each( function() {
                    let ID = jQuery( this ).attr( 'id' );

                    if ( ID ) {
                        if ( jQuery.inArray( jQuery( this ).attr( 'type' ), [ 'checkbox', 'radio' ] ) !== -1 ) {
                            jQuery( this ).prop( 'checked', paymentDetails[ ID ] ).change();
                        } else if ( null !== jQuery( this ).val() && 0 === jQuery( this ).val().length ) {
                            jQuery( this ).val( paymentDetails[ ID ] ).change();
                        }
                    }
                });
            }

            // Setup payment gateway radio buttons again
            // since we replaced the HTML
            Main.instance.tabContainer.setUpPaymentGatewayRadioButtons();
        }

        /**
         * Update Fragments
         *
         * For our elements as well as those from other plugins
         */
        // Always update the fragments
        if ( resp.fragments ) {
            jQuery.each( resp.fragments, function ( key, value ) {
                jQuery( key ).replaceWith( value );
            } );
        }

        let alerts = [];

        if ( resp.notices.success ) {
            Object.keys( resp.notices.success ).forEach( ( key: any ) => {
                alerts.push( {
                    type: "success",
                    message: resp.notices.success[ key ],
                    cssClass: "cfw-alert-success"
                } );
            } );
        }

        if ( resp.notices.notice ) {
            Object.keys( resp.notices.notice ).forEach( ( key: any ) => {
                alerts.push( {
                    type: "notice",
                    message: resp.notices.notice[ key ],
                    cssClass: "cfw-alert-info"
                } );
            } );
        }

        if ( resp.notices.error ) {
            Object.keys( resp.notices.error ).forEach( ( key: any ) => {
                alerts.push( {
                    type: "error",
                    message: resp.notices.error[ key ],
                    cssClass: "cfw-alert-error"
                } );
            } );
        }

        if ( ! Main.instance.preserve_alerts ) {
            Alert.removeAlerts( Main.instance.alertContainer );
        }

        Main.instance.preserve_alerts = false;

        if ( alerts.length > 0 ) {
            alerts.forEach( ( alertInfo: any ) => {
                let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
                alert.addAlert();
            } );
        }

        /**
         * Unblock UI
         */
        this.unblockUI();

        /**
         * A custom event that runs every time, since we are suppressing
         * updated_checkout if the payment gateways haven't updated
         */
		jQuery( document.body ).trigger( 'cfw_updated_checkout' );

		if ( main.force_updated_checkout === true || false !== resp.updated_payment_methods ) {
		    main.force_updated_checkout = false;
            Main.instance.tabContainer.triggerUpdatedCheckout( resp );
        }

        updated_payment_methods_container.unblock();
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        /**
         * Unblock UI
         */
        this.unblockUI();

        console.log(`Update Checkout Error: ${errorThrown} (${textStatus})`);
    }

    /**
     * @returns {any}
     */
    static get underlyingRequest(): any {
        return this._underlyingRequest;
    }

    /**
     * @param value
     */
    static set underlyingRequest( value: any ) {
        this._underlyingRequest = value;
    }

    static get blockUISelector(): string {
        return this._blockUISelector;
    }

    static set blockUISelector( value: string ) {
        this._blockUISelector = value;
    }
}