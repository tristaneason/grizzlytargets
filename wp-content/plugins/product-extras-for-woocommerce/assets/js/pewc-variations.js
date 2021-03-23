(function($) {

	$(document).ready(function() {

		$( 'body' ).find( '.pewc-variable-child-product-wrapper select' ).each( function() {
			update_variation( $( this ) );
		});
		$( 'body' ).on( 'pewc_update_child_quantity', function( e, el ) {
			// This is triggered when the quantity field for a child product is updated
			update_checkbox_image_wrapper( el );
		});
		$( '.pewc-column-form-field' ).on( 'change', function() {
			update_checkbox_image_wrapper( $( this ) );
		});
		$( '.pewc-add-button' ).on( 'click', function( e ) {
			e.preventDefault();
			var button = $( this );
			var wrapper = $( this ).closest( '.pewc-checkbox-image-wrapper' );
			var checkbox = $( wrapper ).find( '.pewc-column-form-field' );
			var manage_stock = $( wrapper ).data( 'manage-stock' );
			if( manage_stock && $( wrapper ).hasClass( 'pewc-simple-child-product-wrapper' ) ) {
				var stock = $( wrapper ).find( 'input.pewc-child-quantity-field' ).attr( 'max' );
				if( ! stock ) stock = true;
			} else if( manage_stock ) {
				var stock = $( wrapper ).find( '.pewc-variable-child-select option:selected' ).data( 'stock' );
			}

			var independent_quantities = $( button ).closest( '.pewc-column-wrapper' ).hasClass( 'products-quantities-independent' );
			// if( $( checkbox ).attr( 'checked' ) == 'checked' ) {
			if( $( wrapper ).hasClass( 'checked' ) ) {
				$( checkbox ).prop( 'checked', false );
				$( wrapper ).removeClass( 'checked' );
				$( wrapper ).find( 'input.pewc-child-quantity-field' ).val( 0 );
			} else {
				$( checkbox ).prop( 'checked', true );
				$( wrapper ).addClass( 'checked' );
				// If stock is available, or if we're not managing stock so don't care
				if( stock || ! manage_stock ) {
					// Update quantity field
					$( wrapper ).find( 'input.pewc-child-quantity-field' ).val( 1 );
				}
			}
			$( 'body' ).trigger( 'pewc_add_button_clicked' );
		});
		$( '.pewc-variable-child-select' ).on( 'change', function() {
			var select = $( this );
			// If we change the variation, then select the product
			var checkbox = $( select ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input[type=checkbox]' ).attr( 'checked' , true );
			var quantity = $( select ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-child-quantity-field' );
			if( $( quantity ).val() == 0 ) {
				$( quantity ).val( 1 );
			}
			update_checkbox_image_wrapper( $( select ) );
			// Check available stock
			var stock = $( select ).find( ':selected' ).data( 'stock' );
			if( stock ) {
				// Restrict quantity field to available stock
				var independent_quantities = $( select ).closest( '.pewc-column-wrapper' ).hasClass( 'products-quantities-independent' );
				var quantity = $( select ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-child-quantity-field' ).attr( 'max', stock );
			}
			update_variation( select );
		});
		function update_variation( select ) {
			var variation_id = $( select ).val();
			var variation_data = $( select ).data( 'product_variations' );
			for ( var i = 0; i < variation_data.length; i++ ) {
				var variation = variation_data[i];
				if( variation.variation_id == variation_id ) {
					var wrapper = $( select ).closest( '.pewc-checkbox-desc-wrapper' );
					// Found the attribute
					$( wrapper ).find( '.pewc-column-description' ).html( variation.variation_description + '<p class="pewc-variation-price">' + variation.price_html + '</div>' + variation.availability_html );
					// $( wrapper ).find( '.pewc-column-price-wrapper' ).html( variation.price_html );
					break;
				}
			}
		}
		function update_checkbox_image_wrapper( el ) {
			var id = $( el ).closest( '.pewc-checkbox-image-wrapper' ).attr( 'data-option-id' );
			var wrapper = $( el ).closest( '.pewc-checkbox-image-wrapper' );
			var checked = $( wrapper ).find( '.pewc-column-form-field' ).prop( 'checked' );
			if( checked == true ) {
				$( '.' + id ).addClass( 'checked' );
			} else {
				$( '.' + id ).removeClass( 'checked' );
			}
		}

		var swatches = {

			init: function() {

				$( 'body' ).on( 'click', '.pewc-variation-swatch a', this.update_swatch_wrapper );
				$( 'body' ).on( 'click', '.pewc-swatches-toggle', this.toggle_swatch );

			},

			toggle_swatch: function( e ) {

				e.preventDefault();
				var wrapper = $( this ).closest( '.pewc-swatches-child-product-outer' );
				$( wrapper ).toggleClass( 'visible-swatch' );
			},

			update_swatch_wrapper: function( e ) {

				e.preventDefault();
				var swatch = $( this ).closest( '.pewc-variation-swatch' );
				var variation_id = $( swatch ).attr( 'data-variation-id' );
				var wrapper = $( this ).closest( '.pewc-swatches-child-product-outer' );
				$( wrapper ).find( '.pewc-variation-swatch img' ).removeClass( 'active-swatch' );
				$( swatch ).find( 'img' ).addClass( 'active-swatch' );
				var update_selected_id = $( wrapper ).find( 'input.pewc-child-variant' ).val( variation_id );
				var image = $( swatch ).find( 'img' ).attr( 'src' );
				var viewer_image = $( swatch ).attr( 'data-viewer-image' );
				var price = $( swatch ).attr( 'data-option-cost' );
				var name = $( swatch ).attr( 'data-name' );
				var sku = $( swatch ).attr( 'data-sku' );
				if( image ) {
					$( wrapper ).find( '.pewc-child-thumb img' ).attr( 'src', image );
				}
				if( viewer_image ) {
					$( wrapper ).find( '.pewc-viewer-thumb img' ).attr( 'src', viewer_image );
				}
				if( price ) {
					$( wrapper ).find( '.pewc-child-name input' ).attr( 'data-option-cost', price );
					var currency = $( wrapper ).find( '.pewc-child-name .pewc-swatches-main-title span.pewc-variation-price' ).html( pewc_get_wc_price( price ) );
				}
				if( name ) {
					$( wrapper ).find( 'span.pewc-variation-name' ).html( name );
					$( wrapper ).find( '.pewc-viewer-title' ).html( name );
				}
				if( sku ) {
					$( wrapper ).find( '.pewc-variation-sku' ).html( sku );
					$( wrapper ).find( '.pewc-viewer-sku' ).html( sku );
				}

			},

		};

		swatches.init();

		var grid = {

			init: function() {

				$( 'body' ).on( 'change keyup', '.grid-layout .pewc-grid-quantity-field', this.update_grid_quantities );

			},

			update_grid_quantities: function( e ) {

				e.preventDefault();
				var field_price = 0;
				var total = 0;
				var field = $( this ).closest( '.pewc-item' );
				$( field ).find( '.pewc-grid-quantity-field' ).each( function() {
					var child_price = $( this ).val() * $( this ).attr( 'data-option-cost' );
					field_price += child_price;
					total += parseFloat( $( this ).val() );
				});
				$( field ).attr( 'data-price', field_price );
				$( '#pewc-grid-total-variations' ).val( total );
				$( 'body' ).trigger( 'pewc_force_update_total_js' );

			},

		};

		grid.init();

		function pewc_get_wc_price( price ) {
			var return_html, price_html, formatted_price;
			if( pewc_vars.currency_pos == 'left' ) {
				formatted_price = pewc_vars.currency_symbol + '&#x200e;' + price;
			} else if( pewc_vars.currency_pos == 'right' ) {
				formatted_price = price + pewc_vars.currency_symbol + '&#x200f;';
			} else if( pewc_vars.currency_pos == 'left_space' ) {
				formatted_price = pewc_vars.currency_symbol + '&#x200e;&nbsp;' + price;
			} else if( pewc_vars.currency_pos == 'right_space' ) {
				formatted_price = price + '&nbsp;' + pewc_vars.currency_symbol + '&#x200f;';
			}
			formatted_price = formatted_price.replace('.',pewc_vars.decimal_separator);
			price_html = '<span class="woocommerce-Price-currencySymbol">' + formatted_price + '</span>';
			return_html = '<span class="woocommerce-Price-amount amount">' + price_html + '</span>';

			$('#pewc_total_calc_price').val( price ); // Used in Bookings for WooCommerce

			return return_html;
		}

	});
})(jQuery);
