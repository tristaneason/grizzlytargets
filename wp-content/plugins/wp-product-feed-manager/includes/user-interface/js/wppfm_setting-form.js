/*global wppfm_setting_form_vars */
function wppfm_auto_feed_fix_changed() {
	wppfm_auto_feed_fix_mode(
		jQuery( '#wppfm_auto_feed_fix_mode' ).is( ':checked' ),
		function( response ) {
			console.log( 'Auto feed fix setting changed to ' + response );
		}
	);
}

function wppfm_background_processing_mode_changed() {
	wppfm_background_processing_mode(
		jQuery( '#wppfm_background_processing_mode' ).is( ':checked' ),
		function( response ) {
			console.log( 'Background processing setting changed to ' + response );
		}
	);
}

function wppfm_feed_logger_status_changed() {
	wppfm_feed_logger_status(
		jQuery( '#wppfm_process_logging_mode' ).is( ':checked' ),
		function( response ) {
			console.log( 'Feed process logger status changed to ' + response );
		}
	);
}

function wppfm_show_product_identifiers_changed() {
	wppfm_show_pi_status(
		jQuery( '#wppfm_product_identifiers' ).is( ':checked' ),
		function( response ) {
			console.log( 'Show Product Identifiers setting changed to ' + response );
		}
	);
}

function wppfm_wpml_use_full_resolution_urls_changed() {
	wppfm_wpml_use_full_url_resolution(
		jQuery( '#wppfm_wpml_use_full_resolution_urls' ).is( ':checked' ),
		function( response ) {
			console.log( 'WPML Use full resolution URLs setting changed to ' + response );
		}
	);
}

function wppfm_third_party_attributes_changed() {
	wppfm_change_third_party_attribute_keywords(
		jQuery( '#wppfm_third_party_attr_keys' ).val(),
		function( response ) {
			console.log( 'Third party attributes changed to ' + response );
		}
	);
}

function wppfm_notice_mailaddress_changed() {
	wppfm_change_notice_mailaddress(
		jQuery( '#wppfm_notice_mailaddress' ).val(),
		function( response ) {
			console.log( 'Notice recipient setting changed to ' + response );
		}
	);
}

function wppfm_clear_feed_process() {
	wppfm_showFeedSpinner();
	wppfm_clear_feed_process_data(
		function( response ) {
			console.log( 'Clear feed process activated' );
			wppfm_hideFeedSpinner();
		}
	);
}

function wppfm_reinitiate() {
	wppfm_showFeedSpinner();
	wppfm_reinitiate_plugin(
		function( response ) {
			console.log( 'Re-initialization initiated ' + response );
			wppfm_hideFeedSpinner();
		}
	);
}

function wppfm_backup() {
	var backupFileNameElement = jQuery( '#wppfm_backup-file-name' );

	if ( backupFileNameElement.val() !== '' ) {
		jQuery( '#wppfm_backup-wrapper' ).hide();

		wppfm_initiateBackup(
			backupFileNameElement.val(),
			function( response ) {
				wppfm_resetBackupsList();

				if ( response !== '1' ) {
					wppfm_show_error_message( 'New backup file made ' + response );
				}
			}
		);
	} else {
		alert( wppfm_setting_form_vars.first_enter_file_name );
	}
}

function wppfm_deleteBackupFile( fileName ) {
	var userInput = confirm( wppfm_setting_form_vars.confirm_file_deletion.replace( '%backup_file_name%', fileName ) );

	if ( userInput === true ) {
		wppfm_deleteBackup(
			fileName,
			function( response ) {
				wppfm_show_success_message( wppfm_setting_form_vars.file_deleted.replace( '%backup_file_name%', fileName ) );
				wppfm_resetBackupsList();
				console.log( 'Backup file deleted ' + response );
			}
		);
	}
}

function wppfm_restoreBackupFile( fileName ) {

	var userInput = confirm( wppfm_setting_form_vars.confirm_file_restoring.replace( '%backup_file_name%', fileName ) );

	if ( userInput === true ) {

		wppfm_restoreBackup(
			fileName,
			function( response ) {

				if ( response === '1' ) {
					wppfm_show_success_message( wppfm_setting_form_vars.file_restored.replace( '%backup_file_name%', fileName ) );
					wppfm_resetOptionSettings();
					console.log( 'Backup file restored ' + response );
				} else {
					wppfm_show_error_message( response );
				}
			}
		);
	}
}

function wppfm_duplicateBackupFile( fileName ) {

	wppfm_duplicateBackup(
		fileName,
		function( response ) {

			if ( response === '1' ) {
				wppfm_show_success_message( wppfm_setting_form_vars.file_duplicated.replace( '%backup_file_name%', fileName ) );
				console.log( 'Backup file duplicated' + response );
			} else {
				wppfm_show_error_message( response );
			}
			wppfm_resetBackupsList();
		}
	);
}
