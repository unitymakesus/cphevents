<?php

/**
 * This Class Handles Rules Filtering
 *
 * @author Akshay
 */
class XA_RulesValidator {

    Public $execution_mode = "first_match";
    Public $execution_order = array('product_rules', 'combinational_rules', 'cat_combinational_rules', 'category_rules', 'cart_rules', 'buy_get_free_rules','BOGO_category_rules');
    Public $rule_based_quantity = array();
    Public $for_offers_table = false;

    /**
     * Finds valid rules for a Product
     *
     * @param wc_product $product (object of product for which we need discounted price)
     * @param integer $pid (id of product)
     * 
     * @return array $valid_rules
     */
    function __construct($mode = '', $for_offers_table = false, $only_execute_this_mode = '') {
        global $xa_dp_setting;

        $this->for_offers_table = $for_offers_table;
        $this->execution_mode = empty($mode) ? $xa_dp_setting['mode'] : $mode;
        $this->execution_order = empty($only_execute_this_mode) ? (isset($xa_dp_setting['execution_order']) ? $xa_dp_setting['execution_order'] : array('product_rules',
            'combinational_rules',
            'cat_combinational_rules',
            'category_rules',
            'cart_rules',
            'buy_and_get_free_rules','BOGO_category_rules') ) : array($only_execute_this_mode);
    }
    /**
     * Function which converts product and category id's based on current language selected by user
     */

    Public Function getValidRulesForProduct($product, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
        if (empty($pid))
            $pid = xa_get_pid($product);
        if (!empty($pid)) {
            switch ($this->execution_mode) {
                case "first_match":
                    return $this->getFirstMatchedRule($product, $pid, $current_quantity, $price, $weight);
                case "best_discount":
                    return $this->getBestMatchedRules($product, $pid, $current_quantity, $price, $weight);
                case "all_match":
                    return $this->getAllMatchedRules($product, $pid, $current_quantity, $price, $weight);
                default:
                    return false;
            }
        }
        return false;
    }

    Function getFirstMatchedRule($product, $pid, $current_quantity = 1, $price = 0, $weight = 0) {
        global $xa_dp_rules;
        foreach ($this->execution_order as $rule_type) {
            $rules = !empty($xa_dp_rules[$rule_type])?$xa_dp_rules[$rule_type]:array();
            foreach ($rules as $rule_no => $rule) {
                //error_log($rule_type.'->'.$rule_no." pid=".$pid);
                $rule['rule_no'] = $rule_no;
                $rule['rule_type'] = $rule_type;
                if ($this->checkRuleApplicableForProduct($rule, $rule_type, $product, $pid, $current_quantity, $price, $weight) === true) {
                    //error_log('type='.$rule_type.' ruleno='.$rule_no.' pid='.$pid);
                    return array($rule_type . ":" . $rule_no => $rule);
                }
            }
        }
        return array();
    }

    Function getAllMatchedRules($product, $pid, $current_quantity = 1, $price = 0, $weight = 0) {
        global $xa_dp_rules;
        $valid_rules = array();
        foreach ($this->execution_order as $rule_type) {
            $rules = $xa_dp_rules[$rule_type];
            if (!empty($rules)) {

                foreach ($rules as $rule_no => $rule) {
                    //error_log($rule_type.'->'.$rule_no." pid=".$pid);
                    $rule['rule_no'] = $rule_no;
                    $rule['rule_type'] = $rule_type;
                    if ($this->checkRuleApplicableForProduct($rule, $rule_type, $product, $pid, $current_quantity, $price, $weight) === true) {
                        //error_log('type='.$rule_type.' ruleno='.$rule_no.' pid='.$pid);
                        $valid_rules[$rule_type . ":" . $rule_no] = $rule;
                    }
                }
            }
        }
        return $valid_rules;
    }

    Function getBestMatchedRules($product, $pid, $current_quantity = 1, $price = 0, $weight = 0) {
        global $xa_dp_rules;
        $valid_rules = array();
        $max_price = 9999999;
        foreach ($this->execution_order as $rule_type) {
            $rules = $xa_dp_rules[$rule_type];
            if (!empty($rules)) {
                foreach ($rules as $rule_no => $rule) {
                    //error_log($rule_type.'->'.$rule_no." pid=".$pid);
                    $rule['rule_no'] = $rule_no;
                    $rule['rule_type'] = $rule_type;
                    if ($this->checkRuleApplicableForProduct($rule, $rule_type, $product, $pid, $current_quantity, $price, $weight) === true) {
                        //error_log('type=' . $rule_type . ' ruleno=' . $rule_no . ' pid=' . $pid);
                        if (!empty($rule['calculated_discount']) && $max_price > $rule['calculated_discount']) {   //error_log('type='.$rule_type.' ruleno='.$rule_no.' pid='.$pid);
                            $max_price = $rule['calculated_discount'];
                            $valid_rules = array($rule_type . ":" . $rule_no => $rule);
                        }
                    }
                }
            }
        }
        return $valid_rules;
    }

