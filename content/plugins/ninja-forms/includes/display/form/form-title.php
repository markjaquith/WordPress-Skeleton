<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Outputs the HTML of the form title.
 * The form title can be filtered with 'ninja_forms_form_title'.
**/
add_action( 'init', 'ninja_forms_register_display_form_title' );
function ninja_forms_register_display_form_title(){
	add_action( 'ninja_forms_display_form_title', 'ninja_forms_display_form_title', 10 );
}

function ninja_forms_display_form_title( $form_id ){

	$show_title = Ninja_Forms()->form( $form_id )->get_setting( 'show_title' );
	$form_title = Ninja_Forms()->form( $form_id )->get_setting( 'form_title' );

	$title_class = 'ninja-forms-form-title';

	$title_class = apply_filters( 'ninja_forms_form_title_class', $title_class, $form_id );

	$form_title = '<h2 class="' . $title_class . '">'.$form_title.'</h2>';

	$form_title = apply_filters( 'ninja_forms_form_title', $form_title, $form_id );
	if($show_title == 1){
		echo $form_title;
	}
}