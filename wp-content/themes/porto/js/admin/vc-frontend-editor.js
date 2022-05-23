jQuery( document ).ready( function ( $ ) {
	'use strict';

	$( 'body' ).on( 'tabsbeforeactivate', '.wpb_tour_tabs_wrapper', function ( e, ui ) {
		ui.oldTab.removeClass( 'active' );
		ui.newTab.addClass( 'active' );
	} );

	$( '.compose-mode .vc_controls-bc .vc_control-btn-append' ).each( function () {
		$( this ).insertAfter( $( this ).closest( '.vc_controls' ).find( '.vc_control-btn-prepend' ) );
	} );

	if ( window.parent.vc && window.parent.vc.events ) {
		window.parent.vc.events.on( 'shortcodes:add', function ( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent && 'porto_carousel' == parent.attributes.shortcode ) {
				var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
				if ( $obj.length ) {
					$obj.removeData( '__carousel' );
					$obj.trigger( 'destroy.owl.carousel' );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:ready', function ( e ) {
			var shortcode = e.attributes.shortcode;
			if ( 'porto_scroll_progress' == shortcode ) {
				if ( $( 'script#porto-scroll-progress-js' ).length ) {
					$( document.body ).trigger( 'porto_init_scroll_progress', [ e.view.$el ] );
				} else {
					$( document.createElement( 'script' ) ).attr( 'id', 'porto-scroll-progress-js' ).appendTo( 'body' ).attr( 'src', js_porto_vars.ajax_loader_url.replace( '/images/ajax-loader@2x.gif', '/js/libs/porto-scroll-progress.min.js' ) ).on( 'load', function () {
						$( document.body ).trigger( 'porto_init_scroll_progress', [ e.view.$el ] );
					} );
				}
			}
		} );

		window.parent.vc.events.on( 'shortcodeView:destroy', function ( model ) {
			var parent_id = model.attributes.parent_id;
			if ( !parent_id ) {
				return;
			}
			var parent = window.parent.vc.shortcodes.get( parent_id );
			if ( parent && 'porto_carousel' == parent.attributes.shortcode ) {
				var $obj = $( '[data-model-id="' + parent.attributes.id + '"]' ).children( '.owl-carousel' );
				if ( $obj.length ) {
					$obj.removeData( '__carousel' );
					$obj.trigger( 'destroy.owl.carousel' );
					$obj.children( '.owl-item:empty' ).remove();
					$obj.themeCarousel( $obj.data( 'plugin-options' ) );
				}
			}
		} );
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