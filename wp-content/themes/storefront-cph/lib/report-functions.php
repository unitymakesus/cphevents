<?php
/**
 * Add link to WC admin menu
 */

add_action('admin_menu', function() {
  add_submenu_page('woocommerce', 'Event Ticket Reports', 'Event Ticket Reports', 'manage_options', 'event-ticket-reports', 'event_ticket_reports_page_callback');
});


/**
 * Report page layout callback function
 * @return string HTML layout
 */
function event_ticket_reports_page_callback() {
  ?>
  <div class="wrap">
    <h1>Event Ticket Reports</h1>

    <?php
    // Display single event?
    if (isset($_GET['eid'])) {
      ?>
      <a href="<?php echo esc_url(remove_query_arg('eid')); ?>">Back</a>

      <?php
      do_settings_sections( 'cphevents_single_event');

    } else {
      // Display list of events

      // we check if the page is visited by click on the tabs or on the menu button.
      // then we get the active tab.
      $active_tab = "upcoming";
      if (isset($_GET["tab"])) {
        if ($_GET["tab"] == "upcoming") {
          $active_tab = "upcoming";
        } else {
          $active_tab = "previous";
        }
      }
      ?>

      <h2 class="nav-tab-wrapper">
        <a href="<?php echo esc_url(add_query_arg('tab', 'upcoming')); ?>" class="nav-tab <?php if($active_tab == 'upcoming'){echo 'nav-tab-active';} ?> "><?php _e('Upcoming Events', 'cphevents'); ?></a>
        <a href="<?php echo esc_url(add_query_arg('tab', 'previous')); ?>" class="nav-tab <?php if($active_tab == 'previous'){echo 'nav-tab-active';} ?>"><?php _e('Previous Events', 'cphevents'); ?></a>
      </h2>

      <?php
      do_settings_sections( 'cphevents_list' );

    }
  ?>
  </div>
<?php
}


/**
 * Create Settings UI for upcoming/previous events
 */
add_action( 'admin_init', function() {

 register_setting( 'pluginPage', 'cphevents_settings' );

 if (isset($_GET["eid"])) {
   add_settings_section(
     'cphevents_attendees',
     __( '', 'cphevents' ),
     'cphevents_single_event_callback',
     'cphevents_single_event'
   );
 }

 // Here we display the sections and options in the settings page based on the active tab
 if (isset($_GET["tab"])) {
   if ($_GET["tab"] == "upcoming") {
     $section_id = 'cphevents_upcoming';
   } else {
     $section_id = 'cphevents_previous';
   }
 } else {
   $section_id = 'cphevents_upcoming';
 }

 add_settings_section(
   $section_id,
   __( '', 'cphevents' ),
   'cphevents_list_callback',
   'cphevents_list'
 );

});


/**
 * List of upcoming or previous events
 * @param  string $section (cphevents_upcoming, cphevents_previous)
 * @return string HTML layout
 */
