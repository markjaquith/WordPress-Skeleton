<?php

/**
 * Add the forms to the admin bar
 *
 * @package BuddyForms
 * @since 0.3 beta
 *
 * @uses add_action()
 */
add_action('wp_before_admin_bar_render', 'buddyforms_members_wp_before_admin_bar_render',99,1);
function buddyforms_members_wp_before_admin_bar_render(){
	global $wp_admin_bar, $buddyforms;

	if (empty($buddyforms))
		return;

	foreach ($buddyforms as $key => $buddyform) {

        if (!isset($buddyform['post_type']) || $buddyform['post_type'] == 'none'){
            continue;
        }

		if(isset($buddyform['profiles_integration'])) :

			$parent_tab = buddyforms_members_parent_tab($buddyform);

			$slug = $key;
			if(isset($buddyform['slug']))
				$slug = $parent_tab .'/';

			$post_type_object = get_post_type_object( $key );

			if(isset($post_type_object->labels->name))
				$name = $post_type_object->labels->name;

			if(isset($buddyform['name']))
				$name = $buddyform['name'];

			if(isset($buddyform['admin_bar'][0])){
                if (current_user_can('buddyforms_' . $key . '_create')) {
                    $wp_admin_bar->add_menu(array(
                        'parent' => 'my-account-buddypress',
                        'id' => 'my-account-buddypress-' . $key,
                        'title' => __($name, 'buddypress'),
                        'href' => trailingslashit(bp_loggedin_user_domain() . $slug)
                    ));
                    $wp_admin_bar->add_menu(array(
                        'parent' => 'my-account-buddypress-' . $key,
                        'id' => 'my-account-buddypress-' . $key . '-view',
                        'title' => __('View my ', 'buddyforms') . $buddyform['name'],
                        'href' => trailingslashit(bp_loggedin_user_domain() . $slug . $key . '-my-posts')
                    ));
                    $wp_admin_bar->add_menu(array(
                        'parent' => 'my-account-buddypress-' . $key,
                        'id' => 'my-account-buddypress-' . $key . '-new',
                        'title' => __('New ', 'buddyforms') . $buddyform['singular_name'],
                        'href' => trailingslashit(bp_loggedin_user_domain() . $slug . $key . '-create')
                    ));
                }
			}
		endif;
	}
}

add_action('wp_before_admin_bar_render', 'buddyforms_admin_bar_members' ,10,1);
/**
 * Remove forms from the admin used by BuddyForms. They will be added to the BuddyPress menu
 *
 * @package BuddyForms
 * @since 0.3 beta
 *
 * @uses add_action()
 */
function buddyforms_admin_bar_members() {
    global $wp_admin_bar, $buddyforms;

    if(!isset($buddyforms))
        return;

	if(!is_array($buddyforms))
        return;

    foreach ($buddyforms as $key => $buddyform) {

        if(isset($buddyform['profiles_integration']))
            $wp_admin_bar->remove_menu('my-account-'.$key);

    }

}

/**
 * Get the BuddyForms template directory
 *
 * @package BuddyForms
 * @since 0.1 beta
 *
 * @uses apply_filters()
 * @return string
 */
function buddyforms_members_get_template_directory() {
	return apply_filters('buddyforms_members_get_template_directory', constant('BUDDYFORMS_MEMBERS_TEMPLATE_PATH'));
}

/**
 * Locate a template
 *
 * @package BuddyForms
 * @since 0.1 beta
 */
function buddyforms_members_locate_template($file) {
	if (locate_template(array($file), false)) {
		locate_template(array($file), true);
	} else {
		include (BUDDYFORMS_MEMBERS_TEMPLATE_PATH . $file);
	}
}


add_filter('buddyforms_front_js_css_loader', 'buddyforms_front_js_loader_bp_members_support', 10, 1);
function buddyforms_front_js_loader_bp_members_support($found){
	global $bp, $buddyforms, $buddyforms_member_tabs;

	$form_slug = $buddyforms_member_tabs[$bp->current_component][$bp->current_action];

	// check the post content for the short code
	if(isset($buddyforms[$form_slug]))
		$found = true;

	return $found;
}

add_filter('buddyforms_button_view_posts', 'buddyforms_members_button_view_posts', 10, 2);
function buddyforms_members_button_view_posts($button,$args){
	global $buddyforms;

	extract(shortcode_atts(array(
		'form_slug' => '',
		'label'     => 'View',
	), $args));

	if(isset($buddyforms[$form_slug]['profiles_integration'])){
        $url = trailingslashit(bp_loggedin_user_domain());
		$button =   '<a class="button" href="'.$url.$form_slug.'/">'.__($label, 'buddyforms').' </a>';
      }

	return $button;
}
add_filter('buddyforms_button_add_new', 'buddyforms_members_button_add_new', 10, 2);
function buddyforms_members_button_add_new($button,$args){
	global $buddyforms;

	extract(shortcode_atts(array(
		'form_slug' => '',
		'label'     => 'Add New',
	), $args));

	if(isset($buddyforms[$form_slug]['profiles_integration'])){
        $url = trailingslashit(bp_loggedin_user_domain());
		$button =  '<a class="button" href="'.$url.$form_slug.'/create/">'.__($label, 'buddyforms').'</a>';
    }

	return $button;
}
?>
