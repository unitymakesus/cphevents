<?php
/**
 * Add custom checkout fields for each product in cart
 * @param  object $checkout WooCommerce checkout object
 * @return string HTML
 */
function cph_custom_checkout_fields( $checkout ) {

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
      ?>
      <div class="ticket-details-wrapper">

        <h3><?php echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ); ?></h3>

        <?php for ($i = 1; $i <= $cart_item['quantity']; $i++) { ?>

          <?php $field_prefix = $_product->get_id() . '_ticket_' . $i; ?>

          <div class="ticket-details <?php echo $field_prefix; ?>__field-wrapper" data-ticket-key="<?php echo $field_prefix; ?>" data-product="<?php echo $_product->get_id(); ?>">
            <h4>Ticket <?php echo $i; ?></h4>

            <p class="form-row form-row-wide control-copy">
              <label for="<?php echo $field_prefix; ?>_copy_data">Copy data from:</label>
              <select id="<?php echo $field_prefix; ?>_copy_data" class="copy-data">
                <option value="">Select (optional)</option>
                <option value="woocommerce-billing-fields">Billing details</option>
              </select>
            </p>

            <?php
            woocommerce_form_field( $field_prefix . '_first_name',
              array(
                'type'          => 'text',
                'class'         => array('form-row-first'),
                'label'         => __('First name'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_first_name' )
            );

            woocommerce_form_field( $field_prefix . '_last_name',
              array(
                'type'          => 'text',
                'class'         => array('form-row-last'),
                'label'         => __('Last name'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_last_name' )
            );

            woocommerce_form_field( $field_prefix . '_address_1',
              array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('Mailing address'),
                'placeholder'   => __('House number and street name'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_address_1' )
            );

            woocommerce_form_field( $field_prefix . '_address_2',
              array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __(''),
                'placeholder'   => __('Apartment, suite, unit etc. (optional)'),
                'required'      => false,
              ),
              $checkout->get_value( $field_prefix . '_address_2' )
            );

            woocommerce_form_field( $field_prefix . '_city',
              array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('Town / City'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_city' )
            );

            woocommerce_form_field( $field_prefix . '_state',
              array(
                'type'          => 'state',
                'class'         => array('form-row-wide'),
                'label'         => __('State'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_state' )
            );

            woocommerce_form_field( $field_prefix . '_postcode',
              array(
                'type'          => 'text',
                'class'         => array('form-row-wide'),
                'label'         => __('ZIP'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_postcode' )
            );

            woocommerce_form_field( $field_prefix . '_phone',
              array(
                'type'          => 'text',
                'class'         => array('form-row-first'),
                'label'         => __('Phone'),
                'required'      => false,
              ),
              $checkout->get_value( $field_prefix . '_phone' )
            );

            woocommerce_form_field( $field_prefix . '_email',
              array(
                'type'          => 'text',
                'class'         => array('form-row-last'),
                'label'         => __('Email address'),
                'required'      => true,
              ),
              $checkout->get_value( $field_prefix . '_email' )
            );

            woocommerce_form_field( $field_prefix . '_special_needs',
              array(
                'type'          => 'textarea',
                'class'         => array('form-row-wide'),
                'label'         => __('Please indicate any accommodations and/or services you require.'),
                'required'      => false,
              ),
              $checkout->get_value( $field_prefix . '_special_needs' )
            );

            // Get product category
            if ($_product->is_type('variation')) {
              $parent = $_product->get_parent_ID();
              $terms = wp_get_post_terms($parent, 'product_cat');
            } else {
              $terms = wp_get_post_terms($_product->get_ID(), 'product_cat');
            }

            // If this is an Adventures in Ideas Seminar:
            if ($terms[0]->slug == 'adventures-in-ideas-seminar') {

              echo '<div class="discount-validation" data-discount-type="teacher" data-original-price="' . $_product->get_price() . '" data-order-id="' . $order_id . '">';

                woocommerce_form_field( $field_prefix . '_teacher',
                  array(
                    'type'          => 'checkbox',
                    'class'         => array('form-row-wide', 'validation-checkbox'),
                    'label'         => __('This participant is a teacher/educator.'),
                    'required'      => false,
                  ),
                  $checkout->get_value( $field_prefix . '_teacher' )
                );

                echo '<div class="hidden-fields">';

                  woocommerce_form_field( $field_prefix . '_teacher_type',
                    array(
                      'type'          => 'select',
                      'class'         => array('form-row-wide'),
                      'label'         => __('Teacher Type'),
                      'options'       => array(
                        ''                    => '',
                        'k-12-teacher'        => 'K-12 Teacher',
                        'k-12-librarian'      => 'K-12 Librarian',
                        'k-12-administrator'  => 'K-12 Administrator',
                        'community-college'   => 'Community College Teacher'
                      ),
                      'required'      => false,
                    ),
                    $checkout->get_value( $field_prefix . '_teacher_type' )
                  );

                  woocommerce_form_field( $field_prefix . '_teacher_school',
                    array(
                      'type'          => 'text',
                      'class'         => array('form-row-wide'),
                      'label'         => __('School Name'),
                      'required'      => false,
                    ),
                    $checkout->get_value( $field_prefix . '_teacher_school' )
                  );

                  woocommerce_form_field( $field_prefix . '_teacher_county',
                    array(
                      'type'          => 'text',
                      'class'         => array('form-row-wide'),
                      'label'         => __('School County'),
                      'required'      => false,
                    ),
                    $checkout->get_value( $field_prefix . '_teacher_county' )
                  );

                echo '</div>';

              echo '</div>';

              echo '<div class="discount-validation" data-discount-type="gaa">';

                woocommerce_form_field( $field_prefix . '_gaa',
                  array(
                    'type'          => 'checkbox',
                    'class'         => array('form-row-wide', 'validation-checkbox'),
                    'label'         => __('This participant is a member of the UNC General Alumni Association.'),
                    'required'      => false,
                  ),
                  $checkout->get_value( $field_prefix . '_gaa' )
                );

                echo '<div class="hidden-fields">';

                  woocommerce_form_field( $field_prefix . '_gaa_type',
                    array(
                      'type'          => 'select',
                      'class'         => array('form-row-wide'),
                      'label'         => __('GAA Membership Type'),
                      'options'       => array(
                        ''              => '',
                        'annual'        => 'Annual Membership',
                        'lifetime'      => 'Lifetime Membership',
                      ),
                      'required'      => false,
                    ),
                    $checkout->get_value( $field_prefix . '_gaa_type' )
                  );

                  woocommerce_form_field( $field_prefix . '_gaa_pid',
                    array(
                      'type'          => 'text',
                      'class'         => array('form-row-wide'),
                      'label'         => __('PID Number'),
                      'required'      => false,
                    ),
                    $checkout->get_value( $field_prefix . '_gaa_pid' )
                  );

                echo '</div>';

              echo '</div>';

            }
            ?>

          </div>

        <?php } ?>

      </div>

    <?php
    }

  }

}

/**
 * Sets errors for custom checkout fields that are required
 * @return null
 */
function cph_custom_checkout_field_process() {

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

      for ($i = 1; $i <= $cart_item['quantity']; $i++) {
        $field_prefix = $_product->get_id() . '_ticket_' . $i;

        if ( ! $_POST[$field_prefix . '_first_name'] )
          wc_add_notice( 'Please enter a first name for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_last_name'] )
          wc_add_notice( 'Please enter a last name for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_address_1'] )
          wc_add_notice( 'Please enter a street address for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_city'] )
          wc_add_notice( 'Please enter a city for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_state'] )
          wc_add_notice( 'Please enter a state for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_postcode'] )
          wc_add_notice( 'Please enter a zip code for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
        if ( ! $_POST[$field_prefix . '_email'] )
          wc_add_notice( 'Please enter an email address for ' . apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i, 'error' );
      }

    }

  }

}

