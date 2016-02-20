<?php
/**
 * make the template redirect
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_filter('the_content', 'buddyforms_attached_page_content', 10, 1);

function buddyforms_attached_page_content($content){
    global $wp_query, $buddyforms;

    remove_filter('the_content', 'buddyforms_attached_page_content', 10, 1);
    remove_filter('the_content', 'buddyforms_hierarchical_display_child_posts', 50, 1); //this is a dirty fix and needs to be addressed
    if(is_admin())
        return $content;

    if(!isset($buddyforms))
        return $content;

    $new_content = $content;
    if (isset($wp_query->query_vars['bf_action'])) {
        $form_slug = '';
        if(isset($wp_query->query_vars['bf_form_slug']))
            $form_slug = $wp_query->query_vars['bf_form_slug'];

        $post_id = '';
        if(isset($wp_query->query_vars['bf_post_id']))
            $post_id = $wp_query->query_vars['bf_post_id'];

        $parent_post_id = '';
        if(isset($wp_query->query_vars['bf_parent_post_id']))
            $parent_post_id = $wp_query->query_vars['bf_parent_post_id'];

        if(!isset($buddyforms[$form_slug]['post_type']))
            return $content;

        $post_type = $buddyforms[$form_slug]['post_type'];

        $args = array(
            'form_slug'     => $form_slug,
            'post_id'       => $post_id,
            'parent_post'	=> $parent_post_id,
            'post_type'     => $post_type
        );

        if($wp_query->query_vars['bf_action'] == 'create' || $wp_query->query_vars['bf_action'] == 'edit' || $wp_query->query_vars['bf_action'] == 'revision'){
            ob_start();
            buddyforms_create_edit_form($args);
            $bf_form = ob_get_contents();
            ob_clean();
            $new_content = $bf_form;
        }
        if($wp_query->query_vars['bf_action'] == 'view'){
            ob_start();
            buddyforms_the_loop($args);
            $bf_form = ob_get_contents();
            ob_clean();
            $new_content = $bf_form;
        }

    } elseif(isset($wp_query->query_vars['pagename'])){

        if($buddyforms) : foreach ($buddyforms as $key => $buddyform) {

            if(isset($buddyform['attached_page']) && $wp_query->query_vars['pagename'] == $buddyform['attached_page'])
                $post_data = get_post($buddyform['attached_page'], ARRAY_A);

            if(isset($post_data['post_name']) && $post_data['post_name'] == $wp_query->query_vars['pagename']){
                $args = array(
                    'form_slug' => $buddyform['slug'],
                );
                ob_start();
                buddyforms_the_loop($args);
                $bf_form = ob_get_contents();
                ob_clean();
                $new_content = $bf_form;
            }
        }
    endif;
    }

    add_filter('the_content', 'buddyforms_attached_page_content', 10, 1);

    return $new_content;

}