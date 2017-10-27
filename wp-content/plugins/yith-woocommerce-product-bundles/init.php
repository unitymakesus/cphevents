<?php
/**
 * Plugin Name: YITH WooCommerce Product Bundles
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-product-bundles
 * Description: YITH WooCommerce Product Bundles allows you to bundle WooCommerce products and sell them with a unique price.
 * Version: 1.1.3
 * Author: YITHEMES
 * Author URI: http://yithemes.com/
 * Text Domain: yith-woocommerce-product-bundles
 * Domain Path: /languages/
 * WC requires at least: 2.6.0
 * WC tested up to: 3.2.0
 *
 * @author yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.1.3
 */
/*  Copyright 2015  Your Inspiration Themes  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* == COMMENT == */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_wcpb_install_woocommerce_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'YITH WooCommerce Product Bundles is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-bundles' ); ?></p>
    </div>
    <?php
}


function yith_wcpb_install_free_admin_notice() {
    ?>
    <div class="error">
        <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Product Bundles while you are using the premium one.', 'yith-woocommerce-product-bundles' ); ?></p>
    </div>
    <?php
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( !defined( 'YITH_WCPB_VERSION' ) ) {
    define( 'YITH_WCPB_VERSION', '1.1.3' );
}

if ( !defined( 'YITH_WCPB_FREE_INIT' ) ) {
    define( 'YITH_WCPB_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YITH_WCPB' ) ) {
    define( 'YITH_WCPB', true );
}

if ( !defined( 'YITH_WCPB_FILE' ) ) {
    define( 'YITH_WCPB_FILE', __FILE__ );
}

if ( !defined( 'YITH_WCPB_URL' ) ) {
    define( 'YITH_WCPB_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'YITH_WCPB_DIR' ) ) {
    define( 'YITH_WCPB_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YITH_WCPB_TEMPLATE_PATH' ) ) {
    define( 'YITH_WCPB_TEMPLATE_PATH', YITH_WCPB_DIR . 'templates' );
}

if ( !defined( 'YITH_WCPB_ASSETS_URL' ) ) {
    define( 'YITH_WCPB_ASSETS_URL', YITH_WCPB_URL . 'assets' );
}

if ( !defined( 'YITH_WCPB_ASSETS_PATH' ) ) {
    define( 'YITH_WCPB_ASSETS_PATH', YITH_WCPB_DIR . 'assets' );
}

if ( !defined( 'YITH_WCPB_INCLUDES_PATH' ) ) {
    define( 'YITH_WCPB_INCLUDES_PATH', YITH_WCPB_DIR . 'includes' );
}


function yith_wcpb_init() {

    load_plugin_textdomain( 'yith-woocommerce-product-bundles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    // Load required classes and functions
    require_once( 'includes/class.yith-wc-product-bundle.php' );
    require_once( 'includes/class.yith-wc-bundled-item.php' );
    require_once( 'includes/compatibility/class.yith-wcpb-compatibility.php' );
    require_once( 'class.yith-wcpb-admin.php' );
    require_once( 'class.yith-wcpb-frontend.php' );
    require_once( 'class.yith-wcpb.php' );

    // Let's start the game!
    YITH_WCPB();
}

add_action( 'yith_wcpb_init', 'yith_wcpb_init' );


function yith_wcpb_install() {

    if ( !function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_wcpb_install_woocommerce_admin_notice' );
    } elseif ( defined( 'YITH_WCPB_PREMIUM' ) ) {
        add_action( 'admin_notices', 'yith_wcpb_install_free_admin_notice' );
        deactivate_plugins( plugin_basename( __FILE__ ) );
    } else {
        do_action( 'yith_wcpb_init' );
    }
}

add_action( 'plugins_loaded', 'yith_wcpb_install', 11 );

/* Plugin Framework Version Check */
if ( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );