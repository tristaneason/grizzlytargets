jQuery(document).ready( function() {
    jQuery('.color-picker').wpColorPicker();
});


jQuery( document ).ready( function( $ ) {

    // Uploading files
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    var set_to_post_id = objectiv_cfw_admin.logo_attachment_id;

    jQuery("#template_select").on('change', function() {
       let template_value = jQuery("#template_select").val();

       jQuery(".template_select_info_table_screen_shot_container").each(function(index, el) { jQuery(el).css("display", "none") });

       jQuery("#template_select_info_table_screen_shot_container_" + template_value).css("display", "flex");
    }).trigger('change');

    jQuery('#upload_image_button').on('click', function( event ){

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( file_frame ) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Select a image to upload',
            button: {
                text: 'Use this image',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on( 'select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();

            // Do something with attachment.id and/or attachment.url here
            $( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
            $( '#logo_attachment_id' ).val( attachment.id );

            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;
        });

        // Finally, open the modal
        file_frame.open();
    });

    // Restore the main ID when the add media button is pressed
    jQuery( 'a.add_media' ).on( 'click', function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });

    // DELETE IMAGE LINK
    $('.delete-custom-img').on( 'click', function( event ){

        event.preventDefault();

        $("#logo_attachment_id").val('');
        $('#image-preview' ).attr( 'src', '' ).css( 'width', 'auto' );
    });

    let show_hide_cart_redirect_url = function() {
        let cart_editing_redirect_url = jQuery( '#cart_edit_empty_cart_redirect' ).parents('tr');
        if ( jQuery(`#enable_cart_editing`).is(':checked') && jQuery(`#enable_cart_editing`).is(':enabled') ) {
            cart_editing_redirect_url.show();
        } else {
            cart_editing_redirect_url.hide();
        }
    };

    jQuery('#enable_cart_editing').on( 'change', show_hide_cart_redirect_url );

    show_hide_cart_redirect_url();

    let show_hide_address_autocomplete_fields = function() {
        let google_places_api_key = jQuery( '#google_places_api_key' ).parents('tr');

        if ( ( jQuery(`#enable_address_autocomplete`).is(':enabled') && jQuery(`#enable_address_autocomplete`).is(':checked') ) || ( jQuery(`#enable_thank_you_page`).is(':enabled') && jQuery(`#enable_thank_you_page`).is(':checked') && jQuery(`#enable_map_embed`).is(':checked') ) ) {
            google_places_api_key.show();
        } else {
            google_places_api_key.hide();
        }
    };

    jQuery(`#enable_address_autocomplete, #enable_map_embed`).on( 'change', show_hide_address_autocomplete_fields );

    show_hide_address_autocomplete_fields();

    let show_hide_thank_you_options = function () {
        let map_embed_option = jQuery( '#enable_map_embed' ).parents('tr');
        let thank_you_order_statuses_option = jQuery(`#thank_you_order_statuses`).parents('tr');

        if ( jQuery(`#enable_thank_you_page`).is(':enabled') && jQuery(`#enable_thank_you_page`).is(':checked') ) {
            map_embed_option.show();
            thank_you_order_statuses_option.show();
        } else {
            map_embed_option.hide();
            thank_you_order_statuses_option.hide();
        }

        show_hide_address_autocomplete_fields();
    }

    jQuery('#enable_thank_you_page').on( 'change', show_hide_thank_you_options );
    show_hide_thank_you_options();

    jQuery( document.body ).trigger( 'wc-enhanced-select-init' );
});