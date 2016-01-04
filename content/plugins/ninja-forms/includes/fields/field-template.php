<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * This is a template file that you can use when creating new field types for Ninja Forms.
 * The simplest way to use this file is to copy its contents and paste them into a new file.
 * Then you would just replace the placeholder content with your own and remove the sections you don't need.
 *
 * Below you will find a breakdown of all the variables available to you when registering a field and what they do.
 **/

	$args = array(
		//name - Required - This is the name that will appear on the add field button.
		'name' => 'My Custom Field',
		'edit_options' => array( //Optional - An array of options to show within the field edit <li>. Should be an array of arrays.
			array(
				'type' => 'text', //Required - What type of input should this be?
				'name' => 'my_text', //What should it be named. This should always be a programmatic name, not a label.
				'label' => 'My Text Label', //Label to be shown before the option.
				'class' => 'widefat', //Additional classes to be added to the input element.
			),
			array(
				'type' => 'select',
				'name' => 'my_select',
				'label' => 'My Select Label',
			),
		),
		'display_function' => 'ninja_forms_field_upload_display', //Required - This function will be called to create output when a user accesses a form containing this element.
		'sub_edit_function' => 'ninja_forms_field_upload_sub_edit',	//Optional - This will be called when an admin or user edits the a user submission.
		'group' => '', //Optional
		'edit_label' => true, //True or False
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_upload_pre_process',
		'process' => 'ninja_forms_field_upload_process',
		'req_validation' => 'ninja_forms_field_upload_req_validation',
	);




//Register the Upload field
add_action('init', 'my_custom_field_register');



function my_custom_field_register(){
	$args = array(
		'name' => 'File Upload',
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'my_text',
				'label' => 'My Text Label',
				'class' => 'widefat',
			),
			array(
				'type' => 'select',
				'name' => 'my_select',
				'label' => 'My Select Label',
			),
		),
		'display_function' => 'ninja_forms_field_upload_display',
		'sub_edit_function' => 'ninja_forms_field_upload_sub_edit',
		'group' => '',
		'edit_label' => true,
		'edit_label_pos' => true,
		'edit_req' => true,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_upload_pre_process',
		'process' => 'ninja_forms_field_upload_process',
		'req_validation' => 'ninja_forms_field_upload_req_validation',
	);

	ninja_forms_register_field('_upload', $args);
}