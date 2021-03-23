<?php
/**
 * Functions for sanitising all fields
 * @since 2.4.5
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitise every field
 */
function pewc_sanitise_groups( $groups ) {
	// Iterate through each group
	if( $groups ) {
		foreach( $groups as $group_id=>$group ) {
			if( ! empty( $group['items'] ) ) {
				foreach( $group['items'] as $item_id=>$item ) {
					// Sanitise the description
					$groups[$group_id]['items'][$item_id] = pewc_sanitise_description_field( $item );
					// Sanitise radio field options
					if( isset( $item['field_type'] ) && $item['field_type'] == 'radio' ) {
						$groups[$group_id]['items'][$item_id] = pewc_sanitise_radio_field( $item );
					}
				}
			}
		}
	}
	return $groups;
}

function pewc_sanitise_description_field( $item ) {
	if( ! empty( $item['field_description'] ) ) {
		$item['field_description'] = wp_kses_post( $item['field_description'] );
	}
	return $item;
}

function pewc_sanitise_radio_field( $item ) {
	if( ! empty( $item['field_options'] ) ) {
		foreach( $item['field_options'] as $index=>$option ) {
			$item['field_options'][$index]['value'] = sanitize_text_field( wp_unslash( $item['field_options'][$index]['value'] ) );
			// We're going to create a key using the value - this will change if we introduce the ability to specify the key
			// The key element is what the radio field will use as its value, i.e. <input type="radio" value="_key_">
			// The value element is used as the label
			$item['field_options'][$index]['key'] = pewc_keyify_field( $item['field_options'][$index]['value'] );
		}
	}

	return $item;
}

function pewc_sanitise_field_options( $options ) {
	if( ! empty( $options ) ) {
		foreach( $options as $index=>$option ) {
			// $options[$index]['value'] = sanitize_text_field( wp_unslash( $options[$index]['value'] ) );
			// We're going to create a key using the value - this will change if we introduce the ability to specify the key
			// The key element is what the radio field will use as its value, i.e. <input type="radio" value="_key_">
			// The value element is used as the label
			$options[$index]['key'] = pewc_keyify_field( sanitize_text_field( wp_unslash( $options[$index]['value'] ) ) );
		}
	}

	return $options;
}

/**
 * Remove any slashes from option values to ensure options with apostrophes etc are recognised
 * @since 3.7.10
 */
function pewc_stripslashes_from_options( $options ) {

	if( ! empty( $options ) ) {
		foreach( $options as $index=>$value ) {
			$options[$index] = stripslashes( $value );
		}
	}

	return $options;

}

/**
 * Remove any unwanted characters from a string
 */
function pewc_keyify_field( $field ) {
	$field = str_replace( array( '"', '\'' ), '_', ( wp_unslash( $field ) ) );
	return $field;
}
