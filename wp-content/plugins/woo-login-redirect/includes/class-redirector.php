<?php
defined( 'ABSPATH' ) || exit;

class Woo_Login_Redirector {
    private static $instance;

    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Init all hooks
     *
     * @return void
     */
    public function init_hooks() {
        add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_based_on_user_role' ), 99, 2 );
        add_filter( 'woocommerce_registration_redirect', array( $this, 'register_redirect' ) );
    }

    /**
     * Redirect based on user role
     *
     * @return string
     */
    public function redirect_based_on_user_role( $default_url, $user ) {

        if ( method_exists( 'WLR_Pro', 'redirect_based_on_custom_url' ) ) {
            $url = WLR_Pro::redirect_based_on_custom_url( $default_url, $user );

            if ( $url ) {
                return $url;
            }
        }

        if ( method_exists( 'WLR_Pro', 'redirect_based_on_user' ) ) {
            $url = WLR_Pro::redirect_based_on_user( $default_url, $user );

            if ( $url ) {
                return $url;
            }
        }

        $options = get_option( 'wlr_role_based_redirect' );

        if ( ! $options ) {
            return $default_url;
        }

        $default_page  = url_to_postid( $default_url );
        $checkout_page = function_exists( 'wc_get_page_id' ) ? wc_get_page_id( 'checkout' ) : '';

        if ( $default_page == $checkout_page ) {
            return $default_url;
        }

        $user_role = WLR_Helper::get_current_user_role( $user );

        if ( ! $user_role ) {
            return $default_url;
        }

        $result = array_filter( $options, function( $option ) use ( $user_role ) {
            $role = isset( $option['role'] ) ? $option['role'] : '';

            return $role == $user_role;
        } );

        $result = array_values( $result );

        return isset( $result[0]['url'] ) ? get_permalink( $result[0]['url'] ) : $default_url;
    }

    /**
     * Registration redirect
     *
     * @param  int $url
     *
     * @return string
     */
    public static function register_redirect( $url ) {
        $reg_page = get_option( 'reg_redirect_page_id' );

        if ( ! $reg_page ) {
            return $url;
        }

        $page_id  = isset( $reg_page['id'] ) ? $reg_page['id'] : '';
        $page_url = isset( $reg_page['url'] ) ? $reg_page['url'] : '';

        if ( $page_url ) {
            return $page_url;
        }

        return $page_id === 'default' ? $url : get_permalink( $page_id );
    }

    /**
     * Get class instance
     *
     * @return object
     */
    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Static;
        }

        return self::$instance;
    }
}