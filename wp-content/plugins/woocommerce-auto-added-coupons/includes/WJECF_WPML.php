<?php

defined('ABSPATH') or die();

/**
 * Class to make WJECF compatible with WPML
 */
class WJECF_WPML extends Abstract_WJECF_Plugin {

    public function __construct() {
        $this->set_plugin_data( array(
            'description' => __( 'Compatiblity with WPML.', 'woocommerce-jos-autocoupon' ),
            'dependencies' => array(),
            'can_be_disabled' => true
        ) );        
    }

    public function init_hook() {
        global $sitepress;
        if ( isset( $sitepress ) ) {
            //WJECF_Controller hooks
            add_filter( 'wjecf_get_product_ids', array( $this, 'filter_get_product_ids' ), 10 );
            add_filter( 'wjecf_get_product_cat_ids', array( $this, 'filter_get_product_cat_ids' ), 10 );
        }
    }

//HOOKS

    public function filter_get_product_ids( $product_ids ) {
        return $this->get_translated_object_ids( $product_ids, 'product' );
    }

    public function filter_get_product_cat_ids( $product_cat_ids ) {
        return $this->get_translated_object_ids( $product_cat_ids, 'product_cat' );
    }


//FUNCTIONS

    /**
     * Get the ids of all the translations. Otherwise return the original array
     * 
     * @param int|array $product_ids The product_ids to find the translations for
     * @return array The product ids of all translations
     * 
     */
    public function get_translated_object_ids( $object_ids, $object_type ) {
        //Make sure it's an array
        if ( ! is_array( $object_ids ) ) {
            $object_ids = array( $object_ids );
        }

        $translated_object_ids = array();
        foreach( $object_ids as $object_id) {
            $translated_object_ids[] = apply_filters( 'wpml_object_id', $object_id, $object_type );
        }
        return $translated_object_ids;
    }
}
