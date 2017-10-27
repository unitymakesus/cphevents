<?php
/*
Plugin Name: VarkTech Pricing Deals for WooCommerce
Plugin URI: http://varktech.com
Description: An e-commerce add-on for WooCommerce, supplying Pricing Deals functionality.
Version: 1.1.7.2
Author: Vark
Author URI: http://varktech.com
WC requires at least: 2.4.0
WC tested up to: 3.2.0
*/

/*  ******************* *******************
=====================
ASK YOUR HOST TO TURN OFF magic_quotes_gpc !!!!!
=====================
******************* ******************* */


/*
** define Globals 
*/
   $vtprd_info;  //initialized in VTPRD_Parent_Definitions
   $vtprd_rules_set;
   $vtprd_rule;
   $vtprd_cart;
   $vtprd_cart_item;
   $vtprd_setup_options;
   
   $vtprd_rule_display_framework;
   $vtprd_rule_type_framework; 
   $vtprd_deal_structure_framework;
   $vtprd_deal_screen_framework;
   $vtprd_deal_edits_framework;
   $vtprd_template_structures_framework;
   
   $vtprd_license_options; //v1.1.5
   $vark_args; //v1.1.5
   
   //initial setup only, overriden later in function vtprd_debug_options
   
 error_reporting(E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR); //v1.0.7.7
  
     
class VTPRD_Controller{
	
	public function __construct(){    

    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    } 

		define('VTPRD_VERSION',                               '1.1.7.2');
    define('VTPRD_MINIMUM_PRO_VERSION',                   '1.1.7.2'); 
    define('VTPRD_LAST_UPDATE_DATE',                      '2017-10-10');
    define('VTPRD_DIRNAME',                               ( dirname( __FILE__ ) ));
    define('VTPRD_URL',                                   plugins_url( '', __FILE__ ) );
    define('VTPRD_EARLIEST_ALLOWED_WP_VERSION',           '3.3');   //To pick up wp_get_object_terms fix, which is required for vtprd-parent-functions.php
    define('VTPRD_EARLIEST_ALLOWED_PHP_VERSION',          '5');
    define('VTPRD_PLUGIN_SLUG',                           plugin_basename(__FILE__));
    define('VTPRD_PLUGIN_NAME',                          'Varktech Pricing Deals for WooCommerce');    //v1.1.5
    define('VTPRD_PRO_PLUGIN_FOLDER',                    'pricing-deals-pro-for-woocommerce');    //v1.1.5
    define('VTPRD_PRO_PLUGIN_FILE',                      'vt-pricing-deals-pro.php');    //v1.1.5    
    
    define('VTPRD_PRO_PLUGIN_NAME',                      'Varktech Pricing Deals PRO for WooCommerce');    //v1.0.7.1

    define('VTPRD_ADMIN_CSS_FILE_VERSION',                'v003a'); //v1.1.7.1a new version  ==> use to FORCE pickup of new CSS
    define('VTPRD_ADMIN_JS_FILE_VERSION',                 'v003a'); //v1.1.7.1a new version  ==> use to FORCE pickup of new JS
    
    define('VTPRD_WP_MINIMUM_VERSION_FOR_COMPARISON',     '4.5');    //v1.1.6
   
    require_once ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-definitions.php');
            
