<?php
/**
 * Template used for the spreadsheet editor page in all post types.
 */
$nonce = wp_create_nonce('bep-nonce');

if (empty($current_post_type)) {
	$current_post_type = VGSE()->helpers->get_provider_from_query_string();
}
$editor = VGSE()->helpers->get_provider_editor($current_post_type);

if (!empty($_GET['wpse_load_rows_main_page']) && VGSE_DEBUG && current_user_can('manage_options')) {
	if (!defined('WPSE_PROFILE') && !empty($_GET['wpse_profile'])) {
		define('WPSE_PROFILE', true);
	}
	$rows = VGSE()->helpers->get_rows(array(
		'nonce' => $nonce,
		'post_type' => $current_post_type,
		'filters' => '',
		'wpse_source' => 'load_rows'
	));
	return;
}

$subtle_lock = in_array(date('Y-m-d'), array('2019-10-22', '2019-10-24', '2019-10-30')) ? true : false;
?>
<style>
	/*Hide all the wp-admin notices on the spreadsheet page to make it look cleaner*/
	/*We place the css here so it loads on the spreadsheet page regardless of the placement (wp-admin or frontend)*/
	.wp-core-ui .notice.is-dismissible, .wp-core-ui .notice, .woocommerce-message,
	.notice, div.error, div.updated {
		display: none !important;
	}	
