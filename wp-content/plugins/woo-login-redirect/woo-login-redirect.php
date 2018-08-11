<?php	
/*
Plugin Name: Woo Login Redirect
Plugin URI: https://github.com/nayemDevs/woo-login-redirect
Description: Redirect your user after login/registration in WooCommerce
Version: 2.0
Author: Nayem
Author URI: https://wpdoctor.press
License: GPL2
*/

/**
 * Don't call me directly
 */
if ( ! defined( 'WPINC' ) ) exit;

/**
 * Woo Login Class
 */
class Woo_Login_Redirect {
    public static $version;

    /**
     * Constructor method
     */
    public function __construct() {
        self::$version = '2.0';

        $this->define_constants();

        $this->includes();
        $this->init_classes();
    }

    /**
     * Initial Setup
     * 
     * @return void
     */
    public static function plugin_setup() {
        update_option( 'wlr_plugin_version', self::$version );
    }

    /**
     * Define all the constants
     * 
     * @return void
     */
    public function define_constants() {
        define( 'WLR_INC', plugin_dir_path( __FILE__ ) . 'includes' );
        define( 'WLR_PLUGIN_VERSION', self::$version );
    }

    /**
     * Include files
     * 
     * @return string
     */
    public function includes() {
        if ( is_admin() ) {
            require_once WLR_INC . '/class-settings.php';
            require_once WLR_INC . '/upgrade.php';
        } else {
            require_once WLR_INC . '/class-login-redirector.php';
        }
    }

    /**
     * Init classes
     * 
     * @return void
     */
    public function init_classes() {
        if ( is_admin() ) {
            Woo_Login_Redirect_Settings::init();
            $update = new Wlr_Upgrade();
        } else {
            Woo_Login_Redirector::init();
        }
    }

    public static function init() {
        $instance = false;

        if ( ! $instance ) {
            $instance = new static;
        }

        return $instance;
    }
}

Woo_Login_Redirect::init();

register_activation_hook( __FILE__, array( 'Woo_Login_Redirect', 'plugin_setup' ) );
