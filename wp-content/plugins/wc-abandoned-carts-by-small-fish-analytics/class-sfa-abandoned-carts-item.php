<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

class SFA_Abandoned_Carts_Item
{
    //Product description
    public $description = null;
    public $price = null;
    public $id = null;
    public $link = null;
    public $quantity = null;

    public function __construct($cart_item) {

        if (is_a($cart_item['data'], 'WC_Product_Simple') || is_a($cart_item['data'], 'WC_Product_Variation')) {
            if (method_exists($cart_item['data'], 'get_name') && $cart_item['data']->get_name()) { 

                if (method_exists($cart_item['data'], 'get_id')) {
                    $this->id = $cart_item['data']->get_id();
                }
                else {
                    $this->id = $cart_item['data']->id;
                }

                $this->link = $cart_item['data']->get_permalink();
                $this->price = $cart_item['line_total'] + $cart_item['line_tax'];
                $this->quantity = $cart_item['quantity'];
                $this->description = $cart_item['data']->get_name();
            }
            else {
                foreach ($cart_item['data'] as $product) {
                    if (is_a($product, 'WP_Post')) {
                        $this->id = $product->ID;
                        $this->link = get_permalink($product);
                        $this->price = $cart_item['line_total'] + $cart_item['line_tax'];
                        $this->quantity = $cart_item['quantity'];
                        $this->description = $product->post_title;
                    }
                } 
            }
        }
    }
}

?>