function wppfmAddToQueueString( idToAdd ) {
	var listElement = jQuery( '#wppfm-feed-list-feeds-in-queue' );

	if ( wppfmQueueStringIsEmpty() ) {
		listElement.text( idToAdd );
	} else {
		listElement.text( listElement.text() + ',' + idToAdd );
	}
}

function wppfmRemoveFromQueueString( idToRemove ) {
	var listElement = jQuery( '#wppfm-feed-list-feeds-in-queue' );
	var currentString = listElement.text();

	if ( currentString.indexOf( ',' ) > -1 ) {
		currentString = currentString.endsWith( idToRemove ) ? currentString.replace( idToRemove, '' ) : currentString.replace( idToRemove + ',', '' );
		listElement.text( currentString );
	} else {
		wppfmClearQueueString();
	}
}

function wppfmQueueStringIsEmpty() {
	return jQuery( '#wppfm-feed-list-feeds-in-queue' ).text().length < 1;
}

function wppfmClearQueueString() {
	jQuery( '#wppfm-feed-list-feeds-in-queue' ).text( '' );
}
