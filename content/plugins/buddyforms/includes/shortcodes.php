<?php
// Shortcode to add the form everywhere easily ;) the form is located in form.php
add_shortcode('buddyforms_form', 'buddyforms_create_edit_form_shortcode');
add_shortcode('bf', 'buddyforms_create_edit_form_shortcode');

function buddyforms_create_edit_form_shortcode($args){

    extract(shortcode_atts(array(
        'post_type' => '',
        'the_post' => 0,
        'post_id' => '',
        'revision_id' => false,
        'form_slug' => '',
    ), $args));

    ob_start();
    buddyforms_create_edit_form($args);
    $create_edit_form = ob_get_contents();
    ob_clean();

    return $create_edit_form;
}

function bf_get_url_var($name){
    $strURL = $_SERVER['REQUEST_URI'];
    $arrVals = explode("/",$strURL);
    $found = 0;
    foreach ($arrVals as $index => $value)
    {
        if($value == $name) $found = $index;
    }
    $place = $found + 1;
    return ($found == 0) ? 1 : $arrVals[$place];
}

/**
 * Shortcode to display author posts of a specific post type
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_shortcode('buddyforms_the_loop', 'buddyforms_the_loop');
function buddyforms_the_loop($args){
	global $the_lp_query, $buddyforms, $form_slug, $paged;

    extract(shortcode_atts(array(
        'post_type' => '',
        'form_slug' => '',
        'post_parent' => 0
    ), $args));

	if(!isset($buddyforms[$form_slug]['post_type']))
		return;

	if(empty($post_type))
		$post_type = $buddyforms[$form_slug]['post_type'];

    $list_posts_option = $buddyforms[$form_slug]['list_posts_option'];

    $user_id = get_current_user_id();
    $post_status = array('publish', 'pending', 'draft');

    if (!$user_id)
        $post_status = array('publish');

    $paged = bf_get_url_var('page');

    switch($list_posts_option){
        case 'list_all':
            $query_args = array(
                'post_type'         => $post_type,
                'post_parent'       => $post_parent,
                //'orderby'         => 'menu_order', // ??? ;)
                'form_slug'         => $form_slug,
                'post_status'       => $post_status,
                'posts_per_page'    => 10,
                'author'            => $user_id,
                'paged'             => $paged,
            );
            break;
        default:
            $query_args = array(
                'post_type'         => $post_type,
                'post_parent'       => $post_parent,
                'form_slug'         => $form_slug,
                'post_status'       => $post_status,
                'posts_per_page'    => 10,
                'author'            => $user_id,
                'paged'             => $paged,
                'meta_key'          => '_bf_form_slug',
                'meta_value'        => $form_slug
            );
            break;

    }

    $query_args =  apply_filters('bf_post_to_display_args',$query_args);

    do_action('buddyforms_the_loop_start', $query_args);

	$the_lp_query = new WP_Query( $query_args );

	$form_slug = $the_lp_query->query_vars['form_slug'];

	buddyforms_locate_template('buddyforms/the-loop.php');

	// Support for wp_pagenavi
	if(function_exists('wp_pagenavi')){
		wp_pagenavi( array( 'query' => $the_lp_query) );	
	}
    wp_reset_postdata();

    do_action('buddyforms_the_loop_end', $query_args);
}

/**
 * Shortcode to display author posts of a specific post type
 *
 * @package BuddyForms
 * @since 0.3 beta
 */
add_shortcode('buddyforms_list_all', 'buddyforms_list_all');



function buddyforms_list_all($args){
    global $the_lp_query, $buddyforms, $form_slug, $paged;

    extract(shortcode_atts(array(
        'form_slug' => ''
    ), $args));

    $post_type = $buddyforms[$form_slug]['post_type'];

    if(!$post_type)
        return;

    $paged = bf_get_url_var('page');

    $query_args = array(
        'post_type'         => $post_type,
        'form_slug'         => $form_slug,
        'post_status'       => array('publish'),
        'posts_per_page'    => 10,
        'paged'             => $paged
    );

    do_action('buddyforms_the_loop_start', $query_args);

    $query_args =  apply_filters('bf_post_to_display_args',$query_args);

    $the_lp_query = new WP_Query( $query_args );

    $form_slug = $the_lp_query->query_vars['form_slug'];
    ob_start();
        buddyforms_locate_template('buddyforms/the-loop.php');

        // Support for wp_pagenavi
        if(function_exists('wp_pagenavi')){
            wp_pagenavi( array( 'query' => $the_lp_query) );
        }
        $theloop = ob_get_clean();
    wp_reset_postdata();
    do_action('buddyforms_the_loop_end', $query_args);
    return $theloop;
}

