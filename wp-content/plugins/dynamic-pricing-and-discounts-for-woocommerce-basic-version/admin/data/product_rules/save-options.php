<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!defined('WPINC')) {
    die;
}
if (!empty($_GET['offer_name']) && (($_GET['rule_on'] == 'products' && !empty($_GET['product_id'])) || ($_GET['rule_on'] == 'categories' && !empty($_GET['category_id'])) || ($_GET['rule_on'] == 'cart' ) ) && !empty($_GET['check_on']) && !empty($_GET['min']) && !empty($_GET['discount_type']) && !empty($_GET['value']) && !isset($_GET['edit'])) {

    $dummy_settings['product_rules'] = array();
    $dummy_settings['combinational_rules'] = array();
    $dummy_settings['category_rules'] = array();
    $dummy_settings['cart_rules'] = array();
    $dummy_settings['buy_get_free_rules'] = array();
    if ($_GET['rule_on'] == 'products' && isset($_GET['product_id']) && !empty($_GET['product_id'])) {
        if (is_wc_version_gt_eql('2.7')) {
            $products_ids = $_GET['product_id'];
        } else {
            $products_ids = explode(',', $_GET['product_id']);
        }

        $categories = null;
    } elseif ($_GET['rule_on'] == 'categories' && isset($_GET['category_id']) && !empty($_GET['category_id'])) {
        $products_ids = null;
        $categories = $_GET['category_id'];
    } else {
        $products_ids = null;
        $categories = null;
    }



    $prev_data = get_option('xa_dp_rules', $dummy_settings);
    if (!isset($prev_data[$active_tab]) || sizeof($prev_data[$active_tab]) == 0) {
        $prev_data[$active_tab][1] = array('offer_name' => sanitize_text_field($_GET['offer_name']),
            'rule_on' => $_GET['rule_on'],
            'product_id' => $products_ids,
            'category_id' => $categories,
            'check_on' => sanitize_text_field($_GET['check_on']),
            'min' => sanitize_text_field($_GET['min']),
            'max' => !empty($_GET['max']) ? $_GET['max'] : NULL,
            'discount_type' => sanitize_text_field($_GET['discount_type']),
            'value' => sanitize_text_field($_GET['value']),
            'max_discount' =>  NULL,
            'allow_roles' => sanitize_text_field(!empty($_GET['allow_roles']) ? $_GET['allow_roles'] : NULL),
            'from_date' => !empty($_GET['from_date']) ? $_GET['from_date'] : NULL,
            'to_date' => !empty($_GET['to_date']) ? $_GET['to_date'] : NULL,
            'adjustment' =>  NULL,
            'email_ids' =>  NULL,
            'prev_order_count' =>  NULL,
            'prev_order_total_amt' => NULL,
            'repeat_rule' =>  NULL,
        );
    } else {
        $prev_data[$active_tab][] = array('offer_name' => sanitize_text_field($_GET['offer_name']),
            'rule_on' => $_GET['rule_on'],
            'product_id' => $products_ids,
            'category_id' => $categories,
            'check_on' => sanitize_text_field($_GET['check_on']),
            'min' => sanitize_text_field($_GET['min']),
            'max' => !empty($_GET['max']) ? $_GET['max'] : NULL,
            'discount_type' => sanitize_text_field($_GET['discount_type']),
            'value' => sanitize_text_field($_GET['value']),
            'max_discount' =>  NULL,
            'allow_roles' => sanitize_text_field(!empty($_GET['allow_roles']) ? $_GET['allow_roles'] : NULL),
            'from_date' => !empty($_GET['from_date']) ? $_GET['from_date'] : NULL,
            'to_date' => !empty($_GET['to_date']) ? $_GET['to_date'] : NULL,
            'adjustment' =>  NULL,
            'email_ids' =>  NULL,
            'prev_order_count' =>  NULL,
            'prev_order_total_amt' => NULL,
            'repeat_rule' =>  NULL,
            );
    }

    update_option('xa_dp_rules', $prev_data);
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e('Saved Successfully', 'eh-dynamic-pricing-discounts'); ?></p>
    </div>
    <?php
    wp_safe_redirect(admin_url('admin.php?page=dynamic-pricing-main-page&tab=' . $active_tab));
} else {
    echo '<div class="notice notice-error is-dismissible">';
    echo '<p>' . _e('Please Enter All Fields!! Then Save', 'eh-dynamic-pricing-discounts') . '</p> </div>';
}
