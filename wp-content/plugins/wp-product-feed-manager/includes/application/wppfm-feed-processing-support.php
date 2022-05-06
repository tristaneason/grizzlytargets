<?php

/**
 * @package WP Product Feed Manager/Application/Functions
 * @version 1.4.0
 */

trait WPPFM_Processing_Support {

	protected $_selected_number;

	/**
	 * Returns the correct category for this specific product
	 *
	 * @param string $id
	 * @param string $main_category
	 * @param string $category_mapping
	 *
	 * @return string
	 */
	protected function get_mapped_category( $id, $main_category, $category_mapping ) {
		$result                 = false;
		$support_class          = new WPPFM_Feed_Support();
		$yoast_primary_category = WPPFM_Taxonomies::get_primary_cat( $id );
		$yoast_cat_is_selected  = $yoast_primary_category ? $support_class->category_is_selected( $yoast_primary_category[0]->term_id, $category_mapping ) : false;

		$product_categories = $yoast_primary_category && false !== $yoast_cat_is_selected ? $yoast_primary_category :
			wp_get_post_terms( $id, 'product_cat', array( 'taxonomy' => 'product_cat' ) ); // get the categories from a specific product in the shop

		if ( $product_categories && ! is_wp_error( $product_categories ) ) {
			// loop through each category
			foreach ( $product_categories as $category ) {
				// check if this category is selected in the category mapping
				$shop_category_id = $support_class->category_is_selected( $category->term_id, $category_mapping );

				// only add this product when at least one of the categories is selected in the category mapping
				if ( false !== $shop_category_id ) {
					switch ( $category_mapping[ $shop_category_id ]->feedCategories ) {
						case 'wp_mainCategory':
							$result = $main_category;
							break;

						case 'wp_ownCategory':
							$result = WPPFM_Taxonomies::get_shop_categories( $id, ' > ' );
							break;

						default:
							$result = $category_mapping[ $shop_category_id ]->feedCategories;
					}

					// found a selected category so now return the result
					return $result; // fixed ticket #1117

				} else { // if this product was not selected in the category mapping, it is possible it has been filtered in so map to the default category
					$result = $main_category;
				}
			}
		} else {
			if ( is_wp_error( $product_categories ) ) {
				echo wppfm_handle_wp_errors_response(
					$product_categories,
					sprintf(
						/* translators: %s: link to the support page */
						__(
							'2131 - Please try to refresh the page and open a support ticket at %s if the issue persists.',
							'wp-product-feed-manager'
						),
						WPPFM_SUPPORT_PAGE_URL
					)
				);
			}

			return false;
		}

		return $result;
	}

	/**
	 * Checks if this product has been filtered out of the feed
	 *
	 * @param string $feed_filter_strings
	 * @param array $product_data
	 *
	 * @return boolean
	 */
	protected function is_product_filtered( $feed_filter_strings, $product_data ) {
		if ( $feed_filter_strings ) {
			return $this->filter_result( json_decode( $feed_filter_strings[0]['meta_value'] ), $product_data ) ? true : false;
		} else {
			return false;
		}
	}

	protected function get_meta_parent_ids( $feed_id ) {
		$queries_class = new WPPFM_Queries();

		$query_result = $queries_class->get_meta_parents( $feed_id );
		$ids          = array();

		foreach ( $query_result as $result ) {
			array_push( $ids, $result['ID'] );
		}

		return $ids;
	}

	/**
	 * return an array with source column names from an attribute string
	 *
	 * @param string $value_string
	 *
	 * @return array
	 */
	protected function get_source_columns_from_attribute_value( $value_string ) {
		$source_columns = array();

		$value_object = json_decode( $value_string );

		if ( property_exists( $value_object, 'm' ) ) {
			foreach ( $value_object->m as $source ) {
				// TODO: I guess I should further reduce the "if" loops by combining them more then now
				if ( is_object( $source ) && property_exists( $source, 's' ) ) {
					if ( property_exists( $source->s, 'source' ) ) {
						if ( 'combined' !== $source->s->source ) {
							array_push( $source_columns, $source->s->source );
						} else {
							if ( property_exists( $source->s, 'f' ) ) {
								$source_columns = array_merge( $source_columns, $this->get_combined_sources_from_combined_string( $source->s->f ) );
							}
						}
					}
				}
			}
		}

		return $source_columns;
	}

	/**
	 * return an array with condition column names from an attribute string
	 *
	 * @param string $value_string
	 *
	 * @return array
	 */
	protected function get_condition_columns_from_attribute_value( $value_string ) {
		$condition_columns = array();

		$value_object = json_decode( $value_string );

		if ( property_exists( $value_object, 'm' ) ) {
			foreach ( $value_object->m as $source ) {
				if ( is_object( $source ) && property_exists( $source, 'c' ) ) {
					for ( $i = 0; $i < count( $source->c ); $i ++ ) {
						array_push( $condition_columns, $this->get_names_from_string( $source->c[ $i ]->{$i + 1} ) );
					}
				}
			}
		}

		return $condition_columns;
	}

	/**
	 * return an array with query column names from an attribute string
	 *
	 * @param string $value_string
	 *
	 * @return array
	 */
	protected function get_queries_columns_from_attribute_value( $value_string ) {
		$query_columns = array();

		$value_object = json_decode( $value_string );

		if ( property_exists( $value_object, 'v' ) ) {
			foreach ( $value_object->v as $changed_value ) {
				if ( property_exists( $changed_value, 'q' ) ) {
					for ( $i = 0; $i < count( $changed_value->q ); $i ++ ) {
						array_push( $query_columns, $this->get_names_from_string( $changed_value->q[ $i ]->{$i + 1} ) );
					}
				}
			}
		}

		return $query_columns;
	}

	/**
	 * extract a column name from a string
	 *
	 * @param string $string
	 *
	 * @return array
	 */
	protected function get_names_from_string( $string ) {
		$condition_string_array = explode( '#', $string );

		return $condition_string_array[1];
	}

	/**
	 * split the combined string into single combination items
	 *
	 * @param string $combined_string
	 *
	 * @return array
	 */
	public function get_combined_sources_from_combined_string( $combined_string ) {
		$result                = array();
		$combined_string_array = explode( '|', $combined_string );

		array_push( $result, $combined_string_array[0] );

		for ( $i = 1; $i < count( $combined_string_array ); $i ++ ) {
			$a = explode( '#', $combined_string_array[ $i ] );
			if ( array_key_exists( 1, $a ) ) {
				array_push( $result, $a[1] );
			}
		}

		return $result;
	}

	/**
	 * Gets the meta data from a specific field
	 *
	 * @param string $field
	 * @param stdClass $attributes
	 *
	 * @return stdClass attribute
	 */
	protected function get_meta_data_from_specific_field( $field, $attributes ) {
		$i = 0;

		while ( true ) {
			if ( $attributes[ $i ]->fieldName !== $field ) {
				$i ++;
				if ( $i > 1000 ) {
					break;
				}
			} else {
				return $attributes[ $i ];
			}
		}

		return new stdClass();
	}

