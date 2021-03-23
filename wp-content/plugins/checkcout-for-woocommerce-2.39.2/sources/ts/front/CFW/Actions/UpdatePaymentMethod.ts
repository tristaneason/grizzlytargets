import { Action }                       from "./Action";
import { PaymentMethodData }                    from "../Types/Types";
import { AjaxInfo }                     from "../Types/Types";

/**
 *
 */
export class UpdatePaymentMethod extends Action {

    /**
     *
     * @param id
     * @param ajaxInfo
     * @param payment_method
     */
    constructor( id: string, ajaxInfo: AjaxInfo, payment_method: string ) {
        let data: PaymentMethodData = {
            "wc-ajax": id,
            payment_method: payment_method
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
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        console.log(`Update Payment Method Error: ${errorThrown} (${textStatus})`);
    }
}