=== WooCommerce Product Add-Ons Ultimate ===
Contributors: Gareth Harris
Tags: add-ons, ecommerce
Requires at least: 4.7
Tested up to: 5.7
Stable tag: 3.8.9
Allow your users to customise products through additional fields

== Description ==

WooCommerce Product Add Ons Ultimate allows your users to customise products through additional fields.

== Installation ==
1. Upload the `product-extras-for-woocommerce` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Start adding Product Add-Ons in your WooCommerce products

== Frequently Asked Questions ==


== Screenshots ==

1.

== To Do List ==
* grid calculations / calculation extension - multiple conditional calculations, get total price, multiple variables
* steps layout for groups - do one, next one appears, continue button, breadcrumb menu
* customizer - enhancements to layout and style options
* field repeater and quantity repeater
* image extension - resize images, set different mime types per field
* finalise groups post type
* pricing table
* change product images
* change variation image in single column layout when variation is selected
* table layout for child products
* allow add-ons at cart and checkout
* importing fields via CSV
* include product ID in child product ID attr
* placeholder text
* filter child products (e.g. by tag)
* conditions on groups
* user role specific groups?
* Save Extras button so you don't need to update the product to save
* new pot file
* tidy up validation
* exclude products from globals / list global groups that a product belongs to
* font field
* modal pop-up with child product after added to cart

== Changelog ==

= 3.8.9, 25 March 2021 =
* Added: pewc_after_upload_script_init and pewc_dz_tpl_td actions
* Added: quantity param in $file object for uploads
* Added: pewc_remove_spaces_in_text filter
* Added: look up tables empty cells return null
* Fixed: group conditions not hiding correctly in the cart
* Fixed: group conditions not duplicating correctly
* Fixed: multiply price for number fields using percentage pricing
* Fixed: Multiply Price setting not saving on new number field
* Fixed: calculations always rounding to 2 decimal places
* Fixed: group conditions based on select box fields
* Updated: removed pewc_license_admin_notices nag

= 3.8.8, 11 March 2021 =
* Added: pewc_image_uploaded action
* Added: pewc_apply_underscore_metakey filter
* Added: pewc_qty_changed trigger to pewc.js
* Added: pewc_bypass_extra_fields_transient filter
* Fixed: calculated price not displaying correctly in cart
* Fixed: per character cost for preview text not added to cart
* Fixed: conditions not firing off radio buttons
* Fixed: deselected image swatch not updated in summary panel
* Fixed: group layout missing in group post type
* Updated: delete pewc_has_extra_fields_ transient on product save

= 3.8.7, 3 March 2021 =
* Added: steps layout for groups
* Added: option to force minimum product price
* Updated: conditions and calculations handling
* Updated: removed database update notice
* Updated: removed number_format from products-column.php
* Updated: licence key field for Advanced Calculations

= 3.8.6, 16 February 2021 =
* Added: integration with Advanced Preview extension
* Added: pewc_product_img_wrap filter for image replacement compatibility
* Added: QuickView option for child products
* Added: minimum price setting
* Fixed: parse error in functions-conditionals.php
* Fixed: child products displaying tax twice on product page
* Fixed: price suffix doubling after price
* Fixed: variable child products in columns layout not honouring discount on product page
* Updated: more reliable triggers for checking conditions and calculations

= 3.8.5, 8 February 2021 =
* Fixed: parse error from pewc_reset_hidden_fields
* Updated: filter image replacement container
* Updated: use data-src for image replacement

= 3.8.4, 8 February 2021 =
* Added: pewc_ignore_tax filter
* Added: fourth parameter to pewc_child_product_name filter
* Added: pewc-calculation-trigger class to fields which trigger calculations
* Added: pewc_add_to_cart_button_class filter
* Added: use select field value in look up tables
* Added: pewc_after_calculation_fields action
* Added: extra hooks for Advanced Preview extension
* Added: quantity parameter for renaming uploaded files
* Added: pewc_min_max_val_step filter
* Added: reset all transients on saving global groups
* Fixed: product checkbox quantities not honoured when editing a product from the cart
* Fixed: group conditions not firing on checkbox
* Fixed: group conditions when evaluating radio button value
* Fixed: tax applied twice to checkbox group options
* Fixed: 'Update Product' displaying on cart button for products without add-ons
* Fixed: look up fields not triggering correctly
* Fixed: bulk variations grid now displays cells in correct sequence irrespective of variations order
* Fixed: price suffix doubling
* Fixed: filter categories in global groups displayed as custom post
* Fixed: >= and <= operators not duplicating correctly
* Updated: enable variable subscription products in grid layout for products field
* Updated: moved pewc-radio-image-desc inside label element in products-radio.php
* Updated: don't deactivate plugin when WooCommerce is deactivated
* Updated: reformatted price so that currency symbol only wraps currency symbol
* Updated: Dutch translation
* Updated: show error message for troubleshooting licence activation issues
* Updated: allow groups to be filterable when wp_doing_ajax

= 3.8.3, 13 January 2021 =
* Fixed: pewc_multiply_independent_quantities_by_parent_quantity fatal error

= 3.8.2, 13 January 2021 =
* Added: duplicate global groups when using post type method
* Added: option to reset field value when hidden by a condition
* Added: pewc_default_product_column_value_before_checked filter
* Added: columns layout for groups
* Fixed: deselecting image swatch field failed to fire condition check
* Fixed: use floatval for child products discount field
* Fixed: group conditions attributes not formed correctly
* Fixed: conditions based on calculation field values not firing correctly
* Fixed: price label duplicating with Fees and Discounts
* Updated: check for options in image swatch template
* Updated: removed conditions from global groups
* Updated: moved product and calculation settings to separate tabs
* Updated: improved rounding on calculations
* Updated: use transients on product archive to check for select options button

