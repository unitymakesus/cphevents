<?php

/**
* Displays custom field values on the order details page
*/
function cph_order_details_tickets( $item_id, $item, $order ) {
  $data = $item->get_data();
  $order_id = $data['order_id'];
  $post_meta = get_post_meta($order_id);

  $_product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
  ?>

  <div class="ticket-details-wrapper">
    <?php
    for ($i = 1; $i <= $item->get_quantity(); $i++) {
      $field_prefix = $_product->get_id() . '_ticket_' . $i;

      $first_name = (isset($post_meta[$field_prefix . '_first_name']) ? $post_meta[$field_prefix . '_first_name'][0] : null);
      $last_name = (isset($post_meta[$field_prefix . '_last_name']) ? $post_meta[$field_prefix . '_last_name'][0] : null);
      $address_1 = (isset($post_meta[$field_prefix . '_address_1']) ? $post_meta[$field_prefix . '_address_1'][0] : null);
      $address_2 = (isset($post_meta[$field_prefix . '_address_2']) ? $post_meta[$field_prefix . '_address_2'][0] . '<br />' : null);
      $city = (isset($post_meta[$field_prefix . '_city']) ? $post_meta[$field_prefix . '_city'][0] : null);
      $state = (isset($post_meta[$field_prefix . '_state']) ? $post_meta[$field_prefix . '_state'][0] : null);
      $postcode = (isset($post_meta[$field_prefix . '_postcode']) ? $post_meta[$field_prefix . '_postcode'][0] : null);
      $phone = (isset($post_meta[$field_prefix . '_phone']) ? $post_meta[$field_prefix . '_phone'][0] : null);
      $email = (isset($post_meta[$field_prefix . '_email']) ? $post_meta[$field_prefix . '_email'][0] : null);
      $special_needs = (isset($post_meta[$field_prefix . '_special_needs']) ? $post_meta[$field_prefix . '_special_needs'][0] : null);
      $teacher = (isset($post_meta[$field_prefix . '_teacher']) ? $post_meta[$field_prefix . '_teacher'][0] : null);
      $teacher_type = (isset($post_meta[$field_prefix . '_teacher_type']) ? $post_meta[$field_prefix . '_teacher_type'][0] : null);
      $teacher_school = (isset($post_meta[$field_prefix . '_teacher_school']) ? $post_meta[$field_prefix . '_teacher_school'][0] : null);
      $teacher_county = (isset($post_meta[$field_prefix . '_teacher_county']) ? $post_meta[$field_prefix . '_teacher_county'][0] : null);
      $gaa = (isset($post_meta[$field_prefix . '_gaa']) ? $post_meta[$field_prefix . '_gaa'][0] : null);
      $gaa_type = (isset($post_meta[$field_prefix . '_gaa_type']) ? $post_meta[$field_prefix . '_gaa_type'][0] : null);
      $gaa_discount_flyleaf = (isset($post_meta[$field_prefix . '_gaa_discount_flyleaf']) ? $post_meta[$field_prefix . '_gaa_discount_flyleaf'][0] : null);
      $gaa_discount_bulk_flyleaf = (isset($post_meta[$field_prefix . '_gaa_discount_bulk_flyleaf']) ? $post_meta[$field_prefix . '_gaa_discount_bulk_flyleaf'][0] : null);
      $gaa_discount_seminar = (isset($post_meta[$field_prefix . '_gaa_discount_seminar']) ? $post_meta[$field_prefix . '_gaa_discount_seminar'][0] : null);
      ?>

      <div class="ticket-details">
        <p><strong>Ticket <?php echo $i; ?>:</strong> <?php echo $first_name; ?> <?php echo $last_name; ?></p>
        <p><strong>Address:</strong><br />
          <?php echo $address_1; ?><br />
          <?php echo $address_2; ?>
          <?php echo $city; ?>, <?php echo $state; ?> <?php echo $postcode; ?>
        </p>
        <p><strong>Phone:</strong><br /><?php echo $phone; ?></p>
        <p><strong>Email address:</strong><br /><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></p>

        <?php if (!empty($teacher)) { ?>
          <p><strong>Teacher:</strong><br /><?php echo $teacher_type; ?></p>
          <p><strong>School:</strong><br /><?php echo $teacher_school; ?></p>
          <p><strong>County:</strong><br /><?php echo $teacher_county; ?></p>
          <p><strong>Discount:</strong><br /><?php echo '50% off Adventures in Ideas Seminars'; ?></p>
        <?php } ?>

        <?php if (!empty($gaa)) { ?>
          <p><strong>GAA Member:</strong><br /><?php echo $gaa_type; ?></p>
          <p><strong>Discount:</strong><br />
            <?php
            if (!empty($gaa_discount_flyleaf)) {
              echo '$5 off Humanities in Action series events';
            }
            if (!empty($gaa_discount_bulk_flyleaf)) {
              echo '$35 off Flyleaf Season Pass';
            }
            if (!empty($gaa_discount_seminar)) {
              echo '$15 off Adventures in Ideas or Dialogue Seminars';
            }
            ?>
          </p>
        <?php } ?>

        <?php if (!empty($special_needs)) { ?>
          <p><strong>Special needs:</strong><br /><?php echo $special_needs; ?></p>
        <?php } ?>
      </div>

      <?php
    }
    ?>
  </div>
  <?php
}
