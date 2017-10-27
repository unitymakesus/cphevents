<?php
if ( !defined( 'ABSPATH' ) || ! defined( 'YITH_YWDPD_VERSION' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Template For Pricing Rules Free
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts
 * @since   1.0.0
 * @author  Yithemes
 */

$id   = $this->_panel->get_id_field( $option['id'] );
$name = $this->_panel->get_name_field( $option['id'] );
?>
<p>
	<input id="yith-ywdpd-add-section" type="text" class="ywdpd-section-title" value="" />
	<a href="" id="yith-ywdpd-add-section-button" class="button-secondary" data-section_id="<?php echo $id ?>" data-section_name="<?php echo $name ?>"><?php _e( 'Add new rule', 'yit' ) ?></a>
    <span class="ywdpd-error-input-section"></span>
</p>


<div id="<?php echo $id ?>-container" class="ywdpd-sections-group ui-sortable" <?php if ( isset( $option['deps'] ) ): ?> data-field="<?php echo $id ?>" data-dep="<?php echo $this->_panel->get_id_field( $option['deps']['ids'] ) ?>" data-value="<?php echo $option['deps']['values'] ?>" <?php endif ?>>

    <?php if ( is_array( $db_value ) ) :

        ?>

        <?php foreach ( $db_value as $key => $value ) :
            $suffix_id = $id .'['. $key .']';
            $suffix_name = $name .'['. $key .']';
            ?>

            <div class="ywdpd-section section-<?php echo $key ?>">

                <div class="section-head">
                    <span class="ywdpd-active <?php echo  ( $db_value[$key]['active'] == 'yes' ) ? 'activated' : '' ?>" data-section="<?php echo $key ?>"></span>
                    <?php echo  $db_value[$key]['description'] ?>
                    <input type="hidden" name="<?php echo $suffix_name.'[description]' ?>" id="<?php echo $suffix_id.'[description]' ?>" value="<?php echo $db_value[$key]['description'] ?>">
                    <input type="hidden" name="<?php echo $suffix_name.'[active]' ?>" id="<?php echo $suffix_id.'[active]' ?>" class="active-hidden-field" value="<?php echo $db_value[$key]['active'] ?>">
                    <span class="ywdpd-remove" data-section="<?php echo $key ?>"></span>
                </div>

                <div class="section-body">
                    <table>
                        <tr>
                            <th>
                                <label for="<?php echo $suffix_id .'[apply_to]' ?>"><?php _e( 'Apply to', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></label>
                            </th>
                            <td>
                                <select name="<?php echo $suffix_name ?>[apply_to]" id="<?php echo $suffix_id .'[apply_to]' ?>" class="yith-ywdpd-eventype-select">
                                    <option value="all-products" <?php selected( 'all-products', $db_value[$key]['apply_to'] ) ?>><?php _e('All products', 'yith-woocommerce-dynamic-pricing-and-discounts') ?></option>
                                    <option value="categories" <?php selected( 'categories', $db_value[$key]['apply_to'] ) ?>><?php _e('Categories in list', 'yith-woocommerce-dynamic-pricing-and-discounts') ?></option>
                                </select>
                                <span class="desc-inline"><?php _e('Select the products to which applying the rule') ?></span>
                            </td>
                        </tr>

                        <tr class="deps-apply_to">
                            <th>
                                <label for="<?php echo $suffix_id .'[categories]' ?>"><?php _e( 'Categories', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></label>
                            </th>
                            <td>
                               <select name="<?php echo $suffix_name ?>[categories][]" class="chosen ajax_chosen_select_categories" multiple="multiple" id="<?php echo $suffix_id .'[categories]' ?>"  data-placeholder="<?php _e('Search for a category','yith-woocommerce-dynamic-pricing-and-discounts') ?>">
                                    <?php
                                    if( !empty( $db_value[ $key ]['categories'] ) ):
                                        foreach( $db_value[ $key ]['categories'] as $term_id  ):
                                            $current_category = get_term_by( 'term_id', $term_id, 'product_cat');
                                            ?>
                                            <option value="<?php echo $term_id  ?>"  selected="selected"><?php echo $current_category->name ?></option>
                                        <?php endforeach;
                                    endif ?>
                                </select>
                                <span class="desc-inline"><?php _e('Select the products to which applying the rule') ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="<?php echo $suffix_id .'[discount_amount]' ?>"><?php _e( 'Discount Amount', 'yith-woocommerce-dynamic-pricing-and-discounts' ); ?></label>
                            </th>
                            <td>
                                <table class="discount-rules">
                                    <tr>
                                        <th><?php _e('Minimum Quantity', 'yith-woocommerce-dynamic-pricing-and-discounts') ?></th>
                                        <th><?php _e('Discount Amount', 'yith-woocommerce-dynamic-pricing-and-discounts') ?></th>
                                    </tr>
                                    <tr>
                                        <td><input type="text" name="<?php echo $suffix_name.'[min_quantity]' ?>" id="<?php echo $suffix_id.'[min_quantity]' ?>" value="<?php echo isset( $db_value[ $key ]['min_quantity'] ) ? $db_value[ $key ]['min_quantity'] : '' ?>" placeholder="<?php _e('e.g. 5', 'yith-woocommerce-dynamic-pricing-and-discounts') ?>"></td>
                                        <td><input type="text" name="<?php echo $suffix_name.'[discount_amount]' ?>" id="<?php echo $suffix_id.'[discount_amount]' ?>" value="<?php echo isset( $db_value[ $key ]['discount_amount'] ) ? $db_value[ $key ]['discount_amount'] : '' ?>" placeholder="<?php _e('e.g. 50', 'yith-woocommerce-dynamic-pricing-and-discounts') ?>"></td>
                                    </tr>
                                </table>

                            </td>
                        </tr>


                    </table>
                </div>

            </div>

        <?php endforeach; ?>

    <?php endif; ?>
</div>