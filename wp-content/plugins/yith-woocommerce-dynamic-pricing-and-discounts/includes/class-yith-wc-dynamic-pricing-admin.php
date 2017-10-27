<?php

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWDPD_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Implements admin features of YITH WooCommerce Dynamic Pricing and Discounts
 *
 * @class   YITH_WC_Dynamic_Pricing_Admin
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  Yithemes
 */
if ( !class_exists( 'YITH_WC_Dynamic_Pricing_Admin' ) ) {

    class YITH_WC_Dynamic_Pricing_Admin {

        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Dynamic_Pricing_Admin
         */

        protected static $instance;

        /**
         * @var $_panel Panel Object
         */
        protected $_panel;

        /**
         * @var $_premium string Premium tab template file name
         */
        protected $_premium = 'premium.php';

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = 'http://yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/';

        /**
         * @var string Panel page
         */
        protected $_panel_page = 'yith_woocommerce_dynamic_pricing_and_discounts';

        /**
         * @var string Doc Url
         */
        public $doc_url = 'https://yithemes.com/docs-plugins/yith-woocommerce-dynamic-pricing-and-discounts/';


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Dynamic_Pricing_Admin
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

            $this->create_menu_items();

            // add panel type
            add_action( 'yit_panel_options-pricing-rules', array( $this, 'admin_options_pricing_rules' ), 10, 2 );

            // panel type ajax action add
            add_action( 'wp_ajax_yith_dynamic_pricing_section', array( $this, 'yith_dynamic_pricing_section_ajax' ) );
            add_action( 'wp_ajax_nopriv_yith_dynamic_pricing_section', array( $this, 'yith_dynamic_pricing_section_ajax' ) );

			// panel type category search
			add_action( 'wp_ajax_ywdpd_category_search', array( $this, 'json_search_categories' ) );
			add_action( 'wp_ajax_nopriv_ywdpd_category_search', array( $this, 'json_search_categories' ) );

            // panel type ajax action remove
            add_action( 'wp_ajax_yith_dynamic_pricing_section_remove', array( $this, 'yith_dynamic_pricing_section_remove_ajax' ) );
            add_action( 'wp_ajax_nopriv_yith_dynamic_pricing_section_remove', array( $this, 'yith_dynamic_pricing_section_remove_ajax' ) );

            //Add action links
            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWDPD_DIR . '/' . basename( YITH_YWDPD_FILE ) ), array( $this, 'action_links' ) );
            add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

            //custom styles and javascripts
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 11);

            //tab premium
            add_action( 'yith_ywraq_premium_tab', array( $this, 'premium_tab' ) );

        }


        /**
         * Enqueue styles and scripts
         *
         * @access public
         * @return void
         * @since 1.0.0
         */
        public function enqueue_styles_scripts() {
            wp_enqueue_style( 'yith_ywdpd_backend', YITH_YWDPD_ASSETS_URL . '/css/backend.css', YITH_YWDPD_VERSION );
            wp_enqueue_script( 'yith_ywdpd_admin', YITH_YWDPD_ASSETS_URL . '/js/ywdpd-admin' . YITH_YWDPD_SUFFIX . '.js', array( 'jquery','jquery-ui-sortable' ), YITH_YWDPD_VERSION, true );
            wp_enqueue_script( 'jquery-blockui', YITH_YWDPD_ASSETS_URL . '/js/jquery.blockUI.min.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'ajax-chosen', YITH_YWDPD_URL.'plugin-fw/assets/js/chosen/ajax-chosen.jquery'. YITH_YWDPD_SUFFIX . '.js', array( 'jquery' ), false, true );
            wp_enqueue_script( 'ajax-chosen');
            wp_enqueue_script( 'wc-enhanced-select' );
            wp_localize_script( 'yith_ywdpd_admin', 'yith_ywdpd_admin', array(
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'search_categories_nonce' => wp_create_nonce( 'search-categories' ),
				'block_loader'            => apply_filters( 'yith_ywdpd_block_loader_admin', YITH_YWDPD_ASSETS_URL . '/images/block-loader.gif' ),
				'error_msg'               => apply_filters( 'yith_ywdpd_error_msg_admin', __( 'Please insert a description for the rule', 'yith-woocommerce-dynamic-pricing-and-discounts' ) ),
				'del_msg'                 => apply_filters( 'yith_ywdpd_delete_msg_admin', __( 'Do you really want to delete this rule?', 'yith-woocommerce-dynamic-pricing-and-discounts' ) )
            ));

        }



        /**
         * Create Menu Items
         *
         * Print admin menu items
         *
         * @since  1.0
         * @author Emanuela Castorina
         */

        private function create_menu_items() {

            // Add a panel under YITH Plugins tab
            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
            add_action( 'yith_ywdpd_premium_tab', array( $this, 'premium_tab' ) );
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */

        public function register_panel() {

            if ( !empty( $this->_panel ) ) {
                return;
            }

            $admin_tabs = array(
                'general' => __( 'Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' )
            );

            if ( defined( 'YITH_YWDPD_FREE_INIT' ) ) {
                $admin_tabs['premium'] = __( 'Premium Version', 'yith-woocommerce-dynamic-pricing-and-discounts' );
            }
            else {
                $admin_tabs['cart-discount']     = __( 'Cart Discount', 'yith-woocommerce-dynamic-pricing-and-discounts' );
            }

            $args = array(
                'create_menu_page' => true,
                'parent_slug'      => '',
                'page_title'       => __( 'Dynamic Pricing', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
                'menu_title'       => __( 'Dynamic Pricing', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
                'capability'       => 'manage_options',
                'parent'           => 'ywdpd',
                'parent_page'      => 'yit_plugin_panel',
                'page'             => $this->_panel_page,
                'admin-tabs'       => $admin_tabs,
                'options-path'     => YITH_YWDPD_DIR . '/plugin-options'
            );

            /* === Fixed: not updated theme  === */
            if ( !class_exists( 'YIT_Plugin_Panel' ) ) {
                require_once( YITH_YWDPD_DIR.'/plugin-fw/lib/yit-plugin-panel.php' );
            }

            $this->_panel = new YIT_Plugin_Panel( $args );
            
        }

        /**
         * Add new pricing rules options section
         *
         * @since 1.0.0
         * @access public
         * @author Emanuela Castorina
         */
        public function yith_dynamic_pricing_section_ajax() {

            if ( ! isset( $_REQUEST['section'] ) ) {
                die();
            }

            $description = strip_tags( $_REQUEST['section'] );
            $key     = uniqid();
            $id      = $_REQUEST['id'];
            $name    = $_REQUEST['name'];

            include( YITH_YWDPD_TEMPLATE_PATH . '/admin/options-pricing-rules-panel.php' );

            die();
        }


        
        /**
         * Premium Tab Template
         *
         * Load the premium tab template on admin page
         *
         * @return   void
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */

        public function premium_tab() {
            $premium_tab_template = YITH_YWDPD_TEMPLATE_PATH . '/admin/' . $this->_premium;
            if ( file_exists( $premium_tab_template ) ) {
                include_once( $premium_tab_template );
            }
        }


        /**
         * Action Links
         *
         * add the action links to plugin admin page
         *
         * @param $links | links plugin array
         *
         * @return   mixed Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed
         * @use      plugin_action_links_{$plugin_file_name}
         */

        public function action_links( $links ) {

            $links[] = '<a href="' . admin_url( "admin.php?page={$this->_panel_page}" ) . '">' . __( 'Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' ) . '</a>';
            if ( defined( 'YITH_YWDPD_FREE_INIT' ) ) {
              $links[] = '<a href="' . $this->get_premium_landing_uri() . '" target="_blank">' . __( 'Premium Version', 'yith-woocommerce-dynamic-pricing-and-discounts' ) . '</a>';
           }

            return $links;
        }


        /**
         * plugin_row_meta
         *
         * add the action links to plugin admin page
         *
         * @param $plugin_meta
         * @param $plugin_file
         * @param $plugin_data
         * @param $status
         *
         * @return   Array
         * @since    1.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         * @use      plugin_row_meta
         */

        public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

            if ( defined( 'YITH_YWDPD_INIT' ) && YITH_YWDPD_INIT == $plugin_file ) {
                $plugin_meta[] = '<a href="' . $this->doc_url . '" target="_blank">' . __( 'Plugin Documentation', 'yith-woocommerce-dynamic-pricing-and-discounts' ) . '</a>';
            }
            return $plugin_meta;
        }



        /**
         * Get the premium landing uri
         *
         * @since   1.0.0
         * @author  Andrea Grillo <andrea.grillo@yithemes.com>
         * @return  string The premium landing link
         */
        public function get_premium_landing_uri(){
            return defined( 'YITH_REFER_ID' ) ? $this->_premium_landing . '?refer_id=' . YITH_REFER_ID : $this->_premium_landing . '?refer_id=1030585';
        }


        /**
         * Template for admin section
         *
         * @since 1.0.0
         * @access public
         * @author Emanuela Castorina
         */
        public function admin_options_pricing_rules( $option, $db_value ) {
            include( YITH_YWDPD_TEMPLATE_PATH . '/admin/options-pricing-rules.php' );
        }

        /**
         * Json Search Category to load product categories in chosen selects
         *
         * @since 1.0.0
         * @access public
         * @author Emanuela Castorina
         */
        public function json_search_categories( ) {
			check_ajax_referer( 'search-categories', 'security' );

			ob_start();

			$term = (string) wc_clean( stripslashes( $_GET['term'] ) );

			if ( empty( $term ) ) {
				die();
			}
            global $wpdb;
            $terms = $wpdb->get_results( 'SELECT name, slug, wpt.term_id FROM ' . $wpdb->prefix . 'terms wpt, ' . $wpdb->prefix . 'term_taxonomy wptt WHERE wpt.term_id = wptt.term_id AND wptt.taxonomy = "product_cat" and wpt.name LIKE "%'.$term.'%" ORDER BY name ASC;' );

            $found_categories = array();

            if ( $terms ) {
                foreach ( $terms as $cat ) {
                    $found_categories[$cat->term_id] = ( $cat->name ) ? $cat->name : 'ID: ' . $cat->slug;
                }
            }

			$found_categories = apply_filters( 'ywdpd_json_search_categories', $found_categories );

			wp_send_json( $found_categories );



        }



    }
}

/**
 * Unique access to instance of YITH_WC_Dynamic_Pricing_Admin class
 *
 * @return \YITH_WC_Dynamic_Pricing_Admin
 */
function YITH_WC_Dynamic_Pricing_Admin() {
    return YITH_WC_Dynamic_Pricing_Admin::get_instance();
}
