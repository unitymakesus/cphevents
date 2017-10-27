<?php

abstract class Abstract_WJECF_Plugin {

//Override these functions in the WJECF plugin

    public function init_hook() {}

    public function init_admin_hook() {}

    /**
     * Returns an array with the meta_keys for this plugin and the sanitation to apply.
     * Instead of a sanitation a callback can be supplied; which must return the meta value to save to the database
     * 
     * e.g. [
     *  '_wjecf_some_comma_separated_ints' => 'int,', 
     *  '_wjecf_some_callback' => [ 'callback' => [ callback ] ],
     * ]
     * 
     * @return array The fields for this plugin
     */
    public function admin_coupon_meta_fields( $coupon ) {
        return array();
    }

    /**
     * Asserts that all dependencies are respected. If not an Exception is thrown. Override this function for extra assertions (e.g. minimum plugin versions)
     * @return void
     */
    public function assert_dependencies() {
        foreach( $this->get_plugin_dependencies() as $dependency ) {
            if ( ! isset( $this->plugins[ $dependency ] ) ) {
                throw new Exception( sprintf( 'Missing dependency %s', $dependency) );
            }
        }

        if ( ! empty ( $this->plugin_data['minimal_wjecf_version'] ) ) {
            $this->assert_wjecf_version( $this->plugin_data['minimal_wjecf_version'] );
        }
    }

    /**
     * Assert minimum WJECF version number
     * @param string $required_version 
     * @return void
     */
    protected function assert_wjecf_version( $required_version ) {
        if ( version_compare( WJECF()->plugin_version(), $required_version, '<' ) ) {
            throw new Exception( sprintf( __( 'WooCommerce Extended Coupon Features version %s is required. You have version %s', 'woocommerce-jos-autocoupon' ), $required_version, WJECF()->plugin_version() ) );
        }        
    }

//

    /**
     * Log a message (for debugging)
     *
     * @param string $message The message to log
     *
     */
    protected function log( $level, $message = null ) {
        //Backwards compatibility; $level was introduced in 2.4.4
        if ( is_null( $message ) ) {
            $message = $level;
            $level = 'debug';
        }
        WJECF()->log( $level, $message, 1 );
    }

    private $plugin_data = array();

    /**
     *  Information about the WJECF plugin
     * @param string|null $key The data to look up. Will return an array with all data when omitted
     * @return mixed
     */
    protected function get_plugin_data( $key = null ) {
        $default_data = array(
            'description' => '',
            'can_be_disabled' => false,
            'dependencies' => array(),
            'minimal_wjecf_version' => ''
        );
        $plugin_data = array_merge( $default_data, $this->plugin_data );
        if ( $key === null ) { 
            return $plugin_data;
        }
        return $plugin_data[$key];
    }

    /**
     *  Set information about the WJECF plugin
     * @param array $plugin_data The data for this plugin
     * @return void
     */
    protected function set_plugin_data( $plugin_data ) {
        $this->plugin_data = $plugin_data;
    }

    /**
     *  Get the description if this WJECF plugin.
     * @return string
     */
    public function get_plugin_description() {
        return $this->get_plugin_data( 'description' );
    }

    /**
     *  Get the class name of this WJECF plugin.
     * @return string
     */
    public function get_plugin_class_name() {
        return get_class( $this );
    }

    public function get_plugin_dependencies() {
        return $this->get_plugin_data( 'dependencies' );
    }

    public function plugin_is_enabled() {
        if ( ! $this->get_plugin_data( 'can_be_disabled' ) ) return true;
        return ! in_array( $this->get_plugin_class_name(), WJECF()->get_option('disabled_plugins') );
    }

}
