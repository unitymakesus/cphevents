<?php
/**
 * Add custom checkout fields for each product in cart
 * @param  object $checkout WooCommerce checkout object
 * @return string HTML
 */
function cph_calculate_fees( $checkout ) {

  $teacher_tickets = array();
  $teacher_count = 0;
  $teacher_discount = 0;
  $gaa_seminar_count = 0;
  $gaa_flyleaf_count = 0;
  $gaa_flyleaf_bulk = 0;
  $aiiseminar_count = 0;
  $dseminar_count = 0;

  // Loop through each event in cart and get saved ticket data
  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
      $tickets = WC()->session->get( $_product->get_id() . '_tickets_data' );

      // Get product category
      if ($_product->is_type('variation')) {
        $parent = $_product->get_parent_ID();
        $terms = wp_get_post_terms($parent, 'product_cat');
      } else {
        $terms = wp_get_post_terms($_product->get_ID(), 'product_cat');
      }

      // Loop through each ticket and check for teachers and GAA members, increase count if found
      if (!empty($tickets)) {
        foreach ($tickets as $ticket) {
          if ($ticket['teacher'] == 1) {
            $teacher_count ++;
            $teacher_tickets[] = $_product->get_price();
          }

          if ($ticket['gaa_discount_seminar'] == 1) {
            $gaa_seminar_count ++;
          }

          if ($ticket['gaa_discount_bulk_flyleaf'] == 1) {
            $gaa_flyleaf_bulk ++;
          }

          if ($ticket['gaa_discount_flyleaf'] == 1) {
            $gaa_flyleaf_count ++;
          }

          if ($terms[0]->slug == 'adventures-in-ideas-seminar') {
            $aiiseminar_count ++;
          }

          if ($terms[0]->slug == 'dialogues-seminar') {
            $dseminar_count ++;
          }
        }
      }
    }
  }

  // Apply teacher discounts to cart
  if ($teacher_count > 0) {
    $teacher_total = array_sum($teacher_tickets);
    $teacher_discount = -($teacher_total / 2);

    WC()->cart->add_fee('Teacher Discount (50% off Adventures in Ideas Seminars)', $teacher_discount);
  }

  // Apply GAA Seminar discounts to cart
  if ($gaa_seminar_count > 0) {
    $gaa_seminar_discount = -($gaa_seminar_count * 15);

    WC()->cart->add_fee('GAA Discount ($15 off Adventures in Ideas or Dialogue Seminars)', $gaa_seminar_discount);
  }

  // Apply GAA Humanities in Action discounts to cart
  if ($gaa_flyleaf_count > 0) {
    $gaa_flyleaf_discount = -($gaa_flyleaf_count * 5);

    WC()->cart->add_fee('GAA Discount ($5 off Humanities in Action series events)', $gaa_flyleaf_discount);
  }

  // Apply GAA Humanities in Action season pass discounts to cart
  if ($gaa_flyleaf_bulk > 0) {
    $gaa_flyleaf_bulk_discount = -($gaa_flyleaf_bulk * 35);

    WC()->cart->add_fee('GAA Discount ($35 off Flyleaf Season Pass)', $gaa_flyleaf_bulk_discount);
  }

  // Teacher discount overrides the 3 or more.
  // So only apply the 3 or more coupon if there are
  // 3 or more AiI seminars BEYOND those registered by teachers
  if ($teacher_count +3 > $aiiseminar_count) {
    WC()->cart->remove_coupon('3ormore-spring18');
  } elseif ($aiiseminar_count > 2) {
    if (!in_array('3ormore-spring18', WC()->cart->get_applied_coupons())) {
      WC()->cart->apply_coupon('3ormore-spring18');
    }
  }

  // Discount for all 4 dialogues
}


/**
 * Changes place order button text
 * @return HTML
 */
add_filter( 'woocommerce_order_button_text', function() {
  return 'Proceed to Payment';
});


/**
 * Sets errors for custom checkout fields that are required
 * @return null
 */
// function cph_custom_checkout_field_process() {
//
//   foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
//     $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
//
//     if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
//
//       for ($i = 1; $i <= $cart_item['quantity']; $i++) {
//         $field_prefix = $_product->get_id() . '_ticket_' . $i;
//
//         if ( ! $_POST[$field_prefix . '_first_name'] )
//           wc_add_notice( 'Please enter a first name for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_last_name'] )
//           wc_add_notice( 'Please enter a last name for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_address_1'] )
//           wc_add_notice( 'Please enter a street address for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_city'] )
//           wc_add_notice( 'Please enter a city for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_state'] )
//           wc_add_notice( 'Please enter a state for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_postcode'] )
//           wc_add_notice( 'Please enter a zip code for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//         if ( ! $_POST[$field_prefix . '_email'] )
//           wc_add_notice( 'Please enter an email address for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
//       }
//
//     }
//
//   }
//
// }