	/**
	 * Generate the value of a field based on what the user has selected in filters, combined data, static data eg.
	 *
	 * @param array $product_data
	 * @param stdClass $field_meta_data
	 * @param string $main_category_feed_title
	 * @param string $row_category
	 * @param string $feed_language
	 * @param string $feed_currency
	 * @param array $relation_table
	 *
	 * @return array Returns an key=>value array of a specific product field where the key contains the field name and the value the field value
	 */
	protected function process_product_field( $product_data, $field_meta_data, $main_category_feed_title, $row_category, $feed_language, $feed_currency, $relation_table ) {

		$product_object[ $field_meta_data->fieldName ] = $this->get_correct_field_value(
			$field_meta_data,
			$product_data,
			$main_category_feed_title,
			$row_category,
			$feed_language,
			$feed_currency,
			$relation_table
		);

		return $product_object;
	}

	/**
	 * Processes a single field of a single product in the feed
	 *
	 * @param stdClass $field_meta_data containing the meta data of the field
	 * @param array $product_data containing the product data of the field
	 * @param string $main_category_feed_title main category title
	 * @param string $row_category complete category string
	 * @param string $feed_language language of the feed
	 * @param string $feed_currency currency of the feed
	 * @param array $relation_table table with the shop and merchant category relations
	 *
	 * @return string
	 */
	protected function get_correct_field_value( $field_meta_data, $product_data, $main_category_feed_title, $row_category, $feed_language, $feed_currency, $relation_table ) {
		$this->_selected_number = 0;

		// do not process category strings, but only fields that are requested
		if ( property_exists( $field_meta_data, 'fieldName' ) && $field_meta_data->fieldName !== $main_category_feed_title
			&& $this->meta_data_contains_category_data( $field_meta_data ) === false ) {

			$value_object = property_exists( $field_meta_data, 'value' ) && '' !== $field_meta_data->value ? json_decode( $field_meta_data->value ) : new stdClass();

			if ( property_exists( $field_meta_data, 'value' ) && '' !== $field_meta_data->value && property_exists( $value_object, 'm' ) ) { // seems to be something we need to work on
				$advised_source = property_exists( $field_meta_data, 'advisedSource' ) ? $field_meta_data->advisedSource : '';

				// get the end value depending on the filter settings
				$end_row_value = $this->get_correct_end_row_value( $value_object->m, $product_data, $advised_source );

			} else { // no queries, edit values or alternative sources for this field

				if ( property_exists( $field_meta_data, 'advisedSource' ) && '' !== $field_meta_data->advisedSource ) {
					$db_title = $field_meta_data->advisedSource;
				} else {
					$support_class = new WPPFM_Feed_Support();
					$source_title  = property_exists( $field_meta_data, 'fieldName' ) ? $field_meta_data->fieldName : '';
					$db_title      = $support_class->find_relation( $source_title, $relation_table );
				}

				$end_row_value = array_key_exists( $db_title, $product_data ) ? $product_data[ $db_title ] : '';
			}

			// change value if requested
			if ( property_exists( $field_meta_data, 'value' ) && '' !== $field_meta_data->value && property_exists( $value_object, 'v' ) ) {
				$pos = $this->_selected_number;

				if ( property_exists( $value_object, 'm' ) && property_exists( $value_object->m[ $pos ], 's' ) ) {
					$combination_string = property_exists( $value_object->m[ $pos ]->s, 'f' ) ? $value_object->m[ $pos ]->s->f : false;
					$is_money           = property_exists( $value_object->m[ $pos ]->s, 'source' ) ? wppfm_meta_key_is_money( $value_object->m[ $pos ]->s->source ) : false;
				} else {
					$combination_string = false;
					$is_money           = false;
				}

				$row_value     = ! $is_money ? $end_row_value : wppfm_prep_money_values( $end_row_value . $feed_language, $feed_currency );
				$end_row_value = $this->get_edited_end_row_value( $value_object->v, $row_value, $product_data, $combination_string, $feed_language, $feed_currency );
			}
		} else {
			$end_row_value = $row_category;
		}

		return $end_row_value;
	}

	protected function get_correct_end_row_value( $value, $product_data, $advised_source ) {
		$end_row_value = '';
		$nr_values     = count( $value ); // added @since 1.9.4
		$value_counter = 1; // added @since 1.9.4

		foreach ( $value as $filter ) {
			if ( true === $this->get_filter_status( $filter, $product_data ) && '' === $end_row_value ) {

				$end_row_value = $this->get_row_source_data( $filter, $product_data, $advised_source );
				break;
			} else {
				// no "or else" value seems to be selected
				if ( $value_counter >= $nr_values ) {
					return $end_row_value;
				} // added @since 1.9.4

				$this->_selected_number ++;
			}

			$value_counter ++; // added @since 1.9.4
		}

		// not found a condition that was correct so lets take the "for all other products" data to fetch the correct row_value
		if ( '' === $end_row_value ) {
			$end_row_value = $this->get_row_source_data( end( $value ), $product_data, $advised_source );
		}

		return $end_row_value;
	}

	/**
	 * Removes links from a the post content and post excerpts in a product data array.
	 *
	 * @since 2.6.0
	 *
	 * @param $product_data array
	 */
	protected function remove_links_from_product_data_description( &$product_data ) {
		$pattern     = '#<a.*?>(.*?)</a>#i'; // link pattern
		$replacement = '\1';

		if ( array_key_exists( 'post_content', $product_data ) ) {
			$product_data['post_content'] = preg_replace( $pattern, $replacement, $product_data['post_content'] );
		}

		if ( array_key_exists( 'post_excerpt', $product_data ) ) {
			$product_data['post_excerpt'] = preg_replace( $pattern, $replacement, $product_data['post_excerpt'] );
		}
	}

	protected function get_filter_status( $filter, $product_data ) {
		if ( ! empty( $filter ) && property_exists( $filter, 'c' ) ) {
			// check if the query is true for this field
			return $this->filter_result( $filter->c, $product_data );
		} else {
			// apparently there is no condition so the result is always true
			return true;
		}
	}

	protected function filter_result( $conditions, $product_data ) {
		$query_results = array();
		$support_class = new WPPFM_Feed_Support();

		// run each query on the data
		foreach ( $conditions as $condition ) {
			$condition_string = $support_class->get_query_string_from_query_object( $condition );

			$query_split = explode( '#', $condition_string );

			$row_result = $support_class->check_query_result_on_specific_row( $query_split, $product_data ) === true ? 'false' : 'true';

			array_push( $query_results, $query_split[0] . '#' . $row_result );
		}

		// return the final filter result, based on the specific results
		return $this->connect_query_results( $query_results );
	}

