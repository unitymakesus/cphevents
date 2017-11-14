<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			// Get customer details to show in hidden div for jQuery copy ticket info
		  $customer = WC()->session->get('customer');
		  if (!empty($customer['address_1'])) {
		    ?>
		      <div id="account-data" style="display: none;"
		           data-first_name="<?php echo $customer['first_name']; ?>"
		           data-last_name="<?php echo $customer['last_name']; ?>"
		           data-address_1="<?php echo $customer['address_1']; ?>"
		           data-address_2="<?php echo $customer['address_2']; ?>"
		           data-city="<?php echo $customer['city']; ?>"
		           data-state="<?php echo $customer['state']; ?>"
		           data-postcode="<?php echo $customer['postcode']; ?>"
		           data-phone="<?php echo $customer['phone']; ?>"
		           data-email="<?php echo $customer['email']; ?>"
		      ></div>
		    <?php
		  }

			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>

					<div class="ticket-details-wrapper">
						<?php
							// Event title
							echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<h3>%s</h3>', $_product->get_name() ), $cart_item, $cart_item_key );

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

              // Custom fields
              cph_custom_product_fields( $cart_item, $cart_item_key );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							}
						?>
					</div>

					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<div>
				<div class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">
							<label for="coupon_code"><?php _e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>" />
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</div>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<div class="cart-collaterals">
	<?php
		/**
		 * woocommerce_cart_collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
	 	do_action( 'woocommerce_cart_collaterals' );
	?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
