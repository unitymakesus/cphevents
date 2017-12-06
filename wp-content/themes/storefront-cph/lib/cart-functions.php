<?php

/**
 * Add custom checkout fields for each product in cart
 * @param  object $checkout WooCommerce checkout object
 * @return string HTML
 */
function cph_custom_product_fields( $cart_item, $cart_item_key ) {
  $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
  $customer = WC()->session->get('customer');

  $customer_name = $customer['first_name'] . ' ' . $customer['last_name'];

  // Get session data for this product
  $session_data = WC()->session->get( $_product->get_id() . '_tickets_data' );

  for ($i = 1; $i <= $cart_item['quantity']; $i++) {

    $field_prefix = $_product->get_id() . '_ticket_' . $i;
    $field_data = $session_data[$field_prefix];

    // If session data exists for this ticket...
    if (!empty($field_data['address_1'])) {
      $first_name = $field_data['first_name'];
      $last_name = $field_data['last_name'];
      $address_1 = $field_data['address_1'];
      $address_2 = $field_data['address_2'];
      $city = $field_data['city'];
      $state = $field_data['state'];
      $postcode = $field_data['postcode'];
      $phone = $field_data['phone'];
      $email = $field_data['email'];
      $special_needs = $field_data['special_needs'];
      $teacher = $field_data['teacher'];
      $teacher_type = $field_data['teacher_type'];
      $teacher_school = $field_data['teacher_school'];
      $teacher_county = $field_data['teacher_county'];
      $gaa = $field_data['gaa'];
      $gaa_type = $field_data['gaa_type'];
      $full_name = $first_name . ' ' . $last_name;

      $session_prefill = true;
    }
    ?>

      <div class="ticket-details <?php if ($i % 2 == 0) { echo 'col-2'; } else { echo 'col-1'; } ?> <?php echo $field_prefix; ?>__field-wrapper" data-ticket-key="<?php echo $field_prefix; ?>" data-product="<?php echo $_product->get_id(); ?>">
        <h4>Ticket <?php echo $i; ?></h4>

        <p class="form-row form-row-wide control-copy">
          <label for="<?php echo $field_prefix; ?>_ticket_name">Name <abbr class="required" title="required">*</abbr></label>
          <span class="ui-control select">
            <select id="<?php echo $field_prefix; ?>_ticket_name" class="copy-data">
              <option>--Select One--</option>
              <?php if (!empty($customer['address_1']) && $customer_name !== $full_name) { ?>
                <option value="<?php echo sanitize_title_with_dashes($customer_name); ?>"><?php echo $customer_name; ?></option>
              <?php } ?>
              <?php if ($session_prefill == true) { ?>
                <option selected="selected" value="<?php echo sanitize_title_with_dashes($full_name); ?>"><?php echo $full_name; ?></option>
              <?php } ?>
              <option value="new">New Guest</option>
            </select>
            <span class="select_arrow"></span>
          </span>
          <a href="#" class="edit-guest <?php if ($session_prefill !== true) { echo 'hide'; } ?>">Edit contact info</a>
        </p>

        <div class="hidden-fields contact-info">
          <?php
          woocommerce_form_field( $field_prefix . '_first_name',
            array(
              'type'          => 'text',
              'class'         => array('form-row-first'),
              'label'         => __('First name'),
              'required'      => true,
            ),
            $first_name
          );

          woocommerce_form_field( $field_prefix . '_last_name',
            array(
              'type'          => 'text',
              'class'         => array('form-row-last'),
              'label'         => __('Last name'),
              'required'      => true,
            ),
            $last_name
          );

          woocommerce_form_field( $field_prefix . '_address_1',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('Mailing address'),
              'placeholder'   => __('House number and street name'),
              'required'      => true,
            ),
            $address_1
          );

          woocommerce_form_field( $field_prefix . '_address_2',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __(''),
              'placeholder'   => __('Apartment, suite, unit etc. (optional)'),
              'required'      => false,
            ),
            $address_2
          );

          woocommerce_form_field( $field_prefix . '_city',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('Town / City'),
              'required'      => true,
            ),
            $city
          );

          woocommerce_form_field( $field_prefix . '_state',
            array(
              'type'          => 'state',
              'class'         => array('form-row-wide'),
              'label'         => __('State'),
              'required'      => true,
            ),
            $state
          );

          woocommerce_form_field( $field_prefix . '_postcode',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('ZIP'),
              'required'      => true,
            ),
            $postcode
          );

          woocommerce_form_field( $field_prefix . '_phone',
            array(
              'type'          => 'text',
              'class'         => array('form-row-first'),
              'label'         => __('Phone'),
              'required'      => false,
            ),
            $phone
          );

          woocommerce_form_field( $field_prefix . '_email',
            array(
              'type'          => 'text',
              'class'         => array('form-row-last'),
              'label'         => __('Email address'),
              'required'      => true,
            ),
            $email
          );

          woocommerce_form_field( $field_prefix . '_special_needs',
            array(
              'type'          => 'textarea',
              'class'         => array('form-row-wide'),
              'label'         => __('Please indicate any accommodations and/or services you require.'),
              'required'      => false,
            ),
            $special_needs
          );
          ?>

          <button class="ticket-update" data-ticket="<?php echo $field_prefix; ?>">Update Contact Info</button>
          <button class="secondary cancel">Cancel</a>

        </div>

        <?php
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
                  'label'         => __('This guest is a teacher/educator. <a href="#" class="edit-discount hide">Edit details</a>'),
                  'required'      => false,
                ),
                $field_data['teacher']
              );
            ?>

            <div class="hidden-fields">
              <p class="info">Eligible teachers may receive 50% off their ticket for any Adventure in Ideas Seminars. Please complete the fields below.</p>
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

              <button class="ticket-update" data-ticket="<?php echo $field_prefix; ?>">Update Teacher Info</button>
              <button class="secondary cancel">Cancel</a>
            </div>

          </div>

        <?php
        } // End adventures-in-ideas-seminar

        // If this is an Adventures in Ideas Seminar, Dialogues Seminar, or Flyleaf Lecture (humanities-in-action):
        $matches = false;
        $bulk = false;
        foreach ($terms as $term) {
          $cats = [
            'adventures-in-ideas-seminar',
            'dialogues-seminar',
            'humanities-in-action'
          ];
          if (in_array($term->slug, $cats)) {
            $matches[0] = $term->slug;
          }

          if ($term->slug == 'season-pass') {
            $bulk = true;
          }
        }
        if (!empty($matches)) {
          ?>

          <div class="discount-validation" data-discount-type="gaa">
            <?php
              woocommerce_form_field( $field_prefix . '_gaa',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide', 'validation-checkbox'),
                  'label'         => __('This guest is a member of the UNC General Alumni Association. <a href="#" class="edit-discount hide">Edit details</a>'),
                  'required'      => false,
                ),
                $field_data['gaa']
              );
            ?>

            <div class="hidden-fields">
              <?php
                if ($matches[0] == 'humanities-in-action') {
                  if ($bulk == true) {
                    echo '<p class="info">Eligible GAA Members may opt-in to receive $35 off their Flyleaf Season Pass.</p>';

                    woocommerce_form_field( $field_prefix . '_gaa_discount_bulk_flyleaf',
                      array(
                        'type'          => 'checkbox',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Check this box to claim the GAA Discount and select your membership type below:'),
                        'required'      => false,
                      ),
                      $field_data['gaa_discount_bulk_flyleaf']
                    );
                  } else {
                    echo '<p class="info">Eligible GAA Members may opt-in to receive $5 off their ticket to a Humanities in Action series event.</p>';

                    woocommerce_form_field( $field_prefix . '_gaa_discount_flyleaf',
                      array(
                        'type'          => 'checkbox',
                        'class'         => array('form-row-wide'),
                        'label'         => __('Check this box to claim the GAA Discount and select your membership type below:'),
                        'required'      => false,
                      ),
                      $field_data['gaa_discount_flyleaf']
                    );
                  }
                } else {
                  echo '<p class="info">Eligible GAA Members may opt-in to receive $15 off their ticket for one Adventure in Ideas Seminar or Dialogues Seminar per semester.</p>';

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

              <button class="ticket-update" data-ticket="<?php echo $field_prefix; ?>">Update GAA Info</button>
              <button class="secondary cancel">Cancel</a>
            </div>

          </div>
        <?php } ?>
      </div>

    <?php
  }
}