/**
 * Saves custom checkout fields to database
 */
function cph_custom_checkout_field_update_order_meta( $order_id ) {

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $session_data = WC()->session->get( $_product->get_id() . '_tickets_data' );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

      for ($i = 1; $i <= $cart_item['quantity']; $i++) {
        $field_prefix = $_product->get_id() . '_ticket_' . $i;
        $field_data = $session_data[$field_prefix];

        if ( ! empty( $field_data['first_name'] ) )
          update_post_meta( $order_id, $field_prefix . '_first_name', $field_data['first_name'] );
        if ( ! empty( $field_data['last_name'] ) )
          update_post_meta( $order_id, $field_prefix . '_last_name', $field_data['last_name'] );
        if ( ! empty( $field_data['address_1'] ) )
          update_post_meta( $order_id, $field_prefix . '_address_1', $field_data['address_1'] );
        if ( ! empty( $field_data['address_2'] ) )
          update_post_meta( $order_id, $field_prefix . '_address_2', $field_data['address_2'] );
        if ( ! empty( $field_data['city'] ) )
          update_post_meta( $order_id, $field_prefix . '_city', $field_data['city'] );
        if ( ! empty( $field_data['state'] ) )
          update_post_meta( $order_id, $field_prefix . '_state', $field_data['state'] );
        if ( ! empty( $field_data['postcode'] ) )
          update_post_meta( $order_id, $field_prefix . '_postcode', $field_data['postcode'] );
        if ( ! empty( $field_data['phone'] ) )
          update_post_meta( $order_id, $field_prefix . '_phone', $field_data['phone'] );
        if ( ! empty( $field_data['email'] ) )
          update_post_meta( $order_id, $field_prefix . '_email', $field_data['email'] );
        if ( ! empty( $field_data['special_needs'] ) )
          update_post_meta( $order_id, $field_prefix . '_special_needs', $field_data['special_needs'] );
      }

    }

  }

}


// function bbloomer_wc_discount_total_30() {
//
//    global $woocommerce;
//
//    $discount_total = 0;
//
//    foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values) {
//
//    $_product = $values['data'];
//
//        if ( $_product->is_on_sale() ) {
//        $regular_price = $_product->get_regular_price();
//        $sale_price = $_product->get_sale_price();
//        $discount = ($regular_price - $sale_price) * $values['quantity'];
//        $discount_total += $discount;
//        }
//
//    }
//
//    if ( $discount_total > 0 ) {
//    echo '<tr class="cart-discount">
//    <th>'. __( 'You Saved', 'woocommerce' ) .'</th>
//    <td data-title=" '. __( 'You Saved', 'woocommerce' ) .' ">'
//    . wc_price( $discount_total + $woocommerce->cart->discount_cart ) .'</td>
//    </tr>';
//    }
//
// }
//
// // Hook our values to the Basket and Checkout pages
//
// add_action( 'woocommerce_cart_totals_after_order_total', 'bbloomer_wc_discount_total_30', 99);
// add_action( 'woocommerce_review_order_after_order_total', 'bbloomer_wc_discount_total_30', 99);


// add_action( 'woocommerce_review_order_before_order_total', 'custom_cart_total' );
// add_action( 'woocommerce_before_cart_totals', 'custom_cart_total' );
// function custom_cart_total() {
//
//     if ( is_admin() && ! defined( 'DOING_AJAX' ) )
//             return;
//
//     WC()->cart->total *= 0.25;
//     //var_dump( WC()->cart->total);
// }
// add_action('woocommerce_checkout_update_order_review', function($post_data) {
//   // error_log($post_data);
//
//   // Put post data into array
//   parse_str($post_data, $data);
//
//   // Get teacher and GAA checkbox values
//   $teacher = preg_grep_keys('/ticket_\d+_teacher$/', $data);
//   $gaa = preg_grep_keys('/ticket_\d+_gaa$/', $data);
//
//
//   // If teacher checkbox is checked
//   if (array_shift($teacher) == 1) {
//     error_log('Add discount now please');
//
//     WC()->cart->add_fee('Teacher Discount', -50, false, '');
//
//     // remove_action('woocommerce_cart_reset', array(WC()->cart, 'remove_all_fees'), 1);
//
//     // error_log(print_r(WC()->cart, true));
//
//     // unset( WC()->cart->cart_contents['65ded5353c5ee48d0b7d48c591b8f430'] );    // This works to remove item from cart and refreshes the cart preview on checkout page
//     // $ret = WC()->cart->apply_coupon( 't50special' );
//     // $array = array('return' => $ret); print_r($array);
//     // WC()->cart->apply_coupon('t50special');
//     // WC()->cart->total *= 0.25;
//   }
//
//   // If GAA checkbox is checked
//   if ($gaa == 1) {
//
//   }
// }, 1, 1);

