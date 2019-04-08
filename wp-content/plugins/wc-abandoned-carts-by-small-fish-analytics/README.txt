=== Abandoned Cart Reports For WooCommerce ===
Contributors: smallfishes
Tags: abandoned carts, abandoned cart report, cart abandonment, recover abandoned carts, woocommerce
Requires at least: 3.0.1
Tested up to: 5.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple plugin to see how many carts and which products your customers are abandoning

== Description ==

Discover how many abandoned carts your store has by recording when people abandon their carts and view trends over time using the built in dashboard and data pages.

= How The Plugin Works =

The plugin starts recording carts as soon as anyone adds an item to their cart. 

The plugin will do as much as possible to associate carts with users so you can see their email address for manual follow up and recovery. Even if the plugin can't associate the cart with an email address it will at least show the IP address of the cart on the data page.

Each cart starts in the 'In Progress' state on the data page. If a customer hasn't updated their cart for 15 minutes it will then show as 'Abandoned'. If the shopper comes back and later completes their cart it will change to 'Recovered' and be associated with the order.

= Where Do I Get Support? =

Email me or start a support request on Wordpress.org. I can be reached at mike@smallfishanalytics.com.

== Frequently Asked Questions ==
 
= How does the plugin associate carts with users? =

If a cart is started by a user that's logged into your store or logs in at any point after starting the cart the plugin will automatically associate the cart with the user account. On the data page you'll see the entry for the cart as well as the users name and email address.

If a cart is started by an unknown user the plugin will show the IP address of the associated with the cart. This can be useful in the case that you're using other software such as chat widgets because you can reference the IP address with the chat sessions.

If a cart is recovered by a user but that user doesn't create and account the plugin will still create an association with the order and show the name and email address on the cart data page.

= Will web crawlers create a bunch of fake carts? =

The plugin uses a library to filter out common web crawlers. In the case that the library doesn't capture the crawler the plugin also has code that is able to look for patterns that indicate a crawler starting a bunch of carts and filter those carts out.

If you think the plugin is filtering carts the wrong way reach out and we'd love to help out.

= What about support and features? =

We'd love to hear from you! We're currently looking at ways to better report on abandoned carts as well as starting to work towards the ability to recover carts both via email as well as integrating with other services such as Facebook, Twitter, etc.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to SFA Abandoned Cart under WooCommerce in the left hand menu of the WordPress dashboard to see your abandoned carts

== Screenshots ==
1. Abandoned Carts Dashboard 
2. Abandoned Carts Data 
3. Detailed Abandoned Cart Statistics
4. Top Recovered Products
5. Top Abandoned Products

== Changelog ==

= 2.5.1 =
* Declare support for WordPress 5
* Declare support for WooCommerce 3.5
* Fix a bug with money_format not being available on Windows hosts
* Added additional null check to fix a reported bug
* Updated crawler detection code

= 2.5.0 =
* Added a warning if there are too many carts being returned by the reports

= 2.4.1 =
* Declare support for WooCommerce version 3.4

= 2.4.0 =
* Stored cart totals on the cart line for better report performance
* Updated crawler detection code

= 2.3.0 =
* Remove extensions tab

= 2.2.1 =
* User Request: Changed permissions to allow WooCommerce store managers access to view the reports

= 2.2.0 =
* Removed the top abandoned and recovered carts tables from the main dashboard
* Renamed the data tab to carts
* Added a new products tab to show data on abandoned products
* Improved styling of the tabs
* Improved plugin security

= 2.1.11 =
* Added a field to the abandoned cart table to determine which carts to show on the funnel report.

= 2.1.10 =
* Code to track when carts view the checkout

= 2.1.9 =
* Fix for fields being disabled on the checkout. 

= 2.1.8 =
* Tested and declared support for WordPress 4.8
* Added code to track when users view the checkout fields in preparation for adding a funnel report extension
* Adjusted the styling a bit to give a white background on the report tabs

= 2.1.7 =
* Fixed a user reported notice where the date comparison for tables wasn't using the raw cart expiry time

= 2.1.6 =
* Refactoring much of the reporting code to prepare for new features.

= 2.1.5 =
* Quick bug fix for a user reported issue in the report area of the plugin. Thanks bdoga!

= 2.1.4 =
* Better support for showing meta data about products with variations
* Limit the top tables on the dashboard to only 10 items in each
* Add extensions tab to the plugin

= 2.1.3 =
* One more quick fix for a PHP debug notice a user reported.

= 2.1.2 =
* Quick fix for a PHP debug notice a user reported.

= 2.1.1 =
* Modify plugin install code to follow best practices.

= 2.1.0 =
* WooCommerce 3.0 is now supported with backwards compatibility.

= 2.0.3 =
* The plugin now associates carts with known users when they're logged in.
* The plugin now associates carts with completed orders so you can see who the recovered cart belonged too.
* Increased the number of rows on the data page up to 30.
* Added new columns on the data page to show the IP or customer name. Email fields are now links.

= 2.0.2=
* Fixed another bug where the wrong currency symbol was showing in the chart

= 2.0.1 =
* Fixed a bug where the wrong currency symbol was showing in the chart

= 2.0.0 =
* Released charts on the main page

= 1.5.0 =
* Moved the plugin from a top level menu under WooCommerce
* Added helpful links from the plugins page to get the report data and also email for support
* Changed the layout of the plugin to utilize tabs for the report and a premium / help page
* Updated crawler detection with latest code

= 1.4.2 = 
* Remove hard coded table name. Doh!

= 1.4.1 =
* Make sure default report view orders carts from newest to oldest

= 1.4 =
* Trying to better filter bots. If multiple carts from the same IP are added within 10 seconds the all carts for that IP are now flagged as spam
* Updated crawler detection with latest code

= 1.3 =
* IP address logging
* Better querying of data to exclude crawlers that make it passed the crawler detection code
* Added button to delete data from the report
* Updated crawler detection with latest code

= 1.2 =
* Cart report no longer shows $0.00 carts
* Fixed a minor CSS bug
* Refactored code into classes
* Updated crawler detection with latest code

= 1.1 =
* Added sortable grid columns to the report table
* Added total cart and amount counters to the report page
* Included taxes in the cart reports
* Removed text cruft from the report page and replaced with a simple "email us" link
* Updated crawler detection with latest code

= 1.0 =
* Initial Release


