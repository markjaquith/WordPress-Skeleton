<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_add_meta_boxes() {
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    add_meta_box('buddyforms_form_setup', __("Form Setup",'buddyforms'), 'buddyforms_metabox_form_setup'    , 'buddyforms', 'normal', 'high');
    add_meta_box('buddyforms_form_elements', __("Form Builder",'buddyforms'), 'buddyforms_metabox_form_elements' , 'buddyforms', 'normal', 'high');
    add_meta_box('buddyforms_form_mail', __("Mail Notification",'buddyforms'), 'bf_mail_notification_screen'      , 'buddyforms', 'normal', 'default');
    add_meta_box('buddyforms_form_roles', __("Permissions",'buddyforms'), 'bf_manage_form_roles_and_capabilities_screen', 'buddyforms', 'normal', 'default');
    add_meta_box('buddyforms_form_sidebar', __("Form Elements",'buddyforms'), 'buddyforms_metabox_sidebar'       , 'buddyforms', 'side', 'default');

}
add_action( 'add_meta_boxes', 'buddyforms_add_meta_boxes' );

add_filter("get_user_option_meta-box-order_buddyforms", function() {
    remove_all_actions('edit_form_advanced');
    remove_all_actions('edit_page_form');
}, PHP_INT_MAX);

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_edit_form_save_meta_box_data($post_id){
    global $post;

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
        return;

    if(!isset($post->post_type) || $post->post_type != 'buddyforms')
        return;

    if(!isset($_POST['buddyforms_options']))
        return;

    $buddyform = $_POST['buddyforms_options'];

    if(isset($buddyform['form_fields'])) : foreach( $buddyform['form_fields'] as $key => $field ){
        $buddyform['form_fields'][$key]['slug'] = sanitize_title($field['slug']);
        $buddyform['form_fields'][$key]['type'] = sanitize_title($field['type']);
    } endif;

    // First update post meta
    update_post_meta( $post_id, '_buddyforms_options', $buddyform );

    // Save the Roles and capabilities.
    if(isset($_POST['buddyforms_roles'])){

        foreach (get_editable_roles() as $role_name => $role_info):
            $role = get_role( $role_name );
            foreach ($role_info['capabilities'] as $capability => $_):

                $capability_array = explode('_', $capability);

                if($capability_array[0] == 'buddyforms'){
                    if($capability_array[1] == $buddyform['slug']){

                        $role->remove_cap( $capability );

                    }
                }

            endforeach;
        endforeach;

        foreach($_POST['buddyforms_roles'] as $form_role => $capabilities){
            foreach($capabilities as $key => $capability){
                $role = get_role( $key );
                foreach ($capability as $key => $cap) {
                    $role->add_cap( $cap );
                }
            }

        }

    }

    buddyforms_regenerate_global_options();

    buddyforms_attached_page_rewrite_rules(TRUE);

}
add_action( 'save_post', 'buddyforms_edit_form_save_meta_box_data' );

add_action('transition_post_status','buddyforms_transition_post_status_regenerate_global_options',10,3);
function buddyforms_transition_post_status_regenerate_global_options($new_status,$old_status,$post){

    if($post->post_type != 'buddyforms')
        return;

    buddyforms_regenerate_global_options();
    buddyforms_attached_page_rewrite_rules(TRUE);

}

function buddyforms_regenerate_global_options(){
    // get all forms and update the global
    $posts = get_posts(array(
        'numberposts' 	=> -1,
        'post_type' 	=> 'buddyforms',
        'orderby' 		=> 'menu_order title',
        'order' 		=> 'asc',
        'suppress_filters' => false,
        'post_status' => 'publish'
    ));

    $buddyforms_forms = Array();

    if( $posts ){ foreach( $posts as $post ){
        $options = get_post_meta($post->ID,'_buddyforms_options', true);
        if($options){
            $buddyforms_forms[$post->post_name] = $options;
        }
    }}
    update_option('buddyforms_forms', $buddyforms_forms);
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_register_post_type(){

    // Create BuddyForms post type
    $labels = array(
        'name'                  => __('BuddyForms', 'buddyforms'),
        'singular_name'         => __('BuddyForm', 'buddyforms'),
        'add_new'               => __('Add New', 'buddyforms'),
        'add_new_item'          => __('Add New Form', 'buddyforms'),
        'edit_item'             => __('Edit Form', 'buddyforms'),
        'new_item'              => __('New Form', 'buddyforms'),
        'view_item'             => __('View Form', 'buddyforms'),
        'search_items'          => __('Search BuddyForms', 'buddyforms'),
        'not_found'             => __('No BuddyForm found', 'buddyforms'),
        'not_found_in_trash'    => __('No Forms found in Trash', 'buddyforms'),
    );

    register_post_type('buddyforms', array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        '_builtin' => false,
        'capability_type' => 'page',
        'hierarchical' => true,
        'rewrite' => false,
        'supports' => array(
            'title'
        ),
        'show_in_menu' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'menu_icon' => 'dashicons-buddyforms',
    ));

}
add_action( 'init', 'buddyforms_register_post_type' );