function cphevents_list_callback( $section ) {
  ?>
  <table class="wp-list-table widefat fixed striped cph-events">
    <thead>
      <tr>
        <th scope="col" id="title" class="manage-column column-title column-primary">Title</th>
        <th scope="col" id="date" class="manage-column column-date">Date</th>
        <th scope="col" id="attendees" class="manage-column column-tickets">Tickets Sold</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $args = array(
        'post_type' => 'product',
        'posts_per_page' => '-1',
        'meta_key' => 'date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_query' => [
          [
            'key' => 'date',
            'value' => current_time('Ymd'),
            'type' => 'NUMERIC'
          ]
        ]
      );
      if ($section['id'] == 'cphevents_upcoming') {
        $args['meta_query'][0]['compare'] = '>';
      } elseif ($section['id'] == 'cphevents_previous') {
        $args['meta_query'][0]['compare'] = '<=';
      }

      // Set up and call our query.
      // $events = new Eventbrite_Query_Custom( apply_filters( 'eventbrite_query_args', $args ) );
      $events = new WP_Query($args);
      ?>
      <pre style="display:none"><?php print_r($events); ?></pre>
      <?php

      if ( $events->have_posts() ) : while ( $events->have_posts() ) : $events->the_post();
        ?>
        <tr>
          <td class="title column-title column-primary row-title">
            <?php the_title(); ?>
          </td>

          <td class="date column-date">
            <?php echo get_post_meta(get_the_ID(), 'display_date', true); ?>
          </td>

          <td class="attendees column-attendees">
            <?php
              $quantity = get_post_meta(get_the_ID(), 'total_sales', true);

              if ($quantity > 0) {
                echo '<a href="' . esc_url(add_query_arg('eid', get_the_id())) . '" class="row-title">';
              }

              echo $quantity;

              if ($quantity > 0) {
                echo '</a>';
              }
            ?>
          </td>
        </tr>
        <?php
      endwhile; endif; wp_reset_postdata();
      ?>
    </tbody>
  </table>
  <?php
}


/**
 * Single event layout with ticket information
 * @return string HTML layout
 */
