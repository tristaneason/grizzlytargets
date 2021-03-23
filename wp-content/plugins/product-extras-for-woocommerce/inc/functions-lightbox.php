<?php
/**
 * Functions for the lightbox
 * @since 3.6.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * How many fields to show as a teaser in the cart form
 * @since 3.6.0
 */
function pewc_get_number_teaser_fields() {

	if( ! pewc_is_pro () ) {
		return false;
	}

	return apply_filters( 'pewc_number_teaser_fields', 2 );

}

/**
 * How many options to show as a teaser per image swatch field
 * @since 3.6.0
 */
function pewc_get_number_teaser_options() {

	if( ! pewc_is_pro () ) {
		return false;
	}

	return apply_filters( 'pewc_number_teaser_options', 5 );

}

/**
 * How many options to show as a teaser per image swatch field
 * @since 3.6.0
 */
function pewc_show_all_fields_in_lightbox() {

	if( ! pewc_is_pro () ) {
		return false;
	}

	return apply_filters( 'pewc_show_all_fields_in_lightbox', false );

}

function pewc_lightbox_styles( $args ) {

	if( ! pewc_is_pro () ) {
		return false;
	}
	$options = floatval( pewc_get_number_teaser_options() ); ?>

	<style type="text/css">
		form.cart .pewc-groups-lightbox.pewc-teaser-options-<?php echo $options; ?> .pewc-radio-image-wrapper:nth-child(n+<?php echo $options + 1; ?>),
		form.cart .pewc-groups-lightbox.pewc-teaser-options-<?php echo $options; ?> .pewc-checkbox-image-wrapper:nth-child(n+<?php echo $options + 1; ?>) {
			display: none
		}
		<?php if( ! pewc_show_all_fields_in_lightbox() ) { ?>
			.pewc-lightbox .pewc-lightbox-field {
				display: none;
			}
		<?php } ?>
	</style>

	<?php
}
add_action( 'pewc_start_groups', 'pewc_lightbox_styles' );

function pewc_display_lightbox_launch_link( $post_id, $product, $summary_panel ) {

	if( pewc_get_group_display( $post_id ) == 'lightbox' ) {

		printf(
			'<p><a class="pewc-lightbox-launch-link" href="#">%s</a></p>',
			apply_filters(
				'pewc_lightbox_launch_link',
				__( 'See more', 'pewc' ),
				$post_id
			)
		);

	}

}
add_action( 'pewc_after_group_wrap', 'pewc_display_lightbox_launch_link', 1, 3 );

