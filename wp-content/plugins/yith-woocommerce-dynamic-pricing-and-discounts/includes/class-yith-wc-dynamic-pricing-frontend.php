<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWDPD_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements frontend features of YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing_Frontend
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_Dynamic_Pricing_Frontend' ) ) {

    class YITH_WC_Dynamic_Pricing_Frontend {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Dynamic_Pricing_Frontend
         */

        protected static $instance;


        /**
         * The pricing rules
         *
         * @access public
         * @var string
         * @since 1.0.0
         */
        public $pricing_rules = array();



        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Dynamic_Pricing_Frontend
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

            if ( YITH_WC_Dynamic_Pricing()->get_option('enabled') != 'yes' ){
                return;
            }

            $this->pricing_rules  = YITH_WC_Dynamic_Pricing()->get_pricing_rules();

            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_process_discounts' ), 99 );
            add_action( 'woocommerce_ajax_added_to_cart', array( $this, 'cart_process_discounts' ), 110 );
            add_filter( 'woocommerce_cart_item_price', array( $this, 'replace_cart_item_price' ), 100, 3 );
        }


        /**
         * Process dynamic pricing in cart
         *
         * @since  1.0.0
         * @author Emanuela Castorina
         */

        public function cart_process_discounts( ){

            if ( empty( WC()->cart->cart_contents ) ){
                return;
            }

            foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

                if ( isset( WC()->cart->cart_contents[$cart_item_key]['ywdpd_discounts'] ) ) {
                    unset( WC()->cart->cart_contents[$cart_item_key]['ywdpd_discounts'] );
                }

                $item_discounts = YITH_WC_Dynamic_Pricing()->get_adjusts_to_product( $cart_item );


                if ( !empty( $item_discounts ) ) {
                    YITH_WC_Dynamic_Pricing()->apply_discount( $cart_item, $cart_item_key, $item_discounts );
                }

            }

        }

		/**
		 * Replace the price in the cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
        public function replace_cart_item_price( $price, $cart_item, $cart_item_key ){

            if ( !isset( $cart_item['ywdpd_discounts'] ) ) {
                return $price;
            }

            return '<del>' . wc_price( $cart_item['ywdpd_discounts']['default_price'] ) . '</del>'. WC()->cart->get_product_price( $cart_item['data'] );

        }

    }
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Frontend class
 *
 * @return \YITH_WC_Dynamic_Pricing_Frontend
 */
function YITH_WC_Dynamic_Pricing_Frontend() {
    return YITH_WC_Dynamic_Pricing_Frontend::get_instance();
}

