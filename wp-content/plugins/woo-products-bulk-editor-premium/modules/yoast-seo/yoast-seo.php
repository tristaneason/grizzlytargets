<?php
if (!class_exists('WP_Sheet_Editor_YOAST_SEO')) {

	class WP_Sheet_Editor_YOAST_SEO {

		static private $instance = false;

		private function __construct() {
			
		}

		function notify_wrong_core_version() {
			$plugin_data = get_plugin_data(__FILE__, false, false);
			?>
			<div class="notice notice-error">
				<p><?php _e('Please update the WP Sheet Editor plugin and all its extensions to the latest version. The features of the plugin "' . $plugin_data['Name'] . '" will be disabled to prevent errors and they will be enabled automatically after you install the updates.', VGSE()->textname); ?></p>
			</div>
			<?php
		}

		function init() {

			if (version_compare(VGSE()->version, '2.0.0') < 0) {
				add_action('admin_notices', array($this, 'notify_wrong_core_version'));
				return;
			}
			// exit if yoast seo plugin is not active
			if (!$this->is_yoast_seo_plugin_active()) {
				return;
			}

			add_action('vg_sheet_editor/editor/before_init', array($this, 'register_columns'));
			add_action('vg_sheet_editor/load_rows/output', array($this, 'filter_seo_score_cell_html'), 10, 3);
			add_filter('vg_sheet_editor/provider/post/get_item_meta', array($this, 'filter_cell_data_for_readings'), 10, 5);
			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'filter_cell_data_for_saving'), 10, 3);
		}

		/**
		 * Filter html of SEO score cells to display the score icon.
		 * @param array $data
		 * @param array $qry
		 * @param array $spreadsheet_columns
		 * @return array
		 */
		function filter_seo_score_cell_html($data, $qry, $spreadsheet_columns) {

			if (!isset($spreadsheet_columns['_yoast_wpseo_linkdex'])) {
				return $data;
			}
			foreach ($data as $post_index => $post_row) {

				$noindex = (int) VGSE()->helpers->get_current_provider()->get_item_meta($post_row['ID'], '_yoast_wpseo_meta-robots-noindex', true);

				$score = '';
				if ($noindex) {
					$score = 'noindex';
				} elseif (!empty($post_row['_yoast_wpseo_linkdex'])) {
					$score = WPSEO_Utils::translate_score($post_row['_yoast_wpseo_linkdex']);
				}
				$data[$post_index]['_yoast_wpseo_linkdex'] = '<div class="' . esc_attr('wpseo-score-icon ' . $score) . '"></div>';
			}
			return $data;
		}

		/**
		 * Is yoast seo plugin active
		 * @return boolean
		 */
		function is_yoast_seo_plugin_active() {
			$plugins = apply_filters('active_plugins', get_option('active_plugins'));
			if (in_array('wordpress-seo-premium/wp-seo-premium.php', $plugins) || in_array('wordpress-seo/wp-seo.php', $plugins)) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Test whether the yoast metabox is hidden either by choice of the admin or because
		 * the post type is not a public post type
		 *
		 * @param  string $post_type (optional) The post type to test, defaults to the current post post_type
		 *
		 * @return  bool        Whether or not the meta box (and associated columns etc) should be hidden
		 */
		function is_yoast_metabox_hidden($post_type = null) {
			$options = get_option('wpseo_titles');
			$disabled = false;
			if ((isset($options['hideeditbox-' . $post_type]) && $options['hideeditbox-' . $post_type] === true) || (isset($options['hideeditbox-tax-' . $post_type]) && $options['hideeditbox-tax-' . $post_type] === true)) {
				$disabled = true;
			}
			return $disabled;
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_YOAST_SEO::$instance) {
				WP_Sheet_Editor_YOAST_SEO::$instance = new WP_Sheet_Editor_YOAST_SEO();
				WP_Sheet_Editor_YOAST_SEO::$instance->init();
			}
			return WP_Sheet_Editor_YOAST_SEO::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

		function get_term_meta($post, $cell_key, $cell_args) {
			$value = WPSEO_Taxonomy_Meta::get_term_meta($post, $post->taxonomy, str_replace('_yoast_wpseo_', '', $cell_key));

			if ($value === 'default') {
				$value = '';
			}
			if ($cell_key === '_yoast_wpseo_noindex' && empty($value)) {
				$value = 'index';
			}
			return $value;
		}

		function update_term_meta($post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns) {
			$old = WPSEO_Taxonomy_Meta::get_term_meta($post_id, $post_type);
			if (empty($old)) {
				$old = array();
			}
			$new = array_merge($old, array(
				str_replace('_yoast_', '', $cell_key) => $data_to_save
			));

			if (in_array($cell_key, array('_yoast_wpseo_opengraph-image', '_yoast_wpseo_twitter-image'), true)) {
				$new[$cell_key . '-id'] = VGSE()->helpers->get_attachment_id_from_url($data_to_save);
			}

			WPSEO_Taxonomy_Meta::set_values($post_id, $post_type, $new);
		}

		/**
		 * Register spreadsheet columns
		 */
		function register_columns($editor) {
			if ($editor->provider->key === 'user') {
				return;
			}
			$post_types = $editor->args['enabled_post_types'];
			$tax_settings_global = array(
				'get_value_callback' => array($this, 'get_term_meta'),
				'save_value_callback' => array($this, 'update_term_meta'),
				'supports_sql_formulas' => false,
			);

			foreach ($post_types as $post_type) {

				if ($this->is_yoast_metabox_hidden($post_type)) {
					continue;
				}
				$tax_settings = taxonomy_exists($post_type) ? $tax_settings_global : array();
				$editor->args['columns']->register_item('_yoast_wpseo_title', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'unformatted' => array('data' => '_yoast_wpseo_title'),
					'column_width' => 300,
					'title' => __('SEO Title', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'formatted' => array('data' => '_yoast_wpseo_title', 'renderer' => 'html'),
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$desc_key = ( taxonomy_exists($post_type)) ? '_yoast_wpseo_desc' : '_yoast_wpseo_metadesc';
				$editor->args['columns']->register_item($desc_key, $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'unformatted' => array('data' => $desc_key),
					'column_width' => 300,
					'title' => __('SEO Description', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'formatted' => array('data' => $desc_key),
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_focuskw', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'unformatted' => array('data' => '_yoast_wpseo_focuskw'),
					'column_width' => 120,
					'title' => __('SEO Keyword', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'formatted' => array('data' => '_yoast_wpseo_focuskw', 'renderer' => 'html'),
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));

				$editor->args['columns']->register_item('_yoast_wpseo_opengraph-title', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO FB title', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_opengraph-description', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO FB description', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_opengraph-image', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO FB image', VGSE()->textname),
					'type' => 'boton_gallery',
					'supports_formulas' => true,
					'supports_sql_formulas' => false,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_twitter-title', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO TW title', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_twitter-description', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO TW description', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_twitter-image', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO TW image', VGSE()->textname),
					'type' => 'boton_gallery',
					'supports_formulas' => true,
					'supports_sql_formulas' => false,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_canonical', $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'column_width' => 120,
					'title' => __('SEO Canonical URL', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));


				$noindex_key = ( taxonomy_exists($post_type)) ? '_yoast_wpseo_noindex' : '_yoast_wpseo_meta-robots-noindex';
				$noindex_checked = ( taxonomy_exists($post_type)) ? 'noindex' : '1';
				$noindex_unchecked = ( taxonomy_exists($post_type)) ? 'index' : null;
				$noindex_default = ( taxonomy_exists($post_type)) ? 'noindex' : null;
				$editor->args['columns']->register_item($noindex_key, $post_type, array_merge(array(
					'data_type' => 'meta_data',
					'unformatted' => array('data' => $noindex_key),
					'column_width' => 120,
					'title' => __('SEO No Index', VGSE()->textname),
					'type' => '',
					'supports_formulas' => true,
					'formatted' => array('data' => $noindex_key, 'type' => 'checkbox', 'checkedTemplate' => $noindex_checked, 'uncheckedTemplate' => $noindex_unchecked, 'className' => 'htCenter htMiddle'),
					'default_value' => $noindex_default,
					'allow_to_hide' => true,
					'allow_to_rename' => true,
								), $tax_settings));
				$editor->args['columns']->register_item('_yoast_wpseo_linkdex', $post_type, array(
					'data_type' => 'meta_data',
					'unformatted' => array('data' => '_yoast_wpseo_linkdex', 'readOnly' => true, 'renderer' => 'html'),
					'column_width' => 50,
					'title' => __('SEO', VGSE()->textname),
					'type' => '',
					'supports_formulas' => false,
					'formatted' => array('data' => '_yoast_wpseo_linkdex', 'readOnly' => true, 'renderer' => 'html'),
					'allow_to_hide' => true,
					'allow_to_rename' => true,
					'allow_plain_text' => false,
					'is_locked' => true,
				));

				if ($editor->provider->is_post_type) {
					$primary_taxonomies = $this->generate_primary_term_taxonomies($post_type);
					foreach ($primary_taxonomies as $taxonomy) {

						$editor->args['columns']->register_item('_yoast_wpseo_primary_' . $taxonomy->name, $post_type, array(
							'data_type' => 'meta_data',
							'column_width' => 100,
							'title' => sprintf(__('SEO Primary %s', VGSE()->textname), $taxonomy->labels->singular_name),
							'type' => '',
							'supports_formulas' => true,
							'supports_sql_formulas' => false,
							'formatted' => array(
								'type' => 'autocomplete',
								'source' => 'loadTaxonomyTerms',
								'taxonomy_key' => $taxonomy->name
							),
							'allow_to_hide' => true,
							'allow_to_rename' => true,
							'allow_plain_text' => true,
						));
					}
				}
			}
		}

		function filter_cell_data_for_saving($new_value, $id, $key) {
			$post_type = get_post_type($id);
			if ($this->is_yoast_metabox_hidden($post_type)) {
				return $new_value;
			}

			if (strpos($key, '_yoast_wpseo_primary_') !== false) {
				$taxonomy = str_replace('_yoast_wpseo_primary_', '', $key);
				$term = get_term_by('name', $new_value, $taxonomy);
				if (is_object($term) && !is_wp_error($term)) {
					$new_value = $term->term_id;
				} else {
					$new_value = '';
				}
			}

			if (in_array($key, array('_yoast_wpseo_opengraph-image', '_yoast_wpseo_twitter-image'), true)) {
				update_post_meta($id, $key . '-id', VGSE()->helpers->get_attachment_id_from_url($new_value));
			}

			return $new_value;
		}

		function filter_cell_data_for_readings($value, $id, $key, $single, $context) {
			if ($context !== 'read') {
				return $value;
			}

			$post_type = get_post_type($id);
			if (strpos($key, '_yoast_wpseo_primary_') === false || $this->is_yoast_metabox_hidden($post_type)) {
				return $value;
			}
			$taxonomy = str_replace('_yoast_wpseo_primary_', '', $key);
			$term = get_term_by('term_id', $value, $taxonomy);

			if (is_object($term) && !is_wp_error($term)) {
				$value = $term->name;
			} else {
				$value = '';
			}

			return $value;
		}

		/**
		 * Returns whether or not a taxonomy is hierarchical.
		 *
		 * @param stdClass $taxonomy Taxonomy object.
		 *
		 * @return bool
		 */
		private function filter_hierarchical_taxonomies($taxonomy) {
			return (bool) $taxonomy->hierarchical;
		}

		function generate_primary_term_taxonomies($post_type) {
			$all_taxonomies = get_object_taxonomies($post_type, 'objects');
			$all_taxonomies = array_filter($all_taxonomies, array($this, 'filter_hierarchical_taxonomies'));

			/**
			 * Filters which taxonomies for which the user can choose the primary term.
			 *
			 * @api array    $taxonomies An array of taxonomy objects that are primary_term enabled.
			 *
			 * @param string $post_type      The post type for which to filter the taxonomies.
			 * @param array  $all_taxonomies All taxonomies for this post types, even ones that don't have primary term
			 *                               enabled.
			 */
			$taxonomies = (array) apply_filters('wpseo_primary_term_taxonomies', $all_taxonomies, $post_type, $all_taxonomies);

			return $taxonomies;
		}

	}

}

if (!function_exists('WP_Sheet_Editor_YOAST_SEO_Obj')) {

	function WP_Sheet_Editor_YOAST_SEO_Obj() {
		return WP_Sheet_Editor_YOAST_SEO::get_instance();
	}

}


add_action('vg_sheet_editor/initialized', 'WP_Sheet_Editor_YOAST_SEO_Obj');
