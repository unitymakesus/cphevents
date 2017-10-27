<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$dummy_settings['product_rules_on_off'] = 'enable';
$dummy_settings['combinational_rules_on_off'] = 'enable';
$dummy_settings['cat_comb_rules_on_off'] = 'enable';
$dummy_settings['category_rules_on_off'] = 'enable';
$dummy_settings['cart_rules_on_off'] = 'enable';
$dummy_settings['buy_and_get_free_rules_on_off'] = 'enable';
$dummy_settings['price_table_on_off'] = 'enable';
$dummy_settings['offer_table_on_off'] = 'disable';
$dummy_settings['offer_table_position'] = 'woocommerce_before_add_to_cart_button';
$dummy_settings['auto_add_free_product_on_off'] = 'enable';
$dummy_settings['pricing_table_qnty_shrtcode'] = 'nos.';
$dummy_settings['show_discount_in_line_item'] = 'yes';
$dummy_settings['pricing_table_position'] = 'woocommerce_before_add_to_cart_button';
$dummy_settings['mode'] = 'best_discount';
$dummy_settings['disable_shop_page_calculation'] = 'no';
$dummy_settings['disable_product_page_calculation'] = 'no';
$dummy_settings['show_on_sale'] = 'yes';
$dummy_settings['discount_over_price_including_tax'] = 'yes';
$dummy_settings['execution_order'] = array('product_rules',
    'category_rules');

$settings = get_option('xa_dynamic_pricing_setting', $dummy_settings);
extract($settings);
if (!isset($disable_shop_page_calculation)) {
    $disable_shop_page_calculation = "no";
}
if (!isset($cat_comb_rules_on_off)) {
    $cat_comb_rules_on_off = "enable";
}
if (!isset($disable_product_page_calculation)) {
    $disable_product_page_calculation = "no";
}
if (!isset($pricing_table_qnty_shrtcode)) {
    $pricing_table_qnty_shrtcode = "nos.";
}
if (!isset($show_discount_in_line_item)) {
    $show_discount_in_line_item = "yes";
}
if (!isset($mode)) {
    $mode = "best_discount";
}
if (!isset($buy_and_get_free_rules_on_off)) {
    $buy_and_get_free_rules_on_off = "enable";
}
if (!isset($pricing_table_position)) {
    $pricing_table_position = "woocommerce_before_add_to_cart_button";
}
if (!isset($offer_table_position)) {
    $offer_table_position = "woocommerce_before_add_to_cart_button";
}
if (!isset($offer_table_on_off)) {
    $offer_table_on_off = "disable";
}
if (!isset($show_on_sale)) {
    $show_on_sale = "yes";
}
if (!isset($discount_over_price_including_tax)) {
    $discount_over_price_including_tax = "yes";
}
if (!isset($execution_order)) {
    $execution_order = array('product_rules',
        'category_rules',);
}


$rules_modes = array('product_rules' => 'Product Rules',
    'combinational_rules' => 'Combinational Rules',
    'cat_combinational_rules' => 'Category Combinational Rules',
    'category_rules' => 'Category Rules',
    'cart_rules' => 'Cart Rules',
    'buy_get_free_rules' => 'Buy And Get Free Offers',
);
?>
 
<script>

    jQuery(function () {
        jQuery('#show_discount_in_line_item').on('change', function () {
            if (jQuery(this).val() == 'yes')
            {
                jQuery('#xa_calc_row').hide();
            } else
            {
                jQuery('#xa_calc_row').show();
            }

        });
        if (jQuery('#show_discount_in_line_item').val() == 'yes')
        {
            jQuery('#xa_calc_row').hide();
        } else
        {
            jQuery('#xa_calc_row').show();
        }
        jQuery('#send_to_enable').on('click', function () {
            var current_selected = jQuery('#disabled_modes').val();
            jQuery('#disabled_modes').find('[value="' + current_selected + '"]').remove().appendTo('#enabled_modes');
            jQuery('#disabled_modes').prop('selectedIndex', 0);

        });
        jQuery('#send_to_disable').on('click', function () {
            var current_selected = jQuery('#enabled_modes').val();
            jQuery('#enabled_modes').find('[value="' + current_selected + '"]').remove().appendTo('#disabled_modes');
            jQuery('#enabled_modes').prop('selectedIndex', 0);

        });
        jQuery('#xa_reorder_up').on('click', function () {
            var $op = jQuery('#enabled_modes option:selected'),
                    $this = jQuery(this);
            if ($op.length) {
                $op.first().prev().before($op)
            }
        });
        jQuery('#xa_reorder_down').on('click', function () {
            var $op = jQuery('#enabled_modes option:selected'),
                    $this = jQuery(this);
            if ($op.length) {
                $op.last().next().after($op);
            }

        });
        jQuery('#submit').on('click', function () {
            jQuery('#enabled_modes option').prop('selected', true);
        });

    });
