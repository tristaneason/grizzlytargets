import { Action }                       from "./Action";
import { LogInData }                    from "../Types/Types";
import { AjaxInfo }                     from "../Types/Types";
import { Alert, AlertInfo }             from "../Elements/Alert";
import { Main }                         from "../Main";

declare let jQuery: any;

/**
 *
 */
export class LoginAction extends Action {

    /**
     * @type {boolean}
     * @private
     * @static
     */
    private static _loginLocked: boolean;

    /**
     *
     * @param id
     * @param ajaxInfo
     * @param email
     * @param password
     */
    constructor( id: string, ajaxInfo: AjaxInfo, email: string, password: string ) {
        let data: LogInData = {
            "wc-ajax": id,
            email: email,
            password: password
        };

        super( id, data );
    }

    /**
     *
     * @param resp
     */
    public response( resp: any ): void {
        if ( typeof resp !== "object" ) {
            resp = JSON.parse( resp );
        }

        if( resp.logged_in ) {
            location.reload();
        } else {
            let alertInfo: AlertInfo = {
                type: "error",
                message: resp.message,
                cssClass: "cfw-alert-error"
            };

            let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
            alert.addAlert();
        }
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        let alertInfo: AlertInfo = {
            type: "error",
            message: `An error occurred during login. Error: ${errorThrown} (${textStatus})`,
            cssClass: "cfw-alert-error"
        };

        let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
        alert.addAlert();
    }
}