= 3.8.1, 11 December 2020  =
* Fixed: Select Box compatibility with jQuery 3x
* Updated: AJAX uploader compatibility

= 3.8.0, 10 December 2020 =
* Added: allow look up tables to use calculation fields as axis values
* Added: pewc_global_variable_step filter
* Added: pewc_redirect_hidden_products
* Added: group conditions
* Added: greater than equals and less than equals condition operators
* Added: pewc_enable_groups_as_post_types option
* Fixed: don't display 'Edit options' on products with no add-ons
* Fixed: don't display 'Edit options' on child products
* Fixed: 'Select Options' text not showing for products with only global add-ons
* Fixed: fields with double quotes not firing in conditions
* Fixed: Bookings with add-ons incorrectly setting price
* Fixed: correct upgrade link on restricted field types
* Updated: create new field and group IDs by default when duplicating products
* Updated: use .one not .on in functions-conditions.php script
* Updated: price label updates correctly

= 3.7.25, 9 December 2020 =
* Fixed: AJAX uploader breaking with jQuery 3x

= 3.7.24, 26 November 2020 =
* Added: global helper functions for products and categories
* Fixed: new global field values not saving correctly
* Fixed: variation prices hidden
* Fixed: reinstated edit product text in cart
* Fixed: conditions with quantity correctly duplicated when duplicating product
* Fixed: update bulk grid quantities on keyup
* Updated: filter post_class only on single product page

= 3.7.23, 24 November 2020 =
* Fixed: performance issue with transients resetting
* Fixed: invalid child product ID causes fatal error in products layout templates
* Updated: apply pewc_number_field_step filter to default value
* Updated: check WC()->cart is object in cart

= 3.7.22, 12 November 2020 =
* Fixed: archive pages not recognising when products have add-ons
* Fixed: parse error in functions-products.php

= 3.7.21, 11 November 2020 =
* Added: pewc_disable_child_quantities
* Added: params for pewc_field_visibility_updated
* Added: options to hide child and parent items in the cart and order
* Added: pewc_child_product_name filter
* Added: error check for empty formula fields
* Added: compatibility with WooCommerce Currency Switcher
* Added: pewc_child_product_independent_quantity filter
* Added: variations grid layout for child products
* Fixed: check for error in wpml_post_language_details
* Fixed: child select fields not adding correctly to cart
* Fixed: child swatches fields not added correctly to cart
* Fixed: {field_XXX_option_price} not replacing field ID when duplicating products
* Fixed: field IDs not duplicating correctly in calculation formula
* Fixed: conditions not duplicating correctly
* Updated: allow child products on backorder
* Updated: removed default value parameter from get_transient
* Updated: pewc_has_product_extra_groups returns yes/no
* Updated: skip pewc_update_total_js on pewc_trigger_calculations if there are no active calculation fields
* Updated: use plus sign as default separator

= 3.7.20, 20 October 2020 =
* Added: $additional_info param to pewc_filter_field_description
* Added: pewc_get_transient_expiration filter
* Added: reset all transients
* Added: retain upload graphic option
* Fixed: upload fields overriding calculations setting product price
* Fixed: option price tax getting applied twice in certain circumstances
* Fixed: calculations for hidden variations incorrectly firing
* Fixed: independent child products not adding to cart correctly

= 3.7.19, 5 October 2020 =
* Fixed: image swatch fields always allowing multiple selections
* Fixed: options always setting price as percentage
* Fixed: parse error in functions-variations.php

= 3.7.18, 2 October 2020 =
* Fixed: ensure role based prices display correctly
* Fixed: field percentage and flat rate classes getting set incorrectly

= 3.7.17, 30 September 2020 =
* Added: pewc_description_as_placeholder filter
* Added: notice regarding add-on duplication
* Added: pewc_flat_rate_cost_text filter
* Added: option to dequeue scripts on non-product pages
* Fixed: update field ID tags when duplicating calculation fields
* Fixed: duplicated fields saved as pewc_group post type
* Fixed: duplicated fields with 0 value
* Fixed: calculations not working for checkbox groups
* Fixed: discount for child product applied to subsequent child products without discount
* Fixed: child products not addable when max stock not specified
* Fixed: use first variation if default variation not set in products-swatches.php
* Updated: disabled admin notice for inactive licence key
* Updated: on-page help for issues activating licences
* Updated: performance improvement on textarea fields

= 3.7.16, 12 September 2020 =
* Fixed: typo in plugin folder name

= 3.7.15, 4 September 2020 =
* Added: pewc_option_name filter
* Added: pewc_disable_add_to_cart option
* Added: Exclude SKUs from child variants option
* Added: Set Product Price option to Calculation fields
* Added: option to display tax suffix after add-on prices
* Added: pewc_after_cart_item_edit_options_text filter
* Fixed: percentage prices for image swatch and radio group options
* Fixed: error checking conditions for number uploads
* Fixed: indicate out of stock child products in radio layout
* Updated: removed duplicate label for upload fields from cart
* Updated: use pewc_cart_item_has_extra_fields to check for Update Product button
* Updated: allow default for products radio layout

= 3.7.14, 26 August 2020 =
* Added: option to update price on product page dynamically
* Fixed: cart line item price incorrect when field hidden by conditions

