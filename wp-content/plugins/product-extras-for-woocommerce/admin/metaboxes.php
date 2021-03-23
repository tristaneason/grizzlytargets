<?php

/**
 * Returns array of metaboxes
 * @return Array
 * @since 1.0.0
 */
function pewc_metaboxes() {

	$user_fields = array(
		array(
			'ID'		=> 'pewc_user_id',
			'name'		=> 'pewc_user_id',
			'title'		=> __( 'User ID', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_user_name',
			'name'		=> 'pewc_user_name',
			'title'		=> __( 'User Name', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_user_email',
			'name'		=> 'pewc_user_email',
			'title'		=> __( 'User Email', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_user_phone',
			'name'		=> 'pewc_user_phone',
			'title'		=> __( 'User Phone Number', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
	);

	$fields = array(
		array(
			'ID'		=> 'pewc_product_id',
			'name'		=> 'pewc_product_id',
			'title'		=> __( 'Product ID', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_order_id',
			'name'		=> 'pewc_order_id',
			'title'		=> __( 'Order ID', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_item_cost',
			'name'		=> 'pewc_item_cost',
			'title'		=> __( 'Item Cost', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_order_total',
			'name'		=> 'pewc_order_total',
			'title'		=> __( 'Order Total', 'pewc' ),
			'type'		=> 'text',
			'readonly'	=> true,
			'class'		=> 'pewc-metafield-third'
		),
		array(
			'ID'		=> 'pewc_notes',
			'name'		=> 'pewc_notes',
			'title'		=> __( 'Order Notes', 'pewc' ),
			'type'		=> 'textarea',
			'class'		=> ''
		),
	);

	$fields = apply_filters( 'pewc_filter_metabox_fields', $fields );

	// Add metaboxes for product_extras
	$groups_fields = array(
		array(
			'ID'		=> 'pewc_product_extra_fields',
			'name'		=> 'pewc_product_extra_fields',
			'title'		=> __( 'Product Add On Fields', 'pewc' ),
			'type'		=> 'product_extra_fields',
			'class'		=> ''
		),
	);

	$pewc_groups = array(
		array(
			'ID'			=> 'group_title',
			'name'		=> 'group_title',
			'title'		=> __( 'Group Title (Front End)', 'pewc' ),
			'type'		=> 'text',
			'class'		=> ''
		),
		array(
			'ID'			=> 'group_description',
			'name'		=> 'group_description',
			'title'		=> __( 'Group Description', 'pewc' ),
			'type'		=> 'textarea',
			'class'		=> ''
		),
		array(
			'ID'			=> 'group_layout',
			'name'		=> 'group_layout',
			'title'		=> __( 'Group Layout', 'pewc' ),
			'type'		=> 'select',
			'options'	=> array(
				'ul'			=> __( 'Standard', 'pewc' ),
				'table'		=> __( 'Table', 'pewc' ),
				'cols-2'	=> __( 'Two Columns', 'pewc' ),
				'cols-3'	=> __( 'Three Columns', 'pewc' )
			),
			'class'		=> ''
		),
		array(
			'ID'			=> 'global_rules',
			'name'		=> 'global_rules',
			'title'		=> __( 'Group Rules', 'pewc' ),
			'type'		=> 'global_rules',
			'options'	=> array(
				'all'		=> __( 'All the selected rules are met', 'pewc' ),
				'any'		=> __( 'Any of the selected rules are met', 'pewc' )
			),
			'class'		=> ''
		),
	);

	$pewc_groups_fields = array(
		array(
			'ID'			=> 'group_fields_title',
			'name'		=> 'group_fields_title',
			'title'		=> __( 'Fields', 'pewc' ),
			'type'		=> 'group_fields',
			'class'		=> ''
		),
	);

	// Add metaboxes for field post type
	$pewc_group_sidebar_fields = array(
		array(
			'ID'			=> 'pewc_parent_product',
			'name'		=> 'pewc_parent_product',
			'title'		=> __( 'Assigned to', 'pewc' ),
			'type'		=> 'metabox_get_parent_product',
			// 'readonly'	=> true,
			'class'		=> ''
		),
		array(
			'ID'			=> 'pewc_assign_group',
			'name'		=> 'pewc_assign_group',
			'title'		=> __( 'Duplicate to', 'pewc' ),
			'type'		=> 'metabox_assign_group_to_product',
			// 'readonly'	=> true,
			'class'		=> '',
			'description'	=> __( 'This group and its fields will be duplicated to the products you enter here', 'pewc' )
		),
	);

	$additional_fields = array(
		array(
			'ID'		=> 'pewc_extra_notes',
			'name'		=> 'pewc_extra_notes',
			'title'		=> __( 'Notes', 'pewc' ),
			'type'		=> 'textarea',
			'class'		=> ''
		),
		array(
			'ID'		=> 'pewc_extra_status',
			'name'		=> 'pewc_extra_status',
			'title'		=> __( 'Status', 'pewc' ),
			'type'		=> 'select',
			'options'	=> array(
				''				=> '--',
				'accept'	=> __( 'Accept', 'pewc' ),
				'reject'	=> __( 'Reject', 'pewc' )
			),
			'class'		=> ''
		),
	);

	// Add metaboxes for orders
	$order_fields = array(
		array(
			'ID'		=> 'pewc_product_extra_id',
			'name'		=> 'pewc_product_extra_id',
			'title'		=> __( 'Product Add On ID', 'pewc' ),
			'type'		=> 'text',
			'class'		=> ''
		),
	);

	$metaboxes = array(
		'user_fields' => array(
			'ID'				=> 'pewc_user_metabox',
			'title'			=> __( 'User Details', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_product_extra' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $user_fields
		),
		'order_details'	=> array(
			'ID'			=> 'pewc_order_metabox',
			'title'			=> __( 'Order Details', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_product_extra' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $fields
		),
		'pewc_product_extra' 	=> array(
			'ID'			=> 'pewc_metabox',
			'title'			=> __( 'Product Add On Groups', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_product_extra' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $groups_fields
		),
		'additional'	=> array(
			'ID'			=> 'pewc_additional_metabox',
			'title'			=> __( 'Additional Information', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_product_extra' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $additional_fields
		),
		'shop_order' => array(
			'ID'			=> 'pewc_order_metabox',
			'title'			=> __( 'Product Add-Ons', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'shop_order' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $order_fields
		),
		'pewc_group'	=> array(
			'ID'			=> 'pewc_group_metabox',
			'title'			=> __( 'Group Meta', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_group' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $pewc_groups
		),
		'pewc_group_fields'	=> array(
			'ID'			=> 'pewc_group_fields_metabox',
			'title'			=> __( 'Fields', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_group' ),
			'context'		=> 'normal',
			'priority'		=> 'default',
			'fields'		=> $pewc_groups_fields
		),

		'pewc_group_sidebar'	=> array(
			'ID'			=> 'pewc_group_sidebar_metabox',
			'title'			=> __( 'Parent Products', 'pewc' ),
			'callback'		=> 'meta_box_callback',
			'screens'		=> array( 'pewc_group' ),
			'context'		=> 'side',
			'priority'		=> 'default',
			'fields'		=> $pewc_group_sidebar_fields
		),
	);

	return $metaboxes;

}

/**
 * Returns array of metaboxes
 * @return Array
 * @since 3.2.3
 */
function pewc_get_group_metaboxes() {

	// $group_fields = array(
	// 	array(
	// 		'ID'				=> 'pewc_group_title',
	// 		'name'			=> 'pewc_group_title',
	// 		'title'			=> __( 'Group Title', 'pewc' ),
	// 		'type'			=> 'text',
	// 		'screens'		=> array( 'pewc_group' ),
	// 		'class'			=> 'pewc-metafield-third'
	// 	),
	// );

	return $group_fields;

}
