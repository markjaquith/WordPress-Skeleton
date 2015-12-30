<?php

global $epl_fields;
$epl_fields['epl_fields'] =
        array(
            'input_name' => array(
                'input_type' => 'hidden',
                'input_name' => 'input_name',
            ),
            'label' => array(
                'input_type' => 'text',
                'input_name' => 'label',
                'label' => 'Field Label',
                'description' => epl__( 'Will be used in the field label.' ),
                'required' => true,
                'class' => 'epl_w300' ),
            'input_slug' => array(
                'input_type' => 'text',
                'input_name' => 'input_slug',
                'label' => epl__( 'Input Slug' ),
                'description' => epl__( 'Will be used in email templates. Ex. your_city, your_weight, height...' ),
                'required' => true,
                'class' => 'epl_w300 input_name' ),
            'input_type' => array(
                'input_type' => 'select',
                'input_name' => 'input_type',
                'options' => array( 'text' => 'Text', 'textarea' => 'Textarea', 'select' => 'Dropdown', 'radio' => 'Radio', 'checkbox' => 'Checkbox', 'hidden' => 'Hidden' ),
                'id' => 'input_type',
                'label' => 'Field Type',
                'description' => '',
                'style' => '',
                'class' => 'epl_field_type',
                'default_value' => 'text' ),
            'epl_field_choices' => array(
                'input_type' => 'section',
                'class' => 'epl_field_choices'
            ),
            'epl_field_choice_default' => array(
                'return' => 0,
                'input_name' => 'epl_field_choice_default[]' ),
            'epl_field_choice_text' => array(
                'return' => 0,
                'input_name' => 'epl_field_choice_text[]' ),
            'epl_field_choice_value' => array(
                'return' => 0,
                'input_name' => 'epl_field_choice_value[]'
            ),
            'description' => array(
                'input_type' => 'textarea',
                'input_name' => 'description',
                'label' => 'Field Description',
                'description' => epl__( 'Will be displayed below the field.  Can be used as help text.' ),
                'class' => 'epl_w300' ),
            'required' => array(
                'input_type' => 'select',
                'input_name' => 'required',
                'label' => 'Required',
                'options' => epl_yes_no(),
                'default_value' => 0,
                'display_inline' => true ),
            /* TODO - REVISIT THIS */
            /* 'cb_required' => array(
              'input_type' => 'checkbox',
              'input_name' => 'cb_required[]',
              'label' => 'Required',
              'options' =>  array(1,2,3,4,5),
              ), */
            'default_value' => array(
                'input_type' => 'text',
                'input_name' => 'default_value',
                'label' => 'Default Value',
                'description' => epl__( 'Default value for the field, ONLY FOR Text, Hidden, Textarea (for now).' ),
                'class' => 'epl_w300' ),
            'validation' => array(
                'input_type' => 'select',
                'input_name' => 'validation',
                'options' => array( 'email' => 'Email' ),
                'empty_row' => true,
                'id' => 'input_type',
                'label' => 'Validation',
                'description' => epl__( 'More Coming Soon.' ),
                'style' => '',
                'class' => 'epl_field_type',
                'default_value' => 'text' ),
            'epl_controller' => array(
                'input_type' => 'hidden',
                'input_name' => 'epl_controller',
                'default_value' => 'epl_form_manager' ),
            'epl_system' => array(
                'input_type' => 'hidden',
                'input_name' => 'epl_system',
                'value' => 1,
            )
);


$epl_fields['epl_fields_choices'] =
        array(
            /* 'epl_field_choice_default' => array(
              'input_type' => 'checkbox',
              'input_name' => 'epl_field_choice_default[]' ), */
            'epl_field_choice_text' => array(
                'input_type' => 'text',
                'input_name' => 'epl_field_choice_text[]' ),
            'epl_field_choice_value' => array(
                'input_type' => 'text',
                'input_name' => 'epl_field_choice_value[]'
            )
);


$epl_fields['epl_forms'] =
        array(
            'epl_form_id' => array(
                'input_type' => 'hidden',
                'input_name' => 'epl_form_id' ),
            'epl_form_label' => array(
                'input_type' => 'text',
                'input_name' => 'epl_form_label',
                'id' => 'epl_form_label',
                'label' => 'Form Label',
                'description' => 'Displayed form identifier',
                'class' => 'epl_w300',
                'required' => true ),
            'epl_form_slug' => array(
                'input_type' => 'text',
                'input_name' => 'epl_form_slug',
                'label' => 'Form Slug',
                'description' => epl__( 'Will be used in emails.' ),
                'class' => 'epl_w300 make_slug',
                'required' => true ),
            'epl_form_descritption' => array(
                'input_type' => 'textarea',
                'input_name' => 'epl_form_descritption',
                'label' => 'Form Description',
                'description' => 'If you would like to give some form instructions, you can type them here. ',
                'class' => 'epl_w300' ),
            'epl_form_options' => array(
                'input_type' => 'checkbox',
                'input_name' => 'epl_form_options[]',
                'label' => 'On the registration form:',
                'description' => '',
                'options' => array( 0 => 'Show Form Name.', 10 => 'Show Form Description.' ),
                'class' => '' ),
            'epl_form_fields' => array(
                'return' => 0,
                'input_name' => 'epl_form_fields' ),
            'epl_controller' => array(
                'input_type' => 'hidden',
                'input_name' => 'epl_controller',
                'default_value' => 'epl_form_manager' ),
            'epl_system' => array(
                'input_type' => 'hidden',
                'input_name' => 'epl_system',
                'value' => 1 )
);