/**
 * Saves custom checkout fields to database
 */
function cph_custom_checkout_field_update_order_meta( $order_id ) {

  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

      for ($i = 1; $i <= $cart_item['quantity']; $i++) {
        $field_prefix = $_product->get_id() . '_ticket_' . $i;

        if ( ! empty( $_POST[$field_prefix . '_first_name'] ) )
          update_post_meta( $order_id, $field_prefix . '_first_name', $_POST[$field_prefix . '_first_name'] );
        if ( ! empty( $_POST[$field_prefix . '_last_name'] ) )
          update_post_meta( $order_id, $field_prefix . '_last_name', $_POST[$field_prefix . '_last_name'] );
        if ( ! empty( $_POST[$field_prefix . '_address_1'] ) )
          update_post_meta( $order_id, $field_prefix . '_address_1', $_POST[$field_prefix . '_address_1'] );
        if ( ! empty( $_POST[$field_prefix . '_address_2'] ) )
          update_post_meta( $order_id, $field_prefix . '_address_2', $_POST[$field_prefix . '_address_2'] );
        if ( ! empty( $_POST[$field_prefix . '_city'] ) )
          update_post_meta( $order_id, $field_prefix . '_city', $_POST[$field_prefix . '_city'] );
        if ( ! empty( $_POST[$field_prefix . '_state'] ) )
          update_post_meta( $order_id, $field_prefix . '_state', $_POST[$field_prefix . '_state'] );
        if ( ! empty( $_POST[$field_prefix . '_postcode'] ) )
          update_post_meta( $order_id, $field_prefix . '_postcode', $_POST[$field_prefix . '_postcode'] );
        if ( ! empty( $_POST[$field_prefix . '_phone'] ) )
          update_post_meta( $order_id, $field_prefix . '_phone', $_POST[$field_prefix . '_phone'] );
        if ( ! empty( $_POST[$field_prefix . '_email'] ) )
          update_post_meta( $order_id, $field_prefix . '_email', $_POST[$field_prefix . '_email'] );
        if ( ! empty( $_POST[$field_prefix . '_special_needs'] ) )
          update_post_meta( $order_id, $field_prefix . '_special_needs', $_POST[$field_prefix . '_special_needs'] );
      }

    }

  }

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
    // WC()->cart->apply_coupon( 't50special' );
    // print_r(cph_apply_discount('t50special'));

  } elseif ($discount_type == 'gaa') {
    // Apply $15 off total if this coupon isn't already applied
  }

}

add_action('woocommerce_coupon_is_valid', function($valid, $coupon) {
  return true;
}, 10, 2);


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
