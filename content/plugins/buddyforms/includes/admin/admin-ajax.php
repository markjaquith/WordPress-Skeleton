<?php
/**
 * Ajax call back function to add a form element
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_add_form(){
    global $buddyforms;

    if(!is_array($buddyforms))
        $buddyforms = Array();

    if(empty($_POST['create_new_form_name']))
        return;
    if(empty($_POST['create_new_form_singular_name']))
        return;
    if(empty($_POST['create_new_form_attached_page']) && empty($_POST['create_new_page']))
        return;
    if(empty($_POST['create_new_form_post_type']))
        return;

    if(!empty($_POST['create_new_page'])){
        // Create post object
        $mew_post = array(
            'post_title'    => wp_strip_all_tags( $_POST['create_new_page'] ),
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'page'
        );

        // Insert the post into the database
        $_POST['create_new_form_attached_page'] = wp_insert_post( $mew_post );
    }

    $bf_forms_args = array(
        'post_title' 		=> $_POST['create_new_form_name'],
        'post_type' 		=> 'buddyforms',
        'post_status' 		=> 'publish',
    );

    // Insert the new form
    $post_id = wp_insert_post( $bf_forms_args, true );
    $the_post =  get_post($post_id);

    $options = Array(
        'slug'              => $the_post->post_name,
        'id'                => $the_post->ID,
        'name'              => $_POST['create_new_form_name'],
        'singular_name'     => $_POST['create_new_form_singular_name'],
        'attached_page'     => $_POST['create_new_form_attached_page'],
        'post_type'         => $_POST['create_new_form_post_type'],
    );

    if(!empty($_POST['create_new_form_status']))
        $options = array_merge($options, Array('status' => $_POST['create_new_form_status']));

    if(!empty($_POST['create_new_form_comment_status']))
        $options = array_merge($options, Array('comment_status' => $_POST['create_new_form_comment_status']));

    $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

    $options['form_fields'][$field_id]['name']          = 'Title';
    $options['form_fields'][$field_id]['slug']          = 'editpost_title';
    $options['form_fields'][$field_id]['type']          = 'Title';

    $field_id = $mod5 = substr(md5(time() * rand()), 0, 10);

    $options['form_fields'][$field_id]['name']          = 'Content';
    $options['form_fields'][$field_id]['slug']          = 'editpost_content';
    $options['form_fields'][$field_id]['type']          = 'Content';


    update_post_meta($post_id, '_buddyforms_options', $options);

    if($post_id){
        buddyforms_attached_page_rewrite_rules(TRUE);
        echo sanitize_title($_POST['create_new_form_name']);
    } else {
        echo 'Error Saving the Form';
    }

    die();

}
add_action( 'wp_ajax_buddyforms_add_form', 'buddyforms_add_form' );

/**
 * Get all taxonomies
 *
 * @package BuddyForms
 * @since 0.1-beta
 */
function buddyforms_taxonomies($buddyform){

    $post_type = $buddyform['post_type'];

    $taxonomies=get_object_taxonomies($post_type);

    return $taxonomies;
}

function buddyforms_update_taxonomy_default(){

    if(!isset($_POST['taxonomy']))
        echo 'false';

    $taxonomy = $_POST['taxonomy'];

    $args = array(
        'orderby'           => 'name',
        'order'             => 'ASC',
        'hide_empty'        => false,
        'fields'            => 'id=>name',
    );

    $terms = get_terms($taxonomy, $args);

    $tmp = '<option value="none">none</option>';
    foreach($terms as $key => $term_name){
        $tmp .= '<option value="'.$key.'">'.$term_name.'</option>';
    }

    echo $tmp;

    die();

}
add_action( 'wp_ajax_buddyforms_update_taxonomy_default', 'buddyforms_update_taxonomy_default' );