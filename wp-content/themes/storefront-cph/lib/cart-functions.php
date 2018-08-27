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
  $user_id = get_current_user_id();

  // Get session data for this product
  $session_data = WC()->session->get( $_product->get_id() . '_tickets_data' );

  for ($i = 1; $i <= $cart_item['quantity']; $i++) {

    if ($_product->is_type('variation')) {
      $parent = $_product->get_parent_ID();
      $terms = wp_get_post_terms($parent, 'product_cat');
    } else {
      $terms = wp_get_post_terms($_product->get_ID(), 'product_cat');
    }

    foreach ($terms as $term) {
      $cats[] = $term->slug;
    }

    $field_prefix = $_product->get_id() . '_ticket_' . $i;
    $full_name = '';

    // If session data exists for this ticket...
    if (isset($session_data[$field_prefix])) {
      $field_data = $session_data[$field_prefix];

      $first_name = (isset($field_data['first_name']) ? $field_data['first_name'] : null);
      $last_name = (isset($field_data['last_name']) ? $field_data['last_name'] : null);
      $address_1 = (isset($field_data['address_1']) ? $field_data['address_1'] : null);
      $address_2 = (isset($field_data['address_2']) ? $field_data['address_2'] : null);
      $city = (isset($field_data['city']) ? $field_data['city'] : null);
      $state = (isset($field_data['state']) ? $field_data['state'] : null);
      $postcode = (isset($field_data['postcode']) ? $field_data['postcode'] : null);
      $phone = (isset($field_data['phone']) ? $field_data['phone'] : null);
      $email = (isset($field_data['email']) ? $field_data['email'] : null);
      $special_needs = (isset($field_data['special_needs']) ? $field_data['special_needs'] : null);
      $teacher = (isset($field_data['teacher']) ? $field_data['teacher'] : null);
      $teacher_type = (isset($field_data['teacher_type']) ? $field_data['teacher_type'] : null);
      $teacher_school = (isset($field_data['teacher_school']) ? $field_data['teacher_school'] : null);
      $teacher_county = (isset($field_data['teacher_county']) ? $field_data['teacher_county'] : null);
      $gaa = (isset($field_data['gaa']) ? $field_data['gaa'] : null);
      $gaa_type = (isset($field_data['gaa_type']) ? $field_data['gaa_type'] : null);
      $full_name = $first_name . ' ' . $last_name;
    }
    ?>

      <div class="ticket-details <?php if ($i % 2 == 0) { echo 'col-2'; } else { echo 'col-1'; } ?> <?php echo $field_prefix; ?>__field-wrapper" data-ticket-key="<?php echo $field_prefix; ?>" data-product="<?php echo $_product->get_id(); ?>" data-category="<?php echo implode($cats, ' '); ?>">
        <h4>Ticket <?php echo $i; ?></h4>

        <p class="form-row form-row-wide control-copy">
          <label for="<?php echo $field_prefix; ?>_ticket_name">Guest <abbr class="required" title="required">*</abbr></label>
          <span class="ui-control select">
            <select id="<?php echo $field_prefix; ?>_ticket_name" class="copy-data">
              <option>--Select One--</option>
              <?php
                // Session Prefill For Customer
                if ($customer_name == $full_name) {
                  $selected = 'selected="selected"';
                } else {
                  $selected = '';
                }
              ?>
              <option <?php echo $selected; ?> value="<?php echo sanitize_title_with_dashes($customer_name); ?>"><?php echo $customer_name; ?></option>

              <?php
                if (have_rows('guests', "user_{$user_id}")) {
        					while (have_rows('guests', "user_{$user_id}")) {
        						the_row();

                    $guest_name = get_sub_field('first_name') . ' ' . get_sub_field('last_name');

                    // Session Prefill For Additional Guests
                    if ($guest_name == $full_name) {
                      $selected = 'selected="selected"';
                    } else {
                      $selected = '';
                    }
                    ?>
                    <option <?php echo $selected; ?> value="<?php echo sanitize_title_with_dashes($guest_name); ?>"><?php echo $guest_name; ?></option>
                    <?php
                  }
                }
              ?>
              <option value="new">New Guest</option>
            </select>
            <span class="select_arrow"></span>
          </span>
          <a href="#" class="edit-guest <?php if (empty($full_name)) { echo 'hide'; } ?>">Edit guest info</a>
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
            ( !empty($first_name) ? $first_name : '' )
          );

          woocommerce_form_field( $field_prefix . '_last_name',
            array(
              'type'          => 'text',
              'class'         => array('form-row-last'),
              'label'         => __('Last name'),
              'required'      => true,
            ),
            ( !empty($last_name) ? $last_name : '' )
          );

          woocommerce_form_field( $field_prefix . '_address_1',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('Mailing address'),
              'placeholder'   => __('House number and street name'),
              'required'      => true,
            ),
            ( !empty($address_1) ? $address_1 : '' )
          );

          woocommerce_form_field( $field_prefix . '_address_2',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __(''),
              'placeholder'   => __('Apartment, suite, unit etc. (optional)'),
              'required'      => false,
            ),
            ( !empty($address_2) ? $address_2 : '' )
          );

          woocommerce_form_field( $field_prefix . '_city',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('Town / City'),
              'required'      => true,
            ),
            ( !empty($city) ? $city : '' )
          );

          woocommerce_form_field( $field_prefix . '_state',
            array(
              'type'          => 'state',
              'class'         => array('form-row-wide'),
              'label'         => __('State'),
              'required'      => true,
            ),
            ( !empty($state) ? $state : '' )
          );

          woocommerce_form_field( $field_prefix . '_postcode',
            array(
              'type'          => 'text',
              'class'         => array('form-row-wide'),
              'label'         => __('ZIP'),
              'required'      => true,
            ),
            ( !empty($postcode) ? $postcode : '' )
          );

          woocommerce_form_field( $field_prefix . '_phone',
            array(
              'type'          => 'text',
              'class'         => array('form-row-first'),
              'label'         => __('Phone'),
              'required'      => false,
            ),
            ( !empty($phone) ? $phone : '' )
          );

          woocommerce_form_field( $field_prefix . '_email',
            array(
              'type'          => 'text',
              'class'         => array('form-row-last'),
              'label'         => __('Email address'),
              'required'      => true,
            ),
            ( !empty($email) ? $email : '' )
          );

          woocommerce_form_field( $field_prefix . '_special_needs',
            array(
              'type'          => 'textarea',
              'class'         => array('form-row-wide'),
              'label'         => __('Please indicate any accommodations and/or services you require.'),
              'required'      => false,
            ),
            ( !empty($special_needs) ? $special_needs : '' )
          );
          ?>

          <div class="discount-validation" data-discount-type="teacher">
            <?php
              woocommerce_form_field( $field_prefix . '_teacher',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide', 'validation-checkbox'),
                  'label'         => __('This guest is a teacher/educator.'),
                  'required'      => false,
                ),
                ( !empty($teacher) ? $teacher : '' )
              );
            ?>
            <div class="conditional-fields">
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
                  ( isset($teacher_type) ? $teacher_type : '' )
                );

                woocommerce_form_field( $field_prefix . '_teacher_school',
                  array(
                    'type'          => 'text',
                    'class'         => array('form-row-wide'),
                    'label'         => __('School Name'),
                    'required'      => false,
                  ),
                  ( isset($teacher_school) ? $teacher_school : '' )
                );

                woocommerce_form_field( $field_prefix . '_teacher_county',
                  array(
                    'type'          => 'text',
                    'class'         => array('form-row-wide'),
                    'label'         => __('School County'),
                    'required'      => false,
                  ),
                  ( isset($teacher_county) ? $teacher_county : '' )
                );
              ?>
            </div>
          </div>

          <div class="discount-validation" data-discount-type="gaa">
            <?php
              woocommerce_form_field( $field_prefix . '_gaa',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide', 'validation-checkbox'),
                  'label'         => __('This guest is a member of the UNC General Alumni Association.'),
                  'required'      => false,
                ),
                ( isset($gaa) ? $gaa : '' )
              );
            ?>

            <div class="conditional-fields">
              <?php
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
                  ( isset($gaa_type) ? $gaa_type : '' )
                );
              ?>
            </div>
          </div>

          <button class="ticket-update" data-ticket="<?php echo $field_prefix; ?>">Update Guest Info</button>
          <button class="secondary cancel">Cancel</a>

        </div>

        <?php
        // Show GAA discount opt-in if this is an Adventures in Ideas Seminar or Dialogues Seminar:
        $matches = false;

        foreach ($terms as $term) {
          $cats = [
            'adventures-in-ideas-seminar',
            'dialogues-seminar',
          ];
          if (in_array($term->slug, $cats)) {
            $matches[0] = $term->slug;
          }
        }

        if (!empty($matches)) {
          ?>
          <div class="gaa-opt-in conditional-fields">
            <?php
              echo '<p class="info">Eligible GAA Members may opt-in to receive $15 off their ticket for one Adventure in Ideas Seminar <span style="text-decoration: underline;">or</span> one Dialogues Seminar per semester.</p>';

              woocommerce_form_field( $field_prefix . '_gaa_discount_seminar',
                array(
                  'type'          => 'checkbox',
                  'class'         => array('form-row-wide'),
                  'label'         => __('Check this box to claim the one-time GAA Discount.'),
                  'required'      => false,
                ),
                ( isset($field_data['gaa_discount_seminar']) ? $field_data['gaa_discount_seminar'] : '' )
              );
            ?>
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
  // $cart_items = WC()->cart->get_cart_contents();
  foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item) {
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
