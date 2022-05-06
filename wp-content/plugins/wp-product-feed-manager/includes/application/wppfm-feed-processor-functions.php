<?php

/**
 * @package WP Product Review Feed Manager/Functions
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait WPPFM_Feed_Processor_Functions {

	/**
	 * Adds a string to the feed
	 *
	 * @param array $line_data
	 *
	 * @return boolean
	 */
	private function add_file_format_line_to_feed( $line_data ) {
		return false !== file_put_contents( $this->_feed_file_path, $line_data['file_format_line'], FILE_APPEND ) ? true : false;
	}

	/**
	 * Adds an error message to the feed
	 *
	 * @param array $error_message_data
	 *
	 * @return boolean
	 */
	private function add_error_message_to_feed( $error_message_data ) {
		return false !== file_put_contents( $this->_feed_file_path, $error_message_data['feed_line_message'], FILE_APPEND ) ? true : false;
	}


	/**
	 * register the update in the database
	 *
	 * @param string $feed_id
	 * @param string $feed_name
	 * @param string $nr_products
	 * @param string $status
	 */
	private function register_feed_update( $feed_id, $feed_name, $nr_products, $status = null ) {
		$data_class = new WPPFM_Data();

		// register the update and update the feed Last Change time
		$data_class->update_feed_data( $feed_id, wppfm_get_file_url( $feed_name ), $nr_products );

		$actual_status = $status ? $status : $data_class->get_feed_status( $feed_id );

		if ( '4' !== $actual_status && '5' !== $actual_status && '6' !== $actual_status ) { // no errors
			$data_class->update_feed_status( $feed_id, intval( $status ) ); // put feed on status hold if no errors are reported
		}
	}

	private function get_products_main_data( $product_id, $parent_product_id, $post_columns_query_string ) {
		$queries_class   = new WPPFM_Queries();
		$prep_meta_class = new WPPFM_Feed_Value_Editors();

		$product_data = $queries_class->read_post_data( $product_id, $post_columns_query_string );

		// WPML support.
		if ( has_filter( 'wpml_translation' ) ) {
			$product_data = apply_filters( 'wpml_translation', $product_data, $this->_feed_data->language );
		}

		// Polylang support.
		if ( has_filter( 'pll_translation' ) ) {
			$product_data = apply_filters( 'pll_translation', $product_data, $this->_feed_data->language );
		}

		// Parent ids are required to get the main data from product variations.
		$meta_parent_ids = 0 !== $parent_product_id ? array( $parent_product_id ) : $this->get_meta_parent_ids( $product_id );

		array_unshift( $meta_parent_ids, $product_id ); // Add the product id to the parent ids.

		$meta_data = $queries_class->read_meta_data( $product_id, $parent_product_id, $meta_parent_ids, $this->_pre_data['database_fields']['meta_fields'] );

		foreach ( $meta_data as $meta ) {
			$meta_value = $prep_meta_class->prep_meta_values( $meta, $this->_feed_data->language, $this->_feed_data->currency );

			if ( property_exists( $product_data, $meta->meta_key ) ) {
				$meta_key = $meta->meta_key;

				if ( '' === $product_data->$meta_key ) {
					$product_data = (object) array_merge( (array) $product_data, array( $meta->meta_key => $meta_value ) );
				}
			} else {
				$product_data = (object) array_merge( (array) $product_data, array( $meta->meta_key => $meta_value ) );
			}
		}

		foreach ( $this->_pre_data['database_fields']['active_custom_fields'] as $field ) {
			$product_data->{$field} = $this->get_custom_field_data( $product_data->ID, $parent_product_id, $field );
		}

		foreach ( $this->_pre_data['database_fields']['third_party_custom_fields'] as $third_party_field ) {
			$product_data->{$third_party_field} = $this->get_third_party_custom_field_data( $product_data->ID, $parent_product_id, $third_party_field );
		}

		if ( $this->_feed_data ) { // @since 2.29.0 to not start this function when using the WooCommerce Google Review Feed Manager plugin.
			$this->add_procedural_data( $product_data, $this->_pre_data['column_names'], $this->_feed_data->language, $this->_feed_data->currency, $this->_feed_data->feedId );
		}

		return $product_data;
	}
}
