<h2><?php esc_attr_e('Dynamic Pricing Main Page', 'eh-dynamic-pricing-discounts'); ?></h2>
<script>
    jQuery(function () {
        jQuery('.woocommerce-help-tip').tipTip({
            'attribute': 'data-tip',
            'fadeIn': 50,
            'fadeOut': 50,
            'delay': 200
        });
        jQuery('#eh_rule_form').on('submit', function () {
            const min = parseFloat(jQuery('#min').val());
            const max = parseFloat(jQuery('#max').val());
            const value = parseFloat(jQuery('#value').val());
            if (min <= 0)
            {
                alert('The Value of ' + jQuery("[for=min]").text() + ' should be greater than zero');
            } else if (max < min)
            {
                alert('The Value of ' + jQuery("[for=max]").text() + ' should be greater than ' + jQuery("[for=min]").text());
            } else if (value <= 0)
            {
                alert('The Value of ' + jQuery("[for=value]").text() + ' should be greater than zero');

            } else
            {
                return true;
            }
            return false;
        });
    });
    function select(obj, selector)
    {
        let all_links = document.querySelectorAll('.active');
        for (let dv of all_links) {
            dv.classList.remove('active');
        }
        obj.parentElement.className += ' active';
        let div = document.querySelector(selector);
        let alldiv = document.querySelectorAll('.options_group');
        for (let dv of alldiv) {
            dv.style.display = 'none';
        }
        if (div.style.display == "none") {
            div.style.display = "block";
        } else
        {
            div.style.display = "none";
        }
    }
</script>
<style>
    .xa_link{
            cursor: pointer;
    }
    .woocommerce_options_panel label{
        width:210px;
    }
    #tiptip_content{
        max-width: 500px;
        width:auto;
    }

    .deletebtn{
        background: url(<?php echo plugins_url('/dynamic-pricing-and-discounts-for-woocommerce-basic-version/img/del.png'); ?>) 10px 10px no-repeat;
        width: 15px;
        height: 15px;
        background-size: 100%;
        background-position: top left;
        border: none;
        margin-left:10px;
        cursor:pointer;
    }
    .editbtn{
        background: url(<?php echo plugins_url('/dynamic-pricing-and-discounts-for-woocommerce-basic-version/img/edit.png'); ?>) 10px 10px no-repeat;
        width: 15px;
        height: 15px;
        background-size: 100%;
        background-position: top left;
        border: none;
        margin-left:10px;
        cursor:pointer;
    }
    .add_new{
        /*    background: url(http://localhost/wordpress/wp-content/plugins/eh-dynamic-pricing-discounts/img/add_new.png) 10px 10px no-repeat;
          width: 50px;
          height: 50px;
          background-size: 100%;
          background-position: top left;
          border: none;
          margin-left:10px;
          cursor:pointer; */
    }
</style>
<div class = "wrap" style = "margin: auto;">
    <?php
    $rule_tab=false;
    global $on_plugin_page;
    global $thepostid;
    $thepostid = 99999999999;
    $on_plugin_page = true;
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (isset($_REQUEST['tab']) && !empty($_REQUEST['tab'])) {
        $active_tab = $_REQUEST['tab'];
    } else {
        $active_tab = 'No Rules Selected';
    }
    

    if (!empty($_REQUEST['deletesuccess'])) {
        echo '<div class="notice notice-warning inline is-dismissible"><p></br><lable>Deleted Successfully !!</p></div>';
    }
    $settings = get_option('xa_dynamic_pricing_setting');
    $execution_order = array('product_rules',
                            'category_rules',
                            'combinational_rules',
                            'cat_combinational_rules',
                            'cart_rules',
                            'buy_get_free_rules');
    if (in_array($active_tab, $execution_order)) {
        $rule_tab = true;
    }
    if ($active_tab == 'No Rules Selected' && !empty($settings['execution_order'])) {
        $active_tab = current($settings['execution_order']);
        $rule_tab =true ;
    }
    if (!empty($_REQUEST['delete'])) {
        $prev_data = get_option('xa_dp_rules');
        unset($prev_data[$active_tab][$_REQUEST['delete']]);
        update_option('xa_dp_rules', $prev_data);
        wp_redirect(admin_url('admin.php?page=dynamic-pricing-main-page&tab=' . $_REQUEST['tab'] . '&deletesuccess'));
    }
    //add_thickbox();
    ?>
    <h2 class="nav-tab-wrapper">        
        <a href="?page=dynamic-pricing-main-page<?php echo !empty($execution_order) ? "&tab=" . current($execution_order) : ''; ?>" class="nav-tab <?php echo $rule_tab == true ? 'nav-tab-active' : ''; ?>"><?php _e('Discount Rules', 'eh-dynamic-pricing-discounts'); ?></a>
        <a href="?page=dynamic-pricing-main-page&tab=settings_page" class="nav-tab <?php echo $active_tab == 'settings_page' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'eh-dynamic-pricing-discounts'); ?></a>