    // overhead stuff
    add_action('init', array( &$this, 'vtprd_controller_init' ));
    add_action( 'admin_init', array( &$this, 'vtprd_admin_init_overhead') ); //v1.1.5

    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  these control the rules ui, add/save/trash/modify/delete
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
    //  One of these will pick up the NEW post, both the Rule custom post, and the PRODUCT
    //    picks up ONLY the 1st publish, save_post works thereafter...   
    //      (could possibly conflate all the publish/save actions (4) into the publish_post action...)
    /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */    
    if (is_admin()) {   //v1.0.7.2   only add during is_admin
        add_action( 'draft_to_publish',       array( &$this, 'vtprd_admin_update_rule_cntl' )); 
        add_action( 'auto-draft_to_publish',  array( &$this, 'vtprd_admin_update_rule_cntl' ));
        add_action( 'new_to_publish',         array( &$this, 'vtprd_admin_update_rule_cntl' )); 			
        add_action( 'pending_to_publish',     array( &$this, 'vtprd_admin_update_rule_cntl' ));
        
        //standard mod/del/trash/untrash
        add_action('save_post',     array( &$this, 'vtprd_admin_update_rule_cntl' ));
        add_action('delete_post',   array( &$this, 'vtprd_admin_delete_rule' ));    
        add_action('trash_post',    array( &$this, 'vtprd_admin_trash_rule' ));
        add_action('untrash_post',  array( &$this, 'vtprd_admin_untrash_rule' ));
        /*  =============+++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
        
        //get rid of bulk actions on the edit list screen, which aren't compatible with this plugin's actions...
        add_action('bulk_actions-edit-vtprd-rule', array($this, 'vtprd_custom_bulk_actions') );
        
        //v1.1.5 plugin mismatch moved here...
        add_action( 'admin_notices', array( &$this, 'vtprd_maybe_plugin_mismatch' ) ); //v1.1.0.1
        add_action( 'admin_notices', array( &$this, 'vtprd_maybe_system_requirements') ); //v1.1.5    
                  
        //v1.1.7.2  ONLY do this when editing a page!
        add_action( 'load-post.php', array( &$this, 'vtprd_admin_process' ) ); //v1.1.7.2
        add_action( 'load-post-new.php', array( &$this, 'vtprd_admin_process' ) ); //v1.1.7.2
                      
    } //v1.0.7.2  end
    
	}   //end constructor

  	                                                             
 /* ************************************************
 **   Overhead and Init
 *************************************************** */
	public function vtprd_controller_init(){

  //error_log( print_r(  'Function begin - vtprd_controller_init', true ) );
    global $vtprd_setup_options, $product, $vtprd_info, $vtprd_cart;  //v1.1.7

    //$product->get_rating_count() odd error at checkout... woocommerce/templates/single-product-reviews.php on line 20  
    //  (Fatal error: Call to a member function get_rating_count() on a non-object)

    load_plugin_textdomain( 'vtprd', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );  //v1.0.8.4  moved here above defs

  /* //v1.1.7 moved below
    //v1.0.9.3 info not avail here
    //if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') { //v1.0.9.0  doesn't apply if 'discountUnitPrice'
      //v1.0.8.5 begin
      // instead of translation, using filter to allow title change!!!!!!!!
      //  this propagates throughout all plugin code execution through global...
      $coupon_title  = apply_filters('vtprd_coupon_code_discount_title','' );
      if ($coupon_title) {
         global $vtprd_info; 
         $vtprd_info['coupon_code_discount_deal_title'] = $coupon_title;
      }
 */     
      //error_log( print_r(  'vtprd_controller_init, $coupon_title = ' .$coupon_title, true ) );
      //error_log( print_r(  'vtprd_controller_init, coupon_code_discount_deal_title = ' .$vtprd_info['coupon_code_discount_deal_title'], true ) );
   // }  //v1.0.9.0
    /*
    // Sample filter execution ==>>  put into your theme's functions.php file, so it's not affected by plugin updates
          function coupon_code_discount_title() {
            return 'different coupon title';  //<<==  Change this text to be the title you want!!!
          }
          add_filter('vtprd_coupon_code_discount_title', 'coupon_code_discount_title', 10);         
    */
    //v1.0.8.5 end
    
    
    //Split off for AJAX add-to-cart, etc for Class resources.  Loads for is_Admin and true INIT loads are kept here.
    //require_once ( VTPRD_DIRNAME . '/core/vtprd-load-execution-resources.php' );

    require_once  ( VTPRD_DIRNAME . '/core/vtprd-backbone.php' );    
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-rules-classes.php');
    require_once  ( VTPRD_DIRNAME . '/admin/vtprd-rules-ui-framework.php' );
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-functions.php');
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-theme-functions.php');
    require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-cart-validation.php');
//  require_once  ( VTPRD_DIRNAME . '/woo-integration/vtprd-parent-definitions.php');    //v1.0.8.4  moved above
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-cart-classes.php');
        
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-cron-class.php' ); //v1.1.6 
            
    //***************
    //v1.1.5 begin
    // Licensing and Phone Home ONLY occurs when the purchased PRO version is installed
    //***************
    require_once ( VTPRD_DIRNAME . '/admin/vtprd-license-options.php');   
    global $vtprd_license_options; 
    $vtprd_license_options = get_option('vtprd_license_options'); 
    
    $this->vtprd_init_update_license();
    
    if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
       error_log( print_r(  'Begin FREE plugin, vtprd_license_options= ', true ) );  
       error_log( var_export($vtprd_license_options, true ) ); 
    }

   
    /*
    //*********************************************************
      VTPRD_PRO_DIRNAME trigger for Pro functionality...
      ONLY if PRO is active 
       if fatal status, deactivate PRO
       if pending status and ADMIN, load PRO stuff
       if pending status and EXECUTION, load FREE stuff
      Otherwise, load FREE
    //*********************************************************
    */
    
    //v1.1.6 begin                     
/*
NOW DO IN A CRON JOB
    //moved here
    if (is_admin) {
      $test_done_elsewhere = true;
    } else {
//error_log( print_r(  'vtprd_maybe_recheck_license_activation CART ', true ) ); 
      vtprd_maybe_recheck_license_activation(); //v1.1.6  added 1/12hr license recheck to SHOP function too.
    }
*/
    //v1.1.6 end
    
    
    $avanti = false; //v1.1.5
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) {

        switch( true ) { 
          //if fatal status, set Pro to deactivate during admin_init
          /* v1.1.6.3 version_status issues are NO LONger a deactivation action!
          case ( ($vtprd_license_options['state'] == 'suspended-by-vendor')
                             ||
                 ( ($vtprd_license_options['pro_plugin_version_status'] != 'valid') &&
                   ($vtprd_license_options['pro_plugin_version_status'] != null)) ) :  //null = default
          */
          case ($vtprd_license_options['state'] == 'suspended-by-vendor'):                                  
                //set up deactivate during admin_init - it's not available yet! done out of vtprd_maybe_pro_deactivate_action
                $vtprd_license_options['pro_deactivate'] = 'yes';
                update_option('vtprd_license_options', $vtprd_license_options); 
       
             break; 
          //if admin and (good or warning status) 
          case (is_admin()) :
                define('VTPRD_PRO_DIRNAME', VTPRD_PRO_DIRNAME_IF_ACTIVE);
                $avanti = true; //v1.1.5

                if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
                   error_log( print_r(  'is_admin, VTPRD_PRO_DIRNAME defined ', true ) );
                }                  
             break;                  

          //if frontend execution and all good status
          default:
                 if ( ($vtprd_license_options['status'] == 'valid') && 
                      ($vtprd_license_options['state']  == 'active') && //if license is deactivated, pro is not loaded!!
                      ($vtprd_license_options['pro_plugin_version_status'] == 'valid')  )  {
                       
                    define('VTPRD_PRO_DIRNAME', VTPRD_PRO_DIRNAME_IF_ACTIVE); 
                    $avanti = true; //v1.1.5  
                    if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
                       error_log( print_r(  'During Execution, VTPRD_PRO_DIRNAME defined ', true ) );
                    }    
                                    

                    //so CRON JOB ONLY RUN if PRO is active 
                    //v1.1.6.3 Refactored.  Demo no longer available, due to HACKING 
                    
                    add_action( 'vtprd_twice_daily_scheduled_events', 'vtprd_recheck_license_activation' );      
                    
                    /* 
                    //v1.1.6.1 begin                                                          
                    if ($vtprd_license_options['prod_or_test'] == 'demo') {
                      add_action( 'vtprd_thrice_daily_scheduled_events', 'vtprd_recheck_license_activation' ); 
                    } else {
                      //for a non-demo, only do twice per day
                      remove_action( 'vtprd_thrice_daily_scheduled_events', 'vtprd_recheck_license_activation' ); 
                      add_action( 'vtprd_twice_daily_scheduled_events', 'vtprd_recheck_license_activation' ); 
                    }
                    //v1.1.6.1 end
                    */
                    //v1.1.6.3 end
                    
                }
             break;
         } 
         
     }                         
    //***************
    //v1.1.5  end
    //***************


    $vtprd_setup_options = get_option( 'vtprd_setup_options' );  //put the setup_options into the global namespace 

       //error_log( print_r(  '$vtprd_setup_options after LOAD', true ) );
       //error_log( var_export($vtprd_setup_options, true ) );
    
    //**************************
    //v1.0.9.0 begin  
    //**************************
    switch( true ) { 
      
      case  is_admin() : //absolutely REQUIRED!!!
        $do_nothing;
        break;
         
      case ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') :
          //*******************
          //v1.1.7 BEGIN 
          //*******************

          // instead of translation, using filter to allow title change!!!!!!!!
          //  this propagates throughout all plugin code execution through global...
          $coupon_title  = apply_filters('vtprd_coupon_code_discount_title','' );
          if ($coupon_title) {
             $vtprd_info['coupon_code_discount_deal_title'] = $coupon_title;
          }          
/*          
          //at checkout, block the 'remove' URL from appearing next to a pricing deal Deal!!
          if ( ($coupon_title) &&
               ($vtprd_cart > '') &&
               ($vtprd_cart->yousave_cart_total_amt > 0) ) {
            echo '<style type="text/css"> tr.coupon-' .$coupon_title. ' a {display:none;} </style>';
          }
*/
          //v1.1.7 end
          
        break;
             
      case ($vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice') :
        //turn off switches not allowed for "discountUnitPrice" ==> done on the fly, rather than at update time...
        $vtprd_setup_options['show_checkout_purchases_subtotal']     =   'none';                           
        $vtprd_setup_options['show_checkout_discount_total_line']    =   'no'; 
        $vtprd_setup_options['checkout_new_subtotal_line']           =   'no'; 
        $vtprd_setup_options['show_cartWidget_purchases_subtotal']   =   'none';                           
        $vtprd_setup_options['show_cartWidget_discount_total_line']  =   'no'; 
        $vtprd_setup_options['cartWidget_new_subtotal_line']         =   'no';         
        break;
                
      default:    
        // supply default for new variables as needed for upgrade v1.0.8.9 => v1.0.9.0 as needed
        $vtprd_setup_options['discount_taken_where']        =   'discountCoupon';  
        $vtprd_setup_options['give_more_or_less_discount']  =   'more'; 
        $vtprd_setup_options['show_unit_price_cart_discount_crossout']     =   'yes'; //v1.0.9.3 ==> for help when switching to unit pricing...
        $vtprd_setup_options['show_unit_price_cart_discount_computation']  =   'no'; //v1.0.9.3 
        update_option( 'vtprd_setup_options',$vtprd_setup_options);  //v1.0.9.1
        break;
    
    }
    //v1.0.9.0 end 


       //error_log( print_r(  '$vtprd_setup_options after CASE', true ) );
       //error_log( var_export($vtprd_setup_options, true ) );
    
    if (function_exists('vtprd_debug_options')) { 
      vtprd_debug_options();  //v1.0.5
    }
            
    /*  **********************************
        Set GMT time zone for Store 
    Since Web Host can be on a different
    continent, with a different *Day* and Time,
    than the actual store.  Needed for Begin/end date processing
    **********************************  */
    vtprd_set_selected_timezone();

    if (is_admin()){ 
        add_filter( 'plugin_action_links_' . VTPRD_PLUGIN_SLUG , array( $this, 'vtprd_custom_action_links' ) );

        require_once ( VTPRD_DIRNAME . '/admin/vtprd-setup-options.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-rules-ui.php' );
           
        //if ((defined('VTPRD_PRO_DIRNAME')) )  {     //v1.1.5 
        if ($avanti) {                                //v1.1.5 
          require_once ( VTPRD_PRO_DIRNAME . '/admin/vtprd-rules-update.php'); 
          require_once ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' ); 

        } else {
          require_once ( VTPRD_DIRNAME .     '/admin/vtprd-rules-update.php');
        }
        
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-show-help-functions.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-checkbox-classes.php');
        require_once ( VTPRD_DIRNAME . '/admin/vtprd-rules-delete.php');
        
        /* v1.1.7.2 replaced with add_action above!!!
        $this->vtprd_admin_process();  //v1.1.5
        */
        
        //v1.0.7.1 begin
        /* v1.1.0.1  replaced with new notification at admin_init
        if ( (defined('VTPRD_PRO_DIRNAME')) &&
             (version_compare(VTPRD_PRO_VERSION, VTPRD_MINIMUM_PRO_VERSION) < 0) ) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch') );            
        }
        */
        //v1.0.7.1 end 
      
      /* //v1.0.9.3 moved to functions to be run at admin-init time
        if ($vtprd_setup_options['discount_taken_where'] == 'discountCoupon') { //v1.0.9.3  doesn't apply if 'discountUnitPrice'
        //v1.0.7.4 begin  
          //****************************************
          //INSIST that coupons be enabled in woo, in order for this plugin to work!!
          //****************************************
          //always check if the manually created coupon codes are there - if not create them.
          vtprd_woo_maybe_create_coupon_types();        
          $coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;
          if (!$coupons_enabled) {  
            add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_coupon_enable_required') );            
          } 
        }
        */
  // don't have to do this EXCEPT at install time....
  //    $this->vtprd_maybe_add_wholesale_role(); //v1.0.9.0
 
      //v1.0.7.4 end 
      
    //*******************************************************************************
    //v1.1.6 BEGIN
    //v1.1.6  Commented the else, and CHANGED for below - apply-rules CAN be executed in ADMIN, if pricing is called.  need the resources, so put back into MAINLINE!
    //**************
    /* 
    } else {

        add_action( "wp_enqueue_scripts", array(&$this, 'vtprd_enqueue_frontend_scripts'), 1 );    //priority 1 to run 1st, so front-end-css can be overridden by another file with a dependancy
        
        //v1.1.5  BEGIN
         // the 'plugin_version_valid' switches are set in ADMIN, but only used in the Front End
        //if (defined('VTPRD_PRO_DIRNAME'))  {      //v1.1.5  
        if ($avanti) {                              //v1.1.5                 
          require_once  ( VTPRD_PRO_DIRNAME . '/core/vtprd-apply-rules.php' );
          require_once  ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' );
          if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
            error_log( print_r(  'Free Plugin begin, Loaded PRO plugin apply-rules', true ) );
          }                   
        } else {       
          require_once  ( VTPRD_DIRNAME .     '/core/vtprd-apply-rules.php' );
          if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
            error_log( print_r(  'Free Plugin begin, Loaded PFREE plugin apply-rules', true ) );
          }           
        }
        //v1.1.5  End
    
    }
    */

  } 

    add_action( "wp_enqueue_scripts", array(&$this, 'vtprd_enqueue_frontend_scripts'), 1 );    //priority 1 to run 1st, so front-end-css can be overridden by another file with a dependancy
    
    //v1.1.5  BEGIN
     // the 'plugin_version_valid' switches are set in ADMIN, but only used in the Front End
    //if (defined('VTPRD_PRO_DIRNAME'))  {      //v1.1.5  
    if ($avanti) {                              //v1.1.5                 
      require_once  ( VTPRD_PRO_DIRNAME . '/core/vtprd-apply-rules.php' );
      require_once  ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' );
      if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
        error_log( print_r(  'Free Plugin begin, Loaded PRO plugin apply-rules, settings= ', true ) );
        error_log( var_export($vtprd_setup_options, true ) );
      }                   
    } else {       
      require_once  ( VTPRD_DIRNAME .     '/core/vtprd-apply-rules.php' );
      if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
        error_log( print_r(  'Free Plugin begin, Loaded FREE plugin apply-rules', true ) );
      }           
    }
    //v1.1.5  End
    //*******************************************************************************
    //v1.1.6 END
    //*******************************************************************************



      /*
    if (is_admin()){ 

      //LIFETIME logid cleanup...
      //  LogID logic from wpsc-admin/init.php
      if(defined('VTPRD_PRO_DIRNAME')) {
        switch( true ) {
          case ( isset( $_REQUEST['wpsc_admin_action2'] ) && ($_REQUEST['wpsc_admin_action2'] == 'purchlog_bulk_modify') )  :
                 vtprd_maybe_lifetime_log_bulk_modify();
             break; 
          case ( isset( $_REQUEST['wpsc_admin_action'] ) && ($_REQUEST['wpsc_admin_action'] == 'delete_purchlog') ) :
                 vtprd_maybe_lifetime_log_roll_out_cntl();
             break;                                             
        } 
          
        if (version_compare(VTPRD_PRO_VERSION, VTPRD_MINIMUM_PRO_VERSION) < 0) {    //'<0' = 1st value is lower  
          add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch') );            
        }          
      }
      
      //****************************************
      //INSIST that coupons be enabled in woo, in order for this plugin to work!!
      //****************************************
      $coupons_enabled = get_option( 'woocommerce_enable_coupons' ) == 'no' ? false : true;
      if (!$coupons_enabled) {  
        add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_coupon_enable_required') );            
      } 
    } 
      */   
      
      //v1.1.5  BEGIN ==>> shifted down here!   
    
     //*********************************
     //don't run in admin, this is ONLY for executions
     //*********************************
     /*
     if (is_admin() ) { 
        $skip_this = true; 
     } else {
         // the 'plugin_version_valid' switches are set in ADMIN, but only used in the Front End
        if (defined('VTPRD_PRO_DIRNAME'))  {                  
          require_once  ( VTPRD_PRO_DIRNAME . '/core/vtprd-apply-rules.php' );
          require_once  ( VTPRD_PRO_DIRNAME . '/woo-integration/vtprd-lifetime-functions.php' );
          if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
            error_log( print_r(  'Free Plugin begin, Loaded PRO plugin apply-rules', true ) );
          }                   
        } else {       
          require_once  ( VTPRD_DIRNAME .     '/core/vtprd-apply-rules.php' );
          if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
            error_log( print_r(  'Free Plugin begin, Loaded PFREE plugin apply-rules', true ) );
          }           
        }
        
     }
     */
    //v1.1.5  end


    return; 
    
  }
  
  //***************************
  //v1.1.0.1  new function 
  //***************************
  public function vtprd_maybe_plugin_mismatch(){
  //v1.1.5  NOW executed at admin_notices time!!!!!!!

      /* v1.1.6.3 REMOVED
      //v1.1.1
      // Check if WooCommerce is active
      if ( ! class_exists( 'WooCommerce' ) )  {
      	//add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_woocommerce_required') ); //v1.1.5
        $this->vtprd_admin_notice_woocommerce_required();  //v1.1.5
      }
      */
      
      global $vtprd_setup_options;
      if ( ( class_exists( 'WC_Measurement_Price_Calculator' ) ) && 
           ( isset($vtprd_setup_options['discount_taken_where']) ) &&
           ( $vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice' ) ) {
      	//add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_cant_use_unit_price') ); //v1.1.5
        $this->vtprd_admin_notice_cant_use_unit_price(); //v1.1.5
      }      
      if ( ( class_exists( 'WC_Product_Addons' ) ) && 
           ( isset($vtprd_setup_options['discount_taken_where']) ) &&
           ( $vtprd_setup_options['discount_taken_where'] == 'discountUnitPrice' ) ) {
      	//add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_cant_use_unit_price') ); //v1.1.5
        $this->vtprd_admin_notice_cant_use_unit_price();  //v1.1.5
      }      
            
      
      //v1.1.1
       
    return;
  
  }  
  

  public function vtprd_enqueue_frontend_scripts(){
    global $vtprd_setup_options;
  
 //error_log( print_r(  'Function begin - vtprd_enqueue_frontend_scripts', true ) );
         
    wp_enqueue_script('jquery'); //needed universally
    
    if ( $vtprd_setup_options['use_plugin_front_end_css'] == 'yes' ){
      wp_register_style( 'vtprd-front-end-style', VTPRD_URL.'/core/css/vtprd-front-end-min.css'  );   //every theme MUST have a style.css...  
      //wp_register_style( 'vtprd-front-end-style', VTPRD_URL.'/core/css/vtprd-front-end-min.css', array('style.css')  );   //every theme MUST have a style.css...      
      wp_enqueue_style('vtprd-front-end-style');
    }
    
    return;
  
  }  

         
  /* ************************************************
  **   Admin - Remove bulk actions on edit list screen, actions don't work the same way as onesies...
  ***************************************************/ 
  function vtprd_custom_bulk_actions($actions){
              //v1.0.7.2  add  ".inline.hide-if-no-js, .view" to display:none; list
    ?>         
    <style type="text/css"> #delete_all, .inline.hide-if-no-js, .view {display:none;} /*kill the 'empty trash' buttons, for the same reason*/ </style>
    <?php
    
    unset( $actions['edit'] );
    unset( $actions['trash'] );
    unset( $actions['untrash'] );
    unset( $actions['delete'] );
    return $actions;
  }

      
  /* ************************************************
  **   Admin - Show Rule UI Screen
  *************************************************** 
  *  This function is executed whenever the add/modify screen is presented
  *  WP also executes it ++right after the update function, prior to the screen being sent back to the user.   
  */  
	public function vtprd_admin_process(){ //v1.1.5
  
 //error_log( print_r(  'Function begin - vtprd_admin_init', true ) );
      /*
      //v1.1.7 removed - causes warning on plugin install/delete       
     if ( !current_user_can( 'edit_posts', 'vtprd-rule' ) )
          return;
    */

     $vtprd_rules_ui = new VTPRD_Rules_UI;      
  }

  /* ************************************************
  **   Admin - Publish/Update Rule or Parent Plugin CPT 
  *************************************************** */
	public function vtprd_admin_update_rule_cntl(){
      global $post, $vtprd_info;    
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_rule_cntl', true ) );
         
      
      // v1.0.7.3 begin
      if( !isset( $post ) ) {    
        return;
      }  
      // v1.0.7.3  end
                        
      switch( $post->post_type ) {
        case 'vtprd-rule':
            $this->vtprd_admin_update_rule(); 
            
            $this->vtprd_track_rule_coupon_activations(); //v1.1.7.1 
            
          break; 
        case $vtprd_info['parent_plugin_cpt']: //this is the update from the PRODUCT screen, and updates the include/exclude lists
            $this->vtprd_admin_update_product_meta_info();
          break;
      }  
      return;
  }
  
  
  /* ************************************************
  **   Admin - Publish/Update Rule 
  *************************************************** */
	public function vtprd_admin_update_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_rule', true ) );
     
    /* *****************************************************************
         The delete/trash/untrash actions *will sometimes fire save_post*
         and there is a case structure in the save_post function to handle this.
    
          the delete/trash actions are sometimes fired twice, 
               so this can be handled by checking 'did_action'
               
          'publish' action flows through to the bottom     
     ***************************************************************** */
      
      global $post, $vtprd_rules_set;
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
      
      if ( !( 'vtprd-rule' == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }
     if (isset($_REQUEST['vtprd_nonce']) ) {     //nonce created in vtprd-rules-ui.php  
          $nonce = $_REQUEST['vtprd_nonce'];
          if(!wp_verify_nonce($nonce, 'vtprd-rule-nonce')) { 
            return;
          }
      } 
      if ( !current_user_can( 'edit_posts', 'vtprd-rule' ) ) {
          return;
      }

      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      //AND if we're here via an action other than a true save, do the action and exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        switch( $action_type ) {
            case 'trash':
                $this->vtprd_admin_trash_rule();  
              break; 
            case 'untrash':
                $this->vtprd_admin_untrash_rule();
              break;
            case 'delete':
                $this->vtprd_admin_delete_rule();  
              break;
        }
        return;
      }
      // lets through  $action_type == editpost                
      $vtprd_rule_update = new VTPRD_Rule_update;
  }
   
  
 /* ************************************************
 **   Admin - Delete Rule
 *************************************************** */
	public function vtprd_admin_delete_rule(){
     global $post, $vtprd_rules_set; 
  
 //error_log( print_r(  'Function begin - vtprd_admin_delete_rule', true ) );
          
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
      
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }
    
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_delete_rule();
    
    $this->vtprd_track_rule_coupon_activations(); //v1.1.7.1       
    
    /* NO!! - the purchase history STAYS!
    if(defined('VTPRD_PRO_DIRNAME')) {
      vtprd_delete_lifetime_rule_info();
    }   
     */
  }
  
  
  /* ************************************************
  **   Admin - Trash Rule
  *************************************************** */   
	public function vtprd_admin_trash_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_trash_rule', true ) );
           
     global $post, $vtprd_rules_set; 
       //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
          
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        
  
     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }  
     
     if(did_action('trash_post')) {    
         return;
    }
    
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_trash_rule();
    
    $this->vtprd_track_rule_coupon_activations(); //v1.1.7.1

  }
  
  
 /* ************************************************
 **   Admin - Untrash Rule
 *************************************************** */   
	public function vtprd_admin_untrash_rule(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_untrash_rule', true ) );
             
     global $post, $vtprd_rules_set; 
      //v1.1.0.9 begin
      if( !isset( $post ) ) {    
        return;
      }       
      //v1.1.0.9 end
           
     if ( !( 'vtprd-rule' == $post->post_type ) ) {
      return;
     }        

     if ( !current_user_can( 'delete_posts', 'vtprd-rule' ) )  {
          return;
     }       
    $vtprd_rule_delete = new VTPRD_Rule_delete;            
    $vtprd_rule_delete->vtprd_untrash_rule();
    
    $this->vtprd_track_rule_coupon_activations(); //v1.1.7.1
  }
  
  
  /* ************************************************
  **   Admin - New function v1.1.7.1
  *     create and save an array of coupon IDs used to activate rules  
  *************************************************** */
  /* Problem: 
        coupon codes presented on CART page do not actuate their Pricing Deal Rule until a page refresh
     Solution:
        1. - Grab all of the coupon codes to be found in a Pricing Deal rules, place into Options array
           - this array is used to communicate with the  
              "woocommerce_coupon_message" hook and possibly supply an unique triggering string
              
        2. "woocommerce_coupon_message" hook runs vtprd_add_trigger_to_coupon_message, which adds
            a triggering string of '+++'
        3. JS supplied only on the cart page in vtprd_send_cart_js_trigger_page_reload
            looks for the triggering string, and if it's there, forces a page reload to pick up the discount.  
  */
	public function vtprd_track_rule_coupon_activations(){
    //error_log( print_r(  'Function Begin - vtprd_track_rule_coupon_activations', true ) ); 
    
    require_once  ( VTPRD_DIRNAME . '/core/vtprd-rules-classes.php');
    
    //local - do not use the GLOBAL
    $vtprd_rules_set   = get_option( 'vtprd_rules_set' ) ;
    if (!$vtprd_rules_set) {
      delete_option('vtprd_rule_coupon_activations');
    //error_log( print_r(  'delete option and exit1 - vtprd_track_rule_coupon_activations', true ) );
      return; 
    }
    
    $vtprd_rule_coupon_activations = array();
    
    $sizeof_rules_set = sizeof($vtprd_rules_set);
    //error_log( print_r(  '$sizeof_rules_set= s' .$sizeof_rules_set ,true ) );
    for($i=0; $i < $sizeof_rules_set; $i++) { 
     
    //error_log( print_r(  'rules_set loop, $i= ' .$i, true ) );   
      //error_log( print_r(  '$vtprd_rules_set entry:', true ) );
      //error_log( var_export($vtprd_rules_set[$i], true ) ); 
      //error_log( print_r(  'only_for_this_coupon_name' .$vtprd_rules_set[$i]->only_for_this_coupon_name, true ) );
    
       if ($vtprd_rules_set[$i]->only_for_this_coupon_name > ' ') {
          $vtprd_rule_coupon_activations[] = $vtprd_rules_set[$i]->only_for_this_coupon_name;
      //error_log( print_r(  'RABBIT found ', true ) );                       
       }
    }
    
    if (sizeof($vtprd_rule_coupon_activations) > 0) {
    //error_log( print_r(  'update option and exit - vtprd_track_rule_coupon_activations', true ) );
      update_option('vtprd_rule_coupon_activations', $vtprd_rule_coupon_activations);
    } else {
    //error_log( print_r(  'delete option and exit2 - vtprd_track_rule_coupon_activations', true ) );    
      delete_option('vtprd_rule_coupon_activations');
    }
    
    return;
    
      
  }  
  
  /* ************************************************
  **   Admin - Update PRODUCT Meta - include/exclude info
  *      from Meta box added to PRODUCT in rules-ui.php  
  *************************************************** */
	public function vtprd_admin_update_product_meta_info(){
  
 //error_log( print_r(  'Function begin - vtprd_admin_update_product_meta_info', true ) );
   
      global $post, $vtprd_rules_set, $vtprd_info;
      if ( !( $vtprd_info['parent_plugin_cpt'] == $post->post_type )) {
        return;
      }  
      if (( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
            return; 
      }

      if ( !current_user_can( 'edit_posts', $vtprd_info['parent_plugin_cpt'] ) ) {
          return;
      }
       //AND if we're here via an action other than a true save, exit stage left
      $action_type = $_REQUEST['action'];
      if ( in_array($action_type, array('trash', 'untrash', 'delete') ) ) {
        return;
      }
      
      /* ******************************************
       The 'SAVE_POST' action is fired at odd times during updating.
       When it's fired early, there's no post data available.
       So checking for a blank post id is an effective solution.
      *************************************************** */      
      if ( !( $post->ID > ' ' ) ) { //a blank post id means no data to proces....
        return;
      } 
      


      $includeOrExclude_option = $_REQUEST['includeOrExclude'];
      switch( $includeOrExclude_option ) {
        case 'includeAll':
        case 'excludeAll':   
            $includeOrExclude_checked_list = null; //initialize to null, as it's used later...
          break;
        case 'includeList':                  
        case 'excludeList':
            $includeOrExclude_checked_list = $_REQUEST['includeOrExclude-checked_list']; //contains list of checked rule post-id"s  v1.0.8.9                                               
          break;
      }

      $vtprd_includeOrExclude = array (
            'includeOrExclude_option'         => $includeOrExclude_option,
            'includeOrExclude_checked_list'   => $includeOrExclude_checked_list
             );
     
      //keep the add meta to retain the unique parameter...
      $vtprd_includeOrExclude_meta  = get_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], true);
      if ( $vtprd_includeOrExclude_meta  ) {
        update_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], $vtprd_includeOrExclude);
      } else {
        add_post_meta($post->ID, $vtprd_info['product_meta_key_includeOrExclude'], $vtprd_includeOrExclude, true);
      }
      
      //v1.1.0.7 begin
      //Update from product Publish box checkbox, labeled 'wholesale product'
      update_post_meta($post->ID, 'vtprd_wholesale_visibility', $_REQUEST['vtprd-wholesale-visibility']);
      //v1.1.0.7 end
      
  }
 

  /* ************************************************
  **   Admin - Activation Hook
  *************************************************** */  
	public function vtprd_activation_hook() {
  
 //    //error_log( print_r(  'Function begin - vtprd_activation_hook', true ) );
   
    global $wp_version, $vtprd_setup_options;
    //the options are added at admin_init time by the setup_options.php as soon as plugin is activated!!!
    
    //v1.1.7 begin - initialize setup options correctly first time through!
    if (!get_option( 'vtprd_setup_options' )) {
      require_once ( VTPRD_DIRNAME . '/admin/vtprd-setup-options.php');
      $vtprd_setup_plugin_options = new VTPRD_Setup_Plugin_Options;
      $vtprd_setup_options = $vtprd_setup_plugin_options->vtprd_set_default_options();
      update_option( 'vtprd_setup_options',$vtprd_setup_options);
    }
    //v1.1.7 end
        
    $this->vtprd_create_discount_log_tables();

    $this->vtprd_maybe_add_wholesale_role(); //v1.0.9.0
    
    //v1.1.6.2 begin
    //on activation - prevent cron job from checking registration 
    // it was somehow causing the installation check to FAIL - but after install complete, all good.
    update_option('vtprd_no_check_on_activation', 'yes'); 
    //v1.1.6.2 end

    //v1.1.7 begin 
        $message  =  '&nbsp;&nbsp;<strong>' .VTPRD_PLUGIN_NAME. __(' has been updated. ' , 'vtprd') .'</strong>';
        if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) { //check if older than version 3.0.0
          $message .=  '<br><br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;<strong> Due to *coming* WOOCOMMERCE 3.0 changes </strong>' ;
        } else {
          $message .=  '<br><br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;<strong> Due to WOOCOMMERCE 3.0+ changes </strong>' ;
        }
        $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong> <em>  - IF YOU Show Cart deal discounts as an auto-inserted coupon </em></strong> &nbsp;&nbsp;&nbsp;&nbsp;(Coupon: Deals)  ' ;
        $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>  and *change the name of the Coupon* </strong>' ; 
        $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>  PLEASE review the *new requirement* for an additional new WOO coupon. </em>';
        $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Go to the FREE plugin zip file =>  pricing-deals-for-woocommerce/languages/translation directions.txt . ';  

        $admin_notices = '<div class="error fade notice is-dismissible" 
          style="
                line-height: 19px;
                padding: 11px 15px;
                font-size: 14px;
                text-align: left;
                margin: 25px 20px 15px 2px;
                background-color: #fff;
                border-left: 4px solid #ffba00;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';
        //activation notices must be deferred =>>  fatal test for Woo, etc in parent-functions
        $notices= get_option('vtprd_deferred_admin_notices', array());
        $notices[]= $admin_notices;
        update_option('vtprd_deferred_admin_notices', $notices);
     //v1.1.7 end  


    //v1.0.9.3 begin 
 
    //other edits moved to function vtprd_check_for_deactivation_action run at admin-init time
       
    //if plugin updated/installed, wipe out session for fresh start.
    if(!isset($_SESSION)){
      session_start();
      header("Cache-Control: no-cache");
      header("Pragma: no-cache");
    }    
    session_destroy(); 
    
    //pick up any existing coupon info on plugin activation
        //error_log( print_r(  'Activation Hook - vtprd_track_rule_coupon_activations', true ) );    
    $this->vtprd_track_rule_coupon_activations(); //v1.1.7.1
    
    //v1.0.5 begin
    //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) { //v1.1.5
       return;      
    }
    //v1.0.5 end
     
    $pro_plugin_is_installed = $this->vtprd_maybe_pro_plugin_installed(); // function pro_plugin_installed must be in the class!!

    //v1.1.5 begin
    if ($pro_plugin_is_installed) { 
        $message  =  '&nbsp;&nbsp;<strong>' .VTPRD_PLUGIN_NAME. __(' has been updated. ' , 'vtprd') .'</strong>';
        $message .=  '&nbsp;&nbsp;&bull;&nbsp;&nbsp;<strong>' .VTPRD_PRO_PLUGIN_NAME. __(' * may * have been deactivated.' . '</strong>' , 'vtprd'); 
        $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Please Re-Activate, if desired.' , 'vtprd');
        $admin_notices = '<div class="error fade notice is-dismissible" 
          style="
                line-height: 19px;
                padding: 11px 15px;
                font-size: 14px;
                text-align: left;
                margin: 25px 20px 15px 2px;
                background-color: #fff;
                border-left: 4px solid #ffba00;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';
        //activation notices must be deferred =>>  fatal test for Woo, etc in parent-functions
        $notices= get_option('vtprd_deferred_admin_notices', array());
        $notices[]= $admin_notices;
        update_option('vtprd_deferred_admin_notices', $notices);
     } 
     //v1.1.5 end         

      //v1.1.6.3 begin
      wp_clear_scheduled_hook( 'vtprd_thrice_daily_scheduled_events' );  //in case the old demo schedule is still hanging about
      //v1.1.6.3 end

          
     return; 
          
  }
  
  /* ************************************************
  **  v1.1.6.1 new function
  *************************************************** */  
	public function vtprd_deactivation_hook() {

    //  wp_clear_scheduled_hook( 'vtprd_thrice_daily_scheduled_events' );  v1.1.6.3 no longer used
      wp_clear_scheduled_hook( 'vtprd_twice_daily_scheduled_events' );
     
     return; 
  }



   //v1.0.7.1 begin 
   //**************************** 
   //v1.1.5 refactored
   //v1.1.6.3  Refactored to be a SIMPLER Message, except on rego screen
   //v1.1.6.7  Refactored (again!)
   //****************************                       
   public function vtprd_admin_notice_version_mismatch_pro() {
  
   //    //error_log( print_r(  'Function begin - vtprd_admin_notice_version_mismatch_pro', true ) );

      global $vtprd_license_options;
      
      $pageURL = $_SERVER["REQUEST_URI"];      

      switch( true ) { 
        case (strpos($pageURL,'delete-selected') !== false ):         
                return; //annoying to have warnings on the delete page!
             break;
          
        case (strpos($pageURL,'vtprd_license_options_page') !== false ):         
                //v1.1.6.7  NOW handled in vtprd-license-options as a direct message, as admin-notices are sometimes blocked by a conflicting plugin!! 
                return;
             break;
          
        case (strpos($pageURL,'update-core') !== false ):
            $message  =   VTPRD_PRO_PLUGIN_NAME;
            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong> *** &nbsp;&nbsp;' . __(' Now CLICK HERE: '  , 'vtprd').'&nbsp;&nbsp;';
            $message .=     '<a style="text-decoration: underline;font-size:16px;" class="ab-item" href="'.$vtprd_license_options['home_url'].'/wp-admin/plugins.php?plugin_status=all&paged=1&s">' . __('GO BACK to Plugins Page', 'vtprd') . '</a>'.'&nbsp;&nbsp;&nbsp; ***' . '</strong>' ;          
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('and look for the plugin update notification. ' , 'vtprd');
          break;
        
        default:          
            //v1.1.6.7  message reworded
            $message  =  '<strong>' . __('Update Required for: ' , 'vtprd') . ' &nbsp;&nbsp;'  .VTPRD_PRO_PLUGIN_NAME . '</strong>' ;
            $message .=  "<span style='color:red !important;font-size:16px;'><strong><em>&nbsp;&nbsp;&nbsp; (pro plugin will **not discount** until updated)</em></strong></span>" ;  //v1.1.7 change color, wording
            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Your Pro Version = ' , 'vtprd') .$vtprd_license_options['pro_version'] .'&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . __(' <em>Required</em> Pro Version = ' , 'vtprd') .VTPRD_MINIMUM_PRO_VERSION .'</strong>'; 
    
            if (strpos($pageURL,'plugins.php') !== false ) {
              //$message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;<em>' . __('Check for a plugin update reminder (nag) </em><strong>&nbsp;&nbsp; in the plugin list below! </strong>'  , 'vtprd');
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('If no "update now" below, &nbsp;&nbsp; CLICK HERE: '  , 'vtprd') .'&nbsp;&nbsp;';
            } else {
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('CLICK HERE: '  , 'vtprd') .'&nbsp;&nbsp;';
            }
                            
            //$message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Otherwise, CLICK HERE: '  , 'vtprd') .'&nbsp;&nbsp;';
            $message .=  '<a style="text-decoration: underline;font-size:16px;"  href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page&action=force_plugin_updates_check"><strong>' . __('Check for Plugin Updates', 'vtprd'). '</strong></a>';          
          
            if (strpos($pageURL,'plugins.php') !== false ) {
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('If still no "update now", &nbsp; go to the '  , 'vtprd') .'&nbsp;&nbsp;';
              $message .=  '<a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Register Pro License Page</a>' ; 
            }
          break;      
      }
      
      
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      
      echo $admin_notices;
      return;    
  }   

   //**************************** 
   //v1.1.6.7 New Function
   // if pro installed test done before this action is loaded...
   //****************************       
    function vtprd_plugin_notice_version_mismatch_pro( $plugin_file, $plugin_data, $status ) {
       global $vtprd_license_options;
       if (!$vtprd_license_options) {
          $vtprd_license_options = get_option( 'vtprd_license_options' ); 
       } 

       if ( ($vtprd_license_options['pro_plugin_version_status'] == 'Pro Version Error') &&  
          //  (strpos( $plugin_file, 'pricing-deals-for-woocommerce' ) !== false )  ) {
            (strpos( $plugin_file, VTPRD_PRO_PLUGIN_FOLDER ) !== false )  ) {
   
            if ( (isset($plugin_data['url'])) && 
                 (isset($plugin_data['package'])) &&
                 ($plugin_data['url'] !== false) &&
                 ($plugin_data['package'] !== false) ) {              
              //**************************************************************************
              //if update nag data is found, message unneccessary, 
              //   and actually gums up the works, so don't send!
              //**************************************************************************
              return;                    
            }
         
            $message  =  '<td colspan="5" class="update-msg" style="line-height:1.2em; font-size:12px; padding:1px;">';
            $message .=  '<div style="color:#000; font-weight:bold; margin:4px 4px 4px 5%; width:80%; padding:6px 5px; background-color:#fffbe4; border-color:#dfdfdf; border-width:1px; border-style:solid; -moz-border-radius:5px; -khtml-border-radius:5px; -webkit-border-radius:5px; border-radius:5px;">';
            $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Get the New version ', 'vtprd') .'&nbsp; - &nbsp;&nbsp;<em>'. VTPRD_MINIMUM_PRO_VERSION .'</em>&nbsp;&nbsp; - &nbsp;'. __(' *required* &nbsp;&nbsp; for ', 'vtprd')  .'&nbsp;&nbsp;' . VTPRD_PRO_PLUGIN_NAME  ;
            $message .=  "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:red !important;font-size:16px;'><em>&nbsp;&nbsp;&nbsp; (pro plugin will **not discount** until updated)</em></span>" ; //v1.1.7 change color, wording
            //$message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .VTPRD_PRO_PLUGIN_NAME  .'&nbsp;&nbsp;&nbsp;&nbsp;' . __('New version ', 'vtprd') .'&nbsp;&nbsp;<em>'. VTPRD_MINIMUM_PRO_VERSION .'</em>&nbsp;&nbsp;'. __(' *required* ! ', 'vtprd')   ;
                  
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('To fetch the update, CLICK HERE: '  , 'vtprd') .'&nbsp;&nbsp;';
            $message .=  '<a style="text-decoration: underline;font-size:14px;" href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page&action=force_plugin_updates_check"><strong>' . __('Check for Plugin Updates', 'vtprd'). '</strong></a>';          
          
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('If still no update available, go to the '  , 'vtprd') .'&nbsp;&nbsp;';
            $message .=  '<a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Register Pro License Page</a>' ; 
            
            $message .=  '</div	></td>';            
            echo $message;
      }
      
      return;
    }
  
   //**************************** 
   //v1.1.5 refactored
   //****************************                       
   public function vtprd_admin_notice_version_mismatch_free() {
  
 //    //error_log( print_r(  'Function begin - vtprd_admin_notice_version_mismatch_free', true ) );
      global $vtprd_license_options;
      $message  =  '<strong>' . __('Please update the FREE plugin: ' , 'vtprd') . ' &nbsp;&nbsp;'  .VTPRD_PLUGIN_NAME . '</strong>' ;
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
      if (defined('VTPRD_PRO_VERSION')) {
        $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Required FREE version  = ' , 'vtprd') .$vtprd_license_options['pro_minimum_free_version']. ' &nbsp;&nbsp;<strong>' . 
              __(' Current Free Version = ' , 'vtprd') .VTPRD_VERSION .'</strong>';
      }  else {
        $message .=  '<br>&nbsp;&nbsp;&bull;&nbsp;&nbsp;<strong>' . __('FREE Plugin update required!! ' , 'vtprd').'</strong>';
      }          
            
      $message .=  '<br><br><strong>' . 'The PRO Plugin:' . ' &nbsp;&nbsp;</strong><em>'  .VTPRD_PRO_PLUGIN_NAME . '</em>&nbsp;&nbsp;<strong>' . '  ** will not give discounts ** until this is resolved.' .'</strong>' ;              
                   
      $message .=  '<br><br>&nbsp;&nbsp; 1. &nbsp;&nbsp;<strong>' . __('You should see an update prompt on your '  , 'vtprd');
      $message .=     '<a class="ab-item" href="'.$vtprd_license_options['home_url'].'/wp-admin/plugins.php?plugin_status=all&paged=1&s">' . __('Plugins Page', 'vtprd') . '</a>';
      $message .=     __(' for a FREE Plugin automated update'  , 'vtprd') .'</strong>';
      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('If no FREE Plugin update nag is visible, you can request Wordpress to check for an update: '  , 'vtprd');
      $message .=  '<a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page&action=force_plugin_updates_check">' . __('Check for Plugin Updates', 'vtprd'). '</a>';
      

      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bull;&nbsp;&nbsp;' . __('Be sure to  <em> re-Activate the PRO Plugin </em>, once the FREE plugin update has been completed. ', 'vtprd');
      $message .=  '</strong>';
      
      $message .=  "<span style='color:grey !important;'><br><br><em>&nbsp;&nbsp;&nbsp; (This message displays when the Pro version is installed, regardless of whether it's active)</em></span>" ;

      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      return;    
  }    
  
   //v1.0.7.1 end  

   public function vtprd_admin_notice_coupon_enable_required() {
     
 //    //error_log( print_r(  'Function begin - vtprd_admin_notice_coupon_enable_required', true ) );
  
      $message  =  '<strong>' . __('In order for the "' , 'vtprd') .VTPRD_PLUGIN_NAME. __('" plugin to function successfully, the Woo Coupons Setting must be on, and it is currently off.' , 'vtprd') . '</strong>' ;
      $message .=  '<br><br>' . __('Please go to the Woocommerce/Settings page.  Under the "Checkout" tab, check the box next to "Enable the use of coupons" and click on the "Save Changes" button.'  , 'vtprd');
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;
      return;    
  } 

   //v1.1.1 new function
   public function vtprd_admin_notice_woocommerce_required() {
  
 //    //error_log( print_r(  'Function begin - vtprd_admin_notice_woocommerce_required', true ) );
     
      $message  =  '<strong>' . __('In order for the "' , 'vtprd') .VTPRD_PLUGIN_NAME. __('" plugin to function, the WooCommerce must be installed and active!! ' , 'vtprd') . '</strong>' ;
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;     
      return;    
  } 

   //v1.1.1 new function
   public function vtprd_admin_notice_cant_use_unit_price() {
  
 //    //error_log( print_r(  'Function begin - vtprd_admin_notice_cant_use_unit_price', true ) );
      
      $message  =  '*******************************&nbsp;&nbsp;'. '<span style="color: blue !important;">' .VTPRD_PLUGIN_NAME . __('Settings &nbsp; Change &nbsp; ** Required **'  , 'vtprd') .'</span><br><br>';
      $message .=  __('<strong>Pricing Deals</strong> is fully compatible with &nbsp; <em>Woocommerce Product Addons</em> &nbsp; and &nbsp; <em>Woocommerce Measurement Price Calculator</em> . ' , 'vtprd')  ;
      $message .=  '<br><br>**&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('When either of these two plugins are installed and active, <strong>**A CHANGE MUST BE MADE** on your Pricing Deals Settings page.</strong>  ' , 'vtprd') ;
      $message .=  '<br><br>**&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Please go to the Pricing Deals/Settings page.  <em>At "Unit Price Discount or Coupon Discount" select "Coupon Discount"</em> and click on the "Save Changes" button.'  , 'vtprd');
      $message .=  '<br><br>' . __('(this is due to system limitations in the two named plugins.)'  , 'vtprd');     
      $message .=  '<br><br>*******************************';
      $admin_notices = '<div id="message" class="error fade" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
      echo $admin_notices;     
      return;      
  } 
 
   //*************************
   //v1.1.5 new function
   //*************************
   public function vtprd_maybe_system_requirements() {

      //OVERRIDE System Requirements testing
      if (apply_filters('vtprd_turn_off_system_requirements',FALSE ) ) {
        return;
      }
      
      //**********************
      //* MEMORY 64MB REQUIRED
      //**********************         
				  
       //v1.1.6.3
        // Check if WooCommerce is active
        if ( class_exists( 'WooCommerce' ) )  {

          $memory = wc_let_to_num( WP_MEMORY_LIMIT );
  
  				if ( function_exists( 'memory_get_usage' ) ) {
  					$system_memory = wc_let_to_num( @ini_get( 'memory_limit' ) );
  					$memory        = max( $memory, $system_memory );
  				}
           //VTPRD_PRO_VERSION only exists if PRO version is installed and active 
           if ( ( $memory < 67108864 ) && (defined('VTPRD_PRO_VERSION')) ) {     //test for 64mb   
            $message  =  '<h4>' . __('- ' , 'vtprd') .VTPRD_PLUGIN_NAME. __(' - You need a minimum of &nbsp;&nbsp; -- 64mb of system memory -- &nbsp;&nbsp; for your site to run Woocommerce + Pricing Deals successfully. ' , 'vtprd') . '</h4>' ;
            $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Your system memory is currently &nbsp;' .  size_format( $memory ) ;
            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- In wp-admin, please go to Woocommerce/System Status and look for WP Memory Limit.  ' ;
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- *** Suggest that you increase memory to 256mb *** (the new defacto standard...)  ' ;
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '-  First, --contact your Host-- and request the memory change (this should be FREE from your Host).  ' ;
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '-  Then you need to update your wordpress wp_config.php file. See: <a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP">Increasing memory allocated to PHP</a>   ' ;
    
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- *** -- BOTH of these actions must be done, in order for the memory change to be accomplished.  ' ;
            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '<h3> The more plugins that are used, the more server memory is recommended.  These days, 256mb is best!</h3>' ;   
                 
            $admin_notices = '<div id="message" class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
            echo $admin_notices;
          }
        }

      //v1.1.6 begin
      
       global $vtprd_license_options;  //v1.1.6 moved here    
            
      //**********************
      //* php > 5.3.1  REQUIRED
      //**********************         
        //VTPRD_PRO_VERSION only exists if PRO version is installed and active
      if (defined('VTPRD_PRO_VERSION'))  { 
         $php_version = phpversion();
         if ( version_compare( $php_version, '5.3.1', '<' ) ) {	            	 
            $message  =  '<h4>' . __('- ' , 'vtprd') .VTPRD_PRO_PLUGIN_NAME. __(' - PHP version must be &nbsp;==>&nbsp; 5.3.1 &nbsp;<==&nbsp; or greater, to run this PRO plugin successfully. ' , 'vtprd') . '</h4>' ;
            $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Your PHP version is currently &nbsp;' .  $php_version ;            
            $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- Contact your host to upgrade!!!.  ' ;                 
            $admin_notices = '<div id="message" class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
            echo $admin_notices;
         }

/*
        if (!$vtprd_license_options['older_wordpress_warning_done']) {
           global $wp_version;
           //if ( version_compare( $wp_version, '4.5', '<' ) ) { 
           // if ( version_compare( $wp_version, VTPRD_WP_MINIMUM_VERSION_FOR_COMPARISON, '<' ) ) {     
           if ( version_compare( $wp_version, VTPRD_WP_MINIMUM_VERSION_FOR_COMPARISON, '<' ) ) {	            	 
              $message  =  '<h4> ' .VTPRD_PRO_PLUGIN_NAME . '</h4>';
              $message .=  '<h4> - WORDPRESS VERSION WARNING - </h4>' ;
              $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'The recommended Wordpress Version to run this PRO plugin successfully = <strong>Version &nbsp;' .VTPRD_WP_MINIMUM_VERSION_FOR_COMPARISON . '</strong>';  
              $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Your WordPress Version is <strong> currently &nbsp;' .  $wp_version .'</strong>';            
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- If Possible, Please ' ; 
              $message .=  '&nbsp;<strong> <a href="'.$vtprd_license_options['home_url'].'/wp-admin/update-core.php">Upgrade Wordpress Version</a> </strong>&nbsp;' ;
              $admin_notices = '<div class="error fade notice is-dismissible" 
              style="
                    line-height: 19px;
                    padding: 0px 15px 11px 15px;
                    font-size: 14px;
                    text-align: left;
                    margin: 25px 20px 15px 2px;
                    background-color: #fff;
                    border-left: 4px solid #ffba00;
                    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';       
              echo $admin_notices; 
           }
          $vtprd_license_options['older_wordpress_warning_done'] = true;
          update_option('vtprd_license_options', $vtprd_license_options);            
        }
*/ 
      }
            
      //v1.1.6 end
      
      				  
     //v1.1.6.3
      // Check if WooCommerce is active
      if ( class_exists( 'WooCommerce' ) )  {
        //********************************
        //* WOOCOMMERCE 2.4+ now REQUIRED
        //********************************      
        $current_version =  WOOCOMMERCE_VERSION;
        if( (version_compare(strval('2.4.0'), strval($current_version), '>') == 1) ) {   //'==1' = 2nd value is lower
          $message  =  '<h4>' . __('- Current version of - ' , 'vtprd') .VTPRD_PLUGIN_NAME. __(' - needs &nbsp;&nbsp; -- WooCommerce Version 2.4+ -- &nbsp;&nbsp; to run successfully. ' , 'vtprd') . '</h4>' ;
          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Please upgrade to WooCommerce Version 2.4+  ' ;    
          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ' - OR - ' ;
          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Please contact Varktech for an earlier version of Pricing Deals, if you are still on 2.3+' ; 
          $message .=  '<br>&nbsp;<strong> <a href="https://www.varktech.com/support/">Varktech Support</a> </strong>&nbsp;' ;   
          $admin_notices = '<div id="message" class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
          echo $admin_notices;
        } 
      }
/* v1.1.6.1     LOCALHOST TEST TEMPORARILY SUSPENDED 
********************************************************  
      //localhost test
      if (!$vtprd_license_options['localhost_warning_done']) { 
        $this_is_localhost = $this->vtprd_maybe_ip_is_localhost(); //v1.1.6.1
        if ($this_is_localhost) { //v1.1.6.1

              //VTPRD_PRO_VERSION only exists if PRO version is installed and active
            if (defined('VTPRD_PRO_VERSION')) {
              $message .=  '<br><br><strong>' . 'The PRO Plugin:' . ' &nbsp;&nbsp;</strong><em>'  .VTPRD_PRO_PLUGIN_NAME . '</em>&nbsp;&nbsp;<strong>' . '  may not be fully functional in a Localhost environment' .'</strong>' ;               
              $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('For testing, best to use a hosted test environment.', 'vtprd')  ;
              $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('A valid test environment must be a subdomain of the production environment,', 'vtprd')  ;
              $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __("and contain 'demo.' or 'beta.' or 'test.' or 'stage.' or 'staging.' in the name [eg test.prodwebsitename.com].", 'vtprd')  ;
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('If you really want to use Localhost, you must register using "prod" or "3-day".', 'vtprd')  ;
              $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('**be sure** to deactivate the Localhost license before registering it on a host server."', 'vtprd')  ;
              $message .=  '</strong>';      
            } else {         
              $message  =  '<h3>' . VTPRD_PLUGIN_NAME. __(' -  may not be fully functional in a Localhost environment' , 'vtprd') . '</h3>' ; 
              $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . 'Suggest creating a server development environment for ongoing development and testing.' ; 
            }
            $admin_notices = '<div class="error fade notice is-dismissible" 
            style="
                  line-height: 19px;
                  padding: 0px 15px 11px 15px;
                  font-size: 14px;
                  text-align: left;
                  margin: 25px 20px 15px 2px;
                  background-color: #fff;
                  border-left: 4px solid #ffba00;
                  -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                  box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';       
            echo $admin_notices; 

          
        } 
        //only ever show this once!    
        $vtprd_license_options['localhost_warning_done'] = true;
        update_option('vtprd_license_options', $vtprd_license_options);               
      }  
*/


                   
  /*
      //********************************
      //* IF WPML is installed - ERROR!!!
      //********************************
      // function check from https://wpml.org/documentation/support/creating-multilingual-wordpress-themes/language-dependent-ids/
      if ( function_exists('icl_object_id') ) { //WPML IS INSTALLED AND ACTIVE
        $message  =   __('- Pricing Deals - is not fully compatible with the &nbsp;  <strong>WPML</strong>  &nbsp; translation plugin. &nbsp; Pricing Deals is fully compatible with the &nbsp; <a href="https://wordpress.org/plugins/qtranslate-x/">QTranslate</a>  &nbsp; plugin ' , 'vtprd')  ;

        $admin_notices = '<div id="message" class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        echo $admin_notices;
      }      
  */          
           
      //********************************
      //* IF User Role Editor is installed - ERROR!!!
      //********************************
      /*v1.1.6  REMOVED!
      if (!$vtprd_license_options['user_role_editor_warning_done']) { //v1.1.6
        if (class_exists('URE_Assign_Role')) {
          $message  =   __('- ' , 'vtprd') .VTPRD_PLUGIN_NAME. __(' - is ** not compatible ** with the &nbsp;  <strong>User Role Editor</strong>  &nbsp; plugin. &nbsp; Pricing Deals is compatible with the &nbsp; <a href="https://wordpress.org/plugins/members/">Members</a>  &nbsp; plugin.' , 'vtprd')  ;
          
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- Recently, a change in the User Role Editor plugin has "poisoned" the roles created with that plugin ' ;
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- All of the Roles created with the User Role Editor must be ** replaced **' ;
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '- And the new Roles must be updated in any Users and Pricing Deals Rules where the "poisoned" roles had been employed.' ;
                  
          $admin_notices = '<div class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
          echo $admin_notices;
          
          $vtprd_license_options['user_role_editor_warning_done'] = true; //v1.1.6
          update_option('vtprd_license_options', $vtprd_license_options); //v1.1.6            
        }
       } 
       */
       
/*  v1.1.6.1      IP TEST TEMPORARILY SUSPENDED
********************************************************
      //v1.1.6 begin  reworked
      global $vtprd_info;
      $localhost_found = get_option('vtprd_localhost_found');
      $ip_address_override = apply_filters('vtprd_override_with_supplied_ip_address',FALSE); //v1.1.6.1
      //VALIDATE IP ADDRESS for PRO PLUGIN REGISTRATION
        //VTPRD_PRO_VERSION only exists if PRO version is installed and active
      if ( (defined('VTPRD_PRO_VERSION')) &&
           (!$ip_address_override) && //don't show if ip address overridden!   //v1.1.6.1
           (!$localhost_found) &&
           (!$this->vtprd_maybe_ip_valid()) ) {
          $message .=  '<strong>' . 'The PRO Plugin:' . ' &nbsp;&nbsp;</strong><em>'  .VTPRD_PRO_PLUGIN_NAME . '</em>&nbsp;&nbsp;<strong>'  ;              
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ' cannot get a valid website IP address'  ;
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ' for your website &nbsp;&nbsp;' .$vtprd_license_options['url'] .'</strong>'  ;

          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . __('- This issue DOES NOT prevent registration -', 'vtprd') ;
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- This issue DOES NOT affect the Pro plugin function - ' , 'vtprd')  ;
          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>' . __(' * But This issue will cause problems down the road *', 'vtprd')  .'</em></strong>' ;
          
          $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Please contact &nbsp;&nbsp; 
                <strong><a target="_blank" href="https://www.varktech.com/support/">Varktech Support</a></strong>
                &nbsp;&nbsp; for assistance with your IP Address.', 'vtprd')  ;
          $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Please be sure to include this message.', 'vtprd')  ;
          
                          
          $admin_notices = '<div class="error fade notice is-dismissible" 
          style="
                line-height: 19px;
                padding: 0px 15px 11px 15px;
                font-size: 14px;
                text-align: left;
                margin: 25px 20px 15px 2px;
                background-color: #fff;
                border-left: 4px solid #ffba00;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';       
          echo $admin_notices;            
      } 
      //v1.1.6 end 
*/      
      
      //display any system-level licensing issues
      $this->vtprd_maybe_pro_license_error();  
           
      return;    
  }  
  
        
  /* ************************************************
  **   Admin - v1.1.6 new function
  *************************************************** */ 
	public function vtprd_maybe_ip_valid() {
  

      global $vtprd_info;
      
      if (!filter_var($vtprd_info['purchaser_ip_address'], FILTER_VALIDATE_IP)) {
     
        return FALSE;
      }
      $ip_exploded = explode('.',$vtprd_info['purchaser_ip_address']);
     
      if ( ($ip_exploded[0] == 127) ||  //no 127.x.x.x
          (!is_numeric($ip_exploded[0]) ) ) {  //must be numeric!!
        
        return FALSE;
      }
     
    return true;  
 
  }
        
  /* ************************************************
  **   Admin - v1.1.6.1 new function
  *************************************************** */ 
	public function vtprd_maybe_ip_is_localhost() {
  

      //********************************
      //* Localhost Discouraged!
      /*
      list of localhost string searches:
      localhost
      127.0. leading nodes
      192.168 leading nodes
      172.16. TO 172.31
      10.
      local
      ip address NOT NUMERIC

      */
      //********************************   
      
      if ( (stristr( network_site_url( '/' ), 'localhost' ) !== false ) ||
  		     (stristr( network_site_url( '/' ), ':8888'     ) !== false ) ) {   // This is common with MAMP on OS X   
          return true;
      }   
 
      global $vtprd_info;
      
      if (strpos($vtprd_info['purchaser_ip_address'], 'local') !== false ) {
          return true;      
      }  
      
      $ip_address_parts =  explode('.',$vtprd_info['purchaser_ip_address']); 
      
      if ( ($ip_address_parts[0] = '127') &&
           ($ip_address_parts[1] = '0') ) {
          return true;            
      }
       
      if ( ($ip_address_parts[0] = '192') &&
           ($ip_address_parts[1] = '168') ) {
          return true;            
      }   
       
      if ( ($ip_address_parts[0] = '172') &&
           ($ip_address_parts[1] >= '16') &&
           ($ip_address_parts[1] <= '31')  ) {
          return true;            
      } 
      
      
      if ($ip_address_parts[0] = '10') {
          return true;            
      } 
      
      //valid IP address  must have 3 periods!
      if ( (substr_count($vtprd_info['purchaser_ip_address'], '.')) != 3) {
          return true;        
      }    
            
    return false;  
 
  }


   //*************************
   //v1.1.5 new function
   //*************************   
   /*
   If plugin activated
    unregistered - Yellow box rego msg on all pages - mention that PRO will not work until registered - handles 1st time through
    suspended - fatal msg everywhere
    other stuff  - msg on plugins page and plugin pages - mention that PRO will not work until registered
   If plugin deactivated
    unregistered - none
    suspended - fatal msg everywhere
    other stuff  - none  
   */
   
	public function vtprd_maybe_pro_license_error() {
     //if PRO is ACTIVE or even INSTALLED, do messaging.
  //    //error_log( print_r(  'Begin vtprd_maybe_pro_license_error', true ) );
    
    global $vtprd_license_options;
    
    //if deactivated, warn that PRO will NOT function!!
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if ( (defined('VTPRD_PRO_VERSION')) &&
         ($vtprd_license_options['status'] == 'valid') &&
         ($vtprd_license_options['state']  == 'deactivated') ) {
      $message = '<span style="color:black !important;">
                   &nbsp;&nbsp;&nbsp;<strong> ' . VTPRD_ITEM_NAME .   ' </strong> &nbsp;&nbsp; License is not registered</span>';
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '** the PRO Plugin will not function until Registered** ' ; 
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please go the the ' ;  
      $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">License Page</a> &nbsp;' ; 
      $message .=  ' and REGISTER the PRO License. </strong>' ;  
      $admin_notices = '<div class="error fade notice is-dismissible" 
        style="
              line-height: 19px;
              padding: 0px 15px 11px 15px;
              font-size: 14px;
              text-align: left;
              margin: 25px 20px 15px 2px;
              background-color: #fff;
              border-left: 4px solid #ffba00;
              -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
              box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';  //send yellow box
      echo $admin_notices;  
      return;  
    }

    
    
    
    if ($vtprd_license_options['status'] == 'valid') {
        return;
    }  
        
    $pageURL = $_SERVER["REQUEST_URI"];

    //License page messaging handled in license-options.php, so EXIT!
    if (strpos($pageURL,'vtprd_license_options_page') !== false ) {    
      return;
    }
    
    $pro_plugin_installed = false;
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) { 
      
      //PRO IS INSTALLED and ACTIVE, show these msgs on ALL PAGES       
      if ($vtprd_license_options['state'] == 'suspended-by-vendor') { 
        $this->vtprd_pro_suspended_msg();            
        return;   
      }    
      if ($vtprd_license_options['status'] == 'unregistered')  { 
        $this->vtprd_pro_unregistered_msg();            
        return;
      }   
                   
      $pro_plugin_installed = true; //show other error msgs
    }
    
    
    if (!$pro_plugin_installed) {       
      $pro_plugin_installed = vtprd_check_pro_plugin_installed();
    }
     
    //if pro not in system, no further msgs
    if (!$pro_plugin_installed) {   
      return;
    }
    
    //IF PRO at least installed, show this on ALL pages (except license page)
    if ($vtprd_license_options['state'] == 'suspended-by-vendor') { 
      $this->vtprd_pro_suspended_msg(); 
      return;     
    } 
    
    //show other msgs for Plugins Page and vtprd pages 
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if ( (defined('VTPRD_PRO_VERSION')) 
          &&
         ($vtprd_license_options['state'] == 'pending') ) {
      //ACTIVE PRO Plugin and we are on the plugins page or a vtprd page

      //v1.1.6.3  do NOT send this message with version_status error situation...
      if ( ($vtprd_license_options['pro_plugin_version_status'] == 'Pro Version Error') ||
           ($vtprd_license_options['pro_plugin_version_status'] == 'Free Version Error') ) {
        $dont_send_msg = true;
      } else {
        //OTHER MESSAGES, showing on vtprd Pages and PLUGINS.PHP
        $message = '<span style="color:black !important;">
                     &nbsp;&nbsp;&nbsp;<strong> ' . VTPRD_ITEM_NAME .   ' </strong> has NOT been successfully REGISTERED, and **will not function until registered**. </span><br><br>';
        $message .= '&nbsp;&nbsp;&nbsp; Licensing Error Message: <em>' . $vtprd_license_options['msg'] . '</em>';
        $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please go the the ' ;  
        $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">License Page</a> &nbsp;' ; 
        $message .=  ' for more information. </strong>' ;  
        $admin_notices = '<div class="error fade notice is-dismissible" 
          style="
                line-height: 19px;
                padding: 0px 15px 11px 15px;
                font-size: 14px;
                text-align: left;
                margin: 25px 20px 15px 2px;
                background-color: #fff;
                border-left: 4px solid #ffba00;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';  //send yellow box
        echo $admin_notices;        
        return;      
      }
    }        
      
    //show other msgs for Plugins Page and vtprd pages 
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if ( (defined('VTPRD_PRO_VERSION')) 
          &&
       ( (strpos($pageURL,'plugins.php') !== false ) || 
         (strpos($pageURL,'vtprd')       !== false ) ) ) {
      //ACTIVE PRO Plugin and we are on the plugins page or a vtprd page
      
            //v1.1.6.3  do NOT send this message with version_status error situation...
      if ( ($vtprd_license_options['pro_plugin_version_status'] == 'Pro Version Error') ||
           ($vtprd_license_options['pro_plugin_version_status'] == 'Free Version Error') ) {
        $dont_send_msg = true;
      } else {

        //OTHER MESSAGES, showing on vtprd Pages and PLUGINS.PHP
        $message = '<span style="color:black !important;">
                     &nbsp;&nbsp;&nbsp;<strong> ' . VTPRD_ITEM_NAME .   ' </strong> has NOT been successfully REGISTERED, and **will not function until registered**. </span><br><br>';
        $message .= '&nbsp;&nbsp;&nbsp; Licensing Error Message: <em>' . $vtprd_license_options['msg'] . '</em>';
        $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please go the the ' ;  
        $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">License Page</a> &nbsp;' ; 
        $message .=  ' for more information. </strong>' ;  
        $admin_notices = '<div class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
        echo $admin_notices; 
      }
    }        
    
  return;  
      
