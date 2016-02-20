<?php
function buddyforms_members_admin_settings_sidebar_metabox(){
    add_meta_box('buddyforms_members', __("BP Member Profiles",'buddyforms'), 'buddyforms_members_admin_settings_sidebar_metabox_html', 'buddyforms', 'side', 'low');
}

function buddyforms_members_admin_settings_sidebar_metabox_html(){
    global $post, $buddyforms;

    if($post->post_type != 'buddyforms')
        return;

    $buddyform = get_post_meta(get_the_ID(), '_buddyforms_options', true);


    $form_setup = array();

    $attache = '';
    if(isset($buddyform['profiles_integration']))
        $attache = $buddyform['profiles_integration'];

    $profiles_parent_tab = false;
    if(isset($buddyform['profiles_parent_tab']))
        $profiles_parent_tab = $buddyform['profiles_parent_tab'];

    $form_setup[] = new Element_Checkbox("<b>" . __('Add this form as Profile Tab', 'buddyforms') . "</b>", "buddyforms_options[profiles_integration]", array("integrate" => "Integrate this Form"), array('value' => $attache, 'shortDesc' => __('Many forms can share the same attached page. All Forms with the same attached page can be grouped together with this option. All Forms will be listed as sub nav tabs of the page main nav', 'buddyforms')));
    $form_setup[] = new Element_Checkbox("<br><b>" . __('Use Attached Page as Parent Tab and make this form a sub tab of the parent', 'buddyforms') . "</b>", "buddyforms_options[profiles_parent_tab]", array("attached_page" => "Use Attached Page as Parent"), array('value' => $profiles_parent_tab, 'shortDesc' => __('Many Forms can have the same attached Page. All Forms with the same page with page as parent enabled will be listed as sub forms. This why you can group forms.', 'buddyforms')));
    //$form_setup[] = new Element_Checkbox("<br><b>" . __('Hide Post List', 'buddyforms') . "</b>", "buddyforms_options[profiles_parent_tab]", array("hide" => "Hide"), array('value' => $profiles_parent_tab, 'shortDesc' => __('Can be useful if you want to display all posts in one tab and only separate the forms', 'buddyforms')));

    foreach($form_setup as $key => $field){
        echo '<div class="buddyforms_field_label">' . $field->getLabel() . '</div>';
        echo '<div class="buddyforms_field_description">' . $field->getShortDesc() . '</div>';
        echo '<div class="buddyforms_form_field">' . $field->render() . '</div>';
    }
}
add_filter('add_meta_boxes','buddyforms_members_admin_settings_sidebar_metabox');
