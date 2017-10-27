=== Pricing Deals for WooCommerce ===
Contributors: vark
Donate link: https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/
Tags: woocommerce bulk pricing, woocommerce discounts, woocommerce dynamic discounts, woocommerce dynamic pricing, woocommerce wholesale pricing, woocommerce cart discount, bulk pricing, cart discount, category discount, customer role discount, user role discount, woocommerce prices, woocommerce pricing
Requires at least: 3.3
Tested up to: 4.8
Stable tag: 1.1.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Pricing Deals for Woocommerce - Dynamic Pricing, BOGO Deals, Bulk Discounts, Wholesale Discounts, Catalog discounts, Role-based pricing and more.  

== Description ==

A great method to offer discount pricing and marketing deals!  
Create a rule tailored to the deal you want. Pricing Deals for Woocommerce is a powerful discounting tool that helps you create both Dyanmic Cart Pricing discounts and Catalog Price (wholesale) discounts. 

= Fully tested with WooCommerce 3.2+ =

= OVERVIEW =

*   Bogo Deals (buy one get one) 
*   Category Pricing
*   Bulk Pricing
*   Catalog Pricing
*   Schedulable
*   Advertise the deal on your site (shortcodes )
*   Multilanguage support through [qTranslateX](https://wordpress.org/plugins/qtranslate-x/) 


= DISCOUNT TYPES =

*   Percent Discount
*   Fixed Amount Discounts  *(applied across a group or individually)*
*   Package Pricing
*   Discount Cheapest  *(or most expensive)*


= Pricing Deals FREE =  
*(deals apply to the whole store)*

*   Bulk Discounts
     - *Buy 5 get a discount, buy 10 get a larger discount*
     - *Buy $10 get a discount, buy $100 get a larger discount*
*   BOGO Deals (Buy one get one)
     - *Buy 1, get next 1 at a discount* 
*   Cart Deal activations by Woo Coupon
*   Catalog Pricing
     - *Show the discount directly in the catalog pricing display.* 
*   Marketing
     - *Theme Sales and Promotions Marketing by displaying the Rule message via shortcode (One Day Sale!)*
*   Show a Cart discounts directly in the Unit Price, or as an automatically-inserted Woo Coupon


= Pricing Deals PRO =  
*(Full Group Power!)*

*   Set up ANY deal by:
     - *Category*
     - *User Role*
     - *Product*
     - *Variation*
     - *Custom Pricing Deals Category*
*   Set Customer Limits:
     - *Example: "One per customer"*
*   Product-level Deal Participation selection
*   Retail / Wholesale Product Visibility and Salability control
*   Wholesale Tax Free/Buy tax free purchasing
*   Add a message next to all Catalog discounts

[GET Pricing Deals Pro](https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/)


= UNPARALLELED CUSTOMER SERVICE =

*   Customer Service is as important as the plugin functionality itself
*   [Support](https://www.varktech.com/support/) is open 7 days for questions and 1-on-1 assistance.
*   [Documentation](https://www.varktech.com/documentation/pricing-deals/introrule/)
*   [Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)


= Additional Plugins by VarkTech.com =
1. [Wholesale Pricing for WooCommerce](https://wordpress.org/plugins/wholesale-pricing-for-woocommerce) .. (skinnier pricing deals plugin)
1. [Cart Deals for WooCommerce](https://wordpress.org/plugins/cart-deals-for-woocommerce) ..  (skinnier pricing deals plugin)
1. [Minimum Purchase for WooCommerce](https://wordpress.org/plugins/minimum-purchase-for-woocommerce)
1. [Maximum Purchase for WooCommerce](https://wordpress.org/plugins/maximum-purchase-for-woocommerce) 
1. [Min or Max Purchase for WooCommerce](https://wordpress.org/plugins/min-or-max-purchase-for-woocommerce)   


= Minimum Requirements =

*   WooCommerce 2.0.14+
*   WordPress 3.3+
*   PHP 5+

== Install Instructions ==

1. Upload the folder `pricing-deals-for-woocommerce` to the `/wp-content/plugins/` directory of your site
1. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Where can I find plugin Documentation and Deal Examples? =
[Documentation](https://www.varktech.com/documentation/pricing-deals/introrule/)
[Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)

= How to set up a CART DEAL =
[Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)
Look for "10% Off all Laptop Purchases for Wholesale"

PRICING DEAL RULE SETUP:
Blueprint Section
Deal Applied in Catalog or Cart : Cart Purchase Discount
Deal Type : Just Discount the Items
Deal Action : Buy something, discount the item
Show Me : Basic
 
Buy Group Section
Buy Group Amount Type : Buy Each unit
Buy Group Filter : Category / Logged-in Role / Plugin Category
Buy Filter Product Categories : Laptops
Buy Filter And / Or : And
Buy Filter Logged-in Role : Wholesale Discount Role
 
Discount Section
Discount Amount Type : % Off
Discount Amount Count : 10
Checkout Message  (sample) : Wholesaler 10% Off Laptops
Sell the Deal Message  (sample) : Special Sale, 10% off All Laptops for Wholesaler! 


= How to set up BULK PRICING =
[Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)
Look for "Pricing Tiers   -   Buy 2-4, get 10% off, Buy 5-10 15% off"

BUY 2 or more GET 10% OFF

PRICING DEAL RULE SETUP:
Blueprint Section
Deal Applied in Catalog or Cart : Cart Purchase Discount
Deal Type : Buy one Get one (Bogo)
Deal Action : Buy Something, Discount *The* Item
Show Me : Advanced
 
Buy Group Section
Buy Group Amount Type : Buy Unit Qauntity
Buy Group Amount Count : 2
Buy Group Amount Applies To : All Products
Buy Group Filter : Category / Logged-in Role / Plugin Category
Buy Filter Product Categories : Books
Buy Rule Usage Count : Apply Rule Once per Cart
 
Get Group Section
Get Group Amount Type : Discount Unit Quantity
Get Group Amount Count : 1
Get Group Amount Applies To : All Products
Get Group Filter : Discount Group same as Buy Group
Get Group Repeat : Unlimited Group Repeats
 
Discount Section
Discount Amount Type : % Off
Discount Amount Count : 10
Discount Applies To : Each Product
Checkout Message  (sample) : Buy 2-4 Laptops, Get 10% off
Sell the Deal Message  (sample) : Buy 2-4 Laptops, Get 10% off 


= How to set up a BOGO DEAL =
[Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)
Look for "Buy a Laptop, get a Second Laptop $100 off"

PRICING DEAL RULE SETUP:
Blueprint Section
Deal Applied in Catalog or Cart : Cart Purchase Discount
Deal Type : Buy one Get one (Bogo)
Deal Action : Buy Something, Discount *Next* Item
Show Me : Basic
 
Buy Group Section
Buy Group Amount Type : Buy Each unit
Buy Group Filter : Category / Logged-in Role / Plugin Category
Buy Filter Product Categories : Laptops
 
Get Group Section
Get Group Amount Type : Discount Next One (single unit)
Get Group Filter : Discount Group same as Buy Group
 
Discount Section
Discount Amount Type : $ Off
Discount Amount Count : 100
Checkout Message  (sample) : 2nd Laptop 20% off
Sell the Deal Message  (sample) : One Day Sale, buy a Laptop, get a 2nd at 20% off! 

= How to set up a CATALOG DEAL =
[Deal Examples](https://www.varktech.com/documentation/pricing-deals/examples/)
Look for "10% Off Entire Catalog"

Blueprint Section
Deal Applied in Catalog or Cart : Catalog Price Reduction
Deal Type : Whole Catalog on Sale
Deal Action : Apply Discount to Catalog Item
Show Me : Basic
 
Buy Group Section
Buy Group Amount Type : Buy Each unit
Buy Group Filter : Any Product
 
Discount Section
Discount Amount Type : % Off
Discount Amount Count : 10
Checkout Message  (sample) : none
Sell the Deal Message  (sample) : Introductory Sale, 10% Off the Entire Store! 

= Where to get Pricing Deals PRO =
[GET Pricing Deals Pro](https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/)

== Screenshots ==

1. Pricing Deals - Add New Rule Basic Lower Area
2. Pricing Deals - Add New Rule Top Area
3. Pricing Deals - Cart Widget with Discount
4. Pricing Deals - Checkout with Discount



== Changelog ==

= 1.1.7.2 - 2017-10-10 =
* Enhancement - Prevent Pricing Deals and Pricing Deals Pro from background auto-updating.  These plugins must always
		These plugins must be updated *only* by and an admin click in response to an update nag ! 
* Enhancement - Show WOO sales badge if shop product discounted by CATALOG rule discount
          	//to TURN OFF this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file  
          	add_filter( 'vtprd_show_catalog_deal_sale_badge', function() { return  FALSE; } );  
* Fix - by Variation Name across Products now also applies to CATALOG rules
* Enhancement - Allow multiple coupons in coupon mode
          	//to TURN ON this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file
          	add_filter( 'vtprd_allow_multiple_coupons_in_coupon_mode', function() { return TRUE; } ); 
* Enhancement - By Role now tests against all roles user participates in (primary and secondary) 
* Fix - by Variation Name across Products in the Get Group now saving name correctly.
* Enhancement - New Filter to Allow full discount reporting on customer emails for Units discounting
          	//to TURN ON this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file 
          	add_filter( 'vtprd_always_show_email_discount_table', function() { return TRUE; } );
* Fix - VTPRD_PURCHASE_LOG definition changed, 2 columns now Longtext.
* Fix - Various Woocommerce 3.0 log warnings resolved. 
* Enhancement - Limit Cart Rule Discounting to a Single Rule or Rule type
          	new setting: - Pricing Deal Settings Page => 'Cart Cross-Rule Limits'
* Enhancement - New Filter vtprd_cumulativeRulePricing_custom_criteria.  Allows custom control of rule interaction.
          	Using this filter, create your own custom function to manage Rule interaction
          	(folow the example for using the 'vtprd_additional_inpop_include_criteria' in the PRO version apply-rules.php)


= 1.1.7.1 - 2017-05-26 =
* Enhancement - In the Group Product Filter, now select 
		by Variation Name across Products ! 
		Example: Apply a discount across all 'large' size shirts
* Enhancement - FOR Cart rule with 'Buy amount applies to' = EAch, and discount group same as buy group,
		process EACH product matching the choice criteria INDIVIDUALLY.
		  *NOTE* if 'Buy Group RULE USAGE COUNT' = apply rule once per cart, the rule will be applied
		  **once per product**
* Enhancement - Filter to allow page Refresh of the CART page after an AJAX update 
   		Valid Values for FILTER:
  		    CouponOnly - only send JS on Cart Page when an existing rule is actuated by a Coupon
  		    Never - never send the JS on Cart Page [DEFAULT] 
 		    Always - always on Cart Page      
            	//Be sure to clear the cache and start a fresh browser session when testing this...
		//to TURN ON this new action, add the 'add_filter...' statement to your theme/child-theme functions.php file
            	//Alternative: same solution with less code, no additional function:
          	add_filter( 'vtprd_js_trigger_cart_page_reload', function() { return  'Never'; } );  //valid values: 'CouponOnly' / 'Never' / 'Always'
* Fix - Remove warnings on coupon use
* Fix - IF auto add to cart granted and user logs in, correct auto added product count will be maintained.

= 1.1.7 - 2017-04-03 =
* Enhancement - Updates to accomodate Woocommerce 3.0
* Enhancement - New 'ex. VAT' filter - 'vtprd_replace_ex_vat_label'
* Change -  (due to change in Woocommerce 3.0)
    	    If you choose to show the Pricing Deals discount via an auto-inserted Coupon, and
    	    you want  translate/change the name of  the 'Deals' title of the auto-inserted "Coupon: Deals", 
		1. ADD the following wordpress filter:
      		// Sample filter execution ==>>  put into your theme's functions.php file (at the BOTTOM is best), so it's not affected by plugin updates
         	 function coupon_code_discount_title() {
           		 return 'different coupon title';  //new coupon title
          	}
          	add_filter('vtprd_coupon_code_discount_title', 'coupon_code_discount_title', 10); 

		**New**
		2. ALSO ADD a new Woocommerce Coupon in wp-admin/woocommerce/coupons
		  Required Coupon Attributes:
   			Coupon Code => coupon title from (1) above
   			Coupon Type => Fixed Cart Discount
   			Coupon Amount => 0

= 1.1.6.8 - 2016-08-08 =
* Fix - added warning about using Deal Type cheapest/most expensive, pointing instead to new "Apply to cheapest item first".
* Enhancement - Added "/stage" etc list to valid Pro license test names
* Enhancement - Function added to prevent Pricing Deals + Pro from automatically updating 
			(and possibly causing version mismatch issues)

= 1.1.6.7 - 2016-07-18 =
* Enhancement - Improved "Cheapest" Deals: 
		Cheapest option now in the Rule's Blueprint area - **please hover** to read the how-to.
* Enhancement - Pro plugin custom "update available" messaging now included on the plugins page, 
		just under the listing forPricing Deals Pro.
* Enhancement - Only check for Pro plugin update if required.

= 1.1.6.6 - 2016-07-09 =
SVN Update issue, final resolution!

= 1.1.6.5 - 2016-07-09 =
SVN Update issue

= 1.1.6.4 - 2016-07-09 =
SVN Update issue

= 1.1.6.3 - 2016-07-09 =
* Enhancement - Pro plugin updater now active - Pro plugin updates now delivered directly to the plugins.php page
* Fix - If Woocommerce deactivated, slide through with no warning
* Fix - Warn if PHP version less than 5.3.1
* Fix - repair rare Fatal error: Call to a member function get_tax_class() ...
* Fix - repair auto add for free bug

= 1.1.6.2 - 2016-06-19 =
* Fix - Auto update was accidentally forcing re-registration

= 1.1.6.1 - 2016-06-19 =
* Fix - URL fixes in anchors
* Fix - Registration fixes (pro only):
	 - fix to rego clock (pro activation)
	 - fix cron scheduling
	 - Localhost and IP warnings suspended
	 - Phone Home frequency reduced (pro activation check)
	 - document that Licensing and PHone Home functions are PRO-only, and run
		only if the PRO version is installed and active

= 1.1.6 - 2016-06-15 =
* Fix - Added warning for invalid Client website IP address
* Fix - delete 'Deals' coupon when not needed for plugin setting
* Fix - removed bloginfo from admin pages
* Fix - minor registration issues
* Enhancement - Now allow ANY staging.wpengine site as part of a test site registration

= 1.1.5 - 2016-06-05 =
* Fix - Added code to handle the rare "Fatal error: Call to a member function get_tax_class() ..."
* Enhancement - At store page refresh, catch any price changes and refigure discount
* Enhancement - Now with FREE full PRO demo available, 3-Day PRO licensing included.

= 1.1.1.3 - 2016-01-22 =
* Fix - Date range end date issue resolved
* Fix - Price Range of Variable Products with a partial Catalog Discount (where only some of the variations have discounts) resolved
* Enhancement - Now Compatible with WooCommerce Currency Switcher  (by realmag777).

= 1.1.1.2 - 2015-11-07 =
* Fix - Coupon discount mini-cart intermittent display issue on 1st time for auto adds
* Enhancement - Formerly, only a single "auto add for free" rule was allowed.
		Now multiple "auto add for free" rules is fully supported. 

= 1.1.1 - 2015-09-26 =
* Enhancement - Now Compatible with Woocommerce Measurement Price Calculator (Woocommerce + Skyverge). 
* Enhancement - Now Compatible with Woocommerce Product Addons (Woocommerce). 
* Enhancement - 'Cheapest in the cart' - see 'cheapest in cart filter' txt file in pro .
* Fix - Other rule discounts = no
* Fix - improve efficiency for Rule Discounts activated by Coupon
* Fix - variation discount pricing display
* Fix - shortcode in-the-loop product messaging
* Fix - discount and sale price scheduling
* Fix - fix for variation pricing for variation groups larger than 20, Catalog rules discount
* Fix - on Users screen
		Pricing Deals User Tax Free (box) User Transactions are Tax-Free
		- is now recognized by the system correctly
		- NB - if the switch is set on, then toggled off, to clear same browser sesion of the setting:
			- Pricing Deal Settings 'nuke session variables'
			- log out/log back in to Uswer
		
* Enhancement - New Filter to enable Pricing Deals to pick up pricing from other plugins 
    
		     // *** add to bottom of Theme Functions file (before the closing ? line, if there is one)
 		     //allows Pricing Deals to pick up current product pricing from other plugins
 		     //  ---  more resource intensive  ---
     
		    add_filter('vtprd_do_compatability_pricing', 'do_compatability_pricing', 10, 1); 
 		    function do_compatability_pricing ($return_status) {
 		     return true;
		    }
* Enhancement - Catalog Products Purchasability Display (pro):
		- ** Gives you the ability to control Product Purchasability
		- ** You can even turn your Woocommerce Store into a Catalog-only Installation!
		- Product screen now has a 'wholesale product' checkbox in the PUBLISH box
			- Label all wholesale products as wholesale
		- Settings Page now has "Catalog Products Purchasability Display"
			- Choose the Retail/Wholesale display option you want
		- Then as each Retail or Wholesale Capability user logs in, they will see
			- a tailored list (Not logged in = Retail)
* Enhancement - Wholesale Product Visibility (pro):
		- new option - Show All Products to Retail, Wholesale Products to Wholesale	
* Note - Now recommend "Members" plugin by Justin Tadlock, rather than User Role Editor

= 1.1.0.9 - 2015-08-12 = 
* Fix - Variation product discount pricing display due to woo 2.4 changes

= 1.1.0.8 - 2015-07-25 =
* Fix - Wp-admin Rule editing - if advanced field in error and basic rule showing, 
	switch to advanced rule in update process to expose errored field. 
* Fix - fix to user tax exempt status on User Screen - save to user updated, not user making the update!
* Enhancement - New Rule Option => Rule Discounts activated by Coupon
		- https://www.varktech.com/documentation/pricing-deals/introrule/#discount.discountcoupon
		- A Woocommerce Coupon code may be included on a Pricing Deals Rule 
		- if the rule has a Woocommerce coupon code included, that rule's discount will only be applied 
			once the same coupon code is redeemed in the cart.
		- May only be used in a Cart Rule.
		Directions:
		- Create a Woocommerce coupon => set to 'Cart Discount' and 'coupon amount' = 0.
		- In the Pricing Deals rule screen, select 'Advanced Rule' in the Blueprint Area
		- Coupon code (Coupon Title) may be entered in the Discount box area at "Discount Coupon Code"
		- With a Coupon code in the rule, the rule discount will only apply 
			when the matching Coupon Code is presented in the Cart


= 1.1.0.7 - 2015-07-21 =
* Fix - User screen tax exempt flag. 
* Fix - "Discount applied to list price, taken if it is less than sale price" now works with Catalog rules also. 
* Fix - buy_tax_free capability applied globally...
* Enhancement - Wholesale Product Visibility (pro):
		- https://www.varktech.com/documentation/pricing-deals/introrule/#rolesetup.productvisibility
		- Product screen now has a 'wholesale product' checkbox in the PUBLISH box
			- Label all wholesale products as wholesale
		- Settings Page now has "Wholesale Products Display Options"
			- Choose the Retail/Wholesale display option you want
		- Then as each Retail or Wholesale Capability user logs in, they will see
			- a tailored list (Not logged in = Retail)
		- NOTE when testing, use the Members plugin to control the new 'wholesale' capability!			
* Note - Now recommend Members plugin by Justin Tadlock, rather than User Role Editor

= 1.1.0.6 - 2015-07-07 =
* Fix - Auto add free item function. 
* Enhancement - Auto add free item function:
		- Can now add multiple free items using the Get Group Amount count.
		- New Filter ==> $0 Price shown as 'Free' unless overridden by filter:
			add_filter('vtprd_show_zero_price_as_free',FALSE); 
			(in your theme's functions.php file)

= 1.1.0.5 - 2015-05-22 =
* Fix - Older email clients dropping strikethrough, added css strikethrough
* Fix - Obscure PHP computation issue (floating point comparison)
* Enhancement - New Template Tag
		vtprd_the_discount() ==> Show the formatted total discount
		Template code: if ( vtprd_the_discount() ) { echo vtprd_the_discount();}
* Enhancement - Shortcode ==> pricing_deal_msgs_standard
		new functionality
		Sample template code:
      			$product_id = get_the_ID();
      			echo do_shortcode( '[pricing_deal_msgs_standard  
						force_in_the_loop="yes"  
						force_in_the_loop_product="'.$product_id.'"]');
* Enhancement - Cleanup if last rule deleted (admin/..rules-delete...)

= 1.1.0.4 - 2015-05-01 =
* Fix - Sale Price Discount exclusion switch issue resolved

= 1.1.0.3 - 2015-04-28 =
* Enhancement - Unit Price Discount subtotal crossouts now on Checkout and Thankyou pages,
	and also on Customer Email.

= 1.1.0.2 - 2015-04-25 =
* Fix - Woo Points and Rewards + regular coupons

= 1.1.0.1 - 2015-04-23 =
* Fix - Compatability issue with other Coupon-based plugins resolved,
	in particular Woo Points and Rewards
* Enhancement - New notification of mismatch between Free and Pro versions

= 1.1 - 2015-04-19 =
* Enhancement - In the Buy Group Filter, added Logged-in Role to Single product and single product with variations:
	By Single Product with Variations   (+ Logged-in Role) 
	By Single Product    (+ Logged-in Role)          

= 1.0.9.7 - 2015-04-19 =
* Fix - Catalog rule variation discounts (from-to) *crossout* value had a rare issue

= 1.0.9.6 - 2015-04-14 =
* Fix - Catalog rule variation discounts (from-to) changed to only show a single price, when all
	variation prices are the same.

= 1.0.9.5 - 2015-04-11 =
* Fix - Widget Catalog discount pricing for variations had an issue.
* Fix - Variation Catalog Discount pricing showing least-to-most expensive had an issue 
	when the actual variations were not in ascending sequence by price.  
	Now sorted for least/most expensive.
* Fix - Different decimal separator for Unit Price discount crossout value in cart/mini-cart.

= 1.0.9.4 - 2015-04-10 =
* Fix - Cart issue if only Catalog discount used.

= 1.0.9.3 - 2015-04-09 =
* Enhancement - Redux - Added **Settings Switches** to SHOW DISCOUNT AS:
		**UNIT COST DISCOUNT** or **COUPON DISCOUNT**
		- "Unit Cost Discount" discounts the unit price in the cart immediately
			- Old price crossed out, followed by discounted price is the default
			- can show discount computation for testing purposes
		- "Coupon Discount" places the discount in a Plugin-specific Woo coupon
		- "Unit Cost Discount" is the new default
		
* Enhancement - Added Settings Switch to show *Catalog Price discount suffix*, with wildcards.
		So you can represent "Save xx" 
		by putting in "Save {price_save_percent} or {price_save_amount}" 
		and the plugin will automatically fill in the saved percentage as "25%".

* Fix - For Catalog Rules, price crossout for variable products now fully controlled
		using Settings switch

= 1.0.9.2 - 2015-01-23 =
* Fix - Release Rollback - A small but significant number of clients continue to have
		issues with release v 1.0.9.0 and fix release 1.0.9.1 . Rather than
		leaving users with issues while a fix is being identified,  
		Release 1.0.9.2 rolls all the code back to v1.0.8.9, 
		prior to the code changes and the issues
		these customers are experiencing.

= 1.0.9.1 - 2015-01-23 =
* Fix - pricing issue - for some installations, no discounts made it to checkout. Fixed.

= 1.0.9.0 - 2015-01-22 =
* Enhancement - Added Settings Switch to SHOW DISCOUNT AS:
		**COUPON DISCOUNT** or 
		**UNIT COST DISCOUNT**
* Enhancement - Added Settings Switch to show *Catalog Price discount suffix*, with wildcards.
		So you can represent "Save xx" by putting in "Save {price_save_percent} {price_save_amount}" 
		and the plugin will automatically fill in the saved percentage as "25%".
 

= 1.0.8.9 - 2014-11-11 =
* Fix - pricing issue - doing_ajax
* Fix - pricing issue - login on the fly at checkout
* Fix - is_taxable Issue
* Fix - Product-level rule include/exclude list
* Enhancement - Shortcode Standard version now produces messages 'in the loop' only 
		when matching the product information 
* Enhancement - Shortcode Standard version now sorts msgs based on request
* Fix - 'excluding taxable' option in subtotal reporting.
* Fix - 'cheapest/most expensive' discount type sometimes would not remain selected - JS.
 

= 1.0.8.8 - 2014-10-19 =
* Enhancement - Added "Wholesale Tax Free" Role.  Added "buy_tax_free" Role Capability.
		Now **Any** User logged in with a role with the "buy_tax_free" Role Capability 
		will have 0 tax applied
		And the tax-free status will apply to the **Role**, regardless of whether a deal is currently active!!

    		**************************************** 
    		**Setup needed - Requires the addition of a  "Zero Rate Rates" tax class in the wp-admin back end 
    		*****************************************     
    		*(1) go to Woocommerce/Settings
    		*(2) Select (click on) the 'Tax' tab at the top of the page
    		*(3) You will then see, just below the tabs, the line     
    		    "Tax Options | Standard Rates | Reduced Rate Rates | Zero Rate Rates (or Exempt from Vat)" 
    		*(4) Select (click on) "Zero Rate Rates (or Exempt from Vat) " 
    		*(5) Then at the bottom left, click on 'insert row' .  
    		* Done.
    		* 
* Fix - Crossout original value in Catalog discount, in a rare situation


= 1.0.8.7 - 2014-09-04 =
* Fix - Rare Discount by each counting issue
* Fix - Onsale Switch for Catalog Rules

= 1.0.8.6 - 2014-08-16 =
* Fix - Rare variation categories list issue
* Enhancement - Variation Attributes

= 1.0.8.5 - 2014-08-13 =
* Enhancement - Coupon Title 'deals' translated via filter - see languages/translation directions.txt 
* Fix - Variation taxable status

= 1.0.8.4 - 2014-08-6 =
* Enhancement - Pick up User Login and apply to Cart realtime 
* Enhancement - Upgraded discount exclusion for pricing tiers, when "Discount Applies to ALL" 
* Enhancement - Pick up admin changes to Catalog rules realtime for all customers
* Fix - JS and/or initialization on Group

= 1.0.8.3 - 2014-08-3 =
* Fix - "Apply to All" rare issue 

= 1.0.8.2 - 2014-07-30 =
* Fix - Auto Insert free product name in discount reporting
* Fix - Fine-tune Admin resources

= 1.0.8.1 - 2014-07-27 =
* Fix - Refactored "Discount This" limits
	If 'Buy Something, Discount This Item' is selected,
	Get Group Amount is now *an absolute amount* of units/$$ applied to
	working with the Get Group Repeat amount 

= 1.0.8.0 - 2014-07-25 =
* Fix - Customer Limits
* Enhancement - Settings System Buttons

= 1.0.7.9 - 2014-07-21 =
* Enhancement - Custom Variation Usage
* Enhancement - Variation Reporting in receipts
* Enhancement - Woo Customer tax exempt

= 1.0.7.8 - 2014-07-15 =
* Fix - variation usage  ...

= 1.0.7.7 - 2014-07-03 =
* Fix - backwards compatability:: if pre woo 2.1 ...

= 1.0.7.6 - 2014-06-30 =
* Enhancement - Group Pricing math
* Enhancement - Percentage discount now defaults to 'all in group'
* Enhancement - Package Pricing now defaults to currency

= 1.0.7.5 - 2014-06-27 =
* Enhancement - backwards compatability
* Fix - mini-cart discount subtotal excluding tax
* Enhancement - rule schedule default - "on always"

= 1.0.7.4 - 2014-06-19 =
* Enhancement - use WC  coupon routine
* Enhancement - VAT pricing - include Woo wildcard in suffix text
* Enhancement - Taxation messaging as needed in checkout
* Enhancement - Auto add 'Wholesale Buyer' role on install
* Enhancement - Coupon Individual_use lockout
* Fix - PHP floating point rounding

= 1.0.7.3 - 2014-06-05 =
* Fix - post-purchase processing
* Fix - intermittent issue with variable product name 
* Fix - use_lifetime_max_limits defaults to 'yes'

= 1.0.7.2 - 2014-05-29 =
* Fix - Package Pricing in same group 
* Fix - Settings update repair
* Fix - update show help functions
* Fix - user role change in cart discount
* Fix - apply rule free catalog product issue repaired
* Fix - group pricing rounding issue

= 1.0.7.1 - 2014-5-23 =
* Enhancement - Admin improvements
* Fix - Include/Exclude box on Product wp-admin screen
* Fix - Cart Updated  woocommerce addressability issue

= 1.0.7 - 2014-5-14 =
* Fix - Include 'price display suffix' in Catalog pricing, as needed
* Enhancement - Pro version check from Free version

= 1.0.6 - 2014-5-10 =
* Enhancement - VAT pricing uses regular_price first, but if empty, looks at _price.

= 1.0.5 - 2014-5-08 =
* Fix -VAT inclusive for Cart pricing
* Fix -Warnings and move vtprd_debug_options to functions
* Enhancement - hook added for additional population logic
* Fix -$product_variations_list

= 1.0.4 - 2014-5-01 =
* Fix - if BCMATH not installed with PHP by host, replacement functions
* Fix - add in missing close comment above function in parent-cart-validation.php
* Fix - framework, removed (future) upcharge... , fix pricing-type-simple for catalog
* Fix - framework, pricing-type discount by catalog Option renamed
* Fix - js for cart simple discount was disallowing discount limits in error

= 1.0.3 - 2014-04-26 =
* Fix - warnings on apply
* Fix - cartWidget print option corrected
* Fix - Get group repeat logic
* Enhancement - e_notices made switchable, based on 'Test Debugging Mode Turned On' settings switch
* Enhancement - debugging_mode output to error log
* Change - cumulativeSalePricing switch default now = 'Yes - Apply Discount to Product Price, even if On Sale' - UI + JS chg

= 1.0.2 - 2014-04-14 =
* Fix - warnings on UI update error
* Enhancement - improved edit error msgs in UI
* Fix - Change to collation syntax on install
* Fix - shortcode role 'notLoggedIn'

= 1.0.1 - 2014-04-10 =
* Fix - warning on install in front end if no rule
* Fix - removed red notices to change host timezone on install
* Fix - removed deprecated WOO hook
* Fix - BOGO 'discount this' fix
* Fix - replace bcdiv with round
* Fix - BOGO missing method in free apply
* Enhancement - reformatted the rule screen, hover help now applies to Label, rather than data field 

= 1.0 - 2014-03-15 =
* Initial Public Release


== Upgrade Notice ==

= 1.1.7 - 2017-04-03 =
* Enhancement - Updates to accomodate Woocommerce 3.0
* Enhancement - New 'ex. VAT' filter - 'vtprd_replace_ex_vat_label'
* Change -  (due to change in Woocommerce 3.0)
    	    If you choose to show the Pricing Deals discount via an auto-inserted Coupon, and
    	    you want  translate/change the name of  the 'Deals' title of the auto-inserted "Coupon: Deals", 
		1. ADD the following wordpress filter:
      		// Sample filter execution ==>>  put into your theme's functions.php file (at the BOTTOM is best), so it's not affected by plugin updates
         	 function coupon_code_discount_title() {
           		 return 'different coupon title';  //new coupon title
          	}
          	add_filter('vtprd_coupon_code_discount_title', 'coupon_code_discount_title', 10); 

		**New**
		2. ALSO ADD a new Woocommerce Coupon in wp-admin/woocommerce/coupons
		  Required Coupon Attributes:
   			Coupon Code => coupon title from (1) above
   			Coupon Type => Fixed Cart Discount
   			Coupon Amount => 0

= 1.1.6 - 2016-06-15 =
* Fix - Added warning for invalid Client website IP address
* Fix - minor registration issues
* Enhancement - Now allow ANY staging.wpengine site as part of a test site registration

= 1.1.5 - 2016-06-05 =
* Fix - Added code to handle the rare "Fatal error: Call to a member function get_tax_class() ..."
* Enhancement - At store page refresh, catch any price changes and refigure discount
* Enhancement - Now with FREE PRO demo available, PRO licensing included.

= 1.1.1.2 - 2015-11-07 =
* Fix - Coupon discount mini-cart intermittent display issue on 1st time 
* Enhancement - Formerly, only a single "auto add for free" rule was allowed.
		Now multiple "auto add for free" rules is fully supported. 

= 1.1.1 - 2015-09-26 =
* Enhancement - Now Compatible with Woocommerce Measurement Price Calculator (Woocommerce + Skyverge). 
* Enhancement - Now Compatible with Woocommerce Product Addons (Woocommerce). 
* Enhancement - 'Cheapest in the cart' - see 'cheapest in cart filter' txt file in pro .
* Fix - Other rule discounts = no
* Fix - improve efficiency for Rule Discounts activated by Coupon
* Fix - variation discount pricing display
* Fix - shortcode in-the-loop product messaging
* Fix - discount and sale price scheduling
* Fix - fix for variation pricing for variation groups larger than 20, Catalog rules discount
* Enhancement - New Filter to enable Pricing Deals to pick up pricing from other plugins 
    
		     // *** add to bottom of Theme Functions file
 		     //allows Pricing Deals to pick up current product pricing from other plugins
 		     //  ---  more resource intensive  ---
     
		    add_filter('vtprd_do_compatability_pricing', 'do_compatability_pricing', 10, 1); 
 		    function do_compatability_pricing ($return_status) {
 		     return true;
		    }
* Enhancement - Catalog Products Purchasability Display (pro):
		- ** Gives you the ability to control Product Purchasability
		- ** You can even turn your Woocommerce Store into a Catalog-only Installation!
		- Product screen now has a 'wholesale product' checkbox in the PUBLISH box
			- Label all wholesale products as wholesale
		- Settings Page now has "Catalog Products Purchasability Display"
			- Choose the Retail/Wholesale display option you want
		- Then as each Retail or Wholesale Capability user logs in, they will see
			- a tailored list (Not logged in = Retail)
* Enhancement - Wholesale Product Visibility (pro):
		- new option - Show All Products to Retail, Wholesale Products to Wholesale	
* Note - Now recommend "Members" plugin by Justin Tadlock, rather than User Role Editor

= 1.1.0.9 - 2015-07-31 =
* Fix - Other rule discounts = no
* Fix - improve efficiency for Rule Discounts activated by Coupon

= 1.1.0.8 - 2015-07-25 =
* Fix - Wp-admin Rule editing - if advanced field in error and basic rule showing, 
	switch to advanced to expose errored field. 
* Fix - fix to user tax exempt status - saved to user updated, not user making the update!
* Enhancement - New Advanced Rule Option - Rule Discount applies only 
			when a specific Coupon Code is redeemed for the cart:
		- Coupon code is entered in the Pricing Deals Rule in the Discount box area (opotional!)
		- The rule discount will not activate in the Cart for a client purchase, 
			until the correct coupon code is presented.
		- Best to use a coupon set to 'Cart Discount' and 'coupon amount' = 0.

= 1.1.0.7 - 2015-07-21 =
* Fix - User screen tax exempt flag. 
* Fix - "Discount applied to list price, taken if it is less than sale price" now works with Catalog rules also. 
* Fix - buy_tax_free capability applied globally...
* Enhancement - Wholesale Product Visibility (pro):
		- Product screen now has a 'wholesale product' checkbox in the PUBLISH box
			- Label all wholesale products as wholesale
		- Settings Page now has "Wholesale Products Display Options"
			- Choose the Retail/Wholesale display option you want
		- Then as each Retail or Wholesale Capability user logs in, they will see
			- a tailored list (Not logged in = Retail)
		- NOTE when testing, use the Members plugin to control the new 'wholesale' capability!			
* Note - Now recommend Members plugin by Justin Tadlock, rather than User Role Editor

= 1.1.0.6 - 2015-07-07 =
* Fix - Auto add free item function. 
* Enhancement - Auto add free item function:
		- Can now add multiple free items using the Get Group Amount count.
		- New Filter ==> $0 Price shown as 'Free' unless overridden by filter:
			add_filter('vtprd_show_zero_price_as_free',FALSE); 
			(in your theme's functions.php file)

= 1.1.0.5 - 2015-05-22 =
* Fix - Older email clients dropping strikethrough, added css strikethrough
* Fix - Obscure PHP computation issue (floating point comparison)
* Enhancement - New Template Tag
		vtprd_the_discount() ==> Show the formatted total discount
		Template code: if ( vtprd_the_discount() ) { echo vtprd_the_discount();}
* Enhancement - Shortcode ==> pricing_deal_msgs_standard
		new functionality
		Sample template code:
      			$product_id = get_the_ID();
      			echo do_shortcode( '[pricing_deal_msgs_standard  
						force_in_the_loop="yes"  
						force_in_the_loop_product="'.$product_id.'"]');
* Enhancement - Cleanup if last rule deleted (admin/..rules-delete...)

= 1.1.0.4 - 2015-05-01 =
* Fix - Sale Price Discount exclusion switch issue resolved

= 1.1.0.3 - 2015-04-28 =
* Enhancement - Unit Price Discount subtotal crossouts now on Checkout and Thankyou pages,
	and also on Customer Email.

= 1.1.0.2 - 2015-04-25 =
* Fix - Woo Points and Rewards + regular coupons

= 1.1.0.1 - 2015-04-23 =
* Fix - Compatability issue with other Coupon-based plugins resolved,
	in particular Woo Points and Rewards
* Enhancement - New notification of mismatch between Free and Pro versions

= 1.1 - 2015-04-19 =
* Enhancement - In the Buy Group Filter, added Logged-in Role to Single product and single product with variations:
	By Single Product with Variations   (+ Logged-in Role) 
	By Single Product    (+ Logged-in Role)          

= 1.0.9.6 - 2015-04-14 =
* Fix - Catalog rule variation discounts (from-to) changed to only show a single price, when all
	variation prices are the same.

= 1.0.9.5 - 2015-04-11 =
* Fix - Widget Catalog discount pricing for variations had an issue.
* Fix - Variation Catalog Discount pricing showing least-to-most expensive had an issue 
	when the actual variations were not in ascending sequence by price.  
	Now sorted for least/most expensive.
* Fix - Different decimal separator for Unit Price discount crossout value in cart/mini-cart.

= 1.0.9.4 - 2015-04-10 =
* Fix - Cart issue if only Catalog discount used, now fixed.

= 1.0.9.3 - 2015-04-09 =
* Enhancement - Redux - Added **Settings Switches** to SHOW DISCOUNT AS:
		**UNIT COST DISCOUNT** or **COUPON DISCOUNT**
		- "Unit Cost Discount" discounts the unit price in the cart immediately
			- Old price crossed out, followed by discounted price is the default
			- can show discount computation for testing purposes
		- "Coupon Discount" places the discount in a Plugin-specific Woo coupon
		- "Unit Cost Discount" is the new default
		
* Enhancement - Added Settings Switch to show *Catalog Price discount suffix*, with wildcards.
		So you can represent "Save xx" 
		by putting in "Save {price_save_percent} or {price_save_amount}" 
		and the plugin will automatically fill in the saved percentage as "25%".

* Fix - For Catalog Rules, price crossout for variable products now fully controlled
		using Settings switch

= 1.0.9.2 - 2015-01-23 =
* Fix - Release Rollback - A small but significant number of clients continue to have
		issues with release v 1.0.9.0 and fix release 1.0.9.1 . Rather than
		leaving users with issues while a fix is being identified,  
		Release 1.0.9.2 rolls all the code back to v1.0.8.9, 
		prior to the code changes and the issues
		these customers are experiencing.

= 1.0.9.1 - 2015-01-23 =
* Fix - pricing issue - for some installations, no discounts made it to checkout. Fixed.

= 1.0.9.0 - 2015-01-22 =
* Enhancement - Added Settings Switch to SHOW DISCOUNT AS:
		**COUPON DISCOUNT** or 
		**UNIT COST DISCOUNT**
* Enhancement - Added Settings Switch to show *Catalog Price discount suffix*, with wildcards.
		So you can represent "Save xx" by putting in "Save {price_save_percent} {price_save_amount}" 
		and the plugin will automatically fill in the saved percentage as "25%".
	and the plugin will automatically fill in the saved percentage as "25%".
 
= 1.0.8.9 - 2014-11-11 =
* Fix - pricing issue - doing_ajax
* Fix - pricing issue - login on the fly at checkout
* Fix - is_taxable Issue
* Fix - Product-level rule include/exclude list
* Enhancement - Shortcode Standard version now produces messages 'in the loop' only 
		when matching the product information 
* Enhancement - Shortcode Standard version now sorts msgs based on request
* Fix - 'excluding taxable' option in subtotal reporting.
* Fix - 'cheapest/most expensive' discount type sometimes would not remain selected - JS.


= 1.0.8.8 - 2014-10-19 =
* Enhancement - Added "Wholesale Tax Free" Role.  Added "buy_tax_free" Role Capability.
		Now **Any** User logged in with a role with the "buy_tax_free" Role Capability 
		will have 0 tax applied
		And the tax-free status will apply to the **Role**, regardless of whether a deal is currently active!!

    		**************************************** 
    		**Setup needed - Requires the addition of a  "Zero Rate Rates" tax class in the wp-admin back end 
    		*****************************************     
    		*(1) go to Woocommerce/Settings
    		*(2) Select (click on) the 'Tax' tab at the top of the page
    		*(3) You will then see, just below the tabs, the line     
    		    "Tax Options | Standard Rates | Reduced Rate Rates | Zero Rate Rates (or Exempt from Vat)" 
    		*(4) Select (click on) "Zero Rate Rates (or Exempt from Vat) " 
    		*(5) Then at the bottom left, click on 'insert row' .  
    		* Done.
    		* 
* Fix - Crossout original value in Catalog discount, in a rare situation

= 1.0.8.7 - 2014-09-04 =
* Fix - Rare Discount by each counting issue
* Fix - Onsale Switch for Catalog Rules

= 1.0.8.6 - 2014-08-16 =
* Fix - Rare variation categories list issue
* Enhancement - Variation Attributes

= 1.0.8.5 - 2014-08-13 =
* Enhancement - Coupon Title 'deals' translated via filter - see languages/translation directions.txt 
* Fix - Variation taxable status

= 1.0.8.4 - 2014-08-6 =
* Enhancement - Pick up User Login and apply to Cart realtime 
* Enhancement - Upgraded discount exclusion for pricing tiers, when "Discount Applies to ALL" 
* Enhancement - Pick up admin changes to Catalog rules realtime for all customers
* Fix - JS and/or initialization on Group

= 1.0.8.3 - 2014-08-3 =
* Fix - "Apply to All" rare issue 

= 1.0.8.2 - 2014-07-30 =
* Fix - Auto Insert free product name in discount reporting
* Fix - Fine-tune Admin resources

= 1.0.8.1 - 2014-07-27 =
* Fix - Refactored "Discount This" limits
	If 'Buy Something, Discount This Item' is selected,
	Get Group Amount is now *an absolute amount* of units/$$ applied to
	working with the Get Group Repeat amount 

= 1.0.8.0 - 2014-07-25 =
* Fix - Customer Limits
* Enhancement - Settings System Buttons

= 1.0.7.9 - 2014-07-21 =
* Enhancement - Custom Variation Usage
* Enhancement - Variation Reporting in receipts
* Enhancement - Woo Customer tax exempt

= 1.0.7.8 - 2014-07-15 =
* Fix - variation usage  ...

= 1.0.7.7 - 2014-07-03 =
* Fix - backwards compatability:: if pre woo 2.1 ...

= 1.0.7.6 - 2014-06-30 =
* Enhancement - Group Pricing math
* Enhancement - Percentage discount now defaults to 'all in group'
* Enhancement - Package Pricing now defaults to currency

= 1.0.7.5 - 2014-06-27 =
* Enhancement - backwards compatability
* Fix - mini-cart discount subtotal excluding tax
* Enhancement - rule schedule default - "on always"

= 1.0.7.4 - 2014-06-19 =
* Enhancement - use WC  coupon routine
* Enhancement - VAT pricing - include Woo wildcard in suffix text
* Enhancement - Taxation messaging as needed in checkout
* Enhancement - Auto add 'Wholesale Buyer' role on install
* Enhancement - Coupon Individual_use lockout
* Fix - PHP floating point rounding

= 1.0.7.3 - 2014-06-05 =
* Fix - post-purchase processing
* Fix - intermittent issue with variable product name 
* Fix - use_lifetime_max_limits defaults to 'yes'

= 1.0.7.2 - 2014-05-27 =
* Fix - Package Pricing in same group 
* Fix - Settings update repair
* Fix - update show help functions
* Fix - user role change in cart discount
* Fix - apply rule free catalog product issue repaired

= 1.0.7.1 - 2014-5-23 =
* Fix - Include/Exclude box on Product wp-admin screen
* Fix - Cart Updated woocommerce addressability issue

= 1.0.7 - 2014-5-14 =
* Fix - Include price display suffix in Catalog pricing, as needed
* Enhancement - Pro version check from Free version

= 1.0.6 - 2014-5-10 =
* Fix -VAT pricing uses regular_price first, but if empty, looks at _price.

= 1.0.5 - 2014-5-08 =
* Fix -VAT inclusive for Cart pricing
* Fix -Warnings fix
* Enhancement - hook added for additional population logic
* Fix -$product_variations_list fix

= 1.0.4 - 2014-05-01 =
* Fix - if BCMATH not installed with PHP by host, replacement functions
* Fix - add in missing close comment above function in parent-cart-validation.php
* Fix - framework, removed (future) upcharge... , fix pricing-type-simple for catalog
* Fix - framework, pricing-type discount by catalog Option renamed
* Fix - js for cart simple discount was disallowing discount limiits in error

= 1.0.3 - 2014-04-26 =
* Fix - warnings on apply
* Fix - cartWidget print option corrected
* Fix - Get group repeat logic
* Enhancement - e_notices made switchable, based on 'Test Debugging Mode Turned On' settings switch
* Enhancement - debugging_mode output to error log
* Change - cumulativeSalePricing switch default now = 'Yes - Apply Discount to Product Price, even if On Sale' - UI + JS chg

= 1.0.2 - 2014-04-14 =
* Fix - warnings on UI update error
* Enhancement - improved edit error msgs in UI
* Fix - Change to collation syntax on install
* Fix - shortcode role 'notLoggedIn'

= 1.0.1 - 2014-04-10 =
* Fix - warning on install in front end if no rule
* Fix - removed red notices to change host timezone on install
* Fix - removed deprecated WOO hook
* Fix - BOGO 'discount this' fix
* Fix - replace bcdiv with round
* Fix - BOGO missing method in free apply
* Enhancement - reformatted the rule screen, hover help now applies to Label, rather than data field 

= 1.0 - 2014-03-15 =
* Initial Public Release