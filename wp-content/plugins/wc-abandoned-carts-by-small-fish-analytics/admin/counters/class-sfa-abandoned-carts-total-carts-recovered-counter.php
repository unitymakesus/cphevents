<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
	
class SFA_Abandoned_Carts_Total_Carts_Recovered_Counter extends SFA_Abandoned_Carts_Counter {
	
	function __construct() {
		$this->title = 'Recovered Carts';
		$this->id = 'sfa_total_recovered_count';
	}

	function calculate_data($data) {
		$count = 0;
		
		foreach($data as $cart) {
			if ($cart->get_cart_is_recovered()) {
				$count++;
			}
		}
		
		$this->count = number_format($count);
	}
}	
?>