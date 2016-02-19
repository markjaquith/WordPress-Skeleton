<?php

function buddyforms_metabox_sidebar()
{
    global $post;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);

    $sidebar_elements = array();

    $slug = $post->post_name;

    $sidebar_elements[] = new Element_HTML('

        <h5>' . __('Classic Fields', 'buddyforms') . '</h5>
        <p><a href="#" data-fieldtype="text" class="bf_add_element_action">' . __('Text', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="textarea" class="bf_add_element_action">' . __('Textarea', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="link" class="bf_add_element_action">' . __('Link', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="mail" class="bf_add_element_action">' . __('Mail', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="dropdown" class="bf_add_element_action">' . __('Dropdown', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="radiobutton" class="bf_add_element_action">' . __('Radiobutton', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="checkbox" class="bf_add_element_action">' . __('Checkbox', 'buddyforms') . '</a></p>
        <h5>Post Fields</h5>
        <p><a href="#" data-fieldtype="title" data-unique="unique" class="bf_add_element_action">' . __('Title', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="content" data-unique="unique" class="bf_add_element_action">' . __('Content', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="taxonomy" class="bf_add_element_action">' . __('Taxonomy', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="comments" data-unique="unique" class="bf_add_element_action">' . __('Comments', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="status" data-unique="unique" class="bf_add_element_action">' . __('Post Status', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="featured_image" data-unique="unique" class="bf_add_element_action">' . __('Featured Image', 'buddyforms') . '</a></p>

        <h5>Extras</h5>
        <p><a href="#" data-fieldtype="file" class="bf_add_element_action">' . __('File', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="hidden" class="bf_add_element_action">' . __('Hidden', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="number" class="bf_add_element_action">' . __('Number', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="html" class="bf_add_element_action">' . __('HTML', 'buddyforms') . '</a></p>
        <p><a href="#" data-fieldtype="date" class="bf_add_element_action">' . __('Date', 'buddyforms') . '</a></p>

    ');

    $sidebar_elements = apply_filters('buddyforms_add_form_element_to_sidebar', $sidebar_elements);

    foreach($sidebar_elements as $key => $field){
        echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
        echo '<p class="buddyforms_field_description">' . $field->getShortDesc() . '</p>';
        echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
    }
}