<?php

function pmwi_pmxi_after_xml_import($import_id)
{
	$import = new PMXI_Import_Record();

	$import->getById($import_id);

    // Re-count WooCommerce Terms
	if ( ! $import->isEmpty() and in_array($import->options['custom_type'], array('product', 'product_variation')))
	{
	    $recount_terms_after_import = true;
        $recount_terms_after_import = apply_filters('wp_all_import_recount_terms_after_import', $recount_terms_after_import, $import_id);
	    if ( $recount_terms_after_import && ( ($import->options['create_new_records'] and $import->options['is_keep_former_posts'] == 'yes') or ($import->options['is_keep_former_posts'] == 'no' and ( $import->options['update_all_data'] == 'yes' or $import->options['is_update_categories'])))) {
            $product_cats = get_terms( 'product_cat', array( 'hide_empty' => false, 'fields' => 'id=>parent' ) );
            _wc_term_recount( $product_cats, get_taxonomy( 'product_cat' ), true, false );
            $product_tags = get_terms( 'product_tag', array( 'hide_empty' => false, 'fields' => 'id=>parent' ) );
            _wc_term_recount( $product_tags, get_taxonomy( 'product_tag' ), true, false );
        }
        $maybe_to_delete = get_option('wp_all_import_products_maybe_to_delete_' . $import_id);
        if ( ! empty($maybe_to_delete)){
            foreach ($maybe_to_delete as $pid){
                $children = get_posts( array(
                    'post_parent' 	=> $pid,
                    'posts_per_page'=> -1,
                    'post_type' 	=> 'product_variation',
                    'fields' 		=> 'ids',
                    'orderby'		=> 'ID',
                    'order'			=> 'ASC',
                    'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
                ) );

                if ( empty($children) ){
                    wp_delete_post($pid, true);
                }
            }
            delete_option('wp_all_import_products_maybe_to_delete_' . $import_id);
        }

        delete_option('wp_all_import_not_linked_products_' . $import_id);
	}
}