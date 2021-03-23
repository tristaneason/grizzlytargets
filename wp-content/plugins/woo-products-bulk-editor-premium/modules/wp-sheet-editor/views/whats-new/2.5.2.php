<?php

$buy_link = VGSE()->bundles['custom_post_types']['inactive_action_url'];
$items = array(
	sprintf(__('New feature - Added support for Advanced Custom Fields +v5.0.0. (Available for paid users only. <a href="%s" target="_blank">Upgrade</a>)', VGSE()->textname), $buy_link),
	sprintf(__('New feature - Spreadsheet formulas - Added support for regular expressions (Available for paid users only. <a href="%s" target="_blank">Upgrade</a>)', VGSE()->textname), $buy_link),
	sprintf(__('New extension - WooCommerce Customers Spreadsheet. View all customer profiles, View shipping and Billing info, Advanced Searches, and more. <a href="%s" target="_blank">View Extension</a>)', VGSE()->textname), 'https://wpsheeteditor.com/extensions/woocommerce-customers-spreadsheet/'),
	sprintf(__('Updated extension - Frontend Spreadsheets. Let your users create and edit Information like Events, Business listings, WooCommerce Products, Posts, etc. <a href="%s" target="_blank">View Extension</a>)', VGSE()->textname), 'https://wpsheeteditor.com/extensions/frontend-spreadsheet-editor/'),
	__('Improved tools: formulas engine', VGSE()->textname),
	__('Improved tools: advanced search', VGSE()->textname),
	__('Fixed more than 19 bugs', VGSE()->textname),
	__('Improved 10 features', VGSE()->textname),
	__('Updated 4 extensions.', VGSE()->textname),
	sprintf(__('<a href="%s" target="_blank">View the entire changelog</a>', VGSE()->textname), 'https://wpsheeteditor.com/changelog/'),
);
