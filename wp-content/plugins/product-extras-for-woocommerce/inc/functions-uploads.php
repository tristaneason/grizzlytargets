<?php
/**
 * Functions for uploading files
 * @since 3.7.6
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

function pewc_ajax_upload_script( $id, $field, $multiply_price ) {

	$accepted_files = pewc_get_accepted_files();
	$max_file_size = pewc_get_max_upload();
	$max_files = ! empty( $field['max_files'] ) ? absint( $field['max_files'] ) : 1; ?>

	<script>
		Dropzone.autoDiscover = false;
		jQuery(document).ready(function( $ ) {

			<?php do_action( 'pewc_start_upload_script', $id, $field ); ?>

			var ajaxUrl = pewc_vars.ajaxurl;
			var dropzone_<?php echo esc_attr( $id ); ?> = new Dropzone( "#dz_<?php echo esc_attr( $id ); ?>", {

	      dictDefaultMessage: "<?php echo apply_filters( 'pewc_filter_dictDefaultMessage_message', __( 'Drop files here to upload', 'pewc' ) ); ?>",
	      dictFallbackMessage: "<?php echo apply_filters( 'pewc_filter_dictFallbackMessage_message', __( 'Your browser does not support drag and drop file uploads', 'pewc' ) ); ?>",
	      dictFallbackText: "<?php echo apply_filters( 'pewc_filter_dictFallbackText_message', __( 'Please use the fallback form below to upload your files like in the olden days', 'pewc' ) ); ?>",
	      dictFileTooBig: "<?php echo apply_filters( 'pewc_filter_dictFileTooBig_message', __( 'The file is too big', 'pewc' ) ); ?>",
	      dictInvalidFileType: "<?php echo apply_filters( 'pewc_filter_dictInvalidFileType_message', __( 'You cannot upload files of this type', 'pewc' ) ); ?>",
	      dictCancelUpload: "<?php echo apply_filters( 'pewc_filter_dictCancelUpload_message', __( 'Cancel upload', 'pewc' ) ); ?>",
	      dictUploadCanceled: "<?php echo apply_filters( 'pewc_filter_dictUploadCanceled_message', __( 'Upload cancelled.', 'pewc' ) ); ?>",
	      dictCancelUploadConfirmation: "<?php echo apply_filters( 'pewc_filter_dictCancelUploadConfirmation_message', __( 'Are you sure you want to cancel this upload?', 'pewc' ) ); ?>",
	      dictRemoveFile: "<?php echo apply_filters( 'pewc_filter_dictRemoveFile_message', __( 'Remove file', 'pewc' ) ); ?>",
	      dictMaxFilesExceeded: "<?php echo apply_filters( 'pewc_filter_dictMaxFilesExceeded_message', __( 'You cannot upload any more files.', 'pewc' ) ); ?>",

				previewTemplate: document.querySelector('#tpl').innerHTML,
				url: ajaxUrl,
				acceptedFiles: "<?php echo esc_attr( $accepted_files ); ?>",
				maxFiles: <?php echo absint( $max_files ); ?>,
				maxFilesize: <?php echo esc_attr( $max_file_size ); ?>,
				thumbnailWidth: <?php echo apply_filters( 'pewc_dropzone_thumbnail_width', 1000, $id, $field ); ?>,
				thumbnailHeight: <?php echo apply_filters( 'pewc_dropzone_thumbnail_height', 1000, $id, $field ); ?>,
				addRemoveLinks: true,
				uploadMultiple: true,
				maxThumbnailFilesize: <?php echo apply_filters( 'pewc_dropzone_max_thumbnail_size', 10, $id ); ?>,
				timeout: <?php echo apply_filters( 'pewc_dropzone_timeout', 30000, $id ); ?>,
				<?php do_action( 'pewc_end_upload_options', $id, $field ); ?>
				init: function() {
					<?php do_action( 'pewc_start_upload_script_init', $id, $field ); ?>

					this.on( 'sendingmultiple', function( file, xhr, formData ) {
						<?php if( pewc_disable_add_to_cart_upload() ) { ?>
							$( 'body' ).find( 'form.cart .single_add_to_cart_button' ).attr( 'disabled', true );
						<?php } ?>
						formData.append( 'action', 'pewc_dropzone_upload' );
						formData.append( 'pewc_file_upload', $( '#pewc_file_upload' ).val() );
						formData.append( 'field_id', '<?php echo $field['field_id']; ?>' );
						formData.append( 'pewc_product_id', $( '#pewc_product_id' ).val() );
						formData.append( 'file_data', $( '#<?php echo esc_attr( $id ); ?>_file_data' ).val() );
					});
					this.on( 'successmultiple', function( file, response ) {
						return;
						var received_files = response.data.files;

						// This is an alternative method to filter duplicates, not currently used
						// var uniq = {};
						// var arrFiltered = received_files.filter( obj => !uniq[obj.name] && ( uniq[obj.name] = true ) );

						var unique_files = [];
						var exists = false;
						var index = 0;
						$.each( received_files, function( i, el ) {
							// Reset this for each file
							exists = false;
							// Check if an element with this file name is already in the unique_files array
							for( k in unique_files ) {
								if( unique_files[k].name === el.name ) {
									// If the file already exists, we're going to overwrite it with the newer version
									exists = true;
									index = k;
									break;
								}
							}
					    if( ! exists ) {
								unique_files.push( el );
							} else {
								unique_files[index] = el;
							}
						});

						$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( JSON.stringify( unique_files ) );

						var num_files = unique_files.length;

						var upload_delay = setTimeout(
							function() {
								$( '#<?php echo esc_attr( $id ); ?>_number_uploads' ).val( JSON.stringify( num_files ) ).trigger( 'change' );
								<?php if( $multiply_price ) { ?>
									var price = $( '#<?php echo esc_attr( $id ); ?>_base_price' ).val();
									price = parseFloat( num_files ) * parseFloat( price );
									$( '#dz_<?php echo esc_attr( $id ); ?>' ).closest( '.pewc-item' ).attr( 'data-price', price );
									$( 'body' ).trigger( 'pewc_force_update_total_js' );
								<?php } ?>
								$( 'body' ).trigger( 'pewc_check_conditions' );
								$( 'body' ).trigger( 'pewc_trigger_calculations' );
								$( 'body' ).trigger( 'pewc_image_uploaded', [ '<?php echo esc_attr( $id ); ?>', num_files ] );
							},
							500
						);

						<?php if( pewc_disable_add_to_cart_upload() ) { ?>
							$( 'body' ).find( 'form.cart .single_add_to_cart_button' ).attr( 'disabled', false );
						<?php } ?>
					});
					this.on( 'queuecomplete', function() {
						// We use this method because successmultiple was overwriting some files when used with Advanced Uploads
						var files = dropzone_<?php echo esc_attr( $id ); ?>.files;
						var num_files = dropzone_<?php echo esc_attr( $id ); ?>.files.length;
						var all_files = [];
						var uploaded_files = [];

						if ( num_files > 0 && $( '#<?php echo esc_attr( $id ); ?>_file_data' ).val() != '' ) {
							// on 3.9.7, we regenerate the dropzone area if files were previously uploaded
							uploaded_files = JSON.parse( $( '#<?php echo esc_attr( $id ); ?>_file_data' ).val() );
						}

						// Ensure we have a list of the currently uploaded files, excluding any that may have been removed
						if( files ) {
							for( k in files ) {
								var file = files[k];
								if (file.xhr === undefined) {
									if ( uploaded_files.length > 0 ) {
										// use the already uploaded files instead
										all_files.push( uploaded_files[k ]) ;
									}
									continue; // if we're regenerating the dropzone, this is undefined, so skip the rest of the loop
								}
								var response = JSON.parse( file.xhr.response );
								var received_files = response.data.files;
								if( received_files ) {
									for( f in received_files ) {
										if( file.name === received_files[f].name ) {
											// Identify the file from the response data
											all_files.push( received_files[f] );
											break;
										}
									}
								}
							}
						}

						$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( JSON.stringify( all_files ) );

						var upload_delay = setTimeout(
							function() {
								$( '#<?php echo esc_attr( $id ); ?>_number_uploads' ).val( JSON.stringify( num_files ) ).trigger( 'change' );
								<?php if( $multiply_price ) { ?>
									var price = $( '#<?php echo esc_attr( $id ); ?>_base_price' ).val();
									price = parseFloat( num_files ) * parseFloat( price );
									$( '#dz_<?php echo esc_attr( $id ); ?>' ).closest( '.pewc-item' ).attr( 'data-price', price );
									$( 'body' ).trigger( 'pewc_force_update_total_js' );
								<?php } ?>
								$( 'body' ).trigger( 'pewc_check_conditions' );
								$( 'body' ).trigger( 'pewc_trigger_calculations' );
								$( 'body' ).trigger( 'pewc_image_uploaded', [ '<?php echo esc_attr( $id ); ?>', num_files ] );
							},
							500
						);

						<?php if( pewc_disable_add_to_cart_upload() ) { ?>
							$( 'body' ).find( 'form.cart .single_add_to_cart_button' ).attr( 'disabled', false );
						<?php } ?>

					});
					this.on( 'removedfile', function( file, response ) {
						$( '.dropzone.dz-clickable' ).block({
							message: null,
							overlayCSS:  {
				        backgroundColor: '#fff',
				        opacity:         0.6,
				        cursor:          'wait'
					    },
						});
						$.ajax({
							type: 'POST',
							url: pewc_vars.ajaxurl,
							data: {
								action: 'pewc_dropzone_remove',
								file: file.name,
								pewc_file_upload: $( '#pewc_file_upload' ).val(),
								file_data: $( '#<?php echo esc_attr( $id ); ?>_file_data' ).val()
							},
							success: function( response ) {
								$( '.dropzone.dz-clickable' ).unblock();
								$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( JSON.stringify( response.data.files ) );
								var num_files = response.data.count;
								if( num_files === 0 ) {
									$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( '' );
								}
								$( '#<?php echo esc_attr( $id ); ?>_number_uploads' ).val( JSON.stringify( num_files ) ).trigger( 'change' );
								<?php // if( $multiply_price ) { ?>
									var price = $( '#<?php echo esc_attr( $id ); ?>_base_price' ).val();
									price = parseFloat( num_files ) * parseFloat( price );
									$( '#dz_<?php echo esc_attr( $id ); ?>' ).closest( '.pewc-item' ).attr( 'data-price', price );
									$( 'body' ).trigger( 'pewc_force_update_total_js' );
								<?php // } ?>
								$( '#dz_<?php echo esc_attr( $id ); ?>' ).closest( '.pewc-item' ).find( '.aouau-quantity-field' ).trigger( 'wcaouau-update-quantity-field' );
								$( 'body' ).trigger( 'pewc_check_conditions' );
								$( 'body' ).trigger( 'pewc_trigger_calculations' );
							}
						});
					});
					this.on( 'error', function( file, response ) {
						console.log( 'error' );
					});

				},

				<?php do_action( 'pewc_after_upload_script_init', $id, $field ); ?>

			});


			// if the product page has been submitted but there's an error, we'll try to re-build the dropzone area with previously uploaded files, so that they won't have to re-upload again
			var pewc_file_data = $( '#<?php echo esc_attr( $id ); ?>_file_data' ).val();
			if ( pewc_file_data != '') {
				// convert to JSON
				var pewc_file_data_json = JSON.parse( pewc_file_data );
				// loop through each file
				$.each(pewc_file_data_json, function(key, value){
					var existingFile = value;
					
					// add other elements needed by Advanced Uploads
					var new_uuid = Dropzone.uuidv4();
					existingFile.upload = { uuid : new_uuid };
					existingFile.accepted = true;

					dropzone_<?php echo esc_attr( $id ); ?>.files.push( existingFile );
					
					dropzone_<?php echo esc_attr( $id ); ?>.emit( 'addedfile', existingFile );
					dropzone_<?php echo esc_attr( $id ); ?>.options.thumbnail.call(dropzone_<?php echo esc_attr( $id ); ?>, existingFile, '<?php echo site_url() ?>' + existingFile.url );
					//dropzone_<?php echo esc_attr( $id ); ?>.createThumbnailFromUrl( existingFile, existingFile.url );
					dropzone_<?php echo esc_attr( $id ); ?>.emit( 'success', existingFile ); // shows the "Uploaded" text
					dropzone_<?php echo esc_attr( $id ); ?>.emit( 'complete', existingFile ); // this needs to be called, or the upload bar will appear
					dropzone_<?php echo esc_attr( $id ); ?>._updateMaxFilesReachedClass();

					<?php if ( ! empty( $field['quantity_per_upload'] ) ) { ?>
						// adjust quantity per field for Advanced Uploads
						if (typeof existingFile.quantity !== 'undefined') {
							$( 'input[name="<?php echo esc_attr( $id ); ?>_extra_fields\['+key+'\]"]' ).val( existingFile.quantity );
						}
					<?php } ?>
				});
				
			}



			<?php do_action( 'pewc_end_upload_script', $id, $field ); ?>

		});
	</script>

	<?php
}
add_action( 'pewc_do_ajax_upload_script', 'pewc_ajax_upload_script', 10, 3 );

/**
 * Get the accepted file types for our upload
 * @since 3.7.6
 */