/*        
    $current_page = '';
    $pos = strpos($pageURL,'plugins.php');
    if ($pos !== false) { 
      $current_page = 'wp-plugins-page';
    } else {
      $pos = strpos($pageURL,'vtprd');
      if ($pos !== false) { 
        $current_page = 'my-plugin-page';
      } else {
        //$current_page = 'other-page';
        //IF on OTHER PAGE, non-urgent msgs do not display, so....
        return;
      }   
    }     
*/
    //$vtprd_license_options = get_option( 'vtprd_license_options' );
/*    
    if ( (strpos($pageURL,'plugins.php') !== false ) || 
         (strpos($pageURL,'vtprd')       !== false ) ) {
      //we are on the plugins page or a vtprd page
      $carry_on = true;     
    } else {
      return;
    }  
    
    if ($vtprd_license_options['status'] == 'unregistered')  { 
      $this->vtprd_pro_unregistered_msg();    
      return;
    } 
    
    //OTHER MESSAGES, showing on vtprd Pages and PLUGINS.PHP
    $message = '<span style="color:black !important;">
                 &nbsp;&nbsp;&nbsp;<strong> ' . VTPRD_ITEM_NAME .   ' </strong>is NOT REGISTERED. </span><br><br>';
    $message .= '&nbsp;&nbsp;&nbsp; Licensing Error Message: ' . $vtprd_license_options['msg'];

    if ($vtprd_license_options['state'] == 'suspended-by-vendor') {        
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . '* ' .VTPRD_PRO_PLUGIN_NAME. ' HAS BEEN DEACTIVATED.' ;
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please go the the ' ;  
      $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">License Page</a> &nbsp;' ; 
      $message .=  ' for more information. </strong>' ;  
        
    } else {
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please ' ;  
      $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Register Pro License</a></strong> ' ;     
    }
    
       
    $admin_notices = '<div class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
    echo $admin_notices; 
*/           

  } 
  
  //********************************
  //   Admin - v1.1.5 new function
  //********************************
	public function vtprd_pro_unregistered_msg() { 
    //plugin version mismatch takes precedence over registration message.
    global $vtprd_license_options;
    if ( ($vtprd_license_options['pro_plugin_version_status'] == 'valid') ||
         ($vtprd_license_options['pro_plugin_version_status'] == null)) { //null = default
      $carry_on = true;
    } else { 
      return;
    }
    
    
    $message  = '<h2>' .VTPRD_PRO_PLUGIN_NAME . '</h2>';
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active    
    if (VTPRD_PRO_VERSION == VTPRD_PRO_LAST_PRELICENSE_VERSION) {
      $message .=   '<strong>' . __(' - We have introduced Plugin Registration,' , 'vtprd')  ; 
      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('Please take a moment to ', 'vtprd')  ;
        $message .=  '<a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">register</a>' ; 
        $message .=   __(' your plugin.', 'vtprd')  ;
      $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('You may use your original purchase <em>SessionID</em> as your registration key.', 'vtprd')  ;
      
      $message .=  '<h3 style="color:grey !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>' . __(' Your PRO plugin will not function until registered', 'vtprd')  . '</em>' . '</h3>' ;    
    } else {
     // $message .= '<span style="background-color: RGB(255, 255, 180) !important;"> ';
      $message .=   '<strong>' . __(' - Requires valid ,' , 'vtprd')  ;
      $message .=  '<a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Registration</a>' ; 
      $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>' . __(' and will not function until registered -', 'vtprd')  . '</em><br><br>' ; //. '</span>' ;        
    }
                             
    $message .=  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Register Pro License</a></strong> ' ; 
        
/*
    $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- Registration can be done using both a License Key ', 'vtprd') ;
    $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- OR, if an older purchase, with the SessionID.', 'vtprd') ;
    $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- If you do not have either ID, Go to <a href="https://www.varktech.com">Varktech.com</a>', 'vtprd') ;
    $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- Log In and get your License Key to Register.', 'vtprd') ;
    $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . __('- OR for older purchases, <em>where a SessionID was furnished</em>,', 'vtprd') ; 
    $message .=  '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'     . __('- by Name and Email Address', 'vtprd') .'&nbsp;&nbsp;&nbsp; <a href="http://www.varktech.com/your-account/license-lookup/">License Key Lookup by Name and Email</a>' ;                     
*/     
     //yellow line box override      
    $admin_notices = '<div class="error fade notice is-dismissible" 
      style="
            line-height: 19px;
            padding: 0px 15px 11px 15px;
            font-size: 14px;
            text-align: left;
            margin: 25px 20px 15px 2px;
            background-color: #fff;
            border-left: 4px solid #ffba00;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1); " > <p>' . $message . ' </p></div>';
    echo $admin_notices;  
    return;
  } 
   
  //   Admin - v1.1.5 new function
	public function vtprd_pro_suspended_msg() { 
    global $vtprd_license_options;
    $message = '<span style="color:black !important;">
                 &nbsp;&nbsp;&nbsp;<strong> ' . VTPRD_PRO_PLUGIN_NAME .   ' </strong>
                 <span style="background-color: RGB(255, 255, 180) !important;">LICENSE HAS BEEN SUSPENDED. </span>
                 </span><br><br>';
    $message .= '&nbsp;&nbsp;&nbsp; Licensing Error Message: <em>' . $vtprd_license_options['msg'] . '</em>';           
    $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>' . '* ' .VTPRD_PRO_PLUGIN_NAME. ' HAS BEEN DEACTIVATED.' ;
    $message .=  '<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . '* Please go to your ' ;  
    $message .=  '&nbsp; <a href="'.$vtprd_license_options['home_url'].'/wp-admin/edit.php?post_type=vtprd-rule&page=vtprd_license_options_page">Register Pro License</a> &nbsp;' ; 
    $message .=  ' page for more information. </strong>' ;  
              
    $message .=  "<span style='color:grey !important;'><br><br><em>&nbsp;&nbsp;&nbsp; (This message displays when the Pro version is installed, regardless of whether it's active)</em></span>" ;
    
    $admin_notices = '<div class="error fade notice is-dismissible" style="background-color: #FFEBE8 !important;"><p>' . $message . ' </p></div>';
    echo $admin_notices;
    
    //double check PRO deactivate
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) {  
      vtprd_deactivate_pro_plugin();
    }
       
    return;
  } 
   