    Public Function checkRuleApplicableForProduct(&$rule = null, $rule_type = '', $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
        if( apply_filters('eha_dp_skip_product',false,$pid,$rule,$rule_type)!=false){     
            return false;
        }
        if (!empty($rule) && !empty($rule_type) && !empty($pid)) {
            switch ($rule_type) {
                case 'product_rules':
                    $valid = $this->checkProductRuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                case 'category_rules':
                    $valid = $this->checkCategoryRuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                case 'cart_rules':
                    $valid = $this->checkCartRuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight); 
                    break;
                case 'combinational_rules':
                    $valid = $this->checkCombinationalRuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                case 'cat_combinational_rules':
                    $valid = $this->checkCategoryCombinationalRuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                case 'buy_get_free_rules':
                    $valid = $this->checkBOGO_RuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                case 'BOGO_category_rules':
                    $valid = $this->checkBOGO_category_RuleApplicableForProduct($rule, $product, $pid, $current_quantity, $price, $weight);
                    break;
                
            }
            return $valid;
        }
        return false;
    }

    Function checkBOGO_RuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
//        echo "<pre>";
//        print_r($rule);
//        echo "</pre>";     
        $rule['purchased_product_id']= XA_WPML_Compatible_ids($rule['purchased_product_id'],'product',true);
        $rule['free_product_id']= XA_WPML_Compatible_ids($rule['free_product_id'],'product',true);
        global $xa_cart_quantities,$xa_cart_price;
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        if (empty($rule['purchased_product_id']) || empty($rule['free_product_id'])) {
            return false;
        }
        $parent_id=$pid;
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
        }
        
        if (!in_array(  $pid, array_keys($rule['purchased_product_id'])) && !in_array(  $parent_id, array_keys($rule['purchased_product_id']) )   && !in_array($pid, array_keys($rule['free_product_id']))) {
            return false;
        }
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table
        foreach ($rule['purchased_product_id'] as $_pid => $_qnty) {  
            if (!isset($xa_cart_quantities[$_pid]) || $xa_cart_quantities[$_pid] < $_qnty) {
                $_product=wc_get_product($_pid);
                if ($_product->is_type('variable')) {
                    foreach($_product->get_children() as $cid)
                    {   
                        if (isset($xa_cart_quantities[$cid]) && $xa_cart_quantities[$cid] >= $_qnty) {
                            continue(2);
                        }
                    }
                }                         
                return false;
            }
        } 
        ////////if free product is already in cart with exact quanitty this code will set its price as zero
        if ((in_array($pid, array_keys($rule['purchased_product_id'])) || in_array($parent_id, array_keys($rule['purchased_product_id']))) && !in_array($pid, array_keys($rule['free_product_id']))) {
            $dprice=0;
            foreach($rule['free_product_id'] as $_pid=>$_qnty)
            {
                $dprice+=$xa_cart_price[$_pid] * $_qnty;
            }
            if ($this->execution_mode == "best_discount") {
                $rule['calculated_discount'] = $dprice;    //to check best descount rule            
            }
        }
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'buy_get_free_rules');
    }
    Function checkBOGO_category_RuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
//        echo "<pre>";
//        print_r($rule);
//        echo "</pre>";     
        $rule['purchased_category_id']= XA_WPML_Compatible_ids($rule['purchased_category_id'],'category',true);
        $rule['free_product_id']= XA_WPML_Compatible_ids($rule['free_product_id'],'product',true);
        global $xa_cart_quantities,$xa_cart_price,$xa_cart_categories_items,$xa_cart_categories_units; 
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }        
        if (empty($rule['purchased_category_id']) || empty($rule['free_product_id'])) {
            return false;
        }
        $parent_id=$pid;
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
            $parent_product = wc_get_product($parent_id);
            $product_categories = is_wc_version_gt_eql('2.7') ? $parent_product->get_category_ids() : xa_get_category_ids($parent_product);
        } else {
            $product_categories = is_wc_version_gt_eql('2.7') ? $product->get_category_ids() : xa_get_category_ids($product);
        }       
        $cids=array();
        $cat_ids=$rule['purchased_category_id'];
        $cids = XA_WPML_Compatible_ids($cat_ids,'category',true);   
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table
        foreach ($rule['purchased_category_id'] as $_cid => $_qnty_and_checkon) { 
            $tmp=explode(":",$_qnty_and_checkon);
            $_qnty=!empty($tmp[0])?$tmp[0]:0;
            $checkon=!empty($tmp[1])?$tmp[1]:'items';            
            if ($checkon=='items' && (!isset($xa_cart_categories_items[$_cid]) || $xa_cart_categories_items[$_cid] < $_qnty )) { 
                return false;
            }elseif($checkon=='units' && (!isset($xa_cart_categories_units[$_cid]) || $xa_cart_categories_units[$_cid] < $_qnty ))
            {
                return false;
            }
        }
        $rule['calculated_discount'] =9999; ///so it will be executed always if in valid rules list
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'buy_get_free_rules');
    }

    Function checkCategoryCombinationalRuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
