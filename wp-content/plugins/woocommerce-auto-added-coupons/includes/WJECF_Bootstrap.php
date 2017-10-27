<?php

if ( ! defined('ABSPATH') ) die();

/**
 * Loads the plugin
 */
class WJECF_Bootstrap {

    public static function execute() {
        self::instance()->bootstrap();
    }

    protected function bootstrap() {

        $this->require_once_php( 'Abstract_WJECF_Plugin' );
        $this->require_once_php( 'WJECF_Options' );
        $this->require_once_php( 'WJECF_Sanitizer' );

        $this->require_once_php( 'WJECF_Controller' );
        $pro = $this->try_include_php( 'pro/WJECF_Pro_Controller' );
        $this->require_once_php( 'WJECF_WC' );
        $this->require_once_php( 'WJECF_Wrap' );

        $this->require_once_php( 'admin/WJECF_Admin_Html' );
        $this->add_plugin('admin/WJECF_Admin');
        $this->try_add_plugin('admin/WJECF_Admin_Data_Update');
        
        $this->try_add_plugin('admin/WJECF_Admin_Settings');
        $this->try_add_plugin('WJECF_AutoCoupon');
        $this->try_add_plugin('WJECF_WPML');

        if ( $pro ) {
            $this->try_include_php( 'pro/WJECF_Pro_API' ); 
            $this->try_add_plugin('pro/admin/WJECF_Pro_Admin_Auto_Update');

            $this->try_add_plugin('pro/WJECF_Pro_Free_Products/WJECF_Pro_Free_Products');
            $this->try_add_plugin('pro/WJECF_Pro_Coupon_Queueing');
            $this->try_add_plugin('pro/WJECF_Pro_Product_Filter');
            $this->try_add_plugin('pro/WJECF_Pro_Limit_Discount_Quantities');
        }

        WJECF()->start();

        //WP-cli for debugging        
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            if ( $this->try_include_php( 'WJECF_Debug_CLI' ) ) {
                WJECF_Debug_CLI::add_command();
            }
        }

    }

    /**
     * Singleton Instance
     *
     * @static
     * @return Singleton Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected static $_instance = null;


    /**
     * require_once( $path + '.php' )
     * @param string $path Path to the php file (excluding extension) relative to the current path
     * @return bool True if succesful
     */
    protected function require_once_php( $path ) {
        $fullpath = $this->get_path( $path ) . '.php';
        require_once( $fullpath );
    }    

    /**
     * tries to include_once( $path + '.php' )
     * @param string $path Path to the php file (excluding extension) relative to the current path
     * @return bool True if succesful
     */
    protected function try_include_php( $path ) {
        $fullpath = $this->get_path( $path ) . '.php';

        if ( ! file_exists( $fullpath ) ) {
            return false;
        }

        include_once( $fullpath );
        return true;
    }

    /**
     * Loads the class file and adds the plugin to WJECF(). Throws exception on failure.
     * @param string $path Path to the php file (excluding extension) relative to the current path
     * @return void
     */
    protected function add_plugin( $path ) {
        $class_name = basename( $path );

        $this->require_once_php( $path );

        if ( ! WJECF()->add_plugin( $class_name ) ) {
            throw new Exception( sprintf( 'Unable to add plugin %s', $class_name ) );
        }
    }

    /**
     * Tries loading the class file and adding the plugin to WJECF(). 
     * @param string $path Path to the php file (excluding extension) relative to the current path
     * @return bool True if succesful
     */
    protected function try_add_plugin( $path ) {
        $class_name = basename( $path );
        return $this->try_include_php( $path ) && WJECF()->add_plugin( $class_name );
    }

    /**
     * Gets the path relative to the includes/ directory
     * @param string $path 
     * @return string
     */
    private function get_path( $path ) {
        return plugin_dir_path( __FILE__ ) . $path;
    }

}