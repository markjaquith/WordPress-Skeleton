<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('admin_init', 'ninja_forms_register_sidebar_layout_fields');

function ninja_forms_register_sidebar_layout_fields(){
	$args = array(
		'name' => __( 'Layout Elements', 'ninja-forms' ),
		'page' => 'ninja-forms', 
		'tab' => 'builder',
		'display_function' => 'ninja_forms_sidebar_display_fields'
	);
	ninja_forms_register_sidebar('layout_fields', $args);
}