//
// BuddyForms Schortcode Buttons
//

add_shortcode('buddyforms_nav', 'buddyforms_nav');

function buddyforms_nav($args){

    extract(shortcode_atts(array(
        'form_slug'     => '',
        'separator'     => ' | '
    ), $args));

    $tmp = buddyforms_button_view_posts($args);
    $tmp .= $separator;
    $tmp .= buddyforms_button_add_new($args);

    return $tmp;
}

add_shortcode('buddyforms_button_view_posts', 'buddyforms_button_view_posts');
function buddyforms_button_view_posts($args){
    global $buddyforms;

    extract(shortcode_atts(array(
        'form_slug' => '',
        'label'    => 'View',
    ), $args));

    $button = '<a class="button" href="/'.get_post( $buddyforms[$form_slug]['attached_page'] )->post_name.'/view/'.$form_slug.'/"> '.__($label, 'buddyforms').' </a>';

    return apply_filters('buddyforms_button_view_posts',$button,$args);

}

add_shortcode('buddyforms_button_add_new', 'buddyforms_button_add_new');
function buddyforms_button_add_new($args){
    global $buddyforms;

    extract(shortcode_atts(array(
        'form_slug' => '',
        'label'     => 'Add New',
    ), $args));


    $button = '<a class="button" href="/'.get_post( $buddyforms[$form_slug]['attached_page'] )->post_name.'/create/'.$form_slug.'/"> '.__($label, 'buddyforms').'</a>';

    return apply_filters('buddyforms_button_add_new',$button,$args);

}

//add_shortcode('buddyforms_ajax_nav', 'buddyforms_ajax_nav');
function buddyforms_ajax_nav($args){

    extract(shortcode_atts(array(
        'form_slug' => ''
    ), $args));


    $tmp = '<a class="button bf_view_form" href="'.$form_slug.'"> '.__('View', 'buddyforms').' </a>';
    $tmp .= '<a class="button bf_add_new_form" href="'.$form_slug.'"> '.__('Add New', 'buddyforms').' </a>';

    $tmp .= '<div class="bf_blub"></div>';


    return $tmp;

}

//add_action('wp_ajax_buddyforms_list_all_ajax', 'buddyforms_list_all_ajax');
//add_action('wp_ajax_nopriv_buddyforms_list_all_ajax', 'buddyforms_list_all_ajax');
function buddyforms_list_all_ajax(){

    if(isset($_POST['form_slug'])) {
        $form_slug = $_POST['form_slug'];

        $args = array(
            'form_slug' => $form_slug
        );
        echo buddyforms_list_all($args);

    }
    die();
}

/**
 * Add a button to the content editor, next to the media button
 * This button will show a popup that contains inline content
 * @package BuddyForms
 * @since 0.3 beta
 *
 */
add_action('media_buttons_context', 'buddyforms_editor_button');
function buddyforms_editor_button($context) {

    if (!is_admin())
        return $context;

    // Path to my icon
    // $img = plugins_url( 'admin/img/icon-buddyformsc-16.png' , __FILE__ );

    // The ID of the container I want to show in the popup
    $container_id = 'buddyforms_popup_container';

    // Our popup's title
    $title = 'BuddyForms Shortcode Generator!';

    // Append the icon <a href="#" class="button insert-media add_media" data-editor="content" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>
    $context .= "<a class='button thickbox' data-editor='content'  title='{$title}'
    href='#TB_inline?width=400&inlineId={$container_id}'>

    <span class='tk-icon-buddyforms'></span> BuddyForms</a>";

    return $context;
}