// add_action( 'woocommerce_cart_calculate_fees', function() {
//
// 	if ( is_admin() && ! defined( 'DOING_AJAX' ) )
// 		return;
//
// 	$surcharge = -( WC()->cart->cart_contents_total + WC()->cart->shipping_total ) * .1;
// 	WC()->cart->add_fee( 'Surcharge', $surcharge, true, '' );
//
// });


function preg_grep_keys($pattern, $input, $flags = 0) {
  return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags)));
}

/**
 * AJAX callback for adding discounts to checkout for teachers and GAA members
 */
add_action('wp_ajax_cph_add_discount', 'cph_add_discount_callback' );
add_action('wp_ajax_nopriv_cph_add_discount', 'cph_add_discount_callback' );
function cph_add_discount_callback() {
  $product_id = absint( $_POST['product_id'] );
  $discount_type = $_POST['discount_type'];
  $original_price = $_POST['original_price'];

  if ($discount_type == 'teacher') {

    // Reduce price of this ticket by 50%
    // $discount_price = -($original_price * .5);
    $ret = WC()->cart->apply_coupon( 't50special' );
    $array = array('return' => $ret); print_r($array);

    // print_r(cph_apply_discount('t50special'));

  } elseif ($discount_type == 'gaa') {
    // Apply $15 off total if this coupon isn't already applied
  }

}
//
// add_action('woocommerce_coupon_is_valid', function($valid, $coupon) {
//   return true;
// }, 10, 2);


/**
 * AJAX callback for removing discounts from checkout if fields no longer validate
 */
// add_action('wp_ajax_cph_cph_remove_discount', 'cph_cph_remove_discount_callback' );
// add_action('wp_ajax_nopriv_cph_cph_remove_discount', 'cph_cph_remove_discount_callback' );
// function cph_cph_remove_discount_callback() {
//   $product_id = absint( $_POST['product_id'] );
//   $discount_type = $_POST['discount_type'];
//
// }
//
//
// function cph_apply_discount($raw_coupon) {
//   $cart = WC()->cart;
//
//   $order = wc_get_order();
//
//   if ( is_a( $raw_coupon, 'WC_Coupon' ) ) {
//     $coupon = $raw_coupon;
//   } elseif ( is_string( $raw_coupon ) ) {
//     $code      = wc_format_coupon_code( $raw_coupon );
//     $coupon    = new WC_Coupon( $code );
//
//     if ( $coupon->get_code() !== $code ) {
//       return new WP_Error( 'invalid_coupon', __( 'Invalid coupon code', 'woocommerce' ) );
//     }
//
//     $discounts = new WC_Discounts( $cart );
//     $valid     = $discounts->is_coupon_valid( $coupon );
//
//     if ( is_wp_error( $valid ) ) {
//       return $valid;
//     }
//   } else {
//     return new WP_Error( 'invalid_coupon', __( 'Invalid coupon', 'woocommerce' ) );
//   }
//
//   $discounts = new WC_Discounts( $cart );
//   $applied   = $discounts->apply_coupon( $coupon );
//
//   if ( is_wp_error( $applied ) ) {
//     return $applied;
//   }
//
//   // $order->set_coupon_discount_amounts( $discounts );
//   // $order->set_item_discount_amounts( $discounts );
//   //
//   // // Recalculate totals and taxes.
//   // $cart->calculate_totals( true );
//
//   // Record usage so counts and validation is correct.
//   // if ( ! $used_by = $cart->get_user_id() ) {
//   //   $used_by = $cart->get_billing_email();
//   // }
//   //
//   // $coupon->increase_usage_count( $used_by );
//
//   return true;
// }
