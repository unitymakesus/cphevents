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
      ?>

      <div class="ticket-details">
        <p><strong>Ticket <?php echo $i; ?>:</strong> <?php echo $post_meta[$field_prefix . '_first_name'][0]; ?> <?php echo $post_meta[$field_prefix . '_last_name'][0]; ?></p>
        <p><strong>Address:</strong><br />
          <?php echo $post_meta[$field_prefix . '_address_1'][0]; ?><br />
          <?php if ($address_2 = $post_meta[$field_prefix . '_address_2'][0]) echo $address_2 . '<br />'; ?>
          <?php echo $post_meta[$field_prefix . '_city'][0]; ?>, <?php echo $post_meta[$field_prefix . '_state'][0]; ?> <?php echo $post_meta[$field_prefix . '_postcode'][0]; ?>
        </p>
        <p><strong>Phone:</strong><br /><?php echo $post_meta[$field_prefix . '_phone'][0]; ?></p>
        <p><strong>Email address:</strong><br /><a href="mailto:<?php echo $post_meta[$field_prefix . '_email'][0]; ?>"><?php echo $post_meta[$field_prefix . '_email'][0]; ?></a></p>
        <?php if ($special_needs = $post_meta[$field_prefix . '_special_needs'][0]) { ?>
          <p><strong>Special needs:</strong><br /><?php echo $special_needs; ?></p>
        <?php } ?>
      </div>

      <?php
    }
    ?>
  </div>
  <?php
}
