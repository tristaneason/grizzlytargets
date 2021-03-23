import { Element }                          from "./Element";
import { TabContainerBreadcrumb }           from "./TabContainerBreadcrumb";
import { TabContainerSection }              from "./TabContainerSection";
import { AjaxInfo }                         from "../Types/Types";
import { AccountExistsAction }              from "../Actions/AccountExistsAction";
import { LoginAction }                      from "../Actions/LoginAction";
import { FormElement }                      from "./FormElement";
import { Main }                             from "../Main";
import { UpdateCheckoutAction }             from "../Actions/UpdateCheckoutAction";
import { ApplyCouponAction }                from "../Actions/ApplyCouponAction";
import { CompleteOrderAction }              from "../Actions/CompleteOrderAction";
import { UpdatePaymentMethod }              from "../Actions/UpdatePaymentMethod";
import { UpdateCartAction }                 from "../Actions/UpdateCartAction";

declare let jQuery: any;
declare var cfwEventData: any;

/**
 *
 */
export class TabContainer extends Element {

    /**
     * @type {TabContainerBreadcrumb}
     * @private
     */
    private _tabContainerBreadcrumb: TabContainerBreadcrumb;

    /**
     * @type {[TabContainerSection]}
     * @private
     */
    private _tabContainerSections: Array<TabContainerSection>;

    /**
     * @type {any}
     * @private
     */
    private _checkoutDataAtSubmitClick: any;

    /**
     * @param jel
     * @param tabContainerBreadcrumb
     * @param tabContainerSections
     */
    constructor( jel: any, tabContainerBreadcrumb: TabContainerBreadcrumb, tabContainerSections: Array<TabContainerSection>) {
        super( jel );

        this.tabContainerBreadcrumb = tabContainerBreadcrumb;
        this.tabContainerSections = tabContainerSections;
    }

    /**
     * Sometimes in some browsers ( looking at you safari and chrome ) the label doesn't float when the data is retrieved
     * via garlic. This will fix this issue and float the label like it should.
     */
    setFloatLabelOnGarlicRetrieve(): void {
        jQuery( '.garlic-auto-save' ).each(( index: number, elem ) => {
            jQuery( elem ).garlic({ onRetrieve: element => jQuery( element ).parent().addClass( FormElement.labelClass ) })
        });
    }

