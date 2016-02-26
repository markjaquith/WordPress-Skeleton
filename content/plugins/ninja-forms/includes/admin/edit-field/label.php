<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action('init', 'ninja_forms_register_edit_field_label');
function ninja_forms_register_edit_field_label(){
	add_action('ninja_forms_edit_field_before_registered', 'ninja_forms_edit_field_label', 10, 2);
}

function ninja_forms_edit_field_label( $field_id, $field_data ) {
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_label = $reg_field['edit_label'];
	if($edit_label){
		if(isset($field_data['label'])){
			$label = stripslashes($field_data['label']);
		}else{
			$label = '';
		}

		ninja_forms_edit_field_el_output($field_id, 'text', 'Label', 'label', $label, 'wide', '', 'widefat ninja-forms-field-label');
	}
}

add_action('init', 'ninja_forms_register_edit_field_label_pos');
function ninja_forms_register_edit_field_label_pos(){
	add_action('ninja_forms_edit_field_before_registered', 'ninja_forms_edit_field_label_pos', 10, 2 );
}

function ninja_forms_edit_field_label_pos( $field_id, $field_data ){
	global $ninja_forms_fields;
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];
	$edit_label_pos = $reg_field['edit_label_pos'];
	$label_pos_options = $reg_field['label_pos_options'];

	if( !$label_pos_options OR $label_pos_options == '' ){
		$options = array(
			array('name' => __( 'Left of Element', 'ninja-forms' ), 'value' => 'left'),
			array('name' => __( 'Above Element', 'ninja-forms' ), 'value' => 'above'),
			array('name' => __( 'Below Element', 'ninja-forms' ), 'value' => 'below'),
			array('name' => __( 'Right of Element', 'ninja-forms' ), 'value' => 'right'),
			array('name' => __( 'Inside Element', 'ninja-forms' ), 'value' => 'inside'),
		);
	}else{
		$options = $label_pos_options;
	}

	if($edit_label_pos){
		if(isset($field_data['label_pos'])){
			$label_pos = $field_data['label_pos'];
		}else{
			$label_pos = 'above';
		}

		ninja_forms_edit_field_el_output($field_id, 'select', __( 'Label Position', 'ninja-forms' ), 'label_pos', $label_pos, 'wide', $options, 'widefat');
	}

}