function pewc_get_accepted_files() {

	$accepted_files = array();
	$permitted_mimes = pewc_get_pretty_permitted_mimes();
	$permitted_mimes = explode( ' ', $permitted_mimes );
	foreach( $permitted_mimes as $file_type ) {
		$accepted_files[] = '.' . $file_type;
	}
	$accepted_files = join( ', ', $accepted_files );

	return $accepted_files;
}

/**
 * Convert the AJAX uploaded files object into a $_FILES type array
 * @param $pewc_file_data		The files object uploaded via jQuery
 * @param $id 							The field ID
 */
function pewc_get_files_array( $pewc_file_data, $id, $product_id ) {

	$files[$id] = array();
	$pewc_file_data = apply_filters(
		'pewc_file_data',
		json_decode( stripslashes( $pewc_file_data ) ),
		$id,
		$product_id
	);

	$index = 0;

	if( $pewc_file_data ) {

		foreach( $pewc_file_data as $upload ) {

			if ( ! is_object( $upload ) )
				continue;
			$files[$id]['file'][$index] = $upload->file;
			$files[$id]['name'][$index] = $upload->name;
			$files[$id]['type'][$index] = isset( $upload->filetype->type ) ? $upload->filetype->type : $upload->type;
			$files[$id]['error'][$index] = $upload->error;
			$files[$id]['size'][$index] = $upload->size;
			$files[$id]['url'][$index] = $upload->url;
			$files[$id]['tmp_name'][$index] = $upload->tmp_name;
			$index++;

		}

	}

	return apply_filters( 'pewc_files_array', $files, $pewc_file_data, $id, $product_id );

}

