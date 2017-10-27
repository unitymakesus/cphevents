<?php

class WJECF_Admin_Data_Update extends Abstract_WJECF_Plugin {
    
    public function __construct() {    
        $this->set_plugin_data( array(
            'description' => __( 'Automatically update data when a new version of this plugin is installed.', 'woocommerce-jos-autocoupon' ),
            'dependencies' => array(),
            'can_be_disabled' => true
        ) );        
    }

    public function init_admin_hook() {
        $this->auto_data_update();
    }

    //Upgrade database on version change
    public function auto_data_update() {
        if ( ! class_exists('WC_Coupon') ) {
            return;
        }
        
        //WJECF()->set_option('db_version', 0); // Will force all upgrades
        global $wpdb;
        $prev_version = WJECF()->get_option('db_version');        
        $current_version = $prev_version;

        //DB_VERSION 1: Since 2.1.0-b5
        if ( $current_version < 1 ) {
            //RENAME meta_key _wjecf_matching_product_qty TO _wjecf_min_matching_product_qty
            $where = array("meta_key" => "_wjecf_matching_product_qty");
            $set = array('meta_key' => "_wjecf_min_matching_product_qty");
            $wpdb->update( _get_meta_table('post'), $set, $where );
            
            //RENAME meta_key woocommerce-jos-autocoupon TO _wjecf_is_auto_coupon
            $where = array("meta_key" => "woocommerce-jos-autocoupon");
            $set = array('meta_key' => "_wjecf_is_auto_coupon");
            $wpdb->update( _get_meta_table('post'), $set, $where );                
            //Now we're version 1
            $current_version = 1;
        } //DB VERSION 1

        //DB_VERSION 2: Since 2.3.3-b3 No changes; but used to omit message if 2.3.3-b3 has been installed before
        if ( $current_version < 2 ) {
            $current_version = 2;
        }

        if ( $current_version > 2 ) {
            WJECF_ADMIN()->enqueue_notice( '<p>' . __( '<strong>WooCommerce Extended Coupon Features:</strong> Please note, you\'re using an older version of this plugin, while the data was upgraded to a newer version.' , 'woocommerce-jos-autocoupon' ) . '</p>', 'notice-warning');
        }

        //An upgrade took place?
        if ( $current_version != $prev_version ) {    
            // Set version and write options to database
            WJECF()->set_option( 'db_version', $current_version );
            WJECF()->save_options();

            WJECF_ADMIN()->enqueue_notice( '<p>' . __( '<strong>WooCommerce Extended Coupon Features:</strong> Data succesfully upgraded to the newest version.', 'woocommerce-jos-autocoupon' ) . '</p>', 'notice-success');
        }        
    }

    // function admin_notice_allow_cart_excluded() {

    //     if( ! empty( $this->admin_notice_allow_cart_excluded_couponcodes ) )
    //     {
    //         $html = '<div class="notice notice-warning">';
    //         $html .= '<p>';
    //         $html .= __( '<strong>WooCommerce Extended Coupon Features:</strong> The following coupons use the deprecated option \'Allow discount on cart with excluded items\'. Please review and save them.', 'woocommerce-jos-autocoupon' );
    //         $html .= '<ul>';
    //         foreach( $this->admin_notice_allow_cart_excluded_couponcodes as $post_id => $coupon_code ) {  
    //             $html .= '<li><a class="post-edit-link" href="' . esc_url( get_edit_post_link( $post_id ) ) . '">' . $coupon_code . '</a></li>';
    //         }
    //         $html .= '</ul>';            
    //         $html .= '</p>';
    //         $html .= '</div>';
    //         echo $html;
    //     }
    // }

}


?>