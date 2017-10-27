<?php
/*
Plugin Name: YITH WooCommerce Dynamic Pricing and Discounts
Description: YITH WooCommerce Dynamic Pricing and Discounts offers a powerful tool to directly modify prices and discounts of your store
Version: 1.1.2
Author: YITHEMES
Author URI: http://yithemes.com/
Text Domain: yith-woocommerce-dynamic-pricing-and-discounts
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  YITHEMES
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// This version can't be activate if premium version is active  ________________________________________
if ( defined( 'YITH_YWDPD_PREMIUM' ) ) {
    function yith_ywdpd_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Dynamic Pricing and Discounts while you are using the premium one.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
        </div>
    <?php
    }

    add_action( 'admin_notices', 'yith_ywdpd_install_free_admin_notice' );

    deactivate_plugins( plugin_basename( __FILE__ ) );
    return;
}

// Registration hook  ________________________________________
if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( !function_exists( 'yith_ywdpd_install_woocommerce_admin_notice' ) ) {
    function yith_ywdpd_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Dynamic Pricing and Discounts is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
        </div>
    <?php
    }
}

// Define constants ________________________________________
if ( defined( 'YITH_YWDPD_VERSION' ) ) {
    return;
}else{
    define( 'YITH_YWDPD_VERSION', '1.1.2' );
}

if ( ! defined( 'YITH_YWDPD_FREE_INIT' ) ) {
    define( 'YITH_YWDPD_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_INIT' ) ) {
    define( 'YITH_YWDPD_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_FILE' ) ) {
    define( 'YITH_YWDPD_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWDPD_DIR' ) ) {
    define( 'YITH_YWDPD_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_URL' ) ) {
    define( 'YITH_YWDPD_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWDPD_ASSETS_URL' ) ) {
    define( 'YITH_YWDPD_ASSETS_URL', YITH_YWDPD_URL . 'assets' );
}

if ( ! defined( 'YITH_YWDPD_TEMPLATE_PATH' ) ) {
    define( 'YITH_YWDPD_TEMPLATE_PATH', YITH_YWDPD_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWDPD_INC' ) ) {
    define( 'YITH_YWDPD_INC', YITH_YWDPD_DIR . '/includes/' );
}

if ( ! defined( 'YITH_YWDPD_SUFFIX' ) ) {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    define( 'YITH_YWDPD_SUFFIX', $suffix );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWDPD_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_YWDPD_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_YWDPD_DIR  );


if ( ! function_exists( 'yith_ywdpd_install' ) ) {
    function yith_ywdpd_install() {

        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_ywdpd_install_woocommerce_admin_notice' );
        } else {
            do_action( 'yith_ywdpd_init' );
        }
    }

    add_action( 'plugins_loaded', 'yith_ywdpd_install', 11 );
}


function yith_ywdpd_constructor() {

    // Woocommerce installation check _________________________
    if ( !function_exists( 'WC' ) ) {
        function yith_ywdpd_install_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'YITH WooCommerce Dynamic Pricing and Discounts is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></p>
            </div>
        <?php
        }

        add_action( 'admin_notices', 'yith_ywdpd_install_woocommerce_admin_notice' );
        return;
    }

    // Load YWSL text domain ___________________________________
    load_plugin_textdomain( 'yith-woocommerce-dynamic-pricing-and-discounts', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    require_once( YITH_YWDPD_INC . 'functions.yith-wc-dynamic-pricing.php' );
    require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-admin.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-frontend.php' );
	require_once( YITH_YWDPD_INC . 'class-yith-wc-dynamic-pricing-helper.php' );

	if ( is_admin() ) {
		YITH_WC_Dynamic_Pricing_Admin();
	}
    YITH_WC_Dynamic_Pricing();
    YITH_WC_Dynamic_Pricing_Frontend();

}
add_action( 'yith_ywdpd_init', 'yith_ywdpd_constructor' );