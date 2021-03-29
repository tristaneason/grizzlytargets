<?php
/**
 * Functions for the settings
 * @since 3.7.6
 * @package WooCommerce Product Add-Ons Ultimate
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the general settings
 */
function pewc_get_general_settings() {

	$settings = array(

		'general_section_title' => array(
			'name'     => __( 'General', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_general_title'
		),
		'pewc_hide_zero' => array(
			'name'			=> __( 'Hide zero prices', 'pewc' ),
			'type'			=> 'checkbox',
			'desc_tip'	=> true,
			'desc'			=> __( 'Hide prices in the cart for extras which don\'t have a cost.', 'pewc' ),
			'id'				=> 'pewc_hide_zero',
			'default'		=> 'no',
			'std'				=> 'no'
		),
		'pewc_ignore_tax' => array(
			'name'			=> __( 'Ignore tax setting', 'pewc' ),
			'type'			=> 'checkbox',
			'desc_tip'	=> true,
			'desc'			=> __( 'Ignore the WooCommerce "Display prices in the shop" setting which determines whether prices are displaying including or excluding tax.', 'pewc' ),
			'id'				=> 'pewc_ignore_tax',
			'default'		=> 'no',
			'std'				=> 'no'
		),
		'pewc_tax_suffix' => array(
			'name'		=> __( 'Display tax suffix', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Display the tax suffix after all add-on price fields.', 'pewc' ),
			'id'			=> 'pewc_tax_suffix',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_price_separator' => array(
			'name'		=> __( 'Price separator', 'pewc' ),
			'type'		=> 'text',
			'desc_tip'	=> true,
			'desc'		=> __( 'Define a symbol to separate the add-on label from the add-on price.', 'pewc' ),
			'id'			=> 'pewc_price_separator',
			'default'	=> '+',
			'std'			=> '+'
		),
		'pewc_enable_cart_editing' => array(
			'name'		=> __( 'Enable editing', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Allow users to edit the add-ons in products that have already been added to the cart.', 'pewc' ),
			'id'			=> 'pewc_enable_cart_editing',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_enable_tooltips' => array(
			'name'		=> __( 'Enable tooltips', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Display add-on field descriptions in interactive elements.', 'pewc' ),
			'id'			=> 'pewc_enable_tooltips',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_update_price_label' => array(
			'name'		=> __( 'Update price label', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Update the price label on product pages when price changes.', 'pewc' ),
			'id'			=> 'pewc_update_price_label',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_dequeue_scripts' => array(
			'name'		=> __( 'Dequeue scripts', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Dequeue scripts and stylesheets on non-product pages.', 'pewc' ),
			'id'			=> 'pewc_dequeue_scripts',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'general_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_general_title'
		),

		'pewc_global_title' => array(
			'name'     => __( 'Global', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_global_title'
		),
		'pewc_enable_groups_as_post_types' => array(
			'name'		=> __( 'Display groups as post type', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Display groups as custom post types, not on single page.', 'pewc' ),
			'id'			=> 'pewc_enable_groups_as_post_types',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'global_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_global_title'
		),

		'pewc_conditions_title' => array(
			'name'     => __( 'Conditions', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_conditions_title'
		),
		'pewc_reset_fields' => array(
			'name'		=> __( 'Reset field values', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Reset field values to null when fields are hidden through a condition.', 'pewc' ),
			'id'			=> 'pewc_reset_fields',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'gconditions_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_conditions_title'
		),

		'labels_section_title' => array(
			'name'     => __( 'Labels', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_labels_title'
		),
		// Price labelling
		'pewc_price_label' => array(
			'name'			=> __( 'Price label', 'pewc' ),
			'type'			=> 'text',
			'desc_tip'	=> true,
			'desc'			=> __( 'Additional or replacement text for the price', 'pewc' ),
			'id'				=> 'pewc_price_label'
		),
		'pewc_price_display' => array(
			'name'			=> __( 'Price label display', 'pewc' ),
			'type'			=> 'select',
			'desc_tip'	=> true,
			'desc'			=> __( 'Decide where to display the label', 'pewc' ),
			'id'				=> 'pewc_price_display',
			'default'		=> 'before',
			'std'				=> 'before',
			'options'     => array(
				'before'			=> __( 'Before price', 'pewc' ),
				'after'				=> __( 'After price', 'pewc' ),
				'hide'				=> __( 'Hide price', 'pewc' )
			)
		),
		// Subtotals
		'pewc_show_totals' => array(
			'name'			=> __( 'Display totals fields', 'pewc' ),
			'type'			=> 'select',
			'desc_tip'	=> true,
			'desc'			=> __( 'Decide how to display totals fields on product pages', 'pewc' ),
			'id'				=> 'pewc_show_totals',
			'default'		=> 'all',
			'std'				=> 'all',
			'options'     => array(
				'all'           => __( 'Show totals', 'woocommerce' ),
				'none'          => __( 'Hide totals', 'woocommerce' ),
				'total'    			=> __( 'Total only', 'woocommerce' ),
			),
		),
		'pewc_product_total_label' => array(
			'name'			=> __( 'Product total label', 'pewc' ),
			'type'			=> 'text',
			'desc_tip'	=> true,
			'desc'			=> __( 'The label for the Product total', 'pewc' ),
			'id'				=> 'pewc_product_total_label',
			'default'		=> __( 'Product total', 'pewc' ),
		),
		'pewc_options_total_label' => array(
			'name'			=> __( 'Options total label', 'pewc' ),
			'type'			=> 'text',
			'desc_tip'	=> true,
			'desc'			=> __( 'The label for the Options total', 'pewc' ),
			'id'				=> 'pewc_options_total_label',
			'default'		=> __( 'Options total', 'pewc' ),
		),
		'pewc_flatrate_total_label' => array(
			'name'			=> __( 'Flat rate total label', 'pewc' ),
			'type'			=> 'text',
			'desc_tip'	=> true,
			'desc'			=> __( 'The label for the Flat rate total', 'pewc' ),
			'id'				=> 'pewc_flatrate_total_label',
			'default'		=> __( 'Flat rate total', 'pewc' ),
		),
		'pewc_grand_total_label' => array(
			'name'			=> __( 'Grand total label', 'pewc' ),
			'type'			=> 'text',
			'desc_tip'	=> true,
			'desc'			=> __( 'The label for the Grand total', 'pewc' ),
			'id'				=> 'pewc_grand_total_label',
			'default'		=> __( 'Grand total', 'pewc' ),
		),
		'labels_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_labels_title'
		),

		'roles_section_title' => array(
			'name'     => __( 'Role-based Pricing', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_roles_title'
		),
		'pewc_role_prices' => array(
			'name'			=> __( 'Roles', 'pewc' ),
			'type'			=> 'multiselect',
			'desc_tip'	=> true,
			'desc'			=> __( 'Enter the roles that you would like to have different add-on prices for.', 'pewc' ),
			'id'				=> 'pewc_role_prices',
			'options'		=> pewc_get_all_roles(),
			'class'			=> 'pewc-multiselect'
		),
		'roles_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_roles_title'
		),

		'beta_section_title' => array(
			'name'     => __( 'Beta', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_beta_title'
		),
		// 'pewc_unserialise_order_meta' => array(
		// 	'name'		=> __( 'Unserialise order meta', 'pewc' ),
		// 	'type'		=> 'checkbox',
		// 	'desc_tip'	=> true,
		// 	'desc'		=> __( 'Check this to save each add-on field as a separate meta data line in the order', 'pewc' ),
		// 	'id'			=> 'pewc_unserialise_order_meta',
		// 	'default'	=> 'no',
		// ),
		'pewc_optimise_calculations' => array(
			'name'			=> __( 'Optimise calculations', 'pewc' ),
			'type'			=> 'checkbox',
			'desc_tip'	=> true,
			'desc'			=> __( 'This will enable an alternative method for checking calculations which might improve page performance. Still beta.', 'pewc' ),
			'id'				=> 'pewc_optimise_calculations',
			'default'		=> 'no',
			'std'				=> 'no'
		),
		'pewc_optimise_conditions' => array(
			'name'			=> __( 'Optimise conditions', 'pewc' ),
			'type'			=> 'checkbox',
			'desc_tip'	=> true,
			'desc'			=> __( 'This will enable an alternative method for checking conditions which might improve page performance. Still beta.', 'pewc' ),
			'id'				=> 'pewc_optimise_conditions',
			'default'		=> 'no',
			'std'				=> 'no'
		),
		'pewc_beta_testing' => array(
			'name'			=> __( 'Beta testing', 'pewc' ),
			'type'			=> 'checkbox',
			'desc_tip'	=> true,
			'desc'			=> __( 'Opt in to beta testing the plugin. You should only choose this option on a staging or development site - don\'t enable this on your live site.', 'pewc' ),
			'id'				=> 'pewc_beta_testing',
			'default'		=> 'no',
			'std'				=> 'no'
		),
		'beta_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_beta_title'
		),

	);

	return apply_filters( 'pewc_filter_settings', $settings );

}

/**
 * Get the licence settings
 */
function pewc_get_uploads_settings() {

	$settings = array(

		'general_uploads_title' => array(
			'name'     => __( 'Uploads', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'general_uploads_title'
		),
		'pewc_require_log_in' => array(
			'name'		=> __( 'Users must be logged in to upload', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'For security reasons, it is strongly recommended that you require users to be logged in before allowing them to upload files.', 'pewc' ),
			'id'		=> 'pewc_require_log_in',
			'default'	=> 'no',
			'std'		=> 'no'
		),
		'pewc_max_upload' => array(
			'name'		=> __( 'Max file size (MB)', 'pewc' ),
			'type'		=> 'number',
			'desc_tip'	=> true,
			'desc'		=> __( 'The max file size for uploads (in MB)', 'pewc' ),
			'id'		=> 'pewc_max_upload',
			'default'	=> '1',
		),
		'pewc_enable_pdf_uploads' => array(
			'name'		=> __( 'Enable PDF uploads', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Allow users to upload PDFs.', 'pewc' ),
			'id'		=> 'pewc_enable_pdf_uploads',
			'default'	=> 'no'
		),
		'pewc_enable_dropzonejs' => array(
			'name'		=> __( 'Enable AJAX uploader', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Add uploaded images via AJAX.', 'pewc' ),
			'id'		=> 'pewc_enable_dropzonejs',
			'default'	=> 'no',
			'std'		=> 'no'
		),
		'pewc_retain_dropzone' => array(
			'name'		=> __( 'Retain Upload Graphic', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Retain the upload graphic even after a file has been uploaded.', 'pewc' ),
			'id'		=> 'pewc_retain_dropzone',
			'default'	=> 'no',
			'std'		=> 'no'
		),
		'pewc_disable_add_to_cart' => array(
			'name'		=> __( 'Disable Add to Cart button', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Disable the add to cart button while files are being uploaded.', 'pewc' ),
			'id'		=> 'pewc_disable_add_to_cart',
			'default'	=> 'no',
			'std'		=> 'no'
		),
		'pewc_email_images' => array(
			'name'		=> __( 'Attach uploads to emails', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Add uploaded images to new order emails.', 'pewc' ),
			'id'		=> 'pewc_email_images',
			'default'	=> 'no',
			'std'		=> 'no'
		),
		'pewc_rename_uploads' => array(
			'name'		=> __( 'Rename uploads', 'pewc' ),
			'type'		=> 'text',
			'desc_tip'	=> true,
			'desc'		=> __( 'Rename uploads using the following tags: {original_file_name}, {order_number}, {date}, {product_id}, {product_sku}, {group_id}, {field_id}', 'pewc' ),
			'id'		=> 'pewc_rename_uploads',
			'default'	=> '',
		),
		'pewc_organise_uploads' => array(
			'name'		=> __( 'Organise uploads by order', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Organise uploads into unique folders for each order', 'pewc' ),
			'id'			=> 'pewc_organise_uploads',
			'default'	=> 'no',
		),
		'uploads_section_end' => array(
			'type' => 'sectionend',
			'id' => 'general_uploads_title'
		),

	);

	return apply_filters( 'pewc_uploads_settings', $settings );

}

/**
 * Get the product field settings
 */
function pewc_get_products_settings() {

	$settings = array(

		'products_section_title' => array(
			'name'     => __( 'Products', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_products_title'
		),
		'pewc_child_variations' => array(
			'name'		=> __( 'Include variations as child products', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to include variations as child products.', 'pewc' ),
			'id'			=> 'pewc_child_variations',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_exclude_skus' => array(
			'name'		=> __( 'Exclude SKUs from child variants', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to exclude SKUs from child variant names.', 'pewc' ),
			'id'			=> 'pewc_exclude_skus',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_hide_child_products_cart' => array(
			'name'		=> __( 'Hide child products in the cart', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to hide child products in the cart.', 'pewc' ),
			'id'			=> 'pewc_hide_child_products_cart',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_display_child_products_as_meta' => array(
			'name'		=> __( 'Display child products as metadata', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to display child products as metadata for parent products in the cart.', 'pewc' ),
			'id'			=> 'pewc_display_child_products_as_meta',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_hide_parent_products_cart' => array(
			'name'		=> __( 'Hide parent products in the cart', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to hide parent products in the cart.', 'pewc' ),
			'id'			=> 'pewc_hide_parent_products_cart',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_hide_child_products_order' => array(
			'name'		=> __( 'Hide child products in the order', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to hide child products in the order.', 'pewc' ),
			'id'			=> 'pewc_hide_child_products_order',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_hide_parent_products_order' => array(
			'name'		=> __( 'Hide parent products in the order', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to hide parent products in the order.', 'pewc' ),
			'id'			=> 'pewc_hide_parent_products_order',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_multiply_independent_quantity' => array(
			'name'		=> __( 'Multiply independent quantities', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to multiply child product independent quantities when parent product quantities are adjusted.', 'pewc' ),
			'id'			=> 'pewc_multiply_independent_quantity',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_redirect_hidden_products' => array(
			'name'		=> __( 'Redirect hidden products', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Enable this to prevent users purchasing child products direct from their product page.', 'pewc' ),
			'id'			=> 'pewc_redirect_hidden_products',
			'default'	=> 'no',
			'std'			=> 'no'
		),
		'pewc_child_product_quickview' => array(
			'name'		=> __( 'Enable QuickView for child products', 'pewc' ),
			'type'		=> 'checkbox',
			'desc_tip'	=> true,
			'desc'		=> __( 'Select this to show extra information for child products when clicking the child product title.', 'pewc' ),
			'id'			=> 'pewc_child_product_quickview',
			'default'	=> 'no',
			'std'			=> 'no'
		),

		'products_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_products_title'
		),

	);

	return apply_filters( 'pewc_products_settings', $settings );

}

/**
 * Get the calculation field settings
 */
function pewc_get_calculations_settings() {

	$settings = array(

		'calculations_section_title' => array(
			'name'     => __( 'Calculations', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_calculations_title'
		),
		'pewc_variable_1' => array(
			'name'			=> __( 'Variable 1', 'pewc' ),
			'type'			=> 'number',
			'desc_tip'	=> true,
			'desc'			=> __( 'Enter a value for variable_1 that will be used in calculations', 'pewc' ),
			'id'				=> 'pewc_variable_1',
			'custom_attributes'	=> array(
				'step'		=> apply_filters( 'pewc_global_variable_step', '0.01' )
			),
			'default'		=> '',
		),
		'pewc_variable_2' => array(
			'name'			=> __( 'Variable 2', 'pewc' ),
			'type'			=> 'number',
			'desc_tip'	=> true,
			'desc'			=> __( 'Enter a value for variable_2 that will be used in calculations', 'pewc' ),
			'id'				=> 'pewc_variable_2',
			'custom_attributes'	=> array(
				'step'		=> apply_filters( 'pewc_global_variable_step', '0.01' )
			),
			'default'		=> '',
		),
		'pewc_variable_3' => array(
			'name'			=> __( 'Variable 3', 'pewc' ),
			'type'			=> 'number',
			'desc_tip'	=> true,
			'desc'			=> __( 'Enter a value for variable_3 that will be used in calculations', 'pewc' ),
			'id'				=> 'pewc_variable_3',
			'custom_attributes'	=> array(
				'step'		=> apply_filters( 'pewc_global_variable_step', '0.01' )
			),
			'default'		=> '',
		),
		'calculations_section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_calculations_title'
		),

	);

	return apply_filters( 'pewc_calculations_settings', $settings );

}

/**
 * Get the licence settings
 */
function pewc_get_licence_settings() {

	$settings = array(
		'section_title' => array(
			'name'     => __( 'WooCommerce Product Add-Ons Ultimate', 'pewc' ),
			'type'     => 'title',
			'desc'     => '',
			'id'       => 'pewc_settings_title'
		),
		'pewc_license_key' => array(
			'name'			=> __( 'License key', 'pewc' ),
			'type'			=> 'pewc_license_key',
			'desc_tip'	=> true,
			'desc'			=> __( 'Enter your license key here. You should have received a key with the email containing the plugin download link.', 'pewc' ),
			'id'				=> 'pewc_license_key',
			'default'		=> '',
		),
		'section_end' => array(
			'type' => 'sectionend',
			'id' => 'pewc_settings_title'
		),

	);

	return apply_filters( 'pewc_licence_settings', $settings );

}
