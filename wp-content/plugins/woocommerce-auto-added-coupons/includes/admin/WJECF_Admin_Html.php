<?php

/**
 * HTML Output functions for admin
 */
class WJECF_Admin_Html {
    

    /**
     * 2.3.6
     * Renders a <SELECT> that has a default value. Relies on woocommerce_wp_select
     * 
     * field['default_value']:  The default value (if omitted the first option will be default)
     * field['append_default_label']: If true or omitted the text '(DEFAULT)' will be appended to the default option caption
     * 
     * @param array $field see wc-meta-box-functions.php:woocommerce_wp_select
     * @return void
     */
    public static function render_select_with_default( $field ) {
        global $thepostid, $post;
        $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

        reset( $field['options'] ); //move to first for key()
        $default_value = isset( $field['default_value'] ) ? $field['default_value'] : key( $field['options'] );
        $append_default_label = isset( $field['append_default_label'] ) ? $field['append_default_label'] : true;

        if ( $append_default_label ) {
            $field['options'][$default_value] = sprintf( __( '%s (Default)', 'woocommerce-jos-autocoupon' ), $field['options'][$default_value] );
        }

        if ( ! isset( $field['value'] ) ) {
            $field['value'] = get_post_meta( $thepostid, $field['id'], true );
        }
        if ( empty( $field['value'] ) ) {
            $field['value'] = $default_value;
        }

        woocommerce_wp_select( $field );
    }

