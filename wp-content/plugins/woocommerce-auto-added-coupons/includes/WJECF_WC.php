<?php

defined('ABSPATH') or die();

/**
 * 
 * Interface to WooCommerce. Handles version differences / backwards compatibility.
 * 
 * @since 2.3.7.2
 */
class WJECF_WC {

    protected $wrappers = array();

    /**
     * Wrap a data object (WC 2.7 introduced WC_Data)
     * @param type $object 
     * @return type
     */
    public function wrap( $object, $use_pool = true ) {
        if ( $use_pool ) {
            //Prevent a huge amount of wrappers to be initiated; one wrapper per object instance should do the trick
            foreach( $this->wrappers as $wrapper ) {
                if ($wrapper->holds( $object ) ) {
                    //error_log('Reusing wrapper ' . get_class( $object ) );
                    return $wrapper;
                }
            }
        }

        if ( is_numeric( $object ) ) {
            $post_type = get_post_type( $object );
            if ( $post_type == 'shop_coupon' ) {
                $object = WJECF_WC()->get_coupon( $object );
            } elseif ( $post_type == 'product' ) {
                $object = new WC_Product( $object );
            } 
        }
        if ( is_string( $object ) ) {
            $object = WJECF_WC()->get_coupon( $object );
        }


        if ( $object instanceof WC_Coupon ) {
            return $this->wrappers[] = new WJECF_Wrap_Coupon( $object );
        }

        if ( $object instanceof WC_Product ) {
            return $this->wrappers[] = new WJECF_Wrap_Product( $object );
        }

        throw new Exception( 'Cannot wrap ' . get_class( $object ) );
    }

    /**
     * Returns a specific item in the cart.
     *
     * @param string $cart_item_key Cart item key.
     * @return array Item data
     */
    public function get_cart_item( $cart_item_key ) {        
        if ( $this->check_woocommerce_version('2.2.9') ) {
            return WC()->cart->get_cart_item( $cart_item_key );
        }

        return isset( WC()->cart->cart_contents[ $cart_item_key ] ) ? WC()->cart->cart_contents[ $cart_item_key ] : array();
       }

    /**
     * Get categories of a product (and anchestors)
     * @param int $product_id 
     * @return array product_cat_ids
     */
    public function wc_get_product_cat_ids( $product_id ) {
        //Since WC 2.5.0
        if ( function_exists( 'wc_get_product_cat_ids' ) ) {
            return wc_get_product_cat_ids( $product_id );
        }

        $product_cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

        foreach ( $product_cats as $product_cat ) {
            $product_cats = array_merge( $product_cats, get_ancestors( $product_cat, 'product_cat' ) );
        }
        return $product_cats;
    }

    /**
     * Coupon types that apply to individual products. Controls which validation rules will apply.
     *
     * @since  2.5.0
     * @return array
     */
    public function wc_get_product_coupon_types() {
        //Since WC 2.5.0
        if ( function_exists( 'wc_get_product_coupon_types' ) ) {
            return wc_get_product_coupon_types();
        }
        return array( 'fixed_product', 'percent_product' );
    }

    public function wc_get_cart_coupon_types() {
        //Since WC 2.5.0
        if ( function_exists( 'wc_get_cart_coupon_types' ) ) {
            return wc_get_cart_coupon_types();
        }
        return array( 'fixed_cart', 'percent' );
    }

    /**
     * Output a list of variation attributes for use in the cart forms.
     *
     * @param array $args
     * @since 2.5.1
     */
    public function wc_dropdown_variation_attribute_options( $args = array() ) {
        if ( function_exists( 'wc_dropdown_variation_attribute_options' ) ) {
            return wc_dropdown_variation_attribute_options( $args );
        }

        //Copied from WC2.4.0 wc-template-functions.php
        $args = wp_parse_args( $args, array(
            'options'          => false,
            'attribute'        => false,
            'product'          => false,
            'selected'         => false,
            'name'             => '',
            'id'               => '',
            'show_option_none' => __( 'Choose an option', 'woocommerce' )
        ) );

        $options   = $args['options'];
        $product   = $args['product'];
        $attribute = $args['attribute'];
        $name      = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
        $id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );

