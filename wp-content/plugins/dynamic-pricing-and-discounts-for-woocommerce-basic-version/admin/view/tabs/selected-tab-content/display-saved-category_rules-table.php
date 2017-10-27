<?php
$allrules = array();
$allrules = get_option("xa_dp_rules", array());
if (isset($allrules['category_rules'])) {
    $allrules = $allrules['category_rules'];
    ?>
    <table class="display_all_rules table widefat" style=" border-collapse: collapse;width:100%;">
        <thead style="font-size: smaller;background-color: lightgrey;">
            <tr style=" border-bottom-style: solid; border-bottom-width: thin;">
            <th class="xa-table-header icon-move" style="font-size: 10px;padding:3px;word-wrap: break-word; width: 5px;">Drag</th>
            <th class="xa-table-header" style="padding:3px;width: 60px;"><?php esc_attr_e('Options', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style="word-wrap: break-word; width: 10px;"><?php esc_attr_e('Rule no.', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Offer Name', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Category Name', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Check on', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Min', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Max', 'eh-dynamic-pricing-discounts'); ?></th>
            <th style="word-wrap:break-word;width:5px;" class="xa-table-header" ><?php esc_attr_e('Discount Type', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Value', 'eh-dynamic-pricing-discounts'); ?></th>
            <th style="word-wrap: break-word;width: 10px;" class="xa-table-header" ><?php esc_attr_e('Max Discount', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('Allowed Roles', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('From Date', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style=""><?php esc_attr_e('To Date', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style="padding-right: 4px;padding-left: 4px;"><?php esc_attr_e('Adjustment', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style="padding-right: 4px;padding-left: 4px;"><?php esc_attr_e('For Emails', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style="padding-right: 4px;padding-left: 4px;"><?php esc_attr_e('Min Order Count', 'eh-dynamic-pricing-discounts'); ?></th>
            <th class="xa-table-header" style="padding-right: 4px;padding-left: 4px;"><?php esc_attr_e('Min Order Amt', 'eh-dynamic-pricing-discounts'); ?></th>
            
            </tr>
        </thead>
        <tbody style="font-size: smaller;">
            <?php
            $customclass = 'saved_row';
            if(empty($allrules))
            {
                echo '<tr class="' . $customclass . ' " style="border-bottom:lightgrey; border-bottom-style: solid; border-bottom-width: thin;">';
                echo '<td colspan=20> There are no rules created to create a rule click the "Add New Rule" button on top left-hand side.</td>';             
            }                     
            foreach ($allrules as $key => $value) {
                echo '<tr class="saved_row"  style="border-bottom: lightgrey; border-bottom-style: solid; border-bottom-width: thin;"><td class="icon-move " style="width:10px;cursor: move"></td>';
                echo '<td style="margin-left: 0px; margin-right: 0px; padding-left: 0px; padding-right: 0px;">';
                echo '<button class="editbtn"   type="submit" name="edit" value="' . $key . '" ></button>';
                echo '<button class="deletebtn" type="submit" name="delete" value="' . $key . '" ></button>';
                echo "</td>";

                echo '<td>' . $key . '</td>';
                if (!isset($value['adjustment'])) {
                    $value['adjustment'] = null;
                }
                foreach ($value as $key2 => $value2) {
                    if ($key2 == 'category_id') {
                            if(!is_array($value2)) $value2=array($value2);
                            echo '<td style="width:10%;" class="category_name" >';
                            foreach($value2 as $cid)
                            {
                                echo '<span class="highlight">' . get_product_category_by_id($cid) . '</span></br>';
                            }                        
                            echo '</td>';
                        } elseif ($key2 == 'offer_name') {
                        echo "<td style=\"width:15%;\">";
                        if (!empty($value2))
                            echo $value2;
                        else
                            echo '  -  ';
                        echo "</td>";
                    }
                    elseif ($key2 == 'check_on') {
                        echo "<td style=\" \">";
                        $value2 = str_replace('TotalQuantity', 'Total units', $value2);
                        $value2 = str_replace('Quantity', 'No. of Items', $value2);

                        if (!empty($value2))
                            esc_attr_e($value2, 'eh-dynamic-pricing-discounts');
                        else
                            echo '  -  ';
                        echo "</td>";
                    }
                    else {
                        echo "<td style=\" \">";
                        if (!empty($value2))
                            esc_attr_e($value2, 'eh-dynamic-pricing-discounts');
                        else
                            echo '  -  ';
                        echo "</td>";
                    }
                }

                echo '</tr>';
            }
            ?>
        </tbody>
        <tfoot></tfoot>
    </table>

    <?php
}