//        echo "<pre>";
//        print_r($rule);
//        echo "</pre>";     
        
        global $xa_cart_quantities;
        global $xa_cart_categories;
        $total_units=0;
        //if pid is selected in this rule
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
            $parent_product = wc_get_product($parent_id);
            $product_categories = is_wc_version_gt_eql('2.7') ? $parent_product->get_category_ids() : xa_get_category_ids($parent_product);
        } else {
            $product_categories = is_wc_version_gt_eql('2.7') ? $product->get_category_ids() : xa_get_category_ids($product);
        }
        //error_log("rule cat=".print_r($rule['cat_id'],true)." current prod cat=".print_r($product_categories,true));
        $rule['cat_id']= XA_WPML_Compatible_ids($rule['cat_id'],'category',true);
        $tmp=array_keys($rule['cat_id']);
        $tmp=array_intersect($tmp, $product_categories);
        if (empty($rule['cat_id']) || count($rule['cat_id']) == 0 || empty($tmp)) {
            return false;
        }
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table        

        $total_items_of_this_category_in_cart = array();
        $total_all_units_of_this_category_in_cart = array();
        foreach ($xa_cart_categories as $_pid => $_categories) {
            $cat_id = array_intersect(array_keys($rule['cat_id']), $_categories);
            if (!empty($cat_id)) {
                if (!isset($total_items_of_this_category_in_cart[current($cat_id)])) {
                    $total_items_of_this_category_in_cart[current($cat_id)] = 0;
                    $total_all_units_of_this_category_in_cart[current($cat_id)] = 0;
                }
                $total_items_of_this_category_in_cart[current($cat_id)] ++;
                $total_all_units_of_this_category_in_cart[current($cat_id)] += !empty($xa_cart_quantities[$_pid]) ? $xa_cart_quantities[$_pid] : 0;
            }
        }
        foreach ($rule['cat_id'] as $cat_id => $qnty) {
            if (empty($total_all_units_of_this_category_in_cart[$cat_id]) || $total_all_units_of_this_category_in_cart[$cat_id] < $qnty) {
                return false;
            } else {
                $total_units = !empty($total_all_units_of_this_category_in_cart[$cat_id])?$total_all_units_of_this_category_in_cart[$cat_id]:1;
            }
        }
        $this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']] = $total_units;   // for adjustment
        //to check best descount rule
        if ($this->execution_mode == "best_discount")
            $rule['calculated_discount'] = $this->SimpleExecute($price, $rule['rule_no'], $rule, $pid, $current_quantity, true);
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'cat_combinational_rules');
    }

    Function checkCategoryRuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
