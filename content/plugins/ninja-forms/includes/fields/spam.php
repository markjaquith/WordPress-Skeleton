<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_register_field_spam(){
	$args = array(
		'name' => __( 'Anti-Spam', 'ninja-forms' ),
		'edit_function' => '',
		'display_function' => 'ninja_forms_field_spam_display',
		'group' => 'standard_fields',
		'edit_label' => false,
		'edit_label_pos' => true,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => true,
		'edit_meta' => false,
		'sidebar' => 'template_fields',
		'display_label' => true,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
		),
		'pre_process' => 'ninja_forms_field_spam_pre_process',
		'process_field' => false,
		'limit' => 1,
		'edit_options' => array(
			array(
				'name' => 'label',
				'type' => 'text',
				'label' => __( 'Spam Question', 'ninja-forms' ),
				'width' => 'wide',
				'class' => 'widefat',
			),
			array(
				'name' => 'spam_answer',
				'type' => 'text',
				'label' => __( 'Spam Answer', 'ninja-forms' ),
				'width' => 'wide',
				'class' => 'widefat',
			),
		),
		'req' => true,
	);

	ninja_forms_register_field('_spam', $args);
}

add_action('init', 'ninja_forms_register_field_spam');

function ninja_forms_field_spam_edit( $field_id, $data ){
	if(isset($data['label'])){
		$question = $data['label'];
	}else{
		$question = '';
	}

	if(isset($data['answer'])){
		$answer = $data['answer'];
	}else{
		$answer = '';
	}
	?>
	<input type="hidden" name="ninja_forms_field_<?php echo $field_id;?>[req]" value="1">
	<p class="description description-wide">
		<label for="">
			<?php _e( 'Spam Question' , 'ninja-forms'); ?><br />
			<input type="text" class="widefat code ninja-forms-field-label" name="ninja_forms_field_<?php echo $field_id;?>[label]" id="ninja_forms_field_<?php echo $field_id;?>_label" value="<?php echo stripslashes( $question );?>">
		</label>
	</p>
	<p class="description description-wide">
		<label for="">
			<?php _e( 'Spam Answer', 'ninja-forms'); ?><br />
			<input type="text" class="widefat" name="ninja_forms_field_<?php echo $field_id;?>[answer]" id="" value="<?php echo $answer;?>">
		</label>
	</p>
	<?php
}

function ninja_forms_field_spam_display( $field_id, $data, $form_id = '' ){
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	if(isset($data['show_field'])){
		$show_field = $data['show_field'];
	}else{
		$show_field = true;
	}

	$field_class = ninja_forms_get_field_class( $field_id, $form_id );
	if(isset($data['label_pos'])){
		$label_pos = $data['label_pos'];
	}else{
		$label_pos = "left";
	}

	if(isset($data['label'])){
		$label = $data['label'];
	}else{
		$label = '';
	}

	if($label_pos == 'inside'){
		$default_value = $label;
	}

	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="text" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
	<?php

}

function ninja_forms_field_spam_pre_process( $field_id, $user_value ){
	global $ninja_forms_processing;

	$plugin_settings = nf_get_settings();
	if(isset($plugin_settings['spam_error'])){
		$spam_error = __( $plugin_settings['spam_error'], 'ninja-forms' );
	}
	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_data = $field_row['data'];
	$spam_answer = $field_data['spam_answer'];

	$form_row = ninja_forms_get_form_by_field_id($field_id);
	$form_id = $form_row['id'];

	if( $ninja_forms_processing->get_action() != 'save' AND $ninja_forms_processing->get_action() != 'mp_save' AND !isset($_POST['_wp_login']) AND $user_value != $spam_answer){
		if( is_object( $ninja_forms_processing)){
			if( $user_value != '' ){
				$ninja_forms_processing->add_error('spam-general', $spam_error, 'general');
				$ninja_forms_processing->add_error('spam-'.$field_id, $spam_error, $field_id);				
			}
		}
	}
}