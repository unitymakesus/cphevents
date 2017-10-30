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

          <div class="ticket-details <?php echo $field_prefix; ?>__field-wrapper" data-ticket-key="<?php echo $field_prefix; ?>">
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
