<?php

/**
  ReduxFramework Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */
if (!class_exists('WP_Sheet_Editor_Redux_Setup')) {

	class WP_Sheet_Editor_Redux_Setup {

		public $args = array();
		public $sections = array();
		public $pts;
		public $ReduxFramework;

		public function __construct() {

			if (!class_exists('ReduxFramework')) {
				return;
			}

			$this->initSettings();
		}

		public function initSettings() {


			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if (!isset($this->args['opt_name'])) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			add_action('redux/loaded', array($this, 'remove_demo'));


			add_action('redux/page/' . $this->args['opt_name'] . '/enqueue', array($this, 'add_custom_css_to_panel'));

			$this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
		}

		function add_custom_css_to_panel() {
			wp_register_style(
					'vgse-redux-custom-css', ( VGSE_DEBUG ) ? VGSE()->plugin_url . 'assets/css/reduxframework.css' : VGSE()->plugin_url . 'assets/css/styles.min.css', array('redux-admin-css'), // Be sure to include redux-admin-css so it's appended after the core css is applied
					time(), 'all'
			);
			wp_enqueue_style('vgse-redux-custom-css');
		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if (class_exists('ReduxFrameworkPlugin')) {
				remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
			}
		}

		public function setSections() {

			$helpers = WP_Sheet_Editor_Helpers::get_instance();
			$this->sections[] = array(
				'icon' => 'el-icon-cogs',
				'title' => __('General settings', VGSE()->textname),
				'fields' => array(
					array(
						'id' => 'info_normal_234343',
						'type' => 'info',
						'desc' => __('In this page you can quickly set up the spreadsheet editor. This all you need to use the editor. The settings on the other tabs are completely optional and allow you to tweak the performance of the editor among other things.', VGSE()->textname),
					),
					array(
						'id' => 'be_post_types',
						'type' => 'select',
						'title' => __('Post Types', VGSE()->textname),
						'desc' => __('On which post types do you want to enable the editor?', VGSE()->textname),
						'options' => $helpers->get_allowed_post_types(),
						'multi' => true,
						'default' => 'post',
					),
					array(
						'id' => 'be_posts_per_page',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('How many rows do you want to display on the spreadsheet?', VGSE()->textname),
						'desc' => __('We use pagination to use few server resources. We load 20 rows first and load 20 more every time you scroll down. You can increase this number to load more rows per page. CAREFUL. Loading more than 200 rows per page might overload your server. If we detect that the server is overloaded we will automatically reset to 10 rows per page.', VGSE()->textname),
						'default' => 20,
					),
					array(
						'id' => 'be_load_items_on_scroll',
						'type' => 'switch',
						'title' => __('Load more items on scroll?', VGSE()->textname),
						'desc' => __('When this is enabled more items will be loaded to the bottom of the spreadsheet when you reach the end of the page. You can enable / disable in the spreadsheet too.', VGSE()->textname),
						'default' => true,
					),
			));

			$this->sections[] = array(
				'icon' => 'el-icon-plane',
				'title' => __('Advanced', VGSE()->textname),
				'fields' => array(
					array(
						'id' => 'export_page_size',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('Export batch size', VGSE()->textname),
						'desc' => __('Here you can control the batch size for the exports. If you use a high number the exports will finish faster. You can use a high number safely because we automatically fall back to a lower number if the server is overloaded during one export. For example, export 100 rows per batch and complete the exports super fast and if we detect slowness in one export we will automatically restart the export with 10 rows per batch', VGSE()->textname),
						'default' => 100,
					),
					array(
						'id' => 'be_posts_per_page_save',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('How many posts do you want to save per batch?', VGSE()->textname),
						'desc' => __('When you edit a large amount of posts in the spreadsheet editor we can´t save all the changes at once, so we do it in batches. The recommended value is 4 , which means we will process only 4 posts at once. You can adjust it as it works best for you. If you get errors when saving you should lower the number', VGSE()->textname),
						'default' => 4,
					),
					array(
						'id' => 'be_timeout_between_batches',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('How long do you want to wait between batches? (in seconds)', VGSE()->textname),
						'desc' => __('When you edit a large amount of posts in the spreadsheet editor we can´t save all the changes at once, so we do it in batches. But your server can´t handle all the batches one after another so we need to wait a few seconds after every batch to give your server a little break. The recommended value is 6 seconds, you can adjust it as it works best for you. If you get errors when saving you should increase the number to give your server a longer break after each batch', VGSE()->textname),
						'default' => 6,
					),
					array(
						'id' => 'be_disable_post_actions',
						'type' => 'switch',
						'title' => __('Disable post actions while saving?', VGSE()->textname),
						'desc' => __('Some plugins execute a task after a post is created or updated. For example, there are plugins that share your new posts on your social profiles, other plugins that notify users after a post is updated, etc. There might be an issue with those plugins. For example, if you use a plugin that shares your new posts on your twitter account and update 100 posts in the spreadsheet editor you might end up with 100 tweets shared in your twitter account. So enable this option if you want to update / create posts silently without executing those functions.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_fix_first_columns',
						'type' => 'switch',
						'title' => __('Freeze first 2 columns at the left side?', VGSE()->textname),
						'desc' => __('When this is enabled the first 2 columns will always be visible while scrolling horizontally. You can right click on any column to freeze or unfreeze it.', VGSE()->textname),
						'default' => true,
					),
					array(
						'id' => 'be_disable_cells_lazy_loading',
						'type' => 'switch',
						'title' => __('Disable cells lazy loading?', VGSE()->textname),
						'desc' => __('The spreadsheet loads only the "visible rows" for performance reasons, so when you scroll up or down the rows are loaded dynamically. This way you can "open" thousands of posts in the spreadshet and it will work fast. However, if you want to use the browser search to find a specific cell, you need to disable the lazy loading in order to load all the rows at once and the browser will be able to find the cells. The browser search doesn´t work by default because only the "visible rows" are actually created.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_initial_rows_offset',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('Initial rows offset', VGSE()->textname),
						'desc' => __('When you have 1000 posts , you might want to open the spreadsheet and start editing from post 200. This option lets you skip a lot of rows. IMPORTANT. We use the pagination, so we will display the page closest to that number. For example. If you load 10 rows per page and enter 1205 as offset, the sheet will start from page 120 (index 1200) because it is the page closest to the defined offset.', VGSE()->textname),
						'default' => 0,
					),
					array(
						'id' => 'be_disable_dashboard_widget',
						'type' => 'switch',
						'title' => __('Disable usage stats widget?', VGSE()->textname),
						'desc' => __('If you enable this option, the usage stats widget shown in the wp-admin dashboard will be removed.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_suspend_object_cache_invalidation',
						'type' => 'switch',
						'title' => __('Suspend object cache invalidation?', VGSE()->textname),
						'desc' => __('Disable this if you are using a object/database cache plugin. We disable this by default to make the saving faster, when you edit a lot of posts WordPress tries to "clean up" the cache even if you are not using a cache plugin, making hundreds of unnecessary database queries.', VGSE()->textname),
						'default' => !defined('WP_CACHE') || !WP_CACHE,
					),
					array(
						'id' => 'be_taxonomy_terms_separator',
						'type' => 'text',
						'title' => __('Separator for taxonomy terms cells', VGSE()->textname),
						'desc' => __('Taxonomy columns like post categories, post tags, etc. show terms separated by comma, you can change the separator character if you use commas in your term names.', VGSE()->textname),
						'default' => ',',
					),
					array(
						'id' => 'be_disable_serialized_columns',
						'type' => 'switch',
						'title' => __('Disable serialized columns support?', VGSE()->textname),
						'desc' => __('The spreadsheet automatically generates columns for serialized fields, but this can use a lot of CPU cycles depending on the number of serialized fields. You can disable this feature if the sheet is too slow to load or you get errors when loading the rows or you dont want to see columns with prefix "SEIS".', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_disable_automatic_loading_rows',
						'type' => 'switch',
						'title' => __('Disable the automatic loading of rows?', VGSE()->textname),
						'desc' => __('When you open the spreadsheet, we load the rows automatically so you can start editing right away. Activate this option if you want to search rows and load manually.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_columns_limit',
						'type' => 'text',
						'validate' => 'numeric',
						'title' => __('Columns limit', VGSE()->textname),
						'desc' => __('We limit the spreadsheet columns for performance reasons to avoid loading thousands of columns on the spreadsheet. You can increase this limit if you want to display more columns. Default: 310', VGSE()->textname),
						'default' => 310,
					),
					array(
						'id' => 'be_disable_wpautop',
						'type' => 'switch',
						'title' => __('Disable the replacement of line breaks with p tags?', VGSE()->textname),
						'desc' => __('When the sheet loads and saves post content, we run it through wpautop to prevent issues with line breaks. You can disable this if you dont want to see/save the p tags in the content.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_disable_full_screen_mode_on',
						'type' => 'switch',
						'title' => __('Disable the full screen mode?', VGSE()->textname),
						'desc' => __('When the sheet loads, we open it in full screen and you have the option to exit the full screen mode. Activate this option and we wont open the sheet in full screen.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_disable_heartbeat',
						'type' => 'switch',
						'title' => __('Disable the heartbeat api in the spreadsheet?', VGSE()->textname),
						'desc' => __('WordPress uses the heartbeat API to check the login status every few seconds. This can overload your server because it could make hundreds of requests when you are editing in the spreadsheet. You can disable it to reduce the stress on your server while editing in the sheet. However, if you keep the spreadsheet opened over multiple days your login session can expire and you wont be notified if you disable the heartbeat and this can cause issues while saving. So use this option only when you use the spreadsheet for a few hours only.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_rest_api_only',
						'type' => 'switch',
						'title' => __('Deactivate everything. Use the REST API only', VGSE()->textname),
						'desc' => __('If you activate this option we will deactivate all the spreadsheets, settings pages, and the entire plugin will become invisible to the user. Only the REST API will remain active. This is useful for advanced scenarios when you only use our REST API to keep websites synchronized with external spreadsheets or systems. When this option is active, our settings page will moved under the general settings menu because our sheet editor menu will be removed', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_disable_data_prefetch',
						'type' => 'switch',
						'title' => __('Deactivate the data prefetch', VGSE()->textname),
						'desc' => __('When you load the spreadsheet, we get all the columns at once from the database to make it faster, this is called prefetch. This can cause issues if you have thousands of columns or rare database setups.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_allowed_user_roles',
						'type' => 'select',
						'multi' => true,
						'title' => __('User roles that can use the spreadsheet editor', VGSE()->textname),
						'desc' => __('The plugin will not initialize for the user roles not selected here.', VGSE()->textname),
						'data' => 'roles',
					),
					array(
						'id' => 'be_enable_fancy_taxonomy_cell',
						'type' => 'switch',
						'title' => __('Enable the fancy taxonomy terms selector', VGSE()->textname),
						'desc' => __('The taxonomy columns (i.e. categories, tags) use a limited dropdown by default for selecting one term only and the column can be copy pasted. We have a fancy dropdown selector, which has better auto complete, allows selecting multiple options, etc. but it uses more server resources and it doesn\'t allow copy pasting in these cells. You can activate it here if you prefer a better dropdown over the ability to copy paste.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'be_enable_rest_api',
						'type' => 'switch',
						'title' => __('Enable the REST API', VGSE()->textname),
						'desc' => __('The REST API can be used for interacting with our spreadsheet from external apps.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'delete_attached_images_when_post_delete',
						'type' => 'switch',
						'title' => __('Delete the attached images when deleting a post?', VGSE()->textname),
						'desc' => __('For example, when deleting a post completely (not moving to the trash), delete the featured image and product gallery images from the media library. CAREFUL.If you use the same images on multiple posts, it will break the images on other posts', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'keys_for_infinite_serialized_handler',
						'type' => 'text',
						'title' => __('Meta keys that should use the infinite serialized fields handler', VGSE()->textname),
						'desc' => __('This is only for advanced users or if our support team asks you to use this option. We have 2 ways to handle serialized fields: the old handler (used by default, which has limitations) and the infinite serialization handler (better, it is not active by default to not break previous integrations). Use this option if you have serialized fields that save incorrectly or dont appear in the spreadsheet.', VGSE()->textname),
					),
					array(
						'id' => 'show_all_custom_statuses',
						'type' => 'switch',
						'title' => __('Show all the custom post statuses?', VGSE()->textname),
						'desc' => __('By default we show the CORE statuses: published, draft, private, scheduled, trash. However, some plugins register custom statuses: job managers, woocommerce. Enable this option to show all the custom statuses in the "status" column. CAREFUL. We will show all the statuses from all the post types in the dropdown because it is impossible to know the post type of each status to we can not separate them. Do this only if you are a developer.', VGSE()->textname),
						'default' => false,
					),
					array(
						'id' => 'hide_cell_comments',
						'type' => 'switch',
						'title' => __('Hide cell comments?', VGSE()->textname),
						'desc' => __('By default we show comments in some columns indicating the value format or why they are locked. for example, the category column shows a tip indicating to separate terms with a comma and how to add child categories, variation columns have a tip indicating why they are locked for parent products. You can activate this option to disable those tips.', VGSE()->textname),
						'default' => false,
					),
				)
			);
		}

		public function setHelpTabs() {
			
		}

		/**

		  All the possible arguments for Redux.
		  For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

		 * */
		public function setArguments() {


			$this->args = array(
				'opt_name' => VGSE()->options_key,
				'display_name' => __('WP Sheet Editor', VGSE()->textname),
				'display_version' => VGSE()->version,
				'page_slug' => VGSE()->options_key,
				'page_title' => __('WP Sheet Editor', VGSE()->textname),
				'update_notice' => false,
				'admin_bar' => false,
				'menu_type' => 'submenu',
//				'menu_icon' => VGSE()->plugin_url . 'assets/imgs/icon-20x20.png',
				'menu_title' => __('Settings', VGSE()->textname),
				'allow_sub_menu' => true,
				'page_parent' => 'vg_sheet_editor_setup',
				'default_mark' => '*',
				'hints' =>
				array(
					'icon' => 'el-icon-question-sign',
					'icon_position' => 'right',
					'icon_color' => 'lightgray',
					'icon_size' => 'normal',
					'tip_style' =>
					array(
						'color' => 'light',
					),
					'tip_position' =>
					array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect' =>
					array(
						'show' =>
						array(
							'duration' => '500',
							'event' => 'mouseover',
						),
						'hide' =>
						array(
							'duration' => '500',
							'event' => 'mouseleave unfocus',
						),
					),
				),
				'output' => true,
				'output_tag' => true,
				'compiler' => true,
				'page_icon' => 'icon-themes',
				'dev_mode' => VGSE_DEBUG,
				'page_permissions' => 'manage_options',
				'save_defaults' => true,
				'show_import_export' => true,
				'transient_time' => '3600',
				'network_sites' => true,
			);
		}

	}

}

add_action('vg_sheet_editor/after_init', 'vgse_options_init');

function vgse_options_init() {

	new WP_Sheet_Editor_Redux_Setup();
}

/**
 * Disable dev mode. For some reason it doesnt disable when 
 * I change the dev_mode argument when constructing the options page.
 * So I took this code from the redux-developer-mode-disabler plugin
 */
if (!function_exists('vg_redux_disable_dev_mode_plugin')) {

	function vg_redux_disable_dev_mode_plugin($redux) {
		if ($redux->args['opt_name'] != 'redux_demo') {
			$redux->args['dev_mode'] = false;
			$redux->args['forced_dev_mode_off'] = false;
		}
	}

	add_action('redux/construct', 'vg_redux_disable_dev_mode_plugin');
}
