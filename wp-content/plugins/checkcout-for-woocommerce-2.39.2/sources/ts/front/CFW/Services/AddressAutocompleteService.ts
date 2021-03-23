declare let jQuery: any;
declare let google: any;
declare let cfwEventData: any;

export class AddressAutocompleteService {

    private _address_formats = {
        "DE": "street_name house_number",
        "AL": "street_name house_number",
        "AO": "street_name house_number",
        "AR": "street_name house_number",
        "AT": "street_name house_number",
        "BY": "street_name house_number",
        "BE": "street_name house_number",
        "BO": "street_name house_number",
        "BA": "street_name house_number",
        "BW": "street_name house_number",
        "BR": "street_name, house_number",
        "BN": "house_number, street_name",
        "BG": "street_name house_number",
        "BI": "street_name house_number",
        "CM": "street_name house_number",
        "BQ": "street_name house_number",
        "CF": "street_name house_number",
        "TD": "street_name house_number",
        "CL": "street_name house_number",
        "CO": "street_name house_number",
        "KM": "street_name house_number",
        "HR": "street_name house_number",
        "CW": "street_name house_number",
        "CZ": "street_name house_number",
        "DK": "street_name house_number",
        "DO": "street_name house_number",
        "EC": "street_name house_number",
        "SV": "street_name house_number",
        "GQ": "street_name house_number",
        "ER": "street_name house_number",
        "EE": "street_name house_number",
        "ET": "street_name house_number",
        "FO": "street_name house_number",
        "FI": "street_name house_number",
        "GR": "street_name house_number",
        "GL": "street_name house_number",
        "GD": "street_name house_number",
        "GT": "street_name house_number",
        "GN": "street_name house_number",
        "GW": "street_name house_number",
        "HT": "street_name house_number",
        "VA": "street_name house_number",
        "HN": "street_name house_number",
        "HU": "street_name house_number",
        "IS": "street_name house_number",
        "IR": "street_name house_number",
        "IT": "street_name house_number",
        "JO": "street_name house_number",
        "KZ": "street_name house_number",
        "KI": "street_name house_number",
        "KW": "street_name house_number",
        "KG": "street_name house_number",
        "LV": "street_name house_number",
        "LR": "street_name house_number",
        "LY": "street_name house_number",
        "LI": "street_name house_number",
        "LT": "street_name house_number",
        "MO": "street_name house_number",
        "MK": "street_name house_number",
        "MY": "street_name house_number",
        "ML": "street_name house_number",
        "MX": "street_name house_number",
        "MD": "street_name house_number",
        "ME": "street_name house_number",
        "MZ": "street_name, house_number",
        "NL": "street_name house_number",
        "NO": "street_name house_number",
        "PK": "house_number - street_name",
        "PA": "street_name house_number",
        "PY": "street_name house_number",
        "PE": "street_name house_number",
        "PL": "street_name house_number",
        "PT": "street_name house_number",
        "QA": "street_name house_number",
        "RO": "street_name house_number",
        "RU": "street_name house_number",
        "LC": "street_name house_number",
        "WS": "street_name house_number",
        "SM": "street_name house_number",
        "ST": "street_name house_number",
        "RS": "street_name house_number",
        "SX": "street_name house_number",
        "SK": "street_name house_number",
        "SI": "street_name house_number",
        "SB": "street_name house_number",
        "SO": "street_name house_number",
        "SS": "street_name house_number",
        "ES": "street_name, house_number",
        "SD": "street_name house_number",
        "SR": "street_name house_number",
        "SJ": "street_name house_number",
        "SE": "street_name house_number",
        "CH": "street_name house_number",
        "SY": "street_name house_number",
        "TJ": "street_name house_number",
        "TZ": "street_name house_number",
        "TR": "street_name house_number",
        "UA": "street_name house_number",
        "UY": "street_name house_number",
        "VU": "street_name house_number",
        "EH": "street_name house_number"
    };

    /**
     * Attach change events to postcode fields
     */
    constructor() {
        if ( (<any>window).cfwEventData.settings.enable_address_autocomplete !== true || typeof google === 'undefined' ) {
            return;
        }

        if ( (<any>window).cfwEventData.settings.needs_shipping_address == true ) {
            let shipping_address_1 = jQuery( '#shipping_address_1' );

            shipping_address_1.prop( 'autocomplete', 'new-password' );

            let shipping_autocomplete = new google.maps.places.Autocomplete( shipping_address_1.get(0), { types: ['geocode'] } );

            shipping_autocomplete.setFields( ['address_component'] );

            if ( false !== cfwEventData.settings.address_autocomplete_shipping_countries ) {
                shipping_autocomplete.setComponentRestrictions( { 'country': cfwEventData.settings.address_autocomplete_shipping_countries } );
            }

            shipping_autocomplete.addListener('place_changed', () => { this.fillAddress('shipping_', shipping_autocomplete) } );
        }

        let billing_address_1 = jQuery( '#billing_address_1' );

        billing_address_1.prop( 'autocomplete', 'new-password' );

        let billing_autocomplete = new google.maps.places.Autocomplete( billing_address_1.get(0), { types: ['geocode'] } );

        billing_autocomplete.setFields( ['address_component'] );

        if ( false !== cfwEventData.settings.address_autocomplete_billing_countries ) {
            billing_autocomplete.setComponentRestrictions( { 'country': cfwEventData.settings.address_autocomplete_billing_countries } );
        }

        billing_autocomplete.addListener('place_changed', () => { this.fillAddress('billing_', billing_autocomplete) } );
    }

    fillAddress( prefix: string, autocomplete_object: any ) {
        if ( ! autocomplete_object.getPlace().hasOwnProperty( 'address_components' ) ) {
            return;
        }

        let parts = <any>autocomplete_object.getPlace().address_components.reduce( ( parts, component ) => {
            parts[ component.types[0] ] = component.short_name || '';

            return parts;
        }, {} );

        // Standard format
        let address_1 = [ parts.street_number, parts.route ].filter(Boolean).join(' ');

        // If we have a special format, use it here
        if ( this._address_formats.hasOwnProperty( parts.country ) ) {
            address_1 = this._address_formats[ parts.country ].replace( 'street_name', parts.route ).replace( 'house_number', parts.street_number );
        }

        let city = parts.locality || parts.postal_town || parts.sublocality_level_1 || parts.administrative_area_level_2 || parts.administrative_area_level_3;

        // Cleanup anything undefined
        address_1 = address_1.replace( 'undefined', '' );
        city      = city.replace( 'undefined', '' );

        jQuery( '#' + prefix + 'address_1' ).val( address_1 ).trigger( 'change' ).trigger( 'keyup' );
        jQuery( '#' + prefix + 'country' ).val( parts.country ).trigger( 'change' ).trigger( 'keyup' );
        jQuery( '#' + prefix + 'postcode' ).val( parts.postal_code ).trigger( 'change' ).trigger( 'keyup' );
        jQuery( '#' + prefix + 'state' ).val( parts.administrative_area_level_1 ).trigger( 'change' ).trigger( 'keyup' );
        jQuery( '#' + prefix + 'city' ).val( city ).trigger( 'change' ).trigger( 'keyup' );
    }
}