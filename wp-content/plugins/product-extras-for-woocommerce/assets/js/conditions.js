(function($) {

	$( document ).ready( function() {

		var reset_fields = [];

		var pewc_conditions = {

			init: function() {

				this.initial_check();
				$( '.pewc-condition-trigger input' ).on( 'change input keyup paste', this.trigger_condition_check );
				$( 'body' ).on( 'change', '.pewc-condition-trigger select', this.trigger_condition_check );
				$( '.pewc-calculation-trigger input' ).on( 'change input keyup paste', this.trigger_calculation );

				if( pewc_vars.conditions_timer > 0 ) {
					$( '.pewc-field-triggers-condition input' ).on( 'change input keyup paste', this.trigger_field_condition_check );
					$( '.pewc-field-triggers-condition select' ).on( 'update change', this.trigger_field_condition_check );
					$( '.pewc-calculation-value' ).on( 'calculation_field_updated', this.trigger_field_condition_check );
					$( '.qty' ).on( 'change input keyup paste', this.trigger_quantity_condition_check );
					$( 'body' ).on( 'pewc_reset_field_condition', this.trigger_field_reset_condition_check );
					if( typeof pewc_cost_triggers !== 'undefined' && pewc_cost_triggers.length > 0 ) {
						var cost_interval = setInterval(
							this.trigger_cost_condition_check,
							pewc_vars.conditions_timer
						);
					}
				}

			},

			initial_check: function() {

				// Check the fields
				if( pewc_vars.conditions_timer > 0 ) {

					$( '.pewc-field-triggers-condition' ).each( function() {
						var field = $( this ).closest( '.pewc-item' );
						var field_value = pewc_conditions.get_field_value( $( field ).attr( 'data-field-id' ), $( field ).attr( 'data-field-type' ) );
						var triggers_for = JSON.parse( $( field ).attr( 'data-triggers-for' ) );
						// Iterate through each field that is conditional on the updated field

						for( var g in triggers_for ) {
							conditions_obtain = pewc_conditions.check_field_conditions( triggers_for[g], field_value );
							var action = $( '.pewc-field-' + triggers_for[g] ).attr( 'data-field-conditions-action' );
							pewc_conditions.assign_field_classes( conditions_obtain, action, triggers_for[g] );
						}

					});

				}

				// Check the groups
				$( '.pewc-condition-trigger' ).each( function() {
					var field = $( this );
					var groups = JSON.parse( $( field ).attr( 'data-trigger-groups' ) );
					for( var g in groups ) {
						conditions_obtain = pewc_conditions.check_group_conditions( groups[g] );
						var action = $( '#pewc-group-' + groups[g] ).attr( 'data-condition-action' );
						pewc_conditions.assign_group_classes( conditions_obtain, action, groups[g] );
					}
				});
			},

			trigger_calculation: function() {

				// Possibly add a delay here to ensure calculations are made
				var calculations = $( this ).closest( '.pewc-item' ).attr( 'data-trigger-calculations' );
				if( calculations ) {
					calculations = JSON.parse( calculations );
					for( var c in calculations ) {
						$( '.pewc-field-' + calculations[c] ).find( '.pewc-calculation-value' ).trigger( 'change' );
					}
				}

			},

			trigger_condition_check: function() {

				var field = $( this ).closest( '.pewc-item' );
				var groups = JSON.parse( $( field ).attr( 'data-trigger-groups' ) );
				for( var g in groups ) {
					conditions_obtain = pewc_conditions.check_group_conditions( groups[g] );
					var action = $( '#pewc-group-' + groups[g] ).attr( 'data-condition-action' );
					pewc_conditions.assign_group_classes( conditions_obtain, action, groups[g] );
				}

			},

			trigger_field_condition_check: function() {

				var field = $( this ).closest( '.pewc-item' );
				var field_value = pewc_conditions.get_field_value( $( field ).attr( 'data-field-id' ), $( field ).attr( 'data-field-type' ) );
				var triggers_for = JSON.parse( $( field ).attr( 'data-triggers-for' ) );
				// Iterate through each field that is conditional on the updated field

				for( var g in triggers_for ) {
					conditions_obtain = pewc_conditions.check_field_conditions( triggers_for[g], field_value );
					var action = $( '.pewc-field-' + triggers_for[g] ).attr( 'data-field-conditions-action' );
					pewc_conditions.assign_field_classes( conditions_obtain, action, triggers_for[g] );
				}

				if( pewc_vars.reset_fields == 'yes' ) {
					pewc_conditions.reset_fields();
				}
			},

			// Iterate through fields that have had their values reset
			// Ensures fields with dependent conditions will also get reset correctly
			trigger_field_reset_condition_check: function() {

				// Use a timer to allow complex pages to catch up
				var reset_timer = setTimeout(
					function() {
						$( '.pewc-reset' ).each( function() {
							$( this ).removeClass( 'pewc-reset' );
							var field = $( this );
							var field_value = pewc_conditions.get_field_value( $( field ).attr( 'data-field-id' ), $( field ).attr( 'data-field-type' ) );
							var triggers_for = $( field ).attr( 'data-triggers-for' );
							if( triggers_for != undefined ) {

								var triggers_for = JSON.parse( $( field ).attr( 'data-triggers-for' ) );
								// Iterate through each field that is conditional on the updated field

								for( var g in triggers_for ) {
									conditions_obtain = pewc_conditions.check_field_conditions( triggers_for[g], field_value );
									var action = $( '.pewc-field-' + triggers_for[g] ).attr( 'data-field-conditions-action' );
									pewc_conditions.assign_field_classes( conditions_obtain, action, triggers_for[g] );
								}
							}

						});
					}, 100
				);

			},

			trigger_quantity_condition_check: function() {

				if( typeof pewc_quantity_triggers === 'undefined' ) {
					return;
				}

				var triggers_for = pewc_quantity_triggers;
				// Iterate through each field that is conditional on the updated field
				for( var g in triggers_for ) {
					conditions_obtain = pewc_conditions.check_field_conditions( triggers_for[g] );
					var action = $( '.pewc-field-' + triggers_for[g] ).attr( 'data-field-conditions-action' );
					pewc_conditions.assign_field_classes( conditions_obtain, action, triggers_for[g] );
				}

			},

			trigger_cost_condition_check: function() {

				var triggers_for = pewc_cost_triggers;
				// Iterate through each field that is conditional on the updated field
				for( var g in triggers_for ) {
					conditions_obtain = pewc_conditions.check_field_conditions( triggers_for[g] );
					var action = $( '.pewc-field-' + triggers_for[g] ).attr( 'data-field-conditions-action' );
					pewc_conditions.assign_field_classes( conditions_obtain, action, triggers_for[g] );
				}

			},

			check_group_conditions: function( group_id ) {

				var conditions = JSON.parse( $( '#pewc-group-' + group_id ).attr( 'data-conditions' ) );
				var match = $( '#pewc-group-' + group_id ).attr( 'data-condition-match' );
				var is_visible = false;
				if( match == 'all' ) {
					is_visible = true;
				}
				for( var i in conditions ) {
					var condition = conditions[i];
					var value = pewc_conditions.get_field_value( $( '.' + condition.field ).attr( 'data-field-id' ), condition.field_type );
					var meets_condition = this.field_meets_condition( value, condition.rule, condition.value );
					if( meets_condition && match =='any' ) {
						return true;
					} else if( ! meets_condition && match =='all' ) {
						return false;
					}
				}

				return is_visible;

			},

			check_field_conditions: function( field_id, field_value ) {

				var conditions = JSON.parse( $( '.pewc-field-' + field_id ).attr( 'data-field-conditions' ) );
				var match = $( '.pewc-field-' + field_id ).attr( 'data-field-conditions-match' );
				var is_visible = false;
				if( match == 'all' ) {
					is_visible = true;
				}
				for( var i in conditions ) {
					var condition = conditions[i];
					var field_value = this.get_field_value( $( '.' + condition.field ).attr( 'data-field-id' ), condition.field_type );
					var meets_condition = this.field_meets_condition( field_value, condition.rule, condition.value );
					if( meets_condition && match == 'any' ) {
						return true;
					} else if( ! meets_condition && match =='all' ) {
						return false;
					}
				}

				return is_visible;

			},

			// Get the value of the specified field
			get_field_value: function( field_id, field_type ) {
				var field_wrapper = $( '.' + field_id.replace( 'field', 'group' ) );
				var input_fields = ['text','number'];
				if( input_fields.includes( field_type ) ) {
					return $( '.pewc-field-' + field_id + ' input' ).val();
				} else if( field_type == 'select' || field_type == 'select-box' ) {
					return $( '.pewc-field-' + field_id + ' select' ).val();
				} else if( field_type == 'checkbox_group' ) {
					var field_value = [];
					$( field_wrapper ).find( 'input:checked' ).each( function() {
						field_value.push( $( this ).val() );
					});
					return field_value;
				} else if( field_type == 'products' ) {
					var field_value = [];
					$( field_wrapper ).find( 'input:checked' ).each( function() {
						field_value.push( $( this ).val() );
					});
					return field_value;
				} else if( field_type == 'image_swatch' ) {
					if( $( field_wrapper ).hasClass( 'pewc-item-image-swatch-checkbox' ) ) {
						// Array
						var field_value = [];
						$( field_wrapper ).find( 'input:checked' ).each( function() {
							field_value.push( $( this ).val() );
						});
						return field_value;
					} else {
						return $( '.pewc-field-' + field_id + ' input:radio:checked' ).val();
					}
				} else if( field_type == 'checkbox' ) {
					if( $( '#' + field_id ).prop( 'checked' ) ) {
						return '__checked__';
					}
					return false;
				} else if( field_type == 'radio' ) {
					return $( '.pewc-field-' + field_id  + ' input:radio:checked' ).val();
				} else if( field_type == 'quantity' ) {
					return $( '.quantity input.qty' ).val();
				} else if( field_type == 'cost' ) {
					return $( '#pewc_total_calc_price' ).val();
				} else if( field_type == 'upload' ) {
					return $( '.pewc-field-' + field_id ).find( '.pewc-number-uploads' ).val();
				} else if( field_type == 'calculation' ) {
					return $( '.pewc-field-' + field_id + ' .pewc-calculation-value' ).val();
				}

			},

			field_meets_condition: function( value, rule, required_value ) {

				if( rule == 'is' || rule == 'cost-equals' ) {
					return value == required_value;
				} else if( rule == 'is-not' ) {
					return value != required_value;
				} else if( rule == 'contains' ) {
					return value.includes( required_value );
				} else if( rule == 'does-not-contain' ) {
					return ! value.includes( required_value );
				} else if( rule == 'greater-than' || rule == 'cost-greater' ) {
					return value > required_value;
				} else if( rule == 'greater-than-equals' ) {
					return value >= required_value;
				} else if( rule == 'less-than' || rule == 'cost-less' ) {
					return value < required_value;
				} else if( rule == 'less-than-equals' ) {
					return value <= required_value;
				}

			},

			assign_group_classes: function( conditions_obtain, action, group_id ) {

				if( conditions_obtain ) {
					if( action == 'show' ) {
						$( '#pewc-group-' + group_id ).removeClass( 'pewc-group-hidden' );
						$( '#pewc-tab-' + group_id ).removeClass( 'pewc-group-hidden' );
					} else {
						$( '#pewc-group-' + group_id ).addClass( 'pewc-group-hidden' );
						$( '#pewc-tab-' + group_id ).addClass( 'pewc-group-hidden' );
					}
				} else {
					if( action == 'show' ) {
						$( '#pewc-group-' + group_id ).addClass( 'pewc-group-hidden' );
						$( '#pewc-tab-' + group_id ).addClass( 'pewc-group-hidden' );
					} else {
						$( '#pewc-group-' + group_id ).removeClass( 'pewc-group-hidden' );
						$( '#pewc-tab-' + group_id ).removeClass( 'pewc-group-hidden' );
					}
				}

			},

			assign_field_classes: function( conditions_obtain, action, field_id ) {

				if( conditions_obtain ) {
					if( action == 'show' ) {
						$( '.pewc-field-' + field_id ).removeClass( 'pewc-hidden-field' );
					} else {
						if( ! $( '.pewc-field-' + field_id ).hasClass( 'pewc-hidden-field' ) ) {
							$( '.pewc-field-' + field_id ).addClass( 'pewc-hidden-field pewc-reset-me' );
						}
					}
				} else {
					if( action == 'show' ) {
						if( ! $( '.pewc-field-' + field_id ).hasClass( 'pewc-hidden-field' ) ) {
							$( '.pewc-field-' + field_id ).addClass( 'pewc-hidden-field pewc-reset-me' );
						}
					} else {
						$( '.pewc-field-' + field_id ).removeClass( 'pewc-hidden-field' );
					}
				}

			},

			reset_fields: function() {

				$( '.pewc-reset-me' ).each( function() {

					var field = $( this );
					pewc_conditions.reset_field_value( field );
					$( field ).removeClass( 'pewc-reset-me' ).addClass( 'pewc-reset' );

				});

			},

			reset_field_value: function( field ) {

				// Iterate through all fields with pewc-reset-me class
				var inputs = ['date', 'name_price', 'number', 'text', 'textarea'];
				var checks = ['checkbox', 'checkbox_group', 'radio'];
				var field_type = $( field ).attr( 'data-field-type' );
				if( inputs.includes( field_type ) ) {
					$( field ).find( '.pewc-form-field' ).val( '' );
				} else if( field_type == 'image_swatch' ) {
					$( field ).find( 'input' ).prop( 'checked', false );
					$( field ).find( '.pewc-radio-image-wrapper, .pewc-checkbox-image-wrapper' ).removeClass( 'checked' );
				} else if( field_type == 'products' ) {
					$( field ).find( 'input' ).prop( 'checked', false );
					$( field ).find( '.pewc-form-field' ).val( '' );
					$( field ).find( '.pewc-radio-image-wrapper, .pewc-checkbox-image-wrapper' );
				} else if( checks.includes( field_type ) ) {
					$( field ).find( 'input' ).prop( 'checked', false );
				} else if( field_type == 'select' ) {
					$( field ).find( '.pewc-form-field' ).prop( 'selectedIndex', 0 );
				}

				$( 'body' ).trigger( 'pewc_reset_field_condition' );

			}

		}

		pewc_conditions.init();

	});

})(jQuery);
