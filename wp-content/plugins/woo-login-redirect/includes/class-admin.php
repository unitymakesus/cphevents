<?php
/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu() {
        global $submenu;

        $capability = 'manage_options';
        $slug       = 'wlr-login-redirect';

        $hook = add_menu_page( __( 'Login Redirect', 'textdomain' ), __( 'Login Redirect', 'textdomain' ), $capability, $slug, [ $this, 'plugin_page' ], 'dashicons-text' );

        add_action( 'load-' . $hook, [ $this, 'init_hooks'] );
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'wlr-admin' );
        wp_enqueue_script( 'wlr-admin' );
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page() {
        echo '<div class="wrap"><div id="vue-admin-app"></div></div>';
    }
}

// added in v2.2.3

function woo_admin_notice(){
    global $pagenow;
    if ( $pagenow == 'plugins.php') {
    $user = wp_get_current_user();
    if ( in_array( 'administrator', (array) $user->roles ) ) {
    echo '<div class="notice notice-info is-dismissible">
         <span class="woo-discount-banner" style="float:left; padding:0px 10px;">
          <p> <a href="https://wpdoctor.press/product/login-redirect-pro/"> <img src="https://wpdoctor.press/wp-content/uploads/2019/03/20-off-png-37382.gif" height=100px width=100px></a></p></span>
          
          <h2>Get 20% Discount To Buy <a href="https://wpdoctor.press/product/login-redirect-pro/" target="_blank">Login Redirect Pro</a></h2>
          <p>Use coupon code "<strong>wooget20</strong>"</p>
          <p>This offer is limited!! Grab the deal today</p>
         </div>';
    }
}

}
add_action('admin_notices', 'woo_admin_notice');