<?php

defined('ABSPATH') or die();

/**
 * Wrapper for WC objects. Helps to maintain compatibility between both WC2 and WC3
 */
class WJECF_Wrap {
    protected $object = null;

    public function __construct( $object ) {
        $this->object = $object;
        //error_log('Wrapping ' . get_class( $object ) );
    }

    public $use_wc27 = true;
    public function get_id() {
        //Since WC 2.7
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_id' ) ) ) {
            return $this->object->get_id();
        }
        return $this->object->id;
    }

    public function holds( $object ) {
        return $object === $this->object;
    }

    /**
     * Get Meta Data by Key.
     * 
     * If no value found: 
     * If $single is true, an empty string is returned.
     * If $single is false, an empty array is returned.
     * 
     * @since  2.4.0
     * @param  string $key
     * @param  bool $single return first found meta, or all
     * @return mixed
     */
    public final function get_meta( $meta_key, $single = true ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_meta' ) ) ) {            
            return $this->get_meta_wc27( $meta_key, $single );
        }

        return $this->get_meta_legacy( $meta_key, $single );
    }

    protected function get_meta_wc27( $meta_key, $single = true ) {
        $values = $this->object->get_meta( $meta_key, $single );
        if ($single) {
            return $values; //it's just one, dispite the plural in the name!
        }

        if ( $values === '' ) {
            return array(); //get_meta returns empty string if meta does not exist
        }

        return wp_list_pluck( array_values( $values ), 'value' ); //when not using array_values; the index might not start with 0
    } 

    protected function get_meta_legacy( $meta_key, $single = true ) {
        throw new Exception( sprintf( '%s::get_meta_legacy not implemented', get_class( $this ) ) );
    }

    /**
     * Update single meta data item by meta key.
     * Call save() if the values must to be persisted.
     * @since  2.4.0
     * @param  string $meta_key
     * @param  mixed $value The value; use null to clear
     */
    public final function set_meta( $meta_key, $value ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'update_meta_data' ) ) ) {            
            if ( $value === null ) {
                $this->object->delete_meta_data( $meta_key );
            } else {
                $this->object->update_meta_data( $meta_key, $value );
            }
            return;
        }

        $this->set_meta_legacy( $meta_key, $value );
    }

    protected function set_meta_legacy( $meta_key, $value ) {
        throw new Exception( sprintf( '%s::set_meta_legacy not implemented', get_class( $this ) ) );
    }

}

/**
 * Wrap a data object ( Coupons and products were converted to WC_Data since WC 2.7.0 )
 */
class WJECF_Wrap_Coupon extends WJECF_Wrap {

    public function exists() {
        return $this->get_id() > 0;
    }