function cphevents_single_event_callback() {
  $event_id = $_GET['eid'];
  $args = array(
    'post_type' => 'product',
    'post__in' => [$event_id]
  );
  $event = new WP_Query($args);

  if ( $event->have_posts() ) : while ( $event->have_posts() ) : $event->the_post();
    ?>
    <h2><?php the_title(); ?></h2>
    <p><?php echo get_post_meta($event_id, 'display_date', true); ?></p>

    <table class="wp-list-table widefat fixed striped cph-events">
      <thead>
        <tr>
          <th scope="col" id="order" class="manage-column column-order">Order</th>
          <th scope="col" id="guest" class="manage-column column-guest column-primary">Guest</th>
          <th scope="col" id="details" class="manage-column column-details">Details</th>
        </thead>

        <tbody>
          <?php
          // Get all orders of this item
          $orders = get_orders_by_product($event_id);
          ?>
          <pre style="display:none"><?php print_r($orders); ?></pre>
          <?php
          foreach ($orders as $order) {
            $order_id = $order->get_id();
            $post_meta = get_post_meta($order_id);

            // Select only the items in this order that we want
            $items = $order->get_items();
            foreach ($items as $item) {
              if ($event_id == $item['product_id']) {
                $_product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

                // Loop through quantity of items to get individual tickets
                for ($i = 1; $i <= $item->get_quantity(); $i++) {
                  $field_prefix = $_product->get_id() . '_ticket_' . $i;
                  $variation_id = $item->get_variation_id();
                  ?>
                  <tr>
                    <td class="order column-order">
                      <a href="<?php echo get_edit_post_link($order_id); ?>">#<?php echo $order_id; ?></a> - <?php echo $order->get_status(); ?>
                    </td>
                    <td class="name column-name column-primary">
                      <p><strong>Name:</strong> <?php echo $post_meta[$field_prefix . '_first_name'][0]; ?> <?php echo $post_meta[$field_prefix . '_last_name'][0]; ?></p>
                      <p><strong>Address:</strong><br />
                        <?php echo $post_meta[$field_prefix . '_address_1'][0]; ?><br />
                        <?php if ($address_2 = $post_meta[$field_prefix . '_address_2'][0]) echo $address_2 . '<br />'; ?>
                        <?php echo $post_meta[$field_prefix . '_city'][0]; ?>, <?php echo $post_meta[$field_prefix . '_state'][0]; ?> <?php echo $post_meta[$field_prefix . '_postcode'][0]; ?>
                      </p>
                      <p><strong>Phone:</strong><br /><?php echo $post_meta[$field_prefix . '_phone'][0]; ?></p>
                      <p><strong>Email address:</strong><br /><a href="mailto:<?php echo $post_meta[$field_prefix . '_email'][0]; ?>"><?php echo $post_meta[$field_prefix . '_email'][0]; ?></a></p>
                    </td>
                    <td class="details column-details">
                      <?php
                      if (!empty($variation_id)) {
                        $var_obj = wc_get_product($variation_id);
                        $var_atts = $var_obj->get_variation_attributes();
                        $var_atts_keys = array_keys($var_atts);
                        $terms = wc_get_product_terms( $event_id, str_replace('attribute_','',$var_atts_keys[0]), array( 'fields' => 'all' ) );
                        echo '<p><strong>Variation:</strong> ';
                        foreach ($terms as $key => $term) {
                          if ($term->slug == $var_atts[$var_atts_keys[0]]) {
                            echo $term->name;
                          }
                        }
                        echo '</p>';
                      } ?>

                      <?php if ($teacher = $post_meta[$field_prefix . '_teacher'][0]) { ?>
                        <p><strong>Teacher:</strong><br /><?php echo $post_meta[$field_prefix . '_teacher_type'][0]; ?></p>
                        <p><strong>School:</strong><br /><?php echo $post_meta[$field_prefix . '_teacher_school'][0]; ?></p>
                        <p><strong>County:</strong><br /><?php echo $post_meta[$field_prefix . '_teacher_county'][0]; ?></p>
                        <p><strong>Discount:</strong><br /><?php echo '50% off Adventures in Ideas Seminars'; ?></p>
                      <?php } ?>

                      <?php if ($gaa = $post_meta[$field_prefix . '_gaa'][0]) { ?>
                        <p><strong>GAA Member:</strong><br /><?php echo $post_meta[$field_prefix . '_gaa_type'][0]; ?></p>
                        <p><strong>Discount:</strong><br />
                          <?php
                          if ($gaa_discount_flyleaf = $post_meta[$field_prefix . '_gaa_discount_flyleaf'][0]) {
                            echo '$5 off Humanities in Action series events';
                          }
                          if ($gaa_discount_bulk_flyleaf = $post_meta[$field_prefix . '_gaa_discount_bulk_flyleaf'][0]) {
                            echo '$35 off Flyleaf Season Pass';
                          }
                          if ($gaa_discount_seminar = $post_meta[$field_prefix . '_gaa_discount_seminar'][0]) {
                            echo '$15 off Adventures in Ideas or Dialogue Seminars';
                          }
                          ?>
                        </p>
                      <?php } ?>

                      <?php if ($special_needs = $post_meta[$field_prefix . '_special_needs'][0]) { ?>
                        <p><strong>Special needs:</strong><br /><?php echo $special_needs; ?></p>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php
                }
              }
            }
          }
        ?>
      </tbody>
    </table>

    <?php
  endwhile;
  else:
  echo 'No results';
  endif;
  wp_reset_postdata();
}

/**
* Get all orders given a Product ID.
*
* @global $wpdb
* @param integer $product_id The product ID.
* @return array An array of WC_Order objects.
*/
function get_orders_by_product( $product_id ) {

    global $wpdb;

    $table_posts = $wpdb->prefix . "posts";
    $table_items = $wpdb->prefix . "woocommerce_order_items";
    $table_itemmeta = $wpdb->prefix . "woocommerce_order_itemmeta";

    $raw = "SELECT $table_items.order_id
            FROM $table_itemmeta, $table_items, $table_posts
            WHERE $table_items.order_item_id = $table_itemmeta.order_item_id
            AND $table_items.order_id = $table_posts.ID
            AND $table_posts.post_status IN ( 'wc-completed' )
            AND $table_itemmeta.meta_key LIKE '_product_id'
            AND $table_itemmeta.meta_value = %d
            ORDER BY $table_items.order_item_id DESC";

    $sql = $wpdb->prepare( $raw, $product_id );

    return array_map(function ( $data ) {
        return wc_get_order( $data->order_id );
    }, $wpdb->get_results( $sql ) );

}
