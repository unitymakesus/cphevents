<?php

defined( 'ABSPATH' ) || exit;

class Woo_Login_Redirect_Settings_Vue {
    /**
     * Constructor method
     */
    public function __construct() {
        $this->init_hooks();
    }

    public function init_hooks() {
        add_action( 'wp_ajax_save_role_based',                  array( $this, 'save_role_based' ) );
        add_action( 'wp_ajax_get_role_based_redirects',         array( $this, 'get_role_based_redirects' ) );
        add_action( 'wp_ajax_get_all_user_roles',               array( $this, 'get_all_user_roles' ) );
        add_action( 'wp_ajax_get_all_pages',                    array( $this, 'get_all_pages' ) );
        add_action( 'wp_ajax_save_registration',                array( $this, 'save_registration' ) );
        add_action( 'wp_ajax_get_registration_based_redirect',  array( $this, 'get_registration_based_redirect' ) );
        add_action( 'wp_ajax_is_pro_installed',                 array( $this, 'is_pro_installed' ) );
    }

    public function is_pro_installed() {
        $is_installed = WLR_Helper::is_pro_installed();

        if ( ! $is_installed ) {
            return wp_send_json_success( 'Pro is not installed', 200 );
        }

        wp_send_json_success( 'pro_is_installed', 200 );
        exit;
    }

    public function get_all_pages() {
        $pages = WLR_Helper::get_pages();

        if ( ! $pages || is_wp_error( $pages ) ) {
            wp_send_json_error( __( 'Something went wrong', 'wlr' ), 404 );
        }

        return wp_send_json_success( $pages, 200 );
    }

    public function get_all_user_roles() {
        if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'get_all_user_roles' ) {
            return;
        }

        $roles = WLR_Helper::get_user_roles();

        if ( ! $roles || is_wp_error( $roles ) ) {
            wp_send_json_error( __( 'Something went wrong', 'wlr' ), 404 );
        }

        wp_send_json_success( $roles, 200 );
    }

    public function get_role_based_redirects() {
        if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'get_role_based_redirects' ) {
            return;
        }

        $settings = get_option( 'wlr_role_based_redirect' );

        return wp_send_json_success( $settings, 200 );
    }

    public function save_role_based() {
        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'save_role_based' ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! isset( $_POST['settings'] ) || empty( $_POST['settings'] ) ) {
            return;
        }

        $settings         = $_POST['settings'];
        $settings_to_save = array();

        foreach ( $settings as $setting ) {
            if ( ! isset( $setting['role'], $setting['url'] ) || empty( $setting['role'] ) || empty( $setting['url'] ) ) {
                continue;
            }

            array_push( $settings_to_save, $setting );
        }

        update_option( 'wlr_role_based_redirect', $settings_to_save );
        wp_send_json_success( 'Data is saved successfully', 201 );
        exit;
    }

    public function save_registration() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

        if ( ! $settings ) {
            wp_send_json_error( 'Something went wrong', 404 );
        }

        update_option( 'reg_redirect_page_id', $settings );

        wp_send_json_success( 'Data is saved successfully', 201 );
        exit;
    }


    public function get_registration_based_redirect() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $pages = get_option( 'reg_redirect_page_id' );

        wp_send_json_success( $pages, 200 );
    }

    public static function init() {
        $instance = false;

        if ( ! $instance ) {
            $instance = new static;
        }

        return $instance;
    }
}