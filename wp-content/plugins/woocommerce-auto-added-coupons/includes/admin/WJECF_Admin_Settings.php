<?php

defined('ABSPATH') or die();

if ( class_exists('WJECF_Admin') ) {
    return;
}

class WJECF_Admin_Settings extends Abstract_WJECF_Plugin {
    const DOM_PREFIX = 'wjecf-';
    const SETTINGS_PAGE = 'wjecf_settings'; // In the wp_options table
    const OPTION_NAME = 'wjecf_options'; // In the wp_options table

    public function __construct() {    
        $this->set_plugin_data( array(
            'description' => __( 'Settings page of WooCommerce Extended Coupon Features.', 'woocommerce-jos-autocoupon' ),
            'dependencies' => array(),
            'can_be_disabled' => false
        ) );
    }

    public function init_admin_hook() {
        //return;
        add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'action_admin_init' ) );
    }        

    public function action_admin_menu() {
        add_options_page( __( 'WooCommerce Extended Coupon Features', 'woocommerce-jos-autocoupon' ), __( 'WooCommerce Extended Coupon Features', 'woocommerce-jos-autocoupon' ), 'manage_options', self::SETTINGS_PAGE, array( &$this, 'action_admin_config_page' ) ); 
    }

    public function action_admin_config_page() {
?>
        <h2><?php _e( 'WooCommerce Extended Coupon Features', 'woocommerce-jos-autocoupon' ); ?></h2>
        <form method="post" action="options.php"> 
        <?php 
            settings_fields( self::SETTINGS_PAGE );
            do_settings_sections( self::SETTINGS_PAGE );
            submit_button(); 
        ?>
        </form>
<?php        
    } 


    public function action_admin_init() {
        $page = self::SETTINGS_PAGE;

        register_setting( self::SETTINGS_PAGE, self::OPTION_NAME, array( $this, 'validate_settings' ) );

        do_action( 'wjecf_admin_before_settings' );
        
        // Section DEBUG
        add_settings_section(
            self::DOM_PREFIX . 'section_debug',
            __( 'Advanced settings', 'woocommerce-jos-autocoupon' ),
            array( &$this, 'render_section' ),
            $page
        );

        add_settings_field(
            self::DOM_PREFIX . 'debug_mode',
            __( 'Debug mode', 'woocommerce-jos-autocoupon' ),
            array( $this, 'render_setting_debug_mode' ),
            $page,
            self::DOM_PREFIX . 'section_debug'
        );          

        if ( WJECF()->get_option( 'debug_mode' ) === true ) {
            foreach ( WJECF()->get_plugins() as $name => $plugin ) {
                if ( $plugin->get_plugin_data('can_be_disabled') || WJECF()->get_option('debug_mode') ) {
                    add_settings_field(
                        self::DOM_PREFIX . 'plugin-' . $name,
                        $plugin->get_plugin_class_name(),
                        array( $this, 'render_setting_plugin' ),
                        $page,
                        self::DOM_PREFIX . 'section_debug', //section
                        array( 
                            'plugin' => $plugin,
                        ) //arguments
                    );
                }
            }
        } else {
            $disabled_plugins = WJECF()->get_option('disabled_plugins');
            if ( ! empty( $disabled_plugins ) ) {
                add_settings_field(
                    self::DOM_PREFIX . 'disabled_plugins',
                    __( 'Disabled plugins', 'woocommerce-jos-autocoupon' ),
                    array( $this, 'render_setting_disabled_plugins' ),
                    $page,
                    self::DOM_PREFIX . 'section_debug' //section
                );  
            }          
        }
        
        do_action( 'wjecf_admin_after_settings' );
        


    }

    public function validate_settings( $input ) {

        $options = WJECF()->get_options();

        //disabled_plugins
        if ( isset( $input['disabled_plugins'] ) && is_array( $input['disabled_plugins'] ) ) {
            foreach( $input['disabled_plugins'] as $class_name => $disabled ) {

                $plugin = WJECF()->get_plugin( $class_name );
                if ( $plugin === false ) {
                    continue; //unknown / invalid plugin
                }

                //Never disable those
                if ( ! $plugin->get_plugin_data('can_be_disabled') ) $disabled = 'no';

                //false if not found; otherwise the index
                $index = array_search( $class_name, $options['disabled_plugins'] );

                if ( $disabled == 'yes' && $index === false ) {
                    $options['disabled_plugins'][] = $class_name;
                }
                elseif ( $disabled == 'no' && $index !== false )
                {
                    unset( $options['disabled_plugins'][$index] );
                }
            }
        }

        //debug_mode
        $options['debug_mode'] = isset( $input['debug_mode'] ) && $input['debug_mode'] === 'yes';

        $options = apply_filters( 'wjecf_admin_validate_settings', $options, $input );
        return $options;

    }

    public function render_section( $section ) {
        switch ( $section['id'] ) {
            case self::DOM_PREFIX . 'section_debug':
                $body = __( 'When debug mode is enabled, extensive logging will be active and WJECF Plugins can be enabled/disabled. If there are compatibility issues with WooCommerce plugins, you can try disabling WJECF Plugins. Please don\'t keep debug mode enabled on a production environment when it is not necessary. Log can be found <a href="%s">here</a>.' , 'woocommerce-jos-autocoupon' );
                $body = sprintf( $body, admin_url( 'admin.php?page=wc-status&tab=logs' ) );
                printf( '<p>%s</p>', $body );
                break;
        }
    }

    /**
     * 
     * Renders the enable/disable checkbox for a plugin
     * 
     * Arguments: [ 'plugin' => WJECF_Plugin object ]
     * 
     * @param array $arguments 
     * @return void
     */
    public function render_setting_plugin( $arguments ) {
        //var_dump($arguments);

        $plugin = $arguments['plugin'];
        $plugin_name = $plugin->get_plugin_class_name();
        $plugin_disabled = $plugin->get_plugin_data('can_be_disabled') && in_array( $plugin_name, WJECF()->get_option( 'disabled_plugins' ) );

        //This field yields the value if the checbox is not checked
        //Don't draw this if plugin can't be disabled; as a disabled checkbox will not return a value
        $args = array(
            'type' => 'hidden',
            'id' => self::DOM_PREFIX . 'plugin-' . $plugin_name . '-enabled',
            'name' => sprintf( "%s[disabled_plugins][%s]", self::OPTION_NAME, $plugin_name ),
            'value' => $plugin->get_plugin_data('can_be_disabled') ? 'yes' : 'no' //yes = disabled
        );
        WJECF_Admin_Html::render_input( $args );

        $args = array(
            'type' => 'checkbox',
            'id' => self::DOM_PREFIX . 'plugin-' . $plugin_name . '-disabled',
            'name' => sprintf( "%s[disabled_plugins][%s]", self::OPTION_NAME, $plugin_name ),
            'value' => $plugin_disabled ? 'yes' : 'no',
            'cbvalue' => 'no' //no = enabled
        );
        if ( ! $plugin->get_plugin_data('can_be_disabled') ) $args['disabled'] = true;

        WJECF_Admin_Html::render_input( $args );

        WJECF_Admin_Html::render_tag( 
            'label', 
            array( "for" => esc_attr( $args['id'] ) ),
            __( 'Enabled', 'woocommerce' )
        );
        echo '<br>';

        printf( '<p><i>%s</i></p>', 
            $plugin->get_plugin_description()
        );

        //echo $plugin->get_plugin_description();
        //echo "</p>\n";
        //echo "</li>\n";
    }

    public function render_setting_disabled_plugins() {
        echo "<ul>";
        foreach( WJECF()->get_option( 'disabled_plugins' ) as $class_name ) {
            $plugin = WJECF()->get_plugin( $class_name );
            if ( $plugin ) {
                echo "<li><strong>" . $class_name . "</strong><br><i>" . $plugin->get_plugin_description() . "</i></li>";
            }
        }
        echo "</ul>";
    }    

    public function render_setting_debug_mode() {
        $option_name = 'debug_mode';
        $args = array( 
            'type' => 'checkbox',
            'id' => self::DOM_PREFIX . $option_name,
            'name' => sprintf( "%s[%s]", self::OPTION_NAME, $option_name ),
            'value' => WJECF()->get_option( $option_name ) ? 'yes' : 'no'
        );        
        WJECF_Admin_Html::render_input( $args );

        WJECF_Admin_Html::render_tag( 
            'label', 
            array( "for" => esc_attr( $args['id'] ) ),
            __( 'Enabled', 'woocommerce' )
        );

    }    

 
}