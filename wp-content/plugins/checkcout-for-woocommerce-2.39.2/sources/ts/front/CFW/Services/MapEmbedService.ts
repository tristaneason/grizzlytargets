declare let jQuery: any;
declare let google: any;

export class MapEmbedService {
    /**
     * Attach change events to postcode fields
     */
    setMapEmbedHandlers() {
        if ( (<any>window).cfwEventData.settings.enable_map_embed === true ) {
            jQuery( document ).on( 'ready', this.initMap );
        }
    }

    initMap() {
        if ( jQuery(`#map`).lenght == 0 || typeof google === 'undefined' ) {
            return;
        }

        let map = new google.maps.Map( document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 15,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false
        } );

        let geocoder = new google.maps.Geocoder();

        geocoder.geocode( { 'address': (<any>window).cfwEventData.settings.thank_you_shipping_address }, function(results, status) {
            if ( status == google.maps.GeocoderStatus.OK ) {
                map.setCenter(results[0].geometry.location);

                let marker = new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location
                });

                let parts = <any>results[0].address_components.reduce( ( parts, component ) => {
                    parts[ component.types[0] ] = component.long_name || '';

                    return parts;
                }, {} );

                let shipping_address_label = (<any>window).cfwEventData.settings.shipping_address_label;
                let city                   = parts.locality || parts.postal_town || parts.sublocality_level_1 || parts.administrative_area_level_2 || parts.administrative_area_level_3;
                let state                  = parts.administrative_area_level_1;
                let shipping_address       = city;

                if ( state.length !== 0 ) {
                    shipping_address = `${shipping_address}, ${state}`;
                }

                let contentString          = `<div id="info_window_content"><span class="small-text">${shipping_address_label}</span><br /><span class="emphasis">${shipping_address}</span></div>`;
                let infowindow             = new google.maps.InfoWindow({
                    content: contentString
                });

                infowindow.open(map, marker);

            } else {
                jQuery(`#map`).hide();
            }
        });
    }
}