<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field($slug, $args = array()){
	global $ninja_forms_fields;

	if( !isset( $ninja_forms_fields ) ){
		$ninja_forms_fields = array();
	}

	$defaults = array(
		'conditional' => '',
		'default_label' => '',		
		'default_label_pos' => '',
		'default_value' => '',
 		'display_function' => '',
 		'display_label' => true,
 		'display_wrap' => true,
 		'edit_autocomplete_off' => false,
 		'edit_conditional' => true,
 		'edit_custom_class' => true,
 		'edit_function' => '',
 		'edit_help' => true,
 		'edit_label' => true,
 		'edit_label_pos' => true,
 		'edit_meta' => true,
 		'edit_options' => '',
 		'edit_placeholder' => false,
 		'edit_req' => true,
 		'edit_settings' => '',
 		'edit_sub_post_process' => '',
 		'edit_sub_pre_process' => '',
 		'edit_sub_process' => '',
 		'esc_html' => true,
		'group' => '',
		'interact' => true,
		'label_pos_options' => '',
		'li_class' => '',
 		'limit' => '',
 		'name' => $slug,
		'nesting' => false,
		'post_process' => '',
 		'pre_process' => '',
 		'process' => '',
 		'process_field' => true,
 		'req' => false,
 		'req_validation' => '',
 		'save_function' => '',
 		'save_sub' => true,
 		'show_fav' => true,
 		'show_field_id' => true,
 		'show_remove' => true,
	 	'sub_edit' => 'text',
 		'sub_edit_function' => '',
 		'use_li' => true,
 		'visible' => 1,
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	foreach( $args as $key => $val ){
		$ninja_forms_fields[$slug][$key] = $val;
	}

}

function ninja_forms_register_field_type_group( $slug, $args ){
	global $ninja_forms_field_type_groups;

	foreach( $args as $key => $val ){
		$ninja_forms_field_type_groups[$slug][$key] = $val;
	}
}

function ninja_forms_register_tab( $slug, $args ){
	global $ninja_forms_tabs;

	if(!is_array($ninja_forms_tabs)){
		$ninja_forms_tabs = array();
	}

	$defaults = array(
		'active_class' => '',
		'add_form_id' => true,
		'disable_no_form_id' => false,
		'display_function' => '',
		'inactive_class' => '',
		'name' => '',
		'page' => '',
		'save_function' => '',
		'show_on_no_form_id' => true,
		'show_save' => true,
		'show_tab_links' => true,
		'show_this_tab_link' => true,
		'tab_reload' => false,
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	$page = $args['page'];

	foreach( $args as $key => $val ){
		$ninja_forms_tabs[$page][$slug][$key] = $val;
	}
}

function ninja_forms_register_sidebar( $slug, $args ){
	global $ninja_forms_sidebars;

	if( !is_array($ninja_forms_sidebars ) ){
		$ninja_forms_sidebars = array();
	}

	$defaults = array(
		'display_function' => 'ninja_forms_sidebar_display_fields',
		'name' => '',
		'order' => '',
		'save_function' => '',
		'settings' => ''
	);



	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	foreach( $args as $key => $val ){
		$ninja_forms_sidebars[$page][$tab][$slug][$key] = $val;
	}

}

function ninja_forms_register_sidebar_option( $slug, $args ){
	global $ninja_forms_sidebars;

	if( !is_array($ninja_forms_sidebars ) ){
		$ninja_forms_sidebars = array();
	}

	$defaults = array(
		'desc' => '',
		'display_function' => '',
		'help' => '',
		'name' => ''
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	foreach( $args as $key => $val ){
		$ninja_forms_sidebars[$page][$tab][$sidebar]['settings'][$slug][$key] = $val;
	}
}

function ninja_forms_register_sidebar_options( $args ){
	global $ninja_forms_sidebars;

	extract( $args );

	foreach( $args['settings'] as $setting ){

		$defaults = array(
			'desc' => '',
			'display_function' => '',
			'help' => '',
			'name' => ''
		);

		$slug = $setting['name'];

		// Parse incomming $setting into an array and merge it with $defaults
		$setting = wp_parse_args( $setting, $defaults );

		foreach( $setting as $key => $val ){
			$ninja_forms_sidebars[$page][$tab][$sidebar]['settings'][$slug][$key] = $val;
		}
	}

}

function ninja_forms_field_edit( $slug ){
	global $ninja_forms_fields;
	$function_name = $ninja_forms_fields[$slug]['edit_function'];
	$arguments = func_get_args();
    array_shift( $arguments ); // We need to remove the first arg ($function_name)
    call_user_func_array( $function_name, $arguments );
}

//Screen option registration function
function ninja_forms_register_screen_option( $id, $args ){
	global $ninja_forms_screen_options;

	$defaults = array(
		'display_function' => '',
		'order' => '',
		'page' => '',
		'save_function' => '',
		'tab' => '',
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	if($page == '' AND $tab == ''){
		$ninja_forms_screen_options['_universal_'][$id]['display_function'] = $display_function;
		$ninja_forms_screen_options['_universal_'][$id]['save_function'] = $save_function;
	}elseif($page != '' AND $tab == ''){
		$ninja_forms_screen_options[$page]['_universal_'][$id]['display_function'] = $display_function;
		$ninja_forms_screen_options[$page]['_universal_'][$id]['save_function'] = $save_function;
	}elseif($page != '' AND $tab != ''){
		$ninja_forms_screen_options[$page][$tab][$id]['display_function'] = $display_function;
		$ninja_forms_screen_options[$page][$tab][$id]['save_function'] = $save_function;
	}
}

//Help tab registration function
function ninja_forms_register_help_screen_tab( $id, $args ){
	global $ninja_forms_help_screen_tabs;

	$defaults = array(
		'display_function' => '',
		'order' => '',
		'page' => '',
		'tab' => '',
		'title' => '',
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	if($page == '' AND $tab == ''){
		$ninja_forms_help_screen_tabs['_universal_'][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs['_universal_'][$id]['content'] = $display_function;
	}elseif($page != '' AND $tab == ''){
		$ninja_forms_help_screen_tabs[$page]['_universal_'][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs[$page]['_universal_'][$id]['content'] = $display_function;
	}elseif($page != '' AND $tab != ''){
		$ninja_forms_help_screen_tabs[$page][$tab][$id]['title'] = $title;
		$ninja_forms_help_screen_tabs[$page][$tab][$id]['content'] = $display_function;
	}
}

//Tab - Metaboxes Registration function
function ninja_forms_register_tab_metabox($args = array()){
	global $ninja_forms_tabs_metaboxes;

	$defaults = array(
		'display_container' => true,
		'save_function' => '',
		'state' => ''
	);

	// Parse incomming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	foreach($args as $key => $val){
		$ninja_forms_tabs_metaboxes[$page][$tab][$slug][$key] = $val;
	}
}

//Register Tab Metabox Options
function ninja_forms_register_tab_metabox_options( $args = array() ){
	global $ninja_forms_tabs_metaboxes;

	extract( $args );

	$new_settings = $args['settings'];

	if( isset( $ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'] ) ){
		$settings = $ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'];
	}else{
		$settings = array();
	}

	if( is_array( $new_settings ) AND !empty( $new_settings ) ){
		foreach( $new_settings as $s ){
			if( is_array( $settings ) ){
				array_push( $settings, $s );
			}
		}
	}

	$ninja_forms_tabs_metaboxes[$page][$tab][$slug]['settings'] = $settings;
}