    public function get_code() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_code' ) ) ) {
            return $this->object->get_code();
        }

        return $this->object->code;
    }

    public function get_description() {
        if ( false && $this->use_wc27 && is_callable( array( $this->object, 'get_description' ) ) ) {
            return $this->object->get_description();
        }

        $post = get_post( $this->get_id() );
        return $post->post_excerpt;
    }

    public function get_amount() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_amount' ) ) ) {
            return $this->object->get_amount();
        }

        return $this->object->coupon_amount;
    }    

    public function get_individual_use() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_individual_use' ) ) ) {
            return $this->object->get_individual_use();
        }
        
        return $this->object->individual_use == 'yes';
    }

    public function get_limit_usage_to_x_items() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_limit_usage_to_x_items' ) ) ) {
            return $this->object->get_limit_usage_to_x_items();
        }
        
        return $this->object->limit_usage_to_x_items;
    }

    public function set_limit_usage_to_x_items( $limit_usage_to_x_items ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'set_limit_usage_to_x_items' ) ) ) {
            $this->object->set_limit_usage_to_x_items( $limit_usage_to_x_items );
        } else {
            $this->object->limit_usage_to_x_items = $limit_usage_to_x_items;
        }
    }

    public function get_discount_type() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_discount_type' ) ) ) {
            return $this->object->get_discount_type();
        }
        
        return $this->object->discount_type;
    }    

    public function set_discount_type( $discount_type ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'set_discount_type' ) ) ) {
            $this->object->set_discount_type( $discount_type );
        } else {
            $this->object->discount_type = $discount_type;
            $this->object->type = $discount_type;
        }
    }


    public function get_email_restrictions() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_email_restrictions' ) ) ) {
            return $this->object->get_email_restrictions();
        }
        
        return $this->object->customer_email;
    }

    public function get_product_ids() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_product_ids' ) ) ) {
            return $this->object->get_product_ids();
        }
        
        return $this->object->product_ids;
    }

    public function get_free_shipping() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_free_shipping' ) ) ) {
            return $this->object->get_free_shipping();
        }
        
        return $this->object->enable_free_shipping();
    }    

    public function get_product_categories() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_product_categories' ) ) ) {
            return $this->object->get_product_categories();
        }
        
        return $this->object->product_categories;
    }

    public function get_minimum_amount() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_minimum_amount' ) ) ) {
            return $this->object->get_minimum_amount();
        }
        
        return $this->object->minimum_amount;
    }

    /**
     * Set the product IDs this coupon cannot be used with.
     * @since  2.4.2 (For WC3.0)
     * @param  array $excluded_product_ids
     * @throws WC_Data_Exception
     */
    public function set_excluded_product_ids( $excluded_product_ids ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'set_excluded_product_ids' ) ) ) {
            $this->object->set_excluded_product_ids( $excluded_product_ids );
        } else {
             //NOTE: Prior to WC2.7 it was called exclude_ instead of excluded_
            $this->object->exclude_product_ids = $excluded_product_ids;
        }
    }

    /**
     * Set the product category IDs this coupon cannot be used with.
     * @since  2.4.2 (For WC3.0)
     * @param  array $excluded_product_categories
     * @throws WC_Data_Exception
     */
    public function set_excluded_product_categories( $excluded_product_categories ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'set_excluded_product_categories' ) ) ) {
            $this->object->set_excluded_product_categories( $excluded_product_categories );
        } else {
             //NOTE: Prior to WC2.7 it was called exclude_ instead of excluded_
            $this->object->exclude_product_categories = $excluded_product_categories;
        }
    }

    /**
     * Set if this coupon should excluded sale items or not.
     * @since  2.4.2 (For WC3.0)
     * @param  bool $exclude_sale_items
     * @throws WC_Data_Exception
     */
    public function set_exclude_sale_items( $exclude_sale_items ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'set_exclude_sale_items' ) ) ) {
            $this->object->set_exclude_sale_items( $exclude_sale_items );
        } else {
             //NOTE: Prior to WC2.7 it was yes/no instead of boolean
            $this->object->exclude_sale_items = $exclude_sale_items ? 'yes' : 'no';
        }
    }    

    /**
     * Check the type of the coupon
     * @param string|array $type The type(s) we want to check for
     * @return bool True if the coupon is of the type
     */
    public function is_type( $type ) {
        //Backwards compatibility 2.2.11
        if ( method_exists( $this->object, 'is_type' ) ) {
            return $this->object->is_type( $type );
        }
        
        return ( $this->object->discount_type == $type || ( is_array( $type ) && in_array( $this->object->discount_type, $type ) ) ) ? true : false;
    }    

    protected function set_meta_legacy( $meta_key, $value ) {
        $this->maybe_get_custom_fields();
        //WJECF()->log('...setting legacy meta ' . $meta_key );
        $this->legacy_custom_fields[ $meta_key ] = array( $value );
        $this->legacy_unsaved_keys[] = $meta_key;
    }

    /**
     * Save the metadata
     * @return id of this object
     */
    public function save() {
        //WJECF()->log('Saving ' . $this->get_id() );
        if ( $this->use_wc27 && is_callable( array( $this->object, 'save' ) ) ) {            
            return $this->object->save();
        }

        //Save the unsaved...
        foreach( $this->legacy_unsaved_keys as $meta_key ) {
            //WJECF()->log('...saving legacy meta ' . $meta_key );
            $value = reset( $this->legacy_custom_fields[ $meta_key ] );
            if ( $value === null ) {
                delete_post_meta( $this->get_id(), $meta_key );
            } else {
                update_post_meta( $this->get_id(), $meta_key, $value );
            }
        }
        $this->legacy_unsaved_keys = array();

        return $this->get_id();
    }

    protected $legacy_custom_fields = null; // [ 'meta_key' => [ array_of_values ] ]
    protected $legacy_unsaved_keys = array();

    protected function maybe_get_custom_fields() {
        //Read custom fields if not yet done
        if ( is_null( $this->legacy_custom_fields ) ) {
            $this->legacy_custom_fields = $this->object->coupon_custom_fields;
        }
    }

    protected function get_meta_legacy( $meta_key, $single = true ) {
        //Read custom fields if not yet done
        $this->maybe_get_custom_fields();

        if ( isset( $this->legacy_custom_fields[ $meta_key ] ) ) {
            $values = $this->legacy_custom_fields[ $meta_key ];
            //WP_CLI::log( "LEGACY:" . print_r( $values, true ));
            if ($single) {
                return maybe_unserialize( reset( $values ) ); //reset yields the first
            }
            $values = array_map( 'maybe_unserialize', $values );
            return $values;
        }

        return $single ? '' : array();
    }

}

