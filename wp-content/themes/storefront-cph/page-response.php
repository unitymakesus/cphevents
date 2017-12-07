<?php
/**
 * This is the template for the uPay posting URL.
 *
 * It collects the parameters describing the transaction from uPay and updates
 * the corresponding order accordingly.
 */

// Set up order object
$order_id = $_REQUEST['EXT_TRANS_ID'];
$order = new WC_Order($order_id);

// Handle order status
if ($_REQUEST['pmt_status'] == 'success') {
	$order->update_status('complete', 'TouchNet payment completed. uPay Order ID: ' . $_REQUEST['sys_tracking_id']);
} elseif ($_REQUEST['pmt_status'] == 'cancelled') {
	$order->update_status('failed', 'TouchNet payment cancelled.');
}

// // Save post stuff as order meta
if ( ! empty( $_REQUEST['pmt_status'] ) )
	update_post_meta( $order_id, 'pmt_status', $_REQUEST['pmt_status'] );
if ( ! empty( $_REQUEST['tpg_trans_id'] ) )
	update_post_meta( $order_id, 'tpg_trans_id', $_REQUEST['tpg_trans_id'] );
if ( ! empty( $_REQUEST['pmt_amt'] ) )
	update_post_meta( $order_id, 'pmt_amt', $_REQUEST['pmt_amt'] );
if ( ! empty( $_REQUEST['pmt_status'] ) )
	update_post_meta( $order_id, 'pmt_status', $_REQUEST['pmt_status'] );
if ( ! empty( $_REQUEST['pmt_date'] ) )
	update_post_meta( $order_id, 'pmt_date', $_REQUEST['pmt_date'] );
if ( ! empty( $_REQUEST['sys_tracking_id'] ) )
	update_post_meta( $order_id, 'sys_tracking_id', $_REQUEST['sys_tracking_id'] );
