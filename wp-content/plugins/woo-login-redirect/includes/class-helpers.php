<?php

class WLR_Helper {
    /**
     * Get user roles
     *
     * @return void
     */
    public static function get_user_roles() {
        $roles       = array_keys( wp_roles()->roles );
        $valid_roles = apply_filters( 'wlr_get_valid_roles', array( 'editor', 'shop_manager', 'customer', 'subscriber' ) );

        $result = array_filter( $roles, function( $role ) use ( $valid_roles ) {
            return in_array( $role, $valid_roles );
        });

        return $result;
    }

    /**
     * Get all the page id with title
     *
     * @return array
     */
    public static function get_pages() {
        $page_ids = get_all_page_ids();

        $result = array_map( function( $id ) {
            return array(
                'id'    => $id,
                'title' => get_the_title( $id )
            );
        }, $page_ids );

        return $result;
    }

    /**
     * Get current user role
     *
     * @return string
     */
    public static function get_current_user_role( $user ) {
        if ( ! is_object( $user) ) {
            $user = new WP_User( $user );
        }

        if ( ! $user ) {
            return false;
        }

        return isset( $user->roles[0] ) ? $user->roles[0] : false;
    }

    /**
     * Check if pro version is installed
     *
     * @return boolean
     */
    public static function is_pro_installed() {
        return class_exists( 'WLR_Pro' );
    }
}