= 3.7.13, 20 August 2020 =
* Added: conditions based on number of uploaded files
* Fixed: use unformatted option price in select box template
* Fixed: correctly update option prices in image swatch, select and select box fields using percentage pricing
* Fixed: correctly add role-based option price to cart
* Fixed: ghost fields appearing in groups when editing as post types
* Fixed: checkbox default not saving in global fields
* Fixed: products fields that are variation specific and conditional adding costs on product page
* Fixed: check cost conditions from page load
* Fixed: prevent colour picker throwing error in WP5.5
* Updated: include default param in pewc_create_protection_files
* Updated: include private products in products global rule
* Updated: include empty categories in products global rule

= 3.7.12, 11 August 2020 =
* Fixed: uploaded image not added to order when file renaming empty

= 3.7.11, 2 August 2020 =
* Added: pewc_filter_item_value_in_cart filter in checkout and order
* Fixed: upload directory not changing by order if file renaming not enabled
* Updated: check pewc_do_initial_check is a function on pewc_check_conditions

= 3.7.10, 29 July 2020 =
* Added: pewc_filter_item_value_in_cart
* Added: default third param to pewc_cart_item_quantity
* Added: pewc_cart_item_has_extra_fields
* Added: include variations as child products setting
* Added: pewc_check_conditions event
* Added: Polylang support
* Fixed: non-image files not added to order meta
* Fixed: display file name for non-image files in cart and checkout
* Fixed: hidden fields in accordions set to auto height
* Fixed: prices for options with apostrophes or quotes not respected
* Fixed: flat rate radio buttons on summary panel
* Updated: respect WPML fallback setting
* Updated: background-color for pewc-group-content-wrapper
* Updated: trigger conditions and calculations check on file upload
* Updated: replaced all instances of li.pewc-item with .pewc-item in pewc.js

= 3.7.9, 23 July 2020 =
* Added: WPML config file
* Added: autocomplete attribute to number fields
* Added: pewc_always_show_cart_arrow filter
* Fixed: flat rate costs for select box options not added to totals
* Updated: remove duplicate uploads from Dropzone object
* Updated: correct separator for checkbox group options
* Updated: recalculate percentages using pewc_do_percentages event

= 3.7.8, 6 July 2020 =
* Fixed: default value not displaying correctly

= 3.7.7, 3 July 2020 =
* Added: colour picker field
* Added: pewc_condition_value_step filter
* Added: pewc_add_on_price_separator
* Fixed: all translations for global groups displaying when using WPML
* Fixed: filter to hide option prices in cart
* Updated: radio button layout in child products now deselectable
* Updated: run pewc_create_protection_files weekly

= 3.7.6, 11 June 2020 =
* Added: pewc_files_array filter
* Added: pewc_dropzone_thumbnail_width filter
* Added: pewc_dropzone_thumbnail_height filter
* Added: translation strings for Dropzone
* Fixed: Select Box prices not updating
* Fixed: parse error on line_total in functions-conditionals.php
* Fixed: checkbox group where some options had prices not getting added to cart correctly
* Updated: don't display child product IDs as meta data in the order

= 3.7.5, 27 May 2020 =
* Added: pewc_order_upload_dir and pewc_order_upload_url filters
* Fixed: missing URL to uploaded file in order screen
* Fixed: conditions not working for Select Box field
* Updated: pewc_dequeue_tooltips filter to avoid tooltipster conflicts with certain themes
* Updated: removed double quotes from radio field checked attribute

= 3.7.4, 21 May 2020 =
* Fixed: woocommerce_order_number filter calling order_id incorrectly

= 3.7.3, 15 May 2020 =
* Added: pewc_item_object_{$field_id} transient to reduce number of queries
* Fixed: missing conditions on 'OR' for number fields
* Updated: use pewc_maybe_include_tax when calculating option_price in cart

= 3.7.2, 12 May 2020 =
* Fixed: fields with multiple conditions not hidden correctly in cart
* Updated: enqueue wpColorPicker in Customizer
* Updated: include woocommerce_order_number filter when moving uploaded files

= 3.7.1, 7 May 2020 =
* Added: Select Box field type
* Added: category ID to category names in global rules
* Added: pewc_show_field_prices_in_order filter to hide all add-on prices in the order and order confirmation email
* Fixed: conditional fields visibility not recognised correctly
* Updated: renamed display name for renamed uploaded file
* Updated: check for order add-on meta data and display using old method if necessary
* Updated: respect tax settings where enter and display settings are contrary
* Updated: add-on field prices added to order meta data

= 3.7.0, 30 April 2020 =
* Added: option to rename uploaded files
* Added: option to organise uploads by order number
* Added: download all uploaded files per order
* Added: unserialised add-on meta data in order items
* Added: unserialised meta item data in export
* Added: option to upload PDFs
* Added: min/max settings for Image Swatches
* Added: add-on data to order again buttons
* Fixed: image swatch fields conditionally hidden displaying selection in summary panel
* Fixed: apostrophes in select fields failing conditional checks in cart
* Updated: removed pewc_maybe_include_tax from pewc_field_label

= 3.6.3, 14 April 2020 =
* Added: pewc_enable_assign_duplicate_groups filter allowing users to duplicate and assign groups to other products
* Updated: added default param to pewc_filter_field_title

= 3.6.2, 7 April 2020 =
* Added: pewc_field_item_price_step filter
* Fixed: new global group descriptions not saving
* Fixed: posted child product independent quantities not repopulating after failed validation
* Fixed: lightbox fields not updating fields in main page
* Updated: display variation get_formatted_name in child product select field in products-column.php
* Updated: enabled percentage pricing for Image Swatch options