</style>
<div class="remodal-bg highlightCurrentRow <?php if ($subtle_lock) echo 'vgse-subtle-lock'; ?>" id="vgse-wrapper" data-nonce="<?php echo $nonce; ?>">
	<div class="">
		<div class="sheet-header">
			<?php if (apply_filters('vg_sheet_editor/editor_page/allow_display_logo', true, $current_post_type)) { ?>
				<div class="sheet-logo-wrapper">
					<h2 class="hidden"><?php _e('Sheet Editor', VGSE()->textname); ?></h2>
					<a href="https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=editor-logo&utm_campaign=<?php echo $current_post_type; ?>" target="_blank" class="logo-link"><img src="<?php echo VGSE()->plugin_url; ?>assets/imgs/logo-248x102.png" class="vg-logo"></a>

					<?php if (is_admin() && apply_filters('vg_sheet_editor/editor_page/full_screen_mode_active', empty(VGSE()->options['be_disable_full_screen_mode_on']))) { ?>
						<div class="wpse-full-screen-notice">
							<div class="wpse-full-screen-notice-content notice-on"><?php _e('Full screen mode is active', VGSE()->textname); ?> <a href="#" class="wpse-full-screen-toggle"><?php _e('Exit', VGSE()->textname); ?></a> <a href="#" class="tipso tipso_style" data-tipso="<?php _e('You can deactivate this forever on the settings page', VGSE()->textname); ?>">( ? )</a></div>
							<div class="wpse-full-screen-notice-content notice-off"><a href="#" class="wpse-full-screen-toggle"><?php _e('Activate full screen', VGSE()->textname); ?></a></div>
						</div>
					<?php } ?>
					<?php do_action('vg_sheet_editor/editor_page/after_logo', $current_post_type); ?>
				</div>
			<?php } ?>
			<?php do_action('vg_sheet_editor/editor_page/before_toolbars', $current_post_type); ?>


			<!--Primary toolbar placeholder, used to keep its height when the toolbar is fixed when scrolling-->
			<div id="vg-header-toolbar-placeholder" class="vg-toolbar-placeholder"></div>
			<div id="vg-header-toolbar" class="vg-toolbar">

				<?php
				$secondary_toolbar_items_html = ($editor->args['toolbars']) ? $editor->args['toolbars']->get_rendered_provider_items($current_post_type, 'secondary') : '';
				if ($secondary_toolbar_items_html) {
					?>
					<!--Secondary toolbar-->
					<div class="vg-secondary-toolbar">
						<div class="vg-header-toolbar-inner">

							<?php
							echo $secondary_toolbar_items_html;
							do_action('vg_sheet_editor/toolbar/after_buttons', $current_post_type, 'secondary');
							?>

							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</div>
				<?php } ?>
				<!--Primary toolbar-->
				<div class="vg-header-toolbar-inner">

					<?php
					if ($editor->args['toolbars']) {
						echo $editor->args['toolbars']->get_rendered_provider_items($current_post_type, 'primary');
					}
					do_action('vg_sheet_editor/toolbar/after_buttons', $current_post_type, 'primary');
					?>

					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>

		</div>
		<div>
			<div id="responseConsole" class="console"><span class="be-total-rows"><?php _e('0 rows', VGSE()->textname); ?></span> <?php do_action('vg_sheet_editor/editor_page/after_console_text', $current_post_type); ?></div>
			<div class="vgse-current-filters"><?php _e('Active filters:', VGSE()->textname); ?> </div>

			<?php do_action('vg_sheet_editor/editor_page/before_spreadsheet', $current_post_type); ?>

			<!--Spreadsheet container-->
			<div id="post-data" data-post-type="<?php echo $current_post_type; ?>" class="be-spreadsheet-wrapper"></div>

			<div id="mas-data"></div>

			<!--Footer toolbar-->
			<div id="vg-footer-toolbar" class="vg-toolbar">
				<button name="mas" class="button"><i class="fa fa-chevron-down"></i> <?php _e('Load More Rows', VGSE()->textname); ?></button>  
				<button id="go-top" class="button"><i class="fa fa-chevron-up"></i> <?php _e('Go to the top', VGSE()->textname); ?></button>		
				<?php if (current_user_can('manage_options')) { ?>
					<a class="increase-rows-per-page" href="<?php echo esc_url(VGSE()->helpers->get_settings_page_url()); ?>" target="_blank"><?php _e('Increase rows per page', VGSE()->textname); ?></a> <a class="tipso tipso_style" data-tipso="<?php _e('We use pagination. By default we load 20 rows per page (every time you scroll down). You can increase the number to load more rows every time you scroll down.', VGSE()->textname); ?>" href="#">(?)</a>
				<?php } ?>
				<?php do_action('vg_sheet_editor/editor_page/after_footer_actions', $current_post_type); ?>
			</div>
		</div>

		<br>

	</div>

	<!--Image cells modal-->
	<div class="remodal" data-remodal-id="image" data-remodal-options="closeOnOutsideClick: false">

		<div class="modal-content">

		</div>
		<br>
		<button data-remodal-action="confirm" class="remodal-confirm"><?php _e('OK', VGSE()->textname); ?></button>
	</div>

	<!--handsontable cells modal-->
	<div class="remodal remodal8982 custom-modal-editor" data-remodal-id="custom-modal-editor" data-remodal-options="closeOnOutsideClick: false, hashTracking: false" style="max-width: 825px;">

		<div class="modal-content">
			<p class="custom-attributes-edit">
			<h3 class="modal-title-wrapper">
				<span class="modal-general-title"></span> 
			</h3>
			<p class="modal-description"></p>
			<button class="remodal-confirm save-changes-handsontable"><?php _e('Save changes', VGSE()->textname); ?></button>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></button>
			<div class="handsontable-in-modal" id="handsontable-in-modal"></div>
			<?php include 'editor-metabox-modal.php'; ?>

			<input type="hidden" value="<?php echo $nonce; ?>" name="nonce">
			<input type="hidden" value="" name="handsontable_modal_action">
			<input type="hidden" value="<?php echo $current_post_type; ?>" name="post_type">
		</div>
	</div>

	<!--Tinymce editor modal-->
	<div class="remodal remodal2 modal-tinymce-editor" data-remodal-id="editor" data-remodal-options="hashTracking: false, closeOnOutsideClick: false">

		<div class="modal-content">
			<h3 class="post-title-modal"><?php _e('Editing:', VGSE()->textname); ?> <span class="post-title"></span></h3>
			<?php
			$editor_id = 'editpost';
			wp_editor('', $editor_id);
			?>
			<span class="vgse-resize-editor-indicator vgse-tinymce-popup-indicators"><?php _e('You can resize the editor', VGSE()->textname); ?> <i class="fa fa-arrow-up"></i></span>
		</div>
		<br>
		<?php do_action('vg_sheet_editor/editor_page/tinymce/before_action_buttons'); ?>
		<button class="remodal-mover anterior remodal-secundario guardar-popup-tinymce"><i class="fa fa-chevron-left"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-save"></i></button><a href="#" class="tipso" data-tipso="<?php _e('Save changes and go to the previous post editor', VGSE()->textname); ?>">( ? )</a>
		<button class="remodal-confirm guardar-popup-tinymce" data-remodal-action="confirm"><i class="fa fa-save"></i></button><a href="#" class="tipso" data-tipso="<?php _e('Just save changes', VGSE()->textname); ?>">( ? )</a>
		<?php do_action('vg_sheet_editor/editor_page/tinymce/between_action_buttons'); ?>
		<button data-remodal-action="confirm" class="remodal-cancel"><i class="fa fa-close"></i></button><a href="#" class="tipso" data-tipso="<?php _e('Cancel the changes and close popup', VGSE()->textname); ?>">( ? )</a>
		<button class="siguiente remodal-secundario guardar-popup-tinymce"><i class="fa fa-save"></i>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></button><a href="#" class="tipso" data-tipso="<?php _e('Save changes and go to the next post editor', VGSE()->textname); ?>">( ? )</a>
		<?php do_action('vg_sheet_editor/editor_page/tinymce/after_action_buttons'); ?>
	</div>

	<!--Save changes modal-->
	<div class="remodal remodal5 bulk-save" data-remodal-id="bulk-save" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

		<div class="modal-content">
			<h2><?php _e('Save changes', VGSE()->textname); ?></h2>

			<!--Warning state-->
			<div class="be-saving-warning">
				<?php if (is_admin() && current_user_can('manage_options')) { ?>
					<p><?php _e('The changes about to be made are not reversible. You should backup your database before proceding.', VGSE()->textname); ?></p>
				<?php } else { ?>
					<p><?php _e('The changes about to be made are not reversible', VGSE()->textname); ?></p>
				<?php } ?>
				<button class="be-start-saving remodal-confirm primary"><?php _e('I understand, continue', VGSE()->textname); ?></button> <a href="#" class="remodal-cancel"><?php _e('Close', VGSE()->textname); ?></a>
			</div>

			<!--Start saving state-->
			<div class="bulk-saving-screen">
				<p class="saving-now-message"><?php _e('We are saving now. Don´t close this window until the process has finished.', VGSE()->textname); ?></p>
				<?php if (is_admin() && current_user_can('manage_options')) { ?>
					<p class="tip-saving-speed-message"><?php printf(__('<b>Tip:</b> The saving is too slow? <a href="%s" target="_blank">Save <b>more posts</b> per batch</a><br/>Are you getting errors when saving? <a href="%s" target="_blank">Save <b>less posts</b> per batch</a>', VGSE()->textname), VGSE()->helpers->get_settings_page_url(), VGSE()->helpers->get_settings_page_url()); ?></p>
				<?php } ?>
				<div id="be-nanobar-container"></div>

				<div class="response"></div>

				<!--Loading animation-->
				<div class="be-loading-anim">
					<div class="fountainG_1 fountainG"></div>
					<div class="fountainG_2 fountainG"></div>
					<div class="fountainG_3 fountainG"></div>
					<div class="fountainG_4 fountainG"></div>
					<div class="fountainG_5 fountainG"></div>
					<div class="fountainG_6 fountainG"></div>
					<div class="fountainG_7 fountainG"></div>
					<div class="fountainG_8 fountainG"></div>
				</div>
				<a href="#"  class="remodal-cancel hidden"><?php _e('Close', VGSE()->textname); ?></a>
			</div>


		</div>
		<br>
	</div>
	<!--Used for featured image previews-->
	<div class="vi-preview-wrapper"></div>

	<div class="wpse-stuck-loading"><?php _e('The loading is taking too long?<br>1. You can wait until the process finished.<br>2. You can <button class="" type="button">cancel the process.</button>', VGSE()->textname); ?></div>

	<?php do_action('vg_sheet_editor/editor_page/after_content', $current_post_type); ?>
</div>
			<?php
		