/*
	$message_code = array(
		    //good message codes

		    //non-punitive error message codes (they get another try) 
		'license-key-not-supplied',
    'email-not-supplied',  //allow 3 extra attempts (logged) in 24 hours then shut it down
    'email-invalid-format',  //allow 3 extra attempts (logged) in 24 hours then shut it down
    'prod_url_not_supplied_for_test_registration',  //allow 3 extra attempts (logged) in 24 hours then shut it down
    
    'license-key-prod-already-registered',  //allow 2 extra attempts (logged) in 24 hours then shut it down
		'license-key-mismatch-email',  		      //allow 2 extra attempts (logged) in 24 hours then shut it down

		    //punitive error message codes
		'license-key-disabled-too-many',  //allow 2 extra attempts (logged) then shut it down
    'test_url_site_node_missing',  //must have '.test.' or '.demo.' or '.stage.' as a naming node
    'test_url_site_name_not_prod_subdomain',  //test site name must be a Prod name subdomain (last 2 name nodes must match)

	)

allow unlimited .test. or .demo. or .stage. subdomain sites, as long as last 2 nodes match the production registered site's last 2 nodes

*/  

        
  /* ************************************************
  **   Admin - v1.1.5 new function
  *    v1.1.6 refactored  
  *************************************************** */ 
	public function vtprd_admin_init_overhead() {
     global $vtprd_license_options, $vtprd_setup_options;
     
     //v1.1.7.2 grpB begin
     /*
      require_once for BACKBONE duplicated here - 
      admin_init runs BEFORE init and we were getting 
     "map_meta_cap was called incorrectly. The post type vtprd-rule is not registered"
       as vtprd-rule's capability was being tested before it was defined!!
     */
     require_once  ( VTPRD_DIRNAME . '/core/vtprd-backbone.php' ); 
     //v1.1.7.2 end
     
     if (!$vtprd_license_options) {
        $vtprd_license_options = get_option( 'vtprd_license_options' ); 
     }     

  
    //    //error_log( print_r(  'BEGIN vtprd_admin_init_overhead, current_pro_version= ' .$vtprd_setup_options['current_pro_version'] , true ) ); 
    
    $this->vtprd_maybe_udpate_version_num(); //v1.1.6.3
  
    //    //error_log( print_r(  'AFTER vtprd_maybe_udpate_version_num, current_pro_version= ' .$vtprd_setup_options['current_pro_version'] , true ) ); 
  
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) { //v1.1.6.1
      //$this->vtprd_maybe_rego_clock_action(); //pro only //v1.1.6.3 REMOVED
      $this->vtprd_maybe_pro_deactivate_action(); //pro only
      $this->vtprd_license_count_check(); //pro only
     // vtprd_maybe_force_plugin_updates_check();  //v1.1.6 removed!!  button now resides on settings page and license page, both have separate handlers
      vtprd_maybe_delete_pro_plugin_action(); //pro only
      
      //vtprd_maybe_admin_recheck_license_activation(); //v1.1.6  fallback to cron job //pro only
      vtprd_recheck_license_activation(); //v1.1.6.3  fallback to cron job //pro only
    } 
    
    $this->vtprd_maybe_version_mismatch_action();

    //v1.1.7.2 begin
    /*
    if (VTPRD_VERSION == '1.1.7.2') {
      global $pagenow;
      if ( 'plugins.php' === $pagenow ) {
        add_action( 'in_plugin_update_message-'.VTPRD_PLUGIN_SLUG, array(&$this, 'vtprd_plugin_update_notice' );
      }
    }
    */
    //v1.1.7.2 end
  }
   
        
  /* ************************************************
  **   Admin - v1.1.7.2 new function   
  *************************************************** */ 
	public function vtprd_vtprd_plugin_update_notice() { 
  	$info = '<br><strong>Shuts off</strong> Pricing Deals plugin automatic background update capability';
  	echo '<span class="spam">' . strip_tags( $info, '<br><a><b><i><span>' ) . '</span>'; 
  } 
  
        
  /* ************************************************
  **   Admin - v1.1.6.3 new function   
  *************************************************** */ 
	public function vtprd_maybe_udpate_version_num() { 
    global $vtprd_license_options, $vtprd_setup_options;

  //    //error_log( print_r(  'BEGIN vtprd_maybe_udpate_version_num ', true ) );
    
/*  CURRENTLY, this function has to run all the time, to pick up the new 
    //vtprd_new_version_in_progress ONLY created if plugin_updater has found one.
    //this function updates the current version ONLY after an UPDATED install is complete.
    if (get_option('vtprd_new_version_in_progress') !== false) {
           //error_log( print_r(  'vtprd_new_version OPTION = ' .get_option('vtprd_new_version'), true ) );  
       $carry_on = true;  
    } else {
      return;
    }
 */   
    
    if (defined('VTPRD_PRO_VERSION')) { 
      if( (isset($vtprd_setup_options['current_pro_version'])) &&
          ($vtprd_setup_options['current_pro_version'] == VTPRD_PRO_VERSION) ) {
   //    //error_log( print_r(  'vtprd_maybe_udpate_version_num, current_pro_version001 = ' .$vtprd_setup_options['current_pro_version'], true ) );
        $carry_on = true;
      } else {
        $vtprd_setup_options['current_pro_version'] = VTPRD_PRO_VERSION; 
        update_option( 'vtprd_setup_options',$vtprd_setup_options ); 
   //    //error_log( print_r(  'vtprd_maybe_udpate_version_num, current_pro_version002 = ' .$vtprd_setup_options['current_pro_version'], true ) );
   
        delete_option('vtprd_new_version_in_progress');    
      }
    } else {

      $pro_plugin_installed = vtprd_check_pro_plugin_installed();
      
      //verify if version number, from http://stackoverflow.com/questions/28903203/test-if-string-given-is-a-version-number
      if( version_compare( $pro_plugin_installed, '0.0.1', '>=' ) >= 0 ) {
        if ( (isset($vtprd_setup_options['current_pro_version'])) &&
            ($vtprd_setup_options['current_pro_version'] == $pro_plugin_installed) ) {
   //    //error_log( print_r(  'vtprd_maybe_udpate_version_num, current_pro_version003 = ' .$vtprd_setup_options['current_pro_version'], true ) );            
          $carry_on = true;
        } else {
          $vtprd_setup_options['current_pro_version'] = $pro_plugin_installed; 
   //    //error_log( print_r(  'vtprd_maybe_udpate_version_num, current_pro_version004 = ' .$vtprd_setup_options['current_pro_version'], true ) );           
          update_option( 'vtprd_setup_options',$vtprd_setup_options );
          delete_option('vtprd_new_version_in_progress');  
        }  
       }   
    }
 
     //    //error_log( print_r(  'vtprd_maybe_udpate_version_num, $vtprd_setup_options =', true ) );
        //    //error_log( var_export($vtprd_setup_options, true ) ); 
      //    //error_log( print_r(  '$pro_plugin_installed = ' .$pro_plugin_installed, true ) );   
    return;
  }  
  
        
  /* ************************************************
  **   Admin - v1.1.5 new function
  *   //only runs if PRO version is installed and active  
  *   ***********************  
  *   v1.1.6.1 REFACTORED  
  *   ***********************    
  *************************************************** */ 
	public function vtprd_maybe_rego_clock_action() {
  
    //Client has one week to register successfully!
    
    global $vtprd_license_options;
    
    //only EVER do this once, with VERY FIRST registration!!!!!!!!!!!!!
    if ( (isset($vtprd_license_options['rego_done'])) &&
         ($vtprd_license_options['rego_done'] == 'yes') ) {
      return;
    }     

    //if all good, get rid of rego_clock and exit
    if ( ($vtprd_license_options['status'] == 'valid') &&
         ($vtprd_license_options['pro_plugin_version_status'] == 'valid') ) { //deactivated status ok
      if (get_option('vtprd_rego_clock')) {
        delete_option('vtprd_rego_clock');      
      }
      $vtprd_license_options['rego_done'] = 'yes';
      update_option('vtprd_license_options', $vtprd_license_options); 
      return;
    }
    
           
    //if alrady toast, exit stage left
    if ( ($vtprd_license_options['pro_deactivate'] == 'yes') ||
         ($vtprd_license_options['state'] == 'suspended-by-vendor') ||
         ($vtprd_license_options['state'] == 'deactivated') ||  //allow deactivated through, as a 'resting' state
         (($vtprd_license_options['pro_plugin_version_status'] > null) &&
          ($vtprd_license_options['pro_plugin_version_status'] != 'valid'))  )  { //if 'pro_plugin_version_status' = null, this is unregistered, carry on...
      return;
    }


    //if License or Plugins Page in progress, exit - user may be activating or otherwise fixing things
    $pageURL = $_SERVER["REQUEST_URI"];
    if ( (strpos($pageURL,'vtprd_license_options_page') !== false ) ||
         (strpos($pageURL,'plugins.php') !== false) ||
         (strpos($pageURL,'admin-ajax.php') !== false) ) {  //wordpress sometimes returns admin-ajax.php IN ERROR, so handle that here
      return;
    }
 
    //if already there, get clock, else create and exit
    if (get_option('vtprd_rego_clock')) {
      $vtprd_rego_clock = get_option('vtprd_rego_clock');
    } else {
      $vtprd_rego_clock = time();
      update_option('vtprd_rego_clock',$vtprd_rego_clock);
      return;
    }
    
    $today = time();
    
//test begin
/*
    $vtprd_rego_clock = 164902187;
    //error_log( print_r(  '$pageURL =  ' .$pageURL , true ) ); 
global $pagenow;
    //error_log( print_r(  '$pagenow =  ' .$pagenow , true ) ); 
*/
//test end
    /* //v1.1.7.2 removed to prevent user problems!!   
    //if registration not resolved in 1 week
    if (($today - $vtprd_rego_clock) > 604800) {
      $vtprd_license_options['pro_deactivate'] = 'yes';
      $vtprd_license_options['msg'] = 'Registration not accomplished within 1 week allotted, PRO plugin suspended';
      $vtprd_license_options['state'] = 'suspended-by-vendor';
      //options update happens in pro_deactivate_action...
    }
    */
    
    return; 
  } 
        
  /* ************************************************
  **   Admin - v1.1.5 new function
  * //only runs if PRO version is installed and active    
  *************************************************** */ 
	public function vtprd_maybe_pro_deactivate_action() {
    global $vtprd_license_options;             
    if ($vtprd_license_options['pro_deactivate'] != 'yes') {
      return;
    }
    
    
    vtprd_deactivate_pro_plugin();
    vtprd_increment_license_count(); 
    $vtprd_license_options['pro_deactivate'] = null;
    update_option('vtprd_license_options', $vtprd_license_options); 
                        
    if ( $vtprd_setup_options['debugging_mode_on'] == 'yes' ){   
           //error_log( print_r(  'PRO deactivated, VTPRD_PRO_DIRNAME not defined ', true ) );
    }
  
    
    return; 
  }  
  
        
  /* ************************************************
  **   Admin - v1.1.5 new function
  *************************************************** */ 
	public function vtprd_maybe_pro_plugin_installed() {
     
    // Check if get_plugins() function exists. This is required on the front end of the
    // site, since it is in a file that is normally only loaded in the admin.
    if ( ! function_exists( 'get_plugins' ) ) {
    	require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    
    $all_plugins = get_plugins();

    foreach ($all_plugins as $key => $data) { 
      if ($key == VTPRD_PRO_PLUGIN_FOLDER.'/'.VTPRD_PRO_PLUGIN_FILE) {    
        return true;      
      } 
    } 
    
    return false;  
 
  }



  /* ************************************************
  **   v1.1.5 new function, run at plugin init
  * ONLY RUN IF PRO VERSION IS installed
  * However, the PRO version may have been deactivated
  * when this runs, so no test is applied directly     
  *************************************************** */ 
	public function vtprd_init_update_license() {
    global $vtprd_license_options;
    
    //v1.1.6 Begin
    //don't run if license_options.php has NEVER RUN!
    if( get_option( 'vtprd_license_options' ) !== FALSE ) {
      $carry_on = true;  
    } else {
      return;
    }
    //v1.1.6 end
    
 //    //error_log( print_r(  'BEGIN vtprd_init_update_license, global $vtprd_license_options=' , true ) );   

    /* vtprd_license_suspended / vtprd_license_checked
    is only created during the plugin updater execution

    However, you can't update the options table consistently, so this is done instead. 
    If the call to the home server produces a status change, it's updated here.
      ( Can't update vtprd_license_options in the plugin updater, things explode!! )
    */
    if (get_option('vtprd_license_suspended')) {
      $vtprd_license_options2 = get_option('vtprd_license_suspended');
      $vtprd_license_options['status']  = $vtprd_license_options2['status'];
      $vtprd_license_options['state']   = $vtprd_license_options2['state'];
      $vtprd_license_options['strikes'] = $vtprd_license_options2['strikes'];
      $vtprd_license_options['diagnostic_msg'] = $vtprd_license_options2['diagnostic_msg'];
      $vtprd_license_options['last_failed_rego_ts']        = $vtprd_license_options2['last_failed_rego_ts']; 
      $vtprd_license_options['last_failed_rego_date_time'] = $vtprd_license_options2['last_failed_rego_date_time']; 
      $vtprd_license_options['last_response_from_host'] = $vtprd_license_options2['last_response_from_host']; //v1.1.6
      $vtprd_license_options['msg'] = $vtprd_license_options2['msg']; //v1.1.6
      //v1.1.6 begin
      //moved here from PHONE HOME, as the cron job timing can't check is_installed!
      if ($license_data->state == 'suspended-by-vendor') {   
        vtprd_deactivate_pro_plugin();
      }
      //v1.1.6 end
      //update status change
      update_option('vtprd_license_options', $vtprd_license_options);
 //    //error_log( print_r(  'UPDATED FROM  vtprd_license_suspended', true ) );  
      //cleanup
      delete_option('vtprd_license_suspended'); 
      return;   //if suspneded, no further processing.        
    }
     
    if (get_option('vtprd_license_checked')) {
      $vtprd_license_options2 = get_option('vtprd_license_checked');
      $vtprd_license_options['last_successful_rego_ts']        = $vtprd_license_options2['last_successful_rego_ts']; 
      $vtprd_license_options['last_successful_rego_date_time'] = $vtprd_license_options2['last_successful_rego_date_time'];  
      //update ts change
      update_option('vtprd_license_options', $vtprd_license_options);
          
      //cleanup
      delete_option('vtprd_license_checked');            
    }  

    
    
    //check for PRO VERSION MISMATCH, comparing from Either side
    //$vtprd_license_options['pro_version'] only has a value if pro version has ever been installed.
    //on Pro uninstall clear out these values, so that if plugin uninstalled, values and accompanying error messages don't display!
    if (is_admin()) {
       
      /* vtprd_pro_plugin_deleted 
      is only created if the pro plugin is deleted by the admin.
      However, you can't update the options table consistently, so this is done instead. 
      If the call to the home server produces a status change, it's updated here.
        ( Can't update vtprd_license_options in the plugin updater, things explode!! )
      */     
      if (get_option('vtprd_pro_plugin_deleted')) {
        $vtprd_license_options['pro_version'] = null;      
        $vtprd_license_options['pro_plugin_version_status'] = null;
        $vtprd_license_options['pro_minimum_free_version'] = null; 
        update_option('vtprd_license_options', $vtprd_license_options);
  
        //cleanup
        delete_option('vtprd_pro_plugin_deleted');            
      }   
              
      $this->vtprd_pro_version_verify(); //v1.1.6.3 refactored into new function

      //v1.1.6.1 begin
      //conversion to storing home_url, used in anchors ...
      if ( (!isset($vtprd_license_options['home_url'])) ||
           ($vtprd_license_options['home_url'] == null) ) {
         $vtprd_license_options['home_url'] = home_url();
         update_option('vtprd_license_options', $vtprd_license_options);   
      }
      //v1.1.6.1 end
      
    }  
        
    return;   
  }

  /* ************************************************
  **   Admin - v1.1.6.3 new function, run at admin init  
  *************************************************** */ 
	public function vtprd_pro_version_verify() {
    global $vtprd_license_options;               
    
      //EDIT only if PRO plugin installed or active
      if (defined('VTPRD_PRO_VERSION')) {
        $carry_on = true;
      } else {
        $pro_plugin_is_installed = vtprd_check_pro_plugin_installed();
        if ($pro_plugin_is_installed !== false) {
           $vtprd_license_options['pro_version'] = $pro_plugin_is_installed;
        } else {
          //PRO is not installed, nothing to do!
          return;
        }    
      }
    
    //    //error_log( print_r(  'vtprd_pro_version_verify 001' , true ) ); 
      //PICK up any defined values from active PRO.  If inactive, the license_options value will have previously-loaded values
      //if ((defined('VTPRD_PRO_DIRNAME')) )   { //changed to PRO_VERSION because PRO_DIRNAME is now controlled in THIS file 
      if (defined('VTPRD_PRO_VERSION')) {
      
    //    //error_log( print_r(  'vtprd_pro_version_verify 002' , true ) ); 
        if ( ($vtprd_license_options['pro_version'] == VTPRD_PRO_VERSION) &&
             ($vtprd_license_options['pro_minimum_free_version'] == VTPRD_PRO_MINIMUM_REQUIRED_FREE_VERSION) ) {
      
    //    //error_log( print_r(  'vtprd_pro_version_verify 003' , true ) );             
            $carry_on = true;   //v1.1.6.6
        } else {
       
    //    //error_log( print_r(  'vtprd_pro_version_verify 005' , true ) );        
          $vtprd_license_options['pro_version'] = VTPRD_PRO_VERSION;
          $vtprd_license_options['pro_minimum_free_version'] = VTPRD_PRO_MINIMUM_REQUIRED_FREE_VERSION;
          //update_option('vtprd_license_options', $vtprd_license_options);
        }

      } 

      if ($vtprd_license_options['pro_version'] > '') {
       
    //    //error_log( print_r(  'vtprd_pro_version_verify 006' , true ) );      
        if (version_compare($vtprd_license_options['pro_version'], VTPRD_MINIMUM_PRO_VERSION) < 0) {    //'<0' = 1st value is lower 
          
    //    //error_log( print_r(  'vtprd_pro_version_verify 007' , true ) );            
          $vtprd_license_options['pro_plugin_version_status'] = 'Pro Version Error'; 
          //$vtprd_license_options['state']  = 'pending';  //v1.1.6.3 changed from PRO deactivation to status change
          //$vtprd_license_options['status'] = 'invalid';  //v1.1.6.3 changed from PRO deactivation to status change
        } else {
       
          //v1.1.6.7 begin
          // if previously pro version error, this would have been set, to allow a PLUGIN UPDATE.  Update has been completed, so no longer necessary!
          if ($vtprd_license_options['pro_plugin_version_status'] == 'Pro Version Error') {
            delete_option('vtprd_do_pro_plugin_update');  
          }
          //v1.1.6.7 begin
            
    //    //error_log( print_r(  'vtprd_pro_version_verify 008' , true ) );      
          $vtprd_license_options['pro_plugin_version_status'] = 'valid'; 
        }
        
        if ($vtprd_license_options['pro_plugin_version_status'] == 'valid') { 
         
    //    //error_log( print_r(  'vtprd_pro_version_verify 009' , true ) );     
          if  (version_compare(VTPRD_VERSION, $vtprd_license_options['pro_minimum_free_version']) < 0) {    //'<0' = 1st value is lower   
          
    //    //error_log( print_r(  'vtprd_pro_version_verify 010' , true ) );             
            $vtprd_license_options['pro_plugin_version_status'] = 'Free Version Error';
            //$vtprd_license_options['state']  = 'pending';  //v1.1.6.3 changed from PRO deactivation to status change
            //$vtprd_license_options['status'] = 'invalid';  //v1.1.6.3 changed from PRO deactivation to status change            
          } else {
       
    //    //error_log( print_r(  'vtprd_pro_version_verify 011' , true ) );          
            $vtprd_license_options['pro_plugin_version_status'] = 'valid'; 
          }
        } 
//    //error_log( print_r(  'vtprd_pro_version_verify 012' , true ) );                         
        update_option('vtprd_license_options', $vtprd_license_options);
                         
      } 
  //    //error_log( print_r(  'vtprd_pro_version_verify 013' , true ) );     
      return;   
  }


  /* ************************************************
  **   Admin - v1.1.5 new function, run at admin init  
  *************************************************** */ 
	public function vtprd_maybe_version_mismatch_action() {

    //if PRO **not active** but installed, and VERSION ERROR, still do the messaging
    //can only do this AFTER or as part of admin_init
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option('vtprd_license_options');
    }
    
    if (!$vtprd_license_options['pro_version']) {  //'pro_version' only has data when pro plugin INSTALLED
      return;
    }
    
    
    //v1.1.6.3  REFACTORED
    //*********************************************************************
    // PLUGIN MISMATCH is now a Status Warning, NOT a deactivation action
    //*********************************************************************
 
 /*     
    //this status set at plugin startup
    if ($vtprd_license_options['pro_plugin_version_status'] == 'valid') {
      return;
    }
       
  
    //version_status is IN ERROR, so deactivate PRO plugin
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (defined('VTPRD_PRO_VERSION')) {
      vtprd_deactivate_pro_plugin();
    } else {
    
      //v1.1.6.3 begin
      //ONLY show if the plugin is actually INSTALLED!!
      $pro_plugin_is_installed = $this->vtprd_maybe_pro_plugin_installed(); // function pro_plugin_installed must be in the class!!
      if ($pro_plugin_is_installed !== false) {
        $carry_on = true;
      } else {
        return;      
      }
      //v1.1.6.3 end
    }
*/  

   
    if ($vtprd_license_options['pro_plugin_version_status'] == 'Pro Version Error') {
      //*******************
      //v1.1.6.7 refactored
      //ONLY show if the plugin is actually INSTALLED!!
      if (defined('VTPRD_PRO_VERSION')) {
        $pro_plugin_is_installed = TRUE;
      } else {
        $pro_plugin_is_installed = $this->vtprd_maybe_pro_plugin_installed(); // function pro_plugin_installed must be in the class!!
      }     
      if ($pro_plugin_is_installed !== false) {
        add_action( 'admin_notices',    array(&$this, 'vtprd_admin_notice_version_mismatch_pro') ); 
        add_action( 'after_plugin_row', array(&$this, 'vtprd_plugin_notice_version_mismatch_pro' ), 10, 3  );  //v1.1.6.7
         //v1.1.6.7 - plugin updater now runs *only* when a plugin mismatch is detected in the free version - so there must always be paired updates!! 
        update_option('vtprd_do_pro_plugin_update', TRUE);  //v1.1.6.7 ==>> allows pro_plugin_update action!
      //v1.1.6.7 end 
      //******************* 
      }     
    }
    
    if ($vtprd_license_options['pro_plugin_version_status'] == 'Free Version Error') {
      //v1.1.6.3 begin
      //ONLY show if the plugin is actually INSTALLED!!
      $pro_plugin_is_installed = $this->vtprd_maybe_pro_plugin_installed(); // function pro_plugin_installed must be in the class!!
      if ($pro_plugin_is_installed !== false) {      
        add_action( 'admin_notices',array(&$this, 'vtprd_admin_notice_version_mismatch_free') ); 
      }
      //v1.1.6.3 end                 
    } 
         
    return;    
  }  

  
  /* ************************************************
  **   Admin - v1.1.5 new function, run at admin init 
  * //only runs if PRO version is installed and active     
  *************************************************** */ 
	public function vtprd_license_count_check() {

    $vtprd_license_count = get_option( 'vtprd_license_count');
    if (!$vtprd_license_count) {
      return;
    }
    //if PRO **not active** but installed, and VERSION ERROR, still do the messaging
    //can only do this AFTER or as part of admin_init
    global $vtprd_license_options;
    if (!$vtprd_license_options) {
      $vtprd_license_options = get_option('vtprd_license_options');
    }
    
    if ($vtprd_license_options['state'] == 'suspended-by-vendor') {
      return;    
    }
      //VTPRD_PRO_VERSION only exists if PRO version is installed and active
    if (!defined('VTPRD_PRO_VERSION')) {
      return;
    }
   
    //if fatal counts exceed limit, never allow pro plugin to be activated
    if ($vtprd_license_count >= 10 ) { //v1.1.6.7 upgraded from 5 to 10!
      vtprd_deactivate_pro_plugin();
      $vtprd_license_options['state'] = 'suspended-by-vendor';
      $vtprd_license_options['status'] = 'invalid';
      $vtprd_license_options['diagnostic_msg'] = 'suspended until contact with vendor';
      update_option('vtprd_license_options', $vtprd_license_options);
                    
    }
    
    return;    
  }  

    
  /* ************************************************
  **   Admin - **Uninstall** Hook and cleanup
  *************************************************** */ 
	public function vtprd_uninstall_hook() {
  
 //    //error_log( print_r(  'Function begin - vtprd_uninstall_hook', true ) );
      
      if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
      	return;
        //exit ();
      }
  
      delete_option('vtprd_setup_options');
      $vtprd_nuke = new VTPRD_Rule_delete;            
      $vtprd_nuke->vtprd_nuke_all_rules();
      $vtprd_nuke->vtprd_nuke_all_rule_cats();
      
  }
  
   
    //Add Custom Links to PLUGIN page action links                     ///wp-admin/edit.php?post_type=vtmam-rule&page=vtmam_setup_options_page
  public function vtprd_custom_action_links( $links ) { 
     
 //    //error_log( print_r(  'Function begin - vtprd_custom_action_links', true ) );
  
		$plugin_links = array(
			'<a href="' . admin_url( 'edit.php?post_type=vtprd-rule&page=vtprd_setup_options_page' ) . '">' . __( 'Settings', 'vtprd' ) . '</a>',
			'<a href="https://www.varktech.com">' . __( 'Docs', 'vtprd' ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}



	public function vtprd_create_discount_log_tables() {
     
 //    //error_log( print_r(  'Function begin - vtprd_create_discount_log_tables', true ) );
    
    global $wpdb;
    //Cart Audit Trail Tables
  	
    $wpdb->hide_errors();    
  	$collate = '';  
    if ( $wpdb->has_cap( 'collation' ) ) {  //mwn04142014
  		if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
  		if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
    }
     
      
  //  $is_this_purchLog = $wpdb->get_var("SHOW TABLES LIKE `".VTPRD_PURCHASE_LOG."` ");
    $table_name =  VTPRD_PURCHASE_LOG;
    $is_this_purchLog = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" );
    if ( $is_this_purchLog  == VTPRD_PURCHASE_LOG) {
      return;
    }

     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG."` (
              id bigint NOT NULL AUTO_INCREMENT,
              cart_parent_purchase_log_id bigint,
              purchaser_name VARCHAR(50), 
              purchaser_ip_address VARCHAR(50),                
              purchase_date DATE NULL,
              cart_total_discount_currency DECIMAL(11,2),      
              ruleset_object LONGTEXT,  //v1.1.7.2 changed to LONGTEXT
              cart_object LONGTEXT,  //v1.1.7.2 changed to LONGTEXT
          KEY id (id, cart_parent_purchase_log_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG_PRODUCT."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_row_id bigint,
              product_id bigint,
              product_title VARCHAR(100),
              cart_parent_purchase_log_id bigint,
              product_orig_unit_price   DECIMAL(11,2),     
              product_total_discount_units   DECIMAL(11,2),
              product_total_discount_currency DECIMAL(11,2),
              product_total_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_row_id, product_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );
     
    $sql = "
        CREATE TABLE  `".VTPRD_PURCHASE_LOG_PRODUCT_RULE."` (
              id bigint NOT NULL AUTO_INCREMENT,
              purchase_log_product_row_id bigint,
              product_id bigint,
			  rule_id bigint,
              cart_parent_purchase_log_id bigint,
              product_rule_discount_units   DECIMAL(11,2),
              product_rule_discount_dollars DECIMAL(11,2),
              product_rule_discount_percent DECIMAL(11,2),
          KEY id (id, purchase_log_product_row_id, rule_id)
        ) $collate ;      
        ";
 
     $this->vtprd_create_table( $sql );



  }
  
	public function vtprd_create_table( $sql ) {
     
 //    //error_log( print_r(  'Function begin - vtprd_create_table', true ) );
       
      global $wpdb;
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	        
      dbDelta($sql);
      return; 
   } 
                            
                            
 
  //****************************************
  //v1.0.7.4 new function
  //v1.0.8.8 refactored for new 'Wholesale Tax Free' role, buy_tax_free role capability
  //  adds in default 'Wholesale Buyer' + new 'Wholesale Tax Free'  role at iadmin time  
  //v1.0.9.0 moved here from functions.php, so it only executes on insall...
  //****************************************
  Public function vtprd_maybe_add_wholesale_role(){ 
     
   //error_log( print_r(  'Function begin - vtprd_maybe_add_wholesale_role', true ) );
         
		global $wp_roles;
	
		if ( class_exists( 'WP_Roles' ) ) {
      if ( !isset( $wp_roles ) ) { 
			   $wp_roles = new WP_Roles();
      }
    }

		$capabilities = array( 
			'read' => true,
			'edit_posts' => false,
			'delete_posts' => false,
		); 
     
    $wholesale_buyer_role_name    =  __('Wholesale Buyer' , 'vtprd');
    $wholesale_tax_free_role_name =  __('Wholesale Tax Free' , 'vtprd');
  

		if ( is_object( $wp_roles ) ) { 

      If ( !get_role( $wholesale_buyer_role_name ) ) {
    			add_role ('wholesale_buyer', $wholesale_buyer_role_name, $capabilities );    
    			$role = get_role( 'wholesale_buyer' );
          $role->add_cap( 'buy_wholesale' ); 
    			$role->add_cap( 'wholesale' ); //v1.1.0.7
      } else { //v1.1.0.7 begin
    			$role = get_role( 'wholesale_buyer' );
          $role->add_cap( 'wholesale' );     
      }  //v1.1.0.7 end

      If ( !get_role(  $wholesale_tax_free_role_name ) ) {
    			add_role ('wholesale_tax_free',  $wholesale_tax_free_role_name, $capabilities );    
    			$role = get_role( 'wholesale_tax_free' ); 
    			$role->add_cap( 'buy_tax_free' );
          $role->add_cap( 'wholesale' ); //v1.1.0.7
      } else { //v1.1.0.7 begin
    			$role = get_role( 'wholesale_tax_free' ); 
          $role->add_cap( 'wholesale' ); 
      }  //v1.1.0.7 end

		}
       
    return;
  }  


  
} //end class
$vtprd_controller = new VTPRD_Controller;
     
//has to be out here, accessing the plugin instance
if (is_admin()){
  register_activation_hook(__FILE__, array($vtprd_controller, 'vtprd_activation_hook'));
  register_deactivation_hook(__FILE__, array($vtprd_controller, 'vtprd_deactivation_hook')); //v1.1.6.1
//mwn0405
//  register_uninstall_hook (__FILE__, array($vtprd_controller, 'vtprd_uninstall_hook'));
}
