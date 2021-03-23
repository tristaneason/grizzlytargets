import { FormElement }          from "./FormElement";

/**
 *
 */
export class SelectLabelWrap extends FormElement {

    /**
     * @param jel
     */
    constructor( jel: any ) {
        super( jel );

        this.setHolderAndLabel( this.jel.find( 'select' ));

        if( this.holder ) {
            this.eventCallbacks = [
                {
                    eventName: "change", func: function () {
                        this.wrapClassSwap( this.holder.jel.val());
                    }.bind( this ), target: null
                },
                {
                    eventName: "keyup", func: function () {
                        this.wrapClassSwap( this.holder.jel.val());
                    }.bind( this ), target: null
                }
            ];

            this.regAndWrap();
        }
    }

    /**
     * @param value
     */
    wrapClassSwap( value: string ) {
        if ( ! this.jel.hasClass( FormElement.labelClass ) ) {
            this.jel.addClass( FormElement.labelClass );
        }
    }

}