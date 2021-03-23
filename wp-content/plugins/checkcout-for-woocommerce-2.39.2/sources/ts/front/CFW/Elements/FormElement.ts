import { Element }              from "./Element";
import { LabelType }            from "../Enums/LabelType";
import { EventCallback }        from "../Types/Types";

declare let jQuery: any;

/**
 *
 */
export class FormElement extends Element {

    /**
     * @type {string}
     * @private
     */
    protected static _labelClass: string = "cfw-floating-label";

    /**
     * @type {Array}
     * @private
     */
    protected _eventCallbacks: Array<EventCallback> = [];

    /**
     * @type {any}
     * @private
     */
    private _moduleContainer: any;

    /**
     * @type {Element}
     * @private
     */
    private _holder: Element;

    /**
     * @param jel
     */
    constructor( jel: any ) {
        super( jel );

        this.moduleContainer = this.jel.parents( '.cfw-module' );
    }

    /**
     * @returns {any}
     */
    static getLabelTypes(): Array<any> {
        return jQuery.map( LabelType, function( value, index ) {
            return [value];
        });
    }

    /**
     *
     */
    regAndWrap(): void {
        this.registerEventCallbacks();

        this.wrapClassSwap(<string>this.holder.jel.val());
    }

    /**
     * @param tjel
     * @param useType
     */
    setHolderAndLabel( tjel: any | string, useType: boolean = false ) {
        let lt = FormElement.getLabelTypes();

        // Note: Length is divided by 2 because of ENUM implementation. Read TS docs
        for( let i = 0; i < lt.length / 2; i++) {
            let jqTjel: any = <any>tjel;

            if( useType && typeof tjel === 'string' ) {
                let type = lt[i].toLowerCase();

                jqTjel = <any>this.jel.find(<string>tjel.replace( '%s', type ));
            }

            if ( jqTjel.length > 0) {
                this.holder = new Element( jqTjel );
            }
        }
    }

    /**
     * @param value
     */
    wrapClassSwap( value: string ) {
        if ( value !== '' && ! this.jel.hasClass( FormElement.labelClass ) ) {
            this.jel.addClass( FormElement.labelClass );
        }

        if ( value === '' && this.jel.hasClass( FormElement.labelClass ) ) {
            this.jel.removeClass( FormElement.labelClass );
        }
    }

    /**
     *
     */
    registerEventCallbacks(): void {
        if( this.holder ) {
            this.eventCallbacks.forEach(( eventCb ) => {
                let eventName: any = eventCb.eventName;
                let cb: Function = eventCb.func;
                let target: any = eventCb.target;

                if(!target ) {
                    target = this.holder.jel;
                }

                target.on( eventName, cb );
            });
        }
    }

    /**
     * @returns {string}
     */
    static get labelClass(): string {
        return FormElement._labelClass;
    }

    /**
     * @param value
     */
    static set labelClass( value: string ) {
        FormElement._labelClass = value;
    }

    /**
     * @returns {Array<EventCallback>}
     */
    get eventCallbacks(): Array<EventCallback> {
        return this._eventCallbacks;
    }

    /**
     * @param value
     */
    set eventCallbacks( value: Array<EventCallback>) {
        this._eventCallbacks = value;
    }

    /**
     * @returns {any}
     */
    get moduleContainer(): any {
        return this._moduleContainer;
    }

    /**
     * @param value
     */
    set moduleContainer( value: any ) {
        this._moduleContainer = value;
    }

    /**
     * @returns {Element}
     */
    get holder(): Element {
        return this._holder;
    }

    /**
     * @param value
     */
    set holder( value: Element ) {
        this._holder = value;
    }
}