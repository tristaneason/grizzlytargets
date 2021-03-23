<?php

if (!class_exists('WPSE_WC_Products_Data_Formatting')) {

	class WPSE_WC_Products_Data_Formatting {

		static private $instance = false;
		var $wc_lookuptable_after_save_synced = array();
		var $wc_subscriptions_after_save_synced = array();
		var $wc_after_save_prices_synced = array();

		private function __construct() {
			
		}

		function init() {

			add_action('vg_sheet_editor/save_rows/after_saving_cell', array($this, 'product_cell_updated_on_spreadsheet'), 10, 7);
			add_action('vg_sheet_editor/save_rows/after_saving_post', array($this, 'product_updated_on_spreadsheet'), 10, 6);
			add_action('vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array($this, 'product_updated_with_formula'), 10, 8);
			add_filter('vg_sheet_editor/load_rows/output', array($this, 'format_sale_dates'), 10, 3);
			add_filter('vg_sheet_editor/save_rows/incoming_data', array($this, 'convert_sales_date_to_timestamp'), 10, 2);
			add_filter('vg_sheet_editor/provider/post/update_item_meta', array($this, 'filter_cell_data_for_saving'), 10, 3);
			add_filter('vg_sheet_editor/provider/post/get_item_meta', array($this, 'filter_cell_data_for_readings'), 10, 5);
		}

		function filter_cell_data_for_readings($value, $id, $cell_key, $single, $context) {
			if ($context !== 'read' || get_post_type($id) !== VGSE()->WC->post_type) {
				return $value;
			}
			if (in_array($cell_key, array('_crosssell_ids', '_upsell_ids'))) {
				if (!empty($value)) {
					$value = implode(', ', $value);
				} else {
					$value = '';
				}
			}
			if ($cell_key === '_featured') {
				$terms = VGSE()->helpers->get_current_provider()->get_item_terms($id, 'product_visibility');
				$separator = (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',';
				$term_names = array_map('trim', explode($separator, $terms));
				$value = ( in_array('featured', $term_names, true) ) ? 'featured' : '';
			}
			return $value;
		}

		function filter_cell_data_for_saving($new_value, $id, $key) {
			if (get_post_type($id) !== VGSE()->WC->post_type) {
				return $new_value;
			}

			if (in_array($key, array('_crosssell_ids', '_upsell_ids'))) {
				if (!is_array($new_value)) {
					$new_value = array_map('intval', explode(',', $new_value));
				}
			}
			if ($key === '_featured') {
				if ($new_value === 'no') {
					wp_remove_object_terms($id, 'featured', 'product_visibility');
				} elseif ($new_value) {
					wp_set_object_terms($id, 'featured', 'product_visibility', true);
				}
			}

			return $new_value;
		}

		/**
		 * Compatibility for the WooCommerce Subscriptions plugin, it requires prices synchronization
		 */
		function _sync_subscription_prices($product_id, $modified_data = array()) {
			if (!class_exists('WC_Subscriptions')) {
				return;
			}
			$product_lookup_keys = array('_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to');
			if (array_intersect($modified_data, $product_lookup_keys) && !in_array($product_id, $this->wc_subscriptions_after_save_synced)) {

				$_POST['variable_regular_price'] = get_post_meta($product_id, '_regular_price', true);
				// Sync the min variation price
				if (WC_Subscriptions::is_woocommerce_pre('3.0')) {
					$variable_subscription = wc_get_product($product_id);
					$variable_subscription->variable_product_sync();
				} else {
					WC_Product_Variable::sync($product_id);
				}

				$this->wc_subscriptions_after_save_synced[] = $product_id;
				$this->clear_wc_caches($product_id);
			}
		}

		/**
		 * Sync with product lookup table
		 * 
		 * WC 3.6 introduces a new lookup table, we need to sync some fields after every change.
		 * @see https://woocommerce.wordpress.com/2019/04/01/performance-improvements-in-3-6/
		 */
		function _sync_product_lookup_table($product_id, $modified_data = array()) {
			$product_lookup_keys = array('_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to', '_sku', '_stock', '_stock_status', '_manage_stock', '_downloadable', '_virtual', '_thumbnail_id');
			if (array_intersect($modified_data, $product_lookup_keys) && !in_array($product_id, $this->wc_lookuptable_after_save_synced)) {

				// We resave the regular price to force WC to execute the internal, protected method update_lookup_table()
				$regular_price = get_post_meta($product_id, '_regular_price', true);

				$product = wc_get_product($product_id);
				$product->set_regular_price(999999);
				$product->save();
				$product->set_regular_price($regular_price);
				$product->save();
				$this->wc_lookuptable_after_save_synced[] = $product_id;
			} else {
				// @todo Clear WC caches only when editing WC core fields, now it clears for all edits, including unrelated custom fields
				$this->clear_wc_caches($product_id);
			}
		}

		function clear_wc_caches($product) {
			if (is_int($product)) {
				$product = wc_get_product($product);
			}
			// Bail if product doesn't exist, in case it was deleted before clearing caches
			if (!is_object($product)) {
				return;
			}
			if (!function_exists('wc_delete_product_transients') || !class_exists('WC_Cache_Helper')) {
				return;
			}
			wc_delete_product_transients($product->get_id());
			if ($product->get_parent_id('edit')) {
				wc_delete_product_transients($product->get_parent_id('edit'));
				if (version_compare(WC()->version, '3.9.0') >= 0) {
					WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_parent_id('edit'));
				} else {
					WC_Cache_Helper::incr_cache_prefix('product_' . $product->get_parent_id('edit'));
				}
			}
			if (version_compare(WC()->version, '3.6.0') >= 0) {
				WC_Cache_Helper::invalidate_attribute_count(array_keys($product->get_attributes()));
			}
			if (version_compare(WC()->version, '3.9.0') >= 0) {
				WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_id());
			} else {
				WC_Cache_Helper::incr_cache_prefix('product_' . $product->get_id());
			}
		}

		/**
		 * Save product price.
		 * 
		 * This handles synchronization between regular and sale prices, and sale price dates.
		 *
		 * @param int $product_id
		 * @param float $regular_price
		 * @param float $sale_price
		 * @param string $date_from
		 * @param string $date_to
		 */
		function _sync_product_price($product_id, $modified_data = array()) {

			// sync prices
			$product_price_keys = array('_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to');
			$price_related_fields_were_updated = array_intersect($product_price_keys, $modified_data);

			// Exit If prices are already synced for this product, or if no price related field was updated
			if (in_array($product_id, $this->wc_after_save_prices_synced) || empty($price_related_fields_were_updated)) {
				return;
			}
			$this->wc_after_save_prices_synced[] = $product_id;

			$post_meta = get_post_meta($product_id);
			$price_data = wp_array_slice_assoc($post_meta, $product_price_keys);

			$regular_price = (!empty($price_data['_regular_price'])) ? $price_data['_regular_price'][0] : '';
			$sale_price = (!empty($price_data['_sale_price'])) ? $price_data['_sale_price'][0] : '';
			$date_from = (!empty($price_data['_sale_price_dates_from'])) ? $price_data['_sale_price_dates_from'][0] : '';
			$date_to = (!empty($price_data['_sale_price_dates_to'])) ? $price_data['_sale_price_dates_to'][0] : '';

			$product_id = absint($product_id);
			$regular_price = wc_format_decimal($regular_price);
			$sale_price = $sale_price === '' ? '' : wc_format_decimal($sale_price);
			$date_from = wc_clean($date_from);
			$date_to = wc_clean($date_to);

			// Convert dates to friendly format
			$date_from = ( is_numeric($date_from) ) ? date('Y-m-d', $date_from) : $date_from;
			$date_to = ( is_numeric($date_to) ) ? date('Y-m-d', $date_to) : $date_to;

			if (!empty($date_from)) {
				update_post_meta($product_id, '_sale_price_dates_from', strtotime($date_from));
			}
			if (!empty($date_to)) {
				update_post_meta($product_id, '_sale_price_dates_to', strtotime($date_to));
			}

			// Auto set date_from if empty
			if ($date_to && !$date_from) {
				update_post_meta($product_id, '_sale_price_dates_from', strtotime('NOW', current_time('timestamp')));
			}

			// Update price if on sale
			if ('' !== $sale_price && '' === $date_to && '' === $date_from) {
				update_post_meta($product_id, '_price', $sale_price);
			} else {
				update_post_meta($product_id, '_price', $regular_price);
			}

			if ('' !== $sale_price && $date_from && strtotime($date_from) < strtotime('NOW', current_time('timestamp'))) {
				update_post_meta($product_id, '_price', $sale_price);
			}

			if ($date_to && strtotime($date_to) < strtotime('NOW', current_time('timestamp'))) {
				update_post_meta($product_id, '_price', $regular_price);
				update_post_meta($product_id, '_sale_price_dates_from', '');
				update_post_meta($product_id, '_sale_price_dates_to', '');
			}
		}

		/**
		 * Convert spreadsheet rows array to WC API format
		 * @param array $rows
		 * @return array
		 */
		function convert_row_to_api_format($rows = array()) {
			$products = array();
			$out = array();

			$rows = VGSE()->helpers->add_post_type_to_rows($rows);


			$parent_products = wp_list_filter($rows, array(
				'post_type' => VGSE()->WC->post_type
			));

			$parent_products_ids = wp_list_pluck($parent_products, 'ID');

			$variations_rows = wp_list_filter($rows, array(
				'post_type' => 'product_variation'
			));

			foreach ($variations_rows as $data_obj) {
				$id = (int) $data_obj['ID'];
				$post_obj = get_post($id);
				$parent_id = $post_obj->post_parent;

				if (!isset($products[$parent_id])) {
					$products[$parent_id] = array();
				}
				$products[$parent_id][$id] = $data_obj;

				if (!in_array($parent_id, $parent_products_ids) && !isset($parent_products[$parent_id])) {
					$parent_products[$parent_id] = array(
						'ID' => $parent_id
					);
				}
			}



			if (empty($parent_products)) {
				return $out;
			}


			foreach ($parent_products as $parent_product) {
				// Es necesario solo cuando $parent_product es un objeto
				// $parent_product = get_object_vars($parent_product);

				$new_data = array();
				if (isset($parent_product['ID'])) {
					$new_data['ID'] = $parent_product['ID'];
				}
				if (isset($parent_product['post_title'])) {
					$new_data['name'] = $parent_product['post_title'];
				}
				if (isset($parent_product['_downloadable_files'])) {
					$new_data['downloads'] = ( is_string($parent_product['_downloadable_files']) ) ? array_values(json_decode(wp_unslash($parent_product['_downloadable_files']), true)) : $parent_product['_downloadable_files'];
					if (!empty($new_data['downloads'])) {
						$new_data['downloadable'] = true;
					}
				}
				if (isset($parent_product['_download_type'])) {
					$new_data['download_type'] = (!empty($parent_product['_download_type'])) ? $parent_product['_download_type'] : 'standard';
				}
				if (isset($parent_product['product_shipping_class'])) {
					$term = get_term_by('name', $parent_product['product_shipping_class'], 'product_shipping_class');
					$new_data['shipping_class'] = $term->slug;
				}
				if (isset($parent_product['_download_expiry'])) {
					$new_data['download_expiry'] = (int) $parent_product['_download_expiry'];
				}
				if (isset($parent_product['_download_limit'])) {
					$new_data['download_limit'] = $parent_product['_download_limit'];
				}
				if (isset($parent_product['post_name'])) {
					$new_data['slug'] = $parent_product['post_name'];
				}
				if (isset($parent_product['content'])) {
					$new_data['description'] = $parent_product['content'];
				}
				if (isset($parent_product['date'])) {
					$new_data['date_created'] = $parent_product['date'];
				}
				if (isset($parent_product['excerpt'])) {
					$new_data['short_description'] = $parent_product['excerpt'];
				}
				if (isset($parent_product['status'])) {
					$new_data['status'] = $parent_product['status'];
				}
				if (isset($parent_product['comment_status'])) {
					$new_data['reviews_allowed'] = VGSE()->WC->_do_booleable($parent_product['comment_status']);
				}

				$taxonomies = get_object_taxonomies(VGSE()->WC->post_type, 'objects');
				$separator = (!empty(VGSE()->options['be_taxonomy_terms_separator']) ) ? VGSE()->options['be_taxonomy_terms_separator'] : ',';

				if (!empty($taxonomies) && is_array($taxonomies)) {
					foreach ($taxonomies as $taxonomy) {
						if (strpos($taxonomy->name, 'pa_') === false) {
							continue;
						}
						$taxonomy_key = $taxonomy->name;

						if (isset($parent_product[$taxonomy_key])) {
							if (!isset($new_data['attributes'])) {
								$new_data['attributes'] = array();
							}
							$brands = explode($separator, $parent_product[$taxonomy_key]);
							$outbrand = array();
							foreach ($brands as $marca) {
								$brand = get_term_by('name', $marca, $taxonomy_key);
								$outbrand[] = $brand;
							}

							$new_data['attributes'][] = array(
								'name' => $taxonomy_key,
								'option' => $outbrand,
							);
						}
					}
				}
				if (isset($parent_product['_thumbnail_id'])) {
					$new_data['images'] = array(
						array(
							'id' => $parent_product['_thumbnail_id'],
							'position' => 0
						)
					);
				}
				if (isset($parent_product['product_cat'])) {
					$cats = explode($separator, $parent_product['product_cat']);
					$outcat = array();
					foreach ($cats as $cate) {
						$cat = get_term_by('name', $cate, 'product_cat');
						$outcat[] = $cat;
					}

					$new_data['categories'] = $outcat;
				}
				if (isset($parent_product['product_tag'])) {
					$tags = explode($separator, $parent_product['product_tag']);
					$outtag = array();
					foreach ($tags as $eti) {
						$tag = get_term_by('name', $eti, 'product_tag');
						$outtag[] = $tag;
					}

					$new_data['tags'] = $outtag;
				}
				if (isset($parent_product['_sku'])) {
					$new_data['sku'] = $parent_product['_sku'];
				}
				if (isset($parent_product['_regular_price'])) {
					$new_data['regular_price'] = $parent_product['_regular_price'];
				}
				if (isset($parent_product['_sale_price'])) {
					$new_data['sale_price'] = $parent_product['_sale_price'];
				}
				if (isset($parent_product['_weight'])) {
					$new_data['weight'] = $parent_product['_weight'];
				}
				if (isset($parent_product['_height'])) {
					$new_data['dimensions']['height'] = $parent_product['_height'];
				}
				if (isset($parent_product['_length'])) {
					$new_data['dimensions']['length'] = $parent_product['_length'];
				}
				if (isset($parent_product['_width'])) {
					$new_data['dimensions']['width'] = $parent_product['_width'];
				}
				if (isset($parent_product['_manage_stock'])) {
					$new_data['manage_stock'] = VGSE()->WC->_do_booleable($parent_product['_manage_stock']);
				}
				if (isset($parent_product['_stock_status'])) {
					$new_data['in_stock'] = VGSE()->WC->_do_booleable($parent_product['_stock_status']);
				}
				if (isset($parent_product['_stock'])) {
					$new_data['stock_quantity'] = $parent_product['_stock'];
				}
				if (isset($parent_product['_visibility'])) {
					$new_data['visible'] = $parent_product['_visibility'];
				}
				if (isset($parent_product['_product_image_gallery'])) {
					if (!isset($new_data['images'])) {
						$new_data['images'] = array();
					}
					$gallery = explode(',', $parent_product['_product_image_gallery']);

					foreach ($gallery as $image_index => $image_id) {
						$new_data['images'][] = array(
							'id' => (int) $image_id,
							'position' => $image_index + 1
						);
					}
				}
				if (isset($parent_product['_downloadable'])) {
					$new_data['downloadable'] = VGSE()->WC->_do_booleable($parent_product['_downloadable']);
				}
				if (isset($parent_product['_virtual'])) {
					$new_data['virtual'] = VGSE()->WC->_do_booleable($parent_product['_virtual']);
				}
				if (isset($parent_product['_sale_price_dates_from'])) {
					if (!empty($parent_product['_sale_price_dates_from'])) {
						$parent_product['_sale_price_dates_from'] = date('Y-m-d', strtotime($parent_product['_sale_price_dates_from']));
					}
					$new_data['date_on_sale_from'] = $parent_product['_sale_price_dates_from'];
				}
				if (isset($parent_product['_sale_price_dates_to'])) {
					if (!empty($parent_product['_sale_price_dates_to'])) {
						$parent_product['_sale_price_dates_to'] = date('Y-m-d', strtotime($parent_product['_sale_price_dates_to']));
					}
					$new_data['date_on_sale_to'] = $parent_product['_sale_price_dates_to'];
				}
				if (isset($parent_product['_sold_individually'])) {
					$new_data['sold_individually'] = VGSE()->WC->_do_booleable($parent_product['_sold_individually']);
				}
				if (isset($parent_product['_featured'])) {
					$new_data['featured'] = VGSE()->WC->_do_booleable($parent_product['_featured']);
				}
				if (isset($parent_product['_backorders'])) {
					$new_data['backorders'] = $parent_product['_backorders'];
				}
				if (isset($parent_product['_purchase_note'])) {
					$new_data['purchase_note'] = $parent_product['_purchase_note'];
				}

				if (isset($products[$parent_product['ID']])) {
					foreach ($products[$parent_product['ID']] as $index => $variation) {
						// Skip variation if we received the id and post type only
						if (count($variation) < 3 && isset($variation['ID']) && isset($variation['post_type'])) {
							continue;
						}
						if (!isset($new_data['variations'])) {
							$new_data['variations'] = array();
						}
						if (isset($variation['ID'])) {
							$new_data['variations'][$index]['id'] = $variation['ID'];
						}

						if (isset($variation['_variation_description'])) {
							$new_data['variations'][$index]['description'] = $variation['_variation_description'];
						}
						if (isset($variation['post_title'])) {
							$new_data['variations'][$index]['name'] = $variation['post_title'];
						}
						if (isset($variation['_downloadable_files'])) {
							$new_data['variations'][$index]['downloads'] = (is_string($variation['_downloadable_files'])) ? array_values(json_decode(wp_unslash($variation['_downloadable_files']), true)) : $variation['_downloadable_files'];
							if (!empty($new_data['variations'][$index]['downloads'])) {
								$new_data['variations'][$index]['downloadable'] = true;
							}
						}
						if (isset($variation['_download_type'])) {
							$new_data['variations'][$index]['download_type'] = (!empty($variation['_download_type'])) ? $variation['_download_type'] : 'standard';
						}
						if (isset($variation['product_shipping_class'])) {
							$term = get_term_by('name', $variation['product_shipping_class'], 'product_shipping_class');
							$new_data['variations'][$index]['shipping_class'] = $term->slug;
						}
						if (isset($variation['_download_expiry'])) {
							$new_data['variations'][$index]['download_expiry'] = (int) $variation['_download_expiry'];
						}
						if (isset($variation['_download_limit'])) {
							$new_data['variations'][$index]['download_limit'] = $variation['_download_limit'];
						}
						if (isset($variation['post_name'])) {
							$new_data['variations'][$index]['slug'] = $variation['post_name'];
						}

						if (isset($variation['_vgse_variation_enabled'])) {
							$new_data['variations'][$index]['visible'] = VGSE()->WC->_do_booleable($variation['_vgse_variation_enabled']);
						}
						if (isset($variation['comment_status'])) {
							$new_data['variations'][$index]['reviews_allowed'] = VGSE()->WC->_do_booleable($variation['comment_status']);
						}
						$taxonomies = get_object_taxonomies(VGSE()->WC->post_type, 'objects');

						if (!empty($taxonomies) && is_array($taxonomies)) {
							foreach ($taxonomies as $taxonomy) {
								if (strpos($taxonomy->name, 'pa_') === false) {
									continue;
								}
								$taxonomy_key = $taxonomy->name;

								if (isset($variation[$taxonomy_key])) {
									if (!isset($new_data['variations'][$index]['attributes'])) {
										$new_data['variations'][$index]['attributes'] = array();
									}
									// We save only the first term
									$brand_raw = current(explode($separator, $variation[$taxonomy_key]));
									$brand = get_term_by('name', $brand_raw, $taxonomy_key);
									$brand_slug = $brand->slug;

									$new_data['variations'][$index]['attributes'][$taxonomy_key] = array(
										'id' => wc_attribute_taxonomy_id_by_name($taxonomy_key),
										'name' => $taxonomy_key,
										'option' => $brand_slug,
									);
								}
							}
						}
						if (isset($variation['_thumbnail_id'])) {
							$new_data['variations'][$index]['image'] = (!empty($variation['_thumbnail_id']) ) ? array(
								'id' => $variation['_thumbnail_id'],
								'position' => 0
									) : null;
						}
						if (isset($variation['_sku'])) {
							$new_data['variations'][$index]['sku'] = $variation['_sku'];
						}
						if (isset($variation['_regular_price'])) {
							$new_data['variations'][$index]['regular_price'] = $variation['_regular_price'];
						}
						if (isset($variation['_sale_price'])) {
							$new_data['variations'][$index]['sale_price'] = $variation['_sale_price'];
						}
						if (isset($variation['_weight'])) {
							$new_data['variations'][$index]['weight'] = $variation['_weight'];
						}
						if (isset($variation['_height'])) {
							$new_data['variations'][$index]['dimensions']['height'] = $variation['_height'];
						}
						if (isset($variation['_length'])) {
							$new_data['variations'][$index]['dimensions']['length'] = $variation['_length'];
						}
						if (isset($variation['_width'])) {
							$new_data['variations'][$index]['dimensions']['width'] = $variation['_width'];
						}
						if (isset($variation['_manage_stock'])) {
							$new_data['variations'][$index]['manage_stock'] = VGSE()->WC->_do_booleable($variation['_manage_stock']);
						}
						if (isset($variation['_stock_status'])) {
							$new_data['variations'][$index]['in_stock'] = VGSE()->WC->_do_booleable($variation['_stock_status']);
						}
						if (isset($variation['_stock'])) {
							$new_data['variations'][$index]['stock_quantity'] = (int) $variation['_stock'];
						}
						if (isset($variation['_visibility'])) {
							$new_data['variations'][$index]['visible'] = $variation['_visibility'];
						}
						if (isset($variation['_downloadable'])) {
							$new_data['variations'][$index]['downloadable'] = VGSE()->WC->_do_booleable($variation['_downloadable']);
						}
						if (isset($variation['_virtual'])) {
							$new_data['variations'][$index]['virtual'] = VGSE()->WC->_do_booleable($variation['_virtual']);
						}
						if (isset($variation['_sale_price_dates_from'])) {
							if (!empty($variation['_sale_price_dates_from'])) {
								$variation['_sale_price_dates_from'] = date('Y-m-d', strtotime($variation['_sale_price_dates_from']));
							}
							$new_data['variations'][$index]['date_on_sale_from'] = $variation['_sale_price_dates_from'];
						}
						if (isset($variation['_sale_price_dates_to'])) {
							if (!empty($variation['_sale_price_dates_to'])) {
								$variation['_sale_price_dates_to'] = date('Y-m-d', strtotime($variation['_sale_price_dates_to']));
							}
							$new_data['variations'][$index]['date_on_sale_to'] = $variation['_sale_price_dates_to'];
						}
						if (isset($variation['_sold_individually'])) {
							$new_data['variations'][$index]['sold_individually'] = VGSE()->WC->_do_booleable($variation['_sold_individually']);
						}
						if (isset($variation['_backorders'])) {
							$new_data['variations'][$index]['backorders'] = $variation['_backorders'];
						}
						if (isset($variation['_purchase_note'])) {
							$new_data['variations'][$index]['purchase_note'] = $variation['_purchase_note'];
						}
					}
				}

				// Skip product if we received the id only
				if (count($new_data) === 1 && isset($new_data['ID'])) {
					continue;
				}
				$out[] = $new_data;
			}
			return $out;
		}

		function format_sale_dates($data, $query, $spreadsheet_columns) {
			if ($query['post_type'] !== VGSE()->WC->post_type || (!isset($spreadsheet_columns['_sale_price_dates_from']) && !isset($spreadsheet_columns['_sale_price_dates_to']) )) {
				return $data;
			}

			foreach ($data as $row_index => $row) {
				if (!empty($row['_sale_price_dates_from']) && is_numeric($row['_sale_price_dates_from'])) {
					$data[$row_index]['_sale_price_dates_from'] = date('Y-m-d', $row['_sale_price_dates_from']);
				}
				if (!empty($row['_sale_price_dates_to']) && is_numeric($row['_sale_price_dates_to'])) {
					$data[$row_index]['_sale_price_dates_to'] = date('Y-m-d', $row['_sale_price_dates_to']);
				}
			}
			return $data;
		}

		/**
		 * The product was updated
		 * @param string $post_type
		 * @param int $post_id
		 * @param string $key
		 * @param mixed $new_value
		 * @param array $cell_args
		 * @param array $spreadsheet_columns
		 * @return null
		 */
		function product_cell_updated_on_spreadsheet($post_type, $post_id, $key, $new_value, $cell_args, $spreadsheet_columns, $row) {
			if ($post_type !== VGSE()->WC->post_type) {
				return;
			}

			$this->_sync_product_terms($post_id, $new_value, $key, $cell_args['data_type'], $row);
		}

		/**
		 * The product was updated using a formula
		 * @param int $post_id
		 * @param string $initial_data
		 * @param string $modified_data
		 * @param string $column
		 * @param string $formula
		 * @param string $post_type
		 * @param array $cell_args
		 * @param array $spreadsheet_columns
		 * @return null
		 */
		function product_updated_with_formula($post_id, $initial_data, $modified_data, $column, $formula, $post_type, $cell_args, $spreadsheet_columns) {
			if ($post_type !== VGSE()->WC->post_type) {
				return;
			}

			$this->_sync_product_terms($post_id, $modified_data, $column, $cell_args['data_type']);
			$this->_sync_product_price($post_id, array($column));
			$this->_sync_product_lookup_table($post_id, array($column));
			$this->_sync_subscription_prices($post_id, array($column));
			$this->_maybe_migrate_variation_to_product($post_id, array($column => $modified_data));
		}

		function product_updated_on_spreadsheet($product_id, $item, $data, $post_type, $spreadsheet_columns, $settings) {
			if (!in_array($post_type, array(VGSE()->WC->post_type, 'product_variation'))) {
				return;
			}
			$this->_sync_product_price($product_id, array_keys($item));
			$this->_sync_product_lookup_table($product_id, array_keys($item));
			$this->_sync_subscription_prices($product_id, array_keys($item));
			$this->_maybe_migrate_variation_to_product($product_id, $item);

			// When we convert a product into a variation and the product has a status not supported by variations, automatically change it to "enabled variation"
			if (get_post_type($product_id) === 'product_variation' && !in_array(get_post_status($product_id), array('trash', 'publish', 'private'), true)) {
				wp_update_post(array(
					'ID' => $product_id,
					'post_status' => 'publish'
				));
			}
		}

		/**
		 * Callback when the product was updated.
		 * @param int $product_id
		 * @param mixed $new_value
		 * @param string $key
		 * @param string $data_source
		 */
		function _sync_product_terms($product_id, $new_value, $key, $data_source, $row = array()) {


			// sync woocommerce attributes
			if ($data_source === 'post_terms' && strpos($key, 'pa_') !== false) {
				// We can't use the provider's get_item_meta function because it uses the object cache
				// and we need to read the real value from the database everytime otherwise it won't save 
				// all attributes on next calls from the same ajax call
				$attributes = maybe_unserialize(get_post_meta($product_id, '_product_attributes', true));
				if (empty($attributes) || !is_array($attributes)) {
					$attributes = array();
				}
				$attribute_key = sanitize_title($key);
				$product_type = (is_array($row) && isset($row['product_type']) ) ? $row['product_type'] : VGSE()->WC->get_product_type($product_id);

				$new_attribute_settings = array();
				if ($product_type === 'variable') {
					$new_attribute_settings['is_variation'] = 1;
					if (!empty(VGSE()->options['wc_product_attributes_not_variation'])) {
						$attributes_used_for_variations = array_filter(array_map('trim', explode(',', VGSE()->options['wc_product_attributes_not_variation'])));
						foreach ($attributes_used_for_variations as $attribute_used_for_variations) {
							if (stripos($attribute_key, $attribute_used_for_variations) !== false) {
								$new_attribute_settings['is_variation'] = 0;
								break;
							}
						}
					}
				}
				if (!empty(VGSE()->options['wc_product_attributes_is_not_visible'])) {
					$attributes_visible = array_filter(array_map('trim', explode(',', VGSE()->options['wc_product_attributes_is_not_visible'])));
					$new_attribute_settings['is_visible'] = 1;
					foreach ($attributes_visible as $attribute_visible) {
						if (stripos($attribute_key, $attribute_visible) !== false) {
							$new_attribute_settings['is_visible'] = 0;
							break;
						}
					}
				}

				$current_attribute_settings = isset($attributes[$attribute_key]) ? $attributes[$attribute_key] : array(
					'name' => wc_clean($key),
					'value' => '',
					'is_taxonomy' => 1,
					'position' => count($attributes),
					'is_visible' => 1
				);

				// Add attribute association only if it doesn´t exist.
				$attributes[$attribute_key] = array_merge($current_attribute_settings, $new_attribute_settings);
				update_post_meta($product_id, '_product_attributes', $attributes);
			}
		}

		function convert_sales_date_to_timestamp($data, $settings) {
			if (!in_array($settings['post_type'], array(VGSE()->WC->post_type, 'product_variation'))) {
				return $data;
			}

			foreach ($data as $row_index => $row) {
				if (!empty($row['_sale_price_dates_from']) && !is_numeric($row['_sale_price_dates_from'])) {
					$data[$row_index]['_sale_price_dates_from'] = strtotime($row['_sale_price_dates_from']);
				}
				if (!empty($row['_sale_price_dates_to']) && !is_numeric($row['_sale_price_dates_to'])) {
					$data[$row_index]['_sale_price_dates_to'] = strtotime($row['_sale_price_dates_to']);
				}
			}

			return $data;
		}

		function _maybe_migrate_variation_to_product($product_id, $row) {
			global $wpdb;
			if (get_post_type($product_id) !== 'product' || empty($row['post_type']) || $row['post_type'] !== 'product') {
				return;
			}

			$post = get_post($product_id);
			if ($post->post_parent < 1) {
				return;
			}

			$variation_meta = get_post_meta($product_id);
			$parent_meta = get_post_meta($post->post_parent);
			$parent = get_post($post->post_parent);

			// We set the type as simple early because it has product_variation type and wc_get_product causes errors
			wp_set_object_terms($product_id, 'simple', 'product_type');
			$product = wc_get_product($product_id);
			if (!$product) {
				return;
			}

			$product->set_description($parent->post_content);

			$short_description = (!empty($variation_meta['_variation_description'])) ? $variation_meta['_variation_description'][0] : $parent->post_excerpt;
			$product->set_short_description($short_description);

			$product_cats_ids = wc_get_product_term_ids($parent->ID, 'product_cat');
			$product->set_category_ids($product_cats_ids);

			$product_tags_ids = wc_get_product_term_ids($parent->ID, 'product_tag');
			$product->set_tag_ids($product_tags_ids);

			$attributes = array();
			$position = 1;

			if (!empty($parent_meta['_product_attributes'][0])) {
				$parent_meta['_product_attributes'][0] = maybe_unserialize($parent_meta['_product_attributes'][0]);
			}
			foreach ($variation_meta as $meta_key => $meta_values) {
				if (strpos($meta_key, 'attribute_') === 0 && !empty($meta_values)) {
					$attribute_value = $meta_values[0];
					$attribute_key = str_replace('attribute_', '', $meta_key);
					$attribute_name = (!empty($parent_meta['_product_attributes'][0][$attribute_key])) ? $parent_meta['_product_attributes'][0][$attribute_key]['name'] : $attribute_key;
					$options = array();
					if (taxonomy_exists($attribute_key)) {
						$attribute_id = wc_attribute_taxonomy_id_by_name($attribute_key);
						$attribute_term = get_term_by('slug', $attribute_value, $attribute_key);
						if (is_object($attribute_term) && !is_wp_error($attribute_term)) {
							$options = array($attribute_term->term_id);
						}
					} else {
						$attribute_id = 0;
						$options = array($attribute_value);
					}
					$attribute = new WC_Product_Attribute();
					$attribute->set_id($attribute_id);
					$attribute->set_name($attribute_name);
					$attribute->set_options($options);
					$attribute->set_position($position);
					$attribute->set_visible(true);
					$attribute->set_variation(false);
					$attributes[] = $attribute;
					$position++;
				}

				foreach ($meta_values as $meta_value) {
					if ($meta_value === 'parent') {
						update_post_meta($product_id, $meta_key, get_post_meta($post->post_parent, $meta_key, true));
					}
				}
			}
			$product->set_attributes($attributes);
			$product->save();

			// Copy all meta data from the parent that doesn't exist on the variation
			// This will transfer the featured image if the variation doesn't have image
			// and other fields from other plugins
			foreach ($parent_meta as $meta_key => $meta_values) {
				// Omit the gallery because it might have images of other variations
				if ($meta_key === '_product_image_gallery') {
					continue;
				}
				$meta_value = current($meta_values);
				$new_product_meta = get_post_meta($product_id, $meta_key, true);
				if (!empty($meta_value) && empty($new_product_meta)) {
					update_post_meta($product_id, $meta_key, $meta_value);
				}
			}

			// The post_type is not being updated by wp_update_post for some reason
			$result = wp_update_post(array(
				'ID' => $product_id,
				'post_parent' => '0',
				'post_name' => '',
					), true);
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if (null == WPSE_WC_Products_Data_Formatting::$instance) {
				WPSE_WC_Products_Data_Formatting::$instance = new WPSE_WC_Products_Data_Formatting();
				WPSE_WC_Products_Data_Formatting::$instance->init();
			}
			return WPSE_WC_Products_Data_Formatting::$instance;
		}

		function __set($name, $value) {
			$this->$name = $value;
		}

		function __get($name) {
			return $this->$name;
		}

	}

}

if (!function_exists('WPSE_WC_Products_Data_Formatting_Obj')) {

	function WPSE_WC_Products_Data_Formatting_Obj() {
		return WPSE_WC_Products_Data_Formatting::get_instance();
	}

}
WPSE_WC_Products_Data_Formatting_Obj();
