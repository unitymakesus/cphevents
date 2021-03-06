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
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<ol class="checkout-progress" tabindex="0" role="progressbar"
		aria-valuemin="1" aria-valuemax="4"
		aria-valuenow="1" aria-valuetext="Step 1 of 4: Ticket Info">
	<li aria-hidden="true" data-step-current>Ticket Info</li>
	<li aria-hidden="true" data-step-incomplete>Review Order</li>
	<li aria-hidden="true" data-step-incomplete>Payment</li>
	<li aria-hidden="true" data-step-incomplete>Complete</li>
</ol>

</div>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<div class="woocommerce-NoticeGroup">
		<?php wc_print_notices(); ?>
	</div>

	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<div id="guest-data" style="display: none;">
				<?php
				// Get customer details to show in hidden div for jQuery copy ticket info
				$user_id = get_current_user_id();
			  $customer = WC()->session->get('customer');
			  if (!empty($customer['address_1'])) {
					$teacher_type = get_field('teacher_type', "user_{$user_id}");
					$gaa_type = get_field('gaa_type', "user_{$user_id}");
			    ?>
						<div class="customer" data-ticket-name="<?php echo sanitize_title_with_dashes($customer['first_name'] . ' ' . $customer['last_name']); ?>"
			           data-first_name="<?php echo $customer['first_name']; ?>"
			           data-last_name="<?php echo $customer['last_name']; ?>"
			           data-address_1="<?php echo $customer['address_1']; ?>"
			           data-address_2="<?php echo $customer['address_2']; ?>"
			           data-city="<?php echo $customer['city']; ?>"
			           data-state="<?php echo $customer['state']; ?>"
			           data-postcode="<?php echo $customer['postcode']; ?>"
			           data-phone="<?php echo $customer['phone']; ?>"
			           data-email="<?php echo $customer['email']; ?>"
								 data-special_needs="<?php echo get_field('special_needs', "user_{$user_id}"); ?>"
								 data-teacher="<?php echo get_field('teacher', "user_{$user_id}"); ?>"
								 data-teacher_type="<?php if (!empty($teacher_type)) echo $teacher_type; ?>"
								 data-teacher_school="<?php echo get_field('teacher_school', "user_{$user_id}"); ?>"
								 data-teacher_county="<?php echo get_field('teacher_county', "user_{$user_id}"); ?>"
								 data-gaa="<?php echo get_field('gaa', "user_{$user_id}"); ?>"
								 data-gaa_type="<?php if (!empty($gaa_type)) echo $gaa_type; ?>"></div>
		    	<?php
				}

				// Get guest details to show in hidden div for jQuery copy ticket info
				if (have_rows('guests', "user_{$user_id}")) {
					while (have_rows('guests', "user_{$user_id}")) {
						the_row();
						$teacher_type = get_sub_field('teacher_type');
						$gaa_type = get_sub_field('gaa_type');
						?>
							<div class="guest" data-ticket-name="<?php echo sanitize_title_with_dashes(get_sub_field('first_name') . ' ' . get_sub_field('last_name')); ?>"
				           data-first_name="<?php echo get_sub_field('first_name'); ?>"
				           data-last_name="<?php echo get_sub_field('last_name'); ?>"
				           data-address_1="<?php echo get_sub_field('address_1'); ?>"
				           data-address_2="<?php echo get_sub_field('address_2'); ?>"
				           data-city="<?php echo get_sub_field('city'); ?>"
				           data-state="<?php echo get_sub_field('state'); ?>"
				           data-postcode="<?php echo get_sub_field('postcode'); ?>"
				           data-phone="<?php echo get_sub_field('phone'); ?>"
				           data-email="<?php echo get_sub_field('email'); ?>"
				           data-special_needs="<?php echo get_sub_field('special_needs'); ?>"
				           data-teacher="<?php echo get_sub_field('teacher'); ?>"
				           data-teacher_type="<?php if (!empty($teacher_type)) echo $teacher_type; ?>"
				           data-teacher_school="<?php echo get_sub_field('teacher_school'); ?>"
				           data-teacher_county="<?php echo get_sub_field('teacher_county'); ?>"
				           data-gaa="<?php echo get_sub_field('gaa'); ?>"
				           data-gaa_type="<?php if (!empty($gaa_type)) echo $gaa_type; ?>"></div>
			    	<?php
					}
				}
				?>
			</div>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>

					<div class="ticket-details-wrapper">
						<?php
							// Event title
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<h3>%s</h3>', $_product->get_name() ), $cart_item, $cart_item_key ) );

							echo '<span class="category">';
							$terms = wp_get_post_terms($product_id, 'product_cat');
							echo $terms[0]->name;
							echo '</span>';
							echo '<span class="date">';
							echo get_post_meta($product_id, 'display_date', true);
							echo '</span>';
							// Meta data
							echo wc_get_formatted_cart_item_data( $cart_item );
							// echo WC()->cart->get_item_data( $cart_item );

              // Custom fields
              cph_custom_product_fields( $cart_item, $cart_item_key );

							// Backorder notification
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
								echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>' ) );
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
							<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php } ?>

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
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
