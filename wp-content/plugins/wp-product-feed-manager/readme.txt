=== Woocommerce Google Feed Manager ===

Contributors: WPMarketingRobot, Michel Jongbloed, AukeJomm
Tags: Google Merchant Export, Product feed, woocommerce, Google product feed export, google, shopping, Google Adwords, Google Merchant, wooCommerce export, woocommerce variations, e-commerce, google merchant product feed, product variations, variations export, wp-e-commerce export, wp marketing robot
Requires at least: 5.4
Tested up to: 5.9
Requires PHP: 5.6
Stable tag: 1.39.0
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Extremely Powerful Woocommerce Google Feed Manager,  Optimize your product listings and campaign results and sell more on Google Shopping! 

== Description ==
Woocommerce Google Feed Manager is an extremely powerful and easy to use google shopping feed manager for Woocommerce web shops.
With Woocommerce Google Feed Manager you can easily add up to 100 products from your woocommerce store to a product feed setup that meets the requirements from Google Shopping. 

We have connected al the required and recommended fields of the feed with the Wordpress database. So after installation you're ready to submit your product feed to your Google merchant center. But you can go even further and tweak the content of every field in order to maximize your revenue from your products in Google shopping.

You have very advanced and professional options at your disposal to make your products stand out, use titles different from your shop, change your product google categories depending on title names. 

> Premium Woocommerce Google Feed manager

> If you love the plugin, or have more than 100 products in your woocommerce webshop we offer a full featured premium version.
> if you're a serious webshop owner and want to have absolute controle about over Google Shopping Feed you should start using our [Premium Woocommerce Google Feed Manager](https://goo.gl/mGFLMm)

> Sell your products on other selling channels

