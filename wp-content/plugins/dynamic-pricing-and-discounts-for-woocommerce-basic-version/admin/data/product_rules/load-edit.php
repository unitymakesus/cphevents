<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
} 
$dummy_option = array('product_rules' => array(), 'combinational_rules' => array(), 'cat_combinational_rules' => array(), 'category_rules' => array(), 'cart_rules' => array());
$rules_option_array = get_option('xa_dp_rules', $dummy_option);
$defaultOptions = array();
$customOptions =isset($rules_option_array[$active_tab][$_REQUEST['edit']])?$rules_option_array[$active_tab][$_REQUEST['edit']]:array();
$_REQUEST = array_merge($_REQUEST, $customOptions);