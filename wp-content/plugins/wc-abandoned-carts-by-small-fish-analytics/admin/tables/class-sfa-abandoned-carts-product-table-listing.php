<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Product_Table_Listing {
	
	public $count;
	public $total;
	public $title;
	
	function __construct($title, $price, $count) {
		$this->title = $title;
		$this->total = $price;
		$this->count = $count;
	}
	
	public function add($price, $count) {
		$this->total += $price;
		$this->count += $count;
	}
}

?>