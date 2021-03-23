jQuery( document ).ready( function( $ ) {

	$('#ph_fedex_hold_at_locations').data('select2', '1');

	$( document.body ).on( 'change', 'select.country_to_state, input.country_to_state,input#billing_city,input#billing_state,select#billing_state,input#billing_postcode', function() {
		$('#ph_fedex_hold_at_locations').val('');
	});
});