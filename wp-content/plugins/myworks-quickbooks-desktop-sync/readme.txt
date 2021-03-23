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

v1.4.12 (2019.05.31)
* Added setting to sync orders with the date of either the order date or the date paid
* Improved license check to resolve issues with sync becoming unlicensed
* Resolved false positive error message encountered in Wordpress 5.2 in some rare cases
* Added custom line item mapping functionality in Map > Shipping Methods
* Improved Push > Orders/Payments pages to set filter to last 30 days when first loading, for performance

v1.4.11 (2019.05.22)
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

v1.4.10 (2019.03.26)
* Resolved UI issue where Add to Queue button was no longer in place on WooCommerce > Orders page
* Resolved UI issue where Map > Payment 

v1.4.9 (2019.03.18)
* Improved support for v1.13.0 of Sequential Order Numbers Pro
* Improved support when adding post-Processing orders and their payments to the queue
* Added setting to add a subtotal line at the bottom of orders synced into QuickBooks
* Improved minor UI elements


v1.4.8 (2019.03.18)
* Added status of "Queued" for orders already in the queue
* Resolved rare issue where orders with free shipping error out with Error:The "" field has an invalid value "".
* Improved handling when adding both an order + payment to queue on status other than Processing
* Improved issues with some orders containing free shipping erroring out when syncing
* Improved handling to more efficiently sync inventory levels from QuickBooks > WooCommerce (reading only mapped products)
* Added functionality to improve recognizing Amazon Pay and PayPal Express Checkout gateways in Payment Mapping
* Adjusted payments syncing in with Sales Orders to not autoapply to invoices
* Adjusted logic to sync shipping address as long as either first/last name or company name is present
* Improved support with WooCommerce MultiLocation


v1.4.7 (2019.01.16)
* Resolved issues with adding orders to the queue from WooCommerce > Orders


v1.4.6 (2019.01.07)
* Added a setting to hide variable parent products from Map/Push > Products by default
* Added a setting to read inventory levels from sales orders not in an inventory site when syncing inventory from QuickBooks
* Added a setting to control if an order should show as "To Be Printed" once synced into Quickbooks
* Added a setting to set the order date of an order synced into QuickBooks as the date synced into QuickBooks instead of the WooCommerce order date
* Improved support for syncing new customers into QuickBooks - as non-taxable, if the accompanying order is also non-taxable


v1.4.5 (2018.12.04)
* Confirmed compatibility with WooCommerce 3.5.0
* Confirmed compatibility with Wordpress 5
* Added support for Advanced Inventory Bins
* Added support for syncing payments/sales receipts into QuickBooks with no assigned payment method
* Improved support for Store Manager user roles to fully access the sync/plugin functionality
* Improved support for honoring the Out of Stock threshold setting in WooCommerce when syncing inventory
* Improved support for syncing inventory level from one QuickBooks product to multiple WooCommerce products
* Added Pay-To client field when syncing checks as a refund
* Added support for syncing pricing for multiple products from QuickBooks to WooCommerce
* Improved handling for creating new customers with company name included in bill/ship address, and correct tax code based on order
* When dealing with multiple shipping line items, the top/first line now determines the Ship Via mapping
* Added the administrator and subscriber role to our Mapping Settings to be automatically recognized as customers by default


V 1.4.3 (2018.12.04)

* Improved compatibility with syncing sales orders and associated payments separately
* Added ability to sync multiple shipping line items to QuickBooks - coupled with standalone helper plugin
* Added option in Map > Payment Methods to set bank account that refund checks should be issued out of
* Improved compatibility with non-mappable payment methods to sync a Sales Receipt into QuickBooks that's deposited into Undeposited Funds
* Improved compatibility with Advanced Inventory for multiple Inventory Sites and Bin support

V 1.4.1 (2018.10.04)

