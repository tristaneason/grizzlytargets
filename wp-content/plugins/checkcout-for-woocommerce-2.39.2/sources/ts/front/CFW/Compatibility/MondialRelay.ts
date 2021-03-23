import { Compatibility }    from "./Compatibility";
import { Main }             from "../Main";
import { Alert, AlertInfo } from "../Elements/Alert";

declare let jQuery: any;
declare let mrwpPluginSettings: any;
declare function mrwp_prepare_shipping() : boolean;
declare function mrwpParcelPickerInit() : void;
declare function mrwpShippingCode( shippingIds: string, selectedShipping: string ) : string;
declare function mrwpNeedsParcelPicker( option: boolean ) : boolean;

export class MondialRelay extends Compatibility {
    /**
     * @param {Main} main The Main object
     * @param {any} params Params for the child class to run on load
     * @param {boolean} load Should load be fired on instantiation
     */
    constructor( main: Main, params, load: boolean = true ) {
        super( main, params, load, 'MondialRelay' );
    }

    load( main: Main ): void {
        jQuery( document.body ).on( 'cfw_updated_checkout', () => {
            let main = Main.instance;

            let same_as_shipping = jQuery( 'input[name="bill_to_different_address"]:checked' ).val();

            if ( same_as_shipping === 'same_as_shipping' ) {
                jQuery( '#billing_country' ).val( jQuery( '#shipping_country' ).val() );
                jQuery( '#billing_postcode' ).val( jQuery( '#shipping_postcode' ).val() );
            }

            if ( ! main.force_updated_checkout && mrwp_prepare_shipping() ) {
                jQuery("#mrwp_weight").attr("value", mrwpPluginSettings.mondialrelay_weight );
                let mrwpShippingRaw = mrwpPluginSettings.mondialrelay_ids_livraison,
                    mrwpShipping = JSON.parse( mrwpShippingRaw ),
                    availableShippingOptions = jQuery( 'input[name^="shipping_method"]' ),
                    selectedShippingOption = jQuery( 'input[name^="shipping_method"]:checked' ),
                    selectedShipping;
                if ( selectedShippingOption.length > 0)
                    selectedShipping = selectedShippingOption.val();
                else {
                    if (!( availableShippingOptions.length > 0))
                        return;
                    selectedShipping = jQuery( 'input[name^="shipping_method"]' ).val()
                }
                let currentShippingCode = mrwpShippingCode( mrwpShipping, selectedShipping );
                if ( currentShippingCode == jQuery("#mrwp_shipping_code").val()) {
                    let previousAddress = jQuery("#mrwp_parcel_shop_address").val();
                    jQuery("#parcel_shop_info").html( previousAddress )
                } else
                    jQuery("#mrwp_shipping_code").attr("value", currentShippingCode ), jQuery("#mrwp_parcel_shop_id").attr("value", ""), jQuery("#mrwp_parcel_shop_address").attr("value", "");
                "DRI" != currentShippingCode && -1 == currentShippingCode.indexOf("24") ? mrwpNeedsParcelPicker(!1) : ( mrwpNeedsParcelPicker(!0), mrwpParcelPickerInit())
            }
        } );

        jQuery( '#cfw-shipping-action' ).hide();

        jQuery( document.body ).on( 'cfw_updated_checkout', function() {
            jQuery( '#cfw-shipping-action' ).show();
        } );

        let easyTabsWrap: any = main.easyTabService.easyTabsWrap;

        easyTabsWrap.bind( 'easytabs:before', ( event, clicked, target ) => {
            if ( jQuery( target ).attr( 'id' ) == 'cfw-payment-method' ) {
                if ( jQuery( '#mrwp_parcel_shop_mandatory' ).val() == "Yes" ) {
                    if ( jQuery( '#mrwp_parcel_shop_id' ).val() == '' ) {
                        // Prevent removing alert on next update checkout
                        Main.instance.preserve_alerts = true;

                        let alertInfo: AlertInfo = {
                            type: "error",
                            message: 'Vous n\'avez pas encore choisi de Point Relais.',
                            cssClass: "cfw-alert-error"
                        };

                        let alert: Alert = new Alert( Main.instance.alertContainer, alertInfo );
                        alert.addAlert( true );

                        event.stopImmediatePropagation();

                        return false;
                    }
                }
            }
        } );
    }
}