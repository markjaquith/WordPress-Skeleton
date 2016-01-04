<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_fields_pre_process');
function ninja_forms_register_fields_pre_process(){
	add_action( 'ninja_forms_pre_process', 'ninja_forms_fields_pre_process', 9 );
}

function ninja_forms_fields_pre_process(){
	global $ninja_forms_fields, $ninja_forms_processing;
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
					$pre_process_function = $reg_field['pre_process'];
					if($pre_process_function != ''){
						$arguments = array();
						$arguments['field_id'] = $field_id;
						$user_value = apply_filters( 'ninja_forms_field_pre_process_user_value', $user_value, $field_id );
						$arguments['user_value'] = $user_value;
						call_user_func_array($pre_process_function, $arguments);
					}
				//}
			}
		}
	}
}