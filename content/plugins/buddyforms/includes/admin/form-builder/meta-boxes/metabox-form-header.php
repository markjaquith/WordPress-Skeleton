<?php
function buddyforms_metabox_form_header(){

    global $post;

    if($post->post_type != 'buddyforms')
        return;

    //include(BUDDYFORMS_INCLUDES_PATH . '/admin/admin-credits.php');
}

add_action( 'edit_form_top', 'buddyforms_metabox_form_header' );