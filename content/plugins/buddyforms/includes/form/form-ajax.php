<?php

add_action('wp_ajax_buddyforms_ajax_edit_post', 'buddyforms_ajax_edit_post');
function buddyforms_ajax_edit_post(){
    $post_id = $_POST['post_id'];
    $form_slug = get_post_meta($post_id, '_bf_form_slug', true);

    $args = Array(
        'post_id'   => $post_id,
        'form_slug' => $form_slug
    );
    echo buddyforms_create_edit_form( $args );
    die();

}

add_action('wp_ajax_buddyforms_ajax_process_edit_post', 'buddyforms_ajax_process_edit_post');
function buddyforms_ajax_process_edit_post(){
    global $buddyforms;

    if( isset($_POST['data'])){
        parse_str($_POST['data'], $formdata);
        $_POST = $formdata;
    }

    $args = buddyforms_process_post($formdata);

    if( $args['hasError'] ){

        if($args['form_notice'])
            $json['form_notice'] .= $args['form_notice'];

        if($args['error_message'])
            $json['form_notice'] .= $args['error_message'];

    } else {
        if(!empty($buddyforms[$_POST['form_slug']]['after_submit_message_text'])){
            $permalink = get_permalink($buddyforms[$args['form_slug']]['attached_page']);

            $display_message = $buddyforms[$_POST['form_slug']]['after_submit_message_text'];
            $display_message = str_ireplace('[form_singular_name]', $buddyforms[$args['form_slug']]['singular_name'], $display_message);
            $display_message = str_ireplace('[post_title]', get_the_title($args['post_id']), $display_message);
            $display_message = str_ireplace('[post_link]', '<a title="Display Post" href="' . get_permalink($args['post_id']) . '"">' . __('Display Post', 'buddyforms') . '</a>', $display_message);
            $display_message = str_ireplace('[edit_link]', '<a title="Edit Post" href="' . $permalink . 'edit/' . $args['form_slug'] . '/' . $args['post_id'] . '">' . __('Continue Editing', 'buddyforms') . '</a>', $display_message);

            $args['form_notice'] = $display_message;
        }

        if (isset($buddyforms[$_POST['form_slug']]['after_submit'])) {
            switch ($buddyforms[$_POST['form_slug']]['after_submit']) {
                case 'display_post':
                    $json['form_remove'] = 'true';
                    $json['form_notice'] = buddyforms_after_save_post_redirect(get_permalink( $args['post_id'] ));
                    break;
                case 'display_posts_list':
                    $json['form_remove'] = 'true';
                    $permalink = get_permalink($buddyforms[$args['form_slug']]['attached_page']);
                    $post_list_link = $permalink . 'view/' . $args['form_slug'] . '/';
                    $json['form_notice'] = buddyforms_after_save_post_redirect($post_list_link);
                    break;
                case 'display_message':
                    $json['form_remove'] = 'true';
                    $json['form_notice'] = $display_message;
                    break;
                default:
                    $json['post_id']      = $args['post_id'];
                    $json['editpost_title']  = $args['post_title'];
                    $json['revision_id']  = $args['revision_id'];
                    $json['post_parent']  = $args['post_parent'];
                    $json['form_notice']  = $args['form_notice'];
                    break;
            }
        }

    }


    $json = apply_filters('buddyforms_ajax_process_edit_post_json_response', $json);
    echo json_encode($json);

    die();
}

add_action('wp_ajax_buddyforms_ajax_delete_post', 'buddyforms_ajax_delete_post');
function buddyforms_ajax_delete_post(){
    global $current_user;
    get_currentuserinfo();

    $post_id    = $_POST['post_id'];
    $the_post	= get_post( $post_id );

    $form_slug = get_post_meta($post_id, '_bf_form_slug', true);
    if(!$form_slug){
        _e('You are not allowed to delete this entry! What are you doing here?', 'buddyforms');
        return;
    }

    // Check if the user is author of the post
    $user_can_delete = false;
    if ($the_post->post_author == $current_user->ID){
        $user_can_delete = true;
    }
    $user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete );
    if ( $user_can_delete == false ){
        _e('You are not allowed to delete this entry! What are you doing here?', 'buddyforms');
        return;
    }

    // check if the user has the roles roles and capabilities
    $user_can_delete = false;

    if( current_user_can('buddyforms_' . $form_slug . '_delete')){
        $user_can_delete = true;
    }
    $user_can_delete = apply_filters( 'buddyforms_user_can_delete', $user_can_delete );
    if ( $user_can_delete == false ){
        _e('You do not have the required user role to use this form', 'buddyforms');
        return;
    }

    do_action('buddyforms_delete_post',$post_id);

    wp_delete_post( $post_id );

    echo $post_id;
    die();
}

function buddyforms_after_save_post_redirect($url){
    $url = apply_filters('buddyforms_after_save_post_redirect', $url);
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    return $string;
}