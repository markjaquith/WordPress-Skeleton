<?php if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'init', 'ninja_forms_register_tab_label_settings' );

function ninja_forms_register_tab_label_settings(){
	$args = array(
		'name' => __( 'Labels', 'ninja-forms' ),
		'page' => 'ninja-forms-settings',
		'display_function' => '',
		'save_function' => 'ninja_forms_save_label_settings',
	);
	ninja_forms_register_tab( 'label_settings', $args );

}

add_action( 'init', 'ninja_forms_register_label_settings_metabox' );

function ninja_forms_register_label_settings_metabox(){

	$args = array(
		'page' => 'ninja-forms-settings',
		'tab' => 'label_settings',
		'slug' => 'label_labels',
		'title' => __( 'Message Labels', 'ninja-forms' ),
		'settings' => array(
			array(
				'name' => 'req_div_label',
				'type' => 'text',
				'label' => __( 'Required Field Label', 'ninja-forms' ),
				'desc' => '',
				'help_text' => '',
			),
			array(
				'name' => 'req_field_symbol',
				'type' => 'text',
				'label' => __( 'Required field symbol', 'ninja-forms' ),
			),
			array(
				'name' => 'req_error_label',
				'type' => 'text',
				'label' => __( 'Error message given if all required fields are not completed', 'ninja-forms' ),
			),
			array(
				'name' => 'req_field_error',
				'type' => 'text',
				'label' => __( 'Required Field Error', 'ninja-forms' ),
				'desc' => '',
			),
			array(
			 	'name' => 'spam_error',
				'type' => 'text',
				'label' => __( 'Anti-spam error message', 'ninja-forms' ),
				'desc' => '',
			),
			array(
			 	'name' => 'honeypot_error',
				'type' => 'text',
				'label' => __( 'Honeypot error message', 'ninja-forms' ),
				'desc' => '',
			),
			array(
			 	'name' => 'timed_submit_error',
				'type' => 'text',
				'label' => __( 'Timer error message', 'ninja-forms' ),
				'desc' => '',
			),
			array(
				'name' => 'javascript_error',
				'type' => 'text',
				'label' => __( 'JavaScript disabled error message', 'ninja-forms' ),
				'desc' => '',
			),
			array(
			 	'name' => 'invalid_email',
				'type' => 'text',
				'label' => __( 'Please enter a valid email address', 'ninja-forms' ),
				'desc' => '',
			),
			array(
				'name' => 'process_label',
				'type' => 'text',
				'label' => __( 'Processing Submission Label', 'ninja-forms' ),
				'desc' => __( 'This message is shown inside the submit button whenever a user clicks "submit" to let them know it is processing.', 'ninja-forms' ),
			),
			array(
				'name' => 'password_mismatch',
				'type' => 'text',
				'label' => __( 'Password Mismatch Label', 'ninja-forms' ),
				'desc' => __( 'This message is shown to a user when non-matching values are placed in the password field.', 'ninja-forms' ),
			),
		),
	);
	ninja_forms_register_tab_metabox( $args );

}

function ninja_forms_save_label_settings( $data ){
	$plugin_settings = nf_get_settings();
	foreach( $data as $key => $val ){
		$plugin_settings[$key] = $val;
	}
	update_option( "ninja_forms_settings", $plugin_settings );
	$update_msg = __( 'Settings Saved', 'ninja-forms' );
	return $update_msg;
}