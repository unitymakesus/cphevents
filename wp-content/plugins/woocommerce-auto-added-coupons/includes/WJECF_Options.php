<?php


/**
 * Manages an Array of values that can be saved in the WP options table
 */
class WJECF_Options {

    private $options = null; // [ 'key' => 'value' ]
    private $defaults = array(); // [ 'key' => 'value' ]

    private $option_name;

    /**
     * Description
     * @param string $option_name 
     * @param array $defaults Default values
     * @param bool $auto_reload_on_save If true, the options shall be reloaded after safe
     * @return type
     */
    public function __construct( $option_name, $defaults = array(), $auto_reload_on_save = true ) {
        $this->option_name = $option_name;
        $this->defaults = $defaults;

        if ( $auto_reload_on_save ) {
            //reload options directly after save
            add_action( 'update_option_' . $option_name, array( $this, 'invalidate' ), 1, 0 ); 
        }
    }

    /**
     * Forces reloading of the options; invalidates the current values
     * @return void
     */
    public function invalidate() {
        $this->options = null;
    }

    /**
     * The option_name used in the WP options table
     * @return string
     */
    public function get_option_name() {
        return $this->option_name;
    }

    /**
     * Loads the options from the database
     * @return void
     */
    protected function load_options() {
        $options = get_option( $this->option_name );
        if ( ! is_array( $options ) || empty ( $options ) ) {
            $this->options = $this->defaults;
        } else {
            $this->options = array_merge( $this->defaults, $options );
        }
    }

    /**
     * Get option [ $key ]. If $key is not given, return all options.
     * @param string $key 
     * @param mixed $default The default value to return (only if $key is given)
     * @return mixed The value of the option
     */
    public function get( $key = null, $default = null ) {
        if ( ! isset( $this->options ) ) {
            $this->load_options();
        }

        //Return all options
        if ( is_null( $key ) ) {
            return $this->options;
        }

        //Return option[$key]
        $value = isset( $this->options[$key] ) ? $this->options[$key] : $default;
        return $value;
    }

    /**
     * Set option [ $key ]
     * @param string $key 
     * @param mixed $value 
     */
    public function set( $key, $value ) {
        if ( ! isset( $this->options ) ) {
            $this->load_options();
        }
        $this->options[$key] = $value;
    }

    /**
     * Save options to the database
     */
    public function save() {
        update_option( $this->option_name , $this->options, false );
    }    
}