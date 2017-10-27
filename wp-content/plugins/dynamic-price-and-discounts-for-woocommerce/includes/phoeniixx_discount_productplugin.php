<?php if ( ! defined( 'ABSPATH' ) ) exit;

	function phoen_dpad_custom_tab_options_tab_discounts() {

		?>

	    <li class="phoeniixx_dynamic_discount_custom_tab"><a href=""><?php _e('Discounts', 'phoen-dpad'); ?></a></li>

		<?php

	}

	


	function phoen_dpad_custom_tab_options_discounts() {
   ?>
		
<script>

	jQuery(document).ready(function(){
		jQuery('.phoeniixx_dynamic_discount_custom_tab').click(function(){
			
				jQuery('.phoeniixx_discount_html_content_div_main').show();
		});
	
		var a = jQuery('#phoeniixx_discount_div').html();

			jQuery('.phoe_add_disc_more').click(function(){

				jQuery('.phoeniixx_discount_html_content_div').append(a);

			});

	});


	jQuery(document).on('click','.phoe_remove_disc_div',function(){

		jQuery(this).parent('div').remove();

	});


</script>

<body>

	<div id="phoeniixx_discount_div" style="display:none;">

		<div class="phoeniixx_discount_min_max_div">

			<input type="number" placeholder="Min Quantity" name="min_val[]"  class="min_val" value="">

			<input type="number"  placeholder="Max Quantity" name="max_val[]" class="max_val" value="">

			<input type="number" step='any' placeholder="Discount Value" name="discount[]" class="discount" value="">

		
			<button name="remove_b" class="phoe_remove_disc_div button">-</button>
		</div>
		
	</div>

	<?php 
	global $product;
		
	global $post;

	$val= get_post_meta($post->ID,'phoen_woocommerce_discount_mode', true); 
	
	?>
	<div class="phoeniixx_discount_html_content_div_main" style="display:none;" ><?php 	
		for($i=0;$i<count($val);$i++)
		{
			
			?>
			
		   <div class="phoeniixx_discount_min_max_div"> 

			<input type="number" placeholder="Min Quantity" name="min_val[]"  class="min_val" value="<?php echo isset($val[$i]['min_val'])?$val[$i]['min_val']:''; ?>">

			<input type="number"  placeholder="Max Quantity" name="max_val[]" class="max_val" value="<?php echo isset($val[$i]['max_val'])?$val[$i]['max_val']:''; ?>">

			<input type="number" step='any' placeholder="Discount Value" name="discount[]" class="discount" value="<?php echo isset($val[$i]['discount'])?$val[$i]['discount']:''; ?>">

			<button name="remove_b" class="phoe_remove_disc_div button">-</button>
			
			</div>
			<?php 
			
		} ?>

		

		
		<div class="phoeniixx_discount_html_content_div">

			</div>
			
		<?php  $pheon_disp_type=isset($val[0]['type'])?$val[0]['type']:''; ?>
		<select name="disc_type" >
			
			<option value="percentage"  <?php if($pheon_disp_type=='percentage') echo 'selected';?>>Percentage</option>
			
			<option value="amount"  <?php if($pheon_disp_type=='amount') echo 'selected';?>>Amount</option>
		
		</select>
		
		<input type="button" value="Add" class="phoe_add_disc_more button">

	</div>
	<?php	

	}

	


	function phoen_dpad_process_product_meta_custom_tab_discounts( $post_id ) {
				
		$discount_data = array();
		
		$min_val= $_POST['min_val'];

		$max_val= $_POST['max_val'];

		$discount= $_POST['discount'];
		
		$disc_type=$_POST['disc_type'];
	
		
		for($i=0;$i<COUNT($min_val);$i++)
		{
			
			 if( $min_val[$i] != ''){
				 
				 $discount_data[] = array(
				 
											'min_val' =>	$min_val[$i],
												
											'max_val' =>	$max_val[$i],

											'discount' =>	$discount[$i],
											
											'type'=>$disc_type
										 );

			}
		}
		update_post_meta( $post_id, 'phoen_woocommerce_discount_mode', $discount_data );
		
	}
	
	add_action('woocommerce_process_product_meta', 'phoen_dpad_process_product_meta_custom_tab_discounts');
	
	add_action('woocommerce_product_write_panels', 'phoen_dpad_custom_tab_options_discounts');
	
	add_action('woocommerce_product_write_panel_tabs', 'phoen_dpad_custom_tab_options_tab_discounts'); 
	
?>