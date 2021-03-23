<?php
if (!class_exists('WP_Sheet_Editor_Advanced_Filters')) {

	/**
	 * Filter rows in the spreadsheet editor.
	 */
	class WP_Sheet_Editor_Advanced_Filters {

		static private $instance = false;
		var $addon_helper = null;
		var $plugin_url = null;
		var $plugin_dir = null;

		private function __construct() {
			
		}

		function init() {

			$this->plugin_url = plugins_url('/', __FILE__);
			$this->plugin_dir = __DIR__;

			add_action('vg_sheet_editor/after_enqueue_assets', array($this, 'register_assets'));
			add_action('vg_sheet_editor/filters/after_fields', array($this, 'add_filters_fields'), 10, 2);
			add_action('vg_sheet_editor/filters/before_form_closing', array($this, 'add_advanced_filters_fields'), 10, 2);
			add_filter('vg_sheet_editor/load_rows/wp_query_args', array($this, 'filter_posts'), 10, 2);
			add_filter('vg_sheet_editor/filters/allowed_fields', array($this, 'register_filters'), 10, 2);
			add_filter('posts_clauses', array($this, 'exclude_by_keyword'), 10, 2);
			add_filter('posts_clauses', array($this, 'add_advanced_post_data_query'), 10, 2);
			add_filter('posts_clauses', array($this, 'add_advanced_taxonomy_query'), 10, 2);
		}

		function add_advanced_taxonomy_query($clauses, $wp_query) {
			global $wpdb;
			if (empty($wp_query->query['wpse_original_filters']) || empty($wp_query->query['wpse_original_filters']['meta_query']) || !is_array($wp_query->query['wpse_original_filters']['meta_query'])) {
				return $clauses;
			}
			$post_data_query = wp_list_filter($wp_query->query['wpse_original_filters']['meta_query'], array(
				'source' => 'taxonomy_keys'
			));
			if (empty($post_data_query)) {
				return $clauses;
			}

			$wheres = array(
				'IN' => array(),
				'NOT IN' => array(),
			);
			foreach ($post_data_query as $post_data_parameters) {
				if (empty($post_data_parameters['key']) || empty($post_data_parameters['compare'])) {
					continue;
				}
				if (in_array($post_data_parameters['compare'], array('LIKE', 'NOT LIKE'))) {
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'] . '%';
				}

				if ($post_data_parameters['compare'] === 'length_less') {
					if ((int) $post_data_parameters['value'] < 1) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value'] = '^.{0,' . (int) $post_data_parameters['value'] . '}$';
				}
				if ($post_data_parameters['compare'] === 'length_higher') {
					if ((int) $post_data_parameters['value'] < 1) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value'] = '^.{' . (int) $post_data_parameters['value'] . ',}$';
				}

				if ($post_data_parameters['compare'] === 'starts_with') {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value'] = $post_data_parameters['value'] . '%';
				}
				if ($post_data_parameters['compare'] === 'ends_with') {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'];
				}

				$group = 'IN';
				if (in_array($post_data_parameters['compare'], array('NOT LIKE'))) {
					$post_data_parameters['compare'] = 'LIKE';
					$group = 'NOT IN';
				} elseif (empty($post_data_parameters['value']) && $post_data_parameters['compare'] === '=') {
					$group = 'NOT IN';
				}
				if (empty($post_data_parameters['value'])) {
					$sql_where = "tt.taxonomy IN ('" . esc_sql($post_data_parameters['key']) . "')";
				} else {
					$sql_where = "tt.taxonomy IN ('" . esc_sql($post_data_parameters['key']) . "') AND t.name " . esc_sql($post_data_parameters['compare']) . " '" . esc_sql($post_data_parameters['value']) . "' ";
				}
				$sql = "SELECT tr.object_id
FROM $wpdb->terms AS t 
INNER JOIN $wpdb->term_taxonomy AS tt 
ON t.term_id = tt.term_id
INNER JOIN $wpdb->term_relationships AS tr
ON tr.term_taxonomy_id = tt.term_taxonomy_id
INNER JOIN $wpdb->posts AS p 
ON (p.ID = tr.object_id)

WHERE 1 = 1 AND p.post_type = '" . $wp_query->query['post_type'] . "' 

AND " . $sql_where . " 
  
GROUP BY tr.object_id";
				$wheres[$group][] = $sql;
			}

			foreach ($wheres as $operator => $queries) {
				foreach ($queries as $query) {
					$clauses['where'] .= " AND $wpdb->posts.ID $operator (" . $query . ")  ";
				}
			}
			return $clauses;
		}

		function add_advanced_post_data_query($clauses, $wp_query) {
			global $wpdb;
			if (empty($wp_query->query['wpse_original_filters']) || empty($wp_query->query['wpse_original_filters']['meta_query']) || !is_array($wp_query->query['wpse_original_filters']['meta_query'])) {
				return $clauses;
			}
			$post_data_query = wp_list_filter($wp_query->query['wpse_original_filters']['meta_query'], array(
				'source' => 'post_data'
			));
			if (empty($post_data_query)) {
				return $clauses;
			}

			$wheres = array();
			foreach ($post_data_query as $post_data_parameters) {
				if (empty($post_data_parameters['key']) || empty($post_data_parameters['compare'])) {
					continue;
				}
				if (!is_string($post_data_parameters['key']) || !is_string($post_data_parameters['compare'])) {
					continue;
				}
				if (in_array($post_data_parameters['compare'], array('LIKE', 'NOT LIKE'))) {
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'] . '%';
				}

				if ($post_data_parameters['compare'] === 'length_less') {
					if ((int) $post_data_parameters['value'] < 1) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value'] = '^.{0,' . (int) $post_data_parameters['value'] . '}$';
				}
				if ($post_data_parameters['compare'] === 'length_higher') {
					if ((int) $post_data_parameters['value'] < 1) {
						$post_data_parameters['value'] = 1;
					}
					$post_data_parameters['compare'] = 'REGEXP';
					$post_data_parameters['value'] = '^.{' . (int) $post_data_parameters['value'] . ',}$';
				}
				if ($post_data_parameters['compare'] === 'starts_with') {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value'] = $post_data_parameters['value'] . '%';
				}
				if ($post_data_parameters['compare'] === 'ends_with') {
					$post_data_parameters['compare'] = 'LIKE';
					$post_data_parameters['value'] = '%' . $post_data_parameters['value'];
				}

				$wheres[] = " $wpdb->posts." . esc_sql($post_data_parameters['key']) . " " . esc_sql($post_data_parameters['compare']) . " '" . esc_sql($post_data_parameters['value']) . "' ";
			}
			if (!empty($wheres)) {
				$clauses['where'] .= ' AND ' . implode(' AND ', $wheres);
			}


			return $clauses;
		}

		function exclude_by_keyword($clauses, $wp_query) {
			if (!empty($wp_query->query['wpse_not_contains_keyword'])) {
				$clauses = WP_Sheet_Editor_Filters::get_instance()->add_search_by_keyword_clause($clauses, $wp_query->query['wpse_not_contains_keyword'], 'NOT LIKE', 'AND');
			}
			return $clauses;
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_script('advanced-filters_js', $this->plugin_url . 'assets/js/init.js', array(), VGSE()->version, false);
		}

		function register_filters($filters, $post_type) {
			if (VGSE()->helpers->get_current_provider()->is_post_type) {
				$taxonomies = get_object_taxonomies($post_type);
				if (!empty($taxonomies)) {
					$filters['taxonomy_term'] = array(
						'label' => '',
						'description' => '',
					);
				}
				$filters['date'] = array(
					'label' => '',
					'description' => '',
				);
				if (post_type_supports($post_type, 'page-attributes') && $post_type !== 'attachment') {
					$filters['post_parent'] = array(
						'label' => __('Parent', VGSE()->textname),
						'description' => ''
					);
				}
			}
			return $filters;
		}

		/**
		 * Apply filters to wp-query args
		 * @param array $query_args
		 * @param array $data
		 * @return array
		 */
		function filter_posts($query_args, $data) {

			if (!empty($data['filters'])) {
				$filters = WP_Sheet_Editor_Filters::get_instance()->get_raw_filters($data);

				$query_args['wpse_original_filters'] = $filters;

				if (!empty($filters['apply_to']) && is_array($filters['apply_to'])) {
					$taxonomies_group = array();

					foreach ($filters['apply_to'] as $term) {
						$term_parts = explode('--', $term);
						if (count($term_parts) !== 2) {
							continue;
						}
						$taxonomy = $term_parts[0];
						$term = $term_parts[1];

						if (!isset($taxonomies_group[$taxonomy])) {
							$taxonomies_group[$taxonomy] = array();
						}
						$taxonomies_group[$taxonomy][] = $term;
					}

					$query_args['tax_query'] = array(
						'relation' => 'AND',
					);

					foreach ($taxonomies_group as $taxonomy_key => $terms) {
						$query_args['tax_query'][] = array(
							'taxonomy' => $taxonomy_key,
							'field' => 'slug',
							'terms' => $terms
						);
					}
				}

				if (!empty($filters['keyword_exclude'])) {
					$editor = VGSE()->helpers->get_provider_editor($query_args['post_type']);
					if ($editor->provider->is_post_type) {
						$query_args['wpse_not_contains_keyword'] = $filters['keyword_exclude'];
					} else {
						$post_id_exclude = $editor->provider->get_item_ids_by_keyword($filters['keyword_exclude'], $query_args['post_type'], 'LIKE');
						$query_args['post__not_in'] = $post_id_exclude;
					}
				}

				if (!empty($filters['post_parent'])) {
					$query_args['post_parent'] = (int) str_replace('page--', '', $filters['post_parent']);
				}
				if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
					$query_args['date_query'] = array(
						'inclusive' => true
					);
				}
				if (!empty($filters['post__in'])) {
					$post_ids_parts = explode(',', $filters['post__in']);
					$post_ids = array();
					foreach ($post_ids_parts as $post_ids_part) {
						if (strpos($post_ids_part, '-') !== false) {
							$range_parts = array_filter(explode('-', $post_ids_part));
							if (count($range_parts) === 2) {
								$post_ids = array_merge($post_ids, range($range_parts[0], $range_parts[1]));
							}
						} else {
							$post_ids[] = $post_ids_part;
						}
					}
					$post_ids = array_map('intval', $post_ids);
					$query_args['post__in'] = (!empty($query_args['post__in'])) ? array_intersect($query_args['post__in'], $post_ids) : $post_ids;
				}
				if (!empty($filters['post_name__in'])) {
					$post_slugs = array_unique(array_filter(array_map('basename', array_map('trim', explode(PHP_EOL, $filters['post_name__in'])))));
					if (!empty($post_slugs)) {
						$query_args['post_name__in'] = $post_slugs;
					}
				}
				if (!empty($filters['date_from'])) {
					$query_args['date_query']['after'] = $filters['date_from'];
				}
				if (!empty($filters['date_to'])) {
					$query_args['date_query']['before'] = $filters['date_to'];
				}
				if (!empty($filters['meta_query']) && is_array($filters['meta_query'])) {
					foreach ($filters['meta_query'] as $index => $meta_query) {
						if (is_array($meta_query['key'])) {
							$meta_query['key'] = array_filter($meta_query['key']);
						}
						if (empty($meta_query['key']) || empty($meta_query['compare']) || empty($meta_query['source'])) {
							unset($filters['meta_query'][$index]);
							unset($query_args['wpse_original_filters']['meta_query'][$index]);
						}
						if ($meta_query['source'] !== 'meta') {
							unset($filters['meta_query'][$index]);
							continue;
						}


						if ($meta_query['compare'] === 'length_less') {
							if ((int) $meta_query['value'] < 1) {
								$meta_query['value'] = 1;
							}
							$filters['meta_query'][$index]['compare'] = 'REGEXP';
							$filters['meta_query'][$index]['value'] = '^.{0,' . (int) $meta_query['value'] . '}$';
						}
						if ($meta_query['compare'] === 'length_higher') {
							if ((int) $meta_query['value'] < 1) {
								$meta_query['value'] = 1;
							}
							$filters['meta_query'][$index]['compare'] = 'REGEXP';
							$filters['meta_query'][$index]['value'] = '^.{' . (int) $meta_query['value'] . ',}$';
						}
						if ($meta_query['compare'] === 'starts_with') {
							$filters['meta_query'][$index]['compare'] = 'REGEXP';
							$filters['meta_query'][$index]['value'] = '^' . $meta_query['value'];
						}
						if ($meta_query['compare'] === 'ends_with') {
							$filters['meta_query'][$index]['compare'] = 'REGEXP';
							$filters['meta_query'][$index]['value'] .= '$';
						}

						if (in_array($meta_query['compare'], array('>', '>=', '<', '<='))) {
							$filters['meta_query'][$index]['type'] = 'NUMERIC';
						}
						if (empty($meta_query['value']) && in_array($meta_query['compare'], array('=', 'LIKE'))) {
							$not_exists = $meta_query;
							$not_exists['compare'] = 'NOT EXISTS';
							$filters['meta_query'][$index] = array(
								'relation' => 'OR',
								$meta_query,
								$not_exists
							);
						}
					}


					$query_args['meta_query'] = $filters['meta_query'];
				}
			}

			return $query_args;
		}

		function add_filters_fields($current_post_type, $filters) {
			?>

			<?php
			if (isset($filters['taxonomy_term'])) {
				?>
				<li class="<?php
				$labels = VGSE()->helpers->get_post_type_taxonomies_single_data($current_post_type, 'label');
				if (empty($labels)) {
					echo ' hidden';
				}

				$labels[count($labels) - 1] = ' or ' . end($labels);
				?>">
					<label><?php printf(__('Enter %s', VGSE()->textname), implode(', ', array_unique($labels))); ?> <a href="#" class="tipso" data-tipso="<?php _e('Enter the names of ' . implode(', ', $labels)); ?>">( ? )</a></label>
					<select data-placeholder="<?php _e('Category name...', VGSE()->textname); ?>" name="apply_to[]" class="select2"  multiple data-remote="true" data-action="vgse_search_taxonomy_terms" data-min-input-length="4">

					</select>
				</li>
			<?php } ?>

			<?php if (isset($filters['post_parent'])) { ?>
				<li>
					<label><?php echo $filters['post_parent']['label']; ?>  <?php if (!empty($filters['post_parent']['description'])) { ?><a href="#" class="tipso" data-tipso="<?php echo $filters['post_parent']['description']; ?>">( ? )</a><?php } ?></label>
					<select name="post_parent" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo $current_post_type; ?>" data-nonce="<?php echo wp_create_nonce('bep-nonce'); ?>" data-placeholder="<?php _e('Select...', VGSE()->textname); ?> " class="select2" multiple>
						<option></option>
					</select> 									
				</li>
				<?php
			}
		}

		function get_advanced_filters_fields($current_post_type, $filters) {
			global $wpdb;
			$all_meta_keys = apply_filters('vg_sheet_editor/advanced_filters/all_meta_keys', VGSE()->helpers->get_all_meta_keys($current_post_type, 1000), $current_post_type, $filters);

			// post data and taxonomy advanced filters are available for post types only
			if (VGSE()->helpers->get_current_provider()->is_post_type) {
				$taxonomy_keys = VGSE()->helpers->get_post_type_taxonomies_single_data($current_post_type, 'name');
				$item_raw = $wpdb->get_row("SELECT * FROM $wpdb->posts LIMIT 1", ARRAY_A);
				$item = (is_array($item_raw)) ? array_keys($item_raw) : array();
			} else {
				$item = array();
				$taxonomy_keys = array();
			}

			$all_fields = array(
				'meta' => array_unique($all_meta_keys),
				'post_data' => array_unique($item),
				'taxonomy_keys' => array_unique($taxonomy_keys)
			);
			return apply_filters('vg_sheet_editor/advanced_filters/all_fields_groups', $all_fields, $current_post_type, $filters);
		}

		function add_advanced_filters_fields($current_post_type, $filters) {
			?>

			<p><label><input type="checkbox" class="advanced-filters-toggle"> <?php _e('Enable advanced filters', VGSE()->textname); ?></label></p>
			<div class="advanced-filters"  style="display: none;">
				<h3><?php _e('Advanced search', VGSE()->textname); ?></h3>
				<p class="advanced-filters-message"><?php _e('You can search by any field using operators. I.e. price > 100, image != (empty)', VGSE()->textname); ?></p>
				<ul class="unstyled-list advanced-filters-list">
					<li class="base advanced-field" style="display: none;">
						<div class="fields-wrap">
							<div class="field-wrap">
								<label><?php _e('Field key', VGSE()->textname); ?></label>
								<select name="meta_query[][key]" data-placeholder="<?php _e('Select...', VGSE()->textname); ?>" class="select2 wpse-advanced-filters-field-selector">
									<option value="" selected>- -</option>
									<?php
									$all_fields = $this->get_advanced_filters_fields($current_post_type, $filters);
									if (!empty($all_fields) && is_array($all_fields)) {
										$unfiltered_columns = WP_Sheet_Editor_Columns_Visibility::$unfiltered_columns;
										$columns = isset($unfiltered_columns[$current_post_type]) ? $unfiltered_columns[$current_post_type] : array();
										foreach ($all_fields as $group_key => $group_fields) {
											foreach ($group_fields as $field_key) {
												$field_label = '';
												if (isset($columns[$field_key])) {
													$field_label = $columns[$field_key]['title'];
												}
												$label = ( $field_label ) ? $field_label . " ($field_key)" : $field_key;

												echo '<option value="' . esc_attr($field_key) . '" data-source="' . esc_attr($group_key) . '" ';
												echo '>' . $label . "</option>";
											}
										}
									}
									?>
								</select>
								<input type="hidden" name="meta_query[][source]" class="field-source">
							</div>
							<div class="field-wrap">
								<label><?php _e('Operator', VGSE()->textname); ?></label>
								<select name="meta_query[][compare]" data-placeholder="<?php _e('Select...', VGSE()->textname); ?>" class=" wpse-advanced-filters-operator-selector">
									<option value="=" selected>=</option>
									<option value="!=" >!=</option>
									<option value="<" ><</option>
									<option value="<=" ><=</option>
									<option value=">" >></option>
									<option value=">=" >>=</option>
									<option value="LIKE" ><?php _e('CONTAINS', VGSE()->textname); ?></option>
									<option value="NOT LIKE" ><?php _e('NOT CONTAINS', VGSE()->textname); ?></option>
									<option value="starts_with" ><?php _e('STARTS WITH', VGSE()->textname); ?></option>
									<option value="ends_with" ><?php _e('ENDS WITH', VGSE()->textname); ?></option>
									<option value="length_less" ><?php _e('CHARACTER LENGTH <', VGSE()->textname); ?></option>
									<option value="length_higher" ><?php _e('CHARACTER LENGTH >', VGSE()->textname); ?></option>
									<option value="REGEXP" ><?php _e('REGEXP', VGSE()->textname); ?></option>
								</select>
							</div>
							<div class="field-wrap">
								<label><?php _e('Value', VGSE()->textname); ?></label>
								<input name="meta_query[][value]" class=" wpse-advanced-filters-value-selector"/>
							</div>
						</div>
						<div class="fields-wrap"><a href="#" class="button remove-advanced-filter"><?php _e('X', VGSE()->textname); ?></a></div>
					</li>
					<?php
					do_action('vg_sheet_editor/filters/after_advanced_fields', $current_post_type);
					?>
					<li>
						<button type="submit" class="button primary hidden apply-formula-submit-inside"><?php _e('Apply filters', VGSE()->textname); ?></button>

					</li>
				</ul>

				<div class="fields-wrap"><a href="#" class="button new-advanced-filter"><?php _e('Add new', VGSE()->textname); ?></a></div>
				<hr>
				<ul class="unstyled-list">
					<li class="exclude-keyword">
						<label><?php echo __('NOT Contains this keyword', VGSE()->textname); ?>  <a href="#" class="tipso" data-tipso="<?php echo __('Enter a keyword to exclude posts, separate multiple keywords with a semicolon (;)', VGSE()->textname); ?>">( ? )</a></label>
						<input type="text" name="keyword_exclude">
					</li>
					<li class="post--in">
						<label><?php _e('Find these IDs:', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Enter IDs separated by commas. You add ranges like 20-50 as a shortcut.', VGSE()->textname); ?>">( ? )</a></label>
						<input type="text" name="post__in">
					</li>
					<?php if (VGSE()->helpers->get_current_provider()->is_post_type) { ?>
						<li class="post-name--in">
							<label><?php _e('Find these URLs:', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Enter one URL per line', VGSE()->textname); ?>">( ? )</a></label>
							<textarea name="post_name__in"></textarea>
						</li>
						<li class="date-range">
							<label><?php _e('Date range from', VGSE()->textname); ?> <a href="#" class="tipso" data-tipso="<?php _e('Show items published between these dates'); ?>">( ? )</a></label><input type="date" name="date_from" /><br/> <?php _e('to', VGSE()->textname); ?><br/> <input type="date" name="date_to" />
						</li>
					<?php } ?>
				</ul>
			</div>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Advanced_Filters::$instance) {
				WP_Sheet_Editor_Advanced_Filters::$instance = new WP_Sheet_Editor_Advanced_Filters();
				WP_Sheet_Editor_Advanced_Filters::$instance->init();
			}
			return WP_Sheet_Editor_Advanced_Filters::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

	add_action('vg_sheet_editor/initialized', 'vgse_advanced_filters_init');

	function vgse_advanced_filters_init() {
		WP_Sheet_Editor_Advanced_Filters::get_instance();
	}

}