	/**
	 * Receives an array with condition results and generates a single end result based on the "and" or "or"
	 * connection between the conditions
	 *
	 * @param array with $results
	 *
	 * @return boolean
	 */
	protected function connect_query_results( $results ) {
		$and_results = array();
		$or_results  = array();

		if ( count( $results ) > 0 ) {
			foreach ( $results as $query_result ) {
				$result_split = explode( '#', $query_result );

				if ( '2' === $result_split[0] ) {
					array_push( $or_results, $and_results ); // store the current "and" result for processing as "or" result

					$and_results = array(); // clear the "and" array
				}

				$and_result = $result_split[1]; // === 'false' ? 'false' : 'true';

				array_push( $and_results, $and_result );
			}

			if ( count( $and_results ) > 0 ) {
				array_push( $or_results, $and_results );
			}

			if ( count( $or_results ) > 0 ) {
				$end_result = false;

				foreach ( $or_results as $or_result ) {
					$a = true;

					foreach ( $or_result as $and_array ) {
						if ( 'false' === $and_array ) {
							$a = false;
						}
					}

					if ( $a ) {
						$end_result = true;
					}
				}
			} else { // no "or" results found
				$end_result = false;
			}
		} else {
			$end_result = false;
		}

		return $end_result;
	}

	protected function get_row_source_data( $filter, $product_data, $advised_source ) {
		$row_source_data = '';

		if ( ! empty( $filter ) && property_exists( $filter, 's' ) ) {
			if ( property_exists( $filter->s, 'static' ) ) {
				$row_source_data = $filter->s->static;
			} elseif ( property_exists( $filter->s, 'source' ) ) {
				if ( 'combined' !== $filter->s->source ) {
					$row_source_data = array_key_exists( $filter->s->source, $product_data ) ? $product_data[ $filter->s->source ] : '';
				} else {
					$row_source_data = $this->generate_combined_string( $filter->s->f, $product_data );
				}
			}
		} else {
			// return the advised source data
			if ( '' !== $advised_source ) {
				$row_source_data = array_key_exists( $advised_source, $product_data ) ? $product_data[ $advised_source ] : '';
			}
		}

		return $row_source_data;
	}

	protected function generate_combined_string( $combined_sources, $row ) {
		$source_selectors_array = explode( '|', $combined_sources ); //split the combined source string in an array containing every single source
		$values_class           = new WPPFM_Feed_Value_Editors();
		$separators             = $values_class->combination_separators(); // array with all possible separators

		// if one of the row results is an array, the final output needs to be an array
		$result_is_array = $this->check_if_any_source_has_array_data( $source_selectors_array, $row );
		$result          = $result_is_array ? array() : '';

		if ( ! $result_is_array ) {
			$result = $this->make_combined_string( $source_selectors_array, $separators, $row, false );
		} else {
			for ( $i = 0; $i < count( $result_is_array ); $i ++ ) {
				$combined_string = $this->make_combined_string( $source_selectors_array, $separators, $row, $i );
				array_push( $result, $combined_string );
			}
		}

		return $result;
	}

	/**
	 * Distracts the keys from the $sources string (separated by a #) and looks if any of these keys
	 * are linked to an array in the $data_row
	 *
	 * @param array $sources
	 * @param array $data_row
	 *
	 * @return array|bool from the data_row or false
	 */
	protected function check_if_any_source_has_array_data( $sources, $data_row ) {
		foreach ( $sources as $source ) {
			$split_source = explode( '#', $source );

			if ( count( $split_source ) > 1 && 'static' === $split_source[1] ) {
				$last_key = 'static';
			} elseif ( 'static' === $split_source[0] ) {
				$last_key = 'static';
			} else {
				$last_key = array_pop( $split_source );
			}

			if ( array_key_exists( $last_key, $data_row ) && is_array( $data_row[ $last_key ] ) ) {
				return $data_row[ $last_key ];
			}
		}

		return false;
	}

	protected function meta_data_contains_category_data( $meta_data ) {
		if ( ! property_exists( $meta_data, 'value' ) || empty( $meta_data->value ) ) {
			return false;
		}

		$meta_obj = json_decode( $meta_data->value );

		return property_exists( $meta_obj, 't' ) ? true : false;
	}

	protected function get_edited_end_row_value( $change_parameters, $original_output, $product_data, $combination_string, $feed_language, $feed_currency ) {
		$result_is_filtered = false;
		$support_class      = new WPPFM_Feed_Support();
		$y                  = 0;
		$final_output       = '';

		for ( $i = 0; $i < ( count( $change_parameters ) - 1 ); $i ++ ) {
			if ( property_exists( $change_parameters[ $i ], 'q' ) ) {
				$filter_result = $this->filter_result( $change_parameters[ $i ]->q, $product_data );

				if ( true === $filter_result ) {
					$combined_data_elements = $combination_string ? $this->get_combined_elements( $product_data, $combination_string ) : '';
					$final_output           = $support_class->edit_value(
						$original_output,
						$change_parameters[ $i ]->{$i + 1},
						$combination_string,
						$combined_data_elements,
						$feed_language,
						$feed_currency
					);

					$result_is_filtered = true;
				}
			}

			$y ++;
		}

		if ( false === $result_is_filtered ) {
			$combined_data_elements = $combination_string ? $this->get_combined_elements( $product_data, $combination_string ) : '';
			$final_output           = $support_class->edit_value(
				$original_output,
				$change_parameters[ $y ]->{$y + 1},
				$combination_string,
				$combined_data_elements,
				$feed_language,
				$feed_currency
			);
		}

		return $final_output;
	}

	protected function get_combined_elements( $product_data, $combination_string ) {
		$result         = array();
		$found_all_data = true;

		$combination_elements = explode( '|', $combination_string );

		if ( false === strpos( $combination_elements[0], 'static#' ) ) {
			if ( array_key_exists( $combination_elements[0], $product_data ) ) {
				array_push( $result, $product_data[ $combination_elements[0] ] );
			} else {
				$found_all_data = false;
			}
		} else {
			$element = explode( '#', $combination_elements[0] );
			array_push( $result, $element[1] );
		}

		for ( $i = 1; $i <= count( $combination_elements ) - 1; $i ++ ) {
			$pos      = strpos( $combination_elements[ $i ], '#' );
			$selector = substr( $combination_elements[ $i ], ( false !== $pos ? $pos + 1 : 0 ) );

			if ( substr( $selector, 0, 7 ) === 'static#' ) {
				$selector = explode( '#', $selector );
				array_push( $result, $selector[1] );
			} elseif ( array_key_exists( $selector, $product_data ) ) {
				array_push( $result, $product_data[ $selector ] );
			} else {
				//array_push( $result, $selector );
				$found_all_data = false;
			}
		}

		if ( $found_all_data ) {
			return $result;
		} else {
			$message = sprintf( 'Missing the data for one or both combined elements of the combination %s in the product with id %s.', $combination_string, $product_data['ID'] );
			do_action( 'wppfm_feed_generation_message', $this->_feed_data->feedId, $message );
			return array();
		}
	}

