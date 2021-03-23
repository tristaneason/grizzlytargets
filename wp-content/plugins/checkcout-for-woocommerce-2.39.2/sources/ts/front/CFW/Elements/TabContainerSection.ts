import { Element }                  from "./Element";
import { InputLabelWrap }           from "./InputLabelWrap";
import { LabelType }                from "../Enums/LabelType";
import { InputLabelType }           from "../Types/Types";
import { SelectLabelWrap }          from "./SelectLabelWrap";
import { FormElement }              from "./FormElement";
import {TextareaLabelWrap} from "./TextareaLabelWrap";

declare let jQuery: any;

/**
 *
 */
export class TabContainerSection extends Element {

    /**
     *
     * @type {string}
     * @private
     */
    private _name: string = "";

    /**
     *
     * @type {Array}
     * @private
     */
    private _inputLabelWraps: Array<InputLabelWrap> = [];

    /**
     *
     * @type {Array}
     * @private
     */
    private _selectLabelWraps: Array<SelectLabelWrap> = [];

    /**
     *
     * @type {string}
     * @private
     */
    private static _inputLabelWrapClass: string = "cfw-input-wrap";

    /**
     *
     * @type {[{type: LabelType; cssClass: string},{type: LabelType; cssClass: string},{type: LabelType; cssClass: string}]}
     * @private
     */
    private static _inputLabelTypes: Array<InputLabelType> = [
        { type: LabelType.TEXT, cssClass: 'cfw-text-input' },
        { type: LabelType.TEL, cssClass: 'cfw-tel-input' },
        { type: LabelType.PASSWORD, cssClass: 'cfw-password-input' },
        { type: LabelType.SELECT, cssClass: 'cfw-select-input' },
        { type: LabelType.TEXTAREA, cssClass: 'cfw-textarea-input' },
        { type: LabelType.NUMBER, cssClass: 'cfw-number-input' },
    ];

    /**
     *
     * @param jel
     * @param name
     */
    constructor(
        jel: any,
        name: string
    ) {
        super( jel );

        this.name = name;
    }

    /**
     *
     * @returns {string}
     */
    getWrapSelector(): string {
        let selector: string = "";

        TabContainerSection.inputLabelTypes.forEach(( labelType, index ) => {
            selector += `.${TabContainerSection.inputLabelWrapClass}.${labelType.cssClass}`;

            if( index+1 != TabContainerSection.inputLabelTypes.length ) {
                selector += ", ";
            }
        });

        return selector;
    }

    /**
     * Gets all the inputs for a tab section
     *
     * @param query
     * @returns {Array<Element>}
     */
    getInputsFromSection( query: string = ""): Array<Element> {
        let out: Array<Element> = [];

        this.jel.find(`input${query}`).each(( index, elem ) => {
            out.push( new Element( jQuery( elem )));
        });

        return out;
    }

    /**
     *
     */
    setWraps(): void {
        let jLabelWrap: any = this.jel.find( this.getWrapSelector());

        jLabelWrap.each( ( index, wrap ) => {
            if( jQuery( wrap ).hasClass( 'cfw-select-input' ) && jQuery( wrap ).find( 'select' ).length > 0 ) {
                new SelectLabelWrap( jQuery( wrap ) );
            } else if( jQuery( wrap ).hasClass( 'cfw-textarea-input' ) && jQuery( wrap ).find( 'textarea' ).length > 0 ) {
                new TextareaLabelWrap( jQuery( wrap ) );
            } else {
                new InputLabelWrap( jQuery( wrap ) );
            }
        } );
    }

    /**
     *
     * @returns {string}
     */
    get name(): string {
        return this._name;
    }

    /**
     *
     * @param value
     */
    set name( value: string ) {
        this._name = value;
    }

    /**
     *
     * @returns {Array<SelectLabelWrap>}
     */
    get selectLabelWraps(): Array<SelectLabelWrap> {
        return this._selectLabelWraps;
    }

    /**
     *
     * @param value
     */
    set selectLabelWraps( value: Array<SelectLabelWrap>) {
        this._selectLabelWraps = value;
    }

    /**
     *
     * @returns {Array<InputLabelType>}
     */
    static get inputLabelTypes(): Array<InputLabelType> {
        return TabContainerSection._inputLabelTypes;
    }

    /**
     *
     * @param value
     */
    static set inputLabelTypes( value: Array<InputLabelType>) {
        TabContainerSection._inputLabelTypes = value;
    }

    /**
     *
     * @returns {string}
     */
    static get inputLabelWrapClass(): string {
        return TabContainerSection._inputLabelWrapClass;
    }

    /**
     *
     * @param value
     */
    static set inputLabelWrapClass( value: string ) {
        TabContainerSection._inputLabelWrapClass = value;
    }
}