> If you want to get your products shown in Price comparison, product aggregators, affiliate networks and selling channels we have our [Premium Woocommerce Product Feed Manager](https://goo.gl/mGFLMm) supporting all shopping channels.
> Increase your Sales, Show your products on worldwide selling channels and affiliate networks!
> We have the most popular and important feed templates already installed like Bing, Pricegrabber, Amazon, Nextag, Beslist, Vergelijk.nl, Shopping.com, Connexity and more.

Everything you need to be successful!
You have full control over your Google feed, how your products are listed and what products you show and which products not.

* Simple: Creating Google shopping feeds is very simple with our plugin,
* Optimize: Tweak the feed in every possible way to improve performance,
* Feed Updates: Changes in your Woocommerce store are reflected in your feed instantly with the scheduled update option,

> Learn how to setup up your first basic feed in our online manual > [Product feed manager manual](https://goo.gl/pbgepQ)

== Installation ==
Step 1. Download the wp product feed manager plugin zip file from the Wordpress Plugin Repository.
Step 2. Upload 'wp-product-feed-manager' to the ’/wp-content/plugins/' directory
Step 3. Activate the plugin through the 'Plugins' menu in WordPress

That's basically it. You're ready to create your first feed and start making money.

Take a look at the video. They lead you through the proces in creating your first product feed.

= Install woocommerce google feed manager =
[youtube https://www.youtube.com/watch?v=bgEFUWAdNOc]

= How to setup a basic feed =
[youtube https://www.youtube.com/watch?v=VDcyo9ifdmk]

== Frequently Asked Questions ==
= Why should i go with your plugin? =
We are very proud of our woocommerce Google feed manager and we think it is the most powerful plugin out there build by Product Feed Marketeers especially for woocommerce powered shops. Our plugin will help you to get the most out of your Google shopping account.

The power of the plugin is that you can add the data from your shop to any feed attribute required, recommended or supported by Google. Next to that you are able to alter the data from you wordpress woocommerce database so you can optimize the data you send to Google. We support custom product fields and product variations making our plugin even stand out more from the competition.

While you grow with your webshop you can start working with our Premium plugin for even more powerful features and even more channel to sell your products on.

= How does the Google feed manager plugin work? =
Basically the plugin will generate a xml product feed valid for the Google product feed guidelines. It will take the data from your woocommerce powered online shop and create a valid Google product feed. The plugin will automatically update the feed to reflect the changes of your products so the xml feed will contain the correct product data. 

The only thing you have to do is add the xml feed url to your Merchant center and start selling in Google.

= Any Issues? Start here =
Start with the next steps when you have any issue with your installation and plugin.

First make sure you have all your plugins, theme and wordpress updated to the latest version. Follow woocommerce requirements as a minimum for the use of our plugin.

PHP 5.6 or greater
MySQL 5.6 or greater
WP Memory limit of 256 MB or higher for larger shops
If you have all of that sorted please follow the next steps before contacting us.

Deactivate and then activate the plugin. Test the issue.
If that does not resolve the issue, deactivate and reinstall the plugin over the current installation. Than activate it again and test the issue.
If it still does not work, remove the plugin through the plugins page (be aware, this will also delete all existing feeds) and reinstall it. Then test the issue again.

If the problem still exists, contact our support.

= Feed Warning, invalid xml feed =
There can be many reasons why a feed is invalid. We have build the feed in such a manner that it will only happen very sporadic.

In case it does please do submit your feed in your Google Merchant center and check what is going on in your feed in the Feed debugger from Google. It is very powerful and spot on.

[Read about it here](https://goo.gl/CChqGH)

== Screenshots ==
1. Add a new feed
2. Map a category
3. Save and generate your feed

== Changelog ==
= 1.39.0 - 14/03/2022 =
* Added some extra test code to prevent a fatal PHP error when the feed attribute data contains some invalid code.
* Changed the way the WooCommerce Google Product Review Feed Manager is initiated to prevent a PHP Fatal error in a specific setting.
* Tested on WooCommerce 6.2 and 6.3

= 1.38.0 - 23/01/2022 =
* Fixed an issue that could cause incorrect price values when using specific third party plugins. 
* Added the "Product Parent Description" source.
* Fixed an issue where in some situations the image url's would not load.
* Added support for the WooCommerce Currency Switcher plugin.
* Tested on WooCommerce 6.1.

= 1.37.0 - 20/12/2021 =
* Added the "Regular Price With Tax", "Regular Price Without Tax", "Sale Price With Tax" and "Sale Price Without Tax" sources.
* Fixed an issue that would cause a type error when opening the Edit Feed page.
* Tested on WooCommerce 6.0.

= 1.36.0 - 10/11/2021 =
* Fixed an issue where if there is a source with the name "null" it would be selected as WooCommerce source by default.
* Fixed an issue where selected categories in the Category Mapping table would automatically deselect if they had zero products assigned to them.
* Fixed an issue with the edit feed page if a user would save a feed with an attribute filter being not assigned.
* Tested on WooCommerce 5.9.

= 1.35.0 - 14/10/2021 =
* Fixed an issue where some attributes like Weight would not calculate correctly in a feed query.
* Updated the Google Feed Specifications.
* Tested on WooCommerce 5.8.

= 1.34.0 - 18/08/2021 =
* Fixed an issue when using more than one filter in a "change values" option.
* Fixed an issue that could leave a .0 in the result of a change values calculation.
* Fixed an issue that would cause a sale_price below 1 to disappear from the feed.
* Fixed an issue that prevented the buttons on the Edit Feed page to become selectable when editing a feed.
* Fixed an issue causing an incorrect outcome when using the Weight source in a filter with a decimal value.
* Tested on WooCommerce 5.6.

= 1.33.0 - 14/07/2021 =
* Added primary category support for the Rank Math SEO plugin.
* Tested on WooCommerce 5.5.

= 1.32.0 - 09/06/2021 =
* Added the "strip tags" and "limit characters" actions to the edit value options.
* Tested on WooCommerce 5.4.

= 1.31.1 - 18/05/2021 =
* Fixed an issue that caused a fatal PHP error when a feed with no selected Shop Categories is opened.

= 1.31.0 - 13/05/2021 =
* Fixed an issue that would add incorrect products to the feed in the case the feeds category selection contains categories that where removed from the shop.
* Added the "Variation Parent Id" and "Product Parent Id" sources.
* Added the "Highest Grouped Price" and "Lowest Grouped Price" sources to the list.
* Fixed an issue where the GTIN of a product variation would not save to the database.
* Tested on WooCommerce 5.3.0.

= 1.30.0 - 13/04/2021 =
* Tested on WooCommerce 5.2
* Password protected products are now excluded from the feed.
* Improved the stability of automatic feed updates.

= 1.29.2 - 26/03/2021 =
* Updated an issue that prevented Regular Prices from Variable Products to be included in the feeds.

= 1.29.1 - 20/03/2021 =
* Fixed an issue that prevented Regular Prices from Variable Products to be included in the feeds.

= 1.29.0 - 15/03/2021 =
* Tested on WooCommerce 5.1

= 1.28.1 - 09/03/2021 =
* Fixed a compatibility issue with the Enhanced Ecommerce Google Analytics Plugin.

= 1.28.0 - 10/02/2021 =
* Tested on WooCommerce 5.0
* Fixed an issue that could cause an error on the Edit Feed page.
* Changed the way the Image Library source is generated. It now only adds images and filters any other attachments.

= 1.27.0 - 11/01/2021 =
* Tested on WooCommerce 4.9

= 1.26.1 - 16/11/2020 =
* Made it possible to use the product_highlight attribute more than once in a feed (use || as a separator)

= 1.26.0 - 10/11/2020 =
* Improved the error messaging for JSON Parse errors.
* Tested on WooCommerce 4.7.
* Added the option to use Category numbers in stead of selectors.

= 1.25.0 - 13/10/2020 =
* Tested on WooCommerce 4.6.

= 1.24.0 - 24/08/2020 =
* Fixed an issue where using an apostrophe in the feed name would make the feed unreachable.
* Added the g: prefix again for the title and link attributes.
* Tested on WooCommerce 4.4.

= 1.23.0 - 20/07/2020 =
* Tested for WooCommerce 4.3.
* Made a few small code updates.
* Updated the Google attributes to the latest specifications.
* Fixed an issue where the shipping class of a product variation would not be placed in the feed.

= 1.22.0 - 03/06/2020 =
* Replace several deprecated functions.
* Fixed an issue that prevented the lower buttons on the Edit Feed page to show up when making a new feed.
* Changed the feed process such that it keeps money values of 0 out of the feed.
* Added a preset for the sale price in a Google feed to include the currency.
* Fixed an issue that could cause an undefined index notice.
* Tested for WooCommerce 4.2.

= 1.21.1 - 03/04/2020 =
* Restored the original definition of the url to the uploads folder because of issues with the new definition. Added a wppfm_corrected_uploads_url filter for specific use cases.

= 1.21.0 - 27/03/2020 =
* Fixed an issue where an incorrect money value would be calculated when the WC settings have a period as thousands separator and 0 decimals for money values
* Further improved the logger function 
* Fixed an issue where a feed would fail when a filter is used that filters out a large part of the selected products   
* Fixed an issue where a duplicate of a feed would not contain the Feed Filter data
* Fixed an issue that caused a duplicated feed to show a 404 page when clicking on the View Feed button
* Fixed an issue that would cause the link to the upload folder (and this to the feeds) be incorrect for a multi-site configuration.
* Fixed an issue that would allow a user to regenerate a feed from the Feed List page even when a feed is still processing, started from the Edit Feed page

= 1.20.1 - 20/03/2020 =
* Fixed a bug that would cause the Edit Feed page to not load for a feed with a filter on an edit values selection

= 1.20.0 - 11/03/2020 =
* Tested for WooCommerce 4.0
* Fixed a priority issue that puts the Add new feed button in front of the WordPress menu items
* Added the option to add Product Identifiers to WooCommerce products
* Fixed an compatibility issue with the DigitalOcean Spaces Sync plugin
* Added more loggings to the Feed Process Logger
* Improved the error handling of the Feed Process
* Fixed an issue that could case the Feed Process to slow down when multiple feed failed
* Fixed an issue where the attributes would not load on an existing custom xml feed
* Fixed an issue where the attachment url would not show up in the feed with a WPML translated non-variation object

= 1.19.1 - 30/01/2020 =
* Fixed an issue with the product filter selection
* Tested for WooCoomerce 3.9

= 1.19.0 - 18/01/2020 =
* Fixed the layout of the Edit Feed page to better cope with smaller screens
* Improved the loading speed of the Edit Feed page
* Smashed some small bugs

= 1.18.1 - 14/12/2019 =
* Fixed an issue that could cause a missing argument error
* Increased the time between feed status checks, this seems to increase the stability of the feed process

= 1.18.0 - 01/12/2019 =
* Killed some small bugs
* When switching between Auto update on and off, using the Auto-on and Auto-off actions in the Feed List the action text now changes accordingly
* Tested on WordPress 5.3
* Tested on WooCommerce 3.8

= 1.17.1 - 03/11/2019 =
* Fixed an issue on the Edit Feed page where on an attribute with a filter, after refreshing the page, would not show the option to add another filter
* Included a Feed Process Logging option
* Fixed the View Feed button to open the correct feed even after a first feed generation with a feed name that contains spaces or forbidden characters

= 1.16.0 - 04/10/2019 =
* Added a View Feed button to the Edit Feed page
* Removed the use of the glob function
* Added a filter that removes any link from a products description and short description. This filter can be bypassed by using the wppfm_leave_links_in_descriptions filter
* Tested with WooCommerce version 3.7
* Pushed the Add New Feed button on top of the page footer so it does not get blocked by the footer anymore

= 1.15.0 - 09/08/2019 =
* Improved the stability of the background mode.
* Improved a check that should prevent forbidden characters in a feed name.
* Fixed a coding issue that could cause a notice on the Feed List page.

= 1.14.1 - 02/07/2019 =
* Fixed a bug that could stop the feed process on a Fatal PHP Error.

= 1.14.0 - 21/06/2019 =
* Added the Regenerate action to the feed list
* Improved the error handling of a situation where the activation of a feed update in the background fails.
* Fixed an issue where a corrupted feed filter attribute would prevent the Edit Feed page to load.
* Fixed an issue where the static value source selectors would not show the correct "Fill with a static value" selection when WP was in a translated mode.
* Fixed an issue where the "Product Category String" source would not always produce the correct category string.

= 1.13.1 - 24/03/2019 =
* Fixed an issue where the Update Schedule values where not correctly read from the Feed Edit form

= 1.13.0 - 16/03/2019 =
* Restructured the code to better comply with the WordPress coding standards and cleaned up some code
* Fixed an issue where the feed process would fail when the product_type source was selected

= 1.12.3 - 08/02/2019 =
* Improved the security of the http requests

= 1.12.2 - 14/12/2018 =
* Restored the wppfm_feed_item_value filter name
* Improved the German translation
* Checked compatibility with Wordpress version 5.0

= 1.12.1 - 30/11/2018 =
* Fixed an issue where error loggings would not write correctly to the logging file
* Fixed an issue where the plugin would log a type error when using the Image Galery source in a filter setting
* Added a default setting (empty array) to the feed queue when first called
* Restored the wppfm_feed_item_value filter

= 1.12.0 - 03/11/2018 =
* Changed the way the xml header is send to the background process to prevent the Wordfence plugin from seeing it as a XSS vulnerability
* Added a notification option so users get an email if an automatic feed update failed
* Changed the defaul value of the "Auto feed fix" option to off
* Added the Tax Status and Tax Class items to the source selectors
* Fixed an issue that could delete important WP files when removing the plugin from the plugins page
* Tested compatibility with WooCommerce version 3.5

= 1.11.4 - 23/10/2018 =
* Improved the handling of product errors in a way so it does not stop the feed process but only keeps the product out of the feed
* Further improved an issue that could delete important WP files when removing the plugin from the plugins page

= 1.11.2 - 19/10/2018 =
* Fixed an issue that could delete important WP files when removing the plugin from the plugins page

= 1.11.1 - 16/10/2018 =
* Fixed a syntax error that caused the feed process to fail

= 1.11.0 - 08/10/2018 =
* Fixed an issue that caused an "Undefined offset" error message in DEBUG_MODE
* Added the underscore to the selectable Combined Fields separator
* Fixed an issue with the WooCommerce Brand attribute where it would register an Invalid argument supplied for foreach warning
* Changed the way xml values are handled so CDATA data is not escaped anymore
* Fixed an issue where changing the category of an existing feed would loose all attribute settings.
* Fixed an issue where adding static value to an attribute that contains a pipe character would screw up the attribute data
* Improved the feed status checking so it also shows a feed processing error when the feed stays at 0kb
* Changed the status text from OK and On hold, to Ready (manual) or Ready (auto), to make it clearer what each status means
* Added the Dutch language
* Added the German language
* Added the French language

= 1.10.1 - 16/09/2018 =
* Fixed an issue where during a manual feed update whilst staying on the Feed page would send multiple ajax calls that could slow down the server
* Fixed an issue with the auto feed update

= 1.10.0 - 21/08/2018 =
* Fixed an issue with the category mapping
* Added the Product Title Without Variable Attributes to the source selection list
* Added the Product Type to the source selection list
* Fixed an issue where a Third Party Attribute keyword could remove an attribute from the feed
* Fixed an issue where attributes used for variations would output its slug in stead of its value
* Increased the background processing batch time limit to 60 seconds and added the option to change this time in the Settings page
* Added the wppfm_category_selector_level filter and lowered the category selector level to prevent issues with shops that have a large number of categories
* Fixed the '$ is not a function' error the plugin showed in combination with some third party plugins

= 1.9.4 - 08/07/2018 =
* Added a "Remove from feed" option to be used as part of a source condition
* Fixed a bug that prevented the plugin from adding the Shipping Class of simple products to the feed
* Fixed an issue that would cause the javascripts not to load when parsing of the js was defered
* Added the double pipe character to the Combined Source fields - separator options

= 1.9.3 - 16/06/2018 =
* Fixed an issue that prevented the wppfm_selected_categories filter to work correctly
* Tested for WooCommerce version 3.4
* Fixed an issue where some attributes would not be activated for the feed and thus would not put data in the feed
* Cleaned up some code

= 1.9.1 - 21/05/2018 =
* Improved the way you can work with repeated fields in the feed
* Fixed an issue where the Image Library source would return http links whilst the site is a secure https site

= 1.9.0 - 12/05/2018 =
* Fixed an issue where the Clear feed process button would not clear the correct data tables in an multi-site environment
* Fixed a bug where the category in the feed would only show the main category when also a subcategory was selected through the Category Mapping
* Added a warning message when WooCommerce version older than 3.0.0 is installed
* Fixed an issue that caused Product Variations not to recalculate when WPML is active
* Improved the user friendliness of the Category Mapping
* Fixed an error that could cause the attribute mapping failing to load
* Added the wppfm_selected_categories filter that allows you to alter the selected categories of a feed

= 1.8.3 - 31/03/2018 =
* Added the wppfm_feed_ids_in_queue filter
* Fixed an issue  that prevented the Third Party Attributes setting to be ignored in a backup
* Extended the Disable background process selection so that it also autmatically clears the feed process when selected
* Added an extra option on the settings page to switch the background processing off in case the feeds get stuck in the processing

= 1.8.1 - 16/02/2018 =
* Fixed an issue where when the user had not selected the "include variations" option, the non product specific variation data like min_variation_price or max_variation_price would not be included on the main version of the variation product.

= 1.8.0 - 04/02/2018 =
* Changed the feed processing proces so it can handle feeds with large number of products
* Fixed an issue where third party attributes that start with an underscore would show up as an empty row in the Google Source pulldown list
* Changed the way third party attributes are shown in the source list. They now keep their origional name
* Fixed a few security issues
* Added the WooCommerce version check
* Improved the auto feed update timing
* Added the wppfm_category_mapping_exclude, wppfm_category_mapping_exclude_tree and wppfm_category_mapping_max_categories filters that allow the user to influence the category mapping list
* Fixed an issue where the Stock count would show a wrong number when the actual Stock account was 0

= 1.7.4 - 21/10/2017 =
* Made some improvements in the script loading process
* Fixed a bug in the automatic feed update time calculation
* Made the plugin accessable for the Shop Manager role
* Made a few fixes to prevent error messages
* Redused the varchar size in the tables to max 255 to prevent issues with certain database collation settings
* Made some improvements to the memory usage during feed generation
* Fixed a bug where an attribute value of "0" would not be placed in the feed

= 1.7.3 - 03/09/2017 =
* Fixed an issue where filtering failed when the user would not enter an "or" input
* Improved the backup process
* Fixed an issue with the duplicate function
* Improved the database setup and update process

= 1.7.1 =
* Added support for WooCommerce Composite Products

= 1.7.0 - 17/08/2017 =
* Added the wppfm_feed_item_value filter that allows users to edit the value of any item in a feed using this filter option
* Added support for Google Dynamic Remarketing
* Fixed an issue where the plugin would conflict with the Mandrill plugin
* The output lists are now sorted alphabetically
* Added support for user made taxonomies
* Added a function that removes WordPress Gallery shortcode from the product description
* Added a warning if a user uses prohibited characters in the feed name
* Fixed an error that could cause calculations in a change value to produce a period as a decimal separator even though a comma is set as required decimal separator
* Changed the Min and Max Variation prices that are not supported by WooCommerce anymore
* Prepaired the code to support the WooCommerce Product Feed Manager WPML Support plugin that adds WPML multilingual support the the plugin

= 1.6.1 - 16/06/2017 =
* Fixed an issue where an update of the database to the new specifications would only occur when visiting the feed update form so if an automatic feed update would be done before visiting the feed update form an error would occur as the database was not updated

= 1.6.0 - 11/06/2017 =
* Changed the way the javascript version numbers are build to prevent cashing issues when switching from a free version to a premium version
* Added the option to change the feed title and description of the feed file
* When an auto feed update is set to once every day the feed will now auto update every day on the set update time independant from manual updates
* Fixed an issue that prevented the "select all" checkbox in the Category Mapping table to correctly select or deselect all Shop Categories
* Added the option to only save (changes to) a feeds data, without (re)generating the feed
* Added the option to use third party attributes by setting attribute keywords in the Settings page

= 1.5.1 - 07/05/2017 =
* Fixed an issue where edit value calculations would not give the correct results with value above 1000 and a comma as the thousands separator
* Fixed an issue where External/Affiliate and Grouped products were excluded from the feed
* Fixed an issue that caused some attributes not to show up in the feed

= 1.5.0 - 23/04/2017 =
* Fixed an issue where pretty permalinks would nog show up in automatic feed updates
* Automatic feed updates are now performed at WordPress time reference instead of server time reference
* Added the option to automatically update a feed more than once a day
* Fixed an issue where the feed generation would fail and kept showing the 'still working' symbol
* Improved the way database update version numbers are stored
* Fixed an issue where the category would not stay at the correct level after generating a feed
* Fixed an issue where users who use an older version of PHP got several error messages
* Fixed an issue with combining a static field with a source that has an array output like the Image Library
* Moved the Settings page from the WP-Admin Settings to the Feed Manager menu
* Rearranged the Feed Manager Menu structure

= 1.4.3 - 03/02/2017 =
* Fixed a bug where using 'Image Library' as a source caused the feed generation to fail

= 1.4.2 - 15/01/2017 =
* Added the option to duplicate an existing feed
* Fixed an issue where the "edit values" option could sometimes not be opened in a Chrome browser
* Sorted the WooCommerce source pull-down fields alphabetically
* Added a "Select All" selector to the feed Category Mapping table
* Fixed an issue with a combined source including static fields and a filter
* Fixed the sale price dates to output the dates in the correct format

= 1.3.1 - 02/12/2016 =
* Fixed an issue that caused an error when calculations where done on a combined input field
* Fixed a code error that caused the plugin not to activate on PHP versions 5.3 or lower
* Added a Last Feed Update source that represents the feed update date and time
* Made some changed to the auto feed update that should improve the update process
* Changed the Edit Feed Page so the user cannot change the channel after the feed has been initialized
* Changed the Edit Feed Page so the user can change the Target Country and Default Category during and after the feed has been stored
* Updated the Google feed specifications to the October 2016 rules
* Changed the code to force feed file names not to have spaces

= 1.2.1 - 16/09/2016 =
* Added the Shipping Class source
* Fixed an issue with numeric condition values with non-english/us values
* Added access to another third party attribute set
* Fixed an issue with html special characters in the feed
* Added the item_group_id source to the source list
* Fixed a bug with combined fields in the not required level
* Changed the way the input fields are shown when making a new feed, thus preventing errors from not using the correct order to fill in the fields
* You can now change the name of an existing feed
* Fixed an issue that caused the advised source of the Shipping source to go to undefined in specific situations
* Added a "Convert to child-element" option to the edit values. This allows you to add a child element with a specific key to an xml feed

= 1.1.0 - 08/08/2016 =
* Due to recurring issues with support folder permissions for some users, moved the support folders from the plugins folder to the uploads folder. Existing feed files will retain their old url, only new feeds will be stored in the new support folder
* Changed the file writing procedures to minimize the times the system asks to enter ftp credentials

= 1.0.1 - 27/07/2016 =
* Added the option to include product variations in the feed (Premium versions only)
* Fixed the change values option in such a way that you now can perform recalculations even on combined source fields
* Fixed a bug that prevented the correct recalculation of comma separated financial values
* Several small changes in the styling code
* Fixed the Add Channels functionality as some firewalls prevented downloading a channel (Premium versions only)

= 0.19.0 - 18/06/2016 =
* Fixed a bug that caused a critical error with users that had PHP 5.5 or older
* Fixed a bug in the selection of recommended and optional output fields
* Expanded the error logging of the channel download process of the Premium versions
* Fixed a bug that prevented the correct output when working with conditions on feed items that have an advised source
* Fixed a bug that slowed down the wp-admin pages in the Premium versions
* Updated the auto-feed update

= 0.18.0 =
* Added the option to filter specific products from a feed (Premium versions only. Goto wpmarketingrobot.com for more information)
* Price values are now formatted according to the Woocommerce settings in the Currency Options, except the currency as this can be added manually and not all channels allow a currency in their feed
* When building a new Feed for Google the price is now preset to the current price followed by a space and the Woocommerce Currency
* Fixed a bug preventing the auto-feed update to work

= 0.17.0 =
* Now fixed the Product Category String in such a way that it shows the correct category string even when only a subcategory has been selected for a product
* Price results are now always printed in a money format with two digits after the comma, even if the product has no digit in its price
* Fixed a compatibility issue with jQuery that prevented the Add New Feed form from proceding after a channel was chosen
* Fixed an issue with the Combined Source Fields option where adding a static value didn't always work
* Added the Woocommerce Currency option that can be used in conjunction with the price outputs (use Combined Source Fields)

= 0.16.0 =
* Fixed a bug in the auto-feed-update function
* Fixed a bug that caused an error when installing a paid version over the free version, causing a white screen and error message
* Fixed a bug in the calculation options of the change value selections
* Changed the way that the support folders are made by temporary removing the umask modifier to force the server to make these folder writable
* Changed the way the change value selections are shown
* Again changed the procedures to get the Product Category String and Selected Product Categories because the last procedure still did not work in all situations

= 0.15.0 =
* Added the option to use Product Tags as source
* Added the Custom Fields from the Posts and the Products as selectable sources
* Changed the procedure to get the Product Category String and Selected Product Categories because the old procedure did not work in all situations
* Fixed a bug where the user could not make a new feed if there is no Custom Attribute defined
* The plugin now works correctly on websites using the https protocol

= 0.14.0 =
* Changed the Product Categories source and split it in a Product Category String source that contains a string representing the category in which the product is stored in the actual shop, and a Selected Product Categories source that will give you a comma separated string with all the shop Categories that where selected for this product. 
* Daarnaast de Image Library source toegevoegd waarmee additionele images aan de feed kunnen worden gekoppeld. 
* fixed a bug.

= 0.13.1 =
* Changed the product name reference for the login process.

= 0.13.0 =
* Added version numbers to the loaded javascript files to make sure the plugin is always using the latest version of the script. 
* Also repaired a few minor bugs.

= 0.12.0 =
* Added 'Min Variation Price', 'Min Variation Regular Price', 'Min Variation Sale Price', 'Max Variation Price', 'Max Variation Regular Price', 'Max Variation Sale Price' and the 'Post Category' as additional sources. 
* Repaired a few minor bugs.

= 0.11.0 =
* Improved the javascript conflict handling

= 0.10.0 =
* Changed the Woocommerce Source pulldown options to match the Woocommerce Products inputs and fixed a few bugs

= 0.9.0 =
* The javascript files are now disabled when the user leaves the plugin pages
* fixed a few small bugs

= 0.8.0 =
* Made the feed production faster

= 0.7.0 =
* Removed all html code from the feed output

= 0.6.0 =
* Changed the processing of the url and image sources

= 0.5.0 =
* Added a preset for the Availability tag for a Google feed and AvantLink feed

= 0.4.3 =
* Fixed a bug that prevented some categories to produce a feed

= 0.4.2 =
* Fixed a bug that prevented products to be added to the feed under specific situations

= 0.4.1 =
* Repaired an include error

= 0.4.0 =
* Added error loggin to a error.log file. 
* Improved the uninstall actions 
* fixed a few bugs

= 0.3.0 =
* Optimized the form layout to better fit in smaller screen resolutions and fixed a few bugs

= 0.2.0 =
* Removed the starting underscore sign from some sources in the source selectors and fixed a bug

= 0.1.1 =
* Removed some traces of an automatic updater in order to get accepted as WordPress plugin

= 0.1.0 =
* First active version, first launch

== Upgrade Notice ==
Please do update the google feed manager to the latest version. Each update makes it sure you feed is following Google Feed rules so your product feed will be validated correct in Google Merchant center.