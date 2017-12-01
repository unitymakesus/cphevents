<?php
/**
 * This is the template for the uPay posting URL.
 *
 * It collects the parameters describing the transaction from uPay and updates
 * the corresponding order accordingly.
 */

// Set up order object
$order = new WC_Order($_POST['EXT_TRANS_ID']);

// Handle order status
if ($_POST['pmt_status'] == 'success') {
	$order->update_status('complete', 'TouchNet payment completed.');
} elseif ($_POST['pmt_status'] == 'cancelled') {
	$order->add_order_note('TouchNet payment cancelled.');
}

// Save post stuff as order meta
if ( ! empty( $_POST['pmt_status'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'pmt_status', $_POST['pmt_status'] );
if ( ! empty( $_POST['tpg_trans_id'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'tpg_trans_id', $_POST['tpg_trans_id'] );
if ( ! empty( $_POST['pmt_amt'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'pmt_amt', $_POST['pmt_amt'] );
if ( ! empty( $_POST['pmt_status'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'pmt_status', $_POST['pmt_status'] );
if ( ! empty( $_POST['pmt_date'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'pmt_date', $_POST['pmt_date'] );
if ( ! empty( $_POST['sys_tracking_id'] ) )
	update_post_meta( $_POST['EXT_TRANS_ID'], 'sys_tracking_id', $_POST['sys_tracking_id'] );
