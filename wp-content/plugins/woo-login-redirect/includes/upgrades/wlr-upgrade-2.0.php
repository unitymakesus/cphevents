<?php 
function update_option_key() {
	$old_login_option = get_option( 'login_redirect_page_id' );

	if ( ! empty( $old_login_option ) ) {
		update_option( 'woo_login_redirect_enable', 'yes' );
		update_option( 'customer_login_redirect_page_id', $old_login_option );
		update_option( 'shop_manager_login_redirect_page_id', $old_login_option );
	}
}

update_option_key();