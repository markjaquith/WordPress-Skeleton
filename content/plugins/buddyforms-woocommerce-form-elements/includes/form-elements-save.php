<?php

add_action('buddyforms_update_post_meta','test_buddyforms_update_post_meta',10,2);


function test_buddyforms_update_post_meta($customfield, $post_id){
     global $bf_product_attributes;

    if(!isset($customfield['taxonomy']))
        return;

    if(substr($customfield['taxonomy'], 0, 3) != 'pa_')
        return;

    $bf_product_attributes[$customfield['taxonomy']] = $customfield['taxonomy'];


}


add_action('buddyforms_update_post_meta', 'buddyforms_woocommerce_updtae_post_meta', 99, 2);
function buddyforms_woocommerce_updtae_post_meta($customfield, $post_id){
    global $bf_wc_save_meta, $bf_wc_save_gallery;

    if( $customfield['type'] == 'woocommerce' )
        $bf_wc_save_meta = 'yes';


    if( $customfield['type'] == 'product-gallery')
        $bf_wc_save_gallery = 'yes';

}

add_action('buddyforms_after_save_post', 'buddyforms_woocommerce_updtae_wc_post_meta', 99, 1);
function buddyforms_woocommerce_updtae_wc_post_meta($post_id){
    global $post, $bf_wc_save_gallery, $bf_wc_save_meta, $bf_product_attributes;

    if($bf_wc_save_meta == 'yes')
        BF_WC_Meta_Box_Product_Data::save($post_id, $post);

    if($bf_wc_save_gallery == 'yes')
        WC_Meta_Box_Product_Images::save($post_id, $post);


    if(!isset($bf_product_attributes))
        return;

    $product_attributes = get_post_meta($post_id, '_product_attributes', true);
    $product_attributes_count = count($product_attributes);


    foreach ($bf_product_attributes as $key => $pa_taxonomy){
        if(is_array($product_attributes) && array_key_exists($pa_taxonomy,$product_attributes))
            continue;


        $product_attributes[$pa_taxonomy] = Array(
            'name' => $pa_taxonomy,
            'value' => '',
            'position' => $product_attributes_count+1,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 1
        );
    }

    update_post_meta($post_id, '_product_attributes', $product_attributes);

}

add_action('buddyforms_after_save_post', 'buddyforms_woocommerce_updtae_visibility', 999, 1);
function buddyforms_woocommerce_updtae_visibility($post_id) {

    if(get_post_status($post_id) == 'publish');
        update_post_meta($post_id, '_visibility', 'visible');

}

