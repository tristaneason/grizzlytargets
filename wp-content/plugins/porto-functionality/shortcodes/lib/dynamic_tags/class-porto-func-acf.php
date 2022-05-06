<?php
/**
 * Porto ACF plugin compatibility for dynamic tags.
 *
 * @author     P-THEMES
 * @since      6.3.0
 */

defined( 'ABSPATH' ) || die;

class Porto_Func_ACF {

	/**
	 * Constructor
	 *
	 * @since 6.3.0
	 */
	public function __construct() {
		add_filter( 'porto_gutenberg_editor_vars', array( $this, 'add_dynamic_field_vars' ) );
	}

	/**
	 * Returns support acf types
	 *
	 * @return array
	 */
	public function get_acf_types() {

		return array(
			'text'             => array( 'field', 'link' ),
			'textarea'         => array( 'field' ),
			'number'           => array( 'field' ),
			'range'            => array( 'field' ),
			'email'            => array( 'field', 'link' ),
			'url'              => array( 'field', 'link' ),
			'image'            => array( 'link', 'image' ),
			'select'           => array( 'field' ),
			'checkbox'         => array( 'field' ),
			'radio'            => array( 'field' ),
			'true_false'       => array( 'field' ),
			'link'             => array( 'field', 'link' ),
			'page_link'        => array( 'field', 'link' ),
			'post_object'      => array( 'field', 'link' ),
			'taxonomy'         => array( 'field', 'link' ),
			'date_picker'      => array( 'field' ),
			'date_time_picker' => array( 'field' ),
			'wysiwyg'          => array( 'field' ),
		);

	}

	public function acf_get_meta( $key ) {
		if ( ! $key ) {
			return null;
		}

		$post_id    = get_the_ID();
		$meta_value = get_post_meta( $post_id, $key, true );
		if ( ! $meta_value ) {
			return null;
		}

		return $meta_value;
	}

	/**
	 * Retrieve ACF Field groups
	 *
	 * @return array
	 * @since 1.0
	 */
	public function get_acf_groups( $widget ) {

		global $post;
		$type = $post->post_type;

		if ( function_exists( 'acf_get_field_groups' ) ) {
			$acf_groups = acf_get_field_groups(
				array(
					'post_id'   => $post->ID,
					'post_type' => $type,
				)
			);
		} else {
			$acf_groups = apply_filters( 'acf/get_field_groups', array() );
		}

		$data      = array();
		$acf_types = $this->get_acf_types();

		foreach ( $acf_groups as $acf_group ) {

			if ( function_exists( 'acf_get_fields' ) ) {
				$fields = acf_get_fields( $acf_group['ID'] );
			} else {
				$fields = array();
			}

			if ( empty( $fields ) ) {
				continue;
			}

			$options = array();

			foreach ( $fields as $field ) {
				if ( ! isset( $acf_types[ $field['type'] ] ) || ! in_array( $widget, $acf_types[ $field['type'] ] ) ) {
					continue;
				}

				$key             = $field['ID'] . '-' . $field['name'];
				$options[ $key ] = array(
					'type'  => $field['type'],
					'label' => $field['label'],
				);
			}

			if ( empty( $options ) ) {
				continue;
			}

			$data[] = array(
				'label'   => $acf_group['title'],
				'options' => $options,
			);
		}

		return $data;

	}

	/**
	 * Retrieve ACF meta fields
	 *
	 * @return array
	 * @since 6.3.0
	 */
	public function add_dynamic_field_vars( $block_vars = array() ) {

		$fields = array( 'field', 'image', 'link' );
		foreach ( $fields as $field_type ) {
			$meta_fields = array();
			$group_data  = $this->get_acf_groups( $field_type );

			if ( empty( $group_data ) ) {
				continue;
			}

			foreach ( $group_data as $data ) {
				$field     = array();
				$data_temp = $data['options'];

				foreach ( $data_temp as $key => $value ) {
					$field[ $key ] = isset( $value['label'] ) ? $value['label'] : '';
				}

				$field = array_filter( $field );

				$meta_fields[] = array(
					'label'   => $data['label'],
					'options' => $field,
				);
			}

			if ( ! isset( $block_vars['acf'] ) ) {
				$block_vars['acf'] = array();
			}
			$block_vars['acf'][ $field_type ] = $meta_fields;
		}

		return $block_vars;
	}

}

new Porto_Func_ACF;