    /**
     * All update_checkout triggers should happen here
     *
     * Exceptions would be edge cases involving TS compat classes
     */
    setUpdateCheckoutTriggers() {
        let main: Main = Main.instance;
        let checkout_form: any = main.checkoutForm;

        checkout_form.on( 'change', 'select.shipping_method, input[name^="shipping_method"], [name="bill_to_different_address"], .update_totals_on_change select, .update_totals_on_change input[type="radio"], .update_totals_on_change input[type="checkbox"]', this.queueUpdateCheckout.bind( this ) );
        checkout_form.on( 'change', '.address-field select', this.queueUpdateCheckout.bind( this ) );
        checkout_form.on( 'change', '.address-field input.input-text, .update_totals_on_change input.input-text', this.queueUpdateCheckout.bind( this ) );
        checkout_form.on( 'change', '#wc_checkout_add_ons :input', this.queueUpdateCheckout.bind( this ) );
        checkout_form.on( 'keydown', '.address-field input.input-text, .update_totals_on_change input.input-text', this.queueUpdateCheckout.bind( this ) );

        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;
        easyTabsWrap.bind( 'easytabs:after', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-shipping-method' ) {
                this.queueUpdateCheckout( event );
            }
        } );
    }

    resetUpdateCheckoutTimer() {
        let main: Main = Main.instance;

        clearTimeout( main.updateCheckoutTimer );
    }

    queueUpdateCheckout( e?, args? ) {
        let main: Main = Main.instance;

        let code = 0;

        if ( typeof e !== 'undefined' ) {
            code = e.keyCode || e.which || 0;
        }

        if ( code === 9 ) {
            return true;
        }

        this.resetUpdateCheckoutTimer();
        jQuery( document.body ).trigger( 'cfw_queue_update_checkout' );
        main.updateCheckoutTimer = setTimeout( this.maybeUpdateCheckout.bind( this ), 1000, args );
    }

    /**
     * Queue up an update_checkout
     */
    maybeUpdateCheckout( args ) {
        let main: Main = Main.instance;

        // Small timeout to prevent multiple requests when several fields update at the same time
        this.resetUpdateCheckoutTimer();
        main.updateCheckoutTimer = setTimeout( this.triggerUpdateCheckout.bind( this ), 5, args );
    }

    /**
     * Call update_checkout
     *
     * This should be the ONLY place we call this ourselves
     */
    triggerUpdateCheckout( args? ) {
        let main: Main = Main.instance;

        if ( main.settings.is_checkout_pay_page ) {
            return;
        }

        if( ! CompleteOrderAction.initCompleteOrder ) {
            if ( typeof args === 'undefined' ) {
                args = {
                    update_shipping_method: true
                };
            }

            new UpdateCheckoutAction( 'update_checkout', main.ajaxInfo, this.getFormObject(), args ).load();
        }
    }

    /**
     * Call updated_checkout
     *
     * This should be the ONLY place we call this ourselves
     */
    triggerUpdatedCheckout( data? ) {
        if ( typeof data === 'undefined' ) {
           // If this is running in the dark, we need
           // to shim in fragments because some plugins
           // ( like WooCommerce Smart Coupons ) expect it
           data = { fragments: {} };
        }
        jQuery( document.body ).trigger( 'updated_checkout', [ data ] );
    }

    /**
     * Find the selected payment gateway and trigger a click
     *
     * Some gateways look for a click action to init themselves properly
     */
    initSelectedPaymentGateway() {
        // If there are none selected, select the first.
        if ( 0 === jQuery( 'input[name^="payment_method"][type="radio"]:checked' ).length ) {
            jQuery( 'input[name^="payment_method"][type="radio"]' ).eq(0).prop( 'checked', true );
        }

        jQuery( 'input[name^="payment_method"][type="radio"]:checked' ).trigger( 'click' );
    }

    /**
     *
     */
    setAccountCheckListener() {
        if ( cfwEventData.settings.enable_checkout_login_reminder ) {
            let email_input: any = jQuery( '#billing_email' );

            if ( email_input ) {
                // Add check to keyup event
                email_input.on( 'keyup change', this.debounce( this.triggerAccountExistsCheck, 250 ) );

                // Handles page onload use case
                this.triggerAccountExistsCheck();
            }
        }
    }

    setDefaultLoginFormListener() {
        jQuery( document.body ).on( 'click', 'a.showlogin', function() {
            jQuery( 'form.login, form.woocommerce-form--login' ).slideToggle();

            return false;
        } );
    }

    triggerAccountExistsCheck() {
        let ajax_info = Main.instance.ajaxInfo;
        let email_input: any = jQuery( '#billing_email' );

        if ( email_input ) {
            new AccountExistsAction( 'account_exists', ajax_info, email_input.val() ).load();
        }
    }

    debounce (func: any, delay: number) {
        let inDebounce
        return function() {
            const context = this
            const args = arguments
            clearTimeout(inDebounce)
            inDebounce = setTimeout(() => func(context, args), delay)
        }
    }

    /**
     *
     */
    setLogInListener() {
        let email_input: any = jQuery( '#billing_email' );

        if( email_input ) {
            let password_input: any = jQuery( '#cfw-password' );

            let login_btn: any = jQuery( '#cfw-login-btn' );

            // Fire the login action on click
            login_btn.on( 'click', () => new LoginAction( 'login', Main.instance.ajaxInfo, email_input.val(), password_input.val()).load() );
        }
    }

    /**
     * Setup payment gateway radio buttons
     */
    setUpPaymentGatewayRadioButtons() {
        // The payment radio buttons to register the click events too
        let payment_radio_buttons: Array<Element> = this
            .tabContainerSectionBy( 'name', 'payment_method' )
            .getInputsFromSection( `[type="radio"][name="payment_method"]` );

        if ( payment_radio_buttons.length > 0 && jQuery(`[type="radio"][name="payment_method"]`).length && jQuery(`[type="radio"][name="payment_method"]:checked`).length == 0 ) {
            payment_radio_buttons[0].jel.prop( 'checked', true );
        }

        this.setRevealOnRadioButtonGroup( payment_radio_buttons );
    }

    /**
     * Setup payment tab address radio buttons ( Billing address )
     */
    setUpPaymentTabAddressRadioButtons() {
        // TODO: Refactor this in the future. There's no reason to use custom Element and TabSection wrappers
        let bill_to_different_address_radio_buttons                       = jQuery(`[type="radio"][name="bill_to_different_address"]`);
        let bill_to_different_address_radio_buttons_array: Array<Element> = [];

        bill_to_different_address_radio_buttons.each(( index, elem ) => {
            bill_to_different_address_radio_buttons_array.push( new Element( jQuery( elem )));
        });

        this.setRevealOnRadioButtonGroup( bill_to_different_address_radio_buttons_array, true, [this.toggleRequiredInputAttribute] );
    }

    /**
     * Handles the payment method revealing and registering the click events.
     */
    setRevealOnRadioButtonGroup( radio_buttons: Array<Element>, click_event: Boolean = true, callbacks: Array<( radio_button: Element ) => void> = [] ) {
        // Register the slide up and down container on click
        radio_buttons
            .forEach(( radio_button: Element ) => {
                let $radio_button = radio_button.jel;

                if ( click_event ) {
                    $radio_button.on( 'click', () => {
                        this.toggleRadioButtonContainers( radio_button, radio_buttons, callbacks );
                    });
                }

                if( $radio_button.is( ':checked' ) ) {
                    this.toggleRadioButtonContainers( radio_button, radio_buttons, callbacks );
                }
            });
    }

    toggleRadioButtonContainers( radio_button: Element, radio_buttons: Array<Element>, callbacks: Array<( radio_button: Element ) => void>) {
        // Filter out the current radio button
        // Slide up the other containers
        radio_buttons
            .filter(( filterItem: Element ) => filterItem != radio_button )
            .forEach(( other: Element ) => {
                other.jel.parents( '.cfw-radio-reveal-title-wrap' ).siblings( '.cfw-radio-reveal-content' ).find( ':input' ).prop( 'disabled', true );
                other.jel.parents( '.cfw-radio-reveal-title-wrap' ).siblings( '.cfw-radio-reveal-content' ).slideUp(300);
            } );

        // Slide down our container
        radio_button.jel.parents( '.cfw-radio-reveal-title-wrap' ).siblings( '.cfw-radio-reveal-content' ).find( ':input' ).prop( 'disabled', false );
        radio_button.jel.parents( '.cfw-radio-reveal-title-wrap' ).siblings( '.cfw-radio-reveal-content' ).slideDown(300);

        // Fire any callbacks
        callbacks.forEach( callback => callback( radio_button ));
    }

    toggleRequiredInputAttribute( radio_button: Element ) {
        const selected_radio_value = radio_button.jel.val();
        const shipping_dif_than_billing = 'different_from_shipping';
        const billing_selected = selected_radio_value === shipping_dif_than_billing;
        const placeholder_attribute = 'cfw-required-placeholder';
        const required_attribute = 'required';
        const attribute_value = '';
        const input_wraps = jQuery( '#cfw-billing-fields-container' ).find( '.cfw-input-wrap' );

        let toggleRequired = ( item, {search, replace, value}) => {
            if( item.hasAttribute( search )) {
                item.setAttribute( replace, value );
                item.removeAttribute( search );
            }
        };

        input_wraps.each(( index, elem ) => {
            let items = jQuery( elem ).find( 'input, select' );

            items.each(( index, item ) => {

                let attributes_data = {
                    search: billing_selected ? placeholder_attribute : required_attribute,
                    replace: billing_selected ? required_attribute : placeholder_attribute,
                    value: attribute_value
                };

                toggleRequired( item, attributes_data );
            })
        });
    }

    /**
     *
     */
    setPaymentMethodUpdate(): void {
        jQuery( document.body ).on( 'click', 'input[name^="payment_method"][type="radio"]', function() {
            if ( jQuery( this ).data( 'order_button_text' ) ) {
                jQuery( '#place_order' ).text( jQuery( this ).data( 'order_button_text' ) );
            } else {
                jQuery( '#place_order' ).text( jQuery( '#place_order' ).data( 'value' ) );
            }

            new UpdatePaymentMethod( 'update_payment_method', Main.instance.ajaxInfo, jQuery( this ).val() ).load();

            jQuery( document.body ).trigger( 'payment_method_selected' );
        });
    }

    /**
     *
     */
    setUpdateCartTriggers(): void {
        jQuery( document.body ).on( 'change', 'select.cfw-cart-quantity-input', ( event ) => {
            if ( jQuery( event.target ).val() !== 'max' ) {
                new UpdateCartAction( 'update_cart', Main.instance.ajaxInfo, this.getFormObject() ).load();
            }
        } );
    }

    /**
     *
     */
    setQuantityPromptTriggers(): void {
        jQuery( document.body ).on( 'change', 'select.cfw-cart-quantity-input', ( event ) => {
            if ( jQuery( event.target ).val() === 'max' ) {
                let response = window.prompt( cfwEventData.settings.quantity_prompt_message, '10' );

                // If we have input
                if ( null !== response ) {
                    let new_quantity = Number( response );

                    jQuery( event.target ).children(`option:selected`).val( new_quantity );

                    new UpdateCartAction( 'update_cart', Main.instance.ajaxInfo, this.getFormObject() ).load();
                } else {
                    // If no input, set back to the original value
                    jQuery( event.target ).val(jQuery( event.target ).data( 'default' ) );
                }
            }
        } );

        jQuery( document.body ).on( 'click', '.quantity-edit-label a', ( event ) => {
            let cart_item_key            = jQuery( event.target ).data('item-key');
            let cart_item_quantity_input = jQuery( `.cfw-cart-quantity-input-${cart_item_key}` );
            let response                 = window.prompt( cfwEventData.settings.quantity_prompt_message, cart_item_quantity_input.val() );

            // If we have input
            if ( null !== response ) {
                let new_quantity = Number( response );

                cart_item_quantity_input.val( new_quantity );

                new UpdateCartAction( 'update_cart', Main.instance.ajaxInfo, this.getFormObject() ).load();
            }
        } );
    }


    /**
     *
     */
    setUpdateCheckoutHandler() {
        jQuery( document.body ).on( 'update_checkout', ( e, args ) => {
            this.queueUpdateCheckout( e, args );
        });
    }

    /**
     *
     */
    setUpMobileCartDetailsReveal(): void {
        let showCartDetails: Element = new Element( jQuery( '#cfw-show-cart-details' ));
        showCartDetails.jel.on( 'click', function( e ) {
            e.preventDefault();
            jQuery( '#cfw-cart-details-collapse-wrap' ).slideToggle(300).parent().toggleClass( 'active' )
        });

        jQuery( window ).on( 'resize', () => {
            if( window.innerWidth >= 770) {
                jQuery( '#cfw-cart-details-collapse-wrap' ).css( 'display', 'block' );
                jQuery( '#cfw-cart-details' ).removeClass( 'active' );
            }
        });

        if( window.innerWidth >= 770) {
            jQuery( '#cfw-cart-details-collapse-wrap' ).css( 'display', 'block' );
        } else {
            jQuery( '#cfw-cart-details-collapse-wrap' ).css( 'display', 'none' );
        }
    }

    /**
     * @returns {{}}
     */
    getFormObject() {
        let main: Main = Main.instance;
        let checkout_form: any = main.checkoutForm;
        let bill_to_different_address = <string>jQuery( '[name="bill_to_different_address"]:checked' ).val();
        let $required_inputs = checkout_form.find( '.address-field.validate-required:visible' );
        let has_full_address: boolean = true;
        let lookFor: Array<string> = main.settings.default_address_fields;

        let formData = {
            post_data: checkout_form.serialize()
        };

        if ( $required_inputs.length ) {
            $required_inputs.each( function() {
                if ( jQuery( this ).find( ':input' ).val() === '' ) {
                    has_full_address = false;
                }
            });
        }

        let formArr: Array<Object> = checkout_form.serializeArray();
        formArr.forEach(( item: any ) => formData[item.name] = item.value );

        // Handle shipped subscriptions since they are render outside of the form
        jQuery( '#cfw-other-totals input[name^="shipping_method"][type="radio"]:checked, #cfw-other-totals input[name^="shipping_method"][type="hidden"]' ).each(( index, el ) => {
            formData[ jQuery( el ).attr( 'name' ) ] = jQuery( el ).val();
        });

        formData['has_full_address'] = has_full_address;
        formData['bill_to_different_address'] = bill_to_different_address;

        if( bill_to_different_address === 'same_as_shipping' ) {
            lookFor.forEach( field => {
                if( jQuery(`#billing_${field}`).length > 0) {
                    formData[`billing_${field}`] = formData[`shipping_${field}`];

                    // Make sure the post_data has the same info
                    formData['post_data'] = formData['post_data'] + `&billing_${field}=` + formData[`shipping_${field}`];
                }
            });
        }

		/**
         * Some gateways remove the checkout and shipping fields. If guest checkout is enabled we need to check for
         * these fields
		 */
		if( jQuery( '#cfw-first-for-plugins #billing_first_name' ).length > 0 && jQuery( '#cfw-last-for-plugins #billing_last_name' ).length > 0) {
            formData['billing_first_name'] = jQuery( '#cfw-first-for-plugins #billing_first_name' ).val();
            formData['billing_last_name'] = jQuery( '#cfw-last-for-plugins #billing_last_name' ).val();
        }

        return formData;
    }

    /**
     *
     */
    setTermsAndConditions(): void {
        const termsAndConditionsLinkClass: string = 'woocommerce-terms-and-conditions-link';
        const termsAndConditionsContentClass: string = 'woocommerce-terms-and-conditions';

        let termsAndConditionsLink: Element = new Element( jQuery(`.${termsAndConditionsLinkClass}`));
        let termsAndConditionsContent: Element = new Element( jQuery(`.${termsAndConditionsContentClass}`));

        termsAndConditionsLink.jel.on( 'click', ( eventObject ) => {
            eventObject.preventDefault();

            termsAndConditionsContent.jel.slideToggle(300);
        });
    }

    setCreateAccountCheckboxListener(): void {
        if ( ! cfwEventData.settings.registration_generate_password ) {
            let create_account_checkbox = jQuery(`#createaccount`);
            let account_password_slide  = jQuery(`#cfw-account-password-slide`);
            let account_password        = jQuery(`#account_password`);

            create_account_checkbox.change( function() {
                if ( jQuery(this).is(':checked') ) {
                    account_password_slide.slideDown(300);
                    account_password.attr( 'data-parsley-group', account_password.attr( 'data-parsley-group-old' ) )
                        .removeAttr( 'data-parsley-group-old' );
                } else {
                    account_password_slide.slideUp(300);
                    account_password.attr( 'data-parsley-group-old', account_password.attr( 'data-parsley-group' ) )
                        .removeAttr( 'data-parsley-group' );
                }
            } ).trigger('change');
        }
    }

    /**
     *
     */
    setCompleteOrderHandlers(): void {
        let checkout_form: any = Main.instance.checkoutForm;

        checkout_form.on( 'submit', this.completeOrderSubmitHandler.bind( this ) );
    }

    /**
     *
     */
    completeOrderSubmitHandler( e ) {
        let main: Main = Main.instance;
        let checkout_form: any = main.checkoutForm;
        let lookFor: Array<string> = main.settings.default_address_fields;
        let preSwapData = this.checkoutDataAtSubmitClick = {};

        if ( checkout_form.is( '.processing' ) ) {
            return false;
        }

        CompleteOrderAction.initCompleteOrder = true;

        Main.addOverlay();

        checkout_form.find( '.woocommerce-error' ).remove();

        jQuery( document.body ).on( 'checkout_error', () => {
            checkout_form.removeClass( 'processing' ).unblock(); // compatibility with gateways / plugins that expect this
            Main.removeOverlay();
            CompleteOrderAction.initCompleteOrder = false
        });

        if ( checkout_form.find( 'input[name="bill_to_different_address"]:checked' ).val() === "same_as_shipping" ) {
            lookFor.forEach( field => {
                let billing = jQuery(`#billing_${field}`);
                let shipping = jQuery(`#shipping_${field}`);

                if( billing.length > 0) {
                    preSwapData[field] = billing.val();

                    billing.val( shipping.val());
                    billing.trigger( 'keyup' );
                }
            });
        }

        // If all the payment stuff has finished any ajax calls, run the complete order.
        if ( checkout_form.triggerHandler( 'checkout_place_order' ) !== false && checkout_form.triggerHandler( 'checkout_place_order_' + checkout_form.find( 'input[name="payment_method"]:checked' ).val() ) !== false ) {
            checkout_form.addClass( 'processing' );

            // Reset data
            for ( let field in preSwapData ) {
                let billing = jQuery(`#billing_${field}`);

                billing.val( preSwapData[field]);
            }

            this.orderKickOff( main.ajaxInfo, this.getFormObject());
        } else {
            checkout_form.removeClass( 'processing' ).unblock();
        }

        /**
         * Throwing an error here seems to cause situations where the error briefly appears during a successful order
         */
        return false;
    }

    /**
     *
     * @param {AjaxInfo} ajaxInfo
     * @param data
     */
    orderKickOff( ajaxInfo: AjaxInfo, data ): void {
        new CompleteOrderAction( 'complete_order', ajaxInfo, data );
    }

    /**
     *
     */
    setApplyCouponListener() {
        let promo_apply_button = jQuery( '#cfw-promo-code-btn' );

        jQuery( '#cfw-promo-code' ).on( 'keypress', function( e ) {
            if ( e.which == 13 ) {
                e.preventDefault();

                promo_apply_button.trigger( 'click' );
            }
        } );

        promo_apply_button.on( 'click', () => {
            let coupon_field: any = jQuery( '#cfw-promo-code' );

            if ( coupon_field.val() !== '' ) {
                new ApplyCouponAction( 'cfw_apply_coupon', Main.instance.ajaxInfo, coupon_field.val(), this.getFormObject() ).load();
            }
        } );
    }

    /**
     * @param by
     * @param value
     * @returns {TabContainerSection}
     */
    tabContainerSectionBy( by: string, value: any ): TabContainerSection {
        return <TabContainerSection>this.tabContainerSections.find(( tabContainerSection: TabContainerSection ) => tabContainerSection[by] == value );
    }

    /**
     * @returns {TabContainerBreadcrumb}
     */
    get tabContainerBreadcrumb(): TabContainerBreadcrumb {
        return this._tabContainerBreadcrumb;
    }

    /**
     * @param value
     */
    set tabContainerBreadcrumb( value: TabContainerBreadcrumb ) {
        this._tabContainerBreadcrumb = value;
    }

    /**
     * @returns {Array<TabContainerSection>}
     */
    get tabContainerSections(): Array<TabContainerSection> {
        return this._tabContainerSections;
    }

    /**
     * @param value
     */
    set tabContainerSections( value: Array<TabContainerSection>) {
        this._tabContainerSections = value;
    }

    /**
     * @returns {any}
     */
    get checkoutDataAtSubmitClick(): any {
        return this._checkoutDataAtSubmitClick;
    }

    /**
     * @param value
     */
    set checkoutDataAtSubmitClick( value: any ) {
        this._checkoutDataAtSubmitClick = value;
    }
}