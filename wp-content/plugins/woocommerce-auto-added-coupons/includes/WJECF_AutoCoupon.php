<?php

defined('ABSPATH') or die();

class WJECF_AutoCoupon extends Abstract_WJECF_Plugin {

    protected $options = null; // WJECF_Options object
    private $_autocoupons = null;    
    private $_user_emails = null;    
    protected $_executed_coupon_by_url = false;
    
    public function __construct() {    
        $this->set_plugin_data( array(
            'description' => __( 'Allow coupons to be automatically applied to the cart when restrictions are met or by url.', 'woocommerce-jos-autocoupon' ),
            'dependencies' => array(),
            'can_be_disabled' => true
        ) );
    }
    
    public function init_hook() {
        if ( ! class_exists('WC_Coupon') ) {
            return;
        }

        //Frontend hooks - logic
        if ( WJECF_WC()->check_woocommerce_version('2.3.0')) {
            WJECF()->add_action_once( 'woocommerce_after_calculate_totals', array( $this, 'update_matched_autocoupons' ) ); 
        } else {
            //WC Versions prior to 2.3.0 don't have after_calculate_totals hook, this is a fallback
            WJECF()->add_action_once( 'woocommerce_cart_updated',  array( $this, 'update_matched_autocoupons' ) ); 
        }

        add_action( 'woocommerce_check_cart_items',  array( $this, 'remove_unmatched_autocoupons' ) , 0, 0 ); //Remove coupon before WC does it and shows a message
        //Last check for coupons with restricted_emails
        add_action( 'woocommerce_checkout_update_order_review', array( $this, 'fetch_billing_email' ), 10 ); // AJAX One page checkout 

        //Frontend hooks - visualisation
        add_filter('woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ), 10, 2 );
        add_filter('woocommerce_cart_totals_coupon_html', array( $this, 'coupon_html' ), 10, 2 );        

        //Inhibit redirect to cart when apply_coupon supplied
        add_filter('option_woocommerce_cart_redirect_after_add', array ( $this, 'option_woocommerce_cart_redirect_after_add') );

        if ( ! is_ajax() ) {
            //Get cart should not be called before the wp_loaded action nor the add_to_cart_action (class-wc-form-handler)
            add_action( 'wp_loaded', array( &$this, 'coupon_by_url' ), 90 ); //Coupon through url
        }

        /**
         * Mark removed autocoupons to prevent them from being automatically applied again
         * (PRO Only)
         * @since 2.5.4
         */
        add_action( 'woocommerce_applied_coupon', array( $this, 'action_applied_coupon' ), 10, 1 );
        add_action( 'woocommerce_removed_coupon', array( $this, 'action_removed_coupon' ), 10, 1 );
        add_action( 'woocommerce_cart_emptied', array( $this, 'action_cart_emptied' ), 10, 0 );
    }

/* ADMIN HOOKS */
    public function init_admin_hook() {
        add_action( 'wjecf_woocommerce_coupon_options_extended_features', array( $this, 'admin_coupon_options_extended_features' ), 20, 2 );

        //Inject columns
        if ( WJECF()->is_pro() ) {
            WJECF()->inject_coupon_column( 
                '_wjecf_auto_coupon', 
                __( 'Auto coupon', 'woocommerce-jos-autocoupon' ), 
                array( $this, 'admin_render_shop_coupon_columns' ), 'coupon_code'
            );    
            WJECF()->inject_coupon_column( 
                '_wjecf_individual_use', 
                __( 'Individual use', 'woocommerce-jos-autocoupon' ),
                array( $this, 'admin_render_shop_coupon_columns' ), 'coupon_code'
            );    
        }

        add_filter( 'views_edit-shop_coupon', array( $this, 'admin_views_edit_coupon' ) );
        add_filter( 'request', array( $this, 'admin_request_query' ) );
        
        add_action( 'wjecf_admin_before_settings', array( $this, 'wjecf_admin_before_settings' ), 20 );
        add_filter( 'wjecf_admin_validate_settings', array( $this, 'wjecf_admin_validate_settings' ), 10, 2 );
    }
    
    public function wjecf_admin_before_settings() {
        $page = WJECF_Admin_Settings::SETTINGS_PAGE;

        if ( WJECF()->is_pro() ) {
            add_settings_section(
                WJECF_Admin_Settings::DOM_PREFIX . 'section_autocoupon',
                __( 'Auto coupons', 'woocommerce-jos-autocoupon' ),
                array( $this, 'render_section' ),
                $page
            );
            
            add_settings_field(
                WJECF_Admin_Settings::DOM_PREFIX . 'autocoupon_allow_remove',
                __( 'Allow remove \'Auto Coupons\'', 'woocommerce-jos-autocoupon' ),
                array( $this, 'render_setting_allow_remove_auto_coupon' ),
                $page,
                WJECF_Admin_Settings::DOM_PREFIX . 'section_autocoupon'
            );
        }
    }
    
    public function render_section( $section ) {
        switch ( $section['id'] ) {
            case WJECF_Admin_Settings::DOM_PREFIX . 'section_autocoupon':
                //$body = ".....";
                //printf( '<p>%s</p>', $body );
                break;
        }
    }    
    
    public function render_setting_allow_remove_auto_coupon() {
        $option_name = 'autocoupon_allow_remove';
        $args = array( 
            'type' => 'checkbox',
            'id' => WJECF_Admin_Settings::DOM_PREFIX . $option_name,
            'name' => sprintf( "%s[%s]", WJECF_Admin_Settings::OPTION_NAME, $option_name ),
            'value' => WJECF()->get_option( $option_name, false )  ? 'yes' : 'no'
        );

        WJECF_Admin_Html::render_input( $args );
        WJECF_Admin_Html::render_tag( 
            'label', 
            array( "for" => esc_attr( $args['id'] ) ),
            __( 'Enabled', 'woocommerce' )
        );
        WJECF_Admin_Html::render_tag(
            'p',
            array( 'class' => 'description'),
            __( 'Check this box to allow the customer to remove \'Auto Coupons\' from the cart.', 'woocommerce-jos-autocoupon' )
        );

    }
    
    public function wjecf_admin_validate_settings( $options, $input ) {
        $options['autocoupon_allow_remove'] = isset( $input['autocoupon_allow_remove'] ) && $input['autocoupon_allow_remove'] === 'yes';
        return $options;
    }

    /**
     * Output a coupon custom column value
     *
     * @param string $column
     * @param WP_Post The coupon post object
     */
    public function admin_render_shop_coupon_columns( $column, $post ) {
        $wrap_coupon = WJECF_Wrap( intval( $post->ID ) );

        switch ( $column ) {
            case '_wjecf_auto_coupon' :
                $is_auto_coupon = $wrap_coupon->get_meta( '_wjecf_is_auto_coupon', true ) == 'yes';
                echo $is_auto_coupon ? __( 'Yes', 'woocommerce' ) : __( 'No', 'woocommerce' );
                if ( $is_auto_coupon ) {
                    $prio = $wrap_coupon->get_meta( '_wjecf_coupon_priority', true );
                    if ( $prio ) echo " (" . intval( $prio ) . ")";
                }
                break;
            case '_wjecf_individual_use' :
                $individual = $wrap_coupon->get_individual_use();
                echo $individual ? __( 'Yes', 'woocommerce' ) : __( 'No', 'woocommerce' );
                break;
        }
    }

    public function admin_views_edit_coupon( $views ) {
        global $post_type, $wp_query;

        $class            = ( isset( $wp_query->query['meta_key'] ) && $wp_query->query['meta_key'] == '_wjecf_is_auto_coupon' ) ? 'current' : '';
        $query_string     = remove_query_arg(array( 'wjecf_is_auto_coupon' ));
        $query_string     = add_query_arg( 'wjecf_is_auto_coupon', '1', $query_string );
        $views['wjecf_is_auto_coupon'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . __( 'Auto coupons', 'woocommerce-jos-autocoupon' ) . '</a>';

        return $views;
    }

    /**
     * Filters and sorting handler
     *
     * @param  array $vars
     * @return array
     */
    public function admin_request_query( $vars ) {
        global $typenow, $wp_query, $wp_post_statuses;

        if ( 'shop_coupon' === $typenow ) {
            if ( isset( $_GET['wjecf_is_auto_coupon'] ) ) {
                $vars['meta_key']   = '_wjecf_is_auto_coupon';
                $vars['meta_value'] = $_GET['wjecf_is_auto_coupon'] == '1' ? 'yes' : 'no';
            }
        }

        return $vars;
    }

    public function admin_coupon_options_extended_features( $thepostid, $post ) {
        
        //=============================
        //Title
        echo "<h3>" . esc_html( __( 'Auto coupon', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";

        
        //=============================
        // Auto coupon checkbox
        woocommerce_wp_checkbox( array(
            'id'          => '_wjecf_is_auto_coupon',
            'label'       => __( 'Auto coupon', 'woocommerce-jos-autocoupon' ),
            'description' => __( "Automatically add the coupon to the cart if the restrictions are met. Please enter a description when you check this box, the description will be shown in the customer's cart if the coupon is applied.", 'woocommerce-jos-autocoupon' )
        ) );

        echo '<div class="_wjecf_show_if_autocoupon">';
        if ( WJECF()->is_pro() ) {            
            // Maximum quantity of matching products (product/category)
            woocommerce_wp_text_input( array( 
                'id' => '_wjecf_coupon_priority', 
                'label' => __( 'Priority', 'woocommerce-jos-autocoupon' ), 
                'placeholder' => __( 'No priority', 'woocommerce' ), 
                'description' => __( 'When \'individual use\' is checked, auto coupons with a higher value will have priority over other auto coupons.', 'woocommerce-jos-autocoupon' ), 
                'data_type' => 'decimal', 
                'desc_tip' => true
            ) );    
        }    

        //=============================
        // Apply without notice
        woocommerce_wp_checkbox( array(
            'id'          => '_wjecf_apply_silently',
            'label'       => __( 'Apply silently', 'woocommerce-jos-autocoupon' ),
            'description' => __( "Don't display a message when this coupon is automatically applied.", 'woocommerce-jos-autocoupon' ),
        ) );
        echo '</div>';
        
        ?>        
        <script type="text/javascript">
            //Hide/show when AUTO-COUPON value changes
            function update_wjecf_apply_silently_field( animation ) { 
                    if ( animation === undefined ) animation = 'slow';
                    
                    if (jQuery("#_wjecf_is_auto_coupon").prop('checked')) {
                        jQuery("._wjecf_show_if_autocoupon").show( animation );
                        jQuery("._wjecf_hide_if_autocoupon").hide( animation );
                    } else {
                        jQuery("._wjecf_show_if_autocoupon").hide( animation );
                        jQuery("._wjecf_hide_if_autocoupon").show( animation );
                    }
            }

            jQuery( function( $ ) {
                update_wjecf_apply_silently_field( 0 );
                $("#_wjecf_is_auto_coupon").click( update_wjecf_apply_silently_field );
            } );
        </script>
        <?php
        
    }

    public function admin_coupon_meta_fields( $coupon ) {
        $fields = array(
            '_wjecf_is_auto_coupon' => 'yesno',
            '_wjecf_apply_silently' => 'yesno',
        );

        if ( WJECF()->is_pro() ) {
            $fields['_wjecf_coupon_priority'] = 'int';
        }
        return $fields;
    }

/* FRONTEND HOOKS */

    /**
     ** Inhibit redirect to cart when apply_coupon supplied
     */
    public function option_woocommerce_cart_redirect_after_add ( $value ) {
        if ( ! $this->_executed_coupon_by_url  && isset( $_GET['apply_coupon'] ) ) {
            $value = 'no';
        }
        return $value;
    }

    /**
     * Add coupon through url
     */
    public function coupon_by_url() {
        $must_redirect = false;

        //Apply coupon by url
        if ( isset( $_GET['apply_coupon'] ) ) {
            $must_redirect = true;
            $this->_executed_coupon_by_url = true;
            $split = explode( ",", wc_clean( $_GET['apply_coupon'] ) );
            //2.2.2 Make sure a session cookie is set
            if( ! WC()->session->has_session() )
            {
                WC()->session->set_customer_session_cookie( true );
            }

            $cart = WC()->cart;
            foreach ( $split as $coupon_code ) {
                $coupon = WJECF_WC()->get_coupon( $coupon_code );
                if ( WJECF_WC()->check_woocommerce_version('2.3.0') && ! WJECF_Wrap( $coupon )->exists() ) {
                    wc_add_notice( $coupon->get_coupon_error( WC_Coupon::E_WC_COUPON_NOT_EXIST ), 'error' );
                } else {
                    $valid = $coupon->is_valid();
                    if ( $valid ) {
                        $cart->add_discount( $coupon_code );
                    }
                }
            }
        }

        //Redirect to page without autocoupon query args
        if ( $must_redirect ) {
            $requested_url  = is_ssl() ? 'https://' : 'http://';
            $requested_url .= $_SERVER['HTTP_HOST'];           
            $requested_url .= $_SERVER['REQUEST_URI'];

            wp_safe_redirect( remove_query_arg( array( 'apply_coupon', 'add-to-cart' ), ( $requested_url ) ) );
            exit;
        }
    }
    
    /**
     * Overwrite the html created by wc_cart_totals_coupon_label() so a descriptive text will be shown for the discount.
     * @param  string $originaltext The default text created by wc_cart_totals_coupon_label()
     * @param  WC_Coupon $coupon The coupon data
     * @return string The overwritten text
    */    
    function coupon_label( $originaltext, $coupon ) {
        
        if ( $this->is_auto_coupon($coupon) ) {            
            return WJECF_Wrap( $coupon )->get_description(); 
        } else {
            return $originaltext;
        }
    }
    
    /**
     * Overwrite the html created by wc_cart_totals_coupon_html(). This function is required to remove the "Remove" link.
     * @param  string $originaltext The html created by wc_cart_totals_coupon_html()
     * @param  WC_Coupon $coupon The coupon data
     * @return string The overwritten html
     */
    function coupon_html( $originaltext, $coupon ) {
        if ( $this->is_auto_coupon( $coupon ) && ! $this->get_option_autocoupon_allow_remove() ) {
                $value  = array();

                if ( $amount = WC()->cart->get_coupon_discount_amount( WJECF_Wrap( $coupon )->get_code(), WC()->cart->display_cart_ex_tax ) ) {
                    $discount_html = '-' . wc_price( $amount );
                } else {
                    $discount_html = '';
                }

                $value[] = apply_filters( 'woocommerce_coupon_discount_amount_html', $discount_html, $coupon );

                if ( WJECF_Wrap( $coupon )->get_free_shipping() ) {
                    $value[] = __( 'Free shipping coupon', 'woocommerce' );
                }

                return implode( ', ', array_filter($value) ); //Remove empty array elements
        } else {
            return $originaltext;
        }
    }

    function remove_unmatched_autocoupons( $valid_coupon_codes = null ) {
        if ( $valid_coupon_codes === null ) {
            //Get the coupons that should be in the cart
            $valid_coupons = $this->get_valid_auto_coupons();
            $valid_coupons = $this->individual_use_filter( $valid_coupons );
            $valid_coupon_codes = array();    
            foreach ( $valid_coupons as $coupon ) {
                $valid_coupon_codes[] = WJECF_Wrap( $coupon )->get_code();
            }
        }

        //Remove invalids
        $calc_needed = false;    
        foreach ( $this->get_all_auto_coupons() as $coupon ) {
            $coupon_code = WJECF_Wrap( $coupon )->get_code();
            if ( WC()->cart->has_discount( $coupon_code ) && ! in_array( $coupon_code, $valid_coupon_codes ) ) {
                $this->log( 'debug', sprintf( "Removing %s", $coupon_code ) );
                $this->ignore_removal[$coupon_code] = $coupon_code;
                WC()->cart->remove_coupon( $coupon_code );  
                unset( $this->ignore_removal[$coupon_code] );
                $calc_needed = true;
            }
        }
        return $calc_needed;
    }


// ============ BEGIN wjecf_autocoupon_removed_coupons =======================

    // When a custom clicks the [remove]-button of an auto coupon, the coupon will not be applied automatically anymore...
    // After that the coupon can only be applied manually by entering the coupon code.

    //Will ignore the remove_coupon action for the given keys
    private $ignore_removal = array( /* coupon_code => coupon_code */ );

    /**
     * Remove the coupon from session 'wjecf_autocoupon_removed_coupons'
     * @since 2.5.4
     */
    function action_applied_coupon( $coupon_code ) {
        if ( ! $this->is_auto_coupon( $coupon_code ) || ! $this->get_option_autocoupon_allow_remove() ) {
            return;
        }

        $removed_autocoupon_codes = $this->get_removed_autocoupon_codes();
        if ( ! isset( $removed_autocoupon_codes[$coupon_code] ) ) {
            return;
        }

        unset( $removed_autocoupon_codes[$coupon_code] ) ;
        $this->set_removed_autocoupon_codes( $removed_autocoupon_codes );
    }

    /**
     * Add the coupon to session 'wjecf_autocoupon_removed_coupons'
     * 
     * @since 2.5.4
     */
    function action_removed_coupon( $coupon_code ) {
        if ( ! $this->is_auto_coupon( $coupon_code ) || ! $this->get_option_autocoupon_allow_remove() ) {
            return;
        }
        //Ignore, because the auto-coupon was removed automatically (not manually by the customer)
        if ( isset( $this->ignore_removal[$coupon_code] ) ) {
            return;
        }

        $removed_autocoupon_codes = $this->get_removed_autocoupon_codes();
        $removed_autocoupon_codes[$coupon_code] = $coupon_code;
        $this->set_removed_autocoupon_codes( $removed_autocoupon_codes );
    }

    /**
     * Remove 'wjecf_autocoupon_removed_coupons' from the session when cart is emptied
     * 
     * @since 2.5.4
     */
    function action_cart_emptied() {
        $this->set_removed_autocoupon_codes( null );
    }

    /**
     * Get the removed auto coupon-codes from the session
     * 
     * @since 2.5.4
     * @return array The queued coupon codes
     */
    private function get_removed_autocoupon_codes( ) {
        $coupon_codes = WC()->session->get( 'wjecf_autocoupon_removed_coupons' , array() );        
        return $coupon_codes;
    }

    /**
     * Save the removed auto coupon-codes in the session
     * 
     * @since 2.5.4
     * @param array $coupon_codes 
     * @return void
     */
    private function set_removed_autocoupon_codes( $coupon_codes ) {
        WC()->session->set( 'wjecf_autocoupon_removed_coupons' , $coupon_codes );
    }

    /**
     * Reads the option 'autocoupon_allow_remove'
     * 
     * @since 2.5.4
     * @return bool
     */
    private function get_option_autocoupon_allow_remove() {
        return WJECF()->is_pro() && WJECF()->get_option( 'autocoupon_allow_remove', false );
    }

// ============ END wjecf_autocoupon_removed_coupons =======================


    /**
     * Apply matched autocoupons and remove unmatched autocoupons.
     * @return void
     */    
    function update_matched_autocoupons() {

        $this->log( 'debug', "()" );

        //2.3.3 Keep track of queued coupons and apply when they validate
        $queuer = WJECF()->get_plugin('WJECF_Pro_Coupon_Queueing');
        if ( $queuer !== false ) {
            $queuer->apply_valid_queued_coupons();
        }

        //Get the coupons that should be in the cart
        $valid_coupons = $this->get_valid_auto_coupons();
        $valid_coupons = $this->individual_use_filter( $valid_coupons );

        $valid_coupon_codes = array();    
        foreach ( $valid_coupons as $coupon ) {
            $valid_coupon_codes[] = WJECF_Wrap( $coupon )->get_code();
        }

        $this->log( 'debug', sprintf( "Auto coupons that should be in cart: %s", implode( ', ', $valid_coupon_codes ) ) );

        $calc_needed = $this->remove_unmatched_autocoupons( $valid_coupon_codes );

        //Add valids
        foreach( $valid_coupons as $coupon ) {
            $coupon_code = WJECF_Wrap( $coupon )->get_code();
            if ( ! WC()->cart->has_discount( $coupon_code )  ) {
                $this->log( 'debug', sprintf( "Applying auto coupon %s", $coupon_code ) );

                $apply_silently = WJECF_Wrap( $coupon )->get_meta( '_wjecf_apply_silently' ) == 'yes';
                
                if ( $apply_silently ) {
                    $new_succss_msg = ''; // no message
                } else {
                    $coupon_excerpt = WJECF_Wrap( $coupon )->get_description();
                    $new_succss_msg = sprintf(
                        __("Discount applied: %s", 'woocommerce-jos-autocoupon'), 
                        __( empty( $coupon_excerpt ) ? $coupon_code : $coupon_excerpt, 'woocommerce-jos-autocoupon')
                    );
                }

                WJECF()->start_overwrite_success_message( $coupon, $new_succss_msg );
                WC()->cart->add_discount( $coupon_code ); //Causes calculation and will remove other coupons if it's a individual coupon
                WJECF()->stop_overwrite_success_message();

                $calc_needed = false; //Already done by adding the discount

            }
        }

        $this->log( 'debug', 'Coupons in cart: ' . implode( ', ', WC()->cart->applied_coupons ) . ($calc_needed ? ". RECALC" : "") );

        if ( $calc_needed ) {
            WC()->cart->calculate_totals();
        }
        
    }

    private function get_valid_auto_coupons( ) {
        $valid_coupons = array();

        //Array will only have values if option autocoupon_allow_remove == true
        $removed_autocoupon_codes = $this->get_option_autocoupon_allow_remove() ? $this->get_removed_autocoupon_codes() : array();

        foreach ( $this->get_all_auto_coupons() as $coupon_code => $coupon ) {
            if ( isset( $removed_autocoupon_codes[ $coupon_code ] ) ) {
                continue;
            }
            if ( $this->coupon_can_be_applied( $coupon ) && $this->coupon_has_a_value( $coupon ) ) {
                $valid_coupons[] = $coupon;
            }
        }
        return $valid_coupons;
    }    

/**
 * Test whether the coupon is valid and has a discount > 0 
 * @return bool
 */
    function coupon_can_be_applied( $coupon ) {
        $wrap_coupon = WJECF_Wrap( $coupon );
        $can_be_applied = true;
        
        //Test validity
        if ( ! $coupon->is_valid() ) {
            $can_be_applied = false;
        }
        //Test restricted emails
        //See WooCommerce: class-wc-cart.php function check_customer_coupons
        else if ( $can_be_applied && is_array( $wrap_coupon->get_email_restrictions() ) && sizeof( $wrap_coupon->get_email_restrictions() ) > 0 ) {
            $user_emails = array_map( 'sanitize_email', array_map( 'strtolower', $this->get_user_emails() ) );
            $coupon_emails = array_map( 'sanitize_email', array_map( 'strtolower', $wrap_coupon->get_email_restrictions() ) );
            
            if ( 0 == sizeof( array_intersect( $user_emails, $coupon_emails ) ) ) {
                $can_be_applied = false;
            }
        }
        return apply_filters( 'wjecf_coupon_can_be_applied', $can_be_applied, $coupon );
        
    }

    /**
     * Does the coupon have a value? (autocoupon should not be applied if it has no value)
     * @param  WC_Coupon $coupon The coupon data
     * @return bool True if it has a value (discount, free shipping, whatever) otherwise false)
     **/
    function coupon_has_a_value( $coupon ) {
        
        $has_a_value = false;
        
        if ( WJECF_Wrap( $coupon )->get_free_shipping() ) {
            $has_a_value = true;
        } else {
            //Test whether discount > 0
            //See WooCommerce: class-wc-cart.php function get_discounted_price
            global $woocommerce;
            foreach ( $woocommerce->cart->get_cart() as $cart_item ) {
                if  ( $coupon->is_valid_for_cart() || $coupon->is_valid_for_product( $cart_item['data'], $cart_item ) ) {
                    $has_a_value = WJECF_Wrap( $coupon )->get_amount() > 0;
                    break;
                }
            }
        }
        
        return apply_filters( 'wjecf_coupon_has_a_value', $has_a_value, $coupon );
        
    }
    
    

/**
 * Check wether the coupon is an "Auto coupon".
 * @param  WC_Coupon $coupon The coupon data
 * @return bool true if it is an "Auto coupon"
 */    
    public function is_auto_coupon( $coupon ) {
        return WJECF_Wrap( $coupon )->get_meta( '_wjecf_is_auto_coupon' ) == 'yes';
    }

    private function get_coupon_priority($coupon) {
        if ( WJECF()->is_pro() ) {            
            $prio = WJECF_Wrap( $coupon )->get_meta( '_wjecf_coupon_priority' );
            if ( ! empty( $prio ) ) {
                return intval( $prio );
            }
        }
        return 0;
    }    
    


/**
 * Get a list of the users' known email addresses
 *
 */
    private function get_user_emails() {
        if ( ! is_array($this->_user_emails) ) {
            $this->_user_emails = array();
            //Email of the logged in user
            if ( is_user_logged_in() ) {
                $current_user   = wp_get_current_user();
                $this->_user_emails[] = $current_user->user_email;
            }
            
            if ( isset( $_POST['billing_email'] ) )
                $this->_user_emails[] = $_POST['billing_email'];
        }
        //$this->log( 'debug', "User emails: " . implode( ",", $this->_user_emails ) );
        return $this->_user_emails;        
    }

/**
 * Append a single or an array of email addresses.
 * @param  array|string $append_emails The email address(es) to be added
 * @return void
 */
    private function append_user_emails($append_emails) {
        //$append_emails must be an array
        if ( ! is_array( $append_emails ) ) {
            $append_emails = array( $append_emails );
        }
        $this->_user_emails = array_unique( array_merge( $this->get_user_emails(), $append_emails ) );
        //$this->log( 'debug','Append emails: ' . implode( ',', $append_emails ) );
    }
    
    public function fetch_billing_email( $post_data ) {
        //post_data can be an array, or a query=string&like=this
        if ( ! is_array( $post_data ) ) {
            parse_str( $post_data, $posted );
        } else {
            $posted = $post_data;
        }
        
        if ( isset ( $posted['billing_email'] ) ) {
            $this->append_user_emails( $posted['billing_email'] );
        }
        
    }

    /**
     * Return an array of WC_Coupons with coupons that shouldn't cause individual use conflicts
     */
    private function individual_use_filter( $valid_auto_coupons ) {
        $filtered = array();

        //Any individual use non-autocoupons in the cart?
        foreach ( WC()->cart->get_applied_coupons() as $coupon_code ) {
            $coupon = new WC_Coupon( $coupon_code );
            if ( WJECF_Wrap( $coupon )->get_individual_use() && ! $this->is_auto_coupon( $coupon ) ) {
                return $filtered; //Don't allow any auto coupon
            }
        }
        foreach ( $valid_auto_coupons as $coupon ) {
            if ( ! WJECF_Wrap( $coupon )->get_individual_use() || empty( $filtered ) ) {
                $filtered[] = $coupon;
                if ( WJECF_Wrap( $coupon )->get_individual_use() ) {
                    break;
                }
            }
        }
        return $filtered;
    }    
    
/**
 * Get an array of all auto coupons [ $coupon_code => $coupon ]
 * @return array All auto coupon codes
 */        
    public function get_all_auto_coupons() {
        if ( ! is_array( $this->_autocoupons ) ) {
            $this->_autocoupons = array();
            
            $query_args = array(
                'posts_per_page' => -1,            
                'post_type'   => 'shop_coupon',
                'post_status' => 'publish',
                'orderby' => array( 'title' => 'ASC' ),
                'meta_query' => array(
                    array(
                        'key' => '_wjecf_is_auto_coupon',
                        'compare' => '=',
                        'value' => 'yes',
                    ),
                )
            );

            $query = new WP_Query($query_args);
            foreach ($query->posts as $post) {
                $coupon = new WC_Coupon($post->post_title);
                if ( $this->is_auto_coupon($coupon) ) {
                    $this->_autocoupons[ WJECF_Wrap( $coupon )->get_code() ] = $coupon;
                }
            }

            //Sort by priority
            @uasort( $this->_autocoupons , array( $this, 'sort_auto_coupons' ) ); //Ignore error PHP Bug #50688

            $coupon_codes = array();
            foreach( $this->_autocoupons as $coupon ) {
                $coupon_codes[] = WJECF_Wrap( $coupon )->get_code();
            }

            $this->log( 'debug', "Autocoupons: " . implode(", ", $coupon_codes ) );
        }

        return $this->_autocoupons;
    }

    /**
     * Compare function to sort coupons by priority
     * @param type $a 
     * @param type $b 
     * @return type
     */
    private function sort_auto_coupons( $coupon_a, $coupon_b ) {
        $prio_a = $this->get_coupon_priority( $coupon_a );
        $prio_b = $this->get_coupon_priority( $coupon_b );
        //$this->log( 'debug', "A: $prio_a B: $prio_b " );
        if ( $prio_a == $prio_b ) {
            return WJECF_Wrap( $coupon_a )->get_code() < WJECF_Wrap( $coupon_b )->get_code() ? -1 : 1; //By title ASC
        } else {
            return $prio_a > $prio_b ? -1 : 1; //By prio DESC
        }
    }
}