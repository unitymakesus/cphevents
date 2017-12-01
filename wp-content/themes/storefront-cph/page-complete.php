<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package storefront
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post();

				do_action( 'storefront_page_before' );
        ?>

        <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <header class="entry-header">
            <h1 class="entry-title">Checkout</h1>

            <ol class="checkout-progress" tabindex="0" role="progressbar"
            		aria-valuemin="1" aria-valuemax="4"
            		aria-valuenow="1" aria-valuetext="Step 1 of 4: Attendee Information">
            	<li aria-hidden="true" data-step-complete>Attendee Info</li>
            	<li aria-hidden="true" data-step-complete>Review Order</li>
            	<li aria-hidden="true" data-step-complete>Payment</li>
            	<li aria-hidden="true" data-step-current>Complete</li>
            </ol>
          </header>

          <div class="entry-content">
            <?php
              $order = new WC_Order($_GET['EXT_TRANS_ID']);
            ?>

            <p>
              <?php
              printf(
            		__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
            		'<mark class="order-number">' . $order->get_order_number() . '</mark>',
            		'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>',
            		'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>'
            	);
              ?>
              <a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>/orders">View all my orders.</a>
            </p>

            <?php
              // Show order details
              do_action( 'woocommerce_view_order', $order->get_order_number() );
            ?>
          </div>

        </div>

        <?php
				/**
				 * Functions hooked in to storefront_page_after action
				 *
				 * @hooked storefront_display_comments - 10
				 */
				do_action( 'storefront_page_after' );

			endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
do_action( 'storefront_sidebar' );
get_footer();
