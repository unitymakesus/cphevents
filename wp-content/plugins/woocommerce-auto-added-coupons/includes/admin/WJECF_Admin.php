<?php

defined('ABSPATH') or die();

if ( ! class_exists('WJECF_Admin') ) {

    class WJECF_Admin extends Abstract_WJECF_Plugin {
        
        public function __construct() {    
            $this->set_plugin_data( array(
                'description' => __( 'Admin interface of WooCommerce Extended Coupon Features.', 'woocommerce-jos-autocoupon' ),
                'dependencies' => array(),
                'can_be_disabled' => false
            ) );
        }

        public function init_admin_hook() {
            add_action( 'admin_notices', array( $this, 'admin_notices'));

            if ( ! WJECF_WC()->check_woocommerce_version('2.3.0') ) {
                $this->enqueue_notice( '<p>' . 
                    __( '<strong>WooCommerce Extended Coupon Features:</strong> You are using an old version of WooCommerce. Updating of WooCommerce is recommended as using an outdated version might cause unexpected behaviour in combination with modern plugins.' ) 
                    . '</p>', 'notice-warning' );
            }
            //Admin hooks
            add_filter( 'plugin_row_meta', array( $this, 'wjecf_plugin_meta' ), 10, 2 );
            add_action( 'admin_head', array( $this, 'on_admin_head'));

            add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'admin_coupon_options_tabs' ), 20, 1);
            add_action( 'woocommerce_coupon_data_panels', array( $this, 'admin_coupon_options_panels' ), 10, 0 );
            add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );        
            
            //Removed in 2.5.0: add_action( 'wjecf_coupon_metabox_products', array( $this, 'admin_coupon_metabox_products' ), 10, 2 );
            //Moved to here:
            add_action('woocommerce_coupon_options_usage_restriction', array( $this, 'on_woocommerce_coupon_options_usage_restriction' ), 20, 1);

            add_action( 'wjecf_coupon_metabox_checkout', array( $this, 'admin_coupon_metabox_checkout' ), 10, 2 );
            add_action( 'wjecf_coupon_metabox_customer', array( $this, 'admin_coupon_metabox_customer' ), 10, 2 );
            //add_action( 'wjecf_coupon_metabox_misc', array( $this, 'admin_coupon_metabox_misc' ), 10, 2 );

            $this->add_inline_style( '
                #woocommerce-coupon-data .wjecf-not-wide { width:50% }
            ');        
        }    

    // ===========================================================================
    // START - ADMIN NOTICES
    // Allows notices to be displayed on the admin pages
    // ===========================================================================

        private $notices = array();
        
        /**
         * Enqueue a notice to display on the admin page
         * @param stirng $html Please embed in <p> tags
         * @param string $class 
         */
        public function enqueue_notice( $html, $class = 'notice-info' ) {
            $this->notices[] = array( 'class' => $class, 'html' => $html );
        }

        public function admin_notices() {
            foreach( $this->notices as $notice ) {
                echo '<div class="notice ' . $notice['class'] . '">';
                echo $notice['html'];
                echo '</div>';
            }
            $this->notices = array();
        }    

    // ===========================================================================
    // END - ADMIN NOTICES
    // ===========================================================================

        //2.3.6 Inline css
        private $admin_css = '';

        /**
         * 2.3.6
         * @return void
         */
        function on_admin_head() {
             //Output inline style for the admin pages
            if ( ! empty( $this->admin_css ) ) {
                echo '<style type="text/css">' . $this->admin_css . '</style>';
                $this->admin_css = '';
            }

            //Enqueue scripts
            wp_enqueue_script( "wjecf-admin", WJECF()->plugin_url() . "assets/js/wjecf-admin.js", array( 'jquery' ), WJECF()->plugin_version() );
            wp_localize_script( 'wjecf-admin', 'wjecf_admin_i18n', array(
                'label_and' => __( '(AND)', 'woocommerce-jos-autocoupon' ),
                'label_or' => __( '(OR)',  'woocommerce-jos-autocoupon' )
            ) );

        }

        //Add tabs to the coupon option page
        public function admin_coupon_options_tabs( $tabs ) {
            
            $tabs['extended_features_checkout'] = array(
                'label'  => __( 'Checkout', 'woocommerce-jos-autocoupon' ),
                'target' => 'wjecf_coupondata_checkout',
                'class'  => 'wjecf_coupondata_checkout',
            );

            $tabs['extended_features_misc'] = array(
                'label'  => __( 'Miscellaneous', 'woocommerce-jos-autocoupon' ),
                'target' => 'wjecf_coupondata_misc',
                'class'  => 'wjecf_coupondata_misc',
            );

            return $tabs;
        }    

        //Add panels to the coupon option page
        public function admin_coupon_options_panels() {
            global $thepostid, $post;
            $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
            ?>
                <div id="wjecf_coupondata_checkout" class="panel woocommerce_options_panel">
                    <?php
                        do_action( 'wjecf_coupon_metabox_checkout', $thepostid, $post );
                        do_action( 'wjecf_coupon_metabox_customer', $thepostid, $post );
                        $this->admin_coupon_data_footer();
                    ?>
                </div>
                <div id="wjecf_coupondata_misc" class="panel woocommerce_options_panel">
                    <?php
                        //Allow other classes to inject options
                        do_action( 'wjecf_woocommerce_coupon_options_extended_features', $thepostid, $post );
                        do_action( 'wjecf_coupon_metabox_misc', $thepostid, $post );
                        $this->admin_coupon_data_footer();
                    ?>
                </div>
            <?php        
        }

        public function admin_coupon_data_footer() {
            $documentation_url = WJECF()->plugin_url( 'docs/index.html' );
            if ( ! WJECF()->is_pro() ) {
                $documentation_url = 'http://www.soft79.nl/documentation/wjecf';
                ?>            
                <h3><?php _e( 'Do you find WooCommerce Extended Coupon Features useful?', 'woocommerce-jos-autocoupon'); ?></h3>
                <p class="form-field"><label for="wjecf_donate_button"><?php
                    echo esc_html( __('Express your gratitude', 'woocommerce-jos-autocoupon' ) );    
                ?></label>
                <a id="wjecf_donate_button" href="<?php echo $this->get_donate_url(); ?>" target="_blank" class="button button-primary">
                <?php
                    echo esc_html( __('Donate to the developer', 'woocommerce-jos-autocoupon' ) );    
                ?></a><br>
                Or get the PRO version at <a href="http://www.soft79.nl" target="_blank">www.soft79.nl</a>.
                </p>
                <?php
            }
            //Documentation link
            echo '<h3>' . __( 'Documentation', 'woocommerce-jos-autocoupon' ) . '</h3>';
            echo '<p><a href="' . $documentation_url . '" target="_blank">' . 
                 __( 'WooCommerce Extended Coupon Features Documentation', 'woocommerce-jos-autocoupon' ) . '</a></p>';

        }

        // //Tab 'extended features'
        //public function admin_coupon_metabox_products( $thepostid, $post ) {

        //since 2.5.0 moved to the 'Usage restriction' tab
        public function on_woocommerce_coupon_options_usage_restriction() {
            global $thepostid, $post;
            $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

            //See WooCommerce class-wc-meta-box-coupon-data.php function ouput
            
            echo "<h3>" . esc_html( __( 'Matching products', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
            //=============================
            // AND instead of OR the products
            WJECF_Admin_Html::render_select_with_default( array(
                'id' => '_wjecf_products_and', 
                'label' => __( 'Products Operator', 'woocommerce-jos-autocoupon' ), 
                'options' => array( 'no' => __( 'OR', 'woocommerce-jos-autocoupon' ), 'yes' => __( 'AND', 'woocommerce-jos-autocoupon' ) ),
                'default_value' => 'no',
                'class' => 'wjecf-not-wide',
                /* translators: OLD TEXT:  'Check this box if ALL of the products (see tab \'usage restriction\') must be in the cart to use this coupon (instead of only one of the products).' */
                'description' => __( 'Use AND if ALL of the products must be in the cart to use this coupon (instead of only one of the products).', 'woocommerce-jos-autocoupon' ),
                'desc_tip' => true
            ) );        

            //=============================
            // 2.2.3.1 AND instead of OR the categories
            WJECF_Admin_Html::render_select_with_default( array(
                'id' => '_wjecf_categories_and', 
                'label' => __( 'Categories Operator', 'woocommerce-jos-autocoupon' ), 
                'options' => array( 'no' => __( 'OR', 'woocommerce-jos-autocoupon' ), 'yes' => __( 'AND', 'woocommerce-jos-autocoupon' ) ),
                'default_value' => 'no',
                'class' => 'wjecf-not-wide',
                /* translators: OLD TEXT:  'Check this box if products from ALL of the categories (see tab \'usage restriction\') must be in the cart to use this coupon (instead of only one from one of the categories).' */
                'description' => __( 'Use AND if products from ALL of the categories must be in the cart to use this coupon (instead of only one from one of the categories).', 'woocommerce-jos-autocoupon' ),
                'desc_tip' => true
            ) );        



            // Minimum quantity of matching products (product/category)
            woocommerce_wp_text_input( array( 
                'id' => '_wjecf_min_matching_product_qty', 
                'label' => __( 'Minimum quantity of matching products', 'woocommerce-jos-autocoupon' ), 
                'placeholder' => __( 'No minimum', 'woocommerce' ), 
                'description' => __( 'Minimum quantity of the products that match the given product or category restrictions (see tab \'usage restriction\'). If no product or category restrictions are specified, the total number of products is used.', 'woocommerce-jos-autocoupon' ), 
                'data_type' => 'decimal', 
                'desc_tip' => true
            ) );
            
            // Maximum quantity of matching products (product/category)
            woocommerce_wp_text_input( array( 
                'id' => '_wjecf_max_matching_product_qty', 
                'label' => __( 'Maximum quantity of matching products', 'woocommerce-jos-autocoupon' ), 
                'placeholder' => __( 'No maximum', 'woocommerce' ), 
                'description' => __( 'Maximum quantity of the products that match the given product or category restrictions (see tab \'usage restriction\'). If no product or category restrictions are specified, the total number of products is used.', 'woocommerce-jos-autocoupon' ), 
                'data_type' => 'decimal', 
                'desc_tip' => true
            ) );        

            // Minimum subtotal of matching products (product/category)
            woocommerce_wp_text_input( array( 
                'id' => '_wjecf_min_matching_product_subtotal', 
                'label' => __( 'Minimum subtotal of matching products', 'woocommerce-jos-autocoupon' ), 
                'placeholder' => __( 'No minimum', 'woocommerce' ), 
                'description' => __( 'Minimum price subtotal of the products that match the given product or category restrictions (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ), 
                'data_type' => 'price', 
                'desc_tip' => true
            ) );

            // Maximum subtotal of matching products (product/category)
            woocommerce_wp_text_input( array( 
                'id' => '_wjecf_max_matching_product_subtotal', 
                'label' => __( 'Maximum subtotal of matching products', 'woocommerce-jos-autocoupon' ), 
                'placeholder' => __( 'No maximum', 'woocommerce' ), 
                'description' => __( 'Maximum price subtotal of the products that match the given product or category restrictions (see tab \'usage restriction\').', 'woocommerce-jos-autocoupon' ), 
                'data_type' => 'price', 
                'desc_tip' => true
            ) );
        }

        public function admin_coupon_metabox_checkout( $thepostid, $post ) {

            echo "<h3>" . esc_html( __( 'Checkout', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";

            //=============================
            // Shipping methods
            ?>
            <p class="form-field"><label for="wjecf_shipping_methods"><?php _e( 'Shipping methods', 'woocommerce-jos-autocoupon' ); ?></label>
            <select id="wjecf_shipping_methods" name="_wjecf_shipping_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any shipping method', 'woocommerce-jos-autocoupon' ); ?>">
                <?php
                    $coupon_shipping_method_ids = WJECF()->get_coupon_shipping_method_ids( $thepostid );
                    $shipping_methods = WC()->shipping->load_shipping_methods();

                    if ( $shipping_methods ) foreach ( $shipping_methods as $shipping_method ) {
                        echo '<option value="' . esc_attr( $shipping_method->id ) . '"' . selected( in_array( $shipping_method->id, $coupon_shipping_method_ids ), true, false ) . '>' . esc_html( $shipping_method->method_title ) . '</option>';
                    }
                ?>
            </select><?php echo WJECF_Admin_Html::wc_help_tip( __( 'One of these shipping methods must be selected in order for this coupon to be valid.', 'woocommerce-jos-autocoupon' ) ); ?>
            </p>
            <?php        
            
            //=============================
            // Payment methods
            ?>
            <p class="form-field"><label for="wjecf_payment_methods"><?php _e( 'Payment methods', 'woocommerce-jos-autocoupon' ); ?></label>
            <select id="wjecf_payment_methods" name="_wjecf_payment_methods[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any payment method', 'woocommerce-jos-autocoupon' ); ?>">
                <?php
                    $coupon_payment_method_ids = WJECF()->get_coupon_payment_method_ids( $thepostid );
                    //DONT USE WC()->payment_gateways->available_payment_gateways() AS IT CAN CRASH IN UNKNOWN OCCASIONS
                    $payment_methods = WC()->payment_gateways->payment_gateways();
                    if ( $payment_methods ) foreach ( $payment_methods as $payment_method ) {
                        if ('yes' === $payment_method->enabled) {
                            echo '<option value="' . esc_attr( $payment_method->id ) . '"' . selected( in_array( $payment_method->id, $coupon_payment_method_ids ), true, false ) . '>' . esc_html( $payment_method->title ) . '</option>';
                        }
                    }
                ?>
            </select><?php echo WJECF_Admin_Html::wc_help_tip( __( 'One of these payment methods must be selected in order for this coupon to be valid.', 'woocommerce-jos-autocoupon' ) ); ?>
            </p>
            <?php        
        }

        public function admin_coupon_metabox_customer( $thepostid, $post ) {

            //=============================
            //Title: "CUSTOMER RESTRICTIONS"
            echo "<h3>" . esc_html( __( 'Customer restrictions', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
            echo "<p><span class='description'>" . __( 'If both a customer and a role restriction are supplied, matching either one of them will suffice.' , 'woocommerce-jos-autocoupon' ) . "</span></p>\n";
            
            //=============================
            // User ids
            ?>
            <p class="form-field"><label><?php _e( 'Allowed Customers', 'woocommerce-jos-autocoupon' ); ?></label>
            <?php
                $coupon_customer_ids = WJECF()->get_coupon_customer_ids( $thepostid );
                WJECF_Admin_Html::render_admin_customer_selector( 'wjecf_customer_ids', '_wjecf_customer_ids', $coupon_customer_ids );
                echo WJECF_Admin_Html::wc_help_tip( __( 'Only these customers may use this coupon.', 'woocommerce-jos-autocoupon' ) );
            ?>
            </p>
            <?php

            //=============================
            // User roles
            ?>
            <p class="form-field"><label for="wjecf_customer_roles"><?php _e( 'Allowed User Roles', 'woocommerce-jos-autocoupon' ); ?></label>
            <select id="wjecf_customer_roles" name="_wjecf_customer_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any role', 'woocommerce-jos-autocoupon' ); ?>">
                <?php            
                    $coupon_customer_roles = WJECF()->get_coupon_customer_roles( $thepostid );

                    $available_customer_roles = array_reverse( get_editable_roles() );
                    foreach ( $available_customer_roles as $role_id => $role ) {
                        $role_name = translate_user_role($role['name'] );
        
                        echo '<option value="' . esc_attr( $role_id ) . '"'
                        . selected( in_array( $role_id, $coupon_customer_roles ), true, false ) . '>'
                        . esc_html( $role_name ) . '</option>';
                    }
                ?>
            </select>
             <?php echo WJECF_Admin_Html::wc_help_tip( __( 'Only these User Roles may use this coupon.', 'woocommerce-jos-autocoupon' ) ); ?>
            </p>
            <?php    

            //=============================
            // Excluded user roles
            ?>
            <p class="form-field"><label for="wjecf_excluded_customer_roles"><?php _e( 'Disallowed User Roles', 'woocommerce-jos-autocoupon' ); ?></label>
            <select id="wjecf_customer_roles" name="_wjecf_excluded_customer_roles[]" style="width: 50%;"  class="wc-enhanced-select" multiple="multiple" data-placeholder="<?php _e( 'Any role', 'woocommerce-jos-autocoupon' ); ?>">
                <?php            
                    $coupon_excluded_customer_roles = WJECF()->get_coupon_excluded_customer_roles( $thepostid );

                    foreach ( $available_customer_roles as $role_id => $role ) {
                        $role_name = translate_user_role($role['name'] );
        
                        echo '<option value="' . esc_attr( $role_id ) . '"'
                        . selected( in_array( $role_id, $coupon_excluded_customer_roles ), true, false ) . '>'
                        . esc_html( $role_name ) . '</option>';
                    }
                ?>
            </select>
             <?php echo WJECF_Admin_Html::wc_help_tip( __( 'These User Roles will be specifically excluded from using this coupon.', 'woocommerce-jos-autocoupon' ) ); ?>
            </p>
            <?php    
        }

        // //REMOVED in 2.4.4: Use coupon queueing instead
        // public function admin_coupon_metabox_misc( $thepostid, $post ) {            
        //     echo "<h3>" . esc_html( __( 'Miscellaneous', 'woocommerce-jos-autocoupon' ) ). "</h3>\n";
                   
        //     //=============================
        //     //2.2.2 Allow if minimum spend not met
        //     woocommerce_wp_checkbox( array(
        //         'id'          => '_wjecf_allow_below_minimum_spend',
        //         'label'       => __( 'Allow when minimum spend not reached', 'woocommerce-jos-autocoupon' ),
        //         'description' => '<b>' . __( 'EXPERIMENTAL: ', 'woocommerce-jos-autocoupon' ) . '</b>' . __( 'Check this box to allow the coupon to be in the cart even when minimum spend (see tab \'usage restriction\') is not reached. Value of the discount will be 0 until minimum spend is reached.', 'woocommerce-jos-autocoupon' ),
        //     ) );
        // }

        public function admin_coupon_meta_fields( $coupon ) {
            $fields = array(
                '_wjecf_min_matching_product_qty' => 'int',
                '_wjecf_max_matching_product_qty' => 'int',
                '_wjecf_min_matching_product_subtotal' => 'decimal',
                '_wjecf_max_matching_product_subtotal' => 'decimal',
                '_wjecf_products_and' => 'yesno',
                '_wjecf_categories_and' => 'yesno',
                '_wjecf_shipping_methods' => 'clean',
                '_wjecf_payment_methods' => 'clean',
                '_wjecf_customer_ids' => 'int,',
                '_wjecf_customer_roles' => 'clean',
                '_wjecf_excluded_customer_roles' => 'clean',
            );

            //Espagueti
            if ( WJECF()->is_pro() ) {
                $fields = array_merge( $fields, WJECF()->admin_coupon_meta_fields( $coupon ) );
            }
            return $fields;
        }

        /**
         * Get an array with all the metafields for all the WJECF plugins
         * 
         * @see Abstract_WJECF_Plugin::admin_coupon_meta_fields()
         * 
         * @param type $coupon 
         * @return type
         */
        function get_all_coupon_meta_fields( $coupon ) {
            //Collect the meta_fields of all the WJECF plugins
            $fields = array();
            foreach ( WJECF()->get_plugins() as $name => $plugin ) {
                if ( $plugin->plugin_is_enabled() ) {
                    $fields = array_merge( $fields, $plugin->admin_coupon_meta_fields( $coupon ) );
                }
            }
            return $fields;
        }

        function process_shop_coupon_meta( $post_id, $post ) {
            $coupon = WJECF_WC()->get_coupon( $post );
            $wrap_coupon = WJECF_Wrap( $coupon );
            $sanitizer = WJECF()->sanitizer();

            $fields = $this->get_all_coupon_meta_fields( $coupon );
            foreach( $fields as $key => $rule ) {
                //If array contains [ 'callback' => callback, 'args' => args[] ] 
                //Then that callback will be called with the given args (optional)
                
                if ( is_array( $rule ) && isset( $rule['callback'] ) && is_callable( $rule['callback'] ) ) {
                    $args = array( 'key' => $key );
                    if ( isset( $rule['args'] ) ) $args = array_merge( $args, $rule['args'] );

                    $value = call_user_func( $rule['callback'], $args );
                } else {
                    $value = $sanitizer->sanitize( isset( $_POST[$key] ) ? $_POST[$key] : null, $rule );
                }
                if ( $value === '' ) $value = null; //Don't save empty entries

                $wrap_coupon->set_meta( $key, $value ); //Always single

                //error_log(sprintf("%s => %s", $key, is_array($value) ? 'array' : $value));
            }

            $wrap_coupon->save();
        }



        /**
         * 2.3.6
         * Add inline style (css) to the admin page. Must be called BEFORE admin_head !
         * @param string $css 
         * @return void
         */
        public function add_inline_style( $css ) {
            $this->admin_css .= $css;
        }


        /**
         * 
         * 2.3.4
         * Parse an array or comma separated string; make sure they are valid ints and return as comma separated string
         * @deprecated 2.5.1 Use WJECF()->sanitizer->sanitize( ..., 'int[]' ) instead
         * @param array|string $int_array 
         * @return string comma separated int array
         */
        public function comma_separated_int_array( $int_array ) {
            _deprecated_function( 'comma_separated_int_array', '2.5.1', 'WJECF()->sanitizer->sanitize()' );
            return WJECF()->sanitizer->sanitize( $int_array, 'int[]' );
        }

        /**
         * Add donate-link to plugin page
         */
        function wjecf_plugin_meta( $links, $file ) {
            if ( strpos( $file, 'woocommerce-jos-autocoupon.php' ) !== false ) {
                $links = array_merge( $links, array( '<a href="' . WJECF_Admin::get_donate_url() . '" title="Support the development" target="_blank">Donate</a>' ) );
            }
            return $links;
        }


        public static function get_donate_url() {
            return "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5T9XQBCS2QHRY&lc=NL&item_name=Jos%20Koenis&item_number=wordpress%2dplugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted";
        }
    }

}