import { Element }              from "./Element";
import { Main }                 from "../Main";
import { Md5 }                  from 'ts-md5/dist/md5';

export type AlertInfo = {
    type: "error" | "notice" | "success",
    message: any,
    cssClass: string
};

declare let jQuery: any;

/**
 *
 */
export class Alert extends Element {

    /**
     * @type {AlertInfo}
     * @private
     */
    private _alertInfo: AlertInfo;

    /**
     *
     * @param alertContainer
     * @param alertInfo
     */
    constructor( alertContainer: any, alertInfo: AlertInfo ) {
		super( alertContainer );

		this.alertInfo = alertInfo;
	}

    /**
     * @param {boolean} temporary
     */
    addAlert( temporary: boolean = false ): void {
        // If error, trigger checkout_error event
        if( this.alertInfo.type === "error") {
			jQuery( document.body ).trigger( 'checkout_error' );
		}

        // TODO: This seems like evil coupling
        Main.removeOverlay();

        let hash = Md5.hashStr( this.alertInfo.message + this.alertInfo.cssClass + this.alertInfo.type );
        let alert_element = jQuery( `.cfw-alert-${hash}` );

        if ( alert_element.length == 0 ) {
            alert_element = jQuery( '#cfw-alert-placeholder' ).contents().clone();

            alert_element.find( '.message' ).html( this.alertInfo.message );
            alert_element.addClass( this.alertInfo.cssClass );
            alert_element.addClass( `cfw-alert-${hash}` );
            alert_element.appendTo( this.jel );

            this.jel.slideDown(300);

            alert_element = jQuery( `.cfw-alert-${hash}` );

            window.dispatchEvent( new CustomEvent( 'cfw-add-alert-event', { detail: { alertInfo: this.alertInfo } }));
        }

        // Temporary alerts are removed on tab switch
        if ( temporary ) {
            alert_element.addClass( 'cfw-alert-temporary' );
        }

        // Scroll to the top of current tab on tab switch
        jQuery( 'html, body' ).stop().animate( {
            scrollTop: alert_element.offset().top
        }, 300 );
    }

    /**
     * @param {any} alertContainer
     */
    static removeAlerts( alertContainer: any ): void {
        alertContainer.find( '.cfw-alert' ).remove();
    }

    /**
     * @returns {AlertInfo}
     */
    get alertInfo(): AlertInfo {
        return this._alertInfo;
    }

    /**
     * @param value
     */
    set alertInfo( value: AlertInfo ) {
        this._alertInfo = value;
    }
}