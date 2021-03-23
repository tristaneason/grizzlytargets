<?php
if (!class_exists('WP_Sheet_Editor_Factory')) {

	class WP_Sheet_Editor_Factory {

		var $args = array();
		var $texts = array();
		var $current_columns = array();
		var $current_toolbars = array();
		var $provider = null;
		static $registered_menus = array();

		function __construct($args = array()) {
			$defaults = array(
				'enabled_post_types' => array(),
				'posts_per_page' => 10,
				'save_posts_per_page' => 4,
				'wait_between_batches' => 10,
				'fixed_columns_left' => 2,
				'provider' => 'post',
				'provider_key' => 'post_type',
				'columns' => '',
				'toolbars' => '',
				'admin_menu' => array(
					array(
						'type' => 'submenu',
						'name' => 'Edit Post',
						'slug' => 'vgse-bulk-edit-post',
						'icon' => null,
					),
				),
				'texts' => array(),
			);
			$this->args = wp_parse_args($args, $defaults);
			$this->provider = VGSE()->helpers->get_data_provider($this->args['provider']);

			add_action('admin_menu', array($this, 'register_menu'));

			// When we bootstrap 2 separate spreadsheets for different post types,
			// if the current sheet loaded is for a post type not enabled in this bootstrap config, 
			// don't bootstrap to avoid loading two spreadsheets in the same page
			if (!defined('WPSE_DISABLE_DOUBLE_SHEET_PROTECTION') && !in_array($this->args['provider'], $this->args['enabled_post_types'])) {
				return;
			}

			$this->texts = array(
				'import_failed_server_error_tips' => sprintf(__('The last import batch failed due to a server error, it\'s more likely that the server got overloaded.<br>1- You can try <a href="%s" target="_blank">importing fewer rows</a> per batch (i.e. import 2 rows every few seconds).<br>2- You can start a new import, sometimes trying again works (use the "advanced settings" in the step 1 of the import to start from a specific row).<br>3- You can increase the php memory <a href="%s" target="_blank">following this tutorial</a><br>4- If the problem happens after trying with 1 row per batch, you can <a href="%s" target="_blank">contact us</a> and we will make it work for you', VGSE()->textname), VGSE()->helpers->get_settings_page_url(), 'https://docs.woocommerce.com/document/increasing-the-wordpress-memory-limit/', VGSE()->get_support_links('contact_us', 'url', 'import-server-error')),
				'import_failed_retry_server_error' => __('Your server was not able to process this batch. Do you want to try again? You can retry 3 times, If 3 attempts fail we will stop the import completely.', VGSE()->textname),
				'import_data_issue_correct_restart' => __('Please correct the error in the file and start a new import. You can use the "Advanced options" in the step 1 of the import to start from this specific row.', VGSE()->textname),
				'product_without_variations' => __('The selected product does not have variations', VGSE()->textname),
				'empty' => __('empty', VGSE()->textname),
				'column_for_variations_only' => ( empty(VGSE()->options['hide_cell_comments']) ) ? __('This column is only for variation rows, parent products don\'t use this field', VGSE()->textname) : '',
				'formulas_starting_edit_single_field' => __('<b>Editing the field: {field_label}</b>', VGSE()->textname),
				'column_not_found' => __('Column not found. Try with another search criteria.', VGSE()->textname),
				'column_for_parent_products_only' => ( empty(VGSE()->options['hide_cell_comments']) ) ? __('This column is for parent products only, variations don\'t use this field', VGSE()->textname) : '',
				'how_to_paste' => __('Paste using keyboard: Ctrl+V', VGSE()->textname),
				'realign_cells' => __('Realign cells', VGSE()->textname),
				'remove_all_filters' => __('Remove all filters', VGSE()->textname),
				'auto_resize_columns' => __('Resize columns based on the values', VGSE()->textname),
				'delete_row' => __('Delete row', VGSE()->textname),
				'confirm_delete_row' => __('We will delete the selected row(s) from the database completely. If you want to restore them later, you should make a backup before. Do you want to delete them?', VGSE()->textname),
				'duplicate_row' => __('Duplicate row', VGSE()->textname),
				'hide_column' => __('Hide column', VGSE()->textname),
				'create_variations' => __('Create variations', VGSE()->textname),
				'copy_variations' => __('Copy variations from this product', VGSE()->textname),
				'enter_column_name' => __('Rename column', VGSE()->textname),
				'delete_meta_key' => __('Delete field', VGSE()->textname),
				'delete_meta_key_confirmation' => __('We will delete this meta field from the database and you will lose the values saved in this field on all the rows. You should make a backup to be able to restore in the future. Do you want to continue with the deletion?', VGSE()->textname),
				'edit_meta_key' => __('Edit meta key', VGSE()->textname),
				'new_value_empty_or_equal' => __('Error: The new value is empty or is equal to the old value', VGSE()->textname),
				'last_session_filters_notice' => __('Showing rows from your last session.', VGSE()->textname),
				'export_column' => __('Export column', VGSE()->textname),
				'formula_execution_failed' => __('<p>The bulk edit was not applied completely. The process was canceled due to an error.</p><p>You can close this window.</p>', VGSE()->textname),
				'process_execution_failed' => __('<p>The process did not finish. The process was canceled due to an error.</p><p>You can close this window.</p>', VGSE()->textname),
				'formula_retry_batch' => __('Your server was not able to process this batch. Do you want to try again?', VGSE()->textname),
				'formula_execution_complete' => __('The bulk edit was executed successfully. You can close this window', VGSE()->textname),
				'open_columns_visibility' => __('Add new column', VGSE()->textname),
				'confirm_column_reload_page' => __('ENABLE COLUMNS. These columns require a page reload: {columns}. ¿Do you want to reload now? We will reload automatically', VGSE()->textname),
				'column_removed' => __('Column removed. Go to "settings > hide/display columns" to enable it again', VGSE()->textname),
				'save_changes_before_remove_filter' => __('You have modified posts. Please save the changes because we will refresh the spreadsheet.', VGSE()->textname),
				'save_changes_before_remove_column' => __('You have modified rows. Please save the changes before removing columns from the spreadsheet.', VGSE()->textname),
				'save_changes_before_we_reload' => __('You have modified posts. Please save the changes because we will refresh the spreadsheet. Do you want to refresh now?', VGSE()->textname),
				'save_changes_reload_optional' => __('Some rows were modified in the background. Please save the changes and reload the spreadsheet to see the changes', VGSE()->textname),
				'no_rows_for_formula' => __("We didn't find rows to update from the search query. Please try another search query.", VGSE()->textname),
				'settings_moved_submenu' => __('You can find all the settings here, like columns visibility, etc.', VGSE()->textname),
				'posts_not_found' => __('Oops, nothing found', VGSE()->textname),
				'add_posts_here' => __('You can create new items here', VGSE()->textname),
				'use_other_image' => __('Upload image', VGSE()->textname),
				'view_image' => __('View Gallery', VGSE()->textname),
				'no_options_available' => __('No options available', VGSE()->textname),
				'posts_loaded' => __('Items loaded in the spreadsheet', VGSE()->textname),
				'new_rows_added' => __('New rows added', VGSE()->textname),
				'formula_applied' => __('The bulk edit has been executed. ¿Do you want to reload the page to see the changes?', VGSE()->textname),
				'saving_stop_error' => __('<p>The changes were not saved completely. The process was canceled due to an error .</p><p>You can close this popup.</p>', VGSE()->textname),
				'paged_batch_saved' => __('{updated} items saved of {total} items that need saving.', VGSE()->textname),
				'everything_saved' => __('All items have been saved.', VGSE()->textname),
				'save_changes_on_leave' => __('Please check if you have unsaved changes. If you have, please save them or they will be dismissed.', VGSE()->textname),
				'no_changes_to_save' => __('Everything is already saved.', VGSE()->textname),
				'http_error_400' => __('The server did not accept our request. Bad request, please try refresh the page and try again.', VGSE()->textname),
				'http_error_403' => __('The server didn´t accept our request. You don´t have permission to do this action. Please log in again.', VGSE()->textname),
				'http_error_500_502_505' => __('The server is not available or overloaded. Please try again later.', VGSE()->textname),
				'http_error_try_now' => __('The server is not available or overloaded. Do you want to try again?', VGSE()->textname),
				'http_error_503' => __('The server wasn´t able to process our request. Server error. Please try again later.', VGSE()->textname),
				'http_error_509' => __('The server has exceeded its allocated resources and is not able to process our request.', VGSE()->textname),
				'http_error_504' => __('The server is busy and took too long to respond to our request. Please try again later.', VGSE()->textname),
				'http_error_default' => __('The server could not process our request. Please try again later.', VGSE()->textname),
			);


			// If is spreadsheet page and this $editor is not related to the active spreadsheet, don't initialize it
			$current_provider_in_page = VGSE()->helpers->get_provider_from_query_string();
			if (VGSE()->helpers->is_editor_page() && !in_array($current_provider_in_page, $this->args['enabled_post_types'])) {
				return;
			}

			do_action('vg_sheet_editor/editor/before_init', $this);

			$this->current_columns = $this->args['columns']->get_provider_items($this->args['provider'], false);
			$this->current_toolbars = $this->args['toolbars']->get_provider_items($this->args['provider'], null);

			// @todo Hook to the filter before registering columns
			add_filter('vg_sheet_editor/columns/can_add_item', array($this, 'disallow_file_uploads_for_some_users'), 10, 3);

			add_action('admin_head', array($this, 'add_editor_settings_to_header'));
			add_action('vg_sheet_editor/render_editor_js_settings', array($this, 'add_editor_settings_to_header'));

			add_action('admin_enqueue_scripts', array($this, 'remove_conflicting_assets'), 99999999);
			add_action('admin_print_styles', array($this, 'remove_conflicting_assets'), 99999999);

			VGSE()->editors[VGSE()->helpers->get_data_provider_class_key($this->args['provider'])] = & $this;

			if (empty(VGSE()->options['be_disable_heartbeat'])) {
				add_filter('heartbeat_settings', array($this, 'limit_heartbeat_on_spreadsheet'));
			}
		}

		function limit_heartbeat_on_spreadsheet($settings) {
			$settings['interval'] = 120;
			return $settings;
		}

		function remove_conflicting_assets() {

			$pages_to_load_assets = VGSE()->frontend_assets_allowed_on_pages();
			if (empty($_GET['page']) ||
					!in_array($_GET['page'], $pages_to_load_assets)) {
				return;
			}

			$this->_remove_conflicting_assets();
		}

		function _remove_conflicting_assets() {
			$remove = array(
				'select2',
				'woocommerce-shop-as-customer-select-2-script',
				'tribe-select2',
				'wc-admin-meta-boxes',
				'woocommerce_settings',
				'wc-enhanced-select',
				'wc-shipping-zones',
				'woocommerce-shop-as-customer',
				'woocommerce_admin_styles',
				'woocommerce_admin',
				'jquery-chosen',
				'wa-wps-admin-script',
				'edd-admin-scripts',
				'chosen-drop-down',
				'csf-plugins',
				'martfury-shortcodes',
				'wc-admin-order-meta-boxes',
				'ced_wovpe_js',
				'ced-wovpr-custom-script',
				'datepicker-style',
				'iccategoryjs'
			);


			if (!empty(VGSE()->options['be_disable_heartbeat'])) {
				$remove[] = 'heartbeat';
			}

			foreach ($remove as $handle) {
				wp_dequeue_style($handle);
				wp_deregister_style($handle);
				wp_dequeue_script($handle);
				wp_deregister_script($handle);
			}
		}

		/**
		 * Render editor page
		 */
		function render_editor_page() {
			if (!current_user_can('edit_posts')) {
				wp_die(__('You dont have enough permissions to view this page.', VGSE()->textname));
			}

			require VGSE_DIR . '/views/editor-page.php';
		}

		function get_editor_settings($current_provider_in_page) {

			$spreadsheet_columns = $this->args['columns']->get_provider_items($this->args['provider'], true);
			$columns = array();
			$titles = array();
			$columsFormat = array();
			$columsUnformat = array();

			if (!empty($spreadsheet_columns)) {
				$columns = wp_list_pluck($spreadsheet_columns, 'column_width', 'key');
				$titles = wp_list_pluck($spreadsheet_columns, 'title', 'key');
				$columsFormat = wp_list_pluck($spreadsheet_columns, 'formatted');
				$columsUnformat = wp_list_pluck($spreadsheet_columns, 'unformatted');
			}
			$unfiltered_original_columns = $this->args['columns']->get_items(true);
			$unfiltered_original_columns[$current_provider_in_page] = $this->args['columns']->_remove_callbacks_on_items($unfiltered_original_columns[$current_provider_in_page]);

			// We need to reduce the size of this array, so we remove all empty elements and internal objects
			$all_spreadsheet_columns_settings = VGSE()->helpers->array_remove_empty(wp_parse_args($spreadsheet_columns, $unfiltered_original_columns[$current_provider_in_page]));
			foreach ($all_spreadsheet_columns_settings as $column_key => $column_settings) {
				foreach ($column_settings as $setting_key => $setting_value) {
					if (is_object($setting_value) || in_array($setting_key, array('get_value_callback', 'save_value_callback'))) {
						unset($all_spreadsheet_columns_settings[$column_key][$setting_key]);
					}
				}
			}

			$settings = array(
				'user_has_saved_sheet' => (int) get_user_meta(get_current_user_id(), 'wpse_has_saved_sheet', true),
				'tinymce_cell_template' => VGSE()->helpers->get_tinymce_cell_content(),
				'gutenberg_cell_template' => VGSE()->helpers->get_gutenberg_cell_content(),
				'handsontable_cell_template' => '<a class="button button-handsontable button-custom-modal-editor" data-existing="{value}" data-modal-settings="{modal_settings}"><i class="fa fa-edit"></i> {button_label}</a>',
				'lock_cell_template' => '<i class="fa fa-lock vg-cell-blocked vg-readonly-lock"></i> {value}',
				'lock_cell_template_pro' => '<i class="fa fa-lock vg-cell-blocked vg-premium-column"></i> {value} <a href="' . VGSE()->get_buy_link('sheet-locked-column-{post_type}') . '" target="_blank" class="vg-premium-column-link">(' . __('Pro', VGSE()->textname) . ')</a>',
				'enable_lock_cell_template' => '<i class="fa fa-lock vg-cell-blocked vg-safety-lock"></i> {value} <a href="#" class="wpse-enable-locked-cell">(' . __('Enable', VGSE()->textname) . ')</a>',
				'lockedColumnsManuallyEnabled' => array(),
				'startRows' => 0,
				'startCols' => !empty($this->current_columns) ? count($this->current_columns) : 0,
				'colWidths' => !empty($columns) ? array_map('intval', $columns) : array(),
				'colHeaders' => $titles,
				'columnsUnformat' => ($columsUnformat),
				'columnsFormat' => ($columsFormat),
				'custom_handsontable_args' => array(),
				'debug' => (!empty(VGSE()->options['be_disable_cells_lazy_loading'])) ? true : null,
				'disable_automatic_loading_rows' => (!empty(VGSE()->options['be_disable_automatic_loading_rows'])) ? true : false,
				'watch_cells_to_lock' => false,
				'final_spreadsheet_columns_settings' => $all_spreadsheet_columns_settings,
				'post_type' => $current_provider_in_page,
				'woocommerce_product_post_type_key' => apply_filters('vg_sheet_editor/woocommerce/product_post_type_key', 'product'),
				'rest_nonce' => wp_create_nonce('wp_rest'),
				'rest_base_url' => rest_url(),
				'taxonomy_terms_separator' => (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',',
				'export_page_size' => (!empty(VGSE()->options['export_page_size']) ) ? (int) VGSE()->options['export_page_size'] : 100,
				'is_editor_page' => VGSE()->helpers->is_editor_page(),
				'is_premium' => VGSE()->helpers->get_plugin_mode() === 'pro-plugin',
				'is_post_type' => $this->provider->is_post_type,
				'is_administrator' => current_user_can('manage_options')
			);

			$all_settings = wp_parse_args($settings, $this->args);

			// We add this text late because VGSE_ANY_PREMIUM_ADDON is set after all plugins are loaded
			$this->texts['hint_missing_column_on_scroll'] = ( defined('VGSE_ANY_PREMIUM_ADDON') && VGSE_ANY_PREMIUM_ADDON && current_user_can('manage_options') && is_admin()) ? __('<h3>Missing column?</h3><button class="button show-column-missing-tips"  data-remodal-target="modal-columns-visibility">Open columns manager</button> or <button class="button">Close this</button>', VGSE()->textname) : '';

			$extension = VGSE()->helpers->get_extension_by_post_type($current_provider_in_page);
			$review_tip_dismissed = (bool) get_option('vgse_dismiss_review_tip');
			$this->texts['ask_review'] = ( VGSE()->helpers->is_happy_user() && $extension && !$review_tip_dismissed && !empty($extension['wp_org_slug'])) ? sprintf(__('<span class="review-tip">Do we deserve a 5-star review? <a href="%s" target="_blank" class="dismiss-review-tip">Yes, you deserve it</a> . - . <a href=""  class="dismiss-review-tip">No</a></span>', VGSE()->textname), 'https://wordpress.org/support/plugin/' . $extension['wp_org_slug'] . '/reviews/?filter=5#new-post') : '';

			$all_settings['texts'] = wp_parse_args($all_settings['texts'], $this->texts);

			if (!empty($all_settings['fixed_columns_left'])) {
				$all_settings['custom_handsontable_args']['fixedColumnsLeft'] = $all_settings['fixed_columns_left'];
			}
			$all_settings['custom_handsontable_args'] = json_encode(apply_filters('vg_sheet_editor/handsontable/custom_args', $all_settings['custom_handsontable_args'], $this->args['provider']), JSON_FORCE_OBJECT);
			$final_settings = apply_filters('vg_sheet_editor/js_data', $all_settings, $current_provider_in_page);
			return $final_settings;
		}

		function add_editor_settings_to_header() {
			if (!VGSE()->helpers->is_editor_page()) {
				return;
			}

			$current_provider_in_page = VGSE()->helpers->get_provider_from_query_string();
			if (!in_array($current_provider_in_page, $this->args['enabled_post_types'])) {
				return;
			}
			$final_settings = $this->get_editor_settings($current_provider_in_page);
			?>
			<script>
				var vgse_editor_settings = <?php echo json_encode($final_settings); ?>
			</script>
			<?php
		}

		function disallow_file_uploads_for_some_users($allowed, $key, $args) {

			if (in_array($args['type'], array('boton_gallery', 'boton_gallery_multiple')) && !current_user_can('upload_files')) {
				return false;
			}
			return $allowed;
		}

		/**
		 * Register admin pages
		 */
		function register_menu() {

			if (empty($this->args['admin_menu'])) {
				return;
			}

			foreach ($this->args['admin_menu'] as $admin_menu) {

				if (!empty($admin_menu['treat_as_url'])) {
					$render_callback = null;
				} else {
					$render_callback = array($this, 'render_editor_page');
				}

				if ($admin_menu['type'] === 'submenu') {
					if (empty($admin_menu['parent'])) {
						$admin_menu['parent'] = 'vg_sheet_editor_setup';
					}
					add_submenu_page($admin_menu['parent'], $admin_menu['name'], $admin_menu['name'], 'edit_posts', $admin_menu['slug'], $render_callback);
				} else {

					if (empty($admin_menu['icon'])) {
						$admin_menu['icon'] = null;
					}
					add_menu_page($admin_menu['name'], $admin_menu['name'], 'edit_posts', $admin_menu['slug'], $render_callback, $admin_menu['icon']);
				}
			}
		}

	}

}