/**
 * Add some content to the bottom of the page for the BuddyForms shortcodes
 * This will be shown in the thickbox of the post edit screen
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
add_action('admin_footer', 'buddyforms_editor_button_inline_content');
function buddyforms_editor_button_inline_content() {
    global $buddyforms;
    if (!is_admin())
        return; ?>

    <div id="buddyforms_popup_container" style="display:none;">
        <h2></h2>
        <?php

        // Get all post types
        $args=array(
            'public' => true,
            'show_ui' => true
        );
        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'
        $post_types = get_post_types($args,$output,$operator);
        $post_types_none['none'] = 'none';
        $post_types = array_merge($post_types_none,$post_types);

        //
        // Insert Form
        //

        $form = new Form("buddyforms_add_form");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => $_SERVER['REQUEST_URI'],
            "view" => new View_Inline
        ));
        $the_forms['none'] = 'Select Form';

        foreach ($buddyforms as $key => $buddyform) {
            $the_forms[$buddyform['slug']] = $buddyform['slug'];
        }


        $form->addElement( new Element_Select("<h3>" . __('Insert Form', 'buddyforms') . "</h3><br>", "buddyforms_add_form", $the_forms, array('class' => 'buddyforms_add_form')));
        $form->addElement( new Element_HTML('  <a href="#" class="buddyforms-button-insert-form button">'. __('Insert into Post', 'buddyforms') .'</a>'));
        $form->render();

        //
        // Insert Navigation
        //

        $form = new Form("buddyforms_add_nav");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => $_SERVER['REQUEST_URI'],
            "view" => new View_Inline
        ));

        $button_type['none'] = 'Insert Navigation';
        $button_type['buddyforms_nav'] = 'View - Add New';
        $button_type['buddyforms_button_view_posts'] = 'View Posts';
        $button_type['buddyforms_button_add_new'] = 'Add New';


        $form->addElement( new Element_Select("<h3>" . __('Button Type', 'buddyforms') . "</h3><br>", "buddyforms_insert_nav", $button_type, array('class' => 'buddyforms_insert_nav')));
        $form->addElement( new Element_Select("", "buddyforms_select_form", $the_forms, array('class' => 'buddyforms_select_form')));
        $form->addElement( new Element_HTML('  <a href="#" class="buddyforms-button-insert-nav button">'. __('Insert into Post', 'buddyforms') .'</a>'));
        $form->render();

        //
        // Insert Posts
        //

        $form = new Form("buddyforms_view_posts");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => $_SERVER['REQUEST_URI'],
            "view" => new View_Inline
        ));

        $view_type['none'] = 'Filter Posts';
        $view_type['buddyforms_list_all'] = 'All User';
        $view_type['buddyforms_the_loop'] = 'Displayed User';

        $form->addElement( new Element_Select("<h3>" . __('List Posts', 'buddyforms') . "</h3><br>", "buddyforms_view_posts", $view_type, array('class' => 'buddyforms_view_posts')));
        $form->addElement( new Element_Select("", "buddyforms_select_form_posts", $the_forms, array('class' => 'buddyforms_select_form_posts')));
        $form->addElement( new Element_HTML('  <a href="#" class="buddyforms-button-insert-posts button">'. __('Insert into Post', 'buddyforms') .'</a>'));
        $form->render();

        ?>
    </div>
<?php
}

add_action('admin_footer',  'buddyforms_editor_button_mce_popup');
function buddyforms_editor_button_mce_popup(){ ?>
    <script>

        jQuery(document).ready(function (){
            jQuery('.buddyforms-button-insert-form').on('click',function(event){
                var form_slug = jQuery('.buddyforms_add_form').val();
                if(form_slug == "none")
                    return

                window.send_to_editor('[buddyforms_form form_slug="'+form_slug +'"]');
            });

            jQuery('.buddyforms-button-insert-nav').on('click',function(event){

                var shortcode = jQuery('.buddyforms_insert_nav').val();
                var form_slug = jQuery('.buddyforms_select_form').val();

                if(shortcode == "none"){
                    alert('Please select a Button Type')
                    return
                }
                if(form_slug == "none"){
                    alert('Please select a Form')
                    return
                }

                window.send_to_editor('['+shortcode+' form_slug="'+form_slug +'"]');
            });

            jQuery('.buddyforms-button-insert-posts').on('click',function(event){
                var shortcode = jQuery('.buddyforms_view_posts').val();
                var form_slug = jQuery('.buddyforms_select_form_posts').val();

                if(shortcode == "none"){
                    alert('Please select a List Type')
                    return
                }
                if(form_slug == "none"){
                    alert('Please select a Form')
                    return
                }


                window.send_to_editor('['+shortcode+' form_slug="'+form_slug +'"]');
            });
        });

    </script>
<?php
}