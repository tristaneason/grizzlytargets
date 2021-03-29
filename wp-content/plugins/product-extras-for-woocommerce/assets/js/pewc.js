(function($) {

	var total_updated = false;

	$( document ).ready( function() {

		// $( 'body').find( '.dz-default.dz-message span' ).text( pewc_vars.drop_files_message );
		// Init each select-box separately
		$( '.pewc-select-box' ).each( function( index, element ) {
			$( element ).ddslick({
				onSelected: function( selectedData ) {
					var original = selectedData.original[0];
					var index = selectedData.selectedIndex;
					var value = selectedData.selectedData.value;
					// var box_id = selectedData.original.context.id;
					var box_id = $( original ).attr( 'id' );
					// Update field price
					var hidden_option_id = box_id.replace( '_select_box', '' ) + '_' + index + '_hidden';
					var price = $( '#' + hidden_option_id ).attr( 'data-option-cost' );
					// var wrapper = $( '#' + box_id ).closest( '.pewc-item' ).addClass( 'xx' );
					var wrapper = $( '#' + box_id ).closest( '.pewc-item' ).attr( 'data-selected-option-price', price ).attr( 'data-value', value );
					var hidden_select = $( '#' + box_id + '_' + index ).closest( '.pewc-item' ).find( '.pewc-select-box-hidden' ).attr( 'data-selected-option-price', price ).val( value );
					pewc_update_total_js();
					var select_box_wrapper = $( 'body' ).find( '.pewc-item-select-box .dd-container' );
					// $( select_box_wrapper ).attr( 'id', $( select_box_wrapper ).attr( 'id' ) + '_select_box' );
					$( 'body' ).find( '.dd-option label, .dd-selected label' ).each( function() {
						$( this ).next( 'small' ).addBack().wrapAll( '<div class="dd-option-wrap"/>' );
					});
					box_id = box_id.replace( '_select_box', '' );
					// Update the field attributes

					$( 'body' ).find( '#' + box_id ).val( value ).trigger( 'change' );
					var selected_option_price = $( '#' + box_id ).find( 'option:selected' ).attr( 'data-option-cost' );
					$( wrapper ).attr( 'data-selected-option-price', selected_option_price );
					$( wrapper ).find( '.dd-selected-description' ).text( pewc_wc_price( selected_option_price, true ) );
				}
			});
		});

		// Init each color-picker separately
		$( '.pewc-color-picker-field' ).each( function ( index, element ) {
			$(element).wpColorPicker({
				defaultColor: $(element).data('color') ? $(element).data('color') : false,
				change: function() { $( 'body' ).trigger( 'pewc_trigger_calculations' ); }, // a callback to fire whenever the color changes to a valid color
				clear: function() { $( 'body' ).trigger( 'pewc_trigger_calculations' ); }, // a callback to fire when the input is emptied or an invalid color
				hide: !$(element).data('show'), // hide the color picker controls on load
				palettes: !!$(element).data('palettes'), // show a group of common colors beneath the square
				width: $(element).data('box-width') ? $(element).data('box-width') : 255,
				mode: 'hsv',
				type: 'full',
				slider: 'horizontal'
			});
		});

		$( 'body' ).find( '.dd-option label, .dd-selected label' ).each( function() {
			$( this ).next( 'small' ).addBack().wrapAll( '<div class="dd-option-wrap"/>' );
		});
	});

	$('.require-depends li:first input').on('change',function() {
		// Display asterisk on dependent required fields
		if( $(this).val() != '' ) {
			$(this).closest('.pewc-group').addClass('show-required');
		} else {
			$(this).closest('.pewc-group').removeClass('show-required');
		}
	});
	$('.pewc-file-upload').on('change',function(){
		readURL( this, $(this).attr('id') );
	});
	$('.pewc-remove-image').on('click',function(e){
		e.preventDefault();
		id = $(this).attr('data-id');
		$('#'+id).val("");
		$('#'+id+'-placeholder').css('display','none');
		$('#'+id+'-placeholder img').attr('src', '#');
		$('#'+id+'-wrapper').removeClass('image-loaded');
	});
	function readURL(input,id) {
		if( input.files && input.files[0] ) {
			var i = input.files.length;
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#'+id+'-wrapper').addClass('image-loaded');
				$('#'+id+'-placeholder').fadeIn();
				$('#'+id+'-placeholder img').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	$('body').on('change input','.pewc-has-maxchars input, .pewc-has-maxchars textarea',function(){
		var maxchars = parseInt( $(this).attr('data-maxchars') );
		var str = $(this).val();
		var str_ex_spaces = $(this).val();
		// Don't cost spaces
		// str_ex_spaces = str_ex_spaces.replace(/\s/g, "");
		if( pewc_vars.remove_spaces != 'yes' ) {
			str_ex_spaces = str.replace(/\s/g, "");
		} else {
			str_ex_spaces = str;
			var num_spaces = str.split( " " ).length - 1;
			maxchars += parseInt( num_spaces );
		}
		var str_len = str_ex_spaces.length;
		if(str_len>maxchars){
			var new_str = str.substring(0, maxchars);
			$(this).val(new_str);
		}
	});
	$('body').on( 'change input', '.pewc-form-field', function() {
		$form = $(this).closest( 'form' );
		add_on_images.update_add_on_image( $( this ), $form );
		pewc_update_total_js();
	});
	$('body').on('click','.pewc-remove-image',function(){
		$form = $(this).closest('form');
		// pewc_update_total( $form );
		pewc_update_total_js();
	});
	// Bind to the show_variation event
	// Update pewc-product-price field with variation price
	$( document ).bind( 'hide_variation', function( event, variation, purchasable ) {
		$('#pewc-product-price').val( 0 );
		pewc_update_total_js();
	});
	$( document ).bind( 'show_variation', function( event, variation, purchasable ) {
		var var_price = variation.display_price;
		$( '#pewc_variation_price' ).val( var_price );
		$( '#pewc-product-price' ).val( var_price );
		// Update percentage prices
		$( 'body' ).trigger( 'pewc_do_percentages' );
		// Find any select options with percentage prices - these might not have a field price
		$( this ).find( '.pewc-percentage.pewc-item-select select' ).each( function() {
			pewc_update_select_percentage_field( $( this ), var_price );
		});
		$( this ).find( '.pewc-percentage.pewc-item-select-box' ).each( function() {
			pewc_update_select_box_percentage_field( $( this ), var_price );
		});
		$( this ).find( '.pewc-percentage.pewc-item-image_swatch, .pewc-percentage.pewc-item-radio' ).each( function() {
			pewc_update_radio_percentage_field( $( this ), var_price );
		});
		// Check for variation dependent fields
		var variation_id = variation.variation_id;
		$('.pewc-variation-dependent').each(function(){
			var ids = $(this).attr('data-variations');
			ids = ids.split( ',' );
			ids = ids.map( function( x ) {
				return parseInt( x, 10 );
			});
			if( ids.indexOf( variation_id ) != -1 ) {
				$(this).addClass( 'active' );
			} else {
				$(this).removeClass( 'active' );
			}
		});
		// Update product dimensions
		$( '#pewc_product_length' ).val( variation.dimensions['length'] );
		$( '#pewc_product_width' ).val( variation.dimensions['width'] );
		$( '#pewc_product_height' ).val( variation.dimensions['height'] );
		$( '#pewc_product_weight' ).val( variation.weight );
		// Trigger recalculation
		$( 'body' ).trigger( 'pewc_trigger_calculations' );
		$( 'body' ).trigger( 'pewc_variations_updated' );
		pewc_update_total_js();
	});

	$( 'body' ).on( 'pewc_do_percentages', function() {
		$('.pewc-percentage').each(function() {
			var var_price = $( '#pewc-product-price' ).val();
			var new_price = ( var_price / 100 ) * parseFloat( $( this ).attr( 'data-percentage' ) );
			if( isNaN( new_price ) ) {
				new_price = 0;
			}
			$(this).attr( 'data-price', new_price );
			new_price = pewc_wc_price( new_price.toFixed(pewc_vars.decimals) );
			$(this).find('.pewc-field-price').html(new_price);
			// Find any options in this field - checkboxes
			$(this).find( '.pewc-option-has-percentage' ).each(function() {
				var option_price = ( var_price / 100 ) * $(this).attr('data-option-percentage');
				$(this).attr('data-option-cost',option_price.toFixed(pewc_vars.decimals));
				option_price = pewc_wc_price( option_price.toFixed(pewc_vars.decimals) );
				$(this).closest('.pewc-checkbox-form-label').find('.pewc-option-cost-label').html(option_price);
			});
		});
	});
	$( 'body.pewc-variable-product' ).on( 'update change', '.pewc-percentage.pewc-item-select select', function( e ) {
		// Only do this on variable products
		pewc_update_select_percentage_field( $( this ), $( '#pewc_variation_price' ).val() );
	});
	// @param select is the select element itself
	function pewc_update_select_percentage_field( select, var_price ) {
		pewc_update_select_option_percentage( select, var_price );
		// Trigger recalculation
		$( 'body' ).trigger( 'pewc_trigger_calculations' );
		pewc_update_total_js();
	}
	// @param selectbox is the wrapper element
	// @param var_price the variation price
	function pewc_update_select_box_percentage_field( selectbox, var_price ) {
		var select = $( selectbox ).find( 'select' );
		// Update the hidden select field
		pewc_update_select_option_percentage( select, var_price );
		// Now update the select box options
		var box = $( selectbox ).find( '.dd-select' );
		var box_options = $( selectbox ).find( '.dd-options' );
		$( select ).find( 'option' ).each( function( i, v ) {
			var new_price = ( var_price / 100 ) * $( this ).attr( 'data-option-percentage' );
			var new_text = $( this ).val() + pewc_vars.separator + pewc_wc_price( new_price.toFixed( pewc_vars.decimals ), true );
			var option = $( box_options ).find( 'li' ).eq( i );
			$( option ).find( '.dd-option-description' ).text( new_text );
		});
		// Update the selected option price
		var selected_price = $( select ).children( 'option:selected' ).attr( 'data-option-cost' );
		selected_price = pewc_wc_price( selected_price, true );
		$( selectbox ).find( '.dd-selected .dd-selected-description' ).text( selected_price );

		// Trigger recalculation
		$( 'body' ).trigger( 'pewc_trigger_calculations' );
		pewc_update_total_js();
	}
	// Shared function for select and select box fields
	function pewc_update_select_option_percentage( select, var_price ) {
		var selected = $( select ).children( 'option:selected' );
		var option_price = ( var_price / 100 ) * $( selected ).attr( 'data-option-percentage' );
		var item = $( select ).closest( '.pewc-item' ).attr( 'data-selected-option-price', option_price );
		$( selected ).attr( 'data-option-cost', option_price.toFixed( pewc_vars.decimals ) );
		option_price = pewc_wc_price( option_price.toFixed( pewc_vars.decimals ) );
		var data_price = $( select ).closest( '.pewc-item' ).attr( 'data-price' );
		if( isNaN( data_price ) ) {
			$( select ).closest( '.pewc-item' ).attr( 'data-price', 0 );
		}
		// Update all options prices
		$( select ).find( 'option' ).each( function( i, v ) {
			var new_price = ( var_price / 100 ) * $( this ).attr( 'data-option-percentage' );
			$( this ).attr( 'data-option-cost', new_price.toFixed( pewc_vars.decimals ) );
			var new_text = $( this ).val() + pewc_vars.separator + pewc_wc_price( new_price.toFixed( pewc_vars.decimals ), true );
			$( this ).text( new_text );
		});
	}
	// @param swatch is the swatch or radio group field wrapper
	function pewc_update_radio_percentage_field( field, var_price ) {
		// Iterate through each input element and update all options prices
		$( field ).find( 'input' ).each( function( i, v ) {
			var new_price = ( var_price / 100 ) * $( this ).attr( 'data-option-percentage' );
			$( this ).attr( 'data-option-cost', new_price.toFixed( pewc_vars.decimals ) );
			var new_text = $( this ).val() + pewc_vars.separator + pewc_wc_price( new_price.toFixed( pewc_vars.decimals ), true );
			$( this ).closest( 'label' ).next().text( new_text );
			if( $( field ).hasClass( 'pewc-item-radio' ) ) {
				$( this ).closest( 'li' ).find( 'label span' ).text( new_text );
			}
		});
		// Trigger recalculation
		$( 'body' ).trigger( 'pewc_trigger_calculations' );
		pewc_update_total_js();
	}

	function pewc_update_total_js( $update=0 ) {

		var flat_rate_total = 0;
		var product_price = parseFloat( $('#pewc-product-price').val() );

		total_price = 0; // Options running total
		var added_price = 0;
		var field_value = [];
		var field_label = [];

		$('form.cart .pewc-form-field').each(function() {

			added_price = 0;

			var field_wrapper = $(this).closest('.pewc-group');
			$( field_wrapper ).removeClass( 'pewc-active-field' );

			// Ignore hidden variation dependent fields
			if( ! $( field_wrapper ).hasClass( 'pewc-variation-dependent' ) || ( $( field_wrapper ).hasClass( 'pewc-variation-dependent' ) && $( field_wrapper ).hasClass( 'active' ) ) ) {

				// Check that the extra field is not flat rate
				if( ! $(field_wrapper).hasClass('pewc-flatrate') ) {

					if( $(field_wrapper).hasClass('pewc-group-checkbox') && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {
						if( $(field_wrapper).hasClass('pewc-per-unit-pricing') && $(this).prop('checked') ) {
							// Bookings for WooCommerce
							// Multiply option cost by number of booked units
							// total_price += parseFloat( $('#num_units_int').val() ) * parseFloat( $(field_wrapper).attr('data-price') );
							added_price = parseFloat( $('#num_units_int').val() ) * parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						} else if( $(this).prop('checked') ) {
							// total_price += parseFloat( $(field_wrapper).attr('data-price') );
							added_price = parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						}

					} else if( $(field_wrapper).hasClass('pewc-group-select' ) && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {

						// Add cost of selected option
						added_price = parseFloat( $(this).find(':selected').attr('data-option-cost') );
						// Add cost of select field
						added_price += parseFloat( $(field_wrapper).attr('data-price') );
						$( field_wrapper ).addClass( 'pewc-active-field' );
						$( field_wrapper ).attr( 'data-field-value', $(this).find(':selected').val() );

					}	else if( $(field_wrapper).hasClass('pewc-group-select-box' ) && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {

						// Add cost of selected option
						added_price = parseFloat( $( field_wrapper ).attr( 'data-selected-option-price') );
						// Add cost of select field
						added_price += parseFloat( $( field_wrapper ).attr( 'data-price' ) );
						$( field_wrapper ).addClass( 'pewc-active-field' );
						$( field_wrapper ).attr( 'data-field-value', $(this).find('.pewc-select-box-hidden' ).val() );

  				} else if( $(this).val() && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {

						if( $(field_wrapper).hasClass('pewc-per-character-pricing') && ( $(field_wrapper).hasClass('pewc-item-text') || $(field_wrapper).hasClass('pewc-item-textarea') || $(field_wrapper).hasClass( 'pewc-item-advanced-preview' ) ) ) {
							var str_len = pewc_get_text_str_len( $(this).val(), field_wrapper );
							added_price = str_len * parseFloat( $(field_wrapper).attr('data-price') );
						} else if( $(field_wrapper).hasClass('pewc-multiply-pricing') ) {
							var num_value = $(this).val();
							added_price = num_value * parseFloat( $(field_wrapper).attr('data-price') );
						} else if( $(field_wrapper).hasClass('pewc-group-name_price') ) {
							added_price = parseFloat( $(this).val() );
						} else if( $(field_wrapper).hasClass('pewc-item-number' ) && $(field_wrapper).hasClass('pewc-per-unit-pricing') ) {
								// Bookings for WooCommerce
								// Multiply option cost by number of booked units
								// total_price += parseFloat( $('#num_units_int').val() ) * parseFloat( $(field_wrapper).attr('data-price') );
								added_price = parseFloat( $('#num_units_int').val() ) * parseFloat( $(field_wrapper).attr('data-price') ) * parseFloat( $( this ).val() );
						} else {
							added_price = parseFloat( $(field_wrapper).attr('data-price') );
						}

						if( $(this).val() ) {
							$( field_wrapper ).addClass( 'pewc-active-field' );
							$( field_wrapper ).attr( 'data-field-value', $(this).val() );
						}

					}

					total_price += added_price;
					if( ! isNaN( added_price ) ) {
						$( field_wrapper ).attr( 'data-field-price', added_price );
					}

				} else {

					// Flat rate item
					if( $(field_wrapper).hasClass('pewc-group-checkbox') && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {
						if( $(field_wrapper).hasClass('pewc-per-unit-pricing') && $(this).prop('checked') ) {
							// Bookings for WooCommerce
							// Multiply option cost by number of booked units
							added_price = parseFloat( $('#num_units_int').val() ) * parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						} else if( $(this).prop('checked') ) {
							added_price = parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						}
					} else if( ( $(field_wrapper).hasClass('pewc-group-select') || $(field_wrapper).hasClass('pewc-group-select-box') ) && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {
						// Add cost of selected option
						added_price = parseFloat( $(this).find(':selected').attr('data-option-cost') );
						// Add cost of select field
						added_price += parseFloat( $(field_wrapper).attr('data-price') );
						$( field_wrapper ).addClass( 'pewc-active-field' );
					} else if( $(this).val() && ! $(field_wrapper).hasClass('pewc-hidden-field') ) {
						if( $(field_wrapper).hasClass('pewc-per-character-pricing') ) {
							var str_len = pewc_get_text_str_len( $(this).val(), field_wrapper );
							added_price = str_len * parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						} else if( $(field_wrapper).hasClass('pewc-multiply-pricing') ) {
							var num_value = $(this).val();
							added_price = num_value * parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						} else if( $(field_wrapper).hasClass('pewc-group-name_price') ) {
							added_price = parseFloat( $(this).val() );
						} else {
							added_price = parseFloat( $(field_wrapper).attr('data-price') );
							$( field_wrapper ).addClass( 'pewc-active-field' );
						}
					}

					flat_rate_total += added_price;
					$( field_wrapper ).attr( 'data-field-price', added_price );

				}

			}

			if( $( field_wrapper ).val() ) {
				set_summary_panel_data( $( field_wrapper ), $( field_wrapper ).val(), added_price );
			}

		});

		$( 'form.cart .pewc-item-radio, form.cart .pewc-item-image_swatch' ).each(function() {

			var field_value = [];

			if( ! $( this ).hasClass( 'pewc-variation-dependent' ) || ( $( this ).hasClass( 'pewc-variation-dependent' ) && $( this ).hasClass( 'active' ) ) ) {

				var radio_group_id = $(this).attr( 'data-id' );
				if( ! $(this).hasClass('pewc-hidden-field') ) {
					if( ! $(this).hasClass('pewc-flatrate') ) {

						if( $('.'+radio_group_id ).find( $('input[type=radio]:checked')).attr('data-option-cost') ) {
							added_price = parseFloat( $(this).attr('data-price') );
							var selected_option_price = $('.'+radio_group_id).find( $('input[type=radio]:checked') ).attr('data-option-cost');
							$( this ).attr( 'data-selected-option-price', selected_option_price );
							added_price += parseFloat( selected_option_price );
							total_price += added_price;
						}

					} else {
						if( $('.'+radio_group_id).find( $('input[type=radio]:checked')).attr('data-option-cost') ) {
							flat_rate_total += parseFloat( $(this).attr('data-price') );
							var selected_option_price = $('.'+radio_group_id).find( $('input[type=radio]:checked') ).attr('data-option-cost');
							$( this ).attr( 'data-selected-option-price', selected_option_price );
							flat_rate_total += parseFloat( selected_option_price );
						}
					}

					if( $(this).find( $('input[type=radio]:checked')).val() ) {
						set_summary_panel_data( $( this ), $(this).find( $('input[type=radio]:checked')).val(), added_price );
					} else {
						$( this ).removeClass( 'pewc-active-field' );
					}

				}

			}

		});

		$('form.cart .pewc-item-select').each(function() {
			var selected_option_price = $( this ).find( 'option:selected' ).attr( 'data-option-cost' );
			$( this ).attr( 'data-selected-option-price', selected_option_price );
		});

		$('form.cart .pewc-item-checkbox_group').each(function() {

			var field_value = [];

			if( ! $( this ).hasClass( 'pewc-variation-dependent' ) || ( $( this ).hasClass( 'pewc-variation-dependent' ) && $( this ).hasClass( 'active' ) ) ) {

				var checkbox_group_id = $(this).attr( 'data-id' );
				var checkbox_group_price = 0;
				if( ! $(this).hasClass('pewc-hidden-field') ) {
					var checkbox_value = [];
					if( ! $(this).hasClass('pewc-flatrate') ) {
						// Get the field price
						if( $("input[name='" + checkbox_group_id + "[]']:checked" ).val() ) {
							checkbox_group_price += parseFloat( $(this).attr('data-price') );
						}
						$('.'+checkbox_group_id).find( $('input[type=checkbox]:checked') ).each( function() {
							checkbox_group_price += parseFloat( $(this).attr('data-option-cost') );
							checkbox_value.push( $( this ).val() );
						});
						total_price += checkbox_group_price;

						// Summary panel
						if( $("input[name='" + checkbox_group_id + "[]']:checked" ).val() ) {
							$( this ).attr( 'data-field-price', checkbox_group_price );
							$( this ).addClass( 'pewc-active-field' );
							$( this ).attr( 'data-field-value', checkbox_value.join( ', ' ) );
						}

					} else {

						// Flat rate
						if( $("input[name='" + checkbox_group_id + "[]']:checked" ).val() ) {
							flat_rate_total += parseFloat( $(this).attr('data-price') );
						}
						$('.'+checkbox_group_id).find( $('input[type=checkbox]:checked') ).each( function() {
							flat_rate_total += parseFloat( $(this).attr('data-option-cost') );
						});

					}
				}

			}

		});
		$('form.cart .pewc-item-image-swatch-checkbox').each(function() {

			var field_value = [];

			if( ! $( this ).hasClass( 'pewc-variation-dependent' ) || ( $( this ).hasClass( 'pewc-variation-dependent' ) && $( this ).hasClass( 'active' ) ) ) {

				var checkbox_group_id = $(this).attr( 'data-id' );
				var checkbox_group_price = 0;
				if( ! $(this).hasClass('pewc-hidden-field') ) {
					var field_value = [];
					if( ! $(this).hasClass('pewc-flatrate') ) {
						// Get the field price
						if( $("input[name='" + checkbox_group_id + "[]']:checked" ).val() ) {
							checkbox_group_price += parseFloat( $(this).attr('data-price') );
						}
						$('.'+checkbox_group_id).find( $('input[type=checkbox]:checked') ).each( function() {
							field_value.push( $( this ).val() );
							checkbox_group_price += parseFloat( $(this).attr('data-option-cost') );
						});
						total_price += checkbox_group_price;
					} else {
						// Flat rate
						if( $("input[name='" + checkbox_group_id + "[]']:checked" ).val() ) {
							flat_rate_total += parseFloat( $(this).attr('data-price') );
						}
						$('.'+checkbox_group_id).find( $('input[type=checkbox]:checked') ).each( function(){
							flat_rate_total += parseFloat( $(this).attr('data-option-cost') );
						});
					}
				}

				if( field_value.length > 0 ) {
					set_summary_panel_data( $( this ), field_value.join( ', ' ), checkbox_group_price );
				} else {
					$( this ).removeClass( 'pewc-active-field' );
				}

			}

		});

		// Check child products with select layout
		var child_products_total = 0;
		$('form.cart .pewc-child-select-field').each(function() {

			var field_wrapper = $( this ).closest( '.pewc-item' );
			var field_value = [];
			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				// If the select field has a value
				if( $(this).val() && $(this).val() != '' ) {
					field_value.push( $(this).find(':selected').attr( 'data-field-value' ) );
					var child_product_price = $(this).find(':selected').data( 'option-cost' );
					var qty = 0;
					if( child_product_price > 0 ) {
						var wrapper = $(this).closest('.child-product-wrapper');
						var quantities = $(wrapper).data('products-quantities');
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							// Find the child_quantity field
							qty = $(wrapper).find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}

					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );
					if( field_value.length > 0 ) {
						$( this ).closest( '.pewc-item' ).attr( 'data-field-price', child_products_total );
						set_summary_panel_data( $( this ).closest( '.pewc-item' ), field_value.join( ', ' ), parseFloat( child_product_price ) * parseFloat( qty ) );
					} else {
						$( this ).closest( '.pewc-item' ).removeClass( 'pewc-active-field' );
					}

				}
			}
		});
		$('form.cart .pewc-radio-images-wrapper.child-product-wrapper').each(function() {
			var field_value = [];
			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				var quantities = $(this).data('products-quantities');
				var radio_val = $(this).find('.pewc-radio-form-field:checked').val();
				if( radio_val && radio_val != undefined ) {
					var child_product_price = $(this).find('.pewc-radio-form-field:checked').data('option-cost');
					var qty = 0;
					if( child_product_price > 0 ) {
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							// Find the child_quantity field
							qty = $(this).closest('.pewc-item-field-wrapper').find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}
					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );

					if( child_products_total > 0 ) {
						$( this ).closest( '.pewc-item' ).attr( 'data-field-price', child_products_total );
						set_summary_panel_data( $( this ).closest( '.pewc-item' ), $(this).find('.pewc-radio-form-field:checked').attr( 'data-field-label' ), parseFloat( child_product_price ) * parseFloat( qty ) );
					}

				} else {
					$( this ).closest( '.pewc-item' ).removeClass( 'pewc-active-field' );
				}

			}
		});

		$('form.cart .pewc-checkboxes-images-wrapper.child-product-wrapper').each(function() {
			var this_child_total = 0;
			var field_value = [];
			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				var quantities = $(this).data('products-quantities');
				// Run through each selected checkbox

				$( this ).closest( '.pewc-item' ).removeClass( 'pewc-active-field' );

				$( this ).find('.pewc-checkbox-form-field:checkbox:checked').each(function() {
					field_value.push( $( this ).attr( 'data-field-label' ) );
					var child_product_price = $(this).data('option-cost');
					var qty = 0;
					if( child_product_price > 0 ) {
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							qty = $(this).closest('.pewc-checkbox-image-wrapper').find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}

					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );
					this_child_total += parseFloat( child_product_price ) * parseFloat( qty );

				});

				if( field_value.length > 0 ) {
					$( this ).closest( '.pewc-item' ).attr( 'data-field-price', child_products_total );
					set_summary_panel_data( $( this ).closest( '.pewc-item' ), field_value.join( ', ' ), this_child_total );
				}

			}

		});
		$('form.cart .pewc-column-wrapper .pewc-variable-child-product-wrapper.checked').each(function() {
			var field_value = [];
			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				var quantities = $(this).closest( '.pewc-column-wrapper' ).data('products-quantities');
				// Run through each selected checkbox for variable child products

				$( this ).removeClass( 'pewc-active-field' );

				$(this).find('.pewc-variable-child-select').each(function() {
					field_value.push( $( this ).attr( 'data-field-label' ) );
					var child_product_price = $(this).find(':selected').data('option-cost');
					var qty = 0;
					if( child_product_price > 0 ) {
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							qty = $(this).closest('.pewc-checkbox-image-wrapper').find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}

					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );

					if( field_value.length > 0 ) {
						$( this ).closest( '.pewc-item' ).attr( 'data-field-price', child_products_total );
						set_summary_panel_data( $( this ).closest( '.pewc-item' ), field_value.join( ', ' ), parseFloat( child_product_price ) * parseFloat( qty ) );
					}

				});

			}
		});
		$('form.cart .pewc-column-wrapper .pewc-simple-child-product-wrapper.checked').each(function() {
			var field_value = [];
			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				var quantities = $(this).closest( '.pewc-column-wrapper' ).data('products-quantities');

				$( this ).closest( '.pewc-item' ).removeClass( 'pewc-active-field' );

				// Run through each selected checkbox
				$(this).find('.pewc-checkbox-form-field').each(function(){
					field_value.push( $( this ).attr( 'data-field-label' ) );
					var child_product_price = $(this).data('option-cost');
					var qty = 0;
					if( child_product_price > 0 ) {
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							qty = $(this).closest('.pewc-simple-child-product-wrapper').find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}
					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );

					if( field_value.length > 0 ) {
						$( this ).closest( '.pewc-item' ).attr( 'data-field-price', child_products_total );
						set_summary_panel_data( $( this ).closest( '.pewc-item' ), field_value.join( ', ' ), parseFloat( child_product_price ) * parseFloat( qty ) );
					}

				});

			}
		});
		$('form.cart .pewc-swatches-wrapper .pewc-child-variation-main').each(function() {

			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {

				var quantities = $(this).closest( '.pewc-swatches-wrapper' ).data('products-quantities');

				// Run through each selected variation product
				$(this).find('.pewc-child-name input').each(function() {
					var child_product_price = parseFloat( $(this).data( 'option-cost' ) );
					var qty = 0;
					if( child_product_price > 0 ) {
						// Get the quantity
						if( quantities == 'linked' ) {
							qty = $('.quantity .qty').val();
						} else if( quantities == 'independent' ) {
							qty = $(this).closest('.pewc-child-variation-main').find('.pewc-child-quantity-field').val();
						} else if( quantities == 'one-only' ) {
							qty = 1;
						}
					}
					child_products_total += parseFloat( child_product_price ) * parseFloat( qty );
				});

			}

		});
		$('form.cart .grid-layout').each( function() {

			var is_hidden = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-hidden-field' );
			var is_dependent = ( $( this ).closest( '.pewc-item' ).hasClass( 'pewc-variation-dependent' ) ) && ( ! $( this ).closest( '.pewc-item' ).hasClass( 'active' ) );
			if( ! is_hidden && ! is_dependent ) {
				var child_product_price = parseFloat( $( this ).closest( '.pewc-item' ).attr( 'data-price' ) );
				child_products_total += parseFloat( child_product_price );
			}

		});

		if( product_price < 0 ) product_price = 0;

		// Summary panel rows
		$( '.pewc-summary-panel-field-row' ).addClass( 'pewc-summary-panel-field-row-inactive' );

		$( '.pewc-active-field' ).not( '.pewc-hidden-field' ).each( function() {
			var field_id = $( this ).attr( 'data-field-id' );
			var field_type = $( '.pewc-field-' + field_id ).attr( 'data-field-type' );
			$( '#pewc-summary-row-' + field_id ).removeClass( 'pewc-summary-panel-field-row-inactive' ).addClass( 'pewc-field-' + field_type );
			var field_price = parseFloat( $( this ).attr( 'data-field-price' ) );
			if( field_price ) {
				field_price = field_price.toFixed( pewc_vars.decimals );
			}

			var field_label = $( this ).attr( 'data-field-label' );
			var field_value;
			if( $( this ).attr( 'data-field-type') != 'checkbox' ) {
				field_value = $( this ).attr( 'data-field-value' );
			}
			$( '#pewc-summary-row-' + field_id ).find( '.pewc-summary-panel-product-name' ).html( field_label );
			$( '#pewc-summary-row-' + field_id ).find( '.pewc-summary-panel-product-value' ).html( field_value );
			$( '#pewc-summary-row-' + field_id ).find( '.pewc-summary-panel-price' ).html( pewc_wc_price( field_price ) );
		});

		// Summary panel subtotal
		var subtotal = parseFloat( child_products_total ) + parseFloat( total_price ) + parseFloat( product_price );
		if( ! isNaN( subtotal ) ) {
			$( '#pewc-summary-panel-subtotal' ).html( pewc_wc_price( subtotal.toFixed(pewc_vars.decimals) ) );
		}

		var qty = 1;
		if($('.qty').val()) {
			qty = $('.qty').val();
		}
		var total_grid_variations = $( '#pewc-grid-total-variations' ).val();

		var product_price = qty * product_price;
		var grand_total = product_price;
		base_price = product_price;
		product_price = product_price.toFixed(pewc_vars.decimals);
		product_price = pewc_wc_price( product_price );
		product_price = add_price_suffix( product_price, base_price );
		$('#pewc-per-product-total').html( product_price );

		total_price = qty * total_price;
		// Multiply add-ons by number of variations in bulk grid
		if( total_grid_variations ) {
			total_price = total_grid_variations * total_price;
		}
		total_price += child_products_total;
		grand_total += total_price;

		if( ! isNaN( total_price ) ) {
			var base_total_price = total_price;
			total_price = total_price.toFixed(pewc_vars.decimals);
			total_price = pewc_wc_price( total_price );
			total_price = add_price_suffix( total_price, base_total_price );
			$('#pewc-options-total').html( total_price );
		}

		if( flat_rate_total < 0 ) flat_rate_total = 0;
		base_flat_rate_total = flat_rate_total;
		grand_total += flat_rate_total;
		flat_rate_total = flat_rate_total.toFixed(pewc_vars.decimals);
		flat_rate_total = pewc_wc_price( flat_rate_total );
		flat_rate_total = add_price_suffix( flat_rate_total, base_flat_rate_total );
		$('#pewc-flat-rate-total').html( flat_rate_total );

		// Set the product price using a calculation field
		if( $( '#pewc_calc_set_price').attr( 'data-calc-set' ) == 1 ) {
			grand_total = parseFloat( $( '#pewc_calc_set_price').val() );
		}

		if( ! isNaN( grand_total ) ) {
			var base_price = grand_total;
			grand_total = grand_total.toFixed( pewc_vars.decimals );
			grand_total = pewc_wc_price( grand_total );
			if( pewc_vars.update_price == 'yes' ) {
				update_product_price( grand_total, base_price );
			}
			grand_total = add_price_suffix( grand_total, base_price );
			$('#pewc-grand-total').html( grand_total );
		}

		// Re-run this because some browsers are too quick
		// Instead of re-running this, introduce a pause at the start of the function to allow all fields to be update?
		if( $update == 0 ) {
			var interval = setTimeout( function() {
				pewc_update_total_js( 1 );
				if( ! total_updated ) {
					// Check any calculations before input fields are updated
					$( 'body' ).trigger( 'pewc_trigger_calculations' );
					total_updated = true;
				}
			},
			250 );
		}
	}
	function add_price_suffix( price, base_price ) {
		if( pewc_vars.show_suffix == 'yes' ) {
			var price_suffix_setting = pewc_vars.price_suffix_setting;
			if( price_suffix_setting.indexOf( '{price_excluding_tax}' ) > -1 ) {
				var price_ex_tax = base_price * ( pewc_vars.percent_exc_tax / 100 );
				suffix = price_suffix_setting.replace( '{price_excluding_tax}', pewc_wc_price( price_ex_tax.toFixed( pewc_vars.decimals ) ) );
			} else if( price_suffix_setting.indexOf( '{price_including_tax}' ) > -1 ) {
				var price_inc_tax = base_price * ( pewc_vars.percent_inc_tax / 100 );
				suffix = price_suffix_setting.replace( '{price_including_tax}', pewc_wc_price( price_inc_tax.toFixed( pewc_vars.decimals ) ) );
			} else {
				suffix = pewc_vars.price_suffix;
			}
			price = price + ' &nbsp;<small class="woocommerce-price-suffix">' + suffix + '</small>';
		}
		return price;
	}
	function update_product_price( grand_total, base_price ) {
		var price_suffix_setting = pewc_vars.price_suffix_setting;
		// We can rebuild him
    var suffix = $( '.pewc-main-price' ).find( '.woocommerce-price-suffix' ).html();
    var label = $( '.pewc-main-price' ).find( '.wcfad-rule-label' ).html();
		var before = $( '.pewc-main-price' ).find( '.pewc-label-before' ).html();
		var after = $( '.pewc-main-price' ).find( '.pewc-label-after' ).html();
		var hide = $( '.pewc-main-price' ).find( '.pewc-label-hidden' ).html();

		var new_total = '';

		if( hide ) {
			new_total = '<span class="pewc-label-hidden">' + hide + '</span>';
		} else {
			if( before ) {
				new_total += '<span class="pewc-label-before">' + before + ' </span>';
			}
			new_total += grand_total;
			if( after ) {
				new_total += '<span class="pewc-label-after"> ' + after + '</span>';
			}
	    if( suffix ) {
				if( price_suffix_setting.indexOf( '{price_excluding_tax}' ) > -1 ) {
					var price_ex_tax = base_price * ( pewc_vars.percent_exc_tax / 100 );
					suffix = price_suffix_setting.replace( '{price_excluding_tax}', pewc_wc_price( price_ex_tax.toFixed( pewc_vars.decimals ) ) );
				} else if( price_suffix_setting.indexOf( '{price_including_tax}' ) > -1 ) {
					var price_inc_tax = base_price * ( pewc_vars.percent_inc_tax / 100 );
					suffix = price_suffix_setting.replace( '{price_including_tax}', pewc_wc_price( price_inc_tax.toFixed( pewc_vars.decimals ) ) );
				}
				if( label && suffix.indexOf( 'wcfad-rule-label' ) < 0 ) {
		      suffix += '<br><span class="wcfad-rule-label">' + label + '</span>';
		    }
	      new_total += '&nbsp;<small class="woocommerce-price-suffix">' + suffix + '</small>';
	    }
		}

    $( '.pewc-main-price').not( '.pewc-quickview-product-wrapper .pewc-main-price' ).html( new_total );
	}

	function set_summary_panel_data( field, value, added_price ) {
		$( field ).attr( 'data-field-value', value );
		$( field ).attr( 'data-field-price', added_price );
		$( field ).addClass( 'pewc-active-field' );
	}

	function pewc_wc_price( price, price_only=false ) {

		$('#pewc_total_calc_price').val( price ); // Used in Bookings for WooCommerce

		var return_html, price_html, formatted_price;

		// Let's split any decimal places out
		var decimal_separator = pewc_vars.decimal_separator;
		var thousand_separator = pewc_vars.thousand_separator;

		// price is passed like this - 2500.45 - irrespective of separators
		var decimal_string = '';

		if( pewc_vars.decimals > 0 ) {
			var string = price.toString();
			decimal_string = string.split( '.' );
			if( decimal_string[1] == undefined ) {
				decimal_string = '';
			} else {
				decimal_string = decimal_separator + decimal_string[1];
			}
		}

		var floor = Math.floor( price );
		// Format the integer first
		floor = format_separator( floor );
		// Now replace the default separator
		floor = floor.replace( /,/g, thousand_separator );
		// Add the decimal back in with the correct separator
		price = floor + decimal_string;

		var currency_symbol = '<span class="woocommerce-Price-currencySymbol">' + pewc_vars.currency_symbol + '</span>';

		if( pewc_vars.currency_pos == 'left' ) {
			formatted_price = currency_symbol + '&#x200e;' + price;
		} else if( pewc_vars.currency_pos == 'right' ) {
			formatted_price = price + currency_symbol + '&#x200f;';
		} else if( pewc_vars.currency_pos == 'left_space' ) {
			formatted_price = currency_symbol + '&#x200e;&nbsp;' + price;
		} else if( pewc_vars.currency_pos == 'right_space' ) {
			formatted_price = price + '&nbsp;' + currency_symbol + '&#x200f;';
		}

		// formatted_price = formatted_price.replace( '.', pewc_vars.decimal_separator );
		price_html = formatted_price;
		return_html = '<span class="woocommerce-Price-amount amount"><bdi>' + price_html + '</bdi></span>';

		if( price_only ) {
			return $( return_html ).text();
		}

		return return_html;
	}

	function format_separator( num ) {
	  return num.toString().replace( /(\d)(?=(\d{3})+(?!\d))/g, '$1,' );
	}
	// var interval = setInterval(function(){
	// 	pewc_update_total_js();
	// },
	// 500);
	$( 'form.cart' ).on('keyup input change paste', 'input:not(.pewc-grid-quantity-field), select, textarea.pewc-has-field-price', function(){
    pewc_update_total_js();
		$( 'body' ).trigger( 'pewc_updated_total_js' );
	});
	var interval = setTimeout( function() {
		pewc_update_total_js();
	},
	250 );
	$( 'body' ).on( 'pewc_add_button_clicked', function() {
		pewc_update_total_js();
	});
	$( 'body' ).on( 'pewc_force_update_total_js', function() {
		pewc_update_total_js();
	});
	// Accordion and tabs
	$('.pewc-groups-accordion h3, .pewc-group-heading-wrapper h3').on('click',function(e){
		if( pewc_vars.close_accordion == 'yes' ) {
			$( '.pewc-group-wrap' ).removeClass( 'group-active' );
		}
		$(this).closest('.pewc-group-wrap').toggleClass('group-active');
	});
	if( pewc_vars.accordion_toggle == 'open' ) {
		$('.pewc-group-wrap').addClass('group-active');
	} else if( pewc_vars.accordion_toggle == 'closed' ) {
		$('.pewc-group-wrap').removeClass('group-active');
	} else {
		$('.first-group').addClass('group-active');
	}

	$('.pewc-tab').on('click',function(e) {
		e.preventDefault();
		var tab_id = $(this).attr('data-group-id');
		$('.pewc-tab').removeClass('active-tab');
		$(this).addClass('active-tab');
		$('.pewc-group-wrap').removeClass('group-active');
		$('.pewc-group-wrap-'+tab_id).addClass('group-active');
	});
	$('.pewc-next-step-button').on('click',function(e) {
		e.preventDefault();
		var tab_id = $(this).attr( 'data-group-id' );
		$( '.pewc-tab' ).removeClass( 'active-tab' );
		$( '#pewc-tab-' + tab_id ).addClass( 'active-tab' );
		$( '.pewc-group-wrap' ).removeClass('group-active');
		$('.pewc-group-wrap-'+tab_id).addClass('group-active');
	});

	function pewc_get_text_str_len( str, wrapper ) {
		var new_str, str_ex_spaces ;
		var field = $(wrapper).find('.pewc-form-field');
		// Don't include spaces
		if( pewc_vars.remove_spaces != 'yes' ) {
			str_ex_spaces = str.replace(/\s/g, "");
		} else {
			str_ex_spaces = str;
		}

		var str_len = str_ex_spaces.length;
		// Exclude alphanumerics if selected
		if( $(field).attr('data-alphanumeric') == 1 ) {
			str_ex_spaces = str_ex_spaces.replace(/\W/g, '');
			$(field).val(str_ex_spaces);
			str_len = str_ex_spaces.length;
		}
		// Allow alphanumerics but don't charge if selected
		if( $(field).attr('data-alphanumeric-charge') == 1 ) {
			str_ex_spaces = str_ex_spaces.replace(/\W/g, '');
			// $(field).val(str_ex_spaces);
			str_len = str_ex_spaces.length;
		}
		// If free characters are allowed
		var freechars = $(field).attr('data-freechars');
		str_len -= freechars;
		str_len = Math.max(0,str_len);
		return str_len;
	}
	$('.woocommerce-cart-form__cart-item.pewc-child-product').each(function() {
		if( pewc_vars.disable_qty || pewc_vars.multiply_independent == 'yes' ) {
			$(this).find('.product-quantity input').attr('disabled',true);
		}
	});
	// If child product is selected, manage allowable purchase quantity
	// Applies to radio and select
	$('body').on('change','.products-quantities-independent .pewc-child-select-field',function(){
		var number_field = $(this).closest('.child-product-wrapper').find('.pewc-child-quantity-field');
		if( $(number_field).val() == 0 ) {
			// Automatically enter a quantity when a product is selected
			$(number_field).val(1);
		};
		var available_stock = $(this).find(':selected').data('stock');
		if( available_stock ) {
			var number = $(number_field).attr('max',available_stock);
			if( $(number).val() > available_stock ) {
				$(number_field).val(available_stock);
			}
		} else {
			$(number_field).removeAttr('max');
		}
	});
	$('body').on('change input keyup paste','.products-quantities-independent .pewc-child-quantity-field',function(){
		// Ensure this child product is selected
		if( $(this).val() > 0 ) {
			var checkbox = $(this).closest('.pewc-checkbox-image-wrapper').find('input[type=checkbox]').prop('checked',true);
			// $(this).closest('.pewc-checkbox-image-wrapper').addClass('checked');
		} else {
			var checkbox = $(this).closest('.pewc-checkbox-image-wrapper').find('input[type=checkbox]').prop('checked',false);
			// $(this).closest('.pewc-checkbox-image-wrapper').removeClass('checked');
		}
		var available_stock = $(this).find(':selected').data('stock');
		$( 'body' ).trigger( 'pewc_update_child_quantity', [ $(this).closest('.pewc-checkbox-image-wrapper').find('input[type=checkbox]') ] );
		pewc_update_total_js();
	});
	$( 'body' ).on( 'click', '.products-quantities-independent .pewc-checkbox-form-field', function() {
		if( $(this).is(':checked') ){
			var number = $(this).closest('.pewc-checkbox-image-wrapper').find('input[type=number]').val();
			if(number==0) {
				$(this).closest('.pewc-checkbox-image-wrapper').find('input[type=number]').val(1);
			}
		} else {
			var number = $(this).closest('.pewc-checkbox-image-wrapper').find('input[type=number]').val(0);
		}
	});

	$( 'body' ).on( 'click', '.pewc-radio-image-wrapper', function( e ) {
		var wrapper = $( this );
		var radio;
		// Remove all checked for radio button
		if( ! $( wrapper ).closest( '.pewc-item' ).hasClass( 'pewc-item-image-swatch-checkbox' ) ) {
			var checked = $( wrapper ).find( '.pewc-radio-form-field' ).prop( 'checked' );
			var group = $( wrapper ).closest( '.pewc-radio-images-wrapper' ).find( '.pewc-radio-image-wrapper' ).removeClass( 'checked' );
			if( ! checked ) {
				radio = $( wrapper ).find( '.pewc-radio-form-field' ).trigger( 'click' );
				$( wrapper ).addClass( 'checked' );
			}
		} else {
			// Checkbox
			radio = $( wrapper ).find( '.pewc-radio-form-field' );
			var checked = $( radio ).prop( 'checked' );
			$( radio ).prop( 'checked', ! checked );
		}
		$form = $( this ).closest( 'form' );
		add_on_images.update_add_on_image( $( this ), $form );
	}).on( 'click', '.pewc-radio-image-wrapper .pewc-radio-form-field', function( e ) {
		var has_class = $( this ).closest( '.pewc-item' ).hasClass( 'pewc-item-image-swatch-checkbox' );
		if( ! has_class ) {
			// Stop propagation for radio buttons
			e.stopPropagation();
			// Deselect the radio button
			var checked = $( this ).closest( '.pewc-radio-image-wrapper' ).hasClass( 'checked' );
			if( ! checked ) {
				$( this ).prop( 'checked', false );
				pewc_update_total_js();
			}
		} else {
			$( this ).closest( '.pewc-radio-image-wrapper' ).toggleClass( 'checked' );
		}

	});
	$( 'body' ).on( 'click' , '.products-quantities-independent .pewc-radio-form-field', function() {
		if($(this).is(':checked')) {
			var number_field = $(this).closest('.pewc-item-field-wrapper').find('input[type=number]');
			var number = $(number_field).val();
			if( number == 0 ) {
				$( number_field ).val( 1 );
			}
			if( $(this).attr('data-stock') > 0 ) {
				// Ensure the quantity field doesn't display more than the available stock
				$(number_field).attr( 'max', $(this).attr('data-stock') );
				if( $(number_field).val() > $(this).attr('data-stock') ) {
					$(number_field).val( $(this).attr('data-stock') );
				}
			}
		} else {
			var number = $(this).closest('.pewc-radio-images-wrapper').find('input[type=number]').val(0);
		}
	});

	var calculations = {

		init: function() {

			if( pewc_vars.calculations_timer > 0 ) {
				var interval = setInterval(
					this.recalculate,
					pewc_vars.calculations_timer
				);
			} else {
				// $( 'body' ).one( 'keyup input change paste', '.pewc-number-field.pewc-calculation-trigger, .pewc-field-triggers-condition .pewc-number-field', this.recalculate );
				$( 'body' ).on( 'keyup input change paste', '.qty', this.recalculate );
				$( 'body' ).on( 'keyup input change paste', 'form.cart .pewc-item.pewc-calculation-trigger, .pewc-form-field.pewc-calculation-trigger, .pewc-field-triggers-condition .pewc-form-field, .pewc-field-triggers-condition .pewc-radio-form-field', this.recalculate );
				$( 'body' ).on( 'keyup input change paste', '.pewc-number-uploads', this.recalculate );
				$( 'body' ).on( 'pewc_trigger_calculations', this.recalculate );
				$( 'body' ).on( 'pewc_conditions_checked', this.recalculate );
			}

		},

		recalculate: function( e ) {

			// If we don't have any calculation fields, just bounce
			var num_calcs = $( '.pewc-product-extra-groups-wrap .pewc-item-calculation' ).not( '.pewc-hidden-field' ).length;

			if( parseFloat( num_calcs ) < 1 ) {
				return;
			}

			var calc_field, price_wrapper, dimensions_wrapper, formula, tags, calc_formula, replace, calc_field_id;

			var calc_fields = [];

			var update = 0;

			// Find any calculation fields
			$( 'body' ).find( 'form.cart .pewc-item-calculation' ).not( '.pewc-hidden-field' ).each( function() {

				if( $( this ).hasClass( 'pewc-variation-dependent' ) && ! $( this ).hasClass( 'active' ) ) {
					return;
				}

				var group = $( this ).closest( '.pewc-group-wrap' );
				if( $( group ).hasClass( 'pewc-group-hidden' ) ) {
					return;
				}

				calc_field = $( this );
				calc_field_id = $( calc_field ).attr( 'data-field-id' );
				price_wrapper = $( calc_field ).find( '.pewc-calculation-price-wrapper' );
				formula = $( price_wrapper ).find( '.pewc-data-formula' ).val();
				fields = $( price_wrapper ).find( '.pewc-data-fields' ).val();
				tags = $( price_wrapper ).find( '.pewc-data-tag' ).val();
				action = $( price_wrapper ).find( '.pewc-action' ).val();
				round = $( price_wrapper ).find( '.pewc-formula-round' ).val();
				decimals = $( price_wrapper ).find( '.pewc-decimal-places' ).val();

				if( fields ) {
					fields = JSON.parse( fields );
				}

				var result = calculations.evaluate_formula( fields, formula, round, decimals, calc_field_id );
				if( result == '*' ) {
					$( price_wrapper ).find( 'span' ).html( pewc_vars.null_signifier );
					$( price_wrapper ).find( '.pewc-calculation-value' ).val( 0 ).trigger( 'calculation_field_updated' );
					if( pewc_vars.disable_button_calcs == 'yes' ) {
						calc_fields.push( calc_field_id );
						$( 'body' ).find( 'form.cart .single_add_to_cart_button' ).attr( 'disabled', true );
					}
				} else if( ! result || ! isNaN( result ) ) {
					if( pewc_vars.disable_button_calcs == 'yes' ) {
						calc_fields = calc_fields.filter( function( item ) {
							return item !== calc_field_id;
						});
						if( calc_fields.length < 1 ) {
							$( 'body' ).find( 'form.cart .single_add_to_cart_button' ).attr( 'disabled', false );
						}
					}
					$( price_wrapper ).find( 'span' ).html( result );
					if( action == 'cost' || action == 'price' ) {
						$( calc_field ).closest( '.pewc-item-calculation' ).attr( 'data-price', result );
						$( price_wrapper ).find( 'span' ).html( pewc_wc_price( result ) );
					} else if( action == 'qty' ) {
						$( 'form.cart' ).find( '.quantity .qty' ).val( result ).trigger( 'pewc_qty_changed' );
					}
					if( action == 'price' ) {
						$( '#pewc_calc_set_price' ).val( result ).attr( 'data-calc-set', 1 );
					}
					// $( price_wrapper ).find( '.pewc-calculation-value' ).val( result ).trigger( 'calculation_field_updated' );
					$( price_wrapper ).find( '.pewc-calculation-value' ).val( result ).trigger( 'calculation_field_updated' );
				}

				update++;

			});

			// Update the totals
			if( update > 0 ) {
				update = 0;
				pewc_update_total_js();
			}

		},

		evaluate_look_up_table: function( calc_field_id ) {

			var result = false;

			if( pewc_look_up_fields == undefined ) return false;

			var look_up_field = pewc_look_up_fields[calc_field_id];

			var table = look_up_field[0];
			var x_field = look_up_field[1];
			var y_field = look_up_field[2];

			var x_value = this.get_field_value( x_field );
			var y_value = this.get_field_value( y_field );

			var tables = pewc_look_up_tables[table];
			if( tables == undefined ) {
				return false;
			}

			var x_axis = tables[x_value];

			// If there's not an element in the tables array for our x value, find the next one
			if( x_axis == undefined && x_value && x_value != undefined ) {
				x_value = calculations.find_nearest_index( x_value, tables );
				x_axis = tables[x_value];
			} else if( x_axis == undefined && x_value==0 && x_value != undefined ) {
				x_axis = tables[Object.keys(tables)[0]];
			}

			if( x_axis != undefined ) {

				var y_axis = tables[y_value];

				if( y_axis == undefined && y_value && y_value != undefined ) {
					y_value = calculations.find_nearest_index( y_value, x_axis );
				}

				if( y_value == 'max' ) {
					// Get last value in x_axis
					result = x_axis[Object.keys(x_axis)[Object.keys(x_axis).length - 1]];
				} else {
					result = x_axis[y_value];
				}

			} else {

				return false;

			}

			return result;

		},

		get_field_value: function( field_id ) {

			var value = 0;

			if( $( '.pewc-field-' + field_id ).find( 'input.pewc-number-field' ).length > 0 ) {
				return $( '.pewc-field-' + field_id ).find( 'input.pewc-number-field' ).val();
			} else if( $( '.pewc-field-' + field_id ).find( 'select' ).length > 0 ) {
				return $( '.pewc-field-' + field_id ).find( 'select option:selected' ).attr( 'value' );
			}

			return value;
		},

		find_nearest_index: function( value, axis ) {

			var keys = Object.keys( axis ); // Just the keys

			if( parseFloat( value ) <= parseFloat( keys[0] ) ) {
				return keys[0];
			}

			for( var i=0; i < keys.length; i++ ) {
				if( ( parseFloat( value ) > parseFloat( keys[i] ) ) && keys[i+1] !=undefined && ( parseFloat( value ) <= parseFloat( keys[i+1] ) ) ) { // Find the first key that is greater than the value passed in
					return keys[i+1];
				}
			}

			if( keys[( keys.length ) - 1] == 'max' ) {
				return 'max';
			}

			// Return this as a fallback in case we've gone over the last index
			return keys[i];

		},

		evaluate_formula: function( fields, formula, round, decimals, calc_field_id ) {

			var calc_formula = formula;

			if( fields ) {

				// Replace any field tags with values
				for( var i in fields ) {

					// Look for any price values
					if( fields[i].indexOf( '_option_price' ) > -1 ) {
						var field_id = fields[i].replace( '_option_price', '' );
						// We want the price of the selected option in this field, not its value
						var o_price = parseFloat( $( 'form.cart .pewc-field-' + field_id ).attr( 'data-selected-option-price' ) );
						replace = new RegExp( '{field_' + fields[i] + '}', 'g' );
						calc_formula = calc_formula.replace( replace, o_price );
					} else if( fields[i].indexOf( '_field_price' ) > -1 ) {
						// We want the price of the field
						var field_id = fields[i].replace( '_field_price', '' );
						var f_price = parseFloat( $( 'form.cart .pewc-field-' + field_id ).attr( 'data-field-price' ) );
						replace = new RegExp( '{field_' + fields[i] + '}', 'g' );
						calc_formula = calc_formula.replace( replace, f_price );
					} else if( fields[i].indexOf( '_number_uploads' ) > -1 ) {
						// We want the number of uploads
						var field_id = fields[i].replace( '_number_uploads', '' );
						var num_uploads = parseFloat( $( 'form.cart .pewc-field-' + field_id ).find( '.pewc-number-uploads' ).val() );
						replace = new RegExp( '{field_' + fields[i] + '}', 'g' );
						calc_formula = calc_formula.replace( replace, num_uploads );
					} else {
						// Look for the value of number fields
						var f_val = parseFloat( $( 'form.cart .pewc-number-field-' + fields[i] ).val() );
						if( ! isNaN( f_val ) ) {
							replace = new RegExp( '{field_' + fields[i] + '}', 'g' );
							calc_formula = calc_formula.replace( replace, f_val );
						}
					}

				}

			}

			if( formula === undefined ) {
				console.log( 'formula not defined: ' + calc_field_id );
				return false;
			}

			var product_price = parseFloat( $('#pewc-product-price').val() );
			var quantity = parseFloat( $( '.quantity' ).find( '.qty' ).val() );
			if( formula.includes( "{look_up_table}" ) ) {
				calc_formula = calculations.evaluate_look_up_table( calc_field_id );
			}
			if( formula.includes( "{product_weight}" ) ) {
				var product_weight = parseFloat( $( '#pewc_product_weight' ).val() );
				calc_formula = calc_formula.replace( /{product_weight}/g, parseFloat( product_weight ) );
			}
			if( formula.includes( "{product_height}" ) ) {
				var product_height = parseFloat( $( '#pewc_product_height' ).val() );
				calc_formula = calc_formula.replace( /{product_height}/g, parseFloat( product_height ) );
			}
			if( formula.includes( "{product_length}" ) ) {
				var product_length = parseFloat( $( '#pewc_product_length' ).val() );
				calc_formula = calc_formula.replace( /{product_length}/g, parseFloat( product_length ) );
			}
			if( formula.includes( "{product_width}" ) ) {
				var product_width = parseFloat( $( '#pewc_product_width' ).val() );
				calc_formula = calc_formula.replace( /{product_width}/g, parseFloat( product_width ) );
			}
			if( formula.includes( "{product_price}" ) && product_price ) {
				calc_formula = calc_formula.replace( /{product_price}/g, parseFloat( product_price ) );
			}
			if( formula.includes( "{quantity}" ) && quantity ) {
				calc_formula = calc_formula.replace( /{quantity}/g, parseFloat( quantity ) );
			}
			if( formula.includes( "{variable_1}" ) && pewc_vars.variable_1 ) {
				calc_formula = calc_formula.replace( /{variable_1}/g, parseFloat( pewc_vars.variable_1 ) );
			}
			if( formula.includes( "{variable_2}" ) && pewc_vars.variable_2 ) {
				calc_formula = calc_formula.replace( /{variable_2}/g, parseFloat( pewc_vars.variable_2 ) );
			}
			if( formula.includes( "{variable_3}" ) && pewc_vars.variable_3 ) {
				calc_formula = calc_formula.replace( /{variable_3}/g, parseFloat( pewc_vars.variable_3 ) );
			}

			if( pewc_vars.global_calc_vars ) {
				var global_calc_vars = pewc_vars.global_calc_vars;
				// Iterate through our global vars
				for( var key in global_calc_vars ) {
					if( formula.includes( "{" + key + "}" ) ) {
						var global_var = "{" + key + "}";
						var global_var_regex = new RegExp( global_var, 'g' );
						calc_formula = calc_formula.replace( global_var_regex, global_calc_vars[key] );
					}
				}
			}

			var result;

			if( calc_formula == '*' ) return calc_formula;

			try {
				result = math.eval( calc_formula );

				if( round == 'ceil' ) {
					result = math.ceil( result );
				} else if( round == 'floor' ) {
					result = math.floor( result );
				}
				if( pewc_vars.math_round == 'yes' ) {
					result = Math.round( result * 100 ) / 100;
				}

				return result.toFixed( parseFloat( decimals ) );
			} catch( err ) {
				// Check all tags have been replaced
				return 'error';
			}

		}

	}

	calculations.init();

	var hidden_groups = {

		init: function() {
			$( 'body' ).on( 'pewc_conditions_checked', this.check_group_visibility );
		},

		/**
		 * Check whether to hide or display groups
		 */
		check_group_visibility: function() {

			// Check each group
			$( 'body' ).find( '.pewc-group-wrap' ).each( function() {
				var all_hidden = true;
				var group = $( this );
				$( group ).find( '.pewc-item' ).each( function() {
					if( ! $( this ).hasClass( 'pewc-hidden-field' ) ) {
						all_hidden = false;
					}
				});
				if( all_hidden ) {
					$( group ).addClass( 'pewc-hidden-group' );
				} else {
					$( group ).removeClass( 'pewc-hidden-group' );
				}
			});

		}

	}

	hidden_groups.init();

	var summary_panel = {

		init: function() {
			$( '.pewc-form-field' ).on( 'change update keyup', this.update_panel );
		},

		update_panel: function( e ) {
			var field_id = $( this ).closest( '.pewc-item' ).attr( 'data-field-id' );
			var field_value = $( this ).val();
		},

	}

	summary_panel.init();

	/**
	 * Sets product images for the chosen add-on
	 */
	var add_on_images = {

		init: function() {

			var $product        = $( 'form.cart' ).closest( '.product' ),
				$product_gallery  = $product.find( '.images' ),
				$gallery_nav      = $product.find( '.flex-control-nav' ),
				$gallery_img      = $gallery_nav.find( 'li:eq(0) img' ),
				$product_img_wrap = $product_gallery.find( '.woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder' ).eq( 0 ),
				$product_img      = $product_img_wrap.find( '.wp-post-image' ),
				$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

			// Set original src values
			$product_img.attr( 'data-pewc-old-src', $product_img.attr( 'src' ) );
			$product_img.attr( 'data-pewc-old-srcset', $product_img.attr( 'srcset' ) );

			// Check for default add ons with images

		},

		update_add_on_image: function( field, $form ) {

			var field_wrapper = $( field ).closest( '.pewc-item' );
			var field_type = $( field_wrapper ).attr( 'data-field-type' );
			if( ! $( field_wrapper ).hasClass( 'pewc-has-field-image' ) && field_type != 'image_swatch' ) {
				return;
			}
			if( ! pewc_vars.replace_image ) {
				return;
			}

			var add_on_image_wrapper;
			var add_on_image_src;
			var turn = 'off';
			if( field_type == 'checkbox' && $( field ).prop( 'checked' ) ) {
				turn = 'on';
				add_on_image_wrapper = $( field_wrapper ).find( '.pewc-item-field-image-wrapper' );
				add_on_image_src = $( add_on_image_wrapper ).attr( 'data-image-full-size' );
			}
			if( field_type == 'image_swatch' ) {
				turn = 'on';
				add_on_image_wrapper = $( field_wrapper ).find( '.pewc-radio-image-wrapper.checked' );
				add_on_image_src = $( add_on_image_wrapper ).find( 'img' ).attr( 'data-src' );
				add_on_image_srcset = add_on_image_src;
			}

			var $product        = $form.closest( '.product' ),
				$product_gallery  = $product.find( pewc_vars.product_gallery ),
				$gallery_nav      = $product.find( '.flex-control-nav' ),
				$gallery_img      = $gallery_nav.find( 'li:eq(0) img' ),
				$product_img_wrap = $product_gallery.find( pewc_vars.product_img_wrap ).eq( 0 ),
				$product_img      = $product_img_wrap.find( 'img' ),
				$product_link     = $product_img_wrap.find( 'a' ).eq( 0 );

			if ( add_on_image_wrapper ) {

				if( turn == 'on' ) {
					$product_img.attr( 'src', add_on_image_src );
					$product_img.attr( 'srcset', add_on_image_srcset );
				} else {
					$product_img.attr( 'src', $product_img.attr( 'data-pewc-old-src' ) );
					$product_img.attr( 'srcset', $product_img.attr( 'data-pewc-old-srcset' ) );
				}

			}

		}

	}

	add_on_images.init();

	var tooltips = {

		init: function() {

			if( pewc_vars.enable_tooltips == 'yes' && ! pewc_vars.dequeue_tooltips ) {
				$( '.tooltip' ).tooltipster(
					{
						theme: 'tooltipster-shadow',
						side: 'right'
					}
				);
			}

		}

	}

	tooltips.init();

	var quickview = {

		init: function() {
			$( 'body' ).on( 'click', '.pewc-show-quickview', this.show_quickview );
			$( 'body' ).on( 'click', '#pewc-quickview-background, .pewc-close-quickview', this.hide_quickview );
		},

		show_quickview: function( e ) {
			e.preventDefault();
			$( 'body' ).addClass( 'pewc-quickview-active' );
			// $( '.pewc-quickview-product-wrapper' ).hide();
			$( '#pewc-quickview-' + $( this ).attr( 'data-child-product-id' ) ).css( 'left', '50%' );
		},

		hide_quickview: function( e ) {
			 e.preventDefault();
			 $( 'body' ).removeClass( 'pewc-quickview-active' );
			 $( '.pewc-quickview-product-wrapper' ).css( 'left', '-5000px' );
		}

	}

	quickview.init();

})(jQuery);
