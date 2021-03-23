import { Action }                       from "./Action";
import { AjaxInfo }                     from "../Types/Types";
import { Main } from "../Main";
import {TabContainer} from "../Elements/TabContainer";
import {UpdateCheckoutAction} from "./UpdateCheckoutAction";

declare let jQuery: any;

/**
 *
 */
export class UpdateCartAction extends Action {

    /**
     *
     * @param id
     * @param ajaxInfo
     * @param formData
     */
    constructor( id: string, ajaxInfo: AjaxInfo, formData: any ) {
        let cleanedFormData = Object.keys( formData ).filter( key => key.startsWith( 'cart' ) ).reduce( function( object, key ) {
            object[ key ] = formData[ key ];

            return object;
        }, {} );

        super( id, Action.prep( id, ajaxInfo, cleanedFormData ) );

        this.blockUI();
    }

    /**
     *
     * @param resp
     */
    public response( resp: any ): void {
        if ( typeof resp !== "object" ) {
            resp = JSON.parse( resp );
        }

        if ( false !== resp.redirect ) {
            window.location = resp.redirect;
        } else {
            let tabContainer: TabContainer = Main.instance.tabContainer;

            // Fire updated_checkout event.
            tabContainer.queueUpdateCheckout( {}, { update_shipping_method: false } );
        }
    }

    public blockUI(): void {
        jQuery( UpdateCheckoutAction.blockUISelector ).block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        }).addClass('blocked');
    }

    /**
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    public error( xhr: any, textStatus: string, errorThrown: string ): void {
        console.log(`Update Cart Error: ${errorThrown} (${textStatus})`);
    }
}