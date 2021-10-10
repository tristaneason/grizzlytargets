<?php
/**
 * Functions for date fields
 * @since 3.9.2
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the option for displaying days of the week in date fields
 * @since 3.9.2
 */
function pewc_show_days_of_the_week( $item ) {
	$disable = get_option( 'pewc_disable_days', 'no' );
	$disable = $disable == 'yes' ? true : false;
	return $disable;
}

/**
 * Check whether to show a setting for offsetting the minimum date
 * @since 3.9.2
 */
function pewc_enable_offset_days( $item ) {
	$offset = get_option( 'pewc_offset_days', 'no' );
	$offset = $offset == 'yes' ? true : false;
	return $offset;
}

/**
 * Check whether to show a setting for blocked dates
 * @since 3.9.2
 */
function pewc_enable_blocked_dates( $item ) {
	$blocked = get_option( 'pewc_blocked_dates', 'no' );
	$blocked = $blocked == 'yes' ? true : false;
	return $blocked;
}

function pewc_get_date_field_params( $item ) {

	$params = array();

	// Check whether we have enabled the offset days option
	if( pewc_enable_offset_days( $item ) ) {
		if( ! empty( $item['offset_days'] ) ) {
			$params[] = '"minDate" : ' . absint( $item['offset_days'] ) .'';
		}
	} else {
		if( ! empty( $item['min_date_today'] ) ) {
			$params[] = '"minDate" : 0';
		} else if( ! empty( $item['field_mindate'] ) ) {
			$mindate = strtotime( $item['field_mindate'] );
			$year = date( 'Y', $mindate );
			$month = date( 'm', $mindate ) - 1;
			$day = date( 'd', $mindate );
			$params[] = '"minDate" : new Date( ' . $year .', ' . $month . ', ' . $day . ' )';
		}
	}

	if( ! empty( $item['field_maxdate'] ) ) {
		$maxdate = strtotime( $item['field_maxdate'] );
		$year = date( 'Y', $maxdate );
		$month = date( 'm', $maxdate ) - 1;
		$day = date( 'd', $maxdate );
		$params[] = '"maxDate" : new Date( ' . $year .', ' . $month . ', ' . $day . ' )';
	}

	// Check for disabled days
	if( pewc_show_days_of_the_week( $item ) || pewc_enable_blocked_dates( $item ) ) {
		$weekdays = isset( $item['weekdays'] ) ? $item['weekdays'] : array();
		if( $weekdays ) {
			$weekdays = json_encode( array_keys( $weekdays ) );
		} else {
			$weekdays = '';
		}
		$blocked = isset( $item['blocked_dates'] ) ? str_replace( ' ', '', $item['blocked_dates'] ) : array();
		if( $blocked ) {
			$blocked = explode( ',', $blocked );
		} else {
			$blocked = array();
		}

		$params[] = 'beforeShowDay : function( date ) {
			var allowed = true;
			var blocked = ' . json_encode( array_values( $blocked ) ) . ';
			var datestring = jQuery.datepicker.formatDate( "yy-mm-dd", date );
			if( blocked.indexOf( datestring ) > -1 ) {
				allowed = false;
			}
			var weekdays = "' . $weekdays . '";
			var day = date.getDay();
			if( weekdays.indexOf( day ) > -1 ) {
				allowed = false;
			}
			return [ allowed, false ];
		}';
	}

	$params = apply_filters( 'pewc_filter_date_field_params', $params, $item );
	if( $params ) {
		$params = join( ', ', $params );
	}

	return $params;
}
