<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Cart
{
    // Contains the raw database record of the cart
    private $raw_data = null;

    // Contains the unencrypted cart data from the $raw_data record
    private $unencrypted_cart = null;

	public $expanded_without_error = true;

    public function __construct($raw_data) {
        $this->raw_data = $raw_data;
    }

    // Unserializes the cart
    private function unserialize($raw_cart_contents) {
        $result = @unserialize(preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
    		return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
		}, $raw_cart_contents ));

		if (!$result) {
			$this->expanded_without_error = false;
		}

		return $result;
    }

    // Returns the cart total
    public function get_cart_total() {
		
		if ($this->raw_data['cart_total'] == null) {
			
			if ($this->unencrypted_cart == null) {
				$this->unencrypted_cart = $this->unserialize($this->raw_data['cart_contents']);
			}

			$total = 0.00;

			foreach($this->unencrypted_cart as $cart_item) {
				$total += $cart_item['line_total'] + $cart_item['line_tax'];
			}

			$recorder = new SFA_Abandoned_Carts_Recorder();
+			$recorder->sfa_record_cart_total($total, $this->get_cart_id());
		}
		else {
			$total = $this->raw_data['cart_total'];
		}
		
		if (function_exists('money_format')) {
			return money_format('%i', $total);
		}
		else {
			return sprintf('%01.2f', $total);
		}
	}
	
	public function get_cart_id() {
		return $this->raw_data['id'];
	}

    // Returns if the cart is recovered
    public function get_cart_is_recovered() {
        return $this->raw_data['cart_is_recovered'] == 1;
    }

    // Returns the cart expiry date
    public function get_cart_expiry_date() {
        return date('Y-m-d', $this->raw_data['cart_expiry']);
    }

	// Returns if the cart started the checkout process
	public function get_viewed_checkout() {
		return $this->raw_data['viewed_checkout'];
	}

    // Returns the cart expiry in raw since epoch
    public function get_cart_expiry_raw() {
        return $this->raw_data['cart_expiry'];
    }

    // Returns a comma seperated list of the cart item descriptions
    public function get_cart_item_descriptions() {
        $items = '';
		
		foreach($this->get_cart_items() as $cart_item) {
			$items = $items . '<a href="' . $cart_item->link . '">' . $cart_item->description . ', ';
		}
		
		$items = trim($items, ', ');

		return $items;
    }

	 // Returns cart items
    public function get_cart_items() {
		$items = array();
		
		if ($this->unencrypted_cart == null) {
			$this->unencrypted_cart = $this->unserialize($this->raw_data['cart_contents']);
		}
		
		foreach($this->unencrypted_cart as $cart_item) {
			$item = new SFA_Abandoned_Carts_Item($cart_item);
			array_push($items, $item);
		}
		
		return $items;
    }

    // Returns the cart status
    public function get_cart_status() {
        if ($this->get_cart_is_recovered()) {
			return '<span style=\'color: green\'>Recovered</span>';
		}
		else if ((time() - $this->get_cart_expiry_raw()) < (15 * 60)) {
			return '<span style=\'color: orange\'>In Progress</span>';
		}
		else {
			return '<span style=\'color: red\'>Abandoned</span>';
		}
    }

	// Returns the cart order id
	public function get_order_id() {
		return $this->raw_data['order_id'];
	}

    // Returns the cart email address if applicable
    public function get_cart_email() {
        $user = get_userdata($this->raw_data['customer_key']);

		if ($user) {
			return '<a href="mailto:' . $user->user_email . '">' . $user->user_email . '</a>';
		}	
		else if ($this->raw_data['order_id']) {
			$meta = get_post_meta($this->raw_data['order_id']);

			if (isset($meta['_billing_email'])) {
				$email = $meta['_billing_email'];
			
				return '<a href="mailto:' . $email[0] . '">' . $email[0] . '</a>';
			}
		}
    }

    // Returns the cart customer or IP address
    public function get_cart_customer() {
        $user = get_userdata($this->raw_data['customer_key']);

		if ($user) {
			return $user->first_name . ' ' . $user->last_name;
		}
		else if ($this->raw_data['order_id']) {
			$meta = get_post_meta($this->raw_data['order_id']);
			
			$result = '';
			
			if (isset($meta['_billing_first_name']))
			{
				$first_name = $meta['_billing_first_name'];
				$result = $result . $first_name[0];
			}
			
			if (isset($meta['_billing_last_name']))
			{
				$last_name = $meta['_billing_last_name'];
				$result = $result . ' ' . $last_name[0];
			}
			
			return $result;
		}
		else
			return $this->raw_data['ip_address'];
    }
}
?>