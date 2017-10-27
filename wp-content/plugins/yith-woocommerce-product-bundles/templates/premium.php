<style>
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
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
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
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
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

.section.one{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/02-bg.png) no-repeat; background-position: 15% 75%
}
.section.three{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/04-bg.png) no-repeat; background-position: 15% 75%
}
.section.five{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/06-bg.png) no-repeat; background-position: 15% 75%
}
.section.seven{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/07-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.eight{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/08-bg.png) no-repeat; background-position: 15% 75%
}
.section.nine{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/09-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.ten{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/10-bg.png) no-repeat; background-position: 15% 75%
}
.section.eleven{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/11-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.twelve{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/12-bg.png) no-repeat; background-position: 15% 75%
}
.section.thirteen{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/13-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.fourteen{
    background: url(<?php echo YITH_WCPB_ASSETS_URL?>/images/14-bg.png) no-repeat; background-position: 15% 75%
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
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Product Bundles%2$s to benefit from all features!','yith-woocommerce-product-bundles'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-product-bundles');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-product-bundles');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-product-bundles');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/01.png" alt="Bundle price" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('Bundle price','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Two different pricing ways for the product bundles of your shop.%3$sAssign a %1$sfixed price%2$s if you want to sell your bundle at a specific price, regardless the costs and the quantity of the included products. Furthermore, if you want, you can also make the %1$sprice dynamic%2$s, summing the costs of the products of the bundle.', 'yith-woocommerce-product-bundles'), '<b>', '</b>','<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e('Variable products','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Don\'t just restrict yourself to the simple products of your shop: purchase the premium version of the plugin and even the variable products will be includable in your product bundles. %1$sEvery single variation can be selected and offered to your users.%2$s', 'yith-woocommerce-product-bundles'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/02.png" alt="Variable products" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/03.png" alt="Shipping" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Shipping fees','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php _e('Shipping fees regard administrator management, so you will be free to assign them to the single bundle, or to each included element.', 'yith-woocommerce-product-bundles');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('Hide the product','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php _e('Use this option to hide some of the products of a bundle. With just one click, you will solve your problem and your users won\'t see them anymore.', 'yith-woocommerce-product-bundles');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/04.png" alt="Hide the product" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/05.png" alt="Product quantity" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/05-icon.png" alt="icon 05" />
                    <h2><?php _e('Product quantity','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Set a %1$sminimum%2$s and %1$smaximum%2$s quantity for each product added in a bundle: only in this way you will be able to force your users to purchase a product in the amount you want, so that they can benefit from the whole bundle.','yith-woocommerce-product-bundles'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/06-icon.png" alt="icon 06" />
                    <h2><?php _e('Title and description','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Do you want to change names and descriptions of the products included in a bundle? This feature is what you are looking for.%3$sAdd the product to the bundle and change its title and description: %1$sadd something unique and alternative to the original product once in a bundle.%2$s','yith-woocommerce-product-bundles' ),'<b>','</b>','<br>' ) ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/06.png" alt="Title and description" />
            </div>
        </div>
    </div>
    <div class="seven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/07.png" alt="Discount" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/07-icon.png" alt="icon 07" />
                    <h2><?php _e('Discount','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php _e ('Do you want to offer a deal to your users? Apply a discount to the products of the bundle, so that they will keep their original price in the shop and, have a special price in your promotional bundle.','yith-woocommerce-product-bundles'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="eight section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/08-icon.png" alt="icon 08" />
                    <h2><?php _e('Optional product','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( '%1$sEach product of a bundle can be selected as "optional"%2$s. This means that it will be available to your users, but it won\'t be necessary to purchase the product bundle.','yith-woocommerce-product-bundles' ),'<b>','</b>' ) ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/08.png" alt="Optional product" />
            </div>
        </div>
    </div>
    <div class="nine section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/09.png" alt="Widget" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/09-icon.png" alt="icon 09" />
                    <h2><?php _e('Widget','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Put the bundles you have created for your shop in the spotlight. With the %1$s"YITH WooCommerce Product Bundle"%2$s widget, you will be free to add the complete list of the bundles in the sidebars of your e-commerce site, so that your users can always see them','yith-woocommerce-product-bundles'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="ten section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/10-icon.png" alt="icon 10" />
                    <h2><?php _e('Hide the image','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php _e( 'Users can see name, image and description of every product of a bundle. If you want to hide the images of some products, use the related option and the product will be showed as you like.','yith-woocommerce-product-bundles' ); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/10.png" alt="Hide the image" />
            </div>
        </div>
    </div>
    <div class="eleven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/11.png" alt="Widget" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/11-icon.png" alt="icon 11" />
                    <h2><?php _e('Report management','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Decide whether to show only "bundle" products or even those included: a simple click on the option, and your %1$sstatistics%2$s will have the shape you want.','yith-woocommerce-product-bundles'),'<b>','</b>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="twelve section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/12-icon.png" alt="icon 12" />
                    <h2><?php _e('Items quantity','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php _e( 'Would you like to offer the purchase of a bundle only if it includes at least five items? Well, now you can!','yith-woocommerce-product-bundles' ); ?>
                </p>
                <?php _e( 'Set a minimum and/or maximum quantity for the required items%2$s in order to deny the purchase of the bundle if the number of the items added is lower or higher than the one you specified.','yith-woocommerce-product-bundles' ); ?>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/12.png" alt="Hide the image" />
            </div>
        </div>
    </div>
    <div class="thirteen section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/13.png" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/13-icon.png" alt="icon 13" />
                    <h2><?php _e('Out of stock bundle','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Users %1$scan\'t purchase%2$s the Bundle product if one of the products included is not available. %3$s Now you can %1$ssynchronize the availability status%2$s of the bundle with the products in it included. If at least one of the items is not available, the Bundle product is automatically set as Out-of-stock. ','yith-woocommerce-product-bundles'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="fourteen section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/14-icon.png" />
                    <h2><?php _e('"Per item pricing" bundles','yith-woocommerce-product-bundles');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Now you can show the bundle product "per item pricing" price in the %1$sorder details%2$s or %1$sinvoice%2$s in two different ways: choose to show the total value of the bundle or the value of each single item in it included.','yith-woocommerce-product-bundles'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCPB_ASSETS_URL?>/images/14.png" />
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Product Bundles%2$s to benefit from all features!','yith-woocommerce-product-bundles'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-product-bundles');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-product-bundles');?></span>
                </a>
            </div>
        </div>
    </div>
</div>