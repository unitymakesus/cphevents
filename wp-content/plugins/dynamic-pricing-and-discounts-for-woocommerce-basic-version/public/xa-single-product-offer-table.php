<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $offers, $product;
$rules_validator = new XA_RulesValidator('all_match', true);
$valid_rules = $rules_validator->getValidRulesForProduct($product, xa_get_pid($product), 1);  // this will calculate all valid rules and assign to $offers

if (!empty($valid_rules)) {
    foreach ($valid_rules as $key => $rule) {
        $offers[$key] = $rule['offer_name'];
    }
}

if (!empty($offers)) {
    ?>

    <style>
        .xa_offer_header{
            text-align: center;
            border-bottom: 1px solid #e1e1e1;
            line-height: 10px;
            font-weight: 500 !important;
        }
    </style>
    <div class="xa_offer_table">
        <div class="xa_offer_header"  ><h4 style="font-weight: 500;"><?php echo apply_filters('offer_table_description_text', __('Offers', 'eh-dynamic-pricing-discounts')); ?></h4></div>

        <div class="xa_offer_content">
            <ul class="xa_offer_table_list">
                <?php
                foreach ($offers as $type_rule_no => $offer) {
                    ?>
                    <li class="<?php echo $type_rule_no ?>_item  xa_offer_table_list_item "><span class="xa_offer_item_span"><?php echo $offer ?></span></li>
                        <?php
                    }
                    ?>
            </ul>
        </div>
    </div>
<?php } ?>