function pewc_disable_add_to_cart_upload() {
	$disable = get_option( 'pewc_disable_add_to_cart', 'no' );
	return $disable == 'yes' ? true : false;
}

/*
 * Save uploaded files to session, so that they are not lost if there was an error in validation
 * @since 3.9.7
 */
function pewc_save_uploaded_files_to_session( $uploaded_files, $field_id ) {
	// Make sure WooCommerce session is already set
	if ( isset(WC()->session) && WC()->session->has_session() ) {
		$field_id = @floor( $field_id ); // integer only
		if ( ! empty( $uploaded_files ) ) {
			// save
			// if AJAX upload is enabled, $uploaded_files is a JSON string (pewc_file_data). Else, $uploaded_files is an array of $_FILES
			WC()->session->set( 'uploaded_files_'.$field_id, $uploaded_files );
		}
		else {
			// remove from session
			WC()->session->__unset( 'uploaded_files_'.$field_id );
		}
	}
}

/*
 * Get uploaded files from session
 * @since 3.9.7
 */
function pewc_get_uploaded_files_from_session( $field_id, $item, $cart_item ) {
	if( pewc_enable_ajax_upload() == 'yes' ) {
		// AJAX upload, JSON string
		$files = '';
	}
	else {
		// standard
		$files = array();
	}

	$files = WC()->session->get( 'uploaded_files_'.$field_id, $files );

	if ( empty( $files ) && ! empty( $_GET[ 'pewc_key' ]) && pewc_user_can_edit_products() ) {
		// retrieve from cart instead
		if ( ! empty( $cart_item['product_extras']['groups'][$item['group_id']][$field_id]['files'] ) ) {
			$uploaded_files = $cart_item['product_extras']['groups'][$item['group_id']][$field_id]['files'];
			if ( is_array( $uploaded_files ) ) {
				$tmp = array();
				foreach ( $uploaded_files as $uf ) {
					if ( isset( $uf['url'] ) ) {
						// fix URL for displaying in Dropzone
						$uf['url'] = str_replace( site_url().'/', '/', $uf['url'] );
					}
					$tmp[] = $uf;
				}
				$files = json_encode( $tmp );
			}
		}
	}

	return $files;
}