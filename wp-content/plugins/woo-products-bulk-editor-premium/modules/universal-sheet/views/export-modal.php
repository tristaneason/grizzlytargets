<!--Save changes modal-->
<div class="remodal export-csv-modal" data-remodal-id="export-csv-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<?php
		$is_not_supported = apply_filters('vg_sheet_editor/export/is_not_supported', null, $post_type);
		if (!is_null($is_not_supported)) {
			$message = ( is_string($is_not_supported)) ? $is_not_supported : __('The export feature is not compatible with your website. Make sure WordPress and all the plugins and themes are up to date.');
			?>

			<h3><?php _e('Export to CSV', VGSE()->textname); ?></h3>
			<p><?php echo wp_kses_post($message); ?></p>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel', VGSE()->textname); ?></button>

		<?php } else {
			?>
			<?php do_action('vg_sheet_editor/export/before_form', $post_type); ?>
			<form class="export-csv-form vgse-modal-form " action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST">
				<h3><?php _e('Export to CSV', VGSE()->textname); ?></h3>

				<div class="fields-to-export">
					<label><?php _e('What columns do you want to export?', VGSE()->textname); ?></label>
					<br/>
					<select name="export_columns[]" required data-placeholder="<?php _e('Select column...', VGSE()->textname); ?>" class="select2 export-columns" multiple>
						<option></option>
						<?php
						$this->render_wp_fields_export_options($post_type);
						?>
					</select>
					<br/>
					<button class="select-active button"><?php _e('Select active columns', VGSE()->textname); ?></button> <button class="select-all button"><?php _e('Select all', VGSE()->textname); ?></button> <button class="unselect-all button"><?php _e('Unselect  all', VGSE()->textname); ?></button>
					<br/>

					<label class="use-search-query-container"><input type="checkbox" value="yes"  name="use_search_query" required><?php _e('I understand it will export the posts from my current search.', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('For example, if you searched for posts by author = Mark using the search tool, we will export only posts with author Mark', VGSE()->textname); ?>">( ? )</a></label>
					<br/>

					<label class="excel-compatibility-container"><input type="checkbox" value="yes"  name="add_excel_separator_flag"><?php _e('I will edit this file with Microsoft Excel.', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('Sometimes Excel shows all the values in a single cell and it does not recognize the comma as separator. This fixes that problem', VGSE()->textname); ?>">( ? )</a></label>
					<?php if (current_user_can('manage_options')) { ?>
						<br/>

						<label class="save-for-later-container"><?php _e('Name of this export (optional)', VGSE()->textname); ?> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('We will save the current search query and columns, and show a dropdown in the export menu, so you can execute this export with one click in the future', VGSE()->textname); ?>">( ? )</a></label>
						<br/>
						<input type="text"  name="save_for_later_name">
					<?php } ?>
				</div>

				<?php do_action('vg_sheet_editor/export/before_response', $post_type); ?>
				<div class="export-response">

				</div>

				<p class="export-actions"><a href="#" class="button pause-export button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e('Pause', VGSE()->textname); ?></a></p>

				<input type="hidden" value="vgse_export_csv" name="action">
				<input type="hidden" value=" <?php echo $nonce; ?>" name="nonce">
				<input type="hidden" value="<?php echo $post_type; ?>" name="post_type">
				<button type="submit" class="remodal-confirm vgse-trigger-export"><?php _e('Start new export', VGSE()->textname); ?></button>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Cancel', VGSE()->textname); ?></button>

			</form>
		<?php } ?>
	</div>								
</div>