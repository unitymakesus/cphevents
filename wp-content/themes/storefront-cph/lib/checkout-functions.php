<?php
/**
 * Add custom checkout fields for each product in cart
 * @param  object $checkout WooCommerce checkout object
 * @return string HTML
 */
function cph_calculate_fees( $checkout ) {

  $gaa_seminar_guests = array();
  $gaa_flyleaf_bulk = array();

  $teacher_tickets = array();
  $thursday_friday = array();
  $dialogues = array();
  $flyleaf = array();
  $names = array();

  $gaa_flyleaf_count = 0;
  $teacher_count = 0;
  $weekendseminar_count = 0;
  $dialogue_sets = 0;
  $thursday_sets = 0;

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

      // Determine if this is a category that qualifies for discounts
      $matches = false;
      $bulk = false;

      foreach ($terms as $term) {
        $cats = [
          'adventures-in-ideas-seminar',
          'dialogues-seminar',
          'humanities-in-action'
        ];
        if (in_array($term->slug, $cats)) {
          $matches = $term->slug;
        }

        if ($term->slug == 'season-pass') {
          $bulk = true;
        }
      }

      // Loop through each ticket and check for teachers and GAA members, increase count if found
      if (!empty($tickets)) {
        foreach ($tickets as $ticket) {
          $full_name = $ticket['first_name'] . '-' . $ticket['last_name'];

          // Determine auto discounts for GAA members at Humanities in Action events
          if ( !empty( $ticket['gaa'] ) && !empty( $ticket['gaa_type'] ) && $matches == 'humanities-in-action') {
            if ($bulk == true) {
              $gaa_flyleaf_bulk[$full_name] = 1;
            } else {
              $gaa_flyleaf_count ++;
            }
          }

          // // Determine auto discounts for GAA members at Cinema events
          // if ( !empty( $ticket['gaa'] ) && !empty( $ticket['gaa_type'] ) && $matches == 'cinema-school') {
          //   if ($bulk == true) {
          //     $gaa_flyleaf_bulk[$full_name] = 1;
          //   } else {
          //     $gaa_flyleaf_guests[$full_name] = 1;
          //   }
          // }

          // Only allow one GAA discount for seminars per person
          if (isset($ticket['gaa_discount_seminar']) && $ticket['gaa_discount_seminar'] == 1) {
            $gaa_seminar_guests[$full_name] = 1;
          }

          if (isset($terms[0]->slug) && $terms[0]->slug == 'dialogues-seminar') {
            $weekendseminar_count ++;

            // Count number of events in series registered to same guest for bulk pricing
            if (!isset($dialogues[$full_name])) {
              $dialogues[$full_name] = 1;
            } else {
              $dialogues[$full_name] ++;
            }
          }

          if (isset($terms[0]->slug) && $terms[0]->slug == 'thursdays-at-friday-center') {
            // Count number of events in series registered to same guest for bulk pricing
            if (!isset($thursday_friday[$full_name])) {
              $thursday_friday[$full_name] = 1;
            } else {
              $thursday_friday[$full_name] ++;
            }
          }

          // Count AiI Seminars and teachers can only get discount on these
          if (isset($terms[0]->slug) && $terms[0]->slug == 'adventures-in-ideas-seminar') {
            $weekendseminar_count ++;

            // Teachers can get 50% off this category
            if (isset($ticket['teacher']) && $ticket['teacher'] == 1) {
              $teacher_count ++;

              // If ticket includes meal
              $ticket_var = $_product->get_attributes();
              if ($ticket_var['pa_meal'] == "lunch" || $ticket_var['pa_meal'] == "dinner") {
                $parent_product = new WC_Product_Variable($parent);
                $variations = $parent_product->get_available_variations();

                // Get price of ticket without meal
                foreach ($variations as $variation) {
                  if (stristr($variation['attributes']['attribute_pa_meal'], 'no-')) {
                    $teacher_tickets[] = $variation['display_price'];
                  }
                }
              } else {
                $teacher_tickets[] = $_product->get_price();
              }
            }
          }

          // Tally total number of unique names
          if (!isset($names[$full_name])) {
            $names[$full_name] = 1;
          } else {
            $names[$full_name] ++;
          }
        }
      }
    }
  }

  // Apply teacher discounts to cart
  if ($teacher_count > 0) {
    $teacher_total = array_sum($teacher_tickets);
    $teacher_discount = -($teacher_total / 2);

    WC()->cart->add_fee('Teacher Discount (50% off Adventures in Ideas Seminars) x ' . $teacher_count , $teacher_discount);
  }

  // Apply GAA Seminar discounts to cart
  if ($gaa_seminar_count = count($gaa_seminar_guests) > 0) {
    $gaa_seminar_discount = -($gaa_seminar_count * 15);

    WC()->cart->add_fee('GAA Discount ($15 off one Adventures in Ideas or Dialogue Seminar per person) x ' . $gaa_seminar_count, $gaa_seminar_discount);
  }

  // Apply GAA Humanities in Action discounts to cart
  if ($gaa_flyleaf_count > 0) {
    $gaa_flyleaf_discount = -($gaa_flyleaf_count * 5);

    WC()->cart->add_fee('GAA Discount ($5 off Humanities in Action series events) x ' . $gaa_flyleaf_count, $gaa_flyleaf_discount);
  }

  // Apply GAA Humanities in Action season pass discounts to cart
  if ($gaa_flyleaf_bulk_count = count($gaa_flyleaf_bulk) > 0) {
    $gaa_flyleaf_bulk_discount = -($gaa_flyleaf_bulk_count * 35);

    WC()->cart->add_fee('GAA Discount ($35 off Flyleaf Season Pass) x ' . $gaa_flyleaf_bulk_count, $gaa_flyleaf_bulk_discount);
  }

  // Discount for 2 Thursdays at the Friday Center
  foreach ($thursday_friday as $thursday_guest) {
    if ($thursday_guest >= 2) {
      $thursday_sets ++;
    }
  }
  if ($thursday_sets > 0) {
    $thursday_discount = -($thursday_sets * 10);
    WC()->cart->add_fee('Bulk Discount ($10 off both Thursdays at the Friday Center) x ' . $thursday_sets, $thursday_discount);
  }

  // Discount for a semester worth of Dialogues
  // (there were 2 in Fall 2018)
  // $dialogues_this_semester = 2;
  // foreach ($dialogues as $dia_guest) {
  //   if ($dia_guest >= $dialogues_this_semester) {
  //     $dialogue_sets ++;
  //   }
  // }
  // if ($dialogue_sets > 0) {
  //   $dialogues_off = $dialogues_this_semester * 15;
  //   $dialogues_discount = -($dialogues_discount * $dialogue_sets);
  //   WC()->cart->add_fee('Bulk Discount ($' . $dialogues_off . ' off a full semester of Dialogues) x ' . $dialogue_sets, $dialogues_discount);
  // }

  // Bulk Dialogues and Teacher discounts overrides the 3 or more.
  // So only apply the 3 or more discount if there are
  // 3 or more weekend seminars (Adventures in Ideas and Dialogues) BEYOND those registered by teachers and any bulk Dialogues!
  // $weekendseminar_total = $weekendseminar_count - $teacher_count - ($dialogue_sets * $dialogues_this_semester);
  $weekendseminar_total = $weekendseminar_count - $teacher_count;
  if ($weekendseminar_total > 2) {
    $weekendseminar_discount = -($weekendseminar_total * 10);
    WC()->cart->add_fee('Bulk Discount ($10 off each Adventure in Ideas or Dialogues Seminar) x ' . $weekendseminar_total, $weekendseminar_discount);
  }
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
  $user_id = get_current_user_id();
  $customer = WC()->session->get('customer');

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
    $session_data = WC()->session->get( $_product->get_id() . '_tickets_data' );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

      for ($i = 1; $i <= $cart_item['quantity']; $i++) {
        $field_prefix = $_product->get_id() . '_ticket_' . $i;
        $field_data = $session_data[$field_prefix];

        // Save custom fields to order
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
        if ( ! empty( $field_data['teacher'] ) )
          update_post_meta( $order_id, $field_prefix . '_teacher', $field_data['teacher'] );
        if ( ! empty( $field_data['teacher_type'] ) )
          update_post_meta( $order_id, $field_prefix . '_teacher_type', $field_data['teacher_type'] );
        if ( ! empty( $field_data['teacher_school'] ) )
          update_post_meta( $order_id, $field_prefix . '_teacher_school', $field_data['teacher_school'] );
        if ( ! empty( $field_data['teacher_county'] ) )
          update_post_meta( $order_id, $field_prefix . '_teacher_county', $field_data['teacher_county'] );
        if ( ! empty( $field_data['gaa'] ) )
          update_post_meta( $order_id, $field_prefix . '_gaa', $field_data['gaa'] );
        if ( ! empty( $field_data['gaa_discount_flyleaf'] ) )
          update_post_meta( $order_id, $field_prefix . '_gaa_discount_flyleaf', $field_data['gaa_discount_flyleaf'] );
        if ( ! empty( $field_data['gaa_discount_bulk_flyleaf'] ) )
          update_post_meta( $order_id, $field_prefix . '_gaa_discount_bulk_flyleaf', $field_data['gaa_discount_bulk_flyleaf'] );
        if ( ! empty( $field_data['gaa_discount_seminar'] ) )
          update_post_meta( $order_id, $field_prefix . '_gaa_discount_seminar', $field_data['gaa_discount_seminar'] );
        if ( ! empty( $field_data['gaa_type'] ) )
          update_post_meta( $order_id, $field_prefix . '_gaa_type', $field_data['gaa_type'] );
        if ( ! empty( $field_data['special_needs'] ) )
          update_post_meta( $order_id, $field_prefix . '_special_needs', $field_data['special_needs'] );

        // Save custom fields to customer's user account
        if ($field_data['first_name'] == $customer['first_name'] && $field_data['last_name'] == $customer['last_name']) {
          // If this is the account holder...
          if ( ! empty( $field_data['special_needs'] ) )
            update_field('special_needs', $field_data['special_needs'], "user_{$user_id}");
          if ( ! empty( $field_data['teacher'] ) )
            update_field('teacher', $field_data['teacher'], "user_{$user_id}");
          if ( ! empty( $field_data['teacher_type'] ) )
            update_field('teacher_type', $field_data['teacher_type'], "user_{$user_id}");
          if ( ! empty( $field_data['teacher_school'] ) )
            update_field('teacher_school', $field_data['teacher_school'], "user_{$user_id}");
          if ( ! empty( $field_data['teacher_county'] ) )
            update_field('teacher_county', $field_data['teacher_county'], "user_{$user_id}");
          if ( ! empty( $field_data['gaa'] ) )
            update_field('gaa', $field_data['gaa'], "user_{$user_id}");
          if ( ! empty( $field_data['gaa_type'] ) )
            update_field('gaa_type', $field_data['gaa_type'], "user_{$user_id}");
        } else {
          // If this is a guest ticket...
          $guests = get_field('guests', "user_{$user_id}");
          $updated = 0;

          if (!empty($guests)) {
            foreach ($guests as $row => $guest) {
              if ($field_data['first_name'] == $guest['first_name'] && $field_data['last_name'] == $guest['last_name'] && $field_data['email'] == $guest['email']) {
                update_row('guests', $row + 1, $field_data, "user_{$user_id}");
                $updated = 1;
              }
            }
          }

          if ($updated == 0) {
            add_row('guests', $field_data, "user_{$user_id}");
          }
        }
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