    public static function render_admin_cat_selector( $dom_id, $field_name, $selected_ids, $placeholder = null ) {
        if ( $placeholder === null ) $placeholder = __( 'Search for a product…', 'woocommerce' );

        // Categories
        ?>                
        <select id="<?php esc_attr_e( $dom_id ) ?>" name="<?php esc_attr_e( $field_name ) ?>[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php esc_attr_e( $placeholder ); ?>">
            <?php
                $categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

                if ( $categories ) foreach ( $categories as $cat ) {
                    echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $selected_ids ), true, false ) . '>' . esc_html( $cat->name ) . '</option>';
                }
            ?>
        </select>
        <?php    
    }


    /**
     * Display a WooCommerce help tip
     * @param string $tip The tip to display
     * @return string
     */
    public static function wc_help_tip( $tip ) {
        //Since WC 2.5.0
        if ( function_exists( 'wc_help_tip' ) ) {
            return wc_help_tip( $tip );
        }

        return '<img class="help_tip" style="margin-top: 21px;" data-tip="' . esc_attr( $tip ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
    }

    /**
     * Renders a product selection <input>. Will use either select2 v4 (WC3.0+) select2 v3 (WC2.3+) or chosen (< WC2.3)
     * @param string $dom_id 
     * @param string $field_name 
     * @param array $selected_ids Array of integers
     * @param string|null $placeholder 
     * @return void
     */
    public static function render_admin_product_selector( $dom_id, $field_name, $selected_ids, $placeholder = null ) {
        $product_key_values = array();
        foreach ( $selected_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            if ( is_object( $product ) ) {
                $product_key_values[ esc_attr( $product_id ) ] = wp_kses_post( $product->get_formatted_name() );
            }
        }

        if ( $placeholder === null ) $placeholder = __( 'Search for a product…', 'woocommerce' );

        //In WooCommerce version 2.3.0 chosen was replaced by select2
        //In WooCommerce version 3.0 select2 v3 was replaced by select2 v4
        if ( WJECF_WC()->check_woocommerce_version('3.0') ) {
            self::render_admin_select2_v4_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
        } elseif ( WJECF_WC()->check_woocommerce_version('2.3.0') ) {
            self::render_admin_select2_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
        } else {
            self::render_admin_chosen_product_selector( $dom_id, $field_name, $product_key_values, $placeholder );
        }
    }


    /**
     * Renders a product selection <input>. 
     * Chosen (Legacy)
     * @param string $dom_id 
     * @param string $field_name 
     * @param array $selected_ids Array of integers
     * @param string|null $placeholder 
     */    
    private static function render_admin_chosen_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
        // $selected_keys_and_values must be an array of [ id => name ]

        echo '<select id="' . esc_attr( $dom_id ) . '" name="' . esc_attr( $field_name ) . '[]" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="' . esc_attr( $placeholder ) . '">';
        foreach ( $selected_keys_and_values as $product_id => $product_name ) {
            echo '<option value="' . $product_id . '" selected="selected">' . $product_name . '</option>';
        }
        echo '</select>';
    }

    /**
     * @since 2.4.1 for WC 3.0 compatibility
     * 
     * Renders a product selection <input>. 
     * Select2 version 3 (Since WC 2.3.0)
     * @param string $dom_id 
     * @param string $field_name 
     * @param array $selected_ids Array of integers
     * @param string|null $placeholder 
     */    
    private static function render_admin_select2_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
        // $selected_keys_and_values must be an array of [ id => name ]

        $json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );
        echo '<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="' 
        . esc_attr( $field_name ) . '" data-placeholder="' 
        . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_products_and_variations" data-selected="' 
        . $json_encoded . '" value="' . implode( ',', array_keys( $selected_keys_and_values ) ) . '" />';

    }  

    /**
     * Renders a product selection <input>. 
     * Select2 version 4 (Since WC 3.0)
     * @param string $dom_id 
     * @param string $field_name 
     * @param string $selected_keys_and_values 
     * @param string $placeholder 
     */
    private static function render_admin_select2_v4_product_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
        // $selected_keys_and_values must be an array of [ id => name ]

        $json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );

        echo '<select id="'. esc_attr( $dom_id ) .'" class="wc-product-search" name="'
        . esc_attr( $field_name ) . '[]" multiple="multiple" style="width: 50%;" data-placeholder="'
        . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_products_and_variations">';

        foreach ( $selected_keys_and_values as $product_id => $product_name ) {
            echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product_name ) . '</option>';
        }

        echo '</select>';
    }


    /**
     * Renders a customer selection <input>. Will use either select2 v4 (WC3.0+) or select2 v3 (WC2.3+)
     * @param string $dom_id 
     * @param string $field_name 
     * @param array $selected_customer_ids Array of integers
     * @param string|null $placeholder 
     * @return void
     */
    public static function render_admin_customer_selector( $dom_id, $field_name, $selected_customer_ids, $placeholder = null ) {
        $selected_keys_and_values    = array();    
        foreach ( $selected_customer_ids as $customer_id ) {
            $customer = get_userdata( $customer_id );
            if ( is_object( $customer ) ) {
                $selected_keys_and_values[ $customer_id ] = $customer->display_name . ' (#' . $customer->ID . ' &ndash; ' . sanitize_email( $customer->user_email ) . ')';
            }
        }
        if ( $placeholder === null ) $placeholder = __( 'Any customer', 'woocommerce-jos-autocoupon' );

        //In WooCommerce version 2.3.0 chosen was replaced by select2
        //In WooCommerce version 3.0 select2 v3 was replaced by select2 v4
        if ( WJECF_WC()->check_woocommerce_version('3.0') ) {
            self::render_admin_select2_v4_customer_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder );
        } else {
            self::render_admin_select2_customer_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder );
        }
    }

    private static function render_admin_select2_customer_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
        // $selected_keys_and_values must be an array of [ id => name ] .e.g. [ 12 => 'John Smith (#12 john.smith@example.com)' ]

        $json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );
        echo '<input type="hidden" class="wc-customer-search" data-multiple="true" style="width: 50%;" name="'
        . esc_attr( $field_name ) . '" data-placeholder="'
        . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_customers" data-selected="'
        . $json_encoded . '" value="' . implode( ',', array_keys( $selected_keys_and_values ) ) . '" />';
    }

    private static function render_admin_select2_v4_customer_selector( $dom_id, $field_name, $selected_keys_and_values, $placeholder ) {
        // $selected_keys_and_values must be an array of [ id => name ]

        $json_encoded = esc_attr( json_encode( $selected_keys_and_values ) );

        echo '<select id="'. esc_attr( $dom_id ) .'" class="wc-customer-search" name="'
        . esc_attr( $field_name ) . '[]" multiple="multiple" style="width: 50%;" data-placeholder="'
        . esc_attr( $placeholder ) . '" data-action="woocommerce_json_search_customers">';

        foreach ( $selected_keys_and_values as $key => $value ) {
            echo '<option value="' . esc_attr( $key ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $value ) . '</option>';
        }

        echo '</select>';
    }    

