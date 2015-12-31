<?php if ( ! defined( 'ABSPATH' ) ) exit;
//add_action('init', 'ninja_forms_register_field_organizer');

function ninja_forms_register_field_organizer(){
	$args = array(
		'name' => __( 'Organizer', 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => '',
		'display_function' => '',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => false,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => false,
		'nesting' => true,
		'display_label' => false,
		'process_field' => false,
	);

	ninja_forms_register_field('_organizer', $args);
}