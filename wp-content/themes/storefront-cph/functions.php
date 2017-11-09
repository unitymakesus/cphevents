<?php
/**
 * Enqueue sripts
 */
add_action('init', function() {
  // wp_enqueue_script('validate', get_stylesheet_directory_uri() . '/jquery.validate.min.js', array('jquery'), '1.17.0', true);
  wp_enqueue_script('cph-scripts', get_stylesheet_directory_uri() . '/scripts.js', array('jquery'), null, true);
  wp_localize_script( 'cph-scripts', 'cphajax', array(
	   'ajaxurl' => admin_url( 'admin-ajax.php' ),
  ));
});

/**
 * Determine if WooCommerce is active
 * Taken from parent theme
 */
if ( ! function_exists( 'storefront_is_woocommerce_activated' ) ) {
	function storefront_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' ) ? true : false;
	}
}

/**
 * Remove actions from WooCommerce and parent theme
 */
if ( storefront_is_woocommerce_activated() ) {
  add_action('init', function() {
    // Get rid of search in header
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    remove_action( 'storefront_header', 'storefront_header_cart',    60 );
    // Get rid of thumbnail in product list
    remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
    // Don't loop columns
    remove_filter( 'loop_shop_columns',                  'storefront_loop_columns' );
    remove_action( 'woocommerce_before_shop_loop',       'storefront_product_columns_wrapper',       40 );
    remove_action( 'woocommerce_after_shop_loop',        'storefront_product_columns_wrapper_close', 40 );
    // Get rid of stuff from before loop
    remove_action( 'woocommerce_before_shop_loop',       'storefront_sorting_wrapper',               9 );
    remove_action( 'woocommerce_before_shop_loop',       'woocommerce_catalog_ordering',             10 );
    remove_action( 'woocommerce_before_shop_loop',       'woocommerce_result_count',                 20 );
    remove_action( 'woocommerce_before_shop_loop',       'storefront_sorting_wrapper_close',         31 );
    // Get rid of stuff from after loop
    remove_action( 'woocommerce_after_shop_loop',        'storefront_sorting_wrapper',               9 );
    remove_action( 'woocommerce_after_shop_loop',        'woocommerce_catalog_ordering',             10 );
    remove_action( 'woocommerce_after_shop_loop',        'woocommerce_result_count',                 20 );
    remove_action( 'woocommerce_after_shop_loop',        'woocommerce_pagination',                   30 );
    remove_action( 'woocommerce_after_shop_loop',        'storefront_sorting_wrapper_close',         31 );
  });


  /*****************************************************************************
  * EVENT LIST PAGE
  *****************************************************************************/

  /**
   * Display events in date order
   */
  add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_post_type_archive() && $query->get('post_type') == 'product') {
      $query->set('posts_per_page', '-1');
      $query->set('meta_key', 'date');
      $query->set('orderby', 'meta_value');
      $query->set('order', 'ASC');
    }
  });

  /**
   * Show event date in product list
   */
  add_action('woocommerce_after_shop_loop_item_title', function() {
    echo '<span class="category">';
    $terms = wp_get_post_terms(get_the_ID(), 'product_cat');
    echo $terms[0]->name;
    echo '</span>';
    echo '<span class="date">';
    echo get_post_meta(get_the_ID(), 'display_date', true);
    echo '</span>';
  }, 5);

  /**
   * Move price to outside link
   */
  remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
  add_action( 'woocommerce_after_shop_loop_item', function() {
    global $product;

    if ( !$product->is_type('variable') && $price_html = $product->get_price_html() ) {
    	echo '<span class="price">';
      echo $price_html;
      echo '</span>';
    }
  }, 8);

  /**
   * Add quantity picker in product list
   */
  require 'event-list-quantity-picker.php';
  remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
  add_action('woocommerce_after_shop_loop_item', 'cph_event_list_quantity_picker', 10);


  /*****************************************************************************
  * CHECKOUT PAGE
  *****************************************************************************/

  /**
   * Remove order notes field in checkout
   */
  add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
  add_filter( 'woocommerce_checkout_fields' , 'custom_wc_checkout_fields' );
  function custom_wc_checkout_fields( $fields ) {
    $fields['billing']['billing_state']['label'] = 'State';
    unset($fields['billing']['billing_country']);
    unset($fields['order']['order_comments']);
    return $fields;
  }

  add_filter( 'woocommerce_coupons_enabled', function( $enabled ) {
    if ( is_checkout() ) {
      $enabled = false;
    }
    return $enabled;
  });

  /**
   * Add custom checkout fields per product
   */
  require 'checkout-functions.php';
  add_action( 'woocommerce_before_order_notes', 'cph_custom_checkout_fields' );

  /**
  * Process the checkout and check for errors
  */
  add_action('woocommerce_checkout_process', 'cph_custom_checkout_field_process');

  /**
   * Save custom checkout fields to database
   */
  add_action('woocommerce_checkout_update_order_meta', 'cph_custom_checkout_field_update_order_meta' );


  /*****************************************************************************
  * ORDER DETAILS
  *****************************************************************************/

  /**
  * Display custom field values
  */
  require 'order-details-functions.php';
  add_action( 'woocommerce_order_item_meta_end', 'cph_order_details_tickets', 10, 3); // Order details page
  add_action( 'woocommerce_after_order_itemmeta', 'cph_order_details_tickets', 10, 3);  // Admin order details
}
