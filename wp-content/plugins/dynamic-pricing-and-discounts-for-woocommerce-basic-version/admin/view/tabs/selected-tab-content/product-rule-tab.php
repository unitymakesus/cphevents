<?php
if (isset($_REQUEST['edit']) && !empty($_REQUEST['edit'])) {
    echo '<input type="hidden" name="update" value="' . $_REQUEST['edit'] . '" >';
}
?>   
<script>
    jQuery(document).ready(function () {
        jQuery('#check_on').on('change', function () {
            let thisval = jQuery('#check_on').val();
            jQuery("[for=min]").html('Minimum ' + thisval + '<span style="color:red;padding-left:5px">*<span>');
            jQuery("[for=max]").html('Maximum ' + thisval);
        });
        jQuery('#discount_type').on('change', function () {
            let thisval = jQuery('#discount_type').val();
            if (thisval == 'Percent Discount')
            {
                jQuery('#max_discount').parent().show();
                jQuery("[for=value]").html('Discount percentage' + '<span style="color:red;padding-left:5px">*<span>');
            } else if (thisval == 'Flat Discount')
            {
                jQuery("[for=value]").html('Flat discount amount' + '<span style="color:red;padding-left:5px">*<span>');
                jQuery('#max_discount').val('');
                jQuery('#max_discount').parent().hide();
            } else if (thisval == 'Fixed Price')
            {
                jQuery('#max_discount').parent().show();
                jQuery("[for=value]").html('Fixed price' + '<span style="color:red;padding-left:5px">*<span>');
            }
        });
        jQuery('#rule_on').on('change', function () {
            let selected = jQuery('#rule_on').val();

            jQuery('#product_id').removeAttr('required');
            if (selected == 'products')
            {

                jQuery('#category_id').parent().hide();
                jQuery('#product_id').parent().show();
                jQuery('#product_id').attr('required', 'required');
            } else if (selected == 'categories')
            {
                jQuery("#product_id").empty();
                jQuery('#product_id').parent().hide();
                jQuery('#category_id').parent().show();
            } else if (selected == 'cart')
            {
                jQuery('#product_id').parent().hide();
                jQuery('#category_id').parent().hide();
            }
        });
        jQuery('#rule_on').trigger('change');
        jQuery('#rule_tab label').append('<span style="color:red;padding-left:5px">*<span>');
        jQuery('#check_on').trigger('change');
        jQuery("[for=max]").html(jQuery("[for=max]").text());
        jQuery('#discount_type').trigger('change');

    });

