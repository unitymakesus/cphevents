<?php 

/*
** Plugin Name:Dynamic Pricing And Discounts For Woocommerce

** Plugin URI: http://www.phoeniixx.com/

** Description: It is a plugin which helps you to set up product based bulk discounts based on the quantity. 

** Version: 1.3.2

** Author: Phoeniixx

** Text Domain:phoen-dpad

** Author URI: http://www.phoeniixx.com/

** License: GPLv2 or later

** License URI: http://www.gnu.org/licenses/gpl-2.0.html

**/  

if ( ! defined( 'ABSPATH' ) ) exit;
	
		
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
			$gen_settings = get_option('phoe_disc_value');
			
			$enable_disc=isset($gen_settings['enable_disc'])?$gen_settings['enable_disc']:'';
			
			define('PHOEN_DPADPLUGURL',plugins_url(  "/", __FILE__));
	
			define('PHOEN_DPADPLUGDIRPATH',plugin_dir_path(  __FILE__));
				
		

		function phoe_dpad_menu_disc() {
			
			add_menu_page('Phoeniixx_Discounts',__( 'Discounts', 'phoeniixx_woocommerce_discount' ) ,'nosuchcapability','Phoeniixx_Discounts',NULL, PHOEN_DPADPLUGURL.'assets/images/aa2.png' ,'57.1');
		
			add_submenu_page( 'Phoeniixx_Discounts', 'Phoeniixx_Disc_settings', 'Settings','manage_options', 'Phoeniixx_Disc_settings',  'Phoen_dpad_settings_func' );
	
		}
		
		add_action('admin_menu', 'phoe_dpad_menu_disc');
		
		
		function Phoen_dpad_settings_func()  {
			 
			$gen_settings = get_option('phoe_disc_value');
			 
			$enable_disc=isset($gen_settings['enable_disc'])?$gen_settings['enable_disc']:'';

				?>
			
			<div id="profile-page" class="wrap">
		
				<?php
					
				if(isset($_GET['tab']))
						
				{
					$tab = sanitize_text_field( $_GET['tab'] );
					
				}
				
				else
					
				{
					
					$tab="";
					
				}
				
				?>
				<h2> <?php _e('Settings','phoen-dpad'); ?></h2>
				
				<?php $tab = (isset($_GET['tab']))?$_GET['tab']:'';?>
				
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				
					
					<a class="nav-tab <?php if($tab == 'phoeniixx_setting' ){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=Phoeniixx_Disc_settings&amp;tab=phoeniixx_setting"><?php _e('setting','phoen-dpad'); ?></a>
					
					<a class="nav-tab <?php if($tab == 'phoeniixx_premium' ){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=Phoeniixx_Disc_settings&amp;tab=phoeniixx_premium"><?php _e('Premium','phoen-dpad'); ?></a>				
					
				</h2>
				
			</div>
			
			<?php
			
			if($tab=='phoeniixx_setting'|| $tab == '' )
			{
				
				include_once(PHOEN_DPADPLUGDIRPATH.'includes/phoeniixx_discount_pagesetting.php');
										
			}elseif($tab=='phoeniixx_premium'){
				
				 include_once(PHOEN_DPADPLUGDIRPATH.'includes/phoen_premium_setting.php'); 
			} 
			
		}
		
		register_activation_hook( __FILE__, 'phoe_dpad_activation_func');

		function phoe_dpad_activation_func()
		{
			
			$phoe_disc_value = array(
				
				'enable_disc'=>1,
				'coupon_disc'=>1
			
				);
				
			update_option('phoe_disc_value',$phoe_disc_value);
			
		}
		
		if($enable_disc=="1") 	{
			
			include_once(PHOEN_DPADPLUGDIRPATH.'includes/phoeniixx_discount_productplugin.php');
		
		}
	
		function phoen_dpad_calculate_extra_fee( $cart_object ) {
		
			$gen_settings = get_option('phoe_disc_value');
			
			$coupon_disc=isset($gen_settings['coupon_disc'])?$gen_settings['coupon_disc']:'';
			
			 if(($coupon_disc==1)&&($cart_object->applied_coupons[0]!=""))
			{  }
			else 
			{
				$old_price=$value['data']->price;
			
				foreach ( $cart_object->cart_contents as $key => $value ) {  

					$val= get_post_meta($value['product_id'],'phoen_woocommerce_discount_mode', true); 
				
					for($i=0;$i<count($val);$i++) 	{
						
						$quantity = intval( $value['quantity'] );
				   
						$orgPrice = intval( $value['data']->price );
							 
						$phoen_minval=isset($val[$i]['min_val'])?$val[$i]['min_val']:"";
						
						$phoen_maxval=isset($val[$i]['max_val'])?$val[$i]['max_val']:"";
						
						if(($quantity>= $phoen_minval)&&($quantity<=$phoen_maxval))  {
								
							$type=isset($val[$i]['type'])?$val[$i]['type']:'';
								
							if($type=='percentage') {
								
								  $percent=(100-$val[$i]['discount'])/100 ;
								  
								 $pp=$value['data']->price ;
								
								  $new_prc_value =  $value['data']->price *=$percent;
								   
								   $value['data']->set_price($new_prc_value);      
								
								break;
							}else{
								
								$new_prc_value =  $value['data']->price-=$val[$i]['discount'];
								 
								   $value['data']->set_price($new_prc_value); 
								 
								break;
								
							}
						}
					
					}
				}
			
			}
		}
		
		
		function phoen_dpad_filter_item_price( $price, $values ) {
			  		
			global $woocommerce;

			$new_prod_val=get_post_meta( $values['product_id']);
			
			$ret_val="0";
			
				$val= get_post_meta($values['product_id'],'phoen_woocommerce_discount_mode', true); 
				
					 $quantity = intval( $values['quantity'] );
					for($i=0;$i<count($val);$i++) 	{
												 
						$phoen_minval=isset($val[$i]['min_val'])?$val[$i]['min_val']:"";
						
						$phoen_maxval=isset($val[$i]['max_val'])?$val[$i]['max_val']:"";
						
						if(($quantity>= $phoen_minval)&&($quantity<=$phoen_maxval))  {
								
							 $ret_val=1;
							
							}
							
						}
						
			$curr=get_woocommerce_currency_symbol();
			
			$old_price1="";
			
			$old_price="";
			
			global $product;
							
			$plan = wc_get_product($values['product_id']);
			
			$name=get_post($values['product_id'] );
			
			$_product = get_product( $values['product_id'] );

			if ( $_product && $_product instanceof WC_Product_Variable && $values['variation_id'] )
			{
				$variations = $plan->get_available_variation($values['variation_id']);

				if($variations['display_regular_price']!='')
				{
					
				  $old_price1=$curr.$variations['display_regular_price'];
					
				}	
							
					if($variations['display_price']!='')
				{
				
					  $old_price1=$curr.$variations['display_price'];
					
				}	 
			}
			else
			{
				if($new_prod_val['_regular_price'][0]!='')
				{
					 $old_price1=$curr.$new_prod_val['_regular_price'][0];
					
				}
				if($new_prod_val['_sale_price'][0]!='')
				{
					 $old_price1=$curr.$new_prod_val['_sale_price'][0];
					
				}
	
			}
			$gen_settings = get_option('phoe_disc_value');
			
			$coupon_disc=isset($gen_settings['coupon_disc'])?$gen_settings['coupon_disc']:'';
			
			
		
		
			if((($coupon_disc==1)&&(!( empty( $woocommerce->cart->applied_coupons ))))||($ret_val==0))
			{
				return "<span class='discount-info' title=''>" .
					"<span class='old-price' >$old_price1</span></span>";
				
			}
			else{
			
					return "<span class='discount-info' title=''>" .
					"<span class='old-price' style='color:red; text-decoration:line-through;'>$old_price1</span> " .
					"<span class='new-price' > $price</span></span>";
			
		
		}
		}

		function phoen_dpad_filter_subtotal_price( $price, $values ) {
			
			global $woocommerce;

			$amt='';
			
			$type_curr='';
			
			$curr=get_woocommerce_currency_symbol();
			
			$val= get_post_meta($values['product_id'],'phoen_woocommerce_discount_mode', true); 
			
			$gen_settings = get_option('phoe_disc_value');
			
			$coupon_disc=isset($gen_settings['coupon_disc'])?$gen_settings['coupon_disc']:'';
			
			for($i=0;$i<count($val);$i++) 	{
						
				$quantity = intval( $values['quantity'] );
			   $phoen_minval=isset($val[$i]['min_val'])?$val[$i]['min_val']:"";
				$phoen_maxval=isset($val[$i]['max_val'])?$val[$i]['max_val']:"";
				if(($quantity>=$phoen_minval)&&($quantity<=$phoen_maxval))  {
						
					$amt=isset($val[$i]['discount'])?$val[$i]['discount']:'';
							
					$type=isset($val[$i]['type'])?$val[$i]['type']:'';
							
					if($type=='percentage') {
						
						$type_curr="[".$amt."% Discount]";
						break;
					}
							
					else{
							
						$type_curr="[". $curr.$amt." Discount on each Product]";	
						break;									
					}
				}
						
			}
				
			if(($coupon_disc==1)&&(!( empty( $woocommerce->cart->applied_coupons ))))
				{
					return "<span class='discount-info' title='$type_curr'>" .
					"<span>$price</span></span>";
					
				}
			else{
				
					return "<span class='discount-info' title='$type_curr'>" .
					"<span>$price</span>" .
					"<span class='new-price' style='color:red;'> $type_curr</span></span>";

				}
		}
		


		function phoen_dpad_filter_subtotal_order_price( $price, $values, $order )
		{
			
			global $woocommerce;

			$amt='';
			
			$type_curr='';
			
			$curr=get_woocommerce_currency_symbol();
		
			$val= get_post_meta($values['product_id'],'phoen_woocommerce_discount_mode', true); 
			
			$quantity = intval( $values['item_meta']['_qty'][0] );
			
			$gen_settings = get_option('phoe_disc_value');
			
			$coupon_disc=isset($gen_settings['coupon_disc'])?$gen_settings['coupon_disc']:'';
			
			for($i=0;$i<count($val);$i++) 	{
				$phoen_minval=isset($val[$i]['min_val'])?$val[$i]['min_val']:"";
				$phoen_maxval=isset($val[$i]['max_val'])?$val[$i]['max_val']:"";
				if(($quantity>=$phoen_minval)&&($quantity<=$phoen_maxval))  {
						
					$amt=isset($val[$i]['discount'])?$val[$i]['discount']:'';
							
					$type=isset($val[$i]['type'])?$val[$i]['type']:'';
							
					if($type=='percentage') {
							
						 $type_curr="[".$amt."% Discount]";
						break;
					}
							
					else {
						
						$type_curr="[". $curr.$amt." Discount on each Product]";	
								break;							
					}
				}
						
			}
				
			$discount_type = get_post_meta( $order->id);
			
			
			if(($coupon_disc==1)&&(!( empty( $discount_type['_cart_discount'][0]))))
			{
				return "<span class='discount-info' title='$type_curr'>" .
				"<span>$price</span></span>";
				
			}
			else{
			
				return "<span class='discount-info' title='$type_curr'>" .
				"<span>$price</span>" .
				"<span class='new-price' style='color:red;'> $type_curr</span></span>";

			}
		} 
			
			
		if($enable_disc=="1") 	{
			
			add_action( 'woocommerce_before_calculate_totals', 'phoen_dpad_calculate_extra_fee', 1, 1 );
		
			add_filter( 'woocommerce_cart_item_price', 'phoen_dpad_filter_item_price', 10, 2 );
			
			add_filter( 'woocommerce_cart_item_subtotal', 'phoen_dpad_filter_subtotal_price' , 10, 2 );
			
			add_filter( 'woocommerce_checkout_item_subtotal', 'phoen_dpad_filter_subtotal_price' , 10, 2 ); 
			
			add_filter( 'woocommerce_order_formatted_line_subtotal', 'phoen_dpad_filter_subtotal_order_price' , 10, 3 );
			
			
		}

	} ?>
