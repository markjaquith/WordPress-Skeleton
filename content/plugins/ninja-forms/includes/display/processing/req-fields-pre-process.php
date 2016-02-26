<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_req_fields_process(){
	global $ninja_forms_processing, $ninja_forms_fields;

	$all_fields = $ninja_forms_processing->get_all_fields();

	if( is_array( $all_fields ) AND !empty( $all_fields ) ){
		foreach($all_fields as $field_id => $user_value){
			
			$field_row = $ninja_forms_processing->get_field_settings( $field_id );

			if ( !$field_row ) {
				$field_row = ninja_forms_get_field_by_id( $field_id );
			}

			$field_type = $field_row['type'];

			if ( isset ( $ninja_forms_fields[$field_type] ) ) {

				if( isset( $field_row['data'] ) ){
					$field_data = $field_row['data'];
				}else{
					$field_data = '';
				}

				if( isset( $field_data['req'] ) ){
					$req = $field_data['req'];
				}else{
					$req = '';
				}

				if( isset( $field_data['label_pos'] ) ){
					$label_pos = $field_data['label_pos'];
				}else{
					$label_pos = '';
				}
					// Get the required field symbol.
				$settings = nf_get_settings();
				if ( isset ( $settings['req_field_symbol'] ) ) {
					$req_symbol = $settings['req_field_symbol'];
				} else {
					$req_symbol = '*';
				}

				if ( isset ( $field_data['req'] ) and $field_data['req'] == 1 and $label_pos == 'inside' ) {
					$field_data['label'] .= ' '.$req_symbol;
				}

				if( isset( $field_data['label'] ) ){
					$label = $field_data['label'];
				}else{
					$label = '';
				}

				$label = strip_tags( $label );

				$reg_type = $ninja_forms_fields[$field_type];
				$req_validation = $reg_type['req_validation'];

				$plugin_settings = nf_get_settings();
				if ( isset ( $plugin_settings['req_field_error'] ) ) {
					$req_field_error = __( $plugin_settings['req_field_error'], 'ninja-forms' );
				} else {
					$req_field_error = __( 'This is a required field.', 'ninja-forms' );
				}
				
				if( isset( $plugin_settings['req_error_label'] ) ){
					$req_error_label = __( $plugin_settings['req_error_label'], 'ninja-forms' );
				}else{
					$req_error_label = __( 'Please check required fields.', 'ninja-forms' );
				}

				if( $req == 1 AND $user_value !== false ){

					if($req_validation != ''){
						$arguments['field_id'] = $field_id;
						$arguments['user_value'] = $user_value;
						$req = call_user_func_array($req_validation, $arguments);
						if(!$req){
							$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
							$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
						}
					}else{
						if($label_pos == 'inside'){
							if( $user_value == $label OR ( empty( $user_value ) && $user_value !== "0" ) ){
								$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
								$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
							}
						}else{
							if( $user_value === '' ){
								$ninja_forms_processing->add_error('required-'.$field_id, $req_field_error, $field_id);
								$ninja_forms_processing->add_error('required-general', $req_error_label, 'general');
							}
						}
					}
				}
			}
		}
	}
}

add_action( 'ninja_forms_pre_process', 'ninja_forms_req_fields_process', 13);