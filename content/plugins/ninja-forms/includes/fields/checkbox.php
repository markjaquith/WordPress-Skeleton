<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_checkbox(){
	$args = array(
		'name' => __( 'Checkbox', 'ninja-forms' ),
		'edit_options' => array(
			array(
				'type'    => 'select', //What type of input should this be?
				'options' => array(
					array(
						'name'  => __( 'Unchecked', 'ninja-forms' ),
						'value' => 'unchecked',
					),
					array(
						'name'  => __( 'Checked', 'ninja-forms' ),
						'value' => 'checked',
					),
				),
				'name' => 'default_value', //What should it be named. This should always be a programmatic name, not a label.
				'label' => __( 'Default Value', 'ninja-forms' ),
				'class' => 'widefat', //Additional classes to be added to the input element.
			),
		),
		//'edit_function' => 'ninja_forms_field_checkbox_edit',
		'display_function'  => 'ninja_forms_field_checkbox_display',
		'group'             => 'standard_fields',
		'edit_label'        => true,
		'edit_label_pos'    => true,
		'label_pos_options' => array(
			array('name' => __( 'Left of Element', 'ninja-forms' ), 'value' => 'left'),
			array('name' => __( 'Above Element', 'ninja-forms' ), 'value' => 'above'),
			array('name' => __( 'Below Element', 'ninja-forms' ), 'value' => 'below'),
			array('name' => __( 'Right of Element', 'ninja-forms' ), 'value' => 'right'),
		),
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_desc' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'action' => array(
				'show' => array(
					'name' => __( 'Show This', 'ninja-forms' ),
					'js_function' => 'show',
					'output' => 'show',
				),
				'hide' => array(
					'name' => __( 'Hide This', 'ninja-forms' ),
					'js_function' => 'hide',
					'output' => 'hide',
				),
				'change_value' => array(
					'name' => __( 'Change Value', 'ninja-forms' ),
					'output' => 'select',
					'options' => array(
						'Checked' => 'checked',
						'Unchecked' => 'unchecked',
					),
					'js_function' => 'change_value',

				),

			),
			'value' => array(
				'type' => 'select',
				'options' => array(
					'Checked' => 'checked',
					'Unchecked' => 'unchecked',
				),
			),
		),
		'req_validation' => 'ninja_forms_field_checkbox_validation',
		'edit_sub_value' => 'nf_field_checkbox_edit_sub_value',
	);

	ninja_forms_register_field('_checkbox', $args);
}

//Register the Checkbox field
add_action('init', 'ninja_forms_register_field_checkbox');

//Checkbox Display Function
function ninja_forms_field_checkbox_display( $field_id, $data, $form_id = '' ){

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	$default_value = $data['default_value'];
	if($default_value == 'checked' OR $default_value == 1){
		$checked = 'checked = "checked"';
	}else{
		$checked = '';
	}

	?><input id="" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" value="unchecked" /><input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="checkbox" class="<?php echo $field_class;?>" value="checked" <?php echo $checked;?> rel="<?php echo $field_id;?>"/><?php
}

//Checkbox Pre-Processing Function
function ninja_forms_field_checkbox_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;
	if( $user_value != 'checked' AND $user_value != 'unchecked' ){
		if( $user_value == 1 ){
				$user_value = 'checked';
		}else{
			$user_value = 'unchecked';
		}
	}


	if( $ninja_forms_processing->get_field_value( $field_id ) !== false ){
		$ninja_forms_processing->update_field_value( $field_id, $user_value );
	}

}

//Checkbox Validation Function
function ninja_forms_field_checkbox_validation( $field_id, $user_value ){
	if( $user_value != 'unchecked' AND $user_value != '' ){
		return true;
	}else{
		return false;
	}
}

/**
 * Edit submission value output function
 *
 * @since 2.7
 * @return void
 */
function nf_field_checkbox_edit_sub_value( $field_id, $user_value ) {
	
	if ( $user_value == 'checked' ) {
		$checked = 'checked="checked"';
	} else {
		$checked = '';
	}

	?>
	<input type="hidden" name="fields[<?php echo $field_id; ?>]" value="unchecked">
	<input type="checkbox" name="fields[<?php echo $field_id; ?>]" value="checked" <?php echo $checked; ?>>
	<?php
}