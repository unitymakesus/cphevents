<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!defined('WPINC')) {
    die;
}

    if (isset($_GET['offer_name']) && !empty($_GET['offer_name']) && isset($_GET['check_on']) && !empty($_GET['check_on']) && isset($_GET['min']) && !empty($_GET['min']) && isset($_GET['discount_type']) && !empty($_GET['discount_type']) && isset($_GET['value']) && !empty($_GET['value'])) {

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
        $prev_data = get_option('xa_dp_rules');
        $prev_data[$active_tab][$_GET['update']] = array('offer_name' => sanitize_text_field($_GET['offer_name']),
            'rule_on' => $_GET['rule_on'],
            'product_id' => $products_ids,
            'category_id' => $categories,
            'check_on' => sanitize_text_field($_GET['check_on']),
            'min' => sanitize_text_field($_GET['min']),
            'max' => !empty($_GET['max']) ? $_GET['max'] : NULL,
            'discount_type' => sanitize_text_field($_GET['discount_type']),
            'value' => sanitize_text_field($_GET['value']),
            'max_discount' => !empty($_GET['max_discount']) ? $_GET['max_discount'] : NULL,
            'allow_roles' => sanitize_text_field(!empty($_GET['allow_roles']) ? $_GET['allow_roles'] : NULL),
            'from_date' => !empty($_GET['from_date']) ? $_GET['from_date'] : NULL,
            'to_date' => !empty($_GET['to_date']) ? $_GET['to_date'] : NULL,
            'adjustment' => !empty($_GET['adjustment']) ? sanitize_text_field($_GET['adjustment']) : NULL,
            'email_ids' => !empty($_GET['email_ids']) ? sanitize_text_field($_GET['email_ids']) : NULL,
            'prev_order_count' => !empty($_GET['prev_order_count']) ? sanitize_text_field($_GET['prev_order_count']) : NULL,
            'prev_order_total_amt' => !empty($_GET['prev_order_total_amt']) ? sanitize_text_field($_GET['prev_order_total_amt']) : NULL,
            'repeat_rule' => !empty($_GET['repeat_rule']) ? sanitize_text_field($_GET['repeat_rule']) : NULL,
        );

        update_option('xa_dp_rules', $prev_data);
        $_GET = array();
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Updated Successfully', 'language'); ?></p>
        </div>
        <?php
        wp_safe_redirect(admin_url('admin.php?page=dynamic-pricing-main-page&tab=' . $active_tab));
    } else {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . _e('Please Enter All Fields ,Then Try To Update!!', 'eh-dynamic-pricing-discounts') . '</p> </div>';
    }