//
// /**
//  * Sets errors for custom cart fields that are required
//  * @return null
//  */
// function cph_custom_cart_field_process() {
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
 * AJAX callback for adding per ticket attendee info to session
 * @return empty;
 */
add_action('wp_ajax_cph_update_cart_meta', 'cph_update_cart_meta_callback' );
add_action('wp_ajax_nopriv_cph_update_cart_meta', 'cph_update_cart_meta_callback' );
function cph_update_cart_meta_callback(){

  $errors = new WP_Error();

  // For each item in the cart, we're adding session data for the ticket details
  $cart_items = WC()->cart->get_cart_contents();
  foreach ($cart_items as $cart_item) {
    $this_session = array();
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    // Clear ticket session data each time
    WC()->session->set( $_product->get_ID() . '_tickets_data', null);

    // Set up array to put in session data
    $i = 1;
    foreach ($_POST['custom_fields'] as $ticket_details) {
      if ($ticket_details['product_id'] == $_product->get_ID()) {

        $field_label = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . ': Ticket ' . $i;

        // Validate fields
        if ( ! $ticket_details['first_name'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a first name for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['last_name'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a last name for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['address_1'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a street address for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['city'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a city for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['state'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a state for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['postcode'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter a zip code for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );
        if ( ! $ticket_details['email'] )
          $errors->add( 'required-field', apply_filters( 'woocommerce_checkout_required_field_notice', sprintf( __( 'Please enter an email address for %s.', 'woocommerce' ), '<strong>' . esc_html($field_label) . '</strong>' ), $field_label ) );

        // Set session data
        $this_session[$ticket_details['ticket_key']] = $ticket_details;

        $i++;
      }
    }

    // Set new session data
    WC()->session->set( $_product->get_ID() . '_tickets_data', $this_session );
  }

  // Handle errors
	foreach ( $errors->get_error_messages() as $message ) {
		wc_add_notice( $message, 'error' );
	}

  if (wc_notice_count( 'error' ) > 0) {
    ob_start();
  	wc_print_notices();
  	$messages = ob_get_clean();

		$response = array(
			'error'    => true,
			'messages' => isset( $messages ) ? $messages : ''
		);

    wp_send_json($response);
  }

  wp_die();
}


/**
 * Change select, checkboxes, and radio form fields to new UI
 */
function cph_form_field_select($field, $key, $args, $value) {
  $patterns = array(
                '/<\/label>/',
                '/<\/select>/'
              );
  $rplcmnts = array(
                '</label><span class="ui-control select">',
                '</select><span class="select_arrow"></span></span>'
              );
	return preg_replace($patterns, $rplcmnts, $field);
}

function cph_form_field_checkbox($field, $key, $args, $value) {
  return $field;
}

function cph_form_field_radio($field, $key, $args, $value) {
	return $field;
}
