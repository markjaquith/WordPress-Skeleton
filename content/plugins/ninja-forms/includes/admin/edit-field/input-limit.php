<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Function that adds a character and word limit option to textboxes and textareas.
 *
 * @since 2.4.3
 * @return void
 */

function ninja_forms_edit_field_input_limit( $field_id, $field_data ) {
	
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];

	$allowed_types = apply_filters( 'nf_input_limit_types', array( '_text', '_textarea' ) );

	if ( ! in_array( $field_type, $allowed_types ) )
		return false;

	if ( isset ( $field_data['input_limit'] ) ) {
		$input_limit = $field_data['input_limit'];
	} else {
		$input_limit = '';
	}

	if ( isset ( $field_data['input_limit_type'] ) ) {
		$input_limit_type = $field_data['input_limit_type'];
	} else {
		$input_limit_type = '';
	}

	if ( isset ( $field_data['input_limit_msg'] ) ) {
		$input_limit_msg = $field_data['input_limit_msg'];
	} else {
		$input_limit_msg = '';
	}

	$desc = '<em>'.__( 'If you leave the box empty, no limit will be used', 'ninja-forms' ).'</em>';
	ninja_forms_edit_field_el_output( $field_id, 'text', __( 'Limit input to this number', 'ninja-forms' ), 'input_limit', $input_limit, 'thin', '', 'widefat', $desc );
	ninja_forms_edit_field_el_output( $field_id, 'select', __( 'of', 'ninja-forms' ), 'input_limit_type', $input_limit_type, 'thin', array( array( 'name' => __( 'Characters', 'ninja-forms' ), 'value' => 'char') , array( 'name' => __( 'Words', 'ninja-forms' ), 'value' => 'word' ) ), 'widefat' );
	echo '<p class="description description-wide">&nbsp;</p>';
	ninja_forms_edit_field_el_output( $field_id, 'text', __( 'Text to appear after character/word counter', 'ninja-forms' ), 'input_limit_msg', $input_limit_msg, 'wide', '', 'widefat' );
}

add_action( 'nf_edit_field_restrictions', 'ninja_forms_edit_field_input_limit', 15, 2 );