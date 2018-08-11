<?php 

if ( ! defined( 'WPINC' ) ) exit;

class Woo_Login_Redirector {

	public function __construct() {
		// if not enabled return early
		if ( get_option( 'woo_login_redirect_enable' ) !== 'yes' ) {
			return;
		}

		$this->init_hooks();
	}

	/**
	 * Init all hooks
	 * 
	 * @return void
	 */
	public function init_hooks() {
		add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ), 99, 2 );
		add_filter( 'woocommerce_registration_redirect', array( $this, 'register_redirect' ) );
	}

	/**
	 * Login redirect
	 * 
	 * @param  int $page
	 * 
	 * @return string
	 */
	public function login_redirect( $page, $user ) {
		$user_role = $this->get_current_user_role( $user );

		if ( empty( $user_role ) ) {
			return $page;
		}

	    $redire_page_id   = url_to_postid( $page );
	    $checkout_page_id = wc_get_page_id( 'checkout' );

	    if ( $redire_page_id == $checkout_page_id ) {
	    	return $page;
	    }

		if ( $user_role == 'customer' ) {
	    	return get_permalink( get_option( 'customer_login_redirect_page_id' ) );
		}

		if ( $user_role == 'shop_manager' ) {
			return get_permalink( get_option( 'shop_manager_login_redirect_page_id' ) );
		}

	}

	/**
	 * Registration redirect
	 * 
	 * @param  int $page
	 * 
	 * @return string
	 */
	public function register_redirect() {
		return get_permalink( get_option( 'reg_redirect_page_id' ) );
	}

	/**
	 * Get current user role
	 * 
	 * @return string
	 */
	public function get_current_user_role( $user ) {
		$user_roles = $user->roles;

		if ( ! in_array( 'customer', $user_roles ) && ! in_array( 'shop_manager', $user_roles ) ) {
			return false;
		}

		if ( in_array( 'customer', $user_roles ) ) {
			$user_role = 'customer';
		}

		if ( in_array( 'shop_manager', $user_roles ) ) {
			$user_role = 'shop_manager';
		}

		return $user_role;		
	}	

	public static function init() {
		$instance = false;

		if ( ! $instance ) {
			$instance = new static;
		}

		return $instance;
	}
}