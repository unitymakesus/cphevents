<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
	
class SFA_Abandoned_Carts_Total_Carts_Counter extends SFA_Abandoned_Carts_Counter {
	
	function __construct() {
		$this->title = 'Abandoned Carts';
		$this->id = 'sfa_total_abandoned_count';
	}

	function calculate_data($data) {
		$count = 0;
		
		foreach($data as $cart) {
			if (!$cart->get_cart_is_recovered() && (time() - $cart->get_cart_expiry_raw()) > (15 * 60)) {
				$count++;
			}
		}
		
		$this->count = number_format($count);
	}
}	
?>