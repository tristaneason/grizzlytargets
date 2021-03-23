import {AjaxInfo} from "../Types/Types";

declare let jQuery: any;
declare let cfwEventData: any;

/**
 * Base class for our ajax handling. Child classes will extend this and override the response function and implement their
 * own custom solutions for the php side of actions
 */
export abstract class Action {

    /**
     * @type {string}
     * @private
     */
    private _id: string;

    /**
     * @type {string}
     * @private
     */
    private _url: string;

    /**
     * @type {Object}
     * @private
     */
    private _data: any;

    /**
     * @param id
     * @param url
     * @param data
     */
    protected constructor( id: string, data: Object ) {
        this.id = id;
        this.url = cfwEventData.checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', this.id );
        this.data = data;
    }

    /**
     * Automatically assign the items to the data
     *
     * @param {string} id
     * @param {AjaxInfo} ajaxInfo
     * @param items
     * @returns {any}
     */
    public static prep( id: string, ajaxInfo: AjaxInfo, items: any ) {
        let data: any = {
            "wc-ajax": id,
        };

        (<any>Object ).assign( data, items );

        return data;
    }

    /**
     * Fire ze ajax
     */
    load(): void {
        jQuery.ajax({
            type: "POST",
            url: this.url,
            data: this.data,
            success: this.response.bind( this ),
            error: this.error.bind( this ),
            dataType: 'json'
        });
    }

    /**
     * Our ajax response handler. Overridden in child classes
     * @param resp
     */
    abstract response( resp: any ): void;

    /**
     * Our ajax error handler. Overridden in child classes
     * @param xhr
     * @param textStatus
     * @param errorThrown
     */
    abstract error( xhr: any, textStatus: string, errorThrown: string ): void;

    /**
     * @returns {string}
     */
    get id(): string {
        return this._id;
    }

    /**
     * @param value
     */
    set id( value: string ) {
        this._id = value;
    }

    /**
     * @returns {string}
     */
    get url(): string {
        return this._url;
    }

    /**
     * @param value
     */
    set url( value: string ) {
        this._url = value;
    }

    /**
     * @returns {Object}
     */
    get data(): any {
        return this._data;
    }

    /**
     * @param value
     */
    set data( value: any ) {
        this._data = value;
    }
}