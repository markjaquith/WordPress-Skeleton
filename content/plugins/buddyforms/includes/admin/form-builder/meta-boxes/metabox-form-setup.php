<?php

function buddyforms_metabox_form_setup(){
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);

//    echo '<pre>';
//    print_r($buddyform);
//    echo '</pre>';

    // Get all post types
    $args = array(
        'show_ui' => true
    );
    $output = 'names'; // names or objects, note: names is the default
    $operator = 'and'; // 'and' or 'or'
    $post_types = get_post_types($args, $output, $operator);
    $post_types_none['none'] = 'none';

    $post_types = array_merge($post_types_none, $post_types);

    unset($post_types['buddyforms']);

    $name = get_the_title();
    $slug = $post->post_name;
    $singular_name = isset($buddyform['singular_name']) ? stripslashes($buddyform['singular_name']) : '';

    $form_setup = new Element_Hidden('buddyforms_options[name]', $name);
    $form_setup->render();
    $form_setup = new Element_Hidden('buddyforms_options[slug]', $slug);
    $form_setup->render();
    $form_setup = array();

    $form_setup[] = new Element_Textbox( '<b>' . __("Singular Name", 'buddyforms'), "buddyforms_options[singular_name]", array('value' => $singular_name, 'required' => 1));

    $post_type = 'false';
    if (isset($buddyform['post_type']))
        $post_type = $buddyform['post_type'];
    $form_setup[] = new Element_Select('<b>' . __("Post Type", 'buddyforms') . '</b>', "buddyforms_options[post_type]", $post_types, array('value' => $post_type, 'required' => 'required'));

    $args = array(
        'sort_order' => 'asc',
        'sort_column' => 'post_title',
        'parent' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    );
    $pages = get_pages($args);

    $options = Array();

    $options['none'] = 'none';
    foreach ( $pages as $page ) {
        $options[$page->ID] = $page->post_title;
    }

    $attached_page = 'false';
    if (isset($buddyform['attached_page']))
        $attached_page = $buddyform['attached_page'];
    $form_setup[] = new Element_Select( '<b>' . __("Page", 'buddyforms') . '</b>', "buddyforms_options[attached_page]", $options, array('value' => $attached_page, 'shortDesc' => '
    Associate a Page with a BuddyForm. The page you select will be used to build the forum URLs:<br>
    Create: page_name/create/form_name<br>
    View: page_name/view/form_name<br>
    Edit: page_name/edit/form_name<br>
<br><br>
    Pro Tips:<br>
    Different BuddyForms can be associated with the same Page.<br>
    You don’t have to use the auto-generated URLs -- you can add a BuddyForm or list of Posts to any Page or Post using shortcodes.<br>

  '));

    $status = 'false';
    if (isset($buddyform['status']))
        $status = $buddyform['status'];
    $form_setup[] = new Element_Select('<b>' . __("Status", 'buddyforms') . '</b>', "buddyforms_options[status]", array('publish', 'pending', 'draft'), array('value' => $status));

    $comment_status = 'false';
    if (isset($buddyform['comment_status']))
        $comment_status = $buddyform['comment_status'];
    $form_setup[] = new Element_Select('<b>' . __("Comment Status", 'buddyforms') . '</b>', "buddyforms_options[comment_status]", array('open', 'closed'), array('value' => $comment_status));

    $revision = 'false';
    if (isset($buddyform['revision']))
        $revision = $buddyform['revision'];
    $form_setup[] = new Element_Checkbox( '<b>' .__('Revision','buddyforms') . '</b>', "buddyforms_options[revision]", array('Revision' => __('Enable frontend revision control', 'buddyforms')), array('value' => $revision));

    $admin_bar = 'false';
    if (isset($buddyform['admin_bar']))
        $admin_bar = $buddyform['admin_bar'];
    $form_setup[] = new Element_Checkbox('<b>' .__('Admin Bar', 'buddyforms') . '</b>', "buddyforms_options[admin_bar]", array('Admin Bar' => __('Add to Admin Bar', 'buddyforms')), array('value' => $admin_bar));

    $edit_link = 'all';
    if (isset($buddyform['edit_link']))
        $edit_link = $buddyform['edit_link'];
    $form_setup[] = new Element_Radio('<b>' . __("Overwrite Frontend 'Edit Post' Link", 'buddyforms'). '</b>', "buddyforms_options[edit_link]", array('none' => 'None', 'all' => __("All Edit Links", 'buddyforms'), 'my-posts-list' => __("Only in My Posts List", 'buddyforms')), array('view' => 'vertical', 'value' => $edit_link, 'shortDesc' => __('The link to the backend will be changed to use the frontend editing.', 'buddyforms')));

    $after_submit = isset($buddyform['after_submit']) ? $buddyform['after_submit'] : 'display_form';
    $form_setup[] = new Element_Radio('<b>' . __("After Submission", 'buddyforms') . '</b>', "buddyforms_options[after_submit]", array('display_form' => 'Display the Form and Message', 'display_post' => 'Display the Post', 'display_posts_list' => 'Display the User\'s Post List', 'display_message' => 'Just display the Message'), array('value' => $after_submit, 'id' => 'after_submit_hidden' . $slug, 'class' => 'after_submit_hidden'));

    $after_submit_message_text = isset($buddyform['after_submit_message_text']) ? $buddyform['after_submit_message_text'] : 'The [form_singular_name] [post_title] has been successfully updated!<br>1. [post_link]<br>2. [edit_link]';
    $form_setup[] = new Element_Textarea('<b>' . __('After Submission Message', 'buddyforms') . '</b>', "buddyforms_options[after_submit_message_text]", array('rows' => 3, 'style' => "width:100%", 'value' => $after_submit_message_text, 'shortDesc' => __('<p>
        <small>You can use special shortcodes to add dynamic content:<br>
            [form_singular_name] = Singular Name<br>
            [post_title] = The Post Title<br>
            [post_link] = The Post Permalink<br>
            [edit_link] = Link to the Post Edit Form</small><br>
    </p>', 'buddyforms')));

    $bf_ajax = false;
    if (isset($buddyform['bf_ajax']))
        $bf_ajax = $buddyform['bf_ajax'];
    $form_setup[] = new Element_Checkbox( '<b>' . __('AJAX','buddyforms') . '</b>', "buddyforms_options[bf_ajax]", array('bf_ajax' => __('Disable ajax form submission', 'buddyforms')), array('shortDesc' => __('', 'buddyforms'), 'value' => $bf_ajax));


    $list_posts_option = 'list_all_form';
    if (isset($buddyform['list_posts_option']))
        $list_posts_option = $buddyform['list_posts_option'];
    $form_setup[] = new Element_Radio('<b>' . __("List Posts Options", 'buddyforms') . '</b>', "buddyforms_options[list_posts_option]", array('list_all_form' => 'List all Author Posts created with this Form', 'list_all' => 'List all Author Posts of the PostType'), array('value' => $list_posts_option, 'shortDesc' => ''));

    ?>
    <table class="form-table">
        <tbody>
    <?php

    foreach($form_setup as $key => $field){ ?>

        <tr id="row_form_title">
            <th scope="row">
                <label for="form_title"><?php echo $field->getLabel() ?></label>
            </th>
            <td>
                <?php echo $field->render() ?>
                <p class="description"><?php echo $field->getShortDesc() ?></p>
            </td>
        </tr>
    <?php
    }
    ?>
    </tbody>
    </table>
<?php

}
