import {CompleteOrderAction} from "../Actions/CompleteOrderAction";
import {Main} from "../Main";

declare var jQuery: any;
declare var window: any;
declare var cfwEventData: any;
declare var wc_address_i18n_params: any;

export class ParsleyService {

    /**
     * @type {any}
     * @private
     */
    private _parsley: any;

    /**
     *
     */
    constructor() {
        this.setParsleyValidators();
    }

    /**
     *
     */
    setParsleyValidators(): void {
        const self = this;
        
        jQuery( window ).on( 'load', () => {
            this.parsley = window.Parsley;
            this.parsley.on( 'form:error', () => {
                // TODO: Evil coupling!
                Main.removeOverlay();
                CompleteOrderAction.initCompleteOrder = false;
            });

            try {
                // Parsley locale
                window.Parsley.setLocale( cfwEventData.settings.locale );
            } catch {
                console.log('CheckoutWC: Could not load Parsley translation domain (' + cfwEventData.settings.locale + ')' );
            }

            Main.instance.checkoutForm.parsley();
        } );

        let locale_json = wc_address_i18n_params.locale.replace( /&quot;/g, '"' );
        let locale = jQuery.parseJSON( locale_json );

        // TODO: This isn't related to validation, so we should really move this somewhere else
        // We are lucky enough that this seems to consistently run before the select2 handler in country-select.js
        // Theory: Delegated events may run before bound events?
        jQuery( document.body ).on( 'country_to_state_changed', function() {
            jQuery( '.state_select' ).removeClass( 'state_select' );
        } );

        // Setup proper validation whenever the state field changes ( or potentially does )
        jQuery( document.body ).bind( 'country_to_state_changing', function( event, country, wrapper ) {
            // Find the actual field wrapper
            let city_wrapper  = wrapper.find( '#billing_city, #shipping_city' ).parent( '.cfw-input-wrap' );
            let postcode_wrapper  = wrapper.find( '#billing_postcode, #shipping_postcode' ).parent( '.cfw-input-wrap' );
            let thislocale;

            if ( typeof locale[ country ] !== 'undefined' ) {
                thislocale = locale[ country ];
            } else {
                thislocale = locale['default'];
            }

            wrapper = wrapper.find( '#billing_state, #shipping_state' ).parent( '.cfw-input-wrap' );

            wrapper.find( '#billing_state, #shipping_state' ).each( function() {
                let fieldLocale = jQuery.extend( true, {}, locale['default'][ 'state' ], thislocale[ 'state' ] );

                let group = jQuery( this ).attr( 'id' ).split( '_' )[0];

                if ( jQuery( this ).is( 'select' ) ) {
                    // Setup data again
                    jQuery( this ).attr( 'field_key', 'state' )
                        .addClass( 'garlic-auto-save' )
                        .addClass( 'state-select' )
                        .garlic();

                    // Disable first option
                    jQuery( this ).find( 'option:first' ).attr( 'disabled', 'disabled' );

                    wrapper.addClass( 'cfw-select-input' )
                        .removeClass( 'cfw-hidden-input' )
                        .removeClass( 'cfw-text-input' )
                        .addClass( 'cfw-floating-label' );
                } else if( jQuery( this ).attr( 'type' ) === 'text' ) {
                    jQuery( this ).attr( 'field_key', 'state' )
                        .addClass( 'garlic-auto-save' )
                        .addClass( 'input-text' )
                        .garlic();

                    wrapper.addClass( 'cfw-text-input' )
                        .removeClass( 'cfw-hidden-input' )
                        .removeClass( 'cfw-select-input' )
                        .addClass( 'cfw-floating-label' );
                } else {
                    jQuery( this ).addClass( 'hidden' );

                    wrapper.addClass( 'cfw-hidden-input' )
                        .removeClass( 'cfw-text-input' )
                        .removeClass( 'cfw-select-input' )
                        .removeClass( 'cfw-floating-label' );
                }

                // Handle required toggle
                if ( fieldLocale.required ) {
                    jQuery( this ).attr( 'data-parsley-validate-if-empty', '' )
                        .attr( 'data-parsley-trigger', 'keyup change focusout' )
                        .attr( 'data-parsley-group', group )
                        .attr( 'data-parsley-required', 'true' );
                } else {
                    jQuery( this ).removeAttr( 'data-parsley-validate-if-empty' )
                        .removeAttr( 'data-parsley-trigger' )
                        .removeAttr( 'data-parsley-group' )
                        .removeAttr( 'data-parsley-required' )
                        .parsley().validate(); // removes irrelevant errors if they are there
                }
            } );

            setTimeout( () => {
                city_wrapper.find( '#billing_city, #shipping_city' ).each( function() {
                    if ( ! jQuery( this ).is( ':visible' ) ) {
                        jQuery( this ).attr( 'data-parsley-group-old', jQuery( this ).attr( 'data-parsley-group' ) )
                        .attr( 'data-parsley-required-old', jQuery( this ).attr( 'data-parsley-required' ) )
                        .removeAttr( 'data-parsley-group' )
                        .removeAttr( 'data-parsley-required' );
                    } else if ( jQuery( this ).is( ':visible' ) && this.hasAttribute( 'data-parsley-group-old' ) && this.hasAttribute( 'data-parsley-required-old' ) ) {
                        jQuery( this ).attr( 'data-parsley-group', jQuery( this ).attr( 'data-parsley-group-old' ) )
                        .attr( 'data-parsley-required', jQuery( this ).attr( 'data-parsley-required-old' ) )
                        .removeAttr( 'data-parsley-group-old' )
                        .removeAttr( 'data-parsley-required-old' );
                    }
                });

                postcode_wrapper.find( '#billing_postcode, #shipping_postcode' ).each( function() {
                    if ( ! jQuery( this ).is( ':visible' ) ) {
                        jQuery( this ).attr( 'data-parsley-group-old', jQuery( this ).attr( 'data-parsley-group' ) )
                            .removeAttr( 'data-parsley-group' );
                    } else if ( jQuery( this ).is( ':visible' ) && this.hasAttribute( 'data-parsley-group-old' ) ) {
                        jQuery( this ).attr( 'data-parsley-group', jQuery( this ).attr( 'data-parsley-group-old' ) )
                            .removeAttr( 'data-parsley-group-old' );
                    }
                });

                self.reinitParsley( wrapper );
            }, 100);

            self.reinitParsley( wrapper );
        } );
    }

    reinitParsley( wrapper: any ): void {
        // Remove existing parsley errors.
        wrapper.find( '.parsley-errors-list' ).remove();

        // Re-register all the elements
        Main.instance.checkoutForm.parsley();
        Main.instance.checkoutForm.parsley().isValid();
    }

    /**
     * @returns {any}
     */
    get parsley(): any {
        return this._parsley;
    }

    /**
     * @param value
     */
    set parsley( value: any ) {
        this._parsley = value;
    }
}