= 3.6.1, 2 April 2020 =
* Added: pewc_multiply_child_product_quantities filter
* Fixed: missing $group_id in child products field
* Fixed: calculations not updating values
* Updated: full-width number field in Customizer

= 3.6.0, 1 April 2020 =
* Added: role-based prices for add-ons
* Added: pewc_get_field_price function
* Added: number field width setting to Customizer
* Added: enable per unit pricing for Number fields with Bookings for WooCommerce plugin
* Added: pewc_flat_rate_fee_is_taxable and pewc_flat_rate_fee_tax_class filters
* Added: pewc_default_field_value filter
* Added: pewc_check_did_action filter
* Added: optionally display original product price in cart and order
* Fixed: stripslashes for all fields when adding to cart
* Fixed: conditional values containing apostrophes
* Fixed: don't display min val and max val on non-number/NYP fields
* Fixed: save translated global groups when WPML is active
* Fixed: child products in select field displaying zero prices
* Fixed: prevent Customizer loading when theme is Hello Elementor
* Fixed: hidden, required fields with values as arrays getting incorrectly validated
* Fixed: deleting condition from multiple conditions deletes all conditions
* Updated: set field widths to 100% by default
* Updated: Dutch translation

= 3.5.5, 10 March 2020 =
* Added: don't display hidden calculation fields in cart or order
* Fixed: incorrectly counting line breaks in price per character fields
* Fixed: strip slashes from textarea fields

= 3.5.4, 4 March 2020 =
* Added: pewc_filter_global_categories_taxonomy filter
* Fixed: empty conditions for radio groups and image swatches not firing correctly
* Fixed: linked child product quantities not setting correctly
* Fixed: look up calculation not finding first index correctly

= 3.5.3, 26 February 2020 =
* Added: use product dimensions in calculations
* Added: pewc_display_child_product_meta filter to display child product IDs in parent product meta in cart
* Added: default values for products fields using select layout
* Added: pewc_menu_position filter to adjust menu position
* Fixed: parse error when exporting add-ons with incorrect order number
* Fixed: removed allow_none parameter when filtering remove item icon in cart
* Fixed: hidden child products getting added to cart
* Updated: added readonly parameter to date field
* Updated: set pewc-field-select_placeholder field type to text

= 3.5.2, 21 February 2020 =
* Fixed: select field options not added to flat rate
* Fixed: image swatch fields not editable
* Fixed: image swatch and checkbox group values sometimes getting carried into the next field's value
* Updated: changed 'View product' to 'Update options' in line with variable products

= 3.5.1, 20 February 2020 =
* Fixed: parse errors in functions-single-product.php

= 3.5.0, 20 February 2020 =
* Added: {look_up_table} parameter for calculation fields
* Added: initial support for replacing product image - checkbox and image swatch fields
* Added: pewc_group_display filter
* Fixed: hidden number fields with min/max values not validating correctly
* Fixed: not all global groups displaying when using post types
* Fixed: used floatval in $variant_price in products-column.php
* Fixed: allow 0 as default value
* Fixed: global group operator not saving correctly

= 3.4.4, 13 February 2020 =
* Fixed: conditions based on Products fields not setting correctly
* Fixed: radio group and swatch fields sometimes doubling option price
* Fixed: ensure totals don't display NaN from Bookings for WooCommerce add-ons

= 3.4.3, 11 February 2020 =
* Added: pewc_before_update_field_all_params filter
* Added: pewc_dropzone_timeout filter
* Added: pewc_itemmeta_admin_item filter
* Fixed: parse error in functions-cart.php from empty _child_quantity_
* Fixed: default values not saving on new fields
* Updated: format price totals with separator

= 3.4.2, 28 January 2020 =
* Added: extra styles in the Customizer
* Added: support for woocommerce_order_number filter
* Fixed: global fields getting deleted when updating product pages

= 3.4.1, 27 January 2020 =
* Added: pewc_filter_default_price for Fees and Discounts integration
* Added: original_price parameter in cart data for Fees and Discounts integration
* Fixed: fields without price not displaying in summary panel
* Fixed: standard upload field adding price even when empty

= 3.4.0, 21 January 2020 =
* Added: edit add-on fields in products already added to cart
* Added: pewc_hidden_field_types_in_cart filter to hide field types in cart and checkout
* Added: pewc_after_price_subtotal_table action
* Added: field styles in the Customizer
* Added: pewc_bypass_is_admin_check_in_groups_filter filter to avoid is_admin check when getting global groups
* Updated: use wp_kses_post for sanitising radio and image swatch option labels

= 3.3.9, 8 January 2020 =
* Added: uploaded image thumbnail to order pages
* Added: link to uploaded image thumbnail in order emails
* Added: pewc_end_get_item_data filter
* Added: pewc_add_order_itemmeta_admin filter
* Fixed: spaces removed in text fields when max chars is reached
* Fixed: missing dashicons on front end
* Fixed: check that options are defined in update_conditional_value_fields in admin-fields.js

= 3.3.8, 10 December 2019 =
* Added: pewc_rules_transient_pewc_group_xxx transients for condition rules
* Added: pewc_calculation_global_calculation_vars filter for multiple global variables
* Fixed: update field type when duplicating fields
* Fixed: 'is not' conditions not saving for image swatch fields
* Fixed: number format in data-option-cost in products-column.php
* Fixed: name conflict with ACF when removing groups
* Updated: removed input detection from pewc-radio-form-field and pewc-checkbox-form-field condition changes
* Updated: step pewc_variable_2 and 3

