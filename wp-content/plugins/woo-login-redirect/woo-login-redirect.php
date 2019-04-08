<?php
/*
Plugin Name: Woo Login Redirect
Plugin URI: https://wordpress.org/plugins/woo-login-redirect/
Description: Redirect your user after login/registration in WooCommerce
Version: 2.2.3
Author: WP Doctor
Author URI: https://wpdoctor.press
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: baseplugin
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

/**
 * WLR_Login_Redirect class
 *
 * @class WLR_Login_Redirect The class that holds the entire WLR_Login_Redirect plugin
 */
final class WLR_Login_Redirect {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '2.2.2';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the WLR_Login_Redirect class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the WLR_Login_Redirect() class
     *
     * Checks for an existing WLR_Login_Redirect() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WLR_Login_Redirect();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'WLR_VERSION', $this->version );
        define( 'WLR_FILE', __FILE__ );
        define( 'WLR_PATH', dirname( WLR_FILE ) );
        define( 'WLR_INCLUDES', WLR_PATH . '/includes' );
        define( 'WLR_URL', plugins_url( '', WLR_FILE ) );
        define( 'WLR_ASSETS', WLR_URL . '/assets' );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'wlr_installed' );

        if ( ! $installed ) {
            update_option( 'wlr_installed', time() );
        }

        update_option( 'wlr_version', WLR_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once WLR_INCLUDES . '/class-assets.php';
        require_once WLR_INCLUDES . '/class-helpers.php';

        if ( $this->is_request( 'admin' ) ) {
            require_once WLR_INCLUDES . '/class-admin.php';
            require_once WLR_INCLUDES . '/class-settings.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once WLR_INCLUDES . '/class-redirector.php';
        }
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'init', array( $this, 'init_classes' ) );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin'] = new Admin();
            $this->container['woo_login_redirect_settings_vue'] = Woo_Login_Redirect_Settings_Vue::init();
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['woo_login_redirector'] = Woo_Login_Redirector::init();
        }

        $this->container['assets'] = new Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'baseplugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

} // WLR_Login_Redirect

WLR_Login_Redirect::init();
