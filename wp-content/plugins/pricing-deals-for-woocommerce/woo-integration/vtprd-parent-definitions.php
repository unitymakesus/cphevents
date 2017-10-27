<?php
/*

*/


class VTPRD_Parent_Definitions {
	
	public function __construct(){
    
    define('VTPRD_PARENT_PLUGIN_NAME',                      'WooCommerce');
    define('VTPRD_EARLIEST_ALLOWED_PARENT_VERSION',         '2.0.14');  //all due to support for hook 'woocommerce_email_order_items_table' - requires the 2nd order_info variable...
    define('VTPRD_TESTED_UP_TO_PARENT_VERSION',             '2.1.9');
    //v1.1.5 begin ==>> replaced 'http//' with 'https//'
    define('VTPRD_DOCUMENTATION_PATH',                      'https://www.varktech.com/documentation/pricing-deals/introrule/');                                                                                                     //***
    define('VTPRD_INSTALLATION_INSTRUCTIONS_BY_PARENT',     'https://www.varktech.com/woocommerce/pricing-deals-for-woocommerce/?active_tab=instructions');
    define('VTPRD_PRO_INSTALLATION_INSTRUCTIONS_BY_PARENT', 'https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/?active_tab=instructions');
    define('VTPRD_PURCHASE_PRO_VERSION_BY_PARENT',          'https://www.varktech.com/woocommerce/woocommerce-dynamic-pricing-discounts-pro/');
    define('VTPRD_DOWNLOAD_FREE_VERSION_BY_PARENT',         'wordpress.org/extend/plugins/pricing-deals-for-woocommerce/');
    define('VTPRD_SUPPORT_URL',                             'https://www.varktech.com/support/'); //v1.1.5 NEW
    //v1.1.5 end
    
    //html default selector locations in checkout where error message will display before.
    define('VTPRD_CHECKOUT_PRODUCTS_SELECTOR_BY_PARENT',    '.shop_table');        // PRODUCTS TABLE on BOTH cart page and checkout page
    define('VTPRD_CHECKOUT_ADDRESS_SELECTOR_BY_PARENT',     '#customer_details');      //  address area on checkout page    default = on

    define('VTPRD_CHECKOUT_BUTTON_ERROR_MSG_DEFAULT',        
         __('Your email, bill-to or ship-to address are attached to other discounted order(s). This has affected the current order Lifetime discount limit, and resulted in the total Discount being reduced. Please hit the "Purchase" button a second time to complete the transaction.', 'vtprd')
    );
    
    global $vtprd_info, $vtprd_rule_type_framework, $wpdb;      

  
    define('VTPRD_PURCHASE_LOG',                          $wpdb->prefix.'vtprd_purchase_log');      
    define('VTPRD_PURCHASE_LOG_PRODUCT',                  $wpdb->prefix.'vtprd_purchase_log_product');   
    define('VTPRD_PURCHASE_LOG_PRODUCT_RULE',             $wpdb->prefix.'vtprd_purchase_log_product_rule'); 
    

    //option set during update rule process
    if (get_option('vtprd_ruleset_has_a_display_rule') == true) {
      $ruleset_has_a_display_rule = get_option('vtprd_ruleset_has_a_display_rule');
    } else {
      $ruleset_has_a_display_rule = 'no';
    }

    //v1.1.1.2 begin
    //option set during update rule process
    if (get_option('vtprd_ruleset_contains_auto_add_free_product') == true) {
      $ruleset_contains_auto_add_free_product = get_option('vtprd_ruleset_contains_auto_add_free_product');
    } else {
      $ruleset_contains_auto_add_free_product = 'no';
    }
    //v1.1.1.2 end
    
    $coupon_code_discount_deal_title  = __('deals', 'vtprd');
    $default_short_msg  =  __('Short checkout message required', 'vtprd');
    $default_full_msg   =  __('Get 10% off Laptops Today! (sample)', 'vtprd');
    $default_coupon_msg =  __('Optional - Discount applied *only* with Coupon Code', 'vtprd'); //v1.1.0.8

    $default_by_varname_example =  " <span class=\"varname_ex_larger\"> \"large | red + extra large | blue\" </span> &nbsp;&nbsp;&nbsp;&nbsp; ( Enter full attribute names: &nbsp; \"|\" separating values , &nbsp; \"+\" combining values )"; //v1.1.7.1a
    $default_by_varname_msg =  "Enter full attribute names: &nbsp; | &nbsp; separating values ,  &nbsp; + &nbsp; combining values"; //v1.1.7.1a
        //v1.1.7.1a warning used for edit comparison ONLY - NO HTML CHARACTERS
    $default_by_varname_msg_warning =  __('combining values', 'vtprd'); //v1.1.7.1a - used for comparison during update process
    
    $vtprd_info = array(                                                                    
      	'parent_plugin' => 'woo',
      	'parent_plugin_taxonomy' => 'product_cat',
        'parent_plugin_taxonomy_name' => 'Product Categories',
        'parent_plugin_cpt' => 'product',
        'applies_to_post_types' => 'product', //rule cat only needs to be registered to product, not rule as well...
        'rulecat_taxonomy' => 'vtprd_rule_category',
        'rulecat_taxonomy_name' => 'Pricing Deals Rules',
        'cart_discount_processing_save_unit_price' => 0,  //v1.0.7.4  used to store unit price if changed, by rule, to work with previous catalog discount
        
        //element set at filter entry time, to differentiate cart processing from price request/template tag processing
        'current_processing_request' => 'cart',  //'cart'(def) / 'display'
        
        //v1.0.7.4  If a coupon has been presented where individual_use is restricted, Our Coupon (cart discount) MAY NOT RUN
        'skip_cart_processing_due_to_coupon_individual_use' => false, //v1.0.7.4 

        'product_session_info' => '',
        /*
        array (
            'product_list_price'           => $vtprd_cart->cart_items[0]->db_unit_price_list,
            'product_list_price_html_woo'  => $db_unit_price_list_html_woo,
            'product_unit_price'           => $vtprd_cart->cart_items[0]->db_unit_price,
            'product_special_price'        => $vtprd_cart->cart_items[0]->db_unit_price_special,
            'product_discount_price'       => $vtprd_cart->cart_items[0]->discount_price,
            'product_discount_price_html_woo'  => 
                                              $discount_price_html_woo,
            
            //v1.0.7.4 begin
            'product_discount_price_incl_tax_woo'      =>
                                              $price_including_tax,
            'product_discount_price_excl_tax_woo'      =>
                                              $price_excluding_tax,
            'product_discount_price_incl_tax_html_woo'      =>
                                              $price_including_tax_html,
            'product_discount_price_excl_tax_html_woo'      =>
                                              $price_excluding_tax_html,                                              
            'product_discount_price_suffix_html_woo'   =>
                                              $price_display_suffix, 
            //v1.0.7.4 end
                                                        
            'product_is_on_special'        => $vtprd_cart->cart_items[0]->product_is_on_special,
            'product_yousave_total_amt'    => $vtprd_cart->cart_items[0]->yousave_total_amt,     
            'product_yousave_total_pct'    => $vtprd_cart->cart_items[0]->yousave_total_pct,    
            'product_rule_short_msg_array' => $short_msg_array,        
            'product_rule_full_msg_array'  => $full_msg_array,
            'product_has_variations'       => $product_variations_sw,
            'session_timestamp_in_seconds' => time(),
            'user_role'                    => vtprd_get_current_user_role(),
            'product_in_rule_allowing_display'  => $vtprd_cart->cart_items[0]->product_in_rule_allowing_display, //if not= 'yes', only msgs are returned 
            'show_yousave_one_some_msg'    => $show_yousave_one_some_msg, 
            //for later ajaxVariations pricing
            'this_is_a_parent_product_with_variations' => $vtprd_cart->cart_items[0]->this_is_a_parent_product_with_variations,            
            'pricing_by_rule_array'        => $vtprd_cart->cart_items[0]->pricing_by_rule_array,
            'product_id'                   => $product_id,    //v1.0.9.0  
            'product_has_addons'           => $product_has_addons    //v1.1.1                                      
          ) ;
         */
         'ruleset_has_a_display_rule'     => $ruleset_has_a_display_rule,
         'ruleset_contains_auto_add_free_product' => $ruleset_contains_auto_add_free_product,  //v1.1.1.2 
        
        //elements used in vtprd-apply-rules.php at the ruleset processing level
        //'at_least_one_rule_condition_satisfied' => 'no',
        'inPop_conditions_met' => 'no',
        'actionPop_conditions_met' => 'no',
        'maybe_auto_add_free_product_count' => 0,
        
        //computed discount total used in display
 //       'cart_discount_total'  => 0.00,
        'cart_rows_at_checkout_count' => 0,
        'after_checkout_cart_row_execution_count' => 0,
        'product_meta_key_includeOrExclude' => '_vtprd_includeOrExclude',
        /*
          array (
            'includeOrExclude_option'    => '',
            'includeOrExclude_checked_list'    => array( ) //this is the checked list...
          )
         */
		    'inpop_variation_checkbox_total' => 0,
        'on_checkout_page' => '', //are we on the checkout page?
        'coupon_num' => '',
        'checkout_validation_in_process' => 'no', //are we in checkout_form_validation?
        'ajax_test_value' => '',
        'coupon_code_discount_deal_title' => $coupon_code_discount_deal_title, 
        
        'cart_color_cnt' => '',
        'rule_id_list' => '',
        'line_cnt' => 0,
        'action_cnt'  => 0,
        'bold_the_error_amt_on_detail_line'  => 'no',
        'currPageURL'  => '',
        'woo_cart_url'  => '',
        'woo_checkout_url'  => '',
        'woo_pay_url'  => '',
    //    'woo_single_product_name'  => '',     //used in auto add function ONLY, if single product chosen for autoadd
    //    'woo_variation_name_list_by_id'  => '',     //used in auto add function ONLY
        /*
          array (     //KEYED to variation_id, from the original checkbox load...
            'variation_product_name_attributes'    => array( ) 
          )
         */                
        
        //elements used at the ruleset/product level 
        'purch_hist_product_row_id'  => '',              
        'purch_hist_product_price_total'  => '',      
        'purch_hist_product_qty_total'  => '',          
        'get_purchaser_info' => '',          
        'purch_hist_done' => '',
        'purchaser_ip_address'  => vtprd_get_ip_address(), //v1.0.7.4    >>> must be here!!, //v1.1.5 changed below
        'default_short_msg' => $default_short_msg,
        'default_full_msg'  => $default_full_msg,
        'user_is_tax_exempt'  => '',  //v1.0.9.0
        'product_catalog_price_array' => array (),
        'previous_auto_add_array' => '',  //v1.1.0.6  added - init to spaces so is_array test can be used
        'default_coupon_msg'  => $default_coupon_msg, //v1.1.0.8
        'coupon_codes_array' => array(),   //v1.1.0.9
        'cart_has_catalog_discounts' => false,   //v1.1.1  reset each time the cart is processed
        'default_by_varname_msg'  => $default_by_varname_msg, //v1.1.7.1a
        'default_by_varname_msg_warning'  => $default_by_varname_msg_warning, //v1.1.7.1a
        'default_by_varname_example'  => $default_by_varname_example //v1.1.7.1a
      ); //end vtprd_info      
      
    if ($vtprd_info['purchaser_ip_address'] <= ' ' ) {
      $vtprd_info['purchaser_ip_address'] = vtprd_get_ip_address();  //v1.1.5 changed below
    } 
 
    //load up 'user_is_tax_exempt'   //v1.0.9.0 
    //vtprd_get_current_user_role();   //v1.0.9.0 

                                                                                            
	}

	 
} //end class
$vtprd_parent_definitions = new VTPRD_Parent_Definitions;

  //v1.1.5 BEGIN 
  //NEEDS TO BE HERE

  function  vtprd_get_ip_address() {
    
    /* 
        //IF YOU MUST OVERRIDE THE IP ADDRESS ON A PERMANENT BASIS
        //USE SOMETHING LIKE https://www.site24x7.com/find-ip-address-of-web-site.html to find your website IP address (**NOT** your CLIENT ip address)
        //copy code begin
        add_filter('vtprd_override_with_supplied_ip_address', 'override_with_supplied_ip_address', 10 );        
        function override_with_supplied_ip_address() {  return 'YOUR IP ADDRESS HERE'; }
        //copy code end                
    */
    if (apply_filters('vtprd_override_with_supplied_ip_address',FALSE) ) {
      return apply_filters('vtprd_override_with_supplied_ip_address');
    }
    
    
    /*  // IP address license check can fail if you have copied your whole site with options table from one IP address to another
        // ==>>>>> only ever do this with a SINGLE RULE SCREEN ACCESS, 
        // then remove from your theme functions.php file ==>>>>> heavy server resource cost if executed constantly!!!!!!!
        //copy code begin
        add_filter('vtprd_force_new_ip_address', 'force_new_ip_address', 10 );        
        function force_new_ip_address() {  return 'yes'; } 
        //copy code end
    */
    if (apply_filters('vtprd_force_new_ip_address',FALSE) ) {
      $skip_this = true;
    } else {
      $vtprd_ip_address = get_option( 'vtprd_ip_address' );
      if ($vtprd_ip_address) {
        return $vtprd_ip_address;
      }    
    }

    
    //THIS ONLY OCCURS WHEN THE PLUGIN IS FIRST INSTALLED!
    // from http://stackoverflow.com/questions/4305604/get-ip-from-dns-without-using-gethostbyname
    
    //v1.1.6.3 refactored, put in test for php version
    $php_version = phpversion();
    if ( version_compare( $php_version, '5.3.1', '<' ) ) {
      $vtprd_ip_address = $_SERVER['SERVER_ADDR'];
    } else {    
      $host = gethostname();
      $query = `nslookup -timeout=$timeout -retry=1 $host`;
      if(preg_match('/\nAddress: (.*)\n/', $query, $matches)) {
        $vtprd_ip_address =  trim($matches[1]);
      } else {
        $vtprd_ip_address = gethostbyname($host);
      }    
    }	

    
    update_option( 'vtprd_ip_address', $vtprd_ip_address );
    
    return $vtprd_ip_address;

  }
  //v1.1.5 END
