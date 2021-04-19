=== WooCommerce Sync for QuickBooks Desktop ===
Contributors: myworksdesign
Donate link: https://myworks.software
Tags: woocommerce, quickbooks, realtime, manual, sync, crm, pull, push, multilingual, multicurrency, multisite, product, tax, payment, customer, coupon, shipping
Requires at least: 4.0
Requires PHP: 5.6
Tested up to: 5.2.1
Stable tag: 1.4.12
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Automatically sync your WooCommerce store to QuickBooks Desktop, all in real-time! Easily sync your orders, customers, inventory and more from your WooCommerce store to QuickBooks Desktop. Your complete solution to streamline your accounting workflow - with no limits.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Follow the step by step wizard to finish your installation and go live.

== Screenshots ==
 
1. This screen shot description corresponds to screenshot-1.(png|jpg). This describes plugin's dashboard


== Upgrade Notice ==
 
= 1.3.4 =
This is a recommended upgrade in order to improve our Refresh Data capability and remain compatible with PHP 7.2


== Changelog ==

= version 1.5 =
* Added setting to sync Sales Order along with Invoice & Payment to QuickBooks for a WooCommerce order
* Added setting to sync customers into QuickBooks as a Sub-Customer (Job) on a per-role basis
* Added setting to sync artificial payments to QuickBooks based on order status
* Added setting to sync a Sales Order to QuickBooks along with an invoice + payment
* Added setting to set "IsPending" status of invoices synced to QuickBooks
* Added setting in Settings > Taxes to allow control of the rate in the QuickBooks order when syncing tax as a line item
* Added setting to sync order notes to QuickBooks as a line item
* Added {phone_number} merge tag in New Customer Display Name setting and Ship to setting
* Added setting to Sync Meta Data to Line Item Description in QuickBooks order
* Added setting to use "Max Available" value when syncing inventory for Inventory Assembly Items
* Added setting to consolidate "override" customer mapping into one setting (by billing/shipping name/company)
* Added setting to adjust invoice due date out by X days in the future
* Added setting to not include shipping line item in orders if $0 in WooCommerce order
* Added setting to support syncing inventory for Quantity on Hand - Quantity on Sales Order + Quantity on Purchase Order
* Added setting to sync transaction fees into QuickBooks orders as negative line item
* Added improved support for Grouped products when syncing Sales Orders & Estimates
* Added improved support for handling variation names over 50 characters
* Added multi-currency support for both WooCommerce and QuickBooks Desktop
* Added setting to automatically recognize new customers/products - so Refresh Data is no longer necessary after initial run
* Added settings to set Payment Terms, Payment Methods & Price Level for new customers synced into Quickbooks
* Added setting to set "Email Later" for orders synced to QuickBooks
* Added Automap option for products to use Item Description
* Changed the default "Save Log for Days" setting to 30 days
* Changed trial licenses to have access to the last 7 days of orders to sync
* Improved handling of error 3180 - where customer can't be added to QuickBooks because customer list is temporarily locked
* Improved handling of automatically adding orders in custom / Complete status to queue
* Improved Pull > Inventory/Price pages to only show mapped products
* Improved handling to default to  First/Last Name if no Company Name is present when syncing customers by company name
* Improved handling of orders with only first + last name and email address
* Improved the Compound tax layout/UI in Map > Taxes
* Improved class handling to allow global setting to act as a "catchall" while still allowing setting class on per-line item basis
* Improved logging for Inventory pull to log both name/sku of product
* Improved settings/mappings account dropdowns to have a smarter list of only applicable accounts
* Improved handling of non-coupon discount orders and $0 discounted taxed orders to correctly sync
* Improved Map > Tax page to show WooCommerce cities, and QuickBooks rate %
* Improved handling of orders with simple/bundle products mapped to Grouped products in QuickBooks
* Improved handling of orders with simple/bundle products mapped to Inventory Assembly products in QuickBooks
* Improved handling of syncing variable products - ensuring the parent variable isn't automatically synced
* Improved handling of syncing refunds into QuickBooks, even if original order has not synced
* Improved license check to automatically check when web connector runs, eliminating issues with licenses needing to be re-saved in Connection tab
* Resolved mapping setting where "Default Display Name for New Customers" wasn't being honored if searching matches by company name
* Resolved issue where shop manager users may not see customer/product dropdown contents
* Resolved rare issue where inventory sync wouldn't work with MultiLocation plugin
* Removed setting to "Recognize users in custom roles" - all roles are now recognized by default 
* Removed setting to sync Inventory from WooCommerce > QuickBooks - replaced by switch to sync inventory change inside individual products



= 2019-05-31 - version 1.4.12 =
* Added setting to sync orders with the date of either the order date or the date paid
* Improved license check to resolve issues with sync becoming unlicensed
* Resolved false positive error message encountered in Wordpress 5.2 in some rare cases
* Added custom line item mapping functionality in Map > Shipping Methods
* Improved Push > Orders/Payments pages to set filter to last 30 days when first loading, for performance

= 2019-05-22 - version 1.4.11 =
* Added optimizations to Map > Variations to improve loading speed
* Added compatibility with connecting over IPV6 addresses
* Added option in MyWorks Sync > Connection page to control per-connection batch size
* Added setting to control syncing invoices/payments to specific A/R account in QuickBooks, based on the WooCommerce gateway
* Added additional compatibility with UPS Shipping plugins
* Resolved issue when saving multiple pages of tax mappings
* Resolved UI issue where Add to Queue was missing from the Bulk Actions menu
* Improved handling of creating new customers in QuickBooks so the Tax Code and Tax Item in the customer record matches their corresponding order
* Improved syncing product variations by adding support to ensure variation names are unique when syncing to QuickBooks
* Improved syncing new customers into Quickbooks - with the "Sales Tax Code" of the customer following whether the related order is taxable or not.

