
<div data-remodal-id="modal-columns-visibility" data-remodal-options="closeOnOutsideClick: false" class="remodal remodal<?php echo $random_id; ?> modal-columns-visibility">

	<div class="modal-content">
		<?php if (!$partial_form) { ?>
			<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="POST" class="vgse-modal-form" data-nonce="<?php echo wp_create_nonce('bep-nonce'); ?>">
			<?php } ?>
			<h3><?php _e('Columns manager', VGSE()->textname); ?></h3>
			<ul class="unstyled-list">
				<li>
					<p><?php _e('Drag the columns to the left or right side to enable/disable them, drag them to the top or bottom to sort them, click on the "edit" button to rename them, click on the "x" button to delete them completely (only when they are disabled previously).', VGSE()->textname); ?></p> 

					<button class="button vgse-change-all-states" data-to="enabled"><?php _e('Enable all', VGSE()->textname); ?></button> - 
					<button class="button vgse-change-all-states" data-to="disabled"><?php _e('Disable all', VGSE()->textname); ?></button>

				</li>
				<li>
					<div class="vgse-sorter-section">

						<h3><?php _e('Enabled', VGSE()->textname); ?></h3>
						<ul class="vgse-sorter columns-enabled" id="vgse-columns-enabled">
							<?php
							if (empty($options[$post_type])) {
								$options[$post_type] = array();
							}
							if (empty($options[$post_type]['enabled'])) {
								$options[$post_type]['enabled'] = wp_list_pluck($filtered_columns, 'title', 'key');
							}
							foreach ($visible_columns as $column_key => $column) {
								if (in_array($column_key, $not_allowed_columns)) {
									continue;
								}
								if (isset($options[$post_type]['disabled']) && isset($options[$post_type]['disabled'][$column_key])) {
									continue;
								}
								if (!isset($column['title'])) {
									continue;
								}
								$title = $column['title'];
								?>
								<li><span class="handle">::</span> <span class="column-title" title="<?php echo esc_attr($title); ?>"><?php echo $title; ?></span>
									<input type="hidden" name="columns[]" class="js-column-key" value="<?php echo $column_key; ?>" />
									<input type="hidden" name="columns_names[]" class="js-column-title" value="<?php echo $title; ?>" />

									<?php if (current_user_can('manage_options')) { ?>
										<button class="remove-column column-action" title="<?php echo esc_attr(__('Remove column completely. If you want to use it later you can disable it by dragging and dropping to the right column', VGSE()->textname)); ?>"><i class="fa fa-remove"></i></button>
									<?php } ?>
									<button class="deactivate-column column-action" title="<?php echo esc_attr(__('Disable column. You can enable it later.', VGSE()->textname)); ?>"><i class="fa fa-arrow-right"></i></button>
									<button class="enable-column column-action" title="<?php echo esc_attr(__('Enable column', VGSE()->textname)); ?>"><i class="fa fa-arrow-left"></i></button>
									<?php do_action('vg_sheet_editor/columns_visibility/enabled/after_column_action', $column, $post_type); ?>
								</li>
							<?php }
							?>
						</ul>
					</div>
					<div class="vgse-sorter-section">
						<h3><?php _e('Disabled', VGSE()->textname); ?></h3>
						<ul class="vgse-sorter columns-disabled" id="vgse-columns-disabled"><?php
							if (isset($options[$post_type]['disabled'])) {
								foreach ($options[$post_type]['disabled'] as $column_key => $column_title) {
									if (in_array($column_key, $not_allowed_columns)) {
										continue;
									}
									if (is_object($editor->args['columns']) && $editor->args['columns']->is_column_blacklisted($column_key, $post_type)) {
										continue;
									}
									if (isset($columns[$column_key])) {
										$column_title = $columns[$column_key]['title'];
									}
									?>
									<li><span class="handle">::</span> <span class="column-title" title="<?php echo esc_attr($column_title); ?>"><?php echo $column_title; ?></span>  <i class="fa fa-refresh tipso tipso_style" data-tipso="<?php _e('Enabling this column requires a page reload', VGSE()->textname); ?>"></i>
										<input type="hidden" name="disallowed_columns[]" class="js-column-key" value="<?php echo esc_attr($column_key); ?>" />
										<input type="hidden" name="disallowed_columns_names[]" class="js-column-title" value="<?php echo esc_attr($column_title); ?>" />
										<?php if (current_user_can('manage_options')) { ?>
											<button class="remove-column column-action" title="<?php echo esc_attr(__('Remove column completely. If you want to use it later you can disable it by dragging and dropping to the right column', VGSE()->textname)); ?>"><i class="fa fa-remove"></i></button>
										<?php } ?>
										<button class="deactivate-column column-action" title="<?php echo esc_attr(__('Disable column. You can enable it later.', VGSE()->textname)); ?>"><i class="fa fa-arrow-right"></i></button>
										<button class="enable-column column-action" title="<?php echo esc_attr(__('Enable column', VGSE()->textname)); ?>"><i class="fa fa-arrow-left"></i></button>
										<?php do_action('vg_sheet_editor/columns_visibility/disabled/after_column_action', $column, $post_type); ?>
									</li>
									<?php
								}
							}
							?></ul>
					</div>
					<div class="clear"></div>
				</li>
				<?php if (is_admin() && current_user_can('manage_options')) { ?>
					<li class="missing-column-tips">					
						<h3><?php _e('A column is missing?', VGSE()->textname); ?></h3>
						<ul>
							<li><?php _e('- First, edit one item in the normal editor and fill all the fields manually.', VGSE()->textname); ?></li>
							<?php
							if (empty($options[$post_type]['enabled'])) {
								$options[$post_type]['enabled'] = array();
							}
							if (empty($options[$post_type]['disabled'])) {
								$options[$post_type]['disabled'] = array();
							}
							?>
							<li><?php _e('- We can scan the database, find new fields, and create columns automatically', VGSE()->textname); ?> <a class="tipso wpse-scan-db-link" href="<?php
								$rescan_url = ( $current_url ) ? add_query_arg(array('wpse_rescan_db_fields' => 1), $current_url) : add_query_arg(array('wpse_rescan_db_fields' => 1));
								echo esc_url($rescan_url);
								?>" data-tipso="<?php esc_attr_e('You can do this multiple times', VGSE()->textname); ?>"><?php _e('Scan Now', VGSE()->textname); ?></a></li>

							<?php
							if (class_exists('WP_Sheet_Editor_Custom_Columns') && VGSE()->helpers->is_editor_page()) {
								?>
								<li><?php _e('- If the previous solution failed, you can create new columns manually.', VGSE()->textname); ?> <a class="" href="<?php echo admin_url('admin.php?page=vg_sheet_editor_custom_columns'); ?>"><?php _e('Create column', VGSE()->textname); ?></a></li>
							<?php } ?>
							<li><?php _e('- Maybe you deleted the columns from the list.', VGSE()->textname); ?> <a class="vgse-restore-removed-columns" href="javascript:void(0)"><?php _e('Restore deleted columns', VGSE()->textname); ?></a></li>	
							<li><?php _e('- We can help you.', VGSE()->textname); ?> <a class="" target="_blank" href="<?php echo VGSE()->get_support_links('contact_us', 'url', 'sheet-missing-column'); ?>"><?php _e('Contact us', VGSE()->textname); ?></a></li>	
						</ul>
						</p>	
					</li>				
				<?php } ?>
				<li class="vgse-allow-save-settings">
					<label><input type="checkbox" value="yes" name="save_post_type_settings" class="save_post_type_settings" /> <?php _e('Save these settings for future sessions?', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="If you enable this option, we will use these settings the next time you load the editor for this post type.">( ? )</a></label>

				</li>

				<li class="vgse-save-settings">
					<?php if (!$partial_form) { ?>
						<button type="submit" class="remodal-confirm"><?php _e('Apply settings', VGSE()->textname); ?></button>
						<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
					<?php } ?>
				</li>
			</ul>
			<input type="hidden" value="<?php echo implode(',', $not_allowed_columns); ?>" class="not-allowed-columns" name="vgse_columns_disabled_all_keys">
			<input type="hidden" value="" class="all-allowed-columns" name="vgse_columns_enabled_all_keys">
			<?php if (!$partial_form) { ?>
				<input type="hidden" value="vgse_update_columns_visibility" name="action">
				<input type="hidden" value="<?php echo $nonce; ?>" name="nonce">
				<input type="hidden" value="<?php echo $post_type; ?>" name="post_type">
			<?php } ?>
			<input type="hidden" value="<?php echo $post_type; ?>" name="wpsecv_post_type">
			<input type="hidden" value="<?php echo $nonce; ?>" name="wpsecv_nonce">

			<?php if (!$partial_form) { ?>
			</form>
		<?php } ?>
	</div>
	<br>
</div>