* Improved setting to Use QuickBooks Line Item Description to be set on a per-item level
* Improved Shipping Method mapping to map Ship Via field in QuickBooks on a per-gateway level in WooCommerce
* Added support to set invoices/sales receipt/sales order templates in QuickBookson a per-role level in WooCommerce
* Improved support for syncing inventory levels from specific inventory sites in QuickBooks
* Improved setting to minimize/eliminate the amount of logs being written to our database tables
* Improved setting for using QuickBooks Sequence # - so the Woo order number is appended to any other contents of the memo field instead of overwriting

V 1.4.0 (2018.09.20)

* Improved ability to add the complete order/payment to the Queue from the Woo > Orders page.
* Added Barcode and MPN values to the Product Automap functionality if Advanced Inventory is enabled.
* Improved Sales Tax mappings to support Sales Tax Groups
* Improved Refund syncing to support partial refunds
* Improved Discount handling to sync discount line items to "Discount" type products in QuickBooks
* Adjusted AppSupportURL to resolve Unreachable errors when adding Web Connector file
* Added role filter functionality in Map > Customers
* Added tab to "Show Combined Rules" as hidden by default on the Map > Tax page
* Added setting to pull inventory levels either as Quantity on Hand, or Quantity Available
* Improved handling of Append ID to duplicate Customers setting, and set to ON by default
* Added a setting to enable validating QuickBooks customers for a match by either Name or Name + Zip Code
* Improved queue handling to ensure only one activity instance is present in the queue at once
* Improved shipping tax code mapping to ensure the shipping tax in QB matches the Woo order tax
* Fixed errors/issue where orders may be added to the Queue more than once
* Added a setting to limit syncing orders before certain ID
* Improved and renamed the setting to Sync new customers to QuickBooks with appended user/order ID setting
* Added a setting to regenerate variation names to be unique, if they weren't correctly generated when initially added
* Added a setting to recognize/scan Inactive QuickBooks customers when determining customer mappings
* Improved role access to ensure admin and shop manager users can access the sync
* Improved automapping for certain parent/child SKU types
* Added filters on mapping pages to only show unmapped items
* Hid the Database Status menu by default
* Improved log purge by purging both the queue and log tables automatically
* Improved the Map > Products automap to correctly show Item Name/Number for QuickBooks
* Resolved issues causing inability to add orders to Queue from Woo > Orders page when searching/filtering
* Resolved issues when syncing product price from QuickBooks into WooCommerce
* Added settings to sync orders to QB in a different format based on the role or gateway used for the WooCommerce Order
* Added a setting to improve how all orders are mapped to one QB customer - on a per role basis
* Added the ability to sync to QuickBooks Desktop companies while QuickBooks is closed
* Improved functionality of the Automap Customers (First+Last Name > Display Name)
* Added setting to use QuickBooks # sequence instead of the WooCommerce Order number when syncing orders into QuickBooks
* Added functionality to support pushing existing mapped customers into QuickBooks to update their mapped customer record in QuickBooks



V 1.3.4 (2018.06.29)

* Added setting in Settings > Mappings to adjust display name format for QuickBooks customers on mapping pages
* Added setting to sync orders to QuickBooks as Estimates
* Added setting in Settings > Tax Codes to allow choice of syncing WooCommerce Tax Rates to either QuickBooks Sales Tax Items or Codes (for AU/CA/UK users)
* Added setting to automatically update order status once synced to QuickBooks
* Added functionality to push/sync refunds. This is in beta still.
* Added setting to automatically sync pricing levels from QuickBooks to WooCommerce
* Removed setting for Shipping Tax Code in Setings > Taxes (on-demand access now, via helper plugin)
* Improved auto-map feature to allow flexible field syncing, as well as to apply the automap only to unmapped customers.
* Improved Refresh Data stability to retain customer/prodcut mappings consistently when refreshing data
* Improved handling in WooCommerce > Orders, when searching for orders, and adding to queue
* Improved compatibility for PHP 7.2


V 1.3.3

