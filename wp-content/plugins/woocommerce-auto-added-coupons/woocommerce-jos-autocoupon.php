<?php
/**
 * Plugin Name: WooCommerce Extended Coupon Features
 * Plugin URI: http://www.soft79.nl
 * Description: Additional functionality for WooCommerce Coupons: Apply certain coupons automatically, allow applying coupons via an url, etc...
 * Version: 2.5.4
 * Author: Soft79
 * License: GPL2
 */
 
if ( ! defined('WJECF_VERSION') ) define ('WJECF_VERSION', '2.5.4');

// Changelog: see readme.txt

/*
 TODO:
 - Apply filter for autocoupon individual_use_filter
 - (PRO) Eval
*/


if ( ! defined('ABSPATH') ) die();
if ( ! function_exists( 'wjecf_load_plugin_textdomain' ) ) {   

    //Translations
    add_action( 'plugins_loaded', 'wjecf_load_plugin_textdomain' );
    function wjecf_load_plugin_textdomain() {
        $locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce' );

        load_textdomain( 'woocommerce-jos-autocoupon', WP_LANG_DIR . '/woocommerce-jos-autocoupon/woocommerce-jos-autocoupon-' . $locale . '.mo' );        
        load_plugin_textdomain('woocommerce-jos-autocoupon', false, basename(dirname(__FILE__)) . '/languages/' );
    }

    /**
     * Get the instance of WJECF
     * @return WJECF_Controller|WJECF_Pro_Controller The instance of WJECF
     */
    function WJECF() {
        if ( class_exists( 'WJECF_Pro_Controller' ) ) { 
            return WJECF_Pro_Controller::instance();
        } else {
            return WJECF_Controller::instance();
        }
    }

    /**
     * Get the instance of WJECF_Admin
     * @return WJECF_Admin The instance of WJECF_Admin
     */
    function WJECF_ADMIN() {
        return WJECF()->get_plugin('WJECF_Admin');
    }

    /**
     * Get the instance of WJECF_WC
     * @return WJECF_WC The instance of WJECF_WC
     */
    function WJECF_WC() {
        return WJECF_WC::instance();
    }

    /**
     * Get the instance if the WooCommerce Extended Coupon Features API
     * @return WJECF_Pro_API The API object
     */
    function WJECF_API() {
        return WJECF_Pro_API::instance();
    }       

    /**
     * Wraps a product or coupon in a decorator
     * @param mixed $object The WC_Coupon or WC_Product instance, or the post id
     * @return WJECF_Wrap
     */
    function WJECF_Wrap( $object ) {
        return WJECF_WC::instance()->wrap( $object );
    }

    require_once( 'includes/WJECF_Bootstrap.php' );
    
    WJECF_Bootstrap::execute();

    //DEPRECATED. We keep $wjecf_extended_coupon_features for backwards compatibility; use WJECF_API()
    $wjecf_extended_coupon_features = WJECF();


}


// =========================================================================================================
// Some snippets that might be useful
// =========================================================================================================

/* // HINT: Use this snippet in your theme if you use coupons with restricted emails and AJAX enabled one-page-checkout.

//Update the cart preview when the billing email is changed by the customer
add_filter( 'woocommerce_checkout_fields', function( $checkout_fields ) {
    $checkout_fields['billing']['billing_email']['class'][] = 'update_totals_on_change';
    return $checkout_fields;    
} );
 
// */ 
 

/* // HINT: Use this snippet in your theme if you want to update cart preview after changing payment method.
//Even better: In your theme add class "update_totals_on_change" to the container that contains the payment method radio buttons.
//Do this by overriding woocommerce/templates/checkout/payment.php

//Update the cart preview when payment method is changed by the customer
add_action( 'woocommerce_review_order_after_submit' , function () {
    ?><script type="text/javascript">
        jQuery(document).ready(function($){
            $(document.body).on('change', 'input[name="payment_method"]', function() {
                $('body').trigger('update_checkout');
                //$.ajax( $fragment_refresh );
            });
        });
    </script><?php 
} );
// */