</script>

<form name="post"  method="GET" id="post">
<span>In order to configure the discounts for the following rules, go to <a href="?page=dynamic-pricing-main-page"  >Discount Rules</a> tab</span>
</br></br>
    <center style="max-width:900px">
        <table class="table"style="font-size: small;margin-left: 20px;" >
            <tbody style="font-size: inherit;width: 100%;">
            <div style="border-style: solid; border-width: 1px; border-color: grey;background: rgb(53, 165, 245">
                <center> <span style='color:white'> <?php _e('Execution Order', 'eh-dynamic-pricing-discounts'); ?></span></center>
            </div>
            <tr style="padding-top:20px;">
                <td>
                    <label style="font-weight: bold;">Disabled Rules</label>
                </td>  
                <td ></td>
                <td>
                    <label style="font-weight: bold;">Enabled Rules</label>
                </td>   
                <td style="text-align: center;" >
                    <label style="font-weight: bold;">Reorder</label>
                </td>
            </tr>  
            <tr>
                <td>
                    <select id="disabled_modes"  name="disabled_modes[]"  size="6" style="width: 300px;height:auto;" >
                        <?php
                        foreach ($rules_modes as $key => $mode_name) {
                            if (!in_array($key, $execution_order)) {
                                echo "<option value='" . $key . "'>" . $mode_name . "</option>";
                            }
                        }
                        ?>
                    </select>
                </td>  
                <td style="width: 30px;padding: 20px;">
                    <input type="button" class="button" style="margin-bottom: 10px;"  name="send_to_enable" id="send_to_enable" value=">>"/>
                    <input type="button" class="button" style="margin-bottom: 10px;" name="send_to_disable" id="send_to_disable" value="<<"/>                
                </td>
                <td>
                    <select id="enabled_modes" name="enabled_modes[]" size="6" style="width: 300px;height:auto;" multiple>
                        <?php
                        foreach ($execution_order as $key) {
                            echo "<option value='" . $key . "'>" . $rules_modes[$key] . "</option>";
                        }
                        ?>
                    </select>
                </td>   
                <td style="width: 30px;padding: 20px;">
                    <input type="button" class="button" style="margin-bottom: 10px;width:60px;" name="xa_reorder_up" id="xa_reorder_up" value="Up"/>
                    <input type="button" class="button" style="margin-bottom: 10px;width:60px;" name="xa_reorder_down" id="xa_reorder_down" value="Down"/>                
                </td>
            </tr>  
            </tbody>    
            <tfoot>
            </tfoot>
        </table>
        <div >
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">

                </br>
                <div style="border-style: solid; border-width: 1px; border-color: grey;background: rgb(53, 165, 245">
                    <center> <span style='color:white'> <?php _e('Options', 'eh-dynamic-pricing-discounts'); ?></span></center>
                </div>

                <div class="clear"></div>
                <div id="woocommerce-product-data" class="postbox ">
                    <div class="inside">
                        <div class="panel-wrap product_data" style="min-height: 220px;">
                            <ul class="product_data_tabs wc-tabs">
                                <li class="general_options   active">
                                    <a href="#" onclick="select(this, '#general_product_data')">
                                        <span>General</span>
                                    </a>
                                </li>
                                <li class="pricing_tabel_option  " style="display: block;">
                                    <a href="#" onclick="select(this, '#pricing_tabel_option')">
                                        <span>Pricing Table</span>
                                    </a>
                                </li>
                                <li class="offers_tabel_option  " style="display: block;">
                                    <a href="#" onclick="select(this, '#offers_tabel_option')">
                                        <span>Offers Table</span>
                                    </a>
                                </li>
                                <li class="bogo_rules_options  " style="display: block;">
                                    <a href="#" onclick="select(this, '#bogo_rules_options')">
                                        <span>BOGO options</span>
                                    </a>
                                </li>
                                <!--                                    <li class="inventory_options  " style="display: block;">
                                                                            <a href="#" onclick="select(this,'#other_options')">
                                                                                    <span>Other options</span>
                                                                            </a>
                                                                    </li>-->
                                <li class="" style="display: block;">
                                    </br>
                                <center><input class="button button-primary" name="submit" id="submit" type="submit" value="Save Settings" /></center>
                                </li>

                            </ul>
                            <div  class="panel woocommerce_options_panel" style="display: block;">
                                <div class="options_group" id="general_product_data" style="display: block;">
                                    <p class="form-field  " >
                                        <label for="mode">Calculation Mode</label>
                                        <?php echo wc_help_tip('1.       “Calculation Mode” – In case of multiple rules being satisfied by the products on the cart, “Best Discount” option would calculate all the applicable discounts and select the best among them. This option is selected in the settings page by default. In case of “First Match”, as per the order of the rule categories in the settings page and the rule numbers in the corresponding rule categories, the first matched rule will be selected among all the available rules. In case of “All Match”, all the available rules which matches with the current scenario will be applied.'); ?>  

                                        <select id="mode" name="mode" class="select short" style="">
                                            <option value='best_discount'  <?php echo(($mode == 'best_discount') ? 'selected' : ''); ?>>Best Discount</option>
                                            <option value='first_match'  <?php echo(($mode == 'first_match') ? 'selected' : ''); ?>>First Match Rule</option>
                                            <option value='all_match'  <?php echo(($mode == 'all_match') ? 'selected' : ''); ?>>All Matched Rules</option>
                                        </select>
                                    </p>
                                </div>
                                <div class="options_group" id="pricing_tabel_option" style="display: none;">
                                    <p class="form-field  " >
                                        <label for="price_table_on_off">Display Prices Table on Product Page</label>
                                        <span class="woocommerce-help-tip" data-tip="This Option will create a pricing table from rules and show on product page"></span>
                                        <select id="price_table_on_off" name="price_table_on_off" class="select short" style="">
                                            <option value='enable'  <?php echo(($price_table_on_off == 'enable') ? 'selected' : ''); ?>>Yes</option>
                                            <option value='disable'  <?php echo(($price_table_on_off == 'disable') ? 'selected' : ''); ?>>No</option>
                                        </select>
                                    </p>
                                    <p class="form-field  " >
                                        <label for="pricing_table_position">Position of Pricing Table on Product Page</label>
                                        <span class="woocommerce-help-tip" data-tip="Select where you want to show this table on product page"></span>
                                        <select name='pricing_table_position'  class='select short'  selected='<?php echo $pricing_table_position ?>'>
                                            <option value='woocommerce_before_single_product'  <?php echo(($pricing_table_position == 'woocommerce_before_single_product') ? 'selected' : ''); ?>><?php _e('Before Product', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_single_product'  <?php echo(($pricing_table_position == 'woocommerce_after_single_product') ? 'selected' : ''); ?>><?php _e('After Product', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_single_product_summary'  <?php echo(($pricing_table_position == 'woocommerce_before_single_product_summary') ? 'selected' : ''); ?>><?php _e('Before Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_single_product_summary'  <?php echo(($pricing_table_position == 'woocommerce_single_product_summary') ? 'selected' : ''); ?>><?php _e('In Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_single_product_summary'  <?php echo(($pricing_table_position == 'woocommerce_after_single_product_summary') ? 'selected' : ''); ?>><?php _e('After Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_add_to_cart_button'  <?php echo(($pricing_table_position == 'woocommerce_before_add_to_cart_button') ? 'selected' : ''); ?>><?php _e('Before Add To Cart Button', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_add_to_cart_button'  <?php echo(($pricing_table_position == 'woocommerce_after_add_to_cart_button') ? 'selected' : ''); ?>><?php _e('After Add To Cart Button', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_add_to_cart_form'  <?php echo(($pricing_table_position == 'woocommerce_before_add_to_cart_form') ? 'selected' : ''); ?>><?php _e('Before Add To Cart Form', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_add_to_cart_form'  <?php echo(($pricing_table_position == 'woocommerce_after_add_to_cart_form') ? 'selected' : ''); ?>><?php _e('After Add To Cart Form', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_thumbnails'  <?php echo(($pricing_table_position == 'woocommerce_product_thumbnails') ? 'selected' : ''); ?>><?php _e('Product Thumbnails', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_meta_start'  <?php echo(($pricing_table_position == 'woocommerce_product_meta_start') ? 'selected' : ''); ?>><?php _e('Product Meta Start', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_meta_end'  <?php echo(($pricing_table_position == 'woocommerce_product_meta_end') ? 'selected' : ''); ?>><?php _e('Product Meta End', 'eh-dynamic-pricing-discounts'); ?></option>
                                        </select>
                                    </p>
                                    <p class="form-field pricing_table_qnty_shrtcode ">
                                        <label for="pricing_table_qnty_shrtcode">Short Name For Quantity:</label>
                                        <input type="text" class="short" style="" name="pricing_table_qnty_shrtcode" id="pricing_table_qnty_shrtcode" value='<?php echo $pricing_table_qnty_shrtcode; ?>' placeholder="">
                                    </p>                                    
                                </div>
                                <div class="options_group" id="offers_tabel_option" style="display: none;">                                        
                                    <p class="form-field  " >
                                        <label for="offer_table_on_off">Display Offers Table on Product Page</label>  <?php //premium ?>
                                        <span class="woocommerce-help-tip" data-tip="This option will create list of offers applicable on that product which will be visible on product page"></span>
                                        <select id="offer_table_on_off" name="offer_table_on_off" class="select short" style="" selected='disable'>
                                            <option value='disable'  selected >No</option>
                                        </select>
                                    </p>
                                    <p class="form-field  " >
                                        <label for="offer_table_position">Position of Offers Table on Product Page</label>
                                        <span class="woocommerce-help-tip" data-tip="Select where you want to show this table on product page" ></span>
                                        <select id="offer_table_position" name="offer_table_position" class="select short" style=""  selected=''>
                                            <option value='woocommerce_before_single_product'  <?php echo(($offer_table_position == 'woocommerce_before_single_product') ? 'selected' : ''); ?>><?php _e('Before Product', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_single_product'  <?php echo(($offer_table_position == 'woocommerce_after_single_product') ? 'selected' : ''); ?>><?php _e('After Product', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_single_product_summary'  <?php echo(($offer_table_position == 'woocommerce_before_single_product_summary') ? 'selected' : ''); ?>><?php _e('Before Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_single_product_summary'  <?php echo(($offer_table_position == 'woocommerce_single_product_summary') ? 'selected' : ''); ?>><?php _e('In Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_single_product_summary'  <?php echo(($offer_table_position == 'woocommerce_after_single_product_summary') ? 'selected' : ''); ?>><?php _e('After Product Summary', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_add_to_cart_button'  <?php echo(($offer_table_position == 'woocommerce_before_add_to_cart_button') ? 'selected' : ''); ?>><?php _e('Before Add To Cart Button', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_add_to_cart_button'  <?php echo(($offer_table_position == 'woocommerce_after_add_to_cart_button') ? 'selected' : ''); ?>><?php _e('After Add To Cart Button', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_before_add_to_cart_form'  <?php echo(($offer_table_position == 'woocommerce_before_add_to_cart_form') ? 'selected' : ''); ?>><?php _e('Before Add To Cart Form', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_after_add_to_cart_form'  <?php echo(($offer_table_position == 'woocommerce_after_add_to_cart_form') ? 'selected' : ''); ?>><?php _e('After Add To Cart Form', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_thumbnails'  <?php echo(($offer_table_position == 'woocommerce_product_thumbnails') ? 'selected' : ''); ?>><?php _e('Product Thumbnails', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_meta_start'  <?php echo(($offer_table_position == 'woocommerce_product_meta_start') ? 'selected' : ''); ?>><?php _e('Product Meta Start', 'eh-dynamic-pricing-discounts'); ?></option>
                                            <option value='woocommerce_product_meta_end'  <?php echo(($offer_table_position == 'woocommerce_product_meta_end') ? 'selected' : ''); ?>><?php _e('Product Meta End', 'eh-dynamic-pricing-discounts'); ?></option>
                                        </select>
                                    </p>

                                </div>


                                <div class="options_group" id="bogo_rules_options" style="display: none;">
                                    <p class="form-field  ">
                                        <label for="auto_add_free_product_on_off">Automatically add free products</label> <?php  //premium ?>
                                        <span class="woocommerce-help-tip" data-tip="If this field is enabled then, the free product would be automatically added to the cart. If this field is disabled, then the free product would have to be selected manually. By default, this field would be “enabled” as that what most of the store owners desire"></span>
                                        <select name='auto_add_free_product_on_off' >
                                            <option value='disable'  selected >Disable</option>
                                        </select>
                                    </p>
                                </div>


                                <div class="options_group" id="other_options" style="display: none;">

                                </div>

                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </center>
</form>




