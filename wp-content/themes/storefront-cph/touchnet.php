<?php
/**
 * WooCommerce TouchNet Payment Gateway class.
 *
 * Extended to handle payments through UNC's TouchNet.
 *
 * @class       WC_Gateway_Touchnet
 * @extends     WC_Payment_Gateway
 */

class WC_Gateway_Touchnet extends WC_Payment_Gateway {
  /**
   * Set up class and define required variables
   */
  public function __construct() {
    $this->id = 'touchnet';
    $this->title = 'UNC TouchNet';
    $this->method_title = 'UNC TouchNet';
    $this->method_description = 'Custom payment gateway that sends form information to the UNC TouchNet payment processing system.';
    $this->has_fields = false;

    // Load settings
    $this->init_form_fields();
    $this->init_settings();
    $this->enabled = $this->get_option('enabled');

    add_action('check_touchnet', array($this, 'check_response'));

    // Save settings
    if (is_admin()) {
      // We're not doing anything special here, so let's just load the default method from the parent class instead
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }
  }

  /**
   * Configure options to show in admon on gateway settings page
   */
  public function init_form_fields() {
  	$this->form_fields = array(
  		'enabled' => array(
  			'title' => 'Enable',
  			'type' => 'checkbox',
  			'label' => 'Enable UNC TouchNet',
  			'default' => 'yes'
  		)
  	);
  }

  /**
   * Process the payment
   * @param  int $order_id
   * @return array
   */
  public function process_payment( $order_id ) {
    $order = new WC_Order($order_id);

    // Encode validation key
    $upay_posting_key = 'EX123';
    $trans_id = str_replace( "#", "", $order->get_order_number() );
    $amount = $order->get_total();
    $validation_key = base64_encode(md5($upay_posting_key + $trans_id + $amount, TRUE));

    // Build payload
    $payload = array(
      'UPAY_SITE_ID' => 52,
      'BILL_EMAIL_ADDRESS' => $order->get_billing_email(),
      'BILL_NAME' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
      'BILL_STREET1' => $order->get_billing_address_1(),
      'BILL_STREET2' => $order->get_billing_address_2(),
      'BILL_CITY' => $order->get_billing_city(),
      'BILL_STATE' => $order->get_billing_state(),
      'BILL_POSTAL_CODE' => $order->get_billing_postcode(),
      'BILL_COUNTRY' => 'US',
      'AMT' => $amount,
      'EXT_TRANS_ID' => $trans_id,
      'VALIDATION_KEY' => $validation_key,
      'SUCCESS_LINK' => home_url() . '/my-account/view-order/' . $trans_id,
      'SUCCESS_LINK_TEXT' => 'Thank you for your order. Click here to view order summary.',
      // 'ERROR_LINK' => [LINK TO ERROR PAGE TO GO BACK TO CART],
      // 'ERROR_LINK_TEXT' => 'There was an error. You may return to your cart.',
      // 'CANCEL_LINK' => [LINK TO GO BACK TO CART],
      // 'CANCEL_LINK_TEXT' => 'Back to cart'
    );

    // Build redirect URL
    $redirect_url = get_permalink(get_page_by_path('redirecting'))  . '?' . http_build_query($payload);

    // Mark as on-hold
    $order->update_status('pending', 'Awaiting payment through TouchNet');

    // Reduce stock levels
    $order->reduce_order_stock();

    // Remove cart
    WC()->cart->empty_cart();

    return array(
      'result' => 'success',
      'redirect' => $redirect_url
    );
  }
}