<!--        <a href="?page=dynamic-pricing-main-page&tab=licence" class="nav-tab  thickbox <?php echo $active_tab == 'import_export' ? 'nav-tab-active' : ''; ?>"><?php _e('Import/Export', 'eh-dynamic-pricing-discounts'); ?></a>  -->
        <a href="?page=dynamic-pricing-main-page&tab=licence" style="color:red;" class="nav-tab  thickbox <?php echo $active_tab == 'licence' ? 'nav-tab-active' : ''; ?>"><?php _e('Go Premium', 'eh-dynamic-pricing-discounts'); ?></a>  
        
    </h2>
    <div class="clear"></div>
    </br>
        <?php if (!empty($rule_tab) && $rule_tab) { ?>
        <ul class="subsubsub">
            <?php
            foreach ($execution_order as $key => $tabkey) {
                switch ($tabkey) {
                    case 'product_rules':$tab_name = __('Product Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    case 'combinational_rules':$tab_name = __('Combinational Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    case 'category_rules':$tab_name = __('Category Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    case 'cat_combinational_rules':$tab_name = __('Category Combinational Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    case 'cart_rules':$tab_name = __('Cart Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    case 'buy_get_free_rules':$tab_name = __('BOGO Rules', 'eh-dynamic-pricing-discounts');
                        break;
                    default:
                        break;
                }
                $active_class = '';
                if ($active_tab == $tabkey)
                {
                    $active_class = 'current';                    
                }
                $premium='';
                $link='?page=dynamic-pricing-main-page&tab=' . $tabkey ;
                if(!in_array($tabkey,array('product_rules','category_rules')))
                {
                    $active_class="thickbox";
                    $link='?page=dynamic-pricing-main-page&tab=licence'  ;
                    $premium='</a><span class="super">Premium</span>';
                }
                

                $delimiter = ($key < count($execution_order) - 1) ? '  |  ' : ' ';
                echo "<li>";
                echo '<a  href="'.$link.'"  class="' . $active_class.'">' . $tab_name .$premium . $delimiter;
                echo "</li>";
            }
            ?>
        </ul>
    <?php
    }elseif ($active_tab == 'No Rules Selected') {
        echo "</br></br>No Rules Available!! .  <a href='admin.php?page=dynamic-pricing-main-page&tab=settings_page' >Go to Settings Page and Enable Rules </a></br></r>";
    }
    ?>
    <br class="clear">
    <style>
        .super{
            vertical-align: super;
            font-size:x-small;
            }
    </style>
    <div id="col-container2">
        <div class="col-wrap">
            <div class="inside">
                <div style="">
                    <button style="margin: 10px 0px;<?php if (!empty($_REQUEST['edit']) || in_array($active_tab,array('settings_page','import_export','licence','No Rules Selected'))) echo "display:none;" ?> <?php echo $active_tab; ?>" class="add_new add_new_rule_btn button button-primary" onclick="xa_show_form()" >Add New Rule</button> </div>
                <form method="get" action="" id="eh_rule_form" style=<?php
                if (!empty($_REQUEST['edit']) || isset($_REQUEST['update']) || $active_tab == 'settings_page' || $active_tab == 'import_export' || !$rule_tab || $active_tab == 'licence') {
                    
                } else {
                    echo '"display:none"';
                }
                ?>
                      >
                          <?php
                          if (isset($_REQUEST['edit'])) {
                              wp_nonce_field('update_rule_' . $_REQUEST['edit']);
                          } else {
                              wp_nonce_field('save_rule');
                          }
                          ?>
                    <input type="hidden" id="page" name="page" value="dynamic-pricing-main-page">
                    <input type="hidden" id="tab" name="tab" value="<?php
                    if (isset($_REQUEST['tab']) && !empty($_REQUEST['tab'])) {
                        echo $_REQUEST['tab'];
                    } else {
                        echo $active_tab;
                    }
                    ?>">

                    <?php
                    do_action('my_admin_notification');
                    
                    if ($active_tab == 'category_rules') {
                        require('selected-tab-content/category-rule-tab.php');
                    } elseif ($active_tab == 'settings_page') {
                        require('selected-tab-content/settings_page-tab.php');
                    } elseif ($active_tab == 'import_export') { 
                        require('selected-tab-content/import_export-tab.php');
                    } elseif ($active_tab == 'licence') { 
                        require('market.php');
                    } elseif ($active_tab == 'product_rules') {    // product rule tab
                        require('selected-tab-content/product-rule-tab.php');
                    }
                    if ($active_tab == 'import_export' && !empty($_REQUEST['action']) && $_REQUEST['action'] == "delete_all_rules") {
                        if (is_admin()) {

                            $dummy_settings['product_rules'] = array();
                            $dummy_settings['combinational_rules'] = array();
                            $dummy_settings['cat_combinational_rules'] = array();
                            $dummy_settings['category_rules'] = array();
                            $dummy_settings['cart_rules'] = array();
                            $dummy_settings['buy_get_free_rules'] = array();
                            update_option('xa_dp_rules', $dummy_settings);
                            ?>
                            <div class="notice notice-success is-dismissible" >
                                <p><?php _e('All Rules Deleted Successfully!', 'eh-dynamic-pricing-discounts'); ?></p>
                            </div>
                            <?php
                        }
                    }
                    if ($active_tab != 'import_export' && $rule_tab) {
                        ?>
                        <p class="submit" style="<?php if ($active_tab == 'settings_page') echo "display:none;"; ?>">
                            <button type="submit" name="update" id="update" class="button button-primary" value="<?php echo!empty($_REQUEST['edit']) ? $_REQUEST['edit'] : '';
                        ?>"> 
                                        <?php
                                        if (isset($_REQUEST['edit'])) {
                                            _e('Update Rule', 'eh-dynamic-pricing-discounts');
                                            ;
                                        } else {
                                            _e('Save Rule', 'eh-dynamic-pricing-discounts');
                                        }
                                        ?>
                            </button>
                        <?php } 
                        
                        if (!in_array($active_tab,array('settings_page','import_export','licence','No Rules Selected'))) {
                        ?>                            
                        <button name="cancel_btn" style="<?php
                        if (empty($_REQUEST['edit'])) {
                            echo "display:none;";
                        }
                        ?>" id="cancel_btn" class="button button-primary" onclick="return hide_rule_form()"hide_rule_form >Cancel</button>
                        <?php } ?>
                    </p>
                </form>

                <form name="alter_display_form" method="get" action="">			<!--Displays Table List Of Saved Rules--->
                    <input type="hidden" name="page" value="dynamic-pricing-main-page">
                    <input type="hidden" id="tab" name="tab" value="<?php
                    if (isset($_REQUEST['tab']) && !empty($_REQUEST['tab'])) {
                        echo $_REQUEST['tab'];
                    } else {
                        echo $active_tab;
                    }
                    ?>">
                           <?php
                           if (!isset($_REQUEST["tab"])) {
                               $_REQUEST["tab"] = $active_tab;
                           }
                           if (isset($_REQUEST["tab"]) && !empty($_REQUEST["tab"])) {
                               if (file_exists(plugin_dir_path(__FILE__) . '/selected-tab-content/display-saved-' . $_REQUEST["tab"] . '-table.php') === true)
                                   include('selected-tab-content/display-saved-' . $_REQUEST["tab"] . '-table.php');
                           }
                           ?>
                </form>

            </div>
        </div>
    </div>
</div>
<script>
    function xa_show_form()
    {
        jQuery('#eh_rule_form').show();
        jQuery('.add_new_rule_btn').hide();
        jQuery('#cancel_btn').show();        
        return false;
    }
    function hide_rule_form()
    {
        jQuery('#eh_rule_form').hide();
        jQuery('.add_new_rule_btn').show();
        jQuery('#update').val('');
        jQuery('#update').text('Save Rule');

        return false;
    }
    jQuery(document).ready(function ()
    {
        jQuery(".date-picker").datepicker({
            dateFormat: "dd-mm-yy",minDate: 0
        });
        jQuery('#more_options').on('click', function () {
            jQuery('.more_options').toggle();
            if (jQuery('.more_options').is(":hidden"))
            {
                jQuery('#more_options').html('<h4><a>More Options+</a></h4>');
            } else
            {
                jQuery('#more_options').html('<h4><a>More Options-</a></h4>');
            }
        });

        jQuery('.more_options').hide();
        jQuery('tbody').sortable({
            placeholder: "ui-widget-shadow",
            handle: 'td.icon-move',
            update: function () {
                xa_update_rules_arrangement();
            }
        });
    });

    function xa_update_rules_arrangement() {
        var rules_order = '';
        var new_index = 1;
        jQuery('.saved_row').each(function ($index, $element) {
            var current_row_no = jQuery($element).find('td:nth-child(3)').text();
            jQuery($element).find('td:nth-child(3)').text(new_index++);
            if (rules_order)
                rules_order = rules_order + ',' + current_row_no;
            else
                rules_order = current_row_no;
        });
        //alert(rules_order);
        jQuery.post(
                ajaxurl,
                {
                    'action': 'update_rules_arrangement',
                    'rules-order': rules_order,
                    'rules-type': '<?php echo !empty($_REQUEST['tab'])?$_REQUEST['tab']:current($execution_order); ?>',
                    'xa-nonce': '<?php echo wp_create_nonce('update_rules_arrangement'); ?>'
                },
                function (response) {
                    //alert(response);
                }
        );

        return false;
    }

</script>
<style>
    td.icon-move{
        background-image: url('<?php echo plugins_url('dynamic-pricing-and-discounts-for-woocommerce-basic-version/jquery-ui/drag2.png'); ?>');
        background-size: auto auto ;
        background-position: center;
        background-repeat: no-repeat;
    }
</style>