function menue_icon_admin_head_css(){ ?>
    <style>

        .wp-menu-image.dashicons-before.dashicons-buddyforms:before {
        content: "\e000";
        font-family: 'icomoon';
        font-size: 27px;
        padding: 0;
        padding-right: 10px;
        }

    </style>

<?php }
add_action( 'admin_head', 'menue_icon_admin_head_css' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_form_updated_messages( $messages ) {
    global $post, $post_ID;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);
    $viwe_form_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : '';

    $messages = array(
        0 => '', // Unused. Messages start at index 1.
        1 => sprintf( __('Form updated. <a href="%s">View Form</a>'), $viwe_form_permalink . 'create/' . $post->post_name  ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Form updated.'),
        /* translators: %s: date and time of the revision */
        5 => isset($_GET['revision']) ? sprintf( __('Form restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Form published. <a href="%s">View Form</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Form saved.'),
        8 => sprintf( __('Form submitted. <a target="_blank" href="%s">Preview Form</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Form scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Form</a>'),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Form draft updated. <a target="_blank" href="%s">Preview Form</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );

    return $messages;
}
add_filter( 'post_updated_messages', 'buddyforms_form_updated_messages' );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function set_custom_edit_buddyforms_columns($columns) {
    unset($columns['date']);
    $columns['slug'] = __( 'Slug', 'buddyforms' );
    $columns['attached_post_type'] = __( 'Attached Post Type', 'buddyforms' );
    $columns['attached_page'] = __( 'Attached Page', 'buddyforms' );
    $columns['shortcode'] = __( 'Shortcode', 'buddyforms' );
    return $columns;
}
add_filter( 'manage_buddyforms_posts_columns', 'set_custom_edit_buddyforms_columns',10,1 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function custom_buddyforms_column( $column, $post_id ) {

    $post =  get_post($post_id);
    $buddyform = get_post_meta($post_id, '_buddyforms_options', true);

    switch ( $column ) {
        case 'slug' :
            echo $post->post_name;
            break;
        case 'attached_post_type' :

            $post_type_html = isset($buddyform['post_type']) ? $buddyform['post_type'] : 'none';

            if(!post_type_exists($post_type_html))
                $post_type_html = '<p style="color: red;">' . __('Post Type not exists', 'buddyforms') . '</p>';

            if(!isset($buddyform['post_type']) || $buddyform['post_type'] == 'none')
                $post_type_html = '<p style="color: red;">' . __('No Post Type not Selected', 'buddyforms') . '</p>';

            echo $post_type_html;
            break;
        case 'attached_page' :
            if( isset($buddyform['attached_page']) && empty($buddyform['attached_page']) ){
                $attached_page = '<p style="color: red;">No Page Attached</p>';
            } elseif(isset($buddyform['attached_page']) && $attached_page_title = get_the_title($buddyform['attached_page'])) {
                $attached_page = $attached_page_title;
            } else {
                $attached_page = '<p style="color: red;">Page not Exists</p>';
            }

            echo $attached_page;

        break;
        case 'shortcode':
            echo '[bf form_slug="'.$post->post_name.'"]';
            break;
    }
}
add_action( 'manage_buddyforms_posts_custom_column' , 'custom_buddyforms_column', 10, 2 );

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function buddyforms_hide_publishing_actions(){

    global $post;
    if($post->post_type == 'buddyforms'){
        echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        ?>
        <style>
            #message{display:none;}
            .error {
                display: none;
            }
            h1{
                display:none;
            }
            .metabox-prefs label {
                /* float: right; */
                /* margin-top: 57px; */
                width: 100%;
            }
        </style>

        <script>
            jQuery(document).ready(function(jQuery) {
                //jQuery('#screen-meta-links').hide();
                jQuery('body').find('h1:first').css('line-height', '58px');
                jQuery('body').find('h1:first').css('font-size', '30px');
                //jQuery('body').find('h1:first').addClass('tk-icon-buddyforms');
                jQuery('body').find('h1:first').html('<div style="font-size: 52px; margin-top: -5px; float: left; margin-right: 15px;" class="tk-icon-buddyforms"></div> ' +
                    'BuddyForms <small style="font-size: 20px; padding-top: 23px;" >Version <?php echo BUDDYFORMS_VERSION ?></small>'
                );
                jQuery('h1').show();
            });

        </script>


        <?php
    }
}
add_action('admin_head-edit.php', 'buddyforms_hide_publishing_actions');
add_action('admin_head-post.php', 'buddyforms_hide_publishing_actions');
add_action('admin_head-post-new.php', 'buddyforms_hide_publishing_actions');


function buddyforms_add_button_to_submit_box() {
    global $post;

    if (get_post_type($post) == 'buddyforms') {

        $buddyform = get_post_meta($post->ID, '_buddyforms_options', true);
        $attached_page_permalink = isset($buddyform['attached_page']) ? get_permalink($buddyform['attached_page']) : '';

        echo '<a class="button button-large bf_button_action" href="'.$attached_page_permalink . 'view/' . $post->post_name . '/" target="_new">'.__('View Form Posts', 'buddyforms').'</a>
        <a class="button button-large bf_button_action" href="'.$attached_page_permalink . 'create/' . $post->post_name . '/" target="_new">'.__('View Form', 'buddyforms').'</a>';

    }
}
add_action( 'post_submitbox_start', 'buddyforms_add_button_to_submit_box' );

function buddyforms_remove_slugdiv() {
    remove_meta_box( 'slugdiv' , 'buddyforms' , 'normal' );
}
add_action( 'admin_menu' , 'buddyforms_remove_slugdiv' );
