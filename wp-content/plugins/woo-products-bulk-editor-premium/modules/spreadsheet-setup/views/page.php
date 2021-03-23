<?php
/**
 * Template used for the post type setup page.
 */
$nonce = wp_create_nonce('bep-nonce');
?>
<div class="remodal-bg quick-setup-page-content post-type-setup-wizard" id="vgse-wrapper" data-nonce="<?php echo $nonce; ?>">
	<div class="">
		<div class="">
			<h2 class="hidden"><?php _e('Sheet Editor', VGSE()->textname); ?></h2>
			<a href="https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=spreadsheet-setup-logo" target="_blank"><img src="<?php echo VGSE()->plugin_url; ?>assets/imgs/logo-248x102.png" class="vg-logo"></a>
		</div>
		<h2><?php _e('Set up Spreadsheet', VGSE()->textname); ?></h2>
		<div class="setup-screen">
			<?php do_action('vg_sheet_editor/post_type_setup_page/quick_setup_screen/before_content'); ?>

			<p><?php _e('You can start using the spreadsheet editor in just 5 minutes. Please follow these steps.', VGSE()->textname); ?></p>

			<?php
			ob_start();
			require VGSE_DIR . '/views/post-types-form.php';
			$post_types_form = str_replace(array(
				'type="checkbox"',
				'name="append" value="no"',
				'button button-primary hidden save-trigger',
				'<form',
					), array(
				'type="radio"',
				'name="append" value="yes"',
				'button button-primary save-trigger',
				'<form data-callback="vgsePostTypeSetupPostTypesSaved" data-custom-post-types="' . $custom_post_types . '" data-confirm-delete="' . __('Are you sure you want to delete the post type? You will delete the posts in the post type as well', VGSE()->textname) . '" ',
					), ob_get_clean());
			$steps = array();

			$steps['enable_post_types'] = '<p>' . __('Select the information that you want to edit with the spreadsheet editor.', VGSE()->textname) . '</p>';

			if (class_exists('WP_Sheet_Editor_CPTs')) {
				$steps['enable_post_types'] .= '<form class="inline-add" action="' . admin_url('admin-ajax.php') . '" method="POST" data-callback="vgsePostTypeSaved"><input type="hidden" name="action" value="vgse_register_post_type" /><input type="hidden" name="nonce" value="' . $nonce . '" /><input type="text" class="vgse-new-post-type" name="post_type" placeholder="' . __('Add new post type', VGSE()->textname) . '"/><button class="button"><i class="fa fa-plus"></i></button></form> ';
			}
			$steps['enable_post_types'] .= $post_types_form;


			// Columns visibility section
			if (class_exists('WP_Sheet_Editor_Columns_Visibility')) {
				$steps['setup_columns'] = '<div class="inline-add"><form class="inline-add" action="' . admin_url('admin-ajax.php') . '" method="POST" data-callback="vgsePostTypeSetupColumnSaved"><input type="hidden" name="action" value="vgse_post_type_setup_save_column" /><input type="hidden" name="nonce" value="' . $nonce . '" /><label>' . __('Add new column', VGSE()->textname) . ' <small><a href="' . admin_url('admin.php?page=vg_sheet_editor_custom_columns') . '" target="_blank">' . __('Advanced settings') . '</a></small></label><input type="text" class="vgse-new-column" name="label" placeholder="' . __('Label', VGSE()->textname) . '"/><input type="text" class="vgse-new-column" name="key" placeholder="' . __('Key', VGSE()->textname) . '"/><button class="button"><i class="fa fa-plus"></i></button></form></div> ';
			}


			$steps = apply_filters('vg_sheet_editor/post_type_setup_page/setup_steps', $steps);

			if (!empty($steps)) {
				echo '<ol class="steps">';
				foreach ($steps as $key => $step_content) {
					?>
					<li class="<?php echo $key; ?>"><?php echo $step_content; ?></li>		
					<?php
				}

				echo '</ol>';
			}
			?>

			<?php do_action('vg_sheet_editor/post_type_setup_page/quick_setup_screen/after_content'); ?>
		</div>

		<?php do_action('vg_sheet_editor/post_type_setup_page/after_content'); ?>
	</div>
</div>
			<?php
		