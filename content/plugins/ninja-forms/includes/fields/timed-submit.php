<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_timed_submit(){
	$args = array(
		'name' => __( 'Timed Submit', 'ninja-forms' ),
		'edit_options' => array(
			array(
				'type' => 'text',
				'name' => 'label',
				'label' => __( 'Label', 'ninja-forms' ),
				'desc' => __( 'Submit button text after timer expires', 'ninja-forms' ),
				'default' => __( 'Submit' ),
				'class' => 'widefat ninja-forms-field-label'
			),
			array(
				'type' => 'text',
				'name' => 'timer-text',
				'label' => __( 'Label', 'ninja-forms' ),
				'default' => __( 'Please wait %n seconds', 'ninja-forms' ),
				'desc' => __( '%n will be used to signify the number of seconds', 'ninja-forms' ),
				'width' => 'wide',
				'class' => 'widefat ninja-forms-field-label'
			),
			array(
				'type' => 'number',
				'name' => 'countdown',
				'label' => __( 'Number of seconds for countdown', 'ninja-forms' ),
				'desc' => __( 'This is how long a user must wait to submit the form', 'ninja-forms' ),
				'default' => 10,
			),
		),
		'display_function' => 'ninja_forms_field_timed_submit_display',
		'group' => 'standard_fields',
		'edit_label' => false,
		'edit_label_pos' => false,
		'edit_req' => false,
		'default_label' => __( 'Submit', 'ninja-forms' ),
		'edit_desc' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'display_label' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'process_field' => false,
		'pre_process' => 'ninja_forms_field_timed_submit_pre_process',
		'limit' => 1,
		'visible' => false
	);

	ninja_forms_register_field('_timed_submit', $args);
}

add_action('init', 'ninja_forms_register_field_timed_submit');



function ninja_forms_field_timed_submit_display( $field_id, $data, $form_id = '' ){

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );

	if(isset($data['timer-text']) AND $data['timer-text'] != ''){
		$label = $data['timer-text'];
	}else{
		$label = __( 'Please wait %n seconds', 'ninja-forms' );
	}

	if(isset($data['countdown']) AND $data['countdown'] != ''){
		$countdown = $data['countdown'];
	}else{
		$countdown = '10';
	}

	if(isset($data['label']) AND $data['label'] != ''){
		$submit_text = $data['label'];
	}else{
		$submit_text = __( 'Submit', 'ninja-forms' );
	}

	$label = preg_replace( "/%n/", "<span>" . $countdown . "</span>", $label);
	?>

	<input id="ninja_forms_field_<?php echo $field_id;?>_js" name="ninja_forms_field_<?php echo $field_id;?>[no-js]" type="hidden" value="1" rel="<?php echo $field_id;?>_js" class="no-js" />

	<button type="submit" name="ninja_forms_field_<?php echo $field_id;?>[timer]" class="<?php echo $field_class;?> countdown-timer" id="ninja_forms_field_<?php echo $field_id;?>" value="<?php echo $countdown;?>" rel="<?php echo $field_id;?>" data-countdown="<?php echo $countdown;?>" data-text="<?php esc_attr_e( $submit_text );?>"><?php echo $label ;?></button>

	<?php

}

function ninja_forms_field_timed_submit_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;

	$plugin_settings = nf_get_settings();
	if ( isset ( $plugin_settings['timed_submit_error'] ) ) {
		$timed_submit_error = __( $plugin_settings['timed_submit_error'], 'ninja-forms' );
	} else {
		$timed_submit_error = __('If you are a human, please slow down.', 'ninja-forms');
	}

	if ( isset ( $plugin_settings['javascript_error'] ) ) {
		$javascript_error = __( $plugin_settings['javascript_error'], 'ninja-forms' );
	} else {
		$javascript_error = __( 'You need JavaScript to submit this form. Please enable it and try again.', 'ninja-forms' );
	}

	if ( isset ( $user_value['no-js'] ) ){
		$ninja_forms_processing->add_error('javascript-general', $javascript_error, 'general' );
	} else {
		$timer = isset( $user_value['timer'] ) ? $user_value['timer'] : 10;
		if( intval( $timer ) > 0 ){
	   	$ninja_forms_processing->add_error('timer-'.$field_id, $timed_submit_error, $field_id);
		}
	}



}
