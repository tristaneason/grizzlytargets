import { Action }                       from "./Action";
import { AccountExistsData }            from "../Types/Types";
import { AjaxInfo }                     from "../Types/Types";

declare let jQuery: any;

/**
 * Ajax does the account exist action. Takes the information from email box and fires of a request to see if the account
 * exists
 */
export class AccountExistsAction extends Action {
    /**
     * @type {boolean}
     * @private
     */
    private static _checkBox: boolean = true;

    /**
     * @param id
     * @param ajaxInfo
     * @param email
     * @param ezTabContainer
     */
    constructor( id: string, ajaxInfo: AjaxInfo, email: string ) {
        // Object prep
        let data: AccountExistsData = {
            "wc-ajax": id,
            email: email
        };

        // Call parent
        super(id, data);
    }

    /**
     *
     * @param resp
     */
    public response( resp: any ): void {
        if ( typeof resp !== "object" ) {
            resp = JSON.parse( resp );
        }

        let login_slide: any = jQuery( '#cfw-login-slide' );
        let $create_account = jQuery( '#createaccount' );
        let register_user_checkbox: any = ($create_account.length > 0) ? $create_account : null;
        let register_container: any = jQuery( '#cfw-login-details .cfw-check-input' );

        // Cleanup any login required alerts
        jQuery( `.cfw-login-required-error` ).remove();

        // If account exists slide down the password field, uncheck the register box, and hide the container for the checkbox
        if ( resp.account_exists ) {
            if( ! login_slide.hasClass( 'stay-open' ) ) {
                login_slide.slideDown(300);
            }

            if ( register_user_checkbox && register_user_checkbox.is( ':checkbox' ) ) {
                register_user_checkbox.prop( 'checked', false );
                register_user_checkbox.trigger( 'change' );
                register_user_checkbox.prop( 'disabled', true );
            }

            register_container.css( 'display', 'none' );

            AccountExistsAction.checkBox = true;
            // If account does not exist, reverse
        } else {
            if( ! login_slide.hasClass( 'stay-open' ) ) {
                login_slide.slideUp(300);
            }

            register_container.css( 'display', 'block' );

            if ( AccountExistsAction.checkBox ) {
                if ( register_user_checkbox && register_user_checkbox.is( ':checkbox' ) ) {
                    if ( (<any>window).cfwEventData.settings.check_create_account_by_default == true ) {
                        register_user_checkbox.prop( 'checked', true );
                    }

                    register_user_checkbox.trigger( 'change' );
                    register_user_checkbox.prop( 'disabled', false );
                }

                AccountExistsAction.checkBox = false;
            }
        }
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        console.log(`Account Exists Error: ${errorThrown} (${textStatus})`);
    }

    /**
     * @returns {boolean}
     */
    static get checkBox(): boolean {
        return AccountExistsAction._checkBox;
    }

    /**
     * @param {boolean} value
     */
    static set checkBox( value: boolean ) {
        AccountExistsAction._checkBox = value;
    }
}