function pewc_lightbox() {

	$post_id = get_the_ID();

	if( pewc_get_group_display( $post_id ) == 'lightbox' ) { ?>

		<div class="pewc-lightbox">
			<div class="pewc-lightbox-background pewc-close-lightbox"></div>
			<div class="pewc-lightbox-wrapper">
				<div class="pewc-lightbox-inner">
					<div class="pewc-lightbox-fields"></div><!-- pewc-lightbox-fields -->
					<p><a class="pewc-close-lightbox button" href="#"><?php _e( 'Close', 'pewc' ); ?></a></p>
				</div><!-- pewc-lightbox-inner -->
			</div><!-- pewc-lightbox-wrapper -->
		</div>

		<script>
		jQuery( document ).ready( function( $ ) {

			$( '.pewc-lightbox-launch-link' ).on( 'click', function( e ) {
				e.preventDefault();
				$( 'body' ).addClass( 'pewc-lightbox-active' );
			});

			$( '.pewc-close-lightbox' ).on( 'click', function( e ) {
				e.preventDefault();
				$( 'body' ).removeClass( 'pewc-lightbox-active' );
			});

			var clone = $( '.pewc-product-extra-groups-wrap' ).clone();
			$( clone ).find( "[id]" ).each( function() {
				var id = $( this ).attr( 'id' );
				$( this ).attr( 'id', id + '_clone' );
				$( this ).addClass( 'pewc-form-field-clone' );
			});

			$( clone ).find( '.pewc-checkbox-image-wrapper label' ).each( function() {
				var label = $( this ).attr( 'for' );
				label += '_clone';
				$( this ).attr( 'for', label );
			});

			$( '.pewc-lightbox-fields' ).html( clone );

			// Update the clone fields with new attributes
			$( 'body' ).on( 'change update', '.pewc-form-field:not(.pewc-form-field-clone), .pewc-checkbox-form-field:not(.pewc-form-field-clone), .pewc-radio-form-field:not(.pewc-form-field-clone)', function() {
				var field = $( this ).closest( '.pewc-item' );
				var type = $( field ).attr( 'data-field-type' );
				var original_id = $( this ).attr( 'name' );
				original_id += '_clone';
				if( type == 'checkbox' ) {
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
				} else if( type == 'checkbox_group' ) {
					original_id = $( this ).attr( 'id' );
					original_id += '_clone';
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
				} else if( type == 'products' ) {
					// Checkboxes
					var wrapper = $( this ).closest( '.pewc-checkbox-image-wrapper' );
					original_id = $( this ).attr( 'id' );
					original_id += '_clone';
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
					if( $( field ).find( '.child-product-wrapper' ).hasClass( 'products-quantities-independent' ) ) {
						var quantity = $( wrapper ).find( '.pewc-child-quantity-field' ).val();
						$( '#' + original_id ).closest( '.pewc-checkbox-image-wrapper' ).find( '.pewc-child-quantity-field' ).val( quantity );
					}
					// Radio
					wrapper = $( this ).closest( '.pewc-radio-image-wrapper' );
					original_id = $( this ).attr( 'id' );
					original_id += '_clone';
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
					if( $( field ).find( '.child-product-wrapper' ).hasClass( 'products-quantities-independent' ) ) {
						var delay = setTimeout(
							function() {
								var quantity = $( field ).find( '.pewc-child-quantity-field' ).val();
								$( '#' + original_id ).closest( '.pewc-item' ).find( '.pewc-child-quantity-field' ).val( quantity );
							}, 500
						);
					}
				} else if( type == 'name_price' || type == 'number' || type == 'text' || type == 'textarea' ) {
					$( '#' + original_id ).val( $( this ).val() );
				}
			});

			$( 'body' ).on( 'change update', '.pewc-form-field-clone', function() {
				var field = $( this ).closest( '.pewc-item' );
				var type = $( field ).attr( 'data-field-type' );
				var original_id = $( this ).attr( 'name' );
				if( type == 'checkbox' ) {
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
				} else if( type == 'checkbox_group' ) {
					original_id = $( this ).attr( 'id' );
					original_id = original_id.replace( '_clone', '' );
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
				} else if( type == 'products' ) {
					// Checkboxes
					var wrapper = $( this ).closest( '.pewc-checkbox-image-wrapper' );
					original_id = $( this ).attr( 'id' );
					original_id = original_id.replace( '_clone', '' );
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
					if( $( field ).find( '.child-product-wrapper' ).hasClass( 'products-quantities-independent' ) ) {
						var delay = setTimeout(
							function() {
								var quantity = $( wrapper ).find( '.pewc-child-quantity-field' ).val();
								$( '#' + original_id ).closest( '.pewc-checkbox-image-wrapper' ).find( '.pewc-child-quantity-field' ).val( quantity );
							}, 500
						);
					}
					// Radio
					wrapper = $( this ).closest( '.pewc-radio-image-wrapper' );
					original_id = $( this ).attr( 'id' );
					original_id = original_id.replace( '_clone', '' );
					$( '#' + original_id ).prop( 'checked', $( this ).prop( 'checked' ) );
					if( $( field ).find( '.child-product-wrapper' ).hasClass( 'products-quantities-independent' ) ) {
						var delay = setTimeout(
							function() {
								var quantity = $( wrapper ).find( '.pewc-child-quantity-field' ).val();
								$( '#' + original_id ).closest( '.pewc-radio-image-wrapper' ).find( '.pewc-child-quantity-field' ).val( quantity );
							}, 500
						);
					}
					$( '#' + original_id ).trigger( 'change' );
				} else if( type == 'name_price' || type == 'number' || type == 'text' || type == 'textarea' ) {
					$( '#' + original_id ).val( $( this ).val() );
				}
			});

			$( 'body' ).on( 'change update', '.pewc-lightbox .pewc-child-quantity-field', function() {
				var name = $( this ).attr( 'name' );
				var quantity = $( this ).val();
				if( quantity > 0 ) {
					$( 'form.cart' ).find( 'input[name="' + name + '"]' ).val( quantity ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-checkbox-form-field' ).prop( 'checked', true );
				} else {
					$( 'form.cart' ).find( 'input[name="' + name + '"]' ).val( quantity ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-checkbox-form-field' ).prop( 'checked', false );
				}
			});

			$( 'body' ).on( 'change update', 'form.cart .pewc-child-quantity-field', function() {
				var name = $( this ).attr( 'name' );
				var quantity = $( this ).val();
				if( quantity > 0 ) {
					$( '.pewc-lightbox' ).find( 'input[name="' + name + '"]' ).val( quantity ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-checkbox-form-field' ).prop( 'checked', true );
				} else {
					$( 'pewc-lightbox' ).find( 'input[name="' + name + '"]' ).val( quantity ).closest( '.pewc-checkbox-image-wrapper' ).find( 'input.pewc-checkbox-form-field' ).prop( 'checked', false );
				}
			});

		});
		</script>

		<?php
	}

}
add_action( 'wp_footer', 'pewc_lightbox' );