= 3.3.7, 21 November 2019 =
* Fixed: missing duplicated fields
* Fixed: multiple uploads not pricing correctly

= 3.3.6, 19 November 2019 =
* Added: pewc_duplicate_fields option to duplicate fields and groups when duplicating products
* Fixed: validation strings not translatable
* Fixed: parse error for missing $post->ID
* Fixed: Dropzone already attached error
* Fixed: group layout not saving in table layout
* Fixed: global field options not saving correctly after first option is deleted

= 3.3.5, 6 November 2019 =
* Added: new filters for add to cart buttons in blocks
* Added: pewc_apply_random_hash_child_product filter
* Added: pewc_order_item_product_name filter
* Fixed: uploaded files not displaying in cart and order
* Fixed: parse errors in export

= 3.3.4, 1 November 2019 =
* Fixed: parse error for View product button on some themes

= 3.3.3, 30 October 2019 =
* Fixed: parse error after add to cart validation for products without add-ons
* Fixed: only show thumbs for image files in the cart
* Fixed: radio group default option
* Updated: Elementor styles
* Updated: include original classes in button link in archives

= 3.3.2, 25 October 2019 =
* Fixed: group and field ordering

= 3.3.1, 24 October 2019 =
* Fixed: parse error for empty option values

= 3.3.0, 23 October 2019 =
* Added: pewc_enable_groups_as_post_types filter to view global groups as standard post types
* Added: pewc_enable_ajax_load_addons filter to load add-on fields on edit screens via AJAX
* Added: pewc_after_option_header and pewc_after_option_row actions
* Added: pewc_filter_validate_cart_item_status filter
* Added: pewc_filter_cart_item_data filter
* Added: pewc_filter_permitted_cats filter
* Added: use quantity in calculations
* Added: empty option in conditions based on select and radio fields
* Fixed: products select field showing zero prices
* Fixed: calculations decimal places setting to 0
* Fixed: thumbnail not displaying in AJAX uploader
* Updated: reverted default step attribute in Number fields to 1
* Updated: enable discount fields for all child product quantity types
* Updated: selected image swatch border colour
* Updated: mobile swatches single column
* Updated: products-column.php template to allow variation descriptions
* Updated: use pewc_global_order transient
* Updated: remove unnecessary queries for non-existent conditions
* Updated: use multiple variables for group and field parameters
* Updated: global groups now displayed as standard post types
* Updated: dropzone.js to 5.5.0

= 3.2.19, 18 October 2019 =
* Added: prefix_filter_field_option_name filter
* Added: pewc_filter_initial_accordion_states filter
* Fixed: correctly calculate percentages for options in cart
* Updated: allow textarea sanitisation
* Updated: sanitise information field using wp_kses_post

= 3.2.18, 16 October 2019 =
* Added: Elementor styles
* Fixed: percentages select options in simple products not calculating correctly
* Fixed: inactive variation dependent field costs included in total price on product page
* Fixed: radio options respect percentage setting
* Fixed: missing 'Default' param for select fields

= 3.2.17, 9 October 2019 =
* Added: pewc_number_field_step filter
* Fixed: correctly respect multiple conditions

= 3.2.16, 24 September 2019 =
* Added: pewc_hidden_group_types_in_order filter
* Updated: trigger calculations on page load
* Updated: allow calculations without input fields

= 3.2.15, 23 September 2019 =
* Added: $value parameter to pewc_filter_end_add_cart_item_data filter

= 3.2.14, 18 September 2019 =
* Added: $cart_item_data and $quantity parameters to pewc_get_conditional_field_visibility
* Added: conditions based on quantity
* Added: pewc_after_option_params action
* Added: multiple filters for AJAX file upload strings
* Fixed: correctly respect conditions based on products

= 3.2.13, 7 September 2019 =
* Fixed: pewc_filter_end_add_cart_item_data filter
* Fixed: child product checkbox layout respects discounts
* Fixed: strip slashes from text fields

= 3.2.12, 29 August 2019 =
* Added: pewc_filter_end_add_cart_item_data filter
* Fixed: information fields not displaying correctly for Basic licences

= 3.2.11, 20 August 2019 =
* Added: pewc_filter_child_products_method filter
* Fixed: incorrectly validating required upload fields

= 3.2.10, 17 August 2019 =
* Added: pewc_option_price_separator filter
* Added: additional parameters for pewc_filter_minchars_validation_notice and pewc_filter_minchars_validation_notice filters
* Fixed: allow multiple ajax uploads fields per product
* Fixed: min / max char validation only on required fields

= 3.2.9, 2 August 2019 =
* Fixed: JS error on upload fields

= 3.2.8, 1 August 2019 =
* Added: increased number of columns for image swatches
* Added: pewc_total_only_text filter
* Added: pewc_after_create_product_extra action
* Added: additional parameters for pewc_filter_validation_notice
* Fixed: respecting conditions based on products fields
* Fixed: media upload fields in group post types
* Fixed: respecting min and max chars in textareas
* Fixed: show min/max for new checkbox fields

= 3.2.7, 5 July 2019 =
* Fixed: checkbox swatches not toggling class
* Updated: extended pewc_is_group_public filter to all field types with options

= 3.2.6, 5 July 2019 =
* Added: filter to hide prices in options
* Updated: respect percentage setting for select field options
* Updated: greater than and less than operators for numeric field conditions