//        echo "<pre>";
//        print_r($rule);
//        echo "</pre>";       
        global $xa_cart_quantities;
        global $xa_cart_weight;
        global $xa_cart_price;
        global $xa_cart_categories;
        $min = (empty($rule['min']) == true) ? 1 : $rule['min'];
        $max = (empty($rule['max']) == true ) ? 999999 : $rule['max'];

        if ($max < $min && $max != 0) {
            return false;
        }
        //if pid is selected in this rule
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
            $parent_product = wc_get_product($parent_id);
            $product_categories = is_wc_version_gt_eql('2.7') ? $parent_product->get_category_ids() : xa_get_category_ids($parent_product);
        } else {
            $product_categories = is_wc_version_gt_eql('2.7') ? $product->get_category_ids() : xa_get_category_ids($product);
        }
        $cids=array();
        $cat_ids=$rule['category_id'];
        if(!is_array($cat_ids)) $cat_ids=array($cat_ids);
        foreach( $cat_ids as $_cid)
        {
            $cids[] = XA_WPML_Compatible_ids($_cid,'category');
        }       
        $matched=array_intersect($cids, $product_categories);
        if (empty($cids) || empty($matched)) {
            return false;
        }
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table        

        $total_items_of_this_category_in_cart = 0;
        $total_all_units_of_this_category_in_cart = 0;
        $total_weight_of_this_category = 0;
        $total_price_of_this_category = 0;
        if (is_shop() || is_category() || is_product()) {
            $current_quantity++;
            if (empty($xa_cart_quantities[$pid])) {
                $total_items_of_this_category_in_cart++;
            }
            $total_all_units_of_this_category_in_cart++;
            $total_weight_of_this_category += !empty($xa_cart_weight[$pid]) ? $xa_cart_weight[$pid] : (float) $product->get_weight();
            $total_price_of_this_category += !empty($xa_cart_price[$pid]) ? $xa_cart_price[$pid] : (float) $price;
        }
        foreach ($xa_cart_categories as $_pid => $_categories) {
            $match=array_intersect($matched,$_categories);
            if (!empty($match)) {
                $total_items_of_this_category_in_cart++;
                if (!empty($xa_cart_quantities[$_pid])) {
                    $total_all_units_of_this_category_in_cart += (int) $xa_cart_quantities[$_pid];
                }
                if (!empty($xa_cart_weight[$_pid])) {
                    $total_weight_of_this_category += (int) ($xa_cart_quantities[$_pid] * $xa_cart_weight[$_pid]);
                }
                if (!empty($xa_cart_price[$_pid])) {
                    $total_price_of_this_category += (int) ($xa_cart_quantities[$_pid] * $xa_cart_price[$_pid]);
                }
            }
        }
        if ($total_items_of_this_category_in_cart == 0) {
            $total_items_of_this_category_in_cart = 1;
            $total_all_units_of_this_category_in_cart = 1;
            $total_weight_of_this_category = (float) $product->get_weight();
            $total_price_of_this_category = $price;
        }
        $this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']] = $total_all_units_of_this_category_in_cart;   // for adjustment
        //error_log('total units=' . $total_all_units_of_this_category_in_cart . " total items=" . $total_items_of_this_category_in_cart);
        //error_log('total price=' . $total_price_of_this_category . " total weights=" . $total_weight_of_this_category);
        if ($rule['check_on'] == 'TotalQuantity' && ($total_all_units_of_this_category_in_cart < $min || $total_all_units_of_this_category_in_cart > $max || empty($total_all_units_of_this_category_in_cart))) {
            return false;
        } elseif ($rule['check_on'] == 'Quantity' && ($total_items_of_this_category_in_cart < $min || $total_items_of_this_category_in_cart > $max || empty($total_items_of_this_category_in_cart))) {
            return false;
        } elseif ($rule['check_on'] == 'Weight' && ($total_weight_of_this_category < $min || $total_weight_of_this_category > $max || empty($total_weight_of_this_category))) {
            return false;
        } elseif ($rule['check_on'] == 'Price' && ($total_price_of_this_category < $min || $total_price_of_this_category > $max || empty($total_price_of_this_category))) {
            return false;
        }

        //to check best descount rule
        if ($this->execution_mode == "best_discount")
            $rule['calculated_discount'] = $this->SimpleExecute($price, $rule['rule_no'], $rule, $pid, $current_quantity, true);

        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'category_rules');
    }

    Function checkCartRuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
