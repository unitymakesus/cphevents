<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWDPD_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_Dynamic_Pricing' ) ) {

    class YITH_WC_Dynamic_Pricing {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Dynamic_Pricing
         */

        protected static $instance;

        /**
         * The name for the plugin options
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $plugin_options = 'yit_ywdpd_options';

        public $validated_rules = array();


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Dynamic_Pricing
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function __construct() {

            /* plugin */
            add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
        }

        /**
         * Return pricing rules filtered and validates
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        function get_pricing_rules(){

            $pricing_rules  = $this->filter_valid_rules(  $this->get_option( 'pricing-rules' ) );

            return $pricing_rules;
        }

	    /**
	     * Return pricing rules validates
	     *
	     * Initialize plugin and registers actions and filters to be used
	     *
	     * @since  1.0.0
	     * @author Emanuela Castorina
	     *
	     * @param $pricing_rules
	     *
	     * @return mixed
	     */
        function filter_valid_rules( $pricing_rules ){
            
            if( !is_array( $pricing_rules ) ){
                return;
            }


            foreach( $pricing_rules as $key => $rule ){

                //check if the rule is active of the value of discount_amount is empty
                if( $rule['active'] != 'yes' ||  ( isset( $rule['discount_amount'] ) && $rule['discount_amount'] == '') || ! isset( $rule['min_quantity'] ) ){
                    continue;
                }

                //check if the discount is must be applied to specific categories
                if( $rule['apply_to'] == 'categories' && ! isset( $rule['categories']) ){
                    continue;
                }

                if( $rule['discount_amount'] > 1 ){
                    $rule['discount_amount'] = $rule['discount_amount'] / 100;
                }

                if( $rule['min_quantity'] == '' ||  $rule['min_quantity'] == 0  ){
                    $rule['min_quantity'] = 1;
                }

                $this->validated_rules[$key] = $rule;

            }

            return $this->validated_rules;
        }

	    /**
	     * Return all adjustments to single cart item
	     *
	     *
	     * @since  1.0.0
	     * @author Emanuela Castorina
	     *
	     * @param $cart_item
	     *
	     * @return mixed
	     */
        function get_adjusts_to_product( $cart_item ) {

            if ( empty( $cart_item ) ) {
                return false;
            }

            $item_discounts = array();

            foreach ( $this->validated_rules as $key_rule => $rule ) {
                if ( $rule['apply_to'] == 'all-products' ) {
                    if( $cart_item['quantity'] >=  $rule['min_quantity'] ){
                        $item_discounts[$key_rule] = array( 'type' => 'percentage', 'amount' => $rule['discount_amount'] );
                    }
                }

                if ( $rule['apply_to'] == 'categories' ) {
                    if ( $this->product_categories_validation( $cart_item['product_id'], $rule['categories'], $rule['min_quantity'] ) ) {

                        $item_discounts[$key_rule] = array( 'type' => 'percentage', 'amount' => $rule['discount_amount'] );
                    }
                }

                if ( ! empty( $item_discounts ) ) {
                    break;
                }

            }

            return $item_discounts;

        }

	    /**
	     * Return all adjustments to single cart item
	     *
	     *
	     * @since  1.0.0
	     * @author Emanuela Castorina
	     *
	     * @param $cart_item
	     * @param $cart_item_key
	     * @param $item_discounts
	     */
        public function apply_discount( $cart_item, $cart_item_key, $item_discounts ){

            $default_price = $cart_item['data']->get_price();
            $price = $default_price;
            foreach( $item_discounts as $key_discount => $discount ){
                if( $discount['type'] == 'percentage' ){
                    $price = $price - $price * $discount['amount'];
                }
            }

            $product = WC()->cart->cart_contents[$cart_item_key]['data'];

            WC()->cart->cart_contents[$cart_item_key]['ywdpd_discounts'] = array(
                'default_price'    => ( WC()->cart->tax_display_cart == 'excl' ) ? yit_get_price_excluding_tax( $product ) : yit_get_price_including_tax( $product ),
                'discount_applied' => $item_discounts,
            );


	        if ( version_compare( WC()->version, '2.6', '<' ) ) {
		        WC()->cart->cart_contents[$cart_item_key]['data']->price = $price;
		        WC()->cart->cart_contents[$cart_item_key]['data']->has_dynamic_price = true;
	        }else{
		        WC()->cart->cart_contents[$cart_item_key]['data']->set_price($price);
		        WC()->cart->cart_contents[$cart_item_key]['data']->has_dynamic_price = true;
	        }


        }

	    /**
	     * Check if a product has specific categories
	     *
	     *
	     * @since  1.0.0
	     * @author Emanuela Castorina
	     *
	     * @param $product_id
	     * @param $categories
	     * @param $min_amount
	     *
	     * @return bool
	     */
        function product_categories_validation( $product_id, $categories, $min_amount ) {

            $categories_cart = YITH_WC_Dynamic_Pricing_Helper()->cart_categories;
            $intersect_cart_category = array_intersect( $categories, $categories_cart );

            $return = false;

            if( is_array( $intersect_cart_category ) ){
                $categories_counter = YITH_WC_Dynamic_Pricing_Helper()->categories_counter;
                $categories_of_item = wc_get_product_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );
                $intersect_product_category = array_intersect( $categories_of_item, $categories );

                if( !empty ( $intersect_product_category ) ){
                    $tot = 0;
                    foreach( $categories as $cat ){
                        $tot += ( isset( $categories_counter[$cat] ) ) ? $categories_counter[$cat] : 0;

                    }

                    if( $tot >= $min_amount ){
                        $return = true;
                    }
                }

            }

            return $return;

        }

	    /**
	     * Load YIT Plugin Framework
	     *
	     * @since  1.0.0
	     * @return void
	     * @author Emanuela Castorina
	     */
	    public function plugin_fw_loader() {
		    if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
			    global $plugin_fw_data;
			    if( ! empty( $plugin_fw_data ) ){
				    $plugin_fw_file = array_shift( $plugin_fw_data );
				    require_once( $plugin_fw_file );
			    }
		    }
	    }


	    /**
         * Get options from db
         *
         * @access public
         * @since 1.0.0
         * @author Francesco Licandro <francesco.licandro@yithemes.com>
         * @param $option string
         * @return mixed
         */
        public function get_option( $option ) {
            // get all options
            $options = get_option( $this->plugin_options );

            if( isset( $options[ $option ] ) ) {
                return $options[ $option ];
            }

            return false;
        }

    }
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing class
 *
 * @return \YITH_WC_Dynamic_Pricing
 */
function YITH_WC_Dynamic_Pricing() {
    return YITH_WC_Dynamic_Pricing::get_instance();
}

