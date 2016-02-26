<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_edit_field_required');
function ninja_forms_register_edit_field_required(){
	add_action('nf_edit_field_restrictions', 'ninja_forms_field_required', 9, 2 );
}

function ninja_forms_field_required( $field_id, $field_data ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_req = $reg_field['edit_req'];
	
	if($edit_req){
		if(isset($field_data['req'])){
			$req = $field_data['req'];
		}else{
			$req = '';
		}

		ninja_forms_edit_field_el_output($field_id, 'checkbox', __( 'Required', 'ninja-forms' ), 'req', $req, 'wide' );
	}

}

function nf_edit_field_req( $field_id, $field_data ) {
	global $ninja_forms_fields;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$field_req = $reg_field['req'];
	
	if ( $field_req ) {
		ninja_forms_edit_field_el_output($field_id, 'hidden', '', 'req', 1);
	}	
}

add_action( 'ninja_forms_edit_field_before_registered', 'nf_edit_field_req', 10, 2 );