	protected function make_combined_string( $source_selectors_array, $separators, $row, $array_pos ) {
		$combined_string = '';

		foreach ( $source_selectors_array as $source ) {
			$split_source = explode( '#', $source );

			// get the separator
			$separators_id = count( $split_source ) > 1 && 'static' !== $split_source[0] ? $split_source[0] : 0;
			$sep           = $separators[ $separators_id ];

			$data_key = count( $split_source ) > 1 && 'static' !== $split_source[0] ? $split_source[1] : $split_source[0];

			if ( ( array_key_exists( $data_key, $row ) && $row[ $data_key ] ) || 'static' === $data_key ) {
				if ( 'static' !== $data_key && ! is_array( $row[ $data_key ] ) ) { // not static and no array
					$combined_string .= $sep;
					$combined_string .= 'static' !== $data_key ? $row[ $data_key ] : $split_source[2];
				} elseif ( 'static' === $data_key ) { // static inputs
					$static_string    = count( $split_source ) > 2 ? $split_source[2] : $split_source[1];
					$combined_string .= $sep . $static_string;
				} else { // array inputs
					$input_array      = $row[ $data_key ][ $array_pos ];
					$combined_string .= $sep . $input_array;
				}
			}
		}

		return $combined_string;
	}

	/**
	 * get an array with the relations between the WooCommerce fields and the channel fields
	 *
	 * @return array
	 */
	public function get_channel_to_woocommerce_field_relations() {
		$relations = array();

		foreach ( $this->_feed->attributes as $attribute ) {

			// get the source name except for the category_mapping field
			if ( 'category_mapping' !== $attribute->fieldName ) {
				$source = $this->get_source_from_attribute( $attribute );
			}

			if ( ! empty( $source ) ) {
				// correct Google product category source
				if ( 'google_product_category' === $attribute->fieldName ) {
					$source = 'google_product_category';
				}

				// correct Google identifier exists source
				if ( 'identifier_exists' === $attribute->fieldName ) {
					$source = 'identifier_exists';
				}

				// fill the relations array
				$a = array(
					'field' => $attribute->fieldName,
					'db'    => $source,
				);
				array_push( $relations, $a );
			}
		}

		if ( empty( $relations ) ) {
			wppfm_write_log_file( 'Function get_channel_to_woocommerce_field_relations returned zero relations.' );
		}

		return $relations;
	}

	/**
	 * extract the source name from the attribute string
	 *
	 * @param array $attribute
	 *
	 * @return string
	 */
	protected function get_source_from_attribute( $attribute ) {

		$value_source = property_exists( $attribute, 'value' ) ? $this->get_source_from_attribute_value( $attribute->value ) : '';

		if ( ! empty( $value_source ) ) {
			$source = $value_source;
		} elseif ( property_exists( $attribute, 'advisedSource' ) && '' !== $attribute->advisedSource ) {
			$source = $attribute->advisedSource;
		} else {
			$source = $attribute->fieldName;
		}

		return $source;
	}

	/**
	 * extract the source value from the attribute string
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function get_source_from_attribute_value( $value ) {
		$source = '';

		if ( $value ) {
			$value_string = $this->get_source_string( $value );

			$value_object = json_decode( $value_string );

			if ( is_object( $value_object ) && property_exists( $value_object, 'source' ) ) {
				$source = $value_object->source;
			}
		}

		return $source;
	}

	/**
	 * get the value
	 *
	 * @param string $value_string
	 *
	 * @return string
	 */
	protected function get_source_string( $value_string ) {
		$source_string = '';

		if ( ! empty( $value_string ) ) {
			$value_object = json_decode( $value_string );

			if ( property_exists( $value_object, 'm' )
			     && ! empty( $value_object->m[0] )
			     && property_exists( $value_object->m[0], 's' ) ) {
				$source_string = wp_json_encode( $value_object->m[0]->s );
			}
		}

		return $source_string;
	}

	/**
	 * makes an xml string of one product including its variations
	 *
	 * @param array $data
	 * @param string $category_name
	 * @param string $description_name
	 *
	 * @return string
	 */
	protected function convert_data_to_xml( $data, $category_name, $description_name, $channel ) {
		return $data ? $this->make_xml_string_row( $data, $category_name, $description_name, $channel ) : '';
	}

	/**
	 * makes an xml string for one product
	 *
	 * @param   array   $product            Contains all the data from the product.
	 * @param   string  $category_name      Selected category name.
	 * @param   string  $description_name   The name of the description.
	 * @param   string  $channel            Contains the channel id.
	 *
	 * @return string
	 */
	protected function make_xml_string_row( $product, $category_name, $description_name, $channel ) {
		$product_node_name    = function_exists( 'product_node_name' ) ? product_node_name( $channel ) : 'item';
		$node_pre_tag_name    = function_exists( 'get_node_pretag' ) ? get_node_pretag( $channel ) : 'g:';
		$product_node         = apply_filters( 'wppfm_xml_product_node_name', $product_node_name, $channel );
		$node_pre_tag         = apply_filters( 'wppfm_xml_product_pre_tag_name', $node_pre_tag_name, $channel );
		$tags_with_sub_tags   = $this->_channel_class->keys_that_have_sub_tags();
		$tags_repeated_fields = $this->_channel_class->keys_that_can_be_used_more_than_once();
		$sub_keys_for_subs    = $this->_channel_class->sub_keys_for_sub_tags();

		$this->_channel_class->add_xml_sub_tags( $product, $sub_keys_for_subs, $tags_with_sub_tags, $node_pre_tag );
		$xml_string = "<$product_node>";

		// for each product value item
		foreach ( $product as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$xml_string .= $this->make_xml_string( $key, $value, $category_name, $description_name, $node_pre_tag, $tags_with_sub_tags, $tags_repeated_fields, $channel );
			} else {
				$xml_string .= $this->make_array_string( $key, $value, $node_pre_tag, $channel );
			}
		}

		$xml_string .= "</$product_node>";