= 3.2.5, 3 July 2019 =
* Fixed: issues with conditionals for calculation fields

= 3.2.4, 1 July 2019 =
* Fixed: issues with AJAX uploads

= 3.2.3, 28 June 2019 =
* Fixed: Tabs and Accordion layout

= 3.2.2, 28 June 2019 =
* Fixed: JS error when dropzone.js not enqueued
* Fixed: JS error when formula missing in calculation field

= 3.2.1, 28 June 2019 =
* Added: AJAX upload option
* Fixed: allow multiple file uploads
* Fixed: global information fields not saving correctly
* Fixed: default radio button value not set
* Updated: reduce size of image thumb in order email

= 3.2.0, 24 June 2019 =
* Added: swatch option to variable child products
* Added: information field type
* Added: allow multiple uploads setting
* Fixed: escape condition fields with apostrophes
* Fixed: conditional field visibility not correctly evaluating on add to cart
* Updated: conditionally enqueue math.js

= 3.1.2, 13 June 2019 =
* Fixed: checkboxes in global groups not saving correctly

= 3.1.1, 13 June 2019 =
* Added: group layout option
* Fixed: clear product price when no variation set
* Updated: cost and action settings for calculation field
* Updated: exclude upload fields from conditions

= 3.1.0, 10 June 2019 =
* Added: calculation field

= 3.0.2, 8 June 2019 =
* Added: hide groups where all fields are hidden
* Added: option to attach uploaded images to order email
* Fixed: missing select_placeholder parameter
* Fixed: options in global conditions not populating correctly
* Fixed: incorrectly removing uploaded images
* Fixed: duplicated group and field conditions
* Fixed: default values not displaying correctly
* Updated: restored duplicate global groups
* Updated: reinstated allow_multiple parameter
* Updated: don't check character fields for non-text fields
* Updated: timing on initial page load for pewc_update_total_js

= 3.0.1, 4 June 2019 =
* Fixed: global groups not deleting correctly

= 3.0.0, 3 June 2019 =
* Added: allow html in group description
* Added: further front end template filters
* Added: pewc_flat_rate_label filter
* Fixed: checkbox group field values persisting in fields
* Fixed: image swatch prices not added
* Fixed: parse errors in field-item.php
* Fixed: parse error in field description
* Fixed: missing cost value in condition
* Fixed: JS error when setting condition rule fields
* Fixed: condition cost operator not setting correctly
* Fixed: removing conditions incorrectly hiding condition rules
* Fixed: checkbox default value not retained correctly
* Fixed: repeat pewc_update_total_js after running to help quicker browsers
* Updated: Pro fields visible to Basic users
* Updated: populate pewc_product_extra post with order details when customer is not registered
* Updated: CSS for globals page
* Updated: default total for variable products set to 0
* Updated: uploads no longer moved to media folder
* Updated: migrated product extras data to custom post type

= 2.8.6, 29 May 2019 =
* Added: updater upgrade functions

= 2.8.5, 21 May 2019 =
* Added: beta testing option
* Fixed: reinstated child product functions lost due to version control
* Fixed: zero value number field not validating correctly

= 2.8.4, 10 May 2019 =
* Fixed: hidden child products added to cart
* Updated: POT file and Dutch translation

= 2.8.3, 7 May 2019 =
* Fixed: correctly enqueue pewc-variations.js script

= 2.8.2, 6 May 2019 =
* Updated: changed plugin name to WooCommerce Product Add-Ons Ultimate

= 2.8.2, 3 May 2019 =
* Fixed: removed field price from Products field type
* Fixed: spaces and accented characters counted incorrectly
* Updated: deprecated Allow Multiple option from Products field

= 2.8.1, 1 May 2019 =
* Added: pewc_force_update_total_js trigger to JS
* Fixed: inactive variation specific fields updating price on product page
* Fixed: incorrect validation on hidden product fields with min/max products
* Updated: allow separate flat rate charges for variations
* Updated: reduced length of field ID string

= 2.8.0, 18 April 2019 =
* Added: product cost conditions
* Added: filter for multiple file uploads
* Fixed: default values not setting correctly
* Fixed: condition rules not saving correctly

= 2.7.0, 16 April 2019 =
* Added: minimum and maximum quantities for child product fields
* Fixed: variation prices not updating correctly
* Updated: additional methods for pewc-child-quantity-field field updates

= 2.6.1, 11 April 2019 =
* Updated: allow independent child products to be deleted in the cart
* Updated: allow independent child products quantities to be updated in the cart

= 2.6.0, 9 April 2019 =
* Added: column layout for child products
* Added: support for variable child products
* Fixed: parse error in global settings
* Updated: removed AJAX totals updater in pewc.js

= 2.5.1, 5 April 2019 =
* Fixed: mini cart returning zero price for products without extras

= 2.5.0, 4 April 2019 =
* Added: variation-specific fields
* Fixed: restrict per character pricing to text and textarea fields only
* Fixed: update product price in mini cart

= 2.4.12, 28 March 2019 =
* Added: allow conditions on checkbox groups and product fields
* Fixed: duplicate options for conditions

= 2.4.11, 17 March 2019 =
* Added: display upload thumbs in cart and checkout
* Fixed: conditional fields dependent on checkboxes not saving correctly
* Fixed: flat rate input fields not appearing in order confirmation
* Updated: disabled autocomplete for datepicker fields

= 2.4.10, 4 March 2019 =
* Fixed: conditions for radio groups not firing correctly

= 2.4.9, 21 February 2019 =
* Fixed: condition values getting overwritten