//        echo "<pre>";
//        print_r($rule);
//        echo "</pre>";       
        global $xa_cart_quantities;
        global $xa_cart_weight;
        global $xa_cart_price;
        $min = (empty($rule['min']) == true) ? 1 : $rule['min'];
        $max = (empty($rule['max']) == true ) ? 999999 : $rule['max'];

        if ($max < $min && $max != 0) {
            return false;
        }
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $customer = new WC_Customer(get_current_user_id());
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table
        //if pid is selected in this rule

        if (is_cart() && (empty($pid) || !in_array($pid, array_keys($xa_cart_quantities)))) {
            return false;
        }

        $total_items_in_cart = 0;
        $total_all_units_in_cart = 0;
        $total_weight_in_cart = 0;
        $total_price_in_cart = 0;
        if (is_shop() || is_category() || is_product()) {
            $current_quantity++;
            if (empty($xa_cart_quantities[$pid])) {
                $total_items_in_cart++;
            }
            $total_all_units_in_cart++;
            $total_weight_in_cart += !empty($xa_cart_weight[$pid]) ? $xa_cart_weight[$pid] : (float) $product->get_weight();
            $total_price_in_cart += !empty($xa_cart_price[$pid]) ? $xa_cart_price[$pid] : (float) $price;
        }
        foreach ($xa_cart_quantities as $_pid => $_qnty) {
            $total_items_in_cart++;
            if (!empty($_qnty)) {
                $total_all_units_in_cart += $_qnty;
                if (!empty($xa_cart_weight[$_pid])) {
                    $total_weight_in_cart += ($_qnty * $xa_cart_weight[$_pid]);
                }
                if (!empty($xa_cart_price[$_pid])) {
                    $total_price_in_cart += ($_qnty * $xa_cart_price[$_pid]);
                }
            }
        }
        //error_log('total units=' . $total_all_units_of_this_category_in_cart . " total items=" . $total_items_of_this_category_in_cart);
        //error_log('total price=' . $total_price_of_this_category . " total weights=" . $total_weight_of_this_category);
        if ($rule['check_on'] == 'TotalQuantity' && ($total_all_units_in_cart < $min || $total_all_units_in_cart > $max || empty($total_all_units_in_cart))) {
            return false;
        } elseif ($rule['check_on'] == 'Quantity' && ($total_items_in_cart < $min || $total_items_in_cart > $max || empty($total_items_in_cart))) {
            return false;
        } elseif ($rule['check_on'] == 'Weight' && ($total_weight_in_cart < $min || $total_weight_in_cart > $max || empty($total_weight_in_cart))) {
            return false;
        } elseif ($rule['check_on'] == 'Price' && ($total_price_in_cart < $min || $total_price_in_cart > $max || empty($total_price_in_cart))) {
            return false;
        }

        $this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']] = $total_all_units_in_cart;   // for adjustment
        //to check best descount rule
        if ($this->execution_mode == "best_discount")
            $rule['calculated_discount'] = $this->SimpleExecute($price, $rule['rule_no'], $rule, $pid, $current_quantity, true);
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'cart_rules');
    }

    Function checkCombinationalRuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
        global $xa_cart_quantities;
        $total_units=0;
        $rule['product_id']= XA_WPML_Compatible_ids($rule['product_id'],'product',true);
        if (empty($rule['product_id']) || count($rule['product_id']) == 0 || !in_array($pid, array_keys($rule['product_id']))) {
            return false;
        }
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table
        //if pid is selected in this rule
        foreach ($rule['product_id'] as $_pid => $_qnty) {
            if (empty($xa_cart_quantities[$_pid]) || $xa_cart_quantities[$_pid] < $_qnty) {
                return false;
            } else {
                $total_units += !empty($xa_cart_quantities[$_pid])?$xa_cart_quantities[$_pid]:1;
            }
        }
        $rule['discount_on_product_id']=XA_WPML_Compatible_ids($rule['discount_on_product_id']);
        if (!empty($rule['discount_on_product_id']) && is_array($rule['discount_on_product_id']) && !in_array($pid, $rule['discount_on_product_id'])) {
            return false;
        }
        $this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']] = $total_units;   // for adjustment
        //to check best descount rule
        if ($this->execution_mode == "best_discount")
            $rule['calculated_discount'] = $this->SimpleExecute($price, $rule['rule_no'], $rule, $pid, $current_quantity, true);
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'combinational_rules');
    }

    Function checkProductRuleApplicableForProduct(&$rule = null, $product = null, $pid = null, $current_quantity = 1, $price = 0, $weight = 0) {
        if (empty($pid)) {
            $pid = xa_get_pid($product);
        }
        $min = (empty($rule['min']) == true) ? 1 : $rule['min'];
        $max = (empty($rule['max']) == true ) ? 999999 : $rule['max'];
        $total_price = !empty($price) ? ($price * $current_quantity) : 0;
        $total_weight = !empty($weight) ? ($weight * $current_quantity) : 0;
        if ($max < $min && $max != 0) {
            return false;
        }
        $repeat = false;
        if (isset($rule['repeat_rule']) && $rule['repeat_rule'] == 'yes') {
            $repeat = true;
        }
        //if pid is selected in this rule
        if (!empty($product) && $product->is_type('variation')) {
            $check_for_pid = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
        } else {
            $check_for_pid = $pid;
        }
        if ($rule['rule_on'] == 'products') {
            $pids = XA_WPML_Compatible_ids($rule['product_id']);
            if (empty($pids) || (!is_array($pids) || (!in_array($check_for_pid, $pids) && !in_array($pid, $pids)))) {
                return false;
            }
        } elseif ($rule['rule_on'] == 'categories') {
            if (!empty($product) && $product->is_type('variation')) {
                $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
                $parent_product = wc_get_product($parent_id);
                $product_categories = is_wc_version_gt_eql('2.7') ? $parent_product->get_category_ids() : xa_get_category_ids($parent_product);
            } else {
                $product_categories = is_wc_version_gt_eql('2.7') ? $product->get_category_ids() : xa_get_category_ids($product);
            }
            $cids=array();
            $cat_ids=$rule['category_id'];
            if(!is_array($cat_ids)) $cat_ids=array($cat_ids);
            foreach( $cat_ids as $_cid)
            {
                $cids[] = XA_WPML_Compatible_ids($_cid,'category');
            }       
            $matched=array_intersect($cids, $product_categories);
            if (empty($cids) || empty($matched)) {
                return false;
            }
        } elseif ($rule['rule_on'] == 'cart') {
            global $xa_cart_quantities;
            if (empty($xa_cart_quantities) || !in_array($pid, array_keys($xa_cart_quantities))) {
                return false;
            }
        }
        // if the rule is only applicable for some email ids
        if (!empty($rule['email_ids'])) {
            $current_user = wp_get_current_user();
            $customer_email = $current_user->user_email;
            $emails = explode(',', $rule['email_ids']);
            if (empty($customer_email) || !in_array($customer_email, $emails)) {
                return false;
            }
        }
        $customer = new WC_Customer(get_current_user_id());
        if (!empty($rule['prev_order_count']) || !empty($rule['prev_order_total_amt'])) {
            $order_count = $customer->get_order_count();
            $total_spent = $customer->get_total_spent();
            //error_log('order_count='.$order_count." total spent=".$total_spent);
            if (!empty($rule['prev_order_count']) && (int) $rule['prev_order_count'] > $order_count) {
                return false;
            }
            if (!empty($rule['prev_order_total_amt']) && (float) $rule['prev_order_total_amt'] > $total_spent) {
                return false;
            }
        }
        if ($this->for_offers_table == true) {
            return true;
        } // to show in offers table        
        if ($rule['check_on'] == 'Quantity' && ($current_quantity < $min || ($current_quantity > $max && $repeat == false))) {
            return false;
        } elseif ($rule['check_on'] == 'Weight' && ($total_weight < $min || ($total_weight > $max && $repeat == false ) || empty($total_weight))) {
            return false;
        } elseif ($rule['check_on'] == 'Price' && ($total_price < $min || ( $total_price > $max && $repeat == false) || empty($total_price))) {
            return false;
        }
        $this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']] = $current_quantity;   // for adjustment
        //to check best descount rule
        if ($this->execution_mode == "best_discount")
            $rule['calculated_discount'] = $this->SimpleExecute($price, $rule['rule_no'], $rule, $pid, $current_quantity, true);
        //checking roles and tofrom date for which rule is applicable
        return $this->check_date_range_and_roles($rule, 'product_rules');
    }

    function check_date_range_and_roles($rule, $rule_type) {
        $fromdate = $rule['from_date'];
        $todate = $rule['to_date'];
        $user_role = $rule['allow_roles'];
        //checking applicable roles
        if ($user_role != 'all' && !current_user_can($user_role)) {
            return false;
        }
        $now = date('d-m-Y');
        if ((empty($fromdate) && empty($todate)) || (empty($fromdate) && empty($todate) == false && (strtotime($now) <= strtotime($todate))) || (empty($fromdate) == false && (strtotime($now) >= strtotime($fromdate)) && empty($todate)) || ((strtotime($now) >= strtotime($fromdate)) && (strtotime($now) <= strtotime($todate)))) {
            
        } else {
            return false;
        }
        return true;
    }

    Public Function execute_rule($old_price, $rule_type_colon_rule_no, $rule, $current_quantity = 1, $pid = 0) {
        global $executed_rule_pid_price;
        $new_price = $old_price;
        $data = explode(':', $rule_type_colon_rule_no);
        $rule_type = $data[0];
        $rule_no = $data[1];
        if (isset($executed_rule_pid_price[$rule_type_colon_rule_no]) && !isset($_REQUEST['debug'])) {
            if (isset($executed_rule_pid_price[$rule_type_colon_rule_no][$pid])) {
                return $executed_rule_pid_price[$rule_type_colon_rule_no][$pid];
            }
        } else {
            $executed_rule_pid_price[$rule_type_colon_rule_no] = array();
        }

        switch ($rule_type) {
            case "product_rules":
                $new_price = $this->SimpleExecute($old_price, $rule_no, $rule, $pid);
                break;
            case "category_rules":
                $new_price = $this->SimpleExecute($old_price, $rule_no, $rule, $pid);
                break;
            case "cart_rules":
                $new_price = $this->SimpleExecute($old_price, $rule_no, $rule, $pid);
                break;
            case "combinational_rules":
                $new_price = $this->SimpleExecute($old_price, $rule_no, $rule, $pid);
                break;
            case "cat_combinational_rules":
                $new_price = $this->SimpleExecute($old_price, $rule_no, $rule, $pid);
                break;
            case "buy_get_free_rules":
                $new_price = $this->ExecuteBOGORule($old_price, $rule_no, $rule, $pid);
                break;
            case "BOGO_category_rules":
                $new_price = $this->ExecuteBOGO_category_Rule($old_price, $rule_no, $rule, $pid);
                break;
            
        }
        return $new_price;
    }

    Public Function SimpleExecute($old_price, $rule_no, $rule, $pid = 0, $current_quantity = 1, $do_not_execute = false) {
        global $xa_common_flat_discount, $executed_max_discount, $xa_cart_quantities, $executed_rule_pid_price;
        
        $new_price = $old_price;
        if (empty($executed_max_discount[$rule['rule_type'] . $rule_no])) {
            $executed_max_discount[$rule['rule_type'] . $rule_no] = 0;
        }
        $cart_quantity = 0;
        if (isset($xa_cart_quantities[$pid])) {
            if(isset($rule['repeat_rule']) && $rule['repeat_rule'] == 'yes' && !empty($rule['max']) && !empty($rule['min'])){
                $cart_quantity=$current_quantity;
            }else
            {
                $cart_quantity = $xa_cart_quantities[$pid];
            }
        }
        if (is_product() || is_shop() || is_category()) {
            $cart_quantity++;
        }
        extract($rule);
        $discount_amt = 0;
        if ($discount_type == 'Percent Discount') { 
            $discount_amt = floatval($value) * floatval($old_price) / 100;
        } elseif ($discount_type == 'Flat Discount') {
            if ($do_not_execute === true) {
                $discount_amt = floatval($value);
            } else {
                $prev=!empty($xa_common_flat_discount[$rule['rule_type'] . ":" . $rule_no.":".$pid])?$xa_common_flat_discount[$rule['rule_type'] . ":" . $rule_no.":".$pid]:0;
                $xa_common_flat_discount[$rule['rule_type'] . ":" . $rule_no.":".$pid] =  floatval($prev) + floatval($value);
            }
        } elseif ($discount_type == 'Fixed Price') {
            $discount_amt = floatval($old_price) - floatval($value);
        } else {
            $discount_amt = 0;
        } 
        $total_units = 1;
        if (!empty($this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']]) && is_numeric($this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']])) {
            $total_units = !empty($this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']])?$this->rule_based_quantity[$rule['rule_type'] . ":" . $rule['rule_no']]:1;
        }
        if (!empty($max_discount) && is_numeric($max_discount) && ($discount_amt * $cart_quantity ) > ($max_discount - $executed_max_discount[$rule['rule_type'] . $rule_no])) {
            $discount_amt = ($max_discount - $executed_max_discount[$rule['rule_type'] . $rule_no]) / $cart_quantity;
        } elseif (!empty($max_discount)) {
            $executed_max_discount[$rule['rule_type'] . $rule_no] += ($discount_amt * ( $cart_quantity) );
        }
        if (isset($adjustment) && is_numeric($adjustment)) {
            $discount_amt -= $adjustment / $total_units;
        }
        $new_price = $old_price - $discount_amt;
        if (isset($_GET['debug']) && $do_not_execute == false) {
            echo "\n<div id='rules_info' style=''><pre> RuleType= " . $rule['rule_type'] . " |   RuleNo=" . $rule_no . "  |   OldPrice=" . $old_price . "   |   Discount=$discount_amt  NewPrice=$new_price |   OfferName=" . $rule['offer_name'] . "</pre></div>";
        }

        if (!isset($executed_rule_pid_price[$rule['rule_type'] . ":" . $rule_no][$pid]) ) {
            $executed_rule_pid_price[$rule['rule_type'] . ":" . $rule_no][$pid] = $new_price;
        }
        return $new_price;
    }

    Public Function ExecuteBOGORule($old_price, $rule_no, $rule, $pid = 0) {
        global $xa_dp_setting;
        global $woocommerce;
        global $xa_cart_quantities;
        $product= wc_get_product($pid);
        $parent_id=$pid;
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
        }
        $rule['purchased_product_id']= XA_WPML_Compatible_ids($rule['purchased_product_id'],'product',true);
        $rule['free_product_id']= XA_WPML_Compatible_ids($rule['free_product_id'],'product',true);        
        ////////if free product is already in cart with exact quanitty this code will set its price as zero
        if (in_array($pid, array_keys($rule['free_product_id'])) &&  (!in_array($pid, array_keys($rule['purchased_product_id'])) || !in_array($parent_id, array_keys($rule['purchased_product_id'])))  ) {
            $all_free_product_present = true;
            foreach ($rule['free_product_id'] as $_pid => $_qnty) {
                if (empty($xa_cart_quantities[$_pid]) || $xa_cart_quantities[$_pid] < $_qnty) {
                    $all_free_product_present = false;
                    break;
                }
            }
            if ($all_free_product_present == true && $xa_dp_setting['auto_add_free_product_on_off'] != 'enable') {
                return $old_price * ($xa_cart_quantities[$_pid] - (float) $rule['free_product_id'][$pid] )  / $xa_cart_quantities[$_pid];
            }
        }
        /////////////////////////////////////////////////////////        
        $cart = $woocommerce->cart;
        extract($rule);
        if ($xa_dp_setting['auto_add_free_product_on_off'] == 'enable') {         // only works for different products
            foreach ($free_product_id as $pid2 => $qnty2) {
                $product_data = wc_get_product($pid2);
                if (empty($pid2) || empty($product_data)) {
                    continue;
                }
                if (isset($adjustment) && is_numeric($adjustment)) {
                    $product_data->set_price($adjustment);
                } else {
                    $product_data->set_price(0.0);
                }
                $cart_item_key = 'FreeForRule' . $rule['rule_no'] . md5($pid2);
                $cart->cart_contents[$cart_item_key] = array(
                    'product_id' => $pid2,
                    'variation_id' => 0,
                    'variation' => array(),
                    'quantity' => $qnty2,
                    'data' => $product_data
                );
            }
        }
        if (isset($_REQUEST['debug'])) {
            echo "\n<div id='rules_info' style=''><pre> RuleType= " . $rule['rule_type'] . " |   RuleNo=" . $rule_no . "  |   OldPrice=" . $old_price . "   |   OfferName=" . $rule['offer_name'] . "</pre></div>";
        }
        return $old_price;
    }
    Public Function ExecuteBOGO_category_Rule($old_price, $rule_no, $rule, $pid = 0) {
        global $xa_dp_setting;
        global $woocommerce;
        global $xa_cart_quantities;
        $product= wc_get_product($pid);
        $parent_id=$pid;
        if (!empty($product) && $product->is_type('variation')) {
            $parent_id = is_wc_version_gt_eql('2.7') ? $product->get_parent_id() : $product->parent->id;
        }
        $rule['purchased_category_id']= XA_WPML_Compatible_ids($rule['purchased_category_id'],'category',true);
        $rule['free_product_id']= XA_WPML_Compatible_ids($rule['free_product_id'],'product',true);        
        ////////if free product is already in cart with exact quanitty this code will set its price as zero
        if (in_array($pid, array_keys($rule['free_product_id']))) {
            $all_free_product_present = true;
            foreach ($rule['free_product_id'] as $_pid => $_qnty) {                
                if (empty($xa_cart_quantities[$_pid]) || $xa_cart_quantities[$_pid] < $_qnty) {
                    $all_free_product_present = false;
                    break;
                }
            }
            if ($all_free_product_present == true && $xa_dp_setting['auto_add_free_product_on_off'] != 'enable') {
                return $old_price * ($xa_cart_quantities[$_pid] - (float) $rule['free_product_id'][$pid] )  / $xa_cart_quantities[$_pid];
            }
        }
        /////////////////////////////////////////////////////////        
        $cart = $woocommerce->cart;
        extract($rule);
        if ($xa_dp_setting['auto_add_free_product_on_off'] == 'enable') {         // only works for different products
            
            foreach ($free_product_id as $pid2 => $qnty2) {
                $product_data = wc_get_product($pid2);
                if (empty($pid2) || empty($product_data)) {
                    continue;
                }
                if (isset($adjustment) && is_numeric($adjustment)) {
                    $product_data->set_price($adjustment);
                } else {
                    $product_data->set_price(0.0);
                }
                $cart_item_key = 'FreeForRule' . $rule['rule_no'] . md5($pid2);
                $cart->cart_contents[$cart_item_key] = array(
                    'product_id' => $pid2,
                    'variation_id' => 0,
                    'variation' => array(),
                    'quantity' => $qnty2,
                    'data' => $product_data
                );
            }
        }
        if (isset($_REQUEST['debug'])) {
            echo "\n<div id='rules_info' style=''><pre> RuleType= " . $rule['rule_type'] . " |   RuleNo=" . $rule_no . "  |   OldPrice=" . $old_price . "   |   OfferName=" . $rule['offer_name'] . "</pre></div>";
        }
        return $old_price;
    }

}