class WJECF_Wrap_Product extends WJECF_Wrap {

    protected $legacy_custom_fields = null; // [ 'meta_key' => [ array_of_values ] ]
    protected $legacy_unsaved_keys = array();

    protected function get_meta_legacy( $meta_key, $single = true ) {
        if ( isset( $this->legacy_custom_fields[ $meta_key ] ) ) {
            $values = $this->legacy_custom_fields[ $meta_key ];
            //WP_CLI::log( "LEGACY:" . print_r( $values, true ));
            if ($single) {
                return maybe_unserialize( reset( $values ) ); //reset yields the first
            }
            $values = array_map( 'maybe_unserialize', $values );
            return $values;
        }

        return get_post_meta( $this->get_product_or_variation_id(), $field_name, $single );
    }

    public function set_meta_legacy( $meta_key, $value ) {
        $this->legacy_custom_fields[ $meta_key ] = array( 0 => $value );
        $this->legacy_unsaved_keys[] = $meta_key;
    } 

    /**
     * Combines get_meta or get_prop (in legacy WC those were the same thing, in WC3.0+ there is a difference)
     * @param string $field_name 
     * @param bool $single 
     * @return
     */
    public function get_field($field_name, $single = true) {
        if ( $this->use_wc27 ) {
            $values = $this->get_meta($field_name, $single);
            if ( ! empty( $values ) ) return $values;

            if ( is_callable( array( $this->object, 'get_prop' ) ) ) {
                $value = $this->object->get_prop($field_name);
                if ( ! empty( $value ) ) return $single ? $value : array( $value );
            }
        }

        return get_post_meta( $this->get_product_or_variation_id(), $field_name, $single );
    }   

    private function is_variation() {
        return $this->object instanceof WC_Product_Variation;
    }

    /**
     * Retrieve the id of the product or the variation id if it's a variant.
     * 
     * (2.4.0: Moved from WJECF_Controller to WJECF_WC)
     * 
     * @param WC_Product $product 
     * @return int|bool The variation or product id. False if not a valid product
     */
    public function get_product_or_variation_id() {
        if ( $this->is_variation() ) {
            return $this->get_variation_id();
        } elseif ( $this->object instanceof WC_Product ) {
            return $this->get_id();
        } else {
            return false;
        }
    }

    /**
     * Retrieve the id of the parent product if it's a variation; otherwise retrieve this products id
     * 
     * (2.4.0: Moved from WJECF_Controller to WJECF_WC)
     * 
     * @param WC_Product $product 
     * @return int|bool The product id. False if this product is not a variation
     */
    public function get_variable_product_id() {
        if ( ! $this->is_variation() ) {
            return false;
        }

        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_parent_id' ) ) ) {
            return $this->object->get_parent_id();
        } else {
            return wp_get_post_parent_id( $this->object->variation_id );
        }
    }

    /**
     * Get current variation id
     * @return int|bool False if this is not a variation
     */
    protected function get_variation_id() {
        if ( ! $this->is_variation() ) {
            return false;
        }

        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_id' ) ) ) {
            return $this->object->get_id(); 
        } elseif ( $this->use_wc27 && is_callable( array( $this->object, 'get_variation_id' ) ) ) {
            return $this->object->get_variation_id(); 
        }
        return $this->object->variation_id;
    }


    public function get_name() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_name' ) ) ) {
            return $this->object->get_name();
        } else {
            return $this->object->post->post_title;
        }
    }    

    public function get_description() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_description' ) ) ) {
            return $this->object->get_description();
        } else {
            return $this->object->post->post_content;
        }
    }

    public function get_short_description() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_short_description' ) ) ) {
            return $this->object->get_short_description();
        } else {
            return $this->object->post->post_excerpt;
        }
    }

    public function get_tag_ids() {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_tag_ids' ) ) ) {
            return $this->object->get_tag_ids();
        } else {
            return $this->legacy_get_term_ids( 'product_tag' );
        }
    }

    protected function legacy_get_term_ids( $taxonomy ) {
        $terms = get_the_terms( $this->get_id(), $taxonomy );
        if ( false === $terms || is_wp_error( $terms ) ) {
            return array();
        }
        return wp_list_pluck( $terms, 'term_id' );
    }

    /**
     * If set, get the default attributes for a variable product.
     *
     * @param string $attribute_name
     * @return string
     */
    public function get_variation_default_attribute( $attribute_name ) {
        if ( $this->use_wc27 && is_callable( array( $this->object, 'get_variation_default_attribute' ) ) ) {
            return $this->object->get_variation_default_attribute( $attribute_name );
        }
        return '';
    }

}