= 2.4.8, 19 February 2019 =
* Fixed: parse error when adding variable child product to cart

= 2.4.7, 16 February 2019 =
* Updated: licensing after site migration

= 2.4.6, 13 February 2019 =
* Updated: provide support for non-image uploads

= 2.4.5, 13 February 2019 =
* Added: better sanitisation for fields
* Added: key element for radio fields
* Fixed: remove child product from cart when parent quantity set to 0
* Fixed: new condition fields not retaining action and rule settings
* Fixed: pewc_get_permitted_mimes filter

= 2.4.4, 25 January 2019 =
* Fixed: changed permitted mime element to 'jpg|jpeg|jpe'	=> 'image/jpeg'
* Updated: removed simple products requirement from json_search in Products field

= 2.4.3, 21 January 2019 =
* Added: actions after each field
* Added: checkbox option for swatch field
* Added: pewc_name_your_price_step filter for Name Your Price field
* Fixed: missing checkbox group items in order screens
* Fixed: parse error in functions-conditionals.php
* Fixed: default values overriding submitted values
* Updated: field description now runs off pewc_after_field_template hook
* Updated: changed name of Radio Image to Image Swatch

= 2.4.2, 9 January 2019 =
* Added: pewc_filter_item_start_list filter
* Fixed: re-allow negative values for fields
* Fixed: parse error on missing placeholder in field-item.php
* Fixed: NaN error on child products with zero value

= 2.4.1, 24 December 2018 =
* Fixed: missing <li> tags in checkbox group
* Updated: change hook for creating new product extra to woocommerce_checkout_order_processed

= 2.4.0, 16 December 2018 =
* Added: German translation
* Added: customizer support
* Added: pricing and subtotal labels and options

= 2.3.2, 11 December 2018 =
* Fixed: conditionals dependent on radio groups not adding to cart correctly
* Fixed: undefined variable in global extras
* Fixed: added space between attributes in front end form fields

= 2.3.1, 27 November 2018 =
* Fixed: new global groups not saving correctly
* Fixed: removed esc_html from field names containing formatted prices

= 2.3.0, 22 November 2018 =
* Added: checkbox groups
* Added: products field in global extras
* Fixed: respect tax settings for product prices
* Fixed: respect tax settings for option prices
* Fixed: correctly calculate totals when using percentage fields
* Fixed: conditions dependent on checkboxes now functioning correctly
* Updated: formatted option prices
* Updated: changed pewc_get_price_for_display to pewc_maybe_include_tax
* Updated: percentage values for variations update dynamically
* Updated: removed pewc_filter_field_label filter to display percentage instead of price

= 2.2.3, 20 November 2018 =
* Fixed: global condition not retaining field from other group
* Updated: tweaked styles for default parameter in new fields

= 2.2.2, 13 November 2018 =
* Added: explanatory text in Product Extras page
* Added: explanatory text in Product Add-Ons page
* Fixed: removed escaping characters from field and group titles
* Fixed: global conditions not picking up fields from other groups
* Fixed: PHP error for missing pewc_product_hash
* Fixed: prevent order without Product Add-Ons generating a new product extra post
* Updated: changed dashicon to plus-alt
* Updated: changed post type label to 'Extras by Order'

= 2.2.1, 6 November 2018 =
* Fixed: prevent 'View Product' button displaying for products that don't have extras
* Updated: French, Italian and Spanish translations

= 2.2.0, 1 November 2018 =
* Added: child products (Pro only)
* Added: tooltips
* Fixed: validation for radio and select fields
* Fixed: 0 default values
* Fixed: missing prices for extras in order confirmation
* Fixed: hide flat rate items in product itemisation in order confirmation
* Fixed: min_date_today field not saving correctly
* Updated: improved price formatting for extras
* Updated: extra prices now respect the WooCommerce tax display setting
* Updated: improved UX for conditionals
* Updated: updated UI
* Updated: changed icon to wcicon-plus
* Updated: removed pewc_filter_is_purchasable and replaced with pewc_view_product_button

= 2.1.8, 31 October 2018 =
* Fixed: date field not validating correctly

= 2.1.7, 29 October 2018 =
* Fixed: Name Your Price field not validating correctly

= 2.1.6, 29 October 2018 =
* Fixed: Name Your Price field not validating correctly
* Fixed: select and radio fields not validating correctly

= 2.1.5, 22 October 2018 =
* Fixed: admin styles for select fields

= 2.1.4, 21 October 2018 =
* Added: 'Instruction only' option for select fields
* Fixed: field image in Global Add-Ons
* Fixed: radio button prices not updating correctly in totals

= 2.1.3, 18 October 2018 =
* Added: integration with WooCommerce PDF Invoices & Packing Slips
* Fixed: missing colon in order confirmation and emails
* Fixed: radio image buttons displaying arrays as labels

= 2.1.2, 30 September 2018 =
* Added: Dutch translation
* Fixed: flat rate pricing in radio buttons
* Fixed: retain field values after validation fails
* Updated: allow HTML in Description field

= 2.1.1, 27 September 2018 =
* Added: conditions for global extras
* Fixed: prevent non-object error in functions-order.php for empty $user object
* Fixed: add correct flat rate values for select and radio button fields
* Fixed: values of select fields not getting added to cart
* Updated: improved conditional field population using JS

= 2.1.0, 18 September 2018 =
* Added: allow free characters (Pro only)
* Added: only allow alphanumeric characters (Pro only)
* Added: only charge for alphanumeric characters (Pro only)
* Fixed: duplicated pewc-field-label class
* Fixed: correctly save Price Per Character value for new fields
* Updated: deprecated import feature
* Updated: text and textarea field templates

