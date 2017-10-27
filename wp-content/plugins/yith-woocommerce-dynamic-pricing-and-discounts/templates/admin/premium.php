<style>
    body{
        overflow-x: hidden;
    }

    .section{
        margin-left: -20px;
        margin-right: -20px;
        font-family: "Raleway",san-serif;
    }
    .section h1{
        text-align: center;
        text-transform: uppercase;
        color: #808a97;
        font-size: 35px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0 0;
    }
    .section ul{
        list-style-type: disc;
        padding-left: 15px;
    }
    .section:nth-child(even){
        background-color: #fff;
    }
    .section:nth-child(odd){
        background-color: #f1f1f1;
    }
    .section .section-title img{
        display: table-cell;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }
    .section h2,
    .section h3 {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
        color: #808a97;
        text-transform: uppercase;
    }

    .section .section-title h2{
        display: table-cell;
        vertical-align: middle;
        line-height: 25px;
    }

    .section-title{
        display: table;
    }

    .section h3 {
        font-size: 14px;
        line-height: 28px;
        margin-bottom: 0;
        display: block;
    }

    .section p{
        font-size: 13px;
        margin: 25px 0;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .landing-container{
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }
    .landing-container:after{
        display: block;
        clear: both;
        content: '';
    }
    .landing-container .col-1,
    .landing-container .col-2{
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }
    .landing-container .col-1 img{
        width: 100%;
    }
    .landing-container .col-1{
        width: 55%;
    }
    .landing-container .col-2{
        width: 45%;
    }
    .premium-cta{
        background-color: #808a97;
        color: #fff;
        border-radius: 6px;
        padding: 20px 15px;
    }
    .premium-cta:after{
        content: '';
        display: block;
        clear: both;
    }
    .premium-cta p{
        margin: 7px 0;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
        width: 60%;
    }
    .premium-cta a.button{
        border-radius: 6px;
        height: 60px;
        float: right;
        background: url(<?php echo YITH_YWDPD_URL?>assets/images/upgrade.png) #ff643f no-repeat 13px 13px;
        border-color: #ff643f;
        box-shadow: none;
        outline: none;
        color: #fff;
        position: relative;
        padding: 9px 50px 9px 70px;
    }
    .premium-cta a.button:hover,
    .premium-cta a.button:active,
    .premium-cta a.button:focus{
        color: #fff;
        background: url(<?php echo YITH_YWDPD_URL?>assets/images/upgrade.png) #971d00 no-repeat 13px 13px;
        border-color: #971d00;
        box-shadow: none;
        outline: none;
    }
    .premium-cta a.button:focus{
        top: 1px;
    }
    .premium-cta a.button span{
        line-height: 13px;
    }
    .premium-cta a.button .highlight{
        display: block;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }
    .premium-cta .highlight{
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

    @media (max-width: 768px) {
        .section{margin: 0}
        .premium-cta p{
            width: 100%;
        }
        .premium-cta{
            text-align: center;
        }
        .premium-cta a.button{
            float: none;
        }
    }

    @media (max-width: 480px){
        .wrap{
            margin-right: 0;
        }
        .section{
            margin: 0;
        }
        .landing-container .col-1,
        .landing-container .col-2{
            width: 100%;
            padding: 0 15px;
        }
        .section-odd .col-1 {
            float: left;
            margin-right: -100%;
        }
        .section-odd .col-2 {
            float: right;
            margin-top: 65%;
        }
    }

    @media (max-width: 320px){
        .premium-cta a.button{
            padding: 9px 20px 9px 70px;
        }

        .section .section-title img{
            display: none;
        }
    }
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Dynamic Pricing and Discounts%2$s to benefit from all features!','yith-woocommerce-dynamic-pricing-and-discounts'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-dynamic-pricing-and-discounts');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-dynamic-pricing-and-discounts');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/01-bg.png) no-repeat #fff; background-position: 85% 75%">
        <h1><?php _e('Premium Features','yith-woocommerce-dynamic-pricing-and-discounts');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/01.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('Cart discount','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php _e('Create one or more discount rules to be applied to the cart only if the users fulfill all the requirements you previously configured. ', 'yith-woocommerce-dynamic-pricing-and-discounts');?>
                </p>
                <p>
                    <?php echo sprintf(__('%1$sUser role, cart amount%2$s or %1$sselected products%2$s are only some of the parameters you can verify to grant or not the discount', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/02-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/02-icon.png" />
                    <h2><?php _e('Product discount','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Enjoy creating advantageous purchase conditions for your users by %1$sconfiguring product discount rules basing on the quantity%2$s. %3$sYou can offer a different product price, in an easy and intuitive way, depending on the quantity the user is going to purchase.', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/02.png" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/03-bg.png) no-repeat #fff; background-position: 85% 75%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/03.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/03-icon.png"/>
                    <h2><?php _e('Price table','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php _e('Have you configured different product discounts basing on the quantity?', 'yith-woocommerce-dynamic-pricing-and-discounts');?>
                </p>
                <p>
                    <?php echo sprintf(__('Thanks to the price table shown on the product page, you can %1$shighlight the complete list of the prices split by quantity.%2$s ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/04-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/04-icon.png" />
                    <h2><?php _e('Select the products','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('You can apply %1$scustom discounts to the products%2$s of your shop through the advanced management.%3$sFor each rule you created, you can choose on which products applying the discount and which products user must add to the cart to take advantage of the discount. ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/04.png" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/05-bg.png) no-repeat #fff; background-position: 85% 75%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/05.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/05-icon.png" />
                    <h2><?php _e('Select the users','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('A discount for all of the users or only happy few? %1$sMake your choice while creating a new rule%2$s. If you want to limit the offer only to some users, you can specify the email address or the user role. ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/06-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/06-icon.png" />
                    <h2><?php _e('Cumulative discount','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Does an offer exclude the others? Maybe, but it depends on your will.%3$s Specify %1$sif you want each discount rule to be applied in combination with others%2$s to let the user take advantage of further benefits on their orders.  ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/06.png" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/07-bg.png) no-repeat #fff; background-position: 85% 75%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/07.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/07-icon.png"/>
                    <h2><?php _e('Notes on product','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('After creating advantageous offers, it is %1$sgood to notify your users about them and increase their cart value%2$s. %3$s By enabling the option to show custom messages on the product page, the users will be informed of all the offers related to the selected product. ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>','<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/08-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/08-icon.png" />
                    <h2><?php _e('3 discount methods','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php _e('Three different ways to apply discounts to your products. ', 'yith-woocommerce-dynamic-pricing-and-discounts');?>
                </p>
                <ul>
                    <li><?php echo sprintf(__('%1$sPercentage:%2$s discount applied as percentage of the product price ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?></li>
                    <li><?php echo sprintf(__('%1$sAmount:%2$s deduction of the configured amount  ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?></li>
                    <li><?php echo sprintf(__('%1$sFixed value:%2$s regardless of how set on WooCommerce, the price of the products linked to the discount rule is the same as the specified value ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?></li>
                </ul>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/08.png" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/09-bg.png) no-repeat #fff; background-position: 85% 75%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/09.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/09-icon.png"/>
                    <h2><?php _e('Schedule the offers','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Plan your future discounts and set the time span to make them available. The automatic process will let you have a %1$svery dynamic shop%2$s appreciated by your users.', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/10-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/10-icon.png" />
                    <h2><?php _e('Price format','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php _e('The useful placeholders allow editing the format to show the product discounted prices as follows:', 'yith-woocommerce-dynamic-pricing-and-discounts');?>
                </p>
                <ul>
                    <li><?php _e('regular price', 'yith-woocommerce-dynamic-pricing-and-discounts');?></li>
                    <li><?php _e('discounted price', 'yith-woocommerce-dynamic-pricing-and-discounts');?></li>
                    <li><?php _e('discount percentage', 'yith-woocommerce-dynamic-pricing-and-discounts');?></li>
                </ul>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/10.png" />
            </div>
        </div>
    </div>
    <div class="section section-even clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/11-bg.png) no-repeat #fff; background-position: 85% 75%">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/11.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/11-icon.png"/>
                    <h2><?php _e('No discount on certain products','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php _e('Have you created your discounts but you want to apply exceptions? ', 'yith-woocommerce-dynamic-pricing-and-discounts');?>
                </p>
                <p>
                    <?php echo sprintf(__('Easy! %1$sYou can select all the products you want to exclude from the discount rules you previously configured.%2$s The price of this product won\'t be edited dynamically.  ', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear" style="background: url(<?php echo YITH_YWDPD_URL ?>assets/images/12-bg.png) no-repeat #f1f1f1; background-position: 15% 100%">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWDPD_URL ?>assets/images/12-icon.png" />
                    <h2><?php _e('Highlight the best price','yith-woocommerce-dynamic-pricing-and-discounts');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Have you set different price ranges basing on the purchase quantity? Show it off to your users to let them see the %1$smost convenient price%2$s.', 'yith-woocommerce-dynamic-pricing-and-discounts'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWDPD_URL ?>assets/images/12.png" />
            </div>
        </div>
    </div>

    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Dynamic Pricing and Discounts%2$s to benefit from all features!','yith-woocommerce-dynamic-pricing-and-discounts'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-dynamic-pricing-and-discounts');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-dynamic-pricing-and-discounts');?></span>
                </a>
            </div>
        </div>
    </div>
</div>