<?php
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Total_Cart_Amount_Counter extends SFA_Abandoned_Carts_Counter {
	
	function __construct() {
		$this->title = 'Abandoned Amount';
		$this->id = 'sfa_total_abandoned_amount';
	}

	function calculate_data($data) {
		
		$total = 0.00;
		
		foreach($data as $cart) {
			if (!$cart->get_cart_is_recovered() && (time() - $cart->get_cart_expiry_raw()) > (15 * 60)) {
				$total += $cart->get_cart_total();
			}
		}
		
		$this->count = wc_price($total);
	}
}	
?>