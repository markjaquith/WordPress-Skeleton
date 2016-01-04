<?php if ( ! defined( 'ABSPATH' ) ) exit;
// add_action( 'init', 'ninja_forms_register_form_settings_help' );
function ninja_forms_register_form_settings_help(){
	$args = array(
		'page' => 'ninja-forms',
		'tab' => 'form_settings',
		'title' => __( 'Basic Settings', 'ninja-forms' ),
		'display_function' => 'ninja_forms_help_form_settings',
	);
	ninja_forms_register_help_screen_tab('basic_settings', $args);
}

function ninja_forms_help_form_settings(){
	echo '<p>';
	_e( 'Ninja Forms basic help goes here.', 'ninja-forms' );
	echo '</p>';
}