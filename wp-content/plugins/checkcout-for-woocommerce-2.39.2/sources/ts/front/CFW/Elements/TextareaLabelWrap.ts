import { FormElement }          from "./FormElement";

/**
 *
 */
export class TextareaLabelWrap extends FormElement {

    /**
     * @param jel
     */
    constructor( jel: any ) {
        super( jel );

        this.setHolderAndLabel( this.jel.find( 'textarea' ));

        if( this.holder ) {
            this.eventCallbacks = [
                {
                    eventName: "keyup", func: function () {
                        this.wrapClassSwap( this.holder.jel.val());
                    }.bind( this ), target: null
                }
            ];

            this.regAndWrap();
        }
    }
}