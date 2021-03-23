<?php

if (!class_exists('WP_Sheet_Editor_Data')) {

	class WP_Sheet_Editor_Data {

		static private $instance = false;
		var $friendly_terms_to_ids_cache = array();

		private function __construct() {
			
		}

		/**
		 * Get individual post field.
		 * @param string $item
		 * @param int $id
		 * @return mixed
		 */
		function get_post_data($item, $id) {
			$post = VGSE()->helpers->get_current_provider()->get_item($id);

			$out = '';
			if ($item === 'ID') {
				$out = $id;
			} elseif ($item === 'post_title') {
				$post_title = $post->post_title;
				if ($post->post_type === 'attachment' && empty($post_title)) {
					$out = basename($post->guid);
				} else {
					$out = $post_title;
				}
			} elseif ($item === 'post_content') {
				$out = empty(VGSE()->options['be_disable_wpautop']) ? wpautop($post->post_content) : $post->post_content;
			} elseif ($item === 'post_date') {
				$out = get_the_date('Y-m-d H:i:s', $id);
			} elseif ($item === 'modified') {
				$out = get_the_modified_date('Y-m-d H:i:s', $id);
			} elseif ($item === 'post_author') {
				$author = get_userdata($post->post_author);
				$out = ( $author ) ? $author->user_login : '';
			} elseif ($item === 'post_status') {

				// We include the custom statuses added by other plugins
				// The provider get_statuses() is used for the internal capability checks
				$all_statuses = get_post_stati(array('show_in_admin_status_list' => true), 'objects');
				$custom_statuses = array();
				foreach ($all_statuses as $status_key => $status) {
					if (!empty($status->label_count['domain'])) {
						$custom_statuses[$status_key] = $status->label;
					}
				}

				// If the post status is found in the public post statuses we return it directly,
				// otherwise we return it with a lock icon because the cell will be read-only
				$statuses = array_merge(VGSE()->helpers->get_current_provider()->get_statuses(), $custom_statuses);
				if (!isset($statuses['trash'])) {
					$statuses['trash'] = 'Trash';
				}
				$out = ( isset($statuses[$post->post_status]) || VGSE()->helpers->is_plain_text_request() ) ? $post->post_status : '<i class="fa fa-lock vg-cell-blocked"></i> ' . $post->post_status;
			} elseif ($item === 'post_parent') {
				$out = (!empty($post->post_parent) ) ? get_the_title($post->post_parent) : '';
			} else {
				$out = VGSE()->helpers->get_current_provider()->get_item_data($id, $item);
			}

			return $out;
		}

		/**
		 * Prepare individual post field for saving
		 * @param string $key
		 * @param mixed $item
		 * @param int $id
		 * @return mixed
		 */
		function set_post($key, $item, $id = null) {

			if (!VGSE()->helpers->get_current_provider()->is_post_type) {
				return $item;
			}
			$out = false;

			if ($key === 'ID') {
				$out = (int) $item;
			} elseif ($key === 'post_content') {
				$out = empty(VGSE()->options['be_disable_wpautop']) ? wpautop($item) : $item;
			} elseif ($key === 'post_date') {
				$out = $this->change_date_format_for_saving($item);
			} elseif ($key === 'post_modified') {
				$out = (!empty($item) ) ? $item : current_time('mysql');
			} elseif ($key === 'post_author') {
				$out = $this->get_author_id_from_username($item);
			} elseif ($key === 'post_parent') {
				$out = $this->get_post_id_from_title($item);
			} elseif ($key === 'post_status') {
				$statuses_raw = get_post_stati(null, 'objects');
				$statuses = wp_list_pluck($statuses_raw, 'label', 'name');
				// Allow to save status delete, which deletes the post completely
				$statuses['delete'] = 'delete';
				if (isset($statuses[$item])) {
					$out = $item;
				} elseif ($status_key = array_search($item, $statuses)) {
					$out = $status_key;
				}
			} else {
				$out = $item;
			}

			return $out;
		}

		/**
		 * Format term ids to names.
		 * Copied from WC core WC_CSV_Exporter::format_term_ids()
		 * @param  array  $term_ids Term IDs to format.
		 * @param  string $taxonomy Taxonomy name.
		 * @return string
		 */
		public function format_term_ids($term_ids, $taxonomy, $separator) {
			$term_ids = wp_parse_id_list($term_ids);

			if (!count($term_ids)) {
				return '';
			}

			$formatted_terms = array();

			if (is_taxonomy_hierarchical($taxonomy)) {
				foreach ($term_ids as $term_id) {
					$formatted_term = array();
					$ancestor_ids = array_reverse(get_ancestors($term_id, $taxonomy));

					foreach ($ancestor_ids as $ancestor_id) {
						$term = get_term($ancestor_id, $taxonomy);
						if ($term && !is_wp_error($term)) {
							$formatted_term[] = $term->name;
						}
					}

					$term = get_term($term_id, $taxonomy);

					if ($term && !is_wp_error($term)) {
						$formatted_term[] = $term->name;
					}

					$formatted_terms[] = implode(' > ', $formatted_term);
				}
			} else {
				foreach ($term_ids as $term_id) {
					$term = get_term($term_id, $taxonomy);

					if ($term && !is_wp_error($term)) {
						$formatted_terms[] = $term->name;
					}
				}
			}

			return implode("$separator ", $formatted_terms);
		}

		/**
		 * Convert terms list to friendly text.
		 * 
		 * List of terms separated by commas.
		 * 
		 * @param string|array $current_terms
		 * @return string
		 */
		function prepare_post_terms_for_display($current_terms) {
			if (is_string($current_terms)) {
				return $current_terms;
			}
			if (empty($current_terms) || is_wp_error($current_terms)) {
				return '';
			}

			$first_term = current($current_terms);
			$separator = (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',';
			$names = $this->format_term_ids(wp_list_pluck($current_terms, 'term_id'), $first_term->taxonomy, $separator);
			return $names;
		}

		function get_hierarchy_for_single_term($term) {
			$out = $term->name;
			while ($term->parent > 0) {
				$term = get_term_by('id', $term->parent, $term->taxonomy);
				$out = $term->name . ' > ' . $out;
			}

			return $out;
		}

		function get_taxonomy_hierarchy($taxonomy, $parent = 0, $parent_name = '') {
			// only 1 taxonomy
			$taxonomy = is_array($taxonomy) ? array_shift($taxonomy) : $taxonomy;
			// get all direct decendants of the $parent
			$terms = get_terms(array('taxonomy' => $taxonomy, 'parent' => $parent, 'hide_empty' => false, 'update_term_meta_cache' => false));
			// prepare a new array.  these are the children of $parent
			// we'll ultimately copy all the $terms into this new array, but only after they
			// find their own children
			$out = array();
			// go through all the direct decendants of $parent, and gather their children
			foreach ($terms as $term) {
				// add the term to our new array
				if (!empty($parent_name)) {
					$term->name = $parent_name . ' > ' . $term->name;
				}

				$out[] = $term;

				// recurse to get the direct decendants of "this" term
				$children = $this->get_taxonomy_hierarchy($taxonomy, $term->term_id, $term->name);
				$out = array_merge($out, $children);
			}
			// send the results back to the caller
			return $out;
		}

		/**
		 * Get all terms in taxonomy
		 * @param string $taxonomy
		 * @return array|bool
		 */
		function get_taxonomy_terms($taxonomy, $source = '') {
			$cache_key = 'wpse_terms_' . $taxonomy;
			$terms = get_transient($cache_key);
			if (empty($terms)) {

				if (!is_taxonomy_hierarchical($taxonomy)) {
					$get_hierarchy = false;
				} else {
					// Building the hierarchy tree is expensive so we do it only for taxonomies with < 2500 terms
					// taxonomies with > 2500 terms get the list of names without hierarchy
					$terms_count = wp_count_terms($taxonomy, array(
						'hide_empty' => false,
					));
					$get_hierarchy = $terms_count < 2500;
				}

				if ($get_hierarchy) {
					$terms = wp_list_pluck($this->get_taxonomy_hierarchy($taxonomy), 'name');
				} else {
					$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false, 'fields' => 'names', 'update_term_meta_cache' => false));
				}
				set_transient($cache_key, $terms, WEEK_IN_SECONDS);
			}

			return apply_filters('vg_sheet_editor/data/taxonomy_terms', $terms, $taxonomy, $source);
		}

		/**
		 * Get users
		 * @param int $first Display first a specific user
		 * @param bool $with_keys include user ID as array keys.
		 * @return array
		 */
		function get_authors_list($first = null, $with_keys = false) {
			global $wpdb;


			if (!VGSE()->helpers->is_editor_page() || !post_type_supports(VGSE()->helpers->get_provider_from_query_string(), 'author')) {
				return array();
			}

			$cache_key = 'wpse_authors' . (int) $with_keys;
			$list = wp_cache_get($cache_key);

			if (!$list) {
				// We use a custom query for performance reasons
				$blogusers = $wpdb->get_results("SELECT ID,user_login FROM $wpdb->users WHERE 1=1
ORDER BY user_login ASC", OBJECT);
				$list = array();

				if (!empty($blogusers)) {
					foreach ($blogusers as $user) {
						if (is_numeric($first) && (int) $first === $user->ID) {

							if ($with_keys) {
								$list = array_merge(array($user->ID => $user->user_login), $list);
							} else {
								array_unshift($list, $user->user_login);
							}
						}

						if ($with_keys) {
							$list[$user->ID] = $user->user_login;
						} else {
							$list[] = $user->user_login;
						}
					}
				}
				wp_cache_set($cache_key, $list);
			}

			return array_map('esc_html', $list);
		}

		/**
		 * Prepare modified date for saving.
		 * 
		 * Changes date to Y-d-m H:i:s format
		 * @param string $date
		 * @param int $post_id
		 * @return string
		 */
		function prepare_modified_date_for_saving($date = null, $post_id) {
			$current_time = get_the_modified_date('Y-m-d H:i:s', $post_id);
			return $current_time;
		}

		/**
		 * Get user ID from username
		 * @param string $author username
		 * @return int
		 */
		function get_author_id_from_username($author) {
			$autor = get_user_by('login', $author);

			if (!$autor) {
				return false;
			}
			return $autor->ID;
		}

		/**
		 * Prepare date format for saving
		 * @param string $date
		 * @param int $post_id
		 * @return string
		 */
		function change_date_format_for_saving($date) {
			// note, we had some logic related to product dates. I removed it because it seemed unnecessary.
			// Keep in mind a possible rollback in case users report issues.
			// The date must always come in Y-m-d format, so we can easily change the format here.
			$date_timestamp = ( empty($date)) ? time() : strtotime($date);
			$savedate = date('Y-m-d H:i:s', $date_timestamp);
			return $savedate;
		}

		/**
		 * Save single post data, either post data or metadata.
		 * @param int $id
		 * @param mixed $content
		 * @param string $key
		 * @param string $type
		 * @return boolean
		 */
		function save_single_post_data($id, $content, $key, $type) {

			if ($type === 'post_data') {
				$my_post['ID'] = $id;
				if (strpos($key, 'post_') === false) {
					$my_post['post_' . $key] = $content;
				} else {
					$my_post[$key] = $content;
				}

				if (!empty($my_post['post_title'])) {
					$my_post['post_title'] = wp_strip_all_tags($my_post['post_title']);
				}
				$post_id = VGSE()->helpers->get_current_provider()->update_item_data($my_post, true);
				if (is_wp_error($post_id)) {
					return $post_id;
				}
			} else if ($type === 'meta_data' || $type === 'post_meta') {
				VGSE()->helpers->get_current_provider()->update_item_meta($id, $key, $content);
			}
			return true;
		}

		/**
		 * Get all post titles from post type
		 * @global type $wpdb
		 * @param string $post_type
		 * @param array $output ARRAY_N or ARRAY_A
		 * @param bool $flatten
		 * @return array
		 */
		function get_all_post_titles_from_post_type($post_type, $output = ARRAY_N, $flatten = false) {

			global $wpdb;
			$results = $wpdb->get_results("SELECT post_title FROM $wpdb->posts WHERE post_type = '" . esc_sql($post_type) . "' AND post_status IN ('" . implode("','", array_keys(VGSE()->helpers->get_current_provider()->get_statuses())) . "') ", $output);

			if ($flatten) {
				$results = VGSE()->helpers->array_flatten($results, array());
			}

			return $results;
		}

		/**
		 * Parse a category field from a CSV.
		 * Categories are separated by commas and subcategories are "parent > subcategory".
		 * Copied from WC core: WC_Product_CSV_Importer::parse_categories_field()
		 *
		 * @param string $value Field value.
		 * @param string $taxonomy Taxonomy key value.
		 * @return array of arrays with "parent" and "name" keys.
		 */
		public function parse_terms_string_for_saving($value, $taxonomy) {
			$out = array(
				'created' => 0,
				'term_ids' => array()
			);
			if (empty($value)) {
				return $out;
			}

			// Init object cache
			if (!isset($this->friendly_terms_to_ids_cache[$taxonomy])) {
				$this->friendly_terms_to_ids_cache[$taxonomy] = array();
			}

			$separator = (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',';
			$row_terms = array_map('trim', explode("$separator", $value));
			$categories = array();
			$created = 0;
			$wc_attributes = function_exists('wc_get_attribute_taxonomy_names') ? wc_get_attribute_taxonomy_names() : array();
			$woocommerce_taxonomies = array_merge($wc_attributes, array('product_cat', 'product_tag'));


			foreach ($row_terms as $row_term) {
				if (isset($this->friendly_terms_to_ids_cache[$taxonomy][$row_term]) && empty($_REQUEST['wpse_no_cache'])) {
					$categories[] = (int) $this->friendly_terms_to_ids_cache[$taxonomy][$row_term];
					continue;
				}

				$parent = null;
				$_terms = array_map('trim', explode('>', $row_term));
				$total = count($_terms);

				foreach ($_terms as $index => $_term) {
					// Check if category exists. Parent must be empty string or null if doesn't exists.
					// We can't use term_exists() because it converts the name to slug and it 
					// always returns the term D for term D+
					$term_exists_raw = get_terms(array(
						'taxonomy' => $taxonomy,
						'name' => $_term,
						'parent' => $parent,
						'fields' => 'ids',
						'hide_empty' => false
					));
					$term = ( is_array($term_exists_raw) && !empty($term_exists_raw)) ? current($term_exists_raw) : null;

					if ($term) {
						$term_id = (int) $term;
						// Don't allow users without capabilities to create new product categories or tags
					} elseif (in_array($taxonomy, $woocommerce_taxonomies) && !current_user_can('manage_product_terms')) {
						break;
					} else {
						$term = wp_insert_term($_term, $taxonomy, array('parent' => intval($parent)));

						if (is_wp_error($term)) {
							break; // We cannot continue if the term cannot be inserted.
						}

						$term_id = $term['term_id'];
						$created++;
					}

					// Only requires assign the last category.
					if (( 1 + $index ) === $total) {
						$categories[] = (int) $term_id;
						$this->friendly_terms_to_ids_cache[$taxonomy][$row_term] = (int) $term_id;
					} else {
						// Store parent to be able to insert or query categories based in parent ID.
						$parent = (int) $term_id;
					}
				}
			}

			$out = array(
				'created' => $created,
				'term_ids' => $categories
			);
			return $out;
		}

		/**
		 * Prepare post terms for saving.
		 * 
		 * Convert a string of terms separated by commas to a terms IDs array.
		 * If the term doesn´t exist, it creates it automatically.
		 * 
		 * @param string $categories
		 * @param string $taxonomy
		 * @return array
		 */
		function prepare_post_terms_for_saving($categories, $taxonomy) {
			$separator = (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',';

			// If this is one term, try to find by slug first
			if (!empty($categories) && strpos($categories, $separator) === false) {
				$term = get_term_by('slug', $categories, $taxonomy);
				if ($term) {
					return array($term->term_id);
				}
			}

			$parsed_data = $this->parse_terms_string_for_saving(html_entity_decode(sanitize_text_field($categories)), $taxonomy);
			if (!empty($parsed_data['created'])) {
				VGSE()->helpers->increase_counter('editions', $parsed_data['created']);
			}
			return array_unique($parsed_data['term_ids']);
		}

		/**
		 * Get posts count by post type
		 * @global obj $wpdb
		 * @param string $current_post post type
		 * @return int
		 */
		function total_posts($current_post) {
			$provider = VGSE()->helpers->get_data_provider($current_post);
			return $provider->get_total($current_post);
		}

		/**
		 * Get post status key from friendly name
		 * @param string $status
		 * @return boolean|string
		 */
		function get_status_key_from_name($status) {

			$statuses = VGSE()->helpers->get_current_provider()->get_statuses();

			if (!in_array($status, $statuses)) {
				return false;
			}

			$status_key = array_search($status, $statuses);

			return $status_key;
		}

		/*
		 * Devuelve el parent de cada post si lo tiene
		 */

		/**
		 * Get post ID from title
		 * @global obj $wpdb
		 * @param string $page_title
		 * @param string $output OBJECT , ARRAY_N , or ARRAY_A.
		 * @return ID
		 */
		function get_post_id_from_title($page_title, $post_type = null) {
			global $wpdb;

			if (empty($page_title)) {
				return null;
			}

			if (!$post_type) {
				$post_type = (isset($_REQUEST['post_type'])) ? sanitize_text_field($_REQUEST['post_type']) : 'post';
			}
			$post_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", $page_title, esc_sql($post_type)));
			if ($post_id) {
				return $post_id;
			}
			return null;
		}

		/**
		 * Get post statuses by friendly names.
		 * @return array
		 */
		function get_post_statuses() {

			$status = VGSE()->helpers->get_current_provider()->get_statuses();
			$list = array();

			foreach ($status as $item) {
				$list[] = esc_html($item);
			}

			return $list;
		}

		/**
		 * Creates or returns an instance of this class.
		 *
		 */
		static function get_instance() {
			if (null == WP_Sheet_Editor_Data::$instance) {
				WP_Sheet_Editor_Data::$instance = new WP_Sheet_Editor_Data();
			}
			return WP_Sheet_Editor_Data::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}