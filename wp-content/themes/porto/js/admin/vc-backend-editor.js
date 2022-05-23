jQuery( document ).ready( function ( $ ) {
	'use strict';
	if ( window.parent.vc && window.parent.vc.events && vc.storage ) {
		window.parent.vc.edit_element_block_view.on( 'afterRender', function () {
			var $el = this.$el,
				widgets = [ 'porto_ultimate_heading', 'porto_buttons', 'porto_image_comparison', 'porto_interactive_banner', 'vc_custom_heading', 'vc_btn', 'porto_countdown', 'vc_single_image' ];
			if ( $.inArray( $el.attr( 'data-vc-shortcode' ), widgets ) >= 0 ) {
				$el.find( 'select' ).each( function () {
					var $this = $( this ),
						el_class = $this.attr( 'class' ),
						index_last = el_class.indexOf( '_dynamic_source' );
					if ( index_last >= 0 ) {
						var index_first = el_class.lastIndexOf( ' ', index_last );
						if ( index_first == -1 ) {
							index_first = 0;
						}
						var field_name = el_class.substring( index_first, index_last ).trim(),
							field_index = field_name.indexOf( '_' ),
							field_type = '';
						if ( field_index > 0 ) {
							field_type = field_name.substring( 0, field_index );
						} else {
							field_type = field_name;
						}
						if ( field_type == 'field' || field_type == 'link' || field_type == 'image' ) {
							porto_wpb_dynamic_execute( $el, field_type, field_name );
						}
					}
				} );
			}
		} );
		function porto_wpb_dynamic_execute( $el, field_type, field_name ) {
			var dynamic_source_object = $el.find( 'select.' + field_name + '_dynamic_source' ),
				dynamic_source = dynamic_source_object.val(),
				dynamic_content = $el.find( 'select.' + field_name + '_dynamic_content' );
			porto_wpb_dyanmic_content( dynamic_source, field_type, dynamic_content );

			dynamic_source_object.on( 'change', function () {
				dynamic_source = $( this ).val();
				porto_wpb_dyanmic_content( dynamic_source, field_type, dynamic_content );
			} );

		}
		function porto_wpb_dyanmic_content( dynamic_source, field_type, dynamic_content ) {
			dynamic_content.find( '*' ).remove();
			if ( '' != dynamic_source && 'meta_field' != dynamic_source && dynamic_content.length && !dynamic_content.hasClass( '.vc_dependent-hidden' ) && porto_wpb_vars[ dynamic_source ] ) {
				if ( porto_wpb_vars[ dynamic_source ][ field_type ] ) {
					var $contents = porto_wpb_vars[ dynamic_source ][ field_type ],
						keys = Object.keys( $contents ),
						attribute = dynamic_content.attr( 'data-option' ), selected_content = false;

					if ( keys.length ) {
						dynamic_content.append( '<option class="" value="">Select Source...</option>' );
						for ( let index = 0; index < keys.length; index++ ) {
							var selected = '';
							if ( keys[ index ] == attribute ) {
								selected = 'selected="selected"';
								selected_content = true;
							}
							dynamic_content.append( '<option class="' + keys[ index ] + '" value="' + keys[ index ] + '" ' + selected + '>' + $contents[ keys[ index ] ] + '</option>' );
						}
					}
					if ( selected_content ) {
						dynamic_content.val( attribute ).addClass( attribute );
					}
				}
			}
		}
	}
} );