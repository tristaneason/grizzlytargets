import { MapEmbedService } from "./front/CFW/Services/MapEmbedService";
import {Element} from "./front/CFW/Elements/Element";

declare let cfwEventData: any;

export class ThankYou {
    constructor() {
        let map_embed_service = new MapEmbedService();

        map_embed_service.setMapEmbedHandlers();

        jQuery( document ).on('ready', () => {
            jQuery(`.status-step-selected`).prevAll().addClass('status-step-selected');

            this.setUpMobileCartDetailsReveal();
        } );
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
}

let thankyou = new ThankYou();