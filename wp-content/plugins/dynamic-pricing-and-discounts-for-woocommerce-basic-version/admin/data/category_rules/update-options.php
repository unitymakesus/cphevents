<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!defined('WPINC')) {
    die;
}
    if (isset($_REQUEST['offer_name']) && !empty($_REQUEST['offer_name']) && isset($_REQUEST['check_on']) && !empty($_REQUEST['check_on']) && isset($_REQUEST['min']) && !empty($_REQUEST['min']) && isset($_REQUEST['discount_type']) && !empty($_REQUEST['discount_type']) && isset($_REQUEST['value']) && !empty($_REQUEST['value'])) {


        $prev_data = get_option('xa_dp_rules');
        $prev_data[$active_tab][$_REQUEST['update']] = array('offer_name' => sanitize_text_field($_REQUEST['offer_name']),
            'category_id' => $_REQUEST['category_id'],
            'check_on' => sanitize_text_field($_REQUEST['check_on']),
            'min' => sanitize_text_field($_REQUEST['min']),
            'max' => !empty($_REQUEST['max']) ? $_REQUEST['max'] : NULL,
            'discount_type' => sanitize_text_field($_REQUEST['discount_type']),
            'value' => sanitize_text_field($_REQUEST['value']),
            'max_discount' =>  NULL,
            'allow_roles' =>  'all',
            'from_date' =>  NULL,
            'to_date' =>  NULL,
            'adjustment' =>  NULL,
            'email_ids' =>  NULL,
            'prev_order_count' =>  NULL,
            'prev_order_total_amt' =>  NULL,
        );
        update_option('xa_dp_rules', $prev_data);
        $_REQUEST = array();
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Updated Successfully', 'eh-dynamic-pricing-discounts'); ?></p>
        </div>
        <?php
        wp_safe_redirect(admin_url('admin.php?page=dynamic-pricing-main-page&tab=' . $active_tab));
    } else {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p>' . _e('Please Enter All Fields ,Then Try To Update!!', 'eh-dynamic-pricing-discounts') . '</p> </div>';
    }
