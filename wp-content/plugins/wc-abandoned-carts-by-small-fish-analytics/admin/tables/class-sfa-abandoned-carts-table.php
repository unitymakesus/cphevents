<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Table extends SFA_WP_List_Table
{
	function __construct($start_date, $end_date, $show_funnel_carts, $limit = null) {
		parent::__construct(array(
			'ajax' => false
		));
		
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->show_funnel_carts = $show_funnel_carts;
		$this->limit = $limit;
	}
	
	public $carts;
	public $start_date;
	public $end_date;
	private $show_funnel_carts;
	private $limit;
	
	function prepare_items() {
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($this->get_columns(), array(), $sortable);
		
		global $wpdb;
	
		$table_name = $wpdb->prefix . "sfa_abandoned_carts";

		if ($this->show_funnel_carts) {
			$cart_filter = ' AND show_on_funnel_report = 1 ';
		}
		else {
			$cart_filter = ' AND (cart_is_recovered = 1 OR (cart_is_recovered = 0 AND order_id IS NULL)) ';
		}

		$start_date_filter = new DateTime($this->start_date);
		$end_date_filter = new DateTime($this->end_date);

		$query = 'SELECT id, cart_expiry, cart_contents, cart_is_recovered, NULL as cart_value, customer_key, ip_address, order_id, viewed_checkout, cart_total
		FROM ' . $table_name . ' 
		WHERE cart_contents <> \'a:0:{}\''
			. $cart_filter .
			'AND id IN 
			(
				SELECT id 
				FROM ' . $table_name . ' 
				WHERE ip_address IS NOT NULL 
					AND ip_address NOT IN
					(
						SELECT ip_address
							FROM ' . $table_name . '
						WHERE ip_address IS NOT NULL 
						GROUP BY ip_address, round(cart_expiry, -1)
						HAVING COUNT(*) > 1
					)
				UNION SELECT id 
				FROM ' . $table_name . ' 
				WHERE ip_address IS NULL
			) 
			AND cart_expiry >= ' . $start_date_filter->getTimestamp() . ' AND cart_expiry <= ' . ($end_date_filter->getTimestamp() + 86400) . '
		ORDER BY cart_expiry DESC';

		if (isset($this->limit)) {
			$query = $query . ' LIMIT ' . $this->limit;
		}
		
		$data = $wpdb->get_results($query, 'ARRAY_A');

		//Turn database data into cart objects
		$cart_objects = [];
		foreach ($data as $raw_cart) {
			$expanded_cart = new SFA_Abandoned_Carts_Cart($raw_cart);

			if ($expanded_cart->expanded_without_error) {
				array_push($cart_objects, $expanded_cart);
			}
		}
		 
		$this->items = $cart_objects;
		$this->carts = $cart_objects;
		
		$per_page = 30;
	 	$current_page = $this->get_pagenum();
		$total_items = count($this->items);
		
		usort($this->items, array(&$this, 'sort'));
		$this->items = array_slice($this->items,(($current_page-1)*$per_page),$per_page);
		
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => ceil($total_items/$per_page)
			)
		);
	}
	
	function sort($a, $b) {
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'cart_expiry';
		
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'desc';
		
		if ($orderby === 'cart_value') {
			$result = strnatcmp($this->column_cart_value($a), $this->column_cart_value($b));
		}
		else {
			$result = strcmp($a->get_cart_expiry_raw(), $b->get_cart_expiry_raw());
		}
		
		return ($order === 'asc') ? $result : -$result;
	}
	
	function get_sortable_columns() {
		$sortable_columns = array(
			'cart_expiry' => array('cart_expiry', false),
			'cart_value' => array('cart_value', false)
		);
		
		return $sortable_columns;
	}
	
	function get_columns() {
		$columns = array(
			'cart_status' => 'Cart Status',
			'cart_expiry' => 'Cart Expired',
			'cart_customer' => 'Customer / IP',
			'cart_email' => 'Email',
			'cart_contents' => 'Products',
			'cart_value' => 'Cart Value'
		);
		
		return $columns;
	}
	
	function column_default($item, $column_name) {
	    return $item[$column_name];
	}

	function column_cart_email($item) {
		return $item->get_cart_email();
	}

	function column_cart_customer($item) {
		return $item->get_cart_customer();
	}
	
	function column_cart_status($item) {
		return $item->get_cart_status();
	}
	
	function column_cart_expiry($item) {
		return $item->get_cart_expiry_date();
	}
	
	function column_cart_contents($item) {
		return $item->get_cart_item_descriptions();
	}
	
	function column_cart_value($item) {
		if (function_exists('money_format')) {
			return money_format('%i', $item->get_cart_total());
		}
		else {
			return sprintf('%01.2f', $item->get_cart_total());
		}
	}
}
?>