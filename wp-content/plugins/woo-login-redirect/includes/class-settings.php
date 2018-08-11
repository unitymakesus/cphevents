<?php

if ( ! defined( 'WPINC' ) ) exit;

class Woo_Login_Redirect_Settings {
	/**
	 * Constructor method
	 */
	public function __construct() {
		$this->init_hooks();
	}

	public function init_hooks() {
        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'register_tab' ), 99 );
        add_action( 'woocommerce_settings_login_redirect', array( $this, 'woo_login_redirect_settings' ) );
        add_action( 'woocommerce_update_options_login_redirect', array( $this, 'save_settings' ) );
	}

    /**
     * Regeister the settings feilds
     *
     * @since 1.0
     *
     * @return void;
     */
    public function woo_login_redirect_settings() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    /**
     * Get all the settings fields
     *
     * @return array
     */
    public function get_settings() {
        $settings = array(
            'section_title' => array(
                'name' => __( 'Login Redirect Settings', 'wlr' ),
                'type' => 'section',
                'desc' => 'des',
                'id'   => 'woo_login_reg_redirect_section'
            ),           
            array(
                'title'     => __( 'Woo Login Redirect Settings', 'wlr' ),
                'id'        => 'woo_login_redirect_settings',
                'type'      => 'title',
            ),
            array(
                'title'     => __( 'Enable', 'wlr' ),
                'desc'      => __( 'Enable Login Redirect.', 'wlr' ),
                'id'        => 'woo_login_redirect_enable',
                'type'      => 'checkbox',
                'options'   => array( 'yes' ),
                'desc_tip'  => true,
                'default'   => 'yes'
            ),             
            array(
                'type'   => 'sectionend',
                'id' => 'woo_login_reg_redirect_section'
            ),

            'section_title' => array(
                'name' => __( 'Login Redirect Customer Section', 'wlr' ),
                'type' => 'section',
                'desc' => 'des',
                'id'   => 'woo_login_redirect_section'
            ),           
            array(
                'title'		=> __( 'Woo Login Redirect', 'wlr' ),
                'id'        => 'woo_login_redirect_customer_settings',
                'type'      => 'title',
            ),
            array(
                'title'    => __( 'Customer Login Redirect', 'woocommerce' ),
                'desc'     => __( 'Redirect customer after login', 'woocommerce' ),
                'id'       => 'customer_login_redirect_page_id',
                'type'     => 'single_select_page',
                'default'  => '',
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'title'    => __( 'Shop Manager Login Redirect', 'woocommerce' ),
                'desc'     => __( 'Redirect shop manager after registration', 'woocommerce' ),
                'id'       => 'shop_manager_login_redirect_page_id',
                'type'     => 'single_select_page',
                'default'  => '',
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'type'   => 'sectionend',
                'id' => 'woo_login_redirect_section'
            ),

            array(
                'title'     => __( 'Woo Registration Redirect', 'wlr' ),
                'id'        => 'woo_reg_redirect_settings',
                'type'      => 'title',
            ),
            'section_title' => array(
                'name' => __( 'Registration Redirect Section', 'wlr' ),
                'type' => 'section',
                'desc' => 'des',
                'id'   => 'woo_reg_redirect_section'
            ),
            array(
                'title'    => __( 'Registration Redirect', 'woocommerce' ),
                'desc'     => __( 'Redirect user after registration', 'woocommerce' ),
                'id'       => 'reg_redirect_page_id',
                'type'     => 'single_select_page',
                'default'  => '',
                'class'    => 'wc-enhanced-select',
                'css'      => 'min-width:300px;',
                'desc_tip' => true
            ),
            array(
                'type'   => 'sectionend',
                'id' => 'woo_reg_redirect_section'
            ),
        );

        return apply_filters( 'woo_login_redirect_settings', $settings );
    }

    /**
     * Save the settings
     *
     * @return void;
     */
    public function save_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    /**
     * Register the login redirect tab
     *
     * @param $tabs
     *
     * @return mixed
     */
    public function register_tab( $tabs ) {
        $tabs['login_redirect'] = __( 'Login Redirect', 'wlr' );

        return $tabs;
    }	

	public static function init() {
		$instance = false;

		if ( ! $instance ) {
			$instance = new static;
		}

		return $instance;
	}
}