		return $xml_string;
	}

	/**
	 * makes an csv string of one product including its variations
	 *
	 * @param array $data
	 * @param array $active_fields
	 * @param string $csv_separator
	 *
	 * @return string
	 */
	protected function convert_data_to_csv( $data, $active_fields, $csv_separator ) {
		if ( $data ) {
			if ( count( $data ) > count( $active_fields ) ) {
				$support_class = new WPPFM_Feed_Support();
				$support_class->correct_active_fields_list( $active_fields );
			}

			// the first row in a csv file should contain the index, the following rows the data
			return $this->make_comma_separated_string_from_data_array( $data, $active_fields, $this->_feed_data->channel, $csv_separator );
		} else {
			return '';
		}
	}

	/**
	 * makes a tab separated string for a tsv file
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	protected function convert_data_to_tsv( $data ) {
		if ( $data ) {
			return $this->make_feed_string_from_data_array( $data, "\t" );
		} else {
			return '';
		}
	}

	protected function convert_data_to_txt( $data, $separator ) {
		if ( $data ) {
			return $this->make_feed_string_from_data_array( $data, $separator );
		} else {
			return '';
		}
	}

	/**
	 * takes one row data and converts it to a tab delimited string
	 *
	 * @param array $row_data
	 * @param string $separator
	 *
	 * @return string
	 */
	protected function make_feed_string_from_data_array( $row_data, $separator ) {
		$row_string = '';

		foreach ( $row_data as $row_item ) {
			$a_row_item     = ! is_array( $row_item ) ? preg_replace( "/\r|\n/", "", $row_item ) : implode( ', ', $row_item );
			$clean_row_item = strip_tags( $a_row_item );
			$row_string    .= $clean_row_item;
			$separator === 'TAB' ? $row_string .= "\t" : $row_string .= $separator;
		}

		$row = $separator === 'TAB' ? trim( $row_string ) : trim( $row_string, $separator ); // removes the separator at the end of the line

		return $row . "\r\n";
	}

	/**
	 * Takes the data for one row and converts it to a comma separated string that fits into the feed.
	 *
	 * @param   array   $row_data       Array with the attribute name => attribute data.
	 * @param   array   $active_fields  Array containing the attributes that are active and need to go into the feed.
	 * @param   string  $channel        Channel id.
	 * @param   string  $separator      Requested data separator (default ,).
	 *
	 * @return string
	 */
	public function make_comma_separated_string_from_data_array( $row_data, $active_fields, $channel, $separator = ',' ) {
		$row_string = '';

		$quotes_not_allowed = channel_requires_no_quotes_on_empty_attributes( $channel );

		// @since 2.11.0 allows choosing another separator for array data.
		$separator_for_arrays = apply_filters( 'wppfm_separator_for_arrays_in_csv_feed', '|' );

		// Loop through the active attributes.
		foreach ( $active_fields as $row_item ) {
			if ( array_key_exists( $row_item, $row_data ) ) {
				$clean_row_item = ! is_array( $row_data[ $row_item ] ) ? preg_replace( "/\r|\n/", '', $row_data[ $row_item ] ) : implode( $separator_for_arrays, $row_data[ $row_item ] );
			} else {
				$clean_row_item = '';
			}

			$quotes = $quotes_not_allowed && '' === $clean_row_item ? '' : '"';

			$remove_double_quotes_from_string = str_replace( '"', "'", $clean_row_item );
			$row_string                      .= $quotes . $remove_double_quotes_from_string . $quotes . $separator;
		}

		$row = rtrim( $row_string, $separator ); // Removes the comma at the end of the line.

		return $row . "\r\n";
	}

	/**
	 * Makes the header string for a csv or tsv file.
	 *
	 * @param array $active_fields
	 * @param string $separator
	 *
	 * @return string
	 */
	protected function make_custom_header_string( $active_fields, $separator ) {
		$header = implode( $separator, $active_fields );

		return $header . "\r\n";
	}

	/**
	 * make an array of product element strings
	 *
	 * @param string $key
	 * @param array $value
	 * @param string $google_node_pre_tag
	 * @param string $channel
	 *
	 * @return string
	 */
	protected function make_array_string( $key, $value, $google_node_pre_tag, $channel ) {
		$xml_strings          = '';
		$tags_with_sub_tags   = $this->_channel_class->keys_that_have_sub_tags();
		$tags_repeated_fields = $this->_channel_class->keys_that_can_be_used_more_than_once();

		for ( $i = 0; $i < count( $value ); $i ++ ) {
			$xml_key      = 'Extra_Afbeeldingen' === $key ? 'Extra_Image_' . ( $i + 1 ) : $key; // required for Beslist.nl
			$xml_strings .= $this->make_xml_string( $xml_key, $value[ $i ], '', '', $google_node_pre_tag, $tags_with_sub_tags, $tags_repeated_fields, $channel );
		}

		return $xml_strings;
	}

	/**
	 * Generates an xml node.
	 *
	 * Returns an xml node for a product tag and uses the product data to make the node.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key                   Note id.
	 * @param string $xml_value             Note value.
	 * @param string $category_name         Category name.
	 * @param string $description_name      Description name.
	 * @param string $google_node_pre_tag   Pre node tag.
	 * @param array  $tags_with_sub_tags    Array with tags that have a sub tag construction.
	 * @param array  $tags_repeated_fields  Array with tags that are allowed to be placed in the feed more than once
	 * @param string $channel               Selected channel id.
	 *
	 * @return string    Node string in xml format eg. <id>43</id>.
	 */
	protected function make_xml_string( $key, $xml_value, $category_name, $description_name, $google_node_pre_tag, $tags_with_sub_tags, $tags_repeated_fields, $channel ) {
		$xml_string     = '';
		$repeated_field = ! in_array( $key, $tags_repeated_fields ) ? false : true;
		$subtag_sep     = apply_filters( 'wppfm_sub_tag_separator', '||' );

		if ( substr( $xml_value, 0, 5 ) === '!sub:' ) {
			$sub_array = explode( "|", $xml_value );
			$sa        = $sub_array[0];
			$st        = explode( ":", $sa );
			$sub_tag   = $st[1];
			$xml_value = "<$google_node_pre_tag$sub_tag>$sub_array[1]</$google_node_pre_tag$sub_tag>";
		}

		if ( $repeated_field && ! is_array( $xml_value ) ) {
			$xml_value = explode( $subtag_sep, $xml_value );
		}

		// keys to be added in a CDATA bracket to the xml feed
		$cdata_keys = apply_filters( 'wppfm_cdata_keys', array(
			$category_name,
			$description_name,
			'title'
		) );

		if ( ! is_array( $xml_value ) && ! in_array( $key, $tags_with_sub_tags ) ) {
			if ( in_array( $key, $cdata_keys ) ) {
				$xml_value = $this->convert_to_character_data_string( $xml_value ); // put in a ![CDATA[...]] bracket
			} else {
				$xml_value = $this->convert_to_xml_value( $xml_value );
			}
		}

		if ( $key !== '' ) {
			if ( is_array( $xml_value ) && $repeated_field ) {
				foreach ( $xml_value as $value_item ) {
					$xml_string .= $this->add_xml_string( $key, $value_item, $google_node_pre_tag );
				}
			} else {
				$xml_string = $this->add_xml_string( $key, $xml_value, $google_node_pre_tag );
			}
		}

		return $xml_string;
	}

	/**
	 * Generates a single xml line string
	 *
	 * @since 1.9.0
	 *
	 * @param string $key
	 * @param string $xml_value
	 * @param string $google_node_pre_tag
	 *
	 * @return string
	 */
	protected function add_xml_string( $key, $xml_value, $google_node_pre_tag ) {
		$not_allowed_characters = array( ' ', '-' );
		$clean_key              = str_replace( $not_allowed_characters, '_', $key );

		// @since 2.13.0
		$element_attribute        = apply_filters( 'wppfm_xml_element_attribute', '', $key, $xml_value );
		$element_attribute_string = '' !== $element_attribute ? ' ' . $element_attribute : '';

		return "<$google_node_pre_tag$clean_key$element_attribute_string>$xml_value</$google_node_pre_tag$clean_key>";
	}

	/**
	 * converts an ordinary xml string into a CDATA string as long as it's not only a numeric value;
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	protected function convert_to_character_data_string( $string ) {
		return ! is_numeric($string) ? "<![CDATA[ $string ]]>" : $string;
	}

	/**
	 * can be overridden by a channel specific function in its class-feed.php
	 *
	 * @since 1.9.0
	 *
	 * @param   $product                array   Pointer to the product data.
	 * @param   $sub_keys_for_subs      array   Array with the tags that can be placed in the feed as a sub tag (eg. <loyalty_points><ratio>).
	 * @param   $tags_repeated_fields   array   Array with tags of fields that can have more than one instance in the feed.
	 * @param   $node_pre_tag           string  The channel dependant pre tag (eg. g: for Google Feeds).
	 *
	 * @return  array   The product with the correct xml tags.
	 */
	public function add_xml_sub_tags( &$product, $sub_keys_for_subs, $tags_repeated_fields, $node_pre_tag ) {
		$sub_tags = array_intersect_key( $product, array_flip( $sub_keys_for_subs ) );

		if ( count( $sub_tags ) < 1 ) {
			return $product;
		}

		$subtag_sep = apply_filters( 'wppfm_sub_tag_separator', '||' );
		$tags_value = array();

		foreach ( $sub_tags as $key => $value ) {
			$split = explode( '-', $key );

			if ( in_array( $split[0], $tags_repeated_fields ) ) {
				$tags_counter = 0;
				$value_array  = is_array( $value ) ? $value : explode( $subtag_sep, $value );

				foreach ( $value_array as $sub_value ) {
					$prev_string                 = array_key_exists( $tags_counter, $tags_value ) ? $tags_value[ $tags_counter ] : '';
					$tags_value[ $tags_counter ] = $prev_string . '<' . $node_pre_tag . $split[1] . '>' . $sub_value . '</' . $node_pre_tag . $split[1] . '>';
					$tags_counter ++;
				}
			} else {
				$tags_value  = array_key_exists( $split[0], $product ) ? $product[ $split[0] ] : '';
				$tags_value .= '<' . $node_pre_tag . $split[1] . '>' . $value . '</' . $node_pre_tag . $split[1] . '>';
			}

			unset( $product[ $key ] );
			$product[ $split[0] ] = $tags_value;
		}

		return $product;
	}

	/**
	 * can be overridden by a channel specific function in its class-feed.php
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public function keys_that_have_sub_tags() {
		return array();
	}

	/**
	 * can be overridden by a channel specific function in its class-feed.php
	 *
	 * @since 2.1.0
	 *
	 * @return array
	 */
	public function sub_keys_for_sub_tags() {
		return array();
	}

	/**
	 * can be overridden by a channel specific function in its class-feed.php
	 *
	 * @since 1.9.0
	 *
	 * @return array
	 */
	public function keys_that_can_be_used_more_than_once() {
		return array();
	}

	/**
	 * replaces certain characters to get a valid xml value
	 *
	 * @param string $value_string
	 *
	 * @return string
	 */
	public function convert_to_xml_value( $value_string ) {
		$string_without_tags = strip_tags( $value_string );
		$prep_string         = str_replace(
			array(
				'&amp;',
				'&lt;',
				'&gt;',
				'&apos;',
				'&quot;',
				'&nbsp;',
			),
			array(
				'&',
				'<',
				'>',
				'\'',
				'"',
				'nbsp;',
			),
			$string_without_tags
		);

		$clean_xml_string = str_replace(
			array(
				'&',
				'<',
				'>',
				'\'',
				'"',
				'nbsp;',
				'`',
			),
			array(
				'&amp;',
				'&lt;',
				'&gt;',
				'&apos;',
				'&quot;',
				' ',
				'',
			),
			$prep_string
		);

		return $clean_xml_string;
	}

	/**
	 * get formal woocommerce custom fields data
	 *
	 * @param string $id
	 * @param string $parent_product_id @since 2.0.9
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_custom_field_data( $id, $parent_product_id, $field ) {
		$custom_string = '';
		$taxonomy      = 'pa_' . $field;
		$custom_values = get_the_terms( $id, $taxonomy );

		if ( ! $custom_values && 0 !== $parent_product_id ) {
			$custom_values = get_the_terms( $parent_product_id, $taxonomy );
		}

		if ( $custom_values ) {
			foreach ( $custom_values as $custom_value ) {
				$custom_string .= $custom_value->name . ', ';
			}
		}

		return $custom_string ? substr( $custom_string, 0, - 2 ) : '';
	}

	/**
	 * get third party custom field data
	 *
	 * @since 1.6.0
	 *
	 * @param string $feed_id
	 * @param string $parent_product_id @since 2.0.9
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_third_party_custom_field_data( $feed_id, $parent_product_id, $field ) {
		$result        = '';
		$product_brand = '';

		// YITH Brands plugin
		if ( get_option( 'yith_wcbr_brands_label' ) === $field ) { // YITH Brands plugin active
			if ( has_term( '', 'yith_product_brand', $feed_id ) ) {
				$product_brand = get_the_terms( $feed_id, 'yith_product_brand' );
			}

			if ( ! $product_brand && 0 !== $parent_product_id && has_term( '', 'yith_product_brand', $parent_product_id ) ) {
				$product_brand = get_the_terms( $parent_product_id, 'yith_product_brand' );
			}

			if ( $product_brand && ! is_wp_error( $product_brand ) ) {
				foreach ( $product_brand as $brand ) {
					$result .= $brand->name . ', ';
				}
			}
		}

		// WooCommerce Brands plugin
		if (
			in_array(
				'woocommerce-brands/woocommerce-brands.php',
				apply_filters(
					'active_plugins',
					get_option( 'active_plugins' )
				)
			)
		) {

			if ( has_term( '', 'product_brand', $feed_id ) ) {
				$product_brand = get_the_terms( $feed_id, 'product_brand' );
			}

			if ( ! $product_brand && 0 !== $parent_product_id && has_term( '', 'product_brand', $parent_product_id ) ) {
				$product_brand = get_the_terms( $parent_product_id, 'product_brand' );
			}

			if ( $product_brand && ! is_wp_error( $product_brand ) ) {
				foreach ( $product_brand as $brand ) {
					$result .= $brand->name . ', ';
				}
			} elseif ( is_wp_error( $product_brand ) ) {
				do_action( 'wppfm_feed_generation_warning', $feed_id, $product_brand ); // @since 2.3.0
			}
		}

		return $result ? substr( $result, 0, - 2 ) : '';
	}

	/**
	 * adds data to the product that require a procedure to get
	 *
	 * @param object $product
	 * @param array $active_field_names
	 * @param string $selected_language
	 * @param string $selected_currency
	 * @param string $feed_id
	 *
	 * @return bool
	 */
	protected function add_procedural_data( &$product, $active_field_names, $selected_language, $selected_currency, $feed_id ) {
		$woocommerce_product = wc_get_product( $product->ID );

		if ( false === $woocommerce_product ) {
			$msg = sprintf( 'Failed to get the WooCommerce products procedural data from product %s.', $product->ID );
			do_action( 'wppfm_feed_generation_warning', $feed_id, $msg ); // @since 2.3.0

			return false;
		}

		$woocommerce_parent_id      = $woocommerce_product->get_parent_id();
		$woocommerce_product_parent = $woocommerce_product && ( $woocommerce_product->is_type( 'variable' ) ||
			$woocommerce_product->is_type( 'variation' ) ) ? wc_get_product( $woocommerce_parent_id ) : null;

		if ( false === $woocommerce_product_parent || null === $woocommerce_product_parent ) {
			// this product has no parent id so it is possible this is the main of a variable product
			// So to make sure the general variation data like min_variation_price are available, copy the product
			// in the parent product
			$woocommerce_product_parent = $woocommerce_product;
		}

		if ( in_array( 'shipping_class', $active_field_names ) ) {
			// Get the shipping class.
			$shipping_class = $woocommerce_product->get_shipping_class();

			// If the shipping class in the product was empty and the product has a parent, then check if the parent has a shipping class.
			$product->shipping_class = ! $shipping_class && $woocommerce_product_parent ? $woocommerce_product_parent->get_shipping_class() : $shipping_class;
		}

		if ( in_array( 'permalink', $active_field_names ) ) {
			$permalink = get_permalink( $product->ID );
			if ( false === $permalink && 0 !== $woocommerce_parent_id ) {
				$permalink = get_permalink( $woocommerce_parent_id );
			}

			// WPML support
			$permalink = has_filter( 'wppfm_get_wpml_permalink' )
				? apply_filters( 'wppfm_get_wpml_permalink', $permalink, $selected_language ) : $permalink;

			// WOOCS support since @2.29.0
			$permalink = has_filter( 'wppfm_get_woocs_currency' )
				? apply_filters( 'wppfm_woocs_product_permalink', $permalink, $selected_currency ) : $permalink;

			$product->permalink = $permalink;
		}

		if ( in_array( 'attachment_url', $active_field_names ) ) {
			// WPML support -> Returns an elements ID in the selected language.
			$object_id      = has_filter( 'wpml_object_id' ) ? apply_filters( 'wpml_object_id', $product->ID, 'attachment', true ) : $product->ID;
			$attachment_url = wp_get_attachment_image_url( get_post_thumbnail_id( $object_id ), '' );

			// If the attachment url is empty and the product has a parent try getting the attachment url of the parent.
			if ( false === $attachment_url && 0 !== $woocommerce_parent_id ) {
				// WPML support -> Returns an elements ID in the selected language.
				$parent_object_id = has_filter( 'wpml_object_id' ) ? apply_filters( 'wpml_object_id', $woocommerce_parent_id, 'attachment', true ) : $woocommerce_parent_id;
				$attachment_url   = wp_get_attachment_image_url( get_post_thumbnail_id( $parent_object_id ), '' );
			}

			// WPML support -> Filter the permalink and convert it to a language-specific permalink.
			$product->attachment_url = has_filter( 'wppfm_get_wpml_permalink' )
				? apply_filters( 'wppfm_get_wpml_permalink', $attachment_url, $selected_language ) : $attachment_url;
		}

		if ( in_array( 'product_cat', $active_field_names ) ) {
			$product->product_cat = WPPFM_Taxonomies::get_shop_categories( $product->ID );
			if ( '' === $product->product_cat && 0 !== $woocommerce_parent_id ) {
				$product->product_cat = WPPFM_Taxonomies::get_shop_categories( $woocommerce_parent_id );
			}
		}

		if ( in_array( 'product_cat_string', $active_field_names ) ) {
			$product->product_cat_string = WPPFM_Taxonomies::make_shop_taxonomies_string( $product->ID );
			if ( '' === $product->product_cat_string && 0 !== $woocommerce_parent_id ) {
				$product->product_cat_string = WPPFM_Taxonomies::make_shop_taxonomies_string( $woocommerce_parent_id );
			}
		}

		if ( in_array( 'last_update', $active_field_names ) ) {
			$product->last_update = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
		}

		if ( in_array( '_wp_attachement_metadata', $active_field_names ) ) {
			$attachment_id                     = 0 === $woocommerce_parent_id ? $product->ID : $woocommerce_parent_id;
			$product->_wp_attachement_metadata = $this->get_product_image_gallery( $attachment_id, $selected_language );
		}

		if ( in_array( 'product_tags', $active_field_names ) ) {
			$product->product_tags = $this->get_product_tags( $product->ID );
		}

		if ( in_array( 'wc_currency', $active_field_names ) ) {
			// WPML support
			$product->wc_currency = has_filter( 'wppfm_get_wpml_currency' )
				? apply_filters( 'wppfm_get_wpml_currency', get_woocommerce_currency(), $selected_language ) : get_woocommerce_currency();
		}

		if ( $woocommerce_product_parent && ( $woocommerce_product_parent->is_type( 'variable' ) || $woocommerce_product_parent->is_type( 'variation' ) ) ) {
			if ( in_array( '_min_variation_price', $active_field_names ) ) {
				$product->_min_variation_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_price(), $selected_language, $selected_currency );
			}

			if ( in_array( '_max_variation_price', $active_field_names ) ) {
				$product->_max_variation_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_price( 'max' ), $selected_language, $selected_currency );
			}

			if ( in_array( '_min_variation_regular_price', $active_field_names ) ) {
				$product->_min_variation_regular_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_regular_price(), $selected_language, $selected_currency );
			}

			if ( in_array( '_max_variation_regular_price', $active_field_names ) ) {
				$product->_max_variation_regular_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_regular_price( 'max' ), $selected_language, $selected_currency );
			}

			if ( in_array( '_min_variation_sale_price', $active_field_names ) ) {
				$product->_min_variation_sale_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_sale_price(), $selected_language, $selected_currency );
			}

			if ( in_array( '_max_variation_sale_price', $active_field_names ) ) {
				$product->_max_variation_sale_price = wppfm_prep_money_values( $woocommerce_product_parent->get_variation_sale_price( 'max' ), $selected_language, $selected_currency );
			}

			if ( in_array( 'item_group_id', $active_field_names ) ) {
				$parent_sku = $woocommerce_product_parent->get_sku();

				if ( $parent_sku ) {
					$product->item_group_id = $parent_sku; // best practise
				} elseif ( $woocommerce_parent_id ) {
					$product->item_group_id = 'GID' . $woocommerce_parent_id;
				} else {
					$product->item_group_id = '';
				}
			}
		} else {
			if ( ! $woocommerce_product_parent->is_type( 'simple' ) && ! $woocommerce_product_parent->is_type( 'grouped' )
				&& ! $woocommerce_product_parent->is_type( 'virtual' ) && ! $woocommerce_product_parent->is_type( 'downloadable' )
				&& ! $woocommerce_product_parent->is_type( 'external' ) ) {
				$msg = sprintf(
					'Product type of product %s could not be identified. The products shows as type %s',
					$product->ID,
					function_exists( 'get_product_type' ) ? get_product_type( $product->ID ) : 'unknown'
				);
				do_action( 'wppfm_feed_generation_warning', $feed_id, $msg ); // @since 2.3.0
			}
		}

		if ( in_array('_stock', $active_field_names ) ) {
			$product->_stock = $woocommerce_product->get_stock_quantity();
		}

		// @since 2.1.4
		if ( in_array( 'empty', $active_field_names ) ) {
			$product->empty = '';
		}

		// @since 2.2.0
		if ( in_array( 'product_type', $active_field_names ) ) {
			$product->type = $woocommerce_product->get_type();
		}

		// @since 2.2.0
		if ( in_array( 'product_variation_title_without_attributes', $active_field_names ) ) {
			$product_title = get_post_field( 'post_title', $product->ID );

			if ( false !== strpos( $product_title, ' - ' ) ) { // assuming that the woocommerce_product_variation_title_attributes_separator is ' - '
				$title_parts   = explode( ' - ', $product_title );
				$product_title = $title_parts[0];
			}

			$product->product_variation_title_without_attributes = $product_title;
		}

		// @since 2.21.0
		if ( in_array( '_variation_parent_id', $active_field_names ) ) {
			$product->_variation_parent_id = $woocommerce_parent_id;
		}

		// @since 2.21.0
		if ( in_array( '_product_parent_id', $active_field_names ) ) {
			$product->_product_parent_id = $woocommerce_parent_id ?: '0';
		}

		// @since 2.21.0
		if ( in_array( '_max_group_price', $active_field_names ) && $woocommerce_product_parent->is_type( 'grouped' ) ) {
			$product->_max_group_price = $this->get_group_price( $woocommerce_product_parent, 'max' );
		}

		// @since 2.21.0
		if ( in_array( '_min_group_price', $active_field_names ) && $woocommerce_product_parent->is_type( 'grouped' ) ) {
			$product->_min_group_price = $this->get_group_price( $woocommerce_product_parent );
		}

		// @since 2.26.0
		if ( in_array( '_regular_price_with_tax', $active_field_names )  ) {
			$price = wc_get_price_including_tax( $woocommerce_product, array( 'price' => $woocommerce_product->get_regular_price( 'feed' ) ) );
			$product->_regular_price_with_tax = wppfm_prep_money_values( $price, $selected_language, $selected_currency );
		}

		// @since 2.26.0
		if ( in_array( '_regular_price_without_tax', $active_field_names )  ) {
			$price = wc_get_price_excluding_tax( $woocommerce_product, array( 'price' => $woocommerce_product->get_regular_price( 'feed' ) ) );
			$product->_regular_price_without_tax = wppfm_prep_money_values( $price, $selected_language, $selected_currency );
		}

		// @since 2.26.0
		if ( in_array( '_sale_price_with_tax', $active_field_names )  ) {
			$price = wc_get_price_including_tax( $woocommerce_product, array( 'price' => $woocommerce_product->get_sale_price( 'feed' ) ) );
			$product->_sale_price_with_tax = wppfm_prep_money_values( $price, $selected_language, $selected_currency );
		}

		// @since 2.26.0
		if ( in_array( '_sale_price_without_tax', $active_field_names )  ) {
			$price = wc_get_price_excluding_tax( $woocommerce_product, array( 'price' => $woocommerce_product->get_sale_price( 'feed' ) ) );
			$product->_sale_price_without_tax = wppfm_prep_money_values( $price, $selected_language, $selected_currency );
		}

		// @since 2.28.0
		if ( in_array( '_product_parent_description', $active_field_names )  ) {
			$product->_product_parent_description = $woocommerce_product_parent->get_description( 'feed' );
		}

		// @since 2.28.0
		if ( in_array( '_woocs_currency', $active_field_names )  ) {
			// WOOCS support
			$product->_woocs_currency = has_filter( 'wppfm_get_woocs_currency' )
				? apply_filters( 'wppfm_get_woocs_currency', $selected_currency ) : get_woocommerce_currency();
		}

		$woocommerce_product = null;

		return true;
	}

	/**
	 * get additional images
	 *
	 * @param string $post_id
	 * @param string $selected_language
	 *
	 * @return array|string
	 */
	protected function get_product_image_gallery( $post_id, $selected_language ) {
		$image_urls    = array();
		$images        = 1;
		$max_nr_images = 10;

		$prdct          = wc_get_product( $post_id );
		$attachment_ids = $prdct->get_gallery_image_ids();

		foreach ( $attachment_ids as $attachment_id ) {
			$link = wp_get_attachment_image_url( $attachment_id, '' );

			// WPML support
			$image_link = has_filter( 'wppfm_get_wpml_permalink' )
				? apply_filters( 'wppfm_get_wpml_permalink', $link, $selected_language ) : $link;

			// correct baseurl for https if required
			if ( is_ssl() ) {
				$url = str_replace( 'http://', 'https://', $image_link );
			} else {
				$url = $image_link;
			}

			array_push( $image_urls, $url );
			$images ++;

			if ( $images > $max_nr_images ) {
				break;
			}
		}

		return ! empty( $image_urls ) ? $image_urls : '';
	}

	protected function get_product_tags( $id ) {
		$product_tags_string = '';
		$product_tag_values  = get_the_terms( $id, 'product_tag' );
		$post_tag_values     = get_the_tags( $id );

		if ( $product_tag_values ) {
			foreach ( $product_tag_values as $product_tag ) {
				$product_tags_string .= $product_tag->name . ', ';
			}
		}

		if ( $post_tag_values ) {
			foreach ( $post_tag_values as $post_tag ) {
				$product_tags_string .= $post_tag->name . ', ';
			}
		}

		return $product_tags_string ? substr( $product_tags_string, 0, - 2 ) : '';
	}

	/**
	 * Returns the lowest or highest price of the products in a grouped product.
	 *
	 * @param WC_Product $woocommerce_product_parent
	 * @param string     $min_max
	 *
	 * @return string The highest or lowest price as a string.
	 */
	protected function get_group_price( $woocommerce_product_parent, $min_max = 'min' ) {
		$children       = $woocommerce_product_parent->get_children();
		$product_prices = [];

		foreach( $children as $key => $value ) {
			$product = wc_get_product( $value );
			array_push( $product_prices, $product->get_price() );
		}

		return 'min' === $min_max ? min( $product_prices ) : max( $product_prices );
	}
}