* Confirmed compatibility for WooCommerce 3.4.1 and PHP 7.2
* Improved display of how Parent:Child customers show in Mapping pages
* Resolved issue where setting to Append Client ID for duplicate customers wasn't working as intended
* Resolved issue where license connection wouldn't stay activated
* Resolved issue where Avalara Tax Compatibility wouldn't sync into QB without an assigned Tax Code
* Resolved issue with payments occasionally not syncing into QuickBooks when paired with Sales Orders
* Resolved issue where orders containing discounts would sync into QuickBooks with the discount applied twice
* Resolved issue where Shipping Tax codes weren't being appropriately applied
* Updated the design of AutoMap buttons in Map/Push pages
* Added a log entry when Refresh Data switches are set
* Added a setting to override tax mappings and sync order taxes to a line item
* Added a setting to choose whether to apply discounts as a separate line item or within the original line item
* Added a setting to choose how to display WooCommerce names in mapping pages


V 1.3.2

* Improved logging of scenarios where no updated inventory needed to be synced from QuickBooks into WooCommerce
* Improved customer/product automapping when characters are involved


V 1.3.1

* Resolved issue with new site activation errors


V 1.3.0

* When pushing multiple existing orders made by one guest, made sure that only one instance of the customer is in the queue at one time
* Added ability to search Push > Payments by Order #
* Disabled check boxes in Push > Order Page if order has already been pushed
* Improved the "Sync all orders to one Customer" option to segment by customer role
* Rebuilt the Connection process to replace mcrypt with openssl and add compatibility with PHP 7.2 (requires re-saving password)
* Improved compatibility with Inventory Assembly items
* Added global Class setting
* Added setting to set QuickBooks order date to equal the date synced into QuickBooks
* Improved Sales Order functionality to allow syncing payments into QuickBooks with Sales Orders
* Ensured Sales Receipts follow Payment Method mappings when syncing into QuickBooks
* Improved Variation automap by SKU to be compatible with multi-level products/parents in QuickBooks


V 1.2.2

* Improved tax setting to specially designate a shipping tax code
* Improved Avalara compatibility when syncing orders to QB as Sales Receipts
* Improved general Aelia compatibility to set multiple currency columns in Map > Payment Method


V 1.2.1

* Added functionality to unlink products/variations in Push pages
* Added tax setting in MyWorks Sync > Settings to specially designate a shipping tax code
* Improved pushing of payments that have no TXN ID
* Improved compatibility with Aelia Payment Plugin for orders with discounts
* Vastly improved handling of ghost mappings (fixed scenarios where customer/product is deleted in QuickBooks but mapping is not removed in our integration)
* Added error message to notify if database password includes non-supported characters


V 1.0.0

* First Launch


V 1.0.1

* Minor bug fixes


V 1.0.2

* Added RealTime Sync settings
* Added support for Guest order syncing
* Added Clear all Mapping button on dashboard
* Minor bug fixes


V 1.0.3 

* Added automap functionality for customers and products
* Added Company Info section in Connection tab
* Added setting to Sync all orders to one Customer
* Added Clear Log button
* Added Clear Mapping buttons for Customer / Product mapping pages
* Added Shipping/Coupon map pages


V 1.0.4

* Minor Bug Fixes


V 1.0.5

* Upgrade Bug Fixes
* fopen error resolution
* Fixed Default tab not showing initially when visiting MyWorks Sync > Settings


V 1.0.6

* Resolved minor bugs


V 1.0.7

* Added filter by stock status in Product Map page
* Resolved issue with payment methods not saving
* Resolved issue with Push buttons not functioning correctly
* Resolved issue with "Sync all orders to one QB customer" not functioning correctly


V 1.0.8

* Added option to sync order notes to invoice memo in QB Desktop
* Added Sync Status in Order & Mapping pages
* Added option to sync orders as sales receipt


V 1.0.9

* Added Push > Payment tab
* Improved Sales Receipt syncing
* Added links in Push > Orders to link directly to order
* Improved support for adding orders created directly in the admin to the queue in real time


V 1.0.10

