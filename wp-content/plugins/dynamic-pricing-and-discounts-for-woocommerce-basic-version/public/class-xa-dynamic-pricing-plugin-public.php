<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include 'new_calculation_handler.php';


add_action('wp_loaded', 'xa_init_calculator');
$GLOBALS['settings'] = get_option('xa_dynamic_pricing_setting');

function xa_init_calculator() {
    xa_init_wc_functions();
    global $xa_common_flat_discount;
    global $xa_hooks;
    global $executed_max_discount;
    $executed_max_discount = array();
    global $executed_rule_pid_price;
    $executed_rule_pid_price = array();
    $xa_common_flat_discount = array();
    $obj = new XA_NewCalculationHandler();
    add_action('woocommerce_cart_calculate_fees', 'xa_calculate_and_apply_discount_and_add_fee');

    $xa_hooks['woocommerce_get_price_hook_name'] = 'woocommerce_get_price';
    if (is_wc_version_gt_eql('2.7.0') == true) {
        $xa_hooks['woocommerce_get_price_hook_name'] = 'woocommerce_product_get_price';
    }
    $xa_hooks['woocommerce_get_sale_price_hook_name'] = 'woocommerce_get_sale_price';
    if (is_wc_version_gt_eql('2.7') == true) {
        $xa_hooks['woocommerce_get_sale_price_hook_name'] = 'woocommerce_product_get_sale_price';
    }

    add_filter('woocommerce_product_is_on_sale', 'product_is_on_sale', 99, 2);

    function product_is_on_sale($on_sale, $product) {
        
        if($product->is_type('grouped') || $product->is_type('variable') )
        {        
            $childrens=$product->get_children();
            foreach($childrens as $child)
            {
                $prod= wc_get_product($child);
                $sale_price=$prod->get_sale_price();
                $regular_price=$prod->get_regular_price();
                if(!empty($sale_price) && $sale_price!=$regular_price){
                    return true;
                }
            }
        }elseif($product->is_type('simple'))
        {
            if ('' !== (string) $product->get_price() && $product->get_regular_price() > $product->get_price()) {
                $on_sale = true;
                if (WC()->version >= '3.0.0') {
                    if ($product->get_date_on_sale_from() && $product->get_date_on_sale_from()->getTimestamp() > time()) {
                        $on_sale = false;
                    }

                    if ($product->get_date_on_sale_to() && $product->get_date_on_sale_to()->getTimestamp() < time()) {
                        $on_sale = false;
                    }
                }
            } else {
                $on_sale = false;
            }            
        }
        return $on_sale;
    }

    add_filter('woocommerce_get_price_html', array($obj, 'getDiscountedPriceHTML'), 22, 2);              // update sale price on product variation page
    add_filter($xa_hooks['woocommerce_get_price_hook_name'], array($obj, 'getDiscountedPriceForProduct'), 22, 2);         // update sale price on product page
    add_filter($xa_hooks['woocommerce_get_sale_price_hook_name'], array($obj, 'getDiscountedPriceForProduct'), 22, 2);    // update sale price on product page
    add_filter('woocommerce_product_variation_get_price', array($obj, 'getDiscountedPriceForProduct'), 22, 2);    // update sale price on product page
    add_filter('woocommerce_product_variation_get_sale_price', array($obj, 'getDiscountedPriceForProduct'), 22, 2);    // update sale price on product page
    
}

function xa_calculate_and_apply_discount_and_add_fee() {
    global $xa_common_flat_discount;
    global $woocommerce;
    $total_flat_discount = 0;
    foreach ($xa_common_flat_discount as $dis) {
        $total_flat_discount += $dis;
    }
    if ($total_flat_discount > 0) {
        $label = apply_filters('eha_change_discount_label_filter', __('Discount', 'eh-dynamic-pricing-discounts'));
        $woocommerce->cart->add_fee($label, -$total_flat_discount);
    } elseif ($total_flat_discount < 0) {
        $label = apply_filters('eha_change_discount_label_filter', __('Discount Adjustment', 'eh-dynamic-pricing-discounts'));
        $woocommerce->cart->add_fee($label, -$total_flat_discount);
    }
}

$pricing_table_hook = isset($GLOBALS['settings']['pricing_table_position']) ? $GLOBALS['settings']['pricing_table_position'] : 'woocommerce_before_add_to_cart_button';
add_action($pricing_table_hook, 'xa_show_pricing_table', 40);

function xa_show_pricing_table() {
    if ($GLOBALS['settings']['price_table_on_off'] == 'enable') {
        include "xa-single-product-pricing-table.php";
    }
}

$offer_table_hook = isset($GLOBALS['settings']['offer_table_position']) ? $GLOBALS['settings']['offer_table_position'] : 'woocommerce_before_add_to_cart_button';
add_action($offer_table_hook, 'xa_show_offer_table', 40);

function xa_show_offer_table() {
    if (isset($GLOBALS['settings']['offer_table_on_off']) && $GLOBALS['settings']['offer_table_on_off'] == 'enable') {
        include "xa-single-product-offer-table.php";
    }
}

add_filter('woocommerce_cart_item_price', 'xa_show_discount_on_line_item', 100, 2);

function xa_show_discount_on_line_item($price, $cart_item) {
    $prod = $cart_item['data'];
    $price = $prod->get_price_html();
    return $price;
}

add_action('wc_ajax_get_refreshed_fragments', 'xa_wc_ajax_get_refreshed_fragments', 1);

function xa_wc_ajax_get_refreshed_fragments() {
    if (is_cart()) {
        global $woocommerce;
        $woocommerce->cart->calculate_totals();
    }
}