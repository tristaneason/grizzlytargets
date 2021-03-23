<?php
/**
 * Template used for the settings page.
 */
$nonce = wp_create_nonce('bep-nonce');
?>
<div class="remodal-bg custom-columns-page-content" id="vgse-wrapper" data-nonce="<?php echo $nonce; ?>">
	<div class="">
		<div class="">
			<h2 class="hidden"><?php _e('Sheet Editor', VGSE()->textname); ?></h2>
			<a href="https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=custom-columns-logo" target="_blank"><img src="<?php echo VGSE()->plugin_url; ?>assets/imgs/logo-248x102.png" class="vg-logo"></a>
		</div>
		<h2><?php _e('Add New Columns to the Spreadsheet', VGSE()->textname); ?></h2>

		<p><?php printf(__('Enter the column name (anything you want), and select the field key from the dropdown. If you dont find it you can type it in the dropdown. <a href="%s" target="_blank">View Tutorial</a>', VGSE()->textname), 'https://www.youtube.com/watch?v=fxzVgzjhdR0'); ?></p>
		<p><?php _e('Enable the advanced mode at the bottom of the page to customize the column width, format (editor, file upload, taxonomies), etc.', VGSE()->textname); ?></p>
		<p><a class="button help-button" href="<?php echo VGSE()->get_support_links('contact_us', 'url', 'custom-columns-help'); ?>" target="_blank" ><i class="fa fa-envelope"></i> <?php _e('Need help? Contact us', VGSE()->textname); ?></a></p>

		<?php do_action('vg_sheet_editor/custom_columns/settings_page/before_form'); ?>
		<form class="repeater custom-columns-form" action="<?php echo admin_url('admin-ajax.php'); ?>">
			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
			<input type="hidden" name="action" value="vgse_save_columns">
			<div data-repeater-list="columns" class="columns-wrapper">
				<?php
				$columns = get_option($this->key, array());

				if (empty($columns)) {
					$columns[] = $this->default_column_settings;
				}

				foreach ($columns as $column_index => $column_settings) {
					?>
					<div data-repeater-item class="column-wrapper">
						<div class="column-fields-wrapper">
							<?php do_action('vg_sheet_editor/custom_columns/settings_page/before_template_fields', $columns); ?>
							<div class="field-container field-container-name">
								<label><?php _e('Column name', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('The column name displayed in the spreadsheet', VGSE()->textname); ?>">( ? )</a></label>
								<input type="text" name="name" value="<?php echo $column_settings['name']; ?>" class="name-field"/>
							</div>
							<div class="field-container field-container-key">
								<label><?php _e('Column key', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('The key that will be used for saving the information in the database. This must be unique, only letters and underscores.', VGSE()->textname); ?>">( ? )</a></label>
								<select name="key" class="key-field select2"><?php
									$custom_columns = WP_Sheet_Editor_Custom_Columns::get_instance();
									$all_keys = VGSE()->helpers->get_all_meta_keys('', 1000);
									$registered_columns = $custom_columns->get_all_registered_columns_keys();

									if (!empty($column_settings['key']) && !in_array($column_settings['key'], $all_keys)) {
										$all_keys[] = $column_settings['key'];
									}
									foreach ($all_keys as $key) {
										if (in_array($key, $registered_columns) && $key !== $column_settings['key']) {
											continue;
										}
										?>
										<option value="<?php echo $key ?>" <?php selected($key, $column_settings['key']); ?>><?php echo ucwords(str_replace('_', ' ', $key)); ?></option>
										<?php
									}
									?></select>
							</div>
							<div class="field-container field-container-data-source">
								<label><?php _e('Data source', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Select the kind of information used in the cells of this column.', VGSE()->textname); ?>">( ? )</a></label>
								<select name="data_source">
									<option value="post_data" <?php selected($column_settings['data_source'], 'post_data'); ?>><?php _e('Post data', VGSE()->textname); ?></option>
									<option value="post_meta" <?php selected($column_settings['data_source'], 'post_meta'); ?>><?php _e('Post meta (i.e. metaboxes)', VGSE()->textname); ?></option>
									<option value="post_terms" <?php selected($column_settings['data_source'], 'post_terms'); ?>><?php _e('Post terms (i.e. categories)', VGSE()->textname); ?></option>
								</select>
							</div>
							<div class="field-container field-container-post-types">
								<label><?php _e('Post type(s)', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('What kind of posts require this column in the spreadsheet?', VGSE()->textname); ?>">( ? )</a></label>

								<?php
								$post_types = VGSE()->helpers->get_allowed_post_types();

								if (!is_array($column_settings['post_types'])) {
									$column_settings['post_types'] = array($column_settings['post_types']);
								}
								if (!empty($post_types)) {
									foreach ($post_types as $key => $post_type_name) {
										if (is_numeric($key)) {
											$key = $post_type_name;
										}
										?>
										<div class="post-type-field"><input type="checkbox" name="post_types" value="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php checked(in_array($key, $column_settings['post_types'])); ?>> <label for="<?php echo $key; ?>"><?php echo $post_type_name; ?></label></div>
										<?php
									}
								}
								?>
							</div>
							<div class="field-container field-container-read-only">
								<label><?php _e('Is read only?', VGSE()->textname); ?></label>
								<input type="checkbox" name="read_only" value="yes"  <?php checked('yes', $column_settings['read_only']); ?>/>
							</div>
							<div class="field-container field-container-formulas">
								<label><?php _e('Allow to edit using formulas?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('If you disable this option, this column will be edited manually only.', VGSE()->textname); ?>">( ? )</a></label>
								<input type="checkbox" name="allow_formulas" value="yes" <?php checked('yes', $column_settings['allow_formulas']); ?>/>
							</div>
							<div class="field-container field-container-hide">
								<label><?php _e('Allow to hide column?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Allow to hide this column on the settings page?', VGSE()->textname); ?>">( ? )</a></label>
								<input type="checkbox" name="allow_hide" value="yes" <?php checked('yes', $column_settings['allow_hide']); ?>/>
							</div>
							<div class="field-container field-container-rename">
								<label><?php _e('Allow to rename column?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Allow to rename column on the settings page?', VGSE()->textname); ?>">( ? )</a></label>
								<input type="checkbox" name="allow_rename" value="yes" <?php checked('yes', $column_settings['allow_rename']); ?>/>
							</div>
							<div class="field-container field-container-cell-type">
								<label><?php _e('Cell type', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Select the format of the cells, if the cells should be normal text, a file uploader, or text editor.', VGSE()->textname); ?>">( ? )</a></label>

								<select name="cell_type" >
									<option value=""><?php _e('Normal cell', VGSE()->textname); ?></option>
									<option value="boton_tiny" <?php selected($column_settings['cell_type'], 'boton_tiny'); ?>/><?php _e('TinyMCE Editor', VGSE()->textname); ?></option>
									<option value="boton_gallery" <?php selected($column_settings['cell_type'], 'boton_gallery'); ?>/><?php _e('File upload (single)', VGSE()->textname); ?></option>
									<option value="boton_gallery_multiple" <?php selected($column_settings['cell_type'], 'boton_gallery_multiple'); ?>/><?php _e('File upload (multiple)', VGSE()->textname); ?></option>
								</select>
							</div>
							<div class="field-container field-container-plain-renderer">
								<label><?php _e('Plain text mode: Render as: (Use only if cell type is empty)', VGSE()->textname); ?></label>
								<select name="plain_renderer" >
									<option value="text" <?php selected($column_settings['plain_renderer'], 'text'); ?>/><?php _e('Simple text', VGSE()->textname); ?></option>
									<option value="date" <?php selected($column_settings['plain_renderer'], 'date'); ?>/><?php _e('Calendar', VGSE()->textname); ?></option>
									<option value="taxonomy_dropdown" <?php selected($column_settings['plain_renderer'], 'taxonomy_dropdown'); ?>/><?php _e('Taxonomy dropdown. Only if data source = post terms.', VGSE()->textname); ?></option>
									<option value="html" <?php selected($column_settings['plain_renderer'], 'html'); ?>/><?php _e('Unfiltered HTML', VGSE()->textname); ?></option>
								</select>
							</div>
							<div class="field-container field-container-formatted-renderer">
								<label><?php _e('Formatted cell mode: Render as: (Use only if cell type is empty)', VGSE()->textname); ?></label>
								<select name="formatted_renderer" >
									<option value="text" <?php selected($column_settings['formatted_renderer'], 'text'); ?>/><?php _e('Simple text', VGSE()->textname); ?></option>
									<option value="date" <?php selected($column_settings['formatted_renderer'], 'date'); ?>/><?php _e('Calendar', VGSE()->textname); ?></option>
									<option value="taxonomy_dropdown" <?php selected($column_settings['formatted_renderer'], 'taxonomy_dropdown'); ?>/><?php _e('Taxonomy dropdown', VGSE()->textname); ?></option>
									<option value="html" <?php selected($column_settings['formatted_renderer'], 'html'); ?>/><?php _e('Unfiltered HTML', VGSE()->textname); ?></option>
								</select>
							</div>
							<div class="field-container field-container-width">
								<label><?php _e('Column width (pixels)', VGSE()->textname); ?></label>
								<input type="text" name="width" value="<?php echo $column_settings['width']; ?>" min="50" max="350"/>
							</div>

							<?php do_action('vg_sheet_editor/custom_columns/settings_page/after_template_fields', $columns); ?>
							<div class="field-container field-container-delete">
								<input data-repeater-delete type="button" value="<?php _e('Delete', VGSE()->textname); ?>" class="button"/>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php do_action('vg_sheet_editor/custom_columns/settings_page/before_form_submit'); ?>
			<input data-repeater-create type="button" value="<?php esc_attr_e('Add new column', VGSE()->textname); ?>" class="button add-column"/>
			<button class="button button-primary button-primary save"><?php _e('Save', VGSE()->textname); ?></button>


			<div class="mode"><input type="checkbox" class="mode-field" id="mode-field" value="yes"/> <label for="mode-field"><?php _e('Advanced mode', VGSE()->textname); ?></label></div>
		</form>

		<?php do_action('vg_sheet_editor/custom_columns/settings_page/after_content'); ?>
	</div>
</div>
			<?php
		