* Added setting to store/display debug log for better troubleshooting
* Added "Unlink" option to Push > Orders to facilitate pushing an order again if need be
* Added metrics on Dashboard tab to show customers, products & accounts loaded into the plugin


V 1.0.11

* Resolved Sales Receipt syncing bugs
* Added number next to Queue menu to show amount of data in queue
* Added dropdown in Map/Push > Products to filter displayed products by category
* Added "Add to Queue" option in WooCommerce >Orders page


V 1.0.12

* Resolved issue with separate Billing/Shipping address not syncing properly
* Resolved minor bug issues


V 1.0.13

* Added new Sync Status design
* Improved Payment sync functionality


V 1.0.14

* Improved multi-line address syncing
* Added option in Miscellaneous to switch between UTF and ISO encoding


V 1.0.15

* Improved Payment Push page, and real-time payment syncing
* Added setting to adjust locales for improved Chinese character handling


V 1.0.16

* Improved pushing products into QB Desktop
* Improved/added Inventory level push for products to QB desktop


V 1.0.17

* Minor Bug Fixes


V 1.0.18

* Added setting to allow control of billing/address line formatting
* Minor Bug Fixes


V 1.0.19

* Updated QBXML version to 13.0
* Added better support for guest order syncing * to check first by email, then by name, for existing QB customer
* Added better support for changing/saving password in Connection tab
* Added option to append SKU to end of product description in QB Order
* Added option to remove product description field when syncing to QB 
* Improved Inventory sync for both realtime and push
* Improved Inventory Sync for 0 qty levels


V 1.0.20

* Added better database upgrade handling over automatic update
* Added dedicated Database Status tab
* Added option to set Default QBD Display Name
* Added option to add customer name to statement memo
* Adjusted Payment number to reflect Transaction ID in WooCommerce
* Resolved rare issue where payment would error out syncing to QB when automatically added to Queue


V 1.0.21

* Resolved issue where Settings > Automatic Sync was prohibiting customers from being added to the queue when pushing orders manually
* Added option to Use Company Billing Address for syncing/mapping customers 
* Added setting to limit automatic sync of orders based on order status (Completed/Processing being default)


V 1.0.22

* Added better support for non-Customer roles in WooCommerce


V 1.0.23

* Added a splash page in Map/Push pages to protect against accidentally loading customer pages with excessive records.


V 1.1.0

* Added setting to sync orders as Sales Order
* Added Pull section to manually pull inventory levels into WooCommerce
* Added setting in Automatic Sync to enable automatic inventory sync from QB to WooCommerce
* Added setting in Inventory to allow choice between time interval 
* Added setting to automatically clear logs after X Days
* Added time zone selection to localize inventory & logging times
* Improved discount handling with taxes to achieve correct order total
* Added compatibility to search Customer Map/Push by company name
* Added compatibility to search Push > Orders by order number
* Added compatibility to set QuickBooks Rep, Customer Type and Other field on global level


V 1.1.1

* Resolved minor issue with QWC connection returning Not Authenticated in some instances
* Added Variation switch in Settings > Automatic Sync to control syncing new variations to QB
* Improved new variation handling * to sync inventory/non-inventory product to QB when new variation is added
* Added First/Last Name to customer record billing/shipping address
* Improved support for syncing all orders to one QuickBooks customer * allowing exclusion of specific customer roles
* Adjusted order of shipping/discount line items to move shipping to the end
* Resolved issue with AJAX Search dropdowns not functioning correctly in some cases
* Added setting to append customer ID if the customer is a duplicate in Quickbooks
* Added ability to search Push>Orders by order number
* Improved Map > Customers page to show first/last name, company name, and email
* Changed Map > Tax to map tax rules to QuickBooks Sales Tax Item instead of Sales Tax Code
* Added support to sync payment terms set in Map > Payment Methods for Sales Orders
* Confirmed WooCommerce 3.3.0 Compatibility


V 1.1.2
* Resolved minor issue with the setting to use Billing Company to map/sync customers
* Added functionality to pull Pricing Levels from QuickBooks to WooCommerce