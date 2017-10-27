<?php if ( ! defined( 'ABSPATH' ) ) exit;
$plugin_dir_url = plugin_dir_url( __FILE__ );
?>


<style>
					
						.upgrade{background:#f4f4f9;padding: 50px 0; width:100%; clear: both;}
						.upgrade .upgrade-box{ background-color: #808a97;
							color: #fff;
							margin: 0 auto;
						   min-height: 110px;
							position: relative;
							width: 60%;}

						.upgrade .upgrade-box p{ font-size: 15px;
							 padding: 19px 20px;
							text-align: center;}

						.upgrade .upgrade-box a{background: none repeat scroll 0 0 #6cab3d;
							border-color: #ff643f;
							color: #fff;
							display: inline-block;
							font-size: 17px;
							left: 50%;
							margin-left: -150px;
							outline: medium none;
							padding: 11px 6px;
							position: absolute;
							text-align: center;
							text-decoration: none;
							top: 36%;
							width: 277px;}

						.upgrade .upgrade-box a:hover{background: none repeat scroll 0 0 #72b93c;}
                       
					   /**premium box**/    
						.premium-box{ width:100%; height:auto; background:#fff; float:left; }
						.premium-features{}
						.premium-heading{color:#484747;font-size: 40px; padding-top:35px;text-align:center;text-transform:uppercase;}
						.premium-features li{ width:100%; float:left;  padding: 80px 0; margin: 0; }
						.premium-features li .detail{ width:50%; }
						.premium-features li .img-box{ width:50%;box-sizing:border-box; }
						

						.premium-features li:nth-child(odd) { background:#f4f4f9; }
						.premium-features li:nth-child(odd) .detail{float:right; text-align:left; }
						.premium-features li:nth-child(odd) .detail .inner-detail{}
						.premium-features li:nth-child(odd) .detail p{ }
						.premium-features li:nth-child(odd) .img-box{ float:left; text-align:right; padding-right:30px;}

						.premium-features li:nth-child(even){  }
						.premium-features li:nth-child(even) .detail{ float:left; text-align:right;}
						.premium-features li:nth-child(even) .detail .inner-detail{ margin-right: 46px;}
						.premium-features li:nth-child(even) .detail p{ float:right;} 
						.premium-features li:nth-child(even) .img-box{ float:right;}

						.premium-features .detail{}
						.premium-features .detail h2{ color: #484747;  font-size: 24px; font-weight: 700; padding: 0; line-height:1.1;}
						.premium-features .detail p{  color: #484747;  font-size: 13px;  max-width: 327px;}
					 
					 /**images**/
					 
					 .pd_prm_option1 { background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option1.png") no-repeat; width:100%; max-width:496px; height:98px; display:inline-block; background-size:100% auto;}
					  
					 .prm_option2{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option2.png") no-repeat; width:100%;max-width:409px; height:82px; display:inline-block;  background-size:100% auto; }
					
                     .prm_option3{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option3.png") no-repeat; width:100%;max-width:452px;   height:118px; display:inline-block;background-size:100% auto;}

					 .prm_option4{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option4.png") no-repeat; width:100%;max-width:264px;  height:73px; display:inline-block;  background-size:100% auto;}					
					 .prm_option5{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option5.png") no-repeat; width:100%;max-width:658px; height:265px; display:inline-block; background-size:100% auto;}	
					 .prm_option6{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option6.png") no-repeat; width:100%; max-width:514px; height: 138px; display:inline-block; background-size:100% auto;}  					
					 .prm_option7{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option7.png") no-repeat; width:100%; max-width:670px; height: 864px; display:inline-block; background-size:100% auto;}  					
					 .prm_option8{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option8.png") no-repeat; width:100%; max-width:743px; height: 955px; display:inline-block; background-size:100% auto;}  					
					 .prm_option9{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option9.png") no-repeat; width:100%; max-width:689px; height: 346px; display:inline-block; background-size:100% auto;}  					
					 .prm_option10{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option10.png") no-repeat; width:100%; max-width:600px; height: 279px; display:inline-block; background-size:100% auto;}  					
				     .prm_option11{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option11.png") no-repeat; width:100%; max-width:395px; height: 462px; display:inline-block; background-size:100% auto;}
					 .prm_option12{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option12.png") no-repeat; width:100%; max-width:405px; height: 450px; display:inline-block; background-size:100% auto;}
					 .prm_option13{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option13.png") no-repeat; width:100%; max-width:323px; height: 70px; display:inline-block; background-size:100% auto;}					 
					 .prm_option14{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option14.png") no-repeat; width:100%; max-width:421px; height: 68px; display:inline-block; background-size:100% auto;}
					 .prm_option14_front{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option14-front.png") no-repeat; width:100%; max-width:270px; height: 133px; display:inline-block; background-size:100% auto;}
					 .prm_option15{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option15.png") no-repeat; width:100%; max-width:647px; height: 231px; display:inline-block; background-size:100% auto;}					 
					 .prm_option15_front{background:url("<?php echo plugin_dir_url( __FILE__ ); ?>../assets/images/prm_option15-front.png") no-repeat; width:100%; max-width:790px; height: 654px; display:inline-block; background-size:100% auto;}
                     
   
.premium-box .premium-box-head {
    background: #eae8e7 none repeat scroll 0 0;
    height: 500px;
    text-align: center;
    width: 100%;
}
.premium-box .premium-box-head .pho-upgrade-btn {
    display: block;
    text-align: center;
}

.premium-box .premium-box-head .pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}


.main-heading {
    background: #fff none repeat scroll 0 0;
    margin-bottom: -70px;
    text-align: center;
}
.main-heading img {
    margin-top: -200px;
}

.premium-box-container {
    margin: 0 auto;
}
.premium-box-container .description:nth-child(2n+1) {
    background: #fff none repeat scroll 0 0;
}
.premium-box-container .description {
    display: block;
    padding: 35px 0;
    text-align: center;
}

.premium-box-container .pho-desc-head::after {
    background: rgba(0, 0, 0, 0) url("imges/head-arrow.png") no-repeat scroll 0 0;
    content: "";
    height: 98px;
    position: absolute;
    right: -30px;
    top: -6px;
    width: 69px;
}
.premium-box-container .pho-desc-head {
    margin: 0 auto;
    position: relative;
    width: 768px;
}
.premium-box-container .description {
    text-align: center;
}

.pho-plugin-content p {
    color: #212121;
    font-size: 18px;
    line-height: 32px;
}

.premium-box-container .pho-desc-head h2 {
    color: #02c277;
    font-size: 28px;
    font-weight: bolder;
    margin: 0;
    text-transform: capitalize;
    line-height: 30px;
}

.premium-box-container .description:nth-child(2n+1) .pho-img-bg {
     background: #f1f1f1 url("<?php echo $plugin_dir_url; ?>../assets/images/image-frame-odd.png") no-repeat scroll 100% top;
}
.description .pho-plugin-content .pho-img-bg {
    border-radius: 5px 5px 0 0;
    height: auto;
    margin: 0 auto;
    padding: 70px 0 40px;
    width: 750px;
}

.premium-box-container .description:nth-child(2n) .pho-img-bg {
    background: #f1f1f1 url("<?php echo $plugin_dir_url; ?>../assets/images/image-frame-even.png") no-repeat scroll 100% top;
}

.premium-box-container .description:nth-child(2n) {
    background: #eae8e7 none repeat scroll 0 0;
}
 
 
.premium-box-container .pho-desc-head::after {
    background: rgba(0, 0, 0, 0) url("<?php echo $plugin_dir_url; ?>../assets/images/head-arrow.png") no-repeat scroll 0 0;
    content: "";
    height: 98px;
    position: absolute;
    right: -30px;
    top: 0;
    width: 69px;
} 

.pho-plugin-content {
    margin: 0 auto;
    overflow: hidden;
    width: 768px;
} 

.pho-upgrade-btn {
    display: block;
    text-align: center;
}


.pho-upgrade-btn a {
    display: inline-block;
    margin-top: 75px;
}
           
img {
	height: auto;
	max-width: 100%;
}

a:focus {
	box-shadow: none;
}
		   
				</style>
		<div class="premium-box">	
			<div class="premium-box-head">
					<div class="pho-upgrade-btn">
						<a href="https://www.phoeniixx.com/product/dynamic-pricing-and-discounts-for-woocommerce/" target="_blank"><img src="<?php echo $plugin_dir_url; ?>../assets/images/premium-btn.png" /></a>	
						<a target="blank" href="http://dynamicprice.phoeniixxdemo.com/wp-login.php?redirect_to=http%3A%2F%2Fdynamicprice.phoeniixxdemo.com%2Fwp-admin%2F&reauth=1"><img src="<?php echo $plugin_dir_url; ?>../assets/images/button2.png" /></a>
					</div>
			</div>
									
			<ul class="premium-features">
					<div class="main-heading"><h1><img src="<?php echo $plugin_dir_url; ?>../assets/images/premium-head.png" /></h1></div>
										
				<div class="premium-box-container">
	
						<div class="description">
							<div class="pho-desc-head"><h2>General settings of pricing rule</h2></div>
							<div class="pho-plugin-content">
								<p>
										  These are the general settings you could see in the pricing rule section. 
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/general-settings-of-pricing-rule.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Pricing method</h2></div>
							<div class="pho-plugin-content">
								<p>
										  There are 3 types of pricing method you can apply on the products. With this feature you can set any one of them. The 3 rules are - Quantity discount , Special offer and Exclude matched items. These methods will be shown in the Pricing rule section.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/Pricing-method.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Calender option</h2></div>
							<div class="pho-plugin-content">
								<p>
										  With this option you could set the date valid from / until on the discounts.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/calender-option.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Special offer pricing method</h2></div>
							<div class="pho-plugin-content">
								<p>
										  You could also set some special offer discount on your products. Like buy 3 items get 2 free. 
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/special-offer-pricing-method.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Exclude matched items</h2></div>
							<div class="pho-plugin-content">
								<p>
										  With this  option you could  exclude the products , user roles , capabilities and customers it means that the discount will not apply if that excluded products , user roles , capabilities and customers available in the cart and if the customer is logged in.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/Exclude-matched-items.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Conditions rules</h2></div>
							<div class="pho-plugin-content">
								<p>
										With this feature you could set the rules to apply on the discounts. 
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/conditions-rules.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Product and Category based</h2></div>
							<div class="pho-plugin-content">
								<p>
										  With this feature you can apply the discount on a particular product and on a particular category also. 
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/product-and-category-based.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Select specific Roles , Capabilities and Customers</h2></div>
							<div class="pho-plugin-content">
								<p>
										 This features lets you to select the user role , it's capabilities and customer's in which you can set the discount.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/roles-capabilities-and-customer-list.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Quantity Discount</h2></div>
							<div class="pho-plugin-content">
								<p>
										 With this feature you could set the range from Minimum quantity to Maximum quantity.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/minimum-and-maximum-quantity.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Products to adjust</h2></div>
							<div class="pho-plugin-content">
								<p>
										 This features lets you select the specific product in which you could set the discount.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/products-to-adjust-option.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Cart Rule Settings</h2></div>
							<div class="pho-plugin-content">
								<p>
										 These are the general settings of the Cart Rule section
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/cart-rule-settings.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Subtotal field condition in cart rule</h2></div>
							<div class="pho-plugin-content">
								<p>
										 This condition let you set the subtotal of cart to atleast or less than the value you set as per your choice.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/subtotal-field-in-cart rule.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Count of cart items</h2></div>
							<div class="pho-plugin-content">
								<p>
										 This option let you set the count of cart items to atleast or less than the value you set as per your choice.
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/count-of-cart-items.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Product based condition</h2></div>
							<div class="pho-plugin-content">
								<p>
										 This condition will let you set the discount on a particular product. 
										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/product-based-rule-in-cart.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Category based condition</h2></div>
							<div class="pho-plugin-content">
								<p>
										This condition will let you set the discount on a particular category.

										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/category-based-in-cart-rule.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
							<div class="description">
							<div class="pho-desc-head"><h2>User role based in cart rule</h2></div>
							<div class="pho-plugin-content">
								<p>
										By using this condition you could set either the user role or particular user.

										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/user-role-based-in-cart-rule.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Orders placed and Amount spent by user</h2></div>
							<div class="pho-plugin-content">
								<p>
										This condition will let you set the discount for the user who has placed the orders and the total amount spent by them.

										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/orders-placed-and-amount-spent-by-user-field.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Price & Percentage discount</h2></div>
							<div class="pho-plugin-content">
								<p>
										This option will set you either the Price or Percentage discount.

										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/price-percentage-discount-in-cart-rule.jpg" />
								</div>
							</div>
						</div><!-- description end -->
						
						<div class="description">
							<div class="pho-desc-head"><h2>Other rules to apply on all the conditions</h2></div>
							<div class="pho-plugin-content">
								<p>
									With this feature you could apply any rule to show the discount. There are 3 rules - Apply first matched rule , Apply biggest discount and Apply all matched rules. 

										</p>
								<div class="pho-img-bg">
									<img src="<?php echo $plugin_dir_url; ?>../assets/images/Other-rules-apply-on-all-conditions.jpg" />
								</div>
							</div>
						</div><!-- description end -->

				   
					<div class="pho-upgrade-btn">
						<a href="https://www.phoeniixx.com/product/dynamic-pricing-and-discounts-for-woocommerce/" target="_blank"><img src="<?php echo $plugin_dir_url; ?>../assets/images/premium-btn.png" /></a>
						<a target="blank" href="http://dynamicprice.phoeniixxdemo.com/wp-login.php?redirect_to=http%3A%2F%2Fdynamicprice.phoeniixxdemo.com%2Fwp-admin%2F&reauth=1"><img src="<?php echo $plugin_dir_url; ?>../assets/images/button2.png" /></a>
					</div>

				</div><!-- premium-box-container -->
										
									 
			</ul>
	
		</div>