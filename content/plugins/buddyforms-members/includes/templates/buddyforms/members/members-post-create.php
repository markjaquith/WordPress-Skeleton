<div id="item-body">
<?php
global $bp, $buddyforms, $buddyforms_member_tabs;

$post_id = 0;
$post_parent_id = 0;
$revision_id = '';
$current_component = $bp->current_component;

$form_slug = $buddyforms_member_tabs[$bp->current_component][$bp->current_action];

if(bp_current_action() == $form_slug . '-create'){
    if(isset($bp->action_variables[0]))
        $post_parent_id = $bp->action_variables[0];
}
if(bp_current_action() == $form_slug . '-edit'){
    if(isset($bp->action_variables[0]))
        $post_id = $bp->action_variables[0];
}
if(bp_current_action() == $form_slug . '-revision'){
    if(isset($bp->action_variables[1]))
        $revision_id = $bp->action_variables[1];
}

$args = array(
	'form_slug'		=> $form_slug,
	'post_id'		=> $post_id,
    'post_parent'	=> $post_parent_id,
    'post_type'		=> $post_type,
	'revision_id'	=> $revision_id
);

buddyforms_create_edit_form($args);

?>
</div><!-- #item-body -->
