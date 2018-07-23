<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Abandoned_Chart extends SFA_Abandoned_Carts_Chart
{
	private $start_date;
	private $end_date;
	
	function __construct($start_date, $end_date) {
		$this->title = 'Abandoned Carts';
		$this->chart_div = 'abandoned_chart';
		
		$this->start_date = new DateTime($start_date);
		$this->start_date = $this->start_date->getTimestamp();
		
		$this->end_date = new DateTime($end_date);
		$this->end_date = $this->end_date->getTimestamp();
	}
	
	function calculate_data($data = array()) {
		$recovered_raw_data = array();
		$abandoned_raw_data = array();
		$recovered_count_raw_data = array();
		$abandoned_count_raw_data = array();
		$recovery_rate_raw_data = array();
		
		while ($this->start_date <= $this->end_date) {
			$recovered_raw_data[$this->start_date] = 0;
			$abandoned_raw_data[$this->start_date] = 0;
			$recovered_count_raw_data[$this->start_date] = 0;
			$abandoned_count_raw_data[$this->start_date] = 0;
			$recovery_rate_raw_data[$this->start_date] = 0;
			$this->start_date += 86400;
		}
		
		foreach ($data as $cart) {
			$bucket = $cart->get_cart_expiry_raw() - ($cart->get_cart_expiry_raw() % 86400);

			if ($cart->get_cart_is_recovered()) {
				$recovered_raw_data[$bucket] += $cart->get_cart_total();
			}
			else if ((time() - $cart->get_cart_expiry_raw()) < (15 * 60)) {
				continue;
			}
			else {
				$abandoned_raw_data[$bucket] += $cart->get_cart_total();
			}
			
			if ($cart->get_cart_is_recovered()) {
				$recovered_count_raw_data[$bucket] += 1;
			}
			else if ((time() - $cart->get_cart_expiry_raw()) < (15 * 60)) {
				continue;
			}
			else {
				$abandoned_count_raw_data[$bucket] += 1;		
			}
		}
		
		foreach ($recovered_count_raw_data as $key => $value) {
			$abandoned = $abandoned_count_raw_data[$key];
			
			if ($abandoned == 0 && $value == 0) {
				$recovery_rate_raw_data[$key] = 0;
			}
			else if ($abandoned > 0 && $value == 0) {
				$recovery_rate_raw_data[$key] = 0;
			}
			else if ($abandoned > 0 && $value > 0) {
				$recovery_rate_raw_data[$key] = $value / ($value + $abandoned) * 100;
			}
			else if ($abandoned == 0 && $value > 0) {
				$recovery_rate_raw_data[$key] = 100;
			}
		}
		
		$recovered_data = '[';
		$abandoned_data = '[';
		$recovered_count_data = '[';
		$abandoned_count_data = '[';
		$recovery_rate_data = '[';
		
		foreach($recovered_raw_data as $key => $value) {
			$recovered_data = $recovered_data . '[' . $key * 1000 . ',' . $value .'],';
		}
		
		foreach ($abandoned_raw_data as $key => $value) {
			$abandoned_data = $abandoned_data . '[' . $key * 1000 . ',' . $value .'],';
		}
	
		foreach($recovered_count_raw_data as $key => $value) {
			$recovered_count_data = $recovered_count_data . '[' . $key * 1000 . ',' . $value .'],';
		}
		
		foreach ($abandoned_count_raw_data as $key => $value) {
			$abandoned_count_data = $abandoned_count_data . '[' . $key * 1000 . ',' . $value .'],';
		}
		
		foreach ($recovery_rate_raw_data as $key => $value) {
			$recovery_rate_data = $recovery_rate_data . '[' . $key * 1000 . ',' . $value .'],';
		}
		
		$this->recovered_data = rtrim($recovered_data, ',') . ']';
		$this->abandoned_data = rtrim($abandoned_data, ',') . ']';
		$this->recovered_count_data = rtrim($recovered_count_data, ',') . ']';
		$this->abandoned_count_data = rtrim($abandoned_count_data, ',') . ']';
		$this->recovery_rate_data = rtrim($recovery_rate_data, ',') . ']';
	}
}
?>