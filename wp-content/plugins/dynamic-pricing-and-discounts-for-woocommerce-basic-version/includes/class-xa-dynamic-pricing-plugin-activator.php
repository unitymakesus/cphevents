<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.xadapter.com
 * @since      1.0.0
 *
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    xa_dynamic_pricing_plugin
 * @subpackage xa_dynamic_pricing_plugin/includes
 * @author     Your Name <email@example.com>
 */
class xa_dynamic_pricing_plugin_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        $dummy_option = array('product_rules' => array(), 'combinational_rules' => array(), 'cat_combinational_rules' => array(), 'category_rules' => array(), 'cart_rules' => array(), 'buy_get_free_rules' => array());
        update_option('xa_dp_rules', get_option('xa_dp_rules', $dummy_option));
        $enabled_modes=array(   'product_rules',
                                'combinational_rules',
                                'cat_combinational_rules',
                                'category_rules',
                                'cart_rules',
                                'buy_get_free_rules');
        $prev_data=get_option('xa_dynamic_pricing_setting',array());
        try {
            $prev_data = array(
                'product_rules_on_off' => in_array('product_rules', $enabled_modes) ? 'enable' : 'disable',
                'combinational_rules_on_off' => in_array('combinational_rules', $enabled_modes) ? 'enable' : 'disable',
                'category_rules_on_off' => in_array('category_rules', $enabled_modes) ? 'enable' : 'disable',
                'cat_comb_rules_on_off' => in_array('cat_combinational_rules', $enabled_modes) ? 'enable' : 'disable',
                'cart_rules_on_off' => in_array('cart_rules_on_off', $enabled_modes) ? 'enable' : 'disable',
                'buy_and_get_free_rules_on_off' => in_array('buy_and_get_free_rules', $enabled_modes) ? 'enable' : 'disable',
                'price_table_on_off' => !empty($prev_data['price_table_on_off']) ? $prev_data['price_table_on_off'] : 'disable',
                'offer_table_on_off' => !empty($prev_data['offer_table_on_off']) ? $prev_data['offer_table_on_off'] : 'disable',
                'auto_add_free_product_on_off' => !empty($prev_data['auto_add_free_product_on_off']) ? $prev_data['auto_add_free_product_on_off'] : 'enable',
                'pricing_table_qnty_shrtcode' => !empty($prev_data['pricing_table_qnty_shrtcode']) ? $prev_data['pricing_table_qnty_shrtcode'] : 'nos.',
                'pricing_table_position' => !empty($prev_data['pricing_table_position']) ? $prev_data['pricing_table_position'] : 'woocommerce_before_add_to_cart_button',
                'offer_table_position' => !empty($prev_data['offer_table_position']) ? $prev_data['offer_table_position'] : 'woocommerce_before_add_to_cart_button',
                'mode' => !empty($prev_data['mode']) ? $prev_data['mode'] : 'first_match',
                'execution_order' => $enabled_modes
            );
            update_option('xa_dynamic_pricing_setting', $prev_data);
        } catch (Exception $e) {
            error_log(print_r($e, true));
        }
    }

}