// ============================================
// Simple html output


    /**
     * Renders a <input type='text' />
     * $args should be an array in this form:
     *  [ 'type' => 'text', name' => 'field-name', 'id' => 'dom-id', 'value' => 'value', 'class' => 'css-class' ]
     * @param array $args 
     */
    public static function render_input( $args )
    {
        if ( ! isset( $args['type'] ) ) throw new Exception('Type field is obligatory.');

        $fields = array();
        switch ( $args['type'] ) {
            case 'text':
                $fields['type'] = 'text';
                if ( isset( $args['value'] ) ) $fields['value']  = esc_attr( $args['value'] );
                break;

            case 'hidden':
                $fields['type'] = 'hidden';
                if ( isset( $args['value'] ) ) $fields['value']  = esc_attr( $args['value'] );
                break;

            case 'radio':
                $fields['type'] = 'radio';
                if ( isset( $args['checked'] ) && $args['checked'] ) $fields['checked']  = esc_attr( $args['checked'] );
                if ( isset( $args['disabled'] ) && $args['disabled'] ) $fields['disabled'] = 'disabled';

                if ( isset( $args['value'] ) ) $fields['value']  = esc_attr( $args['value'] );
                break;

            case 'checkbox':
                $fields['type'] = 'checkbox';
                if ( isset( $args['disabled'] ) && $args['disabled'] ) $fields['disabled'] = 'disabled';

                $fields['value'] = isset( $args['cbvalue'] ) ? $args['cbvalue'] : 'yes';
                if ( isset( $args['value'] ) && (string) $args['value'] === (string) $fields['value'] ) {
                    $fields['checked']  = "checked";
                }

                if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
                    echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
                }                
                break;

            default:
                throw new Exception( sprintf( 'Unknown field type "%s" is obligatory.', $args['type'] ) );
        }

        //var_dump($args);
        if ( isset( $args['name'] ) ) $fields['name'] = esc_attr( $args['name'] );
        if ( isset( $args['id'] ) ) $fields['id']  = esc_attr( $args['id'] );
        if ( isset( $args['class'] ) ) $fields['class']  = esc_attr( $args['class'] );

        self::render_tag( 'input', $fields );
    } 

    /**
     * Renders a html tag:
     * e.g. <name field="value">contents</name>
     * 
     * This function does not escape the fields! Keys must be valid attribute names and values MUST be properly escaped!!!
     * 
     * @param string $name 
     * @param array $fields E.g. [ 'class' => 'clearfix' ] yields: class="clearfix"
     * @param string|null $contents If null the html tag will be self closing
     * @return void
     */
    public static function render_tag( $name, $fields = array(), $contents = null ) {
        echo self::tag( $name, $fields, $contents );
    }

    /**
     * Returns a html tag:
     * e.g. <name field="value">contents</name>
     * 
     * This function does not escape the fields! Keys must be valid attribute names and values MUST be properly escaped!!!
     * 
     * @param string $name 
     * @param array $fields E.g. [ 'class' => 'clearfix' ] yields: class="clearfix"
     * @param string|null $contents If null the html tag will be self closing
     * @return string
     */
    protected static function tag( $name, $fields = array(), $contents = null ) {
        if ( is_null( $contents ) ) {
            return sprintf( "<%s %s/>", $name, self::extract_attributes( $fields ) );
        } else {
            return sprintf( "<%s %s>%s</%s>", $name, self::extract_attributes( $fields ), $contents, $name );
        }
    }

    /**
     * Extracts fields to attributes for html tags.
     * E.g. [ 'class' => 'clearfix' ] yields: class="clearfix"
     * 
     * This function does not escape the fields! Keys must be valid attribute names and values MUST be properly escaped!!!
     * 
     * @param array $fields
     * @return string
     */
    protected static function extract_attributes( $fields ) {
        //NOTE: attrib and value MUST be properly escaped!!!
        $extracted = "";
        foreach( $fields as $field => $value ) {
            $extracted .= sprintf( '%s="%s" ', $field, $value );
        }
        return $extracted;
    }  

}