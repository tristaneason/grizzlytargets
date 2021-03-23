<!--Save changes modal-->
<div class="remodal import-csv-modal" data-remodal-id="import-csv-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<?php do_action('vg_sheet_editor/import/before_form', $post_type); ?>
		<?php
		$is_not_supported = apply_filters('vg_sheet_editor/import/is_not_supported', null, $post_type);
		if (!is_null($is_not_supported)) {
			$message = ( is_string($is_not_supported)) ? $is_not_supported : __('The import feature is not compatible with your website. Make sure WordPress and all the plugins and themes are up to date.');
			?>

			<h3><?php _e('Import csv', VGSE()->textname); ?></h3>
			<p><?php echo wp_kses_post($message); ?></p>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel', VGSE()->textname); ?></button>

		<?php } else {
			?>
			<?php do_action('vg_sheet_editor/import/before_form', $post_type); ?>
			<form class="import-csv-form vgse-modal-form " action="<?php echo admin_url('admin.php?page=vgse_import_page'); ?>" method="POST">
				<ul class="unstyled-list">
					<li class="step current">
						<h3><?php _e('Import csv', VGSE()->textname); ?></h3>
						<label><?php _e('Source', VGSE()->textname); ?></label>								
						<select name="source" class="source">
							<option value="">- -</option>
							<option value="csv_upload"><?php _e('CSV file from my computer', VGSE()->textname); ?></option>
							<option value="csv_url"><?php _e('CSV file from url', VGSE()->textname); ?></option>
							<option value="paste"><?php _e('Copy & paste from another spreadsheet or table', VGSE()->textname); ?></option>
							<option value="server_file"><?php _e('CSV file in the server', VGSE()->textname); ?></option>
						</select>

						<div class="data-input csv-upload">
							<label><?php _e('CSV file', VGSE()->textname); ?> </label>
							<input type="file" name="local_file" class="data" id="vgse-import-local-file"  /> 
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav"  data-type="local"><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input csv-url">
							<label><?php _e('File URL', VGSE()->textname); ?> </label>								
							<input type="text" name="file_url" placeholder="File URL" class="data" />
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav" data-type="url"><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input paste">
							<label><?php _e('Copy and Paste into the spreadsheet below', VGSE()->textname); ?> </label>
							<p>This is not recommended for large amounts of data.</p>								
							<div id="handsontable-paste"></div>
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav" data-type="json"><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input server-file">
							<label><?php _e('CSV file location', VGSE()->textname); ?> </label>
							<input type="text" name="server_file" class="data" id="vgse-import-local-file"  /> 
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav"  data-type="server_file"><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
						</div> 
						<label class=""><input type="checkbox" name="enable_advanced_source_options" class="toggle-advanced-options"> <?php _e('Show advanced options', VGSE()->textname); ?></label>
						<div class="advanced-options" style="display: none">
							<div class="field">
								<label><?php _e('Separator', VGSE()->textname); ?></label><br>
								<input type="text" name="separator" class="separator" value="," />
							</div>
							<div class="field">
								<label><?php _e('Start from row number:', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('If you stop an import to edit your CSV file or change the import speed, you can start a new import and continue from where you left off.', VGSE()->textname); ?>">( ? )</a></label><br>
								<input type="number" name="start_row" class="skip-rows"/>								
							</div>
							<div class="field">
								<label><input type="checkbox" name="decode_quotes" class="decode-quotes"/> <?php _e('Decode quotes?', VGSE()->textname); ?></label>								
							</div>
							<div class="field">
								<label><input type="checkbox" name="auto_retry_failed_batches" class="auto-retry-failed-batches"/> <?php _e('Auto retry failed batches?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('We import the file in batches (i.e. 4 rows every few seconds). When one batch fails, we normally pause the import and ask you if you want to retry or cancel the import. Select this option to auto retry. Careful, you need to select the option to update existing rows in step 3 of the import, so we can retry and skip what was imported successfully and only retry what failed, if you dont select the option to update in step 3 of the import, every retry might duplicate some previously imported rows.', VGSE()->textname); ?>">( ? )</a></label>								
							</div>
							<?php do_action('vg_sheet_editor/import/after_advanced_options', $post_type); ?>
						</div>
						<p><?php printf(__('Tip. You can use the "export" tool to download a CSV and see the available columns and format.<br>You can read our <a href="%s" target="_blank">documentation here</a>.', VGSE()->textname), VGSE()->get_site_link('https://wpsheeteditor.com/blog/?s=import', 'importer-documentation')); ?></p>

						<?php do_action('vg_sheet_editor/import/after_data_sources', $post_type); ?>
					</li>
					<li class="map-columns step">
						<h3><?php _e('Select columns to import', VGSE()->textname); ?></h3>
						<p class="one-column-detected-tip alert alert-blue"><?php printf(__('Important. We only detected one column in the CSV file. If this is incorrect, follow <a href="%s" target="_blank">these steps</a> to fix it', VGSE()->textname), 'https://wpsheeteditor.com/documentation/faq/#1572924330879-e05ed559-f740234'); ?></p>
						<p class="import-auto-map-notice"><?php _e('We automatically detected all the columns.', VGSE()->textname); ?><br/><button class="button  next-step step-nav"><?php _e('Import all the columns', VGSE()->textname); ?></button> <?php _e('or', VGSE()->textname); ?> <button class="button import-map-select-columns"><?php _e('Select individual columns to import', VGSE()->textname); ?></button></p>
						<p><?php _e('Tip. If you edited information from this site, you should import the columns edited and record_id. Don\'t import columns that weren\'t modified', VGSE()->textname); ?></p>
						<p class="import-column-bulk-actions"><span class="csv-column-list-header"></span><span class="wp-column-list-header"><select><option value=""><?php _e('Bulk actions', VGSE()->textname); ?></option><option value="unselect"><?php _e('Unselect all columns', VGSE()->textname); ?></option></select></span></p>
						<p class="import-column-list-headers"><span class="csv-column-list-header"><?php _e('CSV Column', VGSE()->textname); ?></span><span class="wp-column-list-header"><?php _e('WordPress field', VGSE()->textname); ?></span></p>
						<div class="map-template hidden">
							<span class="csv-column-name-wrapper"><span class="csv-column-name-text"></span><small class="csv-column-name-example"><?php _e('Example: ', VGSE()->textname); ?></small></span><select class="" name="sheet_editor_column[]"><?php $this->render_wp_fields_import_options($post_type);
						?></select>
							<input class="csv-column-name-value" name="source_column[]" type="hidden" />
						</div>	
						<button class="button button-primary button-primario prev-step step-nav" ><i class="fa fa-chevron-left"></i> <?php _e('Previous', VGSE()->textname); ?></button>
						<button class="button button-primary button-primario next-step step-nav" ><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
					</li>
					<li class="write-type step">
						<h3><?php _e('Do you want to update or create items?', VGSE()->textname); ?></h3>
						<select name="writing_type" required>
							<option value="">- -</option>
							<option value="both"><?php _e('Create new items and update existing items', VGSE()->textname); ?></option>
							<option value="all_new"><?php _e('Import all rows as new', VGSE()->textname); ?></option>
							<option value="only_new"><?php _e('Only create new items, ignore existing items', VGSE()->textname); ?></option>
							<option value="only_update"><?php _e('Update existing items, ignore new items', VGSE()->textname); ?></option>
						</select>	
						<div class="field-find-existing-columns">						
							<h4><?php _e('How do we find existing items to update?', VGSE()->textname); ?></h4>

							<?php do_action('vg_sheet_editor/import/before_existing_wp_check_message', $post_type); ?>

							<p class="wp-check-message"><?php _e('We find rows with the same value in the CSV Field and the WP Field.<br>I.e. Products with same SKU or ID.', VGSE()->textname); ?></p>

							<p class="wp-field-requires-ignored-column alert alert-blue"><?php _e('You selected a column from the CSV file below but the column is not <br>being imported. Please go to the previous step and select the column to be imported.<br>Hypothetical example, if you want to update existing products with same ID, you need to import the ID column otherwise we don\'t have the IDs to find them.', VGSE()->textname); ?></p>
							<div class="field-wrapper">
								<label><?php _e('CSV Field', VGSE()->textname); ?></label>
								<select name="existing_check_csv_field[]" class="select2 existing-check-csv-field">
									<option value="">- -</option>
								</select>	
							</div>
							<div class="field-wrapper">
								<label><?php _e('WordPress Field', VGSE()->textname); ?></label>
								<select name="existing_check_wp_field[]" class="select2">
									<option value="">- -</option>
									<?php
									$wp_columns_to_search = implode(apply_filters('vg_sheet_editor/import/wp_check/available_columns_options', VGSE()->helpers->get_post_type_columns_options($post_type, array(
														'conditions' => array(
															'data_type' => 'post_terms'
														),
														'operator' => 'NOT'
															), false, false), $post_type));
									echo $wp_columns_to_search;
									?>
								</select>	
							</div>
							<!--Deactivated temporarily, I don't think this option is being used and it causes confusion-->
							<!--<div class="field-wrapper">
								<label><?php _e('Field 2: CSV Field', VGSE()->textname); ?></label>
								<select name="existing_check_csv_field[]" class="select2 existing-check-csv-field">
									<option value="">- -</option>
								</select>	
							</div>
							<div class="field-wrapper">
								<label><?php _e('Field 2: WordPress Field', VGSE()->textname); ?></label>
								<select name="existing_check_wp_field[]" class="select2">
									<option value="">- -</option>
							<?php echo $wp_columns_to_search; ?>
								</select>	
							</div>-->
						</div>
						<button class="button button-primary button-primario prev-step step-nav" ><i class="fa fa-chevron-left"></i> <?php _e('Previous', VGSE()->textname); ?></button>								
						<button class="button button-primary button-primario next-step step-nav" ><?php _e('Next', VGSE()->textname); ?> <i class="fa fa-chevron-right"></i></button>
					</li>
					<li class="preview-step step">
						<h3><?php _e('Final step', VGSE()->textname); ?></h3>
						<p><?php _e('1. Are we reading the file properly? Here is a preview of the first 5 rows from the file.', VGSE()->textname); ?></p>
						<div id="hot-preview"></div>
						<p><?php _e('2. Please make a backup before executing the import, so you can revert in case you used wrong settings or the file was wrong. The import will save the information directly.', VGSE()->textname); ?></p>
						<button class="button button-primary button-primario prev-step step-nav step-nav" ><i class="fa fa-chevron-left"></i> <?php _e('Previous', VGSE()->textname); ?></button>
					</li>
				</ul>
				<input type="hidden" name="import_file" class="import-file">
				<input type="hidden" name="total_rows" class="total-rows">
				<input type="hidden" name="vgse_plain_mode" value="yes">
				<input type="hidden" name="import_type" value="csv">
				<input type="hidden" name="action" value="vgse_import_csv">
				<input type="hidden" name="vgse_import" value="yes">
				<input type="hidden" value="<?php echo $nonce; ?>" name="nonce">
				<input type="hidden" value="<?php echo $post_type; ?>" name="post_type">
				<button type="submit" class="remodal-confirm"><?php _e('The preview is fine, start import', VGSE()->textname); ?></button>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel import', VGSE()->textname); ?></button>
			</form>
			<div class="import-step">
				<h3><?php _e('Importing', VGSE()->textname); ?></h3>


				<?php do_action('vg_sheet_editor/import/before_response', $post_type); ?>
				<div class="import-response">

				</div>

				<p class="import-actions"><a href="#" class="button pause-import button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e('Pause', VGSE()->textname); ?></a></p>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
			</div >
		<?php } ?>
	</div>								
</div>