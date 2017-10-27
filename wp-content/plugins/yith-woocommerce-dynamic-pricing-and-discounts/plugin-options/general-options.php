<?php

$settings = array(

    'general' => array(

        'header'    => array(

            array(
                'name' => __( 'General Settings', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
                'type' => 'title'
            ),

            array( 'type' => 'close' )
        ),


        'settings' => array(

            array( 'type' => 'open' ),

            array(
                'id'      => 'enabled',
                'name'    => __( 'Enable Dynamic Pricing and Discounts', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
                'desc'    => '',
                'type'    => 'on-off',
                'std'     => 'yes'
            ),

            array(
                'id'      => 'pricing-rules',
                'name'    => __( 'Add a new rule for pricing', 'yith-woocommerce-dynamic-pricing-and-discounts' ),
                'desc'    => '',
                'type'    => 'options-pricing-rules',
                'deps'    => array(
                    'ids'       => 'yith-ywdpd-enable',
                    'values'    => 'yes'
                )
            ),

            array( 'type' => 'close' ),
        )
    )
);

return apply_filters( 'yith_ywdpd_panel_settings_options', $settings );