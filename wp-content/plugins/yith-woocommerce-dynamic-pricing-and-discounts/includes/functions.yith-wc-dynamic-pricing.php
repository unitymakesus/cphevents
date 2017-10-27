<?php
if ( !defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  Yithemes
 */

if ( !function_exists( 'ywdpd_get_shop_categories' ) ) {
    function ywdpd_get_shop_categories( $show_all = true ) {
        global $wpdb;

        $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_cat" ORDER BY name ASC;' );

        $categories = array();
        if ( $show_all ) {
            $categories['0'] = __( 'All categories', 'ywcm' );
        }
        if ( $terms ) {
            foreach ( $terms as $cat ) {
                $categories[$cat->term_id] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
            }
        }
        return $categories;
    }
}
