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

			// $( '#dz_<?php echo esc_attr( $id ); ?>' ).dropzone({
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
				thumbnailWidth: <?php echo apply_filters( 'pewc_dropzone_thumbnail_width', 100, $id ); ?>,
				thumbnailHeight: <?php echo apply_filters( 'pewc_dropzone_thumbnail_height', 100, $id ); ?>,
				addRemoveLinks: true,
				uploadMultiple: true,
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
					this.on( 'removedfile', function( file, response ) {
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
								$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( JSON.stringify( response.data.files ) );
								var num_files = response.data.count;
								if( num_files === 0 ) {
									$( '#<?php echo esc_attr( $id ); ?>_file_data' ).val( '' );
									console.log( 'removed file' );
								}

								$( '#<?php echo esc_attr( $id ); ?>_number_uploads' ).val( JSON.stringify( num_files ) ).trigger( 'change' );
								<?php if( $multiply_price ) { ?>
									var price = $( '#<?php echo esc_attr( $id ); ?>_base_price' ).val();
									price = parseFloat( num_files ) * parseFloat( price );
									$( '#dz_<?php echo esc_attr( $id ); ?>' ).closest( '.pewc-item' ).attr( 'data-price', price );
									$( 'body' ).trigger( 'pewc_force_update_total_js' );
								<?php } ?>
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

			$files[$id]['file'][$index] = $upload->file;
			$files[$id]['name'][$index] = $upload->name;
			$files[$id]['type'][$index] = $upload->filetype->type;
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
