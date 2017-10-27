<?php

if ( ! function_exists( 'wpclever_add_dashboard_widgets' ) ) {
	add_action( 'wp_dashboard_setup', 'wpclever_add_dashboard_widgets' );

	function wpclever_widget_function( $post, $callback_args ) {
		$args     = (object) array( 'author' => 'wpclever', 'per_page' => '20', 'page' => '1' );
		$request  = array( 'action' => 'query_plugins', 'timeout' => 15, 'request' => serialize( $args ) );
		$url      = 'http://api.wordpress.org/plugins/info/1.0/';
		$response = wp_remote_post( $url, array( 'body' => $request ) );
		$plugins  = unserialize( $response['body'] );
		if ( isset( $plugins->plugins ) && ( count( $plugins->plugins ) > 0 ) ) {
			foreach ( $plugins->plugins as $pl ) {
				echo '<div class="item"><a href="https://wordpress.org/plugins/' . $pl->slug . '/"><img src="https://ps.w.org/' . $pl->slug . '/assets/icon-128x128.png"/><span class="title">' . $pl->name . '</span><br/><span class="info">Version ' . $pl->version . '</span></a></div>';
			}
		}
	}

	function wpclever_add_dashboard_widgets() {
		wp_add_dashboard_widget( 'wpclever_dashboard_widget', 'WPclever.net Plugins', 'wpclever_widget_function' );
	}
}