<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
	
class SFA_Abandoned_Carts_Recovery_Rate_Counter extends SFA_Abandoned_Carts_Counter {
	
	function __construct() {
		$this->title = "Recovery Rate";
		$this->id = "sfa_total_recovery_rate";
	}
	
	public function calculate_data($data) {
		$total_carts = 0;
		$recovered_carts = 0;
		
		foreach($data as $cart) {
			$total_carts++;
			
			if ($cart->get_cart_is_recovered()) {
				$recovered_carts++;
			}
		}
		
		if ($total_carts > 0) {
			$this->count = round($recovered_carts / $total_carts * 100, 2) . '%';
		}
		else {
			$this->count = 0;
		}
	}
}
?>