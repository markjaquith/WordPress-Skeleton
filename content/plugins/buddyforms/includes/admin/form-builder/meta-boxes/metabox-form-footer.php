<?php
function buddyforms_metabox_form_footer(){

    global $post;

    if($post->post_type != 'buddyforms')
        return;


}
//add_action( 'edit_form_after_title', 'buddyforms_metabox_form_footer' );