</script>
<div >
    <div id="normal-sortables" class="meta-box-sortables ui-sortable">

        </br>

        <div class="clear"></div>
        <div id="woocommerce-product-data" class="postbox ">
            <div class="inside">
                <div class="panel-wrap product_data" style="min-height: 390px;">
                    <ul class="product_data_tabs wc-tabs">
                        <li class="rule_options   active">
                            <a class="xa_link" onclick="select(this, '#rule_tab')">
                                <span>Rule</span>
                            </a>
                        </li>
                        <li class="adjustment_options">
                            <a class="xa_link" onclick="select(this, '#adjustment_tab')">
                                <span>Adjustments<span class="super" style="color:black;">Premium</span></span>
                            </a>
                        </li>
                        <li class="roles_options " style="display: block;">
                            <a class="xa_link" onclick="select(this, '#allowed_roles_and_date_tab')">
                                <span>Allowed Roles & Date</span>
                            </a>
                        </li>
                        <li class="restricion_options " style="display: block;">
                            <a class="xa_link" onclick="select(this, '#restriction_tab')">
                                <span>Restrictions<span class="super" style="color:black;">Premium</span></span>
                            </a>
                        </li>

                    </ul>
                    <div  class="panel woocommerce_options_panel" style="display: block;">
                        <div class="options_group" id="rule_tab" style="display: block;">
                            <?php
                            woocommerce_wp_text_input(array(
                                'id' => 'offer_name',
                                'label' => __('Offer name', 'woocommerce'),
                                'placeholder' => 'Enter a descriptive offer name',
                                'description' => __('Name/Text of the offer to be displayed in the Offer Table. We suggest a detailed description of the discount.', 'woocommerce'),
                                'type' => 'text',
                                'desc_tip' => true,
                                'value' => !empty($_REQUEST['offer_name']) ? $_REQUEST['offer_name'] : '',
                                'custom_attributes' => array('required' => 'required')
                            ));
                            woocommerce_wp_select(array(
                                'id' => 'rule_on',
                                'label' => __('Rule applicable on', 'woocommerce'),
                                'options' => array('products' => 'Selected products',
                                    'categories' => 'Selected category',
                                    'cart' => 'Products in cart',),
                                'value' => !empty($_REQUEST['rule_on']) ? $_REQUEST['rule_on'] : 'products',
                                'description' => __('Decide whether the rule has to be applied product wise or category wise.', 'woocommerce'),
                                'desc_tip' => true,
                            ));
                            ///// start product search     
                            if (is_wc_version_gt_eql('2.7')) {
                                ?>
                                <p class="form-field"><label><?php _e('Products', 'woocommerce'); ?></label>
                                    <select class="wc-product-search" multiple="multiple" style="width: 50%;height:30px" id="product_id" name="product_id[]" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations">
                                        <?php
                                        $product_ids = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : array();  // selected product ids
                                        foreach ($product_ids as $product_id) {
                                            $product = wc_get_product($product_id);
                                            if (is_object($product)) {
                                                echo '<option value="' . esc_attr($product_id) . '"' . selected(true, true, false) . '>' . wp_kses_post($product->get_formatted_name()) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select> <?php echo wc_help_tip(__('Rule to be applied on which products', 'woocommerce')); ?></p>
                                <?php
                            } else {
                                ?>
                                <p class="form-field"><label><?php _e('Products', 'woocommerce'); ?></label>
                                    <input id="product_id" name="product_id" type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;"  data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'woocommerce'); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="<?php
                                    $product_ids = (!empty($_REQUEST['product_id']) && is_array($_REQUEST['product_id'])) ? $_REQUEST['product_id'] : array();  // selected product ids
                                    $json_ids = array();
                                    foreach ($product_ids as $product_id) {
                                        $product = wc_get_product($product_id);
                                        if (is_object($product)) {
                                            $json_ids[$product_id] = wp_kses_post($product->get_formatted_name());
                                        }
                                    }

                                    echo esc_attr(json_encode($json_ids));
                                    ?>" value="<?php echo implode(',', array_keys($json_ids)); ?>" /> <?php echo wc_help_tip(__('Rule to be applied on which products', 'woocommerce')); ?></p>
                                    <?php
                                }
                                // start Categories  search
                                ?>
                            <p class="form-field"><label for="category_id"><?php _e('Product categories', 'woocommerce'); ?></label>
                                <select id="category_id" name="category_id" style="width: 50%;height:30px"  class="wc-enhanced-select"  data-placeholder="<?php esc_attr_e('Any category', 'woocommerce'); ?>">
                                    <?php
                                    $category_ids = !empty($_REQUEST['category_id']) ? $_REQUEST['category_id'] : '';  //selected product categorie
                                    $categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
                                    if ($categories) {
                                        foreach ($categories as $cat) {
                                            echo '<option value="' . esc_attr($cat->term_id) . '"' . selected($cat->term_id == $category_ids, true, false) . '>' . esc_html($cat->name) . '</option>';
                                        }
                                    }
                                    ?>
                                </select> <?php echo wc_help_tip(__('Product categories that the rule will be applied to, or that need to be in the cart in order for the "Fixed cart discount" to be applied.', 'woocommerce')); ?></p>
                            <?php
                            //// end category search
                            woocommerce_wp_select(array(
                                'id' => 'check_on',
                                'label' => __('Check for', 'woocommerce'),
                                'options' => array('Quantity' => 'Quantity',
                                    'Weight' => 'Weight',
                                    'Price' => 'Price',),
                                'value' => !empty($_REQUEST['check_on']) ? $_REQUEST['check_on'] : 'Quantity',
                                'description' => __('The rules can be applied based on “Quantity/Price/Weight”', 'woocommerce'),
                                'desc_tip' => true,
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'min',
                                'label' => __('Minimum', 'woocommerce'),
                                'description' => __('Minimum value to check', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'value' => !empty($_REQUEST['min']) ? $_REQUEST['min'] : '1',
                                'custom_attributes' => array('required' => 'required')
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'max',
                                'label' => __('Maximum', 'woocommerce'),
                                'description' => __('Maximum value to check, set it empty for no limit', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'value' => !empty($_REQUEST['max']) ? $_REQUEST['max'] : ''
                            ));
                            woocommerce_wp_select(array(
                                'id' => 'discount_type',
                                'label' => __('Discount type', 'woocommerce'),
                                'options' => array('Percent Discount' => 'Percent Discount',
                                    'Flat Discount' => 'Flat Discount',
                                    'Fixed Price' => 'Fixed Price',),
                                'value' => !empty($_REQUEST['discount_type']) ? $_REQUEST['discount_type'] : 'Percent Discount',
                                'description' => __('Three types of discounts can be applied – “Percentage Discount/Flat Discount/Fixed Price”', 'woocommerce'),
                                'desc_tip' => true,
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'value',
                                'label' => __('Discount', 'woocommerce'),
                                'description' => __('If you select “Percentage Discount”, the given percentage (value) would be discounted on each unit of the product in the cart.
If you select “Flat Discount”, the given amount (value) would be discounted at subtotal level in the cart
If you select “Fixed Price”, the original price of the product is replaced by the given fixed price (value).', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'value' => !empty($_REQUEST['value']) ? $_REQUEST['value'] : '',
                                'custom_attributes' => array('required' => 'required',"step"=>"any")
                            ));
                            ?>
                        </div>
                        <div class="options_group" id="adjustment_tab" style="display: none;">
                            <?php //premium ?>
                            <?php
                            woocommerce_wp_text_input(array(
                                'id' => 'max_discount',
                                'label' => __('Maximum discount amount', 'woocommerce'),
                                'description' => __('After Calculation Discount Value Must Not Exceeed This Amount For This Rule', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'value' => !empty($_REQUEST['max_discount']) ? $_REQUEST['max_discount'] : '',
                                'custom_attributes'=>array('disabled'=>'disabled')
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'adjustment',
                                'label' => __('Adjustment amount', 'woocommerce'),
                                'description' => __('Adjust final discount amount by this amount', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'value' => !empty($_REQUEST['adjustment']) ? $_REQUEST['adjustment'] : '',
                                'custom_attributes'=>array('disabled'=>'disabled')
                            ));
                            woocommerce_wp_checkbox(array(
                                'id' => 'repeat_rule',
                                'label' => __('Allow repeat execution', 'woocommerce'),
                                'description' => sprintf('<span class="description">' . __('Rule will be executed multiple times if quantity of product is in multiple of max quantity &(min = max)', 'woocommerce') . "</span>"),
                                'value' => !empty($_REQUEST['repeat_rule']) ? $_REQUEST['repeat_rule'] : 'on',
                                'custom_attributes'=>array('disabled'=>'disabled')
                            ));
                            ?>                            
                        </div>
                        <div class="options_group" id="allowed_roles_and_date_tab" style="display: none;" disabled>
                            <?php
                            global $wp_roles;
                            $roles = $wp_roles->get_names();
                            $roles['all'] = __('All', 'eh-dynamic-pricing-discounts');
                            woocommerce_wp_select(array(
                                'id' => 'allow_roles',
                                'label' => __('Allowed Roles', 'woocommerce'),
                                'options' => $roles,
                                'value' => !empty($_REQUEST['allow_roles']) ? $_REQUEST['allow_roles'] : 'all',
                                'description' => ' Use this only if you wish to give discounts based on user roles. Leave this blank if the rules are valid for all',
                                'desc_tip' => true,
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'from_date',
                                'value' => esc_attr(!empty($_REQUEST['from_date']) ? $_REQUEST['from_date'] : ''),
                                'label' => __('Valid from date', 'woocommerce'),
                                'placeholder' => 'YYYY-MM-DD',
                                'description' => 'The date from which the rule would be applied. This can be left blank if do not wish to set up any date range.',
                                'desc_tip' => true,
                                'class' => 'date-picker',
                                'custom_attributes' => array(
                                    'pattern' => apply_filters('woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}'),
                                ),
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'to_date',
                                'value' => esc_attr(!empty($_REQUEST['to_date']) ? $_REQUEST['to_date'] : ''),
                                'label' => __('Expiry date', 'woocommerce'),
                                'placeholder' => 'YYYY-MM-DD',
                                'description' => ' The date till which the rule would be valid. You can leave it blank if you wish the rule to be applied forever or would like to end it manually.',
                                'desc_tip' => true,
                                'class' => 'date-picker',
                                'custom_attributes' => array(
                                    'pattern' => apply_filters('woocommerce_date_input_html_pattern', '(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-[0-9]{4}'),
                                ),
                            ));
                            ?>
                        </div>
                        <div class="options_group" id="restriction_tab" style="display: none;">
                            <?php //premium ?>
                            <?php
                            woocommerce_wp_text_input(array(
                                'id' => 'email_ids',
                                'label' => __('Allowed Email Ids', 'woocommerce'),
                                'placeholder' => 'Enter Email ids seperated by commas',
                                'description' => __('Enter Email ids seperated by commas, for which you want to allow this rule. and leave blank to allow for all', 'woocommerce'),
                                'type' => 'text',
                                'desc_tip' => true,
                                'value' => !empty($_REQUEST['email_ids']) ? $_REQUEST['email_ids'] : '',
                                'custom_attributes'=>array('disabled'=>'disabled')
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'prev_order_count',
                                'label' => __('Minimum number of orders (previous orders)', 'woocommerce'),
                                'description' => __('Minimum count of preivious orders required for this rule to be executed', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'custom_attributes' => array(
                                    'step' => 1,
                                    'min' => 0,
                                    'disabled'=>'disabled'
                                ),
                                'value' => !empty($_REQUEST['prev_order_count']) ? $_REQUEST['prev_order_count'] : ''
                            ));
                            woocommerce_wp_text_input(array(
                                'id' => 'prev_order_total_amt',
                                'label' => __('Minimum total spending (previous orders)', 'woocommerce'),
                                'description' => __('Minimum amount the user has spent till now for the rule to execute. total calculated from all previous orders', 'woocommerce'),
                                'type' => 'number',
                                'desc_tip' => true,
                                'class' => 'short',
                                'custom_attributes' => array(
                                    'step' => 1,
                                    'min' => 0,
                                    'disabled'=>'disabled'
                                ),
                                'value' => !empty($_REQUEST['prev_order_total_amt']) ? $_REQUEST['prev_order_total_amt'] : ''
                            ));
                            ?>
                        </div>

                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
</div>
