<?php
/**
 * Functions for pro stuff
 * @since 2.0.0
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter field types with some Pro ones
 * @since 2.0.0
 */
function pewc_filter_field_types_pro( $field_types ) {
	if( ! pewc_is_pro() ) {
		return $field_types;
	}
	$field_types['products'] = __( 'Products', 'pewc' );
	$field_types['checkbox_group'] = __( 'Checkbox Group', 'pewc' );
	$field_types['image_swatch'] = __( 'Image Swatch', 'pewc' );
	ksort( $field_types );
	return $field_types;
}
// add_filter( 'pewc_filter_field_types', 'pewc_filter_field_types_pro' );

function pewc_field_item_pro_message( $item ) {
	if( ! pewc_is_pro() ) {
		printf(
			'<div class="pewc-pro-only">%s %s</div>',
			__( 'This field is not available with the Basic licence. Please upgrade to the Pro licence to use this field.', 'pewc' ),
			sprintf(
				'<a target="_blank" href="%s">%s</a>',
				pewc_get_upgrade_url(),
				__( 'Upgrade', 'pewc' ) . '<span style="font-size: 14px;" class="dashicons dashicons-external"></span>'
			)
		);
	}
}
add_action( 'pewc_end_fields_heading', 'pewc_field_item_pro_message' );

/**
 * Add group display class
 * @since 2.0.0
 */
function pewc_filter_groups_wrapper_classes_display( $classes, $product_extra_groups, $post_id ) {
	$display = pewc_get_group_display( $post_id );
	$classes[] = 'pewc-groups-' . $display;
	return $classes;
}
// add_filter( 'pewc_filter_groups_wrapper_classes', 'pewc_filter_groups_wrapper_classes_display', 10, 3 );

/**
 * Ensure the radio image field has a radio wrapper
 * @since 2.0.0
 */
function pewc_filter_single_product_radio_classes( $classes, $item ) {
	if( $item['field_type'] == 'image_swatch' && empty( $item['allow_multiple'] ) ) {
		$classes[] = 'pewc-item-radio';
	} else if( $item['field_type'] == 'image_swatch' && ! empty( $item['allow_multiple'] ) ) {
		$classes[] = 'pewc-item-image-swatch-checkbox';
	}
	return $classes;
}
add_filter( 'pewc_filter_single_product_classes', 'pewc_filter_single_product_radio_classes', 10, 2 );

/**
 * Print the tabs for groups
 * @since 2.0.0
 * @param $args	0 - $post_id, 1 - $product_extra_groups
 */
function pewc_do_group_tabs( $args ) {
	$display = pewc_get_group_display( $args[0] );
	if( $display == 'tabs' || $display == 'steps' ) {
		$product_extra_groups = $args[1];
		if( $product_extra_groups ) {
			$class = 'active-tab';
			echo '<div class="pewc-' . $display . '-wrapper">';
			foreach( $product_extra_groups as $group_id=>$group ) {

				$group_title = pewc_get_group_title( $group_id, $group, pewc_has_migrated() );

				if( isset( $group_title ) ) {
					printf(
						'<div id="pewc-tab-%s" data-group-id="%s" class="pewc-tab %s">%s</div>',
						$group_id,
						$group_id,
						$class,
						esc_html( $group_title )
					);
				}
				$class = '';
			}
			echo '</div>';
		}
	}


}
add_action( 'pewc_start_groups', 'pewc_do_group_tabs', 10 );

function pewc_next_step_button( $group, $group_id, $display, $groups ) {
	if( $display == 'steps' ) {
		$groups = array_keys( $groups );
		$position = array_search( $group_id, $groups );
		echo '<div class="pewc-step-buttons">';
		if( isset( $groups[$position - 1] ) ) {
	    $prev_group = $groups[$position - 1];
			printf(
				'<a href="#" id="pewc-step-%s" data-group-id="%s" class="button pewc-next-step-button">%s</a>',
				$prev_group,
				$prev_group,
				__( 'Previous', 'pewc' )
			);
		}
		if( isset( $groups[$position + 1] ) ) {
	    $next_group = $groups[$position + 1];
			printf(
				'<a href="#" id="pewc-step-%s" data-group-id="%s" class="button pewc-next-step-button">%s</a>',
				$next_group,
				$next_group,
				__( 'Next', 'pewc' )
			);
		}
		echo '</div>';
	}
}
add_action( 'pewc_end_group_content_wrapper', 'pewc_next_step_button', 10, 4 );

function pewc_get_group_display( $product_id ) {
	if( ! pewc_is_pro() ) {
		return 'standard';
	}
	$product = wc_get_product( $product_id );
	if( is_object( $product ) ) {
		$display = $product->get_meta( 'pewc_display_groups' );
		return apply_filters( 'pewc_group_display', $display );
	}
	return false;
}

function pewc_get_upgrade_url() {
	$url = 'https://pluginrepublic.com/my-account/';
	$licence_id = '';
	$payment_id = get_option( 'pewc_payment_id', false );
	$license_id = get_option( 'pewc_license_id', false );
	if( false === $payment_id && false === $license_id ) {
		return $url;
	}

	// Check what kind of licence they have
	$expires = get_option( 'pewc_licence_expires', false );

	if( false === $license_id || false === $expires ) {
		// If we don't have the license ID we can send the user to the correct part of the account page
		$url = add_query_arg(
			array(
				'action'			=> 'manage_licenses',
				'payment_id'	=> $payment_id,
				'view'				=> 'upgrades'
			),
			$url
		);
	} else {
		// Send users direct to the checkout for their upgrade
		$upgrade_id = 1;
		if( $expires == 'lifetime' ) {
			$upgrade_id = 3;
		}
		$url = 'https://pluginrepublic.com/checkout/';
		$url = add_query_arg(
			array(
				'edd_action'	=> 'sl_license_upgrade',
				'license_id'	=> $license_id,
				// 'payment_id'	=> $payment_id,
				'upgrade_id'	=> $upgrade_id
			),
			$url
		);
	}
	return $url;
}
