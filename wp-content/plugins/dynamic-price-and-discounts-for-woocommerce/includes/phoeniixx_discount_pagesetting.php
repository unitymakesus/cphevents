<?php if ( ! defined( 'ABSPATH' ) ) exit;
	
if ( ! empty( $_POST ) && check_admin_referer( 'phoe_Discount_form_action', 'phoe_Discount_form_action_form_nonce_field' ) ) {

	if(sanitize_text_field( $_POST['disc_submit'] ) == 'Save'){
		
		$enable_disc=sanitize_text_field($_POST['enable_disc']);
		
		$coupon_disc=sanitize_text_field($_POST['coupon_disc']);
		
		$phoe_disc_value = array(
		
		'enable_disc'=>$enable_disc,
		'coupon_disc'=>$coupon_disc
		);
		
		update_option('phoe_disc_value',$phoe_disc_value);
		
	}
	
}

	$gen_settings = get_option('phoe_disc_value');

	$enable_disc=isset($gen_settings['enable_disc'])?$gen_settings['enable_disc']:'';
	
	$plugin_dir_url = plugin_dir_url( __FILE__ );	
		
 ?>

	<div id="phoeniixx_phoe_Disc_wrap_profile-page" class=" phoeniixx_phoe_Disc_wrap_profile_div">
	
		<div class="pho-upgrade-btn">
			<a href="https://www.phoeniixx.com/product/dynamic-pricing-and-discounts-for-woocommerce/" target="_blank"><img src="<?php echo $plugin_dir_url; ?>../assets/images/premium-btn.png" /></a>
			<a target="blank" href="http://dynamicprice.phoeniixxdemo.com/wp-login.php?redirect_to=http%3A%2F%2Fdynamicprice.phoeniixxdemo.com%2Fwp-admin%2F&reauth=1"><img src="<?php echo $plugin_dir_url; ?>../assets/images/button2.png" /></a>
		</div>
		
		<div class="phoe_video_main">
			<h3>How to set up plugin</h3> 
			<iframe width="800" height="360"src="https://www.youtube.com/embed/bZKXDZzaMfM" allowfullscreen></iframe>
		</div>

		<form method="post" id="phoeniixx_phoe_Disc_wrap_profile_form" action="" >
		
			<?php wp_nonce_field( 'phoe_Discount_form_action', 'phoe_Discount_form_action_form_nonce_field' ); ?>
			
			<table class="form-table">
				
				<tbody>	
		
					<tr class="phoeniixx_phoe_Disc_wrap">
				
						<th>
						
							<label><?php _e('Enable Discounts','phoen-dpad'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="enable_disc" id="enable_disc" value="1" <?php echo(isset($gen_settings['enable_disc']) && $gen_settings['enable_disc'] == '1')?'checked':'';?>>
							
						</td>
						
					</tr>
					<tr class="phoeniixx_phoe_Disc_wrap">
				
						<th>
						
							<label><?php _e('Remove any bulk discounts if a coupon code is applied','phoen-dpad'); ?> </label>
							
						</th>
						
						<td>
						
							<input type="checkbox"  name="coupon_disc" id="coupon_disc" value="1" <?php echo(isset($gen_settings['coupon_disc']) && $gen_settings['coupon_disc'] == '1')?'checked':'';?>>
							
						</td>
						
					</tr>
		
		
		
				</tbody>
				
			</table>
			</br>
					<input type="submit" value="Save" name="disc_submit" id="submit" class="button button-primary">
		</form>
		
	</div>
	
	<style>

	.form-table th {
	
		width: 270px;
		
		padding: 25px;
		
	}
	
	.form-table td {
	
		padding: 20px 10px;
	}
	
	.form-table {
	
		background-color: #fff;
	}
	
	h3 {
	
		padding: 10px;
	}
	
	a:focus {
		box-shadow: none;
	}
	
	pho-upgrade-btn {
		margin-top: 15px;
	}
	
	.phoe_video_main {
		padding: 20px;
		text-align: center;
	}
	
	.phoe_video_main h3 {
		color: #02c277;
		font-size: 28px;
		font-weight: bolder;
		margin: 20px 0;
		text-transform: capitalize
		display: inline-block;
	}

</style>