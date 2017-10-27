<?php
/**
 * Get quantity of product already in cart
 * @param  object product     WooCommerce global variable
 * @param  int    $variation  ID of product variation
 * @return int                Number of this product in cart
 */
function product_in_cart($product, $variation_id = null) {
  global $woocommerce;
  foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
    if($cart_item['product_id'] == $product->get_id() && $cart_item['variation_id'] == $variation_id ){
      $qty =  $cart_item['quantity'];
      break; // stop the loop if product is found
    }
  }
  return $qty;
}

/**
 * Display quantity picker for this event
 * @param  int    $qty    Number of tickets currently in cart
 * @return string         HTML of quantity picker
 */
function quantity_picker($qty) {
  ob_start();
  ?>
  <div class="cph-quantity">
    <input type="number" min="0" step="1" value="<?php echo $qty; ?>" />
    <div class="cph-quantity-nav">
      <div class="cph-quantity-up">+</div>
      <div class="cph-quantity-down">-</div>
    </div>
  </div>
  <?php
  return ob_get_flush();
}

function cph_event_list_quantity_picker() {
  global $product;

  // If product is variable
  if ($product->is_type('variable')) {
    $variations = $product->get_available_variations();
    $atts = $product->get_variation_attributes();
    $atts_keys = array_keys($atts);
    $product_id = $product->get_id();

    $terms = wc_get_product_terms( $product_id, $atts_keys[0], array( 'fields' => 'all' ) );

    echo '<ul>';

    foreach ($variations as $variation) {
      echo '<li>';

        // Set up new array for variation attributes
        $var_atts = array();

        foreach ($terms as $key => $term) {
          // Display variation name
          if ($term->slug == $variation['attributes']['attribute_' . $atts_keys[0]]) {
            echo $term->name;
          }
        }

        $qty = product_in_cart($product, $variation['variation_id']);
        if ($qty !== NULL) {
          // Show quantity picker
          quantity_picker($qty);
        } else {
          // Show add to cart for this variation
          ob_start();
          ?>

          <a rel="nofollow"
             href="/?add-to-cart=<?php echo $product_id; ?>"
             data-quantity="1"
             data-product_id="<?php echo $product_id; ?>"
             data-variation_id="<?php echo $variation['variation_id']; ?>"
             data-variation='<?php echo json_encode($variation['attributes']); ?>'
             class="button product_type_variable add_to_cart_button">Add to cart</a>

          <?php
          ob_end_flush();
        }
      echo '</li>';
    }

    echo '</ul>';
  } else {
    $qty = product_in_cart($product);
    if ($qty !== NULL) {
      // Show quantity picker
      quantity_picker($qty);
    } else {
      // Show default add to cart
      woocommerce_template_loop_add_to_cart();
    }
  }
}

/**
 * AJAX callback for adding variable items to cart
 * Borrowed from https://github.com/wp-plugins/woocommerce-ajax-add-to-cart-for-variable-products/ by Rcreators
 */
add_action('wp_ajax_cph_variable_add_to_cart', 'cph_variable_add_to_cart_callback' );
function cph_variable_add_to_cart_callback() {

  $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
  $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
  $variation_id = $_POST['variation_id'];
  $variation = $_POST['variation'];
  $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

  // If item is successfully added to the cart
  if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {

    do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
			wc_add_to_cart_message( $product_id );
		}

		// Return fragments
		ob_start();
		woocommerce_mini_cart();
		$mini_cart = ob_get_clean();
		$data = array(
			'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
					'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
				)
			),
			'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() ),
		);
    echo json_encode( $data );

  } else {

    // If there was an error adding to the cart, redirect to the product page to show any errors
    $data = array(
  		'error' => true,
  		'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
  		);
  	echo json_encode( $data );
  }

	die();
}