        if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
            $attributes = $product->get_variation_attributes();
            $options    = $attributes[ $attribute ];
        }

        echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '">';

        if ( $args['show_option_none'] ) {
            echo '<option value="">' . esc_html( $args['show_option_none'] ) . '</option>';
        }

        if ( ! empty( $options ) ) {
            if ( $product && taxonomy_exists( $attribute ) ) {
                // Get terms if this is a taxonomy - ordered. We need the names too.
                $terms = wc_get_product_terms( $product->id, $attribute, array( 'fields' => 'all' ) );

                foreach ( $terms as $term ) {
                    if ( in_array( $term->slug, $options ) ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . apply_filters( 'woocommerce_variation_option_name', $term->name ) . '</option>';
                    }
                }
            } else {
                foreach ( $options as $option ) {
                    // This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
                    $selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
                    echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                }
            }
        }

        echo '</select>';
    }

    /**
     * Get attibutes/data for an individual variation from the database and maintain it's integrity.
     * @since  2.5.1
     * @param  int $variation_id
     * @return array
     */
    public function wc_get_product_variation_attributes( $variation_id ) {
        if ( function_exists( 'wc_get_product_variation_attributes' ) ) {
            return wc_get_product_variation_attributes( $variation_id );
        }

        //Copied from WC2.4.0 wc-product-functions.php

        // Build variation data from meta
        $all_meta                = get_post_meta( $variation_id );
        $parent_id               = wp_get_post_parent_id( $variation_id );
        $parent_attributes       = array_filter( (array) get_post_meta( $parent_id, '_product_attributes', true ) );
        $found_parent_attributes = array();
        $variation_attributes    = array();

        // Compare to parent variable product attributes and ensure they match
        foreach ( $parent_attributes as $attribute_name => $options ) {
            $attribute                 = 'attribute_' . sanitize_title( $attribute_name );
            $found_parent_attributes[] = $attribute;
            if ( ! array_key_exists( $attribute, $variation_attributes ) ) {
                $variation_attributes[ $attribute ] = ''; // Add it - 'any' will be asumed
            }
        }

        // Get the variation attributes from meta
        foreach ( $all_meta as $name => $value ) {
            // Only look at valid attribute meta, and also compare variation level attributes and remove any which do not exist at parent level
            if ( 0 !== strpos( $name, 'attribute_' ) || ! in_array( $name, $found_parent_attributes ) ) {
                unset( $variation_attributes[ $name ] );
                continue;
            }
            /**
             * Pre 2.4 handling where 'slugs' were saved instead of the full text attribute.
             * Attempt to get full version of the text attribute from the parent.
             */
            if ( sanitize_title( $value[0] ) === $value[0] && version_compare( get_post_meta( $parent_id, '_product_version', true ), '2.4.0', '<' ) ) {
                foreach ( $parent_attributes as $attribute ) {
                    if ( $name !== 'attribute_' . sanitize_title( $attribute['name'] ) ) {
                        continue;
                    }
                    $text_attributes = wc_get_text_attributes( $attribute['value'] );

                    foreach ( $text_attributes as $text_attribute ) {
                        if ( sanitize_title( $text_attribute ) === $value[0] ) {
                            $value[0] = $text_attribute;
                            break;
                        }
                    }
                }
            }

            $variation_attributes[ $name ] = $value[0];
        }

        return $variation_attributes;
    } 

    public function find_matching_product_variation( $product, $match_attributes = array() ) {
        if ( $this->check_woocommerce_version( '3.0') ) {
            $data_store   = WC_Data_Store::load( 'product' );
            $variation_id = $data_store->find_matching_product_variation( $product, $match_attributes );
            return $variation_id;
        }

        return $product->get_matching_variation( $match_attributes );      
    }

    /**
     * @since 2.4.0 for WC 2.7 compatibility
     * 
     * Get a WC_Coupon object
     * @param WC_Coupon|string|WP_Post $coupon The coupon code or a WC_Coupon object
     * @return WC_Coupon The coupon object
     */
    public function get_coupon( $coupon ) {
        if ( $coupon instanceof WP_Post ) {
            $coupon = $coupon->ID;
        }
        if ( is_numeric( $coupon ) && ! $this->check_woocommerce_version( '3.0' ) ) {
            //By id; not neccesary for WC3.0; as the WC_Coupon constructor accepts an id
            global $wpdb;
            $coupon_code = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE id = %d AND post_type = 'shop_coupon'", $coupon ) );
            if ( $coupon_code !== null) {
                $coupon = $coupon_code;
            }
        }
        if ( ! ( $coupon instanceof WC_Coupon ) ) {
            //By code
            $coupon = new WC_Coupon( $coupon );
        }
        return $coupon;
    }    


//VERSION

    /**
     * Check whether WooCommerce version is greater or equal than $req_version
     * @param string @req_version The version to compare to
     * @return bool true if WooCommerce is at least the given version
     */
    public function check_woocommerce_version( $req_version ) {
        return version_compare( $this->get_woocommerce_version(), $req_version, '>=' );
    }    

    private $wc_version = null;
    
    /**
     * Get the WooCommerce version number
     * @return string|bool WC Version number or false if WC not detected
     */
    public function get_woocommerce_version() {
        if ( isset( $this->wc_version ) ) {
            return $this->wc_version;
        }

        if ( defined( 'WC_VERSION' ) ) {
            return $this->wc_version = WC_VERSION;
        }

        // If get_plugins() isn't available, require it
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }        
        // Create the plugins folder and file variables
        $plugin_folder = get_plugins( '/woocommerce' );
        $plugin_file = 'woocommerce.php';
        
        // If the plugin version number is set, return it 
        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $this->wc_version = $plugin_folder[$plugin_file]['Version'];
        }

        return $this->wc_version = false; // Not found
    }

//INSTANCE

    /**
     * Singleton Instance
     *
     * @static
     * @return Singleton Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected static $_instance = null;
}
