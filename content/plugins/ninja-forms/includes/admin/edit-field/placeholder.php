<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_edit_field_placeholder');
function ninja_forms_register_edit_field_placeholder(){
	add_action('ninja_forms_edit_field_before_registered', 'ninja_forms_edit_field_placeholder', 10);
}
function ninja_forms_edit_field_placeholder($field_id){
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$field_data = $field_row['data'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_placeholder = $reg_field['edit_placeholder'];
	if($edit_placeholder){
		if(isset($field_data['placeholder'])){
			$placeholder = stripslashes($field_data['placeholder']);
		}else{
			$placeholder = '';
		}
		ninja_forms_edit_field_el_output($field_id, 'text', __( 'Placeholder', 'ninja-forms' ), 'placeholder', $placeholder, 'wide', '', 'widefat ninja-forms-field-label');
	}
}