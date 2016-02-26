<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_fields_post_process');
function ninja_forms_register_fields_post_process(){
	add_action('ninja_forms_post_process', 'ninja_forms_fields_post_process');
}

function ninja_forms_fields_post_process(){
	global $ninja_forms_fields, $ninja_forms_processing;
	//Loop through the submitted form data and call each field's post_processing function, if one exists.
	$form_id = $ninja_forms_processing->get_form_ID();
	$field_results = $ninja_forms_processing->get_all_fields();
	if( is_array( $field_results ) AND !empty( $field_results ) ){
		foreach( $field_results as $field_id => $user_value ){
			$field = $ninja_forms_processing->get_field_settings( $field_id );
			$field_id = $field['id'];
			$field_type = $field['type'];
			$field_data = $field['data'];
			if( isset( $ninja_forms_fields[$field_type] ) ){
				$reg_field = $ninja_forms_fields[$field_type];
				//if( $reg_field['process_field'] ){
					$post_process_function = $reg_field['post_process'];
					if($post_process_function != ''){
						$arguments = array();
						$arguments['field_id'] = $field_id;
						$user_value = apply_filters( 'ninja_forms_field_post_process_user_value', $user_value, $field_id );
						$arguments['user_value'] = $user_value;
						call_user_func_array($post_process_function, $arguments);
					}
				//}
			}
		}
	}
}