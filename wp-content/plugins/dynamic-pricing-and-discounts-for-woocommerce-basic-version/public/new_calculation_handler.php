<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
require_once('XA_RulesValidator.php');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of new_calculation_handler
 *
 * @author Akshay
 */
class XA_NewCalculationHandler {

    public $debug_mode = false;

    public function __construct() {
        $dummy_settings['product_rules_on_off'] = 'enable';
        $dummy_settings['combinational_rules_on_off'] = 'enable';
        $dummy_settings['category_rules_on_off'] = 'enable';
        $dummy_settings['cart_rules_on_off'] = 'enable';
        $dummy_settings['buy_and_get_free_rules_on_off'] = 'enable';
        $dummy_settings['BOGO_category_rules_on_off'] = 'enable';
        $dummy_settings['price_table_on_off'] = 'enable';
        $dummy_settings['auto_add_free_product_on_off'] = 'enable';
        $dummy_settings['pricing_table_qnty_shrtcode'] = 'nos.';
        $dummy_settings['show_discount_in_line_item'] = 'yes';
        $dummy_settings['pricing_table_position'] = 'woocommerce_before_add_to_cart_button';
        $dummy_settings['show_on_sale'] = 'no';
        $dummy_settings['execution_order'] = array('product_rules',
            'combinational_rules',
            'cat_combinational_rules',
            'category_rules',
            'cart_rules',
            'buy_and_get_free_rules','BOGO_category_rules');
        global $woocommerce;
        global $xa_dp_rules;
        global $xa_dp_setting;
        global $xa_cart_quantities;
        global $xa_cart_weight;
        global $xa_cart_price;
        global $xa_cart_categories;
        global $xa_hooks;
        global $xa_cart_categories_items;
        global $xa_cart_categories_units;

        $xa_cart_quantities = array();
        $xa_cart_weight = array();
        $xa_cart_price = array();
        $xa_cart_categories = array();        
        $xa_cart_categories_items=array();
        $xa_cart_categories_units=array();
        $xa_dp_rules = get_option('xa_dp_rules', array());
        $xa_dp_setting = get_option('xa_dynamic_pricing_setting', $dummy_settings);
        if (!is_admin() && !defined('DOING_CRON')) {

            ////Removing Free Products Which are Automatically Added by Dynamic Pricing
            $cart_item_data = $woocommerce->cart->get_cart();
            foreach ($cart_item_data as $key => $hash) {
                if (strpos($key, 'FreeForRule') !== false) {            //remove free products
                    $woocommerce->cart->remove_cart_item($key);
                    continue;
                }
            }
            //////////////////////////////////////
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
                    $product = $values['data'];
                    $id=$product->get_id();
                    $xa_cart_quantities[ $id ] = isset( $quantities[ $id ] ) ? $quantities[ $id ] + $values['quantity'] : $values['quantity'];
            }
            //$xa_cart_quantities = $woocommerce->cart->get_cart_item_quantities();
            remove_filter($xa_hooks['woocommerce_get_price_hook_name'], array($this, 'getDiscountedPriceForProduct'), 22);
            foreach ($xa_cart_quantities as $_pid => $_qnty) {
                $prod = wc_get_product($_pid);
                $xa_cart_weight[$_pid] = $prod->get_weight();
                $xa_cart_price[$_pid] = $prod->get_price();
                if ($prod->is_type('variation')) {
                    $parent_product = wc_get_product($prod->get_parent_id());
                    $xa_cart_categories[$_pid] = is_wc_version_gt_eql('2.7') ? $parent_product->get_category_ids() : xa_get_category_ids($parent_product);                    
                } else {
                    $xa_cart_categories[$_pid] = is_wc_version_gt_eql('2.7') ? $prod->get_category_ids() : xa_get_category_ids($prod);
                }
                foreach($xa_cart_categories[$_pid] as $_cid){
                    $xa_cart_categories_items[$_cid]= isset($xa_cart_categories_items[$_cid])? ($xa_cart_categories_items[$_cid]+1) : 1;
                    $xa_cart_categories_units[$_cid]= isset($xa_cart_categories_units[$_cid])? ($xa_cart_categories_units[$_cid]+$_qnty) : $_qnty;
                }
            }
            add_filter($xa_hooks['woocommerce_get_price_hook_name'], array($this, 'getDiscountedPriceForProduct'), 22, 2);
        }
    }

    /**
     * Finds valid rules for this product and return discounted price based on (all rules,first match,best discount)
     *
     * @param float $old_price  (price over which discount needs to be applied)
     * @param wc_product $product (object of product for which we need discounted price)
     * @param integer $pid (id of product)
     * 
     * @return $discounted_price
     */
    
    Public Function getDiscountedPriceForProduct($old_price = '', $product = null, $pid = null) { 
        global $xa_hooks, $xa_common_flat_discount;
        remove_filter($xa_hooks['woocommerce_get_price_hook_name'], array($this, 'getDiscountedPriceForProduct'), 22);
        $regular_price=$product->get_regular_price();
        if (empty($pid)) {
            $pid = xa_get_pid($product);
        }
        if ($pid == false) {
            add_filter($xa_hooks['woocommerce_get_price_hook_name'], array($this, 'getDiscountedPriceForProduct'), 22, 2);
            return $old_price;
        }
        if(((current_filter()==$xa_hooks['woocommerce_get_sale_price_hook_name']) || (current_filter()== 'woocommerce_product_variation_get_sale_price')) && empty($old_price))  // if sale price is empty then old price is regular_price
        {
            $old_price=$regular_price;
        }
        $discounted_price = $old_price;
        $weight = $product->get_weight();
        if (!empty($old_price) && (!empty($product) || !empty($pid))) {

            global $xa_cart_quantities;
            Global $rule_based_quantity;
            $parent_id=is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
            if (isset($xa_cart_quantities[$pid]) ||isset($xa_cart_quantities[$parent_id]) ) {
                $current_quantity = isset($xa_cart_quantities[$pid])?$xa_cart_quantities[$pid]:$xa_cart_quantities[$parent_id];
            } else {
                $current_quantity = 0;
            }
            if($current_quantity==0  && class_exists('SitePress')){
                global $sitepress;
                $trid = $sitepress->get_element_trid($pid);
                $trans = $sitepress->get_element_translations($trid);
                foreach($trans as $lan){
                    $all_ids[]=$lan->element_id;
                }
                foreach($all_ids as $_pid)
                {
                    if(!empty($xa_cart_quantities[$_pid]))
                    {
                        $current_quantity=$xa_cart_quantities[$_pid];
                        break;
                    }
                }       
            }           
            if (is_shop() || is_category() || is_product()) {
                $current_quantity++;
            }
            $objRulesValidator = New XA_RulesValidator();
            $valid_rules = $objRulesValidator->getValidRulesForProduct($product, $pid, $current_quantity, $discounted_price, $weight);
            if ($this->debug_mode) {
                error_log(str_repeat("-", 380));
                error_log('Valid Rules for Pid=' . $pid . ' and qnty=' . $current_quantity . ' Valid Rules= ' . print_r($valid_rules, true));
            }
            if (is_array($valid_rules)) {
                foreach ($valid_rules as $rule_type_colon_rule_no => $rule) {
                    //this section supports repeat execution for product rules
                    if (isset($rule['repeat_rule']) && $rule['repeat_rule'] == 'yes' && !empty($rule['max']) && !empty($rule['min'])) {
                        $times = intval($current_quantity / $rule['max']);
                        $total_price = 0;
                        $repeat_qnty = (float) $rule['max'];
                        if ($this->debug_mode) {
                            error_log(str_repeat(">", 208));
                            error_log('Executing Rule(Repeat=True):' . $rule_type_colon_rule_no);
                        }
                        if (!empty($rule['discount_type']) && $rule['discount_type'] == 'Flat Discount') {
                            $xa_common_flat_discount[$rule['rule_type'] . ":" . $rule['rule_no'].":".$pid] = floatval($rule['value']) * floatval($times);
                            if (!empty($rule['adjustment'])) {
                                $adjusted_qnty=!empty($objRulesValidator->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']])?$objRulesValidator->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']]:$current_quantity;
                                $discounted_price = $discounted_price + ( (float) $rule['adjustment'] / $adjusted_qnty);
                            }
                        }else
                        {
                            $r_price = $objRulesValidator->execute_rule($discounted_price, $rule_type_colon_rule_no, $rule, $repeat_qnty, $pid);
                            $total_price = $r_price * $times * $repeat_qnty;
                            //error_log("$total_price = $r_price * $times * $repeat_qnty");
                            $remaining_qnty = $current_quantity - ($times * $repeat_qnty);
                            if ($remaining_qnty > 0) { 
                                $r_price = $objRulesValidator->execute_rule($discounted_price, $rule_type_colon_rule_no, $rule, $remaining_qnty, $pid);
                                $total_price = $total_price + ($remaining_qnty * $discounted_price);
                            }
                            $discounted_price = $total_price / $current_quantity;
                        }                        

                    } else {
                        if ($this->debug_mode) {
                            error_log(str_repeat(">", 208));
                            error_log('Executing Rule:' . $rule_type_colon_rule_no);
                        }
                        //fix for best discount mode flat discount is not getting calculated]
                        if (!empty($rule['discount_type']) && $rule['discount_type'] == 'Flat Discount') {
                            $xa_common_flat_discount[$rule['rule_type'] . ":" . $rule['rule_no'].":".$pid] = floatval($rule['value']);
                            if (!empty($rule['adjustment'])) {
                                $adjusted_qnty=!empty($objRulesValidator->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']])?$objRulesValidator->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']]:$current_quantity;
                                $discounted_price = $discounted_price + ( (float) $rule['adjustment'] / $adjusted_qnty);
                            }
                        } else {
                            $discounted_price = $objRulesValidator->execute_rule($discounted_price, $rule_type_colon_rule_no, $rule, $current_quantity, $pid);
                        }
                    }
                }
            }
        }
        if(((current_filter()==$xa_hooks['woocommerce_get_sale_price_hook_name']) || (current_filter()== 'woocommerce_product_variation_get_sale_price')) && ($regular_price==$discounted_price))  // if sale price is empty then old price is regular_price
        {
            return '';
        }
        add_filter($xa_hooks['woocommerce_get_price_hook_name'], array($this, 'getDiscountedPriceForProduct'), 22, 2);
        return $discounted_price;
    }

    public function getDiscountedPriceHTML($price, $product) {     // hooked to get_price_html filter
        if ($product->is_type('simple')) {
            return $this->getDiscountedPriceHTML_for_simple_product($price, $product);
        } elseif ($product->is_type('variable')) {
            return $this->getDiscountedPriceHTML_for_variable_product($price, $product);
        } elseif ($product->is_type('grouped')) {
            return $this->getDiscountedPriceHTML_for_group_product($price, $product);
        }
        return $price;
    }

    public function getDiscountedPriceHTML_for_simple_product($price, $product) {     // hooked to get_price_html filter
        return $price;
    }

    public function getDiscountedPriceHTML_for_group_product($price, $product) {     // hooked to get_price_html filter
        $tax_display_mode = get_option('woocommerce_tax_display_shop');
        $child_prices = array();

        foreach ($product->get_children() as $child_id) {
            $child = wc_get_product($child_id);
            //$child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax($child) : wc_get_price_excluding_tax($child);
            if ($child->is_type('variable')) {
                $prices = $child->get_variation_prices(true);

                if (empty($prices['price'])) {
                    return '';
                }
                foreach ($prices['price'] as $pid => $old_price) {
                    $prices['price'][$pid] = $this->getDiscountedPriceForProduct($old_price, wc_get_product($pid), $pid);
                }
                asort($prices['price']);
                $min_price = current($prices['price']);
                $child_prices[]=$min_price;
            } else {
                $child_prices[] = 'incl' === $tax_display_mode ? wc_get_price_including_tax($child) : wc_get_price_excluding_tax($child);
            }
        }
        if (!empty($child_prices)) {
            $min_price = min($child_prices);
            $max_price = max($child_prices);
        } else {
            $min_price = '';
            $max_price = '';
        }

        if ('' !== $min_price) {
            $price = $min_price !== $max_price ? sprintf(_x('%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce'), wc_price($min_price), wc_price($max_price)) : wc_price($min_price);
            $is_free = ( 0 == $min_price && 0 == $max_price );

            if ($is_free) {
                $price = apply_filters('woocommerce_grouped_free_price_html', __('Free!', 'woocommerce'), $product);
            } else {
                $price = apply_filters('woocommerce_grouped_price_html', $price . $product->get_price_suffix(), $product, $child_prices);
            }
        } else {
            $price = apply_filters('woocommerce_grouped_empty_price_html', '', $product);
        }

        return $price;
    }

    public function getDiscountedPriceHTML_for_variable_product($price, $product) {     // hooked to get_price_html filter
        $prices=array();
        $childrens=$product->get_children();
        $tax_display_mode = get_option('woocommerce_tax_display_shop');
        foreach($childrens as $_pid)
        {
            $pd=wc_get_product($_pid);
            if(!empty($pd)){
                $prices['price'][$_pid]='incl' === $tax_display_mode ? wc_get_price_including_tax($pd) : wc_get_price_excluding_tax($pd);
                $prices['regular_price'][$_pid]=$pd->get_regular_price();                
            }
        }        
//      $prices = $product->get_variation_prices(true);

        if (empty($prices['price'])) {
            return '';
        }
//        foreach ($prices['price'] as $pid => $old_price) {
//            $prices['price'][$pid] = $this->getDiscountedPriceForProduct($old_price, wc_get_product($pid), $pid);
//        }
        asort($prices['price']);
        $min_price = current($prices['price']);
        $max_price = end($prices['price']);
        $regular_price=current($prices['regular_price']);
        if ($min_price !== $max_price) {
            $price = wc_format_price_range($min_price, $max_price) . $product->get_price_suffix();
        }elseif($regular_price!= $max_price){
            $price=wc_format_sale_price( $regular_price, $max_price ) . $product->get_price_suffix();
        }else{
            $price = wc_price($min_price) . $product->get_price_suffix();
        }
        return apply_filters('eha_variable_sale_price_html', $price, $min_price, $max_price, $regular_price);
    }

}