= 2.0.1, 13 September 2018 =
* Fixed: out of memory error in import-groups.php

= 2.0.0, 10 September 2018 =
* Added: Radio buttons with image backgrounds (Pro only)
* Added: Percentages (Pro only)
* Added: Group toggles and tabs (Pro only)
* Added: French translation
* Added: Italian translation
* Added: Spanish translation
* Added: upgrade action links
* Fixed: incorrect default value in text fields following a select or radio field
* Fixed: new condition field not showing select options
* Updated: better detection of radio button selection
* Updated: admin templates moved to templates/admin
* Updated: created separate template files for all field types on the frontend
* Updated: pewc_field_label returns value instead of echoing
* Updated: pewc_field_description returns value instead of echoing
* Updated: removed pewc-product-extra-group-wrap class in favour of pewc-group-wrap

= 1.7.4, 15 August 2018 =
* Added: Portuguese translation
* Added: WooCommerce Subscriptions support
* Fixed: formatting issue for 'Duplicate' link in Products table
* Updated: ensure pewc_product_extra_fields only runs once
* Updated: displays extra fields on all product types

= 1.7.3, 15 August 2018 =
* Fixed: radio button conditionals triggering duplicated fields
* Updated: add pewc-has-maxchars class correctly to fields

= 1.7.2, 14 August 2018 =
* Added: field images
* Added: filterable classes for group wrap div
* Added: prevent users entering more than the max chars for input fields
* Fixed: parse errors in empty field values
* Updated: .pot file

= 1.7.1, 2 August 2018 =
* Fixed: undefined qty for products without quantity selector

= 1.7.0, 1 August 2018 =
* Added: flat rate extras
* Fixed: total calculation error with right space currency position
* Fixed: global extras not showing on products with no local extras
* Updated: improved totals fields on product page

= 1.6.1, 30 July 2018 =
* Added: multiplier option on number fields
* Fixed: global extra rules

= 1.6.0, 30 July 2018 =
* Added: global extras
* Fixed: remove deleted conditions from front end
* Fixed: display options group for new radio and select fields

= 1.5.3, 21 June 2018 =
* Added: modal image viewer in Product Extras entries
* Added: modal image viewer in Product Add-Ons entries
* Fixed: deleting product extra group data on save
* Updated: set create_posts capability for pewc post type to do_not_allow

= 1.5.2, 14 May 2018 =
* Fixed: prices for multiple fields of the same type not totalling correctly

= 1.5.1, 3 May 2018 =
* Added: support for WooCommerce Print Invoices/Packing Lists

= 1.5.0, 27 April 2018 =
* Added: radio button group
* Added: default values
* Added: span wrapper for prices in cart meta data
* Added: discount pricing - select extras to reduce the product cost
* Fixed: too many parameters for pewc_order_item_name
* Updated: spaces no longer costed in cost per character fields

= 1.4.5, 6 April 2018 =
* Added: filter for Total heading on single product page
* Added: upload URLs in order meta
* Fixed: hidden required uploads forcing validation to fail

= 1.4.4, 6 April 2018 =
* Added: product extra line item meta on edit order screen

= 1.4.3, 4 April 2018 =
* Added: added pewc-description to description fields
* Added: permitted file type at add to cart validation
* Fixed: overwriting line items in Product Extras custom post type
* Fixed: overwriting line items in Product Add-Ons custom post type

= 1.4.2, 15 March 2018 =
* Updated: wrap order item prices in span tags

= 1.4.1, 20 February 2018 =
* Fixed: incorrectly adding variation price to cart
* Fixed: parse error for empty conditional
* Fixed: incorrectly priced file uploads

= 1.4.0, 9 February 2018 =
* Added: support for variable products
* Updated: default pewc_require_log_in set to no
* Updated: moved log in requirement to upload fields, not all fields

= 1.3.3, 22 January 2018 =
* Fixed: set product price in cart via woocommerce_add_cart_item
* Updated: improved integration with Bookings

= 1.3.2, 19 January 2018 =
* Added: added per_unit field for new fields

= 1.3.1, 17 January 2018 =
* Updated: improved Bookings for WooCommerce integration

= 1.3.0, 17 January 2018 =
* Added: support for Bookings for WooCommerce plugin

= 1.2.4, 16 January 2018 =
* Fixed: correctly remove associated conditions when field is deleted
* Updated: product name for updater

= 1.2.3, 22 November 2017 =
* Added: Price per character option for text input and textarea fields
* Updated: subtotal calculated directly in JS, not via AJAX
* Updated: allow Product Extras on simple products only
* Updated: allow Product Add-Ons on simple products only

= 1.2.2, 21 November 2017 =
* Added: Name Your Price field
* Added: min and max attributes for number fields
* Fixed: missing ID attribute in new field type fields

= 1.2.1, 13 November 2017 =
* Added: total field on product page
* Fixed: parse error condition_action
* Fixed: not adding hidden items to cart
* Updated: 'is-not' parameter not allowed for conditions on checkboxes

= 1.2.0, 8 November 2017 =
* Added: group and field duplication
* Updated: icon font to WooCommerce
* Updated: updater class

= 1.1.0, 6 November 2017 =
* Added: conditional fields

= 1.0.1, 14 October 2017 =
* Fixed: removed duplicate updater class

= 1.0.0, 14 October 2017 =
* Initial commit

== Upgrade Notice ==
