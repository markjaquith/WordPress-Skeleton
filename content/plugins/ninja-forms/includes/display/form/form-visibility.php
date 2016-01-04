<?php if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'ninja_forms_register_display_form_visibility', 99 );
function ninja_forms_register_display_form_visibility(){
	add_filter( 'ninja_forms_display_form_visibility', 'ninja_forms_display_form_visibility', 10, 2 );
}

function ninja_forms_display_form_visibility( $display, $form_id ){
	global $ninja_forms_processing;

	if( is_object( $ninja_forms_processing ) ){
		$hide_complete = $ninja_forms_processing->get_form_setting( 'hide_complete' );
	}else{
		$hide_complete = Ninja_Forms()->form( $form_id )->get_setting( 'hide_complete' );
	}

	//If the plugin setting 'hide complete' has been set and a success message exists, hide the form.
	if( $hide_complete == 1 AND ( is_object( $ninja_forms_processing ) AND $ninja_forms_processing->get_form_setting( 'processing_complete' ) == 1 ) AND $ninja_forms_processing->get_form_ID() == $form_id ){
		$display = 0;
	}

	return $display;
}