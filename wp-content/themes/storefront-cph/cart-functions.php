<?php

/**
 * Add custom checkout fields for each product in cart
 * @param  object $checkout WooCommerce checkout object
 * @return string HTML
 */
function cph_custom_product_fields( $cart_item, $cart_item_key ) {
  $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
  $customer = WC()->session->get('customer');

  // Get session data for this product
  $pre_pop_data = WC()->session->get( $_product->get_id() . '_tickets_data' );

  for ($i = 1; $i <= $cart_item['quantity']; $i++) {

    $field_prefix = $_product->get_id() . '_ticket_' . $i;
    $field_data = $pre_pop_data[$field_prefix];
      ?>

      <div class="ticket-details <?php echo $field_prefix; ?>__field-wrapper" data-ticket-key="<?php echo $field_prefix; ?>" data-product="<?php echo $_product->get_id(); ?>">
        <h4>Ticket <?php echo $i; ?></h4>

        <?php if (!empty($customer['address_1']) || $i > 1) { ?>
          <p class="form-row form-row-wide control-copy">
            <label for="<?php echo $field_prefix; ?>_copy_data">Copy information from:</label>
            <select id="<?php echo $field_prefix; ?>_copy_data" class="copy-data">
              <option value="">Select (optional)</option>
              <option value="account">My account</option>
            </select>
          </p>
        <?php } ?>

        <?php
        woocommerce_form_field( $field_prefix . '_first_name',
          array(
            'type'          => 'text',
            'class'         => array('form-row-first'),
            'label'         => __('First name'),
            'required'      => true,
          ),
          $field_data['first_name']
        );

        woocommerce_form_field( $field_prefix . '_last_name',
          array(
            'type'          => 'text',
            'class'         => array('form-row-last'),
            'label'         => __('Last name'),
            'required'      => true,
          ),
          $field_data['last_name']
        );

        woocommerce_form_field( $field_prefix . '_address_1',
          array(
            'type'          => 'text',
            'class'         => array('form-row-wide'),
            'label'         => __('Mailing address'),
            'placeholder'   => __('House number and street name'),
            'required'      => true,
          ),
          $field_data['address_1']
        );

        woocommerce_form_field( $field_prefix . '_address_2',
          array(
            'type'          => 'text',
            'class'         => array('form-row-wide'),
            'label'         => __(''),
            'placeholder'   => __('Apartment, suite, unit etc. (optional)'),
            'required'      => false,
          ),
          $field_data['address_2']
        );

        woocommerce_form_field( $field_prefix . '_city',
          array(
            'type'          => 'text',
            'class'         => array('form-row-wide'),
            'label'         => __('Town / City'),
            'required'      => true,
          ),
          $field_data['city']
        );

        woocommerce_form_field( $field_prefix . '_state',
          array(
            'type'          => 'state',
            'class'         => array('form-row-wide'),
            'label'         => __('State'),
            'required'      => true,
          ),
          $field_data['state']
        );

        woocommerce_form_field( $field_prefix . '_postcode',
          array(
            'type'          => 'text',
            'class'         => array('form-row-wide'),
            'label'         => __('ZIP'),
            'required'      => true,
          ),
          $field_data['postcode']
        );

        woocommerce_form_field( $field_prefix . '_phone',
          array(
            'type'          => 'text',
            'class'         => array('form-row-first'),
            'label'         => __('Phone'),
            'required'      => false,
          ),
          $field_data['phone']
        );

        woocommerce_form_field( $field_prefix . '_email',
          array(
            'type'          => 'text',
            'class'         => array('form-row-last'),
            'label'         => __('Email address'),
            'required'      => true,
          ),
          $field_data['email']
        );

        woocommerce_form_field( $field_prefix . '_special_needs',
          array(
            'type'          => 'textarea',
            'class'         => array('form-row-wide'),
            'label'         => __('Please indicate any accommodations and/or services you require.'),
            'required'      => false,
          ),
          $field_data['special_needs']
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
          ?>

          <div class="discount-validation" data-discount-type="teacher" data-original-price="<?php echo $_product->get_price(); ?>" data-order-id="<?php echo $order_id; ?>">
            <?php
              woocommerce_form_field( $field_prefix . '_teacher',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide', 'validation-checkbox'),
                  'label'         => __('This participant is a teacher/educator.'),
                  'required'      => false,
                ),
                $field_data['teacher']
              );
            ?>

            <div class="hidden-fields">
              <p>Eligible teachers may receive 50% off their ticket for any Adventure in Ideas Seminars. Please complete the fields below.</p>
              <?php
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
                  $field_data['teacher_type']
                );

                woocommerce_form_field( $field_prefix . '_teacher_school',
                  array(
                    'type'          => 'text',
                    'class'         => array('form-row-wide'),
                    'label'         => __('School Name'),
                    'required'      => false,
                  ),
                  $field_data['teacher_school']
                );

                woocommerce_form_field( $field_prefix . '_teacher_county',
                  array(
                    'type'          => 'text',
                    'class'         => array('form-row-wide'),
                    'label'         => __('School County'),
                    'required'      => false,
                  ),
                  $field_data['teacher_county']
                );
              ?>
            </div>

          </div>

        <?php
        } // End adventures-in-ideas-seminar

        // If this is an Adventures in Ideas Seminar, Dialogues Seminar, or Flyleaf Lecture (humanities-in-action):
        if ($terms[0]->slug == 'adventures-in-ideas-seminar' || $terms[0]->slug == 'dialogues-seminar' || $terms[0]->slug == 'humanities-in-action') {
          ?>

          <div class="discount-validation" data-discount-type="gaa">
            <?php
              woocommerce_form_field( $field_prefix . '_gaa',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide', 'validation-checkbox'),
                  'label'         => __('This participant is a member of the UNC General Alumni Association.'),
                  'required'      => false,
                ),
                $field_data['gaa']
              );
            ?>

            <div class="hidden-fields">
              <?php
                if ($terms[0]->slug == 'humanities-in-action') {
                  echo '<p>Eligible GAA Members may opt-in to receive $5 off their ticket to a Humanities in Action series event.</p>';

                  woocommerce_form_field( $field_prefix . '_gaa_discount_flyleaf',
                    array(
                      'type'          => 'checkbox',
                      'class'         => array('form-row-wide'),
                      'label'         => __('Check this box to claim the GAA Discount and select your membership type below:'),
                      'required'      => false,
                    ),
                    $field_data['gaa_discount_flyleaf']
                  );
                } else {
                  echo '<p>Eligible GAA Members may opt-in to receive $15 off their ticket for one Adventure in Ideas Seminar or Dialogues Seminar per semester.</p>';

                  woocommerce_form_field( $field_prefix . '_gaa_discount_seminar',
                    array(
                      'type'          => 'checkbox',
                      'class'         => array('form-row-wide'),
                      'label'         => __('Check this box to claim the GAA Discount and select your membership type below:'),
                      'required'      => false,
                    ),
                    $field_data['gaa_discount_seminar']
                  );
                }

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
                  $field_data['gaa_type']
                );
              ?>
            </div>

          </div>
        <?php } ?>
      </div>

    <?php
  }
}


/**
 * Sets errors for custom cart fields that are required
 * @return null
 */
function cph_custom_cart_field_process() {

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
 * AJAX callback for adding per ticket attendee info to session
 * @return empty;
 */
add_action('wp_ajax_cph_update_cart_meta', 'cph_update_cart_meta_callback' );
add_action('wp_ajax_nopriv_cph_update_cart_meta', 'cph_update_cart_meta_callback' );
function cph_update_cart_meta_callback(){

  // For each item in the cart, we're adding session data for the ticket details
  $cart_items = WC()->cart->get_cart_contents();
  foreach ($cart_items as $key => $item) {
    $this_session = array();

    // Clear ticket session data each time
    WC()->session->set( $item['product_id'] . '_tickets_data', null);

    // Set up array to put in session data
    foreach ($_POST['custom_fields'] as $ticket_details) {
      if ($ticket_details['product_id'] == $item['product_id']) {
        $this_session[$ticket_details['ticket_key']] = $ticket_details;
      }
    }

    // Set new session data
    WC()->session->set( $item['product_id'] . '_tickets_data', $this_session );
  }

  wp_die();
}
