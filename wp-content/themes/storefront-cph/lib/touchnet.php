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
  		),
      'touchnet_url' => array(
        'title' => 'TouchNet URL',
        'type' => 'text'
  		),
      'site_id' => array(
        'title' => 'uPay Site ID',
        'type' => 'text'
  		),
      'posting_key' => array(
        'title' => 'uPay Posting Key',
        'type' => 'text'
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
    $upay_posting_key = $this->settings['posting_key'];
    $trans_id = str_replace( "#", "", $order->get_order_number() );
    $amount = $order->get_total();
    $validation_key = base64_encode(md5($upay_posting_key . $trans_id . $amount, TRUE));

    // Build payload
    $payload = array(
      'UPAY_SITE_ID' => $this->settings['site_id'],
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
    );

    // Build redirect URL
    $redirect_url = get_permalink(get_page_by_path('checkout/redirecting'))  . '?' . http_build_query($payload);

    // Mark as pending
    $order->update_status('processing', 'Awaiting payment through TouchNet.');

    // Remove cart
    WC()->cart->empty_cart();

    return array(
      'result' => 'success',
      'redirect' => $redirect_url
    );
  }
}
