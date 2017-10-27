<?php

defined('ABSPATH') or die();

class WJECF_Debug_CLI extends WP_CLI_Command {

    public static function add_command() {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            WP_CLI::add_command( 'wjecf', __CLASS__ );
        }
    }

    public function plugin_info() {
        WP_CLI::log( sprintf("WJECF Version: %s", WJECF()->plugin_version() ) );
        WP_CLI::log( sprintf("WJECF File: %s", WJECF()->plugin_file() ) );
        WP_CLI::log( sprintf("WJECF Url: %s", WJECF()->plugin_url() ) );

    }

    public function test_api( $args ) {
        require_once( 'pro/wjecf-pro-api-example.php' );

        if ( count( $args ) > 0 ) {
            $all = $args;
        } else {
            $all = WJECF_API()->get_all_auto_coupons();
        }

        foreach( $all as $coupon ) {
            $values = WJECF_API_Test_Coupon( $coupon );
            foreach( $values as $key => $value ) {
                WP_CLI::log( sprintf( "%s: %s", $key, print_r( $value, true ) ) );
            }
        }
    }

    /**
     * Test the sanitizers
     */
    public function test_sanitizer() {
        $array_tests = array(
            array(),
            array(0),
            array(1,0,2.0,"3")
        );
        foreach( $array_tests as $array ) {
            $comma = join( ',', $array );
            $ints_array = array_map( 'intval', $array);
            $ints_comma = join( ',', $ints_array );

            $this->single_test_sanitizer( $comma, 'int[]', $ints_array );
            $this->single_test_sanitizer( $array, 'int[]', $ints_array );

            $this->single_test_sanitizer( $comma, 'int,', $ints_comma );
            $this->single_test_sanitizer( $array, 'int,', $ints_comma );
        }

        $this->single_test_sanitizer( null, 'int[]', array() );

        $this->single_test_sanitizer( null, 'int,', '' );
        $this->single_test_sanitizer( '', 'int,', '' );

        $this->single_test_sanitizer( null, 'int', null );
        $this->single_test_sanitizer( '', 'int', null );
        $this->single_test_sanitizer( '1.234', 'int', 1 );

        $this->single_test_sanitizer( '1.234', 'decimal', '1.234' );
        $this->single_test_sanitizer( null, 'decimal', null );
    }

    private function single_test_sanitizer( $value, $rule, $expected ) {
        //$msg = sprintf( "Sanitized %s: %s to %s", $rule, print_r( $value, true ), print_r( $expected, true ) );
        $msg = sprintf( 
            "Sanitized %s: %s to %s", 
            $rule, $value === null ? 'null' : $value, 
            $expected === null ? 'null' : $expected
        );
        $this->assert( WJECF()->sanitizer()->sanitize( $value, $rule ) === $expected, $msg);

    }
    /**
     * Tests if the wrappers return the same values for WooCommerce 3.0 and older WC versions
     */
    public function test_wrappers() {

        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
        );            
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {
            $coupon = WJECF_WC()->get_coupon( $post->ID );
            $this->execute_test_for_coupon( $coupon );
        }

        $args = array(
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'asc',
            'post_type'        => array( 'product', 'product_variation' ),
            'post_status'      => 'publish',
        );    
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {
            $product = wc_get_product( $post->ID );
            $this->execute_test_for_product( $product );
        }

        $msg = sprintf("%d tests executed. Fails: %d  Passes: %d", $this->tests, $this->fails, $this->passes );
        if ( $this->fails != 0 )
            WP_CLI::error( $msg );
        else 
            WP_CLI::success( $msg );

    }

    protected function execute_test_for_coupon( $coupon ) {
        //WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->coupon_custom_fields ) ) );
        //WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->get_meta_data() ) ) );

        $meta_keys = array_keys( $coupon->coupon_custom_fields );
        $meta_keys[] = '__non_existing__';

        //WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->coupon_custom_fields ) ) );
        //WP_CLI::log( sprintf("Coupon fields: %s", print_r( $coupon->get_meta_data() ) ) );

        $wrap_leg = WJECF_WC()->wrap( $coupon, false ); $wrap_leg->use_wc27 = false;
        $wrap_new = WJECF_WC()->wrap( $coupon, false ); $wrap_new->use_wc27 = true;

        $results = array();
        $results['new'] = $wrap_new->get_id();
        $results['legacy'] = $wrap_leg->get_id();
        $results['old'] = $coupon->id;        
        $this->assert_same( $results, sprintf('Same coupon id %s', current( $results ) ) );

        foreach( $meta_keys as $meta_key ) {
            for($i=1; $i>=0; $i--) {
                $single = $i>0;

                $results = array();
                $results['new'] = $wrap_new->get_meta( $meta_key, $single );
                $results['legacy'] = $wrap_leg->get_meta( $meta_key, $single );
                $results['old'] = get_post_meta( $coupon->id, $meta_key, $single );
                $this->assert_same( $results, sprintf('%s: Same value %s', $meta_key, $single ? 'single' : 'multi' ) );

            }
        }
    }

    protected function execute_test_for_product( $product ) {
        $wrap_leg = WJECF_WC()->wrap( $product, false ); $wrap_leg->use_wc27 = false;
        $wrap_new = WJECF_WC()->wrap( $product, false ); $wrap_new->use_wc27 = true;

        if ($product instanceof WC_Product_Variation) {
            $results = array();
            $results['new'] = $wrap_new->get_product_or_variation_id();
            $results['legacy'] = $wrap_leg->get_product_or_variation_id();
            $results['old'] = $product->variation_id;
            $this->assert_same( $results, sprintf('Same variation id %s', current( $results ) ) );

            $results = array();
            $results['new'] = $wrap_new->get_variable_product_id();
            $results['legacy'] = $wrap_leg->get_variable_product_id();
            $results['old'] = $wrap_leg->get_variable_product_id();
            $this->assert_same( $results, sprintf('Same variable product (parent) id %s', current( $results ) ) );            
        } else {
            $results = array();
            $results['new'] = $wrap_new->get_id();
            $results['legacy'] = $wrap_leg->get_id();
            $results['old'] = $product->id;
            $this->assert_same( $results, sprintf('Same product id %s', current( $results ) ) );
        }

        $meta_keys = array( 'total_sales', '_price', '__non_existing__');
        foreach( $meta_keys as $meta_key ) {
            for($i=1; $i>=0; $i--) {
                $single = $i>0;

                $results = array();
                $results['new'] = $wrap_new->get_field( $meta_key, $single );
                $results['legacy'] = $wrap_leg->get_field( $meta_key, $single );
                $results['old'] = get_post_meta(  $wrap_new->get_product_or_variation_id(), $meta_key, $single );
                $this->assert_same( $results, sprintf('%s: Same value %s. %s', $meta_key, $single ? 'single' : 'multi', $this->dd( current( $results ) ) ) );

            }
        }




    }    

    protected $tests = 0;
    protected $fails = 0;
    protected $passess = 0;

    protected function assert( $true, $test_description ) {
        if ( $true !== true ) {
            WP_CLI::error( $test_description );
            die();
        }
        WP_CLI::success( $test_description );
    }

    protected function assert_same( $results, $test_description ) {
        $success = true;
        foreach( $results as $result ) {
            if ( isset( $prev_result ) && $result !== $prev_result ) {
                $success = false;
                break;
            }
            $prev_result = $result;
        }

        $this->tests++;

        if ( $success ) {
            $this->passes++;
            WP_CLI::success( $test_description );
        } else {
            $this->fails++;
            foreach( $results as $key => $result ) {
                WP_CLI::log( sprintf("%s : %s", $key, $this->dd( $result ) ) );
            }
            WP_CLI::error( $test_description );
        }
    }

    protected function dd( $variable ) {
        return print_r( $variable, true );
    }

}