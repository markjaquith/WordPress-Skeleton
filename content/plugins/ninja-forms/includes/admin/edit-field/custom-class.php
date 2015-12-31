<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_edit_field_custom_class');
function ninja_forms_register_edit_field_custom_class(){
	add_action('ninja_forms_edit_field_after_registered', 'ninja_forms_edit_field_custom_class', 10, 2 );
}

function ninja_forms_edit_field_custom_class( $field_id, $field_data ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_custom_class = $reg_field['edit_custom_class'];
	if($edit_custom_class){
		if(isset($field_data['class'])){
			$class = $field_data['class'];
		}else{
			$class = '';
		}

		ninja_forms_edit_field_el_output($field_id, 'text', __( 'Custom CSS Classes', 'ninja-forms' ), 'class', $class, 'wide', '', 'widefat');
	}
}